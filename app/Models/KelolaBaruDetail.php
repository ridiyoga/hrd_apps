<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KelolaBaruDetail extends Model
{
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $table = 'attendance_r_detail';
    protected $fillable = [
        'id',
        'id_r',
        'id_karyawan',
        'nama_karyawan',
        'tanggal_r',
        'baru',
        'created_by',
        'created_by_name',
        'created_at',
        'updated_at',
    ];
    public function KelolaBaru(): HasOne
    {
        return $this->hasOne(KelolaBaru::class, 'id', 'id_r');
    }
}
