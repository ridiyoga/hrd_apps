<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisPerjalanan extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'inventaris_perjalanan_master';
    protected $fillable = [
        'id',
        'nama_perjalanan',
        'deskripsi',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
