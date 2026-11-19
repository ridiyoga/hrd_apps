<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftarkan command buatan kamu di sini.
     */
    protected $commands = [
        \App\Console\Commands\DokumenSyncExpired::class,
        \App\Console\Commands\SendDokumenLegalExpiryReminderEmails::class,
        \App\Console\Commands\ResetKuotaCutiAuto::class,
        // tambahkan command lain kalau ada
    ];

    /**
     * Jadwal otomatis (opsional).
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('dokumen:send-expiry-reminder-email')
            ->dailyAt('00:15')
            ->withoutOverlapping()
            ->runInBackground();
        $schedule->command('dokumen:sync-expired')
            ->dailyAt('00:30')
            ->withoutOverlapping()
            ->runInBackground();
        $schedule->command('cuti:reset-cuti-auto')
            ->yearly()
            ->runInBackground();

        //untuk menjalankan di production: php artisan schedule:run
    }

    /**
     * Auto-load semua command dari folder Commands.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
