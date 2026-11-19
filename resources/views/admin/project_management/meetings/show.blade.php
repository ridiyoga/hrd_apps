@extends('admin.layouts.dashboard')

@section('isi')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-1">{{ $meeting->title }}</h5>
                <small class="text-muted">{{ $meeting->meeting_code }} - {{ $project->project_name }}</small>
                <span class="badge bg-{{ $meeting->status === 'final' ? 'success' : ($meeting->status === 'cancelled' ? 'secondary' : 'warning') }} ms-2">{{ strtoupper($meeting->status) }}</span>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-info btn-sm" target="_blank" href="{{ route('project_management.meetings.pdf', [$holding->holding_code, $project->id, $meeting->id, 'preview' => 1]) }}">Preview</a>
                <a class="btn btn-outline-primary btn-sm js-mom-download" data-redirect="{{ route('project_management.meetings.index', [$holding->holding_code, $project->id]) }}" href="{{ route('project_management.meetings.pdf', [$holding->holding_code, $project->id, $meeting->id]) }}">Unduh PDF</a>
                @if($canEdit)<a class="btn btn-warning btn-sm" href="{{ route('project_management.meetings.edit', [$holding->holding_code, $project->id, $meeting->id]) }}">Edit</a>@endif
                <a class="btn btn-secondary btn-sm" href="{{ route('project_management.meetings.index', [$holding->holding_code, $project->id]) }}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Jenis Meeting</small><div class="fw-semibold">{{ strtoupper($meeting->meeting_type) }}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Tanggal & Jam</small><div class="fw-semibold">{{ $meeting->meeting_at->format('d/m/Y H:i') }}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Milestone</small><div class="fw-semibold">{{ $meeting->milestone?->milestone_name ?? 'Umum / Seluruh Project' }}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Lokasi / Media</small><div class="fw-semibold">{{ $meeting->location ?: '-' }}</div></div></div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <table class="table table-bordered align-middle">
                        <tr><th style="width:210px">Agenda</th><td>{!! nl2br(e($meeting->agenda)) !!}</td></tr>
                        <tr><th>Summary MoM</th><td>{!! nl2br(e($meeting->summary ?: 'Belum diisi. Summary dilengkapi saat finalisasi MoM.')) !!}</td></tr>
                    </table>

                    <div class="d-flex justify-content-between align-items-end mt-4 mb-2"><h6 class="mb-0">Task yang Dibahas</h6><small class="text-muted">Tambahkan satu per satu; satu MoM dapat memuat banyak task.</small></div>
                    @if($canEdit)
                    <form method="POST" action="{{ route('project_management.meetings.tasks.store',[$holding->holding_code,$project->id,$meeting->id]) }}" class="border rounded p-3 mb-3">@csrf
                        <div class="row g-2">
                            <div class="col-12"><label class="form-label">Task Done yang Belum Disampaikan *</label><select name="task_id" class="form-select" required><option value="">-- Pilih Task --</option>@foreach($availableTasks as $task)<option value="{{ $task->id }}">{{ $task->task_code }} - {{ $task->title }}</option>@endforeach</select>@if($availableTasks->isEmpty())<small class="text-muted">Tidak ada task Done yang menunggu pembahasan.</small>@endif</div>
                            <div class="col-md-7"><label class="form-label">Catatan Pembahasan *</label><textarea name="discussion_notes" rows="3" class="form-control" required></textarea></div>
                            <div class="col-md-5"><label class="form-label">Keputusan *</label><select name="decision" class="form-select" required><option value="">-- Pilih --</option><option value="accepted">Done / Diterima</option><option value="return_developer">Return ke Developer</option></select><label class="form-label mt-2">Due Revisi</label><input type="date" name="revision_due_date" class="form-control"><small class="text-muted">Bebas menentukan tanggal sesuai kesepakatan meeting.</small></div>
                            <div class="col-12"><label class="form-label">Ekspektasi Perbaikan</label><textarea name="return_expectation" rows="2" class="form-control" placeholder="Wajib jika keputusan Return ke Developer"></textarea></div>
                            <div class="col-12 text-end"><button class="btn btn-primary btn-sm" @disabled($availableTasks->isEmpty())>Simpan Pembahasan Task</button></div>
                        </div>
                    </form>
                    @endif
                    <div class="table-responsive"><table class="table table-bordered align-middle"><thead class="table-primary"><tr><th>Task</th><th>Pembahasan</th><th>Keputusan</th><th>Ekspektasi / Due Revisi</th><th>Aksi</th></tr></thead><tbody>
                        @forelse($meeting->meetingTasks as $row) @php($task=$row->task)
                        <tr><td><strong>{{ $task?->task_code ?? '-' }}</strong><br>{{ $task?->title ?? 'Task telah dihapus' }}<br><small class="text-muted">{{ $task?->primaryAssignee?->username ?? '-' }}</small></td><td>{!! nl2br(e($row->discussion_notes ?: '-')) !!}</td><td><span class="badge bg-{{ $row->decision === 'accepted' ? 'success' : 'warning' }}">{{ $row->decision === 'accepted' ? 'Done / Diterima' : 'Return Developer' }}</span></td><td>{!! nl2br(e($row->return_expectation ?: '-')) !!}@if($row->revision_due_date)<br><small class="text-muted">Due: {{ $row->revision_due_date->format('d/m/Y') }}</small>@endif</td><td><div class="d-flex gap-1">@if($task)<a class="btn btn-info btn-sm" href="{{ route('project_management.tasks.show',[$holding->holding_code,$project->id,$task->id]) }}">Detail</a>@endif @if($canEdit)<form method="POST" action="{{ route('project_management.meetings.tasks.destroy',[$holding->holding_code,$project->id,$meeting->id,$row->id]) }}" onsubmit="return confirm('Hapus pembahasan task dari MoM?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>@endif</div></td></tr>
                        @empty<tr><td colspan="5" class="text-center text-muted">Tidak ada task yang dibahas.</td></tr>@endforelse
                    </tbody></table></div>

                    <div class="d-flex justify-content-between align-items-center mt-4 mb-2"><h6 class="mb-0">Task Baru</h6><small class="text-muted">Task baru hasil pembahasan meeting</small></div>
                    <div class="table-responsive"><table class="table table-bordered align-middle"><thead class="table-primary"><tr><th>Judul Task</th><th>PIC</th><th>Due</th><th>Status</th><th>Kode Task</th><th>Aksi</th></tr></thead><tbody>
                        @forelse($meeting->actionItems as $item)
                        <tr><td><strong>{{ $item->title }}</strong>@if($item->description)<br><small class="text-muted">{{ $item->description }}</small>@endif</td><td>{{ $item->pic?->username ?? '-' }}</td><td>{{ $item->due_date->format('d/m/Y') }}</td><td><span class="badge bg-{{ $item->status==='done'?'success':($item->status==='cancelled'?'secondary':'warning') }}">{{ ucwords(str_replace('_',' ',$item->status)) }}</span></td><td>@if($item->generatedTask)<a href="{{ route('project_management.tasks.show',[$holding->holding_code,$project->id,$item->generatedTask->id]) }}">{{ $item->generatedTask->task_code }}</a>@elseif($item->linkedTask)<a href="{{ route('project_management.tasks.show',[$holding->holding_code,$project->id,$item->linkedTask->id]) }}">{{ $item->linkedTask->task_code }}</a>@else-@endif</td><td>
                            @if($meeting->status === 'draft' && ($canEdit || $item->pic_id===auth()->id()))<form method="POST" action="{{ route('project_management.meetings.action_items.update',[$holding->holding_code,$project->id,$meeting->id,$item->id]) }}" class="d-flex gap-1 mb-1">@csrf @method('PATCH')<select name="status" class="form-select form-select-sm">@foreach(['open'=>'Open','in_progress'=>'In Progress','done'=>'Done','cancelled'=>'Cancelled'] as $value=>$label)<option value="{{ $value }}" @selected($item->status===$value)>{{ $label }}</option>@endforeach</select><button class="btn btn-outline-primary btn-sm">Simpan</button></form>@endif
                            @if($meeting->status === 'draft' && $canManageTasks && !$item->generated_task_id && !$item->linked_task_id)<form method="POST" action="{{ route('project_management.meetings.action_items.generate_task',[$holding->holding_code,$project->id,$meeting->id,$item->id]) }}">@csrf<button class="btn btn-primary btn-sm">Terbitkan Task</button></form>@endif
                        </td></tr>
                        @empty<tr><td colspan="6" class="text-center text-muted">Belum ada task baru.</td></tr>@endforelse
                    </tbody></table></div>

                    @if($canEdit)
                    <form method="POST" action="{{ route('project_management.meetings.action_items.store',[$holding->holding_code,$project->id,$meeting->id]) }}" class="border rounded p-3 mt-3">@csrf
                        <div class="d-flex justify-content-between align-items-end mb-2"><h6 class="mb-0">Tambah Task Baru</h6><small class="text-muted">Form dapat digunakan berulang untuk menambahkan beberapa task baru.</small></div>
                        <div class="row g-2">
                            <div class="col-md-5"><label class="form-label">Judul Task *</label><input name="title" class="form-control" required></div>
                            <div class="col-md-3"><label class="form-label">PIC *</label><select name="pic_id" class="form-select" required><option value="">-- Pilih --</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->username }} - {{ data_get($user,'KaryawanNIK.name') }}</option>@endforeach</select></div>
                            <div class="col-md-2"><label class="form-label">Estimasi *</label><input id="mom-estimate-value" type="number" name="estimate_value" min="0.125" step="0.125" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Satuan *</label><select id="mom-estimate-unit" name="estimate_unit" class="form-select" required><option value="mandays">Mandays</option><option value="manhours">Manhours</option></select></div>
                            <div class="col-md-4"><label class="form-label">Due Date Otomatis</label><input id="mom-auto-due-date" type="date" class="form-control" readonly><small class="text-muted">Dihitung dari tanggal MoM; Minggu dilewati.</small></div>
                            <div class="col-md-8"><label class="form-label">Acceptance Criteria *</label><textarea name="description" rows="2" class="form-control" required></textarea></div>
                            <div class="col-md-4"><label class="form-label">Hubungkan Task Existing</label><select name="linked_task_id" class="form-select"><option value="">-- Buat Task Baru --</option>@foreach($project->tasks as $task)<option value="{{ $task->id }}">{{ $task->task_code }} - {{ $task->title }}</option>@endforeach</select><small class="text-muted">Task baru memakai milestone MoM: {{ $meeting->milestone?->milestone_name ?? 'belum dipilih' }}.</small></div>
                            <div class="col-12 text-end"><button class="btn btn-primary btn-sm">Tambah Task Baru</button></div>
                        </div>
                    </form>
                    @endif
                </div>

                <div class="col-lg-4">
                    <h6>Peserta</h6><div class="border rounded mb-4"><ul class="list-group list-group-flush">@forelse($meeting->participants as $participant)<li class="list-group-item"><strong>{{ $participant->participant_name }}</strong><br><small class="text-muted">{{ $participant->participant_role ?: 'Participant' }}</small></li>@empty<li class="list-group-item text-muted">Belum ada peserta.</li>@endforelse</ul></div>
                    <h6>Lampiran</h6><div class="border rounded"><ul class="list-group list-group-flush">@forelse($meeting->attachments as $file)<li class="list-group-item d-flex justify-content-between align-items-center gap-2"><div class="text-truncate"><strong>{{ $file->original_name }}</strong><br><small class="text-muted">{{ number_format($file->file_size/1024,1) }} KB</small></div><a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ route('project_management.meetings.attachments.show',[$holding->holding_code,$project->id,$meeting->id,$file->id]) }}">Buka</a></li>@empty<li class="list-group-item text-muted">Tidak ada lampiran.</li>@endforelse</ul></div>
                </div>
            </div>

            @if($meeting->status === 'draft' && $canManageTasks)
            <div class="border-top mt-4 pt-4">
                <h6>Finalisasi MoM</h6>
                <form method="POST" action="{{ route('project_management.meetings.finalize',[$holding->holding_code,$project->id,$meeting->id]) }}">@csrf @method('PATCH')<label class="form-label">Summary MoM *</label><textarea name="summary" rows="4" class="form-control" required placeholder="Rangkum hasil pembahasan, keputusan utama, dan tindak lanjut.">{{ old('summary',$meeting->summary) }}</textarea><div class="d-flex justify-content-end gap-2 mt-3"><button type="submit" formnovalidate formaction="{{ route('project_management.meetings.cancel',[$holding->holding_code,$project->id,$meeting->id]) }}" class="btn btn-outline-danger" onclick="return confirm('Batalkan MoM ini?')">Batalkan MoM</button><button class="btn btn-success">Finalisasi MoM</button></div></form>
            </div>
            @endif
        </div>
    </div>
</div>
@if($canEdit)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const valueInput = document.getElementById('mom-estimate-value');
    const unitInput = document.getElementById('mom-estimate-unit');
    const dueInput = document.getElementById('mom-auto-due-date');
    if (!valueInput || !unitInput || !dueInput) return;

    const meetingDate = '{{ $meeting->meeting_at->format('Y-m-d') }}';
    const updateDueDate = function () {
        const value = Number.parseFloat(valueInput.value);
        if (!Number.isFinite(value) || value <= 0) {
            dueInput.value = '';
            return;
        }

        const hours = value * (unitInput.value === 'mandays' ? 8 : 1);
        const workdays = Math.max(1, Math.ceil(hours / 8));
        const dueDate = new Date(meetingDate + 'T00:00:00');
        for (let day = 1; day < workdays;) {
            dueDate.setDate(dueDate.getDate() + 1);
            if (dueDate.getDay() !== 0) day++;
        }
        dueInput.value = [dueDate.getFullYear(), String(dueDate.getMonth() + 1).padStart(2, '0'), String(dueDate.getDate()).padStart(2, '0')].join('-');
    };

    valueInput.addEventListener('input', updateDueDate);
    unitInput.addEventListener('change', updateDueDate);
    updateDueDate();
});
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-mom-download').forEach(function (link) {
        link.addEventListener('click', async function (event) {
            event.preventDefault();
            if (link.dataset.downloading === 'true') return;
            link.dataset.downloading = 'true';

            try {
                const response = await fetch(link.href, { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Dokumen gagal dibuat.');
                const disposition = response.headers.get('Content-Disposition') || '';
                const match = disposition.match(/filename="?([^";]+)"?/i);
                const blobUrl = URL.createObjectURL(await response.blob());
                const download = document.createElement('a');
                download.href = blobUrl;
                download.download = match ? match[1] : 'minutes-of-meeting.pdf';
                document.body.appendChild(download);
                download.click();
                download.remove();
                setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 1000);
                window.location.href = link.dataset.redirect;
            } catch (error) {
                alert(error.message);
                delete link.dataset.downloading;
            }
        });
    });
});
</script>
@endsection
