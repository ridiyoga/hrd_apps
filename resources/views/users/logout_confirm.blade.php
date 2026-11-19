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
