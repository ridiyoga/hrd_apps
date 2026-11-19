<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuAccessController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        // $role = RoleUsers::all();
        // $karyawan = User::with('Karyawan')->where('is_admin', 'admin')->get();
        // dd($role, $karyawan);
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
        return view('admin.karyawan.hak_akses.menu.index', [
            'menus' => $menus,
            'holding' => $holding,
        ]);
    }
    public function get_datatable($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table = Role::with(['menus' => function ($q) {
            $q->orderBy('sort_order', 'ASC');
        }])
            ->with(['roleUsers' => function ($q) {
                $q->with('Users');
            }])

            ->get();
        // dd($table);
        return datatables()->of($table)
            ->addColumn('total_karyawan', function ($row) {
                $user = User::with('roleUsers')->whereHas('KaryawanNIK')->whereHas('roleUsers', function ($q) use ($row) {
                    $q->where('role_menu_id', $row->id);
                })->count();
                $total = $user . ' User';
                return $total;
            })
            ->addColumn('kategori', function ($row) {
                $kategori = '<span class="badge bg-label-primary">' . $row->kategori . '</span>';
                return $kategori;
            })
            ->addColumn('option', function ($row) {
                $btn = '<button  id="btn_hak_akses_karyawan" data-id="' . $row->id . '" class="btn btn-sm btn-primary btn-icon-text btn-edit-data" ><i class="mdi mdi-account-key btn-icon-prepend"></i></button>';
                return $btn;
            })
            ->rawColumns(['option', 'kategori', 'total_karyawan'])
            ->make(true);
    }
    public function karyawan_datatable($id, $holding)
    {
        // dd($id, $holding);
        $holding = Holding::where('holding_code', $holding)->first();
        $table = User::with(['roleUsers' => function ($q) use ($id) {
            $q->where('role_menu_id', $id);
            $q->with('Users');
            $q->with('roleMenu');
        }])->with('KaryawanNIK')
            ->whereHas('KaryawanNIK', function ($query) {
                $query->where('kategori', 'Karyawan Bulanan');
                $query->where('status_aktif', 'AKTIF');
            })
            // ->where('username', 'cerdasbadrus')
            // ->where('is_admin', 'hrd')
            // ->limit('5')
            ->get();

        // dd($table, $id);
        return datatables()->of($table)
            ->addColumn('username', function ($row) {
                return $row->username;
            })
            ->addColumn('nama_karyawan', function ($row) {
                if ($row->KaryawanNIK == NULL) {
                    $nama_karyawan = $row->username;
                } else {
                    $nama_karyawan = $row->KaryawanNIK->name;
                }
                return $nama_karyawan;
            })
            ->addColumn('shift', function ($row) {
                if ($row->KaryawanNIK->shift == 'SHIFT') {
                    $shift = $row->KaryawanNIK->shift;
                } else {
                    $shift = 'NON';
                }
                return $shift;
            })
            ->addColumn('nama_departemen', function ($row) {
                if ($row->KaryawanNIK == null || $row->KaryawanNIK == '') {
                    $departemen = '-';
                } else {
                    if ($row->KaryawanNIK->KaryawanJabatan == null || $row->KaryawanNIK->KaryawanJabatan == '') {
                    } else {
                        $departemen = '<ul>';
                        $printedDepartemen = []; // penanda departemen yang sudah ditampilkan

                        if ($row->KaryawanNIK->KaryawanJabatan && $row->KaryawanNIK->KaryawanJabatan->count()) {
                            foreach ($row->KaryawanNIK->KaryawanJabatan as $data) {

                                $namaDepartemen = $data->Departemen->nama_departemen ?? null;

                                // SKIP jika nama departemen sama dengan sebelumnya / sudah ada
                                if (!$namaDepartemen || in_array($namaDepartemen, $printedDepartemen)) {
                                    continue;
                                }

                                $printedDepartemen[] = $namaDepartemen;

                                $main = ($data->main == 1)
                                    ? '<span class="badge bg-label-success ms-1">Utama</span>'
                                    : '';
                                $strong = ($data->main == 1)
                                    ? 'style="font-weight: bold;"'
                                    : '';
                                $departemen .= '<li ' . $strong . '>' . e($namaDepartemen) . $main . '</li>';
                            }

                            $departemen .= '</ul>';
                        } else {
                            $departemen = '-';
                        }
                    }
                }
                return $departemen;
            })
            ->addColumn('nama_jabatan', function ($row) {
                if ($row->KaryawanNIK == null || $row->KaryawanNIK == '') {
                    $jabatan = '-';
                } else {
                    if ($row->KaryawanNIK->KaryawanJabatan == null || $row->KaryawanNIK->KaryawanJabatan == '') {
                        $jabatan = '-';
                    } else {
                        $jabatan = '<ul>';
                        $printedJabatan = []; // penanda jabatan yang sudah ditampilkan

                        if ($row->KaryawanNIK->KaryawanJabatan && $row->KaryawanNIK->KaryawanJabatan->count()) {
                            foreach ($row->KaryawanNIK->KaryawanJabatan as $data) {

                                $namaJabatan = $data->Jabatan->nama_jabatan ?? null;
                                // SKIP jika nama jabatan sama dengan sebelumnya / sudah ada
                                if (!$namaJabatan || in_array($namaJabatan, $printedJabatan)) {
                                    continue;
                                }

                                $printedJabatan[] = $namaJabatan;

                                $main = ($data->main == 1)
                                    ? '<span class="badge bg-label-success ms-1">Utama</span>'
                                    : '';
                                $strong = ($data->main == 1)
                                    ? 'style="font-weight: bold;"'
                                    : '';
                                $jabatan .= '<li ' . $strong . '>' . e($namaJabatan) . $main . '</li>';
                            }

                            $jabatan .= '</ul>';
                        } else {
                            $jabatan = '-';
                        }
                    }
                }
                return $jabatan;
            })
            ->addColumn('option', function ($row) use ($id) {
                $uniqueId = 'menu_id_' . $row->id;
                $get_user = RoleUsers::where('role_user_id', $row->id)->where('role_menu_id', $id)->first();
                if ($get_user == NULL) {
                    $checked = '';
                } else {
                    $checked = 'checked';
                }
                $btn = '<input id="' . $uniqueId . '" name="menu_id[]" data-id="' . $row->id . '" value="' . $row->id . '" class="form-check-input" ' . $checked . ' type="checkbox"></input>';
                return $btn;
            })
            ->rawColumns([
                'option',
                'nama_karyawan',
                'shift',
                'nama_departemen',
                'nama_jabatan'
            ])
            ->make(true);
    }
    public function save_access($holding, Request $request)
    {
        try {
            $role = Role::where('id', $request->id)->first();
            // dd($role);
            if ($request->menu == null) {
                return response()->json([
                    'code' => 500,
                    'error' => 'Karyawan Tidak Ada'
                ]);
            }
            RoleUsers::where('role_menu_id', $request->id)->delete();
            foreach ($request->menu as $menu) {
                // $role_user = new RoleUsers();
                // $role_user->role_user_id = $menu;
                // $role_user->role_menu_id = $request->id;
                // $role_user->save();
                RoleUsers::create([
                    'role_user_id' => $menu,
                    'role_menu_id' => $request->id,
                ]);
            }
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Akses Menu',
                // 'id_table' => $approval->id_approval,
                // 'holding_id' => $holding->id,
                // 'holding_category' => $holding->holding_category,
                'table' => 'roles',
                'activity' => 'create',
                'description' => 'Mengedit hak akses pada kelompok: ' .  $role->role_name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Sukses Menambahkan Akses'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function hak_akses_karyawan($id)
    {
        return view('admin.menu_access.hak_akses_karyawan', compact('id'));
    }
}
