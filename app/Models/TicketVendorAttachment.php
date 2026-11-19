<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketVendorAttachment extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $table = 'ticket_vendor_attachment';

    protected $fillable = [
        'ticket_vendor_head_id',

        'document_type',

        'original_filename',
        'stored_filename',

        'file_path',
        'mime_type',
        'file_size',

        'uploaded_by',
    ];


    public function header()
    {
        return $this->belongsTo(
            TicketVendorHead::class,
            'ticket_vendor_head_id'
        );
    }
}
