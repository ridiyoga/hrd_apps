<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HoldingController extends Controller
{
    public function index($holding, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
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
        return view('admin.holding.holding', [
            'title' => 'Master Holding',
            'holding' => $holding,
            'menus' => $menus,
            'master_holding' => Holding::all()
        ]);
    }
    public function datatable(Request $request, $holding)
    {
        // $table = Titik::get();
        $table = Holding::orderBy('id', 'ASC')->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('holding_image', function ($row) {
                    if ($row->holding_image != null) {
                        return '<a type="button" href="' . asset('/storage/holding_image/' . $row->holding_image) . '" class="btn btn-sm btn-success" target="_blank" id="btn_ktp" accept="application/pdf"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Lihat</button>';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('option', function ($row) {

                    $btn = '<button id="btn_edit_holding" 
                    data-id="' . $row->id . '" data-code="' . $row->holding_code . '" 
                    data-holding="' . $row->holding_name . '" 
                    data-category="' . $row->holding_category . '" 
                    data-holding_name_hint="' . $row->holding_name_hint . '" 
                    data-holding_number="' . $row->holding_number . '" 
                    data-holding_image="' . $row->holding_image . '" 
                    data-kota="' . $row->kota . '" 
                    data-alamat="' . $row->alamat . '" 
                    data-telepon="' . $row->telepon . '" 
                    data-website="' . $row->website . '" 
                    type="button" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_holding" 
                    data-id="' . $row->id . '" 
                    data-holding_image="' . $row->holding_image . '" 
                    class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['option', 'holding_image'])
                ->make(true);
        }
    }
    public function holding_update(Request $request)
    {
        // dd($request->all());
        try {
            if ($request->holding_image != null) {
                if ($request->old_image != null) {
                    if (Storage::disk('holding_image')->exists($request->old_image)) {
                        Storage::disk('holding_image')->delete($request->old_image);
                    }
                }
                $file = $request->file('holding_image')->store('holding_image');
                $holding_image = basename($file);
            } else {
                $holding_image = $request->old_image;
            }
            Holding::where('id', $request->id_holding)->update([
                'holding_name' => $request->nama_holding,
                'holding_category' => $request->holding_category,
                'holding_number' => $request->holding_number,
                'holding_name_hint' => $request->holding_name_hint,
                'holding_image' => $holding_image,
                'kota' => $request->kota,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'website' => $request->website,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_holding,
                'holding_id' => $request->id_holding,
                'holding_category' => $request->holding_category,
                'table' => 'holding',
                'activity' => 'update',
                'description' => 'mengedit holding ' . $request->nama_holding,
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
    public function holding_post(Request $request)
    {
        // dd($request->all());
        try {
            if ($request->holding_image != null) {
                $file = $request->file('holding_image')->store('holding_image');
                $holding_image = basename($file);
            } else {
                $holding_image = null;
            }
            $id_holding = Uuid::uuid4();
            Holding::create([
                'id'           => $id_holding,
                'holding_name' => $request->nama_holding,
                'holding_category' => $request->holding_category,
                'holding_code' => $request->holding_category . '_' . $id_holding,
                'holding_number' => $request->holding_number,
                'holding_name_hint' => $request->holding_name_hint,
                'holding_image' => $holding_image,
                'kota' => $request->kota,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'website' => $request->website,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_holding,
                'holding_id' => $request->id_holding,
                'holding_category' => $request->holding_category,
                'table' => 'holding',
                'activity' => 'create',
                'description' => 'menambah holding ' . $request->nama_holding,
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
    public function holding_delete(Request $request)
    {
        // dd($request->all());
        $delete = Holding::where('id', $request->id)->first();
        try {
            if ($request->holding_image != NULL) {

                Storage::disk('holding_image')->delete($request->holding_image);
            }
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $request->id,
                'holding_category' => $delete->holding_category,
                'table' => 'holding',
                'activity' => 'delete',
                'description' => 'menghapus holding ' . $delete->holding_name,
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
