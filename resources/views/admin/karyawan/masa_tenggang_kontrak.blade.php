@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
        }

        .masa-tenggang-header,
        .masa-tenggang-tabs-nav {
            flex: 0 0 auto;
        }

        .masa-tenggang-body,
        .masa-tenggang-tab-content,
        .masa-tenggang-pane.active {
            min-height: 0;
        }

        .masa-tenggang-tab-content {
            flex: 1 1 auto;
            overflow: hidden;
        }

        .masa-tenggang-pane.active {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .masa-tenggang-table {
            width: 100% !important;
            table-layout: fixed;
        }

        .masa-tenggang-table th,
        .masa-tenggang-table td {
            padding-right: 0.5rem !important;
            padding-left: 0.5rem !important;
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .masa-tenggang-table th:nth-child(1),
        .masa-tenggang-table td:nth-child(1) {
            width: 4%;
        }

        .masa-tenggang-table th:nth-child(2),
        .masa-tenggang-table td:nth-child(2) {
            width: 10%;
        }

        .masa-tenggang-table th:nth-child(3),
        .masa-tenggang-table td:nth-child(3) {
            width: 14%;
        }

        .masa-tenggang-table th:nth-child(4),
        .masa-tenggang-table td:nth-child(4) {
            width: 13%;
        }

        .masa-tenggang-table th:nth-child(5),
        .masa-tenggang-table td:nth-child(5) {
            width: 12%;
        }

        .masa-tenggang-table th:nth-child(6),
        .masa-tenggang-table td:nth-child(6),
        .masa-tenggang-table th:nth-child(7),
        .masa-tenggang-table td:nth-child(7) {
            width: 10%;
        }

        .masa-tenggang-table th:nth-child(8),
        .masa-tenggang-table td:nth-child(8) {
            width: 14%;
        }

        .masa-tenggang-table th:nth-child(9),
        .masa-tenggang-table td:nth-child(9) {
            width: 13%;
        }

        .masa-tenggang-table-habis th:nth-child(1),
        .masa-tenggang-table-habis td:nth-child(1) {
            width: 4%;
        }

        .masa-tenggang-table-habis th:nth-child(2),
        .masa-tenggang-table-habis td:nth-child(2) {
            width: 11%;
        }

        .masa-tenggang-table-habis th:nth-child(3),
        .masa-tenggang-table-habis td:nth-child(3) {
            width: 15%;
        }

        .masa-tenggang-table-habis th:nth-child(4),
        .masa-tenggang-table-habis td:nth-child(4) {
            width: 14%;
        }

        .masa-tenggang-table-habis th:nth-child(5),
        .masa-tenggang-table-habis td:nth-child(5),
        .masa-tenggang-table-habis th:nth-child(6),
        .masa-tenggang-table-habis td:nth-child(6) {
            width: 11%;
        }

        .masa-tenggang-table-habis th:nth-child(7),
        .masa-tenggang-table-habis td:nth-child(7) {
            width: 18%;
        }

        .masa-tenggang-table-habis th:nth-child(8),
        .masa-tenggang-table-habis td:nth-child(8) {
            width: 16%;
        }

        .masa-tenggang-table th:last-child,
        .masa-tenggang-table td:last-child {
            text-align: center;
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
                    <div class="card-header masa-tenggang-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA KARYAWAN MASA TENGGANG KONTRAK</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modal_keterangan_masa_tenggang">
                                <i class="mdi mdi-information-outline me-1"></i>Keterangan
                            </button>
                        </div>
                    </div>
                    <div class="card-body py-3 masa-tenggang-body">
                        <ul class="nav nav-tabs masa-tenggang-tabs-nav" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active position-relative" id="habis-tab" data-bs-toggle="tab"
                                    data-bs-target="#habis" type="button" role="tab" aria-controls="habis"
                                    aria-selected="true">Kontrak
                                    Habis
                                    <span
                                        class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.55rem;">
                                        {{ $habis }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link position-relative" id="atas-tab" data-bs-toggle="tab"
                                    data-bs-target="#atas" type="button" role="tab" aria-controls="atas"
                                    aria-selected="false">Atasan
                                    <span
                                        class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.55rem;">
                                        {{ $atas }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link position-relative" id="tengah-tab" data-bs-toggle="tab"
                                    data-bs-target="#tengah" type="button" role="tab" aria-controls="tengah"
                                    aria-selected="false">Menengah &
                                    Bawahan
                                    <span
                                        class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.55rem;">
                                        {{ $tengah }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </button>
                            </li>

                        </ul>
                        <div class="tab-content masa-tenggang-tab-content" id="myTabContent">
                            <div class="tab-pane fade show active masa-tenggang-pane" id="habis" role="tabpanel"
                                aria-labelledby="habis-tab">
                                <div class="master-table-area">
                                <table class="table masa-tenggang-table masa-tenggang-table-habis"
                                    id="table_karyawan_bulanan_tenggang"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nomor&nbsp;ID</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Telepon</th>
                                            <th>Mulai&nbsp;Kontrak</th>
                                            <th>Selesai&nbsp;Kontrak</th>
                                            <th>Kontrak&nbsp;Kerja</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <div class="tab-pane fade masa-tenggang-pane" id="atas" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="master-table-area">
                                <table class="table masa-tenggang-table" id="table_karyawan_tenggang_atasan"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nomor&nbsp;ID</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Telepon</th>
                                            <th>Nama&nbsp;Jabatan</th>
                                            <th>Mulai&nbsp;Kontrak</th>
                                            <th>Selesai&nbsp;Kontrak</th>
                                            <th>Kontrak&nbsp;Kerja</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <div class="tab-pane fade masa-tenggang-pane" id="tengah" role="tabpanel" aria-labelledby="contact-tab">
                                <div class="master-table-area">
                                <table class="table masa-tenggang-table" id="table_karyawan_tenggang_menengah"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nomor&nbsp;ID</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Telepon</th>
                                            <th>Nama&nbsp;Jabatan</th>
                                            <th>Mulai&nbsp;Kontrak</th>
                                            <th>Selesai&nbsp;Kontrak</th>
                                            <th>Kontrak&nbsp;Kerja</th>
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
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
    <div class="modal fade" id="modal_keterangan_masa_tenggang" tabindex="-1"
        aria-labelledby="modal_keterangan_masa_tenggang_title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_keterangan_masa_tenggang_title">
                        Keterangan Notifikasi Masa Tenggang Kontrak
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: smaller;">
                            <thead class="table-primary">
                                <tr>
                                    <th>No.</th>
                                    <th>Keterangan</th>
                                    <th>Jabatan</th>
                                    <th>Notifikasi Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>ATASAN</td>
                                    <td>DIREKTUR, ASM, HEAD, MANAGER, JUNIOR MANAGER, REGIONAL MANAGER</td>
                                    <td>90 HARI</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>MENENGAH DAN BAWAHAN</td>
                                    <td>SUPERVISOR, ADMIN, HELPER, KERNET, KOORDINATOR, OPERATOR, SALES, SOPIR, STAF</td>
                                    <td>60 HARI</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_non_aktif_karyawan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form method="post" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Form Non Aktif Karyawan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mt-2">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <!-- Account -->
                                <div class="card-body">
                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                        <input type="hidden" name="id_nonactive" id="id_nonactive" value="">
                                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="user-avatar"
                                            class="d-block w-px-120 h-px-120 rounded" id="template_foto_karyawan" />
                                        <table>
                                            <tr>
                                                <th>Nama</th>
                                                <td>&nbsp;</td>
                                                <td>:</td>
                                                <td id="td_nama"></td>
                                            </tr>
                                            <tr>
                                                <th>Kontrak Kerja</th>
                                                <td>&nbsp;</td>
                                                <td>:</td>
                                                <td id="td_kontrak_kerja"></td>
                                            </tr>
                                            <tr>
                                                <th>Penempatan Kerja</th>
                                                <td>&nbsp;</td>
                                                <td>:</td>
                                                <td id="td_penempatan_kerja"> </td>
                                            </tr>
                                            <tr>
                                                <th>Tgl Mulai Kontrak</th>
                                                <td>&nbsp;</td>
                                                <td>:</td>
                                                <td id="td_mulai_kontrak"> </td>
                                            </tr>
                                            <tr>
                                                <th>Tgl Selesai Kontrak</th>
                                                <td>&nbsp;</td>
                                                <td>:</td>
                                                <td id="td_selesai_kontrak"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="date_now_add" name="date_now" class="form-control"
                                    placeholder="Tanggal Nonaktif" />
                                <label for="date_now">Tanggal Nonaktif</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <textarea rows="10" id="alasan_non_aktif_add" name="alasan_non_aktif" class="form-control"
                                    placeholder="Alasan"></textarea>
                                <label for="alasan_non_aktif">Alasan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-success" id="simpan_nonaktif">
                        Save
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    {{-- <script>
        let holding = window.location.pathname.split("/").pop();
        var table = $('#table_masa_tenggang_kontrak').DataTable({
            pageLength: 50,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('database_masa_tenggang_kontrak') }}" + '/' + holding,
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
                    data: 'telepon',
                    name: 'telepon'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'tgl_kontrak',
                    name: 'tgl_kontrak'
                },
                {
                    data: 'status',
                    name: 'status'
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
    </script> --}}
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
        })
        $(document).on('click', '#btn_non_aktif_karyawan', function() {
            var id = $(this).data('id');
            var holding = $(this).data("holding");
            var nama = $(this).data('nama');
            var foto = $(this).data('foto');
            var tgl_mulai_kontrak = $(this).data('tgl_mulai_kontrak');
            var tgl_selesai_kontrak = $(this).data('tgl_selesai_kontrak');
            var kontrak_kerja = $(this).data('kontrak_kerja');
            var penempatan_kerja = $(this).data('penempatan_kerja');
            if (foto == '' | foto == null) {
                $('#template_foto_karyawan').attr('src', "{{ asset('admin/assets/img/avatars/1.png') }}");
            } else {
                $('#template_foto_karyawan').attr('src', "{{ url('storage/app/public/foto_karyawan/') }}" + foto);
            }
            $('#td_nama').html(nama);
            $('#td_mulai_kontrak').html(tgl_mulai_kontrak);
            $('#td_selesai_kontrak').html(tgl_selesai_kontrak);
            $('#td_kontrak_kerja').html(kontrak_kerja);
            $('#td_penempatan_kerja').html(penempatan_kerja);
            $('#id_nonactive').val(id);
            $('#modal_non_aktif_karyawan').modal('show');
        });
        var holding = '{{ $holding->holding_code }}';

        var table = $('#table_karyawan_bulanan_tenggang').DataTable({
            pageLength: 10,
            scrollY: '200px',
            scrollCollapse: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_bulanan_tenggang-datatable') }}" + '/' + holding,
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
                    data: 'mulai_kontrak',
                    name: 'mulai_kontrak'
                },
                {
                    data: 'selesai_kontrak',
                    name: 'selesai_kontrak'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [5, 'asc']
            ]
        });
        $('#habis').on('shown.bs.tab', function(e) {
            table.columns.adjust().draw().responsive.recalc();
            // table.draw();
        });
        var table1 = $('#table_karyawan_tenggang_atasan').DataTable({
            pageLength: 10,
            scrollY: '200px',
            scrollCollapse: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_tenggang_atasan-datatable') }}" + '/' + holding,
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
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'mulai_kontrak',
                    name: 'mulai_kontrak'
                },
                {
                    data: 'selesai_kontrak',
                    name: 'selesai_kontrak'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [6, 'asc']
            ]
        });
        $('#atas').on('shown.bs.tab', function(e) {
            table1.columns.adjust().draw().responsive.recalc();
            // table.draw();
        })
        var table2 = $('#table_karyawan_tenggang_menengah').DataTable({
            pageLength: 10,
            scrollY: '200px',
            scrollCollapse: true,
            scrollX: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_tenggang_menengah-datatable') }}" + '/' + holding,
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
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'mulai_kontrak',
                    name: 'mulai_kontrak'
                },
                {
                    data: 'selesai_kontrak',
                    name: 'selesai_kontrak'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
            order: [
                [6, 'asc']
            ]
        });
        $('#tengah').on('shown.bs.tab', function(e) {
            table2.columns.adjust().draw().responsive.recalc();
            // table.draw();
        })
        $('#simpan_nonaktif').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('date_now', $('#date_now_add').val());
            formData.append('id_nonactive', $('#id_nonactive').val());
            formData.append('alasan_non_aktif', $('#alasan_non_aktif_add').val());
            $('#modal_non_aktif_karyawan').modal('hide');

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan/non_aktif_proses') }}",
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
                        $('#table_karyawan_bulanan').DataTable().ajax.reload();
                        $('#table_karyawan_bulanan_nonaktif').DataTable().ajax.reload();
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
    </script>
@endsection
