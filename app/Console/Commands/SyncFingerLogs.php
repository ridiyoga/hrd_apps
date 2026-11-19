<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\FingerMachine;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncFingerLogs extends Command
{
    protected $signature = 'finger:sync';
    protected $description = 'Sync logs dari semua mesin fingerprint via API .NET (paralel, aman error)';

    public function handle()
    {
        $machines = FingerMachine::where('IsActive', 1)->get();

        if ($machines->isEmpty()) {
            $this->warn(' Tidak ada mesin fingerprint.');
            ActivityLog::create([
                'user_id'     => 1,
                'user_name'   => 'System HRD-APPS',
                'activity'    => 'finger:sync',
                'description' => 'Tidak ada mesin fingerprint.',
                'karir'       => 3,
                'keterangan'  => 'Failed',
                'table'       => 'attendance_logs',
                'created_at'  => now(),
            ]);
            return Command::SUCCESS;
        }

        $this->info(' Mulai sync dari ' . $machines->count() . ' mesin, Jam : ' . date("Y-m-d H:i"));
        ActivityLog::create([
            'user_id'     => 1,
            'user_name'   => 'System HRD-APPS',
            'activity'    => 'finger:sync',
            'description' => 'Mulai sync dari ' . $machines->count() . ' mesin, Jam : ' . date("Y-m-d H:i"),
            'karir'       => 3,
            'keterangan'  => 'Success',
            'table'       => 'attendance_logs',
            'created_at'  => now(),
        ]);
        // RANGE WAKTU
        $now = now();
        $dateFrom = $now->copy()->subDay(1)->toIso8601String();
        $dateTo   = $now->copy()->addDay(1)->toIso8601String();

        foreach ($machines as $machine) {

            $ip = $machine->Ip;
            $port = $machine->Port;
            $name = $machine->Name;

            $this->line(" Proses mesin Finger {$name} IP: {$ip}:{$port} ...");
            ActivityLog::create([
                'user_id'     => 1,
                'user_name'   => 'System HRD-APPS',
                'activity'    => 'finger:sync',
                'description' => "Proses mesin Finger {$name} IP:{$ip}:{$port} ...",
                'karir'       => 3,
                'keterangan'  => 'Success',
                'table'       => 'attendance_logs',
                'created_at'  => now(),
            ]);
            try {

                // ⚠️ JALANKAN SEQUENTIAL — TANPA POOL
                $response = Http::timeout(520)->get('http://localhost:5000/api/ZK/logs', [
                    'ip'       => $ip,
                    'port'     => $port,
                    'dateFrom' => $dateFrom,
                    'dateTo'   => $dateTo,
                ]);
            } catch (\Exception $e) {

                $this->error(" {$ip}: gagal connect ({$e->getMessage()})");
                ActivityLog::create([
                    'user_id'     => 1,
                    'user_name'   => 'System HRD-APPS',
                    'activity'    => 'finger:sync',
                    'description' => " {$ip}: gagal connect ({$e->getMessage()})",
                    'karir'       => 3,
                    'keterangan'  => 'Failed',
                    'table'       => 'attendance_logs',
                    'created_at'  => now(),
                ]);
                continue;
            }

            if (!$response->successful()) {
                $this->error(" {$ip}: HTTP error " . $response->status());
                ActivityLog::create([
                    'user_id'     => 1,
                    'user_name'   => 'System HRD-APPS',
                    'activity'    => 'finger:sync',
                    'description' => " {$ip}: HTTP error " . $response->status(),
                    'karir'       => 3,
                    'keterangan'  => 'Error',
                    'table'       => 'attendance_logs',
                    'created_at'  => now(),
                ]);

                continue;
            }

            $logs = $response->json();

            if (empty($logs)) {
                $this->line(" {$ip}: tidak ada log.");
                ActivityLog::create([
                    'user_id'     => 1,
                    'user_name'   => 'System HRD-APPS',
                    'activity'    => 'finger:sync',
                    'description' => " {$ip}: tidak ada log.",
                    'karir'       => 3,
                    'keterangan'  => 'Not Found',
                    'table'       => 'attendance_logs',
                    'created_at'  => now(),
                ]);

                continue;
            }

            $insertData = [];

            foreach ($logs as $log) {
                if (!empty($log['enrollNumber']) && !empty($log['dateTime'])) {
                    $insertData[] = [
                        'MachineIp'    => $ip,
                        'EnrollNumber' => $log['enrollNumber'],
                        'LogTime'      => $log['dateTime'],
                        'VerifyMode'   => $log['verifyMode'] ?? null,
                        'InOutMode'    => $log['inOutMode'] ?? null,
                        'WorkCode'     => $log['workCode'] ?? null,
                    ];
                }
            }

            if (!empty($insertData)) {
                foreach (array_chunk($insertData, 1000) as $chunk) {
                    DB::table('attendance_logs')->upsert(
                        $chunk,
                        ['MachineIp', 'EnrollNumber', 'LogTime']
                    );
                }

                $this->info(" {$ip}: berhasil sync " . count($insertData) . " log.");
                ActivityLog::create([
                    'user_id'     => 1,
                    'user_name'   => 'System HRD-APPS',
                    'activity'    => 'finger:sync',
                    'description' => " Finger {$name} IP:{$ip}: berhasil sync " . count($insertData) . " log.",
                    'karir'       => 3,
                    'keterangan'  => 'Success',
                    'table'       => 'attendance_logs',
                    'created_at'  => now(),
                ]);
            } else {
                $this->line(" {$ip}: tidak ada data valid.");
                ActivityLog::create([
                    'user_id'     => 1,
                    'user_name'   => 'System HRD-APPS',
                    'activity'    => 'finger:sync',
                    'description' => " {$ip}: tidak ada data valid.",
                    'karir'       => 3,
                    'keterangan'  => 'Not Found',
                    'table'       => 'attendance_logs',
                    'created_at'  => now(),
                ]);
            }
        }

        $this->info(' Selesai! Semua mesin diproses.');
        ActivityLog::create([
            'user_id'     => 1,
            'user_name'   => 'System HRD-APPS',
            'activity'    => 'finger:sync',
            'description' => " Selesai! Semua mesin diproses.",
            'karir'       => 3,
            'keterangan'  => 'Success',
            'table'       => 'attendance_logs',
            'created_at'  => now(),
        ]);
        return Command::SUCCESS;
    }
}
