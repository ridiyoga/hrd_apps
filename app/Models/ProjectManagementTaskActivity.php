<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectManagementTaskActivity extends Model
{
    use HasFactory;

    protected $table = 'project_management_task_activities';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'user_id',
        'activity_type',
        'field_name',
        'old_value',
        'new_value',
        'description',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementTask::class, 'task_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
