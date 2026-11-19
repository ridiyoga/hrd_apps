@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Tambah Sub Kategori Dokumen</h5>
                <a href="{{ route('dokumen_legal.kategori.kategori_sub.index', $holding->holding_code) }}"
                    class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>

            <div class="card-body">
                <form id="form-sub-kategori-dokumen-legal" method="POST"
                    action="{{ route('dokumen_legal.kategori.kategori_sub.store', $holding->holding_code) }}">
                    @csrf

                    {{-- ================= PENANDA ASAL FORM ================= --}}
                    @if (isset($isFromKategori) && $isFromKategori)
                        <input type="hidden" name="from_kategori" value="1">
                        <input type="hidden" name="redirect_kategori_id" value="{{ $kategori->id_kategori }}">
                    @endif

                    {{-- ================= ID SUB KATEGORI ================= --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">ID Sub Kategori Dokumen</label>
                        <div class="col-sm-6">
                            <input type="number" name="id_sub" class="form-control @error('id_sub') is-invalid @enderror"
                                value="{{ old('id_sub') }}" required>
                            @error('id_sub')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ================= NAMA SUB KATEGORI ================= --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama Sub Kategori Dokumen</label>
                        <div class="col-sm-6">
                            <input type="text" name="sub_name"
                                class="form-control @error('sub_name') is-invalid @enderror" value="{{ old('sub_name') }}"
                                required>
                            @error('sub_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ================= KATEGORI ================= --}}
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Kategori</label>
                        <div class="col-sm-6">

                            {{-- MODE: DARI SHOW KATEGORI --}}
                            @if ($isFromKategori)
                                <input type="text" class="form-control" value="{{ $kategori->nama_kategori }}" readonly>

                                {{-- hidden agar tetap terkirim ke store --}}
                                <input type="hidden" name="kategori_id" value="{{ $kategori->id_kategori }}">
                            @else
                                {{-- MODE: GLOBAL --}}
                                <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($kategoriDokumenList as $kat)
                                        <option value="{{ $kat->id_kategori }}"
                                            {{ old('kategori_id') == $kat->id_kategori ? 'selected' : '' }}>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif

                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ================= SUBMIT ================= --}}
                    <div class="text-end">
                        <button id="btn-submit" type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('form-sub-kategori-dokumen-legal').addEventListener('submit', function() {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            document.getElementById('btn-submit').disabled = true;
        });
    </script>
@endsection
