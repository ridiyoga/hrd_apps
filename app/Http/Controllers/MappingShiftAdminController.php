<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalGroup;
use App\Models\ApprovalUser;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\MappingShiftHead;
use App\Models\MappingShiftTukar;
use App\Models\Menu;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\StrukturModel;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class MappingShiftAdminController extends Controller
{
    public function index($holding, Request $request)
    {
        // dd($me);
        // abort(404);
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
            return view('admin.karyawan.mapping_shift_approval.index', [
                'holding' => $getHolding,
                'menus' => $menus

            ]);
        }
    }
    public function dt_shift_approval(Request $request, $holding)
    {
        $table = MappingShiftHead::with('KaryawanPengaju')->where('approved', null)->get();
        // dd($table);

        if (request()->ajax()) {
            return DataTables::of($table)

                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->KaryawanPengaju->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->KaryawanPengaju->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->KaryawanPengaju->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan', function ($row) {
                    return ($row->KaryawanPengaju->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-');
                })
                ->addColumn('divisi', function ($row) {
                    return ($row->KaryawanPengaju->KaryawanJabatanOne->Divisi->nama_divisi ?? '-');
                })
                ->addColumn('bagian', function ($row) {
                    return ($row->KaryawanPengaju->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('departemen', function ($row) {
                    return ($row->KaryawanPengaju->KaryawanJabatanOne->Departemen->nama_departemen ?? '-');
                })
                ->addColumn('penempatan_kerja', function ($row) {
                    return $row->KaryawanPengaju->PenempatanKerja->site_name ?? '-';
                })
                ->addColumn('detail', function ($row) {
                    $btn = '<button id="btn_setuju" 
                                data-id="' . $row->id . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    return $btn;
                })
                ->addColumn('approved', function ($row) {
                    if ($row->approved == 1) {
                        return '<span class="badge bg-label-success">Disetujui</span>';
                    } else {
                        return '<span class="badge bg-label-info">Belum Disetujui</span>';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan',
                    'divisi',
                    'bagian',
                    'departemen',
                    'detail',
                    'approved',
                    'created_at',
                    'penempatan_kerja'
                ])
                ->make(true);
        }
    }
    public function dt_detail_shift_approval($holding, $id)

    {
        $table = MappingShift::where('id_head', $id)->with('Karyawan')->with('Shift')->get();
        // dd($table->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan);
        // foreach ($table as $row) {

        //     dd($row->Karyawan->Holding);
        // }
        // return response()->json([
        //     'holding_param' => $getHolding,
        //     'is_ajax' => request()->ajax(),
        // ]);
        // // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)

                ->addColumn('jabatan_karyawan', function ($row) {
                    return ($row->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Karyawan->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Karyawan->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })

                ->addColumn('jabatan', function ($row) {
                    return ($row->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-');
                })
                ->addColumn('divisi', function ($row) {
                    return ($row->Karyawan->KaryawanJabatanOne->Divisi->nama_divisi ?? '-');
                })
                ->addColumn('bagian', function ($row) {
                    return ($row->Karyawan->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('departemen', function ($row) {
                    return ($row->Karyawan->KaryawanJabatanOne->Departemen->nama_departemen ?? '-');
                })
                ->addColumn('hari_masuk', function ($row) {
                    return Carbon::parse($row->tanggal_masuk)->locale('id')->translatedFormat('l');
                })
                ->addColumn('penempatan_kerja', function ($row) {
                    return                         Carbon::parse($row->tanggal_masuk)
                        ->locale('id')
                        ->translatedFormat('l');
                })
                ->addColumn('jam_masuk', function ($row) {
                    return $row->Shift->jam_masuk;
                })
                ->addColumn('jam_keluar', function ($row) {
                    return $row->Shift->jam_keluar;
                })
                ->addColumn('pengubahan', function ($row) {
                    if ($row->is_active == 1) {
                        return '<span class="badge bg-label-secondary">ORIGINAL</span>';
                    } elseif ($row->is_active == 2) {
                        return '<span class="badge bg-label-info">DIUBAH&nbsp;PEGAWAI</span>';
                    } elseif ($row->is_active == 3) {
                        return '<span class="badge bg-label-success">DIUBAH&nbsp;ATASAN</span>';
                    }
                })
                ->rawColumns([
                    'jabatan_karyawan',
                    'jabatan',
                    'divisi',
                    'bagian',
                    'departemen',
                    'penempatan_kerja',
                    'jam_masuk',
                    'jam_keluar',
                    'pengubahan',
                ])
                ->make(true);
        }
    }
    public function shift_approve(Request $request)
    {
        $rules = [
            'signature' => 'required',

        ];
        $messages = [
            'signature.required' => 'Tanda tangan wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ]);
        }
        try {
            $data_shift = MappingShiftHead::where('id', $request->id_shift)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($data_shift);

            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();

            // nama file
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $fileName = 'signature_' . time() . '.png';
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $data_shift->update([
                'status_approve_hrd' => 1,
                'approved' => 1,
                'approved_by' => $user_karyawan->id,
                'approved_at' => now(),
                'approved_signature' => $fileName,
                'approved_by_name' => $user_karyawan->name,
                'updated_at' => now(),
            ]);
            MappingShift::where('id_head', $data_shift->id)->update([
                'approved_by' => $user_karyawan->id,
                'status_approved' => 1,
                'is_active' => 1,
                'updated_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval shift HRD',
                'id_table' => $data_shift->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'izin_keluar',
                'activity' => 'add',
                'description' => 'Menyetujui mapping shift sebagai HRD ' .  $data_shift->input_by_name,
                'keterangan' => 'Success',
                'karir' => null
            ]);
            // } else {
            //     $data_shift->update([
            //         'status_approve_hrd' => 1,
            //         'status_shift' => 5,
            //         'id_user_hrd' => $user_karyawan->id,
            //         'nama_hrd' => $user_karyawan->name,
            //         'ttd_hrd' => $fileName,
            //         'waktu_approve_hrd' => now(),
            //     ]);
            //     ActivityLog::create([
            //         'user_id' => $request->user()->id,
            //         'user_name' => $user_name->username,
            //         'menu_id' => null,
            //         'menu_name' => 'approval shift HRD',
            //         'id_table' => $data_shift->id,
            //         'holding_id' => $holdings->id ?? '-',
            //         'holding_category' => $holdings->holding_category ?? '-',
            //         'table' => 'izin_keluar',
            //         'activity' => 'add',
            //         'description' => 'Menolak izin shift sebagai HRD ' .  $data_shift->nama_karyawan,
            //         'keterangan' => 'Success',
            //         'karir' => null
            //     ]);

            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan mapping shift berhasil Disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
