<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $parent = DB::table('menus')->where('kategori', 'web')->where('name', 'Report')->whereNull('parent_id')->first();
            if (!$parent) {
                throw new RuntimeException('Menu induk Report belum terdaftar.');
            }
            $menu = DB::table('menus')->where('kategori', 'web')->where('parent_id', $parent->id)->where('name', 'Project Management')->first();
            $payload = ['url' => 'project_management/report', 'icon' => 'mdi-file-chart-outline', 'is_active' => 1, 'updated_at' => now()];
            if ($menu) {
                DB::table('menus')->where('id', $menu->id)->update($payload);
                $menuId = $menu->id;
            } else {
                $menuId = DB::table('menus')->insertGetId(array_merge($payload, [
                    'name' => 'Project Management', 'parent_id' => $parent->id, 'kategori' => 'web',
                    'sort_order' => ((int) DB::table('menus')->where('parent_id', $parent->id)->max('sort_order')) + 1,
                    'created_at' => now(),
                ]));
            }
            $source = DB::table('menus')->where('kategori', 'web')->where('url', 'project_management/index')->first();
            if (!$source) {
                return;
            }
            foreach (DB::table('role_menus')->where('menu_id', $source->id)->get() as $permission) {
                foreach ([$parent->id, $menuId] as $target) {
                    if (!DB::table('role_menus')->where('role_id', $permission->role_id)->where('menu_id', $target)->exists()) {
                        DB::table('role_menus')->insert([
                            'role_id' => $permission->role_id, 'menu_id' => $target,
                            'can_view' => $permission->can_view, 'can_create' => 0, 'can_edit' => 0, 'can_delete' => 0,
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        DB::table('menus')->where('url', 'project_management/report')->update(['url' => null, 'updated_at' => now()]);
    }
};
