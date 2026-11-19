@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
        }

        .jabatan-main-card,
        .jabatan-tab-content,
        .jabatan-home-pane.active,
        .jabatan-home-body,
        .jabatan-data-pane.active {
            min-height: 0;
        }

        .jabatan-tab-content {
            flex: 1 1 auto;
            overflow: hidden;
        }

        .jabatan-main-card > .nav-tabs {
            position: relative;
            z-index: 3;
            flex: 0 0 auto;
            min-height: 46px;
            overflow: visible;
            background: #fff;
        }

        .jabatan-main-card > .nav-tabs .nav-link {
            min-height: 45px;
        }

        .jabatan-tab-content > .tab-pane.active {
            height: 100%;
        }

        .jabatan-home-pane.active,
        .jabatan-data-pane.active {
            display: flex;
            flex-direction: column;
        }

        .jabatan-home-body {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            overflow: hidden;
        }

        .jabatan-search-panel,
        .jabatan-home-body > hr {
            flex: 0 0 auto;
        }

        .jabatan-card-scroll {
            flex: 1 1 auto;
            min-height: 0;
            padding-right: 0.35rem;
            overflow-x: hidden;
            overflow-y: auto;
            scrollbar-gutter: stable;
        }

        .jabatan-data-pane > .card-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card jabatan-main-card">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                                type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan"
                                type="button" role="tab" aria-controls="jabatan" aria-selected="false">Jabatan</button>
                        </li>
                    </ul>
                    <div class="tab-content jabatan-tab-content" id="myTabContent">
                        <div class="tab-pane fade show active jabatan-home-pane" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">DATA MASTER JABATAN</h5>
                                    <input type="hidden" id="menu_id_add" name="menu_id">

                                </div>
                            </div>
                            <div class="card-body jabatan-home-body">

                                <div class="col-lg-8 jabatan-search-panel">
                                    <div class="nav-item d-flex align-items-center">
                                        <i class="mdi mdi-magnify mdi-24px lh-0"></i>
                                        <input type="text" id="search-jabatan"
                                            class="search-jabatan form-control border-0 shadow-none bg-body"
                                            placeholder="Search..." aria-label="Search..." />
                                        <button type="button" class="btn btn-sm btn-success waves-effect waves-light"
                                            data-bs-toggle="modal" data-bs-target="#modal_import_jabatan"><i
                                                class="menu-icon tf-icons mdi mdi-file-excel"></i>Import</button>
                                    </div>
                                </div>
                                <div class="modal fade" id="modal_import_jabatan" data-bs-backdrop="static" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                        <form method="post"
                                            action="@if (Auth::user()->is_admin == 'hrd') {{ url('/hrd/jabatan/ImportJabatan/' . $holding) }}@else{{ url('/jabatan/ImportJabatan/' . $holding) }} @endif"
                                            class="modal-content" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="backDropModalTitle">Import Jabatan</h4>
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
                                                <div class="row g-2 mt-2">
                                                    <a href="{{ asset('') }}" type="button" download=""
                                                        class="btn btn-sm btn-primary"> Download Format Excel</a>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <hr class="5">
                                <div class="containerItems row jabatan-card-scroll">
                                    @foreach ($data_divisi as $divisi)
                                        <div class="col-md-6 col-lg-4"
                                            data-search="{{ $divisi->Departemen->nama_departemen }}">
                                            <div class="card text-center mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <p class="card-text">Departemen : </p>
                                                        @if ($divisi->Departemen == null)
                                                        @else
                                                            {{ $divisi->Departemen->nama_departemen }}
                                                        @endif
                                                    </h6>
                                                    <p class="card-text">({{ $divisi->nama_divisi }})</p>
                                                    <a href="{{ url('jabatan/detail_jabatan', $divisi->id) . '/' . $holding->holding_code }}"
                                                        class="btn btn-primary btn-sm waves-effect waves-light">Lihat
                                                        Jabatan
                                                        ({{ count($divisi->Jabatan) }})
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show jabatan-data-pane" id="jabatan" role="tabpanel" aria-labelledby="home-tab">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">TAMBAH MASTER JABATAN</h5>
                                </div>
                                <br>
                                <button type="button" id="btn_add_jabatan"
                                    class="btn btn-sm btn-primary waves-effect waves-light"><i
                                        class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                                <div class="col-lg-12 my-4">
                                    <table class="table" id="table_master_jabatan"
                                        style="width: 100%; font-size: small;">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama&nbsp;Jabatan</th>
                                                <th>Level&nbsp;Jabatan</th>
                                                <th>Level</th>
                                                <th>Option</th>
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
        </div>
        <!--/ Transactions -->
        <!--/ Data Tables -->
    </div>
    <div class="modal fade" id="modal_tambah_jabatan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="form_add_jabatan" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Jabatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_tambah_jabatan">
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="nama_jabatan_add" name="nama_jabatan"
                                        class="." value="">
                                    <label for="nama_jabatan" class="float-left">Nama Jabatan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select type="text" class="form-select" id="level_id_add" name="level_id"
                                        class="." value="">
                                        <option value="" selected>Pilih Level</option>
                                        @foreach ($get_level as $level)
                                            <option value="{{ $level->id }}">{{ $level->level_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="level_id" class="float-left">Level Jabatan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary" id="btn_save_jabatan">Save</button>
                    </div>
                </form>
            </form>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_jabatan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="form_add_jabatan" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Jabatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_tambah_jabatan">
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="hidden" id="id_update">
                                    <input type="text" class="form-control" id="nama_jabatan_update"
                                        name="nama_jabatan" value="">
                                    <label for="nama_jabatan" class="float-left">Nama Jabatan</label>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select type="text" class="form-select" id="level_id_update" name="level_id"
                                        value="">
                                        <option value="" disabled selected>Pilih Level</option>
                                        @foreach ($get_level as $level)
                                            <option value="{{ $level->id }}">{{ $level->level_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="level_id" class="float-left">Level Jabatan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary" id="btn_update_jabatan">Save</button>
                    </div>
                </form>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('search/e-search.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#search-jabatan').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('[data-search]').filter(function() {
                    $(this).toggle($(this).data('search').toLowerCase().indexOf(value) > -1);
                });
            });
            $('#nama_jabatan_add').on('keyup', function() {
                $(this).val($(this).val().toUpperCase());
            });
            $('#nama_jabatan_update').on('keyup', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>
    <script>
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-36251023-1']);
        _gaq.push(['_setDomainName', 'jqueryscript.net']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') +
                '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <script>
        $(document).on("click", "#btn_add_jabatan", function() {
            $('#modal_tambah_jabatan').modal('show');
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
        var table = $('#table_master_jabatan').DataTable({
            pageLength: 50,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('jabatan_master_dt') }}",
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'jabatan_name',
                    name: 'jabatan_name'
                },
                {
                    data: 'level_name',
                    name: 'level_name'
                },
                {
                    data: 'level_jabatan',
                    name: 'level_jabatan'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [3, 'asc']
            ]
        });
        $('#jabatan-tab').on('shown.bs.tab', function(e) {
            table.columns.adjust().draw().responsive.recalc();
            // table.draw();
        });
        $('#btn_save_jabatan').on('click', function(e) {
            $('#modal_tambah_jabatan').modal('hide');

            e.preventDefault();
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_jabatan', $('#nama_jabatan_add').val());
            formData.append('level_id', $('#level_id_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // formData.append('main', $('#main_update').val());
            // console.log(formData);
            $.ajax({
                type: "POST",

                url: "{{ url('jabatan_master_post') }}",
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
                    alert('Something is wrong');
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
                        $('#table_master_jabatan').DataTable().ajax.reload();
                        $('#form_add_jabatan').trigger('reset')
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
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on("click", "#btn_edit_jabatan", function() {
            var id = $(this).data('id');
            var jabatan_name = $(this).data('jabatan_name');
            var level_id = $(this).data('level_id');
            $('#id_update').val(id);
            $('#nama_jabatan_update').val(jabatan_name);
            $('#modal_edit_jabatan').modal('show');
            $('#level_id_update').val(level_id);
        });
        $('#btn_update_jabatan').on('click', function(e) {
            $('#modal_edit_jabatan').modal('hide');

            e.preventDefault();
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_update').val());
            formData.append('nama_jabatan', $('#nama_jabatan_update').val());
            formData.append('level_id', $('#level_id_update').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // formData.append('main', $('#main_update').val());
            // console.log(formData);
            $.ajax({
                type: "POST",

                url: "{{ url('jabatan_master_update') }}",
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
                    alert('Something is wrong');
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
                        $('#table_master_jabatan').DataTable().ajax.reload();
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
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on('click', '#btn_delete_jabatan', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id = $(this).data('id');
            var menu_id = $('#asoy').val();

            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: id,
                            menu_id: menu_id,
                        },
                        url: "{{ url('jabatan_master_delete') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memuat Data...',
                                html: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal
                                        .showLoading();
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#table_master_jabatan').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            } else {
                                $('#table_master_jabatan').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'error',
                                    text: data.error,
                                    icon: 'error',
                                    timer: 6500
                                })
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
    </script>
@endsection
