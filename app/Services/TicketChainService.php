<?php

namespace App\Services;

use App\Models\TicketHead;
use App\Models\TicketApproval;
use App\Models\TicketApprovalValidator;
use App\Services\KaryawanPenggantiService;
use App\Models\TicketAssignmentMaster;
use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Collection;

class TicketChainService
{
    /**
     * Mengumpulkan informasi seluruh rangkaian tiket dalam satu grup pengajuan
     * serta mendeteksi siapa petugas yang saat ini sedang ditunggu tindakannya.
     *
     * @param TicketHead $ticket Tiket yang sedang dibuka
     * @return array
     */
    public function resolveChainInfo(TicketHead $ticket): array
    {
        $rootId = $ticket->root_ticket_id ?: $ticket->id;
        // Ambil semua tiket yang berada dalam satu rombongan / rantai pengajuan
        $chainTickets = TicketHead::with([
            'ticketMenu',
            'ticketSubMenu',
            'approvals' => function ($q) {
                $q->orderBy('step_order', 'asc');
            },
            'approvals.karyawan',
            'approvals.dependencyRule',
            'approvals.branchSubMenu.ticketMenu',
            'Requestor',
            'parentTicket',
        ])
            ->where(function ($q) use ($rootId, $ticket) {
                $q->where('root_ticket_id', $rootId)
                    ->orWhere('id', $rootId)
                    ->orWhere('parent_ticket_id', $rootId)
                    ->orWhere('parent_ticket_id', $ticket->id)
                    ->orWhere('generated_from_ticket_id', $ticket->id)
                    ->orWhere('origin_cross_ticket_id', $ticket->id);
                if (!empty($ticket->parent_ticket_id)) {
                    $q->orWhere('id', $ticket->parent_ticket_id)
                        ->orWhere('parent_ticket_id', $ticket->parent_ticket_id);
                }
            })
            ->orderBy('created_at', 'asc')
            ->get();
        if ($chainTickets->isEmpty()) {
            $chainTickets = collect([$ticket]);
        }
        $items = [];
        $stuckItems = [];
        foreach ($chainTickets as $t) {
            $isCurrent = ($t->id === $ticket->id);
            $isRoot    = ($t->id === $rootId || (empty($t->parent_ticket_id) && empty($t->generated_from_ticket_id) && empty($t->origin_cross_ticket_id)));
            $menuName    = $t->ticketMenu?->nama_menu ?? '-';
            $subMenuName = $t->ticketSubMenu?->nama_sub_menu ?? '-';
            $creatorName = $t->requestor_name ?: ($t->Requestor?->name ?? 'Pemohon');
            $statusInfo = $this->analyzeTicketStatus($t, $creatorName);
            $itemData = [
                'id'            => $t->id,
                'ticket_id'     => $t->id,
                'link_url'      => url('/ticket/lihat/' . ($t->ticket_menu_id ?: '1') . '/' . $t->id),
                'ticket_number' => $t->nomor_ticket ?: '-',
                'menu_name'     => $menuName,
                'sub_menu_name' => $subMenuName,
                'menu_id'       => $t->ticket_menu_id,
                'environment_kategori' => $t->ticketMenu?->kategori ?? 1,
                'is_current'    => $isCurrent,
                'is_root'       => $isRoot,
                'type_label'    => $isRoot ? 'Tiket Utama (Pengajuan Awal)' : 'Tiket Lanjutan',
                'parent_ticket_number' => $t->parentTicket?->nomor_ticket ?? null,
                'creator_name'  => $creatorName,
                'created_at'    => $t->created_at,
                'status_code'   => $t->ticket_status,
                'status_label'  => $statusInfo['status_label'],
                'status_badge'  => $statusInfo['status_badge'],
                'is_done'       => $statusInfo['is_done'],
                'is_rejected'   => $statusInfo['is_rejected'],
                'waiting_on'    => $statusInfo['waiting_on'],
                'waiting_role'  => $statusInfo['waiting_role'],
                'waiting_stage' => $statusInfo['waiting_stage'],
                'keterangan'    => $statusInfo['keterangan'],
                'waiting_on_list'      => $statusInfo['waiting_on_list'] ?? [],
                'non_blocking_waiting' => $statusInfo['non_blocking_waiting'] ?? [],
                'waiting_dependency_ticket_id' => $statusInfo['waiting_dependency_ticket_id'] ?? null,
                'waiting_dependency_ticket_number' => $statusInfo['waiting_dependency_ticket_number'] ?? null,
            ];
            $items[] = $itemData;
            if (!$statusInfo['is_done']) {
                $stuckItems[] = $itemData;
            }
        }
        // Tentukan ringkasan keseluruhan rombongan tiket
        $hasChain = count($items) > 1;
        $summary  = $this->buildOverallSummary($items, $stuckItems, $ticket);
        return [
            'has_chain'      => $hasChain,
            'total_tickets'  => count($items),
            'done_tickets'   => count($items) - count($stuckItems),
            'items'          => $items,
            'stuck_items'    => $stuckItems,
            'summary'        => $summary,
        ];
    }
    /**
     * Menganalisis status per tiket dan mencari siapa petugas yang sedang ditunggu.
     */
    private function analyzeTicketStatus(TicketHead $t, string $creatorName): array
    {
        // 1. Dibatalkan atau Ditolak
        if ($t->ticket_status === TicketHead::STATUS_CANCELLED || $t->approval_status === TicketHead::APPROVAL_REJECTED) {
            return [
                'is_done'       => true,
                'is_rejected'   => true,
                'status_badge'  => 'status-rejected',
                'status_label'  => 'Dibatalkan / Ditolak',
                'waiting_on'    => null,
                'waiting_role'  => null,
                'waiting_stage' => null,
                'keterangan'    => 'Pengajuan ini telah dibatalkan atau ditolak.',
                'stage_rank'    => -2,
            ];
        }

        // 2. Selesai
        if ($t->ticket_status === TicketHead::STATUS_CLOSED || !empty($t->finish_at)) {
            return [
                'is_done'       => true,
                'is_rejected'   => false,
                'status_badge'  => 'status-approved',
                'status_label'  => 'Selesai',
                'waiting_on'    => null,
                'waiting_role'  => null,
                'waiting_stage' => null,
                'keterangan'    => 'Seluruh proses pengajuan ini telah selesai dikerjakan.',
                'stage_rank'    => 99,
            ];
        }

        // 3. Draft Tiket Lanjutan (Belum Dilengkapi oleh Pemeriksa)
        if (($t->is_draft && $t->is_generated) || ($t->is_draft && !$t->is_submit) || $t->ticket_status === TicketHead::STATUS_DRAFT) {
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-draft',
                'status_label'  => 'Formulir Belum Dilengkapi (Draft)',
                'waiting_on'    => $creatorName,
                'waiting_role'  => 'Pemeriksa',
                'waiting_stage' => 'Pengisian Formulir Tiket Lanjutan',
                'keterangan'    => 'Pemeriksa (' . $creatorName . ') belum selesai melengkapi dan mengajukan rincian tiket lanjutan ini.',
                'stage_rank'    => 0,
            ];
        }

        // 4. Persiapan status persetujuan Atasan 1 & Atasan 2 (Harus disetujui terlebih dahulu sebelum masuk ke Pemeriksa/Checker)
        $hasApprove1 = !empty($t->approve1_id) || !empty($t->approve1_name) || ($t->approvals && $t->approvals->whereIn('role', ['atasan1', 'approve1'])->isNotEmpty());
        $approve1Done = !$hasApprove1
            || !empty($t->approve1_at)
            || !empty($t->approve1_signature)
            || ($t->approvals && $t->approvals->whereIn('role', ['atasan1', 'approve1'])->whereIn('status', ['completed', 'approved'])->isNotEmpty());

        if (!$approve1Done) {
            $atasan1Name = $t->approve1_name
                ?: ($t->approvals?->whereIn('role', ['atasan1', 'approve1'])->first()?->karyawan_name
                    ?? $t->approvals?->whereIn('role', ['atasan1', 'approve1'])->first()?->karyawan?->name
                    ?? 'Atasan 1');

            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-pending',
                'status_label'  => 'Menunggu Atasan 1',
                'waiting_on'    => $atasan1Name,
                'waiting_role'  => 'Atasan 1',
                'waiting_stage' => 'Persetujuan Atasan 1',
                'keterangan'    => 'Sedang menunggu persetujuan dari ' . $atasan1Name . ' (Atasan 1).',
                'waiting_dependency_ticket_id' => null,
                'waiting_dependency_ticket_number' => null,
                'stage_rank'    => 1,
            ];
        }

        $hasApprove2 = !empty($t->approve2_id) || !empty($t->approve2_name) || ($t->approvals && $t->approvals->whereIn('role', ['atasan2', 'approve2'])->isNotEmpty());
        $approve2Done = !$hasApprove2
            || !empty($t->approve2_at)
            || !empty($t->approve2_signature)
            || ($t->approvals && $t->approvals->whereIn('role', ['atasan2', 'approve2'])->whereIn('status', ['completed', 'approved'])->isNotEmpty());

        if (!$approve2Done) {
            $atasan2Name = $t->approve2_name
                ?: ($t->approvals?->whereIn('role', ['atasan2', 'approve2'])->first()?->karyawan_name
                    ?? $t->approvals?->whereIn('role', ['atasan2', 'approve2'])->first()?->karyawan?->name
                    ?? 'Atasan 2');

            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-pending',
                'status_label'  => 'Menunggu Atasan 2',
                'waiting_on'    => $atasan2Name,
                'waiting_role'  => 'Atasan 2',
                'waiting_stage' => 'Persetujuan Atasan 2',
                'keterangan'    => 'Sedang menunggu persetujuan dari ' . $atasan2Name . ' (Atasan 2).',
                'waiting_dependency_ticket_id' => null,
                'waiting_dependency_ticket_number' => null,
                'stage_rank'    => 2,
            ];
        }

        // Mulai dari sini tiket sudah lolos Atasan 1 & 2. Hitung dulu status "asli" tiket
        // ini sendiri (step 5, 7, 8, 9, 10 di bawah), lalu baru dibandingkan dengan status
        // tiket turunan (dependency) untuk menentukan mana yang lebih tertinggal/lebih blocking.
        $selfStatus = $this->resolveSelfStage($t, $creatorName);

        $pendingDependent = $this->resolvePendingDependentTicket($t);
        if ($pendingDependent && $pendingDependent['rank'] < $selfStatus['stage_rank']) {
            // Tiket turunan masih lebih tertinggal daripada tahap tiket ini sendiri ->
            // itulah bottleneck sebenarnya, tampilkan itu, bukan status diri sendiri.
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-pending',
                'status_label'  => 'Menunggu Ticket Lanjutan',
                'waiting_on'    => $pendingDependent['person'],
                'waiting_role'  => $pendingDependent['role'],
                'waiting_stage' => $pendingDependent['stage'],
                'keterangan'    => $pendingDependent['reason'],
                'waiting_dependency_ticket_id' => $pendingDependent['ticket_id'],
                'waiting_dependency_ticket_number' => $pendingDependent['ticket_number'],
                'stage_rank'    => $pendingDependent['rank'],
            ];
        }

        return $selfStatus;
    }

    /**
     * Menentukan status "asli" tiket ini sendiri (step 5, 7, 8, 9, 10, dan fallback),
     * tanpa mempertimbangkan tiket turunan/dependency sama sekali. Dipakai untuk
     * dibandingkan dengan status tiket turunan di analyzeTicketStatus().
     */
    private function resolveSelfStage(TicketHead $t, string $creatorName): array
    {
        $hasValidatorConfig = $this->hasValidatorInMaster($t);
        $isValidated = (bool) $t->validator || !empty($t->validator_at);
        $isCheckerDone = (bool) $t->checked || !empty($t->checker_name);

        // 5. Menunggu Persetujuan / Pemeriksaan / Validasi dari tabel approvals
        $allPendingApprovals = $t->approvals
            ? $t->approvals
            ->where('status', 'pending')
            ->sortBy('step_order')
            : collect();

        // Checker lintas environment bersifat NON-BLOCKING: berjalan sendiri-sendiri dan
        // hanya berfungsi sebagai pengingat/informasi perubahan. Mereka TIDAK boleh
        // menahan status tiket di "Menunggu Pemeriksa", jadi dipisahkan dari approval
        // yang benar-benar menghambat.
        $pendingApprovals = $allPendingApprovals
            ->reject(fn($a) => $this->isNonBlockingApproval($a, $t))
            ->values();
        $nonBlockingWaiting = $this->buildNonBlockingList(
            $allPendingApprovals->filter(fn($a) => $this->isNonBlockingApproval($a, $t))->values()
        );
        if ($pendingApprovals->isNotEmpty()) {
            $firstStepOrder = $pendingApprovals->first()->step_order;
            $sameStepApprovals = $pendingApprovals->filter(fn($a) => $a->step_order === $firstStepOrder || (empty($a->step_order) && empty($firstStepOrder)));
            $firstRole = $pendingApprovals->first()->role;
            $roleLabel = $this->translateRoleName($firstRole);

            // Jika role adalah checker atau memiliki banyak petugas di step yang sama (multichecker)
            $personDetails = $sameStepApprovals->map(function ($a) {
                return [
                    'name'     => $a->karyawan_name ?: ($a->karyawan?->name ?? 'Petugas Terkait'),
                    'sub_menu' => $a->branchSubMenu?->nama_sub_menu,
                ];
            })->unique('name')->values()->all();

            $personNames = collect($personDetails)->map(function ($p) {
                return $p['name'] . ($p['sub_menu'] ? ' (' . $p['sub_menu'] . ')' : '');
            })->implode(' / ');

            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-pending',
                'status_label'  => 'Menunggu ' . $roleLabel,
                'waiting_on'    => $personNames,
                'waiting_on_list' => $personDetails,
                'waiting_role'  => $roleLabel,
                'waiting_stage' => 'Persetujuan / ' . $roleLabel,
                'keterangan'    => 'Sedang menunggu tindakan dari ' . $personNames . '.',
                'waiting_dependency_ticket_id' => null,
                'waiting_dependency_ticket_number' => null,
                'stage_rank'    => $this->roleRank($firstRole),
            ];
        }

        // 7. Menunggu Validasi
        // CATATAN: STATUS_WAITING_CHECKER (T12) SENGAJA tidak dimasukkan di sini.
        // Selama ticket_status masih T12, artinya secara status resmi tiket ini masih
        // dianggap "menunggu proses tiket turunan/lain dalam satu group root_ticket_id" -
        // bukan berarti sudah siap masuk ke tahap Validasi. Hanya T11 (STATUS_WAITING_VALIDATOR)
        // atau kombinasi status lain di bawah ini yang benar-benar berarti siap divalidasi.
        if (
            !$isValidated &&
            (
                $t->ticket_status === TicketHead::STATUS_WAITING_VALIDATOR ||
                (
                    $hasValidatorConfig &&
                    $isCheckerDone &&
                    in_array($t->ticket_status, [
                        TicketHead::STATUS_APPROVED,
                        TicketHead::STATUS_WAITING_ASSIGNER,
                        TicketHead::STATUS_SUBMITTED
                    ])
                )
            )
        ) {
            // Tampilkan label singkat per environment (tanpa nama karyawan)
            $validatorNames = $this->resolveValidatorLabel($t);
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-pending',
                'status_label'  => 'Validasi',
                'waiting_on'    => $validatorNames,
                // Satu daftar gabungan: Validator (blocking) + Pemeriksa lintas env (non-blocking)
                'waiting_on_list' => array_merge(
                    [[
                        'name'         => $validatorNames,   // contoh: "Validator Epicor SPS"
                        'role'         => 'Validator',
                        'sub_menu'     => null,
                        'environment'  => null,
                        'non_blocking' => false,
                    ]],
                    $nonBlockingWaiting
                ),
                // Checker lintas environment yang belum memeriksa (informasi saja, tidak menghambat)
                'non_blocking_waiting' => $nonBlockingWaiting,
                'waiting_role'  => 'Validator',
                'waiting_stage' => 'Validasi Dokumen',
                'keterangan'    => 'Pengajuan telah selesai diperiksa oleh Pemeriksa dan saat ini sedang menunggu validasi oleh ' . $validatorNames . '.'
                    . (!empty($nonBlockingWaiting)
                        ? ' Pemeriksa lintas sistem yang belum memeriksa tidak menghambat proses ini.'
                        : ''),
                'waiting_dependency_ticket_id' => null,
                'waiting_dependency_ticket_number' => null,
                'stage_rank'    => 4,
            ];
        }

        // 8. Sedang Dikerjakan oleh Petugas Pelaksana (PIC IT)
        if (in_array($t->ticket_status, [TicketHead::STATUS_ASSIGNED, TicketHead::STATUS_IN_PROGRESS])) {
            $picName = $t->pic_name ?: 'Petugas Pelaksana (PIC IT)';
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-process',
                'status_label'  => 'Sedang Dikerjakan',
                'waiting_on'    => $picName,
                'waiting_role'  => 'Petugas Pelaksana (PIC)',
                'waiting_stage' => 'Pengerjaan Perubahan Data',
                'keterangan'    => 'Pengajuan telah disetujui dan saat ini sedang dikerjakan oleh ' . $picName . '.',
                'stage_rank'    => 6,
            ];
        }

        // 9. Menunggu Konfirmasi / Respon Pemohon
        if (in_array($t->ticket_status, [TicketHead::STATUS_WAITING_REQUESTOR, TicketHead::STATUS_FEEDBACK_REQUESTOR])) {
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-waiting',
                'status_label'  => 'Menunggu Konfirmasi Pemohon',
                'waiting_on'    => $creatorName,
                'waiting_role'  => 'Pemohon',
                'waiting_stage' => 'Konfirmasi Hasil Pengerjaan',
                'keterangan'    => 'Petugas telah menyelesaikan pekerjaan dan menunggu konfirmasi penerimaan dari ' . $creatorName . '.',
                'stage_rank'    => 7,
            ];
        }

        // 10. Status Disetujui (Menunggu Eksekusi / Penugasan PIC)
        if (in_array($t->ticket_status, [TicketHead::STATUS_APPROVED, TicketHead::STATUS_WAITING_ASSIGNER])) {
            $env = (int) ($t->ticketMenu?->kategori ?: 1);
            $envLabels = [
                1 => [
                    'name'       => 'Epicor ERP',
                    'label'      => 'Menunggu Penugasan PIC Epicor',
                    'waiting_on' => 'ASSIGNER EPICOR',
                ],
                2 => [
                    'name'       => 'Accurate Online SP',
                    'label'      => 'Menunggu Penugasan PIC Accurate SP',
                    'waiting_on' => 'ASSIGNER ACCURATE SP',
                ],
                3 => [
                    'name'       => 'Accurate Online AJA',
                    'label'      => 'Menunggu Penugasan PIC Accurate AJA',
                    'waiting_on' => 'ASSIGNER ACCURATE AJA',
                ],
            ];
            $cfg = $envLabels[$env] ?? $envLabels[1];   // nilai tak dikenal dianggap Epicor
            $hasAssignor = TicketAssignmentMaster::where('assignment_type', 'A01')
                ->where('is_active', 1)
                ->forEnvironment($env)
                ->exists();
            if (!$hasAssignor) {
                return [
                    'is_done'       => false,
                    'is_rejected'   => false,
                    'status_badge'  => 'status-pending',
                    'status_label'  => $cfg['label'],
                    'waiting_on'    => 'Petugas Terkait',
                    'waiting_role'  => 'Petugas Terkait',
                    'waiting_stage' => 'Penugasan Petugas Pelaksana',
                    'keterangan'    => "Pengajuan telah disetujui, namun tidak ditemukan Assignor yang aktif untuk environment {$cfg['name']}.",
                    'stage_rank'    => 5,
                ];
            }
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-waiting',
                'status_label'  => $cfg['label'],
                'waiting_on'    => $cfg['waiting_on'],
                'waiting_role'  => $cfg['waiting_on'],
                'waiting_stage' => 'Penugasan Petugas Pelaksana',
                'keterangan'    => "Semua persetujuan/validasi telah lengkap. Menunggu penugasan PIC untuk environment {$cfg['name']}.",
                'stage_rank'    => 5,
            ];
        }

        // 10.5 Status masih T12 (STATUS_WAITING_CHECKER) tapi tidak ditemukan tiket turunan
        // aktif (misal race condition / data belum konsisten). Tetap jangan dianggap "Validasi"
        // atau fallback generik - beri label yang jujur bahwa proses turunan yang ditunggu.
        if ($t->ticket_status === TicketHead::STATUS_WAITING_CHECKER) {
            return [
                'is_done'       => false,
                'is_rejected'   => false,
                'status_badge'  => 'status-pending',
                'status_label'  => 'Menunggu Proses Tiket Turunan',
                'waiting_on'    => 'Petugas Terkait',
                'waiting_role'  => 'Petugas Terkait',
                'waiting_stage' => 'Penyelesaian Tiket Turunan',
                'keterangan'    => 'Status pengajuan masih menunggu penyelesaian tiket turunan/lain dalam satu rangkaian pengajuan ini.',
                // Rank sengaja dibuat tinggi (bukan 3), sama seperti fallback umum di
                // bawah (case 11): label ini generik/placeholder ("Petugas Terkait")
                // sehingga HARUS selalu kalah dibanding status konkret dari tiket
                // turunan (resolvePendingDependentTicket) jika ditemukan - lihat
                // perbandingan rank di analyzeTicketStatus(). Sebelumnya rank=3 ini
                // sering seri dengan rank role 'checker' (juga 3), sehingga info
                // tiket turunan yang sebenarnya (mis. nama checker & nomor tiketnya)
                // tidak pernah ditampilkan dan malah muncul label generik ini.
                'stage_rank'    => 999,
            ];
        }

        // 11. Fallback umum — tidak ada status spesifik yang cocok untuk tiket ini sendiri.
        // Rank dibuat sangat tinggi (999) supaya status tiket turunan APAPUN (yang konkret)
        // akan selalu menang dibanding fallback generik ini.
        return [
            'is_done'       => false,
            'is_rejected'   => false,
            'status_badge'  => 'status-pending',
            'status_label'  => 'Sedang Diproses',
            'waiting_on'    => 'Petugas Terkait',
            'waiting_role'  => 'Petugas Terkait',
            'waiting_stage' => 'Proses Pengajuan',
            'keterangan'    => 'Pengajuan sedang dalam proses penanganan.',
            'stage_rank'    => 999,
        ];
    }

    /**
     * Apakah approval ini bersifat non-blocking (checker lintas environment)?
     *
     * Penentu yang sama dengan ApprovalController (validasi & tryFinalizeApproval):
     * checker dengan dependency_rule yang is_cross_environment = true tidak
     * menghambat tiket.
     */
    private function isNonBlockingApproval($approval, TicketHead $t): bool
    {
        return strtolower((string) $approval->role) === 'checker'
            && (bool) ($approval->dependencyRule?->is_cross_environment ?? false);
    }

    /**
     * Susun daftar checker non-blocking untuk ditampilkan di card (nama + sub menu).
     */
    private function buildNonBlockingList(Collection $approvals): array
    {
        return $approvals
            ->map(fn($a) => [
                'name'        => $a->karyawan_name ?: ($a->karyawan?->name ?? 'Petugas Terkait'),
                'role'        => 'Pemeriksa',
                'sub_menu'    => $a->branchSubMenu?->nama_sub_menu,
                'environment' => $a->branchSubMenu?->ticketMenu?->kategori_display,
                'non_blocking' => true,
            ])
            ->unique('name')
            ->values()
            ->all();
    }

    /**
     * Memetakan nama role approval (atasan1/atasan2/checker/validator/dst) ke
     * urutan tahap alur proses, dipakai untuk membandingkan seberapa "tertinggal"
     * status tersebut dibanding tahap-tahap lain (draft=0 ... konfirmasi pemohon=7).
     */
    private function roleRank(?string $role): int
    {
        $role = strtolower((string) $role);
        return match (true) {
            str_contains($role, 'atasan1') || str_contains($role, 'approve1') => 1,
            str_contains($role, 'atasan2') || str_contains($role, 'approve2') => 2,
            str_contains($role, 'check') || str_contains($role, 'periksa') => 3,
            str_contains($role, 'valid') => 4,
            str_contains($role, 'assign') || str_contains($role, 'pic') => 5,
            default => 3,
        };
    }

    /**
     * Mencari ticket turunan/dependency yang masih menghambat
     * ticket utama untuk masuk ke tahap berikutnya.
     *
     * Return null jika tidak ada dependency yang pending.
     */
    private function resolvePendingDependentTicket(TicketHead $ticket): ?array
    {
        $dependentTickets = TicketHead::query()
            ->with('Requestor')
            ->where('parent_ticket_id', $ticket->id)
            ->orderBy('created_at', 'asc')
            ->get();
        if ($dependentTickets->isEmpty()) {
            return null;
        }
        foreach ($dependentTickets as $dependent) {
            // Cancelled / rejected tidak menjadi blocker
            if (
                $dependent->ticket_status === TicketHead::STATUS_CANCELLED ||
                $dependent->approval_status === TicketHead::APPROVAL_REJECTED
            ) {
                continue;
            }
            // Sudah selesai tidak menjadi blocker
            if (
                $dependent->ticket_status === TicketHead::STATUS_CLOSED ||
                !empty($dependent->finish_at)
            ) {
                continue;
            }
            $person = $dependent->requestor_name
                ?: ($dependent->Requestor?->name ?? 'Petugas Terkait');
            // Draft / belum submit
            if (
                $dependent->is_draft ||
                !$dependent->is_submit ||
                $dependent->ticket_status === TicketHead::STATUS_DRAFT
            ) {
                return [
                    'waiting'       => true,
                    'type'          => 'dependent_ticket',
                    'ticket_id'     => $dependent->id,
                    'ticket_number' => $dependent->nomor_ticket ?: '-',
                    'person'        => $person,
                    'role'          => 'Checker',
                    'stage'         => 'Pengisian Formulir Tiket Lanjutan',
                    'reason'        => sprintf(
                        'Ticket lanjutan %s masih berupa draft dan belum diajukan oleh %s.',
                        $dependent->nomor_ticket ?: '-',
                        $person
                    ),
                    'rank'          => 0,
                ];
            }
            // Sudah submit: analisis status aktual dari tiket turunan tersebut, termasuk
            // tahap (rank) nya, supaya bisa dibandingkan dengan tahap tiket induk sendiri.
            $depStatus = $this->analyzeTicketStatus($dependent, $person);

            return [
                'waiting'       => true,
                'type'          => 'dependent_ticket',
                'ticket_id'     => $dependent->id,
                'ticket_number' => $dependent->nomor_ticket ?: '-',
                'person'        => $depStatus['waiting_on'] ?: $person,
                'role'          => $depStatus['waiting_role'] ?: 'Ticket Lanjutan',
                'stage'         => $depStatus['waiting_stage'] ?: 'Penyelesaian Ticket Lanjutan',
                'reason'        => sprintf(
                    'Menunggu tiket lanjutan %s: %s.',
                    $dependent->nomor_ticket ?: '-',
                    $depStatus['keterangan'] ?? 'dalam proses penanganan'
                ),
                'rank'          => $depStatus['stage_rank'] ?? 3,
            ];
        }
        return null;
    }

    /**
     * Memeriksa apakah tipe tiket / environment ini memiliki konfigurasi validator.
     */
    public function hasValidatorInMaster(TicketHead $ticket): bool
    {
        $environment = $ticket->ticketMenu?->kategori;
        if (empty($environment)) {
            $nomor = (string) $ticket->nomor_ticket;
            if (str_contains($nomor, 'EPIC')) {
                $environment = 1;
            } elseif (str_contains($nomor, 'AOL-SP')) {
                $environment = 2;
            } elseif (str_contains($nomor, 'AOL-AJA')) {
                $environment = 3;
            }
        }
        $hasValidator = TicketApprovalValidator::where('is_active', true)
            ->where(function ($query) use ($environment) {
                $query->whereNull('environment');
                if (!is_null($environment)) {
                    $query->orWhere('environment', $environment);
                }
            })
            ->exists();

        if (!$hasValidator) {
            $hasValidator = TicketApprovalValidator::where('is_active', true)->exists();
        }

        return $hasValidator;
    }


    /**
     * Label singkat validator per environment (tanpa nama karyawan):
     * "Validator Epicor SPS", "Validator AoL SP", "Validator AoL AJA".
     */
    public function resolveValidatorLabel(TicketHead $ticket): string
    {
        $environment = $ticket->ticketMenu?->kategori;
        if (empty($environment)) {
            $nomor = (string) $ticket->nomor_ticket;
            if (str_contains($nomor, 'AOL-SP')) {
                $environment = 2;
            } elseif (str_contains($nomor, 'AOL-AJA')) {
                $environment = 3;
            } else {
                $environment = 1;
            }
        }

        return match ((int) $environment) {
            2       => 'Validator AoL SP',
            3       => 'Validator AoL AJA',
            default => 'Validator Epicor SPS',
        };
    }

    /**
     * Mengambil nama validator yang bertugas memvalidasi tiket ini.
     * Jika validator sedang cuti/sakit dan memiliki pengganti aktif,
     * tampilkan nama karyawan pengganti yang berlaku pada hari ini.
     */
    public function resolveValidatorNames(TicketHead $ticket): string
    {
        $environment = $ticket->ticketMenu?->kategori;
        if (empty($environment)) {
            $nomor = (string) $ticket->nomor_ticket;
            if (str_contains($nomor, 'EPIC')) {
                $environment = 1;
            } elseif (str_contains($nomor, 'AOL-SP')) {
                $environment = 2;
            } elseif (str_contains($nomor, 'AOL-AJA')) {
                $environment = 3;
            }
        }
        $validators = TicketApprovalValidator::with('karyawan')
            ->where('is_active', true)
            ->where(function ($query) use ($environment) {
                $query->whereNull('environment');

                if (!is_null($environment)) {
                    $query->orWhere('environment', $environment);
                }
            })
            ->get();
        if ($validators->isEmpty()) {
            $validators = TicketApprovalValidator::with('karyawan')
                ->where('is_active', true)
                ->get();
        }
        $penggantiService = app(KaryawanPenggantiService::class);
        $tanggalHariIni = now()->toDateString();
        $names = $validators
            ->map(function ($validator) use (
                $penggantiService,
                $tanggalHariIni
            ) {
                $karyawanAsli = $validator->karyawan;
                if (!$karyawanAsli) {
                    return null;
                }
                $hasil = $penggantiService
                    ->cariKaryawanPenggantiAktif(
                        $karyawanAsli,
                        $tanggalHariIni
                    );
                $karyawanAktif = $hasil['karyawan'] ?? null;
                return $karyawanAktif?->name
                    ?: $karyawanAsli->name;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
        if (!empty($names)) {
            return implode(' / ', $names);
        }
        return $ticket->validator_name ?: 'Tim Validator';
    }

    /**
     * Menerjemahkan nama peran teknis ke Bahasa Indonesia yang ramah pengguna.
     */
    private function translateRoleName(?string $role): string
    {
        $map = [
            'approve1' => 'Atasan 1',
            'approve2' => 'Atasan 2',
            'atasan1'  => 'Atasan 1',
            'atasan2'  => 'Atasan 2',
            'checker'  => 'Pemeriksa',
            'validator' => 'Validator',
            'pic'      => 'Petugas Pelaksana',
            'assign'   => 'Koordinator Penugasan',
        ];

        return $map[strtolower((string) $role)] ?? 'Petugas Penyetuju';
    }

    /**
     * Menyusun kalimat ringkasan yang menjelaskan kondisi tertahan rombongan tiket.
     */
    private function buildOverallSummary(
        array $items,
        array $stuckItems,
        TicketHead $currentTicket
    ): array {
        // Cari informasi ticket yang sedang dibuka
        $currentItem = collect($items)->firstWhere('id', $currentTicket->id);

        // Semua ticket selesai
        if (empty($stuckItems)) {
            return [
                'type'        => 'success',
                'title'       => 'Seluruh Rangkaian Pengajuan Telah Selesai',
                'description' => 'Semua proses pada tiket utama dan tiket lanjutan dalam group pengajuan ini telah selesai dikerjakan.',
                'waiting_on'  => null,
                'waiting_sub' => null,
            ];
        }

        // Cari tiket turunan paling aktif / leaf yang saat ini menjadi bottleneck utama rombongan
        // (yaitu tiket stuck yang tidak sedang menunggu tiket lain lagi)
        $activeBottleneck = collect($stuckItems)
            ->firstWhere(fn($item) => empty($item['waiting_dependency_ticket_id']))
            ?? collect($stuckItems)->last();

        // Validasi berjalan PARALEL per tiket (tiap tiket punya validator sesuai
        // environment-nya sendiri), bukan berurutan. Jadi kalau tiket yang sedang dibuka
        // juga menunggu Validator, tampilkan validator tiket ini sendiri - jangan
        // menampilkan tiket lain dalam rantai yang kebetulan lebih dulu dibuat.
        if (
            !empty($currentItem)
            && empty($currentItem['is_done'])
            && empty($currentItem['waiting_dependency_ticket_id'])
            && ($currentItem['waiting_role'] ?? null) === 'Validator'
        ) {
            $activeBottleneck = $currentItem;
        }

        $isCurrentBottleneck = ($activeBottleneck['id'] ?? null) === $currentTicket->id;

        if ($isCurrentBottleneck) {
            $dependencyNumber = $currentItem['waiting_dependency_ticket_number'] ?? null;
            if ($dependencyNumber) {
                return [
                    'type'            => 'warning',
                    'title'           => 'Menunggu Ticket Lanjutan (' . $dependencyNumber . ')',
                    'description'     => $currentItem['keterangan'],
                    'waiting_on'      => $currentItem['waiting_on'],
                    'waiting_on_list' => $currentItem['waiting_on_list'] ?? [],
                    'waiting_sub'     => $currentItem['waiting_role'] . ' — ' . $dependencyNumber,
                    'non_blocking_waiting' => $currentItem['non_blocking_waiting'] ?? [],
                ];
            }

            return [
                'type'            => 'warning',
                'title'           => ($currentItem['waiting_role'] ?? null) === 'Validator'
                    ? 'Menunggu Validator'
                    : $currentItem['status_label'],
                'description'     => $currentItem['keterangan'],
                'waiting_on'      => $currentItem['waiting_on'],
                'waiting_on_list' => $currentItem['waiting_on_list'] ?? [],
                'waiting_sub'     => $currentItem['waiting_role'] . ' — ' . $currentItem['waiting_stage'],
                'non_blocking_waiting' => $currentItem['non_blocking_waiting'] ?? [],
            ];
        }

        // Tiket yang dibuka bukan titik bottleneck aktif utama (bisa sudah selesai atau sedang menunggu tiket turunan)
        $bottleneckTicketNum = $activeBottleneck['ticket_number'] ?? '-';
        $bottleneckSubMenu   = $activeBottleneck['sub_menu_name'] ?? '-';
        $bottleneckRole      = $activeBottleneck['waiting_role'] ?? 'Petugas Terkait';
        $bottleneckPerson    = $activeBottleneck['waiting_on'] ?? '-';

        $isCurrentDone = !empty($currentItem['is_done']);
        $titlePrefix   = $isCurrentDone ? 'Tiket Ini Selesai ' : '';

        return [
            'type'            => 'warning',
            'title'           => $titlePrefix . 'Menunggu ' . $bottleneckRole . ' (' . $bottleneckTicketNum . ')',
            'description'     => 'Alur pengajuan group saat ini tertahan pada ' . $bottleneckSubMenu . ' (' . $bottleneckTicketNum . ') yang sedang menunggu tindakan dari ' . $bottleneckPerson . ' (' . $bottleneckRole . ').',
            'waiting_on'      => $bottleneckPerson,
            'waiting_on_list' => $activeBottleneck['waiting_on_list'] ?? [],
            'waiting_sub'     => $bottleneckRole . ' — ' . $bottleneckTicketNum,
            'non_blocking_waiting' => $activeBottleneck['non_blocking_waiting'] ?? [],
        ];
    }

    /**
     * Menyelaraskan status seluruh tiket dalam satu grup (root_ticket_id).
     * Jika seluruh checker & tiket turunan telah disubmit dan selesai,
     * update seluruh tiket dalam grup secara serentak menjadi STATUS_WAITING_VALIDATOR (T11) atau STATUS_APPROVED (T02).
     */
    public function syncGroupStatusToValidatorOrApproved(TicketHead $ticket): void
    {
        $rootId = $ticket->root_ticket_id ?: $ticket->id;
        $emailService = app(TicketApprovalEmailService::class);

        // Periksa apakah seluruh tiket dalam rombongan sudah lengkap disetujui & disubmit
        if (!$emailService->isChainFullyApprovedAndChecked($ticket)) {
            return;
        }

        // Tentukan apakah grup membutuhkan validator
        $hasValidator = $this->hasValidatorInMaster($ticket);

        $targetStatus = $hasValidator ? TicketHead::STATUS_WAITING_VALIDATOR : TicketHead::STATUS_APPROVED;

        // Update seluruh tiket dalam grup yang belum closed/cancelled
        TicketHead::where(function ($q) use ($rootId) {
            $q->where('id', $rootId)
                ->orWhere('root_ticket_id', $rootId);
        })
            ->whereNotIn('ticket_status', [TicketHead::STATUS_CLOSED, TicketHead::STATUS_CANCELLED])
            ->update([
                'ticket_status'   => $targetStatus,
                'approval_status' => TicketHead::APPROVAL_APPROVED,
                'is_draft'        => false,
            ]);
    }
}