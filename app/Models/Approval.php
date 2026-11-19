<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Testing\Fluent\Concerns\Has;

class Approval extends Model
{
    use HasFactory;
    protected $table = 'master_approval';
    protected $fillable = [
        'id_approval',
        'group_id',
        'nama_approval',
        'level_approval',
        'parent_approval_id',
        'holding_id',
        'departemen_id'
    ];
    protected $primaryKey = 'id_approval';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'departemen_id' => 'array',
    ];
    function levelJabatan(): BelongsTo
    {
        return $this->belongsTo(LevelJabatan::class, 'level_approval', 'id');
    }
    public function Departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id');
    }
    function approvalGroup(): BelongsTo
    {
        return $this->belongsTo(ApprovalGroup::class, 'group_id', 'id_group');
    }
    function approvalParent(): HasMany
    {
        return $this->hasMany(Approval::class, 'parent_approval_id');
    }
    function Parent(): BelongsTo
    {
        return $this->belongsTo(Approval::class, 'parent_approval_id', 'id_approval');
    }
    public function ApprovalUser(): HasMany
    {
        return $this->hasMany(ApprovalUser::class, 'approval_id', 'id_approval');
    }
    public function ApprovalUserFirst(): BelongsTo
    {
        return $this->belongsTo(ApprovalUser::class, 'id_approval', 'approval_id');
    }
}
