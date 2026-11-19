<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Models\TicketApprovalStep;

class TicketApprovalFlow extends Model
{
    protected $table = 'ticket_approval_flow';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 
        'nama_flow', 
        'description', 
        'is_active'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->id ??= (string) Str::uuid());
    }

    public function steps()
    {
        return $this->hasMany(TicketApprovalStep::class, 'approval_flow_id')
            ->orderBy('step_order');
    }

    public function subMenus()
    {
        return $this->belongsToMany(
            TicketSubMenu::class,
            'ticket_approval_flow_menu',
            'approval_flow_id',
            'sub_menu_id'
        );
    }

    // alias dipakai di controller lama
    public function approvalFlowMenuTicket()
    {
        return $this->subMenus();
    }
}
