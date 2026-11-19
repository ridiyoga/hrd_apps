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
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">PERJALANAN INVENTARIS</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="d-flex master-table-toolbar">
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_perjalanan"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                            <table class="table" id="table_perjalanan2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Perjalanan</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                            <table class="table" id="table_perjalanan" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Perjalanan</th>
                                        <th>Deskripsi</th>
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
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
    <div class="modal fade" id="modal_tambah_perjalanan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form method="post" id="add_perjalanan_form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Perjalanan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="nama_perjalanan_add" name="nama_perjalanan"
                                    placeholder="Nama Perjalanan" />
                                <label for="nama_perjalanan_add" class="float-left">Nama Perjalanan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea id="deskripsi_add" name="deskripsi" class="form-control" placeholder="Deskripsi"></textarea>
                                <label for="deskripsi_add" class="float-left">Deskripsi</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_save_perjalanan" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- modal edit -->
    <div class="modal fade" id="modal_edit_perjalanan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Edit Perjalanan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="hidden" name="id_perjalanan" id="id_perjalanan_update" value="">
                                <input type="text" class="form-control" id="nama_perjalanan_update"
                                    name="nama_perjalanan" autofocus>
                                <label for="nama_perjalanan_update" class="float-left">Nama
                                    Perjalanan</label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <div class="form-floating form-floating-outline">
                                    <textarea id="deskripsi_update" name="deskripsi" class="form-control" placeholder="Deskripsi"></textarea>
                                    <label for="deskripsi_update" class="float-left">Deskripsi</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_update_perjalanan" class="btn btn-primary">Save</button>
                </div>
            </form>
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
        var table = $('#table_perjalanan').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('perjalanan_inventaris-datatable') }}",
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_perjalanan',
                    name: 'nama_perjalanan'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ]
        });

        $(document).ready(function() {
            $('#nama_perjalanan_add').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
        });
        $(document).ready(function() {
            $('#nama_perjalanan_update').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
        });

        $('#table_perjalanan2').hide();
        var table2 = $('#table_perjalanan2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('perjalanan_inventaris-datatable') }}",
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'PERJALANAN_INVENTARIS',
                messageTop: 'PERJALANAN INVENTARIS',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'PERJALANAN_INVENTARIS_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_perjalanan',
                    name: 'nama_perjalanan'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
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
        $(document).on("click", "#btn_edit_perjalanan", function() {
            let id = $(this).data('id');
            let perjalanan = $(this).data("perjalanan");
            let deskripsi = $(this).data("deskripsi");
            let holding = $(this).data("holding");
            // console.log(jamkeluar);
            $('#id_perjalanan_update').val(id);
            $('#nama_perjalanan_update').val(perjalanan);
            $('#deskripsi_update').val(deskripsi);
            $('#modal_edit_perjalanan').modal('show');

        });
        $('#btn_update_perjalanan').on('click', function(e) {
            $('#modal_edit_perjalanan').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_perjalanan', $('#id_perjalanan_update').val());
            formData.append('nama_perjalanan', $('#nama_perjalanan_update').val());
            formData.append('deskripsi', $('#deskripsi_update').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/perjalanan_inventaris/update/') }}" + '/' + holding,
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
                        $('#table_perjalanan').DataTable().ajax.reload();
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
        $('#btn_save_perjalanan').on('click', function(e) {
            $('#modal_tambah_perjalanan').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_perjalanan', $('#nama_perjalanan_add').val());
            formData.append('deskripsi', $('#deskripsi_add').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/perjalanan_inventaris/insert/') }}" + '/' + holding,
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
                        $('#add_perjalanan_form')[0].reset();
                        $('#table_perjalanan').DataTable().ajax.reload();
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
        $(document).on('click', '#btn_delete_perjalanan', function() {
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
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/perjalanan_inventaris/delete/') }}" + '/' + id + '/' +
                            holding + '/' +
                            menu_id,
                        type: "GET",
                        error: function() {
                            alert('Something is wrong');
                        },
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data anda berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1500
                                })
                                $('#table_perjalanan').DataTable().ajax.reload();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Perjalanan Masih Terhubung Karyawan',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_perjalanan').DataTable().ajax.reload();
                            } else if (data.status == 0) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Perjalanan Masih Terhubung Divisi',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_perjalanan').DataTable().ajax.reload();
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
