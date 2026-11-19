@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - Tambah Ticket
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
<style>
    .futuristic-header {
        padding: 28px 20px 18px;
    }

    .futuristic-header h5 {
        font-weight: 600;
    }

    .futuristic-header small {
        color: rgba(100, 60, 180, 0.65);
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
        z-index: 6;
        pointer-events: none;
    }

    .form-control {
        border-radius: 12px;
        padding-left: 42px;
        height: 44px;
        border: 1px solid #e5e5e5;
        font-size: 13px;
        transition: border-color .2s, box-shadow .2s;
        background: #fdfcff;
    }

    .form-control:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .12);
        background: #fff;
        outline: none;
    }

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
    }

    textarea.form-control {
        height: auto;
        padding-top: 12px;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #e5e5e5 !important;
        border-radius: 12px !important;
        height: 44px !important;
        padding-left: 38px !important;
        background: #fdfcff !important;
        font-size: 13px !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        color: #374151 !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #a855f7 !important;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .12) !important;
    }

    .select-loader {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        z-index: 5;
        pointer-events: none;
    }

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

        0%,
        100% {
            opacity: 0.3;
        }

        50% {
            opacity: 1;
        }
    }

    .alert-inline {
        font-size: 12px;
        padding: 8px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        display: none;
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
        transition: background .3s;
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
        transition: transform .3s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .15);
    }

    .toggle-switch input:checked+.toggle-slider {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
    }

    .toggle-switch input:checked+.toggle-slider::before {
        transform: translateX(20px);
    }

    .border-dashed {
        border: 2px dashed #d8b4fe !important;
    }

    .kbw-signature {
        width: 100%;
        height: 160px;
        border: 1px solid #f3e8ff !important;
        background: #ffffff;
    }

    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
    }

    .skeleton-number {
        display: inline-block;
        width: 2ch;
        height: 30px;
        vertical-align: middle;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5;
        }

        50% {
            opacity: 1;
        }
    }

    #off-canvas-change-detail {
        z-index: 1090 !important;
    }

    #off-canvas-konfirmasi-save {
        z-index: 1095 !important;
    }

    #offcanvas_lampiran {
        z-index: 1095 !important;
    }

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

    .otorisasi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
    }

    @media (min-width: 768px) {
        .otorisasi-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .otorisasi-card {
        min-width: 0;
    }

    .otorisasi-card.d-none {
        display: none !important;
    }
    .subdetail-accordion-item {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 8px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .subdetail-accordion-item:hover {
        border-color: #c4b5fd;
    }
    .subdetail-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        cursor: pointer;
        user-select: none;
        background: #fdfcff;
        gap: 8px;
        transition: background 0.15s ease;
    }
    .subdetail-accordion-header:hover {
        background: #f5f0ff;
    }
    .subdetail-accordion-header[aria-expanded="true"] {
        background: #f3e8ff;
        border-bottom: 1px solid #ede9fe;
    }
    .subdetail-chevron {
        color: #7c3aed;
        font-size: 10px;
        transition: transform 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ede9fe;
        flex-shrink: 0;
    }
    .subdetail-accordion-header[aria-expanded="true"] .subdetail-chevron {
        transform: rotate(180deg);
        background: #7c3aed;
        color: #ffffff;
    }
    .subdetail-accordion-body {
        padding: 12px;
        background: #faf9ff;
    }
.field-empty { color: #c4b5fd; font-style: italic; font-size: 11px; }
.field-readonly {
    background: #faf9ff;
    border: 1.5px solid #ede9fe;
    border-radius: 12px;
    padding: 11px 14px 11px 42px;
    font-size: 13px;
    color: #374151;
    min-height: 44px;
    display: flex;
    align-items: center;
    position: relative;
    word-break: break-word;
}
.field-readonly.textarea-view {
    align-items: flex-start;
    min-height: 70px;
    padding-top: 12px;
    padding-bottom: 12px;
    line-height: 1.6;
}
.field-readonly.no-icon {
    padding-left: 14px;
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
        <h5 class="mt-3 mb-1" style="color: #623acf; font-size: 20px;">Form Pengajuan Ubah/Hapus</h5>
        <small class="text-muted">Isi formulir dengan lengkap dan benar</small>
    </div>
    <div id="alert_pengajuan_ticket" class="mx-3"></div>
    @if (session('hapus_ticket_sukses'))
    <div class="alert alert-success d-flex align-items-center gap-2 rounded-4 shadow-sm border-0 py-3 mx-3 mt-2 fade show">
        <i class="fa-regular fa-circle-check fs-5 text-success"></i>
        <div>
            <strong style="font-size:13px;">Hapus Ticket</strong><br>
            <span style="font-size:11px;color:#555;">{{ session('hapus_ticket_sukses') }}</span>
        </div>
    </div>
    @endif
    <div class="card-body px-3 px-md-4">
        <form class="my-2" method="post" id="form_pengajuan_ticket" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="nomor_ticket" id="nomor_ticket_hidden">
            <input type="hidden" name="tanggal_ticket" id="tanggal_ticket_hidden">
            <input type="hidden" name="signature" id="signature">
            <div class="col-12">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">1</span>
                        <h6 class="mb-0 fw-bold">Pengajuan Ubah / Hapus Data</h6>
                    </div>
                    <div class="form-input">
                        <span class="input-icon"><i class="fa-solid fa-desktop"></i></span>
                        <select name="ticket" id="ticket">
                            <option value="">-- Pilih Sistem / Aplikasi (Epicor / Accurate) --</option>
                            <option value="1">Epicor ERP</option>
                            <option value="2">Accurate Online SP</option>
                            <option value="3">Accurate Online AJA</option>
                        </select>
                    </div>
                    <div class="alert-inline alert alert-danger" id="alert_ticket"></div>
                    <div class="form-input">
                        <span class="input-icon"><i class="fa-solid fa-folder-open"></i></span>
                        <div class="select-loader d-none">
                            <div class="dot-loader"><span></span><span></span><span></span></div>
                        </div>
                        <select name="menu_ticket" id="menu_ticket">
                            <option value="">-- Pilih Modul Transaksi --</option>
                        </select>
                    </div>
                    <div class="alert-inline alert alert-danger" id="alert_menu_ticket"></div>
                    <div class="form-input">
                        <span class="input-icon"><i class="fa-solid fa-list-check"></i></span>
                        <div class="select-loader d-none">
                            <div class="dot-loader"><span></span><span></span><span></span></div>
                        </div>
                        <select name="submenu_ticket" id="submenu_ticket">
                            <option value="">-- Pilih Jenis Koreksi / Transaksi --</option>
                        </select>
                    </div>
                    <div class="alert-inline alert alert-danger" id="alert_submenu_ticket"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex align-items-center justify-content-between" style="font-size: 12px; color: #6b21a8;">
                            <span><i class="fa-solid fa-table-list me-1"></i> Detail Perubahan Data</span>
                            <small class="text-muted fw-normal" style="font-size: 10px;">(Wajib diisi minimal 1 transaksi)</small>
                        </label>
                        <div id="change-card-list" class="d-flex flex-column gap-2 mb-2">
                            <div id="change-empty-state"
                                class="text-center text-muted py-3 rounded-3 border border-dashed"
                                style="font-size: 12px; border-color: #d8b4fe !important; background: #faf8ff;">
                                <i class="fa-solid fa-table fa-lg mb-1 d-block" style="color: #d8b4fe;"></i>
                                Belum ada detail transaksi — klik <strong>+ Tambah Detail Data</strong>
                            </div>
                        </div>
                        <button type="button" id="btn-add-change-card"
                            class="btn btn-sm w-100 py-2"
                            style="border: 1.5px dashed #a855f7; color: #7c3aed; background: #faf5ff; border-radius: 10px; font-size: 12px; font-weight: 600;">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Detail Data Transaksi
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">2</span>
                        <h6 class="mb-0 fw-bold">Upload Lampiran Pendukung</h6>
                    </div>
                    <div class="mb-3">
                        <label for="lampiran_ticket"
                            class="form-label fw-semibold"
                            style="font-size: 12px; color: #6b21a8;">
                            <i class="fa-solid fa-paperclip me-1"></i>
                            Lampiran Bukti / Dokumen (Opsional)
                        </label>
                        <input class="form-control form-control-sm"
                            type="file"
                            id="lampiran_ticket"
                            name="lampiran[]"
                            multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.jpg,.jpeg,.png,.gif,.webp,.bmp,.zip,.rar,.7z,image/*"
                            style="padding-left: 14px; height: auto; padding-top: 10px; padding-bottom: 10px;">
                        <small class="text-muted d-block mt-1" style="font-size: 10.5px;">Format file: PDF, Excel, Word, Foto/Gambar, ZIP. Maksimal 5MB per file.</small>
                        <div id="lampiran-preview" class="mt-2 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">3</span>
                        <h6 class="mb-0 fw-bold">Otorisasi &amp; Pengesahan Pengaju</h6>
                    </div>
                    <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                        <div class="card-header bg-light border-0 py-2 text-center">
                            <input type="hidden" name="name" value="{{ $user_karyawan->name }}">
                            <small class="fw-bold text-uppercase"
                                style="font-size: 10px; color: #6b21a8;">
                                Diajukan Oleh: {{ $user_karyawan->name }}
                            </small>
                        </div>
                        <div class="card-body p-3">
                            <div class="alert-inline alert alert-danger" id="alert_signature"></div>
                            <canvas id="signature-pad"
                                style="width:100%; touch-action: none !important; height:180px; border:1.5px solid #f3e8ff; border-radius: 10px; display:block; background:#fdfcff;"></canvas>
                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <button type="button"
                                    class="btn btn-outline-danger btn-sm py-1 px-2.5 rounded-pill"
                                    style="font-size: 11px;"
                                    id="clear">
                                    <i class="fa-solid fa-eraser me-1"></i> Hapus Goresan
                                </button>
                                <small class="text-muted" style="font-size: 10.5px;">Gunakan jari/stylus untuk tanda tangan di atas</small>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12" id="cross_env_wrapper" style="display:none;">
                            <div class="card border rounded-3 p-3 h-100"
                                style="background:#ffffff; border:1px solid #000000 !important;">
                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-1 mb-2">
                                    <label class="fw-bold text-dark mb-0" style="font-size:12px;">
                                        <i class="fa-solid fa-network-wired me-1 text-primary"></i>
                                        Koreksi Terkait pada Sistem / Cabang Lain
                                    </label>
                                    <span class="badge bg-success-subtle text-primary border border-primary" style="font-size:9.5px;">
                                        Dapat Berjalan Bersamaan
                                    </span>
                                </div>
                                <div class="row g-2" id="cross_env_container"></div>
                            </div>
                        </div>
                        <div class="col-12" id="conditions_wrapper" style="display:none;">
                            <div class="card border rounded-3 p-3 h-100"
                                style="background:#faf5ff; border-color:#d8b4fe !important;">
                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-1 mb-1">
                                    <label class="fw-bold text-dark mb-0" style="font-size:12px;">
                                        <i class="fa-solid fa-arrows-split-up-and-left me-1 text-primary"></i>
                                        Koreksi Tambahan pada Modul Terkait (Opsional)
                                    </label>
                                    <span class="badge" style="background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe; font-size:9.5px;">
                                        <i class="fa-solid fa-sliders me-1"></i>Pilihan Opsional
                                    </span>
                                </div>
                                <small class="text-muted d-block mb-2" style="font-size:10.5px;">
                                    Modul di bawah ini bersifat <strong>opsional</strong>. Aktifkan hanya jika perubahan data ini juga berdampak dan perlu disesuaikan pada modul terkait (tiket turunan akan dibuat otomatis jika diaktifkan):
                                </small>
                                <div class="row g-2" id="conditions_container"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border rounded-3 p-3" style="background:#f8fafc; border-color:#e2e8f0;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <label class="fw-bold mb-0 text-dark" style="font-size:12px;" for="rev_switch">
                                            <i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i>
                                            Revisi / Koreksi Dokumen Transaksi Sebelumnya
                                        </label>
                                        <small class="d-block text-muted" style="font-size:10.5px;">
                                            Aktifkan opsi ini jika pengajuan bertujuan untuk membatalkan atau merevisi dokumen transaksi yang telah terbit sebelumnya
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <span class="badge bg-light text-muted border py-1 px-2" id="rev_switch_badge" style="font-size:10px;">
                                            <i class="fa-solid fa-circle-minus me-1 text-muted"></i>Nonaktif (Normal)
                                        </span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input"
                                                type="checkbox"
                                                id="rev_switch"
                                                name="include_backward_dependency"
                                                value="1">
                                        </div>
                                    </div>
                                </div>
                                <div id="rev_info_panel" class="mt-2 p-3 rounded-3 border" style="display:none; background:#eff6ff; border-color:#bfdbfe !important;">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa-solid fa-circle-info text-primary mt-0.5" style="font-size:13px;"></i>
                                        <div style="font-size:11px; line-height:1.45;" class="text-dark">
                                            <strong class="d-block text-primary mb-1">Penjelasan Dampak Mode Koreksi Transaksi Sebelumnya:</strong>
                                            <ul class="mb-0 ps-3 text-secondary" style="font-size:10.5px;">
                                                <li><b>Pemeriksa (Checker):</b> Sistem akan otomatis menyertakan Pemeriksa dari modul dokumen asal untuk memverifikasi konsistensi data sebelum revisi dieksekusi.</li>
                                                <li><b>Modul Prasyarat:</b> Jika terdapat konfigurasi relasi balik, opsi koreksi untuk dokumen sumber/prasyarat akan otomatis dimunculkan.</li>
                                                <li><b>Rincian Perubahan:</b> Pastikan nomor dokumen atau nomor transaksi lama dicantumkan pada alasan perubahan di tabel detail di atas.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert-inline alert alert-danger" id="alert_atasan_1"></div>
                    <div class="alert-inline alert alert-danger" id="alert_atasan_2"></div>
                    <div class="otorisasi-grid mb-3" id="otorisasi_cards">
                        <div class="otorisasi-card">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIAJUKAN OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                </div>
                                <small class="fw-bold text-break" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                    {{ $user_karyawan->name }}
                                </small>
                            </div>
                        </div>
                        <div class="otorisasi-card">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-1:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                </div>
                                <small class="fw-bold text-break" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;" id="gen_atasan1">
                                    <span class="skeleton" style="height:11px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                </small>
                            </div>
                        </div>
                        <div class="otorisasi-card">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DISETUJUI OLEH-2:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                </div>
                                <small class="fw-bold text-break" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;" id="gen_atasan2">
                                    <span class="skeleton" style="height:11px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                </small>
                            </div>
                        </div>
                        <div class="otorisasi-card checker-card" id="gen_checker_card">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIPERIKSA OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                </div>
                                <small class="fw-bold text-break" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;" id="gen_checker">
                                    <span class="skeleton" style="height:11px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                </small>
                            </div>
                        </div>
                        <div class="otorisasi-card">
                            <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                                 style="background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;">
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIVALIDASI OLEH:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                </div>
                                <small class="fw-bold text-break" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                    JM FINANCE &amp; ACCOUNTING
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="alert-inline alert alert-danger" id="alert_error_ticket"></div>
                    <button type="button" id="btn_save_konfirmasi_ticket"
                        class="btn btn-primary btn-sm w-100 py-3 rounded-pill fw-bold mb-2 shadow">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        KIRIM PENGAJUAN UBAH/HAPUS DATA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-konfirmasi-save" style="height: auto; min-height: 190px; z-index: 1095 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                <i class="fa-solid fa-circle-check fa-bounce"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Konfirmasi</h6>
                <small class="text-muted">Pastikan data sudah benar sebelum menyimpan</small>
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
                Batal&nbsp;<i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-change-detail" style="height: 85vh; z-index: 1090 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 18px;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Detail Transaksi</h6>
                <small class="text-muted" id="change-detail-subtitle">Transaksi #1</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">
        <input type="hidden" id="change-detail-index">
        <div class="alert-inline alert alert-danger" id="alert_change_detail"></div>
        <label class="form-label fw-semibold mb-0" style="font-size: 12px; color: #6b21a8;">
            <i class="fa-solid fa-hashtag me-1"></i> Nomor Transaksi
        </label>
        <div class="form-input mb-3" style="position: relative;">
            <!-- <span class="input-icon" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#6f42c1; font-size:14px; z-index:6;">
                <i class="fa-solid fa-hashtag"></i>
            </span> -->
            <input type="text" id="cd-nomor-transaksi" class="form-control" placeholder="Contoh: INV-2023-001 atau DO: 34567 (KDR) atau PO: 73456">
        </div>
        <label class="form-label fw-semibold mb-0" style="font-size: 12px; color: #6b21a8;">
            <i class="fa-solid fa-calendar-days me-1"></i> Tanggal Transaksi
        </label>
        <div class="form-input mb-3" style="position: relative;">
            <!-- <span class="input-icon" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#6f42c1; font-size:14px; z-index:6;">
                <i class="fa-solid fa-calendar-days"></i>
            </span> -->
            <input type="date" id="cd-tanggal-transaksi" class="form-control" placeholder="Tanggal Transaksi">
        </div>
        <div class="d-flex align-items-center justify-content-between mb-2 mt-4">
            <label class="form-label fw-semibold mb-0" style="font-size: 12px; color: #6b21a8;">
                <i class="fa-solid fa-list-ol me-1"></i> Baris Perubahan
            </label>
            <button type="button" id="btn-add-sub-line" class="btn btn-sm"
                style="border: 1px dashed #a855f7; color: #7c3aed; background: transparent; border-radius: 8px; font-size: 11px; padding: 3px 10px;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Baris
            </button>
        </div>
        <div id="sub-detail-line-list" class="d-flex flex-column gap-2 mb-3"></div>
        <div class="d-flex gap-2 mt-3 mb-4">
            <button type="button" id="btn-save-change-detail"
                class="btn btn-primary btn-sm flex-grow-1 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-save me-1"></i> Simpan Transaksi
            </button>
            <button type="button" id="btn-delete-change-detail"
                class="btn btn-danger btn-sm flex-grow-1 light px-3 py-2 rounded-pill">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        let menu_active = '{{ $menu_id }}';
        let changeCards = [];
        let activeChangeIndex = null;
        let activeSubDetails = [];
        const changeOC = new bootstrap.Offcanvas(document.getElementById('off-canvas-change-detail'));
        function escapeAttr(str) {
            return $('<div>').text(str || '').html().replace(/"/g, '&quot;');
        }
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            const ctx = canvas.getContext('2d');
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(ratio, ratio);
            signaturePad.clear();
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        $('#clear').on('click', () => signaturePad.clear());
        $('#ticket').select2({
            dropdownParent: $('body'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Sistem / Aplikasi (Epicor / Accurate) --',
            allowClear: true
        });
        $('#menu_ticket').select2({
            dropdownParent: $('body'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Modul Transaksi --',
            allowClear: true
        });
        $('#submenu_ticket').select2({
            dropdownParent: $('body'),
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Jenis Koreksi / Transaksi --',
            allowClear: true
        });
        function showSelectLoading(sel) {
            $(sel).prop('disabled', true)
                .closest('.form-input').find('.select-loader').removeClass('d-none');
        }
        function hideSelectLoading(sel) {
            $(sel).prop('disabled', false)
                .closest('.form-input').find('.select-loader').addClass('d-none');
        }
        function showAlert(selector, type, msg) {
            $(selector).stop(true, true)
                .html(`<div class="alert alert-${type} py-2 px-3 rounded-3" style="font-size:12px;">${msg}</div>`)
                .show();
            setTimeout(function() {
                $(selector).animate({
                    opacity: 0,
                    marginTop: '-10px'
                }, 800, function() {
                    $(this).slideUp('fast', function() {
                        $(this).html('').css({
                            opacity: '',
                            marginTop: ''
                        });
                    });
                });
            }, 5000);
        }
        let isAutoPopulating = false;
        $(document).on('change', '#ticket', function() {
            const id = $(this).val();
            $('#menu_ticket').html('<option value="">-- Pilih Modul --</option>').trigger('change');
            $('#submenu_ticket').html('<option value="">-- Pilih Jenis Perubahan --</option>').trigger('change');
            $('#alert_menu_ticket, #alert_submenu_ticket').hide().html('');
            if (!id) return;
            isAutoPopulating = true;
            showSelectLoading('#menu_ticket');
            $.ajax({
                type: 'GET',
                url: "{{ url('ticket/get_menu_kategori') }}" + '/' + id,
                cache: false,
                success: function(res) {
                    hideSelectLoading('#menu_ticket');
                    let opt = '<option value="">-- Pilih Modul --</option>';
                    if (res.code == 200 && res.menu.length > 0) {
                        res.menu.forEach(m => {
                            opt += `<option value="${m.id}">${m.nama_menu}</option>`;
                        });
                        $('#menu_ticket').html(opt).trigger('change');
                    } else {
                        isAutoPopulating = false;
                        $('#menu_ticket').html(opt).trigger('change');
                        showAlert('#alert_menu_ticket', 'warning',
                            '<i class="fa-solid fa-triangle-exclamation me-1"></i>Tidak ada modul tersedia.');
                    }
                },
                error: function(xhr) {
                    hideSelectLoading('#menu_ticket');
                    isAutoPopulating = false;
                    showAlert('#alert_menu_ticket', 'danger',
                        window.getFriendlyErrorMessage(xhr, 'Gagal memuat modul.'));
                }
            });
        });
        $(document).on('change', '#menu_ticket', function() {
            const menu_id = $(this).val();
            $('#submenu_ticket').html('<option value="">-- Pilih Jenis Perubahan --</option>').trigger('change');
            $('#alert_submenu_ticket').hide().html('');
            if (!menu_id) {
                isAutoPopulating = false;
                return;
            }
            showSelectLoading('#submenu_ticket');
            $.ajax({
                type: 'GET',
                url: "{{ url('ticket/submenu/get_submenu_ticket') }}" + '/' + menu_id,
                cache: false,
                success: function(res) {
                    hideSelectLoading('#submenu_ticket');
                    let opt = '<option value="">-- Pilih Jenis Perubahan --</option>';
                    const wasAuto = isAutoPopulating;
                    isAutoPopulating = false;
                    if (res.code == 200 && res.menu.length > 0) {
                        res.menu.forEach(m => {
                            opt += `<option value="${m.id}">${m.nama_sub_menu}</option>`;
                        });
                    } else {
                        if (!wasAuto) {
                            showAlert('#alert_submenu_ticket', 'warning',
                                '<i class="fa-solid fa-triangle-exclamation me-1"></i>Tidak ada jenis perubahan tersedia.');
                        }
                    }
                    $('#submenu_ticket').html(opt).trigger('change');
                },
                error: function(xhr) {
                    hideSelectLoading('#submenu_ticket');
                    const wasAuto = isAutoPopulating;
                    isAutoPopulating = false;
                    if (!wasAuto) {
                        showAlert('#alert_submenu_ticket', 'danger',
                            window.getFriendlyErrorMessage(xhr, 'Gagal memuat jenis perubahan.'));
                    }
                }
            });
        });
        $('#btn_save_konfirmasi_ticket').on('click', function() {
            if (changeCards.length === 0) {
                showAlert('#alert_pengajuan_ticket', 'danger',
                    'Minimal harus ada 1 detail perubahan (transaksi beserta sub detail-nya) yang diisi.');
                return;
            }
            const oc = new bootstrap.Offcanvas(document.getElementById('off-canvas-konfirmasi-save'));
            oc.show();
        });
        $(document).on('click', '#btn_simpan_ticket', function() {
            const btn = $(this);
            const orig = btn.html();
            if (changeCards.length === 0) {
                bootstrap.Offcanvas.getInstance(document.getElementById('off-canvas-konfirmasi-save'))?.hide();
                showAlert('#alert_pengajuan_ticket', 'danger',
                    'Minimal harus ada 1 detail perubahan (transaksi beserta sub detail-nya) yang diisi.');
                return;
            }
            let sig_val = '';
            if (!signaturePad.isEmpty()) {
                sig_val = signaturePad.toDataURL();
            }
            $('#signature').val(sig_val);
            let formData = new FormData($('#form_pengajuan_ticket')[0]);
            $('.condition-branch-toggle').each(function() {
                formData.set(`selected_conditions[${$(this).data('key')}]`, $(this).is(':checked') ? 1 : 0);
            });
            $.ajax({
                url: "{{ route('ticket.head.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    btn.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                    `);
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(orig);
                    bootstrap.Offcanvas.getInstance(document.getElementById('off-canvas-konfirmasi-save'))?.hide();
                    const errMsg = window.getFriendlyErrorMessage(xhr, 'Terjadi gangguan saat menyimpan data.');
                    showAlert('#alert_pengajuan_ticket', 'danger', errMsg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errMsg
                    });
                },
                success: function(data) {
                    btn.prop('disabled', false).html(orig);
                    bootstrap.Offcanvas.getInstance(document.getElementById('off-canvas-konfirmasi-save'))?.hide();
                    if (data.code == 200) {
                        showAlert('#alert_pengajuan_ticket', 'success', data.message);
                        $('#form_pengajuan_ticket')[0].reset();
                        signaturePad.clear();
                        changeCards = [];
                        renderChangeCards();
                        $('#ticket').val(null).trigger('change');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Ticket berhasil diajukan.',
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
                            const targetSelector = '#alert_' + topKey;
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
                    } else if (data.code == 500) {
                        const srvErrMsg = window.getFriendlyErrorMessage(data.message, 'Terjadi gangguan pada server. Silakan hubungi administrator.');
                        showAlert('#alert_error_ticket', 'danger', srvErrMsg);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: srvErrMsg
                        });
                    } else {
                        const failMsg = window.getFriendlyErrorMessage(data.message, 'Gagal menyimpan ticket.');
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

        function renderChangeCards() {
            const list = $('#change-card-list');
            list.find('.change-card-item').remove();
            if (changeCards.length === 0) {
                $('#change-empty-state').show();
            } else {
                $('#change-empty-state').hide();
                changeCards.forEach((card, idx) => {
                    const nomor = card.nomor_transaksi || '-';
                    const jumlahBaris = card.sub_details.length;
                    const subdetailsJson = JSON.stringify(card.sub_details.map(sd => ({
                        line_number: sd.line_number,
                        alasan_ubah: sd.alasan_ubah,
                        before_ubah: sd.before_ubah,
                        after_ubah: sd.after_ubah,
                    })));

                    let linesSummaryHtml = '';
                    if (card.sub_details && card.sub_details.length > 0) {
                        linesSummaryHtml = '<div class="mt-2 pt-2 border-top d-flex flex-column gap-1" style="border-color:#ede9fe !important;">';
                        card.sub_details.slice(0, 3).forEach((sd, sIdx) => {
                            linesSummaryHtml += `
                                <div class="d-flex align-items-center justify-content-between gap-2 overflow-hidden" style="font-size:10.5px;">
                                    <span class="fw-semibold text-truncate" style="color:#5b21b6; max-width:60%;">
                                        <i class="fa-solid fa-angle-right me-1 text-muted"></i>Line ${escapeAttr(sd.line_number || (sIdx+1))}: ${escapeAttr(sd.alasan_ubah || '-')}
                                    </span>
                                    <span class="text-muted text-truncate flex-shrink-0" style="font-size:9.5px; max-width:40%;">
                                        <span class="text-danger">${escapeAttr(sd.before_ubah || '')}</span> &rarr; <span class="text-success">${escapeAttr(sd.after_ubah || '')}</span>
                                    </span>
                                </div>
                            `;
                        });
                        if (card.sub_details.length > 3) {
                            linesSummaryHtml += `<div class="text-muted fst-italic" style="font-size:9.5px;">+ ${card.sub_details.length - 3} baris lainnya...</div>`;
                        }
                        linesSummaryHtml += '</div>';
                    }

                    const el = $(`
                        <div class="change-card-item p-3 rounded-3 border mb-2 shadow-sm"
                            style="background:linear-gradient(135deg,#faf5ff,#ffffff); border-color:#e9d5ff !important; cursor:pointer; transition: all 0.2s ease;"
                            data-idx="${idx + 1}"
                            data-nomor="${escapeAttr(nomor)}"
                            data-tanggal="${escapeAttr(card.tanggal_transaksi || '')}"
                            data-subdetails="${escapeAttr(subdetailsJson)}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                    style="width:36px;height:36px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;font-size:13px;font-weight:700;">
                                    ${idx + 1}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="fw-bold text-truncate" style="font-size:12.5px;color:#4c1d95;">No. Transaksi: ${nomor}</div>
                                        <span class="badge" style="background:#ede9fe; color:#6b21a8; font-size:10px;">${jumlahBaris} Baris</span>
                                    </div>
                                    <div class="text-truncate text-muted" style="font-size:11px;">
                                        <i class="fa-regular fa-calendar me-1"></i>${card.tanggal_transaksi || 'Tanggal belum diisi'}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <span class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" style="font-size:10.5px;">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                    </span>
                                </div>
                            </div>
                            ${linesSummaryHtml}
                        </div>`);
                    el.on('click', () => openChangeDetail(idx));
                    list.append(el);
                });
            }
            $('#change-hidden-inputs').remove();
            let h = '<div id="change-hidden-inputs">';
            changeCards.forEach((c, i) => {
                h += `<input type="hidden" name="details[${i}][nomor_transaksi]" value="${escapeAttr(c.nomor_transaksi)}">`;
                h += `<input type="hidden" name="details[${i}][tanggal_transaksi]" value="${escapeAttr(c.tanggal_transaksi)}">`;
                c.sub_details.forEach((sd, j) => {
                    h += `<input type="hidden" name="details[${i}][sub_details][${j}][line_number]" value="${escapeAttr(sd.line_number)}">`;
                    h += `<input type="hidden" name="details[${i}][sub_details][${j}][alasan_ubah]" value="${escapeAttr(sd.alasan_ubah)}">`;
                    h += `<input type="hidden" name="details[${i}][sub_details][${j}][before_ubah]" value="${escapeAttr(sd.before_ubah)}">`;
                    h += `<input type="hidden" name="details[${i}][sub_details][${j}][after_ubah]" value="${escapeAttr(sd.after_ubah)}">`;
                });
            });
            h += '</div>';
            $('#form_pengajuan_ticket').append(h);
        }

        function renderSubDetailLines(openIdx = 0) {
            const wrap = $('#sub-detail-line-list');
            wrap.empty();
            if (activeSubDetails.length === 0) {
                wrap.append(`<div class="text-center text-muted py-3 rounded-3 border border-dashed" style="font-size:11px; border-color:#d8b4fe !important;"><i class="fa-solid fa-table fa-lg mb-1 d-block" style="color:#d8b4fe;"></i>Belum ada baris — klik <strong>Tambah Baris</strong></div>`);
                return;
            }
            activeSubDetails.forEach((sd, j) => {
                const isExpanded = j === openIdx;
                const collapseId = 'edit_collapse_sub_' + j;
                const summaryText = sd.alasan_ubah || (sd.line_number ? 'Line: ' + sd.line_number : 'Baris Baru (Klik untuk isi)');
                const el = $(`
                    <div class="subdetail-accordion-item sub-detail-line-item mb-2" data-line-idx="${j}">
                        <div class="subdetail-accordion-header ${isExpanded ? '' : 'collapsed'}"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="${isExpanded ? 'true' : 'false'}"
                            aria-controls="${collapseId}">
                            <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                                <span class="badge" style="background:#ede9fe; color:#6b21a8; font-size:10px;">
                                    <i class="fa-solid fa-list-ol me-1"></i> Line #${j + 1}
                                </span>
                                <span class="text-truncate fw-semibold sdl-header-summary" style="font-size:11px; color:#4b5563;">
                                    ${escapeAttr(summaryText)}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-sub-line" data-line-idx="${j}" title="Hapus Baris" style="font-size:12px; line-height:1; z-index:2;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                <div class="subdetail-chevron">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                        <div id="${collapseId}" class="collapse ${isExpanded ? 'show' : ''}">
                            <div class="subdetail-accordion-body p-3">
                                <div class="mb-2">
                                    <label class="fw-bold mb-1" style="color:#6b21a8; font-size:11px;">
                                        <i class="fa-solid fa-hashtag me-1"></i>Nomor Line / Keterangan Line
                                    </label>
                                    <textarea class="form-control form-control-sm sdl-line-detail" rows="1" placeholder="Contoh: Line 1 atau Line 2 atau Semua Line" style="font-size:12px;">${escapeAttr(sd.line_number || '')}</textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="fw-bold mb-1" style="color:#6b21a8; font-size:11px;">
                                        <i class="fa-solid fa-comment-dots me-1"></i>Alasan Ubah / Hapus
                                    </label>
                                        <textarea class="form-control form-control-sm sdl-alasan" rows="2" placeholder="Contoh: Ubah Harga / Salah Input" style="font-size:12px;">${escapeAttr(sd.alasan_ubah || '')}</textarea>
                                    </div>
                                <div class="row g-2">
                                    <div class="col-12 col-md-6">
                                        <label class="fw-bold mb-1 text-danger" style="font-size:11px;">
                                            <i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum Perubahan
                                        </label>
                                        <textarea class="form-control form-control-sm sdl-before" rows="2" placeholder="Contoh: Harga Rp. 200.000 atau Ubah Tonase: 8000 KG" style="font-size:12px; background:#fffbfb; border-color:#fecdd3;">${escapeAttr(sd.before_ubah || '')}</textarea>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="fw-bold mb-1 text-success" style="font-size:11px;">
                                            <i class="fa-solid fa-circle-check me-1"></i>Setelah Perubahan
                                        </label>
                                        <textarea class="form-control form-control-sm sdl-after" rows="2" placeholder="Contoh: Harga Rp. 200.000 atau Ubah Tonase: 8000 KG" style="font-size:12px; background:#f0fdf4; border-color:#bbf7d0;">${escapeAttr(sd.after_ubah || '')}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`);
                wrap.append(el);
            });
        }
        function collectSubDetailLinesFromDom() {
            const rows = [];
            $('#sub-detail-line-list .sub-detail-line-item').each(function() {
                rows.push({
                    line_number: $(this).find('.sdl-line-detail').val().trim(),
                    alasan_ubah: $(this).find('.sdl-alasan').val().trim(),
                    before_ubah: $(this).find('.sdl-before').val().trim(),
                    after_ubah: $(this).find('.sdl-after').val().trim(),
                });
            });
            return rows;
        }
        $(document).on('input', '.sdl-alasan, .sdl-line-detail', function() {
            const item = $(this).closest('.sub-detail-line-item');
            const alasan = item.find('.sdl-alasan').val().trim();
            const line = item.find('.sdl-line-detail').val().trim();
            const summary = alasan || (line ? 'Line: ' + line : 'Baris Baru (Klik untuk isi)');
            item.find('.sdl-header-summary').text(summary);
        });
        function openChangeDetail(idx) {
            activeChangeIndex = idx;
            const c = changeCards[idx];
            activeSubDetails = JSON.parse(JSON.stringify(c.sub_details || []));
            $('#change-detail-subtitle').text('Transaksi #' + (idx + 1));
            $('#change-detail-index').val(idx);
            $('#cd-nomor-transaksi').val(c.nomor_transaksi || '');
            $('#cd-tanggal-transaksi').val(c.tanggal_transaksi || '');
            $('#alert_change_detail').hide().html('');
            renderSubDetailLines(0);
            changeOC.show();
        }
        $('#btn-add-change-card').on('click', function() {
            changeCards.push({
                nomor_transaksi: '',
                tanggal_transaksi: '',
                sub_details: []
            });
            renderChangeCards();
            openChangeDetail(changeCards.length - 1);
        });
        $(document).on('click', '#btn-add-sub-line', function() {
            activeSubDetails = collectSubDetailLinesFromDom();
            activeSubDetails.push({
                line_number: '',
                alasan_ubah: '',
                before_ubah: '',
                after_ubah: ''
            });
            renderSubDetailLines(activeSubDetails.length - 1);
        });
        $(document).on('click', '.btn-remove-sub-line', function(e) {
            e.stopPropagation();
            const lineIdx = parseInt($(this).data('line-idx'));
            activeSubDetails = collectSubDetailLinesFromDom();
            activeSubDetails.splice(lineIdx, 1);
            renderSubDetailLines(Math.max(0, activeSubDetails.length - 1));
        });
        $('#btn-save-change-detail').on('click', function() {
            const idx = parseInt($('#change-detail-index').val());
            const nomorTransaksi = $('#cd-nomor-transaksi').val().trim();
            const tanggalTransaksi = $('#cd-tanggal-transaksi').val().trim();
            const subDetails = collectSubDetailLinesFromDom();
            if (!nomorTransaksi) {
                showAlert('#alert_change_detail', 'danger', 'Nomor transaksi wajib diisi.');
                return;
            }
            if (!tanggalTransaksi) {
                showAlert('#alert_change_detail', 'danger', 'Tanggal transaksi wajib diisi.');
                return;
            }
            if (subDetails.length === 0) {
                showAlert('#alert_change_detail', 'danger', 'Minimal 1 baris perubahan wajib diisi.');
                return;
            }
            for (let i = 0; i < subDetails.length; i++) {
                const sd = subDetails[i];
                if (!sd.line_number || !sd.alasan_ubah || !sd.before_ubah || !sd.after_ubah) {
                    showAlert('#alert_change_detail', 'danger',
                        `Baris #${i + 1}: Nomor Line, Alasan Ubah, Sebelum Perubahan, dan Setelah Perubahan wajib diisi.`);
                    renderSubDetailLines(i);
                    return;
                }
            }
            changeCards[idx] = {
                nomor_transaksi: nomorTransaksi,
                tanggal_transaksi: $('#cd-tanggal-transaksi').val(),
                sub_details: subDetails,
            };
            renderChangeCards();
            changeOC.hide();
        });
        $('#btn-delete-change-detail').on('click', function() {
            const idx = parseInt($('#change-detail-index').val());
            changeCards.splice(idx, 1);
            renderChangeCards();
            changeOC.hide();
        });
        let selectedFiles = new DataTransfer();
        $('#lampiran_ticket').on('change', function(e) {
            const newFiles = Array.from(e.target.files);
            newFiles.forEach(function(file) {
                const alreadyExists = Array.from(selectedFiles.files).some(
                    existingFile =>
                    existingFile.name === file.name &&
                    existingFile.size === file.size &&
                    existingFile.lastModified === file.lastModified
                );
                if (!alreadyExists) {
                    selectedFiles.items.add(file);
                }
            });
            this.files = selectedFiles.files;
            renderLampiranPreview();
        });

        function renderLampiranPreview() {
            const preview = $('#lampiran-preview');
            preview.html('');
            Array.from(selectedFiles.files).forEach(function(file, index) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                let filePreview = '';
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        filePreview = `
                    <div class="border rounded-3 p-2 bg-light position-relative"
                         style="width:160px;">
                        <button type="button"
                                class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 btn-remove-lampiran"
                                data-index="${index}"
                                style="
                                    width:25px;
                                    height:25px;
                                    padding:0;
                                    z-index:10;
                                ">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <img src="${e.target.result}"
                             class="img-fluid rounded-2 mb-2"
                             style="
                                height:90px;
                                object-fit:cover;
                                width:100%;
                             ">
                        <div class="text-truncate mb-1"
                             style="font-size:11px; color:#555;"
                             title="${file.name}">
                            ${file.name}
                        </div>
                        <div style="font-size:10px; color:#888;">
                            ${fileSizeMB} MB
                        </div>
                    </div>
                `;
                        preview.append(filePreview);
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.append(`
                <div class="border rounded-3 p-2 bg-light position-relative"
                     style="width:160px;">
                    <button type="button"
                            class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 btn-remove-lampiran"
                            data-index="${index}"
                            style="
                                width:25px;
                                height:25px;
                                padding:0;
                                z-index:10;
                            ">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="text-center py-3">
                        <i class="fa-solid fa-file text-primary"
                           style="font-size:40px;"></i>
                    </div>
                    <div class="text-truncate mb-1"
                         style="font-size:11px; color:#555;"
                         title="${file.name}">
                        ${file.name}
                    </div>
                    <div style="font-size:10px; color:#888;">
                        ${fileSizeMB} MB
                    </div>
                </div>
            `);
                }
            });
        }
        $(document).on('click', '.btn-remove-lampiran', function() {
            const index = parseInt($(this).data('index'));
            const newFiles = new DataTransfer();
            Array.from(selectedFiles.files).forEach(function(file, i) {
                if (i !== index) {
                    newFiles.items.add(file);
                }
            });
            selectedFiles = newFiles;
            $('#lampiran_ticket')[0].files = selectedFiles.files;
            renderLampiranPreview();
        });
        $('#submenu_ticket').on('change', function() {
            let submenuId = $(this).val();
            let environment = $('#ticket').val();
            if (!submenuId || !environment) {
                return;
            }
            function namesToText(names, fallback) {
                if (Array.isArray(names) && names.length) {
                    return names.join(', ');
                }
                if (typeof names === 'string' && names.trim() !== '') {
                    return names;
                }
                return fallback || '-';
            }
            function showPersistentAlert(selector, type, msg) {
                $(selector).stop(true, true)
                    .html(`<div class="alert alert-${type} py-2 px-3 rounded-3" style="font-size:12px;">${msg}</div>`)
                    .css({
                        opacity: '',
                        marginTop: ''
                    })
                    .show();
            }
            $('#gen_atasan1').html('<span class="skeleton" style="height:11px; width:60px; display:inline-block;"></span>');
            $('#gen_atasan2').html('<span class="skeleton" style="height:11px; width:60px; display:inline-block;"></span>');
            $('#gen_checker').html('<span class="skeleton" style="height:11px; width:60px; display:inline-block;"></span>');
            $('#otorisasi_cards .checker-card-dynamic').remove();
            $('#gen_checker_card').removeClass('d-none');
            $('#alert_atasan_1, #alert_atasan_2').stop(true, true).hide().html('');
            $('#btn_save_konfirmasi_ticket').prop('disabled', false);
            let includeBackward = $('input[name="include_backward_dependency"]:checked').val() || 0;
            let selectedConditions = {};
            $('.condition-branch-toggle').each(function() {
                selectedConditions[$(this).data('key')] = $(this).is(':checked') ? 1 : 0;
            });
            $.get(
                '/approval/ticket/preview-flow/' + submenuId, {
                    environment: environment,
                    include_backward_dependency: includeBackward ? 1 : 0,
                    selected_conditions: selectedConditions
                },
                function(res) {
                    if (res.code == 404 || res.found === false) {
                        $('#gen_atasan1').html('-');
                        $('#gen_atasan2').html('-');
                        $('#gen_checker').html('-');
                        showPersistentAlert('#alert_atasan_1', 'danger',
                            '<i class="fa-solid fa-triangle-exclamation me-1"></i>' +
                            (res.message || 'Atasan tidak ditemukan pada alur persetujuan untuk menu/submenu ini. Hubungi admin untuk melengkapi konfigurasi approval.')
                        );
                        $('#btn_save_konfirmasi_ticket').prop('disabled', true);
                        return;
                    }
                    if (res.cross_env_conditions && res.cross_env_conditions.length > 0) {
                        let crossHtml = '';
                        res.cross_env_conditions.forEach(function(c) {
                            let isChecked = selectedConditions[c.condition_key] !== undefined ?
                                (selectedConditions[c.condition_key] == 1) : true;
                            let label = c.label || `Revisi Transaksi Terkait di ${c.target_kategori_name || 'Sistem Terkait'}`;
                            
                            let envTheme = {
                                bg: '#f0fdf4',
                                border: '#bbf7d0',
                                badgeBg: '#dcfce7',
                                badgeText: '#15803d',
                                badgeBorder: '#86efac',
                                iconColor: '#16a34a'
                            };
                            if (c.target_kategori === 2) {
                                envTheme = {
                                    bg: '#fef2f2',
                                    border: '#fecaca',
                                    badgeBg: '#fee2e2',
                                    badgeText: '#b91c1c',
                                    badgeBorder: '#fca5a5',
                                    iconColor: '#dc2626'
                                };
                            } else if (c.target_kategori === 1) {
                                envTheme = {
                                    bg: '#eff6ff',
                                    border: '#bfdbfe',
                                    badgeBg: '#dbeafe',
                                    badgeText: '#1d4ed8',
                                    badgeBorder: '#93c5fd',
                                    iconColor: '#2563eb'
                                };
                            }

                            crossHtml += `
                                <div class="col-12">
                                    <div class="card border rounded-3 p-3 h-100 shadow-none" style="background:${envTheme.bg}; border-color:${envTheme.border} !important;">
                                        <div class="d-flex align-items-center justify-content-between gap-3">
                                            <div style="min-width:0;" class="flex-grow-1">
                                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-1.5 mb-1.5 flex-wrap">
                                                    <span class="badge" style="background:${envTheme.badgeBg}; color:${envTheme.badgeText}; border:1px solid ${envTheme.badgeBorder}; font-size:10px; max-width:100%; white-space:normal; text-align:left;">
                                                        <i class="fa-solid fa-network-wired me-1"></i>${c.target_kategori_name || 'Sistem Terkait'}
                                                    </span>
                                                    <span class="badge bg-light text-muted border" style="font-size:9.5px; max-width:100%; white-space:normal; text-align:left;">Dapat Berjalan Bersamaan</span>
                                                </div>
                                                <label class="fw-bold mb-0 text-dark d-block" for="cond_${c.condition_key}" style="font-size:12px; cursor:pointer;">
                                                    ${label}
                                                </label>
                                                <small class="d-block text-muted mt-0.5" style="font-size:10.5px;">
                                                    Modul Tujuan: <b style="color:${envTheme.badgeText}">${c.target_name}</b>
                                                </small>
                                                <div class="mt-2 pt-1.5 border-top" style="border-color:${envTheme.border} !important; font-size:10.5px;" id="desc_cond_${c.condition_key}">
                                                    ${isChecked 
                                                        ? '<span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i><strong>Tombol Nyala:</strong> Pengajuan di sistem akan ditampilkan pada halaman Pemeriksa sistem terkait & dapat diproses terpisah.</span>'
                                                        : '<span class="text-muted"><i class="fa-solid fa-circle-minus me-1"></i><strong>Tombol Mati:</strong> Pengajuan ini tidak akan membuat tiket di sistem tujuan.</span>'
                                                    }
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                                                <span class="badge ${isChecked ? 'bg-success text-white' : 'bg-light text-muted border'}" id="badge_cond_${c.condition_key}" style="font-size:9.5px;">
                                                    ${isChecked ? 'Aktif' : 'Mati'}
                                                </span>
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input condition-branch-toggle cross-env-toggle"
                                                        type="checkbox"
                                                        id="cond_${c.condition_key}"
                                                        data-key="${c.condition_key}"
                                                        data-type="cross"
                                                        name="selected_conditions[${c.condition_key}]"
                                                        value="1"
                                                        ${isChecked ? 'checked' : ''}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#cross_env_container').html(crossHtml);
                        $('#cross_env_wrapper').show();
                    } else {
                        $('#cross_env_wrapper').hide();
                    }

                    if (res.available_conditions && res.available_conditions.length > 0) {
                        let condHtml = '';
                        let colClass = res.available_conditions.length > 1 ? 'col-12 col-md-6' : 'col-12';
                        res.available_conditions.forEach(function(c) {
                            let isChecked = selectedConditions[c.condition_key] !== undefined ?
                                (selectedConditions[c.condition_key] == 1) : true;
                            let label = c.label || 'Koreksi modul terkait';
                            let katName = c.target_kategori_name || 'Sistem Terkait';
                            let isReverse = c.direction === 'REVERSE';
                            condHtml += `
                                <div class="${colClass}">
                                    <div class="card border rounded-3 p-3 h-100 shadow-none" style="background:#ffffff; border-color:#e9d5ff !important;">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <div style="min-width:0;" class="flex-grow-1">
                                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-1.5 mb-1.5 flex-wrap">
                                                    <span class="badge" style="background:#f3e8ff; color:#6b21a8; border:1px solid #d8b4fe; font-size:9.5px; max-width:100%; white-space:normal; text-align:left;">
                                                        <i class="fa-solid fa-cube me-1"></i>${katName}
                                                    </span>
                                                    ${isReverse ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:9px; max-width:100%; white-space:normal; text-align:left;"><i class="fa-solid fa-rotate-left me-1"></i>Revisi Dokumen Asal (Opsional)</span>' : '<span class="badge" style="background:#e0e7ff; color:#4338ca; border:1px solid #c7d2fe; font-size:9px; max-width:100%; white-space:normal; text-align:left;"><i class="fa-solid fa-sliders me-1"></i>Modul Opsional</span>'}
                                                </div>
                                                <label class="fw-semibold mb-0 text-dark text-truncate d-block" for="cond_${c.condition_key}" style="font-size:11.5px; cursor:pointer;" title="${label}">
                                                    ${label}
                                                </label>
                                                <small class="d-block text-muted text-truncate mt-0.5" style="font-size:10px;" title="${c.target_name}">
                                                    Modul: <strong class="text-primary">${c.target_name}</strong>
                                                </small>
                                                <div class="mt-2 pt-1.5 border-top border-light-subtle" style="font-size:10px;" id="desc_cond_${c.condition_key}">
                                                    ${isChecked
                                                        ? '<span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i><strong>Tombol Nyala:</strong> Tiket koreksi modul ini akan otomatis dibuat.</span>'
                                                        : '<span class="text-muted"><i class="fa-solid fa-circle-minus me-1"></i><strong>Tombol Mati:</strong> Modul ini tidak akan ikut dikoreksi.</span>'
                                                    }
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                                                <span class="badge ${isChecked ? 'bg-success text-white' : 'bg-light text-muted border'}" id="badge_cond_${c.condition_key}" style="font-size:9.5px;">
                                                    ${isChecked ? 'Aktif' : 'Mati'}
                                                </span>
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input condition-branch-toggle"
                                                        type="checkbox"
                                                        id="cond_${c.condition_key}"
                                                        data-key="${c.condition_key}"
                                                        data-type="branch"
                                                        name="selected_conditions[${c.condition_key}]"
                                                        value="1"
                                                        ${isChecked ? 'checked' : ''}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#conditions_container').html(condHtml);
                        $('#conditions_wrapper').show();
                    } else {
                        $('#conditions_wrapper').hide();
                    }
                    if (res.code == 200) {
                        $('#gen_atasan1').html('-');
                        $('#gen_atasan2').html('-');
                        $('#gen_checker').html('-');
                        $('#gen_validator').html('-');
                        $('#otorisasi_cards .checker-card-dynamic').remove();
                        $('#gen_checker_card').removeClass('d-none');
                        if (res.approvers?.length) {
                            const approve1 = res.approvers.find(x => x.role === 'approve1');
                            const approve2 = res.approvers.find(x => x.role === 'approve2');
                            if (approve1) {
                                $('#gen_atasan1').html(namesToText(approve1.karyawan_names, approve1.step_name));
                            }
                            if (approve2) {
                                $('#gen_atasan2').html(namesToText(approve2.karyawan_names, approve2.step_name));
                            }
                        }
                        function getEnvThemeForChecker(chk) {
                            let kat = chk.target_kategori || 1;
                            if (kat === 2) {
                                return {
                                    cardStyle: 'background:#fef2f2 !important; border-color:#fca5a5 !important;',
                                    badgeBg: '#fee2e2',
                                    badgeText: '#b91c1c',
                                    badgeBorder: '#fca5a5',
                                    envName: chk.target_kategori_name || 'Accurate SP',
                                    iconColor: '#dc2626'
                                };
                            } else if (kat === 3) {
                                return {
                                    cardStyle: 'background:#f0fdf4 !important; border-color:#86efac !important;',
                                    badgeBg: '#dcfce7',
                                    badgeText: '#15803d',
                                    badgeBorder: '#86efac',
                                    envName: chk.target_kategori_name || 'Accurate AJA',
                                    iconColor: '#16a34a'
                                };
                            }
                            return {
                                cardStyle: 'background:#eff6ff !important; border-color:#93c5fd !important;',
                                badgeBg: '#dbeafe',
                                badgeText: '#1d4ed8',
                                badgeBorder: '#93c5fd',
                                envName: chk.target_kategori_name || 'Epicor ERP',
                                iconColor: '#2563eb'
                            };
                        }

                        if (res.checkers && res.checkers.length > 1) {
                            $('#gen_checker_card').addClass('d-none');
                            let $insertAfter = $('#gen_checker_card');
                            res.checkers.forEach(function(chk, idx) {
                                let title = `DIPERIKSA OLEH-${idx + 1}`;
                                let envInfo = '';
                                let defaultCardStyle = 'background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;';
                                let cardCustomStyle = `style="${defaultCardStyle}"`;
                                
                                if (chk.is_cross_environment) {
                                    let theme = getEnvThemeForChecker(chk);
                                    cardCustomStyle = `style="${theme.cardStyle}"`;
                                    envInfo = `
                                        <div class="mb-1">
                                            <span class="badge" style="background:${theme.badgeBg}; color:${theme.badgeText}; border:1px solid ${theme.badgeBorder}; font-size:7.5px; padding:2px 4px; white-space:normal;">
                                                <i class="fa-solid fa-network-wired me-0.5"></i>${theme.envName}
                                            </span>
                                        </div>
                                    `;
                                }
                                
                                let branchInfo = '';
                                let branchText = chk.target_menu_name ? `${chk.target_menu_name} - ${chk.target_submenu_name || chk.branch_name}` : chk.branch_name;
                                if (branchText) {
                                    branchInfo = `<div class="text-truncate mt-1" style="font-size:8px; line-height:1.1; color:#4338ca !important; font-weight:600;" title="${branchText}">(${branchText})</div>`;
                                }

                                let $card = $(`
                                <div class="otorisasi-card checker-card-dynamic">
                                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between" ${cardCustomStyle}>
                                        <div>
                                            <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">${title}:</small>
                                            ${envInfo}
                                        </div>
                                        <div class="py-1 flex-grow-1 d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-clock-rotate-left" style="color:#94a3b8 !important;"></i>
                                        </div>
                                        <div>
                                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                                ${chk.name}
                                            </small>
                                            ${branchInfo}
                                        </div>
                                    </div>
                                </div>
                            `);
                                $insertAfter.after($card);
                                $insertAfter = $card;
                            });
                        } else if (res.checkers && res.checkers.length === 1) {
                            let chk = res.checkers[0];
                            $('#gen_checker').html(namesToText(chk.karyawan_names, chk.step_name));
                            $('#gen_checker_card .dynamic-env-meta').remove();
                            let singleBranchText = chk.target_menu_name ? `${chk.target_menu_name} - ${chk.target_submenu_name || chk.branch_name}` : chk.branch_name;
                            let branchInfo = singleBranchText ? `<div class="text-truncate mt-1 dynamic-env-meta" style="font-size:8px; line-height:1.1; color:#4338ca !important; font-weight:600;" title="${singleBranchText}">(${singleBranchText})</div>` : '';
                            if (chk.is_cross_environment) {
                                let theme = getEnvThemeForChecker(chk);
                                $('#gen_checker_card > div').attr('style', theme.cardStyle);
                                let envInfo = `
                                    <div class="mb-1 dynamic-env-meta">
                                        <span class="badge" style="background:${theme.badgeBg}; color:${theme.badgeText}; border:1px solid ${theme.badgeBorder}; font-size:7.5px; padding:2px 4px; white-space:normal;">
                                            <i class="fa-solid fa-network-wired me-0.5"></i>${theme.envName}
                                        </span>
                                    </div>
                                `;
                                $('#gen_checker_card > div > small:first-child').after(envInfo);
                                $('#gen_checker').after(branchInfo);
                            } else {
                                $('#gen_checker_card > div').attr('style', 'background: #ffffff !important; border: 1.5px solid #e2e8f0 !important;');
                                if (branchInfo) {
                                    $('#gen_checker').after(branchInfo);
                                }
                            }
                        } else {
                            $('#gen_checker_card').addClass('d-none');
                        }
                    }
                },
                'json'
            ).fail(function(xhr) {
                $('#gen_atasan1').html('-');
                $('#gen_atasan2').html('-');
                $('#gen_checker').html('-');
                showPersistentAlert('#alert_atasan_1', 'danger',
                    xhr.responseJSON?.message ?? 'Gagal memuat data approval.'
                );
                $('#btn_save_konfirmasi_ticket').prop('disabled', true);
            });
        });
        $(document).on('change', '.reverse-flow-toggle, .condition-branch-toggle, .cross-env-toggle', function() {
            $('#submenu_ticket').trigger('change');
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
        $(document).on('input', 'input[type="text"], textarea', function() {
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });
        function updateReverseLabel(isChecked) {
            if (isChecked) {
                $('#label_rev_on').css({
                    color: '#7c3aed'
                });
                $('#label_rev_off').css({
                    color: '#cbd5e1'
                });
            } else {
                $('#label_rev_on').css({
                    color: '#cbd5e1'
                });
                $('#label_rev_off').css({
                    color: '#64748b'
                });
            }
        }
        $(document).on('change', '#rev_switch', function() {
            const isChecked = $(this).is(':checked');
            if (isChecked) {
                $(this).val(1);
                $('#rev_switch_badge')
                    .removeClass('bg-light text-muted border')
                    .addClass('bg-primary text-white')
                    .html('<i class="fa-solid fa-circle-check me-1"></i>Aktif (Koreksi Dokumen Lama)');
                $('#rev_info_panel').slideDown(150);
            } else {
                $(this).val(0);
                $('#rev_switch_badge')
                    .removeClass('bg-primary text-white')
                    .addClass('bg-light text-muted border')
                    .html('<i class="fa-solid fa-circle-minus me-1 text-muted"></i>Nonaktif (Normal)');
                $('#rev_info_panel').slideUp(150);
            }
            $('#submenu_ticket').trigger('change');
        });

        $(document).on('change', '.condition-branch-toggle', function() {
            const isChecked = $(this).is(':checked');
            const key = $(this).data('key');
            const type = $(this).data('type');
            
            if (isChecked) {
                $(`#badge_cond_${key}`)
                    .removeClass('bg-light text-muted border')
                    .addClass('bg-success text-white')
                    .text('Aktif');
                if (type === 'cross') {
                    $(`#desc_cond_${key}`).html('<span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i><strong>Tombol Nyala:</strong> Pengajuan di sistem tujuan akan otomatis dibuat & dapat diproses bersamaan.</span>');
                } else {
                    $(`#desc_cond_${key}`).html('<span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i><strong>Tombol Nyala:</strong> Tiket koreksi modul ini akan otomatis dibuat.</span>');
                }
            } else {
                $(`#badge_cond_${key}`)
                    .removeClass('bg-success text-white')
                    .addClass('bg-light text-muted border')
                    .text('Mati');
                if (type === 'cross') {
                    $(`#desc_cond_${key}`).html('<span class="text-muted"><i class="fa-solid fa-circle-minus me-1"></i><strong>Tombol Mati:</strong> Pengajuan ini tidak akan membuat tiket di sistem tujuan.</span>');
                } else {
                    $(`#desc_cond_${key}`).html('<span class="text-muted"><i class="fa-solid fa-circle-minus me-1"></i><strong>Tombol Mati:</strong> Modul ini tidak akan ikut dikoreksi.</span>');
                }
            }
        });
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    });
</script>
@endsection