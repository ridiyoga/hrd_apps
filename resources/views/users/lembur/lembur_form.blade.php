<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>FORM LEMBUR | HRD APP</title>
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
            vertical-align: middle;
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
            /* margin-bottom: 30px; */
        }

        .info-table th {
            width: 30%;
            text-align: left;
            color: #777;
            font-weight: 400;
            padding: 2px 0;
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

        /* Badge untuk Tipe Lembur */
        .badge-lembur {
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

        .approval-section {
            margin-top: 15px;
            width: 100%;
            page-break-inside: avoid;
        }

        .title-pengesahan {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-approval {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Memaksa lebar kolom persis sama rata (25% per kolom) */
        }

        .table-approval th,
        .table-approval td {
            border: 1px solid #c0c0c0;
            text-align: center;
            padding: 0;
            /* Padding di-nol-kan agar dikontrol penuh oleh approval-card */
            vertical-align: top;
            width: 25%;
            /* Membagi rata 4 kolom sama besar */
        }

        .table-approval th {
            background-color: #f5f5f5;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 2px;
            text-transform: uppercase;
        }

        .approval-card {
            padding: 6px;
            box-sizing: border-box;
            height: 125px;
            /* Mengunci tinggi kotak agar semua kolom lurus sejajar */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .status-text {
            font-size: 8.5px;
            color: #777;
            display: block;
            margin-bottom: 2px;
            font-style: italic;
        }

        .signature-container {
            height: 60px;
            /* Mengunci tinggi ruang tanda tangan agar seragam */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px 0;
            overflow: hidden;
        }

        .signature-container img {
            max-height: 55px;
            /* Batas tinggi gambar TTD */
            max-width: 100%;
            /* Mencegah gambar TTD keluar dari lebar kolom */
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .info-container {
            border-top: 1px solid #eee;
            padding-top: 4px;
            margin-top: auto;
            /* Memaksa teks nama berada paling bawah kartu */
        }

        .name-text {
            font-weight: bold;
            margin: 0;
            font-size: 9.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            /* Mengubah nama yang kepanjangan menjadi titik-titik (...) agar tidak merusak layout */
        }

        .date-text {
            margin: 1px 0 0 0;
            font-size: 8.5px;
            color: #666;
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
                    <h2>FORM LEMBUR</h2>
                    <small>Tanggal Pengajuan:
                        {{ \Carbon\Carbon::parse($data_lembur->created_at)->translatedFormat('d F Y') }}
                    </small>
                </td>
                <td width="30%" align="right">
                    <div class="no-doc">
                        {{ $data_lembur->no_form_lembur }}
                    </div>
                </td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="width: 65%; vertical-align: top;">
                    <table class="info-table" style="font-size: 11px;">

                        <tr>
                            <th>NAMA LENGKAP</th>
                            <td>: {{ $user_karyawan->name }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>: {{ $user_karyawan->nomor_identitas_karyawan }}</td>
                        </tr>
                        <tr>
                            <th>POSISI</th>
                            <td>: {{ $karyawan_jabatan->Jabatan->nama_jabatan }}
                                {{ $karyawan_jabatan->Departemen->nama_departemen ?? '-' }},
                                {{ $karyawan_jabatan->Divisi->nama_divisi ?? '-' }}, BAGIAN
                                {{ $karyawan_jabatan->Bagian->nama_bagian ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>LOKASI KERJA</th>
                            <td>: {{ $user_karyawan->PenempatanKerja->site_name }}</td>
                        </tr>
                    </table>
                </td>

                <td style="width: 25%; vertical-align: top;">
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td>Keterangan Lembur:
                                <br>
                                <p>
                                    {{ $data_lembur->keterangan_lembur }}
                                </p>
                            </td>

                            <td align="right">
                            </td>
                        </tr>
                    </table>
                    <hr>
                </td>
            </tr>
        </table>

        {{-- <div class="note-box">
            <label><i class="fa fa-info-circle"></i> Alasan / Keterangan Lembur:</label>
            <p class="m-0 text-dark">{{ $data_lembur->keterangan_lembur ?: 'Tidak ada keterangan.' }}</p>
    </div> --}}
        <div class="title-pengesahan">DAFTAR KARYAWAN</div>

        <table class="table-approval">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama&nbsp;Karyawan</th>
                    <th>Tanggal</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Lama&nbsp;(Jam)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_lembur->LemburDetail as $detail)
                    <tr>
                        <td>
                            <p class="date-text">
                                {{ $loop->iteration }}
                            </p>
                        </td>
                        <td>
                            <p class="date-text" style="text-align: left; margin: 2;">
                                {{ $detail->nama_karyawan }}
                            </p>
                        </td>

                        <td>
                            <p class="date-text">
                                {{ \Carbon\Carbon::parse($detail->tgl_lembur)->translatedFormat('d-m-Y') }}
                            </p>
                        </td>
                        <td>
                            <p class="date-text">
                                {{ \Carbon\Carbon::parse($detail->jam_mulai)->format('H:i') }}
                            </p>
                        </td>
                        <td>
                            <p class="date-text">
                                {{ \Carbon\Carbon::parse($detail->jam_selesai)->format('H:i') }}
                            </p>
                        </td>
                        <td>
                            <p class="date-text">
                                {{ $detail->selisih_convert ? \Carbon\Carbon::parse($detail->selisih_convert)->format('H:i') : '-' }}
                            </p>
                        </td>
                        <td>
                            @if ($detail->status == 1)
                                <p class="date-text" style="color: #198754;">
                                    <b>DISETUJUI&nbsp;HRD</b>
                                </p>
                            @else
                                <p class="date-text" style="color: #dc3545;">
                                    <b>DITOLAK&nbsp;HRD</b>
                                </p>
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="approval-section">
            <div class="title-pengesahan">Lembar Pengesahan</div>

            <table class="table-approval">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Atasan Langsung</th>
                        <th>Atasan 2</th>
                        <th>HRD</th>
                        <th>Kasir</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <tr>
                        <!-- PEMOHON -->
                        <td>
                            <div class="approval-card">
                                <span class="status-text">Diajukan</span>
                                <div class="signature-container">
                                    @if ($data_lembur->ttd_user)
                                        <img
                                            src="https://hrd.sumberpangan.store/storage/signatures/{{ $data_lembur->ttd_user }}">
                                    @endif
                                </div>
                                <div class="info-container">
                                    <p class="name-text">
                                        {{ $user_karyawan ? $user_karyawan->name : '........................' }}</p>
                                    <p class="date-text">Tgl:
                                        {{ $data_lembur->created_at ? \Carbon\Carbon::parse($data_lembur->created_at)->translatedFormat('d F Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- ATASAN 1 -->
                        <td>
                            <div class="approval-card">
                                <span class="status-text">Disahkan Oleh-1</span>
                                <div class="signature-container">
                                    @if ($data_lembur->ttd_atasan)
                                        <img
                                            src="https://hrd.sumberpangan.store/storage/signatures/{{ $data_lembur->ttd_atasan }}">
                                    @endif
                                </div>
                                <div class="info-container">
                                    <p class="name-text">
                                        {{ $data_lembur->Atasan1 ? $data_lembur->Atasan1->name : '........................' }}
                                    </p>
                                    <p class="date-text">Tgl:
                                        {{ $data_lembur->waktu_approve ? \Carbon\Carbon::parse($data_lembur->waktu_approve)->translatedFormat('d F Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- ATASAN 2 -->
                        <td>
                            <div class="approval-card">
                                <span class="status-text">Disahkan Oleh-2</span>
                                <div class="signature-container">
                                    @if ($data_lembur->ttd_atasan2)
                                        <img
                                            src="https://hrd.sumberpangan.store/storage/signatures/{{ $data_lembur->ttd_atasan2 }}">
                                    @endif
                                </div>
                                <div class="info-container">
                                    <p class="name-text">
                                        {{ $data_lembur->Atasan2 ? $data_lembur->Atasan2->name : '........................' }}
                                    </p>
                                    <p class="date-text">Tgl:
                                        {{ $data_lembur->waktu_approve2 ? \Carbon\Carbon::parse($data_lembur->waktu_approve2)->translatedFormat('d F Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- HRD -->
                        <td>
                            <div class="approval-card">
                                <span class="status-text">Disahkan Oleh-3</span>
                                <div class="signature-container">
                                    @if ($data_lembur->ttd_hrd)
                                        <img
                                            src="https://hrd.sumberpangan.store/storage/signatures/{{ $data_lembur->ttd_hrd }}">
                                    @endif
                                </div>
                                <div class="info-container">
                                    <p class="name-text">
                                        {{ $data_lembur->AtasanHRD ? $data_lembur->nama_hrd : '........................' }}
                                    </p>
                                    <p class="date-text">Tgl:
                                        {{ $data_lembur->waktu_approve_hrd ? \Carbon\Carbon::parse($data_lembur->waktu_approve_hrd)->translatedFormat('d F Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="approval-card">
                                <span class="status-text">Disahkan Oleh-4</span>
                                <div class="signature-container">

                                </div>
                                <div class="info-container">
                                    <p class="name-text">
                                        ........................
                                    </p>
                                    <p class="date-text">Tgl:........................
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer style="text-align:center; vertical-align: bottom; margin-top: 5px;">
            <p>
                <small class="text-muted">
                    Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}
                </small>
            </p>
        </footer>
    </div>
</body>

</html>
