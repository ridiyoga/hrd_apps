@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            {{-- HEADER --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Sub Kategori Dokumen Legal</h5>
                <a href="{{ route('dokumen_legal.kategori.kategori_sub.index', $holding->holding_code) }}"
                    class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>

            {{-- BODY --}}
            <div class="card-body">
                <form id="form_sub_kategori_dokumen_legal" method="POST"
                    action="{{ route('dokumen_legal.kategori.kategori_sub.update', [$holding->holding_code, $subKategoriDokumen->id_sub]) }}">
                    @csrf

                    @if ($isFromKategori)
                        <input type="hidden" name="from_kategori" value="1">
                        <input type="hidden" name="redirect_kategori_id" value="{{ $redirectKategoriId }}">
                    @endif

                    @method('PUT')

                    {{-- ID SUB (DISPLAY ONLY) --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">ID Sub Kategori Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control-plaintext" readonly
                                value="{{ $subKategoriDokumen->id_sub }}">
                        </div>
                    </div>

                    {{-- NAMA SUB KATEGORI --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Sub Kategori Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" name="sub_name"
                                class="form-control @error('sub_name') is-invalid @enderror"
                                value="{{ old('sub_name', $subKategoriDokumen->sub_name) }}" required>

                            @error('sub_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- KATEGORI --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kategori</label>
                        <div class="col-sm-6">
                            <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror"
                                required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($KategoriDokumenList as $kategori)
                                    <option value="{{ $kategori->id_kategori }}"
                                        {{ old('kategori_id', $subKategoriDokumen->kategori_id) == $kategori->id_kategori ? 'selected' : '' }}>
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>

                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ACTION --}}
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
        document.getElementById('form_sub_kategori_dokumen_legal')
            .addEventListener('submit', function() {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            });
    </script>
@endsection
