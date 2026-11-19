<div class="org-item">
    <div class="org-card">
        <h6 class="mb-0">{{ $jabatan->nama_jabatan }}</h6>

        @if($jabatan->karyawan)
        <small class="text-muted">
            {{ $jabatan->karyawan->name }}
        </small>
        @else
        <small class="text-muted fst-italic">
            (Belum diisi)
        </small>
        @endif
    </div>

    @if($jabatan->children->count())
    <div class="org-children">
        @foreach($jabatan->children as $child)
        @include('profile.partials.org-item', ['jabatan' => $child])
        @endforeach
    </div>
    @endif
</div>