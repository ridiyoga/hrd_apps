@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
        }

        /* Styling Tab Pills */
        .nav-pills .nav-link {
            color: #697a8d;
            background-color: #f0f2f4;
            border-radius: 6px;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background-color: #5a8dee;
            color: #fff;
            box-shadow: 0 2px 4px rgba(90, 141, 238, 0.4);
        }
    </style>
@endsection
@section('isi')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y" style="font-size: small;">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DAFTAR LOG</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-2">
                        <div class="d-flex justify-content-center w-100">
                            <div id="collapseFilterWrapper" class="sticky-top bg-white w-100"
                                style="z-index: 1020; max-width: 1200px;">
                                <div class="card-body">
                                    <div class="row gy-4 align-items-end justify-content-center">

                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-floating form-floating-outline">
                                                <div id="reportrange" style="white-space: nowrap;">
                                                    <button class="btn btn-outline-secondary w-100">
                                                        <span class="fw-bold">FILTER&nbsp;DATE&nbsp;:&nbsp;</span>
                                                        <span class="date_daterange"></span>
                                                        <input type="date" id="start_date" name="start_date" hidden
                                                            value="">
                                                        <input type="date" id="end_date" name="end_date" hidden
                                                            value="">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-6 col-sm-12">
                                            <button type="button" class="btn btn-info w-100" id="btn_filter">
                                                <i class="mdi mdi-filter-outline"></i>&nbsp;Filter
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2">

                        <div id="content_null">
                            <div class="alert alert-secondary" role="alert">
                                <div class="alert-content text-center">
                                    <svg width="120" height="120" viewBox="0 0 120 120" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">

                                        <rect x="25" y="25" width="70" height="70" rx="8" ry="8"
                                            stroke="#CED4DA" stroke-width="2" />

                                        <path d="M35 35H85" stroke="#E6E8EB" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M35 45H75" stroke="#E6E8EB" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M35 55H85" stroke="#E6E8EB" stroke-width="1.5" stroke-linecap="round" />

                                        <path d="M40 70L80 70" stroke="#7E8A9A" stroke-width="2" stroke-dasharray="4 4"
                                            stroke-linecap="round" />
                                        <path d="M40 80L70 80" stroke="#7E8A9A" stroke-width="2" stroke-dasharray="4 4"
                                            stroke-linecap="round" />

                                    </svg>
                                    <h4 class="alert-heading text-center">Tidak Ada Data</h4>
                                    <p class="text-center">Filter Data Terlebih Dahulu</p>
                                </div>
                            </div>
                        </div>
                        <div id="table_activity_div">
                            <div class="nav-align-top mb-4 px-2">

                                <ul class="nav nav-pills mb-3" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" id="admin-tab" data-bs-toggle="tab"
                                            data-bs-target="#admin" type="button" role="tab" aria-controls="admin"
                                            aria-selected="true">
                                            <span class="mdi mdi-message-text-clock-outline me-2"></span>
                                            admin
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link" id="mobile-tab" data-bs-toggle="tab"
                                            data-bs-target="#mobile" type="button" role="tab" aria-controls="mobile"
                                            aria-selected="false">
                                            <span class="mdi mdi-message-text-clock-outline me-2"></span>
                                            mobile
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="karir-tab" data-bs-toggle="tab"
                                            data-bs-target="#karir" type="button" role="tab" aria-controls="karir"
                                            aria-selected="false">
                                            <span class="mdi mdi-message-text-clock-outline me-2"></span>karir</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" id="system-tab" data-bs-toggle="tab"
                                            data-bs-target="#system" type="button" role="tab"
                                            aria-controls="system" aria-selected="false">
                                            <span class="mdi mdi-message-text-clock-outline me-2"></span>
                                            System Scheduler
                                        </button>
                                    </li>
                                </ul>
                            </div>


                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="admin" role="tabpanel"
                                    aria-labelledby="admin-tab">


                                    <table class="table" id="table_activity2" style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Menu</th>
                                                <th>Holding</th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>ID&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                    <table class="table" id="table_activity" style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Menu</th>
                                                <th>Holding</th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>ID&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="mobile" role="tabpanel" aria-labelledby="mobile-tab">
                                    <table class="table" id="table_activity_mobile2"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Menu</th>
                                                <th>Holding</th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>ID&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                    <table class="table" id="table_activity_mobile"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Menu</th>
                                                <th>Holding</th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>ID&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="karir" role="tabpanel" aria-labelledby="karir-tab">
                                    <table class="table" id="table_activity_karir2"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Menu</th>
                                                <th>Holding</th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>ID&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                    <table class="table" id="table_activity_karir"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Menu</th>
                                                <th>Holding</th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>ID&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                                    <table class="table" id="table_activity_system2"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                        </tbody>
                                    </table>
                                    <table class="table" id="table_activity_system"
                                        style="width: 100%; font-size: smaller;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>Username</th>
                                                <th>Aktivitas</th>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </th>
                                                <th>Nama&nbsp;Tabel</th>
                                                <th>Keterangan</th>
                                                <th>Dibuat&nbsp;Pada</th>
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
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables 2 -->
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables Buttons 3 -->
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>

    <!-- Export Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        var holding = '{{ $holding->holding_code }}';
        var start = moment().startOf('month');
        var end = moment().endOf('month');
        // var start = moment('2025-07-21');
        // var end = moment('2025-08-21');
        var lstart, lend;


        function cb(start, end) {
            lstart = start.format('YYYY-MM-DD');
            lend = end.format('YYYY-MM-DD');
            $('#start_date').val(lstart);
            $('#end_date').val(lend);
            $('#reportrange .date_daterange').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
            // console.log(start, end);

        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
        $('#table_activity_div').hide();
        $('#btn_filter').click(function(e) {
            $('#table_activity_div').show();
            var start_date = $('#start_date').val() || '';
            var end_date = $('#end_date').val() || '';

            console.log(start_date, end_date);

            $('#content_null').empty();
            // $('#table_recruitment2').empty();

            if ($.fn.DataTable.isDataTable('#table_activity2')) {
                $('#table_activity2').DataTable().clear().destroy();
            }

            if ($.fn.DataTable.isDataTable('#table_activity')) {
                $('#table_activity').DataTable().clear().destroy();
            }
            if ($.fn.DataTable.isDataTable('#table_activity_mobile2')) {
                $('#table_activity_mobile2').DataTable().clear().destroy();
            }

            if ($.fn.DataTable.isDataTable('#table_activity_mobile')) {
                $('#table_activity_mobile').DataTable().clear().destroy();
            }
            if ($.fn.DataTable.isDataTable('#table_activity_karir2')) {
                $('#table_activity_karir2').DataTable().clear().destroy();
            }

            if ($.fn.DataTable.isDataTable('#table_activity_karir')) {
                $('#table_activity_karir').DataTable().clear().destroy();
            }
            if ($.fn.DataTable.isDataTable('#table_activity_system2')) {
                $('#table_activity_system2').DataTable().clear().destroy();
            }

            if ($.fn.DataTable.isDataTable('#table_activity_system')) {
                $('#table_activity_system').DataTable().clear().destroy();
            }
            $('#table_activity2').hide();
            var table1_exc = $('#table_activity2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('/log/dt_activity') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },

                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LOG WEBSITE',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'JABATAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'holding_category',
                        name: 'holding_category'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'id_table',
                        name: 'id_table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });
            var table1 = $('#table_activity').DataTable({
                pageLength: 10,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/log/dt_activity') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'holding_category',
                        name: 'holding_category'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'id_table',
                        name: 'id_table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });


            $(document).on('click', '#admin-tab', function() {
                // console.log('asoy');
                table1.ajax.reload();
            });
            $('#table_activity_mobile2').hide();
            var table2_exc = $('#table_activity_mobile2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('/log/dt_activity_mobile') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LOG MOBILE',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'JABATAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'holding_category',
                        name: 'holding_category'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'id_table',
                        name: 'id_table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });
            var table2 = $('#table_activity_mobile').DataTable({
                pageLength: 10,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/log/dt_activity_mobile') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'holding_category',
                        name: 'holding_category'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'id_table',
                        name: 'id_table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });

            $(document).on('click', '#mobile-tab', function() {
                // console.log('asoy');/
                table2.ajax.reload();
            });
            $('#table_activity_karir2').hide();
            var table3_exc = $('#table_activity_karir2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('/log/dt_activity_karir') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LOG PORTAL KARIR',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'JABATAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'holding_category',
                        name: 'holding_category'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'id_table',
                        name: 'id_table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });
            var table3 = $('#table_activity_karir').DataTable({
                pageLength: 10,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/log/dt_activity_karir') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'menu_name',
                        name: 'menu_name'
                    },
                    {
                        data: 'holding_category',
                        name: 'holding_category'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'id_table',
                        name: 'id_table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]

            });
            $('#table_activity_system2').hide();
            var table4_exc = $('#table_activity_system2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('/log/dt_activity_system') }}" + '/' + holding,

                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'LOG MESIN FINGER',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'JABATAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            })
            var table4 = $('#table_activity_system').DataTable({
                pageLength: 10,
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/log/dt_activity_system') }}" + '/' + holding,
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    }
                },
                columns: [{
                        data: "id",
                        name: "id",
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'activity',
                        name: 'activity'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'table',
                        name: 'table'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                order: [
                    [0, 'desc']
                ]
            });
            $(document).on('click', '#karir-tab', function() {
                // console.log(table3);
                table3.ajax.reload();
            });
            $(document).on('click', '#system-tab', function() {
                // console.log(table3);
                table4.ajax.reload();
            });
        });



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
        })
    </script>
@endsection
