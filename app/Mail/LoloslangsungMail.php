<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoloslangsungMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $nama;
    public $jabatan;
    public $divisi;
    public $e_holding;
    public $gaji;
    public $hari;
    public $pukul;
    public $tempat;
    public $maps;
    public $user_id;

    public function __construct($nama, $jabatan, $divisi, $e_holding, $gaji, $hari, $pukul, $tempat, $maps, $user_id)
    {
        $this->nama = $nama;
        $this->jabatan = $jabatan;
        $this->divisi = $divisi;
        $this->e_holding = $e_holding;
        $this->gaji = $gaji;
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
