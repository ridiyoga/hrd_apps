@extends('users.layouts.main')

@section('title')
APPS | KARYAWAN - Validator Ticket
@endsection

@section('css')
<style>
    /* =====================
       FUTURISTIC HEADER
    ===================== */
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

    /* =====================
       SUMMARY STRIP
    ===================== */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 0 16px 16px;
    }

    .sum-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 14px;
        padding: 12px 8px;
        text-align: center;
    }

    .sum-card .sum-val {
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        color: #6d28d9;
    }

    .sum-card .sum-val.green {
        color: #16a34a;
    }

    .sum-card .sum-val.red {
        color: #dc2626;
    }

    .sum-card .sum-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: .4px;
        margin-top: 4px;
    }

    /* =====================
       SEARCH BAR
    ===================== */
    .search-wrap {
        position: relative;
        margin: 0 16px 14px;
    }

    .search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a78bfa;
        font-size: 13px;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 14px 10px 40px;
        font-size: 13px;
        background: #fdfcff;
        color: #374151;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .search-input:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .12);
    }

    .search-input::placeholder {
        color: #c4b5fd;
    }

    /* =====================
       ENVIRONMENT FILTER TABS
    ===================== */
    .env-filter-wrap {
        padding: 0 16px 8px;
    }

    .env-filter-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        scrollbar-width: none;
        padding-bottom: 2px;
    }

    .env-filter-tabs::-webkit-scrollbar {
        display: none;
    }

    .env-filter-tab {
        flex-shrink: 0;
        padding: 6px 13px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        border: 1.5px solid #ede9fe;
        background: #fff;
        color: #4b5563;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }

    .env-filter-tab:hover {
        background: #faf8ff;
        border-color: #c4b5fd;
    }

    .env-badge-count {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        background: #f3e8ff;
        color: #7c3aed;
        transition: all .2s;
    }

    /* Active States */
    .env-filter-tab.active[data-env="all"] {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(124, 58, 237, .3);
    }
    .env-filter-tab.active[data-env="all"] .env-badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    .env-filter-tab.active[data-env="1"] {
        background: linear-gradient(135deg, #6d28d9, #8b5cf6);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(109, 40, 217, .3);
    }
    .env-filter-tab.active[data-env="1"] .env-badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    .env-filter-tab.active[data-env="2"] {
        background: linear-gradient(135deg, #ea580c, #f97316);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(234, 88, 12, .3);
    }
    .env-filter-tab.active[data-env="2"] .env-badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    .env-filter-tab.active[data-env="3"] {
        background: linear-gradient(135deg, #0d9488, #14b8a6);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(13, 148, 136, .3);
    }
    .env-filter-tab.active[data-env="3"] .env-badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    /* =====================
       FILTER TABS
    ===================== */
    .filter-tabs {
        display: flex;
        gap: 8px;
        padding: 0 16px 12px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .filter-tabs::-webkit-scrollbar {
        display: none;
    }

    .filter-tab {
        flex-shrink: 0;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        border: 1.5px solid #ede9fe;
        background: #fff;
        color: #7c3aed;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(124, 58, 237, .3);
    }

    /* =====================
       GROUP CARD
    ===================== */
    .group-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        margin: 0 16px 10px;
        overflow: hidden;
        position: relative;
        transition: box-shadow .2s, transform .15s, border-color .2s;
    }

    .group-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 4px 0 0 4px;
    }

    .group-card:hover {
        box-shadow: 0 8px 24px rgba(124, 58, 237, .14);
        transform: translateY(-2px);
        border-color: #c4b5fd;
    }

    .group-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 14px 14px 18px;
        cursor: pointer;
        user-select: none;
    }

    .group-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #7c3aed;
        flex-shrink: 0;
    }

    .group-meta {
        flex: 1;
        min-width: 0;
    }

    .group-meta .gm-root {
        font-size: 12px;
        font-weight: 800;
        color: #6b21a8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .group-meta .gm-sub {
        font-size: 11px;
        color: #6b7280;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .group-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }

    .group-count {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: #ede9fe;
        color: #7c3aed;
    }

    .group-date {
        font-size: 10px;
        color: #9ca3af;
    }

    .group-chevron {
        font-size: 11px;
        color: #d8b4fe;
        transition: transform .25s;
        margin-left: 4px;
    }

    .group-card.open .group-chevron {
        transform: rotate(180deg);
    }

    /* ── APPROVAL CHAIN ── */
    .approval-chain {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 18px;
        background: #faf9ff;
        border-top: 1px dashed #f3e8ff;
        border-bottom: 1px dashed #f3e8ff;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .approval-chain::-webkit-scrollbar {
        display: none;
    }

    .ac-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        flex-shrink: 0;
    }

    .ac-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
    }

    .ac-dot-done {
        background: #f0fdf4;
        color: #16a34a;
        border: 1.5px solid #bbf7d0;
    }

    .ac-dot-wait {
        background: #f3f4f6;
        color: #9ca3af;
        border: 1.5px solid #e5e7eb;
    }

    .ac-label {
        font-size: 8px;
        color: #6b7280;
        font-weight: 600;
        white-space: nowrap;
    }

    .ac-arrow {
        font-size: 8px;
        color: #d8b4fe;
        flex-shrink: 0;
    }

    /* ── TICKET LIST (children) ── */
    .ticket-list {
        display: none;
    }

    .group-card.open .ticket-list {
        display: block;
    }

    .ticket-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px 10px 18px;
        border-bottom: 1px dashed #f3e8ff;
        text-decoration: none;
        color: inherit;
        transition: background .15s;
    }

    .ticket-row:last-child {
        border-bottom: none;
    }

    .ticket-row:hover {
        background: #faf8ff;
    }

    .tr-num {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: #f5f3ff;
        color: #7c3aed;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .tr-body {
        flex: 1;
        min-width: 0;
    }

    .tr-nomor {
        font-size: 12px;
        font-weight: 700;
        color: #1e1b4b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tr-meta {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tr-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }

    .tc-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-pending, .badge-waiting {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-approved, .badge-done {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-rejected {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }

    .badge-process, .badge-ready {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .badge-draft {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    .badge-feedback {
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

    .tr-arrow {
        font-size: 11px;
        color: #d8b4fe;
    }

    /* ── GROUP ACTION ── */
    .group-action {
        padding: 10px 14px 14px 18px;
        border-top: 1px solid #ede9fe;
        background: #fafaf9;
        display: flex;
        gap: 8px;
    }

    .btn-validate-all {
        flex: 1;
        background: linear-gradient(135deg, #7c3aed, #9333ea);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .3px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(124, 58, 237, .3);
        transition: opacity .2s, transform .15s;
        cursor: pointer;
    }

    .btn-validate-all:hover {
        opacity: .9;
        transform: scale(.98);
        color: #fff;
    }

    /* =====================
       EMPTY STATE
    ===================== */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #c4b5fd;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p {
        font-size: 13px;
        color: #9ca3af;
        margin: 0;
    }

    .empty-state small {
        font-size: 11px;
        color: #d1d5db;
    }

    /* =====================
       SKELETON
    ===================== */
    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
        width: 100%;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5
        }

        50% {
            opacity: 1
        }
    }

    .skeleton-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 14px;
        margin: 0 16px 10px;
    }

    /* =====================
       FLOATING REFRESH
    ===================== */
    .fab-refresh {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border: none;
        font-size: 16px;
        box-shadow: 0 6px 18px rgba(124, 58, 237, .4);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        transition: transform .2s;
    }

    .fab-refresh:active {
        transform: scale(.92) rotate(180deg);
    }

    /* =====================
       SIGNATURE PAD (Offcanvas)
    ===================== */
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

    #sig-pad-index {
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

    .filter-tab {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .notif-badge {
        min-width: 18px;
        height: 18px;
        padding: 0 5px;

        border-radius: 999px;

        background: #ef4444;
        color: #fff;

        font-size: 11px;
        font-weight: 700;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        line-height: 1;
    }
</style>
@endsection

@section('content_top')
<div class="card" style="position: relative; padding-bottom: 16px;">

    <!-- Back -->
    <a href="{{ route('approval.menu', $menu_id) }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <!-- Header -->
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-shield"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color:#623acf; font-size:20px;">Validasi Ticket Ubah/Hapus</h5>
        <small class="text-muted">Ticket Ubah/Hapus menunggu validasi Anda</small>
    </div>

    <!-- Summary Strip -->
    <div class="summary-strip">
        <div class="sum-card">
            <div class="sum-val" id="sum-total">
                <span class="skeleton" style="height:22px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Total Ubah/Hapus</div>
        </div>
        <div class="sum-card">
            <div class="sum-val green" id="sum-group">
                <span class="skeleton" style="height:22px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Grup Pengajuan</div>
        </div>
        <div class="sum-card">
            <div class="sum-val" id="sum-validasi">
                <span class="skeleton" style="height:22px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Total yang di Validasi</div>
        </div>
    </div>

    <!-- {{-- Search --}} -->
    <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" class="search-input" id="search-input"
            placeholder="Cari nomor ticket, pengaju...">
    </div>

    <!-- {{-- Filter Environment / Sistem --}} -->
    <div class="env-filter-wrap">
        <div class="env-filter-tabs">
            <div class="env-filter-tab active" data-env="all">
                <i class="fa-solid fa-layer-group"></i> Semua Sistem
                <span class="env-badge-count" id="count-env-all">0</span>
            </div>
            <div class="env-filter-tab" data-env="1">
                <i class="fa-solid fa-database"></i> Epicor
                <span class="env-badge-count" id="count-env-epicor">0</span>
            </div>
            <div class="env-filter-tab" data-env="2">
                <i class="fa-solid fa-bolt"></i> AOL SP
                <span class="env-badge-count" id="count-env-sp">0</span>
            </div>
            <div class="env-filter-tab" data-env="3">
                <i class="fa-solid fa-network-wired"></i> AOL AJA
                <span class="env-badge-count" id="count-env-aja">0</span>
            </div>
        </div>
    </div>

    <!-- {{-- Filter Status --}} -->
    <div class="filter-tabs">
        <div class="filter-tab" data-filter="all">Semua Status</div>
        <div class="filter-tab active" data-filter="menunggu">
            <i class="fa-solid fa-clock me-1"></i>Menunggu
            <span class="notif-badge" id="notif-menunggu">0</span>
        </div>
        <div class="filter-tab" data-filter="pending">
            <i class="fa-solid fa-hourglass-half me-1"></i>Pending
            <span class="notif-badge" id="notif-pending" style="display:none; background:#f59e0b;">0</span>
        </div>
        <div class="filter-tab" data-filter="divalidasi">
            <i class="fa-solid fa-circle-check me-1"></i>Divalidasi
        </div>
        <div class="filter-tab" data-filter="ditolak">
            <i class="fa-solid fa-circle-xmark me-1"></i>Ditolak
        </div>
    </div>

    <!-- {{-- Ticket / Group List --}} -->
    <div id="ticket-list" style="padding-bottom: 24px;">
        <!-- {{-- Skeleton --}} -->
        @for ($sk = 0; $sk < 4; $sk++)
            <div class="skeleton-card">
            <div class="d-flex justify-content-between mb-2">
                <span class="skeleton" style="height:14px; width:60%; display:inline-block;"></span>
                <span class="skeleton" style="height:14px; width:20%; display:inline-block;"></span>
            </div>
            <div class="d-flex gap-2 mb-2">
                <span class="skeleton" style="height:22px; width:90px; display:inline-block; border-radius:8px;"></span>
                <span class="skeleton" style="height:22px; width:80px; display:inline-block; border-radius:8px;"></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="skeleton" style="height:28px; width:28px; border-radius:50%; display:inline-block;"></span>
                <span class="skeleton" style="height:12px; width:40%; display:inline-block;"></span>
            </div>
    </div>
    @endfor
</div>

</div>

<!-- {{-- Floating Refresh --}} -->
<button class="fab-refresh" id="btn-refresh" title="Refresh">
    <i class="fa-solid fa-rotate-right"></i>
</button>
@endsection

@section('content')
<!-- {{-- ═══ OFFCANVAS: Tanda Tangan Validasi Group ═══ --}} -->
<div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1"
    id="off-canvas-sig-validate" style="height:75vh; z-index:1090 !important;">
    <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:40px; height:40px; border-radius:12px;
                        background:linear-gradient(135deg,#7c3aed,#a855f7);
                        display:flex; align-items:center; justify-content:center;
                        color:#fff; font-size:16px;
                        box-shadow:0 4px 12px rgba(124,58,237,.3); flex-shrink:0;">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <div class="fw-bold" style="font-size:13px; color:#1e1b4b;">Validasi Semua Tiket Sekaligus</div>
                <small class="text-muted" id="sig-root-title" style="font-size:11px;"></small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-auto">

        <div id="alert_sig" class="mb-2"></div>

        <!-- {{-- Signature Pad --}} -->
        <div class="sig-approver-card">
            <div class="sig-approver-header">
                <small>Tanda tangan validator: {{ $user_karyawan->name }}</small>
            </div>
            <div class="alert alert-danger mx-2 mt-2 py-2 px-3 rounded-3"
                id="alert-sig-empty" style="font-size:12px; display:none;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i>Tanda tangan wajib diisi.
            </div>
            <canvas id="sig-pad-index"></canvas>
            <div class="sig-footer">
                <button type="button" id="btn-sig-clear"
                    class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                    <i class="fa-solid fa-eraser me-1"></i> Hapus
                </button>
                <small class="text-muted" style="font-size:10px;">
                    <i class="fa-solid fa-hand-pointer me-1"></i>Tanda tangan di atas
                </small>
            </div>
        </div>

        <!-- {{-- Catatan --}} -->
        <div style="position:relative; margin-top:10px; margin-bottom:14px;">
            <span style="position:absolute; left:14px; top:14px; color:#7c3aed; font-size:13px; z-index:6; pointer-events:none;">
                <i class="fa-solid fa-comment-dots"></i>
            </span>
            <textarea id="sig-catatan"
                class="form-control"
                placeholder="Catatan validasi finance &amp; accounting (opsional)..."
                style="border-radius:12px; border:1.5px solid #ede9fe; font-size:13px;
                       padding:12px 14px 12px 42px; resize:none; min-height:70px;
                       background:#fdfcff; width:100%;"></textarea>
        </div>

        <!-- {{-- Action Buttons --}} -->
        <div class="d-flex gap-2">
            <button type="button" id="btn-confirm-reject"
                style="flex:1; background:linear-gradient(135deg,#dc2626,#ef4444); border:none;
                       border-radius:14px; padding:12px 8px; color:#fff; font-size:12px; font-weight:700;
                       letter-spacing:.3px; box-shadow:0 6px 18px rgba(220,38,38,.25);
                       transition:transform .15s; cursor:pointer;">
                <i class="fa-solid fa-circle-xmark me-1"></i>Tolak Semua
            </button>
            <button type="button" id="btn-confirm-pending"
                style="flex:1; background:linear-gradient(135deg,#d97706,#f59e0b); border:none;
                       border-radius:14px; padding:12px 8px; color:#fff; font-size:12px; font-weight:700;
                       letter-spacing:.3px; box-shadow:0 6px 18px rgba(217,119,6,.25);
                       transition:transform .15s; cursor:pointer;">
                <i class="fa-solid fa-hourglass-half me-1"></i>Pending
            </button>
            <button type="button" id="btn-confirm-validate"
                style="flex:1.5; background:linear-gradient(135deg,#7c3aed,#9333ea); border:none;
                       border-radius:14px; padding:12px 8px; color:#fff; font-size:12px; font-weight:700;
                       letter-spacing:.3px; box-shadow:0 6px 18px rgba(124,58,237,.3);
                       transition:transform .15s; cursor:pointer;">
                <i class="fa-solid fa-shield-check me-1"></i>Validasi
            </button>
        </div>

        <!-- {{-- Pending reason (hidden by default) --}} -->
        <div id="pending-reason-wrap" style="display:none; margin-top:10px;">
            <div style="position:relative;">
                <span style="position:absolute; left:14px; top:14px; color:#d97706; font-size:13px; z-index:6; pointer-events:none;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </span>
                <textarea id="alasan-pending"
                    class="form-control"
                    placeholder="Alasan pending (wajib diisi, misal kelengkapan lampiran yang ditunggu dari requestor)..."
                    style="border-radius:12px; border:1.5px solid #fde68a; font-size:13px;
                           padding:12px 14px 12px 42px; resize:none; height:72px;
                           background:#fffbeb; width:100%;"></textarea>
            </div>
            <div class="d-flex gap-3 mt-3">
                <button type="button" id="btn-do-pending"
                    class="btn btn-warning btn-sm px-4 py-2 rounded-pill fw-bold text-white flex-fill">
                    <i class="fa-solid fa-hourglass-half me-1"></i> Ya, Pending Tiket Ini
                </button>
                <button type="button" id="btn-cancel-pending"
                    class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-pill flex-fill">
                    Batal
                </button>
            </div>
        </div>

        <!-- {{-- Reject reason (hidden by default) --}} -->
        <div id="reject-reason-wrap" style="display:none; margin-top:10px;">
            <div style="position:relative;">
                <span style="position:absolute; left:14px; top:14px; color:#ef4444; font-size:13px; z-index:6; pointer-events:none;">
                    <i class="fa-solid fa-comment-dots"></i>
                </span>
                <textarea id="alasan-reject"
                    class="form-control"
                    placeholder="Alasan penolakan (wajib diisi minimal 10 karakter)..."
                    style="border-radius:12px; border:1.5px solid #fecaca; font-size:13px;
                           padding:12px 14px 12px 42px; resize:none; height:72px;
                           background:#fff5f5; width:100%;"></textarea>
            </div>
            <div class="d-flex gap-3 mt-3">
                <button type="button" id="btn-do-reject"
                    class="btn btn-danger btn-sm px-4 py-2 rounded-pill fw-bold flex-fill">
                    <i class="fa-solid fa-xmark me-1"></i> Ya, Tolak Semua
                </button>
                <button type="button" id="btn-cancel-reject"
                    class="btn btn-outline-secondary btn-sm px-4 py-2 rounded-pill flex-fill">
                    Batal
                </button>
            </div>
        </div>

    </div>
</div>

<!-- {{-- Offcanvas Logout --}} -->
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

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });

    $(document).ready(function() {

        const MENU_ID = '{{ $menu_id }}';
        const LIST_URL = '{{ route("approval.ticket.validator.list", $menu_id) }}';
        const SAVE_URL = '{{ route("approval.ticket.validator.group.save", $menu_id) }}';

        let allTickets = [];
        let allGroups = [];
        let activeFilter = 'menunggu';
        let activeEnv = 'all'; // 'all', '1' (Epicor), '2' (AOL SP), '3' (AOL AJA)
        let searchQuery = '';
        let currentRootId = null; // root_ticket_id yang sedang divalidasi

        // Cek parameter URL (env / environment) untuk filter awal
        const urlParams = new URLSearchParams(window.location.search);
        const qEnv = urlParams.get('env') || urlParams.get('environment');
        if (qEnv) {
            let matchedEnv = null;
            const qLower = qEnv.toLowerCase();
            if (['1', 'epicor', 'epic'].includes(qLower)) matchedEnv = '1';
            else if (['2', 'sp', 'aol-sp', 'aol_sp'].includes(qLower)) matchedEnv = '2';
            else if (['3', 'aja', 'aol-aja', 'aol_aja'].includes(qLower)) matchedEnv = '3';
            else if (['all', 'semua'].includes(qLower)) matchedEnv = 'all';

            if (matchedEnv) {
                activeEnv = matchedEnv;
                $('.env-filter-tab').removeClass('active');
                $(`.env-filter-tab[data-env="${matchedEnv}"]`).addClass('active');
            }
        }

        /* ─── SIGNATURE PAD ─── */
        const canvas = document.getElementById('sig-pad-index');
        const sigPad = new SignaturePad(canvas);

        function resizeSigCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            sigPad.clear();
        }

        /* Resize saat offcanvas terbuka agar canvas mengisi lebar yang tepat */
        document.getElementById('off-canvas-sig-validate')
            .addEventListener('shown.bs.offcanvas', resizeSigCanvas);

        window.addEventListener('resize', resizeSigCanvas);

        $('#btn-sig-clear').on('click', () => {
            sigPad.clear();
            $('#alert-sig-empty').hide();
        });

        /* ─── STATUS CONFIG ─── */
        const STATUS_MAP = {
            'pending': {
                label: 'Diajukan',
                cls: 'badge-pending',
                icon: 'fa-clock',
                filter: 'menunggu'
            },
            'on_hold': {
                label: 'Pending Lampiran',
                cls: 'badge-waiting',
                icon: 'fa-hourglass-half',
                filter: 'pending'
            },
            'approved': {
                label: 'Divalidasi',
                cls: 'badge-approved',
                icon: 'fa-clipboard-check',
                filter: 'divalidasi'
            },
            'rejected': {
                label: 'Ditolak',
                cls: 'badge-rejected',
                icon: 'fa-ban',
                filter: 'ditolak'
            },
            'draft': {
                label: 'Draft',
                cls: 'badge-draft',
                icon: 'fa-pen-to-square',
                filter: 'menunggu'
            },
            'T00': { label: 'Draft', cls: 'badge-draft', icon: 'fa-pen-to-square', filter: 'menunggu' },
            'T01': { label: 'Menunggu Persetujuan', cls: 'badge-pending', icon: 'fa-clock', filter: 'menunggu' },
            'T02': { label: 'Disetujui', cls: 'badge-approved', icon: 'fa-circle-check', filter: 'disetujui' },
            'T03': { label: 'Ditugaskan', cls: 'badge-process', icon: 'fa-user-check', filter: 'disetujui' },
            'T04': { label: 'Dikerjakan', cls: 'badge-process', icon: 'fa-spinner', filter: 'disetujui' },
            'T05': { label: 'Menunggu Respon Peminta', cls: 'badge-waiting', icon: 'fa-hourglass-half', filter: 'disetujui' },
            'T06': { label: 'Selesai', cls: 'badge-approved', icon: 'fa-circle-check', filter: 'disetujui' },
            'T07': { label: 'Respon Balasan Peminta', cls: 'badge-feedback', icon: 'fa-comment-dots', filter: 'disetujui' },
            'T08': { label: 'Ditolak', cls: 'badge-rejected', icon: 'fa-ban', filter: 'ditolak' },
            'T09': { label: 'Disetujui Atasan 1', cls: 'badge-approved', icon: 'fa-check-double', filter: 'disetujui' },
            'T10': { label: 'Disetujui Atasan 2', cls: 'badge-approved', icon: 'fa-check-double', filter: 'disetujui' },
            'T11': { label: 'Menunggu Divalidasi', cls: 'badge-approved', icon: 'fa-clipboard-check', filter: 'disetujui' },
            'T12': { label: 'Menunggu Pemeriksaan', cls: 'badge-waiting', icon: 'fa-magnifying-glass', filter: 'menunggu' },
            'T13': { label: 'Menunggu Ditugaskan', cls: 'badge-waiting', icon: 'fa-user-check', filter: 'disetujui' },
            'T14': { label: 'Pending', cls: 'badge-waiting', icon: 'fa-spinner', filter: 'disetujui' },
            'T15': { label: 'Ditutup Otomatis Sistem', cls: 'badge-done', icon: 'fa-circle-check', filter: 'disetujui' },
        };

        /* ─── LOAD DATA ─── */
        function loadData() {
            $('#ticket-list').html(skeletonHtml());

            $.ajax({
                url: LIST_URL,
                type: 'GET',
                success: function(res) {
                    if (res.code !== 200 || !res.tickets || res.tickets.length === 0) {
                        allTickets = [];
                        allGroups = [];
                        showEmpty('Tidak ada ticket yang menunggu validasi Anda.');
                        updateStats(0, 0, 0, 0);
                        updateStatsAndCounts();
                        return;
                    }

                    allTickets = res.tickets;
                    allGroups = groupByRoot(allTickets);
                    renderGroups();
                },
                error: function(xhr) {
                    allTickets = [];
                    allGroups = [];
                    showEmpty(window.getFriendlyErrorMessage(xhr, 'Gagal memuat data validasi. Silakan coba lagi.'));
                    updateStats(0, 0, 0, 0);
                    updateStatsAndCounts();
                }
            });
        }

        /* ─── RESOLVE ENVIRONMENT KATEGORI ─── */
        function getTicketEnv(t) {
            if (!t) return 1;
            const nomor = t.nomor_ticket || '';
            if (nomor.includes('AOL-AJA')) return 3;
            if (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS')) return 2;
            if (nomor.includes('EPIC')) return 1;
            let kat = Number(t.environment_kategori || t.environment);
            if (kat === 2 || kat === 3) return kat;
            return 1;
        }

        function getEnvBadge(kategori, name, nomor) {
            let kat = getTicketEnv({ environment_kategori: kategori, nomor_ticket: nomor });
            let cls = 'badge-env-epicor';
            let icon = 'fa-database';
            let label = 'Epicor';
            if (kat === 1) {
                cls = 'badge-env-epicor';
                icon = 'fa-database';
                label = 'Epicor';
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

        /* ─── GROUP BY root_ticket_id ─── */
        function groupByRoot(tickets) {
            const map = {};
            tickets.forEach(function(t) {
                const key = t.root_ticket_id || t.id;
                if (!map[key]) {
                    map[key] = {
                        root_id: key,
                        root_nomor: t.root_nomor || t.nomor_ticket,
                        root_menu: t.menu_name || '—',
                        root_submenu: t.submenu_name || '—',
                        root_tanggal: t.tanggal_ticket || null,
                        root_env_kategori: getTicketEnv(t),
                        root_env_name: t.environment_name,
                        tickets: []
                    };
                }
                map[key].tickets.push(t);
            });
            return Object.values(map);
        }

        /* ─── UPDATE STATS & COUNTERS ─── */
        function updateStatsAndCounts() {
            // 1. Update counter per environment berdasarkan status tab yang aktif
            let statusScopedTickets = allTickets;
            if (activeFilter !== 'all') {
                statusScopedTickets = allTickets.filter(t => {
                    const s = STATUS_MAP[t.validator_status];
                    return s && s.filter === activeFilter;
                });
            }

            const epicorCount = statusScopedTickets.filter(t => getTicketEnv(t) === 1).length;
            const spCount     = statusScopedTickets.filter(t => getTicketEnv(t) === 2).length;
            const ajaCount    = statusScopedTickets.filter(t => getTicketEnv(t) === 3).length;

            $('#count-env-all').text(statusScopedTickets.length);
            $('#count-env-epicor').text(epicorCount);
            $('#count-env-sp').text(spCount);
            $('#count-env-aja').text(ajaCount);

            // 2. Update notifikasi tab Menunggu & kartu summary sesuai environment aktif
            let envScopedTickets = allTickets;
            if (activeEnv !== 'all') {
                envScopedTickets = allTickets.filter(t => String(getTicketEnv(t)) === String(activeEnv));
            }

            const totalPendingEnv = envScopedTickets.filter(t => t.validator_status === 'pending').length;
            if (totalPendingEnv > 0) {
                $('#notif-menunggu')
                    .text(totalPendingEnv > 99 ? '99+' : totalPendingEnv)
                    .show();
            } else {
                $('#notif-menunggu').hide();
            }

            const totalOnHoldEnv = envScopedTickets.filter(t => t.validator_status === 'on_hold').length;
            if (totalOnHoldEnv > 0) {
                $('#notif-pending')
                    .text(totalOnHoldEnv > 99 ? '99+' : totalOnHoldEnv)
                    .show();
            } else {
                $('#notif-pending').hide();
            }

            const totalValidatedEnv = envScopedTickets.filter(t => t.validator_status === 'approved').length;
            const envGroups = allGroups.filter(g =>
                activeEnv === 'all' || g.tickets.some(t => String(getTicketEnv(t)) === String(activeEnv))
            );

            updateStats(
                envScopedTickets.length,
                envGroups.length,
                envScopedTickets.reduce((s, t) => s + (t.detail_count || 0), 0),
                totalValidatedEnv
            );
        }

        /* ─── RENDER GROUPS ─── */
        function renderGroups() {
            updateStatsAndCounts();

            let filtered = allGroups;

            // 1. Filter Grup berdasarkan Environment
            if (activeEnv !== 'all') {
                filtered = filtered.filter(g =>
                    g.tickets.some(t => String(getTicketEnv(t)) === String(activeEnv))
                );
            }

            // 2. Filter Grup berdasarkan Status
            if (activeFilter !== 'all') {
                filtered = filtered.filter(g =>
                    g.tickets.some(t => {
                        const s = STATUS_MAP[t.validator_status];
                        return s && s.filter === activeFilter;
                    })
                );
            }

            // 3. Filter berdasarkan Search
            if (searchQuery.trim() !== '') {
                const q = searchQuery.toLowerCase();
                filtered = filtered.filter(g =>
                    g.root_nomor.toLowerCase().includes(q) ||
                    g.root_menu.toLowerCase().includes(q) ||
                    g.root_submenu.toLowerCase().includes(q) ||
                    g.tickets.some(t =>
                        (t.nomor_ticket || '').toLowerCase().includes(q) ||
                        (t.requestor_name || '').toLowerCase().includes(q)
                    )
                );
            }

            if (filtered.length === 0) {
                let emptyMsg = 'Tidak ada ticket ditemukan.';
                if (activeEnv !== 'all' || activeFilter !== 'all' || searchQuery.trim() !== '') {
                    const envNames = { '1': 'Epicor', '2': 'AOL SP', '3': 'AOL AJA' };
                    const envText = envNames[activeEnv] ? ` pada sistem ${envNames[activeEnv]}` : '';
                    const statusText = activeFilter !== 'all' ? ` status "${activeFilter}"` : '';
                    emptyMsg = `Tidak ada ticket${statusText}${envText}.`;
                }
                showEmpty(emptyMsg);
                return;
            }

            let html = '';

            filtered.forEach(function(group, gi) {
                const tanggal = group.root_tanggal ? formatDate(group.root_tanggal) : '—';
                const chainHtml = buildChain(group.tickets[0]);

                /* ── Filter baris tiket yang ditampilkan di dalam grup sesuai env & status ── */
                let visibleTickets = group.tickets;
                if (activeEnv !== 'all') {
                    visibleTickets = visibleTickets.filter(t => String(getTicketEnv(t)) === String(activeEnv));
                }
                if (activeFilter !== 'all') {
                    visibleTickets = visibleTickets.filter(t => {
                        const s = STATUS_MAP[t.validator_status];
                        return s && s.filter === activeFilter;
                    });
                }
                if (searchQuery.trim() !== '') {
                    const q = searchQuery.toLowerCase();
                    if (!group.root_nomor.toLowerCase().includes(q) &&
                        !group.root_menu.toLowerCase().includes(q) &&
                        !group.root_submenu.toLowerCase().includes(q)) {
                        visibleTickets = visibleTickets.filter(t =>
                            (t.nomor_ticket || '').toLowerCase().includes(q) ||
                            (t.requestor_name || '').toLowerCase().includes(q)
                        );
                    }
                }
                const ticketsToRender = visibleTickets.length > 0 ? visibleTickets : group.tickets;

                /* ── Ticket rows (hanya link ke detail/show) ── */
                let rowsHtml = '';
                ticketsToRender.forEach(function(t, ti) {
                    const st = STATUS_MAP[t.validator_status] ?? STATUS_MAP['pending'];
                    const detUrl = t.page_url || `/approval/ticket/validator/${MENU_ID}/${t.id}`;
                    const rowEnvBadge = getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket);

                    rowsHtml += `
                    <a class="ticket-row" href="${detUrl}">
                        <div class="tr-num">${ti + 1}</div>
                        <div class="tr-body">
                            <div class="tr-nomor d-flex align-items-center flex-wrap gap-1">
                                <span><i class="fa-solid fa-ticket fa-xs me-1" style="color:#c4b5fd;"></i>${esc(t.nomor_ticket || '—')}</span>
                                ${rowEnvBadge}
                            </div>
                            <div class="tr-meta">
                                <i class="fa-regular fa-user fa-xs me-1"></i>${esc(t.requestor_name || '—')}
                                &nbsp;·&nbsp;
                                <i class="fa-solid fa-layer-group fa-xs me-1"></i>${t.detail_count || 0} detail
                            </div>
                        </div>
                        <div class="tr-right">
                            <span class="tc-status-badge ${st.cls}">
                                <i class="fa-solid ${st.icon} fa-xs"></i>${st.label}
                            </span>
                            <span style="font-size:10px; color:#c4b5fd;">${formatDate(t.tanggal_ticket)}</span>
                        </div>
                        <i class="fa-solid fa-chevron-right tr-arrow ms-1"></i>
                    </a>`;
                });

                /* ── Aksi validasi dipindahkan ke halaman detail ticket sesuai permintaan validator ── */
                const onHoldCount = ticketsToRender.filter(t =>
                    t.validator_status === 'on_hold'
                ).length;

                let actionHtml = '';
                if (onHoldCount > 0) {
                    actionHtml = `
                    <div class="group-action" style="background:#fffbeb; border-top:1px dashed #fde68a;">
                        <div class="d-flex align-items-center justify-content-between w-100 py-1" style="font-size:11.5px; color:#b45309;">
                            <div>
                                <i class="fa-solid fa-hourglass-half me-1"></i> <strong>${onHoldCount}</strong> ticket dipending validator
                            </div>
                            <span class="badge" style="background:#fef3c7; color:#92400e; font-size:10px;">
                                Klik tiket untuk cek / resume
                            </span>
                        </div>
                    </div>`;
                }

                const groupEnvBadge = getEnvBadge(group.root_env_kategori, group.root_env_name, group.root_nomor);
                const countBadgeText = ticketsToRender.length < group.tickets.length ?
                    `${ticketsToRender.length} / ${group.tickets.length}` :
                    `${group.tickets.length}`;

                html += `
                <div class="group-card" data-gi="${gi}">
                    <div class="group-header">
                        <div class="group-avatar">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <div class="group-meta">
                            <div class="gm-root">${esc(group.root_nomor)}</div>
                            <div class="mt-1">${groupEnvBadge}</div>
                        </div>
                        <div class="group-right">
                            <span class="group-count">
                                <i class="fa-solid fa-ticket fa-xs"></i>${countBadgeText}
                            </span>
                            <span class="group-date">${tanggal}</span>
                        </div>
                        <i class="fa-solid fa-chevron-down group-chevron"></i>
                    </div>
                    ${chainHtml}
                    <div class="ticket-list">${rowsHtml}</div>
                    ${actionHtml}
                </div>`;
            });

            $('#ticket-list').html(html);
            $('#ticket-list .group-card').first().addClass('open');
        }

        /* ─── APPROVAL CHAIN MINI STRIP ─── */
        function buildChain(ticket) {
            if (!ticket) return '';
            const steps = [{
                    label: 'Approve 1',
                    done: ticket.approve1
                },
                {
                    label: 'Approve 2',
                    done: ticket.approve2
                },
                {
                    label: 'Checker',
                    done: ticket.checked
                },
                {
                    label: 'Validator',
                    done: ticket.is_validated
                },
            ];
            let html = '<div class="approval-chain">';
            steps.forEach(function(s, i) {
                const dc = s.done ? 'ac-dot-done' : 'ac-dot-wait';
                const icon = s.done ? 'fa-check' : 'fa-clock';
                const color = s.done ? '#16a34a' : '#9ca3af';
                html += `
                <div class="ac-step">
                    <div class="ac-dot ${dc}">
                        <i class="fa-solid ${icon}" style="font-size:9px;"></i>
                    </div>
                    <div class="ac-label" style="color:${color};">${s.label}</div>
                </div>`;
                if (i < steps.length - 1)
                    html += `<i class="fa-solid fa-chevron-right ac-arrow" style="margin-bottom:10px;"></i>`;
            });
            return html + '</div>';
        }

        /* ─── TOGGLE GROUP ─── */
        $(document).on('click', '.group-header', function() {
            $(this).closest('.group-card').toggleClass('open');
        });

        /* ─── BUKA OFFCANVAS SIGNATURE ─── */
        $(document).on('click', '.btn-open-sig', function(e) {
            e.preventDefault();
            e.stopPropagation(); // jangan trigger toggle grup

            currentRootId = $(this).data('root-id');
            const rootNomor = $(this).data('root-nomor');
            const count = $(this).data('count');

            $('#sig-root-title').text(
                rootNomor + (count > 1 ? ` · ${count} ticket` : '')
            );

            /* Reset state */
            sigPad.clear();
            $('#alert-sig-empty').hide();
            $('#sig-catatan').val('');
            $('#reject-reason-wrap').hide();
            $('#alasan-reject').val('');
            $('#pending-reason-wrap').hide();
            $('#alasan-pending').val('');
            $('#alert_sig').empty();

            /* Tombol kembali ke normal */
            resetBtn('#btn-confirm-validate', '<i class="fa-solid fa-shield-check me-1"></i>Validasi');
            resetBtn('#btn-confirm-reject', '<i class="fa-solid fa-circle-xmark me-1"></i>Tolak Semua');
            resetBtn('#btn-confirm-pending', '<i class="fa-solid fa-hourglass-half me-1"></i>Pending');

            new bootstrap.Offcanvas(
                document.getElementById('off-canvas-sig-validate')
            ).show();
        });

        /* ─── TOMBOL TOLAK → tampilkan area alasan ─── */
        $(document).on('click', '#btn-confirm-reject', function() {
            if ($('#reject-reason-wrap').is(':visible')) {
                $('#reject-reason-wrap').slideUp(200);
                resetBtn('#btn-confirm-reject', '<i class="fa-solid fa-circle-xmark me-1"></i>Tolak Semua');
                return;
            }
            $('#pending-reason-wrap').slideUp(150);
            resetBtn('#btn-confirm-pending', '<i class="fa-solid fa-hourglass-half me-1"></i>Pending');
            $('#reject-reason-wrap').slideDown(200);
            $(this).text('Batal Tolak').removeClass('btn-do-reject');
        });

        $(document).on('click', '#btn-cancel-reject', function() {
            $('#reject-reason-wrap').slideUp(200);
            resetBtn('#btn-confirm-reject', '<i class="fa-solid fa-circle-xmark me-1"></i>Tolak Semua');
        });

        /* ─── TOMBOL PENDING → tampilkan area alasan ─── */
        $(document).on('click', '#btn-confirm-pending', function() {
            if ($('#pending-reason-wrap').is(':visible')) {
                $('#pending-reason-wrap').slideUp(200);
                resetBtn('#btn-confirm-pending', '<i class="fa-solid fa-hourglass-half me-1"></i>Pending');
                return;
            }
            $('#reject-reason-wrap').slideUp(150);
            resetBtn('#btn-confirm-reject', '<i class="fa-solid fa-circle-xmark me-1"></i>Tolak Semua');
            $('#pending-reason-wrap').slideDown(200);
            $(this).text('Batal Pending');
        });

        $(document).on('click', '#btn-cancel-pending', function() {
            $('#pending-reason-wrap').slideUp(200);
            resetBtn('#btn-confirm-pending', '<i class="fa-solid fa-hourglass-half me-1"></i>Pending');
        });

        $(document).on('click', '#btn-do-pending', function() {
            submitGroupValidation('pending');
        });

        /* ─── KONFIRMASI VALIDASI ─── */
        $(document).on('click', '#btn-confirm-validate', function() {
            if (sigPad.isEmpty()) {
                $('#alert-sig-empty').show();
                canvas.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }
            submitGroupValidation('validate');
        });

        /* ─── KONFIRMASI TOLAK ─── */
        $(document).on('click', '#btn-do-reject', function() {
            if (sigPad.isEmpty()) {
                $('#alert-sig-empty').show();
                canvas.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }
            submitGroupValidation('reject');
        });

        /* ─── SUBMIT GROUP VALIDATION ─── */
        function submitGroupValidation(action) {
            if (!currentRootId) return;

            let $btn;
            if (action === 'validate') {
                $btn = $('#btn-confirm-validate');
            } else if (action === 'reject') {
                $btn = $('#btn-do-reject');
            } else {
                $btn = $('#btn-do-pending');
            }
            const origHtml = $btn.html();

            $btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('root_ticket_id', currentRootId);
            formData.append('action', action);
            if (!sigPad.isEmpty()) {
                formData.append('signature', sigPad.toDataURL());
            }
            formData.append('comment_text', $('#sig-catatan').val().trim());

            if (action === 'reject') {
                const alasan = $('#alasan-reject').val().trim();
                if (!alasan) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#alert_sig').html(
                        '<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">' +
                        '<i class="fa-solid fa-triangle-exclamation me-1"></i>Alasan penolakan wajib diisi.</div>'
                    );
                    return;
                }
                formData.append('alasan_reject', alasan);
            }

            if (action === 'pending') {
                const alasan = $('#alasan-pending').val().trim();
                if (!alasan) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#alert_sig').html(
                        '<div class="alert alert-warning py-2 px-3 rounded-3 mb-0" style="font-size:12px;">' +
                        '<i class="fa-solid fa-triangle-exclamation me-1"></i>Alasan pending (menunggu lampiran) wajib diisi.</div>'
                    );
                    return;
                }
                formData.append('alasan_pending', alasan);
            }

            $.ajax({
                url: SAVE_URL,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#alert_sig').html(
                        '<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">' +
                        window.getFriendlyErrorMessage(xhr, 'Terjadi gangguan saat memproses.') + '</div>'
                    );
                },

                success: function(data) {
                    $btn.prop('disabled', false).html(origHtml);

                    if (data.code === 200) {
                        bootstrap.Offcanvas.getInstance(
                            document.getElementById('off-canvas-sig-validate')
                        )?.hide();

                        Swal.fire({
                            icon: action === 'validate' ? 'success' : (action === 'pending' ? 'info' : 'warning'),
                            title: action === 'validate' ? 'Berhasil Divalidasi' : (action === 'pending' ? 'Tiket Dipending' : 'Ticket Ditolak'),
                            text: data.message,
                            timer: 1800,
                            showConfirmButton: false,
                        }).then(() => {
                            loadData(); // refresh list tanpa redirect
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
                            `<div class="alert alert-danger py-2 px-3 rounded-3 mb-0" style="font-size:12px;">${window.getFriendlyErrorMessage(data.message, 'Gagal memproses validasi.')}</div>`
                        );
                    }
                }
            });
        }

        /* ─── ENVIRONMENT FILTER TABS ─── */
        $(document).on('click', '.env-filter-tab', function() {
            $('.env-filter-tab').removeClass('active');
            $(this).addClass('active');
            activeEnv = String($(this).data('env'));
            renderGroups();
        });

        /* ─── FILTER TABS ─── */
        $(document).on('click', '.filter-tab', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('filter');
            renderGroups();
        });

        /* ─── SEARCH ─── */
        let searchTimer;
        $('#search-input').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                searchQuery = $(this).val();
                renderGroups();
            }, 300);
        });

        /* ─── REFRESH ─── */
        $('#btn-refresh').on('click', function() {
            const icon = $(this).find('i');
            icon.addClass('fa-spin');
            loadData();
            setTimeout(() => icon.removeClass('fa-spin'), 1000);
        });

        /* ─── HELPERS ─── */
        function showEmpty(msg) {
            $('#ticket-list').html(`
            <div class="empty-state">
                <i class="fa-solid fa-shield-check"></i>
                <p>${msg}</p>
                <small>Coba ubah filter atau refresh halaman</small>
            </div>`);
        }

        function updateStats(total, group, detail, validasi) {
            $('#sum-total').text(total);
            $('#sum-group').text(group);
            $('#sum-detail').text(detail);
            $('#sum-validasi').text(validasi);
        }

        function resetBtn(sel, html) {
            $(sel).prop('disabled', false).html(html);
        }

        function skeletonHtml() {
            let h = '';
            for (let i = 0; i < 4; i++) {
                h += `<div class="skeleton-card">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="skeleton" style="height:14px; width:60%; display:inline-block;"></span>
                        <span class="skeleton" style="height:14px; width:20%; display:inline-block;"></span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="skeleton" style="height:22px; width:90px; display:inline-block; border-radius:8px;"></span>
                        <span class="skeleton" style="height:22px; width:70px; display:inline-block; border-radius:8px;"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="skeleton" style="height:28px; width:28px; border-radius:50%; display:inline-block;"></span>
                        <span class="skeleton" style="height:12px; width:40%; display:inline-block;"></span>
                    </div>
                </div>`;
            }
            return h;
        }

        function formatDate(str) {
            if (!str) return '—';
            try {
                return new Date(str).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            } catch (e) {
                return str;
            }
        }

        function esc(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function escAttr(str) {
            return String(str || '').replace(/'/g, "&#39;").replace(/"/g, '&quot;');
        }

        /* ─── INIT ─── */
        loadData();

        /* ─── PAGE UNLOAD ─── */
        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div>'.repeat(3),
                showCancelButton: false,
                showConfirmButton: false,
            });
        };
    });
</script>
@endsection