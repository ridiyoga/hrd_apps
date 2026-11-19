<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\TicketApproval;
use App\Models\TicketDependencyRule;
use App\Models\TicketHead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketSubmissionService
{
    /**
     * Submits a draft generated ticket, transitioning it from DRAFT (T00) to SUBMITTED (T01).
     *
     * @param TicketHead $draftTicket The draft ticket being completed and submitted
     * @param Request $request Form submission request
     * @param Karyawan $userKaryawan Currently logged-in karyawan profile
     * @return TicketHead
     */
    public function submitDraftGenerated(TicketHead $draftTicket, Request $request, Karyawan $userKaryawan): TicketHead
    {
        return DB::transaction(function () use ($draftTicket, $request, $userKaryawan) {
            $ticketController = app(\App\Http\Controllers\TicketController::class);
            $dependencyEngine = app(DependencyEngine::class);

            $submenuId          = $draftTicket->ticket_sub_menu_id;
            $selectedConditions = $request->input('selected_conditions', []);

            // ── Direction Inheritance: derive includeBackward from lineage, NOT from user input ──
            // Root ticket (dependency_rule_id = NULL): user's toggle is respected.
            // Generated ticket: direction is inherited from the edge that created it.
            //   - If the ticket was produced via BACKWARD traversal (rule.source == ticket.sub_menu)
            //     → continue backward (includeBackward = true)
            //   - If the ticket was produced via FORWARD traversal (rule.target == ticket.sub_menu)
            //     → continue forward only (includeBackward = false)
            // This prevents the UI include_backward_dependency toggle from accidentally
            // injecting reverse checkers into a forward-generated ticket (or vice versa).
            $userChoice      = $request->boolean('include_backward_dependency');
            $includeBackward = $dependencyEngine->deriveIncludeBackward($draftTicket, $userChoice);

            Log::info('[TicketSubmissionService] includeBackward derived from lineage', [
                'draft_ticket_id'    => $draftTicket->id,
                'dependency_rule_id' => $draftTicket->dependency_rule_id,
                'ticket_sub_menu_id' => $draftTicket->ticket_sub_menu_id,
                'include_backward'   => $includeBackward,
                'user_choice'        => $userChoice,
            ]);

            // Resolve approval steps & checkers for this draft ticket
            $approvalData = $ticketController->resolveApprovalDataPublic(
                $submenuId,
                $userKaryawan,
                $includeBackward,
                $selectedConditions,
                $draftTicket
            );

            $signaturePath = $request->has('signature') ? $ticketController->storeSignaturePublic($request) : $draftTicket->requestor_signature;
            $lampiranFile  = $request->hasFile('lampiran') ? $ticketController->storeLampiranPublic($request) : $draftTicket->lampiran;

            $initialStatus = $ticketController->determineInitialStatusPublic($approvalData, (string) $submenuId);

            // Resolve cross-environment status
            $isCrossEnv = (bool) ($draftTicket->is_cross_environment ?? false);
            if (!$isCrossEnv && $draftTicket->dependency_rule_id) {
                $rule = TicketDependencyRule::find($draftTicket->dependency_rule_id);
                if ($rule && $rule->is_cross_environment) {
                    $isCrossEnv = true;
                }
            }
            if (!$isCrossEnv && $draftTicket->parent_ticket_id) {
                $parentTicket = TicketHead::with('ticketMenu')->find($draftTicket->parent_ticket_id);
                $targetMenu = DB::table('ticket_menu')->where('id', $draftTicket->ticket_menu_id)->first();
                if ($parentTicket && $parentTicket->ticketMenu && $targetMenu) {
                    if ((int)($parentTicket->ticketMenu->kategori ?? 0) !== (int)($targetMenu->kategori ?? 0)) {
                        $isCrossEnv = true;
                    }
                }
            }
            $finalRootId = $isCrossEnv ? $draftTicket->id : ($draftTicket->root_ticket_id ?: $draftTicket->id);

            // Update Draft Ticket to Submitted status
            $draftTicket->update([
                'requestor_name'           => $userKaryawan->name,
                'requestor_signature'      => $signaturePath,
                'ticket_status'            => $initialStatus,
                'approval_status'          => $approvalData['auto_approved']
                    ? TicketHead::APPROVAL_APPROVED
                    : TicketHead::APPROVAL_PENDING,
                'is_draft'                 => false,
                'is_submit'                => true,
                'is_cross_environment'     => $isCrossEnv,
                'cross_env_status'         => $isCrossEnv ? TicketHead::CROSS_ENV_TRIGGERED : TicketHead::CROSS_ENV_NONE,
                'origin_cross_ticket_id'   => $isCrossEnv ? $draftTicket->parent_ticket_id : $draftTicket->origin_cross_ticket_id,
                'root_ticket_id'           => $finalRootId,
                'lampiran'                 => $lampiranFile,
                'approve1'                 => $approvalData['auto_approved'] ? true : null,
                'approve1_id'              => $approvalData['approve1_id'],
                'approve1_name'            => $approvalData['approve1_name'],
                'approve1_at'              => $approvalData['auto_approved'] ? now() : null,
                'approve2'                 => $approvalData['auto_approved'] ? true : null,
                'approve2_id'              => $approvalData['approve2_id'],
                'approve2_name'            => $approvalData['approve2_name'],
                'approve2_at'              => $approvalData['auto_approved'] ? now() : null,
                'checked'                  => 0,
                'checker_id'               => $approvalData['checker_id'],
                'checker_name'             => $approvalData['checker_name'],
                'validator'                => !empty($approvalData['auto_validated']) ? true : null,
                'validator_id'             => !empty($approvalData['auto_validated']) ? $userKaryawan->id : null,
                'validator_name'           => !empty($approvalData['auto_validated']) ? $userKaryawan->name : null,
                'validator_at'             => !empty($approvalData['auto_validated']) ? now() : null,
                'validator_signature'      => !empty($approvalData['auto_validated']) ? $signaturePath : null,
                'execution_status'         => !empty($approvalData['auto_validated']) ? TicketHead::EXEC_READY : TicketHead::EXEC_WAITING,
                'updated_at'               => now()->toDateTimeString(),
            ]);

            // Sync checkers and save detail rows
            $ticketController->syncTicketCheckersPublic($draftTicket->id, $approvalData['checkers'] ?? []);
            $ticketController->saveTicketDetailsPublic($draftTicket->id, $request);
            $ticketController->storeLampiranMultiplePublic($request, $draftTicket->id);

            // Update the parent's approval row status to SUCCESS now that ticket has been submitted
            if ($draftTicket->dependency_rule_id && $draftTicket->parent_ticket_id) {
                TicketApproval::where('ticket_id', $draftTicket->parent_ticket_id)
                    ->where('dependency_rule_id', $draftTicket->dependency_rule_id)
                    ->update([
                        'generate_status'     => GeneratedTicketService::STATUS_SUCCESS,
                        'generated_ticket_id' => $draftTicket->id,
                        'generated_at'        => now(),
                    ]);
            }

            // Sync status seluruh rombongan tiket dalam grup jika seluruhnya sudah selesai disubmit
            app(TicketChainService::class)->syncGroupStatusToValidatorOrApproved($draftTicket);

            $holdings        = Holding::where('id', $userKaryawan->kontrak_kerja)->first();
            $holdingId       = $holdings?->id ?? '-';
            $holdingCategory = $holdings?->name ?? '-';

            ActivityLog::create([
                'user_id'          => Auth::id(),
                'user_name'        => Auth::user()->username,
                'menu_id'          => null,
                'menu_name'        => 'Submit Generated Ticket',
                'id_table'         => $draftTicket->id,
                'holding_id'       => $holdingId,
                'holding_category' => $holdingCategory,
                'table'            => 'ticket_head',
                'activity'         => 'Submit Ticket Generated',
                'description'      => 'Ticket Generated ' . $draftTicket->nomor_ticket . ' berhasil diajukan.',
                'keterangan'       => 'Success',
                'karir'            => '2',
            ]);

            Log::info('[TicketSubmissionService] Draft generated ticket successfully submitted', [
                'ticket_id'    => $draftTicket->id,
                'nomor_ticket' => $draftTicket->nomor_ticket,
                'requestor_id' => $userKaryawan->id,
            ]);

            return $draftTicket;
        });
    }
}
