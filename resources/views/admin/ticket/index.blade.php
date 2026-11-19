@extends('admin.layouts.dashboard')
@section('css')
@include('css_baru')
<style type="text/css">
    .my-swal {
        z-index: 9999;
    }

    #tab-bar {
        display: flex;
        gap: 4px;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0;
    }

    .tab-btn {
        padding: 8px 20px;
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
        color: #6c757d;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        cursor: pointer;
        margin-bottom: -2px;
        border-radius: 4px 4px 0 0;
        transition: all 0.2s ease;
    }

    .tab-btn:hover {
        color: #6d46da;
        background-color: #f0f0ff;
    }

    .tab-btn.active {
        color: #6d46da;
        border-bottom: 3px solid #6d46da;
        background-color: transparent;
    }

    /* Departemen checklist panel */
    .departemen-panel {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        margin-top: 4px;
    }

    .departemen-panel-header {
        background: #f8f9fa;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .departemen-panel-body {
        max-height: 200px;
        overflow-y: auto;
        padding: 4px 0;
    }

    .departemen-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 12px;
        cursor: pointer;
        transition: background 0.15s;
        border-bottom: 1px solid #f1f1f1;
        font-size: 13px;
    }

    .departemen-item:last-child {
        border-bottom: none;
    }

    .departemen-item:hover {
        background: #f0eeff;
    }

    .departemen-item input[type="checkbox"] {
        cursor: pointer;
        width: 15px;
        height: 15px;
        accent-color: #6d46da;
        flex-shrink: 0;
    }

    .departemen-item label {
        cursor: pointer;
        margin: 0;
        flex: 1;
        color: #333;
    }

    .departemen-empty {
        text-align: center;
        padding: 20px;
        color: #adb5bd;
        font-size: 13px;
    }

    .departemen-loading {
        text-align: center;
        padding: 16px;
        color: #6c757d;
        font-size: 13px;
    }

    .select-all-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 12px;
        border-bottom: 1px solid #dee2e6;
        background: #faf9ff;
    }

    .select-all-row input[type="checkbox"] {
        accent-color: #6d46da;
        width: 15px;
        height: 15px;
    }

    .select-all-row label {
        font-size: 12px;
        font-weight: 600;
        color: #6d46da;
        cursor: pointer;
        margin: 0;
    }

    .departemen-count {
        font-size: 11px;
        font-weight: 600;
        color: #6d46da;
        background: #ede9ff;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .departemen-search {
        padding: 8px 12px;
        border-bottom: 1px solid #dee2e6;
        background: #fff;
    }

    .departemen-search input {
        width: 100%;
        padding: 6px 10px;
        font-size: 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        outline: none;
    }

    .departemen-search input:focus {
        border-color: #6d46da;
    }

    .departemen-item.d-none {
        display: none !important;
    }

    .ticket-master-page {
        overflow: visible;
    }

    .ticket-master-page .master-table-card,
    .ticket-master-page .master-table-card>.card-body,
    .ticket-master-page .master-table-area,
    .ticket-master-page .master-table-area .dt-container,
    .ticket-master-page .master-table-area .dt-layout-table {
        height: auto !important;
        min-height: 0 !important;
    }

    .ticket-master-page .master-table-area {
        flex: none;
        overflow: visible;
    }

    .ticket-master-page .master-table-area .dt-layout-table,
    .ticket-master-page .master-table-area .dt-scroll {
        overflow: visible !important;
    }

    .ticket-master-page .master-table-area .dt-scroll-body {
        overflow: auto !important;
    }

    .ticket-master-page .master-table-area .dt-scroll-head,
    .ticket-master-page .master-table-area .dt-scroll-headInner,
    .ticket-master-page .master-table-area .dt-scroll-headInner table,
    .ticket-master-page .master-table-area .dt-scroll-body table {
        width: 100% !important;
    }

    .ticket-master-page #table_menu_master_wrapper .dt-scroll-body,
    .ticket-master-page #table_submenu_master_wrapper .dt-scroll-body {
        min-height: 52vh;
    }

    .ticket-master-page .master-table-toolbar {
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem !important;
    }

    .ticket-master-page .ticket-filter-group {
        margin-left: auto;
    }

    .ticket-master-page .ticket-filter-group label,
    .ticket-master-page .dt-length label,
    .ticket-master-page .dt-search label,
    .ticket-master-page .dt-info {
        color: #6d7585;
        font-size: 13px;
        font-weight: 600;
    }

    .ticket-master-page .dt-layout-row {
        align-items: center;
        margin: 0.75rem 0;
    }

    .ticket-master-page .dt-layout-row:first-child {
        margin-top: 0.5rem;
    }

    .ticket-master-page .dt-length,
    .ticket-master-page .dt-search {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .ticket-master-page .dt-length select {
        min-width: 62px;
    }

    .ticket-master-page .dt-search input {
        width: 180px;
        margin-left: 0 !important;
    }

    .ticket-master-page #table_menu_master {
        table-layout: fixed;
    }

    .ticket-master-page #table_submenu_master {
        table-layout: fixed;
    }

    .ticket-master-page #table_menu_master th,
    .ticket-master-page #table_menu_master td,
    .ticket-master-page #table_submenu_master th,
    .ticket-master-page #table_submenu_master td {
        vertical-align: middle;
    }

    .ticket-master-page #table_menu_master th:nth-child(1),
    .ticket-master-page #table_menu_master td:nth-child(1) {
        width: 70px;
        text-align: center;
    }

    .ticket-master-page #table_menu_master th:nth-child(2),
    .ticket-master-page #table_menu_master td:nth-child(2) {
        width: 22%;
    }

    .ticket-master-page #table_menu_master th:nth-child(3),
    .ticket-master-page #table_menu_master td:nth-child(3) {
        width: 14%;
        text-align: center;
    }

    .ticket-master-page #table_menu_master th:nth-child(4),
    .ticket-master-page #table_menu_master td:nth-child(4) {
        width: auto;
    }

    .ticket-master-page #table_menu_master th:nth-child(5),
    .ticket-master-page #table_menu_master td:nth-child(5) {
        width: 170px;
        text-align: center;
        white-space: nowrap;
    }

    .ticket-master-page #table_submenu_master th:nth-child(1),
    .ticket-master-page #table_submenu_master td:nth-child(1) {
        width: 70px;
        text-align: center;
    }

    .ticket-master-page #table_submenu_master th:nth-child(2),
    .ticket-master-page #table_submenu_master td:nth-child(2) {
        width: 18%;
        text-align: center;
    }

    .ticket-master-page #table_submenu_master th:nth-child(3),
    .ticket-master-page #table_submenu_master td:nth-child(3) {
        width: 26%;
    }

    .ticket-master-page #table_submenu_master th:nth-child(4),
    .ticket-master-page #table_submenu_master td:nth-child(4) {
        width: auto;
    }

    .ticket-master-page #table_submenu_master th:nth-child(5),
    .ticket-master-page #table_submenu_master td:nth-child(5) {
        width: 170px;
        text-align: center;
        white-space: nowrap;
    }

    .ticket-master-page .dept-cell {
        max-width: 100%;
        color: #62748a;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.45;
    }

    .ticket-master-page .dept-list {
        margin: 0;
        padding-left: 1rem;
    }

    .ticket-master-page .dept-list li {
        margin: 0 0 0.12rem;
        padding-left: 0.1rem;
    }

    .ticket-master-page .dept-collapse {
        margin-top: 0.3rem;
    }

    .ticket-master-page .dept-collapse summary {
        display: inline-flex;
        list-style: none;
        cursor: pointer;
        outline: none;
    }

    .ticket-master-page .dept-collapse summary::-webkit-details-marker {
        display: none;
    }

    .ticket-master-page .dept-toggle {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        padding: 3px 9px;
        border: 1px solid #d8c7ff;
        border-radius: 6px;
        background: #efe5ff;
        color: #7c3aed;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
    }

    .ticket-master-page .dept-collapse[open] .dept-toggle {
        display: none;
    }

    .ticket-master-page .dept-collapse[open] summary {
        display: none;
    }

    .ticket-master-page .dept-extra-list {
        max-height: 112px;
        overflow-y: auto;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        border-top: 1px dashed #d9dee8;
    }

    .ticket-master-page .dept-collapse-close {
        margin-top: 0.35rem;
        padding: 0;
        border: 0;
        background: transparent;
        color: #7c3aed;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
        cursor: pointer;
    }

    .ticket-master-page .dept-collapse-close:hover {
        text-decoration: underline;
    }

    @media (max-width: 767.98px) {
        .ticket-master-page .master-table-toolbar,
        .ticket-master-page .ticket-filter-group {
            align-items: stretch !important;
            flex-direction: column;
            width: 100%;
            margin-left: 0;
        }

        .ticket-master-page .ticket-filter-group select,
        .ticket-master-page .dt-search input {
            width: 100% !important;
        }
    }
</style>
@endsection

@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y ticket-master-page">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card width master-table-card">
                <div class="card-header">
                    <div id="tab-bar">
                        <button onclick="openTab('tabMenu', this)" class="tab-btn active">MASTER MENU</button>
                        <button onclick="openTab('tabSubMenu', this)" class="tab-btn">MASTER SUB-MENU</button>
                        {{-- <button onclick="openTab('tabKondisi', this)" class="tab-btn">MASTER KONDISI</button> --}}
                        <button onclick="openTab('tabHubMenu', this)" class="tab-btn">MENU HUB</button>
                    </div>
                </div>

                <div id="tabMenu" class="card-body tab-content active">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 master-table-toolbar">
                        <button id="btn_add_menu" class="btn btn-sm btn-primary">Tambah Menu</button>
                        <div class="d-flex align-items-center gap-2 ticket-filter-group">
                            <label for="filter_environment_menu" class="fw-semibold text-nowrap mb-0" style="font-size: 13px;">Filter Environment:</label>
                            <select id="filter_environment_menu" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">Semua Environment</option>
                                <option value="1">Epicor ERP</option>
                                <option value="2">Accurate Online SP</option>
                                <option value="3">Accurate Online AJA</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_add_menu" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable modal-md">
                            <form id="form_add_menu" method="post" action="{{ route('ticket.store') }}" class="modal-content" data-no-loading="true">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Tambah Menu</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="nama_menu">Nama Menu</label>
                                        <input type="text" name="nama_menu" id="nama_menu"
                                            class="form-control" placeholder="Masukkan Nama Menu" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kategori_add">Kategori</label>
                                        <select class="form-control" name="kategori" id="kategori_add" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="1">Epicor ERP</option>
                                            <option value="2">Accurate Online SP</option>
                                            <option value="3">Accurate Online AJA</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3" id="wrapper_departemen_add">
                                        <label>Departemen Menu <small class="text-muted">(opsional)</small></label>
                                        <div class="departemen-panel">
                                            <div class="departemen-panel-header">
                                                <span>Pilih Departemen</span>
                                                <span class="departemen-count" id="count_add">0 dipilih</span>
                                            </div>
                                            <div class="select-all-row">
                                                <input type="checkbox" id="chk_all_add">
                                                <label for="chk_all_add">Pilih Semua</label>
                                            </div>
                                            <div class="departemen-search">
                                                <input type="text" id="search_departemen_add" placeholder="Cari departemen...">
                                            </div>
                                            <div class="departemen-panel-body" id="list_departemen_add">
                                                <div class="departemen-loading">
                                                    <i class="ti ti-loader"></i> Memuat departemen...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_edit_menu" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable modal-md">
                            <form id="form_edit_menu" method="post" action="{{ route('ticket.update') }}" class="modal-content" data-no-loading="true">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Menu</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_menu_edit" id="id_menu_edit">
                                    <div class="form-group mb-3">
                                        <label for="nama_menu_edit">Nama Menu</label>
                                        <input type="text" name="nama_menu_edit" id="nama_menu_edit"
                                            class="form-control" placeholder="Masukkan Nama Menu" required>
                                    </div>
                                    <div class="form-group mb-3">
                                         <label for="kategori_edit">Kategori</label>
                                         <select class="form-control" name="kategori_edit" id="kategori_edit" required>
                                             <option value="">Pilih Kategori</option>
                                             <option value="1">Epicor ERP</option>
                                             <option value="2">Accurate Online SP</option>
                                             <option value="3">Accurate Online AJA</option>
                                         </select>
                                    </div>

                                    <div class="form-group mb-3" id="wrapper_departemen_edit">
                                        <label>Departemen Menu <small class="text-muted">(opsional)</small></label>
                                        <div class="departemen-panel">
                                            <div class="departemen-panel-header">
                                                <span>Pilih Departemen</span>
                                                <span class="departemen-count" id="count_edit">0 dipilih</span>
                                            </div>
                                            <div class="select-all-row">
                                                <input type="checkbox" id="chk_all_edit">
                                                <label for="chk_all_edit">Pilih Semua</label>
                                            </div>
                                            <div class="departemen-search">
                                                <input type="text" id="search_departemen_edit" placeholder="Cari departemen...">
                                            </div>
                                            <div class="departemen-panel-body" id="list_departemen_edit">
                                                <div class="departemen-loading">
                                                    <i class="ti ti-loader"></i> Memuat departemen...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="master-table-area">
                        <table class="table" id="table_menu_master" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Menu</th>
                                    <th>Kategori</th>
                                    <th>List Departemen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0"></tbody>
                        </table>
                    </div>
                </div>

                <div id="tabSubMenu" class="card-body tab-content" style="display: none;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 master-table-toolbar">
                        <button id="btn_add_submenu" class="btn btn-sm btn-primary">Tambah Sub Menu</button>
                        <div class="d-flex align-items-center gap-2 ticket-filter-group">
                            <label for="filter_environment_submenu" class="fw-semibold text-nowrap mb-0" style="font-size: 13px;">Filter Environment:</label>
                            <select id="filter_environment_submenu" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">Semua Environment</option>
                                <option value="1">Epicor ERP</option>
                                <option value="2">Accurate Online SP</option>
                                <option value="3">Accurate Online AJA</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_add_submenu" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable modal-md">
                            <form id="form_add_submenu" method="post" action="{{ route('ticket.submenu.store') }}" class="modal-content" data-no-loading="true">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Tambah Sub Menu</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="kategori_add_submenu">Kategori</label>
                                        <select class="form-control" name="kategori" id="kategori_add_submenu" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="1">Epicor ERP</option>
                                            <option value="2">Accurate Online SP</option>
                                            <option value="3">Accurate Online AJA</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="ticket_menu_add">Menu</label>
                                        <select class="form-control" name="parent_menu_id" id="ticket_menu_add" required>
                                            <option value="">-- Pilih Kategori Dulu --</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="nama_submenu">Nama Sub Menu</label>
                                        <input type="text" name="nama_submenu" id="nama_submenu"
                                            class="form-control" placeholder="Masukkan Nama Sub Menu" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_edit_submenu" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-md">
                            <form id="form_edit_submenu" method="post" action="{{ route('ticket.submenu.update') }}" class="modal-content" data-no-loading="true">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Sub Menu</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_submenu_edit" id="id_submenu_edit">
                                    <div class="form-group mb-3">
                                        <label>Menu Induk</label>
                                        <input type="text" id="nama_menu_edit_label" class="form-control" readonly
                                            style="background-color: #f5f5f5;">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nama_submenu_edit">Nama Sub Menu</label>
                                        <input type="text" name="nama_submenu_edit" id="nama_submenu_edit"
                                            class="form-control" placeholder="Masukkan Nama Sub Menu" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="master-table-area">
                        <table class="table" id="table_submenu_master" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Kategori</th>
                                    <th>Nama&nbsp;Menu</th>
                                    <th>Nama&nbsp;Sub Menu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0"></tbody>
                        </table>
                    </div>
                </div>

                {{-- MASTER KONDISI (NONAKTIF)
                <div id="tabKondisi" class="card-body tab-content" style="display: none;">
                    <button id="btn_add_kondisi" class="btn btn-sm btn-primary mb-3">Tambah Kondisi</button>

                    <div class="modal fade" id="modal_add_kondisi" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable modal-md">
                            <form method="post" action="{{ route('ticket.kondisi.store') }}" class="modal-content">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Tambah Kondisi Menu</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="code">Kode Kondisi</label>
                                        <input type="text" name="code" id="code" class="form-control" placeholder="Masukkan Kode Kondisi" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="label_kondisi">Nama Kondisi</label>
                                        <input type="text" name="label_kondisi" id="label_kondisi" class="form-control" placeholder="Masukkan Nama Kondisi" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tipe_data">Tipe Data</label>
                                        <select class="form-control" name="tipe_data" id="tipe_data" required>
                                            <option value="">Pilih Tipe Data</option>
                                            <option value="boolean">Boolean</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_edit_kondisi" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable modal-md">
                            <form method="post" action="{{ route('ticket.kondisi.update') }}" class="modal-content">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Kondisi</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_kondisi_edit" id="id_kondisi_edit">
                                    <div class="form-group mb-3">
                                        <label for="kode_edit">Kode Kondisi</label>
                                        <input type="text" name="kode_edit" id="kode_edit" class="form-control" placeholder="Masukkan Kode Kondisi" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="label_kondisi_edit">Nama Kondisi</label>
                                        <input type="text" name="label_kondisi_edit" id="label_kondisi_edit" class="form-control" placeholder="Masukkan Nama Kondisi" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tipe_data_edit">Tipe Data</label>
                                        <select class="form-control" name="tipe_data_edit" id="tipe_data_edit" required>
                                            <option value="">Pilih Tipe Data</option>
                                            <option value="boolean">Boolean</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="status_edit">Status</label>
                                        <select class="form-control" name="status_edit" id="status_edit" required>
                                            <option value="">Pilih Status</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <table class="table" id="table_kondisi_menu" style="width: 100%; font-size: small;">
                        <thead class="table-primary">
                            <tr>
                                <th>Kode</th>
                                <th>Label</th>
                                <th>Tipe Data</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0"></tbody>
                    </table>
                </div>
                --}}

                <div id="tabHubMenu" class="card-body tab-content" style="display:none;">
                    <div class="d-flex master-table-toolbar mb-3">
                        <button id="btn_add_hub_menu" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus me-1"></i>Tambah Menu Hub
                        </button>
                    </div>
                    <div class="modal fade" id="modal_hub_menu" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modal_hub_title">Tambah Menu Hub</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="hub_id_edit">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nama Menu <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="hub_nama"
                                                    placeholder="Contoh: Pengajuan Vendor">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Icon <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="hub_icon_preview_wrap">
                                                        <i id="hub_icon_preview" class="fa-solid fa-file"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="hub_icon"
                                                        placeholder="fa-solid fa-file">
                                                </div>
                                                <small class="text-muted">
                                                    Gunakan class Font Awesome.
                                                    <a href="https://fontawesome.com/icons" target="_blank">Cari icon →</a>
                                                </small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Deskripsi</label>
                                                <input type="text" class="form-control" id="hub_desc"
                                                    placeholder="Deskripsi singkat menu ini">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Warna Icon <span class="text-danger">*</span></label>
                                                <select class="form-control" id="hub_color">
                                                    <option value="ic-purple">🟣 Ungu (ic-purple)</option>
                                                    <option value="ic-blue">🔵 Biru (ic-blue)</option>
                                                    <option value="ic-green">🟢 Hijau (ic-green)</option>
                                                    <option value="ic-amber">🟡 Kuning (ic-amber)</option>
                                                    <option value="ic-red">🔴 Merah (ic-red)</option>
                                                    <option value="ic-teal">🩵 Teal (ic-teal)</option>
                                                </select>
                                                <div class="mt-2 d-flex align-items-center gap-2">
                                                    <div id="hub_tile_preview"
                                                        class="ic-purple"
                                                        style="width:40px;height:40px;border-radius:12px;font-size:18px;
                                                            display:flex;align-items:center;justify-content:center;">
                                                        <i class="fa-solid fa-file"></i>
                                                    </div>
                                                    <small class="text-muted">Preview icon</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Urutan Tampil</label>
                                                <input type="number" class="form-control" id="hub_sort" value="0" min="0">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Route Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="hub_route"
                                                    placeholder="ticket.vendor">
                                                <small class="text-muted">
                                                    Nama route Laravel. Contoh: <code>ticket</code>, <code>ticket.vendor</code>, <code>ticket.report</code>
                                                </small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Route Params (JSON)</label>
                                                <input type="text" class="form-control" id="hub_params"
                                                    placeholder='{"menu_id": null}'>
                                                <small class="text-muted">
                                                    Format JSON. Gunakan <code>null</code> agar otomatis diisi <code>menu_id</code> context.
                                                    Contoh: <code>{"menu_id": null}</code> atau <code>{"id": null}</code>
                                                </small>
                                            </div>
                                            <div class="col-12" id="hub_active_wrap" style="display:none">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="hub_is_active" checked>
                                                    <label class="form-check-label" for="hub_is_active">Menu Aktif</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-sm btn-primary" id="btn_save_hub_menu">
                                        <i class="mdi mdi-content-save me-1"></i>Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_hub_users" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <div>
                                        <h5 class="modal-title fw-semibold mb-0">
                                <i class="mdi mdi-account-multiple me-1 text-primary"></i>
                                Hak Akses User
                                        </h5>
                                        <small class="text-muted" id="hub_user_modal_label"></small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="hub_user_menu_id">
                                    <input type="hidden" id="hub_user_holding">

                                    <div class="alert alert-light border mb-3" style="font-size:12px">
                                        <i class="mdi mdi-information-outline me-1 text-primary"></i>
                                        Jika <strong>tidak ada user dipilih</strong>, menu ini akan tampil ke
                                        <strong>semua user</strong>. Centang user tertentu untuk membatasi akses.
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tbl_hub_users"
                                            class="table table-hover align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">No</th>
                                                    <th>Username</th>
                                                    <th>Nama Karyawan</th>
                                                    <th>Departemen</th>
                                                    <th>Jabatan</th>
                                                    <th width="50">
                                                        <input type="checkbox"
                                                            id="chk_all_hub_users"
                                                            class="form-check-input">
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="mt-2">
                                        <span id="hub_user_selected_count" class="badge bg-primary-subtle text-primary d-none">
                                            0 Terpilih
                                        </span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-sm btn-primary" id="btn_save_hub_users">
                                        <i class="mdi mdi-content-save me-1"></i>Simpan Hak Akses
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalApprovalMenu" data-bs-backdrop="static" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <div>
                                        <h5 class="modal-title fw-semibold mb-0">
                                            <i class="mdi mdi-shield-check me-1 text-success"></i>
                                            Pilih Approval Group
                                        </h5>
                                        <small class="text-muted" id="hub_approval_modal_label"></small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="hub_approval_menu_id">
                                    <input type="hidden" id="hub_approval_holding">

                                    <div class="alert alert-info border mb-3" style="font-size:12px">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Pilih <strong>satu Approval Group</strong> yang akan digunakan sebagai alur approval untuk menu hub ini.
                                        Approval group ditentukan berdasarkan menu <strong>Helpdesk IT (ID 109)</strong>.
                                    </div>

                                    <div id="hub_approval_selected_info" class="mb-2 d-none">
                                        <span class="badge bg-success-subtle text-success" id="hub_approval_selected_label">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            <span id="hub_approval_selected_nama">Belum dipilih</span>
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tbl_hub_approval"
                                            class="table table-hover align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">No</th>
                                                    <th>Group Approval</th>
                                                    <th width="60">Pilih</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-sm btn-success" id="btn_save_hub_approval">
                                        <i class="mdi mdi-content-save me-1"></i>Simpan Approval Group
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ══ DATATABLE ══ -->
                    <div class="master-table-area">
                        <table class="table table-sm" id="tbl_hub_menu" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Icon</th>
                                    <th>Nama Menu</th>
                                    <th>Route</th>
                                    <th>Warna</th>
                                    <th>Sort</th>
                                    <th>Hak Akses</th>
                                    <th>Approval Group</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
    const BASE_HUB = "{{ url('master_ticket') }}";
    const TOKEN = "{{ csrf_token() }}";
    const CURRENT_HOLDING = "{{ $holding->holding_code }}";

    function swalLoading(text = 'Memproses...') {
        Swal.fire({
            title: text,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
    }

    let tblHubMenu = null;
    let tblHubUsers = null;
    let hubCheckedIds = new Set();
    let hubFirstLoad = true;
    let selectedApprovalGroupId = null; // untuk radio approval group

    function adjustTicketTables() {
        if (!$.fn.DataTable || !$.fn.dataTable) {
            return;
        }

        const adjustVisibleTables = function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust();
        };

        requestAnimationFrame(adjustVisibleTables);
        setTimeout(adjustVisibleTables, 80);
        setTimeout(adjustVisibleTables, 250);
    }

    function openTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        
        const targetTab = document.getElementById(tabId);
        if (targetTab) {
            targetTab.style.display = 'block';
        }

        if (btn) {
            btn.classList.add('active');
        } else {
            document.querySelectorAll('.tab-btn').forEach(b => {
                if (b.getAttribute('onclick') && b.getAttribute('onclick').includes(tabId)) {
                    b.classList.add('active');
                }
            });
        }

        sessionStorage.setItem('active_ticket_tab', tabId);

        if (tabId === 'tabKondisi' && !$.fn.DataTable.isDataTable('#table_kondisi_menu')) {
            initTableKondisi();
        } else if (tabId === 'tabSubMenu' && !$.fn.DataTable.isDataTable('#table_submenu_master')) {
            initTableSubMenu();
        }
        if (tabId === 'tabHubMenu' && !$.fn.DataTable.isDataTable('#tbl_hub_menu')) {
            initHubMenuTable();
        }

        adjustTicketTables();
    }
    function fetchDepartemenList() {
        return $.ajax({
            url: "{{ route('ticket.departemen.list') }}",
            type: 'GET',
        });
    }

    function renderDepartemenList(containerId, countId, checkAllId, inputName, departemenList, selectedIds = []) {
        var $container = $('#' + containerId);
        $container.empty();

        if (!departemenList || departemenList.length === 0) {
            $container.html('<div class="departemen-empty"><i class="ti ti-mood-empty"></i> Tidak ada departemen tersedia</div>');
            return;
        }

        departemenList.forEach(function(d) {
            var isChecked = selectedIds.includes(String(d.id)) ? 'checked' : '';
            var row = `
            <div class="departemen-item">
                <input type="checkbox"
                    id="chk_dept_${containerId}_${d.id}"
                    name="${inputName}"
                    value="${d.id}"
                    class="departemen-checkbox-${containerId}"
                    ${isChecked}>
                <label for="chk_dept_${containerId}_${d.id}">
                    <span class="fw-semibold">[${d.holding_name}]</span> — ${d.nama_departemen}
                </label>
            </div>`;
            $container.append(row);
        });

        $container.off('change', 'input[type="checkbox"]').on('change', 'input[type="checkbox"]', function() {
            updateDepartemenCount(containerId, countId, checkAllId);
        });

        updateDepartemenCount(containerId, countId, checkAllId);
    }

    function updateDepartemenCount(containerId, countId, checkAllId) {
        var total = $('.departemen-checkbox-' + containerId).length;
        var selected = $('.departemen-checkbox-' + containerId + ':checked').length;
        $('#' + countId).text(selected + ' dipilih');
        $('#' + checkAllId).prop('indeterminate', selected > 0 && selected < total);
        $('#' + checkAllId).prop('checked', selected === total && total > 0);
    }

    function loadDepartemenAdd() {
        var $wrapper = $('#wrapper_departemen_add');
        var $list = $('#list_departemen_add');
        $wrapper.show();
        $list.html('<div class="departemen-loading"><i class="ti ti-loader"></i> Memuat departemen...</div>');
        $('#count_add').text('0 dipilih');

        fetchDepartemenList()
            .done(function(res) {
                renderDepartemenList('list_departemen_add', 'count_add', 'chk_all_add', 'departemen_ids[]', res.data, []);
            })
            .fail(function() {
                $list.html('<div class="departemen-empty text-danger"><i class="ti ti-alert-circle"></i> Gagal memuat departemen.</div>');
            });
    }

    function loadDepartemenEdit(selectedIds) {
        var $wrapper = $('#wrapper_departemen_edit');
        var $list = $('#list_departemen_edit');
        $wrapper.show();
        $list.html('<div class="departemen-loading"><i class="ti ti-loader"></i> Memuat departemen...</div>');
        $('#count_edit').text('0 dipilih');

        fetchDepartemenList()
            .done(function(res) {
                renderDepartemenList('list_departemen_edit', 'count_edit', 'chk_all_edit', 'departemen_ids[]', res.data, selectedIds);
            })
            .fail(function() {
                $list.html('<div class="departemen-empty text-danger"><i class="ti ti-alert-circle"></i> Gagal memuat departemen.</div>');
            });
    }

    $(document).ready(function() {
        const savedTab = sessionStorage.getItem('active_ticket_tab');
        if (savedTab && document.getElementById(savedTab)) {
            openTab(savedTab);
        }

        var tableMenu = $('#table_menu_master').DataTable({
            pageLength: 50,
            scrollY: '52vh',
            scrollCollapse: false,
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ticket.datatable') }}",
                data: function(d) {
                    d.kategori = $('#filter_environment_menu').val();
                }
            },
            columnDefs: [{
                    targets: 0,
                    width: '70px',
                    className: 'text-center'
                },
                {
                    targets: 1,
                    width: '22%'
                },
                {
                    targets: 2,
                    width: '14%',
                    className: 'text-center'
                },
                {
                    targets: 3,
                    width: '44%'
                },
                {
                    targets: 4,
                    width: '170px',
                    className: 'text-center text-nowrap'
                },
            ],
            columns: [{
                    data: "id",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_menu',
                    name: 'nama_menu'
                },
                {
                    data: 'kategori',
                    name: 'kategori',
                    render: function(data) {
                        if (data === 1) return '<span class="badge bg-primary">Epicor ERP</span>';
                        if (data === 2) return '<span class="badge bg-info">Accurate Online SP</span>';
                        if (data === 3) return '<span class="badge bg-success">Accurate Online AJA</span>';
                        return data;
                    }
                },
                {
                    data: 'departemen',
                    name: 'departemen',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data ?? '—';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [1, 'asc']
            ],
            initComplete: adjustTicketTables,
            drawCallback: adjustTicketTables
        });

        $(document).on('change', '#filter_environment_menu', function() {
            tableMenu.ajax.reload();
        });

        $(document).on('click', '.dept-collapse-close', function() {
            $(this).closest('details.dept-collapse').prop('open', false);
        });

        $(document).on('click', '#btn_add_menu', function() {
            $('#modal_add_menu').modal('show');
        });

        $('#modal_add_menu').on('shown.bs.modal', function() {
            loadDepartemenAdd();
        });

        $(document).on('change', '#chk_all_add', function() {
            var checked = $(this).is(':checked');
            $('.departemen-checkbox-list_departemen_add').prop('checked', checked);
            updateDepartemenCount('list_departemen_add', 'count_add', 'chk_all_add');
        });

        $(document).on('submit', '#form_add_menu', function(e) {
            e.preventDefault();
            var form = $(this);
            var btnSubmit = form.find('button[type="submit"]');
            btnSubmit.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#modal_add_menu').modal('hide');
                    form[0].reset();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Menu berhasil ditambahkan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (window.tableMenu) {
                        window.tableMenu.ajax.reload(null, false);
                    } else if ($.fn.DataTable.isDataTable('#table_menu_master')) {
                        $('#table_menu_master').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat menyimpan menu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal!', errorMsg, 'error');
                },
                complete: function() {
                    btnSubmit.prop('disabled', false);
                    if (typeof window.hideGlobalLoading === 'function') {
                        window.hideGlobalLoading();
                    }
                }
            });
        });

        $(document).on('click', '.btn_edit_menu', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var kategori = $(this).data('kategori');
            var departemenRaw = $(this).data('departemen-ids');
            var selectedIds = departemenRaw ?
                String(departemenRaw).split(',').map(function(x) {
                    return x.trim();
                }).filter(Boolean) : [];

            $('#id_menu_edit').val(id);
            $('#nama_menu_edit').val(nama);
            $('#kategori_edit').val(kategori);

            $('#modal_edit_menu').modal('show');
            loadDepartemenEdit(selectedIds);
        });

        $(document).on('change', '#chk_all_edit', function() {
            var checked = $(this).is(':checked');
            $('.departemen-checkbox-list_departemen_edit').prop('checked', checked);
            updateDepartemenCount('list_departemen_edit', 'count_edit', 'chk_all_edit');
        });

        $(document).on('submit', '#form_edit_menu', function(e) {
            e.preventDefault();
            var form = $(this);
            var btnSubmit = form.find('button[type="submit"]');
            btnSubmit.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#modal_edit_menu').modal('hide');
                    form[0].reset();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Menu berhasil diupdate.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (window.tableMenu) {
                        window.tableMenu.ajax.reload(null, false);
                    } else if ($.fn.DataTable.isDataTable('#table_menu_master')) {
                        $('#table_menu_master').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat memperbarui menu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal!', errorMsg, 'error');
                },
                complete: function() {
                    btnSubmit.prop('disabled', false);
                    if (typeof window.hideGlobalLoading === 'function') {
                        window.hideGlobalLoading();
                    }
                }
            });
        });

        $(document).on('click', '.btn_delete_menu', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Menu dan semua sub menu di dalamnya akan terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('ticket.destroy', ':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Menu berhasil dihapus.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            tableMenu.ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Dibatalkan',
                        text: 'Data aman :)',
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('click', '#btn_add_submenu', function() {
            $('#modal_add_submenu').modal('show');
        });

        $(document).on('change', '#kategori_add_submenu', function() {
            var kategori = $(this).val();
            var $menuSelect = $('#ticket_menu_add');

            $menuSelect.html('<option value="">-- Pilih Menu --</option>');

            if (!kategori) return;

            $.ajax({
                url: "{{ route('ticket.submenu.menu_kategori') }}",
                type: "GET",
                data: {
                    kategori: kategori
                },
                success: function(data) {
                    if (data.length === 0) {
                        $menuSelect.html('<option value="">Tidak ada menu</option>');
                        return;
                    }
                    $.each(data, function(i, menu) {
                        $menuSelect.append(
                            $('<option>', {
                                value: menu.id,
                                text: menu.nama_menu
                            })
                        );
                    });
                },
                error: function() {
                    Swal.fire('Gagal!', 'Gagal memuat data menu.', 'error');
                }
            });
        });

        $(document).on('submit', '#form_add_submenu', function(e) {
            e.preventDefault();
            var form = $(this);
            var btnSubmit = form.find('button[type="submit"]');
            btnSubmit.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#modal_add_submenu').modal('hide');
                    form[0].reset();
                    $('#ticket_menu_add').html('<option value="">-- Pilih Kategori Dulu --</option>');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Sub Menu berhasil ditambahkan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (window.tableSubMenu) {
                        window.tableSubMenu.ajax.reload(null, false);
                    } else if ($.fn.DataTable.isDataTable('#table_submenu_master')) {
                        $('#table_submenu_master').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal!', errorMsg, 'error');
                },
                complete: function() {
                    btnSubmit.prop('disabled', false);
                    if (typeof window.hideGlobalLoading === 'function') {
                        window.hideGlobalLoading();
                    }
                }
            });
        });

        $(document).on('submit', '#form_edit_submenu', function(e) {
            e.preventDefault();
            var form = $(this);
            var btnSubmit = form.find('button[type="submit"]');
            btnSubmit.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#modal_edit_submenu').modal('hide');
                    form[0].reset();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Sub Menu berhasil diupdate.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    if (window.tableSubMenu) {
                        window.tableSubMenu.ajax.reload(null, false);
                    } else if ($.fn.DataTable.isDataTable('#table_submenu_master')) {
                        $('#table_submenu_master').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat memperbarui data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Gagal!', errorMsg, 'error');
                },
                complete: function() {
                    btnSubmit.prop('disabled', false);
                    if (typeof window.hideGlobalLoading === 'function') {
                        window.hideGlobalLoading();
                    }
                }
            });
        });

        $(document).on('click', '.btn_edit_submenu', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var menu = $(this).data('menu');

            $('#id_submenu_edit').val(id);
            $('#nama_submenu_edit').val(nama);
            $('#nama_menu_edit_label').val(menu);

            $('#modal_edit_submenu').modal('show');
        });

        $(document).on('click', '.btn_delete_submenu', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Sub menu akan terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('ticket.submenu.destroy', ':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Sub menu berhasil dihapus.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            tableSubMenu.ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '#btn_add_kondisi', function() {
            $('#modal_add_kondisi').modal('show');
        });

        $(document).on('click', '.btn_edit_kondisi', function() {
            $('#id_kondisi_edit').val($(this).data('id'));
            $('#kode_edit').val($(this).data('kode'));
            $('#label_kondisi_edit').val($(this).data('label'));
            $('#tipe_data_edit').val($(this).data('tipe'));
            $('#status_edit').val($(this).data('status'));
            $('#modal_edit_kondisi').modal('show');
        });

        $(document).on('click', '.btn_delete_kondisi', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Kondisi ini akan terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('ticket.kondisi.destroy', ':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Kondisi berhasil dihapus.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            window.tableKondisi.ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Dibatalkan',
                        text: 'Data aman :)',
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        var tableMenu = $('#table_dependency_master').DataTable({
            pageLength: 50,
            scrollY: '320px',
            scrollCollapse: true,
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ticket.dependency.datatable') }}"
            },
            columns: [{
                    data: "id",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'source_sub_menu',
                    name: 'source_sub_menu'
                },
                {
                    data: 'kondisi',
                    name: 'kondisi',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data ?? '—';
                    }
                },
                {
                    data: 'target_sub_menu',
                    name: 'target_sub_menu'
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return data == 1 ?
                            '<span class="badge bg-success">Aktif</span>' :
                            '<span class="badge bg-secondary">Tidak Aktif</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [1, 'asc']
            ]
        });

        $(document).on('click', '#btn_add_dependency', function() {
            $('#modal_add_dependency').modal('show');
        });

        $('#modal_add_dependency').on('show.bs.modal', function() {
            loadMenuOptions('source_menu');
            loadMenuOptions('target_menu');
            loadKondisiOptions();

            $('#source_sub_menu_id, #target_sub_menu_id').html('<option value="">--Pilih--</option>').prop('disabled', true);
            $('#condition_key').html('<option value="">--Pilih Kondisi--</option>').prop('disabled', true);
        });

        function loadMenuOptions(targetId) {
            $.ajax({
                url: "{{ route('ticket.dependency.menu_kategori') }}",
                method: 'GET',
                success: function(res) {
                    let options = '<option value="">--Pilih Menu--</option>';
                    $.each(res.data, function(i, item) {
                        options += `<option value="${item.id}">${item.nama_menu}</option>`;
                    });
                    $(`#${targetId}`).html(options).prop('disabled', false);
                },
                error: function() {
                    toastr.error('Gagal memuat data menu');
                }
            });
        }

        function loadKondisiOptions() {
            $.ajax({
                url: "{{ route('ticket.kondisi.list') }}",
                method: 'GET',
                success: function(res) {
                    let options = '<option value="">--Pilih Kondisi--</option>';
                    $.each(res.data, function(i, item) {
                        options += `<option value="${item.code}">${item.label_kondisi}</option>`;
                    });
                    $('#condition_key').html(options).prop('disabled', false);
                },
                error: function() {
                    toastr.error('Gagal memuat data kondisi');
                }
            });
        }

        function loadSubMenuOptions(menuId, targetId) {
            if (!menuId) {
                $(`#${targetId}`).html('<option value="">--Pilih Sub Menu--</option>').prop('disabled', true);
                return;
            }

            $.ajax({
                url: "{{ route('ticket.dependency.menu_kategori') }}", // adjust to your sub menu by menu route
                method: 'GET',
                data: {
                    menu_id: menuId
                },
                success: function(res) {
                    let options = '<option value="">--Pilih Sub Menu--</option>';
                    $.each(res.data, function(i, item) {
                        options += `<option value="${item.id}">${item.nama_sub_menu}</option>`;
                    });
                    $(`#${targetId}`).html(options).prop('disabled', false);
                },
                error: function() {
                    toastr.error('Gagal memuat data sub menu');
                }
            });
        }

        $('#source_menu').on('change', function() {
            loadSubMenuOptions($(this).val(), 'source_sub_menu_id');
            $('#condition_key').html('<option value="">--Pilih Kondisi--</option>').prop('disabled', true);
        });

        $('#source_sub_menu_id').on('change', function() {
            if ($(this).val()) {
                loadKondisiOptions();
            } else {
                $('#condition_key').html('<option value="">--Pilih Kondisi--</option>').prop('disabled', true);
            }
        });

        $('#target_menu').on('change', function() {
            loadSubMenuOptions($(this).val(), 'target_sub_menu_id');
        });

        $(document).on('click', '.btn_edit_dependency', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var kategori = $(this).data('kategori');
            var kondisiRaw = $(this).data('kondisi-ids');
            var selectedIds = kondisiRaw ?
                String(kondisiRaw).split(',').map(function(x) {
                    return x.trim();
                }).filter(Boolean) : [];

            $('#id_menu_edit').val(id);
            $('#nama_menu_edit').val(nama);
            $('#kategori_edit').val(kategori);

            $('#modal_edit_menu').modal('show');
            loadKondisiEdit(selectedIds);
        });

        $(document).on('click', '.btn_delete_dependency', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Dependency dan semua sub dependency di dalamnya akan terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('ticket.dependency.destroy', ':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Dependency berhasil dihapus.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            tableMenu.ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Dibatalkan',
                        text: 'Data aman :)',
                        icon: 'info',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('keyup', '#search_departemen_add', function() {
            var keyword = $(this).val().toLowerCase().trim();
            $('#list_departemen_add .departemen-item').each(function() {
                var text = $(this).find('label').text().toLowerCase();
                $(this).toggleClass('d-none', !text.includes(keyword));
            });
        });

        $(document).on('keyup', '#search_departemen_edit', function() {
            var keyword = $(this).val().toLowerCase().trim();
            $('#list_departemen_edit .departemen-item').each(function() {
                var text = $(this).find('label').text().toLowerCase();
                $(this).toggleClass('d-none', !text.includes(keyword));
            });
        });

        $('#modal_add_menu').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('#list_departemen_add').empty();
            $('#count_add').text('0 dipilih');
            $('#chk_all_add').prop('checked', false).prop('indeterminate', false);
            $('#search_departemen_add').val('');
        });

        $('#modal_edit_menu').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('#list_departemen_edit').empty();
            $('#count_edit').text('0 dipilih');
            $('#chk_all_edit').prop('checked', false).prop('indeterminate', false);
            $('#search_departemen_edit').val('');
        });

        $('#modal_add_submenu').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
            $('#ticket_menu_add').html('<option value="">-- Pilih Kategori Dulu --</option>');
        });

        $('#modal_edit_submenu').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });

        $('#modal_add_kondisi').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });

        $('#modal_edit_kondisi').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });

        $('#modal_add_dependency').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });

        $('#modal_edit_dependency').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    });

    function initTableKondisi() {
        window.tableKondisi = $('#table_kondisi_menu').DataTable({
            pageLength: 50,
            scrollY: '320px',
            scrollCollapse: true,
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ticket.datatable.kondisi') }}"
            },
            columnDefs: [{
                targets: '_all',
                defaultContent: '-'
            }],
            columns: [{
                    data: "code",
                    name: "code"
                },
                {
                    data: "label_kondisi",
                    name: "label_kondisi"
                },
                {
                    data: "tipe_data",
                    name: "tipe_data"
                },
                {
                    data: "status",
                    name: "status",
                    render: function(data) {
                        if (data == 1) return '<span class="badge bg-success">Aktif</span>';
                        if (data == 0) return '<span class="badge bg-secondary">Tidak Aktif</span>';
                        return data;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [0, 'asc']
            ]
        });
    }

    function initTableSubMenu() {
        window.tableSubMenu = $('#table_submenu_master').DataTable({
            pageLength: 50,
            scrollY: '52vh',
            scrollCollapse: false,
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ticket.submenu.datatable') }}",
                data: function(d) {
                    d.kategori = $('#filter_environment_submenu').val();
                }
            },
            columnDefs: [{
                    targets: '_all',
                    defaultContent: '-'
                },
                {
                    targets: 0,
                    width: '70px',
                    className: 'text-center'
                },
                {
                    targets: 1,
                    width: '18%',
                    className: 'text-center'
                },
                {
                    targets: 2,
                    width: '26%'
                },
                {
                    targets: 3,
                    width: 'auto'
                },
                {
                    targets: 4,
                    width: '170px',
                    className: 'text-center text-nowrap'
                },
            ],
            columns: [{
                    data: "id",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'kategori',
                    name: 'ticket_menu.kategori',
                    render: function(data) {
                        if (data === 1) return '<span class="badge bg-primary">Epicor ERP</span>';
                        if (data === 2) return '<span class="badge bg-info">Accurate Online SP</span>';
                        if (data === 3) return '<span class="badge bg-success">Accurate Online AJA</span>';
                        return data;
                    }
                },
                {
                    data: 'nama_menu',
                    name: 'ticket_menu.nama_menu'
                },
                {
                    data: 'nama_sub_menu',
                    name: 'ticket_sub_menu.nama_sub_menu'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [0, 'asc']
            ],
            initComplete: adjustTicketTables,
            drawCallback: adjustTicketTables
        });

        $(document).on('change', '#filter_environment_submenu', function() {
            if (window.tableSubMenu) {
                window.tableSubMenu.ajax.reload();
            }
        });
    }
    function initHubMenuTable() {
        if ($.fn.DataTable.isDataTable('#tbl_hub_menu')) {
            tblHubMenu.ajax.reload();
            return;
        }
        tblHubMenu = $('#tbl_hub_menu').DataTable({
            pageLength: 50,
            scrollY: '320px',
            scrollCollapse: true,
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: `${BASE_HUB}/hub-menu/datatable?holding=${CURRENT_HOLDING}`,
            columns: [
                { data: null,             render: (d,t,r,m) => m.row + m.settings._iDisplayStart + 1, orderable: false },
                { data: 'icon_preview',   orderable: false },
                { data: 'nama_menu',      orderable: false },
                { data: 'route_name',     orderable: false },
                { data: 'color_class',    orderable: false },
                { data: 'sort_order',     orderable: false },
                { data: 'hak_akses',      orderable: false },
                { data: 'approval_group', orderable: false },
                { data: 'status',         orderable: false },
                { data: 'action',         orderable: false },
            ],
            language: { emptyTable: 'Belum ada menu hub.', processing: 'Memuat...' }
        });
    }

    $(document).on('input', '#hub_icon', function () {
        const cls = $(this).val().trim() || 'fa-solid fa-file';
        $('#hub_icon_preview').attr('class', cls);
        $('#hub_tile_preview i').attr('class', cls);
    });

    $(document).on('change', '#hub_color', function () {
        $('#hub_tile_preview')
            .removeClass('ic-purple ic-blue ic-green ic-amber ic-red ic-teal')
            .addClass($(this).val());
    });

    /* ── Reset modal ── */
    function resetHubModal() {
        $('#hub_id_edit').val('');
        $('#hub_nama, #hub_desc, #hub_route').val('');
        $('#hub_params').val('{"menu_id": null}');
        $('#hub_icon').val('fa-solid fa-file').trigger('input');
        $('#hub_color').val('ic-purple').trigger('change');
        $('#hub_sort').val(0);
        $('#hub_is_active').prop('checked', true);
        $('#hub_active_wrap').hide();
    }

    /* ── Tambah ── */
    $('#btn_add_hub_menu').on('click', function () {
        $('#modal_hub_title').text('Tambah Menu Hub');
        resetHubModal();
        $('#modal_hub_menu').modal('show');
    });

    /* ── Edit ── */
    $(document).on('click', '.btn_edit_hub_menu', function () {
        const $b = $(this);
        $('#modal_hub_title').text('Edit Menu Hub');
        $('#hub_id_edit').val($b.data('id'));
        $('#hub_nama').val($b.data('nama'));
        $('#hub_icon').val($b.data('icon')).trigger('input');
        $('#hub_desc').val($b.data('desc'));
        $('#hub_color').val($b.data('color')).trigger('change');
        $('#hub_route').val($b.data('route'));
        const rawParams = $b.data('params');
        $('#hub_params').val(rawParams ? JSON.stringify(rawParams) : '');
        $('#hub_sort').val($b.data('sort'));
        $('#hub_is_active').prop('checked', $b.data('active') == 1);
        $('#hub_active_wrap').show();
        $('#modal_hub_menu').modal('show');
    });

    /* ── Simpan ── */
    $('#btn_save_hub_menu').on('click', function () {
        const id     = $('#hub_id_edit').val();
        const nama   = $('#hub_nama').val().trim();
        const icon   = $('#hub_icon').val().trim();
        const route  = $('#hub_route').val().trim();
        const params = $('#hub_params').val().trim();

        if (!nama || !icon || !route) {
            Swal.fire('Perhatian', 'Nama, icon, dan route wajib diisi.', 'warning');
            return;
        }
        if (params) {
            try { JSON.parse(params); }
            catch (e) {
                Swal.fire('Perhatian', 'Route Params harus berformat JSON valid.\nContoh: {"menu_id": null}', 'warning');
                return;
            }
        }

        swalLoading();
        $.ajax({
            url: id
                ? `${BASE_HUB}/hub-menu/update/${id}`
                : `${BASE_HUB}/hub-menu/store`,
            type: 'POST',
            data: {
                _token:       TOKEN,
                nama_menu:    nama,
                icon:         icon,
                description:  $('#hub_desc').val(),
                color_class:  $('#hub_color').val(),
                route_name:   route,
                route_params: params || null,
                sort_order:   $('#hub_sort').val(),
                is_active:    $('#hub_is_active').is(':checked') ? 1 : 0,
            },
            success: function (res) {
                Swal.close();
                if (res.code === 200) {
                    $('#modal_hub_menu').modal('hide');
                    tblHubMenu && tblHubMenu.ajax.reload(null, false);
                    Swal.fire({ icon:'success', title:'Berhasil!', text: res.message, timer:3000, showConfirmButton:false });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: e => { Swal.close(); Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error'); }
        });
    });

    /* ── Hapus ── */
    $(document).on('click', '.btn_delete_hub_menu', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Menu Hub?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ea5455'
        }).then(r => {
            if (!r.isConfirmed) return;
            swalLoading();
            $.ajax({
                url: `${BASE_HUB}/hub-menu/delete/${id}`,
                type: 'POST',
                data: { _token: TOKEN, _method: 'DELETE' },
                success: function (res) {
                    Swal.close();
                    tblHubMenu && tblHubMenu.ajax.reload(null, false);
                    Swal.fire({ icon: res.code===200?'success':'error', title: res.message, timer:3000, showConfirmButton:false });
                },
                error: e => { Swal.close(); Swal.fire('Error', e.responseJSON?.message, 'error'); }
            });
        });
    });

    $(document).on('click', '.btn_manage_user_hub', function () {
        const menuId  = $(this).data('id');
        const holding = CURRENT_HOLDING;
        const nama    = $(this).data('nama');

        hubCheckedIds = new Set();
        hubFirstLoad = true;

        $('#hub_user_menu_id').val(menuId);
        $('#hub_user_holding').val(holding);
        $('#hub_user_modal_label').text(nama);
        $('#hub_user_selected_count').addClass('d-none').text('0 Terpilih');
        $('#chk_all_hub_users').prop('checked', false);

        if ($.fn.DataTable.isDataTable('#tbl_hub_users')) {
            $('#tbl_hub_users').DataTable().destroy();
            $('#tbl_hub_users tbody').empty();
        }

        tblHubUsers = $('#tbl_hub_users').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            pageLength: -1,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            scrollY: true,
            scrollX: true,
            ajax: {
                url: BASE_HUB + '/hub-menu/users-datatable/' + holding + '/' + menuId,
                dataSrc: function (json) {
                    if (hubFirstLoad) {
                        (json.assigned_ids || []).forEach(id => hubCheckedIds.add(String(id)));
                        hubFirstLoad = false;
                    }
                    return json.data;
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false,
                render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1 },
                { data: 'username', name: 'username' },
                { data: 'nama_karyawan', orderable: false, searchable: true },
                { data: 'nama_departemen', orderable: false, searchable: true },
                { data: 'nama_jabatan', orderable: false, searchable: true },
                { data: 'option', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            drawCallback: function () {
                $('#tbl_hub_users tbody input[name="user_ids[]"]').each(function () {
                    $(this).prop('checked', hubCheckedIds.has(String($(this).val())));
                });

                const total   = $('#tbl_hub_users tbody input[name="user_ids[]"]').length;
                const checked = $('#tbl_hub_users tbody input[name="user_ids[]"]:checked').length;
                $('#chk_all_hub_users').prop('checked', total > 0 && total === checked);

                updateHubUserCount();
            }
        });

        $('#modal_hub_users').modal('show');
    });

    $(document).on('click','.btn_approval_menu',function(){
        const id       = $(this).data('id');
        const holding  = CURRENT_HOLDING;
        const namMenu  = $(this).data('nama');

        selectedApprovalGroupId = null;
        $('#hub_approval_selected_info').addClass('d-none');
        $('#hub_approval_selected_nama').text('Belum dipilih');

        $('#hub_approval_menu_id').val(id);
        $('#hub_approval_holding').val(holding);
        $('#hub_approval_modal_label').text(namMenu ? 'Menu: ' + namMenu : '');

        if ($.fn.DataTable.isDataTable('#tbl_hub_approval')) {
            $('#tbl_hub_approval').DataTable().destroy();
            $('#tbl_hub_approval tbody').empty();
        }

        datatableApproval(id, holding);
        $('#modalApprovalMenu').modal('show');
    });

    function datatableApproval(hubMenuId, holding){
        $('#tbl_hub_approval').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: `${BASE_HUB}/hub-menu/approval-group/datatable`,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    id: hubMenuId,
                    holding: holding
                }
            },
            columns: [
                { data: null, orderable: false, searchable: false,
                  render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1 },
                { data: 'nama_group', name: 'nama_group' },
                { data: 'option', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            drawCallback: function () {
                // Tandai radio yang sudah terpilih (dari server)
                $('#tbl_hub_approval tbody input[name="approval_group_id"]').each(function () {
                    if ($(this).prop('checked')) {
                        selectedApprovalGroupId = $(this).val();
                        const nama = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                        updateApprovalSelectedInfo(nama);
                    }
                });
            }
        });
    }

    // Saat user klik radio approval
    $(document).on('change', '#tbl_hub_approval tbody input[name="approval_group_id"]', function () {
        selectedApprovalGroupId = $(this).val();
        const nama = $(this).closest('tr').find('td:nth-child(2)').text().trim();
        updateApprovalSelectedInfo(nama);
    });

    function updateApprovalSelectedInfo(nama) {
        if (selectedApprovalGroupId) {
            $('#hub_approval_selected_nama').text(nama);
            $('#hub_approval_selected_info').removeClass('d-none');
        } else {
            $('#hub_approval_selected_info').addClass('d-none');
        }
    }

    // Simpan pilihan approval group
    $('#btn_save_hub_approval').on('click', function () {
        const $btn = $(this);
        const id   = $('#hub_approval_menu_id').val();

        if (!id) {
            Swal.fire('Perhatian', 'ID menu hub tidak ditemukan.', 'warning');
            return;
        }

        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...');

        $.ajax({
            url: `${BASE_HUB}/hub-menu/approval-group/save/${id}`,
            type: 'POST',
            data: {
                _token: TOKEN,
                approval_group_id: selectedApprovalGroupId || '',
                holding: CURRENT_HOLDING
            },
            success: function (res) {
                $('#modalApprovalMenu').modal('hide');
                tblHubMenu && tblHubMenu.ajax.reload(null, false);
                Swal.fire({
                    icon: res.code === 200 ? 'success' : 'error',
                    title: res.code === 200 ? 'Berhasil!' : 'Gagal!',
                    text: res.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            },
            error: e => Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error'),
            complete: () => $btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Simpan Approval Group')
        });
    });

    // Reset modal approval saat ditutup
    $('#modalApprovalMenu').on('hidden.bs.modal', function () {
        selectedApprovalGroupId = null;
        $('#hub_approval_selected_info').addClass('d-none');
        if ($.fn.DataTable.isDataTable('#tbl_hub_approval')) {
            $('#tbl_hub_approval').DataTable().destroy();
            $('#tbl_hub_approval tbody').empty();
        }
    });

    $(document).on('change', '#chk_all_hub_users', function () {
        const checked = $(this).is(':checked');
        $('#tbl_hub_users tbody input[name="user_ids[]"]').each(function () {
            $(this).prop('checked', checked);
            const id = String($(this).val());
            checked ? hubCheckedIds.add(id) : hubCheckedIds.delete(id);
        });
        updateHubUserCount();
    });

    $(document).on('change', '#tbl_hub_users tbody input[name="user_ids[]"]', function () {
        const id = String($(this).val());
        this.checked ? hubCheckedIds.add(id) : hubCheckedIds.delete(id);

        const total   = $('#tbl_hub_users tbody input[name="user_ids[]"]').length;
        const checked = $('#tbl_hub_users tbody input[name="user_ids[]"]:checked').length;
        $('#chk_all_hub_users').prop('checked', total > 0 && total === checked);

        updateHubUserCount();
    });

    function updateHubUserCount() {
        const count = hubCheckedIds.size;   // baca dari Set, bukan dari DOM yang sedang tampil
        const $badge = $('#hub_user_selected_count');
        if (count > 0) {
            $badge.removeClass('d-none').text(`${count} Terpilih`);
        } else {
            $badge.addClass('d-none');
        }
    }

    $('#btn_save_hub_users').on('click', function () {
        const $btn = $(this);
        const id   = $('#hub_user_menu_id').val();
        const ids  = Array.from(hubCheckedIds);   // kirim seluruh pilihan, bukan cuma yang tampil

        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Menyimpan...');

        $.ajax({
            url: `${BASE_HUB}/hub-menu/users/save/${id}`,
            type: 'POST',
            data: { _token: TOKEN, user_ids: ids },
            success: function (res) {
                $('#modal_hub_users').modal('hide');
                tblHubMenu && tblHubMenu.ajax.reload(null, false);
                Swal.fire({
                    icon: res.code === 200 ? 'success' : 'error',
                    title: res.code === 200 ? 'Berhasil!' : 'Gagal!',
                    text: res.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            },
            error: e => Swal.fire('Error', e.responseJSON?.message || 'Terjadi kesalahan.', 'error'),
            complete: () => $btn.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Simpan Hak Akses')
        });
    });
</script>
@endsection
