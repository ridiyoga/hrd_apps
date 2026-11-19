<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LolosposisilainMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $nama;
    public $e_holding;
    public $jabatan;
    public $divisi;
    public $pukul;
    public $hari;
    public $tempat;
    public $maps;
    public $user_id;

    public function __construct($nama, $e_holding, $jabatan, $divisi, $pukul, $hari, $tempat, $maps, $user_id)
    {
        $this->nama = $nama;
        $this->e_holding = $e_holding;
        $this->jabatan = $jabatan;
        $this->divisi = $divisi;
        $this->hari = $hari;
        $this->pukul = $pukul;
        $this->tempat = $tempat;
        $this->maps = $maps;
        $this->user_id = $user_id;
    }
    public function build()
    {
        return $this->view('admin.recruitment-users.ranking.email_lolos_langsung');
    }
}
