<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, UuidTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard = 'web';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $fillable = [
        'karyawan_id',
        'username',
        'is_admin',
        'password',
        'password_show',
        'access_1',
        'access_2',
        'access_3',
        'role',
        'user_aktif',
        'photo',
        'alasan',
        'menu_active',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_show',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function Karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }
    public function KaryawanNIK(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'nomor_identitas_karyawan');
    }
    public function roleUsers(): HasMany
    {
        return $this->hasMany(RoleUsers::class, 'role_user_id', 'id');
    }
    public function approvalUsers(): HasMany
    {
        return $this->hasMany(ApprovalUser::class, 'approval_user_id', 'id');
    }
}
