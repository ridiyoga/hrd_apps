<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TicketApproval extends Model
{
    protected $table = 'ticket_approvals';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'ticket_id',
        'flow_step_id',
        'karyawan_id',
        'karyawan_name',
        'role',
        'status',
        'step_order',
        'branch_sub_menu_id',
        'approved_at',
        'signature',
        'comment',
        'created_at',
        'updated_at',
        'dependency_rule_id',
        'generate_status',
        'generated_ticket_id',
        'generated_at',
        'generate_error',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'step_order'  => 'integer',
        'generated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function ticket()
    {
        return $this->belongsTo(TicketHead::class, 'ticket_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function step()
    {
        return $this->belongsTo(TicketApprovalStep::class, 'flow_step_id');
    }

    public function branchSubMenu()
    {
        return $this->belongsTo(TicketSubMenu::class, 'branch_sub_menu_id');
    }

    public function dependencyRule()
    {
        return $this->belongsTo(TicketDependencyRule::class, 'dependency_rule_id');
    }
}
