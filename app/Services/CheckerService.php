<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\TicketApproval;
use App\Models\TicketApprovalComment;
use App\Models\TicketApprovalValidator;
use App\Models\TicketConditionValue;
use App\Models\TicketDependencyRule;
use App\Models\TicketHead;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class CheckerService
{
    protected DependencyEngine $dependencyEngine;
    protected GeneratedTicketService $generatedTicketService;

    public function __construct(
        DependencyEngine $dependencyEngine,
        GeneratedTicketService $generatedTicketService
    ) {
        $this->dependencyEngine       = $dependencyEngine;
        $this->generatedTicketService = $generatedTicketService;
    }

    /**
     * Process a checker's review submission for a ticket.
     *
     * @param TicketHead $ticket The parent ticket being checked
     * @param User $user Logged-in checker user
     * @param Karyawan $userKaryawan Karyawan profile of logged in user
     * @param array $approvalSubjectIds Karyawan IDs that the user represents
     * @param array $conditions Array of condition answers from the checker form
     * @param string $signaturePath Path to saved signature
     * @param string|null $comment Optional comment text
     * @param string|null $menuId Current active menu ID
     * @param array $selectedDetails Array mapping rule_id => [selected detail IDs]
     * @param array $copyFullData Array mapping rule_id => bool, whether to also clone
     *                            sub-detail rows (line_number, alasan_ubah) for that rule's
     *                            selected details. before_ubah/after_ubah are never cloned.
     * @return array
     */
    public function processCheckerSubmission(
        TicketHead $ticket,
        User $user,
        Karyawan $userKaryawan,
        array $approvalSubjectIds,
        array $conditions,
        string $signaturePath,
        ?string $comment = null,
        ?string $menuId = null,
        array $selectedDetails = [],
        array $copyFullData = [],
        bool $allLinesCancelled = false
    ): array {
        return DB::transaction(function () use (
            $ticket,
            $user,
            $userKaryawan,
            $approvalSubjectIds,
            $conditions,
            $signaturePath,
            $comment,
            $menuId,
            $selectedDetails,
            $copyFullData,
            $allLinesCancelled
        ) {
            $freshTicket = TicketHead::lockForUpdate()->find($ticket->id);
            if (!$freshTicket) {
                throw new \Exception('Ticket tidak ditemukan.');
            }
            // Find current user's checker approval rows
            $myApprovals = TicketApproval::where('ticket_id', $freshTicket->id)
                ->where('role', 'checker')
                ->whereIn('karyawan_id', $approvalSubjectIds)
                ->get();
            if ($myApprovals->isEmpty()) {
                // If no pre-assigned row, create a generic checker approval record
                $myApprovals = collect([
                    TicketApproval::create([
                        'ticket_id'     => $freshTicket->id,
                        'karyawan_id'   => $userKaryawan->id,
                        'karyawan_name' => $userKaryawan->name,
                        'role'          => 'checker',
                        'status'        => 'completed',
                        'approved_at'   => now(),
                        'signature'     => $signaturePath,
                        'comment'       => $comment,
                    ])
                ]);
            } else {
                $myApprovals->each(function ($app) use ($signaturePath, $comment) {
                    $app->update([
                        'status'      => 'completed',
                        'approved_at' => now(),
                        'signature'   => $signaturePath,
                        'comment'     => $comment,
                    ]);
                });
            }
            // Simpan catatan/komentar checker ke ticket_approval_comment untuk riwayat & timeline
            if (!empty(trim($comment ?? ''))) {
                $jabatan = KaryawanJabatan::where('karyawan_id', $userKaryawan->id)
                    ->where('is_active', 1)
                    ->where('main', 1)
                    ->first();

                TicketApprovalComment::create([
                    'id'             => (string) Uuid::uuid4(),
                    'ticket_id'      => $freshTicket->id,
                    'approval_level' => 'checker',
                    'karyawan_id'    => $userKaryawan->id,
                    'jabatan_id'     => $jabatan?->id,
                    'action_ticket'  => 'approve',
                    'comment_text'   => trim($comment),
                    'comment_at'     => now(),
                ]);
            }

            $pendingDraftQueue = [];
            $generatedCount    = 0;
            $failedCount       = 0;
            foreach ($myApprovals as $approval) {
                $ruleId = $approval->dependency_rule_id;
                if (!$ruleId) {
                    continue;
                }
                $rule = TicketDependencyRule::find($ruleId);
                if (!$rule) {
                    continue;
                }
                // Check condition answer submitted for this rule
                $conditionKey = $rule->condition_key;
                $isTrueAnswer = false;
                if ($conditionKey && isset($conditions[$conditionKey])) {
                    $val = $conditions[$conditionKey];
                    $isTrueAnswer = ($val === true || $val === '1' || $val === 1 || $val === 'true');
                } elseif (isset($conditions[$ruleId])) {
                    $val = $conditions[$ruleId];
                    $isTrueAnswer = ($val === true || $val === '1' || $val === 1 || $val === 'true');
                }
                // Save or update condition value in ticket_hasil_cek
                if ($conditionKey) {
                    TicketConditionValue::updateOrCreate(
                        [
                            'ticket_id'          => $freshTicket->id,
                            'condition_key'      => $conditionKey,
                            'dependency_rule_id' => $ruleId,
                        ],
                        [
                            'condition_value' => $isTrueAnswer,
                            'checked_by'      => $user->id,
                            'checked_at'      => now(),
                        ]
                    );
                }
                // Evaluate whether rule should fire (Mandatory Forward or Conditional = TRUE)
                $shouldFire = !$allLinesCancelled
                    && $this->dependencyEngine->shouldRuleFire($rule, $isTrueAnswer);
                if (!$shouldFire) {
                    $this->generatedTicketService->markSkipped($approval);
                    continue;
                }
                // Selected detail IDs for this specific rule (Kondisi 1: specific details, Kondisi 2: empty array)
                $ruleSelectedDetailIds = $selectedDetails[$ruleId] ?? $selectedDetails[$conditionKey] ?? [];
                if (!is_array($ruleSelectedDetailIds)) {
                    $ruleSelectedDetailIds = [];
                }
                // Apakah checker mengaktifkan toggle "Salin Data Lengkap" untuk rule ini.
                // Hanya berlaku jika ada detail yang dipilih; before_ubah/after_ubah tetap
                // tidak pernah ikut disalin (lihat GeneratedTicketService).
                $ruleCopyFullData = !empty($copyFullData[$ruleId]) || (!empty($conditionKey) && !empty($copyFullData[$conditionKey]));
                // Create Draft Ticket with Checker as Requestor & clone selected details (if any)
                $result = $this->generatedTicketService->createDraftFromApproval($approval, $user, $ruleSelectedDetailIds, $ruleCopyFullData);
                if ($result['status'] === GeneratedTicketService::STATUS_AWAITING_DETAILS && $result['draftTicket']) {
                    $generatedCount++;
                    $pendingDraftQueue[] = [
                        'draft_ticket_id' => $result['draftTicket']->id,
                        'menu_id'         => $menuId,
                        'parent_ticket_id' => $freshTicket->id,
                        'submenu_id'      => $result['draftTicket']->ticket_sub_menu_id,
                        'approval_id'     => $approval->id,
                    ];
                } elseif ($result['status'] === GeneratedTicketService::STATUS_FAILED) {
                    $failedCount++;
                }
            }
            // Check if all internal (non-cross-environment) checkers on parent ticket are done
            $remainingCheckersCount = TicketApproval::where('ticket_id', $freshTicket->id)
                ->where('role', 'checker')
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->whereNull('dependency_rule_id')
                        ->orWhereHas('dependencyRule', function ($q2) {
                            $q2->where('is_cross_environment', false);
                        });
                })
                ->count();
            ActivityLog::create([
                'user_id'     => $user->id,
                'user_name'   => $user->username,
                'menu_id'     => null,
                'menu_name'   => 'Approval Ticket',
                'id_table'    => $freshTicket->id,
                'holding_id'  => '-',
                'holding_category' => '-',
                'table'       => 'ticket_head',
                'activity'    => 'Checking Ticket',
                'description' => 'Ticket ' . $freshTicket->nomor_ticket . ' berhasil diperiksa oleh ' . $userKaryawan->name,
                'keterangan'  => 'Success',
                'karir'       => '2',
            ]);
            $hasPendingGeneratedTickets = $generatedCount > 0;
            if ($remainingCheckersCount === 0) {
                $environment = $freshTicket->ticketMenu?->kategori;
                if (empty($environment)) {
                    $nomor = (string) $freshTicket->nomor_ticket;
                    if (str_contains($nomor, 'EPIC')) {
                        $environment = 1;
                    } elseif (str_contains($nomor, 'AOL-SP')) {
                        $environment = 2;
                    } elseif (str_contains($nomor, 'AOL-AJA')) {
                        $environment = 3;
                    }
                }

                $hasValidator = !empty($freshTicket->validator_id)
                    || TicketApproval::where('ticket_id', $freshTicket->id)->where('role', 'validator')->exists()
                    || TicketApprovalValidator::where('is_active', true)
                    ->where(function ($query) use ($environment) {
                        $query->whereNull('environment');
                        if (!is_null($environment)) {
                            $query->orWhere('environment', $environment);
                        }
                    })
                    ->exists();

                $newStatus = $hasPendingGeneratedTickets
                    ? TicketHead::STATUS_WAITING_CHECKER
                    : ($hasValidator ? TicketHead::STATUS_WAITING_VALIDATOR : TicketHead::STATUS_APPROVED);
                $updateData = [
                    'checker_id'        => $userKaryawan->id,
                    'checked'           => true,
                    'checked_at'        => now(),
                    'checker_signature' => $signaturePath,
                    'checker_name'      => $userKaryawan->name,
                    'approval_status'   => TicketHead::APPROVAL_APPROVED,
                    'is_draft'          => false,
                ];
                if (!in_array($freshTicket->ticket_status, [
                    TicketHead::STATUS_ASSIGNED,
                    TicketHead::STATUS_IN_PROGRESS,
                    TicketHead::STATUS_WAITING_REQUESTOR,
                    TicketHead::STATUS_CLOSED,
                    TicketHead::STATUS_FEEDBACK_REQUESTOR,
                    TicketHead::STATUS_CANCELLED,
                ])) {
                    $updateData['ticket_status'] = $newStatus;
                }
                $freshTicket->update($updateData);
                // Sync status seluruh rombongan tiket dalam grup jika seluruhnya sudah selesai disubmit & diperiksa
                app(TicketChainService::class)->syncGroupStatusToValidatorOrApproved($freshTicket);
            }
            return [
                'pendingCheckers'   => $remainingCheckersCount > 0,
                'generatedCount'    => $generatedCount,
                'failedCount'       => $failedCount,
                'pendingDraftQueue' => $pendingDraftQueue,
                'parentTicket'      => $freshTicket,
            ];
        });
    }
}
