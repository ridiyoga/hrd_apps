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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <style>
        .alert_approve_cuti {
            max-width: 100%;
            overflow-x: hidden;
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

        /* Style Signature Pad */
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

        .border-dashed {
            border: 2px dashed #d8b4fe !important;
        }

        .opacity-75 {
            opacity: 0.75;
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

        h6 {
            font-size: 0.9rem !important;
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
            gap: 6px;
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

        .btn-dobel {
            flex: 1;
            border: none;
            /* background: linear-gradient(135deg, #f3c807, #f3c807); */
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-dobel:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(34, 197, 94, 0.4);
        }

        .btn-reject {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-pdf {
            flex: 1;
            border: none;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            border-radius: 14px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(239, 68, 68, 0.4);
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

        .approve-footer {
            margin-top: 18px;
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon title_icon">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <h5 class="mt-3 mb-1">Detail Cuti</h5>
            <small class="text-muted">Informasi Detail Pengajuan Cuti</small>
        </div>
        <div class="alert_approve_cuti"></div>

        <div class="content-approve-cuti px-3 px-md-4">
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-approve" style="height: 55vh;">
        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Otorisasi & Pengesahan</h6>
                    <small class="text-muted">Persetujuan Pengajuan Cuti </small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-2  overflow-auto" id="offcanvas_body_approve" style="overflow-y: auto !important;">
            <form class="my-2" method="post" id="form_pengajuan_cuti" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_cuti" id="id_cuti" value="">
                <input type="hidden" name="status" id="status" value="">
                <input type="hidden" name="menu_id" id="menu_id" value="{{ $menu_id }}">

                <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-light border-0 py-2 text-center">
                        <input type="text" name="name" id="name" value="{{ $user_karyawan->name }}" hidden>
                        <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                            id="user_name"></small>
                    </div>
                    <div class="card-body p-2">
                        <div class="alert_signature"></div>
                        <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                            <canvas id="signature-pad"
                                style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                hidden></canvas>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                id="clear">
                                <i class="fa-solid fa-eraser me-1"></i> Hapus
                            </button>
                            <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di atas</small>
                        </div>
                        <textarea id="signature" name="signature" style="display: none"></textarea>
                    </div>
                </div>

                <div class="alert_error_cuti col-12"></div>
                <div class="mt-3 text-center">
                    <button type="button" id="btn_save_approve" class="btn light btn-success btn-sm me-2">
                        <i class="fa-solid fa-save me-2"></i>Approve
                    </button>
                    <button type="button" id="btn-cancel" data-bs-dismiss="offcanvas" data-bs-target="#off-canvas-approve"
                        class="btn light btn-danger btn-sm">
                        Batal&nbsp;<i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <!-- <div class="dz-spinner d-flex align-items-center flex-wrap"> -->
                    <!-- </div> -->
                </div>
            </form>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom rounded-top-5" tabindex="-1" id="off-canvas-dobel" style="height: 55vh;">
        <div class="offcanvas-header border-bottom py-3">
            <div class="d-flex align-items-center">
                <div class="futuristic-icon shadow-sm me-3" style="width: 45px; height: 45px; font-size: 20px;">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold text-dark">Otorisasi & Pengesahan</h6>
                    <small class="text-muted">Persetujuan Cuti Non Prosedural </small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body p-2  overflow-auto" id="offcanvas_body_dobel" style="overflow-y: auto !important;">
            <div class="py-2">
                <form class="my-2" method="post" id="form_pengajuan_dobel" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_cuti_dobel" id="id_cuti_dobel" value="{{ $data->id }}">
                    <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{ $data->karyawan_id }}">
                    <input type="hidden" name="menu_id" id="menu_id" value="{{ $menu_id }}">

                    <input type="hidden" name="jumlah_hari" id="jumlah_hari" value="{{ $data->jumlah_hari }}">
                    <input type="hidden" name="kuota" id="kuota" value="{{ $data->kuota }}">
                    <input type="hidden" name="terpakai" id="terpakai"
                        value="{{ $data->User->KuotaCuti->terpakai + $data->jumlah_hari * 2 }}">
                    <input type="hidden" name="sisa" id="sisa"
                        value="{{ $data->User->KuotaCuti->sisa - $data->jumlah_hari * 2 }}">


                    <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                        <div class="card-header bg-light border-0 py-2 text-center">
                            <input type="text" name="name" id="name" value="{{ $user_karyawan->name }}"
                                hidden>
                            <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                                id="user_name"></small>
                        </div>
                        <div class="card-body p-2">
                            <div class="alert_signature"></div>
                            <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                                <canvas id="signature-pad2"
                                    style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                    hidden></canvas>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                    id="clear2">
                                    <i class="fa-solid fa-eraser me-1"></i> Hapus
                                </button>
                                <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di atas</small>
                            </div>
                            <textarea id="signature2" name="signature2" style="display: none"></textarea>
                        </div>
                    </div>

                    <div class="alert_error_cuti col-12"></div>
                    <div class="mt-3 text-center">
                        <button type="button" id="btn_save_dobel" data-bs-dismiss="offcanvas"
                            class="btn light btn-warning btn-sm me-2">
                            <i class="fa-solid fa-save me-2"></i>Approve
                        </button>
                        <button type="button" id="btn-cancel" data-bs-dismiss="offcanvas"
                            data-bs-target="#off-canvas-dobel" class="btn light btn-danger btn-sm">
                            Batal&nbsp;<i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <!-- <div class="dz-spinner d-flex align-items-center flex-wrap"> -->
                        <!-- </div> -->
                    </div>
                </form>
            </div>
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
            let id_cuti = '{{ $id }}';
            const canvas = document.getElementById('signature-pad');
            const canvas2 = document.getElementById('signature-pad2');
            const signaturePad = new SignaturePad(canvas);
            const signaturePad2 = new SignaturePad(canvas2);
            var session_sukses = "{{ session('success_approve_quick') }}";
            var session_gagal = "{{ session('hapus_cuti_gagal') }}";
            var karyawan_name = "{{ $user_karyawan->name }}";
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

            $(document).on('click', '.btn-approve', function(e) {
                $('#signature-pad').removeAttr('hidden');
                $('#id_cuti').val(id_cuti);
                $('#id_approve').val($(this).data('id'));
                $('#status').val('approved');
                $('#user_name').html('Disahkan Oleh : ' + karyawan_name);

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);

                    // ambil ukuran CSS asli
                    const width = canvas.offsetWidth;
                    const height = canvas.offsetHeight;

                    // set ukuran canvas sebenarnya
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;

                    const ctx = canvas.getContext('2d');
                    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                    ctx.scale(ratio, ratio);

                    signaturePad.clear();
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
                const offcanvasEl = document.getElementById('off-canvas-approve');
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvas.show();
            })
            $(document).on('click', '.btn-reject', function(e) {
                $('#signature-pad').removeAttr('hidden');
                $('#id_cuti').val(id_cuti);
                $('#id_approve').val($(this).data('id'));
                $('#status').val('reject');
                $('#user_name').html('Ditolak Oleh : ' + karyawan_name);

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);

                    // ambil ukuran CSS asli
                    const width = canvas.offsetWidth;
                    const height = canvas.offsetHeight;

                    // set ukuran canvas sebenarnya
                    canvas.width = width * ratio;
                    canvas.height = height * ratio;

                    const ctx = canvas.getContext('2d');
                    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                    ctx.scale(ratio, ratio);

                    signaturePad.clear();
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
                const offcanvasEl = document.getElementById('off-canvas-approve');
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvas.show();
            })
            $('#clear2').on('click', () => signaturePad2.clear());
            $(document).on('click', '.btn-dobel', function(e) {
                $('#signature-pad2').removeAttr('hidden');
                $('#id_cuti').val(id_cuti);
                $('#id_approve').val($(this).data('id'));
                $('#status').val('dobel');
                $('#user_name').html('Disetujui Oleh : ' + karyawan_name);

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);

                    // ambil ukuran CSS asli
                    const width = canvas2.offsetWidth;
                    const height = canvas2.offsetHeight;

                    // set ukuran canvas2 sebenarnya
                    canvas2.width = width * ratio;
                    canvas2.height = height * ratio;

                    const ctx = canvas2.getContext('2d');
                    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                    ctx.scale(ratio, ratio);

                    signaturePad.clear();
                }

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
                const offcanvasEl = document.getElementById('off-canvas-dobel');
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                offcanvas.show();
            })
            $(document).on('click', '#btn_save_approve', function(e) {
                let btn = $(this);
                let originalText = btn.html();

                let signature_value = '';
                if (!signaturePad.isEmpty()) {
                    signature_value = signaturePad.toDataURL();
                }
                $('#signature').val(signature_value);
                var id = $('#id_cuti').val();
                var signature = $('#signature').val();
                var note = $('#approval_note').val();
                var signature = $('#signature').val();
                var total_cuti = $('#total_hari').val();
                var status = $('#status').val();
                var menu_id = $('#menu_id').val();
                $.ajax({
                    url: "{{ url('approval/cuti/save_approve') }}" + '/' + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        signature: signature,
                        note: note,
                        total_cuti: total_cuti,
                        status: status,
                        menu_id: menu_id
                    },
                    beforeSend: function() {
                        btn.prop('disabled', true);
                        btn.html(
                            `<span class="spinner-border spinner-border-sm me-2"></span>Loading... `
                        );
                    },
                    success: function(data) {
                        // console.log(data);
                        btn.prop('disabled', false);
                        btn.html(originalText);
                        if (data.code == 200) {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            const offcanvasEl = document.getElementById('off-canvas-approve');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);

                            load_data_cuti();
                        } else if (data.code == 422) {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            const offcanvasEl = document.getElementById('off-canvas-approve');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);
                        } else if (data.code == 500) {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            const offcanvasEl = document.getElementById('off-canvas-approve');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);
                        }
                    },
                    error: function(data) {
                        btn.prop('disabled', false);
                        btn.html(originalText);
                    }
                })
            })
            $(document).on('click', '#btn_save_dobel', function(e) {
                let btn = $(this);
                let originalText = btn.html();

                let signature_value2 = '';
                if (!signaturePad2.isEmpty()) {
                    signature_value2 = signaturePad2.toDataURL();
                }
                $('#signature2').val(signature_value2);
                var id = $('#id_cuti_dobel').val();
                var signature2 = $('#signature2').val();
                var kuota = $('#kuota').val();
                var terpakai = $('#terpakai').val();
                var sisa = $('#sisa').val();
                var menu_id = $('#menu_id').val();
                $.ajax({
                    url: "{{ url('approval/cuti/save_dobel') }}" + '/' + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        signature: signature2,
                        kuota: kuota,
                        terpakai: terpakai,
                        sisa: sisa,
                        menu_id: menu_id
                    },
                    beforeSend: function() {
                        btn.prop('disabled', true);
                        btn.html(
                            `<span class="spinner-border spinner-border-sm me-2"></span>Loading... `
                        );
                    },
                    success: function(data) {
                        // console.log(data);
                        btn.prop('disabled', false);
                        btn.html(originalText);
                        if (data.code == 200) {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            const offcanvasEl = document.getElementById('off-canvas-approve');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);

                            load_data_cuti();
                        } else if (data.code == 422) {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            const offcanvasEl = document.getElementById('off-canvas-approve');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);
                        } else if (data.code == 500) {
                            $('.alert_approve_cuti').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                data.message + '</div>').fadeIn(100);
                            const offcanvasEl = document.getElementById('off-canvas-approve');
                            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(
                                offcanvasEl);
                            offcanvas.hide();
                            setTimeout(function() {
                                $('.alert_approve_cuti').fadeOut('slow');
                            }, 8000);
                        } else if (data.code == 401) {
                            console.log(data);

                            Swal.fire({
                                text: data.message,
                                icon: 'warning',
                                timer: 4500
                            });
                        }
                    },
                    error: function(data) {
                        btn.prop('disabled', false);
                        btn.html(originalText);
                    }
                })
            })

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
                            console.log(data.kuota.kuota)
                            dataCuti = data;
                            const signatureBaseUrl = "{{ asset('storage/signatures') }}";
                            const profileBaseUrl = "{{ asset('storage/foto_karyawan') }}";
                            // console.log(data, signatureBaseUrl);
                            if (data.data == '' || data.data == null) {
                                cuti += `  <div class="text-center text-muted py-4">
                                        <i class="fa-solid fa-file-lines fa-bounce fa-2x"></i>
                                        <p class="mb-0">Belum ada data</p>
                                    </div>`;

                            } else {
                                var profile = '';
                                var user_from = '';
                                var atasan1 = '';
                                var atasan2 = '';
                                var type = '';
                                var status = '';
                                var nama_cuti = '';
                                var total_hari = '';
                                var kuota_cuti = '';
                                var terpakai_cuti = '';
                                var sisa_cuti = '';
                                var tanggal_mulai_cuti = '';
                                var tanggal_selesai_cuti = '';
                                var tanggal_kembali_bekerja = '';
                                var keterangan_cuti = '';
                                var tanggal_pengajuan = '';
                                var tanggal_pengajuan_cuti = '';
                                var tanggal_approved_atasan1 = '';
                                var tanggal_approved_atasan2 = '';
                                var approved = '';
                                var ttd = '';
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
                                            `<img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="foto_profil" alt="User">`;
                                    } else {
                                        profile +=
                                            `<img src="${profileBaseUrl}/${data.data.user.foto_karyawan}" class="foto_profil" alt="User">`;
                                    }
                                }
                                tanggal_pengajuan_cuti += data.data.tanggal_pengajuan ? formatLabel(data
                                    .data.tanggal_pengajuan) : '';
                                tanggal_approved_atasan1 += data.data.waktu_approve ? formatLabel(data
                                    .data.waktu_approve) : '';
                                tanggal_approved_atasan2 += data.data.waktu_approve2 ? formatLabel(data
                                    .data.waktu_approve2) : '';
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
                                    actionButtonClass = '';
                                    status += '<span class="badge-status pending">Pending</span>';
                                } else if (data.data.status_cuti == 1) {
                                    actionButtonClass = '';
                                    status += '<span class="badge-status pending">Approve SPV</span>';
                                    if (data.data.id_user_atasan == data.user.id && data.data
                                        .ttd_atasan != null) {
                                        status +=
                                            '<span class="badge-status approved">Disetujui</span>';
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
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan_cuti}</small>
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
                                            `<img src="{{ asset('assets/assets_users/images/approved.png') }}" alt="watermark">`;
                                    } else {
                                        status += '<span class="badge-status pending">Pending</span>';
                                    }
                                } else if (data.data.status_cuti == 2) {
                                    actionButtonClass = 'd-none';
                                    if (data.data.id_user_atasan2 == data.user.id && data.data
                                        .ttd_atasan2 != null) {
                                        // console.log(data.detail_exists);
                                        if (data.detail_exists == 'semua') {
                                            status +=
                                                '<span class="badge-status pending">Dibatalkan</span>';
                                        } else if (data.detail_exists == 'sebagian') {
                                            status +=
                                                '<span class="badge-status pending">Disetujui sebagian</span>';
                                        } else if (data.detail_exists == 'tidak') {
                                            status +=
                                                '<span class="badge-status approved">Disetujui</span>';
                                        }
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
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan_cuti}</small>
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
                                    <div class="approve-footer">
                                        <div class="action-buttons">
                                            <a href ="{{ url('approval/cuti/cetak_form_cuti') }}/${data.data.id}")"  class="btn btn-pdf" target="_blank">
                                                <i class="fa-solid fa-file-pdf me-1"></i> Unduh PDF
                                            </a>
                                        </div>
                                    </div>`;
                                        watermark +=
                                            `<img src="{{ asset('assets/assets_users/images/approved.png') }}" alt="watermark">`;
                                    } else {
                                        status += '<span class="badge-status pending">Pending</span>';
                                    }
                                    $('.action-buttons').addClass('d-none');
                                } else if (data.data.status_cuti == 5) {
                                    actionButtonClass = 'd-none';
                                    status += '<span class="badge-status rejected">Reject</span>';
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
                                                <small style="font-size: 8px; line-height: 1.1;">Tgl: ${tanggal_pengajuan_cuti}</small>
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
                                // if (data.user.id == data.data.id_user_atasan) {
                                //     var catatan = data.data.catatan;
                                // } else if (data.user.id == data.data.id_user_atasan2) {
                                //     var catatan = data.data.catatan2;
                                // }
                                tanggal_mulai_cuti += `<p>${formatLabel(data.data.tanggal_mulai)}</p>`;
                                tanggal_selesai_cuti +=
                                    `<p>${formatLabel(data.data.tanggal_selesai)}</p>`;
                                tanggal_kembali_bekerja +=
                                    `<p>${tanggal_kembali_kerja(data.data.tanggal_selesai)}</p>`;
                                nama_cuti +=
                                    `<p>${data.data.nama_cuti}</p>`;
                                total_hari +=
                                    `<p>${data.jumlah_cuti} Hari</p>`;
                                kuota_cuti +=
                                    `<p>${data.data.kuota ?? '-'} Hari</p>`;
                                terpakai_cuti +=
                                    `<p>${data.data.terpakai ?? '-'} Hari</p>`;
                                sisa_cuti +=
                                    `<p>${data.data.sisa ?? '-'} Hari</p>`;
                                keterangan_cuti +=
                                    `<p>${data.data.keterangan_cuti == null ? '-' : data.data.keterangan_cuti}</p>`;
                                tanggal_pengajuan +=
                                    `<p>${formatLabel2(data.data.tanggal_pengajuan)}</p>`;


                                // console.log(data.user.shift);


                            }
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
                            </div>`

                            if (data.data.status_cuti == 2) {
                                cuti += `<div class="">
                                            <div class="row g-3 mb-3">
                                                <div class="info-box">
                                                    <small>No. Cuti</small>
                                                <p>${data.data.no_form_cuti}</p>
                                            </div>
                                        </div>
                                    </div>`
                            }
                            if (data.data.kategori_cuti_id == null) {
                                // console.log(data.detail_cuti);
                                let tanggalCuti = '';
                                data.detail_cuti.forEach(function(item) {
                                    if (item.status == 1) {
                                        tanggalCuti +=
                                            `<p class="text-success">${moment(item.tgl_cuti).locale('id').format('D-MM-YYYY')}<span class="mdi mdi-check-circle"></span></p>`
                                    } else if (item.status == 2) {
                                        tanggalCuti +=
                                            `<p class="text-danger">${moment(item.tgl_cuti).locale('id').format('D-MM-YYYY')}<span class="mdi mdi-close-circle"></span></p>`
                                    } else {
                                        tanggalCuti +=
                                            `<p>${moment(item.tgl_cuti).locale('id').format('D-MM-YYYY')}</p>`
                                    }
                                });
                                cuti += `
                                <div class="approve-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="info-box">
                                                <small>Tanggal Cuti</small>
                                            ${tanggalCuti}
                                            </div>
                                        </div>`

                            } else {
                                cuti += `<!-- DETAIL BODY -->
                                <div class="approve-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="info-box">
                                            <small>Tanggal Mulai</small>
                                            ${tanggal_mulai_cuti}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-box">
                                                <small>Tanggal Selesai</small>
                                                ${tanggal_selesai_cuti}
                                            </div>
                                        </div>`
                            }
                            cuti += `<div class="col-6">
                                        <div class="info-box">
                                            <small>Tanggal Pengajuan</small>
                                        <span class="fw-semibold">
                                          ${tanggal_pengajuan}
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Kembali Bekerja</small>
                                        <span class="fw-semibold">
                                          ${tanggal_kembali_bekerja}
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Nama Cuti</small>
                                          ${nama_cuti}
                                        </div>
                                    </div>
                                   
                                    <div class="col-6">
                                        <div class="info-box">
                                            <small>Karyawan Pengganti</small>
                                            <br>
                                            <span class="fw-bold">
                                                ${data.data.nama_karyawan_pengganti}
                                            </span>
                                        </div>
                                    </div>
                          <div class="col-6">
                                        <div class="info-box">
                                            <small>Keterangan Cuti</small>
                                            <br>
                                            <span>
                                                ${keterangan_cuti}
                                            </span>
                                        </div>
                                    </div>`
                            if (data.data.kategori_cuti_id == null) {
                                const tanggal_mulai_cuti = moment(data.data.tanggal_mulai,
                                    'YYYY-MM-DD');
                                const tanggal_pengajuan = moment(data.data.created_at);

                                const selisihHari = tanggal_mulai_cuti.diff(tanggal_pengajuan, 'days');
                                console.log(data);
                                if (selisihHari < 13 && data.data.id_user_atasan2 == data.user.id &&
                                    data.data.ttd_atasan2 == null) {
                                    cuti += `
                                        <div class="col-6">
                                            <div class="info-box">
                                                <small>Toleransi</small>
                                                <br>
                                                <span>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="toleransi_radio" checked>
                                                    </div>
                                                </span>
                                            </div>
                                        </div>    
                                                       
                                        `;
                                }
                                if (data.data.toleransi == 1) {
                                    cuti += `
                                        <div class="col-6">
                                            <div class="info-box">
                                                <small>Toleransi</small>
                                                <br>
                                                <span class="fw-bold">
                                                    Tidak Ditoleransi
                                                </span>
                                            </div>
                                        </div>    
                                                       
                                        `;
                                }
                                cuti += `
                                    <div class="col-6 toleran">
                                        <div class="info-box">
                                            <small>Kuota Cuti</small>
                                            <br>
                                            <span>
                                                ${kuota_cuti}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 toleran">
                                        <div class="info-box">
                                            <small>Lama Cuti</small>
                                          ${total_hari}
                                        </div>
                                    </div>
                                     
                                    <div class="col-6 toleran">
                                        <div class="info-box">
                                            <small>Terpakai</small>
                                            <br>
                                            <span>
                                                ${terpakai_cuti}
                                            </span>
                                        </div>
                                    </div>
                                     <div class="col-6 toleran">
                                        <div class="info-box">
                                            <small>Sisa Cuti</small>
                                            <br>
                                            <span>
                                                ${sisa_cuti}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6 intoleran d-none">
                                        <div class="info-box">
                                            <small>Kuota Cuti</small>
                                            <br>
                                            <span>
                                                ${kuota_cuti}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 intoleran d-none">
                                        <div class="info-box">
                                            <small>Lama Cuti</small>
                                          ${total_hari}
                                        </div>
                                    </div>
                                     
                                    <div class="col-6 intoleran d-none">
                                        <div class="info-box">
                                            <small>Terpakai</small>
                                            <br>
                                            <span class="fw-bold">
                                                ${data.data.cuti_kuota.terpakai + (data.jumlah_cuti * 2)} Hari
                                            </span>
                                        </div>
                                    </div>
                                     <div class="col-6 intoleran d-none">
                                        <div class="info-box">
                                            <small>Sisa Cuti</small>
                                            <br>
                                            <span class="fw-bold">
                                                ${data.data.cuti_kuota.sisa - (data.jumlah_cuti * 2) } Hari
                                            </span>
                                        </div>
                                    </div>  
                                    
                                </div>
                            </div>                         
                            
                            `;


                            }
                            // console.log(data.data)
                            cuti += `
                            <!-- APPROVAL SECTION -->
                            ${ttd}
                            <div class="approve-footer ${approved}">
                            <input type="hidden" name="total_hari" id="total_hari" value="${data.jumlah_cuti}">
                                <div class="action-buttons ${actionButtonClass}">
                                    <button class="btn btn-approve">
                                        <i class="fa-solid fa-circle-check me-1"></i> Approve
                                    </button>
                                    <button class="btn btn-dobel btn-warning d-none w-50">
                                                <i class="fa-solid fa-circle-check me-1"></i> Approve
                                            </button>
                                    <button class="btn btn-reject">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Reject
                                    </button>
                                </div>`;

                            cuti += `
                            <!-- APPROVAL SECTION -->
                            ${ttd}
                            <div class="approve-footer ${approved}">
                            <input type="hidden" name="total_hari" id="total_hari" value="${data.jumlah_cuti}">
                            </div>

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
            $(document).on('change', '#toleransi_radio', function() {
                if ($(this).is(':checked')) {
                    // console.log('asoy');
                    $('.btn-dobel').addClass('d-none')
                    $('.intoleran').addClass('d-none')
                    $('.btn-approve').removeClass('d-none')
                    $('.toleran').removeClass('d-none')

                    // ON
                } else {
                    // console.log('asoy1');
                    $('.btn-dobel').removeClass('d-none')
                    $('.intoleran').removeClass('d-none')
                    $('.btn-approve').addClass('d-none')
                    $('.toleran').addClass('d-none')
                    // OFF
                }
            });


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

            function formatLabel(date) {
                const d = new Date(date);

                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                const hour = String(d.getHours()).padStart(2, '0');
                const minute = String(d.getMinutes()).padStart(2, '0');

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
