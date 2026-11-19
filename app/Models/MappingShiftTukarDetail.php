<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingShiftTukarDetail extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'mapping_shift_tukar_detail';
    protected $fillable = [
        'id',
        'id_tukar_utama',
        'no_form',
        'karyawan_penukar_id',
        'karyawan_penukar_name',
        'nama_shift_penukar',
        'shift_id_penukar',
        'karyawan_tertukar_id',
        'karyawan_tertukar_name',
        'nama_shift_tertukar',
        'shift_id_tertukar',
        'created_at',
        'updated_at',
    ];
    public function MappingShiftPenukar(): BelongsTo
    {
        return $this->belongsTo(MappingShift::class, 'shift_id_penukar', 'id');
    }
    public function MappingShiftTertukar(): BelongsTo
    {
        return $this->belongsTo(MappingShift::class, 'shift_id_tertukar', 'id');
    }
}
