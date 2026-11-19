<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\CutiDetail;
use App\Models\IzinLibur;
use App\Models\IzinSakitDetail;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\Holding;
use App\Models\IzinTidakMasukDetail;
use App\Models\LemburDetail;
use App\Models\PerdinDetail;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\PresensiBawahanRelation;
use App\Models\Shift;
use App\Models\StrukturModel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiBawahanController extends Controller
{
    public function index($menu_id, $routePrefix = 'absensi_bawahan')
    {
        // dd($menu_id);
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
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $holdings = Holding::whereIn('id', KaryawanJabatan::where('karyawan_id', $user_karyawan->id)
            ->where('is_active', 1)
            ->pluck('holding_id'))
            ->orderBy('holding_name')
            ->get();

        return view('users.absensi_bawahan.index', compact('all_menu_main', 'all_menus', 'all_menu_bar', 'user_karyawan', 'menu_id', 'routePrefix', 'holdings'));
    }

    public function indexLaporan($menu_id)
    {
        return $this->index($menu_id, 'laporan_presensi_bawahan');
    }
    public function get_dataAbsensiBawahan($id, $holdingId = null)
    {
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        $karyawan_jabatan = KaryawanJabatan::where('karyawan_id', $user_karyawan->id)
            ->where('is_active', 1)
            ->when($holdingId, fn($query) => $query->where('holding_id', $holdingId))
            ->get();
        $holdingIds = $karyawan_jabatan->pluck('holding_id')->toArray();

        if (!$holdingId) {
            return response()->json(['code' => 200, 'message' => 'Silakan pilih holding terlebih dahulu.', 'data' => []]);
        }

        if (!$karyawan_jabatan->contains('holding_id', $holdingId)) {
            return response()->json(['code' => 403, 'message' => 'Holding tidak terdaftar untuk user ini.', 'data' => []], 403);
        }

        $bawahanKaryawanIds = $this->savedDownlineIds($holdingIds, $user_karyawan->id);

        if (!$this->hasSavedRelation($holdingIds, $user_karyawan->id)) {
            $jabatanIds = $karyawan_jabatan->pluck('jabatan_id')->toArray();
            $struktur = StrukturModel::with('UserKaryawan', 'ChildrenRecursive')
                ->whereIn('jabatan', $jabatanIds)
                ->where('holding', $holdingId)
                ->get();
            $bawahanKaryawanIds = $struktur->isNotEmpty() ? $this->collectStructureEmployeeIds($struktur) : collect();
        }

        if ($bawahanKaryawanIds->isEmpty()) {
            return response()->json([
                'code' => 200,
                'message' => 'Belum ada relasi presensi bawahan yang disusun admin.',
                'data' => [],
            ]);
        }
        $user_shift = Karyawan::with(['KaryawanJabatanOne' => function ($q) {
            $q->with('Divisi');
            $q->with('Jabatan');
            $q->with('Bagian');
            $q->with('Departemen');
            $q->where('main', 1);
            $q->where('is_active', 1);
        }])
            ->with('KuotaCuti')
            ->whereIn('id', $bawahanKaryawanIds)
            ->where('status_aktif', 'AKTIF')
            // ->where('shift', 'SHIFT')
            ->where('nomor_identitas_karyawan', '!=', $user_karyawan->nomor_identitas_karyawan)
            ->orderBy('name', 'asc')
            ->get();

        $data = [
            'code' => 200,
            'message' => 'Success',
            'data' => $user_shift,
        ];
        return response()->json($data);
    }

    private function savedDownlineIds(array $holdingIds, string $atasanId)
    {
        $relations = PresensiBawahanRelation::whereIn('holding_id', $holdingIds)
            ->get()
            ->groupBy('atasan_id');
        $visited = collect();
        $downlineIds = collect();
        $queue = collect([(string) $atasanId]);

        while ($queue->isNotEmpty()) {
            $currentId = $queue->shift();
            if ($visited->contains($currentId)) {
                continue;
            }
            $visited->push($currentId);

            foreach ($relations->get($currentId, collect()) as $relation) {
                $bawahanId = (string) $relation->bawahan_id;
                if (!$downlineIds->contains($bawahanId)) {
                    $downlineIds->push($bawahanId);
                    $queue->push($bawahanId);
                }
            }
        }

        return $downlineIds;
    }

    private function hasSavedRelation(array $holdingIds, string $atasanId): bool
    {
        return PresensiBawahanRelation::whereIn('holding_id', $holdingIds)
            ->where('atasan_id', $atasanId)
            ->exists();
    }

    private function collectStructureEmployeeIds($struktur)
    {
        if ($struktur instanceof StrukturModel) {
            $struktur = collect([$struktur]);
        }

        $ids = collect();
        foreach ($struktur as $node) {
            $ids = $ids->merge($node->UserKaryawan->pluck('karyawan_id'));
            if ($node->ChildrenRecursive->isNotEmpty()) {
                $ids = $ids->merge($this->collectStructureEmployeeIds($node->ChildrenRecursive));
            }
        }

        return $ids->unique()->values();
    }
    public function detail_absensi_bawahan($id, $menu_id, $routePrefix = 'absensi_bawahan')
    {
        // dd($menu_id);
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
        $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
        return view('users.absensi_bawahan.detail', compact('all_menu_main', 'all_menus', 'all_menu_bar', 'user_karyawan', 'menu_id', 'id', 'routePrefix'));
    }

    public function detailLaporan($id, $menu_id)
    {
        return $this->detail_absensi_bawahan($id, $menu_id, 'laporan_presensi_bawahan');
    }
    public function get_detail_karyawan_bawahan($id)
    {
        $karyawan_bawahan = Karyawan::with(['KaryawanJabatanOne' => function ($q) {
            $q->with('Divisi');
            $q->with('Jabatan');
            $q->with('Bagian');
            $q->with('Departemen');
            $q->where('main', 1);
            $q->where('is_active', 1);
        }])->where('id', $id)->first();
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $karyawan_bawahan
        ]);
    }
    public function get_detail_absensi_bawahan($id, Request $request)
    {
        // dd($request->all(), $id);
        try {
            $non_shift = Shift::where('nama_shift', 'NON SHIFT')->first();
            $start = Carbon::parse($request->start_date)->format('Y-m-d');
            $end   = Carbon::parse($request->end_date)->format('Y-m-d');
            $end_absensi = Carbon::parse($end)->addDay()->format('Y-m-d');
            $user_karyawan = Karyawan::with(['Absensi' => function ($query) use ($start, $end_absensi) {
                $query->with('MachineFinger');
                $query->whereBetween('LogTime', [$start . ' 00:00:00', $end_absensi . ' 23:59:59']);
                $query->orderBy('LogTime', 'ASC');
            }])
                ->with(['MappingShift' => function ($q) {
                    $q->whereNotNull('is_active');
                }])
                ->where('id', $id)->first();
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
                            ->whereDate('tgl_lembur', $date->toDateString())
                            ->where('status', 1)
                            ->first();
                        if ($lembur_detail) {
                            $start_pulang = Carbon::parse($lembur_detail->tgl_lembur . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end_pulang = Carbon::parse($lembur_detail->tgl_pulang . ' ' . $lembur_detail->jam_selesai)->addHours(2)->format('Y-m-d H:i:59');
                        } else {
                            $start_pulang = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                            $end_pulang = Carbon::parse($date->format('Y-m-d') . ' ' . $non_shift->jam_keluar)->endOfDay()
                                ->format('Y-m-d H:i:59');
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
                            if ($logs_absensi_masuk_any->isNotEmpty()) {
                                $checkIn = Carbon::parse($logs_absensi_masuk_any->min('LogTime'))->format('H:i');
                                $status = 'DATANG LEBIH AWAL';
                            } else {
                                if ($non_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
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
                            $lembur_detail = LemburDetail::where('id_karyawan', $user_karyawan->id)
                                ->whereDate('tgl_lembur', $date->toDateString())
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
                                if ($kategori_shift->tgl_pulang_besok == 1) {
                                    $start_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_pulang_cepat)->format('Y-m-d H:i:59');
                                    $end_pulang = Carbon::parse($jam_masuk->tanggal_pulang . ' ' . $kategori_shift->jam_keluar)->addHours(5)->format('Y-m-d H:i:59');
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
                                    } else {
                                        if ($sakit_detail) {
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
                                                    if ($kategori_shift->hari_libur == Carbon::parse($date->toDateString())->dayOfWeek) {
                                                        $checkIn = 'LIBUR';
                                                        $status = 'LIBUR';
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
}
