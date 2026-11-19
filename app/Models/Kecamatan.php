<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;
    protected $table = 'master_kecamatan';
    protected $fillable = ['code', 'city_code', 'name', 'created_at', 'updated_at'];
}
