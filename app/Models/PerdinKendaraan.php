<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerdinKendaraan extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'perdin_kendaraan_umum';
    protected $fillable = [
        'id',
        'nama_kendaraan',
        'deskripsi',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
