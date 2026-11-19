<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisKondisi extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'inventaris_kondisi';
    protected $fillable = [
        'id',
        'nama_kondisi',
        'deskripsi',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
