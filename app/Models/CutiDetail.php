<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CutiDetail extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'cuti_detail';
    // protected $guarded = ['id'];
    public $incrementing = false;
    protected $fillable = [
        'id',
        'id_cuti',
        'id_karyawan',
        'nama_karyawan',
        'tgl_cuti',
        'status',
        'created_at',
        'updated_at',
        'tipe',
    ];
    public function Karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class, 'id', 'id_karyawan');
    }
    public function Cuti(): HasOne
    {
        return $this->hasOne(Cuti::class, 'id', 'id_cuti');
    }
}
