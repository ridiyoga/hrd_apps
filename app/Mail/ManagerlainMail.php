<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManagerlainMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $jabatan;
    public $divisi;
    public $gaji;
    public $nama;
    public $pukul;
    public $tempat;
    public $maps;
    public $e_holding;
    public $hari;
    public $user_id;

    public function __construct($nama, $e_holding, $jabatan, $divisi, $gaji, $hari, $pukul, $tempat, $maps, $user_id)
    {
        $this->nama = $nama;
        $this->e_holding = $e_holding;
        $this->jabatan = $jabatan;
        $this->divisi = $divisi;
        $this->gaji = $gaji;
        $this->hari = $hari;
        $this->pukul = $pukul;
        $this->tempat = $tempat;
        $this->maps = $maps;
        $this->user_id = $user_id;
    }
    public function build()
    {
        return $this->view('admin.recruitment-users.ranking.email_manager_lain');
    }
}
