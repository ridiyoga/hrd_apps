<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketDetail extends Model
{
    use softDeletes;

    protected $table = 'ticket_detail';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'ticket_id',
        'line_detail',
        'nomor_detail',
        'tanggal_transaksi',
        'alasan_ubah',
        'before_ubah',
        'after_ubah',
        'reference_ticket_detail_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $dates = ['deleted_at'];

    public function ticket()
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id');
    }
    public function subDetails()
    {
        return $this->hasMany(TicketSubDetail::class, 'ticket_detail_id')->orderBy('line_number');
    }

    public function referenceTicketDetail()
    {
        return $this->belongsTo(TicketDetail::class, 'reference_ticket_detail_id');
    }

    public function referencedByDetails()
    {
        return $this->hasMany(TicketDetail::class, 'reference_ticket_detail_id');
    }
}
