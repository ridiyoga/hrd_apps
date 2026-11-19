<?php

namespace App\Http\Controllers;

use App\Mail\DiterimaManagerMail;
use App\Mail\DiterimaWawancaraMail;
use App\Mail\LoloslangsungMail;
use App\Mail\LolosposisilainMail;
use App\Mail\ManagerlainMail;
use App\Mail\TidaklolosMail;
use App\Models\ActivityLog;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Bagian;
use App\Models\DetailEsai;
use App\Models\Divisi;
use App\Models\User;
use App\Models\Recruitment;
use App\Models\RecruitmentUser;
use App\Models\RecruitmentInterview;
use App\Models\UserCareer;
use App\Models\Ujian;
use App\Models\DetailUjian;
use App\Models\WaktuUjian;
use App\Models\PgSiswa;
use App\Models\EssaySiswa;
use App\Models\DetailEssay;
use App\Models\District;
use App\Models\Holding;
use App\Models\InterviewAdmin;
use App\Models\InterviewUser;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KaryawanKeahlian;
use App\Models\KaryawanKesehatan;
use App\Models\KaryawanKesehatanKecelakaan;
use App\Models\KaryawanKesehatanPengobatan;
use App\Models\KaryawanKesehatanRS;
use App\Models\KaryawanPendidikan;
use App\Models\KaryawanRiwayat;
use App\Models\Kecamatan;
use App\Models\Menu;
use App\Models\Pembobotan;
use App\Models\RecruitmentCV;
use App\Models\RecruitmentKeahlian;
use App\Models\RecruitmentKesehatan;
use App\Models\RecruitmentKesehatanKecelakaan;
use App\Models\RecruitmentKesehatanPengobatan;
use App\Models\RecruitmentKesehatanRS;
use App\Models\RecruitmentPendidikan;
use App\Models\RecruitmentReferensi;
use App\Models\RecruitmentRiwayat;
use App\Models\RecruitmentUserRecord;
use App\Models\RoleUsers;
use App\Models\Site;
use App\Models\UjianEsaiJawabDetail;
use App\Models\UjianKategori;
use App\Models\UsersCareer;
use App\Services\BroadcastServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

use RealRashid\SweetAlert\Facades\Alert;
use DB;
use DivisionByZeroError;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Exists;
use PhpParser\Builder\Function_;

class RecruitmentController extends Controller
{
    protected BroadcastServices $mekari;

    public function __construct(BroadcastServices $mekari)
    {
        $this->mekari = $mekari;
    }
    private function formatPhoneIndonesia($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        if (str_starts_with($number, '8')) {
            return '62' . $number;
        }

        return $number;
    }
    public function pg_recruitment($holding, Request $request)
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

        // $menu_active = Menu::where('url', 'pg-data-recruitment')->first();
        // $holding = request()->segment(count(request()->segments()));
        $holdings = Holding::where('holding_code', $holding)->first();
        $aktif = Recruitment::where('holding_recruitment', $holdings->id)
            ->where('status_recruitment', 0)
            ->where('end_recruitment', '>=', now()->toDateString())->count();
        $kadaluarsa = Recruitment::where('holding_recruitment', $holdings->id)
            ->where('status_recruitment', 0)
            ->where('end_recruitment', '<', now()->toDateString())->count();
        // dd($table);
        return view('admin.recruitment-users.recruitment.index', [
            // return view('karyawan.index', [
            'title'             => 'Data Recruitment',
            'menus'             => $menus,
            'aktif'             => $aktif,
            'kadaluarsa'        => $kadaluarsa,
            // 'menu_active'       => $menu_active,
            'holding'           => $holdings,
            'site'              => Site::where('site_holding_category', $holdings->id)->get(),
            'departemen'        => Departemen::where('holding', $holdings->id)->orderBy('nama_departemen', 'ASC')->get(),
            'data_departemen'   => Departemen::all(),
            'data_bagian'       => Bagian::with('Divisi')->where('holding', $holdings->id)->get(),
            'data_jabatan'      => Jabatan::with('Bagian')->where('holding', $holdings->id)->get(),
            'data_dept'         => Departemen::orderBy('nama_departemen', 'asc')->where('holding', $holdings->id)->get(),
            'data_divisi'       => Divisi::orderBy('nama_divisi', 'asc')->where('holding', $holdings->id)->get()
        ]);
    }

    public function dt_recruitment(Request $request, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();

        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        // dd($request->start_date);

        $query = Recruitment::with([
            'Jabatan' => function ($query) {
                $query->with([
                    'Bagian' => function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
                $query->orderBy('nama_jabatan', 'ASC');
            },
        ])->with([
            'Sites' => function ($query) {
                $query;
            }
        ])->with(['jumlahPelamar' => function ($query) {
            $query->where('status', '0');
        }])->with(['jumlahRanking' => function ($query) {
            $query->where('status_lanjutan', null)->where('status', '1a');
        }])
            ->where('holding_recruitment', $holdings->id)
            // ->whereBetween('created_at', [$now, $now1])
            ->whereBetween('created_recruitment', [$now, $now1])
            ->orderBy('created_at', 'DESC');
        if (!empty($request->departemen_filter)) {
            $query->whereIn('nama_dept', (array)$request->departemen_filter ?? []);
        }

        if (!empty($request->divisi_filter)) {
            $query->whereIn('nama_divisi', (array)$request->divisi_filter ?? []);
        }

        if (!empty($request->bagian_filter)) {
            $query->whereIn('nama_bagian', (array)$request->bagian_filter ?? []);
        }

        if (!empty($request->jabatan_filter)) {
            $query->whereIn('nama_jabatan', (array)$request->jabatan_filter ?? []);
        }
        // $table = $query->limit(2)->get();
        $table = $query->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('legal_number', function ($row) {
                    return $row->legal_number;
                })
                ->addColumn('status_recruitment', function ($row) use ($holdings) {
                    if ($row->status_recruitment == 0) {
                        $status = '<button id="btn_status_aktif"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            type="button" class="btn btn-sm btn-success ">
                            <i class="tf-icons mdi mdi-account-search"> </i>
                            &nbsp;AKTIF
                        </button>';
                    } else {
                        $status = '<button id="btn_status_naktif"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            type="button" class="btn btn-sm btn-danger ">
                            <i class="tf-icons mdi mdi-account-off"></i>
                            &nbspN&nbsp;AKTIF
                        </button>';
                    }
                    return $status;
                })
                ->addColumn('pelamar', function ($row) use ($holding) {
                    $notifikasi = $row->jumlahPelamar()
                        ->whereHas('AuthLogin.recruitmentCV', function ($query) {
                            $query->whereNotNull('nama_lengkap')
                                ->where('nama_lengkap', '<>', '');
                        })->where('status', '0')
                        ->count();
                    $url = url('/pg-data-recruitment/data-list-pelamar/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-info position-relative">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ' . $notifikasi . '
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>';
                    return $btn;
                })
                ->addColumn('ranking', function ($row) use ($holding) {
                    $notifikasi = $row->jumlahRanking->count();
                    $url = url('/pg-data-recruitment/data-list-ranking/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-primary position-relative">
                                <i class="tf-icons mdi mdi-chevron-triple-up me-1"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ' . $notifikasi . '
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>';
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('penempatan', function ($row) {
                    return $row->Sites->site_name ?? '-';
                })
                ->addColumn('nama_jabatan', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_jabatan = 'vv';
                    } else {
                        $nama_jabatan = $row->Jabatan->nama_jabatan ?? '-';
                    }
                    return $nama_jabatan;
                })
                ->addColumn('nama_bagian', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_bagian = 'a';
                    } else {
                        $nama_bagian = $row->Jabatan->Bagian->nama_bagian ?? '-';
                    }
                    return $nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_divisi = 'v';
                    } else {
                        $nama_divisi = $row->Jabatan->Bagian->Divisi->nama_divisi ?? '-';
                    }
                    return $nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_departemen = 'vv';
                    } else {
                        $nama_departemen = $row->Jabatan->Bagian->Divisi->Departemen->nama_departemen ?? '-';
                    }
                    return $nama_departemen;
                })


                ->addColumn('desc_recruitment', function ($row) {
                    $desc = htmlspecialchars($row->desc_recruitment, ENT_QUOTES, 'UTF-8');
                    $btn = '<button id="btn_lihat_syarat"
                                data-id="' . $row->id . '"
                                data-desc="' . $desc . '" 
                                type="button" class="btn btn-sm btn-info ">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                Lihat&nbsp;Syarat
                            </button>';
                    return $btn;
                })
                ->addColumn('deadline_recruitment', function ($row) {
                    return $row->deadline_recruitment ?? '-';
                })
                ->addColumn('penggantian_penambahan', function ($row) {
                    if ($row->penggantian_penambahan == '1') {
                        return '<span class="badge bg-label-success">Penggantian</span>';
                    } else {
                        return '<span class="badge bg-label-info">Penambahan</span>';
                    }
                })
                ->addColumn('surat_penambahan', function ($row) {
                    if ($row->surat_penambahan != null) {
                        return '<a type="button" href="' . asset('/storage/surat_penambahan/' . $row->surat_penambahan) . '" class="btn btn-sm btn-danger" target="_blank" id="btn_ktp" accept="application/pdf"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Lihat&nbsp;Surat</button>';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('kuota', function ($row) {
                    return $row->kuota ?? '-';
                })
                ->addColumn('option', function ($row) use ($holdings) {
                    $btn =
                        '<button id="btn_edit_recruitment"
                            data-id="' . $row->id . '"
                            data-penggantian_penambahan="' . $row->penggantian_penambahan . '"
                            data-surat_penambahan="' . $row->surat_penambahan . '"
                            data-kuota="' . $row->kuota . '"
                            data-penempatan="' . $row->penempatan . '"
                            data-dept="' . $row->nama_dept . '"
                            data-divisi="' . $row->nama_divisi . '"
                            data-bagian="' . $row->nama_bagian . '"
                            data-jabatan="' . $row->nama_jabatan . '"
                            data-tanggal_awal="' . $row->created_recruitment . '"
                            data-tanggal_akhir="' . $row->end_recruitment . '"
                            data-deadline="' . $row->deadline_recruitment . '"
                            data-holding="' . $holdings->id . '"
                            data-desc="' . e($row->desc_recruitment) . '"
                            type="button"
                            class="btn btn-icon btn-warning waves-effect waves-light">
                                <span class="tf-icons mdi mdi-pencil-outline"></span>
                        </button>
                        <button type="button" id="btn_delete_recruitment"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <span class="tf-icons mdi mdi-delete-outline"></span>
                        </button>
                        ';
                    return $btn;
                })

                ->rawColumns([
                    'legal_number',
                    'created_at',
                    'penempatan',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_bagian',
                    'nama_jabatan',
                    'created_recruitment',
                    'end_recruitment',
                    'desc_recruitment',
                    'pelamar',
                    'status_recruitment',
                    'deadline_recruitment',
                    'penggantian_penambahan',
                    'surat_penambahan',
                    'option',
                    'ranking',
                    'kuota'
                ])
                ->make(true);
        }
    }
    public function dt_recruitment_aktif(Request $request, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();

        // $now = Carbon::parse($request->start_date)->startOfDay();
        // $now1 = Carbon::parse($request->end_date)->endOfDay();
        // dd($request->start_date);

        $query = Recruitment::with([
            'Jabatan' => function ($query) {
                $query->with([
                    'Bagian' => function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
                $query->orderBy('nama_jabatan', 'ASC');
            },
        ])->with([
            'Sites' => function ($query) {
                $query;
            }
        ])->with(['jumlahPelamar' => function ($query) {
            $query->where('status', '0');
        }])->with(['jumlahRanking' => function ($query) {
            $query->where('status_lanjutan', null)->where('status', '1a');
        }])
            ->where('holding_recruitment', $holdings->id)
            // ->whereBetween('created_at', [$now, $now1])
            // ->whereBetween('created_recruitment', [$now, $now1])
            ->where('status_recruitment', 0)->where('end_recruitment', '>=', now()->toDateString())
            ->orderBy('created_at', 'DESC');
        if (!empty($request->departemen_filter)) {
            $query->whereIn('nama_dept', (array)$request->departemen_filter ?? []);
        }

        if (!empty($request->divisi_filter)) {
            $query->whereIn('nama_divisi', (array)$request->divisi_filter ?? []);
        }

        if (!empty($request->bagian_filter)) {
            $query->whereIn('nama_bagian', (array)$request->bagian_filter ?? []);
        }

        if (!empty($request->jabatan_filter)) {
            $query->whereIn('nama_jabatan', (array)$request->jabatan_filter ?? []);
        }
        // $table = $query->limit(2)->get();
        $table = $query->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('legal_number', function ($row) {
                    return $row->legal_number;
                })
                ->addColumn('status_recruitment', function ($row) use ($holdings) {
                    if ($row->status_recruitment == 0) {
                        $status = '<button id="btn_status_aktif"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            type="button" class="btn btn-sm btn-success ">
                            <i class="tf-icons mdi mdi-account-search"> </i>
                            &nbsp;AKTIF
                        </button>';
                    } else {
                        $status = '<button id="btn_status_naktif"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            type="button" class="btn btn-sm btn-danger ">
                            <i class="tf-icons mdi mdi-account-off"></i>
                            &nbspN&nbsp;AKTIF
                        </button>';
                    }
                    return $status;
                })
                ->addColumn('pelamar', function ($row) use ($holding) {
                    $notifikasi = $row->jumlahPelamar()
                        ->whereHas('AuthLogin.recruitmentCV', function ($query) {
                            $query->whereNotNull('nama_lengkap')
                                ->where('nama_lengkap', '<>', '');
                        })->where('status', '0')
                        ->count();
                    $url = url('/pg-data-recruitment/data-list-pelamar/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-info position-relative">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ' . $notifikasi . '
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>';
                    return $btn;
                })
                ->addColumn('ranking', function ($row) use ($holding) {
                    $notifikasi = $row->jumlahRanking->count();
                    $url = url('/pg-data-recruitment/data-list-ranking/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-primary position-relative">
                                <i class="tf-icons mdi mdi-chevron-triple-up me-1"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ' . $notifikasi . '
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>';
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('penempatan', function ($row) {
                    return $row->Sites->site_name ?? '-';
                })
                ->addColumn('nama_jabatan', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_jabatan = 'vv';
                    } else {
                        $nama_jabatan = $row->Jabatan->nama_jabatan ?? '-';
                    }
                    return $nama_jabatan;
                })
                ->addColumn('nama_bagian', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_bagian = 'a';
                    } else {
                        $nama_bagian = $row->Jabatan->Bagian->nama_bagian ?? '-';
                    }
                    return $nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_divisi = 'v';
                    } else {
                        $nama_divisi = $row->Jabatan->Bagian->Divisi->nama_divisi ?? '-';
                    }
                    return $nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_departemen = 'vv';
                    } else {
                        $nama_departemen = $row->Jabatan->Bagian->Divisi->Departemen->nama_departemen ?? '-';
                    }
                    return $nama_departemen;
                })


                ->addColumn('desc_recruitment', function ($row) {
                    $desc = htmlspecialchars($row->desc_recruitment, ENT_QUOTES, 'UTF-8');
                    $btn = '<button id="btn_lihat_syarat"
                                data-id="' . $row->id . '"
                                data-desc="' . $desc . '" 
                                type="button" class="btn btn-sm btn-info ">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                Lihat&nbsp;Syarat
                            </button>';
                    return $btn;
                })
                ->addColumn('deadline_recruitment', function ($row) {
                    return $row->deadline_recruitment ?? '-';
                })
                ->addColumn('penggantian_penambahan', function ($row) {
                    if ($row->penggantian_penambahan == '1') {
                        return '<span class="badge bg-label-success">Penggantian</span>';
                    } else {
                        return '<span class="badge bg-label-info">Penambahan</span>';
                    }
                })
                ->addColumn('surat_penambahan', function ($row) {
                    if ($row->surat_penambahan != null) {
                        return '<a type="button" href="' . asset('/storage/surat_penambahan/' . $row->surat_penambahan) . '" class="btn btn-sm btn-danger" target="_blank" id="btn_ktp" accept="application/pdf"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Lihat&nbsp;Surat</button>';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('kuota', function ($row) {
                    return $row->kuota ?? '-';
                })
                ->addColumn('option', function ($row) use ($holdings) {
                    $btn =
                        '<button id="btn_edit_recruitment"
                            data-id="' . $row->id . '"
                            data-penggantian_penambahan="' . $row->penggantian_penambahan . '"
                            data-surat_penambahan="' . $row->surat_penambahan . '"
                            data-kuota="' . $row->kuota . '"
                            data-penempatan="' . $row->penempatan . '"
                            data-dept="' . $row->nama_dept . '"
                            data-divisi="' . $row->nama_divisi . '"
                            data-bagian="' . $row->nama_bagian . '"
                            data-jabatan="' . $row->nama_jabatan . '"
                            data-tanggal_awal="' . $row->created_recruitment . '"
                            data-tanggal_akhir="' . $row->end_recruitment . '"
                            data-deadline="' . $row->deadline_recruitment . '"
                            data-holding="' . $holdings->id . '"
                            data-desc="' . e($row->desc_recruitment) . '"
                            type="button"
                            class="btn btn-icon btn-warning waves-effect waves-light">
                                <span class="tf-icons mdi mdi-pencil-outline"></span>
                        </button>
                        <button type="button" id="btn_delete_recruitment"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <span class="tf-icons mdi mdi-delete-outline"></span>
                        </button>
                        ';
                    return $btn;
                })

                ->rawColumns([
                    'legal_number',
                    'created_at',
                    'penempatan',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_bagian',
                    'nama_jabatan',
                    'created_recruitment',
                    'end_recruitment',
                    'desc_recruitment',
                    'pelamar',
                    'ranking',
                    'status_recruitment',
                    'deadline_recruitment',
                    'penggantian_penambahan',
                    'surat_penambahan',
                    'option',
                    'kuota'
                ])
                ->make(true);
        }
    }
    public function dt_recruitment_kadaluarsa(Request $request, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();

        // $now = Carbon::parse($request->start_date)->startOfDay();
        // $now1 = Carbon::parse($request->end_date)->endOfDay();
        // dd($request->start_date);

        $query = Recruitment::with([
            'Jabatan' => function ($query) {
                $query->with([
                    'Bagian' => function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
                $query->orderBy('nama_jabatan', 'ASC');
            },
        ])->with([
            'Sites' => function ($query) {
                $query;
            }
        ])->with(['jumlahPelamar' => function ($query) {
            $query->where('status', '0');
        }])->with(['jumlahRanking' => function ($query) {
            $query->where('status_lanjutan', null)->where('status', '1a');
        }])
            ->where('holding_recruitment', $holdings->id)
            // ->whereBetween('created_at', [$now, $now1])
            // ->whereBetween('created_recruitment', [$now, $now1])
            ->orderBy('created_at', 'DESC');
        if (!empty($request->departemen_filter)) {
            $query->whereIn('nama_dept', (array)$request->departemen_filter ?? []);
        }

        if (!empty($request->divisi_filter)) {
            $query->whereIn('nama_divisi', (array)$request->divisi_filter ?? []);
        }

        if (!empty($request->bagian_filter)) {
            $query->whereIn('nama_bagian', (array)$request->bagian_filter ?? []);
        }

        if (!empty($request->jabatan_filter)) {
            $query->whereIn('nama_jabatan', (array)$request->jabatan_filter ?? []);
        }
        // $table = $query->limit(2)->get();
        $table = $query->where('status_recruitment', 0)->where('end_recruitment', '<', now()->toDateString())->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('legal_number', function ($row) {
                    return $row->legal_number;
                })
                ->addColumn('status_recruitment', function ($row) use ($holdings) {
                    if ($row->status_recruitment == 0) {
                        $status = '<button id="btn_status_aktif"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            type="button" class="btn btn-sm btn-success ">
                            <i class="tf-icons mdi mdi-account-search"> </i>
                            &nbsp;AKTIF
                        </button>';
                    } else {
                        $status = '<button id="btn_status_naktif"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            type="button" class="btn btn-sm btn-danger ">
                            <i class="tf-icons mdi mdi-account-off"></i>
                            &nbspN&nbsp;AKTIF
                        </button>';
                    }
                    return $status;
                })
                ->addColumn('pelamar', function ($row) use ($holding) {
                    $notifikasi = $row->jumlahPelamar()
                        ->whereHas('AuthLogin.recruitmentCV', function ($query) {
                            $query->whereNotNull('nama_lengkap')
                                ->where('nama_lengkap', '<>', '');
                        })->where('status', '0')
                        ->count();
                    $url = url('/pg-data-recruitment/data-list-pelamar/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-info position-relative">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ' . $notifikasi . '
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>';
                    return $btn;
                })
                ->addColumn('ranking', function ($row) use ($holding) {
                    $notifikasi = $row->jumlahRanking->count();
                    $url = url('/pg-data-recruitment/data-list-ranking/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-primary position-relative">
                                <i class="tf-icons mdi mdi-chevron-triple-up me-1"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ' . $notifikasi . '
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>';
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y');
                })
                ->addColumn('penempatan', function ($row) {
                    return $row->Sites->site_name ?? '-';
                })
                ->addColumn('nama_jabatan', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_jabatan = 'vv';
                    } else {
                        $nama_jabatan = $row->Jabatan->nama_jabatan ?? '-';
                    }
                    return $nama_jabatan;
                })
                ->addColumn('nama_bagian', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_bagian = 'a';
                    } else {
                        $nama_bagian = $row->Jabatan->Bagian->nama_bagian ?? '-';
                    }
                    return $nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_divisi = 'v';
                    } else {
                        $nama_divisi = $row->Jabatan->Bagian->Divisi->nama_divisi ?? '-';
                    }
                    return $nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    if (!$row->Jabatan) {
                        $nama_departemen = 'vv';
                    } else {
                        $nama_departemen = $row->Jabatan->Bagian->Divisi->Departemen->nama_departemen ?? '-';
                    }
                    return $nama_departemen;
                })


                ->addColumn('desc_recruitment', function ($row) {
                    $desc = htmlspecialchars($row->desc_recruitment, ENT_QUOTES, 'UTF-8');
                    $btn = '<button id="btn_lihat_syarat"
                                data-id="' . $row->id . '"
                                data-desc="' . $desc . '" 
                                type="button" class="btn btn-sm btn-info ">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                Lihat&nbsp;Syarat
                            </button>';
                    return $btn;
                })
                ->addColumn('deadline_recruitment', function ($row) {
                    return $row->deadline_recruitment ?? '-';
                })
                ->addColumn('penggantian_penambahan', function ($row) {
                    if ($row->penggantian_penambahan == '1') {
                        return '<span class="badge bg-label-success">Penggantian</span>';
                    } else {
                        return '<span class="badge bg-label-info">Penambahan</span>';
                    }
                })
                ->addColumn('surat_penambahan', function ($row) {
                    if ($row->surat_penambahan != null) {
                        return '<a type="button" href="' . asset('/storage/surat_penambahan/' . $row->surat_penambahan) . '" class="btn btn-sm btn-danger" target="_blank" id="btn_ktp" accept="application/pdf"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Lihat&nbsp;Surat</button>';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('kuota', function ($row) {
                    return $row->kuota ?? '-';
                })
                ->addColumn('option', function ($row) use ($holdings) {
                    $btn =
                        '<button id="btn_edit_recruitment"
                            data-id="' . $row->id . '"
                            data-penggantian_penambahan="' . $row->penggantian_penambahan . '"
                            data-surat_penambahan="' . $row->surat_penambahan . '"
                            data-kuota="' . $row->kuota . '"
                            data-penempatan="' . $row->penempatan . '"
                            data-dept="' . $row->nama_dept . '"
                            data-divisi="' . $row->nama_divisi . '"
                            data-bagian="' . $row->nama_bagian . '"
                            data-jabatan="' . $row->nama_jabatan . '"
                            data-tanggal_awal="' . $row->created_recruitment . '"
                            data-tanggal_akhir="' . $row->end_recruitment . '"
                            data-deadline="' . $row->deadline_recruitment . '"
                            data-holding="' . $holdings->id . '"
                            data-desc="' . e($row->desc_recruitment) . '"
                            type="button"
                            class="btn btn-icon btn-warning waves-effect waves-light">
                                <span class="tf-icons mdi mdi-pencil-outline"></span>
                        </button>
                        <button type="button" id="btn_delete_recruitment"
                            data-id="' . $row->id . '"
                            data-holding="' . $holdings->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <span class="tf-icons mdi mdi-delete-outline"></span>
                        </button>
                        ';
                    return $btn;
                })

                ->rawColumns([
                    'legal_number',
                    'created_at',
                    'penempatan',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_bagian',
                    'nama_jabatan',
                    'created_recruitment',
                    'end_recruitment',
                    'desc_recruitment',
                    'pelamar',
                    'ranking',
                    'status_recruitment',
                    'deadline_recruitment',
                    'penggantian_penambahan',
                    'surat_penambahan',
                    'option',
                    'kuota'
                ])
                ->make(true);
        }
    }

    function create(Request $request, $holding)
    {
        // dd($request->all());
        if ($request->penggantian_penambahan == 2) {
            $surat_penambahan = 'required|max:5000';
        } else {
            $surat_penambahan = 'nullable';
        }
        $rules = [
            'penempatan'                => 'required',
            'penggantian_penambahan'    => 'required',
            'surat_penambahan'          => $surat_penambahan,
            'kuota'                     => 'required',
            'nama_dept'                 => 'required',
            'nama_divisi'               => 'required',
            'nama_bagian'               => 'required|max:255',
            'nama_jabatan'              => 'required',
            'created_recruitment'       => 'required',
            'holding_recruitment'       => 'required',
            'end_recruitment'           => 'required',
            'deadline_recruitment'      => 'required',
            'desc_recruitment'          => 'required',
        ];
        $customessages =
            [
                'required'          => ':attribute tidak boleh kosong!',
                'mimes'             => ':attribute harus berupa PDF!',
                'max'               => ':attribute maksimal 5MB'
            ];

        $id_recruitment = Uuid::uuid4();
        try {
            $validator = Validator::make($request->all(), $rules, $customessages);
            if ($validator->fails()) {
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                // dd($nama_menu->name);

            }
            $tanggal = date('ymd');
            $st = Holding::where('holding_code', $holding)->first();
            //mencari nomor terakhir
            $today = date('Y-m-d');
            $no_terakhir = Recruitment::whereDate('created_at', $today)->where('holding_recruitment', $request->holding_recruitment)->latest('created_at')->first();
            if ($no_terakhir == null) {
                $no = '001';
            } else {
                $legal_terakhir = substr($no_terakhir->legal_number, -3);
                $hasil = $legal_terakhir + 1;
                $no = str_pad($hasil, 3, '0', STR_PAD_LEFT);
            }
            // dd($today, $no_terakhir->legal_number, $no_terakhir->created_at, $no);
            //mencari nomor terakhir end
            // Surat Penambahan
            if ($request->surat_penambahan != null) {
                // dd('o');
                $file = $request->file('surat_penambahan')->store('surat_penambahan');
                $file_penambahan = basename($file);
            } else {
                $file_penambahan = null;
            }
            // Surat Penambahan End
            Recruitment::insert(
                [
                    'id'                        => $id_recruitment,
                    'legal_number'              => $st->holding_category . '/REC/' . $st->holding_number . '00' . $tanggal . '/' . $no,
                    'holding_recruitment'       => $request->holding_recruitment,
                    'penempatan'                => $request->penempatan,
                    'penggantian_penambahan'    => $request->penggantian_penambahan,
                    'surat_penambahan'          => $file_penambahan,
                    'kuota'                     => $request->kuota,
                    'nama_dept'                 => $request->nama_dept,
                    'nama_divisi'               => $request->nama_divisi,
                    'nama_bagian'               => $request->nama_bagian,
                    'nama_jabatan'              => $request->nama_jabatan,
                    'status_recruitment'        => '0',
                    'created_recruitment'       => $request->created_recruitment,
                    'end_recruitment'           => $request->end_recruitment,
                    'deadline_recruitment'      => $request->deadline_recruitment,
                    'desc_recruitment'          => $request->desc_recruitment,
                    'created_at'                => date('Y-m-d H:i:s'),
                ]
            );

            // Merekam aktivitas pengguna
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $divisi = Divisi::where('id', $request->nama_divisi)->first();
            $jabatan = Jabatan::where('id', $request->nama_jabatan)->first();
            // dd($st->id, $st->holding_categori);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_recruitment,
                'holding_id' => $st->id,
                'holding_category' => $st->holding_category,
                'table' => 'recruitment_cv',
                'activity' => 'create',
                'description' => 'Menambahkan data Recruitment baru ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }



    function update(Request $request)
    {
        // dd($request->all());
        if ($request->penggantian_penambahan == 2) {
            $surat_penambahan = 'max:5000';
        } else {
            $surat_penambahan = 'nullable';
        }
        $rules = [
            'penempatan'                => 'required',
            'penggantian_penambahan'    => 'required',
            'surat_penambahan'          => $surat_penambahan,
            'kuota'                     => 'required',
            'nama_dept'                 => 'required',
            'nama_divisi'               => 'required',
            'nama_bagian'               => 'required|max:255',
            'nama_jabatan'              => 'required',
            'created_recruitment'       => 'required',
            'holding_recruitment'       => 'required',
            'end_recruitment'           => 'required',
            'deadline_recruitment'      => 'required',
            'desc_recruitment'          => 'required',
        ];
        $customessages =
            [
                'required'           => ':attribute tidak boleh kosong!',
                'mimes'             => ':attribute harus berupa PDF!',
                'max'               => ':attribute maksimal 5MB'
            ];

        try {
            $validator = Validator::make($request->all(), $rules, $customessages);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ]);
            }
            if ($request->surat_penambahan != null) {
                if ($request->old_file != null) {
                    if (Storage::disk('surat_penambahan')->exists($request->old_file)) {
                        Storage::disk('surat_penambahan')->delete($request->old_file);
                    }
                }
                $file = $request->file('surat_penambahan')->store('surat_penambahan');
                $surat_penambahan = basename($file);
            } else {
                $surat_penambahan = $request->old_file;
            }
            $data = Recruitment::where('id', $request->id)->update([
                'holding_recruitment'       => $request->holding_recruitment,
                'penempatan'                => $request->penempatan,
                'penggantian_penambahan'    => $request->penggantian_penambahan,
                'surat_penambahan'          => $surat_penambahan,
                'kuota'                     => $request->kuota,
                'nama_dept'                 => $request->nama_dept,
                'nama_divisi'               => $request->nama_divisi,
                'nama_bagian'               => $request->nama_bagian,
                'nama_jabatan'              => $request->nama_jabatan,
                'created_recruitment'       => $request->created_recruitment,
                'end_recruitment'           => $request->end_recruitment,
                'deadline_recruitment'      => $request->deadline_recruitment,
                'desc_recruitment'          => $request->desc_recruitment,
                'created_at'                => date('Y-m-d H:i:s'),
            ]);
            if ($data) {
                // Merekam aktivitas pengguna
                $holding = Holding::where('id', $request->holding_recruitment)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $request->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $request->nama_jabatan)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holding->id,
                    'holding_category' => $holding->holding_category,
                    'table' => 'recruitment_cv',
                    'activity' => 'update',
                    'description' => 'Update data Recruitment ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);
                return response()->json([
                    'code' => 200,
                    'message' => 'Data Berhasil Disimpan',
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    function delete($id, $holding, $menu_id)
    {
        // dd($id);

        // $holding = request()->segment(count(request()->segments()));
        try {
            $data = RecruitmentUser::where('recruitment_admin_id', $id)->first();
            if ($data == null) {
                $hapus_first    = Recruitment::where('id', $id)->where('holding_recruitment', $holding)->first();
                Recruitment::where('id', $id)->where('holding_recruitment', $holding)->delete();
                // dd($id);
                if ($hapus_first->surat_penambahan != NULL) {

                    Storage::disk('surat_penambahan')->delete($hapus_first->surat_penambahan);
                }
                $holdings = Holding::where('id', $holding)->first();
                $nama_menu = Menu::where('id', $menu_id)->first();
                $user_name = User::where('id', Auth::user()->id)->first();
                $divisi = Divisi::where('id', $hapus_first->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $hapus_first->nama_jabatan)->first();
                // dd($nama_menu->name);
                ActivityLog::create([
                    'user_id' => $user_name->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $menu_id,
                    'menu_name' => $nama_menu->name,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'id_table' => $hapus_first->id,
                    'table' => 'recruitment_cv',
                    'activity' => 'delete',
                    'description' => 'Menghapus data Recruitment ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    function update_status($id, $holding, $menu_id)
    {
        // dd($id);
        $holdings = Holding::where('holding_code', $holding)->first();

        $recruitment = Recruitment::where('id', $id)->where('holding_recruitment', $holdings->id)->first();
        $nama_menu = Menu::where('id', $menu_id)->first();
        $user_name = User::where('id', Auth::user()->id)->first();
        $divisi = Divisi::where('id', $recruitment->nama_divisi)->first();
        $jabatan = Jabatan::where('id', $recruitment->nama_jabatan)->first();
        if ($recruitment->status_recruitment == 0) {
            Recruitment::where('id', $id)->where('holding_recruitment', $holdings->id)->update([
                'status_recruitment' => 1,
            ]);
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id,
                'holding_category' => $holdings->holding_category,
                'table' => 'recruitment_user',
                'activity' => 'update',
                'description' => 'Update data Recruitment Status Close ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                'keterangan' => 'Success',
            ]);
        } else {
            Recruitment::where('id', $id)->where('holding_recruitment', $holdings->id)->update([
                'status_recruitment' => 0,
            ]);
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id,
                'holding_id' => $holdings->id,
                'holding_category' => $holdings->holding_category,
                'table' => 'recruitment_user',
                'activity' => 'update',
                'description' => 'Update data Recruitment Status Open ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                'keterangan' => 'Success',
            ]);
        }
        // ActivityLog::create([
        //     'user_id' => Auth::user()->id,
        //     'activity' => 'update',
        //     'description' => 'Update data Recruitment Status Close ' . Auth::user()->name,
        // ]);
        return redirect()->back()->with('success', 'Data Berhasil di Diupdate');
    }

    function pg_list_pelamar($id, $holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $get_recruitment_user_id = RecruitmentUser::where('recruitment_admin_id', $id)
            ->whereDate('tanggal_konfirmasi', '<', date('Y-m-d H:i:s'))
            ->where('tanggal_konfirmasi', '!=', null)
            ->where('feedback', null)
            ->where('status_user', '1')
            ->get();
        if ($get_recruitment_user_id != null) {
            foreach ($get_recruitment_user_id as $ii) {
                RecruitmentUser::where('id', $ii->id)->update([
                    'status'        => '3',
                    'status_user'   => '3',
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
                $add_record = RecruitmentUserRecord::where('recruitment_user_id', $ii->id)->where('status', '3')->where('status_user', '3')->count();
                if ($add_record == 0) {
                    RecruitmentUserRecord::insert([
                        'id' => Uuid::uuid4(),
                        'recruitment_user_id' => $ii->id,
                        'status' => '3',
                        'status_user' => '3',
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $cv = RecruitmentCV::where('users_career_id', $ii->users_career_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $ii->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $ii->nama_jabatan)->first();
                // dd($divisi);
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_name' => 'Data Recruitment',
                    'id_table' => $request->recruitment_user_id,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'pelamar otomatis tertolak karena tidak konfirmasi ' . $cv->nama_lengkap . ' pada ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);
            }
        }
        // end Hapus pelamar kadaluarsa
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

        // $recruitment_user_id = RecruitmentUser::where('recruitment_admin_id', $id)->first();
        return view('admin.recruitment-users.recruitment.list_pelamar', [
            'title' => 'Data Recruitment',
            'menus' => $menus,
            'holding'   => $holdings,
            'recruitment_admin_id'   => $id,
        ]);
    }
    function user_meta($id, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $user_meta =  RecruitmentUser::with('AuthLogin.recruitmentCV')
            ->where('holding', $holdings->id)
            ->where('status', '0')
            ->where('recruitment_admin_id', $id)
            ->whereHas('AuthLogin.recruitmentCV', function ($query) {
                $query->whereNotNull('nama_lengkap')
                    ->where('nama_lengkap', '!=', '');
            })
            ->get();
        return DataTables::of($user_meta)

            ->addColumn('pelamar', function ($row) {
                return $row->AuthLogin->recruitmentCV->nama_lengkap ?? '-';
            })
            ->addColumn('no_wa', function ($row) {
                return $row->AuthLogin->nomor_whatsapp ?? '-';
            })

            ->addColumn('status', function ($row) {
                // dd($row->status);
                if ($row->status == '0') {
                    return '<span class="badge bg-label-primary">Belum Dilihat</span>';
                } else {
                    return 'Status Tidak Dikenal';
                }
            })
            ->addColumn('lihat_cv', function ($row) use ($holding) {
                return '<a href="/pg-data-recruitment/pelamar-detail/' . $row->id . '/' . $holding . '" type="button" class="btn btn-sm btn-info"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Detail&nbsp;CV</a>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at;
            })

            ->rawColumns(['pelamar', 'no_wa', 'status', 'lihat_cv'])
            ->make(true);
    }
    function user_kandidat($id, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $user_kandidat =  RecruitmentUser::with([
            'AuthLogin' => function ($query) {
                $query->with([
                    'recruitmentCV' => function ($query) {
                        $query->orderBy('nama_lengkap');
                    }
                ]);
            }
        ])
            ->where('holding', $holdings->id)
            ->where('recruitment_admin_id', $id)
            ->whereIn('status', array('1', '1a', '2a'))
            ->get();
        // dd($user_kandidat);
        return DataTables::of($user_kandidat)

            ->addColumn('pelamar', function ($row) {
                return $row->AuthLogin->recruitmentCV->nama_lengkap ?? '-';
            })
            ->addColumn('no_wa', function ($row) {
                return $row->AuthLogin->nomor_whatsapp ?? '-';
            })
            ->addColumn('tanggal_wawancara', function ($row) {
                return $row->tanggal_wawancara;
            })
            ->addColumn('tempat_wawancara', function ($row) {
                return $row->tempat_wawancara;
            })
            ->addColumn('waktu_wawancara', function ($row) {
                return $row->waktu_wawancara;
            })
            ->addColumn('feedback', function ($row) {
                if ($row->feedback == '1') {
                    return '<span class="badge bg-label-success">Bersedia Wawancara</span>';
                } else {
                    return '<span class="badge bg-label-warning">Menunggu Konfirmasi</span>';
                }
            })
            ->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    return '<span class="badge bg-label-success">Kandidat</span>';
                } elseif ($row->status == '1a') {
                    return '<span class="badge bg-label-info">Panggilan Wawancara</span>';
                } elseif ($row->status == '2a') {
                    return '<span class="badge bg-label-danger">Tidak Hadir Wawancara</span>';
                } else {
                    return 'Status Tidak Dikenal';
                }
            })
            ->addColumn('lihat_cv', function ($row) use ($holding) {
                return '<a href="/pg-data-recruitment/pelamar-detail/' . $row->id . '/' . $holding . '" type="button" class="btn btn-sm btn-info"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Detail&nbsp;CV</a>';
            })

            ->rawColumns(['pelamar', 'no_wa', 'tanggal_wawancara', 'waktu_wawancara', 'waktu_wawancara', 'feedback', 'status', 'lihat_cv'])
            ->make(true);
    }
    function user_wait($id, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $user_wait =  RecruitmentUser::with([
            'AuthLogin' => function ($query) {
                $query->with([
                    'recruitmentCV' => function ($query) {
                        $query->orderBy('nama_lengkap');
                    }
                ]);
            }
        ])
            ->where('holding', $holdings->id)
            ->where('status', '2')
            ->where('recruitment_admin_id', $id)
            ->get();
        return DataTables::of($user_wait)

            ->addColumn('pelamar', function ($row) {
                return $row->AuthLogin->recruitmentCV->nama_lengkap ?? '-';
            })
            ->addColumn('no_wa', function ($row) {
                return $row->AuthLogin->nomor_whatsapp ?? '-';
            })

            ->addColumn('status', function ($row) {
                // dd($row->status);
                if ($row->status == '2') {
                    return '<span class="badge bg-label-secondary">Daftar Tunggu</span>';
                } else {
                    return 'Status Tidak Dikenal';
                }
            })
            ->addColumn('lihat_cv', function ($row) use ($holding) {
                return '<a href="/pg-data-recruitment/pelamar-detail/' . $row->id . '/' . $holding . '" type="button" class="btn btn-sm btn-info"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Detail&nbsp;CV</a>';
            })

            ->rawColumns(['pelamar', 'no_wa', 'status', 'lihat_cv'])
            ->make(true);
    }
    function user_reject($id, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $user_reject =  RecruitmentUser::with([
            'AuthLogin' => function ($query) {
                $query->with([
                    'recruitmentCV' => function ($query) {
                        $query->orderBy('nama_lengkap');
                    }
                ]);
            }
        ])
            ->where('holding', $holdings->id)
            ->where('status', '3')
            ->where('recruitment_admin_id', $id)
            ->get();
        return DataTables::of($user_reject)

            ->addColumn('pelamar', function ($row) {
                return $row->AuthLogin->recruitmentCV->nama_lengkap ?? '-';
            })
            ->addColumn('no_wa', function ($row) {
                return $row->AuthLogin->nomor_whatsapp ?? '-';
            })
            ->addColumn('alasan', function ($row) {
                return $row->alasan;
            })
            ->addColumn('status', function ($row) {
                // dd($row->status);
                if ($row->status == '3') {
                    return '<span class="badge bg-label-danger">Ditolak</span>';
                } else {
                    return 'Status Tidak Dikenal';
                }
            })
            ->addColumn('lihat_cv', function ($row) use ($holding) {
                return '<a href="/pg-data-recruitment/pelamar-detail/' . $row->id . '/' . $holding . '" type="button" class="btn btn-sm btn-info"><i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>Detail&nbsp;CV</a>';
            })

            ->rawColumns(['pelamar', 'no_wa', 'alasan', 'status', 'lihat_cv'])
            ->make(true);
    }
    function pelamar_detail($id, $holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $data_cv =  RecruitmentUser::with([
            'AuthLogin' => function ($query) use ($id) {
                $query->with([
                    'recruitmentCV' => function ($query) {
                        $query->orderBy('nama_lengkap')->with([
                            'provinsiKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kabupatenKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kecamatanKTP' => function ($query1) {
                                $query1->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'desaKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'provinsiNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kabupatenNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kecamatanNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'desaNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ]);
                    }
                ]);
            }
        ])->with([
            'Bagian' =>  function ($query) {
                $query->with([
                    'Divisi' => function ($query) {
                        $query->with([
                            'Departemen' => function ($query) {
                                $query->orderBy('nama_departemen', 'ASC');
                            }
                        ]);
                        $query->orderBy('nama_divisi', 'ASC');
                    }
                ]);
                $query->orderBy('nama_bagian', 'ASC');
            },
        ])
            ->where('id', $id)
            ->first();
        // dd($data_cv);
        $kesehatan = RecruitmentKesehatan::where('id_user', $data_cv->AuthLogin->id)->first();
        $kesehatan_pengobatan = RecruitmentKesehatanPengobatan::where('id_user', $data_cv->AuthLogin->id)->get();
        $kesehatan_rs = RecruitmentKesehatanRS::where('id_user', $data_cv->AuthLogin->id)->get();
        $kesehatan_kecelakaan = RecruitmentKesehatanKecelakaan::where('id_user', $data_cv->AuthLogin->id)->get();
        $pendidikan = RecruitmentPendidikan::where('id_user', $data_cv->AuthLogin->id)->orderBy('tanggal_keluar', 'DESC')->get();
        $pekerjaan = RecruitmentRiwayat::where('id_user', $data_cv->AuthLogin->id)->orderBy('tanggal_keluar', 'DESC')->get();
        $pekerjaan_count = RecruitmentRiwayat::where('id_user', $data_cv->AuthLogin->id)->orderBy('tanggal_keluar', 'DESC')->count();
        $keahlian_count = RecruitmentKeahlian::where('id_user', $data_cv->AuthLogin->id)->count();
        $keahlian = RecruitmentKeahlian::where('id_user', $data_cv->AuthLogin->id)->get();
        $site = Site::get();
        // dd($pekerjaan);
        // dd($pendidikan);
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.recruitment.user_detail', [
            'ti$pekerjaan_counttle' => 'Data Recruitment',
            'holding'   => $holdings,
        ], compact(
            'data_cv',
            'pendidikan',
            'menus',
            'pekerjaan',
            'pekerjaan_count',
            'keahlian_count',
            'keahlian',
            'kesehatan',
            'kesehatan_pengobatan',
            'kesehatan_rs',
            'kesehatan_kecelakaan',
            'site',
        ));
    }
    function pelamar_detail_pdf($id)
    {
        $data_cv =  RecruitmentUser::with([
            'AuthLogin' => function ($query) use ($id) {
                $query->with([
                    'recruitmentCV' => function ($query) {
                        $query->orderBy('nama_lengkap')->with([
                            'provinsiKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kabupatenKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kecamatanKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'desaKTP' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'provinsiNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kabupatenNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'kecamatanNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ])->with([
                            'desaNOW' => function ($query) {
                                $query->orderBy('id', 'ASC');
                            }
                        ]);
                    }
                ]);
            }
        ])->with([
            'Bagian' =>  function ($query) {
                $query->with([
                    'Divisi' => function ($query) {
                        $query->with([
                            'Departemen' => function ($query) {
                                $query->orderBy('nama_departemen', 'ASC');
                            }
                        ]);
                        $query->orderBy('nama_divisi', 'ASC');
                    }
                ]);
                $query->orderBy('nama_bagian', 'ASC');
            },
        ])
            ->where('id', $id)
            ->first();
        $kesehatan = RecruitmentKesehatan::where('id_user', $data_cv->AuthLogin->id)->first();
        $kesehatan_pengobatan = RecruitmentKesehatanPengobatan::where('id_user', $data_cv->AuthLogin->id)->get();
        $kesehatan_rs = RecruitmentKesehatanRS::where('id_user', $data_cv->AuthLogin->id)->get();
        $kesehatan_kecelakaan = RecruitmentKesehatanKecelakaan::where('id_user', $data_cv->AuthLogin->id)->get();
        $pendidikan = RecruitmentPendidikan::where('id_user', $data_cv->AuthLogin->id)->orderBy('tanggal_keluar', 'DESC')->get();
        $pekerjaan = RecruitmentRiwayat::where('id_user', $data_cv->AuthLogin->id)->orderBy('tanggal_keluar', 'DESC')->get();
        $pekerjaan_count = RecruitmentRiwayat::where('id_user', $data_cv->AuthLogin->id)->orderBy('tanggal_keluar', 'DESC')->count();
        $keahlian_count = RecruitmentKeahlian::where('id_user', $data_cv->AuthLogin->id)->count();
        $keahlian = RecruitmentKeahlian::where('id_user', $data_cv->AuthLogin->id)->get();

        // dd($pekerjaan_count);

        $pdf = Pdf::setOption([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ])->loadView('admin.recruitment-users.recruitment.user_detail_pdf', [
            'ti$pekerjaan_counttle' => 'Data Recruitment',
        ], compact(
            'data_cv',
            'pendidikan',
            'pekerjaan',
            'pekerjaan_count',
            'keahlian_count',
            'keahlian',
            'kesehatan',
            'kesehatan_pengobatan',
            'kesehatan_rs',
            'kesehatan_kecelakaan'
        ));
        // $pdf->setWarnings(true);
        return $pdf->stream($data_cv->Authlogin->recruitmentCV->nama_lengkap . ' CV.pdf');
    }
    function pelamar_nilai_pdf($id)
    {
        $table = RecruitmentUser::with([
            'Cv' => function ($query) {
                $query;
            }
        ])->with([
            'interviewUser' => function ($query) {
                $query;
            }
        ])->with([
            'DataInterview' => function ($query) {
                $query;
            }
        ])
            ->with([
                'ujianEsaiJawab' => function ($query) {
                    $query->orderBy('recruitment_user_id')->with([
                        'ujian' => function ($query) {
                            $query->with([
                                'pembobotan' => function ($query) {
                                    $query;
                                }
                            ]);
                        }
                    ]);
                }
            ])->with([
                'waktuujian' => function ($query) {
                    $query->orderBy('recruitment_user_id')->with([
                        'ujian' => function ($query) {
                            $query->with([
                                'pembobotan' => function ($query) {
                                    $query;
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->with([
                'recruitmentAdmin' => function ($query) {
                    $query;
                }
            ])
            ->with([
                'Jabatan' => function ($query) {
                    $query->with([
                        'Bagian' =>  function ($query) {
                            $query->with([
                                'Divisi' => function ($query) {
                                    $query->with([
                                        'Departemen' => function ($query) {
                                            $query->orderBy('nama_departemen', 'ASC');
                                        }
                                    ]);
                                    $query->orderBy('nama_divisi', 'ASC');
                                },
                            ]);
                        },
                    ])->with([
                        'LevelJabatan' => function ($query) {
                            $query;
                        },
                    ]);
                },

            ])
            ->where('id', $id)
            ->first();
        // dd($table);
        $esai_total = $table ? $table->ujianEsaiJawab->sum('nilai') : 0;
        $pg_total = $table ? $table->waktuujian->sum('nilai') : 0;
        $get_jabatan = $table ? $table->Jabatan->LevelJabatan->level_jabatan : 0;
        if ($get_jabatan == 0) {
            $esai_count = Ujian::where('esai', 1)->where('nol', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('nol', '1')->count();
        } elseif ($get_jabatan == 1) {
            $esai_count = Ujian::where('esai', 1)->where('satu', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('satu', '1')->count();
        } elseif ($get_jabatan == 2) {
            $esai_count = Ujian::where('esai', 1)->where('dua', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('dua', '1')->count();
        } elseif ($get_jabatan == 3) {
            $esai_count = Ujian::where('esai', 1)->where('tiga', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('tiga', '1')->count();
        } elseif ($get_jabatan == 4) {
            $esai_count = Ujian::where('esai', 1)->where('empat', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('empat', '1')->count();
        } elseif ($get_jabatan == 5) {
            $esai_count = Ujian::where('esai', 1)->where('lima', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('lima', '1')->count();
        } elseif ($get_jabatan == 6) {
            $esai_count = Ujian::where('esai', 1)->where('enam', '1')->count();
            $pg_count = Ujian::where('esai', 0)->where('enam', '1')->count();
        }
        $interview_user = $table ? $table->interviewUser->sum('nilai') : 0;
        $interview_admin = InterviewUser::where('recruitment_user_id', $id)->count() ?? 0;
        $hitung_interview = $interview_user / $interview_admin;
        $hasil_interview = round($hitung_interview * 10, 2);
        $get_bobot = Pembobotan::first();
        try {
            $koefisien_esai = ($esai_total / $esai_count) * ($get_bobot->esai / 100);
            $koefisien_pg = ($pg_total / $pg_count) * ($get_bobot->pilihan_ganda / 100);
            $koefisien_interview = ($interview_user / $interview_admin * 10) * ($get_bobot->interview / 100);
        } catch (DivisionByZeroError $e) {
            $koefisien_esai = 0;
            $koefisien_pg = 0;
            $koefisien_interview = 0;
        }


        $koefisien_total = round($koefisien_esai + $koefisien_pg + $koefisien_interview, 2);
        $pembobotan = Pembobotan::first();

        $ctn = RecruitmentInterview::where('recruitment_user_id', $id)->first();
        // dd($ctn);
        $pdf = Pdf::loadView('admin.recruitment-users.recruitment.user_nilai_pdf', [
            '$pekerjaan_counttle' => 'Data Recruitment',
        ], compact(
            'pembobotan',
            'esai_total',
            'pg_total',
            'hasil_interview',
            'koefisien_pg',
            'koefisien_esai',
            'koefisien_interview',
            'koefisien_total',
            'ctn',
            'table',
        ));
        return $pdf->stream($table->Cv->nama_lengkap . ' NILAI.pdf');
    }
    public function pelamar_detail_ubah(Request $request, $holding)
    {
        // dd($request->all());
        $holdings = Holding::where('holding_code', $holding)->first();
        $recruitment_admin_id = RecruitmentUser::where('id', $request->recruitment_user_id)->first();
        $tanggal_wawancara = Carbon::parse($request->tanggal_wawancara);
        $hari = $tanggal_wawancara->translatedFormat('l, j F Y');

        $site = Site::where('id', $request->tempat_wawancara)->first();
        // dd($request->nomor_whatsapp);
        // dd($recruitment_admin_id);
        if ($request->status == '1') {
            // Rule Untuk form wawancara
            $tanggal_wawancara = 'required';
            $tempat_wawancara = 'required';
            $waktu_wawancara = 'required';
            $link_wawancara = 'required';
        } else {
            $tanggal_wawancara = 'nullable';
            $waktu_wawancara = 'nullable';
            $tempat_wawancara = 'nullable';
            $link_wawancara = 'nullable';
        }
        if ($request->status == '1' && $request->online == '1') {
            $tempat_wawancara = 'required';
            $link_wawancara = 'nullable';
            if (!empty($request->tempat_wawancara)) {
                $map = $site->gm_link;
            }
        } elseif ($request->status == '1' && $request->online == '2') {
            $tempat_wawancara = 'nullable';
            $link_wawancara = 'required';
            $map = '-';
        }
        $rules =
            [
                'status'             => 'required',
                'tanggal_wawancara'  => $tanggal_wawancara,
                'tempat_wawancara'   => $tempat_wawancara,
                'link_wawancara'     => $link_wawancara,
                'waktu_wawancara'    => $waktu_wawancara
            ];
        $customessages =
            [
                'required'             => ':attribute tidak boleh kosong!',
            ];
        $validasi = Validator::make(
            $request->all(),
            $rules,
            $customessages
        );


        if ($validasi->fails()) {
            $error = $validasi->errors()->first();
            Alert::error('Gagal', $error);
            return redirect()->back();
        }
        if ($request->status == '1') {
            if ($request->online == '1') {
                $tempat_wawancara = 'Lokasi : ' . $site->site_name;
            } elseif ($request->online == '2') {
                $tempat_wawancara = 'Link Zoom: ' . $request->link_wawancara;
            }
        }
        // dd($tempat_wawancara);
        if ($request->status == '1') {
            // mencari nama PT

            // Lolos administrasi
            $path = "/qontak/chat/v1/broadcasts/whatsapp/direct";
            $payload = [
                "to_number" => $this->formatPhoneIndonesia($request->nomor_whatsapp),
                "to_name" => $request->nama,
                "message_template_id" => "c51f9820-add1-4ca9-8c5c-2dbea15f6063",
                "channel_integration_id" => "2cde26dd-8c36-4b9e-97d1-4b64634fd72f",
                "language" => ["code" => "id"],
                "parameters" => [
                    "body" => [
                        [
                            "key" => "1",
                            "value" => "nama",
                            "value_text" => "$request->nama"
                        ],
                        [
                            "key" => "2",
                            "value" => "holding",
                            "value_text" => "$holdings->holding_name"
                        ],
                        [
                            "key" => "3",
                            "value" => "date",
                            "value_text" => "$hari"
                        ],
                        [
                            "key" => "4",
                            "value" => "time",
                            "value_text" => "$request->waktu_wawancara"
                        ],
                        [
                            "key" => "5",
                            "value" => "location",
                            "value_text" => "$tempat_wawancara"
                        ],
                        [
                            "key" => "6",
                            "value" => "link_address",
                            "value_text" => "$map"
                        ],
                        [
                            "key" => "7",
                            "value" => "id_user",
                            "value_text" => "$request->recruitment_user_id"
                        ],
                    ]

                ]
            ];
            $response = $this->mekari->request('POST', $path, $payload);

            //             Carbon::setLocale('id');
            //             //kirim pesan ke whatsapp
            //             $curl = curl_init();
            //             curl_setopt_array($curl, array(
            //                 CURLOPT_URL => 'https://api.fonnte.com/send',
            //                 CURLOPT_RETURNTRANSFER => true,
            //                 CURLOPT_ENCODING => '',
            //                 CURLOPT_MAXREDIRS => 10,
            //                 CURLOPT_TIMEOUT => 0,
            //                 CURLOPT_FOLLOWLOCATION => true,
            //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //                 CURLOPT_CUSTOMREQUEST => 'POST',
            //                 CURLOPT_POSTFIELDS => array(
            //                     'target' => $request->nomor_whatsapp,
            //                     'message' =>
            //                     "
            // Halo $request->nama,
            // Terima kasih telah melamar di $holdings->holding_name. Setelah proses seleksi administrasi, kami mengundang Anda untuk mengikuti wawancara tahap pertama pada:

            // 📅 Hari/Tanggal : $hari
            // 🕒 Pukul : $request->waktu_wawancara WIB
            // 📍 $tempat_wawancara, $map

            // Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):

            // https://karir.sumberpangan.store/cpanel/recruitment_detail/$request->recruitment_user_id
            //                         ",

            //                     'countryCode' => '62', //optional
            //                 ),
            //                 CURLOPT_HTTPHEADER => array(
            //                     'Authorization: Xp5bwZZN22VPhojYcPEB' //change TOKEN to your actual token
            //                 ),
            //             ));
            //             $response = curl_exec($curl);
            //             if (curl_errno($curl)) {
            //                 $error_msg = curl_error($curl);
            //             }
            //             curl_close($curl);

            //             if (isset($error_msg)) {
            //                 echo $error_msg;
            //             }
            // end kirim pesan ke whatsapp

            $email      = $request->email;
            $nama       = $request->nama;
            $e_holding  = $holdings->holding_name;
            $pukul      = $request->waktu_wawancara;
            $maps       = $map ?? '-';
            $user_id    = $request->recruitment_user_id;
            Cache::put("Wawancara:$email", true, now()->addHours(2));
            Mail::to($email)->send(new DiterimaWawancaraMail($nama, $pukul, $tempat_wawancara, $maps, $e_holding, $hari, $user_id));
        }

        // $holding = request()->segment(count(request()->segments()));
        // dd($validatedData);
        RecruitmentUser::where('id', $request->recruitment_user_id)->update(
            [
                'status'             => $request->status,
                'status_user'        => $request->status,
                'tanggal_wawancara'  => $request->tanggal_wawancara,
                'tanggal_konfirmasi' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'tempat_wawancara'   => $tempat_wawancara,
                'waktu_wawancara'    => $request->waktu_wawancara,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
        RecruitmentUserRecord::insert(
            [
                'id'                    => Uuid::uuid4(),
                'recruitment_user_id'   => $request->recruitment_user_id,
                'status'                => $request->status,
                'status_user'           => $request->status,
                'created_at'            => date('Y-m-d H:i:s'),
            ]
        );

        if ($request->status == 1) {
            $status_log = 'Lolos';
        } elseif ($request->status == 2) {
            $status_log = 'Daftar Tunggu';
        } else {
            $status_log = 'Tidak Lolos';
        }
        $cv = RecruitmentCV::where('users_career_id', $recruitment_admin_id->users_career_id)->first();
        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        $divisi = Divisi::where('id', $recruitment_admin_id->nama_divisi)->first();
        $jabatan = Jabatan::where('id', $recruitment_admin_id->nama_jabatan)->first();
        // dd($divisi);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $request->recruitment_user_id,
            'holding_id' => $holdings->id,
            'holding_category' => $holdings->holding_category,
            'table' => 'recruitment_user',
            'activity' => 'update',
            'description' => 'mengubah status pelamar ' . $cv->nama_lengkap . ' pada ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan . ' menjadi ' . $status_log,
            'keterangan' => 'Success',
        ]);
        // Merekam aktivitas pengguna
        return redirect('/pg-data-recruitment/data-list-pelamar/' . $recruitment_admin_id->recruitment_admin_id . '/' . $holding . '')->with('success', 'data berhasil ditambahkan');
    }

    function pg_data_interview($holding, Request $request)
    {
        // Hapus pelamar kadaluarsa
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $get_recruitment_user_id = RecruitmentUser::where('status', '1')
            ->whereDate('tanggal_wawancara', '<', date('Y-m-d'))
            ->get();
        // dd($get_recruitment_user_id);
        foreach ($get_recruitment_user_id as $ii) {
            RecruitmentUser::where('id', $ii->id)->update([
                'status' => '2a',
            ]);
            RecruitmentUserRecord::insert([
                'id' => Uuid::uuid4(),
                'recruitment_user_id' => $ii->id,
                'status' => '2a'
            ]);
        }
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        $departemen = Departemen::where('holding', $holdings->id)->orderBy('nama_departemen', 'ASC')->get();
        // end Hapus pelamar kadaluar
        $holding = request()->segment(count(request()->segments()));
        return view('admin.recruitment-users.interview.data_interview', [
            // return view('karyawan.index', [
            'menus'        => $menus,
            'title'        => 'Data Interview',
            'holding'      => $holdings,
            'departemen'   => $departemen,
        ]);
    }
    function dt_data_interview($holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $table = RecruitmentInterview::with([
            'recruitmentUser' => function ($query) use ($holdings) {
                $query->where('holding', $holdings->id)->whereDate('tanggal_wawancara', date('Y-m-d'))->with([
                    'Bagian' =>  function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                                $query->orderBy('nama_divisi', 'ASC');
                            }
                        ]);
                        $query->orderBy('nama_bagian', 'ASC');
                    }
                ])->with([
                    'Cv' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])
            ->whereHas('recruitmentUser', function ($query) {
                $query->whereDate('tanggal_wawancara', date('Y-m-d'));
            })
            ->whereHas('recruitmentUser', function ($query) use ($holdings) {
                $query->where('holding', $holdings->id);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('tanggal_wawancara', function ($row) {
                    return Carbon::parse($row->recruitmentUser->tanggal_wawancara)->format('d-m-Y');
                    // return '-';
                })

                ->addColumn('waktu_wawancara', function ($row) {
                    return $row->recruitmentUser->waktu_wawancara;
                })
                ->addColumn('presensi', function ($row) {
                    if ($row->recruitmentUser->status == '1a') {
                        $btn = '<span class="badge bg-label-success">Hadir</span>';
                    } elseif ($row->recruitmentUser->status == '2a') {
                        $btn = '<span class="badge bg-label-danger">Tidak Hadir</span>';
                    } else {
                        $btn = '<button id="btn_presensi"
                                data-id="' . $row->id . '"
                                type="button" class="btn btn-sm btn-info ">
                                <i class="tf-icons mdi mdi-account-search"> </i>
                                 &nbsp;Presensi
                            </button>';
                    }
                    return $btn;
                })
                ->addColumn('terlambat', function ($row) {
                    if ($row->terlambat == '1') {
                        $btn = '<span class="badge bg-label-success">Tepat Waktu</span>';
                    } elseif ($row->terlambat == '2') {
                        $btn = '<span class="badge bg-label-warning">Terlambat</span>';
                    } else {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->addColumn('ujian', function ($row) {
                    $holding = request()->segment(count(request()->segments()));
                    return '<a href="' . url("/pg-data-interview/dt/data-data_ujian_user/$row->recruitment_user_id/$holding") . '" type="button" class="btn btn-info"><small>Lihat</small></a>';
                })
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->recruitmentUser->Cv->nama_lengkap;
                })
                ->addColumn('nama_bagian', function ($row) {
                    return $row->recruitmentUser->Bagian->nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->recruitmentUser->Bagian->Divisi->nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->recruitmentUser->Bagian->Divisi->Departemen->nama_departemen;
                })
                ->rawColumns([
                    'tanggal_wawancara',
                    'waktu_wawancara',
                    'presensi',
                    'terlambat',
                    'ujian',
                    'nama_lengkap',
                    'nama_bagian',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_departemen'
                ])
                ->make(true);
        }
    }
    function dt_data_interview1(Request $request, $holding)
    {
        // dd($request->all());
        $holdings = Holding::where('holding_code', $holding)->first();
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $query_get = RecruitmentInterview::with([
            'recruitmentUser' => function ($query) use ($holdings) {
                $query->where('holding', $holdings->id)->where('status', '1a')->with([
                    'Jabatan' => function ($query) {
                        $query->with([
                            'Bagian' => function ($query) {
                                $query->with([
                                    'Divisi' => function ($query) {
                                        $query->with([
                                            'Departemen' => function ($query) {
                                                $query->orderBy('nama_departemen', 'ASC');
                                            }
                                        ]);
                                    }
                                ]);
                            }
                        ]);
                        $query->orderBy('nama_jabatan', 'ASC');
                    }
                ])->with([
                    'Cv' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])
            ->whereHas('recruitmentUser', function ($query) {
                $query->where('status', '1a');
            })
            ->whereHas('recruitmentUser', function ($query) use ($holdings) {
                $query->where('holding', $holdings->id);
            })
            ->whereHas('recruitmentUser', function ($query) use ($now, $now1) {
                $query->whereBetween('tanggal_wawancara', [$now, $now1]);
            })
            ->orderBy('created_at', 'DESC');

        if (!empty($request->departemen_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->departemen_filter ?? []);
            });
        }

        if (!empty($request->divisi_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->divisi_filter ?? []);
            });
        }

        if (!empty($request->bagian_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->bagian_filter ?? []);
            });
        }

        if (!empty($request->jabatan_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->jabatan_filter ?? []);
            });
        }
        if (!empty($request->jumlah_filter)) {
            $query_get->limit($request->jumlah_filter);
        }

        $table = $query_get->get();
        // dd($table);
        if (request()->ajax()) {

            return DataTables::of($table)
                ->addColumn('tanggal_wawancara', function ($row) {
                    return $row->recruitmentUser->tanggal_wawancara;
                })
                ->addColumn('waktu_wawancara', function ($row) {
                    return $row->recruitmentUser->waktu_wawancara;
                })
                ->addColumn('presensi', function ($row) {
                    if ($row->recruitmentUser->status == '1a') {
                        $btn = '<span class="badge bg-label-success">Hadir</span>';
                    } elseif ($row->recruitmentUser->status == '2a') {
                        $btn = '<span class="badge bg-label-danger">Tidak Hadir</span>';
                    } else {
                        $btn = '<button id="btn_presensi"
                                data-id="' . $row->id . '"
                                type="button" class="btn btn-sm btn-info ">
                                <i class="tf-icons mdi mdi-account-search"> </i>
                                 &nbsp;Presensi
                            </button>';
                    }
                    return $btn;
                })
                ->addColumn('terlambat', function ($row) {
                    if ($row->terlambat == '1') {
                        $btn = '<span class="badge bg-label-success">Tepat Waktu</span>';
                    } elseif ($row->terlambat == '2') {
                        $btn = '<span class="badge bg-label-warning">Terlambat</span>';
                    } else {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->recruitmentUser->Cv->nama_lengkap;
                })
                ->addColumn('ujian', function ($row) {
                    $holding = request()->segment(count(request()->segments()));
                    return '<a href="' . url("/pg-data-interview/dt/data-data_ujian_user/$row->recruitment_user_id/$holding") . '" type="button" class="btn btn-info"><small>Lihat</small></a>';
                })
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->recruitmentUser->Jabatan->nama_jabatan;
                })
                ->addColumn('nama_bagian', function ($row) {
                    return $row->recruitmentUser->Jabatan->Bagian->nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->recruitmentUser->Jabatan->Bagian->Divisi->nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->recruitmentUser->Jabatan->Bagian->Divisi->Departemen->nama_departemen;
                })
                ->rawColumns([
                    'tanggal_wawancara',
                    'waktu_wawancara',
                    'presensi',
                    'terlambat',
                    'nama_lengkap',
                    'ujian',
                    'nama_jabatan',
                    'nama_bagian',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_departemen'
                ])
                ->make(true);
        }
    }
    function dt_data_interview2(Request $request, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $query_get = RecruitmentInterview::with([
            'recruitmentUser' => function ($query) use ($holdings) {
                $query->where('holding', $holdings->id)->where('status', '2a')->with([
                    'Jabatan' => function ($query) {
                        $query->with([
                            'Bagian' => function ($query) {
                                $query->with([
                                    'Divisi' => function ($query) {
                                        $query->with([
                                            'Departemen' => function ($query) {
                                                $query->orderBy('nama_departemen', 'ASC');
                                            }
                                        ]);
                                    }
                                ]);
                            }
                        ]);
                        $query->orderBy('nama_jabatan', 'ASC');
                    }
                ])->with([
                    'Cv' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])
            ->whereHas('recruitmentUser', function ($query) use ($holdings) {
                $query->where('holding', $holdings->id);
            })
            ->whereHas('recruitmentUser', function ($query) {
                $query->where('status', '2a');
            })
            ->whereHas('recruitmentUser', function ($query) use ($now, $now1) {
                $query->whereBetween('tanggal_wawancara', [$now, $now1]);
            })
            ->orderBy('created_at', 'DESC');
        if (!empty($request->departemen_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->departemen_filter ?? []);
            });
        }

        if (!empty($request->divisi_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->divisi_filter ?? []);
            });
        }

        if (!empty($request->bagian_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->bagian_filter ?? []);
            });
        }

        if (!empty($request->jabatan_filter)) {
            $query_get->whereHas('recruitmentUser', function ($query) use ($request) {
                $query->whereIn('nama_dept', (array)$request->jabatan_filter ?? []);
            });
        }
        if (!empty($request->jumlah_filter)) {
            $query_get->limit($request->jumlah_filter);
        }

        $table = $query_get->get();

        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('tanggal_wawancara', function ($row) {
                    return Carbon::parse($row->recruitmentUser->tanggal_wawancara)->format('d-m-Y');
                })
                ->addColumn('presensi', function ($row) {
                    if ($row->recruitmentUser->status == '1a') {
                        $btn = '<span class="badge bg-label-success">Hadir</span>';
                    } elseif ($row->recruitmentUser->status == '2a') {
                        $btn = '<span class="badge bg-label-danger">Tidak Hadir</span>';
                    } else {
                        $btn = '<button id="btn_presensi"
                                data-id="' . $row->id . '"
                                type="button" class="btn btn-sm btn-info ">
                                <i class="tf-icons mdi mdi-account-search"> </i>
                                 &nbsp;Presensi
                            </button>';
                    }
                    return $btn;
                })

                ->addColumn('nama_lengkap', function ($row) {
                    return $row->recruitmentUser->Cv->nama_lengkap;
                })
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->recruitmentUser->Jabatan->nama_jabatan;
                })
                ->addColumn('nama_bagian', function ($row) {
                    return $row->recruitmentUser->Jabatan->Bagian->nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->recruitmentUser->Jabatan->Bagian->Divisi->nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->recruitmentUser->Jabatan->Bagian->Divisi->Departemen->nama_departemen;
                })
                ->rawColumns([
                    'tanggal_wawancara',
                    'waktu_wawancara',
                    'presensi',
                    'terlambat',
                    'nama_lengkap',
                    'ujian',
                    'nama_jabatan',
                    'nama_bagian',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_departemen'
                ])
                ->make(true);
        }
    }
    function dt_data_interview3($holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $table = RecruitmentUser::with([

            'Bagian' =>  function ($query) {
                $query->with([
                    'Divisi' => function ($query) {
                        $query->with([
                            'Departemen' => function ($query) {
                                $query->orderBy('nama_departemen', 'ASC');
                            }
                        ]);
                        $query->orderBy('nama_divisi', 'ASC');
                    }
                ]);
                $query->orderBy('nama_bagian', 'ASC');
            }
        ])->with([
            'Cv' => function ($query) {
                $query;
            }
        ])
            ->where('holding', $holdings->id)
            ->whereDate('tanggal_wawancara', '>', date('Y-m-d'))
            ->where('status', '1')
            ->orderBy('created_at', 'DESC')
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('tanggal_wawancara', function ($row) {
                    return $row->tanggal_wawancara;
                })
                ->addColumn('presensi', function ($row) {
                    if ($row->status == '1') {
                        $btn = '<span class="badge bg-label-info">Belum Jadwalnya</span>';
                    }
                    return $btn;
                })
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->Cv->nama_lengkap;
                })
                ->addColumn('nama_bagian', function ($row) {
                    return $row->Bagian->nama_bagian;
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->Bagian->Divisi->nama_divisi;
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->Bagian->Divisi->Departemen->nama_departemen;
                })
                ->rawColumns(['tanggal_wawancara', 'presensi', 'nama_lengkap', 'nama_bagian', 'nama_departemen', 'nama_divisi', 'nama_departemen'])
                ->make(true);
        }
    }
    public function data_ujian_user($id, $holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $user_recruitment = RecruitmentUser::where('id', $id)->with([
            'Cv' => function ($query) {
                $query;
            }
        ])->first();
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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

        $user_interview = RecruitmentInterview::where('recruitment_user_id', $id)->first();
        return view('admin.recruitment-users.interview.data_ujian_user', [
            // return view('karyawan.index', [
            'holding'   => $holdings,
            'menus' => $menus,
            'recruitment_user_id' => $id,
            'user_recruitment'   => $user_recruitment,
            'user_interview'   => $user_interview,
        ]);
    }

    public function presensi_recruitment_update(Request $request)
    {

        // dd($request->all());
        try {
            $konfirmasi = RecruitmentInterview::where('id', $request->id)->first();
            // dd($konfirmasi);
            $konfirmasi->updated_at = date('Y-m-d H:i:s');
            $konfirmasi->save();
            RecruitmentUser::where('id', $konfirmasi->recruitment_user_id)->update(
                [
                    'status'                => $request->status,
                    'updated_at'            => date('Y-m-d H:i:s'),
                ]
            );

            RecruitmentUserRecord::insert(
                [
                    'id'                    => Uuid::uuid4(),
                    'recruitment_user_id'   => $konfirmasi->recruitment_user_id,
                    'status'                => $request->status,
                    'created_at'            => date('Y-m-d H:i:s'),
                ]
            );
            if ($request->status == '1a') {
                RecruitmentInterview::where('id', $request->id)->update(
                    [
                        'terlambat'             => $request->terlambat,
                        'updated_at'            => date('Y-m-d H:i:s'),
                    ]
                );
                $get_interview_user = InterviewUser::where('recruitment_user_id', $konfirmasi->recruitment_user_id)->first();
                if ($get_interview_user == null) {
                    $interview_admin  = InterviewAdmin::get();
                    foreach ($interview_admin as $ia) {
                        InterviewUser::create([
                            'parameter' => $ia->parameter,
                            'deskripsi' => $ia->deskripsi,
                            'recruitment_user_id' => $konfirmasi->recruitment_user_id,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            if ($request->terlambat == 1) {
                $terlambat = 'tepat waktu';
            } else {
                $terlambat = 'terlambat';
            }
            $get_holding = RecruitmentUser::where('id', $konfirmasi->recruitment_user_id)->first();
            $holdings = Holding::where('id', $get_holding->holding)->first();
            $cv = RecruitmentCV::where('users_career_id', $get_holding->users_career_id)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            // dd($get_holding);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holdings->id,
                'holding_category' => $holdings->holding_category,
                'table' => 'recruitment_interview',
                'activity' => 'update',
                'description' => 'Mengabsen pelamar ' . $cv->nama_lengkap . ' ' . $terlambat,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function ranking_update_status(Request $request)
    {
        // dd($request->all());
        try {
            $get_recruitment_admin = RecruitmentUser::with([
                'recruitmentAdmin' => function ($query) {
                    $query->with([
                        'Jabatan' => function ($query) {
                            $query->with([
                                'Bagian' =>  function ($query) {
                                    $query->with([
                                        'Divisi' => function ($query) {
                                            $query->with([
                                                'Departemen' => function ($query) {
                                                    $query->orderBy('nama_departemen', 'ASC');
                                                }
                                            ]);
                                            $query->orderBy('nama_divisi', 'ASC');
                                        },
                                    ]);
                                },
                            ]);
                        },
                    ]);
                },
            ])
                ->where('id', $request->id)
                ->first();
            // dd($request->all());
            $holding = $request->holding;
            $holdings = Holding::where('holding_code', $holding)->first();

            $get_wa = RecruitmentUser::with([
                'AuthLogin' => function ($query) {
                    $query;
                }
            ])->with([
                'Cv' => function ($query) {
                    $query;
                }
            ])->where('id', $request->id)->first();
            if ($request->status == '1b') {
                $tanggal_wawancara = Carbon::parse($request->tanggal_wawancara);
                $hari = $tanggal_wawancara->translatedFormat('l, j F Y');
                if ($request->online == 1) {
                    $link_wawancara = 'nullable';
                    $tempat_wawancara = 'required';
                    $site = Site::where('id', $request->tempat_wawancara)->first();
                    if ($site == null) {
                        return response()->json([
                            'code' => 500,
                            'error' => 'Site Wawancara Kosong',
                        ]);
                    }
                    $tempat = $site->site_name;
                    $map = 'Lokasi : ' . $site->gm_link;
                } elseif ($request->online == 2) {
                    $link_wawancara = 'required';
                    $tempat_wawancara = 'nullable';
                    $tempat = 'Link Zoom : ' . $request->link_wawancara;
                    $map = '-';
                }
                $validator = Validator::make(
                    $request->all(),
                    [
                        'tanggal_wawancara' => 'required',
                        'link_wawancara' => $link_wawancara,
                        'tempat_wawancara' => $tempat_wawancara,
                        'waktu_wawancara' => 'required',
                    ],
                    [
                        'required' => ':attribute tidak boleh kosong'
                    ]
                );
                if ($validator->fails()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ]);
                }
                // dd($request->all());
                RecruitmentUser::where('id', $request->id)->update(

                    [
                        'tanggal_wawancara_manager'     => $request->tanggal_wawancara,
                        'tempat_wawancara'              => $tempat,
                        'waktu_wawancara'               => $request->waktu_wawancara,
                        'status_lanjutan'               => $request->status,
                        'tanggal_konfirmasi_manager'    => date('Y-m-d H:i:s', strtotime('+2 days')),
                        'updated_at'                    => date('Y-m-d H:i:s'),
                    ]
                );
                RecruitmentUserRecord::insert(
                    [
                        'id'                        => Uuid::uuid4(),
                        'recruitment_user_id'       => $request->id,
                        'status'                    => $request->status,
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                $get_holding = RecruitmentUser::where('id', $request->id)->first();
                $holdings = Holding::where('id', $get_holding->holding)->first();
                $cv = RecruitmentCV::where('users_career_id', $get_holding->users_career_id)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $get_holding->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $get_holding->nama_jabatan)->first();
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'mengubah status pelamar menjadi lolos interview manager ' . $cv->nama_lengkap . ' pada lowongan ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);



                //                 $curl = curl_init();
                //                 curl_setopt_array($curl, array(
                //                     CURLOPT_URL => 'https://api.fonnte.com/send',
                //                     CURLOPT_RETURNTRANSFER => true,
                //                     CURLOPT_ENCODING => '',
                //                     CURLOPT_MAXREDIRS => 10,
                //                     CURLOPT_TIMEOUT => 0,
                //                     CURLOPT_FOLLOWLOCATION => true,
                //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                     CURLOPT_CUSTOMREQUEST => 'POST',
                //                     CURLOPT_POSTFIELDS => array(
                //                         'target' => $get_wa->Authlogin->nomor_whatsapp,
                //                         'message' =>
                //                         "Halo " . $get_wa->Cv->nama_lengkap . " ,

                // Selamat, Anda lolos tahap wawancara awal. Kami mengundang Anda untuk melanjutkan ke wawancara bersama Manager pada:
                // 📅 Hari/Tanggal : $hari
                // 🕒 Pukul : $request->waktu_wawancara WIB
                // 📍 $tempat, $map

                // Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):
                // https://karir.sumberpangan.store/cpanel/recruitment_detail/$request->id

                // ",
                //                         'countryCode' => '62', //optional
                //                     ),
                //                     CURLOPT_HTTPHEADER => array(
                //                         'Authorization: Xp5bwZZN22VPhojYcPEB' //change TOKEN to your actual token
                //                     ),
                //                 ));

                //                 $response = curl_exec($curl);
                //                 if (curl_errno($curl)) {
                //                     $error_msg = curl_error($curl);
                //                 }
                //                 curl_close($curl);

                //                 if (isset($error_msg)) {
                //                     echo $error_msg;
                //                 }

                // wawancara manager
                $path = "/qontak/chat/v1/broadcasts/whatsapp/direct";
                $payload = [
                    "to_number" => $this->formatPhoneIndonesia($get_wa->Authlogin->nomor_whatsapp),
                    "to_name" => $get_wa->Cv->nama_lengkap,
                    "message_template_id" => "666cfa79-7fbb-43b1-93f1-6cf3b5690eed",
                    "channel_integration_id" => "2cde26dd-8c36-4b9e-97d1-4b64634fd72f",
                    "language" => ["code" => "id"],
                    "parameters" => [
                        "body" => [
                            [
                                "key" => "1",
                                "value" => "nama",
                                "value_text" => $get_wa->Cv->nama_lengkap
                            ],
                            [
                                "key" => "2",
                                "value" => "hari",
                                "value_text" => "$hari"
                            ],
                            [
                                "key" => "3",
                                "value" => "waktu_wawancara",
                                "value_text" => "$request->waktu_wawancara"
                            ],
                            [
                                "key" => "4",
                                "value" => "tempat",
                                "value_text" => "$tempat"
                            ],
                            [
                                "key" => "5",
                                "value" => "map",
                                "value_text" => "$map"
                            ],
                            [
                                "key" => "6",
                                "value" => "id_user",
                                "value_text" => "$request->id"
                            ],
                        ]

                    ]
                ];
                $response = $this->mekari->request('POST', $path, $payload);

                // Kirim email

                $email      = $get_wa->Authlogin->email;
                $nama       = $get_wa->Cv->nama_lengkap;
                $pukul      = $request->waktu_wawancara;
                $maps       = $map ?? '-';
                $user_id    = $request->id;
                Cache::put("Manager:$email", true, now()->addHours(2));
                Mail::to($email)->send(new DiterimaManagerMail($nama, $hari, $pukul, $tempat, $maps, $user_id));
            } elseif ($request->status == '2b') {
                // dd($request->all);
                $site_krj = Site::where('id', $request->tempat_bekerja)->first();
                if ($site_krj == null) {
                    return response()->json([
                        'code' => 500,
                        'error' => 'Site Wawancara Kosong',
                    ]);
                }
                $validator = Validator::make(
                    $request->all(),
                    [
                        'tempat_bekerja' => 'required',
                        'waktu_bekerja' => 'required',
                        'tanggal_diterima' => 'required',
                    ],
                    [
                        'required' => ':attribute tidak boleh kosong'
                    ]
                );
                if ($validator->fails()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ]);
                }
                RecruitmentUser::where('id', $request->id)->update(

                    [
                        'status_lanjutan'       => $request->status,
                        'tempat_bekerja'        => $request->tempat_bekerja,
                        'tanggal_diterima'      => $request->tanggal_diterima,
                        'gaji'                  => $request->gaji,
                        'notes'                 => $request->notes_langsung,
                        'konfirmasi_diterima'   => date('Y-m-d H:i:s', strtotime('+2 days')),
                        'updated_at'            => date('Y-m-d H:i:s'),
                    ]
                );
                RecruitmentUserRecord::insert(
                    [
                        'id'                        => Uuid::uuid4(),
                        'recruitment_user_id'       => $request->id,
                        'status'                    => $request->status,
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                $tanggal_diterima = Carbon::parse($request->tanggal_diterima);
                $hari_diterima = $tanggal_diterima->translatedFormat('l, j F Y');
                $get_wa = RecruitmentUser::with([
                    'AuthLogin' => function ($query) {
                        $query;
                    }
                ])->where('id', $request->id)->first();
                // dd($get_wa);
                $get_holding = RecruitmentUser::where('id', $request->id)->first();
                $holdings = Holding::where('id', $get_holding->holding)->first();
                $cv = RecruitmentCV::where('users_career_id', $get_holding->users_career_id)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $get_holding->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $get_holding->nama_jabatan)->first();

                //                 $curl = curl_init();
                //                 curl_setopt_array($curl, array(
                //                     CURLOPT_URL => 'https://api.fonnte.com/send',
                //                     CURLOPT_RETURNTRANSFER => true,
                //                     CURLOPT_ENCODING => '',
                //                     CURLOPT_MAXREDIRS => 10,
                //                     CURLOPT_TIMEOUT => 0,
                //                     CURLOPT_FOLLOWLOCATION => true,
                //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                     CURLOPT_CUSTOMREQUEST => 'POST',
                //                     CURLOPT_POSTFIELDS => array(
                //                         'target' => $get_wa->Authlogin->nomor_whatsapp,
                //                         'message' =>
                //                         "Halo " . $get_wa->Cv->nama_lengkap . ",

                // Selamat! Anda telah dinyatakan diterima sebagai " . $get_recruitment_admin->recruitmentAdmin->Jabatan->nama_jabatan . "
                // " . $get_recruitment_admin->recruitmentAdmin->Jabatan->Bagian->Divisi->nama_divisi . "
                // di $holdings->holding_name. dengan benefit gaji Rp. $request->gaji 🎉

                // Kami mengundang Anda untuk hadir pada:
                // 📅 Hari/Tanggal : $hari_diterima 
                // 🕒 Pukul : $request->waktu_bekerja
                // 📍 Lokasi : $site_krj->site_name, $site_krj->gm_link
                // Untuk penjelasan lebih lanjut terkait administrasi dan training karyawan baru. Harap membawa dokumen yang diperlukan sesuai instruksi yang akan kami kirimkan.
                // Catatan : $request->notes_langsung

                // Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):
                // https://karir.sumberpangan.store/cpanel/recruitment_detail/$request->id

                //                 ",
                //                         'countryCode' => '62', //optional
                //                     ),
                //                     CURLOPT_HTTPHEADER => array(
                //                         'Authorization: Xp5bwZZN22VPhojYcPEB' //change TOKEN to your actual token
                //                     ),
                //                 ));

                //                 $response = curl_exec($curl);
                //                 if (curl_errno($curl)) {
                //                     $error_msg = curl_error($curl);
                //                 }
                //                 curl_close($curl);

                //                 if (isset($error_msg)) {
                //                     echo $error_msg;
                //                 }

                // lolos karyawan
                // dd($site_krj->gm_link, $site_krj->gm_link);
                $path = "/qontak/chat/v1/broadcasts/whatsapp/direct";
                if ($site_krj->gm_link == null) {
                    $gm_link = '-';
                } else {
                    $gm_link = $site_krj->gm_link;
                }
                $payload = [
                    "to_number" => $this->formatPhoneIndonesia($get_wa->Authlogin->nomor_whatsapp),
                    "to_name" => $cv->nama_lengkap,
                    "message_template_id" => "56fcac8b-922d-4067-80f6-fb4cd571c79e",
                    "channel_integration_id" => "2cde26dd-8c36-4b9e-97d1-4b64634fd72f",
                    "language" => ["code" => "id"],
                    "parameters" => [
                        "body" => [
                            [
                                "key" => "1",
                                "value" => "nama",
                                "value_text" => $get_wa->Cv->nama_lengkap
                            ],
                            [
                                "key" => "2",
                                "value" => "jabatan",
                                "value_text" => $jabatan->nama_jabatan
                            ],
                            [
                                "key" => "3",
                                "value" => "divisi",
                                "value_text" => $divisi->nama_divisi
                            ],

                            [
                                "key" => "4",
                                "value" => "holding_name",
                                "value_text" => $holdings->holding_name
                            ],
                            [
                                "key" => "5",
                                "value" => "gaji",
                                "value_text" => "$request->gaji"
                            ],
                            [
                                "key" => "6",
                                "value" => "hari_diterima",
                                "value_text" => "$hari_diterima"
                            ],
                            [
                                "key" => "7",
                                "value" => "waktu_bekerja",
                                "value_text" => "$request->waktu_bekerja"
                            ],
                            [
                                "key" => "8",
                                "value" => "site_name",
                                "value_text" => $site_krj->site_name
                            ],
                            [
                                "key" => "9",
                                "value" => "gm_link",
                                "value_text" => $gm_link
                            ],

                            [
                                "key" => "10",
                                "value" => "notes_langsung",
                                "value_text" => "Lihat di web dan email"
                            ],
                            [
                                "key" => "11",
                                "value" => "id_user",
                                "value_text" => "$request->id"
                            ],
                        ]

                    ]
                ];
                $response = $this->mekari->request('POST', $path, $payload);
                $status_mekari = 'Success';
                $message_mekari = null;

                if (is_array($response)) {
                    $status_mekari = isset($response['error']) ? 'Failed' : 'Success';
                    $message_mekari = json_encode($response);
                } elseif (is_object($response)) {
                    // kalau object biasa (stdClass)
                    $responseArray = json_decode(json_encode($response), true);

                    $status_mekari = isset($responseArray['error']) ? 'Failed' : 'Success';
                    $message_mekari = json_encode($responseArray);
                } else {
                    // fallback (string / null)
                    $status_mekari = 'Unknown';
                    $message_mekari = (string) $response;
                }
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'table' => 'recruitment_user',
                    'activity' => $status_mekari,
                    'description' => 'mengubah status pelamar menjadi lolos langsung ' . $cv->nama_lengkap . ' pada lowongan ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan . ' ' . $message_mekari,
                    'keterangan' => 'Success',
                ]);

                $jabatan    = $get_recruitment_admin->recruitmentAdmin->Jabatan->nama_jabatan;
                $divisi     = $get_recruitment_admin->recruitmentAdmin->Jabatan->Bagian->Divisi->nama_divisi;
                $gaji       = $request->gaji ?? '-';
                $email      = $get_wa->Authlogin->email;
                $nama       = $get_wa->Cv->nama_lengkap;
                $e_holding  = $holdings->holding_name;
                $hari       = $hari_diterima;
                $pukul      = $request->waktu_bekerja;
                $tempat     = $site_krj->site_name;
                $maps       = $site_krj->gm_link ?? '-';
                $user_id    = $request->id;
                Cache::put("Lolos Langsung:$email", true, now()->addHours(2));
                Mail::to($email)->send(new LoloslangsungMail($nama, $jabatan, $divisi, $e_holding, $gaji, $hari, $pukul, $tempat, $maps, $user_id));
                // dd($response);

            } elseif ($request->status == '3b') {
                RecruitmentUser::where('id', $request->id)->update(

                    [
                        'status_lanjutan'      => $request->status,
                        'updated_at'            => date('Y-m-d H:i:s'),
                    ]
                );
                RecruitmentUserRecord::insert(
                    [
                        'id'                        => Uuid::uuid4(),
                        'recruitment_user_id'       => $request->id,
                        'status'                    => $request->status,
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                $get_holding = RecruitmentUser::where('id', $request->id)->first();
                $holdings = Holding::where('id', $get_holding->holding)->first();
                $cv = RecruitmentCV::where('users_career_id', $get_holding->users_career_id)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $get_holding->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $get_holding->nama_jabatan)->first();
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'mengubah status pelamar menjadi tidak lolos interview manager ' . $cv->nama_lengkap . ' pada lowongan ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);

                //                 $curl = curl_init();
                //                 curl_setopt_array($curl, array(
                //                     CURLOPT_URL => 'https://api.fonnte.com/send',
                //                     CURLOPT_RETURNTRANSFER => true,
                //                     CURLOPT_ENCODING => '',
                //                     CURLOPT_MAXREDIRS => 10,
                //                     CURLOPT_TIMEOUT => 0,
                //                     CURLOPT_FOLLOWLOCATION => true,
                //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                     CURLOPT_CUSTOMREQUEST => 'POST',
                //                     CURLOPT_POSTFIELDS => array(
                //                         'target' => $get_wa->Authlogin->nomor_whatsapp,
                //                         'message' =>
                //                         "Halo " . $get_wa->Cv->nama_lengkap . ",
                // Terima kasih sudah mengikuti proses wawancara bersama Manager di $holdings->holding_name.
                // Setelah mempertimbangkan hasil seleksi, dengan berat hati kami sampaikan bahwa Anda belum dapat melanjutkan ke tahap berikutnya.
                // Kami sangat menghargai waktu dan usaha Anda, serta akan menyimpan data Anda untuk kesempatan lain yang lebih sesuai di kemudian hari.
                //                 ",
                //                         'countryCode' => '62', //optional
                //                     ),
                //                     CURLOPT_HTTPHEADER => array(
                //                         'Authorization: Xp5bwZZN22VPhojYcPEB' //change TOKEN to your actual token
                //                     ),
                //                 ));

                //                 $response = curl_exec($curl);
                //                 if (curl_errno($curl)) {
                //                     $error_msg = curl_error($curl);
                //                 }
                //                 curl_close($curl);

                //                 if (isset($error_msg)) {
                //                     echo $error_msg;
                //                 }

                $path = "/qontak/chat/v1/broadcasts/whatsapp/direct";
                $payload = [
                    "to_number" => $this->formatPhoneIndonesia($get_wa->Authlogin->nomor_whatsapp),
                    "to_name" => $cv->nama_lengkap,
                    "message_template_id" => "0b3d29f9-40fb-49f1-b494-719d4e0883f0",
                    "channel_integration_id" => "2cde26dd-8c36-4b9e-97d1-4b64634fd72f",
                    "language" => ["code" => "id"],
                    "parameters" => [
                        "body" => [
                            [
                                "key" => "1",
                                "value" => "nama_lengkap",
                                "value_text" => $get_wa->Cv->nama_lengkap
                            ],
                            [
                                "key" => "2",
                                "value" => "holding_name",
                                "value_text" => $holdings->holding_name
                            ],

                        ]

                    ]
                ];
                $response = $this->mekari->request('POST', $path, $payload);

                $email      = $get_wa->Authlogin->email;
                $nama       = $get_wa->Cv->nama_lengkap;
                $e_holding  = $holdings->holding_name;
                Cache::put("Gagal:$email", true, now()->addHours(2));
                Mail::to($email)->send(new TidaklolosMail($nama, $e_holding));
            } elseif ($request->status == '6b') {
                // dd($request->all());
                $tanggal_wawancara = Carbon::parse($request->tanggal_wawancara);
                $hari = $tanggal_wawancara->translatedFormat('l, j F Y');
                if ($request->online == 1) {
                    $link_wawancara = 'nullable';
                    $tempat_wawancara = 'required';
                    $site = Site::where('id', $request->tempat_wawancara)->first();
                    if ($site == null) {
                        return response()->json([
                            'code' => 500,
                            'error' => 'Site Wawancara Kosong',
                        ]);
                    }
                    $tempat = 'Lokasi : ' . $site->site_name;
                    $map = $site->gm_link;
                } elseif ($request->online == 2) {
                    $link_wawancara = 'required';
                    $tempat_wawancara = 'nullable';
                    $tempat = 'Link Zoom : ' . $request->link_wawancara;
                    $map = '-';
                }
                $validator = Validator::make(
                    $request->all(),
                    [
                        'lowongan_baru' => 'required',
                        'tanggal_wawancara' => 'required',
                        'link_wawancara' => $link_wawancara,
                        'tempat_wawancara' => $tempat_wawancara,
                        'waktu_wawancara' => 'required',
                    ],
                    [
                        'required' => ':attribute tidak boleh kosong'
                    ]
                );
                if ($validator->fails()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ]);
                }
                // dd($request->all());
                RecruitmentUser::where('id', $request->id)->update(

                    [
                        'tanggal_wawancara_manager'     => $request->tanggal_wawancara,
                        'tempat_wawancara'              => $tempat,
                        'waktu_wawancara'               => $request->waktu_wawancara,
                        'status_lanjutan'               => $request->status,
                        'feedback_lanjutan'             => null,
                        'tanggal_konfirmasi_manager'    => date('Y-m-d H:i:s', strtotime('+2 days')),
                        'updated_at'                    => date('Y-m-d H:i:s'),
                    ]
                );
                RecruitmentUserRecord::insert(
                    [
                        'id'                     => Uuid::uuid4(),
                        'lowongan_baru'          => $request->lowongan_baru,
                        'lowongan_lama'          => $request->lowongan_lama,
                        'recruitment_user_id'    => $request->id,
                        'status'                 => $request->status,
                        'created_at'             => date('Y-m-d H:i:s'),
                    ]
                );
                // dd($get_wa);
                $get_posisi_baru = Recruitment::with([
                    'Jabatan' => function ($query) {
                        $query->with([
                            'Bagian' =>  function ($query) {
                                $query->with([
                                    'Divisi' => function ($query) {
                                        $query->with([
                                            'Departemen' => function ($query) {
                                                $query->orderBy('nama_departemen', 'ASC');
                                            }
                                        ]);
                                        $query->orderBy('nama_divisi', 'ASC');
                                    },
                                ]);
                            },
                        ]);
                    },

                ])
                    ->where('id', $request->lowongan_baru)
                    ->first();
                //                 $curl = curl_init();
                //                 curl_setopt_array($curl, array(
                //                     CURLOPT_URL => 'https://api.fonnte.com/send',
                //                     CURLOPT_RETURNTRANSFER => true,
                //                     CURLOPT_ENCODING => '',
                //                     CURLOPT_MAXREDIRS => 10,
                //                     CURLOPT_TIMEOUT => 0,
                //                     CURLOPT_FOLLOWLOCATION => true,
                //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                     CURLOPT_CUSTOMREQUEST => 'POST',
                //                     CURLOPT_POSTFIELDS => array(
                //                         'target' => $get_wa->Authlogin->nomor_whatsapp,
                //                         'message' =>
                //                         "Halo " . $get_wa->Cv->nama_lengkap . ",
                // Terima kasih telah mengikuti proses seleksi di $holdings->holding_name. Setelah mempertimbangkan kualifikasi Anda,
                // kami melihat potensi Anda lebih sesuai dengan posisi " . $get_posisi_baru->Jabatan->nama_jabatan . ".
                // " . $get_posisi_baru->Jabatan->Bagian->Divisi->nama_divisi . ".

                // Kami mengundang Anda untuk melanjutkan ke wawancara bersama Manager pada:
                // 📅 Hari/Tanggal : $hari
                // 🕒 Pukul : $request->waktu_wawancara WIB
                // 📍 $tempat, $map

                // Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):
                // https://karir.sumberpangan.store/cpanel/recruitment_detail/$request->id
                // ",
                //                         'countryCode' => '62', //optional
                //                     ),
                //                     CURLOPT_HTTPHEADER => array(
                //                         'Authorization: Xp5bwZZN22VPhojYcPEB' //change TOKEN to your actual token
                //                     ),
                //                 ));

                //                 $response = curl_exec($curl);
                //                 if (curl_errno($curl)) {
                //                     $error_msg = curl_error($curl);
                //                 }
                //                 curl_close($curl);

                //                 if (isset($error_msg)) {
                //                     echo $error_msg;
                //                 }
                $get_holding = RecruitmentUser::where('id', $request->id)->first();
                $holdingst = Holding::where('id', $get_holding->holding)->first();
                $cv = RecruitmentCV::where('users_career_id', $get_holding->users_career_id)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $get_holding->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $get_holding->nama_jabatan)->first();
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holdingst->id,
                    'holding_category' => $holdingst->holding_category,
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'memberi tawaran wawancara manager lain ' . $cv->nama_lengkap . ' dari lowongan ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan .
                        ' menuju ' . $get_posisi_baru->Jabatan->Bagian->Divisi->nama_divisi . ' ' . $get_posisi_baru->Jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);

                // lolos wawancara manager lain
                $path = "/qontak/chat/v1/broadcasts/whatsapp/direct";
                $payload = [
                    "to_number" => $this->formatPhoneIndonesia($get_wa->Authlogin->nomor_whatsapp),
                    "to_name" => $cv->nama_lengkap,
                    "message_template_id" => "d5e90509-c238-4a2c-9973-be0df2e0274c",
                    "channel_integration_id" => "2cde26dd-8c36-4b9e-97d1-4b64634fd72f",
                    "language" => ["code" => "id"],
                    "parameters" => [
                        "body" => [
                            [
                                "key" => "1",
                                "value" => "nama_lengkap",
                                "value_text" => $get_wa->Cv->nama_lengkap
                            ],
                            [
                                "key" => "2",
                                "value" => "holding_name",
                                "value_text" => $holdings->holding_name
                            ],
                            [
                                "key" => "3",
                                "value" => "nama_jabatan",
                                "value_text" => $jabatan->nama_jabatan
                            ],
                            [
                                "key" => "4",
                                "value" => "nama_divisi",
                                "value_text" => $divisi->nama_divisi
                            ],
                            [
                                "key" => "5",
                                "value" => "hari",
                                "value_text" => "$hari"
                            ],
                            [
                                "key" => "6",
                                "value" => "waktu_wawancara",
                                "value_text" => "$request->waktu_wawancara"
                            ],
                            [
                                "key" => "7",
                                "value" => "tempa",
                                "value_text" => "$tempat"
                            ],
                            [
                                "key" => "8",
                                "value" => "map",
                                "value_text" => "$map"
                            ],
                            [
                                "key" => "9",
                                "value" => "id_user",
                                "value_text" => "$request->id"
                            ],
                        ]

                    ]
                ];

                $response = $this->mekari->request('POST', $path, $payload);


                $jabatan    = $get_recruitment_admin->recruitmentAdmin->Jabatan->nama_jabatan;
                $divisi     = $get_recruitment_admin->recruitmentAdmin->Jabatan->Bagian->Divisi->nama_divisi;
                $gaji       = $request->gaji;
                $email      = $get_wa->Authlogin->email;
                $nama       = $get_wa->Cv->nama_lengkap;
                $e_holding  = $holdings->holding_name;
                $pukul      = $request->waktu_wawancara;
                $tempat     = $request->waktu_wawancara;
                $maps       = $site_krj->gm_link ?? '-';
                $user_id    = $request->id;
                Cache::put("Lolos:$email", true, now()->addHours(2));
                Mail::to($email)->send(new ManagerlainMail($nama, $e_holding, $jabatan, $divisi, $gaji, $hari, $pukul, $tempat, $maps, $user_id));
            } elseif ($request->status == '7b') {
                $site_krj = Site::where('id', $request->tempat_bekerja)->first();
                if ($site_krj == null) {
                    return response()->json([
                        'code' => 500,
                        'error' => 'Site Wawancara Kosong',
                    ]);
                }
                // dd($request->all);
                $validator = Validator::make(
                    $request->all(),
                    [
                        'tempat_bekerja' => 'required',
                        'waktu_bekerja' => 'required',
                        'lowongan_baru' => 'required',
                        'tanggal_diterima' => 'required',
                    ],
                    [
                        'required' => ':attribute tidak boleh kosong'
                    ]
                );
                if ($validator->fails()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ]);
                }
                RecruitmentUser::where('id', $request->id)->update(

                    [
                        'status_lanjutan'       => $request->status,
                        'feedback_lanjutan'     => null,
                        'tempat_bekerja'        => $site_krj->site_name,
                        'tanggal_diterima'      => $request->tanggal_diterima,
                        'gaji'                  => $request->gaji,
                        'notes'                 => $request->notes_langsung,
                        'konfirmasi_diterima'   => date('Y-m-d H:i:s', strtotime('+2 days')),
                        'updated_at'            => date('Y-m-d H:i:s'),
                    ]
                );
                RecruitmentUserRecord::insert(
                    [
                        'id'                        => Uuid::uuid4(),
                        'lowongan_baru'             => $request->lowongan_baru,
                        'lowongan_lama'             => $request->lowongan_lama,
                        'recruitment_user_id'       => $request->id,
                        'status'                    => $request->status,
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                $get_wa = RecruitmentUser::with([
                    'AuthLogin' => function ($query) {
                        $query;
                    }
                ])->where('id', $request->id)->first();
                // dd($get_wa);
                $get_posisi_baru = Recruitment::with([
                    'Jabatan' => function ($query) {
                        $query->with([
                            'Bagian' =>  function ($query) {
                                $query->with([
                                    'Divisi' => function ($query) {
                                        $query->with([
                                            'Departemen' => function ($query) {
                                                $query->orderBy('nama_departemen', 'ASC');
                                            }
                                        ]);
                                        $query->orderBy('nama_divisi', 'ASC');
                                    },
                                ]);
                            },
                        ]);
                    },

                ])
                    ->where('id', $request->lowongan_baru)
                    ->first();
                $tanggal_diterima = Carbon::parse($request->tanggal_diterima);
                $hari_diterima2 = $tanggal_diterima->translatedFormat('l, j F Y');
                //                 $curl = curl_init();
                //                 curl_setopt_array($curl, array(
                //                     CURLOPT_URL => 'https://api.fonnte.com/send',
                //                     CURLOPT_RETURNTRANSFER => true,
                //                     CURLOPT_ENCODING => '',
                //                     CURLOPT_MAXREDIRS => 10,
                //                     CURLOPT_TIMEOUT => 0,
                //                     CURLOPT_FOLLOWLOCATION => true,
                //                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //                     CURLOPT_CUSTOMREQUEST => 'POST',
                //                     CURLOPT_POSTFIELDS => array(
                //                         'target' => $get_wa->Authlogin->nomor_whatsapp,
                //                         'message' =>
                //                         "Halo " . $get_wa->Cv->nama_lengkap . ",
                // Terima kasih telah mengikuti proses seleksi di $holdings->holding_name. Setelah mempertimbangkan kualifikasi Anda,
                // kami melihat potensi Anda lebih sesuai dengan posisi " . $get_posisi_baru->Jabatan->nama_jabatan . ".
                // " . $get_posisi_baru->Jabatan->Bagian->Divisi->nama_divisi . ". dengan benefit gaji Rp. $request->gaji 🎉

                // Kami mengundang Anda untuk hadir pada:
                // 📅 Hari/Tanggal : $hari_diterima2
                // 🕒 Pukul : $request->waktu_bekerja
                // 📍 Lokasi : $site_krj->site_name, $site_krj->gm_link
                // Untuk penjelasan lebih lanjut terkait administrasi dan training karyawan baru. Harap membawa dokumen yang diperlukan sesuai instruksi yang akan kami kirimkan.

                // Mohon konfirmasi kehadiran Anda pada link di bawah ini (Max. 24 Jam):
                // https://karir.sumberpangan.store/cpanel/recruitment_detail/$request->id
                //                                 ",
                //                         'countryCode' => '62', //optional
                //                     ),
                //                     CURLOPT_HTTPHEADER => array(
                //                         'Authorization: Xp5bwZZN22VPhojYcPEB' //change TOKEN to your actual token
                //                     ),
                //                 ));

                //                 $response = curl_exec($curl);
                //                 if (curl_errno($curl)) {
                //                     $error_msg = curl_error($curl);
                //                 }
                //                 curl_close($curl);

                //                 if (isset($error_msg)) {
                //                     echo $error_msg;
                //                 }
                $get_holding = RecruitmentUser::where('id', $request->id)->first();
                $holdings = Holding::where('id', $get_holding->holding)->first();
                $cv = RecruitmentCV::where('users_career_id', $get_holding->users_career_id)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();
                $divisi = Divisi::where('id', $get_holding->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $get_holding->nama_jabatan)->first();
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $request->id,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'memberi tawaran pekerjaan lain ' . $cv->nama_lengkap . ' dari lowongan ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan .
                        ' menuju ' . $get_posisi_baru->Jabatan->Bagian->Divisi->nama_divisi . ' ' . $get_posisi_baru->Jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);

                // lolos langsung posisi lain

                $path = "/qontak/chat/v1/broadcasts/whatsapp/direct";
                if ($site_krj->gm_link == null) {
                    $gm_link = '-';
                } else {
                    $gm_link = $site_krj->gm_link;
                }
                $payload = [
                    "to_number" => $this->formatPhoneIndonesia($get_wa->Authlogin->nomor_whatsapp),
                    "to_name" => $get_wa->Cv->nama_lengkap,
                    "message_template_id" => "cc730d3f-7385-4dc6-89d0-0093b48ce1a2",
                    "channel_integration_id" => "2cde26dd-8c36-4b9e-97d1-4b64634fd72f",
                    "language" => ["code" => "id"],
                    "parameters" => [
                        "body" => [
                            [
                                "key" => "1",
                                "value" => "nama",
                                "value_text" => $get_wa->Cv->nama_lengkap
                            ],
                            [
                                "key" => "2",
                                "value" => "holding_name",
                                "value_text" => "$holdings->holding_name"
                            ],
                            [
                                "key" => "3",
                                "value" => "nama_jabatan",
                                "value_text" => "$jabatan->nama_jabatan"
                            ],
                            [
                                "key" => "4",
                                "value" => "nama_divisi",
                                "value_text" => "$divisi->nama_divisi"
                            ],
                            [
                                "key" => "5",
                                "value" => "gaji",
                                "value_text" => "$request->gaji"
                            ],
                            [
                                "key" => "6",
                                "value" => "hari_diterima2",
                                "value_text" => "$hari_diterima2"
                            ],
                            [
                                "key" => "7",
                                "value" => "waktu_bekerja",
                                "value_text" => "$request->waktu_bekerja"
                            ],
                            [
                                "key" => "8",
                                "value" => "site_name",
                                "value_text" => "$site_krj->site_name"
                            ],
                            [
                                "key" => "9",
                                "value" => "gm_link",
                                "value_text" => "$gm_link"
                            ],

                            [
                                "key" => "10",
                                "value" => "id_user",
                                "value_text" => "$request->id"
                            ],
                        ]

                    ]
                ];

                $response = $this->mekari->request('POST', $path, $payload);



                $jabatan    = $get_recruitment_admin->recruitmentAdmin->Jabatan->nama_jabatan;
                $divisi     = $get_recruitment_admin->recruitmentAdmin->Jabatan->Bagian->Divisi->nama_divisi;
                $gaji       = $request->gaji;
                $email      = $get_wa->Authlogin->email;
                $nama       = $get_wa->Cv->nama_lengkap;
                $hari       = $hari_diterima2;
                $e_holding  = $holdings->holding_name;
                $pukul      = $request->waktu_wawancara;
                $tempat     = $request->waktu_wawancara;
                $maps       = $site_krj->gm_link ?? '-';
                $user_id    = $request->id;
                Cache::put("Lolos:$email", true, now()->addHours(2));
                Mail::to($email)->send(new LolosposisilainMail($nama, $e_holding, $jabatan, $divisi, $pukul, $hari, $tempat, $maps, $user_id));
            }
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
    public function user_integrasi(Request $request)
    {
        // dd($request->all());
        try {
            if ($request->pilihan == '1') {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'pilihan' => 'required',
                    ],
                    [
                        'required' => ':attribute tidak boleh kosong'
                    ]
                );
                if ($validator->fails()) {
                    return response()->json([
                        'code' => 400,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ]);
                }
                // dd($request->all());
                $get_recruitment_user = RecruitmentUser::where('id', $request->id)->with([
                    'recruitmentAdmin' => function ($query) {
                        $query->with([
                            'Sites' => function ($query) {
                                $query;
                            }
                        ])->with([
                            'Holding' => function ($query) {
                                $query;
                            }
                        ]);
                    }
                ])->first();
                // dd($get_recruitment_user->recruitmentAdmin->Holding->holding_number);
                $get_user = UsersCareer::where('id', $get_recruitment_user->users_career_id)->first();
                $get_cv = RecruitmentCV::where('users_career_id', $get_recruitment_user->users_career_id)->with([
                    'provinsiKTP' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'kabupatenKTP' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'kecamatanKTP' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'desaKTP' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'provinsiNOW' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'kabupatenNOW' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'kecamatanNOW' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->with([
                    'desaNOW' => function ($query) {
                        $query->orderBy('id', 'ASC');
                    }
                ])->first();
                if ($get_cv->alamat_sekarang == 'sama') {
                    $dom = 'ya';
                    $alamatDom  = null;
                } else {
                    $alamatDom  = $get_cv->provinsiNOW->name . ', ' . $get_cv->kabupatenNOW->name . ', ' . $get_cv->desaNOW->name . ', RT. ' . $get_cv->rw_now . ', RW. ' . $get_cv->rw_now;
                    $dom = 'tidak';
                }
                // Foto Karyawan
                $filePP = url_karir() . '/storage/file_pp/' . $get_cv->file_pp;
                $response_pp = Http::get($filePP);
                if ($response_pp->successful()) {
                    Storage::disk('public')->put('foto_karyawan/' . $get_cv->file_pp, $response_pp->body());
                }
                // Foto Karyawan End

                // Foto ktp
                $fileKTP = url_karir() . '/storage/ktp/' . $get_cv->ktp;
                $response_ktp = Http::get($fileKTP);

                if ($response_ktp->successful()) {
                    if (!Storage::disk('public')->exists('ktp')) {
                        Storage::disk('public')->makeDirectory('ktp');
                    }
                    Storage::disk('public')->put('ktp/' . $get_cv->ktp, $response_ktp->body());
                }
                // Foto ktp End

                // file ijazah
                $fileIjazah = url_karir() . '/storage/ijazah/' . $get_cv->ijazah;
                $response_ijazah = Http::get($fileIjazah);

                if ($response_ijazah->successful()) {
                    if (!Storage::disk('public')->exists('ijazah')) {
                        Storage::disk('public')->makeDirectory('ijazah');
                    }
                    Storage::disk('public')->put('ijazah/' . $get_cv->ijazah, $response_ijazah->body());
                }
                // file ijazah End

                // Transkrip Nilai
                $fileTranskripNilai = url_karir() . '/storage/transkrip_nilai/' . $get_cv->transkrip_nilai;
                $response_transkrip_nilai = Http::get($fileTranskripNilai);

                if ($response_transkrip_nilai->successful()) {
                    if (!Storage::disk('public')->exists('transkrip_nilai')) {
                        Storage::disk('public')->makeDirectory('transkrip_nilai');
                    }
                    Storage::disk('public')->put('transkrip_nilai/' . $get_cv->transkrip_nilai, $response_transkrip_nilai->body());
                }
                // Transkrip Nilai End
                // dd($response_transkrip_nilai->body());
                $id_karyawan = Uuid::uuid4();

                $no_karyawan = $get_recruitment_user->recruitmentAdmin->Holding->holding_number . '00' . date('ym', strtotime($get_recruitment_user->tanggal_diterima)) . date('dmy', strtotime($get_cv->tanggal_lahir));

                Karyawan::insert(

                    [
                        'id'                        => $id_karyawan,
                        'name'                      => $get_cv->nama_lengkap,
                        'nomor_identitas_karyawan'  => $no_karyawan,
                        'email'                     => $get_user->email,
                        'telepon'                   => $get_user->nomor_whatsapp,
                        'agama'                     => $get_cv->agama,
                        'foto_karyawan'             => $get_cv->file_pp,
                        'email'                     => $get_user->email,
                        'status_nomor'              => 'ya',
                        'nomor_wa'                  => $get_user->nomor_whatsapp,
                        'tempat_lahir'              => $get_cv->tempat_lahir,
                        'tgl_lahir'                 => $get_cv->tanggal_lahir,
                        'gender'                    => $get_cv->jenis_kelamin,
                        'tgl_join'                  => $get_recruitment_user->tanggal_diterima,
                        'status_alamat'             => $dom,
                        'status_nikah'              => $get_cv->status_pernikahan,
                        'jumlah_anak'               => $get_cv->jumlah_anak,
                        'ijazah'                    => $get_cv->ijazah,
                        'transkrip_nilai'           => $get_cv->transkrip_nilai,
                        'ipk'                       => $get_cv->ipk,
                        'provinsi_domisili'         => $get_cv->provinsi_now,
                        'kabupaten_domisili'        => $get_cv->kabupaten_now,
                        'kecamatan_domisili'        => $get_cv->kecamatan_now,
                        'desa_domisili'             => $get_cv->desa_now,
                        'rt_domisili'               => $get_cv->rt_now,
                        'rw_domisili'               => $get_cv->rw_now,
                        'alamat_domisili'           => $alamatDom,
                        'nik'                       => $get_cv->nik,
                        'ktp'                       => $get_cv->ktp,
                        'detail_alamat'             => $get_cv->nama_jalan_now,
                        'provinsi'                  => $get_cv->provinsi_ktp,
                        'kabupaten'                 => $get_cv->kabupaten_ktp,
                        'kecamatan'                 => $get_cv->kecamatan_ktp,
                        'desa'                      => $get_cv->desa_ktp,
                        'rt'                        => $get_cv->rt_ktp,
                        'rw'                        => $get_cv->rw_ktp,
                        'detail_alamat'             => $get_cv->nama_jalan_ktp,
                        'alamat'                    => $get_cv->provinsiKTP->name . ', ' . $get_cv->kabupatenKTP->name
                            . ', ' . $get_cv->kecamatanKTP->name . ', ' . $get_cv->desaKTP->name . ', RT. ' .  $get_cv->rw_ktp . ', RW. ' .  $get_cv->rw_ktp,
                        'kuota_cuti_tahunan'        => '0',
                        'kategori'                  => 'Karyawan Bulanan',
                        'kontrak_kerja'             => $get_recruitment_user->holding,
                        // 'kategori_jabatan'          => $get_recruitment_user->holding,

                        'penempatan_kerja'          => $get_recruitment_user->tempat_bekerja,
                        'status_aktif'              => 'AKTIF',
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                $holdings = Holding::where('id', $get_recruitment_user->holding)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                $user_name = User::where('id', $request->user()->id)->first();

                $divisi = Divisi::where('id', $get_recruitment_user->nama_divisi)->first();
                $jabatan = Jabatan::where('id', $get_recruitment_user->nama_jabatan)->first();
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' =>  $id_karyawan,
                    'holding_id' => $holdings->id,
                    'holding_category' => $holdings->holding_category,
                    'table' => 'recruitment_user menuju karyawans',
                    'activity' => 'update',
                    'description' => 'integrasi data pelamar menjadi karyawan ' . $get_cv->nama_lengkap . 'pada lowongan ' . $divisi->nama_divisi . ' ' . $jabatan->nama_jabatan,
                    'keterangan' => 'Success',
                ]);
                KaryawanJabatan::insert(
                    [
                        'id_jbt_karyawan'       => Uuid::uuid4(),
                        'karyawan_id'           => $id_karyawan,
                        'departemen_id'         => $get_recruitment_user->nama_dept,
                        'divisi_id'             => $get_recruitment_user->nama_divisi,
                        'bagian_id'             => $get_recruitment_user->nama_bagian,
                        'jabatan_id'            => $get_recruitment_user->nama_jabatan,
                        'holding_id'            => $get_recruitment_user->holding,
                        'main'                  => 1,
                        'is_active'             => 1,
                        'created_at'            => date('Y-m-d H:i:s'),
                    ]
                );
                // dd($ss);
                $pendidikan = RecruitmentPendidikan::where('id_user', $get_user->id)->get();
                foreach ($pendidikan as $pp) {
                    KaryawanPendidikan::insert(
                        [
                            'id_pendidikan'         => Uuid::uuid4(),
                            'id_karyawan'           => $id_karyawan,
                            'institusi'             => $pp->institusi,
                            'jurusan'               => $pp->jurusan,
                            'jenjang'               => $pp->jenjang,
                            'tanggal_masuk'         => $pp->tanggal_masuk,
                            'tanggal_keluar'        => $pp->tanggal_keluar,
                            'created_at'            => date('Y-m-d H:i:s'),
                        ]
                    );
                }
                $keahlian = RecruitmentKeahlian::where('id_user', $get_user->id)->get();
                if (!empty($keahlian)) {
                    foreach ($keahlian as $kk) {
                        $fileKeahlian = url_karir() . '/storage/file_keahlian/' . $kk->file_keahlian;
                        if (!empty($fileKeahlian)) {

                            $response = Http::get($fileKeahlian);

                            if ($response->successful()) {
                                Storage::disk('public')->put('file_keahlian/' . $kk->file_keahlian, $response->body());
                            }
                        }
                        KaryawanKeahlian::insert(
                            [
                                'id_keahlian'           => Uuid::uuid4(),
                                'id_karyawan'           => $id_karyawan,
                                'keahlian'              => $kk->keahlian,
                                'file_keahlian'         => $kk->file_keahlian,
                                'created_at'            => date('Y-m-d H:i:s'),
                            ]
                        );
                    }
                }
                // insert kesehatan
                $kesehatan = RecruitmentKesehatan::where('id_user', $get_user->id)->first();
                KaryawanKesehatan::insert(
                    [
                        'id_kesehatan'                      => Uuid::uuid4(),
                        'id_karyawan'                       => $id_karyawan,
                        'perokok'                           => $kesehatan->perokok,
                        'alkohol'                           => $kesehatan->alkohol,
                        'alergi'                            => $kesehatan->alergi,
                        'sebutkan_alergi'                   => $kesehatan->sebutkan_alergi,
                        'pengobatan_rutin'                  => $kesehatan->pengobatan_rutin,
                        'asma'                              => $kesehatan->asma,
                        'hipertensi'                        => $kesehatan->hipertensi,
                        'jantung'                           => $kesehatan->jantung,
                        'tbc'                               => $kesehatan->tbc,
                        'hepatitis'                         => $kesehatan->hepatitis,
                        'epilepsi'                          => $kesehatan->epilepsi,
                        'gangguan_mental'                   => $kesehatan->gangguan_mental,
                        'gangguan_pengelihatan'             => $kesehatan->gangguan_pengelihatan,
                        'gangguan_lainnya'                  => $kesehatan->gangguan_lainnya,
                        'pernah_dirawat_rs'                 => $kesehatan->pernah_dirawat_rs,
                        'kecelakaan_serius'                 => $kesehatan->kecelakaan_serius,
                        'keterbatasan_fisik'                => $kesehatan->keterbatasan_fisik,
                        'mampu_shift'                       => $kesehatan->mampu_shift,
                        'pemeriksaan_kerja_sebelumnya'      => $kesehatan->pemeriksaan_kerja_sebelumnya,
                        'covid'                             => $kesehatan->covid,
                        'tetanus'                           => $kesehatan->tetanus,
                        'vaksin_lainnya'                    => $kesehatan->vaksin_lainnya,
                        'persetujuan_kesehatan'             => $kesehatan->persetujuan_kesehatan,
                        'created_at'                        => date('Y-m-d H:i:s'),

                    ]
                );
                $kesehatan_kecelakaan = RecruitmentKesehatanKecelakaan::where('id_user', $get_user->id)->get();
                if (!empty($kesehatan_kecelakaan)) {
                    foreach ($kesehatan_kecelakaan as $kecelakaan) {
                        KaryawanKesehatanKecelakaan::insert([
                            'id_kecelakaan'         => Uuid::uuid4(),
                            'id_karyawan'           => $id_karyawan,
                            'tahun_kecelakaan'      => $kecelakaan->tahun_kecelakaan,
                            'penyebab_kecelakaan'   => $kecelakaan->penyebab_kecelakaan,
                            'created_at'            => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                $pengobatan = RecruitmentKesehatanPengobatan::where('id_user', $get_user->id)->get();
                if (!empty($pengobatan)) {
                    foreach ($pengobatan as $obat) {
                        KaryawanKesehatanPengobatan::insert(
                            [
                                'id_pengobatan'         => Uuid::uuid4(),
                                'id_karyawan'           => $id_karyawan,
                                'obat'                  => $obat->obat,
                                'alasan'                => $obat->alasan,
                                'created_at'            => date('Y-m-d H:i:s'),
                            ]
                        );
                    }
                }
                $rumah_sakit = RecruitmentKesehatanRS::where('id_user', $get_user->id)->get();
                if (!empty($rumah_sakit)) {
                    foreach ($rumah_sakit as $rs) {
                        KaryawanKesehatanRS::insert([
                            'id_kesehatan_rs'   => Uuid::uuid4(),
                            'id_karyawan'       => $id_karyawan,
                            'tahun_rs'          => $rs->tahun_rs,
                            'penyebab_rs'       => $rs->penyebab_rs,
                            'created_at'            => date('Y-m-d H:i:s'),

                        ]);
                    }
                }
                // end insert kesehatan
                // Riwayat Pekerjaan
                $riwayat = RecruitmentRiwayat::where('id_user', $get_user->id)->get();
                if (!empty($riwayat)) {
                    foreach ($riwayat as $rw) {
                        if ($rw->surat_keterangan != null) {

                            $fileKeterangan = url_karir() . '/storage/surat_keterangan/' . $rw->surat_keterangan;
                            if (!empty($fileKeterangan)) {
                                $response = Http::get($fileKeterangan);
                                if ($response->successful()) {
                                    Storage::disk('public')->put('surat_keterangan/' . $rw->surat_keterangan, $response->body());
                                }
                            } else {
                                // return response()->json([
                                //     'code' => 500,
                                //     'message' =>  $fileKeterangan
                                // ]);
                            }
                        }
                        KaryawanRiwayat::insert([
                            'id_riwayat'            => Uuid::uuid4(),
                            'id_karyawan'           => $id_karyawan,
                            'nama_perusahaan'       => $rw->nama_perusahaan,
                            'gaji'                  => $rw->gaji,
                            'alamat_perusahaan'     => $rw->alamat_perusahaan,
                            'posisi'                => $rw->posisi,
                            'tanggal_masuk'         => $rw->tanggal_masuk,
                            'tanggal_keluar'        => $rw->tanggal_keluar,
                            'alasan_keluar'         => $rw->alasan_keluar,
                            'surat_keterangan'      => $rw->surat_keterangan,
                            'nomor_referensi'       => $rw->nomor_referensi,
                            'jabatan_referensi'     => $rw->jabatan_referensi,
                            'created_at'            => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                // End Riwayat Pekerjaan
                RecruitmentUserRecord::insert(
                    [
                        'id'                        => Uuid::uuid4(),
                        'recruitment_user_id'       => $request->id,
                        'status'                    => '8b',
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                RecruitmentUser::where('id', $request->id)->update(
                    [
                        'status_lanjutan'           => '8b',
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ]
                );
                UserCareer::where('id', $get_user->id)->update(
                    [
                        'diterima'                  => '1',
                        'updated_at'                => date('Y-m-d H:i:s'),
                    ]
                );
            }
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    function pg_ujian($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $pembobotan = Pembobotan::first();
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.ujian.data_ujian', [
            'holding' => $holdings,
            'menus' => $menus,
            'title' => 'Data Ujian',
            'plugin' => '
                <link rel="stylesheet" type="text/css" href="' . url("/public/assets/cbt-malela/plugins/table/datatable/datatables.css") . '">
                <link rel="stylesheet" type="text/css" href="' . url("/public/assets/cbt-malela/plugins/table/datatable/dt-global_style.css") . '">
                <script src="' . url("/public/assets/cbt-malela") . '/plugins/table/datatable/datatables.js"></script>
                <script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'pembobotan' => $pembobotan
        ]);
    }
    function pembobotan_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'esai' => 'required|numeric',
                'pilihan_ganda' => 'required|numeric',
                'interview' => 'required|numeric',
                // 'interview_user' => 'required|numeric',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            Pembobotan::where('pembobotan_id', $request->pembobotan_id)->update([
                'esai' => $request->esai,
                'pilihan_ganda' => $request->pilihan_ganda,
                'interview' => $request->interview,
                // 'interview_user' => $request->interview_user,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->pembobotan_id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'pembobotan',
                'activity' => 'update',
                'description' => 'mengedit pembobotan esai: ' . $request->esai . '% Pilihan_ganda: ' . $request->pilihan_ganda . '% interview: ' . $request->interview . '%',
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    function dt_pembobotan()
    {
        $data = Pembobotan::get();
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addColumn('esai', function ($row) {
                    return $row->esai . '%';
                })
                ->addColumn('pilihan_ganda', function ($row) {
                    return $row->pilihan_ganda . '%';
                })
                ->addColumn('interview', function ($row) {
                    return $row->interview . '%';
                })
                ->addColumn('interview_user', function ($row) {
                    return $row->interview_user . '%';
                })
                ->addColumn('option', function ($row) {
                    return '
                        <button type="button" id="btn_modal_pembobotan"
                            data-pembobotan_id="' . $row->pembobotan_id . '"
                            data-esai="' . $row->esai . '"
                            data-pilihan_ganda="' . $row->pilihan_ganda . '"
                            data-interview="' . $row->interview . '"
                            data-interview_user="' . $row->interview_user . '"
                            class="btn btn-icon btn-info waves-effect waves-light">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>';
                })
                ->rawColumns(['esai', 'pilihan_ganda', 'interview', 'option'])
                ->make(true);
        }
    }
    function dt_ujian()
    {
        $data = Ujian::with([
            'ujianKategori' => function ($query) {
                $query;
            }
        ])
            ->where('esai', '0')
            ->orderBy('created_at', 'ASC')
            ->get();
        // dd($data);
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addColumn('nama', function ($row) {
                    return $row->nama;
                })
                ->addColumn('kategori', function ($row) {
                    return $row->ujianKategori->nama_kategori;
                })
                ->addColumn('nol', function ($row) {
                    if ($row->nol == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('satu', function ($row) {
                    if ($row->satu == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('dua', function ($row) {
                    if ($row->dua == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('tiga', function ($row) {
                    if ($row->tiga == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('empat', function ($row) {
                    if ($row->empat == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('lima', function ($row) {
                    if ($row->lima == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('lima', function ($row) {
                    if ($row->lima == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('enam', function ($row) {
                    if ($row->enam == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('option', function ($row) {
                    $holding = request()->segment(count(request()->segments()));
                    return '<a href="/pg-data-ujian/edit-ujian/' . $row->kode . '/' . $holding . '" class="btn btn-info btn-sm m-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </a>';
                })
                ->rawColumns(['created_at', 'kategori', 'nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'option'])
                ->make(true);
        }
    }
    function dt_esai()
    {
        $data = Ujian::with([
            'ujianKategori' => function ($query) {
                $query;
            }
        ])
            ->where('esai', '1')
            ->orderBy('created_at', 'ASC')
            ->get();
        // dd($data);
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addColumn('nama', function ($row) {
                    return $row->nama;
                })
                ->addColumn('kategori', function ($row) {
                    return $row->ujianKategori->nama_kategori;
                })
                ->addColumn('nol', function ($row) {
                    if ($row->nol == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('satu', function ($row) {
                    if ($row->satu == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('dua', function ($row) {
                    if ($row->dua == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('tiga', function ($row) {
                    if ($row->tiga == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('empat', function ($row) {
                    if ($row->empat == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('lima', function ($row) {
                    if ($row->lima == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('lima', function ($row) {
                    if ($row->lima == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('enam', function ($row) {
                    if ($row->enam == '1') {
                        $jabatan = '<span class="badge bg-label-success">YA</span>';
                    } else {
                        $jabatan = '<span class="badge bg-label-danger">TIDAK</span>';
                    };
                    return $jabatan;
                })
                ->addColumn('option', function ($row) {
                    $holding = request()->segment(count(request()->segments()));
                    return '<a href="/pg-data-ujian/edit-esai/' . $row->kode . '/' . $holding . '" class="btn btn-info btn-sm m-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg>
                                </a>';
                })
                ->rawColumns(['created_at', 'kategori', 'nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'option'])
                ->make(true);
        }
    }
    function dt_ujian_kategori()
    {
        $data = UjianKategori::get();
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addColumn('nama_kategori', function ($row) {
                    return $row->nama_kategori;
                })
                ->addColumn('option', function ($row) {
                    $btn =
                        '<button type="button" id="btn_edit_ujian_kategori"
                            data-id="' . $row->id . '"
                            data-nama_kategori="' . $row->nama_kategori . '"
                            class="btn btn-icon btn-info waves-effect waves-light">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>
                        <button type="button" id="btn_delete_ujian_kategori"
                            data-id="' . $row->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        ';
                    return $btn;
                })
                ->rawColumns(['created_at', 'nama_kategori', 'option'])
                ->make(true);
        }
    }
    public function delete_ujian_kategori(Request $request)
    {
        // dd($request->all());
        try {
            $delete = UjianKategori::where('id', $request->id)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'ujian_kategori',
                'activity' => 'delete',
                'description' => 'menghapus kategori ujian ' . $delete->nama_kategori,
                'keterangan' => 'Success',
            ]);
            $delete->delete();
            return response()->json([
                'code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine(),
            ]);
        }
    }
    public function ujian_kategori_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nama_kategori' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            $id_kategori = Uuid::uuid4();
            UjianKategori::insert([
                'id' => $id_kategori,
                'nama_kategori' => $request->nama_kategori,
                'created_at'    => date('Y-m-d H:i:s'),

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_kategori,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'ujian_kategori',
                'activity' => 'update',
                'description' => 'mengedit kategori ujian ' . $request->nama_kategori,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function ujian_kategori_update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nama_kategori' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            UjianKategori::where('id', $request->id)->update([
                'nama_kategori' => $request->nama_kategori,
                'created_at'    => date('Y-m-d H:i:s'),

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'ujian_kategori',
                'activity' => 'update',
                'description' => 'menambah kategori ujian ' . $request->nama_kategori,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function interview_admin_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'parameter' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            $id_interview_admin = Uuid::uuid4();
            InterviewAdmin::insert([
                'id'        => $id_interview_admin,
                'parameter' => $request->parameter,
                'deskripsi' => $request->deskripsi,
                'created_at' => date('Y-m-d H:i:s'),

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            // dd($st->id, $st->holding_categori);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_interview_admin,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'interview_admin',
                'activity' => 'create',
                'description' => 'Menambah penilaian interview ' . $request->parameter,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    function dt_interview_admin()
    {
        $data = InterviewAdmin::get();
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addColumn('parameter', function ($row) {
                    return $row->parameter;
                })
                ->addColumn('deskripsi', function ($row) {
                    return $row->deskripsi;
                })
                ->addColumn('option', function ($row) {
                    $btn =
                        '<button type="button" id="btn_update_interview"
                            data-id="' . $row->id . '"
                            data-parameter="' . $row->parameter . '"
                            data-deskripsi="' . $row->deskripsi . '"
                            class="btn btn-icon btn-info waves-effect waves-light">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>
                        <button type="button" id="btn_delete_interview"
                            data-id="' . $row->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        ';
                    return $btn;
                })
                ->rawColumns(['created_at', 'parameter', 'deskripsi', 'option'])
                ->make(true);
        }
    }
    public function interview_admin_delete(Request $request)
    {
        try {
            $delete = InterviewAdmin::where('id', $request->id)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            // dd($st->id, $st->holding_categori);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $delete->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'interview_admin',
                'activity' => 'delete',
                'description' => 'menghapus penilaian interview ' . $delete->parameter,
                'keterangan' => 'Success',
            ]);
            $delete->delete();
            return response()->json([
                'code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function interview_admin_update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'parameter' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            InterviewAdmin::where('id', $request->id)->update([
                'parameter' => $request->parameter,
                'deskripsi' => $request->deskripsi,
                'created_at'    => date('Y-m-d H:i:s'),

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            // dd($st->id, $st->holding_categori);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'interview_admin',
                'activity' => 'update',
                'description' => 'mengedit penilaian interview ' . $request->parameter,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    function edit_ujian($kode, $holding, Request $request)
    {
        // dd($ujian);
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $ujian = Ujian::where('kode', $kode)->with([
            'ujianKategori' => function ($query) {
                $query;
            }
        ])->first();
        $detail_ujian = DetailUjian::where('kode', $kode)->get();
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.ujian.data_ujian_edit', [
            'title' => 'Detail Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . url("/public/assets/ew/css/style.css") . '" rel="stylesheet" type="text/css" />
                <script src="' . url("/public/assets/ew/js/examwizard.js") . '"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'ujian' => $ujian,
            'menus' => $menus,
            'detail_ujian' => $detail_ujian,
            'holding' => $holdings,
            'kategori' =>  UjianKategori::get(),
            'pembobotan' =>  Pembobotan::first()
        ]);
    }
    function show_esai(Ujian $ujian, $holding)
    {
        // dd($ujian);
        $holdings = Holding::where('holding_code', $holding)->first();
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.ujian.data_show_esai', [
            'title' => 'Detail Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . url("/public/assets/ew/css/style.css") . '" rel="stylesheet" type="text/css" />
                <script src="' . url("/public/assets/ew/js/examwizard.js") . '"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'ujian' => $ujian,
            'menus' => $menus,
            'holding' => $holdings
        ]);
    }

    function edit_esai($kode, $holding)
    {
        // dd($ujian);
        $holdings = Holding::where('holding_code', $holding)->first();
        $ujian = Ujian::where('kode', $kode)->with([
            'ujianKategori' => function ($query) {
                $query;
            }
        ])->first();
        $detail_esai = DetailEsai::where('kode', $kode)->get();
        // dd($detail_esai);
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.ujian.data_esai_edit', [
            'title' => 'Detail Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . url("/public/assets/ew/css/style.css") . '" rel="stylesheet" type="text/css" />
                <script src="' . url("/public/assets/ew/js/examwizard.js") . '"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'ujian' => $ujian,
            'menus' => $menus,
            'detail_esai' => $detail_esai,
            'holding' => $holdings,
            'kategori' =>  UjianKategori::get(),
            'pembobotan' =>  Pembobotan::first()
        ]);
    }

    function pg_ujian_pg($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.ujian.data_ujian_create', [
            'holding'   => $holdings,
            'title' => 'Tambah Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . asset("/assets/cbt-malela/plugins/file-upload/file-upload-with-preview.min.css") . '" rel="stylesheet" type="text/css" />
                <script src="' . asset("/assets/cbt-malela/plugins/file-upload/file-upload-with-preview.min.js") . '"></script>
                <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'menus' => $menus,
            'kategori' =>  UjianKategori::get(),
            'pembobotan' =>  Pembobotan::first()
        ]);
    }
    function pg_esai_pg($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        return view('admin.recruitment-users.ujian.data_esai_create', [
            'holding'   => $holdings,
            'title' => 'Tambah Ujian Pilihan Ganda',
            'plugin' => '
                <link href="' . asset("/assets/cbt-malela/plugins/file-upload/file-upload-with-preview.min.css") . '" rel="stylesheet" type="text/css" />
                <script src="' . asset("/assets/cbt-malela/plugins/file-upload/file-upload-with-preview.min.js") . '"></script>
                <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
            ',
            'menu' => [
                'menu' => 'ujian',
                'expanded' => 'ujian'
            ],
            'menus' => $menus,
            'kategori' =>  UjianKategori::get(),
            'pembobotan' =>  Pembobotan::first()
        ]);
    }

    function ujian_pg_store(Request $request)
    {
        // dd($request->all());
        $kode = Str::random(30);
        $ujian = [
            'kode' => $kode,
            'esai' => $request->esai,
            'nama' => $request->nama_ujian,
            'jenis' => 0,
            'kategori_id' => $request->kategori_id,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'pembobotan_id' => $request->pembobotan_id,
            'acak' => $request->acak,
            'nol' => $request->nol,
            'satu' => $request->satu,
            'dua' => $request->dua,
            'tiga' => $request->tiga,
            'empat' => $request->empat,
            'lima' => $request->lima,
            'enam' => $request->enam,
            'soal_tampil' => $request->soal_tampil,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $detail_ujian = [];
        $index = 0;
        $nama_soal =  $request->soal;
        foreach ($nama_soal as $soal) {
            array_push($detail_ujian, [
                'kode' => $kode,
                'soal' => $soal,
                'pg_1' => $request->pg_1[$index],
                'pg_2' => $request->pg_2[$index],
                'pg_3' => $request->pg_3[$index],
                'pg_4' => $request->pg_4[$index],
                'pg_5' => $request->pg_5[$index],
                'jawaban' => $request->jawaban[$index]
            ]);
            $index++;
        }
        // dd($ujian);
        Ujian::create($ujian);
        DetailUjian::insert($detail_ujian);

        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        // dd($st->id, $st->holding_categori);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => 'Kode: ' . $kode,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'ujian dan ujian_detail',
            'activity' => 'create',
            'description' => 'Membuat ujian pilihan ganda baru ' . $request->nama_ujian,
            'keterangan' => 'Success',
        ]);

        return redirect('pg-data-ujian/' . $request->holding)->with('success', 'Ujian berhasil dibuat');
    }
    function ujian_pg_update(Request $request)
    {
        // dd($request->all());
        $ujian = [
            'nama' => $request->nama_ujian,
            'jenis' => 0,
            'pembobotan_id' => $request->pembobotan_id,
            'kategori_id' => $request->kategori_id,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'acak' => $request->acak,
            'nol' => $request->nol,
            'satu' => $request->satu,
            'dua' => $request->dua,
            'tiga' => $request->tiga,
            'empat' => $request->empat,
            'lima' => $request->lima,
            'enam' => $request->enam,
            'soal_tampil' => $request->soal_tampil,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $get_detail = DetailUjian::where('kode', $request->kode)->get();
        foreach ($get_detail as $detail) {
            DetailUjian::where('id', $detail->id)->update([
                // $t = $request->soal[$request->id_detail_soal],
                'soal' =>  $request->soal[$detail->id],
                'pg_1' =>  $request->pg_1[$detail->id],
                'pg_2' =>  $request->pg_2[$detail->id],
                'pg_3' =>  $request->pg_3[$detail->id],
                'pg_4' =>  $request->pg_4[$detail->id],
                'pg_5' =>  $request->pg_5[$detail->id],
            ]);
            // dd($t);
        }
        // WaktuUjian::insert($waktu_ujian);
        Ujian::where('id', $request->id_soal)->update($ujian);

        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        // dd($st->id, $st->holding_categori);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => 'Kode: ' . $request->kode,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'ujian dan ujian_detail',
            'activity' => 'update',
            'description' => 'mengedit ujian pilihan ganda ' . $request->nama_ujian,
            'keterangan' => 'Success',
        ]);
        return redirect('pg-data-ujian/' . $request->holding)->with('success', 'Ujian berhasil dibuat');
    }
    function esai_pg_store(Request $request)
    {
        // dd($request->all());
        $kode = Str::random(30);
        $ujian = [
            'kode' => $kode,
            'esai' => $request->esai,
            'nama' => $request->nama_ujian,
            'jenis' => 0,
            'pembobotan_id' => $request->pembobotan_id,
            'kategori_id' => $request->kategori_id,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'acak' => $request->acak,
            'nol' => $request->nol,
            'satu' => $request->satu,
            'dua' => $request->dua,
            'tiga' => $request->tiga,
            'empat' => $request->empat,
            'lima' => $request->lima,
            'enam' => $request->enam,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $detail_ujian = [];
        $index = 0;
        $nama_soal =  $request->soal;
        foreach ($nama_soal as $soal) {
            array_push($detail_ujian, [
                'kode' => $kode,
                'soal' => $soal,
            ]);
            $index++;
        }
        Ujian::insert($ujian);
        DetailEsai::insert($detail_ujian);
        // WaktuUjian::insert($waktu_ujian);

        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        // dd($st->id, $st->holding_categori);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => 'Kode: ' . $kode,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'ujian dan ujian_esai_detail',
            'activity' => 'create',
            'description' => 'menambah ujian esai ' . $request->nama_ujian,
            'keterangan' => 'Success',
        ]);
        return redirect('pg-data-ujian/' . $request->holding)->with('success', 'Ujian berhasil dibuat');
        return redirect('pg-data-ujian/' . $request->holding)->with('success', 'Ujian berhasil dibuat');
    }
    function esai_pg_update(Request $request)
    {
        // dd($request->all());
        $ujian = [
            'esai' => $request->esai,
            'nama' => $request->nama_ujian,
            'jenis' => 0,
            'pembobotan_id' => $request->pembobotan_id,
            'kategori_id' => $request->kategori_id,
            'jam' => $request->jam,
            'menit' => $request->menit,
            'nol' => $request->nol,
            'satu' => $request->satu,
            'dua' => $request->dua,
            'tiga' => $request->tiga,
            'empat' => $request->empat,
            'lima' => $request->lima,
            'enam' => $request->enam,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $get_detail = DetailEsai::where('kode', $request->kode)->get();
        foreach ($get_detail as $detail) {
            DetailEsai::where('id', $detail->id)->update([
                // $t = $request->soal[$request->id_detail_soal];
                'soal' =>  $request->soal_update[$detail->id]
            ]);
            // dd($t);
        }
        Ujian::where('id', $request->id_soal)->update($ujian);

        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => 'Kode: ' . $request->kode,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'ujian dan ujian_esai_detail',
            'activity' => 'update',
            'description' => 'mengedit ujian esai ' . $request->nama_ujian,
            'keterangan' => 'Success',
        ]);
        return redirect('pg-data-ujian/' . $request->holding)->with('success', 'Ujian berhasil dibuat');
    }
    public function dt_referensi()
    {
        $data = RecruitmentReferensi::get();
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addColumn('alamat', function ($row) {
                    return $row->alamat;
                })
                ->addColumn('tempat_link', function ($row) {
                    return $row->tempat_link;
                })
                ->addColumn('option', function ($row) {
                    return $btn =
                        '<button type="button" id="btn_edit_referensi"
                            data-id_referensi="' . $row->id . '"
                            data-alamat="' . $row->alamat . '"
                            data-tempat_link="' . $row->tempat_link . '"
                            class="btn btn-icon btn-info waves-effect waves-light">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                        </button>
                        <button type="button" id="btn_delete_referensi"
                            data-id_referensi="' . $row->id . '"
                            class="btn btn-icon btn-danger waves-effect waves-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                        ';
                    return $btn;;
                })
                ->rawColumns(['asal', 'tempat_link', 'option'])
                ->make(true);
        }
    }
    public function referensi_add(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'alamat' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            $id_referensi = Uuid::uuid4();
            RecruitmentReferensi::create([
                'id' => $id_referensi,
                'alamat' => $request->alamat,
                'tempat_link' => $request->tempat_link,
                'created_at'    => date('Y-m-d H:i:s'),

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $id_referensi,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'recruitment_referensi',
                'activity' => 'create',
                'description' => 'menambah referensi pelamar: ' . $request->alamat,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function referensi_update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'alamat' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong'
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
            RecruitmentReferensi::where('id', $request->id_referensi)->update([
                'alamat' => $request->alamat,
                'tempat_link' => $request->tempat_link,
                'created_at'    => date('Y-m-d H:i:s'),

            ]);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_referensi,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'recruitment_referensi',
                'activity' => 'update',
                'description' => 'mengedit referensi pelamar: ' . $request->alamat,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data' => $get_data,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function delete_referensi(Request $request)
    {
        try {
            $delete = RecruitmentReferensi::where('id', $request->id)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_referensi,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'recruitment_referensi',
                'activity' => 'delete',
                'description' => 'menghapus referensi pelamar: ' . $delete->alamat,
                'keterangan' => 'Success',
            ]);
            $delete->delete();
            return response()->json([
                'code' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }

    function pg_ranking($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holdings = Holding::where('holding_code', $holding)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
        //kadaluarsa diterima langsung
        // $get_recruitment_user_id1 = RecruitmentUser::whereDate('konfirmasi_diterima', '<', date('Y-m-d H:i:s'))
        //     ->where('konfirmasi_diterima', '!=', null)
        //     ->where('feedback_lanjutan', null)
        //     ->get();
        // if ($get_recruitment_user_id1 != null) {
        //     foreach ($get_recruitment_user_id1 as $ii) {
        //         RecruitmentUser::where('id', $ii->id)->update([
        //             'status_lanjutan'        => '3b',
        //         ]);
        //         RecruitmentUserRecord::insert([
        //             'id' => Uuid::uuid4(),
        //             'recruitment_user_id' => $ii->id,
        //             'status' => '3b',
        //             'created_at' => date('Y-m-d H:i:s'),

        //         ]);
        //     }
        // }
        //kadaluarsa diterima langsung end
        //kadaluarsa diterima wawancara manager
        // $get_recruitment_user_id2 = RecruitmentUser::whereDate('tanggal_konfirmasi_manager', '<', date('Y-m-d H:i:s'))
        //     ->where('tanggal_konfirmasi_manager', '!=', null)
        //     ->where('feedback_lanjutan', null)
        //     ->get();
        // if ($get_recruitment_user_id2 != null) {
        //     foreach ($get_recruitment_user_id2 as $ii) {
        //         RecruitmentUser::where('id', $ii->id)->update([
        //             'status_lanjutan'        => '3b',
        //         ]);
        //         RecruitmentUserRecord::insert([
        //             'id' => Uuid::uuid4(),
        //             'recruitment_user_id' => $ii->id,
        //             'status' => '3b',
        //         ]);
        //     }
        // }
        $departemen = Departemen::where('holding', $holdings->id)->orderBy('nama_departemen', 'ASC')->get();
        // dd($get_recruitment_user_id1, $get_recruitment_user_id2);
        //kadaluarsa diterima wawancara manager end
        $roleId = Auth::user();
        return view('admin.recruitment-users.ranking.data_rankinginterview', [
            // return view('karyawan.index', [
            'title'         => 'Data Ranking',
            'menus'         => $menus,
            'holding'       => $holdings,
            'departemen'    => $departemen
        ]);
    }

    function dt_data_ranking(Request $request, $holding)
    {
        $holdings = Holding::where('holding_code', $holding)->first();
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $query =  Recruitment::with([
            'Jabatan' => function ($query) {
                $query->with([
                    'Bagian' => function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            },
        ])
            ->where('holding_recruitment', $holdings->id)
            ->whereBetween('created_recruitment', [$now, $now1]);
        if (!empty($request->departemen_filter)) {
            $query->whereIn('nama_dept', (array)$request->departemen_filter ?? []);
        }

        if (!empty($request->divisi_filter)) {
            $query->whereIn('nama_divisi', (array)$request->divisi_filter ?? []);
        }

        if (!empty($request->bagian_filter)) {
            $query->whereIn('nama_bagian', (array)$request->bagian_filter ?? []);
        }

        if (!empty($request->jabatan_filter)) {
            $query->whereIn('nama_jabatan', (array)$request->jabatan_filter ?? []);
        }
        if (!empty($request->status_filter)) {
            $query->whereHas('lastUserRecord', function ($query) use ($request) {
                $query->whereIn('status', (array)$request->status_filter ?? []);
            });
        }
        $table = $query->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_recruitment)->format('d-m-Y');
                })
                ->addColumn('legal_number', function ($row) {
                    if ($row->Jabatan == NULL) {
                        $legal_number = NULL;
                    } else {
                        $legal_number = $row->legal_number;
                    }
                    return $legal_number;
                })
                ->addColumn('nama_jabatan', function ($row) {
                    if ($row->Jabatan == NULL) {
                        $jabatan = NULL;
                    } else {
                        $jabatan = $row->Jabatan->nama_jabatan;
                    }
                    return $jabatan;
                })
                ->addColumn('nama_departemen', function ($row) {
                    if ($row->Jabatan->Bagian == NULL) {
                        $nama_departemen = NULL;
                    } else {
                        $nama_departemen = $row->Jabatan->Bagian->Divisi->Departemen->nama_departemen;
                    }
                    return $nama_departemen;
                })
                ->addColumn('nama_divisi', function ($row) {
                    if ($row->Jabatan->Bagian == NULL) {
                        $nama_divisi = NULL;
                    } else {
                        $nama_divisi = $row->Jabatan->Bagian->Divisi->nama_divisi;
                    }
                    return $nama_divisi;
                })
                ->addColumn('nama_bagian', function ($row) {
                    if ($row->Jabatan->Bagian == NULL) {
                        $nama_bagian = NULL;
                    } else {
                        $nama_bagian = $row->Jabatan->Bagian->nama_bagian;
                    }
                    return $nama_bagian;
                })
                ->addColumn('pelamar', function ($row) use ($holding) {
                    $url = url('/pg-data-recruitment/data-list-ranking/' . $row->id . '/' . $holding);
                    $btn = '<a href="' . $url . '" class="btn btn-sm btn-primary">
                                <i class="tf-icons mdi mdi-podium-gold me-1"></i>
                                &nbsp;List&nbsp;Ranking
                            </a>';
                    return $btn;
                })
                ->rawColumns([
                    'created_at',
                    'legal_number',
                    'nama_jabatan',
                    'nama_departemen',
                    'nama_divisi',
                    'nama_bagian',
                    'pelamar'
                ])
                ->make(true);
        }
    }

    function pg_list_ranking($id, $holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $currentDate = date('Y-m-d');
        $recruitment_admin = Recruitment::with([
            'Jabatan' => function ($query) {
                $query->with([
                    'Bagian' =>  function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                                $query->orderBy('nama_divisi', 'ASC');
                            },
                        ]);
                    },
                ]);
            },

        ])
            ->where('end_recruitment', '>=', $currentDate)
            ->get();
        $kuota = Recruitment::where('id', $id)->with([
            'Jabatan' => function ($query) {
                $query->with([
                    'Bagian' =>  function ($query) {
                        $query->with([
                            'Divisi' => function ($query) {
                                $query->with([
                                    'Departemen' => function ($query) {
                                        $query->orderBy('nama_departemen', 'ASC');
                                    }
                                ]);
                                $query->orderBy('nama_divisi', 'ASC');
                            },
                        ]);
                    },
                ]);
            },

        ])->first();
        $holdings = Holding::where('holding_code', $holding)->first();
        $holding = request()->segment(count(request()->segments()));
        $site = Site::get();
        // dd($kuota);
        $roleId = Auth::user();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
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
            // dd($menus);
        }
        return view('admin.recruitment-users.ranking.data_listranking', [
            'title'                 => 'Data Recruitment',
            'holding'               => $holdings,
            'recruitment_admin'     => $recruitment_admin,
            'id_recruitment'        => $id,
            'kuota'                 => $kuota,
            'menus'                 => $menus,
            'data_departemen'       => Departemen::all(),
            'data_bagian'           => Bagian::with('Divisi')->where('holding', $holding)->get(),
            'data_dept'             => Departemen::orderBy('nama_departemen', 'asc')->where('holding', $holding)->get(),
            'data_divisi'           => Divisi::orderBy('nama_divisi', 'asc')->where('holding', $holding)->get()
        ], compact('site'));
    }
    function dt_list_ranking($id)
    {
        $table = RecruitmentUser::with([
            'Cv' => function ($query) {
                $query;
            }
        ])->with([
            'interviewUser' => function ($query) {
                $query;
            }
        ])->with([
            'DataInterview' => function ($query) {
                $query;
            }
        ])
            ->with([
                'ujianEsaiJawab' => function ($query) {
                    $query->orderBy('recruitment_user_id')->with([
                        'ujian' => function ($query) {
                            $query->with([
                                'pembobotan' => function ($query) {
                                    $query;
                                }
                            ]);
                        }
                    ]);
                }
            ])->with([
                'waktuujian' => function ($query) {
                    $query->orderBy('recruitment_user_id')->with([
                        'ujian' => function ($query) {
                            $query->with([
                                'pembobotan' => function ($query) {
                                    $query;
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->with([
                'recruitmentAdmin' => function ($query) {
                    $query;
                }
            ])
            ->with([
                'Jabatan' => function ($query) {
                    $query->with([
                        'Bagian' =>  function ($query) {
                            $query->with([
                                'Divisi' => function ($query) {
                                    $query->with([
                                        'Departemen' => function ($query) {
                                            $query->orderBy('nama_departemen', 'ASC');
                                        }
                                    ]);
                                    $query->orderBy('nama_divisi', 'ASC');
                                },
                            ]);
                        },
                    ])->with([
                        'LevelJabatan' => function ($query) {
                            $query;
                        },
                    ]);
                },

            ])
            ->where('recruitment_admin_id', $id)
            ->whereNotNull('feedback')
            ->get();

        // dd($table);

        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->Cv->nama_lengkap;
                })
                ->addColumn('total_koefisien', function ($row) {
                    $esai_total = $row->ujianEsaiJawab->sum('nilai') ?? 0;
                    $pg_total = $row->waktuujian->sum('nilai') ?? 0;
                    $get_jabatan = $row->Jabatan->LevelJabatan->level_jabatan;
                    if ($get_jabatan == 0) {
                        $esai_count = Ujian::where('esai', 1)->where('nol', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('nol', '1')->count();
                    } elseif ($get_jabatan == 1) {
                        $esai_count = Ujian::where('esai', 1)->where('satu', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('satu', '1')->count();
                    } elseif ($get_jabatan == 2) {
                        $esai_count = Ujian::where('esai', 1)->where('dua', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('dua', '1')->count();
                    } elseif ($get_jabatan == 3) {
                        $esai_count = Ujian::where('esai', 1)->where('tiga', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('tiga', '1')->count();
                    } elseif ($get_jabatan == 4) {
                        $esai_count = Ujian::where('esai', 1)->where('empat', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('empat', '1')->count();
                    } elseif ($get_jabatan == 5) {
                        $esai_count = Ujian::where('esai', 1)->where('lima', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('lima', '1')->count();
                    } elseif ($get_jabatan == 6) {
                        $esai_count = Ujian::where('esai', 1)->where('enam', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('enam', '1')->count();
                    }
                    $interview_user = $row->interviewUser->sum('nilai') ?? 0;
                    $interview_admin = InterviewUser::where('recruitment_user_id', $row->id)->count() ?? 0;
                    $get_bobot = Pembobotan::first();
                    try {
                        $koefisien_esai = ($esai_total / $esai_count) * ($get_bobot->esai / 100);
                        $koefisien_pg = ($pg_total / $pg_count) * ($get_bobot->pilihan_ganda / 100);
                        $koefisien_interview = ($interview_user / $interview_admin * 10) * ($get_bobot->interview / 100);
                    } catch (DivisionByZeroError $e) {
                        $koefisien_esai = 0;
                        $koefisien_pg = 0;
                        $koefisien_interview = 0;
                    }


                    return round($koefisien_esai + $koefisien_pg + $koefisien_interview, 2);
                })
                ->addColumn('esai_average', function ($row) {

                    $esai_total = $row->ujianEsaiJawab->sum('nilai') ?? 0;
                    $get_jabatan = $row->Jabatan->LevelJabatan->level_jabatan;
                    if ($get_jabatan == 0) {
                        $esai_count = Ujian::where('esai', 1)->where('nol', '1')->count();
                    } elseif ($get_jabatan == 1) {
                        $esai_count = Ujian::where('esai', 1)->where('satu', '1')->count();
                    } elseif ($get_jabatan == 2) {
                        $esai_count = Ujian::where('esai', 1)->where('dua', '1')->count();
                    } elseif ($get_jabatan == 3) {
                        $esai_count = Ujian::where('esai', 1)->where('tiga', '1')->count();
                    } elseif ($get_jabatan == 4) {
                        $esai_count = Ujian::where('esai', 1)->where('empat', '1')->count();
                    } elseif ($get_jabatan == 5) {
                        $esai_count = Ujian::where('esai', 1)->where('lima', '1')->count();
                    } elseif ($get_jabatan == 6) {
                        $esai_count = Ujian::where('esai', 1)->where('enam', '1')->count();
                    }
                    return round($esai_total / $esai_count, 2);
                })

                ->addColumn('bobot_esai', function ($row) {
                    $get_bobot = Pembobotan::first();
                    $bobot = $get_bobot->esai;
                    return $bobot . '%';
                })
                ->addColumn('pg_average', function ($row) {

                    $pg_total = $row->waktuujian->sum('nilai') ?? 0;
                    $get_jabatan = $row->Jabatan->LevelJabatan->level_jabatan;
                    if ($get_jabatan == 0) {
                        $pg_count = Ujian::where('esai', 0)->where('nol', '1')->count();
                    } elseif ($get_jabatan == 1) {
                        $pg_count = Ujian::where('esai', 0)->where('satu', '1')->count();
                    } elseif ($get_jabatan == 2) {
                        $pg_count = Ujian::where('esai', 0)->where('dua', '1')->count();
                    } elseif ($get_jabatan == 3) {
                        $pg_count = Ujian::where('esai', 0)->where('tiga', '1')->count();
                    } elseif ($get_jabatan == 4) {
                        $pg_count = Ujian::where('esai', 0)->where('empat', '1')->count();
                    } elseif ($get_jabatan == 5) {
                        $pg_count = Ujian::where('esai', 0)->where('lima', '1')->count();
                    } elseif ($get_jabatan == 6) {
                        $pg_count = Ujian::where('esai', 0)->where('enam', '1')->count();
                    }
                    return round($pg_total / $pg_count, 2);
                })
                ->addColumn('bobot_pg', function ($row) {
                    $get_bobot = Pembobotan::first();
                    $bobot = $get_bobot->pilihan_ganda;
                    return $bobot . '%';
                })
                ->addColumn('interview_average', function ($row) {

                    try {
                        $interview_user = $row->interviewUser->sum('nilai') ?? 0;
                        $interview_admin = InterviewUser::where('recruitment_user_id', $row->id)->count() ?? 0;
                        $hasil = $interview_user / $interview_admin;
                    } catch (DivisionByZeroError $e) {
                        $hasil = 0;
                    }
                    return round($hasil * 10, 2);
                })
                ->addColumn('bobot_interview', function ($row) {
                    $get_bobot = Pembobotan::first();
                    $bobot = $get_bobot->interview;
                    return $bobot . '%';
                })

                ->rawColumns(['nama_lengkap', 'total_koefisien', 'esai_average', 'bobot_esai', 'pg_average', 'bobot_pg', 'interview_average', 'bobot_interview'])
                ->make(true);
        }
    }
    function dt_list_progres($id)
    {
        $table = RecruitmentUser::with([
            'Cv' => function ($query) {
                $query;
            }
        ])->with([
            'interviewUser' => function ($query) {
                $query;
            }
        ])->with([
            'DataInterview' => function ($query) {
                $query;
            }
        ])
            ->with([
                'ujianEsaiJawab' => function ($query) {
                    $query->orderBy('recruitment_user_id')->with([
                        'ujian' => function ($query) {
                            $query->with([
                                'pembobotan' => function ($query) {
                                    $query;
                                }
                            ]);
                        }
                    ]);
                }
            ])->with([
                'waktuujian' => function ($query) {
                    $query->orderBy('recruitment_user_id')->with([
                        'ujian' => function ($query) {
                            $query->with([
                                'pembobotan' => function ($query) {
                                    $query;
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->with([
                'recruitmentAdmin' => function ($query) {
                    $query;
                }
            ])
            ->with([
                'Jabatan' => function ($query) {
                    $query->with([
                        'Bagian' =>  function ($query) {
                            $query->with([
                                'Divisi' => function ($query) {
                                    $query->with([
                                        'Departemen' => function ($query) {
                                            $query->orderBy('nama_departemen', 'ASC');
                                        }
                                    ]);
                                    $query->orderBy('nama_divisi', 'ASC');
                                },
                            ]);
                        },
                    ])->with([
                        'LevelJabatan' => function ($query) {
                            $query;
                        },
                    ]);
                },

            ])
            ->where('recruitment_admin_id', $id)
            ->whereNotNull('feedback')
            ->get();

        // dd($table);

        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->Cv->nama_lengkap;
                })
                ->addColumn('pilih_status', function ($row) {
                    if ($row->status_lanjutan == null) {
                        return '<div class="d-flex justify-content-center gap-2">
                                    <button data-id="' . $row->id . '" type="button" 
                                        class="btn btn-sm btn-info d-flex align-items-center justify-content-center flex-grow-1" id="btn_status_ranking">
                                        <i class="tf-icons mdi mdi-check-circle-outline me-1"></i>
                                        <span>Pilih</span>
                                    </button>
                                    
                                    <button data-id="' . $row->id . '" type="button" 
                                        class="btn btn-sm btn-danger d-flex align-items-center justify-content-center flex-grow-1" id="btn_pemindahan">
                                        <i class="tf-icons mdi mdi-swap-horizontal me-1"></i>
                                        <span>Pindah</span>
                                    </button>
                                </div>';
                    } elseif ($row->status_lanjutan == '4b') {
                        return '<div class="d-flex justify-content-center gap-2">
                                    <button
                                        data-id="' . $row->id . '"
                                        type="button" class="btn btn-sm btn-success d-flex align-items-center justify-content-center flex-grow-1" id="btn_lolos">
                                        <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                        <span>Lolos&nbsp;</span>
                                    </button>
                                </div>';
                    } elseif ($row->status_lanjutan == '5b') {
                        return   '
                                    <div class="d-flex justify-content-center gap-2">
                                        <button
                                            data-id="' . $row->id . '"
                                            type="button" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center flex-grow-1" id="btn_pemindahan">
                                            <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                            <span>Pindah&nbsp;</span>
                                        </button>
                                        
                                        <button
                                            data-id="' . $row->id . '"
                                            type="button" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center flex-grow-1" id="btn_tidak_lolos">
                                            <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                            <span>Tolak&nbsp;</span>
                                        </button>
                                    </div>';
                    } elseif ($row->status_lanjutan == '2b' && $row->feedback_lanjutan == '2b') {
                        return   '<button
                                data-id="' . $row->id . '"
                                type="button" class="btn btn-sm btn-success d-flex align-items-center justify-content-center flex-grow-1" id="btn_integrasi">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                <span>Masukkan&nbspDatabase&nbspKaryawan</span>
                            </button>';
                    } elseif ($row->status_lanjutan == '7b' && $row->feedback_lanjutan == '2b') {
                        return   '<button
                                data-id="' . $row->id . '"
                                type="button" class="btn btn-sm btn-success d-flex align-items-center justify-content-center flex-grow-1" id="btn_integrasi">
                                <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                                <span>Masukkan&nbspDatabase&nbspKaryawan</span>
                            </button>';
                        // } elseif ($row->status_lanjutan == '2b' && $row->feedback_lanjutan == '3b') {
                        //     return   '<button
                        //                     data-id="' . $row->id . '"
                        //                     type="button" class="btn btn-sm btn-success " id="btn_lolos">
                        //                     <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                        //                     Lolos&nbsp;
                        //                 </button>';
                        // } elseif ($row->status_lanjutan == '4b' && $row->feedback_lanjutan == '3b') {
                        //     return   '<button
                        //                     data-id="' . $row->id . '"
                        //                     type="button" class="btn btn-sm btn-success " id="btn_lolos">
                        //                     <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                        //                     Lolos&nbsp;
                        //                 </button>';
                        // } elseif ($row->status_lanjutan == '7b' && $row->feedback_lanjutan == '3b') {
                        //     return   '<button
                        //                     data-id="' . $row->id . '"
                        //                     type="button" class="btn btn-sm btn-success " id="btn_lolos">
                        //                     <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                        //                     Lolos&nbsp;
                        //                 </button>';
                        // } elseif ($row->status_lanjutan == '3b') {
                        //     return   '<button
                        //                     data-id="' . $row->id . '"
                        //                     type="button" class="btn btn-sm btn-success " id="btn_lolos">
                        //                     <i class="tf-icons mdi mdi-eye-circle-outline me-1"></i>
                        //                     Lolos&nbsp;
                        //                 </button>';
                        // } else {
                        return '-';
                    }
                })
                ->addColumn('status', function ($row) {
                    if ($row->status_lanjutan == null) {
                        return '<span class="badge bg-label-secondary">Belum Ditentukan</span>';
                    } elseif ($row->status_lanjutan == '1b') {
                        return '<span class="badge bg-label-warning">Interview Manager</span>';
                    } elseif ($row->status_lanjutan == '2b') {
                        return '<span class="badge bg-label-success">Diterima Bekerja</span>';
                    } elseif ($row->status_lanjutan == '3b') {
                        return '<span class="badge bg-label-danger">Tidak Lolos</span>';
                    } elseif ($row->status_lanjutan == '4b') {
                        return '<span class="badge bg-label-warning">Lolos Interview Manager</span>';
                    } elseif ($row->status_lanjutan == '5b') {
                        return '<span class="badge bg-label-danger">Ditolak Manager</span>';
                    } elseif ($row->status_lanjutan == '6b') {
                        return '<span class="badge bg-label-warning">Perubahan Posisi Lowongan</span>';
                    } elseif ($row->status_lanjutan == '7b') {
                        return '<span class="badge bg-label-success">Lolos Posisi Lain</span>';
                    } elseif ($row->status_lanjutan == '8b') {
                        return '<span class="badge bg-label-info">Ditetapkan Sebagai Karyawan</span>';
                    }
                })
                ->addColumn('total_koefisien', function ($row) {
                    $esai_total = $row->ujianEsaiJawab->sum('nilai') ?? 0;
                    $pg_total = $row->waktuujian->sum('nilai') ?? 0;
                    $get_jabatan = $row->Jabatan->LevelJabatan->level_jabatan;
                    if ($get_jabatan == 0) {
                        $esai_count = Ujian::where('esai', 1)->where('nol', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('nol', '1')->count();
                    } elseif ($get_jabatan == 1) {
                        $esai_count = Ujian::where('esai', 1)->where('satu', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('satu', '1')->count();
                    } elseif ($get_jabatan == 2) {
                        $esai_count = Ujian::where('esai', 1)->where('dua', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('dua', '1')->count();
                    } elseif ($get_jabatan == 3) {
                        $esai_count = Ujian::where('esai', 1)->where('tiga', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('tiga', '1')->count();
                    } elseif ($get_jabatan == 4) {
                        $esai_count = Ujian::where('esai', 1)->where('empat', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('empat', '1')->count();
                    } elseif ($get_jabatan == 5) {
                        $esai_count = Ujian::where('esai', 1)->where('lima', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('lima', '1')->count();
                    } elseif ($get_jabatan == 6) {
                        $esai_count = Ujian::where('esai', 1)->where('enam', '1')->count();
                        $pg_count = Ujian::where('esai', 0)->where('enam', '1')->count();
                    }

                    $interview_user = $row->interviewUser->sum('nilai') ?? 0;
                    $interview_admin = InterviewAdmin::count() ?? 0;
                    $get_bobot = Pembobotan::first();
                    try {
                        $koefisien_esai = ($esai_total / $esai_count) * ($get_bobot->esai / 100);
                        $koefisien_pg = ($pg_total / $pg_count) * ($get_bobot->pilihan_ganda / 100);
                        $koefisien_interview = ($interview_user / $interview_admin * 10) * ($get_bobot->interview / 100);
                    } catch (DivisionByZeroError $e) {
                        $koefisien_esai = 0;
                        $koefisien_pg = 0;
                        $koefisien_interview = 0;
                    }
                    return round($koefisien_esai + $koefisien_pg + $koefisien_interview, 2);
                })->addColumn('feedback', function ($row) {
                    if ($row->feedback_lanjutan == '1b') {
                        return '<span class="badge bg-label-warning">Menyanggupi</span>';
                    } elseif ($row->feedback_lanjutan == '2b') {
                        return '<span class="badge bg-label-success">Menyanggupi</span>';
                    } elseif ($row->feedback_lanjutan == '3b') {
                        return '<span class="badge bg-label-danger">Menolak</span>';
                    } elseif ($row->feedback_lanjutan == '4b') {
                        return '<span class="badge bg-label-warning">Menerima</span>';
                    }
                })
                ->addColumn('alasan_lanjutan', function ($row) {
                    return $row->alasan_lanjutan;
                })
                ->addColumn('catatan', function ($row) {
                    if ($row->DataInterview && $row->DataInterview->catatan) {
                        return '<button id="btn_catatan"
                            data-id_catatan="' . $row->id . '"
                            data-catatan="' . $row->DataInterview->catatan . '"
                            type="button" class="btn btn-sm btn-warning ">
                            <i class="tf-icons mdi mdi-pencil-box-multiple"> </i>
                        </button>';
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['nama_lengkap', 'pilih_status', 'status', 'total_koefisien', 'feedback', 'alasan_lanjutan', 'catatan'])
                ->make(true);
        }
    }
}
