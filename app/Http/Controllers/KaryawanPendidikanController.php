<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanPendidikan;
use App\Models\Menu;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class KaryawanPendidikanController extends Controller
{
    public function button_pendidikan($id)
    {
        $data_pendidikan = KaryawanPendidikan::select()->where('id_karyawan', $id)->count();
        return response()->json([
            'code' => 200,
            // 'data' => $get_data,
            'data_pendidikan' => $data_pendidikan,
            // 'message' => 'Data Berhasil Diupdate'
        ]);
    }
    public function pendidikan_datatable($id)
    {
        $pendidikan = KaryawanPendidikan::where('id_karyawan', $id)->get();
        // dd($pendidikan, $id);  
        return DataTables::of($pendidikan)
            ->addColumn('aksi', function ($row) {
                $btn = '<button type="button"  class="btn btn-sm btn-primary btn_edit_pendidikan" data-id_pendidikan="' . $row->id_pendidikan . '" data-jenjang="' . $row->jenjang . '" data-nama_instansi="' . $row->institusi . '" data-jurusan="' . $row->jurusan . '" data-tahun_masuk="' . $row->tanggal_masuk . '" data-tahun_lulus="' . $row->tanggal_keluar . '"><i class="mdi mdi-pencil"></i></button>';
                $btn = $btn . ' <button class="btn btn-sm btn-danger" type="button" id="btn_delete_pendidikan" data-id="' . $row->id_pendidikan . '"><i class="mdi mdi-delete"></i></button>';
                return $btn;
            })

            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function add_pendidikan(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate(
                [
                    'id_karyawan' => 'required',
                    'nama_instansi' => 'required',
                    'jurusan' => 'required',
                    'jenjang' => 'required',
                    'tahun_masuk' => 'required',
                    'tahun_lulus' => 'required',
                ],
                [
                    'id_karyawan.required' => 'ID Karyawan wajib diisi',
                    'nama_instansi.required' => 'Nama Instansi wajib diisi',
                    'jurusan.required' => 'Jurusan wajib diisi',
                    'jenjang.required' => 'Jenjang wajib diisi',
                    'tahun_masuk.required' => 'Tahun Masuk wajib diisi',
                    'tahun_lulus.required' => 'Tahun Lulus wajib diisi',
                ]
            );
            $id_pendidikan = Uuid::uuid4();
            KaryawanPendidikan::create(
                [
                    'id_pendidikan' => $id_pendidikan,
                    'id_karyawan' => $validatedData['id_karyawan'],
                    'institusi' => $validatedData['nama_instansi'],
                    'jurusan' => $validatedData['jurusan'],
                    'jenjang' => $validatedData['jenjang'],
                    'tanggal_masuk' => $validatedData['tahun_masuk'],
                    'tanggal_keluar' => $validatedData['tahun_lulus'],
                    'created_at' => now(),
                ]
            );
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_pendidikan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_pendidikan',
                'activity' => 'create',
                'description' => 'Menambah data pendidikan ' . $karyawan_id->name,
                'keterangan' => 'Success',
            ]);
            $data_pendidikan = KaryawanPendidikan::select()->where('id_karyawan', $request->id_karyawan)->count();
            return response()->json([
                'code' => 200,
                'data_pendidikan' => $data_pendidikan,
                'message' => 'Data berhasil ditambahkan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }

    public function update_pendidikan(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate(
                [
                    'id_karyawan' => 'required',
                    'nama_instansi' => 'required',
                    'jurusan' => 'required',
                    'jenjang' => 'required',
                    'tahun_masuk' => 'required',
                    'tahun_lulus' => 'required',
                ],
                [
                    'id_karyawan.required' => 'ID Karyawan wajib diisi',
                    'nama_instansi.required' => 'Nama Instansi wajib diisi',
                    'jurusan.required' => 'Jurusan wajib diisi',
                    'jenjang.required' => 'Jenjang wajib diisi',
                    'tahun_masuk.required' => 'Tahun Masuk wajib diisi',
                    'tahun_lulus.required' => 'Tahun Lulus wajib diisi',
                ]
            );
            KaryawanPendidikan::where('id_pendidikan', $request->id_pendidikan)->update(
                [
                    'institusi' => $validatedData['nama_instansi'],
                    'jurusan' => $validatedData['jurusan'],
                    'jenjang' => $validatedData['jenjang'],
                    'tanggal_masuk' => $validatedData['tahun_masuk'],
                    'tanggal_keluar' => $validatedData['tahun_lulus'],
                    'updated_at' => now(),
                ]
            );
            $karyawan_id = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_pendidikan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_pendidikan',
                'activity' => 'update',
                'description' => 'mengedit data pendidikan ' . $karyawan_id->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil Diupdate'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }
    public function delete_pendidikan(Request $request, $id)
    {
        // dd($request->all());
        $get = KaryawanPendidikan::where('id_pendidikan', $request->id_pendidikan);
        $get_pendidikan = KaryawanPendidikan::where('id_pendidikan', $request->id_pendidikan)->first();
        $karyawan_id = Karyawan::where('id', $get_pendidikan->id_karyawan)->first();
        $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $request->id_pendidikan,
            'holding_id' => $holdings->id ?? '-',
            'holding_category' => $holdings->holding_category ?? '-',
            'table' => 'karyawan_pendidikan',
            'activity' => 'delete',
            'description' => 'menghapus data pendidikan ' . $karyawan_id->name,
            'keterangan' => 'Success',
        ]);
        if ($get->exists()) {
            $get->delete();
            $data_pendidikan = KaryawanPendidikan::select()->where('id_karyawan', $id)->count();
            return response()->json([
                'code' => 200,
                'data_pendidikan' => $data_pendidikan,
                'message' => 'Data berhasil Dihapus',

            ]);
        } else {
            return response()->json([
                'code' => 402,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
}