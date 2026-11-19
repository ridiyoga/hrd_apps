<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriCuti extends Model
{
    use HasFactory, UuidTrait;
    protected $table = 'cuti_kategori';
    protected $guard = 'id';

    protected $fillable = [
        'nama_cuti',
        'jumlah_hari',
        'target_gender',
        'target_status_nikah',
        'status',
    ];
}
