<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ProjectManagementTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_management_tasks';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'milestone_id',
        'parent_task_id',
        'task_code',
        'title',
        'type',
        'priority',
        'status',
        'status_updated_at',
        'reporter_id',
        'primary_assignee_id',
        'reviewer_id',
        'estimate_hours',
        'actual_hours',
        'start_date',
        'due_date',
        'due_revision_date',
        'review_due_date',
        'presented_at',
        'acceptance_criteria',
        'developer_update_notes',
        'blocker_reason',
        'review_notes',
        'review_revision_notes',
        'reviewed_at',
        'reviewed_by',
        'blocked_at',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'estimate_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'status_updated_at' => 'datetime',
        'start_date' => 'date',
        'due_date' => 'date',
        'due_revision_date' => 'date',
        'review_due_date' => 'date',
        'presented_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementProject::class, 'project_id', 'id');
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementMilestone::class, 'milestone_id', 'id');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_task_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_task_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectManagementTaskComment::class, 'task_id', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectManagementTaskActivity::class, 'task_id', 'id')
            ->orderByDesc('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectManagementTaskAttachment::class, 'task_id', 'id')
            ->orderByDesc('created_at');
    }

    public function meetings()
    {
        return $this->belongsToMany(ProjectManagementMeeting::class, 'project_management_meeting_tasks', 'task_id', 'meeting_id')
            ->withPivot('discussion_notes')
            ->withTimestamps();
    }

    public function communicatedMeetings()
    {
        return $this->belongsToMany(ProjectManagementMeeting::class, 'project_management_meeting_tasks', 'task_id', 'meeting_id')
            ->wherePivot('decision', 'accepted')
            ->where('project_management_meetings.status', 'final')
            ->withPivot(['discussion_notes', 'decision', 'return_expectation', 'revision_due_date'])
            ->withTimestamps();
    }

    public function isCommunicated(): bool
    {
        return $this->relationLoaded('communicatedMeetings')
            ? $this->communicatedMeetings->isNotEmpty()
            : $this->communicatedMeetings()->exists();
    }

    public function communicatedAt(): ?Carbon
    {
        if ($this->relationLoaded('communicatedMeetings')) {
            return $this->communicatedMeetings->sortByDesc('meeting_at')->first()?->meeting_at;
        }

        $meetingAt = $this->communicatedMeetings()->max('meeting_at');

        return $meetingAt ? Carbon::parse($meetingAt) : null;
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id', 'id');
    }

    public function primaryAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_assignee_id', 'id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }
}
