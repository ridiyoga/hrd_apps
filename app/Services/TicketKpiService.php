<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\TicketAssignmentMaster;
use App\Models\TicketDifficultyMaster;
use App\Models\TicketHead;
use App\Models\TicketWorkSession;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class TicketKpiService
{
    private const DEFAULT_DIFFICULTIES = [
        ['code' => 'P01', 'name' => 'Critical Risk', 'expected_hours' => 24.00],
        ['code' => 'P02', 'name' => 'High', 'expected_hours' => 12.00],
        ['code' => 'P03', 'name' => 'Medium', 'expected_hours' => 6.00],
        ['code' => 'P04', 'name' => 'Low', 'expected_hours' => 1.00],
    ];

    private function seedDifficultyMasterIfMissing(): void
    {
        if (TicketDifficultyMaster::query()->exists()) {
            return;
        }

        foreach (self::DEFAULT_DIFFICULTIES as $difficulty) {
            TicketDifficultyMaster::updateOrCreate(
                ['code' => $difficulty['code']],
                [
                    'name' => $difficulty['name'],
                    'expected_hours' => $difficulty['expected_hours'],
                ]
            );
        }
    }

    public function difficultyMaster()
    {
        $this->seedDifficultyMasterIfMissing();

        return TicketDifficultyMaster::orderBy('code')->get();
    }

    public function expectedHoursForCode(?string $code): ?float
    {
        if (! $code) {
            return null;
        }

        $this->seedDifficultyMasterIfMissing();

        return TicketDifficultyMaster::where('code', $code)->value('expected_hours') !== null
            ? (float) TicketDifficultyMaster::where('code', $code)->value('expected_hours')
            : null;
    }

    /**
     * Memulai sesi pengerjaan tiket oleh PIC.
     * Mencatat sesi kerja aktif ke tabel ticket_work_sessions.
     * Jika ini siklus pertama -> session_type = 'awal' (cycle_number = 1).
     * Jika setelah feedback requestor -> session_type = 'revisi' (cycle_number = n+1).
     */
    public function startWork(
        TicketHead $ticket,
        ?string $picId = null,
        ?string $notes = null,
        string $ticketType = 'ticket',
        ?string $sessionTypeOverride = null
    ): TicketHead {
        $resolvedPicId = $picId ?? $ticket->pic_id;

        // Cek apakah ada sesi aktif yang belum ditutup (mencegah double-start)
        $activeSession = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->whereNull('ended_at')
            ->first();

        if (! $activeSession) {
            $lastCycle = TicketWorkSession::where('ticket_type', $ticketType)
                ->where('ticket_id', $ticket->id)
                ->max('cycle_number') ?? 0;

            $nextCycle = $lastCycle + 1;
            $sessionType = $sessionTypeOverride
                ?? (($nextCycle === 1)
                    ? TicketWorkSession::SESSION_AWAL
                    : TicketWorkSession::SESSION_REVISI);

            TicketWorkSession::create([
                'id'               => (string) Uuid::uuid4(),
                'ticket_type'      => $ticketType,
                'ticket_id'        => $ticket->id,
                'pic_id'           => $resolvedPicId,
                'cycle_number'     => $nextCycle,
                'session_type'     => $sessionType,
                'started_at'       => now(),
                'ended_at'         => null,
                'duration_minutes' => 0,
                'notes'            => $notes,
            ]);
        }

        if (is_null($ticket->pic_started_at)) {
            $ticket->pic_started_at = now();
        }

        $ticket->ticket_status = TicketHead::STATUS_IN_PROGRESS;
        $ticket->execution_status = TicketHead::EXEC_IN_PROGRESS;
        $ticket->pic_status = false;
        $ticket->finish_at = null;
        $ticket->save();

        return $ticket;
    }

    /**
     * Menandai pengerjaan tiket selesai oleh PIC dan dikirim ke pemohon untuk review.
     * Menutup sesi kerja aktif, menghitung durasi sesi ini, dan mengupdate akumulasi actual_hours.
     */
    public function markWaitingRequestor(TicketHead $ticket, ?string $notes = null, string $ticketType = 'ticket'): TicketHead
    {
        $activeSession = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($activeSession) {
            $endedAt = now();
            $diffMinutes = round(Carbon::parse($activeSession->started_at)->diffInSeconds($endedAt) / 60, 2);
            $activeSession->update([
                'ended_at'         => $endedAt,
                'duration_minutes' => $diffMinutes,
                'notes'            => $notes ?? $activeSession->notes,
            ]);
        } else {
            // Jika tidak ada sesi aktif (misal PIC langsung menekan finish tanpa start)
            $hasAnySession = TicketWorkSession::where('ticket_type', $ticketType)
                ->where('ticket_id', $ticket->id)
                ->exists();

            if (! $hasAnySession) {
                $startedAt = $ticket->pic_started_at ?? $ticket->assign_at ?? $ticket->validator_at ?? now();
                $endedAt = now();
                $diffMinutes = round(Carbon::parse($startedAt)->diffInSeconds($endedAt) / 60, 2);

                TicketWorkSession::create([
                    'id'               => (string) Uuid::uuid4(),
                    'ticket_type'      => $ticketType,
                    'ticket_id'        => $ticket->id,
                    'pic_id'           => $ticket->pic_id,
                    'cycle_number'     => 1,
                    'session_type'     => TicketWorkSession::SESSION_AWAL,
                    'started_at'       => $startedAt,
                    'ended_at'         => $endedAt,
                    'duration_minutes' => $diffMinutes,
                    'notes'            => $notes ?? 'Pengerjaan langsung diselesaikan (Auto-session)',
                ]);

                if (is_null($ticket->pic_started_at)) {
                    $ticket->pic_started_at = $startedAt;
                }
            }
        }

        $actualHours = $this->calculateTotalActualHours($ticket->id, $ticketType);

        $ticket->ticket_status = TicketHead::STATUS_WAITING_REQUESTOR;
        $ticket->execution_status = TicketHead::EXEC_DONE;
        $ticket->pic_status = true;
        $ticket->finish_at = now();
        $ticket->actual_hours = $actualHours;
        $ticket->save();

        return $ticket;
    }

    /**
     * PIC menandai tiket "Pending" karena pengerjaannya mandek menunggu aksi
     * PIHAK LAIN yang di luar kendali PIC (mis. PIC sudah membuat transaksi
     * cancelation AR invoice, tapi baru dianggap tuntas setelah di-posting
     * oleh JM Finance). Menutup sesi kerja aktif (jam berhenti di sini),
     * sehingga waktu menunggu pihak lain TIDAK ikut menghukum durasi PIC.
     */
    public function markPendingExternal(
        TicketHead $ticket,
        ?string $reason = null,
        ?string $partyLabel = null,
        string $ticketType = 'ticket'
    ): TicketHead {
        $activeSession = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($activeSession) {
            $endedAt = now();
            $diffMinutes = round(Carbon::parse($activeSession->started_at)->diffInSeconds($endedAt) / 60, 2);
            $activeSession->update([
                'ended_at'         => $endedAt,
                'duration_minutes' => $diffMinutes,
                'notes'            => $reason ?? $activeSession->notes,
            ]);
        }

        $ticket->ticket_status = TicketHead::STATUS_PENDING_EXTERNAL;
        $ticket->execution_status = TicketHead::EXEC_IN_PROGRESS;
        $ticket->pic_status = false;
        $ticket->pending_reason = $reason;
        $ticket->pending_started_at = now();
        $ticket->actual_hours = $this->calculateTotalActualHours($ticket->id, $ticketType);
        $ticket->save();

        return $ticket;
    }

    /**
     * PIC melanjutkan pengerjaan setelah pihak lain selesai memproses
     * (mis. JM Finance sudah posting). Membuka siklus/cycle kerja baru —
     * sama seperti revisi — sehingga waktu tambahan otomatis terakumulasi
     * ke total actual_hours yang sama.
     */
    public function resumeFromPending(TicketHead $ticket, ?string $notes = null, string $ticketType = 'ticket'): TicketHead
    {
        // Simpan histori pending terakhir ke notes sebelum di-reset (opsional,
        // untuk audit trail di kolom notes ticket kalau kolom log terpisah belum ada).
        $ticket->pending_started_at = null;
        $ticket->ticket_status = TicketHead::STATUS_IN_PROGRESS;
        $ticket->execution_status = TicketHead::EXEC_IN_PROGRESS;
        $ticket->save();

        // 'lanjutan' = cycle kerja lanjutan setelah pending pihak eksternal selesai,
        // dibedakan dari 'revisi' (yang dipicu feedback pengaju). Kalau model
        // TicketWorkSession belum punya konstanta SESSION_LANJUTAN, tambahkan:
        // const SESSION_LANJUTAN = 'lanjutan';
        return $this->startWork($ticket, $ticket->pic_id, $notes, $ticketType, 'lanjutan');
    }

    /**
     * Pemohon/Requestor mengirimkan catatan feedback/revisi kembali ke PIC.
     * Status diubah menjadi Respon Balasan Peminta (T07).
     * Waktu kerja PIC berhenti/dipause dan feedback_cycles dinaikkan.
     */
    public function returnWithFeedback(TicketHead $ticket, ?string $notes = null, string $ticketType = 'ticket'): TicketHead
    {
        // Pastikan sesi aktif tertutup jika ada (agar waktu review tidak dihitung ke PIC)
        $activeSession = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($activeSession) {
            $endedAt = now();
            $diffMinutes = round(Carbon::parse($activeSession->started_at)->diffInSeconds($endedAt) / 60, 2);
            $activeSession->update([
                'ended_at'         => $endedAt,
                'duration_minutes' => $diffMinutes,
            ]);
        }

        $ticket->ticket_status = TicketHead::STATUS_FEEDBACK_REQUESTOR;
        $ticket->execution_status = TicketHead::EXEC_IN_PROGRESS;
        $ticket->pic_status = false;
        $ticket->finish_at = null;
        $ticket->feedback_cycles = ($ticket->feedback_cycles ?? 0) + 1;
        $ticket->actual_hours = $this->calculateTotalActualHours($ticket->id, $ticketType);
        $ticket->save();

        return $ticket;
    }

    /**
     * Pemohon/Requestor menutup (close) tiket setelah selesai diperiksa.
     * Menghitung total final akumulasi actual_hours dari seluruh sesi kerja PIC,
     * serta mengevaluasi variance_hours dan overrun_flag terhadap target.
     */
    public function closeByRequestor(TicketHead $ticket, string $ticketType = 'ticket'): TicketHead
    {
        $this->seedDifficultyMasterIfMissing();

        // Pastikan sesi yang masih terbuka (jika ada) ditutup
        $activeSession = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($activeSession) {
            $endedAt = now();
            $diffMinutes = round(Carbon::parse($activeSession->started_at)->diffInSeconds($endedAt) / 60, 2);
            $activeSession->update([
                'ended_at'         => $endedAt,
                'duration_minutes' => $diffMinutes,
            ]);
        }

        $closedAt = now();
        $ticket->closed_at = $closedAt;
        $ticket->ticket_status = TicketHead::STATUS_CLOSED;
        $ticket->execution_status = TicketHead::EXEC_DONE;
        $ticket->pic_status = true;

        $hasSessions = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->exists();

        if ($hasSessions) {
            $actualHours = $this->calculateTotalActualHours($ticket->id, $ticketType);
        } else {
            // Fallback perhitungan sederhana untuk data tiket lama tanpa log sesi (menggunakan assign_at / finish_at bukan created_at)
            $startRef = $ticket->pic_started_at ?? $ticket->assign_at ?? $ticket->validator_at ?? $ticket->finish_at ?? $closedAt;
            $endRef = $ticket->finish_at ?? $closedAt;
            $diffMinutes = round(Carbon::parse($startRef)->diffInSeconds(Carbon::parse($endRef)) / 60, 2);
            $actualHours = $diffMinutes;
        }

        $expectedHours = $this->expectedHoursForCode($ticket->ticket_priority);
        $varianceHours = ! is_null($expectedHours) ? round($actualHours - $expectedHours, 2) : null;
        $overrunFlag = ! is_null($expectedHours) ? ($actualHours > $expectedHours) : false;

        $ticket->actual_hours = $actualHours;
        $ticket->expected_hours = $expectedHours;
        $ticket->variance_hours = $varianceHours;
        $ticket->overrun_flag = $overrunFlag;
        $ticket->save();

        return $ticket;
    }

    public function autoCloseBySystem(
        TicketHead $ticket,
        string $ticketType = 'ticket'
    ): TicketHead {
        // Gunakan logika perhitungan dan penutupan yang sudah ada.
        $this->closeByRequestor($ticket, $ticketType);

        // Ubah status akhir menjadi auto-close.
        $ticket->ticket_status = TicketHead::STATUS_AUTOMATIC_CLOSE;

        // Pastikan waktu selesai pengerjaan PIC tetap dipertahankan.
        // closeByRequestor() tidak mengubah finish_at.
        $ticket->save();

        return $ticket;
    }

    /**
     * Membatalkan tiket.
     */
    public function cancel(TicketHead $ticket, string $ticketType = 'ticket'): TicketHead
    {
        $activeSession = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticket->id)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($activeSession) {
            $endedAt = now();
            $diffMinutes = round(Carbon::parse($activeSession->started_at)->diffInSeconds($endedAt) / 60, 2);
            $activeSession->update([
                'ended_at'         => $endedAt,
                'duration_minutes' => $diffMinutes,
            ]);
        }

        $ticket->ticket_status = TicketHead::STATUS_CANCELLED;
        $ticket->execution_status = TicketHead::EXEC_WAITING;
        $ticket->pic_status = false;
        $ticket->closed_at = now();
        $ticket->actual_hours = $this->calculateTotalActualHours($ticket->id, $ticketType);
        $ticket->save();

        return $ticket;
    }

    /**
     * Menghitung total akumulasi menit kerja dari seluruh sesi pengerjaan yang sudah selesai.
     */
    public function calculateTotalActualHours(string $ticketId, string $ticketType = 'ticket'): float
    {
        $totalMinutes = TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticketId)
            ->whereNotNull('ended_at')
            ->sum('duration_minutes');

        return (float) round($totalMinutes, 2);
    }

    /**
     * Mendapatkan daftar sesi kerja PIC untuk sebuah tiket.
     */
    public function getSessions(string $ticketId, string $ticketType = 'ticket')
    {
        return TicketWorkSession::where('ticket_type', $ticketType)
            ->where('ticket_id', $ticketId)
            ->orderBy('cycle_number')
            ->get();
    }

    /**
     * Ringkasan KPI untuk PIC tertentu.
     */
    public function summaryForPic($picId): array
    {
        $this->seedDifficultyMasterIfMissing();

        $picKaryawan = Karyawan::with('jabatan')->find($picId);
        $picName = $picKaryawan?->name;

        $closedTickets = TicketHead::where('pic_id', $picId)
            ->where('ticket_status', TicketHead::STATUS_CLOSED)
            ->where('execution_status', TicketHead::EXEC_DONE)
            ->get();

        if (! $picName) {
            $picName = $closedTickets->first()?->pic_name ?? '';
            if (! $picName) {
                $anyTicket = TicketHead::where('pic_id', $picId)->first();
                $picName = $anyTicket?->pic_name ?? 'PIC #' . $picId;
            }
        }

        $totalClosed = $closedTickets->count();
        $totalActualHours = round((float) $closedTickets->sum('actual_hours'), 2);
        $avgProcessingHours = $totalClosed > 0
            ? round((float) $closedTickets->avg('actual_hours'), 2)
            : 0.00;

        $validRatioTickets = $closedTickets->filter(function ($t) {
            return ! is_null($t->actual_hours)
                && $t->actual_hours > 0
                && ! is_null($t->expected_hours);
        });

        if ($validRatioTickets->count() > 0) {
            $sumEfficiency = $validRatioTickets->sum(function ($t) {
                return ($t->expected_hours / $t->actual_hours) * 100;
            });
            $efficiencyScore = round($sumEfficiency / $validRatioTickets->count(), 2);
        } else {
            $efficiencyScore = 0.00;
        }

        $overrunCount = $closedTickets->where('overrun_flag', true)->count();
        $ontimeCount = $totalClosed - $overrunCount;
        $ontimeRate = $totalClosed > 0 ? round(($ontimeCount / $totalClosed) * 100, 1) : 100.0;
        $totalFeedbackCycles = (int) $closedTickets->sum('feedback_cycles');

        $totalAssignedCount = TicketHead::where('pic_id', $picId)->count();

        $activeTicketsCount = TicketHead::where('pic_id', $picId)
            ->whereIn('ticket_status', [
                TicketHead::STATUS_ASSIGNED,
                TicketHead::STATUS_IN_PROGRESS,
                TicketHead::STATUS_WAITING_REQUESTOR,
                TicketHead::STATUS_FEEDBACK_REQUESTOR,
            ])
            ->count();

        $inProgressCount = TicketHead::where('pic_id', $picId)
            ->where('ticket_status', TicketHead::STATUS_IN_PROGRESS)
            ->count();

        $waitingFeedbackCount = TicketHead::where('pic_id', $picId)
            ->where('ticket_status', TicketHead::STATUS_FEEDBACK_REQUESTOR)
            ->count();

        $waitingReviewCount = TicketHead::where('pic_id', $picId)
            ->where('ticket_status', TicketHead::STATUS_WAITING_REQUESTOR)
            ->count();

        $fotoUrl = $picKaryawan?->foto_karyawan ? asset('storage/foto_karyawan/' . $picKaryawan->foto_karyawan) : null;

        return [
            'pic_id'                => (string) $picId,
            'pic_name'              => $picName,
            'pic_nik'               => $picKaryawan?->nomor_identitas_karyawan ?? '-',
            'pic_jabatan'           => $picKaryawan?->jabatan?->nama_jabatan ?? '-',
            'pic_foto'              => $fotoUrl,
            'total_assigned'        => $totalAssignedCount,
            'total_tickets'         => $totalClosed,
            'closed_tickets'        => $totalClosed,
            'active_tickets'        => $activeTicketsCount,
            'in_progress_tickets'   => $inProgressCount,
            'waiting_review'        => $waitingReviewCount,
            'waiting_feedback'      => $waitingFeedbackCount,
            'total_actual_hours'    => $totalActualHours,
            'ontime_count'          => $ontimeCount,
            'ontime_rate'           => $ontimeRate,
            'avg_processing_hours'  => $avgProcessingHours,
            'efficiency_score'      => $efficiencyScore,
            'overrun_count'         => $overrunCount,
            'total_feedback_cycles' => $totalFeedbackCycles,
        ];
    }

    /**
     * Ringkasan KPI untuk semua PIC.
     */
    public function summaryForAllPics(): array
    {
        $this->seedDifficultyMasterIfMissing();

        $picIdsFromMaster = TicketAssignmentMaster::where('assignment_type', 'A02')
            ->where('is_active', 1)
            ->pluck('karyawan_id');

        $picIdsFromTickets = TicketHead::whereNotNull('pic_id')
            ->pluck('pic_id');

        $picIds = $picIdsFromMaster->merge($picIdsFromTickets)->unique()->filter()->values();

        $result = [];
        foreach ($picIds as $picId) {
            $result[] = $this->summaryForPic($picId);
        }

        return $result;
    }

    /**
     * Daftar detail tiket untuk PIC tertentu (termasuk sesi kerja dan detail).
     */
    public function ticketsForPic($picId): array
    {
        $this->seedDifficultyMasterIfMissing();

        $tickets = TicketHead::with(['ticketMenu', 'ticketSubMenu', 'difficulty'])
            ->where('pic_id', $picId)
            ->orderBy('closed_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                $difficulty = $t->difficulty;
                $sessions = TicketWorkSession::where('ticket_id', $t->id)
                    ->orderBy('cycle_number', 'asc')
                    ->get()
                    ->map(fn($s) => [
                        'cycle_number'     => $s->cycle_number,
                        'session_type'     => $s->session_type,
                        'started_at'       => $s->started_at ? (is_string($s->started_at) ? $s->started_at : $s->started_at->format('d/m/Y H:i')) : null,
                        'ended_at'         => $s->ended_at ? (is_string($s->ended_at) ? $s->ended_at : $s->ended_at->format('d/m/Y H:i')) : null,
                        'duration_minutes' => $s->duration_minutes,
                        'duration_hours'   => round($s->duration_minutes / 60, 2),
                        'notes'            => $s->notes,
                    ])->values();

                $statusMap = [
                    TicketHead::STATUS_ASSIGNED           => 'Ditugaskan',
                    TicketHead::STATUS_IN_PROGRESS        => 'Sedang Dikerjakan',
                    TicketHead::STATUS_WAITING_REQUESTOR  => 'Menunggu Cek Pengaju',
                    TicketHead::STATUS_CLOSED             => 'Selesai',
                    TicketHead::STATUS_FEEDBACK_REQUESTOR => 'Revisi / Feedback',
                    TicketHead::STATUS_CANCELLED          => 'Dibatalkan',
                ];

                return [
                    'id'              => $t->id,
                    'nomor_ticket'    => $t->nomor_ticket,
                    'menu_name'       => $t->ticketMenu?->nama_menu ?? '-',
                    'submenu_name'    => $t->ticketSubMenu?->nama_sub_menu ?? '-',
                    'requestor_name'  => $t->requestor_name ?? '-',
                    'ticket_status'   => $t->ticket_status,
                    'status_label'    => $statusMap[$t->ticket_status] ?? $t->ticket_status,
                    'priority'        => $t->ticket_priority,
                    'difficulty_name' => $difficulty ? $difficulty->name : ($t->ticket_priority ?? '-'),
                    'expected_hours'  => $t->expected_hours !== null ? (float) $t->expected_hours : null,
                    'actual_hours'    => $t->actual_hours !== null ? (float) $t->actual_hours : null,
                    'variance_hours'  => $t->variance_hours !== null ? (float) $t->variance_hours : null,
                    'overrun_flag'    => (bool) $t->overrun_flag,
                    'feedback_cycles' => (int) ($t->feedback_cycles ?? 0),
                    'pic_started_at'  => $t->pic_started_at ? (is_string($t->pic_started_at) ? $t->pic_started_at : $t->pic_started_at->format('d/m/Y H:i')) : null,
                    'finish_at'       => $t->finish_at ? (is_string($t->finish_at) ? $t->finish_at : $t->finish_at->format('d/m/Y H:i')) : null,
                    'closed_at'       => $t->closed_at ? (is_string($t->closed_at) ? $t->closed_at : $t->closed_at->format('d/m/Y H:i')) : null,
                    'created_at'      => $t->created_at ? (is_string($t->created_at) ? $t->created_at : $t->created_at->format('d/m/Y H:i')) : null,
                    'pdf_url'         => route('ticket.export', $t->id),
                    'sessions'        => $sessions,
                ];
            });

        return $tickets->toArray();
    }
}
