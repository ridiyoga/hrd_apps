<?php

namespace App\Http\Controllers;

use App\Mail\SemuaIzin;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\IzinKeluar;
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



class IzinKeluarController extends Controller
{
    public function keluar($id)
    {
        session(['url.intended' => url()->previous()]);
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

        return view('users.izin.keluar', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function proses_pengajuan_keluar(Request $request, $id)
    {
        $rules = [
            'id_karyawan' => 'required',
            'jam_keluar' => 'required',
            'jam_kembali' => 'required',
            'keterangan_keluar' => 'required',
            'atasan_1' => 'required',
            'atasan_2' => 'required',
            'signature' => 'required',

        ];
        $messages = [
            'id_karyawan.required' => 'Karyawan wajib diisi',
            'jam_kembali.required' => 'Jam perkiraan kembali wajib diisi',
            'jam_keluar.required' => 'Jam keluar wajib diisi',
            'signature.required' => 'Tanda Tangan wajib diisi',
            'keterangan_keluar.required' => 'Alasan keluar wajib diisi',
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
            $jam_keluar = \Carbon\Carbon::parse($request->jam_keluar)
                ->addHours(3)
                ->addMinutes(1)
                ->format('H:i:s');
            $jam_kembali = \Carbon\Carbon::parse($request->jam_kembali)
                ->format('H:i:s');
            // dd($jam_kembali);
            if ($jam_keluar > $jam_kembali) {
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

                $add_izin = new IzinKeluar();
                $add_izin->id = $kode;
                $add_izin->karyawan_id = $request->id_karyawan;
                $add_izin->karyawan_nik = $pengaju->nomor_identitas_karyawan;
                $add_izin->nama_karyawan = $request->name;
                $add_izin->ttd_user = $fileName;

                $add_izin->jam_keluar = $request->jam_keluar;
                $add_izin->jam_kembali = $request->jam_kembali;

                $add_izin->keterangan_keluar = $request->keterangan_keluar;
                $add_izin->status_keluar = 0;

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
                $quick->title_quick = 'Pengajuan Izin keluar';
                $quick->message_quick = 'Ada Pengajuan Izin keluar Baru Dari ' . $request->name;
                $quick->status = 0;
                $quick->quick_type = 'izin_keluar';
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
                    'table' => 'izin_keluar',
                    'activity' => 'add',
                    'description' => 'Mengajukan izin keluar pukul ' . $request->jam_keluar,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
                $email_atasan = $atasan1?->email_kantor ?: $atasan1?->email;
                $email              = $email_atasan;
                $nama_penerim       = $atasan1->name;
                $nama               = $request->name;
                $tanggal_mulai      = Carbon::parse($add_izin->created_at)->format('d-m-Y H:i:s');
                $judul              = 'Pengajuan Izin Keluar';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin keluar dari :';

                $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . '';

                $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                $link               = url('/izin/approve_keluar_detail/' . $kode);
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
                    'message' => 'Jam Keluar tidak boleh lebih dari 3 jam',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_keluar(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_izin = IzinKeluar::where('karyawan_id', $user_karyawan->id)
                ->where('status_keluar', 0)
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
    public function get_keluar_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_keluar = IzinKeluar::where('karyawan_id', $user_karyawan->id)
                ->where('status_keluar', '!=', 0)
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
    public function detail_keluar($id, $id_keluar)
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
        $data = Izinkeluar::where('id', $id_keluar)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.izin.keluar_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_keluar,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_keluar($id, Request $request)
    {
        // dd($request->all());
        $query = IzinKeluar::where('id', $request->id_keluar_hapus)->first();
        $id_keluar = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_keluar->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus keluar',
            'id_table' => $request->id_keluar_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'izin_keluar',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan keluar ' .  $query->jam_keluar,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_keluar_gagal', 'Data keluar Tidak Ditemukan');
        } elseif ($query->status_keluar != 0) {
            $request->session()->flash('hapus_keluar_gagal', 'keluar sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_keluar_hapus)->delete();

            $query->delete();
            $request->session()->flash('hapus_keluar_sukses', 'Form Keluar Berhasil Dihapus');
        }
        return redirect('izin/keluar/' . $id);
    }
    public function keluar_approval($menu_id)
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
        return view('users.izin.keluar_approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_keluar_approval(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_keluar = IzinKeluar::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_keluar', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_keluar', 1);
                    });
            })->get();
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
    public function get_keluar_riwayat_approval(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_keluar = IzinKeluar::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_keluar', '!=', 0)
                        ->where('status_approve_atasan1', '!=', null);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_keluar', '!=', 0)
                            ->where('status_approve_atasan2', '!=', null);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
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
    public function approve_keluar_detail($id, Request $request)
    {
        try {
            session(['url.intended' => url()->previous()]);
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
            $data_keluar = IzinKeluar::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            // $presensi = $user_karyawan->Absensi->where('LogTime',)
            $menu_id = Auth::user()->menu_active;
            return view('users.izin.keluar_approval_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'data' => $data_keluar,
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
    public function get_keluar_detail($id)
    {
        try {
            $data_keluar = IzinKeluar::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            return response()->json([
                'code' => 200,
                'data' => $data_keluar,
                'user' => $user_karyawan,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function tolak_pengajuan_keluar(Request $request)
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
            $data_keluar = IzinKeluar::where('id', $request->id_keluar)->first();
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
            if ($data_keluar->id_user_atasan == $user_karyawan->id) {
                $data_keluar->update([
                    'status_approve_atasan1' => 1,
                    'status_keluar' => 5,
                    'ttd_atasan' => $fileName,
                ]);
            } else {
                $data_keluar->update([
                    'status_approve_atasan2' => 1,
                    'status_keluar' => 5,
                    'ttd_atasan2' => $fileName,
                ]);
            }
            QuickPanel::where('content_id', $request->id_keluar)->update([
                'status' => 1,
            ]);
            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval keluar',
                'id_table' => $data_keluar->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'izin_keluar',
                'activity' => 'add',
                'description' => 'Menolak pengajuan keluar ' .  $data_keluar->nama_karyawan,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email              = $pengaju?->email;
            $nama_penerim       = $data_keluar->nama_karyawan;
            $nama               = $user_karyawan->name;
            $tanggal_mulai      = Carbon::parse($data_keluar->created_at)->format('d-m-Y H:i:s');
            $judul              = 'Penolakan Izin Keluar';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan izin keluar anda ditolak oleh :';

            $waktu              = 'Yang sedianya akan dilakukan pada ' . $tanggal_mulai . '';

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
    public function approve_pengajuan_keluar(Request $request)
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
            $data_keluar = IzinKeluar::where('id', $request->id_keluar)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $user_karyawan_pengajuan = Karyawan::where('id', $data_keluar->karyawan_id)->first();
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
            // $count_keluar = IzinKeluar::whereIn('status_keluar', [5, 2])->whereDate('created_at', $data_keluar->created_at)->where('id', '!=', $data_keluar->id)->get()->count();
            // // dd($count_keluar);
            // if ($count_keluar == 0) {
            //     $last_count = 1;
            // } else {
            //     $last_count = $count_keluar + 1;
            // }
            // if (strlen($last_count) == 1) {
            //     $last_count = '000' . $last_count;
            // } else if (strlen($last_count) == 2) {
            //     $last_count = '00' . $last_count;
            // } else {
            //     $last_count = '0' . $last_count;
            // }

            // $tgl_pengajuan = Carbon::parse($data_keluar->created_at)->format('Ymd');

            // $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FIK' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($no_form);

            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            if ($data_keluar->id_user_atasan2 == $data_keluar->id_user_atasan) {
                $data_keluar->update([
                    'status_approve_atasan1' => 1,
                    'status_approve_atasan2' => 1,
                    'status_keluar' => 2,
                    'waktu_approve' => date('Y-m-d H:i:s'),
                    'waktu_approve2' => date('Y-m-d H:i:s'),
                    // 'no_form_keluar' => $no_form,
                    'ttd_atasan' => $fileName,
                    'ttd_atasan2' => $fileName,
                    'jam_finger' => $request->jam_finger,
                ]);

                QuickPanel::where('content_id', $request->id_keluar)->update([
                    'read_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval keluar',
                    'id_table' => $data_keluar->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_keluar',
                    'activity' => 'add',
                    'description' => 'menyetujui pengajuan keluar sebagai atasan 1 ' .  $data_keluar->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
            } else {
                if ($data_keluar->id_user_atasan == $user_karyawan->id) {
                    $data_keluar->update([
                        'status_approve_atasan1' => 1,
                        'status_keluar' => 1,
                        'waktu_approve' => date('Y-m-d H:i:s'),
                        // 'no_form_keluar' => $no_form,
                        'ttd_atasan' => $fileName,
                        'jam_finger' => $request->jam_finger,
                    ]);

                    QuickPanel::where('content_id', $request->id_keluar)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $quick = new QuickPanel();
                    $quick->id_quick = Uuid::uuid4();
                    $quick->user_id_from = $data_keluar->id_user_atasan;
                    $quick->user_id_to = $data_keluar->id_user_atasan2;
                    $quick->title_quick = 'Pengajuan Izin Keluar';
                    $quick->message_quick = 'Ada Pengajuan Izin Keluar Baru Dari ' . $data_keluar->nama_karyawan;
                    $quick->status = 0;
                    $quick->quick_type = 'izin_keluar';
                    $quick->menu_id = Auth::user()->menu_active;
                    $quick->content_id = $data_keluar->id;
                    $quick->created_at = date('Y-m-d H:i:s');
                    $quick->updated_at = date('Y-m-d H:i:s');
                    $quick->save();
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval keluar',
                        'id_table' => $data_keluar->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_keluar',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan keluar sebagai atasan 1 ' .  $data_keluar->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                } else {
                    $data_keluar->update([
                        'status_approve_atasan2' => 1,
                        'status_keluar' => 2,
                        // 'no_form_keluar' => $no_form,
                        'ttd_atasan2' => $fileName,
                        'waktu_approve2' => date('Y-m-d H:i:s'),
                        'jam_finger' => $request->jam_finger,
                    ]);
                    QuickPanel::where('content_id', $request->id_keluar)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval keluar',
                        'id_table' => $data_keluar->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_keluar',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan keluar sebagai atasan 2 ' .  $data_keluar->nama_karyawan,
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
    public function cetak_form_keluar($id, $karyawan_id)
    {
        $keluar = IzinKeluar::with('Atasan1', 'Atasan2')->where('id', $id)->first();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_keluar' => $keluar,
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
            ->loadView('users/izin/keluar_form', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_PULANG_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
}
