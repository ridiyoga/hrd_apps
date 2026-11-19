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
use App\Models\Approval;
use App\Models\ApprovalUser;
use App\Models\CutiDetail;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Izin;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KategoriCuti;
use App\Models\KuotaCutiKaryawan;
use App\Models\LevelJabatan;
use App\Models\Lokasi;
use App\Models\Menu;
use App\Models\QuickPanel;
use App\Models\ResetCuti;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\StrukturModel;
use PDF;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class CutiUserController extends Controller
{
    public function index($menu_id)
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
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->with('PenempatanKerja')->first();
        $get_kategori_cuti = KategoriCuti::where('status', 1)->orderBy('nama_cuti', 'ASC')->get();

        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.cuti.index', [
            'user_karyawan' => $user_karyawan,
            'get_kategori_cuti' => $get_kategori_cuti,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_approval($menu_id)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $jabatan_karyawan = KaryawanJabatan::with('Jabatan.LevelJabatan')
                ->where('karyawan_id', $user_karyawan->id)
                ->where('is_active', 1)
                ->where('main', 1)
                ->first();

            if (!$jabatan_karyawan) {
                $jabatan_karyawan = KaryawanJabatan::with('Jabatan.LevelJabatan')
                    ->where('karyawan_id', $user_karyawan->id)
                    ->where('is_active', 1)
                    ->first();
            }

            $levelPengaju = (int) $jabatan_karyawan->Jabatan->LevelJabatan->level_jabatan;

            $access = ApprovalUser::with([
                'Approval',
                'Approval.ApprovalGroup'
            ])
                ->whereHas(
                    'Approval.ApprovalGroup',
                    fn($q) => $q->where('menu_id', 'LIKE', '%' . $menu_id . '%')
                )
                ->whereHas('Approval', function ($q) use ($jabatan_karyawan) {
                    $q->where('holding_id', $jabatan_karyawan->holding_id)
                        ->where('level_approval', $jabatan_karyawan->Jabatan->level_id);
                })
                ->where('karyawan_id', $user_karyawan->id)
                ->first();
            // dd($levelPengaju, $access);
            if ($access == null) {
                $access = ApprovalUser::with([
                    'Approval',
                    'Approval.ApprovalGroup'
                ])
                    ->whereHas(
                        'Approval.ApprovalGroup',
                        fn($q) => $q->where('menu_id', 'LIKE', '%' . $menu_id . '%')
                    )
                    ->whereHas('Approval', function ($q) use ($jabatan_karyawan) {
                        $q->where('holding_id', $jabatan_karyawan->holding_id);
                    })
                    ->where('karyawan_id', $user_karyawan->id)
                    ->first();
            }

            $selectedApproval = $access?->Approval;

            if ($selectedApproval == null) {
                $approvalCandidates = Approval::with('ApprovalGroup')
                    ->where('holding_id', $jabatan_karyawan->holding_id)
                    ->where('level_approval', $jabatan_karyawan->Jabatan->level_id)
                    ->get();

                $selectedApproval = $approvalCandidates->first(function ($approval) use ($menu_id, $jabatan_karyawan) {
                    $menuIds = $this->approvalArrayValues($approval->ApprovalGroup?->menu_id);
                    $departemenIds = $this->approvalArrayValues($approval->departemen_id);

                    return in_array((string) $menu_id, $menuIds, true)
                        && (count($departemenIds) === 0 || in_array((string) $jabatan_karyawan->departemen_id, $departemenIds, true));
                });

                if ($selectedApproval == null) {
                    $selectedApproval = $approvalCandidates->first(function ($approval) use ($menu_id) {
                        return in_array((string) $menu_id, $this->approvalArrayValues($approval->ApprovalGroup?->menu_id), true);
                    });
                }
            }

            if ($selectedApproval == null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Access not found, Hubungi Admin'
                ]);
            }
            // dd($selectedApproval);
            $currentApprovalId = $selectedApproval->parent_approval_id;
            $atasan1 = null;
            $atasan2 = null;
            $found = 0;

            while ($currentApprovalId && $found < 2) {

                $approvalUser = ApprovalUser::with(['User', 'Karyawan', 'Approval.levelJabatan'])
                    ->whereHas('Approval', function ($q) use ($currentApprovalId) {
                        $q->where('id_approval', $currentApprovalId);
                    })
                    ->whereHas('Approval.levelJabatan', function ($p) {

                        $p->orderBy('level_jabatan', 'desc');
                    })
                    ->where('karyawan_id', '!=', $user_karyawan->id)
                    ->whereHas('Karyawan')
                    ->first();

                if ($approvalUser) {

                    $jabatan_atasan = KaryawanJabatan::with('Jabatan.LevelJabatan')
                        ->where('karyawan_id', $approvalUser->karyawan_id)
                        ->where('is_active', 1)
                        ->first();

                    if (!$jabatan_atasan && !$approvalUser->Approval?->levelJabatan) {
                        break; // safety
                    }

                    $levelAtasan = (int) (
                        $approvalUser->Approval?->levelJabatan?->level_jabatan
                        ?? $jabatan_atasan?->Jabatan?->LevelJabatan?->level_jabatan
                    );

                    /*
                    =================================================
                    KONDISI KHUSUS LEVEL
                    =================================================
                    */
                    // dd($approvalUser, $levelAtasan);
                    // ✅ STAFF (6) & KOORDINATOR (5) → atasan1: SPV (4), atasan2: MANAGER (2)
                    if ($levelPengaju == 6 || $levelPengaju == 5) {
                        if ($levelAtasan == 4 && !$atasan1) {
                            $atasan1 = $approvalUser;
                            $found++;
                        } elseif ($levelAtasan == 3 && !$atasan2) {
                            $atasan2 = $approvalUser;
                            $found++;
                        } elseif ($levelAtasan == 2 && !$atasan2) {
                            $atasan2 = $approvalUser;
                            $found++;
                        }
                    }

                    // ✅ SPV (4) → cukup MANAGER saja
                    // elseif ($levelPengaju == 4) {
                    //     if ($levelAtasan == 2 || $levelAtasan == 3) { // Asumsi level 3 juga disetujui jika tidak ada 2
                    //         $atasan1 = $approvalUser;
                    //         $atasan2 = $approvalUser;
                    //         break;
                    //     }
                    // }
                    elseif ($levelPengaju == 4) {
                        if ($levelAtasan == 3 && !$atasan1) { // Asumsi level 3 juga disetujui jika tidak ada 2
                            $atasan1 = $approvalUser;
                            $found++;
                        } elseif ($levelAtasan == 2 && !$atasan2) {
                            $atasan2 = $approvalUser;
                            $found++;
                        }
                    }

                    // ✅ DEFAULT BEHAVIOR (Untuk level selain 4, 5, dan 6)
                    else {
                        if ($found == 0) {
                            $atasan1 = $approvalUser;
                        } elseif ($found == 1) {
                            $atasan2 = $approvalUser;
                        }
                        $found++;
                    }
                }

                $parentApproval = Approval::where('id_approval', $currentApprovalId)->first();
                $currentApprovalId = $parentApproval?->parent_approval_id;
            }

            // Fallback: kalau atasan 1 kosong tapi atasan 2 ada (antisipasi jika tidak ada SPV di jalur hirarki)
            if (!$atasan1 && $atasan2) {
                $atasan1 = $atasan2;
            }

            // Fallback: kalau atasan 2 kosong → samakan dengan atasan 1
            if (!$atasan2) {
                $atasan2 = $atasan1;
            }

            return response()->json([
                'code' => 200,
                'user_karyawan' => $user_karyawan,
                'atasan1' => $atasan1,
                'atasan2' => $atasan2,
                'menu_id' => $menu_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    private function approvalArrayValues($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : [$value];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return collect($value)
            ->flatten()
            ->filter(fn($item) => $item !== null && $item !== '')
            ->map(fn($item) => (string) $item)
            ->values()
            ->all();
    }
    public function get_karyawan_pengganti($id)
    {
        try {
            $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $id)
                ->with('Karyawan')
                ->get();
            $departemen_id = $karyawan_jabatan->pluck('departemen_id');
            // dd($departemen_id);
            $karyawan_pengganti = KaryawanJabatan::whereIn('departemen_id', $departemen_id)
                ->where('karyawan_id', '!=', $id)
                ->with('Karyawan')
                ->distinct('karyawan_id')->get();
            // dd($karyawan_pengganti);
            return response()->json([
                'code' => 200,
                'karyawan_pengganti' => $karyawan_pengganti
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_cuti($id)
    {
        // dd('ok');
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        try {
            $cuti      = Cuti::with('KategoriCuti')->where('karyawan_id', $user_karyawan->id)->where('status_cuti', 0)->get();
            return response()->json([
                'code' => 200,
                'cuti' => $cuti
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_saldo_cuti($id)
    {
        try {
            $saldo_cuti = KuotaCutiKaryawan::with('PeriodeGroup')->where('karyawan_id', $id)->where('status', 1)->first();
            if ($saldo_cuti == null) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Saldo Cuti tidak ditemukan.',
                ]);
            }
            return response()->json([
                'code' => 200,
                'saldo_cuti' => $saldo_cuti
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function editCuti(Request $request, $id)
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
        $cuti = Cuti::where('id', $id)->first();
        if ($cuti == null) {
            $request->session()->flash('cuti_not_found', 'Data tidak ditemukan');
            return redirect()->back();
        }
        $atasan_1 = Karyawan::where('id', $cuti->id_user_atasan)->first();
        $atasan_2 = Karyawan::where('id', $cuti->id_user_atasan2)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        return view('users.cuti.edit', [
            'id_cuti' => $id,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'user_karyawan' => $user_karyawan,
            'all_menu_bar' => $all_menu_bar,
            'data' => $cuti,
            'atasan_1' => $atasan_1,
            'atasan_2' => $atasan_2
        ]);
    }
    public function get_kategori_cuti($id)
    {
        try {
            $kategori_cuti      = KategoriCuti::where('status', 1)->orderBy('nama_cuti', 'ASC')->get()->toArray('id', 'nama_cuti', 'jumlah_hari', 'target_gender', 'target_status_nikah');
            return response()->json([
                'code' => 200,
                'kategori_cuti' => $kategori_cuti
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_pengajuan_cuti(Request $request, $id)
    {
        // dd($request->all());
        if ($request->cuti == 1) {
            $cuti_val = 'required|array|min:1';
            $berulang_val = 'required|date';
        } else {
            $cuti_val = 'nullable';
            $berulang_val = 'nullable';
        }
        try {
            $rules = [
                'id_karyawan' => 'required',
                'cuti' => 'required',
                'kategori_cuti' => 'sometimes|required',
                'tanggal_cuti_berulang' => $cuti_val,
                'tanggal_cuti_berulang.*' => $berulang_val,
                'karyawan_pengganti' => 'required',
                'atasan_1' => 'required',
                'atasan_2' => 'required',
                'keterangan_cuti' => 'nullable',
                'signature' => 'required',
            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'karyawan_pengganti.required' => 'Karyawan Pengganti wajib diisi',
                'cuti.required' => 'Kategori Cuti wajib diisi',
                'kategori_cuti.required' => 'Pilihan Cuti wajib diisi',
                'tanggal_cuti_berulang.required' => 'Tanggal cuti wajib diisi.',
                'tanggal_cuti_berulang.min' => 'Minimal pilih satu tanggal cuti.',
                'tanggal_cuti_berulang.*.required' => 'Tanggal cuti wajib diisi.',
                'atasan_1.required' => 'Atasan 1 wajib Terisi',
                'atasan_2.required' => 'Atasan 2 wajib Terisi',
                'signature.required' => 'Tanda Tangan wajib diisi',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'errors' => $validator->errors(),
                ]);
            }
            DB::beginTransaction();
            $kode = Uuid::uuid4();
            $tanggal_array = $request->input('tanggal_cuti_berulang');
            $manajemen_saldo_cuti = KuotaCutiKaryawan::where('karyawan_id', $request->id_karyawan)->where('status', 1)->first();
            // dd($request->cuti);
            if ($request->cuti == 1) {
                $cek_cuti = Cuti::where('karyawan_id', $request->id_karyawan)->where('status_cuti', 0)->exists();
                if ($cek_cuti) {

                    return response()->json([
                        'code' => 203,
                        'errors' => [
                            'kategori_cuti' => 'Ada pengajuan cuti yang belum ditindaklanjuti'
                        ]
                    ]);
                    // dd('haha');

                } else {
                    $start_tanggal_cuti = collect($tanggal_array)->first();
                    $end_tanggal_cuti = collect($tanggal_array)->last();
                    // dd($start_tanggal_cuti, $end_tanggal_cuti);
                    $jumlah_hari = count($tanggal_array);
                    // $hari = intval($get_kategori_cuti->jumlah_hari) - 1;

                    // dd($jumlah_hari, 'saldo:' . $manajemen_saldo_cuti->sisa);

                    if ($manajemen_saldo_cuti->sisa >= $jumlah_hari) {
                        if (!empty($tanggal_array)) {
                            foreach ($tanggal_array as $tanggal) {
                                // Proses setiap tanggal di sini, misalnya simpan ke DB
                                // Contoh dengan Eloquent Laravel:
                                CutiDetail::create([
                                    'id' => Uuid::uuid4(),
                                    'id_cuti' => $kode,
                                    'id_karyawan' => $request->id_karyawan,
                                    'nama_karyawan' => $request->name,
                                    'tgl_cuti' => $tanggal,
                                    'tipe' => 1,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    } else {
                        // dd('hehe');
                        return response()->json([
                            'code' => 202,
                            'errors' => [
                                'kategori_cuti' => 'Cuti Melebihi sisa kuota!'
                            ]
                        ]);
                    }
                    // dd('hehe');

                }
            } else {
                $get_kategori_cuti = KategoriCuti::where('id', $request->kategori_cuti)->first();
                if (!$get_kategori_cuti) {
                    return response()->json([
                        'code' => 422,
                        'errors' => [
                            'kategori_cuti' => 'Pilihan Cuti Tidak Ditemukan'
                        ]
                    ]);
                }
                $hari = intval($get_kategori_cuti->jumlah_hari) - 1;
                // dd($hari);
                $start_tanggal_cuti = $request->tanggal_cuti;
                $end_tanggal_cuti = date('Y-m-d', strtotime($request->tanggal_cuti . ' + ' . $hari . ' days'));
                $mulai = Carbon::parse($start_tanggal_cuti);
                $akhir = Carbon::parse($end_tanggal_cuti);
                $jumlah_hari = $mulai->diffInDays($akhir) + 1;

                while ($mulai->lte($akhir)) {

                    CutiDetail::create([
                        'id' => Uuid::uuid4(),
                        'id_cuti' => $kode,
                        'id_karyawan' => $request->id_karyawan,
                        'nama_karyawan' => $request->name,
                        'tgl_cuti' => $mulai->format('Y-m-d'),
                        'tipe' => 2,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $mulai->addDay();
                }
            }
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

            $nama_karyawan_pengganti = Karyawan::where('id', $request->karyawan_pengganti)->first();
            $nama_atasan1 = Karyawan::where('id', $request->atasan_1)->first();
            $nama_atasan2 = Karyawan::where('id', $request->atasan_2)->first();
            $id_kuota_cuti = Karyawan::where('id', $request->id_karyawan)->with('KuotaCuti')->first();
            // dd($id_kuota_cuti->KuotaCuti->id);




            // dd($jumlah_hari);

            $add_cuti = new Cuti();
            $add_cuti->id = $kode;

            if ($request->kategori_cuti == null) {
                $sisa = (int) ($manajemen_saldo_cuti->sisa ?? 0);
                $total_cuti = (int) ($jumlah_hari ?? 0);
                $saldo_terpakai = (int)($manajemen_saldo_cuti->terpakai ?? 0);
                $new_kuota = $sisa - $total_cuti;
                $terpakai = $saldo_terpakai + $total_cuti;

                $add_cuti->kuota = $manajemen_saldo_cuti->kuota;
                $add_cuti->terpakai = $terpakai;
                $add_cuti->sisa = $new_kuota;
            }

            $add_cuti->karyawan_id = $request->id_karyawan;
            $add_cuti->nama_karyawan = $request->name;
            $add_cuti->ttd_user = $fileName;
            $add_cuti->kategori_cuti_id = $request->kategori_cuti;
            $add_cuti->nama_cuti = $get_kategori_cuti->nama_cuti ?? 'Cuti Tahunan';
            $add_cuti->tanggal_pengajuan = date('Y-m-d H:i:s');
            $add_cuti->tanggal_mulai = $start_tanggal_cuti;
            $add_cuti->tanggal_selesai = $end_tanggal_cuti;
            $add_cuti->keterangan_cuti = $request->keterangan_cuti;
            $add_cuti->status_cuti = 0;

            $add_cuti->karyawan_pengganti_id = $request->karyawan_pengganti;
            $add_cuti->nama_karyawan_pengganti = $nama_karyawan_pengganti->name ?? '-';

            $add_cuti->status_approve_atasan1 = null;
            $add_cuti->id_user_atasan = $request->atasan_1;
            $add_cuti->nama_atasan = $nama_atasan1->name;
            $add_cuti->ttd_atasan = null;
            $add_cuti->waktu_approve = null;

            $add_cuti->status_approve_atasan2 = null;
            $add_cuti->id_user_atasan2 = $request->atasan_2;
            $add_cuti->nama_atasan2 = $nama_atasan2->name;
            $add_cuti->ttd_atasan2 = null;
            $add_cuti->waktu_approve2 = null;

            $add_cuti->id_kuota_karyawan = $id_kuota_cuti->KuotaCuti->id ?? null;

            $add_cuti->jumlah_hari = $jumlah_hari;

            $add_cuti->created_at = date('Y-m-d H:i:s');






            $add_cuti->save();
            // SAVE QUICK PANEL
            $quick = new QuickPanel();
            $quick->id_quick = Uuid::uuid4();
            $quick->user_id_from = $request->id_karyawan;
            $quick->user_id_to = $request->atasan_1;
            $quick->title_quick = 'Pengajuan Cuti';
            $quick->message_quick = 'Ada Pengajuan Cuti Baru Dari ' . $request->name;
            $quick->status = 0;
            $quick->quick_type = 'cuti';
            $quick->menu_id = Auth::user()->menu_active;
            $quick->content_id = $add_cuti->id;
            $quick->created_at = date('Y-m-d H:i:s');
            $quick->updated_at = date('Y-m-d H:i:s');
            $quick->save();

            $holdings = Holding::where('id', $id_kuota_cuti->kontrak_kerja)->first();
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
                'table' => 'cutis',
                'activity' => 'add',
                'description' => 'Mengajukan Cuti ' .  $start_tanggal_cuti . ' sampai ' . $end_tanggal_cuti,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $email_atasan = $nama_atasan1?->email_kantor ?: $nama_atasan1?->email;
            $email              = $email_atasan;
            $nama_penerim       = $nama_atasan1->name;
            $nama               = $request->name;
            $tanggal_mulai      = Carbon::parse($add_cuti->tanggal_mulai)->format('d-m-Y');
            $tanggal_selesai    = Carbon::parse($add_cuti->tanggal_selesai)->format('d-m-Y');
            $judul              = 'Pengajuan Cuti';
            $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan cuti dari :';

            $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

            $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
            $link               = url('/approval/cuti/cutiDetail/' . $kode);
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

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan cuti berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    // public function proses_edit_cuti(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         $rules = [
    //             'id_karyawan' => 'required',
    //             'cuti' => 'required',
    //             'kategori_cuti' => 'sometimes|required',
    //             'tanggal_cuti' => 'required',
    //             'karyawan_pengganti' => 'nullable',
    //             'atasan_1' => 'required',
    //             'atasan_2' => 'required',
    //             'keterangan_cuti' => 'nullable'
    //         ];
    //         $messages = [
    //             'id_karyawan.required' => 'Karyawan wajib diisi',
    //             'cuti.required' => 'Kategori Cuti wajib diisi',
    //             'kategori_cuti.required' => 'Pilihan Cuti wajib diisi',
    //             'tanggal_cuti.required' => 'Tanggal Cuti wajib diisi',
    //             'atasan_1.required' => 'Atasan 1 wajib Terisi',
    //             'atasan_2.required' => 'Atasan 2 wajib Terisi',
    //         ];
    //         $validator = Validator::make($request->all(), $rules, $messages);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => 422,
    //                 'errors' => $validator->errors(),
    //             ]);
    //         }
    //         if ($request->cuti == 1) {
    //             $pisah_tanggal = explode(' - ', $request->tanggal_cuti);
    //             $start_tanggal_cuti = $pisah_tanggal[0];
    //             $end_tanggal_cuti = $pisah_tanggal[1];
    //             // dd($start_tanggal_cuti, $end_tanggal_cuti);
    //         } else {
    //             $get_kategori_cuti = KategoriCuti::where('id', $request->kategori_cuti)->first();
    //             if (!$get_kategori_cuti) {
    //                 return response()->json([
    //                     'code' => 422,
    //                     'errors' => [
    //                         'kategori_cuti' => 'Pilihan Cuti Tidak Ditemukan'
    //                     ]
    //                 ]);
    //             }
    //             $hari = intval($get_kategori_cuti->jumlah_hari) - 1;
    //             $start_tanggal_cuti = $request->tanggal_cuti;
    //             $end_tanggal_cuti = date('Y-m-d', strtotime($request->tanggal_cuti . ' + ' . $hari . ' days'));
    //         }
    //         $update = Cuti::where('id', $request->id_cuti)->first();
    //         if (!$update) {
    //             return response()->json([
    //                 'code' => 422,
    //                 'errors' => [
    //                     'kategori_cuti' => 'Pilihan Cuti Tidak Ditemukan'
    //                 ]
    //             ]);
    //         }
    //         $update->nama_cuti = $request->cuti;
    //         $update->kategori_cuti_id = $request->kategori_cuti;
    //         $update->tanggal_mulai = $start_tanggal_cuti;
    //         $update->tanggal_selesai = $end_tanggal_cuti;
    //         $update->karyawan_pengganti_id = $request->karyawan_pengganti;
    //         $update->id_user_atasan = $request->atasan_1;
    //         $update->id_user_atasan2 = $request->atasan_2;
    //         $update->keterangan_cuti = $request->keterangan_cuti;
    //         $update->tanggal_pengajuan = date('Y-m-d H:i:s');
    //         $update->save();
    //         return response()->json([
    //             'code' => 200,
    //             'message' => 'Pengajuan cuti berhasil disimpan.',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'code' => 500,
    //             'errors' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
    //         ]);
    //     }
    // }
    public function cutiApprove($id)
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $user = Karyawan::join('jabatans', 'jabatans.id', '=', 'karyawans.jabatan_id')
            ->join('level_jabatans', 'jabatans.level_id', '=', 'level_jabatans.id')
            ->join('departemens', 'departemens.id', '=', 'karyawans.dept_id')
            ->join('divisis', 'divisis.id', '=', 'karyawans.divisi_id')
            ->where('karyawans.id', $user_karyawan->id)->first();
        $data   = Cuti::with('KategoriCuti')->where('cutis.id', $id)
            ->join('karyawans', 'karyawans.id', '=', 'cutis.user_id')
            ->select('cutis.*', 'karyawans.name', 'karyawans.kontrak_kerja', 'karyawans.kuota_cuti_tahunan')
            ->first();
        // dd($data);
        $get_id_backup = Karyawan::where('id', $data->user_id_backup)->first();
        return view('users.cuti.approvecuti', [
            'user'  => $user,
            'user_karyawan'  => $user_karyawan,
            'get_id_backup'  => $get_id_backup,
            'data'  => $data
        ]);
    }
    public function cutiAbsen(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        if ($request->approve_atasan == '') {
            $request->session()->flash('atasan1NULL');
            return redirect('cuti');
        } else if ($request->approve_atasan2 == '') {
            $request->session()->flash('atasan2NULL');
            return redirect('cuti');
        } else {
        }
        // No form
        $no_form = NULL;
        // dd($no_form);
        if ($request->cuti == 'Diluar Cuti Tahunan') {
            $jumlah_hari = explode(' ', $request->jumlah_cuti);
            $jumlah_kuota = explode(' ', $request->kuota_cuti);
            $hari = trim($jumlah_hari[0]);
            $hitung_end_date        = Carbon::parse($request->tanggal_cuti)->addDays($hari)->addDays(-1);
            $kuota = trim($jumlah_kuota[0]);
            $data_interval  = $hari;
            $startDate = $request->tanggal_cuti;
            $endDate = $hitung_end_date;
            $cek_tgl = Cuti::where('tanggal_mulai', date('Y-m-d', strtotime($startDate)))->where('user_id', $user_karyawan->id)->count();
            if ($cek_tgl > 0) {
                $request->session()->flash('tgldigunakan', 'Anda Tidak Memiliki Kuota Cuti');
                return redirect('cuti');
            }
            // dd($hitung_end_date);
            $date1          = new DateTime($request->tanggal_cuti);
            $date2          = new DateTime($hitung_end_date);
            $kategori_cuti = $request->kategori_cuti;
            // dd(Karyawan::where('id', $request->user_backup)->value('name'));
            $insert = new Cuti();
            $insert->user_id = Karyawan::where('id', $user_karyawan->id)->value('id');
            $insert->nama_user = Karyawan::where('id', $user_karyawan->id)->value('name');
            $insert->kategori_cuti_id = KategoriCuti::where('id', $kategori_cuti)->value('id');
            $insert->nama_cuti = $request->cuti;
            $insert->tanggal = date('Y-m-d H:i:s');
            $insert->tanggal_mulai = date('Y-m-d', strtotime($startDate));
            $insert->tanggal_selesai = date('Y-m-d', strtotime($endDate));
            $insert->total_cuti = $hari;
            $insert->keterangan_cuti = $request->keterangan_cuti;
            $insert->foto_cuti = NULL;
            $insert->status_cuti = 0;
            $insert->user_id_backup = Karyawan::where('id', $request->user_backup)->value('id');
            $insert->nama_user_backup = Karyawan::where('id', $request->user_backup)->value('name');
            $insert->approve_atasan = Karyawan::where('id', $request->id_user_atasan)->value('name');
            $insert->approve_atasan2 = Karyawan::where('id', $request->id_user_atasan2)->value('name');
            $insert->id_user_atasan = Karyawan::where('id', $request->id_user_atasan)->value('id');
            $insert->nama_user_atasan = Karyawan::where('id', $request->id_user_atasan)->value('name');
            $insert->id_user_atasan2 = Karyawan::where('id', $request->id_user_atasan2)->value('id');
            $insert->nama_user_atasan2 = Karyawan::where('id', $request->id_user_atasan2)->value('name');
            $insert->ttd_atasan = NULL;
            $insert->ttd_atasan2 = NULL;
            $insert->waktu_approve = NULL;
            $insert->waktu_approve2 = NULL;
            $insert->catatan = NULL;
            $insert->catatan2 = NULL;
            $insert->no_form_cuti = $no_form;
            $insert->save();
        } else {
            // cuti tahunan
            $date_range = explode(' - ', $request->tanggal_cuti);
            $startDate = trim($date_range[0]);
            $endDate = trim($date_range[1]);
            $date1          = new DateTime($startDate);
            $date2          = new DateTime($endDate);
            $interval       = $date1->diff($date2);
            $data_interval  = $interval->days + 1;
            $kategori_cuti = NULL;
            $hMin14         = now()->parse($request->startDate)->addDays(14); //2024-04-18
            $format_startDate = date('Y-m-d', strtotime($startDate));
            $cek_tgl = Cuti::where('tanggal_mulai', $format_startDate)->where('user_id', $user_karyawan->id)->count();
            if ($cek_tgl > 0) {
                $request->session()->flash('tgldigunakan', 'Anda Tidak Memiliki Kuota Cuti');
                return redirect('cuti');
            }
            $format_hmin14 = date('Y-m-d', strtotime($hMin14));
            $kuota_cuti     = Karyawan::where('id', $request->id_user)->first();
            if ($format_startDate >= $format_hmin14) {
                if ($kuota_cuti->kuota_cuti_tahunan >= $data_interval) {
                    Cuti::create([
                        'user_id' => Karyawan::where('id', $user_karyawan->id)->value('id'),
                        'nama_user' => Karyawan::where('id', $user_karyawan->id)->value('name'),
                        'kategori_cuti_id' => KategoriCuti::where('id', $kategori_cuti)->value('id'),
                        'nama_cuti' => $request->cuti,
                        'tanggal' => date('Y-m-d H:i:s'),
                        'tanggal_mulai' => date('Y-m-d', strtotime($startDate)),
                        'tanggal_selesai' => date('Y-m-d', strtotime($endDate)),
                        'total_cuti' => $data_interval,
                        'keterangan_cuti' => $request->keterangan_cuti,
                        'foto_cuti' => NULL,
                        'status_cuti' => 0,
                        'user_id_backup' => Karyawan::where('id', $request->user_backup)->value('id'),
                        'nama_user_backup' => Karyawan::where('id', $request->user_backup)->value('name'),
                        'approve_atasan' => Karyawan::where('id', $request->id_user_atasan)->value('name'),
                        'approve_atasan2' => Karyawan::where('id', $request->id_user_atasan2)->value('name'),
                        'id_user_atasan' => Karyawan::where('id', $request->id_user_atasan)->value('id'),
                        'id_user_atasan2' => Karyawan::where('id', $request->id_user_atasan2)->value('id'),
                        'ttd_atasan' => NULL,
                        'ttd_atasan2' => NULL,
                        'waktu_approve' => NULL,
                        'waktu_approve2' => NULL,
                        'catatan' => NULL,
                        'catatan2' => NULL,
                        'no_form_cuti' => $no_form,
                    ]);

                    $request->session()->flash('addcutisuccess', 'Berhasil');
                    return redirect('cuti');
                } else {
                    $request->session()->flash('addcutierror1', 'Anda Tidak Memiliki Kuota Cuti');
                    return redirect('cuti');
                }
            } else {
                $request->session()->flash('addcutierror2', 'Pengajuan Harus H-14 untuk cuti');
                return redirect('cuti');
            }
        }
        $request->session()->flash('addcutisuccess', 'Berhasil');
        return redirect('cuti');
    }
    public function cutiApproveProses(Request $request)
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        if ($request->approve == 'not_approve') {
            if ($request->signature != null) {
                $folderPath     = public_path('signature/cuti/');
                $image_parts    = explode(";base64,", $request->signature);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type     = $image_type_aux[1];
                $image_base64   = base64_decode($image_parts[1]);
                $uniqid         = date('y-m-d') . '-' . uniqid();
                $file           = $folderPath . $uniqid . '.' . $image_type;
                file_put_contents($file, $image_base64);
            } else {
                $uniqid = NULL;
            }
            if ($request->status_cuti == '2') {
                $data = Cuti::where('id', $request->id)->first();
                $data->status_cuti  = 'NOT APPROVE';
                $data->ttd_atasan2  = $uniqid;
                $data->catatan2      = $request->catatan;
                $data->waktu_approve = date('Y-m-d H:i:s');
                $data->update();
            } else if ($request->status_cuti == '1') {
                $data = Cuti::where('id', $request->id)->first();
                $data->status_cuti  = 'NOT APPROVE';
                $data->catatan      = $request->catatan;
                $data->ttd_atasan  = $uniqid;
                $data->waktu_approve = date('Y-m-d H:i:s');
                $data->update();
            }
            $alert = $request->session()->flash('approvecuti_not_approve');
            return response()->json($alert);
        } else {
            if ($request->signature != null) {
                $folderPath     = public_path('signature/cuti/');
                $image_parts    = explode(";base64,", $request->signature);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type     = $image_type_aux[1];
                $image_base64   = base64_decode($image_parts[1]);
                $uniqid         = date('y-m-d') . '-' . uniqid();
                $file           = $folderPath . $uniqid . '.' . $image_type;
                file_put_contents($file, $image_base64);
            } else {
                $uniqid = NULL;
            }
            if ($request->status_cuti == '2') {
                $data = Cuti::where('id', $request->id)->first();

                $data->status_cuti  = 3;
                $data->ttd_atasan2  = $uniqid;
                $data->catatan2      = $request->catatan;
                $data->waktu_approve2 = date('Y-m-d H:i:s');
                $data->update();

                $begin = new \DateTime($data->tanggal_mulai);
                $end = new \DateTime($data->tanggal_selesai);
                $end = $end->modify('+1 day');

                $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
                $daterange = new \DatePeriod($begin, $interval, $end);


                foreach ($daterange as $date) {
                    $tanggal = $date->format("Y-m-d");
                    $update_mapping_cuti = MappingShift::where('user_id', $data->user_id)->where('tanggal_masuk', $tanggal)->first();
                    $update_mapping_cuti->status_absen = 'TIDAK HADIR KERJA';
                    $update_mapping_cuti->keterangan_cuti = 'TRUE';
                    $update_mapping_cuti->keterangan_absensi = 'CUTI';
                    $update_mapping_cuti->keterangan_absensi_pulang = 'CUTI';
                    $update_mapping_cuti->kelengkapan_absensi = 'CUTI APPROVED';
                    $update_mapping_cuti->cuti_id = $data->id;
                    $update_mapping_cuti->update();
                }
                $user_cuti = Karyawan::where('id', $data->user_id)->first();
                $user_cuti->kuota_cuti_tahunan = ($user_cuti->kuota_cuti_tahunan) - ($data->total_cuti);
                $user_cuti->update();
            } else if ($request->status_cuti == '1') {
                $data = Cuti::where('id', $request->id)->first();
                if ($user_karyawan->id == $data->id_user_atasan && $user_karyawan->id == $data->id_user_atasan2) {

                    $data->status_cuti  = 3;
                    $data->ttd_atasan  = $uniqid;
                    $data->ttd_atasan2  = $uniqid;
                    $data->catatan      = $request->catatan;
                    $data->catatan2      = $request->catatan;
                    $data->waktu_approve = date('Y-m-d H:i:s');
                    $data->waktu_approve2 = date('Y-m-d H:i:s');
                    $data->update();

                    $user_cuti = Karyawan::where('id', $data->user_id)->first();
                    $user_cuti->kuota_cuti_tahunan = ($user_cuti->kuota_cuti_tahunan) - ($data->total_cuti);
                    $user_cuti->update();

                    $begin = new \DateTime($data->tanggal_mulai);
                    $end = new \DateTime($data->tanggal_selesai);
                    $end = $end->modify('+1 day');

                    $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
                    $daterange = new \DatePeriod($begin, $interval, $end);


                    foreach ($daterange as $date) {
                        $tanggal = $date->format("Y-m-d");
                        $update_mapping_cuti = MappingShift::where('user_id', $data->user_id)->where('tanggal_masuk', $tanggal)->first();
                        $update_mapping_cuti->status_absen = 'TIDAK HADIR KERJA';
                        $update_mapping_cuti->keterangan_cuti = 'TRUE';
                        $update_mapping_cuti->keterangan_absensi = 'CUTI';
                        $update_mapping_cuti->keterangan_absensi_pulang = 'CUTI';
                        $update_mapping_cuti->kelengkapan_absensi = 'CUTI APPROVED';
                        $update_mapping_cuti->cuti_id = $data->id;
                        $update_mapping_cuti->update();
                    }
                } else {

                    $data->status_cuti  = 2;
                    $data->ttd_atasan  = $uniqid;
                    $data->catatan      = $request->catatan;
                    $data->waktu_approve = date('Y-m-d H:i:s');
                    $data->update();
                }
            }
            $alert = $request->session()->flash('approvecuti_success');
            return response()->json($alert);
        }
    }
    public function proses_hapus_cuti(Request $request)
    {
        // dd($request->all());
        $query = Cuti::where('id', $request->id_cuti_hapus)->first();
        $id_kuota_cuti = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_kuota_cuti->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'edit cuti',
            'id_table' => $request->id_cuti_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'cutis',
            'activity' => 'add',
            'description' => 'Menghapus Cuti ' .  $query->tanggal_mulai . ' sampai ' . $query->tanggal_selesai,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_cuti_gagal', 'Data Cuti Tidak Ditemukan');
        } elseif ($query->status_cuti != 0) {
            $request->session()->flash('hapus_cuti_gagal', 'Cuti sudah di-approve');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            CutiDetail::where('id_cuti', $request->id_cuti_hapus)->delete();
            QuickPanel::where('content_id', $request->id_cuti_hapus)->delete();

            $query->delete();
            $request->session()->flash('hapus_cuti_sukses', 'Data Cuti Berhasil Dihapus');
        }
        return redirect('cuti/' . Auth::user()->menu_active);
    }
    public function cetak_form_cuti($id)
    {
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $cuti = Cuti::with('KategoriCuti', 'KaryawanPengganti', 'Atasan1', 'Atasan2')->where('id', $id)->first();
        $jumlah_hari = CutiDetail::where('id_cuti', $cuti->id)->count();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $user_karyawan->id)->where('is_active', 1)->where('main', 1)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        // dd($holding);
        // $path_holding = public_path('storage/holding_image/' . $holding->holding_image);
        // $path_ttd_pemohon = public_path('storage/signatures/' . $cuti->ttd_user);
        // $path_ttd_atasan = public_path('storage/signatures/' . $cuti->ttd_atasan);
        // $path_ttd_atasan2 = public_path('storage/signatures/' . $cuti->ttd_atasan2);
        // dd($path_holding);

        // $type_holding = pathinfo($path_holding, PATHINFO_EXTENSION);
        // $type_ttd_pemohon = pathinfo($path_ttd_pemohon, PATHINFO_EXTENSION);
        // $type_ttd_atasan = pathinfo($path_ttd_atasan, PATHINFO_EXTENSION);
        // $type_atasan2 = pathinfo($path_ttd_atasan2, PATHINFO_EXTENSION);

        // $data_img_holding = file_get_contents($path_holding);
        // dd($data_img_holding);
        // $data_img_ttd_pemohon = file_get_contents($path_ttd_pemohon);
        // $data_img_ttd_atasan = file_get_contents($path_ttd_atasan);
        // $data_img_ttd_atasan2 = file_get_contents($path_ttd_atasan2);

        // $data['logo'] = 'data:image/' . $type_holding . ';base64,' . base64_encode($data_img_holding);
        // $data['ttd_pemohon'] = 'data:image/' . $type_ttd_pemohon . ';base64,' . base64_encode($data_img_ttd_pemohon);
        // $data['ttd_atasan'] = 'data:image/' . $type_ttd_atasan . ';base64,' . base64_encode($data_img_ttd_atasan);
        // $data['ttd_atasan2'] = 'data:image/' . $type_atasan2 . ';base64,' . base64_encode($data_img_ttd_atasan2);

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
        // dd(file_exists(public_path('storage/holding_image/' . $holding->holding_image)));
        // return view('users/cuti/form_cuti', $data);
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
    public function get_history_cuti(Request $request)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $start = $request->awal;
            $end = $request->akhir;
            $data = Cuti::whereBetween('tanggal_mulai', array($start, $end))
                ->where('karyawan_id', $user_karyawan->id)
                ->whereNotNull('status_cuti')
                ->orderBy('tanggal_mulai', 'DESC')->get();

            // dd($data);
            return response()->json([
                'code' => 200,
                'history' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function history_detail($id)
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
        // dd($all_menu_bar);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.cuti.history_cutiDetail', [
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'user_karyawan' => $user_karyawan,
            'id' => $id
        ]);
    }
}
