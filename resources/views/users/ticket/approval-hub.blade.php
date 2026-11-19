@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - Menu Approval
@endsection

@section('css')
<style>
    .approval-hero {
        padding: 24px 20px 14px;
        text-align: center;
        position: relative;
    }

    .approval-icon {
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

    .approval-menu-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 0 16px 24px;
    }

    @media (min-width: 576px) {
        .approval-menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }

    /* ── Compact Menu Card ── */
    .approval-menu-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid #ede9fe;
        border-radius: 14px;
        padding: 12px 14px;
        text-decoration: none;
        color: inherit;
        transition: all .2s ease;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.04);
        position: relative;
        overflow: hidden;
    }

    .approval-menu-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(124, 58, 237, 0.12);
        border-color: #c4b5fd;
        color: inherit;
        text-decoration: none;
    }

    .approval-menu-card:active {
        transform: scale(.98);
    }

    .amc-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
        transition: transform .2s ease;
    }

    .approval-menu-card:hover .amc-icon {
        transform: scale(1.05);
    }

    .amc-purple {
        background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
        color: #7c3aed;
    }

    .amc-green {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #059669;
    }

    .amc-blue {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        color: #2563eb;
    }

    .amc-teal {
        background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
        color: #0d9488;
    }

    .amc-title {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.35;
        flex: 1;
        min-width: 0;
    }

    .amc-chevron {
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

    .approval-menu-card:hover .amc-chevron {
        background: #7c3aed;
        color: #ffffff;
        transform: translateX(2px);
    }
</style>
@endsection

@section('content_top')
<div class="card position-relative" style="padding-bottom: 8px;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="approval-hero">
        <div class="approval-icon">
            <i class="fa-solid fa-check-to-slot"></i>
        </div>
        <h5 class="mt-2 mb-1" style="color:#7c3aed; font-size:17px; font-weight:700;">Menu Approval</h5>
        <small class="text-muted" style="font-size:12px;">Pilih jenis persetujuan yang akan diproses</small>
    </div>
    <div class="approval-menu-grid">
        <a href="{{ route('approval.ticket', $menu_id) }}" class="approval-menu-card">
            <div class="amc-icon amc-purple">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div class="amc-title text-truncate">Approval Ubah/Hapus</div>
            <div class="amc-chevron"><i class="fa-solid fa-chevron-right"></i></div>
        </a>
        {{-- <a href="{{ route('approval.ticket.vendor.index', $menu_id) }}" class="approval-menu-card">
            <div class="amc-icon amc-purple">
                <i class="fa-solid fa-store"></i>
            </div>
            <div class="amc-title text-truncate">Approval Vendor</div>
            <div class="amc-chevron"><i class="fa-solid fa-chevron-right"></i></div>
        </a> --}}
        @if($is_validator)
        <a href="{{ route('approval.ticket.validator.index', $menu_id) }}" class="approval-menu-card">
            <div class="amc-icon amc-green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="amc-title text-truncate">Validasi Ubah/Hapus</div>
            <div class="amc-chevron"><i class="fa-solid fa-chevron-right"></i></div>
        </a>
        @endif
        @if($is_assignor)
        <a href="{{ route('approval.ticket.assign.index', $menu_id) }}" class="approval-menu-card">
            <div class="amc-icon amc-blue">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="amc-title text-truncate">Assign Ubah/Hapus</div>
            <div class="amc-chevron"><i class="fa-solid fa-chevron-right"></i></div>
        </a>
        @endif
        @if($is_pic)
        <a href="{{ route('approval.ticket.pic.index', $menu_id) }}" class="approval-menu-card">
            <div class="amc-icon amc-teal">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div class="amc-title text-truncate">Pengerjaan Ticket (PIC)</div>
            <div class="amc-chevron"><i class="fa-solid fa-chevron-right"></i></div>
        </a>
        @endif
    </div>
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