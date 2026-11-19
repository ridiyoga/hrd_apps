<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKategoriDokumen extends Model
{
    protected $table = 'sub_kategori_dokumen_legal';
    protected $primaryKey = 'id_sub';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sub',
        'sub_name',
        'kategori_id',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_id', 'id_kategori');
    }
}
