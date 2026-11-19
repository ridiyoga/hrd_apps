<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\InterviewUser;
use App\Models\Menu;
use App\Models\PgSiswa;
use App\Models\Recruitment;
use App\Models\RecruitmentCV;
use App\Models\RecruitmentInterview;
use App\Models\RecruitmentUser;
use App\Models\RoleUsers;
use App\Models\Ujian;
use App\Models\UjianEsaiJawab;
use App\Models\UjianEsaiJawabDetail;
use App\Models\User;
use App\Models\UserCareer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use PDF;


class UjianUserController extends Controller
{
    //Pilihan Ganda
    public function dt_ujian_pg($id)
    {
        $get_jabatan = RecruitmentUser::where('id', $id)->with([
            'Jabatan' => function ($query) {
                $query->with([
                    'LevelJabatan' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])->with([
            'Cv' => function ($query) {
                $query;
            }
        ])
            ->first();
        if ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '0') {
            $ujian = Ujian::where('nol', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '1') {
            $ujian = Ujian::where('satu', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '2') {
            $ujian = Ujian::where('dua', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '3') {
            $ujian = Ujian::where('tiga', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '4') {
            $ujian = Ujian::where('empat', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
            // dd($ujian, $ujian);
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '5') {
            $ujian = Ujian::where('lima', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '6') {
            $ujian = Ujian::where('enam', '1')->where('esai', '0')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'waktuujian' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        }
        // dd($ujian, $id);
        if (request()->ajax()) {
            return DataTables::of($ujian)
                ->addColumn('nama', function ($row) {
                    return $row->nama;
                })
                ->addColumn('kategori', function ($row) {
                    return $row->ujianKategori->nama_kategori;
                })
                ->addColumn('jawaban', function ($row) use ($id) {
                    $holding = request()->segment(count(request()->segments()));
                    if ($row->waktuujian == null) {
                        return '<button class="btn p-2 btn-sm btn-info" disabled><small>belum dikerjakan</small></button>';
                    } else {
                        return '<a href="' . url("/pg-data-interview/dt/data-data_ujian_user/data-get_pg_interview/$row->kode/$id/$holding") . '" type="button" class="btn p-2 btn-sm btn-info"><small><span class="mdi mdi-forum"></span>&nbsp;Jawaban</small></a>';
                    }
                })
                ->addColumn('pdf', function ($row) use ($id) {
                    $holding = request()->segment(count(request()->segments()));
                    if ($row->waktuujian == null) {
                        return '<button class="btn p-2 btn-sm btn-info" disabled><small>PDF</small></button>';
                    } else {
                        return '<a href="' . url("/pg-data-interview/dt/data-data_ujian_user/data-get_pg_interview_pdf/$row->kode/$id/$holding") . '" type="button" class="btn p-2 btn-sm btn-danger" target="_blank()"><small>PDF</small></a>';
                    }
                })
                ->addColumn('nilai', function ($row) use ($id) {
                    if ($row->waktuujian == null) {
                        return 'Belum Dikerjakan';
                    } else {
                        return $row->waktuujian->nilai;
                    }
                })
                ->rawColumns([
                    'nama',
                    'kategori',
                    'jawaban',
                    'pdf',
                    'nilai'
                ])
                ->make(true);
        }
    }
    function show_pg($kode, $recruitment_user_id, $holding)
    {
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
        $holdings = Holding::where('holding_code', $holding)->first();
        $ujian = Ujian::where('kode', $kode)->first();
        $PgSiswa = PgSiswa::where('kode', $kode)->where('siswa_id', $recruitment_user_id)->orderBy('id', 'ASC')->limit($ujian->soal_tampil)->get();
        $benar = $PgSiswa->where('benar', '1')->count();
        $salah = $PgSiswa->where('benar', '0')->count();
        $jumlah_soal = $PgSiswa->count();
        $total_nilai = $benar / $jumlah_soal * 100;
        // dd($ujian);

        return view('admin.recruitment-users.interview.pg_interview', [
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
            'holding' => $holdings,
            'PgSiswa' => $PgSiswa,
            'benar' => $benar,
            'menus' => $menus,
            'salah' => $salah,
            'total_nilai' => $total_nilai,
            'recruitment_user_id' => $recruitment_user_id
        ]);
    }
    function show_pg_pdf($kode, $recruitment_user_id, $holding)
    {
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
        $recruitment_user = RecruitmentUser::where('id', $recruitment_user_id)->with('Jabatan')->with('Bagian')->first();
        // dd($recruitment_user);
        $cv = RecruitmentCV::where('users_career_id', $recruitment_user->users_career_id)->first();
        $holdings = Holding::where('holding_code', $holding)->first();
        $ujian = Ujian::where('kode', $kode)->first();
        $PgSiswa = PgSiswa::where('kode', $kode)->where('siswa_id', $recruitment_user_id)->orderBy('id', 'ASC')->limit($ujian->soal_tampil)->get();
        $benar = $PgSiswa->where('benar', '1')->count();
        $salah = $PgSiswa->where('benar', '0')->count();
        $jumlah_soal = $PgSiswa->count();
        $total_nilai = $benar / $jumlah_soal * 100;
        // dd($ujian);

        $data = [
            'title' => 'domPDF in Laravel 10',
            'recruitment_user' => $recruitment_user,
            'cv' => $cv,
            'ujian' => $ujian,
            'holding' => $holdings,
            'PgSiswa' => $PgSiswa,
            'benar' => $benar,
            'menus' => $menus,
            'salah' => $salah,
            'total_nilai' => $total_nilai,
            'recruitment_user_id' => $recruitment_user_id
            // 'logo' => $data['logo'],
            // 'ttd_user' => $data['ttd_pemohon'],
            // 'ttd_atasan' => $data['ttd_atasan'],
            // 'ttd_atasan2' => $data['ttd_atasan2']
        ];
        $pdf = PDF::setOptions([
            'defaultFont' => 'Poppins, sans-serif',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            // Izinkan DomPDF mengakses folder public dan storage demi keamanan jalur gambar
            'chroot' => [public_path(), storage_path()],
        ])
            ->loadView('admin.recruitment-users.interview.pg_interview_pdf', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'UJIAN_' .
                // $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
    //End Pilihan Ganda


    //Esai
    public function dt_ujian_esai($id)
    {
        $get_jabatan = RecruitmentUser::where('id', $id)->with([
            'Jabatan' => function ($query) {
                $query->with([
                    'LevelJabatan' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])->with([
            'Cv' => function ($query) {
                $query;
            }
        ])
            ->first();
        if ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '0') {
            $ujian_esai = Ujian::where('nol', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '1') {
            $ujian_esai = Ujian::where('satu', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '2') {
            $ujian_esai = Ujian::where('dua', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '3') {
            $ujian_esai = Ujian::where('tiga', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '4') {
            $ujian_esai = Ujian::where('empat', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
            // dd($ujian, $ujian_esai);
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '5') {
            $ujian_esai = Ujian::where('lima', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        } elseif ($get_jabatan->Jabatan->LevelJabatan->level_jabatan == '6') {
            $ujian_esai = Ujian::where('enam', '1')->where('esai', '1')->with([
                'ujianKategori' => function ($query) {
                    $query;
                }
            ])->with([
                'esaiJawab' => function ($query) use ($id) {
                    $query->where('recruitment_user_id', $id);
                }
            ])->get();
        }
        // dd($ujian_esai);
        if (request()->ajax()) {
            return DataTables::of($ujian_esai)
                ->addColumn('nama', function ($row) {
                    return $row->nama;
                })
                ->addColumn('kategori', function ($row) {
                    return $row->ujianKategori->nama_kategori;
                })
                ->addColumn('jawaban', function ($row) use ($id) {
                    $holding = request()->segment(count(request()->segments()));
                    // dd($row->esaiJawab);
                    if ($row->esaiJawab == null) {
                        return '<button class="btn p-2 btn-sm btn-info" disabled><small>belum dikerjakan</small></button>';
                    } else {
                        return '<a href="' . url("/pg-data-interview/data-get_esai_interview/$row->kode/$id/$holding") . '" 
                        type="button" class="btn p-2 btn-sm btn-info"><small><span class="mdi mdi-forum"></span>&nbsp;Jawaban</small></a>';
                    }
                })
                ->addColumn('pdf', function ($row) use ($id) {
                    $holding = request()->segment(count(request()->segments()));
                    if ($row->esaiJawab == null) {
                        return '<button class="btn p-2 btn-sm btn-info" disabled><small>PDF</small></button>';
                    } else {
                        return '<a href="' . url("/pg-data-interview/data-get_esai_interview_pdf/$row->kode/$id/$holding") . '" type="button" class="btn p-2 btn-sm btn-danger" target="_blank()"><small>PDF</small></a>';
                    }
                })
                ->addColumn('nilai', function ($row) {
                    if ($row->esaiJawab == null) {
                        return 'belum dikerjakan';
                    } else {
                        if ($row->esaiJawab->nilai == null) {
                            return 'Belum Dinilai';
                        } else {
                            return $row->esaiJawab->nilai;
                        }
                    }
                })
                ->rawColumns([
                    'nama',
                    'kategori',
                    'jawaban',
                    'pdf',
                    'nilai'
                ])
                ->make(true);
        }
    }
    public function dt_interview_user($id)
    {
        $interview_user = InterviewUser::where('recruitment_user_id', $id)->get();
        // dd($interview_user);
        if (request()->ajax()) {
            return DataTables::of($interview_user)
                ->addColumn('parameter', function ($row) {
                    return $row->parameter;
                })
                ->addColumn('deskripsi', function ($row) {
                    return $row->deskripsi;
                })

                ->addColumn('isi_nilai', function ($row) use ($id) {
                    $holding = request()->segment(count(request()->segments()));
                    if ($row == null) {
                        return '<button class="btn p-2 btn-sm btn-info" disabled><small>belum dikerjakan</small></button>';
                    } else {
                        return '<button type="button" data-id="' . $row->id . '" data-nilai="' . $row->nilai . '" class="btn p-2 btn-sm btn-sm btn-info waves-effect waves-light my-3"
                                 id="btn_isi_nilai">ISI NILAI</button>';
                    }
                })

                ->addColumn('nilai', function ($row) {
                    if ($row->nilai == null) {
                        return 'Belum Dinilai';
                    } else {
                        return $row->nilai;
                    }
                })
                ->rawColumns(['penilaian', 'deskripsi', 'nilai', 'isi_nilai'])
                ->make(true);
        }
    }
    public function dt_catatan($id)
    {
        $catatan = RecruitmentInterview::where('recruitment_user_id', $id)->get();
        // dd($catatan);
        if (request()->ajax()) {
            return DataTables::of($catatan)

                ->addColumn('catatan', function ($row) {
                    return $row->catatan;
                })
                ->rawColumns(['catatan'])
                ->make(true);
        }
    }
    function interview_user_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'nilai' => 'required|numeric|min:1|max:10',
            ],
            [
                'required' => ':attribute tidak boleh kosong',
                'max' => ':attribute melebihi yang ditentukan',
                'min' => ':attribute kurang dari yang ditentukan'
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
            InterviewUser::where('id', $request->id)->update([
                'nilai' => $request->nilai,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            $interview_user = InterviewUser::where('id', $request->id)->first();
            $recruitment_user = RecruitmentUser::where('id', $interview_user->recruitment_user_id)->first();
            $holding = Holding::where('id', $recruitment_user->holding)->first();
            $cv = RecruitmentCV::where('users_career_id', $recruitment_user->users_career_id)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            // dd($id_table->id);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'interview_user',
                'activity' => 'update',
                'description' => 'memberi nilai interview ' . $interview_user->parameter . ' nilai ' . $request->nilai . ' pada pelamar ' . $cv->nama_lengkap,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                // 'data_keahlian' => $data_keahlian,
                // 'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . '' . $e->getLine(),
            ]);
        }
    }
    function update_catatan(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'catatan' => 'required',
            ],
            [
                'required' => ':attribute tidak boleh kosong',
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
            RecruitmentInterview::where('recruitment_user_id', $request->recruitment_user_id)->update([
                'catatan' => $request->catatan,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            return response()->json([
                'code' => 200,
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
    public function get_catatan_interview($id)
    {

        try {
            // dd($detail_esai);
            $get_data = RecruitmentInterview::where('recruitment_user_id', $id)->first();
            // dd($detail_esai);
            return response()->json([
                'code' => 200,
                'data' => $get_data,
                'message' => 'Data Berhasil Didapatkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage(),
            ]);
        }
    }
    function show_esai($kode, $recruitment_user_id, $holding)
    {
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
        $holdings = Holding::where('holding_code', $holding)->first();
        $ujian = Ujian::where('kode', $kode)->first();
        $ujianEsaiJawab = UjianEsaiJawab::where('kode', $kode)->where('recruitment_user_id', $recruitment_user_id)->first();
        $esaiDetailjawab = UjianEsaiJawabDetail::where('kode', $kode)->where('recruitment_user_id', $recruitment_user_id)->get();
        // dd($ujian->detailEsai->ujianEsaiJawabDetail->kode, $ujian->detailEsai->ujianEsaiJawabDetail->recruitment_user_id);
        // dd($ujianEsaiJawab);

        return view('admin.recruitment-users.interview.esai_interview', [
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
            'holding' => $holdings,
            'menus' => $menus,
            'ujianEsaiJawab' => $ujianEsaiJawab,
            'esaiDetailjawab' => $esaiDetailjawab,
            'recruitment_user_id' => $recruitment_user_id
        ]);
    }
    function show_esai_pdf($kode, $recruitment_user_id, $holding)
    {
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
        $recruitment_user = RecruitmentUser::where('id', $recruitment_user_id)->with('Jabatan')->with('Bagian')->first();
        $cv = RecruitmentCV::where('users_career_id', $recruitment_user->users_career_id)->first();
        $holdings = Holding::where('holding_code', $holding)->first();
        $ujian = Ujian::where('kode', $kode)->first();
        $ujianEsaiJawab = UjianEsaiJawab::where('kode', $kode)->where('recruitment_user_id', $recruitment_user_id)->first();
        $esaiDetailjawab = UjianEsaiJawabDetail::where('kode', $kode)->where('recruitment_user_id', $recruitment_user_id)->get();
        // dd($esaiDetailjawab);

        $data = [
            'title' => 'domPDF in Laravel 10',
            'recruitment_user' => $recruitment_user,
            'cv' => $cv,
            'ujian' => $ujian,
            'holding' => $holdings,
            'menus' => $menus,
            'ujianEsaiJawab' => $ujianEsaiJawab,
            'esaiDetailjawab' => $esaiDetailjawab,
            'recruitment_user_id' => $recruitment_user_id
            // 'logo' => $data['logo'],
            // 'ttd_user' => $data['ttd_pemohon'],
            // 'ttd_atasan' => $data['ttd_atasan'],
            // 'ttd_atasan2' => $data['ttd_atasan2']
        ];
        $pdf = PDF::setOptions([
            'defaultFont' => 'Poppins, sans-serif',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'chroot' => public_path(), // penting!
        ])
            ->loadView('admin.recruitment-users.interview.esai_interview_pdf', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'UJIAN_' .
                // $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
    function show_interview_user($kode, $recruitment_user_id)
    {
        $holding = request()->segment(count(request()->segments()));
        $ujian = Ujian::where('kode', $kode)->first();
        $ujianEsaiJawab = UjianEsaiJawab::where('kode', $kode)->where('recruitment_user_id', $recruitment_user_id)->first();
        $esaiDetailjawab = UjianEsaiJawabDetail::where('kode', $kode)->where('recruitment_user_id', $recruitment_user_id)->get();
        // dd($ujian->detailEsai->ujianEsaiJawabDetail->kode, $ujian->detailEsai->ujianEsaiJawabDetail->recruitment_user_id);
        // dd($ujianEsaiJawab);

        return view('admin.recruitment-users.interview.interview', [
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
            'holding' => $holding,
            'ujianEsaiJawab' => $ujianEsaiJawab,
            'esaiDetailjawab' => $esaiDetailjawab,
            'recruitment_user_id' => $recruitment_user_id
        ]);
    }
    public function penilaian_esai(Request $request)
    {
        // dd($request->all());
        $rules =
            [
                'nilai'             => 'required|numeric|min:1|max:100',
            ];
        $customessages =
            [
                'required'             => ':attribute tidak boleh kosong!',
                'max'                  => ':attribute tidak boleh lebih dari 100!',
                'min'                  => ':attribute tidak boleh kurang dari 1!',
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

        // $holding = request()->segment(count(request()->segments()));
        // dd($validatedData);

        $recruitment_user = RecruitmentUser::where('id', $request->recruitment_user_id)->first();
        $holding = Holding::where('id', $recruitment_user->holding)->first();
        $cv = RecruitmentCV::where('users_career_id', $recruitment_user->users_career_id)->first();
        $nama_menu = Menu::where('id', $request->menu_id)->first();
        $user_name = User::where('id', $request->user()->id)->first();
        $id_table = UjianEsaiJawab::where('recruitment_user_id', $request->recruitment_user_id)->where('kode', $request->kode)->first();
        // dd($id_table->id);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $user_name->username,
            'menu_id' => $request->menu_id,
            'menu_name' => $nama_menu->name,
            'id_table' => $id_table->id,
            'holding_id' => $holding->id,
            'holding_category' => $holding->holding_category,
            'table' => 'ujian_esai_jawab',
            'activity' => 'update',
            'description' => 'memberi nilai esai ' . $request->nilai . ' pada pelamar ' . $cv->nama_lengkap,
            'keterangan' => 'Success',
        ]);
        UjianEsaiJawab::where('recruitment_user_id', $request->recruitment_user_id)->where('kode', $request->kode)->update(
            [
                'nilai'             => $request->nilai,
            ]
        );

        // Merekam aktivitas pengguna
        return redirect('/pg-data-interview/dt/data-data_ujian_user/' . $request->recruitment_user_id . '/' . $request->holding . '')->with('success', 'data berhasil ditambahkan');
    }
    //End Esai
}
