<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMenu extends Model
{
    protected $table = 'ticket_menu';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $appends = ['kategori_display'];

    protected $fillable = [
        'id',
        'nama_menu',
        'kategori', //1. epicor, 2. accurate
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function TicketSubMenu()
    {
        return $this->hasMany(TicketSubMenu::class);
    }

    public function kondisis()
    {
        return $this->belongsToMany(
            TicketKondisiMenu::class,
            'ticket_menu_kondisi',   // pivot table
            'menu_id',               // FK kolom ini di pivot
            'kondisi_id'             // FK model target di pivot
        );
    }

    public function departemens()
    {
        return $this->belongsToMany(
            Departemen::class,
            'ticket_menu_departemen', // pivot table name
            'menu_id',               // FK to ticket_menu
            'departemen_id'          // FK to departemens
        );
    }

    public function approvalFlows()
    {
        return $this->belongsToMany(
            TicketApprovalFlow::class,
            'ticket_approval_flow_menu',   // pivot table
            'menu_id',               // FK kolom ini di pivot
            'approval_flow_id'             // FK model target di pivot
        );
    }

    const KATEGORI_EPICOR       = 1;
    const KATEGORI_ACCURATE_SP  = 2;
    const KATEGORI_ACCURATE_AJA = 3;

    public static function getKategoriList(): array
    {
        return [
            self::KATEGORI_EPICOR       => 'Epicor ERP',
            self::KATEGORI_ACCURATE_SP  => 'Accurate Online SP',
            self::KATEGORI_ACCURATE_AJA => 'Accurate Online AJA',
        ];
    }

    public function getKategoriDisplayAttribute(): string
    {
        return match ((int) $this->kategori) {
            1 => 'Epicor ERP',
            2 => 'Accurate Online SP',
            3 => 'Accurate Online AJA',
            default => 'Unknown',
        };
    }
}
