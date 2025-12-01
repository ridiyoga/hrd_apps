<?php

namespace App\Console\Commands;

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
            return Command::SUCCESS;
        }

        $this->info(' Mulai sync dari ' . $machines->count() . ' mesin...');

        // RANGE WAKTU
        $now = now();
        $dateFrom = $now->copy()->subDay(2)->toIso8601String();
        $dateTo   = $now->toIso8601String();

        foreach ($machines as $machine) {

            $ip = $machine->Ip;
            $port = $machine->Port;

            $this->line(" Proses mesin {$ip}:{$port} ...");

            try {

                // ⚠️ JALANKAN SEQUENTIAL — TANPA POOL
                $response = Http::timeout(3600)->get('http://localhost:5000/api/ZK/logs', [
                    'ip'       => $ip,
                    'port'     => $port,
                    'dateFrom' => $dateFrom,
                    'dateTo'   => $dateTo,
                ]);
            } catch (\Exception $e) {

                $this->error(" {$ip}: gagal connect ({$e->getMessage()})");
                continue;
            }

            if (!$response->successful()) {
                $this->error(" {$ip}: HTTP error " . $response->status());
                continue;
            }

            $logs = $response->json();

            if (empty($logs)) {
                $this->line(" {$ip}: tidak ada log.");
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
            } else {
                $this->line(" {$ip}: tidak ada data valid.");
            }
        }

        $this->info(' Selesai! Semua mesin diproses.');
        return Command::SUCCESS;
    }
}
