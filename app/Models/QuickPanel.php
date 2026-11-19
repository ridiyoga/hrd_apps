<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickPanel extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $table = 'quick_panel';
    protected $keyType = 'string';
    protected $guarded = ['id_quick'];
    protected $fillable = [
        'id_quick',
        'menu_id',
        'title_quick',
        'message_quick',
        'user_id_from',
        'user_id_to',
        'read_at',
        'status',
        'quick_type',
        'content_id',
        'created_at',
        'updated_at'
    ];
    function Menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
    public function Cuti()
    {
        return $this->belongsTo(Cuti::class, 'content_id', 'id');
    }
    public function Izin()
    {
        return $this->belongsTo(Izin::class, 'content_id', 'id');
    }
    public function IzinTerlambat()
    {
        return $this->belongsTo(IzinTerlambat::class, 'content_id', 'id');
    }
    public function IzinPulang()
    {
        return $this->belongsTo(IzinPulang::class, 'content_id', 'id');
    }
    public function IzinKeluar()
    {
        return $this->belongsTo(IzinKeluar::class, 'content_id', 'id');
    }

    public function IzinSakit()
    {
        return $this->belongsTo(IzinSakit::class, 'content_id', 'id');
    }
    public function IzinTidakMasuk()
    {
        return $this->belongsTo(IzinTidakMasuk::class, 'content_id', 'id');
    }
    public function IzinLibur()
    {
        return $this->belongsTo(IzinLibur::class, 'content_id', 'id');
    }
    public function Lembur()
    {
        return $this->belongsTo(Lembur::class, 'content_id', 'id');
    }
    public function Perdin()
    {
        return $this->belongsTo(Perdin::class, 'content_id', 'id');
    }
    public function PerjalananDinas()
    {
        return $this->belongsTo(PerjalananDinas::class, 'content_id', 'id');
    }
    public function UserFrom()
    {
        return $this->belongsTo(Karyawan::class, 'user_id_from', 'id');
    }
    public function UserTo()
    {
        return $this->belongsTo(Karyawan::class, 'user_id_to', 'id');
    }
}
