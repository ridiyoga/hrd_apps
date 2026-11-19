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

class KaryawanTemp extends Model
{
    use HasFactory, UuidTrait;
    protected $keyType = 'string';
    protected $table = 'karyawan_temp';

    // protected $primaryKey = null;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $casts = [
        'tgl_terbit_kk' => 'date',
        'tgl_lahir' => 'date',
    ];
    protected $fillable = [
        'id',
        'id_karyawan',
        'nik',
        'ktp',
        'name',
        'tempat_lahir',
        'tgl_lahir',
        'agama',
        'gender',
        'jumlah_anak',
        'status_nikah',
        'no_kk',
        'tgl_terbit_kk',
        'kk',
        'email',
        'telepon',
        'status_nomor',
        'nomor_wa',
        'alasan',
        'approval',
        'approval_name',
        'status',
        'created_at',
        'updated_at',
        'holding',
    ];
}
