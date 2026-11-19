@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection

@section('css')
<style>
    /* =====================
       HEADER CARD
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

    .btn-sort {
        background: #f3e8ff;
        border: 1px solid #e9d5ff;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        color: #7c3aed;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .btn-sort.active {
        background: linear-gradient(135deg, #7c3aed, #a855f7);
        border-color: transparent;
        color: #fff;
    }

    .btn-sort .sort-icon {
        font-size: 9px;
        transition: transform .2s;
    }

    .btn-sort.asc .sort-icon {
        transform: rotate(180deg);
    }

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
    }

    /* =====================
       PROFILE TABS
    ===================== */
    .profile-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    .profile-tabs {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        border-bottom: none;
    }

    .profile-tabs .nav-link {
        border: none;
        border-radius: 14px;
        padding: 8px 12px;
        background: #f3e8ff;
        color: #6b21a8;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        white-space: nowrap;
        transition: 0.3s;
    }

    .profile-tabs .nav-link.active {
        background: linear-gradient(135deg, #a855f7, #7c3aed);
        color: #fff;
        box-shadow: 0 6px 14px rgba(168, 85, 247, 0.4);
    }

    .profile-tabs .nav-link i {
        font-size: 12px;
    }

    @media (max-width: 576px) {
        .profile-tabs span {
            font-size: 10px;
        }
    }

    /* =====================
       TICKET CARDS
    ===================== */
    .coworker-card,
    .coworker-card-history,
    .coworker-card-reject {
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
    }

    .coworker-card {
        background: linear-gradient(135deg, #f5edff, #ffffff);
    }

    .coworker-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
    }

    .coworker-card-history {
        background: linear-gradient(135deg, #cffbd1, #ffffff);
    }

    .coworker-card-reject {
        background: linear-gradient(135deg, #fbcfcf, #ffffff);
    }

    /* Prevent the text column from pushing content out of the card */
    .ticket-card-body {
        min-width: 0;
    }

    .icon-box,
    .icon-box-success,
    .icon-box-reject {
        width: 40px;
        height: 40px;
        min-width: 40px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-box {
        background: rgba(255, 255, 255, .2);
        border-radius: 12px;
        font-size: 20px;
    }

    .icon-box-success {
        background: linear-gradient(135deg, #cffbd1, #ffffff);
        border: 1px solid #8ae68f;
        border-radius: 50%;
    }

    .icon-box-success i {
        color: #2f7e32;
    }

    .icon-box-reject {
        background: linear-gradient(135deg, #fbcfcf, #ffffff);
        border: 1px solid #dc8888;
        border-radius: 50%;
    }

    .icon-box-reject i {
        color: #8b3838;
    }

    @media (max-width: 420px) {

        .coworker-card,
        .coworker-card-history,
        .coworker-card-reject {
            padding: 10px 10px;
        }

        .icon-box,
        .icon-box-success,
        .icon-box-reject {
            width: 32px;
            height: 32px;
            min-width: 32px;
            font-size: 14px;
        }
    }

    /* =====================
       SKELETON
    ===================== */
    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px;
        width: 100%;
        height: 72px;
    }

    @keyframes skPulse {

        0%,
        100% {
            opacity: .5;
        }

        50% {
            opacity: 1;
        }
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

    .stat-box {
        border-radius: 14px;
        padding: 12px 10px;
        text-align: center;
        box-shadow: 0 6px 16px rgba(111, 66, 193, .10);
        background: linear-gradient(135deg, #f5edff, #ffffff);
    }

    .stat-box .stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 6px;
        font-size: 14px;
    }

    .stat-box .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .stat-box .stat-label {
        font-size: 10px;
        color: #6b7280;
        margin-top: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .stat-total .stat-icon {
        background: #ede9fe;
        color: #7c3aed;
    }

    .stat-submitted .stat-icon {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .stat-waiting .stat-icon {
        background: #fef3c7;
        color: #b45309;
    }

    .stat-done .stat-icon {
        background: #dcfce7;
        color: #15803d;
    }
</style>
@endsection

@section('content_top')
<div class="card position-relative">
    <a href="{{ route('ticket.hub', ['menu_id' => $menu_id]) }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-file-lines"></i>
        </div>
        <h5 class="mt-3 mb-1">UBAH/HAPUS</h5>
        <small class="text-muted">Daftar Pengajuan Ubah/Hapus Database</small>
    </div>

    @if (session('hapus_ticket_sukses'))
    <div class="alert alert-success d-flex align-items-center gap-2 rounded-4 shadow-sm border-0 py-3 mx-3 fade show">
        <i class="fa-regular fa-circle-check fs-4 text-success"></i>
        <div>
            <strong style="font-size:13px;">Hapus Ticket</strong><br>
            <span style="font-size:11px;color:#555;">{{ session('hapus_ticket_sukses') }}</span>
        </div>
    </div>
    @endif

    @if (session('hapus_ticket_gagal'))
    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-4 shadow-sm border-0 py-3 mx-3 fade show">
        <i class="fa-regular fa-circle-xmark fs-4 text-danger"></i>
        <div>
            <strong style="font-size:13px;">Hapus Ticket</strong><br>
            <span style="font-size:11px;color:#555;">{{ session('hapus_ticket_gagal') }}</span>
        </div>
    </div>
    @endif

    <div class="card-body px-3 px-md-4">
        <div id="draft_alert_container"></div>
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="stat-box stat-total">
                    <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                    <div class="stat-value" id="cnt_total">
                        <span class="skeleton" style="width:28px;height:18px;display:inline-block;"></span>
                    </div>
                    <div class="stat-label">Total Pengajuan</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box stat-submitted">
                    <div class="stat-icon"><i class="fa-solid fa-paper-plane"></i></div>
                    <div class="stat-value" id="cnt_diajukan">
                        <span class="skeleton" style="width:28px;height:18px;display:inline-block;"></span>
                    </div>
                    <div class="stat-label">Menunggu Review</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box stat-waiting">
                    <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div class="stat-value" id="cnt_waiting">
                        <span class="skeleton" style="width:28px;height:18px;display:inline-block;"></span>
                    </div>
                    <div class="stat-label">Disetujui / Jalan</div>
                </div>
            </div>
            <!-- <div class="col-6 col-md-3">
                <div class="stat-box stat-tolak">
                    <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div class="stat-value" id="cnt_tolak">
                        <span class="skeleton" style="width:28px;height:18px;display:inline-block;"></span>
                    </div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div> -->
            <div class="col-6 col-md-3">
                <div class="stat-box stat-done">
                    <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-value" id="cnt_selesai">
                        <span class="skeleton" style="width:28px;height:18px;display:inline-block;"></span>
                    </div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
        </div>
        <div class="mb-3" style="position:relative;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#a78bfa; font-size:13px; z-index:2;"></i>
            <input type="text" id="search_ticket"
                class="form-control form-control-sm"
                placeholder="Cari nomor, menu, jenis, detail..."
                style="padding-left:36px; border-radius:12px; border:1px solid #e9d5ff; font-size:12px; height:38px; background:#fdfaff;">
        </div>

        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <span style="font-size:11px; color:#a78bfa; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">
                <i class="fa-solid fa-arrow-up-wide-short me-1"></i>Urutkan:
            </span>
            <div class="d-flex gap-1 flex-wrap">
                <button type="button" class="btn-sort active" data-sort="tanggal">
                    <i class="fa-solid fa-calendar me-1"></i>Tanggal
                    <i class="fa-solid fa-arrow-down sort-icon"></i>
                </button>
                <button type="button" class="btn-sort" data-sort="nomor">
                    <i class="fa-solid fa-hashtag me-1"></i>Nomor
                    <i class="fa-solid fa-arrow-down sort-icon"></i>
                </button>
                <button type="button" class="btn-sort" data-sort="progress">
                    <i class="fa-solid fa-chart-line me-1"></i>Progress
                    <i class="fa-solid fa-arrow-down sort-icon"></i>
                </button>
            </div>
        </div>
        <ul class="nav profile-tabs mb-3" id="profileTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link" data-tab="semua" role="tab">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Semua</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link active" data-tab="pending" role="tab">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Menunggu</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="approved" role="tab">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Disetujui</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="selesai" role="tab">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Selesai</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="dibatalkan" role="tab">
                    <i class="fa-solid fa-ban"></i>
                    <span>Dibatalkan/Ditolak</span>
                </button>
            </li>
        </ul>
        <a href="{{ route('ticket.tambah', ['id' => $menu_id]) }}"
            class="btn btn-primary btn-sm mb-3 rounded-pill px-3">
            <i class="fa-solid fa-plus me-1"></i> Tambah
        </a>
        <div id="alert_ticket_list"></div>
        <div class="content-ticket"></div>
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

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });

    $(document).ready(function() {
        const menu_active = '{{ $menu_id }}';
        const REQUESTOR_ACTION_URL = "{{ url('/ticket/requestor-action') }}";
        const CSRF_TOKEN = '{{ csrf_token() }}';
        let allTickets = [];
        // FIX: samakan dengan tab yang berstatus "active" di HTML (Menunggu)
        let activeTab = 'pending';
        let searchQuery = '';
        let sortBy = 'tanggal';
        let sortDir = 'desc';

        /* ─── SKELETON / ALERT ───────────────────────────── */
        function showSkeleton(sel) {
            $(sel).html('<span class="skeleton d-block"></span>');
        }

        function hideSkeleton(sel, html) {
            $(sel).html(html);
        }

        function showAlert(sel, type, msg) {
            $(sel).stop(true, true)
                .html(`<div class="alert alert-${type} py-2 px-3 rounded-3 mb-3" style="font-size:12px;">${msg}</div>`)
                .show();
            setTimeout(function() {
                $(sel).animate({
                    opacity: 0,
                    marginTop: '-10px'
                }, 800, function() {
                    $(this).slideUp('fast', function() {
                        $(this).html('').css({
                            opacity: '',
                            marginTop: ''
                        });
                    });
                });
            }, 5000);
        }

        /* ─── FORMAT DATE ─────────────────────────────────── */
        function formatLabel(date) {
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const mo = String(d.getMonth() + 1).padStart(2, '0');
            return `${day}-${mo}-${d.getFullYear()}`;
        }

        /* ─── STATUS INFO ─────────────────────────────────── */
        function requestorStatusInfo(status, approvalStatus) {
            if (approvalStatus === 'rejected' || status === 'T08') {
                return {
                    label: 'Ditolak',
                    className: 'bg-danger text-white',
                    icon: 'fa-ban'
                };
            }
            const map = {
                T00: {
                    label: 'Draft',
                    className: 'bg-secondary text-white',
                    icon: 'fa-pen-to-square'
                },
                T01: {
                    label: 'Menunggu Persetujuan',
                    className: 'bg-warning text-dark',
                    icon: 'fa-clock'
                },
                T02: {
                    label: 'Disetujui',
                    className: 'bg-success text-white',
                    icon: 'fa-circle-check'
                },
                T03: {
                    label: 'Ditugaskan',
                    className: 'bg-primary text-white',
                    icon: 'fa-user-check'
                },
                T04: {
                    label: 'Dikerjakan',
                    className: 'bg-info text-white',
                    icon: 'fa-spinner'
                },
                T05: {
                    label: 'Menunggu Respon Peminta',
                    className: 'bg-warning text-dark',
                    icon: 'fa-hourglass-half'
                },
                T06: {
                    label: 'Selesai',
                    className: 'bg-success text-white',
                    icon: 'fa-circle-check'
                },
                T07: {
                    label: 'Respon Balasan Peminta',
                    className: 'bg-danger text-white',
                    icon: 'fa-comment-dots'
                },
                T08: {
                    label: 'Dibatalkan',
                    className: 'bg-danger text-white',
                    icon: 'fa-ban'
                },
                T09: {
                    label: 'Disetujui Atasan 1',
                    className: 'bg-info text-white',
                    icon: 'fa-check-double'
                },
                T10: {
                    label: 'Disetujui Atasan 2',
                    className: 'bg-info text-white',
                    icon: 'fa-check-double'
                },
                T11: {
                    label: 'Menunggu Divalidasi',
                    className: 'bg-primary text-white',
                    icon: 'fa-clipboard-check'
                },
                T12: {
                    label: 'Menunggu Pemeriksaan',
                    className: 'bg-warning text-dark',
                    icon: 'fa-magnifying-glass'
                },
                T13: {
                    label: 'Menunggu Ditugaskan',
                    className: 'bg-warning text-dark',
                    icon: 'fa-user-check'
                },
                T14: {
                    label: 'Pending',
                    className: 'bg-warning text-dark',
                    icon: 'fa-clock'
                },
                T15: {
                    label: 'Ditutup Otomatis oleh Sistem',
                    className: 'bg-success text-white',
                    icon: 'fa-circle-check'
                }

            };
            return map[status] || {
                label: '-',
                className: 'bg-secondary text-white',
                icon: 'fa-circle'
            };
        }

        /* ─── TICKET MASUK TAB APA ────────────────────────── */
        function getTicketTab(el) {
            const s = el.ticket_status;
            if (s === 'T08' || el.approval_status === 'rejected') return 'dibatalkan';
            if (s === 'T06' || s === 'T15') return 'selesai';
            if (s === 'T02' || s === 'T03' || s === 'T04' || s === 'T05') return 'approved';
            return 'pending'; // T01, T00, T07, dll
        }

        /* ─── APAKAH COCOK DENGAN SEARCH ─────────────────── */
        function matchSearch(el, q) {
            if (!q) return true;
            const lower = q.toLowerCase();
            const fields = [
                el.nomor_ticket,
                el.ticket_menu?.nama_menu,
                el.ticket_sub_menu?.nama_sub_menu,
                el.status_label,
                el.pic_name,
            ].filter(Boolean).join(' ').toLowerCase();
            const details = (el.details || []).map(d => [d.nomor_detail, d.alasan_ubah, d.before_ubah, d.after_ubah].filter(Boolean).join(' ')).join(' ').toLowerCase();
            return fields.includes(lower) || details.includes(lower);
        }

        /* ─── UPDATE COUNTER & BANNER ────────────────────── */
        function updateCounters(tickets) {
            const list = tickets || [];
            let selesai = 0,
                approved = 0,
                pending = 0,
                draftGenCount = 0;
            list.forEach(el => {
                const tab = getTicketTab(el);
                if (tab === 'selesai') selesai++;
                else if (tab === 'approved') approved++;
                else if (tab === 'pending') pending++; // FIX: dibatalkan tidak ikut dihitung

                if (el.is_generated && (el.is_draft || el.is_submit == false || el.is_submit == 0 || el.ticket_status === 'T00')) {
                    draftGenCount++;
                }
            });
            $('#cnt_total').text(list.length);
            $('#cnt_diajukan').text(pending);
            $('#cnt_waiting').text(approved);
            $('#cnt_selesai').text(selesai);

            if (draftGenCount > 0) {
                $('#draft_alert_container').html(`
                    <div class="alert alert-warning border border-warning-subtle d-flex align-items-center justify-content-between p-3 rounded-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-white flex-shrink-0" style="width:36px; height:36px;">
                                <i class="fa-solid fa-triangle-exclamation fs-6"></i>
                            </div>
                            <div>
                                <strong style="font-size:12.5px; color:#78350f;">Tindakan Diperlukan (${draftGenCount} Tiket Lanjutan)</strong>
                                <div style="font-size:11px; color:#92400e;">Anda memiliki tiket turunan berstatus draf yang wajib dilengkapi agar dapat diproses validator.</div>
                            </div>
                        </div>
                    </div>
                `);
            } else {
                $('#draft_alert_container').empty();
            }
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
            let label = name || 'Epicor SPS';
            if (kat === 1) {
                cls = 'badge-env-epicor';
                icon = 'fa-database';
                label = name || 'Epicor SPS';
            } else if (kat === 2) {
                cls = 'badge-env-accurate-sp';
                icon = 'fa-bolt';
                label = name || 'AOL SP';
            } else if (kat === 3) {
                cls = 'badge-env-accurate-aja';
                icon = 'fa-network-wired';
                label = name || 'AOL AJA';
            }
            return `<span class="badge ${cls}" style="font-size:9px; font-weight:700; padding:2px 6px; border-radius:6px; display:inline-flex; align-items:center; gap:3px;"><i class="fa-solid ${icon}"></i>${label}</span>`;
        }

        /* ─── RENDER TICKET CARD ──────────────────────────── */
        function buildTicketCard(el) {
            const nama = el.ticket_menu?.nama_menu ?? 'Ticket';
            const submenu = el.ticket_sub_menu?.nama_sub_menu ?? '-';
            const pct = el.progress_percent ?? 0;
            const color = pct >= 100 ? 'bg-success' : pct >= 50 ? 'bg-info' : pct > 0 ? 'bg-warning' : 'bg-primary';
            const isRejected = (el.approval_status === 'rejected' || el.ticket_status === 'T08');
            const isClosed = (el.ticket_status === 'T06' || el.ticket_status === 'T15');
            const isDraftGen = (el.is_generated && (el.is_draft || el.is_submit == false || el.is_submit == 0 || el.ticket_status === 'T00'));
            let targetUrl = `{{ url('/ticket/lihat') }}/${menu_active}/${el.id}`;
            if (!isRejected && (el.is_submit == false || el.is_submit == 0 || el.is_draft == true)) {
                if (el.is_generated) {
                    targetUrl = `{{ url('/ticket/tambah_generated/draft') }}/${el.id}?menuId=${menu_active}`;
                } else {
                    targetUrl = `{{ url('/ticket/edit') }}/${menu_active}/${el.id}`;
                }
            }
            const statusInfo = requestorStatusInfo(el.ticket_status, el.approval_status);
            if (el.is_draft || el.ticket_status === 'T00') {
                statusInfo.label = el.is_generated ? 'Perlu Dilengkapi' : 'Draft';
                statusInfo.className = el.is_generated ? 'bg-warning text-dark' : 'bg-secondary text-white';
                statusInfo.icon = 'fa-pen-to-square';
            }
            const picInfo = el.pic_name ?
                `<span class="ms-1"><i class="fa-solid fa-user-tie me-1"></i>${el.pic_name}</span>` :
                '';
            let cardClass = 'coworker-card';
            let dotIcon = '<i class="fa-solid fa-circle-dot text-purple"></i>';
            let iconBox = '<div class="icon-box text-purple"><i class="fa-regular fa-file-lines"></i></div>';
            let indicator = '';
            let flagBadge = `<span class="badge ${statusInfo.className} rounded-pill px-2 py-1" style="font-size:7pt; white-space:nowrap;"><i class="fa-solid ${statusInfo.icon} me-1"></i>${statusInfo.label}</span>`;
            if (isRejected) {
                cardClass = 'coworker-card-reject';
                dotIcon = '<i class="fa-solid fa-circle-xmark text-danger"></i>';
                iconBox = '<div class="icon-box-reject"><i class="fa-solid fa-ban"></i></div>';
                indicator = `
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top border-danger border-opacity-25">
                        <small class="text-danger fw-semibold" style="font-size:7.5pt;">
                            <i class="fa-solid fa-ban me-1"></i>Pengajuan Ditolak
                        </small> 
                    </div>`;
            } else if (isClosed) {
                cardClass = 'coworker-card-history';
                dotIcon = '<i class="fa-solid fa-circle-check text-success"></i>';
                iconBox = '<div class="icon-box-success"><i class="fa-solid fa-check"></i></div>';
                indicator = `
                    <div class="progress mt-2" style="height:5px;">
                        <div class="progress-bar bg-success progress-animated border-0" style="width:100%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-success fw-semibold" style="font-size:7pt;">Progress Ubah/Hapus - 100%</small>
                    </div>`;
            } else {
                cardClass = 'coworker-card';
                dotIcon = '<i class="fa-solid fa-circle-dot text-purple"></i>';
                iconBox = '<div class="icon-box text-purple"><i class="fa-regular fa-file-lines"></i></div>';
                indicator = `
                    <div class="progress mt-2" style="height:5px;">
                        <div class="progress-bar ${color} progress-animated border-0" style="width:${pct}%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted" style="font-size:7pt;">Progress Ubah/Hapus (${el.progress_done}/${el.progress_total}) - ${pct}%</small>
                    </div>`;
            }

            let childDraftPrompt = '';
            if (isDraftGen) {
                childDraftPrompt = `
                    <div class="mt-2 pt-2 border-top border-warning border-opacity-50">
                        <div class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle d-flex align-items-center justify-content-between py-1 px-2 rounded-2" style="font-size:10px;">
                            <span><i class="fa-solid fa-pen-to-square me-1"></i>Tiket Lanjutan Belum Dilengkapi</span>
                            <span class="fw-bold text-decoration-underline">Lengkapi Sekarang &rarr;</span>
                        </div>
                    </div>`;
            }

            const requestorActions = el.can_review_pic ? `
                <div class="d-flex gap-2 mb-3 mt-n2 px-1">
                    <button type="button"
                        class="btn btn-sm btn-outline-warning rounded-pill flex-fill btn-ticket-feedback"
                        data-id="${el.id}">
                        <i class="fa-solid fa-comment-dots me-1"></i>Beri Catatan / Revisi
                    </button>
                    <button type="button"
                        class="btn btn-sm btn-success rounded-pill flex-fill btn-ticket-close"
                        data-id="${el.id}">
                        <i class="fa-solid fa-circle-check me-1"></i>Konfirmasi Selesai
                    </button>
                </div>` : '';
            const envBadge = getEnvBadge(el.environment_kategori, el.environment_name, el.nomor_ticket);
            return `
                <a class="text-decoration-none" href="${targetUrl}">
                    <div class="${cardClass} d-flex align-items-center mb-3">
                        <div class="avatar-wrapper me-1" style="flex-shrink:0;">
                            ${dotIcon}
                        </div>
                        <div class="flex-grow-1 ms-2 text-start ticket-card-body">
                            <div class="d-flex align-items-start gap-1">
                                <div class="flex-grow-1" style="min-width:0;">
                                    <h6 class="mb-0 fw-semibold text-truncate" style="font-size:12px;">${el.nomor_ticket ?? '-'}</h6>
                                    <div class="mt-1">${envBadge}</div>
                                </div>
                                <span class="flex-shrink-0">${flagBadge}</span>
                            </div>
                            <div class="text-muted small">
                                <span style="font-size:10px;">${nama} — ${submenu}</span>
                                <div class="d-flex align-items-center mt-1">
                                    <i class="fa fa-calendar me-1"></i>
                                    <span style="font-size:9pt;">${formatLabel(el.created_at)}</span>
                                </div>
                                <div class="mt-1">
                                    ${picInfo}
                                </div>
                            </div>
                            ${indicator}
                            ${childDraftPrompt}
                        </div>
                        ${iconBox}
                    </div>
                </a>
                ${requestorActions}`;
        }

        function renderTickets() {
            let filtered = allTickets.filter(el => {
                // FIX: "semua" menampilkan semua, selain itu harus cocok dengan tab-nya
                const tabMatch = activeTab === 'semua' || getTicketTab(el) === activeTab;
                return tabMatch && matchSearch(el, searchQuery);
            });
            filtered.sort(function(a, b) {
                let valA, valB;
                if (sortBy === 'tanggal') {
                    valA = new Date(a.created_at).getTime();
                    valB = new Date(b.created_at).getTime();
                } else if (sortBy === 'nomor') {
                    const extractNum = str => {
                        const parts = (str || '').split('/');
                        return parseInt(parts[parts.length - 1]) || 0;
                    };
                    valA = extractNum(a.nomor_ticket);
                    valB = extractNum(b.nomor_ticket);
                } else if (sortBy === 'progress') {
                    valA = a.progress_percent ?? 0;
                    valB = b.progress_percent ?? 0;
                }
                return sortDir === 'asc' ? valA - valB : valB - valA;
            });
            let html = '';
            if (filtered.length === 0) {
                const emptyMsg = searchQuery ?
                    `Tidak ada ticket yang cocok dengan "<strong>${$('<div>').text(searchQuery).html()}</strong>"` :
                    'Belum ada data ticket';
                html = `
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-file-lines fa-2x fa-bounce mb-2 d-block" style="color:#d8b4fe;"></i>
                        ${emptyMsg}
                    </div>`;
            } else {
                const sortLabel = {
                    tanggal: sortDir === 'desc' ? 'Terbaru ke Terlama' : 'Terlama ke Terbaru',
                    nomor: sortDir === 'desc' ? 'Nomor Terbesar' : 'Nomor Terkecil',
                    progress: sortDir === 'desc' ? 'Progress Tertinggi' : 'Progress Terendah',
                };
                html += `
                    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                        <small class="text-muted" style="font-size:10px;">
                            <i class="fa-solid fa-list me-1"></i>
                            ${filtered.length} ticket
                        </small>
                        <small style="font-size:10px; color:#a78bfa; font-weight:600;">
                            <i class="fa-solid fa-arrow-up-wide-short me-1"></i>${sortLabel[sortBy]}
                        </small>
                    </div>`;
                filtered.forEach(el => {
                    html += buildTicketCard(el);
                });
            }
            hideSkeleton('.content-ticket', html);
        }

        $(document).on('click', '.btn-sort', function() {
            const newSort = $(this).data('sort');
            if (sortBy === newSort) {
                sortDir = sortDir === 'desc' ? 'asc' : 'desc';
                $(this).toggleClass('asc', sortDir === 'asc');
            } else {
                sortBy = newSort;
                sortDir = 'desc';
                $('.btn-sort').removeClass('active asc');
                $(this).addClass('active');
            }
            renderTickets();
        });

        function load_data_ticket() {
            showSkeleton('.content-ticket');
            $.ajax({
                url: "{{ url('/ticket/get_ticket') }}" + '/' + menu_active,
                type: 'GET',
                success: function(data) {
                    allTickets = data.ticket || [];
                    updateCounters(allTickets);
                    renderTickets();
                },
                error: function(xhr) {
                    hideSkeleton('.content-ticket', '');
                    updateCounters([]);
                    showAlert('#alert_ticket_list', 'danger',
                        window.getFriendlyErrorMessage(xhr, 'Gagal memuat data ticket.'));
                },
            });
        }

        /* ─── TAB CLICK ───────────────────────────────────── */
        $(document).on('click', '[data-tab]', function() {
            $('[data-tab]').removeClass('active');
            $(this).addClass('active');
            activeTab = $(this).data('tab');
            renderTickets();
        });

        /* ─── SEARCH INPUT ────────────────────────────────── */
        let searchTimer;
        $('#search_ticket').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                searchQuery = $('#search_ticket').val().trim();
                renderTickets();
            }, 300); // debounce 300ms
        });

        /* ─── REQUESTOR ACTION ────────────────────────────── */
        $(document).on('click', '.btn-ticket-feedback', function() {
            submitRequestorAction($(this).data('id'), 'feedback');
        });
        $(document).on('click', '.btn-ticket-close', function() {
            submitRequestorAction($(this).data('id'), 'close');
        });

        function submitRequestorAction(ticketId, action) {
            const isFeedback = action === 'feedback';
            Swal.fire({
                icon: isFeedback ? 'warning' : 'question',
                title: isFeedback ? 'Kirim feedback ke PIC?' : 'Close ticket ini?',
                text: isFeedback ?
                    'Ticket akan dikembalikan ke PIC untuk diperbaiki.' : 'Ticket akan ditutup sebagai benar-benar selesai.',
                input: 'textarea',
                inputPlaceholder: isFeedback ? 'Tuliskan bagian yang belum sesuai...' : 'Catatan opsional...',
                inputValidator: function(value) {
                    if (isFeedback && !String(value || '').trim()) return 'Feedback wajib diisi.';
                    return null;
                },
                showCancelButton: true,
                confirmButtonText: isFeedback ? 'Kirim Feedback' : 'Close Ticket',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: REQUESTOR_ACTION_URL + '/' + ticketId,
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        action: action,
                        comment_text: result.value || ''
                    },
                    success: function(res) {
                        if (res.code == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                            load_data_ticket();
                            return;
                        }
                        Swal.fire('Gagal', res.message || 'Status ticket gagal diperbarui.', 'error');
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', window.getFriendlyErrorMessage(xhr, 'Tidak dapat terhubung ke server.'), 'error');
                    }
                });
            });
        }

        /* ─── PAGE UNLOAD ─────────────────────────────────── */
        window.onbeforeunload = function() {
            Swal.fire({
                allowOutsideClick: false,
                background: 'transparent',
                html: '<div class="spinner-grow text-primary spinner-grow-sm me-2"></div>'.repeat(3),
                showCancelButton: false,
                showConfirmButton: false,
            });
        };

        /* ─── FLASH MESSAGES ─────────────────────────────── */
        @if (session('hapus_ticket_sukses'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('hapus_ticket_sukses') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
        @if (session('hapus_ticket_gagal'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('hapus_ticket_gagal') }}"
            });
        @endif

        /* ─── INIT ────────────────────────────────────────── */
        load_data_ticket();
    });
</script>
@endsection