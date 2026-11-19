<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Holding;
use App\Models\Jabatan;
use App\Models\LevelJabatan;
use App\Models\Lokasi;
use App\Models\Menu;
use App\Models\RoleUsers;
use App\Models\Shift;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

class ReportMasterController extends Controller
{
    public function report_departemen($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
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
        $getHolding = Holding::where('holding_code', $holding)->first();
        if ($getHolding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        return view('admin.report_master.report_departemen', [
            'title' => 'Master Departemen',
            'holding' => $getHolding,
            'menus' => $menus,
            'data_departemen' => Departemen::all()
        ]);
    }
    public function report_divisi($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $get_holding = Holding::where('holding_code', $holding)->first();
        // dd($get_holding);
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
        if ($get_holding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        // $get = Divisi::with('Departemen')->get();
        // dd($get);
        return view('admin.report_master.report_divisi', [
            'title' => 'Master Divisi',
            'holding' => $get_holding,
            'menus' => $menus,
            'data_divisi' => Divisi::with('Departemen')->where('holding', $get_holding->id)->get(),
            'data_departemen' => Departemen::orderBy('nama_departemen', 'asc')->where('holding', $get_holding->id)->get()
        ]);
    }
    public function report_bagian($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
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
        return view('admin.report_master.report_bagian', [
            'title' => 'Master Divisi',
            'holding' => $holding,
            'menus' => $menus,
            'data_bagian' => Bagian::with('Divisi')->where('holding', $holding->id)->get(),
            'data_dept' => Departemen::orderBy('nama_departemen', 'asc')->where('holding', $holding->id)->get(),
            'data_divisi' => Divisi::orderBy('nama_divisi', 'asc')->where('holding', $holding->id)->get()
        ]);
    }
    public function report_jabatan($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
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
        $data_divisi = Divisi::with(['Departemen' => function ($query) {
            $query->orderBy('nama_departemen', 'ASC');
        }])->with(['Jabatan' => function ($query) use ($holding) {
            $query->where('holding', $holding->id);
        }])->where('holding', $holding->id)
            ->orderBy('nama_divisi', 'ASC')
            ->get();
        // dd($data_divisi);
        $data_bagian = Bagian::orderBy('nama_bagian', 'ASC')
            ->where('holding', $holding->id)
            ->get();
        $data_jabatan = Jabatan::with('Bagian')
            ->with('LevelJabatan')
            ->where('holding', $holding->id)->get();

        $level = LevelJabatan::orderBy('level_jabatan', 'ASC')->get();
        // dd($level);
        // dd($holding);
        return view('admin.report_master.report_jabatan', [
            'title' => 'Master Jabatan',
            'holding' => $holding,
            'menus' => $menus,
            'data_jabatan' => $data_jabatan,
            'data_divisi' => $data_divisi,
            'data_bagian' => $data_bagian,
            'get_level' => $level,
        ]);
    }

    public function jabatan_datatable($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
        $table =  Jabatan::where('holding', $holding->id)->with([
            'Bagian' => function ($query) {
                $query->with([
                    'Divisi' => function ($query) {
                        $query->with([
                            'Departemen' => function ($query) {
                                $query->orderBy('nama_departemen', 'ASC');
                            }
                        ]);
                    }
                ]);
            }
        ])
            ->get();
        // dd($table[0]->Bagian->Divisi);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('nama_bagian', function ($row) {
                    return $row->Bagian->nama_bagian ?? '-';
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->Bagian->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->Bagian->Divisi->Departemen->nama_departemen ?? '-';
                })
                ->rawColumns([
                    'nama_bagian',
                    'nama_divisi',
                    'nama_departemen',
                ])
                ->make(true);
        }
    }
    public function report_holding($holding, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
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
        return view('admin.report_master.report_holding', [
            'title' => 'Master Holding',
            'holding' => $holding,
            'menus' => $menus,
            'master_holding' => Holding::all()
        ]);
    }
    public function report_lokasi($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
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
        return view('admin.report_master.report_lokasi', [
            'title' => 'Setting Lokasi Kantor',
            'menus' => $menus,
            'holding' => $holding,
            'data_site' => Site::where('site_holding_category', $holding->id)->get(),
            'lokasi' => Lokasi::all()
        ]);
    }
    public function report_site($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $roleId = Auth::user();
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
        $holding = Holding::where('holding_code', $holding)->first();
        return view('admin.report_master.report_site', [
            'title' => 'Master Sites',
            'holding' => $holding,
            'menus' => $menus,
            'master_site' => Site::all()
        ]);
    }
    public function report_finger($holding, Request $request)
    {
        if (Auth::check() == false) {
            $request->session()->flash('session_timeout', 'Silahkan login terlebih dahulu.');
            return redirect()->route('login');
        }
        $holding = Holding::where('holding_code', $holding)->first();
        $roleId = Auth::user();
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
        return view('admin.report_master.report_finger', [
            'title' => 'Master Finger',
            'holding' => $holding,
            'menus' => $menus
        ]);
    }
    public function report_shift($holding)
    {
        $holding = Holding::where('holding_code', $holding)->first();
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
        $roleId = Auth::user();
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
        return view('admin.report_master.report_shift', [
            'title' => 'Master Shift',
            'holding' => $holding,
            'shift' => Shift::all(),
            'menus' => $menus
        ]);
    }
    public function report_cuti_kategori($holding, Request $request)
    {
        if (!Auth::check()) {
            $request->session()->flash('session_timeout', 'Anda harus login terlebih dahulu');
            return redirect()->route('login');
        }
        $get_holding = Holding::where('holding_code', $holding)->first();
        // dd($get_holding);
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
        if ($get_holding == null) {
            Alert::error('Error', 'Holding Tidak Ditemukan', 3000);
            return redirect()->route('dashboard_holding')->with('error', 'Holding Tidak Ditemukan');
        }
        // $get = Divisi::with('Departemen')->get();
        // dd($get);
        return view('admin.report_master.cuti_kategori', [
            'title' => 'Master Divisi',
            'holding' => $get_holding,
            'menus' => $menus,
        ]);
    }
}
