<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

Broadcast::channel('canvas.workflow', function ($user) {
    $namaKaryawan = null;
    if (!empty($user->karyawan_id)) {
        $namaKaryawan = DB::table('karyawans')
            ->where('nomor_identitas_karyawan', $user->karyawan_id)
            ->orWhere('nik', $user->karyawan_id)
            ->orWhere('id', $user->karyawan_id)
            ->value('name');
    }

    $displayName = $namaKaryawan ?: ($user->name ?: ($user->username ?: 'User'));

    return [
        'id'    => $user->id,
        'name'  => $displayName,
        'color' => '#' . substr(md5($user->id ?? rand()), 0, 6),
    ];
});
