<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cuti extends Model
{
    use HasFactory, UuidTrait;
    // protected $guarded = ['id'];
    public $incrementing = false;
    protected $fillable = [
        'id',
        'karyawan_id',
        'no_form_cuti',
        'kategori_cuti_id',
        'nama_karyawan',
        'ttd_user',
        'kategori_cuti_id',
        'nama_cuti',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan_cuti',
        'status_cuti',
        'karyawan_pengganti_id',
        'nama_karyawan_pengganti',
        'status_approve_atasan1',
        'id_user_atasan',
        'nama_atasan',
        'ttd_atasan',
        'waktu_approve',
        'status_approve_atasan2',
        'id_user_atasan2',
        'nama_atasan2',
        'ttd_atasan2',
        'jumlah_hari',
        'waktu_approve2',
        'created_at',
        'updated_at',
        'kuota',
        'terpakai',
        'sisa',
        'toleransi',
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
    public function KategoriCuti(): HasOne
    {
        return $this->HasOne(KategoriCuti::class, 'id', 'kategori_cuti_id');
    }
    public function Atasan1(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_user_atasan', 'id');
    }
    public function Atasan2(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_user_atasan2', 'id');
    }
    public function KaryawanPengganti(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pengganti_id', 'id');
    }
    public function CutiKuota(): HasOne
    {
        return $this->hasOne(KuotaCutiKaryawan::class, 'id', 'id_kuota_karyawan');
    }
    public function CutiDetail(): HasMany
    {
        return $this->hasMany(CutiDetail::class, 'id_cuti', 'id');
    }
}
