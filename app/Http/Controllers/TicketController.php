<?php

namespace App\Http\Controllers;

use App\Models\ApprovalGroup;
use App\Models\Approval;
use App\Models\ApprovalUser;
use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\RoleMenu;
use App\Models\RoleUsers;
use App\Models\User;
use App\Models\TicketMenu;
use App\Models\TicketSubMenu;
use App\Models\TicketHead;
use App\Models\TicketHeadAttachment;
use App\Models\TicketDetail;
use App\Models\TicketApproval;
use App\Models\TicketApprovalComment;
use App\Models\TicketApprovalFlow;
use App\Models\TicketApprovalStep;
use App\Models\TicketApprovalValidator;
use App\Models\TicketDependencyRule;
use App\Models\TicketVendorHead;
use App\Models\TicketVendorDetail;
use App\Models\TicketVendorAttachment;
use App\Models\TicketHubMenu;
use App\Models\TicketSubDetail;



use Ramsey\Uuid\Uuid;

use App\Services\TicketApprovalService;
use App\Services\TicketApprovalEmailService;
use App\Services\TicketGenerationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Jenssegers\Agent\Agent;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
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

    private function buildHubMenuUrl($hubMenu, $menuId): string
    {
        try {
            $params = $hubMenu->route_params;
            if (is_string($params) && trim($params) !== '') {
                $params = json_decode($params, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception(
                        "route_params JSON tidak valid untuk route {$hubMenu->route_name}"
                    );
                }
            }
            $params = $params ?? [];
            foreach ($params as $key => $value) {
                if ($value === null) {
                    $params[$key] = $menuId;
                }
            }
            // Fallback: jika params kosong, deteksi nama parameter dari definisi route
            if (empty($params)) {
                $routeObj = app('router')->getRoutes()->getByName($hubMenu->route_name);
                if ($routeObj) {
                    preg_match_all('/\{(\w+?)\??}/', $routeObj->uri(), $matches);
                    $firstParam = $matches[1][0] ?? 'menu_id';
                    $params = [$firstParam => $menuId];
                } else {
                    $params = [$menuId];
                }
            }
            return route($hubMenu->route_name, $params);
        } catch (\Throwable $e) {
            Log::warning("Hub menu route tidak valid", [
                'route_name' => $hubMenu->route_name,
                'route_params' => $hubMenu->route_params,
                'menu_id' => $menuId,
                'error' => $e->getMessage(),
            ]);
            return '#';
        }
    }

    public function hub($menu_id)
    {
        $user_karyawan = $this->currentKaryawan();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        $hubMenus = \App\Models\TicketHubMenu::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereDoesntHave('users')
                    ->orWhereHas('users', fn($u) => $u->where('users.id', Auth::id()));
            })
            ->orderBy('sort_order')
            ->get()
            ->map(function ($menu) use ($menu_id) {
                $menu->resolved_url = $this->buildHubMenuUrl($menu, $menu_id);
                return $menu;
            });
        return view('users.ticket.menu-pengajuan-hub', [
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $menu_id,
            'hubMenus'      => $hubMenus,
        ]);
    }

    public function index($id)
    {
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $id]);
        return view('users.ticket.index', [
            'title'         => 'Ubah/Hapus',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $id,
        ]);
    }

    public function ticketHead_add($id)
    {
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        return view('users.ticket.tambah', [
            'title'         => 'Tambah Ubah/Hapus',
            'user_karyawan' => $user_karyawan,
            'all_menu_main' => $all_menu_main,
            'all_menus'     => $all_menus,
            'all_menu_bar'  => $all_menu_bar,
            'menu_id'       => $id,
        ]);
    }

    public function editticket($menu_id, $ticket_id)
    {
        $ticket = TicketHead::with(['ticketMenu', 'ticketSubMenu', 'ticketDetails', 'comments.karyawan', 'attachments'])->findOrFail($ticket_id);
        if (
            $ticket->approval_status === TicketHead::APPROVAL_REJECTED
            || $ticket->ticket_status === TicketHead::STATUS_CANCELLED
            || (bool) $ticket->is_submit
        ) {
            return redirect()->route('ticket.lihat', [$menu_id, $ticket_id]);
        }
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        $kategoriTicket = $ticket->ticketMenu?->kategori ?? null;
        return view('users.ticket.edit', [
            'title'           => 'Edit Ubah/Hapus',
            'user_karyawan'   => $user_karyawan,
            'ticket'          => $ticket,
            'all_menu_main'   => $all_menu_main,
            'all_menus'       => $all_menus,
            'all_menu_bar'    => $all_menu_bar,
            'menu_id'         => $menu_id,
            'kategori_ticket' => $kategoriTicket,
        ]);
    }

    public function lihatTicket($menu_id, $ticket_id)
    {
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        $ticket = TicketHead::with([
            'ticketMenu',
            'ticketSubMenu',
            'ticketDetails.subDetails',
            'ticketDetails.referenceTicketDetail',
            'comments.karyawan',
            'attachments',
            'approvals.dependencyRule',
            'approvals.branchSubMenu.ticketMenu',
            'parentTicket.ticketMenu',
            'parentTicket.ticketSubMenu',
            'rootTicket.ticketMenu',
            'rootTicket.ticketSubMenu',
            'generatedFromTicket.ticketMenu',
            'generatedFromTicket.ticketSubMenu',
            'originCrossTicket.ticketMenu',
            'originCrossTicket.ticketSubMenu',
        ])->findOrFail($ticket_id);
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        $kategoriTicket = $ticket->ticketMenu?->kategori ?? null;
        $approvalSubjectIds = $this->currentApprovalSubjectIds();
        $myGeneratedApprovals = TicketApproval::where('ticket_id', $ticket_id)
            ->where('role', 'checker')
            ->whereIn('karyawan_id', $approvalSubjectIds)
            ->whereNotNull('dependency_rule_id')
            ->get();
        $generatedRules = collect();
        if ($myGeneratedApprovals->isNotEmpty()) {
            $ruleIds  = $myGeneratedApprovals->pluck('dependency_rule_id')->unique()->values();
            $ruleMeta = DB::table('ticket_dependency_rule as tdr')
                ->leftJoin('ticket_kondisi as tk', 'tdr.condition_key', '=', 'tk.code')
                ->join('ticket_sub_menu as tsm', 'tdr.target_sub_menu_id', '=', 'tsm.id')
                ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
                ->whereIn('tdr.id', $ruleIds)
                ->select(
                    'tdr.id as rule_id',
                    'tk.label_kondisi',
                    'tk.code',
                    'tsm.nama_sub_menu as target_submenu_name',
                    'tm.nama_menu as target_menu_name'
                )
                ->get()
                ->keyBy('rule_id');
            $generatedRules = $myGeneratedApprovals->map(function ($approval) use ($ruleMeta) {
                $meta = $ruleMeta->get($approval->dependency_rule_id);
                return [
                    'approval_id'         => $approval->id,
                    'rule_id'             => $approval->dependency_rule_id,
                    'label'               => $meta->label_kondisi ?? $meta->code ?? 'Kondisi',
                    'target_menu_name'    => $meta->target_menu_name ?? '-',
                    'target_submenu_name' => $meta->target_submenu_name ?? '-',
                    'generate_status'     => $approval->generate_status,
                    'generated_ticket'    => $approval->generated_ticket_id
                        ? TicketHead::find($approval->generated_ticket_id)
                        : null,
                ];
            })->values();
        }
        $ticketStatus = ($ticket->approval_status === TicketHead::APPROVAL_REJECTED || $ticket->ticket_status === TicketHead::STATUS_CANCELLED)
            ? 'Ticket Ditolak'
            : $this->ticketStatusLabel($ticket->ticket_status);
        $ticketChainService = app(\App\Services\TicketChainService::class);
        $chainInfo          = $ticketChainService->resolveChainInfo($ticket);

        $canReviewPic = ($ticket->requestor_id == Auth::id())
            && ($ticket->ticket_status === TicketHead::STATUS_WAITING_REQUESTOR);

        $isApprover1  = $ticket->approve1_id && in_array((string) $ticket->approve1_id, $approvalSubjectIds, true);
        $isApprover2  = $ticket->approve2_id && in_array((string) $ticket->approve2_id, $approvalSubjectIds, true);
        $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;
        $approve2Done = is_null($ticket->approve2_id) || (bool) $ticket->approve2;
        $canActionApprover = ($isApprover1 && !(bool) $ticket->approve1 && ($ticket->approval_status === TicketHead::APPROVAL_PENDING_1 || is_null($ticket->approval_status) || $ticket->approval_status === TicketHead::APPROVAL_PENDING))
            || ($isApprover2 && $approve1Done && !(bool) $ticket->approve2 && ($ticket->approval_status === TicketHead::APPROVAL_PENDING_2));

        $myCheckerApprovals = $ticket->approvals->filter(function ($app) use ($approvalSubjectIds) {
            return $app->role === 'checker' && in_array((string) $app->karyawan_id, $approvalSubjectIds, true);
        });
        $isChecker   = ($ticket->checker_id && in_array((string) $ticket->checker_id, $approvalSubjectIds, true)) || $myCheckerApprovals->isNotEmpty();
        $checkerDone = $myCheckerApprovals->isNotEmpty()
            ? $myCheckerApprovals->every(fn($app) => in_array($app->status, ['completed', 'approved'], true))
            : (bool) $ticket->checked;
        $canActionChecker = $isChecker && $approve1Done && $approve2Done && !$checkerDone && in_array($ticket->approval_status, [
            TicketHead::APPROVAL_APPROVED,
            TicketHead::APPROVAL_PENDING
        ]);

        $isValidator = $ticket->validator_id
            && in_array(
                (string) $ticket->validator_id,
                $approvalSubjectIds,
                true
            );

        $canActionValidator =
            $isValidator
            && !(bool) $ticket->validator
            && $ticket->approval_status === TicketHead::APPROVAL_APPROVED;

        return view('users.ticket.lihat', [
            'title'               => 'Lihat Ubah/Hapus',
            'user_karyawan'       => $user_karyawan,
            'ticket'              => $ticket,
            'ticket_status'       => $ticketStatus,
            'all_menu_main'       => $all_menu_main,
            'all_menus'           => $all_menus,
            'all_menu_bar'        => $all_menu_bar,
            'menu_id'             => $menu_id,
            'kategori_ticket'     => $kategoriTicket,
            'generatedRules'      => $generatedRules,
            'chainInfo'           => $chainInfo,
            'can_review_pic'      => $canReviewPic,
            'can_action_approver' => $canActionApprover,
            'can_action_checker'  => $canActionChecker,
            'can_action_validator' => $canActionValidator,
        ]);
    }

    public function ticketHead_addGeneratedDraft(Request $request, $draftTicketId)
    {
        $draftTicket  = TicketHead::with(['parentTicket.ticketDetails.subDetails', 'parentTicket.ticketSubMenu', 'ticketSubMenu'])->findOrFail($draftTicketId);
        $parentTicket = $draftTicket->parentTicket;
        $subMenuId    = $draftTicket->ticket_sub_menu_id;
        $menuId       = $request->query('menuId') ?? $draftTicket->ticket_menu_id;
        $subMenu      = DB::table('ticket_sub_menu as tsm')
            ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
            ->where('tsm.id', $subMenuId)
            ->select('tsm.*', 'tm.nama_menu', 'tm.kategori as menu_kategori', 'tm.id as ticket_menu_id')
            ->first();

        if (!$draftTicket || !$subMenu) {
            abort(404);
        }
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menuId]);

        // Cross-environment metadata
        $envNames = [1 => 'Epicor ERP SPS', 2 => 'Accurate Online SP', 3 => 'Accurate Online AJA'];
        $parentKategori = null;
        if ($parentTicket && $parentTicket->ticketSubMenu) {
            $parentMenu = DB::table('ticket_menu')->where('id', $parentTicket->ticketSubMenu->menu_id)->first();
            $parentKategori = $parentMenu->kategori ?? null;
        }
        $targetKategori = $subMenu->menu_kategori ?? null;
        $isCrossEnv = $parentKategori !== null && $targetKategori !== null && (int)$parentKategori !== (int)$targetKategori;

        return view('users.ticket.tambah_generated', [
            'user_karyawan'         => $user_karyawan,
            'menu_id'               => $menuId,
            'parent_ticket'         => $parentTicket,
            'sub_menu'              => $subMenu,
            'draft_ticket'          => $draftTicket,
            'all_menu_main'         => $all_menu_main,
            'all_menus'             => $all_menus,
            'all_menu_bar'          => $all_menu_bar,
            'approval_id'           => $request->query('approvalId'),
            'exclude_submenu_id'    => $request->query('excludeSubmenuId'),
            'is_cross_environment'  => $isCrossEnv,
            'source_kategori'       => $parentKategori ? (int)$parentKategori : null,
            'target_kategori'       => $targetKategori ? (int)$targetKategori : null,
            'source_kategori_name'  => $envNames[(int)($parentKategori ?? 1)] ?? 'Epicor ERP SPS',
            'target_kategori_name'  => $envNames[(int)($targetKategori ?? 1)] ?? 'Epicor ERP SPS',
        ]);
    }

    public function ticketHead_addGenerated(Request $request, $menuId, $parentTicketId, $subMenuId)
    {
        $parentTicket  = TicketHead::with(['ticketDetails.subDetails', 'ticketSubMenu'])->find($parentTicketId);
        $draftTicketId = $request->query('draftTicketId') ?? $request->query('draft_ticket_id');
        $draftTicket = null;
        if ($draftTicketId) {
            $draftTicket = TicketHead::find($draftTicketId);
        }
        if (!$draftTicket && $parentTicket) {
            $draftTicket = TicketHead::where('parent_ticket_id', $parentTicket->id)
                ->where('ticket_sub_menu_id', $subMenuId)
                ->where('is_draft', true)
                ->latest('created_at')
                ->first();
        }
        $subMenu      = DB::table('ticket_sub_menu as tsm')
            ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
            ->where('tsm.id', $subMenuId)
            ->select('tsm.*', 'tm.nama_menu', 'tm.kategori as menu_kategori', 'tm.id as ticket_menu_id')
            ->first();
        if (!$parentTicket || !$subMenu) {
            abort(404);
        }
        $user_karyawan = $this->currentKaryawan();
        $get_role      = RoleUsers::where('role_user_id', Auth::user()->id)->pluck('role_menu_id')->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menuId]);

        // Cross-environment metadata
        $envNames = [1 => 'Epicor ERP SPS', 2 => 'Accurate Online SP', 3 => 'Accurate Online AJA'];
        $parentKategori = null;
        if ($parentTicket && $parentTicket->ticketSubMenu) {
            $parentMenu = DB::table('ticket_menu')->where('id', $parentTicket->ticketSubMenu->menu_id)->first();
            $parentKategori = $parentMenu->kategori ?? null;
        }
        $targetKategori = $subMenu->menu_kategori ?? null;
        $isCrossEnv = $parentKategori !== null && $targetKategori !== null && (int)$parentKategori !== (int)$targetKategori;

        return view('users.ticket.tambah_generated', [
            'user_karyawan'         => $user_karyawan,
            'menu_id'               => $menuId,
            'parent_ticket'         => $parentTicket,
            'sub_menu'              => $subMenu,
            'draft_ticket'          => $draftTicket,
            'all_menu_main'         => $all_menu_main,
            'all_menus'             => $all_menus,
            'all_menu_bar'          => $all_menu_bar,
            'approval_id'           => $request->query('approvalId'),
            'exclude_submenu_id'    => $request->query('excludeSubmenuId'),
            'is_cross_environment'  => $isCrossEnv,
            'source_kategori'       => $parentKategori ? (int)$parentKategori : null,
            'target_kategori'       => $targetKategori ? (int)$targetKategori : null,
            'source_kategori_name'  => $envNames[(int)($parentKategori ?? 1)] ?? 'Epicor ERP SPS',
            'target_kategori_name'  => $envNames[(int)($targetKategori ?? 1)] ?? 'Epicor ERP SPS',
        ]);
    }

    public function get_menu_kategori($kategori)
    {
        $departemenIds = $this->getActiveDepartemenIds();
        $menu_ticket = TicketMenu::where('kategori', $kategori)
            ->where(function ($q) use ($departemenIds) {
                $q->whereDoesntHave('departemens')
                    ->orWhereHas('departemens', function ($q2) use ($departemenIds) {
                        $q2->whereIn('departemens.id', $departemenIds);
                    });
            })
            ->get();
        return response()->json([
            'code' => 200,
            'menu' => $menu_ticket,
        ]);
    }

    public function get_submenu_ticket($id)
    {
        $submenu_ticket = TicketSubMenu::where('menu_id', $id)->get();
        return response()->json([
            'code' => 200,
            'menu' => $submenu_ticket,
        ]);
    }

    public function getKondisiMenu($menu_id)
    {
        try {
            $menu = TicketMenu::with(['kondisis' => function ($q) {
                $q->where('status', 1)
                    ->select(
                        'ticket_kondisi.id',
                        'ticket_kondisi.code',
                        'ticket_kondisi.label_kondisi',
                        'ticket_kondisi.tipe_data',
                        'ticket_kondisi.status'
                    );
            }])->findOrFail($menu_id);
            return response()->json([
                'code'    => 200,
                'kondisi' => $menu->kondisis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_ticket_ubah_hapus()
    {
        try {
            $tickets = TicketHead::with(['ticketMenu', 'ticketSubMenu'])
                ->where('requestor_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
            $result = $tickets->map(function ($ticket) {
                $steps     = [];
                $completed = 0;
                $addStep = function (string $step, bool $done) use (&$steps, &$completed) {
                    $steps[] = $step;
                    if ($done) {
                        $completed++;
                    }
                };
                if ($ticket->approve1_id) {
                    $addStep('approve1', (bool) $ticket->approve1);
                }
                if ($ticket->approve2_id) {
                    $addStep('approve2', (bool) $ticket->approve2);
                }
                if ($ticket->checker_id) {
                    $addStep('checker', (bool) $ticket->checked);
                }
                $addStep('validator', (bool) $ticket->validator);
                $isDone = $this->isTicketDone($ticket);
                $addStep('selesai', $isDone);
                $total = count($steps);
                if ($isDone) {
                    $completed = $total;
                }
                $progress = $total > 0
                    ? round(($completed / $total) * 100)
                    : 0;
                $envKategori = (int) ($ticket->ticketMenu?->kategori ?? 0);
                if (str_contains($ticket->nomor_ticket, 'AOL-AJA')) {
                    $envKategori = 3;
                } elseif (str_contains($ticket->nomor_ticket, 'AOL-SP') || str_contains($ticket->nomor_ticket, 'AOL-SPS')) {
                    $envKategori = 2;
                } elseif (str_contains($ticket->nomor_ticket, 'EPIC')) {
                    $envKategori = 1;
                } elseif ($envKategori === 0) {
                    $envKategori = 1;
                }

                $envName = match ($envKategori) {
                    1 => 'Epicor ERP',
                    2 => 'Accurate Online SP',
                    3 => 'Accurate Online AJA',
                    default => 'Epicor ERP',
                };

                return [
                    'id'                   => $ticket->id,
                    'nomor_ticket'         => $ticket->nomor_ticket,
                    'ticket_menu'          => $ticket->ticketMenu,
                    'ticket_sub_menu'      => $ticket->ticketSubMenu,
                    'environment_kategori' => $envKategori,
                    'environment_name'     => $envName,
                    'created_at'           => $ticket->created_at,
                    'is_submit'            => $ticket->is_submit,
                    'is_draft'             => (bool) $ticket->is_draft,
                    'is_generated'         => (bool) $ticket->is_generated,
                    'approval_status'      => $ticket->approval_status,
                    'ticket_status'        => $ticket->ticket_status,
                    'status_label'         => $this->ticketStatusLabel($ticket->ticket_status),
                    'pic_name'             => $ticket->pic_name,
                    'finish_at'            => $ticket->finish_at,
                    'closed_at'            => $ticket->closed_at,
                    'can_review_pic'       => $ticket->ticket_status === TicketHead::STATUS_WAITING_REQUESTOR,
                    'progress_percent'     => $progress,
                    'progress_done'        => $completed,
                    'progress_total'       => $total,
                ];
            });
            return response()->json([
                'code'   => 200,
                'ticket' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getActiveDepartemenIds()
    {
        $user = auth()->user();
        $karyawan = $user->KaryawanNIK;
        if (!$karyawan) {
            return [];
        }
        return $karyawan->KaryawanJabatan()
            ->where('is_active', 1)
            ->pluck('departemen_id')
            ->filter()
            ->values()
            ->toArray();
    }

    public function requestorActionTicket(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'action'       => 'required|in:feedback,close',
            'comment_text' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()]);
        }
        if ($request->action === 'feedback' && empty(trim((string) $request->comment_text))) {
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->username,
                'menu_id' => null,
                'menu_name' => 'Requestor',
                'id_table' => '$ticketId->nomor_ticket',
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'ticket_head',
                'activity' => 'Feedback',
                'description' => 'Mengajukan Feedback pada Ticket: ' . $ticketId->nomor_ticket . ' Untuk PIC',
                'keterangan' => 'success',
                'karir' => '2'
            ]);
            return response()->json(['code' => 422, 'message' => 'Feedback wajib diisi.']);
        }
        try {
            $ticket = TicketHead::where('requestor_id', Auth::id())->findOrFail($ticketId);
            if ($ticket->ticket_status !== TicketHead::STATUS_WAITING_REQUESTOR) {
                return response()->json([
                    'code'    => 422,
                    'message' => 'Ticket belum berada pada status menunggu pengecekan pengaju.',
                ]);
            }
            $karyawan = $this->currentKaryawan();
            DB::transaction(function () use ($request, $ticket, $karyawan) {
                $fresh = TicketHead::lockForUpdate()->findOrFail($ticket->id);
                if ($fresh->ticket_status !== TicketHead::STATUS_WAITING_REQUESTOR) {
                    throw new \Exception('Status ticket sudah berubah.');
                }
                $kpiService = app(\App\Services\TicketKpiService::class);
                if ($request->action === 'close') {
                    $kpiService->closeByRequestor($fresh);
                    ActivityLog::create([
                        'user_id' => $request->user()->id,
                        'user_name' => $request->user()->username,
                        'menu_id' => null,
                        'menu_name' => 'Requestor',
                        'id_table' => $ticket->id,
                        'holding_id' => '-',
                        'holding_category' => '-',
                        'table' => 'ticket_head',
                        'activity' => 'Close',
                        'description' => 'Ticket: ' . $ticket->nomor_ticket . ' Selesai dan ditutup',
                        'keterangan' => 'Success',
                        'karir' => '2'
                    ]);
                    $this->saveTicketComment($fresh->id, 'requestor', $karyawan, 'close', $request->comment_text);
                    return;
                }
                $kpiService->returnWithFeedback($fresh, $request->comment_text);
                $this->saveTicketComment($fresh->id, 'requestor', $karyawan, 'feedback', $request->comment_text);
            });

            if ($request->action === 'feedback') {
                app(TicketApprovalEmailService::class)->sendNextActionNotification(
                    $ticket->fresh(),
                    $ticket->ticket_menu_id,
                    'feedback'
                );
            }

            return response()->json([
                'code'    => 200,
                'message' => $request->action === 'close'
                    ? 'Ticket berhasil ditutup.'
                    : 'Feedback berhasil dikirim ke PIC.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function previewApprovalFlow(Request $request, $submenuId)
    {
        $environment        = $request->environment;
        $includeBackward    = $request->boolean('include_backward') || $request->boolean('include_backward_dependency');
        $selectedConditions = $request->input('selected_conditions', []);

        // ── Resolve ticket context (sama persis dengan resolveApprovalData saat simpan) ──
        $ticketId = $request->input('draft_ticket_id') ?? $request->input('ticket_id') ?? $request->route('draftTicketId');
        $ticket   = $ticketId ? TicketHead::find($ticketId) : null;
        if (!$ticket && $request->has('parent_ticket_id')) {
            $parent = TicketHead::find($request->parent_ticket_id);
            if ($parent) {
                $ticket = TicketHead::where('parent_ticket_id', $parent->id)
                    ->where('ticket_sub_menu_id', $submenuId)
                    ->latest('created_at')
                    ->first();
                if (!$ticket) {
                    $ticket = new TicketHead([
                        'ticket_sub_menu_id'       => $submenuId,
                        'parent_ticket_id'         => $parent->id,
                        'root_ticket_id'           => $parent->root_ticket_id ?? $parent->id,
                        'generated_from_ticket_id' => $parent->id,
                    ]);
                }
            }
        }

        // ── Flow & requestor ──
        $flow = $this->findApprovalFlow($submenuId); // path yang sama dengan resolveApprovalData
        if (!$flow) {
            return response()->json(['code' => 404, 'message' => 'Alur persetujuan tidak ditemukan']);
        }
        $requestorKaryawan = $this->currentKaryawan();
        if (!$requestorKaryawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.']);
        }

        // ── Checkers (path yang sama dengan resolveApprovalData) ──
        $checkers = $this->resolveMultiCheckers($submenuId, $includeBackward, $selectedConditions, $ticket);

        // ── Approvers (SATU fungsi, path yang sama dengan resolveApprovalData) ──
        $approvers = $this->resolveApprovers($flow, $requestorKaryawan, !empty($checkers));

        if (!$approvers['found']) {
            return response()->json([
                'code'       => 404,
                'found'      => false,
                'flow_name'  => $flow->nama_flow,
                'message'    => 'Anda belum terdaftar ke menu pengajuan ubah hapus atau atasan langsung tidak ditemukan pada alur persetujuan. Silakan hubungi Administrator.',
                'approvers'  => [],
                'checker'    => null,
                'validators' => [],
            ]);
        }

        // ── Format preview dari hasil resolveApprovers ──
        $preview = collect();
        if (!empty($approvers['approve1_ids'])) {
            $preview->push([
                'step_order'     => $approvers['approve1_step_order'],
                'step_name'      => $approvers['approve1_step_name'],
                'role'           => 'approve1',
                'label'          => 'Approver 1',
                'karyawan_id'    => $approvers['approve1_id'],
                'karyawan_name'  => $approvers['approve1_name'],
                'karyawan_ids'   => $approvers['approve1_ids'],
                'karyawan_names' => $approvers['approve1_names'],
            ]);
        }
        if (!empty($approvers['approve2_ids'])) {
            $preview->push([
                'step_order'     => $approvers['approve2_step_order'],
                'step_name'      => $approvers['approve2_step_name'],
                'role'           => 'approve2',
                'label'          => 'Approver 2',
                'karyawan_id'    => $approvers['approve2_id'],
                'karyawan_name'  => $approvers['approve2_name'],
                'karyawan_ids'   => $approvers['approve2_ids'],
                'karyawan_names' => $approvers['approve2_names'],
            ]);
        }

        // ── Available conditions & Cross-Environment Triggers ──
        $forwardRules = TicketDependencyRule::where('source_sub_menu_id', $submenuId)
            ->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->get();

        $backwardRules = TicketDependencyRule::where('target_sub_menu_id', $submenuId)
            ->where('is_bidirectional', true)
            ->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->get();

        $engine = app(\App\Services\DependencyEngine::class);
        $forwardRules = $forwardRules->filter(function ($rule) use ($engine, $ticket) {
            if (!$engine->isValidRuleConfig($rule)) return false;
            if ($ticket) {
                if ($engine->isIncomingEdge($ticket, $rule)) return false;
                $targetId = $engine->getTargetSubMenuId($rule, $ticket->ticket_sub_menu_id);
                if (!$targetId || $engine->detectLoop($ticket, $targetId)) return false;
            }
            return true;
        });

        $backwardRules = $backwardRules->filter(function ($rule) use ($engine, $ticket) {
            if (!$engine->isValidRuleConfig($rule)) return false;
            if ($ticket) {
                if ($engine->isIncomingEdge($ticket, $rule)) return false;
                $targetId = $engine->getTargetSubMenuId($rule, $ticket->ticket_sub_menu_id);
                if (!$targetId || $engine->detectLoop($ticket, $targetId)) return false;
            }
            return true;
        });

        $availableConditions = [];
        $crossEnvConditions   = [];
        foreach ($forwardRules as $rule) {
            $isCond  = (bool)($rule->is_conditional ?? false);
            $isCross = (bool)($rule->is_cross_environment ?? false);
            $targetSub    = TicketSubMenu::with('ticketMenu')->find($rule->target_sub_menu_id);
            $kondisiModel = !empty($rule->condition_key) ? \App\Models\TicketKondisiMenu::where('code', $rule->condition_key)->first() : null;
            $key = !empty($rule->condition_key) ? $rule->condition_key : $rule->id;

            $kategori = (int)($targetSub?->ticketMenu?->kategori ?? 1);
            $kategoriName = $kategori === 2 ? 'Accurate Online SP' : ($kategori === 3 ? 'Accurate Online AJA' : 'Epicor ERP SPS');

            $label = !empty($rule->description)
                ? $rule->description
                : ($kondisiModel?->label_kondisi ?? ('Revisi Modul di ' . $kategoriName . ': ' . ($targetSub?->nama_sub_menu ?? 'Terkait')));

            $itemData = [
                'rule_id'              => $rule->id,
                'condition_key'        => $key,
                'label'                => $label,
                'is_conditional'       => $isCond,
                'is_cross_environment' => $isCross,
                'is_bidirectional'     => (bool)($rule->is_bidirectional ?? false),
                'direction'            => 'FORWARD',
                'target_submenu_id'    => $targetSub?->id,
                'target_name'          => $targetSub ? ($targetSub->ticketMenu?->nama_menu . ' › ' . $targetSub->nama_sub_menu) : 'Branch',
                'target_kategori'      => $kategori,
                'target_kategori_name' => $kategoriName,
            ];

            if ($isCross) {
                $crossEnvConditions[] = $itemData;
            } elseif ($isCond) {
                $availableConditions[] = $itemData;
            }
        }

        // Backward / Reverse Cross-Environment & Conditional Rules
        foreach ($backwardRules as $rule) {
            $isCond  = (bool)($rule->is_conditional ?? false);
            $isCross = (bool)($rule->is_cross_environment ?? false);
            $targetSub    = TicketSubMenu::with('ticketMenu')->find($rule->source_sub_menu_id);
            $kondisiModel = !empty($rule->condition_key) ? \App\Models\TicketKondisiMenu::where('code', $rule->condition_key)->first() : null;
            $key = !empty($rule->condition_key) ? $rule->condition_key : $rule->id;

            $kategori = (int)($targetSub?->ticketMenu?->kategori ?? 1);
            $kategoriName = $kategori === 2 ? 'Accurate Online SP' : ($kategori === 3 ? 'Accurate Online AJA' : 'Epicor ERP SPS');

            if ($isCross) {
                // Pada alur balik (reverse bidirectional) lintas environment,
                // teks harus menanyakan revisi pada sistem target alur balik ($kategoriName).
                $envAliases = [
                    'Accurate Online AJA',
                    'Accurate Online SP',
                    'Epicor ERP SPS',
                    'Accurate AJA',
                    'Accurate SP',
                    'Epicor ERP',
                    'Epicor SPS',
                ];
                $desc = !empty($rule->description) ? $rule->description : ('Apakah perlu merevisi transaksi di ' . $kategoriName . '?');
                $replaced = false;
                foreach ($envAliases as $alias) {
                    if (stripos($desc, $alias) !== false) {
                        $desc = str_ireplace($alias, $kategoriName, $desc);
                        $replaced = true;
                        break;
                    }
                }
                if (!$replaced) {
                    $desc = 'Apakah perlu merevisi transaksi di ' . $kategoriName . '?';
                }
                $label = !str_starts_with($desc, '[Revisi Balik]') ? ('[Revisi Balik] ' . $desc) : $desc;
            } else {
                $desc = !empty($rule->description)
                    ? $rule->description
                    : ($kondisiModel?->label_kondisi ?? ('Revisi Modul di ' . $kategoriName . ': ' . ($targetSub?->nama_sub_menu ?? 'Terkait')));
                $label = !str_starts_with($desc, '[Revisi Balik]') ? ('[Revisi Balik] ' . $desc) : $desc;
            }

            $itemData = [
                'rule_id'              => $rule->id,
                'condition_key'        => $key,
                'label'                => $label,
                'is_conditional'       => $isCond,
                'is_cross_environment' => $isCross,
                'is_bidirectional'     => true,
                'direction'            => 'REVERSE',
                'target_submenu_id'    => $targetSub?->id,
                'target_name'          => $targetSub ? ($targetSub->ticketMenu?->nama_menu . ' › ' . $targetSub->nama_sub_menu) : 'Reverse Branch',
                'target_kategori'      => $kategori,
                'target_kategori_name' => $kategoriName,
            ];

            if ($isCross) {
                $crossEnvConditions[] = $itemData;
            } elseif ($isCond && $includeBackward) {
                $availableConditions[] = $itemData;
            }
        }

        // ── Validators ──
        $validators = TicketApprovalValidator::with('karyawan')
            ->where('is_active', true)
            ->where('environment', $environment)
            ->get()
            ->map(fn($v) => [
                'role'          => 'validator',
                'label'         => 'Validator',
                'karyawan_id'   => $v->karyawan_id,
                'karyawan_name' => $v->karyawan?->name,
                'environment'   => $v->environment,
            ])
            ->values();

        return response()->json([
            'code'                 => 200,
            'flow_name'            => $flow->nama_flow,
            'approvers'            => $preview,
            'checker'              => !empty($checkers) ? $checkers[0] : null,
            'checkers'             => $checkers,
            'validators'           => $validators,
            'available_conditions' => $availableConditions,
            'cross_env_conditions' => $crossEnvConditions,
        ]);
    }

    /**
     * Rule validasi untuk field details & sub_details (dipakai di ticketHead_store
     * dan ticketHead_storeGenerated) supaya konsisten dan tidak duplikasi.
     */
    private function ticketDetailValidationRules(): array
    {
        return [
            'menu_ticket'    => 'required',
            'submenu_ticket' => 'required',
            'details'        => ['required', 'array', 'min:1'],
            'details.*.nomor_transaksi'   => ['required', 'string', 'max:255'],
            'details.*.tanggal_transaksi' => ['required', 'date'],
            'details.*.sub_details'       => ['required', 'array', 'min:1'],
            'details.*.sub_details.*.line_number'  => ['required'],
            'details.*.sub_details.*.alasan_ubah'  => ['required', 'string'],
            'details.*.sub_details.*.before_ubah'  => ['required', 'string'],
            'details.*.sub_details.*.after_ubah'   => ['required', 'string'],
            'lampiran'   => 'nullable|array',
            'lampiran.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,jpg,jpeg,png,gif,webp,bmp,zip,rar,7z', 'max:20480'],
            'signature'  => 'required',
        ];
    }

    /**
     * Pesan custom (Bahasa Indonesia) untuk rule di atas. Didefinisikan eksplisit
     * di sini (bukan mengandalkan file lang/validation.php) agar pesan yang tampil
     * ke user selalu jelas dan tidak pernah muncul sebagai key mentah semacam
     * "validation.required" saat file terjemahan untuk locale aktif tidak lengkap.
     */
    private function ticketDetailValidationMessages(): array
    {
        return [
            'menu_ticket.required'    => 'Modul wajib dipilih.',
            'submenu_ticket.required' => 'Jenis perubahan wajib dipilih.',

            'details.required' => 'Minimal harus ada 1 transaksi yang diisi pada detail perubahan.',
            'details.array'    => 'Data detail perubahan tidak valid.',
            'details.min'      => 'Minimal harus ada 1 transaksi yang diisi pada detail perubahan.',

            'details.*.nomor_transaksi.required' => 'Nomor transaksi wajib diisi pada setiap detail perubahan.',
            'details.*.nomor_transaksi.string'   => 'Nomor transaksi tidak valid.',
            'details.*.nomor_transaksi.max'      => 'Nomor transaksi maksimal 255 karakter.',

            'details.*.tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi pada setiap detail perubahan.',
            'details.*.tanggal_transaksi.date'      => 'Format tanggal transaksi tidak valid.',

            'details.*.sub_details.required' => 'Minimal 1 baris perubahan (sub detail) wajib diisi pada setiap detail perubahan.',
            'details.*.sub_details.array'    => 'Data sub detail tidak valid.',
            'details.*.sub_details.min'      => 'Minimal 1 baris perubahan (sub detail) wajib diisi pada setiap detail perubahan.',

            'details.*.sub_details.*.line_number.required' => 'Nomor line pada sub detail wajib diisi.',
            'details.*.sub_details.*.alasan_ubah.required' => 'Alasan ubah pada sub detail wajib diisi.',
            'details.*.sub_details.*.alasan_ubah.string'   => 'Alasan ubah tidak valid.',
            'details.*.sub_details.*.before_ubah.required' => 'Data "Sebelum Perubahan" pada sub detail wajib diisi.',
            'details.*.sub_details.*.before_ubah.string'   => 'Data "Sebelum Perubahan" tidak valid.',
            'details.*.sub_details.*.after_ubah.required'  => 'Data "Setelah Perubahan" pada sub detail wajib diisi.',
            'details.*.sub_details.*.after_ubah.string'    => 'Data "Setelah Perubahan" tidak valid.',

            'lampiran.array' => 'Data lampiran tidak valid.',
            // 'lampiran.max'   => 'Lampiran maksimal 10 file.',
            'lampiran.*.file'  => 'Lampiran harus berupa file.',
            'lampiran.*.mimes' => 'Format lampiran tidak didukung.',
            'lampiran.*.max'   => 'Ukuran setiap lampiran maksimal 5MB.',

            'signature.required' => 'Tanda tangan wajib diisi.',
        ];
    }

    public function ticketHead_store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->ticketDetailValidationRules(), $this->ticketDetailValidationMessages());
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => 'Data yang diisi belum lengkap, silakan periksa kembali.',
                'errors'  => $validator->errors(),
            ]);
        }
        $submenu_id = (string) $request->submenu_ticket;
        $menu_id    = (string) $request->menu_ticket;
        $user_karyawan = $this->currentKaryawan();
        if (!$user_karyawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.'], 403);
        }
        $includeBackward    = $request->boolean('include_backward_dependency');
        $selectedConditions = $request->input('selected_conditions', []);
        try {
            $approval = $this->resolveApprovalData($submenu_id, $user_karyawan, $includeBackward, $selectedConditions);
        } catch (\Exception $e) {
            return response()->json(['code' => 422, 'message' => $e->getMessage()], 422);
        }
        $id_ticket     = (string) Uuid::uuid4();
        $nomorTicket   = $this->generateNomorTicket($request->ticket);
        $signaturePath = $this->storeSignature($request);
        $initialStatus = $this->determineInitialStatus($approval, $submenu_id);

        $hasCrossEnvChecker = collect($approval['checkers'] ?? [])->contains(fn($c) => !empty($c['is_cross_environment']));

        $ticketHead = TicketHead::create([
            'id'                  => $id_ticket,
            'nomor_ticket'        => $nomorTicket,
            'ticket_menu_id'      => $menu_id,
            'ticket_sub_menu_id'  => $submenu_id,
            'requestor_id'        => Auth::id(),
            'requestor_name'      => $user_karyawan->name,
            'requestor_signature' => $signaturePath,
            'ticket_status'       => $initialStatus,
            'approval_status'     => $approval['auto_approved']
                ? TicketHead::APPROVAL_APPROVED
                : TicketHead::APPROVAL_PENDING,
            'cross_env_status'    => $hasCrossEnvChecker ? TicketHead::CROSS_ENV_PENDING_TRIGGER : TicketHead::CROSS_ENV_NONE,
            'is_submit'           => true,
            'approve1'            => $approval['auto_approved'] ? true : null,
            'approve1_id'         => $approval['approve1_id'],
            'approve1_name'       => $approval['approve1_name'],
            'approve1_at'         => $approval['auto_approved'] ? now() : null,

            'approve2'            => $approval['auto_approved'] ? true : null,
            'approve2_id'         => $approval['approve2_id'],
            'approve2_name'       => $approval['approve2_name'],
            'approve2_at'         => $approval['auto_approved'] ? now() : null,

            'checked'             => 0,
            'checker_id'          => $approval['checker_id'],
            'checker_name'        => $approval['checker_name'],

            'validator'           => !empty($approval['auto_validated']) ? true : null,
            'validator_id'        => !empty($approval['auto_validated']) ? $user_karyawan->id : null,
            'validator_name'      => !empty($approval['auto_validated']) ? $user_karyawan->name : null,
            'validator_at'        => !empty($approval['auto_validated']) ? now() : null,
            'validator_signature' => !empty($approval['auto_validated']) ? $signaturePath : null,
            'execution_status'    => !empty($approval['auto_validated']) ? TicketHead::EXEC_READY : TicketHead::EXEC_WAITING,

            'created_at'          => now()->toDateTimeString(),
        ]);
        $this->storeLampiranMultiple($request, $ticketHead->id);
        $this->syncTicketCheckers($id_ticket, $approval['checkers'] ?? []);
        $this->saveTicketDetails($id_ticket, $request);
        $ticketHead->update(['root_ticket_id' => $ticketHead->id]);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $request->user()->username,
            'menu_id' => null,
            'menu_name' => 'Requestor Ticket Head',
            'id_table' => $ticketHead->id,
            'holding_id' => '-',
            'holding_category' => '-',
            'table' => 'ticket_head',
            'activity' => 'Mengajukan Ticket',
            'description' => 'Ticket ' . $ticketHead->nomor_ticket . ' berhasil diajukan.',
            'keterangan' => 'Success',
            'karir' => '2'
        ]);
        app(TicketApprovalEmailService::class)->sendNextActionNotification(
            $ticketHead,
            $request->menu_ticket,
            'submitted'
        );
        return response()->json([
            'code'    => 200,
            'message' => 'Ticket berhasil diajukan.',
        ]);
    }

    public function ticketHead_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->ticketDetailValidationRules(), $this->ticketDetailValidationMessages());
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => 'Data yang diisi belum lengkap, silakan periksa kembali.',
                'errors'  => $validator->errors(),
            ], 422);
        }
        $ticket = TicketHead::with(['ticketMenu', 'ticketSubMenu', 'ticketDetails'])->findOrFail($id);
        if ($ticket->ticket_status !== TicketHead::STATUS_DRAFT) {
            return response()->json([
                'code'    => 422,
                'message' => 'Ticket tidak dapat diedit karena sudah diproses.',
            ], 422);
        }
        $user_karyawan = $this->currentKaryawan();
        if (!$user_karyawan) {
            return response()->json([
                'code'    => 403,
                'message' => 'Data karyawan user login tidak ditemukan.',
            ], 403);
        }
        $submenuId = (string) $request->submenu_ticket;
        try {
            $approval = $this->resolveApprovalData($submenuId, $user_karyawan);
        } catch (\Exception $e) {
            return response()->json(['code' => 422, 'message' => $e->getMessage()], 422);
        }
        $lampiranPath = $ticket->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($ticket->lampiran && Storage::disk('public')->exists($ticket->lampiran)) {
                Storage::disk('public')->delete($ticket->lampiran);
            }
            $lampiranPath = $request->file('lampiran')->store('lampiran_tickets', 'public');
        }
        if ($ticket->requestor_signature && Storage::disk('public')->exists($ticket->requestor_signature)) {
            Storage::disk('public')->delete($ticket->requestor_signature);
        }
        $signaturePath = $this->storeSignature($request);
        DB::transaction(function () use ($request, $ticket, $lampiranPath, $signaturePath, $approval, $submenuId) {
            $ticket->update([
                'ticket_menu_id'      => $request->menu_ticket,
                'ticket_sub_menu_id'  => $submenuId,
                'requestor_signature' => $signaturePath,
                'is_submit'           => true,
                'ticket_status'       => $this->determineInitialStatus($approval, $submenuId),

                'approval_status'     => $approval['auto_approved']
                    ? TicketHead::APPROVAL_APPROVED
                    : TicketHead::APPROVAL_PENDING,
                'approve1'            => $approval['auto_approved'] ? true : null,
                'approve1_id'         => $approval['approve1_id'],
                'approve1_name'       => $approval['approve1_name'],
                'approve1_at'         => $approval['auto_approved'] ? now() : null,
                'approve1_signature'  => null,

                'approve2'            => $approval['auto_approved'] ? true : null,
                'approve2_id'         => $approval['approve2_id'],
                'approve2_name'       => $approval['approve2_name'],
                'approve2_at'         => $approval['auto_approved'] ? now() : null,
                'approve2_signature'  => null,

                'checked'             => 0,
                'checker_id'          => $approval['checker_id'],
                'checker_name'        => $approval['checker_name'],
                'checker_at'          => null,
                'checker_signature'   => null,

                'validator'           => !empty($approval['auto_validated']) ? true : null,
                'validator_id'        => !empty($approval['auto_validated']) ? $user_karyawan->id : null,
                'validator_name'      => !empty($approval['auto_validated']) ? $user_karyawan->name : null,
                'validator_at'        => !empty($approval['auto_validated']) ? now() : null,
                'validator_signature' => !empty($approval['auto_validated']) ? $signaturePath : null,
                'execution_status'    => !empty($approval['auto_validated']) ? TicketHead::EXEC_READY : TicketHead::EXEC_WAITING,

                'updated_at'          => now()->toDateTimeString(),
            ]);
            $this->storeLampiranMultiple($request, $ticket->id);
            $this->syncTicketCheckers($ticket->id, $approval['checkers'] ?? []);
            TicketDetail::where('ticket_id', $ticket->id)->delete();
            $this->saveTicketDetails($ticket->id, $request);
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $request->user()->username,
                'menu_id' => null,
                'menu_name' => 'Requestor Ticket Head',
                'id_table' => $ticket->id,
                'holding_id' => '-',
                'holding_category' => '-',
                'table' => 'ticket_head',
                'activity' => 'Mengajukan Ticket',
                'description' => 'Ticket ' . $ticket->nomor_ticket . ' berhasil diajukan.',
                'keterangan' => 'Success',
                'karir' => '2'
            ]);
        });
        app(TicketApprovalEmailService::class)->sendNextActionNotification(
            $ticket->refresh(),
            $request->menu_ticket,
            'submitted'
        );
        return response()->json([
            'code'    => 200,
            'message' => 'Ticket berhasil diperbarui.',
        ]);
    }
    public function ticketHead_storeGenerated(Request $request)
    {
        if ($request->hasFile('lampiran')) {
            $lampiranFiles = $request->file('lampiran');
            if (!is_array($lampiranFiles)) {
                $request->files->set('lampiran', [$lampiranFiles]);
            }
        } else {
            $request->files->remove('lampiran');
            $request->request->remove('lampiran');
        }

        if ($request->hasFile('lampiran')) {
            $lampiranFiles = $request->file('lampiran');
            if (!is_array($lampiranFiles)) {
                $request->files->set('lampiran', [$lampiranFiles]);
            }
        } else {
            $request->files->remove('lampiran');
            $request->request->remove('lampiran');
        }

        $rules = array_merge($this->ticketDetailValidationRules(), [
            'parent_ticket_id' => 'required|exists:ticket_head,id',
            'submenu_id'       => 'required',
            'ticket_menu_id'   => 'required',
        ]);
        unset($rules['menu_ticket'], $rules['submenu_ticket'], $rules['signature']);

        $messages = array_merge($this->ticketDetailValidationMessages(), [
            'parent_ticket_id.required' => 'Ticket induk tidak ditemukan.',
            'parent_ticket_id.exists'   => 'Ticket induk tidak ditemukan.',
            'submenu_id.required'       => 'Jenis perubahan wajib dipilih.',
            'ticket_menu_id.required'   => 'Modul wajib dipilih.',
        ]);

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'code'    => 422,
                'message' => 'Data yang diisi belum lengkap, silakan periksa kembali.',
                'errors'  => $validator->errors(),
            ]);
        }
        $parentTicket  = TicketHead::findOrFail($request->parent_ticket_id);
        $user_karyawan = $this->currentKaryawan();
        if (!$user_karyawan) {
            return response()->json(['code' => 403, 'message' => 'Data karyawan user login tidak ditemukan.'], 403);
        }
        $draftTicketId = $request->input('draft_ticket_id') ?? $request->input('draftTicketId');
        $draftTicket   = null;
        if ($draftTicketId) {
            $draftTicket = TicketHead::find($draftTicketId);
        }
        if (!$draftTicket) {
            $draftTicket = TicketHead::where('parent_ticket_id', $parentTicket->id)
                ->where('ticket_sub_menu_id', $request->submenu_id)
                ->where('is_draft', true)
                ->latest('created_at')
                ->first();
        }
        if (!$draftTicket) {
            $targetSubmenuId = $request->submenu_id;

            // ── Resolve dependency_rule_id dari approval row yang dikaitkan ──
            $approvalRuleId = null;
            $approvalIdParam = $request->input('approval_id');
            $isCrossEnv = false;
            if ($approvalIdParam) {
                $linkedApproval = \App\Models\TicketApproval::find($approvalIdParam);
                $approvalRuleId = $linkedApproval?->dependency_rule_id;
                if ($approvalRuleId) {
                    $ruleModel = \App\Models\TicketDependencyRule::find($approvalRuleId);
                    $isCrossEnv = (bool) ($ruleModel?->is_cross_environment ?? false);
                }
                Log::info('[TicketController] storeGenerated: resolved dependency_rule_id & cross_env', [
                    'approval_id'          => $approvalIdParam,
                    'dependency_rule_id'   => $approvalRuleId,
                    'is_cross_environment' => $isCrossEnv,
                    'parent_ticket_id'     => $parentTicket->id,
                    'submenu_id'           => $targetSubmenuId,
                ]);
            }

            $draftTicketId = (string) Uuid::uuid4();

            // ── Root Ticket ID: Jika Cross-Environment, bentuk Root Group Baru ──
            if ($isCrossEnv) {
                $rootTicketId = $draftTicketId;
            } else {
                $rootTicketId = $parentTicket->root_ticket_id ?? $parentTicket->id;
                // Chain-wide duplicate guard for intra-environment
                $alreadyInChain = \App\Models\TicketHead::where('root_ticket_id', $rootTicketId)
                    ->where('ticket_sub_menu_id', $targetSubmenuId)
                    ->where('is_generated', true)
                    ->exists();
                if ($alreadyInChain) {
                    return response()->json([
                        'code'    => 422,
                        'message' => 'Ticket untuk sub-menu ini sudah ada dalam chain yang sama. Generation dibatalkan untuk mencegah looping.',
                    ], 422);
                }
            }

            $nomorTicket = $this->generateNomorTicket(null, $targetSubmenuId);
            $draftTicket = TicketHead::create([
                'id'                       => $draftTicketId,
                'nomor_ticket'             => $nomorTicket,
                'ticket_menu_id'           => $request->ticket_menu_id,
                'ticket_sub_menu_id'       => $targetSubmenuId,
                'requestor_id'             => Auth::id(),
                'requestor_name'           => $user_karyawan->name,
                'ticket_status'            => TicketHead::STATUS_DRAFT,
                'approval_status'          => TicketHead::APPROVAL_DRAFT,
                'is_draft'                 => true,
                'is_submit'                => false,
                'is_generated'             => true,
                'is_cross_environment'     => $isCrossEnv,
                'cross_env_status'         => $isCrossEnv ? TicketHead::CROSS_ENV_TRIGGERED : TicketHead::CROSS_ENV_NONE,
                'origin_cross_ticket_id'   => $isCrossEnv ? $parentTicket->id : null,
                'root_ticket_id'           => $rootTicketId,
                'parent_ticket_id'         => $parentTicket->id,
                'generated_from_ticket_id' => $parentTicket->id,
                'dependency_rule_id'       => $approvalRuleId, // ← wajib ada, agar isIncomingEdge() berfungsi
                'created_at'               => now()->toDateTimeString(),
            ]);

            if ($isCrossEnv) {
                $parentTicket->update([
                    'cross_env_status' => TicketHead::CROSS_ENV_TRIGGERED,
                ]);
            }
        }
        $submissionService = app(\App\Services\TicketSubmissionService::class);
        $submittedTicket   = $submissionService->submitDraftGenerated($draftTicket, $request, $user_karyawan);
        $menuId = $request->current_menu_id ?: ($request->ticket_menu_id ?: ($request->menu_ticket ?: $submittedTicket->ticket_menu_id));
        app(TicketApprovalEmailService::class)->sendNextActionNotification(
            $submittedTicket,
            $menuId,
            'submitted'
        );
        $queue = session('ticket_generated_queue', []);
        if (!empty($queue)) {
            $next = array_shift($queue);
            session(['ticket_generated_queue' => $queue]);
            return response()->json([
                'code'         => 200,
                'message'      => 'Ticket berhasil diajukan. Melanjutkan ke ticket berikutnya...',
                'redirect_url' => isset($next['draft_ticket_id'])
                    ? route('ticket.tambah.generated.draft', ['draftTicketId' => $next['draft_ticket_id']])
                    : route('ticket.tambah.generated', [
                        'menuId'         => $next['menu_id'],
                        'parentTicketId' => $next['parent_ticket_id'],
                        'subMenuId'      => $next['submenu_id'],
                    ]),
            ]);
        }
        session()->forget('ticket_generated_queue');
        return response()->json([
            'code'         => 200,
            'message'      => 'Semua ticket berhasil diajukan.',
            'redirect_url' => route('ticket', $request->current_menu_id),
        ]);
    }

    private function storeLampiranMultiple(
        Request $request,
        string $ticketHeadId
    ): void {
        if (!$request->hasFile('lampiran')) {
            return;
        }
        foreach ($request->file('lampiran') as $file) {
            if (!$file->isValid()) {
                continue;
            }
            $originalName = $file->getClientOriginalName();
            $extension    = strtolower($file->getClientOriginalExtension());
            $mimeType     = $file->getMimeType();
            $fileSize     = $file->getSize();
            $fileName = (string) Uuid::uuid4() . '.' . $extension;
            $filePath = $file->storeAs(
                'ticket_attachment/' . $ticketHeadId,
                $fileName,
                'public'
            );
            TicketHeadAttachment::create([
                'id'               => (string) Uuid::uuid4(),
                'ticket_head_id'   => $ticketHeadId,
                'original_name'    => $originalName,
                'file_name'        => $fileName,
                'file_path'        => $filePath,
                'mime_type'        => $mimeType,
                'file_size'        => $fileSize,
                'uploaded_by'      => Auth::id(),
                'uploaded_by_name' => Auth::user()->username ?? null,
            ]);
        }
    }

    /**
     * Auto-generate a dependent ticket the instant a checker answers one of
     * their dependency conditions TRUE — used by
     * ApprovalController::saveCheckerTicket() so each checker generates their
     * own ticket(s) immediately when they finish their own review, instead of
     * waiting for every other checker on the ticket to finish first.
     *
     * The checker who triggered it becomes the requestor/submitter of the
     * newly created ticket (their own signature, captured moments earlier on
     * the checker page, is reused as the requestor signature).
     */
    public function autoGenerateFromChecker(
        TicketHead $parentTicket,
        Karyawan $checkerKaryawan,
        string $submenuId,
        ?string $menuId = null,
        ?string $signaturePath = null
    ): TicketHead {
        $subMenu = DB::table('ticket_sub_menu as tsm')
            ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
            ->where('tsm.id', $submenuId)
            ->select('tsm.*', 'tm.id as ticket_menu_id', 'tm.kategori as menu_kategori')
            ->first();
        if (!$subMenu) {
            throw new \Exception('Sub menu tujuan tidak ditemukan untuk generate ticket.');
        }

        $isCrossEnv = false;
        if ($parentTicket) {
            $parentMenu = $parentTicket->ticketMenu ?: DB::table('ticket_menu')->where('id', $parentTicket->ticket_menu_id)->first();
            if ($parentMenu && isset($subMenu->menu_kategori) && (int)($parentMenu->kategori ?? 0) !== (int)$subMenu->menu_kategori) {
                $isCrossEnv = true;
            }
        }

        $newTicketId   = (string) Uuid::uuid4();
        $rootTicketId  = $isCrossEnv ? $newTicketId : ($parentTicket->root_ticket_id ?? $parentTicket->id);
        $tempTicket    = new TicketHead([
            'ticket_sub_menu_id' => $submenuId,
            'root_ticket_id'     => $rootTicketId,
            'parent_ticket_id'   => $parentTicket->id,
        ]);

        $approval = $this->resolveApprovalData($submenuId, $checkerKaryawan, false, [], $tempTicket);

        $nomorTicket   = $this->generateNomorTicket(null, $submenuId);
        $initialStatus = $this->determineInitialStatus($approval, $submenuId);

        $newTicket = TicketHead::create([
            'id'                       => $newTicketId,
            'nomor_ticket'             => $nomorTicket,
            'ticket_menu_id'           => $subMenu->ticket_menu_id,
            'ticket_sub_menu_id'       => $submenuId,
            'requestor_id'             => Auth::id(),
            'requestor_name'           => $checkerKaryawan->name,
            'requestor_signature'      => $signaturePath,
            'ticket_status'            => $initialStatus,
            'approval_status'          => $approval['auto_approved']
                ? TicketHead::APPROVAL_APPROVED
                : TicketHead::APPROVAL_PENDING,
            'is_draft'                 => false,
            'is_submit'                => true,
            'is_generated'             => true,
            'is_cross_environment'     => $isCrossEnv,
            'cross_env_status'         => $isCrossEnv ? TicketHead::CROSS_ENV_TRIGGERED : TicketHead::CROSS_ENV_NONE,
            'origin_cross_ticket_id'   => $isCrossEnv ? $parentTicket->id : null,
            'approve1'                 => $approval['auto_approved'] ? true : null,
            'approve1_id'              => $approval['approve1_id'],
            'approve1_name'            => $approval['approve1_name'],
            'approve1_at'              => $approval['auto_approved'] ? now() : null,
            'approve2'                 => $approval['auto_approved'] ? true : null,
            'approve2_id'              => $approval['approve2_id'],
            'approve2_name'            => $approval['approve2_name'],
            'approve2_at'              => $approval['auto_approved'] ? now() : null,
            'checked'                  => 0,
            'checker_id'               => $approval['checker_id'],
            'checker_name'             => $approval['checker_name'],
            'validator'                => !empty($approval['auto_validated']) ? true : null,
            'validator_id'             => !empty($approval['auto_validated']) ? $checkerKaryawan->id : null,
            'validator_name'           => !empty($approval['auto_validated']) ? $checkerKaryawan->name : null,
            'validator_at'             => !empty($approval['auto_validated']) ? now() : null,
            'validator_signature'      => !empty($approval['auto_validated']) ? $signaturePath : null,
            'execution_status'         => !empty($approval['auto_validated']) ? TicketHead::EXEC_READY : TicketHead::EXEC_WAITING,
            'root_ticket_id'           => $rootTicketId,
            'parent_ticket_id'         => $parentTicket->id,
            'created_at'               => now()->toDateTimeString(),
        ]);
        if ($isCrossEnv) {
            $parentTicket->update([
                'cross_env_status' => TicketHead::CROSS_ENV_TRIGGERED,
            ]);
        }
        $this->syncTicketCheckers($newTicketId, $approval['checkers'] ?? []);

        ActivityLog::create([
            'user_id'          => Auth::id(),
            'user_name'        => Auth::user()->username ?? $checkerKaryawan->name,
            'menu_id'          => null,
            'menu_name'        => 'Requestor Ticket Head',
            'id_table'         => $newTicketId,
            'holding_id'       => '-',
            'holding_category' => '-',
            'table'            => 'ticket_head',
            'activity'         => 'Mengajukan Ticket (Auto-Generated oleh Checker)',
            'description'      => 'Ticket ' . $nomorTicket . ' otomatis digenerate oleh checker ' . $checkerKaryawan->name . ' dari Ticket: ' . $parentTicket->nomor_ticket,
            'keterangan'       => 'Success',
            'karir'            => '2',
        ]);

        app(TicketApprovalEmailService::class)->sendNextActionNotification(
            $newTicket,
            $menuId,
            'submitted'
        );

        return $newTicket;
    }

    /**
     * Public wrapper for the TicketGenerationService. Resolves the target
     * sub-menu (menu + sub-menu) for a ticket to be generated.
     */
    public function resolveSubMenuPublic(string $submenuId): ?object
    {
        return DB::table('ticket_sub_menu as tsm')
            ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
            ->where('tsm.id', $submenuId)
            ->select('tsm.*', 'tm.id as ticket_menu_id')
            ->first();
    }

    /**
     * Public wrapper for the TicketGenerationService. Resolves approval data
     * (approvers + checker) for a generated ticket.
     */
    public function resolveApprovalDataPublic(string $submenuId, Karyawan $requestorKaryawan, bool $includeBackward = false, array $selectedConditions = [], ?TicketHead $ticket = null): array
    {
        return $this->resolveApprovalData($submenuId, $requestorKaryawan, $includeBackward, $selectedConditions, $ticket);
    }

    /**
     * Public wrapper for the TicketGenerationService. Generates the ticket
     * number for a generated ticket.
     */
    public function generateNomorTicketPublic(?int $ticketType, ?string $submenuId = null): string
    {
        return $this->generateNomorTicket($ticketType, $submenuId);
    }

    /**
     * Public wrapper for the TicketGenerationService. Determines the initial
     * ticket status for a generated ticket.
     */
    public function determineInitialStatusPublic(array $approval, ?string $submenuId = null): string
    {
        return $this->determineInitialStatus($approval, $submenuId);
    }

    /**
     * Public wrapper for the TicketGenerationService. Syncs checker approvals
     * for a generated ticket.
     */
    public function syncTicketCheckersPublic(string $ticketId, array $checkers): void
    {
        $this->syncTicketCheckers($ticketId, $checkers);
    }

    public function storeSignaturePublic(Request $request): ?string
    {
        return $this->storeSignature($request);
    }

    public function storeLampiranPublic(Request $request): ?string
    {
        return $this->storeLampiran($request);
    }

    public function saveTicketDetailsPublic(string $ticketId, Request $request): void
    {
        $this->saveTicketDetails($ticketId, $request);
    }

    public function generateNomorTicket(?int $ticketType, ?string $submenuId = null): string
    {
        if (is_null($ticketType) && $submenuId) {
            $subMenu     = DB::table('ticket_sub_menu as tsm')
                ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
                ->where('tsm.id', $submenuId)
                ->select('tm.kategori')
                ->first();
            $ticketType = $subMenu->kategori ?? 1;
        }
        $kodeMap    = [
            1 => 'EPIC-SPS',
            2 => 'AOL-SP',
            3 => 'AOL-AJA',
        ];
        $kode       = $kodeMap[$ticketType] ?? 'EPIC-SPS';
        $prefix     = "F/UHD/{$kode}/" . date('ym') . '/';
        $lastNumber = TicketHead::where('nomor_ticket', 'like', $prefix . '%')->count();
        return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    public function storeLampiranMultiplePublic(Request $request, string $ticketHeadId): void
    {
        $this->storeLampiranMultiple($request, $ticketHeadId);
    }

    public function storeLampiran(Request $request): ?string
    {
        if (!$request->hasFile('lampiran')) {
            return null;
        }
        $file = $request->file('lampiran');
        if (is_array($file)) {
            $file = !empty($file) ? reset($file) : null;
        }
        if (!$file || !$file->isValid()) {
            return null;
        }
        $filename = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('lampiran_tickets', $filename, 'public');
    }

    public function storeSignature(Request $request): ?string
    {
        if (!$request->filled('signature')) {
            return null;
        }
        $signature = $request->signature;
        if (!preg_match('/^data:image\/png;base64,/', $signature)) {
            return null;
        }
        $image   = substr($signature, strpos($signature, ',') + 1);
        $decoded = base64_decode($image, true);
        if ($decoded === false) {
            throw new \Exception('Signature tidak valid.');
        }
        $path = 'signatures/signature_' . uniqid() . '.png';
        Storage::disk('public')->put($path, $decoded);
        return $path;
    }

    public function saveTicketDetails(string $ticketId, Request $request): void
    {
        $details = $request->input('details');
        if (empty($details) || !is_array($details)) {
            return;
        }

        $request->validate([
            'details' => ['required', 'array', 'min:1',],
            // 'details.*.line_number' => [ 'required', ], 
            'details.*.nomor_transaksi' => ['required_without:details.*.nomor_detail', 'nullable', 'string',],
            'details.*.nomor_detail' => ['required_without:details.*.nomor_transaksi', 'nullable', 'string',],
            'details.*.tanggal_transaksi' => ['required', 'date',],
            'details.*.sub_details' => ['required', 'array', 'min:1',],
            'details.*.sub_details.*.line_number' => ['required',],
            'details.*.sub_details.*.alasan_ubah' => ['required', 'string',],
            'details.*.sub_details.*.before_ubah' => ['required', 'string',],
            'details.*.sub_details.*.after_ubah' => ['required', 'string',],
        ], [
            'details.required' => 'Detail transaksi wajib diisi.',
            'details.min' => 'Minimal harus ada 1 detail transaksi.',
            // 'details.*.line_number.required' => 'Nomor Line/Keterangan detail wajib diisi.', 
            'details.*.nomor_transaksi.required_without' => 'Nomor transaksi wajib diisi.',
            'details.*.nomor_detail.required_without' => 'Nomor detail wajib diisi.',
            'details.*.tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'details.*.tanggal_transaksi.date' => 'Tanggal transaksi tidak valid.',
            'details.*.sub_details.required' => 'Sub detail wajib diisi.',
            'details.*.sub_details.min' => 'Minimal harus ada 1 sub detail.',
            'details.*.sub_details.*.line_number.required' => 'Nomor Line/Keterangan wajib diisi.',
            'details.*.sub_details.*.alasan_ubah.required' => 'Alasan perubahan wajib diisi.',
            'details.*.sub_details.*.before_ubah.required' => 'Nilai sebelum perubahan wajib diisi.',
            'details.*.sub_details.*.after_ubah.required' => 'Nilai setelah perubahan wajib diisi.',
        ]);

        // Clean up any existing details/subdetails on this ticket (e.g. from preliminary draft cloning)
        $existingDetails = TicketDetail::where('ticket_id', $ticketId)->get();
        foreach ($existingDetails as $ed) {
            TicketSubDetail::where('ticket_detail_id', $ed->id)->forceDelete();
            $ed->forceDelete();
        }

        foreach ($details as $detailData) {
            $detail = TicketDetail::create([
                'id'                         => (string) Uuid::uuid4(),
                'ticket_id'                  => $ticketId,
                'line_number'                => $detailData['line_number'] ?? null,
                'nomor_detail'               => $detailData['nomor_transaksi'] ?? ($detailData['nomor_detail'] ?? ''),
                'tanggal_transaksi'          => !empty($detailData['tanggal_transaksi']) ? $detailData['tanggal_transaksi'] : null,
                'reference_ticket_detail_id' => !empty($detailData['reference_ticket_detail_id']) ? $detailData['reference_ticket_detail_id'] : null,
                'created_at'                 => now(),
            ]);
            if (!empty($detailData['sub_details']) && is_array($detailData['sub_details'])) {
                foreach ($detailData['sub_details'] as $subData) {
                    TicketSubDetail::create([
                        'id'               => (string) Uuid::uuid4(),
                        'ticket_detail_id' => $detail->id,
                        'line_number'      => $subData['line_number'] ?? '',
                        'alasan_ubah'      => $subData['alasan_ubah'] ?? '',
                        'before_ubah'      => $subData['before_ubah'] ?? '',
                        'after_ubah'       => $subData['after_ubah'] ?? '',
                        'created_at'       => now(),
                    ]);
                }
            }
        }
    }

    private function findApprovalFlow(string $submenuId): ?TicketApprovalFlow
    {
        return TicketApprovalFlow::with(['steps' => function ($q) {
            $q->orderBy('step_order');
        }])
            ->whereHas('subMenus', fn($q) => $q->where('ticket_sub_menu.id', $submenuId))
            ->first();
    }

    private function findRequestorStep($steps, Karyawan $requestorKaryawan): ?TicketApprovalStep
    {
        $stepIds = $steps->pluck('id')->toArray();

        // 1. Cek apakah karyawan pengaju terdaftar (dichecklist) di tabel ticket_approval_karyawan pada step-step flow ini
        $pivotRecords = DB::table('ticket_approval_karyawan')
            ->whereIn('approval_step_id', $stepIds)
            ->where('karyawan_id', $requestorKaryawan->id)
            ->get();

        if ($pivotRecords->isEmpty()) {
            return null; // Karyawan belum dichecklist / tidak terdaftar di step mana pun pada flow ini
        }

        // Jika karyawan terdaftar di beberapa step (misal dual jabatan),
        // pilih step dengan step_order tertinggi (posisi level terbawah / requestor level)
        $matchedSteps = $steps->whereIn('id', $pivotRecords->pluck('approval_step_id'));
        return $matchedSteps->sortByDesc('step_order')->first();
    }

    private function isTicketDone(TicketHead $ticket): bool
    {
        return in_array((string) $ticket->ticket_status, [
            TicketHead::STATUS_WAITING_REQUESTOR,
            TicketHead::STATUS_CLOSED,
            TicketHead::STATUS_CANCELLED,
        ], true)
            || in_array(strtolower((string) $ticket->ticket_status), ['done', 'selesai'], true)
            || $ticket->execution_status === TicketHead::EXEC_DONE
            || !is_null($ticket->finish_at);
    }

    private function saveTicketComment(
        string $ticketId,
        string $level,
        ?Karyawan $karyawan,
        string $action,
        ?string $commentText
    ): void {
        if (!$karyawan || empty(trim((string) $commentText))) {
            return;
        }
        $jabatan = KaryawanJabatan::where('karyawan_id', $karyawan->id)
            ->where('is_active', 1)
            ->where('main', 1)
            ->first();
        TicketApprovalComment::create([
            'id'             => (string) Uuid::uuid4(),
            'ticket_id'      => $ticketId,
            'approval_level' => $level,
            'karyawan_id'    => $karyawan->id,
            'jabatan_id'     => $jabatan?->id,
            'action_ticket'  => $action,
            'comment_text'   => trim($commentText),
            'comment_at'     => now(),
        ]);
    }

    private function ticketStatusLabel(?string $status): string
    {
        return [
            TicketHead::STATUS_DRAFT              => 'Draft',
            TicketHead::STATUS_SUBMITTED          => 'Menunggu Persetujuan',
            TicketHead::STATUS_APPROVED           => 'Disetujui',
            TicketHead::STATUS_ASSIGNED           => 'Ditugaskan',
            TicketHead::STATUS_IN_PROGRESS        => 'Dikerjakan',
            TicketHead::STATUS_WAITING_REQUESTOR  => 'Menunggu Respon Peminta',
            TicketHead::STATUS_CLOSED             => 'Selesai',
            TicketHead::STATUS_FEEDBACK_REQUESTOR => 'Respon Balasan Peminta',
            TicketHead::STATUS_CANCELLED          => 'Dibatalkan',
            TicketHead::STATUS_WAITING_APPROVAL1  => 'Disetujui Atasan 1',
            TicketHead::STATUS_WAITING_APPROVAL2  => 'Disetujui Atasan 2',
            TicketHead::STATUS_WAITING_VALIDATOR  => 'Menunggu Divalidasi',
            TicketHead::STATUS_WAITING_CHECKER    => 'Menunggu Pemeriksaan',
            TicketHead::STATUS_WAITING_ASSIGNER   => 'Menunggu Penugasan',
            TicketHead::STATUS_AUTOMATIC_CLOSE    => 'Ditutup Otomatis',
        ][$status] ?? '-';
    }

    private function resolveKaryawanFromUser(?User $user): ?Karyawan
    {
        if (!$user || !$user->karyawan_id) {
            return null;
        }
        $identifier = (string) $user->karyawan_id;
        return Karyawan::where(function ($query) use ($identifier) {
            if (Uuid::isValid($identifier)) {
                $query->where('id', $identifier)
                    ->orWhere('nomor_identitas_karyawan', $identifier);
                return;
            }
            $query->where('nomor_identitas_karyawan', $identifier);
        })->first();
    }

    private function currentKaryawan(): ?Karyawan
    {
        return $this->resolveKaryawanFromUser(Auth::user());
    }

    /**
     * Semua id (karyawan_id + user_id terkait) yang bisa dipakai untuk
     * mencocokkan kepemilikan approval/checker milik user yang sedang login.
     * Duplikat dari ApprovalController::currentApprovalSubjectIds() supaya
     * TicketController tidak perlu bergantung ke controller lain.
     */
    private function currentApprovalSubjectIds(): array
    {
        $karyawan = $this->currentKaryawan();
        $ids      = [];
        if ($karyawan) {
            $ids[] = (string) $karyawan->id;
            $userIds = User::where(function ($query) use ($karyawan) {
                $query->whereRaw('karyawan_id::text = ?', [(string) $karyawan->id]);
                if ($karyawan->nomor_identitas_karyawan) {
                    $query->orWhereRaw('karyawan_id::text = ?', [(string) $karyawan->nomor_identitas_karyawan]);
                }
            })->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $ids = array_merge($ids, $userIds);
        }
        if (!$karyawan && Auth::id()) {
            $ids[] = (string) Auth::id();
        }
        return array_values(array_unique(array_filter($ids)));
    }

    private const STEP_TO_JABATAN_COLUMN = [
        'department_id' => 'departemen_id',
        'divisi_id'     => 'divisi_id',
        'bagian_id'     => 'bagian_id',
        'jabatan_id'    => 'jabatan_id',
    ];

    private function isValidator(Karyawan $karyawan): bool
    {
        return TicketApprovalValidator::where('karyawan_id', $karyawan->id)
            ->where('is_active', true)
            ->exists();
    }

    private function isStepUsable(?TicketApprovalStep $step): bool
    {
        if (!$step) {
            return false;
        }
        $hasPivotKaryawan = DB::table('ticket_approval_karyawan')
            ->where('approval_step_id', $step->id)
            ->exists();
        if ($hasPivotKaryawan) {
            return true;
        }
        foreach (self::STEP_TO_JABATAN_COLUMN as $stepCol => $jabatanCol) {
            if (!is_null($step->{$stepCol})) {
                return true;
            }
        }
        return false;
    }

    private function resolveApprovers(TicketApprovalFlow $flow, Karyawan $requestorKaryawan, bool $hasChecker = false): array
    {
        $steps = $flow->steps->sortBy('step_order')->values();
        if ($steps->isEmpty()) {
            return ['found' => false];
        }

        if ($this->isValidator($requestorKaryawan)) {
            $root = $steps->firstWhere('parent_step_id', null);
            $chain = collect();
            $current = $root;
            while ($current) {
                $chain->push($current);
                $current = $steps->firstWhere('parent_step_id', $current->id);
            }
            $chain = $chain->reverse()->values();
            $app1Step = $chain[0] ?? null;
            $app2Step = $chain[1] ?? null;

            $app1Data = $app1Step ? $this->getKaryawanNamesForStep($app1Step) : ['karyawan_ids' => [], 'karyawan_names' => []];
            $app2Data = $app2Step ? $this->getKaryawanNamesForStep($app2Step) : ['karyawan_ids' => [], 'karyawan_names' => []];

            return [
                'found'              => true,
                'approve1_id'         => null,
                'approve1_name'       => null,
                'approve1_ids'        => [],
                'approve1_names'      => [],
                'approve1_step_order' => null,
                'approve1_step_name'  => null,
                'approve2_id'         => null,
                'approve2_name'       => null,
                'approve2_ids'        => [],
                'approve2_names'      => [],
                'approve2_step_order' => null,
                'approve2_step_name'  => null,
                'auto_approved'      => true,
            ];
        }

        // 1. Cek apakah karyawan pengaju terdaftar (dichecklist) di tabel ticket_approval_karyawan pada alur ini
        $myStep = $this->findRequestorStep($steps, $requestorKaryawan);
        if (!$myStep) {
            // Jika belum dichecklist / tidak terdaftar di step mana pun pada flow ini -> Block submit
            return ['found' => false];
        }

        // 2. Karyawan terdaftar. Cari atasan langsung (approve1) dan atasan level 2 (approve2)
        $approve1Step = $myStep->parent_step_id
            ? $steps->firstWhere('id', $myStep->parent_step_id)
            : null;
        $approve2Step = $approve1Step?->parent_step_id
            ? $steps->firstWhere('id', $approve1Step->parent_step_id)
            : null;

        if (!$this->isStepUsable($approve1Step)) {
            $approve1Step = $approve2Step;
            $approve2Step = null;
        }
        if (!$this->isStepUsable($approve2Step)) {
            $approve2Step = null;
        }

        $app1Data = $approve1Step ? $this->getKaryawanNamesForStep($approve1Step) : ['karyawan_ids' => [], 'karyawan_names' => []];
        $app2Data = $approve2Step ? $this->getKaryawanNamesForStep($approve2Step) : ['karyawan_ids' => [], 'karyawan_names' => []];

        $requestorId = (string) $requestorKaryawan->id;

        // Cek apakah requestor juga terdaftar di step atasan di atasnya (dual jabatan)
        $isRequestorApprover1 = in_array($requestorId, array_map('strval', $app1Data['karyawan_ids'] ?? []), true)
            || $this->requestorMatchesStep($requestorKaryawan, $approve1Step);
        $isRequestorApprover2 = in_array($requestorId, array_map('strval', $app2Data['karyawan_ids'] ?? []), true)
            || $this->requestorMatchesStep($requestorKaryawan, $approve2Step);

        // 3. Evaluasi Atasan & Kondisi Auto-Approve:
        $autoApproved = false;

        if (!$approve1Step && !$approve2Step) {
            // Karyawan terdaftar pada flow ini dan step miliknya adalah posisi paling atas (tidak ada atasan lagi)
            // -> Memenuhi kondisi Auto Approve (Atasan Tertinggi)
            $autoApproved = true;
        } elseif ($approve2Step && $isRequestorApprover2) {
            // Requestor adalah atasan level 2 (tertinggi) -> Auto Approve
            $autoApproved = true;
        } elseif (!$approve2Step && $isRequestorApprover1) {
            // Hanya ada 1 level atasan dan requestor adalah atasan tersebut -> Auto Approve
            $autoApproved = true;
        } elseif ($approve2Step && $isRequestorApprover1 && empty($app2Data['karyawan_ids'])) {
            // Requestor adalah approve1 dan approve2 step tidak punya karyawan aktif -> Auto Approve
            $autoApproved = true;
        } elseif ($approve1Step && empty($app1Data['karyawan_ids'])) {
            // Ada step atasan di atasnya, tapi step atasan tersebut belum diisi/dichecklist karyawannya
            return ['found' => false];
        }

        if ($autoApproved) {
            Log::info('[resolveApprovers] Auto-approve: requestor is registered at highest level or approver', [
                'requestor_id'   => $requestorId,
                'requestor_name' => $requestorKaryawan->name,
                'step_name'      => $myStep->step_name,
                'step_order'     => $myStep->step_order,
            ]);
        }

        return [
            'found'               => true,
            'approve1_id'         => $app1Data['karyawan_ids'][0] ?? null,
            'approve1_name'       => $app1Data['karyawan_names'][0] ?? null,
            'approve1_ids'        => $app1Data['karyawan_ids'],
            'approve1_names'      => $app1Data['karyawan_names'],
            'approve1_step_order' => $approve1Step?->step_order,
            'approve1_step_name'  => $approve1Step?->step_name,
            'approve2_id'         => $app2Data['karyawan_ids'][0] ?? null,
            'approve2_name'      => $app2Data['karyawan_names'][0] ?? null,
            'approve2_ids'        => $app2Data['karyawan_ids'],
            'approve2_names'      => $app2Data['karyawan_names'],
            'approve2_step_order' => $approve2Step?->step_order,
            'approve2_step_name'  => $approve2Step?->step_name,
            'auto_approved'       => $autoApproved,
        ];
    }

    private function resolveApprovalData(string $submenuId, Karyawan $requestorKaryawan, bool $includeBackward = false, array $selectedConditions = [], ?TicketHead $ticket = null): array
    {
        $flow = $this->findApprovalFlow($submenuId);
        if (!$flow) {
            throw new \Exception('Approval flow tidak ditemukan untuk sub-menu ini.');
        }

        $checkers = $this->resolveMultiCheckers($submenuId, $includeBackward, $selectedConditions, $ticket);
        $internalCheckers = collect($checkers)->filter(fn($c) => empty($c['is_cross_environment']))->values();
        $primaryChecker = $internalCheckers->isNotEmpty() ? $internalCheckers[0] : ['id' => null, 'name' => null];

        $approvers = $this->resolveApprovers($flow, $requestorKaryawan, !empty($primaryChecker['id']));

        if (!$approvers['found']) {
            throw new \Exception('Anda belum terdaftar ke menu pengajuan ubah hapus atau atasan langsung tidak ditemukan pada alur persetujuan. Silakan hubungi Administrator.');
        }

        $isValidatorUser = $this->isValidator($requestorKaryawan);
        $isAutoApproved  = !empty($approvers['auto_approved']);
        $hasChecker      = !empty($primaryChecker['id']) || !empty($checkers);
        $isAutoValidated = $isValidatorUser && $isAutoApproved && !$hasChecker;

        return [
            'approve1_id'    => $approvers['approve1_id'],
            'approve1_name'  => $approvers['approve1_name'],
            'approve2_id'    => $approvers['approve2_id'],
            'approve2_name'  => $approvers['approve2_name'],
            'checker_id'     => $primaryChecker['id'],
            'checker_name'   => $primaryChecker['name'],
            'checkers'       => $checkers,
            'auto_approved'  => $isAutoApproved,
            'auto_validated' => $isAutoValidated,
        ];
    }

    private function determineInitialStatus(array $approval, ?string $submenuId = null): string
    {
        if (!empty($approval['auto_validated'])) {
            return TicketHead::STATUS_WAITING_ASSIGNER;
        }
        if (!empty($approval['auto_approved'])) {
            $hasChecker = !empty($approval['checker_id']) || !empty($approval['checkers']);
            if ($hasChecker) {
                return TicketHead::STATUS_WAITING_CHECKER;
            }
            // Auto-approve (approver tertinggi) + tidak ada checker -> langsung menunggu validator (T11).
            // Hanya jatuh ke APPROVED jika memang tidak ada validator aktif di master.
            return $this->hasActiveValidatorForSubmenu($submenuId)
                ? TicketHead::STATUS_WAITING_VALIDATOR
                : TicketHead::STATUS_APPROVED;
        }
        return TicketHead::STATUS_SUBMITTED;
    }

    /**
     * Cek apakah ada validator aktif untuk environment sub-menu ini.
     * Logikanya disamakan dengan TicketChainService::hasValidatorInMaster()
     * supaya status awal konsisten dengan sinkronisasi status grup.
     */
    private function hasActiveValidatorForSubmenu(?string $submenuId): bool
    {
        $environment = null;
        if ($submenuId) {
            $environment = DB::table('ticket_sub_menu as tsm')
                ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
                ->where('tsm.id', $submenuId)
                ->value('tm.kategori');
        }
        $hasValidator = TicketApprovalValidator::where('is_active', true)
            ->where(function ($query) use ($environment) {
                $query->whereNull('environment');
                if (!is_null($environment)) {
                    $query->orWhere('environment', $environment);
                }
            })
            ->exists();

        if (!$hasValidator) {
            $hasValidator = TicketApprovalValidator::where('is_active', true)->exists();
        }
        return $hasValidator;
    }

    private function syncTicketCheckers(string $ticketId, array $checkers): void
    {
        TicketApproval::where('ticket_id', $ticketId)->where('role', 'checker')->delete();

        foreach ($checkers as $chk) {
            TicketApproval::create([
                'ticket_id'          => $ticketId,
                'karyawan_id'        => $chk['id'],
                'karyawan_name'      => $chk['name'],
                'flow_step_id'       => $chk['step_id'] ?? null,
                'branch_sub_menu_id' => $chk['branch_sub_menu_id'] ?? null,
                'dependency_rule_id' => $chk['rule_id'] ?? null,
                'role'               => 'checker',
                'status'             => 'pending',
                'generate_status'    => TicketGenerationService::PENDING,
                'step_order'         => 1,
            ]);
        }
    }

    /**
     * Resolve all checkers across all dependency branches for a given submenu.
     * Extracts Step 2 (Supervisor level) from each branch's approval flow.
     */
    private function resolveMultiCheckers(string $submenuId, bool $includeBackward = false, array $selectedConditions = [], ?TicketHead $ticket = null): array
    {
        $forwardRules = TicketDependencyRule::where('source_sub_menu_id', $submenuId)
            ->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->get();

        $backwardRules = collect();
        if ($includeBackward) {
            $backwardRules = TicketDependencyRule::where('target_sub_menu_id', $submenuId)
                ->where('is_bidirectional', true)
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->get();
        } else {
            // Selalu sertakan rule cross-environment 2 arah (bidirectional) yang menargetkan submenu ini
            $backwardRules = TicketDependencyRule::where('target_sub_menu_id', $submenuId)
                ->where('is_bidirectional', true)
                ->where('is_cross_environment', true)
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->get();
        }

        $engine = app(\App\Services\DependencyEngine::class);

        // isValidRuleConfig SELALU dijalankan — bukan hanya saat ada $ticket.
        // Sebelumnya, saat $ticket = null (Add Ticket baru / preview), semua rule
        // lolos tanpa validasi config sehingga rule incomplete menghasilkan ghost checker.
        // Ticket-context checks (isIncomingEdge, detectLoop) tetap hanya berjalan
        // jika $ticket tersedia.
        $forwardRules = $forwardRules->filter(function ($rule) use ($engine, $ticket) {
            if (!$engine->isValidRuleConfig($rule)) return false;
            if ($ticket) {
                if ($engine->isIncomingEdge($ticket, $rule)) return false;
                $targetId = $engine->getTargetSubMenuId($rule, $ticket->ticket_sub_menu_id);
                if (!$targetId || $engine->detectLoop($ticket, $targetId)) return false;
            }
            return true;
        });

        $backwardRules = $backwardRules->filter(function ($rule) use ($engine, $ticket) {
            if (!$engine->isValidRuleConfig($rule)) return false;
            if ($ticket) {
                if ($engine->isIncomingEdge($ticket, $rule)) return false;
                $targetId = $engine->getTargetSubMenuId($rule, $ticket->ticket_sub_menu_id);
                if (!$targetId || $engine->detectLoop($ticket, $targetId)) return false;
            }
            return true;
        });

        if ($forwardRules->isEmpty() && $backwardRules->isEmpty()) {
            return [];
        }

        $checkers = [];
        // Dedupe on "karyawan_id|branch_sub_menu_id", NOT karyawan_id alone.
        // The same person can legitimately be the resolved checker for more
        // than one branch on the same submenu (e.g. both the mandatory next
        // step and a separate conditional branch) — each is a distinct
        // TicketApproval row so they get an independent condition to toggle
        // for each branch they own.
        $seenPairs = [];

        // Forward dependency branches
        foreach ($forwardRules as $rule) {
            // is_conditional = false → mandatory/required flow, always included.
            // is_conditional = true  → only included if the matching toggle in
            // selected_conditions wasn't explicitly turned off.
            $isCond  = (bool)($rule->is_conditional ?? false);
            $isCross = (bool)($rule->is_cross_environment ?? false);
            if (($isCond || $isCross) && !empty($selectedConditions)) {
                $key = !empty($rule->condition_key) ? $rule->condition_key : $rule->id;
                $val = $selectedConditions[$key] ?? ($selectedConditions[$rule->id] ?? null);
                if ($val === "0" || $val === 0 || $val === false || $val === "false") {
                    continue;
                }
            }
            $targetSubmenuId = $rule->target_sub_menu_id;
            $targetSubMenu = TicketSubMenu::with('ticketMenu')->find($targetSubmenuId);
            $branchName = $targetSubMenu
                ? ($targetSubMenu->ticketMenu?->nama_menu . ' › ' . $targetSubMenu->nama_sub_menu)
                : 'Branch';
            $targetFlow = TicketApprovalFlow::with('steps')
                ->whereHas('subMenus', fn($q) => $q->where('ticket_sub_menu.id', $targetSubmenuId))
                ->first();
            if (!$targetFlow) continue;
            $steps = $targetFlow->steps()->orderBy('step_order')->get();
            if ($steps->isEmpty()) continue;

            $checkerStep = $this->pickCheckerStep($steps, false);
            if (!$checkerStep) {
                continue;
            }
            $userData = $this->findUserByStep($checkerStep);
            $karyawanIds = $userData['karyawan_ids'] ?? ($userData['id'] ? [$userData['id']] : []);
            $karyawanNames = $userData['karyawan_names'] ?? ($userData['name'] ? [$userData['name']] : []);

            $targetKategori = (int) ($targetSubMenu?->ticketMenu?->kategori ?? 1);
            $targetKategoriName = $targetKategori === 2 ? 'Accurate SP' : ($targetKategori === 3 ? 'Accurate AJA' : 'Epicor ERP');
            $targetMenuName = $targetSubMenu?->ticketMenu?->nama_menu ?? '';
            $targetSubmenuName = $targetSubMenu?->nama_sub_menu ?? '';

            foreach ($karyawanIds as $idx => $kId) {
                if (!$kId) continue;
                $pairKey = $kId . '|' . $targetSubmenuId;
                if (in_array($pairKey, $seenPairs, true)) continue;
                $seenPairs[] = $pairKey;
                $kName = $karyawanNames[$idx] ?? $userData['name'] ?? 'Checker';

                $checkers[] = [
                    'role'                 => 'checker',
                    'label'                => 'Checker',
                    'id'                   => $kId,
                    'name'                 => $kName,
                    'karyawan_id'          => $kId,
                    'karyawan_name'        => $kName,
                    'karyawan_ids'         => $karyawanIds,
                    'karyawan_names'       => $karyawanNames,
                    'step_order'           => $checkerStep->step_order,
                    'step_name'            => $checkerStep->step_name,
                    'step_id'              => $checkerStep->id,
                    'branch_sub_menu_id'   => $targetSubmenuId,
                    'branch_name'          => $branchName,
                    'target_menu_name'     => $targetMenuName,
                    'target_submenu_name'  => $targetSubmenuName,
                    'target_kategori'      => $targetKategori,
                    'target_kategori_name' => $targetKategoriName,
                    'is_reverse'           => false,
                    // Metadata dependency so submit flow can create the matching
                    // TicketConditionValue (active dependency) for this checker.
                    'condition_key'        => $rule->condition_key,
                    'is_conditional'       => (bool)($rule->is_conditional ?? false),
                    'is_cross_environment' => (bool)($rule->is_cross_environment ?? false),
                    'rule_id'              => $rule->id,
                ];
            }
        }

        // =========================================================================
        // [LOGIKA ALUR MUNDUR / REVERSE DEPENDENCY BRANCHES]
        // Menentukan daftar checker untuk cabang alur mundur (misal: Revisi / Reversal).
        // Pada alur reverse, tujuan transaksi dibalik ke $rule->source_sub_menu_id.
        // =========================================================================
        foreach ($backwardRules as $rule) {
            $isCond  = (bool)($rule->is_conditional ?? false);
            $isCross = (bool)($rule->is_cross_environment ?? false);
            if (($isCond || $isCross) && !empty($selectedConditions)) {
                $key = !empty($rule->condition_key) ? $rule->condition_key : $rule->id;
                $val = $selectedConditions[$key] ?? ($selectedConditions[$rule->id] ?? null);
                if ($val === "0" || $val === 0 || $val === false || $val === "false") {
                    continue;
                }
            }
            $reverseSubmenuId = $rule->source_sub_menu_id;
            $reverseSubMenu = TicketSubMenu::with('ticketMenu')->find($reverseSubmenuId);
            $branchName = $reverseSubMenu
                ? ('[Revisi] ' . $reverseSubMenu->ticketMenu?->nama_menu . ' › ' . $reverseSubMenu->nama_sub_menu)
                : '[Revisi] Reverse Branch';

            $reverseKategori = (int) ($reverseSubMenu?->ticketMenu?->kategori ?? 1);
            $reverseKategoriName = $reverseKategori === 2 ? 'Accurate SP' : ($reverseKategori === 3 ? 'Accurate AJA' : 'Epicor ERP');
            $reverseMenuName = $reverseSubMenu?->ticketMenu?->nama_menu ?? '';
            $reverseSubmenuName = $reverseSubMenu?->nama_sub_menu ?? '';

            $targetFlow = TicketApprovalFlow::with('steps')
                ->whereHas('subMenus', fn($q) => $q->where('ticket_sub_menu.id', $reverseSubmenuId))
                ->first();

            if (!$targetFlow) continue;

            $steps = $targetFlow->steps()->orderBy('step_order')->get();
            if ($steps->isEmpty()) continue;

            $checkerStep = $this->pickCheckerStep($steps, true);
            if (!$checkerStep) continue;

            $userData = $this->findUserByStep($checkerStep);
            $karyawanIds = $userData['karyawan_ids'] ?? ($userData['id'] ? [$userData['id']] : []);
            $karyawanNames = $userData['karyawan_names'] ?? ($userData['name'] ? [$userData['name']] : []);

            foreach ($karyawanIds as $idx => $kId) {
                if (!$kId) continue;
                $pairKey = $kId . '|' . $reverseSubmenuId;
                if (in_array($pairKey, $seenPairs, true)) continue;
                $seenPairs[] = $pairKey;
                $kName = $karyawanNames[$idx] ?? $userData['name'] ?? 'Checker';

                $checkers[] = [
                    'role'                 => 'checker',
                    'label'                => 'Checker',
                    'id'                   => $kId,
                    'name'                 => $kName,
                    'karyawan_id'          => $kId,
                    'karyawan_name'        => $kName,
                    'karyawan_ids'         => $karyawanIds,
                    'karyawan_names'       => $karyawanNames,
                    'step_order'           => $checkerStep->step_order,
                    'step_name'            => $checkerStep->step_name,
                    'step_id'              => $checkerStep->id,
                    'branch_sub_menu_id'   => $reverseSubmenuId,
                    'branch_name'          => $branchName,
                    'target_menu_name'     => $reverseMenuName,
                    'target_submenu_name'  => $reverseSubmenuName,
                    'target_kategori'      => $reverseKategori,
                    'target_kategori_name' => $reverseKategoriName,
                    'is_reverse'           => true,
                    // Metadata dependency (reverse/bidirectional) so submit flow
                    // can create the matching TicketConditionValue for this checker.
                    'condition_key'        => $rule->condition_key,
                    'is_conditional'       => (bool)($rule->is_conditional ?? false),
                    'is_cross_environment' => (bool)($rule->is_cross_environment ?? false),
                    'rule_id'              => $rule->id,
                ];
            }
        }

        return $checkers;
    }

    private function pickCheckerStep($steps, bool $reverse)
    {
        // a. return $steps->firstWhere('is_checker', true) jika ada
        $marked = $steps->firstWhere('is_checker', true);
        if ($marked) {
            return $marked;
        }

        // b. jika tidak ada, jalankan logika LAMA persis seperti sekarang
        $level4Steps = $steps->filter(function ($step) {
            if (!$step->jabatan_level_id) {
                return false;
            }
            $jm = \App\Models\JabatanMaster::with('LevelJabatan')->find($step->jabatan_level_id);
            return $jm && $jm->LevelJabatan && (int) $jm->LevelJabatan->level_jabatan === 4;
        });

        // c. di cadangan (b), jika ada lebih dari 1 step level 4, Log::warning dengan nama step-nya
        if ($level4Steps->count() > 1) {
            Log::warning('Approval flow memiliki lebih dari 1 step level 4: ' . $level4Steps->pluck('step_name')->implode(', '));
        }

        if ($reverse) {
            // reverse: firstWhere('step_order', 2), jika tidak ada step level 4, jika tidak ada $steps->first()
            $checkerStep = $steps->firstWhere('step_order', 2);
            if (!$checkerStep) {
                $checkerStep = $level4Steps->first();
            }
            if (!$checkerStep) {
                $checkerStep = $steps->first();
            }
            return $checkerStep;
        }

        // forward: step pertama dengan LevelJabatan->level_jabatan === 4, jika tidak ada $steps->first()
        $checkerStep = $level4Steps->first();
        if (!$checkerStep) {
            $checkerStep = $steps->first();
        }
        return $checkerStep;
    }

    /**
     * @return array{id: string|null, name: string|null}
     */
    private function resolveChecker(string $submenuId): array
    {
        $checkers = $this->resolveMultiCheckers($submenuId);
        if (!empty($checkers)) {
            return ['id' => $checkers[0]['id'], 'name' => $checkers[0]['name']];
        }
        return ['id' => null, 'name' => null];
    }

    public function getApprovalFlowPreview($submenuId)
    {
        $flow = $this->findApprovalFlow($submenuId);
        if (!$flow) {
            return response()->json(['code' => 404, 'message' => 'Approval flow not found'], 404);
        }
        $requestor = $this->currentKaryawan();
        if (!$requestor) {
            return response()->json(['code' => 403, 'message' => 'User not authenticated'], 403);
        }

        $checkers  = $this->resolveMultiCheckers($submenuId);
        $primary   = !empty($checkers) ? $checkers[0] : ['id' => null, 'name' => null];
        $approvers = $this->resolveApprovers($flow, $requestor, !empty($primary['id']));

        if (!$approvers['found']) {
            return response()->json([
                'code'      => 404,
                'found'     => false,
                'message'   => 'Anda belum terdaftar ke menu pengajuan ubah hapus atau atasan langsung tidak ditemukan. Silakan hubungi Administrator.',
                'approvers' => null,
                'checker'   => null,
                'checkers'  => [],
            ]);
        }

        return response()->json([
            'code'      => 200,
            'found'     => true,
            'approvers' => $approvers,
            'checker'   => $primary,
            'checkers'  => $checkers,
        ]);
    }


    /**
     * @return array{id: string|null, name: string|null}
     */
    private function findUserByStep(?TicketApprovalStep $step): array
    {
        if (!$step) {
            return ['id' => null, 'name' => null, 'karyawan_id' => null, 'karyawan_name' => null, 'karyawan_names' => []];
        }
        $karyawanIds = DB::table('ticket_approval_karyawan')
            ->where('approval_step_id', $step->id)
            ->pluck('karyawan_id')
            ->toArray();
        if (empty($karyawanIds)) {
            return ['id' => null, 'name' => null, 'karyawan_id' => null, 'karyawan_name' => null, 'karyawan_names' => []];
        }
        $karyawans = Karyawan::whereIn('id', $karyawanIds)->get();
        if ($karyawans->isEmpty()) {
            return ['id' => null, 'name' => null, 'karyawan_id' => null, 'karyawan_name' => null, 'karyawan_names' => []];
        }
        $primary = $karyawans->first();
        return [
            'id'             => $primary->id,
            'name'           => $primary->name,
            'karyawan_id'    => $primary->id,
            'karyawan_name'  => $primary->name,
            'karyawan_ids'   => $karyawans->pluck('id')->toArray(),
            'karyawan_names' => $karyawans->pluck('name')->toArray(),
        ];
    }

    /**
     * @param string|null $jabatanId
     * @return array<string> List of Karyawan names
     */
    private function getKaryawanNamesByJabatanId(?string $jabatanId): array
    {
        if (!$jabatanId) {
            return [];
        }
        $karyawanIds = KaryawanJabatan::where('jabatan_id', $jabatanId)
            ->where('is_active', 1)
            ->pluck('karyawan_id')
            ->toArray();

        if (empty($karyawanIds)) {
            return [];
        }
        return Karyawan::whereIn('id', $karyawanIds)->pluck('name')->toArray();
    }

    private function getKaryawanNamesForStep(?TicketApprovalStep $step): array
    {
        if (!$step) {
            return ['karyawan_ids' => [], 'karyawan_names' => []];
        }
        $rows = DB::table('ticket_approval_karyawan')
            ->where('approval_step_id', $step->id)
            ->pluck('karyawan_id')
            ->toArray();
        if (!empty($rows)) {
            $karyawans = Karyawan::whereIn('id', $rows)->get();
            return [
                'karyawan_ids'   => $karyawans->pluck('id')->map(fn($id) => (string)$id)->toArray(),
                'karyawan_names' => $karyawans->pluck('name')->toArray(),
            ];
        }
        if ($step->jabatan_id) {
            $karyawanIds = KaryawanJabatan::where('jabatan_id', $step->jabatan_id)
                ->where('is_active', 1)
                ->pluck('karyawan_id')
                ->toArray();
            $karyawans = Karyawan::whereIn('id', $karyawanIds)->get();
            return [
                'karyawan_ids'   => $karyawans->pluck('id')->map(fn($id) => (string)$id)->toArray(),
                'karyawan_names' => $karyawans->pluck('name')->toArray(),
            ];
        }
        return ['karyawan_ids' => [], 'karyawan_names' => []];
    }

    private function requestorMatchesStep(Karyawan $karyawan, ?TicketApprovalStep $step): bool
    {
        if (!$step) {
            return false;
        }
        $jabatanList = KaryawanJabatan::where('karyawan_id', $karyawan->id)
            ->where('is_active', 1)
            ->get();
        if ($jabatanList->isEmpty()) {
            return false;
        }
        foreach ($jabatanList as $jabatan) {
            $match = true;
            foreach (self::STEP_TO_JABATAN_COLUMN as $stepCol => $jabatanCol) {
                $required = $step->{$stepCol};
                if (is_null($required)) {
                    continue;
                }
                if ((string) $jabatan->{$jabatanCol} !== (string) $required) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                return true;
            }
        }
        return false;
    }

    public function ticketExport($id)
    {
        $user = Auth::user();
        $ticket = TicketHead::with([
            'ticketDetails.subDetails',
            'ticketDetails.referenceTicketDetail',
            'ticketMenu',
            'ticketSubMenu',
            'approvals.karyawan',
            'Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen',
            'Requestor.KaryawanNIK.KaryawanJabatanOne.Divisi',
            'Requestor.KaryawanNIK.KaryawanJabatanOne.Jabatan',
            'Requestor.KaryawanNIK.PenempatanKerja',
        ])
            ->find($id);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        // [PEMBATALAN PER BARIS] Baris yang dibatalkan tidak dicetak; transaksi yang seluruh barisnya batal ikut disembunyikan.
        $hideDetailIds = [];
        $ticket->ticketDetails->each(function ($d) use (&$hideDetailIds) {
            $active = $d->subDetails->filter(fn($sd) => !$sd->is_cancelled)->values();
            if ($d->subDetails->isNotEmpty() && $active->isEmpty()) {
                $hideDetailIds[] = (string) $d->id;
            }
            $d->setRelation('subDetails', $active);
        });
        $ticket->setRelation(
            'ticketDetails',
            $ticket->ticketDetails->reject(fn($d) => in_array((string) $d->id, $hideDetailIds, true))->values()
        );
        $menu = TicketMenu::where('kategori', $ticket->ticketMenu?->kategori)
            ->select('id', 'nama_menu')
            ->orderBy('nama_menu', 'asc')
            ->get();

        // Resolve requestor karyawan info
        $requestorKaryawan    = $ticket->Requestor?->KaryawanNIK;
        $karyawanJabatan      = $requestorKaryawan?->KaryawanJabatanOne;

        // Resolve holding environment based on ticket menu kategori (1 => SPS, 2 => SP, 3 => AJA)
        $kategori = (int) ($ticket->ticketMenu?->kategori ?? 1);
        $holdingCodeMap = [
            1 => 'SPS',
            2 => 'SP',
            3 => 'AJA',
        ];
        $targetHoldingCode = $holdingCodeMap[$kategori] ?? 'SPS';
        $holding = Holding::where('holding_code', $targetHoldingCode)->first();

        $pdf = Pdf::setOptions([
            'defaultFont'          => 'sans-serif',
            'isRemoteEnabled'      => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled'         => true,
            'chroot'               => [public_path(), storage_path('app/public'),],
        ])
            ->loadView('users.ticket.export', compact('ticket', 'menu', 'requestorKaryawan', 'karyawanJabatan', 'holding'))
            ->setPaper('a4', 'portrait');
        $filename = 'ticket-' . str_replace('/', '-', $ticket->nomor_ticket) . '.pdf';
        $userAgent = request()->header('User-Agent');
        $isMobile  = preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent);
        if ($isMobile) {
            return $pdf->download($filename);
        } else {
            return $pdf->stream($filename);
        }
    }

    public function ticketReport($menu_id)
    {
        $user_karyawan = $this->currentKaryawan();
        $get_role = RoleUsers::where('role_user_id', Auth::user()->id)
            ->pluck('role_menu_id')
            ->toArray();
        [$all_menu_main, $all_menus, $all_menu_bar] = $this->buildMenus($get_role);
        User::where('id', Auth::user()->id)->update(['menu_active' => $menu_id]);
        return view('users.ticket.report', compact('menu_id', 'user_karyawan', 'all_menu_main', 'all_menus', 'all_menu_bar'));
    }

    public function getListTicketReport(Request $request)
    {
        try {
            $startDate   = $request->start_date;
            $endDate     = $request->end_date;
            $departemen  = $request->departemen;
            $status      = $request->status;
            $kategori    = $request->kategori;

            $tickets = TicketHead::with([
                'ticketDetails.subDetails',
                'ticketDetails.referenceTicketDetail',
                'ticketMenu',
                'ticketSubMenu',
                'Requestor.KaryawanNIK.PenempatanKerja',
                'Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen',
                'workSessions',
            ])
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate,   fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->when($departemen, function ($q) use ($departemen) {
                    $q->whereHas('Requestor.KaryawanNIK.KaryawanJabatanOne.Departemen', function ($q2) use ($departemen) {
                        $q2->where('nama_departemen', 'like', '%' . $departemen . '%')
                            ->orWhere('kode', $departemen);
                    });
                })
                ->when($status, function ($q) use ($status) {
                    if ($status === 'T08' || $status === 'rejected') {
                        $q->where(function ($sub) {
                            $sub->where('ticket_status', TicketHead::STATUS_CANCELLED)
                                ->orWhere('approval_status', TicketHead::APPROVAL_REJECTED);
                        });
                    } elseif ($status === 'T06') {
                        $q->whereIn('ticket_status', [TicketHead::STATUS_CLOSED, TicketHead::STATUS_AUTOMATIC_CLOSE]);
                    } else {
                        $q->where('ticket_status', $status);
                    }
                })
                ->when($kategori, function ($q) use ($kategori) {
                    $q->whereHas('ticketMenu', fn($qm) => $qm->where('kategori', $kategori));
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $result = [];
            foreach ($tickets as $ticket) {
                $envKategori = (int) ($ticket->ticketMenu?->kategori ?? 0);
                if (str_contains($ticket->nomor_ticket, 'AOL-AJA')) {
                    $envKategori = 3;
                } elseif (str_contains($ticket->nomor_ticket, 'AOL-SP') || str_contains($ticket->nomor_ticket, 'AOL-SPS')) {
                    $envKategori = 2;
                } elseif (str_contains($ticket->nomor_ticket, 'EPIC')) {
                    $envKategori = 1;
                } elseif ($envKategori === 0) {
                    $envKategori = 1;
                }

                $siteName    = $ticket->Requestor?->KaryawanNIK?->PenempatanKerja?->site_name ?? '-';
                $deptName    = $ticket->Requestor?->KaryawanNIK?->KaryawanJabatanOne?->Departemen?->nama_departemen ?? '-';

                $envName = match ($envKategori) {
                    1 => 'Epicor ERP',
                    2 => 'Accurate Online SP',
                    3 => 'Accurate Online AJA',
                    default => 'Epicor ERP',
                };

                // Ambil tanggal terakhir selesai dikerjakan oleh PIC (jika ada beberapa revisi / siklus kerja, ambil ended_at / finish_at yang paling akhir)
                $latestSessionEnded = $ticket->workSessions ? $ticket->workSessions->whereNotNull('ended_at')->max('ended_at') : null;
                $picFinishedAt = null;
                if ($latestSessionEnded && $ticket->finish_at) {
                    $picFinishedAt = \Carbon\Carbon::parse($latestSessionEnded)->gt(\Carbon\Carbon::parse($ticket->finish_at))
                        ? $latestSessionEnded
                        : $ticket->finish_at;
                } else {
                    $picFinishedAt = $ticket->finish_at ?? $latestSessionEnded;
                }
                $picFinishedAtFormatted = $picFinishedAt ? \Carbon\Carbon::parse($picFinishedAt)->format('Y-m-d H:i:s') : null;

                $closedAtFormatted = ($ticket->closed_at ?? (in_array($ticket->ticket_status, [TicketHead::STATUS_CLOSED, TicketHead::STATUS_AUTOMATIC_CLOSE]) ? ($ticket->updated_at ?? $picFinishedAt) : null))
                    ? \Carbon\Carbon::parse($ticket->closed_at ?? ($ticket->updated_at ?? $picFinishedAt))->format('Y-m-d H:i:s')
                    : null;

                if ($ticket->ticketDetails->isEmpty()) {
                    $result[] = [
                        'id'                          => $ticket->id,
                        'created_at'                  => $ticket->created_at,
                        'nomor_ticket'                => $ticket->nomor_ticket,
                        'ticket_status'               => $ticket->ticket_status,
                        'approval_status'             => $ticket->approval_status,
                        'ticket_menu'                 => $ticket->ticketMenu,
                        'ticket_kategori'             => $ticket->ticketMenu?->kategori,
                        'environment_kategori'        => $envKategori,
                        'environment_name'            => $envName,
                        'ticket_sub_menu'             => $ticket->ticketSubMenu,
                        'line_detail'                 => '-',
                        'nomor_detail'                => '-',
                        'reference_nomor_detail'      => '-',
                        'reference_tanggal_transaksi' => '-',
                        'line_sub_detail'             => '-',
                        'alasan_ubah'                 => '-',
                        'before_ubah'                 => '-',
                        'after_ubah'                  => '-',
                        'pic_name'                    => $ticket->pic_name ?: '-',
                        'validator_at'                => $ticket->validator_at ? \Carbon\Carbon::parse($ticket->validator_at)->format('Y-m-d H:i:s') : null,
                        'assign_at'                   => $ticket->assign_at ? \Carbon\Carbon::parse($ticket->assign_at)->format('Y-m-d H:i:s') : null,
                        'pic_started_at'              => $ticket->pic_started_at ? \Carbon\Carbon::parse($ticket->pic_started_at)->format('Y-m-d H:i:s') : null,
                        'finish_at'                   => $picFinishedAtFormatted,
                        'closed_at'                   => $closedAtFormatted,
                        'approve1_name'               => $ticket->approve1_name,
                        'approve2_name'               => $ticket->approve2_name,
                        'checker_name'                => $ticket->checker_name,
                        'validator_name'              => $ticket->validator_name,
                        'requestor_name'              => $ticket->requestor_name ?: '-',
                        'site_name'                   => $siteName,
                        'departemen'                  => $deptName,
                    ];
                } else {
                    foreach ($ticket->ticketDetails as $detail) {
                        $refNomor   = $detail->referenceTicketDetail?->nomor_detail ?? '-';
                        $refTanggal = $detail->referenceTicketDetail?->tanggal_transaksi ?? '-';
                        if ($detail->subDetails && $detail->subDetails->isNotEmpty()) {
                            foreach ($detail->subDetails as $sub) {
                                $result[] = [
                                    'id'                          => $ticket->id,
                                    'created_at'                  => $ticket->created_at,
                                    'nomor_ticket'                => $ticket->nomor_ticket,
                                    'ticket_status'               => $ticket->ticket_status,
                                    'approval_status'             => $ticket->approval_status,
                                    'ticket_menu'                 => $ticket->ticketMenu,
                                    'ticket_kategori'             => $ticket->ticketMenu?->kategori,
                                    'environment_kategori'        => $envKategori,
                                    'environment_name'            => $envName,
                                    'ticket_sub_menu'             => $ticket->ticketSubMenu,
                                    'line_detail'                 => $detail->line_detail ?? '-',
                                    'nomor_detail'                => $detail->nomor_detail ?? '-',
                                    'reference_nomor_detail'      => $refNomor,
                                    'reference_tanggal_transaksi' => $refTanggal,
                                    'line_sub_detail'             => $sub->line_number ?? '-',
                                    'alasan_ubah'                 => ($sub->is_cancelled
                                        ? '[DIBATALKAN' . ($sub->cancel_reason ? ': ' . $sub->cancel_reason : '') . '] '
                                        : '') . ($sub->alasan_ubah ?? $detail->alasan_ubah ?? '-'),
                                    'is_cancelled'                => (bool) $sub->is_cancelled,
                                    'before_ubah'                 => $sub->before_ubah ?? $detail->before_ubah ?? '-',
                                    'after_ubah'                  => $sub->after_ubah ?? $detail->after_ubah ?? '-',
                                    'pic_name'                    => $ticket->pic_name ?: '-',
                                    'validator_at'                => $ticket->validator_at ? \Carbon\Carbon::parse($ticket->validator_at)->format('Y-m-d H:i:s') : null,
                                    'assign_at'                   => $ticket->assign_at ? \Carbon\Carbon::parse($ticket->assign_at)->format('Y-m-d H:i:s') : null,
                                    'pic_started_at'              => $ticket->pic_started_at ? \Carbon\Carbon::parse($ticket->pic_started_at)->format('Y-m-d H:i:s') : null,
                                    'finish_at'                   => $picFinishedAtFormatted,
                                    'closed_at'                   => $closedAtFormatted,
                                    'approve1_name'               => $ticket->approve1_name,
                                    'approve2_name'               => $ticket->approve2_name,
                                    'checker_name'                => $ticket->checker_name,
                                    'validator_name'              => $ticket->validator_name,
                                    'requestor_name'              => $ticket->requestor_name ?: '-',
                                    'site_name'                   => $siteName,
                                    'departemen'                  => $deptName,
                                ];
                            }
                        } else {
                            $result[] = [
                                'id'                          => $ticket->id,
                                'created_at'                  => $ticket->created_at,
                                'nomor_ticket'                => $ticket->nomor_ticket,
                                'ticket_status'               => $ticket->ticket_status,
                                'approval_status'             => $ticket->approval_status,
                                'ticket_menu'                 => $ticket->ticketMenu,
                                'ticket_kategori'             => $ticket->ticketMenu?->kategori,
                                'environment_kategori'        => $envKategori,
                                'environment_name'            => $envName,
                                'ticket_sub_menu'             => $ticket->ticketSubMenu,
                                'line_detail'                 => $detail->line_detail ?? '-',
                                'nomor_detail'                => $detail->nomor_detail ?? '-',
                                'reference_nomor_detail'      => $refNomor,
                                'reference_tanggal_transaksi' => $refTanggal,
                                'line_sub_detail'             => '-',
                                'alasan_ubah'                 => $detail->alasan_ubah ?? '-',
                                'before_ubah'                 => $detail->before_ubah ?? '-',
                                'after_ubah'                  => $detail->after_ubah ?? '-',
                                'pic_name'                    => $ticket->pic_name ?: '-',
                                'validator_at'                => $ticket->validator_at ? \Carbon\Carbon::parse($ticket->validator_at)->format('Y-m-d H:i:s') : null,
                                'assign_at'                   => $ticket->assign_at ? \Carbon\Carbon::parse($ticket->assign_at)->format('Y-m-d H:i:s') : null,
                                'pic_started_at'              => $ticket->pic_started_at ? \Carbon\Carbon::parse($ticket->pic_started_at)->format('Y-m-d H:i:s') : null,
                                'finish_at'                   => $picFinishedAtFormatted,
                                'closed_at'                   => $closedAtFormatted,
                                'approve1_name'               => $ticket->approve1_name,
                                'approve2_name'               => $ticket->approve2_name,
                                'checker_name'                => $ticket->checker_name,
                                'validator_name'              => $ticket->validator_name,
                                'requestor_name'              => $ticket->requestor_name ?: '-',
                                'site_name'                   => $siteName,
                                'departemen'                  => $deptName,
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'code'  => 200,
                'data'  => $result,
                'total' => count($result),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getDepartemen()
    {
        try {
            $departemen = Departemen::orderBy('nama_departemen', 'asc')->get();
            $result = [];
            foreach ($departemen as $departemen) {
                $result[] = [
                    'kode' => $departemen->kode,
                    'nama' => $departemen->nama_departemen,
                ];
            }
            return response()->json([
                'code' => 200,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorController::previewApproval()
     */
    public function previewApprovalVendor($menuId)
    {
        return app(TicketVendorController::class)->previewApproval($menuId);
    }


    /**
     * @deprecated Silahkan gunakan TicketVendorController::index()
     */
    public function ticketVendorIndex($menu_id)
    {
        return app(TicketVendorController::class)->index($menu_id);
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorController::getVendorTicket()
     */
    public function get_vendor_ticket()
    {
        return app(TicketVendorController::class)->getVendorTicket();
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorController::create()
     */
    public function ticketVendorCreate($menu_id)
    {
        return app(TicketVendorController::class)->create($menu_id);
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorController::store()
     */
    public function ticketVendorStore(Request $request)
    {
        return app(TicketVendorController::class)->store($request);
    }

    /**
     * @deprecated Silahkan gunakan TicketVendorController::show()
     */
    public function TicketVendorShow($menu_id, $id)
    {
        return app(TicketVendorController::class)->show($menu_id, $id);
    }
}
