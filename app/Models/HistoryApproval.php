<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryApproval extends Model
{
    protected $table = 'history_approval';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_approval',
        'karyawan_id',
        'karyawan_id_pengajuan',
        'status_approval',
        'tipe_approval',
        'content_id',
        'created_at',
        'updated_at'
    ];

    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
    public function KaryawanPengajuan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id_pengajuan', 'id');
    }
    public function Cuti(): BelongsTo
    {
        return $this->belongsTo(Cuti::class, 'content_id', 'id');
    }
    public function Izin(): BelongsTo
    {
        return $this->belongsTo(Izin::class, 'content_id', 'id');
    }
    public function Perdin(): BelongsTo
    {
        return $this->belongsTo(PerjalananDinas::class, 'content_id', 'id');
    }
}
