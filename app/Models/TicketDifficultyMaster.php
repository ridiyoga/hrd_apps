<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDifficultyMaster extends Model
{
    protected $table = 'ticket_difficulty_master';

    protected $fillable = [
        'code',
        'name',
        'expected_hours',
    ];

    protected $casts = [
        'expected_hours' => 'float',
    ];
}
