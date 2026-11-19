<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'inventaris';
    protected $fillable = [
        'id',
        'nama_inventaris',

        // 'kode',
        'serial_number',
        'id_aset',

        'id_kategori',
        'nama_kategori',

        'id_site',
        'nama_site',

        'id_holding',
        'nama_holding',

        'id_gedung',
        'nama_gedung',

        'id_ruangan',
        'nama_ruangan',

        'status',
        'kondisi',
        'nama_kondisi',

        'foto',
        'keterangan',

        'id_karyawan',
        'nama_karyawan',
        'nik_karyawan',

        'created_at',
        'updated_at'
    ];
}