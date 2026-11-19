@extends('admin.layouts.dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/css/jquery.orgchart.min.css">

<style>
    * {
        font-family: 'Work Sans', var(--bs-body-font-family), sans-serif;
    }

    .approval-flow-page {
        --approval-primary: #6d46da;
        --approval-primary-soft: #f0f0ff;
        --approval-primary-light: #ede9ff;
        --approval-primary-border: #d8ccff;
        --approval-text: #566a7f;
        color: var(--approval-text);
    }

    .approval-flow-page .card {
        border: 0;
        border-radius: 8px;
        box-shadow: 0 0.25rem 1rem rgba(67, 89, 113, .12);
    }

    .approval-flow-page .card-header {
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .approval-flow-page .card-title {
        color: var(--approval-text);
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0;
    }

    .approval-flow-page #flowTabs {
        display: flex;
        gap: 4px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0;
    }

    .approval-flow-page #flowTabs .nav-link {
        padding: 8px 20px;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 4px 4px 0 0;
        background: transparent;
        color: #6c757d;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .5px;
        cursor: pointer;
        margin-bottom: -2px;
        transition: all .2s ease;
    }

    .approval-flow-page #flowTabs .nav-link:hover,
    .approval-flow-page #flowTabs .nav-link:focus {
        border-color: transparent;
        border-bottom-color: transparent;
        background-color: var(--approval-primary-soft);
        color: var(--approval-primary);
    }

    .approval-flow-page #flowTabs .nav-link.active {
        border-bottom-color: var(--approval-primary);
        background-color: transparent;
        color: var(--approval-primary);
    }

    .approval-flow-page #flowTabs .nav-link i {
        color: inherit;
        font-size: 16px;
    }

    .approval-flow-page .btn-primary {
        border-color: #8b4dfb;
        background-color: #8b4dfb;
        color: #fff;
    }

    .approval-flow-page .btn-primary:hover,
    .approval-flow-page .btn-primary:focus {
        border-color: var(--approval-primary);
        background-color: var(--approval-primary);
        color: #fff;
    }

    .approval-flow-page .filter-pill:hover {
        border-color: var(--approval-primary);
        color: var(--approval-primary);
    }

    .approval-flow-page .filter-pill.active {
        border-color: var(--approval-primary);
        background: var(--approval-primary);
        color: #fff;
    }

    .approval-flow-page .sc-purple {
        border-color: var(--approval-primary-border);
        background: var(--approval-primary-light);
    }

    .approval-flow-page .sc-purple .sc-icon {
        background: #e3dcff;
        color: var(--approval-primary);
    }

    .approval-flow-page .sc-purple .sc-val {
        color: var(--approval-primary);
    }

    .approval-flow-page .modern-card {
        border-color: var(--approval-primary-border);
        box-shadow: 0 4px 14px rgba(109, 70, 218, .10);
    }

    .approval-flow-page .modern-card:hover {
        box-shadow: 0 8px 24px rgba(109, 70, 218, .18);
    }

    .approval-flow-page .card-header-gradient {
        background: linear-gradient(90deg, var(--approval-primary) 0%, #a78bfa 100%);
    }

    .approval-flow-page .step-seq-badge {
        background: var(--approval-primary);
        box-shadow: 0 2px 6px rgba(109, 70, 218, .3);
    }

    .approval-flow-page .orgchart .lines .topLine,
    .approval-flow-page .orgchart .lines .downLine,
    .approval-flow-page .orgchart .lines .rightLine,
    .approval-flow-page .orgchart .lines .leftLine {
        border-color: var(--approval-primary) !important;
    }

    .approval-flow-page .orgchart .lines .downLine {
        background-color: var(--approval-primary) !important;
    }

    .approval-flow-page .emp-card.selected .check-indicator {
        border-color: var(--approval-primary);
        background: var(--approval-primary);
    }

    .approval-flow-page .avatar-box,
    .approval-flow-page .karyawan-avatar-sm {
        background: linear-gradient(135deg, var(--approval-primary), #a78bfa);
    }

    .select2-container {
        z-index: 9999 !important;
    }
    .swal2-container {
        z-index: 99999 !important;
    }
    .stat-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    .stat-card {
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid transparent;
    }
    .stat-card .sc-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .stat-card .sc-val  { font-size: 22px; font-weight: 800; line-height: 1; }
    .stat-card .sc-label{ font-size: 11px; color: #6b7280; margin-top: 2px; }

    .sc-purple { background: #f5f3ff; border-color: #e9d5ff; }
    .sc-purple .sc-icon { background: #ede9fe; color: #7c3aed; }
    .sc-purple .sc-val  { color: #5b21b6; }

    .sc-blue { background: #eff6ff; border-color: #bfdbfe; }
    .sc-blue .sc-icon { background: #dbeafe; color: #1d4ed8; }
    .sc-blue .sc-val  { color: #1e40af; }

    .sc-green { background: #f0fdf4; border-color: #bbf7d0; }
    .sc-green .sc-icon { background: #dcfce7; color: #16a34a; }
    .sc-green .sc-val  { color: #15803d; }

    .sc-amber { background: #fffbeb; border-color: #fde68a; }
    .sc-amber .sc-icon { background: #fef3c7; color: #d97706; }
    .sc-amber .sc-val  { color: #b45309; }

    /* Filter pill bar */
    .filter-pill-bar {
        display: flex; align-items: center; gap: 8px;
        flex-wrap: wrap; margin-bottom: 14px;
    }
    .filter-pill {
        padding: 5px 14px; border-radius: 50px; font-size: 12px;
        font-weight: 600; border: 1.5px solid #e5e7eb;
        background: #f9fafb; color: #6b7280; cursor: pointer;
        transition: all .15s; user-select: none;
    }
    .filter-pill:hover  { border-color: #7c3aed; color: #7c3aed; }
    .filter-pill.active { background: #7c3aed; color: #fff; border-color: #7c3aed; }

    /* Table enhancements */
    .tbl-modern thead th {
        background: #faf5ff; color: #5b21b6;
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; border-bottom: 2px solid #e9d5ff !important;
        padding: 10px 12px;
    }
    .tbl-modern tbody tr {
        transition: background .12s;
    }
    .tbl-modern tbody tr:hover { background: #faf5ff !important; }
    .tbl-modern tbody td {
        vertical-align: middle; padding: 10px 12px;
        font-size: 13px; border-color: #f3f0ff;
    }

    /* Karyawan display inline */
    .karyawan-cell {
        display: flex; align-items: center; gap: 8px;
    }
    .karyawan-avatar-sm {
        width: 30px; height: 30px; border-radius: 8px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff; font-size: 12px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .karyawan-cell .kc-name  { font-weight: 600; font-size: 12.5px; color: #1f2937; }
    .karyawan-cell .kc-dept  { font-size: 10.5px; color: #9ca3af; }

    /* Env badge */
    .env-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .env-epicor       { background: #1e1b4b; color: #c4b5fd; }
    .env-accurate     { background: #1e3a8a; color: #93c5fd; }
    .env-accurate-sp  { background: #1e3a8a; color: #93c5fd; }
    .env-accurate-aja { background: #064e3b; color: #6ee7b7; }

    /* Assignment type badge */
    .asgn-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .asgn-a01 { background: #fdf4ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .asgn-a02 { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

    /* Action btn mini */
    .act-btn {
        width: 28px; height: 28px; border-radius: 7px;
        border: none; display: inline-flex;
        align-items: center; justify-content: center;
        font-size: 13px; cursor: pointer; transition: all .15s;
    }
    .act-edit   { background: #fff8ec; color: #f59e0b; }
    .act-edit:hover   { background: #f59e0b; color: #fff; }
    .act-delete { background: #fef2f2; color: #ef4444; }
    .act-delete:hover { background: #ef4444; color: #fff; }

    /* ===== ORGCHART OVERRIDE ===== */
    .orgchart {
        background-image: none !important;
        background: none !important;
    }

    .orgchart .node {
        width: auto !important;
        min-width: 190px;
        max-width: 320px;
        height: auto !important;
        padding: 0;
        border: none;
        background: transparent;
        box-shadow: none;
        margin: 0 8px;        /* jarak horizontal antar card */
    }

    .orgchart td {
        padding: 0 4px !important;  /* tambahan padding sel tabel */
    }

    .orgchart > ul > li > ul li::before {
        top: -20px !important;
    }

    .orgchart .node .title,
    .orgchart .node .content {
        display: none !important;
    }

    .orgchart .lines .topLine {
        border-top: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .orgchart .lines .downLine {
        background-color: #9055fd !important;
        border: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .orgchart .lines .rightLine {
        border-right: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .orgchart .lines .leftLine {
        border-left: 2px solid #9055fd !important;
        float: none !important;
        border-radius: 0 !important;
    }

    .org-wrapper {
        border: 1.5px solid #e2d9f3 !important;
        border-radius: 0.5rem !important;
        overflow: auto;
        min-height: 220px;
        text-align: center;
        padding: 1.5rem 1rem;
    }

    /* ===== MODERN NODE CARD (sama persis pola dari master approval) ===== */
    .modern-card {
        background: #ffffff;
        border: 1.5px solid #e2d9f3;
        border-radius: 16px;
        overflow: hidden;
        min-width: 190px;
        max-width: 260px;
        box-shadow: 0 4px 14px rgba(144, 85, 253, 0.10);
        transition: box-shadow .25s, transform .25s;
        cursor: default;
        position: relative;
        padding: 2px;
    }

    .modern-card:hover {
        box-shadow: 0 8px 24px rgba(144, 85, 253, 0.20);
        transform: translateY(-3px);
    }

    /* Header strip warna berdasarkan tipe step */
    .card-header-gradient {
        height: 6px;
        background: linear-gradient(90deg, #9055fd 0%, #c49ffd 100%);
    }


    .modern-card.validator .card-header-gradient {
        background: linear-gradient(90deg, #10b981 0%, #6ee7b7 100%);
    }

    .modern-card.final .card-header-gradient {
        background: linear-gradient(90deg, #28c76f 0%, #81e6a8 100%);
    }

    /* Nomor urut badge di sudut kiri atas */
    .step-seq-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #9055fd;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(144, 85, 253, .3);
    }

    .modern-card.validator .step-seq-badge {
        background: #10b981;
    }

    .modern-card.final .step-seq-badge {
        background: #28c76f;
    }

    .card-body.card-chart {
        padding: 14px 14px 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Avatar */
    .avatar-wrapper .avatar-img {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #e2d9f3;
    }

    /* Info text */
    .info-wrapper {
        flex: 1;
        min-width: 0;
    }

    .emp-name {
        font-weight: 700;
        font-size: .85rem;
        color: #2d3436;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .emp-title {
        font-size: .75rem;
        color: #636e72;
        margin-top: 2px;
    }

    /* Role badge dalam card */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 10px;
        font-weight: 600;
        border-radius: 20px;
        padding: 2px 7px;
        margin-top: 6px;
    }

    .badge-approver {
        background: #eef2ff;
        color: #6366f1;
    }

    .badge-checker {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-validator {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-final {
        background: #dcfce7;
        color: #166534;
    }

    /* User count footer */
    .node-user-footer {
        padding: 5px 12px 8px;
        font-size: 11px;
        color: #9055fd;
        font-weight: 600;
        text-align: center;
        border-top: 1px dashed #e2d9f3;
    }

    .modern-card.validator .node-user-footer {
        color: #10b981;
        border-top-color: #a7f3d0;
    }

    /* ===== ACTION BUTTONS bawah card (sama persis master approval) ===== */
    .card-actions {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-top: 1px solid #f0ede8;
        background: #faf9ff;
    }

    .modern-card.checker .card-actions {
        background: #fffdf5;
        border-top-color: #fde68a22;
    }

    .modern-card.validator .card-actions {
        background: #f0fdf4;
        border-top-color: #a7f3d022;
    }

    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-add {
        background: #eef2ff;
        color: #6366f1;
    }

    .btn-add:hover {
        background: #6366f1;
        color: #fff;
    }

    .btn-users {
        background: #fff8ec;
        color: #f59e0b;
    }

    .btn-users:hover {
        background: #f59e0b;
        color: #fff;
    }

    .btn-edit {
        background: #fff3e0;
        color: #ff9f43;
    }

    .btn-edit:hover {
        background: #ff9f43;
        color: #fff;
    }

    .btn-parent {
        background: #eef2ff;
        color: #4f46e5;
    }

    .btn-parent:hover {
        background: #4f46e5;
        color: #fff;
    }

    .btn-delete {
        background: #ffebee;
        color: #ea5455;
    }

    .btn-delete:hover {
        background: #ea5455;
        color: #fff;
    }

    /* ===== LOADING ===== */
    .table-loading {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, .7);
        backdrop-filter: blur(4px);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loading-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        font-size: .9rem;
        color: #6b7280;
    }

    .spinner {
        width: 36px;
        height: 36px;
        border: 4px solid #e5e7eb;
        border-top-color: #7367f0;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ===== DOT LOADER ===== */
    .dot-loader {
        display: flex;
        gap: 4px;
    }

    .dot-loader span {
        width: 6px;
        height: 6px;
        background: #7367f0;
        border-radius: 50%;
        opacity: .3;
        animation: dotPulse 1.2s infinite ease-in-out;
    }

    .dot-loader span:nth-child(1) {
        animation-delay: 0s;
    }

    .dot-loader span:nth-child(2) {
        animation-delay: .2s;
    }

    .dot-loader span:nth-child(3) {
        animation-delay: .4s;
    }

    @keyframes dotPulse {

        0%,
        100% {
            opacity: .3
        }

        50% {
            opacity: 1
        }
    }

    /* ===== EMPLOYEE CARD (modal pilih karyawan) ===== */
    .emp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        padding: .5rem 0;
    }

    .emp-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .06);
        border-radius: 20px;
        padding: 1.25rem;
        position: relative;
        transition: all .35s cubic-bezier(.165, .84, .44, 1);
        cursor: pointer;
        user-select: none;
    }

    .emp-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 32px rgba(108, 92, 231, .1);
        border-color: #6c5ce7;
    }

    .emp-card.selected {
        background: #f8f7ff;
        border: 2px solid #6c5ce7;
    }

    .check-indicator {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 22px;
        height: 22px;
        border: 2px solid #e9ecef;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .3s;
    }

    .emp-card.selected .check-indicator {
        background: #6c5ce7;
        border-color: #6c5ce7;
    }

    .emp-card.selected .check-indicator::after {
        content: "\F012C";
        font-family: "Material Design Icons";
        color: white;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .avatar-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
        margin-right: .85rem;
        box-shadow: 0 6px 12px rgba(108, 92, 231, .2);
        flex-shrink: 0;
    }

    .name-main {
        font-size: .9rem;
        font-weight: 700;
        color: #2d3436;
        line-height: 1.2;
    }

    .emp-id {
        font-size: .7rem;
        color: #adb5bd;
        font-weight: 500;
    }

    .label-caps {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #adb5bd;
        font-weight: 800;
        margin-bottom: 6px;
        display: block;
        margin-top: .75rem;
    }

    .modern-card.empty-root {
        border-style: dashed;
        border-color: #ced4da;
        background: #f8f9fa;
        min-width: 160px;
        max-width: 200px;
    }

    .karyawan-item {
        border-bottom: 1px solid var(--bs-border-color);
        transition: background-color .12s ease;
        cursor: pointer;
        user-select: none;
    }
    .karyawan-item:last-child { border-bottom: none; }
    .karyawan-item:hover { background-color: rgba(var(--bs-primary-rgb), .05); }
    .karyawan-item.selected { background-color: rgba(var(--bs-primary-rgb), .08); }
    .karyawan-item .check-icon { transition: opacity .12s ease; }
    .karyawan-avatar {
        width: 36px; height: 36px; flex-shrink: 0;
        border-radius: 50%;
        background: rgba(var(--bs-primary-rgb), .12);
        color: var(--bs-primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700;
    }
</style>
@endsection

@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y approval-flow-page">
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Master Approval Flow</h5>
                    <button class="btn btn-sm btn-primary" id="btn_add_flow">
                        <i class="mdi mdi-plus me-1"></i>Tambah Flow
                    </button>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="flowTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-flow">
                                <i class="mdi mdi-sitemap me-1"></i>Alur & Langkah Approval
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-dependency">
                                <i class="mdi mdi-git me-1"></i>Dependency Rules
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-validator">
                                <i class="mdi mdi-shield-check me-1"></i>Master Validator
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-assignment">
                                <i class="mdi mdi-account-tie me-1"></i>Master Assignment
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">

                        <div class="tab-pane active show" id="tab-flow">
                            <div class="position-relative" style="min-height:200px">
                                <div id="tableLoading" class="table-loading d-none">
                                    <div class="loading-content">
                                        <div class="spinner"></div>
                                        <span>Memuat data...</span>
                                    </div>
                                </div>
                                <div id="flow-container"></div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab-dependency">
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-sm btn-primary" id="btn_add_dependency">
                                    <i class="mdi mdi-plus me-1"></i>Tambah Dependency
                                </button>
                            </div>
                            <table class="table table-sm" id="tbl_dependency" style="width:100%;font-size:.85rem">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Source Sub-Menu</th>
                                        <th>Target Sub-Menu</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="tab-validator">
                            <div class="stat-strip" id="vld-stats">
                                <div class="stat-card sc-purple">
                                    <div class="sc-icon"><i class="mdi mdi-shield-account"></i></div>
                                    <div>
                                        <div class="sc-val" id="vld-total">—</div>
                                        <div class="sc-label">Total Validator</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-blue">
                                    <div class="sc-icon"><i class="mdi mdi-cog-box"></i></div>
                                    <div>
                                        <div class="sc-val" id="vld-epicor">—</div>
                                        <div class="sc-label">Epicor ERP</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-green">
                                    <div class="sc-icon"><i class="mdi mdi-chart-pie"></i></div>
                                    <div>
                                        <div class="sc-val" id="vld-accurate">—</div>
                                        <div class="sc-label">Accurate SP</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-green">
                                    <div class="sc-icon"><i class="mdi mdi-domain"></i></div>
                                    <div>
                                        <div class="sc-val" id="vld-accurate-aja">—</div>
                                        <div class="sc-label">Accurate AJA</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-amber">
                                    <div class="sc-icon"><i class="mdi mdi-check-circle"></i></div>
                                    <div>
                                        <div class="sc-val" id="vld-active">—</div>
                                        <div class="sc-label">Aktif</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="filter-pill-bar mb-0">
                                    <span class="filter-pill active" data-vld-filter="">Semua</span>
                                    <span class="filter-pill" data-vld-filter="1">
                                        <i class="mdi mdi-microchip me-1"></i>Epicor
                                    </span>
                                    <span class="filter-pill" data-vld-filter="2">
                                        <i class="mdi mdi-chart-pie me-1"></i>Accurate SP
                                    </span>
                                    <span class="filter-pill" data-vld-filter="3">
                                        <i class="mdi mdi-domain me-1"></i>Accurate AJA
                                    </span>
                                </div>
                                <button class="btn btn-sm btn-primary" id="btn_add_validator">
                                    <i class="mdi mdi-plus me-1"></i>Tambah Validator
                                </button>
                            </div>

                            <table class="table tbl-modern" id="tbl_validator" style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Karyawan</th>
                                        <th>Environment</th>
                                        <th>Departemen</th>
                                        <th>Jabatan</th>
                                        <th width="80">Status</th>
                                        <th width="80">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="tab-assignment">
                            <div class="stat-strip" id="asgn-stats">
                                <div class="stat-card sc-purple">
                                    <div class="sc-icon"><i class="mdi mdi-account-tie"></i></div>
                                    <div>
                                        <div class="sc-val" id="asgn-total">—</div>
                                        <div class="sc-label">Total Assignment</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-blue">
                                    <div class="sc-icon"><i class="mdi mdi-account-star"></i></div>
                                    <div>
                                        <div class="sc-val" id="asgn-a01">—</div>
                                        <div class="sc-label">Assignor (A01)</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-green">
                                    <div class="sc-icon"><i class="mdi mdi-account-check"></i></div>
                                    <div>
                                        <div class="sc-val" id="asgn-a02">—</div>
                                        <div class="sc-label">PIC (A02)</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-amber">
                                    <div class="sc-icon"><i class="mdi mdi-check-circle"></i></div>
                                    <div>
                                        <div class="sc-val" id="asgn-active">—</div>
                                        <div class="sc-label">Aktif</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Toolbar -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="filter-pill-bar mb-0">
                                    <span class="filter-pill active" data-asgn-filter="">Semua</span>
                                    <span class="filter-pill" data-asgn-filter="A01">
                                        <i class="mdi mdi-account-star me-1"></i>Assignor
                                    </span>
                                    <span class="filter-pill" data-asgn-filter="A02">
                                        <i class="mdi mdi-account-check me-1"></i>PIC
                                    </span>
                                </div>
                                <button class="btn btn-sm btn-primary" id="btn_add_assignment">
                                    <i class="mdi mdi-plus me-1"></i>Tambah Assignment
                                </button>
                            </div>

                            <table class="table tbl-modern" id="tbl_assignment" style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Karyawan</th>
                                        <th width="120">Tipe</th>
                                        <th>Environment</th>
                                        <th>Holding / Departemen</th>
                                        <th>Jabatan</th>
                                        <th width="80">Status</th>
                                        <th width="80">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_flow" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_flow_title">Tambah Group Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="flow_id_edit">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Group Approval <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="flow_nama" placeholder="Contoh: Approval Revisi Harga PO">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control" id="flow_deskripsi" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Sub-Menu yang Menggunakan Group Approval Ini <span class="text-danger">*</span></label>
                        <select class="form-control" id="flow_sub_menu_ids" multiple>
                        </select>
                        <small class="text-muted">Pilih satu atau lebih sub-menu</small>
                    </div>
                    <div class="col-12" id="flow_active_wrap" style="display:none">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flow_is_active" checked>
                            <label class="form-check-label" for="flow_is_active">Group Approval Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-primary" id="btn_save_flow">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_add_step" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="form_add_step" class="modal-content">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modal_step_title">Tambah Step</h5>
                    <small class="text-muted" id="modal_step_subtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="step_id_edit">
                <input type="hidden" id="step_flow_id">
                <input type="hidden" id="step_parent_id">

                <div class="card mb-0">
                    <div class="card-body">

                        <!-- {{-- Info parent step --}} -->
                        <div id="parent_step_info" class="alert alert-light border mb-3 d-none">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-arrow-up-thick text-primary fs-5"></i>
                                <div>
                                    <div class="fw-bold" style="font-size:13px">Atasan Langsung</div>
                                    <div class="text-muted" style="font-size:12px" id="parent_step_name_info">—</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12" id="wrapper_root_empty" style="display:none">
                                <div class="alert alert-light border d-flex align-items-start gap-2 mb-0">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" id="step_is_root_empty">
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-semibold mb-0" for="step_is_root_empty">
                                            Root Kosong (General Flow)
                                        </label>
                                        <div class="text-muted" style="font-size:11px">
                                            Aktifkan jika step ini hanya sebagai penanda root tanpa departemen/jabatan spesifik —
                                            seluruh step di bawahnya (Manager per departemen) akan langsung lanjut ke Validator.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Step <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="step_nama" placeholder="Contoh: Approval SPV Purchasing">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Holding <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="step_holding_id" name="holding_id">
                                    <option value="">Pilih Holding...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" id="step_departemen_id" name="departemen_id">
                                    <option value="">Pilih Departemen...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Level Jabatan <span class="text-danger">*</span></label>
                                <select class="form-control" id="step_jabatan_id">
                                    <option value="">Pilih Level Jabatan...</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-start gap-2 mb-0">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" id="step_is_checker" name="is_checker">
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-semibold mb-0" for="step_is_checker">
                                            Jadikan Checker untuk flow ini
                                        </label>
                                        <div class="text-muted" style="font-size:11px">
                                            Dipakai sebagai pemeriksa saat tiket di-generate dari dependency. Hanya 1 step per flow.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Batal
                </button>
                <button type="button" id="btn_save_step" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_edit_step" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="form_edit_step" class="modal-content">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Edit Step</h5>
                    <small class="text-muted" id="edit_step_flow_label"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_step_id">
                <input type="hidden" id="edit_step_flow_id">

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Step <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_step_nama">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Atasan Langsung (Parent Step)</label>
                                <select class="form-control" id="edit_step_parent_id">
                                    <option value="">-- Sebagai Step Pertama (Root / Tidak Ada Atasan) --</option>
                                </select>
                                <small class="text-muted" style="font-size:11px">Ubah atasan langsung untuk memindahkan posisi step ini dalam hierarki persetujuan.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Holding <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_step_holding_id">
                                    <option value="">Pilih Holding...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_step_departemen_id">
                                    <option value="">Pilih Departemen...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Level Jabatan <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_step_jabatan_id">
                                    <option value="">Pilih Level Jabatan...</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-start gap-2 mb-0">
                                    <div class="form-check form-switch mt-1">
                                        <input class="form-check-input" type="checkbox" id="step_is_checker" name="is_checker">
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-semibold mb-0" for="step_is_checker">
                                            Jadikan Checker untuk flow ini
                                        </label>
                                        <div class="text-muted" style="font-size:11px">
                                            Dipakai sebagai pemeriksa saat tiket di-generate dari dependency. Hanya 1 step per flow.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_update_step" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL GANTI PARENT STEP --}}
<div class="modal fade" id="modal_change_parent" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title mb-0 fw-semibold">
                        <i class="mdi mdi-sitemap me-1 text-primary"></i>
                        Ganti Atasan / Parent Step
                    </h5>
                    <small class="text-muted" id="change_parent_step_label"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="change_parent_step_id">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Atasan Baru (Parent Step) <span class="text-danger">*</span></label>
                    <select class="form-control" id="change_parent_target_id">
                        <option value="">-- Jadikan Step Pertama (Root / Tidak Ada Atasan) --</option>
                    </select>
                    <small class="text-muted d-block mt-1" style="font-size:11px">Step dan hierarki di bawahnya akan otomatis berpindah mengikuti atasan baru.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_save_change_parent" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_pilih_karyawan" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title mb-0 fw-semibold">
                        <i class="mdi mdi-account-multiple me-1 text-primary"></i>
                        Pilih Karyawan
                    </h5>
                    <small class="text-muted" id="modal_karyawan_label"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                <div class="px-3 pt-3 pb-2 bg-white border-bottom sticky-top" style="z-index:5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="mdi mdi-magnify text-muted"></i>
                        </span>
                        <input type="text" id="search_karyawan"
                               class="form-control bg-light border-start-0 ps-0"
                               placeholder="Cari nama, divisi, atau bagian...">
                    </div>
                </div>

                <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center sticky-top" style="top:52px;z-index:4">
                    <div class="form-check m-0">
                        <input type="checkbox" class="form-check-input" id="check_all_karyawan">
                        <label class="form-check-label small text-muted" for="check_all_karyawan">
                            Pilih semua yang ditampilkan
                        </label>
                    </div>
                    <span id="selected-count"
                          class="badge bg-primary-subtle text-primary ms-auto d-none">
                    </span>
                </div>

                <div id="karyawan-list" style="max-height: 420px; overflow-y: auto;"></div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Tutup
                </button>
                <button type="button" id="btn_save_karyawan" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_dependency" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_dep_title">Tambah Dependency Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="dep_id_edit">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Source Sub-Menu <span class="text-danger">*</span></label>
                        <select class="form-control select2-dep" id="dep_source" data-placeholder="Pilih source..."></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Target Sub-Menu <span class="text-danger">*</span></label>
                        <select class="form-control select2-dep" id="dep_target" data-placeholder="Pilih target..."></select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Sifat Dependency / Condition Key</label>
                        <select class="form-control" id="dep_condition_key" data-placeholder="Pilih kondisi..."></select>
                        <small class="text-muted d-block mt-1">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Pilih <strong>Mandatori (Unconditional)</strong> jika alur ini wajib selalu dieksekusi (seperti <em>Delivery Order → AR Invoice</em>). Pilih <strong>Kode Kondisi</strong> jika alur ini opsional dan tergantung pilihan user (seperti <em>Delivery Order → Purchase Order</em>).
                        </small>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch bg-light p-3 rounded border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="dep_is_bidirectional" value="1">
                            <label class="form-check-label fw-semibold text-dark mb-0" for="dep_is_bidirectional" style="cursor:pointer;">
                                Izinkan Pengecekan Alur Mundur / Revisi (Bidirectional Flow)
                            </label>
                            <small class="d-block text-muted mt-1" style="font-size:11px;">
                                Aktifkan jika cabang ini juga dapat dipicu secara opsional saat requestor memilih opsi <em>"Apakah transaksi sebelumnya perlu direvisi?"</em>.
                            </small>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" class="form-control" id="dep_description" placeholder="Deskripsi singkat dependency ini">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-sm btn-primary" id="btn_save_dependency">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_validator" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_validator_title">Tambah Master Validator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="validator_id_edit">

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- {{-- Environment --}} -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Environment <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="validator_environment">
                                    <option value="">Pilih Environment...</option>
                                    <option value="1">Epicor ERP</option>
                                    <option value="2">Accurate Online SP</option>
                                    <option value="3">Accurate Online AJA</option>
                                </select>
                            </div>

                            <!-- {{-- Holding (opsional, ikuti pola step) --}} -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Holding</label>
                                <select class="form-control" id="validator_holding_id">
                                    <option value="">Pilih Holding...</option>
                                </select>
                            </div>

                            <!-- {{-- Departemen --}} -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Departemen <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="validator_departemen_id">
                                    <option value="">Pilih Departemen...</option>
                                </select>
                            </div>

                            <!-- {{-- Divisi --}} -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Divisi</label>
                                <select class="form-control" id="validator_divisi_id">
                                    <option value="">Pilih Divisi...</option>
                                </select>
                            </div>

                            <!-- {{-- Bagian --}} -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bagian</label>
                                <select class="form-control" id="validator_bagian_id">
                                    <option value="">Pilih Bagian...</option>
                                </select>
                            </div>

                            <!-- {{-- Jabatan --}} -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Jabatan <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="validator_jabatan_id">
                                    <option value="">Pilih Jabatan...</option>
                                </select>
                            </div>

                            <!-- {{-- Karyawan --}} -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Karyawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="validator_karyawan_id">
                                    <option value="">Pilih Karyawan...</option>
                                </select>
                                <small class="text-muted">Daftar karyawan akan muncul setelah Jabatan dipilih.</small>
                            </div>

                            <!-- {{-- Status Aktif (hanya muncul saat edit) --}} -->
                            <div class="col-12" id="validator_active_wrap" style="display:none">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="validator_is_active" checked>
                                    <label class="form-check-label" for="validator_is_active">Validator Aktif</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Batal
                </button>
                <button class="btn btn-sm btn-primary" id="btn_save_validator">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal tambah assignment master -->
<div class="modal fade" id="modal_assignment" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_assignment_title">Tambah Master Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="assignment_id_edit">

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="row g-3">

                            <!-- Tipe Assignment -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tipe Assignment <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="assignment_type">
                                    <option value="">Pilih Tipe...</option>
                                    <option value="A01">A01 — Assignor</option>
                                    <option value="A02">A02 — PIC</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Environment</label>
                                <select class="form-control" id="assignment_environment">
                                    <option value="">Semua Environment</option>
                                    <option value="1">Epicor ERP</option>
                                    <option value="2">Accurate Online SP</option>
                                    <option value="3">Accurate Online AJA</option>
                                </select>
                            </div>

                            <!-- Holding -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Holding</label>
                                <select class="form-control" id="assignment_holding_id">
                                    <option value="">Pilih Holding...</option>
                                </select>
                            </div>

                            <!-- Departemen -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Departemen <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="assignment_departemen_id">
                                    <option value="">Pilih Departemen...</option>
                                </select>
                            </div>

                            <!-- Divisi -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Divisi</label>
                                <select class="form-control" id="assignment_divisi_id">
                                    <option value="">Pilih Divisi...</option>
                                </select>
                            </div>

                            <!-- Bagian -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bagian</label>
                                <select class="form-control" id="assignment_bagian_id">
                                    <option value="">Pilih Bagian...</option>
                                </select>
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Jabatan <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="assignment_jabatan_id">
                                    <option value="">Pilih Jabatan...</option>
                                </select>
                            </div>

                            <!-- Karyawan -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Karyawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="assignment_karyawan_id">
                                    <option value="">Pilih Karyawan...</option>
                                </select>
                                <small class="text-muted">Daftar karyawan akan muncul setelah Jabatan dipilih.</small>
                            </div>

                            <!-- Status Aktif (hanya saat edit) -->
                            <div class="col-12" id="assignment_active_wrap" style="display:none">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="assignment_is_active" checked>
                                    <label class="form-check-label" for="assignment_is_active">Assignment Aktif</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Batal
                </button>
                <button class="btn btn-sm btn-primary" id="btn_save_assignment">
                    <i class="mdi mdi-content-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/js/jquery.orgchart.min.js"></script>

<script>
    $(document).ready(function() {
        const BASE = "{{ url('master_ticket') }}";
        const TOKEN = "{{ csrf_token() }}";
        let activeStepId = null;

        const showLoading = () => $('#tableLoading').removeClass('d-none');
        const hideLoading = () => $('#tableLoading').addClass('d-none');

        function swalLoading() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                showCancelButton: false,
                showConfirmButton: false
            });
        }

        $('#flow_sub_menu_ids').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih sub-menu...',
            allowClear: true,
            dropdownParent: $('#modal_flow')
        });
        $('#step_holding_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Holding...',
            allowClear: true,
            dropdownParent: $('#modal_add_step')
        });
        $('#step_departemen_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Departemen...',
            allowClear: true,
            dropdownParent: $('#modal_add_step')
        });
        $('#step_jabatan_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Level Jabatan...',
            allowClear: true,
            dropdownParent: $('#modal_add_step')
        });
        $('#edit_step_parent_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Parent Step...',
            allowClear: true,
            dropdownParent: $('#modal_edit_step')
        });
        $('#edit_step_holding_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Holding...',
            allowClear: true,
            dropdownParent: $('#modal_edit_step')
        });
        $('#edit_step_departemen_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Departemen...',
            allowClear: true,
            dropdownParent: $('#modal_edit_step')
        });
        $('#edit_step_jabatan_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Level Jabatan...',
            allowClear: true,
            dropdownParent: $('#modal_edit_step')
        });
        $('#change_parent_target_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Parent Step...',
            allowClear: true,
            dropdownParent: $('#modal_change_parent')
        });
        $('#dep_source, #dep_target').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            dropdownParent: $('#modal_dependency')
        });
        $('#dep_condition_key').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih kondisi...',
            allowClear: true,
            dropdownParent: $('#modal_dependency')
        });
        $('#dep_checker_dept').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Departemen...',
            allowClear: true,
            dropdownParent: $('#modal_dependency')
        });
        $('#dep_checker_jabatan').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Jabatan...',
            allowClear: true,
            dropdownParent: $('#modal_dependency')
        });

        /* ─────────────────────────────────────────────────────────────────────
           LOAD TREE VIEW
        ───────────────────────────────────────────────────────────────────── */
        function loadTreeview() {
            showLoading();
            $.ajax({
                url: "{{ route('ticket.approval_flow.datatable') }}",
                method: 'GET',
                success: function(res) {
                    hideLoading();
                    $('#flow-container').empty();

                    if (!res.data || res.data.length === 0) {
                        $('#flow-container').html(`
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-sitemap" style="font-size:3rem;opacity:.3"></i>
                                <p class="mt-2 mb-3">Belum ada flow approval. Klik tombol <strong>Tambah Flow</strong>.</p>
                            </div>
                        `);
                        return;
                    }

                    res.data.forEach(function(flow) {
                        window.allFlowSteps = window.allFlowSteps || {};
                        window.allFlowSteps[flow.id] = flow.steps || [];

                        const chartId = 'chart-flow-' + flow.id;
                        const hasSteps = flow.steps && flow.steps.length > 0;
                        const menuBadges = (flow.menus || []).map(m =>
                            `<span class="badge bg-label-primary me-1" style="font-size:11px">${m}</span>`
                        ).join('');
                        const activeBadge = flow.is_active ?
                            '<span class="badge bg-success ms-2" style="font-size:10px">Aktif</span>' :
                            '<span class="badge bg-secondary ms-2" style="font-size:10px">Nonaktif</span>';

                        $('#flow-container').append(`
                            <div class="mb-5 flow-block" data-flow-id="${flow.id}">
                                <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">${flow.nama_flow} ${activeBadge}</h6>
                                        <div class="d-flex flex-wrap gap-1">${menuBadges}</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary btn_add_root_step"
                                            data-flow="${flow.id}" data-flow-name="${flow.nama_flow}"
                                            style="font-size:12px">
                                            <i class="mdi mdi-plus me-1"></i>Tambah Step Pertama
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning btn_edit_flow"
                                            data-id="${flow.id}" data-nama="${flow.nama_flow}"
                                            data-desc="${flow.description||''}" data-active="${flow.is_active}"
                                            style="font-size:12px">
                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn_delete_flow"
                                            data-id="${flow.id}" style="font-size:12px">
                                            <i class="mdi mdi-trash-can me-1"></i>Hapus
                                        </button>
                                    </div>
                                </div>
                                <div id="${chartId}" class="org-wrapper"></div>
                            </div>
                        `);

                        if (!hasSteps) {
                            $(`#${chartId}`).html(`
                                <div class="text-center text-muted py-4" style="font-size:13px">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Belum ada step. Klik <strong>"Tambah Step Pertama"</strong> di atas.
                                </div>
                            `);
                            return;
                        }

                        const treeData = buildOrgTree(flow.steps);

                        $(`#${chartId}`).orgchart({
                            data: treeData,
                            nodeContent: 'title',
                            pan: true,
                            zoom: true,
                            createNode: function($node, data) {
                                const isEmptyRoot = !data.department_id && !data.divisi_id && !data.bagian_id && !data.jabatan_id && !data.id?.startsWith('virtual-');

                                // ===== RENDER CARD KHUSUS UNTUK ROOT KOSONG (GENERAL FLOW) =====
                                if (isEmptyRoot) {
                                    const emptyHtml = `
                                        <div class="modern-card empty-root" data-step-id="${data.id}" data-flow-id="${data.flow_id}">
                                            <div class="card-header-gradient" style="background:linear-gradient(90deg,#adb5bd 0%,#ced4da 100%)"></div>
                                            <div class="card-body card-chart justify-content-center text-center" style="flex-direction:column">
                                                <div class="emp-name text-muted">${data.step_name || '— Root —'}</div>
                                                <div class="emp-title">General Flow</div>
                                                <span class="role-badge bg-light text-muted mt-1" style="border:1px dashed #ced4da">
                                                    <i class="mdi mdi-information-outline" style="font-size:11px"></i> Auto-skip ke Validator
                                                </span>
                                            </div>
                                            <div class="card-actions">
                                                <button class="action-btn btn-add btn-add-bawahan"
                                                    title="Tambah Step Bawahan"
                                                    data-step-id="${data.id}"
                                                    data-step-name="${data.step_name}"
                                                    data-flow-id="${data.flow_id}"
                                                    data-flow-name="${data.flow_name}">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                                <button class="action-btn btn-parent btn-change-parent"
                                                    title="Ganti Atasan / Parent Step"
                                                    data-step-id="${data.id}"
                                                    data-step-name="${data.step_name || ''}"
                                                    data-flow-id="${data.flow_id}"
                                                    data-flow-name="${data.flow_name || ''}"
                                                    data-parent-step-id="${data.parent_step_id || ''}">
                                                    <i class="mdi mdi-sitemap"></i>
                                                </button>
                                                <button class="action-btn btn-edit btn-edit-step"
                                                    title="Edit Step"
                                                    data-step-id="${data.id}"
                                                    data-flow-id="${data.flow_id}"
                                                    data-flow-name="${data.flow_name || ''}"
                                                    data-step-name="${data.step_name || ''}"
                                                    data-holding-id="${data.holding_id || ''}"
                                                    data-departemen-id="${data.department_id || ''}"
                                                    data-jabatan-id="${data.jabatan_level_id || ''}"
                                                    data-is-checker="${data.is_checker ? 1 : 0}"
                                                    data-parent-step-id="${data.parent_step_id || ''}">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button class="action-btn btn-delete btn-delete-step"
                                                    title="Hapus Step"
                                                    data-step-id="${data.id}"
                                                    data-flow-id="${data.flow_id}">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                    $node.html(emptyHtml);
                                    return;
                                }

                                // ===== RENDER CARD NORMAL (sama seperti sebelumnya) =====
                                const roleClass = data.role_type || 'approver';
                                const isFinal = data.is_final ? 'final' : '';

                                const titleParts = [];
                                if (data.nama_departemen && data.nama_departemen !== 'null') titleParts.push(data.nama_departemen);
                                if (data.nama_divisi && data.nama_divisi !== 'null') titleParts.push(data.nama_divisi);
                                const empTitle = titleParts.length > 0 ? titleParts.join(' - ') : '—';

                                const roleBadgeMap = {
                                    approver: '<span class="role-badge badge-approver"><i class="mdi mdi-check-circle" style="font-size:11px"></i> Approver</span>',
                                    checker: '<span class="role-badge badge-checker"><i class="mdi mdi-eye" style="font-size:11px"></i> Checker</span>',
                                    validator: '<span class="role-badge badge-validator"><i class="mdi mdi-shield-check" style="font-size:11px"></i> Validator</span>',
                                };

                                const finalBadge = data.is_final ?
                                    '<span class="role-badge badge-final ms-1"><i class="mdi mdi-flag-checkered" style="font-size:11px"></i> Final</span>' :
                                    '';

                                const typeBadge = data.type === 'parallel' ?
                                    '<span class="badge bg-info ms-1" style="font-size:10px">Parallel</span>' :
                                    '';

                                const slaHtml = data.sla_hours ?
                                    `<div style="font-size:10px;color:#9055fd;margin-top:3px">
                                        <i class="mdi mdi-timer" style="font-size:11px"></i>
                                        SLA ${data.sla_hours} jam
                                    </div>` :
                                    '';

                                const customHtml = `
                                    <div class="modern-card ${roleClass} ${isFinal}"
                                        data-step-id="${data.id}"
                                        data-flow-id="${data.flow_id}">
                                        <div class="card-header-gradient"></div>

                                        <div class="card-body card-chart">
                                            <div class="avatar-wrapper">
                                                <img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="avatar-img">
                                            </div>
                                            <div class="info-wrapper">
                                                <div class="emp-name">${data.step_name || '—'}</div>
                                                <div class="emp-title">${empTitle}</div>
                                                <div>
                                                    ${roleBadgeMap[roleClass] || ''}
                                                    ${data.is_checker ? roleBadgeMap.checker : ''}
                                                    ${finalBadge}
                                                    ${typeBadge}
                                                </div>
                                                ${slaHtml}
                                            </div>
                                        </div>

                                       <div class="node-user-footer">
                                            <i class="mdi mdi-account-group" style="font-size:13px"></i>
                                            <span class="user-count-val">${data.user_count ?? 0}</span>
                                            karyawan
                                            ${(data.user_count ?? 0) > 0
                                                ? `<span class="badge bg-success ms-1" style="font-size:9px">Assigned</span>`
                                                : `<span class="badge bg-warning text-dark ms-1" style="font-size:9px">Belum ada</span>`
                                            }
                                        </div>

                                        <div class="card-actions">
                                            <button class="action-btn btn-add btn-add-bawahan"
                                                title="Tambah Step Bawahan"
                                                data-step-id="${data.id}"
                                                data-step-name="${data.step_name}"
                                                data-flow-id="${data.flow_id}"
                                                data-flow-name="${data.flow_name}">
                                                <i class="mdi mdi-plus"></i>
                                            </button>
                                            <button class="action-btn btn-users btn-manage-users"
                                                title="Kelola Karyawan"
                                                data-step-id="${data.id}"
                                                data-step-name="${data.step_name}"
                                                data-flow-id="${data.flow_id}"
                                                data-flow-name="${data.flow_name}">
                                                <i class="mdi mdi-account-multiple"></i>
                                            </button>
                                            <button class="action-btn btn-parent btn-change-parent"
                                                title="Ganti Atasan / Parent Step"
                                                data-step-id="${data.id}"
                                                data-step-name="${data.step_name || ''}"
                                                data-flow-id="${data.flow_id}"
                                                data-flow-name="${data.flow_name || ''}"
                                                data-parent-step-id="${data.parent_step_id || ''}">
                                                <i class="mdi mdi-sitemap"></i>
                                            </button>
                                            <button class="action-btn btn-edit btn-edit-step"
                                                title="Edit Step"
                                                data-step-id="${data.id}"
                                                data-flow-id="${data.flow_id}"
                                                data-flow-name="${data.flow_name}"
                                                data-step-name="${data.step_name || ''}"
                                                data-holding-id="${data.holding_id || ''}"
                                                data-departemen-id="${data.department_id || ''}"
                                                data-divisi-id="${data.divisi_id || ''}"
                                                data-bagian-id="${data.bagian_id || ''}"
                                                data-jabatan="${data.jabatan_id || ''}"
                                                data-role="${data.role_type || 'approver'}"
                                                data-type="${data.type || 'sequential'}"
                                                data-is-final="${data.is_final ? 1 : 0}"
                                                data-is-checker="${data.is_checker ? 1 : 0}"
                                                data-parent-step-id="${data.parent_step_id || ''}">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button class="action-btn btn-delete btn-delete-step"
                                                title="Hapus Step"
                                                data-step-id="${data.id}"
                                                data-flow-id="${data.flow_id}">
                                                <i class="mdi mdi-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                `;

                                $node.html(customHtml);
                            }
                        });
                    });
                },
                error: function() {
                    hideLoading();
                    Swal.fire('Error', 'Gagal memuat data approval flow', 'error');
                }
            });
        }

        function buildOrgTree(steps) {
            if (!steps || steps.length === 0) return null;

            const map = {};
            steps.forEach(function(s) {
                map[s.id] = {
                id: s.id,
                flow_id: s.flow_id,
                flow_name: s.flow_name || '',
                name: s.step_name || '—',
                title: s.jabatan_name || '—',
                step_name: s.step_name || '—',
                nama_departemen: s.nama_departemen || null,
                nama_divisi: s.nama_divisi || null,
                holding_id: s.holding_id || null,
                department_id: s.department_id || null,
                divisi_id: s.divisi_id || null,
                bagian_id: s.bagian_id || null,
                jabatan_id: s.jabatan_id || null,
                jabatan_level_id: s.jabatan_level_id || null,  // ← WAJIB ADA
                role_type: s.role_type || 'approver',
                type: s.type || 'sequential',
                is_final: Boolean(s.is_final),
                is_checker: Boolean(s.is_checker),
                sla_hours: s.sla_hours || null,
                user_count: s.user_count || 0,
                condition: s.condition || null,
                children: []
                };
            });

            const roots = [];
            steps.forEach(function(s) {
                if (!s.parent_step_id) {
                    roots.push(map[s.id]);
                } else if (map[s.parent_step_id]) {
                    map[s.parent_step_id].children.push(map[s.id]);
                }
            });

            if (roots.length === 1) return roots[0];
            if (roots.length > 1) return {
                id: 'virtual-root',
                name: '.',
                title: '',
                children: roots
            };
            return null;
        }

        /* ─────────────────────────────────────────────────────────────────────
           TAMBAH FLOW
        ───────────────────────────────────────────────────────────────────── */
        $('#btn_add_flow').on('click', function() {
            $('#modal_flow_title').text('Tambah Flow');
            $('#flow_id_edit, #flow_nama, #flow_deskripsi').val('');
            $('#flow_sub_menu_ids').val(null).trigger('change');
            $('#flow_active_wrap').hide();
            loadSubMenuOptions();
            $('#modal_flow').modal('show');
        });

        function loadSubMenuOptions(selectedIds = []) {
            $.ajax({
                url: BASE + '/approval-flow/submenu_list',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data || response || [];
                    const $select = $('#flow_sub_menu_ids');
                    selectedIds = (selectedIds || []).map(String);
                    $select.empty();
                    data.forEach(function(sm) {
                        const label = sm.text || `${sm.nama_sub_menu} (${sm.nama_menu})`;
                        const option = new Option(label, sm.id, false, selectedIds.includes(String(sm.id)));
                        $select.append(option);
                    });
                    $select.trigger('change');
                },
                error: function(xhr) {
                    console.error('Gagal memuat submenu flow:', xhr.responseText);
                }
            });
        }

        $('#btn_save_flow').on('click', function() {
            const id = $('#flow_id_edit').val();
            const nama = $('#flow_nama').val().trim();
            const ids = $('#flow_sub_menu_ids').val();
            if (!nama || !ids || ids.length === 0) {
                Swal.fire('Perhatian', 'Nama flow dan sub-menu wajib diisi.', 'warning');
                return;
            }
            swalLoading();
            $.ajax({
                url: id ? `${BASE}/approval-flow/update/${id}` : `${BASE}/approval-flow/store`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    _method: id ? 'PUT' : 'POST',
                    nama_flow: nama,
                    deskripsi: $('#flow_deskripsi').val(),
                    sub_menu_ids: ids,
                    is_active: $('#flow_is_active').is(':checked') ? 1 : 0,
                },
                success: function(res) {
                    Swal.close();
                    if (res.code === 200) {
                        $('#modal_flow').modal('hide');
                        loadTreeview();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 3000
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: e => {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           EDIT FLOW
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_edit_flow', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const desc = $(this).data('desc');
            const active = $(this).data('active');
            $('#modal_flow_title').text('Edit Flow');
            $('#flow_id_edit').val(id);
            $('#flow_nama').val(nama);
            $('#flow_deskripsi').val(desc);
            $('#flow_is_active').prop('checked', active == 1);
            $('#flow_active_wrap').show();
            loadSubMenuOptions();
            $('#modal_flow').modal('show');
        });

        /* ─────────────────────────────────────────────────────────────────────
           DELETE FLOW
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_delete_flow', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Flow?',
                text: 'Semua step akan ikut terhapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea5455'
            }).then((result) => {
                if (!result.isConfirmed) return;
                swalLoading();
                $.ajax({
                    url: `${BASE}/approval-flow/delete/${id}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        Swal.close();
                        if (res.code === 200) loadTreeview();
                        Swal.fire({
                            icon: res.code === 200 ? 'success' : 'error',
                            title: res.code === 200 ? 'Berhasil!' : 'Gagal!',
                            text: res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           TAMBAH STEP PERTAMA
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_add_root_step', function() {
            const flowId = $(this).data('flow');
            const flowName = $(this).data('flow-name');
            openAddStepModal(flowId, flowName, null, null);
        });

        /* ─────────────────────────────────────────────────────────────────────
           TAMBAH BAWAHAN
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn-add-bawahan', function(e) {
            e.stopPropagation();
            const stepId = $(this).data('step-id');
            const stepName = $(this).data('step-name');
            const flowId = $(this).data('flow-id');
            const flowName = $(this).data('flow-name');
            openAddStepModal(flowId, flowName, stepId, stepName);
        });

        function openAddStepModal(flowId, flowName, parentStepId, parentStepName) {
            $('#step_flow_id').val(flowId);
            $('#step_parent_id').val(parentStepId || '');
            $('#step_id_edit').val('');
            $('#step_nama').val('');
            $('#step_is_root_empty').prop('checked', false);
            $('#modal_add_step #step_is_checker').prop('checked', false);

            $('#step_holding_id').val(null).trigger('change.select2');
            $('#step_departemen_id')
                .empty().append('<option value="">Pilih Departemen...</option>')
                .trigger('change.select2');

            // Load jabatan langsung — tidak bergantung departemen
            loadJabatanOptions('#step_jabatan_id');

            if (parentStepId) {
                $('#modal_step_title').text('Tambah Step Bawahan');
                $('#modal_step_subtitle').text('Flow: ' + flowName);
                $('#parent_step_info').removeClass('d-none');
                $('#parent_step_name_info').text(parentStepName);
                $('#wrapper_root_empty').hide();
            } else {
                $('#modal_step_title').text('Tambah Step Pertama');
                $('#modal_step_subtitle').text('Flow: ' + flowName);
                $('#parent_step_info').addClass('d-none');
                $('#wrapper_root_empty').show();
            }

            toggleStepFieldsRequirement();
            loadHoldingOptions('#step_holding_id');
            $('#modal_add_step').modal('show');
        }

        function toggleStepFieldsRequirement() {
            const isEmpty = $('#step_is_root_empty').is(':checked');
            $('#step_nama').prop('disabled', false); // nama tetap wajib (untuk label "—")

            if (isEmpty) {
                $('#step_holding_id, #step_departemen_id, #step_divisi_id, #step_bagian_id, #step_jabatan_id')
                    .val(null).trigger('change.select2').prop('disabled', true);
                $('#step_role_type, #step_type, #step_is_final')
                    .closest('.col-md-6, .col-12').hide();
            } else {
                $('#step_holding_id, #step_departemen_id, #step_divisi_id, #step_bagian_id, #step_jabatan_id')
                    .prop('disabled', false);
                $('#step_role_type, #step_type, #step_is_final')
                    .closest('.col-md-6, .col-12').show();
            }
        }

        $('#step_is_root_empty').on('change', toggleStepFieldsRequirement);

        $('#btn_save_step').on('click', function() {
            const flowId = $('#step_flow_id').val();
            const parentId = $('#step_parent_id').val();
            const nama = $('#step_nama').val().trim();
            const isRootEmpty = $('#step_is_root_empty').is(':checked');

            if (!nama) {
                Swal.fire('Perhatian', 'Nama step wajib diisi.', 'warning');
                return;
            }

            let payload = {
                _token: TOKEN,
                flow_id: flowId,
                parent_step_id: parentId || null,
                nama_step: nama,
                is_checker: $('#modal_add_step #step_is_checker').is(':checked') ? 1 : 0,
            };

            if (isRootEmpty) {
                // Root kosong — semua field organisasi null, role tidak relevan
                payload = {
                    ...payload,
                    holding_id: null,
                    departemen_id: null,
                    divisi_id: null,
                    bagian_id: null,
                    jabatan_id: null,
                    role_type: 'approver',
                    type: 'sequential',
                    is_final: 0,
                };
            } else {
                const jabatan = $('#step_jabatan_id').val();
                const dept = $('#step_departemen_id').val();

                if (!jabatan || !dept || dept.length === 0) {
                    Swal.fire('Perhatian', 'Departemen dan level jabatan wajib diisi.', 'warning');
                    return;
                }

                payload = {
                    ...payload,
                    holding_id: $('#step_holding_id').val(),
                    departemen_id: dept,
                    jabatan_id: jabatan,
                };
            }

            swalLoading();
            $.post(`${BASE}/approval-flow/step/store`, payload, function(res) {
                Swal.close();
                $('#modal_add_step').modal('hide');
                if (res.code === 200) {
                    loadTreeview();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 3000 });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }).fail(e => {
                Swal.close();
                Swal.fire('Error', e.responseJSON?.message, 'error');
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           PARENT STEP HELPERS (Hierarki Step & Cegah Circular Reference)
        ───────────────────────────────────────────────────────────────────── */
        function getDescendantIds(steps, targetId) {
            targetId = String(targetId);
            const descendants = new Set();
            const queue = [targetId];
            while (queue.length > 0) {
                const current = queue.shift();
                (steps || []).forEach(s => {
                    if (String(s.parent_step_id) === current && !descendants.has(String(s.id))) {
                        descendants.add(String(s.id));
                        queue.push(String(s.id));
                    }
                });
            }
            return descendants;
        }

        function populateParentOptions(selectId, steps, currentStepId, selectedParentId) {
            const $select = $(selectId);
            $select.empty().append('<option value="">-- Root (Paling Atas / Tanpa Atasan) --</option>');
            const blocked = getDescendantIds(steps, currentStepId);
            blocked.add(String(currentStepId));

            (steps || []).forEach(s => {
                const sId = String(s.id);
                if (blocked.has(sId)) return;
                const label = `${s.step_name || 'Step #' + s.id} (${s.nama_departemen || s.department_name || '-'} - ${s.nama_jabatan || s.jabatan_name || '-'})`;
                const isSelected = String(selectedParentId || '') === sId;
                $select.append(new Option(label, s.id, false, isSelected));
            });
            $select.trigger('change.select2');
        }

        /* ─────────────────────────────────────────────────────────────────────
           GANTI PARENT STEP LANGSUNG DARI CARD
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn-change-parent', function(e) {
            e.stopPropagation();
            const $btn = $(this);
            const stepId = $btn.data('step-id');
            const stepName = $btn.data('step-name') || ('Step #' + stepId);
            const flowId = $btn.data('flow-id');
            const flowName = $btn.data('flow-name') || '';
            const parentStepId = $btn.data('parent-step-id') || '';

            $('#change_parent_step_id').val(stepId);
            $('#change_parent_step_label').text(`${stepName} (${flowName})`);

            const flowSteps = (window.allFlowSteps && window.allFlowSteps[flowId]) ? window.allFlowSteps[flowId] : [];
            populateParentOptions('#change_parent_target_id', flowSteps, stepId, parentStepId);

            $('#modal_change_parent').modal('show');
        });

        $('#btn_save_change_parent').on('click', function() {
            const stepId = $('#change_parent_step_id').val();
            const newParentId = $('#change_parent_target_id').val();

            swalLoading();
            $.ajax({
                url: `${BASE}/approval-flow/step/change-parent/${stepId}`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    parent_step_id: newParentId || null
                },
                success: function(res) {
                    if (res.code === 200) {
                        $('#modal_change_parent').modal('hide');
                        loadTreeview();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Parent step berhasil diubah',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function(e) {
                    Swal.fire('Error', e.responseJSON?.message || 'Gagal mengubah parent step', 'error');
                }
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           EDIT STEP — buka modal dan isi semua dropdown secara berantai
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn-edit-step', function(e) {
            e.stopPropagation();

            const $btn = $(this);
            const v = {
                stepId      : $btn.data('step-id'),
                flowId      : $btn.data('flow-id'),
                flowName    : $btn.data('flow-name') || '',
                stepName    : $btn.data('step-name') || '',
                holdingId   : String($btn.data('holding-id') || ''),
                departemenId: String($btn.data('departemen-id') || ''),
                jabatanId   : String($btn.data('jabatan-id') || ''),
                parentStepId: String($btn.data('parent-step-id') || ''),
                isChecker   : Boolean($btn.data('is-checker')),
            };

            // Isi field statis
            $('#edit_step_id').val(v.stepId);
            $('#edit_step_flow_id').val(v.flowId);
            $('#edit_step_flow_label').text('Flow: ' + v.flowName);
            $('#edit_step_nama').val(v.stepName);
            $('#modal_edit_step #step_is_checker').prop('checked', v.isChecker);

            // Populate pilihan parent step (kecualikan node ini dan keturunannya)
            const flowSteps = (window.allFlowSteps && window.allFlowSteps[v.flowId]) ? window.allFlowSteps[v.flowId] : [];
            populateParentOptions('#edit_step_parent_id', flowSteps, v.stepId, v.parentStepId);

            // Reset semua dropdown dulu
            $('#edit_step_holding_id')
                .empty().append('<option value="">Pilih Holding...</option>')
                .trigger('change.select2');
            $('#edit_step_departemen_id')
                .empty().append('<option value="">Pilih Departemen...</option>')
                .trigger('change.select2');
            $('#edit_step_jabatan_id')
                .empty().append('<option value="">Pilih Level Jabatan...</option>')
                .trigger('change.select2');

            $('#modal_edit_step').modal('show');

            // Step 1: Load Holding dengan callback
            loadHoldingOptions('#edit_step_holding_id', v.holdingId, function() {

                // Step 2: Load Departemen dengan callback
                loadDepartemenOptions('#edit_step_departemen_id', v.holdingId, v.departemenId, function() {

                    // Step 3: Load Jabatan dengan selected value
                    loadJabatanOptions('#edit_step_jabatan_id', v.jabatanId);
                });
            });
        });

        // Change handler holding di modal edit
        $('#edit_step_holding_id').on('change', function() {
            if ($(this).data('silent-change')) return;
            const holdingId = $(this).val();
            loadDepartemenOptions('#edit_step_departemen_id', holdingId);
        });

        // Change handler departemen di modal edit  
        $('#edit_step_departemen_id').on('change', function() {
            // Jabatan tidak cascade dari departemen, tapi reset saja kalau perlu
            // Biarkan jabatan tetap karena loadJabatanOptions ambil semua
        });

        // Change handler departemen di modal ADD (perbaiki yang duplikat)
        $('#step_departemen_id').off('change').on('change', function() {});

        /* ─────────────────────────────────────────────────────────────────────
           UPDATE STEP
        ───────────────────────────────────────────────────────────────────── */
        $('#btn_update_step').on('click', function() {
            const id         = $('#edit_step_id').val();
            const flowId     = $('#edit_step_flow_id').val();
            const nama       = $('#edit_step_nama').val().trim();
            const parentStepId = $('#edit_step_parent_id').val();
            const holdingId  = $('#edit_step_holding_id').val();
            const deptId     = $('#edit_step_departemen_id').val();
            const jabatanId  = $('#edit_step_jabatan_id').val();
            const isChecker  = $('#modal_edit_step #step_is_checker').is(':checked') ? 1 : 0;

            if (!nama) {
                Swal.fire('Perhatian', 'Nama step wajib diisi.', 'warning');
                return;
            }
            if (!deptId || !jabatanId) {
                Swal.fire('Perhatian', 'Departemen dan level jabatan wajib diisi.', 'warning');
                return;
            }

            swalLoading();
            $.ajax({
                url  : `${BASE}/approval-flow/step/update/${id}`,
                type : 'POST',
                data : {
                    _token       : TOKEN,
                    _method      : 'POST',
                    flow_id      : flowId,
                    nama_step    : nama,
                    parent_step_id: parentStepId,
                    holding_id   : holdingId,
                    departemen_id: deptId,
                    jabatan_id   : jabatanId,
                    is_checker   : isChecker,
                },
                success: function(res) {
                    Swal.close();
                    if (res.code === 200) {
                        $('#modal_edit_step').modal('hide');
                        loadTreeview();
                        Swal.fire({
                            icon : 'success',
                            title: 'Berhasil!',
                            text : res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: e => {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           DELETE STEP
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn-delete-step', function(e) {
            e.stopPropagation();
            const stepId = $(this).data('step-id');
            Swal.fire({
                title: 'Hapus Step?',
                text: 'Step berikutnya (bawahan) juga akan terhapus secara cascade.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea5455'
            }).then(r => {
                if (!r.isConfirmed) return;
                swalLoading();
                $.ajax({
                    url: `${BASE}/approval-flow/step/delete/${stepId}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        Swal.close();
                        loadTreeview();
                        Swal.fire({
                            icon: res.code === 200 ? 'success' : 'error',
                            title: res.code === 200 ? 'Berhasil!' : 'Gagal!',
                            text: res.message,
                            timer: 3000
                        });
                    },
                    error: e => {
                        Swal.close();
                        Swal.fire('Error', e.responseJSON?.message, 'error');
                    }
                });
            });
        });

        let allKaryawan = [];
        let checkedSet  = new Set();

        $(document).on('click', '.btn-manage-users', function (e) {
            e.stopPropagation();
            activeStepId = $(this).data('step-id');
            $('#modal_karyawan_label').text(`Step ${$(this).data('step-name')}`);
            $('#search_karyawan').val('');
            $('#modal_pilih_karyawan').modal('show');
            loadKaryawan(activeStepId);
        });

        function loadKaryawan(stepId) {
            $('#karyawan-list').html(`
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div>Memuat data...
                </div>`);
            checkedSet.clear();
            updateSelectedCount();

            $.ajax({
                url: `${BASE}/approval-flow/karyawan/${stepId}`,
                type: 'GET',
                success: function (res) {
                    allKaryawan = res.data || [];
                    allKaryawan.forEach(k => {
                        if (k.checked) checkedSet.add(String(k.id));
                    });
                    renderKaryawan(allKaryawan);
                },
                error: function () {
                    $('#karyawan-list').html(
                        '<div class="text-center py-5 text-danger">Gagal memuat data karyawan.</div>'
                    );
                }
            });
        }

        function renderKaryawan(list) {
            if (!list.length) {
                $('#karyawan-list').html(
                    '<div class="text-center py-5 text-muted">Tidak ada karyawan yang tersedia.</div>'
                );
                syncCheckAll();
                return;
            }

            const html = list.map(k => {
                const sid       = String(k.id);
                const isChecked = checkedSet.has(sid);
                const initial   = (k.nama || '?').charAt(0).toUpperCase();
                return `
                <label class="karyawan-item d-flex align-items-center gap-3 px-3 py-2 m-0 w-100
                            ${isChecked ? 'selected' : ''}">
                    <input type="checkbox"
                        class="form-check-input flex-shrink-0 mt-0"
                        name="karyawan_ids[]"
                        value="${sid}"
                        ${isChecked ? 'checked' : ''}>
                    <div class="karyawan-avatar">${initial}</div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:13px">${k.nama}</div>
                        <div class="text-muted text-truncate" style="font-size:11px">
                            ${k.divisi} &middot; ${k.bagian} &middot; ${k.jabatan}
                        </div>
                    </div>
                    <i class="mdi mdi-check-circle text-primary check-icon flex-shrink-0"
                    style="font-size:18px;opacity:${isChecked ? 1 : 0}"></i>
                </label>`;
            }).join('');

            $('#karyawan-list').html(html);
            syncCheckAll();
            updateSelectedCount();
        }

        $(document).on('input', '#search_karyawan', function () {
            const q = $(this).val().toLowerCase().trim();
            const filtered = q
                ? allKaryawan.filter(k =>
                    k.nama.toLowerCase().includes(q) ||
                    k.divisi.toLowerCase().includes(q) ||
                    k.bagian.toLowerCase().includes(q))
                : allKaryawan;
            renderKaryawan(filtered);
        });

        $(document).on('change', '#karyawan-list input[name="karyawan_ids[]"]', function () {
            const sid     = String($(this).val());
            const checked = $(this).is(':checked');
            const $item   = $(this).closest('.karyawan-item');

            if (checked) checkedSet.add(sid);
            else          checkedSet.delete(sid);

            $item.toggleClass('selected', checked);
            $item.find('.check-icon').css('opacity', checked ? 1 : 0);

            syncCheckAll();
            updateSelectedCount();
        });

        $(document).on('change', '#check_all_karyawan', function () {
            const checked = $(this).is(':checked');
            $('#karyawan-list input[name="karyawan_ids[]"]').each(function () {
                const sid = String($(this).val());
                $(this).prop('checked', checked);
                $(this).closest('.karyawan-item').toggleClass('selected', checked);
                $(this).closest('.karyawan-item').find('.check-icon').css('opacity', checked ? 1 : 0);
                if (checked) checkedSet.add(sid);
                else          checkedSet.delete(sid);
            });
            updateSelectedCount();
        });

        function syncCheckAll() {
            const $visible  = $('#karyawan-list input[name="karyawan_ids[]"]');
            const total     = $visible.length;
            const checkedN  = $visible.filter(':checked').length;
            $('#check_all_karyawan')
                .prop('checked',       total > 0 && checkedN === total)
                .prop('indeterminate', checkedN > 0 && checkedN < total);
        }

        function updateSelectedCount() {
            const count = checkedSet.size;
            $('#selected-count').toggleClass('d-none', count === 0).text(`${count} Terpilih`);
        }

        $('#btn_save_karyawan').on('click', function () {
            const $btn = $(this);
            const ids  = [...checkedSet];

            // if (!ids.length) {
            //     Swal.fire('Perhatian', 'Pilih minimal 1 karyawan', 'warning');
            //     return;
            // }
            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...');

            $.ajax({
                url: `${BASE}/approval-flow/save-user`,
                type: 'POST',
                data: { _token: TOKEN, step_id: activeStepId, karyawan_ids: ids },
                success: function (res) {
                    $('#modal_pilih_karyawan').modal('hide');
                   if (res.code === 200) {
                    $(`.modern-card[data-step-id="${activeStepId}"] .user-count-val`)
                        .text(checkedSet.size);
                    loadTreeview();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Simpan');
                }
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           DROPDOWN LOADERS  (semua mendukung callback untuk chaining)
        ───────────────────────────────────────────────────────────────────── */

        /**
         * Load Holding — tidak perlu cascade otomatis di sini,
         * cukup isi dropdown dengan nilai terpilih.
         */
        function loadHoldingOptions(selectId, selectedId = null, callback = null) {
            $.ajax({
                url: `${BASE}/approval-flow/get-holding`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const $select = $(selectId);
                    $select.empty().append('<option value="">Pilih Holding...</option>');
                    data.forEach(function(item) {
                        const option = new Option(
                            item.text,
                            item.id,
                            false,
                            String(item.id) === String(selectedId)
                        );
                        $select.append(option);
                    });
                    $select.data('silent-change', true);
                    if (selectedId) {
                        $select.val(selectedId).trigger('change.select2');
                    } else {
                        $select.val('').trigger('change.select2');
                    }
                    $select.data('silent-change', false);
                    if (typeof callback === 'function') callback();
                },
                error: function(xhr) {
                    console.error('Gagal memuat data holding:', xhr.responseText);
                    if (typeof callback === 'function') callback();
                }
            });
        }

        /**
         * Load Departemen berdasarkan holdingId.
         * @param {string}   selectId   — selector target
         * @param {string}   holdingId  — parent holding
         * @param {string}   selectedId — nilai yang dipra-pilih
         * @param {Function} callback   — dipanggil setelah options terisi (opsional)
         */
        function loadDepartemenOptions(selectId, holdingId, selectedId = null, callback = null) {
            const $select = $(selectId);
            $select.empty().append('<option value="">Pilih Departemen...</option>');

            if (!holdingId) {
                $select.trigger('change.select2');
                if (typeof callback === 'function') callback();
                return;
            }

            $.ajax({
                url: `${BASE}/approval-flow/get-departemen`,
                type: 'GET',
                data: { holding: holdingId },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        const option = new Option(
                            item.text,
                            item.id,
                            false,
                            String(item.id) === String(selectedId)
                        );
                        $select.append(option);
                    });
                    $select.data('silent-change', true);
                    if (selectedId) {
                        $select.val(selectedId).trigger('change.select2');
                    } else {
                        $select.val('').trigger('change.select2');
                    }
                    $select.data('silent-change', false);
                    if (typeof callback === 'function') callback();
                },
                error: function(xhr) {
                    console.error('Gagal memuat departemen:', xhr.responseText);
                    if (typeof callback === 'function') callback();
                }
            });
        }

        function loadDivisiOptions(selectId, departemenId, selectedId = null, callback = null) {
            const $select = $(selectId);
            $select.empty().append('<option value="">Pilih Divisi...</option>');

            if (!departemenId) {
                $select.trigger('change.select2');
                if (typeof callback === 'function') callback();
                return;
            }

            $.ajax({
                url: `${BASE}/approval-flow/get-divisi`,
                type: 'GET',
                data: { departemen: departemenId },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false,
                            String(item.id) === String(selectedId)));
                    });
                    if (selectedId) $select.val(selectedId).trigger('change.select2');
                    else            $select.trigger('change.select2');
                    if (typeof callback === 'function') callback();
                },
                error: function() {
                    if (typeof callback === 'function') callback();
                }
            });
        }

        function loadBagianOptions(selectId, divisiId, selectedId = null, callback = null) {
            const $select = $(selectId);
            $select.empty().append('<option value="">Pilih Bagian...</option>');

            if (!divisiId) {
                $select.trigger('change.select2');
                if (typeof callback === 'function') callback();
                return;
            }

            $.ajax({
                url: `${BASE}/approval-flow/get-bagian`,
                type: 'GET',
                data: { divisi: divisiId },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false,
                            String(item.id) === String(selectedId)));
                    });
                    if (selectedId) $select.val(selectedId).trigger('change.select2');
                    else            $select.trigger('change.select2');
                    if (typeof callback === 'function') callback();
                },
                error: function() {
                    if (typeof callback === 'function') callback();
                }
            });
        }

        function loadJabatanByBagianOptions(selectId, bagianId, selectedId = null, callback = null) {
            const $select = $(selectId);
            $select.empty().append('<option value="">Pilih Jabatan...</option>');

            if (!bagianId) {
                $select.trigger('change.select2');
                if (typeof callback === 'function') callback();
                return;
            }

            $.ajax({
                url: `${BASE}/approval-flow/get-jabatan`,
                type: 'GET',
                data: { bagian: bagianId },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false,
                            String(item.id) === String(selectedId)));
                    });
                    if (selectedId) $select.val(selectedId).trigger('change.select2');
                    else            $select.trigger('change.select2');
                    if (typeof callback === 'function') callback();
                },
                error: function() {
                    if (typeof callback === 'function') callback();
                }
            });
        }

        /**
         * Load Jabatan berdasarkan bagianId.
         */
        function loadJabatanOptions(selectId, selectedId = null, callback = null) {
            const $select = $(selectId);
            $select.empty().append('<option value="">Pilih Level Jabatan...</option>');

            $.ajax({
                url: `${BASE}/approval-flow/get-jabatan-level`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        const option = new Option(
                            item.text,
                            item.id,
                            false,
                            String(item.id) === String(selectedId)
                        );
                        $select.append(option);
                    });
                    if (selectedId) {
                        $select.val(selectedId).trigger('change.select2');
                    } else {
                        $select.trigger('change.select2');
                    }
                    if (typeof callback === 'function') callback();
                },
                error: function(xhr) {
                    console.error('Gagal memuat level jabatan:', xhr.responseText);
                    if (typeof callback === 'function') callback();
                }
            });
        }

        // Inisialisasi holding untuk modal Add Step
        loadHoldingOptions('#step_holding_id');

        // Change handlers — Add Step modal
        $('#step_holding_id').on('change', function() {
            const holdingId = $(this).val();
            loadDepartemenOptions('#step_departemen_id', holdingId);
        });

        $('#step_departemen_id').on('change', function() {
            const departemenId = $(this).val();
            loadJabatanOptions('#step_jabatan_id', departemenId);
        });

        /* ─────────────────────────────────────────────────────────────────────
           DEPENDENCY DATATABLE
        ───────────────────────────────────────────────────────────────────── */
        const tblDep = $('#tbl_dependency').DataTable({
            processing: true,
            serverSide: true,
            ajax: `${BASE}/dependency/datatable`,
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                    orderable: false
                },
                {
                    data: 'source_sub_menu',
                    orderable: false
                },
                {
                    data: 'target_sub_menu',
                    orderable: false
                },
                {
                    data: 'kondisi',
                    orderable: false
                },
                {
                    data: 'is_active',
                    render: v => v ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>'
                },
                {
                    data: 'action',
                    orderable: false
                },
            ],
        });

        $('a[href="#tab-dependency"]').on('shown.bs.tab', () => tblDep.columns.adjust().draw());

        /* ─────────────────────────────────────────────────────────────────────
           DEPENDENCY CRUD
        ───────────────────────────────────────────────────────────────────── */
        // btn_add_dependency
        $('#btn_add_dependency').on('click', function() {
            $('#modal_dep_title').text('Tambah Dependency Rule');
            $('#dep_id_edit, #dep_description').val('');
            $('#dep_is_bidirectional').prop('checked', false);
            loadDepSubMenuOptions();
            loadDepConditionOptions();
            $('#modal_dependency').modal('show');
            $('#btn_save_dependency').off('click').on('click', saveDependency);
        });

        // btn_edit_dependency
        $(document).on('click', '.btn_edit_dependency', function() {
            const id = $(this).data('id');
            $('#modal_dep_title').text('Edit Dependency Rule');
            $('#dep_id_edit').val(id);
            loadDepSubMenuOptions($(this).data('source-id'), $(this).data('target-id'));
            loadDepConditionOptions($(this).data('condition'));
            $('#dep_is_bidirectional').prop('checked', $(this).data('bidirectional') == 1);
            $('#dep_description').val($(this).data('description') || '');
            $('#modal_dependency').modal('show');
            $('#btn_save_dependency').off('click').on('click', saveDependency);
        });

        $(document).on('click', '.btn_delete_dependency', function() {
            const id = $(this).data('id');
            Swal.fire({
                    title: 'Hapus rule?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                })
                .then(r => {
                    if (!r.isConfirmed) return;
                    swalLoading();
                    $.ajax({
                        url: `${BASE}/dependency/delete/${id}`,
                        type: 'POST',
                        data: {
                            _token: TOKEN,
                            _method: 'DELETE'
                        },
                        success: function(res) {
                            Swal.close();
                            tblDep.ajax.reload();
                            Swal.fire({
                                icon: res.code === 200 ? 'success' : 'error',
                                title: res.message,
                                timer: 3000
                            });
                        },
                        error: e => {
                            Swal.close();
                            Swal.fire('Error', e.responseJSON?.message, 'error');
                        }
                    });
                });
        });

        function saveDependency() {
            const id = $('#dep_id_edit').val();
            const source = $('#dep_source').val();
            const target = $('#dep_target').val();
            const cond = $('#dep_condition_key').val() ? $('#dep_condition_key').val().trim() : '';
            const isBidirectional = $('#dep_is_bidirectional').is(':checked') ? 1 : 0;
            if (!source || !target) {
                Swal.fire('Perhatian', 'Source dan target sub-menu wajib diisi.', 'warning');
                return;
            }
            swalLoading();
            $.post(id ? `${BASE}/dependency/update/${id}` : `${BASE}/dependency/store`, {
                _token: TOKEN,
                _method: id ? 'PUT' : 'POST',
                source_sub_menu_id: source,
                target_sub_menu_id: target,
                condition_key: cond,
                is_bidirectional: isBidirectional,
                description: $('#dep_description').val(),
            }, function(res) {
                Swal.close();
                $('#modal_dependency').modal('hide');
                if (res.code === 200) {
                    tblDep.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 3000
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }).fail(e => {
                Swal.close();
                Swal.fire('Error', e.responseJSON?.message, 'error');
            });
        }

        function loadDepConditionOptions(selCond = null) {
            $.ajax({
                url: BASE + '/dependency/kondisi_list',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data || [];
                    const $select = $('#dep_condition_key');
                    $select.empty().append('<option value="">-- Mandatori (Wajib Eksekusi / Unconditional) --</option>');
                    data.forEach(function(item) {
                        const isSelected = String(item.code) === String(selCond) ? 'selected' : '';
                        $select.append(`<option value="${item.code}" ${isSelected}>Kondisional: ${item.code} - ${item.label_kondisi}</option>`);
                    });
                }
            });
        }

        function loadDepSubMenuOptions(selSource = null, selTarget = null) {
            $.ajax({
                url: BASE + '/approval-flow/submenu_list',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data || response || [];
                    [{
                        selector: '#dep_source',
                        selected: selSource
                    }, {
                        selector: '#dep_target',
                        selected: selTarget
                    }]
                    .forEach(function(item) {
                        const $select = $(item.selector);
                        $select.empty().append('<option value="">-- Pilih Sub-Menu --</option>');
                        data.forEach(function(sm) {
                            const label = sm.text || `${sm.nama_sub_menu} (${sm.nama_menu})`;
                            const isSelected = String(sm.id) === String(item.selected) ? 'selected' : '';
                            $select.append(`<option value="${sm.id}" ${isSelected}>${label}</option>`);
                        });
                        $select.trigger('change');
                    });
                },
                error: function(xhr) {
                    console.error('Gagal memuat submenu dependency:', xhr.responseText);
                }
            });
        }

        // 
        function loadKondisiOptions(selectedCode = null) {
            $.ajax({
                url: `${BASE}/kondisi/list`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data || response || [];
                    const $select = $('#dep_condition_key');
                    $select.empty().append('<option value=""></option>');
                    data.forEach(function(item) {
                        const label = item.label_kondisi ?
                            `${item.label_kondisi} (${item.code})` :
                            item.code;
                        const option = new Option(label, item.code, false, String(item.code) === String(selectedCode));
                        $select.append(option);
                    });
                    $select.trigger('change');
                },
                error: function(xhr) {
                    console.error('Gagal memuat kondisi:', xhr.responseText);
                }
            });
        }

        $('#dep_checker_dept').on('change', function() {
            const v = $(this).val();
            if (v) loadDepJabatanOptions(v);
            else $('#dep_checker_jabatan').empty().append('<option value="">-- Pilih Jabatan --</option>');
        });

        function loadDepJabatanOptions(deptId, selJabatan = null) {
            $.get(`${BASE}/approval-flow/jabatan/${deptId}`, function(data) {
                $('#dep_checker_jabatan').empty().append('<option value="">-- Pilih Jabatan --</option>');
                data.forEach(function(j) {
                    const s = String(j.id) === String(selJabatan) ? 'selected' : '';
                    $('#dep_checker_jabatan').append(`<option value="${j.id}" ${s}>${j.nama_jabatan}</option>`);
                });
            });
        }

        /* ─────────────────────────────────────────────────────────────────────
           SELECT2 — MODAL VALIDATOR
        ───────────────────────────────────────────────────────────────────── */
        ['#validator_environment', '#validator_holding_id', '#validator_departemen_id',
            '#validator_divisi_id', '#validator_bagian_id', '#validator_jabatan_id',
            '#validator_karyawan_id'
        ].forEach(function(sel) {
            $(sel).select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#modal_validator')
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           DATATABLE MASTER VALIDATOR
        ───────────────────────────────────────────────────────────────────── */
        let tblValidator = null;

        function initValidatorTable() {
            if ($.fn.DataTable.isDataTable('#tbl_validator')) {
                tblValidator.ajax.reload();
                return;
            }
            tblValidator = $('#tbl_validator').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${BASE}/validator/datatable`,
                    dataSrc: function(json) {
                        // Update stat cards
                        $('#vld-total').text(json.recordsTotal ?? '—');
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                        orderable: false
                    },
                    {
                        data: 'nama_karyawan', orderable: false,
                        render: function(data, type, row) {
                            const initial = (data || '?').charAt(0).toUpperCase();
                            const dept  = row.nama_departemen ?? '—';
                            const divisi = row.nama_divisi ? ' · ' + row.nama_divisi : '';
                            return `
                                <div class="karyawan-cell">
                                    <div class="karyawan-avatar-sm">${initial}</div>
                                    <div>
                                        <div class="kc-name">${data ?? '—'}</div>
                                        <div class="kc-dept">${dept}${divisi}</div>
                                    </div>
                                </div>`;
                        }
                    },
                    {
                        data: 'environment', orderable: false,
                        render: function(data) {
                            if (data == 1) return '<span class="env-badge env-epicor"><i class="mdi mdi-microchip"></i> Epicor</span>';
                            if (data == 2) return '<span class="env-badge env-accurate-sp"><i class="mdi mdi-chart-pie"></i> Accurate SP</span>';
                            if (data == 3) return '<span class="env-badge env-accurate-aja"><i class="mdi mdi-domain"></i> Accurate AJA</span>';
                            return `<span class="badge bg-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: 'nama_departemen', orderable: false,
                        render: v => v ? `<span style="font-size:12.5px;font-weight:600">${v}</span>` : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'nama_jabatan', orderable: false,
                        render: v => v
                            ? `<span class="badge" style="background:#f5f3ff;color:#5b21b6;font-size:11px;font-weight:600">${v}</span>`
                            : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'is_active', orderable: false,
                        render: v => v
                            ? '<span class="badge bg-success" style="font-size:10px">Aktif</span>'
                            : '<span class="badge bg-secondary" style="font-size:10px">Nonaktif</span>'
                    },
                    {
                        data: 'action', orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="act-btn act-edit btn_edit_validator me-1"
                                    data-id="${row.id}"
                                    data-environment="${row.environment}"
                                    data-departemen-id="${row.departemen_id ?? ''}"
                                    data-divisi-id="${row.divisi_id ?? ''}"
                                    data-bagian-id="${row.bagian_id ?? ''}"
                                    data-jabatan-id="${row.jabatan_id ?? ''}"
                                    data-karyawan-id="${row.karyawan_id ?? ''}"
                                    data-is-active="${row.is_active ? 1 : 0}"
                                    title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="act-btn act-delete btn_delete_validator"
                                    data-id="${row.id}" title="Hapus">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>`;
                        }
                    }
                ],
                language: { emptyTable: 'Belum ada data validator.', processing: 'Memuat data...' }
            });
        }

        $('a[href="#tab-validator"]').on('shown.bs.tab', function() {
            if (tblValidator) {
                tblValidator.columns.adjust().draw();
            } else {
                initValidatorTable();
            }
        });

        /* ─────────────────────────────────────────────────────────────────────
           CASCADE DROPDOWN — MODAL VALIDATOR
        ───────────────────────────────────────────────────────────────────── */
        $('#validator_holding_id').on('change', function() {
            const holdingId = $(this).val();
            loadDepartemenOptions('#validator_departemen_id', holdingId);
            $('#validator_divisi_id').empty().append('<option value="">Pilih Divisi...</option>').trigger('change.select2');
            $('#validator_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#validator_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#validator_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });

        $('#validator_departemen_id').on('change', function() {
            const departemenId = $(this).val();
            loadDivisiOptions('#validator_divisi_id', departemenId);
            $('#validator_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#validator_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#validator_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });

        $('#validator_divisi_id').on('change', function() {
            const divisiId = $(this).val();
            loadBagianOptions('#validator_bagian_id', divisiId);
            $('#validator_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#validator_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });

        $('#validator_bagian_id').off('change').on('change', function () {
            const bagianId = $(this).val();
            loadJabatanByBagianOptions('#validator_jabatan_id', bagianId);
            $('#validator_karyawan_id')
                .empty().append('<option value="">Pilih Karyawan...</option>')
                .trigger('change.select2');
        });

        $('#validator_jabatan_id').on('change', function() {
            const jabatanId = $(this).val();
            loadValidatorUserOptions(jabatanId);
        });

        /**
         * Load daftar karyawan berdasarkan jabatanId.
         * Endpoint backend: GET /master_ticket/validator/get-users?jabatan=:id
         */
        function loadValidatorUserOptions(jabatanId, selectedId = null) {
            const $select = $('#validator_karyawan_id');
            $select.empty().append('<option value="">Pilih Karyawan...</option>');

            if (!jabatanId) {
                $select.trigger('change.select2');
                return;
            }

            $.ajax({
                url: `${BASE}/validator/get-users`,
                type: 'GET',
                data: {
                    jabatan: jabatanId
                },
                dataType: 'json',
                success: function(data) {
                    (data.data || data || []).forEach(function(item) {
                        const option = new Option(item.text, item.id, false, String(item.id) === String(selectedId));
                        $select.append(option);
                    });
                    $select.trigger('change.select2');
                },
                error: function(xhr) {
                    console.error('Gagal memuat karyawan validator:', xhr.responseText);
                }
            });
        }

        /* ─────────────────────────────────────────────────────────────────────
           TAMBAH VALIDATOR
        ───────────────────────────────────────────────────────────────────── */
        $('#btn_add_validator').on('click', function() {
            $('#modal_validator_title').text('Tambah Master Validator');
            $('#validator_id_edit').val('');
            $('#validator_environment').val(null).trigger('change');
            $('#validator_holding_id').val(null).trigger('change');
            $('#validator_departemen_id').empty().append('<option value="">Pilih Departemen...</option>').trigger('change.select2');
            $('#validator_divisi_id').empty().append('<option value="">Pilih Divisi...</option>').trigger('change.select2');
            $('#validator_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#validator_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#validator_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
            $('#validator_active_wrap').hide();

            loadHoldingOptions('#validator_holding_id');
            $('#modal_validator').modal('show');
        });

        /* ─────────────────────────────────────────────────────────────────────
           EDIT VALIDATOR
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_edit_validator', function() {
            const $btn = $(this);
            const v = {
                id: $btn.data('id'),
                environment: $btn.data('environment'),
                holdingId: String($btn.data('holding-id') || ''),
                departemenId: String($btn.data('departemen-id') || ''),
                divisiId: String($btn.data('divisi-id') || ''),
                bagianId: String($btn.data('bagian-id') || ''),
                jabatanId: String($btn.data('jabatan-id') || ''),
                karyawanId: String($btn.data('karyawan-id') || ''),
                isActive: parseInt($btn.data('is-active')) === 1,
            };

            $('#modal_validator_title').text('Edit Master Validator');
            $('#validator_id_edit').val(v.id);
            $('#validator_active_wrap').show();
            $('#validator_is_active').prop('checked', v.isActive);

            // Reset dulu
            $('#validator_departemen_id').empty().append('<option value="">Pilih Departemen...</option>').trigger('change.select2');
            $('#validator_divisi_id').empty().append('<option value="">Pilih Divisi...</option>').trigger('change.select2');
            $('#validator_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#validator_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#validator_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');

            $('#modal_validator').modal('show');

            // Set environment
            $('#validator_environment').val(v.environment).trigger('change');

            // Cascade chain: holding → dept → divisi → bagian → jabatan → user
            loadHoldingOptions('#validator_holding_id', v.holdingId);

            loadDepartemenOptions('#validator_departemen_id', v.holdingId, v.departemenId, function() {
                loadDivisiOptions('#validator_divisi_id', v.departemenId, v.divisiId, function() {
                    loadBagianOptions('#validator_bagian_id', v.divisiId, v.bagianId, function() {
                        loadJabatanByBagianOptions('#validator_jabatan_id', v.bagianId, v.jabatanId, function () {
                            loadValidatorUserOptions(v.jabatanId, v.karyawanId);
                        });
                    });
                });
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           SIMPAN VALIDATOR (create + update)
        ───────────────────────────────────────────────────────────────────── */
        $('#btn_save_validator').on('click', function() {
            const id = $('#validator_id_edit').val();
            const environment = $('#validator_environment').val();
            const holdingId = $('#validator_holding_id').val();
            const deptId = $('#validator_departemen_id').val();
            const divisiId = $('#validator_divisi_id').val();
            const bagianId = $('#validator_bagian_id').val();
            const jabatanId = $('#validator_jabatan_id').val();
            const karyawanId = $('#validator_karyawan_id').val();

            if (!environment || !deptId || !jabatanId || !karyawanId) {
                Swal.fire('Perhatian', 'Environment, departemen, jabatan, dan karyawan wajib diisi.', 'warning');
                return;
            }

            swalLoading();
            $.ajax({
                url: id ?
                    `${BASE}/validator/update/${id}` : `${BASE}/validator/store`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    _method: id ? 'PUT' : 'POST',
                    environment: environment,
                    holding_id: holdingId,
                    departemen_id: deptId,
                    divisi_id: divisiId,
                    bagian_id: bagianId,
                    jabatan_id: jabatanId,
                    karyawan_id: karyawanId,
                    is_active: $('#validator_is_active').is(':checked') ? 1 : 0,
                },
                success: function(res) {
                    Swal.close();
                    if (res.code === 200) {
                        $('#modal_validator').modal('hide');
                        tblValidator && tblValidator.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: e => {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           DELETE VALIDATOR
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_delete_validator', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Validator?',
                text: 'Data validator akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea5455'
            }).then(result => {
                if (!result.isConfirmed) return;
                swalLoading();
                $.ajax({
                    url: `${BASE}/validator/delete/${id}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        Swal.close();
                        tblValidator && tblValidator.ajax.reload(null, false);
                        Swal.fire({
                            icon: res.code === 200 ? 'success' : 'error',
                            title: res.code === 200 ? 'Berhasil!' : 'Gagal!',
                            text: res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: e => {
                        Swal.close();
                        Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           INIT
        ───────────────────────────────────────────────────────────────────── */
        loadTreeview();

        /* ─────────────────────────────────────────────────────────────────────
   SELECT2 — MODAL ASSIGNMENT
───────────────────────────────────────────────────────────────────── */
        ['#assignment_type', '#assignment_holding_id', '#assignment_departemen_id',
            '#assignment_divisi_id', '#assignment_bagian_id', '#assignment_jabatan_id',
            '#assignment_karyawan_id', '#assignment_environment'
        ].forEach(function(sel) {
            $(sel).select2({
                theme: 'bootstrap-5',
                allowClear: true,
                dropdownParent: $('#modal_assignment')
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           DATATABLE MASTER ASSIGNMENT
        ───────────────────────────────────────────────────────────────────── */
        let tblAssignment = null;

        function initAssignmentTable() {
            if ($.fn.DataTable.isDataTable('#tbl_assignment')) {
                tblAssignment.ajax.reload();
                return;
            }
            tblAssignment = $('#tbl_assignment').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${BASE}/assignment/datatable`,
                    dataSrc: function(json) {
                        $('#asgn-total').text(json.recordsTotal ?? '—');
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                        orderable: false
                    },
                    {
                        data: 'karyawan', orderable: false,
                        render: function(data) {
                            const initial = (data || '?').charAt(0).toUpperCase();
                            return `
                                <div class="karyawan-cell">
                                    <div class="karyawan-avatar-sm">${initial}</div>
                                    <div class="kc-name">${data ?? '—'}</div>
                                </div>`;
                        }
                    },
                    {
                        data: 'assignment_type', orderable: false,
                        render: function(data) {
                            if (data === 'A01') return '<span class="asgn-badge asgn-a01"><i class="mdi mdi-account-star"></i> Assignor</span>';
                            if (data === 'A02') return '<span class="asgn-badge asgn-a02"><i class="mdi mdi-account-check"></i> PIC</span>';
                            return `<span class="badge bg-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: 'environment',
                        orderable: false,
                        render: function(data) {
                            const env = data == null ? null : Number(data);
                            if (env === 1) {
                                return '<span class="badge bg-primary">Epicor ERP</span>';
                            }
                            if (env === 2) {
                                return '<span class="badge bg-info text-dark">Accurate Online SP</span>';
                            }
                            if (env === 3) {
                                return '<span class="badge bg-info text-dark">Accurate Online AJA</span>';
                            }
                            if (env === null) {
                                return '<span class="badge bg-secondary">Semua Environment</span>';
                            }
                            return '<span class="badge bg-warning text-dark">Tidak Dikenal</span>';
                        }
                    },
                    {
                        data: 'holding', orderable: false,
                        render: function(data, type, row) {
                            const departemen = row.departemen ? `<div style="font-size:10.5px;color:#9ca3af">${row.departemen}</div>` : '';
                            return `<div style="font-size:12.5px;font-weight:600">${data ?? '—'}</div>${departemen}`;
                        }
                    },
                    {
                        data: 'departemen', orderable: false,
                        render: function(data, type, row) {
                            const holding = row.holding ? `<div style="font-size:10.5px;color:#9ca3af">${row.holding}</div>` : '';
                            return `<div style="font-size:12.5px;font-weight:600">${data ?? '—'}</div>${holding}`;
                        }
                    },
                    {
                        data: 'jabatan', orderable: false,
                        render: v => v
                            ? `<span class="badge" style="background:#f5f3ff;color:#5b21b6;font-size:11px;font-weight:600">${v}</span>`
                            : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'is_active', orderable: false,
                        render: v => v
                            ? '<span class="badge bg-success" style="font-size:10px">Aktif</span>'
                            : '<span class="badge bg-secondary" style="font-size:10px">Nonaktif</span>'
                    },
                    {
                        data: 'action', orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="act-btn act-edit btn_edit_assignment me-1"
                                    data-id="${row.id}"
                                    data-type="${row.assignment_type}"
                                    data-environment="${row.environment ?? ''}"
                                    data-holding-id="${row.holding_id ?? ''}"
                                    data-departemen-id="${row.departemen_id ?? ''}"
                                    data-divisi-id="${row.divisi_id ?? ''}"
                                    data-bagian-id="${row.bagian_id ?? ''}"
                                    data-jabatan-id="${row.jabatan_id ?? ''}"
                                    data-karyawan-id="${row.karyawan_id ?? ''}"
                                    data-is-active="${row.is_active ? 1 : 0}"
                                    title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="act-btn act-delete btn_delete_assignment"
                                    data-id="${row.id}" title="Hapus">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>`;
                        }
                    }
                ],
                language: { emptyTable: 'Belum ada data assignment.', processing: 'Memuat data...' }
            });
        }

        // Muat tabel saat tab diklik pertama kali
        $('a[href="#tab-assignment"]').on('shown.bs.tab', function() {
            if (tblAssignment) {
                tblAssignment.columns.adjust().draw();
            } else {
                initAssignmentTable();
            }
        });

        // ── Filter pill — Validator ──────────────────────────────────────
        $(document).on('click', '[data-vld-filter]', function () {
            $('[data-vld-filter]').removeClass('active');
            $(this).addClass('active');
            const env = $(this).data('vld-filter');
            if (tblValidator) {
                tblValidator.ajax.url(`${BASE}/validator/datatable${env ? '?env=' + env : ''}`).load();
            }
        });

        // ── Filter pill — Assignment ─────────────────────────────────────
        $(document).on('click', '[data-asgn-filter]', function () {
            $('[data-asgn-filter]').removeClass('active');
            $(this).addClass('active');
            const type = $(this).data('asgn-filter');
            if (tblAssignment) {
                tblAssignment.ajax.url(`${BASE}/assignment/datatable${type ? '?type=' + type : ''}`).load();
            }
        });

        // ── Update stat cards setelah reload ────────────────────────────
        function refreshValidatorStats() {
            $.get(`${BASE}/validator/datatable?stats=1`, function(res) {
                $('#vld-epicor').text(res.epicor ?? '—');
                $('#vld-accurate').text(res.accurate ?? '—');
                $('#vld-accurate-aja').text(res.accurate_aja ?? '—');
                $('#vld-active').text(res.active ?? '—');
            });
        }

        function refreshAssignmentStats() {
            $.get(`${BASE}/assignment/datatable?stats=1`, function(res) {
                $('#asgn-a01').text(res.a01 ?? '—');
                $('#asgn-a02').text(res.a02 ?? '—');
                $('#asgn-active').text(res.active ?? '—');
            });
        }

        // Panggil saat tab dibuka
        $('a[href="#tab-validator"]').on('shown.bs.tab', function () {
            if (!tblValidator) initValidatorTable();
            else tblValidator.columns.adjust().draw();
            refreshValidatorStats();
        });

        $('a[href="#tab-assignment"]').on('shown.bs.tab', function () {
            if (!tblAssignment) initAssignmentTable();
            else tblAssignment.columns.adjust().draw();
            refreshAssignmentStats();
        });

        /* ─────────────────────────────────────────────────────────────────────
           CASCADE DROPDOWN — MODAL ASSIGNMENT
        ───────────────────────────────────────────────────────────────────── */
        $('#assignment_holding_id').on('change', function() {
            const holdingId = $(this).val();
            loadDepartemenOptions('#assignment_departemen_id', holdingId);
            $('#assignment_divisi_id').empty().append('<option value="">Pilih Divisi...</option>').trigger('change.select2');
            $('#assignment_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#assignment_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#assignment_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });

        $('#assignment_departemen_id').on('change', function() {
            const departemenId = $(this).val();
            loadDivisiOptions('#assignment_divisi_id', departemenId);
            $('#assignment_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#assignment_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#assignment_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });

        $('#assignment_divisi_id').on('change', function() {
            const divisiId = $(this).val();
            loadBagianOptions('#assignment_bagian_id', divisiId);
            $('#assignment_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#assignment_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });

        $('#assignment_bagian_id').off('change').on('change', function () {
            const bagianId = $(this).val();
            loadJabatanByBagianOptions('#assignment_jabatan_id', bagianId);
            $('#assignment_karyawan_id')
                .empty().append('<option value="">Pilih Karyawan...</option>')
                .trigger('change.select2');
        });

        $('#assignment_jabatan_id').on('change', function() {
            loadAssignmentUserOptions($(this).val());
        });

        function loadAssignmentUserOptions(jabatanId, selectedId = null) {
            const $select = $('#assignment_karyawan_id');
            $select.empty().append('<option value="">Pilih Karyawan...</option>');

            if (!jabatanId) {
                $select.trigger('change.select2');
                return;
            }

            $.ajax({
                url: `${BASE}/validator/get-users`,
                type: 'GET',
                data: {
                    jabatan: jabatanId
                },
                dataType: 'json',
                success: function(data) {
                    (data.data || data || []).forEach(function(item) {
                        const option = new Option(item.text, item.id, false,
                            String(item.id) === String(selectedId));
                        $select.append(option);
                    });
                    $select.trigger('change.select2');
                },
                error: function(xhr) {
                    console.error('Gagal memuat karyawan assignment:', xhr.responseText);
                }
            });
        }

        /* ─────────────────────────────────────────────────────────────────────
           TAMBAH ASSIGNMENT
        ───────────────────────────────────────────────────────────────────── */
        $('#btn_add_assignment').on('click', function() {
            $('#modal_assignment_title').text('Tambah Master Assignment');
            $('#assignment_id_edit').val('');
            $('#assignment_type').val(null).trigger('change');
            $('#assignment_environment').val(null).trigger('change');
            $('#assignment_holding_id').val(null).trigger('change');
            $('#assignment_departemen_id').empty().append('<option value="">Pilih Departemen...</option>').trigger('change.select2');
            $('#assignment_divisi_id').empty().append('<option value="">Pilih Divisi...</option>').trigger('change.select2');
            $('#assignment_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#assignment_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#assignment_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
            $('#assignment_active_wrap').hide();

            loadHoldingOptions('#assignment_holding_id');
            $('#modal_assignment').modal('show');
        });

        /* ─────────────────────────────────────────────────────────────────────
           EDIT ASSIGNMENT
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_edit_assignment', function() {
            const $btn = $(this);
            const v = {
                id: $btn.data('id'),
                type: $btn.data('type'),
                environment: String($btn.data('environment') || ''),
                holdingId: String($btn.data('holding-id') || ''),
                departemenId: String($btn.data('departemen-id') || ''),
                divisiId: String($btn.data('divisi-id') || ''),
                bagianId: String($btn.data('bagian-id') || ''),
                jabatanId: String($btn.data('jabatan-id') || ''),
                karyawanId: String($btn.data('karyawan-id') || ''),
                isActive: parseInt($btn.data('is-active')) === 1,
            };

            $('#modal_assignment_title').text('Edit Master Assignment');
            $('#assignment_id_edit').val(v.id);
            $('#assignment_active_wrap').show();
            $('#assignment_is_active').prop('checked', v.isActive);

            // Reset dropdown
            $('#assignment_departemen_id').empty().append('<option value="">Pilih Departemen...</option>').trigger('change.select2');
            $('#assignment_divisi_id').empty().append('<option value="">Pilih Divisi...</option>').trigger('change.select2');
            $('#assignment_bagian_id').empty().append('<option value="">Pilih Bagian...</option>').trigger('change.select2');
            $('#assignment_jabatan_id').empty().append('<option value="">Pilih Jabatan...</option>').trigger('change.select2');
            $('#assignment_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');

            $('#modal_assignment').modal('show');

            // Set tipe
            $('#assignment_type').val(v.type).trigger('change');
            $('#assignment_environment').val(v.environment).trigger('change');

            // Cascade chain
            loadHoldingOptions('#assignment_holding_id', v.holdingId);
            loadDepartemenOptions('#assignment_departemen_id', v.holdingId, v.departemenId, function() {
                loadDivisiOptions('#assignment_divisi_id', v.departemenId, v.divisiId, function() {
                    loadBagianOptions('#assignment_bagian_id', v.divisiId, v.bagianId, function() {
                        loadJabatanByBagianOptions('#assignment_jabatan_id', v.bagianId, v.jabatanId, function () {
                            loadAssignmentUserOptions(v.jabatanId, v.karyawanId);
                        });
                    });
                });
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           SIMPAN ASSIGNMENT (create + update)
        ───────────────────────────────────────────────────────────────────── */
        $('#btn_save_assignment').on('click', function() {
            const id = $('#assignment_id_edit').val();
            const type = $('#assignment_type').val();
            const holdingId = $('#assignment_holding_id').val();
            const deptId = $('#assignment_departemen_id').val();
            const divisiId = $('#assignment_divisi_id').val();
            const bagianId = $('#assignment_bagian_id').val();
            const jabatanId = $('#assignment_jabatan_id').val();
            const karyawanId = $('#assignment_karyawan_id').val();

            if (!type || !deptId || !jabatanId || !karyawanId) {
                Swal.fire('Perhatian', 'Tipe, departemen, jabatan, dan karyawan wajib diisi.', 'warning');
                return;
            }

            swalLoading();
            $.ajax({
                url: id ? `${BASE}/assignment/update/${id}` : `${BASE}/assignment/store`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    assignment_type: type,
                    environment: $('#assignment_environment').val(),
                    holding_id: holdingId,
                    departemen_id: deptId,
                    divisi_id: divisiId,
                    bagian_id: bagianId,
                    jabatan_id: jabatanId,
                    karyawan_id: karyawanId,
                    is_active: $('#assignment_is_active').is(':checked') ? 1 : 0,
                },
                success: function(res) {
                    Swal.close();
                    if (res.code === 200) {
                        $('#modal_assignment').modal('hide');
                        tblAssignment && tblAssignment.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: e => {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        });

        /* ─────────────────────────────────────────────────────────────────────
           DELETE ASSIGNMENT
        ───────────────────────────────────────────────────────────────────── */
        $(document).on('click', '.btn_delete_assignment', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Assignment?',
                text: 'Data assignment akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea5455'
            }).then(result => {
                if (!result.isConfirmed) return;
                swalLoading();
                $.ajax({
                    url: `${BASE}/assignment/delete/${id}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        Swal.close();
                        tblAssignment && tblAssignment.ajax.reload(null, false);
                        Swal.fire({
                            icon: res.code === 200 ? 'success' : 'error',
                            title: res.code === 200 ? 'Berhasil!' : 'Gagal!',
                            text: res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    },
                    error: e => {
                        Swal.close();
                        Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            });
        });
    });
</script>
@endsection
