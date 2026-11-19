<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Models\IzinTidakMasuk;
use App\Models\Karyawan;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class PerdinDetail extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'perdin_detail';
    // protected $guarded = ['id'];
    public $incrementing = false;
    protected $fillable = [
        'id',
        'id_perdin',
        'id_karyawan',
        'nama_karyawan',
        'status',
        'tgl_perdin',
        'created_at',
        'updated_at',
    ];
    public function Karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }
    public function Perdin(): HasOne
    {
        return $this->hasOne(Perdin::class, 'id', 'id_perdin');
    }
}
