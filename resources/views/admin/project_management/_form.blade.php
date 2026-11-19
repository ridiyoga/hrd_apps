@php
    $project = $project ?? null;
    $statusOptions = [
        'draft' => 'Draft',
        'proposed' => 'Proposed',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'planning' => 'Planning',
        'in_progress' => 'In Progress',
        'uat' => 'UAT',
        'ready_for_deployment' => 'Ready for Deployment',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
        'cancelled' => 'Cancelled',
    ];
    $priorityOptions = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];
    $healthOptions = [
        'gray' => 'Gray',
        'green' => 'Green',
        'yellow' => 'Yellow',
        'red' => 'Red',
    ];
    $selected = fn($field, $default = null) => old($field, data_get($project, $field, $default));
    $userLabel = function ($user) {
        $employeeName = data_get($user, 'KaryawanNIK.name') ?: data_get($user, 'Karyawan.name');

        return $employeeName && $employeeName !== $user->username
            ? $user->username . ' - ' . $employeeName
            : $user->username;
    };
    $fieldHint = fn($text) => '<small class="text-muted d-block mt-1">' . $text . '</small>';
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nama Project <span class="text-danger">*</span></label>
        <input type="text" name="project_name" class="form-control @error('project_name') is-invalid @enderror"
            value="{{ $selected('project_name') }}" required>
        {!! $fieldHint('Isi nama project yang mudah dikenali, minimal 5 karakter.') !!}
        @error('project_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tipe Project <span class="text-danger">*</span></label>
        <input type="text" name="project_type" class="form-control @error('project_type') is-invalid @enderror"
            value="{{ $selected('project_type', 'Internal Development') }}" required>
        {!! $fieldHint('Jenis pekerjaan, misalnya Internal Development, Integrasi, Support, atau Sourcing.') !!}
        @error('project_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <input type="text" name="category" class="form-control @error('category') is-invalid @enderror"
            value="{{ $selected('category') }}" placeholder="ERP, HRD, Support, Integrasi">
        {!! $fieldHint('Kategori pelaporan agar project mudah dikelompokkan.') !!}
        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Priority <span class="text-danger">*</span></label>
        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
            @foreach ($priorityOptions as $value => $label)
                <option value="{{ $value }}" {{ $selected('priority', 'medium') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih tingkat urgensi atau dampak project terhadap operasional.') !!}
        @error('priority')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}" {{ $selected('status', 'draft') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Tahap lifecycle project saat ini; project baru biasanya Draft atau Proposed.') !!}
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Health <span class="text-danger">*</span></label>
        <select name="health" class="form-select @error('health') is-invalid @enderror" required>
            @foreach ($healthOptions as $value => $label)
                <option value="{{ $value }}" {{ $selected('health', 'gray') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Indikator kondisi project: gray awal, green normal, yellow perhatian, red kritis.') !!}
        @error('health')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Requester <span class="text-danger">*</span></label>
        <select name="requester_id" class="form-select pm-user-select @error('requester_id') is-invalid @enderror" required>
            <option value="">-- Pilih Username Requester --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('requester_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih username akun user yang mengajukan kebutuhan project, bukan ID karyawan.') !!}
        @error('requester_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Departemen Requester</label>
        <select name="requester_department_id"
            class="form-select @error('requester_department_id') is-invalid @enderror">
            <option value="">-- Pilih Departemen --</option>
            @foreach ($departemens as $departemen)
                <option value="{{ $departemen->id }}"
                    {{ $selected('requester_department_id') == $departemen->id ? 'selected' : '' }}>
                    {{ $departemen->nama_departemen }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Departemen asal requester untuk kebutuhan pelaporan dan ownership.') !!}
        @error('requester_department_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Business Owner</label>
        <select name="business_owner_id" class="form-select pm-user-select @error('business_owner_id') is-invalid @enderror">
            <option value="">-- Pilih Username Owner --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('business_owner_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih username pemilik proses/bisnis yang bertanggung jawab menerima hasil project.') !!}
        @error('business_owner_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Project Manager</label>
        <select name="project_manager_id" class="form-select pm-user-select @error('project_manager_id') is-invalid @enderror">
            <option value="">-- Pilih Username PM --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('project_manager_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih username penanggung jawab koordinasi project, timeline, dan deliverable.') !!}
        @error('project_manager_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Technical Lead</label>
        <select name="technical_lead_id" class="form-select pm-user-select @error('technical_lead_id') is-invalid @enderror">
            <option value="">-- Pilih Username Lead --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('technical_lead_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih username lead teknis yang mengarahkan solusi, review, dan dependency.') !!}
        @error('technical_lead_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Sponsor</label>
        <select name="sponsor_id" class="form-select pm-user-select @error('sponsor_id') is-invalid @enderror">
            <option value="">-- Pilih Username Sponsor --</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $selected('sponsor_id') == $user->id ? 'selected' : '' }}>
                    {{ $userLabel($user) }}
                </option>
            @endforeach
        </select>
        {!! $fieldHint('Pilih username sponsor atau pengambil keputusan utama bila ada.') !!}
        @error('sponsor_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Planned Start</label>
        <input type="date" name="planned_start_date" data-no-sunday-date
            class="form-control @error('planned_start_date') is-invalid @enderror"
            value="{{ old('planned_start_date', optional(data_get($project, 'planned_start_date'))->format('Y-m-d')) }}">
        {!! $fieldHint('Tanggal rencana mulai project.') !!}
        @error('planned_start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Planned End</label>
        <input type="date" name="planned_end_date" data-no-sunday-date
            class="form-control @error('planned_end_date') is-invalid @enderror"
            value="{{ old('planned_end_date', optional(data_get($project, 'planned_end_date'))->format('Y-m-d')) }}">
        {!! $fieldHint('Tanggal target selesai; harus sama atau setelah planned start.') !!}
        @error('planned_end_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Estimasi Jam</label>
        <input type="number" step="0.25" min="0" name="estimated_hours"
            class="form-control @error('estimated_hours') is-invalid @enderror"
            value="{{ $selected('estimated_hours') }}">
        {!! $fieldHint('Perkiraan total jam kerja project; boleh dikosongkan bila belum ada estimasi.') !!}
        @error('estimated_hours')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" required>{{ $selected('description') }}</textarea>
        {!! $fieldHint('Ringkasan kebutuhan project, masalah yang ingin diselesaikan, dan output utama.') !!}
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Background</label>
        <textarea name="background" rows="3" class="form-control @error('background') is-invalid @enderror">{{ $selected('background') }}</textarea>
        {!! $fieldHint('Latar belakang atau alasan project dibutuhkan.') !!}
        @error('background')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Objective</label>
        <textarea name="objective" rows="3" class="form-control @error('objective') is-invalid @enderror">{{ $selected('objective') }}</textarea>
        {!! $fieldHint('Tujuan terukur yang ingin dicapai oleh project.') !!}
        @error('objective')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Scope Summary</label>
        <textarea name="scope_summary" rows="3" class="form-control @error('scope_summary') is-invalid @enderror">{{ $selected('scope_summary') }}</textarea>
        {!! $fieldHint('Ringkasan batas pekerjaan project secara umum.') !!}
        @error('scope_summary')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Success Criteria</label>
        <textarea name="success_criteria" rows="3" class="form-control @error('success_criteria') is-invalid @enderror">{{ $selected('success_criteria') }}</textarea>
        {!! $fieldHint('Kondisi yang menandakan project dianggap berhasil atau bisa diterima.') !!}
        @error('success_criteria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">In Scope</label>
        <textarea name="in_scope" rows="3" class="form-control @error('in_scope') is-invalid @enderror">{{ $selected('in_scope') }}</textarea>
        {!! $fieldHint('Daftar pekerjaan atau deliverable yang termasuk dalam project.') !!}
        @error('in_scope')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Out of Scope</label>
        <textarea name="out_of_scope" rows="3" class="form-control @error('out_of_scope') is-invalid @enderror">{{ $selected('out_of_scope') }}</textarea>
        {!! $fieldHint('Batasan pekerjaan yang tidak termasuk agar ekspektasi tetap jelas.') !!}
        @error('out_of_scope')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">PM Commentary <small class="text-muted">(untuk laporan stakeholder)</small></label>
        <textarea name="pm_commentary" rows="4" class="form-control @error('pm_commentary') is-invalid @enderror" placeholder="Isi catatan eksekutif PM tentang kondisi project saat ini, key achievements, risiko utama, dan kebutuhan eskalasi ke stakeholder/direktur.">{{ $selected('pm_commentary') }}</textarea>
        {!! $fieldHint('Catatan naratif PM yang akan ditampilkan di laporan untuk stakeholder. Perbarui secara berkala setiap ada update penting.') !!}
        @error('pm_commentary')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Keputusan / Dukungan yang Dibutuhkan <small class="text-muted">(untuk direktur/stakeholder)</small></label>
        <textarea name="decisions_needed" rows="4" class="form-control @error('decisions_needed') is-invalid @enderror"
            placeholder="Tuliskan keputusan, approval, resource, akses, atau eskalasi yang diperlukan beserta target waktunya.">{{ $selected('decisions_needed') }}</textarea>
        {!! $fieldHint('Isi hanya action yang membutuhkan keputusan stakeholder. Kosongkan jika tidak ada keputusan yang diperlukan.') !!}
        @error('decisions_needed')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Expected Benefit</label>
        <textarea name="expected_benefit" rows="3" class="form-control @error('expected_benefit') is-invalid @enderror">{{ $selected('expected_benefit') }}</textarea>
        {!! $fieldHint('Manfaat yang diharapkan, seperti efisiensi, kontrol, akurasi, atau pengurangan risiko.') !!}
        @error('expected_benefit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.select2) {
                jQuery('.pm-user-select').select2({
                    width: '100%'
                });
            }

            document.querySelectorAll('[data-no-sunday-date]').forEach(function (input) {
                const validateDate = function () {
                    input.setCustomValidity('');

                    if (!input.value) {
                        return;
                    }

                    const date = new Date(input.value);
                    date.setHours(0, 0, 0, 0);

                    if (date.getDay() === 0) {
                        input.setCustomValidity('Tanggal rencana tidak boleh jatuh pada hari Minggu.');
                    }
                };

                input.addEventListener('change', validateDate);
                validateDate();
            });
        });
    </script>
@endpush
