@extends('users.layouts.main')
@section('title') APPS | KARYAWAN - SP @endsection
@section('css')
<style>
    .coworker-card {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .coworker-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
    }

    .avatar-wrapper {
        width: 52px;
        height: 52px;
        min-width: 52px;
        /* Kunci lebar minimal */
        min-height: 52px;
        /* Kunci tinggi minimal */
        flex-shrink: 0;
        /* Mencegah penciutan dalam flexbox/row */
        display: flex;
        /* Memastikan posisi gambar di tengah */
        align-items: center;
        justify-content: center;
        overflow: hidden;
        /* Memastikan tidak ada bagian gambar keluar */
        border-radius: 50%;
        /* Kontainer juga harus bulat */
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Memotong gambar agar pas tanpa lonjong */
        border-radius: 50%;
        border: 2px solid #e6d9ff;
        display: block;
        /* Menghilangkan whitespace di bawah gambar */
    }

    .icon-box {
        width: 36px;
        height: 36px;
        min-width: 36px;
        /* Kunci lebar minimal */
        min-height: 36px;
        /* Kunci tinggi minimal */
        flex-shrink: 0;
        /* WAJIB: Mencegah icon menjadi lonjong/gepeng */

        border-radius: 50%;
        background: rgba(111, 66, 193, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6f42c1;
        font-size: 14px;

        /* Tambahan agar lebih manis */
        transition: all 0.3s ease;
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

    .nama_rekan {
        font-size: 0.8rem;
    }

    .jabatan_rekan {
        font-size: 0.6rem;
    }
</style>
@endsection
@section('content_top')
<div class="card shadow-sm border-0">

    {{-- Header --}}
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <h5 class="mt-3 mb-1">Informasi Rekan</h5>
        <small class="text-muted">Daftar Rekan Kerja Satu Divisi</small>
    </div>

    <div class="card-body px-3">
        <div class="mb-2">
            <input type="text" id="search_karyawan" class="form-control form-control-sm"
                placeholder="Cari nama karyawan...">
        </div>
        @forelse($rekan_kerja as $a)
        <div class="coworker-card d-flex align-items-center mb-3 karyawan-item" data-search="{{ $a->name }}">

            {{-- AVATAR --}}
            <div class="avatar-wrapper">
                @if($a->foto_karyawan)
                <img src="{{ asset('../storage/foto_karyawan/'.$a->foto_karyawan) }}" alt="Empty">
                @else
                <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt="avatar">
                @endif
            </div>

            {{-- INFO --}}
            <div class="flex-grow-1 ms-3 text-start">
                <h6 class="mb-1 fw-semibold nama_rekan">{{ $a->name }}</h6>

                <div class="text-muted small jabatan_rekan">
                    @foreach($a->KaryawanJabatan as $jabatan)
                    {{ $jabatan->Jabatan->nama_jabatan ?? '-' }}
                    <span class="mx-1">•</span>
                    {{ $jabatan->Divisi->nama_divisi ?? '-' }}
                    @endforeach
                </div>
            </div>

            {{-- ICON --}}
            <div class="icon-box">
                <i class="fa-solid fa-user-group"></i>
            </div>

        </div>
        @empty
        <div class="text-center text-muted py-4">
            <i class="fa-solid fa-user-xmark fa-bounce fa-2x"></i>
            <p class="mb-0">Belum ada data rekan kerja</p>
        </div>
        @endforelse

    </div>

    <div class="card-footer bg-transparent border-0 text-center">
        <a href="{{ url('profile/'.$menu_id) }}" type="button" class="btn btn-sm btn-outline-info btn-sm px-4">
            <i class="fa-solid fa-angle-left me-1"></i>KEMBALI
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
        $('.karyawan-item').each(function() {
            let searchText = $(this).data('search');

            let nameEl = $(this).find('h6')[0]; // nama karyawan
            console.log(searchText, nameEl);

            if (searchText.includes(keyword)) {
                $(this).show();

                // highlight nama
                if (nameEl) highlightText(nameEl, keyword);


            } else {
                $(this).hide();
            }

            // reset highlight jika kosong
            if (keyword === '') {
                if (nameEl) nameEl.innerHTML = nameEl.textContent;

            }
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