@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Project Management</h5>
                <a href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}"
                    class="btn btn-secondary btn-sm">
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

                <form method="POST" action="{{ route('project_management.update', [$holding->holding_code, $project->id]) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.project_management._form', ['project' => $project])

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}"
                            class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
