@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Edit Task</h5>
                    <small class="text-muted">{{ $task->task_code }} - {{ $project->project_name }}</small>
                </div>
                <a href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}"
                    class="btn btn-secondary btn-sm">Kembali</a>
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

                <form method="POST"
                    action="{{ route('project_management.tasks.update', [$holding->holding_code, $project->id, $task->id]) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.project_management.tasks._form', ['task' => $task])

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}"
                            class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
