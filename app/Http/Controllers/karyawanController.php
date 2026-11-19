<?php

namespace App\Http\Controllers;

use App\Exports\KaryawanExport;
use App\Helpers\WebJsonResponse;
use App\Imports\KaryawanImport;
use App\Imports\KaryawanImportUpdate;
use App\Imports\UsersImport;
use App\Imports\UserUpdateImport;
use App\Models\Cuti;
use App\Models\Jabatan;
use App\Models\Lembur;
use App\Models\User;
use App\Models\MappingShift;
use App\Models\ResetCuti;
use App\Models\Shift;
use App\Models\Sip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use App\Models\ApprovalUser;
use App\Models\Bagian;
use App\Models\Cities;
use App\Models\City;
use App\Models\CutiPeriod;
use App\Models\Departemen;
use App\Models\District;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KaryawanKeahlian;
use App\Models\KaryawanKontrak;
use App\Models\KaryawanNonActive;
use App\Models\KaryawanNonaktif;
use App\Models\KaryawanPendidikan;
use App\Models\KaryawanRiwayat;
use App\Models\KaryawanTemp;
use App\Models\KaryawanTempAlamat;
use App\Models\KategoriCuti;
use App\Models\KuotaCutiKaryawan;
use App\Models\Lokasi;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Laravolt\Indonesia\IndonesiaService;
use App\Models\Provincies;
use App\Models\Regencies;
use App\Models\RoleUsers;
use App\Models\Site;
use App\Models\UserNonActive;
use App\Models\Village;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\AssignOp\Div;
use Rap2hpoutre\FastExcel\FastExcel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;


class karyawanController extends Controller
{
    public function index($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();
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
        $departemen = Departemen::orderBy('nama_departemen', 'ASC')->where('holding', $getHolding->id)->get();
        $user = Karyawan::where('kontrak_kerja', $getHolding->id)->where('status_aktif', 'AKTIF')->get();
        $jabatan = Jabatan::orderBy('nama_jabatan', 'ASC')->where('holding', $getHolding->id)->get();
        $karyawan_laki = Karyawan::where('gender', '1')->where('kontrak_kerja', $getHolding->id)->where('status_aktif', 'AKTIF')->count();
        $karyawan_perempuan = Karyawan::where('gender', '2')->where('kontrak_kerja', $getHolding->id)->where('status_aktif', 'AKTIF')->count();
        $karyawan_office = Karyawan::where('kategori', 'Karyawan Bulanan')->where('kontrak_kerja', $getHolding->id)->where('status_aktif', 'AKTIF')->count();
        $karyawan_shift = Karyawan::where('kategori', 'Karyawan Harian')->where('kontrak_kerja', $getHolding->id)->where('status_aktif', 'AKTIF')->count();
        return view('admin.karyawan.index', [
            'holding' => $getHolding,
            'title' => 'Karyawan',
            "data_departemen" => $departemen,
            'data_user' => $user,
            "data_jabatan" => $jabatan,
            "data_lokasi" => Lokasi::orderBy('nama_lokasi', 'ASC')->get(),
            "karyawan_laki" => $karyawan_laki,
            "karyawan_perempuan" => $karyawan_perempuan,
            "karyawan_office" => $karyawan_office,
            "karyawan_shift" => $karyawan_shift,
            'menus' => $menus
        ]);
    }
    public function ptkp($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();
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
        return view('admin.karyawan.ptkp', [
            'holding' => $getHolding,
            'title' => 'Karyawan',
            'menus' => $menus
        ]);
    }
    public function ptkp_datatable($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::with('Desa')
            ->with('Kecamatan')
            ->with('Kabupaten')
            ->with('Provinsi')
            ->with('Sertifikat')
            ->with('KaryawanJabatanOne')
            ->with('KaryawanKontrakOne')
            ->with('KaryawanPendidikanOne')
            ->with('KaryawanKontrak')
            ->with('PenempatanKerja')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            // ->limit(5)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('holding', function ($row) {
                    return $row->KontrakKerja->holding_category ?? '-';
                })
                ->addColumn('sertifikat', function ($row) {

                    if ($row->Sertifikat->isEmpty()) {
                        return '-';
                    }

                    return $row->Sertifikat
                        ->pluck('keahlian')
                        ->implode(', ');
                })
                ->addColumn('status_nikah', function ($row) {
                    if ($row->status_nikah == 1) {
                        $nikah = 'LAJANG';
                    } elseif ($row->status_nikah == 2) {
                        $nikah = 'MENIKAH';
                    } elseif ($row->status_nikah == 3) {
                        $nikah = 'CERAI HIDUP';
                    } elseif ($row->status_nikah == 4) {
                        $nikah = 'CERAI MATI';
                    } else {
                        $nikah = '-';
                    }
                    return $nikah;
                })
                ->addColumn('no_kk', function ($row) {
                    return $row->no_kk ?? '-';
                })
                ->addColumn('tgl_terbit_kk', function ($row) {
                    return $row->tgl_terbit_kk ? $row->tgl_terbit_kk->format('d-M-Y') : '-';
                })
                ->addColumn('npwp', function ($row) {
                    return $row->npwp ?? '-';
                })
                ->addColumn('ptkp', function ($row) {
                    return $row->ptkp ?? '-';
                })
                ->addColumn('no_bpjs_ketenagakerjaan', function ($row) {
                    return $row->no_bpjs_ketenagakerjaan ?? '-';
                })
                ->addColumn('no_bpjs_kesehatan', function ($row) {
                    return $row->no_bpjs_kesehatan ?? '-';
                })
                ->addColumn('detail_alamat', function ($row) {
                    $detail_alamat = 'RT ' . $row->rt . ', RW ' . $row->rw . ', ' . $row->detail_alamat;
                    return $detail_alamat;
                })
                ->addColumn('desa', function ($row) {
                    return $row->Desa != null ? $row->Desa->name : '-';
                })
                ->addColumn('kecamatan', function ($row) {
                    return $row->Kecamatan != null ? $row->Kecamatan->name : '-';
                })
                ->addColumn('kabupaten', function ($row) {
                    return $row->Kabupaten ? $row->Kabupaten->name : '-';
                })
                ->addColumn('provinsi', function ($row) {
                    return $row->Provinsi != null ? $row->Provinsi->name : '-';
                })
                ->addColumn('kode_pos', function ($row) {
                    return $row->kode_pos ?? '-';
                })
                ->addColumn('agama', function ($row) {
                    if ($row->agama != null) {
                        if ($row->agama == 1) {
                            $agama = 'ISLAM';
                        } elseif ($row->agama == 2) {
                            $agama = 'KRISTEN';
                        } elseif ($row->agama == 3) {
                            $agama = 'KRISTEN';
                        } elseif ($row->agama == 4) {
                            $agama = 'HINDU';
                        } elseif ($row->agama == 5) {
                            $agama = 'BUDHA';
                        } elseif ($row->agama == 6) {
                            $agama = 'KONGHUCU';
                        }
                    } else {
                        $agama = '-';
                    }
                    return $agama;
                })
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->KaryawanJabatanOne->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->KaryawanJabatanOne->Departemen->nama_departemen ?? '-';
                })
                ->addColumn('nama_bagian', function ($row) {
                    return $row->KaryawanJabatanOne->Bagian->nama_bagian ?? '-';
                })
                ->addColumn('penempatan_kerja', function ($row) {
                    return $row->PenempatanKerja->site_name ?? '-';
                })
                ->addColumn('tgl_join', function ($row) {
                    return $row->tgl_join ? $row->tgl_join->format('d-M-Y') : '-';
                })
                ->addColumn('lama_kerja', function ($row) {
                    if (!$row->tgl_join) {
                        return '-';
                    }
                    // return round(Carbon::parse($row->tgl_join)->diffInYears(now())) . ' TAHUN';
                    $diff = Carbon::parse($row->tgl_join)->diff(now());
                    return $diff->y . ' TAHUN ' . $diff->m . ' BULAN';
                })
                ->addColumn('gender', function ($row) {
                    if ($row->gender == 1) {
                        return 'LAKI-LAKI';
                    } elseif ($row->gender == 2) {
                        return 'PEREMPUAN';
                    }
                })
                ->addColumn('nama_bank', function ($row) {
                    return substr($row->nama_bank, 1);
                })
                ->addColumn('riwayat_kontrak', function ($row) {
                    if ($row->KaryawanKontrak->isEmpty()) {
                        return '-';
                    }

                    // return $row->KaryawanKontrak;
                    return $row->KaryawanKontrak
                        ->map(function ($item) {
                            $mulai = $item->tanggal_mulai
                                ? Carbon::parse($item->tanggal_mulai)->format('d-m-Y')
                                : '-';

                            $selesai = $item->tanggal_selesai
                                ? Carbon::parse($item->tanggal_selesai)->format('d-m-Y')
                                : '-';
                            if ($item->lama_kontrak == '1bln') {
                                $lama = '(1 Bulan)';
                            } elseif ($item->lama_kontrak == '3bln') {
                                $lama = '(3 Bulan)';
                            } elseif ($item->lama_kontrak == '6bln') {
                                $lama = '(6 Bulan)';
                            } elseif ($item->lama_kontrak == '1thn') {
                                $lama = '(1 Tahun)';
                            } elseif ($item->lama_kontrak == '2thn') {
                                $lama = '(2 Tahun)';
                            } elseif ($item->lama_kontrak == 'tetap') {
                                $lama = 'Tetap';
                            } elseif ($item->lama_kontrak == null) {
                                $lama = '-';
                            } else {
                                $lama = '-';
                            }
                            return $mulai . ' - ' . $selesai . ' ' . ($lama);
                        })
                        ->implode(', ');
                })
                ->addColumn('tanggal_berakhir', function ($row) {
                    return Carbon::parse($row->KaryawanKontrakOne?->tanggal_selesai)->format('d-m-Y') ?? '-';
                })
                ->addColumn('tgl_lahir', function ($row) {
                    return Carbon::parse($row->tgl_lahir)->format('d-m-Y') ?? '-';
                })
                ->addColumn('hari_expired', function ($row) {
                    $kontrak = $row->KaryawanKontrakOne;
                    if (!$kontrak || !$kontrak->tanggal_selesai) {
                        return '-';
                    }

                    if ($row->KaryawanKontrakOne->tanggal_selesai < today()) {
                        $hari_lebih = Carbon::parse($row->KaryawanKontrakOne->tanggal_selesai)
                            ->diffInDays(today());

                        return '+' . $hari_lebih;
                    } elseif ($row->KaryawanKontrakOne->tanggal_selesai > today()) {
                        $hari_kurang = Carbon::parse($row->KaryawanKontrakOne->tanggal_selesai)
                            ->diffInDays(today());

                        return $hari_kurang;
                    }
                })
                ->addColumn('jenjang', function ($row) {
                    return $row->KaryawanPendidikanOne?->jenjang ?? '-';
                })
                ->addColumn('jurusan', function ($row) {
                    return $row->KaryawanPendidikanOne?->jurusan ?? '-';
                })
                ->addColumn('institusi', function ($row) {
                    return $row->KaryawanPendidikanOne?->institusi ?? '-';
                })
                ->addColumn('status_aktif_karyawan', function ($row) {
                    if ($row->berhenti == null) {
                        $status = 'AKTIF';
                    } else {
                        $status = 'NON AKTIF';
                    }
                    return $status;
                })
                ->addColumn('saldo_cuti', function ($row) {
                    return $row->KuotaCuti->kuota ?? '-';
                })
                ->rawColumns([
                    'status_nikah',
                    'holding',
                    'sertifikat',
                    'no_kk',
                    'tgl_terbit_kk',
                    'npwp',
                    'ptkp',
                    'no_bpjs_ketenagakerjaan',
                    'no_bpjs_kesehatan',
                    'detail_alamat',
                    'desa',
                    'kecamatan',
                    'kabupaten',
                    'provinsi',
                    'kode_pos',
                    'agama',
                    'nama_jabatan',
                    'nama_divisi',
                    'nama_departemen',
                    'nama_bagian',
                    'penempatan_kerja',
                    'tgl_join',
                    'tgl_lahir',
                    'lama_kerja',
                    'gender',
                    'nama_bank',
                    'riwayat_kontrak',
                    'tanggal_berakhir',
                    'hari_expired',
                    'jenjang',
                    'jurusan',
                    'institusi',
                    'status_aktif_karyawan',
                    'saldo_cuti',
                ])
                ->make(true);
        }
    }
    public function tambahKaryawanProses(Request $request, $id)
    {
        // dd($request->all());
        $holding = Holding::where('id', $id)->first();
        $rules = [
            'no_karyawan' => 'required|max:14|min:13',
            'no_karyawan' => 'unique:karyawans,nomor_identitas_karyawan,' . $request->no_karyawan,
            'name' => 'required|max:255',
        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melampaui Batas Maksimal',

        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            // dd($errors);
            Alert::error('Gagal', $errors);
            return back()->withInput();
        }
        $id_karyawan = UUID::uuid4();
        Karyawan::create(
            [
                'id'                                => $id_karyawan,
                'nomor_identitas_karyawan'          => $request->no_karyawan,
                'name'                              => $request->name,
                'kontrak_kerja'                     => $id,
                'status_aktif'                      => 'AKTIF',
                'kategori'                          => $request->kategori,
                'created_at'                        => date('Y-m-d H:i:s'),
            ]
        );
        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $id,
            'holding_id' => $holding->id,
            'holding_category' => $holding->holding_category,
            'table' => 'karyawans',
            'activity' => 'update',
            'description' => 'mengubah data karyawan ' . $request->name,
            'keterangan' => 'Success',
        ]);
        //

        // Merekam aktivitas pengguna
        // ActivityLog::create([
        //     'user_id' => Auth::user()->id,
        //     'activity' => 'create',
        //     'description' => 'Menambahkan data karyawan baru ' . $request->name,
        // ]);
        return redirect('/karyawan/detail/' . $id_karyawan . '/' . $holding->holding_code)->with('success', 'data berhasil ditambahkan');
    }
    public function non_aktif(Request $request, $holding)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();

        // $getHoldingall = Holding::get();
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
        return view('admin.karyawan.non_aktif', [
            // return view('karyawan.index', [
            'title' => 'Karyawan',
            'menus' => $menus,
            'holding' => $getHolding,
        ]);
    }
    public function non_aktif_proses(Request $request)
    {
        // dd($request->all());
        // $data                       = new KaryawanNonActive();
        // $data->karyawan_id              = $request->id_nonactive;
        // $data->tanggal_nonactive     = $request->date_now;
        // $data->alasan               = $request->alasan_non_aktif;
        // $data->save();
        $rules = [
            'date_now'                     => 'required',
            'alasan_non_aktif'                 => 'required',
        ];
        $customessages =
            [
                'required'           => ':attribute tidak boleh kosong!',
            ];

        try {
            $user_name = User::where('id', $request->user()->id)->first();
            $validator = Validator::make($request->all(), $rules, $customessages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ]);
            }
            // dd($request->all());
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $nonaktif_user = Karyawan::where('id', $request->id_nonactive)->first();
            // $nonaktif_user->status_aktif          = 'NON AKTIF';
            $nonaktif_user->tanggal_nonactive     = $request->date_now;
            $nonaktif_user->berhenti              = 1;
            $nonaktif_user->alasan_nonactive      = $request->alasan_non_aktif;


            // dd($approval_user);
            $nonaktif_user->update();
            $cek = KaryawanNonaktif::where('id_karyawan', $nonaktif_user->id)->exists();
            if (!$cek) {
                KaryawanNonaktif::create([
                    'id'                => Uuid::uuid4(),
                    'id_karyawan'       => $nonaktif_user->id,
                    'nama_karyawan'     => $nonaktif_user->name,
                    'deskripsi'         => $request->alasan_non_aktif,
                    'tanggal_nonaktif'  => $request->date_now,
                    'status'             => 0,
                    'created_by'         => $user_karyawan->id,
                    'created_by_name'    => $user_karyawan->name,
                    'created_at'        => now(),

                ]);
                ApprovalUser::where('karyawan_id', $request->id_nonactive)->delete();



                $holdings = Holding::where('id', $nonaktif_user->kontrak_kerja)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                // return response()->json($nama_menu);
                // dd($nama_menu);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'karyawans',
                    'activity' => 'update',
                    'description' => 'menonaktifkan karyawan ' . $nonaktif_user->name,
                    'keterangan' => 'Success',
                ]);
            } else {
                return response()->json([
                    'code' => 409,
                    'status'  => false,
                    'message' => 'Karyawan ' . $nonaktif_user->name . ' sudah terdaftar sebagai karyawan nonaktif!'
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }
    public function pengajuan_profil(Request $request, $holding)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();
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
        return view('admin.karyawan.pengajuan_profil', [
            'holding' => $getHolding,
            'menus' => $menus
        ]);
    }
    public function dt_pengajuan_profil($holding)
    {
        $table = KaryawanTemp::where('holding', $holding)->whereNull('status')->get();
        // return response()->json([
        //     'holding_param' => $getHolding,
        //     'is_ajax' => request()->ajax(),
        // ]);
        // // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('aksi', function ($row) {

                    if ($row->status == null) {
                        if ($row->agama == 1) {
                            $agama = 'ISLAM';
                        } elseif ($row->agama == 2) {
                            $agama = 'KRISTEN';
                        } elseif ($row->agama == 3) {
                            $agama = 'KATOLIK';
                        } elseif ($row->agama == 4) {
                            $agama = 'HINDU';
                        } elseif ($row->agama == 5) {
                            $agama = 'BUDHA';
                        } elseif ($row->agama == 6) {
                            $agama = 'KONGHUCU';
                        }

                        if ($row->status_nikah == 1) {
                            $status_nikah = 'BELUM KAWIN';
                        } elseif ($row->status_nikah == 2) {
                            $status_nikah = 'KAWIN';
                        } elseif ($row->status_nikah == 3) {
                            $status_nikah = 'CERAI HIDUP';
                        } elseif ($row->status_nikah == 4) {
                            $status_nikah = 'CERAI MATI';
                        }
                        $btn = '<button id="btn_open_profil" 
                        data-id="' . $row->id . '" 
                        data-id_karyawan="' . $row->id_karyawan . '" 
                        data-nik="' . $row->nik . '" 
                        data-ktp="' . $row->ktp . '" 
                        data-name="' . $row->name . '" 
                        data-tempat_lahir="' . $row->tempat_lahir . '" 
                        data-tgl_lahir="' . $row->tgl_lahir->format('d-m-Y') . '" 
                        data-agama="' . $agama . '" 
                        data-status_nikah="' . $status_nikah . '" 
                        data-jumlah_anak="' . $row->jumlah_anak . '" 
                        data-no_kk="' . $row->no_kk . '" 
                        data-tgl_terbit_kk="' . $row->tgl_terbit_kk->format('d-m-Y') . '" 
                        data-kk="' . $row->kk . '" 
                        data-email="' . $row->email . '" 
                        data-nomor_wa="' . $row->nomor_wa . '" 
                        data-telepon="' . $row->telepon . '" 
                        data-status_nomor="' . $row->status_nomor . '" 
                        data-alasan="' . $row->alasan . '" 
                        data-approval="' . $row->approval . '" 
                        data-status="' . $row->status . '" 
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="mdi mdi-eye-circle-outline"></span></button>';
                        return $btn;
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == null) {
                        $status = '<span class="badge bg-label-info">Belum Dilihat</span>';
                    } elseif ($row->status == 1) {
                        $status = '<span class="badge bg-label-success">Disetujui</span>';
                    } elseif ($row->status == 2) {
                        $status = '<span class="badge bg-label-warning">Ditolak</span>';
                    }
                    return $status;
                })
                ->rawColumns(['aksi', 'created_at', 'status'])
                ->make(true);
        }
    }
    public function dt_pengajuan_history($holding)
    {
        $table = KaryawanTemp::where('holding', $holding)->whereNotNull('status')->get();
        // return response()->json([
        //     'holding_param' => $getHolding,
        //     'is_ajax' => request()->ajax(),
        // ]);
        // // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('aksi', function ($row) {

                    if ($row->status == null) {
                        if ($row->agama == 1) {
                            $agama = 'ISLAM';
                        } elseif ($row->agama == 2) {
                            $agama = 'KRISTEN';
                        } elseif ($row->agama == 3) {
                            $agama = 'KATOLIK';
                        } elseif ($row->agama == 4) {
                            $agama = 'HINDU';
                        } elseif ($row->agama == 5) {
                            $agama = 'BUDHA';
                        } elseif ($row->agama == 6) {
                            $agama = 'KONGHUCU';
                        }

                        if ($row->status_nikah == 1) {
                            $status_nikah = 'BELUM KAWIN';
                        } elseif ($row->status_nikah == 2) {
                            $status_nikah = 'KAWIN';
                        } elseif ($row->status_nikah == 3) {
                            $status_nikah = 'CERAI HIDUP';
                        } elseif ($row->status_nikah == 4) {
                            $status_nikah = 'CERAI MATI';
                        }
                        $btn = '<button id="btn_open_profil" 
                        data-id="' . $row->id . '" 
                        data-id_karyawan="' . $row->id_karyawan . '" 
                        data-nik="' . $row->nik . '" 
                        data-ktp="' . $row->ktp . '" 
                        data-name="' . $row->name . '" 
                        data-tempat_lahir="' . $row->tempat_lahir . '" 
                        data-tgl_lahir="' . $row->tgl_lahir->format('d-m-Y') . '" 
                        data-agama="' . $agama . '" 
                        data-status_nikah="' . $status_nikah . '" 
                        data-jumlah_anak="' . $row->jumlah_anak . '" 
                        data-no_kk="' . $row->no_kk . '" 
                        data-tgl_terbit_kk="' . $row->tgl_terbit_kk->format('d-m-Y') . '" 
                        data-kk="' . $row->kk . '" 
                        data-email="' . $row->email . '" 
                        data-nomor_wa="' . $row->nomor_wa . '" 
                        data-telepon="' . $row->telepon . '" 
                        data-status_nomor="' . $row->status_nomor . '" 
                        data-alasan="' . $row->alasan . '" 
                        data-approval="' . $row->approval . '" 
                        data-status="' . $row->status . '" 
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="mdi mdi-eye-circle-outline"></span></button>';
                        return $btn;
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == null) {
                        $status = '<span class="badge bg-label-info">Belum Dilihat</span>';
                    } elseif ($row->status == 1) {
                        $status = '<span class="badge bg-label-success">Disetujui</span>';
                    } elseif ($row->status == 2) {
                        $status = '<span class="badge bg-label-warning">Ditolak</span>';
                    }
                    return $status;
                })
                ->rawColumns(['aksi', 'created_at', 'status'])
                ->make(true);
        }
    }
    public function dt_pengajuan_alamat($holding)
    {
        $table = KaryawanTempAlamat::where('holding', $holding)->whereNull('status')->get();
        // return response()->json([
        //     'holding_param' => $getHolding,
        //     'is_ajax' => request()->ajax(),
        // ]);
        // // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('aksi', function ($row) {

                    if ($row->status == null) {

                        $btn = '<button id="btn_open_alamat" 
                        data-id="' . $row->id . '" 
                        data-id_karyawan="' . $row->id_karyawan . '" 
                        data-rt="' . $row->rt . '" 
                        data-rw="' . $row->rw . '" 
                        data-desa="' . $row->Desa->name . '" 
                        data-kecamatan="' . $row->Kecamatan->name . '" 
                        data-kabupaten="' . $row->Kabupaten->name . '" 
                        data-provinsi="' . $row->Provinsi->name . '" 
                        data-detail_alamat="' . $row->detail_alamat . '" 
                        data-status_alamat="' . $row->status_alamat . '" 
                        data-rt_domisili="' . ($row->rt_domisili ?? '-') . '" 
                        data-rw_domisili="' . ($row->rw_domisili ?? '-') . '" 
                        data-desa_domisili="' . ($row->DesaNOW?->name ?? '-') . '" 
                        data-kecamatan_domisili="' . ($row->KecamatanNOW?->name ?? '-') . '" 
                        data-kabupaten_domisili="' . ($row->KabupatenNOW?->name ?? '-') . '" 
                        data-provinsi_domisili="' . ($row->ProvinsiNOW?->name ?? '-') . '" 
                        data-detail_alamat_domisili="' . ($row->detail_alamat_domisili ?? '-') . '"  
                      
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="mdi mdi-eye-circle-outline"></span></button>';
                        return $btn;
                    }
                    return '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->Karyawan->name ?? '-';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == null) {
                        $status = '<span class="badge bg-label-info">Belum Dilihat</span>';
                    } elseif ($row->status == 1) {
                        $status = '<span class="badge bg-label-success">Disetujui</span>';
                    } elseif ($row->status == 2) {
                        $status = '<span class="badge bg-label-warning">Ditolak</span>';
                    }
                    return $status;
                })
                ->rawColumns([
                    'aksi',
                    'name',
                    'created_at',
                    'status'
                ])
                ->make(true);
        }
    }
    public function dt_pengajuan_alamat_history($holding)
    {
        $table = KaryawanTempAlamat::where('holding', $holding)->whereNotNull('status')->get();
        // return response()->json([
        //     'holding_param' => $getHolding,
        //     'is_ajax' => request()->ajax(),
        // ]);
        // // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('aksi', function ($row) {

                    if ($row->status == null) {

                        $btn = '<button id="btn_open_alamat" 
                        data-id="' . $row->id . '" 
                        data-id_karyawan="' . $row->id_karyawan . '" 
                        data-rt="' . $row->rt . '" 
                        data-rw="' . $row->rw . '" 
                        data-desa="' . $row->Desa->name . '" 
                        data-kecamatan="' . $row->Kecamatan->name . '" 
                        data-kabupaten="' . $row->Kabupaten->name . '" 
                        data-provinsi="' . $row->Provinsi->name . '" 
                        data-detail_alamat="' . $row->detail_alamat . '" 
                        data-status_alamat="' . $row->status_alamat . '" 
                        data-rt_domisili="' . $row->rt_domisili . '" 
                        data-rw_domisili="' . $row->rw_domisili . '" 
                        data-desa_domisili="' . $row->DesaNOW->name . '" 
                        data-kecamatan_domisili="' . $row->KecamatanNOW->name . '" 
                        data-kabupaten_domisili="' . $row->KabupatenNOW->name . '" 
                        data-provinsi_domisili="' . $row->ProvinsiNOW->name . '" 
                        data-detail_alamat_domisili="' . $row->detail_alamat_domisili . '" 
                      
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="mdi mdi-eye-circle-outline"></span></button>';
                        return $btn;
                    }
                    return '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->Karyawan->name;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == null) {
                        $status = '<span class="badge bg-label-info">Belum Dilihat</span>';
                    } elseif ($row->status == 1) {
                        $status = '<span class="badge bg-label-success">Disetujui</span>';
                    } elseif ($row->status == 2) {
                        $status = '<span class="badge bg-label-warning">Ditolak</span>';
                    }
                    return $status;
                })
                ->rawColumns([
                    'aksi',
                    'name',
                    'created_at',
                    'status'
                ])
                ->make(true);
        }
    }
    public function pengajuan_profil_update(Request $request)
    {
        $karyawan_temp = KaryawanTemp::where('id', $request->id)->first();
        $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
        try {

            $user_name = User::where('id', $request->user()->id)->first();
            $karyawan_temp->update([
                'status' => $request->status,
                'approval' => $request->user()->id,
                'approval_name' => $user_name->username,
            ]);
            if ($karyawan_temp->ktp != $karyawan->ktp) {
                if (Storage::disk('ktp')->exists($karyawan->ktp)) {
                    Storage::disk('ktp')->delete($karyawan->ktp);
                }
            }
            if ($karyawan_temp->kk != $karyawan->kk) {
                if (Storage::disk('kk')->exists($karyawan->kk)) {
                    Storage::disk('kk')->delete($karyawan->kk);
                }
            }
            $karyawan->update([
                'nik' => $karyawan_temp->nik,
                'ktp' => $karyawan_temp->ktp,
                'name' => $karyawan_temp->name,
                'tempat_lahir' => $karyawan_temp->tempat_lahir,
                'tgl_lahir' => $karyawan_temp->tgl_lahir,
                'agama' => $karyawan_temp->agama,
                'gender' => $karyawan_temp->gender,
                'status_nikah' => $karyawan_temp->status_nikah,
                'jumlah_anak' => $karyawan_temp->jumlah_anak,
                'no_kk' => $karyawan_temp->no_kk,
                'tgl_terbit_kk' => $karyawan_temp->tgl_terbit_kk,
                'kk' => $karyawan_temp->kk,
                'email' => $karyawan_temp->email,
                'nomor_wa' => $karyawan_temp->nomor_wa,
                'telepon' => $karyawan_temp->telepon,
                'status_nomor' => $karyawan_temp->status_nomor,
                'alasan' => $karyawan_temp->alasan,
                'updated_at' => now()
            ]);
            $holdings = Holding::where('id', $karyawan->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            // return response()->json($nama_menu);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'menyetujui pengajuan edit profil ' . $karyawan_temp->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }
    public function pengajuan_profil_ditolak(Request $request)
    {
        $karyawan_temp = KaryawanTemp::where('id', $request->id)->first();
        $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
        try {

            $user_name = User::where('id', $request->user()->id)->first();
            $karyawan_temp->update([
                'status' => $request->status,
                'approval' => $request->user()->id,
                'approval_name' => $user_name->username,
            ]);
            if ($karyawan_temp->ktp != $karyawan->ktp) {
                Storage::disk('ktp')->delete($karyawan->ktp);
            }
            if ($karyawan_temp->kk != $karyawan->kk) {
                Storage::disk('kk')->delete($karyawan->kk);
            }

            $holdings = Holding::where('id', $karyawan->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            // return response()->json($nama_menu);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'menolak pengajuan edit profil ' . $karyawan_temp->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }

    public function pengajuan_alamat_update(Request $request)
    {
        // response()->json($request->all());

        $karyawan_temp_alamat = KaryawanTempAlamat::where('id', $request->id)->first();
        $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
        // return response()->json([
        //     'debug' => $karyawan_temp_alamat
        // ]);

        try {
            $user_name = User::where('id', $request->user()->id)->first();
            $karyawan_temp_alamat->update([
                'status' => $request->status,
                'approval' => $request->user()->id,
                'approval_name' => $user_name->username,
            ]);
            if ($karyawan_temp_alamat->status_alamat == null) {
                $status_alamat = 'tidak';
            } elseif ($karyawan_temp_alamat->status_alamat != null) {
                $status_alamat = $karyawan_temp_alamat->status_alamat;
            }
            $karyawan->update([
                'rt' => $karyawan_temp_alamat->rt,
                'rw' => $karyawan_temp_alamat->rw,
                'desa' => $karyawan_temp_alamat->desa,
                'kecamatan' => $karyawan_temp_alamat->kecamatan,
                'kabupaten' => $karyawan_temp_alamat->kabupaten,
                'provinsi' => $karyawan_temp_alamat->provinsi,
                'detail_alamat' => $karyawan_temp_alamat->detail_alamat,
                'status_alamat' => $status_alamat,
                'rt_domisili' => $karyawan_temp_alamat->rt_domisili,
                'rw_domisili' => $karyawan_temp_alamat->rw_domisili,
                'desa_domisili' => $karyawan_temp_alamat->desa_domisili,
                'kecamatan_domisili' => $karyawan_temp_alamat->kecamatan_domisili,
                'kabupaten_domisili' => $karyawan_temp_alamat->kabupaten_domisili,
                'provinsi_domisili' => $karyawan_temp_alamat->provinsi_domisili,
                'kode_pos_domisili' => $karyawan_temp_alamat->kode_pos_domisili,
                'detail_alamat_domisili' => $karyawan_temp_alamat->detail_alamat_domisili,
                'updated_at' => now()
            ]);
            $holdings = Holding::where('id', $karyawan->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();

            // return response()->json($nama_menu);
            // return response()->json([
            //     'debug' => $nama_menu,
            // ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'menyetujui pengajuan edit alamat ' . $karyawan->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 400,
                'message' => $e->errors()
            ]);
        }
    }
    public function pengajuan_alamat_ditolak(Request $request)
    {
        $karyawan_temp_alamat = KaryawanTempAlamat::where('id', $request->id)->first();
        $karyawan = Karyawan::where('id', $request->id_karyawan)->first();
        try {

            $user_name = User::where('id', $request->user()->id)->first();
            $karyawan_temp_alamat->update([
                'status' => $request->status,
                'approval' => $request->user()->id,
                'approval_name' => $user_name->username,
            ]);

            $holdings = Holding::where('id', $karyawan->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            // return response()->json($nama_menu);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'menolak pengajuan edit alamat ' . $karyawan->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }
    public function masa_tenggang_kontrak(Request $request, $holding)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();
        // dd($getHolding);

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

        $habis = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('KontrakKerja')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->whereHas('KaryawanKontrakOne', function ($query) {
                $query->whereDate('tanggal_selesai', '<', date('Y-m-d'));
            })
            ->orderBy('id', 'DESC')
            ->count();


        $atas = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('KontrakKerja')
            ->with('KaryawanJabatanOne.Jabatan.LevelJabatan')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->whereHas('KaryawanJabatanOne.Jabatan.LevelJabatan', function ($query) {
                $query->where('level_jabatan', '<', 4);
            })
            ->whereHas('KaryawanKontrakOne', function ($query) {
                $query->whereDate('tanggal_selesai', '>=', today())
                    ->whereDate('tanggal_selesai', '<=', today()->addDays(90));
            })
            ->orderBy('id', 'DESC')
            ->count();

        $tengah = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('KontrakKerja')
            ->with('KaryawanJabatanOne.Jabatan.LevelJabatan')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->whereHas('KaryawanJabatanOne.Jabatan.LevelJabatan', function ($query) {
                $query->where('level_jabatan', '>=', 4);
            })
            ->whereHas('KaryawanKontrakOne', function ($query) {
                $query->whereDate('tanggal_selesai', '>=', today())
                    ->whereDate('tanggal_selesai', '<=', today()->addDays(60));
            })
            ->orderBy('id', 'DESC')
            ->count();
        // dd($tengah);
        return view('admin.karyawan.masa_tenggang_kontrak', [
            // return view('karyawan.index', [
            'title' => 'Karyawan',
            'holding' => $getHolding,
            'menus' => $menus,
            'atas' => $atas,
            'tengah' => $tengah,
            'habis' => $habis,
            // 'bawah' => $bawah
        ]);
    }
    public function update_kontrak_proses(Request $request)
    {
        // dd($request->all());
        if ($request->file_kontrak_kerja == '' || $request->file_kontrak_kerja == NULL) {
            return redirect()->back()->with('error', 'Data Harus Terisi Semua', 1500);
        }
        $extension     = $request->file('file_kontrak_kerja')->extension();
        $file_cv_name         = 'KONTAK_KERJA-' . $request->tgl_mulai_kontrak_baru . '-' . $request->tgl_selesai_kontrak_baru . '_' . $request->id_karyawan . '.' . $extension;
        $path           = Storage::putFileAs('file_kontrak_kerja/', $request->file('file_kontrak_kerja'), $file_cv_name);

        $update_user                                = Karyawan::where('id', $request->id_karyawan)->first();
        $update_user->tgl_mulai_kontrak             = $request->tgl_mulai_kontrak_baru;
        $update_user->tgl_selesai_kontrak           = $request->tgl_selesai_kontrak_baru;
        $update_user->lama_kontrak_kerja            = $request->lama_kontrak_baru;
        $update_user->file_kontrak_kerja            = $file_cv_name;
        $update_user->update();

        return redirect()->back()->with('success', 'Data Berhasil di Simpan');
    }
    private function normalize($value)
    {
        return in_array($value, ['', '0', 0, null], true) ? null : $value;
    }
    public function ImportKaryawan(Request $request, $holding)
    {
        // $holding = request()->segment(count(request()->segments()));
        // $query = Excel::import(new KaryawanImport, $request->file_excel);
        // if ($query) {
        //     return redirect()->back()->with('success', 'Import Karyawan Sukses');
        // }
        $holding = Holding::where('holding_code', $holding)->first();
        try {
            $file = $request->file('file_excel');
            // dd($file);
            $users = (new FastExcel)->import($file->path(), function ($line) use ($holding) {
                // $rowNumber = 1;
                $rules = [
                    'ID KARYAWAN'    => 'required|numeric',
                    'NAMA LENGKAP'   => 'required',
                    'NIK' => 'required',
                    'AGAMA' => 'required|numeric',
                    // 'EMAIL' => 'required',
                    'TELEPON' => 'required',
                    'NOMOR WA' => 'required',
                    'STATUS AKTIF'   => 'required',
                    'TEMPAT LAHIR' => 'required',
                    'TANGGAL LAHIR' => 'required',
                    'KELAMIN' => 'required',
                    'STATUS PERNIKAHAN' => 'required',
                    // 'JENJANG SEKOLAH' => 'required',
                    // 'NAMA INSTANSI' => 'required',
                    // 'JURUSAN' => 'required',
                    // 'TAHUN MASUK SEKOLAH' => 'required',
                    // 'TAHUN KELUAR SEKOLAH' => 'required',
                    'KATEGORI KARYAWAN' => 'required',
                    // 'TANGGAL BERGABUNG' => 'required',
                    // 'KONTRAK KERJA' => 'required',
                    // 'SITE KERJA' => 'required',
                    // 'TGL MULAI KONTRAK' => 'required',
                    // 'LAMA KONTRAK' => 'required',
                    // 'TGL SELESAI KONTRAK' => 'required',
                    // 'DEPARTEMEN' => 'required',
                    // 'DIVISI' => 'required',
                    // 'BAGIAN' => 'required',
                    // 'JABATAN' => 'required',
                    // 'SHIFTING' => 'required',
                    // 'STATUS AKTIF' => 'required',
                    // 'NAMA BANK' => 'required',
                    // 'PEMILIK REKENING' => 'required',
                    // 'NOMOR REKENING' => 'required',
                    // 'PTKP' => 'required',
                    // 'PUNYA NPWP' => 'required',
                    'BPJS KETENAGAKERJAAN'  => 'required',
                    // 'BPJS PENSIUN'          => 'required',
                    // 'BPJS KESEHATAN'        => 'required',
                    // 'NO NPWP' => 'required',

                ];
                $customMessages = [
                    'required' => ':attribute tidak boleh kosong.',
                    'numeric' => ':attribute harus berupa angka.',
                ];

                $validator = Validator::make($line, $rules, $customMessages);

                // Set nama attribute untuk custom label
                $validator->setAttributeNames([
                    'ID KARYAWAN'           => 'ID KARYAWAN',
                    'NAMA LENGKAP'          => 'NAMA LENGKAP',
                    'NIK'                   => 'NIK',
                    'AGAMA'                 => 'AGAMA',
                    'EMAIL'                 => 'EMAIL',
                    'TELEPON'               => 'TELEPON',
                    'NOMOR WA'              => 'NOMOR WA',
                    'STATUS AKTIF'          => 'STATUS AKTIF',
                    'TEMPAT LAHIR'          => 'TEMPAT LAHIR',
                    'TANGGAL LAHIR'         => 'TANGGAL LAHIR',
                    'KELAMIN'               => 'KELAMIN',
                    'STATUS PERNIKAHAN'     => 'STATUS PERNIKAHAN',

                    'JENJANG SEKOLAH'       => 'JENJANG SEKOLAH',
                    'NAMA INSTANSI'         => 'NAMA INSTANSI',
                    'JURUSAN'               => 'JURUSAN',
                    'TAHUN MASUK SEKOLAH'   => 'TAHUN MASUK SEKOLAH',
                    'TAHUN KELUAR SEKOLAH'  => 'TAHUN KELUAR SEKOLAH',

                    'KATEGORI KARYAWAN'     => 'KATEGORI KARYAWAN',
                    'TANGGAL BERGABUNG'     => 'TANGGAL BERGABUNG',
                    'KONTRAK KERJA'         => 'KONTRAK KERJA',
                    'SITE KERJA'            => 'SITE KERJA',
                    'TGL MULAI KONTRAK'     => 'TGL MULAI KONTRAK',
                    'LAMA KONTRAK'          => 'LAMA KONTRAK',
                    'TGL SELESAI KONTRAK'   => 'TGL SELESAI KONTRAK',

                    'DEPARTEMEN'            => 'DEPARTEMEN',
                    'DIVISI'                => 'DIVISI',
                    'BAGIAN'                => 'BAGIAN',
                    'JABATAN'               => 'JABATAN',

                    'SHIFTING'              => 'SHIFTING',
                    'STATUS AKTIF'          => 'STATUS AKTIF',
                    'NAMA BANK'             => 'NAMA BANK',
                    'PEMILIK REKENING'      => 'PEMILIK REKENING',
                    'NOMOR REKENING'        => 'NOMOR REKENING',
                    'PTKP'                  => 'PTKP',
                    'PUNYA NPWP'            => 'PUNYA NPWP',
                    // 'NO NPWP'               => 'NO NPWP',

                    'BPJS KETENAGAKERJAAN'  => 'BPJS KETENAGAKERJAAN',
                    'BPJS PENSIUN'          => 'BPJS PENSIUN',
                    'BPJS KESEHATAN'        => 'BPJS KESEHATAN',

                ]);

                if ($validator->fails()) {

                    // Ambil pesan pertama
                    $errorMessage = $validator->errors()->first();

                    // Tambahkan info baris
                    $errorMessage = $errorMessage;

                    throw ValidationException::withMessages([
                        'excel_error' => [$errorMessage]
                    ]);
                }
                $existing = Karyawan::where('nomor_identitas_karyawan', $line['ID KARYAWAN'])->first();
                if ($existing) {
                    return null;
                }
                $id_karyawan = UUID::uuid4();
                Karyawan::create([
                    'id'                       => $id_karyawan,
                    'nomor_identitas_karyawan' => $this->normalize($line['ID KARYAWAN'] ?? null),
                    'name'                     => $this->normalize($line['NAMA LENGKAP'] ?? null),
                    'nik'                      => $this->normalize($line['NIK'] ?? null),
                    'agama'                    => $this->normalize($line['AGAMA'] ?? null),
                    'email'                    => $this->normalize($line['EMAIL'] ?? null),
                    'telepon'                  => $this->normalize($line['TELEPON'] ?? null),
                    'nomor_wa'                 => $this->normalize($line['NOMOR WA'] ?? null),
                    'status_aktif'             => $this->normalize($line['STATUS AKTIF'] ?? null),
                    'tempat_lahir'             => $this->normalize($line['TEMPAT LAHIR'] ?? null),
                    'tgl_lahir'                => $this->normalize($line['TANGGAL LAHIR'] ?? null),
                    'gender'                   => $this->normalize($line['KELAMIN'] ?? null),
                    'status_nikah'             => $this->normalize($line['STATUS PERNIKAHAN'] ?? null),

                    'kategori'                 => $this->normalize($line['KATEGORI KARYAWAN'] ?? null),
                    'kontrak_kerja'            => $this->normalize($line['KONTRAK KERJA'] ?? null),
                    'tgl_join'                 => $this->normalize($line['TANGGAL BERGABUNG'] ?? null),
                    'penempatan_kerja'         => $this->normalize($line['SITE KERJA'] ?? null),

                    'shifting'                 => $this->normalize($line['SHIFTING'] ?? null),

                    'nama_bank'                => $this->normalize($line['NAMA BANK'] ?? null),
                    'nama_pemilik_rekening'    => $this->normalize($line['PEMILIK REKENING'] ?? null),
                    'nomor_rekening'           => $this->normalize($line['NOMOR REKENING'] ?? null),

                    'status_npwp'              => $this->normalize($line['PUNYA NPWP'] ?? null),
                    'npwp'                     => $this->normalize($line['NPWP'] ?? null),
                    'ptkp'                     => $this->normalize($line['PTKP'] ?? null),

                    'bpjs_ketenagakerjaan'     => $this->normalize($line['BPJS KETENAGAKERJAAN'] ?? null),
                    'no_bpjs_ketenagakerjaan'  => $this->normalize($line['NO BPJS KETENAGAKERJAAN'] ?? null),
                    'nama_pemilik_bpjs_ketenagakerjaan' => $this->normalize($line['NAMA BPJS KETENAGAKERJAAN'] ?? null),
                    'bpjs_pensiun'             => $this->normalize($line['BPJS PENSIUN'] ?? null),
                    'bpjs_kesehatan'           => $this->normalize($line['BPJS KESEHATAN'] ?? null),
                    'nama_pemilik_bpjs_kesehatan' => $this->normalize($line['NAMA BPJS KESEHATAN'] ?? null),
                    'no_bpjs_kesehatan'        => $this->normalize($line['NO BPJS KESEHATAN'] ?? null),
                    'kelas_bpjs'               => $this->normalize($line['KELAS BPJS KESEHATAN'] ?? null),

                    'created_at'               => now(),
                ]);

                KaryawanPendidikan::create([
                    'id_pendidikan' => Uuid::uuid4(),
                    'id_karyawan'   => $id_karyawan,
                    'institusi'     => $this->normalize($line['NAMA INSTANSI'] ?? null),
                    'jurusan'       => $this->normalize($line['JURUSAN'] ?? null),
                    'jenjang'       => $this->normalize($line['JENJANG SEKOLAH'] ?? null),
                    'tanggal_masuk' => $this->normalize($line['TAHUN MASUK SEKOLAH'] ?? null),
                    'tanggal_keluar' => $this->normalize($line['TAHUN KELUAR SEKOLAH'] ?? null),
                    'created_at'    => now(),
                ]);
                KaryawanJabatan::create([
                    'id_jbt_karyawan' => Uuid::uuid4(),
                    'karyawan_id'     => $id_karyawan,
                    'departemen_id'   => $this->normalize($line['DEPARTEMEN'] ?? null),
                    'jabatan_id'      => $this->normalize($line['JABATAN'] ?? null),
                    'divisi_id'       => $this->normalize($line['DIVISI'] ?? null),
                    'bagian_id'       => $this->normalize($line['BAGIAN'] ?? null),
                    'holding_id'      => $this->normalize($line['KONTRAK KERJA'] ?? null),
                    'main'            => 1,
                    'is_active'       => 1,
                    'created_at'      => now(),
                ]);
                KaryawanKontrak::create([
                    'id_jbt_karyawan' => Uuid::uuid4(),
                    'karyawan_id'     => $id_karyawan,
                    'tanggal_mulai'   => $this->normalize($line['TGL MULAI KONTRAK'] ?? null),
                    'lama_kontrak'    => $this->normalize($line['LAMA KONTRAK'] ?? null),
                    'tanggal_selesai' => $this->normalize($line['TGL SELESAI KONTRAK'] ?? null),
                    'created_at'      => now(),
                ]);
            });
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil ditambahkan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'errors' => $e->errors()
            ]);
        }
    }
    public function ExportKaryawan(Request $request, $holding)
    {
        $date = date('YmdHis');
        $get_holding = Holding::where('holding_code', $holding)->first();
        if ($get_holding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 4000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan', 4000);
        }
        return Excel::download(new KaryawanExport($get_holding), 'Data Karyawan_' . $get_holding->holding_category . '_' . $date . '.xlsx');
    }
    public function download_pdf_karyawan(Request $request)
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
        // 'user' => Karyawan::leftJoin('departemens as a', 'a.id', 'karyawans.dept_id')
        //     ->leftJoin('divisis as b', 'b.id', 'karyawans.divisi_id')
        //     ->leftJoin('bagians as c', 'c.id', 'karyawans.bagian_id')
        //     ->leftJoin('jabatans as d', 'd.id', 'karyawans.jabatan_id')
        //     ->leftJoin('divisis as e', 'e.id', 'karyawans.divisi1_id')
        //     ->leftJoin('bagians as f', 'f.id', 'karyawans.bagian1_id')
        //     ->leftJoin('jabatans as g', 'g.id', 'karyawans.jabatan1_id')
        //     ->leftJoin('divisis as h', 'h.id', 'karyawans.divisi2_id')
        //     ->leftJoin('bagians as i', 'i.id', 'karyawans.bagian2_id')
        //     ->leftJoin('jabatans as j', 'j.id', 'karyawans.jabatan2_id')
        //     ->leftJoin('divisis as k', 'k.id', 'karyawans.divisi3_id')
        //     ->leftJoin('bagians as l', 'l.id', 'karyawans.bagian3_id')
        //     ->leftJoin('jabatans as m', 'm.id', 'karyawans.jabatan3_id')
        //     ->leftJoin('divisis as n', 'n.id', 'karyawans.divisi4_id')
        //     ->leftJoin('bagians as o', 'o.id', 'karyawans.bagian4_id')
        //     ->leftJoin('jabatans as p', 'p.id', 'karyawans.jabatan4_id')
        //     ->leftJoin('indonesia_provinces as q', 'q.code', 'karyawans.provinsi')
        //     ->leftJoin('indonesia_cities as r', 'r.code', 'karyawans.kabupaten')
        //     ->leftJoin('indonesia_districts as s', 's.code', 'karyawans.kecamatan')
        //     ->leftJoin('indonesia_villages as t', 't.code', 'karyawans.desa')
        //     ->where('karyawans.kontrak_kerja', $cek_holding)
        //     ->where('karyawans.status_aktif', 'AKTIF')
        //     ->select('nomor_identitas_karyawan', 'karyawans.name', 'nik', 'npwp', 'fullname', 'motto', 'email', 'telepon', 'tempat_lahir', 'tgl_lahir', 'gender', 'tgl_join', 'status_nikah', 'q.name as nama_provinsi', 'r.name as nama_kabupaten', 's.name as nama_kecamatan', 't.name as nama_desa', 'rt', 'rw', 'alamat', 'kuota_cuti_tahunan', 'kategori', 'lama_kontrak_kerja', 'tgl_mulai_kontrak', 'tgl_selesai_kontrak', 'kontrak_kerja', 'penempatan_kerja', 'site_job', 'nama_bank', 'nomor_rekening', 'a.nama_departemen', 'b.nama_divisi', 'c.nama_bagian', 'd.nama_jabatan', 'e.nama_divisi as nama_divisi1', 'f.nama_bagian as nama_bagian1', 'g.nama_jabatan as nama_jabatan1', 'h.nama_divisi as nama_divisi2', 'i.nama_bagian as nama_bagian2', 'j.nama_jabatan as nama_jabatan2', 'k.nama_divisi as nama_divisi3', 'l.nama_bagian as nama_bagian3', 'm.nama_jabatan as nama_jabatan3', 'n.nama_divisi as nama_divisi4', 'o.nama_bagian as nama_bagian4', 'p.nama_jabatan as nama_jabatan4')
        //     ->orderBy('name', 'ASC')
        //     // ->take(100)
        //     ->get(),
        $data = [
            'user' => Karyawan::With('Departemen')
                ->With('Divisi')
                ->With('Bagian')
                ->With('Jabatan')
                ->where('karyawans.kontrak_kerja', $cek_holding)
                ->where('karyawans.status_aktif', 'AKTIF')
                ->orderBy('name', 'ASC')
                // ->take(100)
                ->get(),
            'holding' => $holding,
            'cek_holding' => $cek_holding,
        ];
        // dd($data);
        $pdf = PDF::loadView('admin/karyawan/cetak_pdf_karyawan', $data)->setPaper('F4', 'landscape');
        return $pdf->stream('DATA KARYAWAN' . $holding . '_' . $date . 'pdf');
    }
    public function datatable_bulanan($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'foto_karyawan',
            'nomor_wa',
            'email',
            'kontrak_kerja',
            'tgl_mulai_kontrak',
            'tgl_selesai_kontrak',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('PenempatanKerja')
            ->with('KontrakKerja')
            ->with('KaryawanJabatanOne')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->orderBy('id', 'DESC')
            // ->limit(10)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_divisi', function ($row) {
                    return $row->KaryawanJabatanOne->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('nomor_wa', function ($row) {
                    $nomor = preg_replace('/\D/', '', $row->nomor_wa); // hapus non-angka

                    if (str_starts_with($nomor, '0')) {
                        $nomor = '62' . substr($nomor, 1);
                    }
                    $wa = '
                    <a href="https://wa.me/' . preg_replace('/^0/', '62', $nomor) . '" type="button" class="btn btn-success btn-sm" target="_blank">
                   
                        <span class="tf-icons mdi mdi-whatsapp me-2"></span> ' . $row->nomor_wa . '

                    </a>';
                    return $wa;
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->KaryawanJabatanOne->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('kontrak_kerja', function ($row) use ($getHolding) {
                    if ($row->KontrakKerja == '') {
                        $kontrak_kerja = $row->kontrak_kerja;
                    } else {
                        $kontrak_kerja = $row->KontrakKerja->holding_name;
                    }
                    return $kontrak_kerja;
                })
                ->addColumn('penempatan_kerja', function ($row) use ($getHolding) {
                    if ($row->PenempatanKerja == '') {
                        $penempatan_kerja = $row->penempatan_kerja;
                    } else {
                        $penempatan_kerja = $row->PenempatanKerja->site_name;
                    }
                    return $penempatan_kerja;
                })
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('option', function ($row) use ($getHolding) {
                    // $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $row->id)
                    //     ->with('Jabatan')
                    //     ->with('Divisi')
                    //     ->where('main', 1)->first();

                    $btn = '<a href="/karyawan/detail/' . $row->id . '/' . $getHolding->holding_code . '" type="button" class="btn btn-icon btn-success waves-effect waves-light"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i></a>';
                    // $btn = '<button id="btndetail_karyawan" data-id="' . $row->id . '" data-holding="' . $getHolding->holding_code . '" class="btn btn-icon btn-success waves-effect waves-light"><span class="tf-icons mdi mdi-eye-outline"></span></button>';
                    // $btn = $btn . '<button id="btn_mapping_shift" data-id="' . $row->id . '" data-holding="' . $holding_category . '" type="button" class="btn btn-icon btn-info waves-effect waves-light"><span class="tf-icons mdi mdi-clock-outline"></span></button>';
                    $btn = $btn . '<button id="btn_non_aktif_karyawan" 
                        data-status_aktif="' . $row->status_aktif . '" 
                        data-foto="' . $row->foto_karyawan . '" 
                        data-id="' . $row->id . '" 
                        data-tgl_mulai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_mulai ?? '-') . '" 
                        data-tgl_selesai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_selesai ?? '-') . '" 
                        data-nama="' . $row->name . '" 
                        data-holding="' . $getHolding->id . '" 
                        data-penempatan_kerja="' . ($row->PenempatanKerja->site_name ?? '-') . '" 
                        data-kontrak_kerja="' . $row->KontrakKerja->holding_name . '" 
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-account-multiple-remove-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['nama_jabatan', 'nomor_wa', 'nama_divisi', 'kontrak_kerja', 'penempatan_kerja', 'option'])
                ->make(true);
        }
    }
    public function datatable_bulanan_tenggang($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('KontrakKerja')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->whereHas('KaryawanKontrakOne', function ($query) {
                $query->whereDate('tanggal_selesai', '<', date('Y-m-d'));
            })
            ->orderBy('id', 'DESC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_divisi', function ($row) {
                    return $row->KaryawanJabatanOne->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('nomor_wa', function ($row) {
                    $nomor = preg_replace('/\D/', '', $row->nomor_wa); // hapus non-angka

                    if (str_starts_with($nomor, '0')) {
                        $nomor = '62' . substr($nomor, 1);
                    }
                    $wa = '
                    <a href="https://wa.me/' . preg_replace('/^0/', '62', $nomor) . '" type="button" class="btn btn-success btn-sm" target="_blank">
                   
                        <span class="tf-icons mdi mdi-whatsapp me-2"></span> ' . $row->nomor_wa . '

                    </a>';
                    return $wa;
                })

                ->addColumn('kontrak_kerja', function ($row) use ($getHolding) {
                    if ($row->KontrakKerja == '') {
                        $kontrak_kerja = $row->kontrak_kerja;
                    } else {
                        $kontrak_kerja = $row->KontrakKerja->holding_name;
                    }
                    return $kontrak_kerja;
                })
                ->addColumn('mulai_kontrak', function ($row) {
                    return $row->KaryawanKontrakOne->tanggal_mulai ?? '-';
                })
                ->addColumn('selesai_kontrak', function ($row) {
                    return $row->KaryawanKontrakOne->tanggal_selesai ?? '-';
                })
                ->addColumn('option', function ($row) use ($getHolding) {
                    // $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $row->id)
                    //     ->with('Jabatan')
                    //     ->with('Divisi')
                    //     ->where('main', 1)->first();

                    $btn = '<a href="/karyawan/detail/' . $row->id . '/' . $getHolding->holding_code . '" type="button" class="btn btn-icon btn-success waves-effect waves-light"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i></a>';
                    // $btn = '<button id="btndetail_karyawan" data-id="' . $row->id . '" data-holding="' . $getHolding->holding_code . '" class="btn btn-icon btn-success waves-effect waves-light"><span class="tf-icons mdi mdi-eye-outline"></span></button>';
                    // $btn = $btn . '<button id="btn_mapping_shift" data-id="' . $row->id . '" data-holding="' . $holding_category . '" type="button" class="btn btn-icon btn-info waves-effect waves-light"><span class="tf-icons mdi mdi-clock-outline"></span></button>';
                    $btn = $btn . '<button id="btn_non_aktif_karyawan" 
                        data-status_aktif="' . $row->status_aktif . '" 
                        data-foto="' . $row->foto_karyawan . '" 
                        data-id="' . $row->id . '" 
                        data-tgl_mulai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_mulai ?? '-') . '" 
                        data-tgl_selesai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_selesai ?? '-') . '" 
                        data-nama="' . $row->name . '" 
                        data-holding="' . $getHolding->id . '" 
                        data-penempatan_kerja="' . ($row->PenempatanKerja->site_name ?? '-') . '" 
                        data-kontrak_kerja="' . $row->KontrakKerja->holding_name . '" 
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-account-multiple-remove-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['selesai_kontrak', 'nomor_wa', 'mulai_kontrak', 'kontrak_kerja', 'penempatan_kerja', 'option'])
                ->make(true);
        }
    }
    public function datatable_tenggang_atasan($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('KontrakKerja')
            ->with('KaryawanJabatanOne.Jabatan.LevelJabatan')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->whereHas('KaryawanJabatanOne.Jabatan.LevelJabatan', function ($query) {
                $query->where('level_jabatan', '<', 4);
            })
            ->whereHas('KaryawanKontrakOne', function ($query) {
                $query->whereDate('tanggal_selesai', '>=', today())
                    ->whereDate('tanggal_selesai', '<=', today()->addDays(90));
            })
            ->orderBy('id', 'DESC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('nomor_wa', function ($row) {
                    $nomor = preg_replace('/\D/', '', $row->nomor_wa); // hapus non-angka

                    if (str_starts_with($nomor, '0')) {
                        $nomor = '62' . substr($nomor, 1);
                    }
                    $wa = '
                    <a href="https://wa.me/' . preg_replace('/^0/', '62', $nomor) . '" type="button" class="btn btn-success btn-sm" target="_blank">
                   
                        <span class="tf-icons mdi mdi-whatsapp me-2"></span> ' . $row->nomor_wa . '

                    </a>';
                    return $wa;
                })

                ->addColumn('kontrak_kerja', function ($row) use ($getHolding) {
                    if ($row->KontrakKerja == '') {
                        $kontrak_kerja = $row->kontrak_kerja;
                    } else {
                        $kontrak_kerja = $row->KontrakKerja->holding_name;
                    }
                    return $kontrak_kerja;
                })
                ->addColumn('mulai_kontrak', function ($row) {
                    return $row->KaryawanKontrakOne->tanggal_mulai ?? '-';
                })
                ->addColumn('selesai_kontrak', function ($row) {
                    return $row->KaryawanKontrakOne->tanggal_selesai ?? '-';
                })
                ->addColumn('option', function ($row) use ($getHolding) {
                    // $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $row->id)
                    //     ->with('Jabatan')
                    //     ->with('Divisi')
                    //     ->where('main', 1)->first();

                    $btn = '<a href="/karyawan/detail/' . $row->id . '/' . $getHolding->holding_code . '" type="button" class="btn btn-icon btn-success waves-effect waves-light"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i></a>';
                    // $btn = '<button id="btndetail_karyawan" data-id="' . $row->id . '" data-holding="' . $getHolding->holding_code . '" class="btn btn-icon btn-success waves-effect waves-light"><span class="tf-icons mdi mdi-eye-outline"></span></button>';
                    // $btn = $btn . '<button id="btn_mapping_shift" data-id="' . $row->id . '" data-holding="' . $holding_category . '" type="button" class="btn btn-icon btn-info waves-effect waves-light"><span class="tf-icons mdi mdi-clock-outline"></span></button>';
                    $btn = $btn . '<button id="btn_non_aktif_karyawan" 
                        data-status_aktif="' . $row->status_aktif . '" 
                        data-foto="' . $row->foto . '" 
                        data-id="' . $row->id . '" 
                        data-tgl_mulai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_mulai ?? '-') . '" 
                        data-tgl_selesai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_selesai ?? '-') . '" 
                        data-nama="' . $row->name . '" 
                        data-holding="' . $getHolding->id . '" 
                        data-penempatan_kerja="' . ($row->PenempatanKerja->site_name ?? '-') . '" 
                        data-kontrak_kerja="' . $row->KontrakKerja->holding_name . '" 
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-account-multiple-remove-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['nama_jabatan', 'selesai_kontrak', 'nomor_wa', 'mulai_kontrak', 'kontrak_kerja', 'penempatan_kerja', 'option'])
                ->make(true);
        }
    }
    public function datatable_tenggang_menengah($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'kontrak_kerja',
            'penempatan_kerja',
        )
            ->with('KontrakKerja')
            ->with('KaryawanJabatanOne.Jabatan.LevelJabatan')
            ->with('KaryawanKontrakOne')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->whereHas('KaryawanJabatanOne.Jabatan.LevelJabatan', function ($query) {
                $query->where('level_jabatan', '>=', 4);
            })
            ->whereHas('KaryawanKontrakOne', function ($query) {
                $query->whereDate('tanggal_selesai', '>=', today())
                    ->whereDate('tanggal_selesai', '<=', today()->addDays(60));
            })
            ->orderBy('id', 'DESC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('nomor_wa', function ($row) {
                    $nomor = preg_replace('/\D/', '', $row->nomor_wa); // hapus non-angka

                    if (str_starts_with($nomor, '0')) {
                        $nomor = '62' . substr($nomor, 1);
                    }
                    $wa = '
                    <a href="https://wa.me/' . preg_replace('/^0/', '62', $nomor) . '" type="button" class="btn btn-success btn-sm" target="_blank">
                   
                        <span class="tf-icons mdi mdi-whatsapp me-2"></span> ' . $row->nomor_wa . '

                    </a>';
                    return $wa;
                })

                ->addColumn('kontrak_kerja', function ($row) use ($getHolding) {
                    if ($row->KontrakKerja == '') {
                        $kontrak_kerja = $row->kontrak_kerja;
                    } else {
                        $kontrak_kerja = $row->KontrakKerja->holding_name;
                    }
                    return $kontrak_kerja;
                })
                ->addColumn('mulai_kontrak', function ($row) {
                    return $row->KaryawanKontrakOne->tanggal_mulai ?? '-';
                })
                ->addColumn('selesai_kontrak', function ($row) {
                    return $row->KaryawanKontrakOne->tanggal_selesai ?? '-';
                })
                ->addColumn('option', function ($row) use ($getHolding) {
                    // $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $row->id)
                    //     ->with('Jabatan')
                    //     ->with('Divisi')
                    //     ->where('main', 1)->first();

                    $btn = '<a href="/karyawan/detail/' . $row->id . '/' . $getHolding->holding_code . '" type="button" class="btn btn-icon btn-success waves-effect waves-light"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i></a>';
                    // $btn = '<button id="btndetail_karyawan" data-id="' . $row->id . '" data-holding="' . $getHolding->holding_code . '" class="btn btn-icon btn-success waves-effect waves-light"><span class="tf-icons mdi mdi-eye-outline"></span></button>';
                    // $btn = $btn . '<button id="btn_mapping_shift" data-id="' . $row->id . '" data-holding="' . $holding_category . '" type="button" class="btn btn-icon btn-info waves-effect waves-light"><span class="tf-icons mdi mdi-clock-outline"></span></button>';
                    $btn = $btn . '<button id="btn_non_aktif_karyawan" 
                        data-status_aktif="' . $row->status_aktif . '" 
                        data-foto="' . $row->foto . '" 
                        data-id="' . $row->id . '" 
                        data-tgl_mulai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_mulai ?? '-') . '" 
                        data-tgl_selesai_kontrak="' . ($row->KaryawanKontrakOne->tanggal_selesai ?? '-') . '" 
                        data-nama="' . $row->name . '" 
                        data-holding="' . $getHolding->id . '" 
                        data-penempatan_kerja="' . ($row->PenempatanKerja->site_name ?? '-') . '" 
                        data-kontrak_kerja="' . $row->KontrakKerja->holding_name . '" 
                        class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-account-multiple-remove-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['nama_jabatan', 'selesai_kontrak', 'nomor_wa', 'mulai_kontrak', 'kontrak_kerja', 'penempatan_kerja', 'option'])
                ->make(true);
        }
    }

    public function datatable_bulanan_nonaktif($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::select(
            'id',
            'nomor_identitas_karyawan',
            'name',
            'telepon',
            'nomor_wa',
            'email',
            'kontrak_kerja',
            'kontrak_kerja',
            'tanggal_nonactive',
            'alasan_nonactive',
        )
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'NON AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->orderBy('id', 'DESC')
            // ->limit(10)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nomor_wa', function ($row) {
                    $nomor = preg_replace('/\D/', '', $row->nomor_wa); // hapus non-angka

                    if (str_starts_with($nomor, '0')) {
                        $nomor = '62' . substr($nomor, 1);
                    }
                    $wa = '
                    <a href="https://wa.me/' . preg_replace('/^0/', '62', $nomor) . '" type="button" class="btn btn-success btn-sm" target="_blank">
                   
                        <span class="tf-icons mdi mdi-whatsapp me-2"></span> ' . $row->nomor_wa . '

                    </a>';
                    return $wa;
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->KaryawanJabatanOne->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('kontrak_kerja', function ($row) use ($getHolding) {
                    if ($row->KontrakKerja == '') {
                        $kontrak_kerja = $row->kontrak_kerja;
                    } else {
                        $kontrak_kerja = $row->KontrakKerja->holding_name;
                    }
                    return $kontrak_kerja;
                })
                ->rawColumns(['nomor_wa', 'kontrak_kerja',])
                ->make(true);
        }
    }
    public function datatable_akan_nonaktif($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = KaryawanNonaktif::orderBy('nama_karyawan', 'ASC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
                    $btn = '<button type="button" id="btn_delete_karyawan" data-id="' . $row->id . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns([
                    'option'
                ])
                ->make(true);
        }
    }
    public function datatable_akan_nonaktif_delete($id, $holding, $menu_id)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        // dd($holding);
        $karyawan_nonaktif = KaryawanNonaktif::find($id);
        if (!$holding || !$karyawan_nonaktif) {
            return response()->json([
                'status' => 0,
                'message' => 'Data karyawan atau holding tidak ditemukan.',
            ], 404);
        }

        $nama_menu = Menu::where('id', $menu_id)->first();
        $user_name = User::where('id', Auth::user()->id)->first();
        Karyawan::where('id', $karyawan_nonaktif->id_karyawan)->update([
            'berhenti' => null,
            'tanggal_nonactive' => null,
            'alasan_nonactive' => null,
        ]);
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $id,
            'holding_id' => $holding->id,
            'holding_category' => $holding->holding_category,
            'table' => 'karyawan_nonaktif',
            'activity' => 'delete',
            'description' => 'menghapus karyawawn ' . $karyawan_nonaktif->name,
            'keterangan' => 'Success',
        ]);

        $karyawan_nonaktif->delete();
        return response()->json(['status' => 1]);
    }
    public function datatable_harian($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $holding_category = $getHolding->holding_category;
        $table = Karyawan::where('kontrak_kerja', $holding_category)->where('kategori', 'Karyawan Harian')->where('status_aktif', 'AKTIF')->orderBy('id', 'DESC')->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) use ($holding_category) {
                    $btn = '<button id="btndetail_karyawan" data-id="' . $row->id . '" data-holding="' . $holding_category . '" class="btn btn-icon btn-success waves-effect waves-light"><span class="tf-icons mdi mdi-eye-outline"></span></button>';
                    $btn = $btn . '<button id="btn_mapping_shift" data-id="' . $row->id . '" data-holding="' . $holding_category . '" type="button" class="btn btn-icon btn-info waves-effect waves-light"><span class="tf-icons mdi mdi-clock-outline"></span></button>';
                    $btn = $btn . '<button id="btn_edit_password" data-id="' . $row->id . '" data-holding="' . $holding_category . '" type="button" class="btn btn-icon btn-secondary waves-effect waves-light"><span class="tf-icons mdi mdi-key-outline"></span></button>';
                    $btn = $btn . '<button type="button" id="btn_delete_karyawan" data-id="' . $row->id . '" data-holding="' . $holding_category . '" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                    return $btn;
                })
                ->rawColumns(['option'])
                ->make(true);
        }
    }

    public function get_kabupaten($id)
    {
        // dd($id);
        $get_kabupaten = Cities::where('province_code', $id)->orderBy('name', 'ASC')->get();
        // return $get_kabupaten;
        echo "<option value=''>Pilih Kabupaten...</option>";
        foreach ($get_kabupaten as $kabupaten) {
            echo "<option value='$kabupaten->code'>$kabupaten->name</option>";
        }
    }
    public function get_kecamatan($id)
    {
        // dd($id);
        $get_desa = District::where('city_code', $id)->orderBy('name', 'ASC')->get();
        // return $get_desa;
        echo "<option value=''>Pilih Kecamatan...</option>";
        foreach ($get_desa as $desa) {
            echo "<option value='$desa->code'>$desa->name</option>";
        }
    }
    public function get_desa($id)
    {
        // dd($id);
        $get_kecamatan = Village::where('district_code', $id)->orderBy('name', 'ASC')->get();
        // return $get_kecamatan;
        echo "<option value=''>Pilih Desa...</option>";
        foreach ($get_kecamatan as $kecamatan) {
            echo "<option value='$kecamatan->code'>$kecamatan->name</option>";
        }
    }
    public function get_atasan(Request $request)
    {
        if ($request->holding == 'sp') {
            $kontrak = 'SP';
        } else if ($request->holding == 'sps') {
            $kontrak = 'SPS';
        } else {
            $kontrak = 'SIP';
        }
        // dd($holding);
        $get_user = Karyawan::where('id', $request->id_karyawan)->first();
        $get_level = Jabatan::Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')->where('jabatans.id', $request->id)->first();
        // dd($get_level->level_jabatan);
        if ($get_level->level_jabatan <= 4) {
            $get_atasan = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan_id')
                ->Join('divisis', 'divisis.id', 'jabatans.divisi_id')
                ->Join('bagians', 'bagians.id', 'jabatans.bagian_id')
                ->Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')
                // ->where('karyawans.penempatan_kerja', $get_user->penempatan_kerja)
                ->where('karyawans.status_aktif', 'AKTIF')
                ->where('karyawans.dept_id', $get_user->dept_id)
                ->where('level_jabatans.level_jabatan', '<', $get_level->level_jabatan)
                ->select('karyawans.*', 'jabatans.nama_jabatan', 'bagians.nama_bagian')
                ->orderBy('karyawans.name', 'ASC')
                ->get();
        } else {
            $get_atasan = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan_id')
                ->Join('divisis', 'divisis.id', 'jabatans.divisi_id')
                ->Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')
                ->Join('bagians', 'bagians.id', 'jabatans.bagian_id')
                ->where('karyawans.status_aktif', 'AKTIF')
                // ->where('karyawans.penempatan_kerja', $get_user->penempatan_kerja)
                // ->where('divisis.id', $request->id_divisi)
                ->where('level_jabatans.level_jabatan', '<', $get_level->level_jabatan)
                ->select('karyawans.*', 'jabatans.nama_jabatan', 'bagians.nama_bagian')
                ->orderBy('karyawans.name', 'ASC')
                ->get();
        }
        echo "<option value=''>Pilih Atasan...</option>";
        foreach ($get_atasan as $atasan) {
            echo "<option value='$atasan->id'>$atasan->name ($atasan->nama_jabatan | $atasan->nama_bagian)</option>";
        }
    }
    public function get_atasan2(Request $request)
    {
        // dd($request->all());
        if ($request->holding == 'sp') {
            $kontrak = 'SP';
        } else if ($request->holding == 'sps') {
            $kontrak = 'SPS';
        } else {
            $kontrak = 'SIP';
        }
        $get_user = Karyawan::where('id', $request->id_karyawan)->first();
        $get_level = Jabatan::Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')->where('jabatans.id', $request->id)->first();
        // dd($get_level);
        if ($get_level == NULL || $get_level == '') {
            $get_atasan = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan_id')
                ->Join('divisis', 'divisis.id', 'jabatans.divisi_id')
                ->Join('bagians', 'bagians.id', 'jabatans.bagian_id')
                ->Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')
                ->where('karyawans.status_aktif', 'AKTIF')
                ->where('karyawans.penempatan_kerja', $get_user->penempatan_kerja)
                ->where('karyawans.dept_id', $get_user->dept_id)
                ->where('level_jabatans.level_jabatan', '<', 2)
                ->select('karyawans.*', 'jabatans.nama_jabatan', 'bagians.nama_bagian')
                ->get();
        } else {
            if ($get_level->level_jabatan <= 4) {
                $get_atasan = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan_id')
                    ->Join('divisis', 'divisis.id', 'jabatans.divisi_id')
                    ->Join('bagians', 'bagians.id', 'jabatans.bagian_id')
                    ->where('karyawans.status_aktif', 'AKTIF')
                    ->where('karyawans.penempatan_kerja', $get_user->penempatan_kerja)
                    ->where('karyawans.dept_id', $get_user->dept_id)
                    ->Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')
                    ->where('level_jabatans.level_jabatan', '<', $get_level->level_jabatan)
                    ->select('karyawans.*', 'jabatans.nama_jabatan', 'bagians.nama_bagian')
                    ->get();
            } else {
                $get_atasan = Karyawan::Join('jabatans', 'jabatans.id', 'karyawans.jabatan_id')
                    ->Join('divisis', 'divisis.id', 'jabatans.divisi_id')
                    ->Join('level_jabatans', 'level_jabatans.id', 'jabatans.level_id')
                    ->Join('bagians', 'bagians.id', 'jabatans.bagian_id')
                    ->where('karyawans.status_aktif', 'AKTIF')
                    ->where('karyawans.penempatan_kerja', $get_user->penempatan_kerja)
                    ->where('karyawans.dept_id', $get_user->dept_id)
                    // ->where('divisis.id', $request->id_divisi)
                    ->where('level_jabatans.level_jabatan', '<', $get_level->level_jabatan)
                    ->select('karyawans.*', 'jabatans.nama_jabatan', 'bagians.nama_bagian')
                    ->get();
            }
        }
        echo "<option value=''>Pilih Atasan...</option>";
        foreach ($get_atasan as $atasan) {
            echo "<option value='$atasan->id'>$atasan->name ($atasan->nama_jabatan | $atasan->nama_bagian)</option>";
        }
    }

    public function detail($id, $holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $getHolding = Holding::where('holding_code', $holding)->first();

        $getHoldingall = Holding::get();
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
        $karyawan = Karyawan::with('KontrakKerja')
            ->with([
                'karyawanKesehatan' => function ($query) {
                    $query->orderBy('id_kesehatan', 'ASC');
                },
            ])
            ->with([
                'karyawanKesehatanPengobatan' => function ($query) {
                    $query;
                },
            ])
            ->with([
                'karyawanKesehatanRS' => function ($query) {
                    $query;
                },
            ])
            ->with([
                'karyawanKesehatanKecelakaan' => function ($query) {
                    $query;
                },
            ])
            ->find($id);
        $cuti_period = CutiPeriod::where('status', '1')->first();
        $cuti_karyawan = KuotaCutiKaryawan::where('karyawan_id', $id)->where('periode_id', $cuti_period->id_period)->first();
        if ($karyawan == NULL) {
            return redirect()->back()->with('error', 'Karyawan Tidak Ada', 1500);
        } else {
            // dd($karyawan);
            return view('admin.karyawan.detail_karyawan', [
                // return view('karyawan.editkaryawan', [
                'title' => 'Detail Karyawan',
                'holding' => $getHolding,
                'menus' => $menus,
                'holdingAll' => $getHoldingall,
                'karyawan' => $karyawan,
                'cuti_karyawan' => $cuti_karyawan,
                'cuti_period' => $cuti_period,
                "data_lokasi" => Site::whereNotIn('site_status', ['DEPO'])->orderBy('site_name', 'ASC')->get(),
                "site" => Site::orderBy('site_name', 'ASC')->get(),
                "data_provinsi" => Provincies::orderBy('name', 'ASC')->get(),
                "data_kuota" => KuotaCutiKaryawan::where('karyawan_id', $id)->get(),
            ]);
        }
    }
    public function button_keahlian($id)
    {
        $data_keahlian = KaryawanKeahlian::select()->where('id_karyawan', $id)->count();
        return response()->json([
            'code' => 200,
            // 'data' => $get_data,
            'data_keahlian' => $data_keahlian,
            // 'message' => 'Data Berhasil Diupdate'
        ]);
    }

    public function kategori_cuti_datatable($id, $holding)
    {
        $karyawan = Karyawan::where('id', $id)->first();
        $kategori_cuti = KategoriCuti::where('status', 1)->orderBy('nama_cuti', 'ASC')->get();
        // dd($kategori_cuti, $karyawan);
        return DataTables::of($kategori_cuti)
            ->addColumn('sisa', function ($row) use ($karyawan) {
                $sisa = '<span>0</span>';
                return $sisa;
            })
            ->addColumn('terpakai', function ($row) use ($karyawan) {
                $terpakai = '<span>0</span>';
                return $terpakai;
            })
            ->addColumn('status', function ($row) use ($karyawan) {
                $arrayStrGender = str_replace(['{', '}'], '', $row->target_gender);
                $arrayStrNikah = str_replace(['{', '}'], '', $row->target_status_nikah);
                $idsGender = explode(',', $arrayStrGender);
                $idsNikah = explode(',', $arrayStrNikah);
                if (in_array($karyawan->gender, $idsGender)) {
                    $gender = '<span class="mdi mdi-check-circle text-success me-2"></span>';
                } else {
                    $gender = '<span class="mdi mdi-close-circle text-danger me-2"></span>';
                }
                if (in_array($karyawan->status_nikah, $idsNikah)) {
                    $nikah = '<span class="mdi mdi-check-circle text-success"></span>';
                } else {
                    $nikah = '<span class="mdi mdi-close-circle text-danger"></span>';
                }
                return $gender . $nikah;
            })
            ->rawColumns(['status', 'terpakai', 'sisa'])
            ->make(true);
    }
    public function keahlian_datatable($id)
    {
        $keahlian = KaryawanKeahlian::where('id_karyawan', $id)->get();
        return DataTables::of($keahlian)
            ->addColumn('aksi', function ($row) {
                $btn = '<button class="btn_edit_keahlian btn btn-sm btn-primary" data-id_keahlian="' . $row->id_keahlian . '" data-keahlian="' . $row->keahlian . '" data-file_keahlian="' . $row->file_keahlian . '" data-file_url="' . asset('storage/file_keahlian/' . $row->file_keahlian) . '"><i class="mdi mdi-pencil"></i></button>';
                $btn = $btn . ' <button id="btn_delete_keahlian" class="btn btn-sm btn-danger" data-id="' . $row->id_keahlian . '"><i class="mdi mdi-delete"></i></button>';
                return $btn;
            })
            ->addColumn('file', function ($row) {
                if ($row->file_keahlian == null) {
                    return 'Tidak Ada';
                } else {
                    return '<a href="' . asset('storage/file_keahlian/' . $row->file_keahlian) . '" target="_blank" class="btn btn-sm btn-info"><i class="mdi mdi-eye"></i> Lihat</a>';
                }
            })
            ->rawColumns(['aksi', 'file'])
            ->make(true);
    }
    public function add_keahlian(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate(
                [
                    'id_karyawan' => 'required',
                    'nama_keahlian' => 'required',
                    'file_keahlian' => 'nullable|max:5048', // hanya PDF max 5MB
                ],
                [
                    'id_karyawan.required' => 'ID Karyawan wajib diisi',
                    'nama_keahlian.required' => 'Nama Keahlian wajib diisi',
                    'file_keahlian.max' => 'File Keahlian maksimal 5MB',
                ]
            );
            $path = null;
            $filename = null;
            if ($request->hasFile('file_keahlian')) {
                $path = $request->file('file_keahlian')->store('file_keahlian', 'public');
                $filename = basename($path);
            }
            KaryawanKeahlian::create(
                [
                    'id_keahlian' => UUID::uuid4(),
                    'id_karyawan' => $validatedData['id_karyawan'],
                    'keahlian' => $validatedData['nama_keahlian'],
                    'file_keahlian' => $filename,
                    'created_at' => now(),
                ]
            );
            $data_keahlian = KaryawanKeahlian::select()->where('id_karyawan', $request->id_karyawan)->count();
            return response()->json([
                'code' => 200,
                'data_keahlian' => $data_keahlian,
                'message' => 'Data berhasil ditambahkan'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }
    public function update_keahlian(Request $request)
    {
        try {
            // dd($request->all());
            $validatedData = $request->validate(
                [
                    'id_karyawan' => 'required',
                    'nama_keahlian' => 'required',
                    'file_keahlian' => 'nullable|max:5048', // hanya PDF max 5MB
                ],
                [
                    'id_karyawan.required' => 'ID Karyawan wajib diisi',
                    'nama_keahlian.required' => 'Nama Keahlian wajib diisi',
                    'file_keahlian.max' => 'File Keahlian maksimal 5MB',
                ]
            );
            $path = null;
            if ($request->hasFile('file_keahlian')) {
                $path = $request->file('file_keahlian')->store('file_keahlian', 'public');
                if ($path) {
                    $filename = basename($path);
                }
            }
            $delete_old = storage_path('app/public/file_keahlian/' . $request->file_keahlian_old);
            if (file_exists($delete_old)) {
                unlink($delete_old);
            }
            KaryawanKeahlian::where('id_keahlian', $request->id_keahlian)->update(
                [
                    'id_karyawan' => $validatedData['id_karyawan'],
                    'keahlian' => $validatedData['nama_keahlian'],
                    'file_keahlian' => $filename ?? null,
                    'created_at' => now(),
                ]
            );
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil diperbarui'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 402,
                'message' => $e->errors()
            ]);
        }
    }
    public function delete_keahlian(Request $request, $id)
    {
        $get = KaryawanKeahlian::where('id_keahlian', $request->id_keahlian);
        if ($get->exists()) {
            $cek_old_file = $get->first();
            $get->delete();
            $data_keahlian = KaryawanKeahlian::select()->where('id_karyawan', $id)->count();
            if ($cek_old_file->file_keahlian) {
                $delete_old = storage_path('app/public/file_keahlian/' . $cek_old_file->file_keahlian);
                if (file_exists($delete_old)) {
                    unlink($delete_old);
                }
            }
            return response()->json([
                'code' => 200,
                'data_keahlian' => $data_keahlian,
                'message' => 'Data berhasil Dihapus'
            ]);
        } else {
            return response()->json([
                'code' => 402,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function editBank(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama_bank' => 'required',
                'nama_pemilik_rekening' => 'required',
                'nomor_rekening' => 'required',
                // 'nomor_referensi' => 'numeric',
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
            Karyawan::where('id', $id)->update(
                [
                    'nama_bank'                             => $request->nama_bank,
                    'nama_pemilik_rekening'                 => $request->nama_pemilik_rekening,
                    'nomor_rekening'                        => $request->nomor_rekening,
                    'updated_at'                            => date('Y-m-d H:i:s')
                ]
            );
            $karyawan_id = Karyawan::where('id', $request->id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengedit data bank ' . $karyawan_id->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
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
    public function editPajak(Request $request, $id)
    {
        // dd($request->all());
        if ($request->status_npwp == 'on') {
            $nama_pemilik_npwp = 'required';
            $npwp = 'required';
        } else if ($request->status_npwp == 'off') {
            $nama_pemilik_npwp = 'nullable';
            $npwp = 'nullable';
        } else {
            $nama_pemilik_npwp = 'nullable';
            $npwp = 'nullable';
        }
        $rules = [
            'nama_pemilik_npwp' => $nama_pemilik_npwp,
            'npwp' => $npwp . '|max:16',
            'unique:karyawans,npwp,' . $request->npwp,
            'ptkp' => 'required',
        ];


        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($validasi->errors());

        if ($validasi->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validasi->errors()
            ]);
        }
        try {
            Karyawan::where('id', $id)->update(
                [
                    'status_npwp'         => $request->status_npwp,
                    'ptkp'                => $request->ptkp,
                    'nama_pemilik_npwp'   => $request->nama_pemilik_npwp,
                    'npwp'                => $request->npwp,
                    'updated_at'          => date('Y-m-d H:i:s')

                ]
            );
            $karyawan_id = Karyawan::where('id', $id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengedit data pajak ' . $karyawan_id->name,
                'keterangan' => 'Success',
            ]);
            // ActivityLog::create([
            //     'user_id' => Auth::user()->id,
            //     'activity' => 'update',
            //     'description' => 'Mengubah data karyawan ' . $request->name,
            // ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
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
    public function ijazah_update(Request $request, $id)
    {
        if ($request->old_ijazah == NULL) {
            $ijazah_val = 'nullable';
        } elseif ($request->old_ijazah != NULL) {
            $ijazah_val = 'required|mimes:pdf|max:5000';
        }
        if ($request->old_transkrip_nilai != NULL) {
            $transkrip_nilai_val = 'required|mimes:pdf|max:5000';
        } elseif ($request->old_transkrip_nilai == NULL) {
            $transkrip_nilai_val = 'nullable';
        }
        // dd($transkrip_nilai_val);
        $validator = Validator::make(
            $request->all(),
            [
                'ijazah' => $ijazah_val,
                'ipk' => 'required|max:4',
                'transkrip_nilai' => $transkrip_nilai_val,
            ],
            [
                'required' => ':attribute tidak boleh kosong!',
                'max' => ':attribute Melebihi ketentuan!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()

            ]);
        }
        //memasukkan data
        try {
            if ($request->ijazah != null) {
                if ($request->old_ijazah != null) {
                    if (Storage::disk('ijazah')->exists($request->old_ijazah)) {
                        Storage::disk('ijazah')->delete($request->old_ijazah);
                    }
                }
                $ijazah = $request->file('ijazah')->store('ijazah');
                $ijazah_save = basename($ijazah);
            } else {
                $ijazah_save = $request->old_ijazah;
            }
            if ($request->transkrip_nilai != null) {
                if ($request->old_transkrip_nilai != null) {
                    if (Storage::disk('transkrip_nilai')->exists($request->old_transkrip_nilai)) {
                        Storage::disk('transkrip_nilai')->delete($request->old_transkrip_nilai);
                    }
                }
                $transkrip_nilai = $request->file('transkrip_nilai')->store('transkrip_nilai');
                $transkrip_nilai_save = basename($transkrip_nilai);
            } else {
                $transkrip_nilai_save = $request->old_transkrip_nilai;
            }
            $datadiri = Karyawan::where('id', $id)->first();
            $datadiri->ijazah = $ijazah_save;
            $datadiri->transkrip_nilai = $transkrip_nilai_save;
            $datadiri->ipk = $request->ipk;
            $datadiri->updated_at = date('Y-m-d H:i:s');
            $datadiri->save();
            // $data_user = Karyawan::select()
            //     ->where('users_career_id', Auth::user()->id)
            //     ->with([
            //         'ToUser' => function ($query) {
            //             $query;
            //         }
            //     ])->first();
            $karyawan_id = Karyawan::where('id', $request->id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengedit data pendidikan ' . $datadiri->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_user' => $data_user,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function editBPJS(Request $request, $id)
    {
        if ($request->bpjs_kesehatan == 'on') {
            $nama_pemilik_bpjs_kesehatan = 'required';
            $no_bpjs_kesehatan = 'required';
            $kelas_bpjs = 'required';
        } else if ($request->bpjs_kesehatan == 'off') {
            $nama_pemilik_bpjs_kesehatan = 'nullable';
            $no_bpjs_kesehatan = 'nullable';
            $kelas_bpjs = 'nullable';
        } else {
            $nama_pemilik_bpjs_kesehatan = 'nullable';
            $no_bpjs_kesehatan = 'nullable';
            $kelas_bpjs = 'nullable';
        }
        if ($request->bpjs_ketenagakerjaan == 'on') {
            $nama_pemilik_bpjs_ketenagakerjaan = 'required';
            $no_bpjs_ketenagakerjaan = 'required';
        } else if ($request->bpjs_ketenagakerjaan == 'off') {
            $nama_pemilik_bpjs_ketenagakerjaan = 'nullable';
            $no_bpjs_ketenagakerjaan = 'nullable';
        } else {
            $nama_pemilik_bpjs_ketenagakerjaan = 'nullable';
            $no_bpjs_ketenagakerjaan = 'nullable';
        }

        $rules = [

            'bpjs_ketenagakerjaan' => 'required',
            'no_bpjs_ketenagakerjaan' => $no_bpjs_ketenagakerjaan . '|max:16',
            'nama_pemilik_bpjs_ketenagakerjaan' => $nama_pemilik_bpjs_ketenagakerjaan,
            'unique:karyawans,bpjs_ketenagakerjaan,' . $request->bpjs_ketenagakerjaan,
            'bpjs_kesehatan' => 'required',
            'nama_pemilik_bpjs_kesehatan' => $nama_pemilik_bpjs_kesehatan,
            'no_bpjs_kesehatan' => $no_bpjs_kesehatan . '|max:16',
            'unique:karyawans,no_bpjs_kesehatan,' . $request->no_bpjs_kesehatan,
            'bpjs_pensiun' => 'required|max:16',
            'kelas_bpjs' => $kelas_bpjs,

        ];
        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($validasi->errors());
        if ($validasi->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validasi->errors()
            ]);
        }
        try {
            Karyawan::where('id', $id)->update(
                [
                    'bpjs_ketenagakerjaan'                  => $request->bpjs_ketenagakerjaan,
                    'no_bpjs_ketenagakerjaan'               => $request->no_bpjs_ketenagakerjaan,
                    'nama_pemilik_bpjs_ketenagakerjaan'     => $request->nama_pemilik_bpjs_ketenagakerjaan,
                    'bpjs_pensiun'                          => $request->bpjs_pensiun,
                    'bpjs_kesehatan'                        => $request->bpjs_kesehatan,
                    'nama_pemilik_bpjs_kesehatan'           => $request->nama_pemilik_bpjs_kesehatan,
                    'no_bpjs_kesehatan'                     => $request->no_bpjs_kesehatan,
                    'kelas_bpjs'                            => $request->kelas_bpjs,
                    'updated_at'                            => date('Y-m-d H:i:s')

                ]
            );
            $karyawan_id = Karyawan::where('id', $id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengedit data BPJS ' . $karyawan_id->name,
                'keterangan' => 'Success',
            ]);
            // ActivityLog::create([
            //     'user_id' => Auth::user()->id,
            //     'activity' => 'update',
            //     'description' => 'Mengubah data karyawan ' . $request->name,
            // ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
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
    public function dokumen_datadiri($id)
    {
        try {
            $karyawan = Karyawan::select('id', 'nik', 'name', 'ktp', 'kk', 'ijazah', 'transkrip_nilai')->where('id', $id)->first();
            return response()->json([
                'code' => 200,
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function editProfil(Request $request, $id)
    {
        // dd($request->all());

        if ($request->status_nomor == "tidak") {
            $status_nomor = 'required';
            $wa = $request->nomor_wa;
        } else if ($request->status_nomor == "ya") {
            $wa = $request->telepon;
            $status_nomor = 'nullable';
            // dd($wa);
        } else {
            $status_nomor = 'required';
        }

        if ($request->pilihan_alamat_domisili == "ya") {
            $provinsi_domisili = 'nullable';
            $kabupaten_domisili = 'nullable';
            $kecamatan_domisili = 'nullable';
            $desa_domisili = 'nullable';
            $rt_domisili = 'nullable|max:255';
            $rw_domisili = 'nullable|max:255';
            $alamat_domisili = 'nullable|max:255';
            $kode_pos_domisili = 'nullable|max:255';
        } else if ($request->pilihan_alamat_domisili == "tidak") {
            $provinsi_domisili = 'required';
            $kabupaten_domisili = 'required';
            $kecamatan_domisili = 'required';
            $desa_domisili = 'required';
            $rt_domisili = 'required|max:255';
            $rw_domisili = 'required|max:255';
            $alamat_domisili = 'required|max:255';
            $kode_pos_domisili = 'required|max:255';
        } else if ($request->pilihan_alamat_domisili == NULL) {
            $provinsi_domisili = 'nullable';
            $kabupaten_domisili = 'nullable';
            $kecamatan_domisili = 'nullable';
            $desa_domisili = 'nullable';
            $rt_domisili = 'nullable|max:255';
            $rw_domisili = 'nullable|max:255';
            $alamat_domisili = 'nullable|max:255';
            $kode_pos_domisili = 'nullable|max:255';
        }
        if ($request->ktp_old == NULL) {
            $ktp_val = 'required|mimes:jpg,jpeg,png|max:5000';
        } elseif ($request->ktp_old != NULL) {
            $ktp_val = 'nullable';
        }
        if ($request->kk_old == NULL) {
            $kk_val = 'required|mimes:jpg,jpeg,png|max:5000';
        } elseif ($request->kk_old != NULL) {
            $kk_val = 'nullable';
        }
        $rules = [
            'name' => 'required|max:255',
            'nik' => 'required|max:16',
            'unique:karyawans,nik,' . $request->nik,
            'email' => 'max:255|nullable',
            'email:rfc,dns',
            'email_address',
            'unique:karyawans,email,' . $request->email,
            'email_kantor' => 'max:255|nullable|email:rfc,dns' . $request->email_kantor,
            'nomor_wa_kantor' => 'max:13|nullable|min:11',
            'no_kk' => 'required|max:16',
            'tgl_terbit_kk' => 'required',
            'telepon' => 'max:13',
            'tgl_join' => 'max:13',
            'nomor_wa' => 'max:13|' . $status_nomor . '|min:11',
            'nullable',
            'min:11',
            'status_nomor' => 'required',
            'tempat_lahir' => 'required|max:255',
            'tgl_lahir' => 'required|max:255',
            // 'golongan_darah' => 'required|max:255',
            'agama' => 'required|max:255',
            'gender' => 'required',
            'status_nikah' => 'required',
            'ktp' => $ktp_val,
            'kk' => $kk_val,
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'rt' => 'required|max:255',
            'rw' => 'required|max:255',
            'alamat' => 'required|max:255',
            'kode_pos' => 'required|max:5',
            'pilihan_alamat_domisili' => 'required|max:11',
            'provinsi_domisili' => $provinsi_domisili,
            'kabupaten_domisili' => $kabupaten_domisili,
            'kecamatan_domisili' => $kecamatan_domisili,
            'desa_domisili' => $desa_domisili,
            'rt_domisili' => $rt_domisili,
            'rw_domisili' => $rw_domisili,
            'alamat_domisili' => $alamat_domisili,
            'kode_pos_domisili' => $kode_pos_domisili,

        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($validasi->errors());
        if ($validasi->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validasi->errors()
            ]);
        }
        try {
            if ($request->foto_karyawan != null) {
                if ($request->foto_karyawan_old != null) {
                    if (Storage::disk('foto_karyawan')->exists($request->foto_karyawan_old)) {
                        Storage::disk('foto_karyawan')->delete($request->foto_karyawan_old);
                    }
                }
                $foto_karyawan = $request->file('foto_karyawan')->store('foto_karyawan');
                $foto_karyawan_save = basename($foto_karyawan);
            } else {
                $foto_karyawan_save = $request->foto_karyawan_old;
            }
            if ($request->ktp != null) {
                if ($request->ktp_old != null) {
                    if (Storage::disk('ktp')->exists($request->ktp_old)) {
                        Storage::disk('ktp')->delete($request->ktp_old);
                    }
                }
                $ktp = $request->file('ktp')->store('ktp');
                $ktp_save = basename($ktp);
            } else {
                $ktp_save = $request->ktp_old;
            }

            if ($request->kk != null) {
                if ($request->kk_old != null) {
                    if (Storage::disk('kk')->exists($request->kk_old)) {
                        Storage::disk('kk')->delete($request->kk_old);
                    }
                }
                $kk = $request->file('kk')->store('kk');
                $kk_save = basename($kk);
            } else {
                $kk_save = $request->kk_old;
            }
            Karyawan::where('id', $id)->update(
                [
                    'foto_karyawan'                         => $foto_karyawan_save,
                    'nik'                                   => $request->nik,
                    'nomor_identitas_karyawan'              => $request->nomor_identitas_karyawan,
                    'id_finger'                             => $request->id_finger,
                    'name'                                  => $request->name,
                    'email'                                 => $request->email,
                    'telepon'                               => $request->telepon,
                    'email_kantor'                          => $request->email_kantor,
                    'nomor_wa_kantor'                       => $request->nomor_wa_kantor,
                    'tgl_join'                              => $request->tgl_join,
                    'no_kk'                                 => $request->no_kk,
                    'tgl_terbit_kk'                         => $request->tgl_terbit_kk,
                    'ktp'                                   => $ktp_save,
                    'kk'                                    => $kk_save,
                    'nomor_wa'                              => $wa,
                    'status_nomor'                          => $request->status_nomor,
                    'tempat_lahir'                          => $request->tempat_lahir,
                    'tgl_lahir'                             => $request->tgl_lahir,
                    'agama'                                 => $request->agama,
                    'gender'                                => $request->gender,
                    'status_nikah'                          => $request->status_nikah,
                    'jumlah_anak'                           => $request->jumlah_anak,
                    'provinsi'                              => $request->provinsi,
                    'kabupaten'                             => $request->kabupaten,
                    'kecamatan'                             => $request->kecamatan,
                    'desa'                                  => $request->desa,
                    'rt'                                    => $request->rt,
                    'rw'                                    => $request->rw,
                    'detail_alamat'                         => $request->alamat,
                    'kode_pos'                              => $request->kode_pos,
                    'status_alamat'                         => $request->pilihan_alamat_domisili,
                    'provinsi_domisili'                     => $request->provinsi_domisili,
                    'kabupaten_domisili'                    => $request->kabupaten_domisili,
                    'kecamatan_domisili'                    => $request->kecamatan_domisili,
                    'desa_domisili'                         => $request->desa_domisili,
                    'rt_domisili'                           => $request->rt_domisili,
                    'rw_domisili'                           => $request->rw_domisili,
                    'alamat_domisili'                       => $request->alamat_domisili,
                    'kode_pos_domisili'                     => $request->kode_pos_domisili,
                    'updated_at'                            => date('Y-m-d H:i:s')
                ]
            );
            User::where('id_karyawan', $id)->update([
                'karyawan_id' => $request->nomor_identitas_karyawan,
                'id_finger' => $request->id_finger,
            ]);
            // ActivityLog::create([
            //     'user_id' => Auth::user()->id,
            //     'activity' => 'update',
            //     'description' => 'Mengubah data karyawan ' . $request->name,
            // ]);
            $karyawan_id = Karyawan::where('id', $id)->first();
            $holdings = Holding::where('id', $karyawan_id->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengubah data karyawan ' . $request->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
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
    public function editJabatan(Request $request, $id)
    {
        // $lokasi_kerja = Lokasi::where('lokasi_kantor', $request->site_job)->value('kategori_kantor');
        // if ($lokasi_kerja == 'all') {
        //     $kategori_jabatan = 'required';
        // } else {
        //     $kategori_jabatan = 'nullable';
        // }
        $rules = [
            // 'kategori_jabatan' => $kategori_jabatan . '|max:255',
            'departemen_id' => 'required|max:255',
            'divisi_id' => 'required|max:255',
            'bagian_id' => 'required|max:255',
            'jabatan_id' => 'required|max:255',
            'file_cv' => 'max:255',
            'kategori_jabatan' => 'max:255'
        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($validasi->errors());

        try {
            if ($validasi->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Validasi gagal',
                    'errors' => $validasi->errors()
                ]);
            }
            Karyawan::where('id', $id)->update(
                [

                    // 'kategori_jabatan'                      => $request->kategori_jabatan,
                    'dept_id'                               => $request->departemen_id,
                    'divisi_id'                             => $request->divisi_id,
                    'bagian_id'                             => $request->bagian_id,
                    'jabatan_id'                            => $request->jabatan_id,
                    'dept1_id'                              => $request->departemen1_id,
                    'divisi1_id'                            => $request->divisi1_id,
                    'bagian1_id'                            => $request->bagian1_id,
                    'jabatan1_id'                           => $request->jabatan1_id,
                    'dept2_id'                              => $request->departemen2_id,
                    'divisi2_id'                            => $request->divisi2_id,
                    'bagian2_id'                            => $request->bagian2_id,
                    'jabatan2_id'                           => $request->jabatan2_id,
                    'penempatan_kerja'                      => $request->penempatan_kerja,
                    'penempatan_holding'                    => $request->penempatan_holding,
                    // 'divisi3_id'                            => $request->divisi3_id,
                    // 'bagian3_id'                            => $request->bagian3_id,
                    // 'jabatan3_id'                           => $request->jabatan3_id,
                    // 'dept4_id'                              => $request->departemen4_id,
                    // 'divisi4_id'                            => $request->divisi4_id,
                    // 'bagian4_id'                            => $request->bagian4_id,
                    // 'jabatan4_id'                           => $request->jabatan4_id,
                ]
            );
            // ActivityLog::create([
            //     'user_id' => Auth::user()->id,
            //     'activity' => 'update',
            //     'description' => 'Mengubah data karyawan ' . $request->name,
            // ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
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





    public function shift($id, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = request()->segment(count(request()->segments()));
        $user_check = Karyawan::where('id', $id)->first();
        if ($user_check->kategori == 'Karyawan Bulanan') {
            if ($user_check->dept_id == NULL || $user_check->divisi_id == NULL || $user_check->jabatan_id == NULL) {
                return redirect()->back()->with('error', 'Jabatan Karyawan Kosong');
            }
        }
        $oke = MappingShift::with('Shift')->where('user_id', $id)->orderBy('id', 'desc')->limit(100)->get();
        // dd($oke);
        $user = Karyawan::with('Jabatan')
            ->with('Divisi')
            ->where('kontrak_kerja', $holding)
            ->where('karyawans.id', $id)
            ->first();
        $jabatan = Jabatan::join('karyawans', function ($join) {
            $join->on('jabatans.id', '=', 'karyawans.jabatan_id');
            $join->orOn('jabatans.id', '=', 'karyawans.jabatan1_id');
            $join->orOn('jabatans.id', '=', 'karyawans.jabatan2_id');
            $join->orOn('jabatans.id', '=', 'karyawans.jabatan3_id');
            $join->orOn('jabatans.id', '=', 'karyawans.jabatan4_id');
        })->where('karyawans.id', $id)->get();
        $divisi = Divisi::join('karyawans', function ($join) {
            $join->on('divisis.id', '=', 'karyawans.divisi_id');
            $join->orOn('divisis.id', '=', 'karyawans.divisi1_id');
            $join->orOn('divisis.id', '=', 'karyawans.divisi2_id');
            $join->orOn('divisis.id', '=', 'karyawans.divisi3_id');
            $join->orOn('divisis.id', '=', 'karyawans.divisi4_id');
        })->where('karyawans.id', $id)->get();
        $no = 1;
        $no1 = 1;
        // dd($jabatan);
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
        return view('admin.karyawan.mappingshift', [
            'title' => 'Mapping Shift',
            'karyawan' => $user,
            'holding' => $holding,
            'shift_karyawan' => MappingShift::where('user_id', $id)->orderBy('created_at', 'desc')->limit(100)->get(),
            'shift' => Shift::all(),
            'menus' => $menus,
            'jabatan_karyawan' => $jabatan,
            'divisi_karyawan' => $divisi,
            'no' => $no,
            'no1' => $no1,
        ]);
    }
    public function mapping_shift_datatable(Request $request, $id)
    {
        // dd($request->start_date);
        $holding = request()->segment(count(request()->segments()));
        // dd('ok');
        if (request()->ajax()) {
            if (!empty($request->start_date)) {
                $table = MappingShift::join('shifts', 'mapping_shifts.shift_id', 'shifts.id')
                    ->where('mapping_shifts.user_id', $id)
                    ->whereBetween('tanggal_masuk', [$request->start_date, $request->end_date])
                    ->select('mapping_shifts.*', 'shifts.nama_shift', 'shifts.jam_masuk', 'shifts.jam_keluar')
                    ->orderBy('tanggal_masuk', 'ASC')
                    // ->limit(100)
                    ->get();
                return DataTables::of($table)
                    ->addColumn('option', function ($row) use ($holding) {
                        $btn = '<button id="btn_edit_mapping_shift" type="button" data-id="' . $row->id . '" data-shift="' . $row->shift_id . '"  data-userid="' . $row->user_id . '" data-keterangan="' . $row->status_absen . '" data-tanggal="' . $row->tanggal_masuk . '" data-holding="' . $holding . '" class="btn btn-icon btn-warning waves-effect waves-light"><span class="tf-icons mdi mdi-pencil-outline"></span></button>';
                        $btn = $btn . '<button id="btn_delete_mapping_shift" data-id="' . $row->id . '" data-holding="' . $holding . '" type="button" class="btn btn-icon btn-danger waves-effect waves-light"><span class="tf-icons mdi mdi-delete-outline"></span></button>';
                        return $btn;
                    })
                    ->rawColumns(['option'])
                    ->make(true);
            }
        }
    }
    public function get_departemen($holding)
    {
        $get_holding = Holding::where('id', $holding)->first()->nama_holding;

        $departemen      = Departemen::where('holding', $holding)->orderByRaw('nama_departemen ASC')->get();
        // dd($departemen);
        echo "<option value=''>Pilih Departemen...</option>";
        echo "<optgroup label='Daftar Departemen $get_holding'>";
        foreach ($departemen as $departemen) {
            echo "<option value='$departemen->id'>$departemen->nama_departemen</option>";
        }
        echo "</optgroup>";
    }
    public function get_divisi($id_departemen)
    {
        // dd($id_departemen);
        $departemen    = Departemen::where('id', $id_departemen)->first();
        if ($departemen) {
            $holding_1 = Holding::where('id', $departemen->holding)->first()->nama_holding;
        } else {
            $holding_1 = NULL;
        }

        // dd($holding);
        $divisi      = Divisi::where('dept_id', $departemen->id)->orderBy('nama_divisi', 'ASC')->get();
        echo "<option value=''>Pilih Divisi...</option>";
        echo "<optgroup label='Daftar Divisi $holding_1'>";
        foreach ($divisi as $divisi) {
            echo "<option value='$divisi->id'>$divisi->nama_divisi</option>";
        }
        echo "</optgroup>";
    }
    public function get_bagian($id_divisi)
    {
        $divisi    = Divisi::where('id', $id_divisi)->first();
        if ($divisi) {
            $holding_1 = Holding::where('id', $divisi->holding)->first()->nama_holding;
        } else {
            $holding_1 = NULL;
        }
        $bagian      = Bagian::where('divisi_id', $divisi->id)->orderBy('nama_bagian', 'ASC')->get();
        echo "<option value=''>Pilih Bagian...</option>";
        echo "<optgroup label='Daftar Bagian $holding_1'>";
        foreach ($bagian as $bagian) {
            echo "<option value='$bagian->id'>$bagian->nama_bagian</option>";
        }
        echo "</optgroup>";
    }
    public function get_jabatan($id_bagian)
    {
        $bagian    = Bagian::where('id', $id_bagian)->first();
        if ($bagian) {
            $holding_1 = Holding::where('id', $bagian->holding)->first()->nama_holding;
        } else {
            $holding_1 = NULL;
        }
        $jabatan      = Jabatan::where('bagian_id', $bagian->id)->orderBy('nama_jabatan', 'ASC')->get();
        echo "<option value=''>Pilih Jabatan...</option>";
        echo "<optgroup label='Daftar Jabatan $holding_1'>";
        foreach ($jabatan as $jabatan) {
            echo "<option value='$jabatan->id'>$jabatan->nama_jabatan</option>";
        }
        echo "</optgroup>";
    }
    public function prosesTambahShift(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        if ($request["tanggal_mulai"] == null) {
            $request["tanggal_mulai"] = $request["tanggal_akhir"];
        } else {
            $request["tanggal_mulai"] = $request["tanggal_mulai"];
        }

        if ($request["tanggal_akhir"] == null) {
            $request["tanggal_akhir"] = $request["tanggal_mulai"];
        } else {
            $request["tanggal_akhir"] = $request["tanggal_akhir"];
        }
        $rules = [
            'shift_id' => 'required',
            'max:255',
            'tanggal_mulai' => 'required',
            'max:16',
            'tanggal_akhir' => 'required',
            'max:16',
        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($request->all());
        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            // dd($errors);
            Alert::error('Gagal', $errors);
            return back()->withInput();
        }
        $begin = new \DateTime($request["tanggal_mulai"]);
        $end = new \DateTime($request["tanggal_akhir"]);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
        $daterange = new \DatePeriod($begin, $interval, $end);

        // dd($request->all());
        foreach ($daterange as $date) {
            $tanggal_masuk = $date->format("Y-m-d");
            $tanggal_pulang = $date->format("Y-m-d");
            $malam = $date->modify('+1 day');
            $tanggal_pulang_malam = $malam->format("Y-m-d");
            // dd($tanggal_pulang_malam);

            $week = Carbon::parse($date)->dayOfWeek;
            if ($week == 1) {
                $request["status_absen"] = "LIBUR";
            } else {
                $request["status_absen"] = NULL;
            }
            $cek_date_same = MappingShift::where('tanggal_masuk', $tanggal_masuk)->where('tanggal_pulang', $tanggal_pulang)->where('user_id', $request->user_id)->where('shift_id', $request->shift_id)->count();
            if ($cek_date_same != 0) {
                return redirect()->back()->with('error', 'Data ada yang sama', 5000);
            }
            $request["tanggal_masuk"] = $tanggal_masuk;
            $nama_shift = Shift::where('id', $request['shift_id'])->value('nama_shift');
            if ($nama_shift == 'Malam') {
                $request["tanggal_pulang"] = $tanggal_pulang_malam;
            } else {
                $request["tanggal_pulang"] = $tanggal_pulang;
            }
            // dd($request["tanggal_pulang"]);

            $validatedData = $request->validate([
                'user_id' => 'required',
                'shift_id' => 'required',
                'tanggal_masuk' => 'required',
                'tanggal_pulang' => 'required',
            ]);

            $insert = new MappingShift();
            $insert->user_id = Karyawan::where('id', $validatedData['user_id'])->value('id');
            $insert->nik_karyawan = Karyawan::where('id', $validatedData['user_id'])->value('nomor_identitas_karyawan');
            $insert->nama_karyawan = Karyawan::where('id', $validatedData['user_id'])->value('name');
            $insert->shift_id = Shift::where('id', $validatedData['shift_id'])->value('id');
            $insert->nama_shift = Shift::where('id', $validatedData['shift_id'])->value('nama_shift');
            $insert->tanggal_masuk = $validatedData['tanggal_masuk'];
            $insert->tanggal_pulang = $validatedData['tanggal_pulang'];
            $insert->status_absen = $request['status_absen'];
            $insert->save();
        }
        // dd($week);
        // dd($week);
        $holding = request()->segment(count(request()->segments()));
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'object_id' => $insert->id,
            'kategory_activity' => 'MAPPING SHIFT',
            'activity' => 'TAMBAH MAPPING SHIFT ',
            'description' => 'Menambahkan Jadwal shift karyawan ' . $insert->nama_karyawan . ' Shift ' . $insert->nama_shift,
            'read_status' => 0
        ]);
        return redirect()->back()->with('success', 'data berhasil ditambahkan');
    }

    public function deleteShift(Request $request, $id)
    {
        $holding = request()->segment(count(request()->segments()));
        $delete_mapping = MappingShift::where('id', $id)->first();
        $delete = MappingShift::find($id);
        $delete->delete();
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'kategory_activity' => 'MAPPING SHIFT',
            'activity' => 'HAPUS MAPPING SHIFT ',
            'description' => 'Menghapus Jadwal shift karyawan ' . $delete_mapping->nama_karyawan . ' Shift ' . $delete_mapping->nama_shift,
            'read_status' => 0
        ]);
        return redirect()->back()->with('success', 'Data Berhasil di Delete');
    }

    public function editShift($id)
    {
        $holding = request()->segment(count(request()->segments()));
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
        return view('karyawan.editshift', [
            'title' => 'Edit Shift',
            'menu' => $menus,
            'shift_karyawan' => MappingShift::find($id),
            'holding' => $holding,
            'shift' => Shift::all()
        ]);
    }

    public function prosesEditShift(Request $request)
    {
        // dd('ok');
        date_default_timezone_set('Asia/Jakarta');
        $nama_shift = Shift::where('id', $request['shift_id_update'])->value('nama_shift');
        if ($nama_shift == 'Libur') {
            $request["status_absen"] = "Libur";
        } else if ($nama_shift == 'Malam') {
            $tanggal_pulang = date('Y-m-d', strtotime('+1 days', strtotime($request['tanggal_update'])));
            // dd($tanggal_pulang);
            $request["status_absen"] = NULL;
            $request["tanggal_masuk"] = $request['tanggal_update'];
            $request["tanggal_pulang"] = $tanggal_pulang;
        } else {
            $request["tanggal_masuk"] = $request['tanggal_update'];
            $request["tanggal_pulang"] = $request['tanggal_update'];
            $request["status_absen"] = NULL;
        }
        $rules = [
            'shift_id_update' => 'required',
            'max:255',
            'tanggal_update' => 'required',
            'max:16',
            'keterangan_update' => 'required',
            'max:16',
        ];

        $customMessages = [
            'required' => ':attribute tidak boleh kosong.',
            'unique' => ':attribute tidak boleh sama',
            'email' => ':attribute format salah',
            'min' => ':attribute Kurang',
            'max' => ':attribute Melebihi Batas Maksimal'
        ];
        $validasi = Validator::make($request->all(), $rules, $customMessages);
        // dd($request->all());
        if ($validasi->fails()) {
            $errors = $validasi->errors()->first();
            // dd($errors);
            Alert::error('Gagal', $errors);
            return back()->withInput();
        }
        $validatedData = $request->validate([
            'shift_id_update' => 'required',
            'tanggal_masuk' => 'required',
            'tanggal_pulang' => 'required',
        ]);

        MappingShift::where('id', $request["id_shift"])->update([
            'user_id' => $request['user_id'],
            'shift_id' => Shift::where('id', $validatedData['shift_id_update'])->value('id'),
            'nama_shift' => Shift::where('id', $validatedData['shift_id_update'])->value('nama_shift'),
            'tanggal_masuk' => $validatedData['tanggal_masuk'],
            'tanggal_pulang' => $validatedData['tanggal_pulang'],
        ]);
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'update',
            'description' => 'Mengubah shift karyawan ' . $user_karyawan->name,
        ]);
        $holding = request()->segment(count(request()->segments()));
        return redirect()->back()->with('success', 'data berhasil diupdate');
    }

    public function myProfile()
    {
        return view('karyawan.myprofile', [
            'title' => 'My Profile'
        ]);
    }

    public function myProfileUpdate(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:255',
            'foto_karyawan' => 'image|file|max:10240',
            'telepon' => 'required',
            'password' => 'required',
            'tgl_lahir' => 'required',
            'gender' => 'required',
            'tgl_join' => 'required',
            'status_nikah' => 'required',
            'alamat' => 'required'
        ];


        $userId = Karyawan::find($id);

        if ($request->email != $userId->email) {
            $rules['email'] = 'required|email:dns|unique:karyawans';
        }

        if ($request->username != $userId->username) {
            $rules['username'] = 'required|max:255|unique:karyawans';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('foto_karyawan')) {
            if ($request->foto_karyawan_lama) {
                Storage::delete($request->foto_karyawan_lama);
            }
            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan');
        }


        Karyawan::where('id', $id)->update($validatedData);
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'update',
            'description' => 'Mengubah profile karyawan ' . $request->name,
        ]);
        $request->session()->flash('success', 'data berhasil diupdate');
        return redirect('/my-profile');
    }

    public function editPassMyProfile()
    {
        return view('karyawan.editpassmyprofile', [
            'title' => 'Ganti Password'
        ]);
    }

    public function editPassMyProfileProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|min:6|max:255|confirmed',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        Karyawan::where('id', $id)->update($validatedData);
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'update',
            'description' => 'Mengubah password karyawan ' . $request->name,
        ]);
        $request->session()->flash('success', 'Password Berhasil di Update');
        return redirect('/my-profile');
    }

    public function resetCuti()
    {
        $holding = request()->segment(count(request()->segments()));
        return view('admin.karyawan.reset_cuti', [
            'title' => 'Master Data Reset Cuti',
            'holding' => $holding,
            'data_cuti' => ResetCuti::first()
        ]);
    }

    public function resetCutiProses(Request $request, $id)
    {
        $holding = request()->segment(count(request()->segments()));
        $validatedData = $request->validate([
            'cuti_dadakan' => 'required',
            'cuti_bersama' => 'required',
            'cuti_menikah' => 'required',
            'cuti_diluar_tanggungan' => 'required',
            'cuti_khusus' => 'required',
            'cuti_melahirkan' => 'required',
            'izin_telat' => 'required',
            'izin_pulang_cepat' => 'required'
        ]);

        ResetCuti::where('id', $id)->update($validatedData);
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'update',
            'description' => 'Mengubah master data reset cuti',
        ]);
        return redirect()->back()->with('success', 'Master Cuti Berhasil Diupdate');
    }
    public function inventaris($id, $holding)
    {
        $table = Inventaris::orderBy('nama_inventaris', 'ASC')->where('id_karyawan', $id)->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('option', function ($row) {
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
}
