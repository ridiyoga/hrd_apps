<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalGroup;
use App\Models\ApprovalUser;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\MappingShiftHead;
use App\Models\MappingShiftTukar;
use App\Models\Menu;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\StrukturModel;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class MappingShiftController extends Controller
{
    function mapping_shift_index(Request $request, $holding)
    {
        // dd('test');
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
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
        // dd($menus);
        $holding = Holding::where('holding_code', $holding)->first();
        // dd($holding);
        if ($holding == NULL) {
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        date_default_timezone_set('Asia/Jakarta');
        // $bulan = date('m');
        // $tahun = date('Y');
        // $hari_per_bulan = cal_days_in_month(CAL_GREGORIAN,$bulan,$tahun);
        $tanggal_mulai = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $title = "Rekap Data Absensi Tanggal " . date('Y-m-01') . " s/d " . date('Y-m-d');

        $user = Karyawan::with('Cuti')->with('Izin')->where('status_aktif', 'AKTIF')->get();
        // dd($user->Cuti->nama_cuti);

        if ($request["mulai"] && $request["akhir"]) {
            $tanggal_mulai = $request["mulai"];
            $tanggal_akhir = $request["akhir"];
            $title = "Rekap Data Absensi Tanggal " . $tanggal_mulai . " s/d " . $tanggal_akhir;
        }
        $departemen = Departemen::where('holding', $holding->id)->orderBy('nama_departemen')->get();
        $shift = Shift::orderBy('nama_shift')->get();
        // dd($user);

        return view('admin.karyawan.karyawan_mappingshift', [
            'tanggal_mulai' => $tanggal_mulai,
            'departemen' => $departemen,
            'holding' => $holding,
            'menus' => $menus,
            'tanggal_akhir' => $tanggal_akhir,
            'user' => $user,
            'shift' => $shift
        ]);
    }
    function get_karyawan_mapping(Request $request, $holding)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        if ($holding == null) {
            return response()->json([
                'code' => 500,
                'status' => 'Holding tidak ditemukan',
            ]);
        }
        try {
            $query = Karyawan::where('shift', 'SHIFT')
                ->where('status_aktif', 'AKTIF')
                ->where('kontrak_kerja', $holding->id)
                ->orderBy('name', 'ASC');


            if (!empty($request->departemen_filter)) {
                $query->whereIn('dept_id', (array)$request->departemen_filter);
            }
            if (!empty($request->divisi_filter)) {
                $query->whereIn('divisi_id', (array)$request->divisi_filter);
            }
            if (!empty($request->bagian_filter)) {
                $query->whereIn('bagian_id', (array)$request->bagian_filter);
            }
            if (!empty($request->jabatan_filter)) {
                $query->whereIn('jabatan_id', (array)$request->jabatan_filter);
            }
            $data = $query->select('id', 'nomor_identitas_karyawan', 'name')->orderBy('name', 'ASC')->get();
            // dd($data);
            return response()->json([
                'code' => 200,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'Gagal mengambil data karyawan',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    function tambah_mapping($id, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        try {
            // dd($menu_active);
            $shift = Shift::whereNotIn('nama_shift', ['Non Shift'])->orderBy('nama_shift')->get();
            $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
            if (count($get_role) == 0) {
                $roleId = null;
            } else {
                $roleId = $get_role;
            }
            if ($roleId == null) {
                $all_menus = collect();
            } else {
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
            $start_date = $request->start_date
                ? Carbon::parse($request->start_date)->format('Y-m-d')
                : null;
            $end_date = $start_date
                ? Carbon::parse($start_date)->addDays(6)->format('Y-m-d')
                : null;
            // dd($start_date, $end_date);
            // dd($end_date);
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $mapping_shift = MappingShift::with('Karyawan', 'KaryawanInput', 'KaryawanApproved')
                ->whereNotNull('legal_number_mapping')
                ->whereBetween('tanggal_masuk', [$start_date, $end_date])
                ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
                ->whereIn('status_approved', [0, 1])
                ->whereNotNull('is_active')
                ->first();
            $menu_active = User::where('id', Auth::user()->id)->first();
            $menu_active->menu_active = $id;
            $menu_active->save();
            return view('users.mapping_shift.tambah_mapping', [
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'shift' => $shift,
                'start_date' => $start_date,
                'user_karyawan' => $user_karyawan,
                'mapping_shift' => $mapping_shift,
                'end_date' => $end_date,
                'menu_id' => $id
            ]);
        } catch (\Exception $e) {
            session()->flash('tambahmappingerror', $e->getMessage());
            return redirect()->back()->with([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    function tambah_mapping_edit($id_head, $id, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        try {
            // dd($menu_active);
            $shift = Shift::whereNotIn('nama_shift', ['Non Shift'])->orderBy('nama_shift')->get();
            $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
            if (count($get_role) == 0) {
                $roleId = null;
            } else {
                $roleId = $get_role;
            }
            if ($roleId == null) {
                $all_menus = collect();
            } else {
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
            $start_date = $request->start_date
                ? Carbon::parse($request->start_date)->format('Y-m-d')
                : null;
            $end_date = $start_date
                ? Carbon::parse($start_date)->addDays(6)->format('Y-m-d')
                : null;
            // dd($start_date, $end_date);
            // dd($end_date);
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $mapping_shift = MappingShift::with('Karyawan', 'KaryawanInput', 'KaryawanApproved')
                ->whereNotNull('legal_number_mapping')
                ->whereBetween('tanggal_masuk', [$start_date, $end_date])
                ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
                ->whereIn('status_approved', [0, 1])
                ->whereNotNull('is_active')
                ->first();
            $menu_active = User::where('id', Auth::user()->id)->first();
            $menu_active->menu_active = $id;
            $menu_active->save();

            return view('users.mapping_shift.tambah_mapping_edit', [
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'shift' => $shift,
                'start_date' => $start_date,
                'user_karyawan' => $user_karyawan,
                'mapping_shift' => $mapping_shift,
                'end_date' => $end_date,
                'menu_id' => $id,
                'id_head' => $id_head,
            ]);
        } catch (\Exception $e) {
            session()->flash('tambahmappingerror', $e->getMessage());
            return redirect()->back()->with([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function getApproval(Request $request, $id)
    {
        try {
            // dd($coba);
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $access = ApprovalUser::with([
                'Approval',
                'Approval.ApprovalGroup'
            ])
                ->whereHas(
                    'Approval.ApprovalGroup',
                    fn($q) =>
                    $q->where('menu_id', 'LIKE', '%' . $id . '%')
                )
                ->where('karyawan_id', $user_karyawan->id)
                ->first();
            // dd($access);
            if ($access == null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Access not found, Hubungi Admin'
                ]);
            }
            $currentApprovalId = $access->Approval->parent_approval_id;
            $approvalUser = null;

            while ($currentApprovalId) {
                $approvalUser = ApprovalUser::with(['User', 'Karyawan', 'Approval'])
                    ->whereHas('Approval', function ($q) use ($currentApprovalId) {
                        $q->where('id_approval', $currentApprovalId);
                    })
                    ->whereHas('Karyawan') // 🔑 hanya yang punya karyawan
                    ->first();

                // kalau ketemu → STOP
                if ($approvalUser) {
                    break;
                }

                // kalau tidak ketemu, naik ke parent berikutnya
                $parentApproval = Approval::where('id_approval', $currentApprovalId)->first();

                $currentApprovalId = $parentApproval?->parent_approval_id;
            }
            $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $user_karyawan->id)->get();
            // dd($karyawan_jabatan);
            // ambil array id
            $departemenIds = $karyawan_jabatan->pluck('departemen_id')->toArray();
            $divisiIds     = $karyawan_jabatan->pluck('divisi_id')->toArray();
            $holdingIds    = $karyawan_jabatan->pluck('holding_id')->toArray();
            $jabatanIds    = $karyawan_jabatan->pluck('jabatan_id')->toArray();
            $struktur = StrukturModel::with('UserKaryawan', 'ChildrenRecursive')->whereIn('jabatan', $jabatanIds)->whereIn('holding', $holdingIds)->get();
            if ($struktur->isEmpty()) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Struktur not found, Hubungi Admin'
                ]);
            }
            // dd($jabatanIds, $struktur, $holdingIds);
            $bawahanKaryawanIds = collectBawahan($struktur);
            $user_shift = Karyawan::with(['KaryawanJabatan' => function ($q) {
                $q->with('Divisi');
                $q->with('Jabatan');
                $q->with('Bagian');
                $q->with('Departemen');
                $q->where('main', 1);
                $q->where('is_active', 1);
            }])->whereIn('id', $bawahanKaryawanIds)
                ->where('status_aktif', 'AKTIF')
                ->where('shift', 'SHIFT')
                ->orderBy('name', 'asc')
                ->get();


            $master_shift = Shift::whereNot('nama_shift', 'NON SHIFT')->orderBy('nama_shift')->get();
            $mapping_shift = MappingShift::where('input_by', $user_karyawan->id)->where('tanggal_masuk', $request->date)->whereNotNull('is_active')->get();
            // dd($mapping_shift, $request->date);
            // dd($access, $access->Approval->parent_approval_id, $approvaluser);
            return response()->json([
                'code' => 200,
                'data' => $approvalUser,
                'master_shift' => $master_shift,
                'mapping_shift' => $mapping_shift,
                'user_shift' => $user_shift
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function getDate(Request $request, $id)
    {
        // dd('lll');
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $today = Carbon::today();
            $start_date = $request->start_date
                ? Carbon::parse($request->start_date)->lt($today)
                ? $today->format('Y-m-d')
                : Carbon::parse($request->start_date)->format('Y-m-d')
                : $today->format('Y-m-d');
            // $start_date = $request->start_date
            //     ? Carbon::parse($request->start_date)->format('Y-m-d')
            //     : null;
            // dd($start_date);
            $end_date = $request->end_date
                ? Carbon::parse($request->end_date)->format('Y-m-d')
                : null;

            $period = CarbonPeriod::create($start_date, $end_date);

            foreach ($period as $date) {
                $mapping_shift = MappingShift::where('tanggal_masuk', $date->format('Y-m-d'))
                    ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
                    ->whereNotNull('is_active')
                    ->get();
                $data_columns[] = [
                    'header_day' => Carbon::parse($date)->isoFormat('ddd'),
                    'header_date' => Carbon::parse($date)->isoFormat('DD'),
                    'date' => $date->format('Y-m-d'),
                    'name' => 'tanggal_' . $date->format('dmY'),
                    'mapping_shift' => $mapping_shift,
                ];
            }
            $count_period = count($period);
            $access = ApprovalUser::with([
                'Approval',
                'Approval.ApprovalGroup'
            ])
                ->whereHas(
                    'Approval.ApprovalGroup',
                    fn($q) =>
                    $q->where('menu_id', 'LIKE', '%' . $id . '%')
                )
                ->where('approval_user_id', Auth::user()->id)
                ->first();

            // dd($access, $id);
            if ($access == null) {
                // return response()->json([
                //     'code' => 404,
                //     'message' => 'Access not found, Hubungi Admin'
                // ]);
                $approvalUser = null;
            } else {
                $currentApprovalId = $access->Approval->parent_approval_id;
                $approvalUser = null;
                while ($currentApprovalId) {

                    $approvalUser = ApprovalUser::with(['User', 'Karyawan', 'Approval'])
                        ->whereHas('Approval', function ($q) use ($currentApprovalId) {
                            $q->where('id_approval', $currentApprovalId);
                        })
                        ->whereHas('Karyawan') // 🔑 hanya yang punya karyawan
                        ->first();

                    // kalau ketemu → STOP
                    if ($approvalUser) {
                        break;
                    }


                    // kalau tidak ketemu, naik ke parent berikutnya
                    $parentApproval = Approval::where('id_approval', $currentApprovalId)->first();

                    $currentApprovalId = $parentApproval?->parent_approval_id;
                }
            }
            // dd($header, $data_columns, $count_period);
            // dd($access, $access->Approval->parent_approval_id, $approvaluser);
            return response()->json([
                'code' => 200,
                'data' => $data_columns,
                'hrd' => $approvalUser,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function index(Request $request, $id)
    {
        // dd($id);
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $shift = Shift::whereNotIn('nama_shift', ['Non Shift'])->orderBy('nama_shift')->get();


        $weekStart = Carbon::now()->startOfWeek();
        $days = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));


        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menus = collect();
            $all_menu_bar = collect();
        } else {

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
        // $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $user_karyawan->id)->whereNull('approval2')->count();
        // dd($badge_pegawai_ubah);
        $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $user_karyawan->id)->orderBy('created_at', 'desc')->first();
        // dd($get_approval);
        if ($get_approval?->approval1 == 1) {
            $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $user_karyawan->id)->count();
        } elseif ($get_approval?->approval1 == 2) {
            $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $user_karyawan->id)->count();
        } else {
            $badge_pegawai_ubah = 0;
            // dd($badge_pegawai_ubah);
        }
        $menu_active = User::where('id', Auth::user()->id)->first();
        $menu_active->menu_active = $id;
        $menu_active->save();
        return view('users.mapping_shift.index', [
            'shift' => $shift,
            'days' => $days,
            'menu_active' => $id,
            'all_menus' => $all_menus,
            'user_karyawan' => $user_karyawan,
            'all_menu_bar' => $all_menu_bar,
            'badge_pegawai_ubah' => $badge_pegawai_ubah
        ]);
    }
    public function getShiftData(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16

        $mapping_head = MappingShiftHead::where('start_date', $start)
            ->where('end_date', $end)
            ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
            ->first();

        // dd($mapping_head);

        $data = MappingShift::with('KaryawanApproved', 'KaryawanInput', 'User', 'Shift')
            ->whereBetween('tanggal_masuk', array($start, $end))
            ->whereNotNull('status_approved')
            ->whereNotNull('is_active')
            ->where('id_head', $mapping_head ? $mapping_head->id : '')
            ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
            ->orderBy('tanggal_masuk', 'asc')
            ->get();


        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $item) {
            $grouped[$item->tanggal_masuk][] = [
                'id_mapping' => $item->id,
                'shift_id' => $item->shift_id,
                'name' => $item->nama_karyawan,
                'shift' => $item->nama_shift,
                'pengajuan_signature' => $item->pengajuan_signature,
                'approved_signature' => $item->approved_signature,
                'karyawan_input' => $item->KaryawanInput == null ? [] : $item->KaryawanInput->name,
                'karyawan_approved' => $item->KaryawanApproved == null ? [] : $item->KaryawanApproved->name,
                'tanggal_input' => Carbon::parse($item->created_at)->format('d-m-Y'),
                'tanggal_approved' => Carbon::parse($item->approved_at)->format('d-m-Y'),
                'status_approved' => $item->status_approved,
                'is_active' => $item->is_active,
                'jam_shift' => '(' . $item->Shift->jam_masuk . ' - ' . $item->Shift->jam_keluar . ')', // ' . $item->Shift->jam_masuk . ' - ' . $item->Shift->jam_keluar
                'id_head' => $item->is_head,
            ];
        }

        // dd($grouped, $start, $end, $data);
        return response()->json([
            'mapping_head' => $mapping_head ?? null,
            'data' => $grouped
        ]);
    }
    public function getKaryawanMappingShift(Request $request)
    {
        $karyawan = Karyawan::select('id', 'name')->with('MappingShift')->whereNotNull('is_active')->whereIn('id', $request->user)->orderBy('name', 'ASC')->get();
        // dd($karyawan);
        if ($karyawan) {
            return response()->json([
                'code' => 200,
                'data' => $karyawan
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
    }
    public function addMKaryawan(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        try {
            if (empty($request->selected_users)) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Karyawan tidak boleh kosong'
                ]);
            }
            $update_allow = MappingShiftHead::whereNull('approved')->where('input_by', $user_karyawan->id)
                ->exists();
            // dd($update_allow);
            if (!$update_allow) {
                $mapping_shift = MappingShift::where('tanggal_masuk', $request->tanggal)
                    // ->whereIn('karyawan_id', $request->selected_users)
                    ->whereNull('status_approved')
                    ->whereNotNull('is_active')
                    ->where('input_by', $user_karyawan->id)
                    ->delete();
                // dd($mapping_shift, $request->selected_users, $request->tanggal);
                foreach ($request->selected_users as $value) {
                    $exists = Karyawan::where('id', $value)->first();
                    if (!$exists) {
                        return response()->json([
                            'code' => 404,
                            'message' => 'Karyawan dengan ID ' . $value . ' tidak ditemukan'
                        ]);
                    } else {
                        $shiftId = $request->shift[$value] ?? null;
                        if (!$shiftId) {
                            return response()->json([
                                'code' => 404,
                                'message' => 'Shift untuk karyawan dengan Nama ' . $exists->name . ' tidak ditemukan'
                            ]);
                        };
                        $shiftExists = Shift::where('id', $shiftId)->first();
                        if (!$shiftExists) {
                            return response()->json([
                                'code' => 404,
                                'message' => 'Shift dengan ID ' . $shiftId . ' tidak ditemukan'
                            ]);
                        } else {
                            if ($shiftExists->tgl_pulang_besok == 1) {
                                $tanggal_pulang = Carbon::parse($request->tanggal)->addDay(1)->format('Y-m-d');
                            } else {
                                $tanggal_pulang = Carbon::parse($request->tanggal)->format('Y-m-d');
                            }
                            $get_mapping_shift = MappingShift::where('karyawan_id', $value)
                                ->where('tanggal_masuk', $request->tanggal)
                                ->where('status_approved', 0)
                                ->where('input_by', $user_karyawan->id)
                                ->whereNotNull('is_active')
                                ->first();
                            // dd($get_mapping);
                            if ($get_mapping_shift) {
                                $get_mapping_shift->delete();
                                // insert ulang
                                $create = new MappingShift();
                                $create->karyawan_id = $value;
                                $create->nik_karyawan = $exists->nomor_identitas_karyawan;
                                $create->shift_id = $shiftId;
                                $create->nama_karyawan = $exists->name;
                                $create->nama_shift = $shiftExists->nama_shift;
                                $create->input_by = $user_karyawan->id;
                                $create->tanggal_masuk = $request->tanggal;
                                $create->tanggal_pulang = $tanggal_pulang;
                                $create->created_at = Carbon::now();
                                $create->status_approved = 0;
                                $create->is_active = 1;
                                $create->legal_number_mapping = $get_mapping_shift->legal_number_mapping;
                                $create->id_head = $get_mapping_shift->id_head;
                                $create->mapping_group = $get_mapping_shift->mapping_group;
                                $create->approved_by = $get_mapping_shift->approved_by;
                                $create->pengajuan_signature = $get_mapping_shift->pengajuan_signature;
                                $create->save();
                            } else {

                                $create = new MappingShift();
                                $create->karyawan_id = $value;
                                $create->nik_karyawan = $exists->nomor_identitas_karyawan;
                                $create->shift_id = $shiftId;
                                $create->nama_karyawan = $exists->name;
                                $create->nama_shift = $shiftExists->nama_shift;
                                $create->input_by = $user_karyawan->id;
                                $create->tanggal_masuk = $request->tanggal;
                                $create->tanggal_pulang = $tanggal_pulang;
                                $create->is_active = 1;
                                $create->created_at = Carbon::now();
                                $create->save();
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => 'Gagal menambahkan mapping shift',
                    'message' => 'Tunggu Pengubahan anda di-approve HRD',
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Berhasil menambahkan karyawan ke mapping shift',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function addMKaryawan_edit($id, Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        try {
            if (empty($request->selected_users)) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Karyawan tidak boleh kosong'
                ]);
            }
            $update_allow = MappingShiftHead::whereNull('approved')->where('input_by', $user_karyawan->id)
                ->exists();
            // dd($update_allow);
            if (!$update_allow) {
                $mapping_shift = MappingShift::where('tanggal_masuk', $request->tanggal)
                    // ->whereIn('karyawan_id', $request->selected_users)
                    // ->where('status_approved', 0)
                    ->whereNotNull('is_active')
                    ->where('input_by', $user_karyawan->id)
                    ->delete();
                // dd($mapping_shift, $request->selected_users, $request->tanggal);
                foreach ($request->selected_users as $value) {
                    $exists = Karyawan::where('id', $value)->first();
                    if (!$exists) {
                        return response()->json([
                            'code' => 404,
                            'message' => 'Karyawan dengan ID ' . $value . ' tidak ditemukan'
                        ]);
                    } else {
                        $shiftId = $request->shift[$value] ?? null;
                        if (!$shiftId) {
                            return response()->json([
                                'code' => 404,
                                'message' => 'Shift untuk karyawan dengan Nama ' . $exists->name . ' tidak ditemukan'
                            ]);
                        };
                        $shiftExists = Shift::where('id', $shiftId)->first();
                        if (!$shiftExists) {
                            return response()->json([
                                'code' => 404,
                                'message' => 'Shift dengan ID ' . $shiftId . ' tidak ditemukan'
                            ]);
                        } else {
                            if ($shiftExists->tgl_pulang_besok == 1) {
                                $tanggal_pulang = Carbon::parse($request->tanggal)->addDay(1)->format('Y-m-d');
                            } else {
                                $tanggal_pulang = Carbon::parse($request->tanggal)->format('Y-m-d');
                            }
                            $get_mapping_shift = MappingShift::where('karyawan_id', $value)
                                ->where('tanggal_masuk', $request->tanggal)
                                ->where('status_approved', 0)
                                ->where('id_head', $id)
                                ->where('input_by', $user_karyawan->id)
                                ->whereNotNull('is_active')
                                ->first();
                            // dd($get_mapping);
                            if ($get_mapping_shift) {
                                $get_mapping_shift->delete();
                                // insert ulang
                                $create = new MappingShift();
                                $create->karyawan_id = $value;
                                $create->nik_karyawan = $exists->nomor_identitas_karyawan;
                                $create->shift_id = $shiftId;
                                $create->nama_karyawan = $exists->name;
                                $create->nama_shift = $shiftExists->nama_shift;
                                $create->input_by = $user_karyawan->id;
                                $create->tanggal_masuk = $request->tanggal;
                                $create->tanggal_pulang = $tanggal_pulang;
                                $create->created_at = Carbon::now();
                                $create->status_approved = 0;
                                $create->is_active = 1;
                                $create->legal_number_mapping = $get_mapping_shift->legal_number_mapping;
                                $create->id_head = $id;
                                $create->mapping_group = $get_mapping_shift->mapping_group;
                                $create->approved_by = $get_mapping_shift->approved_by;
                                $create->pengajuan_signature = $get_mapping_shift->pengajuan_signature;
                                $create->save();
                            } else {

                                $create = new MappingShift();
                                $create->karyawan_id = $value;
                                $create->nik_karyawan = $exists->nomor_identitas_karyawan;
                                $create->shift_id = $shiftId;
                                $create->nama_karyawan = $exists->name;
                                $create->nama_shift = $shiftExists->nama_shift;
                                $create->input_by = $user_karyawan->id;
                                $create->tanggal_masuk = $request->tanggal;
                                $create->tanggal_pulang = $tanggal_pulang;
                                $create->id_head = $id;
                                $create->is_active = 1;
                                $create->created_at = Carbon::now();
                                $create->save();
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => 'Gagal menambahkan mapping shift',
                    'message' => 'Tunggu Pengubahan anda di-approve HRD',
                ]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Berhasil menambahkan karyawan ke mapping shift',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function addMappingShift(Request $request)
    {
        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'mapping_title' => 'required',
            // 'approve_hrd' => 'required',
            'signature' => 'required',

        ], [
            'mapping_title.required' => 'Judul Mapping Shift wajib diisi',
            // 'approve_hrd.required' => 'Approve HRD wajib diisi',
            'signature.required' => 'Tanda tangan wajib diisi',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'code' => 500,
                'title' => 'Gagal',
                'message' => $validation->errors()
            ]);
        }

        // dd($start, $end, $period);
        // $no_urut = MappingShift::whereNotNull('legal_number_mapping')
        //     ->whereNotNull('is_active')
        //     ->whereBetween('tanggal_masuk', [
        //         $request->tanggal_mulai,
        //         $request->tanggal_selesai
        //     ])
        //     ->distinct('legal_number_mapping')
        //     ->count('legal_number_mapping') + 1;
        $now = Carbon::now()->format('Ymd');
        $asoy = MappingShift::select('legal_number_mapping', 'created_at')->orderbyDesc('created_at')->whereNotNull('legal_number_mapping')
            ->first();
        // dd($asoy);
        $now1 = Carbon::now()->format('Y-m-d');
        if ($asoy == null) {
            DB::statement("ALTER SEQUENCE legal_number_seq RESTART;");
        } else {
            $created_at = Carbon::parse($asoy->created_at)->format('Y-m-d');
            if ($created_at < $now1) {
                DB::statement("ALTER SEQUENCE legal_number_seq RESTART;");
            }
        }
        $no_urut = DB::selectOne("SELECT nextval('legal_number_seq') as num")->num;




        // if (strlen($no_urut) == 1) {
        //     $no = '00' . $no_urut;
        // } else if (strlen($no_urut) == 2) {
        //     $no = '0' . $no_urut;
        // } else {
        //     $no = $no_urut;
        // }


        // Ambil angka terakhir (misal: 001 → 1)
        // $lastNumber = $asoy && $asoy->legal_number_mapping
        //     ? (int) substr(strrchr($asoy->legal_number_mapping, '/'), 1)
        //     : 0;

        // Increment
        // $nextNumber = $lastNumber + 1;

        // Format jadi 3 digit (001, 002, dst)
        // $no = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Tanggal sekarang

        // Gabungkan
        // $legal = 'SHIFT/' . $now . '/' . $no;
        $legal = 'SHIFT/' . $now . '/' . str_pad($no_urut, 3, '0', STR_PAD_LEFT);
        // dd($no_urut);
        // dd($legal);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        try {
            $update_allow = MappingShiftHead::whereNull('approved')->where('input_by', $user_karyawan->id)
                ->exists();
            // dd($update_allow);
            if (!$update_allow) {
                if ($request->signature == 'edit') {
                    // dd('asoy');
                    $get_shift = MappingShift::whereNull('legal_number_mapping')
                        ->whereBetween('tanggal_masuk', [$request->tanggal_mulai, $request->tanggal_selesai])
                        ->whereNull('status_approved')
                        ->whereNotNull('is_active')
                        ->where('input_by', $user_karyawan->id)
                        ->update([
                            'legal_number_mapping' => $request->legal_number_update,
                            'mapping_group' => $request->mapping_title,
                            'approved_by' => $request->approve_hrd,
                            'status_approved' => 0,
                            'pengajuan_signature' => $request->pengajuan_signature,
                            'updated_at' => Carbon::now(),
                        ]);
                } else {
                    // ambil base64
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
                    $kode = Uuid::uuid4();
                    // dd('geboy');

                    $start = Carbon::parse($request->tanggal_mulai);
                    $end = Carbon::parse($request->tanggal_selesai);

                    // Total hari termasuk tanggal awal dan akhir
                    $totalHari = $start->diffInDays($end) + 1;

                    $totalData = MappingShift::whereNull('legal_number_mapping')
                        ->whereBetween('tanggal_masuk', [
                            $request->tanggal_mulai,
                            $request->tanggal_selesai
                        ])
                        ->where('input_by', $user_karyawan->id)
                        ->distinct('tanggal_masuk')
                        ->count();

                    // dd($totalData, $totalHari);

                    if ($totalData != $totalHari) {
                        return response()->json([
                            'code' => 401,
                            'message' => 'Masih ada tanggal yang belum memiliki data shift.'
                        ]);
                    } else {
                        MappingShiftHead::create([
                            'id' => $kode,
                            'judul' => $request->mapping_title,
                            'legal_number_mapping' => $legal,
                            'input_by' => $user_karyawan->id,
                            'input_by_name' => $user_karyawan->name,
                            'input_by_signature' => $fileName,
                            'start_date' => $request->tanggal_mulai,
                            'end_date' => $request->tanggal_selesai,
                            // 'approved',
                            // 'approved_by',
                            // 'approved_at',
                            // 'approved_signature',
                            // 'approved_by_name',
                            'created_at' => now(),
                            // 'updated_at',
                        ]);
                        MappingShift::whereNull('legal_number_mapping')
                            ->whereBetween('tanggal_masuk', [$request->tanggal_mulai, $request->tanggal_selesai])
                            ->whereNull('status_approved')
                            ->whereNotNull('is_active')
                            ->where('input_by', $user_karyawan->id)
                            ->update([
                                'legal_number_mapping' => $legal,
                                'mapping_group' => $request->mapping_title,
                                'approved_by' => $request->approve_hrd,
                                'status_approved' => 0,
                                'pengajuan_signature' => $fileName,
                                'id_head' => $kode,
                                'updated_at' => Carbon::now(),
                            ]);
                    }
                }
                $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->legal_number_update ?? '-',
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'mapping_shifts',
                    'activity' => 'add',
                    'description' => 'menambah mapping shift: ' .  $request->mapping_title,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
                return response()->json([
                    'code' => 200,
                    'message' => 'Berhasil menambahkan mapping shift',
                ]);
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => 'Gagal menambahkan mapping shift',
                    'message' => 'Tunggu Pengubahan anda di-approve HRD',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'Gagal menambahkan mapping shift',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function update_mapping_shift(Request $request)
    {
        // dd($request->all());
        $validation = Validator::make($request->all(), [

            'shift' => 'required',
        ], [
            'shift.required' => 'Shift wajib diisi',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'code' => 500,
                'title' => 'Gagal',
                'message' => $validation->errors()
            ]);
        }
        try {
            $get_shift = Shift::where('id', $request->shift)->first();
            MappingShift::where('id', $request->id_mapping)->update([
                'shift_id' => $request->shift,
                'nama_shift' => $get_shift->nama_shift,
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil memperbarui mapping shift',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function delete_mapping_shift(Request $request)
    {
        try {
            $mapping = MappingShift::where('id', $request->id_mapping)->first();
            $mapping->delete();
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil menghapus mapping shift',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_divisi(Request $request)
    {
        // dd($request->all());
        $id_departemen    = $request->departemen_filter;

        $divisi      = Divisi::with('Departemen')->whereIn('dept_id', (array)$id_departemen)
            ->where('holding', $request->holding)
            ->orderBy('nama_divisi', 'ASC')
            ->get()
            ->sortBy(function ($item) {
                return $item->Departemen->nama_departemen . ' ' . $item->nama_divisi;
            });
        if ($divisi == NULL || $divisi == '' || count($divisi) == '0') {
            $select = '<option value="">Pilih Divisi...</option>';
        } else {

            $select_divisi[] = "<option value=''>Pilih Divisi...</option>";
            $currentDept = null;
            foreach ($divisi as $divisi) {
                if ($currentDept !== $divisi->Departemen->nama_departemen) {
                    // tutup optgroup sebelumnya
                    if ($currentDept !== null) {
                        $select_divisi1[] = "</optgroup>";
                    }

                    // buka optgroup baru
                    $currentDept = $divisi->Departemen->nama_departemen;
                    $select_divisi1[] = "<optgroup label='{$divisi->Departemen->nama_departemen}'>";
                }
                $select_divisi1[] = "<option value='$divisi->id'>$divisi->nama_divisi</option>";
            }
            // tutup optgroup terakhir
            if ($currentDept !== null) {
                $select_divisi1[] = "</optgroup>";
            }

            $select = array_merge($select_divisi, $select_divisi1);
        }
        return array(
            'select' => $select,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        );
    }
    public function get_bagian(Request $request)
    {
        $id_divisi    = $request->divisi_filter;

        $bagian      = Bagian::with('Divisi')->where('divisi_id', (array)$id_divisi)
            ->where('holding', $request->holding)
            ->orderBy('nama_bagian', 'ASC')->get()
            ->sortBy(function ($item) {
                return $item->Divisi->nama_divisi . ' ' . $item->nama_bagian;
            });
        if ($bagian == NULL || $bagian == '' || count($bagian) == '0') {
            $select = '<option value="">Pilih Bagian...</option>';
        } else {
            $select_bagian[] = "<option value=''>Pilih Bagian...</option>";
            $currentDept = null;
            foreach ($bagian as $bagian) {
                if ($currentDept !== $bagian->Divisi->nama_divisi) {
                    // tutup optgroup sebelumnya
                    if ($currentDept !== null) {
                        $select_bagian1[] = "</optgroup>";
                    }

                    // buka optgroup baru
                    $currentDept = $bagian->Divisi->nama_divisi;
                    $select_bagian1[] = "<optgroup label='{$bagian->Divisi->nama_divisi}'>";
                }
                $select_bagian1[] = "<option value='$bagian->id'>$bagian->nama_bagian</option>";
            }
            // tutup optgroup terakhir
            if ($currentDept !== null) {
                $select_bagian1[] = "</optgroup>";
            }

            $select = array_merge($select_bagian, $select_bagian1);
        }
        return array(
            'select' => $select,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        );
    }
    public function get_jabatan(Request $request)
    {
        $id_bagian    = $request->bagian_filter;
        $jabatan      = Jabatan::with('Bagian')->where('bagian_id', (array)$id_bagian)
            ->where('holding', $request->holding)
            ->orderBy('nama_jabatan', 'ASC')->get()
            ->sortBy(function ($item) {
                return $item->Bagian->nama_bagian . ' ' . $item->nama_jabatan;
            });
        if ($jabatan == NULL || $jabatan == '' || count($jabatan) == '0') {
            $select = '<option value="">Pilih Jabatan...</option>';
        } else {
            $select_jabatan[] = "<option value=''>Pilih Jabatan...</option>";
            $currentDept = null;
            foreach ($jabatan as $jabatan) {
                if ($currentDept !== $jabatan->Bagian->nama_bagian) {
                    // tutup optgroup sebelumnya
                    if ($currentDept !== null) {
                        $select_jabatan1[] = "</optgroup>";
                    }

                    // buka optgroup baru
                    $currentDept = $jabatan->Bagian->nama_bagian;
                    $select_jabatan1[] = "<optgroup label='{$jabatan->Bagian->nama_bagian}'>";
                }
                $select_jabatan1[] = "<option value='$jabatan->id'>$jabatan->nama_jabatan</option>";
            }
            // tutup optgroup terakhir
            if ($currentDept !== null) {
                $select_jabatan1[] = "</optgroup>";
            }

            $select = array_merge($select_jabatan, $select_jabatan1);
        }
        return array(
            'select' => $select,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        );
    }

    public function prosesAddMappingShift(Request $request)
    {
        // dd($request->all());
        date_default_timezone_set('Asia/Jakarta');

        if (empty($request["karyawan_id"])) {
            return response()->json([
                'code' => 500,
                'status' => 'warning',
                'message' => 'Karyawan belum dipilih'
            ]);
        } else {
            if ($request["shift_id"] == null) {
                return response()->json([
                    'code' => 500,
                    'status' => 'warning',
                    'message' => 'Shift belum dipilih'
                ]);
            }
        }


        $rules = [
            'karyawan_id' => 'required|array|min:1|max:255',
            'karyawan_id.*' => 'required|max:255',
            'shift_id' => 'required|array|min:1',
            'shift_id.*' => 'required',
            'max:255',
            'tanggal' => 'required|array|min:1',
            'tanggal.*' => 'required',
            'max:16',
        ];
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal',

        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);

        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            // dd($errors);
            Alert::error('Gagal', $errors);
            return response()->json([
                'code' => 500,
                'status' => 'warning',
                'message' => $errors
            ]);
        }

        // dd($request->all());
        try {
            $countInsert = 0;
            foreach ($request['karyawan_id'] as $karyawanId) {
                for ($i = 0; $i < count($request->tanggal); $i++) {
                    $tanggal = $request->tanggal[$i];
                    $shiftId = $request->shift_id[$i];
                    $shift = Shift::where('id', $shiftId)->first();
                    if ($shift) {
                        if ($shift->tgl_pulang_besok == 1) {
                            $tanggal_pulang =  Carbon::parse($tanggal)->addDay('1')->format('Y-m-d');
                        } else {
                            $tanggal_pulang = Carbon::parse($tanggal)->format('Y-m-d');
                        }
                        $namaShift = $shiftId;
                    } else {
                        $namaShift = NULL;
                    }
                    // Cek apakah sudah ada jadwal untuk tanggal tsb
                    $exists = MappingShift::where('tanggal_masuk', $tanggal)
                        ->whereNotNull('is_active')
                        ->where('karyawan_id', $karyawanId)
                        ->exists();
                    if (!$exists) {
                        $insert = MappingShift::insert([
                            'karyawan_id' => $karyawanId,
                            'tanggal_masuk' => $tanggal,
                            'tanggal_pulang' => $tanggal_pulang,
                            'shift_id' => $namaShift,
                            'nama_shift' => Shift::where('id', $namaShift)->value('nama_shift'),
                            'nik_karyawan' => Karyawan::where('id', $karyawanId)->value('nomor_identitas_karyawan'),
                            'nama_karyawan' => Karyawan::where('id', $karyawanId)->value('name'),
                        ]);
                        $countInsert++;
                    } else {
                        $update = MappingShift::where('tanggal_masuk', $tanggal)->where('karyawan_id', $karyawanId)->update([
                            'karyawan_id' => $karyawanId,
                            'tanggal_masuk' => $tanggal,
                            'tanggal_pulang' => $tanggal_pulang,
                            'shift_id' => $namaShift,
                            'nama_shift' => Shift::where('id', $namaShift)->value('nama_shift'),
                            'nik_karyawan' => Karyawan::where('id', $karyawanId)->value('nomor_identitas_karyawan'),
                            'nama_karyawan' => Karyawan::where('id', $karyawanId)->value('name'),
                        ]);
                        $countInsert++;
                    }
                }
            }
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'data berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'Gagal menyimpan jadwal',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function prosesTambahDetailShift(Request $request)
    {
        // dd($request->all());
        date_default_timezone_set('Asia/Jakarta');

        if ($request["tanggal_mulai"] == null) {
            $request["tanggal_mulai"] = $request["tanggal_akhir"];
        } else {
            $request["tanggal_mulai"] = $request["tanggal_mulai"];
        }

        if ($request["tanggal_akhir"] == null) {
            $request["tanggal_akhir"] = $request["tanggal_mulai"];
        } else {
            $request["tanggal_akhir"] = $request["tanggal_akhir"];
        }
        $rules = [
            'shift_id' => 'required',
            'max:255',
            'tanggal_mulai' => 'required',
            'max:16',
            'tanggal_akhir' => 'required',
            'max:16',
        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($request->all());
        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            // dd($errors);
            Alert::error('Gagal', $errors);
            return back()->withInput();
        }
        $begin = new \DateTime($request["tanggal_mulai"]);
        $end = new \DateTime($request["tanggal_akhir"]);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
        $daterange = new \DatePeriod($begin, $interval, $end);

        // dd($request->all());
        foreach ($daterange as $date) {
            $tanggal_masuk = $date->format("Y-m-d");
            $tanggal_pulang = $date->format("Y-m-d");
            $malam = $date->modify('+1 day');
            $tanggal_pulang_malam = $malam->format("Y-m-d");
            // dd($tanggal_pulang_malam);

            $week = Carbon::parse($date)->dayOfWeek;
            if ($week == 1) {
                $request["status_absen"] = "LIBUR";
            } else {
                $request["status_absen"] = NULL;
            }
            $cek_date_same = MappingShift::where('tanggal_masuk', $tanggal_masuk)->where('tanggal_pulang', $tanggal_pulang)->where('user_id', $request->user_id)->whereNotNull('is_active')->where('shift_id', $request->shift_id)->count();
            if ($cek_date_same != 0) {
                return redirect()->back()->with('error', 'Data ada yang sama', 5000);
            }
            $request["tanggal_masuk"] = $tanggal_masuk;
            $nama_shift = Shift::where('id', $request['shift_id'])->value('nama_shift');
            if ($nama_shift == 'Malam') {
                $request["tanggal_pulang"] = $tanggal_pulang_malam;
            } else {
                $request["tanggal_pulang"] = $tanggal_pulang;
            }
            // dd($request["tanggal_pulang"]);

            $validatedData = $request->validate([
                'user_id' => 'required',
                'shift_id' => 'required',
                'tanggal_masuk' => 'required',
                'tanggal_pulang' => 'required',
            ]);

            $insert = new MappingShift();
            $insert->user_id = Karyawan::where('id', $validatedData['user_id'])->value('id');
            $insert->nik_karyawan = Karyawan::where('id', $validatedData['user_id'])->value('nomor_identitas_karyawan');
            $insert->nama_karyawan = Karyawan::where('id', $validatedData['user_id'])->value('name');
            $insert->shift_id = Shift::where('id', $validatedData['shift_id'])->value('id');
            $insert->nama_shift = Shift::where('id', $validatedData['shift_id'])->value('nama_shift');
            $insert->tanggal_masuk = $validatedData['tanggal_masuk'];
            $insert->tanggal_pulang = $validatedData['tanggal_pulang'];
            $insert->status_absen = $request['status_absen'];
            $insert->save();
        }
        // dd($week);
        // dd($week);
        $holding = request()->segment(count(request()->segments()));
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'object_id' => $insert->id,
            'kategory_activity' => 'MAPPING SHIFT',
            'activity' => 'TAMBAH MAPPING SHIFT ',
            'description' => 'Menambahkan Jadwal shift karyawan ' . $insert->nama_karyawan . ' Shift ' . $insert->nama_shift,
            'read_status' => 0
        ]);
        return redirect()->back()->with('success', 'data berhasil ditambahkan');
    }
    public function prosesEditMappingShift(Request $request)
    {
        // dd($request->all());
        date_default_timezone_set('Asia/Jakarta');
        $shift = Shift::where('id', $request['shiftSaatIni'])->first();
        $now = Carbon::now();
        $validatedData = $request->validate([
            'idMappingShift' => 'required',
            'tanggalMasukShift' => 'required',
            'shiftSaatIni' => 'required',

        ]);
        if ($now->greaterThan(Carbon::parse($validatedData['tanggalMasukShift']))) {
            return response()->json([
                'code' => 500,
                'status' => 'warning',
                'message' => 'Tanggal masuk shift tidak boleh kurang dari tanggal hari ini'
            ]);
        }
        if ($shift == NULL) {
            return response()->json([
                'code' => 500,
                'status' => 'warning',
                'message' => 'Shift tidak ditemukan'
            ]);
        } else {
            if ($shift->tgl_pulang_besok == 1) {
                $tanggal_pulang =  Carbon::parse($validatedData['tanggalMasukShift'])->addDay('1')->format('Y-m-d');
            } else {
                $tanggal_pulang = Carbon::parse($validatedData['tanggalMasukShift'])->format('Y-m-d');
            }
        }
        try {
            MappingShift::where('id', $request['idMappingShift'])->update([
                'shift_id' => Shift::where('id', $validatedData['shiftSaatIni'])->value('id'),
                'tanggal_masuk' => $validatedData['tanggalMasukShift'],
                'nama_shift' => Shift::where('id', $validatedData['shiftSaatIni'])->value('nama_shift'),
                'tanggal_pulang' => $tanggal_pulang,
                'status_absen' => $request['status_absen'],
            ]);
            return response()->json([
                'code' => 200,
                'status' => 'Success',
                'message' => 'Data Berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'Gagal menyimpan jadwal',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_columns(Request $request)
    {
        // dd('ok');
        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        // dd($end_date);
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $data_columns_header[] = ['header' => Carbon::parse($date)->isoFormat('ddd/DD')];
            $data_columns[] = ['data' => 'tanggal_' . $date->format('dmY'), 'name' => 'tanggal_' . $date->format('dmY')];
        }
        $count_period = count($period);

        return array(
            'data_columns_header' => $data_columns_header,
            'count_period' => $count_period,
            'datacolumn' => $data_columns,
            'start_date' => Carbon::parse($request->start_date)->format('d-m-Y'),
            'end_date' => Carbon::parse($request->end_date)->format('d-m-Y')
        );
    }

    public function mapping_shift_datatable(Request $request)
    {
        $holding = Holding::where('holding_code', $request->holding)->first();
        // dd($holding);
        // dd($request->all(), $request->departemen_filter);
        $start_date = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $now = Carbon::parse($start_date);
        $now1 = Carbon::parse($end_date);
        $period = CarbonPeriod::create($now, $now1);
        // dd($request->start_date, $request->end_date);
        $table = Karyawan::with([
            'Departemen',
            'Jabatan',
            'Bagian',
            'MappingShift' => function ($q) use ($now, $now1) {
                $q->whereBetween('tanggal_masuk', [$now, $now1])
                    ->with('Shift')
                    ->orderBy('tanggal_masuk', 'ASC');
            }
        ])
            ->where('kontrak_kerja', $holding->id)
            ->where('shift', 'Shift')
            ->where('kategori', 'Karyawan Bulanan')
            ->where('status_aktif', 'AKTIF');

        if (!empty($request->departemen_filter)) {
            $table->whereIn('dept_id', (array)$request->departemen_filter);
        }
        if (!empty($request->divisi_filter)) {
            $table->whereIn('divisi_id', (array)$request->divisi_filter);
        }
        if (!empty($request->bagian_filter)) {
            $table->whereIn('bagian_id', (array)$request->bagian_filter);
        }
        if (!empty($request->jabatan_filter)) {
            $table->whereIn('jabatan_id', (array)$request->jabatan_filter);
        }
        $table->orderBy('name', 'ASC');
        // $table->limit(1);
        $query = $table->get();
        // dd($query);
        $column = DataTables::of($query);
        foreach ($period as $date) {
            $colName = 'tanggal_' . $date->format('dmY');
            $column->addColumn('tanggal_' . $date->format('dmY'), function ($row) use ($date) {
                $shift = $row->MappingShift->where('tanggal_masuk', $date->format('Y-m-d'))->first();
                if ($shift == NULL) {
                    return '-';
                } else {
                    $backgroundColor = $shift->Shift->kode_warna; // Nilai Hex (misal: #007bff)

                    // Panggil fungsi untuk menentukan class warna teks yang kontras
                    $textColorClass = getContrastTextColor($backgroundColor);
                    $shiftName = $shift->Shift->nama_shift;

                    // Menggunakan div dengan style yang memastikan warna mengisi cell
                    $tanggal = Carbon::parse($date->format('Y-m-d'))->isoFormat('DD MMMM YYYY');
                    $jabatan = $row->Jabatan->nama_jabatan ?? '-';
                    $bagian =  $row->Bagian->nama_bagian ?? '-';
                    $departemen = $row->Departemen->nama_departemen ?? '-';
                    $shiftid = $shift->Shift->id ?? '-';
                    return "<a href='javascript:void(0);'id='detail_shift' data-idmapping='{$shift->id}' data-idkaryawan='{$row->id}' data-nama='{$row->name}' data-jabatan='{$jabatan}' data-bagian='{$bagian}' data-departemen='{$departemen}' data-nip='{$row->nomor_identitas_karyawan}' data-tanggal='{$tanggal}' data-shift='{$shiftid}' data-tanggal_masuk='{$date->format('Y-m-d')}'><div class='text-center' style='width: 100%; background-color: {$shift->Shift->kode_warna}; height: 100%; display: block; padding: 5px 5px; border-radius: 0; margin: -8px 0 -8px 0; line-height: 2.5;'><span class='{$textColorClass}'>{$shiftName}</span></div></a>";

                    // ATAU, jika framework CSS Anda sudah menyediakan class full-cell:
                    // return '<span class="full-cell-success">' . $shift->Shift->nama_shift . '</span>'; 
                }
            });
            $data_tanggal[] = $colName;
        }
        $column->addColumn('jabatan', function ($row) {
            return $row->Jabatan->nama_jabatan ?? '-';
        });
        $column->addColumn('departemen', function ($row) {
            return $row->Departemen->nama_departemen ?? '-';
        });
        $rawCols = array_merge([
            'jabatan',
            'departemen',
        ], $data_tanggal);
        $column->rawColumns($rawCols);
        return $column->make(true);
    }

    public function mapping_calendar($holding, Request $request)
    {
        // dd($request->all());
        try {
            $holding = Holding::where('holding_code', $holding)->first();
            $events = [];

            $table = MappingShift::with([
                'Shift'
            ])->with(['User'])
                ->orderBy('tanggal_masuk', 'ASC')
                ->get();
            // dd($table);
            foreach ($table as $row) {
                $events[] = [
                    'title' => $row->User->name,
                    'start' => $row->tanggal_masuk,
                    'end' => $row->tanggal_keluar,
                    'color' => '#3788d8',
                ];
            }
            return response()->json($events);
        } catch (\Exception $e) {
            // log ke laravel.log biar tahu detail error-nya
            \Log::error('Error di getEvents: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getDetailTanggal($holding, Request $request)
    {
        $tanggal = $request->tanggal;
        // dd($tanggal);
        $records = MappingShift::with(['User', 'Shift'])
            ->whereDate('tanggal_masuk', $tanggal)
            ->get();

        if ($records->isEmpty()) {
            return response()->json(['html' => '<p>Tidak ada shift di tanggal ini.</p>']);
        }

        $html = '<table class="table table-sm table-striped mb-0">';
        $html .= '<thead><tr><th>Nama Karyawan</th><th>Shift</th><th</tr></thead><tbody>';
        foreach ($records as $r) {
            $html .= '<tr><td>' . $r->User->name . '</td><td>' . $r->Shift->nama_shift . '</td></tr>';
        }
        $html .= '</tbody></table>';

        return response()->json(['html' => $html]);
    }

    public function mapping_shift_detail_datatable($id, $holding, Request $request)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        // dd($holding);
        // dd($request->start_date, $request->end_date, $holding);
        $nowMonth = Carbon::parse($request->filter_month)->month;
        $table = MappingShift::with([
            'Shift'
        ])
            ->where('karyawan_id', $request->id)
            ->whereBetween('tanggal_masuk', [$request->start_date, $request->end_date])
            ->orderBy('tanggal_masuk', 'DESC')
            ->whereNotNull('is_active')
            ->get();
        // dd($table);
        return DataTables::of($table)
            ->addColumn('nama_shift', function ($row) {
                if ($row->status_absen == 'LIBUR') {
                    return '<span class="badge bg-label-danger">' . $row->status_absen . '</span>';
                } else {
                    return $row->Shift->nama_shift ?? '-';
                }
            })
            ->addColumn('tanggal_masuk', function ($row) {
                return Carbon::parse($row->tanggal_masuk)->isoFormat('DD-MM-YYYY') ?? '-';
            })
            ->addColumn('jam_masuk', function ($row) {
                if ($row->status_absen == 'LIBUR') {
                    return '<span class="badge bg-label-danger">' . $row->status_absen . '</span>';
                } else {
                    return Carbon::parse($row->Shift->jam_masuk)->isoFormat('H:mm') ?? '-';
                }
            })
            ->addColumn('tanggal_keluar', function ($row) {
                return Carbon::parse($row->tanggal_keluar)->isoFormat('DD-MM-YYYY') ?? '-';
            })
            ->addColumn('jam_keluar', function ($row) {
                if ($row->status_absen == 'LIBUR') {
                    return '<span class="badge bg-label-danger">' . $row->status_absen . '</span>';
                } else {
                    return Carbon::parse($row->Shift->jam_keluar)->isoFormat('H:mm') ?? '-';
                }
            })
            ->addColumn('option', function ($row) {
                if ($row->tanggal_masuk < date('Y-m-d')) {
                    return '<button class="btn btn-sm btn-secondary" disabled>Edit</button>';
                } else {
                    return '<button class="btn btn-sm btn-primary" data-id="' . $row->id . '" id="btn_edit_mapping_shift">Edit</button>';
                }
            })
            ->rawColumns(['jabatan', 'nama_shift', 'mapping_shift', 'tanggal_masuk', 'tanggal_keluar', 'jam_masuk', 'jam_keluar', 'option'])
            ->make(true);
    }
    public function mapping_shift_detail_index($id, $holding, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        $nowMonth = Carbon::now()->month;
        $user_check = Karyawan::where('id', $id)->first();
        if ($user_check == NULL) {
            return redirect()->back()->with('error', 'Data Karyawan Tidak Ditemukan');
        } else {
            if ($user_check->kategori == 'Karyawan Bulanan') {
                if ($user_check->dept_id == NULL || $user_check->divisi_id == NULL || $user_check->jabatan_id == NULL) {
                    return redirect()->back()->with('error', 'Jabatan Karyawan Kosong');
                }
            }
        }
        // dd($oke);
        $user = Karyawan::with([
            'Jabatan',
            'KontrakKerja',
            'Divisi',
            'MappingShift' => function ($q) use ($nowMonth) {
                $q->with('Shift')
                    ->whereMonth('tanggal_masuk', $nowMonth)
                    ->limit(100);
            }
        ])
            ->findOrFail($id);
        // dd($user);
        if ($user->kategori === 'Karyawan Bulanan') {
            if (empty($user->dept_id) || empty($user->divisi_id) || empty($user->jabatan_id)) {
                return back()->with('error', 'Jabatan Karyawan Kosong');
            }
        }
        $jabatan = Jabatan::whereIn('id', array_filter([
            $user->jabatan_id,
            $user->jabatan1_id,
            $user->jabatan2_id,
            $user->jabatan3_id,
            $user->jabatan4_id,
        ]))->get();
        $divisi = Divisi::whereIn('id', array_filter([
            $user->divisi_id,
            $user->divisi1_id,
            $user->divisi2_id,
            $user->divisi3_id,
            $user->divisi4_id,
        ]))->get();
        $no = 1;
        $no1 = 1;
        $oke = $user->MappingShift->last();
        // $shift = Carbon::parse($oke->tanggal_masuk)->addDay(1)->format('Y-m-d');
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
        // dd($shift);
        return view('admin.karyawan.mappingshift', [
            'title'             => 'Mapping Shift',
            'karyawan'          => $user,
            'holding'           => $holding,
            'menu'              => $menus,
            // 'shift_karyawan'    => $shift,
            'shift'             => Shift::all(),
            'jabatan_karyawan'  => $jabatan,
            'divisi_karyawan'   => $divisi,
            'no'                => $no,
            'no1'               => $no1,
        ]);
    }
}
