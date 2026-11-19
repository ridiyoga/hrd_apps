<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class DokumenLegal extends Model
{
    use HasFactory;

    protected $table = 'dokumen_legal';

    // Karena id DIISI MANUAL (DB kamu tidak auto increment)
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'nomer_dukumen',
        'nama',
        'file',
        'tanggal_awal',
        'tanggal_akhir',
        'keterangan',
        'status',
        'kategori_id',
        'sub_kategori_id',
        'penerbit_id',
        'holding_id',
        'site_id',
        'dokumen_reference',
        'dokumen_pengganti',
        'is_dokumen_pengganti',
    ];

    protected $casts = [
        'tanggal_awal'  => 'date',
        'tanggal_akhir' => 'date',
    ];

    /* ================= RELATIONS ================= */

    public function holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class, 'holding_id', 'id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }

    public function penerbit()
    {
        return $this->belongsTo(PenerbitDokumen::class, 'penerbit_id', 'id_penerbit');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_id', 'id_kategori');
    }

    public function subKategori(): BelongsTo
    {
        return $this->belongsTo(SubKategoriDokumen::class, 'sub_kategori_id', 'id_sub');
    }

    // dokumen ini menggantikan dokumen lama
    public function dokumenLama(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'dokumen_reference', // FK di dokumen baru
            'id'                 // PK dokumen lama
        );
    }

    // dokumen ini DIGANTIKAN oleh dokumen baru
    public function dokumenPengganti(): HasOne
    {
        return $this->hasOne(
            self::class,
            'dokumen_reference', // FK di dokumen baru
            'id'                 // PK dokumen lama
        );
    }

    /* ================= ACCESSORS ================= */

    public function getIsExpiredAttribute(): bool
    {
        return $this->tanggal_akhir < now();
    }

    /* ================= METHODS ================= */
    public function hitungStatusByTanggal(): string
    {
        if (!$this->tanggal_akhir) {
            return 'aktif';
        }

        return Carbon::today()->gt(Carbon::parse($this->tanggal_akhir))
            ? 'expired'
            : 'aktif';
    }
}
