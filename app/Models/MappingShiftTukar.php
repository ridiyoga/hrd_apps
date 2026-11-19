<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MappingShiftTukar extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'mapping_shift_tukar';
    protected $fillable = [
        'id',
        'id_pengaju',
        'nama_pengaju',
        'sign_pengaju',
        'no_form',
        'approval1',
        'id_approval1',
        'nama_approval1',
        'sign_approval1',
        'approval2',
        'id_approval2',
        'nama_approval2',
        'sign_approval2',
        'alasan',
        'created_at',
        'updated_at'
    ];
}
