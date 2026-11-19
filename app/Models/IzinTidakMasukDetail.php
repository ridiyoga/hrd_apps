<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Models\IzinTidakMasuk;
use App\Models\Karyawan;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class IzinTidakMasukDetail extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'izin_tidak_masuk_detail';
    // protected $guarded = ['id'];
    public $incrementing = false;
    protected $fillable = [
        'id',
        'id_tidak_masuk',
        'id_karyawan',
        'nama_karyawan',
        'status',
        'tgl_tidak_masuk',
        'created_at',
        'updated_at',
    ];
    public function Karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }
    public function IzinTidakMasuk(): HasOne
    {
        return $this->hasOne(IzinTidakMasuk::class, 'id', 'id_tidak_masuk');
    }
}
