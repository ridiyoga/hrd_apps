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
        .alert_approve_cuti {
            max-width: 100%;
            overflow-x: hidden;
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
            padding: 20px 20px 5px;
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

        .approve-card {
            margin-top: 15px;
            transition: 0.3s;
        }

        .approve-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(124, 58, 237, 0.15);
        }

        /* HEADER */
        .approve-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .approve-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 6px 15px rgba(124, 58, 237, 0.4);
            overflow: hidden;
            /* Tambahkan ini agar gambar tidak keluar jalur */
        }

        .approve-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Menjaga foto tetap proporsional (tidak lonjong) */
            object-position: center;
            /* Memastikan wajah tetap di tengah */
        }

        .badge-status {
            margin-left: auto;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-status.pending {
            background: #fff7ed;
            color: #f97316;
        }

        .badge-status.approved {
            background: #ecfdf5;
            color: #16a34a;
        }

        .badge-status.rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        /* INFO BOX */
        .info-box small {
            font-size: 12px;
            color: #888;
        }

        .info-box p {
            margin: 2px 0 0;
            font-weight: 600;
        }

        /* FOOTER */
        .approve-footer {
            margin-top: 18px;
        }

        .approval-note {
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
            height: 90px;
        }

        /* BUTTONS */
        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-approve {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(34, 197, 94, 0.4);
        }

        .btn-reject {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #981515, #ef4444);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        h6 {
            font-size: 0.9rem !important;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(239, 68, 68, 0.4);
            color: #fff;
        }


        .daterangepicker.ltr.show-calendar {
            top: 0px !important;
        }

        .watermark-logo {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Menaruh titik tengah logo di titik tengah card */
            opacity: 0.08;
            /* Mengatur transparansi (0.0 - 1.0) */
            z-index: -1;
            /* Menaruh logo di belakang teks/konten */
            pointer-events: none;
            /* Penting! Agar user tetap bisa klik tombol/input di atasnya */
            user-select: none;
            /* Agar gambar tidak bisa di-drag/pilih */
            width: 50%;
            /* Atur ukuran logo sesuai keinginan */
        }

        .watermark-logo img {
            width: 100%;
            height: auto;
            /* filter: grayscale(100%); */
            /* Opsional: membuat logo jadi hitam putih agar lebih samar */
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon title_icon">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <h5 class="mt-3 mb-1">Detail Histori Cuti</h5>
            <small class="text-muted">Informasi Detail Histori Pengajuan Cuti</small>
        </div>
        <div class="alert_approve_cuti"></div>

        <div class="content-approve-cuti px-3 px-md-4">
        </div>
    </div>
@endsection
@section('content')
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
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
                $('#btn_unduh_pdf').prop('disabled', false);
                $('#btn_unduh_pdf').html('Unduh PDF');
            }
        });
        $(document).ready(function() {
            let id_cuti = '{{ $id }}';
            var session_sukses = "{{ session('success_approve_quick') }}";
            var session_gagal = "{{ session('hapus_cuti_gagal') }}";
            // SELECT 2 
            if (session_sukses) {
                setTimeout(function() {
                    $('.alert_pengajuan_quick').fadeOut('slow');
                }, 6000);
            } else if (session_gagal) {
                setTimeout(function() {
                    $('.alert_pengajuan_quick').fadeOut('slow');
                }, 6000);
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
            $('#clear').on('click', () => signaturePad.clear());

            $(document).on('click', '#btn_unduh_pdf', function(e) {

                let btn = $(this);
                let originalText = btn.html();

                btn.prop('disabled', true);
                btn.html(`<span class="spinner-border spinner-border-sm me-2"></span> Loading...`);

                location.href = "{{ url('cuti/history/cetak_form_cuti') }}/" + id_cuti;

                setTimeout(() => {
                    Swal.close();
                    btn.prop('disabled', false);
                    btn.html(originalText); // ✅ kembalikan teks awal
                }, 2500);

            });

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
            load_data_cuti();

            function load_data_cuti() {
                // console.log('load_data_cuti');
                let cuti = '';
                $.ajax({
                    url: "{{ url('approval/cuti/get_cutiDetail') }}" + '/' + id_cuti,
                    type: "GET",
                    beforeSend: function() {
                        showSkeleton('.content-approve-cuti');
                    },
                    error: function(data) {
                        // console.log(data);
                        $('.alert_approve_cuti').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            data.responseJSON.message + '</div>').fadeIn(100);
                        setTimeout(function() {
                            $('.alert_approve_cuti').fadeOut('slow');
                        }, 6000);
                        cuti +=
                            `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.responseJSON.message}</small></div>`;
                        hideSkeleton('.content-approve-cuti', cuti);
                    },
                    success: function(data) {
                        // console.log(data);
                        if (data.code == 200) {
                            dataCuti = data;
                            const signatureBaseUrl = "{{ asset('storage/signatures') }}";
                            // console.log(data, signatureBaseUrl);
                            if (data.data == '' || data.data == null) {
                                cuti += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data</p>
                                    </div>`;
                                $('#title').html('PINTASAN');
                                $('#sub_title').html('Pintasan Approval Cuti, Izin, Perjalanan Dinas');
                                $('.title_icon').html('<i class="fa-solid fa-file-signature"></i>');
                            } else {
                                var profile = '';
                                var user_from = '';
                                var atasan1 = '';
                                var atasan2 = '';
                                var type = '';
                                var status = '';
                                var total_hari = '';
                                var tanggal_mulai_cuti = '';
                                var tanggal_selesai_cuti = '';
                                var tanggal_kembali_bekerja = '';
                                var keterangan_cuti = '';
                                var tanggal_pengajuan = '';
                                var tanggal_approved_atasan1 = '';
                                var tanggal_approved_atasan2 = '';
                                var approved = '';
                                var ttd = '';
                                var footer = '';
                                var kuota_tahunan = '';
                                var kuota = '';
                                var sisa = '';
                                var terpakai = '';
                                var watermark = '';
                                if (data.data.user == '' || data.data.user == null) {
                                    $('.alert_approve_cuti').stop(true, true).hide().html(
                                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">Karyawan tidak ditemukan</div>'
                                    ).fadeIn(100);
                                    cuti +=
                                        `<div class="text-center mt-3 mb-3"><small class="text-muted">Karyawan tidak ditemukan</small></div>`;
                                    setTimeout(function() {
                                        $('.alert_approve_cuti').fadeOut('slow');
                                    }, 6000);
                                } else {
                                    user_from += data.data.user.name;
                                    if (data.data.user.foto_karyawan == '' || data.data.user
                                        .foto_karyawan == null) {
                                        profile +=
                                            `<img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="rounded-circle" alt="User">`;
                                    } else {
                                        profile +=
                                            `<img src="{{ asset('storage/foto_karyawan/' . $user_karyawan->foto_karyawan) }}" class="rounded-circle" alt="User">`;
                                    }
                                }
                                tanggal_pengajuan += data.data.tanggal_pengajuan ? formatLabel(data.data
                                    .tanggal_pengajuan) : '-';
                                tanggal_approved_atasan1 += data.data.waktu_approve ? formatLabel(data
                                    .data.waktu_approve) : '-';
                                tanggal_approved_atasan2 += data.data.waktu_approve2 ? formatLabel(data
                                    .data.waktu_approve2) : '-';
                                if (data.data.atasan1 == '' || data.data.atasan1 == null) {
                                    atasan1 += '-';
                                } else {
                                    atasan1 += data.data.atasan1.name;
                                }
                                if (data.data.atasan2 == '' || data.data.atasan2 == null) {
                                    atasan2 += '-';
                                } else {
                                    atasan2 += data.data.atasan2.name;
                                }
                                var ttdPengajuan = data.data.ttd_user ?
                                    `<img src='${signatureBaseUrl}/${data.data.ttd_user}' class='img-fluid  rounded' style='object-fit:contain;'>` :
                                    `<i class="fa-solid fa-clock-rotate-left"></i>`;
                                var ttdAtasan = data.data.ttd_atasan ?
                                    `<img src='${signatureBaseUrl}/${data.data.ttd_atasan}' class='img-fluid  rounded' style='object-fit:contain;'>` :
                                    `<i class="fa-solid fa-clock-rotate-left"></i>`;
                                var ttdAtasan2 = data.data.ttd_atasan2 ?
                                    `<img src='${signatureBaseUrl}/${data.data.ttd_atasan2}' class='img-fluid  rounded' style='object-fit:contain;'>` :
                                    `<i class="fa-solid fa-clock-rotate-left"></i>`;

                                if (data.data.status_cuti == 0) {
                                    status += '<span class="badge-status pending">Pending</span>';
                                } else if (data.data.status_cuti == 1) {
                                    if (data.data.id_user_atasan == data.user.id && data.data
                                        .ttd_atasan != null) {
                                        status += '<span class="badge-status approved">Approved</span>';
                                        approved += 'd-none';
                                        ttd += `
                                    <div class="text-center mb-3">
                                        <span class="mb-0 fw-bold">Otorisasi & Pengesahan</span>
                                    </div>
                                        <div class="row g-2 d-flex align-items-stretch">
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdPengajuan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan1">${user_from}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdAtasan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan2">${atasan1}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan1}</small>
                                                </div>
                                                </div>
                                                <div class="col-4">
                                                <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                ${ttdAtasan2}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="hrd_selected">${atasan2}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan2}</small>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                        footer += `<div class="approve-footer">
                                        <div class="action-buttons">
                                            <button class="btn btn-reject" id="btn_unduh_pdf" target="_blank">
                                                <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
                                            </button>
                                        </div>
                                    </div>`;
                                    } else {
                                        status +=
                                            '<span class="badge-status pending">Waiting Approval</span>';
                                        ttd += `
                                    <div class="text-center mb-3">
                                        <span class="mb-0 fw-bold">Otorisasi & Pengesahan</span>
                                    </div>
                                        <div class="row g-2 d-flex align-items-stretch">
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdPengajuan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan1">${user_from}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdAtasan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan2">${atasan1}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan1}</small>
                                                </div>
                                                </div>
                                                <div class="col-4">
                                                <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                ${ttdAtasan2}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="hrd_selected">${atasan2}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan2}</small>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                    }
                                } else if (data.data.status_cuti == 2) {
                                    if (data.data.ttd_atasan2 != null) {
                                        status += '<span class="badge-status approved">Approved</span>';
                                        approved += 'd-none';
                                        ttd += `
                                    <div class="text-center mb-3">
                                        <span class="mb-0 fw-bold">Otorisasi & Pengesahan</span>
                                    </div>
                                        <div class="row g-2 d-flex align-items-stretch">
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdPengajuan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan1">${user_from}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdAtasan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan2">${atasan1}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan1}</small>
                                                </div>
                                                </div>
                                                <div class="col-4">
                                                <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                ${ttdAtasan2}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="hrd_selected">${atasan2}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan2}</small>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                        footer += `<div class="approve-footer">
                                        <div class="action-buttons">
                                            <button class="btn btn-reject" id="btn_unduh_pdf" target="_blank">
                                                <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
                                            </button>
                                        </div>
                                    </div>`;
                                        watermark +=
                                            `<img src="{{ asset('assets/assets_users/images/approved.png') }}" alt="watermark">`;
                                    } else {
                                        status += '<span class="badge-status pending">Pending</span>';
                                    }
                                    $('.action-buttons').addClass('d-none');
                                } else if (data.data.status_cuti == 5) {
                                    status += '<span class="badge-status rejected">Reject</span>';
                                    ttd += `
                                    <div class="text-center mb-3">
                                        <span class="mb-0 fw-bold">Otorisasi & Pengesahan</span>
                                    </div>
                                        <div class="row g-2 d-flex align-items-stretch">
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdPengajuan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan1">${user_from}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan}</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-1:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                    ${ttdAtasan}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan2">${atasan1}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan1}</small>
                                                </div>
                                                </div>
                                                <div class="col-4">
                                                <div class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                                                <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH-2:</small>
                                                <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                                ${ttdAtasan2}
                                                </div>
                                                <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="hrd_selected">${atasan2}</small>
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_approved_atasan2}</small>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                    watermark +=
                                        `<img src="{{ asset('assets/assets_users/images/rejected.png') }}" alt="watermark">`;
                                }


                                console.log(data.data.kuota);
                                tanggal_mulai_cuti += `<p>${formatLabel(data.data.tanggal_mulai)}</p>`;
                                tanggal_selesai_cuti +=
                                    `<p>${formatLabel(data.data.tanggal_selesai)}</p>`;
                                tanggal_kembali_bekerja +=
                                    `<p>${tanggal_kembali_kerja(data.data.tanggal_selesai)}</p>`;
                                total_hari += (data.data.jumlah_hari);
                                kuota += `<p>${data.data.kuota} Hari</p>`;
                                terpakai += `<p>${data.data.terpakai} Hari</p>`;
                                sisa += `<p>${data.data.sisa} Hari</p>`;
                                keterangan_cuti +=
                                    `<p>${data.data.keterangan_cuti==null ? '-' : data.data.keterangan_cuti}</p>`;

                                // console.log(kuota, lamaCuti, sisa);

                            }
                            console.log(watermark);
                            cuti += ` <div class="approve-card shadow-sm">
                          <div class="watermark-logo">
                                ${watermark}
                            </div>
                            <div class="approve-header">
                                <div class="approve-avatar">
                                    ${profile}
                                </div>
                                <div class="approve-title">
                                   <h6 class="mb-1">${data.data.nama_karyawan}</h6>
                                    ${type}
                                </div>
                                ${status}
                            </div>

                            <!-- DETAIL BODY -->
                            <div class="approve-body mb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Tanggal Mulai</small>
                                            ${tanggal_mulai_cuti}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Tanggal Selesai</small>
                                            <br>
                                         ${tanggal_selesai_cuti}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Kembali Bekerja</small>
                                            <br>
                                            <span class="fw-semibold text-success">
                                                ${tanggal_kembali_bekerja}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Lama Cuti</small>
                                            <br>
                                         ${total_hari} Hari
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Pengganti</small>
                                        <p>${data.data.karyawan_pengganti ? data.data.karyawan_pengganti.name : '-'}</p>
                                        </div>
                                    </div>`;
                            if (data.data.kategori_cuti_id == null) {
                                cuti += `
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Keterangan</small>
                                          ${keterangan_cuti}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Kuota</small>
                                            <br>
                                            <span>
                                                ${kuota}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Terpakai</small>
                                            <br>
                                            <span>
                                                ${terpakai}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Sisa</small>
                                            <br>
                                            <span>
                                                ${sisa}
                                            </span>
                                        </div>
                                    </div>`;
                                if (data.data.toleransi == 1) {
                                    cuti += `
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Toleransi</small>
                                            <br>
                                            <span class="fw-bold text-danger">
                                                Tidak Ditoleransi
                                            </span>
                                        </div>
                                    </div> `;

                                }
                            }


                            cuti += `</div>
                            </div>
                            <!-- APPROVAL SECTION -->
                            ${ttd}
                            ${footer}

                        </div>`;
                        } else {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            cuti +=
                                `<div class="text-center mt-3 mb-3"><small class="text-muted">${data.message}</small></div>`;
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);
                        }
                        hideSkeleton('.content-approve-cuti', cuti);
                    }
                })
            }


            function formatLabel(date) {
                const d = new Date(date);

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                const hour = String(d.getHours()).padStart(2, '0');
                const minute = String(d.getMinutes()).padStart(2, '0');

                return `${day}-${month}-${year}`;
            }

            function tanggal_kembali_kerja(tglSelesai) {
                // 1. Buat objek date dari string input
                const d = new Date(tglSelesai);

                // 2. Tambahkan 1 hari ke tanggal tersebut
                d.setDate(d.getDate() + 1);
                if (dataCuti.user.shift == 'NON SHIFT') {
                    if (d.getDay() === 0) {
                        d.setDate(d.getDate() + 1);
                    }
                }

                // 3. Ambil nilai day, month, year setelah ditambah
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
