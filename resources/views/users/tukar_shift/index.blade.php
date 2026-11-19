@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
        @media (max-width: 768px) {
            .daterangepicker {
                position: fixed !important;
                top: 10% !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                width: 90% !important;
                margin: 0 auto !important;
                z-index: 9999 !important;
            }

            .daterangepicker .drp-calendar {
                float: none !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .daterangepicker .drp-buttons {
                text-align: center !important;
            }
        }

        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1051 !important;
        }

        .user-card {
            width: 100%;
            border: 2px solid transparent;
            background-color: #ffffffff;
            border-radius: 10px;
            transition: 0.3s;
            cursor: pointer;
        }

        .user-card:hover {
            border-color: #fffefeff;
        }


        .user-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            font-size: 13px;
        }

        /* Kartu modal */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }



        .modal-title {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .input-group {
            margin-bottom: 10px;
        }

        .input-group .form-control[readonly] {
            background-color: #f1f1f1;
            /* font-weight: 600;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    width: 150px; */
            border-right: none;
            text-align: center;
        }

        .input-group select,
        .input-group input:not([readonly]) {
            border-left: none;
            font-weight: 600;
        }

        .table th {
            background-color: #0d6efd !important;
            color: white !important;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
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

        /* Legend status shift */
        .shift-legend {
            font-size: clamp(11px, 1.6vw, 14px);
        }

        .shift-legend .dot {
            width: clamp(8px, 1.5vw, 10px);
            height: clamp(8px, 1.5vw, 10px);
            border-radius: 50%;
        }

        .shift-legend p {
            margin: 0;
            line-height: 1.3;
        }

        .shift-select {
            min-width: 110px;
            max-width: 80%;
            max-height: fit-content;
            padding: 6px 10px;
            font-size: 10px;
            border-radius: 20px;
            border: 1px solid #e0e0e0;
            background-color: #fff;
            cursor: pointer;
        }

        .shift-select:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, .1);
        }

        .coworker-card {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* Card Ungu (Lebih Deep) */
        .coworker-card- {
            background: linear-gradient(135deg, #e9d5ff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #d8b4fe;
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* Card Peach/Cream (Lebih Warm) */
        .coworker-card1 {
            background: linear-gradient(135deg, #ffedd5, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #fed7aa;
            box-shadow: 0 4px 12px rgba(251, 146, 60, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card1 {
            background: linear-gradient(135deg, #ffedd5, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #fed7aa;
            box-shadow: 0 4px 12px rgba(251, 146, 60, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card11 {
            background: linear-gradient(135deg, #dcfce7, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #bbf7d0;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card12 {
            background: linear-gradient(135deg, #fecdd3, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #fda4af;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card22 {
            background: linear-gradient(135deg, #fecdd3, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #fda4af;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* Card Pink/Rose (Lebih Hidup) */
        .coworker-card2 {
            background: linear-gradient(135deg, #fecdd3, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #fda4af;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* Card Hijau/History (Lebih Segar) */
        .coworker-card-history1 {
            background: linear-gradient(135deg, #dcfce7, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid #bbf7d0;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        /* .coworker-card-history11 {
                                                                                                                        background: linear-gradient(135deg, #dcfce7, #ffffff);
                                                                                                                        border-radius: 14px;
                                                                                                                        padding: 12px 14px;
                                                                                                                        border: 1px solid #bbf7d0;
                                                                                                                        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
                                                                                                                        transition: transform .2s ease, box-shadow .2s ease;
                                                                                                                    } */

        .coworker-card-history2 {
            /* Menurunkan saturasi biru agar teks abu-abu 'keluar' */
            background: linear-gradient(135deg, #d1f0fd, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            /* Border dibuat lebih soft agar tidak mendominasi pandangan */
            border: 1px solid #b3e5fc;
            /* Shadow tetap biru tapi sangat halus */
            box-shadow: 0 4px 12px rgba(0, 110, 150, 0.08);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card-history3 {
            /* Gradasi abu-abu lembut ke putih agar tetap bersih */
            background: linear-gradient(135deg, #f1f5f9, #ffffff);

            border-radius: 14px;
            padding: 12px 14px;

            /* Border abu-abu yang sedikit lebih gelap agar tegas */
            border: 1px solid #cbd5e1;

            /* Shadow yang halus namun memberikan efek kedalaman */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

            /* Transition tetap sama */
            transition: transform .2s ease, box-shadow .2s ease;

            /* Pastikan warna teks utama sangat gelap agar kontras */
            color: #1e293b;
        }

        /* Hover Effect: Tetap konsisten untuk semua kartu */
        .coworker-card-:hover,
        .coworker-card1:hover,
        .coworker-card2:hover,
        .coworker-card-history:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        /* Container Utama */
        .user-checkbox-card {
            border: 2px solid #DCDCDC;
            border-radius: 14px;
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
            display: block;
        }

        /* Icon check default */
        .check-icon {
            color: #ccc;
            font-size: 1.2rem;
        }

        /* EFEK SAAT CHECKBOX DICHECK */
        .btn-check-input:checked+.user-checkbox-card {
            background-color: #e8f4fd;
            /* Warna background saat aktif */
            border-color: #2196f3;
            /* Warna border saat aktif */
        }

        .btn-check-input:checked+.user-checkbox-card .check-icon {
            color: #2196f3;
            /* Warna icon saat aktif */
        }

        .avatar-wrapper img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e6d9ff;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(111, 66, 193, .1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6f42c1;
            font-size: 14px;
        }

        .icon-box-history1 {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(50, 153, 84, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3aac48;
            font-size: 14px;
        }

        .icon-box-history2 {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            /* Biru yang lebih solid tapi tetap lembut */
            background: #e0f2fe;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Warna biru navy/deep sky untuk icon */
            color: #0369a1;
            font-size: 14px;
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

        .form-input {
            position: relative;
            margin-bottom: 14px;
        }

        .form-input .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #6f42c1;
            font-size: 14px;
        }

        /* Container utama: Dibuat lebih ramping dan menyatu dengan background */
        .profile-tabs {
            overflow: visible !important;
            background: #f1f5f9;
            /* Abu-abu sangat muda kebiruan (slate 100) */
            padding: 3px;
            border-radius: 10px;
            display: inline-flex;
            border: 1px solid #e2e8f0;
            gap: 2px;
        }

        .profile-tabs .nav-item {
            margin-bottom: 0 !important;
        }

        .profile-tabs .nav-link {
            border: none;
            border-radius: 8px;
            padding: 5px 12px;
            /* Diperkecil agar lebih ramping */
            background: transparent;
            color: #64748b;
            /* Warna teks yang kalem */
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            font-size: 12px;
            /* Ukuran font lebih kecil */
            transition: all 0.2s ease;
            white-space: nowrap;
            /* Mencegah teks turun ke bawah */
        }

        /* Efek Hover: Halus tanpa shadow berlebih */
        .profile-tabs .nav-link:hover:not(.active) {
            color: #7c3aed;
            background: rgba(124, 58, 237, 0.05);
        }

        /* Efek Hover: Halus tanpa shadow berlebih */
        .profile-tabs .nav-link:hover:not(.active) {
            color: #7c3aed;
            background: rgba(124, 58, 237, 0.05);
        }

        /* State Aktif: Fokus pada kebersihan visual */
        .profile-tabs .nav-link.active {
            background: #7c3aed;
            /* Putih bersih kontras dengan background abu */
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Shadow sangat tipis */
        }

        /* Styling Icon agar lebih kecil dan proporsional */
        .profile-tabs .nav-link i {
            font-size: 11px;
            opacity: 0.7;
        }

        .profile-tabs .nav-link.active i {
            opacity: 1;
        }

        .badge {
            font-size: 0.55rem;
            padding: 0.25em 0.45em;
            line-height: 1;
            top: -8px;
            right: -8px;
            z-index: 10;
        }

        /* Mengatur posisi spesifik gelembung angka */
        .custom-badge {
            top: -5px;
            /* Naik ke atas melewati garis frame */
            right: -10px;
            /* Geser ke kanan melewati garis frame */
            font-size: 0.65rem;
            padding: 0.3em 0.5em;
            z-index: 10;
            /* Pastikan berada di atas elemen lain */
        }

        /* Mengatur posisi spesifik titik merah (dot) */
        .custom-dot {
            top: -2px;
            right: -5px;
            z-index: 10;
        }

        /* Efek opsional: sedikit bayangan agar lebih pop-out */
        .bg-danger {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Perbaikan untuk Mobile agar tidak sesak */
        @media (max-width: 576px) {
            .profile-tabs {
                display: flex;
                /* Memenuhi lebar layar di mobile */
                width: 100%;
                overflow-x: auto;
                /* Jika layar sangat kecil, bisa di-scroll */
            }

            .profile-tabs .nav-item {
                flex: 1;
            }

            .profile-tabs .nav-link {
                justify-content: center;
                padding: 6px 4px;
                font-size: 11px;
                /* Font lebih kecil lagi di mobile */
            }
        }
    </style>
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
@endsection
@section('content_top')
    @if (session()->has('mappingshiftsuccess'))
        <div id="alert_addmappingsuccess" class="alert alert-success light alert-lg alert-dismissible fade show">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"
                stroke-linecap="round" stroke-linejoin="round" class="me-2">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                <line x1="9" y1="9" x2="9.01" y2="9"></line>
                <line x1="15" y1="9" x2="15.01" y2="9"></line>
            </svg>
            <strong>Success!</strong> Anda Berhasil Menyimpan Data
            <button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @elseif(session()->has('tambahmappingerror'))
        <div id="alert_statusmappingeditsuccess" class="alert alert-danger light alert-lg alert-dismissible fade show">

            <strong> <i class="fa-solid fa-triangle-exclamation"></i>&nbsp;Error!</strong>&nbsp;Error Memuat Laman Tambah
            Jadwal
            <button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="offcanvas offcanvas-bottom pwa-offcanvas">
            <div class="container">
                <div class="offcanvas-body small text-center">
                    <img src="{{ asset('assets/assets_users/images/icon_error.gif') }}" width="50" height="50"
                        alt="">
                    <div>{{ session()->get('tambahmappingerror') }}</div>
                </div>
            </div>
        </div>
        <div class="offcanvas-backdrop pwa-backdrop"></div>
    @endif
    <div id="shift-skeleton" class="card">
        {{-- Header --}}
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <h5 class="mt-3 mb-1">Jadwal Shift</h5>
            <small class="text-muted">Informasi Jadwal Shift</small>
        </div>
        <div class="card-body">
            <div id="alert_wrapper"></div>

            <input type="hidden" value="" id="start_date" name="start_date">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-outline-primary btn-sm" id="prevWeek">&lt;&nbsp;Prev</button>
                <h6 class="mb-0" id="weekLabel">Weekly Shift Overview</h6>
                <button class="btn btn-outline-primary btn-sm" id="nextWeek">Next&nbsp;&gt;</button>
            </div>

            <div class="mb-4">
                <div class="d-flex flex-nowrap overflow-auto gap-2 pb-2" id="dateContainer"></div>
            </div>


            <ul class="mb-3 nav profile-tabs" id="shiftTab" role="tablist" style="overflow: visible;">
                <li class="nav-item mf-1">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#shift">
                        <i class="fa-solid fa-user-clock"></i>
                        <span>Shift</span>
                    </button>
                </li>

                <li class="nav-item">
                    <!-- TAMBAHKAN position-relative DI SINI -->
                    <button class="nav-link position-relative" data-bs-toggle="tab" data-bs-target="#pengajuan"
                        style="overflow: visible;">
                        <i class="fa-regular fa-clock"></i>
                        <span>Pengajuan Ubah Shift</span>

                        <!-- Gunakan top & right negatif untuk membuatnya melewati frame -->
                        <span class="position-absolute badge rounded-pill bg-danger shadow-sm">
                            {{ $badge_pegawai_ubah }}
                        </span>
                    </button>

            </ul>
            </li>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="shift">
                    <div class="row shift-legend mb-3">
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-warning me-2"></span>
                            <p>Belum&nbsp;approve&nbsp;HRD</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-success me-2"></span>
                            <p>Sudah&nbsp;di-approve</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-info me-2"></span>
                            <p>Shift&nbsp;yang&nbsp;ditukar</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-light me-2"></span>
                            <p>Ditukar&nbsp;oleh&nbsp;atasan</p>
                        </div>
                    </div>
                    <div id="shiftList">
                        <p class="text-center text-muted my-3">Pilih tanggal untuk melihat jadwal shift.</p>
                    </div>
                </div>
                <!-- TAB PENGAJUAN -->
                <div class="tab-pane fade" id="pengajuan">
                    <div class="row shift-legend mb-3">
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-primary me-1"></span>
                            <p>Belum&nbsp;approve</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-warning me-1"></span>
                            <p>Approval&nbsp;Atasan</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-success me-1"></span>
                            <p>Approval&nbsp;HRD</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-danger me-1"></span>
                            <p>&nbsp;Ditolak</p>
                        </div>
                    </div>
                    <div id="tukarList">
                    </div>
                </div>
            </div>

        </div>


    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom rounded-top-5" style="height: auto;" data-bs-keyboard="false"
        data-bs-backdrop="true" id="off-canvas-tukar_shift">

        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Shift yang Ingin Diubah</h6>
                    <small class="text-muted">Pilih rekan kerja yang ingin ditukar shift </small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <div id="shiftList2" class="mt-2">
                <p class="text-center text-muted my-3">Pilih shift untuk ditukar jadwal shift.</p>
            </div>
            <div class="form-karyawan">
                <form class="my-2" method="post" id="form_add_karyawan" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="menu_id" name="menu_id" value="{{ $menu_active }}">
                    <input type="hidden" name="id_user" value="{{ Auth::user()->id }}">
                    <div class="mb-3">
                        @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-road',
                            'type' => 'text',
                            'name' => 'alasan',
                            'class' => 'alasan',
                            'label' => 'Alasan ubah Shift',
                            'value' => '',
                        ])
                    </div>
                    <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
                    <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                        <div class="card-header bg-light border-0 py-2 text-center">
                            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
                            <input type="text" name="name" id="name" value="{{ $user_karyawan->name }}"
                                hidden>
                            <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                id="user_pengajuan">Diajukan Oleh: {{ $user_karyawan->name }}</small>
                        </div>

                        <div class="card-body p-2">
                            <div class="alert_signature"></div>
                            <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                <canvas id="signature-pad" name="signature_pad"
                                    style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                    hidden></canvas>
                            </div>


                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">

                                <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                    id="clear">
                                    <i class="fa-solid fa-eraser me-1"></i> Hapus
                                </button>
                                <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di
                                    atas</small>
                            </div>

                        </div>
                    </div>
                    <div class="row g-2 d-flex align-items-stretch">
                        <div class="alert_atasan_1"></div>
                        <div class="alert_atasan_2"></div>
                        <div class="col-6">
                            <div
                                class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                    id="atasan1">ATASAN LANGSUNG</small>

                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                </div>
                                <input type="hidden" name="atasan_1" id="atasan_1"
                                    value="{{ $atasan1->karyawan_id }}">
                                <input type="hidden" name="atasan_1_name" id="atasan_1_name"
                                    value="{{ $atasan1->Karyawan->name }}">
                                <small class="d-block text-muted" style="font-size: 8px;">
                                    {{ $atasan1->Karyawan->name }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                    id="atasan1">HRD</small>

                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                </div>
                                <input type="hidden" name="" id="" value="">
                                <input type="hidden" name="" id="
                                "
                                    value="">
                                <small class="d-block text-muted" style="font-size: 8px;">
                                    ...</small>
                            </div>
                        </div>
                    </div>


                    <!-- Form input -->

                    <div class="mt-3 text-center">
                        <input type="text" name="tanggal" id="tanggal" class="form-control" hidden>
                        <button type="button" id="btn_update_shift" class="btn btn-success btn-sm">
                            <i class="fa fa-save"></i>&nbsp;Simpan
                        </button>
                        <a href="javascript:void(0);" class="btn btn-sm light btn-danger ms-2"
                            data-bs-dismiss="offcanvas" aria-label="Close">
                            &nbsp;Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="offcanvas offcanvas-bottom rounded-top-5" style="height: auto;" data-bs-keyboard="false"
        data-bs-backdrop="true" id="off-canvas-tukar_shift_form">

        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Form Pengajuan Shift</h6>
                    <small class="text-muted">List shift yang diajukan</small>
                </div>
            </div>
        </div>

        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <div class="mt-3 text-center">
                <div id="list_shift" class="d-flex flex-column gap-3"></div>
                <input type="text" name="id_mapping" id="id_mapping" class="form-control" hidden>
                <input type="text" name="id_pengaju" id="id_pengaju" class="form-control" hidden>
                <input type="text" name="sign_pengaju" id="sign_pengaju" class="form-control" hidden>
                <div id="detail_tukar_shift"></div>
                <div id="ttd_tukar_shift"></div>
                <div class="row g-2 d-flex align-items-stretch mb-2">
                    <div id="ttd_atasan" class="col-6"></div>
                    <div id="ttd_bawahan" class="col-6"></div>
                </div>
                <div class="d-flex justify-content-center">
                    {{-- @if ($user_karyawan->id != $pengaju->id_pengaju) --}}
                    <div id="div_hapus"></div>
                    {{-- @endif --}}

                    <a href="javascript:void(0);" class="btn btn-sm light btn-info ms-2" data-bs-dismiss="offcanvas"
                        aria-label="Close">
                        &nbsp;Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom rounded-top-5" style="height: auto;" data-bs-keyboard="false"
        data-bs-backdrop="true" id="off-canvas-tukar_konfirmasi">

        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Hapus Pengajuan Tukar Shift</h6>
                    <small class="text-muted">Apakah anda yakin akan menghapus pengajuan ini?</small>
                </div>
            </div>
        </div>

        <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
            <div class="mt-3 text-center">
                <form class="my-2" method="post" id="form_hapus_tukar" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="id_mapping_delete" id="id_mapping_delete" class="form-control" hidden>
                    <input type="text" name="id_pengaju_delete" id="id_pengaju_delete" class="form-control" hidden>
                    <input type="text" name="sign_pengaju" id="sign_pengaju_delete" class="form-control" hidden>
                    <input type="hidden" id="menu_id_delete" name="menu_id" value="{{ $menu_active }}">
                    <button type="button" id="btn_tukar_delete" class="btn btn-danger btn-sm">
                        <i class="fa-regular fa-trash-can"></i>&nbsp;Hapus
                    </button>
                    <a href="javascript:void(0);" class="btn btn-sm light btn-info ms-2" data-bs-dismiss="offcanvas"
                        aria-label="Close">
                        &nbsp;Batal
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h5 class="title">KONFIRMASI LOGOUT APP</h5>
            <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
            <form method="POST" action="{{ route('logout_user') }}">
                @csrf
                <a onclick="event.preventDefault(); this.closest('form').submit();"
                    class="btn_klik btn btn-sm btn-danger light pwa-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none">
                        <path
                            d="M5.46967 12.5303C5.17678 12.2374 5.17678 11.7626 5.46967 11.4697L7.46967 9.46967C7.76257 9.17678 8.23744 9.17678 8.53033 9.46967C8.82323 9.76256 8.82323 10.2374 8.53033 10.5303L7.81066 11.25L15 11.25C15.4142 11.25 15.75 11.5858 15.75 12C15.75 12.4142 15.4142 12.75 15 12.75L7.81066 12.75L8.53033 13.4697C8.82323 13.7626 8.82323 14.2374 8.53033 14.5303C8.23744 14.8232 7.76257 14.8232 7.46967 14.5303L5.46967 12.5303Z"
                            fill="#1C274C" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M13.9453 1.25H15.0551C16.4227 1.24998 17.525 1.24996 18.392 1.36652C19.2921 1.48754 20.05 1.74643 20.6519 2.34835C21.2538 2.95027 21.5127 3.70814 21.6337 4.60825C21.7503 5.47522 21.7502 6.57754 21.7502 7.94513V16.0549C21.7502 17.4225 21.7503 18.5248 21.6337 19.3918C21.5127 20.2919 21.2538 21.0497 20.6519 21.6517C20.05 22.2536 19.2921 22.5125 18.392 22.6335C17.525 22.75 16.4227 22.75 15.0551 22.75H13.9453C12.5778 22.75 11.4754 22.75 10.6085 22.6335C9.70836 22.5125 8.95048 22.2536 8.34857 21.6517C7.94963 21.2527 7.70068 20.7844 7.54305 20.2498C6.59168 20.2486 5.79906 20.2381 5.15689 20.1518C4.39294 20.0491 3.7306 19.8268 3.20191 19.2981C2.67321 18.7694 2.45093 18.1071 2.34822 17.3431C2.24996 16.6123 2.24998 15.6865 2.25 14.5537V9.44631C2.24998 8.31349 2.24996 7.38774 2.34822 6.65689C2.45093 5.89294 2.67321 5.2306 3.20191 4.7019C3.7306 4.17321 4.39294 3.95093 5.15689 3.84822C5.79906 3.76188 6.59168 3.75142 7.54305 3.75017C7.70068 3.21562 7.94963 2.74729 8.34857 2.34835C8.95048 1.74643 9.70836 1.48754 10.6085 1.36652C11.4754 1.24996 12.5778 1.24998 13.9453 1.25ZM7.25197 17.0042C7.25555 17.6487 7.2662 18.2293 7.30285 18.7491C6.46836 18.7459 5.848 18.7312 5.35676 18.6652C4.75914 18.5848 4.46611 18.441 4.26257 18.2374C4.05903 18.0339 3.91519 17.7409 3.83484 17.1432C3.7516 16.5241 3.75 15.6997 3.75 14.5V9.5C3.75 8.30029 3.7516 7.47595 3.83484 6.85676C3.91519 6.25914 4.05903 5.9661 4.26257 5.76256C4.46611 5.55902 4.75914 5.41519 5.35676 5.33484C5.848 5.2688 6.46836 5.25415 7.30285 5.25091C7.2662 5.77073 7.25555 6.35129 7.25197 6.99583C7.24966 7.41003 7.58357 7.74768 7.99778 7.74999C8.41199 7.7523 8.74964 7.41838 8.75194 7.00418C8.75803 5.91068 8.78643 5.1356 8.89448 4.54735C8.9986 3.98054 9.16577 3.65246 9.40923 3.40901C9.68599 3.13225 10.0746 2.9518 10.8083 2.85315C11.5637 2.75159 12.5648 2.75 14.0002 2.75H15.0002C16.4356 2.75 17.4367 2.75159 18.1921 2.85315C18.9259 2.9518 19.3144 3.13225 19.5912 3.40901C19.868 3.68577 20.0484 4.07435 20.1471 4.80812C20.2486 5.56347 20.2502 6.56459 20.2502 8V16C20.2502 17.4354 20.2486 18.4365 20.1471 19.1919C20.0484 19.9257 19.868 20.3142 19.5912 20.591C19.3144 20.8678 18.9259 21.0482 18.1921 21.1469C17.4367 21.2484 16.4356 21.25 15.0002 21.25H14.0002C12.5648 21.25 11.5637 21.2484 10.8083 21.1469C10.0746 21.0482 9.68599 20.8678 9.40923 20.591C9.16577 20.3475 8.9986 20.0195 8.89448 19.4527C8.78643 18.8644 8.75803 18.0893 8.75194 16.9958C8.74964 16.5816 8.41199 16.2477 7.99778 16.25C7.58357 16.2523 7.24966 16.59 7.25197 17.0042Z"
                            fill="#1C274C" />
                    </svg>
                    &nbsp;Logout
                </a>
                <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                    aria-label="Close">Batal</a>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>


    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                Swal.close();
            }
        });

        $(document).ready(function() {

            var canvas = document.getElementById('signature-pad');
            var signaturePad = new SignaturePad(canvas);
            $('#signature-pad').removeAttr('hidden');
            $(document).on('click', '#clear', function() {
                signaturePad.clear();
            })

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

            const shiftList = $("#shiftList");
            const weekLabel = $("#weekLabel");

            const tukarList = $("#tukarList");

            let currentStart = new Date();
            currentStart.setDate(currentStart.getDate() - currentStart.getDay() + 1);

            // ===============================
            // INIT
            // ===============================
            loadWeek();

            $("#prevWeek").on("click", () => changeWeek(-1));
            $("#nextWeek").on("click", () => changeWeek(1));

            function changeWeek(direction) {
                currentStart.setDate(currentStart.getDate() + (direction * 7));
                loadWeek();
            }

            // ===============================
            // LOAD DATA (PER MINGGU)
            // ===============================
            let globalShift = [];

            function loadWeek() {
                const start = formatISO(currentStart);
                const end = formatISO(new Date(currentStart.getTime() + 6 * 86400000));



                weekLabel.text(
                    `${formatLabel(currentStart)} - ${formatLabel(new Date(currentStart.getTime() + 6 * 86400000))}`
                );

                $('#start_date').val(start);

                showSkeleton('#shiftList');
                showSkeleton('#tukarList');
                globalStart = start;
                globalEnd = end;

                $.getJSON(`/tukar_shift/get_TukarShiftData?start=${start}&end=${end}`)
                    .done(function(data) {

                        let shifts = flattenData(data.data).flat();
                        let alert = data.alert;
                        globalShift = shifts;
                        globalAlert = alert;
                        console.log(alert);

                        renderShifts(shifts, alert, start, end);
                    })
                    .fail(function() {
                        shiftList.html('<p class="text-danger text-center">Gagal memuat data</p>');
                    });
                $.getJSON(
                        `/tukar_shift/get_TukarShiftData_pengajuan/{{ $atasan1->Karyawan->id }}/?start=${start}&end=${end}`
                    )
                    .done(function(data) {

                        let tukars = flattenData(data).flat();

                        // console.log(tukars);
                        // globalTukars = tukars;
                        renderTukars(tukars, start, end);
                    })
                    .fail(function() {
                        tukarList.html('<p class="text-danger text-center">Gagal memuat data</p>');
                    });
            }

            // ===============================
            // FLATTEN DATA (ANTI RIBET)
            // ===============================
            function flattenData(data) {
                if (Array.isArray(data)) return data;

                let result = [];

                Object.keys(data).forEach(date => {
                    data[date].forEach(item => {
                        result.push({
                            ...item,
                            tanggal: date
                        });
                    });
                });

                return result;
            }

            // ===============================
            // RENDER SHIFT (SEMUA DATA)
            // ===============================
            function renderShifts(shifts, alert, start, end) {

                // console.log(shifts[0]);
                let html = `<h6 class="mb-3 text-center">Jadwal Shift Mingguan</h6>`;



                if (!shifts || shifts.length === 0) {

                    html += `<p class="text-center text-muted">Tidak ada jadwal shift.</p>`;
                    return shiftList.html(html);
                } else {
                    var approve = shifts[0];
                    // console.log(shifts[0]);
                    // if (approve.status_approved == 1) {
                    //     let antrian = alert;
                    //     // console.log(antrian);
                    //     if (antrian == 0) {
                    //         html += `<div class="mb-4">
                //         <a id="btnTukar" type="button"
                //         data-start="${start}"
                //         data-end="${end}"
                //         class="btn btn-sm btn-outline-primary" href="javascript:void(0)">
                //             <i class="fa-solid fa-right-left"></i>&nbsp;
                //             Tukar Shift
                //         </a>
                //     </div>`;
                    //     } else {
                    //         html += ``;
                    //     }

                    // }
                }

                // 🔥 sorting by tanggal
                shifts.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));



                shifts.forEach(s => {
                    console.log(s.status_approved);

                    let approved = s.status_approved == 1 ? '-history' : '';

                    html += `
                    <div class="">

                        <div class="coworker-card${approved}${s.is_active} d-flex align-items-center mb-3">

                            <div class="avatar-wrapper">
                                <i class="fa-solid fa-dot"></i>
                            </div>

                            <div class="flex-grow-1 ms-3 text-start">
                                <h6 class="mb-1 fw-semibold">${s.tanggal_alf} • Shift ${s.shift} </h6>

                                <div class="text-muted small">
                                    ${s.tanggal_masuk} • 
                                    <span class="mx-1">•</span>
                                    ${s.jam_shift}
                                </div>
                            </div>

                            <div class="icon-box${approved}${s.is_active}">
                                <i class="fa-solid fa-user"></i>
                            </div>

                        </div>
                    </div>`;
                });

                shiftList.html(html);
            }

            // Tukar List
            function renderTukars(tukars, start, end) {

                let html = `<h6 class="mb-3 text-center">Daftar Pengajuan</h6>`;

                if (!tukars || tukars.length === 0) {
                    html += `<p class="text-center text-muted">Tidak ada pengajuan.</p>`;
                    return tukarList.html(html);
                }

                // 🔥 sorting by tanggal
                tukars.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));

                // console.log(tukars);



                tukars.forEach(s => {

                    // let approved = s.status_approved == 1 ? '-history' : '';

                    html += `
                        <button class="btn coworker-card${s.approval1}${s.approval2} d-flex align-items-center mb-3 w-100"
                            data-id_mapping="${s.id_mapping}"                        
                            data-id_pengaju="${s.id_pengaju}"                        
                            data-sign_pengaju="${s.sign_pengaju}"                   
                            id="btn_tukar_shift_offcanvas">

                                <div class="icon-box d-flex align-items-center justify-content-center" style="flex-shrink: 0;">
                                <i class="fa-solid fa-user"></i>
                            </div>

                            <div class="flex-grow-1 ms-3 text-start">
                                <h6 class="mb-0 fw-semibold text-truncate" style="line-height: 1.2;">${s.alasan}</h6>
                                <div class="text-muted small mt-1">
                                    <span>No. Form</span>
                                    <span class="mx-1">•</span>
                                    <span>${s.no_form}</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <span>Dibuat</span>
                                    <span class="mx-1">•</span>
                                    <span>${s.created_at}</span>
                                </div>
                            </div>
                        </button>`;

                    $(document).off('click', '#btn_tukar_shift_offcanvas');
                    $(document).on('click', '#btn_tukar_shift_offcanvas', function() {
                        let id_mapping = $(this).data('id_mapping');
                        let id_pengaju = $(this).data('id_pengaju');
                        let sign_pengaju = $(this).data('sign_pengaju');

                        loadGetDetailTukar(id_mapping)

                        $('#id_mapping').val(id_mapping);
                        $('#id_pengaju').val(id_pengaju);
                        $('#sign_pengaju').val(sign_pengaju);
                        // console.log(id_pengaju);
                        const offcanvasTukar = $('#off-canvas-tukar_shift_form');
                        const offcanvasShow = new bootstrap.Offcanvas(offcanvasTukar);
                        offcanvasShow.show();
                    });

                    // console.log(s.tukar_detail);

                });

                tukarList.html(html);
            }

            // ===============================
            // HELPER
            // ===============================
            function showSkeleton(selector) {
                $(selector).html(`<span class="skeleton skeleton-number" style="width:100%"></span>`);
            }

            function hideSkeleton(selector, value) {
                $(selector)
                    .html(value);
            }

            function formatISO(date) {
                return new Date(date).toISOString().split('T')[0];
            }

            function formatLabel(date) {
                return new Date(date).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            }

            function loadGetDetailTukar(id_mapping) {
                // console.log(id_mapping);

                let id_map = id_mapping;
                // console.log(id_map);


                $.ajax({
                    url: "{{ url('tukar_shift/get_detail_tukar') }}",
                    method: "GET",
                    data: {
                        id_map: id_map,
                    },
                    dataType: "json",
                    beforeSend: function() {
                        showSkeleton('#detail_tukar_shift');
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            // console.log(response.data_utama);
                            $('#detail_tukar_shift').empty();

                            let html = '';


                            response.data.forEach(s => {
                                html +=
                                    `<div class="card border-0 shadow-sm mb-3 overflow-hidden" style="border-radius: 15px;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                
                                                <div class="text-start flex-grow-1">
                                                    <div class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3">Pengaju</div>
                                                    <h6 class="mb-1 fw-bold text-dark">${s.karyawan_penukar_name}</h6>
                                                    <div class="text-muted small">
                                                        <i class="fa-solid fa-calendar-day me-1"></i> ${s.mapping_shift_penukar.tanggal_masuk}
                                                    </div>
                                                    <div class="badge bg-light text-secondary border mt-1 fw-medium">
                                                        ${s.nama_shift_penukar}
                                                    </div>
                                                </div>

                                                <div class="mx-3 text-center">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fa-solid fa-right-left text-primary"></i>
                                                    </div>
                                                </div>

                                                <div class="text-end flex-grow-1">
                                                    <div class="badge bg-info-subtle text-info mb-2 rounded-pill px-3">Ditukar Dengan</div>
                                                    <h6 class="mb-1 fw-bold text-dark">${s.karyawan_tertukar_name}</h6>
                                                    <div class="text-muted small">
                                                        ${s.mapping_shift_tertukar.tanggal_masuk} <i class="fa-solid fa-calendar-day ms-1"></i>
                                                    </div>
                                                    <div class="badge bg-light text-secondary border mt-1 fw-medium">
                                                        ${s.nama_shift_tertukar}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>`
                            });
                            // $('#detail_tukar_shift').html(html);
                            hideSkeleton('#detail_tukar_shift', html)
                            $('#ttd_tukar_shift').empty();
                            $('#ttd_tukar_shift').append(
                                `<div class="mb-3">
                                    <small class="text-muted" style="font-size:10px;">
                                        Tanda tangan pengaju
                                    </small>

                                    <div class="border rounded-3 p-2">
                                        <img src="/storage/signatures/${response.data_utama.sign_pengaju}" 
                                            style="width:100%; height:200px; object-fit:contain;" />
                                    </div>
                                </div>
                                `);
                            $('#ttd_atasan').empty();
                            $('#div_hapus').empty();
                            if (response.data_utama.approval1 == null) {
                                let user_id = "{{ $user_karyawan->id }}";
                                let pengaju_id = response.data_utama.id_pengaju;

                                // console.log(user_id, pengaju_id);
                                $('#ttd_atasan').append(
                                    `<div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">ATASAN LANGSUNG</small>

                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        </div>
                                        <small class="d-block text-muted" style="font-size: 8px;">
                                            ${response.data_utama.nama_approval1}</small>
                                    </div>`);
                                if (user_id == pengaju_id) {
                                    console.log('oke');
                                    $('#div_hapus').append(
                                        `
                                            <a href="javascript:void(0);" id="btn_tukar_konfirmasi" class="btn btn-danger btn-sm"
                                    data-bs-dismiss="offcanvas" aria-label="Close">
                                    <i class="fa-regular fa-trash-can"></i>&nbsp;Hapus
                                </a>`);
                                } else {
                                    console.log('asoy');

                                    $('#div_hapus').append('');
                                }
                            } else {
                                $('#ttd_atasan').append(
                                    `<div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">ATASAN LANGSUNG</small>

                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <img src="/storage/signatures/${response.data_utama.sign_approval1}" 
                                                style="width:100%; height:100%; object-fit:contain;" />
                                        </div>
                                        <small class="d-block text-muted" style="font-size: 8px;">
                                            ${response.data_utama.nama_approval1}</small>
                                    </div>`);

                            }
                            $('#ttd_bawahan').empty();
                            $('#ttd_bawahan').append(
                                `
                                    <div
                                        class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                            id="atasan1">HRD</small>

                                        <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                        </div>
                                        <small class="d-block text-muted" style="font-size: 8px;">
                                            ${response.data_utama.nama_approval2 ?? '-'}</small>
                                    </div>`);




                        } else {
                            $('#alert_wrapper').empty();
                            $('#alert_wrapper').append(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Info!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            );
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 50000);
                            Swal.fire({
                                title: 'Oops...',
                                text: response.message,
                                icon: 'error',
                                iconColor: '#b54a4a',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },
                                timer: 6000,
                                timerProgressBar: true
                            });
                            karyawan =
                                '<div class="card col-12 mb-3 bg-danger"><span class="text-center text-white">' +
                                response.message + '</span></div>';
                            $('#btnAddKaryawan').attr('hidden', true);
                            hideSkeleton('#detail_tukar_shift', karyawan);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();
                        Swal.fire({
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })
            }
            // ===============================
            // EVENT (TIDAK DIUBAH)
            // ===============================

            // $(document).on('click', '.btnTukar', function() {
            //     console.log('asoy');
            //     const startDate = $('#start_date').val();
            //     const menu_active = '{{ $menu_active }}';

            //     window.location.href =
            //         "{{ url('mapping_shift/tambah_mapping') }}/" + menu_active +
            //         "?start_date=" + startDate;
            // });

            $(document).on('click', "#btnTukar", function() {
                let start = $(this).attr('data-start'); // 🔥 lebih aman
                let end = $(this).attr('data-end'); // 🔥 lebih aman

                loadGetPApproval(start, end)

                const offcanvas = new bootstrap.Offcanvas('#off-canvas-tukar_shift');
                offcanvas.show();

                Swal.close();
            });

            // offcanvas

            function loadGetPApproval(start, end) {
                $.ajax({
                    url: "{{ url('tukar_shift/get_TukarShiftData_canvas') }}",
                    method: "GET",
                    data: {
                        start: start,
                        end: end,
                    },
                    dataType: "json",
                    beforeSend: function() {
                        showSkeleton('#list_karyawan');
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            let html = '';
                            let option = '';
                            const shiftList2 = $("#shiftList2");
                            let shifts = response.data;
                            // console.log(shifts);
                            if (!shifts || shifts.length === 0) {
                                html += `<p class="text-center text-muted">Tidak ada jadwal shift.</p>`;
                                return shiftList2.html(html);
                            }

                            // 🔥 sorting by tanggal
                            shifts.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));



                            shifts.forEach(s => {

                                let approved = s.status_approved == 1 ? '-history' : '';
                                let penukaran = s.tukar;

                                // console.log(penukaran);

                                html += `
                                    <div class="shift-card-wrapper mb-1">
                                        <input type="checkbox" id="shift-${s.id_mapping}" name="shift[]" value="${s.id_mapping ?? ''}" class="btn-check-input" hidden>
                                      
                                        <label for="shift-${s.id_mapping}" class="user-checkbox-card d-flex align-items-center mb-1">

                                            <div class="avatar-wrapper">
                                                <i class="fa-solid fa-dot"></i>
                                            </div>

                                            <div class="flex-grow-1 ms-3 text-start">
                                                <h6 class="mb-1 fw-semibold">${s.tanggal_alf} • Shift ${s.shift} </h6>

                                                <div class="text-muted small mb-2">
                                                    ${s.tanggal_masuk} 
                                                    <span class="mx-1">•</span>
                                                    ${s.jam_shift}
                                                </div>
                                                <div class="basic-form style-1">
                                                    <select id="pilihan_tukar-${s.id_mapping}" name="pilihan_tukar[]" class="form-select shift-select" onclick="event.stopPropagation();">
                                                        <option value="">Pilih</option>
                                                        ${penukaran && penukaran.length > 0?
                                                            penukaran.map(
                                                                t => `<option value="${t.id}" >${t.nama_karyawan} | Shift ${t.shift.nama_shift} | ${t.shift.jam_masuk} - ${t.shift.jam_keluar} </option>`
                                                            ).join('') : '<option disabled>Tidak ada data</option>'
                                                        }
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="icon-box">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            

                                        </label>
                                    </div>`;
                            });

                            // TAMPILKAN HASILNYA KE ID list_karyawan
                            $('#list_karyawan').html(html);

                            // Pastikan tombol simpan muncul jika ada data
                            $('#btnAddKaryawan').removeAttr('hidden');
                        } else {
                            $('#alert_wrapper').empty();
                            $('#alert_wrapper').append(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Info!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            );
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 50000);
                            Swal.fire({
                                title: 'Oops...',
                                text: response.message,
                                icon: 'error',
                                iconColor: '#b54a4a',
                                showConfirmButton: false,
                                confirmButtonText: 'Oke, Mengerti',
                                buttonsStyling: false,
                                customClass: {
                                    popup: 'premium-swal',
                                    title: 'premium-title',
                                    htmlContainer: 'premium-text',
                                    confirmButton: 'premium-confirm'
                                },
                                timer: 6000,
                                timerProgressBar: true
                            });
                            karyawan =
                                '<div class="card col-12 mb-3 bg-danger"><span class="text-center text-white">' +
                                response.message + '</span></div>';
                            $('#btnAddKaryawan').attr('hidden', true);
                            hideSkeleton('#list_karyawan', karyawan);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();
                        Swal.fire({
                            title: 'Oops...',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })
            }

            $('#btn_update_shift').on('click', function() {
                let formData = new FormData($('#form_add_karyawan')[0]); // ✅ FIX

                const canvas = document.getElementById('signature-pad');
                const signature = canvas.toDataURL('image/png');

                function isCanvasEmpty(canvas) {
                    const blank = document.createElement('canvas');
                    blank.width = canvas.width;
                    blank.height = canvas.height;
                    return canvas.toDataURL() === blank.toDataURL();
                }
                console.log(canvas, isCanvasEmpty(canvas));
                if (isCanvasEmpty(canvas)) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Tanda tangan wajib diisi!'
                    });
                    return;
                }

                formData.append('signature', signature);
                formData.append('start', globalStart);
                formData.append('end', globalEnd);
                // formData.append('tukars', globalTukars);
                // console.log(formData);
                $.ajax({
                    url: "{{ url('tukar_shift/ubah_shift') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(response) {

                        if (response.code == 200) {
                            // console.log('success');
                            $('#alert_wrapper').stop(true, true).hide().html(
                                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>' +
                                '<strong>Success!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 6000); // 3 detik
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_shift');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();

                            // console.log(response);
                            $('#tukarList').empty();

                            let tukars = flattenData(response.grouped).flat();
                            let start = globalStart
                            let end = globalEnd;
                            renderTukars(tukars, start, end);

                            $('#shiftList').empty();
                            let alert = response.alert;
                            console.log('alert = ', alert);
                            renderShifts(globalShift, alert, start, end);

                        } else {

                            let errorMsg = '';

                            // ambil error alasan (array)
                            if (response.errors && response.errors.alasan) {
                                errorMsg = response.errors.alasan[0];
                            } else {
                                errorMsg = response.message;
                            }
                            $('#alert_wrapper').empty();
                            $('#alert_wrapper').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Gagal!</strong>&nbsp;' + errorMsg +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 60000);
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_shift');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();
                            Swal.close();

                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();

                        let message = 'Terjadi kesalahan sistem';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        } else if (error) {
                            message = error;
                        }

                        Swal.fire({
                            title: 'Eror,...',
                            text: message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })

            });
            $(document).off('click', '#btn_tukar_konfirmasi');
            $(document).on('click', '#btn_tukar_konfirmasi', function() {
                let id_mapping = $('#id_mapping').val();
                let id_pengaju = $('#id_pengaju').val();
                let sign_pengaju = $('#sign_pengaju').val();
                $('#id_mapping_delete').val(id_mapping);
                $('#id_pengaju_delete').val(id_pengaju);
                $('#sign_pengaju_delete').val(sign_pengaju);
                // console.log(id_mapping);

                const offcanvasTukarKonfirmasi = $('#off-canvas-tukar_konfirmasi');
                const offcanvasShowKonfirmasi = new bootstrap.Offcanvas(
                    offcanvasTukarKonfirmasi);
                offcanvasShowKonfirmasi.show();
            });
            $(document).on('click', '#btn_tukar_delete', function() {

                // console.log('mmm');
                let formData = new FormData($('#form_hapus_tukar')[0]);

                // const canvas = document.getElementById('signature-pad');
                // const signature = canvas.toDataURL('image/png');

                // function isCanvasEmpty(canvas) {
                //     const blank = document.createElement('canvas');
                //     blank.width = canvas.width;
                //     blank.height = canvas.height;
                //     return canvas.toDataURL() === blank.toDataURL();
                // }

                // if (isCanvasEmpty(canvas)) {
                //     Swal.fire({
                //         icon: 'warning',
                //         text: 'Tanda tangan wajib diisi!'
                //     });
                //     return;
                // }

                // formData.append('signature', signature);
                formData.append('start', globalStart);
                formData.append('end', globalEnd);

                $.ajax({
                    url: "{{ url('tukar_shift/hapus_ubah_shift') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib

                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(response) {

                        if (response.code == 200) {
                            // console.log('mmm');
                            // console.log('success');
                            $('#alert_wrapper').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>' +
                                '<strong>Success!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('#alert_wrapper').fadeOut('slow');
                            }, 6000); // 3 detik
                            const offcanvasEl = document.getElementById(
                                'off-canvas-tukar_konfirmasi');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            Swal.close();

                            $('#tukarList').empty();

                            console.log(response);
                            let tukars = flattenData(response.grouped).flat();
                            // console.log(tukars);
                            let start = globalStart
                            let end = globalEnd;
                            renderTukars(tukars, start, end);

                            $('#shiftList').empty();
                            let alert = response.alert;
                            console.log('alert = ', alert);
                            renderShifts(globalShift, alert, start, end);

                        } else {
                            $('#alert_add_karyawan').empty();
                            $('#alert_add_karyawan').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Info!</strong>&nbsp;' + response.message +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('#alert_add_karyawan').fadeOut('slow');
                            }, 60000);
                            Swal.close();

                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();

                        let message = 'Terjadi kesalahan sistem';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        } else if (error) {
                            message = error;
                        }

                        Swal.fire({
                            title: 'Oops...',
                            text: message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })
            });

        });
    </script>
@endsection
