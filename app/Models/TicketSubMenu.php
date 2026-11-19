<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TicketSubMenu extends Model
{
    protected $table = 'ticket_sub_menu';
    protected $guarded = [];
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable= [
        'id',
        'menu_id',
        'nama_sub_menu',
        'created_at',
        'updated_at',
        'deleted_at',
        'canvas_x',
        'canvas_y',
        'is_on_canvas',
    ];
    public function ticketMenu()
    {
        return $this->belongsTo(TicketMenu::class , 'menu_id');
    }
}
