<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\TicketAssignmentMaster;
use App\Models\TicketDifficultyMaster;
use App\Models\User;
use App\Services\TicketKpiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketKpiController extends Controller
{
    protected TicketKpiService $kpiService;

    public function __construct(TicketKpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    private function buildMenus(array $get_role): array
    {
        if (empty($get_role)) {
            return [collect(), collect(), collect()];
        }
        $main = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 1)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();
        $sub = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 0)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();
        $bar = RoleMenu::with(['Menu' => fn($q) => $q->where('kategori', 'mobile')])
            ->whereHas('Menu', fn($q) => $q->where('kategori', 'mobile')->where('main', 2)->orderBy('sort_order'))
            ->whereIn('role_id', $get_role)->get();
        return [$main, $sub, $bar];
    }

    /**
     * Display a listing of KPI summaries for all PICs.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $summary = $this->kpiService->summaryForAllPics();

            return response()->json([
                'code' => 200,
                'message' => 'Success fetching ticket KPI summaries.',
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display KPI summary for a specific PIC.
     */
    public function show($picId): JsonResponse
    {
        try {
            $summary = $this->kpiService->summaryForPic($picId);
            $tickets = $this->kpiService->ticketsForPic($picId);

            return response()->json([
                'code' => 200,
                'message' => 'Success fetching ticket KPI summary for PIC.',
                'data' => array_merge($summary, ['tickets' => $tickets]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display KPI dashboard for PICs.
     */
    public function dashboard(Request $request, $picId = null)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $karyawan = Karyawan::where('nomor_identitas_karyawan', $user->karyawan_id)->first();

        // If picId is null, and user is a PIC, default to user's karyawan id
        if ($picId === null && $karyawan) {
            $isPic = TicketAssignmentMaster::where('karyawan_id', $karyawan->id)
                ->where('assignment_type', 'A02')
                ->where('is_active', 1)
                ->exists();
            if ($isPic) {
                $picId = (string) $karyawan->id;
            }
        }

        $targetPic = null;
        if ($picId !== null) {
            $targetPic = Karyawan::find($picId);
            $isPic = TicketAssignmentMaster::where('karyawan_id', $picId)
                ->where('assignment_type', 'A02')
                ->where('is_active', 1)
                ->exists();

            $isAssigner = $karyawan ? TicketAssignmentMaster::where('karyawan_id', $karyawan->id)
                ->where('assignment_type', 'A01')
                ->where('is_active', 1)
                ->exists() : false;

            if (! $isPic && (! $karyawan || (string) $karyawan->id !== (string) $picId) && ! $isAssigner) {
                abort(403, 'Anda tidak memiliki akses ke data PIC ini.');
            }
        }

        $roleIds = RoleUsers::where('role_user_id', $user->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($roleIds);

        $menuId = Menu::where('url', 'approval/ticket/pic/menu')
            ->orWhere('url', 'like', 'approval/ticket/pic%')
            ->value('id') ?? 0;

        return view('users.ticket.kpi_ticket', [
            'picId'         => $picId,
            'user_karyawan' => $karyawan,
            'target_pic'    => $targetPic,
            'menu_id'       => $menuId,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
        ]);
    }

    /**
     * Get ticket difficulty master list.
     */
    public function difficultyMaster(): JsonResponse
    {
        try {
            $difficulties = TicketDifficultyMaster::orderBy('code')->get();

            return response()->json([
                'code' => 200,
                'message' => 'Success fetching difficulty master data.',
                'data' => $difficulties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
