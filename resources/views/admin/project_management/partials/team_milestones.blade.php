@php
    $milestoneStatuses = [
        'planned' => 'Planned',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
        'cancelled' => 'Cancelled',
    ];
    $memberRoles = [
        'developer' => 'Developer',
        'reviewer_qa' => 'Reviewer / QA',
        'business_analyst' => 'Business Analyst',
        'technical_lead' => 'Technical Lead',
        'stakeholder' => 'Stakeholder',
        'observer' => 'Observer',
    ];
    $activeMembers = $project->members->where('is_active', true);
    $participantIds = collect([
        $project->requester_id,
        $project->business_owner_id,
        $project->project_manager_id,
        $project->technical_lead_id,
        $project->sponsor_id,
    ])
        ->merge($activeMembers->pluck('user_id'))
        ->merge($project->tasks->pluck('primary_assignee_id'))
        ->merge($project->tasks->pluck('reviewer_id'))
        ->filter()
        ->unique();
    $participantUsers = $users->whereIn('id', $participantIds);
@endphp

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h6 class="mb-1">Milestone Project</h6>
            <small class="text-muted">Target utama yang mengelompokkan task dan menjadi acuan pencapaian project.</small>
        </div>
        <span class="badge bg-secondary">{{ $project->milestones->count() }} milestone</span>
    </div>

    @if ($canManageProject)
        <form method="POST" action="{{ route('project_management.milestones.store', [$holding->holding_code, $project->id]) }}"
            class="border p-3 mb-3">
            @csrf
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="form-label">Nama Milestone <span class="text-danger">*</span></label>
                    <input type="text" name="milestone_name" class="form-control" value="{{ old('milestone_name') }}" required>
                    <small class="text-muted">Hasil utama atau fase project.</small>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Owner</label>
                    <select name="owner_id" class="form-select">
                        <option value="">-- Pilih Owner --</option>
                        @foreach ($participantUsers as $user)
                            <option value="{{ $user->id }}" @selected(old('owner_id') == $user->id)>{{ $userName($user) }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Penanggung jawab milestone.</small>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Target <span class="text-danger">*</span></label>
                    <input type="date" name="target_date" data-no-sunday-date class="form-control" value="{{ old('target_date') }}" required>
                    <small class="text-muted">Harus dalam periode project.</small>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach ($milestoneStatuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', 'planned') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Kondisi milestone saat ini.</small>
                </div>
                <div class="col-lg-1">
                    <label class="form-label">Bobot % <span class="text-danger">*</span></label>
                    <input type="number" name="weight" min="0.01" max="100" step="0.01" class="form-control" value="{{ old('weight') }}" required>
                    <small class="text-muted">Total maks. 100%.</small>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Kondisi Keberhasilan <span class="text-danger">*</span></label>
                    <textarea name="success_condition" rows="1" class="form-control" required>{{ old('success_condition') }}</textarea>
                    <small class="text-muted">Syarat milestone dianggap tercapai.</small>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary btn-sm">Tambah Milestone</button>
                </div>
            </div>
        </form>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Milestone</th><th>Owner</th><th>Target</th><th>Status</th><th>Bobot</th><th>Kondisi Keberhasilan</th><th>Task</th>
                    @if ($canManageProject)<th style="width:160px;">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse ($project->milestones->sortBy('target_date') as $milestone)
                    @php($milestoneForm = 'milestone-' . $milestone->id)
                    <tr>
                        <td><input form="{{ $milestoneForm }}" name="milestone_name" class="form-control" value="{{ $milestone->milestone_name }}" {{ $canManageProject ? '' : 'readonly' }} required></td>
                        <td>
                            @if ($canManageProject)
                                <select form="{{ $milestoneForm }}" name="owner_id" class="form-select">
                                    <option value="">-- Belum Ditentukan --</option>
                                    @foreach ($participantUsers as $user)
                                        <option value="{{ $user->id }}" @selected($milestone->owner_id === $user->id)>{{ $userName($user) }}</option>
                                    @endforeach
                                </select>
                            @else
                                {{ $userName($milestone->owner) }}
                            @endif
                        </td>
                        <td><input form="{{ $milestoneForm }}" type="date" name="target_date" data-no-sunday-date class="form-control" value="{{ $milestone->target_date?->format('Y-m-d') }}" {{ $canManageProject ? '' : 'readonly' }} required></td>
                        <td>
                            @if ($canManageProject)
                                <select form="{{ $milestoneForm }}" name="status" class="form-select">
                                    @foreach ($milestoneStatuses as $value => $label)
                                        <option value="{{ $value }}" @selected($milestone->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            @else
                                {{ $milestoneStatuses[$milestone->status] ?? $milestone->status }}
                            @endif
                        </td>
                        <td><input form="{{ $milestoneForm }}" type="number" name="weight" min="0.01" max="100" step="0.01" class="form-control" value="{{ $milestone->weight }}" {{ $canManageProject ? '' : 'readonly' }} required></td>
                        <td><textarea form="{{ $milestoneForm }}" name="success_condition" rows="2" class="form-control" {{ $canManageProject ? '' : 'readonly' }} required>{{ $milestone->success_condition }}</textarea></td>
                        <td>{{ $milestone->tasks->count() }}</td>
                        @if ($canManageProject)
                            <td class="text-nowrap">
                                <form id="{{ $milestoneForm }}" method="POST" action="{{ route('project_management.milestones.update', [$holding->holding_code, $project->id, $milestone->id]) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-warning btn-sm" type="submit">Simpan</button>
                                </form>
                                <form method="POST" action="{{ route('project_management.milestones.destroy', [$holding->holding_code, $project->id, $milestone->id]) }}" class="d-inline" onsubmit="return confirm('Hapus milestone ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada milestone.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#team-panel [data-no-sunday-date]').forEach(function (input) {
                input.addEventListener('change', function () {
                    if (this.value && new Date(this.value + 'T00:00:00').getDay() === 0) {
                        this.setCustomValidity('Hari Minggu tidak dapat dipilih. Pilih hari kerja lain.');
                        this.reportValidity();
                        this.value = '';
                    } else {
                        this.setCustomValidity('');
                    }
                });
            });
        });
    </script>
@endpush

<section>
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h6 class="mb-1">Anggota Project</h6>
            <small class="text-muted">Role utama project, anggota aktif, assignee, dan reviewer menjadi participant resmi yang mendapat akses project serta MoM.</small>
        </div>
        <span class="badge bg-secondary">{{ $participantIds->count() }} participant</span>
    </div>

    @if ($canManageProject)
        <form method="POST" action="{{ route('project_management.members.store', [$holding->holding_code, $project->id]) }}" class="border p-3 mb-3">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label">User <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $userName($user) }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Username yang menjadi participant project.</small>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Peran <span class="text-danger">*</span></label>
                    <select name="project_role" class="form-select" required>
                        @foreach ($memberRoles as $value => $label)
                            <option value="{{ $value }}" @selected(old('project_role', 'developer') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Fungsi user di dalam project.</small>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Alokasi %</label>
                    <input type="number" name="allocation_percent" min="0" max="100" step="0.01" class="form-control" value="{{ old('allocation_percent', 0) }}" required>
                    <small class="text-muted">Porsi kapasitas pada project.</small>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Alokasi Jam</label>
                    <input type="number" name="allocation_hours" min="0" step="0.25" class="form-control" value="{{ old('allocation_hours') }}">
                    <small class="text-muted">Opsional untuk perencanaan.</small>
                </div>
                <input type="hidden" name="is_active" value="1">
                <div class="col-lg-1"><button type="submit" class="btn btn-primary btn-sm w-100">Tambah</button></div>
            </div>
        </form>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-primary"><tr><th>User</th><th>Peran</th><th>Alokasi %</th><th>Alokasi Jam</th><th>Status</th>@if($canManageProject)<th style="width:160px;">Aksi</th>@endif</tr></thead>
            <tbody>
                @forelse ($project->members->sortByDesc('is_active') as $member)
                    @php($memberForm = 'member-' . $member->id)
                    <tr>
                        <td>{{ $userName($member->user) }}<input form="{{ $memberForm }}" type="hidden" name="user_id" value="{{ $member->user_id }}"></td>
                        <td>
                            @if ($canManageProject)
                                <select form="{{ $memberForm }}" name="project_role" class="form-select">
                                    @foreach ($memberRoles as $value => $label)<option value="{{ $value }}" @selected($member->project_role === $value)>{{ $label }}</option>@endforeach
                                </select>
                            @else {{ $memberRoles[$member->project_role] ?? $member->project_role }} @endif
                        </td>
                        <td><input form="{{ $memberForm }}" type="number" name="allocation_percent" min="0" max="100" step="0.01" class="form-control" value="{{ $member->allocation_percent }}" {{ $canManageProject ? '' : 'readonly' }} required></td>
                        <td><input form="{{ $memberForm }}" type="number" name="allocation_hours" min="0" step="0.25" class="form-control" value="{{ $member->allocation_hours }}" {{ $canManageProject ? '' : 'readonly' }}></td>
                        <td>
                            @if ($canManageProject)
                                <select form="{{ $memberForm }}" name="is_active" class="form-select"><option value="1" @selected($member->is_active)>Aktif</option><option value="0" @selected(!$member->is_active)>Nonaktif</option></select>
                            @else <span class="badge bg-{{ $member->is_active ? 'success' : 'secondary' }}">{{ $member->is_active ? 'Aktif' : 'Nonaktif' }}</span> @endif
                        </td>
                        @if ($canManageProject)
                            <td class="text-nowrap">
                                <form id="{{ $memberForm }}" method="POST" action="{{ route('project_management.members.update', [$holding->holding_code, $project->id, $member->id]) }}" class="d-inline">@csrf @method('PUT')<button class="btn btn-warning btn-sm" type="submit">Simpan</button></form>
                                <form method="POST" action="{{ route('project_management.members.destroy', [$holding->holding_code, $project->id, $member->id]) }}" class="d-inline" onsubmit="return confirm('Hapus anggota ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" type="submit">Hapus</button></form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada anggota project.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
