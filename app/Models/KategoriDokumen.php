<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriDokumen extends Model
{
    protected $table = 'kategori_dokumen_legal';

    protected $primaryKey = 'id_kategori';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_kategori',
        'nama_kategori',
        'reminder_days',
    ];

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenLegal::class, 'kategori_id', 'id_kategori');
    }

    public function subKategori(): HasMany
    {
        return $this->hasMany(
            \App\Models\SubKategoriDokumen::class,
            'kategori_id',
            'id_kategori'
        );
    }

    public function penerbitDokumen()
    {
        return $this->hasMany(
            PenerbitDokumen::class,
            'kategori_dokumen_id',
            'id_kategori'
        );
    }
}
