<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class PresensiFoto extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'presensi_foto';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'karyawan_id',
        'karyawan_nik',
        'nama_karyawan',
        'ttd_user',
        'no_form_presensi',


        'keterangan_presensi',
        'lokasi_presensi',

        'jam_presensi',

        'foto_presensi',
        'status_presensi',

        'status_approve_hrd',
        'id_user_hrd',
        'nama_hrd',
        'ttd_hrd',
        'waktu_approve_hrd',

        'created_at',
        'updated_at'
    ];
    public function User()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
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
    public function AtasanHRD(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_user_hrd', 'id');
    }
}
