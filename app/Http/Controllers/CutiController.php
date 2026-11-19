<?php

namespace App\Http\Controllers;

use App\Exports\CutiExport;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\MappingShift;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ActivityLog;
use App\Models\CutiDetail;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KategoriCuti;
use App\Models\KuotaCutiKaryawan;
use App\Models\Menu;
use App\Models\RoleUsers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;



class CutiController extends Controller
{
    public function index($holding, Request $request)
    {
        // dd($id);
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan');
            return redirect()->route('dashboard/holding');
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
        return view('admin.karyawan.cuti.index', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_karyawan_kuota_cuti($holding)
    {
        $table = KuotaCutiKaryawan::with('Karyawan')->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nomor_identitas_karyawan', function ($row) {
                    return $row->Karyawan->nomor_identitas_karyawan ?? '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->Karyawan->name ?? '-';
                })
                ->addColumn('edit', function ($row) use ($holding) {
                    $id_karyawan = $row->Karyawan->id ?? '-';
                    // $btn = '<button id="edit_cuti" 
                    //             data-id="' . $row->id . '" 
                    //             data-name="' . $row->Karyawan?->name . '" 
                    //             data-kuota="' . $row->kuota . '" 
                    //             data-terpakai="' . $row->terpakai . '" 
                    //             data-sisa="' . $row->sisa . '" 
                    //             type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                    //         </button>';
                    $btn = '<a href="' . url("/cuti_karyawan_detail/$id_karyawan/$holding") . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1"><span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->rawColumns([
                    'nomor_identitas_karyawan',
                    'name',
                    'edit',
                ])

                ->make(true);
        }
    }
    public function detail($id_karyawan, $holding, Request $request)
    {
        // dd($id);
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan');
            return redirect()->route('dashboard/holding');
        }
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        $karyawan = Karyawan::where('id', $id_karyawan)->first();
        $kuota_awal = KuotaCutiKaryawan::where('karyawan_id', $id_karyawan)->where('status', 1)->first();
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
        return view('admin.karyawan.cuti.detail', [
            'holding' => $getHolding,
            'menus' => $menus,
            'karyawan' => $karyawan,
            'kuota_awal' => $kuota_awal,

        ]);
    }
    public function dt_detail_kuota_cuti($id_karyawan, $holding)
    {
        $table = Cuti::where('karyawan_id', $id_karyawan)
            ->whereNull('kategori_cuti_id')
            ->orderBy('created_at', 'ASC')
            ->get();
        // dd($table);

        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('dibatalkan', function ($row) {
                    return $row->CutiDetail->where('status', 2)->count() ?? '-';
                })
                ->rawColumns([
                    'dibatalkan',
                ])

                ->make(true);
        }
    }
    public function update(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'terpakai' => 'required',
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
            KuotaCutiKaryawan::where('id', $request->id)->update([
                'terpakai' => $request->terpakai,
                'sisa' => $request->sisa,
            ]);
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
                'table' => 'cuti_kuota_karyawan',
                'activity' => 'update',
                'description' => 'mengedit kuota cuti terpakai : ' . $request->terpakai . ', dari Karyawan ' . $request->name,
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
    public function hari_update(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'tgl_baru' => 'required',
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

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();

            $karyawan = Karyawan::where('nomor_identitas_karyawan', $user_name->karyawan_id)->first();
            $cuti_detail = CutiDetail::where('id', $request->id)->first();

            CutiDetail::where('id', $request->id)->update([
                'updated' => 1,
                'tgl_cuti' => $request->tgl_baru,
                'original' => $request->tgl_cuti,
                'updated_by_id' => $karyawan->id,
                'updated_by_name' => $karyawan->name,
                'updated_at' => now(),
            ]);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'cuti_detail',
                'activity' => 'update',
                'description' => 'mengedit tanggal cuti : ' . $request->tgl_baru . ', dari Karyawan ' . $cuti_detail->nama_karyawan,
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
    public function hapus_hari_update(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'tgl_cuti' => 'required',
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

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();

            $karyawan = Karyawan::where('nomor_identitas_karyawan', $user_name->karyawan_id)->first();
            $cuti_detail = CutiDetail::where('id', $request->id)->first();
            $user_diubah = Karyawan::where('id', $cuti_detail->id_karyawan)->first();
            $kuota = KuotaCutiKaryawan::where('karyawan_id', $user_diubah->id)->where('status', '1')->first();
            // dd($kuota);

            $cuti = Cuti::where('id', $cuti_detail->id_cuti)->first();
            // dd($cuti);

            if ($cuti->kategori_cuti_id == null) {
                $kuota_terpakai = $kuota->terpakai - 1;
                $kuota_sisa = $kuota->sisa + 1;
                $kuota->update([
                    'terpakai' => $kuota_terpakai,
                    'sisa' => $kuota_sisa,
                ]);
            }
            CutiDetail::where('id', $request->id)->update([
                'updated' => 1,
                'status' => 2,
                'updated_by_id' => $karyawan->id,
                'updated_by_name' => $karyawan->name,
                'updated_at' => now(),
            ]);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'cuti_detail',
                'activity' => 'update',
                'description' => 'membatalkan tanggal cuti : ' . $request->tgl_baru . ', dari Karyawan ' . $cuti_detail->nama_karyawan,
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
    public function dt_karyawan_hari_cuti(Request $request, $holding)
    {
        $now = Carbon::parse($request->start_date)->format('Y-m-d');
        $now1 = Carbon::parse($request->end_date)->format('Y-m-d');
        // dd($now);

        $table = CutiDetail::with('Karyawan')
            ->whereNotNull('status')
            ->whereBetween('tgl_cuti', [$now, $now1])
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_cuti', function ($row) {
                    return $row->Cuti->nama_cuti ?? '-';
                })
                ->addColumn('nomor_identitas_karyawan', function ($row) {
                    return $row->Karyawan->nomor_identitas_karyawan ?? '-';
                })
                ->addColumn('status', function ($row) {
                    if ($row->updated == null) {
                        return '<span class="badge bg-label-success">CUTI</span>';
                    } else {
                        if ($row->status == 1) {
                            return '<span class="badge bg-label-warning">GANTI HARI</span>';
                        } elseif ($row->status == 2) {
                            return '<span class="badge bg-label-danger">DIBATALKAN</span>';
                        }
                    }
                })
                ->addColumn('edit', function ($row) use ($holding) {
                    $btn = '<button id="edit_hari" 
                                data-id="' . $row->id . '" 
                                data-name="' . $row->nama_karyawan . '" 
                                data-tgl_cuti="' . $row->tgl_cuti . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    $btn = $btn . '<button id="hapus_hari" 
                                data-id="' . $row->id . '" 
                                data-name="' . $row->nama_karyawan . '" 
                                data-tgl_cuti="' . $row->tgl_cuti . '" 
                                type="button" class="btn btn-icon btn-danger waves-effect waves-light m-1"><span class="tf-icons mdi mdi-trash-can"></span>
                            </button>';
                    return $btn;
                })
                ->addColumn('updated_at', function ($row) {
                    if ($row->updated == null) {
                        return '-';
                    } else {
                        return $row->updated_at->format('d-m-Y H:i:s');
                    }
                })
                ->rawColumns([
                    'nomor_identitas_karyawan',
                    'nama_cuti',
                    'status',
                    'edit',
                    'updated_at',
                ])

                ->make(true);
        }
    }
}
