<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketApprovalKaryawan extends Model
{
    protected $table = 'ticket_approval_karyawan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'approval_step_id',
        'karyawan_id',
        'karyawan_name',
    ];

    /**
     * Relationship: belongs to TicketApprovalStep.
     */
    public function step()
    {
        return $this->belongsTo(TicketApprovalStep::class, 'approval_step_id');
    }

    /**
     * Relationship: belongs to Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
