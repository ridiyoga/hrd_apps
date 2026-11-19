<?php

namespace App\Http\Controllers;

use App\Imports\BagianImport;
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

class BagianController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.bagian.index', [
            'title' => 'Master Divisi',
            'holding' => $holding,
            'menus' => $menus,
            'data_bagian' => Bagian::with('Divisi')->where('holding', $holding->id)->get(),
            'data_dept' => Departemen::orderBy('nama_departemen', 'asc')->where('holding', $holding->id)->get(),
            'data_divisi' => Divisi::orderBy('nama_divisi', 'asc')->where('holding', $holding->id)->get()
        ]);
    }
    public function ImportBagian(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        Excel::import(new BagianImport, $request->file_excel);

        return redirect('/bagian/' . $holding->holding_code)->with('success', 'Import Bagian Sukses');
    }
    public function get_divisi($id, $holding)
    {
        $id_holding = Holding::where('id', $holding)->value('id');
        // dd($id_holding);
        $get_divisi = Divisi::where('dept_id', $id)->where('holding', $id_holding)->orderBy('nama_divisi', 'ASC')->get();
        // dd($get_divisi);
        echo "<option value=''>Pilih Divisi...</option>";
        foreach ($get_divisi as $divisi) {
            echo "<option value='$divisi->id'>$divisi->nama_divisi</option>";
        }
    }
    public function datatable(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Bagian::with([
            'Divisi' =>  function ($query) {
                $query->with(['Departemen' => function ($query) {
                    $query->orderBy('nama_departemen', 'ASC');
                }]);
                $query->orderBy('nama_divisi', 'ASC');
            },
        ])
            ->where('holding', $holding->id)
            ->orderBy('nama_bagian', 'ASC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_departemen', function ($row) {
                    if ($row->Divisi == NULL) {
                        $nama_departemen = NULL;
                    } else {
                        $nama_departemen = $row->Divisi->Departemen->nama_departemen;
                    }
                    return $nama_departemen;
                })
                ->addColumn('nama_divisi', function ($row) {
                    if ($row->Divisi == NULL) {
                        $nama_divisi = NULL;
                    } else {
                        $nama_divisi = $row->Divisi->nama_divisi;
                    }
                    return $nama_divisi;
                })
                ->addColumn('jumlah_jabatan', function ($row) use ($holding) {
                    $cek_jabatan = Jabatan::where('bagian_id', $row->id)
                        ->where('divisi_id', $row->divisi_id)
                        ->where('holding', $holding->id)
                        ->count();
                    if ($cek_jabatan == 0) {
                        $jumlah_jabatan = $cek_jabatan;
                    } else {
                        $jumlah_jabatan = $cek_jabatan . '&nbsp; <button id="btn_lihat_jabatan" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-sm btn-outline-primary">
                    <span class="tf-icons mdi mdi-eye-circle-outline me-1"></span>Lihat
                  </button>';
                    }
                    return $jumlah_jabatan;
                })
                ->addColumn('jumlah_jabatan_excel', function ($row) use ($holding) {
                    $cek_jabatan = Jabatan::where('bagian_id', $row->id)
                        ->where('divisi_id', $row->divisi_id)
                        ->where('holding', $holding->id)
                        ->count();
                    $jumlah_jabatan = $cek_jabatan;

                    return $jumlah_jabatan;
                })

                ->addColumn('option', function ($row) use ($holding) {
                    $btn = '<button id="btn_edit_bagian" data-id="' . $row->id . '" data-dept="' . $row->Divisi->Departemen->id . '" data-divisi="' . $row->divisi_id . '" data-bagian="' . $row->nama_bagian . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_bagian" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '"  data-divisi="' . $row->divisi_id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';

                    return $btn;
                })
                ->rawColumns([
                    'nama_departemen',
                    'nama_divisi',
                    'jumlah_jabatan',
                    'jumlah_jabatan_excel',
                    'option'
                ])
                ->make(true);
        }
    }
    public function jabatan_datatable($id, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Jabatan::where('bagian_id', $id)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)

                ->make(true);
        }
    }
    public function karyawanjabatan_datatable($id, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =   Karyawan::leftJoin('users as b', 'b.karyawan_id', 'karyawans.id')
            ->where('status_aktif', 'AKTIF')
            ->where('bagian_id', $id)
            ->orWhere('bagian1_id', $id)
            ->orWhere('bagian2_id', $id)
            // ->orWhere('bagian3_id', $id)
            // ->orWhere('bagian4_id', $id)
            ->where('b.is_admin', 'user')
            ->where('kontrak_kerja', $holding->id)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_jabatan', function ($row) use ($holding) {
                    $jabatan = Jabatan::where('holding', $holding->id)->where('id', $row->jabatan_id)->value('nama_jabatan');
                    return $jabatan;
                })
                ->rawColumns(['nama_jabatan'])
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
        return view('bagian.create', [
            'title' => 'Tambah Data Divisi',
            'holding' => $holding,
            'data_divisi' => Divisi::all(),
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
                'nama_bagian' => 'required|max:255',
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
            $id_bagian = Uuid::uuid4();
            Bagian::create(
                [
                    'id' => $id_bagian,
                    'holding' => $holding->id,
                    'nama_bagian' => $request->nama_bagian,
                    'divisi_id' => Divisi::where('id', $request->nama_divisi)->value('id'),
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $divisi = Divisi::where('id', $request->nama_divisi)->first();
            $departemen = Departemen::where('id', $request->nama_departemen)->first();

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_bagian,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'bagian',
                'activity' => 'create',
                'description' => 'Menambahkan divisi baru ' . $request->nama_bagian . ' di departemen ' .  $divisi->nama_divisi . ' departemen ' . $departemen->nama_departemen,
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
                'nama_divisi' => 'required|max:255',
                'nama_bagian' => 'required|max:255',
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
            Bagian::where('id', $request->id_bagian)->update(
                [
                    'holding' => $holding->id,
                    'nama_bagian' => $request->nama_bagian,
                    'divisi_id' => Divisi::where('id', $request->nama_divisi)->value('id'),
                ]
            );

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_bagian,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'bagian',
                'activity' => 'update',
                'description' => 'mengedit nama bagian ' . $request->nama_bagian,
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

    public function get_bagian($id)
    {
        // dd($id);
        $get_bagian = Bagian::where('divisi_id', $id)->orderBy('nama_bagian', 'ASC')->get();
        echo "<option value=''>Pilih Bagian...</option>";
        foreach ($get_bagian as $bagian) {
            echo "<option value='$bagian->id'>$bagian->nama_bagian</option>";
        }
    }

    public function delete(Request $request, $id, $holding, $menu_id)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        $bagian = Bagian::where('id', $id)->first();
        $nama_menu = Menu::where('id', $menu_id)->first();
        $user_name = User::where('id', Auth::user()->id)->first();
        $cek_jabatan = Jabatan::where('bagian_id', $id)
            ->where('divisi_id', $request->divisi)
            ->where('holding', $request->holding)
            ->count();
        // dd($cek_jabatan);
        if ($cek_jabatan == 0) {
            $cek_karyawan = KaryawanJabatan::where('bagian_id', $id)->where('is_active', '1')->count();
            if ($cek_karyawan == 0) {
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $id,
                    'holding_id' => $holding->id,
                    'holding_category' => $holding->holding_category,
                    'table' => 'bagian',
                    'activity' => 'delete',
                    'description' => 'menghapus bagian ' . $bagian->nama_bagian,
                    'keterangan' => 'Success',
                ]);
                $bagian->delete();
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
                    'table' => 'bagian',
                    'activity' => 'delete',
                    'description' => 'Gagal menghapus bagian karena sudah memiliki relasi karyawan ' . $bagian->nama_divisi,
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
                'table' => 'bagian',
                'activity' => 'delete',
                'description' => 'Gagal menghapus bagian karena sudah memiliki relasi bagian ' . $bagian->nama_divisi,
                'keterangan' => 'Failed',
            ]);
            return response()->json(['status' => 0]);
        }
    }
}
