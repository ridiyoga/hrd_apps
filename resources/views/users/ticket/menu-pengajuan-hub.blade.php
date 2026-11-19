@extends('users.layouts.main')
@section('title') APPS | Menu Pengajuan @endsection

@section('css')
<style>
    .hub-header {
        padding: 24px 20px 14px;
        position: relative;
    }

    .hub-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        box-shadow: 0 4px 18px rgba(155, 109, 255, 0.45);
    }

    .header-back-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: #f3e8ff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        font-size: 13px;
        text-decoration: none;
        transition: background .2s, color .2s;
        z-index: 10;
    }

    .header-back-btn:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    .menu-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 0 16px 24px;
    }

    @media (min-width: 576px) {
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }

    .menu-tile {
        background: #ffffff;
        border: 1px solid #ede9fe;
        border-radius: 14px;
        padding: 12px 14px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all .2s ease;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.04);
        position: relative;
        overflow: hidden;
    }

    .menu-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(124, 58, 237, 0.12);
        border-color: #c4b5fd;
        text-decoration: none;
    }

    .menu-tile:active {
        transform: scale(.98);
    }

    .tile-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 12px;
        font-size: 17px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform .2s ease;
    }

    .menu-tile:hover .tile-icon {
        transform: scale(1.05);
    }

    .tile-name {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.35;
        flex: 1;
        min-width: 0;
    }

    .tile-arrow {
        width: 26px;
        height: 26px;
        min-width: 26px;
        border-radius: 50%;
        background: #f8fafc;
        color: #94a3b8;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all .2s ease;
    }

    .menu-tile:hover .tile-arrow {
        background: #7c3aed;
        color: #ffffff;
        transform: translateX(2px);
    }

    /* Warna icon per menu */
    .ic-purple {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
    }

    .ic-blue {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        color: #2563eb;
    }

    .ic-green {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #059669;
    }

    .ic-amber {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        color: #d97706;
    }

    .ic-red {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        color: #dc2626;
    }

    .ic-teal {
        background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
        color: #0d9488;
    }
</style>
@endsection

@section('content_top')
<div class="card position-relative" style="padding-bottom: 8px;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="hub-header text-center">
        <div class="hub-icon">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <h5 class="mt-2 mb-1" style="color:#7c3aed; font-size:17px; font-weight:700;">Menu Pengajuan</h5>
        <small class="text-muted" style="font-size:12px;">Pilih jenis pengajuan yang ingin dibuat</small>
    </div>
    <div class="menu-grid">
    @forelse($hubMenus as $menu)
        <a href="{{ $menu->resolved_url }}" class="menu-tile">
            <div class="tile-icon {{ $menu->color_class }}">
                <i class="{{ $menu->icon }}"></i>
            </div>
            <div class="tile-name text-truncate">
                {{ $menu->nama_menu }}
            </div>
            <div class="tile-arrow">
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        </a>
    @empty
        <div class="text-center text-muted py-4" style="grid-column: 1 / -1;">
            <i class="fa-solid fa-folder-open mb-2"
               style="font-size:24px; color:#c4b5fd;"></i>
            <p class="mb-0" style="font-size:12px;">
                Belum ada menu pengajuan yang tersedia untuk Anda.
            </p>
        </div>
    @endforelse
</div>
@endsection

@section('content')
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();"
                class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2"
                data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection