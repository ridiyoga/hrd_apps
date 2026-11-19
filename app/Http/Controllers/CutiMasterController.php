<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Cuti;
use App\Models\Holding;
use App\Models\KategoriCuti;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;



class CutiMasterController extends Controller
{
    public function cuti_kategori($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $get_holding = Holding::where('holding_code', $holding)->first();
        // dd($get_holding);
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
        if ($get_holding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        // $get = Divisi::with('Departemen')->get();
        // dd($get);
        return view('admin.cuti.cuti_kategori', [
            'title' => 'Master Divisi',
            'holding' => $get_holding,
            'menus' => $menus,
        ]);
    }
    public function dt_cuti_kategori()
    {
        $table = KategoriCuti::get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addcolumn('target_gender', function ($row) {
                    $array = explode(',', trim($row->target_gender, '{}'));
                    $html = '<ul class="mb-0 ps-3">';

                    if (in_array('1', $array)) {
                        $html .= '
                        <li>
                            <span class="small me-1">Laki-Laki, </span>
                        </li>';
                    }
                    if (in_array('2', $array)) {
                        $html .= '
                        <li>
                            <span class="small me-1">Perempuan</span>
                        </li>
                        ';
                    }

                    $html .= '</ul>';
                    return $html;
                })
                ->addcolumn('target_status_nikah', function ($row) {
                    $array = explode(',', trim($row->target_status_nikah, '{}'));
                    $html = '<ul class="mb-0 ps-3">';

                    if (in_array('1', $array)) {
                        $html .= '
                        <li>
                            <span class="small">Lajang, </span>
                        </li>';
                    }
                    if (in_array('2', $array)) {
                        $html .= '
                        <li>
                            <span class="small">Kawin, </span>
                        </li>
                        ';
                    }
                    if (in_array('3', $array)) {
                        $html .= '
                        <li>
                            <span class="small">Cerai Hidup, </span>
                        </li>
                        ';
                    }
                    if (in_array('3', $array)) {
                        $html .= '
                        <li>
                            <span class="small">Cerai Mati</span>
                        </li>
                        ';
                    }

                    $html .= '</ul>';
                    return $html;
                })
                ->addcolumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge bg-label-success me-1">AKTIF</span>';
                    } else {
                        return '<span class="badge bg-label-danger me-1">NON AKTIF</span>';
                    }
                })
                ->addColumn('option', function ($row) {
                    // $btn =
                    //     '
                    //     <button type="button" id="btn_edit_jabatan"
                    //         data-id_jbt_karyawan="' . $row->id_jbt_karyawan . '"
                    //         data-is_active="' . $row->is_active . '"
                    //         data-main="' . $row->main . '"
                    //         class="btn btn-sm btn-primary">
                    //         <i class="mdi mdi-pencil"></i>
                    //     </button>
                    //     <button type="button" id="btn_delete_jabatan"
                    //         data-id_jbt_karyawan="' . $row->id_jbt_karyawan . '"
                    //         class="btn btn-sm btn-danger">
                    //         <i class="mdi mdi-delete"></i>
                    //     </button>
                    //     ';
                    $btn =
                        '
                        <button type="button" id="btn_edit_kategori"
                            data-id="' . $row->id . '"
                            data-nama_cuti="' . $row->nama_cuti . '"
                            data-jumlah_hari="' . $row->jumlah_hari . '"
                            data-target_gender="' . $row->target_gender . '"
                            data-target_status_nikah="' . $row->target_status_nikah . '"
                            data-status="' . $row->status . '"
                            class="btn btn-sm btn-primary">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" id="btn_delete_kategori"
                            data-id="' . $row->id . '"
                            class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </button>
                        ';
                    return $btn;
                })
                ->rawColumns(['target_gender', 'target_status_nikah', 'status', 'option'])
                ->make(true);
        }
    }
    public function cuti_kategori_create(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nama_cuti' => 'required',
                'jumlah_hari' => 'required',
                'target_gender' => 'required|array',
                // 'target_gender' => 'in:1,2',
                'target_status_nikah' => 'required|array',
                // 'target_status_nikah' => 'required|in:1,2',
                // 'status' => 'required',
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
            $gender = '{' . implode(',', $request->target_gender) . '}';
            $statusNikah = '{' . implode(',', $request->target_status_nikah) . '}';

            $cuti = new KategoriCuti();
            $cuti->id = Uuid::uuid4();
            $cuti->nama_cuti = $request->nama_cuti;
            $cuti->jumlah_hari = $request->jumlah_hari;
            $cuti->target_gender = $gender;
            $cuti->target_status_nikah = $statusNikah;
            $cuti->status = '1';
            $cuti->created_at = date('Y-m-d H:i:s');
            $cuti->save();
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $cuti->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'kategori_cuti',
                'activity' => 'create',
                'description' => 'Menambah master cuti kategori ',
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function cuti_kategori_update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nama_cuti' => 'required',
                'jumlah_hari' => 'required',
                'target_gender' => 'required|array',
                // 'target_gender' => 'in:1,2',
                'target_status_nikah' => 'required|array',
                // 'target_status_nikah' => 'required|in:1,2',
                // 'status' => 'required',
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
            $gender = '{' . implode(',', $request->target_gender) . '}';
            $statusNikah = '{' . implode(',', $request->target_status_nikah) . '}';

            $cuti = KategoriCuti::where('id', $request->id)->first();
            $cuti->nama_cuti = $request->nama_cuti;
            $cuti->jumlah_hari = $request->jumlah_hari;
            $cuti->target_gender = $gender;
            $cuti->target_status_nikah = $statusNikah;
            $cuti->status = $request->status;
            $cuti->created_at = date('Y-m-d H:i:s');
            $cuti->save();
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'kategori_cuti',
                'activity' => 'update',
                'description' => 'mengubah master cuti kategori ',
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function cuti_kategori_delete(Request $request)
    {
        try {
            // dd($request->all());
            $cuti = Cuti::where('kategori_cuti_id', $request->id)->where('status_cuti', 2)->count();
            $kat = KategoriCuti::where('id', $request->id)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            if ($cuti == 0) {

                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'kategori_cuti',
                    'activity' => 'delete',
                    'description' => 'Menghapus master cuti',
                    'keterangan' => 'Success',
                ]);
                $kat->delete();
                return response()->json(['status' => 1]);
            } else {
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => '-',
                    'holding_category' => '-',
                    'table' => 'kategori_cuti',
                    'activity' => 'delete',
                    'description' => 'gagal menghapus master kategori cuti karena sudah digunakan',
                    'keterangan' => 'Failed',
                ]);
                return response()->json(['status' => 2]);
            }
            // return response()->json([
            //     'code' => 200,
            //     'message' => 'Data Berhasil Dihapus'
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
