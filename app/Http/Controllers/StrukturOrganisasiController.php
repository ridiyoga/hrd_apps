<?php

namespace App\Http\Controllers;

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
use App\Models\StrukturModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

class StrukturOrganisasiController extends Controller
{

    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
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
        // $coba = Karyawan::select('karyawans.*', 'b.nama_jabatan')
        //     ->join('jabatans as b', 'karyawans.jabatan_id', 'b.id')
        //     ->get();
        $jabatan = Jabatan::with('LevelJabatan')->where('holding', $holdings->id)->with('Holding')
            ->get()
            ->sortBy(function ($jabatan) {
                return $jabatan->LevelJabatan->level_jabatan;
            });
        $data_dept = Departemen::orderBy('nama_departemen', 'ASC')->where('holding', $holdings->id)->with('Holding')->get();
        // dd($data_dept);
        $struktur = StrukturModel::where('holding', $holdings->id)->count();
        // dd($struktur);
        return view('admin.struktur_organisasi.index', [
            'holding' => $holdings,
            'jabatan' => $jabatan,
            'data_dept' => $data_dept,
            'struktur' => $struktur,
            'menus' => $menus
        ]);
    }
    public function struktur_get_parent($id, $holding_id)
    {
        // dd('asoy');
        $struktur_parent = StrukturModel::where('id', $id)->with('Parent.Jabatan.Bagian')->with('Parent.Jabatan.Divisi.Departemen')->first();
        $struktur_get = StrukturModel::where('holding', $holding_id)->with('Jabatan.Bagian')->with('Jabatan.Divisi.Departemen')->get();
        return response()->json([
            'code' => 200,
            'struktur_parent' => $struktur_parent,
            'struktur_get' => $struktur_get,
            'message' => 'Data Berhasil Disimpan'
        ]);
    }
    public function get_tree_struktur($holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $struktur = StrukturModel::with('Parent')
            ->with('Jabatan.LevelJabatan')
            ->with(['UserKaryawan' => function ($query) {
                $query->whereHas('Karyawan', function ($q) {
                    $q->where('status_aktif', 'AKTIF');
                })->with('Karyawan');
            }])
            // ->where('id', 'be3f60d2-b985-4eaa-97ff-7ffd42b9e701')
            ->where('holding', $holdings->id)
            ->whereNull('parent')
            ->get()
            // dd($struktur);
            ->sortBy(function ($struktur) {
                return $struktur->Jabatan->LevelJabatan->level_jabatantida;
            });

        return response()->json(
            $this->map($struktur)

            // $data_approval
        );
    }
    private function map($struktur)
    {
        // dd($jabatan, ' ok');
        if (is_null($struktur)) {
            return 0;
        }
        return $struktur->map(function ($l) {
            $namaJabatan = optional($l->Jabatan)->nama_jabatan ?? '-';
            $namaBagian  = optional($l->Jabatan?->Bagian)->nama_bagian ?? '-';
            $holdingCat  = optional($l->Jabatan?->Holding)->holding_category ?? '-';
            $status_pegawai  = optional($l)->status_pegawai ?? '';
            if ($status_pegawai == 1) {
                $outs = '<button type="button" class="badge bg-label-primary border-0 mb-1">Outsourching</button>';
            } else {
                $outs = '<button type="button" class="badge bg-label-success border-0 mb-1">PKWT & Organik</button>';
            }
            return [
                'id' => $l->id,
                'id_jabatan' => $l->jabatan,

                'title' => '
                    <i class="mdi mdi-account mdi-24px text-primary"></i>
                    <span class="mb-1">' . e($namaJabatan) . '</span>
                    <span>' . $outs . '</div></span>
                ',

                'name' => '
                    <div>
                        <p style="margin:0;">' . e($namaJabatan) . '</p>
                        <p style="margin:0;">(' . e($namaBagian) . ')</p>
                        <p style="margin:0;">(' . e($holdingCat) . ')</p>
                    </div>
                ',

                'kuota' => '<span>Kuota : ' . ($l->kuota_karyawan ?? 0) . ' Orang</span>',
                'kuota_karyawan' => $l->kuota_karyawan ?? 0,
                'jabatan' => $namaJabatan,
                'status_pegawai' => $status_pegawai,

                'children' => $this->map($l->Children),

                'UserKaryawan' => '
                    <button type="button" class="badge bg-label-info border-0 mb-1">
                        Jumlah Karyawan : ' . $l->UserKaryawan->pluck('Karyawan')->filter()->count() . ' Orang
                    </button>
                ',
            ];
        });
    }
    public function struktur_store(Request $request, $holding_id)
    {
        // dd($request->all(), $holding_id);
        $validator = Validator::make(
            $request->all(),
            [
                'jabatan' => 'required',
                'kuota_bawahan' => 'required',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );


        try {
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ]);
            }
            $get_id = Uuid::uuid4();
            StrukturModel::insert([
                'id' => $get_id,
                'jabatan' => $request->jabatan,
                'kuota_karyawan' => $request->kuota_atasan,
                'holding' => $holding_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            StrukturModel::insert([
                'id' => Uuid::uuid4(),
                'jabatan' => $request->bawahan,
                'kuota_karyawan' => $request->kuota_bawahan,
                'parent' => $get_id,
                'holding' => $holding_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $holding_category = Holding::where('id', $holding_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $get_id,
                'holding_id' => $holding_category->id,
                'holding_category' => $holding_category->holding_category,
                'table' => 'struktur_organisasi',
                'activity' => 'create',
                'description' => 'membuat struktur organisasi baru pada holding',
                'keterangan' => 'Failed',
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function struktur_bawahan(Request $request, $holding_id)
    {
        // dd($request->all(), $holding_id);
        $rules = [
            'bawahan' => 'required',

        ];
        $messages = [
            'jabatan.required' => 'Jabatan wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $id_struktur = Uuid::uuid4();
            StrukturModel::insert([
                'id' => $id_struktur,
                'jabatan' => $request->bawahan,
                'kuota_karyawan' => $request->kuota_karyawan,
                'status_pegawai' => $request->status_pegawai,
                'parent' => $request->id,
                'holding' => $holding_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $holding_category = Holding::where('id', $holding_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_struktur,
                'holding_id' => $holding_category->id,
                'holding_category' => $holding_category->holding_category,
                'table' => 'struktur_organisasi',
                'activity' => 'update',
                'description' => 'menambah bawahan baru pada holding',
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . '' . $e->getLine(),
            ]);
        }
    }

    public function dt_struktur()
    {
        // $table = $query->limit(2)->get();
        $table = StrukturModel::with('Parent')->with('Jabatan')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan', function ($row) {
                    return $row->Jabatan->nama_jabatan;
                })
                ->addColumn('parent', function ($row) {
                    return $row->Parent->Jabatan->nama_jabatan ?? "-";
                })
                ->addColumn('kuota_karyawan', function ($row) {
                    return $row->kuota_karyawan;
                })
                ->addColumn('option', function ($row) {
                    $level = $row->Jabatan->LevelJabatan->level_jabatan;
                    $btn =
                        '<button type="button" id="btn-add-bawahan"
                            data-id="' . $row->id . '"
                            data-kuota_karyawan="' . $row->kuota_karyawan . '"
                            data-jabatan="' . $row->Jabatan->nama_jabatan . '"
                            data-level="' . $level . '"
                            class="btn btn-icon btn-warning waves-effect waves-light">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>
                        <button type="button" id="btn-update-struktur"
                            data-id="' . $row->id . '"
                            data-kuota_karyawan="' . $row->kuota_karyawan . '"
                            data-jabatan="' . $row->Jabatan->nama_jabatan . '"
                            class="btn btn-icon btn-info waves-effect waves-light">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>
                        <button type="button" id="btn-delete-struktur"
                            data-id="' . $row->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        ';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan',
                    'parent',
                    'kuota_karyawan',
                    'option',
                ])
                ->make(true);
        }
    }
    public function dt_karyawan($id_jabatan)
    {
        // dd($id_jabatan);
        // $table = $query->limit(2)->get();
        $table = KaryawanJabatan::where('jabatan_id', $id_jabatan)->with(['Karyawan' => function ($query) {
            $query->where('status_aktif', 'AKTIF')->get();
        }])
            ->whereHas('Karyawan', function ($query) {
                $query->where('status_aktif', 'AKTIF');
            })
            ->where('is_active', 1);
        // dd($table->get());
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama', function ($row) {
                    return $row->Karyawan->name ?? '-';
                })
                ->rawColumns([
                    'nama',
                ])
                ->make(true);
        }
    }
    public function struktur_divisi($id, $holding_id)
    {
        // dd($id);
        $get_divisi = Divisi::where('dept_id', $id)->where('holding', $holding_id)->with('Holding')->orderBy('nama_divisi', 'ASC')->get();
        // dd($get_divisi);
        echo "<option value=''>Pilih Divisi...</option>";
        foreach ($get_divisi as $divisi) {
            echo "<option value='$divisi->id'>$divisi->nama_divisi</option>";
        }
    }
    public function struktur_bagian($id)
    {
        // dd($id);
        $get_bagian = Bagian::where('divisi_id', $id)->with('Holding')->orderBy('nama_bagian', 'ASC')->get();
        echo "<option value=''>Pilih Bagian...</option>";
        foreach ($get_bagian as $bagian) {
            echo "<option value='$bagian->id'>$bagian->nama_bagian</option>";
        }
    }
    public function struktur_bawahan_select($id_bagian)
    {
        $get_bawahan = Jabatan::with('Holding')
            ->where('bagian_id', $id_bagian)
            ->get();
        echo "<option value=''>Pilih Bawahan...</option>";
        foreach ($get_bawahan as $bawahan) {
            echo "<option value='$bawahan->id'>$bawahan->nama_jabatan</option>";
        }
    }
    public function struktur_delete(Request $request)
    {
        // dd($request->all());
        try {
            $delete = StrukturModel::where('id', $request->id)->first();
            $holding_category = Holding::where('id', $delete->holding)->first();
            // dd($holding_category->holding_category);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $get_struktur = StrukturModel::where('parent', $request->id)->first();

            if ($get_struktur) {

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holding_category->id,
                    'holding_category' => $holding_category->holding_category,
                    'table' => 'struktur_organisasi',
                    'activity' => 'delete',
                    'description' => 'gagal menghapus kotak struktur organisasi karena masih memiliki bawahan',
                    'keterangan' => 'Failed',
                ]);
                return response()->json([
                    'code' => 400,
                    'message' => 'Data Tidak Bisa Dihapus Karena Memiliki Bawahan',
                ]);
            } else {
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holding_category->id,
                    'holding_category' => $holding_category->holding_category,
                    'table' => 'struktur_organisasi',
                    'activity' => 'delete',
                    'description' => 'menghapus kotak struktur organisasi',
                    'keterangan' => 'Success',
                ]);
                $delete->delete();
                return response()->json([
                    'code' => 200,
                    'message' => 'Data Berhasil Dihapus',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function struktur_update(Request $request, $holding_id)
    {
        // dd($request->all());
        try {
            StrukturModel::where('id', $request->id)->update([
                'id' => $request->id,
                'kuota_karyawan' => $request->kuota_karyawan,
                'status_pegawai' => $request->status_pegawai,
                'parent' => $request->parent,
                'updated_at' => now(),
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $holding_category = Holding::where('id', $holding_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holding_category->id,
                'holding_category' => $holding_category->holding_category,
                'table' => 'struktur_organisasi',
                'activity' => 'update',
                'description' => 'mengubah jumlah kuota karyawan',
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
