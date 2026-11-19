@extends('users.layouts.main')
@section('title')
APPS | Knowledge Base
@endsection

@section('css')
<style>
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
    .header-back-btn:hover { background: #ede9fe; color: #6d28d9; }

    .futuristic-header { padding: 28px 20px 18px; }
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

    .kb-search-wrap {
        position: relative;
        padding: 0 16px;
        margin-bottom: 16px;
    }
    .kb-search {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 14px 11px 40px;
        border-radius: 14px;
        border: 1px solid #e9d5ff;
        background: #fff;
        font-size: 13px;
        color: #1f2937;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .kb-search:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
    .kb-search-icon {
        position: absolute;
        left: 28px;
        top: 50%;
        transform: translateY(-50%);
        color: #a78bfa;
        font-size: 14px;
        pointer-events: none;
    }
    .stat-box {
        border-radius: 14px;
        padding: 12px 10px;
        text-align: center;
        box-shadow: 0 6px 16px rgba(111, 66, 193, .10);
        background: linear-gradient(135deg, #f5edff, #ffffff);
    }
    .stat-box .stat-icon {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 6px; font-size: 14px;
        background: #ede9fe; color: #7c3aed;
    }
    .stat-box .stat-value { font-size: 20px; font-weight: 700; color: #1f2937; line-height: 1; }
    .stat-box .stat-label { font-size: 10px; color: #6b7280; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }

    .header-box {
        padding: 22px 16px;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        border-radius: 16px 16px 16px 16px;
        border: 1px solid #e0e7ff;
        border-bottom: none;
        text-align: center;
    }
    .header-box .font-weight-bold {
        color: #5b21b6;
        font-size: 14px;
        letter-spacing: 0.4px;
    }

    .kb-section-lbl {
        font-size: 11px; font-weight: 700; color: #6b7280;
        padding: 4px 16px 8px;
        text-transform: uppercase; letter-spacing: 0.6px;
    }

    .kb-cat-item {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 6px 16px rgba(111, 66, 193, 0.10);
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease;
        cursor: pointer;
    }
    .kb-cat-item:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18); }
    .kb-cat-icon {
        width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
        background: #ede9fe; color: #7c3aed;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .kb-cat-info { flex: 1; min-width: 0; }
    .kb-cat-name { font-size: 13px; font-weight: 700; color: #1f2937; margin: 0 0 2px; }
    .kb-cat-desc { font-size: 11px; color: #6b7280; margin: 0 0 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .kb-cat-badge {
        font-size: 10px; background: #ede9fe; color: #5b21b6;
        border-radius: 8px; padding: 2px 8px; font-weight: 600; display: inline-block;
    }
    .kb-cat-arrow { color: #a78bfa; font-size: 14px; flex-shrink: 0; }

    .skeleton {
        display: inline-block;
        background: linear-gradient(90deg, #ede9fe 25%, #f5f3ff 37%, #ede9fe 63%);
        background-size: 400% 100%;
        animation: skPulse 1.4s ease-in-out infinite;
        border-radius: 6px; width: 100%; height: 72px;
    }
    @keyframes skPulse { 0%,100%{opacity:.5} 50%{opacity:1} }

    .kb-empty { text-align: center; padding: 48px 20px; color: #6b7280; }
    .kb-empty i { font-size: 40px; color: #d8b4fe; display: block; margin-bottom: 12px; }
    .kb-empty p { font-size: 13px; margin: 0; }
</style>
@endsection

@section('content_top')
<div class="card position-relative">
    <a href="{{ $back_url ?? url()->previous() }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-book-open-reader"></i>
        </div>
        <h5 class="mt-3 mb-1">Knowledge Base</h5>
        <small class="text-muted">Pusat Informasi & Panduan</small>
    </div>  

    <!-- <div class="row g-2">
        <div class="col-12">
            <div class="header-box">
                <span class="text-white fw-bold" style="font-size: 30px;">Knowledge Base</span><br>
                <span class="text-white fw-bold" style="font-size: 17px;">Pilih atau Cari Kategori</span><br>
                <small class="text-white">Temukan informasi yang kamu butuhkan dengan mudah!</small>
            </div>
        </div>
    </div> -->

    <div class="card-body px-0 pb-0">
        <div class="kb-search-wrap">
            <i class="fa-solid fa-magnifying-glass kb-search-icon"></i>
            <input type="text" class="kb-search" id="kbSearch"
                placeholder="Cari artikel atau kategori...">
        </div>

        <!-- <div class="row g-2 mb-3 px-3">
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                    <div class="stat-value">{{ $stats['total_kategori'] }}</div>
                    <div class="stat-label">Kategori</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="stat-value">{{ $stats['total_artikel'] }}</div>
                    <div class="stat-label">Artikel</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fa-solid fa-globe"></i></div>
                    <div class="stat-value">{{ $stats['total_publik'] }}</div>
                    <div class="stat-label">Publik</div>
                </div>
            </div>
        </div> -->

        <div class="row g-2">
            <div class="col-12">
                <div class="header-box">
                    <span class="text-white fw-bold" style="font-size: 20px;">Pilih atau Cari Kategori</span><br>
                    <small class="text-white">Temukan informasi yang kamu butuhkan dengan mudah!</small>
                </div>
            </div>
        </div>

        <div class="kb-section-lbl">Kategori</div>

        <div id="kategoriList" class="px-3 pb-4">
            @forelse($kategori as $kat)
            <a href="{{ route('user.kb.artikel', $kat->kb_slug ?? $kat->id) }}"
                class="kb-cat-item d-flex">
                <div class="kb-cat-icon">
                    <i class="{{ $kat->kb_icon ?? 'fa-solid fa-folder' }}"></i>
                </div>
                <div class="kb-cat-info">
                    <div class="kb-cat-name">{{ $kat->kb_name }}</div>
                    <div class="kb-cat-desc">{{ $kat->kb_deskripsi }}</div>
                    <span class="kb-cat-badge">{{ $kat->artikel_label }}</span>
                </div>
                <i class="fa-solid fa-chevron-right kb-cat-arrow align-self-center"></i>
            </a>
            @empty
            <div class="kb-empty">
                <i class="fa-solid fa-folder-open"></i>
                <p>Belum ada kategori tersedia</p>
            </div>
            @endforelse
        </div>

        <div id="searchResult" class="px-3 pb-4" style="display:none;">
            <div class="kb-section-lbl ps-0 mb-2">Hasil Pencarian</div>
            <div id="searchResultList"></div>
        </div>

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
<script>
$(document).ready(function () {

    const SEARCH_URL  = "{{ route('user.kb.search') }}";
    const DETAIL_BASE = "{{ url('/knowledge-base/artikel') }}";
    let searchTimer = null;

    $('#kbSearch').on('input', function () {
        const q = $(this).val().trim();
        clearTimeout(searchTimer);

        if (q.length < 2) {
            $('#searchResult').hide();
            $('#kategoriList').show();
            return;
        }

        searchTimer = setTimeout(function () {
            $('#searchResultList').html('<span class="skeleton d-block" style="height:60px;"></span>');
            $('#kategoriList').hide();
            $('#searchResult').show();

            $.get(SEARCH_URL, { q: q }, function (res) {
                let html = '';
                if (res.code === 200 && res.data && res.data.length > 0) {
                    res.data.forEach(function (art) {
                        const statusClass = art.kba_status === 'publik'
                            ? 'background:#dcfce7;color:#166534;'
                            : 'background:#fef3c7;color:#92400e;';
                        const tags = Array.isArray(art.kba_tags) ? art.kba_tags : [];
                        const tagHtml = tags.slice(0, 2).map(t =>
                            `<span style="font-size:10px;background:#ede9fe;color:#5b21b6;border-radius:6px;padding:2px 7px;">${t}</span>`
                        ).join('');

                        html += `
                        <a href="${DETAIL_BASE}/${art.kba_slug || art.id}"
                           class="d-block text-decoration-none mb-2">
                            <div style="background:linear-gradient(135deg,#f5edff,#fff);border-radius:14px;padding:12px 14px;box-shadow:0 4px 12px rgba(111,66,193,.08);">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <span style="font-size:13px;font-weight:700;color:#1f2937;line-height:1.4;flex:1;">${art.kba_title}</span>
                                    <span style="font-size:10px;padding:2px 8px;border-radius:8px;font-weight:600;white-space:nowrap;${statusClass}">
                                        ${art.kba_status === 'publik' ? 'Publik' : 'Draft'}
                                    </span>
                                </div>
                                <div style="font-size:11px;color:#6b7280;margin-bottom:6px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    ${stripTags(art.kba_konten ?? '')}
                                </div>
                                <div class="d-flex gap-1 flex-wrap">${tagHtml}</div>
                            </div>
                        </a>`;
                    });
                } else {
                    html = `
                    <div class="kb-empty">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <p>Artikel tidak ditemukan untuk "<strong>${q}</strong>"</p>
                    </div>`;
                }
                $('#searchResultList').html(html);
            }).fail(function () {
                $('#searchResultList').html('<p class="text-danger text-center" style="font-size:12px;">Gagal memuat hasil pencarian.</p>');
            });
        }, 400);
    });

    function stripTags(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }

    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('q');
    if (initialQuery) {
        $('#kbSearch').val(initialQuery).trigger('input');
    }
});
</script>
@endsection