<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penyelesaian Project</title>
    <style>
        @page { margin: 34px 34px 48px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #20262b; line-height: 1.45; }
        h1 { font-size: 19px; margin: 0; text-align: center; letter-spacing: .4px; }
        h2 { font-size: 12px; margin: 16px 0 7px; padding: 5px 7px; color: #fff; background: #244b5a; page-break-after: avoid; }
        h3 { font-size: 10px; margin: 11px 0 5px; page-break-after: avoid; }
        p { margin: 4px 0 7px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; table-layout: fixed; }
        th, td { border: 1px solid #bcc7cc; padding: 5px; vertical-align: top; overflow-wrap: break-word; }
        th { background: #e8eef0; text-align: left; font-size: 8px; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .header { border-top: 6px solid #244b5a; border-bottom: 1px solid #8fa1a8; padding: 13px 0 12px; margin-bottom: 13px; }
        .subtitle { text-align: center; font-size: 10px; font-weight: bold; margin-top: 4px; }
        .document-number { text-align: center; color: #64747b; font-size: 8px; margin-top: 4px; }
        .label { width: 25%; background: #f2f5f6; font-weight: bold; }
        .muted { color: #64747b; font-size: 8px; }
        .note { white-space: pre-wrap; }
        .metric { font-size: 16px; font-weight: bold; color: #244b5a; }
        .positive { color: #23734a; font-weight: bold; }
        .negative { color: #ad2e3b; font-weight: bold; }
        .warning { color: #8b6100; font-weight: bold; }
        .statement { border: 1px solid #8fa1a8; background: #f6f8f9; padding: 10px; text-align: justify; margin: 9px 0; }
        .progress-track { height: 13px; background: #e2e8ea; }
        .progress-bar { height: 13px; background: #23734a; }
        .status-row { margin: 4px 0; }
        .status-label { display: inline-block; width: 90px; }
        .status-track { display: inline-block; width: 65%; height: 10px; background: #e8ecee; vertical-align: middle; }
        .status-bar { display: block; height: 10px; background: #4d7888; }
        .signature { margin-top: 22px; page-break-inside: avoid; }
        .signature td { border: 0; width: 50%; text-align: center; padding: 4px 25px; }
        .signature-space { height: 68px; }
        .signature-name { display: inline-block; min-width: 180px; border-bottom: 1px solid #20262b; font-weight: bold; padding-bottom: 2px; }
        .footer { position: fixed; bottom: -30px; font-size: 8px; color: #64747b; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
@php
    $project = $reportProjects->first();
    $tasks = $reportTasks->where('project_id', $project->id);
    $done = $tasks->where('status', 'done');
    $cancelled = $tasks->where('status', 'cancelled');
    $outstanding = $tasks->whereNotIn('status', ['done', 'cancelled']);
    $date = fn($value) => $value?->format('d/m/Y') ?? '-';
    $datetime = fn($value) => $value?->format('d/m/Y H:i') ?? '-';
    $md = fn($value) => number_format((float) $value / 8, 2);
    $actualEnd = $project->actual_end_at ?: ($project->status === 'completed' ? $generatedAt : null);
    $deviation = $project->planned_end_date && $actualEnd
        ? $project->planned_end_date->copy()->startOfDay()->diffInDays($actualEnd->copy()->startOfDay(), false)
        : null;
    $completion = (float) $project->progress_percent;
    $finalMeetings = $project->meetings;
    $documentNumber = 'LPP-' . $project->project_code . '-' . $generatedAt->format('Ymd');
@endphp

<div class="footer">{{ $documentNumber }} | Dokumen internal | Dicetak {{ $generatedAt->format('d/m/Y H:i') }} oleh {{ $generatedBy }}</div>

<div class="header">
    <h1>LAPORAN PENYELESAIAN PROJECT</h1>
    <div class="subtitle">BERITA ACARA SERAH TERIMA INTERNAL</div>
    <div class="document-number">Nomor Dokumen: {{ $documentNumber }}</div>
</div>

<table>
    <tr><td class="label">Nama Project</td><td><strong>{{ $project->project_name }}</strong></td></tr>
    <tr><td class="label">Kode Project</td><td>{{ $project->project_code }}</td></tr>
    <tr><td class="label">Jenis / Kategori</td><td>{{ $project->project_type ?: '-' }} / {{ $project->category ?: '-' }}</td></tr>
    <tr><td class="label">Status Dokumen</td><td>{{ $completion >= 100 && $outstanding->isEmpty() ? 'Penyelesaian Final' : 'Penyelesaian Bersyarat / Progress Handover' }}</td></tr>
    <tr><td class="label">Tanggal Dokumen</td><td>{{ $generatedAt->format('d/m/Y') }}</td></tr>
</table>

<h2>1. PARA PIHAK DAN PENANGGUNG JAWAB</h2>
<table>
    <thead><tr><th>Peran</th><th>Nama / Username</th><th>Tanggung Jawab</th></tr></thead>
    <tbody>
        <tr><td>Project Manager</td><td>{{ $project->projectManager?->username ?? '-' }}</td><td>Pelaksanaan, koordinasi, dokumentasi, dan serah terima project.</td></tr>
        <tr><td>Business Owner</td><td>{{ $project->businessOwner?->username ?? '-' }}</td><td>Pemilik kebutuhan dan penerima hasil pekerjaan.</td></tr>
        <tr><td>Requester</td><td>{{ $project->requester?->username ?? '-' }}</td><td>Pengusul kebutuhan project.</td></tr>
        <tr><td>Technical Lead</td><td>{{ $project->technicalLead?->username ?? '-' }}</td><td>Pengendalian teknis dan kualitas implementasi.</td></tr>
        <tr><td>Sponsor</td><td>{{ $project->sponsor?->username ?? '-' }}</td><td>Arahan dan persetujuan manajemen.</td></tr>
    </tbody>
</table>

<h2>2. LATAR BELAKANG, TUJUAN, DAN RUANG LINGKUP</h2>
<table>
    <tr><td class="label">Latar Belakang</td><td class="note">{{ $project->background ?: $project->description ?: '-' }}</td></tr>
    <tr><td class="label">Tujuan</td><td class="note">{{ $project->objective ?: '-' }}</td></tr>
    <tr><td class="label">Manfaat yang Diharapkan</td><td class="note">{{ $project->expected_benefit ?: '-' }}</td></tr>
    <tr><td class="label">Ringkasan Scope</td><td class="note">{{ $project->scope_summary ?: '-' }}</td></tr>
    <tr><td class="label">Termasuk Scope</td><td class="note">{{ $project->in_scope ?: '-' }}</td></tr>
    <tr><td class="label">Di Luar Scope</td><td class="note">{{ $project->out_of_scope ?: '-' }}</td></tr>
    <tr><td class="label">Kriteria Keberhasilan</td><td class="note">{{ $project->success_criteria ?: '-' }}</td></tr>
</table>

<h2>3. RINGKASAN PENYELESAIAN</h2>
<table>
    <tr>
        <td>Progress Project<div class="metric">{{ number_format($completion, 2) }}%</div></td>
        <td>Total Task<div class="metric">{{ $tasks->count() }}</div></td>
        <td>Task Selesai<div class="metric">{{ $done->count() }}</div></td>
        <td>Task Outstanding<div class="metric">{{ $outstanding->count() }}</div></td>
    </tr>
    <tr>
        <td>Estimasi Effort<div class="metric">{{ $md($tasks->sum('estimate_hours')) }} MD</div></td>
        <td>Aktual Tercatat<div class="metric">{{ $md($tasks->sum('actual_hours')) }} MD</div></td>
        <td>Sudah Disampaikan<div class="metric">{{ $done->filter(fn($task) => $task->isCommunicated())->count() }}</div></td>
        <td>Belum Disampaikan<div class="metric">{{ $done->filter(fn($task) => !$task->isCommunicated())->count() }}</div></td>
    </tr>
</table>
<div class="progress-track"><div class="progress-bar" style="width:{{ min(100, $completion) }}%"></div></div>
<p class="muted">Progress dihitung berdasarkan bobot estimasi task. 1 MD = 8 jam kerja.</p>

@foreach($statusOptions as $status => $label)
    @php $count = $tasks->where('status', $status)->count(); $percentage = $tasks->count() ? ($count / $tasks->count() * 100) : 0; @endphp
    <div class="status-row"><span class="status-label">{{ $label }}</span><span class="status-track"><span class="status-bar" style="width:{{ number_format($percentage, 2, '.', '') }}%"></span></span> <strong>{{ $count }}</strong></div>
@endforeach

<h2>4. REALISASI JADWAL</h2>
<table>
    <thead><tr><th>Parameter</th><th>Rencana</th><th>Aktual / Posisi Laporan</th><th>Deviasi</th></tr></thead>
    <tbody>
        <tr><td>Mulai Project</td><td>{{ $date($project->planned_start_date) }}</td><td>{{ $date($project->actual_start_at) }}</td><td>-</td></tr>
        <tr><td>Selesai Project</td><td>{{ $date($project->planned_end_date) }}</td><td>{{ $date($actualEnd) }}</td><td class="{{ ($deviation ?? 0) > 0 ? 'negative' : 'positive' }}">{{ is_null($deviation) ? '-' : (($deviation > 0 ? '+' : '') . $deviation . ' hari') }}</td></tr>
    </tbody>
</table>

<h2>5. REALISASI MILESTONE</h2>
<table>
    <thead><tr><th>Milestone</th><th>Owner</th><th>Target</th><th>Status</th><th>Kondisi Sukses</th></tr></thead>
    <tbody>
        @forelse($project->milestones->sortBy('target_date') as $milestone)
            <tr><td>{{ $milestone->milestone_name }}</td><td>{{ $milestone->owner?->username ?? '-' }}</td><td>{{ $date($milestone->target_date) }}</td><td>{{ ucwords(str_replace('_', ' ', $milestone->status)) }}</td><td class="note">{{ $milestone->success_condition ?: '-' }}</td></tr>
        @empty
            <tr><td colspan="5">Belum ada milestone yang dicatat.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="page-break"></div>
<h2>6. RINCIAN HASIL PEKERJAAN</h2>
@foreach($statusOptions as $status => $label)
    @php $group = $tasks->where('status', $status); @endphp
    @continue($group->isEmpty())
    <h3>{{ $label }} ({{ $group->count() }} Task)</h3>
    <table>
        <thead><tr><th style="width:5%">No</th><th style="width:32%">Task / Output</th><th>PIC / Reviewer</th><th>Estimasi / Aktual</th><th>Due Aktif</th></tr></thead>
        <tbody>
            @foreach($group as $task)
                <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $task->title }}</strong><br><span class="muted">{{ $task->task_code }}</span></td><td>{{ $task->primaryAssignee?->username ?? '-' }}<br><span class="muted">QA: {{ $task->reviewer?->username ?? '-' }}</span></td><td>{{ $md($task->estimate_hours) }} / {{ $md($task->actual_hours) }} MD</td><td>{{ $date($effectiveDueDate($task)) }}</td></tr>
            @endforeach
        </tbody>
    </table>
@endforeach

<h2>7. VERIFIKASI QA / UAT</h2>
<table>
    <thead><tr><th>Task</th><th>Acceptance Criteria</th><th>Hasil Pekerjaan</th><th>Catatan QA / UAT</th><th>Status</th></tr></thead>
    <tbody>
        @forelse($tasks->filter(fn($task) => in_array($task->status, ['review', 'done']) || filled($task->review_notes)) as $task)
            <tr><td>{{ $task->title }}<br><span class="muted">{{ $task->task_code }}</span></td><td class="note">{{ $task->acceptance_criteria ?: '-' }}</td><td class="note">{{ $task->developer_update_notes ?: '-' }}</td><td class="note">{{ $task->review_notes ?: '-' }}</td><td>{{ $statusOptions[$task->status] ?? $task->status }}</td></tr>
        @empty
            <tr><td colspan="5">Belum ada hasil QA / UAT yang tercatat.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>8. PENYAMPAIAN DAN MINUTES OF MEETING</h2>
<table>
    <thead><tr><th>Kode MoM</th><th>Judul</th><th>Tanggal</th><th>Milestone</th><th>Summary</th></tr></thead>
    <tbody>
        @forelse($finalMeetings as $meeting)
            <tr><td>{{ $meeting->meeting_code }}</td><td>{{ $meeting->title }}</td><td>{{ $datetime($meeting->meeting_at) }}</td><td>{{ $meeting->milestone?->milestone_name ?? 'Umum' }}</td><td class="note">{{ $meeting->summary ?: '-' }}</td></tr>
        @empty
            <tr><td colspan="5">Belum ada MoM Final pada project ini.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>9. OUTSTANDING, PENGECUALIAN, DAN TINDAK LANJUT</h2>
@if($outstanding->isEmpty())
    <p class="positive">Tidak ada pekerjaan outstanding. Seluruh task aktif telah selesai.</p>
@else
    <table>
        <thead><tr><th>Task</th><th>Status</th><th>PIC</th><th>Due Aktif</th><th>Catatan / Blocker</th></tr></thead>
        <tbody>
            @foreach($outstanding as $task)
                <tr><td>{{ $task->title }}<br><span class="muted">{{ $task->task_code }}</span></td><td>{{ $statusOptions[$task->status] ?? $task->status }}</td><td>{{ $task->primaryAssignee?->username ?? '-' }}</td><td>{{ $date($effectiveDueDate($task)) }}</td><td class="note">{{ $task->blocker_reason ?: $task->review_revision_notes ?: 'Perlu diselesaikan sesuai rencana tindak lanjut.' }}</td></tr>
            @endforeach
        </tbody>
    </table>
@endif
@if($cancelled->isNotEmpty())
    <p class="muted">Terdapat {{ $cancelled->count() }} task berstatus Cancelled dan tidak dihitung sebagai pekerjaan outstanding.</p>
@endif

<h2>10. KESIMPULAN DAN PERNYATAAN PENYELESAIAN</h2>
<div class="statement">
    Berdasarkan ruang lingkup, realisasi pekerjaan, hasil QA/UAT, dan dokumentasi Minutes of Meeting yang tercantum dalam laporan ini,
    project <strong>{{ $project->project_name }}</strong> ({{ $project->project_code }}) dinyatakan
    <strong>{{ $completion >= 100 && $outstanding->isEmpty() ? 'telah selesai dan siap diserahterimakan' : 'selesai secara bersyarat sesuai daftar outstanding' }}</strong>.
    Pekerjaan lanjutan yang masih tercatat wajib diselesaikan oleh PIC sesuai due date dan tetap menjadi bagian dari histori project.
</div>
<table>
    <tr><td class="label">Narasi Project Manager</td><td class="note">{{ $project->pm_commentary ?: '-' }}</td></tr>
    <tr><td class="label">Keputusan yang Dibutuhkan</td><td class="note">{{ $project->decisions_needed ?: 'Tidak ada keputusan tambahan yang dicatat.' }}</td></tr>
</table>

<div class="signature">
    <h2>11. PENGESAHAN</h2>
    <p>Dokumen ini dibuat sebagai bukti internal atas pelaksanaan, verifikasi, penyampaian, dan penyelesaian project.</p>
    <table>
        <tr><td>Tempat</td><td>........................................................</td><td>Tanggal</td><td>........................................................</td></tr>
    </table>
    <table class="signature">
        <tr><td>Disusun dan Diserahkan oleh,<br><strong>Project Manager</strong></td><td>Diterima dan Disetujui oleh,<br><strong>Business Owner / Sponsor</strong></td></tr>
        <tr><td class="signature-space"></td><td class="signature-space"></td></tr>
        <tr><td><span class="signature-name">{{ $project->projectManager?->username ?: '(........................................)' }}</span></td><td><span class="signature-name">{{ $project->businessOwner?->username ?: ($project->sponsor?->username ?: '(........................................)') }}</span></td></tr>
    </table>
</div>
</body>
</html>
