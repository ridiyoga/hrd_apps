<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalGroup extends Model
{
    use HasFactory;
    protected $table = 'master_approval_group';
    protected $fillable = [
        'nama_group',
        'menu_id',
        'holding_id',
    ];
    protected $primaryKey = 'id_group';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'menu_id' => 'array',
    ];
    public function approval(): HasMany
    {
        return $this->hasMany(Approval::class, 'group_id');
    }
    public function approvalFirst(): BelongsTo
    {
        return $this->belongsTo(Approval::class, 'id_group', 'group_id');
    }
}
