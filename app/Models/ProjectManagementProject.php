<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManagementProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_management_projects';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'holding_id',
        'project_code',
        'project_name',
        'project_type',
        'category',
        'description',
        'background',
        'objective',
        'expected_benefit',
        'requester_id',
        'requester_department_id',
        'business_owner_id',
        'project_manager_id',
        'technical_lead_id',
        'sponsor_id',
        'priority',
        'status',
        'health',
        'requested_at',
        'planned_start_date',
        'planned_end_date',
        'actual_start_at',
        'actual_end_at',
        'progress_percent',
        'scope_summary',
        'in_scope',
        'out_of_scope',
        'success_criteria',
        'estimated_hours',
        'actual_hours',
        'hold_reason',
        'pm_commentary',
        'decisions_needed',
        'cancel_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_at' => 'datetime',
        'actual_end_at' => 'datetime',
        'progress_percent' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public function scopeVisibleTo(Builder $query, User|string $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where(function (Builder $visibleQuery) use ($userId) {
            foreach (['requester_id', 'business_owner_id', 'project_manager_id', 'technical_lead_id', 'sponsor_id'] as $field) {
                $visibleQuery->orWhere($field, $userId);
            }

            $visibleQuery
                ->orWhereHas('members', fn(Builder $members) => $members
                    ->where('user_id', $userId)
                    ->where('is_active', true))
                ->orWhereHas('tasks', function (Builder $tasks) use ($userId) {
                    $tasks->where('primary_assignee_id', $userId)
                        ->orWhere('reviewer_id', $userId);
                });
        });
    }

    public function holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class, 'holding_id', 'id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id', 'id');
    }

    public function requesterDepartment(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'requester_department_id', 'id');
    }

    public function businessOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'business_owner_id', 'id');
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id', 'id');
    }

    public function technicalLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technical_lead_id', 'id');
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id', 'id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectManagementMember::class, 'project_id', 'id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectManagementMilestone::class, 'project_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectManagementTask::class, 'project_id', 'id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(ProjectManagementMeeting::class, 'project_id', 'id');
    }
}
