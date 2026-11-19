<?php

namespace App\Http\Controllers;

use App\Mail\KaryawanPenggantiMail;
use App\Mail\SemuaIzin;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Perdin;
use App\Models\PerdinDetail;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\Menu;
use App\Models\PerdinTujuan;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\Site;
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

class PerdinController extends Controller
{
    //
    public function perdin($id)
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
        $site = Site::orderBy('site_name', 'asc')->get();

        // dd($jam_kerja);
        // dd($finger);

        return view('users.perdin.perdin', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'site' => $site,
            'menu_id' => $id,
        ]);
    }
    public function get_perdin(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_perdin = Perdin::where('karyawan_id', $user_karyawan->id)
                ->where('status_perdin', 0)
                ->get();
            // dd($data_perdin);
            return response()->json([
                'code' => 200,
                'data' => $data_perdin

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_perdin_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_perdin = Perdin::where('karyawan_id', $user_karyawan->id)
                ->where('status_perdin', '!=', 0)
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_perdin);
            return response()->json([
                'code' => 200,
                'data' => $data_perdin

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_pengajuan_perdin(Request $request, $id)
    {
        // dd($request->all());
        try {
            $rules = [
                'id_karyawan' => 'required',
                'site' => 'required|array|min:1',
                'site.*' => 'required|in:1,2',
                'id_site' => 'required|array',
                'id_site.*' => 'nullable|exists:sites,id',
                'nama_site' => 'required|array',
                'nama_site.*' => 'nullable|string',
                'tanggal_perdin_berulang' => 'required|array|min:1',
                'tanggal_perdin_berulang.*' => 'required|date',
                'keterangan_perdin' => 'required',
                'atasan_1' => 'required',
                'atasan_2' => 'required',
                'signature' => 'required',

            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'karyawan_pengganti.required' => 'Karyawan Pengganti wajib diisi',
                'site.required' => 'Pilihan tujuan site wajib diisi',
                'site.*.in' => 'Pilihan tujuan site tidak valid',
                'id_site.required' => 'Daftar site wajib diisi',
                'id_site.*.exists' => 'Site yang dipilih tidak valid',
                'nama_site.required' => 'Daftar nama site wajib diisi',
                'tanggal_perdin_berulang.required' => 'Tanggal Perdin wajib diisi',
                // 'tanggal_perdin.required' => 'Jam perkiraan kembali wajib diisi',
                'signature.required' => 'Tanda Tangan wajib diisi',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            $validator->after(function ($validator) use ($request) {
                $siteTypes = $request->input('site', []);
                if (!is_array($siteTypes)) {
                    return;
                }

                foreach ($siteTypes as $index => $siteType) {
                    if ($siteType == 1 && blank($request->input("id_site.$index"))) {
                        $validator->errors()->add("id_site.$index", 'Nama Site wajib dipilih');
                    } elseif ($siteType == 2 && blank($request->input("nama_site.$index"))) {
                        $validator->errors()->add("nama_site.$index", 'Nama Site wajib diisi');
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'errors' => $validator->errors(),
                ]);
            }

            // dd($request->all());

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
            $tanggal_array = $request->input('tanggal_perdin_berulang');
            $start_tanggal_perdin = collect($tanggal_array)->first();
            $end_tanggal_perdin = collect($tanggal_array)->last();
            // dd($start_tanggal_perdin, $end_tanggal_perdin);
            if (!empty($tanggal_array)) {
                foreach ($tanggal_array as $tanggal) {
                    // Proses setiap tanggal di sini, misalnya simpan ke DB
                    // Contoh dengan Eloquent Laravel:
                    PerdinDetail::create([
                        'id' => Uuid::uuid4(),
                        'id_perdin' => $kode,
                        'id_karyawan' => $request->id_karyawan,
                        'nama_karyawan' => $request->name,
                        'tgl_perdin' => $tanggal,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $site_array = $request->input('site');
            $id_site = $request->input('id_site');
            $nama_site = $request->input('nama_site');
            // dd($site_array);

            if (!empty($site_array)) {
                foreach ($site_array as $key => $sa) {
                    if ($sa == 1) {
                        $sites = Site::where('id', $id_site[$key])->first();
                        $nama = $sites->site_name;
                    } else {
                        $nama = $nama_site[$key];
                    }
                    PerdinTujuan::create([
                        'id' => Uuid::uuid4(),
                        'id_perdin' => $kode,
                        'id_karyawan' => $request->id_karyawan,
                        'nama_karyawan' => $request->name,
                        'id_site' =>  $id_site[$key],
                        'nama_site' =>  $nama,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $pengaju = Karyawan::where('id', $request->id_karyawan)->first();
            $atasan1 = Karyawan::where('id', $request->atasan_1)->first();
            $atasan2 = Karyawan::where('id', $request->atasan_2)->first();


            $add_perdin = new Perdin();
            $add_perdin->id = $kode;
            $add_perdin->karyawan_id = $request->id_karyawan;
            $add_perdin->karyawan_nik = $pengaju->nomor_identitas_karyawan;
            $add_perdin->nama_karyawan = $request->name;
            $add_perdin->ttd_user = $fileName;

            $add_perdin->tanggal_mulai = $start_tanggal_perdin;
            $add_perdin->tanggal_selesai = $end_tanggal_perdin;

            $add_perdin->kendaraan_perdin = $request->kendaraan_perdin;

            $add_perdin->status_perdin = 0;
            $add_perdin->keterangan_perdin = $request->keterangan_perdin;

            $add_perdin->id_user_atasan = $request->atasan_1;
            $add_perdin->nama_atasan = $atasan1->name;

            $add_perdin->id_user_atasan2 = $request->atasan_2;
            $add_perdin->nama_atasan2 = $atasan2->name;

            $add_perdin->created_at = date('Y-m-d H:i:s');
            $add_perdin->save();



            // SAVE QUICK PANEL
            $quick = new QuickPanel();
            $quick->id_quick = Uuid::uuid4();
            $quick->user_id_from = $request->id_karyawan;
            $quick->user_id_to = $request->atasan_1;
            $quick->title_quick = 'Pengajuan Perjalanan Dinas';
            $quick->message_quick = 'Ada Pengajuan Perjalanan Dinas Baru Dari ' . $request->name;
            $quick->status = 0;
            $quick->quick_type = 'perdin';
            $quick->menu_id = Auth::user()->menu_active;
            $quick->content_id = $add_perdin->id;
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
                'table' => 'perdin',
                'activity' => 'add',
                'description' => 'Mengajukan perdin',
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email_atasan = $atasan1?->email_kantor ?: $atasan1?->email;
            $email              = $email_atasan;
            $nama_penerim       = $atasan1->name;
            $nama               = $request->name;
            $tanggal_mulai      = Carbon::parse($add_perdin->tanggal_mulai)->format('d-m-Y');
            $tanggal_selesai    = Carbon::parse($add_perdin->tanggal_selesai)->format('d-m-Y');
            $judul              = 'Pengajuan Perjalanan Dinas';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan perjalanan dinas dari :';

            $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

            $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
            $link               = url('/perdin/approve_perdin_detail/' . $kode);
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
                'message' => 'Pengajuan perdin berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function detail_perdin($id, $id_perdin)
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
        $data = Perdin::with('PerdinDetail')->where('id', $id_perdin)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.perdin.perdin_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_perdin,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_perdin($id, Request $request)
    {
        // dd($request->all());
        $query = Perdin::where('id', $request->id_perdin_hapus)->first();
        $id_perdin = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_perdin->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus perdin',
            'id_table' => $request->id_perdin_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'perdin',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan perdin ' .  $query->tanggal_perdin,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_perdin_gagal', 'Data perdin Tidak Ditemukan');
        } elseif ($query->status_perdin != 0) {
            $request->session()->flash('hapus_perdin_gagal', 'perdin sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_perdin_hapus)->delete();

            PerdinDetail::where('id_perdin', $query->id)->delete();
            $query->delete();
            $request->session()->flash('hapus_perdin_sukses', 'Form perdin Berhasil Dihapus');
        }
        return redirect('perdin/dashboard/' . $id);
    }
    public function get_perdin_approval(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_perdin = Perdin::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_perdin', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_perdin', 1);
                    });
            })->get();
            // dd($data_perdin);
            return response()->json([
                'code' => 200,
                'data' => $data_perdin

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function perdin_approval($menu_id)
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
        return view('users.perdin.perdin_approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_perdin_riwayat_approval(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_perdin = Perdin::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_perdin', '!=', 0)
                        ->where('status_approve_atasan1', '!=', null);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_perdin', '!=', 0)
                            ->where('status_approve_atasan2', '!=', null);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
            // dd($data_perdin);
            return response()->json([
                'code' => 200,
                'data' => $data_perdin

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_perdin_detail($id, Request $request)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        try {
            // dd('ss');
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
            $data_perdin = Perdin::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            // $presensi = $user_karyawan->Absensi->where('LogTime',)
            $menu_id = Auth::user()->menu_active;
            return view('users.perdin.perdin_approval_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'data' => $data_perdin,
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
    public function tolak_pengajuan_perdin(Request $request)
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
            $data_perdin = Perdin::where('id', $request->id_perdin)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $pengaju = Karyawan::where('id', $data_perdin->karyawan_id)->first();
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
            if ($data_perdin->id_user_atasan == $user_karyawan->id) {
                $data_perdin->update([
                    'status_approve_atasan1' => 1,
                    'status_perdin' => 5,
                    'ttd_atasan' => $fileName,
                ]);
            } else {
                $data_perdin->update([
                    'status_approve_atasan2' => 1,
                    'status_perdin' => 5,
                    'ttd_atasan2' => $fileName,
                ]);
            }
            QuickPanel::where('content_id', $request->id_perdin)->update([
                'status' => 1,
            ]);
            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval perdin',
                'id_table' => $data_perdin->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'perdin',
                'activity' => 'add',
                'description' => 'Menolak pengajuan perdin ' .  $data_perdin->nama_karyawan,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $email              = $pengaju?->email;
            $nama_penerim       = $data_perdin->nama_karyawan;
            $nama               = $user_karyawan->name;
            $tanggal_mulai      = Carbon::parse($data_perdin->tanggal_mulai)->format('d-m-Y');
            $tanggal_selesai    = Carbon::parse($data_perdin->tanggal_selesai)->format('d-m-Y');
            $judul              = 'Penolakan Perjalanan Dinas';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan perjalanan dinas anda ditolak oleh :';

            $waktu              = 'Yang sedianya akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

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
                'message' => 'Pengajuan perdin berhasil ditolak.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_pengajuan_perdin(Request $request)
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
            $data_perdin = Perdin::where('id', $request->id_perdin)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $user_karyawan_pengajuan = Karyawan::where('id', $data_perdin->karyawan_id)->first();
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
            $count_perdin = Perdin::whereIn('status_perdin', [5, 2])->whereDate('created_at', $data_perdin->created_at)->where('id', '!=', $data_perdin->id)->get()->count();
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

            $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FTM' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($no_form);

            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            if ($data_perdin->id_user_atasan2 == $data_perdin->id_user_atasan) {
                $data_perdin->update([
                    'status_approve_atasan1' => 1,
                    'status_approve_atasan2' => 1,
                    'status_perdin' => 2,
                    'waktu_approve' => date('Y-m-d H:i:s'),
                    'waktu_approve2' => date('Y-m-d H:i:s'),
                    'no_form_perdin' => $no_form,
                    'ttd_atasan' => $fileName,
                    'ttd_atasan2' => $fileName,
                    'jam_finger' => $request->jam_finger,
                ]);

                QuickPanel::where('content_id', $request->id_perdin)->update([
                    'read_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval perdin',
                    'id_table' => $data_perdin->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'perdin',
                    'activity' => 'add',
                    'description' => 'menyetujui pengajuan perdin sebagai atasan 1 ' .  $data_perdin->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
                $email              = 'jmhrd.gasumberpangan@gmail.com';
                $nama_penerim       = 'HRD Sumber Pangan';
                $nama               = $data_perdin->nama_karyawan;
                $tanggal_mulai      = Carbon::parse($data_perdin->tanggal_mulai)->format('d-m-Y');
                $tanggal_selesai    = Carbon::parse($data_perdin->tanggal_selesai)->format('d-m-Y');
                $judul              = 'Pengajuan Perjalanan Dinas';
                $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan perjalanan dinas dari :';

                $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                $link               = url('/perdin_karyawan/' . $data_perdin->id);
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
                if ($data_perdin->id_user_atasan == $user_karyawan->id) {
                    $data_perdin->update([
                        'status_approve_atasan1' => 1,
                        'status_perdin' => 1,
                        'waktu_approve' => date('Y-m-d H:i:s'),
                        'no_form_perdin' => $no_form,
                        'ttd_atasan' => $fileName,
                        'jam_finger' => $request->jam_finger,
                    ]);


                    QuickPanel::where('content_id', $request->id_perdin)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $quick = new QuickPanel();
                    $quick->id_quick = Uuid::uuid4();
                    $quick->user_id_from = $data_perdin->id_user_atasan;
                    $quick->user_id_to = $data_perdin->id_user_atasan2;
                    $quick->title_quick = 'Pengajuan Perjalanan Dinas';
                    $quick->message_quick = 'Ada Pengajuan Perjalanan Dinas Baru Dari ' . $data_perdin->nama_karyawan;
                    $quick->status = 0;
                    $quick->quick_type = 'perdin';
                    $quick->menu_id = Auth::user()->menu_active;
                    $quick->content_id = $data_perdin->id;
                    $quick->created_at = date('Y-m-d H:i:s');
                    $quick->updated_at = date('Y-m-d H:i:s');
                    $quick->save();
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval perdin',
                        'id_table' => $data_perdin->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'perdin',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan perdin sebagai atasan 1 ' .  $data_perdin->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $atasan2 = $data_perdin->Atasan2;
                    $email_atasan = $atasan2?->email_kantor ?: $atasan2?->email;
                    $email              = $email_atasan;
                    $nama_penerim       = $data_perdin->nama_atasan2;
                    $nama               = $data_perdin->nama_karyawan;
                    $tanggal_mulai      = Carbon::parse($data_perdin->tanggal_mulai)->format('d-m-Y');
                    $tanggal_selesai    = Carbon::parse($data_perdin->tanggal_selesai)->format('d-m-Y');
                    $judul              = 'Pengajuan Perjalanan Dinas';
                    $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan perjalanan dinas dari :';

                    $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                    $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                    $link               = url('/perdin/approve_perdin_detail/' . $data_perdin->id);
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
                        'status_approve_atasan2' => 1,
                        'status_perdin' => 2,
                        'no_form_perdin' => $no_form,
                        'ttd_atasan2' => $fileName,
                        'waktu_approve2' => date('Y-m-d H:i:s'),
                        'jam_finger' => $request->jam_finger,
                    ]);
                    QuickPanel::where('content_id', $request->id_perdin)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval perdin',
                        'id_table' => $data_perdin->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'perdin',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan perdin sebagai atasan 2 ' .  $data_perdin->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $email              = 'jmhrd.gasumberpangan@gmail.com';
                    $nama_penerim       = 'HRD Sumber Pangan';
                    $nama               = $data_perdin->nama_karyawan;
                    $tanggal_mulai      = Carbon::parse($data_perdin->tanggal_mulai)->format('d-m-Y');
                    $tanggal_selesai    = Carbon::parse($data_perdin->tanggal_selesai)->format('d-m-Y');
                    $judul              = 'Pengajuan Perjalanan Dinas';
                    $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan perjalanan dinas dari :';

                    $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                    $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                    $link               = url('/perdin_karyawan/' . $data_perdin->id);
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
                'message' => 'Pengajuan perdin berhasil disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function cetak_form_perdin($id, $karyawan_id)
    {
        $perdin = Perdin::with('Atasan1', 'Atasan2', 'AtasanHRD')->with('PerdinDetail')->where('id', $id)->first();
        $karyawan_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_perdin' => $perdin,
            'karyawan_jabatan' => $karyawan_jabatan,
            'user_karyawan' => $user_karyawan,
            'holding' => $holding,
            // 'logo' => $data['logo'],
            // 'ttd_user' => $data['ttd_pemohon'],
            // 'ttd_atasan' => $data['ttd_atasan'],
            // 'ttd_atasan2' => $data['ttd_atasan2']
        ];
        // dd($perdin->ttd_atasan);
        $pdf = PDF::setOptions([
            'defaultFont' => 'Poppins, sans-serif',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'chroot' => [storage_path('app/public')],
        ])
            ->loadView('users/perdin/perdin_form', $data)
            ->setPaper('letter', 'potrait');
        // dd($user_karyawan->name);

        return $pdf->stream(
            'FORM_perdin_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
}
