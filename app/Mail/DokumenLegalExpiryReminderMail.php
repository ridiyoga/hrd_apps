<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DokumenLegalExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $recipientName,
        public readonly Collection $documents,
        public readonly string $actionUrl,
    ) {
    }

    public function build(): self
    {
        return $this
            ->from(
                config('mail.mailers.hrd.from.address', 'no_reply@beraskediri.com'),
                config('mail.mailers.hrd.from.name', 'HRD Mailers')
            )
            ->subject('Pengingat Dokumen Legal - '.$this->documents->count().' dokumen perlu dicek')
            ->view('emails.dokumen-legal-expiry-reminder');
    }
}
