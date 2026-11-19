<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\MemoInternal;
use App\Models\Menu;
use App\Models\RoleUsers;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Dflydev\DotAccessData\Data;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class MemoInternalController extends Controller
{
    public function index($id_holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $id_holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan');
            return redirect()->route('dashboard/holding');
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
        $holding_all = Holding::orderBy('holding_name', 'ASC')->get();
        $calendar = Calendar::all();
        $memo = MemoInternal::query();
        return view('admin.memo_internal.index', [
            'holding' => $getHolding,
            'count_memo_all' => $memo->count(),
            'count_memo_publish' => $memo->where('status', 1)->count(),
            'count_memo_draft' => $memo->where('status', 0)->count(),
            'holding_all' => $holding_all,
            'menus' => $menus
        ]);
    }
    public function memo_datatable(Request $request)
    {
        $data = MemoInternal::with('KaryawanPembuat')->get();
        // dd($data);
        return DataTables::of($data)
            ->addColumn('dibuat_oleh', function ($data) {
                $created_by = $data->KaryawanPembuat ? $data->KaryawanPembuat->name : '-';
                return $created_by;
            })
            ->addColumn('status', function ($data) {
                if ($data->status == 0) {
                    $status = '<span class="badge bg-label-info">WAITING APPROVAL</span>';
                } else {
                    $status = '<span class="badge bg-label-success">PUBLISHED</span>';
                }
                return $status;
            })
            ->addColumn('file', function ($data) {
                if ($data->status == 0) {
                    $file = '<span class="badge bg-label-info">-</span>';
                } else {
                    $file = '<a href="' . url('memo_internal/memo_file_pdf/' . $data->id) . '" target="_blank" class="btn_download btn btn-sm btn-danger"><i class="mdi mdi-file-document-check mdi-16px me-2"></i>Download</a>';
                }
                return $file;
            })
            ->addColumn('option', function ($data) {
                $option = '<button class="btn_edit btn btn-sm btn-warning" data-id="' . $data->id . '" data-isi=\'' . e($data->isi) . '\' data-holding=\'' . json_encode($data->holding) . '\' data-perihal="' . $data->perihal . '" data-penerima="' . $data->penerima . '" data-tgl_memo="' . $data->tanggal_memo . '" data-penandatangan="' . $data->penandatangan . '" data-holiday="' . $data->is_holiday . '" data-mulai_libur ="' . $data->tanggal_mulai_libur . '" data-selesai_libur="' . $data->tanggal_selesai_libur . '" data-lampiran="' . $data->lampiran . '" data-status ="' . $data->status . '"><i class="mdi mdi-pencil"></i></button>';
                $option = $option . '<button class="btn_delete btn btn-sm btn-danger" data-id="' . $data->id . '"><i class="mdi mdi-delete"></i></button>';
                return $option;
            })
            ->rawColumns(['dibuat_oleh', 'option', 'status', 'file'])
            ->make(true);
    }
    public function store($holding, Request $request)
    {
        try {
            if ($request->isHoliday == 'true') {
                $tgl_mulai =  'required';
                $tgl_selesai = 'required';
            } else {
                $tgl_mulai =  'nullable';
                $tgl_selesai = 'nullable';
            }
            $rules = [
                'isi_memo' => 'required',
                'tanggal' => 'required',
                'penandatangan' => 'required',
                'perihal' => 'required',
                'penerima' => 'required',
                'holding' => 'required',
                'tanggal_mulai_libur' => $tgl_mulai,
                'tanggal_selesai_libur' => $tgl_selesai,
                'lampiran' => 'nullable|mimes:pdf|max:5120',
            ];
            $custom_messages = [
                'isi_memo.required' => 'Isi memo tidak boleh kosong',
                'tanggal.required' => 'Tanggal tidak boleh kosong',
                'penandatangan.required' => 'Penandatangan tidak boleh kosong',
                'perihal.required' => 'Perihal tidak boleh kosong',
                'penerima.required' => 'Penerima tidak boleh kosong',
                'holding.required' => 'Holding tidak boleh kosong',
                'tanggal_mulai_libur.required' => 'Tanggal mulai libur tidak boleh kosong',
                'tanggal_selesai_libur.required' => 'Tanggal selesai libur tidak boleh kosong',
                'lampiran.max' => 'Ukuran file tidak boleh lebih dari 5MB',
                'lampiran.mimes' => 'Format file harus PDF',
            ];
            $validator = Validator::make($request->all(), $rules, $custom_messages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ]);
            }
            // BUAT NOMOR MEMO
            $count = MemoInternal::count() + 1;
            if (strlen($count) == 1) {
                $str_count = '000' . $count;
            } else if (strlen($count) == 2) {
                $str_count = '00' . $count;
            } else if (strlen($count) == 3) {
                $str_count = '0' . $count;
            }
            $bulanAngka = date('n');
            $map = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

            $bulanRomawi = $map[$bulanAngka];
            $tahun = date('Y');
            $no = 'MEMO/HRD/' . $bulanRomawi . '/' . $tahun . '/' . $str_count;
            // END BUAT NOMOR MEMO

            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            if ($request->isHoliday == 'true') {
                $tanggal_mulai = $request->tanggal_mulai_libur;
                $tanggal_selesai = $request->tanggal_selesai_libur;
            } else {
                $tanggal_mulai = null;
                $tanggal_selesai = null;
            }
            if ($request->lampiran != null) {
                $lampiran = $request->file('lampiran')->store('lampiran');
                $lampiran_save = basename($lampiran);
            } else {
                $lampiran_save = '';
            }
            // dd($request->all(), $tanggal_mulai, $tanggal_selesai);
            // dd($request->all(), $lampiran_save, $user_karyawan);
            $add = new MemoInternal();
            $add->id = Uuid::uuid4();
            $add->nomor_memo = $no;
            $add->isi = $request->isi_memo;
            $add->holding = $request->holding;
            $add->tanggal_memo = $request->tanggal;
            $add->penandatangan = $request->penandatangan;
            $add->perihal = $request->perihal;
            $add->penerima = $request->penerima;
            $add->is_holiday = $request->isHoliday;
            $add->tanggal_mulai_libur = $tanggal_mulai;
            $add->tanggal_selesai_libur = $tanggal_selesai;
            $add->lampiran = $lampiran_save;
            $add->status = 0;
            $add->created_by = $user_karyawan->id;
            $add->save();

            if ($request->isHoliday == 'true') {
                $period = CarbonPeriod::create($tanggal_mulai, $tanggal_selesai);
                foreach ($period as $date) {
                    $add_calendar = new Calendar();
                    $add_calendar->id = Uuid::uuid4();
                    $add_calendar->nama_kegiatan = $request->perihal;
                    $add_calendar->start_date = $date->format('Y-m-d');
                    $add_calendar->end_date = $date->format('Y-m-d');
                    $add_calendar->memo_id = $add->id;
                    $add_calendar->created_by = $user_karyawan->id;
                    $add_calendar->created_at = Carbon::now();
                    $add_calendar->updated_at = Carbon::now();
                    $add_calendar->type = 'LIBUR';
                    $add_calendar->status = 0;
                    $add_calendar->save();
                }
            }
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function delete($id)
    {
        try {
            $delete = MemoInternal::where('id', $id)->first();
            if (!$delete) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ]);
            } else {
                if ($delete->status == 1) {
                    return response()->json([
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Data Tidak Bisa Dihapus Karena Sudah Di-Approve',
                    ]);
                } else {
                    $delete->delete();
                    return response()->json([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Data berhasil dihapus'
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function memo_file_pdf($id)
    {
        try {
            $memo = MemoInternal::where('id', $id)->first();
            // dd($memo);
            $get_holding = Holding::whereIn('id', $memo->holding)->get();
            $pdf = Pdf::setOption([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ])->loadView('admin.memo_internal.form_memo_internal', [
                'tittle' => 'Data Recruitment',
            ], compact('memo', 'get_holding'));
            // return view('admin.memo_internal.form_memo_internal', compact('memo', 'get_holding'));
            return $pdf->stream('memo.pdf');
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
