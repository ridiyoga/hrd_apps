<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManagementMeeting extends Model
{
    use SoftDeletes;

    protected $table = 'project_management_meetings';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = ['meeting_at' => 'datetime', 'finalized_at' => 'datetime'];

    public function project() { return $this->belongsTo(ProjectManagementProject::class, 'project_id'); }
    public function milestone() { return $this->belongsTo(ProjectManagementMilestone::class, 'milestone_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function participants() { return $this->hasMany(ProjectManagementMeetingParticipant::class, 'meeting_id'); }
    public function meetingTasks() { return $this->hasMany(ProjectManagementMeetingTask::class, 'meeting_id'); }
    public function tasks() { return $this->belongsToMany(ProjectManagementTask::class, 'project_management_meeting_tasks', 'meeting_id', 'task_id')->withPivot(['discussion_notes', 'decision', 'return_expectation', 'revision_due_date', 'decided_by', 'decided_at'])->withTimestamps(); }
    public function actionItems() { return $this->hasMany(ProjectManagementMeetingActionItem::class, 'meeting_id'); }
    public function attachments() { return $this->hasMany(ProjectManagementMeetingAttachment::class, 'meeting_id'); }
}
