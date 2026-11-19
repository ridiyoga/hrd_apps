<div class="row g-3">

    {{-- NAMA PENERBIT --}}
    <div class="col-md-6">
        <label class="form-label">Nama Penerbit <span class="text-danger">*</span></label>
        <input type="text" name="nama_penerbit" class="form-control @error('nama_penerbit') is-invalid @enderror"
            value="{{ old('nama_penerbit', $penerbitDokumen->nama_penerbit ?? '') }}" required>
        @error('nama_penerbit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- JENIS PENERBIT --}}
    <div class="col-md-6">
        <label class="form-label">Jenis Penerbit <span class="text-danger">*</span></label>
        <select name="jenis_penerbit" class="form-select @error('jenis_penerbit') is-invalid @enderror" required>
            <option value="">-- Pilih Jenis --</option>
            @foreach (['internal', 'eksternal', 'pemerintah'] as $jenis)
                <option value="{{ $jenis }}" @selected(old('jenis_penerbit', $penerbitDokumen->jenis_penerbit ?? '') == $jenis)>
                    {{ ucfirst($jenis) }}
                </option>
            @endforeach
        </select>
        @error('jenis_penerbit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- KATEGORI DOKUMEN --}}
    <div class="col-md-6">
        <label class="form-label">Kategori Dokumen <span class="text-danger">*</span></label>

        {{-- MODE: DARI SHOW KATEGORI --}}
        @if (!empty($isFromKategori) && $isFromKategori)
            <input type="text" class="form-control" value="{{ $kategori->nama_kategori }}" readonly>

            {{-- hidden supaya tetap terkirim --}}
            <input type="hidden" name="kategori_dokumen_id" value="{{ $kategori->id_kategori }}">

            {{-- context redirect --}}
            <input type="hidden" name="from_kategori" value="1">
            <input type="hidden" name="redirect_kategori_id" value="{{ $kategori->id_kategori }}">
        @else
            {{-- MODE: GLOBAL --}}
            <select name="kategori_dokumen_id" class="form-select @error('kategori_dokumen_id') is-invalid @enderror"
                required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriDokumen as $kat)
                    <option value="{{ $kat->id_kategori }}" @selected(old('kategori_dokumen_id', $penerbitDokumen->kategori_dokumen_id ?? '') == $kat->id_kategori)>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        @endif

        @error('kategori_dokumen_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ALAMAT --}}
    <div class="col-md-6">
        <label class="form-label">Alamat Penerbit</label>
        <textarea name="alamat_penerbit" class="form-control" rows="2">{{ old('alamat_penerbit', $penerbitDokumen->alamat_penerbit ?? '') }}</textarea>
    </div>

    {{-- PIC --}}
    <div class="col-md-6">
        <label class="form-label">Nama PIC</label>
        <input type="text" name="nama_pic_penerbit" class="form-control"
            value="{{ old('nama_pic_penerbit', $penerbitDokumen->nama_pic_penerbit ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Nomor PIC</label>
        <input type="text" name="nomor_pic_penerbit" class="form-control"
            value="{{ old('nomor_pic_penerbit', $penerbitDokumen->nomor_pic_penerbit ?? '') }}">
    </div>

</div>
