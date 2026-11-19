<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SemuaIzin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $judul;
    public $nama_penerima;
    public $perihal;
    public $nama;
    public $waktu;
    public $penutup;
    public $link;

    public function __construct(
        $judul,
        $nama_penerima,
        $perihal,
        $nama,
        $waktu,
        $penutup,
        $link,
    ) {
        $this->judul = $judul;
        $this->nama_penerima = $nama_penerima;
        $this->perihal = $perihal;
        $this->nama = $nama;
        $this->waktu = $waktu;
        $this->penutup = $penutup;
        $this->link = $link;
    }
    public function build()
    {
        return $this->subject('Pemberitahuan')->view('admin.karyawan.izin.izin_email');
    }
}
