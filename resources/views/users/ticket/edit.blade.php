@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - Edit Ticket
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
<style>
    .futuristic-header {
        padding: 28px 20px 18px;
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

    /* =====================
       BACK BUTTON
    ===================== */
    .header-back-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f3e8ff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        font-size: 14px;
        text-decoration: none;
        transition: background .2s;
        z-index: 10;
    }

    .header-back-btn:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    .form-input {
        position: relative;
    }

    /* Offcanvas Kedua (Konfirmasi) */
    #off-canvas-konfirmasi-save {
        z-index: 1095 !important;
    }

    /* Backdrop untuk Offcanvas Kedua */
    /* Ini sedikit trik karena Bootstrap pakai satu class backdrop */
    #off-canvas-ticket {
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
        /* Sesuaikan top agar sejajar lurus dengan teks "SALDO ticket" */
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

    textarea {
        height: 100px;
        /* tentukan tinggi */
        padding-top: 35px;
        /* setengah tinggi (approx) */
        padding-bottom: 0;
        text-align: left;
        /* tetap rata kiri */
        box-sizing: border-box;
    }

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
        text-align: left;
    }

    /* KONDISI SWITCH */
    .kondisi-switch-section {
        display: none;
        background: linear-gradient(135deg, #faf7ff, #ffffff);
        border: 1px solid #e9d5ff;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
    }

    .kondisi-switch-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #ede9fe;
        transition: background .2s;
    }

    .kondisi-switch-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .kondisi-switch-item:first-child {
        padding-top: 0;
    }

    .kondisi-label {
        font-size: 12px;
        font-weight: 600;
        color: #4c1d95;
        flex-grow: 1;
        padding-right: 12px;
        line-height: 1.3;
    }

    .toggle-switch {
        position: relative;
        width: 46px;
        height: 26px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #e2e8f0;
        border-radius: 50px;
        transition: background .3s ease;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, .1);
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform .3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .15);
    }

    .toggle-switch input:checked+.toggle-slider {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
    }

    .toggle-switch input:checked+.toggle-slider::before {
        transform: translateX(20px);
    }

    .kondisi-loading {
        text-align: center;
        padding: 12px 0;
        color: #a78bfa;
        font-size: 12px;
    }

    .kondisi-empty {
        text-align: center;
        padding: 10px 0;
        font-size: 12px;
        color: #a0aec0;
    }
</style>
@endsection
@section('content_top')
<div class="card" style="position: relative;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <h5 class="mt-3 mb-2" style="color: #623acf; font-size: 24px;">Nomor: {{ $ticket->nomor_ticket}}</h5>
        <h5 class="mt-3 mb-1">EDIT UBAH/HAPUS</h5>
        <small class="text-muted">Edit Form Pengajuan Ubah/Hapus Database</small>
    </div>
    <div id="alert_pengajuan_ticket"></div>

    <div class="card-body px-3 px-md-4">
        <!-- {{-- PARENT TICKET LINEAGE --}} -->
        @include('components.parent-ticket-card')

        <form class="my-2" method="post" id="form_pengajuan_ticket" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="id_ticket" value="{{ $ticket->id }}">
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <!-- Form input -->
            <div class="col-12">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">1</span>
                        <h6 class="mb-0 fw-bold">Pengajuan Ubah/Hapus</h6>
                    </div>

                    <div class="form-input ticket">
                        <span class="input-icon">
                            <i class="fa-solid fa-tags"></i>
                        </span>
                        <select name="ticket" id="ticket">
                            <option value="">Pilihan Environment</option>
                            <option value="1" {{ $ticket->ticketMenu?->kategori == 1 ? 'selected' : '' }}>Epicor ERP</option>
                            <option value="2" {{ $ticket->ticketMenu?->kategori == 2 ? 'selected' : '' }}>Accurate Online SP</option>
                            <option value="3" {{ $ticket->ticketMenu?->kategori == 3 ? 'selected' : '' }}>Accurate Online AJA</option>
                        </select>
                    </div>
                    <div class="alert_ticket"></div>
                    <input type="hidden" name="nomor_ticket" class="form-control" id="nomor_ticket_hidden">

                    <input type="hidden" name="tanggal_ticket" id="tanggal_ticket_hidden">

                    <div class="alert_nomor_ticket"></div>

                    <div class="form-input menu_ticket">
                        <span class="input-icon">
                            <i class="fa-solid fa-folder-open"></i>
                        </span>
                        <div class="select-loader d-none">
                            <div class="dot-loader">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                        <select name="menu_ticket" id="menu_ticket">
                            <option value="">-- Pilih Modul --</option>
                        </select>
                    </div>
                    <div class="alert_menu_ticket"></div>

                    <div class="form-input submenu_ticket">
                        <span class="input-icon">
                            <i class="fa-solid fa-list-ul"></i>
                        </span>
                        <div class="select-loader d-none">
                            <div class="dot-loader">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                        <select name="submenu_ticket" id="submenu_ticket">
                            <option value="">-- Pilih Jenis Perubahan --</option>
                        </select>
                    </div>
                    <div class="alert_submenu_ticket"></div>

                    <div class="kondisi-switch-section" id="kondisi-switch-section">
                        <div class="d-flex align-items-center mb-2 gap-2">
                            <i class="fa-solid fa-sliders" style="color:#7c3aed; font-size:13px;"></i>
                            <span style="font-size:12px; font-weight:700; color:#4c1d95; text-transform:uppercase; letter-spacing:.5px;">Kondisi Ticket</span>
                            <span id="kondisi-count-badge" class="badge ms-auto"
                                style="background:#ede9fe; color:#6d28d9; font-size:10px; border-radius:50px; padding:3px 10px;">0 aktif</span>
                        </div>
                        <div id="kondisi-list"></div>
                    </div>
                    <div id="kondisi-hidden-inputs"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px; color: #6b21a8;">
                            <i class="fa-solid fa-table-list me-1"></i> Detail Perubahan Data
                        </label>

                        <div id="change-card-list" class="d-flex flex-column gap-2 mb-2">
                            <div id="change-empty-state" class="text-center text-muted py-3 rounded-3 border border-dashed" style="font-size: 12px; border-color: #d8b4fe !important;">
                                <i class="fa-solid fa-table fa-lg mb-1 d-block" style="color: #d8b4fe;"></i>
                                Belum ada detail — klik Tambah Detail Data
                            </div>
                        </div>

                        <button type="button" id="btn-add-change-card"
                            class="btn btn-sm"
                            style="border: 1px dashed #a855f7; color: #7c3aed; background: transparent; border-radius: 8px; font-size: 12px;">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Detail Data
                        </button>
                    </div>

                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">2</span>
                        <h6 class="mb-0 fw-bold">Upload Lampiran</h6>
                    </div>

                    <div class="mb-3">
                        <label for="lampiran"
                            class="form-label fw-semibold"
                            style="font-size: 12px; color: #6b21a8;">
                            <i class="fa-solid fa-paperclip me-1"></i>
                            Lampiran (Opsional)
                        </label>
                        <input class="form-control form-control-sm"
                            type="file"
                            id="lampiran"
                            name="lampiran"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.jpg,.jpeg,.png,.gif,.webp,.bmp,.zip,.rar,.7z,image/*">

                        <div id="lampiran-preview"
                            class="mt-2 d-flex flex-wrap gap-2">
                        </div>
                    </div>

                    @php
                        $attachmentsList = collect();
                        if ($ticket->attachments && $ticket->attachments->isNotEmpty()) {
                            $attachmentsList = $ticket->attachments;
                        } elseif ($ticket->lampiran) {
                            $attachmentsList = collect([(object)[
                                'original_name' => basename($ticket->lampiran),
                                'file_path'     => $ticket->lampiran,
                                'file_size'     => null,
                            ]]);
                        }
                    @endphp
                    @if ($attachmentsList->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach ($attachmentsList as $att)
                        @php
                            $filePath = is_array($att) ? ($att['file_path'] ?? '') : ($att->file_path ?? '');
                            $origName = is_array($att) ? ($att['original_name'] ?? basename($filePath)) : ($att->original_name ?? basename($filePath));
                            $fileUrl  = asset('storage/' . $filePath);
                            $ext      = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $isImg    = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                            $isPdf    = $ext === 'pdf';
                            $isExcel  = in_array($ext, ['xls','xlsx','csv']);
                            $isDoc    = in_array($ext, ['doc','docx']);
                            $isZip    = in_array($ext, ['zip','rar','7z']);
                        @endphp
                        <div class="border rounded p-2 bg-light" style="width:140px;">
                            @if ($isImg)
                            <div style="height:90px; background:#fff; overflow:hidden; display:flex; align-items:center; justify-content:center;" class="rounded mb-2">
                                <img src="{{ $fileUrl }}" alt="{{ $origName }}" style="width:100%; height:90px; object-fit:cover;">
                            </div>
                            @else
                            <div class="text-center py-3 d-flex align-items-center justify-content-center mb-2" style="height:90px; background:#fff; border-radius:6px;">
                                @if($isPdf)
                                    <i class="fa-solid fa-file-pdf text-danger" style="font-size:36px;"></i>
                                @elseif($isExcel)
                                    <i class="fa-solid fa-file-excel text-success" style="font-size:36px;"></i>
                                @elseif($isDoc)
                                    <i class="fa-solid fa-file-word text-primary" style="font-size:36px;"></i>
                                @elseif($isZip)
                                    <i class="fa-solid fa-file-zipper text-warning" style="font-size:36px;"></i>
                                @else
                                    <i class="fa-solid fa-file text-secondary" style="font-size:36px;"></i>
                                @endif
                            </div>
                            @endif
                            <div class="small text-truncate mb-2" title="{{ $origName }}">
                                {{ $origName }}
                            </div>
                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-primary w-100" style="font-size:11px;">
                                <i class="fa-solid fa-eye me-1"></i> Lihat
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">3</span>
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

                    <div class="row g-2 d-flex align-items-stretch mb-3">
                        <!-- {{-- DIAJUKAN OLEH --}} -->
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIAJUKAN OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    @if($ticket->requestor_signature)
                                    <img src="/storage/{{ $ticket->requestor_signature }}"
                                        style="max-height:45px; max-width:100%; object-fit:contain;">
                                    @else
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                    @endif
                                </div>
                                <div>
                                    <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                        {{ $ticket->requestor_name ?? '-' }}
                                    </small>
                                    @if($ticket->created_at)
                                    <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                        <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- DISETUJUI OLEH-1 -->
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-1:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    @if($ticket->approve1_signature)
                                    <img src="/storage/{{ $ticket->approve1_signature }}"
                                        style="max-height:45px; max-width:100%; object-fit:contain;">
                                    @else
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                    @endif
                                </div>
                                <div>
                                    <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;" id="preview_atasan1">
                                        {{ $ticket->approve1_name ?? '' }}
                                        @if(!$ticket->approve1_name)
                                        <span class="skeleton" style="height:12px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                        @endif
                                    </small>
                                    @if($ticket->approve1_at)
                                    <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                        <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve1_at)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- DISETUJUI OLEH-2 -->
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-2:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    @if($ticket->approve2_signature)
                                    <img src="/storage/{{ $ticket->approve2_signature }}"
                                        style="max-height:45px; max-width:100%; object-fit:contain;">
                                    @else
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                    @endif
                                </div>
                                <div>
                                    <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;" id="preview_atasan2">
                                        {{ $ticket->approve2_name ?? '' }}
                                        @if(!$ticket->approve2_name)
                                        <span class="skeleton" style="height:12px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                        @endif
                                    </small>
                                    @if($ticket->approve2_at)
                                    <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                        <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve2_at)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- DIPERIKSA OLEH -->
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIPERIKSA OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    @if($ticket->checker_signature)
                                    <img src="/storage/{{ $ticket->checker_signature }}"
                                        style="max-height:45px; max-width:100%; object-fit:contain;">
                                    @else
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                    @endif
                                </div>
                                <div>
                                    <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;" id="preview_checker">
                                        {{ $ticket->checker_name ?? 'JM FINANCE & ACCOUNTING' }}
                                    </small>
                                    @if($ticket->checked_at)
                                    <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                        <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->checked_at)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- {{-- DIVALIDASI OLEH --}} -->
                        <div class="col-4">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIVALIDASI OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    @if($ticket->validator_signature)
                                    <img src="/storage/{{ $ticket->validator_signature }}"
                                        style="max-height:45px; max-width:100%; object-fit:contain;">
                                    @else
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                    @endif
                                </div>
                                <div>
                                    <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                        {{ $ticket->validator_name ?? 'JM FINANCE & ACCOUNTING' }}
                                    </small>
                                    @if($ticket->validator_at)
                                    <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                        <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->validator_at)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ===== RIWAYAT APPROVAL ===== --}}
                @if($ticket->comments && $ticket->comments->count() > 0)
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">4</span>
                        <h6 class="mb-0 fw-bold">Catatan & Riwayat Approval</h6>
                    </div>

                    @foreach($ticket->comments as $comment)
                    @php
                    $levelMap = [
                    'approve1' => ['label' => 'Approver 1', 'icon' => 'fa-user-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
                    'approve2' => ['label' => 'Approver 2', 'icon' => 'fa-user-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
                    'checker' => ['label' => 'Checker', 'icon' => 'fa-magnifying-glass', 'color' => '#0369a1', 'bg' => '#f0f9ff', 'border' => '#bae6fd'],
                    'validator' => ['label' => 'Validator', 'icon' => 'fa-shield', 'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
                    'pic' => ['label' => 'PIC', 'icon' => 'fa-user-tie', 'color' => '#b45309', 'bg' => '#fffbeb', 'border' => '#fde68a'],
                    'requestor' => ['label' => 'Requestor', 'icon' => 'fa-user', 'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'],
                    'assign' => ['label' => 'Assign', 'icon' => 'fa-user-plus', 'color' => '#1d4ed8', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
                    ];
                    $actionMap = [
                    'approve' => ['label' => 'Disetujui', 'icon' => 'fa-circle-check', 'color' => '#166534'],
                    'reject' => ['label' => 'Ditolak', 'icon' => 'fa-circle-xmark', 'color' => '#9f1239'],
                    'start' => ['label' => 'Mulai Kerjakan', 'icon' => 'fa-play', 'color' => '#1d4ed8'],
                    'finish' => ['label' => 'Selesai', 'icon' => 'fa-flag-checkered', 'color' => '#166534'],
                    'cancel' => ['label' => 'Dibatalkan', 'icon' => 'fa-ban', 'color' => '#9f1239'],
                    'feedback' => ['label' => 'Feedback', 'icon' => 'fa-comment-dots', 'color' => '#1e40af'],
                    'assign' => ['label' => 'Ditugaskan', 'icon' => 'fa-user-plus', 'color' => '#1d4ed8'],
                    ];

                    $lv = $levelMap[$comment->approval_level] ?? ['label' => $comment->approval_level, 'icon' => 'fa-comment', 'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'];
                    $ac = $actionMap[$comment->action_ticket] ?? ['label' => $comment->action_ticket, 'icon' => 'fa-circle', 'color' => '#6b7280'];
                    @endphp

                    <div style="background:{{ $lv['bg'] }}; border:1.5px solid {{ $lv['border'] }}; border-radius:14px; padding:12px 14px; margin-bottom:8px;">

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:28px; height:28px; border-radius:50%; background:{{ $lv['color'] }}; color:#fff;
                     display:inline-flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0;">
                                    <i class="fa-solid {{ $lv['icon'] }}"></i>
                                </span>
                                <div>
                                    <div style="font-size:11px; font-weight:700; color:{{ $lv['color'] }};">
                                        {{ $lv['label'] }}
                                    </div>
                                    <div style="font-size:10px; color:#6b7280;">
                                        {{ $comment->karyawan?->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span style="font-size:10px; font-weight:700; color:{{ $ac['color'] }};">
                                    <i class="fa-solid {{ $ac['icon'] }} me-1"></i>{{ $ac['label'] }}
                                </span>
                                <div style="font-size:9px; color:#9ca3af;">
                                    {{ $comment->comment_at ? \Carbon\Carbon::parse($comment->comment_at)->translatedFormat('d M Y, H:i') : '-' }}
                                </div>
                            </div>
                        </div>

                        @if($comment->comment_text)
                        <div style="background:#fff; border:1px solid {{ $lv['border'] }}; border-radius:8px;
                            padding:8px 12px; font-size:12px; color:#374151; line-height:1.6;">
                            {{ $comment->comment_text }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="alert_error_ticket col-12"></div>
                <button type="button" id="btn_save_konfirmasi_ticket"
                    class="btn btn-primary btn-sm w-100 py-3 rounded-pill fw-bold mb-2 shadow">
                    AJUKAN PENGAJUAN UBAH/HAPUS
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-konfirmasi-save"
    style="height: auto; min-height: 190px; z-index: 1095 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                <i class="fa-solid fa-circle-check fa-bounce"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Konfirmasi</h6>
                <small class="text-muted">Pengajuan Form Ubah Hapus...</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-3 pb-5">
        <div class="mt-2 text-center">
            <button type="button" id="btn_simpan_ticket" class="btn light btn-success btn-sm me-2 px-4 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-save me-2"></i>Simpan
            </button>
            <button type="button" class="btn light btn-danger btn-sm px-4 py-2 rounded-pill" data-bs-dismiss="offcanvas">
                Batal&nbsp;
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-change-detail" style="height: 70vh; z-index: 1090 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 18px;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Detail Perubahan</h6>
                <small class="text-muted" id="change-detail-subtitle">Data #1</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">
        <input type="hidden" id="change-detail-index">
        
        <div class="form-input mb-3" style="position: relative;">
            <span class="input-icon" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#6f42c1; font-size:14px; z-index:6;">
                <i class="fa-solid fa-hashtag"></i>
            </span>
            <input type="text" id="cd-nomor" class="form-control" placeholder="Nomor">
        </div>
        
        <div class="form-input mb-3" style="position: relative;">
            <span class="input-icon" style="position:absolute; left:14px; top:18px; color:#6f42c1; font-size:14px; z-index:6;">
                <i class="fa-solid fa-circle-info"></i>
            </span>
            <textarea id="cd-alasan" class="form-control" rows="3" placeholder="Alasan Perubahan" style="padding-left:42px; padding-top:12px; height:80px;"></textarea>
        </div>
        
        <div class="form-input mb-3" style="position: relative;">
            <span class="input-icon" style="position:absolute; left:14px; top:18px; color:#6f42c1; font-size:14px; z-index:6;">
                <i class="fa-solid fa-arrow-left"></i>
            </span>
            <textarea id="cd-sebelum" class="form-control" rows="3" placeholder="Sebelum Perubahan" style="padding-left:42px; padding-top:12px; height:80px;"></textarea>
        </div>
        
        <div class="form-input mb-3" style="position: relative;">
            <span class="input-icon" style="position:absolute; left:14px; top:18px; color:#6f42c1; font-size:14px; z-index:6;">
                <i class="fa-solid fa-arrow-right"></i>
            </span>
            <textarea id="cd-setelah" class="form-control" rows="3" placeholder="Setelah Perubahan" style="padding-left:42px; padding-top:12px; height:80px;"></textarea>
        </div>
        
        <div class="d-flex gap-2 mt-3">
            <button type="button" id="btn-save-change-detail" class="btn btn-primary btn-sm flex-grow-1 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-save me-1"></i> Simpan Detail
            </button>
            <button type="button" id="btn-delete-change-detail" class="btn btn-danger btn-sm flex-grow-1 light px-3 py-2 rounded-pill">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();"
                class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            Swal.close();
        }
    });

    $(document).ready(function() {
        let menu_active = '{{ $menu_id }}';
        const existingTicket = {
            id: '{{ $ticket->id }}',
            ticket: '{{ $kategori_ticket ?? "" }}',
            menu_id: '{{ $ticket->ticket_menu_id ?? "" }}',
            submenu_id: '{{ $ticket->ticket_sub_menu_id ?? "" }}',
            details: @json($ticket->ticketDetails ?? []),
            signature: '{{ $ticket->signature ?? "" }}'
        };

        const canvas = document.getElementById('signature-pad');
        canvas.removeAttribute('hidden');
        const signaturePad = new SignaturePad(canvas);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            const ctx = canvas.getContext('2d');
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(ratio, ratio);
            signaturePad.clear();
            if (existingTicket.signature) {
                signaturePad.fromDataURL(existingTicket.signature);
            }
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function showSkeleton(selector) {
            $(selector).html(`<span class="skeleton skeleton-number" style="width:100%"></span>`);
        }

        function hideSkeleton(selector, value) {
            $(selector).html(value);
        }

        function showSelectLoading(selectId) {
            $(selectId).prop('disabled', true)
                .closest('.form-input').find('.select-loader').removeClass('d-none');
        }

        function hideSelectLoading(selectId) {
            $(selectId).prop('disabled', false)
                .closest('.form-input').find('.select-loader').addClass('d-none');
        }

        $('#ticket').select2({
            dropdownParent: $('body'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Environment",
            allowClear: true
        });
        $('#menu_ticket').select2({
            dropdownParent: $('body'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Modul Ubah Hapus",
            allowClear: true
        });
        $('#submenu_ticket').select2({
            dropdownParent: $('body'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Jenis Perubahan",
            allowClear: true
        });

        $(document).on('change', '#ticket', function() {
            let id_ticket = $(this).val();
            $('#menu_ticket').html('<option value="">-- Pilih Modul --</option>').trigger('change');
            $('#submenu_ticket').html('<option value="">-- Pilih Jenis Perubahan --</option>').trigger('change');
            $('.alert_menu_ticket').empty();
            $('.alert_submenu_ticket').empty();
            if (!id_ticket) return;

            showSelectLoading('#menu_ticket');
            $.ajax({
                type: 'GET',
                url: "{{ url('ticket/get_menu_kategori') }}" + '/' + id_ticket,
                cache: false,
                success: function(res) {
                    hideSelectLoading('#menu_ticket');
                    let option = '<option value="">-- Pilih Modul --</option>';
                    if (res.code == 200 && res.menu.length > 0) {
                        res.menu.forEach(function(item) {
                            option += `<option value="${item.id}">${item.nama_menu}</option>`;
                        });
                    } else {
                        $('.alert_menu_ticket').html(
                            '<div class="alert alert-warning py-2 px-3 rounded-3" style="font-size:12px;">' +
                            '<i class="fa-solid fa-triangle-exclamation me-1"></i>Tidak ada modul tersedia.</div>'
                        );
                        setTimeout(() => $('.alert_menu_ticket').fadeOut(500, function() {
                            $(this).empty().show();
                        }), 4000);
                    }
                    $('#menu_ticket').html(option).trigger('change');
                    $(document).trigger('menu_loaded'); // ✅ Trigger setelah load
                },
                error: function(xhr) {
                    hideSelectLoading('#menu_ticket');
                    let msg = window.getFriendlyErrorMessage(xhr, 'Gagal memuat modul.');
                    $('.alert_menu_ticket').html(
                        `<div class="alert alert-danger py-2 px-3 rounded-3" style="font-size:12px;">${msg}</div>`
                    );
                    setTimeout(() => $('.alert_menu_ticket').fadeOut(500, function() {
                        $(this).empty().show();
                    }), 5000);
                }
            });
        });

        $(document).on('change', '#menu_ticket', function() {
            let menu_id = $(this).val();
            $('#submenu_ticket').html('<option value="">-- Pilih Jenis Perubahan --</option>').trigger('change');
            $('.alert_submenu_ticket').empty();
            if (!menu_id) return;

            loadKondisiByMenu(menu_id);

            showSelectLoading('#submenu_ticket');
            $.ajax({
                type: 'GET',
                url: "{{ url('ticket/submenu/get_submenu_ticket') }}" + '/' + menu_id,
                cache: false,
                success: function(res) {
                    hideSelectLoading('#submenu_ticket');
                    let option = '<option value="">-- Pilih Jenis Perubahan --</option>';
                    if (res.code == 200 && res.menu.length > 0) {
                        res.menu.forEach(function(item) {
                            option += `<option value="${item.id}">${item.nama_sub_menu}</option>`;
                        });
                    } else {
                        $('.alert_submenu_ticket').html(
                            '<div class="alert alert-warning py-2 px-3 rounded-3" style="font-size:12px;">' +
                            '<i class="fa-solid fa-triangle-exclamation me-1"></i>Tidak ada jenis perubahan tersedia.</div>'
                        );
                        setTimeout(() => $('.alert_submenu_ticket').fadeOut(500, function() {
                            $(this).empty().show();
                        }), 4000);
                    }
                    $('#submenu_ticket').html(option).trigger('change');
                    $(document).trigger('submenu_loaded');
                },
                error: function(xhr) {
                    hideSelectLoading('#submenu_ticket');
                    let msg = window.getFriendlyErrorMessage(xhr, 'Gagal memuat jenis perubahan.');
                    $('.alert_submenu_ticket').html(
                        `<div class="alert alert-danger py-2 px-3 rounded-3" style="font-size:12px;">${msg}</div>`
                    );
                    setTimeout(() => $('.alert_submenu_ticket').fadeOut(500, function() {
                        $(this).empty().show();
                    }), 5000);
                }
            });
        });

        function loadKondisiByMenu(menu_id) {
            if (!menu_id) {
                $('#kondisi-switch-section').hide();
                return;
            }
            $.ajax({
                type: 'GET',
                url: "{{ url('ticket/get_kondisi_menu') }}" + '/' + menu_id,
                cache: false,
                success: function(res) {
                    if (res.code == 200 && res.kondisi && res.kondisi.length > 0) {
                        $('#kondisi-switch-section').show();
                    } else {
                        $('#kondisi-switch-section').hide();
                    }
                },
                error: function() {
                    $('#kondisi-switch-section').hide();
                }
            });
        }

        let isInitialLoad = true;

        $(document).on('change', '#submenu_ticket', function() {
            let submenuId = $(this).val();
            let environment = $('#ticket').val();

            if (!submenuId || !environment) return;

            if (!isInitialLoad) {
                $('#preview_atasan1').html('<span class="skeleton" style="height:11px;width:60px;display:inline-block;"></span>');
                $('#preview_atasan2').html('<span class="skeleton" style="height:11px;width:60px;display:inline-block;"></span>');
                $('#preview_checker').html('<span class="skeleton" style="height:11px;width:60px;display:inline-block;"></span>');
            }

            $.get(
                '/approval/ticket/preview-flow/' + submenuId, {
                    environment: environment
                },
                function(res) {
                    isInitialLoad = false;

                    if (res.approvers?.length) {
                        const approve1 = res.approvers.find(x => x.role === 'approve1');
                        const approve2 = res.approvers.find(x => x.role === 'approve2');

                        if (approve1) {
                            const names1 = Array.isArray(approve1.karyawan_names) ?
                                approve1.karyawan_names.join('<br>') :
                                (approve1.karyawan_names ?? approve1.step_name ?? '-');
                            $('#preview_atasan1').html(names1);
                        } else {
                            $('#preview_atasan1').html('-');
                        }

                        if (approve2) {
                            const names2 = Array.isArray(approve2.karyawan_names) ?
                                approve2.karyawan_names.join('<br>') :
                                (approve2.karyawan_names ?? approve2.step_name ?? '-');
                            $('#preview_atasan2').html(names2);
                        } else {
                            $('#preview_atasan2').html('-');
                        }
                    } else {
                        $('#preview_atasan1').html('-');
                        $('#preview_atasan2').html('-');
                    }

                    if (res.checker) {
                        const checkerNames = Array.isArray(res.checker.karyawan_names) ?
                            res.checker.karyawan_names.join('<br>') :
                            (res.checker.karyawan_names ?? res.checker.step_name ?? '-');
                        $('#preview_checker').html(checkerNames);
                    } else {
                        $('#preview_checker').html('-');
                    }
                }
            ).fail(function() {
                isInitialLoad = false;
                $('#preview_atasan1').html('-');
                $('#preview_atasan2').html('-');
                $('#preview_checker').html('-');
            });
        });

        if (existingTicket.ticket) {
            $('#ticket').val(existingTicket.ticket).trigger('change');
        }
        $(document).on('menu_loaded', function() {
            if (existingTicket.menu_id) {
                $('#menu_ticket').val(existingTicket.menu_id).trigger('change');
            }
        });
        $(document).on('submenu_loaded', function() {
            if (existingTicket.submenu_id) {
                $('#submenu_ticket').val(existingTicket.submenu_id).trigger('change');
            }
        });

        let namaPengaju = '{{ $ticket->requestor_name }}';
        $('#namaPengaju').val(namaPengaju);
        $('#namaPengaju').text(namaPengaju ? `${namaPengaju}` : 'DIAJUKAN OLEH');

        let signatureData = '{{ $ticket->requestor_signature }}';

        if (signatureData && signatureData !== '') {
            $('#signaturePengaju').html(`
                <img src="/storage/${signatureData}"
                    style="max-height:60px; max-width:100%; object-fit:contain;">
            `);
        }

        $(document).on('click', '#btn_save_konfirmasi_ticket', function() {
            const offcanvasEl = document.getElementById('off-canvas-konfirmasi-save');
            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
            offcanvas.show();
        });

        $(document).on('click', '#btn_simpan_ticket', function() {

            let btn = $(this);
            let originalText = btn.html();

            let signature_value = '';
            if (!signaturePad.isEmpty()) {
                signature_value = signaturePad.toDataURL();
            }

            $('#signature').val(signature_value);
            let formData = new FormData($('#form_pengajuan_ticket')[0]);

            $.ajax({
                url: "{{ route('ticket.head.update', ['id' => ':id']) }}"
                    .replace(':id', existingTicket.id),

                type: "POST",
                data: formData,

                processData: false,
                contentType: false,

                beforeSend: function() {
                    btn.prop('disabled', true);

                    btn.html(`
                <span class="spinner-border spinner-border-sm me-2"></span>
                Loading...
            `);
                },

                error: function(data) {
                    btn.prop('disabled', false);
                    btn.html(originalText);

                    bootstrap.Offcanvas
                        .getInstance(document.getElementById('off-canvas-konfirmasi-save'))
                        ?.hide();

                    const errMsg = window.getFriendlyErrorMessage(data, 'Terjadi gangguan saat menyimpan perubahan.');
                    showAlert('#alert_pengajuan_ticket', 'danger', errMsg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errMsg
                    });
                },

                success: function(data) {
                    btn.prop('disabled', false);
                    btn.html(originalText);

                    bootstrap.Offcanvas
                        .getInstance(document.getElementById('off-canvas-konfirmasi-save'))
                        ?.hide();

                    if (data.code == 200) {
                        showAlert(
                            '#alert_pengajuan_ticket',
                            'success',
                            data.message
                        );

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Ticket berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('ticket', $menu_id) }}";
                        });

                    } else if (data.code == 422) {
                        const errors = data.errors || {};
                        const mainMessages = [];
                        $.each(errors, function(key, val) {
                            const topKey = key.split('.')[0];
                            const targetSelector = '#alert_' + topKey + ', .alert_' + topKey;
                            const msg = Array.isArray(val) ? val[0] : val;
                            if (['menu_ticket', 'submenu_ticket', 'signature'].indexOf(topKey) !== -1 && $(targetSelector).length) {
                                showAlert(targetSelector, 'danger', msg);
                            }
                            mainMessages.push(msg);
                        });
                        const valErrMsg = mainMessages.length
                            ? [...new Set(mainMessages)].join('<br>')
                            : (data.message ?? 'Data yang diisi belum lengkap, silakan periksa kembali.');
                        showAlert('#alert_pengajuan_ticket', 'danger', valErrMsg);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            html: valErrMsg
                        });
                    } else {
                        const failMsg = window.getFriendlyErrorMessage(data.message, 'Gagal memperbarui ticket.');
                        showAlert('#alert_pengajuan_ticket', 'danger', failMsg);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: failMsg
                        });
                    }
                }
            });
        });

        function showAlert(selector, type, message) {
            $(selector).stop(true, true).hide()
                .html(`<div class="alert alert-${type} light alert-dismissible fade show">${message}</div>`)
                .fadeIn(100);
            setTimeout(function() {
                $(selector).animate({
                    opacity: 0,
                    marginTop: '-10px'
                }, 1000, function() {
                    $(this).slideUp('slow', function() {
                        $(this).html('').css({
                            opacity: '',
                            marginTop: ''
                        });
                    });
                });
            }, 6000);
        }

        $('#clear').on('click', () => signaturePad.clear());

        let changeCards = [];
        let activeChangeIndex = null;
        const changeOffcanvasEl = document.getElementById('off-canvas-change-detail');
        const changeOffcanvas = new bootstrap.Offcanvas(changeOffcanvasEl);

        if (existingTicket.details && existingTicket.details.length > 0) {
            existingTicket.details.forEach(function(d) {
                changeCards.push({
                    nomor: d.nomor_detail || '', // 
                    alasan: d.alasan_ubah || '', // 
                    sebelum: d.before_ubah || '', // 
                    setelah: d.after_ubah || '', // 
                });
            });
            renderChangeCards();
        }

        function renderChangeCards() {
            const list = $('#change-card-list');
            list.find('.change-card-item').remove();
            if (changeCards.length === 0) {
                $('#change-empty-state').show();
                return;
            }
            $('#change-empty-state').hide();

            changeCards.forEach((card, idx) => {
                const nomor = card.nomor || '-';
                const alasan = card.alasan || '<span class="text-muted fst-italic">Belum diisi</span>';
                const el = $(`
                    <div class="change-card-item d-flex align-items-center gap-3 p-3 rounded-3 border mb-1"
                        style="background:linear-gradient(135deg,#f5edff,#ffffff); border-color:#e9d5ff !important; cursor:pointer;"
                        data-idx="${idx}">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                            style="width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;font-size:13px;font-weight:700;">
                            ${idx + 1}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate" style="font-size:12px;color:#6b21a8;">Nomor: ${nomor}</div>
                            <div class="text-truncate text-muted" style="font-size:11px;">${alasan}</div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted" style="font-size:11px;"></i>
                    </div>`);
                el.on('click', () => openChangeDetail(idx));
                list.append(el);
            });

            $('#change-hidden-inputs').remove();
            let hiddenHtml = '<div id="change-hidden-inputs">';
            changeCards.forEach((card, idx) => {
                hiddenHtml += `
                    <input type="hidden" name="no_perubahan[${idx}]"      value="${card.nomor   || ''}">
                    <input type="hidden" name="alasan_perubahan[${idx}]"  value="${card.alasan  || ''}">
                    <input type="hidden" name="sebelum_perubahan[${idx}]" value="${card.sebelum || ''}">
                    <input type="hidden" name="setelah_perubahan[${idx}]" value="${card.setelah || ''}">`;
            });
            hiddenHtml += '</div>';
            $('#form_pengajuan_ticket').append(hiddenHtml);
        }

        function openChangeDetail(idx) {
            activeChangeIndex = idx;
            const card = changeCards[idx];
            $('#change-detail-subtitle').text('Data #' + (idx + 1));
            $('#change-detail-index').val(idx);
            $('#cd-nomor').val(card.nomor || '');
            $('#cd-alasan').val(card.alasan || '');
            $('#cd-sebelum').val(card.sebelum || '');
            $('#cd-setelah').val(card.setelah || '');
            changeOffcanvas.show();
        }

        $('#btn-add-change-card').on('click', function() {
            changeCards.push({
                nomor: '',
                alasan: '',
                sebelum: '',
                setelah: ''
            });
            renderChangeCards();
            openChangeDetail(changeCards.length - 1);
        });

        $('#btn-save-change-detail').on('click', function() {
            const idx = parseInt($('#change-detail-index').val());
            changeCards[idx] = {
                nomor: $('#cd-nomor').val().trim(),
                alasan: $('#cd-alasan').val().trim(),
                sebelum: $('#cd-sebelum').val().trim(),
                setelah: $('#cd-setelah').val().trim(),
            };
            renderChangeCards();
            changeOffcanvas.hide();
        });

        $('#btn-delete-change-detail').on('click', function() {
            const idx = parseInt($('#change-detail-index').val());
            changeCards.splice(idx, 1);
            renderChangeCards();
            changeOffcanvas.hide();
        });

        $('#lampiran').on('change', function(e) {
            let files = e.target.files;
            $('#lampiran-preview').html('');
            Array.from(files).forEach((file, index) => {
                let ext = file.name.split('.').pop().toLowerCase();
                let html = '';
                if (file.type.startsWith('image/')) {
                    let reader = new FileReader();
                    reader.onload = function(ev) {
                        html = `
                    <div class="border rounded p-2 bg-light"
                        style="width:140px;">
                        <img src="${ev.target.result}"
                            class="img-fluid rounded mb-2"
                            style="height:90px;
                                   object-fit:cover;
                                   width:100%;">
                        <div class="small text-truncate mb-2">
                            ${file.name}
                        </div>
                    </div>
                `;
                        $('#lampiran-preview').append(html);
                    };
                    reader.readAsDataURL(file);
                } else {
                    html = `
                <div class="border rounded p-2 bg-light"
                    style="width:140px;">
                    <div class="text-center py-3">
                        <i class="fa-solid fa-file-pdf text-danger"
                            style="font-size:40px;"></i>
                    </div>
                    <div class="small text-truncate mb-2">
                        ${file.name}
                    </div>
                </div>
            `;
                    $('#lampiran-preview').append(html);
                }
            });
        });

        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div>'.repeat(3),
                showCancelButton: false,
                showConfirmButton: false,
            });
        };

        // Convert all text inputs and textareas to UPPERCASE
        $(document).on('input', 'input[type="text"], textarea', function() {
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });

    });
</script>
@endsection