<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Karyawan;

class TicketApprovalStep extends Model
{
    protected $table = 'ticket_approval_step';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'approval_flow_id',
        'parent_step_id',
        'step_order',
        'step_name',
        'department_id',
        'divisi_id',
        'bagian_id',
        'jabatan_id',
        'jabatan_level_id',
        'approval_type',
        'is_final_step',
        'is_checker',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'is_final_step' => 'boolean',
        'is_checker'    => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->id ??= (string) Str::uuid());
    }

    public function flow()
    {
        return $this->belongsTo(TicketApprovalFlow::class, 'approval_flow_id');
    }

    public function jabatanLevel()
    {
        return $this->belongsTo(JabatanMaster::class, 'jabatan_level_id');
    }

    public function department()
    {
        return $this->belongsTo(Departemen::class, 'department_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
    /**
     * Pivot relationship to Karyawan (employees) assigned to this step.
     */
    public function karyawans()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'ticket_approval_karyawan',
            'approval_step_id',
            'karyawan_id'
        );
    }

    public function karyawansCount()
    {
        return $this->hasMany(TicketApprovalKaryawan::class, 'approval_step_id')
            ->selectRaw('approval_step_id, count(*) as aggregate')
            ->groupBy('approval_step_id');
    }
}

