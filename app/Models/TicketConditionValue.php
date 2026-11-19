<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketConditionValue extends Model
{
    protected $table = 'ticket_hasil_cek';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ticket_id',
        'condition_key',
        'condition_value',
        'checked_by',
        'checked_at',
        'dependency_rule_id',
        'direction',
        'actor_type',
    ];

    protected $casts = [
        'condition_value' => 'boolean',
        'checked_at'      => 'datetime',
        'dependency_rule_id' => 'string',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
