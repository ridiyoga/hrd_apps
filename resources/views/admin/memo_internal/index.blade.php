@extends('admin.layouts.dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<style>
    .swal2-container {
        z-index: 9999 !important;
    }

    .table .btn-sm {
        padding: 4px 8px;
        font-size: 0.75rem;
    }

    .page-header {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 16px;
        padding: 25px;
        color: white;
    }

    .memo-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    }

    .memo-table thead {
        background: #f8fafc;
    }

    .memo-table tbody tr {
        transition: all .2s ease;
    }

    .memo-table tbody tr:hover {
        background: #f1f5f9;
    }

    .badge-publish {
        background: #16a34a;
    }

    .badge-draft {
        background: #f59e0b;
    }

    .btn-create {
        border-radius: 10px;
        padding: 10px 18px;
    }

    .stat-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    /* MODAL ADD MEMO */
    .memo-modal {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
    }

    .memo-header {
        /* background: linear-gradient(135deg, #4f46e5, #7c3aed); */
        color: white;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }

    .form-modern {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
    }

    .form-modern:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    #editorMemo {
        height: 200px;
        background: white;
        border-radius: 10px;
    }

    .holiday-box {
        background: #f8fafc;
        padding: 15px;
        border-radius: 10px;
        border: 1px dashed #e2e8f0;
    }

    .btn-save-memo {
        border-radius: 10px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
    }

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

    /* Container kustom untuk loader */
    .custom-loader-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px 0;
    }

    /* Animasi Double Ring yang Elegan */
    .modern-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(102, 108, 255, 0.1);
        border-left-color: #666cff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        position: relative;
    }

    .modern-spinner::before {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        border: 4px solid rgba(102, 108, 255, 0.1);
        border-left-color: #00cfe8;
        border-radius: 50%;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Efek teks berdenyut (Pulse) */
    .pulse-text {
        animation: pulse 1.5s ease-in-out infinite;
        font-weight: 500;
        color: #5d596c;
        letter-spacing: 1px;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Judul */
    .swal2-title.premium-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700 !important;
        color: #1a202c !important;
        font-size: 1.25rem !important;
        padding-top: 10px !important;
    }

    /* Isi Teks */
    .swal2-html-container.premium-text {
        color: #718096 !important;
        font-size: 0.95rem !important;
        line-height: 1.5 !important;
    }

    /* Tombol Konfirmasi (Utama) */
    .swal2-confirm.premium-confirm {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%) !important;
        border-radius: 14px !important;
        padding: 12px 30px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
        font-size: 0.9rem !important;
    }

    /* Tombol Batal/Cancel */
    .swal2-cancel.premium-cancel {
        background: #f1f5f9 !important;
        color: #64748b !important;
        border-radius: 14px !important;
        padding: 12px 30px !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
    }

    /* Ikon */
    .swal2-icon {
        border-width: 3px !important;
        scale: 0.8;
        /* Mengecilkan sedikit agar proporsional di mobile */
    }

    /* Animasi Masuk Smooth */
    .swal2-show {
        animation: swal-smooth-show 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }

    @keyframes swal-smooth-show {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
@endsection

@section('isi')
@include('sweetalert::alert')

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- HEADER --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h3 class="mb-1 text-white">Memo Internal</h3>
                <small>Manajemen Pengumuman & Memo Perusahaan</small>
            </div>

            <button class="btn btn-light btn-create">
                + Buat Memo
            </button>
        </div>
    </div>


    {{-- STATISTIK --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <small>Total Memo</small>
                <h4 class="mb-0">{{ $count_memo_all }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <small>Memo Publish</small>
                <h4 class="mb-0 text-success">{{ $count_memo_publish }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <small>Draft</small>
                <h4 class="mb-0 text-warning">{{ $count_memo_draft }}</h4>
            </div>
        </div>

    </div>


    {{-- TABLE MEMO --}}
    <div class="card memo-card">
        <div class="card-body">

            <table class="table memo-table" id="tableMemo" style="width: 100%; font-size: 0.8rem;">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nomor&nbsp;Memo</th>
                        <th>Perihal</th>
                        <th>Dibuat&nbsp;Oleh</th>
                        <th>Status</th>
                        <th>File</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>


                </tbody>

            </table>

        </div>
    </div>
    <!-- Modal   -->
    <div class="modal fade" id="modal_tambah_memo" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content memo-modal">

                <div class="modal-header memo-header">

                    <h5 class="modal-title">

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    <form id="form_memo" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="text" id="id_memo" name="id_memo" value="">
                        <div class="row g-4">

                            <div class="col-md-4">
                                <label class="form-label">Holding</label>
                                <select name="holding[]" id="holding" class="form-control form-modern" multiple>
                                    @foreach($holding_all as $item)
                                    <option value="{{$item->id}}">{{$item->holding_name}}</option>
                                    @endforeach
                                </select>
                                <div class="alert-holding alert-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Memo</label>
                                <input type="date" class="form-control form-modern" name="tanggal" id="tanggal" value="">
                                <div class="alert-tanggal alert-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Penandatangan</label>
                                <select class="form-control form-modern" name="penandatangan" id="penandatangan">
                                    <option value="">~ Pilih Penandatangan ~</option>
                                    <option value="1">DIREKTUR</option>
                                    <option value="2">HRD</option>
                                </select>
                                <div class="alert-penandatangan"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Perihal Memo</label>
                                <input type="text" class="form-control form-modern" placeholder="Contoh : Libur Idul Fitri 2026" name="perihal" id="perihal" value="">
                                <div class="alert-perihal alert-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Penerima</label>
                                <select class="form-control form-modern" name="penerima" id="penerima">
                                    <option value="">~ Pilih Penerima ~</option>
                                    <option value="all">Seluruh Karyawan</option>
                                    <option value="departemen">Departemen</option>
                                    <option value="manajemen">Manajemen</option>
                                </select>
                                <div class="alert-penerima alert-error"></div>
                            </div>


                            <div class="col-md-12">
                                <label class="form-label">Isi Memo</label>
                                <div id="editorMemo"></div>
                                <div class="alert-isi_memo alert-error"></div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="holiday-box">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isHoliday">
                                        <span class="form-check-label">
                                            Tandai sebagai Hari Libur (Sync Attendance)
                                        </span>
                                    </label>
                                    <div class="row mt-3 holiday-section d-none">
                                        <div class="col-md-6">
                                            <label>Tanggal Mulai Libur</label>
                                            <input type="date" class="form-control form-modern" name="tanggal_mulai_libur" id="tanggal_mulai_libur" value="">
                                            <div class="alert-tanggal_mulai_libur alert-error"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Tanggal Selesai Libur</label>
                                            <input type="date" class="form-control form-modern" name="tanggal_selesai_libur" id="tanggal_selesai_libur" value="">
                                            <div class="alert-tanggal_selesai_libur alert-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card shadow-sm mb-4 border-start border-primary border-3" i>
                                    <div
                                        class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-dark fw-bold">
                                            <i class="mdi mdi-file-document-outline me-2"></i>LAMPIRAN MEMO INTERNAL
                                        </h6>
                                        <small class="text-muted">Format: JPG, PNG (Maks. 5MB)</small>
                                    </div>
                                    <div class="card-body p-4" id="berkas_lampiran">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0">

                    <button class="btn btn-light" data-bs-dismiss="modal" type="button">
                        Batal
                    </button>

                    <button class="btn btn-secondary btn-preview">
                        Preview
                    </button>

                    <button class="btn btn-primary btn-save-memo">
                        Ajukan Memo
                    </button>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@section('js')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    $(document).ready(function() {
        var holding = "{{$holding->id}}";
        var url = "{{ url('memo_internal/datatable/') }}" + "/" + holding;
        var quill = new Quill('#editorMemo', {
            theme: 'snow'
        });
        $('#holding').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Holding",
            allowClear: true,
            dropdownParent: $('#modal_tambah_memo')
        });
        $('#isHoliday').change(function() {

            if ($(this).is(':checked')) {
                $('.holiday-section').removeClass('d-none')
            } else {
                $('.holiday-section').addClass('d-none')
            }

        });
        $('#tableMemo').DataTable({
            responsive: true,
            pageLength: 10,
            "scrollY": true,
            "scrollX": true,
            language: {
                search: "Cari Memo :"
            },
            ajax: {
                url: url,
            },
            columns: [{
                    data: "id",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tanggal_memo',
                    name: 'tanggal_memo'
                },
                {
                    data: 'nomor_memo',
                    name: 'nomor_memo'
                },
                {
                    data: 'perihal',
                    name: 'perihal'
                },
                {
                    data: 'dibuat_oleh',
                    name: 'dibuat_oleh'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'file',
                    name: 'file'
                },
                {
                    data: 'option',
                    name: 'option'
                },
            ],
        });
        $(document).on('click', '.btn-create', function() {
            $('#form_memo')[0].reset();
            quill.root.innerHTML = '';
            $('.modal-title').html('Tambah Memo Internal');
            $('.btn-save-memo').html('Ajukan Memo');
            $('.form-control').removeClass('is-invalid');
            $('.alert-error').empty();
            $('#holding').val('').trigger('change');
            $('#lampiran').val('');
            $('#berkas_lampiran').empty();
            var base_url_lampiran = "{{ asset('storage/lampiran') }}";
            var lampiran_complete = `<div id="lampiran_empty_row" class="p-2 border border-dashed rounded mb-2 d-flex align-items-center justify-content-center bg-light">
                            <small class="text-muted italic"><i class="mdi mdi-information-outline"></i> Belum ada lampiran Memo</small>
                            </div>`;
            var status_lampiran = `d-none`;
            var color_lampiran = `btn-primary w-100`;
            var icon_lampiran = `mdi-cloud-upload-outline me-1`;
            var text_lampiran = `Unggah Lampiran`;
            var lampiran = '';
            dokumen_lampiran = `<div class="col-md-6 col-lg-6">
                                <div class="document-card border rounded p-3 h-100 d-flex flex-column shadow-none">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="avatar-initial bg-label-info rounded me-3">
                                            <i class="mdi mdi-id-card mdi-24px"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                            <h6 class="mb-0 fw-bold text-dark">LAMPIRAN MEMO</h6>
                                            <small class="text-muted">Upload Scan Lampiran Memo( Optional)</small>
                                            </div>
                                            <span class="badge badge-dot bg-dot-success" data-bs-toggle="tooltip" title="Berkas Tersedia"></span>
                                            </div>
                                            </div>
                                            </div>
                                            
                                            <div class="mt-auto">
                                            <div id="lampiran_wrapper">
                                            ${lampiran_complete}
                                            {{-- MODE 2: PREVIEW FILE BARU (Hidden by default) --}}
                                            <div id="lampiran_preview_row" class="p-2 border border-primary rounded bg-label-primary mb-2 d-flex align-items-center justify-content-between d-none">
                                            <div class="d-flex align-items-center overflow-hidden">
                                            <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                            <small id="new_file_name_lampiran" class="text-truncate text-primary fw-bold" style="max-width: 150px;"></small>
                                                    </div>
                                                    <small class="text-xs text-primary fw-medium anim-pulse" id="siap_simpan_lampiran">Siap Simpan</small>
                                                </div>
                                                </div>

                                        <div class="d-flex gap-2">
                                            {{-- Tombol Lihat (Hanya muncul jika sudah ada file di server) --}}
                                            <a href="${base_url_lampiran}/${lampiran}"
                                            id="btn_lihat_lampiran"
                                            target="_blank"
                                            class="btn btn-primary btn-sm flex-grow-1 shadow-sm ${status_lampiran}">
                                            <i class="mdi mdi-eye-outline me-1"></i> Lihat Berkas
                                            </a>
                                            
                                            {{-- Tombol Upload / Ganti --}}
                                            <label for="lampiran" class="btn btn-sm ${color_lampiran} mb-0" id="label_lampiran">
                                            <i class="mdi ${icon_lampiran}"></i>
                                            <span>${text_lampiran}</span>
                                            <input type="file" hidden name="lampiran" id="lampiran" accept="application/pdf" value=""> 
                                            <input type="hidden" id="lampiran_old" name="lampiran_old" value="${ lampiran }">
                                                </label>
                                                
                                            {{-- Tombol Cancel (Hanya muncul saat preview baru) --}}
                                            <button type="button" id="btn_cancel_upload_lampiran" class="btn btn-sm btn-outline-danger d-none">
                                                <i class="mdi mdi-close"></i>
                                                </button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
            $('#berkas_lampiran').html(dokumen_lampiran);
            $('#modal_tambah_memo').modal('show');
        });
        $(document).on('click', '.btn_edit', function() {
            $('.modal-title').html('Edit Memo Internal');
            $('.btn-save-memo').html('Simpan Perubahan');
            var id = $(this).data('id');
            var isi = $(this).data('isi');
            var holding = $(this).data('holding');
            var perihal = $(this).data('perihal');
            var penerima = $(this).data('penerima');
            var tgl_memo = $(this).data('tgl_memo');
            var penandatangan = $(this).data('penandatangan');
            var holiday = $(this).data('holiday');
            var mulai_libur = $(this).data('mulai_libur');
            var selesai_libur = $(this).data('selesai_libur');
            var lampiran = $(this).data('lampiran');
            var dokumen_lampiran = '';
            // console.log(holiday);
            if (holiday == true) {
                $('#isHoliday').prop('checked', true);
                $('.holiday-section').removeClass('d-none')
                $('#tanggal_mulai_libur').val(mulai_libur);
                $('#tanggal_selesai_libur').val(selesai_libur);

            } else {
                $('#tanggal_mulai_libur').val('');
                $('#tanggal_selesai_libur').val('');
                $('#isHoliday').prop('checked', false);
                $('.holiday-section').addClass('d-none')
            }
            $('#id_memo').val(id);
            // set ke quill
            quill.root.innerHTML = isi;
            $('#holding').val(holding).trigger('change');
            $('#perihal').val(perihal);
            $('#penerima').val(penerima);
            $('#tanggal').val(tgl_memo);
            $('#penandatangan').val(penandatangan);
            var base_url_lampiran = "{{ asset('storage/lampiran') }}";
            $('#berkas_lampiran').empty();
            if (lampiran != null) {
                var lampiran_complete = `<div id="lampiran_info_row" class="p-2 border border-success rounded bg-label-success mb-2 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                            <small class="text-truncate text-success fw-bold" style="max-width: 150px;">${ base_url_lampiran + lampiran }</small>
                                            </div>
                                            <small class="text-xs text-success fw-medium">Tersimpan</small>
                                            </div>`;
                var status_lampiran = ``;
                var color_lampiran = `btn-outline-secondary`;
                var icon_lampiran = `mdi-refresh`;
                var text_lampiran = ``;
            } else {
                var lampiran_complete = `<div id="lampiran_empty_row" class="p-2 border border-dashed rounded mb-2 d-flex align-items-center justify-content-center bg-light">
                            <small class="text-muted italic"><i class="mdi mdi-information-outline"></i> Belum ada lampiran Memo</small>
                            </div>`;
                var status_lampiran = `d-none`;
                var color_lampiran = `btn-primary w-100`;
                var icon_lampiran = `mdi-cloud-upload-outline me-1`;
                var text_lampiran = `Unggah Lampiran`;
            }
            dokumen_lampiran = `<div class="col-md-6 col-lg-6">
                                <div class="document-card border rounded p-3 h-100 d-flex flex-column shadow-none">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="avatar-initial bg-label-info rounded me-3">
                                            <i class="mdi mdi-id-card mdi-24px"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                            <h6 class="mb-0 fw-bold text-dark">LAMPIRAN MEMO</h6>
                                            <small class="text-muted">Upload Scan Lampiran Memo( Optional)</small>
                                            </div>
                                            <span class="badge badge-dot bg-dot-success" data-bs-toggle="tooltip" title="Berkas Tersedia"></span>
                                            </div>
                                            </div>
                                            </div>
                                            
                                            <div class="mt-auto">
                                            <div id="lampiran_wrapper">
                                            ${lampiran_complete}
                                            {{-- MODE 2: PREVIEW FILE BARU (Hidden by default) --}}
                                            <div id="lampiran_preview_row" class="p-2 border border-primary rounded bg-label-primary mb-2 d-flex align-items-center justify-content-between d-none">
                                            <div class="d-flex align-items-center overflow-hidden">
                                            <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                            <small id="new_file_name_lampiran" class="text-truncate text-primary fw-bold" style="max-width: 150px;"></small>
                                                    </div>
                                                    <small class="text-xs text-primary fw-medium anim-pulse" id="siap_simpan_lampiran">Siap Simpan</small>
                                                </div>
                                                </div>

                                        <div class="d-flex gap-2">
                                            {{-- Tombol Lihat (Hanya muncul jika sudah ada file di server) --}}
                                            <a href="${base_url_lampiran}/${lampiran}"
                                            id="btn_lihat_lampiran"
                                            target="_blank"
                                            class="btn btn-primary btn-sm flex-grow-1 shadow-sm ${status_lampiran}">
                                            <i class="mdi mdi-eye-outline me-1"></i> Lihat Berkas
                                            </a>
                                            
                                            {{-- Tombol Upload / Ganti --}}
                                            <label for="lampiran" class="btn btn-sm ${color_lampiran} mb-0" id="label_lampiran">
                                            <i class="mdi ${icon_lampiran}"></i>
                                            <span>${text_lampiran}</span>
                                            <input type="file" hidden name="lampiran" id="lampiran" accept="application/pdf" value=""> 
                                            <input type="hidden" id="lampiran_old" name="lampiran_old" value="${ lampiran }">
                                                </label>
                                                
                                            {{-- Tombol Cancel (Hanya muncul saat preview baru) --}}
                                            <button type="button" id="btn_cancel_upload_lampiran" class="btn btn-sm btn-outline-danger d-none">
                                                <i class="mdi mdi-close"></i>
                                                </button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
            $('#berkas_lampiran').html(dokumen_lampiran);
            $('#modal_tambah_memo').modal('show');
        });
        $(document).on('change', '#lampiran', function() {
            const file = this.files[0];
            // console.log(file.type);
            if (file) {
                // Validasi PDF
                if (file.type !== "application/pdf") {
                    Swal.fire('Oops!', 'Format file harus PDF', 'error');
                    $(this).val('');
                    return;
                }
                $('#new_file_name_lampiran').text('LAMPIRAN_MEMO');
                // Tampilkan Nama File & Ubah UI
                $('#lampiran_info_row, #lampiran_empty_row, #btn_lihat_lampiran').addClass('d-none');
                $('#lampiran_preview_row, #btn_cancel_upload_lampiran').removeClass('d-none').hide().fadeIn(
                    300);

                // Ubah gaya tombol
                const label = $('#label_lampiran');
                label.removeClass('btn-danger btn-primary w-100').addClass('btn-outline-secondary')
                    .find('span').text('');
                label.find('i').removeClass('mdi-cloud-upload-outline me-1').addClass('mdi-refresh');
            }
        });
        $(document).on('click', '#btn_cancel_upload_lampiran', function() {
            $("#file_lampiran").val(''); // Clear input
            $('#lampiran_preview_row, #btn_cancel_upload_lampiran').addClass('d-none');

            // Kembalikan ke kondisi semula
            if ($('#lampiran_info_row').length > 0) {
                $('#lampiran_info_row, #btn_lihat_lampiran').removeClass('d-none');
            } else {
                $('#lampiran_empty_row').removeClass('d-none');
                $('#label_lampiran').addClass('btn-primary w-100').find('span').text('Upload Lampiran');
                $('#label_lampiran').find('i').addClass('mdi-cloud-upload-outline me-1').removeClass(
                    'mdi-refresh');
            }
        });
        $('.btn-preview').click(function() {
            let isi = quill.root.innerHTML
            console.log(isi);
            Swal.fire({
                title: 'Preview Memo',
                html: isi,
                width: 700
            })

        })
        $(document).on('click', '.btn-save-memo', function() {
            var isi_memo = quill.root.innerHTML;
            var id_memo = $("#id_memo").val();
            var holding_array = $("#holding").val();
            var tanggal = $("#tanggal").val();
            var penandatangan = $("#penandatangan").val();
            var perihal = $("#perihal").val();
            var penerima = $("#penerima").val();
            var isHoliday = $("#isHoliday").is(':checked');
            var tanggal_mulai_libur = $("#tanggal_mulai_libur").val();
            var tanggal_selesai_libur = $("#tanggal_selesai_libur").val();

            // hapus tag HTML & cek text
            let text = quill.getText().trim();
            if (text === '') {
                isi_memo = '';
            }

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_memo', id_memo);
            formData.append('isi_memo', isi_memo);
            formData.append('tanggal', tanggal);
            formData.append('penandatangan', penandatangan);
            formData.append('perihal', perihal);
            formData.append('penerima', penerima);
            formData.append('isHoliday', isHoliday);
            formData.append('tanggal_mulai_libur', tanggal_mulai_libur);
            formData.append('tanggal_selesai_libur', tanggal_selesai_libur);
            holding_array.forEach(function(item) {
                formData.append('holding[]', item);
            });
            var lampiran = $("#lampiran")[0].files[0];
            if (lampiran) {
                formData.append('lampiran', lampiran);
            }
            $.ajax({
                url: "{{ url('memo_internal/store') }}" + "/" + holding,
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
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
                    // console.log(data);
                    Swal.close();
                    if (data.code == 200) {
                        $('#modal_tambah_memo').modal('hide');
                        $('#tableMemo').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 6000
                        })
                    } else if (data.code == 400) {
                        $('.alert-error').html('');
                        $('.form-control').removeClass('is-invalid');
                        var errors = data.errors;
                        var error_message = '';
                        $.each(errors, function(key, value) {
                            $('.alert-' + key).html('<div style="font-size: 11px;" class="alert alert-danger alert-dismissible fade show">' + value[0] + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                            $('[name="' + key + '"]').addClass('is-invalid');
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal ',
                            text: data.message
                        });
                    }
                },
                error: function(data) {
                    // console.log(data);
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memproses Data',
                        text: data.responseJSON.message
                    });
                }
            });
        })
        $(document).on('click', '.btn_delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data yang dihapus tidak dapat dikembalikan lagi.",
                icon: 'warning',
                iconColor: '#f87171',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                reverseButtons: true, // Mobile friendly: Cancel di kiri, Confirm di kanan
                customClass: {
                    popup: 'premium-swal',
                    title: 'premium-title',
                    htmlContainer: 'premium-text',
                    confirmButton: 'premium-confirm bg-danger', // Bisa timpa warna merah jika hapus
                    cancelButton: 'premium-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('memo_internal/destroy') }}" + "/" + id,
                        type: 'GET',
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
                            });
                        },
                        success: function(data) {
                            if (data.code == 200) {
                                $('#tableMemo').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    timer: 6000
                                })
                            } else if (data.code == 400) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                    timer: 6000
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message,
                                    timer: 6000
                                });
                            }
                        },
                        error: function(data) {
                            // console.log(data);
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.responseJSON.message
                            });
                        }
                    });
                } else {
                    return false;
                }
            })
        });
    });
</script>

@endsection