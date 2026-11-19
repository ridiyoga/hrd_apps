<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>FORM PRESENSI | HRD APP</title>
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

        /* Badge untuk Tipe Presensi */
        .badge-presensi {
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
                    <h3>FORMULIR PRESENSI</h3>
                    <small>Tanggal Pengajuan:
                        {{ \Carbon\Carbon::parse($data_presensi->created_at)->translatedFormat('d F Y') }}
                    </small>
                </td>
                <td width="30%" align="right">
                    <div class="no-doc">
                        {{ $data_presensi->no_form_presensi }}
                    </div>
                </td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="section-title">Informasi Karyawan</div>
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <th>NIK</th>
                            <td>: {{ $user_karyawan->nomor_identitas_karyawan }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>: {{ $user_karyawan->name }}</td>
                        </tr>
                        <tr>
                            <th>Departemen / Divisi</th>
                            <td>: {{ $karyawan_jabatan->Departemen->nama_departemen ?? '-' }} /
                                {{ $karyawan_jabatan->Divisi->nama_divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>: {{ $karyawan_jabatan->Jabatan->nama_jabatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Kerja</th>
                            <td>: {{ $user_karyawan->PenempatanKerja->site_name }}</td>
                        </tr>
                    </table>
                </td>

                <td style="width: 50%; vertical-align: top;">
                    <div class="section-title">Detail Pengajuan</div>
                    <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td>
                                <small class="text-muted d-block">Tanggal Presensi:</small>
                                <br>
                                <span
                                    class="h4 highlight-purple">{{ \Carbon\Carbon::parse($data_presensi->created_at)->translatedFormat('d F Y') }}</span>
                            </td>
                            <td align="right">
                                <small class="text-muted d-block">Jam Presensi:</small>
                                <br>
                                <span
                                    class="h4 highlight-purple">{{ \Carbon\Carbon::parse($data_presensi->created_at)->translatedFormat('H:i:s') }}</span>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    {{-- <table class="info-table" style="font-size: 11px;">
                        <tr>
                            <td>
                                <small class="text-muted d-block">Mulai:</small>
                                <br>
                                <strong>{{ \Carbon\Carbon::parse($data_presensi->tanggal_mulai)->translatedFormat('d/m/Y') }}</strong>
                            </td>
                            <td>
                                <small class="text-muted d-block">Berakhir:</small>
                                <br>
                                <strong>{{ \Carbon\Carbon::parse($data_presensi->tanggal_selesai)->translatedFormat('d/m/Y') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <small class="text-muted d-block">Kembali Bekerja:</small>
                                <br>
                                @php
                                    $tanggalKembali = \Carbon\Carbon::parse($data_presensi->tanggal_selesai)->addDay();
                                    if ($user_karyawan->shift = 'NON SHIFT') {
                                        # code...
                                        if ($tanggalKembali->isSunday()) {
                                            $tanggalKembali->addDay();
                                        }
                                    }
                                @endphp

                                <strong class="text-success">
                                    {{ $tanggalKembali->translatedFormat('d F Y') }}
                                </strong>
                            </td>
                            <td>
                                <small class="text-muted">Karyawan Pengganti:</small>
                                <br>
                                <strong
                                    class="font-weight-bold">{{ $data_presensi->KaryawanPengganti ? $data_presensi->KaryawanPengganti->name : '-' }}</strong>
                            </td>
                        </tr>
                    </table> --}}

                </td>
            </tr>
        </table>

        <hr>
        <div class="note-box">

            <label><i class="fa fa-info-circle"></i> Alasan / Keterangan Presensi:</label>
            <p class="m-0 text-dark">{{ $data_presensi->keterangan_presensi ?: 'Tidak ada keterangan.' }}</p>
        </div>

        <div class="section-title text-center">Lembar Pengesahan</div>
        <table class="table table-bordered approval-table" style="font-size: 11px;">
            <thead>
                <tr>
                    <th width="50%">Pemohon</th>
                    <th width="50%">HRD</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="approval-card">
                            <small class="text-muted">Diajukan</small>
                            {{-- <img src="{{ $ttd_user }}" class="signature-img"> --}}
                            <div class="signature-area">
                                <img src="{{ 'https://hrd.sumberpangan.store/storage/signatures/' . $data_presensi->ttd_user }}"
                                    style="max-width:180px; height:auto;">
                            </div>
                            <div class="footer-ttd-user">
                                <p class="name-label">
                                    {{ $user_karyawan ? $user_karyawan->name : '........................' }}</p>
                                <p class="tgl-label">Tgl :
                                    {{ \Carbon\Carbon::parse($data_presensi->created_at)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="approval-card">
                            <small class="text-muted">Disahkan Oleh</small>

                            <div class="signature-area">
                                @if ($data_presensi->ttd_hrd)
                                    <img src="{{ 'https://hrd.sumberpangan.store/storage/signatures/' . $data_presensi->ttd_hrd }}"
                                        style="max-width:180px; height:auto;">
                                @endif
                            </div>

                            <div class="footer-ttd-user">
                                <p class="name-label">
                                    {{ $data_presensi ? $data_presensi->nama_hrd : '........................' }}
                                </p>
                                <p class="tgl-label">
                                    Tgl :
                                    {{ \Carbon\Carbon::parse($data_presensi->waktu_approve_hrd)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="section-title text-center">Lampiran</div>
        <div class="note-box">

            <div class="form-floating form-floating-outline mb-2">
                <div class="form-control p-2 d-flex align-items-center justify-content-center overflow-hidden"
                    style="height: 300px; background-color: #f8fafc;">
                    <img id="preview-gambar"
                        src="{{ 'https://hrd.sumberpangan.store/storage/' . $data_presensi->foto_presensi }}"
                        alt="Foto Presensi" class="img-fluid rounded"
                        style="height: 100%; max-width: 100%; object-fit: contain;">
                </div>
                {{-- <label>FOTO PRESENSI</label> --}}
            </div>
        </div>
        <footer style="text-align:center; vertical-align: bottom; margin-top: 15px;">
            <small class="text-muted">Dokumen ini dibuat secara otomatis oleh Sistem HRD-APPS &copy; 2026</small>
        </footer>
    </div>
</body>

</html>
