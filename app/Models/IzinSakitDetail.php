<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Models\IzinSakit;
use App\Models\Karyawan;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class IzinSakitDetail extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'izin_sakit_detail';
    // protected $guarded = ['id'];
    public $incrementing = false;
    protected $fillable = [
        'id',
        'id_sakit',
        'id_karyawan',
        'nama_karyawan',
        'status',
        'tgl_sakit',
        'created_at',
        'updated_at',
    ];
    public function Karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }
    public function IzinSakit(): HasOne
    {
        return $this->hasOne(IzinSakit::class, 'id', 'id_sakit');
    }
}
