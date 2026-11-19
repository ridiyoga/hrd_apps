<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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
use App\Models\Lembur;
use App\Models\LemburDetail;
use App\Models\MappingShift;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class LemburAdminController extends Controller
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
        return view('admin.karyawan.lembur.lembur_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function detail_lembur_karyawan($holding, $id, Request $request)
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
        $lembur = Lembur::where('id', $id)->first();
        return view('admin.karyawan.lembur.detail_lembur_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus,
            'lembur' => $lembur,
            'id_lembur' => $id

        ]);
    }
    public function lembur_karyawan_report($holding, Request $request)
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
        return view('admin.report.lembur', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_lembur($holding)
    {
        $table = Lembur::with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('LemburDetail')
            ->where('status_lembur', 2)
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
                ->addColumn('status_lembur', function ($row) {
                    if ($row->status_lembur == 2) {
                        return '<span class="badge bg-label-success">DISETUJUI</span>';
                    }
                })
                ->addColumn('approve', function ($row) {
                    $tanggal = Carbon::parse($row?->tanggal_lembur)->addDay(2);
                    $today = Carbon::today();

                    if ($today->lt($tanggal)) {
                        $btn = '<button id="btn_setuju" 
                            data-id="' . $row->id . '" 
                            type="button" 
                            class="btn btn-icon btn-warning waves-effect waves-light m-1" 
                            disabled>
                            <span class="tf-icons mdi mdi-pencil-outline"></span>
                        </button>';
                    } else {
                        $btn = '<button id="btn_setuju" 
                            data-id="' . $row->id . '" 
                            type="button" 
                            class="btn btn-icon btn-warning waves-effect waves-light m-1">
                            <span class="tf-icons mdi mdi-pencil-outline"></span>
                        </button>';
                    }

                    return $btn;
                })

                ->addColumn('tanggal_lembur', function ($row) {
                    return $row->tanggal_lembur;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_lembur',
                    'approve',
                ])

                ->make(true);
        }
    }
    public function dt_detail_lembur($id)
    {
        $table = LemburDetail::with('MappingShift')
            ->with('Absensi')
            ->with('Lembur')
            ->where('id_lembur', $id)
            // ->where('id', '3e43dfb7-6527-4803-8649-bbf0afb3055f')
            ->get();
        // dd($table);
        if (request()->ajax()) {

            return DataTables::of($table)
                ->addColumn('jam_mulai', function ($row) {
                    return filled($row?->jam_mulai)
                        ? Carbon::parse($row?->jam_mulai)->format('H:i')
                        : '-';
                })

                ->addColumn('jam_selesai', function ($row) {
                    return filled($row?->jam_selesai)
                        ? Carbon::parse($row?->jam_selesai)->format('H:i')
                        : '-';
                })

                ->addColumn('selisih_convert', function ($row) {
                    return filled($row?->selisih_convert)
                        ? Carbon::parse($row?->selisih_convert)->format('H:i')
                        : '-';
                })
                ->addColumn('presensi_masuk', function ($row) {
                    if ($row->User->shift == 'NON SHIFT') {
                        $masuk =  Shift::where('nama_shift', 'NON SHIFT')->first();
                        if (!filled($row?->tgl_lembur) || !filled($masuk?->jam_min_masuk)) {
                            $row->checkIn = '-';
                            return '-';
                        }

                        $min_masuk = $masuk?->jam_min_masuk
                            ? \Carbon\Carbon::parse($masuk?->jam_min_masuk)->format('H:i')
                            : null;
                        $terlambat =  $masuk?->jam_min_masuk
                            ? \Carbon\Carbon::parse($masuk?->jam_min_masuk)
                            ->addHours(3)
                            ->addMinutes(6)
                            ->format('H:i')
                            : null;
                        $in = $row->Absensi
                            ?->whereBetween('LogTime', [
                                $row->tgl_lembur . ' ' . $min_masuk . ':00',
                                $row->tgl_lembur . ' ' . $terlambat . ':00',
                            ])
                            ?->filter(fn($log) => filled($log?->LogTime))
                            ?->sortBy('LogTime')
                            ?->first();
                        if ($in != null) {
                            $checkIn  = Carbon::parse($in?->LogTime)->format('H:i');
                        } else {
                            $checkIn = "-";
                        }
                        $row->checkIn = $checkIn;
                        return $checkIn;
                    } else {
                        $jam_mapping = $row->MappingShift()
                            ->with('Shift')
                            ->where('karyawan_id', $row->id_karyawan)
                            ->whereDate('tanggal_masuk', $row->Lembur?->tanggal_lembur)
                            // ->whereNotNull('is_active')
                            ->first();
                        if (
                            !filled($jam_mapping?->tanggal_masuk) ||
                            !filled($jam_mapping?->Shift?->jam_min_masuk) ||
                            !filled($jam_mapping?->Shift?->jam_masuk)
                        ) {
                            $row->checkIn = '-';
                            return '-';
                        }

                        $lebih_awal = $jam_mapping?->Shift?->jam_min_masuk
                            ? \Carbon\Carbon::parse($jam_mapping?->Shift?->jam_min_masuk)->subHours(2)->format('H:i')
                            : null;
                        $min_masuk = $jam_mapping?->Shift?->jam_min_masuk
                            ? \Carbon\Carbon::parse($jam_mapping?->Shift?->jam_min_masuk)->format('H:i')
                            : null;
                        $terlambat = $jam_mapping?->Shift?->jam_masuk
                            // dd($terlambat);
                            ? \Carbon\Carbon::parse($jam_mapping?->Shift?->jam_masuk)
                            ->addHours(3)
                            ->addMinutes(6)
                            ->format('H:i')
                            : null;
                        if ($jam_mapping?->Shift?->tgl_pulang_besok != 1) {
                            $end_date  = Carbon::parse($jam_mapping?->tanggal_masuk  . ' ' . $terlambat);
                        } else {
                            $end_date  = Carbon::parse($jam_mapping?->tanggal_masuk  . ' ' . $terlambat)->addDay(1);
                        }
                        $jam_kerja_shift = $row->Absensi->filter(function ($log) use ($min_masuk, $jam_mapping, $end_date) {
                            if (!filled($log?->LogTime)) {
                                return false;
                            }

                            $getcheckIn = Carbon::parse($log?->LogTime);
                            $start = Carbon::parse($jam_mapping?->tanggal_masuk . ' ' . $min_masuk)->format('Y-m-d H:i:s');
                            $end = $end_date->format('Y-m-d H:i:s');
                            return $getcheckIn->between($start, $end);
                        });
                        // dd($jam_mapping->tanggal_masuk . ' ' . $min_masuk, $jam_mapping->tanggal_masuk . ' ' . $terlambat);
                        if ($jam_kerja_shift->isNotEmpty()) {
                            $checkIn  = Carbon::parse($jam_kerja_shift?->min('LogTime'))->format('H:i');
                        } else {
                            $jam_kerja_shift = $row->Absensi->filter(function ($log) use ($lebih_awal, $jam_mapping, $end_date) {
                                if (!filled($log?->LogTime)) {
                                    return false;
                                }

                                $getcheckIn = Carbon::parse($log?->LogTime);
                                $start = Carbon::parse($jam_mapping?->tanggal_masuk . ' ' . $lebih_awal)->format('Y-m-d H:i:s');
                                $end = $end_date->format('Y-m-d H:i:s');
                                return $getcheckIn->between($start, $end);
                            });
                            if ($jam_kerja_shift->isNotEmpty()) {
                                $checkIn  = Carbon::parse($jam_kerja_shift?->min('LogTime'))->format('H:i');
                            } else {
                                $checkIn  = '-';
                            }
                        }
                        // dd($jam_mapping?->tanggal_masuk . ' ' . $min_masuk, $end_date,);
                        $row->checkIn = $checkIn;
                        return $checkIn;
                    }
                })
                ->addColumn('presensi_keluar', function ($row) {

                    if ($row->User->shift == 'NON SHIFT') {
                        $keluar =  Shift::where('nama_shift', 'NON SHIFT')->first();
                        if (
                            !filled($row?->tgl_lembur) ||
                            !filled($row?->tgl_pulang) ||
                            !filled($keluar?->jam_keluar)
                        ) {
                            $row->checkOut = '-';
                            return '-';
                        }

                        $min_keluar = $row?->jam_mulai
                            ? \Carbon\Carbon::parse($row?->jam_mulai)->format('H:i')
                            : null;
                        $end =  $keluar?->jam_min_keluar
                            ? \Carbon\Carbon::parse($keluar?->jam_min_keluar)
                            ->addHours(3)
                            ->addMinutes(6)
                            ->format('H:i')
                            : null;
                        $out = $row->Absensi
                            ?->whereBetween('LogTime', [
                                $row->tgl_lembur . ' ' . $min_keluar . ':00',
                                $row->tgl_pulang . ' ' . $end . ':00',
                            ])
                            ?->filter(fn($log) => filled($log?->LogTime))
                            ?->sortByDesc('LogTime')
                            ?->first();
                        if ($out != null) {
                            $checkOut  = Carbon::parse($out?->LogTime)->format('H:i');
                        } else {
                            $checkOut = "-";
                        }
                        $row->checkOut = $checkOut;
                        return $checkOut;
                    } else {

                        // $jam_mapping = $row->tgl_pulang;
                        if (
                            !filled($row?->tgl_lembur) ||
                            !filled($row?->jam_mulai) ||
                            !filled($row?->tgl_pulang) ||
                            !filled($row?->jam_selesai)
                        ) {
                            $row->checkOut = '-';
                            return '-';
                        }

                        $overtime = $row?->jam_selesai
                            ? \Carbon\Carbon::parse($row?->jam_selesai)
                            ->addHours(3)
                            ->addMinutes(6)
                            ->format('H:i')
                            : null;
                        // dd($jam_mapping, $overtime, $row->tgl_lembur, $row->tgl_pulang);

                        $start = Carbon::parse($row?->tgl_lembur . ' ' . $row?->jam_mulai);
                        $end = Carbon::parse($row?->tgl_pulang . ' ' . $overtime);
                        // dd($start, $end);
                        if ($end->lessThan($start)) {
                            $end->addDay();
                        }
                        $jam_kerja_shift = $row->Absensi->filter(
                            fn($log) => filled($log?->LogTime) && Carbon::parse($log?->LogTime)->between($start, $end)
                        );
                        $checkOut = $jam_kerja_shift?->isNotEmpty()
                            ? Carbon::parse($jam_kerja_shift?->max('LogTime'))->format('H:i')
                            : '-';
                        $row->checkOut = $checkOut;
                        return $checkOut;
                    }
                })
                ->addColumn('selisih_convert', function ($row) {

                    // 1. Kalau checkIn atau checkOut tidak ada,
                    //    tidak bisa menghitung selisih waktu.
                    if (
                        $row->checkIn == "-" ||
                        $row->checkOut == "-" ||
                        !filled($row?->tgl_lembur) ||
                        !filled($row?->jam_mulai) ||
                        !filled($row?->jam_selesai)
                    ) {
                        return "-";
                    }

                    $jamMulai = Carbon::parse($row->tgl_lembur . ' ' . $row->jam_mulai);
                    $jamSelesai = Carbon::parse($row->tgl_lembur . ' ' . $row->jam_selesai);
                    $checkIn = Carbon::parse($row->tgl_lembur . ' ' . $row->checkIn);
                    $checkOut = Carbon::parse($row->tgl_lembur . ' ' . $row->checkOut);
                    if ($jamMulai->greaterThan($row->checkIn)) {
                        $Mulai = $checkIn;
                    } else {
                        $Mulai = $jamMulai;
                    }
                    if ($jamSelesai->lessThan($jamMulai)) {
                        $selesai_biasa = $jamSelesai->addDay();
                    } else {
                        $selesai_biasa = $jamSelesai;
                    }
                    if ($checkOut->lessThan($jamMulai)) {
                        $selesai_cek = $checkOut->addDay();
                    } else {
                        $selesai_cek = $checkOut;
                    }
                    if ($selesai_cek->lessThan($selesai_biasa)) {
                        $pulang = $selesai_cek;
                    } else {
                        $pulang = $selesai_biasa;
                    }

                    $totalMenit = $Mulai->diffInMinutes($pulang);

                    $jam = intdiv($totalMenit, 60);
                    $menit = $totalMenit % 60;
                    $selisih = sprintf('%02d:%02d', $jam, $menit);
                    return '<span class="badge bg-label-primary" style="font-size: 0.9rem; padding: 0.5em 0.75em;">' . $selisih . '</span><input 
                                    class="selisih-lembur"
                                    type="hidden"
                                    value="' . $selisih . '"
                                    id="selisih_lembur_' . $row->id . '"
                                    name="selisih_lembur[]"
                                >';
                })
                ->addColumn('status_lembur', function ($row) {
                    // $jumlah_presensi = $row->Absensi?->pluck('LogTime')->filter()->unique()->count() ?? 0;

                    // return $jumlah_presensi >= 2 ? 'ADA' : '';
                    if ($row->checkIn == "-" || $row->checkOut == "-") {
                        return null;
                    } else {
                        return '<div class="form-check">
                                <input 
                                    class="form-check-input status-lembur"
                                    type="checkbox"
                                    value="' . $row->id . '"
                                    id="status_lembur_' . $row->id . '"
                                    name="status_lembur[]"
                                    checked
                                >
                                <label 
                                    class="form-check-label" 
                                    for="status_lembur_' . $row->id . '">
                                </label>
                                <input 
                                    class="selisih-lembur"
                                    type="hidden"
                                    value="' . $row->checkOut . '"
                                    id="presensi_pulang_' . $row->id . '"
                                    name="presensi_pulang[]"
                                >
                                <input 
                                    class="selisih-lembur"
                                    type="hidden"
                                    value="' . $row->checkIn . '"
                                    id="presensi_masuk_' . $row->id . '"
                                    name="presensi_masuk[]"
                                >
                            </div>';
                    }
                })

                ->rawColumns([
                    'jam_mulai',
                    'jam_selesai',
                    'selisih_convert',
                    'presensi_masuk',
                    'presensi_keluar',
                    'status_lembur',
                ])
                ->make(true);
        }
    }
    public function dt_detail_lembur_report($id)
    {
        $table = LemburDetail::with('MappingShift')
            ->with('Absensi')
            ->with('Lembur')
            ->whereNotNull('status')
            ->where('id_lembur', $id)
            ->get();
        // dd($table);
        if (request()->ajax()) {

            return DataTables::of($table)
                ->addColumn('spv', function ($row) {
                    return $row->Lembur->nama_karyawan;
                })

                ->addColumn('atasan1', function ($row) {
                    return $row->Lembur->nama_atasan;
                })
                ->addColumn('atasan2', function ($row) {
                    return $row->Lembur->nama_atasan2;
                })
                ->addColumn('hrd', function ($row) {
                    return $row->Lembur->nama_hrd;
                })

                ->addColumn('jam_mulai', function ($row) {
                    return $row->jam_mulai
                        ? Carbon::parse($row?->jam_mulai)->format('H:i')
                        : '-';
                })

                ->addColumn('jam_selesai', function ($row) {
                    return $row->jam_selesai
                        ? Carbon::parse($row?->jam_selesai)->format('H:i')
                        : '-';
                })

                ->addColumn('selisih_convert', function ($row) {
                    return $row->selisih_convert
                        ? Carbon::parse($row?->selisih_convert)->format('H:i')
                        : '-';
                })
                ->addColumn('status_lembur', function ($row) {
                    if ($row->status == 2) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } elseif ($row->status == null) {
                        return '<span class="badge bg-label-danger">BELUM DISETUJUI HRD</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->rawColumns([
                    'spv',
                    'atasan1',
                    'atasan2',
                    'hrd',
                    'status_lembur',
                    'jam_mulai',
                    'jam_selesai',
                    'selisih_convert',
                    'presensi_masuk',
                    'presensi_keluar',
                ])
                ->make(true);
        }
    }
    public function dt_detail_lembur_report2($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = LemburDetail::whereBetween('tgl_lembur', array($start, $end))
            ->with('MappingShift')
            ->with('Absensi')
            ->whereNotNull('status')
            ->with('Lembur')
            ->get();
        // dd($table);
        if (request()->ajax()) {

            return DataTables::of($table)
                ->addColumn('spv', function ($row) {
                    return $row->Lembur->nama_karyawan;
                })
                ->addColumn('nik', function ($row) {
                    return $row->User->nomor_identitas_karyawan;
                })
                ->addColumn('holding', function ($row) {
                    return $row->User->KontrakKerja->holding_name ?? '-';
                })
                ->addColumn('atasan1', function ($row) {
                    return $row->Lembur->nama_atasan;
                })
                ->addColumn('atasan2', function ($row) {
                    return $row->Lembur->nama_atasan2;
                })
                ->addColumn('hrd', function ($row) {
                    return $row->Lembur->nama_hrd;
                })

                ->addColumn('jam_mulai', function ($row) {
                    return $row->jam_mulai
                        ? Carbon::parse($row?->jam_mulai)->format('H:i')
                        : '-';
                })

                ->addColumn('jam_selesai', function ($row) {
                    return $row->jam_selesai
                        ? Carbon::parse($row?->jam_selesai)->format('H:i')
                        : '-';
                })

                ->addColumn('selisih_convert', function ($row) {
                    return $row->selisih_convert
                        ? Carbon::parse($row?->selisih_convert)->format('H:i')
                        : '-';
                })
                ->addColumn('status_lembur', function ($row) {
                    if ($row->status == 2) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } elseif ($row->status == null) {
                        return '<span class="badge bg-label-danger">BELUM DISETUJUI HRD</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('shift', function ($row) {
                    if ($row->User->shift == 'NON SHIFT') {
                        return 'NON SHIFT';
                    } else {
                        return $row->MappingShift?->Shift?->nama_shift ?? '-';
                    }
                })
                ->addColumn('shift_terlambat', function ($row) {
                    if ($row->User->shift == 'NON SHIFT') {
                        $shift = Shift::where('nama_shift', 'NON SHIFT')->first();
                        return $shift?->jam_terlambat ?? '-';
                    } else {
                        return $row->MappingShift?->Shift?->jam_terlambat ?? '-';
                    }
                })
                ->rawColumns([
                    'spv',
                    'nik',
                    'holding',
                    'atasan1',
                    'atasan2',
                    'hrd',
                    'status_lembur',
                    'jam_mulai',
                    'jam_selesai',
                    'selisih_convert',
                    'presensi_masuk',
                    'presensi_keluar',
                    'shift',
                    'shift_terlambat',
                ])
                ->make(true);
        }
    }
    public function lembur_approve_hrd($holding, Request $request)
    {
        if ($request->setuju == 1) {
            $status_lembur_req = 'required|array|min:1';
        } else {
            $status_lembur_req = 'nullable';
        }
        $rules = [
            'signature' => 'required',
            'status_lembur' => $status_lembur_req,
        ];
        $messages = [
            'signature.required' => 'Tanda tangan wajib diisi',
            'status_lembur.required' => 'Setuju minimal satu lembur, jika semua lembur bermasalah silahkan DITOLAK',
            'status_lembur.array' => 'Status lembur harus berupa array',
            'status_lembur.min' => 'Setuju minimal satu lembur, jika semua lembur bermasalah silahkan DITOLAK',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ]);
        }
        // dd($request->all());
        try {
            $data_lembur = Lembur::where('id', $request->id_lembur)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($data_lembur_detail);
            $count_lembur = Lembur::whereIn('status_lembur', [5, 3])->whereDate('created_at', $data_lembur->created_at)->where('id', '!=', $data_lembur->id)->get()->count();
            // dd($count_lembur);
            if ($count_lembur == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_lembur + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($data_lembur?->created_at)->format('Ymd');

            $no_form = $data_lembur->User->KontrakKerja->holding_category . '/FL' . '/' . $tgl_pengajuan . '/' . $last_count;

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
                $data_lembur->update([
                    'status_approve_hrd' => 1,
                    'no_form_lembur' => $no_form,
                    'status_lembur' => 3,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                // LemburDetail::where('id_lembur', $data_lembur->id)->update([
                //     'status' => 1,
                //     'updated_at' => now(),
                // ]);
                $statusLembur = $request->input('status_lembur', []);

                $details = LemburDetail::where('id_lembur', $request->id_lembur)
                    ->get();

                foreach ($details as $detail) {

                    $index = array_search($detail->id, $request->status_lembur);

                    $selisih = $index !== false
                        ? ($request->selisih_lembur[$index] ?? null)
                        : null;
                    $checkIn = $index !== false
                        ? ($request->input("presensi_masuk.$index") ?? null)
                        : null;

                    $checkOut = $index !== false
                        ? ($request->input("presensi_pulang.$index") ?? null)
                        : null;
                    $detail->update([
                        'status' => in_array($detail->id, $statusLembur) ? 1 : 2,
                        'updated_at' => now(),
                        'selisih_convert' => $selisih,
                        'presensi_masuk' => $checkIn,
                        'presensi_pulang' => $checkOut,
                    ]);
                }

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval lembur HRD',
                    'id_table' => $data_lembur->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'lembur',
                    'activity' => 'add',
                    'description' => 'Menyetujui lembur sebagai HRD ' .  $data_lembur->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
            } else {
                $data_lembur->update([
                    'status_approve_hrd' => 1,
                    'status_lembur' => 5,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);

                LemburDetail::where('id_lembur', $data_lembur->id)->update([
                    'status' => 2,
                    'updated_at' => now(),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval lembur HRD',
                    'id_table' => $data_lembur->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'lembur',
                    'activity' => 'add',
                    'description' => 'Menolak lembur sebagai HRD ' .  $data_lembur->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan berhasil Disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function dt_lembur_riwayat($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = Lembur::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('LemburDetail')
            ->whereIn('status_lembur', [3, 5])
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
                ->addColumn('status_lembur', function ($row) {
                    if ($row->status_lembur == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('detail', function ($row) use ($holding) {
                    $btn = '<a href="' . asset('detail_lembur_karyawan/' . $holding . '/' . $row->id) . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1" target="_blank">
                                <span class="mdi mdi-account-details-outline"></span>
                            </a>';
                    return $btn;
                })
                ->addColumn('form_lembur', function ($row) {
                    $btn = '<a href="' . asset('lembur/cetak_form_lembur/' . $row->id . '/' . $row->karyawan_id) . '" 
                                type="button" class="btn btn-icon btn-primary waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_lembur',
                    'detail',
                    'form_lembur',
                ])

                ->make(true);
        }
    }
    public function lembur_report_karyawan($holding, Request $request)
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
        return view('admin.report.lembur.lembur_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_lembur_riwayat_report($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = Lembur::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('LemburDetail')
            ->whereIn('status_lembur', [3, 5])
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
                ->addColumn('status_lembur', function ($row) {
                    if ($row->status_lembur == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('detail', function ($row) use ($holding) {
                    $btn = '' . asset('detail_lembur_karyawan/' . $holding . '/' . $row->id) . '';
                    return $btn;
                })
                ->addColumn('form_lembur', function ($row) {
                    $btn = '' . asset('lembur/cetak_form_lembur/' . $row->id . '/' . $row->karyawan_id) . '';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_lembur',
                    'detail',
                    'form_lembur',
                ])

                ->make(true);
        }
    }
}
