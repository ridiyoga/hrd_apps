<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingShiftTukarAtasan extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'mapping_shift_tukar_atasan';
    protected $fillable = [
        'id',
        'alasan',
        'no_form',
        'input_by',
        'input_by_name',
        'input_by_signature',
        'approved',
        'approved_by',
        'approved_at',
        'approved_signature',
        'approved_by_name',
        'created_at',
        'updated_at',
        'id_head',
    ];
    public function Pengaju(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'input_by', 'id');
    }
}
