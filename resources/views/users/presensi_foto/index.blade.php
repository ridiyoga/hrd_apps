@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        <style>.form-input {
            position: relative;
        }

        /* Offcanvas Kedua (Konfirmasi) */
        #off-canvas-konfirmasi-save {
            z-index: 1085 !important;
        }

        /* Backdrop untuk Offcanvas Kedua */
        /* Ini sedikit trik karena Bootstrap pakai satu class backdrop */
        #off-canvas-presensi {
            z-index: 1060 !important;
        }

        .offcanvas-backdrop+.offcanvas-backdrop {
            z-index: 1070 !important;
            opacity: 0.2 !important;
            /* Buat backdrop kedua lebih tipis agar tidak gelap total */
        }

        /* ICON */
        .form-input .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 6;
            pointer-events: none;
        }

        .profile-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .profile-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            border-bottom: none;
        }

        .profile-tabs .nav-link {
            border: none;
            border-radius: 14px;
            padding: 8px 12px;
            background: #f3e8ff;
            color: #6b21a8;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .profile-tabs .nav-link.active {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            color: #fff;
            box-shadow: 0 6px 14px rgba(168, 85, 247, 0.4);
        }

        .profile-tabs .nav-link i {
            font-size: 12px;
        }

        @media (max-width: 576px) {
            .profile-card {
                border-radius: 12px;
            }

            .profile-tabs span {
                font-size: 10px;
            }
        }

        /* Memaksa kalender muncul di paling depan */
        .daterangepicker {
            z-index: 9999 !important;
            /* Angka tinggi agar di atas elemen apapun */
            border: none !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border-radius: 12px !important;
            padding: 8px !important;
            margin-top: 5px !important;
        }

        /* Menghilangkan panah kecil di atas kalender agar lebih clean */
        .daterangepicker:before,
        .daterangepicker:after {
            display: none !important;
        }

        .daterangepicker .ranges li.active {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            color: #fff;
        }

        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            border-color: transparent;
            color: #fff;
        }

        .daterangepicker td.in-range {
            background-color: #dcd9fa;
            border-color: transparent;
            color: #000;
            border-radius: 0;
        }

        /* Responsif Mobile */
        @media (max-width: 768px) {
            .daterangepicker {
                width: 90% !important;
                left: 5% !important;
                right: 5% !important;
                position: fixed !important;
                /* Membuatnya 'floating' di tengah layar mobile */
                top: 20% !important;
            }

            .daterangepicker .drp-calendar {
                max-width: 100% !important;
            }
        }

        /* Style Signature Pad */
        .kbw-signature {
            width: 100%;
            height: 160px;
            border: 1px solid #f3e8ff !important;
            background: #ffffff;
        }

        #sig canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .border-dashed {
            border: 2px dashed #d8b4fe !important;
        }

        .opacity-75 {
            opacity: 0.75;
        }

        /* Agar tulisan di dalam kotak otorisasi rapi */
        .bg-light small {
            letter-spacing: 0.5px;
        }

        .futuristic-header {
            padding: 28px 20px 18px;
        }

        .futuristic-header h5 {
            color: --primary();
            font-weight: 600;
        }

        .futuristic-header small {
            color: rgba(255, 255, 255, 0.65);
        }

        .futuristic-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(145deg, #7b2ff7, #9b6dff);
            color: white;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
        }

        .coworker-card {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-0 {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-1 {
            /* Gradient oranye muda lembut ke putih */
            background: linear-gradient(135deg, #fff3e0, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan dengan tone warna oranye yang halus */
            box-shadow: 0 6px 16px rgba(253, 126, 20, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-2 {
            /* Gradient hijau lime/mint terang yang segar ke putih */
            background: linear-gradient(135deg, #e8fced, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan dengan tone hijau terang yang hidup dan bersih */
            box-shadow: 0 6px 18px rgba(40, 199, 111, 0.2);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-3 {
            /* Menggunakan hex asli kamu, tapi disempurnakan gradient-nya */
            background: linear-gradient(135deg, #b4edf7, #e0f2fe);
            border-radius: 14px;
            padding: 12px 14px;
            /* Box shadow diubah ke tone biru transparan */
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;

            /* Tambahan untuk teks */
            color: #0369a1;
            /* Biru gelap yang solid dan nyaman dibaca */
        }

        .coworker-card-3 .avatar-wrapper i {
            color: #0ea5e9;
            /* Aksan cyan terang */
        }

        .coworker-card-5 {
            /* Gradient merah terang benderang (semi-vibrant) ke putih */
            background: linear-gradient(135deg, #ffdde1, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Bayangan merah terang yang lebih tegas dan hidup */
            box-shadow: 0 6px 18px rgba(255, 0, 43, 0.18);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-history {
            background: linear-gradient(135deg, #cffbd1, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .icon-box-success {
            background: linear-gradient(135deg, #cffbd1, #ffffff);
            width: 55px;
            height: 55px;
            line-height: 50px;
            text-align: center;
            border: 1px solid #8ae68f;
            border-radius: 50%;
        }

        .icon-box-success i {
            color: #2f7e32;
        }

        .icon-box-reject {
            background: linear-gradient(135deg, #fbcfcf, #ffffff);
            width: 55px;
            height: 55px;
            line-height: 50px;
            text-align: center;
            border: 1px solid #dc8888;
            border-radius: 50%;
        }

        .icon-box-reject i {
            color: #8b3838;
        }

        .coworker-card-reject {
            background: linear-gradient(135deg, #fbcfcf, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
        }

        .skeleton {
            display: inline-block;
            background: linear-gradient(90deg,
                    #ede9fe 25%,
                    #f5f3ff 37%,
                    #ede9fe 63%);
            background-size: 400% 100%;
            animation: pulse 1.4s ease-in-out infinite;
            border-radius: 6px;
        }

        .skeleton-number {
            display: inline-block;
            /* ⬅️ WAJIB */
            width: 2ch;
            height: 30px;
            /* ⬅️ samakan dengan h3 */
            vertical-align: middle;
            /* ⬅️ penting */
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: .5;
            }

            50% {
                opacity: 1;
            }
        }

        .form-control {
            border-radius: 12px;
            padding-left: 42px;
            height: 44px;
            border: 1px solid #e5e5e5;
        }

        .form-input {
            position: relative;
            margin-bottom: 14px;
        }

        .form-input .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            /* transform: translateY(-50%); */
            color: #6f42c1;
            font-size: 14px;
        }

        /* ===============================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    SELECT LOADING
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          ================================ */
        .select-loader {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            z-index: 5;
            pointer-events: none;
        }

        /* ===============================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    DOT LOADER (3 DOT)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           ================================ */
        .dot-loader {
            display: flex;
            gap: 4px;
        }

        .dot-loader span {
            width: 6px;
            height: 6px;
            background-color: #7367f0;
            border-radius: 50%;
            opacity: 0.3;
            animation: dotPulse 1.2s infinite ease-in-out;
        }

        .dot-loader span:nth-child(1) {
            animation-delay: 0s;
        }

        .dot-loader span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot-loader span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes dotPulse {
            0% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.3;
            }
        }

        .quota-card-sm {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 5px;
            border: 1px solid #edf2f7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .highlight-purple {
            background: #f8f7ff;
            border: 1px solid #e9e5ff;
        }

        .q-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .q-value-wrap {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 2px;
        }

        .q-value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
        }

        .q-unit {
            font-size: 9px;
            color: #a0aec0;
            font-weight: 500;
        }

        /* Warna Khusus */
        .text-purple {
            color: #6b46c1;
        }

        .text-purple-bold {
            color: #553c9a;
        }

        .bg-purple {
            background-color: #6b46c1;
        }

        /* Aksen Bar di bawah card */
        .q-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            opacity: 0.6;
        }



        .btn-quota-modern {
            background: linear-gradient(135deg, #6f42c1 0%, #a855f7 100%);
            border: none;
            border-radius: 16px;
            padding: 6px 6px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(111, 66, 193, 0.3);
            transition: all 0.3s ease;
            width: 100%;
        }

        /* Efek saat tombol ditekan (Mobile Touch) */
        .btn-quota-modern:active {
            transform: scale(0.97);
            box-shadow: 0 4px 10px rgba(111, 66, 193, 0.2);
        }

        .btn-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(5px);
        }

        .text-box {
            display: flex;
            flex-direction: column;
        }

        .btn-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .btn-subtitle {
            font-size: 11px;
            opacity: 0.8;
            font-weight: 400;
        }

        .arrow-icon {
            font-size: 14px;
            opacity: 0.7;
        }

        /* Aksen Abstrak di dalam tombol */
        .btn-quota-modern::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }

        .badge-status-right {
            position: absolute;
            /* Sesuaikan top agar sejajar lurus dengan teks "SALDO CUTI" */
            top: 20px;
            /* Jarak dari kanan (sebelum tombol close) */
            right: 5px;

            background-color: #e8fadf;
            color: #28a745;
            font-size: 10px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
            /* Bentuk kapsul lebih elegan */
            text-transform: uppercase;
            border: 1px solid #c3e6cb;
        }

        .filter-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
        }

        /* Warna ungu lembut untuk select box */
        .form-select-purple {
            border-color: #d6bcfa !important;
            /* Soft purple border */
            color: #4a5568;
        }

        .form-select-purple:focus {
            border-color: #9f7aea !important;
            box-shadow: 0 0 0 0.25rem rgba(159, 122, 234, 0.25) !important;
        }

        /* Tombol warna ungu estetik */
        .btn-purple {
            background-color: #805ad5 !important;
            border-color: #805ad5 !important;
            color: #ffffff !important;
            transition: all 0.2s ease-in-out;
        }

        .btn-purple:hover {
            background-color: #6b46c1 !important;
            border-color: #6b46c1 !important;
            box-shadow: 0 4px 6px -1px rgba(128, 90, 213, 0.2), 0 2px 4px -1px rgba(128, 90, 213, 0.1) !important;
        }

        .btn-purple:active {
            background-color: #553c9a !important;
        }

        .border-dashed {
            border-style: dashed !important;
            border-color: #cbd5e1 !important;
            transition: all 0.3s ease;
        }

        .file-upload-wrapper {
            cursor: pointer;
        }

        .file-upload-wrapper:hover {
            background-color: #f1f5f9 !important;
            border-color: #3b82f6 !important;
        }

        .file-upload-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <h5 class="mt-3 mb-1">PRESENSI FOTO</h5>
            <small class="text-muted">Informasi Pengajuan Presensi Foto</small>
        </div>
        <div class="alert alert-info d-flex align-items-center gap-2 rounded-3">
            <i class="fa-solid fa-info-circle fs-5"></i>
            <div class="alert-content">
                <strong>Informasi</strong><br>
                Gunakan fitur ini jika anda di luar area kantor atau ketika presensi finger mengalami gangguan
            </div>
        </div>
        <div class="card filter-card mb-4 rounded-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label for="filterBulan" class="form-label small fw-semibold text-secondary mb-1">Bulan</label>
                        <select class="form-select form-select-sm form-select-purple py-2" id="filterBulan">
                            <option value="">-- Pilih Bulan --</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="filterTahun" class="form-label small fw-semibold text-secondary mb-1">Tahun</label>
                        <select class="form-select form-select-sm form-select-purple py-2" id="filterTahun">
                            <option value="">-- Pilih Tahun --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="button" id="btnSearch"
                            class="btn btn-sm btn-purple w-100 py-2 fw-medium rounded-2 d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-magnifying-glass small"></i>
                            <span>Cari Data Periode</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert_add_cuti"></div>
        <div id="alert_pengajuan_cuti"></div>
        @if (session('hapus_presensi_sukses'))
            <div class="alert alert-success alert-dismissible fade show alert_pengajuan_cuti">
                {{ session('hapus_presensi_sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('hapus_presensi_gagal'))
            <div class="alert alert-danger alert-dismissible fade show alert_pengajuan_cuti">
                {{ session('hapus_presensi_gagal') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card-body px-3 px-md-4">
            <ul class="nav profile-tabs" id="profileTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-presensi" role="tab"
                        aria-controls="nav-presensi" aria-selected="true">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Presensi</span>
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#histori" role="tab"
                        aria-controls="histori" aria-selected="false">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Presensi Histori</span>
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="nav-presensi" role="tabpanel" aria-labelledby="nav-presensi-tab">

                    <button class="btn btn-sm light btn-primary mb-3" type="button" id="btn_add_presensi"><i
                            class="fa-solid fa-plus me-2"></i>Tambah</button>
                    <div class="content-presensi"></div>
                </div>
                <div class="tab-pane fade" id="histori" role="tabpanel" aria-labelledby="nav-history-tab">
                    <div class="alert_history_cuti"></div>
                    <div class="riwayat_presensi">
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off_canvas_presensi" style="height: 95vh;">
        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Form Pengajuan Presensi</h6>
                    <small class="text-muted">Pengajuan Presensi Foto </small>
                </div>

            </div>

            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <form class="my-2" method="post" id="form_presensi_presensi" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
                <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
                <!-- Form input -->
                <div class="col-12">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">1</span>
                            <h6 class="mb-0 fw-bold">Foto dan Keterangan</h6>
                        </div>
                        <div class="mb-4">

                            <!-- Upload Box Estetik -->
                            <div class="mb-4">
                                <!-- Label Utama -->
                                <label for="foto_presensi" class="form-label fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-camera-retro text-primary me-2"></i>Foto Presensi
                                </label>
                                <div id="notif_hari" class="alert alert-danger py-2 px-3 mb-2" role="alert"
                                    style="font-size: 11px; line-height: 1.3;">
                                    *Foto wajib menampilkan wajah dan menggunakan GPS cam. Apabila tidak, maka pengajuan
                                    presensi foto akan ditolak
                                </div>
                                <!-- Container Alert -->
                                <div class="alert_pulang mb-2"></div>

                                <!-- Upload Box Estetik -->
                                <div
                                    class="file-upload-wrapper text-center p-4 rounded-0 rounded-end-4 border-2 border-dashed bg-light position-relative">
                                    <input type="file" id="foto_presensi" name="foto_presensi"
                                        class="file-upload-input" accept="image/" />

                                    <!-- Tampilan Default (Belum Ada File) -->
                                    <div class="file-upload-content" id="upload_default">
                                        <div class="icon-circle mb-3 mx-auto bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 60px;">
                                            <i class="fa-solid fa-cloud-arrow-up fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="fw-semibold text-dark mb-1">Unggah Foto Presensi</h6>
                                        <p class="text-muted small mb-0">Tarik & lepas file ke sini, atau <span
                                                class="text-primary fw-bold">Cari File</span></p>
                                        <span class="badge bg-white text-secondary border mt-2 font-monospace">Format: JPG,
                                            PNG, PDF (Max 2MB)</span>
                                    </div>

                                    <!-- Tampilan Sukses (Setelah File Dipilih) -->
                                    <div class="file-upload-content d-none" id="upload_success">
                                        <div class="icon-circle mb-3 mx-auto bg-success bg-opacity-10 text-success shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 60px;">
                                            <i class="fa-solid fa-file-circle-check fs-3"></i>
                                        </div>
                                        <h6 class="fw-semibold text-dark mb-1" id="file_name_display">Nama_File.pdf</h6>
                                        <p class="text-muted small mb-0" id="file_size_display">0 KB</p>
                                        <span class="badge bg-success text-white mt-2">
                                            <i class="fa-solid fa-check me-1"></i>File Berhasil Dipilih
                                        </span>
                                        <div class="mt-2">
                                            <small class="text-primary text-decoration-underline fw-semibold">Klik untuk
                                                mengganti file</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group keterangan_presensi">


                            <label for="keterangan_presensi" class="form-label">Keterangan Presensi</label>
                            <div class="form-input keterangan_presensi">
                                <span class="input-icon">
                                    <i class="fa-solid fa-note-sticky"></i>
                                </span>
                                <textarea name="keterangan_presensi" id="keterangan_presensi" cols="30" rows="3" class="form-control"
                                    placeholder="Keterangan Presensi"></textarea>
                            </div>
                        </div>
                        {{-- <div class="form-group lokasi_presensi">


                            <label for="lokasi_presensi" class="form-label">Lokasi</label>
                            <div class="form-input lokasi_presensi">
                                <span class="input-icon">
                                    <i class="fa-solid fa-note-sticky"></i>
                                </span>
                                <textarea name="lokasi_presensi" id="lokasi_presensi" cols="30" rows="3" class="form-control"
                                    placeholder="Lokasi Presensi" readonly></textarea>
                            </div>
                        </div> --}}

                        <input type="hidden" name="latitude" id="latitude">
                        {{-- <input type="hidden" name="lokasi_presensi" id="lokasi_presensi"> --}}

                        <input type="hidden" name="longitude" id="longitude">
                        {{-- <div class="my-2">
                            <button type="button" id="btn_ambil_lokasi" class="btn btn-primary">
                                <i class="fas fa-location-dot"></i>
                                &nbsp;Ambil Lokasi
                            </button>
                        </div> --}}


                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2">2</span>
                            <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                        </div>

                        <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                            <div class="card-header bg-light border-0 py-2 text-center">
                                <input type="text" name="name" id="name" value="{{ $user_karyawan->name }}"
                                    hidden>
                                <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                    id="user_pengajuan">Diajukan Oleh: {{ $user_karyawan->name }}</small>
                            </div>
                            <div class="card-body p-2">
                                <div class="alert_signature"></div>
                                <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                    <canvas id="signature-pad"
                                        style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                        hidden></canvas>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                    <button type="button"
                                        class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="clear">
                                        <i class="fa-solid fa-eraser me-1"></i> Hapus
                                    </button>
                                    <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di atas</small>
                                </div>
                                <textarea id="signature" name="signature" style="display: none"></textarea>
                            </div>
                        </div>

                        <div class="row g-2 d-flex align-items-stretch">

                            <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">HRD:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                    </div>
                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="hrd_selected">HRD</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert_error_cuti col-12"></div>
                    <div id="btn_pengajuan"></div>
                    <div id="btn_pengajuan_luar">
                        <button type="button" id="btn_simpan_presensi"
                            class="btn btn-primary btn-sm w-100 py-3 rounded-pill fw-bold mb-2 shadow">
                            AJUKAN PRESENSI
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('users.logout_confirm')
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
        $(document).ready(function() {
            let menu_active = '{{ $menu_id }}';

            var session_sukses = "{{ session('hapus_presensi_sukses') }}";
            var session_gagal = "{{ session('hapus_presensi_gagal') }}";

            var user_karyawan_id = '{{ $user_karyawan->id }}';

            function showSelectLoading(selectId) {
                $(selectId).prop('disabled', true)
                    .closest('.form-input')
                    .find('.select-loader')
                    .removeClass('d-none');
            }

            function hideSelectLoading(selectId) {
                $(selectId).prop('disabled', false)
                    .closest('.form-input')
                    .find('.select-loader')
                    .addClass('d-none');
            }

            $('#foto_presensi').on('change', function() {

                const file = this.files[0];

                // Jika tidak ada file
                if (!file) {
                    $('#upload_default').removeClass('d-none');
                    $('#upload_success').addClass('d-none');
                    return;
                }

                // ==========================================
                // 1. Validasi tipe file
                // ==========================================

                const allowedTypes = [
                    'image/jpeg',
                    'image/png'
                ];

                if (!allowedTypes.includes(file.type)) {

                    Swal.fire({
                        icon: 'error',
                        title: 'File tidak valid',
                        text: 'Hanya file JPG, JPEG, dan PNG yang diperbolehkan.'
                    });

                    // Reset input
                    $(this).val('');

                    // Kembalikan tampilan
                    $('#upload_default').removeClass('d-none');
                    $('#upload_success').addClass('d-none');

                    return;
                }


                // ==========================================
                // 2. Validasi ukuran file
                // Maksimal 2 MB = 2048 KB
                // ==========================================

                const maxSize = 5 * 1024 * 1024;

                if (file.size > maxSize) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran file terlalu besar',
                        text: 'Ukuran foto maksimal 5 MB.'
                    });

                    // Reset input
                    $(this).val('');

                    // Kembalikan tampilan
                    $('#upload_default').removeClass('d-none');
                    $('#upload_success').addClass('d-none');

                    return;
                }


                // ==========================================
                // 3. Hitung ukuran file
                // ==========================================

                let sizeText;

                if (file.size < 1024 * 1024) {

                    sizeText = (file.size / 1024).toFixed(1) + ' KB';

                } else {

                    sizeText = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

                }


                // ==========================================
                // 4. Tampilkan nama dan ukuran file
                // ==========================================

                $('#file_name_display').text(file.name);
                $('#file_size_display').text(sizeText);


                // ==========================================
                // 5. Ganti tampilan
                // ==========================================

                $('#upload_default').addClass('d-none');
                $('#upload_success').removeClass('d-none');

            });

            // Ambil Lokasi

            // end ambil lokasi

            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas);

            $('#clear').on('click', () => signaturePad.clear());
            $('#btn_add_presensi').on('click', function() {
                $('#signature-pad').removeAttr('hidden');

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);

                    // ambil ukuran CSS asli
                    const width = canvas.offsetWidth;
                    const height = canvas.offsetHeight;

                    // set ukuran canvas sebenarnya
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;

                    const ctx = canvas.getContext('2d');
                    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                    ctx.scale(ratio, ratio);

                    signaturePad.clear();
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
                const offcanvasEl = document.getElementById('off_canvas_presensi');
                const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();

            });


            function showSkeleton(selector) {
                const el = $(selector);
                el.html(
                    `<span class="skeleton skeleton-number" style="width:100%"></span>`
                );
            }

            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }

            $('#btn_simpan_presensi').on('click', function() {

                let button = $(this);

                // Cek apakah browser mendukung GPS
                if (!navigator.geolocation) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Didukung',
                        text: 'Browser Anda tidak mendukung fitur lokasi.'
                    });

                    return;
                }

                // Loading
                button.prop('disabled', true);

                Swal.fire({
                    title: 'Mengambil lokasi...',
                    text: 'Mohon izinkan akses lokasi pada browser.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });

                navigator.geolocation.getCurrentPosition(

                    // =========================
                    // BERHASIL
                    // =========================
                    function(position) {

                        let latitude = position.coords.latitude;
                        let longitude = position.coords.longitude;

                        // console.log('Latitude:', latitude);
                        // console.log('Longitude:', longitude);

                        // Simpan koordinat
                        $('#latitude').val(latitude);
                        $('#longitude').val(longitude);

                        // Ambil alamat berdasarkan koordinat
                        let signature_value = '';
                        if (typeof signaturePad !== 'undefined' && !signaturePad
                            .isEmpty()) {
                            signature_value = signaturePad.toDataURL();
                        }
                        $('#signature').val(signature_value);

                        let formElement = $('#form_presensi_presensi')[
                            0]; // Ambil elemen DOM form
                        let formData = new FormData(
                            formElement); // Bungkus semua input + file + @csrf
                        $('#signature').val(signature_value);
                        $.ajax({
                            url: "{{ url('/presensi_foto/proses_pengajuan_presensi') }}" +
                                '/' +
                                menu_active,
                            type: "POST",
                            data: formData, // Gunakan objek FormData
                            processData: false, // WAJIB: Mencegah jQuery mengubah FormData jadi string
                            contentType: false, // WAJIB: Biarkan browser mengatur header multipart/form-data
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Memuat Data...',
                                    html: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            error: function(xhr) {
                                Swal.close();
                                // Tampilkan detail error jika CSRF atau server error terjadi
                                console.error("XHR Response:", xhr
                                    .responseText);
                                alert('Terjadi kesalahan pada server!');
                            },
                            success: function(data) {
                                button.prop('disabled', false);

                                Swal.close();
                                if (data.code == 200) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 5000
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else if (data.code == 400) {
                                    button.prop('disabled', false);

                                    let errors = data.errors;
                                    let errorMessages = '';

                                    Object.keys(errors).forEach(
                                        function(key) {
                                            errors[key].forEach(
                                                function(
                                                    message) {
                                                    errorMessages
                                                        +=
                                                        `• ${message}\n`;
                                                });
                                        });
                                    Swal.fire({
                                        text: errorMessages,
                                        icon: 'warning',
                                        timer: 4500
                                    });
                                } else if (data.code == 510) {
                                    button.prop('disabled', false);

                                    Swal.fire({
                                        text: data.message,
                                        icon: 'warning',
                                        timer: 4500
                                    });
                                } else {
                                    button.prop('disabled', false);

                                    Swal.fire({
                                        title: 'Gagal',
                                        text: data.error,
                                        icon: 'error',
                                        timer: 4500
                                    });
                                }
                            }
                        });
                    },

                    // =========================
                    // GAGAL
                    // =========================
                    function(error) {
                        button.prop('disabled', false);


                        console.error('GPS Error:', error);

                        button.prop('disabled', false);

                        let message = 'Gagal mendapatkan lokasi.';

                        switch (error.code) {

                            case error.PERMISSION_DENIED:
                                message = 'Akses lokasi ditolak. Silakan izinkan lokasi pada browser.';
                                break;

                            case error.POSITION_UNAVAILABLE:
                                message = 'Lokasi tidak tersedia.';
                                break;

                            case error.TIMEOUT:
                                message = 'Waktu mengambil lokasi habis.';
                                break;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengambil Lokasi',
                            text: message
                        });
                    },

                    // =========================
                    // OPTIONS
                    // =========================
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            });

            // $(document).on('click', '#btn_simpan_presensi', function(e) {


            //     // GANTI SERIALIZE DENGAN FORMDATA DI SINI

            // });
            const $filterBulan = $("#filterBulan");
            const $filterTahun = $("#filterTahun");

            // 1. Dapatkan info Bulan dan Tahun saat ini secara realtime
            const dateSekarang = new Date();
            const tahunSekarang = dateSekarang.getFullYear();
            const bulanSekarang = String(dateSekarang.getMonth() + 1).padStart(2, '0'); // Hasilnya "01" - "12"

            // 2. Isi data dropdown Tahun (mengambil rentang 3 tahun ke belakang)
            for (let i = tahunSekarang; i >= 2024; i--) {
                $filterTahun.append(`<option value="${i}">${i}</option>`);
            }

            // 3. Otomatis pilih opsi (selected) sesuai bulan & tahun aktif saat ini
            $filterBulan.val(bulanSekarang);
            $filterTahun.val(tahunSekarang);
            // 4. Fungsi Utama pengkondisian filter data


            function showSkeleton(selector) {
                const el = $(selector);
                el.html(
                    `<span class="skeleton skeleton-number" style="width:100%"></span>`
                );
            }

            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }
            load_data_presensi();

            function formatLabel(date) {
                const d = new Date(date);

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();

                return `${day}-${month}-${year}`;
            }

            function load_data_presensi() {
                let presensi = '';
                $.ajax({
                    url: "{{ url('/presensi_foto/get_presensi') }}" + '/' + menu_active,
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('.content-presensi');
                    },
                    error: function(data) {
                        console.log(data);
                        showAlertDanger(data.responseJSON.message);
                        presensi +=
                            `<div class="text-center my-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.content-presensi', presensi);
                    },
                    success: function(data) {
                        if (data.code == 200) {
                            if (data.data == '' || data.data == null) {
                                presensi += `<div class="text-center text-muted py-3">
                        <i class="fa-solid fa-file-lines fa-bounce fa-xl"></i>
                        <p class="mb-0 mt-2 small">Belum ada data</p>
                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    // console.log(element.status_presensi);
                                    let progressValue = 50,
                                        progressColor = 'bg-primary';
                                    if (element.status_presensi == 1) {
                                        progressValue = 100;
                                    }

                                    let indicator =
                                        `
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar ${progressColor} progress-animated border-0" style="width: ${progressValue}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 7pt; display: block; margin-top: 2px;">Progress Approval (${progressValue}%)</small>`;

                                    presensi += `<a class="btn_klik" href="{{ url('/presensi_foto/detail_presensi') }}/${menu_active}/${element.id}">
                            <div class="coworker-card d-flex align-items-center mb-3 p-2-5" style="padding: 12px 15px;">
                                <div class="avatar-wrapper"><i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i></div>
                               <div class="flex-grow-1 ms-3 text-start">
                                    <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">Presensi Foto</h6>
                                    
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-calendar me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${formatLabel(element.created_at)}</span>
                                    </div>
                                    
                                    
                                    ${indicator}
                                </div>
                                <div class="icon-box" style="font-size: 12pt;"><i class="fa-regular fa-file-lines"></i></div>
                            </div>
                        </a>`;
                                });
                            }
                        } else {
                            showAlertDanger(data.message);
                            presensi +=
                                `<div class="text-center my-3"><small class="text-muted">${data.message}</small></div>`;
                        }
                        hideSkeleton('.content-presensi', presensi);
                    }
                });
            }

            function showAlertDanger(message) {
                $('.alert_add_cuti').stop(true, true).hide().html(
                    `<div class="alert alert-danger light alert-md alert-dismissible fade show py-2-5 mb-3">${message}</div>`
                ).fadeIn(100);
                setTimeout(function() {
                    $('.alert_add_cuti').animate({
                        opacity: 0,
                        marginTop: '-8px'
                    }, 1000, function() {
                        $(this).slideUp('slow', function() {
                            $(this).html('').css({
                                'opacity': '',
                                'margin-top': ''
                            });
                        });
                    });
                }, 5000);
            }

            // Fungsi helper untuk menghindari duplikasi kode animasi alert yang panjang
            function showAlertDanger(message) {
                $('.alert_add_cuti').stop(true, true).hide().html(
                    `<div class="alert alert-danger light alert-sm alert-dismissible fade show py-2 mb-2">${message}</div>`
                ).fadeIn(100);
                setTimeout(function() {
                    $('.alert_add_cuti').animate({
                        opacity: 0,
                        marginTop: '-5px'
                    }, 1000, function() {
                        $(this).slideUp('slow', function() {
                            $(this).html('').css({
                                'opacity': '',
                                'margin-top': ''
                            });
                        });
                    });
                }, 4000); // Dikurangi sedikit durasinya agar lebih efisien
            }

            $("#btnSearch").on("click", function() {
                const $btn = $(this);
                const originalText = $btn.html();

                // Beri efek loading spinner estetik agar user tahu data sedang di-refresh
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...'
                ).prop('disabled', true);

                // Eksekusi refresh data
                muatData();

                // Kembalikan tombol ke kondisi normal setelah AJAX diproses (simulasi jeda 500ms)
                setTimeout(function() {
                    $btn.html(originalText).prop('disabled', false);
                }, 500);
            });

            muatData()

            function muatData() {
                const bulanTerpilih = $filterBulan.val() || bulanSekarang;
                const tahunTerpilih = $filterTahun.val() || tahunSekarang;
                // --- LOGIKA MENCARI TANGGAL AKHIR DINAMIS ---
                // Konversi string bulan ke integer (basis 10)
                const intBulan = parseInt(bulanTerpilih, 10);
                const intTahun = parseInt(tahunTerpilih, 10);

                // Cari tahu tanggal terakhir di bulan & tahun tersebut (Otomatis deteksi Kabisat)
                const tglTerakhir = new Date(intTahun, intBulan, 0).getDate();

                // Format Tanggal Awal dan Akhir (Hasil: "01-01" s/d "31-01")
                const tanggalMulai = `${tahunTerpilih}-${bulanTerpilih}-01`;
                const tanggalSelesai = `${tahunTerpilih}-${bulanTerpilih}-${String(tglTerakhir).padStart(2, '0')}`;

                let awal = tanggalMulai;
                let akhir = tanggalSelesai;

                load_riwayat_presensi(awal, akhir)

            }

            function load_riwayat_presensi(awal, akhir) {
                let presensi = '';
                $.ajax({
                    url: "{{ url('/presensi_foto/get_presensi_riwayat') }}",
                    type: "GET",
                    data: {
                        awal: awal,
                        akhir: akhir,
                    },
                    beforeSend: function() {
                        showSkeleton('.riwayat_presensi');
                    },
                    error: function(data) {
                        showAlertDanger(data.responseJSON.message);
                        presensi +=
                            `<div class="text-center my-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.riwayat_presensi', presensi);
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.code == 200) {
                            if (data.data == '' || data.data == null) {
                                presensi += `<div class="text-center text-muted py-3">
                        <i class="fa-solid fa-file-lines fa-bounce fa-xl"></i>
                        <p class="mb-0 mt-2 small">Belum ada data</p>
                    </div>`;
                            } else {
                                data.data.forEach(element => {
                                    let progressValue = 50,
                                        progressColor = 'bg-primary';
                                    if (element.status_presensi == 1) {
                                        progressValue = 100;
                                    } else if (element.status_presensi == 5) {
                                        progressValue = 100;
                                        progressColor = 'bg-danger';
                                    }

                                    let indicator =
                                        `
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar ${progressColor} progress-animated border-0" style="width: ${progressValue}%"></div>
                            </div>
                            <small class="text-muted" style="font-size: 7pt; display: block; margin-top: 2px;">Progress Approval (${progressValue}%)</small>`;

                                    presensi += `<a class="btn_klik" href="{{ url('/presensi_foto/detail_presensi') }}/${menu_active}/${element.id}">
                            <div class="coworker-card-${element.status_presensi} d-flex align-items-center mb-3 p-2-5" style="padding: 12px 15px;">
                                <div class="avatar-wrapper"><i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i></div>
                               <div class="flex-grow-1  ms-3 text-start">
                                    <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">Presensi Foto</h6>
                                    
                                    <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                                        <i class="fa fa-calendar me-2" style="width: 14px; text-align: center;"></i>
                                        <span class="price-item">${formatLabel(element.created_at)}</span>
                                    </div>
                                    
                                    
                                    ${indicator}
                                </div>
                                <div class="icon-box" style="font-size: 12pt;"><i class="fa-regular fa-file-lines"></i></div>
                            </div>
                        </a>`;
                                });
                            }
                        } else {
                            showAlertDanger(data.message);
                            presensi +=
                                `<div class="text-center my-3"><small class="text-muted">${data.message}</small></div>`;
                        }
                        hideSkeleton('.riwayat_presensi', presensi);
                    }
                });
            }


        });
    </script>
@endsection
