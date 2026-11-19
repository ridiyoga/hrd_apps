<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class JabatanMaster extends Model
{
    protected $table = 'jabatan_master';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'jabatan_name',
        'level_id',
        'created_at',
        'updated_at',
    ];
    public function LevelJabatan(): BelongsTo
    {
        return $this->belongsTo(LevelJabatan::class, 'level_id', 'id');
    }
}
