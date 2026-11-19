<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalGroup;
use App\Models\ApprovalUser;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

use function Pest\Laravel\session;

class HakAksesApprovalController extends Controller
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
        $holding = Holding::where('holding_code', $holding)->first();
        return view('admin.karyawan.hak_akses.approval.index', [
            'menus' => $menus,
            'holding' => $holding,
        ]);
    }
    public function karyawan_datatable($id, $group, $holding)
    {
        try {
            $holdingData = Holding::where('holding_code', $holding)->first();

            $getApproval = Approval::find($id);

            $departemenIds = collect($getApproval->departemen_id)
                ->flatten()
                ->toArray();

            $table = Karyawan::with([
                'KaryawanJabatan' => function ($q) use ($departemenIds, $holdingData) {
                    $q->whereIn('departemen_id', $departemenIds)
                        ->with(['Departemen', 'Jabatan', 'Divisi', 'Bagian']);
                }
            ])
                ->withExists([
                    'ApprovalUser as is_checked' => function ($q) use ($id) {
                        $q->where('approval_id', $id);
                    }
                ])
                ->whereHas('KaryawanJabatan', function ($q) use ($departemenIds, $holdingData) {
                    $q->whereIn('departemen_id', $departemenIds)
                        ->where('holding_id', $holdingData->id);
                })
                ->whereHas('KaryawanJabatan.Jabatan', function ($q) use ($getApproval) {
                    $q->where('level_id', $getApproval->level_approval);
                })
                ->where('status_aktif', 'AKTIF')
                ->orderBy('name', 'asc')
                ->get();
            // dd($id, $group, $holding, $holdingData, $getApproval, $departemenIds);

            return response()->json([
                'code' => 200,
                'data' => $table
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'code' => 500,
                    'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
                ]
            );
        }
    }
    public function save_approval($holding, Request $request)
    {
        // dd($request->all());
        try {
            $approval_delete = ApprovalUser::where('approval_id', $request->id)->get();

            foreach ($approval_delete as $app) {

                $karyawan    = Karyawan::where('id', $app->karyawan_id)->first();
                $user_name   = User::where('id', $request->user()->id)->first();
                $approval_master    = Approval::where('id_approval', $request->id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'id_table' => $app->id_approval_user,
                    'menu_name' => 'Approval',
                    // 'holding_id' => $holdings->id ?? '-',
                    // 'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'approval_user',
                    'activity' => 'delete',
                    'description' => 'menghapus centang : ' . $karyawan->name . ' pada kotak ' . $approval_master->nama_approval . ' grup ' . $approval_master->approvalGroup->nama_group,
                    'keterangan' => 'Success',
                ]);
                $app->delete();
            }
            if ($request->menu_id == null || $request->menu_id == '') {
            } else {
                foreach ($request->menu_id as $id) {
                    $karyawan = Karyawan::where('id', $id)->first();
                    $user = User::where('karyawan_id', $karyawan->nomor_identitas_karyawan)->first();
                    if ($user == null) {
                        $user = '';
                    } else {
                        $user = $user->id;
                    }
                    $approval = new ApprovalUser();
                    $approval->id_approval_user = Uuid::uuid4();
                    $approval->approval_id = $request->id;
                    $approval->approval_user_id = $user;
                    $approval->karyawan_id = $id;

                    $karyawan    = Karyawan::where('id', $id)->first();
                    $user_name   = User::where('id', $request->user()->id)->first();
                    $approval_master    = Approval::where('id_approval', $request->id)->first();

                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $user_name->username,
                        'id_table' => $approval->id_approval_user,
                        'menu_name' => 'Approval',
                        // 'holding_id' => $holdings->id ?? '-',
                        // 'holding_category' => $holdings->holding_category ?? '-',
                        'table' => 'approval_user',
                        'activity' => 'update',
                        'description' => 'mencentang : ' . $karyawan->name . ' pada kotak ' . $approval_master->nama_approval . ' grup ' . $approval_master->approvalGroup->nama_group,
                        'keterangan' => 'Success',
                    ]);
                    $approval->save();
                }
            }
            return response()->json(
                [
                    'code' => 200,
                    'message' => 'Sukses Menambahkan Approval',
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'code' => 500,
                    'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
                ]
            );
        }
    }
    public function approvalChart($holding)
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
                'title' => '<span>' . $l->levelJabatan->level_name . '</span>',
                'children' => $this->map($l->approvalParent)
            ];
        });
    }
}
