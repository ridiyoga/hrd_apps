<?php

namespace App\Mail;

use App\Models\TicketVendorHead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketVendorApprovalNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TicketVendorHead $vendor,
        public string $recipientName,
        public string $roleLabel,
        public string $actionUrl,
        public string $trigger
    ) {}

    public function build(): self
    {
        return $this
            ->from(
                config('mail.mailers.hrd.from.address', 'no_reply@beraskediri.com'),
                config('mail.mailers.hrd.from.name', 'HRD Mailers')
            )
            ->subject('Vendor ' . $this->vendor->nomor_pengajuan_fis . ' menunggu ' . $this->roleLabel)
            ->view('emails.vendor-approval-notification');
    }
}
