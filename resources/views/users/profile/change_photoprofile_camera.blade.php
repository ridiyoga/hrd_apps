@extends('users.layouts.main')
@section('title') APPS | KARYAWAN - SP @endsection
@section('css')
    <style>
        .camera-wrapper {
            max-width: 360px;
            background: rgba(255, 255, 255, .85);
            backdrop-filter: blur(10px);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 15px 40px rgba(111, 90, 230, 0.25);
        }

        .camera-header h5 {
            color: #4b3fbf;
        }

        .camera-box {
            border-radius: 18px;
            overflow: hidden;
            border: 2px dashed rgba(111, 90, 230, .35);
        }

        #my_camera video {
            width: 100%;
            border-radius: 18px;
        }

        .btn-purple {
            background: linear-gradient(135deg, #7b6cff, #5b4fd6);
            color: #fff;
            border: none;
        }

        .btn-purple:hover {
            opacity: .9;
        }

        .btn-rounded {
            border-radius: 50px;
        }

        .preview-image img {
            width: 220px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .25);
        }

        .camera-offcanvas {
            border-radius: 26px 26px 0 0;
        }
    </style>
@endsection
@section('content')
    <div class="container py-4">
        <div class="camera-wrapper mx-auto">

            <div class="camera-header text-center mb-3">
                <h5 class="mb-0 fw-semibold">Ambil Foto Profil</h5>
                <small class="text-muted">Pastikan wajah terlihat jelas & pencahayaan cukup</small>
            </div>
            <div class="alert_change_photo"></div>
            <div class="camera-box">
                <div id="my_camera"></div>
            </div>

            <div class="text-center mt-3">
                <button class="btn btn-purple btn-rounded btn-sm" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvas_result_foto" onclick="take_snapshot()">
                    <i class="fa-solid fa-camera"></i>&nbsp;Ambil Foto
                </button>
            </div>

        </div>
    </div>

    <!-- OFFCANVAS PREVIEW -->
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom camera-offcanvas" tabindex="-1" id="offcanvas_result_foto">
        <div class="offcanvas-body text-center">
            <h5 class="fw-semibold mb-1">Konfirmasi Foto</h5>
            <p class="text-muted small mb-3">Foto ini akan digunakan sebagai identitas karyawan</p>

            <form method="POST" action="{{ url('profile/save_capture_profile') }}">
                @csrf

                <div id="results" class="preview-image mb-3"></div>
                <input type="hidden" name="gallery_image" class="image-tag">
                <input type="text" name="old_foto_karyawan" id="old_foto_karyawan"
                    value="{{ $user_karyawan->foto_karyawan }}">

                <div class="d-flex justify-content-center gap-2">
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="fa-regular fa-floppy-disk"></i>&nbsp;Simpan
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="offcanvas">
                        <i class="fa-solid fa-rotate-right"></i>&nbsp;Ulangi
                    </button>
                </div>
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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.css">

    <script language="JavaScript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 85
        });

        Webcam.attach('#my_camera');

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);
                // console.log(data_uri);
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
            });
        }
        var session_success = "{{ session('photo_update_success') }}";
        var session_error = "{{ session('photo_update_error') }}";
        if (session_success) {
            $('.alert_change_photo').stop(true, true).hide().html(
                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                '<i class="fa-regular fa-circle-check"></i>' +
                '<strong>Sukses</strong>&nbsp;' + session_success +
                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>').fadeIn();
            setTimeout(function() {
                $('.alert_change_photo').fadeOut('slow');
            }, 100000);
        } else if (session_error) {
            $('.alert_change_photo').stop(true, true).hide().html(
                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                '<i class="fa-solid fa-circle-exclamation"></i>' +
                '<strong>Error!</strong>&nbsp;' + session_error +
                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>').fadeIn();
            setTimeout(function() {
                $('.alert_change_photo').fadeOut('slow');
            }, 100000);

        }
    </script>
    <script>
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
    </script>
@endsection
