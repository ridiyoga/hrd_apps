<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiPeriod extends Model
{
    protected $table = 'cuti_period';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id_period'];
    protected $primaryKey = 'id_period';
    protected $fillable = [
        'id_period',
        'nama_period',
        'legal_number_period',
        'tanggal_mulai_period',
        'tanggal_selesai_period',
        'status',
        'created_at',
        'updated_at'
    ];
}
