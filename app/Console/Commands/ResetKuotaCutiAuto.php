<?php

namespace App\Console\Commands;

use App\Models\CutiPeriod;
use App\Models\Karyawan;
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
    protected $signature = 'cuti:reset-cuti-auto';

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
        $tgl_mulai_period = Carbon::now()->format('Y-01-21');
        $tglreset = Carbon::parse($tgl_mulai_period)->subMonth(1)->format('Y-m-d');
        $tgl_selesai_period = Carbon::parse($tgl_mulai_period)->addYear(1)->subDay(1)->format('Y-m-d');

        $karyawan = Karyawan::with('PenempatanKerja')->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            // ->where('id', 'd26f55ab-4982-49a6-a392-fc42ddbfaf3a')
            // ->where('status_aktif', '')
            ->get();
        // dd($karyawan);
        $period = CutiPeriod::where('status', '1')->first();
        if ($period) {
            $period->update([
                'status' => 0
            ]);
        }
        KuotaCutiKaryawan::where('status', '1')->where('periode_id', $period->id_period)->update([
            'status' => 0
        ]);

        $add_new_period = new CutiPeriod();
        $add_new_period->id_period = Uuid::uuid4();
        $add_new_period->legal_number_period = 'CP-' . Carbon::now()->format('Y0221') . '-' . Carbon::parse($tgl_mulai_period)->addYear(1)->subDay(1)->format('Ymd');
        $add_new_period->nama_period = 'Periode Cuti Tahunan ' . Carbon::now()->year;
        $add_new_period->tanggal_mulai_period = $tgl_mulai_period;
        $add_new_period->tanggal_selesai_period = $tgl_selesai_period;
        $add_new_period->status = 1;
        $add_new_period->save();
        foreach ($karyawan as $item) {
            if ($item->penempatan_kerja != null) {
                if ($item->tgl_join == null) {
                    $bulan_masa_kerja = 0;
                } else if ($item->tgl_join >= $tglreset) {
                    $bulan_masa_kerja = 0;
                } else {
                    $bulan_masa_kerja  = floor(Carbon::parse($item->tgl_join)->diffInMonths($tglreset));
                }
                if ($item->PenempatanKerja != null) {
                    if ($item->PenempatanKerja->site_status == 'DEPO') {
                        $default_kuota = 8;
                        if ($bulan_masa_kerja >= 12) {
                            $kuota_cuti = $default_kuota;
                        } else {
                            $kuota_cuti = floor(($bulan_masa_kerja / 12) * $default_kuota);
                        }
                    } else {
                        $default_kuota = 14;
                        if ($bulan_masa_kerja >= 12) {
                            $kuota_cuti = $default_kuota;
                        } else {
                            $kuota_cuti = floor(($bulan_masa_kerja / 12) * $default_kuota);
                        }
                        // dd($kuota_cuti);
                    }
                } else {
                    Log::channel('kuota_cuti')->error('Gagal Reset Kuota Cuti', [
                        'periode' => $add_new_period->nama_period,
                        'karyawan' => $item->name,
                        'status' => 'Gagal',
                        'message' => 'Karyawan tidak memiliki penempatan kerja',
                    ]);
                }
                KuotaCutiKaryawan::create([
                    'id' => Uuid::uuid4(),
                    'karyawan_id' => $item->id,
                    'karyawan_nik' => $item->nomor_identitas_karyawan,
                    'periode_id' => $add_new_period->id_period,
                    'kuota' => $kuota_cuti,
                    'terpakai' => 0,
                    'sisa' => $kuota_cuti,
                    'status' => '1',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                ]);
            } else {
                Log::channel('kuota_cuti')->error('Gagal Reset Kuota Cuti', [
                    'periode' => $add_new_period->nama_period,
                    'karyawan' => $item->name,
                    'status' => 'Gagal',
                    'message' => 'Karyawan tidak memiliki penempatan kerja',
                ]);
            }
        }
        Log::channel('kuota_cuti')->info('Berhasil Reset Kuota Cuti', [
            'periode' => $add_new_period->nama_period,
            'status' => 'Sukses',
        ]);
    }
}
