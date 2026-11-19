<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KaryawanJabatan extends Model
{
    use HasFactory, UuidTrait;
    protected $keyType = 'string';
    protected $table = 'karyawan_jabatan';
    public $incrementing = false;
    protected $primaryKey = 'id_jbt_karyawan';
    public $timestamps = false;
    protected $fillable = [
        'id_jbt_karyawan',
        'holding_id',
        'departemen_id',
        'divisi_id',
        'bagian_id',
        'jabatan_id',
        'karyawan_id',
        'is_active',
        'main',
        'created_at',
        'updated_at',
    ];

    public function Holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class, 'holding_id', 'id');
    }
    public function Jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }
    public function Departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id');
    }
    public function Divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id');
    }
    public function Bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class, 'bagian_id', 'id');
    }
    public function PenempatanHolding(): BelongsTo
    {
        return $this->belongsTo(Holding::class, 'holding_id', 'id');
    }
    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id')->where('status_aktif', 'AKTIF');
    }
}
