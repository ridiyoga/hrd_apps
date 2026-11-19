<?php

namespace App\Services;

use App\Jobs\SendTicketApprovalEmailJob;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\TicketApproval;
use App\Models\TicketApprovalValidator;
use App\Models\TicketAssignmentMaster;
use App\Models\TicketHead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TicketApprovalEmailService
{
    private const GROUP_ROLES = ['validator', 'assigner'];
    public function sendNextActionNotification(TicketHead $ticket, ?string $menuId = null, string $trigger = 'submitted'): void
    {
        if ($ticket->exists) {
            $ticket->refresh();
            $ticket->loadMissing(['ticketMenu', 'ticketSubMenu', 'ticketDetails.subDetails', 'ticketDetails.referenceTicketDetail']);
        }
        $recipients = $this->resolveNextRecipients($ticket, $trigger)
            ->unique(fn($r) => ($r['karyawan_id'] ?? '') . '_' . strtolower($r['email']) . '_' . ($r['name'] ?? ''));
        if ($recipients->isEmpty()) {
            Log::info('Ticket approval email: no recipients resolved', [
                'ticket_id'       => $ticket->id,
                'nomor_ticket'    => $ticket->nomor_ticket,
                'ticket_status'   => $ticket->ticket_status,
                'approval_status' => $ticket->approval_status,
                'approve1_id'     => $ticket->approve1_id,
                'approve2_id'     => $ticket->approve2_id,
                'checker_id'      => $ticket->checker_id,
                'trigger'         => $trigger,
            ]);
            return;
        }
        $groupRecipients  = $recipients->filter(fn($r) => in_array($r['role'], self::GROUP_ROLES, true));
        $singleRecipients = $recipients->filter(fn($r) => !in_array($r['role'], self::GROUP_ROLES, true));
        if ($groupRecipients->isNotEmpty()) {
            $groupRecipients->each(function (array $recipient) use ($ticket, $menuId, $trigger) {
                SendTicketApprovalEmailJob::dispatch($ticket->id, $recipient, $menuId, $trigger, 'group')->afterCommit();
                Log::info('Ticket approval group email queued', [
                    'ticket_id'    => $ticket->id,
                    'nomor_ticket' => $ticket->nomor_ticket,
                    'role'         => $recipient['role'],
                    'email'        => $recipient['email'],
                    'trigger'      => $trigger,
                ]);
            });
        }

        $singleRecipients->each(function (array $recipient) use ($ticket, $menuId, $trigger) {
            SendTicketApprovalEmailJob::dispatch($ticket->id, $recipient, $menuId, $trigger, 'single')->afterCommit();
            Log::info('Ticket approval single email queued', [
                'ticket_id'    => $ticket->id,
                'nomor_ticket' => $ticket->nomor_ticket,
                'role'         => $recipient['role'],
                'email'        => $recipient['email'],
                'trigger'      => $trigger,
            ]);
        });
    }

    protected function resolveTicketGroup(TicketHead $ticket): Collection
    {
        $rootTicketId = $ticket->root_ticket_id ?: $ticket->id;
        return TicketHead::with(['ticketMenu', 'ticketSubMenu', 'ticketDetails.subDetails', 'ticketDetails.referenceTicketDetail'])
            ->where(function ($query) use ($rootTicketId) {
                $query->where('id', $rootTicketId)
                    ->orWhere('root_ticket_id', $rootTicketId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->values();
    }

    /**
     * Check whether ALL tickets and checkers in the entire chain group
     * are completely approved and ready for Validator.
     */
    public function isChainFullyApprovedAndChecked(TicketHead $ticket): bool
    {
        $rootTicketId = $ticket->root_ticket_id ?: $ticket->id;
        $groupTickets = TicketHead::where(function ($query) use ($rootTicketId) {
            $query->where('id', $rootTicketId)
                ->orWhere('root_ticket_id', $rootTicketId);
        })
        ->where('ticket_status', '!=', TicketHead::STATUS_CANCELLED)
        ->where('approval_status', '!=', TicketHead::APPROVAL_REJECTED)
        ->get();

        foreach ($groupTickets as $t) {
            // 1. If any ticket in chain is still in draft / not submitted
            if ((bool) $t->is_draft || !(bool) $t->is_submit || $t->ticket_status === TicketHead::STATUS_DRAFT) {
                return false;
            }
            // 2. If approval_status is not approved
            if ($t->approval_status !== TicketHead::APPROVAL_APPROVED) {
                return false;
            }
            // 3. If any approver 1 is still pending
            if ($t->approve1_id && !(bool) $t->approve1) {
                return false;
            }
            // 4. If any approver 2 is still pending
            if ($t->approve2_id && !(bool) $t->approve2) {
                return false;
            }
            // 5. If any internal (non-cross-environment) checkers in ticket_approvals are still pending
            $hasPendingCheckers = TicketApproval::where('ticket_id', $t->id)
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
                ->exists();

            if ($hasPendingCheckers) {
                return false;
            }

            // A TRUE checker condition opens a generated-ticket form first.
            // Until that draft is submitted, the chain must not reach Validator.
            $hasPendingGeneratedTickets = TicketApproval::where('ticket_id', $t->id)
                ->where('role', 'checker')
                ->whereNotNull('dependency_rule_id')
                ->whereIn('generate_status', [
                    TicketGenerationService::PENDING,
                    TicketGenerationService::PROCESSING,
                ])
                ->exists();

            if ($hasPendingGeneratedTickets) {
                return false;
            }
        }

        return true;
    }
    protected function resolveNextRecipients(TicketHead $ticket, string $trigger = ''): Collection
    {
        // 0. If explicitly triggered for PIC assignment / reassignment
        if (in_array($trigger, ['assigned', 'reassigned'], true) && $ticket->pic_id) {
            return $this->collectSingleRecipient($ticket->pic_id, 'pic', 'PIC');
        }
        // If ticket is closed or cancelled
        if ($ticket->ticket_status === TicketHead::STATUS_CLOSED || $ticket->ticket_status === TicketHead::STATUS_CANCELLED) {
            return collect();
        }
        // 1. If PIC finished work -> notify Requestor for review/feedback
        if ($ticket->ticket_status === TicketHead::STATUS_WAITING_REQUESTOR) {
            $user = User::find($ticket->requestor_id);
            $karyawan = $user ? $this->resolveKaryawanBySubject($user->karyawan_id ?: $user->id) : $this->resolveKaryawanBySubject($ticket->requestor_id);
            $recipient = $this->recipientFromKaryawan($karyawan, 'requestor', 'Requestor / Pemohon');
            return collect($recipient ? [$recipient] : []);
        }
        // 2. If Requestor sent feedback back to PIC -> notify PIC
        if ($ticket->ticket_status === TicketHead::STATUS_FEEDBACK_REQUESTOR && $ticket->pic_id) {
            return $this->collectSingleRecipient($ticket->pic_id, 'pic', 'PIC');
        }
        // 3. Approver 1
        $approve1Done = is_null($ticket->approve1_id) || (bool) $ticket->approve1;
        $approve2Done = is_null($ticket->approve2_id) || (bool) $ticket->approve2;
        if (!$approve1Done && $ticket->approve1_id) {
            return $this->collectSingleRecipient($ticket->approve1_id, 'approve1', 'Approver 1');
        }
        // 4. Approver 2
        if ($approve1Done && !$approve2Done && $ticket->approve2_id) {
            return $this->collectSingleRecipient($ticket->approve2_id, 'approve2', 'Approver 2');
        }
        // 5. Checkers
        $pendingCheckers = $ticket->checkers()
            ->where(function ($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })
            ->with(['karyawan', 'branchSubMenu.ticketMenu'])
            ->get();
        if ($approve1Done && $approve2Done && ($pendingCheckers->isNotEmpty() || ($ticket->checker_id && !(bool) $ticket->checked))) {
            $defaultMenuName = $ticket->ticketMenu?->nama_menu;
            if ($pendingCheckers->isNotEmpty()) {
                return $pendingCheckers
                    ->map(function ($approval) use ($defaultMenuName) {
                        $karyawan       = $approval->karyawan ?: $this->resolveKaryawanBySubject($approval->karyawan_id);
                        $branchMenuName = $approval->branchSubMenu?->ticketMenu?->nama_menu ?: $defaultMenuName;
                        $checkerLabel   = $branchMenuName ? 'Checker (' . $branchMenuName . ')' : 'Checker';
                        return $this->recipientFromKaryawan($karyawan, 'checker', $checkerLabel, $approval->karyawan_id);
                    })
                    ->filter()
                    ->values();
            }
            if ($ticket->checker_id && !(bool) $ticket->checked) {
                $checkerLabel = $defaultMenuName ? 'Checker (' . $defaultMenuName . ')' : 'Checker';
                return $this->collectSingleRecipient($ticket->checker_id, 'checker', $checkerLabel);
            }
        }
        // 6. Validator (ONLY IF THE ENTIRE CHAIN GROUP HAS FINISHED ALL CHECKERS & APPROVALS!)
        if ($ticket->approval_status === TicketHead::APPROVAL_APPROVED && !(bool) $ticket->validator) {
            if ($this->isChainFullyApprovedAndChecked($ticket)) {
                return $this->collectValidatorRecipients($ticket);
            }
            // If chain is not fully completed yet, don't send to validator
            return collect();
        }
        // 7. Assigner (AFTER validator has validated, but not yet assigned to PIC)
        if ((bool) $ticket->validator && !(bool) $ticket->assign) {
            return $this->collectAssignerRecipients($ticket);
        }
        // 8. PIC (AFTER assigner has assigned to PIC)
        if ((bool) $ticket->assign && $ticket->pic_id) {
            return $this->collectSingleRecipient($ticket->pic_id, 'pic', 'PIC');
        }
        return collect();
    }

    private function collectSingleRecipient(?string $subjectId, string $role, string $label): Collection
    {
        $recipient = $this->recipientFromKaryawan(
            $this->resolveKaryawanBySubject($subjectId),
            $role,
            $label,
            $subjectId
        );
        return collect($recipient ? [$recipient] : []);
    }

    private function collectValidatorRecipients(TicketHead $ticket): Collection
    {
        $environment = $ticket->ticketMenu?->kategori;
        return TicketApprovalValidator::with('karyawan')
            ->where('is_active', true)
            ->where(function ($query) use ($environment) {
                $query->whereNull('environment');
                if (!is_null($environment)) {
                    $query->orWhere('environment', $environment);
                }
            })
            ->get()
            ->map(fn(TicketApprovalValidator $validator) => $this->recipientFromKaryawan(
                $validator->karyawan,
                'validator',
                'Validator',
                $validator->karyawan_id
            ))
            ->filter()
            ->values();
    }

    private function collectAssignerRecipients(TicketHead $ticket): Collection
    {
        [$env] = $this->resolveEnvironmentKategori($ticket);

        return TicketAssignmentMaster::with('karyawan')
            ->where('assignment_type', 'A01')
            ->where('is_active', 1)
            ->forEnvironment($env) // environment NULL (global) atau sama dengan environment tiket
            ->get()
            ->map(fn(TicketAssignmentMaster $assigner) => $this->recipientFromKaryawan(
                $assigner->karyawan,
                'assigner',
                'Assigner',
                $assigner->karyawan_id
            ))
            ->filter()
            ->values();
    }

    private function recipientFromKaryawan(?Karyawan $karyawan, string $role, string $label, ?string $karyawanId = null): ?array
    {
        if (!$karyawan && $karyawanId) {
            $karyawan = $this->resolveKaryawanBySubject($karyawanId);
        }
        if (!$karyawan) {
            return null;
        }

        // Resolusi delegasi jika karyawan sedang cuti / sakit (hanya untuk approval/checker/validator, bukan PIC atau Assigner)
        if (!in_array($role, ['pic', 'assigner'], true)) {
            $delegasi = app(KaryawanPenggantiService::class)->cariKaryawanPenggantiAktif($karyawan);
            $karyawanAktif = $delegasi['karyawan'] ?: $karyawan;
        } else {
            $delegasi = ['is_pengganti' => false, 'karyawan_asli' => null];
            $karyawanAktif = $karyawan;
        }

        $email = trim((string) ($karyawanAktif->email_kantor ?? $karyawanAktif->email ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $resolvedLabel = $label;
        if ($delegasi['is_pengganti'] && $delegasi['karyawan_asli']) {
            $resolvedLabel = "{$label} (Pengganti an. {$delegasi['karyawan_asli']->name})";
        }

        return [
            'karyawan_id' => (string) ($karyawanAktif->id ?? $karyawanId ?? ''),
            'email'       => $email,
            'name'        => $karyawanAktif->name ?? '-',
            'role'        => $role,
            'label'       => $resolvedLabel,
        ];
    }

    private function resolveKaryawanBySubject(?string $subjectId): ?Karyawan
    {
        if (!$subjectId) {
            return null;
        }
        $karyawan = $this->findKaryawanByIdentifier((string) $subjectId);
        if ($karyawan) {
            return $karyawan;
        }
        $user = User::find($subjectId);
        if (!$user || !$user->karyawan_id) {
            return null;
        }
        return $this->findKaryawanByIdentifier((string) $user->karyawan_id);
    }

    private function findKaryawanByIdentifier(string $identifier): ?Karyawan
    {
        return Karyawan::where(function ($query) use ($identifier) {
            if (Uuid::isValid($identifier)) {
                $query->where('id', $identifier)
                    ->orWhere('nomor_identitas_karyawan', $identifier);
                return;
            }
            $query->where('nomor_identitas_karyawan', $identifier);
        })->first();
    }

    /**
     * Tentukan environment tiket (1 = Epicor, 2 = Accurate SP, 3 = Accurate AJA).
     * Disamakan dengan ApprovalController::resolveEnvironmentKategori().
     */
    private function resolveEnvironmentKategori(TicketHead $ticket): array
    {
        $kat   = (int) ($ticket->ticketMenu?->kategori ?? 0);
        $nomor = $ticket->nomor_ticket ?? '';

        if (str_contains($nomor, 'AOL-AJA')) {
            $kat = 3;
        } elseif (str_contains($nomor, 'AOL-SP') || str_contains($nomor, 'AOL-SPS')) {
            $kat = 2;
        } elseif (str_contains($nomor, 'EPIC')) {
            $kat = 1;
        } elseif ($kat === 0) {
            $kat = 1;
        }

        $name = match ($kat) {
            2       => 'Accurate Online SP',
            3       => 'Accurate Online AJA',
            default => 'Epicor ERP',
        };

        return [$kat, $name];
    }

    private function buildActionUrl(TicketHead $ticket, ?string $menuId, string $role): string
    {
        $resolvedMenuId = $menuId ?: $this->resolveApprovalMenuId();
        return match ($role) {
            'approve1', 'approve2' => route('approval.ticket.approve', [$resolvedMenuId, $ticket->id]),
            'checker'   => route('approval.ticket.checker', [$resolvedMenuId, $ticket->id]),
            'validator' => route('approval.ticket.validator.show', [$resolvedMenuId, $ticket->id]),
            'assigner'  => route('approval.ticket.assign.index', [$resolvedMenuId]),
            'pic'       => route('approval.ticket.pic.index', [$resolvedMenuId]),
            'requestor' => route('ticket.lihat', [$resolvedMenuId, $ticket->id]),
            default     => route('ticket.lihat', [$resolvedMenuId, $ticket->id]),
        };
    }

    private function resolveApprovalMenuId(): int
    {
        return (int) (
            Menu::where('url', 'approval/ticket/menu')->value('id')
            ?? Menu::where('url', 'like', 'approval/ticket%')->value('id')
            ?? 0
        );
    }
}