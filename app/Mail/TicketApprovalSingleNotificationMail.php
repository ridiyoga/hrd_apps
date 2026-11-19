<?php

namespace App\Mail;

use App\Models\TicketHead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketApprovalSingleNotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    public TicketHead $ticket;
    public string $recipientName;
    public string $roleLabel;
    public string $actionUrl;
    public string $trigger;
    public function __construct(
        TicketHead $ticket,
        string $recipientName,
        string $roleLabel,
        string $actionUrl,
        string $trigger
    ) {
        $this->ticket = $ticket;
        $this->recipientName = $recipientName;
        $this->roleLabel = $roleLabel;
        $this->actionUrl = $actionUrl;
        $this->trigger = $trigger;
    }
    public function build()
    {
        $subject = match ($this->trigger) {
            'feedback'          => 'Feedback / Revisi Ticket - ' . $this->ticket->nomor_ticket,
            'waiting_requestor' => 'Ticket Telah Selesai Dikerjakan (Perlu Cek Pengaju) - ' . $this->ticket->nomor_ticket,
            'assigned'          => 'Penugasan PIC Ticket - ' . $this->ticket->nomor_ticket,
            'reassigned'        => 'Pengalihan / Penugasan PIC Ticket - ' . $this->ticket->nomor_ticket,
            'validated'         => 'Ticket Siap di-Assign - ' . $this->ticket->nomor_ticket,
            default             => 'Ticket Menunggu ' . $this->roleLabel . ' - ' . $this->ticket->nomor_ticket,
        };

        return $this->subject($subject)
            ->view('emails.ticket-approval-notification-single')
            ->with([
                'ticket'        => $this->ticket,
                'recipientName' => $this->recipientName,
                'roleLabel'     => $this->roleLabel,
                'actionUrl'     => $this->actionUrl,
                'trigger'       => $this->trigger,
            ]);
    }
}
