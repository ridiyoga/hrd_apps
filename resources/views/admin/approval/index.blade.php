@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/css/jquery.orgchart.min.css">
    <link rel="stylesheet" href="{{ asset('assets/assets_users/css/org_modern.css') }}">
    <style type="text/css">
        .select2-container {
            z-index: 9999 !important;
        }

        /* ukuran teks di area pilihan (input select2) */
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem !important;
            /* Bootstrap small (14px) */
            min-height: calc(1.5em + 0.75rem + 2px);
            /* biar tinggi konsisten */
        }

        /* ukuran teks di dropdown list */
        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 0.875rem !important;
        }

        /* Fokus warna primary */
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25) !important;
        }

        /* Background dan teks saat option terpilih */
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        /* Hover option */
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
            color: var(--bs-primary) !important;
        }

        /* ukuran huruf untuk pilihan yang sudah dipilih (tag dalam box) */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            font-size: 0.75rem;
            /* kecilin text */
            padding: 2px 6px;
            /* biar nggak terlalu tinggi */
            line-height: 1.2;
        }

        /* icon "x" di tag */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            font-size: 0.7rem;
            margin-right: 2px;
        }

        /* tulisan placeholder / hasil render */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            font-size: 0.8rem;
        }

        .swal2-container {
            z-index: 9999 !important;
        }

        .dataTables_wrapper {
            width: 100% !important;
        }

        /* BIKIN NODE AUTO SIZE */
        .orgchart .node {
            width: auto !important;
            min-width: 180px;
            max-width: 360px;
            height: auto !important;
            padding: 6px 10px;
        }

        /* BIAR TEKS TURUN BARIS */
        .orgchart .node .title,
        .orgchart .node .content {
            white-space: normal !important;
            width: auto !important;
            max-width: none !important;
            word-break: break-word;
            height: auto !important;
            min-height: 30px;
            line-height: 1.2;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* OPTIONAL: biar kelihatan rapi */
        .orgchart .node .title {
            font-weight: 600;
            background-color: #9055fd !important;
        }

        .orgchart .node .content {
            border: 2px solid #9055fd !important;
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

        /* HAPUS BACKGROUND GRADIENT LINE */
        .orgchart {
            background-image: none !important;
            background: none !important;
        }

        .org-wrapper {
            border: 1px solid #9055fd !important;
            border-radius: 0.375rem !important;
            text-align: center;
        }



        /* ===============================
                       FORM FLOATING LOOK
                        ================================ */
        .form-floating-outline .form-control {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background-color: #fafafa;
            transition: all 0.25s ease;
            font-size: 0.9rem;
        }

        .form-floating-outline .form-control:focus {
            background-color: #fff;
            border-color: #7367f0;
            box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.12);
        }

        /* Label lebih halus */
        .form-floating-outline label {
            font-size: 0.78rem;
            color: #6b7280;
        }

        /* ===============================
                        TABLE LOADING OVERLAY
                        ================================ */
        .table-loading {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
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
            font-size: 0.9rem;
            color: #6b7280;
        }

        /* Spinner modern */
        .spinner {
            width: 36px;
            height: 36px;
            border: 4px solid #e5e7eb;
            border-top-color: #7367f0;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .select-loader {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            z-index: 5;
            pointer-events: none;
        }

        /* ===============================
                        DOT LOADER (3 DOT)
                        ================================ */
        .dot-loader {
            display: flex;
            gap: 4px;
        }

        .dot-loader span {
            width: 6px;
            height: 6px;
            background-color: #7367f0;
            border-radius: 50%;
            opacity: 0.3;
            animation: dotPulse 1.2s infinite ease-in-out;
        }

        .dot-loader span:nth-child(1) {
            animation-delay: 0s;
        }

        .dot-loader span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot-loader span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes dotPulse {
            0% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.3;
            }
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA MASTER APPROVAL</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="modal fade" id="modal_tambah_approval" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" id="form_add_approval" class="modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Approval</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card mb-4">
                                            <!-- Account -->
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="group_id"
                                                            name="group_id" hidden />
                                                        <dl class="row">
                                                            <dt class="col-2">Group</dt>

                                                            <dd class="col-10" id="nama_group_approval"></dd>
                                                        </dl>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="nama_approval" class="form-label">Nama Approval</label>
                                                        <input type="text" class="form-control" id="nama_approval"
                                                            name="nama_approval" placeholder="Masukkan Nama Approval" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="departemen_id" class="form-label">Departemen</label>
                                                        <select class="form-control" multiple id="departemen_id"
                                                            name="departemen_id[]">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="level_approval" class="form-label">Level
                                                            Approval</label>
                                                        <select class="form-control" id="level_approval"
                                                            name="level_approval">
                                                            <option value="">Pilih Level Jabatan</option>
                                                            @foreach ($levels as $level)
                                                                <option value="{{ $level->level_id }}">
                                                                    {{ $level->jabatan_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-dismiss="modal">
                                            <i class="mdi mdi-close"></i>&nbsp;Close
                                        </button>
                                        <button type="button" class="btn_save_add_approval btn btn-sm btn-primary"><i
                                                class="mdi mdi-content-save"></i>&nbsp;Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_edit_approval" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" id="form_edit_approval" class="modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Approval</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card mb-4">
                                            <!-- Account -->
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="group_id_update"
                                                            name="group_id_update" hidden />
                                                        <dl class="row">
                                                            <dt class="col-2">Group</dt>

                                                            <dd class="col-10" id="nama_group_approval_update"></dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="nama_approval" class="form-label">Nama
                                                            Approval</label>
                                                        <input type="text" class="form-control"
                                                            id="nama_approval_update" name="nama_approval"
                                                            placeholder="Masukkan Nama Approval" />
                                                        <input type="hidden" class="form-control"
                                                            id="id_approval_update" name="id_approval"
                                                            placeholder="Masukkan Nama Approval" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="departemen_id_update"
                                                            class="form-label">Departemen</label>
                                                        <select class="form-control" multiple id="departemen_id_update"
                                                            name="departemen_id_update[]">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="level_approval_update" class="form-label">Level
                                                            Approval</label>
                                                        <select class="form-control" id="level_approval_update" pla
                                                            name="level_approval">
                                                            <option value="">Pilih Level Jabatan</option>
                                                            @foreach ($levels as $level)
                                                                <option value="{{ $level->level_id }}">
                                                                    {{ $level->jabatan_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label for="parent_approval_id" class="form-label">Parent
                                                            Approval</label>
                                                        <select class="form-control" id="parent_approval_id_update"
                                                            name="parent_approval_id_update">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-dismiss="modal">
                                            <i class="mdi mdi-close"></i>&nbsp;Close
                                        </button>
                                        <button type="button" class="btn_save_update_approval btn btn-sm btn-primary"><i
                                                class="mdi mdi-content-save"></i>&nbsp;Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_add_bawahan" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" id="form_add_bawahan" class="modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Bawahan Approval</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card mb-4">
                                            <!-- Account -->
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <input type="text" value="" id="id_parent_bawahan"
                                                            name="id_parent_bawahan" hidden>
                                                        <input type="text" id="group_id_bawahan"
                                                            name="group_id_bawahan" value="" hidden>
                                                        <dl class="row">
                                                            <dt class="col-2">Group</dt>
                                                            <dd class="col-10" id="nama_group_bawahan">: </dd>

                                                            <dt class="col-2">Parent</dt>
                                                            <dd class="col-10" id="parent_approval_bawahan">: </dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="nama_approval_bawahan" class="form-label">Nama
                                                            Approval</label>
                                                        <input type="text" class="form-control"
                                                            id="nama_approval_bawahan" name="nama_approval_bawahan"
                                                            placeholder="Masukkan Nama Approval" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="departemen_id_bawahan"
                                                            class="form-label">Departemen</label>
                                                        <select class="form-control" multiple id="departemen_id_bawahan"
                                                            name="departemen_id_bawahan[]">
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-lg-12">
                                                        <label for="level_approval_bawahan" class="form-label">Level
                                                            Approval</label>
                                                        <select class="form-control" id="level_approval_bawahan"
                                                            name="level_approval_bawahan">
                                                            <option value="">Pilih Level Jabatan</option>
                                                            @foreach ($levels as $level)
                                                                <option value="{{ $level->level_id }}">
                                                                    {{ $level->jabatan_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-dismiss="modal">
                                            <i class="mdi mdi-close"></i>&nbsp;Close
                                        </button>
                                        <button type="button" class="btn_save_add_bawahan btn btn-sm btn-primary"><i
                                                class="mdi mdi-content-save"></i>&nbsp;Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_tambah_group" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" id="form_add_group" class="modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Group</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card mb-4">
                                            <!-- Account -->
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="nama_group" class="form-label">Nama Grup</label>
                                                        <input type="text" class="form-control" id="nama_group"
                                                            name="nama_group" placeholder="Masukkan Nama Grup" />
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <div class="form-floating form-floating-outline">
                                                            <label for="menu_id" class="form-label">Menu
                                                                User(Mobile)</label>
                                                            <select name="menu_id[]" id="menu_id" class="form-control"
                                                                multiple>
                                                            </select>
                                                            <div class="select-loader d-none">
                                                                <div class="dot-loader">
                                                                    <span></span>
                                                                    <span></span>
                                                                    <span></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" class="btn_save_add_group btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_edit_group" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" id="form_edit_group" class="modal-content"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Group</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Account -->
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="nama_group" class="form-label">Nama Group</label>
                                                    <input type="text" class="form-control" id="nama_group_update"
                                                        name="nama_group" placeholder="Masukkan Nama Group" />
                                                    <input type="hidden" class="form-control" id="id_group_update"
                                                        name="id_group" placeholder="Masukkan Nama Group" />
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-lg-12">
                                                    <label for="menu_update" class="form-label">Menu User(Mobile)</label>
                                                    <select class="form-control" id="menu_update" name="menu_update[]"
                                                        multiple placeholder="Masukkan Menu">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" class="btn_save_update_group btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="icon-tab-0" data-bs-toggle="tab" href="#icon-tabpanel-0"
                                    role="tab" aria-controls="icon-tabpanel-0" aria-selected="true">
                                    Master
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="icon-tab-1" data-bs-toggle="tab" href="#icon-tabpanel-1"
                                    role="tab" aria-controls="icon-tabpanel-1" aria-selected="true">
                                    Group
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content pt-5" id="tab-content">
                            <div class="tab-pane active show" id="icon-tabpanel-0" role="tabpanel"
                                aria-labelledby="icon-tab-0">

                                <div class="tree-wrapper mt-4 ">
                                    <div id="tableLoading" class="table-loading d-none">
                                        <div class="loading-content">
                                            <div class="spinner"></div>
                                            <span>Memuat data...</span>
                                        </div>
                                    </div>
                                    <div id="tree-container">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="icon-tabpanel-1" role="tabpanel" aria-labelledby="icon-tab-1">
                                <button type="button" class="btn btn-sm btn-primary" id="btn_add_group"><i
                                        class="mdi mdi-plus"></i>Tambah</button>
                                <div id="tableLoading" class="table-loading d-none">
                                    <div class="loading-content">
                                        <div class="spinner"></div>
                                        <span>Memuat data...</span>
                                    </div>
                                </div>
                                <table class="table" id="table_group" style="width: 100%; font-size: small;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama&nbsp;Grup</th>
                                            <th>Menu&nbsp;(Mobile)</th>
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
        <!--/ Transactions -->
        <!--/ Data Tables -->
    </div>
    @include('users.logout_confirm')
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/js/jquery.orgchart.min.js"></script>
    <script>
        $(document).ready(function() {
            var menu_id = $('#asoy').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);

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

            function showTableLoading() {
                $('#tableLoading').removeClass('d-none');
            }

            function hideTableLoading() {
                $('#tableLoading').addClass('d-none');
            }

            function showSelectLoading(selectId) {
                $(selectId).prop('disabled', true)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .removeClass('d-none');
            }

            function hideSelectLoading(selectId) {
                $(selectId).prop('disabled', false)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .addClass('d-none');
            }
            $('#departemen_id').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Departemen...",
                allowClear: true
            });
            $('#departemen_id_update').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Departemen...",
                allowClear: true
            });

            $('#parent_approval_id').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Parent...",
                allowClear: true
            });


            function formatMenu(menu) {
                if (!menu.id) return menu.text;

                let icon = $(menu.element).data('icon') || '';
                return $(`
            <span style="display:flex;align-items:center;gap:6px">
                ${icon}
                <span>${menu.text}</span>
            </span>
         `);
            }
            $('#menu_id').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Menu...",
                templateResult: formatMenu,
                templateSelection: formatMenu,
                escapeMarkup: markup => markup
            });
            $('#menu_update').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Menu...",
                templateResult: formatMenu,
                templateSelection: formatMenu,
                escapeMarkup: markup => markup
            });
            let holding = "{{ $holding->holding_code }}";
            let holding_id = "{{ $holding->id }}";

            var table2 = $('#table_group').DataTable({
                pageLength: 50,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('approval_group-datatable') }}" + '/' + holding,
                },
                columns: [{
                        data: "id_approval",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_group',
                        name: 'nama_group'
                    },
                    {
                        data: 'daftar_menu',
                        name: 'daftar_menu'
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
            $('#icon-tab-0').on('shown.bs.tab', function(e) {
                loadTreeview();
            });
            $('#icon-tab-1').on('shown.bs.tab', function(e) {
                table2.columns.adjust().draw().responsive.recalc();
            });
            $('#group_id').on('change', function() {
                let get_group = $(this).val();
                let url = "{{ url('/approval/get_group') }}" + "/" + get_group + "/" + holding_id;
                // console.log(get_group);
                // console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        // console.log(response);
                        let options = '<option value="">Pilih Parent...</option>';
                        if (response.code == 200) {

                            $('#parent_approval_id').empty();
                            for (let i = 0; i < response.data.length; i++) {
                                options += '<option value="' + response.data[i].id_approval +
                                    '">' + response.data[i].nama_approval + '</option>';
                            }
                        } else {
                            options += '';
                        }
                        $('#parent_approval_id').html(options);
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        });
                    },

                })
            });
            $('#group_id_update').on('change', function() {
                let get_group = $(this).val();
                let url = "{{ url('/approval/get_group') }}" + "/" + get_group + "/" + holding_id;
                // console.log(get_group);
                // console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $('#parent_approval_id_update').empty();
                        let options = '<option value="">Pilih Parent...</option>';
                        for (let i = 0; i < response.data.length; i++) {
                            options += '<option value="' + response.data[i].id_approval + '">' +
                                response.data[i].nama_approval + '</option>';
                        }
                        $('#parent_approval_id_update').html(options);
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        });
                    },

                })
            });
            $(document).on('click', '#btn_add_approval', function() {
                // console.log(holding_id);
                var id = $(this).data('id_group');
                $.ajax({
                    url: "{{ url('/approval/get_add_approval') }}" + "/" + id + "/" + holding,
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        // console.log(response);
                        Swal.close();
                        if (response.code == 200) {
                            $('#group_id').empty();
                            $('#departemen_id').empty();
                            $('#parent_approval_id').empty();
                            let nama_group = response.group.find(grp => grp.id_group == id)
                                ?.nama_group || '';
                            // console.log(nama_group);
                            let options_departemen =
                                '<option value="">Pilih Departemen...</option>';
                            for (let i = 0; i < response.departemen.length; i++) {
                                options_departemen += '<option value="' + response.departemen[i]
                                    .id + '">' + response.departemen[i].nama_departemen +
                                    '</option>';
                            }
                            $('#departemen_id').html(options_departemen);
                            $('#group_id').val(id);
                            $('#nama_group_approval').html(': ' + nama_group);
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                        $('#level_approval').val('').trigger('change');
                        $('#nama_approval').val('');
                        $('#modal_tambah_approval').modal('show');
                    },
                    error: function(data) {
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                });
                $('#modal_tambah_approval').modal('show');
            });
            $(document).on('click', '.btn-edit-approval', function() {
                var id_approval = $(this).data('id_approval');
                var nama_approval = $(this).data('nama_approval');
                var level_approval = $(this).data('level_approval');
                var departemen = $(this).data('departemen');
                var holding = $(this).data('holding');
                var parent_approval_id = $(this).data('parent_approval_id');
                var group_id = $(this).data('group_id');
                let url = "{{ url('/approval/get_parent') }}" + "/" + id_approval + "/" + group_id + "/" +
                    holding_id;
                // console.log('ok' + level_approval);
                // console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        // console.log(response);
                        Swal.close();
                        $('#departemen_id_update').empty();
                        $('#parent_approval_id_update').empty();
                        let options = '<option value="">Pilih Parent...</option>';
                        for (let i = 0; i < response.data.length; i++) {
                            options += '<option value="' + response.data[i].id_approval + '" ' +
                                (response.data[i].id_approval === parent_approval_id ?
                                    'selected' : '') + '>' + response.data[i].nama_approval +
                                '</option>';
                        }
                        if (!group_id) {
                            group_id = [];
                        }
                        if (!holding) {
                            holding = [];
                        }
                        if (!departemen) {
                            departemen = [];
                        }
                        let nama_group = response.group.find(grp => grp.id_group == group_id)
                            ?.nama_group || '';
                        // console.log(departemen);
                        let options_departemen =
                            '<option value="">Pilih Departemen...</option>';
                        response.departemen.forEach(dep => {
                            const selected = departemen.includes(dep.id) ? 'selected' :
                                '';
                            options_departemen +=
                                `<option value="${dep.id}" ${selected}>${dep.nama_departemen}</option>`;
                        });
                        $('#nama_group_approval_update').html(': ' + nama_group);
                        $('#parent_approval_id_update').html(options);
                        $('#departemen_id_update').html(options_departemen);
                    },
                    error: function(data) {
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },

                });
                $('#id_approval_update').val(id_approval);
                $('#nama_approval_update').val(nama_approval);
                $('#level_approval_update').val(level_approval);
                // $('#parent_approval_id_update').val(parent_approval_id);
                $('#group_id_update').val(group_id);
                $('#departemen_id_update').val(departemen);
                $('#modal_edit_approval').modal('show');
            });
            $(document).on('click', '.btn-add-bawahan', function() {
                var id_approval = $(this).data('id_approval');
                var nama_approval = $(this).data('nama_approval');
                var group_id = $(this).data('group_id');
                var nama_group = $(this).data('nama_group');
                let url = "{{ url('/approval/get_parent') }}" + "/" + id_approval + "/" + group_id + "/" +
                    holding_id;
                // console.log('ok' + level_approval);
                // console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        // console.log(response);
                        Swal.close();
                        $('#id_approval_bawahan').val(id_approval);
                        $('#depatemen_id_bawahan').empty();
                        $('#nama_group_bawahan').empty();
                        $('#parent_approval_bawahan').empty();
                        $('#group_id_bawahan').empty();
                        let options_departemen =
                            '<option value="">Pilih Departemen...</option>';
                        response.departemen.forEach(dep => {
                            options_departemen +=
                                `<option value="${dep.id}">${dep.nama_departemen}</option>`;
                        });
                        $('#departemen_id_bawahan').html(options_departemen);
                        $('#departemen_id_bawahan').select2({
                            theme: 'bootstrap-5',
                            placeholder: "Pilih Departemen...",
                            allowClear: true
                        });
                        // $('#parent_approval_id_update').val(parent_approval_id);
                        $('#group_id_bawahan').val(group_id);
                        $('#id_parent_bawahan').val(id_approval);
                        $('#parent_approval_bawahan').append('<b>' + nama_approval + '</b>');
                        $('#nama_group_bawahan').append('<b>' + nama_group + '</b>');
                        $('#modal_add_bawahan').modal('show');

                    },
                    error: function(data) {
                        Swal.close();

                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },

                });
            });
            $(document).on('click', '.btn_save_update_approval', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var id_approval = $('#id_approval_update').val();
                var nama_approval = $('#nama_approval_update').val();
                var level_approval = $('#level_approval_update').val();
                var parent_approval_id = $('#parent_approval_id_update').val();
                var departemen_id_update = $('#departemen_id_update').val();
                var group_id = $('#group_id_update').val();
                var url = "{{ url('approval/approval_save_update') }}/" + get_holding;
                // console.log(url);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id_approval': id_approval,
                        'nama_approval': nama_approval,
                        'level_approval': level_approval,
                        'parent_approval_id': parent_approval_id,
                        'group_id': group_id,
                        'departemen_id_update': departemen_id_update,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        loadTreeview();
                        $('#form_edit_approval')[0].reset();
                        $('#modal_edit_approval').modal('hide');
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 4500
                            })
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_edit_approval')[0].reset();
                        $('#modal_edit_approval').modal('hide');
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            });
            $(document).on('click', '.btn_save_add_approval', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var nama_approval = $('#nama_approval').val();
                var group_id = $('#group_id').val();
                var departemen_id = $('#departemen_id').val();
                var level_approval = $('#level_approval').val();
                var parent_approval_id = $('#parent_approval_id').val();
                var url = "{{ url('approval/approval_save_add') }}/" + get_holding;
                // console.log(url);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'nama_approval': nama_approval,
                        'level_approval': level_approval,
                        'departemen_id': departemen_id,
                        'parent_approval_id': parent_approval_id,
                        'group_id': group_id
                    },
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        loadTreeview();
                        $('#form_add_approval')[0].reset();
                        $('#group_id').empty();
                        $('#parent_approval_id').empty();
                        $('#departemen_id').empty();
                        $('#modal_tambah_approval').modal('hide');
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 4500
                            })
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_add_approval')[0].reset();
                        $('#group_id').empty();
                        $('#parent_approval_id').empty();
                        $('#departemen_id').empty();
                        $('#modal_tambah_approval').modal('hide');
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            });
            $(document).on('click', '.btn_save_add_bawahan', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var nama_approval = $('#nama_approval_bawahan').val();
                var group_id = $('#group_id_bawahan').val();
                var departemen_id = $('#departemen_id_bawahan').val();
                var level_approval = $('#level_approval_bawahan').val();
                var parent_approval_id = $('#id_parent_bawahan').val();
                var url = "{{ url('approval/approval_save_add') }}/" + get_holding;
                // console.log(url);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'nama_approval': nama_approval,
                        'level_approval': level_approval,
                        'departemen_id': departemen_id,
                        'parent_approval_id': parent_approval_id,
                        'group_id': group_id
                    },
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        loadTreeview();
                        $('#form_add_bawahan')[0].reset();
                        $('#group_id_bawahan').empty();
                        $('#id_parent_bawahan').empty();
                        $('#departemen_id_bawahan').empty();
                        $('#id_parent_bawahan').empty();
                        $('#group_id_bawahan').empty();
                        $('#modal_add_bawahan').modal('hide');
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 4500
                            })
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_add_bawahan')[0].reset();
                        $('#group_id_bawahan').empty();
                        $('#id_parent_bawahan').empty();

                        $('#departemen_id_bawahan').empty();
                        $('#modal_add_bawahan').modal('hide');
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            });
            $(document).on('click', '#btn-edit-approval', function() {
                var id_approval = $(this).data('id_approval');
                var nama_approval = $(this).data('nama_approval');
                var level_approval = $(this).data('level_approval');
                var departemen = $(this).data('departemen');
                var holding = $(this).data('holding');
                var parent_approval_id = $(this).data('parent_approval_id');
                var group_id = $(this).data('group_id');
                let url = "{{ url('/approval/get_parent') }}" + "/" + id_approval + "/" + group_id + "/" +
                    holding_id;
                // console.log(departemen);
                // console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    contentType: false,
                    cache: false,
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
                    success: function(response) {
                        // console.log(response);
                        Swal.close();
                        $('#departemen_id_update').empty();
                        $('#parent_approval_id_update').empty();
                        let options = '<option value="">Pilih Parent...</option>';
                        for (let i = 0; i < response.data.length; i++) {
                            options += '<option value="' + response.data[i].id_approval + '" ' +
                                (response.data[i].id_approval === parent_approval_id ?
                                    'selected' : '') + '>' + response.data[i].nama_approval +
                                '</option>';
                        }
                        if (!group_id) {
                            group_id = [];
                        }
                        if (!holding) {
                            holding = [];
                        }
                        if (!departemen) {
                            departemen = [];
                        }
                        let options_group = '<option value="">Pilih Group...</option>';
                        response.group.forEach(grp => {
                            const selected = group_id.includes(grp.id_group) ?
                                'selected' : '';
                            options_group +=
                                `<option value="${grp.id_group}" ${selected}>${grp.nama_group}</option>`;
                        });
                        let options_departemen =
                            '<option value="">Pilih Departemen...</option>';
                        response.departemen.forEach(dep => {
                            const selected = departemen.includes(dep.id) ? 'selected' :
                                '';
                            options_departemen +=
                                `<option value="${dep.id}" ${selected}>${dep.nama_departemen}</option>`;
                        });
                        $('#group_id_update').html(options_group);
                        $('#parent_approval_id_update').html(options);
                        $('#departemen_id_update').html(options_departemen);
                    },
                    error: function(data) {
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },

                });
                $('#id_approval_update').val(id_approval);
                $('#nama_approval_update').val(nama_approval);
                $('#level_approval_update').val(level_approval);
                // $('#parent_approval_id_update').val(parent_approval_id);
                $('#group_id_update').val(group_id);
                $('#departemen_id_update').val(departemen);
                $('#modal_edit_approval').modal('show');
            });
            $(document).on('click', '.btn_save_update_approval', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var id_approval = $('#id_approval_update').val();
                var nama_approval = $('#nama_approval_update').val();
                var level_approval = $('#level_approval_update').val();
                var parent_approval_id = $('#parent_approval_id_update').val();
                var departemen_id_update = $('#departemen_id_update').val();
                var group_id = $('#group_id_update').val();
                var url = "{{ url('approval/approval_save_update') }}/" + get_holding;
                // console.log(url);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'id_approval': id_approval,
                        'nama_approval': nama_approval,
                        'level_approval': level_approval,
                        'parent_approval_id': parent_approval_id,
                        'group_id': group_id,
                        'departemen_id_update': departemen_id_update,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        loadTreeview();
                        $('#form_edit_approval')[0].reset();
                        $('#modal_edit_approval').modal('hide');
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 4500
                            })
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_edit_approval')[0].reset();
                        $('#modal_edit_approval').modal('hide');
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            });
            $(document).on('click', '.btn-delete-approval', function() {
                let id_approval = $(this).data('id_approval'); // ambil id dari tombol
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data approval akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) { // v9 pakai result.value, bukan result.isConfirmed
                        $.ajax({
                            url: "{{ url('/approval/approval_delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id_approval: id_approval,
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    allowOutsideClick: false,
                                    background: 'transparent',
                                    html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    onBeforeOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                Swal.close();
                                if (response.code == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    });
                                    loadTreeview();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message
                                    });
                                }
                            },
                            error: function() {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan pada server'
                                });
                            }
                        });
                    }
                });
            });
            $(document).on('click', '#btn-delete-group', function() {
                let id_group = $(this).data('id_group'); // ambil id dari tombol
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data approval akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) { // v9 pakai result.value, bukan result.isConfirmed
                        $.ajax({
                            url: "{{ url('/approval/group_delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id_group: id_group,
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    allowOutsideClick: false,
                                    background: 'transparent',
                                    html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    onBeforeOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                Swal.close();
                                if (response.code == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    });
                                    loadTreeview();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message
                                    });
                                }
                                $('#table_group').DataTable().ajax.reload();
                            },
                            error: function() {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan pada server'
                                });
                            }
                        });
                    }
                });
            });


            //Approval Group
            $(document).on('click', '#btn_add_group', function() {
                $('#nama_group').val('');
                $('#modal_tambah_group').modal('show');
                showSelectLoading('#menu_id');
                $.ajax({
                    url: "{{ url('approval/get_menu_group') }}" + "/" + holding,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(response) {
                        // console.log(response);
                        if (response.code == 200) {
                            $('#menu_id').empty();
                            let option_menu =
                                '<option value="">Pilih Menu (Mobile)...</option>';
                            response.data.forEach(value => {
                                if (value.main == 1) {
                                    var main = ' (Menu Utama)';
                                } else {
                                    var main = '';
                                }
                                option_menu +=
                                    `<option value="${value.id}" data-icon='${value.icon}'> ${value.name} ${main} </option>`;
                            });
                            $('#menu_id').html(option_menu);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                timer: 4500
                            })
                        }
                        hideSelectLoading('#menu_id');
                    }
                })
            });
            $(document).on('click', '.btn_save_add_group', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var nama_group = $('#nama_group').val();
                var menu_id = $('#menu_id').val();
                var url = "{{ url('approval/approval_group_save_add') }}/" + get_holding;
                // console.log(url);
                showTableLoading();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'nama_group': nama_group,
                        'menu_id': menu_id,
                        'holding_id': holding_id
                    },
                    success: function(response) {
                        hideTableLoading();
                        if (response.code == 200) {
                            $('#modal_tambah_group').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 4500
                            });
                            loadTreeview();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                timer: 4500
                            })
                        }
                        $('#table_group').DataTable().ajax.reload();
                    },
                    error: function() {
                        hideTableLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server',
                            timer: 4500
                        })
                    }
                });
            });
            loadTreeview();


            function loadTreeview() {
                showTableLoading();
                $.ajax({
                    url: "{{ url('approval/get_tree_approval') }}/" + holding,
                    method: "GET",
                    success: function(response) {
                        console.log(response);
                        hideTableLoading();
                        $('#tree-container').empty();
                        response.forEach(function(g) {

                            let chartId = 'chart-' + g.id;
                            if (g.tree.length == 0) {
                                $('#tree-container').append(
                                    `<div class="mb-5"><h5>${g.nama_group}</h5><button type="button" class="btn btn-sm btn-primary" data-id_group="${g.id}" id="btn_add_approval"><i class="mdi mdi-plus"></i>Tambah</button><div id="${chartId}" class="org-wrapper"></div></div>`
                                );
                            } else {
                                $('#tree-container').append(
                                    `<div class="mb-5"><h5>${g.nama_group}</h5><div id="${chartId}" class="org-wrapper"></div></div>`
                                );
                            }

                            $('#' + chartId).orgchart({
                                'data': g.tree[0],
                                'nodeContent': 'title',
                                'createNode': function($node, data) {
                                    // 1. Kosongkan konten default agar kita bisa buat struktur baru yang elegan
                                    $node.find('.title').remove();
                                    $node.find('.content').remove();

                                    // 2. Buat struktur kartu modern (Header + Body + Footer)
                                    const customHtml = `
                                    <div class="modern-card">
                                        <div class="card-header-gradient">
                                           
                                        </div>
                                        <div class="card-body card-chart">
                                            <div class="avatar-wrapper">
                                                <img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="avatar-img">
                                            </div>
                                            <div class="info-wrapper">
                                                <div class="emp-name">${data.name}</div>
                                                <div class="emp-title">${data.title}</div>
                                            </div>
                                        </div>
                                        <div class="card-actions">
                                            <button class="action-btn btn-add btn-add-bawahan" title="Tambah" data-id_approval="${data.id}" data-group_id="${data.group_id}" data-nama_group="${g.nama_group}" data-nama_approval="${data.name}"  data-parent_approval_id="${data.parent_approval_id}"><i class="mdi mdi-plus"></i></button>
                                            <button class="action-btn btn-edit btn-edit-approval" title="Edit" data-id_approval="${data.id}" data-group_id="${data.group_id}" data-nama_approval="${data.name}" data-level_approval="${data.level_approval}" data-departemen='${JSON.stringify(data.departemen_id)}' data-parent_approval_id="${data.parent_approval_id}"><i class="mdi mdi-pencil"></i></button>
                                            <button class="action-btn btn-delete btn-delete-approval" title="Hapus" data-id_approval="${data.id}"><i class="mdi mdi-trash-can"></i></button>
                                        </div>
                                    </div>
                                `;

                                    $node.append(customHtml);
                                },
                                'pan': true,
                                'zoom': true
                            });
                        });
                    },
                    error: function() {
                        hideTableLoading()
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada memuat data',
                        })
                    }
                });
            }

            $('#table_group').on('preXhr.dt', function(e, settings, data) {
                if (data.search && data.search.value) {
                    // kalau ada search value, biarkan loading default datatable
                    return;
                }
                showTableLoading();
            });

            // tutup Swal setelah data datatable selesai di-load
            // setelah datatable selesai load
            $('#table_group').on('xhr.dt', function(e, settings, json, xhr) {
                // tutup swal hanya kalau sebelumnya kita buka (bukan saat search)
                if (!settings.oPreviousSearch.sSearch) {
                    hideTableLoading();
                }
            });
            $(document).on('click', '#btn-edit-group', function() {
                var id_group = $(this).data('id_group');
                var nama_group = $(this).data('nama_group');
                var menu_update = $(this).data('menu_update');
                showSelectLoading('#menu_update');
                // Normalisasi menu_update jadi ARRAY of INT
                if (!menu_update) {
                    menu_update = [];
                } else if (typeof menu_update === 'string') {
                    try {
                        menu_update = JSON.parse(menu_update);
                    } catch (e) {
                        menu_update = [];
                    }
                }

                if (!Array.isArray(menu_update)) {
                    menu_update = [];
                }

                // Convert ke INT
                menu_update = menu_update
                    .map(id => parseInt(id, 10))
                    .filter(id => !isNaN(id));
                $('#id_group_update').val(id_group);
                $('#nama_group_update').val(nama_group);
                $.ajax({
                    url: "{{ url('approval/get_menu_group') }}" + "/" + holding,
                    method: "GET",
                    success: function(response) {
                        // console.log(response.data);
                        if (response.code == 200) {
                            hideSelectLoading('#menu_update');
                            menu_update = menu_update.map(id => parseInt(id, 10));
                            $('#menu_update').empty();
                            let option_menu =
                                '<option value="">Pilih Menu (Mobile)...</option>';
                            response.data.forEach(value => {
                                if (value.main == 1) {
                                    var main = ' (Menu Utama)';
                                } else {
                                    var main = '';
                                }
                                const selected = menu_update.includes(value.id) ?
                                    'selected' : '';
                                option_menu +=
                                    `<option value="${value.id}" data-icon='${value.icon}' ${selected}> ${value.name} ${main} </option>`;
                            });
                            $('#menu_update').html(option_menu);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        hideSelectLoading('#menu_update');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: data.responseJSON.message,
                            timer: 4500
                        })
                    }
                })
                $('#modal_edit_group').modal('show');
            });
            $(document).on('click', '.btn_save_update_group', function() {
                var get_holding = '{{ $holding->holding_code }}';
                var nama_group = $('#nama_group_update').val();
                var id_group = $('#id_group_update').val();
                var menu_update = $('#menu_update').val();
                var url = "{{ url('approval/approval_group_save_update') }}/" + get_holding;
                // console.log(url);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",

                        'nama_group': nama_group,
                        'id_group': id_group,
                        'menu_update': menu_update,
                        'holding_id': holding_id,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        $('#table_group').DataTable().ajax.reload();
                        $('#form_edit_group')[0].reset();
                        $('#modal_edit_group').modal('hide');
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 4500
                            })
                        } else if (data.code == 422) {
                            errors = data.errors;
                            errorMessages = '';
                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessages,
                                icon: 'error',
                                timer: 4500
                            })

                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                timer: 4500
                            })
                        }
                    },
                    error: function(data) {
                        // console.log(data);
                        $('#form_edit_group')[0].reset();
                        $('#modal_edit_group').modal('hide');
                        Swal.close();
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    }
                })
            })
        });
        //Group End
    </script>
@endsection
