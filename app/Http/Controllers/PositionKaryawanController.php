<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\ApprovalUser;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\PositionKaryawan;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Site;
use App\Models\StrukturModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Colors\Rgb\Channels\Red;

class PositionKaryawanController extends Controller
{
    public function index($id, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan Login Terlebih Dahulu');
            return redirect()->route('login');
        } else {
            $user_karyawan = Karyawan::with('MappingShift')->where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
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

            User::where('id', Auth::user()->id)->update(['menu_active' => $id]);
        }
        return view('users.posisi_karyawan.index', [
            'title' => 'Posisi Karyawan',
            'menu_id' => $id,
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar

        ]);
    }

    public function get_tim_karyawan($id)
    {
        try {

            $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $id)->get();
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            // lihat struktu approval
            $access = ApprovalUser::with([
                'Approval',
                'Approval.ApprovalGroup'
            ])
                ->whereHas(
                    'Approval.ApprovalGroup',
                    fn($q) =>
                    $q->where('menu_id', 'LIKE', '%' . Auth::user()->menu_active . '%')
                )
                ->where('karyawan_id', $id)
                ->first();
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

            while ($currentApprovalId && $found < 2) {
                $approvalUser = ApprovalUser::with(['User', 'Karyawan', 'Approval'])
                    ->whereHas('Approval', function ($q) use ($currentApprovalId) {
                        $q->where('id_approval', $currentApprovalId);
                    })
                    ->whereHas('Karyawan') // 🔑 hanya yang punya karyawan
                    ->first();

                // kalau ketemu → STOP
                if ($approvalUser) {
                    if ($found === 0) {
                        $atasan1 = $approvalUser;
                    } elseif ($found === 1) {
                        $atasan2 = $approvalUser;
                    }
                    $found++;
                }

                // kalau tidak ketemu, naik ke parent berikutnya
                $parentApproval = Approval::where('id_approval', $currentApprovalId)->first();

                $currentApprovalId = $parentApproval?->parent_approval_id;
            }
            // fallback: kalau atasan 2 kosong → samakan dengan atasan 1
            if (!$atasan2) {
                $atasan2 = $atasan1;
            }
            //   END lihat struktu approval
            $holdingIds    = $karyawan_jabatan->pluck('holding_id')->toArray();
            $jabatanIds    = $karyawan_jabatan->pluck('jabatan_id')->toArray();
            $struktur = StrukturModel::with('UserKaryawan', 'ChildrenRecursive')->whereIn('jabatan', $jabatanIds)->whereIn('holding', $holdingIds)->get();
            if ($struktur->isEmpty()) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Struktur not found, Hubungi Admin'
                ]);
            }
            $bawahanKaryawanIds = collectBawahan($struktur);
            $user_bawahan = Karyawan::with(['KaryawanJabatan' => function ($q) {
                $q->with('Divisi');
                $q->with('Jabatan');
                $q->with('Bagian');
                $q->with('Departemen');
                $q->where('main', 1);
                $q->where('is_active', 1);
            }])->whereIn('id', $bawahanKaryawanIds)
                ->whereNotIn('id', [$id])
                ->where('status_aktif', 'AKTIF')
                ->orderBy('name', 'asc')
                ->get();
            $penempatan_holding = Holding::OrderBy('holding_name', 'asc')->get();
            return response()->json([
                'code' => 200,
                'data' => $user_bawahan,
                'penempatan_holding' => $penempatan_holding,
                'atasan' => $atasan1,
                'atasan2' => $atasan2,
                'user' => $user_karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_PosisiKaryawan($id)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $data = PositionKaryawan::where('user_pengajuan', $user_karyawan->id)->get();
            return response()->json([
                'code' => 200,
                'data' => $data,
                'user' => $user_karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_change_karyawan($id)
    {
        try {
            $data = KaryawanJabatan::with(['Karyawan' => function ($q) {
                $q->where('status_aktif', 'AKTIF');
                $q->with('Departemen');
                $q->with('Divisi');
                $q->with('Jabatan');
                $q->with('Bagian');
                $q->with('PenempatanKerja');
            }])
                ->where('karyawan_id', $id)
                ->where('main', 1)
                ->first();
            $result = [
                'id' => $data->id,
                'name' => $data->Karyawan->name,
                'nik' => $data->Karyawan->nik,
                'penempatan' => $data->Karyawan->PenempatanKerja->site_name,
                'departemen' => $data->Departemen->nama_departemen,
                'divisi' => $data->Divisi->nama_divisi,
                'bagian' => $data->Bagian->nama_bagian,
                'jabatan' => $data->Jabatan->nama_jabatan,
            ];
            return response()->json([
                'code' => 200,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function get_departemen(Request $request)
    {
        try {
            $departemen = Departemen::where('holding', $request->id)->orderBy('nama_departemen', 'ASC')->get();
            $site = Site::where('site_holding_category', $request->id)->get();
            return response()->json([
                'code' => 200,
                'data' => $departemen,
                'site' => $site
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
        try {
            $divisi = Divisi::where('dept_id', $request->id)->orderBy('nama_divisi', 'ASC')->get();
            return response()->json([
                'code' => 200,
                'data' => $divisi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_bagian(Request $request)
    {
        try {
            $bagian = Bagian::where('divisi_id', $request->id)->orderBy('nama_bagian', 'ASC')->get();
            return response()->json([
                'code' => 200,
                'data' => $bagian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_jabatan(Request $request)
    {
        try {
            $jabatan = Jabatan::where('bagian_id', $request->id)->orderBy('nama_jabatan', 'ASC')->get();
            return response()->json([
                'code' => 200,
                'data' => $jabatan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function proses_add_update_posisi(Request $request)
    {
        dd($request->all());
        try {
            $rules = [
                'user_dituju' => 'required',
                'penempatan_holding_baru' => 'required',
                'lokasi_baru' => 'required',
                'departemen_baru' => 'required',
                'divisi_baru' => 'required',
                'bagian_baru' => 'required',
                'jabatan_baru' => 'required',
                'masa_percobaan' => 'required',
                'periode' => 'required',
                'gaji' => 'required',
                'tunjangan' => 'max:100',
                'fasilitas' => 'max:100',
                'signature' => 'required',
                'perubahan_posisi' => 'required',

            ];
            $custom_messages = [
                'user_dituju.required' => 'Karyawan tidak boleh kosong',
                'penempatan_holding_baru.required' => 'Penempatan tidak boleh kosong',
                'lokasi_baru.required' => 'Lokasi tidak boleh kosong',
                'departemen_baru.required' => 'Departemen tidak boleh kosong',
                'divisi_baru.required' => 'Divisi tidak boleh kosong',
                'bagian_baru.required' => 'Bagian tidak boleh kosong',
                'jabatan_baru.required' => 'Jabatan tidak boleh kosong',
                'masa_percobaan.required' => 'Masa Percobaan tidak boleh kosong',
                'periode.required' => 'Periode tidak boleh kosong',
                'gaji.required' => 'Gaji tidak boleh kosong',
                'tunjangan.required' => 'Tunjangan tidak boleh kosong',
                'fasilitas.required' => 'Fasilitas tidak boleh kosong',
                'signature.required' => 'Signature tidak boleh kosong',
                'perubahan_posisi.required' => 'Perubahan Posisi tidak boleh kosong',
            ];
            $validator = Validator::make($request->all(), $rules, $custom_messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ]);
            }
            $add = new PositionKaryawan();
            $add->user_dituju = $request->user_dituju;
            $add->penempatan_holding_baru = $request->penempatan_holding_baru;
            $add->lokasi_baru = $request->lokasi_baru;
            $add->departemen_baru = $request->departemen_baru;
            $add->divisi_baru = $request->divisi_baru;
            $add->bagian_baru = $request->bagian_baru;
            $add->jabatan_baru = $request->jabatan_baru;
            $add->masa_percobaan = $request->masa_percobaan;
            $add->periode = $request->periode;
            $add->gaji = $request->gaji;
            $add->tunjangan = $request->tunjangan;
            $add->fasilitas = $request->fasilitas;
            $add->signature = $request->signature;
            $add->perubahan_posisi = $request->perubahan_posisi;
            $add->save();
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
