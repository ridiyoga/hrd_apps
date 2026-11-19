<?php

namespace App\Mail;

use App\Models\TicketHead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TicketApprovalGroupNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public TicketHead $ticket;
    public Collection $groupTickets;
    public string $recipientName;
    public string $roleLabel;
    public string $actionUrl;
    public string $trigger;

    public function __construct(
        TicketHead $ticket,
        Collection $groupTickets,
        string $recipientName,
        string $roleLabel,
        string $actionUrl,
        string $trigger
    ) {
        $this->ticket = $ticket;
        $this->groupTickets = $groupTickets;
        $this->recipientName = $recipientName;
        $this->roleLabel = $roleLabel;
        $this->actionUrl = $actionUrl;
        $this->trigger = $trigger;
    }

    public function build()
    {
        return $this->subject('Ticket Menunggu ' . $this->roleLabel . ' - ' . ($this->ticket->rootTicket?->nomor_ticket ?? $this->ticket->nomor_ticket))
            ->view('emails.ticket-approval-notification-group')
            ->with([
                'ticket' => $this->ticket,
                'groupTickets' => $this->groupTickets,
                'recipientName' => $this->recipientName,
                'roleLabel' => $this->roleLabel,
                'actionUrl' => $this->actionUrl,
                'trigger' => $this->trigger,
            ]);
    }
}
