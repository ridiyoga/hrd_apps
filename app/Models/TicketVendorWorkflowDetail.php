<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketVendorWorkflowDetail extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $table = 'ticket_vendor_workflow_detail';

    protected $fillable = [
        'workflow_master_id',
        'step_order',
        'parent_step_id',
        'role_id',
        'role_name',
        'departemen_id',
        'divisi_id',
        'bagian_id',
        'jabatan_id',
        'karyawan_id',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function workflow()
    {
        return $this->belongsTo(
            TicketVendorWorkflowMaster::class,
            'workflow_master_id'
        );
    }

    // Helper untuk mendapatkan parent approval
    public function parentStep()
    {
        return $this->belongsTo(
            TicketVendorWorkflowDetail::class,
            'parent_step_id'
        );
    }

    // Helper untuk mendapatkan next approval
    public function nextStep()
    {
        return $this->hasOne(
            TicketVendorWorkflowDetail::class,
            'parent_step_id'
        );
    }

    // Helper untuk mendapatkan departemen
    public function departemen()
    {
        return $this->belongsTo(
            Departemen::class,
            'departemen_id'
        );
    }

    // Helper untuk mendapatkan divisi
    public function divisi()
    {
        return $this->belongsTo(
            Divisi::class,
            'divisi_id'
        );
    }

    // Helper untuk mendapatkan bagian
    public function bagian()
    {
        return $this->belongsTo(
            Bagian::class,
            'bagian_id'
        );
    }

    // Helper untuk mendapatkan jabatan
    public function jabatan()
    {
        return $this->belongsTo(
            Jabatan::class,
            'jabatan_id'
        );
    }

    // Helper untuk mendapatkan karyawan
    public function karyawan()
    {
        return $this->belongsTo(
            Karyawan::class,
            'karyawan_id'
        );
    }
}
