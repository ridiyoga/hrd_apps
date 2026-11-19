<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $menu = DB::table('menus')->where('url', 'like', '%reportpresensibawahan%')->first();

        if (!$menu) {
            $menu = DB::table('menus')->where('url', 'like', '%laporan_presensi_bawahan%')->first();
        }

        if (!$menu) {
            return;
        }

        DB::table('menus')->where('id', $menu->id)->update([
            'name' => 'Laporan Presensi Bawahan',
            'icon' => '<i class="fa-solid fa-chart-line"></i>',
            'url' => 'laporan_presensi_bawahan/dashboard',
            'main' => 0,
            'kategori' => 'mobile',
            'is_active' => 1,
            'updated_at' => now(),
        ]);

        $role = DB::table('roles')
            ->where('role_name', 'Karyawan Bulanan')
            ->where('kategori', 'mobile')
            ->first();

        if (!$role) {
            return;
        }

        $roleMenu = DB::table('role_menus')
            ->where('role_id', $role->id)
            ->where('menu_id', $menu->id)
            ->first();

        if ($roleMenu) {
            DB::table('role_menus')->where('id', $roleMenu->id)->update([
                'can_view' => 1,
                'updated_at' => now(),
            ]);
        } else {
            $payload = [
                'role_id' => $role->id,
                'menu_id' => $menu->id,
                'can_create' => 0,
                'can_edit' => 0,
                'can_delete' => 0,
                'can_view' => 1,
            ];

            if (Schema::hasColumn('role_menus', 'created_at')) {
                $payload['created_at'] = now();
            }

            if (Schema::hasColumn('role_menus', 'updated_at')) {
                $payload['updated_at'] = now();
            }

            DB::table('role_menus')->insert($payload);
        }
    }

    public function down(): void
    {
        $menu = DB::table('menus')->where('url', 'laporan_presensi_bawahan/dashboard')->first();
        $role = DB::table('roles')->where('role_name', 'Karyawan Bulanan')->where('kategori', 'mobile')->first();

        if ($menu && $role) {
            DB::table('role_menus')->where('role_id', $role->id)->where('menu_id', $menu->id)->delete();
        }
    }
};
