<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerbitDokumen extends Model
{
    use SoftDeletes;

    protected $table = 'penerbit_dokumen';

    protected $primaryKey = 'id_penerbit';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_penerbit',
        'jenis_penerbit',
        'alamat_penerbit',
        'nama_pic_penerbit',
        'nomor_pic_penerbit',
        'kategori_dokumen_id',
    ];

    /**
     * =====================
     * RELATIONSHIPS
     * =====================
     */

    // Penerbit → Kategori Dokumen
    public function kategoriDokumen(): BelongsTo
    {
        return $this->belongsTo(
            KategoriDokumen::class,
            'kategori_dokumen_id',
            'id_kategori'
        );
    }

    // Penerbit → Banyak Dokumen Legal
    public function dokumenLegal(): HasMany
    {
        return $this->hasMany(
            DokumenLegal::class,
            'penerbit_id',
            'id_penerbit'
        );
    }
}
