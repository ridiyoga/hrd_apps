@php
    $reopenModalId = 'reopen-task-' . $task->id;
@endphp

<button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
    data-bs-target="#{{ $reopenModalId }}" title="Reopen Task">
    <i class="mdi mdi-lock-open-outline"></i>
    @if (($showLabel ?? false) === true)
        Reopen
    @endif
</button>

<div class="modal fade" id="{{ $reopenModalId }}" tabindex="-1" aria-labelledby="{{ $reopenModalId }}-label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST"
                action="{{ route('project_management.tasks.reopen', [$holding->holding_code, $project->id, $task->id]) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $reopenModalId }}-label">Reopen Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Task</small>
                        <div class="fw-semibold">{{ $task->task_code }} - {{ $task->title }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Tujuan <span class="text-danger">*</span></label>
                        <select name="reopen_status" class="form-select" required>
                            <option value="in_progress">In Progress</option>
                            <option value="todo">Todo</option>
                        </select>
                        <small class="text-muted d-block mt-1">Pilih In Progress jika langsung dikerjakan developer, atau Todo jika perlu antre ulang.</small>
                    </div>

                    <div>
                        <label class="form-label">Alasan Reopen / Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea name="reopen_reason" rows="4" class="form-control"
                            placeholder="Tuliskan alasan task dibuka kembali dan revisi yang harus dikerjakan..." required></textarea>
                        <small class="text-muted d-block mt-1">Catatan ini akan tersimpan di Riwayat Pekerjaan dan tidak menimpa catatan lama.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Reopen Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
