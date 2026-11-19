<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PresensiBawahanRelationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $relations = [];
        $employeeIdsByJabatan = DB::table('karyawan_jabatan as kj')
            ->join('karyawans as k', 'k.id', '=', 'kj.karyawan_id')
            ->where('kj.is_active', 1)
            ->where('k.status_aktif', 'AKTIF')
            ->select('kj.jabatan_id', 'kj.holding_id', 'kj.karyawan_id')
            ->distinct()
            ->get()
            ->groupBy(fn($row) => $row->holding_id . ':' . $row->jabatan_id)
            ->map(fn($rows) => $rows->pluck('karyawan_id')->values());

        $nodes = DB::table('struktur_organisasi')
            ->orderBy('holding')
            ->orderBy('id')
            ->get(['id', 'jabatan', 'parent', 'holding']);
        $employeeIdsByNode = $nodes->mapWithKeys(function ($node) use ($employeeIdsByJabatan) {
            return [
                $node->id => $employeeIdsByJabatan->get($node->holding . ':' . $node->jabatan, collect()),
            ];
        });

        $nodes
            ->whereNotNull('parent')
            ->each(function ($node) use (&$relations, $employeeIdsByNode, $now) {
                $atasanIds = $employeeIdsByNode->get($node->parent, collect());
                $bawahanIds = $employeeIdsByNode->get($node->id, collect());

                foreach ($atasanIds as $atasanId) {
                    foreach ($bawahanIds as $bawahanId) {
                        if ((string) $atasanId === (string) $bawahanId) {
                            continue;
                        }

                        $relations[] = [
                            'id' => (string) Str::uuid(),
                            'holding_id' => $node->holding,
                            'atasan_id' => $atasanId,
                            'bawahan_id' => $bawahanId,
                            'created_by' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            });

        if ($relations === []) {
            $this->command?->warn('Tidak ada relasi bawahan langsung yang dapat dibuat dari struktur organisasi.');
            return;
        }

        foreach (array_chunk($relations, 500) as $chunk) {
            DB::table('presensi_bawahan_relations')->insertOrIgnore($chunk);
        }

        $this->command?->info(count($relations) . ' relasi bawahan langsung berhasil diproses.');
    }
}
