@extends('admin.layouts.dashboard')

@section('isi')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Sub Kategori Dokumen Legal</h5>
            <a href="{{ route('dokumen_legal.kategori.kategori_sub.index', $holding->holding_code) }}"
                class="btn btn-secondary btn-sm">
                Kembali
            </a>
        </div>

        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th width="30%">ID SUB KATEGORI</th>
                    <td>{{ $subKategoriDokumen->id_sub }}</td>
                </tr>
                <tr>
                    <th width="30%">NAMA SUB KATEGORI</th>
                    <td>{{ $subKategoriDokumen->sub_name }}</td>
                </tr>
                <tr>
                    <th width="30%">NAMA KATEGORI</th>
                    <td>{{ $subKategoriDokumen->kategori->nama_kategori }}</td>
                    </thead>
            </table>

        </div>
    </div>

</div>
@endsection