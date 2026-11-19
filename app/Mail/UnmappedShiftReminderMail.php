<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UnmappedShiftReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $supervisorName,
        public readonly Collection $employees,
        public readonly CarbonInterface $periodStart,
        public readonly CarbonInterface $periodEnd,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Pengingat Mapping Shift - '.$this->employees->count().' Karyawan')
            ->view('emails.unmapped-shift-reminder');
    }
}
