<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use ParagonIE\Sodium\Core\Curve25519\H;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        return view('admin.role.index', [
            'title' => 'Role Management',
            'holding' => $holding,
            'menus' => $menus
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table = Role::with(['menus' => function ($q) {
            $q->orderBy('name', 'asc');
        }])
            ->orderBy('role_name', 'asc')
            ->get();
        if (request()->ajax()) {
            return datatables()->of($table)
                ->addColumn('role_name', function ($row) use ($holding) {
                    if ($row->role_name != null) {
                        $role_name = $row->role_name;
                    } else {
                        $role_name = '-';
                    }
                    return $role_name;
                })
                ->addColumn('kategori', function ($row) use ($holding) {
                    if ($row->kategori == 'mobile') {
                        $kategori = '<span class="badge bg-label-info">' . strtoupper($row->kategori)  . '</span>';
                    } else {
                        $kategori = '<span class="badge bg-label-success">' . strtoupper($row->kategori) . '</span>';
                    }
                    return $kategori;
                })
                ->addColumn('description', function ($row) use ($holding) {
                    if ($row->role_description != null) {
                        $role_description = $row->role_description;
                    } else {
                        $role_description = '-';
                    }
                    return $role_description;
                })
                ->addColumn('list_menu', function ($row) use ($holding) {
                    $menus = $row->menus->pluck('name')->toArray();
                    $menuList = '';
                    foreach ($menus as $menu) {
                        $menuList .= '<li>' . $menu . ', ' . '</li>';
                    }
                    return '<ul>' . $menuList . '</ul>';
                })

                ->addColumn('action', function ($row) use ($holding) {
                    $btn = '<button  data-id="' . $row->id . '" data-role_name="' . $row->role_name . '" data-description="' . $row->role_description . '" data-kategori="' . $row->kategori . '" class="btn_edit_role btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i></button>';
                    $btn = $btn . '<button id="btn_delete_role" data-id="' . $row->id . '"  class="btn btn-danger btn-sm"><i class="mdi mdi-trash-can"></i></button>';
                    return $btn;
                })
                ->rawColumns([
                    'role_name',
                    'kategori',
                    'description',
                    'action',
                    'list_menu',
                ])
                ->make(true);
        }
    }
    public function datatable_menu($kategori, $holding)
    {
        $menus = Menu::with(['children' => function ($q) {
            $q->orderBy('name', 'asc');
        }])
            ->whereNull('parent_id')
            ->where('kategori', $kategori)
            ->orderBy('name', 'asc')
            ->get();
        // dd($menus);

        // flatten tree supaya bisa dipakai DataTables
        $result = [];

        foreach ($menus as $parent) {
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
                // indent child
                return $row['level'] == 0
                    ? '<strong>' . $row['name'] . '</strong>'
                    : '&nbsp;&nbsp;&nbsp;↳ ' . $row['name'];
            })
            ->addColumn('kategori', function ($row) {
                return $row['kategori'] ?? '-';
            })
            ->addColumn('option', function ($row) {

                // 🔴 parent tidak ada checkbox

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
                        </div>';
                }
            })
            ->rawColumns(['nama_menu', 'option'])
            ->make(true);
    }
    public function datatable_menu_update($id, $kategori, $holding)
    {
        $menus = Menu::with('children')
            ->whereNull('parent_id')
            ->where('kategori', $kategori)
            ->orderBy('name')
            ->get();
        // dd($menus);

        // flatten tree supaya bisa dipakai DataTables
        $result = [];

        foreach ($menus as $parent) {
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
                // indent child
                return $row['level'] == 0
                    ? '<strong>' . $row['name'] . '</strong>'
                    : '&nbsp;&nbsp;&nbsp;↳ ' . $row['name'];
            })
            ->addColumn('kategori', function ($row) {
                return $row['kategori'] ?? '-';
            })
            ->addColumn('option', function ($row) use ($id) {

                // 🔴 parent tidak ada checkbox

                $checked = RoleMenu::where('role_id', $id)
                    ->where('menu_id', $row['id'])
                    ->exists()
                    ? 'checked'
                    : '';

                if ($row['level'] == 0) {
                    return '
                        <div class="form-check">
                            <input class="form-check-input parent_update-checkbox"
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
                            <input class="form-check-input child_update-checkbox"
                                type="checkbox"
                                data-parent="' . $row['parent_id'] . '"
                                name="menu_id[]"
                                value="' . $row['id'] . '"
                                ' . $checked . '>
                            <label class="form-check-label">Buka Access</label>
                        </div>';
                }
            })
            ->rawColumns(['nama_menu', 'option'])
            ->make(true);
    }
    public function menuTree($roleId, $holding)
    {

        $role = Role::where('id', $roleId)->first();
        $menus = Menu::with('children')
            ->where('kategori', $role->kategori)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $roleMenus = RoleMenu::where('role_id', $roleId)
            ->pluck('menu_id')
            ->toArray();

        return view('admin.role.menu-tree', compact('menus', 'roleMenus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function role_save_add(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'role_name' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'role_description' => 'nullable|string|max:500',
        ]);

        $role = Role::create([
            'role_name' => $request->role_name,
            'kategori' => $request->kategori,
            'role_description' => $request->role_description,
        ]);
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            // 'menu_id' => $request->menu_id,
            'menu_name' => 'Master Role',
            // 'id_table' => $approval->id_approval,
            // 'holding_id' => $holding->id,
            // 'holding_category' => $holding->holding_category,
            'table' => 'roles',
            'activity' => 'create',
            'description' => 'Menambahkan Role ' .  $request->role_name . ' Kategori ' . $request->kategori,
            'keterangan' => 'Success',
        ]);
        if ($request->has('menu_id')) {
            // dd($request->has('menu_id'), $request->all());
            $menuData = [];
            foreach ($request->menu_id as $menuId) {
                $menuData[$menuId] = [
                    'can_view' => in_array('can_view_' . $menuId, $request->all()) ? 1 : 0,
                    'can_create' => in_array('can_create_' . $menuId, $request->all()) ? 1 : 0,
                    'can_edit' => in_array('can_edit_' . $menuId, $request->all()) ? 1 : 0,
                    'can_delete' => in_array('can_delete_' . $menuId, $request->all()) ? 1 : 0,
                ];
            }
            $role->menus()->sync($menuData);
        }

        return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
    }
    public function role_save_update(Request $request)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(), [
            'role_name_update' => 'required|string|max:255',
            'role_description_update' => 'nullable|string|max:500',
        ], [
            'role_name_update.required' => 'Nama role harus diisi.',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        // $delete = Role::where('id', $request->id_role)->delete();
        $delete = RoleMenu::where('role_id', $request->id_role)->delete();
        $role = Role::findOrFail($request->id_role);

        $role->update([
            'role_name' => $request->role_name_update,
            'role_description' => $request->role_description_update,
        ]);
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            // 'menu_id' => $request->menu_id,
            'menu_name' => 'Master Role',
            'id_table' => $request->id_role,
            // 'holding_id' => $holding->id,
            // 'holding_category' => $holding->holding_category,
            'table' => 'roles',
            'activity' => 'update',
            'description' => 'Mengedit Role ' .  $request->role_name_update . ' Kategori ' . $role->kategori,
            'keterangan' => 'Success',
        ]);

        if ($request->has('menu_id')) {
            $menuData = [];
            foreach ($request->menu_id as $menuId) {
                $menuData[$menuId] = [
                    'can_view' => in_array('can_view_' . $menuId, $request->all()) ? 1 : 0,
                    'can_create' => in_array('can_create_' . $menuId, $request->all()) ? 1 : 0,
                    'can_edit' => in_array('can_edit_' . $menuId, $request->all()) ? 1 : 0,
                    'can_delete' => in_array('can_delete_' . $menuId, $request->all()) ? 1 : 0,
                ];
            }
            // dd($menuData, $request->all());
            $role->menus()->sync($menuData);
        }

        return redirect()->back()->with('success', 'Role berhasil Diubah.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
