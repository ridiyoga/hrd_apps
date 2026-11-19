@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page" style="font-size: small;">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA KARYAWAN NONAKTIF</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item active" role="presentation">
                                <button class="nav-link active" id="sudah-tab" data-bs-toggle="tab"
                                    data-bs-target="#sudah-tab-pane" type="button" role="tab"
                                    aria-controls="sudah-tab-pane" aria-selected="true">SUDAH NONAKTIF</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="akan-tab" data-bs-toggle="tab" data-bs-target="#akan-tab-pane"
                                    type="button" role="tab" aria-controls="akan-tab-pane" aria-selected="false">AKAN
                                    NONAKTIF</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane active fade show" id="sudah-tab-pane" role="tabpanel"
                                aria-labelledby="sudah-tab" tabindex="0">
                                <table class="table" id="table_karyawan_bulanan_nonaktif"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nomor&nbsp;ID</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Telepon</th>
                                            <th>Email</th>
                                            <th>Tanggal Keluar</th>
                                            <th>Alasan</th>
                                            <th>Kontrak Kerja</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade" id="akan-tab-pane" role="tabpanel" aria-labelledby="akan-tab"
                                tabindex="0">
                                <table class="table" id="table_karyawan_akan_nonaktif"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Tanggal Keluar</th>
                                            <th>Alasan</th>
                                            <th>Hapus</th>
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
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
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
        });
        var holding = '{{ $holding->holding_code }}';
        var url_nonaktif =
            "{{ url('karyawan_bulanan_nonaktif-datatable') }}" + '/' + holding;
        var table2 = $('#table_karyawan_bulanan_nonaktif').DataTable({
            pageLength: 10,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url_nonaktif,
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
                    data: 'tanggal_nonactive',
                    name: 'tanggal_nonactive'
                },
                {
                    data: 'alasan_nonactive',
                    name: 'alasan_nonactive'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
        $('#sudah-tab').on('shown.bs.tab', function() {
            table2.columns.adjust();
            table2.ajax.reload();
        });
        var url_akan =
            "{{ url('karyawan_akan_nonaktif-datatable') }}" + '/' + holding;
        var table3 = $('#table_karyawan_akan_nonaktif').DataTable({
            pageLength: 10,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url_akan,
            },
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
                    data: 'tanggal_nonaktif',
                    name: 'tanggal_nonaktif'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },

                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
        $('#akan-tab').on('shown.bs.tab', function() {
            table3.columns.adjust();
            table3.ajax.reload();
        });
        $(document).on('click', '#btn_delete_karyawan', function() {
            var id = $(this).data('id');
            let holding = "{{ $holding->holding_code }}";
            var menu_id = $('#asoy').val();

            // console.log(holding);
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Kamu tidak dapat mengembalikan data ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/karyawan_akan_nonaktif-delete') }}" + '/' + id +
                            '/' +
                            holding + '/' +
                            menu_id,
                        type: "GET",
                        error: function(xhr) {
                            var message = xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message : 'Data karyawan gagal dihapus.';
                            Swal.fire({
                                title: 'Gagal',
                                text: message,
                                icon: 'error'
                            });
                        },
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data anda berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1500
                                })
                                $('#table_karyawan_akan_nonaktif').DataTable().ajax.reload();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Karyawan Masih Terhubung Karyawan',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_karyawan_akan_nonaktif').DataTable().ajax.reload();
                            } else if (data.status == 0) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message ||
                                        'Data karyawan gagal dihapus.',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_karyawan_akan_nonaktif').DataTable().ajax.reload();
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Cancelled!',
                        text: 'Your data is safe :',
                        icon: 'error',
                        timer: 1500
                    })
                }
            });

        });
    </script>
@endsection
