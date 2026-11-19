<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHubmenu extends Model
{
    protected $table = 'ticket_hub_menus';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'nama_menu', 
        'icon', 
        'description',
        'color_class', 
        'route_name', 
        'route_params',
        'is_active', 
        'sort_order',
    ];

    protected $casts = [
        'route_params' => 'array',
        'is_active'    => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'ticket_hub_menu_users',
            'hub_menu_id',
            'user_id'
        );
    }

    /**
     * Relasi ke setting approval per holding.
     */
    public function approvals()
    {
        return $this->hasMany(
            \App\Models\TicketHubMenuApproval::class,
            'hub_menu_id',
            'id'
        );
    }

    /**
     * Bangun URL berdasarkan route_name + route_params.
     * Nilai null pada params → diganti menu_id dari context.
     */
    public function buildUrl(string $menuId): string
    {
        $params = $this->route_params ?? [];

        foreach ($params as $key => $val) {
            if (is_null($val)) {
                $params[$key] = $menuId;
            }
        }

        try {
            if (empty($params)) {
                $routeObj = app('router')->getRoutes()->getByName($this->route_name);
                if ($routeObj) {
                    preg_match_all('/\{(\w+?)\??}/', $routeObj->uri(), $matches);
                    $firstParam = $matches[1][0] ?? 'menu_id';
                    $params = [$firstParam => $menuId];
                } else {
                    $params = [$menuId];
                }
            }
            return route($this->route_name, $params);
        } catch (\Throwable $e) {
            return '#';
        }
    }
}
