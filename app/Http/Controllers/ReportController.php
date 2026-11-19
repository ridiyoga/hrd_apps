<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Bagian;
use App\Models\Calendar;
use App\Models\Cuti;
use App\Models\CutiDetail;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\FingerMachine;
use App\Models\FingerUser;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\MappingShift;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\SolutionUser;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ParagonIE\Sodium\Core\Curve25519\H;
use RealRashid\SweetAlert\Facades\Alert;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class ReportController extends Controller
{

    public function getLogsOnly()
    {
        $ip   = '192.168.100.150';
        $port = 4370;

        $zk = new \Jmrashed\Zkteco\Lib\ZKTeco($ip, $port);

        if (!$zk->connect()) {
            return response()->json(['error' => 'Gagal connect']);
        }

        // dd($zk);
        $allLogs = $zk->getAttendance();
        $zk->disconnect();
        // Ambil timestamp terakhir dari DB
        $last = DB::table('last_pull')->where('id', 1)->first();

        $lastTimestamp = $last->last_timestamp;

        // Filter hanya log baru
        $newLogs = array_filter($allLogs, function ($log) use ($lastTimestamp) {
            if ($lastTimestamp === null) return true;
            return $log['timestamp'] > $lastTimestamp;
        });
        // dd($newLogs);
        // Update last timestamp
        if (!empty($newLogs)) {
            $latest = max(array_column($newLogs, 'timestamp'));
            DB::table('last_pull')->where('id', 1)->update([
                'last_timestamp' => $latest
            ]);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($newLogs),
            'data' => array_values($newLogs)
        ]);
    }
    // public function getLogsOnly()
    // {
    //     $ip   = '192.168.100.150'; // IP mesin Anda
    //     $port = 4370;

    //     try {
    //         $zk = new \Jmrashed\Zkteco\Lib\ZKTeco($ip, $port);

    //         if (!$zk->connect()) {
    //             return response()->json([
    //                 'status' => 'failed',
    //                 'message' => 'Gagal connect ke mesin fingerprint'
    //             ]);
    //         }

    //         // Ambil semua log dari mesin
    //         $attendance = $zk->getAttendance();
    //         $zk->disconnect();

    //         // Ambil tanggal hari ini (YYYY-mm-dd)
    //         $today = date('Y-m-d');

    //         // Filter log yang hanya dari hari ini
    //         $todayLogs = array_filter($attendance, function ($item) use ($today) {
    //             return strpos($item['dateTime'], $today) === 0;
    //         });

    //         return response()->json([
    //             'status' => 'success',
    //             'count'  => count($todayLogs),
    //             'data'   => array_values($todayLogs)
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function testConnection()
    {
        $ip   = '192.168.100.150'; // ganti dengan IP fingerprint
        $port = 4370;

        try {
            $zk = new \Jmrashed\Zkteco\Lib\ZKTeco($ip, $port);

            // test connect
            if ($zk->connect()) {

                // kalau connect sukses
                $zk->disconnect();

                return response()->json([
                    'status' => 'success',
                    'message' => "Berhasil connect ke fingerprint ($ip:$port)"
                ]);
            } else {

                // gagal connect
                return response()->json([
                    'status' => 'failed',
                    'message' => "Gagal connect ke mesin fingerprint",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function index(Request $request, $holding)
    {
        // dd($request->all());

        $holding = Holding::where('holding_code', $holding)->first();
        $departemen = Departemen::where('holding', $holding)->orderBy('nama_departemen', 'ASC')->get();
        $start_date = Carbon::now()->startOfMonth();
        $end_date = Carbon::now()->endOfMonth();
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $data_columns[] = ['data' => 'tanggal_' . $date->format('dmY'), 'name' => 'tanggal_' . $date->format('dmY')];
            $data_columns_header[] = '<th>' . $date->format('d/m/Y') . '</th>';
        }
        $count_period = count($period);
        // dd($period->toArray());
        $data_columns1 = str_replace(['[', ']'], '', json_encode($data_columns));
        $data_columns2 = str_replace(['"'], "'", $data_columns1);
        $data_columns3 = str_replace(["'data'"], 'data', $data_columns2);
        $data_columns4 = str_replace(["'name'"], 'name', $data_columns3);
        $datacolumn = str_replace(["'searchable'"], 'searchable', $data_columns4);
        $roleId = Auth::user();
        // dd($roleId);
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
        return view('admin.report.index', [
            'holding' => $holding,
            // 'data_finger' => $data_finger,
            'departemen' => $departemen,
            'period' => $period,
            'start_date' => $start_date,
            'datacolumn' => $datacolumn,
            'end_date' => $end_date,
            'menus' => $menus,
            'data_columns_header' => $data_columns_header,
            'count_period' => $count_period,
        ]);
    }
    public function index_kedisiplinan(Request $request, $holding)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        if ($holding == '' || $holding == null) {
            Alert::error('Error', 'Get Holding Error', 5000);
            return redirect()->route('dashboard_holding')->with('Error', 'Get Holding Error', 5000);
        }
        // dd($holding);
        date_default_timezone_set('Asia/Jakarta');

        // $bulan = date('m');
        // $tahun = date('Y');
        // $hari_per_bulan = cal_days_in_month(CAL_GREGORIAN,$bulan,$tahun);
        $tanggal_mulai = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $title = "Rekap Data Absensi Tanggal " . date('Y-m-01') . " s/d " . date('Y-m-d');

        $user = Karyawan::with('Cuti')->with('Izin')->where('status_aktif', 'AKTIF')->get();
        // dd($user->Cuti->nama_cuti);
        // dd($user);

        if ($request["mulai"] && $request["akhir"]) {
            $tanggal_mulai = $request["mulai"];
            $tanggal_akhir = $request["akhir"];
            $title = "Rekap Data Absensi Tanggal " . $tanggal_mulai . " s/d " . $tanggal_akhir;
        }
        $departemen = Departemen::where('holding', $holding->id)->orderBy('nama_departemen', 'ASC')->get();
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
        return view('admin.report.index_kedisiplinan', [
            'title' => $title,
            'data_user' => $user,
            'menus' => $menus,
            'tanggal_mulai' => $tanggal_mulai,
            'departemen' => $departemen,
            'holding' => $holding,
            'tanggal_akhir' => $tanggal_akhir
        ]);
    }
    public function detail_index_kedisiplinan($id, $holding, $start, $end, Request $request)
    {
        // dd($id);
        $holding = Holding::where('holding_code', $holding)->first();
        if ($holding == '' || $holding == null) {
            Alert::error('Error', 'Get Holding Error', 5000);
            return redirect()->route('dashboard_holding')->with('Error', 'Get Holding Error', 5000);
        }
        date_default_timezone_set('Asia/Jakarta');
        // $bulan = date('m');
        // $tahun = date('Y');
        // $hari_per_bulan = cal_days_in_month(CAL_GREGORIAN,$bulan,$tahun);
        $tanggal_mulai = date('Y-m-01');
        $tanggal_akhir = date('Y-m-d');

        $title = "Rekap Data Absensi Tanggal " . date('Y-m-01') . " s/d " . date('Y-m-d');

        $user = Karyawan::where('nomor_identitas_karyawan', $id)->where('status_aktif', 'AKTIF')->first();
        // dd($user->Cuti->nama_cuti);

        if ($start && $end) {
            $tanggal_mulai = $start;
            $tanggal_akhir = $end;
            $title = "Rekap Data Absensi Tanggal " . $tanggal_mulai . " s/d " . $tanggal_akhir;
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
        return view('admin.report.detail_kedisiplinan', [
            'title' => $title,
            'data_user' => $user,
            'menus' => $menus,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'holding' => $holding
        ]);
    }
    public function detail_datatable($id, Request $request, $holding)
    {
        if (request()->ajax()) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $period = CarbonPeriod::create($startDate, $endDate);
            // ubah jadi collection array
            $table = Karyawan::where('nomor_identitas_karyawan', $id)
                ->with('Cuti.KategoriCuti')
                ->with(['CutiDetail' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tgl_cuti', [$startDate, $endDate])->where('status', 1);
                }])
                ->with(['IzinSakitDetail' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tgl_sakit', [$startDate, $endDate])->where('status', 1);
                }])
                ->with(['IzinTidakMasukDetail' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tgl_tidak_masuk', [$startDate, $endDate])->where('status', 1);
                }])
                ->with(['PerdinDetail' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tgl_perdin', [$startDate, $endDate])->where('status', 1);
                }])
                ->with(['IzinTerlambat' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_terlambat', [$startDate, $endDate])->where('status_terlambat', 3);
                }])
                ->with(['IzinPulang' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])->where('status_pulang', 3);
                }])
                ->with(['IzinKeluar' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate])->where('status_keluar', 3);
                }])
                ->with(['IzinLibur' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_tukar', [$startDate, $endDate])->where('status_libur', 3);
                }])
                ->with(['IzinLiburMinggu' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_libur', [$startDate, $endDate])->where('status_libur', 3);
                }])
                ->with(['LemburDetail' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 1)
                        ->whereHas('Lembur', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('tanggal_lembur', [$startDate, $endDate]);
                        })
                        ->with('Lembur');
                }])
                ->with(['PresensiFoto' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('jam_presensi', [$startDate, $endDate])->where('status_presensi', 1);
                }])
                ->with(['Absensi' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('LogTime', [$startDate, $endDate])
                        ->select('EnrollNumber', 'LogTime');
                }])
                ->with(['MappingShift' => function ($q) use ($startDate, $endDate) {
                    $q->with('Shift')
                        ->whereNotNull('is_active')
                        ->where('status_approved', 1)
                        ->whereBetween('tanggal_masuk', [$startDate, $endDate])
                        ->orderBy('tanggal_masuk', 'ASC');
                }])
                ->first();
            if ($table == NULL) {
                return DataTables::of(null)
                    ->make(false);
            } else {
                $absensi_nonshift = AttendanceLog::where('EnrollNumber', $id)->whereBetween('LogTime', [$startDate, $endDate])->get();
            }
            $get_non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $data = collect($period)->map(function ($date, $index) use ($table, $id, $absensi_nonshift, $get_non_shift) {
                // pastikan $date benar-benar Carbon
                $carbonDate = $date instanceof \Carbon\Carbon ? $date : Carbon::parse($date);
                // Karyawan Shift
                $absensi_shift = MappingShift::with('Shift')->where('nik_karyawan', $id)->whereNotNull('is_active')->where('tanggal_masuk', $carbonDate->format('Y-m-d'))->first();

                if ($table->shift_id == 'NON SHIFT') {
                    $logs_absensi_masuk = $absensi_nonshift->filter(function ($log) use ($get_non_shift, $date) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                        return $getcheckIn->between($start, $end);
                    });
                    $logs_absensi_pulang = $absensi_nonshift->filter(function ($log) use ($date, $get_non_shift) {
                        $getcheckOut = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                        return $getcheckOut->between($start, $end);
                    });
                } else {
                    if ($absensi_shift == NULL) {
                        $logs_absensi_masuk = NULL;
                        $logs_absensi_pulang = NULL;
                    } else {
                        $logs_absensi_masuk = $absensi_nonshift->filter(function ($log) use ($absensi_shift, $date) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $absensi_shift->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $absensi_shift->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                            return $getcheckIn->between($start, $end);
                        });
                        $logs_absensi_pulang = $absensi_nonshift->filter(function ($log) use ($date, $absensi_shift) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($absensi_shift->tanggal_pulang . ' ' . $absensi_shift->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end = Carbon::parse($absensi_shift->tanggal_pulang . ' ' . $absensi_shift->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                            return $getcheckOut->between($start, $end);
                        });
                    }
                }
                if ($logs_absensi_masuk?->isNotEmpty()) {
                    $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                    $lokasi_absen_masuk = $logs_absensi_masuk->min('MachineIp');
                    if ($table->shift_id == 'NON SHIFT') {
                        $selisih_menit  = Carbon::parse($checkIn)->greaterThan($get_non_shift->jam_terlambat)
                            ? Carbon::parse($checkIn)->diffInMinutes($get_non_shift->jam_terlambat)
                            : 0;
                        if ($checkIn > $get_non_shift->jam_terlambat) {


                            // hitung selisih dalam menit
                            // kalau datang lebih awal atau tepat waktu

                            if ($selisih_menit <= 0) {
                                $telat = '0';
                                $keterangan_presensi_masuk = '<span>(Tepat&nbsp;Waktu)</span>';
                            } else {
                                $jam = floor($selisih_menit / 60);
                                $menit = $selisih_menit % 60;

                                if ($jam > 0 && $menit > 0) {
                                    $telat = "{$jam} jam {$menit} menit";
                                } elseif ($jam > 0) {
                                    $telat = "{$jam} jam";
                                } else {
                                    $telat = "{$menit} menit";
                                }
                                $keterangan_presensi_masuk =  '<span>(Terlambat)</span>';
                            }
                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                        } else {
                            $telat = '0';
                            $keterangan_presensi_masuk = '<span>(Tepat&nbsp;Waktu)</span>';
                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                        }
                    } else {
                        if ($absensi_shift == NULL) {
                            $selisih_menit = 0;
                        } else {
                            $selisih_menit  = Carbon::parse($checkIn)->greaterThan($absensi_shift->Shift->jam_terlambat)
                                ? Carbon::parse($checkIn)->diffInMinutes($absensi_shift->Shift->jam_terlambat)
                                : 0;
                            if ($checkIn > $absensi_shift->Shift->jam_terlambat) {


                                // hitung selisih dalam menit
                                // kalau datang lebih awal atau tepat waktu

                                if ($selisih_menit <= 0) {
                                    $telat = '0';
                                    $keterangan_presensi_masuk = '<span>(Tepat&nbsp;Waktu)</span>';
                                } else {
                                    $jam = floor($selisih_menit / 60);
                                    $menit = $selisih_menit % 60;

                                    if ($jam > 0 && $menit > 0) {
                                        $telat = "{$jam} jam {$menit} menit";
                                    } elseif ($jam > 0) {
                                        $telat = "{$jam} jam";
                                    } else {
                                        $telat = "{$menit} menit";
                                    }
                                    $keterangan_presensi_masuk =  '<span>(Terlambat)</span>';
                                }
                                $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                            } else {
                                $telat = '0';
                                $keterangan_presensi_masuk = '<span>(Tepat&nbsp;Waktu)</span>';
                                $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                            }
                        }
                        if ($checkIn > $get_non_shift->jam_terlambat) {


                            // hitung selisih dalam menit
                            // kalau datang lebih awal atau tepat waktu

                            if ($selisih_menit <= 0) {
                                $telat = '0';
                                $keterangan_presensi_masuk = '<span>(Tepat&nbsp;Waktu)</span>';
                            } else {
                                $jam = floor($selisih_menit / 60);
                                $menit = $selisih_menit % 60;

                                if ($jam > 0 && $menit > 0) {
                                    $telat = "{$jam} jam {$menit} menit";
                                } elseif ($jam > 0) {
                                    $telat = "{$jam} jam";
                                } else {
                                    $telat = "{$menit} menit";
                                }
                                $keterangan_presensi_masuk =  '<span>(Terlambat)</span>';
                            }
                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                        } else {
                            $telat = '0';
                            $keterangan_presensi_masuk = '<span>(Tepat&nbsp;Waktu)</span>';
                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                        }
                    }
                } else {
                    $logs_absensi_masuk = $absensi_nonshift->filter(function ($log) use ($date) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d'));
                        return $getcheckIn->toDateString() === $start->toDateString();
                    });
                    if ($logs_absensi_masuk->isNotEmpty()) {
                        $lokasi_absen_masuk = $logs_absensi_masuk->min('MachineIp');
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        $telat = '0';
                        $keterangan_presensi_masuk = '';
                    } else {
                        $lokasi_absen_masuk = NULL;
                        $telat = '0';
                        $keterangan_presensi_masuk = '';
                        if ($date->format('Y-m-d') >= Carbon::now()->format('Y-m-d')) {
                            $checkIn = '';
                        } else {
                            $checkIn =
                                'Tidak Presensi';
                        }
                    }
                }

                // return $logs_absensi_pulang;
                if ($logs_absensi_pulang?->isNotEmpty()) {
                    $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                    $lokasi_absen_pulang = $logs_absensi_pulang->min('MachineIp');
                    if ($table->shift_id == 'NON SHIFT') {
                        if ($checkOut < $get_non_shift->jam_keluar) {
                            $keterangan_presensi_pulang = '<span>(Pulang Cepat)</span>';
                            $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                        } else {
                            $keterangan_presensi_pulang = '<span>(Pulang Tepat Waktu)</span>';
                            $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                        }
                    } else {
                        if ($checkOut < $absensi_shift->Shift->jam_keluar) {
                            $keterangan_presensi_pulang = '<span>(Pulang Cepat)</span>';
                            $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                        } else {
                            $keterangan_presensi_pulang = '<span>(Pulang Tepat Waktu)</span>';
                            $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                        }
                    }
                } else {
                    $logs_absensi_pulang = $absensi_nonshift->filter(function ($log) use ($date) {
                        $getcheckOut = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d'));
                        return $getcheckOut->toDateString() === $start->toDateString();
                    });
                    if ($logs_absensi_pulang->isNotEmpty()) {
                        $keterangan_presensi_pulang = '';
                        $lokasi_absen_pulang = $logs_absensi_pulang->min('MachineIp');
                        $checkOut = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                        if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                            $checkOut = 'Tidak Presensi';
                        }
                    } else {
                        $keterangan_presensi_pulang = '';
                        $lokasi_absen_pulang = '<span>(Tidak&nbsp;Absen)</span>';
                        if ($date->format('Y-m-d') >= Carbon::now()->format('Y-m-d')) {
                            $checkOut = '';
                        } else {
                            $checkOut = 'Tidak Presensi';
                        }
                    }
                }
                $check_all = $checkIn . ' - ' . $checkOut;
                if ($checkIn == 'Tidak Presensi' && $checkOut == 'Tidak Presensi') {
                    $check_all = '<span style="color:red;">' . 'TIDAK HADIR' . '</span>';
                }

                $get_machine_masuk = FingerMachine::where('Ip', $lokasi_absen_masuk)->first();
                $get_machine_pulang = FingerMachine::where('Ip', $lokasi_absen_pulang)->first();
                if ($get_machine_masuk == NULL) {
                    $lokasi_masuk = '<span class="badge bg-label-danger">-</span>';
                } else {
                    $lokasi_masuk = $get_machine_masuk->Name;
                }
                if ($get_machine_pulang == NULL) {
                    $lokasi_pulang = '<span class="badge bg-label-danger">-</span>';
                } else {
                    if ($checkOut == 'Tidak Presensi') {
                        $lokasi_pulang = '<span class="badge bg-label-danger">-</span>';
                    } else {
                        $lokasi_pulang = $get_machine_pulang->Name;
                    }
                }

                return [
                    'no' => $index + 1,
                    'tanggal' => $carbonDate->format('d-m-Y'),
                    'hari' => $carbonDate->isoFormat('dddd'),
                    'hari_libur' => $carbonDate->dayOfWeek,
                    'nama_karyawan' => $table->name ?? '',
                    'nomor_identitas_karyawan' => $table->nomor_identitas_karyawan ?? '',
                    'shift_id' => $table->shift ?? '',
                    'non_shift' => $get_non_shift->nama_shift ?? '',
                    'shift' => $absensi_shift,
                    'checkIn' => $checkIn,
                    'checkOut' => $checkOut,
                    'check_all' => $check_all,
                    'keterangan_presensi_masuk' => $keterangan_presensi_masuk,
                    'keterangan_presensi_pulang' => $keterangan_presensi_pulang,
                    'lokasi_absen_masuk' => $lokasi_masuk,
                    'lokasi_absen_pulang' => $lokasi_pulang,
                    'telat' => $telat,
                ];
            });
            // dd($data);
            // $data = $data->filter(function ($row) {
            //     return $row['check_all'] == '<span style="color:red;">TIDAK HADIR</span>';
            // });
            return DataTables::of($data)

                ->addColumn('nomor_identitas_karyawan', function ($row) use ($table) {
                    return $row['nomor_identitas_karyawan'];
                })
                ->addColumn('nama_karyawan', function ($row) use ($table) {
                    return $row['nama_karyawan'];
                })
                ->addColumn('nama_shift', function ($row) use ($get_non_shift, $table) {
                    if ($row['shift_id'] == 'NON SHIFT') {
                        $nama_shift = $get_non_shift->nama_shift;
                    } else {
                        if (empty($row['shift'])) {
                            $nama_shift = '<span class="badge bg-label-danger">' . 'Belum dijadwalkan' . '</span>';
                        } else {
                            $nama_shift = $row['shift']->nama_shift;
                        }
                    }
                    return $nama_shift;
                })
                ->addColumn('jam_kerja', function ($row) use ($get_non_shift, $table) {
                    if ($row['shift_id'] == 'NON SHIFT') {
                        $jam_kerja = $get_non_shift->jam_masuk . ' - ' . $get_non_shift->jam_keluar;
                    } else {
                        if ($row['shift_id'] == NULL) {
                            $jam_kerja = '-';
                        } else {
                            if (empty($row['shift'])) {
                                $jam_kerja = '<span class="badge bg-label-danger">' . 'Belum dijadwalkan' . '</span>';
                            } else {
                                $jam_masuk = $row['shift']->Shift->jam_masuk;
                                $jam_keluar = $row['shift']->Shift->jam_keluar;
                                $jam_kerja = $jam_masuk . ' - ' . $jam_keluar;
                            }
                        }
                    }
                    return $jam_kerja;
                })
                ->addColumn('jam_absen_masuk', function ($row) {
                    if ($row['shift_id'] == 'NON SHIFT') {
                        return $row['checkIn'];
                    } else {
                        if (empty($row['shift'])) {
                            return '<span class="badge bg-label-danger">' . 'Belum dijadwalkan' . '</span>';
                        } else {
                            return $row['checkIn'];
                        }
                    }
                })
                ->addColumn('jam_absen_pulang', function ($row) use ($table) {
                    $lembur_detail = $table->LemburDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_lembur)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    // dd($lembur_detail);
                    if ($lembur_detail) {
                        $asoy =  Carbon::parse($lembur_detail->presensi_pulang)->format('H:i');
                    } else {
                        if ($row['shift_id'] == 'NON SHIFT') {
                            $asoy = $row['checkOut'];
                        } else {
                            if (empty($row['shift'])) {
                                $asoy = '<span class="badge bg-label-danger">' . 'Belum dijadwalkan' . '</span>';
                            } else {
                                $asoy = $row['checkOut'];
                            }
                        }
                    }
                    return $asoy;
                    // $row->presensi == $row['checkOut'];
                })
                ->addColumn('tanggal_masuk', function ($row) {

                    return $row['hari'] . ', ' . $row['tanggal'];
                })
                ->addColumn('tanggal_pulang', function ($row) use ($table) {

                    $lembur_detail = $table->LemburDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_lembur)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    // dd($lembur_detail);
                    if ($lembur_detail) {
                        return Carbon::parse($lembur_detail->tgl_pulang)->isoFormat('dddd, D-MM-Y');
                    } else {

                        if ($row['shift_id'] == 'NON SHIFT') {
                            return $row['hari'] . ', ' . $row['tanggal'];
                        } else {
                            if (empty($row['shift'])) {
                                return '<span class="badge bg-label-danger">' . 'Belum dijadwalkan' . '</span>';
                            } else {
                                return Carbon::parse($row['shift']->tanggal_pulang)->isoFormat('dddd, D-MM-Y');
                            }
                        }
                    }
                })
                ->addColumn('lokasi_absen_masuk', function ($row) {
                    return $row['lokasi_absen_masuk'];
                })
                ->addColumn('lokasi_absen_pulang', function ($row) {
                    return $row['lokasi_absen_pulang'];
                })
                ->addColumn('telat', function ($row) {
                    return $row['telat'];
                })
                ->addColumn('keterangan_presensi', function ($row) use ($get_non_shift, $table) {
                    $sakit_detail = $table->IzinSakitDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_sakit)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $cuti_detail = $table->CutiDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_cuti)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $tidak_masuk_detail = $table->IzinTidakMasukDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_tidak_masuk)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $terlambat = $table->IzinTerlambat->where('status_terlambat', 3)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tanggal_terlambat)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $perdin_detail = $table->PerdinDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_perdin)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $tukar_libur = $table->IzinLibur->where('status_libur', 3)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tanggal_tukar)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $tukar_libur_minggu = $table->IzinLibur->where('status_libur', 3)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tanggal_libur)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    if ($sakit_detail) {
                        return '<a href="' . url("izin/cetak_form_sakit/" . $sakit_detail->id_sakit . '/' . $table->id) . '" target="_blank" class="badge bg-secondary">SAKIT</a>';
                    } elseif ($cuti_detail) {
                        return '<a href="' . url("report/cetak_form_cuti/" . $table->id . '/' . $cuti_detail->id) . '" target="_blank" class="badge bg-warning">' . $cuti_detail->Cuti->nama_cuti . '</a>';
                    } elseif ($tidak_masuk_detail) {
                        return '<a href="' . url("izin/cetak_form_tidak_masuk/" . $tidak_masuk_detail->id . '/' . $table->id) . '" target="_blank" class="badge bg-secondary">IZIN TIDAK MASUK</a>';
                    } elseif ($perdin_detail) {
                        return '<a href="' . url("izin/cetak_form_perdin/" . $perdin_detail->id . '/' . $table->id) . '" target="_blank" class="badge bg-primary">PERJALANAN DINAS</a>';
                    } elseif ($tukar_libur) {
                        return '<a href="' . url("izin/cetak_form_libur/" . $tukar_libur->id . '/' . $table->id) . '" target="_blank" class="badge bg-info">TUKAR LIBUR</a>';
                    }
                    if ($row['shift_id'] == 'NON SHIFT') {
                        if ($row['hari_libur'] == $get_non_shift->hari_libur) {
                            $keterangan_presensi = '<span class="badge bg-label-info">LIBUR</span>';
                        } else {
                            if ($row['check_all'] == '<span style="color:red;">TIDAK HADIR</span>') {
                                $keterangan_presensi = '<span class="badge bg-label-danger">TIDAK HADIR</span>';
                                // $row->keterangan_presensi = 1;
                            } else if ($row['check_all'] == '' . ' - ' . '') {
                                $keterangan_presensi = '<span class="badge bg-label-warning">-</span>';
                            } else {
                                if ($terlambat) {
                                    $kedisiplinan = '<a href="' . url("izin/cetak_form_terlambat/" . $terlambat->id . '/' . $table->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN TERLAMBAT</a>';
                                } else {
                                    $kedisiplinan = $row['keterangan_presensi_masuk'];
                                }
                                $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span> ' . $kedisiplinan . '</span>';
                            }
                        }
                        if ($tukar_libur_minggu) {
                            if ($terlambat) {
                                $kedisiplinan = '<a href="' . url("izin/cetak_form_terlambat/" . $terlambat->id . '/' . $table->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN TERLAMBAT</a>';
                            } else {
                                $kedisiplinan = $row['keterangan_presensi_masuk'];
                            }
                            $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span> ' . $kedisiplinan . '</span>';
                        }
                    } else {
                        if ($sakit_detail) {
                            return '<a href="' . url("izin/cetak_form_sakit/" . $sakit_detail->id_sakit . '/' . $table->id) . '" target="_blank" class="badge bg-secondary">SAKIT</a>';
                        }
                        if (empty($row['shift'])) {
                            $keterangan_presensi = '<span class="badge bg-label-danger">Belum dijadwalkan</span>';
                        } else {
                            if ($row['shift']->nama_shift == 'LIBUR') {
                                $keterangan_presensi = '<span class="badge bg-label-info">LIBUR</span>';
                            } else {
                                if ($row['check_all'] == '<span style="color:red;">TIDAK HADIR</span>') {
                                    $keterangan_presensi = '<span class="badge bg-label-danger">TIDAK HADIR</span>';
                                } else if ($row['check_all'] == '' . ' - ' . '') {
                                    $keterangan_presensi = '<span class="badge bg-label-warning">-</span>';
                                } else {
                                    if ($terlambat) {
                                        $kedisiplinan = '<a href="' . url("izin/cetak_form_terlambat/" . $terlambat->id . '/' . $table->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN TERLAMBAT</a>';
                                    } else {
                                        $kedisiplinan = $row['keterangan_presensi_masuk'];
                                    }
                                    $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span> ' . $kedisiplinan . '</span>';
                                }
                            }
                        }
                    }
                    return $keterangan_presensi;
                })
                ->addColumn('keterangan_presensi_pulang', function ($row) use ($get_non_shift, $table) {

                    $sakit_detail = $table->IzinSakitDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_sakit)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $cuti_detail = $table->CutiDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_cuti)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $tidak_masuk_detail = $table->IzinTidakMasukDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_tidak_masuk)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $pulang = $table->IzinPulang->where('status_pulang', 3)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->created_at)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $perdin_detail = $table->PerdinDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_perdin)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $tukar_libur = $table->IzinLibur->where('status_libur', 3)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tanggal_tukar)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    $tukar_libur_minggu = $table->IzinLibur->where('status_libur', 3)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tanggal_libur)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    if ($sakit_detail) {
                        return '<a href="' . url("izin/cetak_form_sakit/" . $sakit_detail->id_sakit . '/' . $table->id) . '" target="_blank" class="badge bg-secondary">SAKIT</a>';
                    } elseif ($cuti_detail) {
                        return '<a href="' . url("report/cetak_form_cuti/" . $table->id . '/' . $cuti_detail->id) . '" target="_blank" class="badge bg-warning">' . $cuti_detail->Cuti->nama_cuti . '</a>';
                    } elseif ($tidak_masuk_detail) {
                        return '<a href="' . url("izin/cetak_form_tidak_masuk/" . $tidak_masuk_detail->id . '/' . $table->id) . '" target="_blank" class="badge bg-secondary">IZIN TIDAK MASUK</a>';
                    } elseif ($perdin_detail) {
                        return '<a href="' . url("izin/cetak_form_perdin/" . $perdin_detail->id . '/' . $table->id) . '" target="_blank" class="badge bg-primary">PERJALANAN DINAS</a>';
                    } elseif ($tukar_libur) {
                        return '<a href="' . url("izin/cetak_form_libur/" . $tukar_libur->id . '/' . $table->id) . '" target="_blank" class="badge bg-info">TUKAR LIBUR</a>';
                    }
                    $lembur_detail = $table->LemburDetail->where('status', 1)->first(function ($detail) use ($row) {
                        return Carbon::parse($detail->tgl_lembur)->isSameDay(Carbon::parse($row['tanggal']));
                    });
                    if ($lembur_detail) {
                        $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span><br>&nbsp;
                                                <a href="' . url("lembur/cetak_form_lembur/" . $lembur_detail->id_lembur . '/' . $table->id) . '" 
                                                target="_blank" class="badge bg-success">LEMBUR</a></span>';
                    } else {
                        if ($row['shift_id'] == 'NON SHIFT') {
                            if ($row['hari_libur'] == $get_non_shift->hari_libur) {
                                $keterangan_presensi = '<span class="badge bg-label-info">LIBUR</span>';
                            } else {
                                if ($row['check_all'] == '<span style="color:red;">TIDAK HADIR</span>') {
                                    $keterangan_presensi = '<span class="badge bg-label-danger">TIDAK HADIR</span>';
                                } else if ($row['check_all'] == '' . ' - ' . '') {
                                    $keterangan_presensi = '<span class="badge bg-label-warning">-</span>';
                                } else {
                                    if ($pulang) {
                                        $kedisiplinan = '<a href="' . url("izin/cetak_form_pulang/" . $pulang->id . '/' . $table->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN PULANG</a>';
                                    } else {
                                        $kedisiplinan = $row['keterangan_presensi_pulang'];
                                    }
                                    $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span> ' . $kedisiplinan . '</span>';
                                }
                            }
                            if ($tukar_libur_minggu) {
                                if ($pulang) {
                                    $kedisiplinan = '<a href="' . url("izin/cetak_form_pulang/" . $pulang->id . '/' . $table->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN PULANG</a>';
                                } else {
                                    $kedisiplinan = $row['keterangan_presensi_pulang'];
                                }
                                $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span> ' . $kedisiplinan . '</span>';
                            }
                        } else {
                            if (empty($row['shift'])) {
                                $keterangan_presensi = '<span class="badge bg-label-danger">Belum dijadwalkan</span>';
                            } elseif ($row['shift']->nama_shift == 'LIBUR') {
                                $keterangan_presensi = '<span class="badge bg-label-info">LIBUR</span>';
                            } else {
                                if ($row['check_all'] == '<span style="color:red;">TIDAK HADIR</span>') {
                                    $keterangan_presensi = '<span class="badge bg-label-danger">TIDAK HADIR</span>';
                                } else if ($row['check_all'] == '' . ' - ' . '') {
                                    $keterangan_presensi = '<span class="badge bg-label-warning">-</span>';
                                } else {
                                    if ($pulang) {
                                        $kedisiplinan = '<a href="' . url("izin/cetak_form_pulang/" . $pulang->id . '/' . $table->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN PULANG</a>';
                                    } else {
                                        $kedisiplinan = $row['keterangan_presensi_pulang'];
                                    }
                                    $keterangan_presensi = '<span class="badge bg-label-success">HADIR</span><br><span>' . $kedisiplinan . '</span>';
                                }
                            }
                        }
                    }
                    return $keterangan_presensi;
                })
                // ->addColumn('keterangan_izin', function ($row) {
                //     return '';
                // })
                // ->addColumn('keterangan_cuti', function ($row) {
                //     return '';
                // })
                // ->addColumn('keterangan_dinas', function ($row) {
                //     return '';
                // })
                ->rawColumns([
                    'jam_absen_masuk',
                    'jam_absen_pulang',
                    'nomor_identitas_karyawan',
                    'nama_karyawan',
                    'nama_shift',
                    'jam_kerja',
                    'keterangan_presensi',
                    'keterangan_presensi_pulang',
                    'lokasi_absen_masuk',
                    'lokasi_absen_pulang',
                    'telat',
                    'tanggal_pulang',
                    'total_jam_kerja',
                    // 'keterangan_izin',
                    // 'keterangan_cuti',
                    // 'keterangan_dinas'
                ])
                ->make(true);
        }
    }
    public function get_columns(Request $request)
    {
        // dd($request->all());
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $data_columns_header[] = ['header' => Carbon::parse($date)->isoFormat('dddd, DD/MM/YYYY')];
            $data_columns[] = [
                'data' => 'tanggal_' . $date->format('dmY'),
                'name' => 'tanggal_' . $date->format('dmY')
            ];
        }
        $count_period = count($period);

        return array(
            'data_columns_header' => $data_columns_header,
            'count_period' => $count_period,
            'datacolumn' => $data_columns,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        );
    }
    public function get_columns_kedisiplinan(Request $request)
    {
        $start_date = Carbon::parse($request->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($request->end_date)->format('Y-m-d');
        // dd($end_date);
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $data_columns_header[] = ['header' => Carbon::parse($date)->isoFormat('dddd, D/M/YYYY')];
            $data_columns[] = ['data' => 'tanggal_' . $date->format('dmY'), 'name' => 'tanggal_' . $date->format('dmY')];
        }
        $count_period = count($period);

        return array(
            'data_columns_header' => $data_columns_header,
            'count_period' => $count_period,
            'datacolumn' => $data_columns,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        );
    }
    public function get_filter_month(Request $request)
    {
        // dd($request->filter_month);
        $start_date = Carbon::parse($request->filter_month)->startOfMonth();
        $end_date = Carbon::parse($request->filter_month)->endOfMonth();
        $period = CarbonPeriod::create($start_date, $end_date);
        foreach ($period as $date) {
            $data_columns_header[] = ['header' => Carbon::parse($date)->isoFormat('dddd, D/M/YYYY')];
            $data_columns[] = ['data' => 'tanggal_' . $date->format('dmY'), 'name' => 'tanggal_' . $date->format('dmY')];
        }
        $count_period = count($period);
        return array('data_columns_header' => $data_columns_header, 'count_period' => $count_period, 'datacolumn' => $data_columns, 'filter_month' => $request->filter_month);
    }
    public function datatable_kedisiplinan(Request $request, $holding)
    {
        date_default_timezone_set('Asia/Jakarta');

        $holding = Holding::where('holding_code', $holding)->first();
        if ($holding == NULL) {
            $holding = Holding::where('holding_code', request()->segment(count(request()->segments())))->first();
        }

        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $period = CarbonPeriod::create($now, $now1);

        if (request()->ajax()) {
            // 1. PRE-FETCH DATA CALENDAR (Mencegah N+1 Query)
            // Ambil kalender libur di awal, jadikan key array sebagai format Y-m-d
            $calendars = Calendar::whereBetween('start_date', [$now->format('Y-m-d'), $now1->format('Y-m-d')])
                ->where('status', 1)
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->start_date)->format('Y-m-d');
                });

            // 2. EAGER LOAD YANG DIOPTIMASI (Tambahkan CutiDetail)
            $query = Karyawan::with(['KaryawanJabatan' => function ($q) {
                $q->with('Departemen', 'Jabatan', 'Bagian', 'Divisi');
            }])
                ->with('Cuti.KategoriCuti')
                ->with(['CutiDetail' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tgl_cuti', [$now, $now1])->where('status', 1);
                }])
                ->with(['IzinSakitDetail' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tgl_sakit', [$now, $now1])->where('status', 1);
                }])
                ->with(['IzinTidakMasukDetail' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tgl_tidak_masuk', [$now, $now1])->where('status', 1);
                }])
                ->with(['PerdinDetail' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tgl_perdin', [$now, $now1])->where('status', 1);
                }])
                ->with(['IzinTerlambat' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tanggal_terlambat', [$now, $now1])->where('status_terlambat', 3);
                }])
                ->with(['IzinPulang' => function ($q) use ($now, $now1) {
                    $q->whereBetween('created_at', [$now, $now1])->where('status_pulang', 3);
                }])
                ->with(['IzinKeluar' => function ($q) use ($now, $now1) {
                    $q->whereBetween('created_at', [$now, $now1])->where('status_keluar', 3);
                }])
                ->with(['IzinLibur' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tanggal_tukar', [$now, $now1])->where('status_libur', 3);
                }])
                ->with(['IzinLiburMinggu' => function ($q) use ($now, $now1) {
                    $q->whereBetween('tanggal_libur', [$now, $now1])->where('status_libur', 3);
                }])
                ->with(['LemburDetail' => function ($q) use ($now, $now1) {
                    $q->where('status', 1)
                        ->whereHas('Lembur', function ($q) use ($now, $now1) {
                            $q->whereBetween('tanggal_lembur', [$now, $now1]);
                        })
                        ->with('Lembur');
                }])
                ->with(['PresensiFoto' => function ($q) use ($now, $now1) {
                    $q->whereBetween('jam_presensi', [$now, $now1])->where('status_presensi', 1);
                }])
                ->with(['Absensi' => function ($q) use ($now, $now1) {
                    $q->whereBetween('LogTime', [$now, $now1])
                        ->select('EnrollNumber', 'LogTime');
                }])
                ->with(['MappingShift' => function ($q) use ($now, $now1) {
                    $q->with('Shift')
                        ->whereNotNull('is_active')
                        ->where('status_approved', 1)
                        ->whereBetween('tanggal_masuk', [$now, $now1])
                        ->orderBy('tanggal_masuk', 'ASC');
                }])
                ->where('kontrak_kerja', $holding->id)
                ->where('kategori', 'Karyawan Bulanan')
                // ->whereIn('nomor_identitas_karyawan', ['1002002070273'])
                ->where('status_aktif', 'AKTIF')
                ->orderBy('name', 'ASC');

            // Filter...
            if (!empty($request->departemen_filter)) {
                $query->whereHas('KaryawanJabatan', function ($q) use ($request) {
                    $q->whereIn('departemen_id', (array)$request->departemen_filter ?? []);
                });
            }
            if (!empty($request->divisi_filter)) {
                $query->whereHas('KaryawanJabatan', function ($q) use ($request) {
                    $q->whereIn('divisi_id', (array)$request->divisi_filter ?? []);
                });
            }
            if (!empty($request->bagian_filter)) {
                $query->whereHas('KaryawanJabatan', function ($q) use ($request) {
                    $q->whereIn('bagian_id', (array)$request->bagian_filter ?? []);
                });
            }
            if (!empty($request->jabatan_filter)) {
                $query->whereHas('KaryawanJabatan', function ($q) use ($request) {
                    $q->whereIn('jabatan_id', (array)$request->jabatan_filter ?? []);
                });
            }
            if (!empty($request->shift_filter)) {
                $query->where('shift', $request->shift_filter);
            }

            $non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $column = DataTables::eloquent($query);

            // 3. HELPER FUNCTION: Grouping data di sisi Memori agar tidak filter ratusan data per loop
            $groupDataHarian = function ($row) {
                if (!isset($row->_absensiByDate)) {
                    $row->_absensiByDate = $row->Absensi->groupBy(function ($item) {
                        return substr($item->LogTime, 0, 10); // Extract Y-m-d dengan cepat
                    });
                    $row->_cutiByDate = collect($row->CutiDetail)->groupBy(function ($item) {
                        return substr($item->tgl_cuti, 0, 10);
                    });
                }
            };
            // LOOP RENTANG TANGGAL
            foreach ($period as $date) {
                $colName = 'tanggal_' . $date->format('dmY');
                $dateStr = $date->format('Y-m-d');

                $column->addColumn($colName, function ($row) use ($date, $dateStr, $non_shift, $calendars, $groupDataHarian) {
                    $groupDataHarian($row); // Jalankan grouping jika belum

                    // Ambil absensi HANYA untuk hari ini (Sangat Ringan, O(1))
                    $logsToday = $row->_absensiByDate->get($dateStr, collect());
                    $cuti_detail = $row->cutiDetail
                        ->filter(function ($item) use ($date) {
                            return $item->status == 1
                                && \Carbon\Carbon::parse($item->tgl_cuti)->isSameDay($date);
                        })
                        ->isNotEmpty();
                    $sakit_detail = $row->IzinSakitDetail
                        ->filter(function ($item) use ($date) {
                            return $item->status == 1
                                && \Carbon\Carbon::parse($item->tgl_sakit)->isSameDay($date);
                        })
                        ->isNotEmpty();
                    $tidak_masuk_detail = $row->IzinTidakMasukDetail
                        ->filter(function ($item) use ($date) {
                            return $item->status == 1
                                && \Carbon\Carbon::parse($item->tgl_tidak_masuk)->isSameDay($date);
                        })
                        ->isNotEmpty();
                    $perdin_detail = $row->PerdinDetail
                        ->filter(function ($item) use ($date) {
                            return $item->status == 1
                                && \Carbon\Carbon::parse($item->tgl_perdin)->isSameDay($date);
                        })
                        ->isNotEmpty();
                    $tukar_libur = $row->IzinLibur
                        ->filter(function ($item) use ($date) {
                            return $item->status_libur == 3
                                && \Carbon\Carbon::parse($item->tanggal_tukar)->isSameDay($date);
                        })
                        ->isNotEmpty();
                    $tukar_libur_minggu = $row->IzinLibur
                        ->filter(function ($item) use ($date) {
                            return $item->status_libur == 3
                                && \Carbon\Carbon::parse($item->tanggal_libur)->isSameDay($date);
                        })
                        ->isNotEmpty();
                    $lembur_detail = $row->LemburDetail
                        ->filter(function ($item) use ($date) {
                            return $item->Lembur
                                && Carbon::parse($item->Lembur->tanggal_lembur)->isSameDay($date);
                        });
                    $cek_lembur = $lembur_detail->isNotEmpty();
                    // dd($cek_lembur, $lembur_detail, $row->LemburDetail);
                    if ($row->shift == 'NON SHIFT') {
                        $jam_masuk = $logsToday;
                        if ($jam_masuk->count() == 0) {
                            if ($cuti_detail) {
                                $checkIn = 'CUTI';
                                $checkOut = 'CUTI';
                            } else {
                                if ($sakit_detail) {
                                    $checkIn = 'SAKIT';
                                    $checkOut = 'SAKIT';
                                    $row->sakit += 1;
                                } else {
                                    if ($tidak_masuk_detail) {

                                        $checkIn = 'tidak_masuk';
                                        $checkOut = 'tidak_masuk';
                                        $row->tidak_masuk += 1;
                                    } else {
                                        if ($perdin_detail) {

                                            $checkIn = 'PERDIN';
                                            $checkOut = 'PERDIN';
                                            $row->perdin += 1;
                                        } else {
                                            if ($tukar_libur) {
                                                $checkIn = 'tukar_libur';
                                                $checkOut = 'tukar_libur';
                                                $row->tukar_libur += 1;
                                            } else {
                                                if (!$tukar_libur_minggu) {
                                                    if ($non_shift->hari_libur == Carbon::parse($dateStr)->dayOfWeek) {
                                                        $checkIn = 'LIBUR';
                                                        $checkOut = 'LIBUR';
                                                    } else {
                                                        $checkIn = 'Tidak Presensi';
                                                        $checkOut = 'Tidak Presensi';
                                                    }
                                                } else {
                                                    $checkIn = 'Tidak Presensi';
                                                    $checkOut = 'Tidak Presensi';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($non_shift->hari_libur == Carbon::parse($dateStr)->dayOfWeek) {
                                if (!$tukar_libur_minggu) {
                                    $checkIn = 'LIBUR';
                                    $checkOut = 'LIBUR';
                                } else {
                                    $logs_absensi_masuk = $logsToday->filter(function ($log) use ($non_shift, $dateStr) {
                                        $getcheckIn = Carbon::parse($log->LogTime);
                                        $start = Carbon::parse($dateStr . ' ' . $non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                                        $end = Carbon::parse($dateStr . ' ' . $non_shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                                        return $getcheckIn->between($start, $end);
                                    });

                                    if ($logs_absensi_masuk->isNotEmpty()) {
                                        $checkIn = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                                        if ($checkIn >  Carbon::parse($dateStr . ' ' . $non_shift->jam_terlambat)->format('H:i')) {
                                            if ($checkIn <  Carbon::parse($dateStr . ' ' . $non_shift->jam_terlambat)->addMinutes(10)->format('H:i')) {
                                                $row->telat_ringan += 1;
                                            } else {
                                                $row->telat_berat += 1;
                                            }
                                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                                        } else {
                                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                                            $row->tepat_waktu += 1;
                                        }
                                    } else {
                                        $batas_absensi = Carbon::parse($dateStr . ' ' . $non_shift->jam_min_masuk)->subHours(3);
                                        $start = Carbon::parse($dateStr . ' ' . $non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                                        $logs_absensi_masuk_datang_awal = $logsToday->filter(function ($log) use ($batas_absensi, $start) {
                                            return Carbon::parse($log->LogTime)->between($batas_absensi, $start);
                                        });
                                        if ($logs_absensi_masuk_datang_awal->isNotEmpty()) {
                                            $checkIn = Carbon::parse($logs_absensi_masuk_datang_awal->min('LogTime'))->format('H:i');
                                            $row->total_datang_lebih_awal += 1;
                                        } else {
                                            $checkIn = 'Tidak Presensi';
                                        }
                                    }
                                    if ($cek_lembur == true) {
                                        $detail_lembur = $lembur_detail->first();
                                        $startPulang = Carbon::parse($detail_lembur->tgl_lembur . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                        $endPulang = Carbon::parse($detail_lembur->tgl_pulang . ' ' . $detail_lembur->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                                        $tanggalPulangStr = Carbon::parse($detail_lembur->tgl_pulang)->format('Y-m-d');
                                    } else {
                                        $startPulang = Carbon::parse($dateStr . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                        $endPulang = Carbon::parse($dateStr . ' ' . $non_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                        $tanggalPulangStr = Carbon::parse($dateStr)->format('Y-m-d');
                                    }
                                    // dd($startPulang, $endPulang);
                                    $logsPulangToday = $row->_absensiByDate->get($tanggalPulangStr, collect());
                                    $logs_absensi_pulang = $logsPulangToday->filter(function ($log) use ($startPulang, $endPulang) {
                                        $getcheckOut = Carbon::parse($log->LogTime);
                                        return $getcheckOut->between($startPulang, $endPulang);
                                    });
                                    if ($logs_absensi_pulang->isNotEmpty()) {
                                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                                        if ($checkOut < $non_shift->jam_keluar) {
                                            $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                                        } else {
                                            $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                                        }
                                    } else {
                                        $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($date) {
                                            $getcheckOut = Carbon::parse($log->LogTime);
                                            $start = Carbon::parse($date->format('Y-m-d'));
                                            return $getcheckOut->toDateString() === $start->toDateString();
                                        });
                                        if ($logs_absensi_pulang->isNotEmpty()) {
                                            $checkOut  = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                                            if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                                $checkOut = 'Tidak Presensi';
                                            }
                                        } else {
                                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                                $checkOut = '';
                                            } else {
                                                $checkOut = 'Tidak Presensi';
                                            }
                                        }
                                    }
                                }
                            } else {
                                $batas_absensi = Carbon::parse($dateStr . ' ' . $non_shift->jam_min_masuk)->subHours(3);
                                $start = Carbon::parse($dateStr . ' ' . $non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                                $logs_absensi_masuk_datang_awal = $logsToday->filter(function ($log) use ($batas_absensi, $start) {
                                    return Carbon::parse($log->LogTime)->between($batas_absensi, $start);
                                });
                                if ($logs_absensi_masuk_datang_awal->isNotEmpty()) {
                                    $checkIn = Carbon::parse($logs_absensi_masuk_datang_awal->min('LogTime'))->format('H:i');
                                    $row->total_datang_lebih_awal += 1;
                                    $logs_absensi_masuk = $logs_absensi_masuk_datang_awal;
                                } else {
                                    $logs_absensi_masuk = $logsToday->filter(function ($log) use ($non_shift, $dateStr) {
                                        $getcheckIn = Carbon::parse($log->LogTime);
                                        $start = Carbon::parse($dateStr . ' ' . $non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                                        $end = Carbon::parse($dateStr . ' ' . $non_shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                                        return $getcheckIn->between($start, $end);
                                    });
                                    if ($logs_absensi_masuk->isNotEmpty()) {
                                        $checkIn = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                                        if ($checkIn >  Carbon::parse($dateStr . ' ' . $non_shift->jam_terlambat)->format('H:i')) {
                                            if ($checkIn <  Carbon::parse($dateStr . ' ' . $non_shift->jam_terlambat)->addMinutes(10)->format('H:i')) {
                                                $row->telat_ringan += 1;
                                            } else {
                                                $row->telat_berat += 1;
                                            }
                                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                                        } else {
                                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                                            $row->tepat_waktu += 1;
                                        }
                                    } else {
                                        $checkIn = 'Tidak Presensi';
                                    }
                                }


                                if ($cek_lembur == true) {
                                    $detail_lembur = $lembur_detail->first();
                                    $startPulang = Carbon::parse($detail_lembur->tgl_lembur . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                    $endPulang = Carbon::parse($detail_lembur->tgl_pulang . ' ' . $detail_lembur->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                                    $tanggalPulangStr = Carbon::parse($detail_lembur->tgl_pulang)->format('Y-m-d');
                                } else {
                                    $startPulang = Carbon::parse($dateStr . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                    $endPulang = Carbon::parse($dateStr . ' ' . $non_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                    $tanggalPulangStr = Carbon::parse($dateStr)->format('Y-m-d');
                                }
                                // dd($startPulang, $endPulang);
                                $logsPulangToday = $row->_absensiByDate->get($tanggalPulangStr, collect());
                                $logs_absensi_pulang = $logsPulangToday->filter(function ($log) use ($startPulang, $endPulang) {
                                    $getcheckOut = Carbon::parse($log->LogTime);
                                    return $getcheckOut->between($startPulang, $endPulang);
                                });
                                if ($logs_absensi_pulang->isNotEmpty()) {
                                    $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                                    if ($checkOut < $non_shift->jam_keluar) {
                                        $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                                    } else {
                                        $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                                    }
                                } else {
                                    $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($date) {
                                        $getcheckOut = Carbon::parse($log->LogTime);
                                        $start = Carbon::parse($date->format('Y-m-d'));
                                        return $getcheckOut->toDateString() === $start->toDateString();
                                    });
                                    // dd($logs_absensi_pulang);
                                    if ($logs_absensi_pulang->isNotEmpty()) {
                                        $checkOut  = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                                        // if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                        // }
                                        $checkOut = 'Tidak Presensi';
                                    } else {
                                        if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                            $checkOut = '';
                                        } else {
                                            $checkOut = 'Tidak Presensi';
                                        }
                                    }
                                }
                            }
                            // Karena logsToday hanya berisi data hari ini, filter jadi sangat cepat
                        }
                        $nama_shift = $row->shift;
                    } else {
                        $jam_masuk = $row->MappingShift->firstWhere('tanggal_masuk', $dateStr);
                        // dd($jam_masuk);
                        if (!$jam_masuk) {
                            if ($cuti_detail) {
                                $checkIn = 'CUTI';
                                $checkOut = 'CUTI';
                                $nama_shift = 'CUTI';
                            } else {
                                if ($sakit_detail) {
                                    $checkIn = 'SAKIT';
                                    $checkOut = 'SAKIT';
                                    $nama_shift = 'SAKIT';
                                    $row->sakit += 1;
                                } else {
                                    if ($tidak_masuk_detail) {
                                        $checkIn = 'tidak_masuk';
                                        $checkOut = 'tidak_masuk';
                                        $nama_shift = 'tidak_masuk';
                                        $row->tidak_masuk += 1;
                                    } else {
                                        if ($perdin_detail) {
                                            $checkIn = 'PERDIN';
                                            $checkOut = 'PERDIN';
                                            $nama_shift = 'PERDIN';
                                            $row->perdin += 1;
                                        } else {
                                            $checkIn = 'Tidak Presensi';
                                            $checkOut = 'Tidak Presensi';
                                            $nama_shift = 'BELUM DITENTUKAN';
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($cuti_detail) {
                                $checkIn = 'CUTI';
                                $checkOut = 'CUTI';
                                $nama_shift = $jam_masuk->Shift->nama_shift;
                            } else {
                                // dd($jam_masuk->nama_shift);
                                if ($sakit_detail) {
                                    $checkIn = 'SAKIT';
                                    $checkOut = 'SAKIT';
                                    $nama_shift = $jam_masuk->Shift->nama_shift;
                                    $row->sakit += 1;
                                } else {
                                    if ($tidak_masuk_detail) {
                                        $checkIn = 'tidak_masuk';
                                        $checkOut = 'tidak_masuk';
                                        $nama_shift = $jam_masuk->Shift->nama_shift;
                                        $row->tidak_masuk += 1;
                                    } else {
                                        if ($jam_masuk->nama_shift == 'LIBUR') {
                                            $checkIn = 'LIBUR';
                                            $checkOut = 'LIBUR';
                                        } else {
                                            if ($jam_masuk->Shift->tgl_pulang_besok != 1) {
                                                $log_besok = Carbon::parse($dateStr)->format('Y-m-d');
                                                $end_date  = Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_terlambat)->addHours(3)->addMinutes(10);
                                            } else {
                                                $end_date  = Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_terlambat)->addHours(3)->addMinutes(10);
                                                $log_besok = Carbon::parse($dateStr)->addDay(1)->format('Y-m-d');
                                                $logsTodayLembur = $row->_absensiByDate->get($log_besok, collect());
                                            }
                                            $start = Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                                            $end = $end_date->format('Y-m-d H:i:59');
                                            $batas_absensi = Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_min_masuk)->subHours(4)->format('Y-m-d H:i:s');
                                            $logs_absensi_masuk = $logsToday->filter(function ($log) use ($start, $end) {
                                                return Carbon::parse($log->LogTime)->between($start, $end);
                                            });
                                            // dd($logs_absensi_masuk, $start, $end, $logsToday, $logsTodayLembur);
                                            if ($logs_absensi_masuk->isNotEmpty()) {
                                                $checkIn = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                                                $checkInFullDate = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('YmdHi');

                                                // dd($log_besok, $checkInFullDate, Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_terlambat)->addMinutes(10)->format('YmdHi'));
                                                if ($checkInFullDate >  Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_terlambat)->format('YmdHi')) {
                                                    if ($checkInFullDate <  Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_terlambat)->addMinutes(10)->format('YmdHi')) {
                                                        $row->telat_ringan += 1;
                                                    } else {
                                                        $row->telat_berat += 1;
                                                    }
                                                    $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                                                } else {
                                                    $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                                                    $row->tepat_waktu += 1;
                                                }
                                            } else {
                                                $logs_absensi_masuk_datang_awal = $logsToday->filter(function ($log) use ($batas_absensi, $start) {
                                                    return Carbon::parse($log->LogTime)->between($batas_absensi, $start);
                                                });
                                                // dd($start, $logs_absensi_masuk_datang_awal, $batas_absensi, $logsToday);
                                                if ($logs_absensi_masuk_datang_awal->isNotEmpty()) {
                                                    $checkIn = Carbon::parse($logs_absensi_masuk_datang_awal->min('LogTime'))->format('Y-m-d H:i');
                                                    $row->total_datang_lebih_awal += 1;
                                                } else {
                                                    $checkIn = 'Tidak Presensi';
                                                }
                                            }
                                            // dd($checkIn);
                                            // Pulang
                                            if ($cek_lembur == true) {
                                                $detail_lembur = $lembur_detail->first();
                                                $startPulang = Carbon::parse($detail_lembur->tgl_lembur . ' ' . $detail_lembur->jam_mulai)->format('Y-m-d H:i:59');
                                                $endPulang = Carbon::parse($detail_lembur->tgl_pulang . ' ' . $detail_lembur->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                                                // dd($detail_lembur, $jam_masuk->tanggal_pulang, $startPulang, $endPulang, $tanggalMasukStr, $tanggalPulangStr);
                                            } else {
                                                if ($jam_masuk->Shift->tgl_pulang_besok == 1) {
                                                    $startPulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                                    $endPulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                                } else {
                                                    $startPulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                                    $endPulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                                }
                                            }
                                            // dd($startPulang, $endPulang);
                                            $tanggalMasukStr = Carbon::parse($startPulang)->format('Y-m-d'); // cth: 2026-09-20
                                            $tanggalPulangStr = Carbon::parse($startPulang)->format('Y-m-d');
                                            // Perhatian: jika pulang besoknya, kita harus ambil log harian tanggal pulang
                                            $logsPulangToday = $row->_absensiByDate->get($tanggalPulangStr, collect());

                                            $logs_absensi_pulang = $logsPulangToday->filter(function ($log) use ($startPulang, $endPulang) {
                                                return Carbon::parse($log->LogTime)->between($startPulang, $endPulang);
                                            });
                                            // dd($logs_absensi_pulang, $startPulang, $endPulang, $logsPulangToday, $tanggalPulangStr);
                                            if ($logs_absensi_pulang->isNotEmpty()) {
                                                $checkOut = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                                                if ($tanggalPulangStr > $dateStr) {
                                                    if (Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('YmdHis') < Carbon::parse($dateStr . ' ' . $jam_masuk->Shift->jam_keluar)->format('YmdHis')) {
                                                        $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">(' . $checkOut . ' ' . Carbon::parse($tanggalPulangStr)->format('d-m-Y') . ')' . '</span>';
                                                    } else {
                                                        $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span> (' . Carbon::parse($tanggalPulangStr)->format('d-m-Y') . ')';
                                                    }
                                                } else {
                                                    if ($checkOut < $jam_masuk->Shift->jam_keluar) {
                                                        $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                                                    } else {
                                                        $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                                                    }
                                                }
                                            } else {
                                                $logPulangBesok = $row->_absensiByDate->get(Carbon::parse($tanggalPulangStr)->addDays(1)->format('Y-m-d'), collect());
                                                // dd($logPulangBesok, $tanggalMasukStr, $tanggalPulangStr);
                                                $logs_absensi_pulang_besok = $logPulangBesok->filter(function ($log) use ($startPulang, $endPulang) {
                                                    return Carbon::parse($log->LogTime)->between($startPulang, $endPulang);
                                                });
                                                if ($logs_absensi_pulang_besok->isNotEmpty()) {
                                                    $checkOut = Carbon::parse($logs_absensi_pulang_besok->min('LogTime'))->format('H:i');
                                                    $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span> (' . Carbon::parse($tanggalPulangStr)->format('d-m-Y') . ')';
                                                } else {
                                                    $checkOut = 'Tidak Presensi';
                                                }
                                            }
                                        }
                                    }
                                }
                                $nama_shift = $jam_masuk->Shift->nama_shift;
                            }
                        }
                    }

                    // Cek TIDAK HADIR / Libur Calendar / Cuti
                    if ($checkIn == 'Tidak Presensi' && $checkOut == 'Tidak Presensi') {

                        // Cek kalender (Sangat Ringan berkat pre-fetch)
                        if (isset($calendars[$dateStr])) {
                            $calendar = $calendars[$dateStr];
                            return '<span class="badge bg-label-info">' . $calendar->nama_kegiatan . '</span><br><button class="btn btn-sm btn-warning" id="detail_libur" data-id="' . $calendar->id . '" type="button"><span>Detail</span></button>';
                        }
                        $row->tidak_hadir += 1;
                        return '<span class="badge bg-danger">TIDAK HADIR</span><br><span>(Shift : ' . $nama_shift . ')</span>';
                    } else if ($checkIn == 'LIBUR' && $checkOut == 'LIBUR') {
                        $row->libur += 1;
                        return '<span class="badge bg-info">LIBUR</span><br><span>(Shift : ' . $nama_shift . ')</span>';
                    } else if ($checkIn == 'CUTI' && $checkOut == 'CUTI') {
                        $cuti_detail = $row->cutiDetail
                            ->filter(function ($item) use ($date) {
                                return $item->status == 1
                                    && \Carbon\Carbon::parse($item->tgl_cuti)->isSameDay($date);
                            })
                            ->first();

                        return '<a href="' . url("report/cetak_form_cuti/" . $row->id . '/' . $cuti_detail->id) . '" target="_blank" class="badge bg-warning">' . $cuti_detail->Cuti->nama_cuti . '</a>';
                        // return $cuti_detail->id;
                    } else if ($checkIn == 'SAKIT' && $checkOut == 'SAKIT') {
                        $sakit_detail = $row->IzinSakitDetail
                            ->filter(function ($item) use ($date) {
                                return $item->status == 1
                                    && \Carbon\Carbon::parse($item->tgl_sakit)->isSameDay($date);
                            })
                            ->first();

                        return '<a href="' . url("izin/cetak_form_sakit/" . $sakit_detail->id_sakit . '/' . $row->id) . '" target="_blank" class="badge bg-secondary">SAKIT</a>';
                        // return $cuti_detail->id;
                    } else if ($checkIn == 'tidak_masuk' && $checkOut == 'tidak_masuk') {
                        $tidak_masuk_detail = $row->IzinTidakMasukDetail
                            ->filter(function ($item) use ($date) {
                                return $item->status == 1
                                    && \Carbon\Carbon::parse($item->tgl_tidak_masuk)->isSameDay($date);
                            })
                            ->first();

                        return '<a href="' . url("izin/cetak_form_tidak_masuk/" . $tidak_masuk_detail->id_tidak_masuk . '/' . $row->id) . '" target="_blank" class="badge bg-secondary">IZIN TIDAK MASUK</a>';
                        // return $cuti_detail->id;
                    } else if ($checkIn == 'tukar_libur' && $checkOut == 'tukar_libur') {
                        $tukar_libur = $row->IzinLibur
                            ->filter(function ($item) use ($date) {
                                return $item->status_libur == 3
                                    && \Carbon\Carbon::parse($item->tanggal_tukar)->isSameDay($date);
                            })
                            ->first();

                        return '<a href="' . url("izin/cetak_form_libur/" . $tukar_libur->id . '/' . $row->id) . '" target="_blank" class="badge bg-info">TUKAR LIBUR</a>';
                        // return $cuti_detail->id;
                    } else if ($checkIn == 'Tidak Presensi' && $checkOut != 'Tidak Presensi') {
                        $row->tidak_presensi_masuk += 1;
                    } else if ($checkIn != 'Tidak Presensi' && $checkOut == 'Tidak Presensi') {
                        $row->tidak_presensi_pulang += 1;
                    } else {
                        $row->hadir += 1;
                    }
                    $pulang = $row->IzinPulang()
                        ->whereDate('created_at', $dateStr)
                        ->where('status_pulang', 3)
                        ->first();
                    if ($pulang && $checkOut != 'Tidak Presensi') {
                        $izin_pulang = '<br>&nbsp;<a href="' . url("izin/cetak_form_pulang/" . $pulang->id . '/' . $row->id) . '" target="_blank" class="badge my-1 bg-secondary">IZIN PULANG</a>';
                    } else {
                        $izin_pulang = "";
                    }
                    $terlambat = $row->IzinTerlambat()
                        ->whereDate('tanggal_terlambat', $dateStr)
                        ->where('status_terlambat', 3)
                        ->first();
                    if ($terlambat) {
                        $izin_terlambat = '<br>&nbsp;<a href="' . url("izin/cetak_form_terlambat/" . $terlambat->id . '/' . $row->id) . '" target="_blank" class="badge my-1 bg-primary">IZIN TERLAMBAT</a>';
                    } else {
                        $izin_terlambat = "";
                    }
                    $keluar = $row->IzinKeluar()
                        ->whereDate('created_at', $dateStr)
                        ->where('status_keluar', 3)
                        ->first();
                    if ($keluar) {
                        $izin_keluar = '<br>&nbsp;<a href="' . url("izin/cetak_form_keluar/" . $keluar->id . '/' . $row->id) . '" target="_blank" class="badge my-1 bg-success">IZIN KELUAR</a>';
                    } else {
                        $izin_keluar = "";
                    }
                    $lembur = $row->LemburDetail()
                        ->whereDate('tgl_lembur', $dateStr)
                        ->where('status', 1)
                        ->first();
                    if ($lembur) {
                        $lembur_sah = '<br>&nbsp;<a href="' . url("lembur/cetak_form_lembur/" . $lembur->id_lembur . '/' . $row->id) . '" target="_blank" class="badge my-1 bg-success">LEMBUR</a>';
                    } else {
                        $lembur_sah = "";
                    }

                    $perdin = $row->PerdinDetail()
                        ->whereDate('tgl_perdin', $dateStr)
                        ->where('status', 1)
                        ->first();
                    if ($perdin) {
                        $perdin_sah = '<br>&nbsp;<a href="' . url("perdin/cetak_form_perdin/" . $perdin->id_perdin . '/' . $row->id) . '" target="_blank" class="badge my-1 bg-primary">PERJALANAN DINAS</a>';
                    } else {
                        $perdin_sah = "";
                    }
                    $presensi = $row->PresensiFoto()
                        ->whereDate('jam_presensi', $dateStr)
                        ->where('status_presensi', 1)
                        ->first();
                    if ($presensi) {
                        $presensi_foto = '<br>&nbsp;<a href="' . url("presensi_foto/cetak_form_presensi/" . $presensi->id . '/' . $row->id) . '" target="_blank" class="badge my-1 bg-primary">PRESENSI FOTO</a>';
                    } else {
                        $presensi_foto = "";
                    }
                    return '<span style="white-space:nowrap;">' . $checkIn . '&nbsp;-&nbsp;' . $checkOut . '</span>' . $izin_terlambat . '&nbsp;' . $izin_pulang . '&nbsp;' . $izin_keluar . '&nbsp;' . $presensi_foto . '&nbsp;' . $lembur_sah . '&nbsp;' . $perdin_sah . '<br>&nbsp;<span>(Shift&nbsp;: ' . $nama_shift . ')&nbsp;</span>';
                });
                $data_tanggal[] = $colName;
            }

            // =========================================================================
            // KOLOM TAMBAHAN DAN STATISTIK (DIPERCEPAT MENGGUNAKAN $row->_absensiByDate)
            // =========================================================================
            $column->addColumn('btn_detail', function ($row) use ($holding, $request) {
                $btn_detail = '<a id="btn_detail" type="button" href="' . url('report/absensi/kedisiplinan/detail', ['id' => $row->nomor_identitas_karyawan]) . '/' . $holding->holding_code . '/' . $request->start_date . '/' . $request->end_date . '" class="btn btn-sm" target="_blank"><i class="menu-icon tf-icons mdi mdi-database-eye-outline text-primary"></i><span class="text-primary">Detail</span></a>';
                return $btn_detail;
            });
            $column->addColumn('nama_karyawan', function ($row) use ($holding) {
                $nama = $row->name;
                if ($nama != NULL) {
                    $nama = $row->name;
                } else {
                    $nama = '-';
                }
                return $nama;
            });
            $column->addColumn('nomor_identitas_karyawan', function ($row) use ($holding) {
                $nomor = $row->nomor_identitas_karyawan;
                if ($nomor != NULL) {
                    $nomor = $row->nomor_identitas_karyawan;
                } else {
                    $nomor = '-';
                }
                return $nomor;
            });
            $column->addColumn('shift', function ($row) use ($holding) {
                $shift = $row->shift;
                if ($shift != NULL) {
                    $shift = $row->shift;
                } else {
                    $shift = '-';
                }
                return $shift;
            });
            $column->addColumn('departemen', function ($row) {
                $departemen = '<ul>';
                $printedDepartemen = []; // penanda departemen yang sudah ditampilkan

                if ($row->KaryawanJabatan && $row->KaryawanJabatan->count()) {
                    foreach ($row->KaryawanJabatan as $data) {

                        $namaDepartemen = $data->Departemen->nama_departemen ?? null;

                        // SKIP jika nama departemen sama dengan sebelumnya / sudah ada
                        if (!$namaDepartemen || in_array($namaDepartemen, $printedDepartemen)) {
                            continue;
                        }

                        $printedDepartemen[] = $namaDepartemen;

                        $main = ($data->main == 1)
                            ? '<span class="badge bg-label-success ms-1">Utama</span>'
                            : '';
                        $strong = ($data->main == 1)
                            ? 'style="font-weight: bold;"'
                            : '';
                        $departemen .= '<li ' . $strong . '>' . e($namaDepartemen) . $main . '</li>';
                    }

                    $departemen .= '</ul>';
                } else {
                    $departemen = '-';
                }

                return $departemen;
            });
            $column->addColumn('divisi', function ($row) {
                $divisi = '<ul>';
                $last_divisi = []; // penanda divisi yang sudah ditampilkan

                if ($row->KaryawanJabatan && $row->KaryawanJabatan->count()) {
                    foreach ($row->KaryawanJabatan as $data) {

                        $namaDivisi = $data->Divisi->nama_divisi ?? null;

                        // SKIP jika nama divisi sama dengan sebelumnya / sudah ada
                        if (!$namaDivisi || in_array($namaDivisi, $last_divisi)) {
                            continue;
                        }

                        $last_divisi[] = $namaDivisi;

                        $main = ($data->main == 1)
                            ? '<span class="badge bg-label-success ms-1">Utama</span>'
                            : '';
                        $strong = ($data->main == 1)
                            ? 'style="font-weight: bold;"'
                            : '';
                        $divisi .= '<li ' . $strong . '>' . e($namaDivisi) . $main . '</li>';
                    }

                    $divisi .= '</ul>';
                } else {
                    $divisi = '-';
                }

                return $divisi;
            });
            $column->addColumn('jabatan', function ($row) {
                $jabatan = '<ul>';
                $last_jabatan = []; // penanda departemen yang sudah ditampilkan

                if ($row->KaryawanJabatan && $row->KaryawanJabatan->count()) {
                    foreach ($row->KaryawanJabatan as $data) {

                        $namaBagian = $data->Bagian->nama_bagian ?? null;
                        $namaJabatan = $data->Jabatan->nama_jabatan ?? null;

                        $jabatan_full = $namaJabatan . ' - ' . $namaBagian;


                        if (!$jabatan_full || in_array($jabatan_full, $last_jabatan)) {
                            continue;
                        }

                        $last_jabatan[] = $jabatan_full;

                        $main = ($data->main == 1)
                            ? '<span class="badge bg-label-success ms-1">Utama</span>'
                            : '';
                        $strong = ($data->main == 1)
                            ? 'style="font-weight: bold;"'
                            : '';
                        $jabatan .= '<li ' . $strong . '>' . e($jabatan_full) . $main . '</li>';
                    }

                    $jabatan .= '</ul>';
                } else {
                    $jabatan = '-';
                }

                return $jabatan;
            });
            $column->addColumn('total_datang_lebih_awal', function ($row) use ($period, $non_shift) {
                return $row->total_datang_lebih_awal ?? 0;
            });
            $column->addColumn('total_overtime_pulang', function ($row) use ($period, $non_shift) {
                $mapping_shift = $row->MappingShift;
                if ($row->shift == 'NON SHIFT') {
                    $row->total_overtime_pulang = 0;
                } else {
                    foreach ($mapping_shift as $data) {
                        if ($data->nama_shift != 'LIBUR') {
                            if ($data->Shift != NULL) {
                                $jam_pulang = Carbon::parse($data->Shift->jam_keluar)->format('H:i');
                                // return $jam_pulang;
                                $logs = $row->Absensi->filter(function ($log) use ($data) {
                                    return Carbon::parse($log->LogTime)->isSameDay($data->tanggal_pulang);
                                });
                                if ($logs->isNotEmpty()) {
                                    $logtime  = $logs->max('LogTime'); // ambil check-in paling Akhir
                                    $check_in = Carbon::parse($logtime)->format('H:i');
                                    $batas_max = Carbon::parse($data->Shift->jam_keluar)->subHours(3)->format('H:i');
                                    // return $check_in . ' - ' . $jam_pulang . ' - ' . $batas_max;
                                    if ($check_in >= $batas_max && $check_in <= $jam_pulang) {
                                        $row->pulang_cepat += 1;
                                    }
                                    // return $check_in . ' - ' . $plus5 . ' - ' . $plus15;
                                }
                            }
                        }
                    }
                }
                // return [$check_in, ' ', $jam_pulang, ' ', $batas_max];
                return $row->pulang_cepat ?? 0;
            });
            $column->addColumn('total_hadir_tepat_waktu', function ($row) use ($period, $non_shift) {
                return $row->tepat_waktu ?? 0;
            });
            $column->addColumn('total_hadir_telat_hadir', function ($row) use ($period, $non_shift) {

                return $row->telat_ringan ?? 0;
            });
            $column->addColumn('total_hadir_telat_hadir1', function ($row) use ($period, $non_shift) {
                return $row->telat_berat ?? 0;
            });
            $column->addColumn('total_pulang_cepat', function ($row) use ($period, $non_shift) {
                $mapping_shift = $row->MappingShift;
                if ($row->shift == 'NON SHIFT') {
                    foreach ($period as $data) {
                        $jam_pulang = Carbon::parse($non_shift->jam_keluar)->format('H:i');
                        // return $jam_pulang;
                        $logs = $row->Absensi->filter(function ($log) use ($data) {
                            return Carbon::parse($log->LogTime)->isSameDay($data->format('Y-m-d'));
                        });
                        if ($logs->isNotEmpty()) {
                            $logtime  = $logs->max('LogTime'); // ambil check-in paling Akhir
                            $check_in = Carbon::parse($logtime)->format('H:i');
                            $batas_max = Carbon::parse($non_shift->jam_keluar)->subHours(3)->format('H:i');
                            // return $check_in . ' - ' . $jam_pulang . ' - ' . $batas_max;
                            if ($check_in >= $batas_max && $check_in <= $jam_pulang) {
                                $row->pulang_cepat += 1;
                            }
                            // return $check_in . ' - ' . $plus5 . ' - ' . $plus15;
                        }
                    }
                } else {
                    foreach ($mapping_shift as $data) {
                        if ($data->nama_shift != 'LIBUR') {
                            if ($data->Shift != NULL) {
                                $jam_pulang = Carbon::parse($data->Shift->jam_keluar)->format('H:i');
                                // return $jam_pulang;
                                $logs = $row->Absensi->filter(function ($log) use ($data) {
                                    return Carbon::parse($log->LogTime)->isSameDay($data->tanggal_pulang);
                                });
                                if ($logs->isNotEmpty()) {
                                    $logtime  = $logs->max('LogTime'); // ambil check-in paling Akhir
                                    $check_in = Carbon::parse($logtime)->format('H:i');
                                    $batas_max = Carbon::parse($data->Shift->jam_keluar)->subHours(3)->format('H:i');
                                    // return $check_in . ' - ' . $jam_pulang . ' - ' . $batas_max;
                                    if ($check_in >= $batas_max && $check_in <= $jam_pulang) {
                                        $row->pulang_cepat += 1;
                                    }
                                    // return $check_in . ' - ' . $plus5 . ' - ' . $plus15;
                                }
                            }
                        }
                    }
                }
                // return [$check_in, ' ', $jam_pulang, ' ', $batas_max];
                return $row->pulang_cepat ?? 0;
            });
            $column->addColumn('total_izin_true', function ($row) use ($now, $now1) {
                // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');

                return $row->sakit ?? 0;
            });
            $column->addColumn('total_cuti_true', function ($row) use ($now, $now1) {

                $cuti_detail = CutiDetail::where('id_karyawan', $row->id)
                    ->with('Cuti')
                    ->whereHas('Cuti', function ($query) {
                        $query->whereNull('kategori_cuti_id');
                    })
                    ->whereBetween('tgl_cuti', [$now, $now1])
                    ->where('status', 1)
                    ->distinct('tgl_cuti')
                    ->count();
                $row->total_cuti = $cuti_detail;
                return $cuti_detail;
            });
            $column->addColumn('total_cuti_non', function ($row) use ($now, $now1) {

                $cuti_detail = CutiDetail::where('id_karyawan', $row->id)
                    ->with('Cuti')
                    ->whereHas('Cuti', function ($query) {
                        $query->whereNotNull('kategori_cuti_id');
                    })
                    ->whereBetween('tgl_cuti', [$now, $now1])
                    ->where('status', 1)
                    ->distinct('tgl_cuti')
                    ->count();
                $row->total_cuti_non = $cuti_detail;
                return $cuti_detail;
            });
            $column->addColumn('total_tidak_masuk', function ($row) use ($now, $now1) {
                // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');

                return $row->tidak_masuk ?? 0;
            });
            $column->addColumn('total_dinas_true', function ($row) use ($now, $now1) {
                return $row->perdin ?? 0;
            });
            $column->addColumn('tidak_hadir_kerja', function ($row) use ($period, $non_shift) {

                return $row->tidak_hadir ?? 0;
            });
            $column->addColumn('tidak_presensi_masuk', function ($row) {
                return $row->tidak_presensi_masuk ?? 0;
            });
            $column->addColumn('tidak_presensi_pulang', function ($row) {
                return $row->tidak_presensi_pulang ?? 0;
            });
            $column->addColumn('tukar_libur', function ($row) {
                return $row->tukar_libur ?? 0;
            });
            $column->addColumn('total_libur', function ($row) use ($period) {
                $tukar = $row->tukar_libur ?? 0;
                $libur = $row->libur ?? 0;
                $total_libur = $libur + $tukar;

                return $row->total_libur = $total_libur;
            });
            $column->addColumn('total_hadir', function ($row) {
                $total_tepat_waktu = $row->tepat_waktu ?? 0;
                $total_telat_berat = $row->telat_berat ?? 0;
                $total_datang_lebih_awal = $row->total_datang_lebih_awal ?? 0;
                $total_telat_ringan = $row->telat_ringan ?? 0;
                $total_tidak_presensi_masuk = $row->tidak_presensi_masuk ?? 0;
                $total_perdin = $row->perdin ?? 0;

                $total_hadir = $total_tepat_waktu + $total_telat_berat + $total_telat_ringan + $total_datang_lebih_awal + $total_tidak_presensi_masuk + $total_perdin;
                if ($total_hadir == 0) {
                    $jumlah_hadir = '<h6><span class="badge bg-label-danger">0</span></h6>';
                } else {
                    $jumlah_hadir = '<h6><span class="badge bg-label-success">' . $total_hadir . '</span></h6>';
                }
                $row->total_hadir = $total_hadir;
                // return $total_tepat_waktu . ' ' . $total_telat_berat . ' ' . $total_telat_ringan . ' ' . $total_datang_lebih_awal . ' ' . $total_tidak_presensi_masuk;
                return $jumlah_hadir;
            });
            $column->addColumn('total_tidak_hadir', function ($row) use ($period, $non_shift) {
                // return $row->Absensi;
                $total_cuti_true = ($row->total_cuti ?? 0) + ($row->total_cuti_non ?? 0);
                $total_izin_true = $row->sakit ?? 0;
                $total_dinas_true = $row->total_dinas ?? 0;
                $total_libur = $row->total_libur ?? 0;
                $tidak_hadir = $row->tidak_hadir ?? 0;
                $tidak_masuk = $row->tidak_masuk ?? 0;
                $total_tidak_hadir =  $total_izin_true + $total_dinas_true + $total_cuti_true + $total_libur + $tidak_hadir + $tidak_masuk;
                $jumlah_total_tidak_hadir = '<h6><span class="badge bg-label-danger">' . $total_tidak_hadir . '</span></h6>';
                $row->total_tidak_hadir = $total_tidak_hadir;
                return $jumlah_total_tidak_hadir;
            });
            $column->addColumn('total_semua', function ($row) use ($now, $now1) {
                $today = Carbon::now()->format('Y-m-d');
                // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');

                $total_tidak_hadir = $row->total_tidak_hadir ?? 0;
                // $total_izin = $row->sakit ?? 0;
                // $tukar = $row->tukar_libur ?? 0;
                $total_hadir = $row->total_hadir ?? 0;
                $total_semua = ($total_tidak_hadir  + $total_hadir);
                if ($total_semua == 0) {
                    $total_semua = '<h6><span class="badge bg-label-danger">0</span></h6>';
                } else {
                    $total_semua = '<h6><span class="badge bg-label-success">' . $total_semua . '</span></h6>';
                }
                $row->total_semua = $total_semua;
                return $total_semua;
            });

            $rawCols = array_merge([
                'nama_karyawan',
                'nomor_identitas_karyawan',
                'shift',
                'total_hadir_tepat_waktu',
                'tukar_libur',
                'total_libur',
                'btn_detail',
                'total_hadir_telat_hadir',
                'total_hadir_telat_hadir1',
                'total_pulang_cepat',
                'total_izin_true',
                'total_cuti_true',
                'total_cuti_non',
                'total_dinas_true',
                'tidak_hadir_kerja',
                'total_datang_lebih_awal',
                'total_overtime_pulang',
                'departemen',
                'divisi',
                'jabatan',
                'total_tidak_masuk',
                'tidak_presensi_masuk',
                'tidak_presensi_pulang',
                'total_hadir',
                'total_tidak_hadir',
                'total_semua'
            ], $data_tanggal);

            return $column->rawColumns($rawCols)->make(true);
        }
    }
    public function cetak_form_cuti($id, $cuti_id)
    {
        $cuti_detail = CutiDetail::where('id', $cuti_id)->first();
        $cuti = Cuti::with('KategoriCuti', 'KaryawanPengganti', 'Atasan1', 'Atasan2')->where('id', $cuti_detail->id_cuti)->first();
        $jumlah_hari = CutiDetail::where('id_cuti', $cuti->id)->count();
        $karyawn_jabatan = KaryawanJabatan::with('Departemen', 'Jabatan', 'Divisi', 'Bagian')->where('karyawan_id', $id)->where('is_active', 1)->where('main', 1)->first();
        $user_karyawan = Karyawan::where('id', $id)->first();
        $holding = Holding::where('id', $user_karyawan->kontrak_kerja)->first();
        $data = [
            'title' => 'domPDF in Laravel 10',
            'data_cuti' => $cuti,
            'karyawan_jabatan' => $karyawn_jabatan,
            'user_karyawan' => $user_karyawan,
            'holding' => $holding,
            'jumlah_hari' => $jumlah_hari,
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
            'chroot' => [storage_path('app/public')],
        ])
            ->loadView('users/cuti/form_cuti', $data)
            ->setPaper('letter', 'potrait');

        return $pdf->stream(
            'FORM_PENGAJUAN_CUTI_' .
                $user_karyawan->name . '_' .
                now()->format('Y-m-d_H-i-s') .
                '.pdf'
        );
    }
    public function datatable_finger(Request $request, $holding)
    {
        // 1. Ambil semua karyawan dari MySQL
        $holding = Holding::where('holding_code', $holding)->first();
        $now = Carbon::parse($request->start_date);
        $now1 = Carbon::parse($request->end_date);
        $period = CarbonPeriod::create($now, $now1);

        $query  = Karyawan::with(['Departemen' => function ($q) {
            $q->select('id', 'nama_departemen');
        }])
            ->with(['Absensi' => function ($q) use ($now, $now1) {
                $q->whereBetween('LogTime', [$now, $now1]);
                $q->select('EnrollNumber', 'LogTime');
            }])
            ->with(['MappingShift' => function ($q) use ($now, $now1) {
                $q->whereBetween('tanggal_masuk', [$now, $now1]);
            }])
            ->where('kontrak_kerja', $holding->id)
            ->where('kategori', 'Karyawan Bulanan')
            ->where('status_aktif', 'AKTIF');
        $table = $query->select('karyawans.dept_id', 'karyawans.name', 'karyawans.id', 'karyawans.nomor_identitas_karyawan', 'karyawans.shift')
            ->orderBy('karyawans.name', 'ASC')
            // ->limit(10)
            ->get();
        $non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
        $column = DataTables::of($table);
        // dd($table);
        foreach ($period as $date) {
            $colName = 'tanggal_' . $date->format('dmY');
            $column->addColumn('tanggal_' . $date->format('dmY'), function ($row) use ($date, $non_shift) {
                // ambil log dari eager load Absensi
                if ($row->shift == 'NON SHIFT') {
                    // return $date->toDateString();
                    $jam_masuk = $row->Absensi->whereBetween('LogTime', [$date->toDateString() . ' 00:00:00', $date->toDateString() . ' 23:59:59']);
                    // dd($jam_masuk);
                    if (!$jam_masuk) {
                        return '<span class="badge bg-danger">BELUM DITENTUKAN</span>';
                    }
                    if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                        return '<span class="badge bg-info">LIBUR</span>';
                    }
                    $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($non_shift, $date) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                        return $getcheckIn->between($start, $end);
                    });
                    if ($logs_absensi_masuk->isNotEmpty()) {
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        if ($checkIn > $non_shift->jam_terlambat) {
                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                        } else {
                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                        }
                    } else {
                        $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($date) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d'));
                            return $getcheckIn->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_masuk->isNotEmpty()) {
                            $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkIn = '';
                            } else {
                                $checkIn =
                                    'Tidak Presensi';
                            }
                        }
                    }
                    $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($date, $non_shift) {
                        $getcheckOut = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                        return $getcheckOut->between($start, $end);
                    });
                    // return $logs_absensi_pulang;
                    if ($logs_absensi_pulang->isNotEmpty()) {
                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        if ($checkOut < $non_shift->jam_keluar) {
                            $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                        } else {
                            $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                        }
                    } else {
                        $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($date) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d'));
                            return $getcheckOut->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $checkOut  = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                            if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                $checkOut = 'Tidak Presensi';
                            }
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkOut = '';
                            } else {
                                $checkOut = 'Tidak Presensi';
                            }
                        }
                    }
                    $nama_shift = $row->shift;
                } else {
                    $jam_masuk = $row->MappingShift->firstWhere('tanggal_masuk', $date->toDateString());
                    if (!$jam_masuk) {
                        return '<span class="badge bg-danger">BELUM DITENTUKAN</span>';
                    }
                    if ($jam_masuk->status_presensi == 'LIBUR') {
                        return '<span class="badge bg-info">Libur</span>';
                    }
                    if ($jam_masuk->status_presensi == 'CUTI') {
                        return '<span class="badge bg-info">CUTI</span>';
                    }
                    if ($jam_masuk->status_presensi == 'SAKIT') {
                        return '<span class="badge bg-info">SAKIT</span>';
                    }
                    if ($jam_masuk->status_presensi == 'tidak_masuk') {
                        return '<span class="badge bg-info">IZIN TIDAK MASUK</span>';
                    }

                    $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($jam_masuk->tanggal_masuk . ' ' . $jam_masuk->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($jam_masuk->tanggal_masuk . ' ' . $jam_masuk->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                        return $getcheckIn->between($start, $end);
                    });
                    // return $start . ' - ' . $end;
                    // return $logs_absensi_masuk . ' - ' . Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('Y-m-d H:i') . ' - ' . Carbon::parse($logs_absensi_masuk->max('LogTime'))->format('Y-m-d H:i');
                    if ($logs_absensi_masuk->isNotEmpty()) {
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        if ($checkIn > $jam_masuk->Shift->jam_terlambat) {
                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                        } else {
                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                        }
                    } else {
                        $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($jam_masuk->tanggal_masuk);
                            return $getcheckIn->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_masuk->isNotEmpty()) {
                            $checkIn  = Carbon::parse($logs_absensi_masuk->max('LogTime'))->format('H:i');
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkIn = '';
                            } else {
                                $checkIn =
                                    'Tidak Presensi';
                            }
                        }
                    }

                    $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                        $getcheckOut = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                        return $getcheckOut->between($start, $end);
                    });
                    // return $logs_absensi_pulang;
                    if ($logs_absensi_pulang->isNotEmpty()) {
                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        if (Carbon::parse($jam_masuk->tanggal_pulang)->format('Y-m-d') > $date->format('Y-m-d')) {

                            // $checkOut = $row->Absensi;
                            if ($checkOut < $jam_masuk->Shift->jam_keluar) {
                                $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">(' . $checkOut  . Carbon::parse($jam_masuk->tanggal_pulang)->format('d-m-Y') . ')' . '</span>';
                            } else {
                                $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span> (' . Carbon::parse($jam_masuk->tanggal_pulang)->format('d-m-Y') . ')';
                            }
                        } else {
                            if ($checkOut < $jam_masuk->Shift->jam_keluar) {
                                $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                            } else {
                                $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                            }
                        }
                    } else {
                        $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($jam_masuk->tanggal_pulang);
                            return $getcheckOut->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $checkOut  = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                            if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                $checkOut = 'Tidak Presensi';
                            }
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkOut = '';
                            } else {
                                $checkOut = 'Tidak Presensi';
                            }
                        }
                    }
                    $nama_shift = $jam_masuk->Shift->nama_shift;
                }
                if ($checkIn == 'Tidak Presensi' && $checkOut == 'Tidak Presensi') {
                    $check_all = '<span style="color:red;">' . 'TIDAK HADIR' . '</span>';
                    return $check_all;
                }
                // return $logs_absensi_masuk . ' ' . $logs_absensi_pulang;
                return '<span style="white-space:nowrap;">' . $checkIn . '&nbsp;-&nbsp;' . $checkOut . '</span><br>&nbsp;<span>(Shift : ' . $nama_shift . ')</span>';
            });
            $data_tanggal[] = $colName;
        }
        $column->addColumn('action', function ($row) {
            return '<button class="btn btn-sm btn-primary" onclick="edit(' . $row->USERID . ')">Edit</button>';
        });
        $column->addColumn('departemen', function ($row) {
            if ($row->Departemen != NULL) {
                $departemen = $row->Departemen->nama_departemen;
            } else {
                $departemen = '-';
            }
            return $departemen;
        });
        $column->addColumn('shift', function ($row) {

            return $row->shift;
        });
        $column->addColumn('jumlah_hadir', function ($row) use ($period, $non_shift) {
            if ($row->shift == 'NON SHIFT') {
                if ($row->Absensi->isEmpty()) {
                    $row->jumlah_hadir = 0;
                    $jumlah_hadir = '<h6><span class="badge bg-label-danger">0</span></h6>';
                } else {
                    foreach ($period as $data) {
                        $logs = $row->Absensi->filter(function ($log) use ($data, $non_shift) {
                            $logTime = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($data->format('Y-m-d'))->setTimeFromTimeString(Carbon::parse($non_shift->jam_min_masuk)->format('H:i:59'));
                            $end = Carbon::parse($data->format('Y-m-d'))->setTimeFromTimeString(Carbon::parse($non_shift->jam_terlambat)->addHours(3)->format('H:i:59'));
                            return $logTime->between($start, $end);
                        });
                        // return $logs;
                        if ($logs->isNotEmpty()) {
                            $row->jumlah_hadir = ($row->jumlah_hadir ?? 0) + 1;
                            $jumlah_hadir = '<h6><span class="badge bg-label-success">' . $row->jumlah_hadir . '</span></h6>';
                        }
                    }
                }
                return $jumlah_hadir;
            } else {
                $mapping_shift = $row->MappingShift;
                // return $mapping_shift;
                if ($mapping_shift->isEmpty()) {
                    $row->jumlah_hadir = 0;
                    $jumlah_hadir = '<h6><span class="badge bg-label-danger">0</span></h6>';
                } else {
                    $jumlah_hadir = $row->MappingShift
                        ->where('status_presensi', '!=', 'LIBUR')
                        ->filter(function ($shift) use ($row) {
                            // cek apakah ada absensi di tanggal shift
                            return $row->Absensi->contains(function ($log) use ($shift) {
                                return Carbon::parse($log->LogTime)->isSameDay($shift->tanggal_masuk);
                            });
                        })
                        ->count();
                    // simpan di row biar bisa dipakai di kolom lain
                    $row->jumlah_hadir = $jumlah_hadir;
                    $row->mapping_shift = $mapping_shift;
                }
                return '<h6><span class="badge bg-label-success">' . $jumlah_hadir . '</span></h6>';
            }
        })
            ->addColumn('name', function ($row) {
                return $row->name;
            });

        $column->addCoulmn('tidak_presensi_pulang', function ($row) {});
        $column->addColumn('total_cuti', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_cuti = $row->MappingShift->whereBetween('tanggal_masuk', [$now, $now1])
                ->where('keterangan_presensi', 'CUTI')
                ->where('keterangan_presensi_pulang', 'CUTI')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'TRUE')
                        ->orWhere('keterangan_cuti', 'True')
                        ->orWhere('keterangan_cuti', 'true');
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'FALSE')
                        ->orWhere('keterangan_izin', 'false')
                        ->orWhere('keterangan_izin', NULL)
                        ->orWhere('keterangan_izin', '');
                })
                ->count();
            $row->cuti = $total_cuti;
            return $total_cuti;
        });

        $column->addColumn('total_libur', function ($row) use ($now, $now1) {

            $total_libur = $row->MappingShift
                ->where('status_presensi', 'LIBUR')->count();
            $row->libur = $total_libur;
            return $total_libur;
        });
        $column->addColumn('total_semua', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_hadir = ($row->jumlah_hadir ?? 0);
            $total_libur = ($row->libur ?? 0);
            $total_cuti = ($row->cuti ?? 0);

            $total_tidak_hadir = ($row->total_tidak_hadir_kerja ?? 0);
            $total_semua = ($total_hadir + $total_cuti  + $total_libur + $total_tidak_hadir);
            return $total_semua;
        });;

        $rawCols = array_merge([
            'action',
            'total_tidak_hadir_kerja',
            'total_libur',
            'total_cuti',
            'name',
            'jumlah_hadir',
            'departemen',
            'shift'
        ], $data_tanggal);
        return $column->rawColumns($rawCols)
            ->make('true');
    }
    public function datatable(Request $request, $holding)
    {
        // dd($request->filter_month, $holding);
        $holding = Holding::where('holding_code', $holding)->first();
        // if (request()->ajax()) {

        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $period = CarbonPeriod::create($now, $now1);

        // dd($holding);
        // dd($tgl_mulai, $tgl_selesai);
        $query = Karyawan::with(['Departemen' => function ($q) {
            $q->select('id', 'nama_departemen');
        }])
            ->with(['Absensi' => function ($q) use ($now, $now1) {
                $q->whereBetween('LogTime', [$now, $now1])
                    ->select('EnrollNumber', 'LogTime'); // supaya jelas field yg dibawa
            }])
            ->with(['MappingShift' => function ($q) use ($now, $now1) {
                $q->with('Shift');
                $q->whereBetween('tanggal_masuk', [$now, $now1]);
            }])->where('kontrak_kerja', $holding->id)
            ->where('kategori', 'Karyawan Bulanan')
            ->where('status_aktif', 'AKTIF');
        $table = $query->select('karyawans.dept_id', 'karyawans.name', 'karyawans.id', 'karyawans.nomor_identitas_karyawan', 'karyawans.shift')
            ->orderBy('karyawans.name', 'ASC')
            // ->limit(10)
            ->get();
        // dd($table);
        $column = DataTables::of($table);
        $non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
        foreach ($period as $date) {
            $colName = 'tanggal_' . $date->format('dmY');
            $column->addColumn('tanggal_' . $date->format('dmY'), function ($row) use ($date, $non_shift) {
                // ambil log dari eager load Absensi
                if ($row->shift == 'NON SHIFT') {
                    // return $date->toDateString();
                    $jam_masuk = $row->Absensi->whereBetween('LogTime', [$date->toDateString() . ' 00:00:00', $date->toDateString() . ' 23:59:59']);
                    if (!$jam_masuk) {
                        return '<span class="badge bg-danger">BELUM DITENTUKAN</span>';
                    }
                    if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                        return '<span class="badge bg-info">LIBUR</span>';
                    }
                    $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($non_shift, $date) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                        return $getcheckIn->between($start, $end);
                    });
                    if ($logs_absensi_masuk->isNotEmpty()) {
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        if ($checkIn > $non_shift->jam_terlambat) {
                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                        } else {
                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                        }
                    } else {
                        $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($date) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d'));
                            return $getcheckIn->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_masuk->isNotEmpty()) {
                            $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkIn = '';
                            } else {
                                $checkIn =
                                    'Tidak Presensi';
                            }
                        }
                    }
                    $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($date, $non_shift) {
                        $getcheckOut = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                        return $getcheckOut->between($start, $end);
                    });
                    // return $logs_absensi_pulang;
                    if ($logs_absensi_pulang->isNotEmpty()) {
                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        if ($checkOut < $non_shift->jam_keluar) {
                            $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                        } else {
                            $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                        }
                    } else {
                        $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($date) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d'));
                            return $getcheckOut->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $checkOut  = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                            if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                $checkOut = 'Tidak Presensi';
                            }
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkOut = '';
                            } else {
                                $checkOut = 'Tidak Presensi';
                            }
                        }
                    }
                    $nama_shift = $row->shift;
                } else {
                    $jam_masuk = $row->MappingShift->firstWhere('tanggal_masuk', $date->toDateString());
                    if (!$jam_masuk) {
                        return '<span class="badge bg-danger">BELUM DITENTUKAN</span>';
                    }
                    if ($jam_masuk->status_presensi == 'LIBUR') {
                        return '<span class="badge bg-info">Libur</span>';
                    }


                    $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                        $getcheckIn = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($jam_masuk->tanggal_masuk . ' ' . $jam_masuk->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($jam_masuk->tanggal_masuk . ' ' . $jam_masuk->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                        return $getcheckIn->between($start, $end);
                    });
                    // return $start . ' - ' . $end;
                    // return $logs_absensi_masuk . ' - ' . Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('Y-m-d H:i') . ' - ' . Carbon::parse($logs_absensi_masuk->max('LogTime'))->format('Y-m-d H:i');
                    if ($logs_absensi_masuk->isNotEmpty()) {
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        if ($checkIn > $jam_masuk->Shift->jam_terlambat) {
                            $checkIn = '<span style="color:rgba(var(--bs-warning-rgb));">' . $checkIn . '</span>';
                        } else {
                            $checkIn = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkIn . '</span>';
                        }
                    } else {
                        $logs_absensi_masuk = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($jam_masuk->tanggal_masuk);
                            return $getcheckIn->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_masuk->isNotEmpty()) {
                            $checkIn  = Carbon::parse($logs_absensi_masuk->max('LogTime'))->format('H:i');
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkIn = '';
                            } else {
                                $checkIn =
                                    'Tidak Presensi';
                            }
                        }
                    }

                    $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                        $getcheckOut = Carbon::parse($log->LogTime);
                        $start = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                        $end = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $jam_masuk->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                        return $getcheckOut->between($start, $end);
                    });
                    // return $logs_absensi_pulang;
                    if ($logs_absensi_pulang->isNotEmpty()) {
                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        if (Carbon::parse($jam_masuk->tanggal_pulang)->format('Y-m-d') > $date->format('Y-m-d')) {

                            // $checkOut = $row->Absensi;
                            if ($checkOut < $jam_masuk->Shift->jam_keluar) {
                                $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">(' . $checkOut  . Carbon::parse($jam_masuk->tanggal_pulang)->format('d-m-Y') . ')' . '</span>';
                            } else {
                                $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span> (' . Carbon::parse($jam_masuk->tanggal_pulang)->format('d-m-Y') . ')';
                            }
                        } else {
                            if ($checkOut < $jam_masuk->Shift->jam_keluar) {
                                $checkOut = '<span style="color:rgba(var(--bs-danger-rgb));">' . $checkOut . '</span>';
                            } else {
                                $checkOut = '<span style="color:rgba(var(--bs-success-rgb));">' . $checkOut . '</span>';
                            }
                        }
                    } else {
                        $logs_absensi_pulang = $row->Absensi->filter(function ($log) use ($jam_masuk) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($jam_masuk->tanggal_pulang);
                            return $getcheckOut->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $checkOut  = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                            if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                $checkOut = 'Tidak Presensi';
                            }
                        } else {
                            if ($date->format('Y-m-d') > Carbon::now()->format('Y-m-d')) {
                                $checkOut = '';
                            } else {
                                $checkOut = 'Tidak Presensi';
                            }
                        }
                    }
                    $nama_shift = $jam_masuk->Shift->nama_shift;
                }
                if ($checkIn == 'Tidak Presensi' && $checkOut == 'Tidak Presensi') {
                    $check_all = '<span style="color:red;">' . 'TIDAK HADIR' . '</span>';
                    return $check_all;
                }
                // return $logs_absensi_masuk . ' ' . $logs_absensi_pulang;
                return '<span style="white-space:nowrap;">' . $checkIn . '&nbsp;-&nbsp;' . $checkOut . '</span><br><span>(Shift : ' . $nama_shift . ')</span>';
            });
            $data_tanggal[] = $colName;
        }
        $column->addColumn('total_hadir_kerja', function ($row) use ($now, $now1) {
            $mapping_shift = $row->MappingShift;
            foreach ($mapping_shift as $data) {
                if ($data->nama_shift != 'LIBUR') {
                    if ($data->Shift != NULL) {
                        $logs_masuk = $row->Absensi->filter(function ($log) use ($data) {
                            $logTime = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($data->tanggal_masuk)->setTimeFromTimeString(Carbon::parse($data->Shift->jam_min_masuk)->format('H:i:59'));
                            $end = Carbon::parse($data->tanggal_masuk)->setTimeFromTimeString(Carbon::parse($data->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('H:i:59'));
                            return $logTime->between($start, $end);
                        });

                        // return $logs;
                        if ($logs_masuk->isNotEmpty()) {
                            $row->hadir_kerja = ($row->hadir_kerja ?? 0) + 1;
                        }
                    }
                }
            }


            return $row->hadir_kerja ?? 0;
        });
        // dd($oke);
        $column->addColumn('total_tidak_hadir_kerja', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_tidak_hadir_kerja = $row->MappingShift->where(function ($query) {
                $query->where('keterangan_dinas', 'FALSE')
                    ->orWhere('keterangan_dinas', 'false')
                    ->orWhere('keterangan_dinas', NULL)
                    ->orWhere('keterangan_dinas', '');
            })
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'FALSE')
                        ->orWhere('keterangan_cuti', 'false')
                        ->orWhere('keterangan_cuti', NULL)
                        ->orWhere('keterangan_cuti', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'FALSE')
                        ->orWhere('keterangan_izin', 'false')
                        ->orWhere('keterangan_izin', NULL)
                        ->orWhere('keterangan_izin', '');
                });
            foreach ($total_tidak_hadir_kerja as $data) {
                if ($data->nama_shift != 'LIBUR') {
                    if ($data->Shift != NULL) {
                        $logs_masuk = $row->Absensi->filter(function ($log) use ($data) {
                            $logTime = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($data->tanggal_masuk)->setTimeFromTimeString(Carbon::parse($data->Shift->jam_min_masuk)->format('H:i:59'));
                            $end = Carbon::parse($data->tanggal_masuk)->setTimeFromTimeString(Carbon::parse($data->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('H:i:59'));
                            return $logTime->between($start, $end);
                        });

                        // return $logs;
                        if ($logs_masuk->isNotEmpty()) {
                            $row->hadir_kerja = ($row->hadir_kerja ?? 0) + 1;
                        }
                    }
                }
            }


            return $row->hadir_kerja ?? 0;
            return $total_tidak_hadir_kerja;
        });
        $column->addColumn('total_cuti', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_cuti = MappingShift::where('user_id', $row->id)
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->whereNotNull('is_active')
                ->where('keterangan_presensi', 'CUTI')
                ->where('keterangan_presensi_pulang', 'CUTI')
                ->where('status_presensi', 'TIDAK HADIR KERJA')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'TRUE')
                        ->orWhere('keterangan_cuti', 'True')
                        ->orWhere('keterangan_cuti', 'true');
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'FALSE')
                        ->orWhere('keterangan_izin', 'false')
                        ->orWhere('keterangan_izin', NULL)
                        ->orWhere('keterangan_izin', '');
                })
                ->count();
            return $total_cuti;
        });
        $column->addColumn('total_izin_sakit', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_izin_sakit = MappingShift::where('user_id', $row->id)
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->whereNotNull('is_active')
                ->where('keterangan_presensi', 'IZIN SAKIT')
                ->where('keterangan_presensi_pulang', 'IZIN SAKIT')
                ->where('status_presensi', 'TIDAK HADIR KERJA')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'FALSE')
                        ->orWhere('keterangan_cuti', 'false')
                        ->orWhere('keterangan_cuti', NULL);
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'TRUE')
                        ->orWhere('keterangan_izin', 'True')
                        ->orWhere('keterangan_izin', 'true');
                })
                ->count();
            return $total_izin_sakit;
        });
        $column->addColumn('total_izin_lainnya', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_izin_lainnya = MappingShift::where('user_id', $row->id)
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->whereNotNull('is_active')
                ->where('keterangan_presensi', 'IZIN TIDAK MASUK')
                ->where('keterangan_presensi_pulang', 'IZIN TIDAK MASUK')
                ->where('status_presensi', 'TIDAK HADIR KERJA')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'FALSE')
                        ->orWhere('keterangan_cuti', 'false')
                        ->orWhere('keterangan_cuti', NULL)
                        ->orWhere('keterangan_cuti', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'TRUE')
                        ->orWhere('keterangan_izin', 'True')
                        ->orWhere('keterangan_izin', 'true');
                })
                ->count();
            return $total_izin_lainnya;
        });
        $column->addColumn('total_libur', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_libur = MappingShift::where('user_id', $row->id)
                ->whereNotNull('is_active')
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->where('status_presensi', 'LIBUR')->count();
            return $total_libur;
        });
        $column->addColumn('total_semua', function ($row) use ($now, $now1) {
            // $id_karyawan = Karyawan::where('nomor_identitas_karyawan', $row->nomor_identitas_karyawan)->value('id');
            $total_hadir = MappingShift::where('user_id', $row->id)
                ->whereNotNull('is_active')
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->where('status_presensi', 'HADIR KERJA')->count();
            $total_libur = MappingShift::where('user_id', $row->id)
                ->whereNotNull('is_active')
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->where('status_presensi', 'LIBUR')->count();
            $total_cuti = MappingShift::where('user_id', $row->id)
                ->whereNotNull('is_active')
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->where('keterangan_presensi', 'CUTI')
                ->where('keterangan_presensi_pulang', 'CUTI')
                ->where('status_presensi', 'TIDAK HADIR KERJA')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'TRUE')
                        ->orWhere('keterangan_cuti', 'True')
                        ->orWhere('keterangan_cuti', 'true');
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'FALSE')
                        ->orWhere('keterangan_izin', 'false')
                        ->orWhere('keterangan_izin', NULL)
                        ->orWhere('keterangan_izin', '');
                })
                ->count();
            $total_izin_sakit = MappingShift::where('user_id', $row->id)
                ->whereNotNull('is_active')
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->where('keterangan_presensi', 'IZIN SAKIT')
                ->where('keterangan_presensi_pulang', 'IZIN SAKIT')
                ->where('status_presensi', 'TIDAK HADIR KERJA')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'FALSE')
                        ->orWhere('keterangan_cuti', 'false')
                        ->orWhere('keterangan_cuti', NULL);
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'TRUE')
                        ->orWhere('keterangan_izin', 'True')
                        ->orWhere('keterangan_izin', 'true');
                })
                ->count();
            $total_izin_lainnya = MappingShift::where('user_id', $row->id)
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->whereNotNull('is_active')
                ->where('keterangan_presensi', 'IZIN TIDAK MASUK')
                ->where('keterangan_presensi_pulang', 'IZIN TIDAK MASUK')
                ->where('status_presensi', 'TIDAK HADIR KERJA')
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'FALSE')
                        ->orWhere('keterangan_cuti', 'false')
                        ->orWhere('keterangan_cuti', NULL)
                        ->orWhere('keterangan_cuti', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'TRUE')
                        ->orWhere('keterangan_izin', 'True')
                        ->orWhere('keterangan_izin', 'true');
                })
                ->count();
            $total_tidak_hadir = MappingShift::where('user_id', $row->id)
                ->whereBetween('tanggal_masuk', [$now, $now1])
                ->whereNotNull('is_active')
                ->where(function ($query) {
                    $query->where('status_presensi', 'TIDAK HADIR KERJA')
                        ->orWhere('status_presensi', NULL);
                })
                ->where(function ($query) {
                    $query->where('keterangan_dinas', 'FALSE')
                        ->orWhere('keterangan_dinas', 'false')
                        ->orWhere('keterangan_dinas', NULL)
                        ->orWhere('keterangan_dinas', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_cuti', 'FALSE')
                        ->orWhere('keterangan_cuti', 'false')
                        ->orWhere('keterangan_cuti', NULL)
                        ->orWhere('keterangan_cuti', '');
                })
                ->where(function ($query) {
                    $query->where('keterangan_izin', 'FALSE')
                        ->orWhere('keterangan_izin', 'false')
                        ->orWhere('keterangan_izin', NULL)
                        ->orWhere('keterangan_izin', '');
                })
                ->count();
            $total_semua = ($total_hadir + $total_cuti + $total_izin_sakit + $total_izin_lainnya + $total_libur + $total_tidak_hadir);
            return $total_semua;
        });
        return $column->rawColumns(['total_hadir_kerja', 'total_tidak_hadir_kerja', 'total_libur', 'total_semua', 'total_izin_lainnya', 'total_izin_sakit', 'total_cuti'])
            ->make(true);
        // }
    }
    public function get_divisi(Request $request)
    {
        // dd($request->all());
        $id_departemen    = $request->departemen_filter ?? [];

        $divisi      = Divisi::with('Departemen')
            ->whereIn('dept_id', (array)$id_departemen)
            ->where(
                'holding',
                $request->holding
            )->orderBy('nama_divisi', 'ASC')
            ->get()
            ->sortBy(function ($item) {
                return $item->Departemen->nama_departemen . ' ' . $item->nama_divisi;
            });
        // dd($divisi);
        if ($divisi == NULL || $divisi == '' || count($divisi) == '0') {
            $select = '<option value="">Pilih Divisi...</option>';
        } else {

            $select_divisi[] = "<option value=''>Pilih Divisi...</option>";
            $currentDept = null;
            foreach ($divisi as $divisi) {
                if ($currentDept !== $divisi->Departemen->nama_departemen) {
                    // tutup optgroup sebelumnya
                    if ($currentDept !== null) {
                        $select_divisi1[] = "</optgroup>";
                    }

                    // buka optgroup baru
                    $currentDept = $divisi->Departemen->nama_departemen;
                    $select_divisi1[] = "<optgroup label='{$divisi->Departemen->nama_departemen}'>";
                }
                $select_divisi1[] = "<option value='$divisi->id'>$divisi->nama_divisi</option>";
            }
            // tutup optgroup terakhir
            if ($currentDept !== null) {
                $select_divisi1[] = "</optgroup>";
            }
            $select = array_merge($select_divisi, $select_divisi1);
        }
        // dd($select);
        return array(
            'select' => $select,
        );
    }
    public function get_bagian(Request $request)
    {
        // dd($request->all());
        $id_divisi    = $request->divisi_filter;
        // dd($end_date);


        $bagian      = Bagian::with('Divisi')->whereIn('divisi_id', $id_divisi)->where('holding', $request->holding)->orderBy('nama_bagian', 'ASC')->get();
        if ($bagian == NULL || $bagian == '' || count($bagian) == '0') {
            $select = "<option value=''>Pilih Bagian...</option>";
        } else {

            $select_bagian[] = "<option value=''>Pilih Bagian...</option>";
            $currentBagian = null;
            foreach ($bagian as $bagian) {
                if ($currentBagian !== $bagian->Divisi->nama_divisi) {
                    // tutup optgroup sebelumnya
                    if ($currentBagian !== null) {
                        $select_bagian1[] = "</optgroup>";
                    }

                    // buka optgroup baru
                    $currentBagian = $bagian->Divisi->nama_divisi;
                    $select_bagian1[] = "<optgroup label='{$bagian->Divisi->nama_divisi}'>";
                }
                $select_bagian1[] = "<option value='$bagian->id'>$bagian->nama_bagian</option>";
            }
            // tutup optgroup terakhir
            if ($currentBagian !== null) {
                $select_bagian1[] = "</optgroup>";
            }
            $select = array_merge($select_bagian, $select_bagian1);
        }
        // dd($select_bagian1);
        return array(
            'select' => $select,
        );
    }
    public function get_jabatan(Request $request)
    {
        $id_bagian    = $request->bagian_filter;
        // dd($period);

        $jabatan      = Jabatan::where('bagian_id', $id_bagian)->where('holding', $request->holding)->orderBy('nama_jabatan', 'ASC')->get();
        // dd($jabatan, $id_bagian, $request->all());
        if ($jabatan == NULL || $jabatan == '' || count($jabatan) == '0') {
            $select = '<option value="">Pilih Jabatan...</option>';
        } else {
            $select_jabatan[] = "<option value=''>Pilih Jabatan...</option>";
            foreach ($jabatan as $jabatan) {
                $select_jabatan1[] = "<option value='$jabatan->id'>$jabatan->nama_jabatan</option>";
            }
            $select = array_merge($select_jabatan, $select_jabatan1);
        }
        // dd($data_columns_header, $data_columns);
        return array(
            'select' => $select,
        );
    }
    public function get_grafik_absensi(Request $request)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $request->holding)->value('id');

        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();
        $period = CarbonPeriod::create($start_date, $end_date);
        $query = MappingShift::with(['Shift', 'User'])
            ->whereBetween('tanggal_masuk', [$start_date, $end_date])
            ->whereNotNull('is_active')
            ->whereHas('User', function ($q) use ($holding, $request) {
                $q->where('kontrak_kerja', $holding);

                if (!empty($request->departemen_filter)) {
                    $q->whereIn('dept_id', $request->departemen_filter);
                }

                if (!empty($request->divisi_filter)) {
                    $q->whereIn('divisi_id', $request->divisi_filter);
                }

                if (!empty($request->bagian_filter)) {
                    $q->whereIn('bagian_id', $request->bagian_filter);
                }

                if (!empty($request->jabatan_filter)) {
                    $q->whereIn('jabatan_id', $request->jabatan_filter);
                }
            });

        $baseQuery = $query->get();
        $label_absensi = [];


        foreach ($period as $date) {
            $tanggal = $date->format('Y-m-d');
            $label_absensi[] = $tanggal;

            // default 0
            $data_absensi_masuk_tepatwaktu[$tanggal] = 0;
            $data_absensi_masuk_telat[$tanggal] = 0;
            $data_absensi_masuk_tidak_hadir[$tanggal] = 0;
            $data_absensi_masuk_cuti[$tanggal] = 0;
        }
        // dd($baseQuery);
        foreach ($baseQuery as $shiftData) {
            if ($shiftData->status_presensi == 'LIBUR' || !$shiftData->Shift) continue;
            $tanggal = Carbon::parse($shiftData->tanggal_masuk)->format('Y-m-d');
            $timeplus5 = Carbon::parse($shiftData->Shift->jam_masuk)->addMinutes(6);
            // dd($start_hours);
            $logs = AttendanceLog::where('EnrollNumber', $shiftData->user->nomor_identitas_karyawan)->whereDate('LogTime', $shiftData->tanggal_masuk)->first();
            // dd($logs);
            if ($logs != NULL) {
                $logtime = $logs->LogTime;
                $check_in = Carbon::parse($logtime);
                $check_in5 = Carbon::parse($shiftData->tanggal_masuk)->setTimeFrom($timeplus5);
                $check_inmin1hours = Carbon::parse($shiftData->tanggal_masuk)->setTimeFrom($shiftData->Shift->jam_masuk)->subHours(1);
                // dd([
                //     'logtime'          => $check_in->toDateTimeString(),
                //     'check_inmin1hours' => $check_inmin1hours->toDateTimeString(),
                //     'check_in5'        => $check_in5->toDateTimeString(),
                // ]);

                if ($check_in->toDateTimeString() > $check_inmin1hours->toDateTimeString() && $check_in->toDateTimeString() < $check_in5->toDateTimeString()) {
                    $data_absensi_masuk_tepatwaktu[$tanggal]++;
                } else {
                    $data_absensi_masuk_telat[$tanggal]++;
                }
            } else {
                if ($shiftData->status_presensi == 'TIDAK HADIR KERJA') {
                    if ($shiftData->keterangan_presensi == 'CUTI' && $shiftData->keterangan_cuti == 'TRUE') {
                        $data_absensi_masuk_cuti[$tanggal]++;
                    } else {
                        if ($shiftData->tanggal_masuk > date('Y-m-d')) {
                            continue;
                        }
                        $data_absensi_masuk_tidak_hadir[$tanggal]++;
                    }
                }
            }
        }

        $data_result = [
            'label_absensi' => $label_absensi,
            'data_absensi_masuk_tepatwaktu' => array_values($data_absensi_masuk_tepatwaktu),
            'data_absensi_masuk_cuti' => array_values($data_absensi_masuk_cuti),
            'data_absensi_masuk_tidak_hadir' => array_values($data_absensi_masuk_tidak_hadir),
            'data_absensi_masuk_telat' => array_values($data_absensi_masuk_telat)
        ];
        // dd($data_result);
        return response()->json($data_result);
    }
    public function get_grafik_absensi_nonshift(Request $request)
    {
        // dd($request->all());
        $holding = Holding::where('holding_code', $request->holding)->value('id');

        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();
        $period = CarbonPeriod::create($start_date, $end_date);
        $query = AttendanceLog::with(['Karyawan'])
            ->whereBetween(DB::raw('DATE(LogTime)'), [$start_date, $end_date])
            ->whereHas('User', function ($q) use ($holding, $request) {
                $q->where('kontrak_kerja', $holding);

                if (!empty($request->departemen_filter)) {
                    $q->whereIn('dept_id', $request->departemen_filter);
                }

                if (!empty($request->divisi_filter)) {
                    $q->whereIn('divisi_id', $request->divisi_filter);
                }

                if (!empty($request->bagian_filter)) {
                    $q->whereIn('bagian_id', $request->bagian_filter);
                }

                if (!empty($request->jabatan_filter)) {
                    $q->whereIn('jabatan_id', $request->jabatan_filter);
                }
            });

        $baseQuery = $query->get();
        $label_absensi = [];

        foreach ($period as $date) {
            $tanggal = $date->format('Y-m-d');
            $label_absensi[] = $tanggal;

            // default 0
            $data_absensi_masuk_tepatwaktu[$tanggal] = 0;
            $data_absensi_masuk_telat[$tanggal] = 0;
            $data_absensi_masuk_tidak_hadir[$tanggal] = 0;
            $data_absensi_masuk_cuti[$tanggal] = 0;
        }
        // dd($baseQuery);
        foreach ($baseQuery as $shiftData) {
            if ($shiftData->status_presensi == 'LIBUR' || !$shiftData->Shift) continue;
            $tanggal = Carbon::parse($shiftData->tanggal_masuk)->format('Y-m-d');
            $timeplus5 = Carbon::parse($shiftData->Shift->jam_masuk)->addMinutes(6);
            // dd($start_hours);
            $logs = AttendanceLog::where('EnrollNumber', $shiftData->user->nomor_identitas_karyawan)->whereDate('LogTime', $shiftData->tanggal_masuk)->first();
            // dd($logs);
            if ($logs != NULL) {
                $logtime = $logs->LogTime;
                $check_in = Carbon::parse($logtime);
                $check_in5 = Carbon::parse($shiftData->tanggal_masuk)->setTimeFrom($timeplus5);
                $check_inmin1hours = Carbon::parse($shiftData->tanggal_masuk)->setTimeFrom($shiftData->Shift->jam_masuk)->subHours(1);
                // dd([
                //     'logtime'          => $check_in->toDateTimeString(),
                //     'check_inmin1hours' => $check_inmin1hours->toDateTimeString(),
                //     'check_in5'        => $check_in5->toDateTimeString(),
                // ]);

                if ($check_in->toDateTimeString() > $check_inmin1hours->toDateTimeString() && $check_in->toDateTimeString() < $check_in5->toDateTimeString()) {
                    $data_absensi_masuk_tepatwaktu[$tanggal]++;
                } else {
                    $data_absensi_masuk_telat[$tanggal]++;
                }
            } else {
                if ($shiftData->status_presensi == 'TIDAK HADIR KERJA') {
                    if ($shiftData->keterangan_presensi == 'CUTI' && $shiftData->keterangan_cuti == 'TRUE') {
                        $data_absensi_masuk_cuti[$tanggal]++;
                    } else {
                        if ($shiftData->tanggal_masuk > date('Y-m-d')) {
                            continue;
                        }
                        $data_absensi_masuk_tidak_hadir[$tanggal]++;
                    }
                }
            }
        }

        $data_result = [
            'label_absensi' => $label_absensi,
            'data_absensi_masuk_tepatwaktu' => array_values($data_absensi_masuk_tepatwaktu),
            'data_absensi_masuk_cuti' => array_values($data_absensi_masuk_cuti),
            'data_absensi_masuk_tidak_hadir' => array_values($data_absensi_masuk_tidak_hadir),
            'data_absensi_masuk_telat' => array_values($data_absensi_masuk_telat)
        ];
        // dd($data_result);
        return response()->json($data_result);
    }
}
