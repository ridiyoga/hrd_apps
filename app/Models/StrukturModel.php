<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StrukturModel extends Model
{
    use HasFactory;
    protected $table = 'struktur_organisasi';
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'jabatan',
        'parent',
        'kuota_karyawan',
        'holding',
        'created_at',
        'updated_at'
    ];
    public function Jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan', 'id');
    }

    public function Children(): HasMany
    {
        return $this->hasMany(StrukturModel::class, 'parent', 'id');
    }

    public function ChildrenRecursive(): HasMany
    {
        return $this->Children()->with('ChildrenRecursive', 'UserKaryawan');
    }

    public function Parent(): BelongsTo
    {
        return $this->belongsTo(StrukturModel::class, 'parent', 'id');
    }
    public function ParentJabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'parent', 'id');
    }
    public function UserKaryawan(): HasMany
    {
        return $this->hasMany(KaryawanJabatan::class, 'jabatan_id', 'jabatan')->where('is_active', 1);
    }
}
