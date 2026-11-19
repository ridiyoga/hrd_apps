<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketVendorHead extends Model
{
    protected $table = 'ticket_vendor_head';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nomor_pengajuan_fis',
        'creator_name',
        'holding_id',
        'system_type', //1 epicor, 2 accurate
        'vendor_category', // V01 Bahan baku, V02 Non Bahan Baku
        'status',
        'current_step',
        'created_by',
        'updated_by',
        'vd_approve1',
        'vd_approve1_name',
        'vd_approve1_at',
        'vd_approve1_sign',
        'vd_approve2',
        'vd_approve2_name',
        'vd_approve2_at',
        'vd_approve2_sign',
        'vd_checker',
        'vd_checker_name',
        'vd_checker_at',
        'vd_checker_sign',
        'vd_validator',
        'vd_validator_name',
        'vd_validator_at',
        'vd_validator_sign',
        'creator_sign',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function data()
    {
        return $this->hasOne(
            TicketVendorDetail::class,
            'ticket_vendor_head_id'
        );
    }

    public function attachments()
    {
        return $this->hasMany(
            TicketVendorAttachment::class,
            'ticket_vendor_head_id'
        );
    }

    public function approvals()
    {
        return $this->hasMany(
            TicketVendorApproval::class,
            'ticket_vendor_head_id'
        )->orderBy('step_order');
    }

    
}
