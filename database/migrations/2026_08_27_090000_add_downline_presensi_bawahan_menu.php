<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $menu = DB::table('menus')
            ->where('url', 'reportpresensibawahan')
            ->first();

        $parent = DB::table('menus')
            ->whereRaw('LOWER(name) = ?', ['master data'])
            ->where('kategori', 'web')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->first();

        if (!$menu) {
            $sortOrder = $parent
                ? ((int) DB::table('menus')->where('parent_id', $parent->id)->max('sort_order') + 1)
                : ((int) DB::table('menus')->where('kategori', 'web')->max('sort_order') + 1);

            $payload = [
                'name' => 'Downline Presensi Bawahan',
                'icon' => 'mdi-account-clock-outline',
                'url' => 'reportpresensibawahan',
                'parent_id' => $parent->id ?? null,
                'sort_order' => $sortOrder,
                'is_active' => 1,
                'main' => 0,
                'kategori' => 'web',
            ];

            if (Schema::hasColumn('menus', 'created_at')) {
                $payload['created_at'] = now();
            }

            if (Schema::hasColumn('menus', 'updated_at')) {
                $payload['updated_at'] = now();
            }

            $menuId = DB::table('menus')->insertGetId($payload);
        } else {
            $menuId = $menu->id;
        }

        if (!$parent) {
            return;
        }

        $roleMenus = DB::table('role_menus')
            ->where('menu_id', $parent->id)
            ->get();

        foreach ($roleMenus as $roleMenu) {
            $exists = DB::table('role_menus')
                ->where('role_id', $roleMenu->role_id)
                ->where('menu_id', $menuId)
                ->exists();

            if ($exists) {
                continue;
            }

            $rolePayload = [
                'role_id' => $roleMenu->role_id,
                'menu_id' => $menuId,
                'can_create' => $roleMenu->can_create ?? 0,
                'can_edit' => $roleMenu->can_edit ?? 0,
                'can_delete' => $roleMenu->can_delete ?? 0,
                'can_view' => $roleMenu->can_view ?? 1,
            ];

            if (Schema::hasColumn('role_menus', 'created_at')) {
                $rolePayload['created_at'] = now();
            }

            if (Schema::hasColumn('role_menus', 'updated_at')) {
                $rolePayload['updated_at'] = now();
            }

            DB::table('role_menus')->insert($rolePayload);
        }
    }

    public function down(): void
    {
        $menu = DB::table('menus')
            ->where('url', 'reportpresensibawahan')
            ->where('name', 'Downline Presensi Bawahan')
            ->first();

        if (!$menu) {
            return;
        }

        DB::table('role_menus')->where('menu_id', $menu->id)->delete();
        DB::table('menus')->where('id', $menu->id)->delete();
    }
};
