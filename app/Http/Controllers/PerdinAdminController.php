<?php

namespace App\Http\Controllers;

use App\Mail\SemuaIzin;
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
use App\Models\Inventaris;
use App\Models\Perdin;
use App\Models\PerdinDetail;
use App\Models\PerdinKendaraan;
use App\Models\PerdinKendaraanKantor;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class PerdinAdminController extends Controller
{
    public function index($holding, Request $request)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()) {
            return redirect('/');
        }
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
        $umum = PerdinKendaraan::orderBy('nama_kendaraan', 'asc')->get();
        $kantor = PerdinKendaraanKantor::orderBy('nama_kategori', 'asc')->get();
        $kendaraan_kantor = Inventaris::whereIn('id_kategori', $kantor->pluck('id_kategori'))
            ->orderBy('nama_inventaris', 'asc')
            ->get();
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
        return view('admin.perdin.perdin_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus,
            'kendaraan_kantor' => $kendaraan_kantor,
            'umum' => $umum

        ]);
    }
    public function dt_perdin($holding)
    {
        $table = Perdin::with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('PerdinDetail')
            ->with('PerdinTujuan')
            ->where('status_perdin', 2)
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
                ->addColumn('status_perdin', function ($row) {
                    if ($row->status_perdin == 2) {
                        return '<span class="badge bg-label-success">DISETUJUI</span>';
                    }
                })
                ->addColumn('tanggal_perdin', function ($row) {
                    return $row->tanggal_mulai . ' s/d ' . $row->tanggal_selesai;
                })
                ->addColumn('nama_site', function ($row) {
                    return $row->PerdinTujuan
                        ->pluck('nama_site')
                        ->filter()
                        ->implode(', ');
                })
                ->addColumn('approve', function ($row) {
                    $btn = '<button id="btn_setuju" 
                                data-id="' . $row->id . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    return $btn;
                })

                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_perdin',
                    'tanggal_perdin',
                    'nama_site',
                    'approve',
                ])

                ->make(true);
        }
    }
    public function perdin_approve_hrd($holding, Request $request)
    {
        // dd($request->all());
        if ($request->kendaraan_perdin == 1) {
            $id_kendaraan_umum_val = 'nullable';
            $id_kendaraan_kantor_val = 'nullable';
            $nama_kendaraan_kantor = null;
            $nama_kendaraan_umum = null;
            $nopol = null;
        } elseif ($request->kendaraan_perdin == 2) {

            $id_kendaraan_umum_val = 'required';
            $kendaraan_umum = PerdinKendaraan::where('id', $request->id_kendaraan_umum)->first();
            $nama_kendaraan_umum = $kendaraan_umum->nama_kendaraan;

            $id_kendaraan_kantor_val = 'nullable';
            $nama_kendaraan_kantor = null;
            $nopol = null;
        } else {
            $id_kendaraan_kantor_val = 'required';
            $kendaraan_kantor = Inventaris::where('id', $request->id_kendaraan_kantor)->first();
            $nama_kendaraan_kantor = $kendaraan_kantor->nama_inventaris;
            $nopol = $kendaraan_kantor->serial_number;

            $id_kendaraan_umum_val = 'nullable';
            $nama_kendaraan_umum = null;
        }
        $rules = [
            'signature' => 'required',
            'id_kendaraan_umum' => $id_kendaraan_umum_val,
            'id_kendaraan_kantor' => $id_kendaraan_kantor_val,

        ];
        $messages = [
            'signature.required' => 'Tanda tangan wajib diisi',
            'id_kendaraan_umum.required' => 'Nama kendaraan umum wajib diisi',
            'id_kendaraan_kantor.required' => 'Nama kendaraan kantor wajib diisi',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ]);
        }
        try {
            $data_perdin = Perdin::where('id', $request->id_perdin)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($data_perdin_detail);
            $count_perdin = Perdin::whereIn('status_perdin', [5, 3])->whereDate('created_at', $data_perdin->created_at)->where('id', '!=', $data_perdin->id)->get()->count();
            // dd($count_perdin);
            if ($count_perdin == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_perdin + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($data_perdin->created_at)->format('Ymd');

            $no_form = $data_perdin->User->KontrakKerja->holding_category . '/FPD' . '/' . $tgl_pengajuan . '/' . $last_count;

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
                $data_perdin->update([
                    'status_approve_hrd' => 1,
                    'no_form_perdin' => $no_form,
                    'status_perdin' => 3,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,

                    'id_kendaraan_kantor' => $request->id_kendaraan_kantor,
                    'nama_kendaraan_kantor' => $nama_kendaraan_kantor,
                    'nopol' => $nopol,

                    'kendaraan_perdin' => $request->kendaraan_perdin,
                    'id_kendaraan_umum' => $request->id_kendaraan_umum,
                    'nama_kendaraan_umum' => $nama_kendaraan_umum,

                    'waktu_approve_hrd' => now(),
                ]);
                PerdinDetail::where('id_perdin', $data_perdin->id)->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval perdin HRD',
                    'id_table' => $data_perdin->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'perdin',
                    'activity' => 'add',
                    'description' => 'Menyetujui perdin sebagai HRD ' .  $data_perdin->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);

                $email              = $data_perdin->User?->email ?? null;;
                $nama_penerim       = $data_perdin->nama_karyawan;
                $nama               = $user_karyawan->name;
                $tanggal_mulai      = Carbon::parse($data_perdin->tanggal_mulai)->format('d-m-Y');
                $tanggal_selesai    = Carbon::parse($data_perdin->tanggal_selesai)->format('d-m-Y');
                $judul              = 'Pengajuan Perjalanan Dinas Diterima';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan perjalanan dinas anda diterima oleh :';

                $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                $penutup            = 'Selamat menjalankan tugas dan semoga selamat sampai tujuan!';
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
                $data_perdin->update([
                    'status_approve_hrd' => 1,
                    'status_perdin' => 5,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval perdin HRD',
                    'id_table' => $data_perdin->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'perdin',
                    'activity' => 'add',
                    'description' => 'Menolak perdin sebagai HRD ' .  $data_perdin->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
                $email              = $data_perdin->User?->email ?? null;;
                $nama_penerim       = $data_perdin->nama_karyawan;
                $nama               = $user_karyawan->name;
                $tanggal_mulai      = Carbon::parse($data_perdin->tanggal_mulai)->format('d-m-Y');
                $tanggal_selesai    = Carbon::parse($data_perdin->tanggal_selesai)->format('d-m-Y');
                $judul              = 'Pengajuan Perjalanan Dinas Ditolak';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan perjalanan dinas anda ditolak oleh :';

                $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

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
                'message' => 'Pengajuan perdin berhasil Disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function dt_perdin_riwayat($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = Perdin::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('PerdinDetail')
            ->whereIn('status_perdin', [3, 5])
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

                ->addColumn('tanggal_perdin', function ($row) {
                    return $row->tanggal_mulai . ' s/d ' . $row->tanggal_selesai;
                })
                ->addColumn('status_perdin', function ($row) {
                    if ($row->status_perdin == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('kendaraan_perdin', function ($row) {
                    if ($row->kendaraan_perdin == 1) {
                        return 'KENDARAAN PRIBADI';
                    } elseif ($row->kendaraan_perdin == 2) {
                        return 'KENDARAAN UMUM';
                    } else {
                        return 'KENDARAAN KANTOR';
                    }
                })
                ->addColumn('form_perdin', function ($row) {
                    $btn = '<a href="' . asset('perdin/cetak_form_perdin/' . $row->id . '/' . $row->karyawan_id) . '" 
                                type="button" class="btn btn-icon btn-primary waves-effect waves-light m-1" target="_blank">
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
                ->addColumn('nama_site', function ($row) {
                    return $row->PerdinTujuan
                        ->pluck('nama_site')
                        ->filter()
                        ->implode(', ');
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_perdin',
                    'form_perdin',
                    'approve',
                    'nama_site',
                    'tanggal_perdin',
                ])

                ->make(true);
        }
    }
    public function perdin_report_karyawan($holding, Request $request)
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
        return view('admin.report.perdin.perdin_karyawan', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_perdin_report($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = Perdin::whereBetween('created_at', array($start, $end))
            ->with('User')
            ->with('Atasan1')
            ->with('Atasan2')
            ->with('PerdinDetail')
            ->whereIn('status_perdin', [3, 5])
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

                ->addColumn('tanggal_perdin', function ($row) {
                    return $row->tanggal_mulai . ' s/d ' . $row->tanggal_selesai;
                })
                ->addColumn('status_perdin', function ($row) {
                    if ($row->status_perdin == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })
                ->addColumn('kendaraan_perdin', function ($row) {
                    if ($row->kendaraan_perdin == 1) {
                        return 'KENDARAAN PRIBADI';
                    } elseif ($row->kendaraan_perdin == 2) {
                        return 'KENDARAAN UMUM';
                    } else {
                        return 'KENDARAAN KANTOR';
                    }
                })
                ->addColumn('form_perdin', function ($row) {
                    $btn = '' . asset('perdin/cetak_form_perdin/' . $row->id . '/' . $row->karyawan_id) . '';
                    return $btn;
                })
                ->addColumn('nama_site', function ($row) {
                    return $row->PerdinTujuan
                        ->pluck('nama_site')
                        ->filter()
                        ->implode(', ');
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_atasan1',
                    'jabatan_atasan2',
                    'status_perdin',
                    'form_perdin',
                    'approve',
                    'nama_site',
                    'tanggal_perdin',
                ])

                ->make(true);
        }
    }
}
