<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        // dd($get_role);
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $menus = collect();
        } else {
            $menus = Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
                ->whereNull('parent_id') // menu utama
                ->with('children')
                ->where('kategori', 'web')      // load submenunya
                ->orderBy('sort_order')
                ->get();
        }
        // dd($menus);
        $get_menus = Menu::whereNull('parent_id')->with('children')->where('kategori', 'web')->orderBy('sort_order')->get();
        $parenPts = Menu::whereNull('parent_id')->where('kategori', 'web')->orderBy('sort_order')->get();
        // dd($get_menus);
        return view('admin.menu.index', compact('holding', 'menus', 'get_menus', 'parenPts'));
    }
    public function get_parent_options($parentId, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            $parentId = $parentId == 'null' ? null : $parentId;
            $parentOptions = Menu::where('id', $parentId)->orderBy('sort_order')->get();
            if ($parentOptions->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'No parent options found'
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'data' => $parentOptions
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function save_all_change(Request $request)
    {
        dd($request->all());
        $menus = Menu::all();
        foreach ($menus as $menu) {
            $menu->save();
        }
        return response()->json(['status' => 'success']);
    }
    public function store(Request $request, $holding)
    {
        // dd($request->all(), $holding);
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'route' => 'nullable',
            'icon' => 'required',
            'kategori' => 'required',
            'parent_id' => 'nullable',
        ], [
            'kategori.required' => 'Kategori harus diisi',
            'name.required' => 'Nama menu harus diisi',
            'icon.required' => 'Icon harus diisi',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $validator->errors()
            ]);
        }
        if ($request->menu_id == NULL) {
            $sort_order = Menu::where('kategori', $request->kategori)->max('sort_order') + 1;
            try {
                $menu = new Menu();
                $menu->name = $request->name;
                $menu->kategori = $request->kategori;
                $menu->sort_order = $sort_order;
                $menu->url = $request->route;
                $menu->icon = $request->icon;
                $menu->parent_id = $request->parent_id;
                $menu->save();

                $nama_menu = Menu::where('id', $request->menu_id_add)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id_add,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'table' => 'jabatan',
                    'activity' => 'create',
                    'description' => 'menambah menu ' . $request->name . ' pada kategori ' . $request->kategori,
                    'keterangan' => 'Success',
                ]);
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Berhasil menambahkan menu'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            try {
                $update = Menu::where('id', $request->menu_id)->first();
                $update->name = $request->name;
                $update->kategori = $request->kategori;
                $update->url = $request->route;
                $update->icon = $request->icon;
                $update->parent_id = $request->parent_id;
                $update->save();
                return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Berhasil memperbarui menu'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
    function update_menu(Request $request)
    {
        // dd($request->menu_id);
        try {
            User::where('id', Auth::user()->id)->update([
                'menu_active' => $request->menu_id
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function delete($id, $menu_id_add)
    {
        try {
            $delete = Menu::where('id', $id)->first();
            $nama_menu = Menu::where('id', $id)->first();
            $user_name = User::where('id', Auth::user()->id)->first();
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $menu_id_add,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'table' => 'jabatan',
                'activity' => 'create',
                'description' => 'menghapus menu ' . $delete->name . ' pada kategori ' . $delete->kategori,
                'keterangan' => 'Success',
            ]);

            $delete->delete();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Berhasil menghapus menu'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
