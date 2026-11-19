@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
        padding: 8px 11px;
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
        font-size: 13px;
    }

    .profile-tabs .nav-link span {
        font-size: 12px;
    }

    .kbw-signature {
        width: fit-content;
        height: 100%;
    }

    #sig canvas {
        margin-top: 5px;
        width: 100%;
        height: auto;
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

    .coworker-card {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card-history {
        background: linear-gradient(135deg, #cffbd1, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card-reject {
        background: linear-gradient(135deg, #fbcfcf, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
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

    .daterangepicker.ltr.show-calendar {
        top: 0px !important;
    }
</style>
@endsection
@section('content_top')
<div class="card">
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-check-to-slot"></i>
        </div>
        <h5 class="mt-3 mb-1">APPROVAL</h5>
        <small class="text-muted">Informasi Approval Pengajuan Cuti, Izin, Perjalanan Dinas</small>
    </div>

    <div class="alert_approval"></div>
    @if(session('approve_sukses'))
    <div class="alert alert-success alert-dismissible fade show alert_session_approval">
        {{ session('approve_sukses') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('approve_gagal'))
    <div class="alert alert-danger alert-dismissible fade show alert_session_approval">
        {{ session('approve_gagal') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <div class="card-body px-3 px-md-4">
        <ul class="nav profile-tabs" id="profileTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-cuti" role="tab" aria-controls="nav-cuti" aria-selected="true">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Cuti</span>
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-izin" role="tab" aria-controls="nav-izin" aria-selected="false">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Izin</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-perdin" role="tab" aria-controls="nav-perdin" aria-selected="false">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Perdin</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-histori" role="tab" aria-controls="nav-histori" aria-selected="false">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>History</span>
                </button>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="nav-cuti" role="tabpanel" aria-labelledby="nav-cuti-tab">
                <div class="content-absensi"></div>
            </div>
            <div class="tab-pane fade" id="nav-izin" role="tabpanel" aria-labelledby="nav-izin-tab">
                <div class="content-izin"></div>
            </div>
            <div class="tab-pane fade" id="nav-perdin" role="tabpanel" aria-labelledby="nav-perdin-tab">
                <div class="content-perdin"></div>
            </div>
            <div class="tab-pane fade" id="nav-histori" role="tabpanel" aria-labelledby="nav-history-tab">
                <div class="content-history">
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();" class="btn_klik btn btn-sm btn-danger light pwa-btn">
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
<script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    $(document).ready(function() {
        let menu_active = '{{ $menu_id }}';
        var session_sukses = "{{ session('approve_sukses') }}";
        var session_gagal = "{{ session('approve_gagal') }}";
        // SELECT 2 
        if (session_sukses) {
            setTimeout(function() {
                $('.alert_session_approval').fadeOut('slow');
            }, 6000);
        } else if (session_gagal) {
            setTimeout(function() {
                $('.alert_session_approval').fadeOut('slow');
            }, 6000);
        }

        // DATE RANGE PICKER
        let cutiPicker;
        var user_karyawan_id = '{{ $user_karyawan->id }}';
        var start = moment().subtract(-1, 'days');
        cutiPicker = $('#daterangepicker_cuti').daterangepicker({
            drops: 'down',
            minDate: start,
            startDate: start,
            endDate: start,
            autoApply: true,
        }, function(start, end, label) {
            // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });

        function initCutiPicker(single, minDate) {

            if ($('#daterangepicker_cuti').data('daterangepicker')) {
                $('#daterangepicker_cuti').data('daterangepicker').remove();
            }

            $('#daterangepicker_cuti').daterangepicker({
                drops: 'down',
                autoApply: false,
                singleDatePicker: single,
                startDate: minDate,
                endDate: minDate,
                minDate: minDate,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        }

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
        load_data_absensi();


        function load_data_absensi() {
            let data_approve = '';
            $.ajax({
                url: "{{ url('/absen/get_dataAbsensi') }}" + '/' + menu_active,
                type: "GET",
                beforeSend: function() {
                    showSkeleton('.content-absensi');
                },
                error: function(data) {
                    // console.log(data);
                    $('.alert_approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.responseJSON.message + '</div>').fadeIn(100);
                    setTimeout(function() {
                        $('.alert_approval').fadeOut('slow');
                    }, 6000);
                    data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                    hideSkeleton('.content-absensi', data_approve);
                },
                success: function(data) {
                    // console.log(data);
                    if (data.code == 200) {
                        if (data.data == '' || data.data == null) {
                            data_approve += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data cuti</p>
                                    </div>`;
                        } else {
                            data.data.forEach(element => {
                                if (element.nama_cuti == 1) {
                                    element.nama_cuti = 'Cuti Tahunan';
                                } else if (element.nama_cuti == 2) {
                                    element.nama_cuti = element.kategori_cuti.nama_cuti
                                }
                                data_approve += `<a class="btn_klik" href="{{ url('/approval/cuti/cutiDetail') }}/${element.id}">
                                <div class="coworker-card d-flex align-items-center mb-3">
                                     <div class="avatar-wrapper">
                                     <i class="fa-solid fa-circle-dot"></i>
                                        </div>
                                        {{-- INFO --}}
                                        <div class="flex-grow-1 ms-3 text-start">
                                            <h6 class="mb-1 fw-semibold">${element.nama_cuti}</h6>

                                            <div class="text-muted small">
                                                ${formatLabel(element.tanggal_mulai)}
                                                <span class="mx-1">-</span>
                                                ${formatLabel(element.tanggal_selesai)}
                                                <div class="d-flex align-items-center">
                                                <i class="fa fa-calendar me-1"></i><span style="font-size: 9pt;" class="ms-2 price-item">${formatLabel(element.tanggal_pengajuan)}</span>
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
                        $('.alert_approval').stop(true, true).hide().html('<div class="alert alert-danger light alert-lg alert-dismissible fade show">' + data.message + '</div>').fadeIn(100);
                        data_approve += `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                        setTimeout(function() {
                            $('.alert_approval').fadeOut('slow');
                        }, 6000);
                    }
                    hideSkeleton('.content-absensi', data_approve);
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

        function formatLabel2(date) {
            const d = new Date(date);

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            const hour = String(d.getHours()).padStart(2, '0');
            const minute = String(d.getMinutes()).padStart(2, '0');

            return `${day}-${month}-${year} ${hour}:${minute}`;
        }

    });
</script>
@endsection