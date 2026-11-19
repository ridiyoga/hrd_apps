<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MappingShift;
use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Lembur;
use App\Models\ResetCuti;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\RoleUsers;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;

class dashboardController extends Controller
{
    public function dashboard_switch($holding)
    {
        $menu_user =  User::where('id', Auth::user()->id)->first();
        $menu_active = Menu::where('id', $menu_user->menu_active)->first();
        // dd($menu_active);
        if ($menu_user->menu_active == null) {
            return redirect()->to('/dashboard/option/' . $holding);
        } else {
            return redirect()->to($menu_active->url . '/' . $holding);
        }
    }
    public function dashboard_option($holding)
    {
        // dd(Auth::user()->timezone);
        $getHolding = Holding::where('holding_code', $holding)->first();
        //dd($holding, $getHolding);
        $getHoldingAll = Holding::all();
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

        // dd($menus);
        return view('admin.dashboard.dashboard_option', [
            'title' => 'Dashboard',
            'holding' => $getHolding,
            'holdingAll' => $getHoldingAll,
            'menus' => $menus,
            'atas' => $atas,
            'tengah' => $tengah,
            'habis' => $habis,
        ]);
    }
    public function index($holding)
    {
        // dd('ok', Auth::user());
        // dd(Auth::user());
        // dd($holding);
        // Konteks holding dan tanggal yang digunakan oleh seluruh data dashboard.
        $getHolding = Holding::where('holding_code', $holding)->first();
        $getHoldingAll = Holding::all();
        date_default_timezone_set('Asia/Jakarta');
        $tgl_skrg = date("Y-m-d");

        // Data pendukung dashboard: log aktivitas terbaru dan karyawan yang kontraknya hampir habis.
        $logs = ActivityLog::query();
        $date_30day = Carbon::now()->addDay(30);
        $date_now = Carbon::now()->subDay(-30);
        $date_now1 = Carbon::now();
        // dd($date_30day);
        $logs = $logs->orderBy('created_at', 'desc')->limit(5)->get();
        $count_karyawan_habis_kontrak = Karyawan::with('Divisi')
            ->with('Jabatan')
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->where('kontrak_kerja', $getHolding->holding_category)
            ->whereBetween('tgl_selesai_kontrak', [$date_now, $date_30day])
            ->count();
        $karyawan_habis_kontrak = Karyawan::with('Divisi')
            ->with('Jabatan')
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->where('kontrak_kerja', $getHolding->holding_category)
            ->whereBetween('tgl_selesai_kontrak', [$date_now, $date_30day])
            ->orderBy('tgl_selesai_kontrak', 'asc')
            ->take(6)
            ->get();

        // dd($count_karyawan_habis_kontrak);
        // Menu sidebar diambil berdasarkan role user yang sedang login.
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

        // dd(json_encode($nama_jabatan));
        // Data awal grafik absensi bulan berjalan saat halaman pertama kali dirender.
        $start_date = Carbon::now()->startOfMonth();
        $end_date = Carbon::now()->endOfMonth();
        $period = CarbonPeriod::create($start_date, $end_date);

        foreach ($period as $date) {
            $label_absensi[] = $date->format('d/m/Y');
            $data_absensi_masuk[] = MappingShift::Join('karyawans', 'karyawans.id', 'mapping_shifts.karyawan_id')
                ->where('mapping_shifts.tanggal_masuk', $date->format('Y-m-d'))
                ->whereIn('mapping_shifts.keterangan_absensi', ['TELAT HADIR', 'TEPAT WAKTU'])
                ->where('mapping_shifts.status_absen', 'HADIR KERJA')
                ->where('karyawans.kontrak_kerja', $getHolding->holding_category)->count();

            $data_absensi_pulang[] = MappingShift::Join('karyawans', 'karyawans.id', 'mapping_shifts.karyawan_id')
                ->where('mapping_shifts.tanggal_masuk', $date->format('Y-m-d'))
                ->whereIn('mapping_shifts.keterangan_absensi_pulang', ['PULANG CEPAT', 'TEPAT WAKTU'])
                ->where('mapping_shifts.status_absen', 'HADIR KERJA')
                ->where('karyawans.kontrak_kerja', $getHolding->holding_category)->count();
        }
        // dd(json_encode($label_absensi));
        // Ringkasan kartu absensi hari ini untuk holding aktif.
        $absensi_hari_ini = $this->getAbsensiHariIniSummary($getHolding, $tgl_skrg);

        return view('admin.dashboard.index', [
            'count_karyawan_habis_kontrak' => $count_karyawan_habis_kontrak,
            'karyawan_habis_kontrak' => $karyawan_habis_kontrak,
            'date_now1' => $date_now1,
            'menus' => $menus,
            'title' => 'Dashboard',
            'label_absensi' => json_encode($label_absensi),
            'data_absensi_masuk' => json_encode($data_absensi_masuk),
            'data_absensi_pulang' => json_encode($data_absensi_pulang),
            "karyawan_laki" => Karyawan::where('gender', 'Laki-Laki')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            "karyawan_perempuan" => Karyawan::where('gender', 'Perempuan')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            "karyawan_office" => Karyawan::where('kategori', 'Karyawan Bulanan')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            "karyawan_shift" => Karyawan::where('kategori', 'Karyawan Harian')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            'jumlah_masuk' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'HADIR KERJA')->count(),
            'jumlah_tidak_masuk' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'TIDAK HADIR KERJA')->count(),
            'jumlah_libur' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Libur')->count(),
            'jumlah_cuti' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Cuti')->count(),
            'jumlah_izin_telat' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Izin Telat')->count(),
            'jumlah_izin_pulang_cepat' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Izin Pulang Cepat')->count(),
            'jumlah_karyawan_lembur' => Lembur::where('tanggal', $tgl_skrg)->count(),
            'logs' => $logs,
            'holding' => $getHolding,
            'holdingAll' => $getHoldingAll,
            'absensi_hari_ini' => $absensi_hari_ini,
        ]);
    }

    /**
     * Menghitung KPI absensi harian untuk satu holding.
     */
    private function getAbsensiHariIniSummary(Holding $holding, string $tanggal): array
    {
        // Query dasar absensi hari ini yang selalu difilter per holding.
        $baseQuery = function () use ($holding, $tanggal) {
            return MappingShift::join('karyawans', 'karyawans.id', '=', 'mapping_shifts.karyawan_id')
                ->where('mapping_shifts.tanggal_masuk', $tanggal)
                ->where('karyawans.kontrak_kerja', $holding->id);
        };

        // Jadwal kerja tidak menghitung baris libur agar persentase hadir lebih akurat.
        $jadwalKerjaQuery = function () use ($baseQuery) {
            return $baseQuery()
                ->where(function ($query) {
                    $query->whereNull('mapping_shifts.status_absen')
                        ->orWhereNotIn('mapping_shifts.status_absen', ['LIBUR', 'Libur']);
                });
        };

        // Karyawan yang sudah tercatat hadir pada tanggal dashboard.
        $hadir = $baseQuery()
            ->where('mapping_shifts.status_absen', 'HADIR KERJA')
            ->count();

        // Karyawan hadir dengan keterangan telat.
        $terlambat = $baseQuery()
            ->where('mapping_shifts.status_absen', 'HADIR KERJA')
            ->where('mapping_shifts.keterangan_absensi', 'TELAT HADIR')
            ->count();

        // Karyawan tidak hadir karena cuti.
        $cuti = $baseQuery()
            ->where('mapping_shifts.status_absen', 'TIDAK HADIR KERJA')
            ->where(function ($query) {
                $query->where('mapping_shifts.keterangan_cuti', 'TRUE')
                    ->orWhere('mapping_shifts.keterangan_absensi', 'CUTI')
                    ->orWhereNotNull('mapping_shifts.cuti_id')
                    ->orWhere('mapping_shifts.kelengkapan_absensi', 'like', 'CUTI%');
            })
            ->count();

        // Karyawan tidak hadir karena izin atau sakit.
        $izinSakit = $baseQuery()
            ->where('mapping_shifts.status_absen', 'TIDAK HADIR KERJA')
            ->where(function ($query) {
                $query->where('mapping_shifts.keterangan_izin', 'TRUE')
                    ->orWhereIn('mapping_shifts.keterangan_absensi', ['IZIN SAKIT', 'IZIN TIDAK MASUK'])
                    ->orWhere('mapping_shifts.kelengkapan_absensi', 'like', '%IZIN%');
            })
            ->count();

        // Karyawan yang belum absen dan tidak termasuk cuti/izin/sakit.
        $tidakAbsen = $baseQuery()
            ->where(function ($query) {
                $query->whereNull('mapping_shifts.status_absen')
                    ->orWhere(function ($query) {
                        $query->where('mapping_shifts.status_absen', 'TIDAK HADIR KERJA')
                            ->where(function ($query) {
                                $query->whereNull('mapping_shifts.keterangan_absensi')
                                    ->orWhereNotIn('mapping_shifts.keterangan_absensi', [
                                        'CUTI',
                                        'IZIN SAKIT',
                                        'IZIN TIDAK MASUK',
                                        'PENUGASAN',
                                    ]);
                            })
                            ->where(function ($query) {
                                $query->whereNull('mapping_shifts.keterangan_cuti')
                                    ->orWhere('mapping_shifts.keterangan_cuti', '!=', 'TRUE');
                            })
                            ->where(function ($query) {
                                $query->whereNull('mapping_shifts.keterangan_izin')
                                    ->orWhere('mapping_shifts.keterangan_izin', '!=', 'TRUE');
                            });
                    });
            })
            ->count();

        $totalJadwalKerja = $jadwalKerjaQuery()->count();

        // Format data siap pakai untuk view dashboard.
        return [
            'tanggal' => Carbon::parse($tanggal)->format('d/m/Y'),
            'total_jadwal_kerja' => $totalJadwalKerja,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'tidak_absen' => $tidakAbsen,
            'cuti' => $cuti,
            'izin_sakit' => $izinSakit,
            'cuti_izin_sakit' => $cuti + $izinSakit,
            'persentase_hadir' => $totalJadwalKerja > 0 ? round(($hadir / $totalJadwalKerja) * 100, 1) : 0,
        ];
    }
    public function index_portal($holding)
    {
        // dd('ok');
        // dd(Auth::user());
        // dd($holding);
        $getHolding = Holding::where('holding_code', $holding)->first();
        $getHoldingAll = Holding::all();
        date_default_timezone_set('Asia/Jakarta');
        $tgl_skrg = date("Y-m-d");

        $logs = ActivityLog::query();
        $date_30day = Carbon::now()->addDay('30');
        $date_now = Carbon::now()->addDay('-30');
        $date_now1 = Carbon::now();
        // dd($date_30day);
        $logs = $logs->orderBy('created_at', 'desc')->limit(5)->get();
        $count_karyawan_habis_kontrak = Karyawan::with('Divisi')
            ->with('Jabatan')
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->where('kontrak_kerja', $getHolding->holding_category)
            ->whereBetween('tgl_selesai_kontrak', [$date_now, $date_30day])
            ->count();
        $karyawan_habis_kontrak = Karyawan::with('Divisi')
            ->with('Jabatan')
            ->where('status_aktif', 'AKTIF')
            ->where('kategori', 'Karyawan Bulanan')
            ->where('kontrak_kerja', $getHolding->holding_category)
            ->whereBetween('tgl_selesai_kontrak', [$date_now, $date_30day])
            ->orderBy('tgl_selesai_kontrak', 'asc')
            ->take(6)
            ->get();

        // dd($count_karyawan_habis_kontrak);
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

        // dd(json_encode($nama_jabatan));
        $start_date = Carbon::now()->startOfMonth();
        $end_date = Carbon::now()->endOfMonth();
        $period = CarbonPeriod::create($start_date, $end_date);

        foreach ($period as $date) {
            $label_absensi[] = $date->format('d/m/Y');
            $data_absensi_masuk[] = MappingShift::Join('karyawans', 'karyawans.id', 'mapping_shifts.karyawan_id')
                ->where('mapping_shifts.tanggal_masuk', $date->format('Y-m-d'))
                ->whereIn('mapping_shifts.keterangan_absensi', ['TELAT HADIR', 'TEPAT WAKTU'])
                ->where('mapping_shifts.status_absen', 'HADIR KERJA')
                ->where('karyawans.kontrak_kerja', $getHolding->holding_category)->count();

            $data_absensi_pulang[] = MappingShift::Join('karyawans', 'karyawans.id', 'mapping_shifts.karyawan_id')
                ->where('mapping_shifts.tanggal_masuk', $date->format('Y-m-d'))
                ->whereIn('mapping_shifts.keterangan_absensi_pulang', ['PULANG CEPAT', 'TEPAT WAKTU'])
                ->where('mapping_shifts.status_absen', 'HADIR KERJA')
                ->where('karyawans.kontrak_kerja', $getHolding->holding_category)->count();
        }
        // dd(json_encode($label_absensi));

        return view('admin.dashboard.index_portal', [
            'count_karyawan_habis_kontrak' => $count_karyawan_habis_kontrak,
            'karyawan_habis_kontrak' => $karyawan_habis_kontrak,
            'date_now1' => $date_now1,
            'title' => 'Dashboard',
            'menus' => $menus,
            'label_absensi' => json_encode($label_absensi),
            'data_absensi_masuk' => json_encode($data_absensi_masuk),
            'data_absensi_pulang' => json_encode($data_absensi_pulang),
            "karyawan_laki" => Karyawan::where('gender', 'Laki-Laki')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            "karyawan_perempuan" => Karyawan::where('gender', 'Perempuan')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            "karyawan_office" => Karyawan::where('kategori', 'Karyawan Bulanan')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            "karyawan_shift" => Karyawan::where('kategori', 'Karyawan Harian')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->holding_category)->count(),
            'jumlah_masuk' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'HADIR KERJA')->count(),
            'jumlah_tidak_masuk' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'TIDAK HADIR KERJA')->count(),
            'jumlah_libur' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Libur')->count(),
            'jumlah_cuti' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Cuti')->count(),
            'jumlah_izin_telat' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Izin Telat')->count(),
            'jumlah_izin_pulang_cepat' => MappingShift::where('tanggal_masuk', $tgl_skrg)->where('status_absen', 'Izin Pulang Cepat')->count(),
            'jumlah_karyawan_lembur' => Lembur::where('tanggal', $tgl_skrg)->count(),
            'logs' => $logs,
            'holding' => $getHolding,
            'holdingAll' => $getHoldingAll,
        ]);
    }

    /**
     * Endpoint AJAX untuk KPI dan grafik utama dashboard HRD.
     * Semua data karyawan difilter per holding yang sedang dibuka.
     */
    public function graph_Dashboard_All($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();

        // Grafik karyawan per departemen berdasarkan jabatan utama yang aktif.
        $count_karyawan_departemen = Karyawan::join('karyawan_jabatan as kj', 'kj.karyawan_id', '=', 'karyawans.id')
            ->join('departemens', 'departemens.id', '=', 'kj.departemen_id')
            ->where('karyawans.kontrak_kerja', $getHolding->id)
            ->where('karyawans.status_aktif', 'AKTIF')
            ->where('kj.main', 1)
            ->where('kj.is_active', 1)
            ->select(DB::raw("COUNT(DISTINCT karyawans.id) as jumlah"), 'departemens.nama_departemen')
            ->groupBy('departemens.nama_departemen')
            ->pluck('jumlah', 'nama_departemen');
        $nama_departemen = $count_karyawan_departemen->keys();
        $jumlah_karyawan_departemen = $count_karyawan_departemen->values();
        // dd($nama_departemen, $jumlah_karyawan_departemen);

        // Grafik persentase jabatan berdasarkan jabatan utama yang aktif.
        $count_karyawan_jabatan = Karyawan::join('karyawan_jabatan as kj', 'kj.karyawan_id', '=', 'karyawans.id')
            ->join('jabatans as a', 'a.id', '=', 'kj.jabatan_id')
            ->where('karyawans.status_aktif', 'AKTIF')
            ->where('karyawans.kontrak_kerja', $getHolding->id)
            ->where('kj.main', 1)
            ->where('kj.is_active', 1)
            ->select(DB::raw("COUNT(DISTINCT karyawans.id) as jumlah"), 'a.nama_jabatan as nama_jabatan')
            ->groupBy('a.nama_jabatan')
            ->pluck('jumlah', 'nama_jabatan');
        $nama_jabatan = $count_karyawan_jabatan->keys();
        $jumlah_karyawan_jabatan = $count_karyawan_jabatan->values();

        // Grafik komposisi gender karyawan aktif.
        $count_karyawan_gender = Karyawan::where('karyawans.kontrak_kerja', $getHolding->id)
            ->where('karyawans.status_aktif', 'AKTIF')
            ->select(DB::raw("COUNT(*) as jumlah"), 'gender')
            ->groupBy('karyawans.gender')
            ->pluck('jumlah', 'gender');
        $custom_gender_data = $count_karyawan_gender->mapWithKeys(function ($jumlah, $gender_code) {
            // Tentukan nama gender berdasarkan kode numerik
            $nama_gender = match ((int)$gender_code) {
                1 => 'LAKI-LAKI',
                2 => 'PEREMPUAN',
                default => 'Lainnya', // Jika ada kode lain, berikan fallback
            };

            // Kembalikan array dengan kunci (nama gender) dan nilai (jumlah) yang baru
            return [$nama_gender => $jumlah];
        });
        $nama_gender = $custom_gender_data->keys();
        $jumlah_karyawan_gender = $custom_gender_data->values();

        // Grafik kontrak memakai data kontrak terakhir per karyawan.
        $latestKontrak = DB::table('karyawan_kontrak as kk')
            ->select('kk.karyawan_id', 'kk.lama_kontrak')
            ->selectRaw("
                ROW_NUMBER() OVER (
                    PARTITION BY kk.karyawan_id
                    ORDER BY kk.tanggal_mulai DESC NULLS LAST,
                             kk.tanggal_selesai DESC NULLS LAST,
                             kk.created_at DESC NULLS LAST,
                             kk.id_kontrak DESC
                ) as rn
            ");

        $lamaKontrakLabel = "COALESCE(NULLIF(kontrak_terakhir.lama_kontrak, ''), 'Tidak diisi')";
        $count_karyawan_kontrak = Karyawan::leftJoinSub($latestKontrak, 'kontrak_terakhir', function ($join) {
            $join->on('kontrak_terakhir.karyawan_id', '=', 'karyawans.id')
                ->where('kontrak_terakhir.rn', 1);
        })
            ->where('karyawans.kontrak_kerja', $getHolding->id)
            ->where('karyawans.status_aktif', 'AKTIF')
            ->selectRaw("COUNT(*) as jumlah, {$lamaKontrakLabel} as lama_kontrak")
            ->groupByRaw($lamaKontrakLabel)
            ->pluck('jumlah', 'lama_kontrak');
        $nama_kontrak = $count_karyawan_kontrak->keys();
        $jumlah_karyawan_kontrak = $count_karyawan_kontrak->values();

        // Grafik status pernikahan karyawan aktif.
        $count_karyawan_status = Karyawan::where('karyawans.kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            ->select(DB::raw("COUNT(*) as jumlah"), 'status_nikah')
            ->groupBy('status_nikah')
            ->pluck('jumlah', 'status_nikah');
        $custom_status_data = $count_karyawan_status->mapWithKeys(function ($jumlah, $status_nikah) {
            // Tentukan nama gender berdasarkan kode numerik
            $nama_status = match ((int)$status_nikah) {
                1 => 'Belum Kawin',
                2 => 'Sudah Kawin',
                3 => 'Cerai Hidup',
                4 => 'Cerai Mati',
                default => 'Lainnya',
            };

            // Kembalikan array dengan kunci (nama status) dan nilai (jumlah) yang baru
            return [$nama_status => $jumlah];
        });
        $nama_status = $custom_status_data->keys();
        $jumlah_karyawan_status = $custom_status_data->values();
        $jumlah_karyawan_jabatan_all = [];

        $jumlah_karyawan_jabatan_all = $jumlah_karyawan_jabatan->toArray();

        // dd($nama_jabatan->toArray(), $jumlah_karyawan_jabatan->toArray(), $jumlah_karyawan_jabatan1->toArray(), $jumlah_karyawan_jabatan_all);
        // Response JSON untuk kartu KPI dan seluruh chart di halaman dashboard.
        return response()->json([
            'nama_departemen' => $nama_departemen,
            'jumlah_karyawan_departemen' => $jumlah_karyawan_departemen,
            'labels_jabatan_all' => $nama_jabatan,
            'data_karyawan_jabatan_all' => $jumlah_karyawan_jabatan_all,
            'labels_gender' => $nama_gender,
            'data_karyawan_gender' => $jumlah_karyawan_gender,
            'labels_kontrak' => $nama_kontrak,
            'data_karyawan_kontrak' => $jumlah_karyawan_kontrak,
            'labels_status' => $nama_status,
            'karyawan_laki' => Karyawan::where('gender', '1')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->id)->count(),
            'karyawan_perempuan' => Karyawan::where('gender', '2')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->id)->count(),
            'karyawan_office' => Karyawan::where('kategori', 'Karyawan Bulanan')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->id)->count(),
            'karyawan_shift' => Karyawan::where('kategori', 'Karyawan Harian')->where('status_aktif', 'AKTIF')->where('kontrak_kerja', $getHolding->id)->count(),
            'karyawan_baru_bulan_ini' => Karyawan::where('kontrak_kerja', $getHolding->id)
                ->whereYear('tgl_join', Carbon::now()->year)
                ->whereMonth('tgl_join', Carbon::now()->month)
                ->count(),
            'karyawan_nonaktif_bulan_ini' => Karyawan::where('status_aktif', 'NON AKTIF')
                ->where('kontrak_kerja', $getHolding->id)
                ->whereYear('tanggal_nonactive', Carbon::now()->year)
                ->whereMonth('tanggal_nonactive', Carbon::now()->month)
                ->count(),
            'data_karyawan_status' => $jumlah_karyawan_status,
            'jumlah_user' => Karyawan::join('users as b', 'b.karyawan_id', '=', 'karyawans.id')
                ->join('karyawan_jabatan as kj', 'kj.karyawan_id', '=', 'karyawans.id')
                ->where('karyawans.kontrak_kerja', $getHolding->id)
                ->where('karyawans.status_aktif', 'AKTIF')
                ->where('b.is_admin', 'user')
                ->where('kj.main', 1)
                ->where('kj.is_active', 1)
                ->whereNotNull('kj.departemen_id')
                ->distinct('karyawans.id')
                ->count('karyawans.id'),
        ]);
    }
    public function holding()
    {
        // $holding = Holding::with(['Site' => function ($query) {
        //     $query;
        // }])->get();
        $holding = Holding::with(['Site' => function ($query) {
            $query->whereIn('site_status', ['SITE', 'PUSAT']);
            $query->get();
        }])->get();
        // dd($holding);
        return view('admin.dashboard.holding', ['holding' => $holding]);
    }
    private function getAbsensiBulananByStatus(Holding $holding, Carbon $startDate, Carbon $endDate): array
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $labelAbsensi = [];
        $tepatWaktu = [];
        $terlambat = [];
        $tidakHadir = [];
        $cuti = [];

        foreach ($period as $date) {
            $tanggal = $date->format('Y-m-d');
            $labelAbsensi[$tanggal] = $date->format('d/m/Y');
            $tepatWaktu[$tanggal] = 0;
            $terlambat[$tanggal] = 0;
            $tidakHadir[$tanggal] = 0;
            $cuti[$tanggal] = 0;
        }

        $rows = MappingShift::join('karyawans', 'karyawans.id', '=', 'mapping_shifts.karyawan_id')
            ->whereBetween('mapping_shifts.tanggal_masuk', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ])
            ->whereNotNull('mapping_shifts.is_active')
            ->where('karyawans.kontrak_kerja', $holding->id)
            ->select('mapping_shifts.tanggal_masuk')
            ->selectRaw("SUM(CASE WHEN mapping_shifts.status_absen = 'HADIR KERJA' AND mapping_shifts.keterangan_absensi = 'TEPAT WAKTU' THEN 1 ELSE 0 END) as tepat_waktu")
            ->selectRaw("SUM(CASE WHEN mapping_shifts.status_absen = 'HADIR KERJA' AND mapping_shifts.keterangan_absensi = 'TELAT HADIR' THEN 1 ELSE 0 END) as terlambat")
            ->selectRaw("SUM(CASE WHEN mapping_shifts.status_absen = 'TIDAK HADIR KERJA' AND mapping_shifts.keterangan_absensi = 'CUTI' AND mapping_shifts.keterangan_cuti = 'TRUE' THEN 1 ELSE 0 END) as cuti")
            ->selectRaw("SUM(CASE WHEN mapping_shifts.status_absen = 'TIDAK HADIR KERJA' AND (mapping_shifts.keterangan_absensi IS NULL OR mapping_shifts.keterangan_absensi <> 'CUTI' OR mapping_shifts.keterangan_cuti IS NULL OR mapping_shifts.keterangan_cuti <> 'TRUE') THEN 1 ELSE 0 END) as tidak_hadir")
            ->groupBy('mapping_shifts.tanggal_masuk')
            ->get();

        foreach ($rows as $row) {
            $tanggal = Carbon::parse($row->tanggal_masuk)->format('Y-m-d');
            if (!array_key_exists($tanggal, $labelAbsensi)) {
                continue;
            }

            $tepatWaktu[$tanggal] = (int) $row->tepat_waktu;
            $terlambat[$tanggal] = (int) $row->terlambat;
            $tidakHadir[$tanggal] = (int) $row->tidak_hadir;
            $cuti[$tanggal] = (int) $row->cuti;
        }

        return [
            'label_absensi' => array_values($labelAbsensi),
            'data_absensi_masuk_tepatwaktu' => array_values($tepatWaktu),
            'data_absensi_masuk_telat' => array_values($terlambat),
            'data_absensi_masuk_tidak_hadir' => array_values($tidakHadir),
            'data_absensi_masuk_cuti' => array_values($cuti),
        ];
    }

    /**
     * Endpoint AJAX grafik absensi per status pada bulan berjalan.
     */
    public function get_grafik_absensi_karyawan($holding)
    {
        $holding = Holding::where('holding_code', $holding)->firstOrFail();

        // Susun rentang tanggal bulan berjalan sebagai label sumbu X.
        $start_date = Carbon::now()->startOfMonth();
        $end_date = Carbon::now()->endOfMonth();

        return response()->json($this->getAbsensiBulananByStatus($holding, $start_date, $end_date));
    }
}
