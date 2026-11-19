@extends('users.layouts.main')
@section('title') APPS | KARYAWAN - SP @endsection
@section('css')
<style>
    .text-purple {
        color: #6f42c1;
    }

    .icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Soft abstract colors */
    .bg-purple-soft {
        background: rgba(111, 66, 193, 0.12);
        color: #6f42c1;
    }

    .bg-indigo-soft {
        background: rgba(102, 16, 242, 0.12);
        color: #6610f2;
    }

    .bg-pink-soft {
        background: rgba(214, 51, 132, 0.12);
        color: #d63384;
    }

    .bg-violet-soft {
        background: rgba(138, 43, 226, 0.12);
        color: #8a2be2;
    }

    .list-group-item {
        border: 0;
        padding-left: 0;
        padding-right: 0;
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
</style>
@endsection
@section('content_top')
<div class="card border-0 shadow-sm rounded-4">

    {{-- Header --}}
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-tie"></i>
        </div>
        <h5 class="mt-3 mb-1">Informasi Jabatan</h5>
        <small class="text-muted">Struktur Jabatan Karyawan</small>
    </div>

    {{-- Body --}}
    <div class="card-body px-4 pb-4">

        <ul class="list-group list-group-flush">

            {{-- Departemen --}}
            <li class="list-group-item d-flex align-items-center gap-3">
                <div class="icon-box bg-purple-soft">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <small class="text-muted">Departemen</small>
                    <div class="fw-semibold">
                        {{ optional($karyawan_jabatan->Departemen)->nama_departemen ?? '-' }}
                    </div>
                </div>
            </li>

            {{-- Divisi --}}
            <li class="list-group-item d-flex align-items-center gap-3">
                <div class="icon-box bg-indigo-soft">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div>
                    <small class="text-muted">Divisi</small>
                    <div class="fw-semibold">
                        {{ optional($karyawan_jabatan->Divisi)->nama_divisi ?? '-' }}
                    </div>
                </div>
            </li>

            {{-- Bagian --}}
            <li class="list-group-item d-flex align-items-center gap-3">
                <div class="icon-box bg-pink-soft">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <small class="text-muted">Bagian</small>
                    <div class="fw-semibold">
                        {{ optional($karyawan_jabatan->Bagian)->nama_bagian ?? '-' }}
                    </div>
                </div>
            </li>

            {{-- Jabatan --}}
            <li class="list-group-item d-flex align-items-center gap-3">
                <div class="icon-box bg-violet-soft">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <small class="text-muted">Jabatan</small>
                    <div class="fw-semibold">
                        {{ optional($karyawan_jabatan->Jabatan)->nama_jabatan ?? '-' }}
                    </div>
                </div>
            </li>

        </ul>

        {{-- Button --}}
        <div class="text-center mt-4">
            <a href="{{ url('profile/'.$menu_id) }}"
                class="btn btn-sm btn-outline-secondary px-4 shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Kembali
            </a>
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
    var status_nomor = "{{old('status_nomor',Auth::user()->status_nomor)}}";
    var status_alamat = "{{old('status_alamat',Auth::user()->status_alamat)}}";
    if (status_nomor == 'ya') {
        $('#content_nomor_wa').hide();
    } else if (status_nomor == 'tidak') {
        $('#content_nomor_wa').show();
    } else {
        $('#content_nomor_wa').hide();
    }
    if (status_alamat == 'ya') {
        $('#content_alamat_domisili').hide();
    } else if (status_alamat == 'tidak') {
        $('#content_alamat_domisili').show();
    } else {
        $('#content_alamat_domisili').hide();
    }
    $(document).on("click", "#status_nomor", function() {
        var isChecked = $(this).is(':checked')
        if (isChecked) {
            $('#content_nomor_wa').hide();
        } else {
            $('#content_nomor_wa').show();
        }
    });
    $(document).on("click", "#status_alamat_ya", function() {
        var isChecked = $(this).is(':checked')
        if (isChecked) {
            $('#content_alamat_domisili').hide();

        }
    });
    $(document).on("click", "#status_alamat_tidak", function() {
        var isChecked = $(this).is(':checked')
        if (isChecked) {
            $('#content_alamat_domisili').show();
        }
    });

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
</script>
@endsection