@extends('users.layouts.main')
@section('title')
APPS | APPROVAL - Ticket Ubah/Hapus
@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
<style>
    /* =====================
       FUTURISTIC HEADER
    ===================== */
    .futuristic-header {
        padding: 28px 20px 18px;
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

    /* =====================
       BACK BUTTON
    ===================== */
    .header-back-btn {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f3e8ff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        font-size: 14px;
        text-decoration: none;
        transition: background .2s;
        z-index: 10;
    }

    .header-back-btn:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    /* =====================
       SUMMARY STRIP
    ===================== */
    .summary-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 0 16px 16px;
    }

    .sum-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 14px;
        padding: 12px 8px;
        text-align: center;
    }

    .sum-card .sum-val {
        font-size: 22px;
        font-weight: 800;
        line-height: 1;
        color: #6d28d9;
    }

    .sum-card .sum-val.green {
        color: #16a34a;
    }

    .sum-card .sum-val.red {
        color: #dc2626;
    }

    .sum-card .sum-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: .4px;
        margin-top: 4px;
    }

    /* =====================
       FILTER TABS
    ===================== */
    .filter-tabs {
        display: flex;
        gap: 8px;
        padding: 0 16px 12px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .filter-tabs::-webkit-scrollbar {
        display: none;
    }

    .filter-tab {
        flex-shrink: 0;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        border: 1.5px solid #ede9fe;
        background: #fff;
        color: #7c3aed;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(124, 58, 237, .3);
    }

    /* =====================
       SEARCH BAR
    ===================== */
    .search-wrap {
        position: relative;
        margin: 0 16px 14px;
    }

    .search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a78bfa;
        font-size: 13px;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        border: 1.5px solid #ede9fe;
        border-radius: 12px;
        padding: 10px 14px 10px 40px;
        font-size: 13px;
        background: #fdfcff;
        color: #374151;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .search-input:focus {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .12);
    }

    .search-input::placeholder {
        color: #c4b5fd;
    }

    /* =====================
       TICKET CARDS
    ===================== */
    .ticket-list {
        padding: 0 16px 80px;
    }

    .ticket-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 14px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: box-shadow .2s, transform .15s, border-color .2s;
        text-decoration: none;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .ticket-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-radius: 4px 0 0 4px;
    }

    .ticket-card:hover {
        box-shadow: 0 8px 24px rgba(124, 58, 237, .14);
        transform: translateY(-2px);
        border-color: #c4b5fd;
        text-decoration: none;
    }

    .ticket-card:active {
        transform: scale(.98);
    }

    .ticket-card.status-approved::before {
        background: linear-gradient(135deg, #16a34a, #22c55e);
    }

    .ticket-card.status-rejected::before {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }

    .ticket-card.status-process::before {
        background: linear-gradient(135deg, #2563eb, #60a5fa);
    }

    .tc-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .tc-nomor {
        font-size: 12px;
        font-weight: 800;
        color: #6b21a8;
        line-height: 1.3;
        flex: 1;
        word-break: break-word;
    }

    .tc-status-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-pending, .badge-waiting {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-approved, .badge-done {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .badge-rejected {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }

    .badge-process, .badge-ready {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .badge-draft {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    .badge-feedback {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    /* Environment Badges */
    .badge-env-epicor {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
    .badge-env-accurate-sp {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }
    .badge-env-accurate-aja {
        background: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
    }

    /* Perspektif Checker: lintas environment vs satu sistem */
    .badge-scope-cross {
        background: #ecfeff;
        color: #0e7490;
        border: 1px solid #a5f3fc;
    }
    .tc-scope-row {
        margin: 8px 0 12px;   /* atas: jarak dari baris nomor/status, bawah: jarak ke baris meta */
        max-width: 100%;
    }
    .tc-scope-row .badge {
        max-width: 100%;
        white-space: normal;
        text-align: left;
        line-height: 1.3;
    }
    .badge-scope-same {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .tc-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .tc-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: #6b7280;
        background: #f9fafb;
        border: 1px solid #f0e8ff;
        border-radius: 8px;
        padding: 3px 8px;
    }

    .tc-meta-item i {
        color: #a78bfa;
        font-size: 10px;
    }

    .tc-requester {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tc-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #7c3aed;
        flex-shrink: 0;
    }

    .tc-name {
        font-size: 11px;
        font-weight: 600;
        color: #374151;
    }

    .tc-date {
        font-size: 10px;
        color: #9ca3af;
    }

    .tc-arrow {
        position: absolute;
        right: 14px;
        bottom: 50%;
        transform: translateY(50%);
        color: #d8b4fe;
        font-size: 12px;
    }

    /* =====================
       URGENT BADGE
    ===================== */
    .urgent-dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .2);
        animation: urgentPulse 1.5s infinite;
    }

    @keyframes urgentPulse {

        0%,
        100% {
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .2);
        }

        50% {
            box-shadow: 0 0 0 6px rgba(245, 158, 11, .05);
        }
    }

    /* =====================
       EMPTY STATE
    ===================== */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #c4b5fd;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        display: block;
    }

    .empty-state p {
        font-size: 13px;
        color: #9ca3af;
        margin: 0;
    }

    .empty-state small {
        font-size: 11px;
        color: #d1d5db;
    }

    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
        width: 100%;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5
        }

        50% {
            opacity: 1
        }
    }

    .skeleton-card {
        background: #fff;
        border: 1.5px solid #ede9fe;
        border-radius: 16px;
        padding: 14px;
        margin-bottom: 10px;
    }

    .fab-refresh {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        color: #fff;
        border: none;
        font-size: 16px;
        box-shadow: 0 6px 18px rgba(124, 58, 237, .4);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 20;
        transition: transform .2s;
    }

    .fab-refresh:active {
        transform: scale(.92) rotate(180deg);
    }

    #offcanvas_logout {
        z-index: 999999 !important;
    }
</style>
@endsection
@section('content_top')
<div class="card" style="position:relative; padding-bottom: 16px;">
    <a href="{{ url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-clipboard-check"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color:#623acf; font-size:20px;">Approval Ticket</h5>
        <small class="text-muted">Ubah/Hapus Database</small>
    </div>
    <div class="summary-strip">
        <div class="sum-card">
            <div class="sum-val" id="sum-pending">
                <span class="skeleton" style="height:22px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Menunggu</div>
        </div>
        <div class="sum-card">
            <div class="sum-val green" id="sum-approved">
                <span class="skeleton" style="height:22px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Disetujui</div>
        </div>
        <div class="sum-card">
            <div class="sum-val red" id="sum-rejected">
                <span class="skeleton" style="height:22px; width:32px; display:inline-block;"></span>
            </div>
            <div class="sum-label">Ditolak</div>
        </div>
    </div>
    <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" class="search-input" id="search-input"
            placeholder="Cari nomor ticket, pengaju...">
    </div>
    <div class="filter-tabs" id="filter-tabs">
        <div class="filter-tab" data-status="all">Semua</div>
        <div class="filter-tab active" data-status="0">
            <i class="fa-solid fa-clock me-1"></i>Menunggu
        </div>
        <div class="filter-tab" data-status="1">
            <i class="fa-solid fa-circle-check me-1"></i>Disetujui
        </div>
        <div class="filter-tab" data-status="2">
            <i class="fa-solid fa-circle-xmark me-1"></i>Ditolak
        </div>
    </div>
    <div class="ticket-list" id="ticket-list">
        @for($sk = 0; $sk < 4; $sk++)
            <div class="skeleton-card">
                <div class="d-flex justify-content-between mb-2">
                    <span class="skeleton" style="height:14px; width:60%; display:inline-block;"></span>
                    <span class="skeleton" style="height:14px; width:20%; display:inline-block;"></span>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <span class="skeleton" style="height:22px; width:90px; display:inline-block; border-radius:8px;"></span>
                    <span class="skeleton" style="height:22px; width:80px; display:inline-block; border-radius:8px;"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="skeleton" style="height:28px; width:28px; border-radius:50%; display:inline-block;"></span>
                    <span class="skeleton" style="height:12px; width:40%; display:inline-block;"></span>
                </div>
            </div>
        @endfor
    </div>
</div>
</div>
<button class="fab-refresh" id="btn-refresh" title="Refresh">
    <i class="fa-solid fa-rotate-right"></i>
</button>
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
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });
    $(document).ready(function() {
        const MENU_ID = '{{ $menu_id }}';
        const APPROVE_URL = "{{ url('approval/ticket/approve') }}";
        let allTickets = [];
        let activeFilter = '0';
        let searchQuery = '';
        const statusCfg = {
            0: {
                label: 'Menunggu',
                badgeClass: 'badge-pending',
                icon: 'fa-clock',
                cardClass: ''
            },
            1: {
                label: 'Disetujui',
                badgeClass: 'badge-approved',
                icon: 'fa-circle-check',
                cardClass: 'status-approved'
            },
            2: {
                label: 'Ditolak',
                badgeClass: 'badge-rejected',
                icon: 'fa-ban',
                cardClass: 'status-rejected'
            },
            3: {
                label: 'Diproses',
                badgeClass: 'badge-process',
                icon: 'fa-spinner',
                cardClass: 'status-process'
            },
        };
        function loadTickets() {
            $('#ticket-list').html(skeletonHtml());
            $.ajax({
                url: "{{ url('approval/ticket/get-list') }}" + '/' + MENU_ID,
                type: 'GET',
                success: function(res) {
                    if (res.code == 200) {
                        allTickets = res.tickets ?? [];
                        updateSummary(allTickets);
                        renderList();
                    } else {
                        showEmpty('Tidak ada data tersedia.');
                    }
                },
                error: function(xhr) {
                    showEmpty(window.getFriendlyErrorMessage(xhr, 'Gagal memuat data persetujuan. Silakan coba lagi.'));
                }
            });
        }
        function updateSummary(tickets) {
            $('#sum-pending').text(tickets.filter(t => t.status == 0).length);
            $('#sum-approved').text(tickets.filter(t => t.status == 1).length);
            $('#sum-rejected').text(tickets.filter(t => t.status == 2).length);
        }
        function getEnvBadge(kategori, name, nomor) {
            let kat = Number(kategori);
            if (!kat || isNaN(kat) || kat === 0) {
                if (nomor && nomor.includes('AOL-AJA')) kat = 3;
                else if (nomor && (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS'))) kat = 2;
                else if (nomor && (nomor.includes('EPIC-SPS') || nomor.includes('EPIC'))) kat = 1;
                else kat = 1;
            }
            if (nomor && nomor.includes('AOL-AJA')) {
                kat = 3;
            } else if (nomor && (nomor.includes('AOL-SP') || nomor.includes('AOL-SPS'))) {
                kat = 2;
            } else if (nomor && nomor.includes('EPIC')) {
                kat = 1;
            }
            let cls = 'badge-env-epicor';
            let icon = 'fa-database';
            let label = 'Epicor SPS';
            if (kat === 1) {
                cls = 'badge-env-epicor';
                icon = 'fa-database';
                label = 'Epicor SPS';
            } else if (kat === 2) {
                cls = 'badge-env-accurate-sp';
                icon = 'fa-bolt';
                label = 'AOL SP';
            } else if (kat === 3) {
                cls = 'badge-env-accurate-aja';
                icon = 'fa-network-wired';
                label = 'AOL AJA';
            }
            return `<span class="badge ${cls}" style="font-size:10px; font-weight:700; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid ${icon}"></i>${label}</span>`;
        }
        function escHtml(v) {
            return String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }
        // Penanda untuk checker: tugas pemeriksaan ini lintas environment atau satu sistem
        function getCheckerScopeBadge(t) {
            const scope = t.checker_scope;
            if (!scope) return '';
            const base = 'font-size:10px; font-weight:700; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;';
            if (scope === 'same') {
                return `<span class="badge badge-scope-same" style="${base}" title="Pemeriksaan di sistem yang sama dengan tiket"><i class="fa-solid fa-link"></i>Satu Sistem</span>`;
            }
            const targets = (t.checker_cross_targets || []).map(escHtml).join(', ');
            const label = scope === 'mixed' ? 'Lintas Sistem & Sistem yang Sama' : 'Lintas Sistem';
            return `<span class="badge badge-scope-cross" style="${base}" title="Pemeriksaan di environment berbeda (tidak menghambat tiket asal)"><i class="fa-solid fa-arrows-split-up-and-left"></i>${label}${targets ? ' → ' + targets : ''}</span>`;
        }
        function renderList() {
            let filtered = allTickets;
            if (activeFilter !== 'all') {
                filtered = filtered.filter(t => String(t.status) === String(activeFilter));
            }
            if (searchQuery.trim() !== '') {
                const q = searchQuery.toLowerCase();
                filtered = filtered.filter(t =>
                    (t.nomor_ticket || '').toLowerCase().includes(q) ||
                    (t.requestor_name || '').toLowerCase().includes(q) ||
                    (t.menu_name || '').toLowerCase().includes(q) ||
                    (t.submenu_name || '').toLowerCase().includes(q)
                );
            }
            if (filtered.length === 0) {
                showEmpty(
                    activeFilter === '0' ?
                    'Tidak ada ticket yang menunggu persetujuan.' :
                    'Tidak ada ticket ditemukan.'
                );
                return;
            }
            let html = '';
            filtered.forEach(function(t) {
                const cfg = statusCfg[t.status ?? 0] ?? statusCfg[0];
                const initials = getInitials(t.requestor_name || '-');
                const isUrgent = t.status == 0;
                const daysDiff = t.tanggal_ticket ?
                    daysBetween(t.tanggal_ticket) :
                    null;
                const dateLabel = t.tanggal_ticket ?
                    formatDate(t.tanggal_ticket) :
                    '-';
                const envBadge = getEnvBadge(t.environment_kategori, t.environment_name, t.nomor_ticket);
                html += `
                    <a href="${t.page_url}"
                        class="ticket-card ${cfg.cardClass}"
                        style="padding-right: 36px;">
                        ${isUrgent ? '<div class="urgent-dot"></div>' : ''}
                        <div class="tc-top">
                            <div style="min-width:0; flex:1;">
                                <div class="tc-nomor">${t.nomor_ticket || '-'}</div>
                                <div class="mt-1">${envBadge}</div>
                            </div>
                            <span class="tc-status-badge ${cfg.badgeClass}">
                                <i class="fa-solid ${cfg.icon}"></i> ${t.role}
                            </span>
                        </div>
                        ${getCheckerScopeBadge(t) ? `<div class="tc-scope-row">${getCheckerScopeBadge(t)}</div>` : ''}
                        <div class="tc-meta">
                            ${t.menu_name ? `
                            <span class="tc-meta-item">
                                <i class="fa-solid fa-folder-open"></i>${t.menu_name}
                            </span>` : ''}
                            ${t.submenu_name ? `
                            <span class="tc-meta-item">
                                <i class="fa-solid fa-list-ul"></i>${t.submenu_name}
                            </span>` : ''}
                            ${t.detail_count > 0 ? `
                            <span class="tc-meta-item">
                                <i class="fa-solid fa-table-list"></i>${t.detail_count} detail
                            </span>` : ''}
                            ${daysDiff !== null && t.status == 0 ? `
                            <span class="tc-meta-item" style="color:${daysDiff > 3 ? '#b45309' : '#6b7280'};">
                                <i class="fa-solid fa-hourglass-half" style="color:${daysDiff > 3 ? '#f59e0b' : '#a78bfa'};"></i>
                                ${daysDiff === 0 ? 'Hari ini' : daysDiff + ' hari lalu'}
                            </span>` : ''}
                        </div>
                        ${t.has_failed_generate ? `
                        <div class="tc-meta-item" style="color:#dc2626; margin-top:4px;">
                            <i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;"></i>
                            Ada pengajuan turunan belum berhasil dibuat — buka untuk proses ulang
                        </div>` : ''}
                        <div class="tc-requester">
                            <div class="tc-avatar">${initials}</div>
                            <div>
                                <div class="tc-name">${t.requestor_name || '-'}</div>
                                <div class="tc-date">${dateLabel}</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right tc-arrow"></i>
                    </a>
                `;
            });
            $('#ticket-list').html(html);
        }
        function showEmpty(msg) {
            $('#ticket-list').html(`
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <p>${msg}</p>
                    <small>Coba ubah filter atau refresh halaman</small>
                </div>`);
        }
        function skeletonHtml() {
            let h = '';
            for (let i = 0; i < 4; i++) {
                h += `<div class="skeleton-card">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="skeleton" style="height:14px; width:60%; display:inline-block;"></span>
                        <span class="skeleton" style="height:14px; width:20%; display:inline-block;"></span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="skeleton" style="height:22px; width:90px; display:inline-block; border-radius:8px;"></span>
                        <span class="skeleton" style="height:22px; width:70px; display:inline-block; border-radius:8px;"></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="skeleton" style="height:28px;width:28px;border-radius:50%;display:inline-block;"></span>
                        <span class="skeleton" style="height:12px; width:40%; display:inline-block;"></span>
                    </div>
                </div>`;
            }
            return h;
        }
        function getInitials(name) {
            return name.split(' ').slice(0, 2)
                .map(w => w[0] || '').join('').toUpperCase();
        }
        function formatDate(dateStr) {
            const d = new Date(dateStr);
            if (isNaN(d)) return dateStr;
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
        function daysBetween(dateStr) {
            const d = new Date(dateStr);
            if (isNaN(d)) return null;
            const diff = Date.now() - d.getTime();
            return Math.floor(diff / 86400000);
        }
        $(document).on('click', '.filter-tab', function() {
            $('.filter-tab').removeClass('active');
            $(this).addClass('active');
            activeFilter = $(this).data('status');
            renderList();
        });
        let searchTimer;
        $('#search-input').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                searchQuery = $(this).val();
                renderList();
            }, 300);
        });
        $('#btn-refresh').on('click', function() {
            const icon = $(this).find('i');
            icon.addClass('fa-spin');
            loadTickets();
            setTimeout(() => icon.removeClass('fa-spin'), 1000);
        });
        loadTickets();
        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div>'.repeat(3),
                showCancelButton: false,
                showConfirmButton: false,
            });
        };
    });
</script>
@endsection