@extends('users.layouts.main')
@section('title')
APPS | {{ $artikel->kba_title }}
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
        width: 68px; height: 68px; border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white; font-size: 28px;
        display: flex; align-items: center; justify-content: center;
        margin: auto;
        box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
    }

    .kb-status-pub { font-size: 10px; background: #dcfce7; color: #166534; border-radius: 8px; padding: 3px 9px; font-weight: 600; }
    .kb-status-dft { font-size: 10px; background: #fef3c7; color: #92400e; border-radius: 8px; padding: 3px 9px; font-weight: 600; }
    .kb-meta-item { font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 4px; }
    .kb-meta-item i { font-size: 11px; color: #a78bfa; }

    .kb-detail-card {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 6px 20px rgba(111, 66, 193, 0.10);
        margin-bottom: 12px;
    }
    .kb-detail-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b;
        line-height: 1.5;
        margin-bottom: 10px;
    }
    .kb-meta-row {
        display: flex; flex-wrap: wrap; align-items: center;
        gap: 8px; margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid #ede9fe;
    }

    .kb-rte-content {
        font-size: 13px;
        color: #1f2937;
        line-height: 1.85;
    }
    .kb-rte-content p       { margin: 0 0 10px; }
    .kb-rte-content h1      { font-size: 18px; font-weight: 700; color: #1e1b4b; margin: 16px 0 8px; }
    .kb-rte-content h2      { font-size: 16px; font-weight: 700; color: #1e1b4b; margin: 14px 0 7px; }
    .kb-rte-content h3      { font-size: 14px; font-weight: 700; color: #1e1b4b; margin: 12px 0 6px; }
    .kb-rte-content ul,
    .kb-rte-content ol      { padding-left: 18px; margin: 0 0 10px; }
    .kb-rte-content li      { margin-bottom: 4px; }
    .kb-rte-content blockquote {
        border-left: 3px solid #7c3aed;
        padding: 8px 12px;
        margin: 10px 0;
        background: #f5f3ff;
        border-radius: 0 8px 8px 0;
        color: #4c1d95;
        font-style: italic;
        font-size: 12px;
    }
    .kb-rte-content pre {
        background: #1e1b4b;
        color: #e9d5ff;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 11px;
        overflow-x: auto;
        margin: 10px 0;
    }
    .kb-rte-content table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    .kb-rte-content table td,
    .kb-rte-content table th {
        border: 1px solid #e9d5ff;
        padding: 7px 10px;
        font-size: 12px;
    }
    .kb-rte-content table th { background: #ede9fe; color: #5b21b6; font-weight: 700; }
    .kb-rte-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 6px 0; }
    .kb-rte-content a { color: #7c3aed; text-decoration: underline; }
    .kb-rte-content hr { border: none; border-top: 1px solid #ede9fe; margin: 14px 0; }

    .kb-rte-content img {
        max-width: 100% !important;
        height: auto !important;       /* override inline height="xxx" dari RTE */
        width: auto !important;        /* override inline width="xxx" dari RTE */
        border-radius: 8px;
        margin: 6px 0;
        display: block;
        object-fit: contain;           /* fallback jika ada container constraint */
    }

    /* Jika RTE membungkus img dalam figure/p */
    .kb-rte-content figure {
        margin: 8px 0;
        max-width: 100%;
        overflow: hidden;
    }
    .kb-rte-content figure img {
        width: 100% !important;
        height: auto !important;
    }

    /* Jika RTE pakai inline style width/height */
    .kb-rte-content p > img {
        max-width: 100% !important;
        height: auto !important;
    }

    .kb-img-wrap {
        position: relative;
        overflow: hidden;
        border-radius: 0px;
        margin: 8px 0;
        background: #f5f3ff;
        cursor: zoom-in;
    }
    .kb-img-wrap img {
        width: 100% !important;
        height: auto !important;
        display: block;
        border-radius: 0px;
        transition: transform .2s;
    }

    .kb-img-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,.85);
        align-items: center; justify-content: center;
        padding: 16px;
    }
    .kb-img-overlay.show { display: flex; }
    .kb-img-overlay img  { max-width: 100%; max-height: 90vh; border-radius: 0px; object-fit: contain; }

    .kb-section-card {
        background: linear-gradient(135deg, #f5edff, #ffffff);
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.08);
        margin-bottom: 12px;
    }
    .kb-section-title {
        font-size: 12px; font-weight: 700; color: #7c3aed;
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .kb-section-title i { font-size: 13px; }

    .kb-attach-item {
        display: flex; align-items: center; gap: 10px;
        background: #fff;
        border: 1px solid #e9d5ff;
        border-radius: 10px;
        padding: 9px 12px;
        margin-bottom: 6px;
        text-decoration: none;
        transition: border-color .15s;
    }
    .kb-attach-item:hover { border-color: #7c3aed; }
    .kb-attach-icon {
        width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
        background: #ede9fe; color: #7c3aed;
        display: flex; align-items: center; justify-content: center; font-size: 15px;
    }
    .kb-attach-name { font-size: 12px; font-weight: 600; color: #1f2937; }
    .kb-attach-size { font-size: 10px; color: #6b7280; }
    .kb-attach-dl { color: #a78bfa; font-size: 13px; margin-left: auto; flex-shrink: 0; }

    .kb-attach-btn {
        display: inline-flex; align-items: center; gap: 6px;
        flex-shrink: 0; margin-left: auto;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: #fff; font-size: 11px; font-weight: 600;
        padding: 7px 12px; border-radius: 9px;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(124, 58, 237, .25);
        transition: transform .15s, box-shadow .15s;
    }
    .kb-attach-btn:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 14px rgba(124, 58, 237, .35); }
    .kb-attach-btn:active { transform: scale(.97); }

    .kb-attach-icon.file-pdf {
        color: #dc3545;
    }

    .kb-attach-icon.file-word {
        color: #2b579a;
    }

    .kb-attach-icon.file-excel {
        color: #217346;
    }

    .kb-attach-icon.file-zip {
        color: #f0ad00;
    }

    .kb-attach-icon.file-image {
        color: #e83e8c;
    }

    .kb-attach-icon.file-text {
        color: #6c757d;
    }

    .kb-attach-icon.file-default {
        color: #6c757d;
    }

    .kb-art-tag {
        font-size: 10px; background: #ede9fe; color: #5b21b6;
        border-radius: 6px; padding: 3px 8px; font-weight: 600;
        display: inline-block; margin: 0 3px 3px 0;
    }

    .kb-breadcrumb {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; color: #9ca3af; padding: 8px 16px 0;
    }
    .kb-breadcrumb a { color: #7c3aed; text-decoration: none; font-weight: 600; }
    .kb-breadcrumb i { font-size: 9px; }
</style>
@endsection

@section('content_top')
<div class="card position-relative">
    <a href="{{ route('user.kb.artikel', $kategori->kb_slug ?? $kategori->id) }}" class="header-back-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-file-lines"></i>
        </div>
        <h5 class="mt-3 mb-1" style="font-size:14px;padding:0 50px;">{{ Str::limit($artikel->kba_title, 60) }}</h5>
        <small class="text-muted">{{ $kategori->kb_name }}</small>
    </div>

    <div class="kb-breadcrumb">
        <a href="{{ route('user.kb.index') }}">KB</a>
        @foreach($breadcrumbs as $crumb)
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('user.kb.artikel', $crumb->kb_slug ?? $crumb->id) }}">{{ $crumb->kb_name }}</a>
        @endforeach
        <i class="fa-solid fa-chevron-right"></i>
        <span style="color:#6b7280;">Artikel</span>
    </div>

    <div class="card-body px-3 pb-4">

        <div class="kb-detail-card">
            <div class="kb-detail-title">{{ $artikel->kba_title }}</div>

            <div class="kb-meta-row">
                @if($artikel->kba_status === 'publik')
                    <span class="kb-status-pub"><i class="fa-solid fa-globe me-1"></i>Publik</span>
                @else
                    <span class="kb-status-dft"><i class="fa-solid fa-pencil me-1"></i>Draft</span>
                @endif

                @if($artikel->kba_view_count)
                <span class="kb-meta-item">
                    <i class="fa-solid fa-eye"></i>{{ $artikel->kba_view_count }} dilihat
                </span>
                @endif

                @if($artikel->updated_at)
                <span class="kb-meta-item">
                    <i class="fa-regular fa-clock"></i>{{ $artikel->updated_at->diffForHumans() }}
                </span>
                @endif

                @if($artikel->published_at)
                <span class="kb-meta-item">
                    <i class="fa-solid fa-calendar-check"></i>
                    {{ \Carbon\Carbon::parse($artikel->published_at)->format('d M Y') }}
                </span>
                @endif
            </div>

            <div class="kb-rte-content">
                {!! $artikel->kba_konten !!}
            </div>
        </div>
            @if($artikel->attachments && $artikel->attachments->count() > 0)
            <div class="kb-section-card">
                <div class="kb-section-title">
                    <i class="fa-solid fa-paperclip"></i> Lampiran
                </div>
                @foreach($artikel->attachments as $att)
                @php
                    $ext = strtolower(
                        $att->kba_file_extension ??
                        pathinfo($att->kba_file_name, PATHINFO_EXTENSION)
                    );
                    $fileMap = [
                        'pdf'  => ['icon' => 'fa-solid fa-file-pdf',     'color' => 'file-pdf'],
                        'doc'  => ['icon' => 'fa-solid fa-file-word',    'color' => 'file-word'],
                        'docx' => ['icon' => 'fa-solid fa-file-word',    'color' => 'file-word'],
                        'xls'  => ['icon' => 'fa-solid fa-file-excel',   'color' => 'file-excel'],
                        'xlsx' => ['icon' => 'fa-solid fa-file-excel',   'color' => 'file-excel'],
                        'zip'  => ['icon' => 'fa-solid fa-file-zipper',  'color' => 'file-zip'],
                        'rar'  => ['icon' => 'fa-solid fa-file-zipper',  'color' => 'file-zip'],
                        'jpg'  => ['icon' => 'fa-solid fa-image',        'color' => 'file-image'],
                        'jpeg' => ['icon' => 'fa-solid fa-image',        'color' => 'file-image'],
                        'png'  => ['icon' => 'fa-solid fa-image',        'color' => 'file-image'],
                        'gif'  => ['icon' => 'fa-solid fa-image',        'color' => 'file-image'],
                        'txt'  => ['icon' => 'fa-solid fa-file-lines',   'color' => 'file-text'],
                        'csv'  => ['icon' => 'fa-solid fa-file-csv',     'color' => 'file-excel'],
                    ];
                    $file = $fileMap[$ext] ?? [
                        'icon'  => 'fa-solid fa-file',
                        'color' => 'file-default',
                    ];
                    $bytes = $att->kba_file_size ?? 0;
                    $size = $bytes >= 1048576
                        ? round($bytes / 1048576, 1) . ' MB'
                        : ($bytes >= 1024
                            ? round($bytes / 1024, 1) . ' KB'
                            : $bytes . ' B');
                @endphp
                <div class="kb-attach-item">
                    <div class="kb-attach-icon {{ $file['color'] }}">
                        <i class="{{ $file['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="kb-attach-name"
                            style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $att->kba_original_name }}
                        </div>
                        <div class="kb-attach-size">{{ strtoupper($ext) }} · {{ $size }}</div>
                    </div>
                    <a href="{{ route('user.kb.download', $att->id) }}"
                    class="kb-attach-btn"
                    download="{{ $att->kba_original_name }}">
                        <i class="fa-solid fa-download"></i> Unduh
                    </a>
                </div>
            @endforeach
        </div>
        @endif

        @php
            $tags = is_array($artikel->kba_tags)
                ? $artikel->kba_tags
                : (is_string($artikel->kba_tags) ? explode(',', $artikel->kba_tags) : []);
            $tags = array_filter(array_map('trim', $tags));
        @endphp
        @if(count($tags) > 0)
        <div class="kb-section-card">
            <div class="kb-section-title">
                <i class="fa-solid fa-tags"></i> Tags
            </div>
            @foreach($tags as $tag)
                <a href="{{ route('user.kb.index') }}?q={{ urlencode($tag) }}" class="kb-art-tag text-decoration-none">{{ $tag }}</a>
            @endforeach
        </div>
        @endif

        @if(isset($related) && $related->count() > 0)
        <div class="kb-section-card">
            <div class="kb-section-title">
                <i class="fa-solid fa-link"></i> Artikel Terkait
            </div>
            @foreach($related as $rel)
            @php
                $relExcerpt = strip_tags($rel->kba_konten);
                $relExcerpt = strlen($relExcerpt) > 80 ? substr($relExcerpt, 0, 80) . '...' : $relExcerpt;
            @endphp
            <a href="{{ route('user.kb.detail', $rel->kba_slug ?? $rel->id) }}"
               class="d-flex align-items-center gap-10 mb-2 text-decoration-none"
               style="padding:8px 10px;background:#fff;border:1px solid #e9d5ff;border-radius:10px;">
                <div style="width:32px;height:32px;border-radius:8px;background:#ede9fe;color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;font-weight:700;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $rel->kba_title }}</div>
                    <div style="font-size:10px;color:#6b7280;">{{ $relExcerpt }}</div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color:#a78bfa;font-size:12px;flex-shrink:0;"></i>
            </a>
            @endforeach
        </div>
        @endif

    </div>
</div>
<div id="kbImgOverlay" class="kb-img-overlay">
    <img src="" alt="Preview">
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
    $('.kb-rte-content img').each(function () {
        $(this).removeAttr('width').removeAttr('height');

        const currentStyle = $(this).attr('style') || '';
        const cleaned = currentStyle
            .replace(/width\s*:\s*[^;]+;?/gi, '')
            .replace(/height\s*:\s*[^;]+;?/gi, '')
            .trim();

        if (cleaned) {
            $(this).attr('style', cleaned);
        } else {
            $(this).removeAttr('style');
        }
    });

    $('.kb-rte-content a').each(function () {
        const href = $(this).attr('href') || '';
        if (href.startsWith('http')) {
            $(this).attr('target', '_blank').attr('rel', 'noopener noreferrer');
        }
    });
});

$('.kb-rte-content img').each(function () {
    $(this).removeAttr('width height');
    const src = $(this).attr('src');
    $(this).wrap(`<div class="kb-img-wrap"></div>`);
    $(this).parent().on('click', function () {
        $('#kbImgOverlay').find('img').attr('src', src);
        $('#kbImgOverlay').addClass('show');
    });
});

$(document).on('click', '#kbImgOverlay', function () {
    $(this).removeClass('show');
});
</script>
@endsection