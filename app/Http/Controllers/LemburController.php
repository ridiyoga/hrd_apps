<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Lembur;
use App\Models\LemburDetail;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\MappingShiftTukarAtasan;
use App\Models\Menu;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\StrukturModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;

class LemburController extends Controller
{
    //
    public function dashboard($id)
    {
        // dd($id);
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

        return view('users.lembur.index', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function lembur_shift_akhir($id)
    {
        // dd($id);
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

        return view('users.lembur.lembur_shift', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function lembur_non_shift($id)
    {
        // dd($id);
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

        return view('users.lembur.lembur_non_shift', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function get_karyawan_lembur($tanggal_lembur, $jam)
    {
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_karyawan = MappingShift::with('Shift')->where('tanggal_masuk', $tanggal_lembur)
            ->where('input_by', $user_karyawan->id)
            ->whereNotNull('is_active')
            ->whereNotNull('karyawan_id')
            ->whereHas('Shift', function ($query) {
                $query->whereNotNull('jam_masuk');
            })
            // ->select('id', 'nama_karyawan', 'nama_shift', 'jam_keluar')
            ->orderBy('nama_karyawan', 'ASC')
            ->get();

        $options = "<option value=''>Pilih Karyawan...</option>";
        foreach ($get_karyawan as $karyawan) {
            $berakhir = \Carbon\Carbon::parse($karyawan->Shift->jam_keluar)
                ->addHours(\Carbon\Carbon::parse($jam)->hour)
                ->addMinutes(\Carbon\Carbon::parse($jam)->minute)
                ->format('H:i');
            $options .= "<option value='{$karyawan->id}'>" . $karyawan->nama_karyawan . "&nbsp;(" . $karyawan->Shift->jam_keluar . "&nbsp;-&nbsp;" . $berakhir . ")</option>";
        }

        return response($options)->header('Content-Type', 'text/html');
    }
    public function get_non_shift_lembur()
    {
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        // $get_karyawan = Karyawan::where('status_aktif', 'AKTIF')
        //     ->select('id', 'name')
        //     ->orderBy('name', 'ASC')
        //     ->get();
        // dd($get_karyawan);
        $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $user_karyawan->id)->get();
        // dd($karyawan_jabatan);
        // ambil array id
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
        $get_karyawan = Karyawan::with(['KaryawanJabatan' => function ($q) {
            $q->with('Divisi');
            $q->with('Jabatan');
            $q->with('Bagian');
            $q->with('Departemen');
            $q->where('main', 1);
            $q->where('is_active', 1);
        }])->whereIn('id', $bawahanKaryawanIds)
            ->where('status_aktif', 'AKTIF')
            ->where('shift', 'NON SHIFT')
            ->where('id', '!=', $user_karyawan->id)
            ->orderBy('name', 'asc')
            ->get();

        $options = "<option value=''>Pilih Karyawan...</option>";
        foreach ($get_karyawan as $karyawan) {
            $options .= "<option value='{$karyawan->id}'>" . $karyawan->name . "</option>";
        }

        return response($options)->header('Content-Type', 'text/html');
    }
    public function get_keluar_shift($id)
    {
        $get_keluar_shift = MappingShift::where('id', $id)->with('Shift')->first();

        if (!$get_keluar_shift || !$get_keluar_shift->Shift) {
            return response()->json([
                'message' => 'Data shift karyawan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'jam_keluar' => $get_keluar_shift->Shift->jam_keluar,
        ]);
    }
    public function get_lembur(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_lembur = Lembur::where('karyawan_id', $user_karyawan->id)
                ->where('status_lembur', 0)
                ->get();
            // dd($data_lembur);
            return response()->json([
                'code' => 200,
                'data' => $data_lembur

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_lembur_detail(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_lembur = LemburDetail::where('id_karyawan', $user_karyawan->id)
                ->where('status', null)
                ->get();
            // dd($data_lembur);
            return response()->json([
                'code' => 200,
                'data' => $data_lembur

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_lembur_riwayat(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_lembur = Lembur::where('karyawan_id', $user_karyawan->id)
                ->where('status_lembur', '!=', 0)
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_lembur);
            return response()->json([
                'code' => 200,
                'data' => $data_lembur

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_lembur_riwayat_user(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_lembur = LemburDetail::where('id_karyawan', $user_karyawan->id)
                ->whereNotNull('status')
                ->whereBetween('created_at', array($start, $end))->get();
            // dd($data_lembur);
            return response()->json([
                'code' => 200,
                'data' => $data_lembur

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_pengajuan_lembur(Request $request, $id)
    {
        // dd($request->all());

        try {
            if ($request->lembur_shift == 1) {

                $mulai_ar = 'nullable';
                $mulai_req = 'nullable';

                $selesai_ar = 'nullable';
                $selesai_req = 'nullable';

                $karyawan_lembur_ar = 'nullable';
                $karyawan_lembur_req = 'nullable';

                $karyawan_berulang_ar = 'required|array|min:1';
                $karyawan_berulang_req = 'required';
                $lama_lembur_req = 'required';
            } else {

                $mulai_ar = 'required|array|min:1';
                $mulai_req = 'required';

                $selesai_ar = 'required|array|min:1';
                $selesai_req = 'required';

                $karyawan_lembur_ar = 'required|array|min:1';
                $karyawan_lembur_req = 'required';
                $karyawan_berulang_ar = 'nullable';
                $karyawan_berulang_req = 'nullable';
                $lama_lembur_req = 'nullable';
            }

            $rules = [
                'id_karyawan' => 'required',
                'mulai' => $mulai_ar,
                'mulai.*' => $mulai_req,
                'selesai' => $selesai_ar,
                'selesai.*' => $selesai_req,
                'id_karyawan_lembur' => $karyawan_lembur_ar,
                'id_karyawan_lembur.*' => $karyawan_lembur_req,
                'perkiraan' => $lama_lembur_req,

                'karyawan_berulang' => $karyawan_berulang_ar,
                'karyawan_berulang.*' => $karyawan_berulang_req,
                'tanggal_lembur' => 'required',
                'keterangan_lembur' => 'required',
                'atasan_1' => 'required',
                'atasan_2' => 'required',
                'signature' => 'required',

            ];
            $messages = [
                'id_karyawan.required' => 'Karyawan wajib diisi',
                'perkiraan.required' => 'Lama lembur wajib diisi',
                'mulai.required' => 'Jam mulai wajib diisi',
                'mulai.*.required' => 'Jam mulai wajib diisi',
                'selesai.required' => 'Jam selesai wajib diisi',
                'selesai.*.required' => 'Jam selesai wajib diisi',

                'karyawan_berulang.required' => 'Karyawan wajib diisi',
                'karyawan_berulang.*.required' => 'Karyawan wajib diisi',
                'id_mapping_shift.required' => 'Karyawan shift wajib diisi',
                'id_mapping_shift.*.required' => 'Karyawan shift wajib diisi',
                'tanggal_lembur.required' => 'Tanggal lembur wajib diisi',
                'keterangan_lembur.required' => 'Keterangan lembur wajib diisi',
                'atasan_1.required' => 'Atasan 1 wajib diisi',
                'atasan_2.required' => 'Atasan 2 wajib diisi',
                'signature.required' => 'Tanda Tangan wajib diisi',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
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
            // dd($request->all());
            if ($request->lembur_shift == 1) {
                $tukar_shift = MappingShiftTukarAtasan::where('input_by', $request->id_karyawan)->where('approved', null)->exists();
                // dd($tukar_shift);
                if ($tukar_shift) {
                    return response()->json([
                        'code' => 510,
                        'message' => 'Masih ada perubahan shift yang belum selesai',
                    ]);
                } else {
                    // dd($request->all());
                    // $mapping_shift = $request->input('id_mapping_shift');

                    // if (!empty($mapping_shift)) {
                    //     $detail = [];
                    //     foreach ($mapping_shift as $key => $ms) {
                    //         $mulai = $request->input('mulai')[$key];
                    //         $selesai = $request->input('selesai')[$key];

                    //         // if ($mulai < $selesai) {
                    //         //     dd('ya');
                    //         // } else {
                    //         //     dd('tidak');
                    //         // }
                    //         $id_mapping_shift = $request->input('id_mapping_shift')[$key];
                    //         $data_mapping_shift = MappingShift::where('id', $id_mapping_shift)->first();
                    //         $karyawan = Karyawan::where('id', $data_mapping_shift->karyawan_id)->first();
                    //         // dd($karyawan);
                    //         if (!$data_mapping_shift) {
                    //             continue;
                    //         }
                    //         $jamMulai = Carbon::parse($request->tanggal_lembur . ' ' . $mulai);
                    //         $jamSelesai = Carbon::parse($request->tanggal_lembur . ' ' . $selesai);
                    //         // dd($jamSelesai->lessThan($jamMulai));
                    //         if ($jamSelesai->lessThan($jamMulai)) {
                    //             $pulang = $jamSelesai->addDay();
                    //         } else {
                    //             $pulang = $jamSelesai;
                    //         }
                    //         // dd($jamSelesai);
                    //         $tgl_pulang = Carbon::parse($pulang)->format('Y-m-d');

                    //         // $totalMenit = $jamMulai->diffInMinutes($pulang);

                    //         // $jam = intdiv($totalMenit, 60);
                    //         // $menit = $totalMenit % 60;
                    //         // $selisih = sprintf('%02d:%02d', $jam, $menit);
                    //         $detail[] = [
                    //             'id' => Uuid::uuid4(),
                    //             'id_lembur' => $kode,
                    //             'id_karyawan' => $data_mapping_shift->karyawan_id,
                    //             'nomor_identitas_karyawan' => $karyawan->nomor_identitas_karyawan,
                    //             'nama_karyawan' => $data_mapping_shift->nama_karyawan,
                    //             'id_mapping_shift' => $id_mapping_shift,
                    //             'nama_shift' => $data_mapping_shift->nama_shift,
                    //             'tgl_lembur' => $request->tanggal_lembur,
                    //             'awal' => $request->awal,
                    //             'jam_mulai' => $mulai,
                    //             'jam_selesai' => $selesai,
                    //             // 'selisih_convert' => $selisih,
                    //             'tgl_pulang' => $tgl_pulang,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //         ];
                    //     }
                    //     // dd($detail);
                    //     LemburDetail::insert($detail);
                    // }
                    // $mapping_shift = MappingShift::with('Shift')
                    //     ->where('input_by', $request->id_karyawan)
                    //     ->where('tanggal_masuk', $request->tanggal_lembur)
                    //     ->whereHas('Shift', function ($query) {
                    //         $query->whereNotNull('jam_masuk');
                    //     })
                    //     ->whereNotNull('is_active')
                    //     ->get();
                    // // dd($mapping_shift);
                    // if ($mapping_shift->isEmpty()) {
                    //     return response()->json([
                    //         'code' => 510,
                    //         'message' => 'Shift belum diatur pada tanggal yang dipilih.',
                    //     ]);
                    // } else {
                    //     foreach ($mapping_shift as $shift) {
                    //         // dd($request->all());
                    //         $perkiraan = $request->perkiraan;

                    //         [$jam, $menit] = explode(':', $perkiraan);

                    //         $selesai = \Carbon\Carbon::parse($shift->Shift->jam_keluar)
                    //             ->addHours((int) $jam)
                    //             ->addMinutes((int) $menit)
                    //             ->format('H:i:s');
                    //         $jamMulai = Carbon::parse($request->tanggal_lembur . ' ' . $shift->Shift->jam_keluar);
                    //         $jamSelesai = Carbon::parse($shift->tanggal_pulang . ' ' . $selesai);
                    //         if ($jamSelesai->lessThan($jamMulai)) {
                    //             $pulang = $jamSelesai->addDay();
                    //         } else {
                    //             $pulang = $jamSelesai;
                    //         }
                    //         $tgl_pulang = Carbon::parse($pulang)->format('Y-m-d');
                    //         // dd($selesai);
                    //         LemburDetail::insert([[
                    //             'id' => Uuid::uuid4(),
                    //             'id_lembur' => $kode,
                    //             'id_karyawan' => $shift->karyawan_id,
                    //             'nomor_identitas_karyawan' => $shift->nik_karyawan,
                    //             'nama_karyawan' => $shift->nama_karyawan,
                    //             'id_mapping_shift' => $shift->id,
                    //             'nama_shift' => $shift->nama_shift,
                    //             'tgl_lembur' => $shift->tanggal_pulang,
                    //             'awal' => $request->awal,
                    //             'jam_mulai' => $shift->Shift->jam_keluar,
                    //             'jam_selesai' => $selesai,
                    //             'perkiraan' => $request->perkiraan,
                    //             'tgl_pulang' => $tgl_pulang,
                    //             'created_at' => date('Y-m-d H:i:s'),
                    //         ]]);
                    //     }
                    // }
                    $karyawan_berulang = $request->input('karyawan_berulang');
                    // dd($start_tanggal_tidak_masuk, $end_tanggal_tidak_masuk);
                    if (!empty($karyawan_berulang)) {
                        foreach ($karyawan_berulang as $kk) {
                            // Proses setiap tanggal di sini, misalnya simpan ke DB
                            // Contoh dengan Eloquent Laravel:
                            $mapping_shift = MappingShift::with('Shift')
                                ->where('id', $kk)
                                ->first();

                            // dd($mapping_shift);

                            // if (!$mapping_shift || !$mapping_shift->Shift) {
                            //     return response()->json([
                            //         'code' => 510,
                            //         'message' => 'Data shift karyawan yang dipilih tidak ditemukan untuk tanggal lembur ' .
                            //             $request->tanggal_lembur . '.',
                            //     ]);
                            // }

                            $perkiraan = $request->perkiraan;

                            [$jam, $menit] = explode(':', $perkiraan);

                            $selesai = \Carbon\Carbon::parse($mapping_shift->Shift?->jam_keluar)
                                ->addHours((int) $jam)
                                ->addMinutes((int) $menit)
                                ->format('H:i:s');
                            $jamMulai = Carbon::parse($request->tanggal_lembur . ' ' . $mapping_shift->Shift?->jam_keluar);
                            $jamSelesai = Carbon::parse($mapping_shift->tanggal_pulang . ' ' . $selesai);
                            if ($jamSelesai->lessThan($jamMulai)) {
                                $pulang = $jamSelesai->addDay();
                            } else {
                                $pulang = $jamSelesai;
                            }
                            $tgl_pulang = Carbon::parse($pulang)->format('Y-m-d');
                            LemburDetail::insert([[
                                'id' => Uuid::uuid4(),
                                'id_lembur' => $kode,
                                'id_karyawan' => $mapping_shift->karyawan_id,
                                'nomor_identitas_karyawan' => $mapping_shift->nik_karyawan,
                                'nama_karyawan' => $mapping_shift->nama_karyawan,
                                'id_mapping_shift' => $mapping_shift->id,
                                'nama_shift' => $mapping_shift->nama_shift,
                                'tgl_lembur' => $mapping_shift->tanggal_pulang,
                                'awal' => $request->awal,
                                'jam_mulai' => $mapping_shift->Shift?->jam_keluar,
                                'jam_selesai' => $selesai,
                                'perkiraan' => $request->perkiraan,
                                'tgl_pulang' => $tgl_pulang,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]]);
                        }
                    }
                }
            } else {
                $karyawan_lembur = $request->input('id_karyawan_lembur');

                if (!empty($karyawan_lembur)) {
                    $detail = [];
                    foreach ($karyawan_lembur as $key => $ms) {
                        $mulai = $request->input('mulai')[$key];
                        $selesai = $request->input('selesai')[$key];

                        // if ($mulai < $selesai) {
                        //     dd('ya');
                        // } else {
                        //     dd('tidak');
                        // }
                        $id_karyawan_lembur = $request->input('id_karyawan_lembur')[$key];
                        $karyawan = Karyawan::where('id', $id_karyawan_lembur)->first();
                        $jamMulai = Carbon::parse($request->tanggal_lembur . ' ' . $mulai);
                        $jamSelesai = Carbon::parse($request->tanggal_lembur . ' ' . $selesai);
                        // dd($jamSelesai->lessThan($jamMulai));
                        if ($jamSelesai->lessThan($jamMulai)) {
                            $pulang = $jamSelesai->addDay();
                        } else {
                            $pulang = $jamSelesai;
                        }
                        // dd($jamSelesai);
                        $tgl_pulang = Carbon::parse($pulang)->format('Y-m-d');

                        $totalMenit = $jamMulai->diffInMinutes($pulang);

                        $jam = intdiv($totalMenit, 60);
                        $menit = $totalMenit % 60;
                        $selisih = sprintf('%02d:%02d', $jam, $menit);
                        $detail[] = [
                            'id' => Uuid::uuid4(),
                            'id_lembur' => $kode,
                            'id_karyawan' => $id_karyawan_lembur,
                            'nomor_identitas_karyawan' => $karyawan->nomor_identitas_karyawan,
                            'nama_karyawan' => $karyawan->name,
                            'id_mapping_shift' => null,
                            'nama_shift' => 'NON SHIFT',
                            'tgl_lembur' => $request->tanggal_lembur,
                            'awal' => $request->awal,
                            'jam_mulai' => $mulai,
                            'jam_selesai' => $selesai,
                            'perkiraan' => $selisih,
                            'tgl_pulang' => $tgl_pulang,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                    // dd($detail);
                    LemburDetail::insert($detail);
                }
            }

            $pengaju = Karyawan::where('id', $request->id_karyawan)->first();
            $atasan1 = Karyawan::where('id', $request->atasan_1)->first();
            $atasan2 = Karyawan::where('id', $request->atasan_2)->first();

            $add_lembur = new Lembur();
            $add_lembur->id = $kode;
            $add_lembur->karyawan_id = $request->id_karyawan;
            $add_lembur->karyawan_nik = $pengaju->nomor_identitas_karyawan;
            $add_lembur->nama_karyawan = $request->name;
            $add_lembur->ttd_user = $fileName;

            $add_lembur->tanggal_lembur = $request->tanggal_lembur;
            $add_lembur->keterangan_lembur = $request->keterangan_lembur;
            $add_lembur->status_lembur = 0;
            $add_lembur->awal = $request->awal;

            $add_lembur->id_user_atasan = $request->atasan_1;
            $add_lembur->nama_atasan = $atasan1->name;

            $add_lembur->id_user_atasan2 = $request->atasan_2;
            $add_lembur->nama_atasan2 = $atasan2->name;

            $add_lembur->created_at = date('Y-m-d H:i:s');
            $add_lembur->save();

            // SAVE QUICK PANEL
            $quick = new QuickPanel();
            $quick->id_quick = Uuid::uuid4();
            $quick->user_id_from = $request->id_karyawan;
            $quick->user_id_to = $request->atasan_1;
            $quick->title_quick = 'Pengajuan lembur';
            $quick->message_quick = 'Ada Pengajuan lembur Baru Dari ' . $request->name;
            $quick->status = 0;
            $quick->quick_type = 'lembur';
            $quick->menu_id = Auth::user()->menu_active;
            $quick->content_id = $add_lembur->id;
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
                'table' => 'lembur',
                'activity' => 'add',
                'description' => 'Mengajukan lembur ' . $request->tanggal_lembur,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan lembur berhasil disimpan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function detail_lembur($id, $id_lembur)
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
        $data = Lembur::with('LemburDetail')->where('id', $id_lembur)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.lembur.lembur_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_lembur,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
    public function proses_hapus_lembur($id, Request $request)
    {
        // dd($request->all());
        $query = Lembur::where('id', $request->id_lembur_hapus)->first();
        $id_lembur = Karyawan::where('id', $query->karyawan_id)->first();
        $holdings = Holding::where('id', $id_lembur->kontrak_kerja)->first();
        $user_name = User::where('id', $request->user()->id)->first();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => 'hapus lembur',
            'id_table' => $request->id_lembur_hapus,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'lembur',
            'activity' => 'add',
            'description' => 'Menghapus pengajuan lembur ' .  $query->tanggal_lembur,
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        if ($query == null) {
            $request->session()->flash('hapus_lembur_gagal', 'Data lembur Tidak Ditemukan');
        } elseif ($query->status_lembur != 0) {
            $request->session()->flash('hapus_lembur_gagal', 'lembur sudah disetujui');
        } else {
            $path = 'signatures/' . $query->ttd_user;
            // dd($path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            QuickPanel::where('content_id', $request->id_lembur_hapus)->delete();

            LemburDetail::where('id_lembur', $query->id)->delete();
            $query->delete();
            $request->session()->flash('hapus_lembur_sukses', 'Form lembur Berhasil Dihapus');
        }
        return redirect('lembur/lembur_shift_akhir/' . $id);
    }
    public function get_lembur_approval(Request $request)
    {
        // dd($request->all());
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_lembur = Lembur::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_lembur', 0);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_lembur', 1);
                    });
            })->get();
            // dd($data_lembur);
            return response()->json([
                'code' => 200,
                'data' => $data_lembur

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function lembur_approval($menu_id)
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
        return view('users.lembur.lembur_approval', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }
    public function get_lembur_riwayat_approval(Request $request)
    {
        $start = Carbon::parse($request->awal)->startOfDay();
        $end = Carbon::parse($request->akhir)->endOfDay();
        // dd($start, $end);
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // dd($user_karyawan);
            $data_lembur = Lembur::where(function ($query) use ($user_karyawan) {
                $query->where(function ($q) use ($user_karyawan) {
                    $q->where('id_user_atasan', $user_karyawan->id)
                        ->where('status_lembur', '!=', 0)
                        ->where('status_approve_atasan1', '!=', null);
                })
                    ->orWhere(function ($q) use ($user_karyawan) {
                        $q->where('id_user_atasan2', $user_karyawan->id)
                            ->where('status_lembur', '!=', 0)
                            ->where('status_approve_atasan2', '!=', null);
                    });
            })->whereBetween('created_at', array($start, $end))->get();
            // dd($data_lembur);
            return response()->json([
                'code' => 200,
                'data' => $data_lembur

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_lembur_detail($id, Request $request)
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
            $data_lembur = Lembur::where('id', $id)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

            // $presensi = $user_karyawan->Absensi->where('LogTime',)
            $menu_id = Auth::user()->menu_active;
            return view('users.lembur.lembur_approval_detail', [
                'id' => $id,
                'all_menu_main' => $all_menu_main,
                'data' => $data_lembur,
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
    public function tolak_pengajuan_lembur(Request $request)
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
            $data_lembur = Lembur::where('id', $request->id_lembur)->first();
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
            if ($data_lembur->id_user_atasan == $user_karyawan->id) {
                $data_lembur->update([
                    'status_approve_atasan1' => 1,
                    'status_lembur' => 5,
                    'ttd_atasan' => $fileName,
                ]);
            } else {
                $data_lembur->update([
                    'status_approve_atasan2' => 1,
                    'status_lembur' => 5,
                    'ttd_atasan2' => $fileName,
                ]);
            }
            QuickPanel::where('content_id', $request->id_lembur)->update([
                'status' => 1,
            ]);
            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => null,
                'menu_name' => 'approval lembur',
                'id_table' => $data_lembur->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'lembur',
                'activity' => 'add',
                'description' => 'Menolak pengajuan lembur ' .  $data_lembur->nama_karyawan,
                'keterangan' => 'Success',
                'karir' => '2'
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan lembur berhasil ditolak.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve_pengajuan_lembur(Request $request)
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
            $data_lembur = Lembur::where('id', $request->id_lembur)->first();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $user_karyawan_pengajuan = Karyawan::where('id', $data_lembur->karyawan_id)->first();
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
            $count_lembur = Lembur::whereIn('status_lembur', [5, 2])->whereDate('created_at', $data_lembur->created_at)->where('id', '!=', $data_lembur->id)->get()->count();
            // dd($count_lembur);
            if ($count_lembur == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_lembur + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($data_lembur->created_at)->format('Ymd');

            $no_form = $user_karyawan_pengajuan->KontrakKerja->holding_category . '/FTM' . '/' . $tgl_pengajuan . '/' . $last_count;

            // dd($no_form);

            $holdings = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            if ($data_lembur->id_user_atasan2 == $data_lembur->id_user_atasan) {
                $data_lembur->update([
                    'status_approve_atasan1' => 1,
                    'status_approve_atasan2' => 1,
                    'status_lembur' => 2,
                    'waktu_approve' => date('Y-m-d H:i:s'),
                    'waktu_approve2' => date('Y-m-d H:i:s'),
                    'no_form_lembur' => $no_form,
                    'ttd_atasan' => $fileName,
                    'ttd_atasan2' => $fileName,
                    'jam_finger' => $request->jam_finger,
                ]);

                QuickPanel::where('content_id', $request->id_lembur)->update([
                    'read_at' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => null,
                    'menu_name' => 'approval lembur',
                    'id_table' => $data_lembur->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'lembur',
                    'activity' => 'add',
                    'description' => 'menyetujui pengajuan lembur sebagai atasan 1 ' .  $data_lembur->nama_karyawan,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
            } else {
                if ($data_lembur->id_user_atasan == $user_karyawan->id) {
                    $data_lembur->update([
                        'status_approve_atasan1' => 1,
                        'status_lembur' => 1,
                        'waktu_approve' => date('Y-m-d H:i:s'),
                        'no_form_lembur' => $no_form,
                        'ttd_atasan' => $fileName,
                        'jam_finger' => $request->jam_finger,
                    ]);

                    QuickPanel::where('content_id', $request->id_lembur)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $quick = new QuickPanel();
                    $quick->id_quick = Uuid::uuid4();
                    $quick->user_id_from = $data_lembur->id_user_atasan;
                    $quick->user_id_to = $data_lembur->id_user_atasan2;
                    $quick->title_quick = 'Pengajuan lembur';
                    $quick->message_quick = 'Ada Pengajuan lembur Baru Dari ' . $data_lembur->nama_karyawan;
                    $quick->status = 0;
                    $quick->quick_type = 'lembur';
                    $quick->menu_id = Auth::user()->menu_active;
                    $quick->content_id = $data_lembur->id;
                    $quick->created_at = date('Y-m-d H:i:s');
                    $quick->updated_at = date('Y-m-d H:i:s');
                    $quick->save();
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval lembur',
                        'id_table' => $data_lembur->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'lembur',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan lembur sebagai atasan 1 ' .  $data_lembur->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                } else {
                    $data_lembur->update([
                        'status_approve_atasan2' => 1,
                        'status_lembur' => 2,
                        'no_form_lembur' => $no_form,
                        'ttd_atasan2' => $fileName,
                        'waktu_approve2' => date('Y-m-d H:i:s'),
                        'jam_finger' => $request->jam_finger,
                    ]);
                    QuickPanel::where('content_id', $request->id_lembur)->update([
                        'read_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'menu_id' => null,
                        'menu_name' => 'approval lembur',
                        'id_table' => $data_lembur->id,
                        'holding_id' => $holdings->id ?? '-',
                        'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'lembur',
                        'activity' => 'add',
                        'description' => 'menyetujui pengajuan lembur sebagai atasan 2 ' .  $data_lembur->nama_karyawan,
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                }
            }




            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan lembur berhasil disetujui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function cetak_form_lembur($id, $karyawan_id)
    {
        $lembur = Lembur::with('Atasan1', 'Atasan2', 'AtasanHRD')->with('LemburDetail')->where('id', $id)->first();
        $karyawan_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $karyawan_id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $karyawan_id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_lembur' => $lembur,
            'karyawan_jabatan' => $karyawan_jabatan,
            'user_karyawan' => $user_karyawan,
            'holding' => $holding,
            // 'logo' => $data['logo'],
            // 'ttd_user' => $data['ttd_pemohon'],
            // 'ttd_atasan' => $data['ttd_atasan'],
            // 'ttd_atasan2' => $data['ttd_atasan2']
        ];
        // dd($lembur->ttd_atasan);
        $pdf = PDF::setOptions([
            'defaultFont' => 'Poppins, sans-serif',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'chroot' => public_path(), // penting!
        ])
            ->loadView('users/lembur/lembur_form', $data)
            ->setPaper('letter', 'potrait');
        // dd($user_karyawan->name);

        return $pdf->stream(
            'FORM_LEMBUR_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
    public function pantau_lembur($id)
    {
        // dd($id);
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

        return view('users.lembur.user', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id,
            'menu_id' => $id,
        ]);
    }
    public function user_detail($id, $id_lembur_detail)
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
        $data = LemburDetail::with('Lembur')->where('id', $id_lembur_detail)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();

        return view('users.lembur.user_detail', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'id' => $id_lembur_detail,
            'data' => $data,
            'menu_id' => $id,
        ]);
    }
}
