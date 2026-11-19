<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\InventarisKondisi;
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

class InventarisKondisiController extends Controller
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
        return view('admin.inventaris.kondisi_inventaris.index', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
        ]);
    }
    // public function ImportDepartemen(Request $request, $holding)
    // {
    //     $holding = Holding::where('holding_code', $holding)->first();
    //     Excel::import(new DepartemenImport, $request->file_excel);

    //     return redirect('/kondisi_inventaris/' . $holding)->with('success', 'Import Departemen Sukses');
    // }
    public function datatable(Request $request)
    {
        $table = InventarisKondisi::orderBy('nama_kondisi', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button id="btn_edit_kondisi" data-id="' . $row->id . '" data-kondisi="' . $row->nama_kondisi . '" data-deskripsi="' . $row->deskripsi . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_kondisi" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
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
                'nama_kondisi' => 'required|max:255',
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
            $id_kondisi = Uuid::uuid4();
            InventarisKondisi::create(
                [
                    'id' => $id_kondisi,
                    'nama_kondisi' => $request->nama_kondisi,
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
                'id_table' => $id_kondisi,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'kondisi_inventaris',
                'activity' => 'create',
                'description' => 'Menambahkan kondisi inventaris baru ' . $request->nama_kondisi,
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
                'nama_kondisi' => 'required|max:255',
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
            // $id_kondisi = Uuid::uuid4();
            InventarisKondisi::where('id', $request->id_kondisi)->update([
                'nama_kondisi' => $request->nama_kondisi,
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
                'id_table' => $request->id_kondisi,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'kondisi_inventaris',
                'activity' => 'update',
                'description' => 'mengedit nama kondisi inventaris ' . $request->nama_kondisi,
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
        // $cek_divisi = Divisi::where('kondisi_id', $id)->where('holding', $holding->id)->count();
        $kondisi_inventaris = InventarisKondisi::where('id', $id)->first();
        // if ($cek_divisi == 0) {
        // $cek_karyawan = KaryawanJabatan::where('kondisi_inventaris_id', $id)->where('is_active', '1')->count();
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
            'table' => 'kondisi_inventaris',
            'activity' => 'delete',
            'description' => 'menghapus kondisi inventaris ' . $kondisi_inventaris->nama_kondisi,
            'keterangan' => 'Success',
        ]);

        $kondisi_inventaris->delete();
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
        //         'table' => 'kondisi_inventaris',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus kondisi inventaris karena sudah memiliki relasi karyawan ' . $kondisi_inventaris->nama_kondisi,
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
        //         'table' => 'kondisi_inventaris',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus kondisi inventaris karena sudah memiliki relasi ' . $kondisi_inventaris->nama_kondisi,
        //         'keterangan' => 'Failed',
        //     ]);
        //     return response()->json(['status' => 0]);
        // }
    }
}
