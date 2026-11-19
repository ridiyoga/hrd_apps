<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelolaBaru extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'attendance_r';
    protected $fillable = [
        'id',
        'id_karyawan',
        'nama_karyawan',
        'deskripsi',
        'tanggal_join',
        'tanggal_mulai',
        'tanggal_selesai',
        'baru',
        'created_by',
        'created_by_name',
        'created_at',
        'updated_at',
    ];
}
