@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tambah Project Management</h5>
                <a href="{{ route('project_management.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('project_management.store', $holding->holding_code) }}">
                    @csrf
                    @include('admin.project_management._form')

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('project_management.index', $holding->holding_code) }}"
                            class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan & Lanjut Detail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
