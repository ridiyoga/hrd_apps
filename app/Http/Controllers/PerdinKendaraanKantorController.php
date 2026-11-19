<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\InventarisKategori;
use App\Models\Karyawan;
use App\Models\PerdinKendaraanKantor;
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

class PerdinKendaraanKantorController extends Controller
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
        $kategori = InventarisKategori::orderBy('nama_kategori')->get();
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        return view('admin.perdin.karyawan_kantor', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
            'kategori' => $kategori,
        ]);
    }
    // public function ImportDepartemen(Request $request, $holding)
    // {
    //     $holding = Holding::where('holding_code', $holding)->first();
    //     Excel::import(new DepartemenImport, $request->file_excel);

    //     return redirect('/karyawan_umum/' . $holding)->with('success', 'Import Departemen Sukses');
    // }
    public function datatable(Request $request)
    {
        $table = Karyawan::orderBy('nama_kategori', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button id="btn_edit_karyawan" data-id="' . e($row->id) . '" data-id_karyawan="' . e($row->id_karyawan) . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_karyawan" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
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

    public function edit($id, $holding)
    {
        if (!Holding::where('holding_code', $holding)->exists()) {
            return response()->json([
                'code' => 404,
                'message' => 'Holding tidak ditemukan.',
            ], 404);
        }

        $karyawan = Karyawan::find($id);
        if (!$karyawan) {
            return response()->json([
                'code' => 404,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'data' => $karyawan,
        ]);
    }

    public function insert(Request $request, $holding)
    {
        $holding  = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'id_karyawan' => 'required|exists:inventaris_kategori,id',
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
            $kategori = InventarisKategori::where('id', $request->id_karyawan)->first();
            $id_karyawan = Uuid::uuid4();
            Karyawan::create(
                [
                    'id' => $id_karyawan,
                    'id_karyawan' => $kategori->id,
                    'nama_kategori' => $kategori->nama_kategori,
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
                'id_table' => $id_karyawan,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'perdin_karyawan_umum',
                'activity' => 'create',
                'description' => 'Menambahkan karyawan umum baru ' . $kategori->nama_kategori,
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
                'id_karyawan' => 'required|exists:perdin_karyawan_kantor,id',
                'id_karyawan' => 'required|exists:inventaris_kategori,id',
                'deskripsi' => 'nullable|string',
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
            $kategori = InventarisKategori::where('id', $request->id_karyawan)->first();
            Karyawan::where('id', $request->id_karyawan)->update([
                'id_karyawan' => $kategori->id,
                'nama_kategori' => $kategori->nama_kategori,
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
                'id_table' => $request->id_karyawan,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'perdin_karyawan_umum',
                'activity' => 'update',
                'description' => 'mengedit nama karyawan umum ' . $kategori->nama_kategori,
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
        $holding = Holding::where('holding_code', $holding)->first();
        $karyawan_umum = Karyawan::find($id);
        if (!$holding || !$karyawan_umum) {
            return response()->json([
                'status' => 0,
                'message' => 'Data karyawan atau holding tidak ditemukan.',
            ], 404);
        }

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
            'table' => 'perdin_karyawan_umum',
            'activity' => 'delete',
            'description' => 'menghapus karyawan umum ' . $karyawan_umum->nama_kategori,
            'keterangan' => 'Success',
        ]);

        $karyawan_umum->delete();
        return response()->json(['status' => 1]);
    }
}
