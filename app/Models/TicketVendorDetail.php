<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketVendorDetail extends Model
{
    protected $table = 'ticket_vendor_detail';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'ticket_vendor_head_id',
        'nama_usaha',
        'alamat_usaha',
        'nama_pemilik',
        'nik',
        'alamat_pemilik',
        'npwp',
        'npwp16',
        'alamat_npwp',
        'nama_bank',
        'no_rekening',
        'nama_bank_penerima',
        'cabang_bank',
        'syarat_pembayaran',
        'contact_person',
        'nama_ktp',
        'alamat_ktp',
        'jabatan',
        'no_telp',
        // Accurate
        'dipakai_cabang',
        'kategori',
        'sourcing',
        // Epicor
        'metode_pembayaran',
        'supplier_afiliasi',
        'tax_liability',
        'supplier_group',
        'gl_control_type',
        'gl_control_code',
    ];

    protected $casts = [
        'supplier_afiliasi' => 'boolean',
    ];

    public function header()
    {
        return $this->belongsTo(
            TicketVendorHead::class,
            'ticket_vendor_head_id'
        );
    }
}
