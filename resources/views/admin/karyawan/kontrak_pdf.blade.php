<!DOCTYPE html>
<html>

<head>
    <title>Kontrak
        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->name : '-') : '-')) }}
    </title>
    <style>
        @page {
            margin: 0.01cm;

        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
            margin-top: 4cm;
            margin-left: 3cm;
            margin-right: 2.5cm;
            margin-bottom: 4cm;

        }

        p {
            margin: 0 0 10px 0;
            text-align: justify;
        }

        h4.judul {
            text-align: center;
            margin: 0 0 5px 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: 0;
            left: 3cm;
            bottom: 2cm;
            width: calc(100% - 5.5cm);
        }

        .page-number {
            position: fixed;
            bottom: 0cm;
            right: 2.5cm;
            font-size: 10pt;
            font-family: "Times New Roman", serif;
            text-align: right;

        }

        .page:before {
            content: counter(page);
        }

        .pages:before {
            content: counter(pages);
        }

        /* #paraf-footer {
            position: fixed;
            bottom: 1cm;
            right: 2.5cm;
            text-align: right;
        } */


        .page-break {
            page-break-before: always;
        }

        .tabel_paraf {
            border-collapse: collapse;
            font-size: 9pt;
        }

        .tabel_paraf .tdh {
            height: 20px;
            border: 1px solid #000;
            vertical-align: bottom;
            padding-bottom: 4px;
        }

        .tabel_paraf .tdb {
            height: 50px;
            border: 1px solid #000;
            vertical-align: bottom;
            padding-bottom: 4px;
        }

        .paraf-wrapper {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
{{-- <div id="paraf-footer">
    <table class="tabel_paraf">
        <tr>
            <td class="tdh" style="width:120px; text-align:center;">
                Paraf<br>Karyawan
            </td>
            <td class="tdh" style="width:120px; text-align:center;">
                Paraf Pihak<br>Kesatu
            </td>
        </tr>
        <tr>
            <td class="tdb" style="width:120px; text-align:center;">
            </td>
            <td class="tdb" style="width:120px; text-align:center;">
            </td>
        </tr>
    </table>
</div> --}}

{{-- <div class="page-number">
    Halaman <span class="page"></span> dari <span class="pages"></span>
</div> --}}


<body>

    <table width="100%" class="header" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left; border-bottom: 2px solid #000;" width="20%">
                <img src="{{ public_path('storage/holding_image/' . $get_holding->holding_image) }}"
                    style="max-width:100px; height:auto;">
            </td>
            <td style="text-align: right; border-bottom: 2px solid #000;" width="80%">
                <h2 style="margin: 0;">{{ $get_holding ? $get_holding->holding_name : '-' }}</h2>
                <h4 class="judul" style="margin: 0; text-align: right;">RICE MILLING & POLISHING INDUSTRY</h4>
                <p style="text-align: right;">{{ $get_holding ? $get_holding->alamat : '-' }}, Telp :
                    {{ str_replace('–', '-', $get_holding ? $get_holding->telepon : '-') }}, Website :
                    {{ $get_holding ? $get_holding->website : '-' }}
                </p>
            </td>
        </tr>
    </table>
    <h4 class="judul" style="text-align: center;">PERJANJIAN KERJA WAKTU TERTENTU</h4>
    <div>
        <p>Perjanjian Kerja Waktu Tertentu (untuk selanjutnya cukup disebut perjanjian) ini dibuat dan ditandatangani di
            {{ $get_holding ? $get_holding->kota : '-' }}, pada hari
            {{ $hari_alf }}, tanggal {{ $tanggal_alf }}, bulan
            {{ $bulan_alf }}, tahun
            {{ $tahun_alf }} ({{ $date_mulai }}), oleh
            dan di antara :
        </p>
    </div>
    <div>
        <table width="100%" cellspacing="0" cellpadding="2">
            <tr>
                <td width="5%" valign="top">I.</td>
                <td width="25%" valign="top">Nama</td>
                <td width="3%" valign="top">:</td>
                <td width="67%" valign="top">
                    {{ \Illuminate\Support\Str::title(strtolower($direktur ? ($direktur->Karyawan ? $direktur->Karyawan->name : '-') : '-')) }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td valign="top">Jabatan</td>
                <td valign="top">:</td>
                <td valign="top">Direktur</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="text-align: justify;">
                    Dalam hal ini bertindak untuk dan atas nama {{ $get_holding ? $get_holding->holding_name : '-' }}
                    yang didirikan menurut
                    hukum
                    Indonesia, bergerak di bidang usaha industri pengolahan dan distribusi beras, berkedudukan di
                    {{ $get_holding ? $get_holding->alamat : '-' }}. Selanjutnya
                    disebut <b>Pihak Kesatu</b>.
                </td>
            </tr>

            <tr>
                <td valign="top">II.</td>
                <td valign="top">Nama</td>
                <td valign="top">:</td>
                <td valign="top">
                    {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->name : '-') : '-')) }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td valign="top">Jenis Kelamin</td>
                <td valign="top">:</td>
                <td valign="top">
                    @if ($get_kontrak->Karyawan->gender == 1)
                        Laki-laki
                    @elseif ($get_kontrak->Karyawan->gender == 2)
                        Perempuan
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td valign="top">Tempat&nbsp;/&nbsp;tanggal&nbsp;Lahir</td>
                <td valign="top">:</td>
                <td valign="top">
                    {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->tempat_lahir : '-') : '-')) }},
                    {{ Carbon\Carbon::parse($get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->tgl_lahir : '-') : '-')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td valign="top">NIK KTP</td>
                <td valign="top">:</td>
                <td valign="top">
                    {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->nik : '-') : '-' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td valign="top">Alamat NIK</td>
                <td valign="top">:</td>
                <td valign="top">
                    {{-- {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? $get_kontrak->Karyawan ? $get_kontrak->Karyawan->detail_alamat : '-' : '-')) }}, --}}
                    RT {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->rt : '-') : '-' }} / RW
                    {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->rw : '-') : '-' }},
                    {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->desaKTP ? $get_kontrak->Karyawan->desaKTP->name : '-') : '-') : '-')) }},
                    Kecamatan
                    {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->kecamatanKTP ? $get_kontrak->Karyawan->kecamatanKTP->name : '-') : '-') : '-')) }},
                    {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->kabupatenKTP ? $get_kontrak->Karyawan->kabupatenKTP->name : '-') : '-') : '-')) }},
                    {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->provinsiKTP ? $get_kontrak->Karyawan->provinsiKTP->name : '-') : '-') : '-')) }},
                    Kode Pos :
                    {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->kode_pos : '-') : '-' }}
                </td>
            </tr>

            <tr>
                <td></td>
                <td valign="top">Alamat KTP</td>
                <td valign="top">:</td>
                <td valign="top">
                    {{-- {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? $get_kontrak->Karyawan ? $get_kontrak->Karyawan->detail_alamat : '-' : '-')) }}, --}}
                    @if ($get_kontrak->Karyawan->status_alamat == 'ya')
                        RT {{ $get_kontrak->Karyawan ? $get_kontrak->Karyawan->rt : '-' }} / RW
                        {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->rw : '-') : '-' }},
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->desaKTP ? $get_kontrak->Karyawan->desaKTP->name : '-') : '-') : '-')) }},
                        Kecamatan
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->kecamatanKTP ? $get_kontrak->Karyawan->kecamatanKTP->name : '-') : '-') : '-')) }},
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->kabupatenKTP ? $get_kontrak->Karyawan->kabupatenKTP->name : '-') : '-') : '-')) }},
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->provinsiKTP ? $get_kontrak->Karyawan->provinsiKTP->name : '-') : '-') : '-')) }},
                        Kode Pos :
                        {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->kode_pos : '-') : '-' }}
                    @else
                        RT
                        {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->rt_domisili : '-') : '-' }}
                        /
                        RW
                        {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->rw_domisili : '-') : '-' }},
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->desaNOW ? $get_kontrak->Karyawan->desaNOW->name : '-') : '-') : '-')) }},
                        Kecamatan
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->kecamatanNOW ? $get_kontrak->Karyawan->kecamatanNOW->name : '-') : '-') : '-')) }},
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->kabupatenNOW ? $get_kontrak->Karyawan->kabupatenNOW->name : '-') : '-') : '-')) }},
                        {{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? ($get_kontrak->Karyawan->provinsiNOW ? $get_kontrak->Karyawan->provinsiNOW->name : '-') : '-') : '-')) }},
                        Kode Pos :
                        {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->kode_pos_domisili : '-') : '-' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td valign="top">No. Telepon / WA</td>
                <td valign="top">:</td>
                <td valign="top">
                    {{ $get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->telepon : '-') : '-' }}
                </td>
            </tr>
        </table>
    </div>
    <div>
        <p>Dalam hal ini, bertindak untuk dan atas namanya sendiri, selanjutnya disebut <b>Pihak Kedua.</b></p>
    </div>
    <div>
        <p>Untuk selanjutnya, Pihak Kesatu dan pihak kedua secara bersama-sama dalam Perjanjian ini cukup disebut
            <b>Para Pihak,</b> menjelaskan dan menyatakan dengan sesungguhnya hal-hal sebagai berikut:
        </p>
    </div>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Bahwa Pihak Kedua mengajukan permohonan untuk dapat diberikan kesempatan bekerja pada Pihak
                    Kesatu. Setelah melakukan beberapa hal dan pertimbangan, akhirnya Pihak Kesatu bersedia untuk
                    menerima Pihak Kedua sebagai pekerja pada kegiatan usaha Pihak Kesatu.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Bahwa Pihak kedua menyatakan tidak pernah terlibat dalam perkara hukum dalam bentuk apapun baik
                    perdata maupun pidana.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Bahwa dengan ini, Para Pihak sepakat untuk mengikatkan diri dengan mengadakan perjanjian kerja
                    yang mengatur ketentuan - ketentuan sebagai berikut:
                </td>
            </tr>
        </table>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 1</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Luas Pekerjaan</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Lokasi pekerjaan adalah di seluruh area usaha milik Pihak Kesatu, atau di tempat lain yang
                    ditentukan oleh Pihak Kesatu baik yang sekarang maupun yang akan datang. Dan Pihak Kedua bersedia
                    setiap saat mengerti, menyetujui dan siap dipindahkan pada divisi / departemen lain yang
                    ditentukan oleh Pihak Kesatu karena alasan bisnis operasional.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Jabatan sebagai
                    "{{ \Illuminate\Support\Str::title(strtolower($jabatan ? ($jabatan->Jabatan ? $jabatan->Jabatan->nama_jabatan : '-') : '-')) }}
                    {{ \Illuminate\Support\Str::title(strtolower($jabatan ? ($jabatan->Jabatan ? ($jabatan->Jabatan->Bagian ? ($jabatan->Jabatan->Bagian->Divisi ? $jabatan->Jabatan->Bagian->Divisi->nama_divisi : '-') : '-') : '-') : '-')) }}"
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Tugas, tanggung jawab, serta deskripsi pekerjaan Pihak Kedua akan dijelaskan oleh atasan langsung
                    dan tertuang dalam lampiran perjanjian ini yang merupakan satu kesatuan dengan perjanjian.
                </td>
            </tr>
        </table>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 2</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Jangka Waktu Perjanjian</h4>
    <div>
        <p>
            Perjanjian kerja ini berlaku selama
            @if ($get_kontrak->lama_kontrak == '1bln')
                1 (satu) bulan
            @elseif ($get_kontrak->lama_kontrak == '3bln')
                3 (tiga) bulan
            @elseif ($get_kontrak->lama_kontrak == '6bln')
                6 (enam) bulan
            @elseif ($get_kontrak->lama_kontrak == '1thn')
                1 (satu) tahun
            @elseif ($get_kontrak->lama_kontrak == '2thn')
                2 (dua) tahun
            @endif
            terhitung mulai tanggal {{ $tanggal_mulai_kontrak }}
            ({{ $tanggal_mulai_alf }})
            bulan {{ $bulan_alf }}, tahun
            {{ Carbon\Carbon::parse($get_kontrak ? $get_kontrak->tanggal_mulai : '')->format('Y') }}
            ({{ $tahun_alf }}), sampai dengan tanggal {{ $tanggal_selesai_kontrak }}
            ({{ $tanggal_selesai_alf }})
            bulan {{ $bulan_selesai_alf }}, tahun
            {{ Carbon\Carbon::parse($get_kontrak ? $get_kontrak->tanggal_selesai : '')->format('Y') }}
            ({{ $tahun_selesai_alf }}),
            di mana dengan berakhirnya perjanjian ini maka berakhir pula hubungan kerja antara para pihak, baik dengan
            ataupun tanpa pemberitahuan dari perusahaan

        </p>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 3</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Gaji dan Fasilitas</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Gaji yang telah disepakati diatur di lembar terpisah / lampiran perjanjian ini yang merupakan satu
                    kesatuan.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Gaji akan dipotong dengan :
                    <table>
                        <tr>
                            <td width="5%" valign="top">
                                a.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Iuran BPJS Ketenagakerjaan sesuai ketentuan pemerintah
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                b.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Iuran BPJS Kesehatan sesuai ketentuan pemerintah.
                            </td>
                        </tr>
                    </table>
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Tunjangan tidak tetap (T3) akan diberikan sesuai dengan kesepakatan.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    4.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Tunjangan jabatan diberikan selama menjabat dan sesuai dengan kesepakatan.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    5.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Gaji dan tunjangan tersebut sudah termasuk upah lembur apabila dalam penyelesaian pekerjaan
                    memerlukan Pihak Kedua untuk bekerja lembur.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    6. </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Penghitungan gaji dihitung setiap tanggal 21 pada bulan berjalan dan diakhiri pada tanggal 20
                    pada bulan berikutnya.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    7. </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pajak penghasilan (PPH pasal 21) menjadi tanggung jawab karyawan di mana perhitungan akan
                    dilakukan oleh perusahaan dan akan langsung dipotong dari upah karyawan setiap bulannya untuk
                    disetorkan ke kantor pajak
                </td>
            </tr>
        </table>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 4</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Tugas dan Tanggung Jawab</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pihak Kedua berjanji untuk bekerja keras, jujur, tertib dan disiplin dalam menjalankan tugas yang
                    diberikan atau yang akan diberikan oleh Pihak Pertama, dan bertanggung jawab penuh terhadap segala
                    inventaris Pihak Pertama, yang menjadi tanggung jawabnya.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pihak Kedua berjanji menjaga nama baik, rahasia dalam keadaan dan cara apapun, serta data milik
                    Pihak Pertama selama terikat perjanjian maupun setelah berakhirnya perjanjian dengan tanpa mendapat
                    persetujuan tertulis dari perusahaan Pihak Kesatu
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pihak Kedua berjanji akan mematuhi semua peraturan perusahaan dan ketentuan perusahaan <b>yang
                        sudah
                        atau akan dikeluarkan di kemudian hari,</b> dan dalam hal Pihak Kedua melanggar peraturan
                    tersebut, Pihak
                    Kedua setuju dan bersedia menerima segala akibat atas pelanggarannya, untuk ini Pihak Pertama akan
                    mengacu pada peraturan perundang-undangan yang berlaku.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    4.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pihak Kedua berjanji untuk bekerja keras, jujur, tertib dan disiplin dalam menjalankan tugas yang
                    diberikan atau yang akan diberikan oleh Pihak Kesatu, dan bertanggung jawab penuh terhadap segala
                    inventaris Pihak Kesatu, yang menjadi tanggung jawabnya.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    5.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Dalam upaya pembinaan personil, maka segala bentuk pelanggaran yang dilakukan oleh Pihak Kedua akan
                    diberikan sanksi surat peringatan.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    6. </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pemberian surat peringatan dapat dilaksanakan tidak secara berurutan, namun diberikan berdasarkan
                    bobot dari kesalahan yang dilakukan.
                </td>
            </tr>

        </table>
    </div>
    <h4 class="judul page-break" style="text-align:center; margin-bottom:0;">Pasal 5</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Evaluasi Kinerja</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Sejalan dengan Kebijakan manajemen di bidang sumber daya manusia dan selaras dengan upaya
                    perusahaan dalam meningkatkan profesionalitas sumber daya manusia, maka Pihak Kesatu akan melakukan
                    evaluasi kinerja dan kompetensi Pihak kedua dalam periode 3 (tiga) bulan pertama .
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    evaluasi kinerja dimaksud dengan memakai cara dan prosedur yang telah ditetapkan oleh direksi,
                    dan hasil evaluasi kinerja merupakan Hak Prerogatif Pihak Kesatu.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Apabila dari hasil penilaian tersebut ternyata Pihak Kedua tidak dapat memenuhi standar
                    kompetensi sesuai Ketetapan Pihak Kesatu, maka hubungan kerja tidak dapat dilanjutkan lagi, dalam
                    arti hubungan kerja akan berakhir tanpa ganti rugi dalam bentuk apapun oleh Pihak Kesatu kepada
                    Pihak Kedua.
                </td>
            </tr>
        </table>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 6</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Ikatan Dinas / Jabatan Karyawan</h4>
    <div>
        <p>Karyawan yang sedang dalam menjalani masa ikatan dinas / jabatan diatur dalam lembar terpisah dari perjanjian
            ini. </p>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 7</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Tata Tertib kerja</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pihak Kedua diwajibkan memakai seragam kerja sesuai ketentuan yang telah ditetapkan oleh Pihak
                    Kesatu.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Hari kerja dan jam kerja ditentukan oleh Pihak Kesatu.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Dalam hal Pihak Kedua akan mengajukan ijin, maka secara prosedural harus mengajukan ijin tertulis
                    kepada Pihak Kesatu paling lambat 1 (satu) hari sebelumnya.
                </td>
            </tr>
        </table>
    </div>
    <h4 class="judul page-break" style="text-align:center; margin-bottom:0;">Pasal 8</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Berakhirnya Perjanjian</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Perjanjian ini berakhir apabila :
                    <table>
                        <tr>
                            <td width="5%" valign="top">
                                a.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Dikarenakan suatu sebab yang diatur dalam peraturan perundang-undangan.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                b.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua melanggar ketentuan perjanjian, peraturan perusahaan dan atau ketentuan lain
                                yang ditetapkan oleh Pihak Kesatu.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                c.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua sudah tidak dapat lagi melaksanakan pekerjaan yang telah disepakati para
                                pihak.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                d.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pekerjaan yang diperjanjikan telah selesai.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                e.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua mengundurkan diri dari pekerjaan yang diberikan oleh Pihak Kesatu.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                f.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua meninggal dunia.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                g.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua berhalangan tetap untuk melaksanakan perjanjian.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                h.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua membuka rahasia perusahaan Pihak Kesatu baik yang bersifat data,
                                ataupun dokumentasi.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                i.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Pihak Kedua mangkir kerja dengan tanpa keterangan tertulis selama 5 (lima) hari
                                berturut-turut.
                            </td>
                        </tr>
                        <tr>
                            <td width="5%" valign="top">
                                j.
                            </td>
                            <td width="95%" valign="top" style="text-align: justify;">
                                Jika pihak kedua memberikan keterangan palsu atau dipalsukan.
                            </td>
                        </tr>
                    </table>
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Dalam hal perjanjian ini berakhir karena ketentuan sebagaimana dimaksud dalam ayat 1 (satu), maka
                    tuntutan pembayaran gaji (upah), sisa waktu perjanjian, dan pembayaran lainnya yang timbul dari
                    perjanjian ini dinyatakan gugur.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Dalam hal perjanjian ini berakhir karena ketentuan sebagaimana dimaksud dalam ayat 1 (satu), maka
                    Pihak Kedua diwajibkan mengembalikan seluruh inventaris milik Pihak Kesatu
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    4.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Terhadap berakhirnya perjanjian ini, maka Para Pihak sepakat untuk mengesampingkan berlakunya pasal
                    1266 Kitab Undang-Undang Hukum Perdata (<i>Burgerlijk Wetboek</i>).
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    5.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Apabila terjadi kecurangan (<i>fraud</i>) atau selisih dalam stok barang ataupun hasil penjualan,
                    setelah dilakukan pemeriksaan oleh <b>Pihak Pertama</b> terbukti bahwa hal tersebut akibat kesalahan
                    <b>Pihak Kedua</b>, maka <b>Pihak Kedua</b> wajib memberikan ganti rugi atau membayar sejumlah
                    kerugian
                    yang ada kepada
                    <b>Pihak Pertama</b>.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    6. </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Apabila <b>Pihak Kedua</b> bekerja dengan masa kerja kurang dari 14 hari, maka <b>Pihak Kedua</b>
                    tidak
                    akan
                    mendapatkan kompensasi apapun serta jika ada jaminan tidak akan dikembalikan kepada <b>Pihak
                        Kedua</b> oleh
                    <b>Pihak Pertama</b>.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    7. </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Apabila Pihak kedua menghapus hak kekayaan intelektual milik pihak pertama berupa <i>database</i>
                    perusahaan, proses kerja properti seperti program, hak paten ataupun merk, rahasia resep komposisi
                    produksi dari perangkat PC, Laptop, Ponsel, <i>software</i> atau perangkat lainya milik pihak
                    pertama,
                    maka Pihak pertama berhak menuntut pihak kedua secara hukum dan akan dipidanakan.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    8.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Pihak kedua wajib menyerahkan seluruh hasil karya, hasil kerja, <i>database</i> kepada pihak pertama
                    serta menjaga kerahasiaan hal hal tersebut baik ketika pihak kedua sedang dalam masa kontrak ataupun
                    setelah berakhirnya kontrak ini. Hal ini termasuk dan tidak terbatas kepada dilarangnya menghapus
                    data base di perangkat / <i>google drive</i> / <i>cloud</i> serta dilarang menjual /
                    menginformasikan data
                    milik
                    pihak pertama.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    <b>9.</b>
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    @if ($jabatan->Jabatan->LevelJabatan->level_jabatan < 4)
                        <b>Apabila Pihak Kedua akan mengajukan permintaan pengakhiran perjanjian, maka Pihak Kedua wajib
                            mengajukan surat permintaan pengakhiran perjanjian 90 (Sembilan Puluh) hari kerja terhitung
                            dari
                            tanggal efektif pengunduran diri dimaksud dengan memenuhi persyaratan yang ditetapkan oleh
                            perusahaan.</b>
                    @elseif ($jabatan->Jabatan->LevelJabatan->level_jabatan == 4)
                        <b>Apabila Pihak Kedua akan mengajukan permintaan pengakhiran perjanjian, maka Pihak Kedua wajib
                            mengajukan surat permintaan pengakhiran perjanjian 60 (Enam Puluh) hari kerja terhitung
                            dari
                            tanggal efektif pengunduran diri dimaksud dengan memenuhi persyaratan yang ditetapkan oleh
                            perusahaan.</b>
                    @elseif ($jabatan->Jabatan->LevelJabatan->level_jabatan > 4)
                        <b>Apabila Pihak Kedua akan mengajukan permintaan pengakhiran perjanjian, maka Pihak Kedua wajib
                            mengajukan surat permintaan pengakhiran perjanjian 30 (Tiga Puluh) hari kerja terhitung
                            dari
                            tanggal efektif pengunduran diri dimaksud dengan memenuhi persyaratan yang ditetapkan oleh
                            perusahaan.</b>
                    @endif

                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    <b>10.</b>
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    <b>Memberikan data palsu / memalsukan data serta dokumen yang terkait dengan tugas dang
                        tanggung jawab kerja.</b>
                </td>
            </tr>



        </table>
    </div>
    <h4 class="judul" style="text-align:center; margin-bottom:0;">Pasal 9</h4>
    <h4 class="judul" style="text-align:center; margin-top:0;">Penutup</h4>
    <div>
        <table>
            <tr>
                <td width="5%" valign="top">
                    1.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Hal-hal yang belum diatur dalam perjanjian ini, akan diatur dalam ketentuan tersendiri sesuai
                    kebijakan Pihak Kesatu.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    2.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Para Pihak menyatakan bahwa telah membaca dan memahami sepenuhnya isi perjanjian ini, serta
                    menyadari sepenuhnya akan hak dan kewajiban masing-masing.
                </td>

            </tr>
            <tr>
                <td width="5%" valign="top">
                    3.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Apabila terjadi perselisihan di kemudian hari, maka Para Pihak memilih penyelesaian di wilayah hukum
                    {{ $get_holding ? $get_holding->kota : '-' }}.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    4.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Terhadap hal-hal yang belum diatur dalam perjanjian ini, maka akan diatur dan atau merujuk pada
                    peraturan perusahaan Pihak Kesatu.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    5.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Apabila terdapat ketentuan dalam perjanjian kerja ini dilarang, cacat dan dapat dimintakan
                    pembatalannya, atau bertentangan dengan hukum atau peraturan dari pejabat yang berwenang, maka hanya
                    ketentuan itu saja yang tidak berlaku, sedangkan ketentuan lain dalam perjanjian ini masih berlaku.
                </td>
            </tr>
            <tr>
                <td width="5%" valign="top">
                    6.
                </td>
                <td width="95%" valign="top" style="text-align: justify;">
                    Perjanjian ini dibuat dalam keadaan sadar, dengan itikad baik, dan tanpa paksaan maupun tipu
                    muslihat dari pihak manapun.
                </td>
            </tr>
        </table>
        <p>Demikian perjanjian ini dibuat dan ditandatangani pada waktu dan tempat sebagaimana disebut pada awal
            perjanjian.</p>
    </div>
    <div class="">
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top:50px; text-align:center;">
            <tr>
                <td colspan="2" style="padding-bottom:20px;">
                    Menyepakati,
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <strong>PIHAK KESATU</strong>
                </td>
                <td width="50%">
                    <strong>PIHAK KEDUA</strong>
                </td>
            </tr>
            <tr>
                <td style="height:80px;"></td>
                <td style="height:80px;"></td>
            </tr>
            <tr>
                <td style="padding-bottom:20px;">
                </td>
                <td style="padding-bottom:20px;">
                    Materai Rp10.000</td>
            </tr>
            <tr>
                <td>
                    <strong><u>
                            {{ \Illuminate\Support\Str::title(strtolower($direktur ? ($direktur->Karyawan ? $direktur->Karyawan->name : '--------------------------') : '--------------------------')) }}</u></strong>
                </td>
                <td>
                    <strong><u>{{ \Illuminate\Support\Str::title(strtolower($get_kontrak ? ($get_kontrak->Karyawan ? $get_kontrak->Karyawan->name : '--------------------------') : '--------------------------')) }}</u></strong>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
