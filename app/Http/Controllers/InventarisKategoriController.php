<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\InventarisKategori;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class InventarisKategoriController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
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
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        return view('admin.inventaris.kategori_inventaris.index', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
        ]);
    }
    // public function ImportDepartemen(Request $request, $holding)
    // {
    //     $holding = Holding::where('holding_code', $holding)->first();
    //     Excel::import(new DepartemenImport, $request->file_excel);

    //     return redirect('/kategori_inventaris/' . $holding)->with('success', 'Import Departemen Sukses');
    // }
    public function datatable(Request $request)
    {
        $table = InventarisKategori::orderBy('nama_kategori', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button id="btn_edit_kategori" data-id="' . $row->id . '" data-kategori="' . $row->nama_kategori . '" data-deskripsi="' . $row->deskripsi . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_kategori" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns([
                    'option',
                    'jumlah_divisi',
                    'option'
                ])
                ->make(true);
        }
    }

    public function insert(Request $request, $holding)
    {
        // dd($request->all());
        $holding  = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_kategori' => 'required|max:255',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        try {
            $id_kategori = Uuid::uuid4();
            InventarisKategori::create(
                [
                    'id' => $id_kategori,
                    'nama_kategori' => $request->nama_kategori,
                    'deskripsi' => $request->deskripsi,
                    'created_at' => now()
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_kategori,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'kategori_inventaris',
                'activity' => 'create',
                'description' => 'Menambahkan kategori inventaris baru ' . $request->nama_kategori,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function update(Request $request, $holding)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_kategori' => 'required|max:255',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        try {
            // $id_kategori = Uuid::uuid4();
            InventarisKategori::where('id', $request->id_kategori)->update([
                'nama_kategori' => $request->nama_kategori,
                'deskripsi' => $request->deskripsi,
                'updated_at' => now()

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_kategori,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'kategori_inventaris',
                'activity' => 'update',
                'description' => 'mengedit nama kategori inventaris ' . $request->nama_kategori,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function delete($id, $holding, $menu_id)
    {
        // dd($id);
        $holding = Holding::where('holding_code', $holding)->first();
        // dd($holding);
        // $cek_divisi = Divisi::where('kategori_id', $id)->where('holding', $holding->id)->count();
        $kategori_inventaris = InventarisKategori::where('id', $id)->first();
        // if ($cek_divisi == 0) {
        // $cek_karyawan = KaryawanJabatan::where('kategori_inventaris_id', $id)->where('is_active', '1')->count();
        // if ($cek_karyawan == 0) {
        $nama_menu = Menu::where('id', $menu_id)->first();
        $user_name = User::where('id', Auth::user()->id)->first();
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $id,
            'holding_id' => $holding->id,
            'holding_category' => $holding->holding_category,
            'table' => 'kategori_inventaris',
            'activity' => 'delete',
            'description' => 'menghapus kategori inventaris ' . $kategori_inventaris->nama_kategori,
            'keterangan' => 'Success',
        ]);

        $kategori_inventaris->delete();
        return response()->json(['status' => 1]);
        // } else {
        //     $nama_menu = Menu::where('id', $menu_id)->first();
        //     $user_name = User::where('id', Auth::user()->id)->first();
        //     ActivityLog::create([
        //         'user_id' => Auth::user()->id,
        //         'user_name' => $user_name->username,
        //         'menu_id' => $menu_id,
        //         'menu_name' => $nama_menu->name,
        //         'id_table' => $id,
        //         'holding_id' => $holding->id,
        //         'holding_category' => $holding->holding_category,
        //         'table' => 'kategori_inventaris',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus kategori inventaris karena sudah memiliki relasi karyawan ' . $kategori_inventaris->nama_kategori,
        //         'keterangan' => 'Failed',
        //     ]);
        //     return response()->json(['status' => 2]);
        // }
        // } else {
        //     $nama_menu = Menu::where('id', $menu_id)->first();
        //     $user_name = User::where('id', Auth::user()->id)->first();
        //     ActivityLog::create([
        //         'user_id' => Auth::user()->id,
        //         'user_name' => $user_name->username,
        //         'menu_id' => $menu_id,
        //         'menu_name' => $nama_menu->name,
        //         'id_table' => $id,
        //         'holding_id' => $holding->id,
        //         'holding_category' => $holding->holding_category,
        //         'table' => 'kategori_inventaris',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus kategori inventaris karena sudah memiliki relasi ' . $kategori_inventaris->nama_kategori,
        //         'keterangan' => 'Failed',
        //     ]);
        //     return response()->json(['status' => 0]);
        // }
    }
}
