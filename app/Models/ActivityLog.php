<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = ['id'];
    protected $fillable = [
        'id',
        'user_id',
        'user_name',
        'menu_id',
        'menu_name',
        'holding_id',
        'holding_category',
        'object_id',
        'activity',
        'keterangan',
        'id_table',
        'table',
        'description',
        'karir',
    ];
    public function user()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
