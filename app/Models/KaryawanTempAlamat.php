<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;
use maliklibs\Zkteco\Lib\Helper\Attendance;

class KaryawanTempAlamat extends Model
{
    use HasFactory, UuidTrait;
    protected $keyType = 'string';
    protected $table = 'karyawan_temp_alamat';

    // protected $primaryKey = null;
    protected $primaryKey = 'id';
    public $incrementing = false;
    // protected $casts = [
    //     'tgl_terbit_kk' => 'date',
    //     'tgl_lahir' => 'date',
    // ];
    protected $fillable = [
        'id',
        'id_karyawan',
        'provinsikabupaten',
        'kabupaten',
        'kecamatan',
        'desa',
        'rt',
        'rw',
        'kode_pos',
        'detail_alamat',
        'alamat',
        'status_alamat',
        'provinsi_domisili',
        'kabupaten_domisili',
        'kecamatan_domisili',
        'desa_domisili',
        'rt_domisili',
        'rw_domisili',
        'kode_pos_domisili',
        'detail_alamat_domisili',
        'alamat_domisili',
        'alasan',
        'status',
        'approval',
        'approval_name',
        'holding',
        'created_at',
        'updated_at',
    ];
    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id');
    }
    public function Provinsi(): BelongsTo
    {
        return $this->belongsTo(Provincies::class, 'provinsi', 'code');
    }
    public function Kabupaten(): BelongsTo
    {
        return $this->belongsTo(Cities::class, 'kabupaten', 'code');
    }
    public function Kecamatan(): BelongsTo
    {
        return $this->belongsTo(District::class, 'kecamatan', 'code');
    }
    public function Desa(): BelongsTo
    {
        return $this->belongsTo(Village::class,  'desa', 'code');
    }
    // Alamat User sekarang
    public function provinsiNOW(): BelongsTo
    {
        return $this->belongsTo(Provincies::class, 'provinsi_domisili', 'code');
    }
    public function kabupatenNOW(): BelongsTo
    {
        return $this->belongsTo(Cities::class, 'kabupaten_domisili', 'code');
    }
    public function kecamatanNOW(): BelongsTo
    {
        return $this->belongsTo(District::class, 'kecamatan_domisili', 'code');
    }
    public function desaNOW(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'desa_domisili', 'code');
    }
}
