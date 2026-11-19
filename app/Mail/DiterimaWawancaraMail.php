<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiterimaWawancaraMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $nama;
    public $pukul;
    public $tempat_wawancara;
    public $maps;
    public $e_holding;
    public $hari;
    public $user_id;

    public function __construct($nama, $pukul, $tempat_wawancara, $maps, $e_holding, $hari, $user_id)
    {
        $this->nama = $nama;
        $this->pukul = $pukul;
        $this->tempat_wawancara = $tempat_wawancara;
        $this->maps = $maps;
        $this->e_holding = $e_holding;
        $this->hari = $hari;
        $this->user_id = $user_id;
    }
    public function build()
    {
        return $this->view('admin.recruitment-users.recruitment.user_detail_email');
    }
}
