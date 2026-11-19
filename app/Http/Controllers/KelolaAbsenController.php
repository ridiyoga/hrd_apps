<?php

namespace App\Http\Controllers;

use App\Mail\SemuaIzin;
use App\Models\ActivityLog;
use App\Models\AttendanceLog;
use App\Models\Cuti;
use App\Models\CutiDetail;
use App\Models\FingerMachine;
use App\Models\Karyawan;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Menu;
use App\Models\Holding;
use App\Models\Inventaris;
use App\Models\Karyawankecuali;
use App\Models\KuotaCutiKaryawan;
use App\Models\MappingShift;
use App\Models\Perdin;
use App\Models\PerdinDetail;
use App\Models\PerdinKendaraan;
use App\Models\PerdinKendaraanKantor;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class KelolaAbsenController extends Controller
{
    public function index($holding, Request $request)
    {
        session(['url.intended' => url()->previous()]);
        if (!Auth::check()) {
            return redirect('/');
        }
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
        return view('admin.karyawan.absen.index', [
            'holding' => $getHolding,
            'menus' => $menus,
        ]);
    }
    public function kelola_absen_dt(Request $request, $holding)
    {
        if (request()->ajax()) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $period = CarbonPeriod::create($startDate, $endDate);
            // ubah jadi collection array
            $kecuali = KaryawanKecuali::pluck('id_karyawan');
            $tbl = Karyawan::with('Cuti.KategoriCuti')
                ->where('shift', 'NON SHIFT')
                ->whereNotIn('id', $kecuali)
                ->where('status_aktif', 'AKTIF')
                // ->where('nomor_identitas_karyawan', '1002012020994')
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
                }])->with('KontrakKerja')
                // ->limit(5)
                ->get();
            // dd($table);
            if ($tbl == NULL) {
                return DataTables::of(null)
                    ->make(false);
            } else {
                $absensi_nonshift = AttendanceLog::whereBetween('LogTime', [$startDate, $endDate])->get();
            }
            $absensiByEmployee = $absensi_nonshift->groupBy('EnrollNumber');
            $fingerMachines = FingerMachine::whereIn(
                'Ip',
                $absensi_nonshift->pluck('MachineIp')->filter()->unique()->all()
            )->get()->keyBy('Ip');
            $get_non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $data = collect();
            foreach ($tbl as $table) {
                $AbsensiKaryawan = $absensiByEmployee->get($table->nomor_identitas_karyawan, collect());
                $AbsensiByDate = $AbsensiKaryawan->groupBy(function ($log) {
                    return Carbon::parse($log->LogTime)->toDateString();
                });
                $DataKaryawan = collect($period)->map(function ($date, $index) use ($table, $AbsensiByDate, $get_non_shift, $fingerMachines) {
                    // pastikan $date benar-benar Carbon
                    $carbonDate = $date instanceof \Carbon\Carbon ? $date : Carbon::parse($date)->format('Y-m-d');
                    // Karyawan Shift
                    $absensi_shift = $table->MappingShift->first(function ($mappingShift) use ($carbonDate) {
                        return Carbon::parse($mappingShift->tanggal_masuk)->isSameDay($carbonDate);
                    });
                    $AbsenHarian = $AbsensiByDate->get($carbonDate->toDateString(), collect());
                    // dd($AbsensiByDate, $carbonDate->toDateString(), $AbsenHarian);

                    if ($table->shift == 'NON SHIFT') {
                        // dd($AbsenHarian);
                        $logs_absensi_masuk = $AbsenHarian->filter(function ($log) use ($get_non_shift, $date) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_min_masuk)->format('Y-m-d H:i:59');
                            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_terlambat)->addHours(3)->addMinutes(1)->format('Y-m-d H:i:59');
                            return $getcheckIn->between($start, $end);
                        });
                        // dd($logs_absensi_masuk);
                        $logs_absensi_pulang = $AbsenHarian->filter(function ($log) use ($date, $get_non_shift) {
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
                            $logs_absensi_masuk = $AbsenHarian->filter(function ($log) use ($absensi_shift, $date) {
                                $getcheckIn = Carbon::parse($log->LogTime);
                                $start = Carbon::parse($date->format('Y-m-d') . ' ' . $absensi_shift->Shift->jam_min_masuk)->format('Y-m-d H:i:59');
                                $end = Carbon::parse($date->format('Y-m-d') . ' ' . $absensi_shift->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                                return $getcheckIn->between($start, $end);
                            });
                            $logs_absensi_pulang = $AbsenHarian->filter(function ($log) use ($date, $absensi_shift) {
                                $getcheckOut = Carbon::parse($log->LogTime);
                                $start = Carbon::parse($absensi_shift->tanggal_pulang . ' ' . $absensi_shift->Shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                $end = Carbon::parse($absensi_shift->tanggal_pulang . ' ' . $absensi_shift->Shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
                                return $getcheckOut->between($start, $end);
                            });
                        }
                    }
                    if ($logs_absensi_masuk?->isNotEmpty()) {
                        // dd('ok');
                        $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i');
                        $lokasi_absen_masuk = $logs_absensi_masuk->min('MachineIp');
                        if ($table->shift == 'NON SHIFT') {

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
                            $jamTerlambat = $absensi_shift?->Shift?->jam_terlambat;
                            $isTerlambat = $jamTerlambat !== null
                                && Carbon::parse($checkIn)->greaterThan(Carbon::parse($jamTerlambat));
                            $selisih_menit = $isTerlambat
                                ? Carbon::parse($checkIn)->diffInMinutes(Carbon::parse($jamTerlambat))
                                : 0;

                            if ($isTerlambat) {
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
                        $logs_absensi_masuk = $AbsenHarian->filter(function ($log) use ($date) {
                            $getcheckIn = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d'));
                            return $getcheckIn->toDateString() === $start->toDateString();
                        });
                        // dd($logs_absensi_masuk);
                        if ($logs_absensi_masuk->isNotEmpty()) {
                            $lokasi_absen_masuk = $logs_absensi_masuk->min('MachineIp');
                            $checkIn  = Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('YmdHis');
                            if ($table->shift == 'NON SHIFT') {
                                $getTelat = Carbon::parse($date->format('Y-m-d') . ' ' . $get_non_shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('YmdHis');
                                if ($checkIn > $getTelat) {
                                    $checkIn = 'TIDAK PRESENSI';
                                } else {
                                    $checkIn = $checkIn;
                                }
                            } else {
                                $getTelat = Carbon::parse($date->format('Y-m-d') . ' ' . $absensi_shift->Shift->jam_terlambat)->addHours(3)->addMinutes(10)->format('Y-m-d H:i:59');
                                if ($checkIn > $getTelat) {
                                    $checkIn = 'TIDAK PRESENSI';
                                } else {
                                    $checkIn = $checkIn;
                                }
                            }
                            $telat = '0';
                            $keterangan_presensi_masuk = '';
                        } else {
                            $lokasi_absen_masuk = NULL;
                            $telat = '0';
                            $keterangan_presensi_masuk = '';
                            // dd('ok');
                            if ($date->format('Y-m-d') >= Carbon::now()->format('Y-m-d')) {
                                $checkIn = '';
                            } else {
                                // dd('ok');
                                $cuti_detail = $table->cutiDetail
                                    ->filter(function ($item) use ($date) {
                                        return $item->status == 1
                                            && \Carbon\Carbon::parse($item->tgl_cuti)->isSameDay($date);
                                    })
                                    ->isNotEmpty();
                                $sakit_detail = $table->IzinSakitDetail
                                    ->filter(function ($item) use ($date) {
                                        return $item->status == 1
                                            && \Carbon\Carbon::parse($item->tgl_sakit)->isSameDay($date);
                                    })
                                    ->isNotEmpty();
                                $tidak_masuk_detail = $table->IzinTidakMasukDetail
                                    ->filter(function ($item) use ($date) {
                                        return $item->status == 1
                                            && \Carbon\Carbon::parse($item->tgl_tidak_masuk)->isSameDay($date);
                                    })
                                    ->isNotEmpty();
                                $perdin_detail = $table->PerdinDetail
                                    ->filter(function ($item) use ($date) {
                                        return $item->status == 1
                                            && \Carbon\Carbon::parse($item->tgl_perdin)->isSameDay($date);
                                    })
                                    ->isNotEmpty();
                                $tukar_libur = $table->IzinLibur
                                    ->filter(function ($item) use ($date) {
                                        return $item->status_libur == 3
                                            && \Carbon\Carbon::parse($item->tanggal_tukar)->isSameDay($date);
                                    })
                                    ->isNotEmpty();
                                // dd('ok');
                                if ($sakit_detail) {
                                    $checkIn = 'SAKIT';
                                } elseif ($cuti_detail) {
                                    $checkIn = 'CUTI';
                                } elseif ($tidak_masuk_detail) {
                                    $checkIn = 'TIDAK MASUK';
                                } elseif ($perdin_detail) {
                                    $checkIn = 'PERDIN';
                                } else {
                                    if ($table->shift == 'NON SHIFT') {
                                        // dd('ok');

                                        if ($carbonDate->dayOfWeek == $get_non_shift->hari_libur) {

                                            $checkIn = '<span class="badge bg-label-info">LIBUR</span>';
                                            // dd($checkIn);
                                        } elseif ($tukar_libur) {
                                            $checkIn = '<span class="badge bg-label-info">TUKAR LIBUR</span>';
                                        } else {
                                            $checkIn = 'TIDAK PRESENSI';
                                        }
                                    } else {
                                        // dd($absensi_shift->nama_shift);
                                        if (empty($absensi_shift)) {

                                            $checkIn = 'TIDAK PRESENSI';
                                        } else {
                                            if ($absensi_shift->nama_shift == 'LIBUR') {
                                                // dd('ok');
                                                $checkIn = '<span class="badge bg-label-info">LIBUR</span>';
                                            } else {
                                                $checkIn = 'TIDAK PRESENSI';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // dd($checkIn);
                    // return $logs_absensi_pulang;
                    if ($logs_absensi_pulang?->isNotEmpty()) {
                        $checkOut  = Carbon::parse($logs_absensi_pulang->min('LogTime'))->format('H:i');
                        $lokasi_absen_pulang = $logs_absensi_pulang->min('MachineIp');
                        if ($table->shift == 'NON SHIFT') {
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
                        $logs_absensi_pulang = $AbsenHarian->filter(function ($log) use ($date) {
                            $getcheckOut = Carbon::parse($log->LogTime);
                            $start = Carbon::parse($date->format('Y-m-d'));
                            return $getcheckOut->toDateString() === $start->toDateString();
                        });
                        if ($logs_absensi_pulang->isNotEmpty()) {
                            $keterangan_presensi_pulang = '';
                            $lokasi_absen_pulang = $logs_absensi_pulang->min('MachineIp');
                            $checkOut = Carbon::parse($logs_absensi_pulang->max('LogTime'))->format('H:i');
                            if ($checkOut == Carbon::parse($logs_absensi_masuk->min('LogTime'))->format('H:i')) {
                                $checkOut = 'TIDAK PRESENSI';
                            }
                        } else {
                            $keterangan_presensi_pulang = '';
                            $lokasi_absen_pulang = '<span>(Tidak&nbsp;Absen)</span>';
                            if ($date->format('Y-m-d') >= Carbon::now()->format('Y-m-d')) {
                                $checkOut = '';
                            } else {
                                $checkOut = 'TIDAK PRESENSI';
                            }
                        }
                    }
                    $check_all = $checkIn . ' - ' . $checkOut;
                    // dd('ok');
                    // dd($check_all);

                    if ($checkIn == 'TIDAK PRESENSI' && $checkOut == 'TIDAK PRESENSI') {
                        $check_all = '<span style="color:red;">' . 'TIDAK HADIR' . '</span>';
                    }

                    $get_machine_masuk = $fingerMachines->get($lokasi_absen_masuk);
                    $get_machine_pulang = $fingerMachines->get($lokasi_absen_pulang);
                    if ($get_machine_masuk == NULL) {
                        $lokasi_masuk = '<span class="badge bg-label-danger">-</span>';
                    } else {
                        $lokasi_masuk = $get_machine_masuk->Name;
                    }
                    if ($get_machine_pulang == NULL) {
                        $lokasi_pulang = '<span class="badge bg-label-danger">-</span>';
                    } else {
                        if ($checkOut == 'TIDAK PRESENSI') {
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
                        '_karyawan' => $table,
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
                        'tukar_libur' => $table->IzinLibur ?? '',
                    ];
                });
                $data = $data->merge($DataKaryawan);
            }
            // dd($data);
            $data = $data->filter(function ($row) {
                return $row['check_all'] == '<span style="color:red;">TIDAK HADIR</span>';
            });
            return DataTables::of($data)

                ->addColumn('nomor_identitas_karyawan', function ($row) use ($table) {
                    return $row['nomor_identitas_karyawan'];
                })
                ->addColumn('nama_karyawan', function ($row) use ($table) {
                    return $row['nama_karyawan'];
                })
                ->addColumn('holding', function ($row) use ($table) {
                    return $row['_karyawan']->KontrakKerja->holding_name;
                })->addColumn('tanggal_masuk', function ($row) {

                    return $row['hari'] . ', ' . $row['tanggal'];
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
                ->addColumn('jam_absen_pulang', function ($row) {
                    $table = $row['_karyawan'];
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
                ->addColumn('aksi', function ($row) {
                    $table = $row['_karyawan'];
                    $kuotaCuti = $table->KuotaCuti;
                    $idKuotaCuti = $kuotaCuti->id ?? '';
                    $sisaCuti = $kuotaCuti->sisa ?? '-';
                    if ($sisaCuti == '-') {
                        return '<span class="badge bg-label-primary">' . 'BELUM ADA KUOTA' . '</span>';
                    } else {
                        if ($sisaCuti == 0) {
                            return '<span class="badge bg-label-secondary">' . 'KUOTA CUTI HABIS' . '</span>';
                        } else {
                            $btn = '<button id="btn_cuti"
                                data-id="' . $table->id . '"
                                data-nama_karyawan="' . $table->name . '"
                                data-id_kuota_cuti="' . $idKuotaCuti . '"
                                data-sisa="' . $sisaCuti . '"
                                data-tanggal="' . Carbon::parse($row['tanggal'])->format('Y-m-d') . '"
                                type="button" class="btn btn-icon btn-warning waves-effect waves-light m-1"><span class="tf-icons mdi mdi-beach"></span>
                            </button>';
                            return $btn;
                        }
                    }
                })
                ->addColumn('keterangan_presensi', function ($row) use ($get_non_shift) {

                    return '<span class="badge bg-label-danger">TIDAK HADIR</span>';
                })
                ->rawColumns([
                    'nomor_identitas_karyawan',
                    'nama_karyawan',
                    'holding',
                    'nama_shift',
                    'tanggal_masuk',
                    'jam_kerja',
                    'keterangan_presensi',
                    'aksi',
                    // 'keterangan_izin',
                    // 'keterangan_cuti',
                    // 'keterangan_dinas'
                ])
                ->removeColumn('_karyawan')
                ->make(true);
        }
    }
    public function potong_cuti(Request $request)
    {
        // dd($request->all());
        $rules = [
            'id_karyawan'    => 'required',
            'id_kuota_cuti'  => 'required',
            'tanggal'  => 'required',
            'nama_karyawan'  => 'required',
            'sisa_cuti'      => 'required|numeric|min:0',
            'pengurangan'    => 'required|in:1,2',
            'terpotong'      => 'required|numeric|min:0',
        ];

        $messages = [
            'id_karyawan.required'   => 'Karyawan wajib dipilih',
            'id_kuota_cuti.required' => 'Kuota cuti tidak ditemukan',
            'tanggal.required'       => 'Tanggal tidak ditemukan',
            'nama_karyawan.required' => 'Nama karyawan wajib diisi',

            'sisa_cuti.required'     => 'Sisa cuti wajib diisi',
            'sisa_cuti.numeric'      => 'Sisa cuti harus berupa angka',
            'sisa_cuti.min'          => 'Sisa cuti tidak boleh kurang dari 0',

            'pengurangan.required'  => 'Pemotongan cuti wajib dipilih',
            'pengurangan.in'        => 'Pilihan pemotongan cuti tidak valid',

            'terpotong.required'    => 'Sisa cuti setelah pemotongan wajib diisi',
            'terpotong.numeric'     => 'Sisa cuti setelah pemotongan harus berupa angka',
            'terpotong.min'         => 'Sisa cuti setelah pemotongan tidak boleh negatif',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ]);
        }
        DB::beginTransaction();
        try {
            $hrd = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $karyawan = Karyawan::where('id', $request->id_karyawan)->first();

            $kuota = KuotaCutiKaryawan::where('status', 1)
                ->where('karyawan_id', $request->id_karyawan)
                ->first();
            $terpakai = $kuota->terpakai + $request->pengurangan;
            $kuota->update([
                'terpakai' => $terpakai,
                'sisa' => $request->terpotong,
            ]);
            $count_cuti = Cuti::whereIn('status_cuti', [5, 2])->whereDate('tanggal_pengajuan', $request->tanggal)->get()->count();
            if ($count_cuti == 0) {
                $last_count = 1;
            } else {
                $last_count = $count_cuti + 1;
            }
            if (strlen($last_count) == 1) {
                $last_count = '000' . $last_count;
            } else if (strlen($last_count) == 2) {
                $last_count = '00' . $last_count;
            } else {
                $last_count = '0' . $last_count;
            }

            $tgl_pengajuan = Carbon::parse($request->tanggal)->format('Ymd');
            $no_form = $karyawan->KontrakKerja->holding_category . '/FCT' . '/' . $tgl_pengajuan . '/' . $last_count;
            $kode = Uuid::uuid4();
            Cuti::create([
                'id' => $kode,
                'karyawan_id' => $request->id_karyawan,
                'no_form_cuti' => $no_form,
                'nama_karyawan' => $request->nama_karyawan,
                'nama_cuti' => 'Cuti dipotong HRD',
                'status_cuti' => 2,
                'tanggal_pengajuan' => $request->tanggal,
                'tanggal_mulai' => $request->tanggal,
                'tanggal_selesai' => $request->tanggal,
                'keterangan_cuti' => 'Cuti dipotong HRD karena absen tanpa keterangan yang jelas',
                'created_at' => now(),
                'kuota' => $kuota->kuota,
                'id_kuota_karyawan' => $kuota->id,
                'jumlah_hari' => $request->pengurangan,
                'terpakai' => $terpakai,
                'sisa' => $request->terpotong,
                'updated_by' => $hrd->id,
                'updated_by_id' => $hrd->name,
                'toleransi' => 2,
            ]);
            CutiDetail::create([
                'id' => Uuid::uuid4(),
                'id_cuti' => $kode,
                'id_karyawan' => $request->id_karyawan,
                'nama_karyawan' => $request->nama_karyawan,
                'tgl_cuti' => $request->tanggal,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'tipe' => 3,
            ]);


            $holding = Holding::where('id', $karyawan->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_kendaraan,
                'holding_id' => $holding->id,
                'holding_category' => $holding->holding_category,
                'table' => 'cuti_kuota_karyawan, cutis',
                'activity' => 'update',
                'description' => 'Mengurangi cuti karyawan dua kali lipat' . $request->nama_karyawan,
                'keterangan' => 'Success',
            ]);
            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Pengajuan izin berhasil Disetujui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
