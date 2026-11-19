@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<style>
    .profile-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    @media (max-width: 576px) {
        .profile-card {
            border-radius: 12px;
        }

        .profile-tabs span {
            font-size: 13px;
        }
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

    .coworker-card-4b {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card-5b {
        background: linear-gradient(135deg, #FFE4C4, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card-8b {
        background: linear-gradient(135deg, #7FFFD4, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
    }

    .avatar-wrapper img {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e6d9ff;
    }

    .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(111, 66, 193, .1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6f42c1;
        font-size: 14px;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .shift-legend {
        font-size: clamp(11px, 1.6vw, 14px);
    }

    .shift-legend .dot {
        width: clamp(8px, 1.5vw, 10px);
        height: clamp(8px, 1.5vw, 10px);
        border-radius: 50%;
    }

    .shift-legend p {
        margin: 0;
        line-height: 1.3;
    }
</style>
@endsection
@section('content_top')

<div class="card profile-card border-0">
    {{-- Header --}}
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <h5 class="mt-3 mb-1">Informasi Interview</h5>
        <small class="text-muted">Inverview Departemen Calon Karyawan</small>
    </div>
    <div class="card-body px-3 px-md-4">
        <div class="alert_interview">
            @if (session()->has('simpan_sukses'))
            <div id="alert_interview_success" class="alert alert-success light alert-lg alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                    <line x1="9" y1="9" x2="9.01" y2="9"></line>
                    <line x1="15" y1="9" x2="15.01" y2="9"></line>
                </svg>
                <strong>Success!</strong>&nbsp;Anda Berhasil Menyimpan Data
                <button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            @elseif (session()->has('simpan_error'))
            <div id="alert_interview_error" class="alert alert-danger light alert-lg alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                <strong>Info!</strong>&nbsp;Anda Gagal Menyimpan Data
                <button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            @endif
        </div>
        <ul class="nav profile-tabs" id="profileTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#data-interview">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Interview</span>
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#data-history">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>History</span>
                </button>
            </li>
        </ul>
        <div class="tab-content mt-3">

            {{-- DATA DIRI --}}
            <div class="tab-pane fade show active" id="data-interview">
                @forelse($table as $a)
                <a id="btn_klik" href="{{ url('/interview/detail/' . $menu_id . '/' . $a->id) }}">
                    <div class="coworker-card d-flex align-items-center mb-3">

                        {{-- AVATAR --}}
                        <div class="avatar-wrapper">
                            @if ($a->User != null)
                            @if ($a->User->foto_karyawan)
                            <img src="{{ url_karir() . '/storage/file_pp/' . $a->User->file_pp }}"
                                alt="Empty">
                            @else
                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="avatar">
                            @endif
                            @else
                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="avatar">
                            @endif
                        </div>

                        {{-- INFO --}}
                        <div class="flex-grow-1 ms-3 text-start">
                            <h6 class="mb-1 fw-semibold">{{ $a->Cv->nama_lengkap }}</h6>

                            <div class="text-muted small">
                                {{ $a->Jabatan->nama_jabatan }}
                                <span class="mx-1">•</span>
                                {{ $a->Jabatan->Bagian->Divisi->Departemen->nama_departemen }}
                            </div>
                            <div class="text-muted small">
                                Tanggal Wawancara
                                <span class="mx-1">•</span>
                                {{ $a->tanggal_wawancara_manager }}
                            </div>
                        </div>

                        {{-- ICON --}}
                        <div class="icon-box">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>

                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-user-xmark fa-bounce fa-2x"></i>
                    <p class="mb-0">Belum ada data interview</p>
                </div>
                @endforelse
            </div>
            <div class="tab-pane fade" id="data-history">
                <div class="tab-pane fade show active" id="shift">
                    <div class="row shift-legend mb-3">
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-primary me-2"></span>
                            <p>Belum&nbsp;Menentukan</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-success me-2"></span>
                            <p>Lolos&nbsp;Karyawan</p>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <span class="dot bg-warning me-2"></span>
                            <p>Ditolak&nbsp;Manager</p>
                        </div>
                    </div>
                </div>
                @forelse($history as $a)
                <a id="btn_klik" href="{{ url('/interview/detail/' . $menu_id . '/' . $a->id) }}">

                    <div class="coworker-card-{{ $a->status_lanjutan }} d-flex align-items-center mb-3">

                        {{-- AVATAR --}}
                        <div class="avatar-wrapper">
                            @if ($a->User != null)
                            @if ($a->User->foto_karyawan)
                            <img src="{{ url_karir() . '/storage/file_pp/' . $a->User->file_pp }}"
                                alt="Empty">
                            @else
                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="avatar">
                            @endif
                            @else
                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="avatar">
                            @endif
                        </div>

                        {{-- INFO --}}
                        <div class="flex-grow-1 ms-3 text-start">
                            <h6 class="mb-1 fw-semibold">{{ $a->Cv->nama_lengkap }}</h6>

                            <div class="text-muted small">
                                {{ $a->Jabatan->nama_jabatan }}
                                <span class="mx-1">•</span>
                                {{ $a->Jabatan->Bagian->Divisi->Departemen->nama_departemen }}
                            </div>
                            <div class="text-muted small">
                                Tanggal Wawancara
                                <span class="mx-1">•</span>
                                {{ $a->tanggal_wawancara_manager }}
                            </div>
                        </div>

                        {{-- ICON --}}
                        <div class="icon-box">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>

                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-user-xmark fa-bounce fa-2x"></i>
                    <p class="mb-0">Belum ada data History</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
<!-- Categorie End -->
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
<script type="text/javascript">
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    let success = $('#alert_interview_success');
    let error = $('#alert_interview_error');

    if (success.length) {
        setTimeout(function() {
            success.fadeOut(500, function() {
                $(this).alert('close');
            });
        }, 7000); // success 3 detik
    }

    if (error.length) {
        setTimeout(function() {
            error.fadeOut(500, function() {
                $(this).alert('close');
            });
        }, 7000); // error 7 detik
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
</script>
@endsection