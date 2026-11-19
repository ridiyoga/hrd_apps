<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionKaryawan extends Model
{
    use HasFactory, UuidTrait;
    protected $keyType = 'string';
    protected $table = 'posisi_karyawans';
    public $incrementing = false;
    protected $primaryKey = 'id_posisi';
    public $timestamps = false;
    protected $fillable = [
        'id_posisi',
        'kategori',
        'curent_id_posisi',
        'new_id_posisi',
        'user_pengajuan',
        'ttd_pengajuan',
        'created_at_pengajuan',
        'user_disetujui',
        'ttd_disetujui',
        'created_at_disetujui',
        'user_diketahui',
        'ttd_diketahui',
        'created_at_diketahui',
        'user_dituju',
        'ttd_dituju',
        'created_at_dituju',
        'status'
    ];
}
