d
@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
@endsection
@section('isi')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css"> --}}
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header master-report-toolbar">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA MASTER DIVISI</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">

                        <hr class="my-3">


                        <!-- modal lihat divisi -->
                        <div class="modal fade" id="modal_lihat_bagian" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class=" modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle"> Daftar Bagian</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-lg-12">
                                            <table class="table" id="table_lihat_bagian"
                                                style="width: 100%; font-size: smaller;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Nama&nbsp;Bagian</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                </tbody>
                                            </table>
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
                        <div class="d-flex master-table-toolbar">
                            <table class="table" id="table_divisi2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Departemen</th>
                                        <th>Nama Divisi</th>
                                        <th>Jumlah&nbsp;Bagian</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_divisi" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Departemen</th>
                                    <th>Nama Divisi</th>
                                    <th>Jumlah&nbsp;Bagian</th>
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

        $('#table_divisi2').hide();
        var table2 = $('#table_divisi2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('divisi-datatable') }}" + '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'DIVISI',
                messageTop: '{{ $holding->holding_name }}',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'SELURUH_DIVISI_{{ $holding->holding_name }}_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_departemen',
                    name: 'nama_departemen'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'jumlah_bagian_excel',
                    name: 'jumlah_bagian_excel'
                },
            ],
            order: [
                [1, 'asc']
            ]
        });
        var table = $('#table_divisi').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('divisi-datatable') }}" + '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_departemen',
                    name: 'nama_departemen'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'jumlah_bagian',
                    name: 'jumlah_bagian'
                },
            ],
            order: [
                [1, 'asc']
            ]
        });
        $(document).on('click', '#btn_lihat_bagian', function() {

            let id = $(this).data('id');
            let holding = $(this).data("holding");
            // console.log(url);
            var table1 = $('#table_lihat_bagian').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('divisi/bagian-datatable/') }}" + '/' + id + '/' + holding,
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_bagian',
                        name: 'nama_bagian'
                    },
                ],
                order: [
                    [1, 'asc'],
                ],
            });
            $('#modal_lihat_bagian').modal('show');
            $('#modal_lihat_bagian').on('hidden.bs.modal', function(e) {
                table1.destroy();
                $('#table_divisi').DataTable().ajax.reload();
            })
        });
    </script>
@endsection
