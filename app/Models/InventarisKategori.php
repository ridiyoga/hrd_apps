<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisKategori extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'inventaris_kategori';
    protected $fillable = [
        'id',
        'nama_kategori',
        'deskripsi',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
