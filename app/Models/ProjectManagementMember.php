<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectManagementMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'project_management_members';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'user_id',
        'project_role',
        'allocation_percent',
        'allocation_hours',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allocation_percent' => 'decimal:2',
        'allocation_hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectManagementProject::class, 'project_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
