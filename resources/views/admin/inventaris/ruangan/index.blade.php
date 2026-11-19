@extends('admin.layouts.dashboard')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    @include('css_baru')
    <style>
        /* ukuran teks di area pilihan (input select2) */
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem !important;
            /* Bootstrap small (14px) */
            min-height: calc(1.5em + 0.75rem + 2px);
            /* biar tinggi konsisten */
        }

        /* ukuran teks di dropdown list */
        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 0.875rem !important;
        }

        /* Fokus warna primary */
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25) !important;
        }

        /* Background dan teks saat option terpilih */
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        /* Hover option */
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
            color: var(--bs-primary) !important;
        }

        /* ukuran huruf untuk pilihan yang sudah dipilih (tag dalam box) */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            font-size: 0.75rem;
            /* kecilin text */
            padding: 2px 6px;
            /* biar nggak terlalu tinggi */
            line-height: 1.2;
        }

        /* icon "x" di tag */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            font-size: 0.7rem;
            margin-right: 2px;
        }

        /* tulisan placeholder / hasil render */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            font-size: 0.8rem;
        }
    </style>
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
                            <h5 class="card-title m-0 me-2">MASTER RUANGAN</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="d-flex master-table-toolbar">
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_ruangan"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                            <table class="table" id="table_ruangan2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Ruangan</th>
                                        <th>Nama Gedung</th>
                                        <th>Deskripsi</th>
                                        <th>Site</th>
                                        <th>Holding</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                            <table class="table" id="table_ruangan" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Ruangan</th>
                                        <th>Nama Gedung</th>
                                        <th>Deskripsi</th>
                                        <th>Site</th>
                                        <th>Holding</th>
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
    <div class="modal fade" id="modal_tambah_ruangan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form method="post" id="add_ruangan_form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Kategori</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="nama_ruangan_add" name="nama_ruangan"
                                    placeholder="Nama Ruangan" />
                                <label for="nama_ruangan_add" class="float-left">Nama Ruangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_site" id="id_site_add"
                                    placeholder="Pilih Site">
                                    <option disabled value="" selected>Pilih Site</option>
                                    @foreach ($site as $st)
                                        <option value="{{ $st->id }}">{{ $st->site_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_gedung" id="id_gedung_add"
                                    placeholder="Pilih Site">
                                    <option disabled value="">Pilih Ruangan</option>
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
                    <button type="button" id="btn_save_ruangan" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- modal edit -->
    <div class="modal fade" id="modal_edit_ruangan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form class="modal-content" id="edit_ruangan_form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Edit Ruangan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="hidden" name="id_ruangan" id="id_ruangan_update" value="">
                                <input type="text" class="form-control" id="nama_ruangan_update" name="nama_ruangan"
                                    autofocus>
                                <label for="nama_ruangan_update" class="float-left">Nama
                                    Kategori</label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-control" name="id_site" id="id_site_update">
                                        <option disabled value="">Pilih Site</option>
                                        @foreach ($site as $st)
                                            <option value="{{ $st->id }}">{{ $st->site_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-control" name="id_gedung" id="id_gedung_update">
                                        <option disabled value="">-Pilih Gedung-</option>
                                        @foreach ($gedung as $gd)
                                            <option value="{{ $gd->id }}">
                                                {{ $gd->nama_gedung }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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
                    <button type="button" id="btn_update_ruangan" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            let holding = '{{ $holding->holding_code }}';
            var table = $('#table_ruangan').DataTable({
                pageLength: 50,
                scrollY: '200px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('ruangan-datatable') }}",
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_ruangan',
                        name: 'nama_ruangan'
                    },
                    {
                        data: 'nama_gedung',
                        name: 'nama_gedung'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'nama_site',
                        name: 'nama_site'
                    },
                    {
                        data: 'nama_holding',
                        name: 'nama_holding'
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                ]
            });

            $(document).ready(function() {
                $('#nama_ruangan_add').on('keyup', function() {
                    let val = $(this).val().toUpperCase();
                    $(this).val(val);
                });
                $('#id_site_add').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Site',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal_tambah_ruangan')
                });
                $('#id_site_update').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Site',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal_edit_ruangan')
                });
                $('#id_gedung_add').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Site',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal_tambah_ruangan')
                });
                $('#id_gedung_update').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Site',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal_edit_ruangan')
                });
            });
            // Mencari Ruangan
            function showSelectLoading(selectId) {
                $(selectId).prop('disabled', true)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .removeClass('d-none');
            }

            function showTableLoading() {
                $('#tableLoading').removeClass('d-none');
            }

            function hideTableLoading() {
                $('#tableLoading').addClass('d-none');
            }

            function hideSelectLoading(selectId) {
                $(selectId).prop('disabled', false)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .addClass('d-none');
            }
            $('#id_site_add').on('change', function() {
                showSelectLoading('#id_gedung_add');
                let id_site = $(this).val();
                let url = "{{ url('/ruangan/get_gedung') }}" + "/" + id_site;

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
                        hideSelectLoading('#id_gedung_add');
                        $('#id_gedung_add').html(response);

                        // Tutup SweetAlert setelah data berhasil dimuat
                        Swal.close();
                    },
                    error: function(data) {
                        hideSelectLoading(
                            '#id_gedung_add'
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

            $('#table_ruangan2').hide();
            var table2 = $('#table_ruangan2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('ruangan-datatable') }}",
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'ruangan',
                    messageTop: 'KATEGORI INVENTARIS',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'ruangan_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'nama_ruangan',
                        name: 'nama_ruangan'
                    },
                    {
                        data: 'nama_gedung',
                        name: 'nama_gedung'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'nama_site',
                        name: 'nama_site'
                    },
                    {
                        data: 'nama_holding',
                        name: 'nama_holding'
                    },
                ]
            });
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
            $(document).on("click", "#btn_edit_kategori", function() {

                let id = $(this).data('id');
                let kategori = $(this).data("kategori");
                let deskripsi = $(this).data("deskripsi");
                let id_site = $(this).data("id_site");
                let id_gedung = $(this).data("id_gedung");

                $('#id_ruangan_update').val(id);
                $('#nama_ruangan_update').val(kategori);
                $('#deskripsi_update').val(deskripsi);

                // Simpan gedung yang harus dipilih
                selectedGedung = id_gedung;

                // Pilih site
                // Ini akan menjalankan AJAX get_gedung
                $('#id_site_update')
                    .val(String(id_site))
                    .trigger('change');

                $('#modal_edit_ruangan').modal('show');
            });
            let selectedGedung = null;

            $('#id_site_update').on('change', function() {

                let id_site = $(this).val();
                let url = "{{ url('/ruangan/get_gedung') }}" + "/" + id_site;

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
                        Swal.close();

                        $('#id_gedung_update').html(response);

                        // Setelah option gedung selesai dibuat,
                        // baru pilih gedung yang sesuai
                        if (selectedGedung !== null) {

                            $('#id_gedung_update')
                                .val(String(selectedGedung))
                                .trigger('change');

                            // console.log(
                            //     'Gedung terpilih:',
                            //     $('#id_gedung_update option:selected').text()
                            // );

                            selectedGedung = null;
                        }

                        Swal.close();
                    },

                    error: function(data) {

                        let errorMessage = data.responseJSON &&
                            data.responseJSON.message ?
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
            $('#btn_update_ruangan').on('click', function(e) {
                $('#modal_edit_ruangan').modal('hide');
                e.preventDefault();
                let btn = $(this);
                let originalText = btn.html();

                //ambil data dari form
                let formElement = $('#edit_ruangan_form')[0]; // Ambil elemen DOM form
                let formData = new FormData(formElement);
                formData.append('menu_id', $('#menu_id_add').val());
                // post
                $.ajax({
                    type: "POST",

                    url: "{{ url('/ruangan/update/') }}" + '/' + holding,
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
                            $('#table_ruangan').DataTable().ajax.reload();
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
            $(document).on('click', '#btn_save_ruangan', function(e) {
                // console.log('asooy')
                $('#modal_tambah_ruangan').modal('hide');
                e.preventDefault();
                let btn = $(this);
                let originalText = btn.html();

                //ambil data dari form
                let formElement = $('#add_ruangan_form')[0]; // Ambil elemen DOM form
                let formData = new FormData(formElement);
                formData.append('menu_id', $('#menu_id_add').val());


                // post
                $.ajax({
                    type: "POST",

                    url: "{{ url('/ruangan/insert/') }}" + '/' + holding,
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
                            $('#add_ruangan_form')[0].reset();

                            $('#id_site_add').val(null).trigger('change');
                            $('#id_gedung_add').val(null).trigger('change');
                            $('#table_ruangan').DataTable().ajax.reload();
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
            $(document).on('click', '#btn_delete_ruangan', function() {
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
                            url: "{{ url('/ruangan/delete/') }}" + '/' + id +
                                '/' +
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
                                    $('#table_ruangan').DataTable().ajax.reload();
                                } else if (data.status == 2) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gedung Masih Terhubung Karyawan',
                                        icon: 'error',
                                        timer: 1500
                                    })
                                    $('#table_ruangan').DataTable().ajax.reload();
                                } else if (data.status == 0) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gedung Masih Terhubung Ruangan',
                                        icon: 'error',
                                        timer: 1500
                                    })
                                    $('#table_ruangan').DataTable().ajax.reload();
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

            })
        });
    </script>
@endsection
