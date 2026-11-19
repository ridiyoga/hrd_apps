<div class="menubar-area">
    <div class="toolbar-inner menubar-nav">
        @foreach($all_menu_bar as $menu_bar)
        @if($menu_bar->Menu == NULL)
        @else
        <a href="{{ url($menu_bar->Menu->url.'/'.$menu_bar->Menu->id) }}" class="nav-link btn_klik {{ Request::is(($menu_bar->Menu->url ?? '').'*') ? 'active':'' }}">
            {!!$menu_bar->Menu->icon!!}
        </a>
        @endif
        @endforeach
        <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#off-canvas-all-menu" aria-controls="offcanvasBottom" class="nav-link">
            <i class="fa-solid fa-grip"></i>
        </a>
    </div>
</div>
<div class="offcanvas offcanvas-bottom rounded-top-5" data-bs-keyboard="false" tabindex="-1" data-bs-backdrop="true" id="off-canvas-all-menu">

    <div class="offcanvas-header-custom text-center">
        <h6 class="fw-bold mt-2" style="color: #2d3748;">Semua Layanan</h6>
    </div>

    <div class="offcanvas-body small">
        <div class="mini-menu-grid">
            @php
            $color_classes = ['icon-soft-purple', 'icon-soft-blue', 'icon-soft-green', 'icon-soft-red', 'icon-soft-yellow'];
            @endphp

            @foreach($all_menus as $index => $role_menus)
            @if($role_menus->Menu)
            @php
            // Pilih warna berdasarkan urutan agar tidak terlalu acak berantakan
            $selectedColor = $color_classes[$index % count($color_classes)];
            @endphp
            <a href="{{ url($role_menus->Menu->url.'/'.$role_menus->Menu->id) }}" class="mini-menu-item btn_klik">
                <div class="mini-icon {{ $selectedColor }}">
                    {!! $role_menus->Menu->icon !!}
                </div>
                <span class="mini-label">{{ $role_menus->Menu->name }}</span>
            </a>
            @endif
            @endforeach

            {{-- Logout khusus tetap mencolok --}}
            <a href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_logout" class="mini-menu-item">
                <div class="mini-icon icon-soft-red">
                    <i class="fa-solid fa-power-off"></i>
                </div>
                <span class="mini-label text-danger">Keluar</span>
            </a>
        </div>
    </div>
</div>