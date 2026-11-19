@extends('users.layouts.main')
@section('title')
APPS | Approval Vendor
@endsection

@section('css')
<style>
    .vaq-header {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 20px;
        padding: 20px;
        color: white;
        margin-bottom: 16px;
        position: relative;
    }
    .vaq-back {
        position: absolute; top: 16px; left: 16px;
        width: 34px; height: 34px; border-radius: 10px;
        background: rgba(255,255,255,.18); border: none;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 13px; text-decoration: none;
    }
    .vaq-title { font-size: 20px; font-weight: 700; margin-left: 4px; }
    .vaq-subtitle { font-size: 12px; opacity: .85; margin-left: 4px; }

    .vaq-card {
        background: white; border-radius: 16px; padding: 16px;
        margin-bottom: 12px; border: 1px solid #ede9fe;
    }
    .vaq-name { font-size: 14px; font-weight: 700; color: #111827; }
    .vaq-business { font-size: 12px; font-weight: 600; color: #6d28d9; margin-top: 2px; }
    .vaq-role-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10px; font-weight: 700; padding: 4px 10px;
        border-radius: 20px; background: #fef3c7; color: #b45309;
        margin-top: 8px;
    }
    .vaq-footer {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ede9fe;
    }
    .vaq-pengaju { font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 5px; }
    .vaq-pengaju i { color: #a855f7; font-size: 10px; }
    .btn-detail {
        background: #7c3aed; color: #fff !important; border: none;
        font-weight: 700; font-size: 11px; border-radius: 10px;
        padding: 7px 16px; text-decoration: none;
    }
    .vaq-empty { text-align: center; padding: 40px 20px; color: #a78bfa; }
    .vaq-empty i { font-size: 32px; display: block; margin-bottom: 10px; }
</style>
@endsection

@section('content_top')
<div class="vaq-header">
    <a href="{{ url()->previous() }}" class="vaq-back"><i class="fa-solid fa-arrow-left"></i></a>
    <div class="vaq-title">Approval Vendor</div>
    <div class="vaq-subtitle">Semua ticket vendor yang menunggu tindakan Anda</div>
</div>

<div id="vaq-list"></div>
<div class="vaq-empty" id="vaq-empty" style="display:none;">
    <i class="fa-solid fa-folder-open"></i>
    Tidak ada ticket vendor yang menunggu approval Anda.
</div>
@endsection

@section('content')
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();" class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $.ajax({
            url: '{{ route("approval.ticket.vendor.list", $menu_id) }}',
            type: 'GET',
            success: function (res) {
                const rows = res.data || [];
                if (rows.length === 0) {
                    $('#vaq-empty').show();
                    return;
                }
                const $wrap = $('#vaq-list');
                rows.forEach(function (v) {
                    $wrap.append(`
                        <div class="vaq-card">
                            <div class="vaq-name">${v.nomor_pengajuan_fis}</div>
                            <div class="vaq-business">${v.data?.nama_usaha ?? '-'}</div>
                            <span class="vaq-role-badge"><i class="fa-solid fa-user-check"></i> ${v.role_label}</span>
                            <div class="vaq-footer">
                                <div class="vaq-pengaju"><i class="fa-solid fa-user"></i> ${v.creator_name}</div>
                                <a href="{{ url('approval/ticket/vendor/'.$menu_id) }}/${v.id}" class="btn-detail">
                                    Proses <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
                                </a>
                            </div>
                        </div>
                    `);
                });
            },
            error: function (xhr) {
                const errMsg = window.getFriendlyErrorMessage(xhr, 'Gagal memuat daftar approval.');
                $('#vaq-empty').show().find('i').removeClass('fa-folder-open').addClass('fa-triangle-exclamation');
                $('#vaq-empty').append(`<div style="margin-top:6px;">${errMsg}</div>`);
            }
        });
    });
</script>
@endsection