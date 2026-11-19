<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalUser extends Model
{
    //
    protected $table = 'approval_users';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id_approval_user'];
    protected $primaryKey = 'id_approval_user';
    protected $fillable = [
        'approval_id',
        'approval_user_id',
        'parent_approve_id',
        'karyawan_id',
    ];

    public function approval(): BelongsTo
    {
        return $this->belongsTo(Approval::class, 'approval_id', 'id_approval');
    }
    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_user_id', 'id');
    }
    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
    public function ChildrenRecursive(): HasMany
    {
        return $this->Children()->with('ChildrenRecursive', 'UserKaryawan');
    }
}
