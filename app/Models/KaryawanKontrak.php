<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaryawanKontrak extends Model
{
    use HasFactory, UuidTrait;
    protected $keyType = 'string';
    protected $table = 'karyawan_kontrak';
    public $incrementing = false;
    protected $primaryKey = 'id_kontrak';
    public $timestamps = false;
    protected $fillable = [
        'id_kontrak',
        'karyawan_id',
        'file_kontrak',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_kontrak',
        'parent_id',
        'status_kontak',
        'created_at',
        'updated_at',
    ];
    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(KaryawanKontrak::class, 'parent_id')
            ->orderBy('tanggal_mulai', 'asc')
            ->with('children'); // 🔥 INI KUNCINYA
    }

    public function parent()
    {
        return $this->belongsTo(KaryawanKontrak::class, 'parent_id');
    }
}
