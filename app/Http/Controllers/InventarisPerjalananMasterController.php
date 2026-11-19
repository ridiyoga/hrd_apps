<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\InventarisPerjalanan;
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

class InventarisPerjalananController extends Controller
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
        return view('admin.inventaris.perjalanan.index', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
        ]);
    }
    // public function ImportDepartemen(Request $request, $holding)
    // {
    //     $holding = Holding::where('holding_code', $holding)->first();
    //     Excel::import(new DepartemenImport, $request->file_excel);

    //     return redirect('/perjalanan/' . $holding)->with('success', 'Import Departemen Sukses');
    // }
    public function datatable(Request $request)
    {
        $table = InventarisPerjalanan::orderBy('nama_perjalanan', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button id="btn_edit_perjalanan" data-id="' . $row->id . '" data-perjalanan="' . $row->nama_perjalanan . '" data-deskripsi="' . $row->deskripsi . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_perjalanan" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
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
                'nama_perjalanan' => 'required|max:255',
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
            $id_perjalanan = Uuid::uuid4();
            InventarisPerjalanan::create(
                [
                    'id' => $id_perjalanan,
                    'nama_perjalanan' => $request->nama_perjalanan,
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
                'id_table' => $id_perjalanan,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'perjalanan',
                'activity' => 'create',
                'description' => 'Menambahkan perjalanan inventaris baru ' . $request->nama_perjalanan,
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
                'nama_perjalanan' => 'required|max:255',
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
            // $id_perjalanan = Uuid::uuid4();
            InventarisPerjalanan::where('id', $request->id_perjalanan)->update([
                'nama_perjalanan' => $request->nama_perjalanan,
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
                'id_table' => $request->id_perjalanan,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'perjalanan',
                'activity' => 'update',
                'description' => 'mengedit nama perjalanan inventaris ' . $request->nama_perjalanan,
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
        // $cek_divisi = Divisi::where('perjalanan_id', $id)->where('holding', $holding->id)->count();
        $perjalanan = InventarisPerjalanan::where('id', $id)->first();
        // if ($cek_divisi == 0) {
        // $cek_karyawan = KaryawanJabatan::where('perjalanan_id', $id)->where('is_active', '1')->count();
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
            'table' => 'perjalanan',
            'activity' => 'delete',
            'description' => 'menghapus perjalanan inventaris ' . $perjalanan->nama_perjalanan,
            'keterangan' => 'Success',
        ]);

        $perjalanan->delete();
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
        //         'table' => 'perjalanan',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus perjalanan inventaris karena sudah memiliki relasi karyawan ' . $perjalanan->nama_perjalanan,
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
        //         'table' => 'perjalanan',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus perjalanan inventaris karena sudah memiliki relasi ' . $perjalanan->nama_perjalanan,
        //         'keterangan' => 'Failed',
        //     ]);
        //     return response()->json(['status' => 0]);
        // }
    }
}
