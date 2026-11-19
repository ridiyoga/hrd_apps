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
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA MASTER DIVISI</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- <button type="button" class="btn btn-sm btn-success waves-effect waves-light"
                            data-bs-toggle="modal" data-bs-target="#modal_import_divisi"><i
                                class="menu-icon tf-icons mdi mdi-file-excel"></i>Import</button> --}}
                        <div class="modal fade" id="modal_tambah_divisi" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form id="divisi_add_form" class="modal-content" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Tambah Divisi</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select class="form-control" id="nama_departemen_add"
                                                        name="nama_departemen" autofocus>
                                                        <option value=""> Pilih Departemen</option>
                                                        @foreach ($data_departemen as $data)
                                                            <option value="{{ $data->id }}">{{ $data->nama_departemen }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="nama_departemen">Nama Departemen</label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control" id="nama_divisi_add"
                                                        name="nama_divisi" autofocus>
                                                    <label for="nama_divisi" class="float-left">Nama Divisi</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" id="btn_save_divisi" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modal_import_divisi" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form method="post" action="{{ url('/divisi/ImportDivisi/' . $holding->holding_code) }}"
                                    class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Import Divisi</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mt-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="file" id="file_excel" name="file_excel"
                                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                                        class="form-control" placeholder="Masukkan File" />
                                                    <label for="file_excel">File Excel</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- modal edit -->
                        <div class="modal fade" id="modal_edit_divisi" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <form class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Edit Divisi</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col mb-2">

                                            </div>
                                        </div>


                                        <div class="row g-2">
                                            <div class="col mb-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select class="form-control" id="nama_departemen_update"
                                                        name="nama_departemen" autofocus
                                                        value="{{ old('nama_departemen_update') }}">
                                                        <option value=""> Pilih Departemen</option>
                                                        @foreach ($data_departemen as $data)
                                                            <option value="{{ $data->id }}">
                                                                {{ $data->nama_departemen }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="nama_departemen_update">Nama Departemen</label>
                                                </div>
                                                <br>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="hidden" name="id_divisi" id="id_divisi_update"
                                                        value="">
                                                    <input type="text" class="form-control" id="nama_divisi_update"
                                                        name="nama_divisi" autofocus>
                                                    <label for="nama_divisi_update" class="float-left">Nama Divisi</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="button" id="btn_update_divisi"
                                            class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

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
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_divisi"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
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
                title: 'SELURUH DIVISI',
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
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [1, 'asc']
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
        $(document).on("click", "#btn_edit_divisi", function() {
            let id = $(this).data('id');
            let dept = $(this).data("dept");
            let divisi = $(this).data("divisi");
            let holding = $(this).data("holding");
            // console.log(dept);
            $('#nama_departemen_update option').filter(function() {
                // console.log($(this).val().trim());
                return $(this).val().trim() == dept
            }).prop('selected', true)
            $('#id_divisi_update').val(id);
            $('#nama_divisi_update').val(divisi);
            $('#modal_edit_divisi').modal('show');

        });
        $('#btn_update_divisi').on('click', function(e) {
            $('#modal_edit_divisi').modal('hide');
            e.preventDefault();
            var formData = new FormData();
            // console.log(formData);
            // console.log($('#nama_divisi_update').val());
            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_divisi', $('#id_divisi_update').val());
            formData.append('nama_departemen', $('#nama_departemen_update').val());
            formData.append('nama_divisi', $('#nama_divisi_update').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/divisi/update/') }}" + '/' + holding,
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
                        $('#table_divisi').DataTable().ajax.reload();
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
        $('#btn_save_divisi').on('click', function(e) {
            $('#modal_tambah_divisi').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_departemen', $('#nama_departemen_add').val());
            formData.append('nama_divisi', $('#nama_divisi_add').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/divisi/insert/') }}" + '/' + holding,
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
                        $('#divisi_add_form')[0].reset();
                        $('#table_divisi').DataTable().ajax.reload();
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
        $(document).on('click', '#btn_delete_divisi', function() {
            var id = $(this).data('id');
            let holding = $(this).data("holding");
            var menu_id = $('#asoy').val();
            // console.log(id);
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
                        url: "{{ url('/divisi/delete/') }}" + '/' + id + '/' + holding + '/' +
                            menu_id,
                        type: "GET",
                        error: function() {
                            alert('Something is wrong');
                        },
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data anda berhasil di hapus.',
                                    icon: 'success',
                                    timer: 1500
                                })
                                $('#table_divisi').DataTable().ajax.reload();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Departemen Masih Terhubung Karyawan',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_divisi').DataTable().ajax.reload();
                            } else if (data.status == 0) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Departemen Masih Terhubung Bagian',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_divisi').DataTable().ajax.reload();
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
