<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerdinKendaraanKantor extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'perdin_kendaraan_kantor';
    protected $fillable = [
        'id',
        'id_kategori',
        'nama_kategori',
        'deskripsi',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
