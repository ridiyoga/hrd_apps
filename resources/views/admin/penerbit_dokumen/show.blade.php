@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">

            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Detail Penerbit Dokumen</h5>
                <a href="{{ route('dokumen_legal.penerbit.index', $holding->holding_code) }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nama Penerbit</th>
                        <td>{{ $penerbitDokumen->nama_penerbit }}</td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td>{{ ucfirst($penerbitDokumen->jenis_penerbit) }}</td>
                    </tr>
                    <tr>
                        <th>Kategori Dokumen</th>
                        <td>{{ $penerbitDokumen->kategoriDokumen->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $penerbitDokumen->alamat_penerbit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama PIC</th>
                        <td>{{ $penerbitDokumen->nama_pic_penerbit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nomor PIC</th>
                        <td>{{ $penerbitDokumen->nomor_pic_penerbit ?? '-' }}</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
@endsection
