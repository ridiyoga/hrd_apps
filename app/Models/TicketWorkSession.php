<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TicketWorkSession extends Model
{
    protected $table = 'ticket_work_sessions';
    public $incrementing = false;
    protected $keyType = 'string';

    const SESSION_AWAL   = 'awal';
    const SESSION_REVISI = 'revisi';

    protected $fillable = [
        'id',
        'ticket_type',
        'ticket_id',
        'pic_id',
        'cycle_number',
        'session_type',
        'started_at',
        'ended_at',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'id'               => 'string',
        'cycle_number'     => 'integer',
        'duration_minutes' => 'float',
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'pic_id');
    }
}
