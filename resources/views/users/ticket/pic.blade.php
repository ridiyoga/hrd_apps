@extends('users.layouts.main')
@section('title')
APPS | PIC Ticket
@endsection

@section('css')
<style>
    .futuristic-header {
        position: relative;
        padding: 28px 20px 18px;
    }

    .futuristic-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(145deg, #1d9e75, #30c79a);
        color: #fff;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 22px rgba(29, 158, 117, .45);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .summary-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        background: #fff;
        min-width: 0;
    }

    .summary-value {
        font-size: 19px;
        line-height: 1;
        font-weight: 800;
        color: #111827;
    }

    .summary-label {
        margin-top: 5px;
        font-size: 10px;
        color: #6b7280;
        font-weight: 700;
        text-transform: uppercase;
    }

    .filter-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .filter-tab {
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
        background: #fff;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 11px;
        font-weight: 800;
        color: #6b7280;
        cursor: pointer;
    }

    .filter-tab.active {
        background: #111827;
        color: #fff;
        border-color: #111827;
    }

    .form-control::placeholder,
    .swal2-textarea::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
    }

    .ticket-card {
        position: relative;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 14px;
        background: #fff;
        margin-bottom: 10px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    }

    .ticket-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
    }

    .ticket-no {
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        word-break: break-word;
    }

    .ticket-title {
        font-size: 13px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 5px;
    }

    .ticket-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 10px;
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .ticket-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-pending, .status-waiting, .status-review {
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

    .status-process, .status-ready, .status-progress {
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

    /* Environment Badges */
    .badge-env-epicor {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
    .badge-env-accurate-sp {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .badge-env-accurate-aja {
        background: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-row .btn {
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        padding: 7px 10px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* ── Ticket group: beberapa tiket berbagi 1 root_ticket_id, bisa beda PIC ── */
    .pic-group {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 12px;
        overflow: hidden;
        background: #f9fafb;
    }

    .pic-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        cursor: pointer;
        background: #f3f4f6;
    }

    .pic-group-header:hover {
        background: #eceef1;
    }

    .pic-group-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pic-group-sub {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }

    .pic-group-badges {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: flex-end;
    }

    .pic-group-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .pic-group-badge.mine {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .pic-group-badge.other {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .pic-group-badge.unassigned {
        background: #fcfdf0;
        color: #e3cd00;
        border: 1px solid #f7e9bb;
    }

    .pic-group-badge.pending {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .pic-group-toggle {
        color: #9ca3af;
        transition: transform .2s;
        font-size: 11px;
    }

    .pic-group.collapsed .pic-group-toggle {
        transform: rotate(-90deg);
    }

    .pic-group-body {
        padding: 10px;
    }

    .pic-group.collapsed .pic-group-body {
        display: none;
    }

    .pic-group-body .ticket-card {
        margin-bottom: 8px;
    }

    .pic-group-body .ticket-card:last-child {
        margin-bottom: 0;
    }

    .ticket-card.not-mine {
        background: #fcfcfd;
        opacity: .88;
    }

    .owner-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        white-space: nowrap;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .locked-note {
        font-size: 10px;
        color: #9ca3af;
        align-self: center;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .empty-state {
        padding: 36px 16px;
        text-align: center;
        color: #6b7280;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 10px;
        font-size: 12px;
        padding: 7px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .detail-label {
        color: #6b7280;
        font-weight: 800;
    }

    .detail-value {
        color: #111827;
        min-width: 0;
        word-break: break-word;
    }

    .change-accordion-item {
        background: #ffffff;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 8px;
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
    .change-card-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .change-card-info {
        flex: 1;
        min-width: 0;
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
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
        gap: 10px;
    }
    .subdetail-card {
        background: #ffffff;
        border: 1px solid #ede9fe;
        border-radius: 10px;
        padding: 12px;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.03);
    }
    .subdetail-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .subdetail-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .subdetail-field-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .subdetail-field-val {
        font-size: 12px;
        color: #1f2937;
        word-break: break-word;
        white-space: pre-wrap;
        user-select: text;
        padding: 2px 0;
    }
    .subdetail-line-badge {
        font-size: 11px;
        font-weight: 700;
        background: #ede9fe;
        color: #6b21a8;
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-block;
        width: fit-content;
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
    .subdetail-accordion-item {
        border: 1.5px solid #ede9fe;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 6px;
        background: #ffffff;
        transition: border-color 0.2s ease;
    }
    .subdetail-accordion-item:hover { border-color: #c4b5fd; }
    .subdetail-accordion-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        cursor: pointer;
        user-select: none;
        background: #fdfcff;
        gap: 8px;
        transition: background 0.15s ease;
    }
    .subdetail-accordion-header:hover { background: #f5f0ff; }
    .subdetail-accordion-header[aria-expanded="true"] {
        background: #f3e8ff;
        border-bottom: 1px solid #ede9fe;
    }
    .subdetail-chevron {
        color: #7c3aed;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ede9fe;
        flex-shrink: 0;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
    }
    .subdetail-accordion-header[aria-expanded="true"] .subdetail-chevron {
        transform: rotate(180deg);
        background: #7c3aed;
        color: #ffffff;
    }
    .subdetail-accordion-body { padding: 10px 12px; background: #faf9ff; }
    .field-empty-sd { color: #c4b5fd; font-style: italic; font-size: 11px; }
    .sd-field-view {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 11px;
        color: #374151;
        word-break: break-word;
        white-space: pre-wrap;
        line-height: 1.4;
    }
    .sd-field-before { background: #fef2f2; border-color: #fee2e2; }
    .sd-field-after  { background: #f0fdf4; border-color: #dcfce7; }
    .diff-box {
        border-radius: 8px;
        padding: 8px 12px;
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
        user-select: text;
    }

    .change-item,
    .comment-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
        background: #fff;
        font-size: 12px;
    }

    .comment-meta {
        color: #6b7280;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .line-cancel-box{background:#fef2f2;border:1px dashed #fca5a5;border-radius:8px;padding:8px 10px;font-size:11.5px;margin-bottom:8px}
    .line-cancel-box .lcb-title{font-weight:700;color:#b91c1c}
    .line-cancel-box .lcb-reason{margin-top:3px;color:#7f1d1d;white-space:pre-wrap;word-break:break-word}
    .is-line-cancelled{opacity:.72}
    .is-line-cancelled .diff-content,
    .is-line-cancelled .subdetail-field-val,
    .is-line-cancelled .field-readonly{text-decoration:line-through}

    .skeleton {
        display: block;
        width: 100%;
        height: 76px;
        border-radius: 8px;
        background: linear-gradient(90deg, #f3f4f6 25%, #fff 37%, #f3f4f6 63%);
        background-size: 400% 100%;
        animation: skPulse 1.3s ease-in-out infinite;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .55;
        }

        50% {
            opacity: 1;
        }
    }

    @media (max-width: 575px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ticket-top {
            flex-direction: column;
        }

        .detail-row {
            grid-template-columns: 1fr;
            gap: 3px;
        }
    }

    /* ── Lampiran ── */
    .lampiran-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 8px 12px;
        text-decoration: none;
        transition: all .18s;
        max-width: 100%;
    }

    .lampiran-box:hover {
        background: #dcfce7;
        border-color: #86efac;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(22, 163, 74, .1);
    }

    .lampiran-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background: #16a34a;
        color: #fff;
        font-size: 13px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lampiran-name {
        font-size: 11px;
        font-weight: 700;
        color: #14532d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .lampiran-meta {
        font-size: 10px;
        color: #6b7280;
    }

    .lampiran-none {
        font-size: 11px;
        color: #9ca3af;
        font-style: italic;
    }
</style>
@endsection

@section('content_top')
<div class="card">
    <div class="futuristic-header text-center">
        <div class="futuristic-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        <h5 class="mt-3 mb-1" style="font-size:18px; font-weight:800;">PIC TICKET</h5>
        <small class="text-muted">Daftar ticket yang ditugaskan untuk dikerjakan</small>
        <div class="mt-3">
            <a href="{{ url('approval/ticket/kpi/dashboard/' . ($userKaryawan->id ?? '')) }}" class="btn btn-sm rounded-3" style="background: linear-gradient(135deg, #7c3aed, #a855f7); color:#fff; font-weight:700; font-size:11px; padding: 8px 16px; box-shadow: 0 4px 12px rgba(124, 58, 237, .25);">
                <i class="fa-solid fa-chart-line me-1"></i> Lihat KPI Saya
            </a>
        </div>
    </div>

    <div class="card-body px-3 px-md-4">
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value" id="sum-total">0</div>
                <div class="summary-label">Total</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-primary" id="sum-ready">0</div>
                <div class="summary-label">Assigned</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-success" id="sum-progress">0</div>
                <div class="summary-label">Dikerjakan</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-warning" id="sum-review">0</div>
                <div class="summary-label">Cek Pengaju</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-danger" id="sum-cancelled">0</div>
                <div class="summary-label">Dibatalkan</div>
            </div>
            <div class="summary-card">
                <div class="summary-value text-success" id="sum-done">0</div>
                <div class="summary-label">Selesai</div>
            </div>
        </div>

        <div class="filter-tabs">
            <button type="button" class="filter-tab active" data-filter="todo">To Do</button>
            <button type="button" class="filter-tab" data-filter="all">Semua</button>
            <button type="button" class="filter-tab" data-filter="T03">Assigned</button>
            <button type="button" class="filter-tab" data-filter="T04">Dikerjakan</button>
            <button type="button" class="filter-tab" data-filter="T14">Pending</button>
            <button type="button" class="filter-tab" data-filter="T07">Feedback</button>
            <button type="button" class="filter-tab" data-filter="T05">Cek Pengaju</button>
            <button type="button" class="filter-tab" data-filter="terminal">Selesai/Batal</button>
        </div>

        <div id="alert-pic-ticket"></div>
        <div id="pic-ticket-list">
            <span class="skeleton mb-2"></span>
            <span class="skeleton mb-2"></span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="modal fade" id="modalPicDetail" tabindex="-1" aria-labelledby="modalPicDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h6 class="modal-title fw-bold" id="modalPicDetailLabel">Detail Ticket</h6>
                    <small class="text-muted" id="detail-subtitle">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detail-body">
                <span class="skeleton"></span>
            </div>

            <div class="modal-footer d-flex gap-2" style="padding: 12px 16px; border-top: 1px solid #e5e7eb;">
                <a href="#" target="_blank" id="btn-pic-detail-pdf" class="btn btn-danger fw-bold rounded-3 flex-fill d-flex align-items-center justify-content-center gap-1" style="font-size:13px; text-decoration:none; display:none;">
                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                </a>
                <button type="button" class="btn btn-secondary fw-bold rounded-3 flex-fill"
                    data-bs-dismiss="modal" style="font-size:13px;">
                    <i class="fa-solid fa-xmark me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();" class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        const MENU_ID = '{{ $menu_id }}';
        const BASE_URL = "{{ url('approval/ticket/pic') }}/" + MENU_ID;
        const LIST_URL = BASE_URL + '/list';
        const DETAIL_URL = BASE_URL + '/detail/';
        const STATUS_URL = "{{ url('approval/ticket/pic/status') }}/";
        const CSRF = '{{ csrf_token() }}';
        let tickets = [];
        let activeFilter = 'todo';
        const statusCfg = {
            T00: {
                label: 'Draft',
                cls: 'status-draft',
                icon: 'fa-pen-to-square'
            },
            T01: {
                label: 'Menunggu Persetujuan',
                cls: 'status-pending',
                icon: 'fa-clock'
            },
            T02: {
                label: 'Disetujui',
                cls: 'status-approved',
                icon: 'fa-circle-check'
            },
            T03: {
                label: 'Ditugaskan',
                cls: 'status-process',
                icon: 'fa-user-check'
            },
            T04: {
                label: 'Dikerjakan',
                cls: 'status-process',
                icon: 'fa-spinner'
            },
            T05: {
                label: 'Menunggu Respon Peminta',
                cls: 'status-waiting',
                icon: 'fa-hourglass-half'
            },
            T14: {
                label: 'Pending',
                cls: 'status-waiting',
                icon: 'fa-triangle-exclamation'
            },
            T06: {
                label: 'Selesai',
                cls: 'status-approved',
                icon: 'fa-circle-check'
            },
            T07: {
                label: 'Respon Balasan Peminta',
                cls: 'status-feedback',
                icon: 'fa-comment-dots'
            },
            T08: {
                label: 'Dibatalkan',
                cls: 'status-rejected',
                icon: 'fa-ban'
            },
            T09: {
                label: 'Disetujui Atasan 1',
                cls: 'status-approved',
                icon: 'fa-check-double'
            },
            T10: {
                label: 'Disetujui Atasan 2',
                cls: 'status-approved',
                icon: 'fa-check-double'
            },
            T11: {
                label: 'Menunggu Divalidasi',
                cls: 'status-process',
                icon: 'fa-clipboard-check'
            },
            T12: {
                label: 'Menunggu Pemeriksaan',
                cls: 'status-waiting',
                icon: 'fa-magnifying-glass'
            },
            T13: {
                label: 'Menunggu Ditugaskan',
                cls: 'status-waiting',
                icon: 'fa-user-tag'
            },
            T14: {
                label: 'Pending',
                cls: 'status-waiting',
                icon: 'fa-triangle-exclamation'
            },
            T15: {
                label: 'Ditutup Otomatis oleh Sistem',
                cls: 'status-closed',
                icon: 'fa-circle-check'
            }
        };
        function loadTickets() {
            $('#pic-ticket-list').html('<span class="skeleton mb-2"></span><span class="skeleton mb-2"></span>');
            $.ajax({
                url: LIST_URL,
                type: 'GET',
                success: function(res) {
                    if (res.code !== 200) {
                        showAlert(res.message || 'Gagal memuat ticket PIC.');
                        return;
                    }
                    tickets = res.tickets || [];
                    $('#sum-total').text(res.summary?.total ?? tickets.length);
                    $('#sum-ready').text(res.summary?.ready ?? 0);
                    $('#sum-progress').text(res.summary?.progress ?? 0);
                    $('#sum-review').text(res.summary?.review ?? 0);
                    $('#sum-cancelled').text(res.summary?.cancelled ?? 0);
                    $('#sum-done').text(res.summary?.done ?? 0);
                    renderTickets();
                },
                error: function(xhr) {
                    showAlert(window.getFriendlyErrorMessage(xhr, 'Gagal memuat data ticket PIC.'));
                }
            });
        }
        function getEnvBadge(kategori, name, nomor) {
            let kat = Number(kategori);
            if (!kat || isNaN(kat) || kat === 0) {
                if (nomor && nomor.includes('AOL-AJA')) kat = 3;
                else if (nomor && (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS'))) kat = 2;
                else if (nomor && (nomor.includes('EPIC-SPS') || nomor.includes('EPIC'))) kat = 1;
                else kat = 1;
            }
            if (nomor && nomor.includes('AOL-AJA')) {
                kat = 3;
            } else if (nomor && (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS'))) {
                kat = 2;
            } else if (nomor && nomor.includes('EPIC')) {
                kat = 1;
            }
            let cls = 'badge-env-epicor';
            let icon = 'fa-database';
            let label = 'Epicor SPS';
            if (kat === 1) {
                cls = 'badge-env-epicor';
                icon = 'fa-database';
                label = 'Epicor SPS';
            } else if (kat === 2) {
                cls = 'badge-env-accurate-sp';
                icon = 'fa-bolt';
                label = 'AOL SP';
            } else if (kat === 3) {
                cls = 'badge-env-accurate-aja';
                icon = 'fa-network-wired';
                label = 'AOL AJA';
            }
            return `<span class="badge ${cls}" style="font-size:10px; font-weight:700; padding:2px 7px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;"><i class="fa-solid ${icon}"></i>${label}</span>`;
        }
        /* ── Data contract dari LIST_URL (lihat catatan backend di penjelasan) ──
           Setiap item di res.tickets sekarang membawa, selain field lama:
             - root_ticket_id : sama untuk semua tiket dalam 1 grup (null kalau tidak ada grup)
             - is_group       : true kalau tiket ini bagian dari grup induk-turunan/relasi
             - is_mine        : true = milik PIC yang login, false = milik PIC lain di grup yang sama
             - pic_id, pic_name : identitas PIC yang ditugaskan ke tiket ini
           Tiket yang is_mine=false HANYA muncul kalau dia satu grup (root_ticket_id sama)
           dengan minimal 1 tiket milik PIC yang login — backend TIDAK mengirim seluruh
           tiket milik PIC lain di luar konteks grup itu.
        */

        function ticketMatchesTab(t, filter) {
            if (filter === 'todo') return ['T02', 'T03', 'T04', 'T05', 'T07', 'T11', 'T14'].includes(t.ticket_status);
            if (filter === 'terminal') return ['T06', 'T08'].includes(t.ticket_status);
            if (filter === 'all') return true;
            return t.ticket_status === filter;
        }

        function compareTodo(a, b) {
            const priorityOrder = {
                'P01': 1,
                'P02': 2,
                'P03': 3,
                'P04': 4
            };
            const statusOrder = {
                'T03': 1, // Ditugaskan
                'T04': 2, // Dikerjakan
                'T14': 3, // Pending
                'T07': 4, // Respon Balasan Peminta
                'T05': 5, // Menunggu Respon Peminta
                'T11': 6, // Divalidasi
                'T02': 7, // Disetujui
            };
            const statusA = statusOrder[a.ticket_status] || 99;
            const statusB = statusOrder[b.ticket_status] || 99;
            if (statusA !== statusB) {
                return statusA - statusB;
            }
            const priorityA = priorityOrder[a.priority_code] || 99;
            const priorityB = priorityOrder[b.priority_code] || 99;
            if (priorityA !== priorityB) {
                return priorityA - priorityB; // Ascending order of priority
            }
            const dateA = new Date(a.finish_at || a.assign_at || a.created_at).getTime() || 0;
            const dateB = new Date(b.finish_at || b.assign_at || b.created_at).getTime() || 0;
            if (dateA !== dateB) {
                return dateB - dateA; // Descending (yang paling baru di atas)
            }
            return (b.id || 0) - (a.id || 0);
        }

        /* Tiket "terbaik" milik SAYA di dalam 1 grup yang cocok tab aktif — dipakai untuk
           menentukan apakah grup itu tampil di tab ini, dan untuk urutan tampilnya. */
        function bestMineMatch(members, filter) {
            const candidates = members.filter(m => m.is_mine !== false && ticketMatchesTab(m, filter));
            if (candidates.length === 0) return null;
            if (filter === 'todo') {
                return candidates.slice().sort(compareTodo)[0];
            }
            return candidates[0];
        }

        /* ── Bangun 1 kartu tiket. isMine=false → tombol ubah status disembunyikan
           (read-only, hanya untuk konteks), badge "PIC: <nama>" ditampilkan. ── */
        function buildTicketCard(t, isMine) {
            const cfg = statusCfg[t.ticket_status] || {
                label: t.status_label || '-',
                cls: 'status-done',
                icon: 'fa-circle'
            };
            const title = [t.menu_name, t.submenu_name].filter(Boolean).join(' - ') || 'Ticket';
            const canStart = isMine && (['T03', 'T07'].includes(t.ticket_status) || (['T02', 'T11'].includes(t.ticket_status) && !t.pic_started_at));
            const canFinish = isMine && ['T02', 'T03', 'T04', 'T07', 'T11'].includes(t.ticket_status);
            const canCancel = isMine && !['T05', 'T06', 'T08'].includes(t.ticket_status);
            // Pending: dipakai saat PIC sudah sedang mengerjakan tapi mandek menunggu
            // aksi pihak lain (mis. posting JM Finance) yang di luar kendali PIC.
            const canPending = isMine && t.ticket_status === 'T04';
            const canResume = isMine && t.ticket_status === 'T14';

            let kpiChips = '';
            const kpiItems = [];
            if (t.difficulty_name) {
                kpiItems.push(`<span class="badge" style="background:#f5f3ff; color:#6b21a8; font-size:10px; font-weight:700;"><i class="fa-solid fa-layer-group me-1"></i>${escHtml(t.difficulty_name)}</span>`);
            }
            if (t.actual_hours != null) {
                const isOver = !!t.overrun_flag;
                const mins = Number(t.actual_hours);
                const timeStr = mins < 60 ? `${mins} mnt` : `${mins} mnt (~${(mins / 60).toFixed(1)} jam)`;
                kpiItems.push(`<span class="badge ${isOver ? 'bg-danger' : 'bg-success'}" style="font-size:10px;"><i class="fa-solid fa-stopwatch me-1"></i>${timeStr} ${isOver ? '(Over SLA)' : '(On Time)'}</span>`);
            } else if (t.ticket_status === 'T04') {
                kpiItems.push(`<span class="badge bg-primary" style="font-size:10px;"><i class="fa-solid fa-spinner fa-spin me-1"></i>Sedang Dikerjakan</span>`);
            }
            if (t.feedback_cycles > 0) {
                kpiItems.push(`<span class="badge bg-warning text-dark" style="font-size:10px;"><i class="fa-solid fa-rotate-right me-1"></i>${t.feedback_cycles}x Revisi</span>`);
            }
            if (kpiItems.length > 0) {
                kpiChips = `<div class="d-flex flex-wrap gap-1 mt-1 mb-2">${kpiItems.join('')}</div>`;
            }

            let relationBadge = '';
            if (t.has_parent && t.parent_ticket_number) {
                relationBadge = `<span class="badge" style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; font-size:10px; font-weight:700;"><i class="fa-solid fa-arrow-turn-down me-1"></i>Turunan dari: ${escHtml(t.parent_ticket_number)}</span>`;
            } else if (t.child_count > 0) {
                relationBadge = `<span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; font-size:10px; font-weight:700;"><i class="fa-solid fa-sitemap me-1"></i>Tiket Induk (${t.child_count} Turunan)</span>`;
            } else if (t.is_group) {
                relationBadge = `<span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:10px; font-weight:700;"><i class="fa-solid fa-layer-group me-1"></i>Grup Terkait</span>`;
            }

            const isUnassigned = !t.pic_id;
            const ownerBadge = !isMine && !isUnassigned
                ? `<span class="owner-badge"><i class="fa-solid fa-user"></i>PIC: ${escHtml(t.pic_name || '-')}</span>`
                : '';

            const envBadge = getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket);
            return `
                <div class="ticket-card ${isMine ? '' : 'not-mine'}">
                    <div class="ticket-top">
                        <div>
                            <div class="ticket-no">${escHtml(t.nomor_ticket || '-')}</div>
                            <div class="mt-1 d-flex align-items-center flex-wrap gap-1">
                                ${envBadge}
                                ${relationBadge}
                                ${ownerBadge}
                            </div>
                            <div class="ticket-title mt-1">${escHtml(title)}</div>
                        </div>
                        <span class="status-badge ${cfg.cls}">
                            <i class="fa-solid ${cfg.icon}"></i>${escHtml(cfg.label)}
                        </span>
                    </div>
                    ${kpiChips}
                    <div class="ticket-meta">
                        <span><i class="fa-solid fa-user"></i>${escHtml(t.requestor_name || '-')}</span>
                        <span><i class="fa-solid fa-user-tie"></i>${escHtml(t.assign_name || '-')}</span>
                        <span><i class="fa-solid fa-calendar"></i>${formatDate(t.assign_at || t.created_at)}</span>
                        ${t.detail_count ? `<span><i class="fa-solid fa-table-list"></i>${t.detail_count} data</span>` : ''}
                    </div>
                    <div class="action-row">
                        <a href="{{ url('/ticket/export') }}/${t.id}" target="_blank" class="btn btn-light" title="Download / Print PDF" style="text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                            <i class="fa-solid fa-file-pdf text-danger"></i>PDF
                        </a>
                        <button type="button" class="btn btn-light btn-detail" data-id="${t.id}">
                            <i class="fa-solid fa-eye"></i>Detail
                        </button>
                        ${canStart ? `
                            <button type="button" class="btn btn-primary btn-status" data-id="${t.id}" data-action="start">
                                <i class="fa-solid fa-play"></i>Mulai Pengerjaan
                            </button>` : ''}
                        ${canFinish ? `
                            <button type="button" class="btn btn-success btn-status" data-id="${t.id}" data-action="finish">
                                <i class="fa-solid fa-check-double"></i>Selesai Dikerjakan
                            </button>` : ''}
                        ${canPending ? `
                            <button type="button" class="btn btn-warning btn-status" data-id="${t.id}" data-action="pending">
                                <i class="fa-solid fa-triangle-exclamation"></i>Pending
                            </button>` : ''}
                        ${canResume ? `
                            <button type="button" class="btn btn-info btn-status" data-id="${t.id}" data-action="resume">
                                <i class="fa-solid fa-play"></i>Lanjutkan Pengerjaan
                            </button>` : ''}
                        ${canCancel ? `
                            <button type="button" class="btn btn-outline-danger btn-status" data-id="${t.id}" data-action="cancel">
                                <i class="fa-solid fa-ban"></i>Batalkan Tiket
                            </button>` : ''}
                        ${!isMine ? (isUnassigned
                            ? `<span class="locked-note"><i class="fa-solid fa-clock"></i>Belum ditugaskan</span>`
                            : `<span class="locked-note"><i class="fa-solid fa-lock"></i>Milik PIC lain</span>`) : ''}
                    </div>
                </div>
            `;
        }

        /* ─── RENDER LIST ───────────────────── */
        function renderTickets() {
            // ── Kelompokkan SEMUA tiket (milik saya + saudara di grup yang sama milik PIC lain)
            //    berdasarkan root_ticket_id ──
            const groups = {};
            const groupOrder = [];
            tickets.forEach(function(t) {
                const gKey = t.root_ticket_id || t.id;
                if (!groups[gKey]) {
                    groups[gKey] = [];
                    groupOrder.push(gKey);
                }
                groups[gKey].push(t);
            });

            // ── Tab (To Do / Dikerjakan / dst) menggambarkan pekerjaan SAYA, jadi sebuah grup
            //    tampil kalau minimal 1 tiket MILIK SAYA di grup itu cocok tab aktif. Begitu
            //    tampil, SEMUA anggota grup (termasuk milik PIC lain) ikut dirender sebagai konteks. ──
            let visibleGroupKeys = groupOrder.filter(function(gKey) {
                return bestMineMatch(groups[gKey], activeFilter) !== null;
            });

            if (visibleGroupKeys.length === 0) {
                $('#pic-ticket-list').html(`
                    <div class="empty-state">
                        <i class="fa-regular fa-folder-open mb-2" style="font-size:28px;"></i>
                        <div>Tidak ada ticket pada filter ini.</div>
                    </div>
                `);
                return;
            }

            if (activeFilter === 'todo') {
                visibleGroupKeys.sort(function(gA, gB) {
                    return compareTodo(bestMineMatch(groups[gA], activeFilter), bestMineMatch(groups[gB], activeFilter));
                });
            }

            let html = '';
            visibleGroupKeys.forEach(function(gKey) {
                const members = groups[gKey];
                const isRealGroup = members.length > 1 || members[0].is_group;

                // ── Tiket tunggal (bukan bagian grup) → kartu biasa ──
                if (!isRealGroup) {
                    html += buildTicketCard(members[0], members[0].is_mine !== false);
                    return;
                }

                // ── Grup 2+ tiket, bisa lintas PIC → wrapper + semua kartu anggota ──
                const sorted = members.slice().sort(function(a, b) {
                    if (a.is_root && !b.is_root) return -1;
                    if (!a.is_root && b.is_root) return 1;
                    return (a.created_at || a.assign_at || '').localeCompare(b.created_at || b.assign_at || '');
                });

                const mineCount = sorted.filter(m => m.is_mine !== false).length;
                const otherAssignedMembers = sorted.filter(m => m.is_mine === false && m.pic_id);
                const unassignedMembers = sorted.filter(m => m.is_mine === false && !m.pic_id);
                const pendingTicket = sorted.find(m => m.ticket_status === 'T14');
                const otherNames = [...new Set(otherAssignedMembers.map(m => m.pic_name).filter(Boolean))];

                const rootT = sorted.find(m => m.is_root) || sorted[0];

                let badges = `<span class="pic-group-badge mine"><i class="fa-solid fa-user-check"></i>${mineCount} Ditugaskan Kepada Anda</span>`;
                if (otherAssignedMembers.length > 0) {
                    badges += `<span class="pic-group-badge other"><i class="fa-solid fa-users"></i>${otherAssignedMembers.length} milik ${escHtml(otherNames.join(', ') || 'PIC lain')}</span>`;
                }
                if (unassignedMembers.length > 0) {
                    badges += `<span class="pic-group-badge unassigned"><i class="fa-solid fa-clock"></i>${unassignedMembers.length} Belum Ditugaskan</span>`;
                }
                if (pendingTicket) {
                    badges += `<span class="pic-group-badge pending"><i class="fa-solid fa-triangle-exclamation"></i>1 Pending</span>`;
                }

                const membersHtml = sorted.map(t => buildTicketCard(t, t.is_mine !== false)).join('');

                html += `
                    <div class="pic-group" data-group-id="${gKey}">
                        <div class="pic-group-header">
                            <div>
                                <div class="pic-group-title"><i class="fa-solid fa-sitemap"></i>${escHtml(rootT.nomor_ticket || '-')}</div>
                                <div class="pic-group-sub">${escHtml(rootT.requestor_name || '-')} · ${sorted.length} tiket dalam grup ini</div>
                            </div>
                            <div class="pic-group-badges">
                                ${badges}
                                <i class="fa-solid fa-chevron-down pic-group-toggle"></i>
                            </div>
                        </div>
                        <div class="pic-group-body">
                            ${membersHtml}
                        </div>
                    </div>
                `;
            });

            $('#pic-ticket-list').html(html);
        }

        $(document).on('click', '.pic-group-header', function() {
            $(this).closest('.pic-group').toggleClass('collapsed');
        });
        $(document).on('click', '.filter-tab', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            renderTickets();
        });
        $(document).on('click', '.btn-detail', function() {
            const ticketId = $(this).data('id');
            $('#detail-body').html('<span class="skeleton"></span>');
            $('#detail-subtitle').text('-');
            $('#btn-pic-detail-pdf').hide();
            $('#modalPicDetail').modal('show');
            $.ajax({
                url: DETAIL_URL + ticketId,
                type: 'GET',
                success: function(res) {
                    if (res.code !== 200) {
                        $('#detail-body').html(`<div class="text-muted text-center py-4">${escHtml(res.message || 'Detail tidak ditemukan.')}</div>`);
                        return;
                    }
                    renderDetail(res.ticket);
                },
                error: function(xhr) {
                    $('#detail-body').html(`<div class="text-muted text-center py-4">${escHtml(window.getFriendlyErrorMessage(xhr, 'Gagal memuat detail ticket.'))}</div>`);
                }
            });
        });
        $(document).on('click', '.btn-status', function() {
            submitStatus($(this).data('id'), $(this).data('action'));
        });
        function submitStatus(ticketId, action) {
            const titleMap = {
                start: 'Mulai Pengerjaan Tiket Ini?',
                finish: 'Tandai Tiket Selesai Dikerjakan?',
                cancel: 'Batalkan Tiket Ini?',
                pending: 'Tandai Pending',
                resume: 'Lanjutkan Pengerjaan Tiket Ini?'
            };
            const confirmMap = {
                start: 'Ya, Mulai Kerjakan',
                finish: 'Ya, Selesaikan',
                cancel: 'Ya, Batalkan',
                pending: 'Ya, Tandai Pending',
                resume: 'Ya, Lanjutkan'
            };
            const needsNote = ['finish', 'cancel', 'pending'].includes(action);

            // Aksi "pending" butuh 1 input tambahan: nama pihak yang ditunggu
            // (mis. "JM Finance"), supaya nanti bisa dianalisa siapa bottleneck-nya.
            if (action === 'pending') {
                Swal.fire({
                    icon: 'question',
                    title: titleMap.pending,
                    html:
                        '<textarea id="swal-pending-note" class="swal2-textarea" placeholder="Alasan / detail pending..."></textarea>',
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: confirmMap.pending,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    preConfirm: function() {
                        const note = document.getElementById('swal-pending-note').value;
                        if (!String(note || '').trim()) {
                            Swal.showValidationMessage('Alasan pending wajib diisi.');
                            return false;
                        }
                        return { note: note };
                    }
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    postStatus(ticketId, action, result.value.note);
                });
                return;
            }

            Swal.fire({
                icon: action === 'cancel' ? 'warning' : 'question',
                title: titleMap[action] || 'Ubah status?',
                input: 'textarea',
                inputPlaceholder: needsNote ? 'Tuliskan catatan...' : 'Catatan opsional...',
                inputValidator: function(value) {
                    if (needsNote && !String(value || '').trim()) {
                        return 'Catatan wajib diisi.';
                    }
                    return null;
                },
                showCancelButton: true,
                confirmButtonText: confirmMap[action] || 'Simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function(result) {
                if (!result.isConfirmed) return;
                postStatus(ticketId, action, result.value || '');
            });
        }

        function postStatus(ticketId, action, note) {
                $.ajax({
                    url: STATUS_URL + ticketId,
                    type: 'POST',
                    data: {
                        _token: CSRF,
                        action: action,
                        note: note || '',
                    },
                    success: function(res) {
                        if (res.code === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Status ticket berhasil diperbarui.',
                                timer: 1800,
                                showConfirmButton: false
                            });
                            loadTickets();
                            return;
                        }
                        let errMsg = res.message || 'Status ticket gagal diperbarui.';
                        if (res.errors) {
                            errMsg = Object.values(res.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: res.code === 422 ? 'warning' : 'error',
                            title: res.code === 422 ? 'Perhatian' : 'Gagal',
                            html: window.getFriendlyErrorMessage(errMsg, 'Status ticket gagal diperbarui.')
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'warning' : 'error',
                            title: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'Perhatian' : 'Gagal',
                            html: window.getFriendlyErrorMessage(xhr, 'Tidak dapat terhubung ke server.')
                        });
                    }
                });
        }
        function buildPicChainHtml(t) {
            if (!t.chain_info) return '';
            const ci = t.chain_info;
            const summary = ci.summary || {};
            const hasChain = !!ci.has_chain;
            const items = ci.items || [];

            let bannerHtml = '';
            if (summary.type === 'warning') {
                bannerHtml = `
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1.5px solid #fde68a !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-2">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 3px 10px rgba(217, 119, 6, 0.25);">
                                    <i class="fa-solid fa-clock-rotate-left text-white" style="font-size: 16px;"></i>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                        <span style="font-size: 10px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fa-solid fa-circle-info me-1"></i>Informasi Alur Proses
                                        </span>
                                        ${hasChain ? `<span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; border-radius: 20px;"><i class="fa-solid fa-layer-group me-1"></i>${ci.done_tickets}/${ci.total_tickets} Tiket Selesai</span>` : ''}
                                    </div>
                                    <h6 class="mb-1 fw-bold" style="color: #92400e; font-size: 12.5px;">
                                        ${escHtml(summary.title || 'Pengajuan Sedang Menunggu Proses')}
                                    </h6>
                                    <p class="mb-2 text-dark" style="font-size: 11.5px; line-height: 1.4;">
                                        ${escHtml(summary.description || '-')}
                                    </p>
                                    ${summary.waiting_on ? `
                                    <div class="d-inline-flex align-items-center gap-2 px-2 py-1 rounded-3" style="background: #ffffff; border: 1px solid #fde68a; font-size: 11px; color: #78350f;">
                                        <i class="fa-solid fa-user-clock text-warning"></i>
                                        <span>Saat ini menunggu: <strong>${escHtml(summary.waiting_on)}</strong> ${summary.waiting_sub ? `<span class="text-muted">(${escHtml(summary.waiting_sub)})</span>` : ''}</span>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (summary.type === 'success') {
                bannerHtml = `
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 1.5px solid #bbf7d0 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #16a34a, #15803d); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 3px 10px rgba(22, 163, 74, 0.25);">
                                    <i class="fa-solid fa-circle-check text-white" style="font-size: 18px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold" style="color: #166534; font-size: 12.5px;">
                                        ${escHtml(summary.title || 'Seluruh Rangkaian Pengajuan Telah Selesai')}
                                    </h6>
                                    <p class="mb-0 text-muted" style="font-size: 11.5px;">
                                        ${escHtml(summary.description || 'Semua proses pada pengajuan ini telah selesai dikerjakan.')}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            let tableHtml = '';
            if (hasChain && items.length > 0) {
                tableHtml = `
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: #ffffff; border: 1.5px solid #ede9fe !important; overflow: hidden;">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: #f3e8ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-sitemap" style="font-size: 12px;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 12px;">Rangkaian Pengajuan Terkait</h6>
                                    <small class="text-muted" style="font-size: 10px;">Seluruh tiket dalam satu rombongan pengajuan ini</small>
                                </div>
                            </div>
                            <span class="badge" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; font-size: 10px; padding: 4px 8px; border-radius: 20px;">
                                Total ${items.length} Tiket
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 11px;">
                                    <thead style="background: #faf8ff; color: #4c1d95; font-size: 10px; text-transform: uppercase;">
                                        <tr>
                                            <th class="ps-3 py-2" style="width: 30px;">No</th>
                                            <th class="py-2">Nomor &amp; Jenis Tiket</th>
                                            <th class="py-2">Modul / Layanan</th>
                                            <th class="py-2">Status Saat Ini</th>
                                            <th class="py-2">Petugas Ditunggu</th>
                                            <th class="pe-3 py-2 text-center" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${items.map((item, idx) => {
                                            const isCurrent = !!item.is_current;
                                            let statusBadgeHtml = '';
                                            if (item.is_done && !item.is_rejected) {
                                                statusBadgeHtml = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-circle-check me-1"></i>Selesai</span>`;
                                            } else if (item.is_rejected) {
                                                statusBadgeHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-circle-xmark me-1"></i>Dibatalkan</span>`;
                                            } else if (item.status_code === 'T00' || item.status_badge === 'status-draft') {
                                                statusBadgeHtml = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-pencil me-1"></i>Draft</span>`;
                                            } else {
                                                statusBadgeHtml = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded" style="font-size: 9.5px; font-weight:700;"><i class="fa-solid fa-clock me-1"></i>${escHtml(item.status_label || '-')}</span>`;
                                            }

                                            let waitingHtml = '-';
                                            if (item.waiting_on) {
                                                waitingHtml = `
                                                    <div style="font-size: 11px; font-weight: 700; color: #374151;">
                                                        ${escHtml(item.waiting_on)}
                                                    </div>
                                                    <small class="text-muted" style="font-size: 9.5px;">
                                                        ${escHtml(item.waiting_role || '')} ${item.waiting_stage ? `(${escHtml(item.waiting_stage)})` : ''}
                                                    </small>
                                                `;
                                            } else if (item.is_done && !item.is_rejected) {
                                                waitingHtml = `<span class="text-success" style="font-size: 10px;"><i class="fa-solid fa-check me-1"></i>Selesai</span>`;
                                            }

                                            const viewUrl = item.link_url || `{{ url('/ticket/lihat') }}/${item.menu_id || t.menu_id || 1}/${item.id || item.ticket_id}`;
                                            const actionHtml = isCurrent
                                                ? `<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded" style="font-size: 9px; font-weight:700;">Tiket Ini</span>`
                                                : `<a href="${viewUrl}" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size: 9.5px; font-weight:600; text-decoration:none;"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat</a>`;

                                            const envBadgeHtml = getEnvBadge(item.environment_kategori, item.environment_name, item.ticket_number);

                                            return `
                                                <tr style="${isCurrent ? 'background: #fdfaff;' : ''}">
                                                    <td class="ps-3 fw-bold text-muted">${idx + 1}</td>
                                                    <td>
                                                        <div class="fw-bold ${isCurrent ? 'text-primary' : 'text-dark'}" style="font-size: 11.5px;">
                                                            ${escHtml(item.ticket_number || '-')}
                                                        </div>
                                                        <div class="d-flex align-items-center gap-1 mt-1">
                                                            <span class="badge ${item.is_root ? 'bg-primary' : 'bg-info'}" style="font-size: 8.5px; font-weight: 700;">
                                                                ${escHtml(item.type_label || (item.is_root ? 'Tiket Utama' : 'Tiket Turunan'))}
                                                            </span>
                                                            ${envBadgeHtml}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">${escHtml(item.sub_menu_name || '-')}</div>
                                                        <small class="text-muted" style="font-size: 9.5px;">${escHtml(item.menu_name || '-')}</small>
                                                    </td>
                                                    <td>${statusBadgeHtml}</td>
                                                    <td>${waitingHtml}</td>
                                                    <td class="pe-3 text-center">${actionHtml}</td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }

            return bannerHtml + tableHtml;
        }

        function buildPicParentCardHtml(t) {
            const parentTicket = t.parent_ticket;
            const rootTicket = t.root_ticket;
            const childTickets = t.child_tickets || [];

            const hasParent = !!parentTicket;
            const hasRoot = !!rootTicket && (!parentTicket || rootTicket.id !== parentTicket.id) && (rootTicket.id !== t.id);
            const hasChildren = childTickets.length > 0;

            if (!hasParent && !hasRoot && !hasChildren) return '';

            let parentSection = '';
            if (hasParent) {
                const pEnv = getEnvBadge(parentTicket.environment_kategori, null, parentTicket.nomor_ticket);
                const pDate = parentTicket.created_at ? formatDate(parentTicket.created_at) : '-';
                const pSub = [parentTicket.menu_name, parentTicket.submenu_name].filter(Boolean).join(' - ') || (parentTicket.submenu_name || '-');
                const pLink = parentTicket.link_url || `{{ url('/ticket/lihat') }}/${parentTicket.ticket_menu_id || parentTicket.menu_id || t.menu_id || 1}/${parentTicket.id || parentTicket.ticket_id}`;
                parentSection = `
                    <div style="background: white; border: 1px solid #bae6fd; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;">
                        <div class="d-flex align-items-start gap-2">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-arrow-up-from-bracket" style="color: #0284c7; font-size: 12px;"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                    <small style="font-size: 9.5px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Pengajuan Asal / Pemicu (Parent)
                                    </small>
                                    ${pEnv}
                                </div>
                                <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 8px 10px;">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                        <div style="font-size: 12px; font-weight: 800; color: #0c4a6e;">
                                            ${escHtml(parentTicket.nomor_ticket || '-')}
                                        </div>
                                        ${parentTicket.requestor_name ? `<small class="text-muted" style="font-size: 10px;"><i class="fa-solid fa-user me-1"></i>${escHtml(parentTicket.requestor_name)}</small>` : ''}
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                        <small style="color: #475569; font-size: 10.5px; font-weight: 600;">
                                            <i class="fa-solid fa-layer-group me-1 text-primary"></i>${escHtml(pSub)}
                                        </small>
                                        <small style="color: #64748b; font-size: 9.5px;">
                                            <i class="fa-solid fa-calendar me-1"></i>${pDate}
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-1 d-flex align-items-center gap-2">
                                    <a href="${pLink}" target="_blank" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 10px; color: #0284c7; font-weight: 600;">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Detail Pengajuan Asal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            let rootSection = '';
            if (hasRoot) {
                const rEnv = getEnvBadge(rootTicket.environment_kategori, null, rootTicket.nomor_ticket);
                const rDate = rootTicket.created_at ? formatDate(rootTicket.created_at) : '-';
                const rSub = [rootTicket.menu_name, rootTicket.submenu_name].filter(Boolean).join(' - ') || (rootTicket.submenu_name || '-');
                const rLink = rootTicket.link_url || `{{ url('/ticket/lihat') }}/${rootTicket.ticket_menu_id || rootTicket.menu_id || t.menu_id || 1}/${rootTicket.id || rootTicket.ticket_id}`;
                rootSection = `
                    <div style="background: white; border: 1px solid #ddd6fe; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;">
                        <div class="d-flex align-items-start gap-2">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f5f3ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-sitemap" style="color: #7c3aed; font-size: 12px;"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                    <small style="font-size: 9.5px; font-weight: 700; color: #7c3aed; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Pengajuan Pertama / Utama (Root)
                                    </small>
                                    ${rEnv}
                                </div>
                                <div style="background: #faf5ff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 8px 10px;">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                        <div style="font-size: 12px; font-weight: 800; color: #581c87;">
                                            ${escHtml(rootTicket.nomor_ticket || '-')}
                                        </div>
                                        ${rootTicket.requestor_name ? `<small class="text-muted" style="font-size: 10px;"><i class="fa-solid fa-user me-1"></i>${escHtml(rootTicket.requestor_name)}</small>` : ''}
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                        <small style="color: #475569; font-size: 10.5px; font-weight: 600;">
                                            <i class="fa-solid fa-layer-group me-1 text-primary"></i>${escHtml(rSub)}
                                        </small>
                                        <small style="color: #64748b; font-size: 9.5px;">
                                            <i class="fa-solid fa-calendar me-1"></i>${rDate}
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-1 d-flex align-items-center gap-2">
                                    <a href="${rLink}" target="_blank" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 10px; color: #7c3aed; font-weight: 600;">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Detail Pengajuan Pertama
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            let childSection = '';
            if (hasChildren) {
                childSection = `
                    <div style="background: white; border: 1px solid #e9d5ff; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: #f3e8ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-network-wired" style="font-size: 11px;"></i>
                                </div>
                                <span style="font-size: 11px; font-weight: 700; color: #6b21a8; text-transform: uppercase;">
                                    Tiket Turunan yang Dihasilkan (${childTickets.length})
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            ${childTickets.map(c => {
                                const cEnv = getEnvBadge(c.environment_kategori, null, c.nomor_ticket);
                                const cDate = c.created_at ? formatDate(c.created_at) : '-';
                                const cSub = [c.menu_name, c.submenu_name].filter(Boolean).join(' - ') || (c.submenu_name || '-');
                                const cLink = c.link_url || `{{ url('/ticket/lihat') }}/${c.ticket_menu_id || c.menu_id || t.menu_id || 1}/${c.id || c.ticket_id}`;
                                return `
                                    <div class="p-2 rounded-2" style="background: #faf5ff; border: 1px solid #ede9fe; font-size: 11px;">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                                            <div class="fw-bold text-dark">${escHtml(c.nomor_ticket || '-')}</div>
                                            <div class="d-flex align-items-center gap-1">
                                                ${cEnv}
                                                <a href="${cLink}" target="_blank" class="btn btn-sm btn-outline-primary px-1 py-0" style="font-size: 9px; text-decoration:none;">
                                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 text-muted" style="font-size: 10px;">
                                            <span><i class="fa-solid fa-folder me-1 text-primary"></i>${escHtml(cSub)}</span>
                                            <span>${c.pic_name ? `<i class="fa-solid fa-user-tie me-1"></i>${escHtml(c.pic_name)}` : '<span class="text-warning">Belum Ditugaskan</span>'}</span>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }

            return `
                <div class="card mb-3" style="border-left: 4px solid #0284c7; border-radius: 12px; overflow: hidden; box-shadow: 0 3px 10px rgba(2, 132, 199, 0.08); border-top: 1px solid #e0f2fe; border-right: 1px solid #e0f2fe; border-bottom: 1px solid #e0f2fe;">
                    <div class="card-body p-3" style="background: linear-gradient(135deg, #f0f9ff, #ffffff);">
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #0284c7, #0ea5e9); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);">
                                <i class="fa-solid fa-link text-white" style="font-size: 13px;"></i>
                            </div>
                            <div class="ms-2 flex-grow-1">
                                <h6 class="mb-0 fw-bold" style="color: #0369a1; font-size: 12px;">
                                    <i class="fa-solid fa-history me-1"></i>Riwayat &amp; Asal Pengajuan
                                </h6>
                                <small class="text-muted" style="font-size: 10px;">Hubungan relasi tiket (induk / turunan / antar-environment)</small>
                            </div>
                        </div>
                        ${parentSection}
                        ${rootSection}
                        ${childSection}
                    </div>
                </div>
            `;
        }

        function renderDetail(t) {
            $('#detail-subtitle').text(t.nomor_ticket || '-');
            $('#btn-pic-detail-pdf').attr('href', '{{ url("/ticket/export") }}/' + t.id).show();
            let detailsHtml = '';
            if (t.details && t.details.length) {
                t.details.forEach(function(d, idx) {
                    const collapseId = `assignDetail_${t.id || 'x'}_${idx}`; // PIC: `picDetail_${t.id || 'x'}_${idx}`
                    const hasSub = d.sub_details && d.sub_details.length > 0;
                    const subCount = hasSub ? d.sub_details.length : 1;
                    const cancelCount = hasSub ? d.sub_details.filter(s => s.is_cancelled).length : 0;
                    const tglTrx = formatDate(d.tanggal_transaksi);
                    let subHtml = '';
                    const refDetailSubHtml = d.reference_ticket_detail ? `
                        <div class="subdetail-field">
                            <div class="subdetail-field-label">
                                <i class="fa-solid fa-link text-primary"></i> Referensi Detail Sebelumnya
                            </div>
                            <div class="subdetail-field-val text-primary fw-semibold">
                                ${escHtml(d.reference_ticket_detail.nomor_detail || '-')}${d.reference_ticket_detail.tanggal_transaksi ? ' (' + escHtml(formatDate(d.reference_ticket_detail.tanggal_transaksi)) + ')' : ''}
                            </div>
                        </div>` : '';
                    if (hasSub) {
                        subHtml = `<div class="subdetails-wrap">` + d.sub_details.map((sd, sIdx) => `
                            <div class="subdetail-card ${sd.is_cancelled ? 'is-line-cancelled' : ''}">
                                ${lineCancelBox(sd)}
                                <div class="subdetail-list">
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-solid fa-receipt text-primary"></i> Nomor Transaksi
                                        </div>
                                        <div class="subdetail-field-val fw-bold text-dark">${escHtml(d.nomor_detail || '-')}</div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-solid fa-calendar-day text-info"></i> Tanggal Transaksi
                                        </div>
                                        <div class="subdetail-field-val">${escHtml(tglTrx)}</div>
                                    </div>
                                    ${refDetailSubHtml}
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-solid fa-list-ol" style="color:#7c3aed;"></i> Baris / Line Detail
                                        </div>
                                        <div class="subdetail-field-val">${escHtml(sd.line_number || (sIdx + 1))}</div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-regular fa-comment-dots text-warning"></i> Alasan / Reason
                                        </div>
                                        <div class="subdetail-field-val">${escHtml(sd.alasan_ubah || '-')}</div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="diff-box diff-before w-100">
                                            <div class="diff-label"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum</div>
                                            <div class="diff-content">${escHtml(sd.before_ubah || '-')}</div>
                                        </div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="diff-box diff-after w-100">
                                            <div class="diff-label"><i class="fa-solid fa-circle-check me-1"></i>Sesudah</div>
                                            <div class="diff-content">${escHtml(sd.after_ubah || '-')}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('') + `</div>`;
                    } else {
                        subHtml = `
                            <div class="subdetail-card">
                                <div class="subdetail-list">
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-solid fa-receipt text-primary"></i> Nomor Transaksi
                                        </div>
                                        <div class="subdetail-field-val fw-bold text-dark">${escHtml(d.nomor_detail || '-')}</div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-solid fa-calendar-day text-info"></i> Tanggal Transaksi
                                        </div>
                                        <div class="subdetail-field-val">${escHtml(tglTrx)}</div>
                                    </div>
                                    ${refDetailSubHtml}
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-solid fa-list-ol" style="color:#7c3aed;"></i> Baris / Line Detail
                                        </div>
                                        <div class="subdetail-field-val">
                                            <span class="subdetail-line-badge">Baris #${escHtml(d.line_detail || '1')}</span>
                                        </div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="subdetail-field-label">
                                            <i class="fa-regular fa-comment-dots text-warning"></i> Alasan / Reason
                                        </div>
                                        <div class="subdetail-field-val">${escHtml(d.alasan_ubah || '-')}</div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="diff-box diff-before w-100">
                                            <div class="diff-label"><i class="fa-solid fa-clock-rotate-left me-1"></i>Sebelum</div>
                                            <div class="diff-content">${escHtml(d.before_ubah || '-')}</div>
                                        </div>
                                    </div>
                                    <div class="subdetail-field">
                                        <div class="diff-box diff-after w-100">
                                            <div class="diff-label"><i class="fa-solid fa-circle-check me-1"></i>Sesudah</div>
                                            <div class="diff-content">${escHtml(d.after_ubah || '-')}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    const refBadgePic = d.reference_ticket_detail ? `
                        <div class="text-truncate mt-1" style="font-size:10px; color:#6d28d9;">
                            <i class="fa-solid fa-link me-1"></i>Ref: ${escHtml(d.reference_ticket_detail.nomor_detail || '-')}${d.reference_ticket_detail.tanggal_transaksi ? ' (' + escHtml(formatDate(d.reference_ticket_detail.tanggal_transaksi)) + ')' : ''}
                        </div>` : '';
                    detailsHtml += `
                        <div class="change-accordion-item">
                            <div class="change-accordion-header"
                                data-bs-toggle="collapse"
                                data-bs-target="#${collapseId}"
                                aria-expanded="false"
                                aria-controls="${collapseId}">
                                <div class="change-card-num">${idx + 1}</div>
                                <div class="change-card-info">
                                    <div class="nomor">Transaksi: ${escHtml(d.nomor_detail || '-')}</div>
                                    <div class="alasan">${subCount} baris perubahan${d.tanggal_transaksi ? ` · ${tglTrx}` : ''}${cancelCount ? ` · <span class="text-danger fw-bold">${cancelCount} dibatalkan</span>` : ''}</div>
                                    ${refBadgePic}
                                </div>
                                <div class="change-accordion-chevron">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>
                            <div id="${collapseId}" class="collapse">
                                <div class="change-accordion-body">
                                    ${subHtml}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                detailsHtml = '<div class="text-muted small text-center py-2">Tidak ada detail perubahan.</div>';
            }
            let commentsHtml = '';
            if (t.comments && t.comments.length) {
                t.comments.forEach(function(c) {
                    commentsHtml += `
                        <div class="comment-item">
                            <div class="comment-meta">${escHtml(c.level || '-')} - ${escHtml(c.action || '-')} - ${formatDateTime(c.date)}</div>
                            <div class="fw-bold mb-1">${escHtml(c.name || '-')}</div>
                            <div>${escHtml(c.text || '-')}</div>
                        </div>
                    `;
                });
            } else {
                commentsHtml = '<div class="text-muted small">Belum ada catatan.</div>';
            }
            const cfg = statusCfg[t.ticket_status] || {
                label: t.status_label || '-',
                cls: 'status-draft',
                icon: 'fa-circle'
            };
            let kpiBoxHtml = '';
            if (t.expected_hours != null || t.actual_hours != null || (t.work_sessions && t.work_sessions.length > 0)) {
                const isOverrun = !!t.overrun_flag;
                const isClosed = t.ticket_status === 'T06';
                const formatMins = function(m) {
                    if (m == null) return '-';
                    const mins = Number(m);
                    if (isNaN(mins)) return '-';
                    if (mins < 60) return `${mins.toFixed(mins % 1 === 0 ? 0 : 1)} Menit`;
                    return `${mins.toFixed(mins % 1 === 0 ? 0 : 1)} Mnt (~${(mins / 60).toFixed(1)} Jam)`;
                };
                let sessionsHtml = '';
                if (t.work_sessions && t.work_sessions.length > 0) {
                    sessionsHtml = `
                        <div class="mt-2 pt-2" style="border-top:1px dashed #e9d5ff;">
                            <div class="fw-bold mb-1" style="font-size:11px; color:#581c87;">
                                <i class="fa-solid fa-timeline me-1"></i>Timeline Sesi Pengerjaan:
                            </div>
                            <div class="d-flex flex-column gap-1">
                                ${t.work_sessions.map(s => {
                                    const isRevisi = s.session_type === 'revisi';
                                    const durMins = Number(s.duration_minutes || 0);
                                    const durStr = durMins < 60 ? `${durMins.toFixed(durMins % 1 === 0 ? 0 : 1)} Menit` : `${durMins.toFixed(durMins % 1 === 0 ? 0 : 1)} Mnt (~${(durMins / 60).toFixed(1)} Jam)`;
                                    return `
                                        <div class="p-2 rounded-2" style="background:#fff; border:1px solid #ede9fe; font-size:11px;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold ${isRevisi ? 'text-warning' : 'text-primary'}">
                                                    <i class="fa-solid ${isRevisi ? 'fa-rotate-right' : 'fa-play'} me-1"></i>
                                                    Siklus #${s.cycle_number} (${isRevisi ? 'Revisi' : 'Awal'})
                                                </span>
                                                <span class="badge ${isRevisi ? 'bg-warning text-dark' : 'bg-primary'}" style="font-size:10px;">
                                                    ${s.duration_minutes != null ? durStr : 'Sedang berjalan'}
                                                </span>
                                            </div>
                                            <div class="text-muted" style="font-size:10px;">
                                                ${s.started_at || '-'} s/d ${s.ended_at || 'Sekarang'}
                                            </div>
                                            ${s.notes ? `<div class="mt-1 p-1 bg-light rounded text-dark" style="font-size:10px;"><em>${escHtml(s.notes)}</em></div>` : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    `;
                }
                kpiBoxHtml = `
                    <div class="mb-3 p-3 rounded-3" style="background: linear-gradient(135deg, #fbfaff, #f5f3ff); border: 1.5px solid #ddd6fe;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold" style="font-size:12px; color:#4c1d95;">
                                <i class="fa-solid fa-chart-line me-1"></i>Indikator Kinerja & SLA
                            </span>
                            ${t.difficulty_name ? `
                                <span class="badge" style="background:#ede9fe; color:#6b21a8; font-size:10px; font-weight:700;">
                                    <i class="fa-solid fa-layer-group me-1"></i>${escHtml(t.difficulty_name)}
                                </span>` : ''}
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11px; border-color:#ede9fe !important;">
                            <span class="text-muted">Mulai Dikerjakan:</span>
                            <span class="fw-semibold">${t.pic_started_at ? formatDateTime(t.pic_started_at) : '<span class="text-muted fst-italic">Belum dimulai</span>'}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11px; border-color:#ede9fe !important;">
                            <span class="text-muted">Target SLA:</span>
                            <span class="fw-bold text-primary">${t.expected_hours != null ? formatMins(t.expected_hours) : '-'}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11px; border-color:#ede9fe !important;">
                            <span class="text-muted">Total Waktu Aktual:</span>
                            <span class="fw-bold ${isOverrun ? 'text-danger' : 'text-success'}">
                                ${t.actual_hours != null ? formatMins(t.actual_hours) : (t.ticket_status === 'T04' ? '<span class="text-info"><i class="fa-solid fa-spinner fa-spin me-1"></i>Sedang berjalan</span>' : '-')}
                            </span>
                        </div>
                        ${t.variance_hours != null && isClosed ? `
                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11px; border-color:#ede9fe !important;">
                            <span class="text-muted">Varians Waktu:</span>
                            <span class="fw-bold ${t.variance_hours > 0 ? 'text-danger' : 'text-success'}">
                                ${t.variance_hours > 0 ? '+' : ''}${formatMins(t.variance_hours)} (${t.variance_hours > 0 ? 'Overrun' : 'Hemat'})
                            </span>
                        </div>` : ''}
                        <div class="d-flex justify-content-between py-1" style="font-size:11px;">
                            <span class="text-muted">Status SLA:</span>
                            <span>
                                ${!isClosed 
                                    ? '<span class="badge bg-warning text-dark"><i class="fa-solid fa-spinner fa-spin me-1"></i>Aktif</span>' 
                                    : (isOverrun 
                                        ? '<span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Melebihi SLA</span>' 
                                        : '<span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Tepat Waktu</span>'
                                    )}
                            </span>
                        </div>
                        ${sessionsHtml}
                    </div>
                `;
            }
            $('#detail-body').html(`
                ${buildPicChainHtml(t)}
                ${buildPicParentCardHtml(t)}
                <div class="detail-row">
                    <div class="detail-label">Nomor</div>
                    <div class="detail-value">${escHtml(t.nomor_ticket || '-')}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Environment</div>
                    <div class="detail-value">${getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket)}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Menu</div>
                    <div class="detail-value">${escHtml([t.menu_name, t.submenu_name].filter(Boolean).join(' - ') || '-')}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Pengaju</div>
                    <div class="detail-value">${escHtml(t.requestor_name || '-')}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status-badge ${cfg.cls}">
                            <i class="fa-solid ${cfg.icon}"></i> ${escHtml(cfg.label)}
                        </span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Assign Oleh</div>
                    <div class="detail-value">${escHtml(t.assign_name || '-')} (${formatDateTime(t.assign_at)})</div>
                </div>
                
                <div class="detail-row" style="align-items:flex-start;">
                    <div class="detail-label" style="padding-top:4px;">Lampiran</div>
                    <div class="detail-value">
                        ${(() => {
                            const list = (t.attachments && t.attachments.length > 0)
                                ? t.attachments
                                : (t.lampiran ? [{
                                    file_path: t.lampiran,
                                    original_name: t.lampiran.split('/').pop(),
                                    file_size: null
                                }] : []);

                            if (list.length === 0) {
                                return '<span class="lampiran-none"><i class="fa-solid fa-paperclip me-1"></i>Tidak ada lampiran</span>';
                            }

                            const iconMap = {
                                pdf: 'fa-file-pdf',
                                doc: 'fa-file-word',
                                docx: 'fa-file-word',
                                xls: 'fa-file-excel',
                                xlsx: 'fa-file-excel',
                                csv: 'fa-file-excel',
                                zip: 'fa-file-zipper',
                                rar: 'fa-file-zipper',
                                jpg: 'fa-file-image',
                                jpeg: 'fa-file-image',
                                png: 'fa-file-image',
                                webp: 'fa-file-image',
                            };

                            return `<div class="d-flex flex-wrap gap-2">` + list.map(att => {
                                const url = att.file_path || '';
                                const filename = att.original_name || (url.split('/').pop()) || 'Lampiran';
                                const ext = filename.split('.').pop().toLowerCase();
                                const icon = iconMap[ext] || 'fa-file';
                                const sizeLabel = att.file_size ? `${(att.file_size / 1024).toFixed(1)} KB` : '';
                                return `<a href="${url}" target="_blank" class="lampiran-box">
                                    <div class="lampiran-icon"><i class="fa-solid ${icon}"></i></div>
                                    <div>
                                        <div class="lampiran-name" title="${escHtml(filename)}">${escHtml(filename)}</div>
                                        <div class="lampiran-meta">
                                            ${sizeLabel ? `<span class="me-1">${sizeLabel}</span> • ` : ''}
                                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px;"></i> Buka
                                        </div>
                                    </div>
                                </a>`;
                            }).join('') + `</div>`;
                        })()}
                    </div>
                </div>
                <hr>
                ${kpiBoxHtml}
                ${(() => {
                    let total = 0, batal = 0;
                    (t.details || []).forEach(d => (d.sub_details || []).forEach(s => { total++; if (s.is_cancelled) batal++; }));
                    return batal > 0
                        ? `<div class="alert alert-warning py-2 px-3 mb-2" style="font-size:11.5px;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            <strong>${batal} dari ${total} baris dibatalkan</strong> dan tidak perlu dikerjakan. Kerjakan hanya ${total - batal} baris aktif.
                        </div>`
                        : '';
                })()}
                <div class="fw-bold mb-2">Detail Perubahan</div>
                ${detailsHtml}
                <hr>
                <div class="fw-bold mb-2">Catatan</div>
                ${commentsHtml}
            `);
        }

        function showAlert(message) {
            $('#alert-pic-ticket').html(`
                <div class="alert alert-danger py-2 px-3 rounded-3" style="font-size:12px;">
                    ${escHtml(message)}
                </div>
            `);
            $('#pic-ticket-list').html('');
        }

        function formatDate(value) {
            if (!value) return '-';
            if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
                const parts = value.split('T')[0].split(' ')[0].split('-');
                if (parts.length === 3) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
            }
            const d = new Date(value);
            if (Number.isNaN(d.getTime())) return '-';
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            return `${day}-${month}-${d.getFullYear()}`;
        }

        function formatDateTime(value) {
            if (!value) return '-';
            const d = new Date(value);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function lineCancelBox(sd) {
            if (!sd || !sd.is_cancelled) return '';
            const role = sd.cancelled_by_role === 'checker' ? 'oleh Checker'
                : (sd.cancelled_by_role === 'validator' ? 'oleh Validator' : 'otomatis (mengikuti transaksi induk)');
            return `<div class="line-cancel-box">
                <div class="lcb-title"><i class="fa-solid fa-ban me-1"></i>Baris dibatalkan ${role}${sd.cancelled_by_name ? ' (' + escHtml(sd.cancelled_by_name) + ')' : ''}</div>
                <div class="lcb-reason">${escHtml(sd.cancel_reason || '-')}</div>
            </div>`;
        }

        loadTickets();
    });
</script>
@endsection