<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KaryawanPenggantiMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $nama_pengganti;
    public $nama_cuti;
    public $tanggal_mulai;
    public $tanggal_selesai;

    public function __construct($nama_pengganti, $nama_cuti, $tanggal_mulai, $tanggal_selesai)
    {
        $this->nama_cuti = $nama_cuti;
        $this->nama_pengganti = $nama_pengganti;
        $this->tanggal_mulai = $tanggal_mulai;
        $this->tanggal_selesai = $tanggal_selesai;
    }
    public function build()
    {
        return $this->view('admin.karyawan.cuti.cuti_pengganti_email');
    }
}
