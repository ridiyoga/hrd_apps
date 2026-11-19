<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisGedung extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'inventaris_gedung';
    protected $fillable = [
        'id',
        'nama_gedung',
        'deskripsi',

        'id_site',
        'nama_site',

        'id_holding',
        'nama_holding',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
