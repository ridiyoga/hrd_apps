<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProjectManagementMeetingTask extends Model { protected $table = 'project_management_meeting_tasks'; public $incrementing = false; protected $keyType = 'string'; protected $guarded = []; protected $casts = ['revision_due_date' => 'date', 'decided_at' => 'datetime']; public function meeting(){ return $this->belongsTo(ProjectManagementMeeting::class, 'meeting_id'); } public function task(){ return $this->belongsTo(ProjectManagementTask::class, 'task_id'); } }
