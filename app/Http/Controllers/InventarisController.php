<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\InventarisGedung;
use App\Models\InventarisKategori;
use App\Models\InventarisKondisi;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\InventarisRuangan;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class InventarisController extends Controller
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
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        $site = Site::orderBy('site_name')->get();
        $holding_select = Holding::orderBy('holding_name')->get();
        $gedung = InventarisGedung::orderBy('nama_gedung')->get();
        $karyawan = Karyawan::with('KontrakKerja')->orderBy('name')->where('status_aktif', 'AKTIF')->get();
        $kategori = InventarisKategori::orderBy('nama_kategori')->get();
        $kondisi = InventarisKondisi::orderBy('nama_kondisi')->get();
        return view('admin.inventaris.index', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'holding_select' => $holding_select,
            'menus' => $menus,
            'site' => $site,
            'gedung' => $gedung,
            'karyawan' => $karyawan,
            'kategori' => $kategori,
            'kondisi' => $kondisi,
        ]);
    }
    public function insert(Request $request, $holding)
    {
        // dd($request->all());
        $holding  = Holding::where('holding_code', $holding)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'nama_inventaris' => 'required|max:255',
                'serial_number' => 'required|max:255',
                'id_aset' => 'required|max:255',
                'id_kategori' => 'required|max:255',
                'id_site' => 'required',
                'id_gedung' => 'required',
                'id_ruangan' => 'required',
                'id_holding' => 'required',
                'status' => 'required',
                'kondisi' => 'required',
                // 'id_karyawan' => 'required',
                // 'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
                //     'foto.image' => 'File harus berupa gambar!',
                //     'foto.mimes' => 'Format file tidak valid. Harus berupa: jpeg, png, jpg, gif, svg!',
                //     'foto.max' => 'Ukuran file terlalu besar. Maksimal 2MB!',
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
            if ($request->foto != null) {
                $file = $request->file('foto')->store('public/foto_inventaris');
                $file_save = basename($file);
            }
            $kategori = InventarisKategori::where('id', $request->id_kategori)->first();
            $holding_select = Holding::where('id', $request->id_holding)->first();
            $kondisi = InventarisKondisi::where('id', $request->kondisi)->first();
            $site = Site::where('id', $request->id_site)->first();
            $gedung = InventarisGedung::where('id', $request->id_gedung)->first();
            // dd($gedung);
            $ruangan = InventarisRuangan::where('id', $request->id_ruangan)->first();
            $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
            $id_inventaris = Uuid::uuid4();
            Inventaris::create(
                [
                    'id' => $id_inventaris,
                    'nama_inventaris' => $request->nama_inventaris,
                    'serial_number' => $request->serial_number,
                    'id_aset' => $request->id_aset,
                    'id_kategori' => $request->id_kategori,
                    'nama_kategori' => $kategori->nama_kategori,
                    'id_gedung' => $request->id_gedung,
                    'nama_gedung' => $gedung->nama_gedung,
                    'keterangan' => $request->keterangan,

                    'id_holding' => $request->id_holding,
                    'nama_holding' => $holding_select->holding_name,

                    'id_site' => $request->id_site,
                    'nama_site' => $site->site_name,
                    'id_ruangan' => $request->id_ruangan,
                    'nama_ruangan' => $ruangan->nama_ruangan,
                    'status' => $request->status,
                    'kondisi' => $request->kondisi,
                    'nama_kondisi' => $kondisi->nama_kondisi,
                    'foto' => isset($file_save) ? $file_save : null,
                    'keterangan' => $request->keterangan,
                    'id_karyawan'   => $karyawan->id ?? null,
                    'nama_karyawan' => $karyawan->name ?? null,
                    'nik_karyawan'  => $karyawan->nomor_identitas_karyawan ?? null,
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
                'id_table' => $id_inventaris,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'inventaris',
                'activity' => 'create',
                'description' => 'Menambahkan inventaris baru ' . $request->nama_inventaris,
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
        $validator = Validator::make($request->all(), [
            'id_inventaris' => 'required|exists:inventaris,id',
            'nama_inventaris' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'id_aset' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'menu_id' => 'required|exists:menus,id',
        ], [
            'required' => ':attribute Tidak boleh kosong!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
            // dd($karyawan);
            Inventaris::where('id', $request->id_inventaris)->update([
                'nama_inventaris' => $request->nama_inventaris,
                'serial_number' => $request->serial_number,
                'id_aset' => $request->id_aset,
                'keterangan' => $request->keterangan,
                'nama_excel' => $request->nama_excel,
                'id_karyawan' => $request->id_karyawan,
                'nama_karyawan' => $karyawan->name ?? null
            ]);

            $holding = Holding::where('holding_code', $holding)->first();
            $inventaris = Inventaris::where('id', $request->id_inventaris)->first();
            $inventaris->nama_inventaris = $request->nama_inventaris;
            $inventaris->keterangan = $request->keterangan;
            $inventaris->save();

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', Auth::user()->id)->first();
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $inventaris->id,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'inventaris',
                'activity' => 'update',
                'description' => 'mengedit inventaris ' . $inventaris->nama_inventaris,
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diubah',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function datatable(Request $request)
    {
        $table = Inventaris::orderBy('nama_inventaris', 'ASC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) use ($request) {
                    // Lebar seragam untuk semua tombol (sesuaikan jika perlu)
                    $btnStyle = 'style="width: 130px;"';

                    $btn = '<button type="button" id="btn_edit_inventaris" '
                        . 'data-id="' . $row->id . '" '
                        . 'data-nama_inventaris="' . $row->nama_inventaris . '" '
                        . 'data-keterangan="' . $row->keterangan . '" '
                        . 'data-serial_number="' . $row->serial_number . '" '
                        . 'data-id_aset="' . $row->id_aset . '" '
                        . 'data-nama_excel="' . $row->nama_excel . '" '
                        . 'data-id_karyawan="' . $row->id_karyawan . '" '
                        . 'class="btn btn-sm btn-warning waves-effect waves-light m-1 text-start" ' . $btnStyle . '>'
                        . '<span class="tf-icons mdi mdi-pencil-outline me-1"></span> Edit</button>';

                    // $btn .= '<a href="#" '
                    //     . 'class="btn btn-sm btn-success waves-effect waves-light m-1 text-start" ' . $btnStyle . '>'
                    //     . '<span class="tf-icons mdi mdi-swap-horizontal me-1"></span> Perpindahan</a>';

                    // $btn .= '<a href="' . url('/inventaris_pemakaian/' . $row->id_holding . '/' . $row->id) . '" '
                    //     . 'class="btn btn-sm btn-info waves-effect waves-light m-1 text-start" ' . $btnStyle . '>'
                    //     . '<span class="tf-icons mdi mdi-format-list-bulleted me-1"></span> Pemakaian</a>';
                    // $btn .= '<a href="#" '
                    //     . 'class="btn btn-sm btn-primary waves-effect waves-light m-1 text-start" ' . $btnStyle . '>'
                    //     . '<span class="mdi mdi-account-check"></span> Pembawa</a>';

                    $btn .= '<button type="button" id="btn_delete_inventaris" '
                        . 'data-id="' . $row->id . '" '
                        . 'class="btn btn-sm btn-danger waves-effect waves-light m-1 text-start" ' . $btnStyle . '>'
                        . '<span class="tf-icons mdi mdi-delete-outline me-1"></span> Hapus</button>';

                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == '1') {
                        return '<span class="badge bg-label-success me-1">Terpakai</span>';
                    } else {
                        return '<span class="badge bg-label-danger me-1">Tidak Terpakai</span>';
                    }
                })
                ->addColumn('foto', function ($row) {
                    // if ($row->foto) {
                    //     $url = asset('storage/public/foto_inventaris/' . $row->foto); // Sesuaikan dengan path penyimpanan kamu
                    //     return '<img src="' . $url . '" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;" alt="Foto">';
                    // }
                    // return '<span class="badge bg-secondary">Tidak ada foto</span>';
                    $btn = '<a href="' . asset('storage/public/foto_inventaris/' . $row->foto) . '" 
                                type="button" class="btn btn-icon btn-info waves-effect waves-light m-1" target="_blank">
                                <span class="tf-icons mdi mdi-eye-outline"></span>
                            </a>';
                    return $btn;
                })
                ->rawColumns([
                    'status',
                    'foto',
                    'kondisi',
                    'option'
                ])
                ->make(true);
        }
    }
    public function delete($id, $holding, $menu_id)
    {
        // dd($id);
        $holding = Holding::where('holding_code', $holding)->first();
        // dd($holding);
        // $cek_divisi = Divisi::where('kondisi_id', $id)->where('holding', $holding->id)->count();
        $inventaris = Inventaris::where('id', $id)->first();
        // if ($cek_divisi == 0) {
        // $cek_karyawan = KaryawanJabatan::where('inventaris_id', $id)->where('is_active', '1')->count();
        // if ($cek_karyawan == 0) {
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
            'table' => 'inventaris',
            'activity' => 'delete',
            'description' => 'menghapus inventaris ' . $inventaris->nama_kondisi,
            'keterangan' => 'Success',
        ]);

        $inventaris->delete();
        return response()->json(['status' => 1]);
        // } else {
        //     $nama_menu = Menu::where('id', $menu_id)->first();
        //     $user_name = User::where('id', Auth::user()->id)->first();
        //     ActivityLog::create([
        //         'user_id' => Auth::user()->id,
        //         'user_name' => $user_name->username,
        //         'menu_id' => $menu_id,
        //         'menu_name' => $nama_menu->name,
        //         'id_table' => $id,
        //         'holding_id' => $holding->id,
        //         'holding_category' => $holding->holding_category,
        //         'table' => 'kondisi_inventaris',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus kondisi inventaris karena sudah memiliki relasi karyawan ' . $kondisi_inventaris->nama_kondisi,
        //         'keterangan' => 'Failed',
        //     ]);
        //     return response()->json(['status' => 2]);
        // }
        // } else {
        //     $nama_menu = Menu::where('id', $menu_id)->first();
        //     $user_name = User::where('id', Auth::user()->id)->first();
        //     ActivityLog::create([
        //         'user_id' => Auth::user()->id,
        //         'user_name' => $user_name->username,
        //         'menu_id' => $menu_id,
        //         'menu_name' => $nama_menu->name,
        //         'id_table' => $id,
        //         'holding_id' => $holding->id,
        //         'holding_category' => $holding->holding_category,
        //         'table' => 'kondisi_inventaris',
        //         'activity' => 'delete',
        //         'description' => 'Gagal menghapus kondisi inventaris karena sudah memiliki relasi ' . $kondisi_inventaris->nama_kondisi,
        //         'keterangan' => 'Failed',
        //     ]);
        //     return response()->json(['status' => 0]);
        // }
    }
}
