<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $menu = DB::table('menus')
            ->where('url', 'laporan_presensi_bawahan/dashboard')
            ->where('kategori', 'mobile')
            ->first();

        if (!$menu) {
            $sortOrder = ((int) DB::table('menus')->where('kategori', 'mobile')->max('sort_order')) + 1;

            $payload = [
                'name' => 'Laporan Presensi Bawahan',
                'icon' => 'fa-solid fa-chart-line',
                'url' => 'laporan_presensi_bawahan/dashboard',
                'parent_id' => null,
                'sort_order' => $sortOrder,
                'is_active' => 1,
                'main' => 0,
                'kategori' => 'mobile',
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

        $sourceMenu = DB::table('menus')
            ->where('url', 'absensi_bawahan/dashboard')
            ->where('kategori', 'mobile')
            ->first();

        if (!$sourceMenu) {
            return;
        }

        $roleMenus = DB::table('role_menus')->where('menu_id', $sourceMenu->id)->get();

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
            ->where('url', 'laporan_presensi_bawahan/dashboard')
            ->where('kategori', 'mobile')
            ->first();

        if (!$menu) {
            return;
        }

        DB::table('role_menus')->where('menu_id', $menu->id)->delete();
        DB::table('menus')->where('id', $menu->id)->delete();
    }
};
