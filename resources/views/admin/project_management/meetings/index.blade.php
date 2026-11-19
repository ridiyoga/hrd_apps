@extends('admin.layouts.dashboard')

@section('isi')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Minutes of Meeting</h5>
                <small class="text-muted">{{ $project->project_code }} - {{ $project->project_name }}</small>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-primary btn-sm" href="{{ route('project_management.meetings.create', [$holding->holding_code, $project->id]) }}">+ Buat MoM</a>
                <a class="btn btn-secondary btn-sm" href="{{ route('project_management.show', [$holding->holding_code, $project->id]) }}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Kode</th><th>Meeting</th><th>Status</th><th>Waktu</th><th>Milestone</th>
                            <th>Summary</th><th>Task Dibahas</th><th>Task Baru</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetings as $meeting)
                            @php
                                $statusClass = match($meeting->status) {
                                    'final' => 'success',
                                    'cancelled' => 'secondary',
                                    default => 'warning',
                                };
                            @endphp
                            <tr>
                                <td>{{ $meeting->meeting_code }}</td>
                                <td><strong>{{ $meeting->title }}</strong><br><small class="text-muted">{{ strtoupper($meeting->meeting_type) }}</small></td>
                                <td><span class="badge bg-{{ $statusClass }}">{{ strtoupper($meeting->status) }}</span></td>
                                <td>{{ $meeting->meeting_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $meeting->milestone?->milestone_name ?? 'Umum' }}</td>
                                <td style="min-width:240px">{{ $meeting->summary ? \Illuminate\Support\Str::limit($meeting->summary, 140) : 'Diisi setelah pembahasan selesai.' }}</td>
                                <td>{{ $meeting->tasks_count }}</td>
                                <td>{{ $meeting->action_items_count }}</td>
                                <td><div class="d-flex flex-wrap gap-1"><a class="btn btn-info btn-sm" href="{{ route('project_management.meetings.show', [$holding->holding_code, $project->id, $meeting->id]) }}">Detail</a><a class="btn btn-outline-info btn-sm" target="_blank" href="{{ route('project_management.meetings.pdf', [$holding->holding_code, $project->id, $meeting->id, 'preview' => 1]) }}">Preview</a><a class="btn btn-outline-primary btn-sm" href="{{ route('project_management.meetings.pdf', [$holding->holding_code, $project->id, $meeting->id]) }}">Unduh PDF</a></div></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada Minutes of Meeting.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $meetings->links() }}
        </div>
    </div>
</div>
@endsection
