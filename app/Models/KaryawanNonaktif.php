<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaryawanNonaktif extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'karyawan_nonaktif';
    protected $fillable = [
        'id',
        'id_karyawan',
        'nama_karyawan',
        'deskripsi',
        'tanggal_join',
        'tanggal_nonaktif',
        'status',
        'created_by',
        'created_by_name',
        'created_at',
        'updated_at',
    ];
}
