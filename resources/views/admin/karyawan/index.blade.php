@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <style>
        .karyawan-index-body,
        .karyawan-index-tabs,
        .karyawan-index-tab-content,
        .karyawan-index-pane.active {
            min-height: 0;
        }

        .karyawan-index-summary,
        .karyawan-index-divider,
        .karyawan-index-controls-row {
            flex: 0 0 auto;
        }

        .karyawan-index-tabs {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            overflow: hidden;
        }

        .karyawan-index-tab-content {
            flex: 1 1 auto;
            overflow: hidden;
        }

        .karyawan-index-pane.active {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .karyawan-index-body {
            padding: 0.65rem 1rem 1rem !important;
        }

        .karyawan-index-divider {
            display: none;
        }

        .karyawan-index-summary {
            padding: 0.5rem 0.75rem !important;
        }

        .karyawan-index-summary .row {
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.5rem;
        }

        .karyawan-index-summary .document-card {
            min-height: 62px;
            padding: 0.6rem 0.75rem !important;
        }

        .karyawan-index-summary .avatar {
            width: 34px;
            height: 34px;
        }

        .karyawan-index-summary small {
            font-size: 0.625rem;
        }

        .karyawan-index-summary h5 {
            font-size: 0.9rem;
        }

        .karyawan-index-controls-row {
            padding-top: 0.5rem;
        }

        .karyawan-index-controls-row {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 0.75rem;
            justify-content: space-between;
        }

        .karyawan-index-controls-row .nav {
            flex: 0 0 auto;
            gap: 0.35rem;
        }

        .karyawan-index-dt-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        .karyawan-index-dt-controls .dt-length,
        .karyawan-index-dt-controls .dt-search {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0;
            white-space: nowrap;
        }

        .karyawan-index-dt-controls .dt-search input {
            width: 190px;
            margin-left: 0 !important;
        }

        .karyawan-index-tabs .dt-container>.dt-layout-row:first-child,
        .karyawan-index-tabs .dt-container>.row:first-child {
            display: none !important;
        }

        @media (max-width: 767.98px) {
            .karyawan-index-controls-row {
                align-items: stretch;
                flex-direction: column;
            }

            .karyawan-index-dt-controls {
                justify-content: space-between;
                width: 100%;
                margin-left: 0;
            }

            .karyawan-index-dt-controls .dt-search input {
                width: 150px;
            }
        }

        .karyawan-index-tab-content {
            padding: 0.65rem 0 0 !important;
        }

        .karyawan-index-table {
            width: 100% !important;
            table-layout: fixed;
        }

        .karyawan-index-table th,
        .karyawan-index-table td {
            padding: 0.4rem 0.45rem !important;
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .karyawan-index-table td {
            font-size: 0.68rem !important;
            line-height: 1.2;
        }

        .karyawan-index-table .btn {
            max-width: 100%;
            padding: 0.3rem 0.45rem;
            font-size: 0.68rem;
            white-space: normal;
        }

        .karyawan-index-table th.karyawan-index-option-cell,
        .karyawan-index-table td.karyawan-index-option-cell {
            min-width: 108px;
            width: 108px;
            padding-right: 1rem !important;
            text-align: center;
            white-space: nowrap !important;
            word-break: normal;
            overflow-wrap: normal;
        }

        .karyawan-index-table td.karyawan-index-option-cell .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.05rem;
            min-width: 2.05rem;
            height: 2.05rem;
            padding: 0;
            white-space: nowrap;
            vertical-align: middle;
        }

        .karyawan-index-table td.karyawan-index-option-cell .btn+.btn {
            margin-left: 0.25rem;
        }

        .karyawan-index-table-bulanan th:nth-child(1),
        .karyawan-index-table-bulanan td:nth-child(1) {
            width: 4%;
        }

        .karyawan-index-table-bulanan th:nth-child(2),
        .karyawan-index-table-bulanan td:nth-child(2) {
            width: 9%;
        }

        .karyawan-index-table-bulanan th:nth-child(3),
        .karyawan-index-table-bulanan td:nth-child(3) {
            width: 13%;
        }

        .karyawan-index-table-bulanan th:nth-child(4),
        .karyawan-index-table-bulanan td:nth-child(4) {
            width: 13%;
        }

        .karyawan-index-table-bulanan th:nth-child(5),
        .karyawan-index-table-bulanan td:nth-child(5) {
            width: 15%;
        }

        .karyawan-index-table-bulanan th:nth-child(6),
        .karyawan-index-table-bulanan td:nth-child(6) {
            width: 8%;
        }

        .karyawan-index-table-bulanan th:nth-child(7),
        .karyawan-index-table-bulanan td:nth-child(7) {
            width: 9%;
        }

        .karyawan-index-table-bulanan th:nth-child(8),
        .karyawan-index-table-bulanan td:nth-child(8) {
            width: 16%;
        }

        .karyawan-index-table-bulanan th:nth-child(9),
        .karyawan-index-table-bulanan td:nth-child(9) {
            width: 13%;
        }

        .karyawan-index-table-harian th:nth-child(1),
        .karyawan-index-table-harian td:nth-child(1) {
            width: 4%;
        }

        .karyawan-index-table-harian th:nth-child(2),
        .karyawan-index-table-harian td:nth-child(2) {
            width: 11%;
        }

        .karyawan-index-table-harian th:nth-child(3),
        .karyawan-index-table-harian td:nth-child(3) {
            width: 14%;
        }

        .karyawan-index-table-harian th:nth-child(4),
        .karyawan-index-table-harian td:nth-child(4) {
            width: 13%;
        }

        .karyawan-index-table-harian th:nth-child(5),
        .karyawan-index-table-harian td:nth-child(5) {
            width: 18%;
        }

        .karyawan-index-table-harian th:nth-child(6),
        .karyawan-index-table-harian td:nth-child(6) {
            width: 10%;
        }

        .karyawan-index-table-harian th:nth-child(7),
        .karyawan-index-table-harian td:nth-child(7) {
            width: 14%;
        }

        .karyawan-index-table-harian th:nth-child(8),
        .karyawan-index-table-harian td:nth-child(8) {
            width: 16%;
        }

        .karyawan-index-tabs .dt-scroll-body {
            overflow-x: hidden !important;
            scrollbar-gutter: stable;
            padding-right: 0.35rem;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-account-tie me-2"></i>DATABASE KARYAWAN</h5>
                            <input type="hidden" id="menu_id_add" value="">
                            <div>
                                <button type="button" id="btn_tambah_karyawan" class="btn btn-sm btn-outline-primary"><i
                                        class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>

                                <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="menu-icon tf-icons mdi mdi-file-excel"></i> Excel
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#modal_import_karyawan" href="">Import Add Excel</a></li>
                                    <li><a class="dropdown-item" href="{{ route('ptkp', $holding->holding_code) }}">Export
                                            Excel</a></li>
                                </ul>
                                <a type="button"
                                    href="@if (Auth::user()->is_admin == 'hrd') {{ url('hrd/karyawan/pdfKaryawan/' . $holding->holding_code) }}@else{{ url('karyawan/pdfKaryawan/' . $holding->holding_code) }} @endif"
                                    class="btn btn-sm btn-outline-danger"><i
                                        class="menu-icon tf-icons mdi mdi-file-pdf-box"></i>PDF</a>

                            </div>

                        </div>
                    </div>
                    <div class="card-body karyawan-index-body">
                        {{-- <hr class="my-3 karyawan-index-divider"> --}}
                        <div class="fade show shadow_effect karyawan-index-summary" id="" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <div class="card shadow-none document-card border rounded p-3 h-100 d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-primary rounded shadow">
                                                    <i class="mdi mdi-account-tie mdi-20px"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="mb-2" style="border-bottom: 1px solid rgba(0, 0, 0, .05);">
                                                    <small class="text-muted">KARYAWAN LAKI-LAKI</small>
                                                </div>
                                                <h5 class="mb-0 fw-bold">{{ $karyawan_laki }}&nbsp;ORANG</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="card shadow-none document-card border rounded p-3 h-100 d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-success rounded shadow">
                                                    <i class="mdi mdi-account-tie mdi-20px"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="mb-2" style="border-bottom: 1px solid rgba(0, 0, 0, .05);">
                                                    <small class="text-muted">KARYAWAN PEREMPUAN</small>
                                                </div>
                                                <h5 class="mb-0 fw-bold">{{ $karyawan_perempuan }}&nbsp;ORANG</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="card shadow-none document-card border rounded p-3 h-100 d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-warning rounded shadow">
                                                    <i class="mdi mdi-account-tie mdi-20px"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="mb-2" style="border-bottom: 1px solid rgba(0, 0, 0, .05);">
                                                    <small class="text-muted">KARYAWAN BULANAN</small>
                                                </div>
                                                <h5 class="mb-0 fw-bold">{{ $karyawan_office }}&nbsp;ORANG</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="card shadow-none document-card border rounded p-3 h-100 d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-info rounded shadow">
                                                    <i class="mdi mdi-account-tie mdi-20px"></i>
                                                </div>
                                            </div>
                                            <div class="ms-3">
                                                <div class="mb-2" style="border-bottom: 1px solid rgba(0, 0, 0, .05);">
                                                    <small class="text-muted">KARYAWAN HARIAN</small>
                                                </div>
                                                <h5 class="mb-0 fw-bold">{{ $karyawan_shift }}&nbsp;ORANG</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <hr class="my-3 karyawan-index-divider"> --}}
                        <div class="modal fade" id="modal_import_karyawan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <div class="modal-content">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Import Add Karyawan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mt-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="file" id="file_excel" name="file_excel"
                                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                                        class="form-control" placeholder="Masukkan File" />
                                                    <label for="file_excel">File Excel</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-2 mt-2">
                                            <a href="{{ asset('admin/template_import/TEMPLATE IMPORT TAMBAH KARYAWAN BULANAN SP_SPS.xlsx') }}"
                                                type="button" download="" class="btn btn-xs btn-primary">Excel
                                                Download Format Excel</a>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" id="import_karywan_btn"
                                            class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_export_karyawan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Export Excel Karyawan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mt-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <h6>Download File Excel Data Karyawan</h6>
                                                    <a href="@if (Auth::user()->is_admin == 'hrd') {{ url('/hrd/karyawan/ExportKaryawan/' . $holding->holding_code) }}@else{{ url('karyawan/ExportKaryawan/' . $holding->holding_code) }} @endif"
                                                        type="button" class="btn btn-xs btn-success"> <i
                                                            class="menu-icon tf-icons mdi mdi-file-excel-outline"></i>
                                                        Download Excel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_non_aktif_karyawan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Form Nonaktif Karyawan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mt-2">
                                            <div class="col-md-12">
                                                <div class="card mb-4">
                                                    <!-- Account -->
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                            <input type="hidden" name="id_nonactive" id="id_nonactive"
                                                                value="">
                                                            <div id="foto_karyawawn">
                                                                <img class="d-block h-px-120 rounded"
                                                                    id="template_foto_karyawan" />
                                                            </div>

                                                            <table>
                                                                <tr>
                                                                    <th>Nama</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_nama"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Kontrak Kerja</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_kontrak_kerja"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Penempatan Kerja</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_penempatan_kerja"> </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Mulai Kontrak</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_mulai_kontrak"> </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Selesai Kontrak</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_selesai_kontrak"></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="date" id="date_now_add" name="date_now"
                                                        class="form-control" placeholder="Tanggal Nonaktif" />
                                                    <label for="date_now">Tanggal Nonaktif</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <textarea rows="10" id="alasan_non_aktif_add" name="alasan_non_aktif" class="form-control"
                                                        placeholder="Alasan"></textarea>
                                                    <label for="alasan_non_aktif">Alasan</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-xs btn-success" id="simpan_nonaktif">
                                            Save
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary"
                                            data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="nav-align-top karyawan-index-tabs">
                            <div class="karyawan-index-controls-row">
                                <ul class="nav nav-pills nav-fill" role="tablist">
                                    <li class="nav-item">
                                        <a type=" button" style="width: auto;" class="nav-link active" role="tab"
                                            data-bs-toggle="tab" href="#navs-pills-justified-home"
                                            data-karyawan-table="bulanan">
                                            <i class="tf-icons mdi mdi-account-tie me-1"></i><span
                                                class="d-none d-sm-block">Karyawan Bulanan</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a type="button" style="width: auto;" class="nav-link" role="tab"
                                            data-bs-toggle="tab" href="#navs-pills-justified-profile"
                                            data-karyawan-table="harian">
                                            <i class="tf-icons mdi mdi-account me-1"></i><span
                                                class="d-none d-sm-block">Karyawan Harian</span>
                                        </a>
                                    </li>

                                </ul>
                                <div class="karyawan-index-dt-controls">
                                    <div id="karyawan-length-control"></div>
                                    <div id="karyawan-search-control"></div>
                                </div>
                            </div>
                            <div class="tab-content karyawan-index-tab-content">
                                <div class="tab-pane fade show active karyawan-index-pane" id="navs-pills-justified-home"
                                    role="tabpanel">
                                    <div class="master-table-area">
                                        <table class="table table-hover karyawan-index-table karyawan-index-table-bulanan"
                                            id="table_karyawan_bulanan" style="width: 100%; font-size: smaller;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nomor&nbsp;ID</th>
                                                    <th>Nama&nbsp;Karyawan</th>
                                                    <th>Telepon</th>
                                                    <th>Email</th>
                                                    <th>Divisi</th>
                                                    <th>Jabatan</th>
                                                    <th>Penempatan&nbsp;Kerja</th>
                                                    <th>Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0 small">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade karyawan-index-pane" id="navs-pills-justified-profile"
                                    role="tabpanel">
                                    <div class="master-table-area">
                                        <table class="table karyawan-index-table karyawan-index-table-harian"
                                            id="table_karyawan_harian" style="width: 100%; font-size: smaller;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Nomor&nbsp;ID</th>
                                                    <th>Nama</th>
                                                    <th>Telepon</th>
                                                    <th>Alamat</th>
                                                    <th>Tanggal&nbsp;Masuk</th>
                                                    <th>Penempatan&nbsp;Kerja</th>
                                                    <th>Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
    <div class="modal fade" id="modal_add_karyawan" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ url('karyawan/tambah-karyawan-proses/' . $holding->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="number" name="no_karyawan" id="no_karyawan" class="form-control"
                                    value="">
                                <label for="no_karyawan">Nomor Karyawan</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" name="name" id="name" class="form-control"
                                    value="">
                                <input type="hidden" name="menu_id" id="menu_id_tambah" class="form-control"
                                    value="">
                                <label for="name">Nama Karyawan</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <select style="font-size: small;" class="form-control" id="kategori" name="kategori">
                                    <option value="Karyawan Bulanan">Karyawan Bulanan</option>
                                    <option value="Karyawan Harian">Karyawan Harian</option>
                                </select>
                                <label for="tgl_join">Kategori</label>
                            </div>
                        </div>
                        {{-- <div class="col-md-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" name="tgl_lahir" id="tgl_lahir" class="form-control"
                                    value="">
                                <label for="tgl_lahir">Nama Karyawan</label>
                            </div>
                        </div> --}}
                        <div class="col-md-12 mb-3">
                            <button type="submit" id="btn_simpan_keahlian" class="btn btn-sm btn-primary"><i
                                    class="mdi mdi-content-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var holding = '{{ $holding->holding_code }}';
        var url =
            "{{ url('karyawan_bulanan-datatable') }}" + '/' + holding;

        var url1 =
            "{{ url('karyawan_harian-datatable') }}" + '/' + holding;
        var url_add_karyawan =
            "{{ url('karyawan/tambah-karyawan') }}" + '/' + holding;
        // console.log(url);
        var table = $('#table_karyawan_bulanan').DataTable({
            pageLength: 20,
            scrollY: '200px',
            scrollCollapse: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
            },

            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nomor_identitas_karyawan',
                    name: 'nomor_identitas_karyawan'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'nomor_wa',
                    name: 'nomor_wa'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'penempatan_kerja',
                    name: 'penempatan_kerja'
                },
                {
                    data: 'option',
                    name: 'option',
                    width: '112px',
                    orderable: false,
                    searchable: false,
                    className: 'karyawan-index-option-cell'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
        // $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        //     table.columns.adjust().draw().responsive.recalc();
        //     // table.draw();
        // })
        var table1 = $('#table_karyawan_harian').DataTable({
            pageLength: 10,
            scrollY: '200px',
            scrollCollapse: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: url1,
            },

            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nomor_identitas_karyawan',
                    name: 'nomor_identitas_karyawan'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'telepon',
                    name: 'telepon'
                },
                {
                    data: 'detail_alamat',
                    name: 'detail_alamat'
                },
                {
                    data: 'tgl_join',
                    name: 'tgl_join'
                },
                {
                    data: 'penempatan_kerja',
                    name: 'penempatan_kerja'
                },
                {
                    data: 'option',
                    name: 'option',
                    width: '160px',
                    orderable: false,
                    searchable: false,
                    className: 'karyawan-index-option-cell'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });

        function adjustKaryawanTables() {
            if ($.fn.DataTable.isDataTable('#table_karyawan_bulanan')) {
                $('#table_karyawan_bulanan').DataTable().columns.adjust();
            }

            if ($.fn.DataTable.isDataTable('#table_karyawan_harian')) {
                $('#table_karyawan_harian').DataTable().columns.adjust();
            }
        }

        function scheduleKaryawanTableAdjust() {
            requestAnimationFrame(adjustKaryawanTables);
            setTimeout(adjustKaryawanTables, 120);
            setTimeout(adjustKaryawanTables, 260);
        }

        const karyawanTableControls = {
            bulanan: {
                length: $('#table_karyawan_bulanan_wrapper .dt-length').detach(),
                search: $('#table_karyawan_bulanan_wrapper .dt-search').detach()
            },
            harian: {
                length: $('#table_karyawan_harian_wrapper .dt-length').detach(),
                search: $('#table_karyawan_harian_wrapper .dt-search').detach()
            }
        };

        function showKaryawanTableControls(type) {
            const controls = karyawanTableControls[type];

            if (!controls) {
                return;
            }

            $('#karyawan-length-control').empty().append(controls.length);
            $('#karyawan-search-control').empty().append(controls.search);
        }

        showKaryawanTableControls('bulanan');

        $('a[data-karyawan-table]').on('shown.bs.tab', function() {
            showKaryawanTableControls($(this).data('karyawan-table'));
            scheduleKaryawanTableAdjust();
        });

        $(window).on('resize admin:sidebar-resize', scheduleKaryawanTableAdjust);
    </script>
    <script>
        $(document).ready(function() {
            var menu_id = $('#asoy').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);
            $('#menu_id_tambah').val(menu_id);


            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('menu_id', menu_id);
            // console.log(formData);

            $.ajax({
                type: "POST",

                url: "{{ url('/recruitment/update_menu') }}",
                data: formData,
                contentType: false,
                processData: false,
                error: function() {
                    alert('Something is wrong!');
                    // console.log(formData);
                },
            });
        })
        $("input[type=text]").keyup(function() {
            $(this).val($(this).val().toUpperCase());
        });
        $(document).on("click", "#btn_tambah_karyawan", function() {
            $('#modal_add_karyawan').modal('show');
        });
        $(document).on("click", "#btndetail_karyawan", function() {
            let id = $(this).data('id');
            let holding = $(this).data("holding");
            // console.log(holding);
            let url =
                "{{ url('/karyawan/detail/') }}" +
                '/' + id + '/' + holding;
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                showCancelButton: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    // Swal.showLoading()
                    $.ajax({
                        url: url,
                        method: 'GET',
                        contentType: false,
                        cache: false,
                        processData: false,
                        // data: {
                        //     id_kecamatan: id_kecamatan
                        // },
                        success: function(response) {
                            // console.log(response);
                            Swal.close();
                            window.location.assign(url);
                        },
                        error: function(data) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Error',
                                text: 'Error : ' + data.responseJSON.message,
                                showConfirmButton: true,
                            });
                            // console.log('error:', data)
                        },

                    })
                },
                onAfterClose() {
                    Swal.close()
                }
            });
        });
        $(document).on('click', '#btn_non_aktif_karyawan', function() {
            var id = $(this).data('id');
            var holding = $(this).data("holding");
            var nama = $(this).data('nama');
            var foto = $(this).data('foto');
            var tgl_mulai_kontrak = $(this).data('tgl_mulai_kontrak');
            var tgl_selesai_kontrak = $(this).data('tgl_selesai_kontrak');
            var kontrak_kerja = $(this).data('kontrak_kerja');
            var penempatan_kerja = $(this).data('penempatan_kerja');
            console.log(foto);

            if (foto == '' || foto == null || foto == undefined) {
                $('#template_foto_karyawan').attr(
                    'src',
                    "{{ asset('admin/assets/img/avatars/1.png') }}"
                );
            } else {
                $('#template_foto_karyawan').attr(
                    'src',
                    "{{ asset('storage/foto_karyawan') }}/" + foto
                );
            }
            $('#td_nama').html(nama);
            $('#td_mulai_kontrak').html(tgl_mulai_kontrak);
            $('#td_selesai_kontrak').html(tgl_selesai_kontrak);
            $('#td_kontrak_kerja').html(kontrak_kerja);
            $('#td_penempatan_kerja').html(penempatan_kerja);
            $('#id_nonactive').val(id);
            $('#modal_non_aktif_karyawan').modal('show');
        });
        $(document).on("click", "#btn_mapping_shift", function() {
            // console.log('ok');
            let id = $(this).data('id');
            let holding = $(this).data("holding");
            let url =
                "@if (Auth::user()->is_admin == 'hrd'){{ url('/hrd/karyawan/shift/') }}@else{{ url('/karyawan/shift/') }}@endif" +
                '/' + id + '/' + holding;
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    // console.log(response);
                    window.location.assign(url);
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.responseJSON.message,
                    })
                },

            })
        });

        // excel add
        $('#import_karywan_btn').on('click', function(e) {
            $('#modal_import_karyawan').modal('hide');

            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');

            var fileInput = $('#file_excel')[0];
            if (fileInput.files.length > 0) {
                formData.append('file_excel', fileInput.files[0]);
            }
            // post
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/ImportKaryawan/') }}" + '/' + holding,
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#modal_import_karyawan').modal('hide');
                        $('#file_excel').val('');
                        $('#table_karyawan_bulanan').DataTable().ajax.reload();
                    } else if (data.code == 402) {
                        let errors = data.errors;
                        // pastikan errors bukan null/undefined
                        if (!errors || Object.keys(errors).length === 0) {
                            Swal.fire({
                                title: data.message,
                                text: 'Terjadi kesalahan, tapi detail error tidak tersedia.',
                                icon: 'warning'
                            });
                            return;
                        }

                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            let messages = errors[key];
                            if (messages && messages.length > 0) {
                                messages.forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            }
                        });

                        Swal.fire({
                            title: data.message,
                            text: errorMessages,
                            icon: 'warning'
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        $('#simpan_nonaktif').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('date_now', $('#date_now_add').val());
            formData.append('id_nonactive', $('#id_nonactive').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('alasan_non_aktif', $('#alasan_non_aktif_add').val());
            $('#modal_non_aktif_karyawan').modal('hide');

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan/non_aktif_proses') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#table_karyawan_bulanan').DataTable().ajax.reload();
                        $('#table_karyawan_bulanan_nonaktif').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        // console.log(errors);
                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                errorMessages += `• ${message}\n`;
                            });
                        });
                        Swal.fire({
                            // title: data.message,
                            text: errorMessages,
                            icon: 'warning',
                            timer: 4500
                        })

                    } else if (data.code == 600) {
                        Swal.fire({
                            text: data.message,
                            icon: 'warning',
                            timer: 4500
                        });
                    } else if (data.code == 409) {
                        Swal.fire({
                            text: data.message,
                            icon: 'warning',
                            timer: 4500
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        // end excel add
    </script>
@endsection
