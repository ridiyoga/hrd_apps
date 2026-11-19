@extends('users.layouts.main')
@section('title') APPS | Pendaftaran Vendor Baru @endsection

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

    /* ── SISTEM / KATEGORI SELECTOR ── */
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
    .sistem-head .sh-required { font-size: 10px; color: #e24b4a; font-weight: 600; }
    .sistem-body { padding: 14px; }
    .sistem-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

    .sistem-opt {
        border: 1.5px solid #e8e2fc; border-radius: 11px; padding: 14px 12px;
        cursor: pointer; background: #fdfdff; transition: all .18s;
        display: flex; flex-direction: column; align-items: center;
        gap: 8px; text-align: center; position: relative;
    }
    .sistem-opt input[type=radio] { display: none; }
    .sistem-opt .so-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; transition: all .18s;
    }
    .sistem-opt .so-name  { font-size: 12px; font-weight: 700; color: #374151; line-height: 1.2; }
    .sistem-opt .so-desc  { font-size: 10px; color: #9ca3af; line-height: 1.3; }
    .sistem-opt .so-check {
        position: absolute; top: 8px; right: 8px;
        width: 16px; height: 16px; border-radius: 50%;
        border: 1.5px solid #ddd6fe; background: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 8px; color: transparent; transition: all .15s;
    }

    /* Epicor */
    .sistem-opt.epicor-opt .so-icon { background: #f5f3ff; color: #a78bfa; }
    .sistem-opt.epicor-opt:has(input:checked) { border-color: #7c3aed; background: #f5f3ff; }
    .sistem-opt.epicor-opt:has(input:checked) .so-icon { background: #ede9fe; color: #7c3aed; }
    .sistem-opt.epicor-opt:has(input:checked) .so-name  { color: #5b21b6; }
    .sistem-opt.epicor-opt:has(input:checked) .so-check { background: #7c3aed; border-color: #7c3aed; color: #fff; }

    /* Accurate */
    .sistem-opt.accurate-opt .so-icon { background: #eff6ff; color: #93c5fd; }
    .sistem-opt.accurate-opt:has(input:checked) { border-color: #2563eb; background: #eff6ff; }
    .sistem-opt.accurate-opt:has(input:checked) .so-icon { background: #dbeafe; color: #1d4ed8; }
    .sistem-opt.accurate-opt:has(input:checked) .so-name  { color: #1e40af; }
    .sistem-opt.accurate-opt:has(input:checked) .so-check { background: #2563eb; border-color: #2563eb; color: #fff; }

    /* Bahan Baku */
    .sistem-opt.bb-opt .so-icon { background: #dcfce7; color: #16a34a; }
    .sistem-opt.bb-opt:has(input:checked) { border-color: #16a34a; background: #f0fdf4; }
    .sistem-opt.bb-opt:has(input:checked) .so-icon { background: #bbf7d0; color: #15803d; }
    .sistem-opt.bb-opt:has(input:checked) .so-name  { color: #15803d; }
    .sistem-opt.bb-opt:has(input:checked) .so-check { background: #16a34a; border-color: #16a34a; color: #fff; }

    /* Non Bahan Baku */
    .sistem-opt.nbb-opt .so-icon { background: #fef9c3; color: #d97706; }
    .sistem-opt.nbb-opt:has(input:checked) { border-color: #d97706; background: #fffbeb; }
    .sistem-opt.nbb-opt:has(input:checked) .so-icon { background: #fde68a; color: #b45309; }
    .sistem-opt.nbb-opt:has(input:checked) .so-name  { color: #b45309; }
    .sistem-opt.nbb-opt:has(input:checked) .so-check { background: #d97706; border-color: #d97706; color: #fff; }

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
        background: #fdfdff; outline: none;
        transition: border-color .18s, box-shadow .18s;
        appearance: none; -webkit-appearance: none; box-sizing: border-box;
    }
    .vnd-input:focus, .vnd-select:focus, .vnd-textarea:focus {
        border-color: #a855f7; box-shadow: 0 0 0 3px rgba(168,85,247,.08); background: #fff;
    }
    .vnd-input::placeholder, .vnd-textarea::placeholder { color: #c4b5fd; font-size: 12px; }
    .vnd-textarea { resize: vertical; min-height: 68px; line-height: 1.5; }
    .vnd-select-wrap { position: relative; }
    .vnd-select-wrap::after {
        content: '\f107'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
        position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
        color: #a855f7; font-size: 11px; pointer-events: none;
    }
    .vnd-select { padding-right: 34px; cursor: pointer; }

    .vnd-label {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .vnd-label-actions {
        display: flex;
        align-items: center;
        justify-content: end;
        margin-left: auto;
        margin-bottom: 10px;
        gap: 4px;
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

    .btn-copy-alamat {
        color: #6366f1;
        border-color: #a5b4fc;
        background: #eef2ff;
    }
    .btn-copy-alamat:hover {
        background: #6366f1;
        color: #fff;
        border-color: #6366f1;
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

    /* Feedback flash saat berhasil copy */
    .btn-copy-alamat.copied {
        background: #22c55e;
        color: #fff;
        border-color: #22c55e;
    }

    .radio-group { display: flex; flex-wrap: wrap; gap: 7px; }
    .radio-opt {
        display: flex; align-items: center; gap: 5px;
        border: 1px solid #e8e2fc; border-radius: 8px; padding: 6px 11px;
        cursor: pointer; background: #fdfdff; transition: all .15s;
        font-size: 12px; font-weight: 600; color: #4b5563;
    }
    .radio-opt input[type=radio] { display: none; }
    .radio-dot {
        width: 13px; height: 13px; border-radius: 50%;
        border: 1.5px solid #c4b5fd; background: #fff;
        flex-shrink: 0; transition: all .15s;
    }
    .radio-opt:has(input:checked) { border-color: #7c3aed; background: #f5f3ff; color: #5b21b6; }
    .radio-opt:has(input:checked) .radio-dot { border-color: #7c3aed; background: #7c3aed; box-shadow: inset 0 0 0 3px #fff; }
    .accurate-radio-group .radio-opt:has(input:checked) { border-color: #2563eb; background: #eff6ff; color: #1e40af; }
    .accurate-radio-group .radio-opt:has(input:checked) .radio-dot { border-color: #2563eb; background: #2563eb; box-shadow: inset 0 0 0 3px #fff; }

    .toggle-wrap {
        display: flex; align-items: center; justify-content: space-between;
        background: #faf9ff; border: 1px solid #e8e2fc; border-radius: 10px; padding: 10px 13px;
    }
    .toggle-info { flex: 1; }
    .toggle-title { font-size: 12px; font-weight: 700; color: #1f2937; display: block; }
    .toggle-sub   { font-size: 10px; color: #9ca3af; margin-top: 1px; display: block; }
    .toggle-switch {
        width: 40px; height: 22px; border-radius: 50px; background: #e5e7eb;
        position: relative; cursor: pointer; transition: background .18s;
        flex-shrink: 0; margin-left: 10px;
    }
    .toggle-switch::after {
        content: ''; position: absolute; width: 16px; height: 16px;
        border-radius: 50%; background: #fff; top: 3px; left: 3px; transition: transform .18s;
    }
    .toggle-switch.on { background: #7c3aed; }
    .toggle-switch.on::after { transform: translateX(18px); }

    .vnd-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    @media (max-width: 380px) { .vnd-row { grid-template-columns: 1fr; } }

    .vnd-divider { border: none; border-top: 1px solid #f3f0ff; margin: 12px 0; }
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

    .sistem-placeholder {
        margin: 0 14px 12px; border: 1.5px dashed #e8e2fc;
        border-radius: 14px; padding: 20px 16px; text-align: center; display: none;
    }
    .sistem-placeholder i { font-size: 28px; color: #ddd6fe; display: block; margin-bottom: 8px; }
    .sistem-placeholder p { font-size: 12px; color: #c4b5fd; margin: 0; font-weight: 600; }

    .upload-zone {
        border: 1.5px dashed #c4b5fd; border-radius: 12px;
        padding: 18px 14px; text-align: center;
        background: #fdfcff; cursor: pointer;
        transition: border-color .18s, background .18s; position: relative;
    }
    .upload-zone:hover    { border-color: #7c3aed; background: #f5f3ff; }
    .upload-zone.dragover { border-color: #7c3aed; background: #f5f3ff; }
    .upload-zone input[type=file] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer;
        width: 100%; height: 100%;
    }
    .upload-zone .uz-icon {
        width: 40px; height: 40px; border-radius: 12px;
        background: #ede9fe; color: #7c3aed; font-size: 18px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 8px;
    }
    .upload-zone .uz-title { font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 2px; }
    .upload-zone .uz-sub   { font-size: 10px; color: #9ca3af; }

    .file-list { margin-top: 10px; display: flex; flex-direction: column; gap: 7px; }
    .file-item {
        display: flex; align-items: center; gap: 10px;
        background: #f9f7ff; border: 1px solid #e8e2fc;
        border-radius: 10px; padding: 8px 11px;
    }
    .file-item .fi-icon {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0;
        background: #ede9fe; color: #7c3aed;
    }
    .file-item .fi-info { flex: 1; min-width: 0; }
    .file-item .fi-name {
        font-size: 11.5px; font-weight: 700; color: #1f2937;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .fi-meta-row { display: flex; align-items: center; gap: 6px; margin-top: 3px; flex-wrap: wrap; }
    .fi-size { font-size: 10px; color: #9ca3af; }
    .file-item .fi-remove {
        width: 22px; height: 22px; border-radius: 6px; border: none;
        background: #fee2e2; color: #ef4444; font-size: 10px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; flex-shrink: 0; padding: 0;
    }

    .doc-type-select {
        font-size: 10px; padding: 3px 22px 3px 8px;
        border-radius: 6px; border: 1px solid #ddd6fe;
        background: #fff; color: #5b21b6; font-weight: 600;
        appearance: none; -webkit-appearance: none; cursor: pointer; outline: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23a855f7' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 6px center;
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
    @keyframes skPulse {
        0%, 100% { opacity: .5; } 50% { opacity: 1; }
    }
    .skeleton-text {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 4px; width: 56px; height: 10px; vertical-align: middle;
    }

    .validator-notice {
        display: flex; align-items: flex-start; gap: 8px;
        background: #fffbeb; border: 1px solid #fde68a;
        border-radius: 9px; padding: 9px 11px;
        margin-bottom: 12px; font-size: 11px; color: #92400e;
    }
    .validator-notice i { color: #d97706; margin-top: 1px; flex-shrink: 0; }

    .vnd-submit-wrap { margin: 0 14px 20px; }
    .vnd-submit {
        width: 100%; padding: 13px; background: #7c3aed; color: #fff;
        border: none; border-radius: 12px; font-size: 13.5px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        gap: 8px; cursor: pointer; transition: opacity .2s, transform .15s;
    }
    .vnd-submit:hover   { opacity: .92; }
    .vnd-submit:active  { transform: scale(.98); }
    .vnd-submit:disabled { opacity: .6; cursor: not-allowed; }

    .sec-sistem { transition: all .2s; }
</style>
@endsection

@section('content_top')
<div class="card mb-3" style="position:relative; padding-bottom:0;">
    <a href="{{ route('ticket.hub', ['menu_id' => $menu_id]) }}"
        style="position:absolute; top:14px; left:14px; width:34px; height:34px; border-radius:10px;
               background:#f5f3ff; border:1px solid #ddd6fe; display:flex; align-items:center;
               justify-content:center; color:#7c3aed; font-size:13px; text-decoration:none; z-index:10;">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="vnd-header">
        <div class="vnd-icon"><i class="fa-solid fa-building-user"></i></div>
        <div class="vnd-title">Pendaftaran Vendor Baru</div>
        <div class="vnd-sub">Pilih sistem ERP &amp; kategori vendor,<br>lalu lengkapi formulir pendaftaran</div>
    </div>

    {{-- STEP BAR --}}
    <div class="step-bar" id="step-bar">
        <div class="step-item">
            <div class="step-dot done" id="st0">
                <i class="fa-solid fa-cog" style="font-size:9px;"></i>
            </div>
            <div class="step-label">Sistem</div>
        </div>
        <div class="step-line done" id="sl0"></div>
        <div class="step-item">
            <div class="step-dot done" id="st1">1</div>
            <div class="step-label">Usaha</div>
        </div>
        <div class="step-line" id="sl1"></div>
        <div class="step-item">
            <div class="step-dot" id="st2">2</div>
            <div class="step-label">Pemilik</div>
        </div>
        <div class="step-line" id="sl2"></div>
        <div class="step-item">
            <div class="step-dot" id="st3">3</div>
            <div class="step-label">Bank</div>
        </div>
        <div class="step-line" id="sl3"></div>
        <div class="step-item">
            <div class="step-dot" id="st4">4</div>
            <div class="step-label">Bayar</div>
        </div>
        <div class="step-line" id="sl4"></div>
        <div class="step-item">
            <div class="step-dot" id="st5">5</div>
            <div class="step-label">Kontak</div>
        </div>
        <div class="step-line" id="sl5"></div>
        <div class="step-item">
            <div class="step-dot" id="st6">6</div>
            <div class="step-label" id="st6-label">ERP</div>
        </div>
        <div class="step-line" id="sl6"></div>
        <div class="step-item">
            <div class="step-dot" id="st7">
                <i class="fa-solid fa-paperclip" style="font-size:9px;"></i>
            </div>
            <div class="step-label">Dok.</div>
        </div>
    </div>
</div>

<div class="vnd-wrap">
    <form id="form-vendor" method="POST" action="{{ route('ticket.vendor.store') }}">
        @csrf
        <input type="hidden" name="menu_id" value="{{ $menu_id }}">

        <div class="sistem-wrap">
            <div class="sistem-head">
                <div class="sh-icon"><i class="fa-solid fa-cog"></i></div>
                <div class="sh-title">Pilih Sistem ERP</div>
                <span class="sh-required">Wajib *</span>
            </div>
            <div class="sistem-body">
                <div class="sistem-grid">
                    <label class="sistem-opt epicor-opt">
                        <input type="radio" name="system_type" value="1" id="sys-epicor" required>
                        <span class="so-check"><i class="fa-solid fa-check"></i></span>
                        <div class="so-icon"><i class="fa-solid fa-microchip"></i></div>
                        <div class="so-name">Epicor</div>
                        <div class="so-desc">ERP Sistem PT. SPS</div>
                    </label>
                    <label class="sistem-opt accurate-opt">
                        <input type="radio" name="system_type" value="2" id="sys-accurate">
                        <span class="so-check"><i class="fa-solid fa-check"></i></span>
                        <div class="so-icon"><i class="fa-solid fa-chart-pie"></i></div>
                        <div class="so-name">Accurate</div>
                        <div class="so-desc">Sistem CV. SP</div>
                    </label>
                </div>
            </div>
        </div>

        <div class="sistem-wrap">
            <div class="sistem-head">
                <div class="sh-icon" style="background:#15803d;"><i class="fa-solid fa-tags"></i></div>
                <div class="sh-title">Kategori Vendor</div>
                <span class="sh-required">Wajib *</span>
            </div>
            <div class="sistem-body">
                <div class="sistem-grid">
                    <label class="sistem-opt bb-opt">
                        <input type="radio" name="vendor_category" value="V01">
                        <span class="so-check"><i class="fa-solid fa-check"></i></span>
                        <div class="so-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                        <div class="so-name">Bahan Baku</div>
                        <div class="so-desc">Material Bahan Baku Produksi</div>
                    </label>
                    <label class="sistem-opt nbb-opt">
                        <input type="radio" name="vendor_category" value="V02">
                        <span class="so-check"><i class="fa-solid fa-check"></i></span>
                        <div class="so-icon"><i class="fa-solid fa-box-archive"></i></div>
                        <div class="so-name">Non Bahan Baku</div>
                        <div class="so-desc">Asset, Jasa, ATK &amp; kebutuhan lain</div>
                    </label>
                </div>
            </div>
        </div>

        <div class="vnd-section" id="sec1">
            <div class="vnd-section-head" data-sec="sec1-body">
                <div class="sec-icon ic-s1"><i class="fa-solid fa-store"></i></div>
                <div class="sec-title">Data Usaha</div>
                <span class="sec-counter">5 field</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec1-body">

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-signature"></i> Nama Usaha <span class="req">*</span></label>
                    <input type="text" name="nama_usaha" class="vnd-input" placeholder="PT / CV / UD / Nama Usaha" required>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat Usaha <span class="req">*</span></label>
                    <textarea name="alamat_usaha" class="vnd-textarea" placeholder="Jl. ..., Kel. ..., Kec. ..., Kota ..." required></textarea>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-id-card"></i> NPWP <span class="req">*</span></label>
                    <input type="text" name="npwp" class="vnd-input" placeholder="00.000.000.0-000.000" maxlength="20" inputmode="numeric">
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-id-card"></i> NPWP16 <span class="req">*</span></label>
                    <input type="text" name="npwp16" class="vnd-input" placeholder="0000.0000.0000.0000" maxlength="20" inputmode="numeric">
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat NPWP 
                        <span class="badge-opt">opsional</span>
                        <span class="vnd-label-actions">
                            <button type="button" class="btn-label-action btn-copy-alamat" title="Salin dari Alamat Usaha">
                                <i class="fa-solid fa-copy"></i> Salin Alamat Usaha
                            </button>
                            <button type="button" class="btn-label-action btn-clear-alamat" title="Hapus isian">
                                <i class="fa-solid fa-xmark"></i> Hapus
                            </button>                         
                        </span>
                    </label>
                    <textarea name="alamat_npwp" id="alamat_npwp" class="vnd-textarea" placeholder="Kosongkan jika sama dengan alamat usaha"></textarea>
                    <div class="field-hint"><i class="fa-solid fa-circle-info"></i> Isi jika berbeda dengan alamat usaha</div>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-store"></i>Kategori Vendor</label>
                    <div class="vnd-select-wrap">
                        <select name="kategori" class="vnd-select" required>
                            <option value="">— Pilih Kategori —</option>
                            @foreach(['Vendor Asset','Vendor Bahan Baku Gabah','Vendor Bahan Baku Beras','Vendor Bahan Baku Jagung', 'Vendor Jasa', 'Vendor Sparepart Mesin Produksi', 'Vendor Sparepart Kendaraan','Vendor Sparepart Bangunan', 'Vendor GA', 'Vendor Maintenance', 'Vendor Rafaksi', 'Vendor Sak & Bahan Pembantu', 'Vendor Umum' ] as $kategori)
                            <option>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-link"></i> Supplier Afiliasi</label>
                    <div class="toggle-wrap">
                        <div class="toggle-info">
                            <span class="toggle-title" id="afiliasi-label">Tidak ada afiliasi</span>
                            <span class="toggle-sub">Aktifkan jika vendor terafiliasi</span>
                        </div>
                        <div class="toggle-switch" id="afiliasi-toggle"></div>
                        <input type="hidden" name="is_afiliasi" id="afiliasi-val" value="0">
                    </div>
                </div>

            </div>
        </div>

        <div class="vnd-section" id="sec2">
            <div class="vnd-section-head collapsed" data-sec="sec2-body">
                <div class="sec-icon ic-s2"><i class="fa-solid fa-user-tie"></i></div>
                <div class="sec-title">Data Pemilik</div>
                <span class="sec-counter">3 field</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec2-body" style="display:none;">

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-user"></i> Nama Pemilik <span class="req">*</span></label>
                    <input type="text" name="nama_pemilik" class="vnd-input" placeholder="Nama sesuai KTP" required>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-id-card"></i> NIK Pemilik <span class="req">*</span></label>
                    <input type="text" name="nik_pemilik" class="vnd-input" placeholder="16 digit NIK" maxlength="16" inputmode="numeric" required>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat Pemilik <span class="req">*</span></label>
                    <textarea name="alamat_pemilik" class="vnd-textarea" placeholder="Alamat sesuai KTP" required></textarea>
                </div>

            </div>
        </div>

        <div class="vnd-section" id="sec3">
            <div class="vnd-section-head collapsed" data-sec="sec3-body">
                <div class="sec-icon ic-s3"><i class="fa-solid fa-building-columns"></i></div>
                <div class="sec-title">Data Bank</div>
                <span class="sec-counter">4 field</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec3-body" style="display:none;">

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-building-columns"></i> Nama Bank <span class="req">*</span></label>
                    <div class="vnd-select-wrap">
                        <select name="nama_bank" class="vnd-select" required>
                            <option value="">— Pilih Bank —</option>
                            @foreach(['Bank BCA','Bank Mandiri','Bank BNI','Bank BRI','Bank CIMB Niaga','Bank Danamon','Bank Permata','Bank BTN','Bank OCBC NISP','Bank Mega','Bank Maybank','Bank Panin','Lainnya'] as $bank)
                            <option>{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-user-check"></i> Nama Penerima Rekening <span class="req">*</span></label>
                    <input type="text" name="nama_bank_penerima" class="vnd-input" placeholder="Nama sesuai buku tabungan" required>
                </div>

                <div class="vnd-row">
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-hashtag"></i> No. Rekening <span class="req">*</span></label>
                        <input type="text" name="no_rekening" class="vnd-input" placeholder="Nomor rekening" inputmode="numeric" required>
                    </div>
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-code-branch"></i> Cabang <span class="badge-opt">opsional</span></label>
                        <input type="text" name="cabang_bank" class="vnd-input" placeholder="KCP Surabaya">
                    </div>
                </div>

            </div>
        </div>

        <div class="vnd-section" id="sec4">
            <div class="vnd-section-head collapsed" data-sec="sec4-body">
                <div class="sec-icon ic-s4"><i class="fa-solid fa-wallet"></i></div>
                <div class="sec-title">Pembayaran</div>
                <span class="sec-counter">2 field</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec4-body" style="display:none;">

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-wallet"></i> Metode Pembayaran <span class="req">*</span></label>
                    <div class="radio-group">
                        @foreach(['Transfer Bank','Cek/Giro','Tunai','Virtual Account'] as $m)
                        <label class="radio-opt">
                            <input type="radio" name="metode_pembayaran" value="{{ $m }}" {{ $loop->first ? 'required' : '' }}>
                            <span class="radio-dot"></span><span>{{ $m }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <hr class="vnd-divider">

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-calendar-days"></i> Syarat Pembayaran (Termin) <span class="req">*</span></label>
                    <div class="radio-group">
                        @foreach(['Cash','NET 14','NET 30','NET 45','NET 60'] as $s)
                        <label class="radio-opt">
                            <input type="radio" name="syarat_pembayaran" value="{{ $s }}" {{ $loop->first ? 'required' : '' }}>
                            <span class="radio-dot"></span><span>{{ $s }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

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
                        <label class="vnd-label"><i class="fa-solid fa-user"></i> Nama (KTP) <span class="req">*</span></label>
                        <input type="text" name="cp_nama" class="vnd-input" placeholder="Nama sesuai KTP" required>
                    </div>
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-briefcase"></i> Jabatan <span class="badge-opt">opsional</span></label>
                        <input type="text" name="cp_jabatan" class="vnd-input" placeholder="Direktur">
                    </div>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-phone"></i> No. Telp / HP <span class="req">*</span></label>
                    <input type="text" name="cp_no_telp" class="vnd-input" placeholder="08xx-xxxx-xxxx" inputmode="tel" required>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-location-dot"></i> Alamat (KTP) <span class="badge-opt">opsional</span></label>
                    <textarea name="cp_alamat" class="vnd-textarea" placeholder="Alamat sesuai KTP"></textarea>
                </div>

            </div>
        </div>

        <div class="vnd-section sec-sistem" id="sec-epicor" style="display:none;">
            <div class="vnd-section-head collapsed" data-sec="sec-epicor-body">
                <div class="sec-icon ic-s6-epicor"><i class="fa-solid fa-microchip"></i></div>
                <div class="sec-title">
                    Konfigurasi Epicor
                    <!-- <span class="epicor-tag"><i class="fa-solid fa-cube"></i> ERP</span> -->
                </div>
                <span class="sec-counter" style="background:#fef3c7; color:#b45309; border-color:#fde68a;">Validator</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec-epicor-body" style="display:none;">
                <div class="validator-notice">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Konfigurasi ini <strong>diisi oleh Validator</strong> pada saat proses validasi — pengaju tidak perlu mengisi bagian ini.</span>
                </div>
                <div class="vnd-row">
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-file-invoice-dollar"></i> Tax Liability</label>
                        <input type="text" name="epicor_tax_liability" class="vnd-input" placeholder="DOMESTIC">
                    </div>
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-layer-group"></i> Supplier Group</label>
                        <input type="text" name="epicor_supplier_group" class="vnd-input" placeholder="LOCAL">
                    </div>
                </div>

                <div class="vnd-row">
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-sliders"></i> GL Control Type</label>
                        <input type="text" name="epicor_gl_control_type" class="vnd-input" placeholder="AP">
                    </div>
                    <div class="vnd-group">
                        <label class="vnd-label"><i class="fa-solid fa-code"></i> GL Control Code</label>
                        <input type="text" name="epicor_gl_control_code" class="vnd-input" placeholder="AP-001">
                    </div>
                </div>
                <!-- <div class="field-hint" style="margin-top:2px;">
                    <i class="fa-solid fa-circle-info"></i> Semua field opsional — isi sesuai konfigurasi Epicor
                </div> -->
            </div>
        </div>

        <div class="vnd-section sec-sistem" id="sec-accurate" style="display:none;">
            <div class="vnd-section-head collapsed" data-sec="sec-accurate-body">
                <div class="sec-icon ic-s6-accurate"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="sec-title">
                    Konfigurasi Accurate
                    <!-- <span class="accurate-tag"><i class="fa-solid fa-chart-bar"></i> Akuntansi</span> -->
                </div>
                <span class="sec-counter">3 field</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec-accurate-body" style="display:none;">

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-sitemap"></i> Dipakai Cabang <span class="badge-opt">opsional</span></label>
                    <input type="text" name="accurate_dipakai_cabang" class="vnd-input" placeholder="Cabang">
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-tag"></i> Kategori Vendor <span class="req">*</span></label>
                    <input type="text" name="accurate_kategori" class="vnd-input" placeholder="Kategori">
                    <!-- <div class="vnd-select-wrap">
                        <select name="accurate_kategori" class="vnd-select">
                            <option value="">— Pilih Kategori —</option>
                            <option value="barang">Barang</option>
                            <option value="jasa">Jasa</option>
                            <option value="barang_jasa">Barang &amp; Jasa</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div> -->
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-truck-ramp-box"></i> Sourcing</label>
                    <input type="text" name="accurate_sourching" class="vnd-input" placeholder="Sourching">
                    <!-- <div class="radio-group accurate-radio-group">
                        @foreach(['Lokal','Import','Keduanya'] as $src)
                        <label class="radio-opt">
                            <input type="radio" name="accurate_sourcing" value="{{ $src }}">
                            <span class="radio-dot"></span><span>{{ $src }}</span>
                        </label>
                        @endforeach
                    </div> -->
                </div>
                <!-- <div class="field-hint" style="margin-top:2px;">
                    <i class="fa-solid fa-circle-info"></i> Isi sesuai konfigurasi Accurate di perusahaan
                </div> -->
            </div>
        </div>

        <div class="sistem-placeholder" id="sistem-placeholder">
            <i class="fa-solid fa-arrow-up-from-bracket"></i>
            <p>Pilih sistem ERP di atas untuk menampilkan konfigurasi yang sesuai</p>
        </div>

        <div class="vnd-section" id="sec-dokumen">
            <div class="vnd-section-head collapsed" data-sec="sec-dokumen-body">
                <div class="sec-icon ic-s7"><i class="fa-solid fa-paperclip"></i></div>
                <div class="sec-title">Dokumen Pendukung</div>
                <span class="sec-counter" id="doc-counter">Opsional</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec-dokumen-body" style="display:none;">

                <div class="field-hint" style="margin-bottom:10px;">
                    <i class="fa-solid fa-circle-info"></i>
                    Unggah KTP, NPWP, SIUP, Akta, dll. Maks. <strong>5 MB</strong> per file, format <strong>PDF / JPG / PNG</strong>.
                </div>

                <div class="upload-zone" id="upload-zone">
                    <input type="file" id="upload-input" name="documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <div class="uz-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <div class="uz-title">Klik atau seret file ke sini</div>
                    <div class="uz-sub">PDF, JPG, PNG &bull; Maks. 5 MB per file</div>
                </div>
                <div class="file-list" id="file-list" style="display:none;"></div>
            </div>
        </div>

        <div class="vnd-section" id="sec-sign">
            <div class="vnd-section-head" data-sec="sec-sign-body">
                <div class="sec-icon" style="background:#fce7f3; color:#be185d;">
                    <i class="fa-solid fa-pen-nib"></i>
                </div>
                <div class="sec-title">Otorisasi &amp; Pengesahan</div>
                <span class="sec-counter">TTD</span>
                <i class="fa-solid fa-chevron-down sec-chevron"></i>
            </div>
            <div class="vnd-section-body" id="sec-sign-body">
                <div class="vnd-group">
                    <label class="vnd-label">
                        <i class="fa-solid fa-pen-nib"></i> Tanda Tangan Pengaju <span class="req">*</span>
                    </label>
                    <div style="border:1.5px solid #e8e2fc; border-radius:12px; overflow:hidden;">
                        <div style="background:#faf5ff; padding:7px 12px; border-bottom:1px solid #ede9fe;
                                    font-size:9px; font-weight:700; color:#6d28d9; text-transform:uppercase;
                                    letter-spacing:.5px; text-align:center;">
                            Diajukan Oleh
                        </div>
                        <div style="padding:10px 12px;">
                            <canvas id="signature-pad"
                                style="width:100%; touch-action:none; height:180px;
                                    border:1px solid #f3e8ff; border-radius:8px; display:block;">
                            </canvas>
                            <textarea id="signature" name="signature" style="display:none;"></textarea>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                <button type="button" id="clear-signature"
                                    class="btn-label-action btn-clear-alamat" style="font-size:10px;">
                                    <i class="fa-solid fa-eraser"></i> Hapus TTD
                                </button>
                                <span style="font-size:10px; color:#9ca3af;">Tanda tangan di atas</span>
                            </div>
                        </div>
                        <div style="border-top:1px solid #ede9fe; padding:7px; text-align:center;
                                    font-size:10px; font-weight:700; color:#374151; background:#fafafa;">
                            {{ auth()->user()->name ?? 'Pengaju' }}
                        </div>
                    </div>
                </div>

                <div class="vnd-group">
                    <label class="vnd-label"><i class="fa-solid fa-stamp"></i> Kolom Otorisasi</label>
                    <div class="sign-approver-grid">

                        <div class="sign-col-card">
                            <div class="sign-col-head">Diajukan Oleh:</div>
                            <div class="sign-col-body">
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            </div>
                            <div class="sign-col-name" id="sign-requestor">
                                <span class="skeleton-text"></span>
                            </div>
                        </div>

                        <div class="sign-col-card">
                            <div class="sign-col-head">Diketahui Oleh:</div>
                            <div class="sign-col-body">
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            </div>
                            <div class="sign-col-name" id="sign-atasan1">
                                <span class="skeleton-text"></span>
                            </div>
                        </div>

                        <div class="sign-col-card">
                            <div class="sign-col-head">Disetujui Oleh:</div>
                            <div class="sign-col-body">
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            </div>
                            <div class="sign-col-name" id="sign-atasan2">
                                <span class="skeleton-text"></span>
                            </div>
                        </div>

                        <div class="sign-col-card">
                            <div class="sign-col-head">Diperiksa Oleh:</div>
                            <div class="sign-col-body">
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            </div>
                            <div class="sign-col-name" id="sign-checker">
                                <span class="skeleton-text"></span>
                            </div>
                        </div>

                        <div class="sign-col-card" style="grid-column: span 2;">
                            <div class="sign-col-head">Divalidasi Oleh:</div>
                            <div class="sign-col-body">
                                <div class="sign-col-placeholder"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            </div>
                            <div class="sign-col-name" id="sign-validator">
                                <span class="skeleton-text"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="vnd-submit-wrap">
            <button type="submit" class="vnd-submit" id="btn-submit">
                <i class="fa-solid fa-paper-plane"></i> Ajukan Pendaftaran Vendor
            </button>
        </div>

    </form>
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
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });

    $(document).ready(function () {

        let selectedFiles = [];
        let fileIdCounter = 0;

        $('input[name="system_type"]').on('change', function () {
            const val = $(this).val();
            $('#sec-epicor, #sec-accurate').hide();
            $('#sistem-placeholder').hide();
            $('#sec-epicor-body, #sec-accurate-body').hide();
            $('#sec-epicor .vnd-section-head, #sec-accurate .vnd-section-head').addClass('collapsed');

            if (val === '1') {
                $('#sec-epicor').show();
                $('#st6-label').text('Epicor');
                $('select[name="kategori"]').removeAttr('required');
            } else if (val === '2') {
                $('#sec-accurate').show();
                $('#st6-label').text('Accurate');
                $('select[name="kategori"]').attr('required', 'required');
            }
            updateSteps();
        });

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

        $(document).on('click', '.btn-copy-alamat', function () {
            const sumber = $('[name="alamat_usaha"]').val().trim();

            if (!sumber) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Alamat Usaha Kosong',
                    text: 'Isi Alamat Usaha terlebih dahulu sebelum menyalin.',
                    confirmButtonColor: '#6366f1',
                });
                return;
            }

            $('#alamat_npwp').val(sumber).trigger('input');

            const $btn = $(this);
            $btn.addClass('copied').html('<i class="fa-solid fa-check"></i> Tersalin');
            setTimeout(() => {
                $btn.removeClass('copied').html('<i class="fa-solid fa-copy"></i> Salin');
            }, 1800);
        });

        $(document).on('click', '.btn-clear-alamat', function () {
            $('#alamat_npwp').val('').trigger('input');
        });

        $('#afiliasi-toggle').on('click', function () {
            const isOn = $(this).hasClass('on');
            $(this).toggleClass('on');
            $('#afiliasi-val').val(isOn ? '0' : '1');
            $('#afiliasi-label').text(isOn ? 'Tidak ada afiliasi' : 'Ya, ada afiliasi');
        });

        $('#upload-input').on('change', function () {
            Array.from(this.files).forEach(addFile);
            this.value = ''; 
        });

        $('#upload-zone')
            .on('dragenter dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            })
            .on('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (!this.contains(e.originalEvent.relatedTarget)) {
                    $(this).removeClass('dragover');
                }
            })
            .on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                Array.from(e.originalEvent.dataTransfer.files).forEach(addFile);
            });

        function addFile(file) {
            const MAX_SIZE = 5 * 1024 * 1024;
            const ALLOWED  = ['application/pdf', 'image/jpeg', 'image/png'];

            if (file.size > MAX_SIZE) {
                Swal.fire('File Terlalu Besar', '"' + file.name + '" melebihi batas 5 MB.', 'warning');
                return;
            }
            if (!ALLOWED.includes(file.type)) {
                Swal.fire('Format Tidak Didukung', '"' + file.name + '" harus berformat PDF, JPG, atau PNG.', 'warning');
                return;
            }

            const id = ++fileIdCounter;
            selectedFiles.push({ 
                id: id, 
                file: file, 
                documentName: '' 
            });
            renderFileList();
            updateSteps();
        }

        function renderFileList() {
            const $list = $('#file-list');
            $list.empty();

            if (selectedFiles.length === 0) {
                $list.hide();
                $('#doc-counter').text('Opsional');
                return;
            }

            $list.show();
            $('#doc-counter').text(selectedFiles.length + ' file');

            selectedFiles.forEach(function (item) {
                const isPdf   = item.file.type === 'application/pdf';
                const iconHtml = isPdf
                    ? '<i class="fa-solid fa-file-pdf" style="color:#ef4444;font-size:15px;"></i>'
                    : '<i class="fa-solid fa-file-image" style="color:#7c3aed;font-size:15px;"></i>';

                $list.append(
                    '<div class="file-item" data-fid="' + item.id + '">' +
                        '<div class="fi-icon">' + iconHtml + '</div>' +
                        '<div class="fi-info">' +
                            '<div class="fi-name" title="' + item.file.name + '">' + item.file.name + '</div>' +
                            '<div class="fi-meta-row">' +
                                '<span class="fi-size">' + formatBytes(item.file.size) + '</span>' +
                                '<input type="text" ' +
                                'class="doc-name-input" ' +
                                'placeholder="Masukkan nama dokumen" ' +
                                'value="' + (item.documentName || '') + '">' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" class="fi-remove"><i class="fa-solid fa-xmark"></i></button>' +
                    '</div>'
                );
            });
        }

        $(document).on('input', '#file-list .doc-name-input', function () {
            const fid = parseInt(
                $(this).closest('[data-fid]').data('fid')
            );
            const item = selectedFiles.find(function (f) {
                return f.id === fid;
            });
            if (item) {
                item.documentName = $(this).val();
            }
        });

        $(document).on('click', '#file-list .fi-remove', function () {
            const fid = parseInt($(this).closest('[data-fid]').data('fid'));
            selectedFiles = selectedFiles.filter(function (f) { return f.id !== fid; });
            renderFileList();
            updateSteps();
        });

        function formatBytes(bytes) {
            if (bytes < 1024)        return bytes + ' B';
            if (bytes < 1048576)     return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        function updateSteps() {
            $('#st0').addClass('done');
            $('#sl0').addClass('done');

            ['sec1-body', 'sec2-body', 'sec3-body', 'sec4-body', 'sec5-body']
                .forEach(function (id, i) {
                    const n = i + 1;
                    if ($('#' + id).is(':visible')) {
                        $('#st' + n).addClass('done');
                        if (n > 1) $('#sl' + (n - 1)).addClass('done');
                    }
                });

            const epicorOpen   = $('#sec-epicor').is(':visible') && $('#sec-epicor-body').is(':visible');
            const accurateOpen = $('#sec-accurate').is(':visible') && $('#sec-accurate-body').is(':visible');
            if (epicorOpen || accurateOpen) {
                $('#st6').addClass('done');
                $('#sl5').addClass('done');
            }

            if ($('#sec-dokumen-body').is(':visible')) {
                $('#st7').addClass('done');
                $('#sl6').addClass('done');
            }
        }

        const signCanvas = document.getElementById('signature-pad');
        let signaturePad = null;

        if (signCanvas) {
            signaturePad = new SignaturePad(signCanvas);

            function resizeSignCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                signCanvas.width  = signCanvas.offsetWidth  * ratio;
                signCanvas.height = signCanvas.offsetHeight * ratio;
                signCanvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener('resize', resizeSignCanvas);
            resizeSignCanvas();

            $('#clear-signature').on('click', function () {
                signaturePad.clear();
            });
        }

        function loadApprovalVendorPreview() {
            $.ajax({
                url: '{{ route("ticket.vendor.preview_flow", ["menuId" => $menu_id]) }}',
                type: 'GET',
                success: function (res) {
                    if (res.code !== 200) {
                        $('#sign-atasan1, #sign-atasan2, #sign-checker, #sign-validator')
                            .html('<span style="color:#c4b5fd;">-</span>');
                        return;
                    }
                    const fill = function (elId, data, fallbackText) {
                        if (data && data.karyawan_name) {
                            $(elId).html(
                                '<div>' + data.karyawan_name + '</div>' +
                                (data.nama_approval
                                    ? '<div style="font-size:8px;color:#9ca3af;font-weight:500;margin-top:2px;">' + data.nama_approval + '</div>'
                                    : '')
                            );
                            return;
                        }
                        if (data && data.nama_approval) {
                            $(elId).html(
                                '<div style="color:#9ca3af;font-style:italic;font-size:10px;">Belum ditentukan</div>' +
                                '<div style="font-size:8px;color:#c4b5fd;font-weight:600;margin-top:2px;">' + data.nama_approval + '</div>'
                            );
                            return;
                        }
                        $(elId).html(fallbackText || '<span style="color:#c4b5fd;">-</span>');
                    };
                    fill('#sign-requestor', res.result.karyawan);
                    fill('#sign-atasan1', res.result.atasan1);
                    fill('#sign-atasan2', res.result.atasan2);
                    fill('#sign-checker', res.result.checker);
                    fill('#sign-validator', res.result.validator, 'Finance &amp; Accounting');
                },
                error: function () {
                    $('#sign-atasan1, #sign-atasan2, #sign-checker, #sign-validator')
                        .html('<span style="color:#ef4444;">Gagal memuat</span>');
                }
            });
        }
        loadApprovalVendorPreview();

        $('#form-vendor').on('submit', function (e) {
            e.preventDefault();

            if (!$('input[name="vendor_category"]:checked').val()) {
                Swal.fire({ icon: 'warning', title: 'Pilih Kategori Vendor', text: 'Harap pilih Bahan Baku atau Non Bahan Baku.' });
                return;
            }

            if (!$('input[name="system_type"]:checked').val()) {
                Swal.fire({ icon: 'warning', title: 'Pilih Sistem ERP', text: 'Harap pilih sistem Epicor atau Accurate terlebih dahulu.' });
                return;
            }

            // ✅ Capture signature FIRST, before FormData snapshots the form
            if (signaturePad && !signaturePad.isEmpty()) {
                $('#signature').val(signaturePad.toDataURL());
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanda Tangan Kosong',
                    text: 'Harap tanda tangani formulir sebelum mengajukan.',
                    confirmButtonColor: '#7c3aed',
                });
                return;
            }

            const $btn = $('#btn-submit');
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Mengajukan...');

            // ✅ Now formData will correctly pick up the populated #signature value
            const formData = new FormData(this);

            selectedFiles.forEach(function (item) {
                formData.append('documents[]', item.file, item.file.name);
                formData.append('document_names[]', item.documentName || '');
            });

            $.ajax({
                url:         $(this).attr('action'),
                type:        'POST',
                data:        formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.code == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Pengajuan vendor berhasil dikirim.',
                            timer: 2500,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.href = res.redirect ?? '/';
                        });
                    } else {
                        Swal.fire('Gagal', window.getFriendlyErrorMessage(res.message, 'Gagal mengajukan pendaftaran vendor.'), 'error');
                        resetBtn();
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'warning' : 'error',
                        title: (xhr.status === 422 || xhr.responseJSON?.code === 422) ? 'Validasi Gagal' : 'Gagal',
                        html: window.getFriendlyErrorMessage(xhr, 'Tidak dapat terhubung ke server.')
                    });
                    resetBtn();
                }
            });

            function resetBtn() {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane"></i> Ajukan Pendaftaran Vendor');
            }
        });

        $('input[name="nik_pemilik"]').on('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 16);
        });
        // $('input[name="npwp"]').on('input', function () {
        //     let v = this.value.replace(/\D/g, '').slice(0, 15);
        //     if      (v.length > 9) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'.'+v.slice(8,9)+'-'+v.slice(9,12)+'.'+v.slice(12);
        //     else if (v.length > 8) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'.'+v.slice(8);
        //     else if (v.length > 5) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5);
        //     else if (v.length > 2) v = v.slice(0,2)+'.'+v.slice(2);
        //     this.value = v;
        // });

        $('input[name="cp_no_telp"]').on('input', function () {
            this.value = this.value.replace(/[^\d\-\+\s]/g, '');
        });

        // Convert all text inputs and textareas to UPPERCASE
        $(document).on('input', 'input[type="text"], textarea', function () {
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });

        updateSteps();
    });
</script>
@endsection