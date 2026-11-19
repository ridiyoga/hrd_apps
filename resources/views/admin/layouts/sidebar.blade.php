@php
    $isSidebarPathActive = static function ($url) {
        $path = trim((string) $url, '/');

        return $path !== '' && (request()->is($path) || request()->is($path . '/*'));
    };

    $isSidebarItemActive = function ($item) use (&$isSidebarItemActive, $isSidebarPathActive) {
        if ($isSidebarPathActive(data_get($item, 'url'))) {
            return true;
        }

        foreach (['children', 'subchildren'] as $relation) {
            if (!method_exists($item, 'relationLoaded') || !$item->relationLoaded($relation)) {
                continue;
            }

            foreach ($item->getRelation($relation) as $descendant) {
                if ($isSidebarItemActive($descendant)) {
                    return true;
                }
            }
        }

        return false;
    };

    $projectManagementSidebarCounts = function () use ($holding) {
        static $counts = null;

        if ($counts !== null) {
            return $counts;
        }

        $counts = [
            'my_tasks' => 0,
            'review_tasks' => 0,
            'blocked_tasks' => 0,
        ];

        if (!auth()->check() || !is_object($holding) || empty($holding->id)) {
            return $counts;
        }

        $userId = auth()->id();
        $baseQuery = \App\Models\ProjectManagementTask::query()
            ->whereHas('project', fn($query) => $query->where('holding_id', $holding->id));

        $counts['my_tasks'] = (clone $baseQuery)
            ->where('primary_assignee_id', $userId)
            ->whereNotIn('status', ['done', 'cancelled'])
            ->count();

        $counts['review_tasks'] = (clone $baseQuery)
            ->where('reviewer_id', $userId)
            ->where('status', 'review')
            ->count();

        $counts['blocked_tasks'] = (clone $baseQuery)
            ->where('status', 'blocked')
            ->whereHas('project', fn($query) => $query->where('project_manager_id', $userId))
            ->count();

        return $counts;
    };

    $dokumenLegalSidebarActionCount = function () use ($holding) {
        static $count = null;

        if ($count !== null) {
            return $count;
        }

        if (!auth()->check() || !is_object($holding) || empty($holding->id)) {
            return $count = 0;
        }

        $today = \Carbon\Carbon::today();

        return $count = \App\Models\DokumenLegal::query()
            ->where('holding_id', $holding->id)
            ->where(function ($query) use ($today) {
                $query->where('status', 'expired')
                    ->orWhere(function ($query) use ($today) {
                        $query->where('status', 'aktif')
                            ->whereDate('tanggal_akhir', '<', $today);
                    });
            })
            ->count();
    };

    $sidebarOwnBadgeCount = function ($item) use ($projectManagementSidebarCounts, $dokumenLegalSidebarActionCount) {
        $url = trim((string) data_get($item, 'url'), '/');
        $name = strtolower((string) data_get($item, 'name'));
        $counts = $projectManagementSidebarCounts();

        if (str_contains($url, 'project_management/my-tasks') || str_contains($name, 'task saya')) {
            return $counts['my_tasks'];
        }

        if (str_contains($url, 'project_management/review-tasks') || str_contains($name, 'review task')) {
            return $counts['review_tasks'];
        }

        if (str_contains($url, 'project_management/blocked-tasks') || str_contains($name, 'blocked task')) {
            return $counts['blocked_tasks'];
        }

        if ($url === 'dokumen_legal/index') {
            return $dokumenLegalSidebarActionCount();
        }

        return 0;
    };

    $sidebarBadgeCount = function ($item) use (&$sidebarBadgeCount, $sidebarOwnBadgeCount) {
        $count = $sidebarOwnBadgeCount($item);

        foreach (['children', 'subchildren'] as $relation) {
            if (!method_exists($item, 'relationLoaded') || !$item->relationLoaded($relation)) {
                continue;
            }

            foreach ($item->getRelation($relation) as $descendant) {
                $count += $sidebarBadgeCount($descendant);
            }
        }

        return $count;
    };

    $renderSidebarBadge = static function (int $count) {
        if ($count <= 0) {
            return '';
        }

        $label = $count > 99 ? '99+' : (string) $count;

        return '<span class="badge bg-danger rounded-pill ms-auto sidebar-count-badge">' . e($label) . '</span>';
    };
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('dashboard/holding') }}" class="app-brand-link">
            <span class="app-brand-logo demo me-1">
                <span style="color: var(--bs-primary)">
                    <img src="{{ asset('storage/holding_image/' . $holding->holding_image) }}" width="50">
                </span>
            </span>
            <span
                class="app-brand-text demo menu-text fw-semibold ms-2">{{ $holding == '' ? '' : $holding->holding_name_hint }}</span>
        </a>

    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item
        {{ Request::is('dashboard*') ? 'active' : '' }}">
            <a href="{{ url('/dashboard/option/' . $holding->holding_code) }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Dashboards">Dashboards</div>
            </a>
        </li>
        <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text">MAIN MENU</span>
        </li>
        @foreach ($menus as $menu)
        @if ($menu->children->count() > 0)
        <li class="menu-item {{ $isSidebarItemActive($menu) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons mdi {{ $menu->icon }}"></i>
                <div style="font-size: 10pt;">&nbsp;{{ $menu->name }}</div>
                {!! $renderSidebarBadge($sidebarBadgeCount($menu)) !!}
            </a>

            <ul class="menu-sub">
                @foreach ($menu->children as $child)
                <li class="menu-item {{ $isSidebarItemActive($child) ? 'active open' : '' }} ">
                    {!! $isSidebarPathActive($child->url ?? null) ? '<input type="hidden" id="asoy" value="' . $child->id . '">' : '' !!}
                    @if ($child->subchildren->count() > 0)
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons mdi {{ $child->icon }}"></i>
                        <div style="font-size: 10pt;">&nbsp;{{ $child->name }}</div>
                        {!! $renderSidebarBadge($sidebarBadgeCount($child)) !!}
                    </a>

                    <ul class="menu-sub">
                        @foreach ($child->subchildren as $subchild)
                        <li class="menu-item {{ $isSidebarItemActive($subchild) ? 'active open' : '' }}">
                            {!! $isSidebarPathActive($subchild->url ?? null)
                            ? '<input type="hidden" id="asoy_sub" value="' . $subchild->id . '">'
                            : '' !!}
                            @if ($subchild->subchildren->count() > 0)
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons mdi {{ $subchild->icon }}"></i>
                                <div style="font-size: 10pt;">&nbsp;{{ $subchild->name }}</div>
                                {!! $renderSidebarBadge($sidebarBadgeCount($subchild)) !!}
                            </a>
                            <ul class="menu-sub">
                                @foreach ($subchild->subchildren as $subchild2)
                                <li
                                    class="menu-item {{ $isSidebarItemActive($subchild2) ? 'active open' : '' }}">
                                    {!! $isSidebarPathActive($subchild2->url ?? null)
                                    ? '<input type="hidden" id="asoy_sub2" value="' . $subchild2->id . '">'
                                    : '' !!}
                                    <a href="{{ $subchild2->url ? url($subchild2->url, $holding->holding_code) : '#' }}"
                                        class="menu-link">
                                        <i
                                            class="menu-icon tf-icons mdi {{ $subchild2->icon }}"></i>
                                        <div style="font-size: 10pt;">
                                            &nbsp;{{ $subchild2->name }}</div>
                                        {!! $renderSidebarBadge($sidebarBadgeCount($subchild2)) !!}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <a href="{{ $subchild->url ? url($subchild->url, $holding->holding_code) : '#' }}"
                                class="menu-link">
                                <i class="menu-icon tf-icons mdi {{ $subchild->icon }}"></i>
                                <div style="font-size: 10pt;">&nbsp;{{ $subchild->name }}</div>
                                {!! $renderSidebarBadge($sidebarBadgeCount($subchild)) !!}
                            </a>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <a href="{{ $child->url ? url($child->url, $holding->holding_code) : '#' }}"
                        class="menu-link" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="{{ $child->name }}">
                        <i class="menu-icon tf-icons mdi {{ $child->icon }}"></i>
                        <div style="font-size: 10pt;" data-i18n="{{ $child->name }}">
                            &nbsp;{{ $child->name }}
                        </div>
                        {!! $renderSidebarBadge($sidebarBadgeCount($child)) !!}
                    </a>
                    @endif

                </li>
                @endforeach
            </ul>
        </li>
        @else
        <li class="menu-item {{ $isSidebarItemActive($menu) ? 'active' : '' }}">
            {{-- Menu tanpa submenu --}}
            <a href="{{ $menu->url ? url($menu->url, $holding->holding_code) : '#' }}" class="menu-link"
                title="{{ $menu->name }}">
                <i class="menu-icon tf-icons mdi {{ $menu->icon }}"></i>
                <div style="font-size: 10pt;" data-i18n="{{ $menu->name }}">
                    {{ $menu->name }}
                </div>
                {!! $renderSidebarBadge($sidebarBadgeCount($menu)) !!}
            </a>
        </li>
        @endif
        @endforeach
        <!-- ABSENSI KARYAWAN -->


        <!-- <li class="menu-item {{ Request::is('izin*') ? 'active' : '' }}">
            <a href="{{ url('/izin/' . $holding->holding_code) }}" class=" menu-link">
                <i class="menu-icon tf-icons mdi mdi-calendar-filter-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Izin</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('cuti*') ? 'active' : '' }}">
            <a href="{{ url('/cuti/' . $holding->holding_code) }}" class=" menu-link">
                <i class="menu-icon tf-icons mdi mdi-clipboard-text-clock-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Cuti</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('penugasan*') ? 'active' : '' }}">
            <a href="{{ url('/penugasan/' . $holding->holding_code) }}" class=" menu-link">
                <i class="menu-icon tf-icons mdi mdi-airplane-clock"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Perjalanan Dinas</div>
            </a>
        </li>

        <li class="menu-item {{ Request::is('inventaris*') ? 'active' : '' }}">
            <a href="{{ url('/inventaris/' . $holding->holding_code) }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-archive-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Data Inventaris</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('access*') ? 'active' : '' }}">
            <a href="{{ url('/access/' . $holding->holding_code) }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-account-key"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">User Level Access</div>
            </a>
        </li> -->


        <!-- <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text">ABSENSI</span>
        </li> -->
        <!-- Apps -->
        <!-- <li class="menu-item {{ Request::is('hrd/absen*') ? 'active' : '' }}">
            <a href="{{ url('/absen') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Absen</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('hrd/id-card*') ? 'active' : '' }}">
            <a href="{{ url('/id-card') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-card-account-details-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">ID Card</div>
                <div class="badge bg-label-primary fs-tiny rounded-pill ms-auto">1</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('hrd/data-absen*') ? 'active' : '' }}">
            <a href="{{ url('/data-absen') }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-table-account"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Data Absen</div>
                <div class="badge bg-label-primary fs-tiny rounded-pill ms-auto">1</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('hrd/my-absen*') ? 'active' : '' }}">
            <a href="{{ url('/my-absen') }}" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-fingerprint"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">Presensi</div>
                <div class="badge bg-label-primary fs-tiny rounded-pill ms-auto">1</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('hrd/kpi*') ? 'active' : '' }}">
            <a href="{{ url('/kpi') }}" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-chart-line"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">KPI</div>
                <div class="badge bg-label-primary fs-tiny rounded-pill ms-auto">1</div>
            </a>
        </li>
        <li class="menu-item {{ Request::is('hrd/slip*') ? 'active' : '' }}">
            <a href="{{ url('/slip') }}" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-credit-card-outline"></i>
                <div style="font-size: 10pt;" data-i18n="Blank">My Slip</div>
                <div class="badge bg-label-primary fs-tiny rounded-pill ms-auto">1</div>
            </a>
        </li>
        <!-- Components -->
        <!-- <li class="menu-header fw-medium mt-4"><span class="menu-header-text">Activity Logs</span></li> -->
        <!-- Cards -->
        <!-- <li class="menu-item  {{ Request::is('hrd/activity-logs*') ? 'active' : '' }}">
            <a href="{{ url('/activity-logs/' . $holding->holding_code) }}" class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-history"></i>
                <div style="font-size: 10pt;" data-i18n="Basic">Activity Log</div>
            </a>
        </li>  -->
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                    class="menu-link" style="border: none; background: none;">
                    <i class="menu-icon tf-icons mdi mdi-logout"></i>
                    <div style="font-size: 10pt;" data-i18n="Blank">Log Out</div>
                </a>
            </form>
        </li>
    </ul>
</aside>
