<?php

namespace App\Http\Controllers;

use App\Mail\SemuaIzin;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Izinpulang;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use PDF;



class IzinPulangController extends Controller
{
    public function pulang($id)
    {
        // dd($me);
        // abort(404);
        session(['url.intended' => url()->previous()]);
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

        $jam_kerja = MappingShift::with('Shift')
            ->where('karyawan_id', $user_karyawan->id)
            ->whereDate('tanggal_masuk', now())
            ->whereNotNull('is_active')
            ->first();
        // dd($jam_kerja);

        if ($user_karyawan->shift == 'NON SHIFT') {
            $pulang =  Shift::where('nama_shift', 'NON SHIFT')->first();
            $jam_min_pulang = $pulang?->jam_pulang_cepat;
        } else {
            $jam_min_pulang = $jam_kerja?->Shift?->jam_pulang_cepat;
        }
        if ($user_karyawan->shift == 'NON SHIFT') {


            $shift =  Shift::where('nama_shift', 'NON SHIFT')->first();
        } else {
            $mapping_shift = MappingShift::where('tanggal_masuk', today())->whereNotNull('is_active')->first();
            $shift =  Shift::where('id', $mapping_shift->shift_id)->first();
        }
        $today = now()->startOfDay();
        $start = $today->copy()->setTimeFromTimeString($shift->jam_min_masuk);
        $end = $today->copy()
            ->setTimeFromTimeString($shift->jam_terlambat)
            ->addHours(4)
            ->addMinutes(10);
        if (!empty($start) && !empty($end)) {
            $finger = $user_karyawan->Absensi->whereBetween('LogTime', [$start, $end])->first();
            // dd($finger);
        } else {
            $finger = null;
        }
        // dd($finger);

        return view('users.izin.pulang', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'finger' => $finger,
            'menu_id' => $id,
            'jam_min_pulang' => $jam_min_pulang,
        ]);
    }
    public function proses_pengajuan_pulang(Request $request, $id)
    {
        // dd($request->all());
        try {
            $rules = [
                'id_karyawan' => 'required',
                'jam_pulang' => 'required',
                'keterangan_pulang' => 'required',
                'atasan_1' => 'required',
                'atasan_2' => 'required',
                'signature' => 'required',

            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'jam_pulang.required' => 'Jam pulang wajib diisi',
                'signature.required' => 'Tanda Tangan wajib diisi',
                'keterangan_pulang.required' => 'Alasan pulang wajib diisi',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'errors' => $validator->errors(),
                ]);
            }
            if ($request->jam_min_pulang < $request->jam_pulang) {
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
                $atasan1 = Karyawan::where('id', $request->atasan_1)->first();
                $atasan2 = Karyawan::where('id', $request->atasan_2)->first();

                $add_izin = new IzinPulang();
                $add_izin->id = $kode;
                $add_izin->karyawan_id = $request->id_karyawan;
                $add_izin->karyawan_nik = $pengaju->nomor_identitas_karyawan;
                $add_izin->nama_karyawan = $request->name;
                $add_izin->ttd_user = $fileName;

                $add_izin->jam_pulang = $request->jam_pulang;
                $add_izin->jam_min_pulang = $request->jam_min_pulang;

                $add_izin->keterangan_pulang = $request->keterangan_pulang;
                $add_izin->status_pulang = 0;

                $add_izin->id_user_atasan = $request->atasan_1;
                $add_izin->nama_atasan = $atasan1->name;

                $add_izin->id_user_atasan2 = $request->atasan_2;
                $add_izin->nama_atasan2 = $atasan2->name;

                $add_izin->created_at = date('Y-m-d H:i:s');
                $add_izin->save();

                // SAVE QUICK PANEL
                $quick = new QuickPanel();
                $quick->id_quick = Uuid::uuid4();
                $quick->user_id_from = $request->id_karyawan;
                $quick->user_id_to = $request->atasan_1;
                $quick->title_quick = 'Pengajuan Izin pulang';
                $quick->message_quick = 'Ada Pengajuan Izin pulang Baru Dari ' . $request->name;
                $quick->status = 0;
                $quick->quick_type = 'izin_pulang';
                $quick->menu_id = Auth::user()->menu_active;
                $quick->content_id = $add_izin->id;
                $quick->created_at = date('Y-m-d H:i:s');
                $quick->updated_at = date('Y-m-d H:i:s');
                $quick->save();

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
                    'table' => 'izin_pulang',
                    'activity' => 'add',
                    'description' => 'Mengajukan izin pulang pukul ' . $request->jam_pulang,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
                $email_atasan = $atasan1?->email_kantor ?: $atasan1?->email;
                // dd($email_atasan);
                $email              = $email_atasan;
                $nama_penerim       = $atasan1->name;
                $nama               = $request->name;
                // $tanggal      = Carbon::parse($add_izin->tanggal_terlambat)->format('d-m-Y');
                $judul              = 'Pengajuan Izin Pulang';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin pulang dari :';

                $waktu              = 'Pada Jam ' . $add_izin->jam_pulang . '';

                $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                $link               = url('/izin/approve_pulang_detail/' . $kode);
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
                return response()->json([
                    'code' => 200,
                    'message' => 'Pengajuan izin berhasil disimpan.',
                ]);
            } else {
                return response()->json([
                    'code' => 510,
                    'message' => 'Jam izin kurang dari batas minimal pulang',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_pulang(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_izin = IzinPulang::where('karyawan_id', $user_karyawan->id)
                ->where('status_pulang', 0)
                ->get();
            // dd($data_izin);
            return response()->json([
                'code' => 200,
                'data' => $data_izin

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_pulang_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_pulang = IzinPulang::where('karyawan_id', $user_karyawan->id)
                ->where('status_pulang', '!=', 0)
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_pulang);
            return response()->json([
                'code' => 200,
                'data' => $data_pulang

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function detail_pulang($id, $id_pulang)
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
        $data = Izinpulang::where('id', $id_pulang)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.izin.pulang_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_pulang,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_pulang($id, Request $request)
    {
        // dd($request->all());
        $query = IzinPulang::where('id', $request->id_pulang_hapus)->first();
        $id_pulang = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_pulang->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus pulang',
            'id_table' => $request->id_pulang_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'izin_pulang',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan pulang ' .  $query->jam_pulang,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_pulang_gagal', 'Data pulang Tidak Ditemukan');
        } elseif ($query->status_pulang != 0) {
            $request->session()->flash('hapus_pulang_gagal', 'pulang sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_pulang_hapus)->delete();

            $query->delete();
            $request->session()->flash('hapus_pulang_sukses', 'Form Pulang Berhasil Dihapus');
        }
        return redirect('izin/pulang/' . $id);
    }
    public function pulang_approval($menu_id)
    {
        // dd($menu_id);
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
        // dd($all_menu_bar);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.izin.pulang_approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_pulang_approval(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_pulang = IzinPulang::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_pulang', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_pulang', 1);
                    });
            })->get();
            // dd($data_pulang);
            return response()->json([
                'code' => 200,
                'data' => $data_pulang

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_pulang_riwayat_approval(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_pulang = IzinPulang::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_pulang', '!=', 0)
                        ->where('status_approve_atasan1', '!=', null);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_pulang', '!=', 0)
                            ->where('status_approve_atasan2', '!=', null);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
            // dd($data_pulang);
            return response()->json([
                'code' => 200,
                'data' => $data_pulang

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_pulang_detail($id, Request $request)
    {
        session(['url.intended' => url()->previous()]);
        try {
            // dd('ss');
            if (!Auth::check()) {
                $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
                return redirect()->route('login');
            }
            $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
            if (count($get_role) == 0) {
                $roleId = null;
            } else {
                $roleId = $get_role;
            }
            if ($roleId == null) {
                $all_menus = collect();
                $all_menu_bar = collect();
                $all_menu_main = collect();
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
            $data_pulang = IzinPulang::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            // $presensi = $user_karyawan->Absensi->where('LogTime',)
            $menu_id = Auth::user()->menu_active;
            return view('users.izin.pulang_approval_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'data' => $data_pulang,
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'user_karyawan' => $user_karyawan,
                'menu_id' => $menu_id
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
            return redirect()->back()->with('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
        }
    }
    public function get_pulang_detail($id)
    {
        try {
            $data_pulang = IzinPulang::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            return response()->json([
                'code' => 200,
                'data' => $data_pulang,
                'user' => $user_karyawan,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function tolak_pengajuan_pulang(Request $request)
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
        // dd($request->all());
        try {
            $data_pulang = IzinPulang::where('id', $request->id_pulang)->first();
            $pengaju = Karyawan::where('id', $request->id_karyawan)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // nama file
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $fileName = 'signature_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            if ($data_pulang->id_user_atasan == $user_karyawan->id) {
                $data_pulang->update([
                    'status_approve_atasan1' => 1,
                    'status_pulang' => 5,
                    'ttd_atasan' => $fileName,
                ]);
            } else {
                $data_pulang->update([
                    'status_approve_atasan2' => 1,
                    'status_pulang' => 5,
                    'ttd_atasan2' => $fileName,
                ]);
            }
            QuickPanel::where('content_id', $request->id_pulang)->update([
                'status' => 1,
            ]);
            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval pulang',
                'id_table' => $data_pulang->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'izin_pulang',
                'activity' => 'add',
                'description' => 'Menolak pengajuan pulang ' .  $data_pulang->nama_karyawan,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email              = $pengaju?->email;
            $nama_penerim       = $data_pulang->nama_karyawan;
            $nama               = $user_karyawan->name;
            // $tanggal            = Carbon::parse($data_pulang->tanggal_pulang)->format('d-m-Y');
            $judul              = 'Penolakan Izin Pulang';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan izin pulang anda ditolak oleh :';

            $waktu              = 'Yang sedianya akan dilakukan pada ' . $data_pulang->jam_pulang . '';

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

            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan izin berhasil ditolak.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_pengajuan_pulang(Request $request)
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
        // dd($request->all());
        try {

            // dd($request->jam_finger, $request->jam_masuk);
            $data_pulang = IzinPulang::where('id', $request->id_pulang)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $user_karyawan_pengajuan = Karyawan::where('id', $data_pulang->karyawan_id)->first();
            // nama file
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $fileName = 'signature_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            // $count_pulang = IzinPulang::whereIn('status_pulang', [5, 2])->whereDate('created_at', $data_pulang->created_at)->where('id', '!=', $data_pulang->id)->get()->count();
            // // dd($count_pulang);
            // if ($count_pulang == 0) {
            //     $last_count = 1;
            // } else {
            //     $last_count = $count_pulang + 1;
            // }
            // if (strlen($last_count) == 1) {
            //     $last_count = '000' . $last_count;
            // } else if (strlen($last_count) == 2) {
            //     $last_count = '00' . $last_count;
            // } else {
            //     $last_count = '0' . $last_count;
            // }

            // $tgl_pengajuan = Carbon::parse($data_pulang->created_at)->format('Ymd');

            // $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FIP' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($no_form);

            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            if ($data_pulang->id_user_atasan2 == $data_pulang->id_user_atasan) {
                $data_pulang->update([
                    'status_approve_atasan1' => 1,
                    'status_approve_atasan2' => 1,
                    'status_pulang' => 2,
                    'waktu_approve' => date('Y-m-d H:i:s'),
                    'waktu_approve2' => date('Y-m-d H:i:s'),
                    // 'no_form_pulang' => $no_form,
                    'ttd_atasan' => $fileName,
                    'ttd_atasan2' => $fileName,
                    'jam_finger' => $request->jam_finger,
                ]);

                QuickPanel::where('content_id', $request->id_pulang)->update([
                    'read_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval pulang',
                    'id_table' => $data_pulang->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_pulang',
                    'activity' => 'add',
                    'description' => 'menyetujui pengajuan pulang sebagai atasan 1 ' .  $data_pulang->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
            } else {
                if ($data_pulang->id_user_atasan == $user_karyawan->id) {
                    $data_pulang->update([
                        'status_approve_atasan1' => 1,
                        'status_pulang' => 1,
                        'waktu_approve' => date('Y-m-d H:i:s'),
                        // 'no_form_pulang' => $no_form,
                        'ttd_atasan' => $fileName,
                        'jam_finger' => $request->jam_finger,
                    ]);

                    QuickPanel::where('content_id', $request->id_pulang)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $quick = new QuickPanel();
                    $quick->id_quick = Uuid::uuid4();
                    $quick->user_id_from = $data_pulang->id_user_atasan;
                    $quick->user_id_to = $data_pulang->id_user_atasan2;
                    $quick->title_quick = 'Pengajuan Izin Pulang';
                    $quick->message_quick = 'Ada Pengajuan Izin Pulang Baru Dari ' . $data_pulang->nama_karyawan;
                    $quick->status = 0;
                    $quick->quick_type = 'izin_pulang';
                    $quick->menu_id = Auth::user()->menu_active;
                    $quick->content_id = $data_pulang->id;
                    $quick->created_at = date('Y-m-d H:i:s');
                    $quick->updated_at = date('Y-m-d H:i:s');
                    $quick->save();
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval pulang',
                        'id_table' => $data_pulang->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_pulang',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan pulang sebagai atasan 1 ' .  $data_pulang->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                } else {
                    $data_pulang->update([
                        'status_approve_atasan2' => 1,
                        'status_pulang' => 2,
                        // 'no_form_pulang' => $no_form,
                        'ttd_atasan2' => $fileName,
                        'waktu_approve2' => date('Y-m-d H:i:s'),
                        'jam_finger' => $request->jam_finger,
                    ]);
                    QuickPanel::where('content_id', $request->id_pulang)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval pulang',
                        'id_table' => $data_pulang->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_pulang',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan pulang sebagai atasan 2 ' .  $data_pulang->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                }
            }




            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan izin berhasil disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function cetak_form_pulang($id, $karyawan_id)
    {
        $pulang = IzinPulang::with('Atasan1', 'Atasan2')->where('id', $id)->first();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_pulang' => $pulang,
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
            ->loadView('users/izin/pulang_form', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_PULANG_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
}
