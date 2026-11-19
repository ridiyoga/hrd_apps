@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style type="text/css">
        .my-swal {
            z-index: X;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card.shadow-sm:hover {
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
        }

        .card-header {
            font-weight: 600;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            padding: 1rem 1.25rem;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f2f4f7;
            color: #566a7f;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            font-weight: 700;
            border-top: none;
            padding: 10px 10px;
            border-top: none !important;
        }

        .table tbody td {
            font-size: 0.875rem;
            color: #697a8d;
            vertical-align: middle;
            padding: 12px 15px;
            font-weight: 400;
        }

        .swal2-container {
            z-index: 9999 !important;
        }

        /* Hover option */
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

        .form-floating-outline {
            position: relative;
        }

        .form-select.border-bottom:focus {
            border-bottom: 2px solid #6f24b6 !important;
            /* Warna biru primary */
            box-shadow: none;
        }

        .table .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
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
                    <div class="card-header master-table-card-content">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark"><i class="mdi mdi-file-document-multiple-outline me-2"></i>KATEGORI
                                CUTI</h6>
                            <input type="hidden" id="menu_id_add" name="menu_id">

                            <button class="btn btn-sm btn-outline-primary" id="btn_tambah_kategori_cuti" type="button">
                                <i class="mdi mdi-plus me-2"></i>
                                Tambah Kategori Cuti</button>
                        </div>
                        <div class="card-body master-table-pane-body">
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-hover mb-0" id="tabel_cuti_kategori2"
                                    style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Opsi</th>
                                            <th class="ps-3">
                                                Jenis&nbsp;Cuti&nbsp;Diluar&nbsp;Tahunan</th>
                                            <th class="text-center">Jatah&nbsp;(Max)</th>
                                            <th>Gender</th>
                                            <th>Status&nbsp;Nikah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="master-table-area mt-2">
                                <table class="table table-sm table-hover mb-0" id="tabel_cuti_kategori" style="width: 100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Opsi</th>
                                            <th class="ps-3">
                                                Jenis&nbsp;Cuti&nbsp;Diluar&nbsp;Tahunan</th>
                                            <th class="text-center">Jatah&nbsp;(Max)</th>
                                            <th>Gender</th>
                                            <th>Status&nbsp;Nikah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
        {{-- tambah kategori cuti --}}
        <div class="modal fade" id="modal_tambah_kategori_cuti" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Kategori Cuti</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="nama_cuti_add" name="nama_cuti">
                                        <label for="recipient-name">Jenis Cuti</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control" id="jumlah_hari_add"
                                                name="jumlah_hari">
                                            <label for="recipient-name">Hari (Maksimal)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" name="target_gender[]" id="target_gender_add"
                                                multiple>
                                                <option disabled value="">Pilih Gender</option>
                                                <option value="1">Laki-Laki</option>
                                                <option value="2">Perempuan</option>
                                            </select>
                                            <label for="target_gender_add">Gender</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" name="target_status_nikah[]"
                                                id="target_status_nikah_add" multiple>
                                                <option disabled value="">Status Nikah</option>
                                                <option value="1">Lajang</option>
                                                <option value="2">Kawin</option>
                                                <option value="3">Cerai Hidup</option>
                                                <option value="4">Cerai Mati</option>
                                            </select>
                                            <label for="target_status_nikah_add">Status nikah</label>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="status" id="status_add">
                                                <option disabled value="">Status</option>
                                                <option value="1">Aktif</option>
                                                <option value="2">Non Aktif</option>
                                            </select>
                                            <label for="status">Status nikah</label>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button> --}}
                        <button class="btn btn-secondary" id="tambah_kategori_cuti" type="button">Tambah
                            Kategori Cuti</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal_edit_kategori_cuti" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Cuti Kategori</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row g-2">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="nama_cuti_update"
                                            name="nama_cuti">
                                        <input type="hidden" class="form-control" id="id_update" name="id">
                                        <label for="recipient-name">Jenis Cuti</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control" id="jumlah_hari_update"
                                                name="jumlah_hari">
                                            <label for="recipient-name">Hari (Maksimal)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" name="target_gender[]" id="target_gender_update"
                                                multiple>
                                                <option disabled value="">Pilih Gender</option>
                                                <option value="1">Laki-Laki</option>
                                                <option value="2">Perempuan</option>
                                            </select>
                                            <label for="target_gender_update">Gender</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" name="target_status_nikah[]"
                                                id="target_status_nikah_update" multiple>
                                                <option disabled value="">Status Nikah</option>
                                                <option value="1">Lajang</option>
                                                <option value="2">Kawin</option>
                                                <option value="3">Cerai Hidup</option>
                                                <option value="4">Cerai Mati</option>
                                            </select>
                                            <label for="target_status_nikah_update">Status Nikah</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col mb-2">
                                        <div class="form-floating form-floating-outline">
                                            <select type="text" class="form-control" name="status"
                                                id="status_update">
                                                <option disabled value="">Status</option>
                                                <option value="1">Aktif</option>
                                                <option value="2">Non Aktif</option>
                                            </select>
                                            <label for="status">Status</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button> --}}
                        <button class="btn btn-secondary" id="edit_kategori_cuti" type="button">Edit
                            Kategori Cuti</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end tambah kategori cuti --}}
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

        var tablecuti = $('#tabel_cuti_kategori').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('dt_cuti_kategori') }}",
                },
                columns: [{
                        data: 'option',
                        name: 'option',
                    },
                    {
                        data: 'nama_cuti',
                        name: 'nama_cuti'
                    },
                    {
                        data: 'jumlah_hari',
                        name: 'jumlah_hari',
                        className: 'text-center'
                    },
                    {
                        data: 'target_gender',
                        name: 'target_gender',
                    },
                    {
                        data: 'target_status_nikah',
                        name: 'target_status_nikah',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
            $('#tabel_cuti_kategori2').hide();
            var tablecuti = $('#tabel_cuti_kategori2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('dt_cuti_kategori') }}",
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'MASTER SHIFT',
                    messageTop: 'SELURUH HOLDING',
                    exportOptions: {
                        columns: ':not(:first-child)',
                    },
                    filename: function() {
                        var d = new Date();
                        var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                        var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                        return 'JABATAN_{{ $holding->holding_name }}_' + l + ' ' + n;
                    },
                }],
                columns: [{
                        data: 'option',
                        name: 'option',
                    },
                    {
                        data: 'nama_cuti',
                        name: 'nama_cuti'
                    },
                    {
                        data: 'jumlah_hari',
                        name: 'jumlah_hari',
                        className: 'text-center'
                    },
                    {
                        data: 'target_gender',
                        name: 'target_gender',
                    },
                    {
                        data: 'target_status_nikah',
                        name: 'target_status_nikah',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                ],
                order: [
                    [0, 'asc']
                ]
            });
            $('#target_gender_add').select2({
                theme: 'bootstrap-5',
                // placeholder: "Pilih Gender",
                allowClear: true,
                dropdownParent: $('#modal_tambah_kategori_cuti')
            });
            $('#target_status_nikah_add').select2({
                theme: 'bootstrap-5',
                // placeholder: "Pilih Gender",
                allowClear: true,
                dropdownParent: $('#modal_tambah_kategori_cuti')
            });
            $('#target_gender_update').select2({
                theme: 'bootstrap-5',
                // placeholder: "Pilih Gender",
                allowClear: true,
                dropdownParent: $('#modal_edit_kategori_cuti')
            });
            $('#target_status_nikah_update').select2({
                theme: 'bootstrap-5',
                // placeholder: "Pilih Gender",
                allowClear: true,
                dropdownParent: $('#modal_edit_kategori_cuti')
            });

            $('#btn_tambah_kategori_cuti').click(function() {
                $('#modal_tambah_kategori_cuti').modal('show');
            });

            $('#tambah_kategori_cuti').on('click', function(e) {
                e.preventDefault();
                var formData = new FormData();
                let gender = $('#target_gender_add').val();
                if (gender) {
                    gender.forEach(function(val) {
                        formData.append('target_gender[]', val);
                    });
                }
                let statusNikah = $('#target_status_nikah_add').val();
                if (statusNikah) {
                    statusNikah.forEach(function(val) {
                        formData.append('target_status_nikah[]', val);
                    });
                }

                //ambil data dari form
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('nama_cuti', $('#nama_cuti_add').val());
                formData.append('jumlah_hari', $('#jumlah_hari_add').val());
                formData.append('menu_id', $('#menu_id_add').val());
                // formData.append('target_gender', $('#target_gender_add').val());
                // formData.append('target_status_nikah', $('#target_status_nikah_add').val());
                // formData.append('status', $('#status_add').val());

                // post
                $.ajax({
                    type: "POST",

                    url: "{{ url('/cuti_kategori_create') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: '', // Kosongkan title agar fokus ke animasi
                            html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            width: '320px',
                            background: '#ffffff',
                            customClass: {
                                popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                            },
                            didOpen: () => {
                                // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                            }
                        });
                    },
                    error: function() {
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
                            $('#modal_tambah_kategori_cuti').modal('hide');
                            $('#nama_cuti_add').val('');
                            $('#jumlah_hari_add').val('');
                            $('#target_gender_add').val([]).trigger('change.select2');
                            $('#target_status_nikah_add').val([]).trigger('change.select2');
                            $('#tabel_cuti_kategori').DataTable().ajax.reload();
                        } else if (data.code == 400) {
                            let errors = data.errors;
                            // console.log(errors);
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            $('#modal_tambah_kategori_cuti').modal('hide');

                            Swal.fire({
                                // title: data.message,
                                text: errorMessages,
                                icon: 'warning',
                                timer: 4500
                            })
                            $('#modal_tambah_kategori_cuti').modal('hide');

                            $('#tabel_cuti_kategori').DataTable().ajax.reload();


                        } else {
                            $('#modal_tambah_kategori_cuti').modal('hide');
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
            $(document).on('click', '#btn_delete_kategori', function() {
                // $('#modal_delete_riwayat').modal('show');
                var id = $(this).data('id');
                var menu_id = $('#menu_id_add').val();
                // console.log(menu_id);

                Swal.fire({
                    title: 'Konfirmasi',
                    icon: 'warning',
                    text: "Apakah benar-benar ingin menghapus data ini?",
                    showCancelButton: true,
                    inputValue: 0,
                    confirmButtonText: 'Yes',
                }).then(function(result) {
                    if (result.value) {
                        // console.log(id_riwayat);
                        $.ajax({
                            data: {
                                "_token": "{{ csrf_token() }}",
                                id: id,
                                menu_id: menu_id,
                            },
                            url: "{{ url('cuti_kategori_delete') }}",
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function() {
                                Swal.fire({
                                    title: '', // Kosongkan title agar fokus ke animasi
                                    html: `<div class="custom-loader-wrapper">
                                    <div class="modern-spinner"></div>
                                    </div>
                                    <div class="mt-3">
                                        <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                                        <p class="pulse-text small">Mohon tunggu sebentar...</p>
                                    </div>`,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    width: '320px',
                                    background: '#ffffff',
                                    customClass: {
                                        popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                    },
                                    didOpen: () => {
                                        // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                    }
                                });
                            },
                            success: function(data) {
                                Swal.close();
                                if (data.status == 1) {
                                    Swal.fire({
                                        title: 'Terhapus!',
                                        text: 'Data anda berhasil dihapus.',
                                        icon: 'success',
                                        timer: 1500
                                    })
                                    $('#tabel_cuti_kategori').DataTable().ajax
                                        .reload();
                                } else if (data.status == 2) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Kategori Cuti sudah digunakan',
                                        icon: 'error',
                                        timer: 1500
                                    })
                                    $('#tabel_cuti_kategori').DataTable().ajax
                                        .reload();
                                }
                            },
                            error: function(data) {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: 'Data Gagal dihapus',
                                    icon: 'error',
                                    timer: 1500
                                })
                            }
                        });

                    } else {
                        Swal.fire({
                            title: 'Gagal !',
                            text: 'Data gagal dihapus',
                            icon: 'warning',
                            timer: 1500
                        })
                    }

                });
            });
            $(document).on('click', '#btn_edit_kategori', function() {
                var id = $(this).data('id');
                var nama_cuti = $(this).data('nama_cuti');
                var jumlah_hari = $(this).data('jumlah_hari');
                var status = $(this).data('status');
                var gender = $(this).data('target_gender');
                var statusNikah = $(this).data('target_status_nikah');

                let genderArray = gender ? gender.replace(/[{}]/g, '').split(',') : [];
                let statusArray = statusNikah ? statusNikah.replace(/[{}]/g, '').split(',') : [];

                $('#id_update').val(id);
                $('#nama_cuti_update').val(nama_cuti);
                $('#jumlah_hari_update').val(jumlah_hari);
                $('#status_update').val(status);

                $('#target_gender_update').val(genderArray).trigger('change');
                $('#target_status_nikah_update').val(statusArray).trigger('change');

                $('#modal_edit_kategori_cuti').modal('show');

            });
            $('#edit_kategori_cuti').on('click', function(e) {
                e.preventDefault();
                var formData = new FormData();
                let gender = $('#target_gender_update').val();
                if (gender) {
                    gender.forEach(function(val) {
                        formData.append('target_gender[]', val);
                    });
                }
                let statusNikah = $('#target_status_nikah_update').val();
                if (statusNikah) {
                    statusNikah.forEach(function(val) {
                        formData.append('target_status_nikah[]', val);
                    });
                }

                //ambil data dari form
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('id', $('#id_update').val());
                formData.append('nama_cuti', $('#nama_cuti_update').val());
                formData.append('jumlah_hari', $('#jumlah_hari_update').val());
                formData.append('menu_id', $('#menu_id_add').val());
                // formData.append('target_gender', $('#target_gender_update').val());
                // formData.append('target_status_nikah', $('#target_status_nikah_update').val());
                formData.append('status', $('#status_update').val());

                // post
                $.ajax({
                    type: "POST",

                    url: "{{ url('/cuti_kategori_update') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: '', // Kosongkan title agar fokus ke animasi
                            html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            width: '320px',
                            background: '#ffffff',
                            customClass: {
                                popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                            },
                            didOpen: () => {
                                // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                            }
                        });
                    },
                    error: function() {
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
                            $('#modal_edit_kategori_cuti').modal('hide');
                            // $('#nama_cuti_add').val('');
                            // $('#jumlah_hari_add').val('');
                            // $('#target_gender_add').val([]).trigger('change.select2');
                            // $('#target_status_nikah_add').val([]).trigger('change.select2');
                            $('#tabel_cuti_kategori').DataTable().ajax.reload();
                        } else if (data.code == 400) {
                            let errors = data.errors;
                            // console.log(errors);
                            let errorMessages = '';

                            Object.keys(errors).forEach(function(key) {
                                errors[key].forEach(function(message) {
                                    errorMessages += `• ${message}\n`;
                                });
                            });
                            $('#modal_edit_kategori_cuti').modal('hide');

                            Swal.fire({
                                // title: data.message,
                                text: errorMessages,
                                icon: 'warning',
                                timer: 4500
                            })
                            $('#modal_edit_kategori_cuti').modal('hide');

                            $('#tabel_cuti_kategori').DataTable().ajax.reload();


                        } else {
                            $('#modal_edit_kategori_cuti').modal('hide');
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
        });
    </script>
@endsection
