<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bagian extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = ['id'];
    protected $fillable = ['id', 'holding', 'nama_bagian', 'divisi_id', 'created_at', 'updated_at'];

    public function Holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class, 'holding', 'id');
    }
    public function Divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id');
    }
    public function Karyawan()
    {
        return $this->hasMany(Karyawan::class, 'bagian_id', 'id');
    }
}
