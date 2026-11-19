<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\CutiDetail;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KuotaCutiKaryawan;
use App\Models\Menu;
use App\Models\RoleUsers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use PDF;


class CutiReportController extends Controller
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
        return view('admin.cuti_report.index', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_report_cuti(Request $request, $holding)
    {
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $table = Cuti::with('User')
            ->whereBetween('created_at', [$now, $now1])
            ->with('CutiDetail')
            ->with('User')
            // ->where('status_cuti', 2)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)

                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-')
                        . ', (PENEMPATAN : ' . ($row->User->PenempatanKerja->site_name ?? '-') . ')';
                })
                ->addColumn('nama_atasan', function ($row) {
                    return $row->nama_atasan . ' (' . ($row->Atasan1->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-') . ')';
                })
                ->addColumn('nama_atasan2', function ($row) {
                    return $row->nama_atasan2 . ' (' . ($row->Atasan2->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-') . ')';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status_cuti == null) {
                        return '<span class="badge bg-label-warning">BELUM DISETUJUI</span>';
                    } elseif ($row->status_cuti == 1) {
                        return '<span class="badge bg-label-info">ATASAN 1 SETUJU</span>';
                    } elseif ($row->status_cuti == 2) {
                        return '<span class="badge bg-label-success">DISETUJUI</span>';
                    } elseif ($row->status_cuti == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    }
                })
                // ->addColumn('jabatan', function ($row) {
                //     return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-');
                // })
                // ->addColumn('divisi', function ($row) {
                //     return ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-');
                // })
                // ->addColumn('bagian', function ($row) {
                //     return ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                // })
                // ->addColumn('departemen', function ($row) {
                //     return ($row->User->KaryawanJabatanOne->Departemen->nama_departemen ?? '-');
                // })
                // ->addColumn('penempatan_kerja', function ($row) {
                //     return $row->User->PenempatanKerja->site_name ?? '-';
                // })
                // ->addColumn('jabatan_penyetuju', function ($row) {
                //     return ($row->KaryawanPenyetuju->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                //         . ' ' . ($row->KaryawanPenyetuju->KaryawanJabatanOne->Divisi->nama_divisi ?? '-');
                // })
                // ->addColumn('riwayat_pengubahan', function ($row) {
                //     return '<button data-id="' . $row->id . '" type="button" class="btn btn-info btn-sm" id="btn_daftar_tukar">

                //         <span class="mdi mdi-camera-switch"></span>

                //     </button>';
                // })
                // ->addColumn('detail', function ($row) {
                //     return '<a href="/report/mapping_shift_detail_report/' . $row->User->KontrakKerja->holding_code . '/' . $row->id . '" type="button" class="btn btn-success btn-sm" target="_blank">

                //         <span class="mdi mdi-account-details-outline"></span>

                //     </a>';
                //     // return  $row->User->kontrak_kerja;
                // })
                // ->addColumn('approved', function ($row) {
                //     if ($row->approved == 1) {
                //         return '<span class="badge bg-label-success">Disetujui</span>';
                //     } else {
                //         return '<span class="badge bg-label-info">Belum Disetujui</span>';
                //     }
                // })
                ->addColumn('lihat', function ($row) use ($holding) {
                    $id_karyawan = $row->User->id ?? '-';
                    $btn = '<a href="' . url("/cuti_report_detail/$row->id/$id_karyawan/$holding") . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1"><span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->addColumn('periode', function ($row) {
                    $cutiDetail = $row->CutiDetail()
                        // ->where('status', 1)
                        ->where('id_cuti', $row->id)
                        ->count();
                    return Carbon::parse($row->tanggal_mulai)->format('d-m-Y') . ' - ' . Carbon::parse($row->tanggal_selesai)->format('d-m-Y')
                        . ' (' . $cutiDetail . ' HARI) ';
                    // return $cutiDetail;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                // ->addColumn('detail', function ($row) {
                //     return '<a href="/report/mapping_shift_detail_report/' . $row->User->KontrakKerja->holding_code . '/' . $row->id . '" type="button" class="btn btn-success btn-sm" target="_blank">

                //         <span class="mdi mdi-account-details-outline"></span>

                //     </a>';
                // })
                ->rawColumns([
                    'jabatan_pengaju',
                    'lihat',
                    'status',
                    // 'jabatan_penyetuju',
                    // 'jabatan',
                    // 'divisi',
                    // 'bagian',
                    // 'departemen',
                    // 'riwayat_pengubahan',
                    // 'approved',
                    'periode',
                    'created_at',
                    // 'detail',
                    // 'penempatan_kerja'
                ])
                ->make(true);
        }
    }
    public function detail($id, $id_karyawan, $holding, Request $request)
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
        $cuti = Cuti::where('id', $id)->first();
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
        return view('admin.cuti_report.detail', [
            'holding' => $getHolding,
            'menus' => $menus,
            'karyawan' => $karyawan,
            'cuti' => $cuti,
            'kuota_awal' => $kuota_awal,

        ]);
    }
    public function dt_detail_report_cuti($id, $id_karyawan, $holding)
    {
        $table = CutiDetail::where('id_karyawan', $id_karyawan)
            ->where('id_cuti', $id)
            // ->whereNotNull('status')
            ->orderBy('created_at', 'ASC')
            ->get();
        // dd($table);

        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_cuti', function ($row) {
                    return $row->Cuti->nama_cuti ?? '-';
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
                    'updated_at',
                ])

                ->make(true);
        }
    }
    public function dt_laporan_kuota_cuti(Request $request, $holding)
    {
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        // dd($now);

        $table = KuotaCutiKaryawan::with(
            ['Karyawan', 'Karyawan.Cuti']
        )
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nomor_identitas_karyawan', function ($row) {
                    return $row->Karyawan->nomor_identitas_karyawan ?? '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->Karyawan->name ?? '-';
                })
                ->addColumn('total_terpakai', function ($row) use ($now1) {
                    $hasil =  $row->Karyawan?->Cuti?->where('status_cuti', 2)
                        ->where('created_at', '<', $now1)
                        ->whereNull('kategori_cuti_id')
                        ->sum('jumlah_hari') ?? 0;
                    return $hasil;
                })
                ->addColumn('terpakai', function ($row) use ($now, $now1) {
                    $hasil = $row->Karyawan?->Cuti?->where('status_cuti', 2)
                        ->whereBetween('created_at', [$now, $now1])
                        ->whereNull('kategori_cuti_id')
                        ->sum('jumlah_hari') ?? 0;
                    return $hasil;
                })
                ->addColumn('sisa', function ($row) use ($now1) {
                    $terpakai =  $row->Karyawan?->Cuti?->where('status_cuti', 2)
                        ->where('created_at', '<', $now1)
                        ->whereNull('kategori_cuti_id')
                        ->sum('jumlah_hari') ?? 0;
                    $kuota = $row->kuota;
                    $hasil = $kuota - $terpakai;
                    return $hasil;
                })
                ->rawColumns([
                    'nomor_identitas_karyawan',
                    'name',
                    'total_terpakai',
                    'terpakai',
                ])

                ->make(true);
        }
    }
    public function cetak_form_cuti_detail($id)
    {
        $cuti = Cuti::with('KategoriCuti', 'KaryawanPengganti', 'Atasan1', 'Atasan2')->where('id', $id)->first();
        $jumlah_hari = CutiDetail::where('id_cuti', $cuti->id)->count();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $cuti->karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $cuti->karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_cuti' => $cuti,
            'karyawan_jabatan' => $karyawn_jabatan,
            'user_karyawan' => $user_karyawan,
            'holding' => $holding,
            'jumlah_hari' => $jumlah_hari,
            // 'logo' => $data['logo'],
            // 'ttd_user' => $data['ttd_pemohon'],
            // 'ttd_atasan' => $data['ttd_atasan'],
            // 'ttd_atasan2' => $data['ttd_atasan2']
        ];
        $pdf = PDF::setOptions([
            'defaultFont' => 'Poppins, sans-serif',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'chroot' => [storage_path('app/public')],
        ])
            ->loadView('users/cuti/form_cuti', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_PENGAJUAN_CUTI_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
}
