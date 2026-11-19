<div class="grid grid-cols-2 gap-4">

    <input name="id_kategori" value="{{ old('id_kategori', $legalDocument->id_kategori ?? '') }}"
        placeholder="ID Kategori">

    <input name="nama_kategori" value="{{ old('nama_kategori', $legalDocument->nama_kategori ?? '') }}" placeholder="Nama Kategori">

</div>
