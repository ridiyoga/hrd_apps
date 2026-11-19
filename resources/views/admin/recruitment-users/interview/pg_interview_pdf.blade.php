<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>HASIL UJIAN | HRD APP</title>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Dekorasi Abstrak */
        .abstract-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%);
            clip-path: circle(50% at 70% 20%);
            z-index: -1;
            opacity: 0.1;
        }

        .container-fluid {
            padding: 40px;
            position: relative;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: left;
        }

        .no-doc {
            background: #6f42c1;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .title {
            text-align: center;
        }

        .title h2 {
            margin: 0;
            font-size: 20px;
            color: #4b2c85;
        }

        /* Card Style Sections */
        .section-title {
            background: #f1edff;
            color: #5a32a3;
            padding: 2px 15px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 10px;
            border-left: 5px solid #6f42c1;
        }

        .info-table {
            width: 100%;
            text-align: left;

            /* margin-bottom: 30px; */
        }

        .info-table th {
            width: 30%;
            text-align: left;
            color: #777;
            font-weight: 400;
            padding: 2px 0;
            align: left;
        }

        .info-table td {
            font-weight: 600;
            color: #222;
            padding: 0px 0;
        }



        .highlight-purple {
            color: #6f42c1;
            font-weight: 700;
            font-size: 15px;
        }

        /* Keterangan Box */
        .note-box {
            background: #fdfbff;
            border: 1px dashed #6f42c1;
            border-radius: 10px;
            padding: 1px;
            margin: 25px 0;
        }

        .note-box label {
            display: block;
            font-weight: 700;
            color: #6f42c1;
            margin-bottom: 5px;
        }

        /* Table Pengesahan */
        .approval-table {
            margin-top: 25px;
            border: none !important;
        }

        .approval-table th {
            background-color: #f1edff !important;
            color: #6f42c1 !important;
            text-align: center;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
        }

        .approval-card {
            text-align: center;
            padding: 15px;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #eee;
        }

        .text-success {
            color: #198754 !important;
        }

        footer {
            color: #9c9c9c;
        }

        .signature-img {
            max-height: 80px;
            object-fit: contain;
            margin: 10px auto;
        }

        .name-label {
            font-weight: 600;
            text-decoration: underline;
            margin: 0;
        }

        .tgl-label {
            margin: 0;
        }

        /* Badge untuk Tipe Cuti */
        .badge-cuti {
            display: inline-block;
            background: #e6e0f8;
            color: #6f42c1;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        .signature-area {
            height: 100px;
            /* tinggi area tanda tangan tetap */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .signature-area img {
            max-height: 100px;
            max-width: 120px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .konten-soal p,
        .konten-soal div,
        .konten-soal h1,
        .konten-soal h2,
        .konten-soal h3 {
            margin: 0 0 8px 0 !important;
            /* Memberikan sedikit jarak antar paragraf jika ada */
            padding: 0 !important;
        }

        /* Memastikan paragraf pertama benar-benar rata atas dengan nomor */
        .konten-soal p:first-child,
        .konten-soal div:first-child {
            margin-top: 0 !important;
        }
    </style>
</head>

<body>
    <div class="abstract-bg"></div>

    <div class="container-fluid">
        <table class="header-table">
            <tr>
                <td width="20%">
                    {{-- <img src="{{ $logo }}" style="max-width:100px; height:auto;"> --}}
                    <img src="{{ 'https://hrd.sumberpangan.store/storage/holding_image/' . $holding->holding_image }}"
                        style="max-width:80px; height:auto;">
                </td>
                <td width="50%" class="title">
                    <h2>LEMBAR JAWABAN</h2>
                    <small>Ujian
                        {{ $ujian->nama }}
                    </small>
                </td>
                <td width="20%" align="center">
                    <div class="no-doc">
                        Nilai : {{ $total_nilai }}
                    </div>
                </td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="section-title">Informasi Pelamar</div>
                    <table class="info-table" style="font-size: 12px;">
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>: {{ $cv->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan Dilamar</th>
                            <td>: {{ $recruitment_user->Jabatan->nama_jabatan ?? '-' }} -
                                {{ $recruitment_user->Bagian->nama_bagian ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Ujian</th>
                            <td>: {{ $ujian->jam }} Jam {{ $ujian->menit }} Menit</td>
                        </tr>
                        <tr>
                            <th>Benar</th>
                            <td>: {{ $benar }}</td>
                        </tr>
                        <tr>
                            <th>Salah</th>
                            <td>: {{ $salah }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="section-title">Soal dan Jawaban</div>
        @php
            $no = 1;
        @endphp
        @foreach ($PgSiswa as $uu)
            <table class="info-table" style="width:100%; font-size:12px;">
                <tr>
                    <!-- Kolom Nomor Soal -->
                    <td style="width:30px; text-align:left; vertical-align:top; padding:0; line-height: 1.5;">
                        {{ $no++ }}.
                    </td>

                    <!-- Kolom Teks Soal -->
                    <td style="text-align:left; vertical-align:top; padding:0; line-height: 1.5;" class="konten-soal">
                        {!! $uu->soal !!}
                    </td>
                </tr>
            </table>
            <table class="info-table" style="width:100%; font-size:12px;">
                <tr>
                    <!-- Kolom kosong di kiri untuk menyelaraskan dengan nomor soal atas -->
                    <th style="width:30px;"></th>

                    <!-- Pilihan A -->
                    <th style="width:25px; text-align:left; vertical-align:top;">A.</th>
                    <th style="width:17%; text-align:left; vertical-align:top; padding-right:15px;">
                        {{ $uu->pg_1 }}
                    </th>

                    <!-- Pilihan B -->
                    <th style="width:25px; text-align:left; vertical-align:top;">B.</th>
                    <th style="width:17%; text-align:left; vertical-align:top; padding-right:15px;">
                        {{ $uu->pg_2 }}
                    </th>

                    <!-- Pilihan C -->
                    <th style="width:25px; text-align:left; vertical-align:top;">C.</th>
                    <th style="width:17%; text-align:left; vertical-align:top; padding-right:15px;">
                        {{ $uu->pg_3 }}
                    </th>

                    <!-- Pilihan D -->
                    <th style="width:25px; text-align:left; vertical-align:top;">D.</th>
                    <th style="width:17%; text-align:left; vertical-align:top; padding-right:15px;">
                        {{ $uu->pg_4 }}
                    </th>

                    <!-- Pilihan E -->
                    <th style="width:25px; text-align:left; vertical-align:top;">E.</th>
                    <th style="width:17%; text-align:left; vertical-align:top;">
                        {{ $uu->pg_5 }}
                    </th>
                </tr>
            </table>
            <table>
                <th style="width:30px; text-align:left;"></th>
                <td>
                    <p style="font-size: 12px;">Jawaban Yang Benar : {{ $uu->kunci }}</p>
                </td>
                <th style="width:30px; text-align:left;"></th>
                <td>
                <td>
                    <p class="fw-bold" style="font-size: 12px;">Jawaban Pelamar : {{ $uu->jawaban }}
                        @if ($uu->benar == 1)
                            (Benar)
                        @else
                            (Salah)
                        @endif
                    </p>
                </td>
            </table>
        @endforeach
    </div>
</body>

</html>
