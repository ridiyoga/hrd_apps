<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Departemen;
use App\Models\Holding;
use App\Models\InterviewUser;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\Menu;
use App\Models\Pembobotan;
use App\Models\RecruitmentInterview;
use App\Models\RecruitmentKeahlian;
use App\Models\RecruitmentKesehatan;
use App\Models\RecruitmentKesehatanKecelakaan;
use App\Models\RecruitmentKesehatanPengobatan;
use App\Models\RecruitmentKesehatanRS;
use App\Models\RecruitmentPendidikan;
use App\Models\RecruitmentRiwayat;
use App\Models\RecruitmentUser;
use App\Models\RecruitmentUserRecord;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Ujian;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use DivisionByZeroError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class InterviewController extends Controller
{
    public function index($menu_id, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menus = collect();
            $all_menu_main = collect();
            $all_menu_bar = collect();
        } else {

            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();

            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)
            ->with(['ApprovalUser' => function ($query) {
                $query;
            }])
            ->first();
        $departemen = KaryawanJabatan::where('karyawan_id', $user_karyawan->id)->where('is_active', 1)->pluck('departemen_id')->toArray();
        // dd($karyawan_jabatan);


        // dd($departemen);
        // dd($user_karyawan->Jabatan->Bagian->Divisi->Departemen->id);
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
            ->where('feedback_lanjutan', '1b')
            ->where('tanggal_wawancara_manager', '!=', null)
            ->whereIn('nama_dept', $departemen)
            ->get();
        // dd($departemen, $table);
        $history = RecruitmentUser::with([
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
            // // ->where('feedback_lanjutan', '1b')
            // ->whereIn('status_lanjutan', ['4b', '5b'])
            // // ->whereIn('nama_dept', $departemen)
            // ->get();
            // ->where('feedback_lanjutan', '1b')
            ->where('tanggal_wawancara_manager', '!=', null)
            ->whereIn('status_lanjutan', ['4b', '5b', '8b'])
            ->whereIn('nama_dept', $departemen)
            ->get();


        // dd($table, $user_karyawan->Jabatan->LevelJabatan->level_jabatan);
        // dd($history);
        // dd($datacuti_tingkat1);
        // dd($datacuti_tingkat2);
        // dd($datapenugasan);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view(
            'users.interview.index',
            [
                'departemen' => $departemen,
                'all_menus' => $all_menus,
                'table'     => $table,
                'history' => $history,
                'user_karyawan' => $user_karyawan,
                'menu_id' => $menu_id,
                'all_menu_main' => $all_menu_main,
                'all_menu_bar' => $all_menu_bar
            ]
        );
    }
    public function detail($menu_id, $id)
    {
        $user_karyawan  = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_dept = KaryawanJabatan::where('karyawan_id', $user_karyawan->id)->first();
        if ($get_dept == '') {
            $departemen = '';
        } else {
            $departemen = $get_dept->nama_departemen;
        }
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
        ])->with([
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
        ])->with([
            'recruitmentAdmin' => function ($query) {
                $query;
            }
        ])->with([
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
        // dd($table->waktuujian);
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
            $all_menu_main  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 1);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
            $all_menus  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 0);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();

            $all_menu_bar  = RoleMenu::with(['Menu' => function ($query) {
                $query->where('kategori', 'mobile');
            }])
                ->whereHas('Menu', function ($query) {
                    $query->where('kategori', 'mobile');
                    $query->where('main', 2);
                    $query->orderBy('sort_order', 'ASC');
                })
                ->whereIn('role_id', $get_role)
                ->get();
        }
        // dd($menu);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view(
            'users.interview.detail',
            [
                'departemen' => $departemen,
                'all_menus' => $all_menus,
                'all_menu_main' => $all_menu_main,
                'all_menu_bar' => $all_menu_bar,
                'table'     => $table,
                'menu_id'   => $menu_id,
                'user_karyawan' => $user_karyawan
            ]
        );
    }
    public function pdfUserKaryawan($id)
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
        // dd($pekerjaan);
        // dd($pendidikan);
        $pdf = Pdf::loadView('users.interview.cvpdf', [
            'ti$pekerjaan_counttle' => 'Data Recruitment'
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

        return $pdf->download($data_cv->AuthLogin->recruitmentCV->nama_lengkap . ' CV.pdf');
    }
    function pdfNilaiKaryawan($id)
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
        $pdf = Pdf::loadView('users.interview.nilaipdf', [
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
        return $pdf->download($table->Cv->nama_lengkap . ' NILAI.pdf');
    }
    public function prosesInterview(Request $request)
    {
        $manager = User::where('id', Auth::user()->id)->first();
        $id_manager = Karyawan::where('nomor_identitas_karyawan', $manager->karyawan_id)->first();
        // dd($id_manager->name);
        try {
            $id = $request->input('id');
            $catatan = $request->input('catatan');
            $status = $request->input('status');

            $recruitment = RecruitmentUser::with('Cv')->where('id', $id)->first();
            $recruitment->status_lanjutan = $status;
            $recruitment->id_manager = $id_manager->id;
            $recruitment->nama_manager = $id_manager->name;
            $recruitment->feedback_lanjutan = NULL;
            $recruitment->tanggal_konfirmasi_manager = date('Y-m-d H:i:s');
            $recruitment->save();

            $record = new RecruitmentUserRecord();
            $record->id = Uuid::uuid4();
            $record->recruitment_user_id = $id;
            $record->status = $status;
            $record->created_at = date('Y-m-d H:i:s');
            $record->save();

            $user_name = User::where('id', $request->user()->id)->first();
            $interview = RecruitmentInterview::where('recruitment_user_id', $id)->first();
            $interview->catatan_interview_manager = $catatan;
            $interview->save();

            if ($request->status == '5b') {
                $holdings = Holding::where('id', $recruitment->holding)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'Menolak calon karyawan ' . $recruitment->Cv->nama_lengkap,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
            } elseif ($request->status == '4b') {
                $holdings = Holding::where('id', $recruitment->holding)->first();
                $nama_menu = Menu::where('id', $request->menu_id)->first();
                ActivityLog::create([
                    'user_id' => $request->user()->id,
                    'user_name' => $user_name->username,
                    'menu_id' => $request->menu_id,
                    'menu_name' => $nama_menu->name,
                    'id_table' => $id,
                    'holding_id' => $holdings->id ?? '-',
                    'holding_category' => $holdings->holding_category ?? '-',
                    'table' => 'recruitment_user',
                    'activity' => 'update',
                    'description' => 'Menerima calon karyawan ' . $recruitment->Cv->nama_lengkap,
                    'keterangan' => 'Success',
                    'karir' => '2'
                ]);
            }


            $request->session()->flash('simpan_sukses', 'Data Berhasil Disimpan');
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil disimpan',
                'session' => session('simpan_sukses'),
                'menu' => $request->menu_id
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('simpan_error', 'Data Gagal Disimpan');
            return response()->json([
                'code' => 500,
                'menu' => $request->menu_id,
                'message' => $e->getMessage(),
                'session' => session('simpan_error')
            ]);
        }
    }
}
