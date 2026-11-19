<?php

namespace App\Http\Controllers;

use App\Mail\SemuaIzin;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\MappingShift;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use App\Models\Jabatan;
use App\Models\Izin;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\IzinTerlambat;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KategoriIzin;
use App\Models\LevelJabatan;
use App\Models\Lokasi;
use App\Models\Menu;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use DateTime;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Excel;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;


class IzinUserController extends Controller
{
    public function index($id)
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

        return view('users.izin.index', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }

    public function terlambat($id)
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

        $jam_kerja = MappingShift::with('Shift')
            ->where('karyawan_id', $user_karyawan->id)
            ->whereDate('tanggal_masuk', now())
            ->whereNotNull('is_active')
            ->first();
        // dd($jam_kerja);
        if ($jam_kerja == '' || $jam_kerja == NULL) {
            $req_jm_klr = NULL;
        } else {
            $jam_masuk = \Carbon\Carbon::parse($jam_kerja->Shift->jam_masuk)->addMinute(5)->isoFormat('HH:mm');
            // dd($jam_masuk);
            if ($jam_kerja->jam_absen <= $jam_masuk) {
                $req_jm_klr = new DateTime(date('Y-m-d') . $jam_kerja->Shift->jam_masuk);
            } else {
                $req_jm_klr = new DateTime(date('Y-m-d') . $jam_kerja->jam_absen);
            }
        }
        // dd($jam_kerja);

        $jam_min_plg_cpt = \Carbon\Carbon::parse($req_jm_klr)->addHour(6)->isoFormat('H:mm');
        if ($user_karyawan->shift == 'NON SHIFT') {
            $masuk =  Shift::where('nama_shift', 'NON SHIFT')->first();
            $min_masuk = $masuk?->jam_masuk
                ? \Carbon\Carbon::parse($masuk->jam_masuk)->addMinutes(5)->format('H:i')
                : null;
            $terlambat =  $masuk->jam_masuk
                ? \Carbon\Carbon::parse($masuk->jam_masuk)
                ->addHours(3)
                ->addMinutes(6)
                ->format('H:i')
                : null;
        } else {
            $min_masuk = $jam_kerja?->Shift?->jam_masuk
                ? \Carbon\Carbon::parse($jam_kerja->Shift->jam_masuk)->addMinutes(5)->format('H:i')
                : null;
            $terlambat = $jam_kerja?->Shift?->jam_masuk
                // dd($terlambat);
                ? \Carbon\Carbon::parse($jam_kerja->Shift->jam_masuk)
                ->addHours(3)
                ->addMinutes(6)
                ->format('H:i')
                : null;
        }
        return view('users.izin.terlambat', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'jam_min_plg_cpt' => $jam_min_plg_cpt,
            'terlambat' => $terlambat,
            'menu_id' => $id,
            'min_masuk' => $min_masuk,
        ]);
    }
    public function get_jam_terlambat($tanggal)
    {
        // dd($tanggal);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            if ($user_karyawan->shift == 'NON SHIFT') {
                $shift = Shift::where('nama_shift', 'NON SHIFT')->first();
                $data_izin = \Carbon\Carbon::parse($shift->jam_terlambat)
                    ->addHours(3)
                    ->addMinutes(1)
                    ->format('H:i');
                $jam_min_masuk = \Carbon\Carbon::parse($shift->jam_min_masuk)
                    ->format('H:i');
                // dd($data_izin);
            } else {
                // $shift = Shift
                $mapping_shift = MappingShift::where('karyawan_id', $user_karyawan->id)
                    ->where('tanggal_masuk', $tanggal)
                    ->whereNotNull('is_active')
                    ->first();
                // dd($mapping_shift);
                $shift = Shift::where('id', $mapping_shift->shift_id)->first();
                if (!empty($shift->jam_terlambat)) {
                    $data_izin = \Carbon\Carbon::parse($shift->jam_terlambat)
                        ->addHours(3)
                        ->addMinutes(1)
                        ->format('H:i');
                    $jam_min_masuk = \Carbon\Carbon::parse($shift->jam_min_masuk)
                        ->format('H:i');
                }
            }
            // dd($data_izin);
            return response()->json([
                'code' => 200,
                'data' => [
                    'jam_masuk' => $data_izin ?? null,
                    'jam_min_masuk' => $jam_min_masuk ?? null,
                ]

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_pengajuan_terlambat(Request $request, $id)
    {
        // dd($request->all());
        try {
            $rules = [
                'id_karyawan' => 'required',
                'jam_masuk' => 'required',
                'jam_terlambat' => 'required',
                'tanggal_terlambat' => 'required',
                'keterangan_terlambat' => 'required',
                'atasan_1' => 'required',
                'atasan_2' => 'required',
                'signature' => 'required',

            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'jam_masuk.required' => 'Jam masuk wajib diisi',
                'jam_terlambat.required' => 'Jam Terlambat wajib diisi',
                'tanggal_terlambat.required' => 'Tanggal Terlambat wajib diisi',
                'signature.required' => 'Tanda Tangan wajib diisi',
                'keterangan_terlambat.required' => 'Alasan terlambat wajib diisi',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'errors' => $validator->errors(),
                ]);
            }
            $terlambat = \Carbon\Carbon::parse(
                $request->tanggal_terlambat . ' ' . $request->jam_terlambat
            );
            $jam_min_masuk = \Carbon\Carbon::parse(
                $request->tanggal_terlambat . ' ' . $request->jam_min_masuk
            );


            // dd($jam_masuk, $jam_min_masuk);
            // dd($request->jam_masuk, $request->jam_min_masuk);
            if ($request->jam_masuk < $request->jam_min_masuk) {

                $jam_masuk = \Carbon\Carbon::parse(
                    $request->tanggal_terlambat . ' ' . $request->jam_masuk
                )->addDay();
                // dd($jam_masuk);
            } else {
                $jam_masuk = \Carbon\Carbon::parse(
                    $request->tanggal_terlambat . ' ' . $request->jam_masuk
                );
            }
            if ($request->jam_terlambat < $request->jam_min_masuk) {
                $jam_terlambat = \Carbon\Carbon::parse($terlambat)
                    ->addDay();
            } else {
                $jam_terlambat = \Carbon\Carbon::parse($terlambat);
            }
            // dd($jam_terlambat, $jam_masuk);
            if ($jam_terlambat->lessThan($jam_masuk)) {
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



                $add_izin = new IzinTerlambat();
                $add_izin->id = $kode;
                $add_izin->karyawan_id = $request->id_karyawan;
                $add_izin->karyawan_nik = $pengaju->nomor_identitas_karyawan;
                $add_izin->nama_karyawan = $request->name;
                $add_izin->ttd_user = $fileName;

                $add_izin->tanggal_terlambat = $request->tanggal_terlambat;
                $add_izin->jam_terlambat = $jam_terlambat;
                $add_izin->jam_min_masuk = $jam_min_masuk;
                $add_izin->jam_masuk = $jam_masuk;

                $add_izin->keterangan_terlambat = $request->keterangan_terlambat;
                $add_izin->status_terlambat = 0;

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
                $quick->title_quick = 'Pengajuan Izin Terlambat';
                $quick->message_quick = 'Ada Pengajuan Izin Terlambat Baru Dari ' . $request->name;
                $quick->status = 0;
                $quick->quick_type = 'izin_terlambat';
                $quick->menu_id = Auth::user()->menu_active;
                $quick->content_id = $add_izin->id;
                $quick->created_at = date('Y-m-d H:i:s');
                $quick->updated_at = date('Y-m-d H:i:s');
                $quick->save();
                // dd($quick->save());

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
                    'table' => 'izin_terlambat',
                    'activity' => 'add',
                    'description' => 'Mengajukan izin keterlambatan pukul ' . $request->jam_terlambat,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
                $email_atasan = $atasan1?->email_kantor ?: $atasan1?->email;
                // dd($email_atasan);
                $email              = $email_atasan;
                $nama_penerim       = $atasan1->name;
                $nama               = $request->name;
                $tanggal      = Carbon::parse($add_izin->tanggal_terlambat)->format('d-m-Y');
                $judul              = 'Pengajuan Izin Terlambat';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin terlambat dari :';

                $waktu              = 'Pada Tanggal ' . $tanggal . '';

                $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                $link               = url('/izin/approve_terlambat_detail/' . $kode);
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
                    'message' => 'Jam izin melebihi batas keterlambatan',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_terlambat(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_izin = IzinTerlambat::where('karyawan_id', $user_karyawan->id)
                ->where('status_terlambat', 0)
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
    public function detail_terlambat($id, $id_terlambat)
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
        $data = IzinTerlambat::where('id', $id_terlambat)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.izin.terlambat_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_terlambat,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_terlambat($id, Request $request)
    {
        // dd($request->all());
        $query = IzinTerlambat::where('id', $request->id_terlambat_hapus)->first();
        $id_terlambat = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_terlambat->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus terlambat',
            'id_table' => $request->id_terlambat_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'izin_terlambat',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan terlambat ' .  $query->jam_terlambat,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_terlambat_gagal', 'Data terlambat Tidak Ditemukan');
        } elseif ($query->status_terlambat != 0) {
            $request->session()->flash('hapus_terlambat_gagal', 'keterlambatan sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_terlambat_hapus)->delete();

            $query->delete();
            $request->session()->flash('hapus_terlambat_sukses', 'Form Keterlambatan Berhasil Dihapus');
        }
        return redirect('izin/terlambat/' . $id);
    }
    public function terlambat_approval($menu_id)
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
        return view('users.izin.terlambat_approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_terlambat_approval(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_terlambat = IzinTerlambat::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_terlambat', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_terlambat', 1);
                    });
            })->get();
            // dd($data_terlambat);
            return response()->json([
                'code' => 200,
                'data' => $data_terlambat

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_terlambat_detail($id, Request $request)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()) {
            return redirect('/');
        }
        // dd($id);
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
            $data_terlambat = IzinTerlambat::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $user_pengaju = Karyawan::where('id', $data_terlambat->karyawan_id)->first();
            // dd($user_pengaju);
            if ($user_pengaju->shift == 'NON SHIFT') {


                $shift =  Shift::where('nama_shift', 'NON SHIFT')->first();
            } else {
                // dd('hehe');
                $mapping_shift = MappingShift::where('karyawan_id', $user_pengaju->id)
                    ->where('tanggal_masuk', $data_terlambat->tanggal_terlambat)->whereNotNull('is_active')->first();
                $shift =  Shift::where('id', $mapping_shift->shift_id)->first();
                // dd($mapping_shift);
            }
            $start = $data_terlambat->jam_min_masuk;
            // ->subMinutes(50);
            $end = $data_terlambat->jam_masuk;
            if (!empty($start) && !empty($end)) {
                // dd($start, $end);
                // dd($end);
                $finger = $data_terlambat->User->Absensi->whereBetween('LogTime', [$start, $end])->first();
                // dd($finger);
            } else {
                $finger = null;
            }
            // $presensi = $user_karyawan->Absensi->where('LogTime',)
            $menu_id = Auth::user()->menu_active;
            return view('users.izin.terlambat_approval_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'data' => $data_terlambat,
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'user_karyawan' => $user_karyawan,
                'finger' => $finger,
                'menu_id' => $menu_id
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
            return redirect()->back()->with('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
        }
    }
    public function get_terlambat_Detail($id)
    {
        try {
            $data_terlambat = IzinTerlambat::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            return response()->json([
                'code' => 200,
                'data' => $data_terlambat,
                'user' => $user_karyawan,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function tolak_pengajuan_terlambat(Request $request)
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
            $data_terlambat = IzinTerlambat::where('id', $request->id_terlambat)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $pengaju = Karyawan::where('id', $data_terlambat->karyawan_id)->first();

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
            if ($data_terlambat->id_user_atasan == $user_karyawan->id) {
                $data_terlambat->update([
                    'status_approve_atasan1' => 1,
                    'status_terlambat' => 5,
                    'ttd_atasan' => $fileName,
                ]);
            } else {
                $data_terlambat->update([
                    'status_approve_atasan2' => 1,
                    'status_terlambat' => 5,
                    'ttd_atasan2' => $fileName,
                ]);
            }
            QuickPanel::where('content_id', $request->id_terlambat)->update([
                'status' => 1,
            ]);
            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval terlambat',
                'id_table' => $data_terlambat->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'izin_terlambat',
                'activity' => 'add',
                'description' => 'Menolak pengajuan terlambat ' .  $data_terlambat->nama_karyawan,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email              = $pengaju?->email;
            $nama_penerim       = $data_terlambat->nama_karyawan;
            $nama               = $user_karyawan->name;
            $tanggal            = Carbon::parse($data_terlambat->tanggal_terlambat)->format('d-m-Y');
            $judul              = 'Penolakan Izin Terlambat';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan izin terlambat anda ditolak oleh :';

            $waktu              = 'Yang sedianya akan dilakukan pada ' . $tanggal . '';

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
    public function get_terlambat_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_terlambat = IzinTerlambat::where('karyawan_id', $user_karyawan->id)
                ->where('status_terlambat', '!=', 0)
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_terlambat);
            return response()->json([
                'code' => 200,
                'data' => $data_terlambat

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_terlambat_riwayat_approval(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_terlambat = IzinTerlambat::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_terlambat', '!=', 0)
                        ->where('status_approve_atasan1', '!=', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_terlambat', '!=', 0)
                            ->where('status_approve_atasan2', '!=', 0);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
            // dd($data_terlambat);
            return response()->json([
                'code' => 200,
                'data' => $data_terlambat

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_pengajuan_terlambat(Request $request)
    {
        $rules = [
            'signature' => 'required',
            'jam_finger' => 'required',

        ];
        $messages = [
            'signature.required' => 'Tanda tangan wajib diisi',
            'jam_finger.required' => 'Jam Presensi Pegawai belum terekam (tunggu setiap 15 Menit)',
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
            $jam_finger = \Carbon\Carbon::parse($request->jam_finger);
            $jam_masuk = \Carbon\Carbon::parse($request->jam_masuk);
            if ($jam_finger->lessThan($jam_masuk)) {
                // dd($jamFinger, $jam_masuk);
                $data_terlambat = IzinTerlambat::where('id', $request->id_terlambat)->first();
                $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
                $user_karyawan_pengajuan = Karyawan::where('id', $data_terlambat->karyawan_id)->first();
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
                // if ($jamFinger->lessThan($jamMinMasuk)) {
                //     $created = \Carbon\Carbon::parse($data_terlambat->created_at)
                //         ->subDay(1)
                //         ->addMinute(1);
                // } else {
                //     $created = \Carbon\Carbon::parse($data_terlambat->created_at)
                //         ->addMinute(1);
                // }

                // $count_terlambat = IzinTerlambat::whereIn('status_terlambat', [5, 2])->whereDate('created_at', $data_terlambat->created_at)->where('id', '!=', $data_terlambat->id)->get()->count();
                // // dd($count_terlambat);
                // if ($count_terlambat == 0) {
                //     $last_count = 1;
                // } else {
                //     $last_count = $count_terlambat + 1;
                // }
                // if (strlen($last_count) == 1) {
                //     $last_count = '000' . $last_count;
                // } else if (strlen($last_count) == 2) {
                //     $last_count = '00' . $last_count;
                // } else {
                //     $last_count = '0' . $last_count;
                // }

                // $tgl_pengajuan = Carbon::parse($data_terlambat->created_at)->format('Ymd');

                // $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FIT' . '/' . $tgl_pengajuan . '/' . $last_count;

                // dd($no_form);

                $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                if ($data_terlambat->id_user_atasan2 == $data_terlambat->id_user_atasan) {
                    $data_terlambat->update([
                        'status_approve_atasan1' => 1,
                        'status_approve_atasan2' => 1,
                        'status_terlambat' => 2,
                        'waktu_approve' => date('Y-m-d H:i:s'),
                        'waktu_approve2' => date('Y-m-d H:i:s'),
                        // 'no_form_terlambat' => $no_form,
                        'ttd_atasan' => $fileName,
                        'ttd_atasan2' => $fileName,
                        'jam_finger' => $request->jam_finger,
                        // 'created_at' => $created,
                    ]);

                    QuickPanel::where('content_id', $request->id_terlambat)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval terlambat',
                        'id_table' => $data_terlambat->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_terlambat',
                        'activity' => 'add',
                        'description' => 'Menyatujui pengajuan terlambat sebagai atasan 1 ' .  $data_terlambat->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $email              = 'jmhrd.gasumberpangan@gmail.com';
                    $nama_penerim       = 'HRD Sumber Pangan';
                    $nama               = $data_terlambat->nama_karyawan;
                    $tanggal_terlambat      = Carbon::parse($data_terlambat->tanggal_terlambat)->format('d-m-Y');
                    $judul              = 'Pengajuan Izin Terlambat';
                    $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin terlambat dari :';

                    $waktu              = 'Yang akan dilakukan pada ' . $tanggal_terlambat . '';

                    $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                    $link               = url('/terlambat_karyawan/' . $data_terlambat->id);
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
                    if ($data_terlambat->id_user_atasan == $user_karyawan->id) {
                        $data_terlambat->update([
                            'status_approve_atasan1' => 1,
                            'status_terlambat' => 1,
                            'waktu_approve' => date('Y-m-d H:i:s'),
                            // 'no_form_terlambat' => $no_form,
                            'ttd_atasan' => $fileName,
                            'jam_finger' => $request->jam_finger,
                        ]);

                        QuickPanel::where('content_id', $request->id_terlambat)->update([
                            'read_at' => date('Y-m-d H:i:s'),
                            'status' => 1,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        $quick = new QuickPanel();
                        $quick->id_quick = Uuid::uuid4();
                        $quick->user_id_from = $data_terlambat->id_user_atasan;
                        $quick->user_id_to = $data_terlambat->id_user_atasan2;
                        $quick->title_quick = 'Pengajuan Izin Terlambat';
                        $quick->message_quick = 'Ada Pengajuan Izin Terlambat Baru Dari ' . $data_terlambat->nama_karyawan;
                        $quick->status = 0;
                        $quick->quick_type = 'izin_terlambat';
                        $quick->menu_id = Auth::user()->menu_active;
                        $quick->content_id = $data_terlambat->id;
                        $quick->created_at = date('Y-m-d H:i:s');
                        $quick->updated_at = date('Y-m-d H:i:s');
                        $quick->save();
                        ActivityLog::create([
                            'user_id' => $request->user()->id,
                            'user_name' => $user_name->username,
                            'menu_id' => null,
                            'menu_name' => 'approval terlambat',
                            'id_table' => $data_terlambat->id,
                            'holding_id' => $holdings->id ?? '-',
                            'holding_category' => $holdings->holding_category ?? '-',
                            'table' => 'izin_terlambat',
                            'activity' => 'add',
                            'description' => 'Menyatujui pengajuan terlambat sebagai atasan 1 ' .  $data_terlambat->nama_karyawan,
                            'keterangan' => 'Success',
                            'karir' => '2'
                        ]);
                        $atasan2 = $data_terlambat->Atasan2;
                        $email_atasan = $atasan2?->email_kantor ?: $atasan2?->email;
                        $email              = $email_atasan;
                        $nama_penerim       = $data_terlambat->nama_atasan2;
                        $nama               = $data_terlambat->nama_karyawan;
                        $tanggal_terlambat      = Carbon::parse($data_terlambat->tanggal_terlambat)->format('d-m-Y');
                        $judul              = 'Pengajuan Izin Terlambat';
                        $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin terlambat dari :';

                        $waktu              = 'Yang akan diajukan pada ' . $tanggal_terlambat . '';

                        $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                        $link               = url('/izin/approve_terlambat_detail/' . $data_terlambat->id);
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
                        $data_terlambat->update([
                            'status_approve_atasan2' => 1,
                            'status_terlambat' => 2,
                            // 'no_form_terlambat' => $no_form,
                            'ttd_atasan2' => $fileName,
                            'waktu_approve2' => date('Y-m-d H:i:s'),
                            'jam_finger' => $request->jam_finger,
                        ]);
                        QuickPanel::where('content_id', $request->id_terlambat)->update([
                            'read_at' => date('Y-m-d H:i:s'),
                            'status' => 1,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        ActivityLog::create([
                            'user_id' => $request->user()->id,
                            'user_name' => $user_name->username,
                            'menu_id' => null,
                            'menu_name' => 'approval terlambat',
                            'id_table' => $data_terlambat->id,
                            'holding_id' => $holdings->id ?? '-',
                            'holding_category' => $holdings->holding_category ?? '-',
                            'table' => 'izin_terlambat',
                            'activity' => 'add',
                            'description' => 'Menyatujui pengajuan terlambat sebagai atasan 2 ' .  $data_terlambat->nama_karyawan,
                            'keterangan' => 'Success',
                            'karir' => '2'
                        ]);

                        $email              = 'jmhrd.gasumberpangan@gmail.com';
                        $nama_penerim       = 'HRD Sumber Pangan';
                        $nama               = $data_terlambat->nama_karyawan;
                        $tanggal_terlambat      = Carbon::parse($data_terlambat->tanggal_terlambat)->format('d-m-Y');
                        $judul              = 'Pengajuan Izin Terlambat';
                        $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin terlambat dari :';

                        $waktu              = 'Yang akan dilakukan pada ' . $tanggal_terlambat . '';

                        $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                        $link               = url('/terlambat_karyawan/' . $data_terlambat->id);
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
                }
                return response()->json([
                    'code' => 200,
                    'message' => 'Pengajuan izin berhasil disetujui.',
                ]);
            } else {
                // dd('kk');

                return response()->json([
                    'code' => 401,
                    'message' => 'Jam presensi finger melebihi batas keterlambatan, mohon ditolak',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function cetak_form_terlambat($id, $karyawan_id)
    {
        $terlambat = IzinTerlambat::with('Atasan1', 'Atasan2')->where('id', $id)->first();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_terlambat' => $terlambat,
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
            'chroot' => [storage_path('app/public')],
        ])
            ->loadView('users/izin/terlambat_form', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_KETERLAMBATAN_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
}
