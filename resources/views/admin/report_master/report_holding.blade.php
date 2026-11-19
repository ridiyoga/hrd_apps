@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header master-report-toolbar">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA MASTER HOLDING</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-5">


                        <div class="d-flex master-table-toolbar">
                            <table class="table" id="table_holding2" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Holding</th>
                                        <th>Kategori&nbsp;Holding</th>
                                        <th>Singkatan</th>
                                        <th>Kota / Kabupaten</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Website</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_holding" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Holding</th>
                                    <th>Kategori&nbsp;Holding</th>
                                    <th>Singkatan</th>
                                    <th>Logo</th>
                                    <th>Kota / Kabupaten</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Website</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            </tbody>
                        </table>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        let holding = '{{ $holding->holding_code }}';
        var table = $('#table_holding').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('holding-datatable') }}" + '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'holding_name',
                    name: 'holding_name'
                },
                {
                    data: 'holding_category',
                    name: 'holding_category'
                },
                {
                    data: 'holding_name_hint',
                    name: 'holding_name_hint'
                },
                {
                    data: 'holding_image',
                    name: 'holding_image'
                },
                {
                    data: 'kota',
                    name: 'kota'
                },
                {
                    data: 'alamat',
                    name: 'alamat'
                },
                {
                    data: 'telepon',
                    name: 'telepon'
                },

                {
                    data: 'website',
                    name: 'website'
                },
            ]
        });
        $('#table_holding2').hide();
        var table2 = $('#table_holding2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('holding-datatable') }}" + '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'HOLDING',
                messageTop: '',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'HOLDING_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'holding_name',
                    name: 'holding_name'
                },
                {
                    data: 'holding_category',
                    name: 'holding_category'
                },
                {
                    data: 'holding_name_hint',
                    name: 'holding_name_hint'
                },
                {
                    data: 'kota',
                    name: 'kota'
                },
                {
                    data: 'alamat',
                    name: 'alamat'
                },
                {
                    data: 'telepon',
                    name: 'telepon'
                },

                {
                    data: 'website',
                    name: 'website'
                },
            ]
        });
    </script>
@endsection
