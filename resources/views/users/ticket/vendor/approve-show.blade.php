@extends('users.layouts.main')
@section('title') APPS | Approval Pendaftaran Vendor @endsection

@section('css')
<style>
    /* ── BASE ── */
    .vnd-wrap { padding: 0 0 20px; }

    /* ── HEADER ── */
    .vnd-header {
        padding: 24px 20px 16px;
        text-align: center;
    }
    .vnd-icon {
        width: 60px; height: 60px; border-radius: 18px;
        background: #ede9fe; color: #7c3aed; font-size: 24px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto; border: 1.5px solid #c4b5fd;
    }
    .vnd-title { font-size: 17px; font-weight: 700; color: #3b0764; margin-top: 10px; margin-bottom: 2px; }
    .vnd-sub   { font-size: 12px; color: #9ca3af; line-height: 1.5; }

    /* ── STEP BAR ── */
    .step-bar {
        display: flex; align-items: center;
        padding: 0 20px 18px; gap: 0;
        overflow-x: auto; scrollbar-width: none;
    }
    .step-bar::-webkit-scrollbar { display: none; }
    .step-item { display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0; }
    .step-dot {
        width: 26px; height: 26px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700;
        background: #f5f3ff; color: #a78bfa; border: 1.5px solid #ddd6fe;
        transition: all .2s;
    }
    .step-dot.done { background: #7c3aed; color: #fff; border-color: #7c3aed; }
    .step-label { font-size: 9px; color: #a78bfa; font-weight: 600; white-space: nowrap; }
    .step-line {
        flex: 1; height: 1.5px; background: #ede9fe;
        min-width: 10px; margin-bottom: 14px; transition: background .2s;
    }
    .step-line.done { background: #7c3aed; }

    /* ── SISTEM / KATEGORI SELECTOR (readonly) ── */
    .sistem-wrap {
        margin: 0 14px 12px; background: #fff;
        border: 1px solid #e8e2fc; border-radius: 14px; overflow: hidden;
    }
    .sistem-head {
        display: flex; align-items: center; gap: 8px;
        padding: 11px 14px; background: #faf5ff;
        border-bottom: 1px solid #ede9fe;
    }
    .sistem-head .sh-icon {
        width: 30px; height: 30px; border-radius: 9px;
        background: #7c3aed; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
    }
    .sistem-head .sh-title  { font-size: 11.5px; font-weight: 700; color: #3b0764; flex: 1; }
    .sistem-body { padding: 14px; }
    .sistem-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

    .sistem-opt {
        border: 1.5px solid #e8e2fc; border-radius: 11px; padding: 14px 12px;
        background: #fdfdff;
        display: flex; flex-direction: column; align-items: center;
        gap: 8px; text-align: center; position: relative;
    }
    .sistem-opt input[type=radio] { display: none; }
    .sistem-opt .so-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; opacity: .45;
    }
    .sistem-opt .so-name  { font-size: 12px; font-weight: 700; color: #374151; line-height: 1.2; }
    .sistem-opt .so-desc  { font-size: 10px; color: #9ca3af; line-height: 1.3; }
    .sistem-opt .so-check {
        position: absolute; top: 8px; right: 8px;
        width: 16px; height: 16px; border-radius: 50%;
        border: 1.5px solid #ddd6fe; background: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 8px; color: transparent;
    }
    .sistem-opt.epicor-opt:has(input:checked) { border-color: #7c3aed; background: #f5f3ff; }
    .sistem-opt.epicor-opt:has(input:checked) .so-icon { background: #ede9fe; color: #7c3aed; opacity: 1; }
    .sistem-opt.epicor-opt:has(input:checked) .so-name  { color: #5b21b6; }
    .sistem-opt.epicor-opt:has(input:checked) .so-check { background: #7c3aed; border-color: #7c3aed; color: #fff; }

    .sistem-opt.accurate-opt:has(input:checked) { border-color: #2563eb; background: #eff6ff; }
    .sistem-opt.accurate-opt:has(input:checked) .so-icon { background: #dbeafe; color: #1d4ed8; opacity: 1; }
    .sistem-opt.accurate-opt:has(input:checked) .so-name  { color: #1e40af; }
    .sistem-opt.accurate-opt:has(input:checked) .so-check { background: #2563eb; border-color: #2563eb; color: #fff; }

    .sistem-opt.bb-opt:has(input:checked) { border-color: #16a34a; background: #f0fdf4; }
    .sistem-opt.bb-opt:has(input:checked) .so-icon { background: #bbf7d0; color: #15803d; opacity: 1; }
    .sistem-opt.bb-opt:has(input:checked) .so-name  { color: #15803d; }
    .sistem-opt.bb-opt:has(input:checked) .so-check { background: #16a34a; border-color: #16a34a; color: #fff; }

    .sistem-opt.nbb-opt:has(input:checked) { border-color: #d97706; background: #fffbeb; }
    .sistem-opt.nbb-opt:has(input:checked) .so-icon { background: #fde68a; color: #b45309; opacity: 1; }
    .sistem-opt.nbb-opt:has(input:checked) .so-name  { color: #b45309; }
    .sistem-opt.nbb-opt:has(input:checked) .so-check { background: #d97706; border-color: #d97706; color: #fff; }

    /* ── SECTIONS ── */
    .vnd-section {
        background: #fff; border: 1px solid #e8e2fc;
        border-radius: 14px; margin: 0 14px 12px; overflow: hidden;
    }
    .vnd-section-head {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 14px; background: #faf9ff;
        border-bottom: 1px solid #ede9fe;
        cursor: pointer; user-select: none;
    }
    .vnd-section-head .sec-icon {
        width: 30px; height: 30px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
    }
    .vnd-section-head .sec-title { font-size: 11.5px; font-weight: 700; color: #3b0764; flex: 1; letter-spacing: .2px; }
    .vnd-section-head .sec-counter {
        font-size: 10px; font-weight: 600; color: #a78bfa;
        background: #f5f3ff; border: 1px solid #ddd6fe;
        border-radius: 20px; padding: 2px 8px; flex-shrink: 0;
    }
    .vnd-section-head .sec-chevron { font-size: 10px; color: #a855f7; transition: transform .22s; flex-shrink: 0; }
    .vnd-section-head.collapsed .sec-chevron { transform: rotate(-90deg); }
    .vnd-section-body { padding: 14px; }

    /* ── FIELDS ── */
    .vnd-group { margin-bottom: 13px; }
    .vnd-group:last-child { margin-bottom: 0; }
    .vnd-label {
        font-size: 11px; font-weight: 700; color: #4b5563;
        margin-bottom: 5px; display: flex; align-items: center; gap: 5px;
    }
    .vnd-label i { color: #a855f7; font-size: 10px; width: 12px; text-align: center; }
    .vnd-label .req { color: #e24b4a; font-size: 10px; margin-left: 1px; }
    .vnd-input, .vnd-select, .vnd-textarea {
        width: 100%; border: 1px solid #e8e2fc; border-radius: 10px;
        padding: 9px 13px; font-size: 13px; color: #1f2937;
        background: #f9f8fd; outline: none;
        appearance: none; -webkit-appearance: none; box-sizing: border-box;
    }
    .vnd-input[readonly], .vnd-textarea[readonly] {
        background: #f9f8fd; cursor: default;
    }
    .vnd-input:not([readonly]):focus, .vnd-select:focus, .vnd-textarea:not([readonly]):focus {
        border-color: #a855f7; box-shadow: 0 0 0 3px rgba(168,85,247,.08); background: #fff;
    }
    .vnd-input:not([readonly]), .vnd-textarea:not([readonly]) {
        background: #fff; border-color: #c4b5fd;
    }
    .vnd-input::placeholder, .vnd-textarea::placeholder { color: #c4b5fd; font-size: 12px; }
    .vnd-textarea { resize: none; min-height: 68px; line-height: 1.5; }
    .vnd-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    @media (max-width: 380px) { .vnd-row { grid-template-columns: 1fr; } }

    .radio-group { display: flex; flex-wrap: wrap; gap: 7px; }
    .radio-opt {
        display: flex; align-items: center; gap: 5px;
        border: 1px solid #e8e2fc; border-radius: 8px; padding: 6px 11px;
        background: #fdfdff;
        font-size: 12px; font-weight: 600; color: #4b5563;
    }
    .radio-opt input[type=radio] { display: none; }
    .radio-dot {
        width: 13px; height: 13px; border-radius: 50%;
        border: 1.5px solid #c4b5fd; background: #fff;
        flex-shrink: 0;
    }
    .radio-opt:has(input:checked) { border-color: #7c3aed; background: #f5f3ff; color: #5b21b6; }
    .radio-opt:has(input:checked) .radio-dot { border-color: #7c3aed; background: #7c3aed; box-shadow: inset 0 0 0 3px #fff; }
    .accurate-radio-group .radio-opt:has(input:checked) { border-color: #2563eb; background: #eff6ff; color: #1e40af; }
    .accurate-radio-group .radio-opt:has(input:checked) .radio-dot { border-color: #2563eb; background: #2563eb; box-shadow: inset 0 0 0 3px #fff; }

    .field-hint { font-size: 10px; color: #9ca3af; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
    .badge-opt {
        background: #f3f4f6; color: #9ca3af;
        font-size: 9px; font-weight: 700; padding: 2px 6px;
        border-radius: 4px; text-transform: uppercase; letter-spacing: .3px;
    }

    .epicor-tag {
        display: inline-flex; align-items: center; gap: 3px;
        background: #1e1b4b; color: #c4b5fd;
        font-size: 8.5px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase;
        padding: 2px 7px; border-radius: 5px; margin-left: 5px; vertical-align: middle;
    }
    .accurate-tag {
        display: inline-flex; align-items: center; gap: 3px;
        background: #1e3a8a; color: #bfdbfe;
        font-size: 8.5px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase;
        padding: 2px 7px; border-radius: 5px; margin-left: 5px; vertical-align: middle;
    }

    .ic-s1 { background: #ede9fe; color: #7c3aed; }
    .ic-s2 { background: #dbeafe; color: #1d4ed8; }
    .ic-s3 { background: #dcfce7; color: #15803d; }
    .ic-s4 { background: #fef9c3; color: #a16207; }
    .ic-s5 { background: #ccfbf1; color: #0f766e; }
    .ic-s6-epicor   { background: #1e1b4b; color: #a5b4fc; }
    .ic-s6-accurate { background: #1e3a8a; color: #93c5fd; }
    .ic-s7 { background: #fce7f3; color: #be185d; }
    .ic-s8 { background: #fce7f3; color: #be185d; }

    /* ── DOKUMEN ── */
    .doc-list{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
        gap:12px;
    }
    .doc-item{
        border:1px solid #e5e7eb;
        border-radius:12px;
        overflow:hidden;
        background:#fff;
        text-align:center;
        font-size: 10px;
        color: #6b7280;
        padding-bottom: 8px;
    }
    .doc-preview-image{
        width:100%;
        height:180px;
        object-fit:cover;
        display:block;
    }
    .doc-link{
        display:flex;
        align-items:center;
        gap:12px;
        padding:14px;
        color:#374151;
        text-decoration:none;
    }
    .doc-link i{ font-size:28px; }
    .doc-link.pdf i{ color:#dc2626; }

    /* ── SIGN BOXES ── */
    .sign-box {
        border: 1.5px solid #e8e2fc; border-radius: 12px; overflow: hidden;
    }
    .sign-box-head {
        background: #faf5ff; padding: 7px 12px; border-bottom: 1px solid #ede9fe;
        font-size: 9px; font-weight: 700; color: #6d28d9; text-transform: uppercase;
        letter-spacing: .5px; text-align: center;
    }
    .sign-box-body {
        padding: 14px; display: flex; align-items: center; justify-content: center;
        min-height: 140px; background: #fdfcff;
    }
    .sign-box-body img { max-height: 140px; max-width: 100%; }
    .sign-box-empty { color: #ddd6fe; font-size: 11px; font-weight: 600; }
    .sign-box-name {
        border-top: 1px solid #ede9fe; padding: 7px; text-align: center;
        font-size: 10px; font-weight: 700; color: #374151; background: #fafafa;
    }

    .sign-col-card {
        border: 1px solid #e8e2fc;
        border-radius: 10px;
        overflow: hidden;
        background: #fdfcff;
    }
    .sign-col-head {
        background: #f5f3ff;
        border-bottom: 1px solid #ede9fe;
        padding: 5px 8px;
        font-size: 9px; font-weight: 700;
        color: #6d28d9; text-transform: uppercase;
        letter-spacing: .4px; text-align: center;
    }
    .sign-col-body {
        height: 58px;
        display: flex; align-items: center; justify-content: center;
    }
    .sign-col-placeholder { color: #ddd6fe; font-size: 20px; }
    .sign-col-name {
        border-top: 1px solid #ede9fe;
        padding: 5px 8px;
        font-size: 9px; font-weight: 700;
        color: #374151; text-align: center;
        min-height: 26px; line-height: 1.3;
    }
    .sign-approver-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    /* ── VALIDATOR NOTICE ── */
    .validator-notice {
        display: flex; align-items: flex-start; gap: 8px;
        background: #fffbeb; border: 1px solid #fde68a;
        border-radius: 9px; padding: 9px 11px;
        margin-bottom: 12px; font-size: 11px; color: #92400e;
    }
    .validator-notice i { color: #d97706; margin-top: 1px; flex-shrink: 0; }

    /* ── APPROVAL ACTION PANEL ── */
    .approval-panel {
        border: 1.5px solid #fde68a; background: #fffbeb;
        border-radius: 14px; margin: 0 14px 12px; padding: 14px;
    }
    .approval-panel-title {
        font-size: 12px; font-weight: 700; color: #92400e;
        display: flex; align-items: center; gap: 6px; margin-bottom: 12px;
    }

    .btn-label-action {
        background: transparent;
        border: none;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 500;
        border-radius: 20px;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.6;
    }
    .btn-clear-alamat {
        color: #ef4444;
        border-color: #fca5a5;
        background: #fef2f2;
    }
    .btn-clear-alamat:hover {
        background: #ef4444;
        color: #fff;
        border-color: #ef4444;
    }

    .approval-btn-row { display: flex; gap: 10px; margin-top: 12px; }
    .btn-approve, .btn-reject {
        flex: 1; padding: 12px; border: none; border-radius: 12px;
        font-size: 13px; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: opacity .18s, transform .12s;
    }
    .btn-approve { background: #16a34a; color: #fff; }
    .btn-approve:hover { opacity: .9; }
    .btn-reject  { background: #fff; color: #dc2626; border: 1.5px solid #fca5a5; }
    .btn-reject:hover  { background: #fef2f2; }
    .btn-reject.active { background: #fee2e2; border-color: #f87171; }
    .reject-reason-wrap { display: none; margin-top: 10px; }
    .vnd-textarea.approval-textarea {
        background: #fff; border-color: #fca5a5;
        resize: vertical; min-height: 80px;
    }
    .vnd-textarea.approval-textarea:focus {
        border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.08);
    }
</style>
@endsection

@section('content_top')
<div class="card mb-3" style="position:relative; padding-bottom:0;">
    <a href="{{ route('approval.ticket.vendor.index', $menu_id) }}"
        style="position:absolute; top:14px; left:14px; width:34px; height:34px; border-radius:10px;
               background:#f5f3ff; border:1px solid #ddd6fe; display:flex; align-items:center;
               justify-content:center; color:#7c3aed; font-size:13px; text-decoration:none; z-index:10;">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="vnd-header">
        <div class="vnd-icon"><i class="fa-solid fa-building-user"></i></div>
        <div class="vnd-title">Approval Pendaftaran Vendor</div>
        <div class="vnd-sub">
            {{ $vendor->nomor_pengajuan_fis }}<br>
            Diajukan oleh {{ $vendor->creator_name }}
            @if($canAct)
                &mdash; <strong style="color:#92400e;">Menunggu tindakan Anda</strong>
            @endif
        </div>
    </div>

    {{-- STEP BAR --}}
    <div class="step-bar" id="step-bar">
        <div class="step-item">
            <div class="step-dot done" id="st0"><i class="fa-solid fa-cog" style="font-size:9px;"></i></div>
            <div class="step-label">Sistem</div>
        </div>
        <div class="step-line done" id="sl0"></div>
        <div class="step-item"><div class="step-dot done" id="st1">1</div><div class="step-label">Usaha</div></div>
        <div class="step-line" id="sl1"></div>
        <div class="step-item"><div class="step-dot" id="st2">2</div><div class="step-label">Pemilik</div></div>
        <div class="step-line" id="sl2"></div>
        <div class="step-item"><div class="step-dot" id="st3">3</div><div class="step-label">Bank</div></div>
        <div class="step-line" id="sl3"></div>
        <div class="step-item"><div class="step-dot" id="st4">4</div><div class="step-label">Bayar</div></div>
        <div class="step-line" id="sl4"></div>
        <div class="step-item"><div class="step-dot" id="st5">5</div><div class="step-label">Kontak</div></div>
        <div class="step-line" id="sl5"></div>
        <div class="step-item"><div class="step-dot" id="st6">6</div><div class="step-label" id="st6-label">ERP</div></div>
        <div class="step-line" id="sl6"></div>
        <div class="step-item"><div class="step-dot" id="st7"><i class="fa-solid fa-paperclip" style="font-size:9px;"></i></div><div class="step-label">Dok.</div></div>
    </div>
</div>

<div class="vnd-wrap">

    {{-- ── SISTEM ERP ── --}}
    <div class="sistem-wrap">
        <div class="sistem-head">
            <div class="sh-icon"><i class="fa-solid fa-cog"></i></div>
            <div class="sh-title">Sistem ERP</div>
        </div>
        <div class="sistem-body">
            <div class="sistem-grid">
                <label class="sistem-opt epicor-opt">
                    <input type="radio" {{ $vendor->system_type == 1 ? 'checked' : '' }} disabled>
                    <span class="so-check"><i class="fa-solid fa-check"></i></span>
                    <div class="so-icon"><i class="fa-solid fa-microchip"></i></div>
                    <div class="so-name">Epicor</div>
                    <div class="so-desc">ERP Sistem PT. SPS</div>
                </label>
                <label class="sistem-opt accurate-opt">
                    <input type="radio" {{ $vendor->system_type == 2 ? 'checked' : '' }} disabled>
                    <span class="so-check"><i class="fa-solid fa-check"></i></span>
                    <div class="so-icon"><i class="fa-solid fa-chart-pie"></i></div>
                    <div class="so-name">Accurate</div>
                    <div class="so-desc">Sistem CV. SP</div>
                </label>
            </div>
        </div>
    </div>

    {{-- ── KATEGORI VENDOR ── --}}
    <div class="sistem-wrap">
        <div class="sistem-head">
            <div class="sh-icon" style="background:#15803d;"><i class="fa-solid fa-tags"></i></div>
            <div class="sh-title">Kategori Vendor</div>
        </div>
        <div class="sistem-body">
            <div class="sistem-grid">
                <label class="sistem-opt bb-opt">
                    <input type="radio" {{ $vendor->vendor_category == 'V01' ? 'checked' : '' }} disabled>
                    <span class="so-check"><i class="fa-solid fa-check"></i></span>
                    <div class="so-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <div class="so-name">Bahan Baku</div>
                    <div class="so-desc">Material Bahan Baku Produksi</div>
                </label>
                <label class="sistem-opt nbb-opt">
                    <input type="radio" {{ $vendor->vendor_category == 'V02' ? 'checked' : '' }} disabled>
                    <span class="so-check"><i class="fa-solid fa-check"></i></span>
                    <div class="so-icon"><i class="fa-solid fa-box-archive"></i></div>
                    <div class="so-name">Non Bahan Baku</div>
                    <div class="so-desc">Asset, Jasa, ATK &amp; kebutuhan lain</div>
                </label>
            </div>
        </div>
    </div>

    {{-- ── SEC 1: DATA USAHA ── --}}
    <div class="vnd-section" id="sec1">
        <div class="vnd-section-head" data-sec="sec1-body">
            <div class="sec-icon ic-s1"><i class="fa-solid fa-store"></i></div>
            <div class="sec-title">Data Usaha</div>
            <span class="sec-counter">6 field</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec1-body">
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-signature"></i> Nama Usaha</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->nama_usaha }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat Usaha</label>
                <textarea class="vnd-textarea" readonly>{{ $vendor->data?->alamat_usaha }}</textarea>
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-id-card"></i> NPWP</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->npwp }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-id-card"></i> NPWP16</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->npwp16 }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat NPWP <span class="badge-opt">opsional</span></label>
                <textarea class="vnd-textarea" readonly>{{ $vendor->data?->alamat_npwp }}</textarea>
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-store"></i> Kategori Vendor</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->kategori ?? '-' }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-link"></i> Supplier Afiliasi</label>
                @if($vendor->data?->supplier_afiliasi)
                    <span class="badge bg-primary" style="font-size:11px;">YA</span>
                @else
                    <span class="badge bg-secondary" style="font-size:11px;">TIDAK</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── SEC 2: DATA PEMILIK ── --}}
    <div class="vnd-section" id="sec2">
        <div class="vnd-section-head collapsed" data-sec="sec2-body">
            <div class="sec-icon ic-s2"><i class="fa-solid fa-user-tie"></i></div>
            <div class="sec-title">Data Pemilik</div>
            <span class="sec-counter">3 field</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec2-body" style="display:none;">
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-user"></i> Nama Pemilik</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->nama_pemilik }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-id-card"></i> NIK Pemilik</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->nik }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat Pemilik</label>
                <textarea class="vnd-textarea" readonly>{{ $vendor->data?->alamat_pemilik }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── SEC 3: DATA BANK ── --}}
    <div class="vnd-section" id="sec3">
        <div class="vnd-section-head collapsed" data-sec="sec3-body">
            <div class="sec-icon ic-s3"><i class="fa-solid fa-building-columns"></i></div>
            <div class="sec-title">Data Bank</div>
            <span class="sec-counter">4 field</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec3-body" style="display:none;">
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-building-columns"></i> Nama Bank</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->nama_bank }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-user-check"></i> Nama Penerima Rekening</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->nama_bank_penerima }}">
            </div>
            <div class="vnd-row">
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-hashtag"></i> No. Rekening</label>
                    <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->no_rekening }}">
                </div>
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-code-branch"></i> Cabang <span class="badge-opt">opsional</span></label>
                    <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->cabang_bank }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ── SEC 4: PEMBAYARAN ── --}}
    <div class="vnd-section" id="sec4">
        <div class="vnd-section-head collapsed" data-sec="sec4-body">
            <div class="sec-icon ic-s4"><i class="fa-solid fa-wallet"></i></div>
            <div class="sec-title">Pembayaran</div>
            <span class="sec-counter">2 field</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec4-body" style="display:none;">
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-wallet"></i> Metode Pembayaran</label>
                <div class="radio-group">
                    @foreach(['Transfer Bank','Cek/Giro','Tunai','Virtual Account'] as $m)
                    <label class="radio-opt">
                        <input type="radio" {{ $vendor->data?->metode_pembayaran == $m ? 'checked' : '' }} disabled>
                        <span class="radio-dot"></span><span>{{ $m }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <hr style="border:none;border-top:1px solid #f3f0ff;margin:12px 0;">
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-calendar-days"></i> Syarat Pembayaran (Termin)</label>
                <div class="radio-group">
                    @foreach(['Cash','NET 14','NET 30','NET 45','NET 60'] as $s)
                    <label class="radio-opt">
                        <input type="radio" {{ $vendor->data?->syarat_pembayaran == $s ? 'checked' : '' }} disabled>
                        <span class="radio-dot"></span><span>{{ $s }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── SEC 5: CONTACT PERSON ── --}}
    <div class="vnd-section" id="sec5">
        <div class="vnd-section-head collapsed" data-sec="sec5-body">
            <div class="sec-icon ic-s5"><i class="fa-solid fa-address-card"></i></div>
            <div class="sec-title">Contact Person</div>
            <span class="sec-counter">4 field</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec5-body" style="display:none;">
            <div class="vnd-row">
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-user"></i> Nama (KTP)</label>
                    <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->nama_ktp }}">
                </div>
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-briefcase"></i> Jabatan <span class="badge-opt">opsional</span></label>
                    <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->jabatan }}">
                </div>
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-phone"></i> No. Telp / HP</label>
                <input type="text" class="vnd-input" readonly value="{{ $vendor->data?->no_telp }}">
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat (KTP) <span class="badge-opt">opsional</span></label>
                <textarea class="vnd-textarea" readonly>{{ $vendor->data?->alamat_ktp }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── SEC 6: KONFIGURASI EPICOR / ACCURATE ── --}}
    @if($vendor->system_type == 1)
    <div class="vnd-section" id="sec-epicor">
        <div class="vnd-section-head {{ $myStep === 'validator' ? '' : 'collapsed' }}" data-sec="sec-epicor-body">
            <div class="sec-icon ic-s6-epicor"><i class="fa-solid fa-microchip"></i></div>
            <div class="sec-title">
                Konfigurasi Epicor <span class="epicor-tag"><i class="fa-solid fa-cube"></i> ERP</span>
            </div>
            <span class="sec-counter" style="{{ $myStep === 'validator' ? 'background:#fef3c7;color:#b45309;border-color:#fde68a;' : '' }}">
                {{ $myStep === 'validator' ? 'Isi Sekarang' : '4 field' }}
            </span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec-epicor-body" {{ $myStep === 'validator' ? '' : 'style="display:none;"' }}>
            @if($myStep === 'validator')
            <div class="validator-notice">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Lengkapi konfigurasi Epicor berikut sebelum validasi disimpan.</span>
            </div>
            @endif
            <div class="vnd-row">
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-file-invoice-dollar"></i> Tax Liability</label>
                    <input type="text" name="epicor_tax_liability" class="vnd-input"
                        value="{{ $vendor->data?->tax_liability }}"
                        placeholder="DOMESTIC"
                        {{ $myStep === 'validator' ? '' : 'readonly' }}>
                </div>
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-layer-group"></i> Supplier Group</label>
                    <input type="text" name="epicor_supplier_group" class="vnd-input"
                        value="{{ $vendor->data?->supplier_group }}"
                        placeholder="LOCAL"
                        {{ $myStep === 'validator' ? '' : 'readonly' }}>
                </div>
            </div>
            <div class="vnd-row">
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-sliders"></i> GL Control Type</label>
                    <input type="text" name="epicor_gl_control_type" class="vnd-input"
                        value="{{ $vendor->data?->gl_control_type }}"
                        placeholder="AP"
                        {{ $myStep === 'validator' ? '' : 'readonly' }}>
                </div>
                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-code"></i> GL Control Code</label>
                    <input type="text" name="epicor_gl_control_code" class="vnd-input"
                        value="{{ $vendor->data?->gl_control_code }}"
                        placeholder="AP-001"
                        {{ $myStep === 'validator' ? '' : 'readonly' }}>
                </div>
            </div>
        </div>
    </div>
    @elseif($vendor->system_type == 2)
    <div class="vnd-section" id="sec-accurate">
        <div class="vnd-section-head {{ $myStep === 'validator' ? '' : 'collapsed' }}" data-sec="sec-accurate-body">
            <div class="sec-icon ic-s6-accurate"><i class="fa-solid fa-chart-pie"></i></div>
            <div class="sec-title">
                Konfigurasi Accurate <span class="accurate-tag"><i class="fa-solid fa-chart-bar"></i> Akuntansi</span>
            </div>
            <span class="sec-counter" style="{{ $myStep === 'validator' ? 'background:#fef3c7;color:#b45309;border-color:#fde68a;' : '' }}">
                {{ $myStep === 'validator' ? 'Isi Sekarang' : '3 field' }}
            </span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec-accurate-body" {{ $myStep === 'validator' ? '' : 'style="display:none;"' }}>
            @if($myStep === 'validator')
            <div class="validator-notice">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Lengkapi konfigurasi Accurate berikut sebelum validasi disimpan.</span>
            </div>
            @endif
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-sitemap"></i> Dipakai Cabang <span class="badge-opt">opsional</span></label>
                <input type="text" name="accurate_dipakai_cabang" class="vnd-input"
                    value="{{ $vendor->data?->dipakai_cabang }}"
                    placeholder="Cabang"
                    {{ $myStep === 'validator' ? '' : 'readonly' }}>
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-tag"></i> Kategori Vendor</label>
                <input type="text" name="accurate_kategori" class="vnd-input"
                    value="{{ $vendor->data?->kategori }}"
                    placeholder="Kategori"
                    {{ $myStep === 'validator' ? '' : 'readonly' }}>
            </div>
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-truck-ramp-box"></i> Sourcing</label>
                <input type="text" name="accurate_sourcing" class="vnd-input"
                    value="{{ $vendor->data?->sourcing }}"
                    placeholder="Sourcing"
                    {{ $myStep === 'validator' ? '' : 'readonly' }}>
            </div>
        </div>
    </div>
    @endif

    {{-- ── SEC 7: DOKUMEN PENDUKUNG ── --}}
    <div class="vnd-section" id="sec-dokumen">
        <div class="vnd-section-head collapsed" data-sec="sec-dokumen-body">
            <div class="sec-icon ic-s7"><i class="fa-solid fa-paperclip"></i></div>
            <div class="sec-title">Dokumen Pendukung</div>
            <span class="sec-counter">{{ $vendor->attachments->count() }} file</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec-dokumen-body" style="display:none;">
            @if($vendor->attachments->count())
            <div class="doc-list">
                @foreach($vendor->attachments as $file)
                    @php
                        $url = asset('storage/'.$file->file_path);
                        $ext = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                        $isPdf = $ext === 'pdf';
                    @endphp
                    <div class="doc-item">
                        @if($isImage)
                            <a href="{{ $url }}" target="_blank">
                                <img src="{{ $url }}" class="doc-preview-image" alt="{{ $file->original_filename }}">
                            </a>
                        @elseif($isPdf)
                            <a href="{{ $url }}" target="_blank" class="doc-link pdf">
                                <i class="fa-solid fa-file-pdf"></i>
                                <div><div>{{ $file->original_filename }}</div></div>
                            </a>
                        @else
                            <a href="{{ $url }}" target="_blank" class="doc-link">
                                <i class="fa-solid fa-file-lines"></i>
                                <div><div>{{ $file->original_filename }}</div></div>
                            </a>
                        @endif
                        <div style="padding:6px 8px;">
                            {{ $file->original_filename }}<br>
                            {{ number_format($file->file_size / 1024, 0) }} KB
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <span class="text-muted" style="font-size:12px;">Tidak ada dokumen pendukung</span>
            @endif
        </div>
    </div>

    {{-- ── SEC 8: OTORISASI & PENGESAHAN ── --}}
    <div class="vnd-section" id="sec-sign">
        <div class="vnd-section-head" data-sec="sec-sign-body">
            <div class="sec-icon ic-s8"><i class="fa-solid fa-pen-nib"></i></div>
            <div class="sec-title">Otorisasi &amp; Pengesahan</div>
            <span class="sec-counter">TTD</span>
            <i class="fa-solid fa-chevron-down sec-chevron"></i>
        </div>
        <div class="vnd-section-body" id="sec-sign-body">

            {{-- Tanda tangan pengaju --}}
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-pen-nib"></i> Tanda Tangan Pengaju</label>
                <div class="sign-box">
                    <div class="sign-box-head">Diajukan Oleh</div>
                    <div class="sign-box-body">
                        @if($vendor->creator_sign)
                            <img src="{{ asset('storage/' . $vendor->creator_sign) }}" alt="Tanda Tangan Pengaju">
                        @else
                            <span class="sign-box-empty">Belum ada tanda tangan</span>
                        @endif
                    </div>
                    <div class="sign-box-name">{{ $vendor->creator_name }}</div>
                </div>
            </div>

            {{-- Grid otorisasi --}}
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-stamp"></i> Kolom Otorisasi</label>
                <div class="sign-approver-grid">

                    <div class="sign-col-card">
                        <div class="sign-col-head">Diajukan Oleh:</div>
                        <div class="sign-col-body">
                            @if($vendor->creator_sign)
                                <img src="{{ asset('storage/' . $vendor->creator_sign) }}" style="max-height:54px; max-width:100%;">
                            @else
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            @endif
                        </div>
                        <div class="sign-col-name">{{ $vendor->creator_name ?? '-' }}</div>
                    </div>

                    <div class="sign-col-card">
                        <div class="sign-col-head">Diketahui Oleh:</div>
                        <div class="sign-col-body">
                            @if($vendor->vd_approve1_sign)
                                <img src="{{ asset('storage/' . $vendor->vd_approve1_sign) }}" style="max-height:54px; max-width:100%;">
                            @else
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            @endif
                        </div>
                        <div class="sign-col-name">
                            {{ $vendor->vd_approve1_name ?? '-' }}
                            @if($vendor->vd_approve1_at)
                                <div style="font-size:8px; color:#9ca3af; font-weight:500;">{{ \Carbon\Carbon::parse($vendor->vd_approve1_at)->format('d/m/Y H:i') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="sign-col-card">
                        <div class="sign-col-head">Disetujui Oleh:</div>
                        <div class="sign-col-body">
                            @if($vendor->vd_approve2_sign)
                                <img src="{{ asset('storage/' . $vendor->vd_approve2_sign) }}" style="max-height:54px; max-width:100%;">
                            @else
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            @endif
                        </div>
                        <div class="sign-col-name">
                            {{ $vendor->vd_approve2_name ?? '-' }}
                            @if($vendor->vd_approve2_at)
                                <div style="font-size:8px; color:#9ca3af; font-weight:500;">{{ \Carbon\Carbon::parse($vendor->vd_approve2_at)->format('d/m/Y H:i') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="sign-col-card">
                        <div class="sign-col-head">Diperiksa Oleh:</div>
                        <div class="sign-col-body">
                            @if($vendor->vd_checker_sign)
                                <img src="{{ asset('storage/' . $vendor->vd_checker_sign) }}" style="max-height:54px; max-width:100%;">
                            @else
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            @endif
                        </div>
                        <div class="sign-col-name">
                            {{ $vendor->vd_checker_name ?? '-' }}
                            @if($vendor->vd_checker_at)
                                <div style="font-size:8px; color:#9ca3af; font-weight:500;">{{ \Carbon\Carbon::parse($vendor->vd_checker_at)->format('d/m/Y H:i') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="sign-col-card" style="grid-column: span 2;">
                        <div class="sign-col-head">Divalidasi Oleh:</div>
                        <div class="sign-col-body">
                            @if($vendor->vd_validator_sign)
                                <img src="{{ asset('storage/' . $vendor->vd_validator_sign) }}" style="max-height:54px; max-width:100%;">
                            @else
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            @endif
                        </div>
                        <div class="sign-col-name">
                            {{ $vendor->vd_validator_name ?? 'Finance & Accounting' }}
                            @if($vendor->vd_validator_at)
                                <div style="font-size:8px; color:#9ca3af; font-weight:500;">{{ \Carbon\Carbon::parse($vendor->vd_validator_at)->format('d/m/Y H:i') }}</div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ── PANEL TINDAKAN APPROVAL (hanya tampil kalau canAct) ── --}}
    @if($canAct)
    <div class="approval-panel">
        <div class="approval-panel-title">
            <i class="fa-solid fa-user-check"></i>
            Tindakan Anda sebagai:
            <strong>
                {{ ['atasan1'=>'Yang Mengetahui','atasan2'=>'Yang Menyetujui','checker'=>'Pemeriksa','validator'=>'Validator'][$myStep] ?? $myStep }}
            </strong>
        </div>

        <div class="vnd-group">
            <label class="vnd-label"><i class="fa-solid fa-pen-nib"></i> Tanda Tangan Anda <span class="req">*</span></label>
            <div style="border:1.5px solid #fde68a; border-radius:12px; overflow:hidden; background:#fff;">
                <canvas id="signature-pad" style="width:100%; touch-action:none; height:160px; display:block;"></canvas>
            </div>
            <div style="display:flex; justify-content:flex-end; margin-top:6px;">
                <button type="button" id="clear-signature" class="btn-label-action btn-clear-alamat" style="font-size:10px;">
                    <i class="fa-solid fa-eraser"></i> Hapus TTD
                </button>
            </div>
        </div>

        <div class="reject-reason-wrap" id="reject-reason-wrap">
            <div class="vnd-group">
                <label class="vnd-label"><i class="fa-solid fa-comment-dots"></i> Alasan Penolakan <span class="req">*</span></label>
                <textarea class="vnd-textarea approval-textarea" id="reject-comment" placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
        </div>

        <div class="approval-btn-row">
            <button type="button" class="btn-reject" id="btn-reject">
                <i class="fa-solid fa-xmark"></i> Tolak
            </button>
            <button type="button" class="btn-approve" id="btn-approve">
                <i class="fa-solid fa-check"></i> Setujui
            </button>
        </div>
    </div>
    @endif

</div>
@endsection

@section('content')
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
@if($canAct)
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endif
<script>
    $(document).ready(function () {

        /* ── collapse/expand section heads ── */
        $(document).on('click', '.vnd-section-head', function () {
            const targetId = $(this).data('sec');
            const $body = $('#' + targetId);
            if ($body.is(':visible')) {
                $body.slideUp(200);
                $(this).addClass('collapsed');
            } else {
                $body.slideDown(200);
                $(this).removeClass('collapsed');
            }
            updateSteps();
        });

        function updateSteps() {
            $('#st0').addClass('done');
            $('#sl0').addClass('done');

            ['sec1-body','sec2-body','sec3-body','sec4-body','sec5-body']
                .forEach(function (id, i) {
                    const n = i + 1;
                    if ($('#' + id).is(':visible')) {
                        $('#st' + n).addClass('done');
                        if (n > 1) $('#sl' + (n - 1)).addClass('done');
                    }
                });

            const epicorOpen   = $('#sec-epicor-body').is(':visible');
            const accurateOpen = $('#sec-accurate-body').is(':visible');
            if (epicorOpen || accurateOpen) {
                $('#st6').addClass('done');
                $('#sl5').addClass('done');
                @if($vendor->system_type == 1)
                    $('#st6-label').text('Epicor');
                @else
                    $('#st6-label').text('Accurate');
                @endif
            }

            if ($('#sec-dokumen-body').is(':visible')) {
                $('#st7').addClass('done');
                $('#sl6').addClass('done');
            }
        }

        updateSteps();

        @if($canAct)
        /* ── SIGNATURE PAD ── */
        let wantsReject = false;

        const signCanvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(signCanvas);

        function resizeSignCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            signCanvas.width  = signCanvas.offsetWidth  * ratio;
            signCanvas.height = signCanvas.offsetHeight * ratio;
            signCanvas.getContext('2d').scale(ratio, ratio);
            signaturePad.clear();
        }
        window.addEventListener('resize', resizeSignCanvas);
        resizeSignCanvas();

        $('#clear-signature').on('click', function () { signaturePad.clear(); });

        /* ── REJECT TOGGLE ── */
        $('#btn-reject').on('click', function () {
            if (!wantsReject) {
                wantsReject = true;
                $(this).addClass('active').html('<i class="fa-solid fa-xmark"></i> Konfirmasi Tolak');
                $('#reject-reason-wrap').slideDown(150);
            } else {
                submitAction('reject');
            }
        });

        /* ── SUBMIT FUNCTION ── */
        function submitAction(action) {
            if (action === 'approve' && signaturePad.isEmpty()) {
                Swal.fire('Tanda Tangan Kosong', 'Harap tanda tangani terlebih dahulu sebelum menyetujui.', 'warning');
                return;
            }
            if (action === 'reject' && !$('#reject-comment').val().trim()) {
                Swal.fire('Alasan Wajib Diisi', 'Harap isi alasan penolakan.', 'warning');
                return;
            }

            @if($myStep === 'validator' && $vendor->system_type == 1)
            if (action === 'approve') {
                const taxLiability = $('[name="epicor_tax_liability"]').val().trim();
                const supplierGroup = $('[name="epicor_supplier_group"]').val().trim();
                const glType = $('[name="epicor_gl_control_type"]').val().trim();
                const glCode = $('[name="epicor_gl_control_code"]').val().trim();
                if (!taxLiability || !supplierGroup || !glType || !glCode) {
                    Swal.fire('Konfigurasi Epicor Belum Lengkap', 'Lengkapi semua field Tax Liability, Supplier Group, GL Control Type, dan GL Control Code sebelum menyetujui.', 'warning');
                    $('#sec-epicor-body').slideDown(200);
                    $('#sec-epicor .vnd-section-head').removeClass('collapsed');
                    return;
                }
            }
            @endif

            @if($myStep === 'validator' && $vendor->system_type == 2)
            if (action === 'approve') {
                const kategori = $('[name="accurate_kategori"]').val().trim();
                if (!kategori) {
                    Swal.fire('Konfigurasi Accurate Belum Lengkap', 'Lengkapi field Kategori Vendor Accurate sebelum menyetujui.', 'warning');
                    $('#sec-accurate-body').slideDown(200);
                    $('#sec-accurate .vnd-section-head').removeClass('collapsed');
                    return;
                }
            }
            @endif

            Swal.fire({
                title: action === 'approve' ? 'Setujui ticket ini?' : 'Tolak ticket ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: action === 'approve' ? '#16a34a' : '#dc2626',
            }).then(function (result) {
                if (!result.isConfirmed) return;

                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: '{{ route("approval.ticket.vendor.save", $vendor->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        menu_id: '{{ $menu_id }}',
                        signature: (action === 'approve' && !signaturePad.isEmpty()) ? signaturePad.toDataURL() : null,
                        comment_text: action === 'reject' ? $('#reject-comment').val() : null,
                        epicor_tax_liability:    $('[name="epicor_tax_liability"]').val(),
                        epicor_supplier_group:   $('[name="epicor_supplier_group"]').val(),
                        epicor_gl_control_type:  $('[name="epicor_gl_control_type"]').val(),
                        epicor_gl_control_code:  $('[name="epicor_gl_control_code"]').val(),
                        accurate_dipakai_cabang: $('[name="accurate_dipakai_cabang"]').val(),
                        accurate_kategori:       $('[name="accurate_kategori"]').val(),
                        accurate_sourcing:       $('[name="accurate_sourcing"]').val(),
                    },
                    success: function (res) {
                        Swal.close();
                        if (res.code === 200) {
                            Swal.fire({
                                icon: 'success', title: 'Berhasil',
                                text: res.message, timer: 2000, showConfirmButton: false
                            }).then(() => window.location.href = res.redirect ?? '{{ route("approval.ticket.vendor.index", $menu_id) }}');
                        } else {
                            Swal.fire('Gagal', window.getFriendlyErrorMessage(res.message, 'Gagal memproses approval.'), 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'warning' : 'error',
                            title: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'Validasi Gagal' : 'Gagal',
                            html: window.getFriendlyErrorMessage(xhr, 'Tidak dapat terhubung ke server.')
                        });
                    }
                });
            });
        }

        $('#btn-approve').on('click', function () { submitAction('approve'); });
        @endif

    });
</script>
@endsection