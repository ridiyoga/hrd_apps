<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManagementTaskAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_management_task_attachments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'uploaded_by',
        'source',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementTask::class, 'task_id', 'id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
