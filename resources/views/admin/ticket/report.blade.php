@extends('admin.layouts.dashboard')

@section('css')
    @include('css_baru')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .report-page-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card-item {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid #ede9fe;
            box-shadow: 0 2px 6px rgba(109, 70, 218, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109, 70, 218, 0.1);
        }

        .stat-card-item .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-card-item .stat-val {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-card-item .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        .stat-purple { background: #faf7ff; border-color: #ede9fe; }
        .stat-purple .stat-icon { background: #ede9fe; color: #7c3aed; }
        .stat-purple .stat-val { color: #5b21b6; }

        .stat-blue { background: #f0f9ff; border-color: #e0f2fe; }
        .stat-blue .stat-icon { background: #e0f2fe; color: #0284c7; }
        .stat-blue .stat-val { color: #0369a1; }

        .stat-amber { background: #fffbeb; border-color: #fef3c7; }
        .stat-amber .stat-icon { background: #fef3c7; color: #d97706; }
        .stat-amber .stat-val { color: #b45309; }

        .stat-green { background: #f0fdf4; border-color: #dcfce7; }
        .stat-green .stat-icon { background: #dcfce7; color: #16a34a; }
        .stat-green .stat-val { color: #15803d; }

        .stat-rose { background: #fff1f2; border-color: #ffe4e6; }
        .stat-rose .stat-icon { background: #ffe4e6; color: #e11d48; }
        .stat-rose .stat-val { color: #be123c; }

        /* Filter Box */
        .filter-container-box {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #ede9fe;
            box-shadow: 0 2px 6px rgba(109, 70, 218, 0.04);
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 700;
            color: #4c1d95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-control-filter, .form-select-filter {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .form-control-filter:focus, .form-select-filter:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        /* Status Pills */
        .status-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        .status-draft { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
        .status-pending, .status-waiting { background: #fff8e1; color: #b45309; border: 1px solid #fde68a; }
        .status-process, .status-ready { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .status-approved, .status-done { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .status-feedback, .status-warning { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .status-rejected { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }

        .btn-view-ticket {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ede9fe;
            color: #6d28d9;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-view-ticket:hover {
            background: #7c3aed;
            color: #ffffff;
            transform: scale(1.05);
        }

        #table_ticket_report thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            vertical-align: middle;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 10px 12px;
        }

        #table_ticket_report tbody td {
            font-size: 12px;
            vertical-align: middle;
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        #table_ticket_report tbody tr:hover {
            background-color: #faf7ff;
        }

        .dt-buttons .btn {
            font-size: 12px;
            border-radius: 6px !important;
            margin-right: 6px;
            font-weight: 600;
            padding: 6px 14px;
        }

        /* ================= Modal Detail Tiket — Enhanced ================= */
        #modalTicketDetail .modal-content {
            border-radius: 16px;
            overflow: hidden;
        }

        #modalTicketDetail .modal-header {
            background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 55%, #8b5cf6 100%);
            border-bottom: none;
            padding: 20px 24px;
        }

        #modalTicketDetail .modal-header .stat-icon {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }

        #modalTicketDetail .modal-header .modal-title,
        #modalTicketDetail .modal-header small {
            color: #ffffff !important;
        }

        #modalTicketDetail .modal-header small {
            opacity: 0.85;
        }

        #modalTicketDetail .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        #modalTicketDetail .modal-body {
            background: #f8f7ff;
        }

        #modalTicketDetail .modal-footer {
            border-top: 1px solid #ede9fe;
        }

        /* Info cards */
        .detail-info-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #ede9fe;
            padding: 14px 16px;
            height: 100%;
            box-shadow: 0 1px 3px rgba(109, 70, 218, 0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .detail-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(109, 70, 218, 0.1);
        }

        .detail-info-card .info-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .detail-info-card .info-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .detail-info-card .info-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.3;
            word-break: break-word;
        }

        .info-icon-purple { background: #ede9fe; color: #7c3aed; }
        .info-icon-blue   { background: #e0f2fe; color: #0284c7; }
        .info-icon-amber  { background: #fef3c7; color: #d97706; }
        .info-icon-green  { background: #dcfce7; color: #16a34a; }
        .info-icon-rose   { background: #ffe4e6; color: #e11d48; }
        .info-icon-teal   { background: #ccfbf1; color: #0d9488; }
        .info-icon-indigo { background: #e0e7ff; color: #4f46e5; }

        /* Status banner */
        .status-banner {
            background: #ffffff;
            border: 1px solid #ede9fe;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .status-banner .status-banner-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #94a3b8;
        }

        /* Tabs */
        #modalDetailTabs {
            border-bottom: 2px solid #ede9fe;
            margin-bottom: 18px;
            gap: 4px;
        }

        #modalDetailTabs .nav-link {
            border: none;
            font-size: 12.5px;
            font-weight: 700;
            color: #6b7280;
            padding: 10px 4px;
            margin-right: 22px;
            border-radius: 0;
            position: relative;
            background: transparent;
        }

        #modalDetailTabs .nav-link .badge-count {
            background: #ede9fe;
            color: #7c3aed;
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 20px;
            margin-left: 6px;
        }

        #modalDetailTabs .nav-link.active {
            color: #6d28d9;
        }

        #modalDetailTabs .nav-link.active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -2px;
            height: 3px;
            border-radius: 3px;
            background: #7c3aed;
        }

        #modalDetailTabs .nav-link i {
            margin-right: 5px;
        }

        /* Rincian table */
        #table-modal-details thead th {
            background: #f5f3ff;
            color: #5b21b6;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 700;
            border-bottom: 2px solid #ede9fe;
        }

        #table-modal-details tbody td {
            font-size: 12px;
            vertical-align: middle;
        }

        #table-modal-details tbody tr:hover {
            background: #faf7ff;
        }

        #table-modal-details code {
            background: #fff1f2;
            color: #be123c;
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 11px;
        }

        #table-modal-details .value-after {
            background: #f0fdf4;
            color: #15803d;
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        /* Timeline enhanced */
        .timeline-item {
            position: relative;
            padding-left: 34px;
            margin-bottom: 18px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 24px;
            bottom: -18px;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            background: #16a34a;
            box-shadow: 0 0 0 3px #dcfce7;
        }

        .timeline-dot.pending {
            background: #cbd5e1;
            box-shadow: 0 0 0 3px #f1f5f9;
            color: #64748b;
        }

        .timeline-dot.rejected {
            background: #e11d48;
            box-shadow: 0 0 0 3px #ffe4e6;
            color: #ffffff;
        }

        .timeline-dot.process {
            background: #0284c7;
            box-shadow: 0 0 0 3px #e0f2fe;
            color: #ffffff;
        }

        .timeline-item .timeline-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #1e293b;
        }

        .timeline-item .timeline-sub {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Attachments */
        .attachment-item {
            background: #ffffff;
            border: 1px solid #ede9fe;
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .attachment-item:hover {
            background: #7c3aed;
            border-color: #7c3aed;
            transform: translateX(2px);
        }

        .attachment-item:hover .attachment-name,
        .attachment-item:hover i {
            color: #ffffff !important;
        }

        .attachment-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #ede9fe;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .attachment-icon i {
            color: #7c3aed;
            font-size: 16px;
        }

        .attachment-name {
            font-size: 11.5px;
            font-weight: 600;
            color: #1e293b;
        }
        /* Comment timeline */
        .comment-timeline-item {
            position: relative;
            padding-left: 34px;
            margin-bottom: 16px;
        }

        .comment-timeline-item:last-child {
            margin-bottom: 0;
        }

        .comment-timeline-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 24px;
            bottom: -16px;
            width: 2px;
            background: #e2e8f0;
        }

        .comment-timeline-item:last-child::before {
            display: none;
        }

        .comment-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11px;
        }

        .comment-dot.dot-approve {
            background: #16a34a;
            box-shadow: 0 0 0 3px #dcfce7;
        }

        .comment-dot.dot-reject {
            background: #e11d48;
            box-shadow: 0 0 0 3px #ffe4e6;
        }

        .comment-dot.dot-comment {
            background: #0284c7;
            box-shadow: 0 0 0 3px #e0f2fe;
        }

        .comment-dot.dot-default {
            background: #94a3b8;
            box-shadow: 0 0 0 3px #f1f5f9;
        }

        .comment-bubble {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 4px;
        }

        .comment-bubble.bubble-approve {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .comment-bubble.bubble-reject {
            background: #fff1f2;
            border-color: #fecdd3;
        }

        .comment-bubble .comment-text {
            font-size: 12.5px;
            color: #334155;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .comment-meta {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .comment-action-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-approve {
            background: #dcfce7;
            color: #166534;
        }

        .badge-reject {
            background: #ffe4e6;
            color: #9f1239;
        }

        .badge-comment {
            background: #e0f2fe;
            color: #0369a1;
        }

        /* Generated Ticket Styles */
        .gen-status-badge {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .gen-awaiting {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .gen-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .gen-skipped {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .gen-failed {
            background: #ffe4e6;
            color: #be123c;
            border: 1px solid #fecdd3;
        }
    </style>

@endsection

@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y report-page-container">
        <!-- Breadcrumb / Header Title -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #4c1d95;">
                    <i class="mdi mdi-ticket-confirmation-outline me-2"></i>REPORT TIKET
                </h4>
                <p class="text-muted mb-0 small">Laporan keseluruhan data pengajuan tiket & riwayat ubah/hapus data</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-primary px-3 py-2" style="font-size: 12px;">
                    <i class="mdi mdi-office-building me-1"></i> Holding: <strong>{{ $holding->holding_name ?? 'Holding' }}</strong>
                </span>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="stat-grid">
            <div class="stat-card-item stat-purple">
                <div class="stat-icon">
                    <i class="mdi mdi-ticket-outline"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-total">0</div>
                    <div class="stat-label">Total Tiket</div>
                </div>
            </div>
            <div class="stat-card-item stat-amber">
                <div class="stat-icon">
                    <i class="mdi mdi-clock-outline"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-submitted">0</div>
                    <div class="stat-label">Diajukan / Validasi</div>
                </div>
            </div>
            <div class="stat-card-item stat-blue">
                <div class="stat-icon">
                    <i class="mdi mdi-progress-wrench"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-progress">0</div>
                    <div class="stat-label">Sedang Dikerjakan</div>
                </div>
            </div>
            <div class="stat-card-item stat-green">
                <div class="stat-icon">
                    <i class="mdi mdi-check-circle-outline"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-completed">0</div>
                    <div class="stat-label">Selesai / Ditutup</div>
                </div>
            </div>
            <div class="stat-card-item stat-rose">
                <div class="stat-icon">
                    <i class="mdi mdi-close-circle-outline"></i>
                </div>
                <div>
                    <div class="stat-val" id="stat-cancelled">0</div>
                    <div class="stat-label">Ditolak / Batal</div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-container-box">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-filter-variant text-primary" style="font-size: 18px;"></i>
                    <span class="fw-bold text-dark" style="font-size: 13px;">FILTER PENCARIAN TIKET</span>
                </div>
                <small class="text-muted">Gunakan filter untuk menyaring data spesifik</small>
            </div>

            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">
                        <i class="mdi mdi-calendar-range"></i> Dari Tanggal
                    </label>
                    <input type="date" id="filter_start_date" class="form-control form-control-sm form-control-filter">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">
                        <i class="mdi mdi-calendar-range"></i> Sampai Tanggal
                    </label>
                    <input type="date" id="filter_end_date" class="form-control form-control-sm form-control-filter">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">
                        <i class="mdi mdi-domain"></i> Holding
                    </label>
                    <select id="filter_holding" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Holding --</option>
                        @foreach($holdings as $h)
                            <option value="{{ $h->holding_code }}" {{ $h->holding_code === $holding->holding_code ? 'selected' : '' }}>
                                {{ $h->holding_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">
                        <i class="mdi mdi-office-building"></i> Departemen
                    </label>
                    <select id="filter_departemen" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Departemen --</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">
                        <i class="mdi mdi-list-status"></i> Status Tiket
                    </label>
                    <select id="filter_status" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Status --</option>
                        <option value="T00">Draft</option>
                        <option value="T01">Menunggu Persetujuan</option>
                        <option value="T02">Disetujui</option>
                        <option value="T03">Ditugaskan</option>
                        <option value="T04">Dikerjakan</option>
                        <option value="T05">Menunggu Respon Peminta</option>
                        <option value="T06">Selesai</option>
                        <option value="T07">Respon Balasan Peminta</option>
                        <option value="T08">Dibatalkan / Ditolak</option>
                        <option value="T09">Disetujui Atasan 1</option>
                        <option value="T10">Disetujui Atasan 2</option>
                        <option value="T11">Menunggu Divalidasi</option>
                        <option value="T12">Menunggu Pemeriksaan</option>
                        <option value="T13">Menunggu Ditugaskan</option>
                        <option value="T14">PENDING</option>
                        <option value="T15">Tutup Otomatis Oleh Sistem</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="filter-label">
                        <i class="mdi mdi-server-network"></i> Sistem
                    </label>
                    <select id="filter_kategori" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Sistem --</option>
                        <option value="1">Epicor ERP</option>
                        <option value="2">Accurate Online SP</option>
                        <option value="3">Accurate Online AJA</option>
                    </select>
                </div>
                <div class="col-md-6 col-12 d-flex align-items-end gap-2">
                    <button type="button" id="btn-apply-filter" class="btn btn-primary btn-sm px-4 fw-bold flex-grow-1" style="height: 38px;">
                        <i class="mdi mdi-magnify me-1"></i> Cari / Tampilkan Data
                    </button>
                    <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary btn-sm px-3 fw-bold" style="height: 38px;">
                        <i class="mdi mdi-refresh me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Data Table Card -->
        <div class="card master-table-card border shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-table-large text-primary" style="font-size: 20px;"></i>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 14px;">DAFTAR KESELURUHAN TIKET</h5>
                    <span id="badge-total-rows" class="badge bg-light text-primary border ms-2">0 Detail Data</span>
                </div>
                <div id="table-buttons-slot" class="d-flex align-items-center gap-2"></div>
            </div>

            <div class="card-body p-3">
                <div id="empty-state" class="text-center py-5 text-muted">
                    <i class="mdi mdi-filter-check-outline mb-2" style="font-size: 48px; color: #c4b5fd;"></i>
                    <h6 class="fw-bold text-dark">Data Siap Ditampilkan</h6>
                    <p class="small text-muted mb-3">Klik tombol <strong>Cari / Tampilkan Data</strong> untuk memuat data laporan tiket.</p>
                </div>

                <div id="table-wrapper" style="display: none;">
                    <div class="table-responsive">
                        <table id="table_ticket_report" class="table table-hover align-middle w-100 table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tanggal</th>
                                    <th>Nomor Ticket</th>
                                    <th>Sistem</th>
                                    <th>Modul</th>
                                    <th>Sub Modul</th>
                                    <th>Status</th>
                                    <th>PIC</th>
                                    <th class="text-nowrap" title="Tanggal Divalidasi">Tgl Divalidasi</th>
                                    <th class="text-nowrap" title="Tanggal Ditugaskan oleh Assigner">Tgl Ditugaskan</th>
                                    <th class="text-nowrap" title="Tanggal Dikerjakan Pertama Kali oleh PIC">Tgl Mulai Dikerjakan</th>
                                    <th class="text-nowrap" title="Tanggal Terakhir Selesai Dikerjakan oleh PIC (Termasuk Revisi)">Tgl Selesai Dikerjakan</th>
                                    <th class="text-nowrap" title="Tanggal Closed (Selesai)">Tgl Closed</th>
                                    <th>Tgl Transaksi</th>
                                    <th>No. Dokumen</th>
                                    <th>Line Sub</th>
                                    <th>Alasan Ubah</th>
                                    <th>Sebelum</th>
                                    <th>Sesudah</th>
                                    <th>Departemen</th>
                                    <th>Pengaju</th>
                                    <th>Area / Site</th>
                                    <th>Holding</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Tiket -->
    <div class="modal fade" id="modalTicketDetail" tabindex="-1" aria-labelledby="modalTicketDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="mdi mdi-ticket-outline" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalTicketDetailLabel">Detail Tiket</h5>
                            <small id="modal-ticket-subtitle">-</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="modal-detail-loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted small">Memuat detail tiket...</p>
                    </div>

                    <div id="modal-detail-content" style="display: none;">
                        <!-- Status Banner -->
                        <div class="status-banner d-flex align-items-center gap-3 mb-4">
                            <span class="status-banner-label">Status Tiket Saat Ini</span>
                            <div id="det-status">-</div>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav" id="modalDetailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-info-btn" data-bs-toggle="tab" data-bs-target="#tab-info" type="button" role="tab" aria-selected="true">
                                    <i class="mdi mdi-information-outline"></i>Informasi Umum
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-rincian-btn" data-bs-toggle="tab" data-bs-target="#tab-rincian" type="button" role="tab" aria-selected="false">
                                    <i class="mdi mdi-format-list-bulleted"></i>Rincian Perubahan
                                    <span class="badge-count" id="rincian-count">0</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-approval-btn" data-bs-toggle="tab" data-bs-target="#tab-approval" type="button" role="tab" aria-selected="false">
                                    <i class="mdi mdi-timeline-check-outline"></i>Approval & Lampiran
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-comments-btn" data-bs-toggle="tab" data-bs-target="#tab-comments" type="button" role="tab" aria-selected="false">
                                    <i class="mdi mdi-comment-text-multiple-outline"></i>Riwayat Komentar
                                    <span class="badge-count" id="comments-count">0</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-generated-btn" data-bs-toggle="tab" data-bs-target="#tab-generated" type="button" role="tab" aria-selected="false">
                                    <i class="mdi mdi-source-branch"></i>Tiket Lanjutan
                                    <span class="badge-count" id="generated-count">0</span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Tab 1: Informasi Umum -->
                            <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-purple"><i class="mdi mdi-ticket-outline"></i></div>
                                            <div class="info-label">Nomor Tiket</div>
                                            <div class="info-value" id="det-nomor-ticket">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-blue"><i class="mdi mdi-calendar"></i></div>
                                            <div class="info-label">Tanggal Pengajuan</div>
                                            <div class="info-value" id="det-tanggal">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-amber"><i class="mdi mdi-account-circle"></i></div>
                                            <div class="info-label">Pengaju (Requestor)</div>
                                            <div class="info-value" id="det-pengaju">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-teal"><i class="mdi mdi-server-network"></i></div>
                                            <div class="info-label">Sistem / Kategori</div>
                                            <div class="info-value" id="det-sistem">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-purple"><i class="mdi mdi-briefcase-outline"></i></div>
                                            <div class="info-label">Jabatan</div>
                                            <div class="info-value" id="det-jabatan">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-indigo"><i class="mdi mdi-view-module"></i></div>
                                            <div class="info-label">Modul & Sub Modul</div>
                                            <div class="info-value" id="det-modul">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-rose"><i class="mdi mdi-office-building"></i></div>
                                            <div class="info-label">Departemen & Site</div>
                                            <div class="info-value" id="det-departemen">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-green"><i class="mdi mdi-account-tie"></i></div>
                                            <div class="info-label">PIC Pelaksana</div>
                                            <div class="info-value" id="det-pic">-</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6" id="box-parent-ticket" style="display: none;">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-blue"><i class="mdi mdi-arrow-up-bold-box-outline"></i></div>
                                            <div class="info-label">Parent Ticket (Sebelumnya)</div>
                                            <div class="info-value" id="det-parent-ticket">-</div>
                                            <small class="text-muted d-block mt-1" id="det-parent-ticket-sub" style="font-size: 11px;"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6" id="box-root-ticket" style="display: none;">
                                        <div class="detail-info-card">
                                            <div class="info-icon info-icon-teal"><i class="mdi mdi-source-branch"></i></div>
                                            <div class="info-label">Root Ticket (Dasar Pengajuan)</div>
                                            <div class="info-value" id="det-root-ticket">-</div>
                                            <small class="text-muted d-block mt-1" id="det-root-ticket-sub" style="font-size: 11px;"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Rincian Perubahan -->
                            <div class="tab-pane fade" id="tab-rincian" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0" id="table-modal-details">
                                        <thead>
                                            <tr>
                                                <th>Tgl Transaksi</th>
                                                <th>No. Dokumen</th>
                                                <th>Sub Line</th>
                                                <th>Alasan Perubahan</th>
                                                <th>Nilai Sebelum</th>
                                                <th>Nilai Sesudah</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 3: Approval & Lampiran -->
                            <div class="tab-pane fade" id="tab-approval" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 12.5px;">
                                            <i class="mdi mdi-timeline-clock-outline me-1 text-primary"></i> ALUR PERSETUJUAN
                                        </h6>
                                        <div id="det-approval-timeline" class="p-3 bg-white rounded-3 border">
                                            <!-- Dynamic timeline -->
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <h6 class="fw-bold text-dark mb-3" style="font-size: 12.5px;">
                                            <i class="mdi mdi-paperclip me-1 text-primary"></i> LAMPIRAN DOKUMEN
                                        </h6>
                                        <div id="det-attachments" class="p-3 bg-white rounded-3 border" style="min-height: 120px;">
                                            <!-- Dynamic attachments -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Riwayat Komentar -->
                            <div class="tab-pane fade" id="tab-comments" role="tabpanel">
                                <h6 class="fw-bold text-dark mb-3" style="font-size: 12.5px;">
                                    <i class="mdi mdi-comment-text-multiple-outline me-1 text-primary"></i> RIWAYAT KOMENTAR & AKSI
                                </h6>
                                <div id="det-comments-timeline" class="p-3 bg-white rounded-3 border" style="min-height: 120px;">
                                    <!-- Dynamic comment timeline -->
                                </div>
                            </div>

                            <!-- Tab 5: Tiket Lanjutan -->
                            <div class="tab-pane fade" id="tab-generated" role="tabpanel">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 12.5px;">
                                        <i class="mdi mdi-source-branch me-1 text-primary"></i> DAFTAR TIKET LANJUTAN / GENERATED TICKET
                                    </h6>
                                    <small class="text-muted">Tiket turunan yang terbentuk otomatis dari pengecekan kondisi oleh Checker / SPC</small>
                                </div>
                                <div id="det-generated-container" class="p-3 bg-white rounded-3 border" style="min-height: 120px;">
                                    <!-- Dynamic generated tickets table -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi Select2
            $('#filter_departemen').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Semua Departemen --',
                allowClear: true,
                width: '100%'
            });

            loadDepartemenList();

            const envMap = {
                1: 'Epicor ERP',
                2: 'Accurate Online SP',
                3: 'Accurate Online AJA'
            };

            function getStatusLabel(ticketStatus, approvalStatus) {
                if (approvalStatus === 'rejected' || ticketStatus === 'T08') {
                    return 'Dibatalkan / Ditolak';
                }
                const map = {
                    T00: 'Draft',
                    T01: 'Menunggu Persetujuan',
                    T02: 'Disetujui',
                    T03: 'Ditugaskan',
                    T04: 'Dikerjakan',
                    T05: 'Menunggu Respon Peminta',
                    T06: 'Selesai',
                    T07: 'Respon Balasan Peminta',
                    T08: 'Dibatalkan / Ditolak',
                    T09: 'Disetujui Atasan 1',
                    T10: 'Disetujui Atasan 2',
                    T11: 'Menunggu Divalidasi',
                    T12: 'Menunggu Pemeriksaan',
                    T13: 'Menunggu Ditugaskan',
                    T14: 'Pending',
                    T15: 'Tutup Otomatis Oleh Sistem'
                };
                return map[ticketStatus] || ticketStatus || '-';
            }

            function getStatusPill(ticketStatus, approvalStatus) {
                if (approvalStatus === 'rejected' || ticketStatus === 'T08') {
                    return '<span class="status-pill status-rejected"><i class="mdi mdi-close-circle-outline"></i> Ditolak</span>';
                }
                const map = {
                    T00: '<span class="status-pill status-draft"><i class="mdi mdi-pencil-outline"></i> Draft</span>',
                    T01: '<span class="status-pill status-pending"><i class="mdi mdi-clock-outline"></i> Menunggu Persetujuan</span>',
                    T02: '<span class="status-pill status-approved"><i class="mdi mdi-check-circle-outline"></i> Disetujui</span>',
                    T03: '<span class="status-pill status-process"><i class="mdi mdi-account-arrow-right-outline"></i> Ditugaskan</span>',
                    T04: '<span class="status-pill status-process"><i class="mdi mdi-progress-wrench"></i> Dikerjakan</span>',
                    T05: '<span class="status-pill status-waiting"><i class="mdi mdi-timer-sand"></i> Menunggu Respon Peminta</span>',
                    T06: '<span class="status-pill status-approved"><i class="mdi mdi-checkbox-marked-circle-outline"></i> Selesai</span>',
                    T07: '<span class="status-pill status-feedback"><i class="mdi mdi-comment-account-outline"></i> Respon Balasan</span>',
                    T08: '<span class="status-pill status-rejected"><i class="mdi mdi-cancel"></i> Dibatalkan</span>',
                    T09: '<span class="status-pill status-approved"><i class="mdi mdi-check-all"></i> Disetujui Atasan 1</span>',
                    T10: '<span class="status-pill status-approved"><i class="mdi mdi-check-all"></i> Disetujui Atasan 2</span>',
                    T11: '<span class="status-pill status-process"><i class="mdi mdi-clipboard-check-outline"></i> Menunggu Divalidasi</span>',
                    T12: '<span class="status-pill status-waiting"><i class="mdi mdi-file-search-outline"></i> Menunggu Pemeriksaan</span>',
                    T13: '<span class="status-pill status-process"><i class="mdi mdi-account-arrow-right-outline"></i> Menunggu Ditugaskan</span>',
                    T14: '<span class="status-pill status-pending"><i class="mdi mdi-clock-alert-outline"></i> PENDING</span>',
                    T15: '<span class="status-pill status-closed"><i class="mdi mdi-timer-off"></i> Ditutup Otomatis</span>'
                };
                return map[ticketStatus] || `<span class="status-pill status-draft">${ticketStatus || '-'}</span>`;
            }

            function formatDateTime(data) {
                if (!data || data === '-') return '-';
                const str = String(data).replace(' ', 'T');
                const d = new Date(str);
                const targetDate = isNaN(d.getTime()) ? new Date(data) : d;
                if (isNaN(targetDate.getTime())) return data;
                const b = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                const hh = String(targetDate.getHours()).padStart(2, '0');
                const mm = String(targetDate.getMinutes()).padStart(2, '0');
                return `${targetDate.getDate()} ${b[targetDate.getMonth()]} ${targetDate.getFullYear()} ${hh}:${mm}`;
            }

            let allData = [];
            let tableInst = null;

            function initTable() {
                if (tableInst) {
                    tableInst.destroy();
                    $('#table_ticket_report tbody').empty();
                }

                tableInst = $('#table_ticket_report').DataTable({
                    data: [],
                    columns: [
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1
                        },
                        {
                            data: 'created_at',
                            render: function (data) {
                                if (!data) return '-';
                                const d = new Date(data);
                                const b = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                                const hh = String(d.getHours()).padStart(2, '0');
                                const mm = String(d.getMinutes()).padStart(2, '0');
                                const ss = String(d.getSeconds()).padStart(2, '0');
                                return `${d.getDate()} ${b[d.getMonth()]} ${d.getFullYear()} ${hh}:${mm}:${ss}`;
                            }
                        },
                        {
                            data: 'nomor_ticket',
                            defaultContent: '-',
                            render: d => `<span class="fw-bold text-primary">${d ?? '-'}</span>`
                        },
                        {
                            data: 'ticket_menu',
                            render: (d, t, r) => envMap[r.ticket_kategori ?? r.ticket_menu?.kategori] ?? '-'
                        },
                        {
                            data: 'ticket_menu',
                            render: (d, t, r) => r.ticket_menu?.nama_menu ?? '-'
                        },
                        {
                            data: 'ticket_sub_menu',
                            render: (d, t, r) => r.ticket_sub_menu?.nama_sub_menu ?? '-'
                        },
                        {
                            data: 'ticket_status',
                            render: function (data, type, row) {
                                if (type === 'export' || type === 'sort' || type === 'filter') {
                                    return getStatusLabel(data, row.approval_status);
                                }
                                return getStatusPill(data, row.approval_status);
                            }
                        },
                        {
                            data: 'pic_name',
                            render: d => (d && d !== '-')
                                ? `<span class="badge bg-label-info text-info fw-bold px-2 py-1"><i class="mdi mdi-account-tie me-1"></i>${d}</span>`
                                : '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'validator_at',
                            defaultContent: '-',
                            render: function (data, type) {
                                if (!data || data === '-') return type === 'sort' ? '' : '-';
                                if (type === 'sort') return data;
                                return formatDateTime(data);
                            }
                        },
                        {
                            data: 'assign_at',
                            defaultContent: '-',
                            render: function (data, type) {
                                if (!data || data === '-') return type === 'sort' ? '' : '-';
                                if (type === 'sort') return data;
                                return formatDateTime(data);
                            }
                        },
                        {
                            data: 'pic_started_at',
                            defaultContent: '-',
                            render: function (data, type) {
                                if (!data || data === '-') return type === 'sort' ? '' : '-';
                                if (type === 'sort') return data;
                                return formatDateTime(data);
                            }
                        },
                        {
                            data: 'finish_at',
                            defaultContent: '-',
                            render: function (data, type) {
                                if (!data || data === '-') return type === 'sort' ? '' : '-';
                                if (type === 'sort') return data;
                                return formatDateTime(data);
                            }
                        },
                        {
                            data: 'closed_at',
                            defaultContent: '-',
                            render: function (data, type) {
                                if (!data || data === '-') return type === 'sort' ? '' : '-';
                                if (type === 'sort') return data;
                                return formatDateTime(data);
                            }
                        },
                        {
                            data: 'tanggal_transaksi',
                            defaultContent: '-',
                            render: function (data) {
                                if (!data || data === '-') return '-';
                                const d = new Date(data);
                                if (isNaN(d.getTime())) return data;
                                const b = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                                return `${d.getDate()} ${b[d.getMonth()]} ${d.getFullYear()}`;
                            }
                        },
                        { data: 'nomor_detail',    defaultContent: '-' },
                        { data: 'line_sub_detail', defaultContent: '-' },
                        { data: 'alasan_ubah',     defaultContent: '-' },
                        { data: 'before_ubah',     defaultContent: '-' },
                        { data: 'after_ubah',      defaultContent: '-' },
                        { data: 'departemen',      defaultContent: '-' },
                        { data: 'requestor_name',  defaultContent: '-' },
                        { data: 'site_name',       defaultContent: '-' },
                        { data: 'holding_name',    defaultContent: '-' },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function (d, t, row) {
                                return `<button type="button" class="btn-view-ticket btn-open-detail" data-id="${row.id}" title="Lihat Detail">
                                            <i class="mdi mdi-eye-outline" style="font-size: 16px;"></i>
                                        </button>`;
                            }
                        },
                    ],
                    order: [],
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        emptyTable:   'Tidak ada data yang ditemukan',
                        zeroRecords:  'Data tidak cocok dengan pencarian',
                        search:       'Cari di tabel:',
                        lengthMenu:   'Tampilkan _MENU_ data',
                        info:         'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                        infoEmpty:    'Menampilkan 0 data',
                        infoFiltered: '(difilter dari _MAX_ total data)',
                        paginate: {
                            previous: '<i class="mdi mdi-chevron-left"></i>',
                            next:     '<i class="mdi mdi-chevron-right"></i>'
                        }
                    },
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-3"Bf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-3"ip>',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="mdi mdi-file-excel-outline me-1"></i> Export Excel',
                            className: 'btn btn-sm btn-success',
                            title: 'Report_Ticket_Admin_' + new Date().toISOString().slice(0,10),
                            exportOptions: {
                                columns: ':not(:last-child)',
                                format: {
                                    body: function (data) {
                                        return $('<div>').html(data).text().trim();
                                    }
                                }
                            }
                        },
                        {
                            extend: 'copyHtml5',
                            text: '<i class="mdi mdi-content-copy me-1"></i> Copy',
                            className: 'btn btn-sm btn-outline-primary',
                            exportOptions: {
                                columns: ':not(:last-child)',
                                format: {
                                    body: function (data) {
                                        return $('<div>').html(data).text().trim();
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="mdi mdi-printer-outline me-1"></i> Print',
                            className: 'btn btn-sm btn-outline-secondary',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ]
                });
            }

            function loadData() {
                const start       = $('#filter_start_date').val();
                const end         = $('#filter_end_date').val();
                const holdingCode = $('#filter_holding').val();
                const dept        = $('#filter_departemen').val();
                const status      = $('#filter_status').val();
                const kat         = $('#filter_kategori').val();

                $('#btn-apply-filter')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memuat Data...');

                $.ajax({
                    url: "{{ route('ticket.report.data.list') }}",
                    type: 'GET',
                    data: {
                        start_date:   start,
                        end_date:     end,
                        holding_code: holdingCode,
                        departemen:   dept,
                        status:       status,
                        kategori:     kat,
                    },
                    success: function (res) {
                        $('#btn-apply-filter')
                            .prop('disabled', false)
                            .html('<i class="mdi mdi-magnify me-1"></i> Cari / Tampilkan Data');

                        if (res.code === 200) {
                            allData = res.data || [];
                            // Update stats
                            const stats = res.stats || {};
                            $('#stat-total').text(stats.total || 0);
                            $('#stat-submitted').text(stats.submitted || 0);
                            $('#stat-progress').text(stats.in_progress || 0);
                            $('#stat-completed').text(stats.completed || 0);
                            $('#stat-cancelled').text(stats.cancelled || 0);
                            $('#badge-total-rows').text(`${allData.length} Detail Data`);
                            $('#empty-state').hide();
                            $('#table-wrapper').show();
                            if (!tableInst) initTable();
                            tableInst.clear().rows.add(allData).draw();

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal memuat data laporan tiket.'
                            });
                        }
                    },
                    error: function (xhr) {
                        $('#btn-apply-filter')
                            .prop('disabled', false)
                            .html('<i class="mdi mdi-magnify me-1"></i> Cari / Tampilkan Data');

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal memuat data dari server. Silakan coba kembali.'
                        });
                    }
                });
            }

            $('#btn-apply-filter').on('click', loadData);

            $('#btn-reset-filter').on('click', function () {
                $('#filter_start_date, #filter_end_date').val('');
                $('#filter_holding').val('{{ $holding->holding_code }}');
                $('#filter_departemen').val('').trigger('change');
                $('#filter_status, #filter_kategori').val('');

                $('#stat-total').text(0);
                $('#stat-submitted').text(0);
                $('#stat-progress').text(0);
                $('#stat-completed').text(0);
                $('#stat-cancelled').text(0);
                $('#badge-total-rows').text('0 Detail Data');

                allData = [];
                if (tableInst) {
                    tableInst.clear().draw();
                }
                $('#table-wrapper').hide();
                $('#empty-state').show();
            });

            $('#filter_start_date, #filter_end_date').on('keydown', function (e) {
                if (e.key === 'Enter') loadData();
            });

            function loadDepartemenList() {
                $.ajax({
                    url: "{{ route('ticket.report.departemen.list') }}",
                    type: 'GET',
                    success: function (res) {
                        if (res && res.code === 200) {
                            const sel = $('#filter_departemen');
                            sel.find('option:not(:first)').remove();

                            (res.data || []).forEach(dep => {
                                sel.append(`<option value="${dep.nama_departemen}">${dep.nama_departemen} (${dep.holding_name})</option>`);
                            });
                        }
                    },
                    error: function (xhr) {
                        console.warn('Gagal memuat daftar departemen', xhr);
                    }
                });
            }

            // Click View Detail Handler
            $(document).on('click', '.btn-open-detail', function () {
                const ticketId = $(this).data('id');
                if (!ticketId) return;

                $('#modalTicketDetail').modal('show');
                $('#modal-detail-loading').show();
                $('#modal-detail-content').hide();

                let url = "{{ route('ticket.report.detail', ':id') }}".replace(':id', ticketId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (res) {
                        $('#modal-detail-loading').hide();
                        if (res.code === 200 && res.data) {
                            const t = res.data;
                            const req = t.requestor || {};
                            const nik = req.karyawan_n_i_k || req.karyawan || {};
                            const jab = nik.karyawan_jabatan_one || {};
                            const dept = jab.departemen?.nama_departemen || nik.departemen?.nama_departemen || '-';
                            const site = nik.penempatan_kerja?.site_name || '-';
                            const holdingName = nik.penempatan_kerja?.holding?.holding_name || '-';

                            // Format Jabatan: "Nama Divisi + Bagian , Jabatan"
                            const divisiName  = (jab.divisi?.nama_divisi || nik.divisi?.nama_divisi || '').trim();
                            const bagianName  = (jab.bagian?.nama_bagian || nik.bagian?.nama_bagian || '').trim();
                            const jabatanName = (jab.jabatan?.nama_jabatan || nik.jabatan?.nama_jabatan || '').trim();

                            let divisiBagian = [divisiName, bagianName].filter(Boolean).join(' ');
                            let fullJabatan = '';
                            if (jabatanName && divisiBagian) {
                                fullJabatan = `${jabatanName} , ${divisiBagian}`;
                            } else if (divisiBagian) {
                                fullJabatan = divisiBagian;
                            } else if (jabatanName) {
                                fullJabatan = jabatanName;
                            } else if (divisiBagian) {
                                fullJabatan = divisiBagian;
                            } else {
                                fullJabatan = '-';
                            }
                            $('#det-jabatan').text(fullJabatan);

                            // Parent Ticket & Root Ticket
                            if (t.parent_ticket || (t.parent_ticket_id && t.parent_ticket_id !== t.id)) {
                                const p = t.parent_ticket;
                                const pNomor = p?.nomor_ticket || t.parent_ticket_id;
                                const pSub = p?.ticket_sub_menu?.nama_sub_menu || '';
                                const pDate = p?.created_at ? new Date(p.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';

                                $('#det-parent-ticket').html(`<span class="text-primary fw-bold">${pNomor}</span>`);
                                $('#det-parent-ticket-sub').text([pSub, pDate].filter(Boolean).join(' • '));
                                $('#box-parent-ticket').show();
                            } else {
                                $('#box-parent-ticket').hide();
                            }

                            if (t.root_ticket || (t.root_ticket_id && t.root_ticket_id !== t.id && t.root_ticket_id !== t.parent_ticket_id)) {
                                const r = t.root_ticket;
                                const rNomor = r?.nomor_ticket || t.root_ticket_id;
                                const rSub = r?.ticket_sub_menu?.nama_sub_menu || '';
                                const rDate = r?.created_at ? new Date(r.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';

                                $('#det-root-ticket').html(`<span class="fw-bold" style="color: #4f46e5;">${rNomor}</span>`);
                                $('#det-root-ticket-sub').text([rSub, rDate].filter(Boolean).join(' • '));
                                $('#box-root-ticket').show();
                            } else {
                                $('#box-root-ticket').hide();
                            }

                            $('#modalTicketDetailLabel').text(`Detail Tiket: ${t.nomor_ticket || '-'}`);
                            $('#modal-ticket-subtitle').text(`Diajukan oleh ${t.requestor_name || req.name || '-'} pada ${t.created_at ? new Date(t.created_at).toLocaleString() : '-'}`);

                            $('#det-nomor-ticket').text(t.nomor_ticket || '-');
                            $('#det-tanggal').text(t.created_at ? new Date(t.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false } ).replace(/\./g, ':') .replace(/(\d{2}:\d{2}:\d{4})/, '$1') : '-');
                            $('#det-pengaju').text(t.requestor_name || req.name || '-');
                            $('#det-status').html(getStatusPill(t.ticket_status, t.approval_status));

                            $('#det-sistem').text(envMap[t.ticket_menu?.kategori] || '-');
                            $('#det-modul').text(`${t.ticket_menu?.nama_menu || '-'} / ${t.ticket_sub_menu?.nama_sub_menu || '-'}`);
                            $('#det-departemen').text(`${dept} (${site} - ${holdingName})`);
                            $('#det-pic').text(t.pic_name || '-');

                            // Render table details
                            const tbody = $('#table-modal-details tbody');
                            tbody.empty();

                            let rincianRowCount = 0;

                            if (!t.ticket_details || t.ticket_details.length === 0) {
                                tbody.append('<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada rincian data</td></tr>');
                            } else {
                                t.ticket_details.forEach(det => {
                                    const refDetailText = det.reference_ticket_detail
                                        ? `<br><small class="text-primary"><i class="mdi mdi-link-variant me-1"></i>Ref: ${det.reference_ticket_detail.nomor_detail || '-'}${det.reference_ticket_detail.tanggal_transaksi ? ' (' + det.reference_ticket_detail.tanggal_transaksi + ')' : ''}</small>`
                                        : '';
                                    if (det.sub_details && det.sub_details.length > 0) {
                                        det.sub_details.forEach(sub => {
                                            rincianRowCount++;
                                            tbody.append(`
                                                <tr>
                                                    <td>${det.tanggal_transaksi || '-'}</td>
                                                    <td>${det.nomor_detail || '-'}${refDetailText}</td>
                                                    <td>${sub.line_number || '-'}</td>
                                                    <td>${sub.alasan_ubah || det.alasan_ubah || '-'}</td>
                                                    <td><code>${sub.before_ubah || det.before_ubah || '-'}</code></td>
                                                    <td><span class="value-after">${sub.after_ubah || det.after_ubah || '-'}</span></td>
                                                </tr>
                                            `);
                                        });
                                    } else {
                                        rincianRowCount++;
                                        tbody.append(`
                                                <tr>
                                                    <td>${det.tanggal_transaksi || '-'}</td>
                                                    <td>${det.nomor_detail || '-'}${refDetailText}</td>
                                                    <td>-</td>
                                                    <td>${det.alasan_ubah || '-'}</td>
                                                    <td><code>${det.before_ubah || '-'}</code></td>
                                                    <td><span class="value-after">${det.after_ubah || '-'}</span></td>
                                                </tr>
                                            `);
                                    }
                                });
                            }

                            $('#rincian-count').text(rincianRowCount);

                            // Render Approval Timeline
                            const timelineBox = $('#det-approval-timeline');
                            timelineBox.empty();

                            const approvals = t.approvals || [];
                            let timelineItemsHtml = '';

                            // 1. Atasan 1 (Approve 1) - hanya jika ada
                            const app1 = approvals.find(a => a.role === 'approve1' || a.role === 'atasan1');
                            const hasApprove1 = app1 || (t.approve1_name && t.approve1_name !== '-' && t.approve1_name.trim() !== '' && t.approve1_name !== 'Belum Ditentukan');
                            if (hasApprove1) {
                                const name = app1?.karyawan_name || app1?.karyawan?.name || t.approve1_name || 'Atasan 1';
                                const isApproved = (app1 && (app1.status === 'approved' || app1.status === 'completed')) || t.approve1;
                                const isRejected = app1 && app1.status === 'rejected';
                                const dateVal = app1?.approved_at || t.approve1_at;
                                const dateFormatted = dateVal ? new Date(dateVal).toLocaleString() : '';

                                let dotClass = 'pending';
                                let iconClass = 'mdi-clock-outline';
                                let subHtml = '<span class="text-warning">Menunggu Persetujuan</span>';

                                if (isApproved) {
                                    dotClass = '';
                                    iconClass = 'mdi-check';
                                    subHtml = `<span class="text-success fw-bold">Disetujui ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                } else if (isRejected) {
                                    dotClass = 'rejected';
                                    iconClass = 'mdi-close';
                                    subHtml = `<span class="text-danger fw-bold">Ditolak ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                }

                                timelineItemsHtml += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${dotClass}"><i class="mdi ${iconClass}"></i></div>
                                        <div class="timeline-title">Atasan 1: ${name}</div>
                                        <div class="timeline-sub">${subHtml}</div>
                                    </div>
                                `;
                            }

                            // 2. Atasan 2 (Approve 2) - hanya jika ada
                            const app2 = approvals.find(a => a.role === 'approve2' || a.role === 'atasan2');
                            const hasApprove2 = app2 || (t.approve2_name && t.approve2_name !== '-' && t.approve2_name.trim() !== '' && t.approve2_name !== 'Belum Ditentukan');
                            if (hasApprove2) {
                                const name = app2?.karyawan_name || app2?.karyawan?.name || t.approve2_name || 'Atasan 2';
                                const isApproved = (app2 && (app2.status === 'approved' || app2.status === 'completed')) || t.approve2;
                                const isRejected = app2 && app2.status === 'rejected';
                                const dateVal = app2?.approved_at || t.approve2_at;
                                const dateFormatted = dateVal ? new Date(dateVal).toLocaleString() : '';

                                let dotClass = 'pending';
                                let iconClass = 'mdi-clock-outline';
                                let subHtml = '<span class="text-warning">Menunggu Persetujuan</span>';

                                if (isApproved) {
                                    dotClass = '';
                                    iconClass = 'mdi-check';
                                    subHtml = `<span class="text-success fw-bold">Disetujui ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                } else if (isRejected) {
                                    dotClass = 'rejected';
                                    iconClass = 'mdi-close';
                                    subHtml = `<span class="text-danger fw-bold">Ditolak ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                }

                                timelineItemsHtml += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${dotClass}"><i class="mdi ${iconClass}"></i></div>
                                        <div class="timeline-title">Atasan 2: ${name}</div>
                                        <div class="timeline-sub">${subHtml}</div>
                                    </div>
                                `;
                            }

                            // 3. Checker (dari ticket_approvals dengan role 'checker', mendukung multiple checker)
                            const checkerApprovals = approvals.filter(a => a.role === 'checker');
                            if (checkerApprovals.length > 0) {
                                checkerApprovals.forEach((chk, idx) => {
                                    const name = chk.karyawan_name || chk.karyawan?.name || 'Checker';
                                    const titleLabel = checkerApprovals.length > 1 ? `Checker ${idx + 1}: ${name}` : `Checker: ${name}`;
                                    const isChecked = chk.status === 'completed' || chk.status === 'approved';
                                    const isRejected = chk.status === 'rejected';
                                    const dateVal = chk.approved_at;
                                    const dateFormatted = dateVal ? new Date(dateVal).toLocaleString() : '';

                                    let dotClass = 'pending';
                                    let iconClass = 'mdi-clock-outline';
                                    let subHtml = '<span class="text-warning">Menunggu Pemeriksaan</span>';

                                    if (isChecked) {
                                        dotClass = '';
                                        iconClass = 'mdi-check';
                                        subHtml = `<span class="text-success fw-bold">Diperiksa ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                    } else if (isRejected) {
                                        dotClass = 'rejected';
                                        iconClass = 'mdi-close';
                                        subHtml = `<span class="text-danger fw-bold">Ditolak ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                    }

                                    timelineItemsHtml += `
                                        <div class="timeline-item">
                                            <div class="timeline-dot ${dotClass}"><i class="mdi ${iconClass}"></i></div>
                                            <div class="timeline-title">${titleLabel}</div>
                                            <div class="timeline-sub">${subHtml}</div>
                                        </div>
                                    `;
                                });
                            } else if (t.checker_name && t.checker_name !== '-' && t.checker_name.trim() !== '' && t.checker_name !== 'Belum Ditentukan') {
                                // Fallback legacy single checker
                                const isChecked = Boolean(t.checked);
                                const dateVal = t.checked_at;
                                const dateFormatted = dateVal ? new Date(dateVal).toLocaleString() : '';

                                timelineItemsHtml += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${isChecked ? '' : 'pending'}"><i class="mdi ${isChecked ? 'mdi-check' : 'mdi-clock-outline'}"></i></div>
                                        <div class="timeline-title">Checker: ${t.checker_name}</div>
                                        <div class="timeline-sub">${isChecked ? '<span class="text-success fw-bold">Diperiksa ' + (dateFormatted ? '(' + dateFormatted + ')' : '') + '</span>' : '<span class="text-warning">Menunggu Pemeriksaan</span>'}</div>
                                    </div>
                                `;
                            }

                            // 4. Validator - hanya jika ada
                            const valApp = approvals.find(a => a.role === 'validator');
                            const hasValidator = valApp || (t.validator_name && t.validator_name !== '-' && t.validator_name.trim() !== '' && t.validator_name !== 'Belum Ditentukan');
                            if (hasValidator) {
                                const name = valApp?.karyawan_name || valApp?.karyawan?.name || t.validator_name || 'Validator';
                                const isValidated = (valApp && (valApp.status === 'approved' || valApp.status === 'completed')) || t.validator;
                                const isRejected = valApp && valApp.status === 'rejected';
                                const dateVal = valApp?.approved_at || t.validator_at;
                                const dateFormatted = dateVal ? new Date(dateVal).toLocaleString() : '';

                                let dotClass = 'pending';
                                let iconClass = 'mdi-clock-outline';
                                let subHtml = '<span class="text-warning">Menunggu Validasi</span>';

                                if (isValidated) {
                                    dotClass = '';
                                    iconClass = 'mdi-check';
                                    subHtml = `<span class="text-success fw-bold">Divalidasi ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                } else if (isRejected) {
                                    dotClass = 'rejected';
                                    iconClass = 'mdi-close';
                                    subHtml = `<span class="text-danger fw-bold">Ditolak ${dateFormatted ? '(' + dateFormatted + ')' : ''}</span>`;
                                }

                                timelineItemsHtml += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${dotClass}"><i class="mdi ${iconClass}"></i></div>
                                        <div class="timeline-title">Validator: ${name}</div>
                                        <div class="timeline-sub">${subHtml}</div>
                                    </div>
                                `;
                            }

                            // 5. PIC Pelaksana - hanya jika ada
                            if (t.pic_name && t.pic_name !== '-' && t.pic_name.trim() !== '' && t.pic_name !== 'Belum Ditugaskan') {
                                const isFinished = Boolean(t.finish_at);
                                const isStarted = Boolean(t.pic_started_at);
                                const finishFormatted = t.finish_at ? new Date(t.finish_at).toLocaleString() : '';

                                let dotClass = 'pending';
                                let iconClass = 'mdi-account-tie';
                                let subHtml = '<span class="text-muted">Ditugaskan (Belum Selesai)</span>';

                                if (isFinished) {
                                    dotClass = '';
                                    iconClass = 'mdi-check';
                                    subHtml = `<span class="text-success fw-bold">Selesai ${finishFormatted ? '(' + finishFormatted + ')' : ''}</span>`;
                                } else if (isStarted) {
                                    dotClass = 'process';
                                    iconClass = 'mdi-progress-wrench';
                                    subHtml = '<span class="text-primary fw-bold">Sedang Dikerjakan</span>';
                                }

                                timelineItemsHtml += `
                                    <div class="timeline-item">
                                        <div class="timeline-dot ${dotClass}"><i class="mdi ${iconClass}"></i></div>
                                        <div class="timeline-title">PIC: ${t.pic_name}</div>
                                        <div class="timeline-sub">${subHtml}</div>
                                    </div>
                                `;
                            }

                            if (!timelineItemsHtml) {
                                timelineItemsHtml = '<div class="text-muted small text-center py-4">Tidak ada alur persetujuan untuk tiket ini</div>';
                            }

                            timelineBox.html(timelineItemsHtml);
                            // Render Attachments
                            const attachBox = $('#det-attachments');
                            attachBox.empty();
                            if (!t.attachments || t.attachments.length === 0) {
                                attachBox.html('<div class="text-muted small text-center py-4">Tidak ada lampiran dokumen</div>');
                            } else {
                                let attachList = '<div class="d-flex flex-column gap-2">';
                                t.attachments.forEach(att => {
                                    let url = "{{ asset('storage') }}/" + att.file_path;
                                    attachList += `
                                        <a href="${url}" target="_blank" class="attachment-item">
                                            <div class="d-flex align-items-center gap-2 text-truncate">
                                                <div class="attachment-icon"><i class="mdi mdi-file-document-outline"></i></div>
                                                <span class="attachment-name text-truncate">${att.file_name || 'Lampiran Tiket'}</span>
                                            </div>
                                            <i class="mdi mdi-download text-primary"></i>
                                        </a>
                                    `;
                                });
                                attachList += '</div>';
                                attachBox.html(attachList);
                            }

                            // Render Comments Timeline
                            const commentsBox = $('#det-comments-timeline');
                            commentsBox.empty();
                            const comments = t.comments || [];
                            $('#comments-count').text(comments.length);

                            if (comments.length === 0) {
                                commentsBox.html('<div class="text-muted small text-center py-4"><i class="mdi mdi-comment-off-outline d-block mb-1" style="font-size: 28px; color: #cbd5e1;"></i>Belum ada riwayat komentar atau catatan aksi</div>');
                            } else {
                                const levelMap = {
                                    'atasan1': 'Atasan 1',
                                    'atasan2': 'Atasan 2',
                                    'approve1': 'Atasan 1',
                                    'approve2': 'Atasan 2',
                                    'checker': 'Checker',
                                    'validator': 'Validator',
                                    'pic': 'PIC Pelaksana',
                                    'requestor': 'Requestor / Pengaju'
                                };

                                let commentItemsHtml = '';
                                comments.forEach(cm => {
                                    const action = (cm.action_ticket || '').toLowerCase();
                                    const actionLevel = (cm.approval_level || '').toLowerCase();
                                    const levelName = levelMap[actionLevel] || (cm.approval_level ? cm.approval_level.toUpperCase() : 'Approval');

                                    // Cari nama karyawan / orang yang melakukan aksi
                                    let personName = cm.karyawan?.name || cm.karyawan?.nama_karyawan || cm.karyawan_name || '';

                                    if (!personName && t.approvals && t.approvals.length > 0) {
                                        const appr = t.approvals.find(a => (cm.karyawan_id && a.karyawan_id === cm.karyawan_id) || a.role === actionLevel || a.role === cm.approval_level);
                                        if (appr) {
                                            personName = appr.karyawan?.name || appr.karyawan_name || '';
                                        }
                                    }

                                    if (!personName) {
                                        if (actionLevel === 'approve1' || actionLevel === 'atasan1') {
                                            personName = t.approve1_name || '';
                                        } else if (actionLevel === 'approve2' || actionLevel === 'atasan2') {
                                            personName = t.approve2_name || '';
                                        } else if (actionLevel === 'checker') {
                                            personName = t.checker_name || '';
                                        } else if (actionLevel === 'validator') {
                                            personName = t.validator_name || '';
                                        } else if (actionLevel === 'pic') {
                                            personName = t.pic_name || '';
                                        } else if (actionLevel === 'requestor') {
                                            personName = t.requestor_name || t.requestor?.name || '';
                                        }
                                    }

                                    if (!personName || personName === '-' || personName.trim() === '') {
                                        personName = levelName;
                                    }

                                    const jabName = cm.jabatan?.jabatan?.nama_jabatan || cm.jabatan?.nama_jabatan || '';
                                    
                                    let dotClass = 'dot-comment';
                                    let dotIcon = 'mdi-comment-outline';
                                    let badgeHtml = '<span class="comment-action-badge badge-comment"><i class="mdi mdi-comment-outline"></i> Komentar</span>';
                                    let bubbleClass = '';

                                    if (action === 'approve' || action === 'approved' || action === 'setujui') {
                                        dotClass = 'dot-approve';
                                        dotIcon = 'mdi-check';
                                        badgeHtml = '<span class="comment-action-badge badge-approve"><i class="mdi mdi-check-circle-outline"></i> Disetujui</span>';
                                        bubbleClass = 'bubble-approve';
                                    } else if (action === 'reject' || action === 'rejected' || action === 'tolak') {
                                        dotClass = 'dot-reject';
                                        dotIcon = 'mdi-close';
                                        badgeHtml = '<span class="comment-action-badge badge-reject"><i class="mdi mdi-close-circle-outline"></i> Ditolak</span>';
                                        bubbleClass = 'bubble-reject';
                                    } else if (action === 'start') {
                                        dotClass = 'dot-comment';
                                        dotIcon = 'mdi-play';
                                        badgeHtml = '<span class="comment-action-badge badge-comment"><i class="mdi mdi-play-circle-outline"></i> Mulai Kerja</span>';
                                    } else if (action === 'finish') {
                                        dotClass = 'dot-approve';
                                        dotIcon = 'mdi-check-all';
                                        badgeHtml = '<span class="comment-action-badge badge-approve"><i class="mdi mdi-check-all"></i> Selesai Dikerjakan</span>';
                                        bubbleClass = 'bubble-approve';
                                    } else if (action === 'cancel') {
                                        dotClass = 'dot-reject';
                                        dotIcon = 'mdi-cancel';
                                        badgeHtml = '<span class="comment-action-badge badge-reject"><i class="mdi mdi-cancel"></i> Dibatalkan</span>';
                                        bubbleClass = 'bubble-reject';
                                    } else if (action === 'revisi' || action === 'revise') {
                                        dotClass = 'dot-reject';
                                        dotIcon = 'mdi-alert-outline';
                                        badgeHtml = '<span class="comment-action-badge badge-reject"><i class="mdi mdi-alert-circle-outline"></i> Minta Revisi</span>';
                                        bubbleClass = 'bubble-reject';
                                    }

                                    const dateVal = cm.comment_at || cm.created_at;
                                    let dateFormatted = '-';
                                    if (dateVal) {
                                        const d = new Date(dateVal);
                                        if (!isNaN(d.getTime())) {
                                            const b = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                                            const hh = String(d.getHours()).padStart(2, '0');
                                            const mm = String(d.getMinutes()).padStart(2, '0');
                                            dateFormatted = `${d.getDate()} ${b[d.getMonth()]} ${d.getFullYear()} ${hh}:${mm}`;
                                        }
                                    }

                                    const textContent = cm.comment_text 
                                        ? $('<div>').text(cm.comment_text).html() 
                                        : '<em class="text-muted small">Tidak ada pesan teks / catatan tambahan.</em>';

                                    commentItemsHtml += `
                                        <div class="comment-timeline-item">
                                            <div class="comment-dot ${dotClass}"><i class="mdi ${dotIcon}"></i></div>
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="fw-bold text-dark" style="font-size: 12.5px;">${personName}</span>
                                                    <span class="badge bg-label-secondary" style="font-size: 10px;">${levelName}</span>
                                                    ${jabName ? `<small class="text-muted">(${jabName})</small>` : ''}
                                                    ${badgeHtml}
                                                </div>
                                                <span class="comment-meta"><i class="mdi mdi-clock-outline me-1"></i>${dateFormatted}</span>
                                            </div>
                                            <div class="comment-bubble ${bubbleClass}">
                                                <div class="comment-text">${textContent}</div>
                                            </div>
                                        </div>
                                    `;
                                });
                                commentsBox.html(commentItemsHtml);
                            }

                            // Render Generated Tickets Tab
                            const generatedBox = $('#det-generated-container');
                            generatedBox.empty();
                            const generatedTickets = t.generated_tickets || [];
                            const approvalsWithRules = (t.approvals || []).filter(a => a.dependency_rule_id || a.generate_status);
                            
                            $('#generated-count').text(generatedTickets.length);

                            if (generatedTickets.length === 0 && approvalsWithRules.length === 0) {
                                generatedBox.html(`
                                    <div class="text-muted small text-center py-4">
                                        <i class="mdi mdi-source-branch-sync d-block mb-1" style="font-size: 32px; color: #cbd5e1;"></i>
                                        Tidak ada tiket lanjutan yang dibuat dari tiket ini.
                                    </div>
                                `);
                            } else {
                                let genHtml = `
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 12px;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" style="width: 40px;">No</th>
                                                    <th>Nomor Tiket Lanjutan</th>
                                                    <th>Sub Modul Tujuan</th>
                                                    <th>Status Generate</th>
                                                    <th>Status Tiket</th>
                                                    <th>Pengaju / Checker</th>
                                                    <th>Tgl Dibuat</th>
                                                    <th class="text-center" style="width: 150px;">Aksi Admin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                                if (generatedTickets.length > 0) {
                                    generatedTickets.forEach((g, idx) => {
                                        // Cari data approval yang me-link ke tiket draft ini
                                        const matchingApp = (t.approvals || []).find(a => a.generated_ticket_id === g.id || a.dependency_rule_id === g.dependency_rule_id);
                                        const genStatus = matchingApp?.generate_status || (g.is_draft ? 'AWAITING_DETAILS' : 'SUCCESS');
                                        
                                        let genStatusBadge = '';
                                        if (genStatus === 'AWAITING_DETAILS') {
                                            genStatusBadge = '<span class="gen-status-badge gen-awaiting"><i class="mdi mdi-alert-circle-outline"></i> Perlu Dilengkapi (Draft)</span>';
                                        } else if (genStatus === 'SUCCESS') {
                                            genStatusBadge = '<span class="gen-status-badge gen-success"><i class="mdi mdi-check-circle-outline"></i> Sukses Diajukan</span>';
                                        } else if (genStatus === 'SKIPPED') {
                                            genStatusBadge = '<span class="gen-status-badge gen-skipped"><i class="mdi mdi-cancel"></i> Dibatalkan / Skipped</span>';
                                        } else if (genStatus === 'FAILED') {
                                            genStatusBadge = '<span class="gen-status-badge gen-failed"><i class="mdi mdi-alert-outline"></i> Gagal</span>';
                                        } else {
                                            genStatusBadge = `<span class="gen-status-badge gen-awaiting">${genStatus}</span>`;
                                        }

                                        const gDate = g.created_at ? new Date(g.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
                                        const gSubmenu = g.ticket_sub_menu?.nama_sub_menu || matchingApp?.branch_sub_menu?.nama_sub_menu || '-';
                                        const gReqName = g.requestor_name || g.requestor?.name || '-';

                                        // Tombol Batalkan hanya jika tiket berstatus Draft
                                        let actionHtml = '';
                                        const isDraftTicket = (g.ticket_status === 'T00' || g.is_draft == 1 || g.is_draft === true) && g.ticket_status !== 'T08';

                                        if (isDraftTicket) {
                                            actionHtml = `
                                                <button type="button" 
                                                    class="btn btn-sm btn-danger btn-cancel-generated-draft" 
                                                    data-draft-id="${g.id}" 
                                                    data-ticket-nomor="${g.nomor_ticket || '-'}" 
                                                    data-parent-id="${t.id}"
                                                    style="font-size: 11px; padding: 4px 10px; border-radius: 6px;">
                                                    <i class="mdi mdi-cancel me-1"></i>Batalkan Draft
                                                </button>
                                            `;
                                        } else if (g.ticket_status === 'T08' || matchingApp?.generate_status === 'SKIPPED') {
                                            actionHtml = '<span class="badge bg-label-danger" style="font-size: 10.5px;"><i class="mdi mdi-cancel me-1"></i>Sudah Dibatalkan</span>';
                                        } else {
                                            actionHtml = '<span class="badge bg-label-success" style="font-size: 10.5px;"><i class="mdi mdi-check me-1"></i>Sudah Berjalan</span>';
                                        }

                                        genHtml += `
                                            <tr>
                                                <td class="text-center">${idx + 1}</td>
                                                <td>
                                                    <span class="fw-bold text-primary">${g.nomor_ticket || '-'}</span>
                                                    ${g.is_cross_environment ? '<span class="badge bg-label-info ms-1" style="font-size: 9px;">Cross-Env</span>' : ''}
                                                </td>
                                                <td>${gSubmenu}</td>
                                                <td>${genStatusBadge}</td>
                                                <td>${getStatusPill(g.ticket_status, g.approval_status)}</td>
                                                <td>${gReqName}</td>
                                                <td>${gDate}</td>
                                                <td class="text-center">${actionHtml}</td>
                                            </tr>
                                        `;
                                    });
                                } else {
                                    // Fallback jika tiket belum terbuat tapi ada rule skipped/awaiting
                                    approvalsWithRules.forEach((app, idx) => {
                                        const submenuName = app.branch_sub_menu?.nama_sub_menu || '-';
                                        const ruleLabel = app.dependency_rule?.condition_label || 'Rule Dependency';
                                        genHtml += `
                                            <tr>
                                                <td class="text-center">${idx + 1}</td>
                                                <td class="text-muted"><em>Belum terbentuk</em></td>
                                                <td>${submenuName} <br><small class="text-muted">(${ruleLabel})</small></td>
                                                <td><span class="gen-status-badge gen-skipped">${app.generate_status || 'SKIPPED'}</span></td>
                                                <td>-</td>
                                                <td>${app.karyawan?.name || app.karyawan_name || '-'}</td>
                                                <td>${app.generated_at ? new Date(app.generated_at).toLocaleString() : '-'}</td>
                                                <td class="text-center"><span class="text-muted small">-</span></td>
                                            </tr>
                                        `;
                                    });
                                }

                                genHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                                generatedBox.html(genHtml);
                            }

                            $('#modal-detail-content').show();
                        } else {
                            Swal.fire('Gagal', 'Tidak dapat mengambil rincian data tiket.', 'error');
                            $('#modalTicketDetail').modal('hide');
                        }
                    },
                    error: function () {
                        $('#modal-detail-loading').hide();
                        Swal.fire('Error', 'Terjadi kesalahan saat memuat data tiket.', 'error');
                        $('#modalTicketDetail').modal('hide');
                    }
                });
            });

            // Handler Pembatalan Draft Tiket Lanjutan oleh Admin
            $(document).on('click', '.btn-cancel-generated-draft', function () {
                const draftTicketId = $(this).data('draft-id');
                const nomorTicket   = $(this).data('ticket-nomor');
                const parentId      = $(this).data('parent-id');

                if (!draftTicketId) return;

                Swal.fire({
                    title: 'Batalkan Draft Tiket Lanjutan?',
                    html: `Apakah Anda yakin ingin membatalkan draft tiket <strong>${nomorTicket}</strong>?<br><br>
                           <small class="text-muted">Tindakan ini akan membatalkan draft tiket lanjutan, mengembalikan status kondisi di tiket induk menjadi nonaktif, dan otomatis memajukan tiket induk ke tahap berikutnya.</small>`,
                    icon: 'warning',
                    input: 'text',
                    inputLabel: 'Alasan Pembatalan (Opsional):',
                    inputPlaceholder: 'Contoh: Checker salah mengaktifkan switch checklist kondisi',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="mdi mdi-cancel me-1"></i> Ya, Batalkan Draft',
                    cancelButtonText: 'Kembali',
                    showLoaderOnConfirm: true,
                    preConfirm: (reason) => {
                        let cancelUrl = "{{ route('ticket.report.cancel_generated_draft', ':id') }}".replace(':id', draftTicketId);
                        return $.ajax({
                            url: cancelUrl,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                reason: reason || 'Dibatalkan oleh admin dari Report Tiket'
                            }
                        }).catch(xhr => {
                            const errMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat membatalkan draft tiket.';
                            Swal.showValidationMessage(errMsg);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const res = result.value;
                        if (res.code === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dibatalkan!',
                                text: res.message || 'Draft tiket berhasil dibatalkan.',
                                confirmButtonColor: '#7c3aed',
                            });

                            // Reload detail tiket di dalam modal
                            if (parentId) {
                                $('.btn-open-detail[data-id="' + parentId + '"]').first().trigger('click');
                            } else {
                                $('#modalTicketDetail').modal('hide');
                            }

                            // Reload table list jika data sedang tampil
                            if (typeof loadData === 'function' && allData.length > 0) {
                                loadData();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal membatalkan tiket draft.'
                            });
                        }
                    }
                });
            });
            // Data hanya dimuat setelah pengguna menekan tombol filter (trigger manual)
        });
    </script>
@endsection