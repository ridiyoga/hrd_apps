<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketKondisiMenu extends Model
{
    protected $table = 'ticket_kondisi';
    protected $guarded = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable= [
        'id',
        'code',
        'label_kondisi',
        'tipe_data',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function ticketMenu()
    {
        return $this->belongsTo(TicketMenu::class);
    }

    public function ticketHead()
    {
        return $this->belongsToMany(TicketHead::class, 'ticket_head_kondisi', 'kondisi_id', 'ticket_id');
    }
}
