<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanNonaktif;
use App\Models\KelolaBaru;
use App\Models\KelolaBaruDetail;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class KelolaBaruController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
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
        $karyawan = Karyawan::orderBy('name')->where('status_aktif', 'AKTIF')->get();
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        return view('admin.karyawan.karyawan_r', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
            'karyawan' => $karyawan,
        ]);
    }
    public function get_tanggal($tanggal)
    {
        // dd($tanggal);
        $detail = KelolaBaruDetail::with('KelolaBaru')
            ->whereHas('KelolaBaru', function ($query) {
                $query->where('baru', 1);
            })->pluck('id_karyawan');
        $get_karyawan = Karyawan::whereNotNull('tgl_join')
            ->whereDate('tgl_join', '>=', $tanggal)
            ->whereNotIn('id', $detail)
            ->orderBy('name', 'ASC')
            ->get();
        // dd($get_karyawan);
        echo "<option value=''>Pilih Karyawan...</option>";
        foreach ($get_karyawan as $karyawan) {
            echo "<option value='{$karyawan->id}' data-tgl_join='" . Carbon::parse($karyawan->tgl_join)->format('Y-m-d') . "'>
            {$karyawan->name}
          </option>";
        }
    }
    public function get_cut($tanggal)
    {
        // dd($tanggal);
        $detail = KelolaBaruDetail::with('KelolaBaru')
            ->whereHas('KelolaBaru', function ($query) {
                $query->where('baru', 2);
            })->pluck('id_karyawan');;
        $get_karyawan = KaryawanNonaktif::whereNotIn('id', $detail)
            ->whereDate('tanggal_nonaktif', '<=', $tanggal)
            ->where('status', 0)
            ->orderBy('nama_karyawan', 'ASC')
            ->get();
        // dd($detail);
        echo "<option value=''>Pilih Karyawan...</option>";
        foreach ($get_karyawan as $karyawan) {
            echo "<option value='{$karyawan->id_karyawan}' data-tanggal_nonaktif='" . Carbon::parse($karyawan->tanggal_nonaktif)->format('Y-m-d') . "'>
            {$karyawan->nama_karyawan}
          </option>";
        }
    }
    public function datatable(Request $request)
    {
        $table = KelolaBaru::orderBy('nama_karyawan', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button type="button" id="btn_delete_karyawan" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns([
                    'option',
                    'jumlah_divisi',
                    'option'
                ])
                ->make(true);
        }
    }

    public function insert(Request $request, $holding)
    {
        // dd($request->all());
        $holding  = Holding::where('holding_code', $holding)->first();
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'id_karyawan' => 'required',
                'tanggal_mulai' => 'required',
                'tanggal_selesai' => 'required',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        try {
            $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
            $kode = Uuid::uuid4();

            $start = $request->tanggal_mulai;
            $end = $request->tanggal_selesai;

            $start = Carbon::parse($request->tanggal_mulai);
            $end = Carbon::parse($request->tanggal_selesai);

            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $tanggal) {
                KelolaBaruDetail::create(
                    [
                        'id' => Uuid::uuid4(),
                        'id_r' => $kode,
                        'id_karyawan' => $karyawan->id,
                        'nama_karyawan' => $karyawan->name,
                        'tanggal_r' => $request->tanggal_mulai,
                        'created_at' => now()
                    ]
                );

                // Proses setiap tanggal
            }

            KelolaBaru::create(
                [
                    'id' => $kode,
                    'id_karyawan' => $karyawan->id,
                    'nama_karyawan' => $karyawan->name,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'deskripsi' => $request->deskripsi,
                    'created_by' => $user_karyawan->id,
                    'created_by_name' => $user_karyawan->name,
                    'baru' => $request->baru,
                    'created_at' => now()
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $kode,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'attendace_r',
                'activity' => 'create',
                'description' => 'Menambahkan karyawawn baru ' . $karyawan->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function update(Request $request, $holding)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'id_kendaraan' => 'required',
                'id_karyawan' => 'required',
                'deskripsi' => 'nullable|string',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        try {
            $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
            KelolaBaru::where('id', $request->id_kendaraan)->update([
                'id_karyawan' => $karyawan->id,
                'nama_karyawan' => $karyawan->name,
                'deskripsi' => $request->deskripsi,
                'updated_at' => now()

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_kendaraan,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'attendace_r',
                'activity' => 'update',
                'description' => 'mengedit nama karyawawn ' . $karyawan->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function delete($id, $holding, $menu_id)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $karyawan_r = KelolaBaru::where('id', $id)->first();
        KelolaBaruDetail::where('id_r', $id)->delete();

        $nama_menu = Menu::where('id', $menu_id)->first();
        $user_name = User::where('id', Auth::user()->id)->first();
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $id,
            'holding_id' => $holding->id,
            'holding_category' => $holding->holding_category,
            'table' => 'attendace_r',
            'activity' => 'delete',
            'description' => 'menghapus karyawawn ' . $karyawan_r->name,
            'keterangan' => 'Success',
        ]);

        $karyawan_r->delete();
        return response()->json(['status' => 1]);
    }
}
