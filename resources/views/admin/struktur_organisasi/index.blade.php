@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@2.1.9/dist/css/jquery.orgchart.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style type="text/css">
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

        .chart-wrapper {
            overflow: auto;
            width: 100%;
            height: 80vh;
            background: #fff;
        }

        .swal2-container {
            position: fixed !important;
            z-index: 2147483647 !important;
            /* max safe z-index */
        }

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

        /* BIKIN NODE AUTO SIZE */
        .orgchart .node {
            min-width: 180px;
            max-width: 360px;
            min-height: 90px;
            /* PENTING */
            height: auto;
            /* jangan pakai !important */
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
            width: 100%;
            height: auto;
            overflow: auto;
        }

        /* ===============================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        SELECT LOADING
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ================================ */
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
                            <h5 class="card-title m-0 me-2">STRUKTUR ORGANISASI</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- @if ($struktur == 0)
                        @endif --}}
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-primary dropdown-toggle hide-arrow" data-bs-toggle="dropdown"
                                aria-expanded="false" data-bs-offset="0,10" href="javascript:void(0);">
                                <i class="mdi mdi-download mdi-24px"></i>
                                <span class="sr-only">&nbsp;DOWNLOAD STRUKTUR ORGANISASI</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                                <li class="dropdown-item">
                                    <a href="javascript:void(0);" id="btn_download_image">
                                        <i class="mdi mdi-file-image-outline me-2"></i>
                                        <span>Download JPG</span>
                                    </a>
                                </li>
                                <li class="dropdown-item">
                                    <a href="javascript:void(0);" id="btn_download_pdf">
                                        <i class="mdi mdi-file-document-outline me-2"></i>
                                        <span>Download PDF</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div id="btn-tree"></div>
                        <div class="tree-wrapper mt-4 ">

                            <div id="tree-container">
                                <div id="tableLoading" class="table-loading d-none">
                                    <div class="loading-content">
                                        <div class="spinner"></div>
                                        <span>Memuat data...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_struktur" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Buat Struktur</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_add_struktur">
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-control asoy" id="jabatan_add" name="jabatan" autofocus
                                        value="">
                                        <option value="">Pilih jabatan</option>
                                        @foreach ($jabatan as $jabt)
                                            <option value="{{ $jabt->id }}">
                                                {{ $jabt->nama_jabatan }} ({{ $jabt->Holding->holding_category }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="jabatan">jabatan</label>

                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="kuota_atasan_add" name="kuota_atasan" class="form-control" />
                                    <label for="kuota_atasan">Kuota Atasan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-control asoy" id="bawahan_add" name="bawahan" autofocus
                                        value="">
                                        <option value="">Pilih Bawahan</option>
                                        @foreach ($jabatan as $jabt)
                                            <option value="{{ $jabt->id }}">
                                                {{ $jabt->nama_jabatan }} ({{ $jabt->Holding->holding_category }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="bawahan">Bawahan</label>

                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="kuota_bawahan_add" name="kuota_bawahan"
                                        class="form-control" />
                                    <label for="kuota_bawahan">Kuota Bawahan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-secondary m-1" data-bs-dismiss="modal">
                                Tutup
                            </button>
                            <button type="button" class="btn btn-primary m-1" id="btn_add_struktur">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_struktur_update" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Update Kuota</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_add_struktur">
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="hidden" id="id_update" name="id" class="form-control" />
                                    <input type="text" id="jabatan_update" name="jabatan" class="form-control"
                                        disabled />
                                    <label for="jabatan">Jabatan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="parent_update" name="parent">
                                    </select>
                                    <label for="parent_update" class="form-label">Atasan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="kuota_karyawan_update" name="kuota_karyawan"
                                        class="form-control" />
                                    <label for="kuota_karyawan">Kuota Karyawan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="status_pegawai_update2" name="status_pegawai">
                                        <option value="">PKWT & Organik</option>
                                        <option value="1">Outsourching</option>
                                    </select>
                                    <label for="status_pegawai_update2" class="form-label">Status</label>
                                </div>
                            </div>
                            {{-- <input type="text" name="" id="status_pegawai_update2"> --}}
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-secondary m-1" data-bs-dismiss="modal">
                                Tutup
                            </button>
                            <button type="button" class="btn btn-primary m-1" id="btn_update_struktur">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_struktur_bawahan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Bawahan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_add_struktur">
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="hidden" id="id_atasan" name="id" class="form-control" />
                                    <input type="text" id="jabatan_bawahan" name="jabatan" class="form-control"
                                        disabled />
                                    <label for="jabatan">Jabatan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" asoy id="nama_dept_update" name="nama_dept" autofocus
                                        value="">
                                        <option value=""> Pilih Departemen
                                        </option>
                                        @foreach ($data_dept as $data)
                                            <option value="{{ $data->id }}">
                                                {{ $data->nama_departemen }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="nama_dept_update">Nama Departemen</label>
                                </div>
                                @error('nama_departemen_update')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" asoy id="nama_divisi_update" name="nama_divisi_update"
                                        autofocus value="">
                                    </select>
                                    <label for="nama_divisi_update">Nama Divisi</label>
                                    <div class="select-loader d-none">
                                        <div class="dot-loader">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                @error('nama_divisi_update')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" asoy id="nama_bagian_update" name="nama_bagian_update"
                                        autofocus value="">
                                    </select>
                                    <label for="nama_bagian_update">Nama Bagian</label>
                                    <div class="select-loader d-none">
                                        <div class="dot-loader">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                @error('nama_bagian_update')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" asoy id="bawahan_update" name="bawahan">
                                    </select>
                                    <label for="bawahan_update" class="form-label">Jabatan</label>
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
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" asoy id="status_pegawai_update" name="bawahan">
                                        <option value="">PKWT & Organik</option>
                                        <option value="1">Outsourching</option>
                                    </select>
                                    <label for="status_pegawai_update" class="form-label">Status</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="kuota_karyawan_bawahan" name="kuota_karyawan"
                                        class="form-control" />
                                    <label for="kuota_karyawan">Kuota Karyawan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-secondary m-1" data-bs-dismiss="modal">
                                Tutup
                            </button>
                            <button type="button" class="btn btn-primary m-1" id="btn_bawahan_struktur">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_karyawan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Daftar Karyawan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table" id="tabel_karyawan" style="width: 100%; font-size: small;">
                        <thead class="table-primary">
                            <tr>
                                <th>No.</th>
                                <th>Nama&nbsp;Karyawan</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.9/js/jquery.orgchart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            var menu_id = $('#asoy').val();
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
            // console.log(menu_id);

            loadTreeview();
            var holding_id = '{{ $holding->id }}'

            function loadTreeview() {
                $.ajax({
                    url: "{{ url('/get_tree_struktur/' . $holding->holding_code) }}",
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        showTableLoading();
                    },
                    success: function(response) {
                        hideTableLoading();
                        $('#tree-container').empty(); // container utama untuk semua org chart
                        $('#btn-tree').empty(); // container utama untuk semua org chart
                        // console.log(response.length);
                        if (response.length == 0) {
                            $('#btn-tree').append(
                                `<button type="button" class="btn btn-sm btn-primary waves-effect waves-light my-3"
                                id="btn_struktur"><i class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>`
                            );
                        }
                        response.forEach(function(g) {
                            // buat wrapper org chart per root
                            let chartId = 'chart-' + g.id;
                            $('#tree-container').append(
                                `<div class="mb-5"><div id="${chartId}" class="org-wrapper"></div></div>`
                            );

                            // render orgchart menggunakan plugin
                            $('#' + chartId).orgchart({
                                'data': g, // langsung data root dari controller
                                'nodeContent': 'title', // gunakan field title
                                createNode: function($node, data) {
                                    // tambahkan tombol edit & delete jika perlu
                                    const UserKaryawan =
                                        `<span class="click_asoy" data-id_jabatan="${data.id_jabatan}" >${data.UserKaryawan}</span>`;
                                    const Kuota =
                                        `<span class="click_asoy" data-id_jabatan="${data.id_jabatan}" >${data.kuota}</span>`;
                                    const actionHtml = `
                                <div class="row mt-2">
                                    <div class="col-lg-4">
                                        <a href="javascript:void(0)" id="btn-update-struktur" class="btn-edit-node" 
                                            data-id="${data.id}" 
                                            data-kuota_karyawan='${data.kuota_karyawan}'
                                            data-jabatan='${data.jabatan}'
                                            data-status_pegawai='${data.status_pegawai}'
                                            >
                                            <i class="mdi mdi-pencil text-secondary mdi-24px"></i>
                                        </a>
                                    </div>
                                    <div class="col-lg-4">
                                        <a href="javascript:void(0)" id="btn-add-bawahan" class="btn-edit-node" 
                                            data-id="${data.id}" 
                                            data-kuota_karyawan='${data.kuota_karyawan}'
                                            data-jabatan='${data.jabatan}'
                                            data-level='${data.level}'
                                            >
                                            <i class="mdi mdi-plus-thick text-secondary mdi-24px"></i>
                                        </a>
                                    </div>
                                    <div class="col-lg-4">
                                        <a href="javascript:void(0)" id="btn-delete-struktur" class="button" 
                                            data-id="${data.id}">
                                            <i class="mdi mdi-delete text-danger mdi-24px"></i>
                                        </a>
                                    </div>
                                </div>
                            `;
                                    $node.find('.content').append(UserKaryawan,
                                        Kuota,
                                        actionHtml);
                                },
                                'pan': true,
                                'zoom': true
                            });
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data struktur organisasi.',
                        })
                    }
                });
            }
            $(document).on('click', '#btn_download_image', function() {

                const chart = document.querySelector('#tree-container');

                // 🔥 CLONE CHART
                const clone = chart.cloneNode(true);

                // container sementara (di luar layar)
                const temp = document.createElement('div');
                temp.style.position = 'fixed';
                temp.style.left = '-10000px';
                temp.style.top = '0';
                temp.style.background = '#fff';

                // RESET semua transform di clone
                clone.style.transform = 'none';
                clone.style.position = 'relative';
                clone.style.left = '0';
                clone.style.top = '0';

                temp.appendChild(clone);
                document.body.appendChild(temp);

                // hitung ukuran FULL
                const width = clone.scrollWidth;
                const height = clone.scrollHeight;

                html2canvas(clone, {
                    backgroundColor: '#ffffff',
                    scale: 2,
                    width: width,
                    height: height,
                    windowWidth: width,
                    windowHeight: height
                }).then(canvas => {

                    document.body.removeChild(temp); // bersihkan DOM

                    const link = document.createElement('a');
                    link.download = 'STRUKTUR ORGANISASI.jpg';
                    link.href = canvas.toDataURL('image/jpeg', 1.0);
                    link.click();
                });
            });
            $('#btn_download_pdf').on('click', function() {

                const original = document.querySelector('#tree-container');

                // CLONE
                const clone = original.cloneNode(true);
                clone.style.transform = 'none';
                clone.style.position = 'relative';

                const temp = document.createElement('div');
                temp.style.position = 'fixed';
                temp.style.left = '-10000px';
                temp.style.top = '0';
                temp.style.background = '#fff';

                temp.appendChild(clone);
                document.body.appendChild(temp);

                const width = clone.scrollWidth;
                const height = clone.scrollHeight;

                html2canvas(clone, {
                    backgroundColor: '#ffffff',
                    scale: 2,
                    width: width,
                    height: height,
                    windowWidth: width,
                    windowHeight: height
                }).then(canvas => {

                    const imgData = canvas.toDataURL('image/jpeg', 1.0);

                    const {
                        jsPDF
                    } = window.jspdf;

                    // PDF ukuran custom (sesuai chart)
                    const pdf = new jsPDF({
                        orientation: width > height ? 'landscape' : 'portrait',
                        unit: 'px',
                        format: [width, height]
                    });

                    pdf.addImage(imgData, 'JPEG', 0, 0, width, height);
                    pdf.save('STRUKTUR ORGANISASI.pdf');

                    document.body.removeChild(temp);
                });
            });

            $(document).on('click', '#btn_struktur', function() {
                // console.log('asoy');
                $('#modal_struktur').modal('show');
            });
            $(document).on('click', '.click_asoy', function() {

                let id_jabatan = $(this).data('id_jabatan');
                let url = "{{ url('/struktur_organisasi/dt_karyawan') }}" + "/" + id_jabatan;
                $('#tabel_karyawan').DataTable().destroy()
                // console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    // data: {
                    //     id_dept: id_dept
                    // },
                    // beforeSend: function() {
                    //     Swal.fire({
                    //         title: 'Mohon tunggu...',
                    //         text: 'Sedang memproses data',
                    //         allowOutsideClick: false,
                    //         didOpen: () => {
                    //             Swal.showLoading();
                    //         }
                    //     });
                    // },
                    success: function(response) {
                        Swal.close();

                        // console.log(response);
                        $('#modal_karyawan').modal('show');
                        var table = $('#tabel_karyawan').DataTable({
                            pageLength: 50,
                            "scrollY": true,
                            "scrollX": true,
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: "{{ url('/struktur_organisasi/dt_karyawan') }}" +
                                    "/" +
                                    id_jabatan,
                            },
                            columns: [{
                                    data: "id_approval",

                                    render: function(data, type, row, meta) {
                                        return meta.row + meta.settings
                                            ._iDisplayStart + 1;
                                    }
                                },

                                {
                                    data: 'nama',
                                    name: 'nama'
                                },
                            ],
                            order: [
                                [1, 'asc']
                            ]
                        });

                    },
                    error: function(data) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.responseJSON.message ||
                                'Terjadi kesalahan pada server',
                        });
                    },

                })
            });
            $('#jabatan_add').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih jabatan",
                allowClear: true,
                dropdownParent: $('#modal_struktur')
            });
            $('#bawahan_add').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih jabatan",
                allowClear: true,
                dropdownParent: $('#modal_struktur')
            });
            $('#nama_dept_update').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Departemen",
                allowClear: true,
                dropdownParent: $('#modal_struktur_bawahan')
            });
            $('#nama_divisi_update').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Divisi",
                allowClear: true,
                dropdownParent: $('#modal_struktur_bawahan')
            });
            $('#nama_bagian_update').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Bagian",
                allowClear: true,
                dropdownParent: $('#modal_struktur_bawahan')
            });
            $('#bawahan_update').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Bawahan",
                allowClear: true,
                dropdownParent: $('#modal_struktur_bawahan')
            });

            $('#parent_update').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih Atasan",
                allowClear: true,
                dropdownParent: $('#modal_struktur_update')
            });
            var table = $('#table_struktur').DataTable({
                pageLength: 50,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/dt_struktur/') }}",
                },
                columns: [{
                        data: "jabatan",
                        name: "jabatan"
                    },
                    {
                        data: "parent",
                        name: "parent"
                    },
                    {
                        data: "kuota_karyawan",
                        name: "kuota_karyawan"
                    },
                    {
                        data: "option",
                        name: "option"
                    },
                ],
                order: [
                    [2, 'asc']
                ]
            });

            function showSelectLoading(selectId) {
                $(selectId).prop('disabled', true)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .removeClass('d-none');
            }

            function showTableLoading() {
                $('#tableLoading').removeClass('d-none');
            }

            function hideTableLoading() {
                $('#tableLoading').addClass('d-none');
            }

            function hideSelectLoading(selectId) {
                $(selectId).prop('disabled', false)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .addClass('d-none');
            }
            $('#btn_add_struktur').click(function() {
                $('#modal_struktur').modal('hide');
                var jabatan = $('#jabatan_add').val();
                var bawahan = $('#bawahan_add').val();
                var kuota_atasan = $('#kuota_atasan_add').val();
                var kuota_bawahan = $('#kuota_bawahan_add').val();
                // console.log(id_karyawan, jenjang, jurusan, tahun_masuk, tahun_lulus, nama_instansi);
                $.ajax({
                    url: "{{ url('/struktur_store/' . $holding->id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        jabatan: jabatan,
                        bawahan: bawahan,
                        kuota_atasan: kuota_atasan,
                        kuota_bawahan: kuota_bawahan,
                        menu_id: menu_id
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mohon tunggu...',
                            text: 'Sedang memproses data',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            $('#btn_struktur').hide()
                            $('#modal_struktur').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            })
                        } else if (data.code == 400) {
                            let errors = response.errors;
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}<br>`;
                                });
                            });

                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Gagal',
                                html: errorMessages,
                            });
                        } else {
                            let errors = '';
                            $.each(response.message, function(key, value) {
                                errors += value.join('<br>') + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errors,
                            })
                        }
                        loadTreeview();
                        $('#form_add_struktur').trigger('reset');
                        // table.ajax.reload();
                    },
                    error: function(data) {
                        Swal.close();
                        // console.log(data);
                        $('#form_add_struktur').trigger('reset');
                        $('#modal_struktur').modal('hide');
                        let errors = '';
                        $.each(data.responseJSON.message, function(key, value) {
                            errors += value.join('<br>') + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errors,
                        });

                    }
                });

            });
            $(document).on('click', '#btn-delete-struktur', function() {
                let id = $(this).data('id'); // ambil id dari tombol
                // console.log(menu_id);
                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data struktur akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) { // v9 pakai result.value, bukan result.isConfirmed
                        $.ajax({
                            url: "{{ url('/struktur/struktur_delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id,
                                menu_id: menu_id

                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Mohon tunggu...',
                                    text: 'Sedang menghapus data',
                                    allowOutsideClick: false,
                                    onBeforeOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                Swal.close();
                                table.ajax.reload();
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
            $(document).on('click', '#btn-update-struktur', function() {
                var id = $(this).data('id');
                var kuota_karyawan = $(this).data('kuota_karyawan');
                var jabatan = $(this).data('jabatan');
                var status_pegawai = $(this).data('status_pegawai');

                // console.log(status_pegawai);
                $('#id_update').val(id);
                $('#kuota_karyawan_update').val(kuota_karyawan);
                $('#status_pegawai_update2').val(status_pegawai);
                // $('#jabatan_update').val(jabatan);

                // if (status_pegawai == null || status_pegawai == "") {
                //     $('#status_pegawai_update2').val('');
                // } else {
                //     $('#status_pegawai_update2').val(status_pegawai.toString());
                // }
                Swal.fire({
                    title: 'Mohon Tunggu...',
                    html: 'Sedang memuat data...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: "{{ url('struktur_get_parent') }}" + '/' + id + '/' + holding_id,
                    type: "GET",
                    dataType: "json",
                    beforeSend: function() {
                        $('#parent_update').html(`
                            <option value="">Loading...</option>
                        `);
                    },
                    success: function(response) {
                        // let html = '<'
                        // console.log(response.struktur_get);
                        let html =
                            `<option value="${response.struktur_parent?.id ?? ''}">${response.struktur_parent?.parent?.jabatan.nama_jabatan ?? '-'}&nbsp;BAGIAN&nbsp;${response.struktur_parent?.parent?.jabatan.bagian.nama_bagian ?? '-'}</option>`;
                        response.struktur_get.forEach((item) => {
                            html +=
                                `<option value="${item.id ?? ''}"> ${item?.jabatan.nama_jabatan ?? '-'}&nbsp;BAGIAN&nbsp;${item?.jabatan.bagian.nama_bagian ?? '-'}</option>`;
                        });
                        $('#parent_update').html(html);

                        Swal.close();

                        $('#modal_struktur_update').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data gagal dimuat.'
                        });
                    }
                });

            });
            // $(document).on('select2:open', '#parent_update', function() {
            //     var id = $('#id_update').val();
            //     // console.log(id);

            // });
            $('#btn_update_struktur').click(function() {
                $('#modal_struktur_update').modal('hide');
                var id = $('#id_update').val();
                var kuota_karyawan = $('#kuota_karyawan_update').val();
                var parent_update = $('#parent_update').val();
                var status_pegawai = $('#status_pegawai_update2').val();
                // console.log(id_karyawan, jenjang, jurusan, tahun_masuk, tahun_lulus, nama_instansi);
                $.ajax({
                    url: "{{ url('/struktur_update/' . $holding->id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        kuota_karyawan: kuota_karyawan,
                        menu_id: menu_id,
                        status_pegawai: status_pegawai,
                        parent: parent_update
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mohon tunggu...',
                            text: 'Sedang memproses data',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            $('#btn_struktur').hide()
                            $('#modal_struktur_update').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            })
                        } else {
                            let errors = '';
                            $.each(response.message, function(key, value) {
                                errors += value.join('<br>') + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errors,
                            })
                        }
                        loadTreeview();

                        $('.form-select')[0].reset();
                        table.ajax.reload();
                    },
                    error: function(data) {
                        Swal.close();
                        // console.log(data);
                        $('#form_add_struktur').trigger('reset');
                        $('#modal_struktur').modal('hide');
                        let errors = '';
                        $.each(data.responseJSON.message, function(key, value) {
                            errors += value.join('<br>') + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errors,
                        });

                    }
                });

            });
            $(document).on('click', '#btn-add-bawahan', function() {

                var id = $(this).data('id');
                var level = $(this).data('level');
                var holding_code = '{{ $holding->holding_code }}'
                var kuota_karyawan = $(this).data('kuota_karyawan');
                var jabatan = $(this).data('jabatan');
                // console.log(kuota_karyawan);
                $('#id_atasan').val(id);
                $('#kuota_karyawan_bawahan').val(kuota_karyawan);
                $('#jabatan_bawahan').val(jabatan);
                $(document)
                    .off('change', '#nama_dept_update')
                    .on('change', '#nama_dept_update', function() {
                        showSelectLoading('#nama_divisi_update');
                        showSelectLoading('#nama_bagian_update');
                        showSelectLoading('#bawahan_update');
                        let id_dept = $('#nama_dept_update').val();
                        if (!id_dept) {
                            hideSelectLoading('#nama_divisi_update');
                            hideSelectLoading('#nama_bagian_update');
                            hideSelectLoading('#bawahan_update');

                            $('#nama_divisi_update').html('');
                            $('#nama_bagian_update').html('');
                            $('#bawahan_update').html('');

                            return;
                        }
                        // console.log(id_dept);
                        let url = "{{ url('/struktur_bawahan/divisi/') }}" + "/" + id_dept + "/" +
                            holding_id;
                        // console.log(url);
                        $.ajax({
                            url: url,
                            method: 'GET',
                            contentType: false,
                            cache: false,
                            processData: false,

                            success: function(response) {
                                hideSelectLoading('#nama_divisi_update');

                                $('#nama_divisi_update').html(response);
                            },
                            error: function(data) {
                                hideSelectLoading('#nama_divisi_update');
                                hideSelectLoading('#nama_bagian_update');
                                hideSelectLoading('#bawahan_update');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.responseJSON.message
                                });

                            },

                        })
                    })
                $(document)
                    .off('change', '#nama_divisi_update')
                    .on('change', '#nama_divisi_update', function() {
                        showSelectLoading('#nama_bagian_update');
                        showSelectLoading('#bawahan_update');
                        let id_divisi = $(this).val();
                        if (!id_divisi) {
                            hideSelectLoading('#nama_bagian_update');
                            hideSelectLoading('#bawahan_update');

                            $('#nama_bagian_update').html('');
                            $('#bawahan_update').html('');

                            return;
                        }
                        let url = "{{ url('/struktur_bawahan/bagian') }}" + "/" + id_divisi;

                        $.ajax({
                            url: url,
                            method: 'GET',
                            contentType: false,
                            cache: false,
                            processData: false,

                            success: function(response) {
                                hideSelectLoading('#nama_bagian_update');
                                $('#nama_bagian_update').html(response);
                            },
                            error: function(data) {
                                hideSelectLoading('#nama_divisi_update');
                                hideSelectLoading('#nama_bagian_update');
                                hideSelectLoading('#jabatan_update');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.responseJSON.message
                                });
                            },

                        })
                    })
                $(document)
                    .off('change', '#nama_bagian_update')
                    .on('change', '#nama_bagian_update', function() {
                        showSelectLoading('#bawahan_update');
                        let id_bagian = $(this).val();
                        if (!id_bagian) {
                            hideSelectLoading('#bawahan_update');

                            $('#bawahan_update').html('');

                            return;
                        }
                        let url = "{{ url('/struktur_bawahan_select') }}" + "/" +
                            id_bagian;
                        $.ajax({
                            url: url,
                            method: 'GET',
                            contentType: false,
                            cache: false,
                            processData: false,

                            success: function(response) {
                                hideSelectLoading('#bawahan_update');

                                $('#bawahan_update').html(response);
                            },
                            error: function(data) {
                                hideSelectLoading('#bawahan_update');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.responseJSON.message
                                });
                            },

                        })
                    });
                $('#modal_struktur_bawahan').modal('show');
            });
            $('#btn_bawahan_struktur').click(function() {
                $('#modal_struktur_bawahan').modal('hide');
                var id = $('#id_atasan').val();
                var kuota_karyawan = $('#kuota_karyawan_bawahan').val();
                var bawahan = $('#bawahan_update').val();
                var status_pegawai = $('#status_pegawai_update').val();
                // console.log(id_karyawan, jenjang, jurusan, tahun_masuk, tahun_lulus, nama_instansi);
                $.ajax({
                    url: "{{ url('/struktur_bawahan/' . $holding->id) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        kuota_karyawan: kuota_karyawan,
                        bawahan: bawahan,
                        menu_id: menu_id,
                        status_pegawai: status_pegawai,
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mohon tunggu...',
                            text: 'Sedang memproses data',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.code == 200) {
                            $('#btn_struktur').hide()
                            $('#modal_struktur_bawahan').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            })
                        } else if (response.code == 400) {
                            let errors = response.errors;
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            Swal.fire({
                                text: errorMessages,
                                icon: 'warning',
                                timer: 4500
                            });
                        } else {
                            let errors = '';
                            $.each(response.message, function(key, value) {
                                errors += value.join('<br>') + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errors,
                            })
                        }
                        loadTreeview();

                        $('.form-select').val(null).trigger('change');
                        table.ajax.reload();
                    },
                    error: function(data) {
                        Swal.close();
                        // console.log(data);
                        $('#form_add_struktur').trigger('reset');
                        $('#modal_struktur').modal('hide');
                        let errors = '';
                        $.each(data.responseJSON.message, function(key, value) {
                            errors += value.join('<br>') + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errors,
                        });

                    }
                });

            });
        });
    </script>
@endsection
