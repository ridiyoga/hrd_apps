<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManagementMilestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_management_milestones';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'milestone_name',
        'owner_id',
        'target_date',
        'status',
        'success_condition',
        'weight',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_date' => 'date',
        'weight' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementProject::class, 'project_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectManagementTask::class, 'milestone_id', 'id');
    }
}
