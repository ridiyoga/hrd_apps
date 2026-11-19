<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sip extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    protected $guarded = ['id'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
