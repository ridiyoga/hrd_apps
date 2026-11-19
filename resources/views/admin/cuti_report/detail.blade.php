@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
@endsection
@section('isi')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-beach me-2"></i>DETAIL CUTI
                            </h5>
                            <input type="hidden" id="menu_id_add" value="">

                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-3">
                        <div class="row g-1 w-75 mx-auto" style="font-size: 0.85rem;">

                            <div class="col-6">
                                <div class="row g-1">
                                    <div class="col-4 text-secondary py-0.5">NAMA</div>
                                    <div class="col-8 text-dark py-0.5">: <b>{{ $karyawan->name }}</b></div>

                                    <div class="col-4 text-secondary py-0.5">NO. LEGAL</div>
                                    <div class="col-8 text-dark py-0.5">: {{ $cuti->no_form_cuti }}</div>
                                    <div class="col-4 text-secondary py-0.5">ATASAN 1</div>
                                    <div class="col-8 text-dark py-0.5">: <b>{{ $cuti->nama_atasan }}</b></div>
                                    <div class="col-4 text-secondary py-0.5">DISETUJUI 1 PADA</div>
                                    <div class="col-8 text-dark py-0.5">: {{ $cuti->waktu_approve }}</div>
                                    <div class="col-4 text-secondary py-0.5">ATASAN 2</div>
                                    <div class="col-8 text-dark py-0.5">: <b>{{ $cuti->nama_atasan2 }}</b></div>
                                    <div class="col-4 text-secondary py-0.5">DISETUJUI 2 PADA</div>
                                    <div class="col-8 text-dark py-0.5">: {{ $cuti->waktu_approve2 }}</div>

                                </div>
                            </div>

                            <div class="col-6">
                                <div class="row g-1">
                                    <div class="col-4 text-secondary py-0.5">PENGGANTI</div>
                                    <div class="col-8 text-dark py-0.5">: <b>{{ $cuti->nama_karyawan_pengganti }}</b></div>
                                    @if ($cuti->kategori_cuti_id == null)
                                        <div class="col-4 text-secondary py-0.5">KUOTA</div>
                                        <div class="col-8 text-dark py-0.5">: {{ $cuti->kuota }}&nbsp;HARI</div>

                                        <div class="col-4 text-secondary py-0.5">TERPAKAI</div>
                                        <div class="col-8 text-dark py-0.5">: {{ $cuti->terpakai }}&nbsp;HARI</div>

                                        <div class="col-4 text-secondary py-0.5">SISA</div>
                                        <div class="col-8 text-dark py-0.5">: {{ $cuti->kuota - $cuti->terpakai }}&nbsp;HARI
                                        </div>
                                    @endif

                                    @if ($cuti->status_cuti == null)
                                        <div class="col-4 text-secondary py-0.5">STATUS</div>
                                        <div class="col-8 text-dark py-0.5">: <b>BELUM DISETUJUI</b></div>
                                    @elseif ($cuti->status_cuti == 1)
                                        <div class="col-4 text-secondary py-0.5">STATUS</div>
                                        <div class="col-8 text-dark py-0.5">: <b>ATASAN 1 SETUJU</b></div>
                                    @elseif ($cuti->status_cuti == 2)
                                        <div class="col-4 text-secondary py-0.5">STATUS</div>
                                        <div class="col-8 text-dark py-0.5">: <b>DISETUJUI</b></div>
                                        <div class="col-4 text-secondary py-0.5">PDF</div>
                                        <div class="col-8 text-dark py-0.5">: <a
                                                href="{{ url('/report/cetak_form_cuti_detail/' . $cuti->id) }}"
                                                class="btn btn-danger" target="_blank">
                                                <i class="fa-solid fa-file-pdf me-1"></i> PDF
                                            </a></div>
                                    @elseif ($cuti->status_cuti == 5)
                                        <div class="col-4 text-secondary py-0.5">STATUS</div>
                                        <div class="col-8 text-dark py-0.5">: <b>DITOLAK</b></div>
                                    @endif


                                </div>
                            </div>

                        </div>
                        <hr class="my-3">
                        <input type="hidden" id="menu_id_add" value="">

                        <div id="table_detail_cuti_div">
                            <table class="table table-hover" id="table_detail_cuti"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr class="text-start">
                                        <th>No.</th>
                                        <th>Nama&nbsp;Cuti</th>
                                        <th>Tanggal&nbsp;Cuti</th>
                                        <th>Status</th>
                                        <th>Diubah&nbsp;Oleh</th>
                                        <th>Diubah&nbsp;Pada</th>
                                        <th>Tanggal&nbsp;Original</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0 small">

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script>
        let holding = '{{ $holding->holding_code }}';
        let id_karyawan = '{{ $karyawan->id }}';
        let id_cuti = '{{ $cuti->id }}';

        $(document).ready(function() {
            var menu_id = $('#asoy').val();
            // console.log(menu_id);
            $('#menu_id_add').val(menu_id);

            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('menu_id', menu_id);
            // console.log(formData);

            $.ajax({
                type: "POST",

                url: "{{ url('/recruitment/update_menu') }}",
                data: formData,
                contentType: false,
                processData: false,
                error: function() {
                    alert('Something is wrong!');
                    // console.log(formData);
                },
            });
        })

        var table2 = $('#table_detail_cuti').DataTable({
            responsive: true,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            ordering: false,
            ajax: {
                url: "{{ url('/dt_detail_report_cuti') }}" + "/" + id_cuti + "/" + id_karyawan + "/" + holding,
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_cuti',
                    name: 'nama_cuti'
                },
                {
                    data: 'tgl_cuti',
                    name: 'tgl_cuti'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'updated_by_name',
                    name: 'updated_by_name'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: 'original',
                    name: 'original'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
    </script>
@endsection
