@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ================= CARD DETAIL KATEGORI ================= --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Kategori Dokumen Legal</h5>
                <a href="{{ route('dokumen_legal.kategori.kategori_master.index', $holding->holding_code) }}"
                    class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>

            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th width="30%">ID Kategori</th>
                        <td>{{ $kategoriDokumen->id_kategori }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kategori</th>
                        <td>{{ $kategoriDokumen->nama_kategori }}</td>
                    </tr>
                    <tr>
                        <th>Reminder Days</th>
                        <td>{{ $kategoriDokumen->reminder_days }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ================= CARD SUB KATEGORI ================= --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Sub Kategori Dokumen</h6>

                {{-- BUTTON TAMBAH SUB KATEGORI (DARI SHOW KATEGORI) --}}
                <a href="{{ route('dokumen_legal.kategori.kategori_master.sub_kategori.create_from_kategori', [
                    $holding->holding_code,
                    $kategoriDokumen->id_kategori,
                ]) }}"
                    class="btn btn-primary btn-sm">
                    + Tambah Sub Kategori
                </a>
            </div>

            <div class="card-body">
                @if ($kategoriDokumen->subKategori->count() > 0)
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">ID Sub</th>
                                <th>Nama Sub Kategori</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoriDokumen->subKategori as $sub)
                                <tr>
                                    <td>{{ $sub->id_sub }}</td>
                                    <td>{{ $sub->sub_name }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('dokumen_legal.kategori.kategori_sub.edit', [$holding->holding_code, $sub->id_sub]) }}?from_kategori=1&redirect_kategori_id={{ $kategoriDokumen->id_kategori }}"
                                            class="btn btn-warning btn-sm">
                                            EDIT
                                        </a>

                                        <form method="POST"
                                            action="{{ route('dokumen_legal.kategori.kategori_sub.destroy', [$holding->holding_code, $sub->id_sub]) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')

                                            <input type="hidden" name="from_kategori" value="1">
                                            <input type="hidden" name="redirect_kategori_id"
                                                value="{{ $kategoriDokumen->id_kategori }}">

                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin hapus sub kategori ini?')">
                                                HAPUS
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-3">
                        <div class="alert alert-info mb-0">
                            Belum ada sub kategori untuk kategori ini.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= CARD PENERBIT DOKUMEN ================= --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    Penerbit Dokumen
                    <span class="text-muted">
                        ({{ $kategoriDokumen->penerbitDokumen->count() }})
                    </span>
                </h6>

                {{-- BUTTON TAMBAH PENERBIT (DARI SHOW KATEGORI) --}}
                <a href="{{ route('dokumen_legal.kategori.kategori_master.penerbit.create_from_kategori', [
                    $holding->holding_code,
                    $kategoriDokumen->id_kategori,
                ]) }}"
                    class="btn btn-primary btn-sm">
                    + Tambah Penerbit
                </a>
            </div>

            <div class="card-body">
                @if ($kategoriDokumen->penerbitDokumen->count() > 0)
                    <table class="table table-bordered mb-0">
                        <thead class="bg-white">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Penerbit</th>
                                <th width="15%">Jenis</th>
                                <th>Alamat</th>
                                <th width="20%">PIC</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoriDokumen->penerbitDokumen as $i => $penerbit)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $penerbit->nama_penerbit }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($penerbit->jenis_penerbit) }}
                                        </span>
                                    </td>
                                    <td>{{ $penerbit->alamat_penerbit ?? '-' }}</td>
                                    <td>
                                        {{ $penerbit->nama_pic_penerbit ?? '-' }}
                                        @if ($penerbit->nomor_pic_penerbit)
                                            <div class="text-muted" style="font-size:12px;">
                                                {{ $penerbit->nomor_pic_penerbit }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- ================= AKSI ================= --}}
                                    <td class="text-center">

                                        {{-- EDIT --}}
                                        <a href="{{ route('dokumen_legal.penerbit.edit', [$holding->holding_code, $penerbit->id_penerbit]) }}?from_kategori=1&redirect_kategori_id={{ $kategoriDokumen->id_kategori }}"
                                            class="btn btn-warning btn-sm">
                                            EDIT
                                        </a>

                                        {{-- DELETE --}}
                                        <form method="POST"
                                            action="{{ route('dokumen_legal.penerbit.destroy', [$holding->holding_code, $penerbit->id_penerbit]) }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')

                                            <input type="hidden" name="from_kategori" value="1">
                                            <input type="hidden" name="redirect_kategori_id"
                                                value="{{ $kategoriDokumen->id_kategori }}">

                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin hapus penerbit ini?')">
                                                HAPUS
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-3">
                        <div class="alert alert-info mb-0">
                            Belum ada penerbit yang terhubung dengan kategori ini.
                        </div>
                    </div>
                @endif
            </div>
        </div>


    </div>
@endsection
