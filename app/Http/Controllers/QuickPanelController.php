<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\HistoryApproval;
use App\Models\Karyawan;
use App\Models\KuotaCutiKaryawan;
use App\Models\Menu;
use App\Models\QuickPanel;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use RealRashid\SweetAlert\Facades\Alert;

class QuickPanelController extends Controller
{
    public function getdata_quick_approval(Request $request)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $quick_approval = QuickPanel::with('Menu', 'Cuti')
                ->with('IzinTerlambat')
                ->with('IzinPulang')
                ->with('IzinKeluar')
                ->with('IzinSakit')
                ->with('IzinLibur')
                ->with('IzinTidakMasuk')
                ->with('Lembur')
                ->with('Perdin')
                ->where('status', 0)
                ->where('user_id_to', $user_karyawan->id)
                ->get();
            // dd($quick_approval);
            return response()->json([
                'code' => 200,
                'data' => $quick_approval,
                'user' => $user_karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function approve($id)
    {
        $quick = QuickPanel::where('id_quick', $id)->first();
        // dd($quick->quick_type);
        if ($quick->quick_type == 'cuti') {
            return redirect('/approval/cuti/cutiDetail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'izin_terlambat') {
            return redirect('izin/approve_terlambat_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'izin_pulang') {
            return redirect('izin/approve_pulang_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'izin_keluar') {
            return redirect('izin/approve_keluar_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'izin_sakit') {
            return redirect('izin/approve_sakit_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'izin_libur') {
            return redirect('izin/approve_libur_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'izin_tidak_masuk') {
            return redirect('izin/approve_tidak_masuk_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'lembur') {
            return redirect('lembur/approve_lembur_detail/' . $quick->content_id);
        } elseif ($quick->quick_type == 'perdin') {
            return redirect('perdin/approve_perdin_detail/' . $quick->content_id);
        }
    }
    public function get_quick_approve($id)
    {
        try {
            $user_karyawan = Karyawan::where('nomor_identitas_karyawan', Auth::user()->karyawan_id)->first();
            $quick = QuickPanel::with(
                'Menu',
                'Cuti.KategoriCuti',
                'Cuti.KaryawanPengganti',
                'Izin',
                'PerjalananDinas',
                'UserFrom',
                'Lembur',
                'Perdin',
                'UserTo'
            )->where('status', 0)->where('id_quick', $id)->first();
            return response()->json([
                'code' => 200,
                'data' => $quick,
                'user' => $user_karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
}
