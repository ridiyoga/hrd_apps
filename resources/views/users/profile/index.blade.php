@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1051 !important;
        }

        .avatar-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .15);
        }

        .profile-avatar {
            position: relative;
            width: fit-content;
        }

        .edit-avatar {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #fff;
            padding: 6px 8px;
            border-radius: 50%;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .2);
            color: --primary;
        }

        .profile-action {
            display: flex;
            justify-content: space-around;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .profile-action a {
            width: 50px;
            height: 50px;
            background: #f5f7fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: --primary;
        }

        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 14px 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .1);
            display: flex;
            flex-direction: column;
            gap: 6px;
            color: --primary;
            transition: .2s ease;
        }

        .info-card i {
            font-size: 22px;
        }

        .info-card span {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
        }

        .bnr-img {
            border-radius: 22px 22px 0 0;
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
            height: 26px;
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

        .section-title.accent-inline span {
            display: inline-block;
            padding: 6px 16px;
            font-size: 14px;
            font-weight: 600;
            background: linear-gradient(90deg,
                    rgba(111, 79, 216, 0.35),
                    rgba(111, 79, 216, 0.15));
            border-radius: 0 999px 999px 0;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.css">
@endsection
@section('content_top')
    <div class="alert_profile"></div>
    <div class="dz-banner-heading">
        <div class="overlay-black-light">
            <img src="{{ asset('assets/assets_users/images/bg_profil.jpg') }}" class="bnr-img" alt="">
        </div>
    </div>
    <div class="container profile-area">
        <div class="profile text-center position-relative">

            <!-- Avatar -->
            <div class="profile-avatar mx-auto mb-2">
                @if (empty($user_karyawan->foto_karyawan))
                    <a href="{{ asset('admin/assets/img/avatars/1.png') }}">
                        <img src="{{ asset('admin/assets/img/avatars/1.png') }}" class="avatar-img">
                    </a>
                @else
                    <a href="{{ asset('storage/foto_karyawan/' . $user_karyawan->foto_karyawan) }}">
                        <img src="{{ asset('storage/foto_karyawan/' . $user_karyawan->foto_karyawan) }}" class="avatar-img">
                    </a>
                @endif

                <!-- Edit Photo -->
                <a href="javascript:void(0)" class="edit-avatar" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvas_edit_profile">
                    <i class="fa-regular fa-pen-to-square"></i>
                </a>
            </div>

            <!-- Info -->
            <h4 class="mb-0">{{ $user_karyawan->name }}</h4>
            <small class="text-muted">{{ Auth::user()->username }}</small>
            <p class="mt-1 text-primary">
                {{ $user_karyawan->PenempatanKerja->site_name }}
            </p>
        </div>
        <div class="card shadow-sm border-0 rounded-4 mb-3">

            <div class="card-body card-profile-action py-3">

            </div>
        </div>

        <div class="skill-section mt-4">
            <!-- <h5 class="mb-3 fw-bold">Informasi</h5> -->
            <h6 class="section-title accent-inline">
                <span>INFORMASI</span>
            </h6>
            <div class="row g-3 card-profile-info">
            </div>
        </div>

    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_edit_profile"
        aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h5 class="title">Pilih Metode</h5>
            <p>Pilih Menggunakan Gallery Atau Kamera</p>
            <button type="button" class="btn btn-sm btn-info light pwa-btn " id="btn_gallery">
                <i class="fa-regular fa-images"></i>
                &nbsp;Gallery
            </button>
            <a id="btn_klik" href="{{ url('profile/change_photoprofile_camera/' . $menu_id) }}"
                class="btn btn-sm light btn-primary ms-2">
                <i class="fa-solid fa-camera"></i>
                &nbsp;Camera
            </a>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_konfirmasi_edit_profile"
        aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h5 class="title">Konfirmasi</h5>
            <p>Konfirmasi Pengambilan Foto</p>
            <img src="" id="preview_gallery" alt="">
            <div class="col-md-4">
                <div class="preview"></div>
            </div>
            <form method="POST" id="form_konfirmasi" action="{{ url('profile/save_capture_profile') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="col-md-6">
                    <div id="results"></div>
                    <img id="image_crop" class="img-fluid rounded">
                    <input type="hidden" name="karyawan_id" value="{{ $user_karyawan->id }}">
                    <input type="hidden" name="menu_id" value="{{ $menu_id }}">
                    <input type="file" hidden id="foto_karyawan" name="foto_karyawan" value="" accept="image/jpeg">
                    <input type="file" id="foto_karyawan_pic" name="foto_karyawan_pic" value="" hidden>
                    <input type="text" name="foto_karyawan_old" value="{{ $user_karyawan->foto_karyawan }}" hidden>
                </div>
                <br>
                <button type="button" id="btn_simpan" class="btn btn-sm btn-info light pwa-btn">
                    <i class="fa-regular fa-floppy-disk"></i>
                    &nbsp;Simpan
                </button>
                <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                    aria-label="Close">
                    &nbsp;Batal
                </a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.js"></script>
    <script>
        var cropper;
        var image = document.getElementById('preview_gallery');
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
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
        $(document).ready(function() {
            var session_success = "{{ session('photo_update_success') }}";
            var session_error = "{{ session('photo_update_error') }}";

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#preview_gallery').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
            let cropper;
            let $image = $('#image_crop');
            let offcanvasEl = $('#offcanvas_konfirmasi_edit_profile');

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
            showProfileAction('.card-profile-action');
            showProfileInfo('.card-profile-info');
            // BUAT SHOW PROFILE ACTION
            function showProfileAction() {
                let profileAction = '';
                profileAction += ` <ul class="profile-action">
                <li class="text-center">
                    <a href="{{ url('/profile/detail_profile/' . $menu_id) }}">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </a>
                    <small>Profil</small>
                </li>
                <li class="text-center">
                    <a href="{{ url('profile/detail_hr/' . $menu_id) }}">
                        <i class="fa-solid fa-user-tag"></i>
                    </a>
                    <small>HR</small>
                </li>
                <li class="text-center">
                    <a href="{{ url('profile/detail_account/' . $menu_id) }}">
                        <i class="fa-solid fa-user-shield"></i>
                    </a>
                    <small>Akun</small>
                </li>
            </ul>`;
                hideSkeleton('.card-profile-action', profileAction);
            }
            // BUAT SHOW PROFILE INFO
            function showProfileInfo() {
                let profileInfo = '';
                profileInfo += `
             <!-- Jabatan -->
            <div class="col-4">
                <a href="{{ url('profile/lihat_jabatan/' . $menu_id) }}" class="info-card">
                    <i class="fa-solid fa-user-tie"></i>
                    <span>Jabatan</span>
                </a>
            </div>

            <!-- Kontrak -->
            <div class="col-4">
                <a href="{{ url('profile/lihat_kontrak_kerja/' . $menu_id) }}" class="info-card">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Kontrak</span>
                </a>
            </div>

            <!-- Dokumen -->
            <div class="col-4">
                <a href="{{ url('profile/lihat_dokumen') }}" class="info-card">
                    <i class="fa-regular fa-file-lines"></i>
                    <span>Dokumen</span>
                </a>
            </div>

            <!-- Organisasi -->
            <div class="col-4">
                <a href="{{ url('profile/lihat_struktur_organisasi') }}" class="info-card">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Organisasi</span>
                </a>
            </div>

            <!-- Rekan Kerja -->
            <div class="col-4">
                <a href="{{ url('profile/lihat_rekan_kerja/' . $menu_id) }}" class="info-card">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Rekan</span>
                </a>
            </div>            
            <!-- Rekan Kerja -->
            <div class="col-4">
                <a href="{{ url('inventaris/dashboard/' . $menu_id) }}" class="info-card">
                    <i class="fa-solid fa-desktop"></i>
                    <span>Inventaris</span>
                </a>
            </div>`;
                hideSkeleton('.card-profile-info', profileInfo);
            }

            $(document).on('click', '#btn_gallery', function() {
                $('#foto_karyawan').trigger('click');
            })
            $('#foto_karyawan').on('change', function(e) {

                const file = e.target.files[0];
                if (!file) return;

                // Validasi tipe file
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Format file tidak sesuai!',
                    });
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {

                    // Set preview image
                    $image.attr('src', event.target.result);

                    // Tampilkan offcanvas
                    const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl[0]);
                    bsOffcanvas.show();

                    // Init cropper setelah offcanvas muncul
                    offcanvasEl.one('shown.bs.offcanvas', function() {

                        // Destroy cropper lama (kalau ada)
                        if (cropper) {
                            cropper.destroy();
                        }

                        cropper = new Cropper($image[0], {
                            aspectRatio: 1,
                            viewMode: 2,
                            autoCropArea: 1,
                            responsive: true,
                            background: false,
                            preview: '.preview'
                        });
                    });
                };

                reader.readAsDataURL(file);
            });
            $(document).on('click', '#btn_simpan', function(e) {
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
                if (!cropper) {
                    e.preventDefault();
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Lengkapi gambar terlebih dahulu!',
                    })
                }
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400
                });
                canvas.toBlob(function(blob) {

                    let file = new File([blob], "foto_karyawan.jpg", {
                        type: "image/jpeg",
                        lastModified: new Date().getTime()
                    });

                    let dt = new DataTransfer();
                    dt.items.add(file);

                    document.getElementById('foto_karyawan_pic').files = dt.files;

                    // baru submit setelah file terisi
                    $('#form_konfirmasi')[0].submit();

                }, 'image/jpeg', 0.9);
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
            if (session_success) {
                $('.alert_profile').stop(true, true).hide()
                    .html(`
                <div class="alert alert-success light alert-lg alert-dismissible fade show d-flex align-items-center gap-2">
                    <i class="fa-regular fa-circle-check fs-5"></i>
                    <div class="flex-grow-1">
                        <strong>Sukses</strong>&nbsp;${session_success}
                    </div>
                    <button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `).fadeIn();
                setTimeout(function() {
                    $('.alert_profile').fadeOut('slow');
                }, 10000);
            } else if (session_error) {
                $('.alert_profile').stop(true, true).hide().html(
                        '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                        '<i class="fa-solid fa-circle-exclamation"></i>>' +
                        '<strong>Error!</strong>&nbsp;' + session_error +
                        '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>')
                    .fadeIn();
                setTimeout(function() {
                    $('.alert_profile').fadeOut('slow');
                }, 10000);

            }

        });
    </script>
@endsection
