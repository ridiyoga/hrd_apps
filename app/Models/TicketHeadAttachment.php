<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHeadAttachment extends Model
{
    protected $table = 'ticket_head_attachment';
    protected $guarded = [];
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable= [
        'id',
        'ticket_head_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
        'uploaded_by_name',
        'deleted_at',
    ];

    public function ticketHead()
    {
        return $this->belongsTo(TicketHead::class, 'ticket_head_id', 'id');
    }
}
