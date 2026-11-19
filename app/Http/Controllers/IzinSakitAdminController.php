<?php

namespace App\Http\Controllers;

use App\Exports\IzinExport;
use App\Mail\SemuaIzin;
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
use App\Models\IzinSakit;
use App\Models\IzinSakitDetail;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class IzinSakitAdminController extends Controller
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
        return view('admin.karyawan.izin.sakit_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_sakit($holding)
    {
        $table = IzinSakit::with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('IzinSakitDetail')
            ->where('status_sakit', 2)
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
                ->addColumn('status_sakit', function ($row) {
                    if ($row->status_sakit == 2) {
                        return '<span class="badge bg-label-success">DISETUJUI</span>';
                    }
                })
                ->addColumn('surat_dokter', function ($row) {
                    $btn = '<a href="' . asset('storage/surat_dokter/' . $row->surat_dokter) . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->addColumn('approve', function ($row) {
                    $btn = '<button id="btn_setuju" 
                                data-id="' . $row->id . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    return $btn;
                })

                ->addColumn('tanggal_sakit', function ($row) {
                    return $row->tanggal_sakit . ' (' . $row->IzinSakitDetail->count() . ' HARI)';
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_sakit',
                    'surat_dokter',
                    'approve',
                ])

                ->make(true);
        }
    }
    public function sakit_approve_hrd($holding, Request $request)
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
            $data_sakit = IzinSakit::where('id', $request->id_sakit)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($data_sakit_detail);
            $count_sakit = IzinSakit::whereIn('status_sakit', [5, 3])->whereDate('created_at', $data_sakit->created_at)->where('id', '!=', $data_sakit->id)->get()->count();
            // dd($count_sakit);
            if ($count_sakit == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_sakit + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($data_sakit->created_at)->format('Ymd');

            $no_form = $data_sakit->User->KontrakKerja->holding_category . '/FIS' . '/' . $tgl_pengajuan . '/' . $last_count;

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
                $data_sakit->update([
                    'status_approve_hrd' => 1,
                    'no_form_sakit' => $no_form,
                    'status_sakit' => 3,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                IzinSakitDetail::where('id_sakit', $data_sakit->id)->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval sakit HRD',
                    'id_table' => $data_sakit->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_keluar',
                    'activity' => 'add',
                    'description' => 'Menyetujui izin sakit sebagai HRD ' .  $data_sakit->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
                $email              = $data_sakit->User?->email ?? null;;
                $nama_penerim       = $data_sakit->nama_karyawan;
                $nama               = $user_karyawan->name;
                $tanggal_mulai      = Carbon::parse($data_sakit->tanggal_mulai)->format('d-m-Y');
                $judul              = 'Pengajuan Izin Sakit Diterima';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan izin sakit anda diterima oleh :';

                $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . '';

                $penutup            = 'Semoga Lekas sembuh!';
                $link               = 'https://hrd.sumberpangan.store/';
                Cache::put("Wawancara:$email", true, now()->addHours(2));
                if ($email) {
                    Mail::mailer('hrd')->to($email)->queue(new SemuaIzin(
                        $judul,
                        $nama_penerim,
                        $perihal,
                        $nama,
                        $waktu,
                        $penutup,
                        $link,
                    ));
                }
            } else {
                $data_sakit->update([
                    'status_approve_hrd' => 1,
                    'status_sakit' => 5,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval sakit HRD',
                    'id_table' => $data_sakit->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_keluar',
                    'activity' => 'add',
                    'description' => 'Menolak izin sakit sebagai HRD ' .  $data_sakit->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
                $email              = $data_sakit->User?->email ?? null;
                $nama_penerim       = $data_sakit->nama_karyawan;
                $nama               = $user_karyawan->name;
                $tanggal_mulai      = Carbon::parse($data_sakit->tanggal_mulai)->format('d-m-Y');
                $judul              = 'Pengajuan Izin Sakit Ditolak';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan izin sakit anda ditolak oleh :';

                $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . '';

                $penutup            = 'Segera berkoordinasi dengan atasan anda untuk penjelasan yang lebih lanjut.';
                $link               = 'https://hrd.sumberpangan.store/';
                Cache::put("Wawancara:$email", true, now()->addHours(2));
                if ($email) {
                    Mail::mailer('hrd')->to($email)->queue(new SemuaIzin(
                        $judul,
                        $nama_penerim,
                        $perihal,
                        $nama,
                        $waktu,
                        $penutup,
                        $link,
                    ));
                }
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
    public function dt_sakit_riwayat($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = IzinSakit::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('IzinSakitDetail')
            ->whereIn('status_sakit', [3, 5])
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

                ->addColumn('tanggal_sakit', function ($row) {
                    return $row->tanggal_sakit . ' (' . $row->IzinSakitDetail->count() . ' HARI)';
                })
                ->addColumn('status_sakit', function ($row) {
                    if ($row->status_sakit == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })

                ->addColumn('surat_dokter', function ($row) {
                    $btn = '<a href="' . asset('storage/surat_dokter/' . $row->surat_dokter) . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->addColumn('form_sakit', function ($row) {
                    $btn = '<a href="' . asset('izin/cetak_form_sakit/' . $row->id . '/' . $row->karyawan_id) . '" 
                                type="button" class="btn btn-icon btn-primary waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_sakit',
                    'surat_dokter',
                    'form_sakit',
                ])

                ->make(true);
        }
    }
    public function sakit_report_karyawan($holding, Request $request)
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
        return view('admin.report.izin.sakit_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_sakit_report($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = IzinSakit::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('IzinSakitDetail')
            ->whereIn('status_sakit', [3, 5])
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
                        . ' ' . ($row->AtasanHRD->KaryawanJabatanOne->Bagian->nama_bagian ?? '');
                })

                ->addColumn('tanggal_sakit', function ($row) {
                    return $row->tanggal_sakit . ' (' . $row->IzinSakitDetail->count() . ' HARI)';
                })
                ->addColumn('status_sakit', function ($row) {
                    if ($row->status_sakit == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })

                ->addColumn('surat_dokter', function ($row) {
                    $btn = '' . asset('storage/surat_dokter/' . $row->surat_dokter) . '';
                    return $btn;
                })
                ->addColumn('form_sakit', function ($row) {
                    $btn = '' . asset('izin/cetak_form_sakit/' . $row->id . '/' . $row->karyawan_id) . '';
                    return $btn;
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_hrd',
                    'status_sakit',
                    'surat_dokter',
                    'form_sakit',
                ])

                ->make(true);
        }
    }
}
