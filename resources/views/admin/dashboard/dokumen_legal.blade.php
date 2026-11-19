@extends('admin.layouts.dashboard')

@section('css')
    <style>
        .stat-card {
            border-left: 4px solid transparent;
        }

        .stat-title {
            font-size: .9rem;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .btn-aksi {
            width: 38px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* supaya chart tidak gepeng */
        .chart-wrap {
            min-height: 280px;
        }

        /* LEGEND HEATMAP */
        .legend-box {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: inline-block;
        }

        .bg-orange {
            background-color: #fd7e14;
        }

        /*HEATMAP TANGGAL*/
        .heatmap {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
        }

        .heatmap-cell {
            height: 44px;
            border-radius: 8px;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            transition: transform .1s ease, box-shadow .1s ease;
        }

        .heatmap-cell:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .15);
        }

        /*Heatmap Colors*/

        .hm-none {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #adb5bd;
            cursor: default;
        }

        .hm-warning {
            background: #ffc107;
            color: #000;
        }

        .hm-urgent {
            background: #fd7e14;
            color: #fff;
        }

        .hm-expired {
            background: #dc3545;
            color: #fff;
        }

        .hm-mix {
            background: linear-gradient(135deg, #dc3545 50%, #ffc107 50%);
            color: #fff;
        }

        .hm-normal {
            background: #198754;
            /* hijau (bootstrap success) */
            color: #fff;
        }

        .hm-info {
            background: #0dcaf0;
            /* biru muda (bootstrap info) */
            color: #000;
        }
    </style>
@endsection

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ===================== --}}
        {{-- RINGKASAN --}}
        {{-- ===================== --}}
        <div class="row g-3 mb-4">

            {{-- BARIS ATAS (2 KARTU) --}}
            <div class="col-12 col-md-6">
                <div class="card stat-card border-primary">
                    <div class="card-body">
                        <div class="stat-title text-muted">Total Dokumen</div>
                        <p class="stat-value text-primary">{{ $totalDokumen }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card stat-card border-success">
                    <div class="card-body">
                        <div class="stat-title text-muted">Dokumen Aktif</div>
                        <p class="stat-value text-success">{{ $dokumenAktif }}</p>
                    </div>
                </div>
            </div>

            {{-- BARIS BAWAH (3 KARTU) --}}
            <div class="col-12 col-md-4">
                <div class="card stat-card border-warning">
                    <div class="card-body">
                        <div class="stat-title text-muted">Dokumen Digantikan</div>
                        <p class="stat-value text-warning">{{ $dokumenDigantikan }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stat-card border-danger">
                    <div class="card-body">
                        <div class="stat-title text-muted">Dokumen Expired</div>
                        <p class="stat-value text-danger">{{ $dokumenExpired }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stat-card border-secondary">
                    <div class="card-body">
                        <div class="stat-title text-muted">Dokumen Tidak Diperpanjang</div>
                        <p class="stat-value text-secondary">{{ $dokumenTidakDiperpanjang }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- LEGEND HEATMAP --}}
        {{-- ===================== --}}
        <div class="d-flex flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="legend-box bg-white border"></span>
                <small>Tidak ada dokumen</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="legend-box bg-warning"></span>
                <small>Warning (H-30 s/d H-7)</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="legend-box bg-orange"></span>
                <small>Urgent (H-3 s/d H-1)</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="legend-box bg-danger"></span>
                <small>Expired</small>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="legend-box" style="background:#198754"></span>
                <small>Aman (di luar window)</small>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- HEATMAP DOKUMEN EXPIRED --}}
        {{-- ===================== --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Heatmap Bulanan Dokumen</h5>
                <small class="text-muted">{{ \Carbon\Carbon::parse($today)->format('F Y') }}</small>
            </div>

            <div class="card-body">
                <div class="heatmap">
                    @foreach ($calendarHeatmap as $d)
                        <div class="heatmap-cell
                {{ $d['level'] === 'none'
                    ? 'hm-none'
                    : ($d['level'] === 'expired' && $d['total'] > 1
                        ? 'hm-mix'
                        : 'hm-' . $d['level']) }}"
                            data-date="{{ $d['tanggal'] }}" title="{{ $d['tanggal'] }} | {{ $d['total'] }} dokumen">
                            {{ $d['day'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- MODAL HEATMAP --}}
        {{-- ===================== --}}
        <div class="modal fade" id="modalHeatmap" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Dokumen pada <span id="modalTanggal"></span>
                        </h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="modalContent">
                            <div class="text-muted text-center py-4">
                                Memuat data...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- GRAFIK + RINGKASAN REMINDER --}}
        {{-- ===================== --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Grafik Status Dokumen</h5>
                        <span class="text-muted small">Holding:
                            {{ $holding->holding_name_hint ?? $holding->holding_code }}</span>
                    </div>
                    <div class="card-body chart-wrap">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Reminder</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0">
                            <div>
                                <div class="fw-bold">Dokumen Akan Berakhir (sesuai Reminder kategori)</div>
                                <div class="text-muted small">
                                    Dokumen aktif yang masuk window reminder berdasarkan <code>reminder_days</code>.
                                </div>
                            </div>
                            <div class="fs-2 fw-bold text-warning">
                                {{ $dokumenReminder->count() }}
                            </div>
                        </div>

                        <hr>

                        <div class="text-muted small">
                            Catatan:
                            <ul class="mb-0">
                                <li>Expired menghitung dokumen berstatus <b>expired</b> dan dokumen <b>aktif</b> yang
                                    sudah lewat tanggal akhir.</li>
                                <li>Dokumen <b>digantikan</b> tidak akan menjadi expired walaupun tanggalnya lewat.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- GRAFIK DOKUMEN AKAN EXPIRED --}}
        {{-- ===================== --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dokumen Akan Expired (berdasarkan kategori)</h5>
            </div>
            <div class="card-body">
                <canvas id="chartAkanExpired" height="120"></canvas>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- TABEL REMINDER --}}
        {{-- ===================== --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dokumen Akan Berakhir</h5>
                <span class="text-muted small">Total: {{ $dokumenReminder->count() }}</span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-warning">
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th style="width:150px;">Berlaku Sampai</th>
                                <th style="width:140px;">Reminder (Hari)</th>
                                <th style="width:90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumenReminder as $doc)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $doc->nomer_dukumen }}</td>
                                    <td>{{ $doc->nama }}</td>
                                    <td>{{ $doc->nama_kategori ?? '-' }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($doc->tanggal_akhir)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-center">{{ $doc->reminder_days ?? 30 }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('dokumen_legal.show', [$holding->holding_code, $doc->id]) }}"
                                            class="btn btn-info btn-sm btn-aksi" title="Lihat">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada dokumen yang mendekati expired.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- TABEL EXPIRED --}}
        {{-- ===================== --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Dokumen Expired</h5>
                <span class="text-muted small">Total: {{ $listExpired->count() }}</span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-danger">
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th style="width:150px;">Tanggal Akhir</th>
                                <th style="width:120px;" class="text-center">Status</th>
                                <th style="width:90px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listExpired as $doc)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $doc->nomer_dukumen }}</td>
                                    <td>{{ $doc->nama }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($doc->tanggal_akhir)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($doc->status === 'aktif')
                                            <span class="badge bg-danger">Lewat Tanggal</span>
                                        @else
                                            <span class="badge bg-danger">Expired</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dokumen_legal.show', [$holding->holding_code, $doc->id]) }}"
                                            class="btn btn-info btn-sm btn-aksi" title="Lihat">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Tidak ada dokumen expired.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function() {
            const data = @json($grafikStatus);

            const ctx = document.getElementById('chartStatus');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Digantikan', 'Expired', 'Tidak Diperpanjang'],
                    datasets: [{
                        data: [data.aktif ?? 0, data.digantikan ?? 0, data.expired ?? 0, data
                            .tidak_diperpanjang ?? 0
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>

    <script>
        const dataExpired = @json($chartAkanExpired);

        const labels = Object.keys(dataExpired);
        const values = Object.values(dataExpired);

        const ctx = document.getElementById('chartAkanExpired');

        if (ctx && labels.length) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Dokumen',
                        data: values,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.heatmap-cell').forEach(cell => {

                const date = cell.dataset.date;
                if (!date) return;

                cell.addEventListener('click', function() {

                    if (this.classList.contains('hm-none')) return;

                    document.getElementById('modalTanggal').innerText = date;
                    document.getElementById('modalContent').innerHTML =
                        '<div class="text-center py-4">Memuat data...</div>';

                    fetch(
                            `{{ route('dashboard.dokumen-legal.heatmap-detail', $holding->holding_code) }}?date=${date}`
                        )
                        .then(res => {
                            if (!res.ok) throw new Error('404');
                            return res.text();
                        })
                        .then(html => {
                            document.getElementById('modalContent').innerHTML = html;
                        })
                        .catch(() => {
                            document.getElementById('modalContent').innerHTML =
                                '<div class="alert alert-danger text-center">Gagal memuat data</div>';
                        });

                    new bootstrap.Modal(document.getElementById('modalHeatmap')).show();
                });
            });
        });
    </script>
@endsection
