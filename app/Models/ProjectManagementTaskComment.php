<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManagementTaskComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_management_task_comments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'parent_id',
        'user_id',
        'comment_type',
        'comment',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementTask::class, 'task_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
