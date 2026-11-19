<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProjectManagementMeetingParticipant extends Model { protected $table = 'project_management_meeting_participants'; public $incrementing = false; protected $keyType = 'string'; protected $guarded = []; public function user(){ return $this->belongsTo(User::class, 'user_id'); } }
