<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Karyawan;
use App\Models\TicketApproval;
use App\Models\TicketHead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

/**
 * Dedicated service for evaluating a checker's dependency-rule decision and
 * deciding what should happen next. This is the SINGLE entry point used by
 * BOTH:
 *
 *  - Automatic evaluation (when a checker submits their review), and
 *  - Manual evaluation (per-rule "Generate" button / retry on the checker page)
 *
 * IMPORTANT: this service no longer creates the ticket itself. When a rule
 * fires it only validates the target and returns AWAITING_DETAILS with a
 * `redirect` payload; the caller must send the user to the tambah_generated
 * page (TicketController::ticketHead_addGenerated /
 * ticket.tambah.generated route) so they fill in details exactly like a
 * normal ticket. TicketController::ticketHead_storeGenerated is what
 * actually persists the ticket and reports back to the approval row.
 *
 * It owns idempotency (with recovery), skip-marking, and logging.
 * Controllers only invoke it.
 */
class TicketGenerationService
{
    public const PENDING          = 'PENDING';
    public const PROCESSING       = 'PROCESSING';
    public const SUCCESS          = 'SUCCESS';
    public const FAILED           = 'FAILED';
    public const SKIPPED          = 'SKIPPED';
    // A dependency rule fired but the ticket has NOT been created yet — the
    // user still needs to fill in details on the tambah_generated page
    // before anything is written to ticket_head. generate_status on the
    // approval row stays PENDING until that page actually submits.
    public const AWAITING_DETAILS = 'AWAITING_DETAILS';

    /**
     * Generate the dependent ticket for a single checker approval.
     *
     * @param  \App\Models\TicketApproval $approval the checker's approval row (one rule)
     * @param  string|null $menuId
     * @return array{status: string, message: string, ticket: \App\Models\TicketHead|null}
     */
    public function generateFromApproval(TicketApproval $approval, ?string $menuId = null): array
    {
        $parentTicket = TicketHead::find($approval->ticket_id);
        if (!$parentTicket) {
            Log::error('[TicketGeneration] Parent ticket not found', [
                'approval_id' => $approval->id,
                'ticket_id'   => $approval->ticket_id,
            ]);
            return ['status' => self::FAILED, 'message' => 'Parent ticket tidak ditemukan.', 'ticket' => null];
        }

        $checkerKaryawan = Karyawan::find($approval->karyawan_id);
        if (!$checkerKaryawan) {
            Log::error('[TicketGeneration] Checker karyawan not found', [
                'approval_id' => $approval->id,
                'karyawan_id' => $approval->karyawan_id,
            ]);
            return ['status' => self::FAILED, 'message' => 'Data checker tidak ditemukan.', 'ticket' => null];
        }

        $submenuId = $approval->branch_sub_menu_id;
        $ruleId    = $approval->dependency_rule_id;

        // ── Idempotency check #1: already generated on this approval row ──
        if ($approval->generated_ticket_id || $approval->generate_status === self::SUCCESS) {
            Log::info('[TicketGeneration] Already generated, skip', [
                'approval_id'         => $approval->id,
                'generated_ticket_id' => $approval->generated_ticket_id,
            ]);
            $generated = $approval->generated_ticket_id ? TicketHead::find($approval->generated_ticket_id) : null;
            return ['status' => self::SUCCESS, 'message' => 'Ticket sudah dibuat.', 'ticket' => $generated];
        }

        // ── Idempotency check #2 (recovery): an existing generated child for
        //    the same (root_ticket_id + dependency_rule_id + parent_ticket_id)
        //    already exists. Replay it instead of creating a duplicate. ──
        $rootId      = $parentTicket->root_ticket_id ?? $parentTicket->id;
        $existingRow = DB::table('ticket_approvals as ta')
            ->join('ticket_head as th', function ($join) {
                // ta.generated_ticket_id is stored as uuid while ticket_head.id
                // is character varying — Postgres has no uuid = varchar
                // operator for column-to-column joins, so both sides are cast
                // to text explicitly.
                $join->on(DB::raw('ta.generated_ticket_id::text'), '=', DB::raw('th.id::text'));
            })
            ->where('ta.dependency_rule_id', $ruleId)
            ->where('th.root_ticket_id', $rootId)
            ->where('th.parent_ticket_id', $parentTicket->id)
            ->whereNotNull('ta.generated_ticket_id')
            ->select('ta.generated_ticket_id')
            ->first();

        if ($existingRow) {
            Log::warning('[TicketGeneration] Recovery: replay existing ticket', [
                'approval_id'         => $approval->id,
                'generated_ticket_id' => $existingRow->generated_ticket_id,
            ]);
            $approval->update([
                'generate_status'     => self::SUCCESS,
                'generated_ticket_id' => $existingRow->generated_ticket_id,
                'generated_at'        => now(),
                'generate_error'      => null,
            ]);
            return [
                'status'  => self::SUCCESS,
                'message' => 'Ticket sudah dibuat (recovery).',
                'ticket'  => TicketHead::find($existingRow->generated_ticket_id),
            ];
        }

        // ── The ticket is NOT built here anymore. The user must land on the
        //    tambah_generated page and fill in the change/transaction details
        //    (same as a normal ticket) before anything is persisted — this
        //    mirrors the manual "tambah generate" flow instead of bypassing
        //    it. We only validate that the target sub menu is real so we
        //    don't redirect the user into a dead page. ──
        $subMenu = app(\App\Http\Controllers\TicketController::class)->resolveSubMenuPublic($submenuId);
        if (!$subMenu) {
            $approval->update([
                'generate_status' => self::FAILED,
                'generate_error'  => 'Sub menu tujuan tidak ditemukan untuk generate ticket.',
            ]);
            return ['status' => self::FAILED, 'message' => 'Sub menu tujuan tidak ditemukan untuk generate ticket.', 'ticket' => null];
        }

        // ── Reverse/bidirectional detection: if the fired rule is
        //    bidirectional and its normal (forward) target is the PARENT
        //    ticket's own submenu, then this generation ran backward
        //    (e.g. DO "unshipped" -> SO). In that case the new ticket must
        //    NOT re-check the forward rule pointing back into the parent's
        //    submenu, or it would immediately loop a checker back to where
        //    we just came from (SO -> DO -> SO -> ...). We still let the new
        //    ticket's own backward rule fire normally in case the chain
        //    continues further back (AR -> DO -> SO). ──
        $rule = \App\Models\TicketDependencyRule::find($ruleId);
        $isReverse = $rule
            && $rule->is_bidirectional
            && (string) $submenuId === (string) $rule->source_sub_menu_id;

        Log::info('[TicketGeneration] Rule fired, awaiting details on tambah_generated page', [
            'approval_id'        => $approval->id,
            'rule_id'            => $ruleId,
            'parent_ticket'      => $parentTicket->nomor_ticket,
            'branch_sub_menu_id' => $submenuId,
            'checker'            => $checkerKaryawan->name,
            'is_reverse'         => $isReverse,
        ]);

        return [
            'status'  => self::AWAITING_DETAILS,
            'message' => 'Kondisi terpenuhi. Silakan lengkapi detail ticket untuk melanjutkan.',
            'ticket'  => null,
            'redirect' => [
                'menu_id'            => $menuId,
                'parent_ticket_id'   => $parentTicket->id,
                'submenu_id'         => $submenuId,
                'approval_id'        => $approval->id,
                // Only set when this generation itself ran in reverse — the
                // submenu of the parent ticket, so the new ticket's own
                // forward rule back into it gets skipped.
                'exclude_submenu_id' => $isReverse ? $parentTicket->ticket_sub_menu_id : null,
            ],
        ];
    }

    /**
     * Mark an approval as SKIPPED (checker answered FALSE for that rule).
     *
     * @param  \App\Models\TicketApproval $approval
     * @return void
     */
    public function markSkipped(TicketApproval $approval): void
    {
        $approval->update(['generate_status' => self::SKIPPED]);
        Log::info('[TicketGeneration] Marked SKIPPED', ['approval_id' => $approval->id]);
    }
}
