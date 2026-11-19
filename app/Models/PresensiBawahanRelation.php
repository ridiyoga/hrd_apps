<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiBawahanRelation extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'presensi_bawahan_relations';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'holding_id',
        'atasan_id',
        'bawahan_id',
        'created_by',
    ];

    public function Atasan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'atasan_id', 'id');
    }

    public function Bawahan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'bawahan_id', 'id');
    }
}
