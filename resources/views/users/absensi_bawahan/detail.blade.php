@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
<style>
    /* =====================
   DETAIL KARYAWAN (LUXURY MOBILE UI)
   ===================== */
    .detail-karyawan-wrapper {
        background: #faf9fb;
        min-height: 100vh;
        padding-bottom: 30px;
    }

    /* Header Gradient */
    .detail-header {
        height: 180px;
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        border-bottom-left-radius: 35px;
        border-bottom-right-radius: 35px;
        padding: 20px;
        position: relative;
    }

    /* Back Button Capsule dg Efek Glassmorphism */
    .btn-back-capsule {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: #fff;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: 0.3s;
    }

    .btn-back-capsule:active {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0.95);
    }

    /* Profil Card (Overlapping) */
    .detail-profile-card {
        background: #ffffff;
        border-radius: 24px;
        margin: -10px 20px 20px;
        /* Negatif margin agar naik ke atas header */
        padding: 30px 20px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(124, 58, 237, 0.08);
        position: relative;
        border: 1px solid rgba(124, 58, 237, 0.05);
    }

    .profile-avatar-wrapper {
        width: 85px;
        height: 85px;
        margin: -70px auto 15px;
        /* Naik ke atas card */
        position: relative;
    }

    .profile-avatar-wrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        object-fit: cover;
    }

    .status-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
    }

    .profile-name {
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .profile-role {
        font-size: 13px;
        color: #6b7280;
    }

    /* Contact Capsule */
    .contact-capsule {
        display: inline-block;
        background: #f3f4f6;
        color: #4b5563;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 11px;
        margin: 4px 2px;
        font-weight: 500;
    }

    /* Sections */
    .detail-section {
        padding: 0 10px;
        margin-bottom: 25px;
    }

    .section-title {
        font-weight: 700;
        color: #374151;
        font-size: 15px;
    }

    /* Grid Statistik */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .stat-info h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1.2;
    }

    .stat-info span {
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    /* Warna Utility untuk Icon Stat */
    .bg-success-light {
        background: #ecfdf5;
    }

    .bg-warning-light {
        background: #fffbeb;
    }

    .bg-danger-light {
        background: #fef2f2;
    }

    .bg-primary-light {
        background: #eff6ff;
    }

    /* Chart Container Placeholder */
    .chart-luxury-container {
        background: #ffffff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    /* Riwayat Timeline Mewah */
    .history-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .timeline-item {
        background: #ffffff;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.02);
        transition: 0.2s;
    }

    .timeline-item:active {
        transform: scale(0.98);
        background: #faf9ff;
    }

    /* Kotak Tanggal dg Soft Shadow */
    .timeline-date {
        background: linear-gradient(135deg, #f3e8ff, #ffffff);
        border: 1px solid #e9d5ff;
        min-width: 50px;
        height: 55px;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        box-shadow: 0 4px 10px rgba(124, 58, 237, 0.08);
    }

    .timeline-date .date {
        font-size: 18px;
        font-weight: 800;
        line-height: 1;
    }

    .timeline-date .month {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-day {
        font-size: 14px;
        color: #1f2937;
    }

    #reportrange {
        border-radius: 14px;
        height: 35px;
        background: linear-gradient(135deg, #eee9f9, #ffffff);
        border: 1px dashed #d1d5db;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        cursor: pointer;

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

    /* =====================
   LIST KARYAWAN BAWAHAN (PREMIUM MOBILE UI)
   ===================== */
    .bawahan-list-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        /* Jarak antar card */
        padding: 10px 0;
    }

    .bawahan-card {
        display: flex;
        align-items: center;
        background: #ffffff;
        border-radius: 18px;
        padding: 16px;
        gap: 14px;
        border: 1px solid rgba(0, 0, 0, 0.03);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        /* Soft shadow mewah */
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    /* Efek saat card di-hover (Desktop) */
    .bawahan-card:hover {
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.12);
        /* Shadow warna ungu tipis */
        transform: translateY(-2px);
    }

    /* Efek smooth saat card di-tap (Mobile) */
    .bawahan-card:active {
        transform: scale(0.97);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        background-color: #faf9ff;
    }

    /* Styling Avatar */
    .bawahan-avatar {
        position: relative;
        width: 48px;
        height: 48px;
        flex-shrink: 0;
    }

    .bawahan-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Titik Indikator Online/Offline */
    .status-dot {
        position: absolute;
        bottom: 0;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    /* Bagian Info Teks */
    .bawahan-info {
        flex: 1;
        min-width: 0;
        /* Mencegah teks overlap */
    }

    .bawahan-name {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bawahan-role {
        font-size: 12px;
        display: block;
        margin-top: 2px;
    }

    /* Bagian Aksi & Status */
    .bawahan-action {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
    }

    .time-text {
        font-size: 11px;
        font-weight: 500;
    }

    /* Ikon Chevron (Panah Kanan) */
    .bawahan-chevron {
        color: #d1d5db;
        font-size: 14px;
        margin-left: 4px;
        transition: 0.3s;
    }

    .bawahan-card:hover .bawahan-chevron {
        color: #7c3aed;
        /* Berubah ungu saat di-hover/tap */
        transform: translateX(3px);
    }

    /* =====================
   STATUS BADGE REFINED
   ===================== */
    .status-badge {
        font-size: 10px;
        padding: 4px 10px;
        border-radius: 8px;
        /* Sudut sedikit melengkung, tidak full bulat agar modern */
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        display: inline-block;
    }

    /* Menggunakan warna dari CSS Anda sebelumnya dengan penyesuaian */
    .status-badge.TEPAT {
        background: #ecfdf5;
        color: #059669;
    }

    .status-badge.DATANG {
        background: #ecfdf5;
        color: #059669;
    }

    .text-TEPAT {
        color: #0faa79;
    }

    .text-TELAT {
        color: #dc5912;
    }

    .text-DATANG {
        color: #059669;
    }

    .text- {
        color: #dc5912;
    }

    .status-badge.TELAT {
        background: #fff7ed;
        color: #ea580c;
    }

    .status-badge.LIBUR {
        background: #f4f4f4;
        color: #7e7e7e;
    }

    .status-badge.TIDAK {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-badge.BELUM {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-badge.IZIN {
        background: #eff6ff;
        color: #3b82f6;
    }

    .status-badge.CUTI {
        background: #fdf4ff;
        color: #c026d3;
    }

    .absensi-info .time {
        font-weight: 600;
        font-size: 14px;
    }

    .absensi-info .divider {
        margin: 0 6px;
        color: #9ca3af;
    }

    /* Status badge */
    .status {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 600;
    }

    .status.TIDAKHADIR {
        background: #fff7ed;
        color: #ea1b0c;
    }

    .status.BELUMDIJADWALKAN {
        background: #fff7ed;
        color: #ea1b0c;
    }

    .status.TEPATWAKTU {
        background: #ecfdf5;
        color: #059669;
    }

    .status.TELAT {
        background: #fff7ed;
        color: #ea580c;
    }

    .status.IZIN {
        background: #eef2ff;
        color: #4f46e5;
    }

    .status.DATANGLEBIHAWAL {
        background: #eef2ff;
        color: #4f46e5;
    }

    .status {
        background: #eef2ff;
        color: #4f46e5;
    }

    .status.CUTI {
        background: #fdf4ff;
        color: #a21caf;
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
        padding: 8px 11px;
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
        font-size: 13px;
    }

    .profile-tabs .nav-link span {
        font-size: 12px;
    }

    .kbw-signature {
        width: fit-content;
        height: 100%;
    }

    #sig canvas {
        margin-top: 5px;
        width: 100%;
        height: auto;
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

    .skeleton {
        background: linear-gradient(90deg,
                #ede9fe 25%,
                /* purple-100 */
                #f5f3ff 37%,
                /* purple-50  */
                #ede9fe 63%);
        /* Warna dasar abu-abu terang */
        overflow: hidden;
        position: relative;
        border-radius: 4px;
    }

    /* Membuat elemen semu untuk efek kilauan */
    .skeleton::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(90deg,
                #ede9fe 25%,
                /* purple-100 */
                #f5f3ff 37%,
                /* purple-50  */
                #ede9fe 63%);
        animation: shimmerSmooth 1.5s linear infinite;
    }

    /* Mendefinisikan animasi kilauan yang bergerak ke kanan */
    @keyframes shimmerSmooth {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
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

    .filter-capsule {
        background: #ffffff;
        border: 1px solid rgba(124, 58, 237, 0.15);
        /* Border ungu sangat tipis */
        padding: 4px 12px 4px 4px;
        /* Padding kiri lebih kecil untuk mengakomodasi icon-circle */
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        user-select: none;
    }

    .filter-capsule .icon-circle {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }

    .filter-capsule .filter-text {
        font-size: 11px;
        font-weight: 700;
        color: #374151;
        white-space: nowrap;
    }

    /* Efek saat ditekan di Mobile */
    .filter-capsule:active {
        transform: scale(0.95);
        background: #faf9ff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05) !important;
    }

    .top-action-bar {
        padding: 15px 20px 0;
        /* Jarak dari tepi layar atas dan kiri */
        position: absolute;
        /* Agar tidak mendorong profil ke bawah */
        top: 0;
        left: 0;
        z-index: 10;
    }

    .btn-back-capsule {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        color: #7c3aed;
        /* Warna ungu utama */
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid rgba(124, 58, 237, 0.15);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .btn-back-capsule i {
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    /* Efek Hover (Desktop) */
    .btn-back-capsule:hover {
        color: #6d28d9;
        box-shadow: 0 6px 20px rgba(124, 58, 237, 0.12);
        border-color: rgba(124, 58, 237, 0.3);
    }

    .btn-back-capsule:hover i {
        transform: translateX(-3px);
        /* Panah geser sedikit ke kiri */
    }

    /* Efek saat ditap (Mobile) */
    .btn-back-capsule:active {
        transform: scale(0.95);
        background: #f8f6fc;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection
@section('content_top')
<div class="top-action-bar">
    <a href="javascript:history.back()" class="btn-back-capsule">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>
<div class="futuristic-header text-center">
    <div class="profile-avatar-wrapper">
        <img src="https://ui-avatars.com/api/?name=Ahmad+Faisal&background=f3e8ff&color=7c3aed&rounded=true" alt="Profile">
        <span class="status-indicator bg-success border-white"></span>
    </div>
    <h4 class="profile-name">Nama Karyawan</h4>
    <span class="profile-role">Jabatan Karyawan</span>

    <div class="profile-contact mt-2">
        <span class="contact-capsule phone"><i class="fa-solid fa-phone"></i> 0812-xxxx-xxxx</span>
        <span class="contact-capsule email"><i class="fa-solid fa-envelope"></i> karyawan@gmail.com</span>
    </div>
</div>
<div class="alert_approval"></div>
@if(session('approve_sukses'))
<div class="alert alert-success alert-dismissible fade show alert_session_approval">
    {{ session('approve_sukses') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('approve_gagal'))
<div class="alert alert-danger alert-dismissible fade show alert_session_approval">
    {{ session('approve_gagal') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<!-- <div class="detail-header">
    <a href="#" class="btn-back-capsule">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div> -->


<div class="detail-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="section-title mb-0">Statistik Absensi</h6>

        <div id="daterange-detail" class="filter-capsule shadow-sm">
            <div class="icon-circle">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <span class="filter-text">Pilih Tanggal</span>
            <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 10px;"></i>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-success-light text-success">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="stat-info">
                <h3 class="count_hadir">001</h3>
                <span>Hadir</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning-light text-warning">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 class="count_telat">001</h3>
                <span>Telat</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-danger-light text-danger">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <div class="stat-info">
                <h3 class="count_alpha">001</h3>
                <span>Tidak Hadir</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-primary-light text-primary">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <div class="stat-info">
                <h3 class="count_cuti">001</h3>
                <span>Izin/Cuti</span>
            </div>
        </div>
    </div>

    <div class="chart-luxury-container mt-3 text-center">
        <p class="text-muted small mb-0"><i class="fa-solid fa-chart-pie"></i> Area Grafik Persentase</p>
    </div>
</div>

<div class="detail-section">
    <h6 class="section-title mb-3">Detail Absensi</h6>

    <div class="history-timeline">
        <div class="timeline-item">
            <div class="timeline-date">
                <span class="date"></span>
                <span class="month"></span>
            </div>
            <div class="timeline-content">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="timeline-day fw-bold">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span class="status-badge TEPATWAKTU"></span>
                </div>
                <div class="timeline-time text-muted small">
                    <i class="fa-solid fa-arrow-right-to-bracket text-success"></i>
                    <span class="mx-2">|</span>
                    <i class="fa-solid fa-arrow-right-from-bracket text-secondary"></i>
                </div>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-date">
                <span class="date"></span>
                <span class="month"></span>
            </div>
            <div class="timeline-content">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="timeline-day fw-bold"></span>
                    <span class="status-badge"></span>
                </div>
                <div class="timeline-time text-muted small">
                    <i class="fa-solid fa-arrow-right-to-bracket text-danger"></i>
                    <span class="mx-2">|</span>
                    <i class="fa-solid fa-arrow-right-from-bracket text-secondary"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="detailAbsensi" style="height: auto; min-height: 40vh;">
    <div class="offcanvas-header pb-0 justify-content-center">
        <div style="width: 40px; height: 5px; background: #e2e8f0; border-radius: 10px;"></div>
    </div>
    <div class="offcanvas-body pt-4" id="detailContent">
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();" class="btn_klik btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                &nbsp;Logout
            </a>
            <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal</a>
        </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    $(document).on('click', '#btn_klik', function(e) {
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
    });
    window.onbeforeunload = function() {
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
    };
    var id = '{{$id}}';
    var route_prefix = '{{$routePrefix ?? "absensi_bawahan"}}';
    moment.locale('id');
    var start = moment().startOf('month');
    var end = moment();

    function cb(start, end, label) {
        $('#daterange-detail .filter-text').html(start.format('DD MMM') + ' - ' + end.format('DD MMM'));

        // --- Eksekusi Fungsi Load Data di Sini ---
        // Format untuk database: 2026-05-01
        var startDateDB = start.format('YYYY-MM-DD');
        var endDateDB = end.format('YYYY-MM-DD');

        // Panggil fungsi Anda
        load_data_absensi_bawahan(startDateDB, endDateDB, id);
    }

    $('#daterange-detail').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput :true, 
        ranges: {
            'Hari Ini': [moment(), moment()],
            'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Minggu Ini': [moment().startOf('week'), moment().endOf('week')],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: "Terapkan",
            cancelLabel: "Batal",
            customRangeLabel: "Custom Tanggal",
            daysOfWeek: ["Mi", "Se", "Sl", "Ra", "Ka", "Ju", "Sa"],
            monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
        },
        opens: 'left', // Buka popup ke arah kiri agar tidak keluar layar
        drops: 'down'
    }, cb);

    // Panggil fungsi pertama kali agar teksnya sesuai
    cb(start, end, 'Bulan Ini');

    function showSkeleton(selector) {
        const el = $(selector);

        // 1. Simpan konten asli ke memori elemen (agar bisa dikembalikan nanti)
        if (!el.data('original-content')) {
            el.data('original-content', el.html());
        }

        // 2. Baca panjang teks aslinya. Jika kosong, beri default misalnya 4 karakter.
        const textLength = el.text().trim().length || 4;

        // 3. Render skeleton dengan lebar sesuai jumlah karakter teks aslinya
        // (Tambahkan height: 1em agar tingginya sama dengan teks)
        el.html(
            `<span class="skeleton" style="width: ${textLength}ch; height: 1em; display: inline-block;"></span>`
        );
    }


    function hideSkeleton(selector, value) {
        $(selector)
            .html(value);
    }


    load_data_karyawan_bawahan(id);

    function load_data_karyawan_bawahan(id) {
        let data_approve = '';
        $.ajax({
            url: "{{ url('/') }}/" + route_prefix + "/get_detail_karyawan_bawahan/" + id,
            type: "GET",
            beforeSend: function() {
                showSkeleton('.profile-name');
                showSkeleton('.profile-role');
                showSkeleton('.profile-contact .phone');
                showSkeleton('.profile-contact .email');

            },
            error: function(data) {
                // console.log(data);
                $('.alert_approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                setTimeout(function() {
                    $('.alert_approval').fadeOut('slow');
                }, 6000);
                data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                hideSkeleton('.profile-name', data_approve);
            },
            success: function(res) {
                // console.log(res);
                var karyawan = '';
                if (res.code == 200) {
                    var jabatan = res.data ? res.data.karyawan_jabatan_one ? res.data.karyawan_jabatan_one.jabatan ? res.data.karyawan_jabatan_one.jabatan.nama_jabatan : '-' : '-' : '-';
                    var phone = res.data ? res.data.telepon ? res.data.telepon : '0812-xxxx-xxxx' : '0812-xxxx-xxxx';
                    var email = res.data ? res.data.email ? res.data.email : '-' : '-';
                    var profile = res.data ? res.data.foto_karyawan ? 'https://hrd.sumberpangan.store/storage/foto_karyawan/' + res.data.foto_karyawan : '/admin/assets/img/avatars/1.png' : '/admin/assets/img/avatars/1.png';
                    $('.profile-name').empty();
                    $('.profile-role').empty();
                    $('.profile-contact .phone').empty();
                    $('.profile-contact .email').empty();
                    hideSkeleton('.profile-name', res.data.name);
                    hideSkeleton('.profile-role', jabatan);
                    hideSkeleton('.profile-contact .phone', phone);
                    hideSkeleton('.profile-contact .email', email);
                    $('.profile-avatar-wrapper img').attr('src', profile);


                    // $('.profile-name').html(karyawan);
                } else if (res.code == 500) {
                    $('.alert_approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + res.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_approval').fadeOut('slow');
                    }, 6000);

                }
            },
        })
    }

    function load_data_absensi_bawahan(start_date, end_date, id) {
        // console.log(start_date, end_date, id);
        let data_approve = '';
        $.ajax({
            url: "{{ url('/') }}/" + route_prefix + "/get_detail_absensi_bawahan/" + id,
            type: "GET",
            data: {
                start_date: start_date,
                end_date: end_date
            },
            beforeSend: function() {
                showSkeleton('.count_hadir');
                showSkeleton('.count_telat');
                showSkeleton('.count_alpha');
                showSkeleton('.count_cuti');
                showSkeleton('.timeline-day');
                showSkeleton('.status-badge');
                showSkeleton('.timeline-time i');

            },
            error: function(data) {
                // console.log(data);
                $('.alert_approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                setTimeout(function() {
                    $('.alert_approval').fadeOut('slow');
                }, 6000);
                data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                hideSkeleton('.profile-name', data_approve);
            },
            success: function(res) {
                // console.log(res);
                if (res.code == 200) {
                    let dataAbsensi = res.data_absensi;
                    // 1. Hitung TEPAT WAKTU
                    let countTepatWaktu = dataAbsensi.filter(item => item.status === 'TEPAT WAKTU').length;

                    // 2. Hitung TELAT
                    let countTelat = dataAbsensi.filter(item => item.status === 'TELAT').length;
                    let countCuti = dataAbsensi.filter(item => item.status === 'CUTI').length;
                    let countAlpha = dataAbsensi.filter(item => item.status === 'TIDAK HADIR').length;
                    let totalHadir = countTepatWaktu + countTelat;
                    // console.log(countTepatWaktu, countTelat, totalHadir);
                    hideSkeleton('.count_hadir', totalHadir);
                    hideSkeleton('.count_telat', countTelat);
                    hideSkeleton('.count_cuti', countCuti);
                    hideSkeleton('.count_alpha', countAlpha);
                    var data_absensi = '';
                    dataAbsensi.forEach(item => {
                        var day = item.tanggal ? moment(item.tanggal).format('dddd') : '-';
                        var tanggal = item.tanggal ? moment(item.tanggal).format('DD') : '-';
                        var bulan = item.tanggal ? moment(item.tanggal).format('MMMM') : '-';
                        $('.history-timeline').empty();
                        data_absensi += `<div class="timeline-item">
                            <div class="timeline-date">
                                <span class="date">${tanggal}</span>
                                <span class="month">${bulan}</span>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="timeline-day fw-bold">${day}</span>
                                    <span class="status-badge ${item.status}">${item.status}</span>
                                </div>
                                <div class="timeline-time text-muted small">
                                    <i class="fa-solid fa-arrow-right-to-bracket text-${item.status}"></i> ${item.masuk ? item.masuk : '-'}
                                    <span class="mx-2">|</span>
                                    <i class="fa-solid fa-arrow-right-from-bracket text-secondary"></i> ${item.pulang ? item.pulang : '-'}
                                </div>
                            </div>
                        </div>`;
                        hideSkeleton('.history-timeline', data_absensi);
                    });
                    // $('.profile-name').html(karyawan);
                } else if (res.code == 500) {
                    $('.alert_approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + res.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_approval').fadeOut('slow');
                    }, 6000);
                }
            },
        })
    }
</script>
@endsection