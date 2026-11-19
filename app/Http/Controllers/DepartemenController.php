<?php

namespace App\Http\Controllers;

use App\Imports\DepartemenImport;
use App\Models\ActivityLog;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
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

class DepartemenController extends Controller
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
        return view('admin.departemen.index', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
            'data_departemen' => Departemen::all()
        ]);
    }
    public function ImportDepartemen(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        Excel::import(new DepartemenImport, $request->file_excel);

        return redirect('/departemen/' . $holding)->with('success', 'Import Departemen Sukses');
    }
    public function datatable(Request $request, $holding)
    {
        $holding  = Holding::where('holding_code', $holding)->first();
        $table = Departemen::where('holding', $holding->id)
            ->orderBy('nama_departemen', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jumlah_divisi', function ($row) use ($holding) {
                    $cek_divisi = Divisi::where('dept_id', $row->id)->where('holding', $holding->id)->count();
                    if ($cek_divisi == 0) {
                        $jumlah_divisi = $cek_divisi;
                    } else {
                        $jumlah_divisi = $cek_divisi;

                        //         $jumlah_divisi = $cek_divisi . '&nbsp; <button id="btn_lihat_divisi" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-sm btn-outline-primary">
                        //     <span class="tf-icons mdi mdi-eye-circle-outline me-1"></span>Lihat
                        //   </button>';
                    }
                    return $jumlah_divisi;
                })
                ->addColumn('option', function ($row) use ($holding) {
                    $btn = '<button id="btn_edit_dept" data-id="' . $row->id . '" data-dept="' . $row->nama_departemen . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_dept" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['option', 'jumlah_divisi'])
                ->make(true);
        }
    }
    public function divisi_datatable($id, $holding)
    {
        // dd($holding);
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Divisi::with(['Karyawan' => function ($query) use ($holding) {
            $query->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $holding->id);
        }])->where('dept_id', $id)
            ->where('holding', $holding->id)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
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
                'nama_departemen' => 'required|max:255',
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
            $id_dept = Uuid::uuid4();
            Departemen::create(
                [
                    'id' => $id_dept,
                    'holding' => $holding->id,
                    'nama_departemen' => $request->nama_departemen,
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_dept,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'departemen',
                'activity' => 'create',
                'description' => 'Menambahkan departemen baru ' . $request->nama_departemen,
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
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_departemen' => 'required|max:255',
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
            $id_dept = Uuid::uuid4();
            Departemen::where('id', $request->id_departemen)->update([
                'nama_departemen' => $request->nama_departemen,
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_dept,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'departemen',
                'activity' => 'update',
                'description' => 'mengedit nama departemen ' . $request->nama_departemen,
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
        $cek_divisi = Divisi::where('dept_id', $id)->where('holding', $holding->id)->count();
        $departemen = Departemen::where('id', $id)->first();
        if ($cek_divisi == 0) {
            $cek_karyawan = KaryawanJabatan::where('departemen_id', $id)->where('is_active', '1')->count();
            if ($cek_karyawan == 0) {
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
                    'table' => 'departemen',
                    'activity' => 'delete',
                    'description' => 'menghapus departemen ' . $departemen->nama_departemen,
                    'keterangan' => 'Success',
                ]);

                $departemen->delete();
                return response()->json(['status' => 1]);
            } else {
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
                    'table' => 'departemen',
                    'activity' => 'delete',
                    'description' => 'Gagal menghapus departemen karena sudah memiliki relasi karyawan ' . $departemen->nama_departemen,
                    'keterangan' => 'Failed',
                ]);
                return response()->json(['status' => 2]);
            }
        } else {
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
                'table' => 'departemen',
                'activity' => 'delete',
                'description' => 'Gagal menghapus departemen karena sudah memiliki relasi divisi ' . $departemen->nama_departemen,
                'keterangan' => 'Failed',
            ]);
            return response()->json(['status' => 0]);
        }
    }
}
