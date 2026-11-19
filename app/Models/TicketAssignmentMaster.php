<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAssignmentMaster extends Model
{
    protected $table = 'ticket_assignment_master';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'assignment_type', //'A01 = Assignor', 'A02 = PIC'
        'holding_id',
        'departemen_id',
        'divisi_id',
        'bagian_id',
        'jabatan_id',
        'is_active',
        'created_at',
        'karyawan_id',
        'environment'
    ];

    public function holding()
    {
        return $this->belongsTo(Holding::class, 'holding_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
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

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function scopeForEnvironment($query, int $env)
    {
        return $query->where(function ($q) use ($env) {
            $q->whereNull('environment')->orWhere('environment', $env);
        });
    }
    
}
