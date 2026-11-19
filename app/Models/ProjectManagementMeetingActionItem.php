<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProjectManagementMeetingActionItem extends Model { protected $table = 'project_management_meeting_action_items'; public $incrementing = false; protected $keyType = 'string'; protected $guarded = []; protected $casts = ['due_date' => 'date', 'estimate_hours' => 'decimal:2']; public function pic(){ return $this->belongsTo(User::class, 'pic_id'); } public function linkedTask(){ return $this->belongsTo(ProjectManagementTask::class, 'linked_task_id'); } public function generatedTask(){ return $this->belongsTo(ProjectManagementTask::class, 'generated_task_id'); } }
