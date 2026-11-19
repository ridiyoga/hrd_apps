<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiterimaManagerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $nama;
    public $hari;
    public $pukul;
    public $tempat;
    public $maps;
    public $user_id;

    public function __construct($nama, $hari, $pukul, $tempat, $maps, $user_id)
    {
        $this->nama = $nama;
        $this->hari = $hari;
        $this->pukul = $pukul;
        $this->tempat = $tempat;
        $this->maps = $maps;
        $this->user_id = $user_id;
    }
    public function build()
    {
        return $this->view('admin.recruitment-users.ranking.email_wawancara_manager');
    }
}
