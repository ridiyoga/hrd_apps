@php
    $task = $task ?? null;
    $isCreate = !$task;
    $taskStatusOptions = [
        'todo' => 'Todo',
        'in_progress' => 'In Progress',
        'blocked' => 'Blocked',
        'review' => 'Review',
        'done' => 'Done',
        'cancelled' => 'Cancelled',
    ];
    $taskTypeOptions = [
        'task' => 'Task',
        'bug' => 'Bug',
        'change' => 'Change',
        'documentation' => 'Documentation',
        'support' => 'Support',
    ];
    $priorityOptions = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];
    $selected = fn($field, $default = null) => old($field, data_get($task, $field, $default));
    $userLabel = function ($user) {
        $employeeName = data_get($user, 'KaryawanNIK.name') ?: data_get($user, 'Karyawan.name');

        return $employeeName && $employeeName !== $user->username
            ? $user->username . ' - ' . $employeeName
            : $user->username;
    };
    $fieldHint = fn($text) => '<small class="text-muted d-block mt-1">' . $text . '</small>';
    $assigneeRows = $users->map(function ($user) {
        $karyawan = data_get($user, 'KaryawanNIK') ?: data_get($user, 'Karyawan');
        $jabatanDepartemens = collect(data_get($karyawan, 'KaryawanJabatan', []))
            ->pluck('departemen_id');
        $departmentIds = collect([
            data_get($karyawan, 'dept_id'),
            data_get($karyawan, 'dept1_id'),
            data_get($karyawan, 'dept2_id'),
            data_get($karyawan, 'dept3_id'),
            data_get($karyawan, 'dept_id1'),
            data_get($karyawan, 'dept_id2'),
            data_get($karyawan, 'dept_id3'),
        ])
            ->merge($jabatanDepartemens)
            ->filter()
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values();
        $employeeId = data_get($karyawan, 'nomor_identitas_karyawan') ?: $user->karyawan_id;
        $employeeName = data_get($karyawan, 'name') ?: $user->username;

        return [
            'user_id' => $user->id,
            'username' => $user->username,
            'employee_key' => data_get($karyawan, 'id') ?: $employeeId ?: $user->id,
            'employee_id' => $employeeId ?: '-',
            'employee_name' => $employeeName,
            'employee_label' => trim(($employeeId ? $employeeId . ' - ' : '') . $employeeName),
            'department_ids' => $departmentIds->all(),
        ];
    })->values();
    $selectedAssigneeId = (string) $selected('primary_assignee_id', '');
    $selectedAssignee = $assigneeRows->firstWhere('user_id', $selectedAssigneeId);
    $availableDepartmentIds = $departemens->pluck('id')->map(fn($id) => (string) $id);
    $selectedAssigneeDepartment = $selectedAssignee
        ? collect($selectedAssignee['department_ids'])->first(fn($id) => $availableDepartmentIds->contains((string) $id))
        : '';
    $selectedDepartmentId = old('assign_department_id', $selectedAssigneeDepartment ?: '');
    $selectedEmployeeKey = old('assign_karyawan_key', data_get($selectedAssignee, 'employee_key', ''));
    $hoursPerWorkday = 8;
    $workdaysPerMonth = 26;
    $hoursPerWorkMonth = $workdaysPerMonth * $hoursPerWorkday;
    $estimateTotalHours = is_numeric($selected('estimate_hours')) ? (float) $selected('estimate_hours') : 0;
    $estimateDefaultMonths = $estimateTotalHours > 0 ? (int) floor($estimateTotalHours / $hoursPerWorkMonth) : null;
    $estimateRemainderHours = $estimateTotalHours - (($estimateDefaultMonths ?: 0) * $hoursPerWorkMonth);
    $estimateDefaultDays = $estimateRemainderHours > 0 ? (int) floor($estimateRemainderHours / $hoursPerWorkday) : null;
    $estimateDefaultHours = $estimateRemainderHours - (($estimateDefaultDays ?: 0) * $hoursPerWorkday);
    $estimateDefaultHours = $estimateDefaultHours > 0 ? round($estimateDefaultHours, 2) : null;
    $selectedEstimateMonths = old('estimate_months', $estimateDefaultMonths);
    $selectedEstimateDays = old('estimate_days', $estimateDefaultDays);
    $selectedEstimateTimeHours = old('estimate_time_hours', $estimateDefaultHours);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Judul Task <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ $selected('title') }}" required>
        {!! $fieldHint('Isi pekerjaan yang harus diselesaikan, singkat dan spesifik.') !!}
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Milestone</label>
        <select name="milestone_id" class="form-select @error('milestone_id') is-invalid @enderror">
            <option value="">-- Belum Dikelompokkan --</option>
            @foreach ($project->milestones()->orderBy('target_date')->orderBy('milestone_name')->get() as $milestone)
                <option value="{{ $milestone->id }}" {{ $selected('milestone_id') == $milestone->id ? 'selected' : '' }}>
                    {{ $milestone->milestone_name }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Kelompok target atau fase project untuk task ini.') !!}
        @error('milestone_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Parent Task</label>
        <select name="parent_task_id" class="form-select @error('parent_task_id') is-invalid @enderror">
            <option value="">-- Tidak Ada --</option>
            @foreach ($tasks as $parentTask)
                <option value="{{ $parentTask->id }}"
                    {{ $selected('parent_task_id') == $parentTask->id ? 'selected' : '' }}>
                    {{ $parentTask->task_code }} - {{ $parentTask->title }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih task induk jika item ini adalah subtask; kosongkan untuk task utama.') !!}
        @error('parent_task_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Tipe <span class="text-danger">*</span></label>
        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($taskTypeOptions as $value => $label)
                <option value="{{ $value }}" {{ $selected('type', 'task') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Klasifikasi pekerjaan: task, bug, change, dokumentasi, atau support.') !!}
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Priority <span class="text-danger">*</span></label>
        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
            @foreach ($priorityOptions as $value => $label)
                <option value="{{ $value }}" {{ $selected('priority', 'medium') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Tingkat urgensi task dibanding task lain dalam project.') !!}
        @error('priority')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        @if ($isCreate)
            <input type="hidden" name="status" value="todo">
            <input type="text" class="form-control" value="Todo" readonly>
            {!! $fieldHint('Task baru otomatis dibuat dengan status Todo.') !!}
        @else
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach ($taskStatusOptions as $value => $label)
                    <option value="{{ $value }}" {{ $selected('status', 'todo') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            {!! $fieldHint('PM dapat membuka task Blocked ke In Progress; Done akan memengaruhi progress project.') !!}
        @endif
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Reporter</label>
        <select name="reporter_id" class="form-select @error('reporter_id') is-invalid @enderror">
            <option value="">-- Otomatis User Login --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('reporter_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pembuat atau pelapor task; kosongkan untuk otomatis memakai user login.') !!}
        @error('reporter_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Departemen Assignee</label>
        <select name="assign_department_id" id="assign_department_id" class="form-select pm-assignee-select">
            <option value="">-- Pilih Departemen --</option>
            @foreach ($departemens as $departemen)
                <option value="{{ $departemen->id }}" {{ $selectedDepartmentId == $departemen->id ? 'selected' : '' }}>
                    {{ $departemen->nama_departemen }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih departemen lebih dulu untuk membatasi daftar karyawan assignee.') !!}
    </div>

    <div class="col-md-4">
        <label class="form-label">Karyawan Assignee</label>
        <select name="assign_karyawan_key" id="assign_karyawan_key" class="form-select pm-assignee-select">
            <option value="">-- Pilih Karyawan --</option>
            @if ($selectedAssignee && $selectedEmployeeKey)
                <option value="{{ $selectedEmployeeKey }}" selected>{{ $selectedAssignee['employee_label'] }}</option>
            @endif
        </select>
        {!! $fieldHint('Cari berdasarkan ID karyawan atau nama karyawan yang akan diberi task.') !!}
    </div>

    <div class="col-md-4">
        <label class="form-label">Username Assignee</label>
        <select name="primary_assignee_id" id="primary_assignee_id"
            class="form-select pm-assignee-select @error('primary_assignee_id') is-invalid @enderror">
            <option value="">-- Pilih Username --</option>
            @if ($selectedAssignee)
                <option value="{{ $selectedAssignee['user_id'] }}" selected>
                    {{ $selectedAssignee['username'] }} ({{ $selectedAssignee['employee_label'] }})
                </option>
            @endif
        </select>
        {!! $fieldHint('Username inilah yang disimpan sebagai PIC task dan muncul di menu Task Saya user tersebut.') !!}
        @error('primary_assignee_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Reviewer</label>
        <select name="reviewer_id" class="form-select @error('reviewer_id') is-invalid @enderror">
            <option value="">-- Pilih Reviewer --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('reviewer_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('User yang memeriksa hasil pekerjaan sebelum task dianggap selesai.') !!}
        @error('reviewer_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="date" name="start_date" id="start_date"
            data-no-sunday-date
            class="form-control @error('start_date') is-invalid @enderror"
            value="{{ old('start_date', optional(data_get($task, 'start_date'))->format('Y-m-d')) }}">
        {!! $fieldHint('Tanggal rencana mulai pengerjaan task.') !!}
        @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Estimasi Waktu</label>
        <input type="hidden" name="estimate_hours" id="estimate_hours" value="{{ $selected('estimate_hours') }}">
        <div class="row g-2">
            <div class="col-4">
                <input type="number" min="0" step="1" name="estimate_months" id="estimate_months"
                    class="form-control @error('estimate_months') is-invalid @enderror"
                    value="{{ $selectedEstimateMonths }}" placeholder="0">
                <small class="text-muted d-block mt-1">Bulan</small>
                @error('estimate_months')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-4">
                <input type="number" min="0" step="1" name="estimate_days" id="estimate_days"
                    class="form-control @error('estimate_days') is-invalid @enderror"
                    value="{{ $selectedEstimateDays }}" placeholder="0">
                <small class="text-muted d-block mt-1">Hari</small>
                @error('estimate_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-4">
                <input type="number" min="0" step="0.25" name="estimate_time_hours" id="estimate_time_hours"
                    class="form-control @error('estimate_time_hours') is-invalid @enderror"
                    value="{{ $selectedEstimateTimeHours }}" placeholder="0">
                <small class="text-muted d-block mt-1">Jam</small>
                @error('estimate_time_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        {!! $fieldHint('Dipakai untuk bobot progress dan due date. 1 bulan = 26 hari kerja, 1 hari kerja = 8 jam.') !!}
        @error('estimate_hours')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" id="due_date"
            class="form-control @error('due_date') is-invalid @enderror"
            value="{{ old('due_date', optional(data_get($task, 'due_date'))->format('Y-m-d')) }}" readonly>
        {!! $fieldHint('Otomatis dari Start Date + estimasi hari kerja.') !!}
        @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @unless ($isCreate)
        <div class="col-md-2">
            <label class="form-label">Review Due Date</label>
            <input type="date" name="review_due_date"
                data-no-sunday-date
                class="form-control @error('review_due_date') is-invalid @enderror"
                value="{{ old('review_due_date', optional(data_get($task, 'review_due_date'))->format('Y-m-d')) }}">
            {!! $fieldHint('Target reviewer/QA memberi keputusan; default 3 hari kerja setelah submit review.') !!}
            @error('review_due_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endunless

    <div class="col-12">
        <label class="form-label">Acceptance Criteria</label>
        <textarea name="acceptance_criteria" rows="3"
            class="form-control @error('acceptance_criteria') is-invalid @enderror">{{ $selected('acceptance_criteria') }}</textarea>
        {!! $fieldHint('Kriteria hasil yang harus terpenuhi agar task bisa dinyatakan Done.') !!}
        @error('acceptance_criteria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($isCreate)
        <div class="col-12">
            <label class="form-label" for="task_attachments">Lampiran Task</label>
            <input type="file" name="task_attachments[]" id="task_attachments" multiple
                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip"
                class="form-control @error('task_attachments') is-invalid @enderror @error('task_attachments.*') is-invalid @enderror">
            {!! $fieldHint('Unggah gambar atau dokumen pendukung task. Dapat memilih beberapa file, maksimal 10 MB per file.') !!}
            @error('task_attachments')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('task_attachments.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif

    <div class="col-12">
        <label class="form-label">Blocker Reason</label>
        <textarea name="blocker_reason" rows="3" class="form-control @error('blocker_reason') is-invalid @enderror"
            placeholder="Wajib diisi jika status Blocked">{{ $selected('blocker_reason') }}</textarea>
        {!! $fieldHint('Jelaskan hambatan, pihak yang dibutuhkan, atau aksi berikutnya jika task Blocked.') !!}
        @error('blocker_reason')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @unless ($isCreate)
        <div class="col-12">
            <div class="border p-3">
                <div class="form-check">
                    <input type="hidden" name="status_override" value="0">
                    <input class="form-check-input @error('status_override') is-invalid @enderror" type="checkbox"
                        name="status_override" id="status_override" value="1" {{ old('status_override') ? 'checked' : '' }}>
                    <label class="form-check-label" for="status_override">Override alur status oleh Project Manager</label>
                </div>
                {!! $fieldHint('Gunakan hanya untuk kondisi khusus. Status Done normal ditetapkan Reviewer / QA dan override akan masuk ke Riwayat Pekerjaan.') !!}
                @error('status_override')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror

                <label class="form-label mt-2" for="status_override_reason">Alasan Override</label>
                <textarea name="status_override_reason" id="status_override_reason" rows="2"
                    class="form-control @error('status_override_reason') is-invalid @enderror"
                    placeholder="Jelaskan kondisi khusus dan dasar keputusan PM">{{ old('status_override_reason') }}</textarea>
                @error('status_override_reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endunless
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const assigneeRows = @json($assigneeRows);
            const selectedDepartmentId = @json((string) $selectedDepartmentId);
            const selectedEmployeeKey = @json((string) $selectedEmployeeKey);
            const selectedAssigneeId = @json((string) $selectedAssigneeId);
            const departmentSelect = document.getElementById('assign_department_id');
            const employeeSelect = document.getElementById('assign_karyawan_key');
            const assigneeSelect = document.getElementById('primary_assignee_id');
            const startDateInput = document.getElementById('start_date');
            const estimateHoursInput = document.getElementById('estimate_hours');
            const estimateMonthsInput = document.getElementById('estimate_months');
            const estimateDaysInput = document.getElementById('estimate_days');
            const estimateTimeHoursInput = document.getElementById('estimate_time_hours');
            const dueDateInput = document.getElementById('due_date');
            const hoursPerWorkday = 8;
            const workdaysPerMonth = 26;

            if (window.jQuery && jQuery.fn.select2) {
                jQuery('.pm-assignee-select').select2({
                    width: '100%'
                });
            }

            function refreshSelect(select) {
                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(select).trigger('change.select2');
                }
            }

            function resetSelect(select, placeholder) {
                select.innerHTML = '';
                select.appendChild(new Option(placeholder, ''));
            }

            function rowsByDepartment() {
                const departmentId = departmentSelect.value;

                if (!departmentId) {
                    return [];
                }

                return assigneeRows.filter(function (row) {
                    return (row.department_ids || []).map(String).includes(String(departmentId));
                });
            }

            function populateEmployees(selectedKey = '') {
                const rows = rowsByDepartment();
                const employees = new Map();

                resetSelect(employeeSelect, '-- Pilih Karyawan --');
                resetSelect(assigneeSelect, '-- Pilih Username --');

                rows.forEach(function (row) {
                    if (!employees.has(row.employee_key)) {
                        employees.set(row.employee_key, row.employee_label);
                    }
                });

                Array.from(employees.entries())
                    .sort(function (a, b) {
                        return a[1].localeCompare(b[1]);
                    })
                    .forEach(function ([key, label]) {
                        employeeSelect.appendChild(new Option(label, key, false, String(key) === String(selectedKey)));
                    });

                if (selectedKey) {
                    employeeSelect.value = selectedKey;
                }

                populateUsernames(selectedAssigneeId);
                refreshSelect(employeeSelect);
            }

            function populateUsernames(selectedUserId = '') {
                const employeeKey = employeeSelect.value;
                const rows = rowsByDepartment().filter(function (row) {
                    return String(row.employee_key) === String(employeeKey);
                });

                resetSelect(assigneeSelect, '-- Pilih Username --');

                rows.sort(function (a, b) {
                    return String(a.username).localeCompare(String(b.username));
                }).forEach(function (row) {
                    const label = row.username + ' (' + row.employee_label + ')';
                    assigneeSelect.appendChild(new Option(label, row.user_id, false, String(row.user_id) === String(selectedUserId)));
                });

                if (selectedUserId) {
                    assigneeSelect.value = selectedUserId;
                }

                refreshSelect(assigneeSelect);
            }

            function parseDurationPart(input) {
                if (!input || input.value === '') {
                    return 0;
                }

                const value = parseFloat(input.value);
                return Number.isNaN(value) ? 0 : Math.max(0, value);
            }

            function formatEstimateHours(value) {
                if (value <= 0) {
                    return '';
                }

                return Number.isInteger(value) ? String(value) : value.toFixed(2).replace(/0+$/, '').replace(/\.$/, '');
            }

            function syncEstimateHours() {
                if (!estimateHoursInput) {
                    return;
                }

                const totalHours = (parseDurationPart(estimateMonthsInput) * workdaysPerMonth * hoursPerWorkday)
                    + (parseDurationPart(estimateDaysInput) * hoursPerWorkday)
                    + parseDurationPart(estimateTimeHoursInput);

                estimateHoursInput.value = formatEstimateHours(totalHours);
                calculateDueDate();
            }

            function calculateDueDate() {
                if (!startDateInput || !estimateHoursInput || !dueDateInput) {
                    return;
                }

                const startDate = startDateInput.value;
                const estimateHours = parseFloat(estimateHoursInput.value);

                if (!startDate || Number.isNaN(estimateHours)) {
                    dueDateInput.value = '';
                    return;
                }

                const parts = startDate.split('-').map(Number);
                const dueDate = new Date(parts[0], parts[1] - 1, parts[2], 0, 0, 0);
                while (dueDate.getDay() === 0) {
                    dueDate.setDate(dueDate.getDate() + 1);
                }
                const workdays = Math.max(1, Math.ceil(Math.max(0, estimateHours) / hoursPerWorkday));
                let addedWorkdays = 1;

                while (addedWorkdays < workdays) {
                    dueDate.setDate(dueDate.getDate() + 1);

                    if (dueDate.getDay() !== 0) {
                        addedWorkdays += 1;
                    }
                }

                const year = dueDate.getFullYear();
                const month = String(dueDate.getMonth() + 1).padStart(2, '0');
                const day = String(dueDate.getDate()).padStart(2, '0');
                dueDateInput.value = `${year}-${month}-${day}`;
            }

            if (startDateInput && estimateHoursInput) {
                startDateInput.addEventListener('change', function () {
                    validateNoSundayDate(startDateInput);
                    calculateDueDate();
                });
                [estimateMonthsInput, estimateDaysInput, estimateTimeHoursInput].forEach(function (input) {
                    if (input) {
                        input.addEventListener('input', syncEstimateHours);
                    }
                });
                syncEstimateHours();
            }

            function validateNoSundayDate(input) {
                input.setCustomValidity('');

                if (!input.value) {
                    return;
                }

                const date = new Date(input.value);
                date.setHours(0, 0, 0, 0);

                if (date.getDay() === 0) {
                    input.setCustomValidity('Tanggal rencana tidak boleh jatuh pada hari Minggu.');
                }
            }

            document.querySelectorAll('[data-no-sunday-date]').forEach(function (input) {
                input.addEventListener('change', function () {
                    validateNoSundayDate(input);
                });
                validateNoSundayDate(input);
            });

            if (window.jQuery) {
                jQuery(departmentSelect).on('change', function () {
                    populateEmployees();
                });

                jQuery(employeeSelect).on('change', function () {
                    populateUsernames();
                });
            } else {
                departmentSelect.addEventListener('change', function () {
                    populateEmployees();
                });

                employeeSelect.addEventListener('change', function () {
                    populateUsernames();
                });
            }

            if (selectedDepartmentId) {
                departmentSelect.value = selectedDepartmentId;
                refreshSelect(departmentSelect);
                populateEmployees(selectedEmployeeKey);
            } else if (departmentSelect.value) {
                populateEmployees();
            }
        });
    </script>
@endpush
