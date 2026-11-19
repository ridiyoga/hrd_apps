<?php

namespace App\Http\Controllers;

use App\Models\DokumenLegal;
use App\Models\KategoriDokumen;
use App\Models\Holding;
use App\Models\Menu;
use App\Models\RoleUsers;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DokumenLegalDashboardController extends Controller
{
    public function index(string $holdingCode, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $today = Carbon::today();

        // ================= HOLDING =================
        $holding = Holding::where('holding_code', $holdingCode)->firstOrFail();

        // ================= MENU SIDEBAR =================
        $roleIds = RoleUsers::where('role_user_id', Auth::id())
            ->pluck('role_menu_id')
            ->toArray();

        $menus = Menu::whereIn('id', function ($q) use ($roleIds) {
            $q->select('menu_id')
                ->from('role_menus')
                ->whereIn('role_id', $roleIds);
        })
            ->whereNull('parent_id')
            ->where('kategori', 'web')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        // ================= BASE QUERY =================
        $baseQuery = DokumenLegal::where('holding_id', $holding->id);

        // ================= RINGKASAN =================
        $totalDokumen = (clone $baseQuery)->count();

        $dokumenAktif = (clone $baseQuery)
            ->where('status', 'aktif')
            ->whereDate('tanggal_akhir', '>=', $today)
            ->count();

        $dokumenDigantikan = (clone $baseQuery)
            ->where('status', 'digantikan')
            ->count();

        // expired/perlu tindakan = status expired atau status aktif yang tanggalnya sudah lewat
        $dokumenExpired = (clone $baseQuery)
            ->where(function ($query) use ($today) {
                $query->where('status', 'expired')
                    ->orWhere(function ($query) use ($today) {
                        $query->where('status', 'aktif')
                            ->whereDate('tanggal_akhir', '<', $today);
                    });
            })
            ->count();

        // tidak_diperpanjang = status tidak_diperpanjang
        $dokumenTidakDiperpanjang = (clone $baseQuery)
            ->where('status', 'tidak_diperpanjang')
            ->count();

        // ================= DOKUMEN REMINDER =================
        $dokumenReminder = DokumenLegal::query()
            ->where('dokumen_legal.holding_id', $holding->id)
            ->where('dokumen_legal.status', 'aktif')
            ->join('kategori_dokumen_legal as k', 'k.id_kategori', '=', 'dokumen_legal.kategori_id')
            ->whereDate('dokumen_legal.tanggal_akhir', '>=', $today)
            ->whereRaw(
                "dokumen_legal.tanggal_akhir - (COALESCE(k.reminder_days, 30) * INTERVAL '1 day') <= ?",
                [$today]
            )
            ->select(
                'dokumen_legal.*',
                'k.nama_kategori',
                'k.reminder_days'
            )
            ->orderBy('dokumen_legal.tanggal_akhir')
            ->get();

        // ================= LIST EXPIRED =================
        $listExpired = (clone $baseQuery)
            ->where(function ($query) use ($today) {
                $query->where('status', 'expired')
                    ->orWhere(function ($query) use ($today) {
                        $query->where('status', 'aktif')
                            ->whereDate('tanggal_akhir', '<', $today);
                    });
            })
            ->orderBy('tanggal_akhir')
            ->get();

        // ================= GRAFIK STATUS =================
        $grafikStatus = (clone $baseQuery)
            ->selectRaw("
            SUM(CASE WHEN status = 'aktif' AND tanggal_akhir >= ? THEN 1 ELSE 0 END) as aktif,
            SUM(CASE WHEN status = 'digantikan' THEN 1 ELSE 0 END) as digantikan,
            SUM(CASE WHEN status = 'expired' OR (status = 'aktif' AND tanggal_akhir < ?) THEN 1 ELSE 0 END) as expired,
            SUM(CASE WHEN status = 'tidak_diperpanjang' THEN 1 ELSE 0 END) as tidak_diperpanjang
        ", [$today, $today])
            ->first();

        // ================= GRAFIK AKAN EXPIRED (BAR) =================
        $chartAkanExpired = $dokumenReminder
            ->groupBy('nama_kategori')
            ->map(fn($items) => $items->count())
            ->toArray();

        // ================= HEATMAP DATA (PER TANGGAL) =================
        $heatmapTanggal = DokumenLegal::where('holding_id', $holding->id)
            ->whereNotNull('tanggal_akhir')
            ->selectRaw(" DATE(tanggal_akhir) as tanggal,COUNT(*) as total")
            ->groupByRaw("DATE(tanggal_akhir)")
            ->orderBy('tanggal')
            ->get();

        $heatmapData = collect($heatmapTanggal)->map(function ($row) use ($today) {
            $tanggal = Carbon::parse($row['tanggal']);
            $selisih = $today->diffInDays($tanggal, false);

            if ($selisih < 0) {
                $level = 'expired';       // merah
            } elseif ($selisih <= 7) {
                $level = 'urgent';        // orange
            } elseif ($selisih <= 30) {
                $level = 'warning';       // kuning
            } else {
                $level = 'normal';        // hijau
            }

            return [
                'tanggal' => $row['tanggal'],
                'total'   => $row['total'],
                'level'   => $level,
            ];
        });

        $bulan = $today->copy()->startOfMonth();
        $akhirBulan = $today->copy()->endOfMonth();

        $docsBulan = DokumenLegal::where('holding_id', $holding->id)
            ->whereNotNull('tanggal_akhir')
            ->whereBetween('tanggal_akhir', [$bulan, $akhirBulan])
            ->get(['id', 'nama', 'nomer_dukumen', 'tanggal_akhir', 'status']);

        // index data heatmap by tanggal
        $milestones = [365, 180, 90, 60, 30, 14, 7, 3, 1];

        $byTanggal = $docsBulan->groupBy(fn($d) => Carbon::parse($d->tanggal_akhir)->toDateString());

        $calendarHeatmap = [];

        foreach (CarbonPeriod::create($bulan, $akhirBulan) as $date) {
            $tgl = $date->toDateString();
            $docs = $byTanggal->get($tgl, collect());

            // hitung komposisi level per dokumen (buat gradasi nanti)
            $counts = [
                'expired' => 0,
                'urgent'  => 0,
                'warning' => 0,
                'info'    => 0,
                'normal'  => 0,
            ];

            foreach ($docs as $doc) {
                $sisa = $today->diffInDays(Carbon::parse($doc->tanggal_akhir), false);

                if ($sisa < 0) {
                    $counts['expired']++;
                } elseif ($sisa <= 3) {
                    $counts['urgent']++;      // H-3 s/d H-1
                } elseif ($sisa <= 30) {
                    $counts['warning']++;     // H-30 s/d H-4 (atau H-7 kalau kamu mau ubah)
                } else {
                    $counts['normal']++;      // Aman (di luar window)
                }
            }

            // pilih level utama (priority)
            $level = 'none';
            if ($docs->isNotEmpty()) {
                if ($counts['expired'] > 0) $level = 'expired';
                elseif ($counts['urgent'] > 0) $level = 'urgent';
                elseif ($counts['warning'] > 0) $level = 'warning';
                elseif ($counts['info'] > 0) $level = 'info';
                else $level = 'normal';
            }

            $calendarHeatmap[] = [
                'tanggal' => $tgl,
                'day'     => $date->day,
                'level'   => $level,
                'total'   => $docs->count(),
                'counts'  => $counts, // ini buat gradasi/legend detail
            ];
        }

        //===Debugging===
        // dd([
        //     //     'today' => $today->toDateString(),
        //     //     'totalDokumen' => $totalDokumen,
        //     //     'dokumenAktif' => $dokumenAktif,
        //     //     'dokumenDigantikan' => $dokumenDigantikan,
        //     //     'dokumenExpired' => $dokumenExpired,
        //     //     'dokumenTidakDiperpanjang' => $dokumenTidakDiperpanjang,
        //     //     'grafikStatus' => $grafikStatus,
        //     'today' => $today->toDateString(),
        //     'heatmapData' => $heatmapData->toArray(),
        // ]);

        return view('admin.dashboard.dokumen_legal', compact(
            'holding',
            'menus',
            'today',
            'totalDokumen',
            'dokumenAktif',
            'dokumenDigantikan',
            'dokumenExpired',
            'dokumenTidakDiperpanjang',
            'dokumenReminder',
            'listExpired',
            'grafikStatus',
            'chartAkanExpired',
            'heatmapTanggal',
            'heatmapData',
            'calendarHeatmap'
        ));
    }

    public function heatmapDetail(Request $request, string $holdingCode)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date)->toDateString();

        $holding = Holding::where('holding_code', $holdingCode)->firstOrFail();

        $docs = DokumenLegal::with(['kategori', 'site']) // optional biar rapi di modal
            ->where('holding_id', $holding->id)
            ->whereDate('tanggal_akhir', $date)
            ->orderBy('nama')
            ->get();

        return view('admin.dashboard.heatmap_detail', compact('docs', 'date', 'holding'));
    }
}
