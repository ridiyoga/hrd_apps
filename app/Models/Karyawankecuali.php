<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawankecuali extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'attendance_kecuali';
    protected $fillable = [
        'id',
        'id_karyawan',
        'nama_karyawan',
        'deskripsi',

        'created_by',
        'created_by_name',

        'created_at',
        'updated_at'
    ];
}
