@extends('users.layouts.main')
@section('title') APPS | KARYAWAN - SP @endsection
@section('css')
<style>
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

    /* Container Timeline */
    .card-body-wrapper {
        margin-top: -35px;
        /* Pulling content up to overlap header */
        background: #ffffff;
        border-radius: 30px 30px 0 0;
        padding: 25px 20px;
    }

    /* Timeline Styling */
    .dz-timeline {
        list-style: none;
        padding-left: 15px;
        position: relative;
    }

    .dz-timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 2px;
        height: 100%;
        background: #f0f0f0;
    }

    /* Item Kontrak */
    .contract-item-modern {
        position: relative;
        padding-bottom: 30px;
        padding-left: 25px;
    }

    .contract-item-modern::before {
        content: '';
        position: absolute;
        left: -4px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #6366f1;
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        z-index: 2;
    }

    .timeline-card-inner {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease;
    }

    .timeline-card-inner:active {
        transform: scale(0.98);
    }

    /* Status Badges */
    .status-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        text-transform: uppercase;
    }

    .btn-back {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 25px;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .border-dashed {
        border-top-style: dashed !important;
        border-top-width: 1px !important;
        border-color: #e2e8f0 !important;
    }

    /* Mempercantik tampilan mobile agar tidak terlalu rapat */
    @media (max-width: 576px) {
        .timeline-card-inner {
            padding: 12px;
        }
    }
</style>
@endsection
@section('content_top')
<div class="card shadow-lg border-0 rounded-4">
    {{-- Header --}}
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <h5 class="mt-3 mb-1">Kontrak Kerja</h5>
        <small class="text-muted">Timeline Informasi Kontrak Kerja</small>
    </div>
    <div class="card-body p-4">
        @foreach ($karyawan_kontrak as $item)
        <div class="d-flex justify-content-between align-items-start mt-4">
            <div>
                <h6 class="fw-bold mb-0" style="color: #2d3748; font-size: 0.95rem;">
                    Kontrak Kerja
                </h6>
                <span class="text-muted" style="font-size: 0.75rem;">
                    <i class="fa-regular fa-hashtag me-1"></i> No: {{ $item->nomor_kontrak ?? '-' }}
                </span>
            </div>

            {{-- Badge Status Otomatis --}}
            @php
            $is_active = \Carbon\Carbon::parse($item->tanggal_selesai)->isFuture();
            $status_label = $is_active ? 'Aktif' : 'Expired';
            $status_class = $is_active ? 'light badge-success text-success' : 'light badge-danger';
            @endphp
            <span class="badge {{ $status_class }} rounded-pill px-2 py-1" style="font-size: 10px;">
                {{ $status_label }}
            </span>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-6">
                <div class="p-2 rounded-3 border-0" style="background: #f8faff;">
                    <small class="text-muted d-block" style="font-size: 10px;">Mulai</small>
                    <span class="fw-semibold text-dark" style="font-size: 0.85rem;">
                        {{ $item->tanggal_mulai != null ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 rounded-3 border-0" style="background: #fff5f5;">
                    <small class="text-muted d-block" style="font-size: 10px;">Berakhir</small>
                    <span class="fw-semibold text-danger" style="font-size: 0.85rem;">
                        {{ $item->tanggal_selesai != null ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>


        {{-- Tombol Download/Detail --}}
        <div class="mt-3 d-flex gap-2">
            <a href="{{url('profile/kontrak_download/' . $item->id_kontrak)}}" target="_blank" type="button" class="btn btn-sm btn-primary-light flex-grow-1 border-0" {{$is_active ? '' : 'disabled'}} style="border-radius: 8px; font-size: 11px; background: #eef2ff; color: #6366f1;">
                <i class="fa-solid fa-download me-1"></i> File
            </a>
        </div>
        @endforeach
    </div>
    {{-- Button --}}
    <div class="text-center mt-4 mb-3">
        <a href="{{ url('profile/'.$menu_id) }}"
            class="btn btn-sm btn-outline-secondary px-4 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i>
            Kembali
        </a>
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

    function highlightText(element, keyword) {
        let originalText = element.textContent;

        if (!keyword) {
            element.innerHTML = originalText;
            return;
        }

        const regex = new RegExp(`(${keyword})`, 'gi');
        element.innerHTML = originalText.replace(
            regex,
            `<span class="highlight-keyword">$1</span>`
        );
    }
    $(document).on('keyup', '#search_karyawan', function() {
        let keyword = $(this).val().toLowerCase();

        $('.user-item').each(function() {
            let searchText = $(this).data('search');

            let nameEl = $(this).find('strong')[0]; // nama karyawan
            let jabatanEls = $(this).find('small'); // jabatan

            if (searchText.includes(keyword)) {
                $(this).show();

                // highlight nama
                if (nameEl) highlightText(nameEl, keyword);

                // highlight jabatan
                jabatanEls.each(function() {
                    highlightText(this, keyword);
                });

            } else {
                $(this).hide();
            }

            // reset highlight jika kosong
            if (keyword === '') {
                if (nameEl) nameEl.innerHTML = nameEl.textContent;
                jabatanEls.each(function() {
                    this.innerHTML = this.textContent;
                });
            }
        });
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
@endsection