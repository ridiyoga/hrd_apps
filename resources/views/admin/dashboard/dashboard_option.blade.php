@extends('admin.layouts.dashboard')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<style>
    /* Custom Card Styles for Dashboard Options */
    .dashboard-option-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 12px;
        min-height: 150px;
        cursor: pointer;
        background-color: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        height: 100%;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Tambahan: Pastikan konten di tengah secara horizontal */
        text-align: center;
    }

    .dashboard-option-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .card-content-wrap {
        padding: 20px;
    }

    .card-title-option {
        font-weight: 600;
        color: #673ab7;
        margin-bottom: 5px;
        font-size: 1.2rem;
    }

    .card-description {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .card-icon {
        color: #673ab7;
        font-size: 3.5rem;
        margin-bottom: 10px;
    }

    .dashboard-link {
        text-decoration: none;
        display: block;
        height: 100%;
    }
</style>
@endsection
@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y" style="font-size: small;">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">DASHBOARD OPTIONS</h5>

                    </div>
                    <p class="mt-3"><span class="fw-medium">Pilih salah satu dashboard untuk melanjutkan.</span></p>
                </div>

                <div class="card-body">
                    <div class="row g-4 justify-content-center">

                        {{-- *********************************** --}}
                        {{-- START: Dashboard Selection Cards --}}
                        {{-- *********************************** --}}

                        {{-- Mock Data for demonstration. Replace with your actual $dashboards loop --}}
                        @php
                        $dashboards = [
                        [
                        'title' => 'Dashboard HRD',
                        'description' => 'Grafik Karyawan, Grafik Absensi,Grafik Status Karyawan.',
                        'url' => url('dashboard/hrd/{$holding->holding_code}'),
                        'icon' => 'mdi mdi-view-dashboard',
                        ],
                        [
                        'title' => 'Dashboard PORTAL KARIR',
                        'description' => '',
                        'url' => url('dashboard/portal'),
                        'icon' => 'mdi mdi-account-group',
                        ],
                        ];
                        @endphp

                        <div class="col-sm-6 col-md-4">
                            <a href="{{ url('dashboard/hrd', $holding->holding_code) }}" class="dashboard-link">
                                <div class="card dashboard-option-card">
                                    <div class="card-content-wrap">
                                        <i class="mdi mdi-view-dashboard card-icon"></i>
                                        <h5 class="card-title-option">Dashboard HRD</h5>
                                        <p class="card-description">Grafik Karyawan, Grafik Absensi,Grafik Status
                                            Karyawan.</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="{{ url('dashboard/portal', $holding->holding_code) }}" class="dashboard-link">
                                <div class="card dashboard-option-card">
                                    <div class="card-content-wrap">
                                        <i class="mdi mdi-account-group card-icon"></i>
                                        <h5 class="card-title-option">Dashboard PORTAL KARIR</h5>
                                        <p class="card-description">Lihat Status Rekrutmen Karyawan Baru, Grafik Calon
                                            Karyawan, dan Grafik Rekrutmen.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <a href="{{ route('dokumen_legal.dashboard', $holding->holding_code) }}"
                                class="dashboard-link">
                                <div class="card dashboard-option-card">
                                    <div class="card-content-wrap">
                                        <i class="mdi mdi-file-document-alert-outline card-icon"></i>
                                        <h5 class="card-title-option">Dashboard Dokumen Legal</h5>
                                        <p class="card-description">
                                            Reminder dokumen akan berakhir
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        {{-- *********************************** --}}
                        {{-- END: Dashboard Selection Cards --}}
                        {{-- *********************************** --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_add_pendidikan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Karyawan Kontrak Habis dan Masa Tenggang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body py-4">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active position-relative" id="habis-tab" data-bs-toggle="tab"
                                data-bs-target="#habis" type="button" role="tab" aria-controls="habis"
                                aria-selected="true">Kontrak
                                Habis
                                <span
                                    class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.55rem;">
                                    {{ $habis }}
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative" id="atas-tab" data-bs-toggle="tab"
                                data-bs-target="#atas" type="button" role="tab" aria-controls="atas"
                                aria-selected="false">Atasan
                                <span
                                    class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.55rem;">
                                    {{ $atas }}
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative" id="tengah-tab" data-bs-toggle="tab"
                                data-bs-target="#tengah" type="button" role="tab" aria-controls="tengah"
                                aria-selected="false">Menengah &
                                Bawahan
                                <span
                                    class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 0.55rem;">
                                    {{ $tengah }}
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </button>
                        </li>

                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="habis" role="tabpanel"
                            aria-labelledby="habis-tab">
                            <table class="table" id="table_karyawan_bulanan_tenggang"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nomor ID</th>
                                        <th>Nama Karyawan</th>
                                        <th>Mulai Kontrak</th>
                                        <th>Selesai Kontrak</th>
                                        <th>Kontrak Kerja</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="atas" role="tabpanel" aria-labelledby="profile-tab">
                            <table class="table" id="table_karyawan_tenggang_atasan"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nomor ID</th>
                                        <th>Nama Karyawan</th>
                                        <th>Nama Jabatan</th>
                                        <th>Mulai Kontrak</th>
                                        <th>Selesai Kontrak</th>
                                        <th>Kontrak Kerja</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="tengah" role="tabpanel" aria-labelledby="contact-tab">
                            <table class="table" id="table_karyawan_tenggang_menengah"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nomor ID</th>
                                        <th>Nama Karyawan</th>
                                        <th>Nama Jabatan</th>
                                        <th>Mulai Kontrak</th>
                                        <th>Selesai Kontrak</th>
                                        <th>Kontrak Kerja</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            {{-- <div class="modal-footer">
                    <a class="button btn-primary"
                        href="{{ url('/masa_tenggang_kontrak/SPSCode_0b6ce327-1a26-4e25-91c8-9a48b3fae487') }}"></a>
        </div> --}}
        <div class="modal-footer">
            <!-- <a type="button" class="btn btn-primary"
                        href="{{ url('/masa_tenggang_kontrak/' . $holding->holding_code) }}">Lihat</a> -->
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#modal_add_pendidikan').modal('show');
        $('#habis-tab').on('click', function() {
            // console.log('asoy');
            $('#table_karyawan_bulanan_tenggang').DataTable().ajax.reload();
        });
        $('#atas-tab').on('click', function() {
            // console.log('asoy1');
            $('#table_karyawan_tenggang_atasan').DataTable().ajax.reload();
        });
        $('#tengah-tab').on('click', function() {
            // console.log($.fn.dataTable.isDataTable('#table_karyawan_tenggang_menengah'));
            $('#table_karyawan_tenggang_menengah').DataTable().ajax.reload();
        });
        var holding = '{{ $holding->holding_code }}';

        var table = $('#table_karyawan_bulanan_tenggang').DataTable({
            pageLength: 10,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_bulanan_tenggang-datatable') }}" + '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nomor_identitas_karyawan',
                    name: 'nomor_identitas_karyawan'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'mulai_kontrak',
                    name: 'mulai_kontrak'
                },
                {
                    data: 'selesai_kontrak',
                    name: 'selesai_kontrak'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
        var table1 = $('#table_karyawan_tenggang_atasan').DataTable({
            pageLength: 10,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_tenggang_atasan-datatable') }}" + '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nomor_identitas_karyawan',
                    name: 'nomor_identitas_karyawan'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'mulai_kontrak',
                    name: 'mulai_kontrak'
                },
                {
                    data: 'selesai_kontrak',
                    name: 'selesai_kontrak'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
        var table2 = $('#table_karyawan_tenggang_menengah').DataTable({
            pageLength: 10,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan_tenggang_menengah-datatable') }}" + '/' + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nomor_identitas_karyawan',
                    name: 'nomor_identitas_karyawan'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'mulai_kontrak',
                    name: 'mulai_kontrak'
                },
                {
                    data: 'selesai_kontrak',
                    name: 'selesai_kontrak'
                },
                {
                    data: 'kontrak_kerja',
                    name: 'kontrak_kerja'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
    });
</script>
@endsection