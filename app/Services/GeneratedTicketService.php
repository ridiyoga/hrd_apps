<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Karyawan;
use App\Models\TicketApproval;
use App\Models\TicketApprovalValidator;
use App\Models\TicketConditionValue;
use App\Models\TicketDependencyRule;
use App\Models\TicketHead;
use App\Models\TicketDetail;
use App\Models\TicketSubDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class GeneratedTicketService
{
    public const STATUS_PENDING          = 'PENDING';
    public const STATUS_AWAITING_DETAILS = 'AWAITING_DETAILS';
    public const STATUS_SUCCESS          = 'SUCCESS';
    public const STATUS_FAILED           = 'FAILED';
    public const STATUS_SKIPPED          = 'SKIPPED';

    protected DependencyEngine $dependencyEngine;

    public function __construct(DependencyEngine $dependencyEngine)
    {
        $this->dependencyEngine = $dependencyEngine;
    }

    /**
     * Creates a DRAFT TicketHead record for a fired dependency rule.
     *
     * Key Business Rules:
     * 1. Draft generated ticket has: is_draft = true, is_submit = false, ticket_status = 'T00' (STATUS_DRAFT)
     * 2. requestor_id is set to the Checker User ($checkerUser) who submitted the review
     * 3. Lineage metadata (root_ticket_id, parent_ticket_id, generated_from_ticket_id, dependency_rule_id) is recorded
     * 4. Anti-loop check ensures target sub_menu is not already in parent lineage chain
     * 5. Selected parent details are always cloned (header row only) as a REFERENCE
     *    when $selectedDetailIds is provided, via reference_ticket_detail_id.
     * 6. Sub-detail rows (nomor baris & alasan) are ONLY cloned when $copyFullData
     *    is true. Even then, before_ubah & after_ubah are NEVER copied — they are
     *    intentionally left blank so the next requestor/checker must fill the
     *    current condition & target change consciously, instead of silently
     *    inheriting stale values from the parent ticket.
     * 7. Entire draft creation + approval update is transactional & idempotent
     *
     * @param TicketApproval $approval The checker's approval row
     * @param User $checkerUser The logged-in checker user
     * @param array $selectedDetailIds Array of parent ticket_detail.id selected by the checker
     * @param bool $copyFullData Whether to also clone sub-detail rows (line_number, alasan_ubah)
     *                            for the selected details. before_ubah/after_ubah are always
     *                            left blank regardless of this flag.
     * @return array{status: string, message: string, draftTicket: TicketHead|null}
     */
    public function createDraftFromApproval(TicketApproval $approval, User $checkerUser, array $selectedDetailIds = [], bool $copyFullData = false): array
    {
        return DB::transaction(function () use ($approval, $checkerUser, $selectedDetailIds, $copyFullData) {
            $parentTicket = TicketHead::find($approval->ticket_id);
            if (!$parentTicket) {
                Log::error('[GeneratedTicketService] Parent ticket not found', ['approval_id' => $approval->id]);
                return ['status' => self::STATUS_FAILED, 'message' => 'Parent ticket tidak ditemukan.', 'draftTicket' => null];
            }

            $ruleId    = $approval->dependency_rule_id;
            $submenuId = $approval->branch_sub_menu_id;

            if (!$ruleId || !$submenuId) {
                return ['status' => self::STATUS_FAILED, 'message' => 'Dependency rule atau sub menu tujuan tidak valid.', 'draftTicket' => null];
            }

            $rule = TicketDependencyRule::find($ruleId);
            if (!$rule) {
                return ['status' => self::STATUS_FAILED, 'message' => 'Dependency rule tidak ditemukan.', 'draftTicket' => null];
            }

            // ── Idempotency Check #1: Draft ticket already linked to approval row ──
            if ($approval->generated_ticket_id) {
                $existingDraft = TicketHead::find($approval->generated_ticket_id);
                if ($existingDraft) {
                    Log::info('[GeneratedTicketService] Reusing existing generated draft ticket', [
                        'approval_id' => $approval->id,
                        'draft_id'    => $existingDraft->id,
                    ]);
                    return [
                        'status'      => self::STATUS_AWAITING_DETAILS,
                        'message'     => 'Draft ticket sudah ada.',
                        'draftTicket' => $existingDraft,
                    ];
                }
            }

            // ── Idempotency Check #2 (Recovery): Existing ticket with same root + parent + rule ──
            $rootId      = $parentTicket->root_ticket_id ?? $parentTicket->id;
            $existingRow = DB::table('ticket_approvals as ta')
                ->join('ticket_head as th', function ($join) {
                    $join->on(DB::raw('ta.generated_ticket_id::text'), '=', DB::raw('th.id::text'));
                })
                ->where('ta.dependency_rule_id', $ruleId)
                ->where('th.root_ticket_id', $rootId)
                ->where('th.parent_ticket_id', $parentTicket->id)
                ->whereNotNull('ta.generated_ticket_id')
                ->select('ta.generated_ticket_id')
                ->first();

            if ($existingRow) {
                $existingDraft = TicketHead::find($existingRow->generated_ticket_id);
                if ($existingDraft) {
                    $approval->update([
                        'generate_status'     => self::STATUS_AWAITING_DETAILS,
                        'generated_ticket_id' => $existingDraft->id,
                        'generated_at'        => now(),
                    ]);
                    return [
                        'status'      => self::STATUS_AWAITING_DETAILS,
                        'message'     => 'Draft ticket ditemukan (recovery).',
                        'draftTicket' => $existingDraft,
                    ];
                }
            }

            // ── Level 1 Loop Prevention: Incoming Edge Exclusion ──
            if ($this->dependencyEngine->isIncomingEdge($parentTicket, $rule)) {
                $approval->update([
                    'generate_status' => self::STATUS_SKIPPED,
                    'generate_error'  => 'Level 1 exclusion: edge is the incoming rule that spawned this ticket.',
                ]);
                return [
                    'status'      => self::STATUS_SKIPPED,
                    'message'     => 'Dependency dilewati (incoming edge exclusion).',
                    'draftTicket' => null,
                ];
            }

            // ── Level 2 Loop Prevention: Anti-Loop Lineage Check ──
            if ($this->dependencyEngine->detectLoop($parentTicket, $submenuId)) {
                $approval->update([
                    'generate_status' => self::STATUS_SKIPPED,
                    'generate_error'  => 'Level 2 exclusion: sub menu sudah ada di lineage transaksi.',
                ]);
                return [
                    'status'      => self::STATUS_SKIPPED,
                    'message'     => 'Dependency dilewati untuk mencegah looping transaksi.',
                    'draftTicket' => null,
                ];
            }

            // ── Resolve Target SubMenu & Menu ──
            $subMenu = DB::table('ticket_sub_menu as tsm')
                ->join('ticket_menu as tm', 'tsm.menu_id', '=', 'tm.id')
                ->where('tsm.id', $submenuId)
                ->select('tsm.*', 'tm.id as ticket_menu_id', 'tm.kategori as menu_kategori')
                ->first();

            if (!$subMenu) {
                $approval->update([
                    'generate_status' => self::STATUS_FAILED,
                    'generate_error'  => 'Sub menu tujuan tidak ditemukan.',
                ]);
                return ['status' => self::STATUS_FAILED, 'message' => 'Sub menu tujuan tidak ditemukan.', 'draftTicket' => null];
            }

            // ── Resolve Cross-Environment Status & Independent Root ──
            $isCrossEnv = (bool) ($rule?->is_cross_environment ?? false);
            if (!$isCrossEnv && $parentTicket) {
                $parentMenu = $parentTicket->ticketMenu ?: DB::table('ticket_menu')->where('id', $parentTicket->ticket_menu_id)->first();
                if ($parentMenu && isset($subMenu->menu_kategori) && (int)($parentMenu->kategori ?? 0) !== (int)$subMenu->menu_kategori) {
                    $isCrossEnv = true;
                }
            }

            $checkerKaryawan = Karyawan::where('nomor_identitas_karyawan', $checkerUser->karyawan_id)
                ->orWhere('id', $checkerUser->karyawan_id)
                ->first();
            $checkerName = $checkerKaryawan?->name ?? $checkerUser->username ?? 'Checker';

            $draftTicketId  = (string) Uuid::uuid4();
            $nomorTicket    = app(\App\Http\Controllers\TicketController::class)->generateNomorTicket(null, $submenuId);
            $rootId         = $isCrossEnv ? $draftTicketId : ($parentTicket->root_ticket_id ?? $parentTicket->id);

            // ── Create Draft Ticket (is_draft = true, is_submit = false) ──
            $draftTicket = TicketHead::create([
                'id'                       => $draftTicketId,
                'nomor_ticket'             => $nomorTicket,
                'ticket_menu_id'           => $subMenu->ticket_menu_id,
                'ticket_sub_menu_id'       => $submenuId,
                'requestor_id'             => $checkerUser->id,
                'requestor_name'           => $checkerName,
                'ticket_status'            => TicketHead::STATUS_DRAFT,
                'approval_status'          => TicketHead::APPROVAL_DRAFT,
                'is_draft'                 => true,
                'is_submit'                => false,
                'is_generated'             => true,
                'is_cross_environment'     => $isCrossEnv,
                'cross_env_status'         => $isCrossEnv ? TicketHead::CROSS_ENV_TRIGGERED : TicketHead::CROSS_ENV_NONE,
                'origin_cross_ticket_id'   => $isCrossEnv ? $parentTicket->id : null,
                'root_ticket_id'           => $rootId,
                'parent_ticket_id'         => $parentTicket->id,
                'generated_from_ticket_id' => $parentTicket->id,
                'dependency_rule_id'       => $ruleId,
                'created_at'               => now()->toDateTimeString(),
            ]);

            if ($isCrossEnv) {
                $parentTicket->update([
                    'cross_env_status' => TicketHead::CROSS_ENV_TRIGGERED,
                ]);
            }

            // ── Clone selected parent details & sub-details if specified by Checker (Kondisi 1) ──
            if (!empty($selectedDetailIds)) {
                $parentDetails = TicketDetail::with('subDetails')
                    ->where('ticket_id', $parentTicket->id)
                    ->whereIn('id', $selectedDetailIds)
                    ->get();

                foreach ($parentDetails as $pDet) {
                    $newDetailId = (string) Uuid::uuid4();
                    TicketDetail::create([
                        'id'                         => $newDetailId,
                        'ticket_id'                  => $draftTicketId,
                        'line_number'                => $pDet->line_number,
                        'reference_ticket_detail_id' => $pDet->id,
                        'created_at'                 => now(),
                    ]);
                    // Sub-detail HANYA disalin jika checker mengaktifkan toggle "Salin Data
                    // Lengkap". before_ubah & after_ubah SENGAJA tidak pernah disalin (tetap
                    // null) supaya requestor/checker berikutnya wajib mengisi ulang kondisi
                    // & target perubahan secara sadar, bukan mewarisi nilai lama tanpa disadari.
                    if ($copyFullData && $pDet->subDetails && $pDet->subDetails->isNotEmpty()) {
                        TicketDetail::updateOrCreate([
                            'id' => $newDetailId
                        ], [
                            'nomor_detail'               => $pDet->nomor_detail,
                            'tanggal_transaksi'          => $pDet->tanggal_transaksi,
                        ]);
                        foreach ($pDet->subDetails as $pSub) {
                            TicketSubDetail::create([
                                'id'               => (string) Uuid::uuid4(),
                                'ticket_detail_id' => $newDetailId,
                                // 'line_number'      => $pSub->line_number,
                                'line_number'      => null, // intentionally left null to force new input
                                'alasan_ubah'      => $pSub->alasan_ubah,
                                'before_ubah'      => $pSub->before_ubah,
                                'after_ubah'       => $pSub->after_ubah,
                                'created_at'       => now(),
                            ]);
                        }
                    }
                }
            }

            // ── Link Approval Row to Generated Draft Ticket ──
            $approval->update([
                'generate_status'     => self::STATUS_AWAITING_DETAILS,
                'generated_ticket_id' => $draftTicketId,
                'generated_at'        => now(),
                'generate_error'      => null,
            ]);

            // ── Pre-sync checkers for draft ticket using lineage-filtered context ──
            $ticketController = app(\App\Http\Controllers\TicketController::class);
            if ($checkerKaryawan) {
                try {
                    $approvalData = $ticketController->resolveApprovalDataPublic(
                        $submenuId,
                        $checkerKaryawan,
                        false,
                        [],
                        $draftTicket
                    );
                    $ticketController->syncTicketCheckersPublic($draftTicket->id, $approvalData['checkers'] ?? []);
                } catch (\Exception $e) {
                    Log::info('[GeneratedTicketService] No additional checkers resolved for draft ticket', [
                        'draft_id' => $draftTicket->id,
                        'reason'   => $e->getMessage(),
                    ]);
                }
            }

            ActivityLog::create([
                'user_id'     => $checkerUser->id,
                'user_name'   => $checkerUser->username,
                'menu_id'     => null,
                'menu_name'   => 'Generated Draft Ticket',
                'id_table'    => $draftTicketId,
                'holding_id'  => '-',
                'holding_category' => '-',
                'table'       => 'ticket_head',
                'activity'    => 'Create Draft Generated Ticket',
                'description' => 'Draft Ticket ' . $nomorTicket . ' berhasil dibuat dari Ticket: ' . $parentTicket->nomor_ticket,
                'keterangan'  => 'Success',
                'karir'       => '2',
            ]);

            Log::info('[GeneratedTicketService] Draft ticket successfully created', [
                'approval_id'     => $approval->id,
                'draft_ticket_id' => $draftTicketId,
                'nomor_ticket'    => $nomorTicket,
                'requestor_id'    => $checkerUser->id,
            ]);

            return [
                'status'      => self::STATUS_AWAITING_DETAILS,
                'message'     => 'Draft ticket berhasil dibuat. Silakan lengkapi detail transaksi.',
                'draftTicket' => $draftTicket,
            ];
        });
    }

    /**
     * Marks an approval row as SKIPPED (condition answered FALSE).
     */
    public function markSkipped(TicketApproval $approval): void
    {
        $approval->update(['generate_status' => self::STATUS_SKIPPED]);
        Log::info('[GeneratedTicketService] Approval rule marked SKIPPED', ['approval_id' => $approval->id]);
    }

    /**
     * Cancels a DRAFT generated ticket (e.g. when checker mistakenly triggered a dependency condition).
     *
     * @param TicketHead $draftTicket The generated draft ticket to cancel
     * @param User $actor The user (admin or checker) executing the cancellation
     * @param string $reason Cancellation rationale
     * @return array{success: bool, message: string, parentTicket: TicketHead|null}
     */
    public function cancelGeneratedDraft(TicketHead $draftTicket, User $actor, string $reason = ''): array
    {
        return DB::transaction(function () use ($draftTicket, $actor, $reason) {
            $freshDraft = TicketHead::lockForUpdate()->find($draftTicket->id);
            if (!$freshDraft) {
                return [
                    'success'      => false,
                    'message'      => 'Tiket draft yang dimaksud tidak ditemukan.',
                    'parentTicket' => null,
                ];
            }

            if (!$freshDraft->is_generated && !$freshDraft->parent_ticket_id) {
                return [
                    'success'      => false,
                    'message'      => 'Tiket ini bukan tiket turunan/generated sehingga tidak dapat dibatalkan melalui fitur ini.',
                    'parentTicket' => null,
                ];
            }

            if (!$freshDraft->is_draft || $freshDraft->is_submit || !in_array($freshDraft->ticket_status, [TicketHead::STATUS_DRAFT, TicketHead::STATUS_WAITING_CHECKER])) {
                return [
                    'success'      => false,
                    'message'      => 'Tiket sudah diajukan atau diproses, pembatalan draft hanya berlaku untuk tiket yang masih berstatus Draft.',
                    'parentTicket' => null,
                ];
            }

            $parentTicketId = $freshDraft->parent_ticket_id ?: $freshDraft->generated_from_ticket_id;
            $parentTicket = null;
            if ($parentTicketId) {
                $parentTicket = TicketHead::lockForUpdate()->find($parentTicketId);
            }

            // 1. Rollback parent ticket approval row
            if ($parentTicket) {
                $approvals = TicketApproval::where('ticket_id', $parentTicket->id)
                    ->where(function ($q) use ($freshDraft) {
                        $q->where('generated_ticket_id', $freshDraft->id)
                            ->orWhere(function ($q2) use ($freshDraft) {
                                if ($freshDraft->dependency_rule_id) {
                                    $q2->where('dependency_rule_id', $freshDraft->dependency_rule_id);
                                }
                            });
                    })
                    ->get();

                foreach ($approvals as $app) {
                    $app->update([
                        'generate_status'     => self::STATUS_SKIPPED,
                        'generated_ticket_id' => null,
                        'generate_error'      => 'Dibatalkan oleh ' . ($actor->username ?? 'Admin') . ': ' . ($reason ?: 'Salah toggle checklist'),
                    ]);

                    // 2. Rollback condition value in ticket_hasil_cek to false
                    if ($app->dependency_rule_id) {
                        $rule = TicketDependencyRule::find($app->dependency_rule_id);
                        if ($rule && $rule->condition_key) {
                            TicketConditionValue::where('ticket_id', $parentTicket->id)
                                ->where('condition_key', $rule->condition_key)
                                ->update([
                                    'condition_value' => false,
                                    'checked_by'      => $actor->id,
                                    'checked_at'      => now(),
                                ]);
                        }
                    }
                }
            }

            // 3. Mark the draft ticket as CANCELLED
            $freshDraft->update([
                'ticket_status'   => TicketHead::STATUS_CANCELLED,
                'approval_status' => TicketHead::APPROVAL_REJECTED,
                'is_draft'        => false,
                'is_submit'       => false,
            ]);

            // 4. Re-evaluate parent ticket status
            if ($parentTicket) {
                // Check if any other generated drafts are still pending
                $hasOtherPendingDrafts = TicketApproval::where('ticket_id', $parentTicket->id)
                    ->where('generate_status', self::STATUS_AWAITING_DETAILS)
                    ->whereNotNull('generated_ticket_id')
                    ->where('generated_ticket_id', '!=', $freshDraft->id)
                    ->exists();

                $hasOtherPendingDraftTickets = TicketHead::where('parent_ticket_id', $parentTicket->id)
                    ->where('id', '!=', $freshDraft->id)
                    ->where('is_draft', true)
                    ->where('ticket_status', '!=', TicketHead::STATUS_CANCELLED)
                    ->exists();

                $hasOtherPendingDrafts = $hasOtherPendingDrafts || $hasOtherPendingDraftTickets;

                // Check remaining pending checkers
                $remainingCheckersCount = TicketApproval::where('ticket_id', $parentTicket->id)
                    ->where('role', 'checker')
                    ->where(function ($q) {
                        $q->where('status', 'pending')->orWhereNull('status');
                    })
                    ->where(function ($q) {
                        $q->whereNull('dependency_rule_id')
                            ->orWhereHas('dependencyRule', function ($q2) {
                                $q2->where('is_cross_environment', false);
                            });
                    })
                    ->where(function ($q) {
                        $q->whereNull('generate_status')
                            ->orWhereNotIn('generate_status', [self::STATUS_SKIPPED, self::STATUS_FAILED]);
                    })
                    ->count();

                if (!$hasOtherPendingDrafts && $remainingCheckersCount === 0) {
                    $environment = $parentTicket->ticketMenu?->kategori;
                    if (empty($environment)) {
                        $nomor = (string) $parentTicket->nomor_ticket;
                        if (str_contains($nomor, 'EPIC')) {
                            $environment = 1;
                        } elseif (str_contains($nomor, 'AOL-SP')) {
                            $environment = 2;
                        } elseif (str_contains($nomor, 'AOL-AJA')) {
                            $environment = 3;
                        }
                    }

                    $hasValidator = !empty($parentTicket->validator_id)
                        || TicketApproval::where('ticket_id', $parentTicket->id)->where('role', 'validator')->exists()
                        || TicketApprovalValidator::where('is_active', true)
                        ->where(function ($query) use ($environment) {
                            $query->whereNull('environment');
                            if (!is_null($environment)) {
                                $query->orWhere('environment', $environment);
                            }
                        })
                        ->exists();

                    $newStatus = $hasValidator ? TicketHead::STATUS_WAITING_VALIDATOR : TicketHead::STATUS_APPROVED;

                    $parentTicket->update([
                        'ticket_status'   => $newStatus,
                        'approval_status' => TicketHead::APPROVAL_APPROVED,
                        'is_draft'        => false,
                    ]);

                    if ($newStatus === TicketHead::STATUS_WAITING_VALIDATOR) {
                        try {
                            app(\App\Services\TicketApprovalEmailService::class)->sendNextActionNotification(
                                $parentTicket->fresh(),
                                $parentTicket->ticket_menu_id,
                                'approved'
                            );
                        } catch (\Exception $e) {
                            Log::warning('[GeneratedTicketService] Failed sending validator notification after draft cancel', [
                                'ticket_id' => $parentTicket->id,
                                'error'     => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }

            // 5. Activity Log
            ActivityLog::create([
                'user_id'          => $actor->id,
                'user_name'        => $actor->username ?? 'Admin',
                'menu_id'          => null,
                'menu_name'        => 'Report Ticket Admin',
                'id_table'         => $freshDraft->id,
                'holding_id'       => '-',
                'holding_category' => '-',
                'table'            => 'ticket_head',
                'activity'         => 'Cancel Generated Draft Ticket',
                'description'      => 'Draft tiket lanjutan ' . $freshDraft->nomor_ticket . ' dibatalkan. Alasan: ' . ($reason ?: 'Salah pengisian oleh checker') . ($parentTicket ? (' (Parent Ticket: ' . $parentTicket->nomor_ticket . ')') : ''),
                'keterangan'       => 'Success',
                'karir'            => '2',
            ]);

            return [
                'success'      => true,
                'message'      => 'Draft tiket lanjutan ' . $freshDraft->nomor_ticket . ' berhasil dibatalkan dan status tiket induk telah disinkronkan.',
                'parentTicket' => $parentTicket ? $parentTicket->fresh() : null,
            ];
        });
    }
}
