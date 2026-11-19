<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class SitesController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $roleId = Auth::user();
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
        $holding = Holding::where('holding_code', $holding)->first();
        return view('admin.site.site', [
            'title' => 'Master Sites',
            'holding' => $holding,
            'menus' => $menus,
            'master_site' => Site::all()
        ]);
    }

    public function addSite(Request $request, $holding)
    {
        // dd($request->all());
        $holdings = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                // 'id_holding' => 'required',
                'nama_site' => 'required',
                'alamat_site' => 'required',
                'status_site' => 'required',
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
            Site::create(
                [
                    'site_name' => $request->nama_site,
                    'site_holding_category' => $holdings->id,
                    'site_alamat' => $request->alamat_site,
                    'site_status' => $request->status_site,
                    'gm_link' => $request->gm_link,
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'holding_id' => $holdings->id,
                'holding_category' => $holdings->holding_category,
                'table' => 'site',
                'activity' => 'create',
                'description' => 'menambah site ' . $request->nama_site,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function updateSite(Request $request, $holding)
    {
        // dd($request->all());
        $holdings = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                // 'id_holding' => 'required',
                'site_name' => 'required',
                'alamat_site' => 'required',
                'status_site' => 'required',
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
            Site::where('id', $request->id)->update(
                [
                    'site_name' => $request->site_name,
                    'site_alamat' => $request->alamat_site,
                    'site_status' => $request->status_site,
                    'gm_link' => $request->gm_link,
                ]
            );
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id,
                'holding_category' => $holdings->holding_category,
                'table' => 'site',
                'activity' => 'create',
                'description' => 'menambah site ' . $request->site_name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function datatable($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        // $table = Titik::get();
        $table = Site::with('Holding')->where('site_holding_category', $holding->id)->orderBy('id', 'ASC')->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)

                ->addColumn('holding', function ($row) {
                    $holding = $row->Holding->holding_name;
                    return $holding;
                })
                ->addColumn('lihat_maps', function ($row) use ($holding) {

                    // $btn = '<button id="btn_lihat_lokasi" data-id="' . $row->id . '" data-lokasi="' . $row->site_name . '" data-nama_titik="' . $row->site_name . '" data-lat="' . $row->site_lat . '" data-long="' . $row->site_long . '"  data-radius="' . $row->site_radius . '" data-holding="' . $holding->id . '" type="button" class="btn btn-sm btn-success "><i class="menu-icon tf-icons mdi mdi-file-image-marker-outline"></i>Lihat Maps</button>';
                    // return $btn;
                    if ($row->gm_link != null) {
                        $map = '<a href="' . url("$row->gm_link") . '" type="button" class="btn btn-success" target="_blank"><span class="tf-icons mdi mdi-map-marker-multiple"></span></a>';
                        return $map;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('lihat_maps_excel', function ($row) use ($holding) {

                    // $btn = '<button id="btn_lihat_lokasi" data-id="' . $row->id . '" data-lokasi="' . $row->site_name . '" data-nama_titik="' . $row->site_name . '" data-lat="' . $row->site_lat . '" data-long="' . $row->site_long . '"  data-radius="' . $row->site_radius . '" data-holding="' . $holding->id . '" type="button" class="btn btn-sm btn-success "><i class="menu-icon tf-icons mdi mdi-file-image-marker-outline"></i>Lihat Maps</button>';
                    // return $btn;
                    return $row->gm_link;
                })
                ->addColumn('option', function ($row) {

                    $btn = '<button id="btn_edit_site" 
                            data-id="' . $row->id . '" 
                            data-site_name="' . $row->site_name . '" 
                            data-alamat_site="' . $row->site_alamat . '" 
                            data-status_site="' . $row->site_status . '" 
                            data-gm_link="' . $row->gm_link . '" 
                            type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_site" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns([
                    'holding',
                    'option',
                    'lihat_maps',
                    'lihat_maps_excel',
                ])
                ->make(true);
        }
    }
    public function site_delete(Request $request)
    {
        // dd($request->all());
        try {
            $delete = Site::where('id', $request->id)->first();
            $holding = Holding::where('id', $delete->site_holding_category)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $delete->site_holding_category,
                'holding_category' => $holding->holding_category,
                'table' => 'site',
                'activity' => 'delete',
                'description' => 'menghapus site ' . $delete->site_name,
                'keterangan' => 'Success',
            ]);
            $delete->delete();
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
