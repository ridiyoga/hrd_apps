<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoInternal extends Model
{
    protected $table = 'memo_internal';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'nomor_memo',
        'perihal',
        'isi',
        'penerima',
        'holding',
        'tanggal_memo',
        'status',
        'penandatangan',
        'jabatan_penandatangan',
        'footer',
        'lampiran',
        'is_holiday',
        'tanggal_mulai_libur',
        'tanggal_selesai_libur',
        'ttd_penandatangan',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'holding' => 'array'
    ];
    public function KaryawanPembuat()
    {
        return $this->belongsTo(Karyawan::class, 'created_by', 'id');
    }
    public function KaryawanPenandatangan()
    {
        return $this->belongsTo(Karyawan::class, 'penandatangan', 'id');
    }
}
