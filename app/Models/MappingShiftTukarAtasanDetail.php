<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MappingShiftTukarAtasanDetail extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $table = 'mapping_shift_tukar_atasan_detail';
    protected $fillable = [
        'id_mapping',
        'id_tukar_atasan_utama',
        'legal_number_mapping',
        'karyawan_id',
        'nik_karyawan',
        'nama_karyawan',
        'shift_id',
        'nama_shift',
        'input_by',
        'pengajuan_signature',
        'approved_by',
        'approved_at',
        'approved_signature',
        'status_approved',
        'tanggal_masuk',
        'tanggal_pulang',
        'mapping_group',
        'is_active',
        'id_tukar_shift_detail',
        'id_head',
        'created_at',
        'updated_at',
    ];

    public function Original(): BelongsTo
    {
        return $this->belongsTo(MappingShift::class, 'id_mapping', 'id');
    }

    public function KaryawanJabatanOne(): HasOne
    {
        return $this->hasOne(KaryawanJabatan::class, 'karyawan_id', 'id')->where('main', 1);
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
    public function Koordinator()
    {
        return $this->belongsTo(Karyawan::class, 'koordinator_id', 'id');
    }

    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
    public function Shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function AttendanceLog()
    {
        return $this->hasMany(AttendanceLog::class, 'EnrollNumber', 'nomor_identitas_karyawan');
    }
    public function Izin()
    {
        return $this->belongsTo(Izin::class, 'izin_id', 'id');
    }
    public function Cuti()
    {
        return $this->belongsTo(Cuti::class, 'cuti_id', 'id');
    }
    public function KaryawanInput(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'input_by', 'id');
    }
    public function KaryawanApproved(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'approved_by', 'id');
    }
}
