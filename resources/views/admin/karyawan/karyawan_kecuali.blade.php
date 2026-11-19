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
                            <h5 class="card-title m-0 me-2">ABSEN PENGECUALIAN</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="d-flex master-table-toolbar">
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_karyawan"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                            <table class="table" id="table_karyawan2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Karyawan</th>
                                        <th>Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                            <table class="table" id="table_karyawan" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Karyawan</th>
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
    <div class="modal fade" id="modal_tambah_karyawan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form method="post" id="add_karyawan_form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Karyawan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_karyawan" id="id_karyawan_add"
                                    placeholder="Pilih Site">
                                    <option disabled value="" selected>-Pilih Karyawan-</option>
                                    @foreach ($karyawan as $kt)
                                        <option value="{{ $kt->id }}">{{ $kt->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                    <button type="button" id="btn_save_karyawan" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- modal edit -->
    <div class="modal fade" id="modal_edit_karyawan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form class="modal-content" id="edit_karyawan_form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Edit Karyawan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="hidden" name="id" id="id_karyawan_record_update" value="">
                                <select class="form-control" name="id_karyawan" id="id_karyawan_update">
                                    <option value="">-Pilih Karyawan-</option>
                                    @foreach ($karyawan as $kt)
                                        <option value="{{ $kt->id }}">{{ $kt->name }}</option>
                                    @endforeach
                                </select>
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
                    <button type="button" id="btn_update_karyawan" class="btn btn-primary">Save</button>
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
        var table = $('#table_karyawan').DataTable({
            pageLength: 50,
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_kecuali-datatable') }}",
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
                    data: 'deskripsi',
                    name: 'deskripsi'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ]
        });

        $('#id_karyawan_add').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Karyawan',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modal_tambah_karyawan')
        });
        $('#id_karyawan_update').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Karyawan',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modal_edit_karyawan')
        });

        $('#table_karyawan2').hide();
        var table2 = $('#table_karyawan2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('karyawan_kecuali-datatable') }}",
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'karyawan_kantor',
                messageTop: 'karyawan UMUM',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'karyawan_kantor_' + l + ' ' + n;
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
        $(document).on("click", "#btn_edit_karyawan", function() {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('/karyawan_kecuali/edit') }}" + '/' + id + '/' + holding,
                type: 'GET',
                success: function(response) {
                    $('#id_karyawan_record_update').val(response.data.id);
                    $('#id_karyawan_update').val(response.data.id_karyawan).trigger('change');
                    $('#deskripsi_update').val(response.data.deskripsi);
                    $('#modal_edit_karyawan').modal('show');
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ?
                        xhr.responseJSON.message : 'Data karyawan gagal dimuat.';
                    Swal.fire({
                        title: 'Gagal',
                        text: message,
                        icon: 'error'
                    });
                }
            });
        });
        $('#btn_update_karyawan').on('click', function(e) {
            $('#modal_edit_karyawan').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan_update').val());
            formData.append('id', $('#id_karyawan_record_update').val());
            formData.append('deskripsi', $('#deskripsi_update').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan_kecuali/update/') }}" + '/' + holding,
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
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Data karyawan gagal diperbarui.',
                        icon: 'error'
                    });
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
                        $('#edit_karyawan_form')[0].reset();
                        $('#id_karyawan_update').val(null).trigger('change');
                        $('#table_karyawan').DataTable().ajax.reload();
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
        $('#btn_save_karyawan').on('click', function(e) {
            $('#modal_tambah_karyawan').modal('hide');
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan_add').val());
            formData.append('deskripsi', $('#deskripsi_add').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan_kecuali/insert/') }}" + '/' + holding,
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
                        $('#add_karyawan_form')[0].reset();
                        $('#id_karyawan_add').val(null).trigger('change');
                        $('#table_karyawan').DataTable().ajax.reload();
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
                        url: "{{ url('/karyawan_kecuali/delete/') }}" + '/' + id + '/' +
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
                                $('#table_karyawan').DataTable().ajax.reload();
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Karyawan Masih Terhubung Karyawan',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_karyawan').DataTable().ajax.reload();
                            } else if (data.status == 0) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message ||
                                        'Data karyawan gagal dihapus.',
                                    icon: 'error',
                                    timer: 1500
                                })
                                $('#table_karyawan').DataTable().ajax.reload();
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
