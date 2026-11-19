<?php

namespace App\Console\Commands;

use App\Models\TicketHead;
use App\Services\TicketKpiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AutoCloseUnrespondedTickets extends Command
{
    protected $signature = 'ticket:auto-close-unresponded
                            {--days=3 : Jumlah hari tanpa respons sebelum tiket ditutup}';

    protected $description =
        'Menutup tiket T05 secara otomatis jika requestor tidak merespons melewati batas waktu';

    public function handle(TicketKpiService $kpiService): int
    {
        $days = (int) $this->option('days');

        if ($days < 1) {
            $this->error('Opsi --days minimal 1 hari.');
            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);
        $closedCount = 0;
        $failedCount = 0;

        $this->info(
            "Memeriksa tiket T05 dengan finish_at sampai {$cutoff->toDateTimeString()}."
        );

        TicketHead::query()
            ->where('ticket_status', TicketHead::STATUS_WAITING_REQUESTOR)
            ->whereNotNull('finish_at')
            ->where('finish_at', '<=', $cutoff)
            ->orderBy('finish_at')
            ->chunkById(100, function ($tickets) use (
                $kpiService,
                &$closedCount,
                &$failedCount
            ) {
                foreach ($tickets as $ticket) {
                    try {
                        DB::transaction(function () use (
                            $ticket,
                            $kpiService,
                            &$closedCount
                        ) {
                            // Ambil ulang dan kunci data untuk mencegah
                            // menutup tiket yang sudah berubah status.
                            $current = TicketHead::query()
                                ->whereKey($ticket->id)
                                ->lockForUpdate()
                                ->first();

                            if (
                                ! $current ||
                                $current->ticket_status !==
                                    TicketHead::STATUS_WAITING_REQUESTOR ||
                                ! $current->finish_at ||
                                $current->finish_at->gt(now()->subDays(
                                    (int) $this->option('days')
                                ))
                            ) {
                                return;
                            }

                            // Gunakan layanan penutupan resmi agar KPI dihitung.
                            $kpiService->autoCloseBySystem($current);

                            $closedCount++;

                            Log::info(
                                'Tiket ditutup otomatis oleh Sistem karena requestor tidak merespons.',
                                [
                                    'ticket_id' => $current->id,
                                    'nomor_ticket' => $current->nomor_ticket,
                                    'finish_at' => $current->finish_at,
                                    'closed_at' => $current->closed_at,
                                    'days_without_response' =>
                                        (int) $this->option('days'),
                                ]
                            );
                        });
                    } catch (Throwable $e) {
                        $failedCount++;

                        Log::error(
                            'Gagal menutup tiket otomatis.',
                            [
                                'ticket_id' => $ticket->id,
                                'nomor_ticket' => $ticket->nomor_ticket,
                                'error' => $e->getMessage(),
                            ]
                        );

                        $this->error(
                            "Gagal: {$ticket->nomor_ticket} — {$e->getMessage()}"
                        );
                    }
                }
            }, 'id');

        $this->info("Tiket berhasil ditutup: {$closedCount}");
        $this->info("Tiket gagal diproses: {$failedCount}");

        return $failedCount > 0 ? self::FAILURE : self::SUCCESS;
    }
}