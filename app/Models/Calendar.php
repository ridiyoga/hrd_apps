<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendars';
    public $timestamps = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'memo_id',
        'nama_kegiatan',
        'keterangan',
        'start_date',
        'end_date',
        'status',
        'holding',
        'type',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
