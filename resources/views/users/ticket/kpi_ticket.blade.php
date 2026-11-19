@extends('users.layouts.main')
@section('title')
APPS | Laporan KPI PIC
@endsection

@section('css')
<style>
    :root {
        --kpi-purple-primary: #7c3aed;
        --kpi-purple-secondary: #a855f7;
        --kpi-purple-light: #f5f3ff;
        --kpi-purple-border: #ede9fe;
    }

    .kpi-container {
        padding: 0 16px 32px;
    }

    /* TOP PROFILE HERO BANNER */
    .kpi-hero {
        background: linear-gradient(135deg, #6d28d9, #8b5cf6);
        border-radius: 20px;
        padding: 24px 20px;
        color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 12px 30px rgba(109, 40, 217, .25);
        position: relative;
        overflow: hidden;
    }

    .kpi-hero::after {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, .08);
        border-radius: 50%;
        top: -40px;
        right: -40px;
        pointer-events: none;
    }

    .kpi-hero-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .kpi-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, .18);
        border: 1px solid rgba(255, 255, 255, .3);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 999px;
        text-decoration: none;
        backdrop-filter: blur(4px);
        transition: all .2s;
    }

    .kpi-back-btn:hover {
        background: rgba(255, 255, 255, .3);
        color: #fff;
        transform: translateY(-1px);
    }

    .kpi-action-btns {
        display: flex;
        gap: 8px;
    }

    .kpi-hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: #6d28d9;
        font-size: 11px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
    }

    .kpi-hero-btn:hover {
        background: #f3f0ff;
        color: #5b21b6;
        transform: translateY(-1px);
    }

    .kpi-profile-box {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .kpi-avatar {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: rgba(255, 255, 255, .25);
        border: 2px solid rgba(255, 255, 255, .6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
    }

    .kpi-profile-info h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
    }

    .kpi-profile-info p {
        margin: 4px 0 0;
        font-size: 12px;
        opacity: .9;
    }

    .kpi-badge-role {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(255, 255, 255, .22);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-top: 6px;
    }

    /* SUMMARY CARDS GRID */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 16px;
        position: relative;
        transition: all .2s;
        box-shadow: 0 4px 12px rgba(124, 58, 237, .04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, .08);
        border-color: #c4b5fd;
    }

    .stat-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .stat-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .icon-purple { background: #f5f3ff; color: #7c3aed; }
    .icon-green  { background: #f0fdf4; color: #16a34a; }
    .icon-amber  { background: #fffbeb; color: #d97706; }
    .icon-blue   { background: #eff6ff; color: #2563eb; }
    .icon-red    { background: #fef2f2; color: #dc2626; }
    .icon-indigo { background: #eef2ff; color: #4f46e5; }

    .stat-card-val {
        font-size: 24px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1.1;
    }

    .stat-card-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-top: 4px;
    }

    .stat-card-sub {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* FILTER BAR */
    .filter-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .kpi-tabs {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 4px;
        max-width: 100%;
    }

    .kpi-tab {
        border: none;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 11px;
        font-weight: 700;
        padding: 7px 14px;
        border-radius: 999px;
        cursor: pointer;
        white-space: nowrap;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .kpi-tab:hover {
        background: #ede9fe;
        color: #7c3aed;
    }

    .kpi-tab.active {
        background: #7c3aed;
        color: #fff;
        box-shadow: 0 4px 12px rgba(124, 58, 237, .3);
    }

    .filter-inputs {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-grow: 1;
        justify-content: flex-end;
    }

    .search-box {
        position: relative;
        min-width: 200px;
        max-width: 280px;
        width: 100%;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 12px;
    }

    .search-input {
        width: 100%;
        padding: 7px 12px 7px 32px;
        font-size: 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        outline: none;
        transition: all .2s;
    }

    .search-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .1);
    }

    .search-input::placeholder,
    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
    }

    /* DATA TABLE */
    .table-container {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .02);
    }

    .kpi-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
    }

    .kpi-table thead th {
        background: #f8f6ff;
        color: #6b21a8;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 12px 14px;
        border-bottom: 1.5px solid #ede9fe;
        white-space: nowrap;
    }

    .kpi-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
        vertical-align: middle;
    }

    .kpi-table tbody tr:hover {
        background: #faf9ff;
    }

    .kpi-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* BADGES */
    .badge-sla-ontime {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
        border-radius: 50px;
        padding: 3px 9px;
        font-size: 10px;
        font-weight: 700;
    }

    .badge-sla-overrun {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        border-radius: 50px;
        padding: 3px 9px;
        font-size: 10px;
        font-weight: 700;
    }

    .badge-priority {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f3f4f6;
        color: #4b5563;
        border-radius: 6px;
        padding: 2px 7px;
        font-size: 10px;
        font-weight: 700;
    }

    .badge-feedback {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #fef3c7;
        color: #92400e;
        border-radius: 50px;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 700;
    }

    .btn-action-pdf {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fca5a5;
        background: #fef2f2;
        color: #dc2626;
        text-decoration: none;
        transition: all .2s;
        font-size: 12px;
    }

    .btn-action-pdf:hover {
        background: #dc2626;
        color: #fff;
        transform: scale(1.05);
    }

    .btn-action-session {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ede9fe;
        background: #f5f3ff;
        color: #7c3aed;
        border: none;
        cursor: pointer;
        transition: all .2s;
        font-size: 12px;
    }

    .btn-action-session:hover {
        background: #7c3aed;
        color: #fff;
        transform: scale(1.05);
    }

    /* WORK SESSION MODAL */
    .session-timeline {
        position: relative;
        padding-left: 20px;
        margin: 16px 0;
    }

    .session-timeline::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: #ede9fe;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 16px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -20px;
        top: 2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #7c3aed;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #7c3aed;
    }

    .timeline-dot.revisi {
        background: #d97706;
        box-shadow: 0 0 0 2px #d97706;
    }

    .timeline-content {
        background: #faf9ff;
        border: 1px solid #ede9fe;
        border-radius: 12px;
        padding: 12px;
    }

    .timeline-title {
        font-size: 12px;
        font-weight: 700;
        color: #4b5563;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .timeline-meta {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .timeline-notes {
        font-size: 12px;
        color: #1f2937;
        background: #fff;
        border: 1px solid #f3f4f6;
        border-radius: 8px;
        padding: 8px 10px;
        margin-top: 8px;
    }

    /* PRINT STYLING */
    @media print {
        .app-header, .sidebar, .footer, .kpi-back-btn, .kpi-action-btns, .filter-card, .btn-action-pdf, .btn-action-session {
            display: none !important;
        }
        .kpi-container {
            padding: 0;
        }
        .kpi-hero {
            box-shadow: none;
            background: #fff !important;
            color: #000 !important;
            border: 1px solid #ddd;
        }
        .kpi-hero h4, .kpi-hero p {
            color: #000 !important;
        }
        .table-container {
            border: 1px solid #ddd;
            box-shadow: none;
        }
    }
    /* MODAL Z-INDEX FIX — mencegah backdrop menghalangi modal */
    body.modal-open .modal {
        z-index: 1055 !important;
    }
    body.modal-open .modal-backdrop {
        z-index: 1050 !important;
    }
    #modalWorkSessions {
        z-index: 1060 !important;
    }
</style>
@endsection
@section('content_top')
<div class="kpi-container">
    <!-- HERO PROFILE BANNER -->
    <div class="kpi-hero">
        <div class="kpi-hero-top">
            <a href="{{ route('approval.ticket.pic.index', $menu_id ?? 0) }}" class="kpi-back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman PIC
            </a>
            <div class="kpi-action-btns">
                <button type="button" class="kpi-hero-btn" onclick="window.print()" title="Cetak Laporan">
                    <i class="fa-solid fa-print"></i> Cetak Laporan
                </button>
                <button type="button" class="kpi-hero-btn" id="btn-refresh-kpi" title="Segarkan Data">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
            </div>
        </div>
        <div class="kpi-profile-box">
            <div class="kpi-avatar" id="hero-avatar">
                {{ strtoupper(substr($target_pic->name ?? ($user_karyawan->name ?? 'P'), 0, 2)) }}
            </div>
            <div class="kpi-profile-info">
                <h4 id="hero-pic-name">{{ $target_pic->name ?? ($user_karyawan->name ?? 'PIC Ticket Support') }}</h4>
                <p id="hero-pic-sub">
                    <i class="fa-solid fa-id-badge me-1"></i>NIK: {{ $target_pic->nomor_identitas_karyawan ?? ($user_karyawan->nomor_identitas_karyawan ?? '-') }}
                </p>
                <div class="kpi-badge-role">
                    <i class="fa-solid fa-shield-halved"></i> PIC Ticket Support
                </div>
            </div>
        </div>
    </div>
    <!-- SUMMARY METRICS CARDS -->
    <div class="summary-grid" id="kpi-summary-grid">
        <!-- 1. Total Ticket Selesai -->
        <div class="stat-card">
            <div class="stat-card-head">
                <div class="stat-card-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
                <span class="badge-sla-ontime" id="stat-ontime-rate"><i class="fa-solid fa-bolt"></i> 100% On-Time</span>
            </div>
            <div class="stat-card-val" id="stat-total-closed">
                <span class="skeleton" style="width:50px; height:24px; display:inline-block;"></span>
            </div>
            <div class="stat-card-label">Tiket Selesai (Closed)</div>
            <div class="stat-card-sub" id="stat-overrun-info">
                <i class="fa-solid fa-circle-info"></i> 0 Overrun SLA
            </div>
        </div>
        <!-- 2. Tiket Aktif / Dalam Proses -->
        <div class="stat-card">
            <div class="stat-card-head">
                <div class="stat-card-icon icon-amber"><i class="fa-solid fa-hourglass-half"></i></div>
                <span class="badge-priority"><i class="fa-solid fa-bars-progress"></i> Aktif</span>
            </div>
            <div class="stat-card-val" id="stat-active-tickets">
                <span class="skeleton" style="width:50px; height:24px; display:inline-block;"></span>
            </div>
            <div class="stat-card-label">Tiket Dalam Pengerjaan</div>
            <div class="stat-card-sub" id="stat-active-sub">
                Sedang dikerjakan / review
            </div>
        </div>
        <!-- 3. Rata-rata Durasi Pengerjaan -->
        <div class="stat-card">
            <div class="stat-card-head">
                <div class="stat-card-icon icon-blue"><i class="fa-solid fa-clock"></i></div>
                <span class="badge-priority">Avg Duration</span>
            </div>
            <div class="stat-card-val" id="stat-avg-hours">
                <span class="skeleton" style="width:50px; height:24px; display:inline-block;"></span>
            </div>
            <div class="stat-card-label">Rata-rata Waktu Proses</div>
            <div class="stat-card-sub">
                Menit per tiket selesai
            </div>
        </div>
        <!-- 4. Skor Efisiensi KPI -->
        <div class="stat-card">
            <div class="stat-card-head">
                <div class="stat-card-icon icon-purple"><i class="fa-solid fa-gauge-high"></i></div>
                <span class="badge-priority">Target vs Aktual</span>
            </div>
            <div class="stat-card-val" id="stat-efficiency" style="color:#7c3aed;">
                <span class="skeleton" style="width:50px; height:24px; display:inline-block;"></span>
            </div>
            <div class="stat-card-label">Skor Efisiensi Waktu</div>
            <div class="stat-card-sub" id="stat-efficiency-sub">
                Rasio SLA / Menit Aktual
            </div>
        </div>

        <!-- 5. Kepatuhan SLA (On-Time) -->
        <div class="stat-card">
            <div class="stat-card-head">
                <div class="stat-card-icon icon-indigo"><i class="fa-solid fa-bullseye"></i></div>
                <span class="badge-priority">Kepatuhan SLA</span>
            </div>
            <div class="stat-card-val" id="stat-ontime-count">
                <span class="skeleton" style="width:50px; height:24px; display:inline-block;"></span>
            </div>
            <div class="stat-card-label">Tiket Tepat Waktu</div>
            <div class="stat-card-sub" id="stat-ontime-sub">
                Sesuai batas SLA
            </div>
        </div>

        <!-- 6. Total Feedback / Revisi -->
        <div class="stat-card">
            <div class="stat-card-head">
                <div class="stat-card-icon icon-rose"><i class="fa-solid fa-rotate-right"></i></div>
                <span class="badge-priority">Siklus Revisi</span>
            </div>
            <div class="stat-card-val" id="stat-feedback-cycles">
                <span class="skeleton" style="width:50px; height:24px; display:inline-block;"></span>
            </div>
            <div class="stat-card-label">Total Respon Balasan</div>
            <div class="stat-card-sub">
                Dari pemohon tiket
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="filter-card">
        <div class="kpi-tabs">
            <button type="button" class="kpi-tab active" data-filter="all">
                <i class="fa-solid fa-list"></i> Semua (<span id="count-all">0</span>)
            </button>
            <button type="button" class="kpi-tab" data-filter="ontime">
                <i class="fa-solid fa-circle-check text-success"></i> Tepat Waktu (<span id="count-ontime">0</span>)
            </button>
            <button type="button" class="kpi-tab" data-filter="overrun">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i> Melebihi SLA (<span id="count-overrun">0</span>)
            </button>
            <button type="button" class="kpi-tab" data-filter="feedback">
                <i class="fa-solid fa-arrows-rotate text-warning"></i> Ada Revisi (<span id="count-feedback">0</span>)
            </button>
            <button type="button" class="kpi-tab" data-filter="active">
                <i class="fa-solid fa-play text-primary"></i> Tiket Aktif (<span id="count-active">0</span>)
            </button>
        </div>

        <div class="filter-inputs">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="search-input" id="search-ticket" placeholder="Cari nomor ticket, menu...">
            </div>
        </div>
    </div>

    <!-- DATA TABLE LIST -->
    <div class="table-container">
        <div style="overflow-x: auto;">
            <table class="kpi-table" id="kpi-tickets-table">
                <thead>
                    <tr>
                        <th>Nomor Ticket &amp; Modul</th>
                        <th>Pengaju</th>
                        <th>Tingkat Kesulitan</th>
                        <th>Target SLA (Menit)</th>
                        <th>Waktu Aktual (Menit)</th>
                        <th>Varians Waktu</th>
                        <th>Status SLA</th>
                        <th>Siklus Revisi</th>
                        <th>Tanggal Selesai</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kpi-tickets-tbody">
                    <tr>
                        <td colspan="10" style="text-align:center; padding: 30px;">
                            <span class="skeleton" style="width:80%; height:18px; display:inline-block;"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL DETAIL SESI KERJA (WORK SESSIONS) -->
<div class="modal fade" id="modalWorkSessions" tabindex="-1" aria-labelledby="modalWorkSessionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
        <div class="modal-content" style="border-radius:18px; border:none; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed, #a855f7); color:#fff; padding:16px 20px;">
                <div>
                    <h6 class="modal-title fw-bold" id="modalWorkSessionsLabel" style="color:#fff; font-size:15px;">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>Histori Sesi Kerja PIC
                    </h6>
                    <small style="font-size:11px; opacity:.9;" id="modal-ticket-nomor">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: brightness(0) invert(1); opacity:.8;"></button>
            </div>
            <div class="modal-body" id="modal-sessions-body" style="padding:20px;">
                <!-- Diisi via JS -->
            </div>
            <div class="modal-footer" style="padding:12px 20px; border-top:1px solid #ede9fe; background:#faf9ff;">
                <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const PIC_ID = "{{ $picId ?? '' }}";
        const API_BASE_URL = "{{ url('approval/ticket/kpi') }}";
        let allTickets = [];
        let activeFilter = 'all';

        function loadKpiData() {
            if (!PIC_ID) {
                // If no specific PIC, load general summary
                loadGeneralKpi();
                return;
            }

            $.ajax({
                url: API_BASE_URL + '/' + PIC_ID,
                type: 'GET',
                success: function(res) {
                    if (res.code !== 200 || !res.data) {
                        $('#kpi-tickets-tbody').html('<tr><td colspan="10" style="text-align:center; padding:30px; color:#9ca3af;">Tidak ada data KPI.</td></tr>');
                        return;
                    }

                    const data = res.data;
                    renderSummary(data);
                    allTickets = data.tickets || [];
                    renderTicketsTable();
                },
                error: function(xhr) {
                    const msg = window.getFriendlyErrorMessage ? window.getFriendlyErrorMessage(xhr, 'Gagal memuat data KPI PIC.') : 'Gagal memuat data KPI PIC.';
                    $('#kpi-tickets-tbody').html(`<tr><td colspan="10" style="text-align:center; padding:30px; color:#ef4444;"><i class="fa-solid fa-circle-exclamation me-1"></i> ${msg}</td></tr>`);
                }
            });
        }

        function loadGeneralKpi() {
            $.ajax({
                url: API_BASE_URL,
                type: 'GET',
                success: function(res) {
                    if (res.code !== 200 || !res.data) {
                        $('#kpi-tickets-tbody').html('<tr><td colspan="10" style="text-align:center; padding:30px; color:#9ca3af;">Tidak ada data KPI.</td></tr>');
                        return;
                    }
                    const list = res.data;
                    let totalClosed = list.reduce((sum, s) => sum + (s.total_tickets || 0), 0);
                    let avgHours = list.length > 0 ? (list.reduce((sum, s) => sum + (s.avg_processing_hours || 0), 0) / list.length) : 0;
                    let avgEff = list.length > 0 ? (list.reduce((sum, s) => sum + (s.efficiency_score || 0), 0) / list.length) : 0;

                    $('#stat-total-closed').text(totalClosed);
                    $('#stat-avg-hours').text(avgHours.toFixed(2) + ' Jam');
                    $('#stat-efficiency').text(avgEff.toFixed(1) + '%');

                    let html = '';
                    list.forEach(function(s) {
                        html += `
                            <tr>
                                <td colspan="3">
                                    <div class="fw-bold" style="color:#7c3aed;">${escHtml(s.pic_name || 'PIC #' + s.pic_id)}</div>
                                    <small class="text-muted">NIK: ${escHtml(s.pic_nik || '-')}</small>
                                </td>
                                <td class="fw-bold">${s.total_tickets || 0} Tiket Selesai</td>
                                <td class="fw-bold">${s.avg_processing_hours ? s.avg_processing_hours.toFixed(2) + ' Jam' : '-'}</td>
                                <td>${s.efficiency_score ? s.efficiency_score.toFixed(1) + '%' : '-'}</td>
                                <td>
                                    ${(s.overrun_count || 0) > 0 
                                        ? `<span class="badge-sla-overrun">${s.overrun_count} Overrun</span>`
                                        : `<span class="badge-sla-ontime">100% On-Time</span>`
                                    }
                                </td>
                                <td>${s.total_feedback_cycles || 0} Siklus</td>
                                <td>-</td>
                                <td style="text-align:center;">
                                    <a href="{{ url('approval/ticket/kpi/dashboard') }}/${s.pic_id}" class="btn btn-sm btn-primary rounded-3" style="font-size:11px; font-weight:700;">
                                        <i class="fa-solid fa-chart-pie me-1"></i> Buka Report
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    $('#kpi-tickets-tbody').html(html);
                },
                error: function(xhr) {
                    const msg = window.getFriendlyErrorMessage ? window.getFriendlyErrorMessage(xhr, 'Gagal memuat data KPI.') : 'Gagal memuat data KPI.';
                    $('#kpi-tickets-tbody').html(`<tr><td colspan="10" style="text-align:center; padding:30px; color:#ef4444;"><i class="fa-solid fa-circle-exclamation me-1"></i> ${msg}</td></tr>`);
                }
            });
        }

        function renderSummary(data) {
            // Update Profile Name if available
            if (data.pic_name) {
                $('#hero-pic-name').text(data.pic_name);
                const initials = data.pic_name.split(' ').map(w => w[0]).filter(Boolean).slice(0, 2).join('').toUpperCase();
                $('#hero-avatar').text(initials || 'PI');
            }
            if (data.pic_nik) {
                $('#hero-pic-sub').html(`<i class="fa-solid fa-id-badge me-1"></i>NIK: ${data.pic_nik}`);
            }

            // Stat Cards
            $('#stat-total-closed').text(data.total_tickets || 0);
            $('#stat-ontime-rate').html(`<i class="fa-solid fa-bolt"></i> ${(data.ontime_rate || 100).toFixed(1)}% On-Time`);
            $('#stat-overrun-info').html(`<i class="fa-solid fa-circle-info text-danger"></i> ${data.overrun_count || 0} Tiket Melebihi SLA`);

            $('#stat-active-tickets').text(data.active_tickets || 0);
            $('#stat-active-sub').text(`${data.in_progress_tickets || 0} Dikerjakan, ${data.waiting_review || 0} Review`);

            const avgM = data.avg_processing_hours || 0;
            const avgStr = avgM < 60 ? `${avgM.toFixed(1)} Menit` : `${avgM.toFixed(0)} Menit (~${(avgM / 60).toFixed(1)} Jam)`;
            $('#stat-avg-hours').text(avgStr);

            const effScore = data.efficiency_score || 0;
            let effColor = '#15803d'; // Green
            if (effScore < 80) effColor = '#dc2626'; // Red
            else if (effScore < 100) effColor = '#d97706'; // Amber

            $('#stat-efficiency').text(effScore.toFixed(1) + '%').css('color', effColor);
            $('#stat-efficiency-sub').text(effScore >= 100 ? 'Sangat Efisien (Cepat)' : 'Memerlukan Optimasi');

            $('#stat-ontime-count').text(data.ontime_count || 0);
            $('#stat-ontime-sub').text(`Dari total ${data.total_tickets || 0} tiket selesai`);

            $('#stat-feedback-cycles').text(data.total_feedback_cycles || 0);
        }

        function renderTicketsTable() {
            let filtered = allTickets;

            // Filter status
            if (activeFilter === 'ontime') {
                filtered = filtered.filter(t => !t.overrun_flag && t.ticket_status === 'T06');
            } else if (activeFilter === 'overrun') {
                filtered = filtered.filter(t => t.overrun_flag && t.ticket_status === 'T06');
            } else if (activeFilter === 'feedback') {
                filtered = filtered.filter(t => (t.feedback_cycles || 0) > 0);
            } else if (activeFilter === 'active') {
                filtered = filtered.filter(t => t.ticket_status !== 'T06' && t.ticket_status !== 'T08');
            }

            // Search query
            const q = ($('#search-ticket').val() || '').trim().toLowerCase();
            if (q) {
                filtered = filtered.filter(t => 
                    (t.nomor_ticket || '').toLowerCase().includes(q) ||
                    (t.menu_name || '').toLowerCase().includes(q) ||
                    (t.submenu_name || '').toLowerCase().includes(q) ||
                    (t.requestor_name || '').toLowerCase().includes(q)
                );
            }

            // Update Counts in tabs
            $('#count-all').text(allTickets.length);
            $('#count-ontime').text(allTickets.filter(t => !t.overrun_flag && t.ticket_status === 'T06').length);
            $('#count-overrun').text(allTickets.filter(t => t.overrun_flag && t.ticket_status === 'T06').length);
            $('#count-feedback').text(allTickets.filter(t => (t.feedback_cycles || 0) > 0).length);
            $('#count-active').text(allTickets.filter(t => t.ticket_status !== 'T06' && t.ticket_status !== 'T08').length);

            if (filtered.length === 0) {
                $('#kpi-tickets-tbody').html(`
                    <tr>
                        <td colspan="10" style="text-align:center; padding:40px 20px; color:#9ca3af;">
                            <i class="fa-regular fa-folder-open mb-2" style="font-size:32px; display:block; color:#c4b5fd;"></i>
                            Tidak ada ticket yang sesuai dengan filter.
                        </td>
                    </tr>
                `);
                return;
            }

            const formatMins = function(m) {
                if (m === null || m === undefined) return '-';
                const mins = Number(m);
                if (isNaN(mins)) return '-';
                if (mins < 60) return `${mins.toFixed(mins % 1 === 0 ? 0 : 1)} Menit`;
                return `${mins.toFixed(mins % 1 === 0 ? 0 : 1)} Mnt (~${(mins / 60).toFixed(1)} Jam)`;
            };

            let html = '';
            filtered.forEach(function(t, idx) {
                const isOverrun = t.overrun_flag;
                const isClosed = t.ticket_status === 'T06';
                
                // Variance display
                // Di DB: variance_hours = actual_hours - expected_hours (dalam menit)
                // Negatif = aktual lebih cepat dari SLA (HEMAT), Positif = melebihi SLA (OVERRUN)
                let varianceHtml = '<span class="text-muted">—</span>';
                if (t.variance_hours !== null && isClosed) {
                    const absVariance = Math.abs(t.variance_hours).toFixed(t.variance_hours % 1 === 0 ? 0 : 1);
                    const varStr = Math.abs(t.variance_hours) < 60 ? `${absVariance} Menit` : `${absVariance} Mnt (~${(Math.abs(t.variance_hours) / 60).toFixed(1)} Jam)`;
                    if (isOverrun) {
                        // Aktual > Expected → Melebihi SLA
                        varianceHtml = `<span class="text-danger fw-bold"><i class="fa-solid fa-arrow-trend-up"></i> +${varStr} (Overrun)</span>`;
                    } else if (Math.abs(t.variance_hours) < 0.01) {
                        varianceHtml = `<span class="text-primary fw-bold">0 Menit (Pas)</span>`;
                    } else {
                        // Aktual < Expected → Lebih cepat dari SLA (HEMAT)
                        varianceHtml = `<span class="text-success fw-bold"><i class="fa-solid fa-arrow-trend-down"></i> -${varStr} (Hemat)</span>`;
                    }
                }

                // SLA Badge
                let slaBadge = '';
                if (!isClosed) {
                    slaBadge = `<span class="badge bg-warning text-dark"><i class="fa-solid fa-spinner fa-spin me-1"></i> ${escHtml(t.status_label || 'Aktif')}</span>`;
                } else if (isOverrun) {
                    slaBadge = `<span class="badge-sla-overrun"><i class="fa-solid fa-triangle-exclamation"></i> Melebihi SLA</span>`;
                } else {
                    slaBadge = `<span class="badge-sla-ontime"><i class="fa-solid fa-circle-check"></i> Tepat Waktu</span>`;
                }

                const expectedDisplay = t.expected_hours !== null ? formatMins(t.expected_hours) : '-';
                const actualDisplay = t.actual_hours !== null ? formatMins(t.actual_hours) : (isClosed ? '0 Menit' : '<span class="text-muted italic">Berjalan</span>');

                html += `
                    <tr>
                        <td>
                            <div class="fw-bold" style="color:#7c3aed;">${escHtml(t.nomor_ticket || '-')}</div>
                            <small class="text-muted">${escHtml(t.menu_name || '')} ${t.submenu_name ? '• ' + escHtml(t.submenu_name) : ''}</small>
                        </td>
                        <td>
                            <div class="fw-bold">${escHtml(t.requestor_name || '-')}</div>
                        </td>
                        <td>
                            <span class="badge-priority"><i class="fa-solid fa-layer-group text-primary"></i> ${escHtml(t.difficulty_name || t.priority || '-')}</span>
                        </td>
                        <td class="fw-bold">${expectedDisplay}</td>
                        <td class="fw-bold">${actualDisplay}</td>
                        <td>${varianceHtml}</td>
                        <td>${slaBadge}</td>
                        <td>
                            ${(t.feedback_cycles || 0) > 0
                                ? `<span class="badge-feedback"><i class="fa-solid fa-arrows-rotate"></i> ${t.feedback_cycles}x Revisi</span>`
                                : `<span class="text-muted" style="font-size:11px;">Tanpa Revisi</span>`
                            }
                        </td>
                        <td>
                            <div style="font-size:11px;">${t.closed_at || t.finish_at || t.created_at || '-'}</div>
                        </td>
                        <td style="text-align: center;">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn-action-session btn-view-sessions" data-index="${idx}" title="Lihat Sesi Kerja">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                                <a href="${t.pdf_url || ('{{ url("/ticket/export") }}/' + t.id)}" target="_blank" class="btn-action-pdf" title="Download / Cetak PDF Ticket">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#kpi-tickets-tbody').html(html);
        }

        // TAB FILTER CLICK
        $(document).on('click', '.kpi-tab', function() {
            $('.kpi-tab').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            renderTicketsTable();
        });

        // LIVE SEARCH
        $('#search-ticket').on('input', function() {
            renderTicketsTable();
        });

        // REFRESH BUTTON
        $('#btn-refresh-kpi').on('click', function() {
            $('#kpi-tickets-tbody').html('<tr><td colspan="10" style="text-align:center; padding:30px;"><span class="skeleton" style="width:80%; height:18px; display:inline-block;"></span></td></tr>');
            loadKpiData();
        });

        // VIEW WORK SESSIONS MODAL
        $(document).on('click', '.btn-view-sessions', function() {
            const idx = $(this).data('index');
            const t = allTickets[idx];
            if (!t) return;

            const formatMins = function(m) {
                if (m === null || m === undefined) return '-';
                const mins = Number(m);
                if (isNaN(mins)) return '-';
                if (mins < 60) return `${mins.toFixed(mins % 1 === 0 ? 0 : 1)} Menit`;
                return `${mins.toFixed(mins % 1 === 0 ? 0 : 1)} Mnt (~${(mins / 60).toFixed(1)} Jam)`;
            };

            $('#modal-ticket-nomor').text(`Nomor: ${t.nomor_ticket} • ${t.menu_name}`);
            
            let bodyHtml = `
                <div class="mb-3 p-3" style="background:#f5f3ff; border-radius:12px; border:1px solid #ede9fe;">
                    <div class="d-flex justify-content-between mb-1" style="font-size:12px;">
                        <span class="text-muted">Target SLA:</span>
                        <span class="fw-bold">${t.expected_hours !== null ? formatMins(t.expected_hours) : '-'}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:12px;">
                        <span class="text-muted">Total Waktu Aktual:</span>
                        <span class="fw-bold text-primary">${t.actual_hours !== null ? formatMins(t.actual_hours) : '-'}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:12px;">
                        <span class="text-muted">Status Kepatuhan:</span>
                        <span>${t.overrun_flag ? '<span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Melebihi SLA</span>' : '<span class="text-success fw-bold"><i class="fa-solid fa-circle-check"></i> Sesuai SLA</span>'}</span>
                    </div>
                </div>
                <h6 class="fw-bold text-muted mb-2" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">
                    <i class="fa-solid fa-timeline me-1"></i> Timeline Sesi Pengerjaan
                </h6>
            `;

            if (t.sessions && t.sessions.length > 0) {
                bodyHtml += `<div class="session-timeline">`;
                t.sessions.forEach(function(s) {
                    const isRevisi = s.session_type === 'revisi';
                    const durMins = Number(s.duration_minutes || 0);
                    const durDisplay = durMins < 60 ? `${durMins.toFixed(durMins % 1 === 0 ? 0 : 1)} Menit` : `${durMins.toFixed(durMins % 1 === 0 ? 0 : 1)} Mnt (~${(durMins / 60).toFixed(1)} Jam)`;
                    bodyHtml += `
                        <div class="timeline-item">
                            <div class="timeline-dot ${isRevisi ? 'revisi' : ''}"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">
                                    <span>${isRevisi ? '<i class="fa-solid fa-arrows-rotate text-warning me-1"></i> Siklus #' + s.cycle_number + ' (Pengerjaan Revisi)' : '<i class="fa-solid fa-play text-primary me-1"></i> Siklus #1 (Pengerjaan Awal)'}</span>
                                    <span class="badge ${isRevisi ? 'bg-warning text-dark' : 'bg-primary'}">${durDisplay}</span>
                                </div>
                                <div class="timeline-meta">
                                    <i class="fa-solid fa-clock me-1"></i> ${s.started_at || '-'} s/d ${s.ended_at || 'Sekarang'}
                                </div>
                                ${s.notes ? `<div class="timeline-notes"><strong>Catatan:</strong> ${escHtml(s.notes)}</div>` : ''}
                            </div>
                        </div>
                    `;
                });
                bodyHtml += `</div>`;
            } else {
                bodyHtml += `
                    <div class="text-center py-4 text-muted" style="font-size:12px;">
                        <i class="fa-regular fa-clock mb-2" style="font-size:28px; display:block; color:#c4b5fd;"></i>
                        Belum ada sesi kerja tercatat untuk tiket ini.
                    </div>
                `;
            }

            $('#modal-sessions-body').html(bodyHtml);
            // Pastikan modal ada di level body untuk menghindari masalah z-index/overflow
            var $modal = $('#modalWorkSessions');
            if ($modal.parent()[0] !== document.body) {
                $modal.appendTo('body');
            }
            var modalEl = document.getElementById('modalWorkSessions');
            var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            bsModal.show();
        });

        function escHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Initialize
        loadKpiData();
    });
</script>
@endsection
