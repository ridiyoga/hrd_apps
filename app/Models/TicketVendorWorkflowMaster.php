<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketVendorWorkflowMaster extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $table = 'ticket_vendor_workflow_master';

    protected $fillable = [
        'workflow_code',
        'workflow_name',
        'holding_id',
        'system_type',
        'vendor_category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(
            TicketVendorWorkflowDetail::class,
            'workflow_master_id'
        )->orderBy('step_order');
    }
}
