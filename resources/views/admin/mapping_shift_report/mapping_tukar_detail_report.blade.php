@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
@endsection
@section('isi')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-camera-switch me-2"></i>DETAIL LAPORAN TUKAR SHIFT
                            </h5>
                            <input type="hidden" id="menu_id_add" value="">
                        </div>

                    </div>
                    <div class="card-body">
                        <hr class="my-3">
                        <div class="row g-1" style="max-width: 500px; font-size: 0.85rem;">

                            <!-- Baris Pembuat -->
                            <div class="col-4 text-secondary py-0.5">PEMBUAT</div>
                            <div class="col-8 text-dark py-0.5">: {{ $head->input_by_name }}</div>

                            <!-- Baris No Legal -->
                            <div class="col-4 text-secondary py-0.5">NO. LEGAL</div>
                            <div class="col-8 text-dark py-0.5">: {{ $head->no_form }}</div>

                            <!-- Baris Tanggal Mulai -->
                            {{-- <div class="col-4 text-secondary py-0.5">TANGGAL MULAI</div>
                            <div class="col-8 text-dark py-0.5">: <i
                                    class="mdi mdi-calendar-clock me-1 text-muted"></i>{{ $head->start_date }}</div>

                            <!-- Baris Tanggal Akhir -->
                            <div class="col-4 text-secondary py-0.5">TANGGAL AKHIR</div>
                            <div class="col-8 text-dark py-0.5">: <i
                                    class="mdi mdi-calendar-check me-1 text-muted"></i>{{ $head->end_date }}</div> --}}

                        </div>
                        <hr class="my-3">
                        <table class="table table-hover" id="table_detail_shift2" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>NAMA&nbsp;KARYAWAN</th>
                                    <th>HARI</th>
                                    <th>TANGGAL&nbsp;MASUK</th>
                                    <th>SHIFT</th>
                                    <th>PENGUBAHAN</th>
                                    <th>SHIFT&nbsp;ORIGINAL</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 small">
                            </tbody>
                        </table>
                        <table class="table table-hover" id="table_detail_shift" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>NAMA&nbsp;KARYAWAN</th>
                                    <th>HARI</th>
                                    <th>TANGGAL&nbsp;MASUK</th>
                                    <th>SHIFT</th>
                                    <th>PENGUBAHAN</th>
                                    <th>SHIFT&nbsp;ORIGINAL</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 small">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

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
        let holding = '{{ $holding->holding_code }}';
        let id = '{{ $id }}';
        var table = $('#table_detail_shift').DataTable({
            pageLength: 25,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            // dom: 'Bfrtip',
            ajax: {
                url: "{{ url('/report/dt_laporan_detail_tukar') }}" + "/" + holding + "/" + id,

            },
            // buttons: [{

            //     extend: 'excelHtml5',
            //     className: 'btn btn-sm btn-success',
            //     text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
            //     titleAttr: 'Excel',
            //     title: 'LAPORAN MAPPING SHIFT',
            //     messageTop: '{{ $head->input_by_name }}, NOMOR LEGAL : {{ $head->legal_number_mapping }}',
            //     exportOptions: {
            //         columns: ':not(:first-child)',
            //     },
            //     filename: function() {
            //         var d = new Date();
            //         var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
            //             .getDate();
            //         var n = d.getHours() + ':' + d.getMinutes() + ':' + d
            //             .getSeconds();
            //         return 'LAPORAN_MAPPING_SHIFT_{{ $holding->holding_name }}_' +
            //             l + ' ' + n;
            //     },
            // }],


            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_karyawan',
                    name: 'nama_karyawan'
                },
                {
                    data: 'hari_masuk',
                    name: 'hari_masuk'
                },
                {
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'pengubahan',
                    name: 'pengubahan'
                },
                {
                    data: 'original',
                    name: 'original'
                },
            ],
            order: [
                [3, 'ASC']
            ]
        });
        $('#table_detail_shift2').hide();
        var table2 = $('#table_detail_shift2').DataTable({
            // pageLength: 25,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('/report/dt_laporan_detail_tukar') }}" + "/" + holding + "/" + id,

            },
            buttons: [{

                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'LAPORAN MAPPING SHIFT',
                messageTop: '{{ $head->input_by_name }}, NOMOR LEGAL : {{ $head->no_form }}',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d
                        .getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d
                        .getSeconds();
                    return 'LAPORAN_MAPPING_SHIFT_{{ $holding->holding_name }}_' +
                        l + ' ' + n;
                },
            }],


            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_karyawan',
                    name: 'nama_karyawan'
                },
                {
                    data: 'hari_masuk',
                    name: 'hari_masuk'
                },
                {
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'nama_shift',
                    name: 'nama_shift'
                },
                {
                    data: 'pengubahan',
                    name: 'pengubahan'
                },
                {
                    data: 'original',
                    name: 'original'
                },

            ],
            order: [
                [3, 'ASC']
            ]
        });
    </script>
@endsection
