<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleUsers;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;



use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request, $holding)
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
        $holdings = Holding::where('holding_code', $holding)->first();
        // dd($table);
        return view('admin.log.index', [
            // return view('karyawan.index', [
            'title'             => 'Data Recruitment',
            'menus'             => $menus,
            'holding'           => $holdings,
        ]);
    }
    public function dt_activity(Request $request)
    {
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $table = ActivityLog::orderBy('id', 'DESC')->where('karir', null)->whereBetween('created_at', [$now, $now1])->get();
        // dd($request->start_date);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['created_at'])
                ->make(true);
        }
    }
    public function dt_activity_mobile(Request $request)
    {
        // dd($request->start_date, $request->end_date);
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $table = ActivityLog::orderBy('id', 'DESC')->where('karir', 2)->whereBetween('created_at', [$now, $now1])->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['created_at'])
                ->make(true);
        }
    }
    public function dt_activity_karir(Request $request)
    {
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $table = ActivityLog::orderBy('id', 'DESC')->where('karir', 1)->whereBetween('created_at', [$now, $now1])->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['created_at'])
                ->make(true);
        }
    }
    public function dt_activity_system(Request $request)
    {
        $now = Carbon::parse($request->start_date)->startOfDay();
        $now1 = Carbon::parse($request->end_date)->endOfDay();
        $table = ActivityLog::orderBy('id', 'DESC')->where('karir', 3)->whereBetween('created_at', [$now, $now1])->get();
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->rawColumns(['created_at'])
                ->make(true);
        }
    }
}
