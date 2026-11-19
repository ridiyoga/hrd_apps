<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Yajra\DataTables\Html\Editor\Fields\BelongsTo;

class KuotaCutiKaryawan extends Model
{
    protected $table = 'cuti_kuota_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'karyawan_id',
        'karyawan_nik',
        'periode_id',
        'kuota',
        'terpakai',
        'sisa',
        'status',
        'created_at',
        'updated_at'
    ];

    public function PeriodeGroup()
    {
        return $this->belongsTo(CutiPeriod::class, 'periode_id', 'id_period');
    }
    public function Karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class, 'id', 'karyawan_id');
    }
}
