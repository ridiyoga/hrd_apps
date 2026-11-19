@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - Report Ticket
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/assets_users/css/select2.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    .futuristic-header {
        padding: 28px 20px 18px;
    }

    .futuristic-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(145deg, #7b2ff7, #9b6dff);
        color: white;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
    }

    .report-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .report-toolbar .toolbar-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: 15px;
        color: #4c1d95;
    }

    .report-toolbar .toolbar-title i {
        color: #7c3aed;
        font-size: 16px;
    }

    .report-actions-wrapper.d-flex {
        display: flex;
        gap: 8px;
    }

    .filter-bar {
        display: flex;
        gap: 8px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .filter-bar .form-select,
    .filter-bar .form-control {
        border-radius: 10px;
        border: 1.5px solid #ede9fe;
        font-size: 12px;
    }

    .filter-bar .form-select:focus,
    .filter-bar .form-control:focus {
        border-color: #c4b5fd;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .08);
    }

    .form-control::placeholder,
    input::placeholder,
    textarea::placeholder {
        color: #c4b5fd !important;
        opacity: 1;
    }

    /* =====================
       TABLE
    ===================== */
    #table-report {
        border-collapse: separate;
        border-spacing: 0;
    }

    #table-report thead th {
        background: #f5f3ff;
        color: #6b21a8;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        vertical-align: middle;
        border-bottom: 1px solid #ede9fe !important;
    }

    #table-report tbody td {
        font-size: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f0ff;
    }

    #table-report tbody tr:hover {
        background: #faf9ff;
    }

    #table-report tbody td {
        white-space: nowrap;
    }

    /* Kolom teks panjang (Alasan, Sebelum, Sesudah): dibatasi lebarnya, boleh wrap */
    .cell-long {
        min-width: 200px;
        max-width: 260px;
        white-space: normal;
        word-break: break-word;
    }

    /* Scrollbar horizontal selalu terlihat di desktop */
    @media (min-width: 992px) {
        .dataTables_scrollBody {
            overflow-x: scroll !important;
        }
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        height: 12px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-track {
        background: #f5f3ff;
        border-radius: 10px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: #c4b5fd;
        border-radius: 10px;
    }
    .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
        background: #7c3aed;
    }
    .dataTables_scrollBody {
        scrollbar-color: #c4b5fd #f5f3ff;
        scrollbar-width: thin;
    }

    /* =====================
       STATUS PILL
    ===================== */
    .status-pill {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .status-draft {
        background: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    .status-pending, .status-waiting {
        background: #fff8e1;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-process, .status-ready {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .status-approved, .status-done {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-feedback, .status-warning {
        background: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }

    .status-rejected {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }

    .bg-purple {
        background: #7c3aed !important;
    }

    /* =====================
       AKSI BUTTON
    ===================== */
    .btn-view {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3e8ff;
        color: #7c3aed;
        border: none;
        text-decoration: none;
        transition: background .2s;
    }
    .btn-view:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    /* =====================
       DATATABLES OVERRIDE
    ===================== */
    .dt-buttons .btn {
        font-size: 12px;
        border-radius: 50px !important;
        margin-right: 6px;
        font-weight: 600;
    }

    .dataTables_filter input {
        border-radius: 10px;
        border: 1.5px solid #ede9fe;
        font-size: 12px;
    }

    .dataTables_length select {
        border-radius: 8px;
        border: 1.5px solid #ede9fe;
        font-size: 12px;
    }

    .dataTables_info {
        font-size: 12px;
        color: #6b7280;
    }

    .page-link {
        color: #7c3aed;
        border-radius: 8px !important;
        margin: 0 2px;
        font-size: 12px;
    }
    .page-item.active .page-link {
        background-color: #7c3aed;
        border-color: #7c3aed;
        color: #fff;
    }
    .page-item .page-link:hover {
        background-color: #f5f3ff;
        color: #6d28d9;
    }
</style>
@endsection

@section('content_top')
<div class="card" style="position: relative;">
    <div class="futuristic-header text-center">
        <div class="futuristic-icon">
            <i class="fa-solid fa-table-list"></i>
        </div>
        <h5 class="mt-3 mb-1" style="color: #623acf; font-size: 18px;">Report Ticket</h5>
        <small class="text-muted">Riwayat pengajuan ubah/hapus data</small>
    </div>

    <div class="card-body px-3 px-md-4 pb-4">
        <div class="rounded-3 p-3 mb-4" style="background:#faf7ff; border:1.5px solid #ede9fe;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fa-solid fa-filter" style="color:#7c3aed; font-size:13px;"></i>
                <span style="font-size:12px; font-weight:700; color:#4c1d95; text-transform:uppercase; letter-spacing:.5px;">
                    Filter Data
                </span>
            </div>

            <div class="row g-2">
                <div class="col-md-6 col-12">
                    <label style="font-size:11px; font-weight:600; color:#6b21a8; margin-bottom:4px; display:block;">
                        <i class="fa-solid fa-calendar-day me-1"></i>Dari Tanggal
                    </label>
                    <input type="date" id="filter-date-start"
                        class="form-control form-control-sm"
                        style="border-radius:10px; border:1.5px solid #ede9fe; font-size:12px;">
                </div>
                <div class="col-md-6 col-12">
                    <label style="font-size:11px; font-weight:600; color:#6b21a8; margin-bottom:4px; display:block;">
                        <i class="fa-solid fa-calendar-check me-1"></i>Sampai Tanggal
                    </label>
                    <input type="date" id="filter-date-end"
                        class="form-control form-control-sm"
                        style="border-radius:10px; border:1.5px solid #ede9fe; font-size:12px;">
                </div>
                <div class="col-md-4 col-12">
                    <label style="font-size:11px; font-weight:600; color:#6b21a8; margin-bottom:4px; display:block;">
                        <i class="fa-solid fa-building me-1"></i>Departemen
                    </label>
                    <select id="filter-departemen"
                        class="form-select form-select-sm"
                        style="border-radius:10px; border:1.5px solid #ede9fe; font-size:12px;">
                        <option value="">-- Semua Departemen --</option>
                    </select>
                </div>

                <div class="col-md-4 col-6">
                    <label style="font-size:11px; font-weight:600; color:#6b21a8; margin-bottom:4px; display:block;">
                        <i class="fa-solid fa-circle-half-stroke me-1"></i>Status
                    </label>
                    <select id="filter-status"
                        class="form-select form-select-sm"
                        style="border-radius:10px; border:1.5px solid #ede9fe; font-size:12px;">
                        <option value="">Semua Status</option>
                        <option value="T00">Draft</option>
                        <option value="T01">Diajukan</option>
                        <option value="T02">Disetujui</option>
                        <option value="T03">Ditugaskan</option>
                        <option value="T04">Dikerjakan</option>
                        <option value="T05">Menunggu Respon Peminta</option>
                        <option value="T06">Ditutup</option>
                        <option value="T07">Respon Balasan Peminta</option>
                        <option value="T08">Dibatalkan</option>
                        <option value="T09">Disetujui Atasan 1</option>
                        <option value="T10">Disetujui Atasan 2</option>
                        <option value="T11">Menunggu Divalidasi</option>
                        <option value="T12">Menunggu Diperiksa</option>
                    </select>
                </div>
                <div class="col-md-4 col-6">
                    <label style="font-size:11px; font-weight:600; color:#6b21a8; margin-bottom:4px; display:block;">
                        <i class="fa-solid fa-server me-1"></i>Sistem
                    </label>
                    <select id="filter-kategori"
                        class="form-select form-select-sm"
                        style="border-radius:10px; border:1.5px solid #ede9fe; font-size:12px;">
                        <option value="">Semua Sistem</option>
                        <option value="1">Epicor ERP</option>
                        <option value="2">Accurate Online SP</option>
                        <option value="3">Accurate Online AJA</option>
                    </select>
                </div>

                <div class="col-8 mt-3">
                    <button type="button" id="btn-apply-filter"
                        class="btn btn-primary btn-sm w-100 rounded-pill fw-bold py-2"
                        style="font-size:12px;">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Cari / Tampilkan Data
                    </button>
                </div>
                <div class="col-4 mt-3">
                    <button type="button" id="btn-reset-filter"
                        class="btn btn-sm w-100 rounded-pill fw-bold py-2"
                        style="font-size:12px; background:#f3e8ff; color:#7c3aed; border:none;">
                        <i class="fa-solid fa-rotate-left me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="report-toolbar">
            <div class="toolbar-title">
                <i class="fa-solid fa-list-check"></i>
                <span id="report-title-text">Pilih filter dan klik Cari / Tampilkan Data</span>
            </div>
            <div class="report-actions"></div>
        </div>

        <div id="empty-state" class="text-center py-5 text-muted">
            <i class="fa-solid fa-filter fa-2x mb-2 d-block" style="color:#d8b4fe;"></i>
            <div style="font-size:13px;">Pilih filter di atas lalu klik tombol <strong>Cari / Tampilkan Data</strong></div>
        </div>

        <div id="table-wrapper" style="display:none;">
            <div>
                <table id="table-report" class="table table-hover align-middle nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nomor Ticket</th>
                            <th>Sistem</th>
                            <th>Modul</th>
                            <th>Sub Modul</th>
                            <th>Status</th>
                            <th>PIC</th>
                            <th class="text-nowrap" title="Tanggal Divalidasi">Tgl Divalidasi</th>
                            <th class="text-nowrap" title="Tanggal Ditugaskan oleh Assigner">Tgl Ditugaskan</th>
                            <th class="text-nowrap" title="Tanggal Dikerjakan Pertama Kali oleh PIC">Tgl Mulai Dikerjakan</th>
                            <th class="text-nowrap" title="Tanggal Terakhir Selesai Dikerjakan oleh PIC (Termasuk Revisi)">Tgl Selesai Dikerjakan</th>
                            <th class="text-nowrap" title="Tanggal Closed (Selesai)">Tgl Closed</th>
                            <th>Line Dok</th>
                            <th>No. Dokumen</th>
                            <th>Line Sub</th>
                            <th>Alasan Ubah</th>
                            <th>Sebelum</th>
                            <th>Sesudah</th>
                            <th>Departemen</th>
                            <th>Pengaju</th>
                            <th>Area</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
        <form method="POST" action="{{ route('logout_user') }}">
            @csrf
            <a onclick="event.preventDefault(); this.closest('form').submit();"
                class="btn btn-sm btn-danger light">
                <i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout
            </a>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2"
                data-bs-dismiss="offcanvas">Batal</a>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) Swal.close();
    });

    $(document).ready(function () {
        loadDepartemen();

        const envMap = { 1: 'Epicor ERP', 2: 'Accurate Online SP', 3: 'Accurate Online AJA' };

        function getStatusLabel(ticketStatus, approvalStatus) {
            if (approvalStatus === 'rejected' || ticketStatus === 'T08') {
                return 'Dibatalkan';
            }
            const map = {
                T00: 'Draft',
                T01: 'Menunggu Persetujuan',
                T02: 'Disetujui',
                T03: 'Ditugaskan',
                T04: 'Dikerjakan',
                T05: 'Menunggu Respon Peminta',
                T06: 'Selesai',
                T07: 'Respon Balasan Peminta',
                T08: 'Dibatalkan',
                T09: 'Disetujui Atasan 1',
                T10: 'Disetujui Atasan 2',
                T11: 'Menunggu Divalidasi',
                T12: 'Menunggu Pemeriksaan',
                T13: 'Menunggu Ditugaskan',
                T14: 'Pending',
                T15: 'Ditutup Otomatis oleh Sistem'
            };
            return map[ticketStatus] || ticketStatus || '-';
        }

        function getStatusPill(ticketStatus, approvalStatus) {
            if (approvalStatus === 'rejected' || ticketStatus === 'T08') {
                return '<span class="status-pill status-rejected"><i class="fa-solid fa-ban me-1"></i>Ditolak</span>';
            }
            const map = {
                T00: '<span class="status-pill status-draft"><i class="fa-solid fa-pen-to-square me-1"></i>Draft</span>',
                T01: '<span class="status-pill status-pending"><i class="fa-solid fa-clock me-1"></i>Menunggu Persetujuan</span>',
                T02: '<span class="status-pill status-approved"><i class="fa-solid fa-circle-check me-1"></i>Disetujui</span>',
                T03: '<span class="status-pill status-process"><i class="fa-solid fa-user-check me-1"></i>Ditugaskan</span>',
                T04: '<span class="status-pill status-process"><i class="fa-solid fa-spinner me-1"></i>Dikerjakan</span>',
                T05: '<span class="status-pill status-waiting"><i class="fa-solid fa-hourglass-half me-1"></i>Menunggu Respon Peminta</span>',
                T06: '<span class="status-pill status-approved"><i class="fa-solid fa-circle-check me-1"></i>Selesai</span>',
                T07: '<span class="status-pill status-feedback"><i class="fa-solid fa-comment-dots me-1"></i>Respon Balasan Peminta</span>',
                T08: '<span class="status-pill status-rejected"><i class="fa-solid fa-ban me-1"></i>Dibatalkan</span>',
                T09: '<span class="status-pill status-approved"><i class="fa-solid fa-check-double me-1"></i>Disetujui Atasan 1</span>',
                T10: '<span class="status-pill status-approved"><i class="fa-solid fa-check-double me-1"></i>Disetujui Atasan 2</span>',
                T11: '<span class="status-pill status-process"><i class="fa-solid fa-clipboard-check me-1"></i>Menunggu Divalidasi</span>',
                T12: '<span class="status-pill status-waiting"><i class="fa-solid fa-magnifying-glass me-1"></i>Menunggu Pemeriksaan</span>',
                T13: '<span class="status-pill status-waiting"><i class="fa-solid fa-user-check me-1"></i>Menunggu Ditugaskan</span>',
                T14: '<span class="status-pill status-pending"><i class="fa-solid fa-clock me-1"></i>Pending</span>',
                T15: '<span class="status-pill status-approved"><i class="fa-solid fa-circle-check me-1"></i>Ditutup Otomatis</span>'
            };
            return map[ticketStatus] || `<span class="status-pill status-draft">${ticketStatus || '-'}</span>`;
        }

        let allData   = [];
        let tableInst = null;

        function longCell(d, type) {
            if (type !== 'display') return d ?? '';
            if (d === null || d === undefined || d === '') return '-';
            return '<div class="cell-long">' + $('<div>').text(d).html() + '</div>';
        }

        function formatDateTime(data) {
            if (!data || data === '-') return '-';
            const str = String(data).replace(' ', 'T');
            const d = new Date(str);
            const targetDate = isNaN(d.getTime()) ? new Date(data) : d;
            if (isNaN(targetDate.getTime())) return data;
            const b = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            const hh = String(targetDate.getHours()).padStart(2, '0');
            const mm = String(targetDate.getMinutes()).padStart(2, '0');
            return `${targetDate.getDate()} ${b[targetDate.getMonth()]} ${targetDate.getFullYear()} ${hh}:${mm}`;
        }

        function initTable() {
            if (tableInst) {
                tableInst.destroy();
                $('#table-report tbody').empty();
            }

            tableInst = $('#table-report').DataTable({
                data: [],
                columns: [
                    {
                        data: null, orderable: false, searchable: false,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1
                    },
                    {
                        data: 'created_at',
                        render: function (data) {
                            if (!data) return '-';
                            const d = new Date(data);
                            const b = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                            return d.getDate() + ' ' + b[d.getMonth()] + ' ' + d.getFullYear();
                        }
                    },
                    {
                        data: 'nomor_ticket', defaultContent: '-',
                        render: d => '<span class="fw-bold text-nowrap">' + (d ?? '-') + '</span>'
                    },
                    {
                        data: 'ticket_menu',
                        render: (d, t, r) => envMap[r.ticket_kategori ?? r.ticket_menu?.kategori] ?? '-'
                    },
                    {
                        data: 'ticket_menu',
                        render: (d, t, r) => r.ticket_menu?.nama_menu ?? '-'
                    },
                    {
                        data: 'ticket_sub_menu',
                        render: (d, t, r) => r.ticket_sub_menu?.nama_sub_menu ?? '-'
                    },
                    {
                        data: 'ticket_status',
                        render: function (data, type, row) {
                            if (type === 'export' || type === 'sort' || type === 'filter') {
                                return getStatusLabel(data, row.approval_status);
                            }
                            return getStatusPill(data, row.approval_status);
                        }
                    },
                    {
                        data: 'pic_name',
                        render: d => d && d !== '-'
                            ? `<span class="badge bg-purple text-white px-2 py-1"><i class="fa-solid fa-user-tie me-1"></i>${d}</span>`
                            : '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'validator_at',
                        defaultContent: '-',
                        render: function (data, type) {
                            if (!data || data === '-') return type === 'sort' ? '' : '-';
                            if (type === 'sort') return data;
                            return formatDateTime(data);
                        }
                    },
                    {
                        data: 'assign_at',
                        defaultContent: '-',
                        render: function (data, type) {
                            if (!data || data === '-') return type === 'sort' ? '' : '-';
                            if (type === 'sort') return data;
                            return formatDateTime(data);
                        }
                    },
                    {
                        data: 'pic_started_at',
                        defaultContent: '-',
                        render: function (data, type) {
                            if (!data || data === '-') return type === 'sort' ? '' : '-';
                            if (type === 'sort') return data;
                            return formatDateTime(data);
                        }
                    },
                    {
                        data: 'finish_at',
                        defaultContent: '-',
                        render: function (data, type) {
                            if (!data || data === '-') return type === 'sort' ? '' : '-';
                            if (type === 'sort') return data;
                            return formatDateTime(data);
                        }
                    },
                    {
                        data: 'closed_at',
                        defaultContent: '-',
                        render: function (data, type) {
                            if (!data || data === '-') return type === 'sort' ? '' : '-';
                            if (type === 'sort') return data;
                            return formatDateTime(data);
                        }
                    },
                    { data: 'line_detail',     defaultContent: '-' },
                    { data: 'nomor_detail',    defaultContent: '-' },
                    { data: 'line_sub_detail', defaultContent: '-' },
                    { data: 'alasan_ubah',     defaultContent: '-', render: longCell },
                    { data: 'before_ubah',      defaultContent: '-', render: longCell },
                    { data: 'after_ubah',       defaultContent: '-', render: longCell },
                    { data: 'departemen',      defaultContent: '-' },
                    { data: 'requestor_name',  defaultContent: '-' },
                    { data: 'site_name',       defaultContent: '-' },
                    {
                        data: null, orderable: false, searchable: false,
                        className: 'text-center',
                        render: function (d, t, row) {
                            let url = "{{ route('ticket.lihat', ['menu_id' => $menu_id, 'ticket_id' => ':id']) }}"
                                .replace(':id', row.id);
                            return `<a href="${url}" class="btn-view" title="Lihat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>`;
                        }
                    },
                ],
                order: [],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                scrollX: true,
                autoWidth: false,
                language: {
                    emptyTable:   'Tidak ada data',
                    zeroRecords:  'Data tidak ditemukan',
                    search:       'Cari di tabel:',
                    lengthMenu:   'Tampilkan _MENU_ data',
                    info:         'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                    infoEmpty:    'Menampilkan 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    paginate: {
                        previous: '<i class="fa-solid fa-chevron-left"></i>',
                        next:     '<i class="fa-solid fa-chevron-right"></i>'
                    }
                },
                dom: '<"report-actions-wrapper"B>frtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa-solid fa-file-excel me-1"></i> Export Excel',
                        className: 'btn btn-sm btn-primary',
                        title: 'Report_Ticket_' + new Date().toISOString().slice(0,10),
                        exportOptions: {
                            columns: ':not(:last-child)',
                            format: {
                                body: function (data, rowIdx, colIdx, cellNode) {
                                    return $('<div>').html(data).text().trim();
                                }
                            }
                        }
                    },
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa-solid fa-copy me-1"></i> Copy',
                        className: 'btn btn-sm btn-outline-primary',
                        exportOptions: {
                            columns: ':not(:last-child)',
                            format: {
                                body: function (data) {
                                    return $('<div>').html(data).text().trim();
                                }
                            }
                        }
                    },
                ],
            });

            $('.report-actions-wrapper').addClass('d-flex').appendTo('.report-actions');
        }

        function loadData() {
            const start   = $('#filter-date-start').val();
            const end     = $('#filter-date-end').val();
            const dept    = $('#filter-departemen').val();
            const status  = $('#filter-status').val();
            const kat     = $('#filter-kategori').val();

            $('#btn-apply-filter')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span>Mencari Data...');

            $.ajax({
                url: "{{ route('ticket.list_report') }}",
                type: 'GET',
                data: {
                    start_date: start,
                    end_date:   end,
                    departemen: dept,
                    status:     status,
                    kategori:   kat,
                },
                success: function (res) {
                    $('#btn-apply-filter')
                        .prop('disabled', false)
                        .html('<i class="fa-solid fa-magnifying-glass me-1"></i>Cari / Tampilkan Data');

                    if (res.code == 200) {
                        allData = res.data || [];

                        $('#empty-state').hide();
                        $('#table-wrapper').show();

                        if (!tableInst) initTable();

                        tableInst.clear().rows.add(allData).draw();

                        let labelParts = [];
                        if (start && end) labelParts.push(`${start} s/d ${end}`);
                        else if (start)   labelParts.push(`dari ${start}`);
                        else if (end)     labelParts.push(`sampai ${end}`);

                        if (status) {
                            labelParts.push(`Status: ${getStatusLabel(status)}`);
                        }
                        if (kat) {
                            labelParts.push(`Sistem: ${envMap[kat] ?? kat}`);
                        }

                        const rangeLabel = labelParts.length > 0 ? ` (${labelParts.join(' | ')})` : '';

                        $('#report-title-text').text(
                            `${allData.length} baris data ditemukan${rangeLabel}`
                        );

                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: window.getFriendlyErrorMessage(res.message, 'Gagal memuat data laporan.') });
                    }
                },
                error: function (xhr) {
                    $('#btn-apply-filter')
                        .prop('disabled', false)
                        .html('<i class="fa-solid fa-magnifying-glass me-1"></i>Cari / Tampilkan Data');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal memuat data',
                        text: window.getFriendlyErrorMessage(xhr, 'Terjadi gangguan saat memuat data laporan.')
                    });
                }
            });
        }

        $('#btn-apply-filter').on('click', loadData);

        $('#btn-reset-filter').on('click', function () {
            $('#filter-date-start, #filter-date-end').val('');
            $('#filter-status, #filter-kategori, #filter-departemen').val('');

            allData = [];
            if (tableInst) {
                tableInst.clear().draw();
            }
            $('#table-wrapper').hide();
            $('#empty-state').show();
            $('#report-title-text').text('Pilih filter dan klik Cari / Tampilkan Data');
        });

        $('#filter-date-start, #filter-date-end').on('keydown', function (e) {
            if (e.key === 'Enter') loadData();
        });

        function loadDepartemen() {
            $.ajax({
                url: "{{ route('ticket.get_departemen') }}",
                type: 'GET',
                success: function (res) {
                    if (res && res.code == 200) {
                        const sel = $('#filter-departemen');
                        sel.find('option:not(:first)').remove();

                        (res.data || []).forEach(dep => {
                            sel.append(`<option value="${dep.nama}">${dep.nama}</option>`);
                        });
                    }
                },
                error: function (xhr) {
                    console.warn('Gagal memuat daftar departemen', xhr);
                }
            });
        }
    });
</script>
@endsection