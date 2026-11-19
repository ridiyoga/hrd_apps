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

    .form-control {
        border-radius: 12px;
        padding-left: 42px;
        height: 44px;
        border: 1px solid #e5e5e5;
    }

    .form-control:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 .15rem rgba(111, 66, 193, .2);
    }

    .form-input {
        position: relative;
        margin-bottom: 14px;
    }

    .form-input .input-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #6f42c1;
        font-size: 14px;
    }
</style>
@endsection
@section('content_top')
<div class="card shadow-lg border-0 rounded-4">

    {{-- Header --}}
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <h5 class="mt-3 mb-1">Informasi Akun</h5>
        <small class="text-muted">Perbarui Informasi Akun</small>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ url('/profile/update_username') }}">
            @csrf

            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

            <!-- Username -->
            <div class="alert_change_username"></div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                @include('users.profile.form_input', [
                'icon' => 'fa-regular fa-user',
                'type' => 'text',
                'name' => 'username',
                'class' => 'username',
                'label' => 'Masukkan username',
                'value' => old('username', Auth::user()->username),
                ])
            </div>
            <div class="row text-center">
                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-primary btn-rounded"
                        @if (Auth::user()->user_aktif == 'NON AKTIF') disabled @endif>
                        <i class="fa-regular fa-floppy-disk me-1"></i></i> UPDATE
                    </button>

                </div>
            </div>
        </form>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <a href="{{ url('profile/detail_account/change_password/' . $menu_id) }}"
                    class="btn btn-outline-primary btn-sm" type="button">
                    <i class="fa-solid fa-unlock-keyhole me-1"></i>Change</a>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-4">
            <span class="fw-semibold">Status User :</span>
            <span
                class="badge px-3 py-2 ms-2
                                {{ Auth::user()->user_aktif == 'AKTIF' ? 'bg-success' : 'bg-danger' }}">
                {{ Auth::user()->user_aktif }}
            </span>
        </div>

        <!-- Buttons -->

        {{-- <div class="row text-center">
                <div class="col-12">
                    <a href="{{ url('profile/' . $menu_id) }}" class="btn btn-sm btn-outline-secondary btn-rounded"
        type="button">
        <i class="fa-solid fa-angle-left me-1"></i>KEMBALI
        </a>

    </div>
</div> --}}
</form>
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
    $("input[type=text]").keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });
    var session_success = @json(session('profile_update_success'));
    var session_error = @json(session('profile_update_error'));
    // console.log(session_success);
    if (session_success) {
        $('.alert_change_username').stop(true, true).hide().html(
                '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
                '<strong><i class="fa-regular fa-circle-check me-2"></i>Sukses</strong>&nbsp;' + session_success +
                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>')
            .fadeIn();
        setTimeout(function() {
            $('.alert_change_username').fadeOut('slow');
        }, 7000);
    } else if (session_error) {
        $('.alert_change_username').stop(true, true).hide().html(
                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                '<i class="fa-solid fa-circle-exclamation"></i>' +
                '<strong>Error!</strong>&nbsp;' + session_error +
                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>')
            .fadeIn();
        setTimeout(function() {
            $('.alert_change_username').fadeOut('slow');
        }, 7000);

    }
</script>
@endsection