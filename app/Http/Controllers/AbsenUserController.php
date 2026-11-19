<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lokasi;
use App\Models\MappingShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ActivityLog;
use App\Models\AttendanceLog;
use App\Models\Calendar;
use App\Models\CutiDetail;
use App\Models\IzinLibur;
use App\Models\IzinSakitDetail;
use App\Models\IzinTidakMasukDetail;
use App\Models\Karyawan;
use App\Models\LemburDetail;
use App\Models\PerdinDetail;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AbsenUserController extends Controller
{
    public function index($menu_id)
    {
        $user_karyawan = Karyawan::with('MappingShift')->where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        if (count($get_role) == 0) {
            $roleId = null;
        } else {
            $roleId = $get_role;
        }
        if ($roleId == null) {
            $all_menu_main = collect();
            $all_menus = collect();
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
        // dd($all_menu_bar);

        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.absen.index', [
            'title' => 'My Absen',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus' => $all_menus,
            'all_menu_bar' => $all_menu_bar,
            'menu_id' => $menu_id
        ]);
    }

    public function get_dataAbsensi($id, Request $request)
    {
        try {
            $non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $start = Carbon::parse($request->start_date)->format('Y-m-d');
            $end   = Carbon::parse($request->end_date)->format('Y-m-d');
            // $start = '2026-10-01';
            // $end   = '2026-10-02';
            $user_karyawan = Karyawan::with(['Absensi' => function ($query) use ($start, $end) {
                $query->with('MachineFinger');
                $query->whereBetween('LogTime', [$start . ' 00:00:00', $end . ' 23:59:59']);
                $query->orderBy('LogTime', 'ASC');
            }])
                ->with(['MappingShift' => function ($q) {
                    $q->whereNotNull('is_active');
                }])
                ->where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $period = CarbonPeriod::create($start, $end);
            // dd($user_karyawan);
            if ($user_karyawan == null) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Karyawan Tidak Ditemukan',
                ]);
            }
            if ($user_karyawan->shift == 'NON SHIFT') {
                foreach ($period as $date) {
                    $perdin_detail = PerdinDetail::where('id_karyawan', $user_karyawan->id)
                        ->whereDate('tgl_perdin', $date->toDateString())
                        ->where('status', 1)
                        ->exists();
                    $calendar = Calendar::where('start_date', $date->format('Y-m-d'))->where('status', 1)->first();
                    if ($calendar) {
                        $checkIn = '';
                        $checkOut = '';
                        $status = $calendar->nama_kegiatan;

                        $data_absensi[] = [
                            'tanggal'       => $date->format('Y-m-d'),
                            'masuk'         => $checkIn,
                            'pulang'        => $checkOut,
                            'shift'         => 'Klik untuk lihat detail',
                            'status'        => $status,
                            'detail_masuk'  => [],
                            'detail_pulang' => [],
                            'type'          => 'is_calendar'
                        ];
                    } else {
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_min_masuk);
                        $end   = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_terlambat)
                            ->addHours(3)
                            ->addMinutes(10);
                        $lembur_detail = LemburDetail::where('id_karyawan', $user_karyawan->id)
                            ->with('Lembur')
                            ->whereHas('Lembur', function ($query) use ($date) {
                                $query->whereDate('tanggal_lembur', $date->toDateString());
                            })
                            ->where('status', 1)
                            ->first();
                        if ($lembur_detail) {
                            $start_pulang = Carbon::parse($lembur_detail->tgl_lembur . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end_pulang = Carbon::parse($lembur_detail->tgl_pulang . ' ' . $lembur_detail->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                        } else {
                            $start_pulang = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end_pulang = Carbon::parse(
                                $date->format('Y-m-d') . ' ' . $non_shift->jam_keluar
                            )->endOfDay()->format('Y-m-d H:i:s');
                        }

                        $logs_absensi_masuk = $user_karyawan->Absensi->filter(function ($log) use ($start, $end) {
                            return Carbon::parse($log->LogTime)->between($start, $end);
                        });
                        if ($logs_absensi_masuk->isNotEmpty()) {
                            $checkIn = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                            if ($checkIn > $non_shift->jam_terlambat) {
                                $status = 'TELAT';
                            } else {
                                $status = 'TEPAT WAKTU';
                            }
                        } else {
                            $logs_absensi_masuk_any = $user_karyawan->Absensi->filter(function ($log) use ($date, $start) {
                                return Carbon::parse($log->LogTime)->between($date->format('Y-m-d 00:00:00'), $start);
                            });
                            if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                $checkIn = 'LIBUR';
                                $status = 'LIBUR';
                            } else {
                                if ($logs_absensi_masuk_any->isNotEmpty()) {
                                    $checkIn = Carbon::parse($logs_absensi_masuk_any->min('LogTime'))->format('H:i');
                                    $status = 'DATANG LEBIH AWAL';
                                } else {

                                    // CEK CUTIS
                                    $cuti_detail = CutiDetail::where('id_karyawan', $user_karyawan->id)
                                        ->whereDate('tgl_cuti', $date->toDateString())
                                        ->where('status', 1)
                                        ->exists();
                                    $sakit_detail = IzinSakitDetail::where('id_karyawan', $user_karyawan->id)
                                        ->whereDate('tgl_sakit', $date->toDateString())
                                        ->where('status', 1)
                                        ->exists();
                                    $tidak_masuk_detail = IzinTidakMasukDetail::where('id_karyawan', $user_karyawan->id)
                                        ->whereDate('tgl_tidak_masuk', $date->toDateString())
                                        ->where('status', 1)
                                        ->exists();
                                    $tukar_libur = IzinLibur::where('karyawan_id', $user_karyawan->id)
                                        ->whereDate('tanggal_tukar', $date->toDateString())
                                        ->where('status_libur', 3)
                                        ->exists();
                                    // dd($tukar_libur, $date->toDateString());
                                    if ($cuti_detail) {
                                        $checkIn = 'CUTI';
                                        $checkOut = 'CUTI';
                                        $status = 'CUTI';
                                    } else {
                                        if ($sakit_detail) {
                                            $checkIn = 'SAKIT';
                                            $checkOut = 'SAKIT';
                                            $status = 'SAKIT';
                                        } else {
                                            if ($tidak_masuk_detail) {
                                                $checkIn = 'IZIN TIDAK MASUK';
                                                $checkOut = 'IZIN TIDAK MASUK';
                                                $status = 'IZIN TIDAK MASUK';
                                            } else {
                                                if ($tukar_libur) {
                                                    $checkIn = 'TUKAR LIBUR';
                                                    $checkOut = 'TUKAR LIBUR';
                                                    $status = 'TUKAR LIBUR';
                                                } else if ($perdin_detail) {
                                                    $checkIn = 'PERJALANAN DINAS';
                                                    $checkOut = 'PERJALANAN DINAS';
                                                    $status = 'PERJALANAN DINAS';
                                                } else {
                                                    $checkIn = $date->isFuture() ? '' : '';
                                                    $status = $date->isFuture() ? '' : 'TIDAK HADIR';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // Log pulang
                        $logs_absensi_pulang = $user_karyawan->Absensi->filter(function ($log) use ($start_pulang, $end_pulang) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            return $getcheckOut->between($start_pulang, $end_pulang);
                        });
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $checkOut = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        } else {
                            if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                $checkOut = 'LIBUR';
                            } else if ($perdin_detail) {
                                $checkOut = 'PERJALANAN DINAS';
                            } else {
                                $checkOut = $date->isFuture() ? '' : '';
                            }
                        }
                        $detail_masuk = $logs_absensi_masuk->map(function ($log) {
                            return $log->MachineFinger->Name;
                        });
                        $detail_pulang = $logs_absensi_pulang->map(function ($log) {
                            return $log->MachineFinger->Name;
                        });
                        // contoh hasil untuk list
                        $data_absensi[] = [
                            'tanggal'       => $date->format('Y-m-d'),
                            'masuk'         => $checkIn,
                            'pulang'        => $checkOut,
                            'shift'         => $non_shift->nama_shift,
                            'status'        => $status,
                            'detail_masuk'  => $detail_masuk,
                            'detail_pulang' => $detail_pulang,
                            'type'          => ''
                        ];
                    }
                }
            } else {
                foreach ($period as $date) {
                    $mapping_shift = $user_karyawan->MappingShift;
                    $jam_masuk = $mapping_shift->firstWhere('tanggal_masuk', $date->toDateString());
                    $cuti_detail = CutiDetail::where('id_karyawan', $user_karyawan->id)
                        ->whereDate('tgl_cuti', $date->toDateString())
                        ->where('status', 1)
                        ->exists();
                    $sakit_detail = IzinSakitDetail::where('id_karyawan', $user_karyawan->id)
                        ->whereDate('tgl_sakit', $date->toDateString())
                        ->where('status', 1)
                        ->exists();
                    $tidak_masuk_detail = IzinTidakMasukDetail::where('id_karyawan', $user_karyawan->id)
                        ->whereDate('tgl_tidak_masuk', $date->toDateString())
                        ->where('status', 1)
                        ->exists();
                    $perdin_detail = PerdinDetail::where('id_karyawan', $user_karyawan->id)
                        ->whereDate('tgl_perdin', $date->toDateString())
                        ->where('status', 1)
                        ->exists();
                    $lembur_detail = LemburDetail::where('id_karyawan', $user_karyawan->id)
                        ->where('status', 1)
                        ->with('Lembur')
                        ->whereHas('Lembur', function ($query) use ($date) {
                            $query->whereDate('tanggal_lembur', $date->toDateString());
                        })
                        ->first();
                    // dd($jam_masuk, $date->toDateString());
                    $calendar = Calendar::where('start_date', $date->format('Y-m-d'))->where('status', 1)->first();
                    if ($calendar) {
                        $checkIn = '';
                        $checkOut = '';
                        $status = $calendar->nama_kegiatan;

                        $data_absensi[] = [
                            'tanggal'   => $date->format('Y-m-d'),
                            'masuk'     => $checkIn,
                            'pulang'    => $checkOut,
                            'shift'     => 'Klik untuk lihat detail',
                            'status'    => $status
                        ];
                    } else {
                        if ($jam_masuk == null) {
                            // CEK TABEL CALENDAR
                            // return $calendar;
                            if ($cuti_detail) {
                                $checkIn = 'CUTI';
                                $checkOut = 'CUTI';
                                $status = 'CUTI';
                            } else {
                                if ($sakit_detail) {
                                    $checkIn = 'SAKIT';
                                    $checkOut = 'SAKIT';
                                    $status = 'SAKIT';
                                } else {
                                    if ($tidak_masuk_detail) {
                                        $checkIn = 'IZIN TIDAK MASUK';
                                        $checkOut = 'IZIN TIDAK MASUK';
                                        $status = 'IZIN TIDAK MASUK';
                                    } else if ($perdin_detail) {
                                        $checkIn = 'PERJALANAN DINAS';
                                        $checkOut = 'PERJALANAN DINAS';
                                        $status = 'PERJALANAN DINAS';
                                    } else {
                                        $checkIn = '';
                                        $checkOut = '';
                                        $status = 'BELUM DIJADWALKAN';
                                    }
                                }
                            }
                            $data_absensi[] = [
                                'tanggal'   => $date->format('Y-m-d'),
                                'masuk'     => $checkIn,
                                'pulang'    => $checkOut,
                                'shift'     => 'SHIFT: -',
                                'status'    => $status
                            ];
                        } else {
                            if ($jam_masuk->status_absen == 'LIBUR') {
                                $checkIn    = 'LIBUR';
                                $checkOut   = 'LIBUR';
                                $status     = 'LIBUR';
                            }
                            $kategori_shift = $jam_masuk->Shift;
                            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $kategori_shift->jam_min_masuk);
                            $batas_absensi = Carbon::parse($date->format('Y-m-d') . ' ' . $kategori_shift->jam_min_masuk)->subHours(3);
                            $end   = Carbon::parse($date->format('Y-m-d') . ' ' . $kategori_shift->jam_terlambat)
                                ->addHours(3)
                                ->addMinutes(10);

                            if ($lembur_detail) {
                                $start_pulang = Carbon::parse($lembur_detail->tgl_lembur . ' ' . $lembur_detail->jam_mulai)->format('Y-m-d H:i:59');
                                $end_pulang = Carbon::parse($lembur_detail->tgl_pulang . ' ' . $lembur_detail->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                            } else {
                                if ($kategori_shift->tgl_pulang_besok == 1) {
                                    if ($kategori_shift->jam_pulang_cepat > $kategori_shift->jam_keluar) {
                                        $start_pulang = Carbon::parse($jam_masuk->tanggal_masuk . ' ' . $kategori_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                        $end_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                    } else {
                                        $start_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                        $end_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                    }
                                    // dd($start_pulang, $end_pulang, $jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_keluar);
                                } else {
                                    $start_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                    $end_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                }
                            }
                            $logs_absensi_masuk = $user_karyawan->Absensi->filter(function ($log) use ($start, $end) {
                                return Carbon::parse($log->LogTime)->between($start, $end);
                            });
                            if ($logs_absensi_masuk->isNotEmpty()) {
                                $checkIn = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                                if ($checkIn > $kategori_shift->jam_terlambat) {
                                    $status = 'TELAT';
                                } else {
                                    $status = 'TEPAT WAKTU';
                                }
                            } else {
                                $logs_absensi_masuk_datang_awal = $user_karyawan->Absensi->filter(function ($log) use ($start, $batas_absensi) {
                                    return Carbon::parse($log->LogTime)->between($batas_absensi, $start);
                                });
                                if ($logs_absensi_masuk_datang_awal->isNotEmpty()) {
                                    $checkIn = Carbon::parse($logs_absensi_masuk_datang_awal->min('LogTime'))->format('H:i');
                                    $status = 'DATANG LEBIH AWAL';
                                } else {
                                    if ($cuti_detail) {
                                        $checkIn = 'CUTI';
                                        $checkOut = 'CUTI';
                                        $status = 'CUTI';
                                    }
                                    if ($sakit_detail) {
                                        $checkIn = 'SAKIT';
                                        $checkOut = 'SAKIT';
                                        $status = 'SAKIT';
                                    } else {

                                        if ($cuti_detail) {
                                            $checkIn = 'CUTI';
                                            $checkOut = 'CUTI';
                                            $status = 'CUTI';
                                        }
                                        if ($sakit_detail) {
                                            $checkIn = 'SAKIT';
                                            $checkOut = 'SAKIT';
                                            $status = 'SAKIT';
                                        }
                                        if ($tidak_masuk_detail) {
                                            $checkIn = 'IZIN TIDAK MASUK';
                                            $checkOut = 'IZIN TIDAK MASUK';
                                            $status = 'IZIN TIDAK MASUK';
                                        }
                                        if ($kategori_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                            $checkIn = 'LIBUR';
                                            $status = 'LIBUR';
                                        } else {
                                            if ($cuti_detail) {
                                                $checkIn = 'CUTI';
                                                $checkOut = 'CUTI';
                                                $status = 'CUTI';
                                            } else {
                                                if ($sakit_detail) {
                                                    $checkIn = 'SAKIT';
                                                    $checkOut = 'SAKIT';
                                                    $status = 'SAKIT';
                                                } else {
                                                    if ($tidak_masuk_detail) {
                                                        $checkIn = 'IZIN TIDAK MASUK';
                                                        $checkOut = 'IZIN TIDAK MASUK';
                                                        $status = 'IZIN TIDAK MASUK';
                                                    } else if ($perdin_detail) {
                                                        $checkIn = 'PERJALANAN DINAS';
                                                        $checkOut = 'PERJALANAN DINAS';
                                                        $status = 'PERJALANAN DINAS';
                                                    } else {
                                                        // dd('asoy');
                                                        $checkIn = $date->isFuture() ? '' : '';
                                                        $status = $date->isFuture() ? '' : 'TIDAK HADIR';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // Log pulang
                            $logs_absensi_pulang = $user_karyawan->Absensi->filter(function ($log) use ($start_pulang, $end_pulang) {
                                $getcheckOut = Carbon::parse($log->LogTime);
                                return $getcheckOut->between($start_pulang, $end_pulang);
                            });
                            if ($logs_absensi_pulang->isNotEmpty()) {
                                $checkOut = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                                if (Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('Y-m-d') > $date) {
                                    // dd('ok');
                                    $checkOut = $checkOut . ' (' . Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('d-m-Y') . ')';
                                } else {
                                    $checkOut = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                                }
                            } else {
                                if ($kategori_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                    $checkOut = 'LIBUR';
                                } else if ($perdin_detail) {
                                    $checkOut = 'PERJALANAN DINAS';
                                } else {
                                    $checkOut = $date->isFuture() ? '' : '';
                                }
                            }

                            // contoh hasil untuk list
                            $data_absensi[] = [
                                'tanggal' => $date->format('Y-m-d'),
                                'masuk'   => $checkIn,
                                'pulang'  => $checkOut,
                                'shift'   => 'SHIFT: ' . $kategori_shift->nama_shift,
                                'status' => $status
                            ];
                        }
                    }
                    // dd($data_absensi);
                }
            }
            return response()->json([
                'code' => 200,
                'data_absensi' => $data_absensi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_table_absensi(Request $request)
    {

        // dd($request->all());
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->value('id');
        $blnskrg = date('m');
        // dd($firstDayofPreviousMonth);
        if ($request->ajax()) {
            if (!empty($request->filter_month)) {
                if ($request->filter_month == $blnskrg) {
                    $data = MappingShift::where('user_id', $user_karyawan)->whereMonth('tanggal_masuk', $blnskrg)->get();
                } else {
                    $data = MappingShift::where('user_id', $user_karyawan)->whereMonth('tanggal_masuk', $request->filter_month)->orderBy('tanggal_masuk', 'DESC')->get();
                }
                return DataTables::of($data)->addIndexColumn()
                    ->addColumn('tanggal_masuk', function ($row) {
                        $result = Carbon::parse($row->tanggal_masuk)->isoFormat('DD-MM-Y');;
                        return $result;
                    })
                    ->addColumn('jam_absen', function ($row) {
                        if ($row->jam_absen == NULL) {
                            return '-';
                        } else {
                            $result = Carbon::parse($row->jam_absen)->isoFormat('HH:mm');;
                            return $result;
                        }
                    })
                    ->addColumn('jam_pulang', function ($row) {
                        if ($row->jam_pulang == NULL) {
                            return '-';
                        } else {
                            $result = Carbon::parse($row->jam_pulang)->isoFormat('HH:mm');;
                            return $result;
                        }
                    })
                    ->addColumn('keterangan', function ($row) {
                        if ($row->status_absen == NULL) {
                            return '-';
                        } else if ($row->status_absen == 'CUTI') {
                            return '<span class="badge w-100 light badge-warning">CUTI</span>';
                        } else if ($row->status_absen == 'SAKIT') {
                            return '<span class="badge w-100 light badge-warning">SAKIT</span>';
                        } else if ($row->status_absen == 'LIBUR') {
                            return '<span class="badge w-100 light badge-warning">LIBUR</span>';
                        } else if ($row->status_absen == 'TUKAR LIBUR') {
                            return '<span class="badge w-100 light badge-warning">TUKAR LIBUR</span>';
                        } else if ($row->status_absen == 'TIDAK HADIR KERJA') {
                            if ($row->tanggal_masuk >= date('Y-m-d')) {
                                return '-';
                            } else {
                                return '<span class="badge w-100 light badge-warning">TIDAK HADIR KERJA</span>';
                            }
                        } else if ($row->status_absen == 'HADIR KERJA') {
                            return '<span class="badge w-100 light badge-success">HADIR KERJA</span>';
                        } else {
                            return $row->status_absen;
                        }
                    })
                    ->rawColumns(['tanggal_masuk', 'jam_absen', 'jam_pulang', 'keterangan'])
                    ->make(true);
            } else {
                $data = MappingShift::where('user_id', $user_karyawan)->whereMonth('tanggal_masuk', $blnskrg)->orderBy('tanggal_masuk', 'DESC')->get();
                // dd($data);
                return DataTables::of($data)
                    ->addColumn('tanggal_masuk', function ($row) {
                        $result = Carbon::parse($row->tanggal_masuk)->isoFormat('DD-MM-Y');;
                        return $result;
                    })
                    ->addColumn('jam_absen', function ($row) {
                        if ($row->jam_absen == NULL) {
                            return '-';
                        } else {
                            $result = Carbon::parse($row->jam_absen)->isoFormat('HH:mm');;
                            return $result;
                        }
                    })
                    ->addColumn('jam_pulang', function ($row) {
                        if ($row->jam_pulang == NULL) {
                            return '-';
                        } else {
                            $result = Carbon::parse($row->jam_pulang)->isoFormat('HH:mm');;
                            return $result;
                        }
                    })
                    ->addColumn('keterangan', function ($row) {
                        if ($row->status_absen == NULL) {
                            return '-';
                        } else if ($row->status_absen == 'CUTI') {
                            return '<span class="badge w-100 light badge-warning">CUTI</span>';
                        } else if ($row->status_absen == 'SAKIT') {
                            return '<span class="badge w-100 light badge-warning">SAKIT</span>';
                        } else if ($row->status_absen == 'TUKAR LIBUR') {
                            return '<span class="badge w-100 light badge-warning">TUKAR LIBUR</span>';
                        } else if ($row->status_absen == 'LIBUR') {
                            return '<span class="badge w-100 light badge-warning">LIBUR</span>';
                        } else if ($row->status_absen == 'TIDAK HADIR KERJA') {
                            if ($row->tanggal_masuk >= date('Y-m-d')) {
                                return '-';
                            } else {
                                return '<span class="badge w-100 light badge-warning">TIDAK HADIR KERJA</span>';
                            }
                        } else if ($row->status_absen == 'HADIR KERJA') {
                            return '<span class="badge w-100 light badge-success">HADIR KERJA</span>';
                        } else {
                            return $row->status_absen;
                        }
                    })
                    ->rawColumns(['tanggal_masuk', 'jam_absen', 'jam_pulang', 'keterangan'])
                    ->make(true);
            }
        }
    }
}
