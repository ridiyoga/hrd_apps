<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = ['id'];
    protected $fillable = ['name', 'icon', 'url', 'parent_id', 'sort_order', 'is_active', 'main', 'kategori'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }
    public function subchildren()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menus');
    }
}
