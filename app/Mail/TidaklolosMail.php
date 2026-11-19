<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TidaklolosMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $nama;
    public $e_holding;

    public function __construct($nama, $e_holding)
    {
        $this->nama = $nama;
        $this->e_holding = $e_holding;
    }
    public function build()
    {
        return $this->view('admin.recruitment-users.ranking.email_tidak_lolos');
    }
}
