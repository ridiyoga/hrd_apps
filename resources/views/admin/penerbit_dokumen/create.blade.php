@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tambah Penerbit Dokumen</h5>
            </div>

            <form action="{{ route('dokumen_legal.penerbit.store', $holding->holding_code) }}" method="POST">
                @csrf

                <div class="card-body">
                    @include('admin.penerbit_dokumen._form', [
                        'penerbitDokumen' => null,
                        'isFromKategori' => $isFromKategori ?? false,
                        'kategori' => $kategori ?? null,
                    ])
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('dokumen_legal.penerbit.index', $holding->holding_code) }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
