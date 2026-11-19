<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketApprovalComment extends Model
{
    protected $table = 'ticket_approval_comment';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ticket_id',
        'approval_level',
        'karyawan_id',
        'jabatan_id',
        'action_ticket',
        'comment_text',
        'comment_at',
        'updated_at',
        'created_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(KaryawanJabatan::class, 'jabatan_id');
    }
}
