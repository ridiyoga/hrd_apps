<?php

namespace App\Http\Controllers;

use App\Events\IzinPost;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\MappingShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ActivityLog;
use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Izin;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\LemburDetail;
use App\Models\AttendanceLog;
use App\Models\Calendar;
use App\Models\CutiDetail;
use App\Models\IzinLibur;
use App\Models\IzinSakit;
use App\Models\IzinSakitDetail;
use App\Models\IzinTidakMasukDetail;
use App\Models\LevelJabatan;
use App\Models\Menu;
use App\Models\Penugasan;
use App\Models\PerdinDetail;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\Site;
use App\Models\Shift;
use App\Models\Titik;
use App\Models\QuickPanel;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTime;
use Facade\FlareClient\Time\Time;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class HomeUserController extends Controller
{
    public function index(Request $request, $menu_id)
    {
        // dd(Auth::user());
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Silahkan Login Terlebih Dahulu');
            return redirect()->route('login');
        } else {
            if (Auth::user()->is_admin == 'admin') {
            } else {
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
                return view('users.home.index', [
                    'title'             => 'Absen',
                    'user_karyawan'     => $user_karyawan,
                    'all_menu_main'     => $all_menu_main,
                    'all_menus'         => $all_menus,
                    'all_menu_bar'      => $all_menu_bar,

                ]);
            }
        }
    }



    public function get_notif()
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $count_izin_0 = Izin::where('user_id', $user_karyawan->id)->where('status_izin', '0')->count();
        $count_cuti_0 = Cuti::where('user_id', $user_karyawan->id)->where('status_cuti', '0')->count();
        $count_penugasan_0 = Penugasan::where('id_user', $user_karyawan->id)->where('status_penugasan', '0')->count();
        return response()->json([
            'count_izin' => $count_izin_0,
            'count_cuti' => $count_cuti_0,
            'count_penugasan' => $count_penugasan_0,
        ]);
    }
    public function get_notif_cuti()
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $count_cuti_0 = Cuti::where('user_id', $user_karyawan->id)->where('status_cuti', '0')->count();
        return json_encode($count_cuti_0);
    }
    public function get_notif_penugasan()
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $count_penugasan_0 = Penugasan::where('id_user', $user_karyawan->id)->where('status_penugasan', '0')->count();
        return json_encode($count_penugasan_0);
    }
    public function savefaceid(Request $request)
    {
        // dd($request->all());
        $query = Karyawan::where('id', $request->karyawan_id)->first();
        $query->face_id = $request->faceid;
        $query->update();
        if ($query) {
            $request->session()->flash('simpanface_success');
        } else {
            $request->session()->flash('simpanface_error');
        }
        return redirect()->route('home');
    }
    public function get_count_absensi_home(Request $request)
    {
        // dd($request->all());
        try {

            $data_absensi = [];
            $user_login =  Karyawan::with(['Absensi' => function ($query) {
                $query->with('MachineFinger');
            }])
                ->with(['MappingShift' => function ($query) {
                    $query->whereNotNull('is_active');
                    $query->with('Shift');
                    $query->where('status_approved', 1);
                }])
                ->with(['LemburDetail' => function ($query) {
                    $query->where('status', 1)->with('Lembur');
                }])
                ->where('nomor_identitas_karyawan', Auth::user()->karyawan_id)
                ->first();
            $non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();

            // $startDate = '2026-08-05';
            // $endDate   = '2026-08-05';
            $startDate = $request->start_date;
            $endDate   = $request->end_date;
            $period = CarbonPeriod::create($startDate, $endDate);
            $datenow = Carbon::now()->format('Y-m-d');
            if ($datenow < $request->end_date) {
                $now_date = $datenow;
            } else {
                $now_date = $request->end_date;
            }

            // $last7days = '2026-08-05';
            // $last1days   = '2026-08-05';
            $last1days   = Carbon::parse($now_date)->format('Y-m-d');
            $last7days   = Carbon::parse($now_date)->subDays(6)->format('Y-m-d');
            $period_last7days = CarbonPeriod::create($last7days, $last1days);
            $period = CarbonPeriod::create($startDate, $endDate);

            $absensiByDate = $user_login->Absensi->groupBy(function ($item) {
                return Carbon::parse($item->LogTime)->toDateString();
            });
            $getLogsInWindow = function ($start, $end) use ($absensiByDate) {
                $logs = collect();
                foreach (
                    CarbonPeriod::create(
                        Carbon::parse($start)->startOfDay(),
                        Carbon::parse($end)->startOfDay()
                    ) as $logDate
                ) {
                    $logs = $logs->merge($absensiByDate->get($logDate->toDateString(), collect()));
                }

                return $logs->filter(function ($log) use ($start, $end) {
                    return Carbon::parse($log->LogTime)->between($start, $end);
                });
            };
            $karyawan = Karyawan::where('id', $user_login->id)->first();
            $calendars = Calendar::whereBetween('start_date', [$last7days, $last1days])
                ->where('status', 1)
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->start_date)->toDateString();
                });
            $cutiByDate = CutiDetail::where('id_karyawan', $user_login->id)
                ->whereBetween('tgl_cuti', [$last7days, $last1days])
                ->where('status', 1)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tgl_cuti)->toDateString();
                });
            $sakitByDate = IzinSakitDetail::where('id_karyawan', $user_login->id)
                ->whereBetween('tgl_sakit', [$last7days, $last1days])
                ->where('status', 1)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tgl_sakit)->toDateString();
                });
            $tidakMasukByDate = IzinTidakMasukDetail::where('id_karyawan', $user_login->id)
                ->whereBetween('tgl_tidak_masuk', [$last7days, $last1days])
                ->where('status', 1)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tgl_tidak_masuk)->toDateString();
                });
            $perdinByDate = PerdinDetail::where('id_karyawan', $user_login->id)
                ->whereBetween('tgl_perdin', [$last7days, $last1days])
                ->where('status', 1)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tgl_perdin)->toDateString();
                });
            $tukarLiburByDate = IzinLibur::where('karyawan_id', $user_login->id)
                ->whereBetween('tanggal_tukar', [$last7days, $last1days])
                ->where('status_libur', 3)
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->tanggal_tukar)->toDateString();
                });
            $lemburByDate = $user_login->LemburDetail
                ->filter(function ($item) {
                    return $item->Lembur !== null;
                })
                ->groupBy(function ($item) {
                    return Carbon::parse($item->Lembur->tanggal_lembur)->toDateString();
                });
            $getLemburDetailForDate = function ($date) use ($lemburByDate) {
                return $lemburByDate
                    ->get(Carbon::parse($date)->toDateString(), collect())
                    ->first();
            };
            // dd($period_last7days);
            $count_izin = IzinSakitDetail::where('id_karyawan', $user_login->id)->whereBetween('tgl_sakit', [$startDate, $endDate])->where('status', '1')->count();
            date_default_timezone_set('Asia/Jakarta');
            $count_cuti = CutiDetail::where('id_karyawan', $user_login->id)->whereBetween('tgl_cuti', [$startDate, $endDate])->where('status', '1')->count();
            date_default_timezone_set('Asia/Jakarta');

            $tglskrg = date('Y-m-d');
            $blnskrg = date('m');
            $thnskrg = date('Y');
            $jam_kerja = '';
            // dd($user_login->shift);
            $tepat_waktu  = 0;
            $telat_ringan = 0;
            $telat_berat  = 0;
            $tetap_absen = 0;
            $datang_awal = 0;
            if ($user_login->shift == 'NON SHIFT') {
                $mapping_shift = null;
                foreach ($period as $data) {
                    $plus5  = Carbon::parse($data->format('Y-m-d') . ' ' . $non_shift->jam_terlambat)->format('H:i');
                    $plus15 = Carbon::parse($data->format('Y-m-d') . ' ' . $non_shift->jam_terlambat)->addMinutes(10)->format('H:i');
                    $logsToday = $absensiByDate->get($data->toDateString(), collect());
                    $logs_telat = $logsToday;
                    $logs_tepat_waktu = $logsToday->filter(function ($log) use ($data, $non_shift) {
                        $logTime = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($data->format('Y-m-d'))->setTimeFromTimeString(Carbon::parse($non_shift->jam_min_masuk)->format('H:i:59'));
                        $end = Carbon::parse($data->format('Y-m-d'))->setTimeFromTimeString(Carbon::parse($non_shift->jam_terlambat)->format('H:i:59'));
                        return $logTime->between($start, $end);
                    });
                    // return $logs;
                    if ($logs_tepat_waktu->isNotEmpty()) {
                        $tepat_waktu +=  1;
                    } else {
                        if ($logs_telat->isNotEmpty()) {
                            $logtime  = $logs_telat->min('LogTime'); // ambil check-in paling awal
                            $check_in = Carbon::parse($logtime)->format('H:i');
                            if ($check_in > $plus5 && $check_in <= $plus15) {
                                $telat_ringan += 1;
                            } else  if ($check_in > $plus15) {
                                $telat_berat += 1;
                            } else {
                                $tetap_absen += 1;
                            }
                        }
                    }
                }
                // dd($period_last7days);
                foreach ($period_last7days as $date) {
                    $dateKey = $date->toDateString();
                    $calendar = $calendars->get($dateKey);
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
                        $lembur_detail = LemburDetail::where('id_karyawan', $user_login->id)
                            ->whereDate('tgl_lembur', $date->toDateString())
                            ->where('status', 1)
                            ->first();
                        if ($lembur_detail) {
                            $start_pulang = Carbon::parse($lembur_detail->tgl_lembur . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end_pulang = Carbon::parse($lembur_detail->tgl_pulang . ' ' . $lembur_detail->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                        } else {
                            $start_pulang = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end_pulang = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_keluar)->endOfDay()->format('Y-m-d H:i:59');
                        }

                        $logsToday = $absensiByDate->get($dateKey, collect());
                        $logs_absensi_masuk = $logsToday->filter(function ($log) use ($start, $end) {
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
                            $logs_absensi_masuk_any = $logsToday->filter(function ($log) use ($date, $start) {
                                return Carbon::parse($log->LogTime)->between($date->format('Y-m-d 00:00:00'), $start);
                            });
                            if ($logs_absensi_masuk_any->isNotEmpty()) {
                                $checkIn = Carbon::parse($logs_absensi_masuk_any->min('LogTime'))->format('H:i');
                                $status = 'DATANG LEBIH AWAL';
                            } else {
                                if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                    $checkIn = 'LIBUR';
                                    $status = 'LIBUR';
                                } else {
                                    // CEK CUTIS
                                    $cuti_detail = $cutiByDate->has($dateKey);
                                    $sakit_detail = $sakitByDate->has($dateKey);
                                    $tidak_masuk_detail = $tidakMasukByDate->has($dateKey);
                                    $tukar_libur = $tukarLiburByDate->has($dateKey);
                                    $perdin_detail = $perdinByDate->has($dateKey);
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
                                                } else {
                                                    if ($perdin_detail) {
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
                        }
                        // Log pulang
                        $logs_absensi_pulang = $getLogsInWindow($start_pulang, $end_pulang);
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $checkOut = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        } else {
                            if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                $checkOut = 'LIBUR';
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
                            'tanggal' => $date->format('Y-m-d'),
                            'masuk'   => $checkIn,
                            'pulang'  => $checkOut,
                            'detail_masuk' => $detail_masuk,
                            'detail_pulang' => $detail_pulang,
                            'shift'   => $non_shift->nama_shift,
                            'status' => $status
                        ];
                    }
                }
                $get_jam_kerja = Shift::where('nama_shift', 'NON SHIFT')->first();
                $status_absen_skrg = AttendanceLog::where('EnrollNumber', $user_login->nomor_identitas_karyawan)
                    ->whereBetween('LogTime', [$startDate, $endDate])
                    ->orderBy('LogTime', 'ASC')
                    ->get();
                $logs_absensi_masuk = $status_absen_skrg->filter(function ($log) use ($get_jam_kerja, $tglskrg) {
                    $getcheckIn = Carbon::parse($log->LogTime);
                    $start = Carbon::parse($tglskrg . ' ' . $get_jam_kerja->jam_min_masuk)->format('Y-m-d H:i:59');
                    $end = Carbon::parse($tglskrg . ' ' . $get_jam_kerja->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                    return $getcheckIn->between($start, $end);
                });
                // dd($logs_absensi_masuk, $status_absen_skrg, $startDate, $endDate);
                if ($logs_absensi_masuk->isNotEmpty()) {
                    $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                    if ($checkIn > $get_jam_kerja->jam_terlambat) {
                        $checkIn = ' <span class="badge light badge-sm badge-warning absen_masuk_text"><strong>' . $checkIn . ' WIB</strong></span>';
                    } else {
                        $checkIn = '<span class="badge light badge-sm badge-success absen_masuk_text"><strong>' . $checkIn . ' WIB</strong></span>';
                    }
                } else {
                    $checkIn = '-';
                }
                $lembur_detail = $getLemburDetailForDate($tglskrg);
                $tanggal_pulang = $lembur_detail?->tgl_pulang ?? $tglskrg;
                $jam_selesai = $lembur_detail?->jam_selesai;
                $logs_absensi_pulang = $status_absen_skrg->filter(function ($log) use ($tglskrg, $tanggal_pulang, $get_jam_kerja, $jam_selesai) {
                    $getcheckOut = Carbon::parse($log->LogTime);
                    $start = Carbon::parse($tglskrg . ' ' . $get_jam_kerja->jam_pulang_cepat)->format('Y-m-d H:i:59');
                    $end = $jam_selesai
                        ? Carbon::parse($tanggal_pulang . ' ' . $jam_selesai)->addHours(2)->format('Y-m-d H:i:59')
                        : Carbon::parse($tanggal_pulang . ' ' . $get_jam_kerja->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                    return $getcheckOut->between($start, $end);
                });
                // return $logs_absensi_pulang;
                if ($logs_absensi_pulang->isNotEmpty()) {
                    $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                    if ($checkOut < $get_jam_kerja->jam_keluar) {
                        $checkOut = '<span class="badge light badge-sm badge-danger absen_masuk_text"><strong>' . $checkOut . ' WIB</strong></span>';
                    } else {
                        $checkOut = '<span class="badge light badge-sm badge-success absen_masuk_text"><strong>' . $checkOut . ' WIB</span>';
                    }
                } else {
                    $checkOut = null;
                }
                if ($get_jam_kerja != null) {
                    if ($get_jam_kerja->status_absen == 'LIBUR') {
                        $jam_kerja = 'LIBUR';
                    } else {
                        $jam_kerja = $get_jam_kerja->jam_masuk . ' - ' . $get_jam_kerja->jam_keluar;
                    }
                } else {
                    $jam_kerja = null;
                }
                $count_absen_hadir = AttendanceLog::where('EnrollNumber', $user_login->nomor_identitas_karyawan)
                    ->whereMonth('LogTime', $blnskrg)
                    ->distinct('LogTime')
                    ->count('LogTime');
            } else {
                // dd($period);
                foreach ($period as $data) {
                    $mapping_shift = $user_login->MappingShift;
                    $jam_masuk = $mapping_shift->firstWhere('tanggal_masuk', $data->toDateString());
                    if ($jam_masuk == null) {
                        $tepat_waktu += 0;
                        $telat_ringan += 0;
                        $telat_berat += 0;
                        $tetap_absen += 0;
                        $datang_awal += 0;
                    } else {
                        $kategori_shift = $jam_masuk->Shift;
                        $plus5  = Carbon::parse($data->format('Y-m-d') . ' ' . $kategori_shift->jam_terlambat)->format('H:i');
                        $plus15 = Carbon::parse($data->format('Y-m-d') . ' ' . $kategori_shift->jam_terlambat)->addMinutes(10)->format('H:i');
                        $min3jam = Carbon::parse($data->format('Y-m-d') . ' ' . $kategori_shift->jam_min_masuk)->subHours(3)->format('H:i');
                        $batas_telat = Carbon::parse($plus15)->addHours(3)->format('H:i');
                        $logs_telat = $user_login->Absensi->filter(function ($log) use ($plus5, $batas_telat) {
                            return Carbon::parse($log->LogTime)->between($plus5, $batas_telat);
                        });
                        $start = Carbon::parse($data->format('Y-m-d'))->setTimeFromTimeString(Carbon::parse($kategori_shift->jam_min_masuk)->format('H:i:59'));

                        $logs_tepat_waktu = $user_login->Absensi->filter(function ($log) use ($start, $plus5) {
                            $logTime = Carbon::parse($log->LogTime);
                            return $logTime->between($start, $plus5);
                        });
                        // return $logs;
                        if ($logs_tepat_waktu->isNotEmpty()) {
                            $tepat_waktu +=  1;
                        } else {
                            if ($logs_telat->isNotEmpty()) {
                                $logtime  = $logs_telat->min('LogTime'); // ambil check-in paling awal
                                $check_in = Carbon::parse($logtime)->format('H:i');
                                if ($check_in > $plus5 && $check_in <= $plus15) {
                                    $telat_ringan += 1;
                                } else if ($check_in > $plus15) {
                                    $telat_berat += 1;
                                }
                            } else {
                                $logs_datang_awal = $user_login->Absensi->filter(function ($log) use ($start, $min3jam) {
                                    return Carbon::parse($log->LogTime)->between($min3jam, $start);
                                });
                                if ($logs_datang_awal->isNotEmpty()) {
                                    $datang_awali  = $logs_datang_awal->min('LogTime'); // ambil check-in paling awal
                                    // dd($datang_awali);
                                    $check_in = Carbon::parse($datang_awali)->format('H:i');
                                    if ($check_in) {
                                        $datang_awal += 1;
                                    } else {
                                        $datang_awal = 0;
                                    }
                                }
                            }
                        }
                        // dd($tepat_waktu, $telat_ringan, $telat_berat, $logs_tepat_waktu, $logs_telat);
                    }
                }
                // dd($period_last7days);

                foreach ($period_last7days as $date) {
                    $dateKey = $date->toDateString();
                    $calendar = $calendars->get($dateKey);
                    $cuti_detail = $cutiByDate->has($dateKey);
                    $sakit_detail = $sakitByDate->has($dateKey);
                    $tidak_masuk_detail = $tidakMasukByDate->has($dateKey);
                    $tukar_libur = $tukarLiburByDate->has($dateKey);
                    $perdin_detail = $perdinByDate->has($dateKey);
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
                        $mapping_shift = $user_login->MappingShift;
                        $jam_masuk = $mapping_shift->firstWhere('tanggal_masuk', $date->toDateString());

                        if ($jam_masuk == null) {
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
                                    } else if ($tukar_libur) {
                                        $checkIn = 'TUKAR LIBUR';
                                        $checkOut = 'TUKAR LIBUR';
                                        $status = 'TUKAR LIBUR';
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
                            // dd($jam_masuk->nama_shift);
                            $kategori_masuk_shift = $jam_masuk->Shift;
                            if ($jam_masuk->nama_shift == 'LIBUR') {
                                $checkIn = 'LIBUR';
                                $checkOut = 'LIBUR';
                                $status = 'LIBUR';
                                $detail_masuk = [];
                                $detail_pulang = [];
                            } else {
                                $logsToday = $absensiByDate->get($dateKey, collect());
                                $start = Carbon::parse($date->format('Y-m-d') . ' ' . $kategori_masuk_shift->jam_min_masuk);
                                $batas_absensi = Carbon::parse($date->format('Y-m-d') . ' ' . $kategori_masuk_shift->jam_min_masuk)->subHours(3);
                                $end   = Carbon::parse($date->format('Y-m-d') . ' ' . $kategori_masuk_shift->jam_terlambat)
                                    ->addHours(3)
                                    ->addMinutes(10)
                                    ->format('Y-m-d H:i:59');
                                $lembur_detail = LemburDetail::where('id_karyawan', $user_login->id)
                                    ->with('Lembur')
                                    ->whereHas('Lembur', function ($query) use ($date) {
                                        $query->whereDate('tanggal_lembur', $date->toDateString());
                                    })
                                    ->where('status', 1)
                                    ->first();
                                if ($lembur_detail) {
                                    $start_pulang = Carbon::parse($lembur_detail->tgl_lembur . ' ' . $lembur_detail->jam_mulai)->format('Y-m-d H:i:59');
                                    $end_pulang = Carbon::parse($lembur_detail->tgl_pulang . ' ' . $lembur_detail->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                                } else {
                                    if (
                                        $kategori_masuk_shift->tgl_pulang_besok == 1
                                        && $kategori_masuk_shift->jam_pulang_cepat > $kategori_masuk_shift->jam_keluar
                                    ) {
                                        $tanggal_mulai_pulang = $jam_masuk->tanggal_masuk;
                                    } else {
                                        $tanggal_mulai_pulang = $jam_masuk->tanggal_pulang;
                                    }
                                    $start_pulang = Carbon::parse($tanggal_mulai_pulang . ' ' . $kategori_masuk_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                    $end_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_masuk_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                }

                                $logs_absensi_masuk = $logsToday->filter(function ($log) use ($start, $end) {
                                    return Carbon::parse($log->LogTime)->between($start, $end);
                                });
                                if ($logs_absensi_masuk->isNotEmpty()) {
                                    $checkIn = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                                    if ($checkIn > $kategori_masuk_shift->jam_terlambat) {
                                        $status = 'TELAT';
                                    } else {
                                        $status = 'TEPAT WAKTU';
                                    }
                                } else {
                                    $logs_absensi_masuk_datang_awal = $logsToday->filter(function ($log) use ($start, $batas_absensi) {
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
                                        } else {
                                            if ($kategori_masuk_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                                $checkIn = 'LIBUR';
                                                $status = 'LIBUR';
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
                                                    } else if ($tukar_libur) {
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
                                // Log pulang
                                $logs_absensi_pulang = $getLogsInWindow($start_pulang, $end_pulang);
                                if ($logs_absensi_pulang->isNotEmpty()) {
                                    $checkOut = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                                    // dd(Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('Y-m-d'), $dateKey);
                                    if (Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('Y-m-d') > $dateKey) {
                                        // dd('ok');
                                        $checkOut = $checkOut . ' (' . Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('d-m-Y') . ')';
                                    }
                                } else {
                                    if ($kategori_masuk_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                        $checkOut = 'LIBUR';
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
                            }
                            // contoh hasil untuk list
                            $data_absensi[] = [
                                'tanggal' => $date->format('Y-m-d'),
                                'masuk'   => $checkIn,
                                'pulang'  => $checkOut,
                                'detail_masuk' => $detail_masuk,
                                'detail_pulang' => $detail_pulang,
                                'shift'   => 'SHIFT: ' . $kategori_masuk_shift->nama_shift,
                                'status' => $status
                            ];
                        }
                    }
                    // dd($data_absensi);
                }
                $mapping_shift      = MappingShift::with('Shift')
                    ->where('karyawan_id', $user_login->id)
                    ->where('tanggal_masuk', $tglskrg)
                    ->whereNotNull('legal_number_mapping')
                    ->first();
                // dd($mapping_shift);
                if ($mapping_shift != null) {
                    if ($mapping_shift->nama_shift == 'LIBUR') {
                        $jam_kerja = 'LIBUR';
                    } else {
                        $jam_kerja = $mapping_shift->Shift->jam_masuk . ' - ' . $mapping_shift->Shift->jam_keluar;
                    }
                    $logs_absensi_masuk = $user_login->Absensi->filter(function ($log) use ($mapping_shift) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($mapping_shift->tanggal_masuk . ' ' . $mapping_shift->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($mapping_shift->tanggal_masuk . ' ' . $mapping_shift->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                        return $getcheckIn->between($start, $end);
                    });
                    // dd($mapping_shift, $logs_absensi_masuk, $tglskrg);
                    if ($logs_absensi_masuk->isNotEmpty()) {
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        if ($checkIn > $mapping_shift->Shift->jam_terlambat) {
                            $checkIn = ' <span class="badge light badge-sm badge-warning absen_masuk_text"><strong>' . $checkIn . ' WIB</strong></span>';
                        } else {
                            $checkIn = '<span class="badge light badge-sm badge-success absen_masuk_text"><strong>' . $checkIn . ' WIB</strong></span>';
                        }
                    } else {
                        $logs_absensi_masuk_datang_awal = $user_login->Absensi->filter(function ($log) use ($mapping_shift) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($mapping_shift->tanggal_masuk . ' ' . $mapping_shift->Shift->jam_min_masuk)->subHours(3)->format('Y-m-d H:i:59');
                            $end = Carbon::parse($mapping_shift->tanggal_masuk . ' ' . $mapping_shift->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                            return $getcheckIn->between($start, $end);
                        });
                        // dd($logs_absensi_masuk_datang_awal);
                        if ($logs_absensi_masuk_datang_awal->isNotEmpty()) {
                            $getCheckIn  = Carbon::parse($logs_absensi_masuk_datang_awal->min('LogTime'))->format('H:i');
                            $checkIn = '<span class="badge light badge-sm badge-info absen_masuk_text"><strong>' . $getCheckIn . ' WIB</strong></span>';
                        } else {
                            $checkIn = '-';
                        }
                    }
                    if (
                        $mapping_shift->Shift->tgl_pulang_besok == 1
                        && $mapping_shift->Shift->jam_pulang_cepat > $mapping_shift->Shift->jam_keluar
                    ) {
                        $tanggal_mulai_pulang = $mapping_shift->tanggal_masuk;
                    } else {
                        $tanggal_mulai_pulang = $mapping_shift->tanggal_pulang;
                    }
                    $start_pulang = Carbon::parse($tanggal_mulai_pulang . ' ' . $mapping_shift->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                    $end_pulang = Carbon::parse($mapping_shift->tanggal_pulang . ' ' . $mapping_shift->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                    $logs_absensi_pulang = $getLogsInWindow($start_pulang, $end_pulang);
                    // dd($logs_absensi_pulang);
                    // return $logs_absensi_pulang;
                    if ($logs_absensi_pulang->isNotEmpty()) {
                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');

                        if ($checkOut < $mapping_shift->jam_keluar) {
                            $checkOut = '<span class="badge light badge-sm badge-danger absen_masuk_text"><strong>' . $checkOut . ' WIB</strong></span>';
                        } else {
                            $checkOut = '<span class="badge light badge-sm badge-success absen_masuk_text"><strong>' . $checkOut . ' WIB</strong></span>';
                        }
                    } else {
                        $checkOut = '-';
                    }
                } else {
                    $count_absen_hadir = null;
                    $count_telat = null;
                    $jam_kerja = null;
                    $checkIn = null;
                    $checkOut = null;
                }
            }
            $libur_non_shift = $user_login->MappingShift
                ->whereBetween('tanggal_masuk', [$startDate, $endDate])

                ->whereNotNull('is_active')
                ->where('nama_shift', 'LIBUR')
                ->count();
            $count_absen_hadir = ($tepat_waktu + $telat_ringan + $telat_berat + $tetap_absen - $libur_non_shift);

            $count_telat = ($telat_ringan + $telat_berat);
            $penempatan = Site::where('id', $user_login->penempatan_kerja)->first();
            $result = [
                'code' => 200,
                'count_absen_hadir' => $count_absen_hadir,
                'count_telat'       => $count_telat,
                'count_izin'        => $count_izin,
                'count_cuti'        => $count_cuti,
                'data_absensi'      => $data_absensi,
                'thnskrg'           => $thnskrg,
                'jam_kerja'         => $jam_kerja,
                'mapping_shift'     => $mapping_shift,
                'checkIn'           => $checkIn,
                'checkOut'          => $checkOut,
                'penempatan'        => $penempatan
            ];
            return response()->json($result); //$result;
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }


    public function HomeAbsen(Request $request)
    {
        // dd('p');
        $user_login = Auth::user()->karyawan_id;
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $date_now = date('Y');
        $month_now = date('m');
        $month_yesterday = \Carbon\Carbon::now()->subMonthsNoOverflow()->isoFormat('MM');
        $month_yesterday1 = \Carbon\Carbon::now()->subMonthsNoOverflow()->isoFormat('MMMM');
        $month_now1 = \Carbon\Carbon::now()->isoFormat('MMMM');
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = "";
        $tglskrg = date('Y-m-d');
        $tglkmrn = date('Y-m-d', strtotime('-1 days'));
        $tidak_masuk = MappingShift::where('status_absen', 'TIDAK HADIR KERJA')
            ->where('karyawan_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("COUNT(*) as count"))
            ->whereYear('tanggal_masuk', date('Y'))
            ->groupBy(DB::raw("Month(tanggal_masuk)"))
            ->pluck('count');
        $masuk = MappingShift::where('mapping_shifts.status_absen', 'HADIR KERJA')
            ->where('karyawan_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("COUNT(mapping_shifts.tanggal_masuk) as count"))
            ->whereYear('tanggal_masuk', date('Y'))
            ->groupBy(DB::raw("Month(tanggal_masuk)"))
            ->pluck('count');
        $telat = MappingShift::where('status_absen', 'HADIR KERJA')
            ->where('keterangan_absensi', 'TELAT HADIR')
            ->where('karyawan_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("COUNT(*) as count"))
            ->whereYear('tanggal_masuk', date('Y'))
            ->groupBy(DB::raw("Month(tanggal_masuk)"))
            ->pluck('count');
        // dd();

        $get_mapping = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tglkmrn)->first();
        if ($get_mapping == '' || $get_mapping == NULL) {
            $tanggal = $tglskrg;
            $mapping_shift = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tanggal)->first();
        } else {
            $tanggal = $tglkmrn;
            $mapping_shift = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tanggal)->first();
        }

        date_default_timezone_set('Asia/Jakarta');
        $tglskrg = date('Y-m-d');
        $data_absen = MappingShift::where('tanggal_masuk', $tglskrg)->whereNotNull('legal_number_mapping')->where('karyawan_id', $user_karyawan->id);

        if ($request["mulai"] == null) {
            $request["mulai"] = $request["akhir"];
        }

        if ($request["akhir"] == null) {
            $request["akhir"] = $request["mulai"];
        }

        if ($request["mulai"] && $request["akhir"]) {
            $data_absen = MappingShift::where('karyawan_id', $user_karyawan->id)->whereNotNull('legal_number_mapping')->whereBetween('tanggal_masuk', [$request["mulai"], $request["akhir"]]);
        }
        // dd($mapping_shift);
        if ($mapping_shift == NULL) {
            $request->session()->flash('Mapping_shift_kosong');
            return redirect('home');
        }
        $timenow = Carbon::now()->format('H:i:s');
        $hours_1_masuk = Carbon::parse($mapping_shift->shift->jam_masuk)->subHour(1)->format('H:i:s');
        // dd($hours_1_masuk);
        $jam_masuk = Carbon::parse($mapping_shift->shift->jam_masuk)->format('H:i:s');
        $hours_1_pulang = Carbon::parse($mapping_shift->shift->jam_keluar)->subHour(-1)->format('H:i:s');
        $get_nama_shift = $mapping_shift->shift->nama_shift;
        // dd($hours_1_pulang);
        if ($get_nama_shift == 'Malam') {
            if ($hours_1_pulang > $timenow) {
                // dd('1');
                // dd('oke');
                $status_absen_skrg = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tglkmrn)->orderBy('tanggal_masuk', 'DESC')->first();
            } else {
                // dd('2');
                $status_absen_skrg = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tglskrg)->orderBy('tanggal_masuk', 'DESC')->first();
            }
        } else {
            $status_absen_skrg = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tglskrg)->orderBy('tanggal_masuk', 'DESC')->first();
            // dd($status_absen_skrg);
        }
        // dd($status_absen_skrg->status_absen);
        if ($status_absen_skrg == NULL) {
            $request->session()->flash('Mapping_shift_kosong');
            return redirect('home');
        }
        if ($status_absen_skrg->status_absen == "LIBUR") {
            $request->session()->flash('jam_kerja_libur');
            return redirect('home');
        }
        $cek_jam_maks_kerja = MappingShift::With('Shift')->whereNotNull('legal_number_mapping')->where('karyawan_id', $user_login)->where('tanggal_masuk', $tglskrg)->first();
        $time_now = date('H:i:s');
        $lembur_detail = LemburDetail::where('id_karyawan', $user_login)
            ->whereDate('tgl_lembur', $status_absen_skrg->tanggal_masuk)
            ->where('status', 1)
            ->first();
        $tanggal_akhir_kerja = $lembur_detail?->tgl_pulang ?? $cek_jam_maks_kerja->tanggal_masuk;
        $jam_akhir_kerja = $lembur_detail?->jam_selesai;
        // dd($cek_jam_maks_kerja->Shift->jam_keluar);
        $date1          = $jam_akhir_kerja
            ? new DateTime($tanggal_akhir_kerja . $jam_akhir_kerja)
            : new DateTime($tanggal_akhir_kerja . $cek_jam_maks_kerja->Shift->jam_keluar);
        $date2          = new DateTime($cek_jam_maks_kerja->tanggal_masuk . $time_now);
        $interval       = $date1->diff($date2);
        // dd($date1, $date2, $interval);
        if ($status_absen_skrg->jam_absen == '' || $status_absen_skrg->jam_absen == NULL) {
            if ($timenow >= $jam_masuk) {
                // print_r($interval->p);

                if ($interval->h < 6) {
                    $request->session()->flash('jam_kerja_kurang');
                } else {
                }
                return view('users.absen.index', [
                    'title' => 'My Absen',
                    'user_karyawan' => $user_karyawan,
                    'shift_karyawan' => $status_absen_skrg,
                    'status_absen_skrg' => $status_absen_skrg,
                    'data_absen' => $data_absen->get(),
                    'masuk' => array_map('intval', json_decode($masuk)),
                    'tidak_masuk' => array_map('intval', json_decode($tidak_masuk)),
                    'telat' => array_map('intval', json_decode($telat)),
                    'date_now' => $date_now,
                    'month_now1' => $month_now1,
                    'month_yesterday1' => $month_yesterday1,
                    'face' => Karyawan::where('id', $user_login)->whereNotNull('face_id')->select('id', 'name', 'face_id')->get(),
                    'karyawan' => Karyawan::where('id', $user_login)->whereNotNull('face_id')->select('id', 'name', 'face_id')->get(),
                    'angka' => 1,
                    'absensi' => MappingShift::where('tanggal_masuk', date('Y-m-d'))->whereNotNull('legal_number_mapping')->where('karyawan_id', $user_login)->get(),
                    'jumlah_absensi' => 1,
                    'faceid' => Karyawan::where('id', $user_login)->value('face_id'),

                ]);
            } else {
                if ($time_now > $hours_1_masuk) {
                    return view('users.absen.index', [
                        'title' => 'My Absen',
                        'user_karyawan' => $user_karyawan,
                        'shift_karyawan' => $status_absen_skrg,
                        'status_absen_skrg' => $status_absen_skrg,
                        'data_absen' => $data_absen->get(),
                        'masuk' => array_map('intval', json_decode($masuk)),
                        'tidak_masuk' => array_map('intval', json_decode($tidak_masuk)),
                        'telat' => array_map('intval', json_decode($telat)),
                        'date_now' => $date_now,
                        'month_now1' => $month_now1,
                        'month_yesterday1' => $month_yesterday1,

                        'face' => Karyawan::where('id', $user_login)->whereNotNull('face_id')->select('id', 'name', 'face_id')->get(),
                        'karyawan' => Karyawan::where('id', $user_login)->whereNotNull('face_id')->select('id', 'name', 'face_id')->get(),
                        'angka' => 1,
                        'absensi' => MappingShift::where('tanggal_masuk', date('Y-m-d'))->whereNotNull('legal_number_mapping')->where('karyawan_id', $user_login)->get(),
                        'jumlah_absensi' => 1,
                        'faceid' => Karyawan::where('id', $user_login)->value('face_id'),
                    ]);
                } else {
                    Alert::error('Gagal', 'Anda Belum Masuk Jam Absensi');
                    return redirect()->back()->with('Gagal', 'Anda Belum Masuk Jam Absensi');
                }
            }
        } else if ($status_absen_skrg->jam_absen != '' || $status_absen_skrg->jam_absen != NULL) {
            $date1_pulang          = new DateTime($cek_jam_maks_kerja->tanggal_pulang . $cek_jam_maks_kerja->Shift->jam_masuk);
            $date2_pulang          = new DateTime($cek_jam_maks_kerja->tanggal_pulang . $time_now);
            $interval_pulang       = $date1_pulang->diff($date2_pulang);
            // dd($interval_pulang);
            $hitung_jam_kerja = ($interval_pulang->format('%H') . ':' . $interval_pulang->format('%I') . ':' . $interval_pulang->format('%S'));
            // dd($hitung_jam_kerja);
            if ($hitung_jam_kerja <= '06:00:00') {
                $request->session()->flash('jam_kerja_kurang');
            }
            return view('users.absen.index', [
                'title' => 'My Absen',
                'shift_karyawan' => $status_absen_skrg,
                'status_absen_skrg' => $status_absen_skrg,
                'data_absen' => $data_absen->get(),
                'user_karyawan' => $user_karyawan,
                'masuk' => array_map('intval', json_decode($masuk)),
                'tidak_masuk' => array_map('intval', json_decode($tidak_masuk)),
                'telat' => array_map('intval', json_decode($telat)),
                'date_now' => $date_now,
                'month_now1' => $month_now1,
                'month_yesterday1' => $month_yesterday1,

                'face' => Karyawan::where('id', $user_login)->whereNotNull('face_id')->select('id', 'name', 'face_id')->get(),
                'karyawan' => Karyawan::where('id', $user_login)->whereNotNull('face_id')->select('id', 'name', 'face_id')->get(),
                'angka' => 1,
                'absensi' => MappingShift::where('tanggal_masuk', date('Y-m-d'))->whereNotNull('legal_number_mapping')->where('karyawan_id', $user_login)->get(),
                'jumlah_absensi' => 1,
                'faceid' => Karyawan::where('id', $user_login)->value('face_id'),
            ]);
        }
    }

    public function proses_izin_datang_terlambat(Request $request)
    {
        // dd($request->all());
        $cek_duplicate = Izin::whereDate('tanggal', $request->tanggal)->where('user_id', $request->id_user)->where('izin', $request->izin)->count();
        if ($cek_duplicate > 0) {
            return redirect('/home');
        }
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $lokasi_kerja = $user_karyawan->penempatan_kerja;
        if ($lokasi_kerja == '' || $lokasi_kerja == NULL) {
            $request->session()->flash('lokasikerjanull', 'Gagal Absen Masuk');
            return redirect('/home');
        } else {
            $cek_penugasan = MappingShift::where('id', $user_karyawan->id)->whereNotNull('legal_number_mapping')
                ->where('keterangan_absensi', 'ABSENSI PENUGASAN WILAYAH KANTOR')
                ->first();
            if ($cek_penugasan != '' || $cek_penugasan != NULL) {
                $request->session()->flash('penugasan_wilayah_kantor');
                return redirect('/home');
            } else {
                $jam_kerja = MappingShift::with('Shift')->where('karyawan_id', $user_karyawan->id)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', date('Y-m-d'))->first();
                if ($jam_kerja == '' || $jam_kerja == NULL) {
                    $request->session()->flash('mapping_kosong');
                    return redirect('/izin/dashboard');
                } else {
                    if ($request->id_user_atasan == NULL || $request->id_user_atasan == '') {
                        if ($request->level_jabatan != '1') {
                            $request->session()->flash('atasankosong');
                            return redirect('/izin/dashboard');
                        } else {
                            // No form
                            $count_tbl_izin = Izin::where('izin', $request->izin)->where('tanggal', date('Y-m-d'))->count();
                            // dd($count_tbl_izin);
                            $countstr = strlen($count_tbl_izin + 1);
                            if ($countstr == '1') {
                                $no = '000' . $count_tbl_izin + 1;
                            } else if ($countstr == '2') {
                                $no = '00' . $count_tbl_izin + 1;
                            } else if ($countstr == '3') {
                                $no = '0' . $count_tbl_izin + 1;
                            } else {
                                $no = $count_tbl_izin + 1;
                            }
                            $no_form = $user_karyawan->kontrak_kerja . '/SK/FKDT/' . date('Y/m/d') . '/' . $no;
                            // dd($no_form);
                            $jam_terlambat = $request->terlambat;
                            $jam_masuk_kerja = $request->jam_masuk;
                            $jam_pulang_cepat = NULL;
                            $img_name = NULL;
                            $jam_keluar = NULL;
                            $jam_kembali = NULL;
                            $id_backup = NULL;
                            $name_backup = NULL;
                            $catatan_backup = NULL;
                            $tanggal = $request->tanggal;
                            $tanggal_selesai = NULL;
                            $folderPath     = public_path('signature/izin/');
                            $image_parts    = explode(";base64,", $request->signature);
                            $image_type_aux = explode("image/", $image_parts[0]);
                            $image_type     = $image_type_aux[1];
                            $image_base64   = base64_decode($image_parts[1]);
                            $uniqid         = date('y-m-d') . '-' . uniqid();
                            $file           = $folderPath . $uniqid . '.' . $image_type;
                            file_put_contents($file, $image_base64);
                            $data                   = new Izin();
                            $data->user_id          = $request->id_user;
                            $data->departements_id  = Departemen::where('id', $request["departements"])->value('id');
                            $data->jabatan_id       = Jabatan::where('id', $request["jabatan"])->value('id');
                            $data->divisi_id        = Divisi::where('id', $request["divisi"])->value('id');
                            $data->telp             = $request->telp;
                            $data->terlambat        = $jam_terlambat;
                            $data->jam_masuk_kerja  = $jam_masuk_kerja;
                            $data->pulang_cepat     = $jam_pulang_cepat;
                            $data->email            = $request->email;
                            $data->fullname         = $request->fullname;
                            $data->izin             = $request->izin;
                            $data->user_id_backup   = $id_backup;
                            $data->user_name_backup = $name_backup;
                            $data->catatan_backup = $catatan_backup;
                            $data->foto_izin        = $img_name;
                            $data->jam_keluar        = $jam_keluar;
                            $data->jam_kembali        = $jam_kembali;
                            $data->tanggal          = $tanggal;
                            $data->tanggal_selesai   = $tanggal_selesai;
                            $data->jam              = $request->jam;
                            $data->keterangan_izin  = $request->keterangan_izin;
                            $data->approve_atasan   = $request->approve_atasan;
                            $data->id_approve_atasan = $request->id_user_atasan;
                            $data->status_izin      = 2;
                            $data->no_form_izin      = $no_form;
                            $data->ttd_pengajuan    = $uniqid;
                            $data->waktu_ttd_pengajuan    = date('Y-m-d');
                            $data->ttd_atasan      = NULL;
                            $data->waktu_approve      = NULL;
                            $data->save();
                            // jam telat
                            // dd($request->jam_masuk);
                            $date1          = new DateTime($tanggal . $request->jam_masuk);
                            $date2          = new DateTime($tanggal . $request->jam);
                            $interval       = $date1->diff($date2);
                            $toleransi_mnt = '00:05:00';
                            $jml_all = ($interval->format('%H') . ':' . $interval->format('%I') . ':' . $interval->format('%S'));
                            // $jum_all_toleransi = ($jml_all - $toleransi_mnt);
                            //
                            $lokasi_kantor = Lokasi::where('lokasi_kantor', $lokasi_kerja)->first();
                            $lat_kantor = $lokasi_kantor->lat_kantor;
                            $long_kantor = $lokasi_kantor->long_kantor;
                            // absen
                            $update = MappingShift::where('id', $request->id_mapping)->first();
                            $update->jam_absen = date('H:i:s');
                            $update->telat = $jml_all;
                            $update->foto_jam_absen = $request['foto_jam_absen'];
                            $update->lat_absen = $request['lat_absen'];
                            $update->long_absen = $request['long_absen'];
                            $update->jarak_masuk = $request['jarak_masuk'];
                            $update->lokasi_absen = $request['lokasi_absen'];
                            $update->status_absen = 'HADIR KERJA';
                            $update->keterangan_absensi = 'TELAT HADIR';
                            $update->kelengkapan_absensi = 'BELUM PRESENSI PULANG';
                            $update->update();

                            ActivityLog::create([
                                'user_id' => Auth::user()->id,
                                'object_id' => $update->id,
                                'kategory_activity' => 'ABSENSI',
                                'activity' => 'Absen Masuk',
                                'description' => 'Absen Masuk Tanggal ' . $tanggal . ' Jam ' . $update->jam_absen . ' Keterangan ' . $update->keterangan_absensi,
                                'read_status' => 0
                            ]);
                            ActivityLog::create([
                                'user_id' => Auth::user()->id,
                                'object_id' => $update->id,
                                'kategory_activity' => 'IZIN',
                                'activity' => 'Izin Datang Terlambat',
                                'description' => 'Pengajuan Datang Terlambat Tanggal ' . $tanggal . ' Jam ' . $update->jam_absen . ', Terlmbat : ' . $data->terlambat . ' Keterangan ' . $data->keterangan_izin,
                                'read_status' => 0
                            ]);
                            $request->session()->flash('absenmasuksuccess');
                            return redirect('home');
                        }
                    } else {
                        // No form
                        $count_tbl_izin = Izin::where('izin', $request->izin)->where('tanggal', date('Y-m-d'))->count();
                        // dd($count_tbl_izin);
                        $countstr = strlen($count_tbl_izin + 1);
                        if ($countstr == '1') {
                            $no = '000' . $count_tbl_izin + 1;
                        } else if ($countstr == '2') {
                            $no = '00' . $count_tbl_izin + 1;
                        } else if ($countstr == '3') {
                            $no = '0' . $count_tbl_izin + 1;
                        } else {
                            $no = $count_tbl_izin + 1;
                        }
                        $no_form = $user_karyawan->kontrak_kerja . '/SK/FKDT/' . date('Y/m/d') . '/' . $no;
                        $jam_terlambat = $request->terlambat;
                        $jam_masuk_kerja = $request->jam_masuk;
                        $jam_pulang_cepat = NULL;
                        $img_name = NULL;
                        $jam_keluar = NULL;
                        $jam_kembali = NULL;
                        $id_backup = NULL;
                        $name_backup = NULL;
                        $catatan_backup = NULL;
                        $tanggal = $request->tanggal;
                        $tanggal_selesai = NULL;
                        $folderPath     = public_path('signature/izin/');
                        $image_parts    = explode(";base64,", $request->signature);
                        $image_type_aux = explode("image/", $image_parts[0]);
                        $image_type     = $image_type_aux[1];
                        $image_base64   = base64_decode($image_parts[1]);
                        $uniqid         = date('y-m-d') . '-' . uniqid();
                        $file           = $folderPath . $uniqid . '.' . $image_type;
                        file_put_contents($file, $image_base64);
                        $data                   = new Izin();
                        $data->user_id          = $request->id_user;
                        $data->departements_id  = Departemen::where('id', $request["departements"])->value('id');
                        $data->jabatan_id       = Jabatan::where('id', $request["jabatan"])->value('id');
                        $data->divisi_id        = Divisi::where('id', $request["divisi"])->value('id');
                        $data->telp             = $request->telp;
                        $data->terlambat        = $jam_terlambat;
                        $data->jam_masuk_kerja  = $jam_masuk_kerja;
                        $data->pulang_cepat     = $jam_pulang_cepat;
                        $data->email            = $request->email;
                        $data->fullname         = $request->fullname;
                        $data->izin             = $request->izin;
                        $data->user_id_backup   = $id_backup;
                        $data->user_name_backup = $name_backup;
                        $data->catatan_backup = $catatan_backup;
                        $data->foto_izin        = $img_name;
                        $data->jam_keluar        = $jam_keluar;
                        $data->jam_kembali        = $jam_kembali;
                        $data->tanggal          = $tanggal;
                        $data->tanggal_selesai   = $tanggal_selesai;
                        $data->jam              = $request->jam;
                        $data->keterangan_izin  = $request->keterangan_izin;
                        $data->approve_atasan   = $request->approve_atasan;
                        $data->id_approve_atasan = $request->id_user_atasan;
                        $data->status_izin      = 1;
                        $data->no_form_izin      = $no_form;
                        $data->ttd_pengajuan    = $uniqid;
                        $data->waktu_ttd_pengajuan    = date('Y-m-d');
                        $data->ttd_atasan      = NULL;
                        $data->waktu_approve      = NULL;
                        $data->save();
                        // jam telat
                        $date1          = new DateTime($tanggal . $request->jam_masuk);
                        $date2          = new DateTime($tanggal . $request->jam);
                        $interval       = $date1->diff($date2);
                        $toleransi_mnt = 05;
                        $jum_mnt  = $interval->format('%i');
                        $jum_mnt_toleransi = ($jum_mnt - $toleransi_mnt);
                        $jum_hours  = $interval->format('%H');
                        $jum_second  = $interval->format('%S');
                        $jml_all = ($jum_hours . ':' . $jum_mnt_toleransi . ':' . $jum_second);
                        // dd($jml_all);
                        //
                        $lokasi_kantor = Lokasi::where('lokasi_kantor', $lokasi_kerja)->first();
                        $lat_kantor = $lokasi_kantor->lat_kantor;
                        $long_kantor = $lokasi_kantor->long_kantor;
                        // absen
                        $update = MappingShift::where('id', $request->id_mapping)->first();
                        $update->jam_absen = date('H:i:s');
                        $update->telat = $jml_all;
                        $update->foto_jam_absen = $request['foto_jam_absen'];
                        $update->lat_absen = $request['lat_absen'];
                        $update->long_absen = $request['long_absen'];
                        $update->jarak_masuk = $request['jarak_masuk'];
                        $update->lokasi_absen = $request['lokasi_absen'];
                        $update->status_absen = 'HADIR KERJA';
                        $update->keterangan_absensi = 'TELAT HADIR';
                        $update->kelengkapan_absensi = 'BELUM PRESENSI PULANG';
                        $update->update();

                        ActivityLog::create([
                            'user_id' => Auth::user()->id,
                            'object_id' => $update->id,
                            'kategory_activity' => 'ABSENSI',
                            'activity' => 'Absen Masuk',
                            'description' => 'Absen Masuk Tanggal ' . $tanggal . ' Jam ' . $update->jam_absen . ' Keterangan ' . $update->keterangan_absensi,
                            'read_status' => 0
                        ]);

                        $request->session()->flash('absenmasuksuccess');
                        return redirect('home');
                    }
                }
            }
        }
    }
    public function myLocation(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        // dd($request->all());
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        return view('users.absen.locationmaps', [
            'title' => 'Maps',
            'lat' => $request->lat_location,
            'long' => $request->long_location,
            'lokasi_kantor' => Lokasi::first(),
            'user_karyawan' => $user_karyawan
        ]);
    }

    public function proses_izin_pulang_cepats(Request $request)
    {
        // dd($request->all());
        $cek_duplicate = Izin::whereDate('tanggal', $request->tanggal)->where('user_id', $request->id_user)->where('izin', $request->izin)->count();
        if ($cek_duplicate > 0) {
            return redirect('/home');
        }
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $jam_kerja = MappingShift::with('Shift')->where('user_id', $user_karyawan->id)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', date('Y-m-d'))->first();
        if ($jam_kerja == '' || $jam_kerja == NULL) {
            $request->session()->flash('mapping_kosong');
            return redirect('/home');
        } else {
            // dd('ok');
            if ($request->id_user_atasan == NULL || $request->id_user_atasan == '') {
                if ($request->level_jabatan != '1') {
                    $request->session()->flash('atasankosong');
                    return redirect('/home');
                } else {
                    $count_tbl_izin = Izin::whereDate('tanggal', date('Y-m-d'))->where('izin', $request->izin)->count();
                    // dd($count_tbl_izin);
                    $countstr = strlen($count_tbl_izin + 1);
                    if ($countstr == '1') {
                        $no = '000' . $count_tbl_izin + 1;
                    } else if ($countstr == '2') {
                        $no = '00' . $count_tbl_izin + 1;
                    } else if ($countstr == '3') {
                        $no = '0' . $count_tbl_izin + 1;
                    } else {
                        $no = $count_tbl_izin + 1;
                    }
                    $no_form = $user_karyawan->kontrak_kerja . '/IP/' . date('Y/m/d') . '/' . $no;
                    $tgl_skrg = date('Y-m-d');
                    $akhir = new DateTime($tgl_skrg . $request["jam_pulang"]);
                    $awal  = new DateTime($tgl_skrg . $request["jam_pulang_cepat"]);
                    $diff  = $awal->diff($akhir);
                    $hours = $diff->format('%H');
                    $minutes = $diff->format('%I');
                    $second = $diff->format('%S');
                    $hitung_pulang_cepat = ($hours . ':' . $minutes . ':' . $second);
                    // dd($hitung_pulang_cepat);
                    $folderPath     = public_path('signature/izin/');
                    $image_parts    = explode(";base64,", $request->signature);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type     = $image_type_aux[1];
                    $image_base64   = base64_decode($image_parts[1]);
                    $uniqid         = date('y-m-d') . '-' . uniqid();
                    $file           = $folderPath . $uniqid . '.' . $image_type;
                    file_put_contents($file, $image_base64);
                    $data                   = new Izin();
                    $data->user_id          = $request->id_user;
                    $data->departements_id  = Departemen::where('id', $request["departements"])->value('id');
                    $data->jabatan_id       = Jabatan::where('id', $request["jabatan"])->value('id');
                    $data->divisi_id        = Divisi::where('id', $request["divisi"])->value('id');
                    $data->telp             = $request->telp;
                    $data->email            = $request->email;
                    $data->fullname         = $request->fullname;
                    $data->izin             = $request->izin;
                    $data->tanggal          = $request->tanggal;
                    $data->jam              = $request->jam_pulang_cepat;
                    $data->keterangan_izin  = $request->keterangan_izin;
                    $data->status_izin      = 1;
                    $data->ttd_pengajuan    = $uniqid;
                    $data->waktu_ttd_pengajuan  = date('Y-m-d');
                    $data->ttd_atasan      = NULL;
                    $data->no_form_izin       = $no_form;
                    $data->waktu_approve      = NULL;
                    $data->save();

                    $update = MappingShift::where('id', $request['id_mapping'])->first();
                    $update->jam_pulang           = $request['jam_pulang_cepat'];
                    $update->foto_jam_pulang      = $request['foto_jam_pulang'];
                    $update->lat_pulang           = $request['lat_pulang'];
                    $update->long_pulang          = $request['long_pulang'];
                    $update->pulang_cepat         = $hitung_pulang_cepat;
                    $update->jarak_pulang         = $request['jarak_pulang'];
                    $update->total_jam_kerja         = $request['total_jam_kerja'];
                    $update->lokasi_absen_pulang  = $request['lokasi_absen_pulang'];
                    $update->keterangan_absensi_pulang = 'PULANG CEPAT';
                    $update->kelengkapan_absensi  = 'PRESENSI LENGKAP';
                    $update->update();

                    ActivityLog::create([
                        'user_id' => Auth::user()->id,
                        'object_id' => $update->id,
                        'kategory_activity' => 'ABSENSI',
                        'activity' => 'Absen Pulang',
                        'description' => 'Absen Pulang Tanggal ' . $update->tanggal_pulang . ' Jam ' . $update->jam_pulang . ' Keterangan  ' . $update->keterangan_absensi_pulang,
                        'read_status' => 0

                    ]);
                    ActivityLog::create([
                        'user_id' => Auth::user()->id,
                        'object_id' => $data->id,
                        'kategory_activity' => 'IZIN',
                        'activity' => 'Izin Pulang Cepat',
                        'description' => 'Pengajuan Izin Pulang Cepat Pulang Tanggal ' . $data->tanggal . ' Jam ' . $data->pulang_cepat . ' Keterangan  ' . $data->keterangan_izin,
                        'read_status' => 0

                    ]);
                    $request->session()->flash('absenpulangsuccess', 'Berhasil Absen Pulang');
                    return redirect('/home');
                }
            } else {
                // No form
                $count_tbl_izin = Izin::whereDate('tanggal', date('Y-m-d'))->where('izin', $request->izin)->count();
                // dd($count_tbl_izin);
                $countstr = strlen($count_tbl_izin + 1);
                if ($countstr == '1') {
                    $no = '000' . $count_tbl_izin + 1;
                } else if ($countstr == '2') {
                    $no = '00' . $count_tbl_izin + 1;
                } else if ($countstr == '3') {
                    $no = '0' . $count_tbl_izin + 1;
                } else {
                    $no = $count_tbl_izin + 1;
                }

                // $req_plg_cpt = new DateTime(date('Y-m-d') . $request->jam_pulang_cepat);
                // $req_jm_klr = new DateTime(date('Y-m-d') . $jam_kerja->Shift->jam_keluar);
                // $jam_plg_cpt = $req_plg_cpt->diff($req_jm_klr);
                // if ($jam_plg_cpt->h == 3 && $jam_plg_cpt->i > 0) {
                // }
                if ($jam_kerja->jam_absen == '' && $jam_kerja->jam_pulang == '') {
                    $request->session()->flash('absen_masuk_kosong');
                    return redirect('/home');
                } else if ($jam_kerja->jam_pulang != '') {
                    $request->session()->flash('absen_pulang_terisi');
                    return redirect('/home');
                } else {
                    // dd($request->all());
                    $id_backup = NULL;
                    $name_backup = NULL;
                    $jam_pulang_cepat = $request->jam_pulang_cepat;
                    $jam_terlambat = NULL;
                    $jam_masuk_kerja = NULL;
                    $img_name = NULL;
                    $jam_keluar = NULL;
                    $jam_kembali = NULL;
                    $catatan_backup = NULL;
                    $tanggal = date('Y-m-d');
                    $tanggal_selesai = NULL;
                    $no_form = $user_karyawan->kontrak_kerja . '/IP/' . date('Y/m/d') . '/' . $no;
                }
                $tgl_skrg = date('Y-m-d');
                $akhir = new DateTime($tgl_skrg . $request["jam_pulang"]);
                $awal  = new DateTime($tgl_skrg . $request["jam_pulang_cepat"]);
                $diff  = $awal->diff($akhir);
                $hours = $diff->format('%H');
                $minutes = $diff->format('%I');
                $second = $diff->format('%S');
                $hitung_pulang_cepat = ($hours . ':' . $minutes . ':' . $second);
                // dd($hitung_pulang_cepat);
                $folderPath     = public_path('signature/izin/');
                $image_parts    = explode(";base64,", $request->signature);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type     = $image_type_aux[1];
                $image_base64   = base64_decode($image_parts[1]);
                $uniqid         = date('y-m-d') . '-' . uniqid();
                $file           = $folderPath . $uniqid . '.' . $image_type;
                file_put_contents($file, $image_base64);
                // dd($request->all());
                $data                   = new Izin();
                $data->user_id          = $request->id_user;
                $data->departements_id  = Departemen::where('id', $request["departements"])->value('id');
                $data->jabatan_id       = Jabatan::where('id', $request["jabatan"])->value('id');
                $data->divisi_id        = Divisi::where('id', $request["divisi"])->value('id');
                $data->telp             = $request->telp;
                $data->terlambat        = $jam_terlambat;
                $data->jam_masuk_kerja  = $jam_masuk_kerja;
                $data->pulang_cepat     = $jam_pulang_cepat;
                $data->email            = $request->email;
                $data->fullname         = $request->fullname;
                $data->izin             = $request->izin;
                $data->tanggal          = $tanggal;
                $data->jam              = $jam_pulang_cepat;
                $data->keterangan_izin  = $request->keterangan_izin;
                $data->approve_atasan   = $request->approve_atasan;
                $data->id_approve_atasan = $request->id_user_atasan;
                $data->status_izin      = 1;
                $data->ttd_pengajuan    = $uniqid;
                $data->waktu_ttd_pengajuan  = date('Y-m-d');
                $data->no_form_izin     = $no_form;
                $data->ttd_atasan       = NULL;
                $data->waktu_approve    = NULL;
                $data->save();

                $update = MappingShift::where('id', $request['id_mapping'])->first();
                $update->jam_pulang           = $request['jam_pulang_cepat'];
                $update->foto_jam_pulang      = $request['foto_jam_pulang'];
                $update->lat_pulang           = $request['lat_pulang'];
                $update->long_pulang          = $request['long_pulang'];
                $update->pulang_cepat         = $hitung_pulang_cepat;
                $update->jarak_pulang         = $request['jarak_pulang'];
                $update->lokasi_absen_pulang  = $request['lokasi_absen_pulang'];
                $update->total_jam_kerja         = $request['total_jam_kerja'];
                $update->keterangan_absensi_pulang = 'PULANG CEPAT';
                $update->kelengkapan_absensi  = 'PRESENSI LENGKAP';
                $update->update();
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'object_id' => $update->id,
                    'kategory_activity' => 'ABSENSI',
                    'activity' => 'Absen Pulang',
                    'description' => 'Absen Pulang Tanggal ' . $update->tanggal_pulang . ' Jam ' . $update->jam_pulang . ' Keterangan  ' . $update->keterangan_absensi_pulang,
                    'read_status' => 0

                ]);
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'object_id' => $data->id,
                    'kategory_activity' => 'IZIN',
                    'activity' => 'Izin Pulang Cepat',
                    'description' => 'Pengajuan Izin Pulang Cepat Pulang Tanggal ' . $data->tanggal . ' Jam ' . $data->pulang_cepat . ' Keterangan  ' . $data->keterangan_izin,
                    'read_status' => 0

                ]);
                $request->session()->flash('absenpulangsuccess', 'Berhasil Absen Pulang');
                return redirect('/home');
            }
        }
    }
    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function dataAbsen(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tglskrg = date('Y-m-d');
        $data_absen = MappingShift::where('tanggal_masuk', $tglskrg)->whereNotNull('legal_number_mapping');

        if ($request["mulai"] == null) {
            $request["mulai"] = $request["akhir"];
        }

        if ($request["akhir"] == null) {
            $request["akhir"] = $request["mulai"];
        }

        if ($request["user_id"] && $request["mulai"] && $request["akhir"]) {
            $data_absen = MappingShift::where('karyawan_id', $request["user_id"])->whereBetween('tanggal_masuk', [$request["mulai"], $request["akhir"]])->whereNotNull('legal_number_mapping');
        }

        return view('absen.dataabsen', [
            'title' => 'Data Absen',
            'user' => Karyawan::select('id', 'name')->get(),
            'data_absen' => $data_absen->get()
        ]);
    }

    public function maps($lat, $long)
    {
        date_default_timezone_set('Asia/Jakarta');
        return view('users.absen.locationmaps', [
            'title' => 'Maps',
            'lat' => $lat,
            'long' => $long,
            'lokasi_kantor' => Lokasi::first()
        ]);
    }

    public function editMasuk($id)
    {
        return view('absen.editmasuk', [
            'title' => 'Edit Absen Masuk',
            'data_absen' => MappingShift::findOrFail($id),
            'lokasi_kantor' => Lokasi::first()
        ]);
    }

    public function prosesEditMasuk(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');

        $mapping_shift = MappingShift::where('id', $id)->whereNotNull('legal_number_mapping')->get();

        foreach ($mapping_shift as $mp) {
            $shift = $mp->Shift->jam_masuk;
            $tanggal = $mp->tanggal_masuk;
        }

        $awal  = strtotime($tanggal . $shift);
        $akhir = strtotime($tanggal . $request["jam_absen"]);
        $diff  = $akhir - $awal;

        if ($diff <= 0) {
            $request["telat"] = 0;
        } else {
            $request["telat"] = $diff;
        }

        $lokasi_kantor = Lokasi::first();
        $lat_kantor = $lokasi_kantor->lat_kantor;
        $long_kantor = $lokasi_kantor->long_kantor;

        $request["jarak_masuk"] = $this->distance($request["lat_absen"], $request["long_absen"], $lat_kantor, $long_kantor, "K") * 1000;

        $validatedData = $request->validate([
            'jam_absen' => 'required',
            'telat' => 'nullable',
            'foto_jam_absen' => 'image|max:5000',
            'lat_absen' => 'required',
            'long_absen' => 'required',
            'jarak_masuk' => 'required',
            'status_absen' => 'required'
        ]);

        if ($request->file('foto_jam_absen')) {
            if ($request->foto_jam_absen_lama) {
                Storage::delete($request->foto_jam_absen_lama);
            }
            $validatedData['foto_jam_absen'] = $request->file('foto_jam_absen')->store('foto_jam_absen');
        }

        MappingShift::where('id', $id)->update($validatedData);
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'tambah',
            'description' => 'Edit Absen Masuk Pada Tanggal ' . $tanggal
        ]);
        return redirect('/data-absen')->with('success', 'Berhasil Edit Absen Masuk (Manual)');
    }

    public function editPulang($id)
    {
        return view('absen.editpulang', [
            'title' => 'Edit Absen Pulang',
            'data_absen' => MappingShift::findOrFail($id),
            'lokasi_kantor' => Lokasi::first()
        ]);
    }

    public function prosesEditPulang(Request $request, $id)
    {
        $mapping_shift = MappingShift::where('id', $id)->whereNotNull('legal_number_mapping')->get();
        foreach ($mapping_shift as $mp) {
            $shiftmasuk = $mp->Shift->jam_masuk;
            $shiftpulang = $mp->Shift->jam_keluar;
            $tanggal = $mp->tanggal_masuk;
        }
        $new_tanggal = "";
        $timeMasuk = strtotime($shiftmasuk);
        $timePulang = strtotime($shiftpulang);


        if ($timePulang < $timeMasuk) {
            $new_tanggal = date('Y-m-d', strtotime('+1 days', strtotime($tanggal)));
        } else {
            $new_tanggal = $tanggal;
        }

        $akhir = strtotime($new_tanggal . $shiftpulang);
        $awal  = strtotime($new_tanggal . $request["jam_pulang"]);
        $diff  = $akhir - $awal;

        if ($diff <= 0) {
            $request["pulang_cepat"] = 0;
        } else {
            $request["pulang_cepat"] = $diff;
        }

        $lokasi_kantor = Lokasi::first();
        $lat_kantor = $lokasi_kantor->lat_kantor;
        $long_kantor = $lokasi_kantor->long_kantor;

        $request["jarak_pulang"] = $this->distance($request["lat_pulang"], $request["long_pulang"], $lat_kantor, $long_kantor, "K") * 1000;

        $validatedData = $request->validate([
            'jam_pulang' => 'required',
            'foto_jam_pulang' => 'image|max:5000',
            'lat_pulang' => 'required',
            'long_pulang' => 'required',
            'pulang_cepat' => 'required',
            'jarak_pulang' => 'required'
        ]);

        if ($request->file('foto_jam_pulang')) {
            if ($request->foto_jam_pulang_lama) {
                Storage::delete($request->foto_jam_pulang_lama);
            }
            $validatedData['foto_jam_pulang'] = $request->file('foto_jam_pulang')->store('foto_jam_pulang');
        }

        MappingShift::where('id', $id)->update($validatedData);
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'tambah',
            'description' => 'Edit Absen Pulang Pada Tanggal ' . $tanggal
        ]);

        return redirect('/data-absen')->with('success', 'Berhasil Edit Absen Pulang (Manual)');
    }

    public function deleteAdmin($id)
    {
        $delete = MappingShift::find($id);
        Storage::delete($delete->foto_jam_absen);
        Storage::delete($delete->foto_jam_pulang);
        $delete->delete();
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'activity' => 'hapus',
            'description' => 'Hapus Absen'
        ]);
        return redirect('/data-absen')->with('success', 'Data Berhasil di Delete');
    }

    public function myAbsen(Request $request)
    {
        $user_karyawan = Karyawan::where('id', Auth::user()->karyawan_id)->first();
        $date_now = date('Y');
        $month_now = date('m');
        $month_yesterday = \Carbon\Carbon::now()->subMonthsNoOverflow()->isoFormat('MM');
        $month_yesterday1 = \Carbon\Carbon::now()->subMonthsNoOverflow()->isoFormat('MMMM');
        $month_now1 = \Carbon\Carbon::now()->isoFormat('MMMM');
        date_default_timezone_set('Asia/Jakarta');
        $user_login = $user_karyawan->id;
        $tanggal = "";
        $tglskrg = date('Y-m-d');
        $tglkmrn = date('Y-m-d', strtotime('-1 days'));
        $mapping_shift = MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tglkmrn)->get();
        $tidak_masuk = MappingShift::where('status_absen', 'Tidak Masuk')
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("COUNT(*) as count"))
            ->whereYear('tanggal_masuk', date('Y'))
            ->groupBy(DB::raw("Month(tanggal_masuk)"))
            ->pluck('count');
        $masuk = MappingShift::where('mapping_shifts.status_absen', 'Masuk')
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("COUNT(mapping_shifts.tanggal_masuk) as count"))
            ->whereYear('tanggal_masuk', date('Y'))
            ->groupBy(DB::raw("Month(tanggal_masuk)"))
            ->pluck('count');
        $telat = MappingShift::where('status_absen', 'Telat')
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("COUNT(*) as count"))
            ->whereYear('tanggal_masuk', date('Y'))
            ->groupBy(DB::raw("Month(tanggal_masuk)"))
            ->pluck('count');
        // dd();
        $telat_now = MappingShift::whereMonth('tanggal_masuk', $month_now)
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("telat as count"))
            ->pluck('count');
        $telat_yesterday = MappingShift::whereMonth('tanggal_masuk', $month_yesterday)
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("telat as count"))
            ->pluck('count');
        $lembur_now = MappingShift::whereMonth('tanggal_masuk', $month_now)
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("lembur as count"))
            ->pluck('count');
        $lembur_yesterday = MappingShift::whereMonth('tanggal_masuk', $month_yesterday)
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("lembur as count"))
            ->pluck('count');
        $data_telat_now = MappingShift::whereMonth('tanggal_masuk', $month_yesterday)
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("tanggal_masuk as count"))
            ->pluck('count');
        $data_telat_yesterday = MappingShift::whereMonth('tanggal_masuk', $month_yesterday)
            ->where('user_id', $user_login)
            ->whereNotNull('legal_number_mapping')
            ->select(DB::raw("tanggal_masuk as count "))
            ->pluck('count');
        if ($mapping_shift->count() > 0) {
            foreach ($mapping_shift as $mp) {
                $jam_absen = $mp->jam_absen;
                $jam_pulang = $mp->jam_pulang;
            }
        } else {
            $jam_absen = "-";
            $jam_pulang = "-";
        }
        if ($jam_absen != null && $jam_pulang == null) {
            $tanggal = $tglkmrn;
        } else {
            $tanggal = $tglskrg;
        }

        date_default_timezone_set('Asia/Jakarta');
        $tglskrg = date('Y-m-d');
        $data_absen = MappingShift::where('tanggal_masuk', $tglskrg)->whereNotNull('legal_number_mapping')->where('user_id', Auth::user()->id);

        if ($request["mulai"] == null) {
            $request["mulai"] = $request["akhir"];
        }

        if ($request["akhir"] == null) {
            $request["akhir"] = $request["mulai"];
        }

        if ($request["mulai"] && $request["akhir"]) {
            $data_absen = MappingShift::where('karyawan_id', Auth::user()->id)->whereNotNull('legal_number_mapping')->whereBetween('tanggal_masuk', [$request["mulai"], $request["akhir"]]);
        }

        return view('absen.myabsen', [
            'title' => 'My Absen',
            'shift_karyawan' => MappingShift::where('karyawan_id', $user_login)->whereNotNull('legal_number_mapping')->where('tanggal_masuk', $tanggal)->get(),
            'data_absen' => $data_absen->get(),
            'masuk' => array_map('intval', json_decode($masuk)),
            'tidak_masuk' => array_map('intval', json_decode($tidak_masuk)),
            'telat' => array_map('intval', json_decode($telat)),
            'date_now' => $date_now,
            'month_now1' => $month_now1,
            'month_yesterday1' => $month_yesterday1,
            'telat_now' => array_map('intval', json_decode($telat_now)),
            'telat_yesterday' => array_map('intval', json_decode($telat_yesterday)),
            'lembur_now' => array_map('intval', json_decode($lembur_now)),
            'data_telat_now' => $data_telat_now,
            'data_telat_yesterday' => $data_telat_yesterday,
            'lembur_yesterday' => array_map('intval', json_decode($lembur_yesterday)),
            'user_karyawan' => $user_karyawan
        ]);
    }
}
