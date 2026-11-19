@extends('admin.layouts.dashboard')

@section('css')
    @include('css_baru')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .kpi-page { font-family: 'Plus Jakarta Sans', sans-serif; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card-item {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid #ede9fe;
            box-shadow: 0 2px 6px rgba(109, 70, 218, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card-item:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109, 70, 218, 0.1); }
        .stat-card-item .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .stat-card-item .stat-val { font-size: 22px; font-weight: 800; line-height: 1.1; }
        .stat-card-item .stat-label {
            font-size: 11px; font-weight: 600; color: #6b7280;
            text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px;
        }

        .stat-purple { background: #faf7ff; border-color: #ede9fe; }
        .stat-purple .stat-icon { background: #ede9fe; color: #7c3aed; }
        .stat-purple .stat-val { color: #5b21b6; }
        .stat-blue { background: #f0f9ff; border-color: #e0f2fe; }
        .stat-blue .stat-icon { background: #e0f2fe; color: #0284c7; }
        .stat-blue .stat-val { color: #0369a1; }
        .stat-amber { background: #fffbeb; border-color: #fef3c7; }
        .stat-amber .stat-icon { background: #fef3c7; color: #d97706; }
        .stat-amber .stat-val { color: #b45309; }
        .stat-green { background: #f0fdf4; border-color: #dcfce7; }
        .stat-green .stat-icon { background: #dcfce7; color: #16a34a; }
        .stat-green .stat-val { color: #15803d; }
        .stat-rose { background: #fff1f2; border-color: #ffe4e6; }
        .stat-rose .stat-icon { background: #ffe4e6; color: #e11d48; }
        .stat-rose .stat-val { color: #be123c; }
        .stat-slate { background: #f8fafc; border-color: #e2e8f0; }
        .stat-slate .stat-icon { background: #e2e8f0; color: #475569; }
        .stat-slate .stat-val { color: #334155; }

        .filter-container-box {
            background: #ffffff; border-radius: 12px; border: 1px solid #ede9fe;
            box-shadow: 0 2px 6px rgba(109, 70, 218, 0.04); padding: 18px 20px; margin-bottom: 20px;
        }
        .filter-label {
            font-size: 11px; font-weight: 700; color: #4c1d95; text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 4px;
        }
        .form-control-filter, .form-select-filter {
            border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 12px; transition: all 0.2s ease;
        }
        .form-control-filter:focus, .form-select-filter:focus {
            border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .period-btn-group .btn { font-size: 12px; font-weight: 600; }
        .period-btn-group .btn.active { background: #7c3aed; color: #fff; border-color: #7c3aed; }

        .chart-card {
            background: #ffffff; border-radius: 12px; border: 1px solid #ede9fe;
            box-shadow: 0 2px 6px rgba(109, 70, 218, 0.05); padding: 18px; height: 100%;
        }
        .chart-card .chart-title {
            font-size: 13px; font-weight: 800; color: #4c1d95;
            display: flex; align-items: center; gap: 6px; margin-bottom: 4px;
        }
        .chart-card .chart-sub { font-size: 11px; color: #94a3b8; margin-bottom: 14px; }

        .gauge-wrap { display: flex; flex-direction: column; align-items: center; }
        .gauge-caption { font-size: 12px; font-weight: 700; color: #475569; margin-top: -10px; }
        .gauge-caption small { display: block; font-weight: 500; color: #94a3b8; font-size: 10.5px; }

        #table_assignee_perf thead th {
            background-color: #f8fafc; color: #475569; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;
            border-bottom: 2px solid #e2e8f0 !important; padding: 10px 12px;
        }
        #table_assignee_perf tbody td { font-size: 12px; vertical-align: middle; padding: 9px 12px; }
        .kpi-progress-wrap {
            min-width: 135px;
        }
        .kpi-progress-bar-container {
            height: 8px;
            background-color: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            display: flex;
            position: relative;
        }
        .kpi-progress-bar-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.4s ease;
        }
        .kpi-progress-green {
            background: linear-gradient(90deg, #10b981, #059669);
        }
        .kpi-progress-amber {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }
        .kpi-progress-rose {
            background: linear-gradient(90deg, #f43f5e, #e11d48);
        }

        .rank-badge {
            width: 22px; height: 22px; border-radius: 6px; background: #ede9fe; color: #7c3aed;
            font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;
        }

        #table_avg_time_detail thead th, #table_pic_sla_tickets thead th {
            background-color: #f8fafc; color: #475569; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;
            border-bottom: 2px solid #e2e8f0 !important; padding: 10px 12px;
        }
        #table_avg_time_detail tbody td, #table_pic_sla_tickets tbody td { font-size: 12px; vertical-align: middle; padding: 9px 12px; }
        #table_avg_time_detail tbody tr:hover, #table_pic_sla_tickets tbody tr:hover { background-color: #faf7ff; }

        .bg-lighter { background-color: #f8fafc; }
        .border-end-lg { border-right: 1px solid #e2e8f0; }
        @media (max-width: 991.98px) {
            .border-end-lg { border-right: none; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 15px; }
        }

        #last-updated-label { font-size: 11px; color: #94a3b8; }
    </style>
@endsection

@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y kpi-page">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="color: #4c1d95;">
                    <i class="mdi mdi-speedometer me-2"></i>KPI DASHBOARD — REPORT UBAH / HAPUS
                </h4>
                <p class="text-muted mb-0 small">Ringkasan performa pekerjaan assignee &amp; monitoring tiket secara real-time</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span id="last-updated-label"><i class="mdi mdi-clock-outline"></i> Belum dimuat</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="chk-auto-refresh">
                    <label class="form-check-label small" for="chk-auto-refresh">Auto-refresh 5m</label>
                </div>
                <button type="button" id="btn-refresh-all" class="btn btn-sm btn-primary fw-bold">
                    <i class="mdi mdi-refresh me-1"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-container-box">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-filter-variant text-primary" style="font-size: 18px;"></i>
                    <span class="fw-bold text-dark" style="font-size: 13px;">PERIODE &amp; FILTER</span>
                </div>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-lg-5 col-12">
                    <label class="filter-label"><i class="mdi mdi-calendar-clock"></i> Periode Cepat</label>
                    <div class="btn-group period-btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-period="today">Hari Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-period="week">Minggu Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm active" data-period="month">Bulan Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-period="year">Tahun Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-period="custom">Custom</button>
                    </div>
                </div>
                <div class="col-lg-2 col-6" id="box-start-date" style="display:none;">
                    <label class="filter-label">Dari Tanggal</label>
                    <input type="date" id="filter_start_date" class="form-control form-control-sm form-control-filter">
                </div>
                <div class="col-lg-2 col-6" id="box-end-date" style="display:none;">
                    <label class="filter-label">Sampai Tanggal</label>
                    <input type="date" id="filter_end_date" class="form-control form-control-sm form-control-filter">
                </div>
                <div class="col-lg-3 col-12">
                    <label class="filter-label"><i class="mdi mdi-domain"></i> Holding</label>
                    <select id="filter_holding" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Holding --</option>
                        @foreach($holdings as $h)
                            <option value="{{ $h->holding_code }}" {{ $h->holding_code === $holding->holding_code ? 'selected' : '' }}>
                                {{ $h->holding_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4 col-sm-6">
                    <label class="filter-label"><i class="mdi mdi-office-building"></i> Departemen</label>
                    <select id="filter_departemen" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Departemen --</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="filter-label"><i class="mdi mdi-server-network"></i> Sistem</label>
                    <select id="filter_kategori" class="form-select form-select-sm form-select-filter">
                        <option value="">-- Semua Sistem --</option>
                        <option value="1">Epicor ERP</option>
                        <option value="2">Accurate Online SP</option>
                        <option value="3">Accurate Online AJA</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6 d-flex align-items-end">
                    <button type="button" id="btn-apply-filter" class="btn btn-primary btn-sm px-4 fw-bold w-100" style="height: 34px;">
                        <i class="mdi mdi-magnify me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="stat-grid">
            <div class="stat-card-item stat-purple">
                <div class="stat-icon"><i class="mdi mdi-ticket-outline"></i></div>
                <div><div class="stat-val" id="kpi-total">0</div><div class="stat-label">Total Tiket</div></div>
            </div>
            <div class="stat-card-item stat-amber">
                <div class="stat-icon"><i class="mdi mdi-clock-outline"></i></div>
                <div><div class="stat-val" id="kpi-submitted">0</div><div class="stat-label">Diajukan / Validasi</div></div>
            </div>
            <div class="stat-card-item stat-blue">
                <div class="stat-icon"><i class="mdi mdi-progress-wrench"></i></div>
                <div><div class="stat-val" id="kpi-progress">0</div><div class="stat-label">Sedang Dikerjakan</div></div>
            </div>
            <div class="stat-card-item stat-green">
                <div class="stat-icon"><i class="mdi mdi-check-circle-outline"></i></div>
                <div><div class="stat-val" id="kpi-completed">0</div><div class="stat-label">Selesai / Ditutup</div></div>
            </div>
            <div class="stat-card-item stat-rose">
                <div class="stat-icon"><i class="mdi mdi-close-circle-outline"></i></div>
                <div><div class="stat-val" id="kpi-cancelled">0</div><div class="stat-label">Ditolak / Batal</div></div>
            </div>
            <div class="stat-card-item stat-slate">
                <div class="stat-icon"><i class="mdi mdi-inbox-arrow-down-outline"></i></div>
                <div><div class="stat-val" id="kpi-backlog">0</div><div class="stat-label">Backlog Aktif</div></div>
            </div>
        </div>

        <!-- Gauges + Status distribution -->
        <div class="row g-3 mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="chart-card d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="chart-title mb-0"><i class="mdi mdi-speedometer"></i> Rata-rata Penyelesaian</div>
                        </div>
                        <div class="chart-sub">Dibanding target SLA rata-rata</div>
                        <div class="gauge-wrap">
                            <div id="gauge-avg-time"></div>
                            <div class="gauge-caption" id="gauge-avg-time-caption">-</div>
                        </div>
                    </div>
                    <div class="text-center mt-2 pt-2 border-top">
                        <button type="button" id="btn-show-avg-detail" class="btn btn-xs btn-outline-primary fw-bold w-100 rounded-pill py-1">
                            <i class="mdi mdi-information-outline me-1"></i> Lihat Rincian Perhitungan
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="chart-card">
                    <div class="chart-title"><i class="mdi mdi-check-decagram"></i> SLA On-Time</div>
                    <div class="chart-sub">% tiket selesai sesuai target</div>
                    <div class="gauge-wrap">
                        <div id="gauge-sla"></div>
                        <div class="gauge-caption" id="gauge-sla-caption">-</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="chart-card">
                    <div class="chart-title"><i class="mdi mdi-chart-pie"></i> Distribusi Status Tiket</div>
                    <div class="chart-sub">Sebaran status pada periode terpilih</div>
                    <div id="chart-status-pie"></div>
                </div>
            </div>
        </div>

        <!-- Trend Mixed Chart -->
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="chart-card">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <div class="chart-title"><i class="mdi mdi-chart-line"></i> Tren Pengajuan vs Penyelesaian Tiket</div>
                            <div class="chart-sub">Batang = jumlah diajukan, Garis = jumlah diselesaikan</div>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" id="trend-groupby-toggle">
                            <button type="button" class="btn btn-outline-secondary active" data-groupby="day">Harian</button>
                            <button type="button" class="btn btn-outline-secondary" data-groupby="month">Bulanan</button>
                        </div>
                    </div>
                    <div id="chart-trend-mixed" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Divisi & Top 10 Modul -->
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-title"><i class="mdi mdi-account-group"></i> Top 10 Divisi Pengaju</div>
                    <div class="chart-sub">Akumulasi jumlah tiket per divisi</div>
                    <div id="chart-top-divisi"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-title"><i class="mdi mdi-view-module"></i> Top 10 Modul</div>
                    <div class="chart-sub">Modul/sub-modul paling sering diajukan</div>
                    <div id="chart-top-module"></div>
                </div>
            </div>
        </div>

        <!-- Assignee Performance Table -->
        <div class="card master-table-card border shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-account-tie text-primary" style="font-size: 20px;"></i>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 14px;">PERFORMA ASSIGNEE (PIC)</h5>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="table_assignee_perf" class="table table-hover align-middle w-100 table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama PIC</th>
                                <th class="text-center">Ditugaskan</th>
                                <th class="text-center">Selesai</th>
                                <th>Rating Penyelesaian</th>
                                <th class="text-center">Rata-rata Waktu</th>
                                <th>SLA On-Time</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Detail Perhitungan Rata-rata Penyelesaian -->
        <div class="modal fade" id="modalAvgTimeDetail" tabindex="-1" aria-labelledby="modalAvgTimeDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white py-3">
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalAvgTimeDetailLabel">
                                <i class="mdi mdi-calculator-variant-outline me-2"></i>Rincian Perhitungan Rata-rata Waktu Penyelesaian
                            </h5>
                            <small class="text-white-50" id="modal-avg-period-info">Periode: -</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <!-- Formula & Explanation Box -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-body p-3">
                                <div class="row g-3 align-items-center">
                                    <div class="col-lg-6 col-12 border-end-lg">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-label-primary p-2 rounded">
                                                <i class="mdi mdi-function text-primary" style="font-size: 18px;"></i>
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0">Rumus &amp; Cara Perhitungan</h6>
                                        </div>
                                        <div class="p-3 bg-lighter rounded-3 border">
                                            <div class="fw-bold text-secondary small text-uppercase mb-1">Rumus Matematis:</div>
                                            <div class="text-primary fw-bold font-monospace mb-2" style="font-size: 13px;">
                                                Rata-rata Waktu = Total Durasi Pengerjaan Tiket ÷ Jumlah Tiket Selesai
                                            </div>
                                            <div class="p-2 rounded bg-white border small font-monospace text-dark" id="detail-formula-calc">
                                                0 Menit ÷ 0 Tiket = 0 Menit/Tiket
                                            </div>
                                        </div>
                                        <p class="text-muted small mt-2 mb-0" id="detail-formula-explanation">
                                            Penjelasan: Semua waktu pengerjaan tiket berstatus selesai dijumlahkan, kemudian dibagi dengan total jumlah tiket yang selesai dalam rentang filter terpilih.
                                        </p>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="p-3 bg-white rounded-3 border text-center">
                                                    <div class="text-muted small fw-bold text-uppercase">Total Durasi Aktual</div>
                                                    <div class="h5 fw-bold text-primary mb-0 mt-1" id="box-total-actual">0 Menit</div>
                                                    <small class="text-muted">Akumulasi menit</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 bg-white rounded-3 border text-center">
                                                    <div class="text-muted small fw-bold text-uppercase">Tiket Selesai (Pembagi)</div>
                                                    <div class="h5 fw-bold text-dark mb-0 mt-1" id="box-total-tickets">0 Tiket</div>
                                                    <small class="text-muted">Jumlah data</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 bg-white rounded-3 border text-center">
                                                    <div class="text-muted small fw-bold text-uppercase">Rata-rata Aktual</div>
                                                    <div class="h5 fw-bold text-success mb-0 mt-1" id="box-avg-actual">0 Menit</div>
                                                    <small class="text-muted">Per tiket</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 bg-white rounded-3 border text-center">
                                                    <div class="text-muted small fw-bold text-uppercase">Rata-rata Target SLA</div>
                                                    <div class="h5 fw-bold text-info mb-0 mt-1" id="box-avg-expected">0 Menit</div>
                                                    <small class="text-muted">Ekspektasi SLA</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table of Individual Tickets -->
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-format-list-bulleted text-primary" style="font-size: 20px;"></i>
                                    <h6 class="mb-0 fw-bold text-dark">Daftar Tiket yang Digunakan dalam Perhitungan</h6>
                                </div>
                                <div>
                                    <span class="badge bg-label-info" id="badge-table-count">0 Tiket</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive">
                                    <table id="table_avg_time_detail" class="table table-hover align-middle w-100 table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">#</th>
                                                <th>Nomor Tiket</th>
                                                <th>Modul / Fitur</th>
                                                <th>PIC (Assignee)</th>
                                                <th class="text-center">Tgl Selesai</th>
                                                <th class="text-center">Target SLA</th>
                                                <th class="text-center">Durasi Aktual</th>
                                                <th class="text-center">Selisih</th>
                                                <th class="text-center">Status SLA</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <th colspan="5" class="text-end">TOTAL &amp; RATA-RATA:</th>
                                                <th class="text-center" id="foot-total-expected">-</th>
                                                <th class="text-center" id="foot-total-actual">-</th>
                                                <th colspan="2" class="text-center" id="foot-avg-calc">-</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white py-2">
                        <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail SLA PIC -->
        <div class="modal fade" id="modalPicSlaDetail" tabindex="-1" aria-labelledby="modalPicSlaDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white py-3">
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalPicSlaDetailLabel">
                                <i class="mdi mdi-account-star-outline me-2 text-warning"></i>Detail Performa &amp; SLA PIC: <span id="modal-pic-name" class="text-warning"></span>
                            </h5>
                            <small class="text-white-50" id="modal-pic-period-info">Periode: -</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <!-- Stat Summary Grid for this PIC -->
                        <div class="row g-3 mb-4">
                            <div class="col-lg-3 col-sm-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="stat-icon rounded p-2 bg-label-primary">
                                            <i class="mdi mdi-ticket-outline text-primary" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 10.5px;">Tiket PIC</small>
                                            <h5 class="fw-bold mb-0 text-dark"><span id="pic-stat-completed">0</span> / <span id="pic-stat-assigned">0</span></h5>
                                            <small class="text-muted">Selesai / Ditugaskan</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="stat-icon rounded p-2 bg-label-success">
                                            <i class="mdi mdi-check-circle-outline text-success" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 10.5px;">Completion Rate</small>
                                            <h5 class="fw-bold mb-0 text-success" id="pic-stat-completion">0%</h5>
                                            <small class="text-muted">Tingkat penyelesaian</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="stat-icon rounded p-2 bg-label-info">
                                            <i class="mdi mdi-clock-outline text-info" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 10.5px;">Rata-rata Waktu</small>
                                            <h5 class="fw-bold mb-0 text-info" id="pic-stat-avg-time">0 Menit</h5>
                                            <small class="text-muted">Per tiket selesai</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="card border-0 shadow-sm rounded-3 h-100">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="stat-icon rounded p-2 bg-label-warning">
                                            <i class="mdi mdi-check-decagram text-warning" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted text-uppercase fw-bold" style="font-size: 10.5px;">SLA On-Time</small>
                                            <h5 class="fw-bold mb-0 text-warning" id="pic-stat-sla">0%</h5>
                                            <small class="text-muted"><span id="pic-stat-ontime-detail">0 dari 0</span> tepat waktu</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table of Tickets for PIC -->
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-format-list-checks text-primary" style="font-size: 20px;"></i>
                                    <h6 class="mb-0 fw-bold text-dark">Daftar Tiket Ditugaskan ke PIC</h6>
                                </div>
                                <div>
                                    <span class="badge bg-label-primary" id="badge-pic-tickets-count">0 Tiket</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="table-responsive">
                                    <table id="table_pic_sla_tickets" class="table table-hover align-middle w-100 table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 40px;">#</th>
                                                <th>Nomor Tiket</th>
                                                <th>Modul / Fitur</th>
                                                <th class="text-center">Status Tiket</th>
                                                <th class="text-center">Tgl Pengajuan</th>
                                                <th class="text-center">Tgl Selesai</th>
                                                <th class="text-center">Target SLA</th>
                                                <th class="text-center">Durasi Aktual</th>
                                                <th class="text-center">Selisih</th>
                                                <th class="text-center">Status SLA</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white py-2">
                        <button type="button" class="btn btn-secondary btn-sm fw-bold" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#filter_departemen').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Semua Departemen --',
                allowClear: true,
                width: '100%'
            });

            loadDepartemenList();

            let currentPeriod = 'month';
            let currentGroupBy = 'day';
            let autoRefreshTimer = null;

            let gaugeAvgTime, gaugeSla, chartStatusPie, chartTrend, chartTopDivisi, chartTopModule;
            let tableAssignee = null;

            function showKpiError(message) {
                $('#last-updated-label').html('<i class="mdi mdi-alert-circle-outline"></i> Gagal memuat data');
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data KPI gagal dimuat',
                        text: message || 'Periksa filter periode dan koneksi database.',
                        confirmButtonText: 'Tutup'
                    });
                }
            }

            $(document).ajaxError(function (event, xhr, settings) {
                if (settings.url && settings.url.indexOf('/ticket/report/kpi/') !== -1) {
                    showKpiError('Server mengembalikan HTTP ' + xhr.status + '.');
                }
            });

            // ---------- Period Buttons ----------
            $('.period-btn-group .btn').on('click', function () {
                $('.period-btn-group .btn').removeClass('active');
                $(this).addClass('active');
                currentPeriod = $(this).data('period');
                if (currentPeriod === 'custom') {
                    $('#box-start-date, #box-end-date').show();
                } else {
                    $('#box-start-date, #box-end-date').hide();
                }
            });

            $('#trend-groupby-toggle .btn').on('click', function () {
                $('#trend-groupby-toggle .btn').removeClass('active');
                $(this).addClass('active');
                currentGroupBy = $(this).data('groupby');
                loadTrend();
            });

            $('#btn-apply-filter, #btn-refresh-all').on('click', function () {
                loadAll();
            });

            $('#chk-auto-refresh').on('change', function () {
                if ($(this).is(':checked')) {
                    autoRefreshTimer = setInterval(loadAll, 5 * 60 * 1000);
                } else {
                    clearInterval(autoRefreshTimer);
                }
            });

            function commonParams(extra) {
                return Object.assign({
                    period:       currentPeriod,
                    start_date:   $('#filter_start_date').val(),
                    end_date:     $('#filter_end_date').val(),
                    holding_code: $('#filter_holding').val(),
                    departemen:   $('#filter_departemen').val(),
                    kategori:     $('#filter_kategori').val(),
                }, extra || {});
            }

            function loadDepartemenList() {
                $.ajax({
                    url: "{{ route('ticket.report.departemen.list') }}",
                    type: 'GET',
                    success: function (res) {
                        if (res && res.code === 200) {
                            const sel = $('#filter_departemen');
                            sel.find('option:not(:first)').remove();
                            (res.data || []).forEach(dep => {
                                sel.append(`<option value="${dep.nama_departemen}">${dep.nama_departemen} (${dep.holding_name})</option>`);
                            });
                        }
                    }
                });
            }

            // ---------- Summary + Gauges ----------
            function loadSummary() {
                $.ajax({
                    url: "{{ route('ticket.report.kpi.summary') }}",
                    type: 'GET',
                    data: commonParams(),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const d = res.data;
                        $('#kpi-total').text(d.total);
                        $('#kpi-submitted').text(d.submitted);
                        $('#kpi-progress').text(d.in_progress);
                        $('#kpi-completed').text(d.completed);
                        $('#kpi-cancelled').text(d.cancelled);

                        $('#kpi-backlog').text(d.backlog);

                        const avgPct = d.avg_time_target_pct === null ? 0 : Math.min(100, d.avg_time_target_pct);
                        renderGaugeAvgTime(avgPct, d.avg_hours, d.avg_expected_hours);

                        const slaPct = d.sla_ontime_pct === null ? 0 : d.sla_ontime_pct;
                        renderGaugeSla(slaPct, d.sla_ontime_pct);

                        updateLastRefreshed();
                    }
                });
            }

            function renderGaugeAvgTime(percent, avgHours, expectedHours) {
                const options = {
                    chart: { type: 'radialBar', height: 190, sparkline: { enabled: true } },
                    series: [percent],
                    colors: ['#7c3aed'],
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '60%' },
                            dataLabels: {
                                name: { show: false },
                                value: {
                                    fontSize: '20px',
                                    fontWeight: 800,
                                    formatter: function () { return (avgHours ?? 0) + ' Menit'; }
                                }
                            }
                        }
                    }
                };
                if (gaugeAvgTime) { gaugeAvgTime.updateOptions(options); }
                else { gaugeAvgTime = new ApexCharts(document.querySelector('#gauge-avg-time'), options); gaugeAvgTime.render(); }
                $('#gauge-avg-time-caption').html('Rata-rata waktu penyelesaian<br><small>'
                    + (expectedHours === null ? 'Belum ada target SLA' : 'Terhadap target rata-rata: ' + expectedHours + ' Menit')
                    + '</small>');
            }

            function renderGaugeSla(percent, rawPct) {
                const color = percent >= 80 ? '#16a34a' : (percent >= 50 ? '#d97706' : '#e11d48');
                const options = {
                    chart: { type: 'radialBar', height: 190, sparkline: { enabled: true } },
                    series: [percent],
                    colors: [color],
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '60%' },
                            dataLabels: {
                                name: { show: false },
                                value: { fontSize: '20px', fontWeight: 800, formatter: function () { return percent + '%'; } }
                            }
                        }
                    }
                };
                if (gaugeSla) { gaugeSla.updateOptions(options); }
                else { gaugeSla = new ApexCharts(document.querySelector('#gauge-sla'), options); gaugeSla.render(); }
                $('#gauge-sla-caption').html(rawPct === null
                    ? 'Belum ada data SLA'
                    : 'Tiket selesai tepat waktu<br><small>berdasarkan target tingkat kesulitan</small>');
            }

            // ---------- Status Pie Chart ----------
            function loadStatusPie() {
                $.ajax({
                    url: "{{ route('ticket.report.kpi.status-distribution') }}",
                    type: 'GET',
                    data: commonParams(),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const options = {
                            chart: { type: 'pie', height: 230 },
                            series: res.data.values,
                            labels: res.data.labels,
                            legend: { position: 'bottom', fontSize: '11px' },
                            colors: ['#7c3aed', '#0284c7', '#16a34a', '#d97706', '#e11d48', '#64748b', '#4f46e5', '#0d9488', '#be123c', '#4b5563'],
                            dataLabels: {
                                enabled: true,
                                style: { fontSize: '11px', fontFamily: 'Plus Jakarta Sans, sans-serif' },
                                dropShadow: { enabled: false }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return val + ' Tiket';
                                    }
                                }
                            }
                        };
                        if (chartStatusPie) { chartStatusPie.updateOptions(options); }
                        else { chartStatusPie = new ApexCharts(document.querySelector('#chart-status-pie'), options); chartStatusPie.render(); }
                    }
                });
            }

            // ---------- Trend Mixed Chart ----------
            function loadTrend() {
                $.ajax({
                    url: "{{ route('ticket.report.kpi.trend') }}",
                    type: 'GET',
                    data: commonParams({ group_by: currentGroupBy }),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const options = {
                            chart: { height: 340, type: 'line', toolbar: { show: false } },
                            series: [
                                { name: 'Diajukan', type: 'column', data: res.data.submitted },
                                { name: 'Diselesaikan', type: 'line', data: res.data.completed }
                            ],
                            colors: ['#c4b5fd', '#7c3aed'],
                            stroke: { width: [0, 3], curve: 'smooth' },
                            xaxis: { categories: res.data.labels, labels: { style: { fontSize: '10px' } } },
                            yaxis: { title: { text: 'Jumlah Tiket' } },
                            legend: { position: 'top', fontSize: '12px' },
                            dataLabels: { enabled: false },
                            plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } }
                        };
                        if (chartTrend) { chartTrend.updateOptions(options); }
                        else { chartTrend = new ApexCharts(document.querySelector('#chart-trend-mixed'), options); chartTrend.render(); }
                    }
                });
            }

            // ---------- Top 10 Divisi ----------
            function loadTopDivisi() {
                $.ajax({
                    url: "{{ route('ticket.report.kpi.top-divisi') }}",
                    type: 'GET',
                    data: commonParams({ limit: 10 }),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const options = {
                            chart: { type: 'bar', height: 320, toolbar: { show: false } },
                            series: [{ name: 'Jumlah Tiket', data: res.data.values }],
                            colors: ['#7c3aed'],
                            plotOptions: { bar: { horizontal: true, borderRadius: 4, distributed: false } },
                            dataLabels: { enabled: true, style: { fontSize: '10px' } },
                            xaxis: { categories: res.data.labels, labels: { style: { fontSize: '10px' } } }
                        };
                        if (chartTopDivisi) { chartTopDivisi.updateOptions(options); }
                        else { chartTopDivisi = new ApexCharts(document.querySelector('#chart-top-divisi'), options); chartTopDivisi.render(); }
                    }
                });
            }

            // ---------- Top 10 Modul ----------
            function loadTopModule() {
                $.ajax({
                    url: "{{ route('ticket.report.kpi.top-module') }}",
                    type: 'GET',
                    data: commonParams({ limit: 10 }),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const options = {
                            chart: { type: 'bar', height: 320, toolbar: { show: false } },
                            series: [{ name: 'Jumlah Tiket', data: res.data.values }],
                            colors: ['#0284c7'],
                            plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                            dataLabels: { enabled: true, style: { fontSize: '10px' } },
                            xaxis: { categories: res.data.labels, labels: { style: { fontSize: '10px' } } }
                        };
                        if (chartTopModule) { chartTopModule.updateOptions(options); }
                        else { chartTopModule = new ApexCharts(document.querySelector('#chart-top-module'), options); chartTopModule.render(); }
                    }
                });
            }

            // ---------- Assignee Performance Table ----------
            function initAssigneeTable() {
                tableAssignee = $('#table_assignee_perf').DataTable({
                    data: [],
                    columns: [
                        { data: null, orderable: false, className: 'text-center', render: (d, t, r, m) => `<span class="rank-badge">${m.row + 1}</span>` },
                        { data: 'pic_name' },
                        { data: 'assigned', className: 'text-center' },
                        { data: 'completed', className: 'text-center' },
                        {
                            data: 'completion_rate',
                            render: function (d, t, r) {
                                const pct = Math.max(0, Math.min(100, parseFloat(d) || 0));
                                let colorClass = 'kpi-progress-green';
                                let badgeClass = 'bg-label-success';
                                if (pct < 50) {
                                    colorClass = 'kpi-progress-rose';
                                    badgeClass = 'bg-label-danger';
                                } else if (pct < 80) {
                                    colorClass = 'kpi-progress-amber';
                                    badgeClass = 'bg-label-warning';
                                }
                                return `<div class="kpi-progress-wrap">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <strong>${r.completed}</strong>/${r.assigned}
                                                </small>
                                                <span class="badge ${badgeClass} py-0 px-1 fw-bold" style="font-size: 11px;">${pct}%</span>
                                            </div>
                                            <div class="kpi-progress-bar-container">
                                                <div class="kpi-progress-bar-fill ${colorClass}" style="width: ${pct}%;"></div>
                                            </div>
                                        </div>`;
                            }
                        },
                        {
                            data: 'avg_hours',
                            className: 'text-center',
                            render: d => d === null ? '<span class="text-muted">-</span>' : `<span class="fw-bold">${d}</span> <small class="text-muted">Menit</small>`
                        },
                        {
                            data: 'on_time_pct',
                            render: function (d, t, r) {
                                if (d === null || r.sla_eligible === 0) {
                                    return '<span class="badge bg-label-secondary small py-1 px-2"><i class="mdi mdi-minus me-1"></i>Belum ada data</span>';
                                }
                                const pct = Math.max(0, Math.min(100, parseFloat(d) || 0));
                                let colorClass = 'kpi-progress-green';
                                let badgeClass = 'bg-label-success';
                                if (pct < 50) {
                                    colorClass = 'kpi-progress-rose';
                                    badgeClass = 'bg-label-danger';
                                } else if (pct < 80) {
                                    colorClass = 'kpi-progress-amber';
                                    badgeClass = 'bg-label-warning';
                                }
                                return `<div class="kpi-progress-wrap">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <strong>${r.on_time}</strong>/${r.sla_eligible}
                                                </small>
                                                <span class="badge ${badgeClass} py-0 px-1 fw-bold" style="font-size: 11px;">${pct}%</span>
                                            </div>
                                            <div class="kpi-progress-bar-container">
                                                <div class="kpi-progress-bar-fill ${colorClass}" style="width: ${pct}%;"></div>
                                            </div>
                                        </div>`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: function (d, t, r) {
                                return `<button type="button" class="btn btn-xs btn-outline-primary fw-bold rounded-pill btn-view-pic-sla py-1 px-2" data-pic="${r.pic_name}">
                                            <i class="mdi mdi-eye-outline me-1"></i>Detail SLA
                                        </button>`;
                            }
                        }
                    ],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50],
                    order: [],
                    language: {
                        emptyTable: 'Belum ada data assignee pada periode ini',
                        zeroRecords: 'Data tidak ditemukan',
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
                        infoEmpty: 'Menampilkan 0 data',
                    },
                });
            }

            function loadAssigneePerformance() {
                $.ajax({
                    url: "{{ route('ticket.report.kpi.assignee') }}",
                    type: 'GET',
                    data: commonParams(),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        if (!tableAssignee) initAssigneeTable();
                        tableAssignee.clear().rows.add(res.data).draw();
                    }
                });
            }

            // ---------- Rincian Perhitungan Rata-rata Penyelesaian Modal ----------
            let tableAvgDetail = null;

            function initAvgDetailTable() {
                tableAvgDetail = $('#table_avg_time_detail').DataTable({
                    data: [],
                    columns: [
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: (d, t, r, m) => `<span class="rank-badge">${m.row + 1}</span>`
                        },
                        {
                            data: 'nomor_ticket',
                            render: (d, t, r) => `<span class="fw-bold text-primary font-monospace">${d}</span>`
                        },
                        {
                            data: null,
                            render: (d, t, r) => {
                                const sub = r.submenu_name || '-';
                                const menu = r.menu_name || '-';
                                return `<div><strong>${sub}</strong><br><small class="text-muted">${menu}</small></div>`;
                            }
                        },
                        { data: 'pic_name' },
                        { data: 'finish_at', className: 'text-center small' },
                        {
                            data: 'expected_minutes',
                            className: 'text-center',
                            render: d => d !== null ? `<span class="badge bg-label-info">${d} Menit</span>` : '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'actual_minutes',
                            className: 'text-center',
                            render: d => `<span class="badge bg-label-primary fw-bold">${d} Menit</span>`
                        },
                        {
                            data: 'variance_minutes',
                            className: 'text-center',
                            render: (d, t, r) => {
                                if (d === null) return '<span class="text-muted">-</span>';
                                if (d <= 0) {
                                    return `<span class="text-success small fw-bold"><i class="mdi mdi-arrow-down-bold"></i> ${Math.abs(d)} Menit lebih cepat</span>`;
                                }
                                return `<span class="text-danger small fw-bold"><i class="mdi mdi-arrow-up-bold"></i> +${d} Menit lebih lambat</span>`;
                            }
                        },
                        {
                            data: 'sla_status',
                            className: 'text-center',
                            render: (d, t, r) => {
                                if (d === 'On Time') {
                                    return '<span class="badge bg-success"><i class="mdi mdi-check-circle me-1"></i>Tepat Waktu</span>';
                                } else if (d === 'Overrun') {
                                    return '<span class="badge bg-danger"><i class="mdi mdi-alert-circle me-1"></i>Overrun</span>';
                                }
                                return '<span class="badge bg-secondary">Tanpa SLA</span>';
                            }
                        }
                    ],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[4, 'desc']],
                    language: {
                        emptyTable: 'Tidak ada data tiket selesai pada periode ini',
                        zeroRecords: 'Data tidak ditemukan',
                        search: 'Cari Tiket:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ tiket',
                        infoEmpty: 'Menampilkan 0 data',
                    }
                });
            }

            function loadAvgTimeDetail() {
                $('#modalAvgTimeDetail').modal('show');
                
                $('#box-total-actual').text('Memuat...');
                $('#box-total-tickets').text('Memuat...');
                $('#box-avg-actual').text('Memuat...');
                $('#box-avg-expected').text('Memuat...');
                $('#detail-formula-calc').text('Menghitung data...');

                $.ajax({
                    url: "{{ route('ticket.report.kpi.avg-time-detail') }}",
                    type: 'GET',
                    data: commonParams(),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const data = res.data;
                        const summary = data.summary;
                        const tickets = data.tickets;

                        $('#modal-avg-period-info').html(`<i class="mdi mdi-calendar-range me-1"></i> Periode: <strong>${summary.period.start} - ${summary.period.end}</strong>`);
                        $('#badge-table-count').text(`${summary.total_tickets} Tiket Selesai`);

                        $('#box-total-actual').text(`${summary.total_actual_minutes} Menit`);
                        $('#box-total-tickets').text(`${summary.total_tickets} Tiket`);
                        $('#box-avg-actual').text(`${summary.avg_actual_minutes} Menit`);
                        $('#box-avg-expected').text(summary.avg_expected_minutes !== null ? `${summary.avg_expected_minutes} Menit` : '-');

                        // Calculation formula display
                        const formulaText = `Total Waktu (${summary.total_actual_minutes} Menit) ÷ ${summary.total_tickets} Tiket Selesai = ${summary.avg_actual_minutes} Menit / Tiket`;
                        $('#detail-formula-calc').html(`<i class="mdi mdi-calculator me-1 text-primary"></i><strong>${formulaText}</strong>`);

                        $('#detail-formula-explanation').html(
                            `Dari total <strong>${summary.total_tickets} tiket selesai</strong> dalam filter yang dipilih, total waktu pengerjaan keseluruhan adalah <strong>${summary.total_actual_minutes} Menit</strong>. Sehingga menghasilkan rata-rata waktu penyelesaian <strong>${summary.avg_actual_minutes} Menit per tiket</strong>.`
                        );

                        $('#foot-total-expected').html(summary.avg_expected_minutes !== null ? `Total: ${summary.total_expected_minutes} m<br><small class="text-muted">Rata-rata: ${summary.avg_expected_minutes} m</small>` : '-');
                        $('#foot-total-actual').html(`Total: ${summary.total_actual_minutes} m<br><small class="text-primary">Rata-rata: ${summary.avg_actual_minutes} m</small>`);
                        $('#foot-avg-calc').html(`Rata-rata: <strong>${summary.avg_actual_minutes} Menit</strong> / Tiket`);

                        if (!tableAvgDetail) {
                            initAvgDetailTable();
                        }
                        tableAvgDetail.clear().rows.add(tickets).draw();
                        setTimeout(() => {
                            tableAvgDetail.columns.adjust().draw();
                        }, 200);
                    },
                    error: function () {
                        showKpiError('Gagal mengambil rincian perhitungan rata-rata.');
                    }
                });
            }

            $('#btn-show-avg-detail').on('click', function () {
                loadAvgTimeDetail();
            });

            $('#modalAvgTimeDetail').on('shown.bs.modal', function () {
                if (tableAvgDetail) {
                    tableAvgDetail.columns.adjust().draw();
                }
            });

            // ---------- Detail SLA PIC Modal ----------
            let tablePicSla = null;

            function initPicSlaTable() {
                tablePicSla = $('#table_pic_sla_tickets').DataTable({
                    data: [],
                    columns: [
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: (d, t, r, m) => `<span class="rank-badge">${m.row + 1}</span>`
                        },
                        {
                            data: 'nomor_ticket',
                            render: d => `<span class="fw-bold text-primary font-monospace">${d}</span>`
                        },
                        {
                            data: null,
                            render: (d, t, r) => {
                                const sub = r.submenu_name || '-';
                                const menu = r.menu_name || '-';
                                return `<div><strong>${sub}</strong><br><small class="text-muted">${menu}</small></div>`;
                            }
                        },
                        {
                            data: 'status_label',
                            className: 'text-center',
                            render: (d, t, r) => {
                                let badgeColor = 'bg-label-secondary';
                                if (r.ticket_status === 'T06') badgeColor = 'bg-label-success';
                                else if (r.ticket_status === 'T04') badgeColor = 'bg-label-primary';
                                else if (r.ticket_status === 'T03') badgeColor = 'bg-label-info';
                                else if (r.ticket_status === 'T07') badgeColor = 'bg-label-warning';
                                else if (r.ticket_status === 'T08') badgeColor = 'bg-label-danger';
                                return `<span class="badge ${badgeColor}">${d}</span>`;
                            }
                        },
                        { data: 'created_at', className: 'text-center small' },
                        { data: 'finish_at', className: 'text-center small' },
                        {
                            data: 'expected_minutes',
                            className: 'text-center',
                            render: d => d !== null ? `<span class="badge bg-label-info">${d} Menit</span>` : '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'actual_minutes',
                            className: 'text-center',
                            render: d => d !== null ? `<span class="badge bg-label-primary fw-bold">${d} Menit</span>` : '<span class="text-muted">-</span>'
                        },
                        {
                            data: 'variance_minutes',
                            className: 'text-center',
                            render: (d, t, r) => {
                                if (d === null) return '<span class="text-muted">-</span>';
                                if (d <= 0) {
                                    return `<span class="text-success small fw-bold"><i class="mdi mdi-arrow-down-bold"></i> ${Math.abs(d)} Menit lebih cepat</span>`;
                                }
                                return `<span class="text-danger small fw-bold"><i class="mdi mdi-arrow-up-bold"></i> +${d} Menit lebih lambat</span>`;
                            }
                        },
                        {
                            data: 'sla_status',
                            className: 'text-center',
                            render: (d, t, r) => {
                                if (d === 'On Time') {
                                    return '<span class="badge bg-success"><i class="mdi mdi-check-circle me-1"></i>Tepat Waktu</span>';
                                } else if (d === 'Overrun') {
                                    return '<span class="badge bg-danger"><i class="mdi mdi-alert-circle me-1"></i>Overrun</span>';
                                } else if (d === 'Dalam Proses') {
                                    return '<span class="badge bg-label-warning">Dalam Proses</span>';
                                }
                                return '<span class="badge bg-secondary">Tanpa Target</span>';
                            }
                        }
                    ],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50],
                    order: [[4, 'desc']],
                    language: {
                        emptyTable: 'Tidak ada data tiket untuk PIC ini pada periode terpilih',
                        zeroRecords: 'Data tidak ditemukan',
                        search: 'Cari Tiket:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ tiket',
                        infoEmpty: 'Menampilkan 0 data',
                    }
                });
            }

            function loadPicSlaDetail(picName) {
                $('#modal-pic-name').text(picName);
                $('#modalPicSlaDetail').modal('show');

                $('#pic-stat-assigned').text('...');
                $('#pic-stat-completed').text('...');
                $('#pic-stat-completion').text('...');
                $('#pic-stat-avg-time').text('...');
                $('#pic-stat-sla').text('...');
                $('#pic-stat-ontime-detail').text('...');

                $.ajax({
                    url: "{{ route('ticket.report.kpi.pic-sla-detail') }}",
                    type: 'GET',
                    data: commonParams({ pic_name: picName }),
                    success: function (res) {
                        if (res.code !== 200) {
                            showKpiError(res.message);
                            return;
                        }
                        const info = res.data.pic_info;
                        const tickets = res.data.tickets;

                        $('#modal-pic-period-info').html(`<i class="mdi mdi-calendar-range me-1"></i> Periode: <strong>${info.period.start} - ${info.period.end}</strong>`);
                        $('#badge-pic-tickets-count').text(`${info.total_assigned} Tiket Total`);

                        $('#pic-stat-assigned').text(info.total_assigned);
                        $('#pic-stat-completed').text(info.total_completed);
                        $('#pic-stat-completion').text(info.completion_rate + '%');
                        $('#pic-stat-avg-time').text(info.avg_actual_minutes !== null ? info.avg_actual_minutes + ' Menit' : '-');
                        $('#pic-stat-sla').text(info.sla_ontime_pct !== null ? info.sla_ontime_pct + '%' : '-');
                        $('#pic-stat-ontime-detail').text(`${info.on_time_count} dari ${info.sla_eligible_count}`);

                        if (!tablePicSla) {
                            initPicSlaTable();
                        }
                        tablePicSla.clear().rows.add(tickets).draw();
                        setTimeout(() => {
                            tablePicSla.columns.adjust().draw();
                        }, 200);
                    },
                    error: function () {
                        showKpiError('Gagal memuat detail SLA PIC.');
                    }
                });
            }

            $(document).on('click', '.btn-view-pic-sla', function () {
                const picName = $(this).data('pic');
                loadPicSlaDetail(picName);
            });

            $('#modalPicSlaDetail').on('shown.bs.modal', function () {
                if (tablePicSla) {
                    tablePicSla.columns.adjust().draw();
                }
            });

            function updateLastRefreshed() {
                const now = new Date();
                $('#last-updated-label').html(
                    '<i class="mdi mdi-clock-outline"></i> Diperbarui: ' + now.toLocaleTimeString()
                );
            }

            function loadAll() {
                loadSummary();
                loadStatusPie();
                loadTrend();
                loadTopDivisi();
                loadTopModule();
                loadAssigneePerformance();
            }

            // initial load
            loadAll();
        });
    </script>
@endsection