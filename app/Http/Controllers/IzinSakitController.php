<?php

namespace App\Http\Controllers;

use App\Mail\SemuaIzin;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\IzinSakit;
use App\Models\IzinSakitDetail;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\Menu;
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

class IzinSakitController extends Controller
{
    //
    public function sakit($id)
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

        // dd($jam_kerja);
        // dd($finger);

        return view('users.izin.sakit', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function get_sakit(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_izin = IzinSakit::where('karyawan_id', $user_karyawan->id)
                ->where('status_sakit', 0)
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
    public function get_sakit_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_sakit = IzinSakit::where('karyawan_id', $user_karyawan->id)
                ->where('status_sakit', '!=', 0)
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_sakit);
            return response()->json([
                'code' => 200,
                'data' => $data_sakit

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_pengajuan_sakit(Request $request, $id)
    {
        // dd($request->all());
        session(['url.intended' => url()->previous()]);
        try {
            $rules = [
                'id_karyawan' => 'required',

                'tanggal_sakit_berulang' => 'required|array|min:1',
                'tanggal_sakit_berulang.*' => 'required|date',
                'surat_dokter' => 'required',
                'karyawan_pengganti' => 'required',
                // 'keterangan_sakit' => 'required',
                'atasan_1' => 'required',
                'atasan_2' => 'required',
                'signature' => 'required',

            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'karyawan_pengganti.required' => 'Karyawan Pengganti wajib diisi',
                // 'tanggal_sakit.required' => 'Jam perkiraan kembali wajib diisi',
                'surat_dokter.required' => 'Surat dokter wajib diunggah',
                'signature.required' => 'Tanda Tangan wajib diisi',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'errors' => $validator->errors(),
                ]);
            }
            if ($request->surat_dokter != null) {
                $file = $request->file('surat_dokter')->store('public/surat_dokter');
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
            $tanggal_array = $request->input('tanggal_sakit_berulang');
            $start_tanggal_sakit = collect($tanggal_array)->first();
            $end_tanggal_sakit = collect($tanggal_array)->last();
            // dd($start_tanggal_sakit, $end_tanggal_sakit);
            $jumlah_hari = count($tanggal_array);
            if (!empty($tanggal_array)) {
                foreach ($tanggal_array as $tanggal) {
                    // Proses setiap tanggal di sini, misalnya simpan ke DB
                    // Contoh dengan Eloquent Laravel:
                    IzinSakitDetail::create([
                        'id' => Uuid::uuid4(),
                        'id_sakit' => $kode,
                        'id_karyawan' => $request->id_karyawan,
                        'nama_karyawan' => $request->name,
                        'tgl_sakit' => $tanggal,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $pengaju = Karyawan::where('id', $request->id_karyawan)->first();
            $pengganti = Karyawan::where('id', $request->karyawan_pengganti)->first();
            $atasan1 = Karyawan::where('id', $request->atasan_1)->first();
            $atasan2 = Karyawan::where('id', $request->atasan_2)->first();

            $add_izin = new IzinSakit();
            $add_izin->id = $kode;
            $add_izin->karyawan_id = $request->id_karyawan;
            $add_izin->karyawan_nik = $pengaju->nomor_identitas_karyawan;
            $add_izin->nama_karyawan = $request->name;
            $add_izin->ttd_user = $fileName;

            $add_izin->karyawan_pengganti_id = $request->karyawan_pengganti;
            $add_izin->nama_karyawan_pengganti = $pengganti->name;

            $add_izin->surat_dokter = $file_save;
            $add_izin->tanggal_sakit = $start_tanggal_sakit . ' - ' . $end_tanggal_sakit;
            $add_izin->jumlah_hari = $jumlah_hari;

            // $add_izin->keterangan_sakit = $request->keterangan_sakit;
            $add_izin->status_sakit = 0;

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
            $quick->title_quick = 'Pengajuan Izin sakit';
            $quick->message_quick = 'Ada Pengajuan Izin sakit Baru Dari ' . $request->name;
            $quick->status = 0;
            $quick->quick_type = 'izin_sakit';
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
                'table' => 'izin_sakit',
                'activity' => 'add',
                'description' => 'Mengajukan izin sakit',
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email_atasan = $atasan1?->email_kantor ?: $atasan1?->email;
            $email              = $email_atasan;
            $nama_penerim       = $atasan1->name;
            $nama               = $request->name;
            $tanggal_mulai      = Carbon::parse($add_izin->start_tanggal_sakit)->format('d-m-Y');
            $tanggal_selesai    = Carbon::parse($add_izin->end_tanggal_sakit)->format('d-m-Y');
            $judul              = 'Pengajuan Izin Sakit';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan izin sakit dari :';

            $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

            $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
            $link               = url('/sakit/approve_sakit_detail/' . $kode);
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
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function detail_sakit($id, $id_sakit)
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
        $data = IzinSakit::with('IzinSakitDetail')->where('id', $id_sakit)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.izin.sakit_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_sakit,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_sakit($id, Request $request)
    {
        // dd($request->all());
        $query = IzinSakit::where('id', $request->id_sakit_hapus)->first();
        $id_sakit = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_sakit->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus sakit',
            'id_table' => $request->id_sakit_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'izin_sakit',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan sakit ' .  $query->tanggal_sakit,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_sakit_gagal', 'Data sakit Tidak Ditemukan');
        } elseif ($query->status_sakit != 0) {
            $request->session()->flash('hapus_sakit_gagal', 'sakit sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_sakit_hapus)->delete();
            if (filled($query->surat_dokter)) {
                Storage::disk('public')->delete('surat_dokter/' . $query->surat_dokter);
            }

            IzinSakitDetail::where('id_sakit', $query->id)->delete();
            $query->delete();
            $request->session()->flash('hapus_sakit_sukses', 'Form sakit Berhasil Dihapus');
        }
        return redirect('izin/sakit/' . $id);
    }
    public function get_sakit_approval(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_sakit = IzinSakit::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_sakit', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_sakit', 1);
                    });
            })->get();
            // dd($data_sakit);
            return response()->json([
                'code' => 200,
                'data' => $data_sakit

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function sakit_approval($menu_id)
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
        return view('users.izin.sakit_approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_sakit_riwayat_approval(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_sakit = IzinSakit::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_sakit', '!=', 0)
                        ->where('status_approve_atasan1', '!=', null);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_sakit', '!=', 0)
                            ->where('status_approve_atasan2', '!=', null);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
            // dd($data_sakit);
            return response()->json([
                'code' => 200,
                'data' => $data_sakit

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_sakit_detail($id, Request $request)
    {
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
            $data_sakit = IzinSakit::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            // $presensi = $user_karyawan->Absensi->where('LogTime',)
            $menu_id = Auth::user()->menu_active;
            return view('users.izin.sakit_approval_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'data' => $data_sakit,
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
    public function tolak_pengajuan_sakit(Request $request)
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
            $data_sakit = IzinSakit::where('id', $request->id_sakit)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $pengaju = Karyawan::where('id', $data_sakit->karyawan_id)->first();
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
            if ($data_sakit->id_user_atasan == $user_karyawan->id) {
                $data_sakit->update([
                    'status_approve_atasan1' => 1,
                    'status_sakit' => 5,
                    'ttd_atasan' => $fileName,
                ]);
            } else {
                $data_sakit->update([
                    'status_approve_atasan2' => 1,
                    'status_sakit' => 5,
                    'ttd_atasan2' => $fileName,
                ]);
            }
            QuickPanel::where('content_id', $request->id_sakit)->update([
                'status' => 1,
            ]);
            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval sakit',
                'id_table' => $data_sakit->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'izin_sakit',
                'activity' => 'add',
                'description' => 'Menolak pengajuan sakit ' .  $data_sakit->nama_karyawan,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email              = $pengaju?->email;
            $nama_penerim       = $data_sakit->nama_karyawan;
            $nama               = $user_karyawan->name;
            $tanggal_mulai      = Carbon::parse($data_sakit->tanggal_sakit)->format('d-m-Y');
            $judul              = 'Penolakan Izin Sakit';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan izin sakit anda ditolak oleh :';

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
    public function approve_pengajuan_sakit(Request $request)
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
            $data_sakit = IzinSakit::where('id', $request->id_sakit)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $user_karyawan_pengajuan = Karyawan::where('id', $data_sakit->karyawan_id)->first();
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
            $count_sakit = IzinSakit::whereIn('status_sakit', [5, 2])->whereDate('created_at', $data_sakit->created_at)->where('id', '!=', $data_sakit->id)->get()->count();
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

            $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FIK' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($no_form);

            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            if ($data_sakit->id_user_atasan2 == $data_sakit->id_user_atasan) {
                $data_sakit->update([
                    'status_approve_atasan1' => 1,
                    'status_approve_atasan2' => 1,
                    'status_sakit' => 2,
                    'waktu_approve' => date('Y-m-d H:i:s'),
                    'waktu_approve2' => date('Y-m-d H:i:s'),
                    'no_form_sakit' => $no_form,
                    'ttd_atasan' => $fileName,
                    'ttd_atasan2' => $fileName,
                    'jam_finger' => $request->jam_finger,
                ]);

                QuickPanel::where('content_id', $request->id_sakit)->update([
                    'read_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval sakit',
                    'id_table' => $data_sakit->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'izin_sakit',
                    'activity' => 'add',
                    'description' => 'menyetujui pengajuan sakit sebagai atasan 1 ' .  $data_sakit->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
            } else {
                if ($data_sakit->id_user_atasan == $user_karyawan->id) {
                    $data_sakit->update([
                        'status_approve_atasan1' => 1,
                        'status_sakit' => 1,
                        'waktu_approve' => date('Y-m-d H:i:s'),
                        'no_form_sakit' => $no_form,
                        'ttd_atasan' => $fileName,
                        'jam_finger' => $request->jam_finger,
                    ]);

                    QuickPanel::where('content_id', $request->id_sakit)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $quick = new QuickPanel();
                    $quick->id_quick = Uuid::uuid4();
                    $quick->user_id_from = $data_sakit->id_user_atasan;
                    $quick->user_id_to = $data_sakit->id_user_atasan2;
                    $quick->title_quick = 'Pengajuan Izin Sakit';
                    $quick->message_quick = 'Ada Pengajuan Izin Sakit Baru Dari ' . $data_sakit->nama_karyawan;
                    $quick->status = 0;
                    $quick->quick_type = 'izin_sakit';
                    $quick->menu_id = Auth::user()->menu_active;
                    $quick->content_id = $data_sakit->id;
                    $quick->created_at = date('Y-m-d H:i:s');
                    $quick->updated_at = date('Y-m-d H:i:s');
                    $quick->save();
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval sakit',
                        'id_table' => $data_sakit->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_sakit',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan sakit sebagai atasan 1 ' .  $data_sakit->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                } else {
                    $data_sakit->update([
                        'status_approve_atasan2' => 1,
                        'status_sakit' => 2,
                        'no_form_sakit' => $no_form,
                        'ttd_atasan2' => $fileName,
                        'waktu_approve2' => date('Y-m-d H:i:s'),
                        'jam_finger' => $request->jam_finger,
                    ]);
                    QuickPanel::where('content_id', $request->id_sakit)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval sakit',
                        'id_table' => $data_sakit->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'izin_sakit',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan sakit sebagai atasan 2 ' .  $data_sakit->nama_karyawan,
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
    public function cetak_form_sakit($id, $karyawan_id)
    {
        $sakit = IzinSakit::with('Atasan1', 'Atasan2', 'AtasanHRD')->with('IzinSakitDetail')->where('id', $id)->first();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_sakit' => $sakit,
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
            ->loadView('users/izin/sakit_form', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_PULANG_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
}
