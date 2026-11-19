<li class="timeline-item">
    <a href="javascript:void(0);" class="btn_download">
        <div class="line-content-box">
            <h6>
                {{ $title }}

                @if($item->status_kontrak == 1)
                <span class="badge light badge-success ms-1">Aktif</span>
                @else
                <span class="badge light badge-danger ms-1">Expired</span>
                @endif
            </h6>

            <small class="text-muted d-block mb-2">
                {{ $item->tanggal_mulai }} — {{ $item->tanggal_selesai }}
            </small>
        </div>
    </a>

    <div class="line-num">{{ $number++ }}</div>
</li>

{{-- 🔁 RECURSIVE --}}
@if($item->children && $item->children->count())
@foreach($item->children as $i => $child)
@include('users.profile.timeline_kontrak', [
'item' => $child,
'title' => 'Perpanjangan Kontrak',
'number' => &$number
])
@endforeach
@endif