@extends('admin.layouts.dashboard')

@section('isi')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-2">
            <h5 class="mb-0">Report Project Management</h5>
            <div class="d-flex gap-2">
                @if(request()->filled('project_id'))
                    <a class="btn btn-primary btn-sm js-report-download" data-no-loading data-fallback-name="laporan-penyelesaian-project.pdf"
                        href="{{ route('project_management.report', array_merge(request()->except('page', 'export', 'holding'), ['holding' => $holding->holding_code, 'export' => 'pdf'])) }}"><i class="mdi mdi-file-pdf-box"></i> Unduh Laporan Penyelesaian</a>
                @else
                    <button type="button" class="btn btn-primary btn-sm" disabled title="Pilih satu project terlebih dahulu"><i class="mdi mdi-file-pdf-box"></i> Unduh Laporan Penyelesaian</button>
                @endif
                <a class="btn btn-success btn-sm js-report-download" data-no-loading data-fallback-name="report-project-management.csv"
                    href="{{ route('project_management.report', array_merge(request()->except('page', 'export', 'holding'), ['holding' => $holding->holding_code, 'export' => 'csv'])) }}"><i class="mdi mdi-download"></i> Ekspor CSV</a>
                <a class="btn btn-secondary btn-sm" href="{{ route('project_management.index', $holding->holding_code) }}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form method="GET" action="{{ route('project_management.report', $holding->holding_code) }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">Semua Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(request('project_id') === $project->id)>{{ $project->project_code }} - {{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>
                @foreach (['assignee_id' => 'Assignee', 'reviewer_id' => 'Reviewer'] as $field => $label)
                    <div class="col-md-4">
                        <label class="form-label">{{ $label }}</label>
                        <select name="{{ $field }}" class="form-select">
                            <option value="">Semua {{ $label }}</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(request($field) === $user->id)>{{ $user->username }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                <div class="col-md-3">
                    <label class="form-label">Status Task</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Penyampaian via MoM</label>
                    <select name="presentation" class="form-select">
                        <option value="">Semua</option>
                        <option value="pending" @selected(request('presentation') === 'pending')>Belum Disampaikan</option>
                        <option value="presented" @selected(request('presentation') === 'presented')>Sudah Disampaikan</option>
                    </select>
                </div>
                @foreach (['due_from' => 'Due Aktif Dari', 'due_to' => 'Due Aktif Sampai'] as $field => $label)
                    <div class="col-md-3"><label class="form-label">{{ $label }}</label><input type="date" name="{{ $field }}" value="{{ request($field) }}" class="form-control"></div>
                @endforeach
                <div class="col-md-3">
                    <label class="form-label">Data per Halaman</label>
                    <select name="per_page" class="form-select">
                        @foreach ([25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', 25) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('project_management.report', $holding->holding_code) }}">Reset</a>
                    <button class="btn btn-primary" type="submit">Filter</button>
                </div>
            </form>
            <div class="row g-3 mb-3">
                @foreach (['Total Task' => $totals->total, 'Estimasi (MD)' => number_format($totals->estimate / 8, 2), 'Aktual (MD)' => number_format($totals->actual / 8, 2)] as $label => $value)
                    <div class="col-md-4"><div class="border rounded p-3"><small>{{ $label }}</small><div class="fw-semibold">{{ $value }}</div></div></div>
                @endforeach
            </div>
            <div class="d-flex flex-wrap gap-3 mb-3">
                @foreach ($statusOptions as $value => $label)
                    <span>{{ $label }} <span class="badge bg-secondary">{{ $statusCounts[$value] ?? 0 }}</span></span>
                @endforeach
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary"><tr>@foreach ($columns as $column)<th class="text-nowrap">{{ $column }}</th>@endforeach<th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                @foreach ($row($task) as $value)<td style="min-width:110px; max-width:320px; overflow-wrap:anywhere;">{{ $value }}</td>@endforeach
                                <td><a class="btn btn-info btn-sm" title="Detail Task" href="{{ route('project_management.tasks.show', [$task->project->holding->holding_code, $task->project_id, $task->id]) }}"><i class="mdi mdi-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="17" class="text-center text-muted">Tidak ada data sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <small>Menampilkan {{ $tasks->firstItem() ?? 0 }} - {{ $tasks->lastItem() ?? 0 }} dari {{ $tasks->total() }} data</small>
                <nav aria-label="Halaman report"><ul class="pagination mb-0">
                    <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}"><a class="page-link" href="{{ $tasks->previousPageUrl() ?? '#' }}">Sebelumnya</a></li>
                    <li class="page-item disabled"><span class="page-link">{{ $tasks->currentPage() }} / {{ $tasks->lastPage() }}</span></li>
                    <li class="page-item {{ !$tasks->hasMorePages() ? 'disabled' : '' }}"><a class="page-link" href="{{ $tasks->nextPageUrl() ?? '#' }}">Berikutnya</a></li>
                </ul></nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.querySelectorAll('.js-report-download').forEach(function (link) {
        link.addEventListener('click', async function (event) {
            event.preventDefault();

            if (link.dataset.downloading === 'true') return;

            const originalHtml = link.innerHTML;
            link.dataset.downloading = 'true';
            link.classList.add('disabled');
            link.setAttribute('aria-disabled', 'true');
            link.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Menyiapkan file...';

            try {
                const response = await fetch(link.href, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) {
                    throw new Error('Server mengembalikan status ' + response.status + '.');
                }

                const contentType = response.headers.get('Content-Type') || '';
                if (!contentType.includes('application/pdf') && !contentType.includes('text/csv')) {
                    throw new Error('Sesi berakhir atau format file tidak sesuai. Muat ulang halaman lalu coba kembali.');
                }

                const disposition = response.headers.get('Content-Disposition') || '';
                const encodedName = disposition.match(/filename\*=UTF-8''([^;]+)/i);
                const plainName = disposition.match(/filename="?([^";]+)"?/i);
                const fileName = encodedName
                    ? decodeURIComponent(encodedName[1])
                    : (plainName ? plainName[1] : link.dataset.fallbackName);
                const blobUrl = URL.createObjectURL(await response.blob());
                const download = document.createElement('a');
                download.href = blobUrl;
                download.download = fileName;
                document.body.appendChild(download);
                download.click();
                download.remove();
                setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 1000);
            } catch (error) {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Unduhan gagal', text: error.message });
                } else {
                    alert('Unduhan gagal: ' + error.message);
                }
            } finally {
                link.innerHTML = originalHtml;
                link.classList.remove('disabled');
                link.removeAttribute('aria-disabled');
                delete link.dataset.downloading;
                window.hideGlobalLoading?.();
            }
        });
    });
</script>
@endpush
