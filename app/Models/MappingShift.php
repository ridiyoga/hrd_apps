<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingShift extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $table = 'mapping_shifts';
    protected $fillable = [
        'karyawan_id',
        'input_by',
        'legal_number_mapping',
        'nik_karyawan',
        'nama_karyawan',
        'shift_id',
        'nama_shift',
        'approved_by',
        'status_approved',
        'approved_at',
        'tanggal_masuk',
        'jam_absen',
        'telat',
        'lat_absen',
        'long_absen',
        'jarak_masuk',
        'lokasi_absen',
        'foto_jam_absen',
        'keterangan_absensi',
        'tanggal_pulang',
        'jam_pulang',
        'pulang_cepat',
        'lembur',
        'foto_jam_pulang',
        'lat_pulang',
        'long_pulang',
        'jarak_pulang',
        'lokasi_absen_pulang',
        'keterangan_absensi_pulang',
        'total_jam_kerja',
        'status_absen',
        'kelengkapan_absensi',
        'izin_id',
        'cuti_id',
        'approved_signature',
        'pengajuan_signature',
        'penugasaan_id',
        'keterangan_izin',
        'keterangan_cuti',
        'keterangan_dinas',
        'mapping_group',
        'is_active',
        'id_tukar_shift_detail',
        'id_head',
        'created_at',
        'updated_at'
    ];

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
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
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
