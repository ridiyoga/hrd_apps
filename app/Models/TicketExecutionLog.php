<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketExecutionLog extends Model
{
    protected $table = 'ticket_execution_log';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'status_before',
        'status_after',
        'action',
        'notes',
        'done_by',
    ];
 
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id');
    }
 
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'done_by');
    }
}
