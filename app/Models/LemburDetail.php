<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class LemburDetail extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'lembur_detail';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'id_lembur',
        'id_karyawan',
        'nama_karyawan',
        'id_mapping_shift',
        'nama_shift',
        'tgl_lembur',
        'tgl_pulang',
        'awal',
        'jam_mulai',
        'jam_selesai',
        'presensi_masuk',
        'presensi_pulang',
        'selisih_convert',
        'updated',
        'updated_by_id',
        'updated_by_name',
        'original',
        'status',
        'created_at',
        'updated_at',
    ];
    public function User()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }
    public function Lembur()
    {
        return $this->belongsTo(Lembur::class, 'id_lembur', 'id');
    }
    public function MappingShift()
    {
        return $this->belongsTo(MappingShift::class, 'id_mapping_shift', 'id');
    }
    public function Absensi()
    {
        return $this->hasMany(AttendanceLog::class, 'EnrollNumber', 'nomor_identitas_karyawan');
    }
    public function Divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
    public function Bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class);
    }

    public function Departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }
    public function Atasan1(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_user_atasan', 'id');
    }
    public function Atasan2(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_user_atasan2', 'id');
    }
    public function AtasanHRD(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_user_hrd', 'id');
    }
    public function KaryawanPengganti(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pengganti_id', 'id');
    }
    public function IzinTidakMasukDetail(): HasMany
    {
        return $this->HasMany(IzinTidakMasukDetail::class, 'id_tidak_masuk', 'id');
    }
}
