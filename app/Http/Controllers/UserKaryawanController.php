<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\KaryawanImport;
use App\Imports\UsersImport;
use App\Imports\UserUpdateImport;
use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalUser;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\UserNonActive;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class UserKaryawanController extends Controller
{
    public function index_users($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();

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
        return view('admin.karyawan.index_users', [
            // return view('karyawan.index', [
            'title' => 'Karyawan',
            'menus' => $menus,
            'holding' => $holding,
        ]);
    }


    public function index_finger(Request $request, $holding)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        $karyawan = Karyawan::where('karyawans.status_aktif', 'AKTIF')
            ->where('karyawans.kontrak_kerja', $holding->holding_name)
            ->orderBy('karyawans.name', 'ASC')
            ->get();
        $menus = Menu::with(['children' => function ($query) {
            $query->with('subchildren');
        }])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
        $data_user = User::Join('karyawans', 'users.karyawan_id', 'karyawans.id')->where('kontrak_kerja', $holding)->where('user_aktif', 'AKTIF')->get();
        return view('admin.karyawan.index_finger', [
            // return view('karyawan.index', [
            'title' => 'Karyawan',
            "data_departemen" => Departemen::orderBy('nama_departemen', 'ASC')->where('holding', $holding->id)->get(),
            'holding' => $holding,
            'menus' => $menus,
            'karyawan' => $karyawan,
            'data_user' => $data_user,
            "data_jabatan" => Jabatan::orderBy('nama_jabatan', 'ASC')->where('holding', $holding)->get(),
            "data_lokasi" => Lokasi::orderBy('nama_lokasi', 'ASC')->get(),
            "karyawan_laki" => User::Join('karyawans', 'users.karyawan_id', 'karyawans.id')->where('karyawans.gender', 'Laki-Laki')->where('kontrak_kerja', $holding)->where('user_aktif', 'AKTIF')->count(),
            "karyawan_perempuan" => User::Join('karyawans', 'users.karyawan_id', 'karyawans.id')->where('karyawans.gender', 'Perempuan')->where('kontrak_kerja', $holding)->where('user_aktif', 'AKTIF')->count(),
            "karyawan_office" => User::Join('karyawans', 'users.karyawan_id', 'karyawans.id')->where('karyawans.kategori', 'Karyawan Bulanan')->where('kontrak_kerja', $holding)->where('user_aktif', 'AKTIF')->count(),
            "karyawan_shift" => User::Join('karyawans', 'users.karyawan_id', 'karyawans.id')->where('karyawans.kategori', 'Karyawan Harian')->where('kontrak_kerja', $holding)->where('user_aktif', 'AKTIF')->count(),
        ]);
    }
    public function prosesTambahUser(Request $request)
    {
        // dd($request->all());
        $rules = [
            'karyawan_user'              => 'required',
            'username'              => 'required|min:4|unique:users,username|regex:/^[A-Za-z0-9._-]+$/',
            'level'                 => 'required',
            'password_confirmation' => 'required',
            'password'              => [
                'required',
                'min:6',
                'confirmed',
                // Password::min(6)->mixedCase()->numbers()
            ],
        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique'   => ':attribute sudah digunakan.',
            'min'      => ':attribute terlalu pendek.',
            'password.min'      => 'Password minimal 6 karakter',
            'password.letters'  => 'Password harus mengandung huruf',
            'password.numbers'  => 'Password harus mengandung angka',
            'password.mixed'    => 'Password harus mengandung huruf dan angka',
            'password.mixedCase'    => 'Password harus mengandung huruf besar dan kecil',
            'password.symbols'  => 'Password harus mengandung simbol',
            'password_confimation.required' => 'Konfirmasi Password Tidak Boleh Kosong',
            'password.confirmed' => 'Konfirmasi Password Tidak Cocok',
            'password.uncompromised' => 'Password Tidak Boleh Sama Dengan Password Terlama',
            'karyawan_user.required' => 'Karyawan tidak boleh kosong.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik (.), underscore (_) dan strip (-).',
        ];

        try {
            $validasi = Validator::make($request->all(), $rules, $customMessages);
            if ($validasi->fails()) {
                $errors = $validasi->errors();
                return response()->json([
                    'code' => 500,
                    'message' => $errors
                ]);
            }
            $karyawan = Karyawan::where('nomor_identitas_karyawan', $request->karyawan_user)->first();
            $user                = new User();
            $user->karyawan_id   = $request->karyawan_user;
            $user->username      = $request->username;
            $user->password      = Hash::make($request->password);
            $user->password_show = $request->password;
            $user->is_admin      = $request->level;
            $user->id_finger     = $karyawan->id_finger;
            $user->id_karyawan   = $karyawan->id;
            $user->user_aktif    = 'AKTIF';
            $user->save();

            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Master Approval',
                'id_table' => $user->karyawan_id,
                // 'holding_id' => $holding->id,
                // 'holding_category' => $holding->holding_category,
                'table' => 'master_approval_group',
                'activity' => 'update',
                'description' => 'Menambahkan User, ' .  $user->username,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil Menambahkan User',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function datatable_users_bulanan(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();


        $table = User::with(['KaryawanNIK' => function ($query) use ($holding) {
            $query->with(['KaryawanJabatan' => function ($query) use ($holding) {
                $query->with('Jabatan');
                $query->with('Divisi');
                $query->with('Departemen');
                $query->with('Bagian');
            }]);
            $query->where('status_aktif', 'AKTIF')
                ->orderBy('name', 'ASC');
        }])
            // ->where('username', 'admin')
            ->orderBy('username', 'ASC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('name', function ($row) use ($holding) {
                    if ($row->KaryawanNIK == null || $row->KaryawanNIK == '') {
                        $name = '-';
                        $nama_karyawan = '-';
                    } else {
                        if ($row->KaryawanNIK->status_aktif == 'NON AKTIF') {
                            $name = '<span style="color:red; text-decoration: line-through;">' . $row->KaryawanNIK->name . '</span>';
                            $name = $name . '<br><span class="badges bg-label-danger">NON AKTIF</span>';
                        } else {
                            $name = '<a href=../karyawan/shift/' . $row->KaryawanNIK->id . '/' . $holding . '>
                            <span class="badge bg-label-success">' . $row->KaryawanNIK->name . '</span>
                            </a>';
                        }
                        $nama_karyawan = $row->KaryawanNIK->name;
                    }
                    $row->nama_karyawan = $nama_karyawan;
                    return $name;
                })
                ->addColumn('nomor_identitas_karyawan', function ($row) use ($holding) {
                    if ($row->KaryawanNIK == null || $row->KaryawanNIK == '') {
                        $nomor_identitas_karyawan = '-';
                    } else {
                        if ($row->KaryawanNIK->status_aktif == 'NON AKTIF') {
                            $nomor_identitas_karyawan = '<span style="color:red; text-decoration: line-through;">' . $row->KaryawanNIK->nomor_identitas_karyawan . '</span>';
                            $nomor_identitas_karyawan = $nomor_identitas_karyawan . '<br><span class="badges bg-label-danger">NON AKTIF</span>';
                        } else {
                            $nomor_identitas_karyawan = $row->KaryawanNIK->nomor_identitas_karyawan;
                        }
                    }
                    return $nomor_identitas_karyawan;
                })
                ->addColumn('nama_departemen', function ($row) use ($holding) {
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
                    $row->nama_departemen = $departemen;
                    return $departemen;
                })
                ->addColumn('nama_divisi', function ($row) use ($holding) {
                    if ($row->KaryawanNIK == null || $row->KaryawanNIK == '') {
                        $divisi = '-';
                    } else {
                        if ($row->KaryawanNIK->KaryawanJabatan == null || $row->KaryawanNIK->KaryawanJabatan == '') {
                            $divisi = '-';
                        } else {
                            $divisi = '<ul>';
                            $printedDivisi = []; // penanda divisi yang sudah ditampilkan

                            if ($row->KaryawanNIK->KaryawanJabatan && $row->KaryawanNIK->KaryawanJabatan->count()) {
                                foreach ($row->KaryawanNIK->KaryawanJabatan as $data) {

                                    $namaDivisi = $data->Divisi->nama_divisi ?? null;

                                    // SKIP jika nama divisi sama dengan sebelumnya / sudah ada
                                    if (!$namaDivisi || in_array($namaDivisi, $printedDivisi)) {
                                        continue;
                                    }

                                    $printedDivisi[] = $namaDivisi;

                                    $main = ($data->main == 1)
                                        ? '<span class="badge bg-label-success ms-1">Utama</span>'
                                        : '';
                                    $strong = ($data->main == 1)
                                        ? 'style="font-weight: bold;"'
                                        : '';
                                    $divisi .= '<li ' . $strong . '>' . e($namaDivisi) . $main . '</li>';
                                }

                                $divisi .= '</ul>';
                            } else {
                                $divisi = '-';
                            }
                        }
                    }
                    $row->nama_divisi = $divisi;
                    return $divisi;
                })
                ->addColumn('nama_jabatan', function ($row) use ($holding) {
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
                    $row->nama_jabatan = $jabatan;
                    return $jabatan;
                })
                ->addColumn('user_aktif', function ($row) use ($holding) {
                    if ($row->user_aktif == '' || $row->user_aktif == '-') {
                        $user_aktif = '-';
                    } else {
                        if ($row->user_aktif == 'AKTIF') {
                            $user_aktif = '<button type="button" class="btn btn-sm btn-icon btn-outline-success waves-effect"><span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span></button>';
                        } else {
                            $user_aktif = '<button type="button" class="btn btn-sm btn-icon btn-outline-danger waves-effect"><span class="tf-icons mdi mdi-minus-circle-outline"></span></button>';
                        }
                    }
                    return $user_aktif;
                })
                ->addColumn('status', function ($row) use ($holding) {
                    if ($row->user_aktif == '' || $row->user_aktif == '-') {
                        $status = '-';
                    } else if ($row->user_aktif == 'AKTIF') {
                        $status = '<span class="badge bg-label-success">' . $row->user_aktif . '</span>';
                    } else if ($row->user_aktif == 'NON AKTIF') {
                        $status = '<span class="badge bg-label-danger">' . $row->user_aktif . '</span>';
                    }
                    return $status;
                })
                ->addColumn('option', function ($row) use ($holding) {
                    if ($row->Karyawan == null) {
                        $jabatan = NULL;
                        $divisi = NULL;
                        $foto = NULL;
                        $tgl_mulai_kontrak = NULL;
                        $tgl_selesai_kontrak = NULL;
                        $name = NULL;
                        $penempatan_kerja = NULL;
                        $kontrak_kerja = NULL;
                        $bagian = NULL;
                        $status_aktif = NULL;
                    } else {
                        $foto = $row->Karyawan->foto;
                        $tgl_mulai_kontrak = $row->Karyawan->tgl_mulai_kontrak;
                        $tgl_selesai_kontrak = $row->Karyawan->tgl_selesai_kontrak;
                        $name = $row->Karyawan->name;
                        $penempatan_kerja = $row->Karyawan->penempatan_kerja;
                        $kontrak_kerja = $row->Karyawan->kontrak_kerja;
                        $status_aktif = $row->Karyawan->status_aktif;
                        if ($row->Karyawan->bagian_id == '' || $row->Karyawan->bagian_id == null) {
                            $bagian = NULL;
                        } else {
                            if ($row->Karyawan->Bagian == null) {
                                $bagian = NULL;
                            } else {
                                $bagian = $row->Karyawan->Bagian->nama_bagian;
                            }
                        }
                        if ($row->Karyawan->jabatan_id == '' || $row->Karyawan->jabatan_id == null) {
                            $jabatan = NULL;
                        } else {
                            if ($row->Karyawan->Jabatan == null) {
                                $jabatan = NULL;
                            } else {
                                $jabatan = $row->Karyawan->Jabatan->nama_jabatan;
                            }
                        }
                        if ($row->Karyawan->divisi_id == '' || $row->Karyawan->divisi_id == null) {
                            $divisi = NULL;
                        } else {
                            if ($row->Karyawan->Divisi == null) {
                                $divisi = NULL;
                            } else {
                                $divisi = $row->Karyawan->Divisi->nama_divisi;
                            }
                        }
                    }
                    if ($row->user_aktif == 'AKTIF') {
                        $btn_edit = '<button type="button" id="btn_edit_karyawan" data-id="' . $row->id . '" data-username="' . $row->username . '" data-level="' . $row->is_admin . '" data-karyawan="' . $row->karyawan_id . '" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-account-edit-outline"></span></button>';
                        $btn1 = '<button type="button" id="btn_non_aktif_karyawan"  data-status_aktif="' . $status_aktif . '" data-foto="' . $foto . '" data-id="' . $row->id . '" data-tgl_mulai_kontrak="' . $tgl_mulai_kontrak . '" data-tgl_selesai_kontrak="' . $tgl_selesai_kontrak . '" data-nama="' . $name . '" data-divisi="' . $divisi . '" data-jabatan="' . $jabatan . '" data-bagian="' . $bagian . '"  data-holding="' . $holding . '" data-penempatan_kerja="' . $penempatan_kerja . '" data-kontrak_kerja="' . $kontrak_kerja . '"  class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-account-off"></span></button>';
                    } else {
                        $btn_edit = '';
                        $btn1 = '<button type="button" id="btn_aktif_karyawan"  data-status_aktif="' . $status_aktif . '" data-foto="' . $foto . '" data-id="' . $row->id . '" data-tgl_mulai_kontrak="' . $tgl_mulai_kontrak . '" data-tgl_selesai_kontrak="' . $tgl_selesai_kontrak . '" data-nama="' . $name . '" data-divisi="' . $divisi . '" data-jabatan="' . $jabatan . '" data-bagian="' . $bagian . '"  data-holding="' . $holding . '" data-penempatan_kerja="' . $penempatan_kerja . '" data-kontrak_kerja="' . $kontrak_kerja . '"  class="btn btn-icon btn-success waves-effect waves-light"><span class="tf-icons mdi mdi-account-check-outline"></span></button>';
                    }
                    $btn =  $btn_edit . $btn1;
                    return $btn;
                })
                ->rawColumns(['nama_jabatan', 'nomor_identitas_karyawan', 'name', 'user_aktif',  'nama_departemen', 'nama_divisi', 'status', 'option'])
                ->make(true);
        }
    }
    public function approval_role_option($level, $holding)
    {
        // dd($level);
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            $approval = Approval::where('level_approval', $level)->get();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $approval
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data approval: ' . $e->getMessage()
            ]);
        }
    }
    public function approval_save_add(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        try {

            // pastikan approval_role adalah array (karena multiple)
            $approvalRoles = is_array($request->approval_role)
                ? $request->approval_role
                : [$request->approval_role];

            // dd($approvalRoles);
            $get = ApprovalUser::where('karyawan_id', $request->id_karyawan_approval)->where('approval_user_id', $request->id_approval)->delete();
            foreach ($approvalRoles as $roleId) {
                // NEW INSERT
                $approvalUser = new ApprovalUser();
                $approvalUser->id_approval_user = Uuid::uuid4();
                $approvalUser->approval_user_id = $request->id_approval;
                $approvalUser->karyawan_id = $request->id_karyawan_approval;
                $approvalUser->approval_id = $roleId;
                $approvalUser->save();
            }

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Approval berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    public function users_finger_datatable(Request $request, $holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table = DB::connection('solution_access')->select('SELECT TOP 20 * FROM USERINFO');
        // dd($table);
        if (request()->ajax()) {
            $column = DataTables::of($table);
            $column->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary" onclick="edit(' . $row->USERID . ')">Edit</button>';
            });
            return $column->rawColumns(['action'])
                ->make('true');
        }
    }
    // dd($table, $holding->holding_name);
    public function role_access_datatable($id, $holding)
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
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_akses', function ($row) use ($holding) {
                    if ($row->role_name != null) {
                        $role_name = $row->role_name;
                    } else {
                        $role_name = '-';
                    }
                    return $role_name;
                })
                ->addColumn('deskripsi', function ($row) use ($holding) {
                    if ($row->role_description != null) {
                        $role_description = $row->role_description;
                    } else {
                        $role_description = '-';
                    }
                    return $role_description;
                })
                ->addColumn('list_menu', function ($row) use ($holding) {
                    if ($row->menus != null) {
                        $list_menu = '<li>' . implode('</li><li>', $row->menus->pluck('name')->toArray()) . '</li>';
                    } else {
                        $list_menu = '-';
                    }
                    return $list_menu;
                })
                ->addColumn('option', function ($row) use ($id) {
                    // 1. Definisikan ID unik
                    $uniqueId = 'menu_id_' . $row->id;
                    if ($row->roleUsers->isEmpty()) {
                        $btn = ' <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="' . $uniqueId . '" name="menu_id[]" value="' . $row->id . '"><label class="form-check-label" for="' . $uniqueId . '">Pilih</label></div>';
                    } else {
                        $get = $row->roleUsers->where('role_user_id', $id)->first();
                        if ($get == null) {
                            $btn = ' <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="' . $uniqueId . '" name="menu_id[]" value="' . $row->id . '"><label class="form-check-label" for="' . $uniqueId . '">Pilih</label></div>';
                        } else {
                            $btn = ' <div class="form-check mb-2"><input class="form-check-input" checked type="checkbox" id="' . $uniqueId . '" name="menu_id[]" value="' . $row->id . '"><label class="form-check-label" for="' . $uniqueId . '">Pilih</label></div>';
                        }
                    }
                    // 2. Gunakan ID unik untuk 'id' checkbox dan 'for' label
                    return $btn;
                })
                ->rawColumns(['nama_akses', 'option', 'deskripsi', 'list_menu'])
                ->make(true);
        }
    }
    public function datatable_users_harian(Request $request, $holding)
    {
        $holding = Holding::where('id', $holding)->first();
        $table = User::With(['Karyawan' => function ($query) use ($holding) {

            $query->with('Divisi');
            $query->with('Jabatan');
            $query->where('kontrak_kerja', $holding);
            $query->where('status_aktif', 'AKTIF');
            $query->where('kategori', 'Karyawan Harian');
        }])->Join('karyawans', 'karyawans.id', 'users.karyawan_id')
            ->where('user_aktif', 'AKTIF')
            ->where('karyawans.kategori', 'Karyawan Harian')
            ->where('karyawans.kontrak_kerja', $holding)
            ->select('users.*')
            ->orderBy('id', 'DESC')
            // ->limit(2)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('name', function ($row) use ($holding) {
                    if ($row->Karyawan == null || $row->Karyawan == '') {
                        $name = '-';
                    } else {
                        if ($row->Karyawan->status_aktif == 'NON AKTIF') {
                            $name = '<span style="text-decoration: line-through;>' . $row->Karyawan->name . '</span>';
                        } else {
                            $name = '<span>' . $row->Karyawan->name . '</span>';
                        }
                    }
                    return $name;
                })
                ->addColumn('nomor_identitas_karyawan', function ($row) use ($holding) {
                    if ($row->Karyawan == null || $row->Karyawan == '') {
                        $nomor_identitas_karyawan = '-';
                    } else {
                        $nomor_identitas_karyawan = $row->Karyawan->nomor_identitas_karyawan;
                    }
                    return $nomor_identitas_karyawan;
                })
                ->addColumn('nama_divisi', function ($row) use ($holding) {
                    if ($row->Karyawan == null || $row->Karyawan == '') {
                        $divisi = '-';
                    } else {
                        if ($row->Karyawan->divisi_id == '' || $row->Karyawan->divisi_id == null) {
                            $divisi = '-';
                        } else {
                            if ($row->Karyawan->Divisi == null) {
                                $divisi = '-';
                            } else {
                                $divisi = $row->Karyawan->Divisi->nama_divisi;
                            }
                        }
                    }
                    return $divisi;
                })
                ->addColumn('nama_jabatan', function ($row) use ($holding) {
                    if ($row->Karyawan == null || $row->Karyawan == '') {
                        $jabatan = '-';
                    } else {
                        if ($row->Karyawan->jabatan_id == '' || $row->Karyawan->jabatan_id == '-') {
                            $jabatan = '-';
                        } else {
                            if ($row->Karyawan->Jabatan == null) {
                                $jabatan = '-';
                            } else {
                                $jabatan = $row->Karyawan->Jabatan->nama_jabatan;
                            }
                        }
                    }
                    return $jabatan;
                })
                ->addColumn('akses', function ($row) use ($holding) {
                    if ($row->is_admin == '' || $row->is_admin == '-') {
                        $akses = '-';
                    } else {
                        $akses = $row->is_admin;
                    }
                    return $akses;
                })
                ->addColumn('user_aktif', function ($row) use ($holding) {
                    if ($row->user_aktif == '' || $row->user_aktif == '-') {
                        $user_aktif = '-';
                    } else {
                        $user_aktif = '<button type="button" class="btn btn-sm btn-icon btn-outline-success waves-effect"><span class="tf-icons mdi mdi-checkbox-marked-circle-outline"></span></button>';
                    }
                    return $user_aktif;
                })
                ->addColumn('status', function ($row) use ($holding) {
                    if ($row->status_aktif == '' || $row->status_aktif == '-') {
                        $status = '-';
                    } else if ($row->status_aktif == 'AKTIF') {
                        $status = '<span class="badge bg-label-success">' . $row->status_aktif . '</span>';
                    } else if ($row->status_aktif == 'NON AKTIF') {
                        $status = '<span class="badge bg-label-danger">' . $row->status_aktif . '</span>';
                    }
                    return $status;
                })
                ->addColumn('option', function ($row) use ($holding) {
                    if ($row->Karyawan == null) {
                        $jabatan = NULL;
                        $divisi = NULL;
                        $foto = NULL;
                        $tgl_mulai_kontrak = NULL;
                        $tgl_selesai_kontrak = NULL;
                        $name = NULL;
                        $penempatan_kerja = NULL;
                        $kontrak_kerja = NULL;
                        $bagian = NULL;
                    } else {
                        $foto = $row->Karyawan->foto;
                        $tgl_mulai_kontrak = $row->Karyawan->tgl_mulai_kontrak;
                        $tgl_selesai_kontrak = $row->Karyawan->tgl_selesai_kontrak;
                        $name = $row->Karyawan->name;
                        $penempatan_kerja = $row->Karyawan->penempatan_kerja;
                        $kontrak_kerja = $row->Karyawan->kontrak_kerja;
                        if ($row->Karyawan->bagian_id == '' || $row->Karyawan->bagian_id == null) {
                            $bagian = NULL;
                        } else {
                            if ($row->Karyawan->Bagian == null) {
                                $bagian = NULL;
                            } else {
                                $bagian = $row->Karyawan->Bagian->nama_bagian;
                            }
                        }
                        if ($row->Karyawan->jabatan_id == '' || $row->Karyawan->jabatan_id == null) {
                            $jabatan = NULL;
                        } else {
                            if ($row->Karyawan->Jabatan == null) {
                                $jabatan = NULL;
                            } else {
                                $jabatan = $row->Karyawan->Jabatan->nama_jabatan;
                            }
                        }
                        if ($row->Karyawan->divisi_id == '' || $row->Karyawan->divisi_id == null) {
                            $divisi = NULL;
                        } else {
                            if ($row->Karyawan->Divisi == null) {
                                $divisi = NULL;
                            } else {
                                $divisi = $row->Karyawan->Divisi->nama_divisi;
                            }
                        }
                    }
                    $btn = '<button id="btn_edit_password" data-id="' . $row->id . '" data-holding="' . $holding . '" type="button" class="btn btn-icon btn-secondary waves-effect waves-light"><span class="tf-icons mdi mdi-key-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_non_aktif_karyawan" data-foto="' . $foto . '" data-id="' . $row->id . '" data-tgl_mulai_kontrak="' . $tgl_mulai_kontrak . '" data-tgl_selesai_kontrak="' . $tgl_selesai_kontrak . '" data-nama="' . $name . '" data-divisi="' . $divisi . '" data-jabatan="' . $jabatan . '" data-bagian="' . $bagian . '"  data-holding="' . $holding . '" data-penempatan_kerja="' . $penempatan_kerja . '" data-kontrak_kerja="' . $kontrak_kerja . '"  class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-account-remove-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['nama_jabatan', 'nomor_identitas_karyawan', 'name', 'user_aktif', 'nama_divisi', 'akses', 'status', 'option'])
                ->make(true);
        }
    }
    public function access_save_add(Request $request, $holding)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $holding)->first();
        $user = User::where('id', $request->id)->first();
        // dd($request->menu_id);
        try {
            RoleUsers::where('role_user_id', $user->id)->delete();
            foreach ($request->menu as $menu_id) {
                RoleUsers::create([
                    'role_menu_id' => $menu_id,
                    'role_user_id' => $user->id,
                ]);
            }
            return response()->json(
                [
                    'message' => 'Sukses Menambahkan Akses',
                    'code' => 200
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                    'code' => 404
                ]
            );
        }
    }

    public function get_user_karyawan($holding)
    {
        try {
            $holding = Holding::where('holding_code', $holding)->first();
            $karyawan = Karyawan::where('kontrak_kerja', $holding->id)
                ->where('karyawans.status_aktif', 'AKTIF')
                ->where('kategori', 'Karyawan Bulanan')
                ->orderBy('karyawans.name', 'ASC')
                // ->limit(5)
                ->get();
            return response()->json([
                'code' => 200,
                'data' => $karyawan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            ]);
        }
    }
    public function edit_karyawan($id, $holding)
    {
        try {
            $user = User::where('id', $id)->first();
            $holding = Holding::where('holding_code', $holding)->first();
            $karyawan = Karyawan::where('kontrak_kerja', $holding->id)
                ->where('karyawans.status_aktif', 'AKTIF')
                ->orderBy('karyawans.name', 'ASC')
                // ->limit(5)
                ->get();
            return response()->json([
                'code' => 200,
                'user'  => $user,
                'data' => $karyawan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            ]);
        }
    }
    public function update_user_karyawan(Request $request, $id)
    {
        // dd($request->all());
        $validasi = Validator::make($request->all(), [
            'id_user' => 'required',
            'karyawan' => 'required',
            'username' => 'required|unique:users,username,' . $request->id_user . ',id',
            'level' => 'required',
        ], [
            'id_user.required' => 'User Tidak Boleh Kosong',
            'karyawan.required' => 'Karyawan Tidak Boleh Kosong',
            'username.required' => 'Username Tidak Boleh Kosong',
            'username.unique' => 'Username Sudah Ada',
            'level.required' => 'Level Tidak Boleh Kosong',
        ]);
        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            return response()->json([
                'code' => 500,
                'message' => $errors
            ]);
        }
        try {
            $user = User::where('id', $request->id_user)->first();
            if ($user == null) {
                return response()->json([
                    'code' => 500,
                    'message' => 'User Tidak Ditemukan'
                ]);
            }
            $user->karyawan_id = $request->karyawan;
            $user->username = $request->username;
            $user->is_admin = $request->level;
            $user->update();

            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                // 'menu_id' => $request->menu_id,
                'menu_name' => 'Master Approval',
                'id_table' => $user->karyawan_id,
                // 'holding_id' => $holding->id,
                // 'holding_category' => $holding->holding_category,
                'table' => 'master_approval_group',
                'activity' => 'update',
                'description' => 'mengedit user, ' .  $user->username,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil di Simpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function editPasswordProses(Request $request, $id)
    {
        // dd('ok');
        $holding = request()->segment(count(request()->segments()));
        if ($request->username == $request->username_old) {
            $rules = [
                'username' => 'required|max:255',
                'password' => 'required|min:6|max:18',
            ];
        } else {
            $rules = [
                'username' => 'required|unique:users|max:255',
                'password' => 'required|min:6|max:18',
            ];
        }
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            // 'email' => ':attribute format email salah',
            'min' => ':attribute Kurang',
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            // dd($errors);
            Alert::error('Gagal', $errors);
            return back()->withInput();
        }
        $validatedData = $request->validate($rules, $customMessages);
        User::where('id', $id)->update([
            'username' => $validatedData['username'],
            'password_show' => $validatedData['password'],
            'password' => Hash::make($validatedData['password'])
        ]);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'activity' => 'update',
            'description' => 'Mengubah Ubah User karyawan ' . $request->name,
        ]);
        Alert::success('success', 'User Berhasil Diganti');
        return back();
    }
    public function non_aktif_proses(Request $request)
    {
        $update_user                = User::where('id', $request->id_nonactive)->first();
        $update_user->user_aktif    = 'NON AKTIF';
        $update_user->alasan        = $request->alasan_non_aktif;
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            // 'menu_id' => $request->menu_id,
            'menu_name' => 'Master Approval',
            'id_table' => $request->id_nonactive,
            // 'holding_id' => $holding->id,
            // 'holding_category' => $holding->holding_category,
            'table' => 'master_approval_group',
            'activity' => 'update',
            'description' => 'Menonaktifkan user, ' .  $update_user->username,
            'keterangan' => 'Success',
        ]);
        $update_user->update();


        return redirect()->back()->with('success', 'Data Berhasil di Simpan');
    }
    public function aktif_proses(Request $request)
    {
        $update_user                    = User::where('id', $request->id_active)->first();
        $update_user->user_aktif        = 'AKTIF';
        $update_user->alasan            = $request->alasan_aktif;
        $update_user->update();

        $user_name = User::where('id', $request->user()->id)->first();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            // 'menu_id' => $request->menu_id,
            'menu_name' => 'Master Approval',
            'id_table' => $request->id_active,
            // 'holding_id' => $holding->id,
            // 'holding_category' => $holding->holding_category,
            'table' => 'master_approval_group',
            'activity' => 'update',
            'description' => 'Mengaktifkan user, ' .  $update_user->username,
            'keterangan' => 'Success',
        ]);

        return redirect()->back()->with('success', 'Data Berhasil di Simpan');
    }

    public function ImportUser(Request $request)
    {
        // dd('ok');
        $holding = request()->segment(count(request()->segments()));
        try {
            Excel::import(new UsersImport, $request->file_excel);
            return redirect('/users/' . $holding)->with('success', 'Import User Sukses');
        } catch (\InvalidArgumentException $th) {
            // dd($th);
            return redirect()->back()->with('error', 'Import Error' . $th->getMessage());
        }
    }
    public function ImportUpdateUser(Request $request)
    {
        // dd('ok');
        $holding = request()->segment(count(request()->segments()));
        try {
            Excel::import(new UserUpdateImport, $request->file_excel_update);
            return redirect('/users/' . $holding)->with('success', 'Import User Sukses');
        } catch (\InvalidArgumentException $th) {
            // dd($th);
            return redirect('/users/' . $holding)->with('error', 'Import Error' . $th->getMessage());
        }
    }
    public function ExportUser(Request $request)
    {
        $date = date('YmdHis');
        $holding = request()->segment(count(request()->segments()));
        try {
            $query = Excel::download(new UserExport($holding), 'Data User Karyawan_' . $holding . '_' . $date . '.xlsx');
            return $query;
        } catch (\InvalidArgumentException $th) {
            // dd($th);
            return redirect()->back()->with('error', 'Import Error' . $th->getMessage());
        }
    }
    public function download_pdf_user_karyawan(Request $request)
    {
        set_time_limit(6000);
        $cek_holding = request()->segment(count(request()->segments()));
        if ($cek_holding == 'sp') {
            $holding = 'CV. SUMBER PANGAN';
        } else if ($cek_holding == 'sps') {
            $holding = 'PT. SURYA PANGAN SEMESTA';
        } else {
            $holding = 'CV. SURYA INTI PANGAN';
        }
        $date = date('YmdHis');
        $data = [
            'user' => User::Join('karyawans', 'karyawans.id', 'users.karyawan_id')->where('karyawans.kontrak_kerja', $cek_holding)->orderBy('karyawans.name', 'ASC')->get(),
            'holding' => $holding,
            'cek_holding' => $cek_holding,
        ];
        // dd($data);
        $pdf = PDF::loadView('admin/karyawan/cetak_pdf_user_karyawan', $data)->setPaper('F4', 'potrait');
        return $pdf->stream('DATA USER KARYAWAN' . $holding . '_' . $date . 'pdf');
    }
}
