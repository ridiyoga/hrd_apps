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
use App\Models\InventarisGedung;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\InventarisRuangan;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class InventarisGedungController extends Controller
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
        $site = Site::orderBy('site_name')->get();
        return view('admin.inventaris.gedung.index', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
            'site' => $site,
            'data_kategori_gedung' => Departemen::all()
        ]);
    }
    // public function ImportDepartemen(Request $request, $holding)
    // {
    //     $holding = Holding::where('holding_code', $holding)->first();
    //     Excel::import(new DepartemenImport, $request->file_excel);

    //     return redirect('/kategori_gedung/' . $holding)->with('success', 'Import Departemen Sukses');
    // }
    public function datatable(Request $request)
    {
        $table = InventarisGedung::orderBy('nama_gedung', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button id="btn_edit_kategori" data-id="' . $row->id . '" data-kategori="' . $row->nama_gedung . '" data-deskripsi="' . $row->deskripsi . '"  data-id_site="' . $row->id_site . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
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
                'nama_gedung' => 'required|max:255',
                'id_site' => 'required',
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
            $site = Site::where('id', $request->id_site)->first();
            $holding = Holding::where('id', $site->site_holding_category)->first();
            // dd($holding);
            $id_gedung = Uuid::uuid4();
            InventarisGedung::create(
                [
                    'id' => $id_gedung,
                    'nama_gedung' => $request->nama_gedung,
                    'deskripsi' => $request->deskripsi,
                    'id_site' => $request->id_site,
                    'nama_site' => $site->site_name,
                    'id_holding' => $site->site_holding_category,
                    'nama_holding' => $holding->holding_name,
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
                'id_table' => $id_gedung,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'kategori_gedung',
                'activity' => 'create',
                'description' => 'Menambahkan kategori gedung baru ' . $request->nama_gedung,
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
                'nama_gedung' => 'required|max:255',
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
            $site = Site::where('id', $request->id_site)->first();
            $holding = Holding::where('id', $site->site_holding_category)->first();
            InventarisGedung::where('id', $request->id_gedung)->update([
                'nama_gedung' => $request->nama_gedung,
                'deskripsi' => $request->deskripsi,
                'id_site' => $request->id_site,
                'nama_site' => $site->site_name,
                'id_holding' => $site->site_holding_category,
                'nama_holding' => $holding->holding_name,
                'created_at' => now()
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
                'table' => 'kategori_gedung',
                'activity' => 'update',
                'description' => 'mengedit nama kategori gedung ' . $request->nama_gedung,
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
        $kategori_gedung = InventarisGedung::where('id', $id)->first();
        $cek_ruangan = InventarisRuangan::where('id_gedung', $id)->count();
        // dd($cek_ruangan);
        if ($cek_ruangan == 0) {
            // $cek_karyawan = KaryawanJabatan::where('kategori_gedung_id', $id)->where('is_active', '1')->count();
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
                'table' => 'kategori_gedung',
                'activity' => 'delete',
                'description' => 'menghapus kategori gedung ' . $kategori_gedung->nama_gedung,
                'keterangan' => 'Success',
            ]);

            $kategori_gedung->delete();
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
            //         'table' => 'kategori_gedung',
            //         'activity' => 'delete',
            //         'description' => 'Gagal menghapus kategori gedung karena sudah memiliki relasi karyawan ' . $kategori_gedung->nama_gedung,
            //         'keterangan' => 'Failed',
            //     ]);
            //     return response()->json(['status' => 2]);
            // }
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
                'table' => 'kategori_gedung',
                'activity' => 'delete',
                'description' => 'Gagal menghapus kategori gedung karena sudah memiliki relasi ' . $kategori_gedung->nama_gedung,
                'keterangan' => 'Failed',
            ]);
            return response()->json(['status' => 0]);
        }
    }
}
