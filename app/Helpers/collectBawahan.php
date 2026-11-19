<?php

use Illuminate\Support\Collection;

function collectBawahan($struktur): Collection
{
    if ($struktur instanceof \App\Models\StrukturModel) {
        $struktur = collect([$struktur]);
    }
    $ids = collect();

    foreach ($struktur as $node) {

        // ambil karyawan di jabatan ini
        if ($node->UserKaryawan->isNotEmpty()) {
            $ids = $ids->merge(
                $node->UserKaryawan->pluck('karyawan_id')
            );
        }

        // recursive ke bawah
        if ($node->ChildrenRecursive->isNotEmpty()) {
            $ids = $ids->merge(
                collectBawahan($node->ChildrenRecursive)
            );
        }
    }

    return $ids->unique()->values();
}
