<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\MappingShift;
use App\Models\MappingShiftHead;
use App\Models\MappingShiftTukarAtasan;
use App\Models\MappingShiftTukarAtasanDetail;
use App\Models\Menu;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class MappingShiftReportController extends Controller
{
    //
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
        return view('admin.mapping_shift_report.index', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_laporan_shift(Request $request, $holding)
    {
        // dd($request->all());
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $table = MappingShiftHead::with('KaryawanPengaju')->whereBetween('start_date', [$now, $now1])->get();
        // dd($now, $now1);
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
                ->addColumn('jabatan_penyetuju', function ($row) {
                    return ($row->KaryawanPenyetuju->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->KaryawanPenyetuju->KaryawanJabatanOne->Divisi->nama_divisi ?? '-');
                })
                ->addColumn('riwayat_pengubahan', function ($row) {
                    return '<button data-id="' . $row->id . '" type="button" class="btn btn-info btn-sm" id="btn_daftar_tukar">

                        <span class="mdi mdi-camera-switch"></span>

                    </button>';
                })
                ->addColumn('detail', function ($row) {
                    return '<a href="/report/mapping_shift_detail_report/' . $row->KaryawanPengaju->KontrakKerja->holding_code . '/' . $row->id . '" type="button" class="btn btn-success btn-sm" target="_blank">

                        <span class="mdi mdi-account-details-outline"></span>

                    </a>';
                    // return  $row->KaryawanPengaju->kontrak_kerja;
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
                    'jabatan_penyetuju',
                    'jabatan',
                    'divisi',
                    'bagian',
                    'departemen',
                    'riwayat_pengubahan',
                    'detail',
                    'approved',
                    'created_at',
                    'penempatan_kerja'
                ])
                ->make(true);
        }
    }
    public function mapping_shift_detail_report($holding, Request $request, $id)
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
        $head = MappingShiftHead::where('id', $id)->first();
        return view('admin.mapping_shift_report.mapping_shift_detail_report', [
            'holding' => $getHolding,
            'menus' => $menus,
            'id' => $id,
            'head' => $head,

        ]);
    }

    public function dt_laporan_detail_shift($holding, $id)

    {
        $table = MappingShift::where('id_head', $id)->whereNotNull('is_active')->with('Karyawan')->with('Shift')->get();
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
    // public function dt_laporan_detail_shift2($holding, $id)

    // {
    //     $table = MappingShift::where('id_head', $id)->whereNotNull('is_active')->with('Karyawan')->with('Shift')->get();
    //     // dd($table->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan);
    //     // foreach ($table as $row) {

    //     //     dd($row->Karyawan->Holding);
    //     // }
    //     // return response()->json([
    //     //     'holding_param' => $getHolding,
    //     //     'is_ajax' => request()->ajax(),
    //     // ]);
    //     // // dd($table);
    //     if (request()->ajax()) {
    //         return DataTables::of($table)

    //             ->addColumn('jabatan_karyawan', function ($row) {
    //                 return ($row->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
    //                     . ' ' . ($row->Karyawan->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
    //                     . ', BAGIAN ' . ($row->Karyawan->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
    //             })
    //             ->addColumn('jabatan', function ($row) {
    //                 return ($row->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-');
    //             })
    //             ->addColumn('divisi', function ($row) {
    //                 return ($row->Karyawan->KaryawanJabatanOne->Divisi->nama_divisi ?? '-');
    //             })
    //             ->addColumn('bagian', function ($row) {
    //                 return ($row->Karyawan->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
    //             })
    //             ->addColumn('departemen', function ($row) {
    //                 return ($row->Karyawan->KaryawanJabatanOne->Departemen->nama_departemen ?? '-');
    //             })
    //             ->addColumn('hari_masuk', function ($row) {
    //                 return Carbon::parse($row->tanggal_masuk)->locale('id')->translatedFormat('l');
    //             })
    //             ->addColumn('penempatan_kerja', function ($row) {
    //                 return                         Carbon::parse($row->tanggal_masuk)
    //                     ->locale('id')
    //                     ->translatedFormat('l');
    //             })
    //             ->addColumn('jam_masuk', function ($row) {
    //                 return $row->Shift->jam_masuk;
    //             })
    //             ->addColumn('jam_keluar', function ($row) {
    //                 return $row->Shift->jam_keluar;
    //             })

    //             ->addColumn('jam_keluar', function ($row) {
    //                 return $row->Shift->jam_keluar;
    //             })
    //             ->addColumn('pengubahan', function ($row) {
    //                 if ($row->is_active == 1) {
    //                     return '<span class="badge bg-label-secondary">ORIGINAL</span>';
    //                 } elseif ($row->is_active == 2) {
    //                     return '<span class="badge bg-label-info">DIUBAH&nbsp;PEGAWAI</span>';
    //                 } elseif ($row->is_active == 3) {
    //                     return '<span class="badge bg-label-success">DIUBAH&nbsp;ATASAN</span>';
    //                 }
    //             })
    //             ->rawColumns([
    //                 'jabatan_karyawan',
    //                 'jabatan',
    //                 'divisi',
    //                 'bagian',
    //                 'departemen',
    //                 'penempatan_kerja',
    //                 'jam_masuk',
    //                 'jam_keluar',
    //                 'pengubahan',
    //             ])
    //             ->make(true);
    //     }
    // }
    public function dt_laporan_tukar($holding, $id)
    {
        $head = MappingShiftHead::where('id', $id)->first();
        $table = MappingShiftTukarAtasan::where('id_head', $head->id)->with('Pengaju')->where('approved', 1)->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('detail', function ($row) {
                    return '<a href="/report/mapping_tukar_detail_report/' . $row->Pengaju->KontrakKerja->holding_code . '/' . $row->id . '" type="button" class="btn btn-success btn-sm" target="_blank">

                        <span class="mdi mdi-account-details-outline"></span>

                    </a>';
                    // return  $row->KaryawanPengaju->kontrak_kerja;
                })
                ->rawColumns([
                    'created_at',
                    'detail',
                ])
                ->make(true);
        }
    }
    public function mapping_tukar_detail_report($holding, Request $request, $id)
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
        $head = MappingShiftTukarAtasan::where('id', $id)->first();
        return view('admin.mapping_shift_report.mapping_tukar_detail_report', [
            'holding' => $getHolding,
            'menus' => $menus,
            'id' => $id,
            'head' => $head,

        ]);
    }
    public function dt_laporan_detail_tukar($holding, $id)

    {
        $table = MappingShiftTukarAtasanDetail::where('id_tukar_atasan_utama', $id)->whereNotNull('is_active')->with('Original')->get();
        // dd($table->Original->nama_shift);
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
                ->addColumn('hari_masuk', function ($row) {
                    return Carbon::parse($row->tanggal_masuk)->locale('id')->translatedFormat('l');
                })
                ->addColumn('pengubahan', function ($row) {
                    if ($row->is_active == 1) {
                        return '<span class="badge bg-label-secondary">ORIGINAL</span>';
                    } elseif ($row->is_active == 2) {
                        return '<span class="badge bg-label-info">DIUBAH PEGAWAI</span>';
                    } elseif ($row->is_active == 3) {
                        return '<span class="badge bg-label-success">DIUBAH ATASAN</span>';
                    }
                })
                ->addColumn('original', function ($row) {
                    if ($row->is_active == 1) {
                        return '-';
                    } elseif ($row->is_active == 2) {
                        return $row->Original->nama_shift ?? '-';
                    } elseif ($row->is_active == 3) {
                        return $row->Original->nama_shift ?? '-';
                    }
                })

                ->rawColumns([
                    'hari_masuk',
                    'pengubahan',
                    'original',
                ])
                ->make(true);
        }
    }
}
