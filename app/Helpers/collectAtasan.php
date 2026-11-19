<?php

use Illuminate\Support\Collection;

function collectAtasan($struktur): Collection
{
    $ids = collect();

    if ($struktur instanceof \Illuminate\Database\Eloquent\Collection) {
        return $struktur
            ->flatMap(fn($item) => collectAtasan($item))
            ->unique()
            ->values();
    }

    $current = $struktur->Parent;

    while ($current) {

        // 🔥 FILTER LEVEL JABATAN
        if (
            $current->Jabatan &&
            $current->Jabatan->LevelJabatan &&
            $current->Jabatan->LevelJabatan->level_jabatan > 3
        ) {
            if ($current->UserKaryawan->isNotEmpty()) {
                $ids = $ids->merge(
                    $current->UserKaryawan->pluck('karyawan_id')
                );
            }
        }

        $current = $current->Parent;
    }

    return $ids->unique()->values();
}
