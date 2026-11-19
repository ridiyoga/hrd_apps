<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingShiftHead extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'mapping_shift_head';
    protected $fillable = [
        'id',
        'judul',
        'legal_number_mapping',
        'input_by',
        'input_by_name',
        'input_by_signature',
        'start_date',
        'end_date',
        'approved',
        'approved_by',
        'approved_at',
        'approved_signature',
        'approved_by_name',
        'created_at',
        'updated_at',
    ];
    public function KaryawanPengaju(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'input_by', 'id');
    }
    public function KaryawanPenyetuju(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'approved_by', 'id');
    }
}
