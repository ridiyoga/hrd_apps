@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style type="text/css">
        .my-swal {
            z-index: X;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card.shadow-sm:hover {
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
        }

        .card-header {
            font-weight: 600;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            padding: 1rem 1.25rem;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f2f4f7;
            color: #566a7f;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            border-top: none;
            padding: 10px 10px;
            border-top: none !important;
        }

        .table tbody td {
            font-size: 0.875rem;
            color: #697a8d;
            vertical-align: middle;
            padding: 12px 15px;
            font-weight: 400;
        }

        .swal2-container {
            z-index: 9999 !important;
        }

        /* Hover option */
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

        .form-floating-outline {
            position: relative;
        }

        .form-select.border-bottom:focus {
            border-bottom: 2px solid #6f24b6 !important;
            /* Warna biru primary */
            box-shadow: none;
        }

        .table .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
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
                    <div class="card-header master-table-card-content">
                        <div
                            class="card-header bg-white d-flex justify-content-between align-items-center master-report-toolbar">
                            <h6 class="mb-0 text-dark"><i class="mdi mdi-file-document-multiple-outline me-2"></i>KATEGORI
                                CUTI</h6>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                        <div class="card-body master-table-pane-body">
                            <div class="table-responsive mt-2 master-table-toolbar">
                                <table class="table table-sm table-hover mb-0" id="tabel_cuti_kategori2"
                                    style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Opsi</th>
                                            <th class="ps-3">
                                                Jenis&nbsp;Cuti&nbsp;Diluar&nbsp;Tahunan</th>
                                            <th class="text-center">Jatah&nbsp;(Max)</th>
                                            <th>Gender</th>
                                            <th>Status&nbsp;Nikah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="master-table-area mt-2">
                                <table class="table table-sm table-hover mb-0" id="tabel_cuti_kategori" style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">
                                                Jenis&nbsp;Cuti&nbsp;Diluar&nbsp;Tahunan</th>
                                            <th class="text-center">Jatah&nbsp;(Max)</th>
                                            <th>Gender</th>
                                            <th>Status&nbsp;Nikah</th>
                                            <th>Status</th>
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
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
        {{-- end tambah kategori cuti --}}
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

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

            var tablecuti = $('#tabel_cuti_kategori').DataTable({
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('dt_cuti_kategori') }}",
                },
                columns: [{
                        data: 'nama_cuti',
                        name: 'nama_cuti'
                    },
                    {
                        data: 'jumlah_hari',
                        name: 'jumlah_hari',
                        className: 'text-center'
                    },
                    {
                        data: 'target_gender',
                        name: 'target_gender',
                    },
                    {
                        data: 'target_status_nikah',
                        name: 'target_status_nikah',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
            $('#tabel_cuti_kategori2').hide();
            var tablecuti = $('#tabel_cuti_kategori2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('dt_cuti_kategori') }}",
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'MASTER SHIFT',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'MASTER_CUTI_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: 'option',
                        name: 'option',
                    },
                    {
                        data: 'nama_cuti',
                        name: 'nama_cuti'
                    },
                    {
                        data: 'jumlah_hari',
                        name: 'jumlah_hari',
                        className: 'text-center'
                    },
                    {
                        data: 'target_gender',
                        name: 'target_gender',
                    },
                    {
                        data: 'target_status_nikah',
                        name: 'target_status_nikah',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });


        });
    </script>
@endsection
