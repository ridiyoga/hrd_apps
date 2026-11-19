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
                            <h5 class="card-title m-0 me-2">ABSENSI KARYAWAN BARU / KELUAR (R)</h5>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="d-flex master-table-toolbar">
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_karyawan"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Karyawan Baru</button>
                            <button type="button" class="btn btn-sm btn-danger waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_keluar"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Karyawan Keluar</button>
                            <table class="table" id="table_karyawan2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Karyawan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Dibuat Oleh</th>
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
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Deskripsi</th>
                                        <th>Dibuat Oleh</th>
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
                <input type="hidden" id="menu_id_add" name="menu_id">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Karyawan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="tanggal_mulai_add" name="tanggal_mulai" class="form-control"
                                    placeholder="Tanggal Mulai Cut-off">
                                <label for="tanggal_mulai_add" class="float-left">Tanggal Mulai</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_karyawan" id="id_karyawan_add"
                                    placeholder="Pilih Site">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="tanggal_selesai_add" name="tanggal_selesai" class="form-control"
                                    placeholder="Tanggal Selesai Cut-off" readonly>
                                <label for="tanggal_selesai_add" class="float-left">Tanggal Join</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="deskripsi_add" name="deskripsi" value="KARYAWAN BARU">
                    <input type="hidden" id="baru_add" name="baru" value="1">
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
    <div class="modal fade" id="modal_tambah_keluar" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form method="post" id="add_keluar_form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="menu_id_keluar_add" name="menu_id">
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Karyawan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="tanggal_cut_add" name="tanggal_selesai" class="form-control"
                                    placeholder="Tanggal Mulai Cut-off">
                                <label for="tanggal_cut_add" class="float-left">Tanggal Cut Off Akhir</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_karyawan"
                                    id="id_karyawan_keluar_add"a placeholder="Pilih Site">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="tanggal_nonaktif_add" name="tanggal_mulai"
                                    class="form-control" placeholder="Tanggal Nonaktif" readonly>
                                <label for="tanggal_nonaktif_add" class="float-left">Tanggal Keluar </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="deskripsi_add" name="deskripsi" value="KARYAWAN KELUAR">
                    <input type="hidden" id="baru_add" name="baru" value="2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_save_keluar" class="btn btn-primary">Save</button>
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
                url: "{{ url('karyawan_r-datatable') }}",
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
                    data: 'tanggal_mulai',
                    name: 'tanggal_mulai'
                },
                {
                    data: 'tanggal_selesai',
                    name: 'tanggal_selesai'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },

                {
                    data: 'created_by_name',
                    name: 'created_by_name'
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
        $('#id_karyawan_keluar_add').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Karyawan',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modal_tambah_keluar')
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
                url: "{{ url('karyawan_r-datatable') }}",
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
                    data: 'tanggal_mulai',
                    name: 'tanggal_mulai'
                },
                {
                    data: 'tanggal_selesai',
                    name: 'tanggal_selesai'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },
                {
                    data: 'created_by_name',
                    name: 'created_by_name'
                },
            ]
        });
        $(document).ready(function() {
            var menu_id = $('#asoy').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);
            $('#menu_id_keluar_add').val(menu_id);
            $('#menu_id_update').val(menu_id);

            $.ajax({
                type: "POST",

                url: "{{ url('/recruitment/update_menu') }}",
                data: $('#add_karyawan_form').serialize(),
                error: function() {
                    alert('Something is wrong!');
                },
            });
        })

        $('#btn_save_karyawan').on('click', function(e) {
            $('#modal_tambah_karyawan').modal('hide');
            e.preventDefault();
            var formData = new FormData(document.getElementById('add_karyawan_form'));

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan_r/insert/') }}" + '/' + holding,
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
        $('#btn_save_keluar').on('click', function(e) {
            $('#modal_tambah_keluar').modal('hide');
            e.preventDefault();
            var formData = new FormData(document.getElementById('add_keluar_form'));

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan_r/insert/') }}" + '/' + holding,
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
                        $('#add_keluar_form')[0].reset();
                        $('#id_karyawan_keluar_add').val(null).trigger('change');
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
                        url: "{{ url('/karyawan_r/delete/') }}" + '/' + id + '/' +
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

        $('#tanggal_mulai_add').on('change', function() {
            let tanggal_mulai = $(this).val();
            if (!tanggal_mulai) {
                $('#id_karyawan_add').html('<option></option>').val(null).trigger('change.select2');
                return; // Hentikan eksekusi AJAX
            }
            showSelectLoading('#id_karyawan_add');
            let url = "{{ url('/karyawan_r/get_tanggal') }}" + "/" + tanggal_mulai;

            // Tampilkan SweetAlert Loading Spinner sebelum request AJAX dikirim
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    hideSelectLoading('#id_karyawan_add');
                    $('#id_karyawan_add').html(response);

                    // Tutup SweetAlert setelah data berhasil dimuat
                    Swal.close();
                },
                error: function(data) {
                    hideSelectLoading(
                        '#id_karyawan_add'
                    ); // Memastikan indikator loading tambahan juga tertutup

                    // Ambil pesan error (dengan fallback jika responseJSON/message kosong)
                    let errorMessage = data.responseJSON && data.responseJSON.message ?
                        data.responseJSON.message :
                        'Terjadi kesalahan saat mengambil data.';

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                        timer: 4200,
                        showConfirmButton: false
                    });
                }
            });
        });
        $('#id_karyawan_add').on('change', function() {
            let tgl_join = $(this).find(':selected').data('tgl_join');
            console.log(tgl_join);


            $('#tanggal_selesai_add').val(tgl_join || '');
        });
        $('#tanggal_cut_add').on('change', function() {
            let tanggal_cut = $(this).val();
            if (!tanggal_cut) {
                $('#id_karyawan_keluar_add').html('<option></option>').val(null).trigger('change.select2');
                return; // Hentikan eksekusi AJAX
            }
            showSelectLoading('#id_karyawan_keluar_add');
            let url = "{{ url('/karyawan_r/get_cut') }}" + "/" + tanggal_cut;

            // Tampilkan SweetAlert Loading Spinner sebelum request AJAX dikirim
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    hideSelectLoading('#id_karyawan_keluar_add');
                    $('#id_karyawan_keluar_add').html(response);

                    // Tutup SweetAlert setelah data berhasil dimuat
                    Swal.close();
                },
                error: function(data) {
                    hideSelectLoading(
                        '#id_karyawan_keluar_add'
                    ); // Memastikan indikator loading tambahan juga tertutup

                    // Ambil pesan error (dengan fallback jika responseJSON/message kosong)
                    let errorMessage = data.responseJSON && data.responseJSON.message ?
                        data.responseJSON.message :
                        'Terjadi kesalahan saat mengambil data.';

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                        timer: 4200,
                        showConfirmButton: false
                    });
                }
            });
        });
        $('#id_karyawan_keluar_add').on('change', function() {
            let tanggal_nonaktif = $(this).find(':selected').data('tanggal_nonaktif');
            console.log(tanggal_nonaktif);


            $('#tanggal_nonaktif_add').val(tanggal_nonaktif || '');
        });

        function showSelectLoading(selectId) {
            $(selectId).prop('disabled', true)
                .closest('.form-floating')
                .find('.select-loader')
                .removeClass('d-none');
        }

        function hideSelectLoading(selectId) {
            $(selectId).prop('disabled', false)
                .closest('.form-floating')
                .find('.select-loader')
                .addClass('d-none');
        }
    </script>
@endsection
