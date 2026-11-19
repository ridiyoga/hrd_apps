@extends('admin.layouts.dashboard')

@section('css')
<style>
    .presence-layout {
        display: grid;
        grid-template-columns: 360px minmax(0, 1fr);
        min-height: 680px;
        border: 1px solid #d9dee8;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }

    .presence-sidebar {
        min-width: 0;
        border-right: 1px solid #d9dee8;
        background: #f8f9fb;
    }

    .presence-sidebar-title,
    .presence-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        background: linear-gradient(360deg, #9055fd 0%, #c4a5fe 100%);
        color: #fff;
    }

    .presence-sidebar-title {
        font-size: 14px;
        font-weight: 800;
    }

    .presence-header h6 {
        margin: 0;
        color: #fff;
        font-size: 15px;
        font-weight: 800;
    }

    .presence-tools {
        padding: 12px;
        border-bottom: 1px solid #e5e7ef;
    }

    .presence-list {
        height: 900px;
        overflow-y: auto;
        padding: 8px;
    }

    .presence-item {
        width: 100%;
        display: grid;
        grid-template-columns: 32px minmax(0, 1fr);
        gap: 9px;
        align-items: center;
        padding: 9px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #2f2b3d;
        text-align: left;
    }

    .presence-item:hover,
    .presence-item.active {
        background: rgb(244, 238, 255);
        color: rgb(104, 64, 179);
    }

    .presence-avatar {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: rgb(222, 209, 247);
        color: rgb(104, 64, 179);
        font-size: 12px;
        font-weight: 800;
    }

    .presence-name {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 13px;
        font-weight: 800;
    }

    .presence-meta {
        display: block;
        margin-top: 2px;
        color: #7d8491;
        font-size: 11px;
        line-height: 1.35;
        word-break: break-word;
    }

    .presence-main {
        min-width: 0;
        background: #fff;
    }

    .presence-tabs {
        display: flex;
        gap: 2px;
        padding: 0 14px;
        border-bottom: 1px solid #d9dee8;
        background: #f2f4f7;
    }

    .presence-tab {
        padding: 8px 12px;
        border-bottom: 2px solid transparent;
        color: #4b5563;
        font-size: 13px;
        font-weight: 700;
    }

    .presence-tab.active {
        border-color: #71dd37;
        background: #fff;
        color: #2f2b3d;
    }

    .presence-content {
        padding: 18px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .metric {
        padding: 12px;
        border: 1px solid #e5e7ef;
        border-radius: 8px;
        background: #fff;
    }

    .metric-label {
        color: #7d8491;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .metric-value {
        margin-top: 5px;
        color: #2f2b3d;
        font-size: 18px;
        font-weight: 800;
    }

    .identity-panel {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        gap: 8px;
        max-width: 860px;
        margin-bottom: 18px;
    }

    .identity-label {
        padding: 7px 10px;
        background: #59595c;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        text-align: right;
    }

    .identity-value {
        min-height: 34px;
        display: flex;
        align-items: center;
        padding: 6px 10px;
        border: 1px solid #c9ced8;
        background: #fff;
        color: #2f2b3d;
        font-size: 13px;
        font-weight: 600;
        word-break: break-word;
    }

    .relation-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .relation-panel {
        border: 1px solid #d9dee8;
        border-radius: 8px;
        overflow: hidden;
    }

    .relation-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7ef;
        background: #f8f9fb;
        color: #2f2b3d;
        font-size: 13px;
        font-weight: 800;
    }

    .relation-list {
        display: grid;
        gap: 8px;
        max-height: 210px;
        overflow-y: auto;
        padding: 10px;
    }

    .downline-checklist {
        display: grid;
        gap: 6px;
        max-height: 260px;
        overflow-y: auto;
        padding: 2px;
    }

    .downline-check-item {
        display: grid;
        grid-template-columns: 20px minmax(0, 1fr);
        gap: 8px;
        align-items: start;
        padding: 8px 10px;
        border: 1px solid #e5e7ef;
        border-radius: 6px;
        background: #fff;
        cursor: pointer;
    }

    .downline-check-item:hover {
        border-color: #00a7d8;
        background: #f3fbfe;
    }

    .relation-item {
        width: 100%;
        display: grid;
        grid-template-columns: 86px minmax(0, 1fr);
        gap: 10px;
        align-items: start;
        padding: 9px 10px;
        border: 1px solid #eef0f4;
        border-radius: 6px;
        background: #fff;
        color: #2f2b3d;
        text-align: left;
    }

    .relation-item:hover {
        border-color: #00a7d8;
        background: #f3fbfe;
    }

    .report-filter {
        display: grid;
        grid-template-columns: repeat(2, 160px) auto;
        gap: 10px;
        align-items: end;
        margin-bottom: 12px;
    }

    .presence-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .presence-table th,
    .presence-table td {
        padding: 9px 10px;
        border: 1px solid #e5e7ef;
        vertical-align: top;
    }

    .presence-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8f9fb;
        color: #4b5563;
        font-weight: 800;
    }

    .table-wrap {
        max-height: 460px;
        overflow: auto;
        border: 1px solid #e5e7ef;
        border-radius: 8px;
    }

    .status-badge {
        display: inline-flex;
        min-height: 22px;
        align-items: center;
        justify-content: center;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-hadir {
        background: #e8fadf;
        color: #2f7b14;
    }

    .status-tidak_hadir {
        background: #ffe7e7;
        color: #b42318;
    }

    .status-belum_dijadwalkan,
    .status-kosong {
        background: #fff4d6;
        color: #946200;
    }

    .status-izin {
        background: rgb(244, 238, 255);
        color: #006d8f;
    }

    .status-libur {
        background: #eceef1;
        color: #4b5563;
    }

    .empty-state {
        padding: 16px;
        color: #8a8d93;
        font-size: 13px;
        text-align: center;
    }

    .table-loading {
        position: absolute;
        inset: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .72);
        backdrop-filter: blur(4px);
    }

    .loading-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: #6b7280;
        font-size: 13px;
    }

    .spinner {
        width: 36px;
        height: 36px;
        border: 4px solid #e5e7eb;
        border-top-color: #00a7d8;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 991px) {

        .presence-layout,
        .relation-grid,
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .presence-sidebar {
            border-right: 0;
            border-bottom: 1px solid #d9dee8;
        }

        .presence-list {
            height: 300px;
        }
    }

    @media (max-width: 575px) {

        .identity-panel,
        .report-filter {
            grid-template-columns: 1fr;
        }

        .identity-label {
            text-align: left;
        }
    }
</style>
@endsection

@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">DOWNLINE PRESENSI BAWAHAN</h5>
                    </div>
                </div>
                <div class="card-body position-relative">
                    <div id="tableLoading" class="table-loading d-none">
                        <div class="loading-content">
                            <div class="spinner"></div>
                            <span>Memuat data...</span>
                        </div>
                    </div>

                    <div class="presence-layout">
                        <aside class="presence-sidebar">
                            <div class="presence-sidebar-title">
                                <span>Karyawan</span>
                                <span id="summaryEmployee">0</span>
                            </div>
                            <div class="presence-tools">
                                <input type="text" id="searchEmployee" class="form-control form-control-sm" placeholder="Cari ID / nama / jabatan...">
                            </div>
                            <div id="employeeList" class="presence-list"></div>
                        </aside>

                        <section class="presence-main">
                            <div class="presence-header">
                                <h6>Report Presensi Bawahan</h6>
                                <span id="activeEmployeeLabel">-</span>
                            </div>
                            <div class="presence-content">
                                <div class="detail-grid">
                                    <div class="metric">
                                        <div class="metric-label">Scope Karyawan</div>
                                        <div id="metricScope" class="metric-value">0</div>
                                    </div>
                                    <div class="metric">
                                        <div class="metric-label">Downline</div>
                                        <div id="metricDownline" class="metric-value">0</div>
                                    </div>
                                    <div class="metric">
                                        <div class="metric-label">Hadir</div>
                                        <div id="metricHadir" class="metric-value">0</div>
                                    </div>
                                    <div class="metric">
                                        <div class="metric-label">Tidak Hadir</div>
                                        <div id="metricTidakHadir" class="metric-value">0</div>
                                    </div>
                                </div>

                                <div class="identity-panel">
                                    <div class="identity-label">ID Karyawan</div>
                                    <div id="detailId" class="identity-value">-</div>

                                    <div class="identity-label">Nama</div>
                                    <div id="detailName" class="identity-value">-</div>

                                    <div class="identity-label">Jabatan</div>
                                    <div id="detailRole" class="identity-value">-</div>

                                    <div class="identity-label">Unit</div>
                                    <div id="detailUnit" class="identity-value">-</div>
                                </div>

                                <div class="relation-panel mb-4">
                                    <div class="relation-header">
                                        <span>Susun Bawahan</span>
                                        <button type="button" id="btnSaveRelations" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-content-save-outline me-1"></i>Simpan
                                        </button>
                                    </div>
                                    <div class="p-3">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <label class="form-label mb-0">Semua Karyawan</label>
                                            <span id="structureDownlineCount" class="presence-meta">0 karyawan</span>
                                        </div>
                                        <input type="text" id="searchDownline" class="form-control form-control-sm mb-2" placeholder="Cari nama / ID / jabatan / unit...">
                                        <div id="downlineChecklist" class="downline-checklist"></div>
                                    </div>
                                </div>

                                <div class="relation-grid">
                                    <div class="relation-panel">
                                        <div class="relation-header">
                                            <span><i class="mdi mdi-arrow-up-bold-outline me-1"></i>Upline</span>
                                            <span id="uplineCount">0</span>
                                        </div>
                                        <div id="uplineList" class="relation-list"></div>
                                    </div>

                                    <div class="relation-panel">
                                        <div class="relation-header">
                                            <span><i class="mdi mdi-arrow-down-bold-outline me-1"></i>Downline Langsung</span>
                                            <span id="directDownlineCount">0</span>
                                        </div>
                                        <div id="directDownlineList" class="relation-list"></div>
                                    </div>
                                </div>

                                <div class="report-filter">
                                    <div>
                                        <label class="form-label mb-1" for="startDate">Tanggal Awal</label>
                                        <input type="date" id="startDate" class="form-control form-control-sm">
                                    </div>
                                    <div>
                                        <label class="form-label mb-1" for="endDate">Tanggal Akhir</label>
                                        <input type="date" id="endDate" class="form-control form-control-sm">
                                    </div>
                                    <button type="button" id="btnLoadAttendance" class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-refresh me-1"></i> Tampilkan
                                    </button>
                                </div>

                                <div class="table-wrap">
                                    <table class="presence-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>ID Karyawan</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Shift</th>
                                                <th>Masuk</th>
                                                <th>Pulang</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attendanceRows"></tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const holding = "{{ $holding->holding_code }}";
        const menuId = $('#asoy_sub2').val() || $('#asoy_sub').val() || $('#asoy').val();
        let employees = [];
        let activeEmployeeId = null;
        let selectedDownlineIds = [];

        const today = "{{ now()->toDateString() }}";
        $('#startDate').val(today);
        $('#endDate').val(today);

        if (menuId) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('menu_id', menuId);

            $.ajax({
                type: 'POST',
                url: "{{ url('/recruitment/update_menu') }}",
                data: formData,
                contentType: false,
                processData: false
            });
        }

        function showTableLoading() {
            $('#tableLoading').removeClass('d-none');
        }

        function hideTableLoading() {
            $('#tableLoading').addClass('d-none');
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function initial(employee) {
            return (employee.name || '?').trim().charAt(0).toUpperCase();
        }

        function unitLabel(employee) {
            if (!employee) return '-';
            return `${employee.departemen || '-'} / ${employee.divisi || '-'} / ${employee.bagian || '-'}`;
        }

        function renderEmployeeList(keyword = '') {
            const term = keyword.trim().toLowerCase();
            const filtered = employees.filter(function(employee) {
                return [
                    employee.work_force_id,
                    employee.name,
                    employee.role,
                    employee.departemen,
                    employee.divisi,
                    employee.bagian
                ].join(' ').toLowerCase().includes(term);
            });

            $('#summaryEmployee').text(filtered.length + ' / ' + employees.length);
            $('#employeeList').html(filtered.map(function(employee) {
                const active = employee.id === activeEmployeeId ? 'active' : '';
                return `
                        <button type="button" class="presence-item ${active}" data-id="${escapeHtml(employee.id)}">
                            <span class="presence-avatar">${escapeHtml(initial(employee))}</span>
                            <span>
                                <span class="presence-name">${escapeHtml(employee.name)}</span>
                                <span class="presence-meta">${escapeHtml(employee.work_force_id || employee.id)} | ${escapeHtml(employee.role)}</span>
                                <span class="presence-meta">${escapeHtml(employee.bagian)}</span>
                            </span>
                        </button>
                    `;
            }).join('') || '<div class="empty-state">Karyawan tidak ditemukan.</div>');
        }

        function renderRelationList(target, items) {
            if (!items || items.length === 0) {
                $(target).html('<div class="empty-state">Tidak ada data.</div>');
                return;
            }

            $(target).html(items.map(function(employee) {
                return `
                        <button type="button" class="relation-item" data-id="${escapeHtml(employee.id)}">
                            <span class="presence-meta">${escapeHtml(employee.work_force_id || employee.id)}</span>
                            <span>
                                <span class="presence-name">${escapeHtml(employee.name)}</span>
                                <span class="presence-meta">${escapeHtml(employee.role)} | ${escapeHtml(employee.bagian)}</span>
                            </span>
                        </button>
                    `;
            }).join(''));
        }

        function renderDownlineChecklist() {
            const term = ($('#searchDownline').val() || '').trim().toLowerCase();
            const filtered = employees.filter(function(employee) {
                if (String(employee.id) === String(activeEmployeeId)) return false;
                return [
                    employee.work_force_id,
                    employee.name,
                    employee.role,
                    employee.title,
                    employee.departemen,
                    employee.divisi,
                    employee.bagian
                ].join(' ').toLowerCase().includes(term);
            });

            $('#structureDownlineCount').text(filtered.length + ' / ' + Math.max(employees.length - 1, 0) + ' karyawan');
            $('#downlineChecklist').html(filtered.length ? filtered.map(function(item) {
                const checked = selectedDownlineIds.includes(String(item.id)) ? 'checked' : '';
                return `<label class="downline-check-item">
                        <input type="checkbox" name="downline_ids[]" value="${escapeHtml(item.id)}" ${checked}>
                        <span><span class="presence-name">${escapeHtml(item.name)}</span><span class="presence-meta">${escapeHtml(item.role)} | ${escapeHtml(item.bagian)}</span></span>
                    </label>`;
            }).join('') : '<div class="empty-state">Karyawan tidak ditemukan.</div>');
        }

        function statusClass(statusGroup) {
            return 'status-' + String(statusGroup || 'kosong').replace(/[^a-zA-Z0-9_]/g, '');
        }

        function renderAttendanceRows(rows) {
            if (!rows || rows.length === 0) {
                $('#attendanceRows').html('<tr><td colspan="8" class="empty-state">Tidak ada data presensi.</td></tr>');
                return;
            }

            $('#attendanceRows').html(rows.map(function(row) {
                return `
                        <tr>
                            <td>${escapeHtml(row.hari)}, ${escapeHtml(row.tanggal)}</td>
                            <td>${escapeHtml(row.work_force_id || row.karyawan_id)}</td>
                            <td>${escapeHtml(row.nama_karyawan)}</td>
                            <td>${escapeHtml(row.jabatan)}<br><span class="presence-meta">${escapeHtml(row.bagian)}</span></td>
                            <td>${escapeHtml(row.shift)}</td>
                            <td>${escapeHtml(row.masuk || '-')}</td>
                            <td>${escapeHtml(row.pulang || '-')}</td>
                            <td><span class="status-badge ${statusClass(row.status_group)}">${escapeHtml(row.status || '-')}</span></td>
                        </tr>
                    `;
            }).join(''));
        }

        function loadAttendance() {
            if (!activeEmployeeId) return;

            showTableLoading();

            $.ajax({
                url: "{{ url('reportpresensibawahan/attendance') }}/" + holding + "/" + activeEmployeeId,
                method: 'GET',
                data: {
                    start_date: $('#startDate').val(),
                    end_date: $('#endDate').val()
                },
                success: function(response) {
                    const summary = response.summary || {};
                    $('#metricHadir').text(summary.hadir || 0);
                    $('#metricTidakHadir').text(summary.tidak_hadir || 0);
                    renderAttendanceRows(response.data || []);
                    hideTableLoading();
                },
                error: function(data) {
                    hideTableLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON?.message || 'Terjadi kesalahan pada server'
                    });
                }
            });
        }

        function loadScope(employeeId) {
            if (!employeeId) return;

            activeEmployeeId = employeeId;
            renderEmployeeList($('#searchEmployee').val());
            showTableLoading();

            $.ajax({
                url: "{{ url('reportpresensibawahan/scope') }}/" + holding + "/" + employeeId,
                method: 'GET',
                success: function(response) {
                    const data = response.data || {};
                    const employee = data.employee || {};
                    const summary = data.summary || {};

                    $('#activeEmployeeLabel').text(employee.name || '-');
                    $('#detailId').text((employee.work_force_id || employee.id || '-'));
                    $('#detailName').text(employee.name || '-');
                    $('#detailRole').text((employee.role || '-') + ' / ' + (employee.title || '-'));
                    $('#detailUnit').text(unitLabel(employee));
                    $('#metricScope').text(summary.total_scope || 0);
                    $('#metricDownline').text(summary.total_downline || 0);
                    $('#uplineCount').text((data.uplines || []).length);
                    $('#directDownlineCount').text((data.direct_downlines || []).length);
                    renderRelationList('#uplineList', data.uplines || []);
                    renderRelationList('#directDownlineList', data.direct_downlines || []);
                    selectedDownlineIds = (data.direct_downlines || []).map(item => String(item.id));
                    renderDownlineChecklist();
                    hideTableLoading();
                    loadAttendance();
                },
                error: function(data) {
                    hideTableLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON?.message || 'Terjadi kesalahan pada server'
                    });
                }
            });
        }

        function loadEmployees() {
            showTableLoading();

            $.ajax({
                url: "{{ url('reportpresensibawahan/employees') }}/" + holding,
                method: 'GET',
                success: function(response) {
                    employees = response.data || [];
                    renderEmployeeList();
                    hideTableLoading();

                    if (employees.length > 0) {
                        loadScope(employees[0].id);
                    }
                },
                error: function(data) {
                    hideTableLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON?.message || 'Terjadi kesalahan pada server'
                    });
                }
            });
        }

        $('#searchEmployee').on('keyup', function() {
            renderEmployeeList($(this).val());
        });

        $('#searchDownline').on('keyup', function() {
            renderDownlineChecklist();
        });

        $(document).on('change', '#downlineChecklist input[name="downline_ids[]"]', function() {
            const id = String(this.value);
            if (this.checked && !selectedDownlineIds.includes(id)) {
                selectedDownlineIds.push(id);
            } else if (!this.checked) {
                selectedDownlineIds = selectedDownlineIds.filter(selectedId => selectedId !== id);
            }
        });

        $('#btnLoadAttendance').on('click', function() {
            loadAttendance();
        });

        $('#btnSaveRelations').on('click', function() {
            if (!activeEmployeeId) return;
            $.ajax({
                url: "{{ url('reportpresensibawahan/relations') }}/" + holding,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    atasan_id: activeEmployeeId,
                    bawahan_ids: selectedDownlineIds
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadScope(activeEmployeeId);
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyimpan',
                        text: data.responseJSON?.message || 'Terjadi kesalahan pada server'
                    });
                }
            });
        });

        $(document).on('click', '.presence-item, .relation-item', function() {
            loadScope($(this).data('id'));
        });

        loadEmployees();
    });
</script>
@endsection