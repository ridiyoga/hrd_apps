<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use App\Models\DokumenLegal;
use Carbon\Carbon;

class DokumenSyncExpired extends Command
{
    protected $signature = 'dokumen:sync-expired {--dry-run : Jalankan tanpa update database}';

    protected $description = 'Sinkronisasi status dokumen legal yang expired secara otomatis';

    public function handle()
    {
        $today = Carbon::today();
        $dryRun = $this->option('dry-run');

        $this->info('🚀 Proses sinkronisasi dokumen expired dimulai :' . date("Y-m-d H:i"));
        $this->info('Tanggal acuan: ' . $today->toDateString());
        ActivityLog::create([
            'user_id'     => 1,
            'user_name'   => 'System HRD-APPS',
            'activity'    => 'dokumen_expired:sync',
            'description' => '🚀 Proses sinkronisasi dokumen expired dimulai :' . date("Y-m-d H:i"),
            'karir'       => 3,
            'keterangan'  => 'Success',
            'table'       => 'dokumen_legal',
            'created_at'  => now(),
        ]);

        // ===========================
        // QUERY DOKUMEN YANG EXPIRED
        // ===========================
        $query = DokumenLegal::where('status', 'aktif')
            ->whereNull('dokumen_pengganti') // ❗ sudah digantikan JANGAN di-expire
            ->whereDate('tanggal_akhir', '<', $today);

        $total = $query->count();

        if ($total === 0) {
            $this->info('✅ Tidak ada dokumen yang perlu di-update.');
            ActivityLog::create([
                'user_id'     => 1,
                'user_name'   => 'System HRD-APPS',
                'activity'    => 'dokumen_expired:sync',
                'description' => '✅ Tidak ada dokumen yang perlu di-update.',
                'karir'       => 3,
                'keterangan'  => 'Success',
                'table'       => 'dokumen_legal',
                'created_at'  => now(),
            ]);
            return Command::SUCCESS;
        }

        $this->warn("📄 Ditemukan {$total} dokumen yang akan di-set EXPIRED");
        ActivityLog::create([
            'user_id'     => 1,
            'user_name'   => 'System HRD-APPS',
            'activity'    => 'dokumen_expired:sync',
            'description' => "📄 Ditemukan {$total} dokumen yang akan di-set EXPIRED",
            'karir'       => 3,
            'keterangan'  => 'Success',
            'table'       => 'dokumen_legal',
            'created_at'  => now(),
        ]);
        if ($dryRun) {
            $this->info('🧪 DRY RUN MODE — tidak ada perubahan database');
            ActivityLog::create([
                'user_id'     => 1,
                'user_name'   => 'System HRD-APPS',
                'activity'    => 'dokumen_expired:sync',
                'description' => "🧪 DRY RUN MODE — tidak ada perubahan database",
                'karir'       => 3,
                'keterangan'  => 'Success',
                'table'       => 'dokumen_legal',
                'created_at'  => now(),
            ]);
            return Command::SUCCESS;
        }

        // ===========================
        // UPDATE STATUS
        // ===========================
        $updated = $query->update([
            'status' => 'expired',
            'updated_at' => now(),
        ]);

        $this->info("✅ {$updated} dokumen berhasil di-set EXPIRED");
        ActivityLog::create([
            'user_id'     => 1,
            'user_name'   => 'System HRD-APPS',
            'activity'    => 'dokumen_expired:sync',
            'description' => "✅ {$updated} dokumen berhasil di-set EXPIRED",
            'karir'       => 3,
            'keterangan'  => 'Success',
            'table'       => 'dokumen_legal',
            'created_at'  => now(),
        ]);
        return Command::SUCCESS;
    }
    //Untuk menjalankan command ini dalam mode dry-run (tanpa mengubah database) : php artisan dokumen:sync-expired --dry-run
    //Untuk menjalankan command ini : php artisan dokumen:sync-expired
}
