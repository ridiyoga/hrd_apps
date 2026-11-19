@extends('admin.layouts.dashboard')
@section('isi')
@include('sweetalert::alert')
{{-- Style khusus untuk merapikan kartu KPI dan chart dashboard HRD. --}}
<style>
    .dashboard-scope-badge {
        background: rgba(147, 112, 219, .12);
        border-radius: 999px;
        color: #6f42c1;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        white-space: nowrap;
    }

    .dashboard-total-pill {
        background: #f8f9fb;
        border: 1px solid #eceef3;
        border-radius: 8px;
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
        padding: 7px 10px;
        white-space: nowrap;
    }

    .dashboard-chart-card {
        height: 100%;
    }

    .dashboard-chart-card .card-header {
        min-height: 64px;
    }

    .dashboard-chart-title {
        margin-bottom: 2px;
    }

    .dashboard-chart-subtitle {
        color: #8a8c94;
        font-size: 12px;
    }

    .dashboard-chart-body {
        align-items: center;
        display: flex;
        justify-content: center;
        overflow: hidden;
    }

    .dashboard-chart-body.chart-bar {
        min-height: 340px;
    }

    .dashboard-chart-body.chart-pie {
        min-height: 320px;
    }

    .dashboard-chart-body.chart-line {
        min-height: 360px;
    }

    .dashboard-chart-body > div {
        width: 100%;
    }

    .dashboard-chart-body.chart-rank {
        align-items: stretch;
        min-height: 340px;
        overflow-y: auto;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y" style="font-size: small;">
    {{-- Blok KPI karyawan per holding: gender, kategori kerja, join bulan ini, dan resign bulan ini. --}}
    <div class="row gy-4">
        <!-- Transactions -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ url('dashboard/option/' . $holding->holding_code) }}">DASHBOARD</a>
                            </li>
                            <li class="breadcrumb-item active">HRD
                            </li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Karyawan</h5>

                    </div>
                    <p class="mt-3"><span class="fw-medium">Total Karyawan </span> Kontrak Kerja @if ($holding == '')
                        @else
                        {{ $holding->holding_name }}
                        @endif
                    </p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow">
                                        <i class="mdi mdi-account-tie mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Karyawan Laki- Laki</div>
                                    <h5 class="mb-0" id="karyawan_lakilaki"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow">
                                        <i class="mdi mdi-account-tie mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Karyawan Perempuan</div>
                                    <h5 class="mb-0" id="karyawan_perempuan"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow">
                                        <i class="mdi mdi-account-tie mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Karyawan Bulanan</div>
                                    <h5 class="mb-0" id="karyawan_office"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="mdi mdi-account-tie mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Karyawan Harian</div>
                                    <h5 class="mb-0" id="karyawan_shift"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-secondary rounded shadow">
                                        <i class="mdi mdi-account-plus mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Masuk Bulan Ini</div>
                                    <h5 class="mb-0" id="karyawan_baru_bulan_ini"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-danger rounded shadow">
                                        <i class="mdi mdi-account-cancel mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1">Resign Bulan Ini</div>
                                    <h5 class="mb-0" id="karyawan_nonaktif_bulan_ini"></h5>
                                </div>
                            </div>
                        </div>
                        {{-- Blok peringatan kontrak: karyawan dengan masa kontrak mendekati habis. --}}
                        <!-- @if ($count_karyawan_habis_kontrak > 0)
    -->
                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-account-alert-outline "></i>
                            <span>Karyawan Masa Tenggang Kontrak @if ($holding == '')
                                @else
                                {{ $holding->holding_name }}
                                @endif </span><span>Total : {{ $count_karyawan_habis_kontrak }}
                                Orang
                                <a
                                    href=" @if (Auth::user()->is_admin == 'hrd') {{ url('hrd/karyawan/masa_tenggang_kontrak/' . $holding->holding_code) }}@else{{ url('karyawan/masa_tenggang_kontrak/' . $holding->holding_code) }} @endif">&nbsp;Lihat&nbsp;Semua&nbsp;.
                                    .</a>
                            </span>

                        </div>
                        <div class="modal fade" id="modal_perbarui_kontrak" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <form id="form_update_kontrak" method="post"
                                    action="@if (Auth::user()->is_admin == 'hrd') {{ url('hrd/karyawan/update_kontrak_proses') }}@else {{ url('karyawan/update_kontrak_proses') }} @endif"
                                    class="modal-content" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Form Pembaruan Kontrak Karyawan
                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mt-2">
                                            <div class="col-md-12">
                                                <div class="card mb-4">
                                                    <!-- Account -->
                                                    <div class="card-body">
                                                        <div
                                                            class="d-flex align-items-start align-items-sm-center gap-4">
                                                            <input type="hidden" name="id_karyawan" id="id_karyawan"
                                                                value="">
                                                            <img src="{{ asset('admin/assets/img/avatars/1.png') }}"
                                                                alt="user-avatar"
                                                                class="d-block w-px-120 h-px-120 rounded"
                                                                id="template_foto_karyawan" />
                                                            <table>
                                                                <tr>
                                                                    <th>Nama</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_nama"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Divisi</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_divisi"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Jabatan</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_jabatan"></td>
                                                                <tr>
                                                                    <th>Kontrak Kerja</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_kontrak_kerja"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Penempatan Kerja</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_penempatan_kerja"> </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tgl Mulai Kontrak</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_mulai_kontrak"> </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Tgl Selesai Kontrak</th>
                                                                    <td>&nbsp;</td>
                                                                    <td>:</td>
                                                                    <td id="td_selesai_kontrak"></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="date" id="tgl_mulai_kontrak_baru"
                                                        name="tgl_mulai_kontrak_baru" readonly
                                                        value="{{ date('Y-m-d') }}"
                                                        class="form-control @error('tgl_mulai_kontrak_baru') is-invalid @enderror"
                                                        placeholder="Tanggal" />
                                                    <label for="tgl_mulai_kontrak_baru">Tanggal Mulai Kontrak
                                                        Baru</label>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="date" id="tgl_selesai_kontrak_baru"
                                                        name="tgl_selesai_kontrak_baru" value=""
                                                        class="form-control @error('tgl_selesai_kontrak_baru') is-invalid @enderror"
                                                        placeholder="Tanggal" />
                                                    <label for="tgl_selesai_kontrak_baru">Tanggal Selesai Kontrak
                                                        Baru</label>
                                                </div>
                                            </div>
                                            <br>
                                            <br>

                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="lama_kontrak_baru" name="lama_kontrak_baru"
                                                        class="form-control @error('lama_kontrak_baru') is-invalid @enderror"
                                                        placeholder="Lama Kontrak">
                                                        <option value="">- Select Lama Kontrak -</option>
                                                        <option value="3 bulan">3 Bulan</option>
                                                        <option value="6 bulan">6 Bulan</option>
                                                        <option value="1 tahun">1 Tahun</option>
                                                        <option value="2 tahun">2 Tahun</option>
                                                        <option value="tetap">Tetap</option>
                                                    </select>
                                                    <label for="lama_kontrak_baru">Lama Kontrak Baru</label>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-md-12">
                                                <hr class="m-0">
                                                <small class="text-light fw-medium">Upload File Pendukung</small>
                                                <br>
                                                <br>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="file" id="file_kontrak_kerja"
                                                        accept="application/pdf" name="file_kontrak_kerja"
                                                        class="form-control @error('file_kontrak_kerja') is-invalid @enderror"
                                                        placeholder="File" />
                                                    <label for="file_kontrak_kerja">File Kontrak Kerja</label>
                                                </div>
                                                <small class="text-info fw-medium">*Format PDF</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Save
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-12 col-12 mt--10">
                            <div class="d-flex align-items-center" style="overflow-x:auto;">
                                <table class="table table-responsive" id="table_karyawan_tenggang"
                                    style="width: 100%; font-size: smaller;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nomor&nbsp;ID</th>
                                            <th>Nama&nbsp;Karyawan</th>
                                            <th>Telepon</th>
                                            <th>Divisi</th>
                                            <th>Jabatan</th>
                                            <th>Tanggal&nbsp;Kontrak</th>
                                            <th>Status</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @php
                                        $no = 1;
                                        @endphp
                                        @foreach ($karyawan_habis_kontrak as $karyawan_habis_kontrak)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $karyawan_habis_kontrak->nomor_identitas_karyawan }}</td>
                                            <td>{{ $karyawan_habis_kontrak->name }}</td>
                                            <td>{{ $karyawan_habis_kontrak->telepon }}</td>
                                            <td>
                                                @if ($karyawan_habis_kontrak->Divisi == null)
                                                -@else{{ $karyawan_habis_kontrak->Divisi->nama_divisi }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($karyawan_habis_kontrak->Jabatan == null)
                                                -@else{{ $karyawan_habis_kontrak->Jabatan->nama_jabatan }}
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($karyawan_habis_kontrak->tgl_mulai_kontrak)->isoFormat('DD MMMM YYYY') }}&nbsp;-&nbsp;{{ \Carbon\Carbon::parse($karyawan_habis_kontrak->tgl_selesai_kontrak)->isoFormat('DD MMMM YYYY') }}
                                            </td>
                                            <?php
                                            $date1 = new DateTime();
                                            $date2 = new DateTime($karyawan_habis_kontrak->tgl_selesai_kontrak);
                                            $interval = $date1->diff($date2);
                                            // print_r($interval->d);
                                            ?>
                                            <td>
                                                @if ($karyawan_habis_kontrak->tgl_selesai_kontrak <= $date_now1)
                                                    <span class="badge bg-label-danger"><i
                                                        class="mdi mdi-close-octagon-outline"></i> Melebihi
                                                    Masa Kontrak {{ $interval->format('%a') }} Hari </span>
                                                    @else
                                                    <span class="badge bg-label-warning"><i
                                                            class="mdi mdi-alert-octagon-outline"></i> Kontrak
                                                        Kurang {{ $interval->format('%a') }} Hari </span>
                                                    @endif
                                            </td>
                                            <td><button id="btn_perbarui_kontrak"
                                                    data-id="{{ $karyawan_habis_kontrak->id }}"
                                                    data-nama="{{ $karyawan_habis_kontrak->name }}"
                                                    data-divisi="@if ($karyawan_habis_kontrak->Divisi == null) -@else{{ $karyawan_habis_kontrak->Divisi->nama_divisi }} @endif"
                                                    data-jabatan="@if ($karyawan_habis_kontrak->Jabatan == null) -@else{{ $karyawan_habis_kontrak->Jabatan->nama_jabatan }} @endif"
                                                    data-foto="{{ $karyawan_habis_kontrak->foto_karyawan }}"
                                                    data-tgl_mulai_kontrak="{{ $karyawan_habis_kontrak->tgl_mulai_kontrak }}"
                                                    data-tgl_selesai_kontrak="{{ $karyawan_habis_kontrak->tgl_selesai_kontrak }}"
                                                    data-penempatan_kerja="{{ $karyawan_habis_kontrak->penempatan_kerja }}"
                                                    data-kontrak_kerja="@if ($karyawan_habis_kontrak->kontrak_kerja == 'SPS') PT. SURYA PANGAN SEMESTA @elseif($karyawan_habis_kontrak->kontrak_kerja == 'SP') CV. SUMBER PANGAN @else CV. SURYA INTI PANGAN @endif"
                                                    type="button"
                                                    class="btn btn-xs btn-info waves-effect waves-light"><i
                                                        class="mdi mdi-update"></i>&nbsp;Perbarui</button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div style="float: right; margin-right: 2px; margin-top: 2px;" class="float-right">
                                <a
                                    href="@if (Auth::user()->is_admin == 'hrd') {{ url('hrd/karyawan/masa_tenggang_kontrak/' . $holding->holding_code) }}@else {{ url('karyawan/masa_tenggang_kontrak/' . $holding->holding_code) }} @endif"><span
                                        class="badge bg-label-success">Lihat&nbsp;Semua&nbsp;<i
                                            class="mdi mdi-chevron-double-right"></i></span></a>
                            </div>
                        </div>
                        <!--
    @endif -->
                    </div>
                </div>
            </div>
        </div>
        {{-- Blok KPI absensi hari ini: hadir, terlambat, tidak absen, cuti/izin/sakit. --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="card-title m-0 me-2">Absensi Hari Ini</h5>
                        <small class="text-muted">{{ $absensi_hari_ini['tanggal'] }} | Jadwal kerja:
                            {{ $absensi_hari_ini['total_jadwal_kerja'] }} Orang</small>
                    </div>
                    <span class="badge bg-label-success">Hadir {{ $absensi_hari_ini['persentase_hadir'] }}%</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-xl-3 col-md-6 col-12">
                            <div class="d-flex align-items-center p-3 border rounded h-100">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow">
                                        <i class="mdi mdi-account-check mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1 text-muted">Hadir</div>
                                    <h4 class="mb-0">{{ $absensi_hari_ini['hadir'] }} Orang</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-12">
                            <div class="d-flex align-items-center p-3 border rounded h-100">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow">
                                        <i class="mdi mdi-clock-alert-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1 text-muted">Terlambat</div>
                                    <h4 class="mb-0">{{ $absensi_hari_ini['terlambat'] }} Orang</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-12">
                            <div class="d-flex align-items-center p-3 border rounded h-100">
                                <div class="avatar">
                                    <div class="avatar-initial bg-danger rounded shadow">
                                        <i class="mdi mdi-account-off-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1 text-muted">Tidak Absen</div>
                                    <h4 class="mb-0">{{ $absensi_hari_ini['tidak_absen'] }} Orang</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-12">
                            <div class="d-flex align-items-center p-3 border rounded h-100">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow">
                                        <i class="mdi mdi-calendar-account-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <div class="small mb-1 text-muted">Cuti/Izin/Sakit</div>
                                    <h4 class="mb-0">{{ $absensi_hari_ini['cuti_izin_sakit'] }} Orang</h4>
                                    <small class="text-muted">Cuti {{ $absensi_hari_ini['cuti'] }} | Izin/Sakit
                                        {{ $absensi_hari_ini['izin_sakit'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        {{-- Blok grafik utama: karyawan per departemen dan persentase jabatan utama. --}}
        <div class="row gy-4">
            <div class="col-xl-8 col-md-12">
                <div class="card dashboard-chart-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="dashboard-chart-title">Karyawan per Departemen</h6>
                            <div class="dashboard-chart-subtitle">Data aktif {{ $holding->holding_name }}</div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="dashboard-total-pill">Total <span id="total_departemen_chart">0</span> Orang</span>
                            <span class="dashboard-scope-badge">Per Holding</span>
                        </div>
                    </div>
                    <div class="card-body dashboard-chart-body chart-bar">
                        <div id="grafik_dept"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12">
                <div class="card dashboard-chart-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="dashboard-chart-title">Jabatan Karyawan</h6>
                            <div class="dashboard-chart-subtitle">Komposisi jabatan utama</div>
                        </div>
                        <span class="dashboard-total-pill">Total <span id="total_jabatan_chart">0</span> Orang</span>
                    </div>
                    <div class="card-body dashboard-chart-body chart-rank">
                        <div id="grafik_jabatan"></div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        {{-- Blok grafik demografi: gender, lama kontrak terakhir, dan status nikah. --}}
        <div class="row gy-4">
            <div class="col-xl-4 col-md-4">
                <div class="card dashboard-chart-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="dashboard-chart-title">Gender Karyawan</h6>
                            <div class="dashboard-chart-subtitle">Karyawan aktif</div>
                        </div>
                    </div>
                    <div class="card-body dashboard-chart-body chart-pie">
                        <div id="grafik_gender"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4">
                <div class="card dashboard-chart-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="dashboard-chart-title">Kontrak Karyawan</h6>
                            <div class="dashboard-chart-subtitle">Lama kontrak kerja</div>
                        </div>
                    </div>
                    <div class="card-body dashboard-chart-body chart-pie">
                        <div id="grafik_kontrak"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4">
                <div class="card dashboard-chart-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="dashboard-chart-title">Status Nikah Karyawan</h6>
                            <div class="dashboard-chart-subtitle">Karyawan aktif</div>
                        </div>
                    </div>
                    <div class="card-body dashboard-chart-body chart-pie">
                        <div id="grafik_status"></div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        {{-- Blok grafik absensi bulanan per status yang sudah didefinisikan. --}}
        <div class="row gy-4">
            <div class="col-xl-12 col-md-12">
                <div class="card dashboard-chart-card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="dashboard-chart-title">Absensi Bulan Ini</h6>
                            <div class="dashboard-chart-subtitle">Tepat waktu, terlambat, tidak hadir, cuti -
                                {{ $holding->holding_name }}</div>
                        </div>
                        <span class="dashboard-scope-badge">Per Holding</span>
                    </div>
                    <div class="card-body dashboard-chart-body chart-line">
                        <div id="grafik_absensi"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    {{-- Script dashboard: modal kontrak, request AJAX, dan render chart ApexCharts. --}}
    @section('js')
    <script src="{{ asset('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        // Isi modal perbarui kontrak dari tombol pada tabel masa tenggang kontrak.
        $(document).on('click', '#btn_perbarui_kontrak', function() {
            var id = $(this).data('id');
            var holding = $(this).data("holding");
            var nama = $(this).data('nama');
            var divisi = $(this).data('divisi');
            var jabatan = $(this).data('jabatan');
            var bagian = $(this).data('bagian');
            var foto = $(this).data('foto');
            var tgl_mulai_kontrak = $(this).data('tgl_mulai_kontrak');
            var tgl_selesai_kontrak = $(this).data('tgl_selesai_kontrak');
            var kontrak_kerja = $(this).data('kontrak_kerja');
            var penempatan_kerja = $(this).data('penempatan_kerja');
            if (foto == '' | foto == null) {
                $('#template_foto_karyawan').attr('src', "{{ asset('admin/assets/img/avatars/1.png') }}");
            } else {
                $('#template_foto_karyawan').attr('src', "{{ url('storage/app/public/foto_karyawan/') }}" + foto);
            }
            $('#td_nama').html(nama);
            $('#td_divisi').html(divisi);
            $('#td_jabatan').html(jabatan);
            $('#td_bagian').html(bagian);
            $('#td_jabatan').html(jabatan);
            $('#td_mulai_kontrak').html(tgl_mulai_kontrak);
            $('#td_selesai_kontrak').html(tgl_selesai_kontrak);
            $('#td_kontrak_kerja').html(kontrak_kerja);
            $('#td_penempatan_kerja').html(penempatan_kerja);
            $('#id_karyawan').val(id);
            $('#modal_perbarui_kontrak').modal('show');
        });
    </script>
    <script>
        /**
         * Dashboard Analytics
         */
        $(document).ready(function() {
            // Load data awal sesuai holding yang sedang dibuka.
            var holding = '{{ $holding->holding_code }}';
            load_graph_Dashboard_All(holding);
            get_grafik_absensi(holding);

            // Ambil KPI karyawan dan seluruh data chart utama dashboard.
            function load_graph_Dashboard_All(holding = '') {
                // console.log(holding)
                $.ajax({
                    url: "{{ url('/graph_Dashboard_All') }}" + '/' + holding,
                    type: "GET",
                    error: function(error) {
                        // console.log(error);
                        Swal.fire({
                            title: 'Error',
                            text: error.responseJSON.message + ' ' + error.responseJSON.file +
                                ' ' + error.responseJSON.line,
                            icon: 'error',
                            timer: 60000,
                            showConfirmButton: false,
                        })
                    },
                    success: function(response) {
                        // console.log(response);
                        // Update kartu KPI karyawan di bagian atas dashboard.
                        $('#karyawan_lakilaki').html(response.karyawan_laki + ' Orang');
                        $('#karyawan_perempuan').html(response.karyawan_perempuan + ' Orang');
                        $('#karyawan_office').html(response.karyawan_office + ' Orang');
                        $('#karyawan_shift').html(response.karyawan_shift + ' Orang');
                        $('#karyawan_baru_bulan_ini').html(response.karyawan_baru_bulan_ini + ' Orang');
                        $('#karyawan_nonaktif_bulan_ini').html(response.karyawan_nonaktif_bulan_ini + ' Orang');

                        // Helper untuk merapikan response Laravel Collection/object menjadi array JavaScript.
                        const toArray = function(value) {
                            if (Array.isArray(value)) {
                                return value;
                            }

                            if (value && typeof value === 'object') {
                                return Object.values(value);
                            }

                            return [];
                        };
                        const normalizeLabels = function(labels) {
                            return labels.map(function(label) {
                                return label === null || label === undefined || String(label).trim() === '' ?
                                    'Tidak diisi' : String(label);
                            });
                        };
                        const toPercent = function(values) {
                            const total = values.reduce(function(sum, value) {
                                return sum + Number(value || 0);
                            }, 0);

                            if (total <= 0) {
                                return values.map(function() {
                                    return 0;
                                });
                            }

                            return values.map(function(value) {
                                return Number(((Number(value || 0) / total) * 100).toFixed(1));
                            });
                        };
                        // Susun data mentah dari backend untuk masing-masing chart.
                        const jumlahKaryawanDepartemen = toArray(response.jumlah_karyawan_departemen).map(Number);
                        const namaDepartemen = normalizeLabels(toArray(response.nama_departemen));
                        const dataKaryawanJabatan = toArray(response.data_karyawan_jabatan_all).map(Number);
                        const labelsJabatan = normalizeLabels(toArray(response.labels_jabatan_all));
                        const persentaseJabatan = toPercent(dataKaryawanJabatan);
                        const jabatanRows = labelsJabatan.map(function(label, index) {
                            return {
                                label: label,
                                jumlah: dataKaryawanJabatan[index] || 0,
                                persen: persentaseJabatan[index] || 0
                            };
                        }).sort(function(a, b) {
                            return b.persen - a.persen;
                        });
                        const labelsJabatanSorted = jabatanRows.map(function(row) {
                            return row.label;
                        });
                        const dataKaryawanJabatanSorted = jabatanRows.map(function(row) {
                            return row.jumlah;
                        });
                        const persentaseJabatanSorted = jabatanRows.map(function(row) {
                            return row.persen;
                        });
                        const dataKaryawanGender = toArray(response.data_karyawan_gender).map(Number);
                        const labelsGender = normalizeLabels(toArray(response.labels_gender));
                        const dataKaryawanKontrak = toArray(response.data_karyawan_kontrak).map(Number);
                        const labelsKontrak = normalizeLabels(toArray(response.labels_kontrak));
                        const dataKaryawanStatus = toArray(response.data_karyawan_status).map(Number);
                        const labelsStatus = normalizeLabels(toArray(response.labels_status));
                        // Hitung total dan skala maksimum supaya label chart lebih mudah dibaca.
                        const maxDepartemen = Math.max(...jumlahKaryawanDepartemen, 1);
                        const maxDepartemenAxis = Math.ceil((maxDepartemen * 1.15) / 10) * 10;
                        const totalDepartemenChart = jumlahKaryawanDepartemen.reduce(function(total, value) {
                            return total + Number(value || 0);
                        }, 0);
                        const totalJabatanChart = dataKaryawanJabatan.reduce(function(total, value) {
                            return total + Number(value || 0);
                        }, 0);
                        $('#total_departemen_chart').html(totalDepartemenChart);
                        $('#total_jabatan_chart').html(totalJabatanChart);
                        let cardColor, labelColor, borderColor, chartBgColor, bodyColor;


                        cardColor = config.colors.cardColor;
                        labelColor = config.colors.textMuted;
                        borderColor = config.colors.borderColor;
                        chartBgColor = config.colors.chartBgColor;
                        bodyColor = config.colors.bodyColor;

                        // Chart departemen: bar jumlah karyawan aktif per departemen.
                        // --------------------------------------------------------------------
                        const weeklyOverviewChartEl = document.querySelector('#grafik_dept'),
                            weeklyOverviewChartConfig = {
                                chart: {
                                    type: 'bar',
                                    height: 330,
                                    width: "100%",
                                    parentHeightOffset: 0,
                                    toolbar: {
                                        show: false
                                    },
                                    animations: {
                                        initialAnimation: {
                                            enabled: false
                                        }
                                    }
                                },
                                series: [{
                                    name: 'Jumlah Karyawan',
                                    data: jumlahKaryawanDepartemen,
                                }],
                                colors: [config.colors.primary],
                                plotOptions: {
                                    bar: {
                                        borderRadius: 6,
                                        columnWidth: '46%',
                                        endingShape: 'rounded',
                                        startingShape: 'rounded'
                                    }
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                legend: {
                                    show: false
                                },
                                grid: {
                                    strokeDashArray: 8,
                                    borderColor,
                                    padding: {
                                        bottom: 8,
                                        left: 8,
                                        right: 8
                                    }
                                },
                                xaxis: {
                                    categories: namaDepartemen,
                                    // tickPlacement: 'on',
                                    labels: {
                                        style: {
                                            fontSize: '10px',
                                        },
                                        rotate: -45,
                                        trim: true,
                                        maxHeight: 92
                                    },
                                    axisBorder: {
                                        show: true
                                    },
                                    axisTicks: {
                                        show: true
                                    }
                                },
                                yaxis: {
                                    min: 0,
                                    max: maxDepartemenAxis,
                                    show: true,
                                    tickAmount: 5,
                                    labels: {
                                        formatter: function(val) {
                                            return parseInt(val) + ' Orang';
                                        },
                                        style: {
                                            fontSize: '0.75rem',
                                            fontFamily: 'Inter',
                                            colors: labelColor
                                        }
                                    }
                                },
                                states: {
                                    hover: {
                                        filter: {
                                            type: 'none'
                                        }
                                    },
                                    active: {
                                        filter: {
                                            type: 'none'
                                        }
                                    }
                                },
                                tooltip: {
                                    y: {
                                        formatter: function(val) {
                                            return parseInt(val) + ' Orang';
                                        }
                                    }
                                }
                            };
                        if (typeof weeklyOverviewChartEl !== undefined && weeklyOverviewChartEl !==
                            null) {
                            const weeklyOverviewChart = new ApexCharts(weeklyOverviewChartEl,
                                weeklyOverviewChartConfig);
                            weeklyOverviewChart.render();
                        }
                        // -------------------------------------------------------------------
                        // End Chart Nama Departemen
                        // Chart jabatan: horizontal bar persentase jabatan utama karyawan.
                        // --------------------------------------------------------------------

                        var options_jabatan = {
                            series: [{
                                name: 'Persentase',
                                data: persentaseJabatanSorted
                            }],
                            chart: {
                                height: Math.max(300, labelsJabatanSorted.length * 24),
                                width: '100%',
                                type: 'bar',
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: [config.colors.primary],
                            plotOptions: {
                                bar: {
                                    barHeight: '65%',
                                    borderRadius: 4,
                                    horizontal: true
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                offsetX: 8,
                                textAnchor: 'start',
                                formatter: function(val) {
                                    return val.toFixed(1) + '%';
                                },
                                style: {
                                    colors: ['#4b5563'],
                                    fontSize: '11px',
                                    fontWeight: 600
                                }
                            },
                            grid: {
                                borderColor,
                                strokeDashArray: 6,
                                padding: {
                                    left: 8,
                                    right: 18
                                }
                            },
                            xaxis: {
                                categories: labelsJabatanSorted,
                                max: 100,
                                labels: {
                                    formatter: function(val) {
                                        return parseInt(val) + '%';
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    maxWidth: 150,
                                    style: {
                                        fontSize: '11px'
                                    }
                                }
                            },
                            legend: {
                                show: false
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val, opts) {
                                        const jumlah = dataKaryawanJabatanSorted[opts.dataPointIndex] || 0;
                                        return val.toFixed(1) + '% (' + jumlah + ' Orang)';
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#grafik_jabatan"),
                            options_jabatan);
                        chart.render();
                        // --------------------------------------------------------------------
                        // End Chart Nama Jabatan
                        // Chart gender: donut komposisi laki-laki, perempuan, dan data kosong/lainnya.
                        // --------------------------------------------------------------------
                        var options_gender = {
                            series: dataKaryawanGender,
                            chart: {
                                height: 280,
                                width: '100%',
                                type: 'donut',
                                toolbar: {
                                    show: false
                                }
                            },
                            labels: labelsGender,
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '64%',
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce(function(a, b) {
                                                        return a + b;
                                                    }, 0) + ' Orang';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return val >= 4 ? val.toFixed(1) + '%' : '';
                                },
                                dropShadow: {
                                    enabled: false
                                },
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 600
                                }
                            },
                            legend: {
                                fontSize: '11px',
                                position: 'bottom'
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + ' Orang';
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#grafik_gender"),
                            options_gender);
                        chart.render();
                        // --------------------------------------------------------------------
                        // End Chart Gender
                        // Chart kontrak: donut lama kontrak dari kontrak terakhir per karyawan.
                        // --------------------------------------------------------------------
                        var options_kontrak = {
                            series: dataKaryawanKontrak,
                            chart: {
                                height: 280,
                                width: '100%',
                                type: 'donut',
                                toolbar: {
                                    show: false
                                }
                            },
                            labels: labelsKontrak,
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '64%',
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce(function(a, b) {
                                                        return a + b;
                                                    }, 0) + ' Orang';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return val >= 4 ? val.toFixed(1) + '%' : '';
                                },
                                dropShadow: {
                                    enabled: false
                                },
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 600
                                }
                            },
                            legend: {
                                fontSize: '11px',
                                position: 'bottom'
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + ' Orang';
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#grafik_kontrak"),
                            options_kontrak);
                        chart.render();
                        // --------------------------------------------------------------------
                        // End Chart Kontrak
                        // Chart status pernikahan: donut komposisi status nikah karyawan aktif.
                        // --------------------------------------------------------------------
                        var options_status = {
                            series: dataKaryawanStatus,
                            chart: {
                                height: 280,
                                width: '100%',
                                type: 'donut',
                                toolbar: {
                                    show: false
                                }
                            },
                            labels: labelsStatus,
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '64%',
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: 'Total',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce(function(a, b) {
                                                        return a + b;
                                                    }, 0) + ' Orang';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function(val) {
                                    return val >= 4 ? val.toFixed(1) + '%' : '';
                                },
                                dropShadow: {
                                    enabled: false
                                },
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 600
                                }
                            },
                            legend: {
                                fontSize: '11px',
                                position: 'bottom'
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val + ' Orang';
                                    }
                                }
                            }
                        };

                        var chart = new ApexCharts(document.querySelector("#grafik_status"),
                            options_status);
                        chart.render();
                        // --------------------------------------------------------------------
                        // End Chart Status Pernikahan
                    }
                });
            }

            // Ambil data absensi bulanan dan render line chart per status.
            function get_grafik_absensi(get_holding = '') {
                get_holding = get_holding || '{{ $holding->holding_code }}';
                var url =
                    "{{ url('get_grafik_absensi_karyawan') }}" +
                    "/" + get_holding;
                // console.log(url);
                // console.log(get_holding);
                $.ajax({
                    url: url,
                    data: {
                        get_holding: get_holding,
                    },
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        // console.log(data);
                        var label_absensi = data.label_absensi;
                        var data_absensi_masuk_tepatwaktu = data.data_absensi_masuk_tepatwaktu;
                        var data_absensi_masuk_telat = data.data_absensi_masuk_telat;
                        var data_absensi_masuk_tidak_hadir = data.data_absensi_masuk_tidak_hadir;
                        var data_absensi_masuk_cuti = data.data_absensi_masuk_cuti;
                        var options = {
                            series: [{
                                name: 'Total Tepat Waktu',
                                data: data_absensi_masuk_tepatwaktu
                            }, {
                                name: 'Total Terlambat',
                                data: data_absensi_masuk_telat
                            }, {
                                name: 'Total Tidak Hadir',
                                data: data_absensi_masuk_tidak_hadir
                            }, {
                                name: 'Total Cuti',
                                data: data_absensi_masuk_cuti
                            }],
                            annotations: {
                                points: [{
                                    x: 'Bananas',
                                    seriesIndex: 0,
                                    label: {
                                        borderColor: '#775DD0',
                                        offsetY: 0,
                                        style: {
                                            color: '#fff',
                                            background: '#775DD0',
                                        },
                                        text: 'Bananas are good',
                                    }
                                }]
                            },
                            chart: {
                                height: 350,
                                type: 'line',
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 10,
                                    columnWidth: '50%',
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                width: [3, 3, 2, 2]
                            },
                            colors: ["#008000", "#F8C300", "#8B0000", "#1E90FF"],
                            grid: {
                                row: {
                                    colors: ['#fff', '#f2f2f2']
                                }
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45
                                },
                                categories: label_absensi,
                                tickAmount: 31
                            },
                            yaxis: {
                                title: {
                                    text: 'Jumlah Karyawan Absensi',
                                },
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'light',
                                    type: "horizontal",
                                    shadeIntensity: 0.25,
                                    gradientToColors: undefined,
                                    inverseColors: true,
                                    opacityFrom: 0.85,
                                    opacityTo: 0.85,
                                    stops: [50, 0, 100]
                                },
                            }
                        };
                        var chart = new ApexCharts(document.querySelector("#grafik_absensi"), options);
                        chart.render();
                        chart.updateSeries([{
                            name: 'Jumlah Karyawan Absen Masuk ',
                            data: data_absensi_masuk
                        }, {
                            name: 'Jumlah Karyawan Absen Pulang ',
                            data: data_absensi_pulang
                        }])
                    },
                    error: function(error) {
                        Swal.fire({
                            title: 'Error',
                            text: error.responseJSON.message + ' ' + error.responseJSON.file +
                                ' ' + error.responseJSON.line,
                            icon: 'error',
                            timer: 60000,
                            showConfirmButton: false,
                        })
                    },
                });
            }
        });
    </script>
    @endsection
