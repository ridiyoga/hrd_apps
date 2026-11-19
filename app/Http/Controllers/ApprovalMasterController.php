<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalGroup;
use App\Models\ApprovalUser;
use App\Models\Departemen;
use App\Models\Holding;
use App\Models\JabatanMaster;
use App\Models\LevelJabatan;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Dompdf\Adapter\GD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class ApprovalMasterController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
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

        $levels = JabatanMaster::OrderBy('jabatan_name', 'asc')->get();
        return view('admin.approval.index', [
            'levels' => $levels,
            'menus' => $menus,
            'holding' => $holding,
        ]);
    }
    public function get_tree_approval1($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            $data_approval = ApprovalGroup::with(['approval' => function ($query) use ($holding) {
                // $query->where('holding_id',  $holding->id);
                // $query->with('Departemen');
                $query->with('levelJabatan')->with('approvalParent');
            }])
                ->where('holding_id',  $holding->id)
                ->orderBy('nama_group', 'ASC')
                ->get();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_approval,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_tree_approval($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $data_approval = ApprovalGroup::with(['approval' => function ($query) use ($holding) {
            $query->where('holding_id',  $holding->id);
            $query->whereNull('parent_approval_id');
            $query->with('levelJabatan')
                ->with('approvalParent');
        }])
            ->where('holding_id',  $holding->id)
            ->orderBy('nama_group', 'ASC')
            ->get();
        // dd($data_approval);
        // dd($data_approval);
        return response()->json(
            $data_approval->map(fn($g) => [
                'id' => $g->id_group,
                'nama_group' => $g->nama_group,
                'tree' => $this->map($g->approval),
            ])
            // $data_approval
        );
    }
    private function map($approval)
    {
        // dd($approval, ' ok');

        return $approval->map(function ($l) {
            return [
                'id' => $l->id_approval,
                'group_id' => $l->group_id,
                'name' => $l->nama_approval,
                'level_approval' => $l->levelJabatan != null ? $l->levelJabatan->id : '',
                'departemen_id' => $l->departemen_id,
                'parent_approval_id' => $l->parent_approval_id,
                // 'title' => '<i class="mdi mdi-account mdi-24px text-primary"></i><span>' . $l->levelJabatan != null ? $l->levelJabatan->jabatan_name : '' . '</span>',
                'title' => $l->levelJabatan != null ? $l->levelJabatan->level_name : '',
                'children' => $this->map($l->approvalParent)
            ];
        });
    }
    public function get_add_approval($id, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            $departemen = Departemen::where('holding', $holding->id)->orderBy('nama_departemen')->get();
            $group = ApprovalGroup::where('id_group', $id)->get();
            $get_holding = Holding::get();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data fetched successfully.',
                'departemen' => $departemen,
                'holding' => $get_holding,
                'group' => $group
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
    public function get_group($id, $holding)
    {
        // dd($id);
        try {
            $appr = Approval::where('group_id', $id)->orderBy('nama_approval')->get();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data fetched successfully.',
                'data' => $appr,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_menu_group($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            $menu_mobile = Menu::where('kategori', 'mobile')->orderBy('name')->get();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data fetched successfully.',
                'data' => $menu_mobile,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_parent($id_approval, $group_id, $holding_id)
    {
        // dd($id);
        try {
            $appr2 = Approval::where('group_id', $group_id)->where('id_approval', '!=', $id_approval)->orderBy('nama_approval')->with('approvalParent')->get();
            $departemen = Departemen::where('holding', $holding_id)->orderBy('nama_departemen')->get();
            $holding = Holding::get();
            $group = ApprovalGroup::get();
            // dd($appr2, $group_id, $id_approval);
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data fetched successfully.',
                'data' => $appr2,
                'departemen' => $departemen,
                'holding' => $holding,
                'group' => $group
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function datatable($holding)
    {
        $data = Approval::with('levelJabatan')->with('approvalGroup')->with('approvalParent')->with('Departemen')->get();
        // dd($data);
        return DataTables::of($data)
            ->addColumn('group', function ($row) {
                return $row->approvalGroup->nama_group ?? '-';
            })
            ->addColumn('departemen', function ($row) {
                if ($row->Departemen == NULL) {
                    return '-';
                } else {
                    return $row->Departemen->nama_departemen;
                }
            })
            ->addColumn('level_approval', function ($row) {
                return $row->levelJabatan->level_name;
            })
            ->addColumn('parent', function ($row) {
                return $row->approvalParent->nama_approval ?? '-';
            })
            ->addColumn('option', function ($row) {
                $option = '<button type="button" class="btn btn-sm btn-warning" id="btn-edit-approval" 
                            data-id_approval="' . $row->id_approval . '"
                            data-group_id="' . $row->group_id . '"
                            data-nama_approval="' . $row->nama_approval . '"
                            data-level_approval="' . $row->level_approval . '"
                            data-parent_approval_id="' . $row->parent_approval_id . '"
                            >
                            <i class="mdi mdi-pencil"></i>
                            </button> ';
                $option .= '<button type="button" class="btn btn-sm btn-danger" id="btn-delete-approval" data-id_approval="' . $row->id_approval . '"><i class="mdi mdi-delete"></i></button>';
                return $option;
            })
            ->rawColumns(['option', 'departemen'])
            ->make(true);
    }
    public function group_datatable($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $data = ApprovalGroup::where('holding_id', $holding->id)->orderBy('created_at', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('nama_group', function ($row) {
                return $row->nama_group;
            })
            ->addColumn('daftar_menu', function ($row) {

                // 🔐 Pastikan menu_id array
                $menuIds = [];

                if (is_array($row->menu_id)) {
                    $menuIds = $row->menu_id;
                } elseif (is_string($row->menu_id)) {
                    $decoded = json_decode($row->menu_id, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $menuIds = $decoded;
                    }
                }

                if (empty($menuIds)) {
                    return '-';
                }

                // 🔥 Ambil menu SEKALI query
                $menus = Menu::whereIn('id', $menuIds)
                    ->select('name', 'icon', 'main')
                    ->orderBy('name')
                    ->get();

                // 🧩 Build HTML list
                $html = '<ul class="mb-0 ps-3">';

                foreach ($menus as $menu) {
                    if ($menu->main == 1) {
                        $main = ' (Menu Utama)';
                    } else {
                        $main = '';
                    }
                    $html .= '
                    <li style="list-style:none; display:flex; align-items:center; gap:6px;">
                        <span>' . $menu->icon . '</span>
                        <span>' . e($menu->name)  . $main . '</span>
                    </li>';
                }
                $html .= '</ul>';

                return $html;
            })
            ->addColumn('option', function ($row) {

                $menuId = is_array($row->menu_id)
                    ? json_encode($row->menu_id)
                    : $row->menu_id;

                $option = '
                <button type="button"
                    class="btn btn-sm btn-warning"
                    id="btn-edit-group"
                    data-id_group="' . e($row->id_group) . '"
                    data-nama_group="' . e($row->nama_group) . '"
                    data-menu_update=\'' . e($menuId) . '\'>
                    <i class="mdi mdi-pencil"></i>
                </button> <button type="button" class="btn btn-sm btn-danger" id="btn-delete-group" data-id_group="' . e($row->id_group) . '"><i class="mdi mdi-delete"></i></button>';
                return $option;
            })
            ->rawColumns([
                'option',
                'daftar_menu'
            ])
            ->make(true);
    }
    public function group_delete(Request $request)
    {
        // dd($request->all());
        try {
            $get_approval = Approval::where('group_id', $request->id_group)->first();
            if ($get_approval) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Data Tidak Bisa Dihapus Karena Memiliki Approval Aktif',
                ]);
            } else {
                $approval =  ApprovalGroup::where('id_group', $request->id_group)->first();

                $holding = Holding::where('id', $approval->holding_id)->first();
                ApprovalUser::where('approval_id', $request->id_approval)->delete();
                $user_name = User::where('id', $request->user()->id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    // 'menu_id' => $request->menu_id,
                    'menu_name' => 'Master Approval',
                    'id_table' => $request->id_group,
                    'holding_id' => $approval->id,
                    'holding_category' => $holding->holding_category,
                    'table' => 'master_approval_group',
                    'activity' => 'delete',
                    'description' => 'menghapus Approval grup ' .  $approval->nama_group,
                    'keterangan' => 'Success',
                ]);
                $approval->delete();
                return response()->json([
                    'code' => 200,
                    'message' => 'Data Berhasil Dihapus',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approval_save_add($holding, Request $request)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            // dd($final_holding, $holding);
            $approval =  new Approval();
            $approval->id_approval          = Uuid::uuid4();
            $approval->group_id             = $request->group_id;
            $approval->nama_approval        = $request->nama_approval;
            $approval->level_approval       = $request->level_approval;
            $approval->parent_approval_id   = $request->parent_approval_id;
            $approval->departemen_id        = $request->departemen_id;
            $approval->holding_id           = $holding->id;
            $approval->created_at           = now();
            $approval->updated_at           = now();
            $approval->save();

            // $nama_menu = Menu::where('id', $request->menu_id)->first();
            $group = ApprovalGroup::where('id_group', $request->group_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Master Approval',
                'id_table' => $approval->id_approval,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'master_approval',
                'activity' => 'create',
                'description' => 'Menambahkan Approval pada grup ' . $group->nama_group . ', ' .  $request->nama_approval,
                'keterangan' => 'Success',
            ]);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Approval(s) added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approval_save_update($holding, Request $request)
    {
        // dd($request->all());
        try {
            Approval::where('id_approval', $request->id_approval)->update([
                'group_id'   => $request->group_id,
                'nama_approval'   => $request->nama_approval,
                'level_approval'    => $request->level_approval,
                'parent_approval_id'    => $request->parent_approval_id,
                'departemen_id'    => $request->departemen_id_update,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $holding = Holding::where('holding_code', $holding)->first();

            $group = ApprovalGroup::where('id_group', $request->group_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Master Approval',
                'id_table' => $request->id_approval,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'master_approval',
                'activity' => 'update',
                'description' => 'Menambahkan Anak Approval pada grup ' . $group->nama_group . ', ' .  $request->nama_approval,
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Approval(s) updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    /**
     * Save add approval group
     * 
     * @param string $holding
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function approval_group_save_add($holding, Request $request)
    {
        // dd($request->all());
        try {
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'menu_id' => 'required',
                'nama_group' => 'required',
            ], [
                'menu_id.required' => 'Menu harus dipilih',
                'nama_group.required' => 'Nama Group harus diisi',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'errors' => $validator->errors()
                ]);
            }
            $approval = new ApprovalGroup();
            $approval->id_group = Uuid::uuid4();
            $approval->nama_group   = $request->nama_group;
            $approval->menu_id      = $request->menu_id;
            $approval->holding_id   = $request->holding_id;
            $approval->created_at = now();
            $approval->updated_at = now();
            $approval->save();

            $holding = Holding::where('holding_code', $holding)->first();

            // $group = ApprovalGroup::where('id_group', $request->group_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Master Approval',
                'id_table' => $approval->id_group,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'master_approval_group',
                'activity' => 'update',
                'description' => 'Menambahkan Approval grup, ' .  $approval->nama_group,
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Approval Group added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approval_group_save_update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'menu_update' => 'required',
            'nama_group' => 'required',
        ], [
            'menu_update.required' => 'Menu harus dipilih',
            'nama_group.required' => 'Nama Group harus diisi',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }
        try {
            $approval_group = ApprovalGroup::where('id_group', $request->id_group)->first();
            $holding = Holding::where('id', $approval_group->holding_id)->first();

            ApprovalGroup::where('id_group', $request->id_group)->update([
                'nama_group'     => $request->nama_group,
                'menu_id' => $request->menu_update,
                'holding_id' => $request->holding_id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Master Approval',
                'id_table' => $request->id_group,
                'holding_id' => $approval_group->id,
                'holding_category' => $holding->holding_category,
                'table' => 'divisi',
                'activity' => 'approval_group',
                'description' => 'Mengedit Approval grup, ' .  $approval_group->nama_group,
                'keterangan' => 'Success',
            ]);
            // $approval->id_group = Uuid::uuid4();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Approval Group updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approval_delete(Request $request)
    {
        // dd($request->all());
        try {
            $get_approval = Approval::where('parent_approval_id', $request->id_approval)->first();
            if ($get_approval) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Data Tidak Bisa Dihapus Karena Memiliki Sub Approval',
                ]);
            } else {
                $approval = Approval::where('id_approval', $request->id_approval)->first();
                $holding = Holding::where('id', $approval->holding_id)->first();
                ApprovalUser::where('approval_id', $request->id_approval)->delete();
                $user_name = User::where('id', $request->user()->id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    // 'menu_id' => $request->menu_id,
                    'menu_name' => 'Master Approval',
                    'id_table' => $request->id_approval,
                    'holding_id' => $approval->id,
                    'holding_category' => $holding->holding_category,
                    'table' => 'master_approval',
                    'activity' => 'delete',
                    'description' => 'menghapus Approval ' . $approval->nama_approval . ' pada grup ' .  $approval->approvalGroup->nama_group,
                    'keterangan' => 'Success',
                ]);
                $approval->delete();
                return response()->json([
                    'code' => 200,
                    'message' => 'Data Berhasil Dihapus',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
