<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenLegalReminderLog extends Model
{
    protected $table = 'dokumen_legal_reminder_logs';

    protected $fillable = [
        'dokumen_id',
        'recipient_user_id',
        'recipient_email',
        'reminder_type',
        'target_date',
        'sent_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(DokumenLegal::class, 'dokumen_id', 'id');
    }
}
