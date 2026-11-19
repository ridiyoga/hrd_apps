@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<style>
    .btn-rounded {
        border-radius: 50px;
    }

    .rounded-4 {
        border-radius: 1rem;
    }

    .card {
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .15);
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

    /* =====================
       LUXURY INPUT GROUP
       ===================== */
    .input-group-luxury {
        border-radius: 50px;
        /* Bentuk Kapsul */
        background: #fdfcff;
        border: 1px solid rgba(124, 58, 237, 0.2);
        /* Garis batas tipis ungu */
        transition: all 0.3s ease;
        overflow: hidden;
        /* Menjaga sudut tetap melengkung */
    }

    /* Efek saat input diklik (Focus) */
    .input-group-luxury:focus-within {
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        /* Glow ungu lembut */
    }

    /* Menghapus border bawaan form-control agar menyatu dengan bungkusnya */
    .input-group-luxury .form-control {
        border: none;
        background: transparent;
        font-size: 14px;
        padding: 12px 10px;
        box-shadow: none !important;
        /* Hilangkan shadow bawaan bootstrap */
    }

    .input-group-luxury .input-group-text {
        background: transparent;
        border: none;
        color: #a78bfa;
        padding-left: 20px;
        font-size: 16px;
    }

    /* Tombol Mata (Show/Hide) */
    .input-group-luxury .toggle-password {
        background: transparent;
        border: none;
        color: #9ca3af;
        padding-right: 20px;
        z-index: 10;
        transition: 0.3s;
    }

    .input-group-luxury .toggle-password:hover {
        color: #7c3aed;
    }

    .input-group-luxury .toggle-password:focus {
        box-shadow: none;
        outline: none;
    }

    .form-label.fw-semibold {
        font-size: 13px;
        color: #4b5563;
        margin-left: 5px;
    }
</style>
@endsection
@section('content_top')
<div class="card shadow-lg border-0 rounded-4">
    {{-- Header --}}
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-key"></i>
        </div>
        <h5 class="mt-3 mb-1">Informasi Privasi</h5>
        <small class="text-muted">Perbarui Data Password</small>
    </div>

    <div class="card-body p-4">
        <div class="alert_change_password">

        </div>

        <form method="POST" action="{{ url('profile/save_change_password') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-semibold">Password Lama</label>
                <div class="input-group input-group-luxury @error('old_password') border-danger @enderror">
                    <span class="input-group-text">
                        <i class="fa-solid fa-unlock-alt"></i>
                    </span>
                    <input type="password" class="form-control" name="old_password" placeholder="Masukkan password lama" value="{{old('old_password')}}">
                    <button class="toggle-password" type="button" tabindex="-1">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                @error('old_password')
                <div class="text-danger small ms-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password Baru</label>
                <div class="input-group input-group-luxury @error('new_password') border-danger @enderror">
                    <span class="input-group-text">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" name="new_password" value="{{old('new_password')}}" placeholder="Kombinasi huruf & angka (min. 8)">
                    <button class="toggle-password" type="button" tabindex="-1">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                @error('new_password')
                <div class="text-danger small ms-2">{{ $message }}</div>
                @enderror
            </div>

            <div class=" mb-5">
                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                <div class="input-group input-group-luxury">
                    <span class="input-group-text">
                        <i class="fa-solid fa-check-double"></i>
                    </span>
                    <input type="password" class="form-control" name="new_password_confirmation" placeholder="Ulangi password baru">
                    <button class="toggle-password" type="button" tabindex="-1">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <div class="row text-center mt-2">
                <div class="col-12">
                    <a href="{{ url('profile/detail_account/' . $menu_id) }}" type="button"
                        class="btn btn-sm btn-outline-secondary btn-rounded px-4 py-2 me-2">
                        <i class="fa-solid fa-angle-left me-1"></i> KEMBALI
                    </a>
                    <button type="submit" class="btn btn-sm btn-primary btn-rounded px-4 py-2" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); border:none;">
                        <i class="fa-regular fa-floppy-disk me-1"></i> SIMPAN
                    </button>
                </div>
            </div>
        </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    var errors = '{{$errors->any()}}';
    if (errors) {
        let errorString = '{!! json_encode($errors->all()) !!}';

        // 2. Ubah string tersebut menjadi Array sungguhan
        let errorArray = JSON.parse(errorString);
        // console.log(errorArray);
        let errorList = '<ul style="text-align: left; margin-bottom: 0; color: #ef4444; font-size: 14px;">';
        errorArray.forEach(function(error) {
            errorList += '<li>' + error + '</li>';
        })
        errorList += '</ul>';

        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            html: errorList,
            background: '#ffffff',
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'Perbaiki',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0'
            }
        });
    }
    $('.toggle-password').on('click', function() {
        // Cari input yang posisinya bersebelahan dengan tombol yang diklik
        let input = $(this).siblings('input');
        let icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash text-primary'); // Ubah ikon ke mata dicoret
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash text-primary').addClass('fa-eye'); // Kembalikan ikon mata normal
        }
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
</script>
<script>
    $(document).ready(function() {

        function password_show_hide() {
            var x = document.getElementById("password");
            var show_eye = document.getElementById("show_eye");
            var hide_eye = document.getElementById("hide_eye");
            hide_eye.classList.remove("d-none");
            if (x.type === "password") {
                x.type = "text";
                show_eye.style.display = "none";
                hide_eye.style.display = "block";
            } else {
                x.type = "password";
                show_eye.style.display = "block";
                hide_eye.style.display = "none";

            }
        }
        var session_success = "{{ session('password_update_success') }}";
        var session_error = "{{ session('password_update_error') }}";
        if (session_success) {
            $('.alert_change_password').stop(true, true).hide().html(
                    '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                    '<i class="fa-regular fa-circle-check"></i>' +
                    '<strong>Sukses</strong>&nbsp;' + session_success +
                    '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>')
                .fadeIn();
            setTimeout(function() {
                $('.alert_change_password').fadeOut('slow');
            }, 7000);
        } else if (session_error) {
            $('.alert_change_password').stop(true, true).hide().html(
                    '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                    '<i class="fa-solid fa-circle-exclamation"></i>&nbsp;' +
                    '<strong>Error!</strong>&nbsp;' + session_error +
                    '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>')
                .fadeIn();
            setTimeout(function() {
                $('.alert_change_password').fadeOut('slow');
            }, 7000);

        }
    });
</script>
@endsection