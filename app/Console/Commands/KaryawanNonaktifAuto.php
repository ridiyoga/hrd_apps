<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\CutiPeriod;
use App\Models\Karyawan;
use App\Models\KaryawanNonaktif;
use App\Models\KuotaCutiKaryawan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ResetKuotaCutiAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karyawan:nonaktif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = Carbon::now()->subMonth(1)->format('Y-m-21');
        $end = Carbon::now()->format('Y-m-21');
        // $tglreset = Carbon::parse($tgl_mulai_period)->subMonth(1)->format('Y-m-d');
        // $tgl_selesai_period = Carbon::parse($tgl_mulai_period)->addYear(1)->subDay(1)->format('Y-m-d');

        $this->info('Proses penonaktifan karyawan dimulai');
        $nonaktif = KaryawanNonaktif::where('status', 0)->whereBetween('tanggal_nonaktif', [$start, $end])->get();
        // $asoy = [];
        foreach ($nonaktif as $non) {
            Karyawan::where('id', $non->id_karyawan)->update([
                'status_aktif' => 'NON AKTIF'
            ]);
            $non->update([
                'status' => 1
            ]);
            // $asoy[] = $non->nama_karyawan;
        }
        if ($nonaktif->isNotEmpty()) {
            ActivityLog::create([
                'user_id'     => 1,
                'user_name'   => 'System HRD-APPS',
                'activity'    => 'finger:sync',
                'description' => "Proses penonaktifan karyawan ...",
                'karir'       => 3,
                'keterangan'  => 'Success',
                'table'       => 'attendance_logs',
                'created_at'  => now(),
            ]);
            $this->info("Total {$nonaktif->count()} karyawan berhasil dinonaktifkan.");
        } else {
            ActivityLog::create([
                'user_id'     => 1,
                'user_name'   => 'System HRD-APPS',
                'activity'    => 'finger:sync',
                'description' => "Tidak ada karyawan yang dinonaktifkan...",
                'karir'       => 3,
                'keterangan'  => 'Success',
                'table'       => 'attendance_logs',
                'created_at'  => now(),
            ]);
            $this->warn('Tidak ada karyawan yang perlu dinonaktifkan.');
        }

        Log::channel('karyawan_nonaktif')->info('========== COMMAND SELESAI ==========');

        // $this->info($start);
        // $this->info($end);
        // foreach ($asoy as $namaKaryawan) {
        //     $this->info($namaKaryawan);
        // }
        $this->info('Command karyawan nonaktif berhasil dijalankan.');

        return self::SUCCESS;
    }
}
