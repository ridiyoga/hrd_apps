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

    .work-flow-page {
        --workflow-primary: #6d46da;
        --workflow-primary-soft: #f0f0ff;
        --workflow-primary-light: #ede9ff;
        --workflow-primary-border: #d8ccff;
        --workflow-text: #566a7f;
        color: var(--workflow-text);
    }

    .work-flow-page .card {
        border: 0;
        border-radius: 8px;
        box-shadow: 0 0.25rem 1rem rgba(67, 89, 113, .12);
    }

    .work-flow-page .card-header {
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }

    .work-flow-page .card-title {
        color: var(--workflow-text);
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0;
    }

    .work-flow-page #flowTabs {
        display: flex;
        gap: 4px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0;
    }

    .work-flow-page #flowTabs .nav-link {
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

    .work-flow-page #flowTabs .nav-link:hover,
    .work-flow-page #flowTabs .nav-link:focus {
        border-color: transparent;
        border-bottom-color: transparent;
        background-color: var(--workflow-primary-soft);
        color: var(--workflow-primary);
    }

    .work-flow-page #flowTabs .nav-link.active {
        border-bottom-color: var(--workflow-primary);
        background-color: transparent;
        color: var(--workflow-primary);
    }

    .work-flow-page #flowTabs .nav-link i {
        color: inherit;
        font-size: 16px;
    }

    .work-flow-page .btn-primary {
        border-color: #8b4dfb;
        background-color: #8b4dfb;
        color: #fff;
    }

    .work-flow-page .btn-primary:hover,
    .work-flow-page .btn-primary:focus {
        border-color: var(--workflow-primary);
        background-color: var(--workflow-primary);
        color: #fff;
    }

    .work-flow-page .filter-pill:hover {
        border-color: var(--workflow-primary);
        color: var(--workflow-primary);
    }

    .work-flow-page .filter-pill.active {
        border-color: var(--workflow-primary);
        background: var(--workflow-primary);
        color: #fff;
    }

    .work-flow-page .sc-purple {
        border-color: var(--workflow-primary-border);
        background: var(--workflow-primary-light);
    }

    .work-flow-page .sc-purple .sc-icon {
        background: #e3dcff;
        color: var(--workflow-primary);
    }

    .work-flow-page .sc-purple .sc-val {
        color: var(--workflow-primary);
    }

    .work-flow-page .modern-card {
        border-color: var(--workflow-primary-border);
        box-shadow: 0 4px 14px rgba(109, 70, 218, .10);
    }

    .work-flow-page .modern-card:hover {
        box-shadow: 0 8px 24px rgba(109, 70, 218, .18);
    }

    .work-flow-page .card-header-gradient {
        background: linear-gradient(90deg, var(--workflow-primary) 0%, #a78bfa 100%);
    }

    .work-flow-page .orgchart .lines .topLine,
    .work-flow-page .orgchart .lines .downLine,
    .work-flow-page .orgchart .lines .rightLine,
    .work-flow-page .orgchart .lines .leftLine {
        border-color: var(--workflow-primary) !important;
    }

    .work-flow-page .orgchart .lines .downLine {
        background-color: var(--workflow-primary) !important;
    }

    .work-flow-page .karyawan-avatar-sm {
        background: linear-gradient(135deg, var(--workflow-primary), #a78bfa);
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
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-card .sc-val {
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-card .sc-label {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }

    .sc-purple {
        background: #f5f3ff;
        border-color: #e9d5ff;
    }

    .sc-purple .sc-icon {
        background: #ede9fe;
        color: #7c3aed;
    }

    .sc-purple .sc-val {
        color: #5b21b6;
    }

    .sc-blue {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .sc-blue .sc-icon {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .sc-blue .sc-val {
        color: #1e40af;
    }

    .sc-green {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .sc-green .sc-icon {
        background: #dcfce7;
        color: #16a34a;
    }

    .sc-green .sc-val {
        color: #15803d;
    }

    .sc-amber {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .sc-amber .sc-icon {
        background: #fef3c7;
        color: #d97706;
    }

    .sc-amber .sc-val {
        color: #b45309;
    }

    .filter-pill-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .filter-pill {
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        border: 1.5px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        cursor: pointer;
        transition: all .15s;
        user-select: none;
    }

    .filter-pill:hover {
        border-color: #7c3aed;
        color: #7c3aed;
    }

    .filter-pill.active {
        background: #7c3aed;
        color: #fff;
        border-color: #7c3aed;
    }

    .tbl-modern thead th {
        background: #faf5ff;
        color: #5b21b6;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 2px solid #e9d5ff !important;
        padding: 10px 12px;
    }

    .tbl-modern tbody tr {
        transition: background .12s;
    }

    .tbl-modern tbody tr:hover {
        background: #faf5ff !important;
    }

    .tbl-modern tbody td {
        vertical-align: middle;
        padding: 10px 12px;
        font-size: 13px;
        border-color: #f3f0ff;
    }

    .karyawan-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .karyawan-avatar-sm {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .karyawan-cell .kc-name {
        font-weight: 600;
        font-size: 12.5px;
        color: #1f2937;
    }

    .karyawan-cell .kc-dept {
        font-size: 10.5px;
        color: #9ca3af;
    }

    .env-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .env-epicor {
        background: #1e1b4b;
        color: #c4b5fd;
    }

    .env-accurate {
        background: #1e3a8a;
        color: #93c5fd;
    }

    .env-accurate-sp {
        background: #1e3a8a;
        color: #93c5fd;
    }

    .env-accurate-aja {
        background: #064e3b;
        color: #6ee7b7;
    }

    .asgn-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .asgn-a01 {
        background: #fdf4ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .asgn-a02 {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .act-btn {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all .15s;
    }

    .act-edit {
        background: #fff8ec;
        color: #f59e0b;
    }

    .act-edit:hover {
        background: #f59e0b;
        color: #fff;
    }

    .act-delete {
        background: #fef2f2;
        color: #ef4444;
    }

    .act-delete:hover {
        background: #ef4444;
        color: #fff;
    }

    /* ===== ORGCHART / MODERN CARD — shared by the Workflow Canvas org-tree modal ===== */
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
        margin: 0 8px;
    }

    .orgchart td {
        padding: 0 4px !important;
    }

    .orgchart>ul>li>ul li::before {
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

    .card-body.card-chart {
        padding: 14px 14px 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar-wrapper .avatar-img {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #e2d9f3;
    }

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

    .node-user-footer {
        padding: 5px 12px 8px;
        font-size: 11px;
        color: #9055fd;
        font-weight: 600;
        text-align: center;
        border-top: 1px dashed #e2d9f3;
    }

    .card-actions {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-top: 1px solid #f0ede8;
        background: #faf9ff;
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

    .btn-parent {
        background: #ecfdf5;
        color: #10b981;
    }

    .btn-parent:hover {
        background: #10b981;
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

    .btn-delete {
        background: #ffebee;
        color: #ea5455;
    }

    .btn-delete:hover {
        background: #ea5455;
        color: #fff;
    }

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

    .karyawan-item {
        border-bottom: 1px solid var(--bs-border-color);
        transition: background-color .12s ease;
        cursor: pointer;
        user-select: none;
    }

    .karyawan-item:last-child {
        border-bottom: none;
    }

    .karyawan-item:hover {
        background-color: rgba(var(--bs-primary-rgb), .05);
    }

    .karyawan-item.selected {
        background-color: rgba(var(--bs-primary-rgb), .08);
    }

    .karyawan-item .check-icon {
        transition: opacity .12s ease;
    }

    .karyawan-avatar {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 50%;
        background: rgba(var(--bs-primary-rgb), .12);
        color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
    }
</style>
@endsection

@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y work-flow-page">
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Master Approval Flow</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="flowTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-canvas">
                                <i class="mdi mdi-vector-curve me-1"></i>Workflow Canvas &amp; Dependency
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
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-difficulty">
                                <i class="mdi mdi-speedometer me-1"></i>Master Priority
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">
                        {{-- ================= TAB: WORKFLOW CANVAS & DEPENDENCY (konsolidasi) ================= --}}
                        <div class="tab-pane active show" id="tab-canvas" style="position:relative; height: 720px; overflow:hidden;">
                            @include('admin.ticket.partials.canvas_viewport')
                        </div>
                        {{-- ================= TAB: MASTER VALIDATOR (tidak berubah) ================= --}}
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
                                    <span class="filter-pill" data-vld-filter="1"><i class="mdi mdi-microchip me-1"></i>Epicor</span>
                                    <span class="filter-pill" data-vld-filter="2"><i class="mdi mdi-chart-pie me-1"></i>Accurate SP</span>
                                    <span class="filter-pill" data-vld-filter="3"><i class="mdi mdi-domain me-1"></i>Accurate AJA</span>
                                </div>
                                <button class="btn btn-sm btn-primary" id="btn_add_validator"><i class="mdi mdi-plus me-1"></i>Tambah Validator</button>
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

                        {{-- ================= TAB: MASTER ASSIGNMENT (tidak berubah) ================= --}}
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

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="filter-pill-bar mb-0">
                                    <span class="filter-pill active" data-asgn-filter="">Semua</span>
                                    <span class="filter-pill" data-asgn-filter="A01"><i class="mdi mdi-account-star me-1"></i>Assignor</span>
                                    <span class="filter-pill" data-asgn-filter="A02"><i class="mdi mdi-account-check me-1"></i>PIC</span>
                                </div>
                                <button class="btn btn-sm btn-primary" id="btn_add_assignment"><i class="mdi mdi-plus me-1"></i>Tambah Assignment</button>
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
                        {{-- ================= TAB: MASTER KESULITAN / DIFFICULTY ================= --}}
                        <div class="tab-pane" id="tab-difficulty">
                            <div class="stat-strip" id="diff-stats">
                                <div class="stat-card sc-purple">
                                    <div class="sc-icon"><i class="mdi mdi-speedometer"></i></div>
                                    <div>
                                        <div class="sc-val" id="diff-total">—</div>
                                        <div class="sc-label">Total Tingkat Prioritas</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-blue">
                                    <div class="sc-icon"><i class="mdi mdi-clock-check-outline"></i></div>
                                    <div>
                                        <div class="sc-val" id="diff-avg">—</div>
                                        <div class="sc-label">Rata-rata Target SLA</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-green">
                                    <div class="sc-icon"><i class="mdi mdi-lightning-bolt-outline"></i></div>
                                    <div>
                                        <div class="sc-val" id="diff-min">—</div>
                                        <div class="sc-label">SLA Tercepat</div>
                                    </div>
                                </div>
                                <div class="stat-card sc-amber">
                                    <div class="sc-icon"><i class="mdi mdi-timer-sand"></i></div>
                                    <div>
                                        <div class="sc-val" id="diff-max">—</div>
                                        <div class="sc-label">SLA Terlama</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div>
                                    <h6 class="mb-0 fw-bold" style="color:#5b21b6;"><i class="mdi mdi-format-list-bulleted-type me-1"></i>Daftar Tingkat Kesulitan &amp; Standar SLA</h6>
                                    <small class="text-muted">Kelola kode kesulitan, nama prioritas, dan estimasi waktu pengerjaan dalam hitungan <strong>menit</strong>.</small>
                                </div>
                                <button class="btn btn-sm btn-primary" id="btn_add_difficulty">
                                    <i class="mdi mdi-plus me-1"></i>Tambah Tingkat Kesulitan
                                </button>
                            </div>

                            <table class="table tbl-modern" id="tbl_difficulty" style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="40">#</th>
                                        <th width="110">Kode</th>
                                        <th>Nama Tingkat Kesulitan</th>
                                        <th width="240">Target SLA (Menit)</th>
                                        <th width="100">Aksi</th>
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

{{-- ============================ MODAL: Master Difficulty ============================ --}}
<div class="modal fade" id="modal_difficulty" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_difficulty_title">Tambah Tingkat Kesulitan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="difficulty_id_edit">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Kode Tingkat Kesulitan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="difficulty_code" placeholder="Contoh: P01, P02, P03, P04..." maxlength="50">
                                <small class="text-muted">Kode unik tingkat kesulitan (misal: P01, P02, P03, P04, dst).</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Tingkat Kesulitan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="difficulty_name" placeholder="Contoh: Critical Risk, High, Medium, Low..." maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Target SLA (Menit Pengerjaan) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="difficulty_expected_hours" placeholder="Contoh: 60" min="1" step="1">
                                    <span class="input-group-text bg-light fw-bold text-primary">Menit</span>
                                </div>
                                <small class="text-muted">Contoh: 30 menit, 60 menit (=1 jam), 120 menit (=2 jam), 720 menit (=12 jam), 1440 menit (=1 hari kerja).</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i class="mdi mdi-close me-1"></i>Batal</button>
                <button class="btn btn-sm btn-primary" id="btn_save_difficulty"><i class="mdi mdi-content-save me-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Master Validator (tidak berubah) ============================ --}}
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
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Environment <span class="text-danger">*</span></label>
                                <select class="form-control" id="validator_environment">
                                    <option value="">Pilih Environment...</option>
                                    <option value="1">Epicor ERP</option>
                                    <option value="2">Accurate Online SP</option>
                                    <option value="3">Accurate Online AJA</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Holding</label>
                                <select class="form-control" id="validator_holding_id">
                                    <option value="">Pilih Holding...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" id="validator_departemen_id">
                                    <option value="">Pilih Departemen...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Divisi</label>
                                <select class="form-control" id="validator_divisi_id">
                                    <option value="">Pilih Divisi...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bagian</label>
                                <select class="form-control" id="validator_bagian_id">
                                    <option value="">Pilih Bagian...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-control" id="validator_jabatan_id">
                                    <option value="">Pilih Jabatan...</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                                <select class="form-control" id="validator_karyawan_id">
                                    <option value="">Pilih Karyawan...</option>
                                </select>
                                <small class="text-muted">Daftar karyawan akan muncul setelah Jabatan dipilih.</small>
                            </div>
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
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i class="mdi mdi-close me-1"></i>Batal</button>
                <button class="btn btn-sm btn-primary" id="btn_save_validator"><i class="mdi mdi-content-save me-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================ MODAL: Master Assignment (tidak berubah) ============================ --}}
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
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipe Assignment <span class="text-danger">*</span></label>
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
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Holding</label>
                                <select class="form-control" id="assignment_holding_id">
                                    <option value="">Pilih Holding...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" id="assignment_departemen_id">
                                    <option value="">Pilih Departemen...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Divisi</label>
                                <select class="form-control" id="assignment_divisi_id">
                                    <option value="">Pilih Divisi...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bagian</label>
                                <select class="form-control" id="assignment_bagian_id">
                                    <option value="">Pilih Bagian...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-control" id="assignment_jabatan_id">
                                    <option value="">Pilih Jabatan...</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                                <select class="form-control" id="assignment_karyawan_id">
                                    <option value="">Pilih Karyawan...</option>
                                </select>
                                <small class="text-muted">Daftar karyawan akan muncul setelah Jabatan dipilih.</small>
                            </div>
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
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"><i class="mdi mdi-close me-1"></i>Batal</button>
                <button class="btn btn-sm btn-primary" id="btn_save_assignment"><i class="mdi mdi-content-save me-1"></i>Simpan</button>
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

        function swalLoading() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                showCancelButton: false,
                showConfirmButton: false
            });
        }

        /* ─────────────────────────────────────────────────────────────────────
           DROPDOWN LOADERS (shared: Validator + Assignment tabs)
        ───────────────────────────────────────────────────────────────────── */
        function loadHoldingOptions(selectId, selectedId = null, callback = null) {
            $.ajax({
                url: `${BASE}/approval-flow/get-holding`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const $select = $(selectId);
                    $select.empty().append('<option value="">Pilih Holding...</option>');
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId)));
                    });
                    if (selectedId) {
                        $select.val(selectedId).trigger('change.select2');
                    } else {
                        $select.trigger('change.select2');
                    }
                    if (typeof callback === 'function') callback();
                },
                error: function() {
                    if (typeof callback === 'function') callback();
                }
            });
        }

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
                data: {
                    holding: holdingId
                },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId)));
                    });
                    if (selectedId) {
                        $select.val(selectedId).trigger('change.select2');
                    } else {
                        $select.trigger('change.select2');
                    }
                    if (typeof callback === 'function') callback();
                },
                error: function() {
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
                data: {
                    departemen: departemenId
                },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId)));
                    });
                    if (selectedId) $select.val(selectedId).trigger('change.select2');
                    else $select.trigger('change.select2');
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
                data: {
                    divisi: divisiId
                },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId)));
                    });
                    if (selectedId) $select.val(selectedId).trigger('change.select2');
                    else $select.trigger('change.select2');
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
                data: {
                    bagian: bagianId
                },
                dataType: 'json',
                success: function(data) {
                    data.forEach(function(item) {
                        $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId)));
                    });
                    if (selectedId) $select.val(selectedId).trigger('change.select2');
                    else $select.trigger('change.select2');
                    if (typeof callback === 'function') callback();
                },
                error: function() {
                    if (typeof callback === 'function') callback();
                }
            });
        }

        /* ─────────────────────────────────────────────────────────────────────
           SELECT2 — MODAL VALIDATOR / ASSIGNMENT
        ───────────────────────────────────────────────────────────────────── */
        ['#validator_environment', '#validator_holding_id', '#validator_departemen_id',
            '#validator_divisi_id', '#validator_bagian_id', '#validator_jabatan_id', '#validator_karyawan_id'
        ].forEach(sel => $(sel).select2({
            theme: 'bootstrap-5',
            allowClear: true,
            dropdownParent: $('#modal_validator')
        }));

        ['#assignment_type', '#assignment_environment', '#assignment_holding_id', '#assignment_departemen_id',
            '#assignment_divisi_id', '#assignment_bagian_id', '#assignment_jabatan_id', '#assignment_karyawan_id'
        ].forEach(sel => $(sel).select2({
            theme: 'bootstrap-5',
            allowClear: true,
            dropdownParent: $('#modal_assignment')
        }));

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
                        $('#vld-total').text(json.recordsTotal ?? '—');
                        return json.data;
                    }
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                        orderable: false
                    },
                    {
                        data: 'nama_karyawan',
                        orderable: false,
                        render: function(data, type, row) {
                            const initial = (data || '?').charAt(0).toUpperCase();
                            const dept = row.nama_departemen ?? '—';
                            const divisi = row.nama_divisi ? ' · ' + row.nama_divisi : '';
                            return `<div class="karyawan-cell"><div class="karyawan-avatar-sm">${initial}</div><div><div class="kc-name">${data ?? '—'}</div><div class="kc-dept">${dept}${divisi}</div></div></div>`;
                        }
                    },
                    {
                        data: 'environment',
                        orderable: false,
                        render: function(data) {
                            if (data == 1) return '<span class="env-badge env-epicor"><i class="mdi mdi-microchip"></i> Epicor</span>';
                            if (data == 2) return '<span class="env-badge env-accurate-sp"><i class="mdi mdi-chart-pie"></i> Accurate SP</span>';
                            if (data == 3) return '<span class="env-badge env-accurate-aja"><i class="mdi mdi-domain"></i> Accurate AJA</span>';
                            return `<span class="badge bg-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: 'nama_departemen',
                        orderable: false,
                        render: v => v ? `<span style="font-size:12.5px;font-weight:600">${v}</span>` : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'nama_jabatan',
                        orderable: false,
                        render: v => v ? `<span class="badge" style="background:#f5f3ff;color:#5b21b6;font-size:11px;font-weight:600">${v}</span>` : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'is_active',
                        orderable: false,
                        render: v => v ? '<span class="badge bg-success" style="font-size:10px">Aktif</span>' : '<span class="badge bg-secondary" style="font-size:10px">Nonaktif</span>'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button class="act-btn act-edit btn_edit_validator me-1"
                                data-id="${row.id}" data-environment="${row.environment}"
                                data-departemen-id="${row.departemen_id ?? ''}" data-divisi-id="${row.divisi_id ?? ''}"
                                data-bagian-id="${row.bagian_id ?? ''}" data-jabatan-id="${row.jabatan_id ?? ''}"
                                data-karyawan-id="${row.karyawan_id ?? ''}" data-is-active="${row.is_active ? 1 : 0}" title="Edit">
                                <i class="mdi mdi-pencil"></i></button>
                                <button class="act-btn act-delete btn_delete_validator" data-id="${row.id}" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;
                        }
                    }
                ],
                language: {
                    emptyTable: 'Belum ada data validator.',
                    processing: 'Memuat data...'
                }
            });
        }
        $('a[href="#tab-validator"]').on('shown.bs.tab', function() {
            if (!tblValidator) initValidatorTable();
            else tblValidator.columns.adjust().draw();
            refreshValidatorStats();
        });

        $('#validator_holding_id').on('change', function() {
            loadDepartemenOptions('#validator_departemen_id', $(this).val());
            $('#validator_divisi_id, #validator_bagian_id, #validator_jabatan_id, #validator_karyawan_id').each(function() {
                $(this).empty().append(`<option value="">Pilih...</option>`).trigger('change.select2');
            });
        });
        $('#validator_departemen_id').on('change', function() {
            loadDivisiOptions('#validator_divisi_id', $(this).val());
            $('#validator_bagian_id, #validator_jabatan_id, #validator_karyawan_id').each(function() {
                $(this).empty().append(`<option value="">Pilih...</option>`).trigger('change.select2');
            });
        });
        $('#validator_divisi_id').on('change', function() {
            loadBagianOptions('#validator_bagian_id', $(this).val());
            $('#validator_jabatan_id, #validator_karyawan_id').each(function() {
                $(this).empty().append(`<option value="">Pilih...</option>`).trigger('change.select2');
            });
        });
        $('#validator_bagian_id').off('change').on('change', function() {
            loadJabatanByBagianOptions('#validator_jabatan_id', $(this).val());
            $('#validator_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
        });
        $('#validator_jabatan_id').on('change', function() {
            loadValidatorUserOptions($(this).val());
        });

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
                    (data.data || data || []).forEach(item => $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId))));
                    $select.trigger('change.select2');
                }
            });
        }

        $('#btn_add_validator').on('click', function() {
            $('#modal_validator_title').text('Tambah Master Validator');
            $('#validator_id_edit').val('');
            $('#validator_environment, #validator_holding_id').val(null).trigger('change');
            ['#validator_departemen_id', '#validator_divisi_id', '#validator_bagian_id', '#validator_jabatan_id', '#validator_karyawan_id']
            .forEach(sel => $(sel).empty().append('<option value="">Pilih...</option>').trigger('change.select2'));
            $('#validator_active_wrap').hide();
            loadHoldingOptions('#validator_holding_id');
            $('#modal_validator').modal('show');
        });

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
            ['#validator_departemen_id', '#validator_divisi_id', '#validator_bagian_id', '#validator_jabatan_id', '#validator_karyawan_id']
            .forEach(sel => $(sel).empty().append('<option value="">Pilih...</option>').trigger('change.select2'));
            $('#modal_validator').modal('show');
            $('#validator_environment').val(v.environment).trigger('change');
            loadHoldingOptions('#validator_holding_id', v.holdingId);
            loadDepartemenOptions('#validator_departemen_id', v.holdingId, v.departemenId, function() {
                loadDivisiOptions('#validator_divisi_id', v.departemenId, v.divisiId, function() {
                    loadBagianOptions('#validator_bagian_id', v.divisiId, v.bagianId, function() {
                        loadJabatanByBagianOptions('#validator_jabatan_id', v.bagianId, v.jabatanId, function() {
                            loadValidatorUserOptions(v.jabatanId, v.karyawanId);
                        });
                    });
                });
            });
        });

        $('#btn_save_validator').on('click', function() {
            const id = $('#validator_id_edit').val();
            const environment = $('#validator_environment').val();
            const deptId = $('#validator_departemen_id').val();
            const jabatanId = $('#validator_jabatan_id').val();
            const karyawanId = $('#validator_karyawan_id').val();
            if (!environment || !deptId || !jabatanId || !karyawanId) {
                Swal.fire('Perhatian', 'Environment, departemen, jabatan, dan karyawan wajib diisi.', 'warning');
                return;
            }
            swalLoading();
            $.ajax({
                url: id ? `${BASE}/validator/update/${id}` : `${BASE}/validator/store`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    environment: environment,
                    holding_id: $('#validator_holding_id').val(),
                    departemen_id: deptId,
                    divisi_id: $('#validator_divisi_id').val(),
                    bagian_id: $('#validator_bagian_id').val(),
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
                        _token: TOKEN
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
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                        orderable: false
                    },
                    {
                        data: 'karyawan',
                        orderable: false,
                        render: function(data) {
                            const initial = (data || '?').charAt(0).toUpperCase();
                            return `<div class="karyawan-cell"><div class="karyawan-avatar-sm">${initial}</div><div class="kc-name">${data ?? '—'}</div></div>`;
                        }
                    },
                    {
                        data: 'assignment_type',
                        orderable: false,
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
                            const env = (data === null || data === undefined || data === '') ? null : Number(data);
                            if (env === null) return '<span class="badge bg-secondary">Semua Environment</span>';
                            if (env === 1) return '<span class="env-badge env-epicor"><i class="mdi mdi-microchip"></i> Epicor</span>';
                            if (env === 2) return '<span class="env-badge env-accurate-sp"><i class="mdi mdi-chart-pie"></i> Accurate SP</span>';
                            if (env === 3) return '<span class="env-badge env-accurate-aja"><i class="mdi mdi-domain"></i> Accurate AJA</span>';
                            return '<span class="badge bg-warning text-dark">Tidak Dikenal</span>';
                        }
                    },
                    {
                        data: 'departemen',
                        orderable: false,
                        render: function(data, type, row) {
                            const holding = row.holding ? `<div style="font-size:10.5px;color:#9ca3af">${row.holding}</div>` : '';
                            return `<div style="font-size:12.5px;font-weight:600">${data ?? '—'}</div>${holding}`;
                        }
                    },
                    {
                        data: 'jabatan',
                        orderable: false,
                        render: v => v ? `<span class="badge" style="background:#f5f3ff;color:#5b21b6;font-size:11px;font-weight:600">${v}</span>` : '<span class="text-muted">—</span>'
                    },
                    {
                        data: 'is_active',
                        orderable: false,
                        render: v => v ? '<span class="badge bg-success" style="font-size:10px">Aktif</span>' : '<span class="badge bg-secondary" style="font-size:10px">Nonaktif</span>'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button class="act-btn act-edit btn_edit_assignment me-1"
                                data-id="${row.id}" data-type="${row.assignment_type}"
                                data-environment="${row.environment ?? ''}"
                                data-holding-id="${row.holding_id ?? ''}" data-departemen-id="${row.departemen_id ?? ''}"
                                data-divisi-id="${row.divisi_id ?? ''}" data-bagian-id="${row.bagian_id ?? ''}"
                                data-jabatan-id="${row.jabatan_id ?? ''}" data-karyawan-id="${row.karyawan_id ?? ''}"
                                data-is-active="${row.is_active ? 1 : 0}" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                <button class="act-btn act-delete btn_delete_assignment" data-id="${row.id}" title="Hapus"><i class="mdi mdi-trash-can"></i></button>`;
                        }
                    }
                ],
                language: {
                    emptyTable: 'Belum ada data assignment.',
                    processing: 'Memuat data...'
                }
            });
        }
        $('a[href="#tab-assignment"]').on('shown.bs.tab', function() {
            if (!tblAssignment) initAssignmentTable();
            else tblAssignment.columns.adjust().draw();
            refreshAssignmentStats();
        });

        $('#assignment_holding_id').on('change', function() {
            loadDepartemenOptions('#assignment_departemen_id', $(this).val());
            $('#assignment_divisi_id, #assignment_bagian_id, #assignment_jabatan_id, #assignment_karyawan_id').each(function() {
                $(this).empty().append('<option value="">Pilih...</option>').trigger('change.select2');
            });
        });
        $('#assignment_departemen_id').on('change', function() {
            loadDivisiOptions('#assignment_divisi_id', $(this).val());
            $('#assignment_bagian_id, #assignment_jabatan_id, #assignment_karyawan_id').each(function() {
                $(this).empty().append('<option value="">Pilih...</option>').trigger('change.select2');
            });
        });
        $('#assignment_divisi_id').on('change', function() {
            loadBagianOptions('#assignment_bagian_id', $(this).val());
            $('#assignment_jabatan_id, #assignment_karyawan_id').each(function() {
                $(this).empty().append('<option value="">Pilih...</option>').trigger('change.select2');
            });
        });
        $('#assignment_bagian_id').off('change').on('change', function() {
            loadJabatanByBagianOptions('#assignment_jabatan_id', $(this).val());
            $('#assignment_karyawan_id').empty().append('<option value="">Pilih Karyawan...</option>').trigger('change.select2');
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
                    (data.data || data || []).forEach(item => $select.append(new Option(item.text, item.id, false, String(item.id) === String(selectedId))));
                    $select.trigger('change.select2');
                }
            });
        }

        $('#btn_add_assignment').on('click', function() {
            $('#modal_assignment_title').text('Tambah Master Assignment');
            $('#assignment_id_edit').val('');
            $('#assignment_type, #assignment_environment, #assignment_holding_id').val(null).trigger('change');
            ['#assignment_departemen_id', '#assignment_divisi_id', '#assignment_bagian_id', '#assignment_jabatan_id', '#assignment_karyawan_id']
            .forEach(sel => $(sel).empty().append('<option value="">Pilih...</option>').trigger('change.select2'));
            $('#assignment_active_wrap').hide();
            loadHoldingOptions('#assignment_holding_id');
            $('#modal_assignment').modal('show');
        });

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
            ['#assignment_departemen_id', '#assignment_divisi_id', '#assignment_bagian_id', '#assignment_jabatan_id', '#assignment_karyawan_id']
            .forEach(sel => $(sel).empty().append('<option value="">Pilih...</option>').trigger('change.select2'));
            $('#modal_assignment').modal('show');
            $('#assignment_type').val(v.type).trigger('change');
            $('#assignment_environment').val(v.environment).trigger('change');
            loadHoldingOptions('#assignment_holding_id', v.holdingId);
            loadDepartemenOptions('#assignment_departemen_id', v.holdingId, v.departemenId, function() {
                loadDivisiOptions('#assignment_divisi_id', v.departemenId, v.divisiId, function() {
                    loadBagianOptions('#assignment_bagian_id', v.divisiId, v.bagianId, function() {
                        loadJabatanByBagianOptions('#assignment_jabatan_id', v.bagianId, v.jabatanId, function() {
                            loadAssignmentUserOptions(v.jabatanId, v.karyawanId);
                        });
                    });
                });
            });
        });

        $('#btn_save_assignment').on('click', function() {
            const id = $('#assignment_id_edit').val();
            const type = $('#assignment_type').val();
            const deptId = $('#assignment_departemen_id').val();
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
                    holding_id: $('#assignment_holding_id').val(),
                    departemen_id: deptId,
                    divisi_id: $('#assignment_divisi_id').val(),
                    bagian_id: $('#assignment_bagian_id').val(),
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
                        _token: TOKEN
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

        /* ─────────────────────────────────────────────────────────────────────
           STAT REFRESH
        ───────────────────────────────────────────────────────────────────── */
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

        // ── Filter pill — Validator ──────────────────────────────────────
        $(document).on('click', '[data-vld-filter]', function() {
            $('[data-vld-filter]').removeClass('active');
            $(this).addClass('active');
            const env = $(this).data('vld-filter');
            if (tblValidator) tblValidator.ajax.url(`${BASE}/validator/datatable${env ? '?env=' + env : ''}`).load();
        });
        // ── Filter pill — Assignment ─────────────────────────────────────
        $(document).on('click', '[data-asgn-filter]', function() {
            $('[data-asgn-filter]').removeClass('active');
            $(this).addClass('active');
            const type = $(this).data('asgn-filter');
            if (tblAssignment) tblAssignment.ajax.url(`${BASE}/assignment/datatable${type ? '?type=' + type : ''}`).load();
        });

        /* ─────────────────────────────────────────────────────────────────────
           DATATABLE MASTER KESULITAN / DIFFICULTY
        ───────────────────────────────────────────────────────────────────── */
        let tblDifficulty = null;

        function initDifficultyTable() {
            if ($.fn.DataTable.isDataTable('#tbl_difficulty')) {
                tblDifficulty.ajax.reload();
                return;
            }
            tblDifficulty = $('#tbl_difficulty').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${BASE}/difficulty/datatable`,
                    dataSrc: function(json) {
                        $('#diff-total').text(json.recordsTotal ?? '—');
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1,
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'code_badge',
                        orderable: true,
                        name: 'code'
                    },
                    {
                        data: 'name',
                        orderable: true,
                        render: function(data) {
                            return `<div class="fw-bold" style="color:#2e2c34;">${data ?? '—'}</div>`;
                        }
                    },
                    {
                        data: 'sla_formatted',
                        orderable: true,
                        name: 'expected_hours'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center'
                    }
                ],
                order: [[1, 'asc']],
                language: {
                    emptyTable: 'Belum ada data tingkat kesulitan.',
                    processing: 'Memuat data...'
                }
            });
        }

        $('a[href="#tab-difficulty"]').on('shown.bs.tab', function() {
            if (!tblDifficulty) initDifficultyTable();
            else tblDifficulty.columns.adjust().draw();
            refreshDifficultyStats();
        });

        function refreshDifficultyStats() {
            $.get(`${BASE}/difficulty/datatable?stats=1`, function(res) {
                $('#diff-total').text(res.total ?? '—');
                $('#diff-avg').text(res.avg ?? '—');
                $('#diff-min').text(res.min ?? '—');
                $('#diff-max').text(res.max ?? '—');
            });
        }

        $('#btn_add_difficulty').on('click', function() {
            $('#modal_difficulty_title').text('Tambah Tingkat Kesulitan');
            $('#difficulty_id_edit').val('');
            $('#difficulty_code').val('').prop('readonly', false);
            $('#difficulty_name').val('');
            $('#difficulty_expected_hours').val('');
            $('#modal_difficulty').modal('show');
        });

        $(document).on('click', '.btn_edit_difficulty', function() {
            const $btn = $(this);
            const id = $btn.data('id');
            const code = $btn.data('code');
            const name = $btn.data('name');
            const hours = $btn.data('hours');

            $('#modal_difficulty_title').text('Edit Tingkat Kesulitan');
            $('#difficulty_id_edit').val(id);
            $('#difficulty_code').val(code).prop('readonly', false);
            $('#difficulty_name').val(name);
            $('#difficulty_expected_hours').val(hours);
            $('#modal_difficulty').modal('show');
        });

        $('#btn_save_difficulty').on('click', function() {
            const id = $('#difficulty_id_edit').val();
            const code = $('#difficulty_code').val().trim();
            const name = $('#difficulty_name').val().trim();
            const hours = $('#difficulty_expected_hours').val().trim();

            if (!code || !name || !hours) {
                Swal.fire('Perhatian', 'Kode, Nama Tingkat Kesulitan, dan Target SLA wajib diisi.', 'warning');
                return;
            }

            swalLoading();
            $.ajax({
                url: id ? `${BASE}/difficulty/update/${id}` : `${BASE}/difficulty/store`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    code: code,
                    name: name,
                    expected_hours: hours
                },
                success: function(res) {
                    Swal.close();
                    if (res.code === 200) {
                        $('#modal_difficulty').modal('hide');
                        if (tblDifficulty) tblDifficulty.ajax.reload(null, false);
                        refreshDifficultyStats();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(e) {
                    Swal.close();
                    Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });

        $(document).on('click', '.btn_delete_difficulty', function() {
            const id = $(this).data('id');
            const code = $(this).data('code') || '';
            Swal.fire({
                title: 'Hapus Tingkat Kesulitan?',
                text: `Tingkat kesulitan ${code} akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea5455'
            }).then(result => {
                if (!result.isConfirmed) return;
                swalLoading();
                $.ajax({
                    url: `${BASE}/difficulty/delete/${id}`,
                    type: 'POST',
                    data: {
                        _token: TOKEN
                    },
                    success: function(res) {
                        Swal.close();
                        if (res.code === 200) {
                            if (tblDifficulty) tblDifficulty.ajax.reload(null, false);
                            refreshDifficultyStats();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function(e) {
                        Swal.close();
                        Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            });
        });
    });
</script>
@endsection