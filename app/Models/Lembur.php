<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Lembur extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'lembur';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'karyawan_id',
        'karyawan_nik',
        'nama_karyawan',
        'ttd_user',

        'no_form_lembur',
        'tanggal_lembur',

        'status_lembur',
        'keterangan_lembur',
        'awal',

        'status_approve_atasan1',
        'id_user_atasan',
        'nama_atasan',
        'ttd_atasan',
        'waktu_approve',

        'status_approve_atasan2',
        'id_user_atasan2',
        'nama_atasan2',
        'ttd_atasan2',
        'waktu_approve2',

        'status_approve_hrd',
        'id_user_hrd',
        'nama_hrd',
        'ttd_hrd',
        'waktu_approve_hrd',

        'created_at',
        'updated_at',
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
    public function LemburDetail(): HasMany
    {
        return $this->HasMany(LemburDetail::class, 'id_lembur', 'id');
    }
}
