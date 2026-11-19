<?php

namespace App\Http\Controllers;

use App\Imports\DivisiImport;
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

class DivisiController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $get_holding = Holding::where('holding_code', $holding)->first();
        // dd($get_holding);
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
        if ($get_holding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        // $get = Divisi::with('Departemen')->get();
        // dd($get);
        return view('admin.divisi.index', [
            'title' => 'Master Divisi',
            'holding' => $get_holding,
            'menus' => $menus,
            'data_divisi' => Divisi::with('Departemen')->where('holding', $get_holding->id)->get(),
            'data_departemen' => Departemen::orderBy('nama_departemen', 'asc')->where('holding', $get_holding->id)->get()
        ]);
    }
    public function ImportDivisi(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        Excel::import(new DivisiImport, $request->file_excel);

        return redirect('/divisi/' . $holding)->with('success', 'Import Divisi Sukses');
    }
    public function datatable(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Divisi::with(['Departemen' => function ($query) {
            $query->orderBy('nama_departemen', 'ASC');
        }])
            ->with(['Bagian' => function ($query) use ($holding) {
                $query->where('holding', $holding->id);
            }])
            ->where('holding', $holding->id)
            // ->orderBy('nama_divisi', 'ASC')
            ->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_departemen', function ($row) {
                    $nama_departemen = $row->Departemen->nama_departemen;
                    return $nama_departemen;
                })
                ->addColumn('jumlah_bagian', function ($row) use ($holding) {
                    $cek_bagian = $row->Bagian->count();
                    if ($cek_bagian == 0) {
                        $jumlah_bagian = $cek_bagian;
                    } else {
                        $jumlah_bagian = $cek_bagian . '&nbsp; <button id="btn_lihat_bagian" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-sm btn-outline-primary">
                    <span class="tf-icons mdi mdi-eye-circle-outline me-1"></span>Lihat
                  </button>';
                    }
                    return $jumlah_bagian;
                })
                ->addColumn('jumlah_bagian_excel', function ($row) use ($holding) {
                    $cek_bagian = $row->Bagian->count();
                    $jumlah_bagian = $cek_bagian;
                    return $jumlah_bagian;
                })
                ->addColumn('option', function ($row) use ($holding) {
                    $btn = '<button id="btn_edit_divisi" data-id="' . $row->id . '" data-dept="' . $row->dept_id . '" data-divisi="' . $row->nama_divisi . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_divisi" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns([
                    'nama_departemen',
                    'jumlah_bagian',
                    'jumlah_bagian_excel',
                    'option'
                ])
                ->make(true);
        }
    }
    public function bagian_datatable($id, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Bagian::where('divisi_id', $id)
            ->where('holding', $holding->id)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->make(true);
        }
    }
    public function create($holding, Request $request)
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
        return view('divisi.create', [
            'title' => 'Tambah Data Divisi',
            'holding' => $holding,
            'data_departemen' => Departemen::all(),
        ]);
    }

    public function insert(Request $request, $holding)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_departemen' => 'required|max:255',
                'nama_divisi' => 'required|max:255',
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
            $id_divsi = Uuid::uuid4();
            Divisi::create(
                [
                    'id' => $id_divsi,
                    'holding' => $holding->id,
                    'nama_divisi' => $request->nama_divisi,
                    'dept_id' => Departemen::where('id', $request->nama_departemen)->value('id'),
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $departemen = Departemen::where('id', $request->nama_departemen)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_divsi,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'divisi',
                'activity' => 'create',
                'description' => 'Menambahkan divisi baru ' . $request->nama_divisi . ' di departemen ' .  $departemen->nama_departemen,
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
                'nama_divisi' => 'required|max:255',
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
            Divisi::where('id', $request->id_divisi)->update(
                [
                    'nama_divisi' => $request->nama_divisi,
                    'dept_id' => $request->nama_departemen,
                ]
            );

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_divisi,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'divisi',
                'activity' => 'update',
                'description' => 'mengedit nama divisi ' . $request->nama_divisi,
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
        $cek_bagian = Bagian::where('divisi_id', $id)->where('holding', $holding->id)->count();
        $divisi = Divisi::where('id', $id)->first();
        $nama_menu = Menu::where('id', $menu_id)->first();
        $user_name = User::where('id', Auth::user()->id)->first();
        if ($cek_bagian == 0) {
            $cek_karyawan = KaryawanJabatan::where('divisi_id', $id)->where('is_active', '1')->count();
            if ($cek_karyawan == 0) {
                try {
                } catch (QueryException $e) {
                    Alert::error('Error', $e->getMessage());
                    return response()->json(['status' => 0]);
                }
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $id,
                    'holding_id' => $holding->id,
                    'holding_category' => $holding->holding_category,
                    'table' => 'divisi',
                    'activity' => 'delete',
                    'description' => 'menghapus divisi ' . $divisi->nama_divisi,
                    'keterangan' => 'Success',
                ]);

                $divisi->delete();
                return response()->json(['status' => 1]);
            } else {
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $id,
                    'holding_id' => $holding->id,
                    'holding_category' => $holding->holding_category,
                    'table' => 'divisi',
                    'activity' => 'delete',
                    'description' => 'Gagal menghapus divisi karena sudah memiliki relasi karyawan ' . $divisi->nama_divisi,
                    'keterangan' => 'Failed',
                ]);
                return response()->json(['status' => 2]);
            }
        } else {
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'divisi',
                'activity' => 'delete',
                'description' => 'Gagal menghapus divisi karena sudah memiliki relasi bagian ' . $divisi->nama_divisi,
                'keterangan' => 'Failed',
            ]);
            return response()->json(['status' => 0]);
        }
    }
}
