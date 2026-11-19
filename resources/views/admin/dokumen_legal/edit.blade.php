@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Dokumen Legal</h5>
                <a href="{{ route('dokumen_legal.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>

            <div class="card-body">
                <form id="form_dokumen_legal" method="POST"
                    action="{{ route('dokumen_legal.update', [$holding->holding_code, $document->id]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kategori Dokumen</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" disabled
                                value="{{ $document->kategori->nama_kategori }}">

                            {{-- value real untuk backend --}}
                            <input type="hidden" name="kategori_id" value="{{ $document->kategori_id }}">
                        </div>
                    </div>

                    @if ($document->sub_kategori)
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Sub Kategori Dokumen</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" disabled
                                    value="{{ $document->sub_kategori->sub_name }}">

                                <input type="hidden" name="sub_kategori_id" value="{{ $document->sub_kategori_id }}">
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">
                            Penerbit Dokumen <span class="text-danger">*</span>
                        </label>

                        <div class="col-sm-6">
                            <input type="text" class="form-control" disabled
                                value="{{ $document->penerbit?->nama_penerbit ?? 'PENERBIT TIDAK DITEMUKAN' }}">

                            <input type="hidden" name="penerbit_id" value="{{ $document->penerbit_id }}">
                        </div>
                    </div>

                    @if ($document->is_dokumen_pengganti)
                        <div class="alert alert-info">
                            Dokumen ini adalah <b>dokumen pengganti</b>
                            @if ($document->referensi)
                                dari dokumen:
                                <a
                                    href="{{ route('dokumen_legal.show', [$holding->holding_code, $document->referensi->id]) }}">
                                    {{ $document->referensi->nomer_dukumen }}
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" name="nomer_dukumen"
                                class="form-control @error('nomer_dukumen') is-invalid @enderror"
                                value="{{ old('nomer_dukumen', $document->nomer_dukumen) }}">
                            @error('nomer_dukumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $document->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Site</label>
                        <div class="col-sm-6">
                            <select name="site_id" class="form-select @error('site_id') is-invalid @enderror">
                                <option value="">Pilih Site</option>

                                @foreach ($sites as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('site_id', $document->site_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->site_name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-4">
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="aktif"
                                    {{ old('status', $document->status) === 'aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="expired"
                                    {{ old('status', $document->status) === 'expired' ? 'selected' : '' }}>Expired
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Awal</label>
                        <div class="col-sm-4">
                            <input type="date" name="tanggal_awal"
                                class="form-control @error('tanggal_awal') is-invalid @enderror"
                                value="{{ old('tanggal_awal', optional($document->tanggal_awal)->format('Y-m-d')) }}">
                            @error('tanggal_awal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tanggal Akhir</label>
                        <div class="col-sm-4">
                            <input type="date" name="tanggal_akhir"
                                class="form-control @error('tanggal_akhir') is-invalid @enderror"
                                value="{{ old('tanggal_akhir', optional($document->tanggal_akhir)->format('Y-m-d')) }}">
                            @error('tanggal_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-6">
                            <input type="text" name="keterangan"
                                class="form-control @error('keterangan') is-invalid @enderror"
                                value="{{ old('keterangan', $document->keterangan) }}">
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-2 col-form-label">File Dokumen</label>
                        <div class="col-sm-6">
                            <input type="file" name="file"
                                class="form-control @error('file') is-invalid @enderror">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted d-block mt-2">
                                PDF / DOC / DOCX (max 5MB). Kalau tidak upload, file lama tetap dipakai.
                            </small>

                            @if ($document->file)
                                <small class="d-block mt-1">
                                    File saat ini:
                                    <a href="{{ asset('storage/' . $document->file) }}" target="_blank">
                                        Lihat/Download
                                    </a>
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" id="btn_simpan">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('form_dokumen_legal').addEventListener('submit', function() {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const kategoriSelect = document.getElementById('kategori_id');
            const subWrapper = document.getElementById('subKategoriWrapper');
            const subSelect = document.getElementById('sub_kategori_id');

            if (!kategoriSelect || !subWrapper || !subSelect) return;

            const selectedSubFromDB = "{{ old('sub_kategori_id', $document->sub_kategori_id ?? '') }}";

            function resetSub() {
                subSelect.innerHTML = '<option value="">-- Pilih Sub Kategori --</option>';
                subWrapper.style.display = 'none';
            }

            function loadSubKategori(kategoriId) {
                resetSub();
                if (!kategoriId) return;

                fetch(`/dokumen-legal/ajax/sub-kategori/${kategoriId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            subWrapper.style.display = 'flex';

                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.id_sub;
                                opt.textContent = item.sub_name;

                                if (item.id_sub == selectedSubFromDB) {
                                    opt.selected = true;
                                }

                                subSelect.appendChild(opt);
                            });
                        }
                    })
                    .catch(() => resetSub());
            }

            // ketika kategori diganti manual
            kategoriSelect.addEventListener('change', function() {
                loadSubKategori(this.value);
            });

            // AUTO LOAD SAAT EDIT PAGE
            if (kategoriSelect.value) {
                loadSubKategori(kategoriSelect.value);
            }

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const penerbitSelect = document.getElementById('penerbit_select');
            const penerbitHidden = document.getElementById('penerbit_hidden');

            // set hidden value saat load
            if (penerbitSelect && penerbitHidden) {
                penerbitHidden.value = penerbitSelect.value;

                penerbitSelect.addEventListener('change', function() {
                    penerbitHidden.value = this.value;
                });
            }
        });
    </script>
@endsection
