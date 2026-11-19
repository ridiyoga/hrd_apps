@extends('users.layouts.main')
@section('title')
APPS | KB – {{ $kategori->kb_name }}
@endsection

@section('css')
<style>
    .header-back-btn {
        position: absolute; top: 16px; left: 16px;
        width: 38px; height: 38px; border-radius: 12px;
        background: #f3e8ff; border: none;
        display: flex; align-items: center; justify-content: center;
        color: #7c3aed; font-size: 14px; text-decoration: none;
        transition: background .2s; z-index: 10;
    }
    .header-back-btn:hover { background: #ede9fe; color: #6d28d9; }

    .futuristic-header { padding: 28px 20px 5px; }
    .futuristic-icon {
        width: 68px; height: 68px; border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white; font-size: 28px;
        display: flex; align-items: center; justify-content: center;
        margin: auto; box-shadow: 0 0 25px rgba(155,109,255,.8);
    }

    .kb-breadcrumb-wrap {
        display: flex; align-items: center; gap: 5px;
        padding: 0 16px 14px; overflow-x: auto; scrollbar-width: none;
    }
    .kb-breadcrumb-wrap::-webkit-scrollbar { display: none; }
    .kb-bc-home  { color: #7c3aed; font-size: 14px; flex-shrink: 0; text-decoration: none; }
    .kb-bc-sep   { color: #c4b5fd; font-size: 9px; flex-shrink: 0; }
    .kb-bc-link  { font-size: 11px; font-weight: 600; color: #7c3aed; text-decoration: none; white-space: nowrap; flex-shrink: 0; }
    .kb-bc-link:hover { text-decoration: underline; }
    .kb-bc-current { font-size: 11px; font-weight: 700; color: #1f2937; white-space: nowrap; flex-shrink: 0; }

    .stat-box {
        border-radius: 14px; padding: 12px 10px; text-align: center;
        box-shadow: 0 6px 16px rgba(111,66,193,.10);
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

    .kb-filter-wrap { display: flex; gap: 6px; padding: 4px 16px 12px; overflow-x: auto; }
    .kb-filter-wrap::-webkit-scrollbar { display: none; }
    .kb-pill {
        font-size: 11px; font-weight: 600; white-space: nowrap;
        padding: 5px 14px; border-radius: 20px; border: none; cursor: pointer;
        transition: all .15s;
    }
    .kb-pill.active          { background: #ede9fe; color: #5b21b6; }
    .kb-pill:not(.active)    { background: #f3f4f6; color: #6b7280; }
    .kb-pill:hover:not(.active) { background: #f0e6ff; color: #7c3aed; }

    .kb-section-lbl {
        font-size: 11px; font-weight: 700; color: #6b7280;
        padding: 6px 0 8px;
        text-transform: uppercase; letter-spacing: .6px;
        display: flex; align-items: center; gap: 5px;
    }
    .kb-section-lbl i { color: #a78bfa; }

    .kb-folder-item {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px; padding: 12px 14px; margin-bottom: 10px;
        display: flex; align-items: center; gap: 12px;
        box-shadow: 0 6px 16px rgba(111,66,193,.10);
        text-decoration: none;
        transition: transform .2s, box-shadow .2s;
    }
    .kb-folder-item:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(111,66,193,.18); }
    .kb-folder-icon {
        width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
        background: linear-gradient(135deg, #ddd6fe, #c4b5fd);
        color: #5b21b6;
        display: flex; align-items: center; justify-content: center; font-size: 19px;
    }
    .kb-folder-info { flex: 1; min-width: 0; }
    .kb-folder-name { font-size: 13px; font-weight: 700; color: #1f2937; margin: 0 0 2px; }
    .kb-folder-desc { font-size: 11px; color: #6b7280; margin: 0 0 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .kb-folder-badge { font-size: 10px; background: #ede9fe; color: #5b21b6; border-radius: 8px; padding: 2px 8px; font-weight: 600; }
    .kb-folder-sub   { font-size: 10px; background: #ddd6fe; color: #4c1d95; border-radius: 8px; padding: 2px 7px; font-weight: 600; margin-left: 4px; }
    .kb-arrow        { color: #a78bfa; font-size: 13px; flex-shrink: 0; }

    .kb-art-item {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px; padding: 12px 14px; margin-bottom: 10px;
        box-shadow: 0 6px 16px rgba(111,66,193,.10);
        text-decoration: none; display: block;
        transition: transform .2s, box-shadow .2s;
    }
    .kb-art-item:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(111,66,193,.18); }
    .kb-art-top     { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 6px; }
    .kb-art-title   { font-size: 13px; font-weight: 700; color: #1f2937; line-height: 1.4; flex: 1; }
    .kb-art-excerpt {
        font-size: 11px; color: #6b7280; line-height: 1.6; margin-bottom: 8px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .kb-art-footer { display: flex; align-items: center; justify-content: space-between; }
    .kb-art-tags   { display: flex; gap: 4px; flex-wrap: wrap; }
    .kb-art-tag    { font-size: 10px; background: #ede9fe; color: #5b21b6; border-radius: 6px; padding: 2px 7px; font-weight: 600; }
    .kb-art-date   { font-size: 10px; color: #9ca3af; display: flex; align-items: center; gap: 3px; }
    .kb-view-count { font-size: 10px; color: #a78bfa; display: flex; align-items: center; gap: 3px; }

    .kb-status-pub { font-size: 10px; background: #dcfce7; color: #166534; border-radius: 8px; padding: 2px 8px; font-weight: 600; white-space: nowrap; }
    .kb-status-dft { font-size: 10px; background: #fef3c7; color: #92400e; border-radius: 8px; padding: 2px 8px; font-weight: 600; white-space: nowrap; }

    .kb-empty { text-align: center; padding: 48px 20px; color: #6b7280; }
    .kb-empty i { font-size: 40px; color: #d8b4fe; display: block; margin-bottom: 12px; }
    .kb-empty p { font-size: 13px; margin: 0; }
    .kb-empty small { font-size: 11px; color: #9ca3af; }
</style>
@endsection

@section('content_top')
<div class="card position-relative">

    @if($kategori->kb_parent_id)
        <a href="{{ route('user.kb.artikel', $kategori->parent ? ($kategori->parent->kb_slug ?? $kategori->parent->id) : $kategori->kb_parent_id) }}" class="header-back-btn">
    @else
        <a href="{{ route('user.kb.index') }}" class="header-back-btn">
    @endif
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="{{ $kategori->kb_icon ?? 'fa-solid fa-folder-open' }}"></i>
        </div>
        <h5 class="mt-3 mb-1">{{ $kategori->kb_name }}</h5>
        <small class="text-muted">{{ Str::limit($kategori->kb_deskripsi ?? '', 70) }}</small>
    </div>

    <!-- <div class="row g-2">
        <div class="col-12">
            <div class="header-box">
                <span class="text-white fw-bold" style="font-size: 20px;">Kategori {{$kategori->kb_name}}</span><br>
                <small class="text-white">{{Str::limit($kategori->kb_deskripsi ?? '', 70)}}</small>
            </div>
            </div>
        </div> -->
    <div class="card-body px-0 pb-0">
        <div class="kb-breadcrumb-wrap">
            <a href="{{ route('user.kb.index') }}" class="kb-bc-home">
                <i class="fa-solid fa-house-chimney"></i>
            </a>
            @foreach($breadcrumbs as $crumb)
                <i class="fa-solid fa-chevron-right kb-bc-sep"></i>
                @if($crumb->id === $kategori->id)
                    <span class="kb-bc-current">{{ $crumb->kb_name }}</span>
                @else
                    <a href="{{ route('user.kb.artikel', $crumb->kb_slug ?? $crumb->id) }}" class="kb-bc-link">
                        {{ $crumb->kb_name }}
                    </a>
                @endif
            @endforeach
        </div>

        <!-- <div class="row g-2 mb-3 px-3">
            <div class="col-6">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fa-solid fa-folder"></i></div>
                    <div class="stat-value">{{ $subKategori->count() }}</div>
                    <div class="stat-label">Sub-folder</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-box">
                    <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="stat-value">{{ $artikels->count() }}</div>
                    <div class="stat-label">Artikel</div>
                </div>
            </div>
        </div> -->

        <div class="row g-2 mb-2">
            <div class="col-12">
                <div class="header-box">
                    <span class="text-white fw-bold" style="font-size: 15px;">Pilih Sub-Kategori atau Artikel</span><br>
                    <small class="text-white">Temukan panduan &amp; informasi yang kamu butuhkan dengan mudah!</small>
                </div>
            </div>
        </div>

        @if($subKategori->count() > 0 && $artikels->count() > 0)
        <div class="kb-filter-wrap">
            <button class="kb-pill active" data-filter="all">
                <i class="fa-solid fa-border-all me-1"></i>Semua
            </button>
            <button class="kb-pill" data-filter="folder">
                <i class="fa-solid fa-folder me-1"></i>Folder
                <span>({{ $subKategori->count() }})</span>
            </button>
            <button class="kb-pill" data-filter="artikel">
                <i class="fa-solid fa-file-lines me-1"></i>Artikel
                <span>({{ $artikels->count() }})</span>
            </button>
        </div>
        @endif

        <div class="px-3 pb-4">

            @if($subKategori->count() > 0)
            <div class="kb-section-lbl" data-section="folder">
                <i class="fa-solid fa-folder-tree"></i>Sub-folder
            </div>
            @foreach($subKategori as $sub)
            <a href="{{ route('user.kb.artikel', $sub->kb_slug ?? $sub->id) }}"
               class="kb-folder-item"
               data-type="folder">
                <div class="kb-folder-icon">
                    <i class="{{ $sub->kb_icon ?? 'fa-solid fa-folder' }}"></i>
                </div>
                <div class="kb-folder-info">
                    <div class="kb-folder-name">{{ $sub->kb_name }}</div>
                    <div class="kb-folder-desc">{{ $sub->kb_deskripsi }}</div>
                    <span class="kb-folder-badge">{{ $sub->artikel_label }}</span>
                    @if($sub->children_count > 0)
                        <span class="kb-folder-sub">
                            <i class="fa-solid fa-folder-open" style="font-size:9px;"></i>
                            {{ $sub->children_count }} subfolder
                        </span>
                    @endif
                </div>
                <i class="fa-solid fa-chevron-right kb-arrow"></i>
            </a>
            @endforeach
            @endif

            @if($artikels->count() > 0)
            <div class="kb-section-lbl @if($subKategori->count() > 0) mt-2 @endif" data-section="artikel">
                <i class="fa-solid fa-file-lines"></i>Artikel
            </div>
            @foreach($artikels as $art)
            @php
                $tags    = is_array($art->kba_tags) ? $art->kba_tags : (is_string($art->kba_tags) ? explode(',', $art->kba_tags) : []);
                $excerpt = strip_tags($art->kba_konten);
                $excerpt = strlen($excerpt) > 110 ? substr($excerpt, 0, 110) . '...' : $excerpt;
            @endphp
            <a href="{{ route('user.kb.detail', $art->kba_slug ?? $art->id) }}"
               class="kb-art-item"
               data-type="artikel"
               data-status="{{ $art->kba_status }}">
                <div class="kb-art-top">
                    <div class="kb-art-title">{{ $art->kba_title }}</div>
                    @if($art->kba_status === 'publik')
                        <span class="kb-status-pub">Publik</span>
                    @else
                        <span class="kb-status-dft">Draft</span>
                    @endif
                </div>
                <div class="kb-art-excerpt">{{ $excerpt }}</div>
                <div class="kb-art-footer">
                    <div class="kb-art-tags">
                        @foreach(array_slice($tags, 0, 2) as $tag)
                            @if(trim($tag))
                                <span class="kb-art-tag">{{ trim($tag) }}</span>
                            @endif
                        @endforeach
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($art->kba_view_count)
                        <span class="kb-view-count">
                            <i class="fa-solid fa-eye"></i>{{ $art->kba_view_count }}
                        </span>
                        @endif
                        <span class="kb-art-date">
                            <i class="fa-regular fa-clock"></i>
                            {{ $art->updated_at?->diffForHumans() ?? '-' }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
            @endif

            @if($subKategori->count() === 0 && $artikels->count() === 0)
            <div class="kb-empty">
                <i class="fa-solid fa-folder-open"></i>
                <p>Folder ini masih kosong</p>
                <small>Belum ada artikel maupun subfolder di sini</small>
            </div>
            @endif

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

    $('.kb-pill').on('click', function () {
        const filter = $(this).data('filter');
        $('.kb-pill').removeClass('active');
        $(this).addClass('active');
        if (filter === 'all') {
            $('[data-type]').show();
            $('[data-section]').show();
        } else {
            $('[data-type]').hide();
            $('[data-section]').hide();
            $('[data-type="' + filter + '"]').show();
            $('[data-section="' + filter + '"]').show();
        }
    });
});
</script>
@endsection