<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\ApprovalUser;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class KaryawanJabatanController extends Controller
{
    // public function button_jabatan($id)
    // {
    //     $data_jabatan = KaryawanJabatan::select()->where('karyawan_id', $id)->count();
    //     return response()->json([
    //         'code' => 200,
    //         // 'data' => $get_data,
    //         'data_jabatan' => $data_jabatan,
    //         // 'message' => 'Data Berhasil Diupdate'
    //     ]);
    // }
    public function jabatan_datatable($id)
    {
        $table = KaryawanJabatan::with('Holding')
            ->with('Departemen')
            ->with('Divisi')
            ->with('Bagian')
            ->with('Jabatan')
            ->where('karyawan_id', $id)->orderBy('created_at', 'ASC')->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('holding', function ($row) {
                    return $row->Holding->holding_name ?? '-';
                })
                ->addColumn('departemen', function ($row) {
                    return $row->Departemen->nama_departemen ?? '-';
                })
                ->addColumn('divisi', function ($row) {
                    return $row->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('bagian', function ($row) {
                    return $row->Bagian->nama_bagian ?? '-';
                })
                ->addColumn('jabatan', function ($row) {
                    return $row->Jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('option', function ($row) {
                    $btn =
                        '
                        <button type="button" id="btn_edit_jabatan"
                            data-id_jbt_karyawan="' . $row->id_jbt_karyawan . '"
                            data-is_active="' . $row->is_active . '"
                            data-main="' . $row->main . '"
                            class="btn btn-sm btn-primary">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" id="btn_delete_jabatan"
                            data-id_jbt_karyawan="' . $row->id_jbt_karyawan . '"
                            class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </button>
                        ';
                    return $btn;
                })
                ->addColumn('is_active', function ($row) {
                    if ($row->is_active == '1') {
                        return '<span class="badge bg-label-success">Aktif</span>';
                    } else {
                        return '<span class="badge bg-label-secondary">Non Aktif</span>';
                    }
                })
                ->addColumn('main', function ($row) {
                    if ($row->main == '1') {
                        return '<span class="badge bg-label-success">Utama</span>';
                    } else {
                        return '<span class="badge bg-label-info">Sampingan</span>';
                    }
                })
                ->rawColumns(['option', 'departemen', 'divisi', 'bagian', 'jabatan', 'is_active', 'main'])
                ->make(true);
        }
    }
    public function get_departemen($holding_id)
    {
        // dd($holding_id);
        $get_departemen = Departemen::where('holding', $holding_id)->with('Holding')->orderBy('nama_departemen', 'ASC')->get();
        // dd($get_departemen);
        echo "<option value=''>Pilih Departemen...</option>";
        foreach ($get_departemen as $departemen) {
            echo "<option value='$departemen->id'>$departemen->nama_departemen</option>";
        }
    }
    public function get_divisi($id)
    {
        // dd($id);
        $get_divisi = Divisi::where('dept_id', $id)->orderBy('nama_divisi', 'ASC')->get();
        // dd($get_divisi);
        echo "<option value=''>Pilih Divisi...</option>";
        foreach ($get_divisi as $divisi) {
            echo "<option value='$divisi->id'>$divisi->nama_divisi</option>";
        }
    }
    public function get_bagian($id)
    {
        // dd($id);
        $get_bagian = Bagian::where('divisi_id', $id)->orderBy('nama_bagian', 'ASC')->get();
        echo "<option value=''>Pilih Bagian...</option>";
        foreach ($get_bagian as $bagian) {
            echo "<option value='$bagian->id'>$bagian->nama_bagian</option>";
        }
    }
    public function get_jabatan($id)
    {
        $get_jabatan = Jabatan::where('bagian_id', $id)->orderBy('nama_jabatan', 'ASC')->get();
        // dd($get_jabatan);
        echo "<option value=''>Pilih Jabatan...</option>";
        foreach ($get_jabatan as $jabatan) {
            echo "<option value='$jabatan->id'>$jabatan->nama_jabatan</option>";
        }
    }
    public function jabatan_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'holding_id' => 'required',
                'departemen_id' => 'required',
                'divisi_id' => 'required',
                'bagian_id' => 'required',
                'jabatan_id' => 'required',
                'main' => 'required',
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

        $get_holding = Karyawan::where('id', $request->id_karyawan)->first();
        $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();

        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        $name = Karyawan::where('id', $request->id_karyawan)->first();
        if ($request->main == 1) {
            $utama = KaryawanJabatan::where('karyawan_id', $request->id_karyawan)->where('main', '1')->where('is_active', '1')->count();

            // dd($utama);
            if ($utama != 0) {
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id_karyawan,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'karyawan_jabatan',
                    'activity' => 'create',
                    'description' => 'gagal menambahkan jabatan karyawan karena sudah ada jabatan utama ' . $name->name,
                    'keterangan' => 'Failed',
                ]);
                return response()->json([
                    'code' => 600,
                    'message' => 'Karyawan sudah punya jabatan utama',

                ]);
            } else {
                $main = $request->main;
            }
        } else {
            $main = $request->main;
        }
        try {
            $riwayat = new KaryawanJabatan();
            $riwayat->id_jbt_karyawan = Uuid::uuid4();
            $riwayat->karyawan_id = $request->id_karyawan;
            $riwayat->holding_id = $request->holding_id;
            $riwayat->departemen_id = $request->departemen_id;
            $riwayat->divisi_id = $request->divisi_id;
            $riwayat->bagian_id = $request->bagian_id;
            $riwayat->jabatan_id = $request->jabatan_id;
            $riwayat->main = $main;
            $riwayat->is_active = 1;
            $riwayat->created_at = date('Y-m-d H:i:s');
            $riwayat->save();
            // $first_image = Carousel::limit('1', 'asc')->first();
            // $get_data = Carousel::limit('1', 'asc')->get();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_karyawan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_jabatan',
                'activity' => 'create',
                'description' => 'Menambah jabatan karyawan ' . $name->name,
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
    public function kategori_jabatan_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'kategori' => 'required',
                'shift' => 'required',
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
            Karyawan::where('id', $request->id_karyawan)->update([
                'kategori' => $request->kategori,
                'shift' => $request->shift
            ]);
            $get_holding = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $name = Karyawan::where('id', $request->id_karyawan)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_karyawan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengubah Kategori karyawan ' . $name->name . ' menjadi ' . $request->shift,
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
    public function jabatan_update(Request $request)
    {

        $karyawan = KaryawanJabatan::where('id_jbt_karyawan', $request->id_jbt_karyawan)->first();
        $utama = KaryawanJabatan::where('karyawan_id', $karyawan->karyawan_id)->where('main', '1')->where('is_active', '1')->count();
        // dd($utama);
        if ($utama != 0) {
            return response()->json([
                'code' => 600,
                'message' => 'Karyawan sudah punya jabatan utama yang aktif',

            ]);
        }

        try {
            // if ($request->main == 1) {
            //     $utama = KaryawanJabatan::where('karyawan_id', $request->id_karyawan)->where('main', '1')->count();
            //     dd($utama);
            //     if ($utama != 0) {
            //         return response()->json([
            //             'code' => 600,
            //             'message' => 'Karyawan sudah punya jabatan utama',

            //         ]);
            //     } else {
            //         $main = $request->main;
            //     }
            // } else {
            //     $main = $request->main;
            // }

            KaryawanJabatan::where('id_jbt_karyawan', $request->id_jbt_karyawan)->update([
                'is_active' => $request->is_active,
                // 'main' => $main,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $get_holding = Karyawan::where('id', $karyawan->karyawan_id)->first();
            $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();
            // dd($karyawan);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $name = Karyawan::where('id', $karyawan->karyawan_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_jbt_karyawan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_jabatan',
                'activity' => 'update',
                'description' => 'mengubah jabatan karyawan ' . $name->name,
                'keterangan' => 'Success',
            ]);
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
    public function jabatan_delete(Request $request)
    {
        try {
            // dd($request->id_riwayat);
            $karyawan_jabatan = KaryawanJabatan::where('id_jbt_karyawan', $request->id_jbt_karyawan)->first();
            $karyawan = Karyawan::where('id', $karyawan_jabatan->karyawan_id)->first();

            // $master_approval = Approval::where('departemen_id', 'LIKE', '%' . $karyawan_jabatan->departemen_id . '%')->get();

            // dd($approval_user);

            $holdings = Holding::where('id', $karyawan->kontrak_kerja)->first();

            ApprovalUser::with('approval')
                ->where('karyawan_id', $karyawan->id)
                ->whereHas('approval', function ($query) use ($karyawan_jabatan) {
                    $query->where('departemen_id', 'LIKE', '%' . $karyawan_jabatan->departemen_id . '%');
                })
                ->delete();
            // dd($karyawan_jabatan);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $name = Karyawan::where('id', $karyawan_jabatan->karyawan_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_jbt_karyawan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_jabatan',
                'activity' => 'delete',
                'description' => 'Menghapus jabatan karyawan ' . $name->name,
                'keterangan' => 'Success',
            ]);
            KaryawanJabatan::where('id_jbt_karyawan', $request->id_jbt_karyawan)->delete();
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
