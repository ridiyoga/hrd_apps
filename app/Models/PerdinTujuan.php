<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerdinTujuan extends Model
{
    protected $table = 'perdin_tujuan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'id_perdin',
        'id_karyawan',
        'nama_karyawan',
        'site',
        'id_site',
        'nama_site',
        'created_at',
        'updated_at',
    ];
}
