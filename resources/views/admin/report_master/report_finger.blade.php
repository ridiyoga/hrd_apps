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
                            <h5 class="card-title m-0 me-2">DATA MASTER FINGER</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-5">
                        <div class="d-flex master-table-toolbar">
                            <table class="table" id="table_finger2" style="width: 100%; font-size: small;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Finger</th>
                                        <th>IP&nbsp;Mesin</th>
                                        <th>Port&nbsp;Mesin</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_finger" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Finger</th>
                                    <th>IP&nbsp;Mesin</th>
                                    <th>Port&nbsp;Mesin</th>
                                    <th>Status</th>
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
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        let holding = window.location.pathname.split("/").pop();
        var table = $('#table_finger').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/finger-datatable') }}@else {{ url('finger-datatable') }}@endif" +
                    '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'Name',
                    name: 'Name'
                },
                {
                    data: 'Ip',
                    name: 'Ip'
                },
                {
                    data: 'Port',
                    name: 'Port'
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ]
        });
        $('#table_finger2').hide();
        var table2 = $('#table_finger2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/finger-datatable') }}@else {{ url('finger-datatable') }}@endif" +
                    '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'DAFTAR MESIN FINGER',
                messageTop: 'SELURUH HOLDING',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'MESIN_FINGER_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'Name',
                    name: 'Name'
                },
                {
                    data: 'Ip',
                    name: 'Ip'
                },
                {
                    data: 'Port',
                    name: 'Port'
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ]
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
