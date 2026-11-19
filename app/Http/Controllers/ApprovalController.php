<?php

namespace App\Http\Controllers;

use App\Mail\DiterimaWawancaraMail;
use App\Mail\KaryawanPenggantiMail;
use App\Mail\SemuaIzin;
use App\Models\ActivityLog;
use App\Models\Cuti;
use App\Models\CutiDetail;
use App\Models\HistoryApproval;
use App\Models\Holding;
use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KategoriCuti;
use App\Models\KuotaCutiKaryawan;
use App\Models\MappingShift;
use App\Models\MappingShiftHead;
use App\Models\MappingShiftTukar;
use App\Models\MappingShiftTukarAtasan;
use App\Models\Menu;
use App\Models\Penugasan;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\TicketHead;
use App\Models\TicketDetail;
use App\Models\TicketSubDetail;
use App\Models\TicketApproval;
use App\Models\TicketApprovalFlow;
use App\Models\TicketApprovalStep;
use App\Models\TicketApprovalComment;
use App\Models\TicketConditionValue;
use App\Models\TicketDependencyRule;
use App\Models\TicketApprovalValidator;
use App\Models\TicketAssignmentMaster;
use App\Models\TicketDifficultyMaster;
use App\Models\TicketHeadAttachment;
use App\Models\TicketVendorHead;
use App\Models\TicketVendorDetail;
use App\Models\User;
use App\Services\TicketApprovalEmailService;
use App\Services\TicketVendorApprovalEmailService;
use App\Services\TicketGenerationService;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Doctrine\DBAL\Schema\Index;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF;


class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $dataizin       = Izin::with('User')->where('id_approve_atasan', $user_karyawan->id)
            ->where('status_izin', 1)
            // ->whereNotNull('ttd_pengajuan')
            ->get();
        // get atasan tingkat 
        $datacuti_tingkat1       = Cuti::with('KategoriCuti')
            ->where('status_cuti', 1)
            ->join('karyawans', 'karyawans.id', '=', 'cutis.user_id')
            ->where('id_user_atasan', $user_karyawan->id)
            ->whereNotNull('ttd_user')
            ->select('cutis.*', 'karyawans.name', 'karyawans.foto_karyawan')
            ->get();
        $datacuti_tingkat2       = Cuti::with('KategoriCuti')
            ->where('status_cuti', 2)
            ->join('karyawans', 'karyawans.id', '=', 'cutis.user_id')
            ->where('id_user_atasan2', $user_karyawan->id)
            ->whereNotNull('ttd_user')
            ->select('cutis.*', 'karyawans.name', 'karyawans.foto_karyawan')
            ->get();
        // dd($datacuti_tingkat2);
        $datapenugasan  = Penugasan::join('karyawans', 'karyawans.id', 'penugasans.id_user')
            ->where('penugasans.status_penugasan', '!=', 5)
            ->where('id_diminta_oleh', $user_karyawan->id)
            ->orWhere('id_disahkan_oleh', $user_karyawan->id)
            ->orWhere('id_user_hrd', $user_karyawan->id)
            ->orWhere('id_user_finance', $user_karyawan->id)
            ->select('penugasans.*', 'karyawans.name')
            ->get();

        // dd($user);
        // dd($dataizin);
        // dd($datacuti_tingkat1);
        // dd($datacuti_tingkat2);
        // dd($datapenugasan);
        return view(
            'users.approval.index',
            [
                'dataizin'          => $dataizin,
                'user_karyawan'     => $user_karyawan,
                'datacuti_tingkat1' => $datacuti_tingkat1,
                'datacuti_tingkat2' => $datacuti_tingkat2,
                'datapenugasan'     => $datapenugasan,
            ]
        );
    }
    public function mapping_shift($id, Request $request)
    {
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
        $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        // $mapping_shift = MappingShift::where('approved_by', Auth::user()->id)->whereNotNull('is_active')->where('status_approved', 0)->get();

        $badge_shift = MappingShiftHead::whereNull('approved')->count();
        $badge_pegawai_ubah = MappingShiftTukar::where('approval1', 1)->whereNull('approval2')->count();
        $badge_atasan_ubah = MappingShiftTukarAtasan::whereNull('approved')->count();

        $menu_active = User::where('id', Auth::user()->id)->first();
        $menu_active->menu_active = $id;
        $menu_active->save();
        return view('users.approval.mapping_shift.mapping_shift', [
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'user_karyawan' => $user_karyawan,
            'menu_active' => $menu_active,
            'badge_shift' => $badge_shift,
            'badge_pegawai_ubah' => $badge_pegawai_ubah,
            'badge_atasan_ubah' => $badge_atasan_ubah,
            'menu_id' => $id,
            // 'mapping_shift' => $mapping_shift
        ]);
    }
    public function get_list_approval_mapping_shift(Request $request, $id)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // $mapping_shift = MappingShift::selectRaw('DISTINCT ON (legal_number_mapping) *')->with('KaryawanInput')
            //     // ->where('approved_by', $user_karyawan->id)
            //     ->where('status_approved', 0)
            //     ->whereBetween('created_at', array($request->start, $request->end))
            //     ->whereNotNull('is_active')
            //     ->orderBy('legal_number_mapping')
            //     ->orderBy('created_at', 'desc')
            //     ->get();
            // $mapping_shift_approved = MappingShift::selectRaw('DISTINCT ON (legal_number_mapping) *')->with('KaryawanInput')
            //     // ->where('approved_by', $user_karyawan->id)
            //     ->where('status_approved', 1)
            //     ->whereBetween('created_at', array($request->start, $request->end))
            //     ->whereNotNull('is_active')
            //     ->orderBy('legal_number_mapping')
            //     ->orderBy('created_at', 'desc') // pilih data terbaru
            //     ->get();

            $mapping_shift = MappingShiftHead::whereNull('approved')
                ->whereBetween('start_date', array($request->start, $request->end))
                ->get();
            $mapping_shift_approved = MappingShiftHead::whereNotNull('approved')
                ->whereBetween('start_date', array($request->start, $request->end))
                ->get();
            $menu_active = User::where('id', Auth::user()->id)->first();
            $menu_active->menu_active = $id;
            $menu_active->save();
            return response()->json([
                'code' => 200,
                'data' => $mapping_shift,
                'data_approved' => $mapping_shift_approved
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    // public function approvalDetail($id, Request $request)
    // {
    //     if (!Auth::check()) {
    //         $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
    //         return redirect()->route('login');
    //     }
    //     $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
    //     $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
    //     if (count($get_role) == 0) {
    //         $roleId = null;
    //     } else {
    //         $roleId = $get_role;
    //     }
    //     if ($roleId == null) {
    //         $all_menus = collect();
    //         $all_menu_bar = collect();
    //         $all_menu_main = collect();
    //     } else {

    //         $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
    //             $query->where('kategori', 'mobile');
    //         }])
    //             ->whereHas('Menu', function ($query) {
    //                 $query->where('kategori', 'mobile');
    //                 $query->where('main', 1);
    //                 $query->orderBy('sort_order', 'ASC');
    //             })
    //             ->whereIn('role_id', $get_role)
    //             ->get();
    //         $all_menus  = RoleMenu::with(['Menu' => function ($query) {
    //             $query->where('kategori', 'mobile');
    //         }])
    //             ->whereHas('Menu', function ($query) {
    //                 $query->where('kategori', 'mobile');
    //                 $query->where('main', 0);
    //                 $query->orderBy('sort_order', 'ASC');
    //             })
    //             ->whereIn('role_id', $get_role)
    //             ->get();

    //         $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
    //             $query->where('kategori', 'mobile');
    //         }])
    //             ->whereHas('Menu', function ($query) {
    //                 $query->where('kategori', 'mobile');
    //                 $query->where('main', 2);
    //                 $query->orderBy('sort_order', 'ASC');
    //             })
    //             ->whereIn('role_id', $get_role)
    //             ->get();
    //     }
    //     return view('users.approval.mapping_shift.mapping_shift_detail', [
    //         'all_menus' => $all_menus,
    //         'all_menu_main' => $all_menu_main,
    //         'all_menu_bar' => $all_menu_bar,
    //         'id' => $id,
    //         'user_karyawan' => $user_karyawan
    //     ]);
    // }
    public function approvalDetail($id, $menu_id, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
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
        $detail_mapping = MappingShift::where('id_head', $id)
            ->with(['Karyawan' => function ($query) {
                $query->with(['KaryawanJabatan' => function ($query) {
                    $query->with('Jabatan');
                    $query->with('Bagian');
                    $query->with('Departemen');
                    $query->with('Divisi');
                }]);
                $query->orderBy('name', 'ASC');
            }])
            ->orderBy('tanggal_masuk', 'ASC')
            ->get()

            ->groupBy(function ($item) {
                return $item->tanggal_masuk;
            });

        $shifting = MappingShiftHead::where('id', $id)->first();
        return view('users.approval.mapping_shift.mapping_shift_detail', [
            'all_menus' => $all_menus,
            'all_menu_main' => $all_menu_main,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $menu_id,
            'detail_mapping' => $detail_mapping,
            'shifting' => $shifting,
            'user_karyawan' => $user_karyawan
        ]);
    }
    public function get_list_approval_mapping_shift_detail($id)
    {
        // dd($tanggal)
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $detail_first = MappingShift::with('KaryawanInput')->where('legal_number_mapping', $id)->whereNotNull('is_active')->orderBy('tanggal_masuk', 'asc')->first();
            $detail_last = MappingShift::where('legal_number_mapping', $id)->whereNotNull('is_active')->orderBy('tanggal_masuk', 'desc')->first();
            $tanggal_mulai = Carbon::parse($detail_first->tanggal_masuk)->format('Y-m-d');
            $tanggal_selesai = Carbon::parse($detail_first->tanggal_masuk)->addDay(6)->format('Y-m-d');
            $periode = CarbonPeriod::create($tanggal_mulai, $tanggal_selesai);
            $detail = [];
            foreach ($periode as  $date) {
                $tanggal = $date->format('Y-m-d');
                $detail[$tanggal] = MappingShift::where('legal_number_mapping', $id)->whereNotNull('is_active')->with(['Karyawan' => function ($query) {
                    $query->with(['KaryawanJabatan' => function ($query) {
                        $query->with('Jabatan');
                        $query->with('Bagian');
                        $query->with('Departemen');
                        $query->with('Divisi');
                    }]);
                    $query->orderBy('name', 'ASC');
                }])
                    ->whereDate('tanggal_masuk', $tanggal)
                    ->distinct('karyawan_id', 'nama_karyawan')
                    ->orderBy('nama_karyawan', 'ASC')
                    ->get();
            }

            return response()->json([
                'code' => 200,
                'data' => $detail,
                'data_first' => $detail_first,
                'data_last' => $detail_last,
                'id' => $id,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'user_karyawan' => $user_karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function save_approval_mapping_shift(Request $request)
    {
        // dd($request->all());

        $validatedData = $request->validate([
            'signature' => 'required'
        ], [
            'signature.required' => 'Signature wajib diisi'
        ]);
        if (!$validatedData) {
            return response()->json([
                'code' => 500,
                'message' => 'Signature wajib diisi'
            ]);
        }
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
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
            $mapping_log = MappingShiftHead::where('id', $request->shift)->first();

            $mapping_log->update([
                'approved' => 1,
                'approved_signature' => $fileName,
                'approved_by' => $user_karyawan->id,
                'approved_by_name' => $user_karyawan->name,
                'approved_at' => date('Y-m-d H:i:s'),
            ]);

            $mapping_shift = MappingShift::where('id_head', $request->shift)->update([
                'status_approved' => 1,
                'approved_signature' => $fileName,
                'approved_by'        => $user_karyawan->id,
                'approved_at'        => date('Y-m-d H:i:s'),
            ]);

            $karyawan_id = Karyawan::where('id', $mapping_log->input_by)->first();
            $holdings    = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $user_name   = User::where('id', $request->user()->id)->first();

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'id_table' => $request->shift ?? '-',
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shifts',
                'activity' => 'update',
                'description' => 'Menyetujui pengajuan shift : ' . $mapping_log->judul,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Tanda Tangan Berhasil disimpan',
                'redirect' => url('approval/mapping_shift/' . $request->menu_id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    // APPROVAL 
    public function dashboard($id)
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


        return view('users.approval.index', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function cuti($menu_id)
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
        return view('users.approval.cuti_index', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_cuti(Request $request)
    {
        // dd($request->all());
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_cuti = Cuti::with(['Atasan1', 'Atasan2', 'KategoriCuti'])->where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_cuti', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_cuti', 1);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
            // dd($data_cuti);
            return response()->json([
                'code' => 200,
                'data' => $data_cuti

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_cuti_riwayat(Request $request)
    {
        $start = $request->awal;
        $end = $request->akhir;
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_cuti = Cuti::with(['Atasan1', 'Atasan2', 'KategoriCuti'])->where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_cuti', '!=', 0)
                        ->where('status_approve_atasan1', '!=', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_cuti', '!=', 0)
                            ->where('status_approve_atasan2', '!=', 0);
                    });
            })->whereBetween('tanggal_mulai', array($start, $end))->get();
            // dd($data_cuti);
            return response()->json([
                'code' => 200,
                'data' => $data_cuti

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approveCutiDetail($id, Request $request)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()) {
            return redirect('/');
        }
        try {
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
            $data = Cuti::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $menu_id = Auth::user()->menu_active;
            return view('users.approval.cuti.cutiDetail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'user_karyawan' => $user_karyawan,
                'menu_id' => $menu_id,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
            return redirect()->back()->with('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
        }
    }
    public function cetak_form_cuti($id)
    {
        $cuti = Cuti::with('KategoriCuti', 'KaryawanPengganti', 'Atasan1', 'Atasan2')->where('id', $id)->first();
        $jumlah_hari = CutiDetail::where('id_cuti', $cuti->id)->count();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $cuti->karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $cuti->karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
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
    public function get_approveCutiDetail($id)
    {
        try {
            $detail_cuti = CutiDetail::where('id_cuti', $id)->get();
            $jumlah_cuti = CutiDetail::where('id_cuti', $id)->count();
            $data_cuti = Cuti::with('KategoriCuti', 'User', 'KaryawanPengganti', 'Atasan1', 'Atasan2', 'CutiKuota')->where('id', $id)->first();
            $kuota_cuti_karyawan = KuotaCutiKaryawan::with('PeriodeGroup')->where('karyawan_id', $data_cuti->karyawan_id)->where('status', 1)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $dibatalkan = CutiDetail::where('id_cuti', $id)->where('status', 2)->count();

            if ($dibatalkan == $jumlah_cuti) {
                $detail_exists = 'semua';
            } elseif ($dibatalkan > 0) {
                $detail_exists = 'sebagian';
            } else {
                $detail_exists = 'tidak';
            }
            return response()->json([
                'code' => 200,
                'data' => $data_cuti,
                'jumlah_cuti' => $jumlah_cuti,
                'detail_cuti' => $detail_cuti,
                'detail_exists' => $detail_exists,
                'user' => $user_karyawan,
                'kuota' => $kuota_cuti_karyawan,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function save_dobel($id, Request $request)
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
        // dd($fileName);
        // simpan ke storage
        try {
            if ($request->sisa > 0) {
                $image = $request->signature;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $fileName = 'signature_' . time() . '.png';
                Storage::disk('public')->put(
                    'signatures/' . $fileName,
                    base64_decode($image)
                );
                $cuti = Cuti::where('id', $id)->first();
                $kuota_cuti = KuotaCutiKaryawan::where('karyawan_id', $cuti->karyawan_id)->where('status', 1)->first();
                $user_karyawan_pengajuan = Karyawan::where('id', $cuti->karyawan_id)->first();
                $holdings = Holding::where('id', $user_karyawan_pengajuan->kontrak_kerja)->first();
                $pengganti = Karyawan::where('id', $cuti->karyawan_pengganti_id)->first();
                $tgl_pengajuan = Carbon::parse($cuti->tanggal_pengajuan)->format('Ymd');
                $count_cuti = Cuti::whereIn('status_cuti', [5, 2])->whereDate('tanggal_pengajuan', $cuti->tanggal_pengajuan)->where('id', '!=', $cuti->id)->get()->count();
                if ($count_cuti == 0) {
                    $last_count = 1;
                } else {
                    $last_count = $count_cuti + 1;
                }
                if (strlen($last_count) == 1) {
                    $last_count = '000' . $last_count;
                } else if (strlen($last_count) == 2) {
                    $last_count = '00' . $last_count;
                } else {
                    $last_count = '0' . $last_count;
                }
                $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FCT' . '/' . $tgl_pengajuan . '/' . $last_count;
                $email              = $pengganti?->email;
                $nama_pengganti     = $cuti->nama_karyawan_pengganti;
                $nama               = $cuti->nama_karyawan;
                $tanggal_mulai      = $cuti->tanggal_mulai;
                $tanggal_selesai    = $cuti->tanggal_selesai;
                Cache::put("Wawancara:$email", true, now()->addHours(2));
                if ($email) {
                    Mail::mailer('hrd')->to($email)->queue(new KaryawanPenggantiMail($nama_pengganti, $nama, $tanggal_mulai, $tanggal_selesai));
                }
                $cuti->update([
                    'status_cuti' => 2,
                    'no_form_cuti' => $no_form,
                    'kuota' => $request->kuota,
                    'terpakai' => $request->terpakai,
                    'sisa' => $request->sisa,
                    'ttd_atasan2' => $fileName,
                    'status_approve_atasan2' => 1,
                    'toleransi' => 1,
                    'waktu_approve2' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $kuota_cuti->update([
                    'kuota' => $request->kuota,
                    'terpakai' => $request->terpakai,
                    'sisa' => $request->sisa,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                CutiDetail::where('id_cuti', $cuti->id)->update([
                    'status' => 1
                ]);
                QuickPanel::where('content_id', $id)->where('quick_type', 'cuti')->update([
                    'read_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),

                ]);

                $user_name = User::where('id', $request->user()->id)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $cuti->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'cutis',
                    'activity' => 'add',
                    'description' => 'Menerima Pengaujan cuti Non Prosedur sebagai atasan ' . $cuti->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);

                return response()->json([
                    'code' => 200,
                    'message' => 'Pengajuan Berhasil Di-Approve',
                ]);
            } else {
                return response()->json([
                    'code' => 401,
                    'message' => 'Saldo cuti tidak cukup jika dipotong dobel',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function save_approval_cuti(Request $request, $id)
    {
        // dd($request->all(), $id);
        try {
            $rules = [
                'signature' => 'required',
                'note' => 'nullable',
            ];
            $customMessages = [
                'signature.required' => 'Tanda Tangan tidak boleh kosong.',
            ];
            $validasi = Validator::make($request->all(), $rules, $customMessages);
            if ($validasi->fails()) {
                $errors = $validasi->errors()->first();
                return response()->json([
                    'code' => 422,
                    'message' => $errors
                ]);
            }
            DB::beginTransaction();
            // dd($id);
            $cuti = Cuti::where('id', $id)->first();
            if ($cuti == null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ]);
            } else {
                $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
                $user_karyawan_pengajuan = Karyawan::with('KontrakKerja')->where('id', $cuti->karyawan_id)->first();
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
                $count_cuti = Cuti::whereIn('status_cuti', [5, 2])->whereDate('tanggal_pengajuan', $cuti->tanggal_pengajuan)->where('id', '!=', $cuti->id)->get()->count();
                if ($count_cuti == 0) {
                    $last_count = 1;
                } else {
                    $last_count = $count_cuti + 1;
                }
                if (strlen($last_count) == 1) {
                    $last_count = '000' . $last_count;
                } else if (strlen($last_count) == 2) {
                    $last_count = '00' . $last_count;
                } else {
                    $last_count = '0' . $last_count;
                }

                $tgl_pengajuan = Carbon::parse($cuti->tanggal_pengajuan)->format('Ymd');
                $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FCT' . '/' . $tgl_pengajuan . '/' . $last_count;
                // dd($no_form);
                $manajemen_saldo_cuti = KuotaCutiKaryawan::where('karyawan_id', $user_karyawan_pengajuan->id)->where('status', 1)->first();

                $sisa = (int) ($manajemen_saldo_cuti->sisa ?? 0);
                $total_cuti = (int) ($request->total_cuti ?? 0);
                $saldo_terpakai = (int)($manajemen_saldo_cuti->terpakai ?? 0);
                $new_kuota = $sisa - $total_cuti;
                $terpakai = $saldo_terpakai + $total_cuti;
                // dd($new_kuota, $terpakai);
                $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $pengganti = Karyawan::where('id', $cuti->karyawan_pengganti_id)->first();
                // dd($user_karyawan->kuota_cuti_tahunan, $request->total_cuti);
                if ($request->status == 'approved') {
                    if ($cuti->status_cuti == 0) {
                        $cuti->ttd_atasan = $fileName;
                        $cuti->waktu_approve = date('Y-m-d H:i:s');
                        if ($cuti->id_user_atasan == $cuti->id_user_atasan2) {
                            $cuti->no_form_cuti = $no_form;
                            $cuti->ttd_atasan2 = $fileName;
                            $cuti->waktu_approve2 = date('Y-m-d H:i:s');
                            $cuti->status_cuti = 2;
                            $cuti->status_approve_atasan1 = 1;
                            $cuti->status_approve_atasan2 = 1;

                            // $cuti->catatan = $request->note;
                            // $cuti->catatan2 = $request->note;

                            $email              = $pengganti?->email;
                            $nama_pengganti     = $cuti->nama_karyawan_pengganti;
                            $nama               = $cuti->nama_karyawan;
                            $tanggal_mulai      = $cuti->tanggal_mulai;
                            $tanggal_selesai    = $cuti->tanggal_selesai;
                            Cache::put("Wawancara:$email", true, now()->addHours(2));
                            if ($email) {
                                Mail::mailer('hrd')->to($email)->queue(new KaryawanPenggantiMail($nama_pengganti, $nama, $tanggal_mulai, $tanggal_selesai));
                            }

                            // UPDATE KUOTA CUTI KARYAWAN
                            if ($cuti->kategori_cuti_id == null) {
                                $cuti->kuota = $manajemen_saldo_cuti->kuota;
                                $cuti->terpakai = $terpakai;
                                $cuti->sisa = $new_kuota;
                                $manajemen_saldo_cuti->sisa = $new_kuota;
                                $manajemen_saldo_cuti->terpakai = $terpakai;
                                $manajemen_saldo_cuti->save();
                            }

                            CutiDetail::where('id_cuti', $cuti->id)->update([
                                'status' => 1
                            ]);


                            // UPDATE KUOTA CUTI KARYAWAN
                            QuickPanel::where('content_id', $id)->where('quick_type', 'cuti')->update([
                                'read_at' => date('Y-m-d H:i:s'),
                                'status' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),

                            ]);
                            ActivityLog::create([
                                'user_id' => $request->user()->id,
                                'user_name' => $user_name->username,
                                'menu_id' => $request->menu_id,
                                'menu_name' => $nama_menu->name,
                                'id_table' => $cuti->id,
                                'holding_id' => $holdings->id ?? '-',
                                'holding_category' => $holdings->holding_category ?? '-',
                                'table' => 'cutis',
                                'activity' => 'add',
                                'description' => 'Menerima Pengaujan cuti sebagai atasan ' . $cuti->nama_karyawan,
                                'keterangan' => 'Success',
                                'karir' => '2'
                            ]);
                            $email              = $cuti->User?->email ?? null;;
                            $nama_penerim       = $cuti->nama_karyawan;
                            $nama               = $user_karyawan->name;
                            $tanggal_mulai      = Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y');
                            $tanggal_selesai    = Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y');
                            $judul              = 'Pengajuan Cuti Diterima';
                            $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan cuti anda diterima oleh :';

                            $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                            $penutup            = 'Selamat berlibur!';
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
                            $cuti->status_cuti = 1;
                            // $cuti->catatan = $request->note;
                            $cuti->status_approve_atasan1 = 1;
                            $cuti->status_approve_atasan2 = 0;

                            QuickPanel::where('content_id', $id)->where('quick_type', 'cuti')->update([
                                'read_at' => date('Y-m-d H:i:s'),
                                'status' => 1,
                                'updated_at' => date('Y-m-d H:i:s'),

                            ]);
                            // dd('ok');
                            $new_quick = new QuickPanel();
                            $new_quick->id_quick = Uuid::uuid4();
                            $new_quick->user_id_from = $cuti->id_user_atasan;
                            $new_quick->user_id_to = $cuti->id_user_atasan2;
                            $new_quick->content_id = $cuti->id;
                            $new_quick->quick_type = 'cuti';
                            $new_quick->status = 0;
                            $new_quick->created_at = date('Y-m-d H:i:s');
                            $new_quick->updated_at = date('Y-m-d H:i:s');
                            $new_quick->message_quick = 'Ada Pengajuan Cuti dari ' . $cuti->nama_karyawan;
                            $new_quick->title_quick = 'Pengajuan Cuti';
                            $new_quick->save();
                            ActivityLog::create([
                                'user_id' => $request->user()->id,
                                'user_name' => $user_name->username,
                                'menu_id' => $request->menu_id,
                                'menu_name' => $nama_menu->name,
                                'id_table' => $cuti->id,
                                'holding_id' => $holdings->id ?? '-',
                                'holding_category' => $holdings->holding_category ?? '-',
                                'table' => 'cutis',
                                'activity' => 'add',
                                'description' => 'Menerima Pengaujan cuti sebagai SPV ' . $cuti->nama_karyawan,
                                'keterangan' => 'Success',
                                'karir' => '2'
                            ]);
                            $atasan2 = $cuti->Atasan2;
                            $email_atasan = $atasan2?->email_kantor ?: $atasan2?->email;
                            $email              = $email_atasan;
                            $nama_penerim       = $cuti->nama_atasan2;
                            $nama               = $cuti->nama_karyawan;
                            $tanggal_mulai      = Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y');
                            $tanggal_selesai    = Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y');
                            $judul              = 'Pengajuan Cuti';
                            $perihal            = 'Melalui email ini, kami menginformasikan bahwa ada pengajuan cuti dari :';

                            $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                            $penutup            = 'Pengajuan ini dapat disetujui atau ditolak melalui button di bawah ini';
                            $link               = url('/approval/cuti/cutiDetail/' . $cuti->id);
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
                    } else if ($cuti->status_cuti == 1) {
                        $cuti->no_form_cuti = $no_form;
                        $cuti->ttd_atasan2 = $fileName;
                        // $cuti->catatan2 = $request->note;
                        $cuti->waktu_approve2 = date('Y-m-d H:i:s');
                        $cuti->status_cuti = 2;
                        $cuti->status_approve_atasan2 = 1;

                        // UPDATE KUOTA CUTI KARYAWAN
                        QuickPanel::where('content_id', $id)->where('quick_type', 'cuti')->update([
                            'read_at' => date('Y-m-d H:i:s'),
                            'status' => 1,
                            'updated_at' => date('Y-m-d H:i:s'),

                        ]);
                        CutiDetail::where('id_cuti', $cuti->id)->update([
                            'status' => 1
                        ]);



                        $email              = $pengganti?->email;
                        $nama_pengganti     = $cuti->nama_karyawan_pengganti;
                        $nama_cuti               = $cuti->nama_karyawan;
                        $tanggal_mulai      = Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y');
                        $tanggal_selesai    = Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y');
                        Cache::put("Wawancara:$email", true, now()->addHours(2));
                        if ($email) {
                            Mail::mailer('hrd')->to($email)->queue(new KaryawanPenggantiMail($nama_pengganti, $nama_cuti, $tanggal_mulai, $tanggal_selesai));
                        }

                        if ($cuti->kategori_cuti_id == null) {
                            $cuti->kuota = $manajemen_saldo_cuti->kuota;
                            $cuti->terpakai = $terpakai;
                            $cuti->sisa = $new_kuota;
                            // UPDATE KUOTA CUTI KARYAWAN
                            $manajemen_saldo_cuti->sisa = $new_kuota;
                            $manajemen_saldo_cuti->terpakai = $terpakai;
                            $manajemen_saldo_cuti->save();
                        }
                        $email_user         = $cuti->User?->email ?? null;;
                        $nama_penerim       = $cuti->nama_karyawan;
                        $nama               = $user_karyawan->name;
                        $judul              = 'Pengajuan Perjalanan Dinas Diterima';
                        $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan perjalanan dinas anda diterima oleh :';

                        $waktu              = 'Yang akan dilakukan pada ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '';

                        $penutup            = 'Selamat berlibur!';
                        $link               = 'https://hrd.sumberpangan.store/';
                        Cache::put("Wawancara:$email_user", true, now()->addHours(2));
                        if ($email_user) {
                            Mail::mailer('hrd')->to($email_user)->send(new SemuaIzin(
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
                    $history = new HistoryApproval();
                    $history->id_approval = Uuid::uuid4();
                    $history->content_id = $cuti->id;
                    $history->tipe_approval = 'cuti';
                    $history->karyawan_id = $user_karyawan->id;
                    $history->karyawan_id_pengajuan = $cuti->karyawan_id;
                    $history->status_approval = 1;
                    $history->created_at = date('Y-m-d H:i:s');
                    $history->updated_at = date('Y-m-d H:i:s');
                    $history->save();
                } else if ($request->status == 'reject') {
                    if ($cuti->status_cuti == 0) {
                        $cuti->no_form_cuti = $no_form;
                        $cuti->ttd_atasan = $fileName;
                        $cuti->waktu_approve2 = date('Y-m-d H:i:s');
                        $cuti->status_cuti = 5;
                        // $cuti->catatan = $request->note;
                        $cuti->status_approve_atasan1 = 5;
                        // UPDATE KUOTA CUTI KARYAWAN

                        ActivityLog::create([
                            'user_id' => $request->user()->id,
                            'user_name' => $user_name->username,
                            'menu_id' => $request->menu_id,
                            'menu_name' => $nama_menu->name,
                            'id_table' => $cuti->id,
                            'holding_id' => $holdings->id ?? '-',
                            'holding_category' => $holdings->holding_category ?? '-',
                            'table' => 'cutis',
                            'activity' => 'add',
                            'description' => 'Menolak Pengaujan cuti sebagai SPV ' . $cuti->nama_karyawan,
                            'keterangan' => 'Success',
                            'karir' => '2'
                        ]);
                        $email              = $cuti->User?->email ?? null;;
                        $nama_penerim       = $cuti->nama_karyawan;
                        $nama               = $user_karyawan->name;
                        $tanggal_mulai      = Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y');
                        $tanggal_selesai    = Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y');
                        $judul              = 'Pengajuan Cuti Ditolak';
                        $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan cuti anda ditolak oleh :';

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
                    } else if ($cuti->status_cuti == 1) {
                        $cuti->no_form_cuti = $no_form;
                        $cuti->ttd_atasan2 = $fileName;
                        $cuti->waktu_approve2 = date('Y-m-d H:i:s');
                        $cuti->status_cuti = 5;
                        // $cuti->catatan2 = $request->note;
                        $cuti->status_approve_atasan2 = 5;
                        ActivityLog::create([
                            'user_id' => $request->user()->id,
                            'user_name' => $user_name->username,
                            'menu_id' => $request->menu_id,
                            'menu_name' => $nama_menu->name,
                            'id_table' => $cuti->id,
                            'holding_id' => $holdings->id ?? '-',
                            'holding_category' => $holdings->holding_category ?? '-',
                            'table' => 'cutis',
                            'activity' => 'add',
                            'description' => 'Menolak Pengaujan cuti sebagai Atasan Tertinggi ' . $cuti->nama_karyawan,
                            'keterangan' => 'Success',
                            'karir' => '2'
                        ]);
                        $email              = $cuti->User?->email ?? null;;
                        $nama_penerim       = $cuti->nama_karyawan;
                        $nama               = $user_karyawan->name;
                        $tanggal_mulai      = Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y');
                        $tanggal_selesai    = Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y');
                        $judul              = 'Pengajuan Cuti Ditolak';
                        $perihal            = 'Melalui email ini, kami menginformasikan bahwa pengajuan cuti anda ditolak oleh :';

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
                    QuickPanel::where('content_id', $id)->where('quick_type', 'cuti')->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),

                    ]);
                    $history = new HistoryApproval();
                    $history->id_approval = Uuid::uuid4();
                    $history->content_id = $cuti->id;
                    $history->tipe_approval = 'cuti';
                    $history->karyawan_id = $user_karyawan->id;
                    $history->karyawan_id_pengajuan = $cuti->karyawan_id;
                    $history->status_approval = 2;
                    $history->created_at = date('Y-m-d H:i:s');
                    $history->updated_at = date('Y-m-d H:i:s');
                    $history->save();
                }
                $cuti->save();
            }

            // $home = Menu::where('name', 'Home')->first();
            // User::where('id', Auth::user()->id)->update([
            //     'menu_active' => $home->id
            // ]);
            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan Berhasil Di-Approve',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_history_approval($id)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $data = HistoryApproval::with('Karyawan', 'Cuti.KategoriCuti', 'Izin', 'Perdin', 'KaryawanPengajuan')->where('karyawan_id', $user_karyawan->id)->get();
            // dd($data);
            return response()->json([
                'code' => 200,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approveHistoryDetail($id, Request $request)
    {
        try {
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
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            return view('users.approval.history_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'user_karyawan' => $user_karyawan
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
            return redirect()->back()->with('error', $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
        }
    }

    public function get_HistoryDetailApproval($id)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $data          = HistoryApproval::with('Karyawan', 'Cuti.user', 'Cuti.KategoriCuti', 'Cuti.KaryawanPengganti', 'Cuti.Atasan1', 'Cuti.Atasan2', 'Izin', 'Perdin')->where('karyawan_id', $user_karyawan->id)->where('id_approval', $id)->first();
            return response()->json(['code' => 200, 'data' => $data, 'user' => $user_karyawan]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    // Mulai approval ticket Ubah Hapus
    // TICKET — APPROVAL
    private function buildMenus(array $get_role): array
    {
        if (empty($get_role)) {
            return [collect(), collect(), collect()];
        }

        $main = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 1)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();

        $sub = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 0)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();

        $bar = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 2)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();

        return [$main, $sub, $bar];
    }

    public function ubahHapusIndex($id)
    {
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        $user_karyawan = $this->currentKaryawan();
        $ticket        = TicketHead::first();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $id]);
        return view('users.ticket.approval-index', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $id,
            'ticket'        => $ticket,
        ]);
    }
    public function getListTicket($id)
    {
        try {
            $approvalSubjectIds = $this->currentApprovalSubjectIds();
            $checkerTicketIds   = TicketApproval::whereIn('karyawan_id', $approvalSubjectIds)
                ->where('role', 'checker')
                ->pluck('ticket_id')
                ->map(fn($id) => (string) $id)
                ->toArray();

            $tickets = TicketHead::with([
                'ticketMenu',
                'ticketSubMenu',
                'ticketDetails.subDetails',
                'approvals',
                'approvals.dependencyRule',
                'approvals.branchSubMenu.ticketMenu',
                'parentTicket:id,nomor_ticket,ticket_menu_id,ticket_sub_menu_id',
                'rootTicket:id,nomor_ticket,ticket_menu_id,ticket_sub_menu_id',
                'childTickets:id,parent_ticket_id,nomor_ticket',
                'generatedTickets:id,generated_from_ticket_id,nomor_ticket',
                'spawnedCrossTickets:id,origin_cross_ticket_id,nomor_ticket',
                'originCrossTicket:id,nomor_ticket',
            ])
                ->where('is_submit', true)
                ->where(function ($query) use ($approvalSubjectIds, $checkerTicketIds) {
                    $query->whereIn('approve1_id', $approvalSubjectIds)
                        ->orWhereIn('approve2_id', $approvalSubjectIds)
                        ->orWhereIn('checker_id', $approvalSubjectIds);
                    if (!empty($checkerTicketIds)) {
                        $query->orWhereIn('id', $checkerTicketIds);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $result = $tickets->map(function ($ticket) use ($approvalSubjectIds, $id) {
                $isApprover1 = $ticket->approve1_id && in_array((string) $ticket->approve1_id, $approvalSubjectIds, true);
                $isApprover2 = $ticket->approve2_id && in_array((string) $ticket->approve2_id, $approvalSubjectIds, true);
                $myCheckerApprovals = $ticket->approvals->filter(function ($app) use ($approvalSubjectIds) {
                    return $app->role === 'checker' && in_array((string) $app->karyawan_id, $approvalSubjectIds, true);
                });
                $myCheckerApproval = $myCheckerApprovals->first();
                $isChecker   = ($ticket->checker_id && in_array((string) $ticket->checker_id, $approvalSubjectIds, true)) || $myCheckerApprovals->isNotEmpty();

                // Apakah ada rule generate ticket turunan milik checker ini yang gagal
                $hasFailedGenerate = $myCheckerApprovals
                    ->filter(fn($app) => !is_null($app->dependency_rule_id))
                    ->contains(fn($app) => $app->generate_status === 'FAILED');

                $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;
                $approve2Done = is_null($ticket->approve2_id) || (bool) $ticket->approve2;
                $checkerDone  = $myCheckerApprovals->isNotEmpty()
                    ? $myCheckerApprovals->every(fn($app) => in_array($app->status, ['completed', 'approved'], true))
                    : (bool) $ticket->checked;

                [$envKategori, $envName] = $this->resolveEnvironmentKategori($ticket);

                $hasParent = !empty($ticket->parent_ticket_id) || !empty($ticket->generated_from_ticket_id) || !empty($ticket->origin_cross_ticket_id);
                $parentNomor = $ticket->parentTicket?->nomor_ticket ?? ($ticket->generatedFromTicket?->nomor_ticket ?? ($ticket->originCrossTicket?->nomor_ticket ?? null));
                $childCount = ($ticket->childTickets ? $ticket->childTickets->count() : 0)
                    + ($ticket->generatedTickets ? $ticket->generatedTickets->count() : 0)
                    + ($ticket->spawnedCrossTickets ? $ticket->spawnedCrossTickets->count() : 0);
                $isRoot = empty($ticket->parent_ticket_id) && empty($ticket->generated_from_ticket_id) && empty($ticket->origin_cross_ticket_id);
                $isGroup = $hasParent || $childCount > 0 || (!empty($ticket->root_ticket_id) && $ticket->root_ticket_id !== $ticket->id);

                // Perspektif CHECKER: apakah tugas pemeriksaan saya di tiket ini lintas environment
                // (dependency rule is_cross_environment) atau masih satu sistem dengan tiketnya?
                //   null  = saya bukan checker di tiket ini
                //   same  = semua tugas pemeriksaan saya satu sistem
                //   cross = semua tugas pemeriksaan saya lintas environment
                //   mixed = campuran keduanya
                $crossCheckerRows = $myCheckerApprovals
                    ->filter(fn($app) => (bool) ($app->dependencyRule?->is_cross_environment ?? false));
                if (!$isChecker) {
                    $checkerScope = null;
                } elseif ($crossCheckerRows->isEmpty()) {
                    $checkerScope = 'same';
                } elseif ($crossCheckerRows->count() === $myCheckerApprovals->count()) {
                    $checkerScope = 'cross';
                } else {
                    $checkerScope = 'mixed';
                }
                $checkerCrossTargets = $crossCheckerRows
                    ->map(fn($app) => $app->branchSubMenu?->ticketMenu?->kategori_display)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $baseMeta = [
                    'checker_scope'         => $checkerScope,
                    'checker_cross_targets' => $checkerCrossTargets,
                    'has_parent'           => $hasParent,
                    'parent_ticket_number' => $parentNomor,
                    'parent_ticket_id'     => $ticket->parent_ticket_id ?? $ticket->generated_from_ticket_id ?? $ticket->origin_cross_ticket_id,
                    'is_root'              => $isRoot,
                    'root_ticket_number'   => $ticket->rootTicket?->nomor_ticket ?? null,
                    'child_count'          => $childCount,
                    'is_group'             => $isGroup,
                ];

                // 1. TICKET REJECTED / CANCELLED
                if ($ticket->approval_status === TicketHead::APPROVAL_REJECTED || $ticket->ticket_status === TicketHead::STATUS_CANCELLED) {
                    return array_merge([
                        'id'                   => $ticket->id,
                        'nomor_ticket'         => $ticket->nomor_ticket,
                        'status'               => 2,
                        'ticket_status'        => $ticket->ticket_status,
                        'requestor_name'       => $ticket->requestor_name,
                        'menu_name'            => $ticket->ticketMenu?->nama_menu,
                        'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $envKategori,
                        'environment_name'     => $envName,
                        'detail_count'         => $ticket->ticketDetails->count(),
                        'tanggal_ticket'       => $ticket->created_at,
                        'role'                 => 'Ditolak',
                        'page_url'             => route('ticket.lihat', [$id, $ticket->id]),
                        'has_failed_generate'  => $hasFailedGenerate,
                    ], $baseMeta);
                }

                // 2. PRIORITAS: APAKAH ADA AKSI YANG SEDANG MENUNGGU USER INI? (STATUS = 0 / MENUNGGU)

                // 3a. Giliran Approver 1
                if ($isApprover1 && !(bool) $ticket->approve1) {
                    return array_merge([
                        'id'                   => $ticket->id,
                        'nomor_ticket'         => $ticket->nomor_ticket,
                        'status'               => 0,
                        'ticket_status'        => $ticket->ticket_status,
                        'requestor_name'       => $ticket->requestor_name,
                        'menu_name'            => $ticket->ticketMenu?->nama_menu,
                        'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $envKategori,
                        'environment_name'     => $envName,
                        'detail_count'         => $ticket->ticketDetails->count(),
                        'tanggal_ticket'       => $ticket->created_at,
                        'role'                 => 'Menunggu Approve 1',
                        'page_url'             => route('approval.ticket.approve', [$id, $ticket->id]),
                        'has_failed_generate'  => $hasFailedGenerate,
                    ], $baseMeta);
                }

                // 3b. Giliran Approver 2 (Hanya jika Approver 1 sudah selesai)
                if ($isApprover2 && $approve1Done && !(bool) $ticket->approve2) {
                    return array_merge([
                        'id'                   => $ticket->id,
                        'nomor_ticket'         => $ticket->nomor_ticket,
                        'status'               => 0,
                        'ticket_status'        => $ticket->ticket_status,
                        'requestor_name'       => $ticket->requestor_name,
                        'menu_name'            => $ticket->ticketMenu?->nama_menu,
                        'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $envKategori,
                        'environment_name'     => $envName,
                        'detail_count'         => $ticket->ticketDetails->count(),
                        'tanggal_ticket'       => $ticket->created_at,
                        'role'                 => 'Menunggu Approve 2',
                        'page_url'             => route('approval.ticket.approve', [$id, $ticket->id]),
                        'has_failed_generate'  => $hasFailedGenerate,
                    ], $baseMeta);
                }

                // 3c. Giliran Checker (Hanya jika Approver 1 & Approver 2 sudah selesai)
                if ($isChecker && $approve1Done && $approve2Done && !$checkerDone) {
                    return array_merge([
                        'id'                   => $ticket->id,
                        'nomor_ticket'         => $ticket->nomor_ticket,
                        'status'               => 0,
                        'ticket_status'        => $ticket->ticket_status,
                        'requestor_name'       => $ticket->requestor_name,
                        'menu_name'            => $ticket->ticketMenu?->nama_menu,
                        'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $envKategori,
                        'environment_name'     => $envName,
                        'detail_count'         => $ticket->ticketDetails->count(),
                        'tanggal_ticket'       => $ticket->created_at,
                        'role'                 => 'Menunggu Checker',
                        'page_url'             => route('approval.ticket.checker', [$id, $ticket->id]),
                        'has_failed_generate'  => $hasFailedGenerate,
                    ], $baseMeta);
                }

                // 4. JIKA BELUM GILIRAN USER (Misal: User adalah Approver 2 atau Checker, tapi tahap sebelumnya belum selesai)
                $hasActedAsApprover1 = $isApprover1 && (bool) $ticket->approve1;
                $hasActedAsApprover2 = $isApprover2 && (bool) $ticket->approve2;
                $hasActedAsChecker   = $isChecker && $checkerDone;

                if (!$hasActedAsApprover1 && !$hasActedAsApprover2 && !$hasActedAsChecker) {
                    // User belum giliran dan belum pernah approve/check -> sembunyikan agar tidak muncul prematur
                    return null;
                }

                // 5. JIKA USER SUDAH PERNAH MELAKUKAN AKSI (STATUS = 1 / SUDAH DISETUJUI)
                $roleLabel = 'Sudah Disetujui';
                if ($hasActedAsChecker) {
                    $roleLabel = $hasFailedGenerate ? 'Sudah Diperiksa (Generate Gagal)' : 'Sudah Diperiksa';
                } elseif ($hasActedAsApprover2) {
                    $roleLabel = 'Sudah Approve 2';
                } elseif ($hasActedAsApprover1) {
                    $roleLabel = 'Sudah Approve 1';
                }

                return array_merge([
                    'id'                   => $ticket->id,
                    'nomor_ticket'         => $ticket->nomor_ticket,
                    'status'               => 1,
                    'ticket_status'        => $ticket->ticket_status,
                    'requestor_name'       => $ticket->requestor_name,
                    'menu_name'            => $ticket->ticketMenu?->nama_menu,
                    'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                    'environment_kategori' => $envKategori,
                    'environment_name'     => $envName,
                    'detail_count'         => $ticket->ticketDetails->count(),
                    'tanggal_ticket'       => $ticket->created_at,
                    'role'                 => $roleLabel,
                    'page_url'             => route('ticket.lihat', [$id, $ticket->id]),
                    'has_failed_generate'  => $hasFailedGenerate,
                ], $baseMeta);
            })
                ->filter() // drop tickets excluded above (e.g. checker items not yet approved by top-level supervisor)
                ->values();
            return response()->json([
                'user_id'     => Auth::id(),
                'karyawan_id' => $this->currentKaryawan()?->id,
                'code'        => 200,
                'tickets'     => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage() . ' line ' . $e->getLine(),
                'file'    => $e->getFile(),
            ]);
        }
    }

    public function showApproveTicket($id, $ticketId)
    {
        $menuId = $this->resolveApprovalMenuId($id);
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        $ticket = TicketHead::with([
            'ticketMenu',
            'ticketSubMenu',
            'ticketDetails.subDetails',
            'approvals.dependencyRule',
            'approvals.branchSubMenu.ticketMenu',
            'attachments',
            'parentTicket.ticketMenu',
            'parentTicket.ticketSubMenu',
            'rootTicket.ticketMenu',
            'rootTicket.ticketSubMenu',
            'generatedFromTicket.ticketMenu',
            'generatedFromTicket.ticketSubMenu',
            'originCrossTicket.ticketMenu',
            'originCrossTicket.ticketSubMenu',
        ])->findOrFail($ticketId);
        $user_karyawan = $this->currentKaryawan();
        $approvalSubjectIds = $this->currentApprovalSubjectIds();
        $isApprover1 = $ticket->approve1_id && in_array((string) $ticket->approve1_id, $approvalSubjectIds, true);
        $isApprover2 = $ticket->approve2_id && in_array((string) $ticket->approve2_id, $approvalSubjectIds, true);
        $myCheckerApproval = $ticket->approvals->first(function ($app) use ($approvalSubjectIds) {
            return $app->role === 'checker' && in_array((string) $app->karyawan_id, $approvalSubjectIds, true);
        });
        $isChecker = ($ticket->checker_id && in_array((string) $ticket->checker_id, $approvalSubjectIds, true)) || !is_null($myCheckerApproval);
        if (!$isApprover1 && !$isApprover2 && !$isChecker) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui atau memeriksa ticket ini.');
        }
        $approve1Turn = $isApprover1 && !(bool) $ticket->approve1;
        $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;
        $approve2Turn = $isApprover2 && $approve1Done && !(bool) $ticket->approve2;
        if (!$approve1Turn && !$approve2Turn && !$isChecker) {
            return redirect()->route('ticket.lihat', [$menuId, $ticketId]);
        }
        User::where('id', Auth::user()->id)->update(['menu_active' => $menuId]);
        // dd($ticket->toArray());        
        return view('users.ticket.approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $menuId,
            'ticket'        => $ticket,
        ]);
    }

    /**
     * Simpan approval atau rejection untuk Approver 1 / Approver 2.
     *
     * Behaviour:
     *  APPROVE → simpan tanda tangan + update kolom approve.
     *            Jika ada comment_text, simpan ke ticket_approval_comment
     *            dengan action_ticket = 'approve'.
     *
     *  REJECT  → reset SELURUH kolom approval pada ticket_head:
     *            approve1, approve1_id, approve1_name, approve1_signature, approve1_at,
     *            approve2, approve2_id, approve2_name, approve2_signature, approve2_at,
     *            checked, checker_id, checker_name, checker_signature, checker_at
     *            semua menjadi NULL, lalu set is_submit=false, is_draft=true.
     *            Simpan catatan ke ticket_approval_comment dengan action_ticket = 'reject'.
     */
    public function saveApproveTicket(Request $request)
    {
        $ticket = TicketHead::find($request->id_ticket);
        if (!$ticket) {
            return response()->json(['code' => 404, 'message' => 'Ticket tidak ditemukan.'], 404);
        }

        $user               = Auth::user();
        $approvalSubjectIds = $this->currentApprovalSubjectIds();
        $userKaryawan       = $this->resolveKaryawanFromUser($user);
        if (!$userKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.'], 403);
        }

        $authKaryawanId = (string) $userKaryawan->id;
        $approverName   = $userKaryawan->name ?? $user->name;
        $menuId         = $request->menu_id;
        $redirectUrl    = route('approval.ticket', $menuId);

        $isApprover1 = in_array((string) $ticket->approve1_id, $approvalSubjectIds, true);
        $isApprover2 = in_array((string) $ticket->approve2_id, $approvalSubjectIds, true);
        if (!$isApprover1 && !$isApprover2) {
            return response()->json([
                'code'    => 403,
                'message' => 'Anda tidak memiliki akses untuk menyetujui ticket ini.',
            ], 403);
        }

        $action = $request->action ?? 'approve';

        // ── APPROVE ──────────────────────────────────────────────────────────
        if ($action === 'approve') {
            $validator = Validator::make($request->all(), [
                'id_ticket'    => 'required|exists:ticket_head,id',
                'signature'    => 'required|string',
                'comment_text' => 'nullable|string|max:1000',
            ], [
                'id_ticket.required'  => 'ID Ticket tidak ditemukan, silakan muat ulang halaman.',
                'id_ticket.exists'    => 'Ticket tidak ditemukan di sistem.',
                'signature.required'  => 'Tanda tangan wajib diisi sebelum menyetujui.',
                'signature.string'    => 'Format tanda tangan tidak valid.',
                'comment_text.string' => 'Komentar harus berupa teks.',
                'comment_text.max'    => 'Komentar maksimal 1000 karakter.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code'    => 422,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;

            // Tentukan level yang sedang giliran. Approver 1 didahulukan.
            if ($isApprover1 && !$ticket->approve1) {
                $level = 'approve1';
            } elseif ($isApprover2 && !$ticket->approve2) {
                // Guard urutan: approver 2 hanya boleh setelah approver 1 selesai.
                if (!$approve1Done) {
                    return response()->json([
                        'code'    => 422,
                        'message' => 'Ticket masih menunggu persetujuan Approver 1.',
                    ], 422);
                }
                $level = 'approve2';
            } else {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Anda bukan approver untuk ticket ini atau sudah melakukan approval.',
                ], 422);
            }

            $signaturePath       = $this->saveSignature($request->signature, $level);
            $hasInternalCheckers = $this->hasInternalCheckers($ticket);

            if ($level === 'approve1') {
                // Masih ada approver 2 -> T09. Kalau tidak: checker dulu (T12), atau langsung validator (T11).
                $nextStatus = !empty($ticket->approve2_id)
                    ? TicketHead::STATUS_WAITING_APPROVAL1
                    : ($hasInternalCheckers
                        ? TicketHead::STATUS_WAITING_CHECKER
                        : TicketHead::STATUS_WAITING_VALIDATOR);
            } else {
                // Approver 2 adalah tahap terakhir sebelum checker / validator.
                $nextStatus = $hasInternalCheckers
                    ? TicketHead::STATUS_WAITING_CHECKER
                    : TicketHead::STATUS_WAITING_VALIDATOR;
            }

            $ticket->update([
                "{$level}_id"        => $authKaryawanId,
                "{$level}_name"      => $approverName,
                $level               => true,
                "{$level}_at"        => now(),
                "{$level}_signature" => $signaturePath,
                'ticket_status'      => $nextStatus,
            ]);

            ActivityLog::create([
                'user_id'          => $request->user()->id,
                'user_name'        => $request->user()->username,
                'menu_id'          => null,
                'menu_name'        => 'Approval Ticket',
                'id_table'         => $ticket->id,
                'holding_id'       => '-',
                'holding_category' => '-',
                'table'            => 'ticket_head',
                'activity'         => 'Approve Ticket',
                'description'      => 'Ticket ' . $ticket->nomor_ticket . ' berhasil disetujui',
                'keterangan'       => 'Success',
                'karir'            => '2',
            ]);

            $this->saveApprovalComment($ticket->id, $level, $userKaryawan, 'approve', $request->comment_text);

            $freshTicket = $ticket->fresh();
            $this->tryFinalizeApproval($freshTicket);

            app(TicketApprovalEmailService::class)->sendNextActionNotification(
                $freshTicket->fresh(),
                $menuId,
                'approved'
            );

            return response()->json([
                'code'         => 200,
                'message'      => 'Ticket berhasil disetujui oleh ' . ($level === 'approve1' ? 'Approver 1' : 'Approver 2') . '.',
                'redirect_url' => $redirectUrl,
            ]);
        }

        // ── REJECT (tidak berubah) ───────────────────────────────────────────
        if ($action === 'reject') {
            $validator = Validator::make($request->all(), [
                'id_ticket'    => 'required|exists:ticket_head,id',
                'comment_text' => 'nullable|string|max:1000',
            ], [
                'id_ticket.required'  => 'ID Ticket tidak ditemukan, silakan muat ulang halaman.',
                'id_ticket.exists'    => 'Ticket tidak ditemukan di sistem.',
                'comment_text.string' => 'Komentar harus berupa teks.',
                'comment_text.max'    => 'Komentar maksimal 1000 karakter.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code'    => 422,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $approvalLevel = $isApprover1 ? 'approve1' : 'approve2';
            $canReject     = ($isApprover1 && !$ticket->approve1)
                || ($isApprover2 && !$ticket->approve2);

            if (!$canReject) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Anda bukan approver untuk ticket ini atau sudah melakukan approval.',
                ], 422);
            }

            $rootId = $ticket->root_ticket_id ?: $ticket->id;
            $groupTickets = TicketHead::where(function ($query) use ($rootId) {
                $query->where('id', $rootId)->orWhere('root_ticket_id', $rootId);
            })->get();

            TicketHead::whereIn('id', $groupTickets->pluck('id'))->update([
                'approval_status' => TicketHead::APPROVAL_REJECTED,
                'ticket_status'   => TicketHead::STATUS_CANCELLED,
                'is_submit'       => false,
                'is_draft'        => false,
                'closed_at'       => now(),
            ]);

            foreach ($groupTickets as $item) {
                $this->saveApprovalComment($item->id, $approvalLevel, $userKaryawan, 'reject', $request->comment_text);
                ActivityLog::create([
                    'user_id'          => $request->user()->id,
                    'user_name'        => $request->user()->username,
                    'menu_id'          => $menuId ?? null,
                    'menu_name'        => 'Approval Ticket',
                    'id_table'         => $item->id,
                    'table'            => 'ticket_head',
                    'activity'         => 'Reject',
                    'description'      => 'Ticket ' . $item->nomor_ticket . ' ditolak oleh ' . ucfirst($approvalLevel) . ' (' . $userKaryawan->name . ')',
                    'keterangan'       => 'Success',
                    'holding_id'       => '-',
                    'holding_category' => '-',
                    'karir'            => '2',
                ]);
            }

            return response()->json([
                'code'         => 200,
                'message'      => 'Ticket berhasil ditolak oleh ' . ucfirst($approvalLevel) . '.',
                'redirect_url' => $redirectUrl,
            ]);
        }

        return response()->json(['code' => 400, 'message' => 'Action tidak valid.'], 400);
    }

    public function getApprovalInfo($menuId, $ticketId)
    {
        try {
            $ticket = TicketHead::find($ticketId);
            if (!$ticket) {
                return response()->json(['code' => 200, 'atasan1' => null, 'atasan2' => null]);
            }
            $approver1 = null;
            $approver2 = null;
            if ($ticket->approve1_id) {
                $karyawan  = $this->resolveKaryawanByTicketSubject($ticket->approve1_id);
                $approver1 = ['karyawan' => ['name' => $karyawan?->name ?? $ticket->approve1_name ?? 'Unknown']];
            }
            if ($ticket->approve2_id) {
                $karyawan  = $this->resolveKaryawanByTicketSubject($ticket->approve2_id);
                $approver2 = ['karyawan' => ['name' => $karyawan?->name ?? $ticket->approve2_name ?? 'Unknown']];
            }
            return response()->json(['code' => 200, 'atasan1' => $approver1, 'atasan2' => $approver2]);
        } catch (\Exception $e) {
            return response()->json(['code' => 200, 'atasan1' => null, 'atasan2' => null]);
        }
    }

    // TICKET — CHECKER
    public function showCheckerTicket($menuId, $ticketId, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $menuId = $this->resolveApprovalMenuId($menuId);
        $ticket = TicketHead::with([
            'ticketMenu',
            'ticketSubMenu',
            'ticketDetails.subDetails',
            'approvals.karyawan',
            'approvals.dependencyRule',
            'approvals.branchSubMenu.ticketMenu',
            'attachments',
            'parentTicket.ticketMenu',
            'parentTicket.ticketSubMenu',
            'rootTicket.ticketMenu',
            'rootTicket.ticketSubMenu',
            'childTickets.ticketMenu',
            'childTickets.ticketSubMenu',
            'generatedTickets.ticketMenu',
            'generatedTickets.ticketSubMenu',
            'spawnedCrossTickets.ticketMenu',
            'spawnedCrossTickets.ticketSubMenu',
            'originCrossTicket.ticketMenu',
            'originCrossTicket.ticketSubMenu',
        ])->find($ticketId);
        if (!$ticket) abort(404);

        $approvalSubjectIds = $this->currentApprovalSubjectIds();

        $isChecker = in_array((string) $ticket->checker_id, $approvalSubjectIds, true)
            || TicketApproval::where('ticket_id', $ticketId)
            ->where('role', 'checker')
            ->whereIn('karyawan_id', $approvalSubjectIds)
            ->exists();

        if (!$isChecker) {
            abort(403, 'Anda bukan checker ticket ini.');
        }

        $myCheckerApprovals = $ticket->approvals->filter(function ($app) use ($approvalSubjectIds) {
            return $app->role === 'checker' && in_array((string)$app->karyawan_id, $approvalSubjectIds, true);
        });

        $isCompleted = $myCheckerApprovals->isNotEmpty()
            ? $myCheckerApprovals->every(fn($app) => in_array($app->status, ['completed', 'approved'], true))
            : (bool)$ticket->checked;

        if ($isCompleted) {
            return redirect()->route('ticket.lihat', [$menuId, $ticketId]);
        }

        $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;
        $approve2Done = is_null($ticket->approve2_id) || (bool) $ticket->approve2;
        if (!$approve1Done || !$approve2Done) {
            $request->session()->flash('info_message', 'Ticket ini belum disetujui oleh atasan/supervisor, namun Anda dapat memeriksa sebagai checker.');
        }

        $currentSubmenuId = $ticket->ticket_sub_menu_id;

        $myDependencyRuleIds = TicketApproval::where('ticket_id', $ticketId)
            ->where('role', 'checker')
            ->whereIn('karyawan_id', $approvalSubjectIds)
            ->whereNotNull('dependency_rule_id')
            ->pluck('dependency_rule_id')
            ->toArray();

        $sourceKategori = (int)($ticket->ticketMenu?->kategori ?? 1);
        $forward = DB::table('ticket_dependency_rule as tdr')
            ->leftJoin('ticket_kondisi as tk', 'tdr.condition_key', '=', 'tk.code')
            ->join('ticket_sub_menu as tsm', 'tdr.target_sub_menu_id', '=', 'tsm.id')
            ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
            ->where('tdr.source_sub_menu_id', $currentSubmenuId)
            ->where(function ($q) {
                $q->where('tdr.is_active', true)->orWhereNull('tdr.is_active');
            })
            ->select(
                'tdr.id as rule_id',
                'tdr.condition_key',
                'tdr.description as rule_description',
                'tdr.target_sub_menu_id',
                'tdr.is_conditional',
                'tdr.is_cross_environment',
                'tdr.is_bidirectional',
                DB::raw("'FORWARD' as direction"),
                'tk.label_kondisi',
                'tk.code',
                'tsm.nama_sub_menu as target_submenu_name',
                'tm.nama_menu as target_menu_name',
                'tm.id as target_menu_id',
                'tm.kategori as target_menu_kategori'
            );
        // [LOGIKA REVERSE DEPENDENCY]
        // Ambil rule di mana tiket saat ini adalah TARGET dan rule diset bidirectional = true.
        // Pada alur reverse, submenu tujuan dibalik (source_sub_menu_id menjadi target_sub_menu_id).
        $reverse = DB::table('ticket_dependency_rule as tdr')
            ->leftJoin('ticket_kondisi as tk', 'tdr.condition_key', '=', 'tk.code')
            ->join('ticket_sub_menu as tsm', 'tdr.source_sub_menu_id', '=', 'tsm.id')
            ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
            ->where('tdr.target_sub_menu_id', $currentSubmenuId)
            ->where(function ($q) {
                $q->where('tdr.is_active', true)->orWhereNull('tdr.is_active');
            })
            ->where('tdr.is_bidirectional', true)
            ->select(
                'tdr.id as rule_id',
                'tdr.condition_key',
                'tdr.description as rule_description',
                'tdr.source_sub_menu_id as target_sub_menu_id',
                'tdr.is_conditional',
                'tdr.is_cross_environment',
                'tdr.is_bidirectional',
                DB::raw("'REVERSE' as direction"),
                'tk.label_kondisi',
                'tk.code',
                'tsm.nama_sub_menu as target_submenu_name',
                'tm.nama_menu as target_menu_name',
                'tm.id as target_menu_id',
                'tm.kategori as target_menu_kategori'
            );
        $allRules = $forward
            ->unionAll($reverse)
            ->get();
        $envNames = [
            1 => 'Epicor ERP SPS',
            2 => 'Accurate Online SP',
            3 => 'Accurate Online AJA',
        ];
        $rules = $allRules
            ->filter(function ($rule) use ($myDependencyRuleIds) {
                return in_array((string) $rule->rule_id, $myDependencyRuleIds, true);
            })
            ->unique('rule_id')
            ->map(function ($rule) use ($envNames, $sourceKategori) {
                $targetKat = (int)($rule->target_menu_kategori ?? 1);
                $rule->source_kategori = $sourceKategori;
                $rule->source_kategori_name = $envNames[$sourceKategori] ?? 'Epicor ERP SPS';
                $rule->target_kategori = $targetKat;
                $rule->target_kategori_name = $envNames[$targetKat] ?? 'Epicor ERP SPS';
                $rule->is_cross_environment = (bool)($rule->is_cross_environment ?? ($sourceKategori !== $targetKat));

                if ($rule->direction === 'REVERSE') {
                    if ($rule->is_cross_environment) {
                        $envAliases = [
                            'Accurate Online AJA',
                            'Accurate Online SP',
                            'Epicor ERP SPS',
                            'Accurate AJA',
                            'Accurate SP',
                            'Epicor ERP',
                            'Epicor SPS',
                        ];
                        $desc = !empty($rule->rule_description) ? $rule->rule_description : ('Apakah perlu merevisi transaksi di ' . $rule->target_kategori_name . '?');
                        $replaced = false;
                        foreach ($envAliases as $alias) {
                            if (stripos($desc, $alias) !== false) {
                                $desc = str_ireplace($alias, $rule->target_kategori_name, $desc);
                                $replaced = true;
                                break;
                            }
                        }
                        if (!$replaced) {
                            $desc = 'Apakah perlu merevisi transaksi di ' . $rule->target_kategori_name . '?';
                        }
                        $rule->rule_description = !str_starts_with($desc, '[Revisi Balik]') ? ('[Revisi Balik] ' . $desc) : $desc;
                    } elseif (!empty($rule->rule_description) && !str_starts_with($rule->rule_description, '[Revisi Balik]')) {
                        $rule->rule_description = '[Revisi Balik] ' . $rule->rule_description;
                    }
                }
                return $rule;
            })
            ->values();
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menuId]);
        $myApprovals = TicketApproval::where('ticket_id', $ticketId)
            ->where('role', 'checker')
            ->whereIn('karyawan_id', $approvalSubjectIds)
            ->whereNotNull('dependency_rule_id')
            ->get()
            ->keyBy('dependency_rule_id');

        $ticketChainService = app(\App\Services\TicketChainService::class);
        $chainInfo          = $ticketChainService->resolveChainInfo($ticket);

        return view('users.ticket.checker', [
            'ticket'        => $ticket,
            'rules'         => $rules,
            'myApprovals'   => $myApprovals,
            'user_karyawan' => $user_karyawan,
            'menu_id'       => $menuId,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'chainInfo'     => $chainInfo,
        ]);
    }

    /**
     * @param  TicketHead $ticket The current ticket being checked
     * @param  array<string,string> $conditions Map of rule_id => condition_value
     * @param  array<string> $myDependencyRuleIds The checker's assigned dependency rules
     * @return array<string,string> Map of target rule_id => rule_id (for approved targets)
     */
    private function resolveMyTrueTargets(TicketHead $ticket, array $conditions, array $myDependencyRuleIds): array
    {
        $targets = [];
        foreach ($conditions as $ruleId => $value) {
            if (!in_array((string) $ruleId, $myDependencyRuleIds, true)) {
                continue;
            }
            $rule = TicketDependencyRule::find($ruleId);
            if (!$rule) {
                continue;
            }
            // [PENENTUAN ARAH TARGET EFEKTIF]
            // Jika rule bidirectional dan posisi tiket adalah target -> alur reverse -> arahkan ke source_sub_menu_id.
            $matchedInReverse = $rule->is_bidirectional
                && (string) $rule->target_sub_menu_id === (string) $ticket->ticket_sub_menu_id;
            $effectiveTargetSubmenuId = $matchedInReverse
                ? $rule->source_sub_menu_id
                : $rule->target_sub_menu_id;

            $isTrue = (bool)(int) $value;
            TicketConditionValue::create([
                'id'              => Uuid::uuid4(),
                'ticket_id'       => $ticket->id,
                'rule_id'         => $ruleId,  // Store the rule_id
                'condition_key'   => $rule->condition_key,
                'condition_value' => $isTrue,
                'checked_by'      => Auth::id(),
                'checked_at'      => now(),
            ]);
            if (!$isTrue) {
                continue;
            }
            $targets[(string) $ruleId] = $ruleId;
        }
        return $targets;
    }

    public function saveCheckerTicket(Request $request, $ticketId)
    {
        $ticket = TicketHead::find($ticketId);
        if (!$ticket) {
            return response()->json(['code' => 404, 'message' => 'Ticket tidak ditemukan.']);
        }
        $user               = Auth::user();
        $approvalSubjectIds = $this->currentApprovalSubjectIds();
        $userKaryawan       = $this->resolveKaryawanFromUser($user);
        if (!$userKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.'], 403);
        }
        $isChecker = in_array((string) $ticket->checker_id, $approvalSubjectIds, true)
            || TicketApproval::where('ticket_id', $ticketId)
            ->where('role', 'checker')
            ->whereIn('karyawan_id', $approvalSubjectIds)
            ->exists();

        if (!$isChecker) {
            return response()->json(['code' => 403, 'message' => 'Anda bukan checker ticket ini.']);
        }
        $myApproval = TicketApproval::where('ticket_id', $ticketId)
            ->where('role', 'checker')
            ->whereIn('karyawan_id', $approvalSubjectIds)
            ->first();

        if ($myApproval && in_array($myApproval->status, ['completed', 'approved'], true)) {
            return response()->json(['code' => 422, 'message' => 'Anda sudah menyelesaikan pemeriksaan ticket ini.']);
        }
        $validator = Validator::make($request->all(), [
            'signature'    => 'required|string',
            'menu_id'      => 'required',
            'comment_text' => 'nullable|string|max:1000',
        ], [
            'signature.required'    => 'Tanda tangan wajib diisi sebelum menyelesaikan pemeriksaan.',
            'signature.string'      => 'Format tanda tangan tidak valid.',
            'menu_id.required'      => 'ID Menu tidak ditemukan, silakan muat ulang halaman.',
            'comment_text.string'   => 'Komentar harus berupa teks.',
            'comment_text.max'      => 'Komentar maksimal 1000 karakter.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ]);
        }
        try {
            $menuId         = $request->menu_id;
            $conditions     = $request->conditions ?? [];
            $selectedDetails = $request->selected_details ?? [];
            $copyFullData   = $request->copy_full_data ?? [];
            $comment        = $request->comment_text;

            // [PEMBATALAN PER BARIS] Terapkan centang "tidak perlu diproses" dari checker
            // SEBELUM generate ticket turunan, supaya baris batal tidak ikut terbawa.
            // [PEMBATALAN PER BARIS] Terapkan centang "tidak perlu diproses" dari checker
            // SEBELUM generate ticket turunan, supaya baris batal tidak ikut terbawa.
            $lineCancel = $this->applyLineCancellations(
                $ticket,
                (array) $request->input('line_cancel', []),
                'checker',
                $userKaryawan
            );
            if ($lineCancel['error']) {
                return response()->json(['code' => 422, 'message' => $lineCancel['error']]);
            }
            // Transaksi yang SELURUH barisnya batal tidak boleh diteruskan ke ticket turunan.
            if (!empty($lineCancel['fully_cancelled_detail_ids']) && is_array($selectedDetails)) {
                foreach ($selectedDetails as $ruleId => $detailIds) {
                    $selectedDetails[$ruleId] = array_values(array_diff(
                        (array) $detailIds,
                        $lineCancel['fully_cancelled_detail_ids']
                    ));
                }
            }
            // Cek apakah SEMUA baris ticket kini batal (termasuk yang sudah batal sebelumnya)
            $ticket->unsetRelation('ticketDetails');
            $allLinesCancelled = $this->isTicketFullyLineCancelled($ticket);

            if ($allLinesCancelled) {
                $selectedDetails = [];
                $note = 'DIBATALKAN SEMUA oleh checker (' . $userKaryawan->name . '): seluruh baris ditandai tidak perlu diproses.';
                $comment = trim($comment ?? '') !== '' ? $note . ' — ' . trim($comment) : $note;
            }
            $signaturePath  = $this->saveSignature($request->signature, 'checker');
            $checkerService = app(\App\Services\CheckerService::class);
            $outcome        = $checkerService->processCheckerSubmission(
                $ticket,
                $request->user(),
                $userKaryawan,
                $approvalSubjectIds,
                $conditions,
                $signaturePath,
                $comment,
                $menuId,
                $selectedDetails,
                $copyFullData,
                $allLinesCancelled
            );
            $generatedNote = $outcome['generatedCount'] > 0
                ? ' ' . $outcome['generatedCount'] . ' draft ticket baru berhasil dibuat atas nama Anda (' . $userKaryawan->name . ').'
                : '';

            $failedNote = $outcome['failedCount'] > 0
                ? ' ' . $outcome['failedCount'] . ' draft ticket gagal dibuat.'
                : '';
            if ($lineCancel['cancelled_count'] > 0) {
                $generatedNote .= ' ' . $lineCancel['cancelled_count'] . ' baris ditandai tidak perlu diproses.';
            }
            if (!empty($outcome['pendingDraftQueue'])) {
                $queue = $outcome['pendingDraftQueue'];
                $first = array_shift($queue);
                session(['ticket_generated_queue' => $queue]);
                return response()->json([
                    'code'         => 200,
                    'message'      => 'Pemeriksaan berhasil disimpan.' . $generatedNote . $failedNote . ' Silakan lengkapi detail ticket draft yang baru terbentuk.',
                    'redirect_url' => route('ticket.tambah.generated.draft', [
                        'draftTicketId' => $first['draft_ticket_id'],
                        'menuId'        => $first['menu_id'],
                    ]),
                ]);
            }
            if ($outcome['pendingCheckers']) {
                return response()->json([
                    'code'         => 200,
                    'message'      => 'Pemeriksaan Anda berhasil disimpan.' . $generatedNote . $failedNote . ' Menunggu checker lainnya.',
                    'redirect_url' => route('approval.ticket', $menuId),
                ]);
            }
            app(TicketApprovalEmailService::class)->sendNextActionNotification(
                $ticket->fresh(),
                $menuId,
                'approved'
            );
            return response()->json([
                'code'         => 200,
                'message'      => 'Pengecekan berhasil.' . $generatedNote . $failedNote . ' Ticket siap untuk divalidasi.',
                'redirect_url' => route('approval.ticket', $menuId),
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage() . ' line ' . $e->getLine()]);
        }
    }

    public function retryGenerateApproval(Request $request, $approvalId)
    {
        $userKaryawan = $this->currentKaryawan();
        if (!$userKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.'], 403);
        }
        $approval = TicketApproval::find($approvalId);
        if (!$approval || $approval->role !== 'checker') {
            return response()->json(['code' => 404, 'message' => 'Approval tidak ditemukan.'], 404);
        }
        $approvalSubjectIds = $this->currentApprovalSubjectIds();
        if (!in_array((string) $approval->karyawan_id, $approvalSubjectIds, true)) {
            return response()->json(['code' => 403, 'message' => 'Anda tidak memiliki akses untuk generate ticket ini.'], 403);
        }
        if (!$approval->dependency_rule_id) {
            return response()->json(['code' => 422, 'message' => 'Approval ini tidak terdaftar ke dependency rule.'], 422);
        }
        $menuId = $request->input('menu_id');
        try {
            $service = app(TicketGenerationService::class);
            $result  = $service->generateFromApproval($approval, $menuId);
            if ($result['status'] === TicketGenerationService::AWAITING_DETAILS) {
                return response()->json([
                    'code'         => 200,
                    'status'       => $result['status'],
                    'message'      => $result['message'],
                    'redirect_url' => route('ticket.tambah.generated', [
                        'menuId'           => $result['redirect']['menu_id'],
                        'parentTicketId'   => $result['redirect']['parent_ticket_id'],
                        'subMenuId'        => $result['redirect']['submenu_id'],
                        'approvalId'       => $result['redirect']['approval_id'],
                        'excludeSubmenuId' => $result['redirect']['exclude_submenu_id'] ?? null,
                    ]),
                ]);
            }
            $statusCode = ($result['status'] === TicketGenerationService::SUCCESS) ? 200 : 422;
            return response()->json([
                'code'    => $statusCode,
                'status'  => $result['status'],
                'message' => $result['message'],
                'ticket'  => $result['ticket'] ? [
                    'id'            => $result['ticket']->id,
                    'nomor_ticket'  => $result['ticket']->nomor_ticket,
                    'page_url'      => route('ticket.lihat', [$menuId, $result['ticket']->id]),
                ] : null,
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    // TICKET — VALIDATOR
    public function approvalMenu($menuId)
    {
        $user     = Auth::user();
        $karyawan = $this->resolveKaryawanFromUser($user);
        if (!$karyawan) abort(403, 'Data karyawan user login tidak ditemukan.');
        $isValidator = $this->isCurrentUserValidator();
        $isPic = $this->isActivePic($karyawan);
        $isAssignor = $this->isCurrentUserAssignor();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus(
            RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray()
        );
        User::where('id', Auth::id())->update(['menu_active' => $menuId]);
        return view('users.ticket.approval-hub', [
            'menu_id'       => $menuId,
            'user_karyawan' => $karyawan,
            'is_validator'  => $isValidator,
            'is_assignor'   => $isAssignor,
            'is_pic'        => $isPic,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
        ]);
    }

    public function validatorIndex($menuId)
    {
        $menuId = $this->resolveApprovalMenuId($menuId);
        if (!$this->isCurrentUserValidator()) {
            abort(403, 'Anda tidak memiliki akses validator.');
        }

        $user_karyawan = $this->currentKaryawan();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus(
            RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray()
        );
        User::where('id', Auth::id())->update(['menu_active' => $menuId]);

        return view('users.ticket.validator', [
            'menu_id'       => $menuId,
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
        ]);
    }

    public function getListValidatorTicket($menuId)
    {
        try {
            $menuId = $this->resolveApprovalMenuId($menuId);
            $karyawan = $this->currentKaryawan();
            if (!$karyawan) {
                return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.']);
            }
            $approvalSubjectIds = $this->currentApprovalSubjectIds();
            $validatorRecord = TicketApprovalValidator::whereIn('karyawan_id', $approvalSubjectIds)
                ->where('is_active', true)->get();
            if ($validatorRecord->isEmpty()) {
                return response()->json(['code' => 403, 'message' => 'Anda bukan validator.']);
            }
            $environments       = $validatorRecord->pluck('environment')->filter()->unique()
                ->map(fn($e) => (string) $e)->values();
            $hasGlobalScope     = $validatorRecord->whereNull('environment')->isNotEmpty();

            $targetEnv  = null;
            $filterEnv  = request()->query('environment') ?? request()->query('env');
            if ($filterEnv !== null && $filterEnv !== '' && $filterEnv !== 'all') {
                $targetEnv = match (strtolower((string) $filterEnv)) {
                    '1', 'epicor', 'epic' => '1',
                    '2', 'aol-sp', 'aol_sp', 'sp' => '2',
                    '3', 'aol-aja', 'aol_aja', 'aja' => '3',
                    default => null,
                };
                if ($targetEnv !== null) {
                    if ($hasGlobalScope || $environments->contains($targetEnv)) {
                        $environments = collect([$targetEnv]);
                        $hasGlobalScope = false;
                    } else {
                        // User does not have access to this environment
                        return response()->json(['code' => 200, 'tickets' => []]);
                    }
                }
            }
            $tickets = TicketHead::with(['ticketMenu', 'ticketSubMenu', 'ticketDetails', 'rootTicket', 'checkers', 'comments'])
                ->where(function ($q) use ($approvalSubjectIds) {
                    $q->where(function ($sub) {
                        $sub->where('approval_status', TicketHead::APPROVAL_APPROVED)
                            ->where(function ($x) {
                                $x->where('validator', false)->orWhereNull('validator');
                            })
                            ->whereNull('validator_id');
                    })
                        ->orWhereIn('validator_id', $approvalSubjectIds)
                        ->orWhereHas('comments', function ($c) use ($approvalSubjectIds) {
                            $c->where('approval_level', 'validator')
                                ->where('action_ticket', 'reject')
                                ->whereIn('karyawan_id', $approvalSubjectIds);
                        });
                })
                ->where(function ($q) use ($environments, $hasGlobalScope) {
                    if ($hasGlobalScope) return;
                    $q->whereExists(function ($sub) use ($environments) {
                        $sub->selectRaw('1')->from('ticket_menu')
                            ->whereRaw('ticket_head.ticket_menu_id::text = ticket_menu.id')
                            ->whereIn('kategori', $environments->toArray());
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function ($t) use ($approvalSubjectIds, $targetEnv) {
                    if ($targetEnv !== null) {
                        [$envKat] = $this->resolveEnvironmentKategori($t);
                        if ((string) $envKat !== (string) $targetEnv) {
                            return false;
                        }
                    }
                    if (in_array((string) $t->validator_id, $approvalSubjectIds, true)) {
                        return true;
                    }
                    if ($t->comments->where('approval_level', 'validator')->where('action_ticket', 'reject')->whereIn('karyawan_id', $approvalSubjectIds)->isNotEmpty()) {
                        return true;
                    }
                    return app(TicketApprovalEmailService::class)
                        ->isChainFullyApprovedAndChecked($t);
                });
            $result = $tickets->map(function ($t) use ($menuId, $approvalSubjectIds) {
                $isValidatorRejected = (in_array((string) $t->validator_id, $approvalSubjectIds, true) && $t->approval_status === TicketHead::APPROVAL_REJECTED)
                    || $t->comments->where('approval_level', 'validator')->where('action_ticket', 'reject')->whereIn('karyawan_id', $approvalSubjectIds)->isNotEmpty();

                if ($t->validator && in_array((string) $t->validator_id, $approvalSubjectIds, true)) {
                    $validatorStatus = 'approved';
                } elseif ($isValidatorRejected) {
                    $validatorStatus = 'rejected';
                } elseif ($t->ticket_status === TicketHead::STATUS_PENDING_EXTERNAL) {
                    $validatorStatus = 'on_hold';
                } else {
                    $validatorStatus = 'pending';
                }
                [$envKategori, $envName] = $this->resolveEnvironmentKategori($t);
                return [
                    'id'                   => $t->id,
                    'nomor_ticket'         => $t->nomor_ticket,
                    'requestor_name'       => $t->requestor_name,
                    'menu_name'            => $t->ticketMenu?->nama_menu,
                    'submenu_name'         => $t->ticketSubMenu?->nama_sub_menu,
                    'environment'          => $envKategori,
                    'environment_kategori' => $envKategori,
                    'environment_name'     => $envName,
                    'detail_count'         => $t->ticketDetails->count(),
                    'tanggal_ticket'       => $t->created_at,
                    'approval_status'      => $t->approval_status,
                    'ticket_status'        => $t->ticket_status,
                    'validator_status'     => $validatorStatus,
                    'pending_reason'       => $t->pending_reason,
                    'pending_started_at'   => $t->pending_started_at,
                    'approve1'             => (bool) $t->approve1,
                    'approve2'             => (bool) $t->approve2,
                    'checked'              => (bool) $t->checked,
                    'is_validated'         => (bool) $t->validator,
                    'root_ticket_id'       => $t->root_ticket_id,
                    'root_nomor'           => $t->rootTicket?->nomor_ticket ?? $t->nomor_ticket,
                    'page_url'             => route('approval.ticket.validator.show', [$menuId, $t->id]),
                ];
            })->values();
            return response()->json(['code' => 200, 'tickets' => $result]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function showValidatorTicket($menuId, $ticketId)
    {
        $menuId = $this->resolveApprovalMenuId($menuId);
        $ticket = TicketHead::with([
            'ticketMenu',
            'ticketSubMenu',
            'ticketDetails.subDetails',
            'approvals.dependencyRule',
            'approvals.branchSubMenu.ticketMenu',
            'comments.karyawan',
            'attachments',
            'parentTicket.ticketMenu',
            'parentTicket.ticketSubMenu',
            'rootTicket.ticketMenu',
            'rootTicket.ticketSubMenu',
            'generatedFromTicket.ticketMenu',
            'generatedFromTicket.ticketSubMenu',
            'originCrossTicket.ticketMenu',
            'originCrossTicket.ticketSubMenu',
        ])->findOrFail($ticketId);
        if (!$this->isUserValidator($ticket)) abort(403, 'Anda bukan validator ticket ini.');
        if ($ticket->validator && !in_array((string) $ticket->validator_id, $this->currentApprovalSubjectIds(), true)) {
            abort(403, 'Ticket sudah divalidasi oleh ' . $ticket->validator_name);
        }
        $user_karyawan = $this->currentKaryawan();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus(
            RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray()
        );
        User::where('id', Auth::id())->update(['menu_active' => $menuId]);

        $rootId = $ticket->root_ticket_id ?: $ticket->id;
        $groupTickets = TicketHead::where(function ($q) use ($rootId) {
            $q->where('id', $rootId)->orWhere('root_ticket_id', $rootId);
        })->get();

        $groupPendingTickets = $groupTickets->filter(function ($t) {
            return (empty($t->validator) || !$t->validator)
                && empty($t->validator_id)
                && $t->approval_status === TicketHead::APPROVAL_APPROVED;
        });
        $groupPendingCount = $groupPendingTickets->count();

        $rootTicketNomor = $ticket->rootTicket?->nomor_ticket
            ?? $ticket->parentTicket?->nomor_ticket
            ?? $ticket->nomor_ticket;

        $canValidate = (empty($ticket->validator) || !$ticket->validator)
            && empty($ticket->validator_id)
            && $ticket->approval_status === TicketHead::APPROVAL_APPROVED;

        return view('users.ticket.validator-show', [
            'ticket'              => $ticket,
            'user_karyawan'       => $user_karyawan,
            'menu_id'             => $menuId,
            'all_menu_main'       => $all_menu_main,
            'all_menus'           => $all_menus,
            'all_menu_bar'        => $all_menu_bar,
            'root_ticket_id'      => $rootId,
            'root_ticket_nomor'   => $rootTicketNomor,
            'group_pending_count' => $groupPendingCount,
            'group_tickets_count' => $groupTickets->count(),
            'can_validate'        => $canValidate,
        ]);
    }

    public function saveValidatorTicket(Request $request, $ticketId)
    {
        $ticket = TicketHead::find($ticketId);
        if (!$ticket) {
            return response()->json(['code' => 404, 'message' => 'Ticket tidak ditemukan.']);
        }
        if (!$this->isUserValidator($ticket)) {
            return response()->json(['code' => 403, 'message' => 'Anda bukan validator.']);
        }
        $approvalSubjectIds = $this->currentApprovalSubjectIds();
        if ($ticket->validator && !in_array((string) $ticket->validator_id, $approvalSubjectIds, true)) {
            return response()->json(['code' => 422, 'message' => 'Ticket sudah divalidasi oleh ' . $ticket->validator_name]);
        }
        $validator = Validator::make($request->all(), [
            'signature'      => $request->action === 'pending' ? 'nullable|string' : 'required|string',
            'menu_id'        => 'required',
            'action'         => 'required|in:validate,reject,pending',
            'comment_text'   => 'nullable|string|max:1000',
            'alasan_pending' => $request->action === 'pending' ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'signature.required'      => 'Tanda tangan wajib diisi sebelum memvalidasi.',
            'signature.string'        => 'Format tanda tangan tidak valid.',
            'menu_id.required'        => 'ID Menu tidak ditemukan, silakan muat ulang halaman.',
            'action.required'         => 'Pilih aksi validasi, tolak, atau pending terlebih dahulu.',
            'action.in'               => 'Aksi yang dipilih tidak valid. Pilih "Validasi", "Tolak", atau "Pending".',
            'comment_text.string'     => 'Komentar harus berupa teks.',
            'comment_text.max'        => 'Komentar maksimal 1000 karakter.',
            'alasan_pending.required' => 'Alasan pending wajib diisi.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ]);
        }
        $userKaryawan = $this->currentKaryawan();
        if (!$userKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.']);
        }
        $authKaryawanId = (string) $userKaryawan->id;
        $menuId         = $request->menu_id;
        $redirectUrl    = route('approval.ticket.validator.index', $menuId);
        try {
            if ($request->action === 'pending') {
                $pendingReason = !empty(trim($request->alasan_pending ?? ''))
                    ? trim($request->alasan_pending)
                    : trim($request->comment_text);

                DB::transaction(function () use ($ticket, $pendingReason, $userKaryawan, $menuId) {
                    $fresh = TicketHead::lockForUpdate()->findOrFail($ticket->id);
                    $fresh->update([
                        'ticket_status'      => TicketHead::STATUS_PENDING_EXTERNAL,
                        'pending_reason'     => $pendingReason,
                        'pending_party'      => 'requestor',
                        'pending_started_at' => now(),
                    ]);

                    ActivityLog::create([
                        'user_id'          => Auth::id(),
                        'user_name'        => Auth::user()->username,
                        'menu_id'          => $menuId,
                        'menu_name'        => 'Validator Ticket',
                        'id_table'         => $fresh->id ?? '-',
                        'holding_id'       => '-',
                        'holding_category' => '-',
                        'table'            => 'ticket_head',
                        'activity'         => 'Pending',
                        'description'      => 'Mem-pending validasi tiket : ' . $fresh->nomor_ticket . ' (alasan: ' . $pendingReason . ')',
                        'keterangan'       => 'Success',
                        'karir'            => '2'
                    ]);

                    $this->saveApprovalComment(
                        $fresh->id,
                        'validator',
                        $userKaryawan,
                        'feedback',
                        'Ticket dipending validator: ' . $pendingReason
                    );
                });

                return response()->json([
                    'code'         => 200,
                    'message'      => 'Ticket berhasil dipending.',
                    'redirect_url' => $redirectUrl,
                ]);
            }

            if (
                $request->action === 'validate'
                && !app(TicketApprovalEmailService::class)->isChainFullyApprovedAndChecked($ticket)
            ) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Group ticket masih menunggu proses ticket lainnya selesai.',
                ], 422);
            }
            $hasPendingCheckers = TicketApproval::where('ticket_id', $ticket->id)
                ->where('role', 'checker')
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->whereNull('dependency_rule_id')
                        ->orWhereHas('dependencyRule', function ($q2) {
                            $q2->where('is_cross_environment', false);
                        });
                })
                ->exists();
            if ($hasPendingCheckers) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Ticket belum dapat divalidasi karena masih ada checker yang belum menyelesaikan pemeriksaan.',
                ], 422);
            }
            DB::transaction(function () use ($ticket, $request, $authKaryawanId, $approvalSubjectIds, $userKaryawan) {
                $fresh = TicketHead::lockForUpdate()->findOrFail($ticket->id);
                if ($fresh->validator && !in_array((string) $fresh->validator_id, $approvalSubjectIds, true)) {
                    throw new \Exception('Ticket sudah divalidasi oleh ' . $fresh->validator_name);
                }
                $signaturePath = $this->saveSignature($request->signature, 'validator');
                $isValidate    = $request->action === 'validate';
                if ($isValidate) {
                    $fresh->update([
                        'validator'           => true,
                        'validator_id'        => $authKaryawanId,
                        'validator_name'      => $userKaryawan->name,
                        'validator_at'        => now(),
                        'validator_signature' => $signaturePath,
                        'approval_status'     => TicketHead::APPROVAL_APPROVED,
                        'execution_status'    => TicketHead::EXEC_READY,
                        'ticket_status'       => TicketHead::STATUS_WAITING_ASSIGNER,
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => Auth::user()->username,
                        'menu_id' => $menuId,
                        'menu_name' => 'Validator Ticket',
                        'id_table' => $ticket->id ?? '-',
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'ticket_head',
                        'activity' => 'update',
                        'description' => 'Memvalidasi pengajuan ticket : ' . $ticket->nomor_ticket,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                } else {
                    $rootId = $fresh->root_ticket_id ?: $fresh->id;
                    $groupTickets = TicketHead::where(function ($query) use ($rootId) {
                        $query->where('id', $rootId)
                            ->orWhere('root_ticket_id', $rootId);
                    })->get();

                    TicketHead::whereIn('id', $groupTickets->pluck('id'))->update([
                        'validator'           => false,
                        'validator_id'        => $authKaryawanId,
                        'validator_name'      => $userKaryawan->name,
                        'validator_at'        => now(),
                        'validator_signature' => $signaturePath,
                        'approval_status'     => TicketHead::APPROVAL_REJECTED,
                        'execution_status'    => TicketHead::EXEC_WAITING,
                        'ticket_status'       => TicketHead::STATUS_CANCELLED,
                        'is_submit'           => false,
                        'is_draft'            => false,
                        'closed_at'           => now(),
                    ]);

                    foreach ($groupTickets as $item) {
                        $this->saveApprovalComment(
                            $item->id,
                            'validator',
                            $userKaryawan,
                            'reject',
                            $request->comment_text
                        );

                        ActivityLog::create([
                            'user_id'          => $request->user()->id,
                            'user_name'        => $user_name->username ?? Auth::user()->username,
                            'menu_id'          => $menuId,
                            'menu_name'        => 'Validator Ticket',
                            'id_table'         => $item->id,
                            'holding_id'       => '-',
                            'holding_category' => '-',
                            'table'            => 'ticket_head',
                            'activity'         => 'Reject',
                            'description'      => 'Ticket ' . $item->nomor_ticket . ' ditolak oleh Validator (' . $userKaryawan->name . ')',
                            'keterangan'       => 'Success',
                            'karir'            => '2'
                        ]);
                    }
                }

                if ($isValidate) {
                    $this->saveApprovalComment(
                        $fresh->id,
                        'validator',
                        $userKaryawan,
                        'approve',
                        $request->comment_text
                    );
                }
            });
            if ($request->action === 'validate') {
                app(TicketApprovalEmailService::class)->sendNextActionNotification(
                    $ticket->fresh(),
                    $menuId,
                    'validated'
                );
            }
            return response()->json([
                'code'         => 200,
                'message'      => $request->action === 'validate' ? 'Ticket berhasil divalidasi.' : 'Ticket berhasil ditolak.',
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function saveValidatorGroupTicket(Request $request, $menuId)
    {
        $validator = Validator::make($request->all(), [
            'root_ticket_id' => 'required',
            'action'         => 'required|in:validate,reject,pending',
            'signature'      => $request->action === 'pending' ? 'nullable|string' : 'required|string',
            'comment_text'   => 'nullable|string|max:1000',
            'alasan_reject'  => $request->action === 'reject'
                ? 'required|string|max:1000'
                : 'nullable',
            'alasan_pending' => $request->action === 'pending'
                ? 'required|string|max:1000'
                : 'nullable',
        ], [
            'root_ticket_id.required' => 'Ticket induk tidak ditemukan, silakan muat ulang halaman.',
            'action.required'         => 'Pilih aksi validasi, tolak, atau pending terlebih dahulu.',
            'action.in'               => 'Aksi yang dipilih tidak valid. Pilih "Validasi", "Tolak", atau "Pending".',
            'signature.required'      => 'Tanda tangan wajib diisi sebelum memvalidasi atau menolak.',
            'signature.string'        => 'Format tanda tangan tidak valid.',
            'comment_text.string'     => 'Komentar harus berupa teks.',
            'comment_text.max'        => 'Komentar maksimal 1000 karakter.',
            'alasan_reject.required'  => 'Alasan penolakan wajib diisi ketika memilih tindakan Tolak.',
            'alasan_reject.string'    => 'Alasan penolakan harus berupa teks.',
            'alasan_reject.max'       => 'Alasan penolakan maksimal 1000 karakter.',
            'alasan_pending.required' => 'Alasan pending wajib diisi ketika memilih tindakan Pending.',
            'alasan_pending.string'   => 'Alasan pending harus berupa teks.',
            'alasan_pending.max'      => 'Alasan pending maksimal 1000 karakter.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ]);
        }
        if (!$this->isCurrentUserValidator()) {
            return response()->json(['code' => 403, 'message' => 'Anda bukan validator.']);
        }
        $userKaryawan = $this->currentKaryawan();
        if (!$userKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.']);
        }
        $authKaryawanId = (string) $userKaryawan->id;
        $rootId         = $request->root_ticket_id;
        $tickets = collect();
        try {
            DB::transaction(function () use ($request, $authKaryawanId, $userKaryawan, $rootId, &$tickets) {
                $tickets = TicketHead::lockForUpdate()
                    ->where(function ($q) use ($rootId) {
                        $q->where('id', $rootId)->orWhere('root_ticket_id', $rootId);
                    })
                    ->where('approval_status', TicketHead::APPROVAL_APPROVED)
                    ->where(function ($q) {
                        $q->where('validator', false)->orWhereNull('validator');
                    })
                    ->whereNull('validator_id')
                    ->get();
                if ($tickets->isEmpty()) {
                    throw new \Exception('Tidak ada ticket yang dapat divalidasi dalam grup ini.');
                }

                $groupTicket = $tickets->first();
                $groupNomorTicket = $groupTicket->rootTicket?->nomor_ticket
                    ?? $groupTicket->parentTicket?->nomor_ticket
                    ?? $groupTicket->nomor_ticket
                    ?? $rootId;

                if ($request->action === 'pending') {
                    $reasonPending = !empty(trim($request->alasan_pending ?? ''))
                        ? trim($request->alasan_pending)
                        : trim($request->comment_text ?? 'Menunggu kelengkapan lampiran');

                    TicketHead::whereIn('id', $tickets->pluck('id'))->update([
                        'ticket_status'      => TicketHead::STATUS_PENDING_EXTERNAL,
                        'pending_reason'     => $reasonPending,
                        'pending_party'      => 'requestor',
                        'pending_started_at' => now(),
                    ]);

                    ActivityLog::create([
                        'user_id'          => $request->user()->id,
                        'user_name'        => Auth::user()->username,
                        'id_table'         => $request->root_ticket_id ?? '-',
                        'holding_id'       => '-',
                        'holding_category' => '-',
                        'menu_name'        => 'Validasi Ticket',
                        'table'            => 'ticket_head',
                        'activity'         => 'Pending',
                        'description'      => 'Mem-pending validasi tiket grup ' . $groupNomorTicket . ' (menunggu lampiran): ' . $reasonPending,
                        'keterangan'       => 'success',
                        'karir'            => '2',
                    ]);

                    foreach ($tickets as $ticket) {
                        $this->saveApprovalComment(
                            $ticket->id,
                            'validator',
                            $userKaryawan,
                            'feedback',
                            'Ticket dipending validator (menunggu kelengkapan lampiran): ' . $reasonPending
                        );
                    }
                    return;
                }

                if ($request->action === 'validate') {
                    if (!app(TicketApprovalEmailService::class)->isChainFullyApprovedAndChecked($groupTicket)) {
                        throw new \Exception('Group ticket masih menunggu proses ticket lainnya selesai.');
                    }
                }
                foreach ($tickets as $ticket) {
                    if (!$this->isUserValidator($ticket)) {
                        throw new \Exception('Anda tidak memiliki akses validator untuk ticket ' . $ticket->nomor_ticket);
                    }
                }
                if ($request->action === 'validate') {
                    // [PEMBATALAN PER BARIS] Ticket yang seluruh barisnya dibatalkan tidak boleh divalidasi kosong.
                    foreach ($tickets as $ticket) {
                        if ($this->isTicketFullyLineCancelled($ticket)) {
                            throw new \Exception(
                                'Seluruh baris pada ticket ' . $ticket->nomor_ticket . ' berstatus dibatalkan. '
                                    . 'Kembalikan minimal satu baris, atau tolak ticket ini.'
                            );
                        }
                    }
                }
                $signaturePath = $this->saveSignature($request->signature, 'validator');
                $isValidate    = $request->action === 'validate';
                $finalComment = !empty(trim($request->alasan_reject ?? ''))
                    ? trim($request->alasan_reject)
                    : null;
                if ($finalComment && !empty(trim($request->comment_text ?? ''))) {
                    $finalComment .= ' — ' . trim($request->comment_text);
                }
                $updateData = $isValidate
                    ? [
                        'validator'           => true,
                        'validator_id'        => $authKaryawanId,
                        'validator_name'      => $userKaryawan->name,
                        'validator_at'        => now(),
                        'validator_signature' => $signaturePath,
                        'approval_status'     => TicketHead::APPROVAL_APPROVED,
                        'execution_status'    => TicketHead::EXEC_READY,
                        'ticket_status'       => TicketHead::STATUS_WAITING_ASSIGNER,
                    ]
                    : [
                        'validator'           => false,
                        'validator_id'        => $authKaryawanId,
                        'validator_name'      => $userKaryawan->name,
                        'validator_at'        => now(),
                        'validator_signature' => $signaturePath,
                        'approval_status'     => TicketHead::APPROVAL_REJECTED,
                        'execution_status'    => TicketHead::EXEC_WAITING,
                        'ticket_status'       => TicketHead::STATUS_CANCELLED,
                        'is_submit'           => false,
                        'is_draft'            => false,
                        'closed_at'           => now(),
                    ];
                TicketHead::whereIn('id', $tickets->pluck('id'))->update($updateData);
                $activity = $isValidate ? 'Validate' : 'Reject';
                $description = $isValidate
                    ? 'Memvalidasi pengajuan keseluruhan ticket : ' . $groupNomorTicket
                    : 'Menolak pengajuan keseluruhan ticket : ' . $groupNomorTicket;
                $keterangan = $isValidate
                    ? 'Ticket berhasil divalidasi'
                    : 'Ticket berhasil ditolak';
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => Auth::user()->username,
                    'id_table' => $request->root_ticket_id ?? '-',
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'menu_name' => 'Validasi Ticket',
                    'table' => 'ticket_head',
                    'activity' => $activity,
                    'description' => $description,
                    'keterangan' => 'success',
                    'karir' => '2'
                ]);
                foreach ($tickets as $ticket) {
                    $this->saveApprovalComment(
                        $ticket->id,
                        'validator',
                        $userKaryawan,
                        $isValidate ? 'approve' : 'reject',
                        $isValidate ? $request->comment_text : $finalComment
                    );
                    if (!$isValidate) {
                        ActivityLog::create([
                            'user_id'          => $request->user()->id,
                            'user_name'        => Auth::user()->username,
                            'menu_id'          => null,
                            'menu_name'        => 'Validasi Ticket',
                            'id_table'         => $ticket->id,
                            'holding_id'       => '-',
                            'holding_category' => '-',
                            'table'            => 'ticket_head',
                            'activity'         => 'Reject',
                            'description'      => 'Ticket ' . $ticket->nomor_ticket . ' dibatalkan oleh Validator (' . $userKaryawan->name . ')',
                            'keterangan'       => 'Success',
                            'karir'            => '2'
                        ]);
                    }
                }
            });
            if ($request->action === 'validate') {
                $primaryTicket = $tickets->firstWhere('id', $rootId) ?? $tickets->first();
                if ($primaryTicket) {
                    app(TicketApprovalEmailService::class)->sendNextActionNotification(
                        $primaryTicket->fresh(),
                        $menuId,
                        'validated'
                    );
                }
            }
            return response()->json([
                'code'    => 200,
                'message' => $request->action === 'validate'
                    ? 'Seluruh ticket dalam grup berhasil divalidasi.'
                    : ($request->action === 'pending'
                        ? 'Seluruh ticket dalam grup berhasil dipending (SLA validasi dihentikan).'
                        : 'Seluruh ticket dalam grup berhasil ditolak.'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Upload lampiran baru khusus oleh validator
     */
    public function uploadValidatorAttachment(Request $request, $ticketId)
    {
        try {
            $ticket = TicketHead::findOrFail($ticketId);
            if (!$this->isUserValidator($ticket)) {
                return response()->json(['code' => 403, 'message' => 'Anda bukan validator ticket ini.'], 403);
            }
            if ($ticket->validator && !in_array((string) $ticket->validator_id, $this->currentApprovalSubjectIds(), true)) {
                return response()->json(['code' => 422, 'message' => 'Ticket sudah divalidasi oleh ' . $ticket->validator_name], 422);
            }

            $request->validate([
                'attachments'   => 'required|array|min:1',
                'attachments.*' => 'required|file|max:20480',
                'notes'         => 'nullable|string|max:500',
            ], [
                'attachments.required' => 'Pilih minimal satu file lampiran.',
                'attachments.*.file'   => 'Format file tidak valid.',
                'attachments.*.max'    => 'Ukuran file maksimal 20 MB per file.',
            ]);

            $userKaryawan = $this->currentKaryawan();
            $uploadedCount = 0;

            foreach ($request->file('attachments') as $file) {
                $origName   = $file->getClientOriginalName();
                $ext        = $file->getClientOriginalExtension();
                $mimeType   = $file->getClientMimeType();
                $size       = $file->getSize();
                $storedName = Str::uuid() . '.' . $ext;
                $path       = $file->storeAs('tickets/attachments', $storedName, 'public');

                TicketHeadAttachment::create([
                    'id'               => (string) Str::uuid(),
                    'ticket_head_id'   => $ticket->id,
                    'original_name'    => $origName,
                    'file_name'        => $storedName,
                    'file_path'        => $path,
                    'mime_type'        => $mimeType,
                    'file_size'        => $size,
                    'uploaded_by'      => Auth::id(),
                    'uploaded_by_name' => $userKaryawan ? $userKaryawan->name : Auth::user()->name,
                ]);
                $uploadedCount++;
            }

            ActivityLog::create([
                'user_id'          => Auth::id(),
                'user_name'        => Auth::user()->username,
                'id_table'         => $ticket->id,
                'holding_id'       => '-',
                'holding_category' => '-',
                'menu_name'        => 'Validator Ticket',
                'table'            => 'ticket_head_attachment',
                'activity'         => 'Upload Attachment',
                'description'      => 'Validator ' . ($userKaryawan->name ?? Auth::user()->name) . ' menambahkan ' . $uploadedCount . ' lampiran baru pada tiket ' . $ticket->nomor_ticket,
                'keterangan'       => 'success',
                'karir'            => '2',
            ]);

            if ($request->filled('notes')) {
                $this->saveApprovalComment(
                    $ticket->id,
                    'validator',
                    $userKaryawan,
                    'feedback',
                    'Menambahkan lampiran: ' . $request->notes
                );
            }

            return response()->json([
                'code'    => 200,
                'message' => "Berhasil mengunggah {$uploadedCount} lampiran.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus lampiran yang diunggah oleh validator
     */
    public function deleteValidatorAttachment(Request $request, $attachmentId)
    {
        try {
            $attachment = TicketHeadAttachment::findOrFail($attachmentId);
            $ticket = TicketHead::findOrFail($attachment->ticket_head_id);
            if (!$this->isUserValidator($ticket)) {
                return response()->json(['code' => 403, 'message' => 'Anda bukan validator ticket ini.'], 403);
            }
            if ($ticket->validator) {
                return response()->json(['code' => 422, 'message' => 'Ticket sudah divalidasi, lampiran tidak dapat dihapus.'], 422);
            }

            $userKaryawan = $this->currentKaryawan();
            $attachment->update(['deleted_at' => now()]);

            ActivityLog::create([
                'user_id'          => Auth::id(),
                'user_name'        => Auth::user()->username,
                'id_table'         => $ticket->id,
                'holding_id'       => '-',
                'holding_category' => '-',
                'menu_name'        => 'Validator Ticket',
                'table'            => 'ticket_head_attachment',
                'activity'         => 'Delete Attachment',
                'description'      => 'Validator ' . ($userKaryawan->name ?? Auth::user()->name) . ' menghapus lampiran: ' . $attachment->original_name,
                'keterangan'       => 'success',
                'karir'            => '2',
            ]);

            return response()->json([
                'code'    => 200,
                'message' => 'Lampiran berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Pending validasi untuk 1 tiket
     */
    public function pendingValidatorTicket(Request $request, $ticketId)
    {
        try {
            $ticket = TicketHead::findOrFail($ticketId);
            if (!$this->isUserValidator($ticket)) {
                return response()->json(['code' => 403, 'message' => 'Anda bukan validator ticket ini.'], 403);
            }
            if ($ticket->validator) {
                return response()->json(['code' => 422, 'message' => 'Ticket sudah divalidasi.'], 422);
            }

            $request->validate([
                'reason' => 'required|string|max:1000',
            ], [
                'reason.required' => 'Alasan pending wajib diisi.',
            ]);

            $userKaryawan = $this->currentKaryawan();

            DB::transaction(function () use ($ticket, $request, $userKaryawan) {
                $ticket->update([
                    'ticket_status'      => TicketHead::STATUS_PENDING_EXTERNAL,
                    'pending_reason'     => $request->reason,
                    'pending_party'      => 'requestor',
                    'pending_started_at' => now(),
                ]);

                ActivityLog::create([
                    'user_id'          => Auth::id(),
                    'user_name'        => Auth::user()->username,
                    'id_table'         => $ticket->id,
                    'holding_id'       => '-',
                    'holding_category' => '-',
                    'menu_name'        => 'Validator Ticket',
                    'table'            => 'ticket_head',
                    'activity'         => 'Pending Validation',
                    'description'      => 'Validator mem-pending ticket ' . $ticket->nomor_ticket . ' (menunggu lampiran): ' . $request->reason,
                    'keterangan'       => 'success',
                    'karir'            => '2',
                ]);

                $this->saveApprovalComment(
                    $ticket->id,
                    'validator',
                    $userKaryawan,
                    'feedback',
                    'Ticket dipending validator (menunggu kelengkapan lampiran): ' . $request->reason
                );
            });

            return response()->json([
                'code'    => 200,
                'message' => 'Ticket berhasil dipending. Waktu SLA validasi dihentikan sementara sampai kelengkapan lampiran terpenuhi.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Lanjutkan validasi (resume) dari status pending
     */
    public function resumeValidatorTicket(Request $request, $ticketId)
    {
        try {
            $ticket = TicketHead::findOrFail($ticketId);
            if (!$this->isUserValidator($ticket)) {
                return response()->json(['code' => 403, 'message' => 'Anda bukan validator ticket ini.'], 403);
            }
            if ($ticket->validator) {
                return response()->json(['code' => 422, 'message' => 'Ticket sudah divalidasi.'], 422);
            }

            $userKaryawan = $this->currentKaryawan();

            DB::transaction(function () use ($ticket, $userKaryawan) {
                $ticket->update([
                    'ticket_status'      => TicketHead::STATUS_WAITING_VALIDATOR,
                    'pending_reason'     => null,
                    'pending_party'      => null,
                    'pending_started_at' => null,
                ]);

                ActivityLog::create([
                    'user_id'          => Auth::id(),
                    'user_name'        => Auth::user()->username,
                    'id_table'         => $ticket->id,
                    'holding_id'       => '-',
                    'holding_category' => '-',
                    'menu_name'        => 'Validator Ticket',
                    'table'            => 'ticket_head',
                    'activity'         => 'Resume Validation',
                    'description'      => 'Validator melanjutkan validasi untuk ticket ' . $ticket->nomor_ticket,
                    'keterangan'       => 'success',
                    'karir'            => '2',
                ]);

                $this->saveApprovalComment(
                    $ticket->id,
                    'validator',
                    $userKaryawan,
                    'start',
                    'Validasi ticket dilanjutkan kembali oleh validator.'
                );
            });

            return response()->json([
                'code'    => 200,
                'message' => 'Ticket berhasil dilanjutkan ke antrean validasi aktif.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    //  PEMBATALAN PER BARIS (sub_detail) — checker menandai, validator memfinalisasi
    // =====================================================================

    /**
     * True jika ticket punya baris perubahan dan SEMUA barisnya dibatalkan.
     */
    private function isTicketFullyLineCancelled(TicketHead $ticket): bool
    {
        $ticket->loadMissing('ticketDetails.subDetails');
        $subs = $ticket->ticketDetails->flatMap(fn($d) => $d->subDetails);
        return $subs->isNotEmpty() && $subs->every(fn($s) => (bool) $s->is_cancelled);
    }

    /**
     * Terapkan perubahan status batal per baris pada satu ticket.
     *
     * @param  array<string,array{cancelled:mixed,reason:?string}> $items  sub_detail_id => state
     * @param  string $role  'checker' | 'validator'
     * @return array{error:?string,changed:array,cancelled_count:int,fully_cancelled_detail_ids:array,warnings:array}
     */
    private function applyLineCancellations(TicketHead $ticket, array $items, string $role, Karyawan $karyawan): array
    {
        $result = [
            'error'                      => null,
            'changed'                    => [],
            'cancelled_count'            => 0,
            'fully_cancelled_detail_ids' => [],
            'warnings'                   => [],
        ];
        $ticket->loadMissing('ticketDetails.subDetails');
        $details = $ticket->ticketDetails;

        // Detail yang seluruh barisnya SUDAH batal sebelum request ini juga ikut dihitung
        // supaya tidak diteruskan ke ticket turunan.
        $state = [];   // sub_id => bool (status akhir)
        $subs  = [];   // sub_id => model
        foreach ($details as $d) {
            foreach ($d->subDetails as $sd) {
                $subs[(string) $sd->id]  = $sd;
                $state[(string) $sd->id] = (bool) $sd->is_cancelled;
            }
        }

        $changes = [];
        foreach ($items as $id => $item) {
            $id = (string) $id;
            if (!isset($subs[$id]) || !is_array($item)) {
                continue; // abaikan id yang bukan milik ticket ini
            }
            $sd      = $subs[$id];
            $want    = filter_var($item['cancelled'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $reason  = trim((string) ($item['reason'] ?? ''));
            $current = (bool) $sd->is_cancelled;
            $label   = trim((string) ($sd->line_number ?? '')) !== '' ? 'Line ' . $sd->line_number : 'baris ' . $id;

            if ($want && $reason === '') {
                $result['error'] = 'Keterangan wajib diisi untuk baris yang dibatalkan (' . $label . ').';
                return $result;
            }
            if ($want && mb_strlen($reason) > 500) {
                $result['error'] = 'Keterangan pembatalan maksimal 500 karakter (' . $label . ').';
                return $result;
            }
            if ($want === $current && (!$want || $reason === (string) $sd->cancel_reason)) {
                continue; // tidak ada perubahan
            }
            // Checker tidak boleh membatalkan keputusan validator.
            if ($role === 'checker' && !$want && $current && $sd->cancelled_by_role === 'validator') {
                continue;
            }
            $changes[$id]  = ['sd' => $sd, 'cancelled' => $want, 'reason' => $reason, 'label' => $label];
            $state[$id]    = $want;
        }

        if (empty($changes)) {
            return $result;
        }
        // SESUDAH
        if ($role === 'validator' && !empty($state) && !in_array(false, $state, true)) {
            $result['error'] = 'Semua baris akan dibatalkan. Gunakan tombol Tolak jika seluruh pengajuan memang tidak diperlukan.';
            return $result;
        }
        $result['all_cancelled'] = !empty($state) && !in_array(false, $state, true);

        DB::transaction(function () use ($changes, $role, $karyawan, $ticket, &$result) {
            foreach ($changes as $id => $c) {
                $payload = $c['cancelled'] ? [
                    'is_cancelled'      => true,
                    'cancel_reason'     => $c['reason'],
                    'cancelled_by_role' => $role,
                    'cancelled_by_id'   => (string) $karyawan->id,
                    'cancelled_by_name' => $karyawan->name,
                    'cancelled_at'      => now(),
                ] : [
                    'is_cancelled'      => false,
                    'cancel_reason'     => null,
                    'cancelled_by_role' => null,
                    'cancelled_by_id'   => null,
                    'cancelled_by_name' => null,
                    'cancelled_at'      => null,
                ];
                TicketSubDetail::where('id', $id)->update($payload);
                $result['changed'][] = ['id' => $id, 'cancelled' => $c['cancelled'], 'label' => $c['label']];
                if ($c['cancelled']) {
                    $result['cancelled_count']++;
                }
            }

            // Detail yang seluruh barisnya batal (kondisi akhir).
            $ticket->unsetRelation('ticketDetails');
            $ticket->load('ticketDetails.subDetails');
            $affectedDetailIds = collect($changes)->map(fn($c) => (string) $c['sd']->ticket_detail_id)->unique()->all();
            foreach ($ticket->ticketDetails as $d) {
                if ($d->subDetails->isNotEmpty() && $d->subDetails->every(fn($s) => (bool) $s->is_cancelled)) {
                    $result['fully_cancelled_detail_ids'][] = (string) $d->id;
                }
            }

            // Rambatkan ke ticket turunan yang sudah terlanjur tergenerate.
            foreach ($ticket->ticketDetails as $d) {
                if (!in_array((string) $d->id, $affectedDetailIds, true)) {
                    continue;
                }
                $fully = in_array((string) $d->id, $result['fully_cancelled_detail_ids'], true);
                $warn  = $this->propagateLineCancelToChildren($d, $fully, $role, $karyawan);
                $result['warnings'] = array_merge($result['warnings'], $warn);
            }

            $cancelled = collect($result['changed'])->where('cancelled', true)->pluck('label')->implode(', ');
            $restored  = collect($result['changed'])->where('cancelled', false)->pluck('label')->implode(', ');
            $desc = 'Ticket ' . $ticket->nomor_ticket . ' oleh ' . ucfirst($role) . ' (' . $karyawan->name . ')'
                . ($cancelled !== '' ? ' — baris dibatalkan: ' . $cancelled : '')
                . ($restored !== '' ? ' — baris dikembalikan: ' . $restored : '');
            ActivityLog::create([
                'user_id'          => Auth::id(),
                'user_name'        => Auth::user()->username,
                'menu_id'          => null,
                'menu_name'        => 'Pembatalan Baris Ticket',
                'id_table'         => $ticket->id,
                'holding_id'       => '-',
                'holding_category' => '-',
                'table'            => 'ticket_sub_detail',
                'activity'         => 'update',
                'description'      => $desc,
                'keterangan'       => 'Success',
                'karir'            => '2',
            ]);
        });

        return $result;
    }

    /**
     * Rambatkan pembatalan/pengembalian dari detail induk ke detail turunan
     * (reference_ticket_detail_id). Hanya menyentuh ticket turunan yang BELUM divalidasi;
     * yang sudah divalidasi hanya mengembalikan peringatan.
     *
     * @return string[] peringatan untuk ditampilkan ke user
     */
    private function propagateLineCancelToChildren(TicketDetail $parentDetail, bool $fullyCancelled, string $role, Karyawan $karyawan): array
    {
        $warnings = [];
        $children = TicketDetail::where('reference_ticket_detail_id', $parentDetail->id)->get();
        if ($children->isEmpty()) {
            return $warnings;
        }
        $heads = TicketHead::whereIn('id', $children->pluck('ticket_id')->unique())->get()->keyBy('id');
        foreach ($children as $child) {
            $head = $heads->get($child->ticket_id);
            if (!$head) {
                continue;
            }
            if ($head->validator) {
                if ($fullyCancelled) {
                    $warnings[] = 'Ticket turunan ' . $head->nomor_ticket . ' sudah divalidasi; baris terkait tidak ikut dibatalkan otomatis.';
                }
                continue;
            }
            if ($fullyCancelled) {
                TicketSubDetail::where('ticket_detail_id', $child->id)
                    ->where(function ($q) {
                        $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
                    })
                    ->update([
                        'is_cancelled'      => true,
                        'cancel_reason'     => 'Mengikuti pembatalan transaksi induk (' . ($parentDetail->nomor_detail ?: '-') . ').',
                        'cancelled_by_role' => 'system',
                        'cancelled_by_id'   => (string) $karyawan->id,
                        'cancelled_by_name' => $karyawan->name,
                        'cancelled_at'      => now(),
                    ]);
                $warnings[] = 'Baris pada ticket turunan ' . $head->nomor_ticket . ' ikut ditandai batal.';
            } else {
                // Induk tidak lagi batal total: kembalikan hanya baris yang dibatalkan otomatis oleh sistem.
                TicketSubDetail::where('ticket_detail_id', $child->id)
                    ->where('cancelled_by_role', 'system')
                    ->update([
                        'is_cancelled'      => false,
                        'cancel_reason'     => null,
                        'cancelled_by_role' => null,
                        'cancelled_by_id'   => null,
                        'cancelled_by_name' => null,
                        'cancelled_at'      => null,
                    ]);
            }
        }
        return $warnings;
    }

    /**
     * Endpoint validator: batalkan / kembalikan baris sebelum ticket divalidasi.
     * POST body: items[<sub_detail_id>][cancelled]=0|1, items[<sub_detail_id>][reason]
     */
    public function saveLineCancel(Request $request, $ticketId)
    {
        $ticket = TicketHead::with('ticketMenu')->find($ticketId);
        if (!$ticket) {
            return response()->json(['code' => 404, 'message' => 'Ticket tidak ditemukan.']);
        }
        if (!$this->isUserValidator($ticket)) {
            return response()->json(['code' => 403, 'message' => 'Anda bukan validator ticket ini.']);
        }
        if ($ticket->validator || in_array($ticket->approval_status, [TicketHead::APPROVAL_REJECTED], true)) {
            return response()->json(['code' => 422, 'message' => 'Ticket sudah divalidasi/ditolak, baris tidak dapat diubah lagi.']);
        }
        $items = $request->input('items');
        if (!is_array($items) || empty($items)) {
            return response()->json(['code' => 422, 'message' => 'Tidak ada baris yang dikirim.']);
        }
        $userKaryawan = $this->currentKaryawan();
        if (!$userKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.']);
        }
        try {
            $res = $this->applyLineCancellations($ticket, $items, 'validator', $userKaryawan);
            if ($res['error']) {
                return response()->json(['code' => 422, 'message' => $res['error']]);
            }
            $msg = empty($res['changed']) ? 'Tidak ada perubahan.' : 'Status baris berhasil diperbarui.';
            if (!empty($res['warnings'])) {
                $msg .= ' ' . implode(' ', $res['warnings']);
            }
            return response()->json(['code' => 200, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    private function resolveKaryawanFromUser(?User $user): ?Karyawan
    {
        if (!$user || !$user->karyawan_id) return null;
        $identifier = (string) $user->karyawan_id;
        return Karyawan::where(function ($query) use ($identifier) {
            if (Uuid::isValid($identifier)) {
                $query->where('id', $identifier)
                    ->orWhere('nomor_identitas_karyawan', $identifier);
                return;
            }
            $query->where('nomor_identitas_karyawan', $identifier);
        })->first();
    }

    private function currentKaryawan(): ?Karyawan
    {
        return $this->resolveKaryawanFromUser(Auth::user());
    }

    private function resolveApprovalMenuId($menuId = null): int
    {
        if (is_numeric($menuId)) {
            return (int) $menuId;
        }

        return (int) (
            Menu::where('url', 'approval/ticket/menu')->value('id')
            ?? Menu::where('url', 'like', 'approval/ticket%')->value('id')
            ?? 0
        );
    }

    private function currentApprovalSubjectIds(): array
    {
        $karyawan = $this->currentKaryawan();
        $ids      = [];
        if ($karyawan) {
            $ids[] = (string) $karyawan->id;

            // Tambahkan ID seluruh karyawan yang saat ini didelegasikan / digantikan oleh user ini (karena sedang cuti/sakit)
            $delegatedIds = app(\App\Services\KaryawanPenggantiService::class)->ambilDaftarIdKaryawanYangDigantikan((string) $karyawan->id);
            if (!empty($delegatedIds)) {
                $ids = array_merge($ids, $delegatedIds);
            }

            $allKaryawanIds = array_values(array_unique(array_filter($ids)));

            $userIds = User::where(function ($query) use ($allKaryawanIds, $karyawan) {
                $query->whereIn('karyawan_id', $allKaryawanIds);
                if ($karyawan->nomor_identitas_karyawan) {
                    $query->orWhere('karyawan_id', (string) $karyawan->nomor_identitas_karyawan);
                }
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $ids = array_merge($ids, $userIds);
        }
        if (!$karyawan && Auth::id()) {
            $ids[] = (string) Auth::id();
        }
        return array_values(array_unique(array_filter($ids)));
    }

    private function directUserSubjectIds(): array
    {
        $karyawan = $this->currentKaryawan();
        $ids      = [];
        if ($karyawan) {
            $ids[] = (string) $karyawan->id;

            $allKaryawanIds = [$karyawan->id];
            $userIds = User::where(function ($query) use ($allKaryawanIds, $karyawan) {
                $query->whereIn('karyawan_id', $allKaryawanIds);
                if ($karyawan->nomor_identitas_karyawan) {
                    $query->orWhere('karyawan_id', (string) $karyawan->nomor_identitas_karyawan);
                }
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $ids = array_merge($ids, $userIds);
        }
        if (!$karyawan && Auth::id()) {
            $ids[] = (string) Auth::id();
        }
        return array_values(array_unique(array_filter($ids)));
    }

    private function resolveKaryawanByTicketSubject(?string $subjectId): ?Karyawan
    {
        if (!$subjectId) return null;
        $karyawan = Karyawan::find($subjectId);
        if ($karyawan) return $karyawan;
        $legacyUser = User::find($subjectId);
        return $this->resolveKaryawanFromUser($legacyUser);
    }

    private function isCurrentUserValidator(): bool
    {
        $subjectIds = $this->currentApprovalSubjectIds();
        if (empty($subjectIds)) return false;
        return TicketApprovalValidator::whereIn('karyawan_id', $subjectIds)
            ->where('is_active', true)->exists();
    }

    private function isUserValidator(TicketHead $ticket): bool
    {
        $subjectIds = $this->currentApprovalSubjectIds();
        if (empty($subjectIds)) return false;

        $validatorRecord = TicketApprovalValidator::whereIn('karyawan_id', $subjectIds)
            ->where('is_active', true)->get();

        if ($validatorRecord->isEmpty()) return false;
        if ($validatorRecord->whereNull('environment')->isNotEmpty()) return true;

        $environments = $validatorRecord->pluck('environment')
            ->filter()->map(fn($e) => (string) $e)->toArray();

        $ticketEnv = (string) ($ticket->ticketMenu?->kategori ?? '');
        return in_array($ticketEnv, $environments);
    }

    private function isCurrentUserAssignor(?int $env = null): bool
    {
        $karyawan = $this->currentKaryawan();
        if (!$karyawan) return false;
        $query = TicketAssignmentMaster::where('karyawan_id', $karyawan->id)
            ->where('assignment_type', 'A01')
            ->where('is_active', 1);
        if ($env !== null) {
            $query->forEnvironment($env);
        }
        return $query->exists();
    }

    private function assignorEnvironments(): array
    {
        $karyawan = $this->currentKaryawan();
        if (!$karyawan) return [];
        $envs = TicketAssignmentMaster::where('karyawan_id', $karyawan->id)
            ->where('assignment_type', 'A01')
            ->where('is_active', 1)
            ->pluck('environment');
        return $envs->contains(null)
            ? [1, 2, 3]
            : $envs->map(fn($e) => (int) $e)->unique()->values()->all();
    }

    private function saveSignature(?string $signatureData, string $approverType): ?string
    {
        if (!$signatureData || strpos($signatureData, 'data:image') === false) {
            return null;
        }
        $image     = str_replace('data:image/png;base64,', '', $signatureData);
        $image     = str_replace(' ', '+', $image);
        $imageName = 'signature_' . $approverType . '_' . time() . '.png';
        $path      = 'signatures/' . $imageName;
        Storage::disk('public')->put($path, base64_decode($image));
        return $path;
    }

    /**
     * @param string      
     * @param string       
     * @param Karyawan 
     * @param string     
     * @param string|null
     */
    private function saveApprovalComment(
        string $ticketId,
        string $approvalLevel,
        Karyawan $karyawan,
        string $actionTicket,
        ?string $commentText
    ): void {
        $jabatan = KaryawanJabatan::where('karyawan_id', $karyawan->id)
            ->where('is_active', 1)
            ->where('main', 1)
            ->first();
        TicketApprovalComment::create([
            'id'             => (string) Uuid::uuid4(),
            'ticket_id'      => $ticketId,
            'approval_level' => $approvalLevel,
            'karyawan_id'    => $karyawan->id,
            'jabatan_id'     => $jabatan?->id,
            'action_ticket'  => $actionTicket,
            'comment_text'   => !empty(trim($commentText ?? '')) ? trim($commentText) : null,
            'comment_at'     => now(),
        ]);
    }

    public function assignTicketIndex($menuId)
    {
        $user_karyawan = $this->currentKaryawan();
        if (!$this->isCurrentUserAssignor()) {
            abort(403, 'Anda tidak memiliki akses sebagai Assignor.');
        }
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus(
            RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray()
        );
        User::where('id', Auth::id())->update(['menu_active' => $menuId]);
        return view('users.ticket.assign', [
            'menu_id'       => $menuId,
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
        ]);
    }

    public function getListAssignTicket($menuId)
    {
        try {
            $karyawan = $this->currentKaryawan();
            if (!$this->isCurrentUserAssignor()) {
                return response()->json([
                    'code'    => 403,
                    'message' => 'Anda tidak memiliki akses sebagai Assignor.',
                ]);
            }
            $allowedEnvs = $this->assignorEnvironments();
            $tickets = TicketHead::with([
                'ticketMenu',
                'ticketSubMenu',
                'parentTicket:id,nomor_ticket,ticket_menu_id,ticket_sub_menu_id',
                'rootTicket:id,nomor_ticket,ticket_menu_id,ticket_sub_menu_id',
                'childTickets:id,parent_ticket_id,nomor_ticket',
                'generatedTickets:id,generated_from_ticket_id,nomor_ticket',
                'spawnedCrossTickets:id,origin_cross_ticket_id,nomor_ticket',
                'originCrossTicket:id,nomor_ticket',
            ])
                ->where('validator', true)
                ->forEnvironments($allowedEnvs)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($t) {
                    [$envKategori, $envName] = $this->resolveEnvironmentKategori($t);

                    $hasParent = !empty($t->parent_ticket_id) || !empty($t->generated_from_ticket_id) || !empty($t->origin_cross_ticket_id);
                    $parentNomor = $t->parentTicket?->nomor_ticket ?? ($t->generatedFromTicket?->nomor_ticket ?? ($t->originCrossTicket?->nomor_ticket ?? null));
                    $rootNomor = $t->rootTicket?->nomor_ticket ?? null;
                    $childCount = ($t->childTickets ? $t->childTickets->count() : 0)
                        + ($t->generatedTickets ? $t->generatedTickets->count() : 0)
                        + ($t->spawnedCrossTickets ? $t->spawnedCrossTickets->count() : 0);
                    $isRoot = empty($t->parent_ticket_id) && empty($t->generated_from_ticket_id) && empty($t->origin_cross_ticket_id);
                    $isGroup = $hasParent || $childCount > 0 || (!empty($t->root_ticket_id) && $t->root_ticket_id !== $t->id);

                    return [
                        'id'                   => $t->id,
                        'nomor_ticket'         => $t->nomor_ticket,
                        'requestor_name'       => $t->requestor_name,
                        'menu_name'            => $t->ticketMenu?->nama_menu,
                        'submenu_name'         => $t->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $envKategori,
                        'environment_name'     => $envName,
                        'priority'             => $t->ticket_priority,
                        'detail_count'         => $t->ticketDetails()->count(),
                        'tanggal_ticket'       => $t->created_at,
                        'pic_id'               => $t->pic_id,
                        'pic_name'             => $t->pic_name,
                        'ticket_status'        => $t->ticket_status,
                        'approval_status'      => $t->approval_status,
                        'execution_status'     => $t->execution_status,
                        'status_meta'          => $t->status_meta,
                        'pic_started_at'       => $t->pic_started_at,
                        'finish_at'            => $t->finish_at,
                        'closed_at'            => $t->closed_at,
                        'actual_hours'         => $t->actual_hours,
                        'expected_hours'       => $t->expected_hours,
                        'variance_hours'       => $t->variance_hours,
                        'overrun_flag'         => (bool) $t->overrun_flag,
                        'feedback_cycles'      => (int) ($t->feedback_cycles ?? 0),
                        'has_parent'           => $hasParent,
                        'parent_ticket_number' => $parentNomor,
                        'parent_ticket_id'     => $t->parent_ticket_id ?? $t->generated_from_ticket_id ?? $t->origin_cross_ticket_id,
                        'is_root'              => $isRoot,
                        'root_ticket_number'   => $rootNomor,
                        'child_count'          => $childCount,
                        'is_group'             => $isGroup,
                        'root_ticket_id'       => $t->root_ticket_id ?: $t->id,
                    ];
                })
                ->filter(fn($t) => in_array($t['environment_kategori'], $allowedEnvs))
                ->values();
            $total      = $tickets->count();
            $cancelled  = $tickets->filter(fn($t) => $t['ticket_status'] === TicketHead::STATUS_CANCELLED)->count();
            $closed     = $tickets->filter(fn($t) => $t['ticket_status'] === TicketHead::STATUS_CLOSED)->count();
            $unassigned = $tickets->filter(fn($t) => empty($t['pic_id']) && $t['ticket_status'] !== TicketHead::STATUS_CANCELLED)->count();
            $assigned   = $total - $unassigned - $cancelled;
            $inProgress = $tickets->filter(fn($t) => $t['ticket_status'] === TicketHead::STATUS_IN_PROGRESS)->count();
            $feedback   = $tickets->filter(fn($t) => $t['ticket_status'] === TicketHead::STATUS_FEEDBACK_REQUESTOR)->count();

            return response()->json([
                'code'    => 200,
                'tickets' => $tickets->values(),
                'summary' => compact('total', 'assigned', 'unassigned', 'inProgress', 'feedback', 'closed', 'cancelled'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getListPic(Request $request, $menuId)
    {
        try {
            $ticket = $request->filled('ticket_id')
                ? TicketHead::with('ticketMenu')->find($request->ticket_id)
                : null;
            $env = $ticket ? $this->resolveEnvironmentKategori($ticket)[0] : null;
            if (!$this->isCurrentUserAssignor($env)) {
                return response()->json([
                    'code'    => 403,
                    'message' => 'Anda tidak memiliki akses sebagai Assignor untuk environment ini.',
                ]);
            }
            $picQuery = TicketAssignmentMaster::with(['karyawan', 'jabatan'])
                ->where('assignment_type', 'A02')
                ->where('is_active', 1);
            if ($env !== null) {
                $picQuery->forEnvironment($env);
            }
            $pics = $picQuery->get()
                ->map(function ($am) {
                    if (!$am->karyawan) return null;
                    $activeTickets = TicketHead::where('pic_id', $am->karyawan_id)
                        ->whereIn('ticket_status', [
                            TicketHead::STATUS_ASSIGNED,
                            TicketHead::STATUS_IN_PROGRESS,
                            TicketHead::STATUS_PENDING_EXTERNAL,
                        ])
                        ->count();
                    return [
                        'id'             => $am->karyawan_id,
                        'name'           => $am->karyawan->name,
                        'role'           => $am->jabatan?->nama_jabatan ?? '-',
                        'active_tickets' => $activeTickets,
                    ];
                })
                ->filter()
                ->unique('id')
                ->values();
            return response()->json(['code' => 200, 'pic' => $pics]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getTicketAssignDetail($menuId, $ticketId)
    {
        try {
            $karyawan = $this->currentKaryawan();
            if (!$this->isCurrentUserAssignor()) {
                return response()->json(['code' => 403, 'message' => 'Akses ditolak.']);
            }
            $ticket = TicketHead::with([
                'ticketMenu',
                'ticketSubMenu',
                'ticketDetails.subDetails',
                'ticketDetails.referenceTicketDetail',
                'attachments',
                'checkers.karyawan',
                'checkers.dependencyRule',
                'checkers.branchSubMenu.ticketMenu',
                'workSessions.pic',
                'comments.karyawan',
                'parentTicket.ticketMenu',
                'parentTicket.ticketSubMenu',
                'rootTicket.ticketMenu',
                'rootTicket.ticketSubMenu',
                'childTickets.ticketMenu',
                'childTickets.ticketSubMenu',
                'generatedTickets.ticketMenu',
                'generatedTickets.ticketSubMenu',
                'spawnedCrossTickets.ticketMenu',
                'spawnedCrossTickets.ticketSubMenu',
                'originCrossTicket.ticketMenu',
                'originCrossTicket.ticketSubMenu',
            ])->findOrFail($ticketId);

            $parentObj = $ticket->parentTicket ?? ($ticket->generatedFromTicket ?? $ticket->originCrossTicket);
            $rootObj   = $ticket->rootTicket;

            $hasParent = !empty($parentObj) && $parentObj->id !== $ticket->id;
            $hasRoot   = !empty($rootObj) && $rootObj->id !== $ticket->id && (!$parentObj || $rootObj->id !== $parentObj->id);
            $isRoot    = empty($ticket->parent_ticket_id) && empty($ticket->generated_from_ticket_id) && empty($ticket->origin_cross_ticket_id);

            $allChildren = collect();
            if ($ticket->childTickets) $allChildren = $allChildren->merge($ticket->childTickets);
            if ($ticket->generatedTickets) $allChildren = $allChildren->merge($ticket->generatedTickets);
            if ($ticket->spawnedCrossTickets) $allChildren = $allChildren->merge($ticket->spawnedCrossTickets);
            $allChildren = $allChildren->unique('id')->values();

            $rootNomor   = $rootObj?->nomor_ticket ?? ($ticket->root_ticket_id ? TicketHead::where('id', $ticket->root_ticket_id)->value('nomor_ticket') : null);
            $parentNomor = $parentObj?->nomor_ticket ?? ($ticket->parent_ticket_id ? TicketHead::where('id', $ticket->parent_ticket_id)->value('nomor_ticket') : null);

            [$assignEnvKategori, $assignEnvName] = $this->resolveEnvironmentKategori($ticket);

            $ticketChainService = app(\App\Services\TicketChainService::class);
            $chainInfo          = $ticketChainService->resolveChainInfo($ticket);

            return response()->json([
                'code'   => 200,
                'ticket' => [
                    'id'                   => $ticket->id,
                    'nomor_ticket'         => $ticket->nomor_ticket,
                    'root_ticket_id'       => $ticket->root_ticket_id,
                    'root_nomor'           => $rootNomor,
                    'parent_ticket_id'     => $ticket->parent_ticket_id,
                    'parent_nomor'         => $parentNomor,
                    'is_root'              => $isRoot,
                    'has_parent'           => $hasParent,
                    'has_root'             => $hasRoot,
                    'parent_ticket'        => $parentObj ? [
                        'id'                   => $parentObj->id,
                        'ticket_id'            => $parentObj->id,
                        'menu_id'              => $parentObj->ticket_menu_id,
                        'ticket_menu_id'       => $parentObj->ticket_menu_id,
                        'link_url'             => url('/ticket/lihat/' . ($parentObj->ticket_menu_id ?: '1') . '/' . $parentObj->id),
                        'nomor_ticket'         => $parentObj->nomor_ticket,
                        'menu_name'            => $parentObj->ticketMenu?->nama_menu,
                        'submenu_name'         => $parentObj->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $parentObj->ticketMenu?->kategori ?? 1,
                        'requestor_name'       => $parentObj->requestor_name,
                        'created_at'           => $parentObj->created_at,
                        'ticket_status'        => $parentObj->ticket_status,
                    ] : null,
                    'root_ticket'          => $rootObj ? [
                        'id'                   => $rootObj->id,
                        'ticket_id'            => $rootObj->id,
                        'menu_id'              => $rootObj->ticket_menu_id,
                        'ticket_menu_id'       => $rootObj->ticket_menu_id,
                        'link_url'             => url('/ticket/lihat/' . ($rootObj->ticket_menu_id ?: '1') . '/' . $rootObj->id),
                        'nomor_ticket'         => $rootObj->nomor_ticket,
                        'menu_name'            => $rootObj->ticketMenu?->nama_menu,
                        'submenu_name'         => $rootObj->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $rootObj->ticketMenu?->kategori ?? 1,
                        'requestor_name'       => $rootObj->requestor_name,
                        'created_at'           => $rootObj->created_at,
                        'ticket_status'        => $rootObj->ticket_status,
                    ] : null,
                    'child_tickets'        => $allChildren->map(fn($c) => [
                        'id'                   => $c->id,
                        'ticket_id'            => $c->id,
                        'menu_id'              => $c->ticket_menu_id,
                        'ticket_menu_id'       => $c->ticket_menu_id,
                        'link_url'             => url('/ticket/lihat/' . ($c->ticket_menu_id ?: '1') . '/' . $c->id),
                        'nomor_ticket'         => $c->nomor_ticket,
                        'menu_name'            => $c->ticketMenu?->nama_menu,
                        'submenu_name'         => $c->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $c->ticketMenu?->kategori ?? 1,
                        'pic_name'             => $c->pic_name,
                        'ticket_status'        => $c->ticket_status,
                        'created_at'           => $c->created_at,
                    ])->values(),
                    'chain_info'           => $chainInfo,
                    'menu_id'              => $ticket->ticket_menu_id,
                    'menu_name'            => $ticket->ticketMenu?->nama_menu,
                    'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                    'environment_kategori' => $assignEnvKategori,
                    'environment_name'     => $assignEnvName,
                    'priority'             => $ticket->ticket_priority,
                    'requestor_name'       => $ticket->requestor_name,
                    'requestor_signature'  => $ticket->requestor_signature
                        ? asset('storage/' . $ticket->requestor_signature)
                        : null,
                    'approve1_id'          => $ticket->approve1_id,
                    'approve1_name'        => $ticket->approve1_name,
                    'approve1_at'          => $ticket->approve1_at,
                    'approve1_signature'   => $ticket->approve1_signature
                        ? asset('storage/' . $ticket->approve1_signature)
                        : null,
                    'approve2_id'          => $ticket->approve2_id,
                    'approve2_name'        => $ticket->approve2_name,
                    'approve2_at'          => $ticket->approve2_at,
                    'approve2_signature'   => $ticket->approve2_signature
                        ? asset('storage/' . $ticket->approve2_signature)
                        : null,
                    'checker_id'           => $ticket->checker_id,
                    'checker_name'         => $ticket->checker_name,
                    'checked_at'           => $ticket->checked_at,
                    'checker_signature'    => $ticket->checker_signature
                        ? asset('storage/' . $ticket->checker_signature)
                        : null,
                    'checkers'             => $ticket->checkers->map(fn($chk) => [
                        'id'                   => $chk->id,
                        'karyawan_id'          => $chk->karyawan_id,
                        'karyawan_name'        => $chk->karyawan_name ?? ($chk->karyawan?->name ?? '-'),
                        'signature'            => $chk->signature ? asset('storage/' . $chk->signature) : null,
                        'status'               => $chk->status,
                        'is_cross_environment' => (bool) ($chk->dependencyRule?->is_cross_environment ?? false),
                        'environment_name'     => $chk->branchSubMenu?->ticketMenu?->kategori_display ?? null,
                        'branch_sub_menu_name' => $chk->branchSubMenu?->nama_sub_menu ?? null,
                        'approved_at'          => $chk->approved_at ?? ($chk->status === 'completed' || $chk->status === 'approved' ? $chk->updated_at : null),
                    ])->values(),
                    'validator_id'        => $ticket->validator_id,
                    'validator_name'      => $ticket->validator_name,
                    'validator_at'        => $ticket->validator_at,
                    'validator_signature' => $ticket->validator_signature
                        ? asset('storage/' . $ticket->validator_signature)
                        : null,
                    'approval_status'     => $ticket->approval_status,
                    'execution_status'    => $ticket->execution_status,
                    'ticket_status'       => $ticket->ticket_status,
                    'status_meta'         => $ticket->status_meta,
                    'pic_id'              => $ticket->pic_id,
                    'pic_name'            => $ticket->pic_name,
                    'assign_name'         => $ticket->assign_name,
                    'assign_at'           => $ticket->assign_at,
                    'created_at'          => $ticket->created_at,
                    'pic_started_at'      => $ticket->pic_started_at,
                    'finish_at'           => $ticket->finish_at,
                    'closed_at'           => $ticket->closed_at,
                    'actual_hours'        => $ticket->actual_hours,
                    'expected_hours'      => $ticket->expected_hours,
                    'variance_hours'      => $ticket->variance_hours,
                    'overrun_flag'        => $ticket->overrun_flag,
                    'feedback_cycles'     => $ticket->feedback_cycles ?? 0,
                    'work_sessions'       => $ticket->workSessions->map(fn($ws) => [
                        'id'               => $ws->id,
                        'cycle_number'     => $ws->cycle_number,
                        'session_type'     => $ws->session_type,
                        'started_at'       => $ws->started_at,
                        'ended_at'         => $ws->ended_at,
                        'duration_minutes' => $ws->duration_minutes,
                        'duration_hours'   => $ws->duration_minutes ? round($ws->duration_minutes / 60, 2) : 0,
                        'notes'            => $ws->notes,
                        'pic_name'         => $ws->pic?->name ?? $ticket->pic_name,
                    ])->values(),
                    'lampiran'            => $ticket->lampiran ? asset('storage/' . $ticket->lampiran) : null,
                    'attachments'         => $ticket->attachments->map(fn($att) => [
                        'id'            => $att->id,
                        'original_name' => $att->original_name,
                        'file_name'     => $att->file_name,
                        'file_path'     => asset('storage/' . $att->file_path),
                        'file_size'     => $att->file_size,
                        'mime_type'     => $att->mime_type,
                    ])->values(),
                    'details'             => $ticket->ticketDetails->map(fn($d) => [
                        'nomor_detail'            => $d->nomor_detail,
                        'tanggal_transaksi'       => $d->tanggal_transaksi,
                        'alasan_ubah'             => $d->alasan_ubah,
                        'before_ubah'             => $d->before_ubah,
                        'after_ubah'              => $d->after_ubah,
                        'reference_ticket_detail' => $d->referenceTicketDetail ? [
                            'nomor_detail'      => $d->referenceTicketDetail->nomor_detail,
                            'tanggal_transaksi' => $d->referenceTicketDetail->tanggal_transaksi,
                        ] : null,
                        'sub_details'             => $d->subDetails->map(fn($s) => [
                            'id'                => $s->id,
                            'is_cancelled'      => (bool) $s->is_cancelled,
                            'cancel_reason'     => $s->cancel_reason,
                            'cancelled_by_role' => $s->cancelled_by_role,
                            'cancelled_by_name' => $s->cancelled_by_name,
                            'line_number' => $s->line_number,
                            'alasan_ubah' => $s->alasan_ubah,
                            'before_ubah' => $s->before_ubah,
                            'after_ubah'  => $s->after_ubah,
                        ])->values()->all(),
                    ]),
                    'comments'            => $ticket->comments->map(fn($comment) => [
                        'level'   => $comment->approval_level,
                        'action'  => $comment->action_ticket,
                        'name'    => $comment->karyawan?->name ?? '-',
                        'text'    => $comment->comment_text,
                        'date'    => $comment->comment_at,
                    ])->values(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeAssignTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:ticket_head,id',
            'pic_id'    => 'required|exists:karyawans,id',
            'note'      => 'nullable|string|max:500',
            'priority'  => 'required|in:P01,P02,P03,P04',
        ], [
            'ticket_id.required' => 'ID Ticket tidak ditemukan, silakan pilih ticket kembali.',
            'ticket_id.exists'   => 'Ticket tidak ditemukan di sistem.',
            'pic_id.required'    => 'Pilih PIC yang akan ditugaskan terlebih dahulu.',
            'pic_id.exists'      => 'Data PIC yang dipilih tidak valid di sistem.',
            'note.string'        => 'Catatan harus berupa teks.',
            'note.max'           => 'Catatan maksimal 500 karakter.',
            'priority.required'  => 'Pilih tingkat kesulitan / prioritas terlebih dahulu.',
            'priority.in'        => 'Tingkat kesulitan / prioritas yang dipilih tidak valid.',
        ]);
        $nama_prioritas = TicketDifficultyMaster::where('code', $request->priority)->value('name') ?? $request->priority;
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }
        try {
            $karyawan = $this->currentKaryawan();
            if (!$this->isCurrentUserAssignor()) {
                return response()->json(['code' => 403, 'message' => 'Anda tidak memiliki akses sebagai Assignor.']);
            }
            $ticket = TicketHead::with('ticketMenu')->findOrFail($request->ticket_id);
            [$env]  = $this->resolveEnvironmentKategori($ticket);
            if (!$this->isCurrentUserAssignor($env)) {
                return response()->json(['code' => 403, 'message' => 'Anda bukan Assignor untuk environment tiket ini.']);
            }
            $isPic = TicketAssignmentMaster::where('karyawan_id', $request->pic_id)
                ->where('assignment_type', 'A02')
                ->where('is_active', 1)
                ->exists();
            if (!$isPic) {
                return response()->json(['code' => 422, 'message' => 'PIC yang dipilih tidak terdaftar untuk environment tiket ini.']);
            }
            if (!$ticket->validator) {
                return response()->json(['code' => 422, 'message' => 'Ticket belum divalidasi, tidak dapat di-assign.']);
            }
            $picKaryawan   = Karyawan::findOrFail($request->pic_id);
            $previousPicId = $ticket->pic_id;
            $isReassigned  = !empty($previousPicId) && (string) $previousPicId !== (string) $picKaryawan->id;

            $ticket->update([
                'assign'           => 1,
                'assign_id'        => $karyawan->id,
                'assign_name'      => $karyawan->name,
                'assign_at'        => now(),
                'pic_id'           => $picKaryawan->id,
                'pic_name'         => $picKaryawan->name,
                'pic_status'       => false,
                'ticket_priority'  => $request->priority,
                'ticket_status'    => TicketHead::STATUS_ASSIGNED,
                'execution_status' => TicketHead::EXEC_READY,
                'finish_at'        => null,
            ]);
            ActivityLog::create([
                'user_id'          => $request->user()->id,
                'user_name'        => Auth::user()->username,
                'menu_id'          => null,
                'menu_name'        => 'Assign Ticket',
                'id_table'         => $ticket->id,
                'holding_id'       => '-',
                'holding_category' => '-',
                'table'            => 'ticket_head',
                'activity'         => $isReassigned ? 'Reassign PIC' : 'Assign',
                'description'      => ($isReassigned ? 'Mengubah PIC ticket : ' : 'Meng-assign ticket : ') . $ticket->nomor_ticket . ' ke PIC: ' . $picKaryawan->name,
                'keterangan'       => 'Success',
                'karir'            => '2'
            ]);
            $actionText = $isReassigned
                ? 'Ticket dialihkan ke PIC'
                : 'Ticket di-assign ke PIC';
            $comment = $actionText . ': ' . $picKaryawan->name
                . ' | Prioritas: ' . $nama_prioritas
                . ' | Catatan: ' . ($request->note ?: '-');
            $this->saveApprovalComment(
                $ticket->id,
                'Assign',
                $karyawan,
                'Assign',
                $comment
            );
            app(TicketApprovalEmailService::class)->sendNextActionNotification(
                $ticket->fresh(),
                $isReassigned ? 'reassigned' : 'assigned'
            );
            return response()->json([
                'code'    => 200,
                'message' => $isReassigned
                    ? 'PIC ticket berhasil diubah ke ' . $picKaryawan->name . '.'
                    : 'Ticket berhasil di-assign ke ' . $picKaryawan->name . '.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function picTicketIndex($menuId)
    {
        $userKaryawan = $this->currentKaryawan();
        if (!$this->isActivePic($userKaryawan)) {
            abort(403, 'Anda tidak memiliki akses sebagai PIC.');
        }
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus(
            RoleUsers::where('role_user_id', Auth::id())->pluck('role_menu_id')->toArray()
        );
        User::where('id', Auth::id())->update(['menu_active' => $menuId]);
        return view('users.ticket.pic', [
            'menu_id'       => $menuId,
            'user_karyawan' => $userKaryawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
        ]);
    }

    public function getListPicTicket($menuId)
    {
        try {
            $karyawan = $this->currentKaryawan();
            if (!$this->isActivePic($karyawan)) {
                return response()->json(['code' => 403, 'message' => 'Anda tidak memiliki akses sebagai PIC.']);
            }

            $subjectIds = $this->directUserSubjectIds();

            // root_ticket_id dari seluruh tiket milik saya (termasuk yang didelegasikan ke saya),
            // dipakai untuk menarik tiket "saudara" (root sama) walau PIC-nya orang lain — supaya
            // kartu grup di halaman PIC menampilkan konteks grup yang lengkap, lintas-PIC.
            $myRootIds = TicketHead::whereIn('pic_id', $subjectIds)
                ->get(['id', 'root_ticket_id'])
                ->map(fn($t) => $t->root_ticket_id ?: $t->id)
                ->unique()
                ->values()
                ->all();

            $tickets = TicketHead::with([
                'ticketMenu',
                'ticketSubMenu',
                'difficulty',
                'parentTicket:id,nomor_ticket,ticket_menu_id,ticket_sub_menu_id',
                'rootTicket:id,nomor_ticket,ticket_menu_id,ticket_sub_menu_id',
                'childTickets:id,parent_ticket_id,nomor_ticket',
                'generatedTickets:id,generated_from_ticket_id,nomor_ticket',
                'spawnedCrossTickets:id,origin_cross_ticket_id,nomor_ticket',
                'originCrossTicket:id,nomor_ticket',
            ])
                ->where(function ($q) use ($subjectIds, $myRootIds) {
                    $q->whereIn('pic_id', $subjectIds);
                    if (!empty($myRootIds)) {
                        $q->orWhere(function ($q2) use ($myRootIds) {
                            $q2->whereIn('root_ticket_id', $myRootIds)
                                ->orWhereIn('id', $myRootIds);
                        });
                    }
                })
                ->orderByRaw('COALESCE(finish_at, assign_at, created_at) DESC')
                ->get()
                ->map(function ($ticket) use ($subjectIds) {
                    [$envKategori, $envName] = $this->resolveEnvironmentKategori($ticket);

                    $hasParent = !empty($ticket->parent_ticket_id) || !empty($ticket->generated_from_ticket_id) || !empty($ticket->origin_cross_ticket_id);
                    $parentNomor = $ticket->parentTicket?->nomor_ticket ?? ($ticket->generatedFromTicket?->nomor_ticket ?? ($ticket->originCrossTicket?->nomor_ticket ?? null));
                    $rootNomor = $ticket->rootTicket?->nomor_ticket ?? null;
                    $childCount = ($ticket->childTickets ? $ticket->childTickets->count() : 0)
                        + ($ticket->generatedTickets ? $ticket->generatedTickets->count() : 0)
                        + ($ticket->spawnedCrossTickets ? $ticket->spawnedCrossTickets->count() : 0);
                    $isRoot = empty($ticket->parent_ticket_id) && empty($ticket->generated_from_ticket_id) && empty($ticket->origin_cross_ticket_id);
                    $isGroup = $hasParent || $childCount > 0 || (!empty($ticket->root_ticket_id) && $ticket->root_ticket_id !== $ticket->id);
                    $isMine = in_array((string) $ticket->pic_id, $subjectIds, true);

                    return [
                        'id'                   => $ticket->id,
                        'nomor_ticket'         => $ticket->nomor_ticket,
                        'requestor_name'       => $ticket->requestor_name,
                        'menu_name'            => $ticket->ticketMenu?->nama_menu,
                        'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $envKategori,
                        'environment_name'     => $envName,
                        'priority'             => $ticket->ticket_priority,
                        'difficulty_name'      => $ticket->difficulty?->name ?? $ticket->ticket_priority,
                        'detail_count'         => $ticket->ticketDetails()->count(),
                        'created_at'           => $ticket->created_at,
                        'assign_at'            => $ticket->assign_at,
                        'pic_started_at'       => $ticket->pic_started_at,
                        'finish_at'            => $ticket->finish_at,
                        'closed_at'            => $ticket->closed_at,
                        'assign_name'          => $ticket->assign_name,
                        'actual_hours'         => $ticket->actual_hours,
                        'expected_hours'       => $ticket->expected_hours,
                        'variance_hours'       => $ticket->variance_hours,
                        'overrun_flag'         => (bool) $ticket->overrun_flag,
                        'feedback_cycles'      => (int) ($ticket->feedback_cycles ?? 0),
                        'ticket_status'        => $ticket->ticket_status,
                        'status_label'         => $this->ticketStatusLabel($ticket->ticket_status),
                        'has_parent'           => $hasParent,
                        'parent_ticket_number' => $parentNomor,
                        'parent_ticket_id'     => $ticket->parent_ticket_id ?? $ticket->generated_from_ticket_id ?? $ticket->origin_cross_ticket_id,
                        'is_root'              => $isRoot,
                        'root_ticket_number'   => $rootNomor,
                        'root_ticket_id'       => $ticket->root_ticket_id ?: $ticket->id,
                        'child_count'          => $childCount,
                        'is_group'             => $isGroup,
                        'pic_id'               => $ticket->pic_id,
                        'pic_name'             => $ticket->pic_name,
                        'is_mine'              => $isMine,
                    ];
                });

            // Kartu ringkasan HANYA dihitung dari tiket milik saya sendiri — tiket saudara
            // milik PIC lain yang ikut terbawa untuk konteks grup tidak masuk hitungan ini.
            $myTickets = $tickets->where('is_mine', true);

            return response()->json([
                'code'    => 200,
                'tickets' => $tickets->values(),
                'summary' => [
                    'total'       => $myTickets->count(),
                    'ready'       => $myTickets->whereIn('ticket_status', [TicketHead::STATUS_ASSIGNED, TicketHead::STATUS_APPROVED, TicketHead::STATUS_WAITING_VALIDATOR, TicketHead::STATUS_WAITING_ASSIGNER])->whereNull('pic_started_at')->count(),
                    'progress'    => $myTickets->where('ticket_status', TicketHead::STATUS_IN_PROGRESS)->count(),
                    'feedback'    => $myTickets->where('ticket_status', TicketHead::STATUS_FEEDBACK_REQUESTOR)->count(),
                    'review'      => $myTickets->where('ticket_status', TicketHead::STATUS_WAITING_REQUESTOR)->count(),
                    'cancelled'   => $myTickets->where('ticket_status', TicketHead::STATUS_CANCELLED)->count(),
                    'done'        => $myTickets->where('ticket_status', TicketHead::STATUS_CLOSED)->count(),
                    'terminal'    => $myTickets->whereIn('ticket_status', [
                        TicketHead::STATUS_CLOSED,
                        TicketHead::STATUS_CANCELLED,
                    ])->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }
    public function getTicketPicDetail($menuId, $ticketId)
    {
        try {
            $karyawan = $this->currentKaryawan();
            if (!$this->isActivePic($karyawan)) {
                return response()->json(['code' => 403, 'message' => 'Anda tidak memiliki akses sebagai PIC.']);
            }

            $subjectIds = $this->directUserSubjectIds();

            // Sama seperti getListPicTicket: selain tiket milik saya sendiri, izinkan juga
            // melihat detail tiket "saudara" (root_ticket_id sama) meski PIC-nya orang lain,
            // supaya tombol Detail di kartu grup lintas-PIC tetap bisa dibuka.
            $myRootIds = TicketHead::whereIn('pic_id', $subjectIds)
                ->get(['id', 'root_ticket_id'])
                ->map(fn($t) => $t->root_ticket_id ?: $t->id)
                ->unique()
                ->values()
                ->all();

            $ticket = TicketHead::with([
                'ticketMenu',
                'ticketSubMenu',
                'ticketDetails.subDetails',
                'ticketDetails.referenceTicketDetail',
                'checkers.karyawan',
                'checkers.dependencyRule',
                'checkers.branchSubMenu.ticketMenu',
                'comments.karyawan',
                'attachments',
                'difficulty',
                'workSessions.pic',
                'parentTicket.ticketMenu',
                'parentTicket.ticketSubMenu',
                'rootTicket.ticketMenu',
                'rootTicket.ticketSubMenu',
                'childTickets.ticketMenu',
                'childTickets.ticketSubMenu',
                'generatedTickets.ticketMenu',
                'generatedTickets.ticketSubMenu',
                'spawnedCrossTickets.ticketMenu',
                'spawnedCrossTickets.ticketSubMenu',
                'originCrossTicket.ticketMenu',
                'originCrossTicket.ticketSubMenu',
            ])
                ->where(function ($q) use ($subjectIds, $myRootIds) {
                    $q->whereIn('pic_id', $subjectIds);
                    if (!empty($myRootIds)) {
                        $q->orWhere(function ($q2) use ($myRootIds) {
                            $q2->whereIn('root_ticket_id', $myRootIds)
                                ->orWhereIn('id', $myRootIds);
                        });
                    }
                })
                ->findOrFail($ticketId);

            [$picEnvKategori, $picEnvName] = $this->resolveEnvironmentKategori($ticket);

            $parentObj = $ticket->parentTicket ?? ($ticket->generatedFromTicket ?? $ticket->originCrossTicket);
            $rootObj   = $ticket->rootTicket;

            $hasParent = !empty($parentObj) && $parentObj->id !== $ticket->id;
            $hasRoot   = !empty($rootObj) && $rootObj->id !== $ticket->id && (!$parentObj || $rootObj->id !== $parentObj->id);
            $isRoot    = empty($ticket->parent_ticket_id) && empty($ticket->generated_from_ticket_id) && empty($ticket->origin_cross_ticket_id);

            $allChildren = collect();
            if ($ticket->childTickets) $allChildren = $allChildren->merge($ticket->childTickets);
            if ($ticket->generatedTickets) $allChildren = $allChildren->merge($ticket->generatedTickets);
            if ($ticket->spawnedCrossTickets) $allChildren = $allChildren->merge($ticket->spawnedCrossTickets);
            $allChildren = $allChildren->unique('id')->values();

            $ticketChainService = app(\App\Services\TicketChainService::class);
            $chainInfo          = $ticketChainService->resolveChainInfo($ticket);

            return response()->json([
                'code'   => 200,
                'ticket' => [
                    'id'                   => $ticket->id,
                    'nomor_ticket'         => $ticket->nomor_ticket,
                    'requestor_name'       => $ticket->requestor_name,
                    'menu_id'              => $ticket->ticket_menu_id,
                    'menu_name'            => $ticket->ticketMenu?->nama_menu,
                    'submenu_name'         => $ticket->ticketSubMenu?->nama_sub_menu,
                    'environment_kategori' => $picEnvKategori,
                    'environment_name'     => $picEnvName,
                    'priority'             => $ticket->ticket_priority,
                    'difficulty_name'      => $ticket->difficulty?->name ?? $ticket->ticket_priority,
                    'detail_count'         => $ticket->ticketDetails->count(),
                    'created_at'           => $ticket->created_at,
                    'assign_at'            => $ticket->assign_at,
                    'assign_name'          => $ticket->assign_name,
                    'pic_id'               => $ticket->pic_id,
                    'pic_name'             => $ticket->pic_name,
                    'is_mine'              => in_array((string) $ticket->pic_id, $subjectIds, true),
                    'pic_started_at'       => $ticket->pic_started_at,
                    'finish_at'            => $ticket->finish_at,
                    'closed_at'            => $ticket->closed_at,
                    'actual_hours'         => $ticket->actual_hours,
                    'expected_hours'       => $ticket->expected_hours,
                    'variance_hours'       => $ticket->variance_hours,
                    'overrun_flag'         => (bool) $ticket->overrun_flag,
                    'feedback_cycles'      => (int) ($ticket->feedback_cycles ?? 0),
                    'ticket_status'        => $ticket->ticket_status,
                    'is_root'              => $isRoot,
                    'has_parent'           => $hasParent,
                    'has_root'             => $hasRoot,
                    'parent_ticket'        => $parentObj ? [
                        'id'                   => $parentObj->id,
                        'ticket_id'            => $parentObj->id,
                        'menu_id'              => $parentObj->ticket_menu_id,
                        'ticket_menu_id'       => $parentObj->ticket_menu_id,
                        'link_url'             => url('/ticket/lihat/' . ($parentObj->ticket_menu_id ?: '1') . '/' . $parentObj->id),
                        'nomor_ticket'         => $parentObj->nomor_ticket,
                        'menu_name'            => $parentObj->ticketMenu?->nama_menu,
                        'submenu_name'         => $parentObj->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $parentObj->ticketMenu?->kategori ?? 1,
                        'requestor_name'       => $parentObj->requestor_name,
                        'created_at'           => $parentObj->created_at,
                        'ticket_status'        => $parentObj->ticket_status,
                    ] : null,
                    'root_ticket'          => $rootObj ? [
                        'id'                   => $rootObj->id,
                        'ticket_id'            => $rootObj->id,
                        'menu_id'              => $rootObj->ticket_menu_id,
                        'ticket_menu_id'       => $rootObj->ticket_menu_id,
                        'link_url'             => url('/ticket/lihat/' . ($rootObj->ticket_menu_id ?: '1') . '/' . $rootObj->id),
                        'nomor_ticket'         => $rootObj->nomor_ticket,
                        'menu_name'            => $rootObj->ticketMenu?->nama_menu,
                        'submenu_name'         => $rootObj->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $rootObj->ticketMenu?->kategori ?? 1,
                        'requestor_name'       => $rootObj->requestor_name,
                        'created_at'           => $rootObj->created_at,
                        'ticket_status'        => $rootObj->ticket_status,
                    ] : null,
                    'child_tickets'        => $allChildren->map(fn($c) => [
                        'id'                   => $c->id,
                        'ticket_id'            => $c->id,
                        'menu_id'              => $c->ticket_menu_id,
                        'ticket_menu_id'       => $c->ticket_menu_id,
                        'link_url'             => url('/ticket/lihat/' . ($c->ticket_menu_id ?: '1') . '/' . $c->id),
                        'nomor_ticket'         => $c->nomor_ticket,
                        'menu_name'            => $c->ticketMenu?->nama_menu,
                        'submenu_name'         => $c->ticketSubMenu?->nama_sub_menu,
                        'environment_kategori' => $c->ticketMenu?->kategori ?? 1,
                        'pic_name'             => $c->pic_name,
                        'ticket_status'        => $c->ticket_status,
                        'created_at'           => $c->created_at,
                    ])->values(),
                    'chain_info'           => $chainInfo,
                    'checkers'             => $ticket->checkers->map(fn($chk) => [
                        'id'                   => $chk->id,
                        'karyawan_id'          => $chk->karyawan_id,
                        'karyawan_name'        => $chk->karyawan_name ?? ($chk->karyawan?->name ?? '-'),
                        'signature'            => $chk->signature ? asset('storage/' . $chk->signature) : null,
                        'status'               => $chk->status,
                        'is_cross_environment' => (bool) ($chk->dependencyRule?->is_cross_environment ?? false),
                        'environment_name'     => $chk->branchSubMenu?->ticketMenu?->kategori_display ?? null,
                        'branch_sub_menu_name' => $chk->branchSubMenu?->nama_sub_menu ?? null,
                        'approved_at'          => $chk->approved_at ?? ($chk->status === 'completed' || $chk->status === 'approved' ? $chk->updated_at : null),
                    ])->values(),
                    'lampiran'        => $ticket->lampiran ? asset('storage/' . $ticket->lampiran) : null,
                    'attachments'     => $ticket->attachments->map(fn($att) => [
                        'id'            => $att->id,
                        'original_name' => $att->original_name,
                        'file_name'     => $att->file_name,
                        'file_path'     => asset('storage/' . $att->file_path),
                        'file_size'     => $att->file_size,
                        'mime_type'     => $att->mime_type,
                    ])->values(),
                    'work_sessions'   => $ticket->workSessions->map(fn($ws) => [
                        'id'               => $ws->id,
                        'cycle_number'     => $ws->cycle_number,
                        'session_type'     => $ws->session_type,
                        'started_at'       => $ws->started_at ? (is_string($ws->started_at) ? $ws->started_at : $ws->started_at->format('d/m/Y H:i')) : null,
                        'ended_at'         => $ws->ended_at ? (is_string($ws->ended_at) ? $ws->ended_at : $ws->ended_at->format('d/m/Y H:i')) : null,
                        'duration_minutes' => $ws->duration_minutes,
                        'notes'            => $ws->notes,
                        'pic_name'         => $ws->pic?->name ?? $ticket->pic_name,
                    ])->values(),
                    'status_label'    => $this->ticketStatusLabel($ticket->ticket_status),
                    'details'         => $ticket->ticketDetails->map(fn($detail) => [
                        'nomor_detail'            => $detail->nomor_detail,
                        'tanggal_transaksi'       => $detail->tanggal_transaksi,
                        'line_detail'             => $detail->line_detail,
                        'alasan_ubah'             => $detail->alasan_ubah,
                        'before_ubah'             => $detail->before_ubah,
                        'after_ubah'              => $detail->after_ubah,
                        'reference_ticket_detail' => $detail->referenceTicketDetail ? [
                            'nomor_detail'      => $detail->referenceTicketDetail->nomor_detail,
                            'tanggal_transaksi' => $detail->referenceTicketDetail->tanggal_transaksi,
                        ] : null,
                        'sub_details'             => $detail->subDetails->map(fn($s) => [
                            'id'                => $s->id,
                            'is_cancelled'      => (bool) $s->is_cancelled,
                            'cancel_reason'     => $s->cancel_reason,
                            'cancelled_by_role' => $s->cancelled_by_role,
                            'cancelled_by_name' => $s->cancelled_by_name,
                            'line_number' => $s->line_number,
                            'alasan_ubah' => $s->alasan_ubah,
                            'before_ubah' => $s->before_ubah,
                            'after_ubah'  => $s->after_ubah,
                        ])->values(),
                    ])->values(),
                    'comments'        => $ticket->comments->map(fn($comment) => [
                        'level'   => $comment->approval_level,
                        'action'  => $comment->action_ticket,
                        'name'    => $comment->karyawan?->name ?? '-',
                        'text'    => $comment->comment_text,
                        'date'    => $comment->comment_at,
                    ])->values(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }
    public function updatePicTicketStatus(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'action'         => 'required|in:start,finish,cancel,pending,resume',
            'note'           => 'nullable|string|max:1000',
        ], [
            'action.required' => 'Aksi status ticket wajib ditentukan.',
            'action.in'       => 'Aksi yang dipilih tidak valid.',
            'note.string'     => 'Catatan harus berupa teks.',
            'note.max'        => 'Catatan maksimal 1000 karakter.',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }
        if (in_array($request->action, ['finish', 'cancel', 'pending'], true) && empty(trim((string) $request->note))) {
            $noteLabel = [
                'cancel'  => 'Alasan pembatalan ticket wajib diisi pada catatan.',
                'finish'  => 'Catatan penyelesaian pekerjaan wajib diisi.',
                'pending' => 'Alasan pending',
            ][$request->action];
            return response()->json([
                'code'    => 422,
                'message' => $noteLabel,
                'errors'  => ['note' => [$noteLabel]],
            ], 422);
        }
        try {
            $karyawan = $this->currentKaryawan();
            if (!$this->isActivePic($karyawan)) {
                return response()->json(['code' => 403, 'message' => 'Anda tidak memiliki akses sebagai PIC.']);
            }
            $ticket = null;
            DB::transaction(function () use ($request, $ticketId, $karyawan, &$ticket) {
                $ticket = TicketHead::lockForUpdate()
                    ->where('pic_id', $karyawan->id)
                    ->findOrFail($ticketId);
                if (in_array($ticket->ticket_status, [
                    TicketHead::STATUS_CLOSED,
                    TicketHead::STATUS_CANCELLED,
                ], true)) {
                    throw new \Exception('Ticket sudah tidak dapat diproses.');
                }
                $allowedStatuses = [
                    'start'  => [
                        TicketHead::STATUS_APPROVED,
                        TicketHead::STATUS_WAITING_VALIDATOR,
                        TicketHead::STATUS_WAITING_ASSIGNER,
                        TicketHead::STATUS_ASSIGNED,
                        TicketHead::STATUS_FEEDBACK_REQUESTOR,
                    ],
                    'finish' => [
                        TicketHead::STATUS_APPROVED,
                        TicketHead::STATUS_WAITING_VALIDATOR,
                        TicketHead::STATUS_WAITING_ASSIGNER,
                        TicketHead::STATUS_ASSIGNED,
                        TicketHead::STATUS_IN_PROGRESS,
                        TicketHead::STATUS_FEEDBACK_REQUESTOR,
                    ],
                    'cancel' => [
                        TicketHead::STATUS_APPROVED,
                        TicketHead::STATUS_WAITING_VALIDATOR,
                        TicketHead::STATUS_WAITING_ASSIGNER,
                        TicketHead::STATUS_ASSIGNED,
                        TicketHead::STATUS_IN_PROGRESS,
                        TicketHead::STATUS_FEEDBACK_REQUESTOR,
                        TicketHead::STATUS_PENDING_EXTERNAL,
                    ],
                    // PIC sedang mengerjakan tapi mandek menunggu aksi pihak lain
                    // (mis. posting oleh JM Finance) -> hanya bisa dari status "sedang dikerjakan".
                    'pending' => [
                        TicketHead::STATUS_IN_PROGRESS,
                    ],
                    // Pihak lain sudah selesai -> PIC lanjutkan pengerjaan.
                    'resume' => [
                        TicketHead::STATUS_PENDING_EXTERNAL,
                    ],
                ];
                if (!in_array($ticket->ticket_status, $allowedStatuses[$request->action] ?? [], true)) {
                    throw new \Exception('Status ticket tidak valid untuk tindakan ini.');
                }
                $kpiService = app(\App\Services\TicketKpiService::class);
                if ($request->action === 'start') {
                    $kpiService->startWork($ticket, $karyawan->id, $request->note);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => Auth::user()->username,
                        'menu_id' => null,
                        'menu_name' => 'PIC Ticket',
                        'id_table' => $ticket->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'ticket_head',
                        'activity' => 'Mulai dikerjakan',
                        'description' => 'Mulai Mengerjakan Ticket: ' . $ticket->nomor_ticket,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $this->saveApprovalComment($ticket->id, 'pic', $karyawan, 'start', $request->note);
                    return;
                }
                if ($request->action === 'pending') {
                    $kpiService->markPendingExternal($ticket, $request->note);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => Auth::user()->username,
                        'menu_id' => null,
                        'menu_name' => 'PIC Ticket',
                        'id_table' => $ticket->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'ticket_head',
                        'activity' => 'Pending',
                        'description' => 'Ticket ' . $ticket->nomor_ticket . ' pending, alasan: ' . $request->note,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $this->saveApprovalComment($ticket->id, 'pic', $karyawan, 'pending', $request->note);
                    return;
                }
                if ($request->action === 'resume') {
                    $kpiService->resumeFromPending($ticket, $request->note);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => Auth::user()->username,
                        'menu_id' => null,
                        'menu_name' => 'PIC Ticket',
                        'id_table' => $ticket->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'ticket_head',
                        'activity' => 'Lanjutkan Pengerjaan',
                        'description' => 'Ticket ' . $ticket->nomor_ticket . ' dilanjutkan setelah pending.',
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $this->saveApprovalComment($ticket->id, 'pic', $karyawan, 'resume', $request->note);
                    return;
                }
                if ($request->action === 'finish') {
                    $kpiService->markWaitingRequestor($ticket, $request->note);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => Auth::user()->username,
                        'menu_id' => null,
                        'menu_name' => 'PIC Ticket',
                        'id_table' => $ticket->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'ticket_head',
                        'activity' => 'Selesai dikerjakan',
                        'description' => 'Selesai Mengerjakan TIcket: ' . $ticket->nomor_ticket,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);

                    $this->saveApprovalComment($ticket->id, 'pic', $karyawan, 'finish', $request->note);
                    return;
                }
                $kpiService->cancel($ticket);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => Auth::user()->username,
                    'menu_id' => null,
                    'menu_name' => 'PIC Ticket',
                    'id_table' => $ticket->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'ticket_head',
                    'activity' => 'Dibatalkan',
                    'description' => 'Ticket: ' . $ticket->nomor_ticket . ' Dibatalkan',
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
                $this->saveApprovalComment($ticket->id, 'pic', $karyawan, 'cancel', $request->note);
            });

            if ($request->action === 'finish' && $ticket) {
                app(TicketApprovalEmailService::class)->sendNextActionNotification(
                    $ticket->fresh(),
                    $ticket->ticket_menu_id,
                    'waiting_requestor'
                );
            }
            $messages = [
                'start'   => 'Status ticket berhasil diubah menjadi Sedang Dikerjakan.',
                'finish'  => 'Ticket berhasil dikirim kembali ke pengaju untuk dicek.',
                'cancel'  => 'Ticket berhasil dibatalkan.',
                'pending' => 'Ticket ditandai Pending, jam pengerjaan PIC dijeda.',
                'resume'  => 'Ticket dilanjutkan, jam pengerjaan PIC berjalan kembali.',
            ];
            return response()->json([
                'code'    => 200,
                'message' => $messages[$request->action] ?? 'Status ticket berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }
    private function isActivePic(?Karyawan $karyawan): bool
    {
        if (!$karyawan) {
            return false;
        }
        return TicketAssignmentMaster::where('karyawan_id', $karyawan->id)
            ->where('assignment_type', 'A02')
            ->where('is_active', 1)
            ->exists();
    }
    private function ticketStatusLabel(?string $status): string
    {
        return [
            TicketHead::STATUS_DRAFT              => 'Draft',
            TicketHead::STATUS_SUBMITTED          => 'Menunggu Persetujuan',
            TicketHead::STATUS_APPROVED           => 'Disetujui',
            TicketHead::STATUS_ASSIGNED           => 'Assigned ke PIC',
            TicketHead::STATUS_IN_PROGRESS        => 'Sedang Dikerjakan',
            TicketHead::STATUS_WAITING_REQUESTOR  => 'Menunggu Cek Pengaju',
            TicketHead::STATUS_CLOSED             => 'Selesai',
            TicketHead::STATUS_FEEDBACK_REQUESTOR => 'Feedback Pengaju',
            TicketHead::STATUS_PENDING_EXTERNAL   => 'Pending',
            TicketHead::STATUS_CANCELLED          => 'Ticket Dibatalkan',
            TicketHead::STATUS_WAITING_APPROVAL1  => 'Disetujui Atasan 1',
            TicketHead::STATUS_WAITING_APPROVAL2  => 'Disetujui Atasan 2',
            TicketHead::STATUS_WAITING_VALIDATOR  => 'Menunggu Divalidasi',
            TicketHead::STATUS_WAITING_CHECKER    => 'Menunggu Pemeriksaan',
            TicketHead::STATUS_WAITING_ASSIGNER   => 'Menunggu Penugasan',
        ][$status] ?? '-';
    }

    private function tryFinalizeApproval(TicketHead $ticket): void
    {
        $hasPendingInternalCheckers = TicketApproval::where('ticket_id', $ticket->id)
            ->where('role', 'checker')
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('dependency_rule_id')
                    ->orWhereHas('dependencyRule', function ($q2) {
                        $q2->where('is_cross_environment', false);
                    });
            })
            ->exists();

        if ($hasPendingInternalCheckers) {
            return;
        }
        $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;
        $approve2Done = is_null($ticket->approve2_id) || (bool) $ticket->approve2;
        if (!$approve1Done || !$approve2Done) {
            return;
        }

        $updateData = [
            'approval_status' => TicketHead::APPROVAL_APPROVED,
        ];

        // Status yang sudah ditentukan oleh alur approval/proses berikutnya
        // TIDAK boleh ditimpa kembali menjadi STATUS_APPROVED.
        // Contoh bug: tiket tanpa checker sudah di-set STATUS_WAITING_VALIDATOR (T11)
        // oleh saveApproveTicket(), lalu tertimpa di sini.
        $protectedStatuses = [
            TicketHead::STATUS_WAITING_CHECKER,
            TicketHead::STATUS_WAITING_VALIDATOR,
            TicketHead::STATUS_WAITING_ASSIGNER,
            TicketHead::STATUS_ASSIGNED,
            TicketHead::STATUS_IN_PROGRESS,
            TicketHead::STATUS_PENDING_EXTERNAL,
            TicketHead::STATUS_WAITING_REQUESTOR,
            TicketHead::STATUS_CLOSED,
            TicketHead::STATUS_FEEDBACK_REQUESTOR,
            TicketHead::STATUS_CANCELLED,
        ];
        if (!in_array($ticket->ticket_status, $protectedStatuses, true)) {
            $updateData['ticket_status'] = TicketHead::STATUS_APPROVED;
        }
        $ticket->update($updateData);
    }

    private function hasInternalCheckers(TicketHead $ticket): bool
    {
        return !empty($ticket->checker_id) || TicketApproval::where('ticket_id', $ticket->id)
            ->where('role', 'checker')
            ->where(function ($q) {
                $q->whereNull('dependency_rule_id')
                    ->orWhereHas('dependencyRule', function ($q2) {
                        $q2->where('is_cross_environment', false);
                    });
            })
            ->exists();
    }

    private function isTerminalTicket(TicketHead $ticket): bool
    {
        $hasActiveChild = TicketHead::where('parent_ticket_id', $ticket->id)
            ->whereNotIn('approval_status', [
                TicketHead::APPROVAL_APPROVED,
                TicketHead::APPROVAL_REJECTED,
            ])
            ->exists();

        return !$hasActiveChild;
    }


    /**
     * @deprecated Silahkan gunakan TicketVendorApprovalController::index()
     */
    public function vendorApprovalIndex($menuId)
    {
        return app(TicketVendorApprovalController::class)->index($menuId);
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorApprovalController::list()
     */
    public function getListVendorApproval($menuId)
    {
        return app(TicketVendorApprovalController::class)->list($menuId);
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorApprovalController::show()
     */
    public function showVendorApproval($menuId, $vendorId)
    {
        return app(TicketVendorApprovalController::class)->show($menuId, $vendorId);
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorApprovalController::save()
     */
    public function saveVendorApproval(Request $request, $vendorId)
    {
        return app(TicketVendorApprovalController::class)->save($request, $vendorId);
    }

    private function resolveEnvironmentKategori(?TicketHead $ticket): array
    {
        if (!$ticket) {
            return [1, 'Epicor ERP'];
        }
        $kat = (int) ($ticket->ticketMenu?->kategori ?? 0);
        $nomor = $ticket->nomor_ticket ?? '';
        if (str_contains($nomor, 'AOL-AJA')) {
            $kat = 3;
        } elseif (str_contains($nomor, 'AOL-SP') || str_contains($nomor, 'AOL-SPS')) {
            $kat = 2;
        } elseif (str_contains($nomor, 'EPIC')) {
            $kat = 1;
        } elseif ($kat === 0) {
            $kat = 1;
        }
        $name = match ($kat) {
            1 => 'Epicor ERP',
            2 => 'Accurate Online SP',
            3 => 'Accurate Online AJA',
            default => 'Epicor ERP',
        };
        return [$kat, $name];
    }
}
