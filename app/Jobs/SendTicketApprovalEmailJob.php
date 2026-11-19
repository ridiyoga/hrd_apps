<?php

namespace App\Jobs;

use App\Mail\TicketApprovalGroupNotificationMail;
use App\Mail\TicketApprovalSingleNotificationMail;
use App\Models\Menu;
use App\Models\TicketHead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTicketApprovalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah maksimum percobaan eksekusi jika terjadi temporary failure.
     */
    public int $tries = 3;

    /**
     * Jeda waktu antar retry dalam detik (Incremental/Exponential backoff).
     * Attempt 1 -> 10s, Attempt 2 -> 30s, Attempt 3 -> 60s
     */
    public array $backoff = [10, 30, 60];

    /**
     * Timeout job dalam detik.
     */
    public int $timeout = 60;

    public string $ticketId;
    public array $recipient;
    public ?string $menuId;
    public string $trigger;
    public string $kind;

    /**
     * Create a new job instance.
     *
     * @param string      $ticketId
     * @param array       $recipient  Array with keys: karyawan_id, email, name, role, label
     * @param string|null $menuId
     * @param string      $trigger
     * @param string      $kind       'single' | 'group'
     */
    public function __construct(
        string $ticketId,
        array $recipient,
        ?string $menuId,
        string $trigger,
        string $kind = 'single'
    ) {
        $this->afterCommit();
        $this->ticketId  = $ticketId;
        $this->recipient = $recipient;
        $this->menuId    = $menuId;
        $this->trigger   = $trigger;
        $this->kind      = $kind;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ticket = TicketHead::with(['ticketMenu', 'ticketSubMenu', 'ticketDetails.subDetails', 'ticketDetails.referenceTicketDetail', 'rootTicket'])
            ->find($this->ticketId);

        if (!$ticket) {
            Log::warning('SendTicketApprovalEmailJob: Ticket not found or deleted, skipping email.', [
                'ticket_id' => $this->ticketId,
                'recipient' => $this->recipient['email'] ?? null,
                'trigger'   => $this->trigger,
            ]);
            return;
        }

        $email = trim((string) ($this->recipient['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('SendTicketApprovalEmailJob: Invalid recipient email address.', [
                'ticket_id' => $this->ticketId,
                'recipient' => $this->recipient,
                'trigger'   => $this->trigger,
            ]);
            return;
        }

        $role      = $this->recipient['role'] ?? '';
        $name      = $this->recipient['name'] ?? '-';
        $label     = $this->recipient['label'] ?? '-';
        $actionUrl = $this->buildActionUrl($ticket, $this->menuId, $role);

        Log::info('Ticket approval email sending attempt', [
            'ticket_id'    => $ticket->id,
            'nomor_ticket' => $ticket->nomor_ticket,
            'recipient'    => $email,
            'role'         => $role,
            'trigger'      => $this->trigger,
            'kind'         => $this->kind,
            'attempt'      => $this->attempts(),
        ]);

        if ($this->kind === 'group') {
            $groupTickets = $this->resolveTicketGroup($ticket);
            Mail::mailer('ticket')->to($email)->send(new TicketApprovalGroupNotificationMail(
                $ticket,
                $groupTickets,
                $name,
                $label,
                $actionUrl,
                $this->trigger
            ));
        } else {
            Mail::mailer('ticket')->to($email)->send(new TicketApprovalSingleNotificationMail(
                $ticket,
                $name,
                $label,
                $actionUrl,
                $this->trigger
            ));
        }

        Log::info('Ticket approval email sent successfully', [
            'ticket_id'    => $ticket->id,
            'nomor_ticket' => $ticket->nomor_ticket,
            'recipient'    => $email,
            'role'         => $role,
            'trigger'      => $this->trigger,
            'kind'         => $this->kind,
            'attempt'      => $this->attempts(),
        ]);
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Ticket approval email failed permanently after all retries', [
            'ticket_id'  => $this->ticketId,
            'recipient'  => $this->recipient['email'] ?? null,
            'role'       => $this->recipient['role'] ?? null,
            'trigger'    => $this->trigger,
            'kind'       => $this->kind,
            'attempts'   => $this->attempts(),
            'exception'  => $exception?->getMessage(),
        ]);
    }

    private function resolveTicketGroup(TicketHead $ticket): Collection
    {
        $rootTicketId = $ticket->root_ticket_id ?: $ticket->id;
        return TicketHead::with(['ticketMenu', 'ticketSubMenu', 'ticketDetails'])
            ->where(function ($query) use ($rootTicketId) {
                $query->where('id', $rootTicketId)
                    ->orWhere('root_ticket_id', $rootTicketId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->values();
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
