<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalUser;
use App\Models\Holding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\MappingShiftTukar;
use App\Models\MappingShiftTukarAtasan;
use App\Models\MappingShiftTukarAtasanDetail;
use App\Models\MappingShiftTukarDetail;
use App\Models\Menu;
use App\Models\User;
use App\Models\Shift;
use App\Models\StrukturModel;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;





class TukarShiftController extends Controller
{
    // Tukar shift yang diajukan oleh karyawan
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
        $jabatan_karyawan = KaryawanJabatan::with('Jabatan.LevelJabatan')->where('karyawan_id', $user_karyawan->id)->where('is_active', 1)->where('main', 1)->first();
        $levelPengaju = (int) $jabatan_karyawan->Jabatan->LevelJabatan->level_jabatan;
        // dd($levelPengaju);
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
        // dd($access, $id);
        if ($access == null) {
            return response()->json([
                'code' => 404,
                'message' => 'Access not found, Hubungi Admin'
            ]);
        }
        $currentApprovalId = $access->Approval->parent_approval_id;
        $atasan1 = null;
        $atasan2 = null;
        $found = 0;

        // dd($currentApprovalId);

        while ($currentApprovalId && $found < 2) {

            $approvalUser = ApprovalUser::with(['User', 'Karyawan', 'Approval'])
                ->whereHas('Approval', function ($q) use ($currentApprovalId) {
                    $q->where('id_approval', $currentApprovalId);
                })
                ->whereHas('Karyawan')
                ->first();

            if ($approvalUser) {



                // default behavior
                if ($found === 0) {
                    $atasan1 = $approvalUser;
                } elseif ($found === 1) {
                    $atasan2 = $approvalUser;
                }

                $found++;
            }

            $parentApproval = Approval::where('id_approval', $currentApprovalId)->first();
            $currentApprovalId = $parentApproval?->parent_approval_id;
            // dd($parentApproval, $currentApprovalId);
        }
        // fallback: kalau atasan 2 kosong → samakan dengan atasan 1
        if (!$atasan2) {
            $atasan2 = $atasan1;
        }
        // dd($atasan1, $atasan2);
        $menu_active = User::where('id', Auth::user()->id)->first();
        $menu_active->menu_active = $id;
        $menu_active->save();
        // dd($atasan1);
        $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $atasan1->Karyawan->id)->orderBy('created_at', 'desc')->first();
        // dd($get_approval);
        if ($get_approval?->approval1 == 1) {
            $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $atasan1->Karyawan->id)->count();
        } elseif ($get_approval?->approval1 == 2) {
            $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $atasan1->Karyawan->id)->count();
        } else {
            $badge_pegawai_ubah = 0;
            // dd($badge_pegawai_ubah);
        }
        // $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $atasan1->Karyawan->id)->count();
        // dd($badge_pegawai_ubah);
        // $pengaju = MappingShiftTukar::where('id_pengaju', $user_karyawan->id)->first();
        // dd($user_karyawan->name, $pengaju->nama_pengaju);
        return view('users.tukar_shift.index', [
            'shift' => $shift,
            'days' => $days,
            'menu_active' => $id,
            'atasan1' => $atasan1,
            'atasan2' => $atasan2,
            'all_menus' => $all_menus,
            'user_karyawan' => $user_karyawan,
            'all_menu_bar' => $all_menu_bar,
            // 'pengaju' => $pengaju,
            'badge_pegawai_ubah' => $badge_pegawai_ubah
        ]);
    }
    public function getTukarShiftData(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
        $badge = MappingShiftTukar::where('id_pengaju', $user_karyawan->id)->first();
        $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $badge?->id_approval1)->orderBy('created_at', 'desc')->first();
        if ($get_approval?->approval1 == 1) {
            $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $badge->id_approval1)->count();
        } elseif ($get_approval?->approval1 == 2) {
            $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $badge->id_approval1)->count();
        } else {
            $badge_pegawai_ubah = 0;
        }


        // dd($badge_pegawai_ubah);
        $data = MappingShift::with('KaryawanApproved', 'Shift')
            ->whereBetween('tanggal_masuk', array($start, $end))
            ->whereNotNull('status_approved')
            ->whereNotNull('is_active')
            ->where('karyawan_id', $user_karyawan->id)
            // ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            $grouped[$key][] = [
                'id_mapping' => $item->id,
                'shift_id' => $item->shift_id,
                'name' => $item->nama_karyawan,
                'shift' => $item->nama_shift,
                'tanggal_input' => Carbon::parse($item->created_at)->format('d-m-Y'),
                'tanggal_approved' => Carbon::parse($item->approved_at)->format('d-m-Y'),
                'tanggal_masuk' => Carbon::parse($item->tanggal_masuk)->format('d-m-Y'),
                'tanggal_alf' => Carbon::parse($item->tanggal_masuk)->translatedFormat('l'),
                'status_approved' => $item->status_approved,
                'is_active' => $item->is_active,
                'jam_shift' => '(' . $item->Shift->jam_masuk . ' - ' . $item->Shift->jam_keluar . ')', // ' . $item->Shift->jam_masuk . ' - ' . $item->Shift->jam_keluar
            ];
        }

        // dd($grouped);
        return response()->json([
            'alert' => $badge_pegawai_ubah,
            'data' => $grouped
        ]);
    }
    public function getTukarShiftData_pengajuan($id, Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
        // dd($start);
        $data = MappingShiftTukar::where('id_approval1', $id)
            ->whereBetween('created_at', array($start, $end))
            ->get();
        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            $id_mapping = $item->id;
            $grouped[$key][] = [
                'id_mapping' => $id_mapping ?? '-',
                'id_pengaju' => $user_karyawan->id ?? '-',
                'sign_pengaju' => $item->sign_pengaju ?? '-',
                'approval1' => $item->approval1 ?? '-',
                'approval2' => $item->approval2 ?? '',
                'alasan' => $item->alasan ?? '-',
                'no_form' => $item->no_form ?? '-',
                'created_at' => $item->created_at->format('Y-m-d') ?? '-',
            ];
        }

        // dd($grouped);
        return response()->json($grouped);
    }
    public function getTukarShiftData_canvas(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $start = Carbon::today()->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16

        // dd($start, $end);

        $data = MappingShift::with('KaryawanApproved', 'Shift')
            ->whereBetween('tanggal_masuk', array($start, $end))
            ->whereNotNull('status_approved')
            ->whereNotNull('is_active')
            ->where('karyawan_id', $user_karyawan->id)
            // ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            $hari = $item->tanggal_masuk;
            $legal = $item->legal_number_mapping;
            $grouped[$key] = [
                'id_mapping' => $item->id,
                'shift_id' => $item->shift_id,
                'name' => $item->nama_karyawan,
                'shift' => $item->nama_shift,
                'tanggal_input' => Carbon::parse($item->created_at)->format('d-m-Y'),
                'tanggal_approved' => Carbon::parse($item->approved_at)->format('d-m-Y'),
                'tanggal_masuk' => Carbon::parse($item->tanggal_masuk)->format('d-m-Y'),
                'tanggal_alf' => Carbon::parse($item->tanggal_masuk)->translatedFormat('l'),
                'tukar' => MappingShift::with('KaryawanApproved', 'Shift')
                    ->where('tanggal_masuk', $hari)
                    ->whereNotNull('status_approved')
                    ->whereNotNull('is_active')
                    ->where('legal_number_mapping', $legal)
                    ->orderBy('tanggal_masuk', 'asc')
                    ->get(),
                'status_approved' => $item->status_approved,
                'is_active' => $item->is_active,
                'jam_shift' => '(' . $item->Shift->jam_masuk . ' - ' . $item->Shift->jam_keluar . ')', // ' . $item->Shift->jam_masuk . ' - ' . $item->Shift->jam_keluar
            ];
        }

        // dd($grouped);
        return response()->json([
            'code' => 200,
            'data' => $grouped,
        ]);
    }
    public function ubah_shift(Request $request)
    {
        // dd($request->all());
        $rules = [
            'alasan'                => 'required',
        ];
        $customessages =
            [
                'required'           => ':attribute tidak boleh kosong!',
            ];

        try {
            $validator = Validator::make($request->all(), $rules, $customessages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ]);
            }
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $kode = Uuid::uuid4();

            $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $request->atasan_1)->orderBy('created_at', 'desc')->first();
            if ($get_approval?->approval1 == 1) {
                $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $request->atasan_1)->count();
            } elseif ($get_approval?->approval1 == 2) {
                $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $request->atasan_1)->count();
            } else {
                $badge_pegawai_ubah = 0;
            }

            // dd($mapping_tukar);
            // dd($mapping_id_head->id_head);

            if ($badge_pegawai_ubah == 0) {
                $shift_id = $request->shift[0];
                $mapping_id_head = MappingShift::where('id', $shift_id)->first();
                $mapping_tukar = MappingShiftTukarAtasanDetail::where('id_head', $mapping_id_head->id_head)->whereNull('id_tukar_atasan_utama')->exists();
                if ($mapping_tukar) {
                    MappingShiftTukarAtasanDetail::where('id_head', $mapping_id_head->id_head)->whereNull('id_tukar_atasan_utama')->delete();
                }
                $tukar = [
                    'id' => $kode,
                    'id_pengaju' => $request->id_karyawan,
                    'nama_pengaju' => $request->name,
                    'sign_pengaju' => $fileName,
                    'alasan' => $request->alasan,
                    'approval1' => null,
                    'id_approval1' => $request->atasan_1,
                    'nama_approval1' => $request->atasan_1_name,
                    'approval2' => null,
                    // 'id_approval2' => $request->atasan_2,
                    // 'nama_approval2' => $request->atasan_2_name,
                    'alasan' => $request->alasan,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $detail_tukar = [];
                $index = 0;
                $pilihan_tukar = array_values(array_filter($request->pilihan_tukar ?? []));
                $shift =  $request->shift;
                foreach ($shift as $ss) {
                    // skip kalau tidak ada pasangan


                    $shift_penukar = MappingShift::where('id', $ss)
                        ->whereNotNull('is_active')
                        ->first();
                    $shift_tertukar = MappingShift::where('id', $pilihan_tukar[$index])
                        ->whereNotNull('is_active')
                        ->first();

                    // dd($shift_tertukar, $pilihan_tukar);

                    // if (!isset($pilihan_tukar[$index]) || ($pilihan_tukar[$index])) {
                    //     continue;
                    // }

                    array_push($detail_tukar, [
                        'id' => Uuid::uuid4(),
                        'id_tukar_utama' => $kode,
                        'karyawan_penukar_id' => $shift_penukar->karyawan_id,
                        'karyawan_penukar_name' => $shift_penukar->nama_karyawan,
                        'nama_shift_penukar' => $shift_penukar->nama_shift,
                        'shift_id_penukar' => $shift_penukar->id,
                        'karyawan_tertukar_id' => $shift_tertukar->karyawan_id,
                        'karyawan_tertukar_name' => $shift_tertukar->nama_karyawan,
                        'nama_shift_tertukar' => $shift_tertukar->nama_shift,
                        'shift_id_tertukar' => $shift_tertukar->id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $index++;
                }
                // dd($shift_mapping, $shift_pilihan);
                // dd($detail_tukar);
                MappingShiftTukar::insert($tukar);
                MappingShiftTukarDetail::insert($detail_tukar);

                $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
                $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $kode,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'mapping_shift_tukar dan mapping_shift_tukar_detail',
                    'activity' => 'update',
                    'description' => 'Mengajukan perubahan shift pribadi, alasan : ' . $request->alasan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);

                // $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
                // $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
                // $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16

                // $badge = MappingShiftTukar::where('id_pengaju', $user_karyawan->id)->first();
                $alert = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $request->atasan_1)->count();

                $data = MappingShiftTukar::where('id_approval1', $request->atasan_1)
                    ->whereBetween('created_at', array($request->start, $request->end))
                    ->get();
                // ubah ke format seperti di JS
                // dd($request->start, $request->end);
                // dd($data);
                $grouped = [];
                foreach ($data as $key => $item) {
                    $id_mapping = $item->id;
                    $grouped[$key][] = [
                        'id_mapping' => $id_mapping ?? '-',
                        'id_pengaju' => $request->id_karyawan ?? '-',
                        'alasan' => $item->alasan ?? '-',
                        'no_form' => $item->no_form ?? '-',
                        'sign_pengaju' => $item->sign_pengaju ?? '-',
                        'created_at' => $item->created_at->format('Y-m-d') ?? '-',
                        'tukar_detail' => MappingShiftTukarDetail::where('id_tukar_utama', $id_mapping)
                            ->get(),
                    ];
                }
                // dd($grouped);
                return response()->json([
                    'code' => 200,
                    // 'data' => $get_data,
                    // 'data_keahlian' => $data_keahlian,
                    'message' => 'Data Berhasil Diupdate',
                    'grouped' => $grouped,
                    'alert' => $alert,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function hapus_ubah_shift(Request $request)
    {
        // dd($request->all());
        try {
            $fileName = $request->sign_pengaju;
            $filePath = 'signatures/' . $fileName;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $mapping_tukar = MappingShiftTukar::where('id', $request->id_mapping_delete)->first();
            $mapping_tukar_detail = MappingShiftTukarDetail::where('id_tukar_utama', $request->id_mapping_delete)->first();

            $karyawan_id = Karyawan::where('id', $mapping_tukar->id_pengaju)->first();

            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_delete,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar dan mapping_shift_tukar_detail',
                'activity' => 'delete',
                'description' => 'Menghapus pengajuan tukar shift pribadi',
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $mapping_tukar->delete();
            $mapping_tukar_detail->delete();

            $badge = MappingShiftTukar::where('id_pengaju', $karyawan_id->id)->first();
            $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $badge->id_approval1)->orderBy('created_at', 'desc')->first();
            if ($get_approval?->approval1 == 1) {
                $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $badge->id_approval1)->count();
                // dd($badge_pegawai_ubah);
            } elseif ($get_approval?->approval1 == 2) {
                $badge_pegawai_ubah = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $badge->id_approval1)->count();
            } else {
                $badge_pegawai_ubah = 0;
            }


            $data = MappingShiftTukar::where('id_approval1', $badge->id_approval1)
                ->whereBetween('created_at', array($request->start, $request->end))
                ->get();
            // ubah ke format seperti di JS
            // dd($request->start, $request->end);
            // dd($data);

            $grouped = [];
            foreach ($data as $key => $item) {
                $id_mapping = $item->id;
                $grouped[$key][] = [
                    'id_mapping' => $id_mapping ?? '-',
                    'id_pengaju' => $request->id_pengaju_delete ?? '-',
                    'sign_pengaju' => $item->sign_pengaju ?? '-',
                    'alasan' => $item->alasan ?? '-',
                    'no_form' => $item->no_form ?? '-',
                    'created_at' => $item->created_at->format('Y-m-d') ?? '-',
                    'tukar_detail' => MappingShiftTukarDetail::where('id_tukar_utama', $id_mapping)
                        ->get(),
                ];
            }
            // dd($grouped);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                'message' => 'Data Berhasil Dihapus',
                'alert' => $badge_pegawai_ubah,
                'grouped' => $grouped,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function get_detail_tukar(Request $request)
    {
        // dd($request->all());
        try {
            $data = MappingShiftTukarDetail::with('MappingShiftPenukar')
                ->with('MappingShiftTertukar')
                ->where('id_tukar_utama', $request->id_map)
                ->get();
            $data_utama = MappingShiftTukar::where('id', $request->id_map)
                ->first();
            // dd($data_utama);z
            return response()->json([
                'code' => 200,
                'data' => $data,
                'data_utama' => $data_utama,
                // 'data_keahlian' => $data_keahlian,
                'message' => 'Data Berhasil Ditampilkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Approval 1
    public function getTukarShiftData_atasan(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        // dd($user_karyawan);
        $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
        // dd($start);
        $data = MappingShiftTukar::where('id_approval1', $user_karyawan->id)
            ->whereBetween('created_at', array($start, $end))
            ->get();
        // dd($data);

        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            $id_mapping = $item->id;
            $grouped[$key] = [
                'id_mapping' => $id_mapping ?? '-',
                'id_pengaju' => $user_karyawan->id ?? '-',
                'nama_pengaju' => $item->nama_pengaju ?? '-',
                'sign_pengaju' => $item->sign_pengaju ?? '-',
                'approval1' => $item->approval1 ?? '-',
                'approval2' => $item->approval2 ?? '',
                'alasan' => $item->alasan ?? '-',
                'no_form' => $item->no_form ?? '-',
                'created_at' => $item->created_at->format('Y-m-d') ?? '-',
            ];
        }

        // dd($grouped);
        return response()->json($grouped);
    }
    public function ubah_shift_atasan(Request $request)
    {
        // dd($request->all());
        try {
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $mapping_shift_tukar = MappingShiftTukar::where('id', $request->id_mapping_tukar)->first();
            $karyawan_id = Karyawan::where('id', $mapping_shift_tukar->id_approval1)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $no_form_get = MappingShiftTukar::select('no_form')->whereNotNull('no_form')->latest('updated_at')->first();
            // dd($no_form_get);
            if ($no_form_get == null) {
                $no = '001';
            } else {
                $legal_terakhir = (int) substr($no_form_get->no_form, -3);;
                $hasil = $legal_terakhir + 1;
                $no = str_pad($hasil, 3, '0', STR_PAD_LEFT);
            }
            $mapping_shift_tukar->update([
                'approval1' => '1',
                'sign_approval1' => $fileName,
                'no_form' => ($holdings->holding_category ?? '-') . '/TSL/' . Carbon::now()->format('Ymd') . '/' . $no
            ]);

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_tukar,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar',
                'activity' => 'update',
                'description' => 'Mengabulkan perubahan shift (sebagai atasan) ' . $mapping_shift_tukar->nama_pengaju,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan); contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
            // dd($start);
            $data = MappingShiftTukar::where('id_approval1', $user_karyawan->id)
                ->whereBetween('created_at', array($request->start, $request->end))
                ->get();
            // dd($data);

            // ubah ke format seperti di JS
            $grouped = [];
            foreach ($data as $key => $item) {
                $id_mapping = $item->id;
                $grouped[$key] = [
                    'id_mapping' => $id_mapping ?? '-',
                    'id_pengaju' => $user_karyawan->id ?? '-',
                    'nama_pengaju' => $item->nama_pengaju ?? '-',
                    'sign_pengaju' => $item->sign_pengaju ?? '-',
                    'approval1' => $item->approval1 ?? '-',
                    'approval2' => $item->approval2 ?? '-',
                    'alasan' => $item->alasan ?? '-',
                    'no_form' => $item->no_form ?? '-',
                    'created_at' => $item->created_at->format('Y-m-d') ?? '-',
                ];
            }
            return response()->json([
                'code' => 200,
                'grouped' => $grouped,
                // 'data_keahlian' => $data_keahlian,
                'message' => 'Data Berhasil Ditampilkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function tolak_ubah_shift_atasan(Request $request)
    {
        // dd($request->all());
        try {
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $mapping_shift_tukar = MappingShiftTukar::where('id', $request->id_mapping_tukar)->first();
            $mapping_shift_tukar->update([
                'approval1' => '2',
                'approval2' => '2',
                'sign_approval1' => $fileName,
            ]);
            $karyawan_id = Karyawan::where('id', $mapping_shift_tukar->id_approval1)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_tukar,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar',
                'activity' => 'update',
                'description' => 'Menolak perubahan shift (sebagai atasan) ' . $mapping_shift_tukar->nama_pengaju,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan); contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
            // dd($start);
            $data = MappingShiftTukar::where('id_approval1', $user_karyawan->id)
                ->whereBetween('created_at', array($request->start, $request->end))
                ->get();
            // dd($data);

            // ubah ke format seperti di JS
            $grouped = [];
            foreach ($data as $key => $item) {
                $id_mapping = $item->id;
                $grouped[$key] = [
                    'id_mapping' => $id_mapping ?? '-',
                    'id_pengaju' => $user_karyawan->id ?? '-',
                    'nama_pengaju' => $item->nama_pengaju ?? '-',
                    'sign_pengaju' => $item->sign_pengaju ?? '-',
                    'approval1' => $item->approval1 ?? '-',
                    'approval2' => $item->approval2 ?? '-',
                    'alasan' => $item->alasan ?? '-',
                    'no_form' => $item->no_form ?? '-',
                    'created_at' => $item->created_at->format('Y-m-d') ?? '-',
                ];
            }

            return response()->json([
                'code' => 200,
                'grouped' => $grouped,
                // 'data_keahlian' => $data_keahlian,
                'message' => 'Data Berhasil Ditampilkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    //Approval 1 end

    // Approval 2 
    public function getTukarShiftData_hrd(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        // dd($user_karyawan);
        $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
        // dd($start);
        $data = MappingShiftTukar::whereNotNull('approval1')
            ->where('approval1', 1)
            ->whereBetween('created_at', array($start, $end))
            ->get();
        // dd($data);

        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            $id_mapping = $item->id;
            $grouped[$key] = [
                'id_mapping' => $id_mapping ?? '-',
                'id_pengaju' => $user_karyawan->id ?? '-',
                'nama_pengaju' => $item->nama_pengaju ?? '-',
                'sign_pengaju' => $item->sign_pengaju ?? '-',
                'approval1' => $item->approval1 ?? '-',
                'approval2' => $item->approval2 ?? '-',
                'alasan' => $item->alasan ?? '-',
                'no_form' => $item->no_form ?? '-',
                'no_shift' => $item->no_shift ?? '-',
                'created_at' => $item->created_at->format('Y-m-d') ?? '-',
            ];
        }

        // dd($grouped);
        return response()->json($grouped);
    }
    public function ubah_shift_hrd(Request $request)
    {
        // $mapping_shift_tukar = MappingShiftTukar::where('id', $request->id_mapping_tukar)->first();
        // $mapping_shift_tukar_detail = MappingShiftTukarDetail::where('id_tukar_utama', $request->id_mapping_tukar)->first();
        // $mapping_penukar = MappingShift::where('id', $mapping_shift_tukar_detail->shift_id_penukar)->first();
        // $mapping_tertukar = MappingShift::where('id', $mapping_shift_tukar_detail->shift_id_tertukar)->first();
        // dd($mapping_penukar, $mapping_tertukar);
        // dd($request->all());
        try {
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $mapping_shift_tukar = MappingShiftTukar::where('id', $request->id_mapping_tukar)->first();
            $karyawan_id = Karyawan::where('id', $mapping_shift_tukar->id_approval1)->first();
            $karyawan_approval = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $mapping_shift_tukar->update([
                'approval2' => '1',
                'id_approval2' => $karyawan_approval->id,
                'nama_approval2' => $karyawan_approval->name,
                'sign_approval2' => $fileName,

            ]);

            $mapping_shift_tukar_detail = MappingShiftTukarDetail::where('id_tukar_utama', $request->id_mapping_tukar)->get();

            foreach ($mapping_shift_tukar_detail as $ms) {
                $penukar = MappingShift::where('id', $ms->shift_id_penukar)
                    ->whereNotNull('is_active')
                    ->first();
                $tertukar = MappingShift::where('id', $ms->shift_id_tertukar)
                    ->whereNotNull('is_active')
                    ->first();
                // dd($penukar, $tertukar);
                // edit penukar
                MappingShift::where('id', $ms->shift_id_penukar)->update([
                    'is_active' => null,
                    'id_tukar_shift_detail' => $ms->id,
                ]);
                MappingShift::create([
                    'legal_number_mapping' => $penukar->legal_number_mapping,
                    'karyawan_id' => $penukar->karyawan_id,
                    'nik_karyawan' => $penukar->nik_karyawan,
                    'nama_karyawan' => $penukar->nama_karyawan,

                    'shift_id' => $tertukar->shift_id,
                    'nama_shift' => $tertukar->nama_shift,

                    'input_by' => $penukar->input_by,
                    'approved_by' => $penukar->approved_by,
                    'status_approved' => $penukar->status_approved,

                    'tanggal_masuk' => $tertukar->tanggal_masuk,
                    'keterangan_absensi' => null,
                    'tanggal_pulang' =>  $tertukar->tanggal_pulang,

                    'jam_pulang' => null,
                    'pulang_cepat' => null,
                    'lembur' => null,
                    'keterangan_absensi_pulang' => null,
                    'total_jam_kerja' => null,
                    'status_absen' => null,
                    'kelengkapan_absensi' => null,
                    'izin_id' => null,
                    'cuti_id' => null,
                    'penugasaan_id' => null,
                    'keterangan_izin' => null,
                    'keterangan_cuti' => null,
                    'keterangan_dinas' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'approved_at' => now(),
                    'approved_signature' => $penukar->approved_signature,
                    'pengajuan_signature' => $penukar->pengajuan_signature,
                    'mapping_group' => $penukar->mapping_group,
                    'id_head' => $penukar->id_head,
                    'is_active' => 2,
                    'id_tukar_shift_detail' => $ms->id,
                ]);
                // tambah penukar


                //edit tertukar
                MappingShift::where('id', $ms->shift_id_tertukar)->update([
                    'is_active' => null,
                    'id_tukar_shift_detail' => $ms->id,
                ]);
                MappingShift::create([
                    'legal_number_mapping' => $tertukar->legal_number_mapping,
                    'karyawan_id' => $tertukar->karyawan_id,
                    'nik_karyawan' => $tertukar->nik_karyawan,
                    'nama_karyawan' => $tertukar->nama_karyawan,

                    'shift_id' => $penukar->shift_id,
                    'nama_shift' => $penukar->nama_shift,

                    'input_by' => $tertukar->input_by,
                    'approved_by' => $tertukar->approved_by,
                    'status_approved' => $tertukar->status_approved,

                    'tanggal_masuk' => $penukar->tanggal_masuk,
                    'keterangan_absensi' => null,
                    'tanggal_pulang' =>  $penukar->tanggal_pulang,

                    'jam_pulang' => null,
                    'pulang_cepat' => null,
                    'lembur' => null,
                    'keterangan_absensi_pulang' => null,
                    'total_jam_kerja' => null,
                    'status_absen' => null,
                    'kelengkapan_absensi' => null,
                    'izin_id' => null,
                    'cuti_id' => null,
                    'penugasaan_id' => null,
                    'keterangan_izin' => null,
                    'keterangan_cuti' => null,
                    'keterangan_dinas' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'approved_at' => now(),
                    'approved_signature' => $tertukar->approved_signature,
                    'pengajuan_signature' => $tertukar->pengajuan_signature,
                    'mapping_group' => $tertukar->mapping_group,
                    'id_head' => $tertukar->id_head,
                    'is_active' => 2,
                    'id_tukar_shift_detail' => $ms->id,
                ]);
            }

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_tukar,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar',
                'activity' => 'update',
                'description' => 'Mengabulkan perubahan shift (sebagai HRD) ' . $mapping_shift_tukar->nama_pengaju,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan); contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
            // dd($start);
            $data = MappingShiftTukar::where('id_approval2', $user_karyawan->id)
                ->whereNotNull('approval1')
                ->whereBetween('created_at', array($request->start, $request->end))
                ->get();
            // dd($data);

            // ubah ke format seperti di JS
            $grouped = [];
            foreach ($data as $key => $item) {
                $id_mapping = $item->id;
                $grouped[$key] = [
                    'id_mapping' => $id_mapping ?? '-',
                    'id_pengaju' => $user_karyawan->id ?? '-',
                    'nama_pengaju' => $item->nama_pengaju ?? '-',
                    'sign_pengaju' => $item->sign_pengaju ?? '-',
                    'approval1' => $item->approval1 ?? '-',
                    'approval2' => $item->approval2 ?? '-',
                    'alasan' => $item->alasan ?? '-',
                    'no_form' => $item->no_form ?? '-',
                    'created_at' => $item->created_at->format('Y-m-d') ?? '-',
                ];
            }
            return response()->json([
                'code' => 200,
                'grouped' => $grouped,
                // 'data_keahlian' => $data_keahlian,
                'message' => 'Data Berhasil Ditampilkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function tolak_ubah_shift_hrd(Request $request)
    {
        // dd($request->all());
        try {
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $mapping_shift_tukar = MappingShiftTukar::where('id', $request->id_mapping_tukar)->first();
            $mapping_shift_tukar->update([
                'approval2' => '2',
                'sign_approval2' => $fileName,
            ]);
            $karyawan_id = Karyawan::where('id', $mapping_shift_tukar->id_approval2)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_tukar,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar',
                'activity' => 'update',
                'description' => 'Menolak perubahan shift (sebagai HRD) ' . $mapping_shift_tukar->nama_pengaju,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan); contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16
            // dd($start);
            $data = MappingShiftTukar::where('id_approval2', $user_karyawan->id)
                ->whereNotNull('approval1')
                ->whereBetween('created_at', array($request->start, $request->end))
                ->get();
            // dd($data);

            // ubah ke format seperti di JS
            $grouped = [];
            foreach ($data as $key => $item) {
                $id_mapping = $item->id;
                $grouped[$key] = [
                    'id_mapping' => $id_mapping ?? '-',
                    'id_pengaju' => $user_karyawan->id ?? '-',
                    'nama_pengaju' => $item->nama_pengaju ?? '-',
                    'sign_pengaju' => $item->sign_pengaju ?? '-',
                    'approval1' => $item->approval1 ?? '-',
                    'approval2' => $item->approval2 ?? '-',
                    'alasan' => $item->alasan ?? '-',
                    'no_form' => $item->no_form ?? '-',
                    'created_at' => $item->created_at->format('Y-m-d') ?? '-',
                ];
            }

            return response()->json([
                'code' => 200,
                'grouped' => $grouped,
                // 'data_keahlian' => $data_keahlian,
                'message' => 'Data Berhasil Ditampilkan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    // Approval 2 end
    // Tukar shift yang diajukan oleh karyawan end

    // Tukar shift yang diajukan oleh atasan
    function edit_mapping($id_head, $id, Request $request)
    {
        $update_allow = MappingShiftTukarAtasan::whereNull('approved_signature')->where('id_head', $id_head)
            ->count();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $user_karyawan->id)->orderBy('created_at', 'desc')->first();
        // dd($get_approval);
        if ($get_approval?->approval1 == 1) {
            $mapping_shift_tukar = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $user_karyawan->id)->exists();
        } elseif ($get_approval?->approval1 == 2) {
            $mapping_shift_tukar = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $user_karyawan->id)->exists();
        } else {
            $mapping_shift_tukar = false;
            // dd($badge_pegawai_ubah);
        }

        if ($mapping_shift_tukar) {
            return redirect('mapping_shift/dashboard/' . $id)->with('warning_pengajuan_pengajuan', 'Masih ada pengajuan tukar shift dari pegawai yang belum diselesaikan.');;
        }
        // dd($id_mapping);
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


            $mapping_shift = MappingShift::with('Karyawan', 'KaryawanInput', 'KaryawanApproved')
                ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
                ->where('id_head', $id_head)
                ->where('status_approved', 1)
                ->whereNotNull('is_active')
                ->first();
            // dd($mapping_shift);
            if ($mapping_shift != null) {
                $mapping_shift_tukar = MappingShiftTukarAtasan::where('id_head', $id_head)->whereNull('approved')->count();
                if ($mapping_shift_tukar == 0) {
                    $mapping_shift_tukar_detail = MappingShiftTukarAtasanDetail::where('id_head', $id_head)->whereNull('id_tukar_atasan_utama')->count();
                    if ($mapping_shift_tukar_detail == 0) {
                        $mapping_shift_all = MappingShift::where('id_head', $id_head)->whereNotNull('is_active')->get();
                        // MappingShift::create([])
                        foreach ($mapping_shift_all as $ms) {
                            MappingShiftTukarAtasanDetail::create([
                                'id_mapping' => $ms->id,
                                'id_tukar_atasan_utama' => null,
                                'legal_number_mapping' => $ms->legal_number_mapping,
                                'karyawan_id' => $ms->karyawan_id,
                                'nik_karyawan' => $ms->nik_karyawan,
                                'nama_karyawan' => $ms->nama_karyawan,
                                'shift_id' => $ms->shift_id,
                                'nama_shift' => $ms->nama_shift,
                                'input_by' => $ms->input_by,
                                'pengajuan_signature' => $ms->pengajuan_signature,
                                'approved_by' => $ms->approved_by,
                                'approved_at' => $ms->approved_at,
                                'approved_signature' => $ms->approved_signature,
                                'status_approved' => $ms->status_approved,
                                'tanggal_masuk' => $ms->tanggal_masuk,
                                'tanggal_pulang' => $ms->tanggal_pulang,
                                'mapping_group' => $ms->mapping_group,
                                'is_active' => $ms->is_active,
                                'id_tukar_shift_detail' => $ms->id_tukar_shift_detail,
                                'id_head' => $ms->id_head,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            }

            $menu_active = User::where('id', Auth::user()->id)->first();
            $menu_active->menu_active = $id;
            $menu_active->save();
            return view('users.tukar_shift.edit_mapping', [
                'all_menus' => $all_menus,
                'all_menu_bar' => $all_menu_bar,
                'shift' => $shift,
                'start_date' => $start_date,
                'user_karyawan' => $user_karyawan,
                'mapping_shift' => $mapping_shift,
                'end_date' => $end_date,
                'update_allow' => $update_allow,
                'menu_id' => $id,
                'id_head' => $id_head
            ]);
        } catch (\Exception $e) {
            session()->flash('tambahmappingerror', $e->getMessage());
            return redirect()->back()->with([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function getDate(Request $request, $id)
    {
        // dd($request->all());
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
                $mapping_shift = MappingShiftTukarAtasanDetail::where('tanggal_masuk', $date->format('Y-m-d'))
                    ->where('input_by', $user_karyawan ? $user_karyawan->id : '')
                    ->whereNotNull('is_active')
                    ->whereNull('id_tukar_atasan_utama')
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
    public function getApproval(Request $request, $id)
    {
        // dd($request->all);
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
            // dd($access, $id);
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
                $q->whereNotNull('is_active');
            }])->whereIn('id', $bawahanKaryawanIds)
                ->where('status_aktif', 'AKTIF')
                ->where('shift', 'SHIFT')
                ->orderBy('name', 'asc')
                ->get();


            $master_shift = Shift::whereNot('nama_shift', 'NON SHIFT')->orderBy('nama_shift')->get();
            $mapping_shift = MappingShiftTukarAtasanDetail::where('input_by', $user_karyawan->id)->where('tanggal_masuk', $request->date)->whereNotNull('is_active')->get();
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
    public function addMKaryawan($id, Request $request)
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
            $update_allow = MappingShiftTukarAtasan::whereNull('approved_signature')->where('id_head', $id)
                ->exists();
            // dd($update_allow);
            if (!$update_allow) {
                $mapping_shift = MappingShiftTukarAtasanDetail::where('tanggal_masuk', $request->tanggal)
                    ->where('status_approved', 1)
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
                        $mappingId = $request->id_mapping[$value] ?? null;
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
                            $get_mapping_shift = MappingShiftTukarAtasanDetail::where('karyawan_id', $value)
                                ->where('tanggal_masuk', $request->tanggal)
                                ->where('status_approved', 0)
                                ->where('input_by', $user_karyawan->id)
                                ->whereNotNull('is_active')
                                ->first();
                            // dd($get_mapping_shift);
                            if ($get_mapping_shift) {
                                $get_mapping_shift->delete();
                                // insert ulang
                                $create = new MappingShiftTukarAtasanDetail();
                                $create->karyawan_id = $value;
                                $create->id_mapping =  is_numeric($mappingId)
                                    ? (int) $mappingId
                                    : null;
                                $create->nik_karyawan = $exists->nomor_identitas_karyawan;
                                $create->shift_id = $shiftId;
                                $create->nama_karyawan = $exists->name;
                                $create->nama_shift = $shiftExists->nama_shift;
                                $create->input_by = $user_karyawan->id;
                                $create->tanggal_masuk = $request->tanggal;
                                $create->tanggal_pulang = $tanggal_pulang;
                                $create->created_at = Carbon::now();
                                $create->status_approved = $request->status_approved;
                                $create->is_active = 3;
                                $create->legal_number_mapping = $get_mapping_shift->legal_number_mapping;
                                $create->id_head = $id;
                                $create->mapping_group = $get_mapping_shift->mapping_group;
                                $create->approved_by = $get_mapping_shift->approved_by;
                                $create->pengajuan_signature = $get_mapping_shift->pengajuan_signature;
                                $create->save();
                            } else {
                                // dd($request->all());

                                $create = new MappingShiftTukarAtasanDetail();
                                $create->legal_number_mapping = $request->legal_number_mapping;
                                $create->id_mapping =  $create->id_mapping =  is_numeric($mappingId)
                                    ? (int) $mappingId
                                    : null;
                                $create->karyawan_id = $value;
                                $create->nik_karyawan = $exists->nomor_identitas_karyawan;
                                $create->shift_id = $shiftId;
                                $create->nama_karyawan = $exists->name;
                                $create->nama_shift = $shiftExists->nama_shift;
                                $create->input_by = $user_karyawan->id;
                                $create->tanggal_masuk = $request->tanggal;
                                $create->tanggal_pulang = $tanggal_pulang;

                                $create->pengajuan_signature = $request->pengajuan_signature;
                                $create->approved_by = $request->approved_by;
                                $create->approved_at = $request->approved_at;
                                $create->approved_signature = $request->approved_signature;
                                $create->status_approved = $request->status_approved;
                                $create->mapping_group = $request->mapping_group;
                                $create->id_head = $id;
                                $create->is_active = 3;
                                $create->created_at = Carbon::now();
                                $create->save();
                            }
                        }
                    }
                }

                return response()->json([
                    'code' => 200,
                    'message' => 'Berhasil menambahkan karyawan ke mapping shift',
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
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function editMappingShift($id, Request $request)
    {
        // dd($id);
        $validation = Validator::make($request->all(), [
            'mapping_title' => 'required',
            // 'approve_hrd' => 'required',
            'signature' => 'required',

        ], [
            'mapping_title.required' => 'Alasan ubah mapping Shift wajib diisi',
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
        // dd($request->all());

        // dd($start, $end, $period);




        $asoy = MappingShiftTukarAtasan::select('no_form')
            ->latest()
            ->first();

        // Ambil angka terakhir (misal: 001 → 1)
        $lastNumber = $asoy && $asoy->no_form
            ? (int) substr(strrchr($asoy->no_form, '/'), 1)
            : 0;

        // Increment
        $nextNumber = $lastNumber + 1;

        // Format jadi 3 digit (001, 002, dst)
        $no = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Tanggal sekarang
        $now = Carbon::now()->format('Ymd');

        // Gabungkan
        $legal = 'TUKAR-SHIFT/' . $now . '/' . $no;

        // dd($no_urut);
        // dd($legal);
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $user_hrd = Karyawan::where('id', $request->approve_hrd)->first();
        // dd($user_hrd);
        try {
            $update_allow = MappingShiftTukarAtasan::whereNull('approved_signature')->where('id_head', $id)
                ->exists();
            if (!$update_allow) {
                $get_approval = MappingShiftTukar::whereNotNull('approval1')->where('id_approval1', $user_karyawan->id)->orderBy('created_at', 'desc')->first();
                // dd($get_approval);
                if ($get_approval?->approval1 == 1) {
                    $mapping_shift_tukar = MappingShiftTukar::whereNull('approval2')->where('id_approval1', $user_karyawan->id)->exists();
                } elseif ($get_approval?->approval1 == 2) {
                    $mapping_shift_tukar = MappingShiftTukar::whereNull('approval1')->where('id_approval1', $user_karyawan->id)->exists();
                } else {
                    $mapping_shift_tukar = false;
                    // dd($badge_pegawai_ubah);
                }
                // dd($mapping_shift_tukar);
                if (!$mapping_shift_tukar) {



                    $image = $request->signature;
                    $image = str_replace('data:image/png;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);

                    // nama file
                    $fileName = 'tukar_shift_' . time() . '.png';
                    // dd($fileName);
                    // simpan ke storage
                    Storage::disk('public')->put(
                        'signatures/' . $fileName,
                        base64_decode($image)
                    );


                    $kode = Uuid::uuid4();
                    MappingShiftTukarAtasan::create([
                        'id' => $kode,
                        'alasan' => $request->mapping_title,
                        'no_form' => $legal,
                        'input_by' => $user_karyawan->id,
                        'input_by_name' => $request->name,
                        'input_by_signature' => $fileName,
                        // 'approved_by' => $request->approve_hrd,
                        'approved_at' => null,
                        'approved_signature' => null,
                        'id_head' => $id,
                        // 'approved_by_name' => $user_hrd->name,
                        'created_at' => now(),
                    ]);

                    // $tukar_shift = MappingShiftTukarAtasanDetail::where('legal_number_mapping', $request->legal_number_update)->whereNull('id_tukar_atasan_utama')->count();
                    // if ($tukar_shift == 0) {
                    MappingShiftTukarAtasanDetail::where('legal_number_mapping', $request->legal_number_update)->whereNull('id_tukar_atasan_utama')->update([
                        'id_tukar_atasan_utama' => $kode,

                    ]);
                    // }



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
                        'description' => 'membuat pengajuan edit mapping shift: ' .  $request->mapping_title,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    return response()->json([
                        'code' => 200,
                        'message' => 'Berhasil menambahkan mapping shift',
                    ]);
                }
                // else {

                //     return response()->json([
                //         'status' => 'warning',
                //         'message' => 'Masih ada pengajuan tukar shift pegawai yang belum diselesaikan.',
                //         'redirect' => url('mapping_shift/dashboard/' . $id)
                //     ]);
                // }
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
    public function getAtasanShiftData(Request $request)
    {
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $start = Carbon::parse($request->query('start'))->format('Y-m-d'); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->format('Y-m-d'); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16

        $data = MappingShiftTukarAtasan::whereBetween('created_at', array($start, $end))
            ->where('input_by', $user_karyawan->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            if ($item->approved == 1) {
                $approval1 = '-history1';
            } elseif ($item->approved == 2) {
                $approval1 = '2';
            } else {
                $approval1 = '-';
            }
            $grouped[$key] = [
                'id' => $item->id,
                'name' => $item->input_by_name,
                'approved' => $item->approved,
                'approval1' => $approval1,
                'no_form' => $item->no_form,
                'alasan' => $item->alasan,
                'created_at' => Carbon::parse($item->created_at)->format('d-m-Y'),
            ];
        }

        // dd($grouped);
        return response()->json($grouped);
    }
    public function detailTukarAtasan($id, Request $request)
    {
        // dd($request->all());
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

        $detail_mapping = MappingShiftTukarAtasanDetail::where('id_tukar_atasan_utama', $id)
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

        $tukar_shift = MappingShiftTukarAtasan::where('id', $id)->first();
        // dd($tukar_shift);
        return view('users.mapping_shift.tukar_detail', [
            'all_menus' => $all_menus,
            'all_menu_main' => $all_menu_main,
            'all_menu_bar' => $all_menu_bar,
            'detail_mapping' => $detail_mapping,
            'user_karyawan' => $user_karyawan,
            'tukar_shift' => $tukar_shift
        ]);
    }

    // HRD
    public function getHRDShiftData(Request $request)
    {
        // dd($request->all());
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $start = Carbon::parse($request->query('start'))->startOfDay(); // contoh: 2025-11-10$request->query('start'); // contoh: 2025-11-10
        $end = Carbon::parse($request->query('end'))->endOfDay(); // contoh: 2025-11-16$request->query('end');     // contoh: 2025-11-16

        $data = MappingShiftTukarAtasan::whereBetween('created_at', array($start, $end))
            // ->where('approved_by', $user_karyawan->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // ubah ke format seperti di JS
        $grouped = [];
        foreach ($data as $key => $item) {
            if ($item->approved == 1) {
                $approval1 = '1';
            } elseif ($item->approved == 2) {
                $approval1 = '2';
            } else {
                $approval1 = '-';
            }
            $grouped[$key] = [
                'id' => $item->id,
                'name' => $item->input_by_name,
                'approval1' => $approval1,
                'no_form' => $item->no_form,
                'alasan' => $item->alasan,
                'created_at' => Carbon::parse($item->created_at)->format('d-m-Y'),
            ];
        }

        // dd($grouped);
        return response()->json($grouped);
    }
    public function detailTukarAtasanHRD($id, $menu_active, Request $request)
    {
        // dd($request->all());
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

        $detail_mapping = MappingShiftTukarAtasanDetail::where('id_tukar_atasan_utama', $id)
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
        $legal_number_mapping = MappingShiftTukarAtasanDetail::select('legal_number_mapping')->where('id_tukar_atasan_utama', $id)->first();
        // dd($detail_mapping);
        $tukar_shift = MappingShiftTukarAtasan::where('id', $id)->first();
        // dd($tukar_shift);
        return view('users.approval.mapping_shift.tukar_shift_detail', [
            'all_menus' => $all_menus,
            'all_menu_main' => $all_menu_main,
            'all_menu_bar' => $all_menu_bar,
            'detail_mapping' => $detail_mapping,
            'user_karyawan' => $user_karyawan,
            'tukar_shift' => $tukar_shift,
            'legal_number_mapping' => $legal_number_mapping,
            'menu_active' => $menu_active
        ]);
    }
    public function ubah_shift_atasan_hrd($menu_id, Request $request)
    {
        // dd($request->all());
        try {
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $mapping_shift_tukar = MappingShiftTukarAtasan::where('id', $request->id)->first();
            $mapping_shift_tukar->update([
                'approved' => '1',
                'approved_by' => $user_karyawan->id,
                'approved_by_name' => $user_karyawan->name,
                'approved_at' => now(),
                'approved_signature' => $fileName,
            ]);

            MappingShift::where('legal_number_mapping', $request->legal_number_mapping)->update([
                'is_active' => null
            ]);
            $tukar_mapping = MappingShiftTukarAtasanDetail::where('id_tukar_atasan_utama', $request->id)->get();
            // dd($tukar_mapping);
            foreach ($tukar_mapping as $tm) {
                MappingShift::create([
                    'legal_number_mapping' => $tm->legal_number_mapping,
                    'karyawan_id' => $tm->karyawan_id,
                    'nik_karyawan' => $tm->nik_karyawan,
                    'nama_karyawan' => $tm->nama_karyawan,
                    'shift_id' => $tm->shift_id,
                    'nama_shift' => $tm->nama_shift,
                    'input_by' => $tm->input_by,
                    'pengajuan_signature' => $tm->pengajuan_signature,
                    'approved_by' => $tm->approved_by,
                    'approved_at' => $tm->approved_at,
                    'approved_signature' => $tm->approved_signature,
                    'status_approved' => $tm->status_approved,
                    'tanggal_masuk' => $tm->tanggal_masuk,
                    'tanggal_pulang' => $tm->tanggal_pulang,
                    'mapping_group' => $tm->mapping_group,
                    'is_active' => $tm->is_active,
                    'id_tukar_shift_detail' => $tm->id_tukar_shift_detail,
                    'id_head' => $tm->id_head,
                    'created_at' => now(),
                ]);
            }


            $karyawan_id = Karyawan::where('id', $mapping_shift_tukar->input_by)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_tukar,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar',
                'activity' => 'update',
                'description' => 'Menerima perubahan shift SPV (sebagai HRD) ' . $mapping_shift_tukar->input_by_name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            return response()->json([
                'status' => true,
                'code' => 200,
                'redirect' => url('approval/mapping_shift/' . $menu_id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function tolak_ubah_shift_atasan_hrd($menu_id, Request $request)
    {
        // dd($request->all());
        try {
            $image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            // nama file
            $fileName = 'tukar_shift_' . time() . '.png';
            // dd($fileName);
            // simpan ke storage
            Storage::disk('public')->put(
                'signatures/' . $fileName,
                base64_decode($image)
            );
            $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $mapping_shift_tukar = MappingShiftTukarAtasan::where('id', $request->id)->first();
            $mapping_shift_tukar->update([
                'approved' => '2',
                'approved_by' => $user_karyawan->id,
                'approved_by_name' => $user_karyawan->name,
                'approved_at' => now(),
                'approved_signature' => $fileName,
            ]);
            $karyawan_id = Karyawan::where('id', $mapping_shift_tukar->input_by)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_mapping_tukar,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'mapping_shift_tukar',
                'activity' => 'update',
                'description' => 'Menolak perubahan shift SPV (sebagai HRD) ' . $mapping_shift_tukar->input_by_name,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            return response()->json([
                'status' => true,
                'code' => 200,
                'redirect' => url('approval/mapping_shift/' . $menu_id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }



    // Tukar shift yang diajukan oleh atasan end
}
