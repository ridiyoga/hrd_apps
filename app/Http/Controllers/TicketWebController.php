<?php

namespace App\Http\Controllers;

use App\Models\RoleUsers;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\TicketMenu;
use App\Models\TicketSubMenu;
use App\Models\TicketKondisiMenu;
use App\Models\TicketHead;
use App\Models\TicketDetail;
use App\Models\TicketApprovalFlow;
use App\Models\TicketApprovalStep;
use App\Models\TicketApprovalStepCondition;
use App\Models\TicketDependencyRule;
use App\Models\TicketConditionValue;
use App\Models\TicketApprovalValidator;
use App\Models\TicketAssignmentMaster;
use App\Models\TicketDifficultyMaster;
use App\Models\TicketWorkSession;
use App\Models\TicketApprovalKaryawan;
use App\Models\TicketHubMenu;
use App\Models\TicketHubMenuApproval;
use App\Models\ApprovalGroup;

use App\Models\Jabatan;
use App\Models\JabatanMaster;
use App\Models\Bagian;
use App\Models\Divisi;
use App\Models\Departemen;
use App\Models\KaryawanJabatan;
use App\Models\Karyawan;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Ramsey\Uuid\Uuid;

use Illuminate\Http\Request;

class TicketWebController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding  = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();
        $roleId = count($get_role) > 0 ? $get_role : null;
        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();
        return view('admin.ticket.index', [
            'title'   => 'Ticket Menu Management',
            'holding' => $holding,
            'menus'   => $menus,
        ]);
    }

    public function datatable(Request $request)
    {
        $query = TicketMenu::with('departemens.Holding');
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        $data = $query->get();

        return DataTables::of($data)
            ->addColumn('departemen', function ($row) {
                if ($row->departemens->isEmpty()) {
                    return '<span class="text-muted" style="font-size:11px;"> — </span>';
                }

                $limit = 3;
                $shown = $row->departemens->take($limit);
                $remaining = $row->departemens->slice($limit);

                $items = $shown->map(function ($d) {
                    $holdingName = $d->Holding->holding_name ?? 'Tanpa Holding';
                    return '<li>' . e($holdingName) . ' - ' . e($d->nama_departemen) . '</li>';
                })->implode('');

                $html = '<div class="dept-cell"><ul class="dept-list">' . $items . '</ul>';

                if ($remaining->isNotEmpty()) {
                    $remCount = $remaining->count();
                    $remItems = $remaining->map(function ($d) {
                        $holdingName = $d->Holding->holding_name ?? 'Tanpa Holding';
                        return '<li>' . e($holdingName) . ' - ' . e($d->nama_departemen) . '</li>';
                    })->implode('');

                    $html .= '
                    <details class="dept-collapse">
                        <summary>
                            <span class="dept-toggle">+ ' . $remCount . ' departemen lainnya</span>
                        </summary>
                        <ul class="dept-list dept-extra-list">' . $remItems . '</ul>
                        <button type="button" class="dept-collapse-close">Sembunyikan</button>
                    </details>';
                }

                return $html . '</div>';
            })
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-xs btn-warning btn_edit_menu me-1"
                            data-id="'       . $row->id       . '"
                            data-nama="'     . $row->nama_menu . '"
                            data-kategori="' . $row->kategori  . '"
                            data-departemen-ids="' . $row->departemens->pluck('id')->implode(',') . '">
                            <i class="bx bx-edit"></i> Ubah
                        </button>';
                $btnDelete = '<button class="btn btn-xs btn-danger btn_delete_menu me-1"
                            data-id="' . $row->id . '">
                            <i class="bx bx-trash"></i> Hapus
                        </button>';
                return $btnEdit . $btnDelete;
            })
            ->rawColumns(['departemen', 'action'])
            ->make(true);
    }

    public function datatableKondisiMenu(Request $request)
    {
        $data = TicketKondisiMenu::all();
        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-xs btn-warning btn_edit_kondisi me-1"
                            data-id="' . $row->id . '"
                            data-kode="' . $row->code . '"
                            data-label="' . $row->label_kondisi . '"
                            data-tipe="' . $row->tipe_data . '"
                            data-status="' . $row->status . '">
                            <i class="bx bx-edit"></i> Ubah
                        </button>';
                $btnDelete = '<button class="btn btn-xs btn-danger btn_delete_kondisi me-1"
                            data-id="' . $row->id . '">
                            <i class="bx bx-trash"></i> Hapus
                        </button>';
                return $btnEdit . $btnDelete;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori'  => 'required|in:1,2,3', //1. epicor, 2. accurate sp, 3. accurate aja
            'departemen_ids' => 'nullable|array',
            'departemen_ids.*' => 'exists:departemens,id',
        ]);
        // dd($request->all());
        $menu = TicketMenu::create([
            'id' => Uuid::uuid4(),
            'nama_menu' => $request->nama_menu,
            'kategori'  => $request->kategori,
            'created_at' => now(),
        ]);
        if ($request->filled('departemen_ids')) {
            $menu->departemens()->sync($request->departemen_ids);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan.'
            ]);
        }

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function datatableSubMenu(Request $request)
    {
        $query = TicketSubMenu::query()
            ->join('ticket_menu', 'ticket_menu.id', '=', 'ticket_sub_menu.menu_id')
            ->select(
                'ticket_sub_menu.id',
                'ticket_sub_menu.nama_sub_menu',
                'ticket_menu.nama_menu',
                'ticket_menu.kategori'
            );
        if ($request->filled('kategori')) {
            $query->where('ticket_menu.kategori', $request->kategori);
        }
        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-xs btn-warning btn_edit_submenu me-1"
                            data-id="'    . $row->id           . '"
                            data-nama="'  . $row->nama_sub_menu . '"
                            data-menu="'  . $row->nama_menu     . '">
                            <i class="bx bx-edit"></i> Ubah
                        </button>';
                $btnDelete = '<button class="btn btn-xs btn-danger btn_delete_submenu"
                            data-id="' . $row->id . '">
                            <i class="bx bx-trash"></i> Hapus
                        </button>';
                return $btnEdit . $btnDelete;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_menu_edit'   => 'required|exists:ticket_menu,id',
            'nama_menu_edit' => 'required|string|max:255',
            'kategori_edit'  => 'required|in:1,2,3', //1. epicor, 2. accurate sp, 3. accurate aja
            'departemen_ids'  => 'nullable|array',
            'departemen_ids.*' => 'exists:departemens,id',
        ]);
        $menu = TicketMenu::findOrFail($request->id_menu_edit);
        $menu->update(
            [
                'nama_menu'  => $request->nama_menu_edit,
                'kategori'   => $request->kategori_edit,
                'updated_at' => now(),
            ]
        );
        $menu->departemens()->sync($request->departemen_ids ?? []);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diupdate.'
            ]);
        }

        return redirect()->back()->with('success', 'Menu berhasil diupdate.');
    }

    public function destroy($id)
    {
        $menu = TicketMenu::where('id', $id)->firstOrFail();
        $menu->departemens()->detach();
        $menu->delete();
        return response()->json(['success' => true, 'message' => 'Menu berhasil dihapus.']);
    }

    //Master Sub Menu View
    public function subMenu($holding, Request $request)
    {
        // dd($id);
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding  = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();
        $roleId = count($get_role) > 0 ? $get_role : null;
        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();
        $ticketMenus = TicketMenu::all();
        return view('admin.ticket.sub_menu', [
            'title'   => 'Sub Menu',
            'holding' => $holding,
            'menus'   => $menus
        ]);
    }

    public function menu_kategori(Request $request)
    {
        $kategori = $request->kategori;
        $menus = TicketMenu::where('kategori', (string) $kategori)->get(['id', 'nama_menu']);
        return response()->json($menus);
    }

    public function storeSubMenu(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'parent_menu_id' => 'required|exists:ticket_menu,id',
            'nama_submenu'   => 'required|string|max:255',
        ]);
        TicketSubMenu::create([
            'id'           => Uuid::uuid4(),
            'menu_id'      => $request->parent_menu_id,
            'nama_sub_menu' => $request->nama_submenu,
            'created_at'   => now(),

        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sub Menu berhasil ditambahkan.'
            ]);
        }

        return redirect()->back()->with('success', 'Sub Menu berhasil ditambahkan.');
    }

    public function updateSubMenu(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_submenu_edit'   => 'required|exists:ticket_sub_menu,id',
            'nama_submenu_edit' => 'required|string|max:255',
        ]);
        // dd($request->all());
        $ticketSubMenu = TicketSubMenu::findOrFail($request->id_submenu_edit);
        $ticketSubMenu->update([
            'nama_sub_menu' => $request->nama_submenu_edit,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sub Menu berhasil diupdate.'
            ]);
        }

        return redirect()->back()->with('success', 'Sub Menu berhasil diupdate.');
    }

    public function destroySubMenu($id)
    {
        TicketSubMenu::where('id', $id)->firstOrFail()->delete();
        return response()->json(['success' => true, 'message' => 'Sub Menu berhasil dihapus.']);
    }

    public function hubMenuDatatable(Request $request)
    {
        $holdingCode = $request->query('holding');
        $holdingId = null;
        if ($holdingCode) {
            $holding = Holding::where('holding_code', $holdingCode)->first();
            if ($holding) {
                $holdingId = $holding->id;
            }
        }
        $query = TicketHubMenu::withCount('users')->orderBy('sort_order');
        if ($holdingId) {
            $query->with(['approvals' => function ($q) use ($holdingId) {
                $q->where('holding_id', $holdingId)->with('approvalGroup');
            }]);
        }
        $data = $query->get();
        return DataTables::of($data)
            ->addColumn(
                'status',
                fn($r) => $r->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-secondary">Nonaktif</span>'
            )
            ->addColumn(
                'icon_preview',
                fn($r) =>
                '<i class="' . e($r->icon) . '" style="font-size:18px"></i>'
                    . ' <code style="font-size:10px">' . e($r->icon) . '</code>'
            )
            ->addColumn(
                'hak_akses',
                fn($r) => $r->users_count > 0
                    ? '<span class="badge bg-primary">' . $r->users_count . ' user</span>'
                    : '<span class="badge bg-secondary">Semua user</span>'
            )
            ->addColumn('approval_group', function ($r) use ($holdingId) {
                if ($holdingId && $r->approvals->isNotEmpty()) {
                    $approval = $r->approvals->first();
                    if ($approval && $approval->approvalGroup) {
                        return '<span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:11px">'
                            . '<i class="mdi mdi-shield-check me-1"></i>'
                            . e($approval->approvalGroup->nama_group)
                            . '</span>';
                    }
                }
                return '<span class="badge bg-secondary-subtle text-secondary" style="font-size:11px">Belum diatur</span>';
            })
            ->addColumn('action', function ($r) {
                return '
                <button class="btn btn-xs btn-outline-warning btn_edit_hub_menu me-1"
                    data-id="'      . $r->id              . '"
                    data-nama="'    . e($r->nama_menu)    . '"
                    data-icon="'    . e($r->icon)         . '"
                    data-desc="'    . e($r->description)  . '"
                    data-color="'   . e($r->color_class)  . '"
                    data-route="'   . e($r->route_name)   . '"
                    data-params=\'' . json_encode($r->route_params) . '\'
                    data-sort="'    . $r->sort_order      . '"
                    data-active="'  . ($r->is_active ? 1 : 0) . '">
                    <i class="mdi mdi-pencil"></i> Edit
                </button>
                <button class="btn btn-xs btn-outline-info btn_manage_user_hub me-1"
                    data-id="'    . $r->id          . '"
                    data-nama="'  . e($r->nama_menu) . '"
                    title="Kelola Hak Akses User">
                    <i class="mdi mdi-account-multiple"></i> Hak Akses
                </button>
                <button class="btn btn-xs btn-outline-success btn_approval_menu me-1"
                    data-id="'   . $r->id          . '"
                    data-nama="' . e($r->nama_menu) . '"
                    title="Pilih Approval Group">
                    <i class="mdi mdi-shield-check"></i> Approval
                </button>
                <button class="btn btn-xs btn-outline-danger btn_delete_hub_menu"
                    data-id="' . $r->id . '">
                    <i class="mdi mdi-trash-can"></i> Hapus
                </button>';
            })
            ->rawColumns(['status', 'icon_preview', 'hak_akses', 'approval_group', 'action'])
            ->make(true);
    }

    public function hubMenuStore(Request $request)
    {
        $request->validate([
            'nama_menu'    => 'required|string|max:255',
            'icon'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:500',
            'color_class'  => 'required|string|max:50',
            'route_name'   => 'required|string|max:255',
            'route_params' => 'nullable|json',
            'sort_order'   => 'nullable|integer',
            'is_active'    => 'nullable|boolean',
        ]);
        TicketHubMenu::create([
            'id'           => (string) Uuid::uuid4(),
            'nama_menu'    => $request->nama_menu,
            'icon'         => $request->icon,
            'description'  => $request->description,
            'color_class'  => $request->color_class,
            'route_name'   => $request->route_name,
            'route_params' => $request->route_params
                ? json_decode($request->route_params, true) : null,
            'sort_order'   => $request->sort_order ?? 0,
            'is_active'    => $request->boolean('is_active', true),
        ]);
        return response()->json(['code' => 200, 'message' => 'Menu hub berhasil ditambahkan.']);
    }

    public function hubMenuUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_menu'    => 'required|string|max:255',
            'icon'         => 'required|string|max:100',
            'description'  => 'nullable|string|max:500',
            'color_class'  => 'required|string|max:50',
            'route_name'   => 'required|string|max:255',
            'route_params' => 'nullable|json',
            'sort_order'   => 'nullable|integer',
            'is_active'    => 'nullable|boolean',
        ]);
        $menu = TicketHubMenu::findOrFail($id);
        $menu->update([
            'nama_menu'    => $request->nama_menu,
            'icon'         => $request->icon,
            'description'  => $request->description,
            'color_class'  => $request->color_class,
            'route_name'   => $request->route_name,
            'route_params' => $request->route_params
                ? json_decode($request->route_params, true) : null,
            'sort_order'   => $request->sort_order ?? 0,
            'is_active'    => $request->boolean('is_active'),
        ]);
        return response()->json(['code' => 200, 'message' => 'Menu hub berhasil diperbarui.']);
    }

    public function hubMenuDestroy($id)
    {
        TicketHubMenu::findOrFail($id)->delete();
        return response()->json(['code' => 200, 'message' => 'Menu hub berhasil dihapus.']);
    }

    public function hubMenuGetUsers($id)
    {
        $menu = TicketHubMenu::with([
            'users:id,username,karyawan_id',
            'users.KaryawanNIK:nomor_identitas_karyawan,name',
        ])->findOrFail($id);
        $assigned = $menu->users->map(fn($u) => [
            'id'       => $u->id,
            'username' => $u->username,
            'name'     => $u->KaryawanNIK->name ?? $u->username,
        ]);
        return response()->json(['code' => 200, 'data' => $assigned]);
    }

    public function hubMenuUserDatatable(Request $request, $holding, $id)
    {
        $hubMenu = TicketHubMenu::findOrFail($id);
        $assignedIds = $hubMenu->users()
            ->pluck('users.id')
            ->toArray();
        $data = User::with([
            'KaryawanNIK',
            'KaryawanNIK.KaryawanJabatanOne.Departemen',
            'KaryawanNIK.KaryawanJabatanOne.Jabatan',
            'KaryawanNIK.KaryawanJabatanOne.Holding',
        ])
            ->whereHas('KaryawanNIK', function ($q) {
                $q->where('status_aktif', 'AKTIF');
            })
            ->select('id', 'username', 'karyawan_id');
        return DataTables::eloquent($data)
            ->addColumn('nama_karyawan', function ($row) {
                return $row->KaryawanNIK->name ?? $row->username;
            })
            ->filterColumn('nama_karyawan', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('username', 'ilike', "%{$keyword}%")
                        ->orWhereHas('KaryawanNIK', function ($q2) use ($keyword) {
                            $q2->where('name', 'ilike', "%{$keyword}%");
                        });
                });
            })
            ->addColumn('nama_departemen', function ($row) {
                $jabatan = $row->KaryawanNIK?->KaryawanJabatanOne;
                if (!$jabatan) {
                    return '-';
                }
                $departemen  = $jabatan->Departemen->nama_departemen ?? '-';
                $holdingName = $jabatan->Holding->holding_name ?? '-';
                return '
                    <div>
                        ' . e($departemen) . '
                        <div class="text-muted" style="font-size:11px;">
                            ' . e($holdingName) . '
                        </div>
                    </div>
                ';
            })
            ->filterColumn('nama_departemen', function ($query, $keyword) {
                $query->whereHas('KaryawanNIK.KaryawanJabatanOne.Departemen', function ($q) use ($keyword) {
                    $q->where('nama_departemen', 'ilike', "%{$keyword}%");
                });
            })
            ->addColumn('nama_jabatan', function ($row) {
                $jabatanRel = $row->KaryawanNIK?->KaryawanJabatanOne;
                if (!$jabatanRel || !$jabatanRel->Jabatan) {
                    return '-';
                }
                return e($jabatanRel->Jabatan->nama_jabatan);
            })
            ->filterColumn('nama_jabatan', function ($query, $keyword) {
                $query->whereHas('KaryawanNIK.KaryawanJabatanOne.Jabatan', function ($q) use ($keyword) {
                    $q->where('nama_jabatan', 'ilike', "%{$keyword}%");
                });
            })
            ->addColumn('option', function ($row) use ($assignedIds) {
                $checked = in_array($row->id, $assignedIds) ? 'checked' : '';
                return '
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="user_ids[]"
                            class="form-check-input hub-user-chk"
                            value="' . $row->id . '"
                            ' . $checked . '>
                    </div>
                ';
            })
            ->with([
                'assigned_count' => count($assignedIds),
                'assigned_ids'   => $assignedIds,
            ])
            ->rawColumns([
                'nama_departemen',
                'nama_jabatan',
                'option'
            ])
            ->make(true);
    }

    public function hubMenuSaveUsers(Request $request, $id)
    {
        $request->validate([
            'user_ids'   => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        // dd($request->all());
        $menu = TicketHubMenu::findOrFail($id);
        $menu->users()->sync($request->user_ids ?? []);

        $count = count($request->user_ids ?? []);
        $msg   = $count > 0
            ? "Hak akses disimpan untuk {$count} user."
            : 'Semua user dapat mengakses menu ini.';
        return response()->json(['code' => 200, 'message' => $msg]);
    }

    public function getApprovalMenu(Request $request)
    {
        $hubMenuId = $request->input('id');
        $holdingCode = $request->input('holding');
        $holding = Holding::where('holding_code', $holdingCode)->first();
        if (!$holding) {
            return DataTables::of([])->make(true);
        }
        $holdingId = $holding->id;
        $hubMenuApproval = TicketHubMenuApproval::where('hub_menu_id', $hubMenuId)
            ->where('holding_id', $holdingId)
            ->first();
        $selectedGroupId = $hubMenuApproval?->approval_group_id;
        $groups = ApprovalGroup::where('holding_id', $holdingId)
            ->where(function ($q) {
                $q->whereJsonContains('menu_id', '109');
            })
            ->get(['id_group', 'nama_group', 'menu_id']);

        return DataTables::of($groups)
            ->addColumn('option', function ($row) use ($selectedGroupId) {
                $checked = ($selectedGroupId === $row->id_group) ? 'checked' : '';
                return '
                    <div class="form-check">
                        <input
                            type="radio"
                            name="approval_group_id"
                            class="form-check-input hub-approval-radio"
                            value="' . $row->id_group . '"
                            ' . $checked . '>
                    </div>
                ';
            })
            ->rawColumns(['option'])
            ->make(true);
    }

    public function saveApprovalGroup(Request $request, $id)
    {
        $request->validate([
            'approval_group_id' => 'nullable|string|exists:master_approval_group,id_group',
            'holding' => 'required|string',
        ]);
        $holdingCode = $request->input('holding');
        $holding = Holding::where('holding_code', $holdingCode)->firstOrFail();
        $holdingId = $holding->id;
        if ($request->approval_group_id) {
            TicketHubMenuApproval::updateOrCreate(
                [
                    'hub_menu_id' => $id,
                    'holding_id' => $holdingId,
                ],
                [
                    'approval_group_id' => $request->approval_group_id,
                ]
            );
            $msg = 'Approval group berhasil disimpan.';
        } else {
            TicketHubMenuApproval::where('hub_menu_id', $id)
                ->where('holding_id', $holdingId)
                ->delete();
            $msg = 'Approval group berhasil dihapus.';
        }
        return response()->json(['code' => 200, 'message' => $msg]);
    }

    public function storeKondisiMenu(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:ticket_kondisi,code',
            'label_kondisi' => 'required|string|max:255',
            'tipe_data' => 'required|in:string,number,boolean',
            'status' => 'required|in:0,1',
        ]);
        // dd($request->all());
        TicketKondisiMenu::create([
            'id' => Uuid::uuid4(),
            'code' => $request->code,
            'label_kondisi' => $request->label_kondisi,
            'tipe_data' => $request->tipe_data,
            'status' => $request->status,
            'created_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Kondisi Menu berhasil ditambahkan.');
    }

    public function updateKondisiMenu(Request $request)
    {
        $request->validate([
            'id_kondisi_edit'    => 'required|exists:ticket_kondisi,id',
            'kode_edit'          => 'required|string|max:255|unique:ticket_kondisi,code,' . $request->id_kondisi_edit,
            'label_kondisi_edit' => 'required|string|max:255',
            'tipe_data_edit'     => 'required|in:string,number,boolean',
            'status_edit'        => 'required|in:0,1',
        ]);
        // dd($request->all());
        TicketKondisiMenu::where('id', $request->id_kondisi_edit)->update([
            'code'          => $request->kode_edit,
            'label_kondisi' => $request->label_kondisi_edit,
            'tipe_data'     => $request->tipe_data_edit,
            'status'        => $request->status_edit,
            'updated_at'    => now(),
        ]);
        return redirect()->back()->with('success', 'Kondisi Menu berhasil diupdate.');
    }

    public function destroyKondisiMenu($id)
    {
        $kondisiMenu = TicketKondisiMenu::where('id', $id)->firstOrFail();
        $kondisiMenu->delete();
        return response()->json(['success' => true, 'message' => 'Kondisi Menu berhasil dihapus.']);
    }

    public function datatableKondisi($kategori, $holding)
    {
        $kondisiMenu = TicketKondisiMenu::with(['children' => function ($q) {
            $q->orderBy('name', 'asc');
        }])
            ->where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();
        dd($kondisiMenu);
        $result = [];
        foreach ($kondisiMenu as $parent) {
            $result[] = [
                'id' => $parent->id,
                'name' => $parent->name,
                'kategori' => $parent->kategori,
                'parent' => true,
                'parent_id' => null,
                'level' => 0
            ];
            foreach ($parent->children as $child) {
                $result[] = [
                    'id' => $child->id,
                    'name' => $child->name,
                    'kategori' => $child->kategori,
                    'parent' => false,
                    'parent_id' => $parent->id,
                    'level' => 1
                ];
            }
        }
        // dd($result);
        return datatables()->of($result)
            ->addColumn('nama_menu', function ($row) {
                return $row['level'] == 0
                    ? '<strong>' . $row['name'] . '</strong>'
                    : '&nbsp;&nbsp;&nbsp;↳ ' . $row['name'];
            })
            ->addColumn('kategori', function ($row) {
                return $row['kategori'] ?? '-';
            })
            ->addColumn('option', function ($row) {
                $checked = '';
                if ($row['level'] == 0) {
                    return '
                        <div class="form-check">
                            <input class="form-check-input parent-checkbox"
                                type="checkbox"
                                data-id="' . $row['id'] . '"
                                name="menu_id[]"
                                value="' . $row['id'] . '"
                                ' . $checked . '>
                            <label class="form-check-label">Buka Access</label>
                        </div>';
                } else {
                    return '
                        <div class="form-check">
                            <input class="form-check-input child-checkbox"
                                type="checkbox"
                                data-parent="' . $row['parent_id'] . '"
                                name="menu_id[]"
                                value="' . $row['id'] . '"
                                ' . $checked . '>
                            <label class="form-check-label">Buka Access</label>
                        </div>
                    ';
                }
            })
            ->rawColumns(['nama_menu', 'option'])
            ->make(true);
    }

    public function kondisiList()
    {
        $data = TicketKondisiMenu::where('status', 1)
            ->select('id', 'code', 'label_kondisi')
            ->orderBy('label_kondisi')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function departemenList()
    {
        $data = Departemen::with('Holding')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'nama_departemen' => $d->nama_departemen,
                    'holding_name' => $d->Holding->holding_name ?? 'Tanpa Holding',
                ];
            })
            ->sortBy(function ($item) {
                return strtolower($item['holding_name'] . ' ' . $item['nama_departemen']);
            })
            ->values();
        return response()->json(['data' => $data]);
    }

    // approval
    public function canvasData(Request $request)
    {
        $subMenus = TicketSubMenu::with('ticketMenu')->get();
        $flowLinks = DB::table('ticket_approval_flow_menu')
            ->join('ticket_approval_flow', 'ticket_approval_flow.id', '=', 'ticket_approval_flow_menu.approval_flow_id')
            ->select(
                'ticket_approval_flow_menu.sub_menu_id',
                'ticket_approval_flow.id as flow_id',
                'ticket_approval_flow.nama_flow'
            )
            ->get()
            ->groupBy('sub_menu_id');
        $stepCounts = DB::table('ticket_approval_step')
            ->select('approval_flow_id', DB::raw('count(*) as cnt'))
            ->groupBy('approval_flow_id')
            ->pluck('cnt', 'approval_flow_id');
        $nodes = $subMenus->map(function ($sm) use ($flowLinks, $stepCounts) {
            $link     = optional($flowLinks->get($sm->id))->first(); // first flow attached to this sub-menu
            $hasSteps = $link ? (($stepCounts[$link->flow_id] ?? 0) > 0) : false;
            return [
                'id'            => $sm->id,
                'sub_menu_id'   => $sm->id,
                'name'          => $sm->nama_sub_menu,
                'menu_name'     => optional($sm->ticketMenu)->nama_menu ?? '-',
                'kategori'      => (int) (optional($sm->ticketMenu)->kategori ?? 1),
                'kategori_name' => optional($sm->ticketMenu)->kategori_display ?? 'Epicor ERP',
                'x'             => (int) ($sm->canvas_x ?? 50),
                'y'             => (int) ($sm->canvas_y ?? 50),
                'is_on_canvas'  => (bool) $sm->is_on_canvas,
                'has_org_tree'  => $hasSteps, // ☑ / ☐ indicator on the node
                'flow_id'       => $link->flow_id ?? null,
                'flow_name'     => $link->nama_flow ?? null,
            ];
        })->values();
        $kondisiLabels = TicketKondisiMenu::pluck('label_kondisi', 'code');
        $dependencies = TicketDependencyRule::query()->get();
        $edges = $dependencies->map(function ($dep) use ($kondisiLabels) {
            return [
                'id'                   => $dep->id,
                'from'                 => $dep->source_sub_menu_id,
                'to'                   => $dep->target_sub_menu_id,
                'condition_key'        => $dep->condition_key,
                'condition_label'      => $kondisiLabels[$dep->condition_key] ?? $dep->condition_key,
                'description'          => $dep->description ?? null,
                'bidirectional'        => (bool) ($dep->is_bidirectional ?? false),
                'is_conditional'       => (bool) ($dep->is_conditional ?? false),
                'is_cross_environment' => (bool) ($dep->is_cross_environment ?? false),
                'is_active'            => (bool) $dep->is_active,
            ];
        })->values();
        return response()->json(['code' => 200, 'nodes' => $nodes, 'edges' => $edges]);
    }

    public function canvasSavePosition(Request $request)
    {
        $request->validate([
            'nodes'      => 'required|array',
            'nodes.*.id' => 'required|exists:ticket_sub_menu,id',
            'nodes.*.x'  => 'required|numeric',
            'nodes.*.y'  => 'required|numeric',
        ]);
        DB::transaction(function () use ($request) {
            foreach ($request->nodes as $node) {
                $sm = TicketSubMenu::find($node['id']);
                if (!$sm) continue;
                $sm->canvas_x     = (int) $node['x'];
                $sm->canvas_y     = (int) $node['y'];
                $sm->is_on_canvas = true;
                $sm->save();
            }
        });
        return response()->json(['code' => 200, 'message' => 'Posisi node tersimpan.']);
    }

    public function canvasEdgeToggle($id)
    {
        $rule = TicketDependencyRule::findOrFail($id);
        $rule->is_bidirectional = !($rule->is_bidirectional ?? false);
        $rule->save();
        return response()->json([
            'code'          => 200,
            'message'       => 'Arah panah diperbarui.',
            'bidirectional' => (bool) $rule->is_bidirectional,
        ]);
    }

    public function canvasOrgTree($subMenuId)
    {
        $subMenu = TicketSubMenu::with('ticketMenu')->findOrFail($subMenuId);
        $flow = TicketApprovalFlow::whereHas('subMenus', function ($q) use ($subMenuId) {
            $q->where('ticket_sub_menu.id', $subMenuId);
        })->with(['steps.department', 'steps.divisi', 'steps.jabatanLevel', 'steps.karyawans'])->first();
        if (!$flow) {
            return response()->json([
                'code'     => 200,
                'has_flow' => false,
                'sub_menu' => ['id' => $subMenu->id, 'name' => $subMenu->nama_sub_menu],
                'steps'    => [],
            ]);
        }
        $steps = $flow->steps->map(function ($step) use ($flow) {
            return [
                'id'               => $step->id,
                'flow_id'          => $step->approval_flow_id,
                'flow_name'        => $flow->nama_flow,
                'parent_step_id'   => $step->parent_step_id,
                'step_name'        => $step->step_name,
                'step_order'       => $step->step_order,
                'department_id'    => $step->department_id,
                'divisi_id'        => $step->divisi_id,
                'bagian_id'        => $step->bagian_id,
                'holding_id'       => $step->department?->holding ?? ($step->holding_id ?? null),
                'jabatan_level_id' => $step->jabatan_level_id,
                'nama_departemen'  => optional($step->department)->nama_departemen,
                'nama_divisi'      => optional($step->divisi)->nama_divisi,
                'jabatan_name'     => optional($step->jabatanLevel)->nama_jabatan,
                'role_type'        => $step->role_type ?? 'approver',
                'type'             => $step->approval_type ?? 'sequential',
                'is_final'         => (bool) $step->is_final_step,
                'is_checker'       => (bool) $step->is_checker,
                'sla_hours'        => $step->sla_hours ?? null,
                'user_count'       => $step->karyawans->count(),
                'condition'        => $step->condition ?? null,
            ];
        })->values();
        return response()->json([
            'code'      => 200,
            'has_flow'  => true,
            'flow_id'   => $flow->id,
            'flow_name' => $flow->nama_flow,
            'sub_menu'  => ['id' => $subMenu->id, 'name' => $subMenu->nama_sub_menu],
            'steps'     => $steps,
        ]);
    }

    public function canvasQuickStoreSubMenu(Request $request)
    {
        $request->validate([
            'parent_menu_id' => 'required|exists:ticket_menu,id',
            'nama_submenu'   => 'required|string|max:255',
            'x'              => 'nullable|numeric',
            'y'              => 'nullable|numeric',
        ]);
        $subMenu = new TicketSubMenu();
        $subMenu->id            = (string) Uuid::uuid4();
        $subMenu->menu_id       = $request->parent_menu_id;
        $subMenu->nama_sub_menu = $request->nama_submenu;
        $subMenu->canvas_x      = (int) ($request->x ?? 50);
        $subMenu->canvas_y      = (int) ($request->y ?? 50);
        $subMenu->is_on_canvas  = true;
        $subMenu->created_at    = now();
        $subMenu->save();
        return response()->json([
            'code'    => 200,
            'message' => 'Sub-menu berhasil didaftarkan ke master & kanvas.',
            'data'    => [
                'id'           => $subMenu->id,
                'sub_menu_id'  => $subMenu->id,
                'name'         => $subMenu->nama_sub_menu,
                'menu_name'    => optional($subMenu->ticketMenu)->nama_menu ?? '-',
                'x'            => $subMenu->canvas_x,
                'y'            => $subMenu->canvas_y,
                'has_org_tree' => false,
                'flow_id'      => null,
            ],
        ]);
    }

    public function canvasUnplaceNode($subMenuId)
    {
        $sm = TicketSubMenu::findOrFail($subMenuId);
        $sm->is_on_canvas = false;
        $sm->save();
        return response()->json(['code' => 200, 'message' => 'Node dikembalikan ke sidebar.']);
    }

    public function canvasFlowList()
    {
        $flows = TicketApprovalFlow::orderBy('nama_flow')->get(['id', 'nama_flow']);
        return response()->json(['code' => 200, 'data' => $flows]);
    }
    public function canvasSetSubMenuFlow(Request $request)
    {
        $request->validate([
            'sub_menu_id' => 'required|exists:ticket_sub_menu,id',
            'mode'        => 'required|in:existing,new,reset',
            'flow_id'     => 'required_if:mode,existing|nullable|exists:ticket_approval_flow,id',
            'nama_flow'   => 'required_if:mode,new|nullable|string|max:255',
            'deskripsi'   => 'nullable|string',
        ]);
        return DB::transaction(function () use ($request) {
            DB::table('ticket_approval_flow_menu')
                ->where('sub_menu_id', $request->sub_menu_id)
                ->delete();
            if ($request->mode === 'reset') {
                return response()->json([
                    'code'    => 200,
                    'message' => 'Approval group direset ke default.',
                    'flow_id' => null,
                ]);
            }
            if ($request->mode === 'existing') {
                $flow = TicketApprovalFlow::findOrFail($request->flow_id);
                $flow->subMenus()->attach($request->sub_menu_id);
                return response()->json([
                    'code'      => 200,
                    'message'   => 'Sub-menu berhasil dihubungkan ke approval group yang sudah ada.',
                    'flow_id'   => $flow->id,
                    'flow_name' => $flow->nama_flow,
                ]);
            }
            $flow = new TicketApprovalFlow();
            $flow->id          = (string) Uuid::uuid4();
            $flow->nama_flow   = $request->nama_flow;
            $flow->description = $request->deskripsi;
            $flow->is_active   = 1;
            $flow->save();
            $flow->subMenus()->attach($request->sub_menu_id);

            return response()->json([
                'code'      => 200,
                'message'   => 'Approval group baru berhasil dibuat.',
                'flow_id'   => $flow->id,
                'flow_name' => $flow->nama_flow,
            ]);
        });
    }

    public function approvalFlow($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding  = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();
        $roleId = count($get_role) > 0 ? $get_role : null;
        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();
        return view('admin.ticket.work-flow', [
            'title'   => 'Approval Flow Management',
            'holding' => $holding,
            'menus'   => $menus,
        ]);
    }

    public function datatableFlowApproval(Request $request)
    {
        $data = TicketApprovalFlow::with([
            'subMenus',
            'steps.department',
        ])->get();
        $result = $data->map(function ($flow) {
            return [
                'id'          => $flow->id,
                'nama_flow'   => $flow->nama_flow,
                'description' => $flow->description,
                'is_active'   => (bool) $flow->is_active,
                'menus'       => $flow->subMenus->pluck('nama_sub_menu')->values()->toArray(),
                'steps' => $flow->steps->map(function ($step) use ($flow) {
                    // Hitung manual pakai query langsung — hindari cast issue
                    $userCount = DB::table('ticket_approval_karyawan')
                        ->where('approval_step_id', $step->id)
                        ->count();
                    return [
                        'id'               => $step->id,
                        'flow_id'          => $step->approval_flow_id,
                        'flow_name'        => $flow->nama_flow,
                        'parent_step_id'   => $step->parent_step_id,
                        'step_name'        => $step->step_name,
                        'step_order'       => $step->step_order,
                        'department_id'    => $step->department_id,
                        'divisi_id'        => $step->divisi_id,
                        'bagian_id'        => $step->bagian_id,
                        'holding_id'       => $step->department?->holding ?? ($step->holding_id ?? null),
                        'jabatan_level_id' => $step->jabatan_level_id,
                        'nama_departemen'  => optional($step->department)->nama_departemen,
                        'nama_divisi'      => optional($step->divisi)->nama_divisi,
                        'jabatan_name'     => optional($step->jabatanLevel)->nama_jabatan,
                        'role_type'        => $step->role_type ?? 'approver',
                        'type'             => $step->approval_type ?? 'sequential',
                        'is_final'         => (bool) $step->is_final_step,
                        'is_checker'       => (bool) $step->is_checker,
                        'sla_hours'        => $step->sla_hours,
                        'user_count'       => $userCount,
                        'condition'        => $step->condition,
                    ];
                })->values()->toArray(),
            ];
        });
        return response()->json(['data' => $result]);
    }

    public function storeFlowApproval(Request $request)
    {
        $request->validate([
            'nama_flow' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'sub_menu_ids' => 'required|array|min:1',
            'sub_menu_ids.*' => 'exists:ticket_sub_menu,id',
        ]);
        // dd($request->all());
        $flow = TicketApprovalFlow::create([
            'id' => Uuid::uuid4()->toString(),
            'nama_flow' => $request->nama_flow,
            'description' => $request->deskripsi,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if (!empty($request->sub_menu_ids)) {
            $flow->subMenus()->attach($request->sub_menu_ids);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Flow approval berhasil dibuat',
            'data' => $flow
        ]);
    }

    public function updateApprovalFlow($id, Request $request)
    {
        $request->validate([
            'nama_flow'       => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'sub_menu_ids'    => 'required|array|min:1',
            'sub_menu_ids.*'  => 'exists:ticket_sub_menu,id',
            'is_active'       => 'nullable|boolean',
        ]);
        $flow = TicketApprovalFlow::findOrFail($id);
        // dd($flow, $request->all());
        $flow->update([
            'nama_flow'  => $request->nama_flow,
            'description' => $request->deskripsi,
            'is_active'  => $request->has('is_active')
                ? (int) $request->is_active
                : $flow->is_active,
            'updated_at' => now(),
        ]);

        $flow->subMenus()->sync($request->sub_menu_ids);
        return response()->json([
            'code'    => 200,
            'message' => 'Flow approval berhasil diperbarui',
            'data'    => $flow->load('subMenus'),
        ]);
    }

    public function deleteApprovalGroup($id)
    {
        try {
            $flow = TicketApprovalFlow::findOrFail($id);
            $flow->delete();
            $flow->subMenus()->detach();

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => 'Flow approval berhasil dihapus.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'code'    => 404,
                'success' => false,
                'message' => 'Flow approval tidak ditemukan.'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approvalFlowList(Request $request)
    {
        $flows = TicketApprovalFlow::with(['subMenus', 'steps.karyawans'])
            ->get()
            ->map(function ($flow) {
                return [
                    'id'     => $flow->id,
                    'name'   => $flow->nama_flow,
                    'desc'   => $flow->description,
                    'active' => $flow->is_active == 1,
                    'menus'  => $flow->subMenus->pluck('nama_menu')->toArray(),
                    'steps'  => $flow->steps->map(function ($step) use ($flow) {
                        return [
                            'id'               => $step->id,
                            'flow_id'          => $step->approval_flow_id,
                            'flow_name'        => $flow->nama_flow,  // ← tambah flow_name
                            'parent_step_id'   => $step->parent_step_id,
                            'step_name'        => $step->step_name,
                            'step_order'       => $step->step_order,
                            'department_id'    => $step->department_id,
                            'divisi_id'        => $step->divisi_id,
                            'bagian_id'        => $step->bagian_id,
                            'holding_id'       => $step->department?->holding ?? ($step->holding_id ?? null),
                            'jabatan_id'       => $step->jabatan_id,
                            'nama_departemen'  => optional($step->department)->nama_departemen,
                            'nama_divisi'      => optional($step->divisi)->nama_divisi,
                            'jabatan_name'     => optional($step->jabatanLevel)->nama_jabatan,
                            'role_type'        => $step->role_type ?? 'approver',
                            'type'             => $step->approval_type ?? 'sequential',
                            'is_final'         => (bool) $step->is_final_step,
                            'is_checker'       => (bool) $step->is_checker,
                            'sla_hours'        => $step->sla_hours,
                            'user_count'       => $step->karyawans->count(),  // ← dari eager load
                            'condition'        => $step->condition,
                        ];
                    })->values(),
                ];
            });
        return response()->json(['code' => 200, 'data' => $flows]);
    }

    public function approvalGetSubMenu()
    {
        $subMenus = TicketSubMenu::with('ticketMenu')
            ->get()
            ->map(function ($sub) {
                return [
                    'id'            => $sub->id,
                    'nama_sub_menu' => $sub->nama_sub_menu,
                    'nama_menu'     => $sub->ticketMenu->nama_menu ?? '-',
                    'text'          => $sub->nama_sub_menu . ' (' . (optional($sub->ticketMenu)->nama_menu ?? '-') . ')',
                ];
            });
        return response()->json($subMenus);
    }

    public function getJabatanLevel(Request $request)
    {
        $levels = JabatanMaster::with('LevelJabatan')
            ->orderBy('jabatan_name')
            ->get()
            ->map(fn($jm) => [
                'id'   => $jm->id,
                'text' => $jm->jabatan_name
                    . ($jm->LevelJabatan ? ' — ' . $jm->LevelJabatan->level_jabatan : ''),
            ]);
        return response()->json($levels);
    }

    public function save_user_approval(Request $request)
    {
        $request->validate([
            'step_id'          => 'required|exists:ticket_approval_step,id',
            'karyawan_ids'     => 'nullable|array',
            'karyawan_ids.*'   => 'exists:karyawans,id',
        ]);
        $stepId = $request->step_id;
        DB::transaction(function () use ($stepId, $request) {
            TicketApprovalKaryawan::where('approval_step_id', $stepId)->delete();
            if (empty($request->karyawan_ids)) {
                return;
            }
            foreach ($request->karyawan_ids as $karyawanId) {
                $karyawan = Karyawan::find($karyawanId);
                if ($karyawan) {
                    TicketApprovalKaryawan::create([
                        'id'               => (string) Uuid::uuid4(),
                        'approval_step_id' => $stepId,
                        'karyawan_id'      => $karyawanId,
                        'karyawan_name'    => $karyawan->name,
                    ]);
                }
            }
        });
        return response()->json([
            'code'    => 200,
            'message' => 'Karyawan berhasil disimpan untuk step ini.',
        ]);
    }

    public function storeApprovalStep(Request $request)
    {
        $request->validate([
            'flow_id'            => 'required|exists:ticket_approval_flow,id',
            'parent_step_id'     => 'nullable|exists:ticket_approval_step,id',
            'nama_step'          => 'required|string|max:255',
            'holding_id'         => 'nullable|uuid',
            'departemen_id'      => 'nullable|uuid',
            'jabatan_id'         => 'nullable|uuid',
            'jabatan_level_id'   => 'nullable|string',
            'is_checker'         => 'nullable|boolean',
        ]);

        $isChecker = $request->boolean('is_checker');

        $step = DB::transaction(function () use ($request, $isChecker) {
            if ($isChecker) {
                TicketApprovalStep::where('approval_flow_id', $request->flow_id)
                    ->update(['is_checker' => false]);
            }

            $order_number = TicketApprovalStep::where('approval_flow_id', $request->flow_id)->max('step_order') + 1;
            $step = TicketApprovalStep::create([
                'approval_flow_id' => $request->flow_id,
                'parent_step_id'   => $request->parent_step_id,
                'step_name'        => $request->nama_step,
                'step_order'       => $order_number,
                'holding_id'       => $request->holding_id,
                'department_id'    => $request->departemen_id,
                'divisi_id'        => null,
                'bagian_id'        => null,
                'jabatan_id'       => $request->jabatan_id,
                'jabatan_level_id' => $request->jabatan_id,
                'is_checker'       => $isChecker,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            if ($request->has('karyawan_ids') && is_array($request->karyawan_ids)) {
                $attachData = [];
                foreach ($request->karyawan_ids as $karyawanId) {
                    $karyawan = Karyawan::find($karyawanId);
                    if ($karyawan) {
                        $attachData[$karyawanId] = ['karyawan_name' => $karyawan->name];
                    }
                }
                $step->karyawans()->sync($attachData);
            }

            return $step;
        });

        return response()->json([
            'code'    => 200,
            'message' => 'Approval step berhasil disimpan.',
            'data'    => $step,
        ]);
    }

    public function updateApprovalStep(Request $request, $id)
    {
        $request->validate([
            'flow_id'        => 'required|exists:ticket_approval_flow,id',
            'nama_step'      => 'required|string|max:255',
            'holding_id'     => 'nullable',
            'departemen_id'  => 'required',
            'jabatan_id'     => 'required',
            'parent_step_id' => 'nullable',
            'is_checker'     => 'nullable|boolean',
        ]);
        $step = TicketApprovalStep::findOrFail($id);
        if ($step->approval_flow_id !== $request->flow_id) {
            return response()->json([
                'code'    => 422,
                'message' => 'Flow approval tidak sesuai.',
            ], 422);
        }

        $isChecker = $request->boolean('is_checker');

        $updateData = [
            'step_name'        => $request->nama_step,
            'holding_id'       => $request->holding_id,
            'department_id'    => $request->departemen_id,
            'divisi_id'        => null,
            'bagian_id'        => null,
            'jabatan_id'       => $request->jabatan_id,
            'jabatan_level_id' => $request->jabatan_id,
            'is_checker'       => $isChecker,
            'updated_at'       => now(),
        ];

        if ($request->has('parent_step_id')) {
            $parentId = $request->parent_step_id ?: null;
            if ($parentId) {
                if ($parentId === $step->id) {
                    return response()->json([
                        'code'    => 422,
                        'message' => 'Step tidak bisa menjadi atasan untuk dirinya sendiri.',
                    ], 422);
                }
                if ($this->isStepDescendant($step->id, $parentId)) {
                    return response()->json([
                        'code'    => 422,
                        'message' => 'Tidak dapat memindahkan step ke bawahannya sendiri (akan menyebabkan circular hierarchy).',
                    ], 422);
                }
                $parentStep = TicketApprovalStep::find($parentId);
                if (!$parentStep || $parentStep->approval_flow_id !== $step->approval_flow_id) {
                    return response()->json([
                        'code'    => 422,
                        'message' => 'Atasan yang dipilih tidak valid atau tidak berada dalam flow yang sama.',
                    ], 422);
                }
            }
            $updateData['parent_step_id'] = $parentId;
        }

        DB::transaction(function () use ($step, $updateData, $isChecker) {
            if ($isChecker) {
                TicketApprovalStep::where('approval_flow_id', $step->approval_flow_id)
                    ->where('id', '!=', $step->id)
                    ->update(['is_checker' => false]);
            }
            $step->update($updateData);
        });

        return response()->json([
            'code'    => 200,
            'message' => 'Approval step berhasil diperbarui.',
            'data'    => $step->fresh(),
        ]);
    }

    public function changeStepParent(Request $request, $id)
    {
        $request->validate([
            'flow_id'        => 'required|exists:ticket_approval_flow,id',
            'parent_step_id' => 'nullable',
        ]);

        $step = TicketApprovalStep::findOrFail($id);
        if ($step->approval_flow_id !== $request->flow_id) {
            return response()->json([
                'code'    => 422,
                'message' => 'Flow approval tidak sesuai.',
            ], 422);
        }

        $parentId = $request->parent_step_id ?: null;
        if ($parentId) {
            if ($parentId === $step->id) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Step tidak bisa menjadi atasan untuk dirinya sendiri.',
                ], 422);
            }
            if ($this->isStepDescendant($step->id, $parentId)) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Tidak dapat memindahkan step ke bawahannya sendiri (akan menyebabkan circular hierarchy).',
                ], 422);
            }
            $parentStep = TicketApprovalStep::find($parentId);
            if (!$parentStep || $parentStep->approval_flow_id !== $step->approval_flow_id) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Atasan yang dipilih tidak valid atau tidak berada dalam flow yang sama.',
                ], 422);
            }
        }

        $step->update([
            'parent_step_id' => $parentId,
            'updated_at'     => now(),
        ]);

        return response()->json([
            'code'    => 200,
            'message' => 'Atasan (parent step) berhasil diubah.',
            'data'    => $step->fresh(),
        ]);
    }

    private function isStepDescendant($ancestorId, $checkId)
    {
        $current = TicketApprovalStep::find($checkId);
        $visited = [];
        while ($current && $current->parent_step_id) {
            if ($current->parent_step_id === $ancestorId) {
                return true;
            }
            if (in_array($current->parent_step_id, $visited, true)) {
                break;
            }
            $visited[] = $current->parent_step_id;
            $current = TicketApprovalStep::find($current->parent_step_id);
        }
        return false;
    }

    public function deleteApprovalStep($id)
    {
        $step = TicketApprovalStep::findOrFail($id);
        $step->forceDelete();
        return response()->json([
            'code'    => 200,
            'message' => 'Approval step berhasil dihapus.',
        ]);
    }

    public function dependencyKondisiList(Request $request)
    {
        $kondisis = TicketKondisiMenu::where('status', 1)
            ->select('code', 'label_kondisi')
            ->get();
        return response()->json(['code' => 200, 'data' => $kondisis]);
    }

    public function dependencyDatatable(Request $request)
    {
        $data = TicketDependencyRule::query()
            ->leftJoin('ticket_kondisi as kondisi', 'kondisi.code', '=', 'ticket_dependency_rule.condition_key')
            ->join('ticket_sub_menu as source', 'source.id', '=', 'ticket_dependency_rule.source_sub_menu_id')
            ->leftJoin('ticket_menu as source_menu', 'source_menu.id', '=', 'source.menu_id')
            ->join('ticket_sub_menu as target', 'target.id', '=', 'ticket_dependency_rule.target_sub_menu_id')
            ->leftJoin('ticket_menu as target_menu', 'target_menu.id', '=', 'target.menu_id')
            ->select(
                'ticket_dependency_rule.id',
                'ticket_dependency_rule.source_sub_menu_id',
                'ticket_dependency_rule.target_sub_menu_id',
                'source.nama_sub_menu as source_sub_menu',
                'source_menu.nama_menu as source_menu',
                'target.nama_sub_menu as target_sub_menu',
                'target_menu.nama_menu as target_menu',
                'ticket_dependency_rule.condition_key',
                'ticket_dependency_rule.is_conditional',
                'ticket_dependency_rule.is_bidirectional',
                'ticket_dependency_rule.is_cross_environment',
                'ticket_dependency_rule.description',
                'kondisi.label_kondisi',
                'ticket_dependency_rule.is_active'
            );
        return DataTables::of($data)
            ->editColumn('source_sub_menu', function ($row) {
                return e($row->source_sub_menu) . '<br>'
                    . '<small class="text-muted">' . e($row->source_menu) . '</small>';
            })
            ->editColumn('target_sub_menu', function ($row) {
                return e($row->target_sub_menu) . '<br>'
                    . '<small class="text-muted">' . e($row->target_menu) . '</small>';
            })
            ->addColumn('kondisi', function ($row) {
                $badge = '';
                if ($row->is_cross_environment) {
                    $badge .= '<span class="badge bg-primary text-white me-1" style="font-size:11px;"><i class="bx bx-transfer-alt me-1"></i>Cross-Env</span> ';
                }
                if (!$row->is_conditional && !$row->condition_key) {
                    $badge .= '<span class="badge bg-success" style="font-size:11px;"><i class="bx bx-check-circle me-1"></i>Mandatori</span>';
                } else {
                    $badge .= '<span class="badge bg-warning text-dark" style="font-size:11px;">'
                        . '<i class="bx bx-git-branch me-1"></i>Kondisional: ' . e($row->condition_key ?? 'Custom')
                        . ($row->label_kondisi ? (' (' . e($row->label_kondisi) . ')') : '')
                        . '</span>';
                }
                return $badge;
            })
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-xs btn-warning btn_edit_dependency me-1"
                            data-id="' . $row->id . '"
                            data-source-id="' . $row->source_sub_menu_id . '"
                            data-target-id="' . $row->target_sub_menu_id . '"
                            data-condition="' . e($row->condition_key ?? '') . '"
                            data-is-conditional="' . ($row->is_conditional ? 1 : 0) . '"
                            data-is-cross-env="' . ($row->is_cross_environment ? 1 : 0) . '"
                            data-bidirectional="' . ($row->is_bidirectional ? 1 : 0) . '"
                            data-description="' . e($row->description ?? '') . '"
                            data-status="' . $row->is_active . '">
                            <i class="bx bx-edit"></i> Ubah
                        </button>';
                $btnDelete = '<button class="btn btn-xs btn-danger btn_delete_dependency me-1"
                            data-id="' . $row->id . '">
                            <i class="bx bx-trash"></i> Hapus
                        </button>';
                return $btnEdit . $btnDelete;
            })
            ->rawColumns(['source_sub_menu', 'target_sub_menu', 'kondisi', 'action'])
            ->make(true);
    }

    public function dependencyStore(Request $request)
    {
        $request->validate([
            'source_sub_menu_id' => 'required|exists:ticket_sub_menu,id',
            'target_sub_menu_id' => 'required|exists:ticket_sub_menu,id',
            'condition_key'      => 'nullable|string',
        ]);
        $isBidirectional     = $request->boolean('is_bidirectional');
        $isConditional       = $request->boolean('is_conditional');
        $isCrossEnvironment = $request->boolean('is_cross_environment');
        try {
            $rule = new TicketDependencyRule();
            $rule->id                   = (string) Uuid::uuid4();
            $rule->source_sub_menu_id   = $request->source_sub_menu_id;
            $rule->target_sub_menu_id   = $request->target_sub_menu_id;
            $rule->condition_key        = $request->filled('condition_key') ? $request->condition_key : '';
            $rule->is_conditional       = $isConditional;
            $rule->is_bidirectional     = $isBidirectional;
            $rule->is_cross_environment = $isCrossEnvironment;
            $rule->description          = $request->description;
            $rule->is_active            = 1;
            $rule->forward_checker_condition_id   = $request->input('forward_checker_condition_id') ?? ($rule->condition_key ?: null);
            $rule->reverse_checker_condition_id   = $isBidirectional ? ($request->input('reverse_checker_condition_id') ?? ($rule->condition_key ?: null)) : null;
            $rule->forward_requestor_condition_id = $isConditional ? ($request->input('forward_requestor_condition_id') ?? ($rule->condition_key ?: null)) : null;
            $rule->reverse_requestor_condition_id = null;
            $rule->save();
            return response()->json([
                'code'    => 200,
                'message' => 'Dependency rule berhasil ditambahkan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function dependencyUpdate(Request $request, $id)
    {
        $rule = TicketDependencyRule::findOrFail($id);
        $request->validate([
            'source_sub_menu_id' => 'sometimes|required|exists:ticket_sub_menu,id',
            'target_sub_menu_id' => 'sometimes|required|exists:ticket_sub_menu,id',
            'condition_key'      => 'nullable|string',
        ]);
        $isBidirectional     = $request->boolean('is_bidirectional');
        $isConditional       = $request->boolean('is_conditional');
        $isCrossEnvironment = $request->boolean('is_cross_environment');
        try {
            $rule->source_sub_menu_id =
                $request->input(
                    'source_sub_menu_id',
                    $rule->source_sub_menu_id
                );
            $rule->target_sub_menu_id =
                $request->input(
                    'target_sub_menu_id',
                    $rule->target_sub_menu_id
                );
            $rule->description =
                $request->input(
                    'description',
                    $rule->description
                );
            $rule->is_bidirectional     = $isBidirectional;
            $rule->is_conditional       = $isConditional;
            $rule->is_cross_environment = $isCrossEnvironment;
            if ($isConditional) {
                $rule->condition_key =
                    $request->filled('condition_key')
                    ? $request->input('condition_key')
                    : ($rule->condition_key ?? '');
                $rule->forward_requestor_condition_id =
                    $request->input('forward_requestor_condition_id')
                    ?? ($rule->condition_key ?: null);
                $rule->reverse_requestor_condition_id =
                    $request->input(
                        'reverse_requestor_condition_id',
                        $rule->reverse_requestor_condition_id
                    );
            } else {
                $rule->condition_key = '';
                $rule->forward_requestor_condition_id = null;
                $rule->reverse_requestor_condition_id = null;
            }
            $rule->forward_checker_condition_id =
                $request->input(
                    'forward_checker_condition_id',
                    $rule->forward_checker_condition_id
                );
            if ($isBidirectional) {
                $rule->reverse_checker_condition_id =
                    $request->input(
                        'reverse_checker_condition_id',
                        $rule->reverse_checker_condition_id
                    );
            } else {

                $rule->reverse_checker_condition_id = null;
            }
            if ($request->has('is_active')) {
                $rule->is_active = $request->boolean('is_active');
            }
            $rule->save();
            return response()->json([
                'code'    => 200,
                'message' => 'Dependency rule berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function dependencyDestroy($id)
    {
        try {
            TicketDependencyRule::where('id', $id)->delete();
            return response()->json([
                'code'    => 200,
                'message' => 'Dependency rule berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function dependency_menu_kategori(Request $request)
    {
        $query = TicketMenu::query();
        if ($request->filled('menu_id')) {
            $data = TicketSubMenu::where('menu_id', $request->menu_id)
                ->select('id', 'nama_sub_menu')
                ->get();
        } else {
            $data = TicketMenu::select('id', 'nama_menu')->get();
        }
        return response()->json(['data' => $data]);
    }

    public function get_holding(Request $request)
    {
        $holdings = Holding::orderBy('holding_name', 'ASC')
            ->get(['id', 'holding_name']);
        return response()->json(
            $holdings->map(function ($holding) {
                return [
                    'id'   => $holding->id,
                    'text' => $holding->holding_name,
                ];
            })
        );
    }

    public function get_departemen(Request $request)
    {
        $request->validate([
            'holding' => 'required'
        ]);
        $departemens = Departemen::where('holding', $request->holding)
            ->orderBy('nama_departemen', 'ASC')
            ->get(['id', 'nama_departemen']);

        return response()->json(
            $departemens->map(function ($departemen) {
                return [
                    'id'   => $departemen->id,
                    'text' => $departemen->nama_departemen,
                ];
            })
        );
    }

    public function get_divisi(Request $request)
    {
        $request->validate([
            'departemen' => 'required'
        ]);
        $divisis = Divisi::where('dept_id', $request->departemen)
            ->orderBy('nama_divisi', 'ASC')
            ->get(['id', 'nama_divisi']);
        return response()->json(
            $divisis->map(function ($divisi) {
                return [
                    'id'   => $divisi->id,
                    'text' => $divisi->nama_divisi,
                ];
            })
        );
    }

    public function get_bagian(Request $request)
    {
        $request->validate([
            'divisi' => 'required'
        ]);
        $bagians = Bagian::where('divisi_id', $request->divisi)
            ->orderBy('nama_bagian', 'ASC')
            ->get(['id', 'nama_bagian']);
        return response()->json(
            $bagians->map(function ($bagian) {
                return [
                    'id'   => $bagian->id,
                    'text' => $bagian->nama_bagian,
                ];
            })
        );
    }

    public function get_jabatan(Request $request)
    {
        $request->validate([
            'bagian' => 'required|uuid',
        ]);
        $jabatans = Jabatan::where('bagian_id', $request->bagian)
            ->orderBy('nama_jabatan', 'ASC')
            ->get(['id', 'nama_jabatan']);
        return response()->json(
            $jabatans->map(function ($jabatan) {
                return [
                    'id'   => $jabatan->id,
                    'text' => $jabatan->nama_jabatan,
                ];
            })
        );
    }

    public function get_karyawan($step_id)
    {
        $step = TicketApprovalStep::findOrFail($step_id);
        $assignedIds = TicketApprovalKaryawan::where('approval_step_id', $step_id)
            ->pluck('karyawan_id')
            ->map(fn($v) => (string) $v)
            ->toArray();
        $query = KaryawanJabatan::query()
            ->with([
                'Karyawan:id,name,status_aktif',
                'Divisi:id,nama_divisi',
                'Bagian:id,nama_bagian',
            ])
            ->where('is_active', 1)
            ->whereHas('Karyawan', fn($q) => $q->where('status_aktif', 'AKTIF'));
        if ($step->holding_id) {
            $query->where('holding_id', $step->holding_id);
        }
        if ($step->department_id) {
            $query->where('departemen_id', $step->department_id);
        }
        if ($step->jabatan_id) {
            $jabatanMaster = JabatanMaster::find($step->jabatan_id);
            if ($jabatanMaster && $jabatanMaster->level_id) {
                $query->whereHas(
                    'Jabatan',
                    fn($q) =>
                    $q->where('level_id', $jabatanMaster->level_id)
                );
            }
        }
        $data = $query->get()
            ->map(function ($row) use ($assignedIds) {
                if (!$row->Karyawan) return null;
                return [
                    'id'      => $row->Karyawan->id,
                    'nama'    => $row->Karyawan->name,
                    'divisi'  => $row->Divisi->nama_divisi ?? '-',
                    'bagian'  => $row->Bagian->nama_bagian ?? '-',
                    'jabatan' => $row->Jabatan->nama_jabatan ?? '-',
                    'checked' => in_array((string) $row->Karyawan->id, $assignedIds),
                ];
            })
            ->filter()
            ->values();
        return response()->json(['data' => $data]);
    }

    public function validatorDatatable(Request $request)
    {
        if ($request->boolean('stats')) {
            return response()->json([
                'epicor'       => TicketApprovalValidator::where('environment', 1)->where('is_active', 1)->count(),
                'accurate'     => TicketApprovalValidator::where('environment', 2)->where('is_active', 1)->count(),
                'accurate_aja' => TicketApprovalValidator::where('environment', 3)->where('is_active', 1)->count(),
                'active'       => TicketApprovalValidator::where('is_active', 1)->count(),
            ]);
        }
        $data = TicketApprovalValidator::query()
            ->leftJoin('departemens', DB::raw('departemens.id::text'), '=', 'ticket_approval_validator.departemen_id')
            ->leftJoin('divisis',  DB::raw('divisis.id::text'), '=', 'ticket_approval_validator.divisi_id')
            ->leftJoin('bagians',  DB::raw('bagians.id::text'), '=', 'ticket_approval_validator.bagian_id')
            ->leftJoin('jabatans',  DB::raw('jabatans.id::text'), '=', 'ticket_approval_validator.jabatan_id')
            ->leftJoin('karyawans',  DB::raw('karyawans.id::text'), '=', 'ticket_approval_validator.karyawan_id')
            ->select([
                'ticket_approval_validator.id',
                'ticket_approval_validator.environment',
                'ticket_approval_validator.departemen_id',
                'ticket_approval_validator.divisi_id',
                'ticket_approval_validator.bagian_id',
                'ticket_approval_validator.jabatan_id',
                'ticket_approval_validator.karyawan_id',
                'ticket_approval_validator.is_active',
                'departemens.nama_departemen',
                'divisis.nama_divisi',
                'bagians.nama_bagian',
                'jabatans.nama_jabatan',
                'karyawans.name as nama_karyawan',
            ]);
        if ($request->filled('env')) {
            $data->where('ticket_approval_validator.environment', $request->env);
        }
        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-xs btn-outline-warning btn_edit_validator me-1"
                    data-id="'            . $row->id             . '"
                    data-environment="'   . e($row->environment) . '"
                    data-departemen-id="' . $row->departemen_id  . '"
                    data-divisi-id="'     . $row->divisi_id      . '"
                    data-bagian-id="'     . $row->bagian_id      . '"
                    data-jabatan-id="'    . $row->jabatan_id     . '"
                    data-karyawan-id="'   . $row->karyawan_id    . '"
                    data-is-active="'     . ($row->is_active ? 1 : 0) . '"
                    title="Edit">
                    <i class="mdi mdi-pencil"></i>
                </button>';
                $btnDelete = '<button class="btn btn-xs btn-outline-danger btn_delete_validator"
                    data-id="' . $row->id . '"
                    title="Hapus">
                    <i class="mdi mdi-trash-can"></i>
                </button>';
                return $btnEdit . $btnDelete;
            })
            ->addColumn('departemen', fn($row) => e($row->nama_departemen ?? '—'))
            ->addColumn('divisi',     fn($row) => e($row->nama_divisi     ?? '—'))
            ->addColumn('bagian',     fn($row) => e($row->nama_bagian     ?? '—'))
            ->addColumn('jabatan',    fn($row) => e($row->nama_jabatan    ?? '—'))
            ->filterColumn('nama_karyawan', function ($q, $keyword) {
                $q->where('karyawans.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('nama_departemen', function ($q, $keyword) {
                $q->where('departemens.nama_departemen', 'ilike', "%{$keyword}%");
            })
            ->rawColumns(['environment', 'action'])
            ->make(true);
    }

    public function validatorGetUsers(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|uuid',
        ]);
        $users = KaryawanJabatan::query()
            ->with('Karyawan:id,name,status_aktif')
            ->where('jabatan_id', $request->jabatan)
            ->where('is_active', 1)
            ->whereHas('Karyawan', fn($q) => $q->where('status_aktif', 'AKTIF'))
            ->get()
            ->filter(fn($kj) => $kj->Karyawan !== null)
            ->map(fn($kj) => [
                'id'   => $kj->Karyawan->id,
                'text' => $kj->Karyawan->name,
            ])
            ->values();
        return response()->json(['data' => $users]);
    }

    public function validatorStore(Request $request)
    {
        $request->validate([
            'environment'   => 'required|in:1,2,3',
            'holding_id'    => 'nullable|uuid',
            'departemen_id' => 'required|uuid',
            'divisi_id'     => 'nullable|uuid',
            'bagian_id'     => 'nullable|uuid',
            'jabatan_id'    => 'required|uuid',
            'karyawan_id'   => 'required|uuid',
        ]);
        $exists = TicketApprovalValidator::where('environment', $request->environment)
            ->where('jabatan_id', $request->jabatan_id)
            ->where('karyawan_id', $request->karyawan_id)
            ->exists();
        if ($exists) {
            return response()->json([
                'code'    => 422,
                'message' => 'Validator sudah ada.',
            ], 422);
        }
        // dd($request->all(), $exists, $userId);
        $validator = TicketApprovalValidator::create([
            'id'            => (string) Uuid::uuid4(),
            'environment'   => $request->environment,
            'holding_id'    => $request->holding_id,
            'departemen_id' => $request->departemen_id,
            'divisi_id'     => $request->divisi_id,
            'bagian_id'     => $request->bagian_id,
            'jabatan_id'    => $request->jabatan_id,
            'karyawan_id'   => $request->karyawan_id,
            'is_active'     => 1,
            'created_at'    => now(),
        ]);
        return response()->json([
            'code'    => 200,
            'message' => 'Master validator berhasil ditambahkan.',
            'data'    => $validator,
        ]);
    }

    public function validatorUpdate(Request $request, $id)
    {
        $request->validate([
            'environment'   => 'required|in:1,2,3',
            'holding_id'    => 'nullable|uuid',
            'departemen_id' => 'required|uuid',
            'divisi_id'     => 'nullable|uuid',
            'bagian_id'     => 'nullable|uuid',
            'jabatan_id'    => 'required|uuid',
            'karyawan_id'   => 'required|uuid',
            'is_active'     => 'nullable|boolean',
        ]);
        $validator = TicketApprovalValidator::findOrFail($id);
        $exists = TicketApprovalValidator::where('environment', $request->environment)
            ->where('jabatan_id', $request->jabatan_id)
            ->where('karyawan_id', $request->karyawan_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'code'    => 422,
                'message' => 'Validator sudah ada.',
            ], 422);
        }
        $validator->update([
            'environment'   => $request->environment,
            'holding_id'    => $request->holding_id,
            'departemen_id' => $request->departemen_id,
            'divisi_id'     => $request->divisi_id,
            'bagian_id'     => $request->bagian_id,
            'jabatan_id'    => $request->jabatan_id,
            'users_id'      => $request->users_id,
            'is_active'     => $request->has('is_active') ? (int) $request->is_active : $validator->is_active,
        ]);

        return response()->json([
            'code'    => 200,
            'message' => 'Master validator berhasil diperbarui.',
            'data'    => $validator->fresh(),
        ]);
    }
    public function validatorDestroy($id)
    {
        try {
            $validator = TicketApprovalValidator::findOrFail($id);
            $validator->delete();

            return response()->json([
                'code'    => 200,
                'message' => 'Master validator berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'code'    => 404,
                'message' => 'Data validator tidak ditemukan.',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function assignmentDatatable(Request $request)
    {
        if ($request->boolean('stats')) {
            return response()->json([
                'a01'    => TicketAssignmentMaster::where('assignment_type', 'A01')->where('is_active', 1)->count(),
                'a02'    => TicketAssignmentMaster::where('assignment_type', 'A02')->where('is_active', 1)->count(),
                'active' => TicketAssignmentMaster::where('is_active', 1)->count(),
            ]);
        }
        $data = TicketAssignmentMaster::query()
            ->leftJoin('holdings', function ($join) {
                $join->on('holdings.id', '=', DB::raw('ticket_assignment_master.holding_id::text'));
            })
            ->leftJoin('departemens', DB::raw('departemens.id::text'), '=', 'ticket_assignment_master.departemen_id')
            ->leftJoin('divisis',     DB::raw('divisis.id::text'),     '=', 'ticket_assignment_master.divisi_id')
            ->leftJoin('bagians',     DB::raw('bagians.id::text'),     '=', 'ticket_assignment_master.bagian_id')
            ->leftJoin('jabatans',    DB::raw('jabatans.id::text'),    '=', 'ticket_assignment_master.jabatan_id')
            ->leftJoin('karyawans',   DB::raw('karyawans.id::text'),   '=', 'ticket_assignment_master.karyawan_id')
            ->select([
                'ticket_assignment_master.id',
                'ticket_assignment_master.assignment_type',
                'ticket_assignment_master.is_active',
                'ticket_assignment_master.departemen_id',
                'ticket_assignment_master.divisi_id',
                'ticket_assignment_master.bagian_id',
                'ticket_assignment_master.jabatan_id',
                'ticket_assignment_master.karyawan_id',
                'ticket_assignment_master.environment',
                'holdings.id as holding_id',
                'holdings.holding_name as holding',
                'departemens.nama_departemen as departemen',
                'divisis.nama_divisi as divisi',
                'bagians.nama_bagian as bagian',
                'jabatans.nama_jabatan as jabatan',
                'karyawans.name as karyawan',
            ]);
        if ($request->filled('type')) {
            $data->where('ticket_assignment_master.assignment_type', $request->type);
        }
        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="btn btn-xs btn-outline-warning btn_edit_assignment me-1"
                    data-id="'            . $row->id              . '"
                    data-type="'          . e($row->assignment_type) . '"
                    data-environment="'   . e($row->environment) . '"
                    data-holding-id="'    . $row->holding_id      . '"
                    data-departemen-id="' . $row->departemen_id   . '"
                    data-divisi-id="'     . $row->divisi_id       . '"
                    data-bagian-id="'     . $row->bagian_id       . '"
                    data-jabatan-id="'    . $row->jabatan_id      . '"
                    data-karyawan-id="'   . $row->karyawan_id     . '"
                    data-is-active="'     . ($row->is_active ? 1 : 0) . '"
                    title="Edit">
                    <i class="mdi mdi-pencil"></i>
                </button>';

                $btnDelete = '<button class="btn btn-xs btn-outline-danger btn_delete_assignment"
                    data-id="' . $row->id . '"
                    title="Hapus">
                    <i class="mdi mdi-trash-can"></i>
                </button>';

                return $btnEdit . $btnDelete;
            })
            ->filterColumn('karyawan', function ($q, $keyword) {
                $q->where('karyawans.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('departemen', function ($q, $keyword) {
                $q->where('departemens.nama_departemen', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('jabatan', function ($q, $keyword) {
                $q->where('jabatans.nama_jabatan', 'ilike', "%{$keyword}%");
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function assignmentStore(Request $request)
    {
        $request->validate([
            'assignment_type' => 'required|in:A01,A02',
            'environment'     => 'required|in:1,2,3',
            'holding_id'      => 'nullable|uuid',
            'departemen_id'   => 'required|uuid',
            'divisi_id'       => 'nullable|uuid',
            'bagian_id'       => 'nullable|uuid',
            'jabatan_id'      => 'required|uuid',
            'karyawan_id'     => 'required|uuid',
        ]);
        $record = TicketAssignmentMaster::create([
            'id'              => (string) Uuid::uuid4(),
            'assignment_type' => $request->assignment_type,
            'environment'     => $request->environment,
            'holding_id'      => $request->holding_id,
            'departemen_id'   => $request->departemen_id,
            'divisi_id'       => $request->divisi_id,
            'bagian_id'       => $request->bagian_id,
            'jabatan_id'      => $request->jabatan_id,
            'karyawan_id'     => $request->karyawan_id,
            'is_active'       => 1,
            'created_at'      => now(),
        ]);
        return response()->json([
            'code'    => 200,
            'message' => 'Master assignment berhasil ditambahkan.',
            'data'    => $record,
        ]);
    }

    public function assignmentUpdate(Request $request, $id)
    {
        $request->validate([
            'assignment_type' => 'required|in:A01,A02',
            'environment'     => 'required|in:1,2,3',
            'holding_id'         => 'nullable|uuid',
            'departemen_id'   => 'required|uuid',
            'divisi_id'       => 'nullable|uuid',
            'bagian_id'       => 'nullable|uuid',
            'jabatan_id'      => 'required|uuid',
            'karyawan_id'     => 'required|uuid',
            'is_active'       => 'nullable|boolean',
        ]);
        $record = TicketAssignmentMaster::findOrFail($id);
        $record->update([
            'assignment_type' => $request->assignment_type,
            'environment'     => $request->environment,
            'holding_id'      => $request->holding_id,
            'departemen_id'   => $request->departemen_id,
            'divisi_id'       => $request->divisi_id,
            'bagian_id'       => $request->bagian_id,
            'jabatan_id'      => $request->jabatan_id,
            'karyawan_id'     => $request->karyawan_id,
            'is_active'       => $request->has('is_active')
                ? (int) $request->is_active
                : $record->is_active,
        ]);
        return response()->json([
            'code'    => 200,
            'message' => 'Master assignment berhasil diperbarui.',
            'data'    => $record->fresh(),
        ]);
    }
    public function assignmentDestroy($id)
    {
        try {
            TicketAssignmentMaster::findOrFail($id)->delete();
            return response()->json([
                'code'    => 200,
                'message' => 'Master assignment berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['code' => 404, 'message' => 'Data tidak ditemukan.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function difficultyDatatable(Request $request)
    {
        if ($request->boolean('stats')) {
            $total = TicketDifficultyMaster::count();
            $avg   = TicketDifficultyMaster::avg('expected_hours');
            $min   = TicketDifficultyMaster::min('expected_hours');
            $max   = TicketDifficultyMaster::max('expected_hours');

            $formatMinutes = function ($mins) {
                if ($mins === null) return '—';
                $m = round((float) $mins);
                if ($m < 60) return $m . ' Menit';
                $h = round($m / 60, 1);
                return $m . ' Menit (~' . $h . ' Jam)';
            };

            return response()->json([
                'total' => $total,
                'avg'   => $formatMinutes($avg),
                'min'   => $formatMinutes($min),
                'max'   => $formatMinutes($max),
            ]);
        }

        $data = TicketDifficultyMaster::query()->orderBy('code');

        return DataTables::of($data)
            ->addColumn('code_badge', function ($row) {
                $code = e($row->code);
                $badgeCls = match ($code) {
                    'P01' => 'bg-danger text-white',
                    'P02' => 'bg-warning text-dark',
                    'P03' => 'bg-info text-white',
                    'P04' => 'bg-success text-white',
                    default => 'bg-primary text-white',
                };
                return '<span class="badge ' . $badgeCls . '" style="font-size:11px; font-weight:700; letter-spacing:0.5px;">' . $code . '</span>';
            })
            ->addColumn('sla_formatted', function ($row) {
                if ($row->expected_hours === null) {
                    return '<span class="text-muted">—</span>';
                }
                $mins = (float) $row->expected_hours;
                if ($mins < 60) {
                    return '<span class="fw-bold" style="color:#6b21a8;"><i class="mdi mdi-clock-outline me-1"></i>' . $mins . ' Menit</span>';
                }
                $hours = round($mins / 60, 1);
                if ($hours < 24) {
                    return '<span class="fw-bold" style="color:#6b21a8;"><i class="mdi mdi-clock-outline me-1"></i>' . $mins . ' Menit</span> <span class="badge bg-light text-muted border ms-1">~' . $hours . ' Jam</span>';
                }
                $days = round($hours / 24, 1);
                return '<span class="fw-bold" style="color:#6b21a8;"><i class="mdi mdi-clock-outline me-1"></i>' . $mins . ' Menit</span> <span class="badge bg-light text-muted border ms-1">~' . $hours . ' Jam / ' . $days . ' Hari</span>';
            })
            ->addColumn('action', function ($row) {
                $btnEdit = '<button class="act-btn act-edit btn_edit_difficulty me-1"
                    data-id="' . $row->id . '"
                    data-code="' . e($row->code) . '"
                    data-name="' . e($row->name) . '"
                    data-hours="' . e($row->expected_hours) . '"
                    title="Edit">
                    <i class="mdi mdi-pencil"></i>
                </button>';

                $btnDelete = '<button class="act-btn act-delete btn_delete_difficulty"
                    data-id="' . $row->id . '"
                    data-code="' . e($row->code) . '"
                    title="Hapus">
                    <i class="mdi mdi-trash-can"></i>
                </button>';

                return $btnEdit . $btnDelete;
            })
            ->rawColumns(['code_badge', 'sla_formatted', 'action'])
            ->make(true);
    }

    public function difficultyStore(Request $request)
    {
        $request->validate([
            'code'           => 'required|string|max:50|unique:ticket_difficulty_master,code',
            'name'           => 'required|string|max:255',
            'expected_hours' => 'required|numeric|min:1',
        ], [
            'code.required'           => 'Kode tingkat kesulitan wajib diisi.',
            'code.unique'             => 'Kode tingkat kesulitan sudah digunakan.',
            'name.required'           => 'Nama tingkat kesulitan wajib diisi.',
            'expected_hours.required' => 'Target SLA (Menit) wajib diisi.',
            'expected_hours.numeric'  => 'Target SLA harus berupa angka (menit).',
            'expected_hours.min'      => 'Target SLA minimal 1 menit.',
        ]);

        $record = TicketDifficultyMaster::create([
            'code'           => strtoupper(trim($request->code)),
            'name'           => trim($request->name),
            'expected_hours' => (float) $request->expected_hours,
        ]);

        return response()->json([
            'code'    => 200,
            'message' => 'Tingkat kesulitan berhasil ditambahkan.',
            'data'    => $record,
        ]);
    }

    public function difficultyUpdate(Request $request, $id)
    {
        $request->validate([
            'code'           => 'required|string|max:50|unique:ticket_difficulty_master,code,' . $id,
            'name'           => 'required|string|max:255',
            'expected_hours' => 'required|numeric|min:1',
        ], [
            'code.required'           => 'Kode tingkat kesulitan wajib diisi.',
            'code.unique'             => 'Kode tingkat kesulitan sudah digunakan.',
            'name.required'           => 'Nama tingkat kesulitan wajib diisi.',
            'expected_hours.required' => 'Target SLA (Menit) wajib diisi.',
            'expected_hours.numeric'  => 'Target SLA harus berupa angka (menit).',
            'expected_hours.min'      => 'Target SLA minimal 1 menit.',
        ]);

        $record = TicketDifficultyMaster::findOrFail($id);
        $record->update([
            'code'           => strtoupper(trim($request->code)),
            'name'           => trim($request->name),
            'expected_hours' => (float) $request->expected_hours,
        ]);

        return response()->json([
            'code'    => 200,
            'message' => 'Tingkat kesulitan berhasil diperbarui.',
            'data'    => $record->fresh(),
        ]);
    }

    public function difficultyDestroy($id)
    {
        try {
            $record = TicketDifficultyMaster::findOrFail($id);
            $record->delete();

            return response()->json([
                'code'    => 200,
                'message' => 'Tingkat kesulitan berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['code' => 404, 'message' => 'Data tidak ditemukan.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function report($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holdingObj = Holding::where('holding_code', $holding)->first();
        if (!$holdingObj) {
            $holdingObj = Holding::first();
        }
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();
        $roleId = count($get_role) > 0 ? $get_role : null;
        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();

        $holdings = Holding::orderBy('holding_name')->get();

        return view('admin.ticket.report', [
            'title'    => 'Report Ticket',
            'holding'  => $holdingObj,
            'holdings' => $holdings,
            'menus'    => $menus,
        ]);
    }

    public function reportDepartemenList(Request $request)
    {
        $departemens = Departemen::with('Holding')
            ->orderBy('nama_departemen')
            ->get()
            ->map(function ($d) {
                return [
                    'id'              => $d->id,
                    'nama_departemen' => $d->nama_departemen,
                    'holding_name'    => $d->Holding->holding_name ?? 'Tanpa Holding',
                ];
            });

        return response()->json([
            'code' => 200,
            'data' => $departemens,
        ]);
    }

    public function getListTicketReport(Request $request)
    {
        try {
            $startDate   = $request->start_date;
            $endDate     = $request->end_date;
            $departemen  = $request->departemen;
            $status      = $request->status;
            $kategori    = $request->kategori;
            $holdingCode = $request->holding_code;

            $query = TicketHead::with([
                'ticketDetails.subDetails',
                'ticketDetails.referenceTicketDetail',
                'ticketMenu',
                'ticketSubMenu',
                'difficulty',
                'Requestor.KaryawanNIK.PenempatanKerja.Holding',
                'Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen',
                'workSessions',
            ])
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->when($departemen, function ($q) use ($departemen) {
                    $q->whereHas('Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen', function ($q2) use ($departemen) {
                        $q2->where('nama_departemen', 'like', '%' . $departemen . '%')
                            ->orWhere('kode', $departemen);
                    });
                })
                ->when($status, function ($q) use ($status) {
                    if ($status === 'T08' || $status === 'rejected') {
                        $q->where(function ($sub) {
                            $sub->where('ticket_status', TicketHead::STATUS_CANCELLED)
                                ->orWhere('approval_status', TicketHead::APPROVAL_REJECTED);
                        });
                    } elseif ($status === 'T06') {
                        $q->whereIn('ticket_status', [TicketHead::STATUS_CLOSED, TicketHead::STATUS_AUTOMATIC_CLOSE]);
                    } else {
                        $q->where('ticket_status', $status);
                    }
                })
                ->when($kategori, function ($q) use ($kategori) {
                    $q->whereHas('ticketMenu', fn($qm) => $qm->where('kategori', $kategori));
                })
                ->when($holdingCode, function ($q) use ($holdingCode) {
                    $q->whereHas('Requestor.KaryawanNIK.PenempatanKerja.Holding', function ($qh) use ($holdingCode) {
                        $qh->where('holding_code', $holdingCode);
                    });
                })
                ->orderBy('created_at', 'desc');

            $tickets = $query->get();

            $stats = [
                'total'       => 0,
                'submitted'   => 0,
                'in_progress' => 0,
                'completed'   => 0,
                'cancelled'   => 0,
            ];

            $result = [];
            foreach ($tickets as $ticket) {
                $stats['total']++;
                if (in_array($ticket->ticket_status, [
                    TicketHead::STATUS_SUBMITTED,
                    TicketHead::STATUS_WAITING_VALIDATOR,
                    TicketHead::STATUS_WAITING_CHECKER,
                    TicketHead::STATUS_WAITING_APPROVAL1,
                    TicketHead::STATUS_WAITING_APPROVAL2,
                    TicketHead::STATUS_WAITING_ASSIGNER,
                ])) {
                    $stats['submitted']++;
                } elseif (in_array($ticket->ticket_status, [
                    TicketHead::STATUS_ASSIGNED,
                    TicketHead::STATUS_IN_PROGRESS,
                    TicketHead::STATUS_WAITING_REQUESTOR,
                    TicketHead::STATUS_FEEDBACK_REQUESTOR,
                    TicketHead::STATUS_APPROVED
                ])) {
                    $stats['in_progress']++;
                } elseif (in_array($ticket->ticket_status, [TicketHead::STATUS_CLOSED, TicketHead::STATUS_AUTOMATIC_CLOSE])) {
                    $stats['completed']++;
                } elseif ($ticket->ticket_status === TicketHead::STATUS_CANCELLED || $ticket->approval_status === TicketHead::APPROVAL_REJECTED) {
                    $stats['cancelled']++;
                }

                $deptName    = $ticket->Requestor?->KaryawanNIK?->KaryawanJabatanOne?->Departemen?->nama_departemen ?? '-';
                $siteName    = $ticket->Requestor?->KaryawanNIK?->PenempatanKerja?->site_name ?? '-';
                $holdingName = $ticket->Requestor?->KaryawanNIK?->PenempatanKerja?->Holding?->holding_name ?? '-';

                // Ambil tanggal terakhir selesai dikerjakan oleh PIC (jika ada beberapa revisi / siklus kerja, ambil ended_at / finish_at yang paling akhir)
                $latestSessionEnded = $ticket->workSessions ? $ticket->workSessions->whereNotNull('ended_at')->max('ended_at') : null;
                $picFinishedAt = null;
                if ($latestSessionEnded && $ticket->finish_at) {
                    $picFinishedAt = \Carbon\Carbon::parse($latestSessionEnded)->gt(\Carbon\Carbon::parse($ticket->finish_at))
                        ? $latestSessionEnded
                        : $ticket->finish_at;
                } else {
                    $picFinishedAt = $ticket->finish_at ?? $latestSessionEnded;
                }
                $picFinishedAtFormatted = $picFinishedAt ? \Carbon\Carbon::parse($picFinishedAt)->format('Y-m-d H:i:s') : null;

                $closedAtFormatted = ($ticket->closed_at ?? (in_array($ticket->ticket_status, [TicketHead::STATUS_CLOSED, TicketHead::STATUS_AUTOMATIC_CLOSE]) ? ($ticket->updated_at ?? $picFinishedAt) : null))
                    ? \Carbon\Carbon::parse($ticket->closed_at ?? ($ticket->updated_at ?? $picFinishedAt))->format('Y-m-d H:i:s')
                    : null;

                if ($ticket->ticketDetails->isEmpty()) {
                    $result[] = [
                        'id'                          => $ticket->id,
                        'created_at'                  => $ticket->created_at,
                        'nomor_ticket'                => $ticket->nomor_ticket,
                        'ticket_status'               => $ticket->ticket_status,
                        'approval_status'             => $ticket->approval_status,
                        'ticket_menu'                 => $ticket->ticketMenu,
                        'ticket_kategori'             => $ticket->ticketMenu?->kategori,
                        'ticket_sub_menu'             => $ticket->ticketSubMenu,
                        'ticket_priority'             => $ticket->ticket_priority,
                        'priority_name'               => $ticket->difficulty?->name ?? $ticket->ticket_priority ?? '-',
                        'tanggal_transaksi'           => '-',
                        'line_detail'                 => '-',
                        'nomor_detail'                => '-',
                        'reference_nomor_detail'      => '-',
                        'reference_tanggal_transaksi' => '-',
                        'line_sub_detail'             => '-',
                        'alasan_ubah'                 => '-',
                        'before_ubah'                 => '-',
                        'after_ubah'                  => '-',
                        'pic_name'                    => $ticket->pic_name ?: '-',
                        'validator_at'                => $ticket->validator_at ? \Carbon\Carbon::parse($ticket->validator_at)->format('Y-m-d H:i:s') : null,
                        'assign_at'                   => $ticket->assign_at ? \Carbon\Carbon::parse($ticket->assign_at)->format('Y-m-d H:i:s') : null,
                        'pic_started_at'              => $ticket->pic_started_at ? \Carbon\Carbon::parse($ticket->pic_started_at)->format('Y-m-d H:i:s') : null,
                        'finish_at'                   => $picFinishedAtFormatted,
                        'closed_at'                   => $closedAtFormatted,
                        'approve1_name'               => $ticket->approve1_name,
                        'approve2_name'               => $ticket->approve2_name,
                        'checker_name'                => $ticket->checker_name,
                        'validator_name'              => $ticket->validator_name,
                        'requestor_name'              => $ticket->requestor_name ?: ($ticket->Requestor?->name ?? '-'),
                        'site_name'                   => $siteName,
                        'holding_name'                => $holdingName,
                        'departemen'                  => $deptName,
                    ];
                } else {
                    foreach ($ticket->ticketDetails as $detail) {
                        $refNomor   = $detail->referenceTicketDetail?->nomor_detail ?? '-';
                        $refTanggal = $detail->referenceTicketDetail?->tanggal_transaksi ?? '-';
                        if ($detail->subDetails && $detail->subDetails->isNotEmpty()) {
                            foreach ($detail->subDetails as $sub) {
                                $result[] = [
                                    'id'                          => $ticket->id,
                                    'created_at'                  => $ticket->created_at,
                                    'nomor_ticket'                => $ticket->nomor_ticket,
                                    'ticket_status'               => $ticket->ticket_status,
                                    'approval_status'             => $ticket->approval_status,
                                    'ticket_menu'                 => $ticket->ticketMenu,
                                    'ticket_kategori'             => $ticket->ticketMenu?->kategori,
                                    'ticket_sub_menu'             => $ticket->ticketSubMenu,
                                    'ticket_priority'             => $ticket->ticket_priority,
                                    'priority_name'               => $ticket->difficulty?->name ?? $ticket->ticket_priority ?? '-',
                                    'tanggal_transaksi'           => $detail->tanggal_transaksi ?? '-',
                                    'line_detail'                 => $detail->line_detail ?? '-',
                                    'nomor_detail'                => $detail->nomor_detail ?? '-',
                                    'reference_nomor_detail'      => $refNomor,
                                    'reference_tanggal_transaksi' => $refTanggal,
                                    'line_sub_detail'             => $sub->line_number ?? '-',
                                    'alasan_ubah'                 => $sub->alasan_ubah ?? $detail->alasan_ubah ?? '-',
                                    'before_ubah'                 => $sub->before_ubah ?? $detail->before_ubah ?? '-',
                                    'after_ubah'                  => $sub->after_ubah ?? $detail->after_ubah ?? '-',
                                    'pic_name'                    => $ticket->pic_name ?: '-',
                                    'validator_at'                => $ticket->validator_at ? \Carbon\Carbon::parse($ticket->validator_at)->format('Y-m-d H:i:s') : null,
                                    'assign_at'                   => $ticket->assign_at ? \Carbon\Carbon::parse($ticket->assign_at)->format('Y-m-d H:i:s') : null,
                                    'pic_started_at'              => $ticket->pic_started_at ? \Carbon\Carbon::parse($ticket->pic_started_at)->format('Y-m-d H:i:s') : null,
                                    'finish_at'                   => $picFinishedAtFormatted,
                                    'closed_at'                   => $closedAtFormatted,
                                    'approve1_name'               => $ticket->approve1_name,
                                    'approve2_name'               => $ticket->approve2_name,
                                    'checker_name'                => $ticket->checker_name,
                                    'validator_name'              => $ticket->validator_name,
                                    'requestor_name'              => $ticket->requestor_name ?: ($ticket->Requestor?->name ?? '-'),
                                    'site_name'                   => $siteName,
                                    'holding_name'                => $holdingName,
                                    'departemen'                  => $deptName,
                                ];
                            }
                        } else {
                            $result[] = [
                                'id'                          => $ticket->id,
                                'created_at'                  => $ticket->created_at,
                                'nomor_ticket'                => $ticket->nomor_ticket,
                                'ticket_status'               => $ticket->ticket_status,
                                'approval_status'             => $ticket->approval_status,
                                'ticket_menu'                 => $ticket->ticketMenu,
                                'ticket_kategori'             => $ticket->ticketMenu?->kategori,
                                'ticket_sub_menu'             => $ticket->ticketSubMenu,
                                'ticket_priority'             => $ticket->ticket_priority,
                                'priority_name'               => $ticket->difficulty?->name ?? $ticket->ticket_priority ?? '-',
                                'tanggal_transaksi'           => $detail->tanggal_transaksi ?? '-',
                                'line_detail'                 => $detail->line_detail ?? '-',
                                'nomor_detail'                => $detail->nomor_detail ?? '-',
                                'reference_nomor_detail'      => $refNomor,
                                'reference_tanggal_transaksi' => $refTanggal,
                                'line_sub_detail'             => '-',
                                'alasan_ubah'                 => $detail->alasan_ubah ?? '-',
                                'before_ubah'                 => $detail->before_ubah ?? '-',
                                'after_ubah'                  => $detail->after_ubah ?? '-',
                                'pic_name'                    => $ticket->pic_name ?: '-',
                                'validator_at'                => $ticket->validator_at ? \Carbon\Carbon::parse($ticket->validator_at)->format('Y-m-d H:i:s') : null,
                                'assign_at'                   => $ticket->assign_at ? \Carbon\Carbon::parse($ticket->assign_at)->format('Y-m-d H:i:s') : null,
                                'pic_started_at'              => $ticket->pic_started_at ? \Carbon\Carbon::parse($ticket->pic_started_at)->format('Y-m-d H:i:s') : null,
                                'finish_at'                   => $picFinishedAtFormatted,
                                'closed_at'                   => $closedAtFormatted,
                                'approve1_name'               => $ticket->approve1_name,
                                'approve2_name'               => $ticket->approve2_name,
                                'checker_name'                => $ticket->checker_name,
                                'validator_name'              => $ticket->validator_name,
                                'requestor_name'              => $ticket->requestor_name ?: ($ticket->Requestor?->name ?? '-'),
                                'site_name'                   => $siteName,
                                'holding_name'                => $holdingName,
                                'departemen'                  => $deptName,
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'code'  => 200,
                'data'  => $result,
                'stats' => $stats,
                'total' => count($result),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function kpiDashboard($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }

        $holdingObj = Holding::where('holding_code', $holding)->first();
        if (!$holdingObj) {
            $holdingObj = Holding::first();
        }

        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();
        $roleId = count($get_role) > 0 ? $get_role : null;
        $menus = $roleId
            ? Menu::whereIn('id', function ($query) use ($roleId) {
                $query->select('menu_id')
                    ->from('role_menus')
                    ->whereIn('role_id', $roleId);
            })
            ->whereNull('parent_id')
            ->with('children')
            ->where('kategori', 'web')
            ->orderBy('sort_order')
            ->get()
            : collect();

        $holdings = Holding::orderBy('holding_name')->get();

        return view('admin.ticket.kpi_dashboard', [
            'title'    => 'KPI Dashboard - Report Ubah Hapus',
            'holding'  => $holdingObj,
            'holdings' => $holdings,
            'menus'    => $menus,
        ]);
    }

    /**
     * Resolve rentang tanggal dari parameter period (today|week|month|year|custom)
     * @return array [Carbon $start, Carbon $end]
     */
    private function resolveKpiDateRange(Request $request)
    {
        $period = $request->input('period', 'month');
        $now = now();

        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                break;
            case 'custom':
                $start = $request->filled('start_date')
                    ? \Carbon\Carbon::parse($request->start_date)->startOfDay()
                    : $now->copy()->subDays(29)->startOfDay();
                $end = $request->filled('end_date')
                    ? \Carbon\Carbon::parse($request->end_date)->endOfDay()
                    : $now->copy()->endOfDay();
                break;
            case 'month':
            default:
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                break;
        }

        return [$start, $end];
    }

    /**
     * Query dasar TicketHead yang sudah difilter tanggal + filter umum
     * (holding_code, departemen, kategori) dipakai bersama oleh semua endpoint KPI.
     */
    private function kpiBaseQuery(Request $request, $start, $end, bool $filterCreatedAt = true)
    {
        $departemen  = $request->departemen;
        $kategori    = $request->kategori;
        $holdingCode = $request->holding_code;

        return TicketHead::query()
            ->when($filterCreatedAt, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($departemen, function ($q) use ($departemen) {
                $q->whereHas('Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen', function ($q2) use ($departemen) {
                    $q2->where('nama_departemen', 'like', '%' . $departemen . '%');
                });
            })
            ->when($kategori, function ($q) use ($kategori) {
                $q->whereHas('ticketMenu', fn($qm) => $qm->where('kategori', $kategori));
            })
            ->when($holdingCode, function ($q) use ($holdingCode) {
                $q->whereHas('Requestor.KaryawanNIK.PenempatanKerja.Holding', function ($qh) use ($holdingCode) {
                    $qh->where('holding_code', $holdingCode);
                });
            });
    }

    /**
     * Ambil total menit KERJA PIC (bukan wall-clock tiket) untuk sekumpulan tiket,
     * dari log ticket_work_sessions. Hanya sesi yang sudah ditutup (ended_at terisi)
     * yang dihitung — sehingga waktu "Menunggu Respon Pengaju" / "Feedback Pengaju"
     * (di luar kendali PIC) otomatis TIDAK ikut terhitung, dan setiap kali ada
     * revisi, cycle baru otomatis terjumlah ke total yang sama (SUM per ticket_id).
     *
     * @return \Illuminate\Support\Collection  [ticket_id => total_minutes]
     */
    private function sessionMinutesForTickets($ticketIds, string $ticketType = 'ticket')
    {
        $ticketIds = collect($ticketIds)->filter()->unique()->values();
        if ($ticketIds->isEmpty()) {
            return collect();
        }

        return TicketWorkSession::whereIn('ticket_id', $ticketIds)
            ->where('ticket_type', $ticketType)
            ->whereNotNull('ended_at')
            ->selectRaw('ticket_id, SUM(duration_minutes) as total_minutes')
            ->groupBy('ticket_id')
            ->pluck('total_minutes', 'ticket_id');
    }

    /**
     * Resolusi "actual minutes" (waktu pengerjaan PIC) untuk 1 tiket, dengan prioritas:
     * 1) Total sesi kerja PIC dari ticket_work_sessions (paling akurat: hanya waktu
     *    aktif dikerjakan, sudah termasuk akumulasi revisi, sudah exclude waktu tunggu).
     * 2) actual_hours yang sudah pernah dihitung (kalau ada, dari service lain).
     * 3) Fallback data lama tanpa log sesi: pic_started_at -> finish_at (BUKAN
     *    created_at -> finish_at), supaya minimal waktu approval sebelum tiket
     *    sampai ke PIC tidak ikut dihitung sebagai keterlambatan PIC.
     */
    private function resolveActualMinutes($ticket, $sessionMinutesMap)
    {
        $sessionMinutes = $sessionMinutesMap[$ticket->id] ?? null;
        if ($sessionMinutes !== null && (float) $sessionMinutes > 0) {
            return (float) round($sessionMinutes, 2);
        }

        if ($ticket->actual_hours !== null) {
            return (float) $ticket->actual_hours;
        }

        if ($ticket->finish_at !== null) {
            $start = $ticket->pic_started_at ?? $ticket->created_at;
            return (float) \Carbon\Carbon::parse($start)
                ->diffInMinutes(\Carbon\Carbon::parse($ticket->finish_at));
        }

        return null;
    }

    /**
     * Status-status yang berarti "pengerjaan PIC sudah selesai" untuk keperluan KPI,
     * walaupun tiket belum di-close/dikonfirmasi oleh pengaju. Ini mencegah tiket
     * yang PIC-nya sudah selesai tepat waktu tapi menunggu konfirmasi pengaju
     * (kadang tidak pernah di-close) ikut menurunkan completion rate & rata-rata waktu.
     * Kalau nanti pengaju mengirim feedback (STATUS_FEEDBACK_REQUESTOR), tiket otomatis
     * dianggap "belum selesai" lagi sampai PIC menyelesaikan siklus revisinya.
     */
    private function picDoneStatuses(): array
    {
        return [
            TicketHead::STATUS_WAITING_REQUESTOR, // T05 - PIC selesai, menunggu respon pengaju
            TicketHead::STATUS_CLOSED,             // T06 - sudah dikonfirmasi/close oleh pengaju
        ];
    }

    /**
     * GET /ticket/report/kpi/summary
     * Kartu ringkasan + data untuk speedometer (avg completion time & SLA on-time %)
     */
    public function kpiSummary(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);

            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->with('difficulty')
                ->get(['id', 'ticket_status', 'approval_status', 'created_at', 'finish_at', 'pic_started_at', 'actual_hours', 'expected_hours', 'overrun_flag']);

            $total = $tickets->count();

            $submitted = $tickets->whereIn('ticket_status', [
                TicketHead::STATUS_SUBMITTED,
                TicketHead::STATUS_WAITING_VALIDATOR,
                TicketHead::STATUS_WAITING_CHECKER,
                TicketHead::STATUS_WAITING_APPROVAL1,
                TicketHead::STATUS_WAITING_APPROVAL2,
                TicketHead::STATUS_WAITING_ASSIGNER,
            ])->count();

            // Catatan: STATUS_WAITING_REQUESTOR TIDAK dianggap "in progress" lagi di sini,
            // karena dari sisi PIC pekerjaannya sudah selesai (lihat picDoneStatuses()).
            $inProgress = $tickets->whereIn('ticket_status', [
                TicketHead::STATUS_ASSIGNED,
                TicketHead::STATUS_IN_PROGRESS,
                TicketHead::STATUS_FEEDBACK_REQUESTOR,
                TicketHead::STATUS_APPROVED,
            ])->count();

            $completed = $tickets->whereIn('ticket_status', $this->picDoneStatuses())->count();

            $cancelled = $tickets->filter(function ($t) {
                return $t->ticket_status === TicketHead::STATUS_CANCELLED
                    || $t->approval_status === TicketHead::APPROVAL_REJECTED;
            })->count();

            $picDoneStatuses = $this->picDoneStatuses();
            $closedTickets = $tickets->whereIn('ticket_status', $picDoneStatuses)
                ->filter(fn($t) => $t->actual_hours !== null || $t->finish_at !== null);

            $sessionMinutesMap = $this->sessionMinutesForTickets($closedTickets->pluck('id'));

            $totalHours       = 0;
            $totalExpectedHours = 0;
            $expectedHoursCount  = 0;
            $onTimeCount      = 0;
            $slaEligibleCount = 0;

            foreach ($closedTickets as $t) {
                $actualHours = $this->resolveActualMinutes($t, $sessionMinutesMap);
                if ($actualHours === null) {
                    continue;
                }
                $totalHours += (float) $actualHours;

                $expectedHours = $t->expected_hours ?? $t->difficulty?->expected_hours;
                if ($expectedHours !== null) {
                    $totalExpectedHours += (float) $expectedHours;
                    $expectedHoursCount++;
                    $slaEligibleCount++;
                    $onTime = ($actualHours !== null)
                        ? ((float) $actualHours <= (float) $expectedHours)
                        : ($t->overrun_flag !== null ? ! $t->overrun_flag : false);
                    if ($onTime) {
                        $onTimeCount++;
                    }
                }
            }

            $closedCount   = $closedTickets->count();
            $avgHours      = $closedCount > 0 ? round($totalHours / $closedCount, 2) : 0;
            $avgMinutes    = $avgHours;
            $avgExpectedHours = $expectedHoursCount > 0
                ? round($totalExpectedHours / $expectedHoursCount, 2)
                : null;
            $avgTimeTargetPct = $avgExpectedHours && $avgExpectedHours > 0
                ? round(($avgHours / $avgExpectedHours) * 100, 1)
                : null;
            $slaOnTimePct  = $slaEligibleCount > 0 ? round(($onTimeCount / $slaEligibleCount) * 100, 1) : null;
            $completionPct = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
            $backlog = max(0, $total - $completed - $cancelled);

            return response()->json([
                'code' => 200,
                'data' => [
                    'total'           => $total,
                    'submitted'       => $submitted,
                    'in_progress'     => $inProgress,
                    'completed'       => $completed,
                    'cancelled'       => $cancelled,
                    'backlog'         => $backlog,
                    'avg_minutes'     => $avgMinutes,
                    'avg_hours'       => $avgHours,
                    'avg_expected_hours' => $avgExpectedHours,
                    'avg_time_target_pct' => $avgTimeTargetPct,
                    'sla_ontime_pct'  => $slaOnTimePct,
                    'sla_eligible'    => $slaEligibleCount,
                    'completion_rate' => $completionPct,
                    'period'          => [
                        'start' => $start->toDateTimeString(),
                        'end'   => $end->toDateTimeString(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/avg-time-detail
     * Rincian perhitungan rata-rata waktu penyelesaian tiket (detail per tiket).
     */
    public function kpiAvgTimeDetail(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);

            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->with(['ticketMenu', 'ticketSubMenu', 'difficulty'])
                ->whereIn('ticket_status', $this->picDoneStatuses())
                ->where(function ($q) {
                    $q->whereNotNull('actual_hours')
                        ->orWhereNotNull('finish_at');
                })
                ->orderBy('finish_at', 'desc')
                ->get();

            $sessionMinutesMap = $this->sessionMinutesForTickets($tickets->pluck('id'));

            $totalActualMinutes = 0;
            $totalExpectedMinutes = 0;
            $expectedCount = 0;
            $onTimeCount = 0;

            $items = [];
            foreach ($tickets as $t) {
                $actual = $this->resolveActualMinutes($t, $sessionMinutesMap) ?? 0.0;
                $totalActualMinutes += $actual;

                $expected = $t->expected_hours ?? $t->difficulty?->expected_hours;
                if ($expected !== null) {
                    $expected = (float) $expected;
                    $totalExpectedMinutes += $expected;
                    $expectedCount++;
                    $onTime = ($actual !== null)
                        ? ($actual <= $expected)
                        : ($t->overrun_flag !== null ? ! $t->overrun_flag : null);
                } else {
                    $onTime = $t->overrun_flag !== null ? ! $t->overrun_flag : null;
                }

                if ($onTime === true) {
                    $onTimeCount++;
                }

                $variance = $expected !== null ? round($actual - $expected, 2) : null;

                $items[] = [
                    'id'               => $t->id,
                    'nomor_ticket'     => $t->nomor_ticket ?? '-',
                    'menu_name'        => $t->ticketMenu?->nama_menu ?? '-',
                    'submenu_name'     => $t->ticketSubMenu?->nama_sub_menu ?? '-',
                    'pic_name'         => $t->pic_name ?: '-',
                    'created_at'       => $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '-',
                    'finish_at'        => $t->finish_at ? \Carbon\Carbon::parse($t->finish_at)->format('d/m/Y H:i') : ($t->closed_at ? \Carbon\Carbon::parse($t->closed_at)->format('d/m/Y H:i') : '-'),
                    'actual_minutes'   => round($actual, 2),
                    'expected_minutes' => $expected !== null ? round($expected, 2) : null,
                    'variance_minutes' => $variance,
                    'is_overrun'       => $onTime === false,
                    'sla_status'       => $onTime === null ? 'No Target' : ($onTime ? 'On Time' : 'Overrun'),
                ];
            }

            $totalCount = count($items);
            $avgActualMinutes = $totalCount > 0 ? round($totalActualMinutes / $totalCount, 2) : 0;
            $avgExpectedMinutes = $expectedCount > 0 ? round($totalExpectedMinutes / $expectedCount, 2) : null;
            $avgTargetPct = ($avgExpectedMinutes && $avgExpectedMinutes > 0)
                ? round(($avgActualMinutes / $avgExpectedMinutes) * 100, 1)
                : null;

            return response()->json([
                'code' => 200,
                'data' => [
                    'summary' => [
                        'total_tickets'          => $totalCount,
                        'total_actual_minutes'   => round($totalActualMinutes, 2),
                        'avg_actual_minutes'     => $avgActualMinutes,
                        'total_expected_minutes' => round($totalExpectedMinutes, 2),
                        'avg_expected_minutes'   => $avgExpectedMinutes,
                        'avg_target_pct'         => $avgTargetPct,
                        'on_time_count'          => $onTimeCount,
                        'overrun_count'          => $totalCount - $onTimeCount,
                        'period' => [
                            'start' => $start->format('d/m/Y'),
                            'end'   => $end->format('d/m/Y'),
                        ],
                    ],
                    'tickets' => $items,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/trend
     * Data untuk mixed chart (bar = diajukan, line = diselesaikan) per hari/bulan.
     */
    public function kpiTrend(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);

            $groupBy = $request->input('group_by');
            if (!$groupBy) {
                $daySpan = $start->diffInDays($end);
                $groupBy = $daySpan > 62 ? 'month' : 'day';
            }

            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->get(['id', 'ticket_status', 'approval_status', 'created_at', 'finish_at']);
            $completedFromOlderTickets = $this->kpiBaseQuery($request, $start, $end, false)
                ->where('created_at', '<', $start)
                ->whereBetween('finish_at', [$start, $end])
                ->where('ticket_status', TicketHead::STATUS_CLOSED)
                ->get(['id', 'ticket_status', 'finish_at']);

            $format      = $groupBy === 'month' ? 'Y-m' : 'Y-m-d';
            $labelFormat = $groupBy === 'month' ? 'M Y' : 'd M';

            $submittedMap = [];
            $completedMap = [];

            // siapkan semua bucket tanggal dalam rentang supaya periode kosong tetap muncul 0
            $cursor = $start->copy();
            while ($cursor <= $end) {
                $key = $cursor->format($format);
                $submittedMap[$key] = 0;
                $completedMap[$key] = 0;
                $cursor = $groupBy === 'month' ? $cursor->addMonth() : $cursor->addDay();
            }

            foreach ($tickets as $t) {
                $key = \Carbon\Carbon::parse($t->created_at)->format($format);
                if (array_key_exists($key, $submittedMap)) {
                    $submittedMap[$key]++;
                }
                if ($t->ticket_status === TicketHead::STATUS_CLOSED && $t->finish_at) {
                    $finishKey = \Carbon\Carbon::parse($t->finish_at)->format($format);
                    if (array_key_exists($finishKey, $completedMap)) {
                        $completedMap[$finishKey]++;
                    }
                }
            }

            foreach ($completedFromOlderTickets as $t) {
                $finishKey = \Carbon\Carbon::parse($t->finish_at)->format($format);
                if (array_key_exists($finishKey, $completedMap)) {
                    $completedMap[$finishKey]++;
                }
            }

            $labels          = [];
            $submittedSeries = [];
            $completedSeries = [];

            foreach ($submittedMap as $key => $count) {
                $labels[] = $groupBy === 'month'
                    ? \Carbon\Carbon::createFromFormat('Y-m', $key)->format($labelFormat)
                    : \Carbon\Carbon::createFromFormat('Y-m-d', $key)->format($labelFormat);
                $submittedSeries[] = $count;
                $completedSeries[] = $completedMap[$key];
            }

            return response()->json([
                'code' => 200,
                'data' => [
                    'labels'    => $labels,
                    'submitted' => $submittedSeries,
                    'completed' => $completedSeries,
                    'group_by'  => $groupBy,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/top-divisi
     * Top 10 divisi berdasarkan akumulasi jumlah tiket yang diajukan.
     */
    public function kpiTopDivisi(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);
            $limit = (int) $request->input('limit', 10);

            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->with('Requestor.KaryawanNIK.KaryawanJabatanOne.Divisi')
                ->get();

            $grouped = [];
            foreach ($tickets as $t) {
                $divisi = $t->Requestor?->KaryawanNIK?->KaryawanJabatanOne?->Divisi?->nama_divisi ?? 'Tanpa Divisi';
                $grouped[$divisi] = ($grouped[$divisi] ?? 0) + 1;
            }
            arsort($grouped);
            $top = array_slice($grouped, 0, $limit, true);

            return response()->json([
                'code' => 200,
                'data' => [
                    'labels' => array_keys($top),
                    'values' => array_values($top),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/top-module
     * Top 10 modul/sub-modul berdasarkan jumlah tiket.
     */
    public function kpiTopModule(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);
            $limit = (int) $request->input('limit', 10);
            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->with([
                    'ticketSubMenu.ticketMenu',
                ])
                ->get();
            $grouped = [];
            foreach ($tickets as $ticket) {
                $subMenu = $ticket->ticketSubMenu?->nama_sub_menu;
                $menu = $ticket->ticketSubMenu?->ticketMenu?->nama_menu;
                if ($subMenu && $menu) {
                    $label = "{$subMenu} ({$menu})";
                } elseif ($subMenu) {
                    $label = $subMenu;
                } elseif ($menu) {
                    $label = $menu;
                } else {
                    $label = 'Lainnya';
                }
                $grouped[$label] = ($grouped[$label] ?? 0) + 1;
            }
            arsort($grouped);
            $top = array_slice($grouped, 0, $limit, true);
            return response()->json([
                'code' => 200,
                'data' => [
                    'labels' => array_keys($top),
                    'values' => array_values($top),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/assignee-performance
     * Tabel monitoring performa PIC/assignee: total ditugaskan, selesai,
     * rata-rata waktu penyelesaian, dan persentase tepat waktu (SLA).
     */
    public function kpiAssigneePerformance(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);

            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->with('difficulty')
                ->whereNotNull('pic_name')
                ->where('pic_name', '!=', '-')
                ->get(['id', 'pic_name', 'ticket_status', 'finish_at', 'created_at', 'pic_started_at', 'actual_hours', 'expected_hours', 'overrun_flag']);

            $sessionMinutesMap = $this->sessionMinutesForTickets($tickets->pluck('id'));

            $grouped = [];
            foreach ($tickets as $t) {
                $pic = $t->pic_name;
                if (!isset($grouped[$pic])) {
                    $grouped[$pic] = [
                        'pic_name'      => $pic,
                        'assigned'      => 0,
                        'completed'     => 0,
                        'total_minutes' => 0,
                        'on_time'       => 0,
                        'sla_eligible'  => 0,
                    ];
                }
                $grouped[$pic]['assigned']++;

                if (
                    in_array($t->ticket_status, $this->picDoneStatuses(), true)
                    && ($t->actual_hours !== null || $t->finish_at !== null)
                ) {
                    $grouped[$pic]['completed']++;
                    $minutes = $this->resolveActualMinutes($t, $sessionMinutesMap) ?? 0.0;
                    $grouped[$pic]['total_minutes'] += $minutes;

                    $expectedHours = $t->expected_hours ?? $t->difficulty?->expected_hours;
                    if ($expectedHours !== null) {
                        $grouped[$pic]['sla_eligible']++;
                        $onTime = ((float) $minutes <= (float) $expectedHours);
                        if ($onTime) {
                            $grouped[$pic]['on_time']++;
                        }
                    }
                }
            }

            $result = collect($grouped)->map(function ($row) {
                $row['avg_hours']       = $row['completed'] > 0 ? round($row['total_minutes'] / $row['completed'], 2) : null;
                $row['on_time_pct']     = $row['sla_eligible'] > 0 ? round(($row['on_time'] / $row['sla_eligible']) * 100, 1) : null;
                $row['completion_rate'] = $row['assigned'] > 0 ? round(($row['completed'] / $row['assigned']) * 100, 1) : 0;
                unset($row['total_minutes']);
                return $row;
            })->sortByDesc('completed')->values();
            // dd($result);
            return response()->json(['code' => 200, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/pic-sla-detail
     * Rincian tiket & performa SLA untuk PIC tertentu dalam periode filter.
     */
    public function kpiPicSlaDetail(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);
            $picName = $request->input('pic_name');

            if (!$picName) {
                return response()->json(['code' => 400, 'message' => 'Nama PIC wajib diisi.'], 400);
            }

            $tickets = $this->kpiBaseQuery($request, $start, $end)
                ->with(['ticketMenu', 'ticketSubMenu', 'difficulty'])
                ->where('pic_name', $picName)
                ->orderBy('created_at', 'desc')
                ->get();

            $statusMap = [
                'T00' => 'Draft',
                'T01' => 'Menunggu Persetujuan',
                'T02' => 'Disetujui',
                'T03' => 'Ditugaskan',
                'T04' => 'Sedang Dikerjakan',
                'T05' => 'Menunggu Respon Pengaju',
                'T06' => 'Selesai',
                'T07' => 'Feedback Pengaju',
                'T08' => 'Dibatalkan / Ditolak',
                'T09' => 'Disetujui Atasan 1',
                'T10' => 'Disetujui Atasan 2',
                'T11' => 'Menunggu Divalidasi',
                'T12' => 'Menunggu Pemeriksaan',
                'T13' => 'Menunggu Ditugaskan',
                'T14' => 'Pending',
                'T15' => 'Ditutup Otomatis oleh Sistem',
            ];

            $sessionMinutesMap = $this->sessionMinutesForTickets($tickets->pluck('id'));

            $totalAssigned = $tickets->count();
            $totalCompleted = 0;
            $totalActualMinutes = 0;
            $totalExpectedMinutes = 0;
            $expectedCount = 0;
            $onTimeCount = 0;
            $slaEligibleCount = 0;

            $items = [];
            foreach ($tickets as $t) {
                // is_closed: benar-benar sudah di-close/dikonfirmasi pengaju (T06) -> untuk label/tampilan.
                // isPicDone: pengerjaan PIC sudah selesai (T05 atau T06) -> dipakai utk hitung KPI,
                // supaya tiket yang PIC-nya sudah selesai tapi masih "Menunggu Respon Pengaju"
                // tidak nyangkut jadi "Dalam Proses" dan menurunkan SLA/completion rate PIC.
                $isClosed  = ($t->ticket_status === TicketHead::STATUS_CLOSED);
                $isPicDone = in_array($t->ticket_status, $this->picDoneStatuses(), true);

                $actual = null;
                if ($isPicDone && ($t->actual_hours !== null || $t->finish_at !== null || isset($sessionMinutesMap[$t->id]))) {
                    $totalCompleted++;
                    $actual = $this->resolveActualMinutes($t, $sessionMinutesMap);
                    $totalActualMinutes += (float) $actual;
                }

                $expected = $t->expected_hours ?? $t->difficulty?->expected_hours;
                $variance = null;
                $slaStatus = '-';

                if ($expected !== null) {
                    $expected = (float) $expected;
                    if ($isPicDone && $actual !== null) {
                        $totalExpectedMinutes += $expected;
                        $expectedCount++;
                        $slaEligibleCount++;
                        $variance = round($actual - $expected, 2);
                        $onTime = ($actual <= $expected);
                        if ($onTime) {
                            $onTimeCount++;
                            $slaStatus = 'On Time';
                        } else {
                            $slaStatus = 'Overrun';
                        }
                    } else {
                        $slaStatus = 'Dalam Proses';
                    }
                } else {
                    $slaStatus = $isPicDone ? 'Tanpa Target' : 'Dalam Proses';
                }

                $items[] = [
                    'id'               => $t->id,
                    'nomor_ticket'     => $t->nomor_ticket ?? '-',
                    'menu_name'        => $t->ticketMenu?->nama_menu ?? '-',
                    'submenu_name'     => $t->ticketSubMenu?->nama_sub_menu ?? '-',
                    'ticket_status'    => $t->ticket_status,
                    'status_label'     => $statusMap[$t->ticket_status] ?? $t->ticket_status,
                    'is_closed'        => $isClosed,
                    'is_pic_done'      => $isPicDone,
                    'created_at'       => $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '-',
                    'finish_at'        => $t->finish_at ? \Carbon\Carbon::parse($t->finish_at)->format('d/m/Y H:i') : ($t->closed_at ? \Carbon\Carbon::parse($t->closed_at)->format('d/m/Y H:i') : '-'),
                    'actual_minutes'   => $actual !== null ? round($actual, 2) : null,
                    'expected_minutes' => $expected !== null ? round($expected, 2) : null,
                    'variance_minutes' => $variance,
                    'sla_status'       => $slaStatus,
                ];
            }
            $completionRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 1) : 0;
            $avgActualMinutes = $totalCompleted > 0 ? round($totalActualMinutes / $totalCompleted, 2) : null;
            $avgExpectedMinutes = $expectedCount > 0 ? round($totalExpectedMinutes / $expectedCount, 2) : null;
            $slaOnTimePct = $slaEligibleCount > 0 ? round(($onTimeCount / $slaEligibleCount) * 100, 1) : null;
            $overrunCount = max(0, $slaEligibleCount - $onTimeCount);
            return response()->json([
                'code' => 200,
                'data' => [
                    'pic_info' => [
                        'pic_name'               => $picName,
                        'total_assigned'         => $totalAssigned,
                        'total_completed'        => $totalCompleted,
                        'completion_rate'        => $completionRate,
                        'total_actual_minutes'   => round($totalActualMinutes, 2),
                        'avg_actual_minutes'     => $avgActualMinutes,
                        'total_expected_minutes' => round($totalExpectedMinutes, 2),
                        'avg_expected_minutes'   => $avgExpectedMinutes,
                        'sla_eligible_count'     => $slaEligibleCount,
                        'on_time_count'          => $onTimeCount,
                        'overrun_count'          => $overrunCount,
                        'sla_ontime_pct'         => $slaOnTimePct,
                        'period'                 => [
                            'start' => $start->format('d/m/Y'),
                            'end'   => $end->format('d/m/Y'),
                        ],
                    ],
                    'tickets' => $items,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /ticket/report/kpi/status-distribution
     * Data untuk donut chart distribusi status tiket dalam periode terpilih.
     */
    public function kpiStatusDistribution(Request $request)
    {
        try {
            [$start, $end] = $this->resolveKpiDateRange($request);
            $tickets = $this->kpiBaseQuery($request, $start, $end)->get(['ticket_status', 'approval_status']);

            $labelMap = [
                'T00' => 'Draft',
                'T01' => 'Menunggu Persetujuan',
                'T02' => 'Disetujui',
                'T03' => 'Ditugaskan',
                'T04' => 'Dikerjakan',
                'T05' => 'Menunggu Respon Peminta',
                'T06' => 'Selesai',
                'T07' => 'Respon Balasan Peminta',
                'T08' => 'Dibatalkan / Ditolak',
                'T09' => 'Disetujui Atasan 1',
                'T10' => 'Disetujui Atasan 2',
                'T11' => 'Menunggu Divalidasi',
                'T12' => 'Menunggu Pemeriksaan',
                'T13' => 'Menunggu Ditugaskan',
                'T14' => 'Pending',
                'T15' => 'Ditutup Otomatis oleh Sistem',
            ];

            $grouped = [];
            foreach ($tickets as $t) {
                $status = ($t->approval_status === 'rejected') ? 'T08' : $t->ticket_status;
                $label  = $labelMap[$status] ?? $status;
                $grouped[$label] = ($grouped[$label] ?? 0) + 1;
            }
            arsort($grouped);

            return response()->json([
                'code' => 200,
                'data' => [
                    'labels' => array_keys($grouped),
                    'values' => array_values($grouped),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTicketDetail($id)
    {
        try {
            $ticket = TicketHead::with([
                'ticketMenu',
                'ticketSubMenu',
                'difficulty',
                'ticketDetails.subDetails',
                'ticketDetails.referenceTicketDetail',
                'Requestor.KaryawanNIK.PenempatanKerja.Holding',
                'Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen',
                'Requestor.KaryawanNIK.KaryawanJabatanOne.Divisi',
                'Requestor.KaryawanNIK.KaryawanJabatanOne.Bagian',
                'Requestor.KaryawanNIK.KaryawanJabatanOne.Jabatan',
                'Requestor.KaryawanNIK.Divisi',
                'Requestor.KaryawanNIK.Bagian',
                'Requestor.KaryawanNIK.Jabatan',
                'Requestor.Karyawan.PenempatanKerja.Holding',
                'Requestor.Karyawan.KaryawanJabatanOne.Departemen',
                'Requestor.Karyawan.KaryawanJabatanOne.Divisi',
                'Requestor.Karyawan.KaryawanJabatanOne.Bagian',
                'Requestor.Karyawan.KaryawanJabatanOne.Jabatan',
                'Requestor.Karyawan.Divisi',
                'Requestor.Karyawan.Bagian',
                'Requestor.Karyawan.Jabatan',
                'attachments',
                'approvals.karyawan',
                'approvals.branchSubMenu',
                'approvals.dependencyRule',
                'executionLogs',
                'comments.karyawan',
                'comments.jabatan.Jabatan',
                'parentTicket.ticketSubMenu',
                'rootTicket.ticketSubMenu',
                'generatedTickets.ticketMenu',
                'generatedTickets.ticketSubMenu',
                'generatedTickets.Requestor',
            ])->findOrFail($id);

            return response()->json([
                'code' => 200,
                'data' => $ticket,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 404,
                'message' => 'Detail tiket tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Cancel a draft generated ticket from the Admin Report view.
     */
    public function cancelGeneratedDraft(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'code'    => 401,
                    'message' => 'Sesi Anda telah berakhir, silakan login kembali.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code'    => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $draftTicket = TicketHead::find($id);
            if (!$draftTicket) {
                return response()->json([
                    'code'    => 404,
                    'message' => 'Tiket draft tidak ditemukan.',
                ], 404);
            }

            $generatedService = app(\App\Services\GeneratedTicketService::class);
            $result = $generatedService->cancelGeneratedDraft($draftTicket, Auth::user(), $request->input('reason', ''));

            if (!$result['success']) {
                return response()->json([
                    'code'    => 422,
                    'message' => $result['message'],
                ], 422);
            }

            return response()->json([
                'code'    => 200,
                'message' => $result['message'],
                'data'    => $result['parentTicket'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => 'Gagal membatalkan tiket draft: ' . $e->getMessage(),
            ], 500);
        }
    }
}
