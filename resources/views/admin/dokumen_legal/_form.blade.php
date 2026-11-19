<div class="grid grid-cols-2 gap-4">

    <input name="nomer_dukumen" value="{{ old('nomer_dukumen', $legalDocument->nomer_dukumen ?? '') }}"
        placeholder="Nomor Dokumen">

    <input name="nama" value="{{ old('nama', $legalDocument->nama ?? '') }}" placeholder="Nama Dokumen">

    <input type="file" name="file">

    <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal', $legalDocument->tanggal_awal ?? '') }}">

    <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir', $legalDocument->tanggal_akhir ?? '') }}">

    <select name="status">
        @foreach (['aktif', 'expired'] as $s)
            <option value="{{ $s }}" @selected(old('status', $legalDocument->status ?? '') == $s)>
                {{ ucfirst($s) }}
            </option>
        @endforeach
    </select>

    <textarea name="keterangan" placeholder="Keterangan">{{ old('keterangan', $legalDocument->keterangan ?? '') }}</textarea>

    <input name="kategori_id" value="{{ old('kategori_id', $legalDocument->kategori_id ?? '') }}"
        placeholder="Kategori ID">

    <select name="dokumen_reference">
        <option value="">-- Dokumen Induk --</option>
        @foreach ($parents ?? [] as $p)
            <option value="{{ $p->id }}" @selected(old('dokumen_reference', $legalDocument->dokumen_reference ?? '') == $p->id)>
                {{ $p->nama }}
            </option>
        @endforeach
    </select>

    <input name="holding_id" value="{{ old('holding_id', $legalDocument->holding_id ?? '') }}" placeholder="Holding ID">

    <input name="site_id" value="{{ old('site_id', $legalDocument->site_id ?? '') }}" placeholder="Site ID">

</div>
