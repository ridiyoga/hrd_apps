<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHubMenuApproval extends Model
{
    use HasFactory;

    protected $table = 'ticket_hub_menu_approvals';

    protected $fillable = [
        'hub_menu_id',
        'holding_id',
        'approval_group_id',
    ];

    public function hubMenu()
    {
        return $this->belongsTo(TicketHubMenu::class, 'hub_menu_id', 'id');
    }

    public function approvalGroup()
    {
        return $this->belongsTo(ApprovalGroup::class, 'approval_group_id', 'id_group');
    }
}
