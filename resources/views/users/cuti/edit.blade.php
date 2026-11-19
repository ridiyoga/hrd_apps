@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
<style>
    /* tinggi dropdown menyesuaikan layar */
    .select2-results__options {
        max-height: 45vh;
        /* cocok untuk mobile */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* SELECT2 WRAPPER */
    .form-input .select2-container {
        width: 100% !important;
    }

    /* SELECTION BOX */
    .form-input .select2-container--bootstrap-5 .select2-selection--single {
        height: 38px;
        display: flex;
        align-items: center;
    }

    /* Memaksa kalender muncul di paling depan */
    .daterangepicker {
        z-index: 9999 !important;
        /* Angka tinggi agar di atas elemen apapun */
        border: none !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border-radius: 12px !important;
        padding: 8px !important;
        margin-top: 5px !important;
    }

    /* Menghilangkan panah kecil di atas kalender agar lebih clean */
    .daterangepicker:before,
    .daterangepicker:after {
        display: none !important;
    }

    .daterangepicker .ranges li.active {
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        color: #fff;
    }

    .daterangepicker td.active,
    .daterangepicker td.active:hover {
        background: linear-gradient(135deg, #7c3aed, #a78bfa);
        border-color: transparent;
        color: #fff;
    }

    .daterangepicker td.in-range {
        background-color: #dcd9fa;
        border-color: transparent;
        color: #000;
        border-radius: 0;
    }

    .border-dashed {
        border: 2px dashed #d8b4fe !important;
    }

    /* Responsif Mobile */
    @media (max-width: 768px) {
        .daterangepicker {
            width: 90% !important;
            left: 5% !important;
            right: 5% !important;
            position: fixed !important;
            /* Membuatnya 'floating' di tengah layar mobile */
            top: 20% !important;
        }

        .daterangepicker .drp-calendar {
            max-width: 100% !important;
        }
    }


    /* TEXT AREA (INI KUNCI 🔥) */
    .form-input .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 42px !important;
        /* ruang icon */
        padding-right: 32px !important;
        /* ruang arrow */
        line-height: 1.2;
    }

    /* scroll lebih halus */
    .select2-results__option {
        padding: 10px 14px;
        font-size: 14px;
    }

    /* highlight option */
    .select2-results__option--highlighted {
        background: linear-gradient(135deg, #6f4fd8, #8b6df0);
        color: #fff;
    }

    .select2-dropdown {
        border-radius: 12px;
        box-shadow: 0 10px 30px #7e7e7e;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 42px;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 10px;
    }

    .form-input {
        position: relative;
    }

    /* ICON */
    .form-input .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 6;
        pointer-events: none;
    }

    .profile-tabs {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        border-bottom: none;
    }

    .profile-tabs .nav-link {
        border: none;
        border-radius: 14px;
        padding: 10px 16px;
        background: #f3e8ff;
        color: #6b21a8;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .profile-tabs .nav-link.active {
        background: linear-gradient(135deg, #a855f7, #7c3aed);
        color: #fff;
        box-shadow: 0 6px 14px rgba(168, 85, 247, 0.4);
    }

    .profile-tabs .nav-link i {
        font-size: 16px;
    }

    .kbw-signature {
        width: 100%;
        height: 160px;
        border: 1px solid #f3e8ff !important;
        background: #ffffff;
    }

    #sig canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .futuristic-header {
        padding: 28px 20px 18px;
    }

    .futuristic-header h5 {
        color: --primary();
        font-weight: 600;
    }

    .futuristic-header small {
        color: rgba(255, 255, 255, 0.65);
    }

    .futuristic-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
    }

    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg,
                #ede9fe 25%,
                #f5f3ff 37%,
                #ede9fe 63%);
        background-size: 400% 100%;
        animation: pulse 1.4s ease-in-out infinite;
        border-radius: 6px;
    }

    .skeleton-number {
        display: inline-block;
        /* ⬅️ WAJIB */
        width: 2ch;
        height: 30px;
        /* ⬅️ samakan dengan h3 */
        vertical-align: middle;
        /* ⬅️ penting */
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: .5;
        }

        50% {
            opacity: 1;
        }
    }

    .form-control {
        border-radius: 12px;
        padding-left: 42px;
        height: 44px;
        border: 1px solid #e5e5e5;
    }

    .form-input {
        position: relative;
        margin-bottom: 14px;
    }

    .form-input .input-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        /* transform: translateY(-50%); */
        color: #6f42c1;
        font-size: 14px;
    }
</style>
@endsection
@section('content_top')
<div class="card">
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <h5 class="mt-3 mb-1">CUTI</h5>
        <small class="text-muted">Edit Pengajuan Cuti</small>
    </div>
    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3">
        <i class="fa-solid fa-info-circle fs-5"></i>
        <div class="alert-content">
            <strong>Informasi</strong><br>
            Pengajuan Cuti Dapat dihapus sebelum di-Approve Atasan
        </div>
    </div>
    <div id="alert_edit_cuti">
    </div>
    <div class="card-body px-3 px-md-4">
        <form class="my-2" method="post" id="form_edit_cuti" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $data->karyawan_id }}">
            <input type="text" name="id_cuti" id="id_cuti" value="{{ $data->id }}" hidden>
            <div id="list_karyawan" class="d-flex flex-column gap-3"></div>
            <!-- Form input -->
            <div class="col-12">
                <div class="p-3 bg-white rounded shadow-sm">
                    <!-- Item: Pengaju -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                            <i class="fa-solid fa-user me-2 text-primary"></i> Pengaju Cuti
                        </div>
                        <div class="fs-6 fw-semibold text-dark ps-4">
                            {{ $data->nama_karyawan }}
                        </div>
                    </div>

                    <!-- Item: Jenis Cuti -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                            <i class="fa-solid fa-tags me-2 text-primary"></i> Nama Cuti
                        </div>
                        <div class="fs-6 fw-semibold text-dark ps-4">
                            {{ $data->nama_cuti }}
                        </div>
                    </div>

                    <!-- Item: Karyawan Pengganti -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                            <i class="fa-solid fa-user-group me-2 text-primary"></i> Karyawan Pengganti
                        </div>
                        <div class="fs-6 fw-semibold text-dark ps-4">
                            {{ $data->nama_karyawan_pengganti }}
                        </div>
                    </div>

                    <!-- Item: Tanggal -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                            <i class="fa-solid fa-calendar-days me-2 text-primary"></i> Tanggal Cuti
                        </div>
                        <div class="fs-6 fw-semibold text-dark ps-4">
                            @if ($data->kategori_cuti == null)
                            @foreach ($data->CutiDetail as $detail)
                            <br>{{ $detail->tgl_cuti }},
                            @endforeach
                            @else
                            {{ $data->tanggal_mulai }} - {{ $data->tanggal_selesai }}
                            @endif
                        </div>
                    </div>

                    <!-- Item: Keterangan -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center mb-1 text-muted small fw-bold text-uppercase">
                            <i class="fa-solid fa-note-sticky me-2 text-primary"></i> Keterangan
                        </div>
                        <div class="fs-6 text-secondary ps-4">
                            {{ $data->keterangan_cuti }}
                        </div>
                    </div>
                </div>
                <div class="alert_keterangan_cuti"></div>
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">2</span>
                        <h6 class="mb-0 fw-bold">Otorisasi & Pengesahan</h6>
                    </div>

                    <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                        <div class="card-header bg-light border-0 py-2 text-center">
                            <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                id="user_pengajuan">Diajukan Oleh: {{ $user_karyawan->name }}</small>
                        </div>
                        <div class="card-body p-2">
                            <div class="alert_signature"></div>
                            <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                <canvas id="signature-pad"
                                    style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                    hidden></canvas>
                                <img src="{{ asset('storage/signatures/' . $data->ttd_user) }}" alt="">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <small class="text-muted italic" style="font-size: 10px;">Tanda tangan</small>
                            </div>
                            <textarea id="signature" name="signature" style="display: none"></textarea>
                        </div>
                    </div>

                    <div class="row g-2 d-flex align-items-stretch">
                        <div class="col-6">
                            <div
                                class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                </div>
                                <input type="hidden" name="atasan_1" id="atasan_1"
                                    value="{{ $data->id_user_atasan }}">
                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                    id="atasan1">{{ $atasan_1 ? $atasan_1->name : '-' }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                <small class="d-block text-muted" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                </div>
                                <input type="hidden" name="atasan_2" id="atasan_2"
                                    value="{{ $data->id_user_atasan2 }}">
                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                    id="atasan2">{{ $atasan_2 ? $atasan_2->name : '-' }}</small>
                            </div>
                        </div>
                        <!-- <div class="col-4">
                                <div
                                    class="p-2 border rounded-3 text-center bg-light opacity-75 h-100 d-flex flex-column justify-content-between">
                                    <small class="d-block text-muted" style="font-size: 8px;">HRD:</small>
                                    <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-clock-rotate-left text-muted"></i>
                                    </div>
                                    <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                        id="hrd_selected">HRD</small>
                                </div>
                            </div> -->
                    </div>
                </div>

                <div class="alert_error_cuti col-12"></div>
                <div class="mt-3 text-center">
                    <button type="button" id="btn_konfirmasi_delete_cuti" data-id_hapus="{{ $data->id }}"
                        class="btn light btn-danger btn-sm">
                        <i class="fa fa-trash me-1"></i>Hapus
                    </button>
                    <!-- <div class="dz-spinner d-flex align-items-center flex-wrap"> -->
                    {{-- <button type="button" id="btn_simpan_cuti" class="btn light btn-success btn-sm me-2">
                            <i class="fa fa-save me-1"></i>Simpan&nbsp;Perubahan
                        </button> --}}
                    <!-- </div> -->
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom" style="height: 60%;" data-bs-keyboard="false" data-bs-backdrop="true"
    id="off-canvas-ttd">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title">FORM TTD</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas">X</button>
    </div>
    <div class="offcanvas-body p-2  overflow-auto" style="overflow-y: auto !important;">
        <div class="lihat_ttd text-center">
            <img src="" id="ttd_user" alt="Lihat TTD" class="img-fluid" style="width: 100%">
            <h6 id="nama_users"></h6>
            <span id="tgl_users"></span>
        </div>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_hapus" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Hapus Pengajuan Cuti Ini ?</p>
        <form method="POST" id="form_hapus_cuti" action="{{ url('cuti/proses_hapus_cuti') }}">
            @csrf
            <input type="hidden" id="id_cuti_hapus" name="id_cuti_hapus" value="">
            <button type="button" id="btn_delete_cuti" class="btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-trash me-1"></i>
                &nbsp;Hapus
            </button>
            <button type="button" class="btn btn-sm light btn-primary me-1" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal</button>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();"
                class="btn_klik btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                &nbsp;Logout
            </a>
            <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal</a>
        </form>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    $(document).ready(function() {
        let id_cuti = '{{ $id_cuti }}';
        var nama_cuti = '{{ $data->nama_cuti }}';
        let tanggal_mulai = '{{ $data->tanggal_mulai }}';
        let tanggal_selesai = '{{ $data->tanggal_selesai }}';
        let kategori_cuti_id = '{{ $data->kategori_cuti_id }}';
        let karyawan_pengganti = '{{ $data->karyawan_pengganti_id }}';
        let date_now = moment().format('YYYY-MM-DD');
        if (nama_cuti == 2) {
            // DATE RANGE PICKER
            initCutiPicker(true, date_now);
            $.ajax({
                type: 'GET',
                url: "{{ url('cuti/get_kategori_cuti') }}" + '/' + id_cuti,
                cache: false,
                success: function(msg) {
                    let option = '<option value="">-- Pilihan Cuti --</option>';
                    msg.kategori_cuti.forEach((element, index) => {
                        option +=
                            `<option value="${element.id}" ${element.id == kategori_cuti_id ? 'selected' : ''}>${element.nama_cuti} (${element.jumlah_hari} Hari)</option>`;
                    })
                    $('.kategori_cuti').show();
                    $('#kategori_cuti').html(option).trigger('change');
                },
                error: function(data) {
                    $('.alert_tanggal_cuti').stop(true, true).html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_tanggal_cuti').fadeOut('slow');
                    }, 6000);
                },
            });
        } else {
            initCutiPicker(false, tanggal_mulai);
            $('.kategori_cuti').hide();
        }
        // SELECT 2 
        $('#karyawan_pengganti').select2({
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Karyawan Pengganti",
            allowClear: true
        });
        $('#cuti').select2({
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Kategori Cuti",
            allowClear: true
        });
        $('#kategori_cuti').select2({
            width: '100%',
            theme: 'bootstrap-5',
            placeholder: "Pilih Pilihan Cuti",
            allowClear: true
        });


        function initCutiPicker(single = false, minDate = null) {

            let el = $('#daterangepicker_cuti');

            if (el.data('daterangepicker')) {
                el.data('daterangepicker').remove();
            }

            el.daterangepicker({
                parentEl: document.body,
                drops: 'auto',
                autoApply: false,
                singleDatePicker: single,
                minDate: minDate,
                startDate: tanggal_mulai,
                endDate: tanggal_selesai ?? tanggal_mulai,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        }

        $(document).on('change', '#cuti', function() {
            let id_cuti = $(this).val();
            let minDate;
            $('#kategori_cuti').html('').trigger('change');
            $('#keterangan_tanggal').empty();
            let keterangan = '';
            if (id_cuti == '1') {
                initCutiPicker(false, tanggal_mulai);
                $('#daterangepicker_cuti').prop('disabled', false);
                $('.kategori_cuti').hide();
                keterangan +=
                    '<small>*Cuti Tahunan dapat diajukan minimal 14 hari dari tanggal pengajuan</small>';
            } else if (id_cuti == '2') {
                initCutiPicker(true, tanggal_mulai);
                $.ajax({
                    type: 'GET',
                    url: "{{ url('cuti/get_kategori_cuti') }}",
                    cache: false,
                    success: function(msg) {
                        let option = '<option value="">-- Pilihan Cuti --</option>';
                        msg.kategori_cuti.forEach((element, index) => {
                            option +=
                                `<option value="${element.id}">${element.nama_cuti} (${element.jumlah_hari} Hari)</option>`;
                        })
                        $('.kategori_cuti').show();
                        $('#kategori_cuti').html(option).trigger('change');
                    },
                    error: function(data) {
                        $('.alert_tanggal_cuti').stop(true, true).html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseJSON.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_tanggal_cuti').fadeOut('slow');
                        }, 6000);
                    },
                });
                keterangan += '<small>*Pilih Tanggal Mulai Cuti</small>';
            } else {
                $('.kategori_cuti').hide();
            }
            $('#keterangan_tanggal').html(keterangan);

        })
        $(document).on('click', '#btn_konfirmasi_delete_cuti', function(e) {
            var id_hapus = $(this).data('id_hapus');
            $('#id_cuti_hapus').val(id_hapus);
            const offcanvasEl = document.getElementById('offcanvas_hapus');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        });
        $(document).on('click', '#btn_delete_cuti', function(e) {
            $('#form_hapus_cuti').submit();
        });
        $(document).on('click', '#btn_lihat_ttd', function(e) {
            var ttd_user = '{{ $data->ttd_user }}';
            var url = "{{ asset('storage/signatures/') }}" + "/" + ttd_user;
            $('#ttd_user').attr('src', url);
            $('#nama_users').text('{{ $data->nama_karyawan }}');
            $('#tgl_users').text('({{ $data->tanggal_pengajuan }})');
            const offcanvasEl = document.getElementById('off-canvas-ttd');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        });

        $('#clear').on('click', () => signaturePad.clear());
        $(document).on('click', '#btn_simpan_cuti', function(e) {
            let btn = $(this);
            let originalText = btn.html();
            let signature_value = '';

            $.ajax({
                url: "{{ url('/cuti/proses_edit_cuti') }}" + '/' + menu_active,
                type: "POST",
                data: $('#form_edit_cuti').serialize(),
                beforeSend: function() {
                    btn.prop('disabled', true);
                    btn.html(
                        `<span class="spinner-border spinner-border-sm me-2"></span>Loading... `
                    );
                },
                error: function(data) {
                    btn.prop('disabled', false);
                    btn.html(originalText);
                    $('.alert_error_cuti').stop(true, true).hide().html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_error_cuti').fadeOut('slow');
                    }, 6000);
                    $('#alert_edit_cuti').stop(true, true).hide().html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('#alert_edit_cuti').fadeOut('slow');
                    }, 6000);
                },
                success: function(data) {
                    btn.prop('disabled', false);
                    btn.html(originalText);
                    $('#alert_edit_cuti').empty();
                    // console.log(data);
                    if (data.code == 200) {
                        $('#alert_edit_cuti').stop(true, true).hide().html(
                            '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                            data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('#alert_edit_cuti').fadeOut('slow');
                        }, 6000);
                    } else if (data.code == 422) {
                        $.each(data.errors, function(key, value) {
                            let input = $('[name="' + key + '"]');
                            // console.log(value[0]);
                            $('.alert_' + key).html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                value[0] + '</div>').fadeIn(100);
                            setTimeout(function() {
                                $('.alert_' + key).fadeOut('slow');
                            }, 6000);
                        })
                    } else if (data.code == 500) {
                        $('.alert_error_cuti').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_error_cuti').fadeOut('slow');
                        }, 6000);
                    }
                }
            })
        });
        $(document).on('click', '#btn_klik', function(e) {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                showCancelButton: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    // Swal.showLoading()
                },
            });
        });
        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                showCancelButton: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    // Swal.showLoading()
                },
            });
        };
        var today = moment().format('YYYY-MM-DD');
        var month = moment().format('MM');
        var day = moment().format('D');
        var year = moment().format('YYYY');

        // console.log(month);
        $('.month').val(month);

        function showSkeleton(selector) {
            const el = $(selector);
            el.html(
                `<span class="skeleton skeleton-number" style="width:100%"></span>`
            );
        }

        function hideSkeleton(selector, value) {
            $(selector)
                .html(value);
        }


        load_data_karyawan_pengganti();

        function load_data_karyawan_pengganti() {
            let user_karyawan_id = '{{ $user_karyawan->id }}';
            $.ajax({
                url: "{{ url('/cuti/get_karyawan_pengganti') }}" + '/' + user_karyawan_id,
                type: "GET",
                // beforeSend: function() {
                //     showSkeleton('.karyawan_pengganti');
                // },
                error: function(data) {
                    $('.alert-pengganti').stop(true, true).hide().html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        data.responseText + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert-pengganti').fadeOut('slow');
                    }, 6000);
                },
                success: function(data) {
                    let option = '<option value="">Pilih Karyawan Pengganti...</option>';
                    let select_karyawan =
                        ' <span class="input-icon"><i class="fa-solid fa-user"></i></span>';
                    if (data.code == 200) {
                        if (data.karyawan_pengganti.length == 0) {
                            $('.alert-pengganti').stop(true, true).hide().html(
                                '<div class="alert alert-warning light alert-lg alert-dismissible fade show">Tidak ada karyawan pengganti tersedia</div>'
                            ).fadeIn(100);
                            setTimeout(function() {
                                $('.alert-pengganti').fadeOut('slow');
                            }, 60000);
                        } else {
                            data.karyawan_pengganti.forEach((element, index) => {
                                option +=
                                    `<option value="${element.karyawan.id}" ${element.karyawan.id == karyawan_pengganti ? 'selected' : ''}>${element.karyawan.name}</option>`;
                            });
                        }
                    } else {
                        $('.alert-pengganti').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert-pengganti').fadeOut('slow');
                        }, 60000);
                    }
                    // hideSkeleton('.karyawan_pengganti', option);
                    $('#karyawan_pengganti').html(option).trigger('change');
                }
            })
        }

        function formatLabel(date) {
            const d = new Date(date);

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();

            return `${day}-${month}-${year}`;
        }

        function load_data_history(filter_month = '') {
            $('.content-history').empty();
            $('.alert_history_cuti').empty();
            $('.alert_add_cuti').empty();
            $.ajax({
                url: "{{ url('/cuti/history/get_history_cuti') }}",
                data: {
                    filter_month: filter_month,
                },
                type: "GET",
                beforeSend: function() {
                    showSkeleton('.content-history');
                },
                error: function(data) {
                    $('.alert_history_cuti').stop(true, true).hide().html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                        '<strong>Info!</strong>&nbsp;' + data.responseJSON.message +
                        '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                    ).fadeIn();
                    setTimeout(function() {
                        $('.alert_history_cuti').fadeOut('slow');
                    }, 6000); // 3 detik

                },
                success: function(data) {
                    // console.log(data);
                    $('.content-history').empty();
                    let history = '';
                    if (data.code == 200) {
                        if (data.history == '' || data.history == null) {
                            history += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-clock-rotate-left fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data History cuti</p>
                                    </div>`;
                        } else {
                            data.history.each(data, function(element) {
                                history += `<a class="btn_klik" href="{{ url('/cuti/history/history_detail') }}/${element.nama_cuti}"><div class="coworker-card d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.tanggal}</h6>

                                            <div class="text-muted small">
                                                ${element.tanggal}
                                                <span class="mx-1">•</span>
                                                (${element.tanggal})
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${element.tanggal}</span>
                                               </div>
                                            </div>
                                        </div>
                                        {{-- ICON --}}
                                        <div class="icon-box">
                                         <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                    </div></a>`;
                            });
                        }
                    } else {
                        $('.alert_history_cuti').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_history_cuti').fadeOut('slow');
                        }, 6000);
                        history +=
                            `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                    }
                    hideSkeleton('.content-history', history);
                }
            });
        }
        $('#month').on('change', function() {
            filter_month = $(this).val();
            // console.log(filter_month);
            $('.content-history').empty();
            load_data_history(filter_month);


        })
    });
</script>
@endsection