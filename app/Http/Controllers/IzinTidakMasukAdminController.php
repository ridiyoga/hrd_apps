<?php

namespace App\Http\Controllers;

use App\Exports\IzinExport;
use App\Models\ActivityLog;
use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Izin;
use App\Models\Jabatan;
use App\Models\MappingShift;
use App\Models\Karyawan;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Menu;
use App\Models\Holding;
use App\Models\IzinTidakMasuk;
use App\Models\IzinTidakMasukDetail;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class IzinTidakMasukAdminController extends Controller
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
        return view('admin.karyawan.izin.tidak_masuk_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_tidak_masuk($holding)
    {
        $table = IzinTidakMasuk::with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('IzinTidakMasukDetail')
            ->where('status_tidak_masuk', 2)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_atasan1', function ($row) {
                    return ($row->Atasan1->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Atasan1->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Atasan1->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_atasan2', function ($row) {
                    return ($row->Atasan2->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Atasan2->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Atasan2->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('status_tidak_masuk', function ($row) {
                    if ($row->status_tidak_masuk == 2) {
                        return '<span class="badge bg-label-success">DISETUJUI</span>';
                    }
                })
                ->addColumn('approve', function ($row) {
                    $btn = '<button id="btn_setuju" 
                                data-id="' . $row->id . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    return $btn;
                })

                ->addColumn('tanggal_tidak_masuk', function ($row) {
                    return $row->tanggal_tidak_masuk . ' (' . $row->IzinTidakMasukDetail->count() . ' HARI)';
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_tidak_masuk',
                    'approve',
                ])

                ->make(true);
        }
    }
    public function tidak_masuk_approve_hrd($holding, Request $request)
    {
        // dd($request->all());
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
            $data_tidak_masuk = IzinTidakMasuk::where('id', $request->id_tidak_masuk)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($data_tidak_masuk_detail);
            $count_tidak_masuk = IzinTidakMasuk::whereIn('status_tidak_masuk', [5, 3])->whereDate('created_at', $data_tidak_masuk->created_at)->where('id', '!=', $data_tidak_masuk->id)->get()->count();
            // dd($count_tidak_masuk);
            if ($count_tidak_masuk == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_tidak_masuk + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($data_tidak_masuk->created_at)->format('Ymd');

            $no_form = $data_tidak_masuk->User->KontrakKerja->holding_category . '/FIS' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($fileName);

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
            if ($request->setuju == 1) {
                $data_tidak_masuk->update([
                    'status_approve_hrd' => 1,
                    'no_form_tidak_masuk' => $no_form,
                    'status_tidak_masuk' => 3,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                IzinTidakMasukDetail::where('id_tidak_masuk', $data_tidak_masuk->id)->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval tidak_masuk HRD',
                    'id_table' => $data_tidak_masuk->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_tidak_masuk',
                    'activity' => 'add',
                    'description' => 'Menyetujui izin tidak_masuk sebagai HRD ' .  $data_tidak_masuk->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
            } else {
                $data_tidak_masuk->update([
                    'status_approve_hrd' => 1,
                    'status_tidak_masuk' => 5,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval tidak_masuk HRD',
                    'id_table' => $data_tidak_masuk->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_tidak_masuk',
                    'activity' => 'add',
                    'description' => 'Menolak izin tidak_masuk sebagai HRD ' .  $data_tidak_masuk->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan izin berhasil Disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function dt_tidak_masuk_riwayat($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = IzinTidakMasuk::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('IzinTidakMasukDetail')
            ->whereIn('status_tidak_masuk', [3, 5])
            // ->
            ->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_atasan1', function ($row) {
                    return ($row->Atasan1->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Atasan1->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Atasan1->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_atasan2', function ($row) {
                    return ($row->Atasan2->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Atasan2->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Atasan2->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })

                ->addColumn('tanggal_tidak_masuk', function ($row) {
                    return $row->tanggal_tidak_masuk . ' (' . $row->IzinTidakMasukDetail->count() . ' HARI)';
                })
                ->addColumn('status_tidak_masuk', function ($row) {
                    if ($row->status_tidak_masuk == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('form_tidak_masuk', function ($row) {
                    $btn = '<a href="' . asset('izin/cetak_form_tidak_masuk/' . $row->id . '/' . $row->karyawan_id) . '" 
                                type="button" class="btn btn-icon btn-primary waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_tidak_masuk',
                    'form_tidak_masuk',
                ])

                ->make(true);
        }
    }
    public function tidak_masuk_report_karyawan($holding, Request $request)
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
        return view('admin.report.izin.tidak_masuk_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_tidak_masuk_report($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = IzinTidakMasuk::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('IzinTidakMasukDetail')
            ->whereIn('status_tidak_masuk', [3, 5])
            // ->
            ->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_atasan1', function ($row) {
                    return ($row->Atasan1->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Atasan1->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Atasan1->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_atasan2', function ($row) {
                    return ($row->Atasan2->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->Atasan2->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->Atasan2->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_hrd', function ($row) {
                    return ($row->AtasanHRD->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '')
                        . ' ' . ($row->AtasanHRD->KaryawanJabatanOne->Divisi->nama_divisi ?? '')
                        . '  ' . ($row->AtasanHRD->KaryawanJabatanOne->Bagian->nama_bagian ?? '');
                })

                ->addColumn('tanggal_tidak_masuk', function ($row) {
                    return $row->tanggal_tidak_masuk . ' (' . $row->IzinTidakMasukDetail->count() . ' HARI)';
                })
                ->addColumn('status_tidak_masuk', function ($row) {
                    if ($row->status_tidak_masuk == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('form_tidak_masuk', function ($row) {
                    $btn = '' . asset('izin/cetak_form_tidak_masuk/' . $row->id . '/' . $row->karyawan_id) . '';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_hrd',
                    'status_tidak_masuk',
                    'form_tidak_masuk',
                ])

                ->make(true);
        }
    }
}
