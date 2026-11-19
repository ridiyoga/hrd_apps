<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjalananDinas extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $table = 'perjalanan_dinas';
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $fillable = [
        'id_user',
        'nama_user',
    ];
}
