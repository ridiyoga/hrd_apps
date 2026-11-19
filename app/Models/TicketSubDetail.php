<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketSubDetail extends Model
{
    use SoftDeletes;

    protected $table = 'ticket_sub_detail';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ticket_detail_id',
        'line_number',
        'alasan_ubah',
        'before_ubah',
        'after_ubah',
        'created_at',
        'updated_at',
        'deleted_at',

        // Status dan informasi pembatalan baris
        'is_cancelled',
        'cancel_reason',
        'cancelled_by_role',
        'cancelled_by_id',
        'cancelled_by_name',
        'cancelled_at',
    ];

    protected $dates = ['deleted_at'];
    protected function casts(): array
    {
        return [
            'is_cancelled' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public function ticketDetail()
    {
        return $this->belongsTo(TicketDetail::class, 'ticket_detail_id');
    }
}
