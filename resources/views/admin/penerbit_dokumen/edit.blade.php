@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Penerbit Dokumen</h5>
            </div>

            <form
                action="{{ route('dokumen_legal.penerbit.update', [$holding->holding_code, $penerbitDokumen->id_penerbit]) }}"
                method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('admin.penerbit_dokumen._form')
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('dokumen_legal.penerbit.index', $holding->holding_code) }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
