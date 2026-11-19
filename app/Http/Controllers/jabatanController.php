<?php

namespace App\Http\Controllers;

use App\Imports\JabatanImport;
use App\Models\ActivityLog;
use App\Models\Bagian;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\JabatanMaster;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\LevelJabatan;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;



class jabatanController extends Controller
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
        $data_divisi = Divisi::with(['Departemen' => function ($query) {
            $query->orderBy('nama_departemen', 'ASC');
        }])->with(['Jabatan' => function ($query) use ($holding) {
            $query->where('holding', $holding->id);
        }])->where('holding', $holding->id)
            ->orderBy('nama_divisi', 'ASC')
            ->get();
        // dd($data_divisi);
        $data_bagian = Bagian::orderBy('nama_bagian', 'ASC')
            ->where('holding', $holding->id)
            ->get();
        $data_jabatan = Jabatan::with('Bagian')
            ->with('LevelJabatan')
            ->where('holding', $holding->id)->get();

        $level = LevelJabatan::orderBy('level_jabatan', 'ASC')->get();
        // dd($level);
        // dd($holding);
        return view('admin.jabatan.index', [
            'title' => 'Master Jabatan',
            'holding' => $holding,
            'menus' => $menus,
            'data_jabatan' => $data_jabatan,
            'data_divisi' => $data_divisi,
            'data_bagian' => $data_bagian,
            'get_level' => $level,
        ]);
    }
    public function get_jabatan($id)
    {
        // dd($id);
        $get_jabatan = Jabatan::where('bagian_id', $id)->get();
        echo "<option value=''>Pilih Jabatan...</option>";
        foreach ($get_jabatan as $jabatan) {
            echo "<option value='$jabatan->id'>$jabatan->nama_jabatan</option>";
        }
    }
    public function ImportJabatan(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        Excel::import(new JabatanImport, $request->file_excel);

        return redirect('/jabatan/' . $holding)->with('success', 'Import Jabatan Sukses');
    }
    public function detail_jabatan($id, $holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        $data_divisi = Divisi::with(['Departemen' => function ($query) {
            $query->orderBy('nama_departemen', 'ASC');
        }])->with('Jabatan')
            ->where('holding', $holding->id)->get();
        $divisi = Divisi::with(['Departemen' => function ($query) use ($holding) {
            $query->where('holding', $holding->id);
            $query->orderBy('nama_departemen', 'ASC');
        }])->where('id', $id)
            ->where('holding', $holding->id)
            ->first();

        $data_jabatan = Divisi::with(['Jabatan' => function ($query) use ($id) {
            $query->where('id', $id);
            $query->orderBy('nama_jabatan', 'ASC');
        }])->with(['Departemen' => function ($query) {
            $query->orderBy('nama_departemen', 'ASC');
        }])->where('holding', $holding->id)
            ->get();
        $data_bagian = Bagian::Join('divisis', 'divisis.id', 'bagians.divisi_id')
            ->where('divisis.id', $id)
            ->orderBy('nama_bagian', 'ASC')
            ->where('bagians.holding', $holding->id)
            ->select('bagians.*', 'divisis.nama_divisi')
            ->get();
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
        $jabatan_master = JabatanMaster::orderBy('jabatan_name', 'asc')->get();
        $level = LevelJabatan::orderBy('level_jabatan', 'ASC')->get();
        return view('admin.jabatan.detail_jabatan', [
            'title' => 'Master Jabatan',
            'holding' => $holding,
            'menus' => $menus,
            'data_divisi' => $data_divisi,
            'divisi' => $divisi,
            'data_jabatan' => $data_jabatan,
            'data_bagian' => $data_bagian,
            'jabatan_master' => $jabatan_master,
            'get_level' => $level,
        ]);
    }
    public function datatable(Request $request, $id, $holding)
    {
        // dd($id);
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Jabatan::Join('divisis', 'divisis.id', 'jabatans.divisi_id')
            ->Join('bagians', 'bagians.id', 'jabatans.bagian_id')
            ->where('jabatans.divisi_id', $id)
            ->where('jabatans.holding', $holding->id)
            ->orderBy('jabatans.nama_jabatan', 'ASC')
            ->select('jabatans.*', 'bagians.nama_bagian', 'divisis.nama_divisi', 'divisis.dept_id')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_atasan', function ($row) use ($holding) {
                    $get_atasan = Jabatan::With('Bagian')->where('holding', $holding->id)->where('id', $row->atasan_id)->first();
                    if ($get_atasan == NULL || $get_atasan == '') {
                        $atasan = NULL;
                    } else {
                        if ($get_atasan->Bagian == NULL) {

                            $atasan  = $get_atasan->nama_jabatan;
                        } else {
                            $atasan = $get_atasan->nama_jabatan . ' (' . $get_atasan->Bagian->nama_bagian . ')';
                        }
                    }
                    return $atasan;
                })
                ->addColumn('jumlah_karyawan', function ($row) use ($holding) {
                    //     $karyawan = Karyawan::leftJoin('users as b', 'b.karyawan_id', 'karyawans.id')
                    //         ->where('status_aktif', 'AKTIF')
                    //         ->where('jabatan_id', $row->id)
                    //         ->orWhere('jabatan1_id', $row->id)
                    //         ->orWhere('jabatan2_id', $row->id)
                    //         // ->orWhere('jabatan3_id', $row->id)
                    //         // ->orWhere('jabatan4_id', $row->id)
                    //         ->where('b.is_admin', 'user')
                    //         ->count();
                    //     if ($karyawan == 0) {
                    //         $karyawan = $karyawan;
                    //     } else {
                    //         $karyawan = $karyawan . '&nbsp; <button id="btn_lihat_karyawan" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" type="button" class="btn btn-sm btn-outline-primary">
                    //     <span class="tf-icons mdi mdi-eye-circle-outline me-1"></span>Lihat
                    //   </button>';
                    //     }
                    // return $karyawan;
                    return '-';
                })
                ->addColumn('option', function ($row) use ($holding) {
                    $btn = '<button id="btn_edit_jabatan" 
                            data-id="' . $row->id . '" 
                            data-jabatan="' . $row->nama_jabatan . '" 
                            data-bagian="' . $row->bagian_id . '" 
                            data-divisi="' . $row->Divisi->nama_divisi . '" 
                            type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_jabatan" data-id="' . $row->id . '" data-holding="' . $holding->holding_code . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['jabatan_atasan', 'jumlah_bawahan', 'lintas_departemen', 'nama_bagian', 'jumlah_karyawan', 'option'])
                ->make(true);
        }
    }
    public function karyawan_datatable(Request $request, $id, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =   Karyawan::leftJoin('users as b', 'b.karyawan_id', 'karyawans.id')
            ->where('status_aktif', 'AKTIF')
            ->where('jabatan_id', $id)
            ->orWhere('jabatan1_id', $id)
            ->orWhere('jabatan2_id', $id)
            // ->orWhere('jabatan3_id', $id)
            // ->orWhere('jabatan4_id', $id)
            ->where('b.is_admin', 'user')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_jabatan', function ($row) use ($holding, $id) {
                    $jabatan = Jabatan::where('holding', $holding->id)->where('id', $id)->value('nama_jabatan');

                    return $jabatan;
                })
                ->addColumn('nama_bagian', function ($row) use ($holding, $id) {
                    // $bagian = NULL;
                    $bagian = Jabatan::With('Bagian')->where('holding', $holding->id)->where('id', $id)->first();
                    if ($bagian->Bagian == NULL) {
                        $bagian = NULL;
                    } else {
                        $bagian = $bagian->Bagian->nama_bagian;
                    }
                    return $bagian;
                })
                ->rawColumns(['nama_jabatan', 'nama_bagian',])
                ->make(true);
        }
    }
    public function get_bagian($id)
    {
        // dd($id);
        $get_bagian = Bagian::where('divisi_id', $id)->get();
        // dd($get_bagian);
        echo "<option value=''>Pilih Bagian...</option>";
        foreach ($get_bagian as $bagian) {
            echo "<option value='$bagian->id'>$bagian->nama_bagian</option>";
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
        return view('jabatan.create', [
            'title' => 'Tambah Data Jabatan',
            'holding' => $holding,
            'menus' => $menus,
            'get_bagian' => Bagian::get(),
            'get_level' => LevelJabatan::orderBy('level_jabatan', 'ASC')->get(),
        ]);
    }

    public function insert(Request $request, $holding)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_divisi' => 'required',
                'nama_jabatan' => 'required|max:255',
                'nama_bagian' => 'required',
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
            // dd($request->all());
            $divisi = Divisi::where('id', $request->nama_divisi)->where('holding', $holding->id)->first();
            $bagian = Bagian::where('id', $request->nama_bagian)->where('holding', $holding->id)->first();
            $jabatan = JabatanMaster::where('id', $request->nama_jabatan)->first();
            Jabatan::create(
                [
                    'holding' => $holding->id,
                    'divisi_id' => $divisi->id,
                    'bagian_id' => $bagian->id,
                    'nama_jabatan' => $jabatan->jabatan_name,
                    'level_id' => $jabatan->level_id,
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'jabatan',
                'activity' => 'create',
                'description' => 'menambah jabatan ' . $jabatan->jabatan_name . ' divisi ' . $divisi->nama_divisi,
                'keterangan' => 'Success',
            ]);
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_bagian' => 'required',
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
            // dd($request->all());
            Jabatan::where('id', $request->id)->update(
                [
                    'bagian_id' => $request->nama_bagian,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $jabatan = Jabatan::where('id', $request->id)->first();
            $holding = Holding::where('id', $jabatan->holding)->first();
            $bagian = Bagian::where('id', $jabatan->bagian_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $jabatan->holding,
                'holding_category' => $holding->holding_category,
                'table' => 'jabatan',
                'activity' => 'update',
                'description' => 'mengedit jabatan ' . $jabatan->nama_jabatan . ' bagian ' . $bagian->nama_divisi,
                'keterangan' => 'Success',
            ]);
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function delete(Request $request)
    {
        // dd($request->all());
        $cek_karyawan_jabatan = KaryawanJabatan::where('jabatan_id', $request->id)->count();
        // dd($cek_karyawan_jabatan);
        $delete = Jabatan::where('id', $request->id)->first();
        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        $holding = Holding::where('id', $delete->holding)->first();
        try {
            // dd($cek_karyawan_jabatan);
            if ($cek_karyawan_jabatan == 0) {
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $delete->holding,
                    'holding_category' => $holding->holding_category,
                    'table' => 'jabatan',
                    'activity' => 'delete',
                    'description' => 'menghapus jabatan ' . $delete->nama_jabatan,
                    'keterangan' => 'Success',
                ]);
                $delete->delete();
                Jabatan::where('id', $request->id)->delete();
                return response()->json([
                    'code' => 200,
                    'message' => 'Data Berhasil Diupdate'
                ]);
            } else {
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $holding = Holding::where('id', $delete->holding)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $delete->holding,
                    'holding_category' => $holding->holding_category,
                    'table' => 'jabatan',
                    'activity' => 'delete',
                    'description' => 'menghapus jabatan gagal karena masih ada relasi karyawan ' . $delete->nama_jabatan,
                    'keterangan' => 'Failed',
                ]);
                return response()->json([
                    'code' => 500,
                    'error' => 'Kosongkan Jabatan Karyawan terlebih dahulu',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
        // return redirect('/jabatan/' . $holding)->with('success', 'Data Berhasil di Delete');
    }
    public function jabatan_master_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nama_jabatan' => 'required',
                'level_id' => 'required',
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
            $riwayat = new JabatanMaster();
            $riwayat->id = Uuid::uuid4();
            $riwayat->jabatan_name = $request->nama_jabatan;
            $riwayat->level_id = $request->level_id;
            $riwayat->created_at = date('Y-m-d H:i:s');
            $riwayat->save();
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $level = LevelJabatan::where('id', $request->level_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'divisi',
                'activity' => 'create',
                'description' => 'menambah master jabatan ' . $request->nama_jabatan . ' dengan level ' . $level->level_jabatan,
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function jabatan_master_dt()
    {
        $table =  JabatanMaster::with([
            'LevelJabatan' =>  function ($query) {
                $query;
            },
        ])
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)

                ->addColumn('level_name', function ($row) {

                    return $row->LevelJabatan->level_name ?? '-';
                })
                ->addColumn('level_jabatan', function ($row) {

                    return $row->LevelJabatan->level_jabatan ?? '-';
                })
                ->addColumn('option', function ($row) {

                    $btn = '<button id="btn_edit_jabatan" 
                    data-id="' . $row->id . '"
                    data-jabatan_name="' . $row->jabatan_name . '"
                    data-level_id="' . $row->level_id . '"
                    type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_jabatan" 
                    data-id="' . $row->id . '" 
                    class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['option'])
                ->make(true);
        }
    }
    public function jabatan_master_update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nama_jabatan' => 'required',
                'level_id' => 'required',
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
            $riwayat = JabatanMaster::where('id', $request->id)->first();
            $riwayat->jabatan_name = $request->nama_jabatan;
            $riwayat->level_id = $request->level_id;
            $riwayat->updated_at = date('Y-m-d H:i:s');
            $riwayat->update();


            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $level = LevelJabatan::where('id', $request->level_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'divisi',
                'activity' => 'update',
                'description' => 'mengedit master jabatan ' . $request->nama_jabatan . ' dengan level ' . $level->level_jabatan,
                'keterangan' => 'Success',
            ]);
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function jabatan_master_delete(Request $request)
    {
        try {
            // dd($request->id_riwayat);
            $delete = JabatanMaster::where('id', $request->id)->first();

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'divisi',
                'activity' => 'update',
                'description' => 'menghapus master jabatan ' . $delete->nama_jabatan,
                'keterangan' => 'Success',
            ]);
            $delete->delete();
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
