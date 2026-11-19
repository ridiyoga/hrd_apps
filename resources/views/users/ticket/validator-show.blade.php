@extends('users.layouts.main')

@section('title')
APPS | KARYAWAN - Detail Ticket
@endsection

@section('css')
<style>
    /* ── HEADER ── */
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
        box-shadow: 0 0 25px rgba(155, 109, 255, .8);
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

    /* ── STATUS BADGE ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .status-pending, .status-waiting {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-approved, .status-done {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-rejected {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }

    .status-process, .status-ready {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .status-draft {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    .status-feedback {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    /* ── FIELD READONLY ── */
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

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
    }

    .form-view-group {
        position: relative;
        margin-bottom: 14px;
    }

    .form-view-group .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #7c3aed;
        font-size: 13px;
        z-index: 6;
        pointer-events: none;
    }

    /* ── SECTION LABEL ── */
    .section-label {
        font-size: 12px;
        font-weight: 700;
        color: #6b21a8;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        margin-top: 4px;
    }

    /* ── CHANGE CARD ── */
    .change-card-item {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border: 1.5px solid #e9d5ff;
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: box-shadow .2s, transform .15s;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .change-card-item:hover {
        box-shadow: 0 6px 20px rgba(124, 58, 237, .15);
        transform: translateY(-1px);
    }

    .change-card-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .change-card-info {
        flex: 1;
        overflow: hidden;
    }

    .change-card-info .nomor {
        font-size: 12px;
        font-weight: 700;
        color: #6b21a8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .change-card-info .alasan {
        font-size: 11px;
        color: #9ca3af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── LAMPIRAN ── */
    .lampiran-card {
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        width: 140px;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        position: relative;
    }

    .lampiran-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(124, 58, 237, 0.16);
        border-color: #a78bfa;
    }

    .lampiran-card img {
        width: 100%;
        height: 90px;
        object-fit: cover;
    }

    .lampiran-card .lc-footer {
        padding: 6px 8px;
    }

    .lampiran-card .lc-name {
        font-size: 11px;
        color: #555;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
    }

    /* ── APPROVAL CHAIN (full) ── */
    .approval-chain-full {
        display: flex;
        align-items: stretch;
        gap: 0;
        background: #faf9ff;
        border: 1px solid #ede9fe;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 14px;
    }

    .acf-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 6px;
        border-right: 1px dashed #ede9fe;
        position: relative;
    }

    .acf-step:last-child {
        border-right: none;
    }

    .acf-step.done {
        background: #f0fdf4;
    }

    .acf-step.wait {
        background: #fafafa;
    }

    .acf-step.mine {
        background: #ede9fe;
    }

    .acf-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        margin-bottom: 5px;
    }

    .acf-step.done .acf-icon {
        background: #bbf7d0;
        color: #166534;
    }

    .acf-step.wait .acf-icon {
        background: #f3f4f6;
        color: #9ca3af;
    }

    .acf-step.mine .acf-icon {
        background: #c4b5fd;
        color: #6d28d9;
    }

    .acf-label {
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        text-align: center;
        line-height: 1.3;
    }

    .acf-step.done .acf-label {
        color: #166534;
    }

    .acf-step.wait .acf-label {
        color: #9ca3af;
    }

    .acf-step.mine .acf-label {
        color: #6d28d9;
    }

    .acf-name {
        font-size: 8px;
        color: #9ca3af;
        text-align: center;
        margin-top: 2px;
        max-width: 60px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .acf-sig {
        max-height: 28px;
        max-width: 50px;
        object-fit: contain;
        margin-top: 4px;
    }

    .acf-date {
        font-size: 7.5px;
        color: #6b7280;
        text-align: center;
        margin-top: 1px;
        line-height: 1.1;
    }

    .change-accordion-item {
        background: #ffffff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    .change-accordion-item:hover {
        border-color: #c4b5fd;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.06);
    }
    .change-accordion-header {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        cursor: pointer;
        user-select: none;
        background: #fdfcff;
        gap: 12px;
        transition: background 0.2s ease;
    }
    .change-accordion-header:hover {
        background: #f8f4ff;
    }
    .change-accordion-header[aria-expanded="true"] {
        background: #f5f0ff;
        border-bottom: 1px solid #ede9fe;
    }
    .change-accordion-chevron {
        color: #7c3aed;
        font-size: 11px;
        transition: transform 0.25s ease;
        margin-left: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #f3e8ff;
        flex-shrink: 0;
    }
    .change-accordion-header[aria-expanded="true"] .change-accordion-chevron {
        transform: rotate(180deg);
        background: #7c3aed;
        color: #ffffff;
    }
    .change-accordion-body {
        padding: 12px 14px;
        background: #faf8ff;
    }
    .subdetails-wrap {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .subdetail-card {
        background: #ffffff;
        border: 1px solid #ede9fe;
        border-radius: 8px;
        padding: 10px;
    }
    .subdetail-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        gap: 8px;
    }
    .subdetail-line-badge {
        font-size: 10px;
        font-weight: 700;
        background: #ede9fe;
        color: #6b21a8;
        padding: 2px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }
    .subdetail-reason {
        font-size: 11px;
        font-weight: 600;
        color: #4b5563;
        text-align: right;
    }
    .subdetail-diff-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .diff-box {
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 11.5px;
    }
    .diff-before {
        background: #fef2f2;
        border: 1px solid #fee2e2;
    }
    .diff-before .diff-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #dc2626;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }
    .diff-after {
        background: #f0fdf4;
        border: 1px solid #dcfce7;
    }
    .diff-after .diff-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #16a34a;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }
    .diff-content {
        color: #1f2937;
        word-break: break-word;
        white-space: pre-wrap;
        line-height: 1.35;
    }

    /* ── INFO BANNER (validator sudah diisi) ── */
    .validated-banner {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1.5px solid #bbf7d0;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .validated-banner i {
        font-size: 22px;
        color: #16a34a;
        flex-shrink: 0;
    }

    .validated-banner .vb-title {
        font-size: 13px;
        font-weight: 700;
        color: #166534;
    }

    .validated-banner .vb-sub {
        font-size: 11px;
        color: #4ade80;
        margin-top: 2px;
    }

    .download-pdf-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 9px 16px;
        border-radius: 999px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(124, 58, 237, .18);
        transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
    }

    .download-pdf-btn:hover {
        color: #fff;
        opacity: .95;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(124, 58, 237, .24);
    }

    /* ── CHANGE CARD ITEM (trigger offcanvas) ── */
    .change-card-item {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fdfcff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 8px;
    }
    .change-card-item:hover {
        border-color: #c4b5fd;
        background: #f5f0ff;
        box-shadow: 0 4px 12px rgba(124,58,237,0.08);
    }
    .change-card-num {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg,#7c3aed,#a855f7);
        color: #fff; font-size: 12px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .change-card-info { flex: 1; min-width: 0; }
    .change-card-info .nomor { font-size: 12px; font-weight: 700; color: #1f2937; }
    .change-card-info .alasan { font-size: 10.5px; color: #6b7280; margin-top: 1px; }
    .change-card-arrow { color: #c4b5fd; font-size: 13px; flex-shrink: 0; }

    /* ── SUBDETAIL ACCORDION (inside offcanvas) ── */
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

    /* ── ACTION VALIDATOR BUTTONS ── */
    .validator-action-card {
        background: #ffffff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(124, 58, 237, 0.08);
        padding: 16px;
        margin-bottom: 12px;
    }

    .btn-validator-approve {
        background: linear-gradient(135deg, #7c3aed, #9333ea);
        border: none;
        border-radius: 14px;
        padding: 12px 10px;
        color: #fff;
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: .2px;
        box-shadow: 0 4px 16px rgba(124, 58, 237, .3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .15s, opacity .2s, box-shadow .2s;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-validator-approve:hover {
        opacity: .95;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(124, 58, 237, .38);
        color: #fff;
    }

    .btn-validator-approve:active {
        transform: scale(.98);
    }

    .btn-validator-reject {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        border: none;
        border-radius: 14px;
        padding: 12px 10px;
        color: #fff;
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: .2px;
        box-shadow: 0 4px 16px rgba(220, 38, 38, .25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .15s, opacity .2s, box-shadow .2s;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-validator-reject:hover {
        opacity: .95;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, .32);
        color: #fff;
    }

    .btn-validator-reject:active {
        transform: scale(.98);
    }

    .btn-validator-pending {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        border: none;
        border-radius: 14px;
        padding: 12px 10px;
        color: #fff;
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: .2px;
        box-shadow: 0 4px 16px rgba(217, 119, 6, .25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .15s, opacity .2s, box-shadow .2s;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-validator-pending:hover {
        opacity: .95;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(217, 119, 6, .35);
        color: #fff;
    }

    .btn-validator-pending:active {
        transform: scale(.98);
    }

    .btn-validator-resume {
        background: linear-gradient(135deg, #059669, #10b981);
        border: none;
        border-radius: 14px;
        padding: 12px 10px;
        color: #fff;
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: .2px;
        box-shadow: 0 4px 16px rgba(16, 185, 129, .25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .15s, opacity .2s, box-shadow .2s;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-validator-resume:hover {
        opacity: .95;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, .35);
        color: #fff;
    }

    .btn-validator-resume:active {
        transform: scale(.98);
    }

    /* ── SIGNATURE PAD (Offcanvas) ── */
    .sig-approver-card {
        border: 2px dashed #a855f7;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 4px;
        box-shadow: 0 4px 16px rgba(124, 58, 237, .1);
    }

    .sig-approver-header {
        background: linear-gradient(135deg, #faf7ff, #fff);
        border-bottom: 1px solid #f3e8ff;
        padding: 10px 14px;
        text-align: center;
    }

    .sig-approver-header small {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #7c3aed;
        letter-spacing: .6px;
    }

    #sig-pad-detail {
        width: 100%;
        height: 160px;
        touch-action: none;
        display: block;
        cursor: crosshair;
    }

    .sig-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 14px;
        border-top: 1px solid #f3e8ff;
    }
</style>
@endsection
@section('content_top')
<div class="card" style="position: relative;">
    <a href="{{ route('approval.ticket.validator.index', $menu_id) }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-shield"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color:#623acf; font-size:18px;">
            {{ $ticket->nomor_ticket }}
        </h5>
        <h5 class="mt-1 mb-1" style="font-size:14px; font-weight:700;">DETAIL TICKET</h5>
        <small class="text-muted">Lihat seluruh detail pengajuan ubah/hapus</small>
        <div class="mt-2 mb-1">
            @include('components.ticket-status-badge', ['ticket' => $ticket])
        </div>
        <a href="{{ route('ticket.export', $ticket->id) }}" target="_blank" class="download-pdf-btn">
            <i class="fa-solid fa-download"></i>
            Download PDF
        </a>
    </div>
    <div class="card-body px-3 px-md-4 pb-4">
        @include('components.parent-ticket-card')

        {{-- BANNER PENDING STATUS JIKA TIKET SEDANG DIPENDING VALIDATOR --}}
        @if($ticket->ticket_status === \App\Models\TicketHead::STATUS_PENDING_EXTERNAL)
        <div class="p-3 mb-4 rounded-3 border d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background:#fffbeb; border-color:#fde68a !important;">
            <div>
                <div class="fw-bold" style="color:#b45309; font-size:13px;">
                    <i class="fa-solid fa-hourglass-half me-1"></i> TICKET SEDANG DIPENDING VALIDATOR
                </div>
                <div class="mt-1" style="font-size:12px; color:#78350f;">
                    <strong>Alasan Pending:</strong> {{ $ticket->pending_reason ?: 'Menunggu kelengkapan lampiran dari requestor.' }}
                </div>
                @if($ticket->pending_started_at)
                <div class="text-muted" style="font-size:11px; margin-top:3px;">
                    <i class="fa-regular fa-clock me-1"></i> Dipending sejak: {{ \Carbon\Carbon::parse($ticket->pending_started_at)->translatedFormat('d M Y, H:i') }} (Waktu SLA validasi dihentikan sementara)
                </div>
                @endif
            </div>
            <div class="flex-shrink-0">
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" id="btn-resume-ticket" style="font-size:12px;">
                    <i class="fa-solid fa-play me-1"></i> Lanjutkan Validasi
                </button>
            </div>
        </div>
        @endif
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">1</span>
                <h6 class="mb-0 fw-bold">Pengajuan Ubah/Hapus</h6>
            </div>
            <div class="form-view-group">
                <span class="input-icon"><i class="fa-solid fa-tags"></i></span>
                <div class="field-readonly">
                    @php $envMap = [1=>'Epicor ERP', 2=>'Accurate Online SP', 3=>'Accurate Online AJA']; @endphp
                    {{ $envMap[$ticket->ticketMenu?->kategori ?? 0] ?? '-' }}
                </div>
            </div>
            <div class="form-view-group">
                <span class="input-icon"><i class="fa-solid fa-folder-open"></i></span>
                <div class="field-readonly">{{ $ticket->ticketMenu?->nama_menu ?? '-' }}</div>
            </div>
            <div class="form-view-group">
                <span class="input-icon"><i class="fa-solid fa-list-ul"></i></span>
                <div class="field-readonly">{{ $ticket->ticketSubMenu?->nama_sub_menu ?? '-' }}</div>
            </div>
            <div class="section-label">
                <i class="fa-solid fa-table-list"></i> Detail Perubahan Data
            </div>
            @if($ticket->ticketDetails && $ticket->ticketDetails->count() > 0)
            @foreach($ticket->ticketDetails as $i => $detail)
            @php
            $jumlahBaris = $detail->subDetails ? $detail->subDetails->count() : 0;
            $jumlahBatal = $detail->subDetails ? $detail->subDetails->where('is_cancelled', true)->count() : 0;
            $tanggalLabel = $detail->tanggal_transaksi
            ? \Carbon\Carbon::parse($detail->tanggal_transaksi)->translatedFormat('d M Y')
            : null;
            $refTanggalLabel = $detail->referenceTicketDetail?->tanggal_transaksi
            ? \Carbon\Carbon::parse($detail->referenceTicketDetail->tanggal_transaksi)->translatedFormat('d M Y')
            : null;
            @endphp
            <div class="change-card-item"
                data-bs-toggle="offcanvas"
                data-bs-target="#off-canvas-change-detail"
                data-nomor="{{ $detail->nomor_detail }}"
                data-tanggal="{{ $tanggalLabel }}"
                data-ref-nomor="{{ $detail->referenceTicketDetail?->nomor_detail ?? '' }}"
                data-ref-tanggal="{{ $refTanggalLabel ?? '' }}"
                data-subdetails="{{ $detail->subDetails ? $detail->subDetails->map(fn($sd) => [
                    'id'                => $sd->id,
                    'is_cancelled'      => (bool) $sd->is_cancelled,
                    'cancel_reason'     => $sd->cancel_reason,
                    'cancelled_by_role' => $sd->cancelled_by_role,
                    'cancelled_by_name' => $sd->cancelled_by_name,
                    'line_number' => $sd->line_number,
                    'alasan_ubah' => $sd->alasan_ubah,
                    'before_ubah' => $sd->before_ubah,
                    'after_ubah'  => $sd->after_ubah,
                ])->toJson() : '[]' }}"
                data-idx="{{ $i + 1 }}">
                <div class="change-card-num">{{ $i + 1 }}</div>
                <div class="change-card-info">
                    <div class="nomor">Transaksi: {{ $detail->nomor_detail ?: '-' }}</div>
                    <div class="alasan">{{ $jumlahBaris }} baris perubahan{{ $tanggalLabel ? ' · ' . $tanggalLabel : '' }}</div>
                    @if($jumlahBatal > 0)
                    <div class="fw-semibold mt-1" style="font-size:10.5px; color:#dc2626;">
                        <i class="fa-solid fa-ban me-1"></i>{{ $jumlahBatal }} baris dibatalkan
                    </div>
                    @endif
                    @if($detail->referenceTicketDetail)
                    <div class="text-truncate mt-1" style="font-size:10.5px; color:#6d28d9;">
                        <i class="fa-solid fa-link me-1"></i>Ref: {{ $detail->referenceTicketDetail->nomor_detail ?? '-' }}{{ $refTanggalLabel ? ' (' . $refTanggalLabel . ')' : '' }}
                    </div>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right text-muted" style="font-size:11px;"></i>
            </div>
            @endforeach
            @else
            <div class="text-center text-muted py-3 rounded-3"
                style="font-size: 12px; border: 1.5px dashed #ede9fe; background: #faf9ff;">
                <i class="fa-solid fa-table fa-lg mb-1 d-block" style="color: #d8b4fe;"></i>
                Tidak ada detail perubahan
            </div>
            @endif
        </div>
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2">2</span>
                    <h6 class="mb-0 fw-bold">Lampiran</h6>
                </div>
                @if(!$ticket->validator && $ticket->approval_status !== \App\Models\TicketHead::APPROVAL_REJECTED)
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-add-attachment" style="font-size:12px; background:linear-gradient(135deg, #7c3aed, #6d28d9); border:none;">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Tambah Lampiran
                </button>
                @endif
            </div>
            @php
                $attachmentsList = collect();
                if ($ticket->attachments && $ticket->attachments->isNotEmpty()) {
                    $attachmentsList = $ticket->attachments;
                } elseif ($ticket->lampiran) {
                    $attachmentsList = collect([(object)[
                        'id'               => null,
                        'original_name'    => basename($ticket->lampiran),
                        'file_path'        => $ticket->lampiran,
                        'file_size'        => null,
                        'uploaded_by'      => null,
                        'uploaded_by_name' => null,
                    ]]);
                }
            @endphp
            @if($attachmentsList->isNotEmpty())
            <div class="d-flex flex-wrap gap-2">
                @foreach($attachmentsList as $att)
                @php
                    $attId      = is_array($att) ? ($att['id'] ?? null) : ($att->id ?? null);
                    $filePath   = is_array($att) ? ($att['file_path'] ?? '') : ($att->file_path ?? '');
                    $origName   = is_array($att) ? ($att['original_name'] ?? basename($filePath)) : ($att->original_name ?? basename($filePath));
                    $fileSize   = is_array($att) ? ($att['file_size'] ?? null) : ($att->file_size ?? null);
                    $uploader   = is_array($att) ? ($att['uploaded_by_name'] ?? null) : ($att->uploaded_by_name ?? null);
                    $uploaderId = is_array($att) ? ($att['uploaded_by'] ?? null) : ($att->uploaded_by ?? null);
                    $fileUrl    = asset('storage/' . $filePath);
                    $ext        = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $isImg      = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    $isPdf      = $ext === 'pdf';
                    $isExcel    = in_array($ext, ['xls','xlsx','csv']);
                    $isDoc      = in_array($ext, ['doc','docx']);
                    $isZip      = in_array($ext, ['zip','rar','7z']);
                @endphp
                <div class="lampiran-card" onclick="window.open('{{ $fileUrl }}', '_blank')" title="Klik untuk membuka {{ $origName }}">
                    @if($isImg)
                    <div style="height:90px; background:#f8fafc; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                        <img src="{{ $fileUrl }}" alt="{{ $origName }}" style="width:100%; height:90px; object-fit:cover;">
                    </div>
                    @else
                    <div class="text-center py-3 d-flex align-items-center justify-content-center" style="height:90px; background:#f8fafc;">
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
                    <div class="lc-footer">
                        <div class="lc-name" title="{{ $origName }}">{{ $origName }}</div>
                        @if($uploader)
                        <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:9.5px; color:#7c3aed;">
                            <span class="text-truncate" title="Diunggah oleh {{ $uploader }}"><i class="fa-solid fa-user-shield me-1"></i>{{ Str::limit($uploader, 11) }}</span>
                            @if(!$ticket->validator && (string)$uploaderId === (string)Auth::id() && $attId)
                            <button type="button" class="btn btn-link text-danger p-0 ms-1 btn-delete-att" data-id="{{ $attId }}" onclick="event.stopPropagation();" title="Hapus lampiran ini" style="font-size:10px; line-height:1;">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                        @endif
                        <a href="{{ $fileUrl }}" target="_blank" onclick="event.stopPropagation();"
                            class="btn btn-sm btn-primary w-100" style="font-size:11px;">
                            <i class="fa-solid fa-eye me-1"></i> Lihat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="field-readonly no-icon" style="color:#c4b5fd; font-style:italic; font-size:12px;">
                <i class="fa-solid fa-paperclip me-2" style="color:#d8b4fe;"></i>Tidak ada lampiran
            </div>
            @endif
        </div>
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">3</span>
                <h6 class="mb-0 fw-bold">Otorisasi &amp; Pengesahan</h6>
            </div>
            @php
                // Catatan yang disubmit bersamaan dengan approval/pemeriksaan/validasi tiket.
                // $ticket->comments terurut comment_at ASC, jadi last() = catatan terbaru.
                $stepComment = function ($level, $karyawanId = null) use ($ticket) {
                    return ($ticket->comments ?? collect())
                        ->where('approval_level', $level)
                        ->when($karyawanId, fn($c) => $c->where('karyawan_id', $karyawanId))
                        ->filter(fn($c) => trim((string) $c->comment_text) !== '')
                        ->last();
                };
                $cmtRequestor = $stepComment('requestor');
                $cmtApprove1  = $stepComment('approve1');
                $cmtApprove2  = $stepComment('approve2');
                $cmtValidator = $stepComment('validator');
                $checkerRows  = $ticket->approvals ? $ticket->approvals->where('role', 'checker') : collect();
            @endphp
            <div class="row g-2 d-flex flex-wrap justify-content-end align-items-stretch mb-3">
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
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
                            @if($cmtRequestor)
                            <div class="text-start mt-1" title="{{ $cmtRequestor->comment_text }}"
                                style="background:#fff; border:1px solid #e9d5ff; border-radius:6px; padding:3px 6px; font-size:8.5px; color:#4b5563; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                <i class="fa-solid fa-comment-dots me-1" style="color:#a78bfa;"></i>{{ $cmtRequestor->comment_text }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
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
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                @if($ticket->approve1_name)
                                {{ $ticket->approve1_name }}
                                @else
                                <span class="skeleton" style="height:12px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                @endif
                            </small>
                            @if($ticket->approve1_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve1_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                            @endif
                            @if($cmtApprove1)
                            <div class="text-start mt-1" title="{{ $cmtApprove1->comment_text }}"
                                style="background:#fff; border:1px solid #e9d5ff; border-radius:6px; padding:3px 6px; font-size:8.5px; color:#4b5563; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                <i class="fa-solid fa-comment-dots me-1" style="color:#a78bfa;"></i>{{ $cmtApprove1->comment_text }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="background: #ffffff !important; border-color: #e2e8f0 !important;">
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
                            <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                @if($ticket->approve2_name)
                                {{ $ticket->approve2_name }}
                                @else
                                <span class="skeleton" style="height:12px; width:60px; display:inline-block; background:#cbd5e1 !important; border-radius:4px;"></span>
                                @endif
                            </small>
                            @if($ticket->approve2_at)
                            <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->approve2_at)->translatedFormat('d M Y, H:i') }}
                            </span>
                            @endif
                            @if($cmtApprove2)
                            <div class="text-start mt-1" title="{{ $cmtApprove2->comment_text }}"
                                style="background:#fff; border:1px solid #e9d5ff; border-radius:6px; padding:3px 6px; font-size:8.5px; color:#4b5563; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                <i class="fa-solid fa-comment-dots me-1" style="color:#a78bfa;"></i>{{ $cmtApprove2->comment_text }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($checkerRows->count() > 0)
                    @foreach($checkerRows as $chk)
                    @php
                        $isCross = (bool) ($chk->dependencyRule?->is_cross_environment ?? false);
                        $targetEnvName = $chk->branchSubMenu?->ticketMenu?->kategori_display;
                        $targetMenuName = $chk->branchSubMenu?->ticketMenu?->nama_menu;
                        $targetSubMenuName = $chk->branchSubMenu?->nama_sub_menu;
                        $ruleLabelParts = [];
                        if (!empty($targetMenuName)) {
                            $ruleLabelParts[] = $targetMenuName;
                        }
                        if (!empty($targetSubMenuName) && $targetSubMenuName !== $targetMenuName) {
                            $ruleLabelParts[] = $targetSubMenuName;
                        }
                        $ruleDisplay = implode(' - ', $ruleLabelParts);
                        if (!empty($targetEnvName)) {
                            $ruleDisplay = ($ruleDisplay !== '' ? $ruleDisplay . ' · ' : '') . $targetEnvName;
                        }

                        $isDone = in_array($chk->status, ['completed', 'approved', 'skipped', 'done'])
                            || !empty($chk->signature)
                            || !empty($chk->approved_at)
                            || !empty($chk->generated_ticket_id)
                            || !empty($chk->generated_at)
                            || in_array($chk->generate_status, ['SUCCESS', 'SKIPPED', 'completed'])
                            || ((bool) $ticket->checked && ($ticket->checker_id == $chk->karyawan_id || $ticket->checker_name == $chk->karyawan_name));
                        $cmtChecker = $stepComment('checker', $chk->karyawan_id);
                    @endphp
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between position-relative"
                             style="{{ $isCross ? 'border-color:#99f6e4 !important; background:#f0fdfa !important;' : 'border-color:#e2e8f0 !important; background:#ffffff !important;' }}">
                            <div>
                                <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">
                                    DIPERIKSA OLEH{{ $checkerRows->count() > 1 ? ('-' . ($loop->index + 1)) : '' }}:
                                </small>
                                @if($isCross)
                                <span class="badge" style="background:#ccfbf1; color:#0f766e; font-size:7.5px; font-weight:700; border:1px solid #99f6e4; padding:1px 5px; border-radius:4px; margin-top:2px;" title="Pemeriksaan lintas sistem (bersifat independen / non-blocking)">
                                    <i class="fa-solid fa-arrows-split-up-and-left me-1"></i>Lintas Sistem
                                </span>
                                @endif
                            </div>
                            <div class="py-2 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                @if(!empty($chk->signature))
                                <img src="{{ asset('storage/' . $chk->signature) }}"
                                    style="max-height:45px; max-width:100%; object-fit:contain;">
                                <span class="badge bg-success mt-1" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                                @elseif($isDone)
                                <span class="badge bg-success" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                                @if(!empty($chk->generated_ticket_id) || $chk->generate_status === 'SUCCESS')
                                <small class="text-success d-block mt-1" style="font-size:8px; font-weight:600;"><i class="fa-solid fa-ticket me-1"></i>Generate Tiket Lanjutan</small>
                                @elseif($chk->generate_status === 'SKIPPED')
                                <small class="text-muted d-block mt-1" style="font-size:8px;"><i class="fa-solid fa-minus me-1"></i>Tidak Generate Tiket</small>
                                @endif
                                @else
                                <span class="badge bg-warning text-dark" style="font-size:9px;"><i class="fa-solid fa-clock me-1"></i>Menunggu Pemeriksaan</span>
                                @if($isCross)
                                <small style="font-size:7.5px; color:#0f766e; font-weight:600; line-height:1.1; margin-top:3px;">
                                    Belum diperiksa<br><span style="color:#64748b; font-weight:400;">(Non-blocking)</span>
                                </small>
                                @endif
                                @endif
                            </div>
                            <div>
                                <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                    {{ $chk->karyawan_name ?? ($chk->karyawan?->name ?? '-') }}
                                </small>
                                @if(!empty($ruleDisplay))
                                <span style="font-size:8px; color:#4338ca !important; font-weight:600; line-height:1.2; display:block; margin-top:2px;">({{ $ruleDisplay }})</span>
                                @endif
                                @php
                                    $chkDate = $chk->approved_at ?? ($chk->status === 'completed' || $chk->status === 'approved' ? $chk->updated_at : null);
                                @endphp
                                @if($chkDate)
                                <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                    <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($chkDate)->translatedFormat('d M Y, H:i') }}
                                </span>
                                @endif
                                @if($cmtChecker)
                                <div class="text-start mt-1" title="{{ $cmtChecker->comment_text }}"
                                    style="background:#fff; border:1px solid #e9d5ff; border-radius:6px; padding:3px 6px; font-size:8.5px; color:#4b5563; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                    <i class="fa-solid fa-comment-dots me-1" style="color:#a78bfa;"></i>{{ $cmtChecker->comment_text }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    @php
                        $singleDone = (bool) $ticket->checked || !empty($ticket->checker_signature) || !empty($ticket->checked_at);
                        $cmtChecker = $stepComment('checker');
                    @endphp
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                             style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                            <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIPERIKSA OLEH:</small>
                            <div class="py-2 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                                @if(!empty($ticket->checker_signature))
                                <img src="/storage/{{ $ticket->checker_signature }}"
                                    style="max-height:45px; max-width:100%; object-fit:contain;">
                                <span class="badge bg-success mt-1" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                                @elseif($singleDone)
                                <span class="badge bg-success" style="font-size:9px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>
                                @else
                                <span class="badge bg-warning text-dark" style="font-size:9px;"><i class="fa-solid fa-clock me-1"></i>Menunggu Pemeriksaan</span>
                                @endif
                            </div>
                            <div>
                                <small class="fw-bold text-break d-block" style="font-size:9.5px; line-height:1.2; color:#0f172a !important; font-weight:700;">
                                    {{ $ticket->checker_name ?? 'JM FINANCE & ACCOUNTING' }}
                                </small>
                                @if($ticket->checked_at)
                                <span class="d-block" style="font-size:8px; margin-top:2px; color:#64748b !important;">
                                    <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ticket->checked_at)->translatedFormat('d M Y, H:i') }}
                                </span>
                                @endif
                                @if($cmtChecker)
                                <div class="text-start mt-1" title="{{ $cmtChecker->comment_text }}"
                                    style="background:#fff; border:1px solid #e9d5ff; border-radius:6px; padding:3px 6px; font-size:8.5px; color:#4b5563; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                    <i class="fa-solid fa-comment-dots me-1" style="color:#a78bfa;"></i>{{ $cmtChecker->comment_text }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded-3 text-center h-100 d-flex flex-column justify-content-between"
                         style="{{ $ticket->validator ? 'background: #ffffff !important; border-color: #e2e8f0 !important;' : 'border-color:#c4b5fd !important; background:#faf5ff !important;' }}">
                        <small class="d-block" style="font-size:8px; color:#475569 !important; font-weight:700;">DIVALIDASI OLEH:</small>
                        <div class="py-2 flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                            @if($ticket->validator_signature)
                            <img src="/storage/{{ $ticket->validator_signature }}"
                                style="max-height:45px; max-width:100%; object-fit:contain;">
                            @elseif(!$ticket->validator)
                            <span class="badge" style="background:#ede9fe; color:#6d28d9; font-size:9px;"><i class="fa-solid fa-pen-nib me-1"></i>Menunggu Validasi</span>
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
                            @if($cmtValidator)
                            <div class="text-start mt-1" title="{{ $cmtValidator->comment_text }}"
                                style="background:#fff; border:1px solid #e9d5ff; border-radius:6px; padding:3px 6px; font-size:8.5px; color:#4b5563; line-height:1.3; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                                <i class="fa-solid fa-comment-dots me-1" style="color:#a78bfa;"></i>{{ $cmtValidator->comment_text }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="field-readonly no-icon" style="font-size: 12px; gap: 8px;">
                <i class="fa-regular fa-calendar me-2" style="color:#a78bfa;"></i>
                Tanggal Pengajuan:&nbsp;
                <strong>{{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y H:i:s') : '-' }}</strong>
            </div>
            @if($ticket->validator)
            <div class="validated-banner">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <div class="vb-title">Ticket telah divalidasi</div>
                    <div class="vb-sub">
                        oleh {{ $ticket->validator_name }}
                        · {{ \Carbon\Carbon::parse($ticket->validator_at)->translatedFormat('d M Y H:i') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
        @if($ticket->comments && $ticket->comments->count() > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">4</span>
                <h6 class="mb-0 fw-bold">Catatan &amp; Riwayat Approval</h6>
            </div>
            @php
            $levelMap = [
            'approve1' => ['label' => 'Approver 1', 'icon' => 'fa-user-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
            'approve2' => ['label' => 'Approver 2', 'icon' => 'fa-user-check', 'color' => '#7c3aed', 'bg' => '#f5f3ff', 'border' => '#c4b5fd'],
            'checker' => ['label' => 'Checker', 'icon' => 'fa-magnifying-glass', 'color' => '#0369a1', 'bg' => '#f0f9ff', 'border' => '#bae6fd'],
            'validator' => ['label' => 'Validator', 'icon' => 'fa-shield-halved', 'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
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
            'feedback' => ['label' => 'Feedback', 'icon' => 'fa-comment', 'color' => '#6b7280'],
            'assign' => ['label' => 'Ditugaskan', 'icon' => 'fa-user-plus', 'color' => '#1d4ed8'],
            ];
            @endphp

            @foreach($ticket->comments as $comment)
            @php
            $lv = $levelMap[$comment->approval_level] ?? ['label' => $comment->approval_level, 'icon' => 'fa-comment', 'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'];
            $ac = $actionMap[$comment->action_ticket] ?? ['label' => $comment->action_ticket, 'icon' => 'fa-circle', 'color' => '#6b7280'];
            @endphp
            <div style="background:{{ $lv['bg'] ?? '#f9fafb' }}; border:1.5px solid {{ $lv['border'] ?? '#e5e7eb' }};
                border-radius:14px; padding:12px 14px; margin-bottom:8px;">
                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:28px; height:28px; border-radius:50%;
                                background:{{ $lv['color'] ?? '#6b7280' }}; color:#fff;
                                display:inline-flex; align-items:center;
                                justify-content:center; font-size:11px; flex-shrink:0;">
                            <i class="fa-solid {{ $lv['icon'] }}"></i>
                        </span>
                        <div>
                            <div style="font-size:11px; font-weight:700; color:{{ $lv['color'] ?? '#6b7280' }};">
                                {{ $lv['label'] }}
                            </div>
                            <div style="font-size:10px; color:#6b7280;">
                                {{ $comment->karyawan?->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div style="font-size:10px; font-weight:700; color:{{ $ac['color'] ?? '#6b7280' }};">
                            <i class="fa-solid {{ $ac['icon'] }} me-1"></i>{{ $ac['label'] }}
                        </div>
                        <div style="font-size:9px; color:#9ca3af;">
                            {{ $comment->comment_at
                        ? \Carbon\Carbon::parse($comment->comment_at)->translatedFormat('d M Y, H:i')
                        : '-' }}
                        </div>
                    </div>
                </div>
                @if($comment->comment_text)
                <div style="background:#fff; border:1px solid {{ $lv['border'] }};
                    border-radius:8px; padding:8px 12px;
                    font-size:12px; color:#374151; line-height:1.6;">
                    {{ $comment->comment_text }}
                </div>
                @else
                <div style="font-size:11px; color:#9ca3af; font-style:italic;">
                    <i class="fa-solid fa-minus me-1"></i>Tidak ada catatan
                </div>
                @endif

            </div>
            @endforeach
        </div>
        @endif
        @if($ticket->all_lines_cancelled)
        <div class="alert alert-danger d-flex align-items-start gap-2 rounded-3 mb-3" style="font-size:12px;">
            <i class="fa-solid fa-ban mt-1"></i>
            <div>
                <strong>Ticket dibatalkan semua.</strong>
                Seluruh baris perubahan ditandai tidak perlu diproses oleh checker.
                Silakan <strong>Tolak</strong> ticket ini, atau kembalikan minimal satu baris
                pada detail perubahan bila pembatalan tidak tepat.
            </div>
        </div>
        @endif
        @php
            $isCanValidate = $can_validate ?? (
                (empty($ticket->validator) || !$ticket->validator)
                && empty($ticket->validator_id)
                && $ticket->approval_status === \App\Models\TicketHead::APPROVAL_APPROVED
            );
            $pendingCount = $group_pending_count ?? 1;
        @endphp

        @if($isCanValidate)
        <div class="validator-action-card">
            @if($pendingCount > 1)
            <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-3" style="background:#faf5ff; border:1px dashed #c4b5fd; font-size:11.5px; color:#5b21b6;">
                <i class="fa-solid fa-layer-group text-primary fs-6 ms-1"></i>
                <div>
                    Aksi ini akan memvalidasi/menolak seluruh <strong>{{ $pendingCount }} ticket</strong> dalam group <strong>#{{ $root_ticket_nomor ?? $ticket->nomor_ticket }}</strong> sekaligus.
                </div>
            </div>
            @endif
            <div class="d-flex gap-2">
                <button type="button" class="btn-validator-reject flex-fill" id="btn-action-reject">
                    <i class="fa-solid fa-circle-xmark me-1"></i>Tolak {{ $pendingCount > 1 ? '(' . $pendingCount . ')' : '' }}
                </button>
                @if(!$ticket->all_lines_cancelled)
                    @if($ticket->ticket_status === \App\Models\TicketHead::STATUS_PENDING_EXTERNAL)
                    <button type="button" class="btn-validator-resume flex-fill" id="btn-action-resume">
                        <i class="fa-solid fa-play me-1"></i>Lanjutkan
                    </button>
                    @else
                    <button type="button" class="btn-validator-pending flex-fill" id="btn-trigger-pending">
                        <i class="fa-solid fa-hourglass-half me-1"></i>Pending {{ $pendingCount > 1 ? '(' . $pendingCount . ')' : '' }}
                    </button>
                    @endif
                    <button type="button" class="btn-validator-approve flex-fill" id="btn-action-validate">
                        <i class="fa-solid fa-shield-check me-1"></i>Validasi {{ $pendingCount > 1 ? '(' . $pendingCount . ')' : '' }}
                    </button>
                @endif
            </div>
            <a href="{{ route('approval.ticket.validator.index', $menu_id) }}"
                class="btn btn-link w-100 text-decoration-none mt-2 py-1"
                style="font-size:12px; color:#64748b; font-weight:600;">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Daftar Validasi
            </a>
        </div>
        @else
        <a href="{{ route('approval.ticket.validator.index', $menu_id) }}"
            class="btn btn-outline-secondary w-100 rounded-pill fw-bold"
            style="font-size:13px;">
            <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar Validasi
        </a>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-change-detail"
    style="height: 85vh; z-index: 1090 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="futuristic-icon shadow-sm me-3" style="width:45px; height:45px; font-size:18px;">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bold text-dark">Detail Transaksi</h6>
                <small class="text-muted" id="change-detail-subtitle">Transaksi #1</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">
        <div class="mb-3" id="oc-wrap-reference" style="display:none;">
            <label class="form-label fw-semibold" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-link me-1"></i> Referensi Detail Sebelumnya
            </label>
            <div class="field-readonly" id="oc-reference" style="padding-left:14px; background:#f5f3ff; border-color:#ddd6fe; color:#5b21b6;">
                <span class="field-empty">-</span>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-hashtag me-1"></i> Nomor Transaksi
            </label>
            <div class="field-readonly" id="oc-nomor" style="padding-left:14px;">
                <span class="field-empty">-</span>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-calendar-days me-1"></i> Tanggal Transaksi
            </label>
            <div class="field-readonly" id="oc-tanggal" style="padding-left:14px;">
                <span class="field-empty">-</span>
            </div>
        </div>

        <label class="form-label fw-semibold mt-2" style="font-size: 11px; color: #6b21a8; text-transform:uppercase; letter-spacing:.5px;">
            <i class="fa-solid fa-list-ol me-1"></i> Baris Perubahan
        </label>
        <div id="oc-sub-detail-list" class="d-flex flex-column gap-2 mb-2"></div>
        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill mt-2"
            data-bs-dismiss="offcanvas">
            <i class="fa-solid fa-xmark me-1"></i> Tutup
        </button>
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
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2"
                data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH LAMPIRAN VALIDATOR -->
<div class="modal fade" id="modal-add-attachment" tabindex="-1" aria-hidden="true" style="z-index: 1095;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <div class="modal-header border-bottom py-3" style="background:linear-gradient(135deg, #7c3aed, #9333ea); color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:16px;">
                        <i class="fa-solid fa-paperclip"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white">Tambah Lampiran</h6>
                        <small style="font-size:11px; opacity:.9;">Lengkapi dokumen pendukung untuk tiket #{{ $ticket->nomor_ticket }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-upload-attachment" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:12px; color:#4b5563;">
                            <i class="fa-solid fa-file-arrow-up me-1 text-primary"></i> Pilih File Lampiran <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="attachments[]" id="input-attachments" class="form-control" multiple
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z" required
                            style="font-size:12px; border-radius:10px; padding:10px;">
                        <small class="text-muted d-block mt-1" style="font-size:11px;">
                            Bisa memilih lebih dari satu file (PDF, Gambar, Excel, Word, Zip). Maksimal 20MB per file.
                        </small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size:12px; color:#4b5563;">
                            <i class="fa-solid fa-comment-dots me-1 text-primary"></i> Catatan Dokumen (Opsional)
                        </label>
                        <textarea name="notes" id="input-att-notes" class="form-control" rows="2"
                            placeholder="Contoh: Faktur pajak asli atau dokumen pendukung dari vendor/finance..."
                            style="font-size:12px; border-radius:10px;"></textarea>
                    </div>
                    <div id="upload-progress-wrap" style="display:none;" class="mt-3">
                        <div class="d-flex align-items-center gap-2 text-primary" style="font-size:12px;">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span>Sedang mengunggah lampiran...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold" id="btn-submit-att" style="background:linear-gradient(135deg, #7c3aed, #6d28d9); border:none;">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Unggah Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL / OFFCANVAS SIGNATURE VALIDASI & TOLAK (GROUP) -->
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1"
    id="off-canvas-sig-validate" style="height:78vh; z-index:1095 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center gap-3">
            <div id="sig-icon-wrap" style="width:40px; height:40px; border-radius:12px;
                        background:linear-gradient(135deg,#7c3aed,#a855f7);
                        display:flex; align-items:center; justify-content:center;
                        color:#fff; font-size:16px;
                        box-shadow:0 4px 12px rgba(124,58,237,.3); flex-shrink:0;">
                <i class="fa-solid fa-file-signature" id="sig-header-icon"></i>
            </div>
            <div>
                <div class="fw-bold" style="font-size:13px; color:#1e1b4b;" id="sig-modal-title">Konfirmasi Validasi Ticket</div>
                <small class="text-muted" id="sig-modal-subtitle" style="font-size:11px;">#{{ $root_ticket_nomor ?? $ticket->nomor_ticket }} · {{ $group_pending_count ?? 1 }} ticket</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">

        <div id="alert_sig" class="mb-2"></div>

        <!-- Signature Pad -->
        <div class="sig-approver-card mb-3">
            <div class="sig-approver-header">
                <small>Tanda tangan validator: {{ $user_karyawan->name }}</small>
            </div>
            <div class="alert alert-danger mx-2 mt-2 py-2 px-3 rounded-3"
                id="alert-sig-empty" style="font-size:12px; display:none;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>Tanda tangan wajib diisi.
            </div>
            <canvas id="sig-pad-detail"></canvas>
            <div class="sig-footer">
                <button type="button" id="btn-sig-clear-detail"
                    class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                    <i class="fa-solid fa-eraser me-1"></i> Hapus
                </button>
                <small class="text-muted" style="font-size:10px;">
                    <i class="fa-solid fa-hand-pointer me-1"></i>Tanda tangan di atas
                </small>
            </div>
        </div>

        <!-- Alasan Reject (Wajib saat Tolak) -->
        <div id="reject-reason-wrap" style="display:none; margin-bottom:14px;">
            <label class="form-label fw-bold small text-danger mb-1">
                <i class="fa-solid fa-circle-exclamation me-1"></i> Alasan Penolakan <span class="text-danger">*</span>
            </label>
            <div style="position:relative;">
                <span style="position:absolute; left:14px; top:14px; color:#ef4444; font-size:13px; z-index:6; pointer-events:none;">
                    <i class="fa-solid fa-comment-dots"></i>
                </span>
                <textarea id="alasan-reject"
                    class="form-control"
                    placeholder="Alasan penolakan (wajib diisi minimal 10 karakter)..."
                    style="border-radius:12px; border:1.5px solid #fecaca; font-size:13px;
                           padding:12px 14px 12px 42px; resize:none; min-height:75px;
                           background:#fff5f5; width:100%;"></textarea>
            </div>
        </div>

        <!-- Catatan Validator (Opsional) -->
        <div class="mb-3" style="position:relative;">
            <label class="form-label fw-bold small text-muted mb-1" id="label-catatan">
                <i class="fa-solid fa-comment-dots me-1 text-primary"></i> Catatan Validasi (Opsional)
            </label>
            <div style="position:relative;">
                <span style="position:absolute; left:14px; top:14px; color:#7c3aed; font-size:13px; z-index:6; pointer-events:none;">
                    <i class="fa-solid fa-pen"></i>
                </span>
                <textarea id="sig-catatan"
                    class="form-control"
                    placeholder="Catatan validasi finance &amp; accounting (opsional)..."
                    style="border-radius:12px; border:1.5px solid #ede9fe; font-size:13px;
                           padding:12px 14px 12px 42px; resize:none; min-height:70px;
                           background:#fdfcff; width:100%;"></textarea>
            </div>
        </div>

        <!-- Action Confirmation Buttons -->
        <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 flex-fill" data-bs-dismiss="offcanvas">
                Batal
            </button>
            <button type="button" id="btn-confirm-submit"
                class="btn rounded-pill px-4 fw-bold text-white flex-fill"
                style="background:linear-gradient(135deg, #7c3aed, #9333ea); border:none; box-shadow:0 4px 14px rgba(124, 58, 237, .3); padding:11px 18px;">
                <i class="fa-solid fa-shield-check me-1"></i> Konfirmasi Validasi
            </button>
        </div>

    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /* Pembatalan per baris — validator bisa membatalkan / mengembalikan baris sebelum ticket divalidasi */
    const CAN_EDIT_LINES = @json(!$ticket->validator && $ticket->approval_status !== \App\Models\TicketHead::APPROVAL_REJECTED);
    const LINE_URL = "{{ route('approval.ticket.line.cancel', $ticket->id) }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";
    const UPLOAD_ATT_URL = "{{ route('approval.ticket.validator.attachment.upload', $ticket->id) }}";
    const DELETE_ATT_BASE_URL = "{{ url('approval/validator/attachment') }}";
    const PENDING_URL = "{{ route('approval.ticket.validator.pending', $ticket->id) }}";
    const RESUME_URL = "{{ route('approval.ticket.validator.resume', $ticket->id) }}";
    const GROUP_SAVE_URL = "{{ route('approval.ticket.validator.group.save', $menu_id) }}";
    const ROOT_TICKET_ID = "{{ $root_ticket_id ?? ($ticket->root_ticket_id ?: $ticket->id) }}";
    const ROOT_TICKET_NOMOR = "{{ $root_ticket_nomor ?? $ticket->nomor_ticket }}";
    const GROUP_PENDING_COUNT = {{ $group_pending_count ?? 1 }};

    $('<style>.swal2-container{z-index:2100 !important;}</style>').appendTo('head');
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        // ── 1. Upload Attachment Validator ──
        $('#form-upload-attachment').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const $btn = $('#btn-submit-att');
            const origHtml = $btn.html();

            $btn.prop('disabled', true);
            $('#upload-progress-wrap').show();

            $.ajax({
                url: UPLOAD_ATT_URL,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modal-add-attachment').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Lampiran berhasil diunggah.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#upload-progress-wrap').hide();
                    const msg = xhr.responseJSON?.message || 'Gagal mengunggah lampiran.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: msg
                    });
                }
            });
        });

        // ── 2. Delete Attachment Validator ──
        $(document).on('click', '.btn-delete-att', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const attId = $(this).data('id');
            if (!attId) return;

            Swal.fire({
                title: 'Hapus Lampiran?',
                text: 'Lampiran yang Anda unggah akan dihapus dari tiket ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${DELETE_ATT_BASE_URL}/${attId}`,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: CSRF_TOKEN
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message || 'Lampiran berhasil dihapus.',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.message || 'Gagal menghapus lampiran.';
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        });

        // ── 3. Pending Validasi Tiket ──
        $('#btn-trigger-pending').on('click', function() {
            const countLabel = GROUP_PENDING_COUNT > 1 ? ` (${GROUP_PENDING_COUNT} Ticket)` : '';
            Swal.fire({
                title: `Pending Validasi${countLabel}?`,
                html: `
                    <p class="text-muted small mb-2">${GROUP_PENDING_COUNT > 1 ? `Seluruh ${GROUP_PENDING_COUNT} tiket dalam group ini akan dipending sementara.` : 'Tiket akan dipending sementara.'}</p>
                    <div class="text-start mt-2">
                        <label class="form-label small fw-bold text-dark">Alasan Pending: <span class="text-danger">*</span></label>
                        <textarea id="swal-pending-reason" class="form-control" rows="3" placeholder="Contoh: Menunggu faktur pajak asli dan purchase order revisi dari pemohon..." style="font-size:12px;"></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-hourglass-half me-1"></i> Ya, Pending',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const reason = $('#swal-pending-reason').val()?.trim();
                    if (!reason) {
                        Swal.showValidationMessage('Alasan pending wajib diisi!');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const postUrl = GROUP_PENDING_COUNT > 1 ? GROUP_SAVE_URL : PENDING_URL;
                    const postData = GROUP_PENDING_COUNT > 1
                        ? {
                            _token: CSRF_TOKEN,
                            root_ticket_id: ROOT_TICKET_ID,
                            action: 'pending',
                            alasan_pending: result.value
                        }
                        : {
                            _token: CSRF_TOKEN,
                            reason: result.value
                        };

                    $.ajax({
                        url: postUrl,
                        type: 'POST',
                        data: postData,
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tiket Dipending',
                                text: res.message || 'Status tiket berhasil diubah menjadi pending.',
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.message || 'Gagal mem-pending tiket.';
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        });

        // ── 4. Resume Validasi Tiket ──
        $('#btn-resume-ticket, #btn-action-resume').on('click', function() {
            Swal.fire({
                title: 'Lanjutkan Validasi?',
                text: 'Status tiket akan dikembalikan ke antrean validasi aktif dan waktu SLA validasi akan kembali berjalan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-play me-1"></i> Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: RESUME_URL,
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dilanjutkan!',
                                text: res.message || 'Tiket siap untuk divalidasi.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON?.message || 'Gagal melanjutkan validasi tiket.';
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
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

        document.getElementById('off-canvas-change-detail')
            .addEventListener('show.bs.offcanvas', function(e) {
                const trigger = e.relatedTarget;
                if (!trigger) return;
                const idx = trigger.dataset.idx || '?';
                const nomor = trigger.dataset.nomor || '';
                const tanggal = trigger.dataset.tanggal || '';
                const refNomor = trigger.dataset.refNomor || '';
                const refTanggal = trigger.dataset.refTanggal || '';
                let subDetails = [];
                try {
                    subDetails = JSON.parse(trigger.dataset.subdetails || '[]');
                } catch (e) {
                    subDetails = [];
                }
                $('#change-detail-subtitle').text('Transaksi #' + idx);
                function fillField(id, val) {
                    const el = $(id);
                    if (val && val.trim() !== '') {
                        el.html(val.replace(/\n/g, '<br>'));
                    } else {
                        el.html('<span class="field-empty">Tidak diisi</span>');
                    }
                }
                if (refNomor || refTanggal) {
                    const refText = [refNomor, refTanggal ? '(' + refTanggal + ')' : ''].filter(Boolean).join(' ');
                    $('#oc-reference').html(`<i class="fa-solid fa-link me-1 text-primary"></i>` + escHtml(refText));
                    $('#oc-wrap-reference').show();
                } else {
                    $('#oc-wrap-reference').hide();
                }
                fillField('#oc-nomor', nomor);
                fillField('#oc-tanggal', tanggal);
                const wrap = $('#oc-sub-detail-list');
                wrap.empty();
                if (subDetails.length === 0) {
                    wrap.append(`
                <div class="text-center text-muted py-3 rounded-3 border border-dashed"
                    style="font-size:11px; border-color:#d8b4fe !important;">
                    Tidak ada baris perubahan
                </div>`);
                    return;
                }
                subDetails.forEach((sd, j) => {
                    const nomor_urut = j + 1;
                    const collapseId = 'collapse_sub_' + j;
                    const line_number = sd.line_number && sd.line_number.trim() !== '' ?
                        sd.line_number.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const alasan = sd.alasan_ubah && sd.alasan_ubah.trim() !== '' ?
                        sd.alasan_ubah.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const before = sd.before_ubah && sd.before_ubah.trim() !== '' ?
                        sd.before_ubah.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const after = sd.after_ubah && sd.after_ubah.trim() !== '' ?
                        sd.after_ubah.replace(/\n/g, '<br>') :
                        '<span class="field-empty">Tidak diisi</span>';
                    const isFirst = j === 0;
                    const roleLabel = sd.cancelled_by_role === 'checker' ? 'oleh Checker'
                        : (sd.cancelled_by_role === 'validator' ? 'oleh Validator' : 'otomatis (mengikuti transaksi induk)');
                    let lineAction = '';
                    if (sd.is_cancelled) {
                        lineAction = `
                                <div class="mt-2 p-2 rounded-2" style="background:#fef2f2; border:1px dashed #fca5a5; font-size:11.5px;">
                                    <div class="fw-bold" style="color:#b91c1c;"><i class="fa-solid fa-ban me-1"></i>Dibatalkan ${roleLabel}${sd.cancelled_by_name ? ' (' + escHtml(sd.cancelled_by_name) + ')' : ''}</div>
                                    <div class="mt-1" style="color:#7f1d1d;">${escHtml(sd.cancel_reason || '-')}</div>
                                    ${CAN_EDIT_LINES && sd.id ? `<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-2 btn-line-restore" data-sub-id="${escHtml(sd.id)}"><i class="fa-solid fa-rotate-left me-1"></i>Kembalikan baris</button>` : ''}
                                </div>`;
                    } else if (CAN_EDIT_LINES && sd.id) {
                        lineAction = `
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-line-cancel" data-sub-id="${escHtml(sd.id)}"><i class="fa-solid fa-ban me-1"></i>Batalkan baris ini</button>
                                </div>`;
                    }

                    wrap.append(`
                    <div class="subdetail-accordion-item">
                        <div class="subdetail-accordion-header ${isFirst ? '' : 'collapsed'}"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="${isFirst ? 'true' : 'false'}"
                            aria-controls="${collapseId}">
                            <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                                <span class="badge" style="background:#ede9fe; color:#6b21a8; font-size:10px;">
                                    <i class="fa-solid fa-list-ol me-1"></i> Line #${nomor_urut}
                                </span>
                                <span class="text-truncate fw-semibold" style="font-size:11px; color:#4b5563; ${sd.is_cancelled ? 'text-decoration:line-through; opacity:.6;' : ''}">
                                    ${escHtml(sd.alasan_ubah || (sd.line_number ? 'Line: ' + sd.line_number : 'Perubahan Data'))}
                                </span>
                                ${sd.is_cancelled ? '<span class="badge bg-danger flex-shrink-0" style="font-size:9.5px;"><i class="fa-solid fa-ban me-1"></i>Dibatalkan</span>' : ''}
                            </div>
                            <div class="subdetail-chevron">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                        </div>
                        <div id="${collapseId}" class="collapse ${isFirst ? 'show' : ''}">
                            <div class="subdetail-accordion-body">
                                <div class="mb-2">
                                    <small class="d-block text-muted mb-1" style="font-size:10px;"><i class="fa-solid fa-circle-info me-1"></i>Line Detail / Keterangan</small>
                                    <div class="field-readonly textarea-view no-icon" style="font-size:12px;">${line_number}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="d-block text-muted mb-1" style="font-size:10px;"><i class="fa-solid fa-comment-dots me-1"></i>Alasan Perubahan</small>
                                    <div class="field-readonly textarea-view no-icon" style="font-size:12px;">${alasan}</div>
                                </div>
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <small class="d-block text-danger fw-bold mb-1" style="font-size:10px;"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum Perubahan</small>
                                        <div class="field-readonly textarea-view no-icon" style="font-size:12px; background:#fef2f2; border-color:#fee2e2;">${before}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="d-block text-success fw-bold mb-1" style="font-size:10px;"><i class="fa-solid fa-circle-check me-1"></i>Setelah Perubahan</small>
                                        <div class="field-readonly textarea-view no-icon" style="font-size:12px; background:#f0fdf4; border-color:#dcfce7;">${after}</div>
                                    </div>
                                </div>
                                ${lineAction}
                            </div>
                        </div>
                    </div>`);
                });
            });

        function postLine(subId, cancelled, reason) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const items = {};
            items[subId] = { cancelled: cancelled ? 1 : 0, reason: reason || '' };
            $.ajax({
                url: LINE_URL,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                data: { items: items },
                success(d) {
                    if (d.code == 200) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1400, showConfirmButton: false })
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire({ icon: d.code == 422 ? 'warning' : 'error', title: 'Perhatian', html: d.message || 'Gagal memproses.' });
                    }
                },
                error(xhr) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi gangguan saat memproses.' });
                }
            });
        }
        $(document).on('click', '.btn-line-cancel', function() {
            const id = String($(this).attr('data-sub-id'));
            Swal.fire({
                target: document.getElementById('off-canvas-change-detail'), // <-- tambahan
                title: 'Batalkan baris ini?',
                input: 'textarea',
                inputLabel: 'Keterangan (wajib)',
                inputPlaceholder: 'mis. sudah benar / tidak jadi diajukan',
                inputAttributes: { maxlength: 500 },
                showCancelButton: true,
                confirmButtonText: 'Batalkan baris',
                cancelButtonText: 'Kembali',
                inputValidator: v => (!v || !v.trim()) ? 'Keterangan wajib diisi' : undefined
            }).then(r => { if (r.isConfirmed) postLine(id, true, r.value.trim()); });
        });
        $(document).on('click', '.btn-line-restore', function() {
            const id = String($(this).attr('data-sub-id'));
            Swal.fire({
                title: 'Kembalikan baris ini?',
                text: 'Baris akan kembali ikut diproses.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, kembalikan',
                cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) postLine(id, false, ''); });
        });

        // ── 5. Signature & Group Validation / Reject ──
        let currentAction = 'validate';
        const canvasDetail = document.getElementById('sig-pad-detail');
        let sigPadDetail = null;
        if (canvasDetail) {
            sigPadDetail = new SignaturePad(canvasDetail);

            function resizeSigCanvasDetail() {
                if (!canvasDetail || !sigPadDetail) return;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvasDetail.width = canvasDetail.offsetWidth * ratio;
                canvasDetail.height = canvasDetail.offsetHeight * ratio;
                canvasDetail.getContext('2d').scale(ratio, ratio);
                sigPadDetail.clear();
            }

            const offCanvasSig = document.getElementById('off-canvas-sig-validate');
            if (offCanvasSig) {
                offCanvasSig.addEventListener('shown.bs.offcanvas', resizeSigCanvasDetail);
            }
            window.addEventListener('resize', resizeSigCanvasDetail);

            $('#btn-sig-clear-detail').on('click', () => {
                sigPadDetail.clear();
                $('#alert-sig-empty').hide();
            });
        }

        /* ── Klik Tombol Validasi ── */
        $('#btn-action-validate').on('click', function(e) {
            e.preventDefault();
            currentAction = 'validate';

            $('#sig-icon-wrap').css({
                'background': 'linear-gradient(135deg,#7c3aed,#a855f7)',
                'box-shadow': '0 4px 12px rgba(124,58,237,.3)'
            });
            $('#sig-header-icon').attr('class', 'fa-solid fa-file-signature');
            $('#sig-modal-title').text(
                GROUP_PENDING_COUNT > 1
                    ? `Validasi Semua Tiket Sekaligus (${GROUP_PENDING_COUNT} Ticket)`
                    : 'Validasi Ticket'
            );
            $('#sig-modal-subtitle').text(
                `#${ROOT_TICKET_NOMOR} · Validasi akan menyetujui seluruh tiket dalam grup ini`
            );

            $('#reject-reason-wrap').hide();
            $('#alasan-reject').val('');
            $('#label-catatan').html('<i class="fa-solid fa-comment-dots me-1 text-primary"></i> Catatan Validasi (Opsional)');
            $('#sig-catatan').attr('placeholder', 'Catatan validasi finance & accounting (opsional)...');

            $('#btn-confirm-submit').css({
                'background': 'linear-gradient(135deg,#7c3aed,#9333ea)',
                'box-shadow': '0 4px 14px rgba(124, 58, 237, .3)'
            }).html('<i class="fa-solid fa-shield-check me-2"></i>Konfirmasi Validasi');

            $('#alert_sig').empty();
            $('#alert-sig-empty').hide();
            if (sigPadDetail) sigPadDetail.clear();

            new bootstrap.Offcanvas(document.getElementById('off-canvas-sig-validate')).show();
        });

        /* ── Klik Tombol Tolak ── */
        $('#btn-action-reject').on('click', function(e) {
            e.preventDefault();
            currentAction = 'reject';

            $('#sig-icon-wrap').css({
                'background': 'linear-gradient(135deg,#dc2626,#ef4444)',
                'box-shadow': '0 4px 12px rgba(220,38,38,.3)'
            });
            $('#sig-header-icon').attr('class', 'fa-solid fa-circle-xmark');
            $('#sig-modal-title').text(
                GROUP_PENDING_COUNT > 1
                    ? `Tolak Semua Tiket Sekaligus (${GROUP_PENDING_COUNT} Ticket)`
                    : 'Tolak Ticket'
            );
            $('#sig-modal-subtitle').text(
                `#${ROOT_TICKET_NOMOR} · Penolakan akan membatalkan seluruh tiket dalam grup ini`
            );

            $('#reject-reason-wrap').show();
            $('#alasan-reject').val(@json((bool) $ticket->all_lines_cancelled)
                ? 'Seluruh baris dibatalkan oleh checker (tidak perlu diproses).'
                : '');
            $('#label-catatan').html('<i class="fa-solid fa-comment-dots me-1 text-danger"></i> Catatan Tambahan (Opsional)');
            $('#sig-catatan').attr('placeholder', 'Catatan tambahan penolakan...');

            $('#btn-confirm-submit').css({
                'background': 'linear-gradient(135deg,#dc2626,#ef4444)',
                'box-shadow': '0 4px 14px rgba(220, 38, 38, .3)'
            }).html('<i class="fa-solid fa-circle-xmark me-2"></i>Konfirmasi Tolak');

            $('#alert_sig').empty();
            $('#alert-sig-empty').hide();
            if (sigPadDetail) sigPadDetail.clear();

            new bootstrap.Offcanvas(document.getElementById('off-canvas-sig-validate')).show();
        });

        /* ── Konfirmasi Submit Validasi / Tolak ── */
        $('#btn-confirm-submit').on('click', function() {
            if (!sigPadDetail || sigPadDetail.isEmpty()) {
                $('#alert-sig-empty').show();
                canvasDetail.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (currentAction === 'reject') {
                const alasan = $('#alasan-reject').val().trim();
                if (!alasan || alasan.length < 10) {
                    $('#alert_sig').html(
                        '<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">' +
                        '<i class="fa-solid fa-triangle-exclamation me-1"></i>Alasan penolakan wajib diisi minimal 10 karakter.</div>'
                    );
                    $('#alasan-reject').focus();
                    return;
                }
            }

            const $btn = $(this);
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            formData.append('root_ticket_id', ROOT_TICKET_ID);
            formData.append('action', currentAction);
            formData.append('signature', sigPadDetail.toDataURL());
            formData.append('comment_text', $('#sig-catatan').val().trim());

            if (currentAction === 'reject') {
                formData.append('alasan_reject', $('#alasan-reject').val().trim());
            }

            $.ajax({
                url: GROUP_SAVE_URL,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    const errorMsg = (window.getFriendlyErrorMessage ? window.getFriendlyErrorMessage(xhr, 'Terjadi gangguan saat memproses.') : (xhr.responseJSON?.message || 'Terjadi kesalahan.'));
                    $('#alert_sig').html(
                        `<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">${errorMsg}</div>`
                    );
                },
                success: function(data) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.code === 200) {
                        bootstrap.Offcanvas.getInstance(
                            document.getElementById('off-canvas-sig-validate')
                        )?.hide();

                        Swal.fire({
                            icon: currentAction === 'validate' ? 'success' : 'warning',
                            title: currentAction === 'validate' ? 'Berhasil Divalidasi' : 'Ticket Ditolak',
                            text: data.message,
                            timer: 1800,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = "{{ route('approval.ticket.validator.index', $menu_id) }}";
                        });
                    } else if (data.code === 422) {
                        const msgs = data.errors ?
                            Object.values(data.errors).flat().join('<br>') :
                            (data.message ?? 'Validasi gagal.');
                        $('#alert_sig').html(
                            `<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">${msgs}</div>`
                        );
                    } else {
                        $('#alert_sig').html(
                            `<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">${data.message || 'Gagal memproses validasi.'}</div>`
                        );
                    }
                }
            });
        });

        // window.onbeforeunload = function() {
        //     Swal.fire({
        //         allowOutsideClick: false,
        //         background: 'transparent',
        //         html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div>'.repeat(3),
        //         showCancelButton: false,
        //         showConfirmButton: false,
        //     });
        // };
    });
</script>
@endsection