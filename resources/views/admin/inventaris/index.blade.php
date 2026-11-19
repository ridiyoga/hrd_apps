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

        .border-dashed {
            border-style: dashed !important;
        }

        .file-upload-wrapper {
            transition: background-color 0.2s ease-in-out;
        }

        .file-upload-wrapper:hover {
            background-color: #e9ecef !important;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">INVENTARIS</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="d-flex master-table-toolbar">
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light me-1"
                                data-bs-toggle="modal" data-bs-target="#modal_tambah_inventaris"><i
                                    class="menu-icon tf-icons mdi mdi-plus"></i>Tambah</button>
                            <table class="table" id="table_inventaris2" style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Opsi</th>
                                        <th>Nama&nbsp;Inventaris</th>
                                        <th>Foto</th>
                                        <th>Nomor&nbsp;Seri</th>
                                        <th>ID&nbsp;Aset</th>
                                        <th>Nama&nbsp;Kategori</th>
                                        <th>Holding&nbsp;Pemilik</th>
                                        <th>Site</th>
                                        <th>Nama&nbsp;Gedung</th>
                                        <th>Nama&nbsp;Ruangan</th>
                                        <th>Status</th>
                                        <th>Kondisi</th>
                                        <th>Keterangan</th>
                                        <th>Nama&nbsp;Karyawan</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <table class="table" id="table_inventaris" style="width: 100%; font-size: smaller;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Opsi</th>
                                    <th>Nama&nbsp;Inventaris</th>
                                    <th>Foto</th>
                                    <th>Nomor&nbsp;Seri</th>
                                    <th>ID&nbsp;Aset</th>
                                    <th>Nama&nbsp;Kategori</th>
                                    <th>Holding&nbsp;Pemilik</th>
                                    <th>Site</th>
                                    <th>Nama&nbsp;Gedung</th>
                                    <th>Nama&nbsp;Ruangan</th>
                                    <th>Status</th>
                                    <th>Kondisi</th>
                                    <th>Keterangan</th>
                                    <th>Nama&nbsp;Karyawan</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
    <div class="modal fade" id="modal_tambah_inventaris" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form method="post" id="add_inventaris_form" class="modal-content" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="backDropModalTitle">Tambah Inventaris</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="nama_inventaris_add" name="nama_inventaris"
                                    placeholder="Nama Inventaris" />
                                <label for="nama_inventaris_add" class="float-left">Nama Inventaris</label>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="kode_add" name="kode"
                                    placeholder="Nama Ruangan" />
                                <label for="kode_add" class="float-left">Kode</label>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="serial_number_add" name="serial_number"
                                    placeholder="Nomor Seri" />
                                <label for="serial_number_add" class="float-left">Nomor Seri</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="id_aset_add" name="id_aset"
                                    placeholder="ID Aset" />
                                <label for="id_aset_add" class="float-left">ID Aset</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_kategori" id="id_kategori_add"
                                    placeholder="Pilih Site">
                                    <option disabled value="" selected>-Pilih Kategori-</option>
                                    @foreach ($kategori as $kt)
                                        <option value="{{ $kt->id }}">{{ $kt->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_holding" id="id_holding_add"
                                    placeholder="Pemilik Inventaris">
                                    <option disabled value="" selected>Pilih Holding</option>
                                    @foreach ($holding_select as $st)
                                        <option value="{{ $st->id }}">{{ $st->holding_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_site" id="id_site_add"
                                    placeholder="Pilih Site">
                                    <option disabled value="" selected>-Pilih Site-</option>
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
                                    placeholder="Pilih Gedung">
                                    <option disabled value="">-Pilih Gedung-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_ruangan" id="id_ruangan_add"
                                    placeholder="Pilih Ruangan">
                                    <option disabled value="">-Pilih Ruangan-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="status" id="status_add"
                                    placeholder="Pilih Status">
                                    <option disabled selected value="">Pilih Status</option>
                                    <option value="1">Terpakai</option>
                                    <option value="2">Tidak Terpakai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="kondisi" id="kondisi_add"
                                    placeholder="Pilih Kondisi">
                                    <option disabled value="" selected>-Pilih Kondisi-</option>
                                    @foreach ($kondisi as $kd)
                                        <option value="{{ $kd->id }}">{{ $kd->nama_kondisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <select type="text" class="form-control" name="id_karyawan" id="id_karyawan_add"
                                    placeholder="Pilih Karyawan">
                                    <option disabled value="" selected>Pilih Karyawan</option>
                                    @foreach ($karyawan as $kk)
                                        <option value="{{ $kk->id }}">{{ $kk->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea id="keterangan_add" name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
                                <label for="keterangan_add" class="float-left">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <!-- Label Utama -->
                        <label for="foto" class="form-label fw-bold text-dark mb-2">
                            <i class="fa-solid fa-file-medical text-primary me-2"></i>Foto Terkini
                        </label>

                        <!-- Container Alert -->
                        <div class="alert_pulang mb-2"></div>

                        <!-- Upload Box -->
                        <div
                            class="file-upload-wrapper text-center p-4 rounded-3 border border-2 border-dashed bg-light position-relative overflow-hidden">

                            <!-- Input File Overlay -->
                            <input type="file" id="foto" name="foto"
                                class="position-absolute top-0 start-0 w-100 h-100 opacity-0 z-3" accept="image/*,.pdf"
                                style="cursor: pointer;" />

                            <!-- Tampilan Default (Belum Ada File) -->
                            <div class="file-upload-content" id="upload_default">
                                <div class="icon-circle mb-3 mx-auto bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-cloud-arrow-up fs-4 text-primary"></i>
                                </div>
                                <h6 class="fw-semibold text-dark mb-1">Foto Terkini</h6>
                                <p class="text-muted small mb-0">Klik atau seret file ke area ini</p>
                                <span class="badge bg-white text-secondary border mt-2 font-monospace">Format: JPG, PNG,
                                    PDF (Max 2MB)</span>
                            </div>

                            <!-- Tampilan Sukses (Setelah File Dipilih) -->
                            <div class="file-upload-content d-none" id="upload_success">
                                <!-- Preview Gambar (Otomatis Tampil Jika Berupa Foto) -->
                                <div class="mb-3 d-none" id="image_preview_container">
                                    <img id="image_preview" src="" alt="Preview Foto"
                                        class="img-thumbnail rounded" style="max-height: 150px; object-fit: cover;" />
                                </div>

                                <!-- Icon Default Jika File Adalah PDF -->
                                <div id="pdf_icon_container"
                                    class="icon-circle mb-3 mx-auto bg-success bg-opacity-10 text-success shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-file-circle-check fs-3"></i>
                                </div>

                                <h6 class="fw-semibold text-dark mb-1 text-truncate px-3" id="file_name_display">
                                    Nama_File.pdf</h6>
                                <p class="text-muted small mb-2" id="file_size_display">0 KB</p>

                                <span class="badge bg-success text-white mb-2">
                                    <i class="fa-solid fa-check me-1"></i>File Berhasil Dipilih
                                </span>

                                <div>
                                    <small class="text-primary text-decoration-underline fw-semibold">Klik area ini untuk
                                        mengganti file</small>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_save_inventaris" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_inventaris" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <form class="modal-content" id="edit_inventaris_form">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Edit Inventaris</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="hidden" name="id_inventaris" id="id_inventaris_update">
                                <input type="text" class="form-control" id="nama_inventaris_update"
                                    name="nama_inventaris" placeholder="Nama Inventaris">
                                <label for="nama_inventaris_update">Nama Inventaris</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="serial_number_update"
                                    name="serial_number" placeholder="Nomor Seri">
                                <label for="serial_number_update">Nomor Seri</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="id_aset_update" name="id_aset"
                                    placeholder="ID Aset">
                                <label for="id_aset_update">ID Aset</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea id="keterangan_update" name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
                                <label for="keterangan_update">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <textarea id="nama_excel_update" name="nama_excel" class="form-control" placeholder="Nama Excel"></textarea>
                                <label for="nama_excel_update">Nama Excel</label>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="form-floating form-floating-outline">
                            <select type="text" class="form-control" name="id_karyawan" id="id_karyawan_update"
                                placeholder="Pilih Karyawan">
                                <option disabled value="" selected>Pilih Karyawan</option>
                                @foreach ($karyawan as $kk)
                                    <option value="{{ $kk->id }}">{{ $kk->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="btn_update_inventaris" class="btn btn-primary">Update</button>
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
            var table = $('#table_inventaris').DataTable({
                pageLength: 20,
                scrollY: '500px',
                scrollCollapse: true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('inventaris-datatable') }}",
                    data: {
                        holding: holding
                    },
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                    {
                        data: 'nama_inventaris',
                        name: 'nama_inventaris'
                    },
                    {
                        data: 'foto',
                        name: 'foto'
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'id_aset',
                        name: 'id_aset'
                    },
                    {
                        data: 'nama_kategori',
                        name: 'nama_kategori'
                    },
                    {
                        data: 'nama_holding',
                        name: 'nama_holding'
                    },
                    {
                        data: 'nama_site',
                        name: 'nama_site'
                    },
                    {
                        data: 'nama_gedung',
                        name: 'nama_gedung'
                    },
                    {
                        data: 'nama_ruangan',
                        name: 'nama_ruangan'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'nama_kondisi',
                        name: 'nama_kondisi'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'nama_karyawan',
                        name: 'nama_karyawan'
                    },
                ],
                order: [
                    [2, 'asc'],
                ],
            });

            $('#nama_ruangan_add').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
            $('#id_site_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Site',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#id_holding_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pemilik Inventaris',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#id_gedung_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Gedung',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#id_ruangan_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Ruangan',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#id_karyawan_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Karyawan yang Membawa',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#id_kategori_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kategori',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#kondisi_add').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kondisi',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_tambah_inventaris')
            });
            $('#id_karyawan_update').select2({
                theme: 'bootstrap-5',
                placeholder: 'Karyawan yang Membawa',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_edit_inventaris')
            });
            // $('#modal_tambah_inventaris').on('hidden.bs.modal', function() {
            //     $('#add_inventaris_form')[0].reset();
            //     $('#id_gedung_add').val(null).trigger('change');
            //     $('#id_karyawan_add').val(null).trigger('change');
            //     $('#id_ruangan_add').val(null).trigger('change');
            //     $('#id_site_add').val(null).trigger('change');
            // });
            // Mencari Ruangan
            function showSelectLoading(selectId) {
                $(selectId).prop('disabled', true)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .removeClass('d-none');
            }

            // function showTableLoading() {
            //     $('#tableLoading').removeClass('d-none');
            // }

            // function hideTableLoading() {
            //     $('#tableLoading').addClass('d-none');
            // }

            function hideSelectLoading(selectId) {
                $(selectId).prop('disabled', false)
                    .closest('.form-floating')
                    .find('.select-loader')
                    .addClass('d-none');
            }

            $('#id_site_add').on('change', function() {
                let id_gedung = $(this).val();

                // 1. JIKA DI-RESET (NILAI NULL/KOSONG), BERSIHKAN CHILD SELECT & STOP
                if (!id_gedung) {
                    $('#id_gedung_add').html('<option></option>').val(null).trigger('change.select2');
                    return; // Hentikan eksekusi AJAX
                }

                // 2. JIKA ADA NILAI, JALANKAN PROSES AJAX SEPERTI BIASA
                showSelectLoading('#id_gedung_add');
                let url = "{{ url('/ruangan/get_gedung') }}" + "/" + id_gedung;

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
                    // Catatan: contentType, cache, processData opsional untuk request GET biasa
                    success: function(response) {
                        hideSelectLoading('#id_gedung_add');

                        // Masukkan HTML option baru dari controller
                        $('#id_gedung_add').html(response);

                        // Re-trigger agar Select2 membaca option baru
                        $('#id_gedung_add').trigger('change.select2');

                        Swal.close();
                    },
                    error: function(data) {
                        hideSelectLoading('#id_gedung_add');

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
            $('#id_gedung_add').on('change', function() {
                let id_site = $(this).val();
                if (!id_site) {
                    $('#id_ruangan_add').html('<option></option>').val(null).trigger('change.select2');
                    return; // Hentikan eksekusi AJAX
                }
                showSelectLoading('#id_ruangan_add');
                let url = "{{ url('/ruangan/get_ruangan') }}" + "/" + id_site;

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
                        hideSelectLoading('#id_ruangan_add');
                        $('#id_ruangan_add').html(response);

                        // Tutup SweetAlert setelah data berhasil dimuat
                        Swal.close();
                    },
                    error: function(data) {
                        hideSelectLoading(
                            '#id_ruangan_add'
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
            $('#table_inventaris2').hide();
            var table2 = $('#table_inventaris2').DataTable({
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                paginate: false,
                filter: false,
                info: false,
                dom: 'Bfrtip',
                ajax: {
                    url: "{{ url('inventaris-datatable') }}",
                    data: {
                        holding: holding
                    },
                },
                buttons: [{
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                    titleAttr: 'Excel',
                    title: 'ruangan',
                    messageTop: 'Inventaris INVENTARIS',
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
                        data: 'option',
                        name: 'option'
                    },
                    {
                        data: 'nama_inventaris',
                        name: 'nama_inventaris'
                    },
                    {
                        data: 'nama_inventaris',
                        name: 'nama_inventaris'
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'id_aset',
                        name: 'id_aset'
                    },
                    {
                        data: 'nama_kategori',
                        name: 'nama_kategori'
                    },
                    {
                        data: 'nama_holding',
                        name: 'nama_holding'
                    },
                    {
                        data: 'nama_site',
                        name: 'nama_site'
                    },
                    {
                        data: 'nama_gedung',
                        name: 'nama_gedung'
                    },
                    {
                        data: 'nama_ruangan',
                        name: 'nama_ruangan'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'nama_kondisi',
                        name: 'nama_kondisi'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'nama_karyawan',
                        name: 'nama_karyawan'
                    },
                ],
                order: [
                    [2, 'asc'],
                ],
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
            $(document).on("click", "#btn_edit_inventaris", function() {

                let id = $(this).data('id');
                let nama_inventaris = $(this).data("nama_inventaris");
                let keterangan = $(this).data("keterangan");
                let serial_number = $(this).data("serial_number");
                let id_aset = $(this).data("id_aset");
                let id_karyawan = $(this).data("id_karyawan");
                let nama_excel = $(this).data("nama_excel");

                $('#id_inventaris_update').val(id);
                $('#nama_inventaris_update').val(nama_inventaris);
                $('#keterangan_update').val(keterangan);
                $('#serial_number_update').val(serial_number);
                $('#id_aset_update').val(id_aset);
                $('#id_karyawan_update').val(id_karyawan).trigger('change');
                $('#nama_excel_update').val(nama_excel);

                $('#modal_edit_inventaris').modal('show');
            });
            $('#btn_update_inventaris').on('click', function(e) {
                e.preventDefault();

                const formData = new FormData($('#edit_inventaris_form')[0]);
                formData.append('menu_id', $('#menu_id_add').val());

                $.ajax({
                    type: 'POST',
                    url: "{{ url('/inventaris/update/') }}" + '/' + holding,
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Memuat Data...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        const message = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Terjadi kesalahan saat memperbarui inventaris.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: message
                        });
                    },
                    success: function(data) {
                        Swal.close();
                        if (data.code == 200) {
                            $('#modal_edit_inventaris').modal('hide');
                            $('#table_inventaris').DataTable().ajax.reload();
                            Swal.fire({
                                title: 'Berhasil',
                                text: data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        } else if (data.code == 400) {
                            const errorMessages = Object.values(data.errors)
                                .flat()
                                .map(message => `• ${message}`)
                                .join('\n');
                            Swal.fire({
                                title: 'Validasi gagal',
                                text: errorMessages,
                                icon: 'warning'
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: data.error || data.message ||
                                    'Data gagal diperbarui.',
                                icon: 'error'
                            });
                        }
                    }
                });
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
            $(document).on('click', '#btn_save_inventaris', function(e) {
                // console.log('asooy')
                $('#modal_tambah_inventaris').modal('hide');
                e.preventDefault();
                let btn = $(this);
                let originalText = btn.html();

                //ambil data dari form
                let formElement = $('#add_inventaris_form')[0]; // Ambil elemen DOM form
                let formData = new FormData(formElement);
                formData.append('menu_id', $('#menu_id_add').val());


                // post
                $.ajax({
                    type: "POST",

                    url: "{{ url('/inventaris/insert/') }}" + '/' + holding,
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
                        // console.log(data);
                        Swal.close();
                        if (data.code == 200) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: data.message,
                                icon: 'success',
                                timer: 5000
                            })
                            //mengosongkan modal dan menyembunyikannya
                            $('#add_inventaris_form')[0].reset();
                            $('#id_site_add, #id_kategori_add, #id_karyawan_add, #kondisi_add, #id_holding_add, #status_add')
                                .val(null)
                                .trigger('change');
                            $('#foto').val('').trigger('change');
                            $('#file_name_display').text('Nama_File.pdf');
                            $('#file_size_display').text('0 KB');
                            $('#image_preview_container').addClass('d-none');
                            $('#pdf_icon_container').removeClass('d-none');
                            $('#id_gedung_add, #id_ruangan_add')
                                .html('<option></option>')
                                .val(null)
                                .trigger('change');
                            $('#table_inventaris').DataTable().ajax.reload();
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
            $(document).on('click', '#btn_delete_inventaris', function() {
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
                            url: "{{ url('/inventaris/delete/') }}" + '/' + id +
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
                                    $('#table_inventaris').DataTable().ajax.reload();
                                } else if (data.status == 2) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gedung Masih Terhubung Karyawan',
                                        icon: 'error',
                                        timer: 1500
                                    })
                                    $('#table_inventaris').DataTable().ajax.reload();
                                } else if (data.status == 0) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gedung Masih Terhubung Ruangan',
                                        icon: 'error',
                                        timer: 1500
                                    })
                                    $('#table_inventaris').DataTable().ajax.reload();
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
            $('#foto').on('change', function() {
                const file = this.files[0];

                if (file) {
                    // 1. Format Ukuran File
                    let sizeText = (file.size < 1024 * 1024) ?
                        (file.size / 1024).toFixed(1) + ' KB' :
                        (file.size / (1024 * 1024)).toFixed(2) + ' MB';

                    $('#file_name_display').text(file.name);
                    $('#file_size_display').text(sizeText);

                    // 2. Logic Preview Gambar vs File Lain (PDF)
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#image_preview').attr('src', e.target.result);
                            $('#image_preview_container').removeClass('d-none');
                            $('#pdf_icon_container').addClass('d-none');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // Jika PDF atau format lain non-gambar
                        $('#image_preview_container').addClass('d-none');
                        $('#pdf_icon_container').removeClass('d-none');
                    }

                    // 3. Switch Tampilan
                    $('#upload_default').addClass('d-none');
                    $('#upload_success').removeClass('d-none');
                } else {
                    // Reset ke tampilan default jika batal memilih
                    $('#upload_default').removeClass('d-none');
                    $('#upload_success').addClass('d-none');
                    $('#image_preview').attr('src', '');
                }
            });
        });
    </script>
@endsection
