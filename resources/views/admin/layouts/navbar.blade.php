<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 d-xl-none">
        <a class="nav-item nav-link px-0" href="javascript:void(0)" title="Buka menu" aria-label="Buka menu">
            <i class="mdi mdi-menu mdi-24px"></i>
        </a>
    </div>

    <div class="flex-grow-1">
        <div class="row navbar-content-row">
            <div class="col-lg-3 navbar-balance-spacer">
            </div>
            <div class="col-lg-6 navbar-clock">
                <button type="button" class="btn btn-outline-primary waves-effect">
                    <span class="tf-icons mdi mdi-calendar-clock-outline me-1"></span>
                    <span class="text-center"
                        style="font-size: 13pt; text-align: center;">{{ \Carbon\Carbon::now()->isoFormat('DD MMMM YYYY') }}&nbsp;&nbsp;</span><br>
                    <span class="text-center" style="font-size: 13pt; text-align: center;" id="jam_sekarang"></span>
                </button>
            </div>
            <div class="col-lg-3 navbar-actions">
                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <!-- Search -->
                    <script>
                        setInterval(customClock, 500);

                        function customClock() {
                            var time = new Date();
                            var hrs = (time.getHours() < 10 ? '0' : '') + time.getHours();
                            var min = (time.getMinutes() < 10 ? '0' : '') + time.getMinutes();
                            var sec = (time.getSeconds() < 10 ? '0' : '') + time.getSeconds();
                            document.getElementById('jam_sekarang').innerHTML = hrs + ":" + min + ":" + sec;
                        }
                    </script>
                    <!-- /Search -->

                    <ul class="navbar-nav flex-row align-items-center ms-auto">

                        {{-- 🔔 NOTIFICATION BELL --}}
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle hide-arrow p-0 position-relative"
                                href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">


                                <i class="mdi mdi-bell-outline mdi-24px"></i>

                                @if ($unreadNotifCount > 0)
                                <span class="badge badge-notifications badge-unread">
                                    {{ $unreadNotifCount }}
                                </span>
                                @endif
                                {{-- DEBUG --}}
                                {{-- {{ dump($unreadNotifCount) }} --}}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end py-0 mt-3 notif-dropdown">
                                <li class="dropdown-header border-bottom">
                                    <h6 class="mb-0">Notifikasi</h6>
                                </li>

                                <li class="dropdown-notifications-list scrollable-container">
                                    <ul class="list-group list-group-flush">

                                        @forelse ($latestNotifs as $notif)
                                        @php
                                        $data = $notif->data ?? [];
                                        $level = $data['level'] ?? 'warning'; // warning | danger
                                        @endphp

                                        <li
                                            class="list-group-item notif-item {{ $level === 'danger' ? 'notif-urgent' : 'notif-warning' }}">
                                            <div class="d-flex gap-2">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-semibold">
                                                        {{ $data['judul'] ?? 'Notifikasi' }}
                                                    </h6>

                                                    <p class="mb-2 small text-muted text-clamp-3">
                                                        {{ $data['pesan'] ?? '' }}
                                                    </p>

                                                    @if (!empty($data['url']))
                                                    <a href="{{ $data['url'] }}"
                                                        class="btn btn-sm btn-outline-primary notif-action"
                                                        data-id="{{ $notif->id }}"
                                                        data-url="{{ $data['url'] }}">
                                                        Lihat detail
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>

                                        @empty
                                        @if ($latestNotifs->isEmpty())
                                        <li class="notif-empty text-center p-3">
                                            <i class="mdi mdi-shield-check-outline mdi-36px mb-2"></i>
                                            <div class="fw-semibold">Tidak ada notifikasi</div>
                                            <small class="text-muted">Semua notifikasi sudah dibaca</small>
                                        </li>
                                        @endif
                                        @endforelse

                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt
                                        class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                                <li>
                                    <a class="dropdown-item pb-2 mb-1" href="#">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2 pe-1">
                                                <div class="avatar avatar-online">
                                                    <img src="{{ asset('admin/assets/img/avatars/1.png') }}" alt
                                                        class="w-px-40 h-auto rounded-circle" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">
                                                    ADMIN
                                                </h6>
                                                <?php

                                                use Illuminate\Support\Facades\Auth;

                                                $karyawan = \App\Models\User::with('karyawanNIK')->where('id', Auth::user()->id)->first();
                                                ?>
                                                <small class="text-muted">{{ $karyawan->karyawanNIK == null ? '-' : $karyawan->karyawanNIK->name}}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider my-1"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('admin/profile/' . ($holding ? $holding->holding_code : '')) }}">
                                        <i class="mdi mdi-account-outline me-1 mdi-20px"></i>
                                        <span class="align-middle">PROFIL</span>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider my-1"></div>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <a class="dropdown-item" href="route('logout')"
                                            onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                            <i class="mdi mdi-power me-1 mdi-20px"></i>
                                            <span class="align-middle">KELUAR</span>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.notif-action').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const url = this.dataset.url;
                const notifId = this.dataset.id;
                const notifItem = this.closest('.notif-item');

                fetch(`/notifications/${notifId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {

                        /* ✅ 1. HILANGKAN ITEM DARI DROPDOWN */
                        notifItem.remove();

                        /* ✅ 2. UPDATE BADGE */
                        const badge = document.querySelector('.badge-notifications');
                        if (badge) {
                            if (res.unread_count > 0) {
                                badge.textContent = res.unread_count;
                                badge.classList.add('badge-unread');
                            } else {
                                badge.remove();
                            }
                        }

                        /* ✅ 3. TAMPILKAN LOADING */
                        document.getElementById('pageLoading')
                            ?.classList.remove('d-none');

                        /* ✅ 4. REDIRECT (beri jeda kecil agar UX terasa) */
                        setTimeout(() => {
                            window.location.href = url;
                        }, 250);
                    });
            });
        });
    </script>
</nav>
