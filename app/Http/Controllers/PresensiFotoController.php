<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AttendanceLog;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\Menu;
use App\Models\PresensiFoto;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use PDF;


class PresensiFotoController extends Controller
{
    public function index($id)
    {
        // dd($me);
        // abort(404);
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();

            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        // dd($jam_kerja);
        // dd($finger);

        return view('users.presensi_foto.index', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function get_presensi(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_presensi = PresensiFoto::where('karyawan_id', $user_karyawan->id)
                ->where('status_presensi', 0)
                ->get();
            // dd($data_presensi);
            return response()->json([
                'code' => 200,
                'data' => $data_presensi

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_presensi_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_keluar = PresensiFoto::where('karyawan_id', $user_karyawan->id)
                ->where('status_presensi', '!=', 0)
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_keluar);
            return response()->json([
                'code' => 200,
                'data' => $data_keluar

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_pengajuan_presensi(Request $request, $id)
    {
        // dd($request->all());
        try {
            $rules = [
                'id_karyawan' => 'required',
                'foto_presensi' => 'required',
                'signature' => 'required',
                'keterangan_presensi' => 'required',
                // 'lokasi_presensi' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',

            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'karyawan_pengganti.required' => 'Karyawan Pengganti wajib diisi',
                // 'tanggal_presensi.required' => 'Jam perkiraan kembali wajib diisi',
                'foto_presensi.required' => 'Foto Presensi wajib diunggah',
                'signature.required' => 'Tanda Tangan wajib diisi',
                'keterangan_presensi.required' => 'Keterangan Presensi wajib diisi',
                // 'lokasi_presensi.required' => 'Lokasi Presensi wajib diisi',
                'longitude.required' => 'longitude gagal didapatkan',
                'latitude.required' => 'latitude gagal didapatkan',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'errors' => $validator->errors(),
                ]);
            }
            if ($request->foto_presensi != null) {
                $file = $request->file('foto_presensi')->store('public/foto_presensi');
                $file_save = basename($file);
            }

            $kode = Uuid::uuid4();
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'signature_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );

            $pengaju = Karyawan::where('id', $request->id_karyawan)->first();

            $add_presensi = new PresensiFoto();
            $add_presensi->id = $kode;
            $add_presensi->karyawan_id = $request->id_karyawan;
            $add_presensi->karyawan_nik = $pengaju->nomor_identitas_karyawan;
            $add_presensi->nama_karyawan = $request->name;
            $add_presensi->ttd_user = $fileName;
            $add_presensi->foto_presensi = $file_save;
            $add_presensi->keterangan_presensi = $request->keterangan_presensi;
            // $add_presensi->lokasi_presensi = $request->lokasi_presensi;
            $add_presensi->longitude = $request->longitude;
            $add_presensi->latitude = $request->latitude;


            // $add_presensi->keterangan_presensi = $request->keterangan_presensi;
            $add_presensi->status_presensi = 0;
            $add_presensi->created_at = date('Y-m-d H:i:s');
            $add_presensi->save();

            // SAVE QUICK PANEL

            $holdings = Holding::where('id', $pengaju->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $kode,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'presensi_foto',
                'activity' => 'add',
                'description' => 'Mengajukan presensi foto',
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function detail_presensi($id, $id_presensi)
    {
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();

            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $data = PresensiFoto::where('id', $id_presensi)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.presensi_foto.presensi_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_presensi,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_presensi($id, Request $request)
    {
        // dd($request->all());
        $query = PresensiFoto::where('id', $request->id_presensi_hapus)->first();
        $id_presensi = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_presensi->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus presensi',
            'id_table' => $request->id_presensi_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'presensi_foto',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan presensi ' .  $query->tanggal_presensi,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_presensi_gagal', 'Data presensi Tidak Ditemukan');
        } elseif ($query->status_presensi != 0) {
            $request->session()->flash('hapus_presensi_gagal', 'presensi sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_presensi_hapus)->delete();
            if (filled($query->foto_presensi)) {
                Storage::disk('public')->delete('foto_presensi/' . $query->foto_presensi);
            }

            $query->delete();
            $request->session()->flash('hapus_presensi_sukses', 'Form presensi Berhasil Dihapus');
        }
        return redirect('presensi_foto/index/' . $id);
    }
    public function presensi_foto_karyawan($holding, Request $request)
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
        return view('admin.karyawan.presensi_foto.index', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_presensi($holding)
    {
        $table = PresensiFoto::with('User')
            ->where('status_presensi', 0)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('status_presensi', function ($row) {
                    if ($row->status_presensi == 0) {
                        return '<span class="badge bg-label-info">DIAJUKAN</span>';
                    }
                })
                ->addColumn('approve', function ($row) {
                    $btn = '<button id="btn_setuju" 
                                data-id="' . $row->id . '" 
                                data-jam="' . Carbon::parse($row->created_at)->format('H:i') . '" 
                                data-tanggal="' . Carbon::parse($row->created_at)->locale('id')->translatedFormat('Y-m-d') . '" 
                                data-foto="' . $row->foto_presensi . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    return $btn;
                })
                ->addColumn('lokasi_presensi', function ($row) {
                    $btn = '<a href="https://www.google.com/maps?q=' . urlencode($row->latitude . ',' . $row->longitude) . '" 
                                type="button" class="btn btn-icon btn-success waves-effect waves-light m-1" target="_blank">
                                <span class="mdi mdi-map-marker"></span>
                            </a>';
                    return $btn;
                })

                ->addColumn('tanggal_presensi', function ($row) {
                    return $row->created_at
                        ? Carbon::parse($row->created_at)
                        ->locale('id')
                        ->translatedFormat('d-m-Y')
                        : '-';
                })

                ->addColumn('jam_presensi', function ($row) {
                    return $row->created_at
                        ? Carbon::parse($row->created_at)->format('H:i:s')
                        : '-';
                })
                ->rawColumns([
                    'lokasi_presensi',
                    'jabatan_pengaju',
                    'status_presensi',
                    'approve',
                ])

                ->make(true);
        }
    }
    public function presensi_approve_hrd($holding, Request $request)
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
            $data_presensi = PresensiFoto::where('id', $request->id_presensi)->first();
            // dd($data_presensi->karyawan_nik);
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $count_presensi = PresensiFoto::whereIn('status_presensi', [5, 3])->whereDate('created_at', $data_presensi->created_at)->where('id', '!=', $data_presensi->id)->get()->count();
            // dd($count_presensi);
            if ($count_presensi == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_presensi + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($data_presensi->created_at)->format('Ymd');


            $no_form = $data_presensi->User->KontrakKerja->holding_category . '/FPF' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($no_form);

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
            // $data = 
            // dd($data);
            if ($request->setuju == 1) {
                $data_presensi->update([
                    'status_approve_hrd' => 1,
                    'no_form_presensi' => $no_form,
                    'status_presensi' => 1,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'jam_presensi' => Carbon::parse($request->tanggal_presensi)->locale('id')->translatedFormat('Y-m-d') . ' ' . $request->jam_presensi . ':0',
                    'waktu_approve_hrd' => now(),
                ]);

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval presensi HRD',
                    'id_table' => $data_presensi->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'presensi_foto',
                    'activity' => 'add',
                    'description' => 'Menyetujui presensi sebagai HRD ' .  $data_presensi->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
                AttendanceLog::create([
                    'EnrollNumber' => $data_presensi->karyawan_nik,
                    'LogTime' => Carbon::parse($request->tanggal_presensi)
                        ->format('Y-m-d') . ' ' . $request->jam_presensi . ':00',
                    'VerifyMode' => 1,
                    'InOutMode' => 1,
                    'WorkCode' => 0,
                    'MachineIp' => '192.168.1.1',
                ]);
                // dd($attendance->getAttributes());
            } else {
                $data_presensi->update([
                    'status_approve_hrd' => 1,
                    'status_presensi' => 5,
                    'id_user_hrd' => $user_karyawan->id,
                    'nama_hrd' => $user_karyawan->name,
                    'ttd_hrd' => $fileName,
                    'waktu_approve_hrd' => now(),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval presensi HRD',
                    'id_table' => $data_presensi->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'presensi_foto',
                    'activity' => 'add',
                    'description' => 'Menolak presensi sebagai HRD ' .  $data_presensi->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => null
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan presensi berhasil Disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function dt_presensi_riwayat($holding, Request $request)
    {
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = PresensiFoto::select(
            'presensi_foto.*',
            DB::raw("
                    CASE
                        WHEN status_presensi = '1' THEN jam_presensi
                        WHEN status_presensi = '5' THEN created_at
                    END AS jam_masuk
                ")
        )
            ->whereIn('status_presensi', [1, 5])
            ->whereBetween(
                DB::raw("
                    CASE
                        WHEN status_presensi = '1' THEN jam_presensi
                        WHEN status_presensi = '5' THEN created_at
                    END
                "),
                [$start, $end]
            )
            ->with('User')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('tanggal_presensi', function ($row) {
                    return $row->created_at
                        ? Carbon::parse($row->created_at)
                        ->locale('id')
                        ->translatedFormat('d-m-Y')
                        : '-';
                })

                ->addColumn('jam_presensi', function ($row) {
                    return $row->jam_presensi
                        ? Carbon::parse($row->jam_presensi)->format('H:i:s')
                        : '-';
                })
                ->addColumn('status_presensi', function ($row) {
                    if ($row->status_presensi == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })

                ->addColumn('foto_presensi', function ($row) {

                    $btn = '<a href="' . asset('storage/foto_presensi/' . $row->foto_presensi) . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->addColumn('form_presensi', function ($row) {
                    if ($row->status_presensi == 1) {
                        $btn = '<a href="' . asset('presensi_foto/cetak_form_presensi/' . $row->id . '/' . $row->karyawan_id) . '" 
                                type="button" class="btn btn-icon btn-primary waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                        return $btn;
                    } else {

                        return '';
                    }
                })
                ->addColumn('lokasi_presensi', function ($row) {
                    $btn = '<a href="https://www.google.com/maps?q=' . urlencode($row->latitude . ',' . $row->longitude) . '" 
                                type="button" class="btn btn-icon btn-success waves-effect waves-light m-1" target="_blank">
                                <span class="mdi mdi-map-marker"></span>
                            </a>';
                    return $btn;
                })->addColumn('approve', function ($row) {
                    $btn = '<button id="btn_setuju" 
                                data-id="' . $row->id . '" 
                                data-jam="' . Carbon::parse($row->created_at)->format('H:i') . '" 
                                data-tanggal="' . Carbon::parse($row->created_at)->locale('id')->translatedFormat('Y-m-d') . '" 
                                data-foto="' . $row->foto_presensi . '" 
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-pencil-outline"></span>
                            </button>';
                    return $btn;
                })

                ->rawColumns([
                    'jabatan_pengaju',
                    'status_presensi',
                    'lokasi_presensi',
                    'foto_presensi',
                    'form_presensi',
                    'tanggal_presensi',
                    'jam_presensi',
                    'approve',
                ])

                ->make(true);
        }
    }
    public function cetak_form_presensi($id, $karyawan_id)
    {
        $presensi = PresensiFoto::with('AtasanHRD')->where('id', $id)->first();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_presensi' => $presensi,
            'karyawan_jabatan' => $karyawn_jabatan,
            'user_karyawan' => $user_karyawan,
            'holding' => $holding,
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
            'chroot' => public_path(), // penting!
        ])
            ->loadView('users/presensi_foto/presensi_foto_form', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_PULANG_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
    public function presensi_foto_report($holding, Request $request)
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
        return view('admin.report.presensi_foto.index', [
            'holding' => $getHolding,
            'menus' => $menus

        ]);
    }
    public function dt_presensi_report($holding, Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $table = PresensiFoto::select(
            'presensi_foto.*',
            DB::raw("
                    CASE
                        WHEN status_presensi = '1' THEN jam_presensi
                        WHEN status_presensi = '5' THEN created_at
                    END AS jam_masuk
                ")
        )
            ->whereIn('status_presensi', [1, 5])
            ->whereBetween(
                DB::raw("
                    CASE
                        WHEN status_presensi = '1' THEN jam_presensi
                        WHEN status_presensi = '5' THEN created_at
                    END
                "),
                [$start, $end]
            )
            ->with('User')
            ->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('jabatan_pengaju', function ($row) {
                    return ($row->User->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-')
                        . ' ' . ($row->User->KaryawanJabatanOne->Divisi->nama_divisi ?? '-')
                        . ', BAGIAN ' . ($row->User->KaryawanJabatanOne->Bagian->nama_bagian ?? '-');
                })
                ->addColumn('jabatan_hrd', function ($row) {
                    return ($row->AtasanHRD->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '')
                        . ' ' . ($row->AtasanHRD->KaryawanJabatanOne->Divisi->nama_divisi ?? '')
                        . '  ' . ($row->AtasanHRD->KaryawanJabatanOne->Bagian->nama_bagian ?? '');
                })
                ->addColumn('tanggal_presensi', function ($row) {
                    return Carbon::parse($row->created_at)->locale('id')->translatedFormat('d-m-Y');
                })
                ->addColumn('jam_presensi', function ($row) {
                    return Carbon::parse($row->jam_presensi)->format('H:i:s');
                })
                ->addColumn('status_presensi', function ($row) {
                    if ($row->status_presensi == 5) {
                        return '<span class="badge bg-label-danger">DITOLAK</span>';
                    } else {
                        return '<span class="badge bg-label-success">DISETUJUI HRD</span>';
                    }
                })

                ->addColumn('foto_presensi', function ($row) {

                    $btn = '' . asset('storage/foto_presensi/' . $row->foto_presensi) . '';
                    return $btn;
                })
                ->addColumn('lokasi_presensi', function ($row) {
                    if ($row->latitude != null) {
                        $btn = 'https://www.google.com/maps?q=' . urlencode($row->latitude . ',' . $row->longitude) . '';
                        return $btn;
                    }
                })
                ->addColumn('form_presensi', function ($row) {
                    if ($row->status_presensi == 1) {
                        $btn = '' . asset('presensi_foto/cetak_form_presensi/' . $row->id . '/' . $row->karyawan_id) . '';
                        return $btn;
                    } else {

                        return '';
                    }
                })
                ->rawColumns([
                    'jabatan_pengaju',
                    'jabatan_hrd',
                    'status_presensi',
                    'lokasi_presensi',
                    'foto_presensi',
                    'form_presensi',
                    'tanggal_presensi',
                    'jam_presensi',
                ])

                ->make(true);
        }
    }
}
