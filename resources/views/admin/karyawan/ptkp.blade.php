@extends('admin.layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.min.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
        }
    </style>
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y" style="font-size: small;">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">DATA EXCEL KARYAWAN</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table" id="table_ptkp" style="width: 100%; font-size: smaller;">
                            <thead class="table-primary">
                                <tr>
                                    <th>NO.</th>
                                    <th>NIP Lengkap</th>
                                    <th>NIK</th>
                                    <th>HOLDING</th>
                                    <th>NAMA</th>
                                    <th>EMAIL</th>
                                    <th>NO. TELP</th>
                                    <th>TEMPAT LAHIR</th>
                                    <th>TANGGAL LAHIR</th>
                                    <th>STATUS PERNIKAHAN</th>
                                    <th>SHIFTING</th>
                                    <th>JABATAN</th>
                                    <th>DIVISI</th>
                                    <th>DEPARTEMEN</th>
                                    <th>BAGIAN</th>
                                    <th>PENEMPATAN KERJA</th>

                                    <th>TANGGAL MASUK</th>
                                    <th>MASA KERJA</th>
                                    <th>STATUS AKTIF</th>
                                    <th>GENDER</th>
                                    <th>NO. REKENING</th>
                                    <th>NAMA REKENING</th>
                                    <th>BANK</th>
                                    <th>RIWAYAT KONTRAK</th>
                                    <th>MASA AKTIF KONTRAK</th>
                                    <th>EXPIRED</th>
                                    <th>PENDIDIKAN TERAKHIR</th>
                                    <th>JURUSAN</th>
                                    <th>NAMA SEKOLAH</th>

                                    <th>SERTIFIKAT</th>
                                    <th>NOMOR KK</th>
                                    <th>TGL TERBIT KK</th>
                                    <th>NPWP</th>
                                    <th>JENIS PTKP</th>
                                    <th>NO. BPJS TK</th>
                                    <th>NO. BPJS KES</th>
                                    <th>ALAMAT</th>
                                    <th>DESA / KELURAHAN</th>
                                    <th>KECAMATAN</th>
                                    <th>KAB / KOTA</th>
                                    <th>PROVINSI</th>
                                    <th>KODE POS</th>
                                    <th>AGAMA</th>
                                    <th>SALDO CUTI</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script>
        var holding = '{{ $holding->holding_code }}';
        var table2 = $('#table_ptkp').DataTable({
            paging: false,
            scrollY: true,
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/karyawan/ptkp_datatable/') }}" + '/' + holding,
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                title: 'Database Karyawan {{ $holding->holding_name }}',
                className: 'btn btn-success btn-sm shadow-sm',
                exportOptions: {
                    columns: [
                        0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21,
                        22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41,
                        42
                    ]
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var textColumns = ['B', 'C', 'AE', 'AG', 'AI', 'AJ'];

                    textColumns.forEach(function(column) {
                        $('row c[r^="' + column + '"]', sheet).each(function() {
                            var cell = $(this);
                            var value = $('v', cell).text();

                            if (value !== '') {
                                cell.attr('t', 'inlineStr');
                                cell.empty();
                                cell.append('<is><t>' + $('<div>').text(value).html() +
                                    '</t></is>');
                            }
                        });
                    });
                }
            }],
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
                    data: 'nik',
                    name: 'nik'
                },
                {
                    data: 'holding',
                    name: 'holding'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'nomor_wa',
                    name: 'nomor_wa'
                },
                {
                    data: 'tempat_lahir',
                    name: 'tempat_lahir'
                },
                {
                    data: 'tgl_lahir',
                    name: 'tgl_lahir'
                },
                {
                    data: 'status_nikah',
                    name: 'status_nikah'
                },
                {
                    data: 'shift',
                    name: 'shift'
                },
                {
                    data: 'nama_jabatan',
                    name: 'nama_jabatan'
                },
                {
                    data: 'nama_divisi',
                    name: 'nama_divisi'
                },
                {
                    data: 'nama_departemen',
                    name: 'nama_departemen'
                },
                {
                    data: 'nama_bagian',
                    name: 'nama_bagian'
                },
                {
                    data: 'penempatan_kerja',
                    name: 'penempatan_kerja'
                },
                {
                    data: 'tgl_join',
                    name: 'tgl_join'
                },
                {
                    data: 'lama_kerja',
                    name: 'lama_kerja'
                },
                {
                    data: 'status_aktif_karyawan',
                    name: 'status_aktif_karyawan'
                },
                {
                    data: 'gender',
                    name: 'gender'
                },
                {
                    data: 'nomor_rekening',
                    name: 'nomor_rekening'
                },
                {
                    data: 'nama_pemilik_rekening',
                    name: 'nama_pemilik_rekening'
                },
                {
                    data: 'nama_bank',
                    name: 'nama_bank'
                },
                {
                    data: 'riwayat_kontrak',
                    name: 'riwayat_kontrak'
                },
                {
                    data: 'tanggal_berakhir',
                    name: 'tanggal_berakhir'
                },
                {
                    data: 'hari_expired',
                    name: 'hari_expired'
                },
                {
                    data: 'jenjang',
                    name: 'jenjang'
                },
                {
                    data: 'jurusan',
                    name: 'jurusan'
                },
                {
                    data: 'institusi',
                    name: 'institusi'
                },
                {
                    data: 'sertifikat',
                    name: 'sertifikat'
                },
                {
                    data: 'no_kk',
                    name: 'no_kk'
                },
                {
                    data: 'tgl_terbit_kk',
                    name: 'tgl_terbit_kk'
                },
                {
                    data: 'npwp',
                    name: 'npwp'
                },
                {
                    data: 'ptkp',
                    name: 'ptkp'
                },
                {
                    data: 'no_bpjs_ketenagakerjaan',
                    name: 'no_bpjs_ketenagakerjaan'
                },
                {
                    data: 'no_bpjs_kesehatan',
                    name: 'no_bpjs_kesehatan'
                },
                {
                    data: 'detail_alamat',
                    name: 'detail_alamat'
                },
                {
                    data: 'desa',
                    name: 'desa'
                },
                {
                    data: 'kecamatan',
                    name: 'kecamatan'
                },
                {
                    data: 'kabupaten',
                    name: 'kabupaten'
                },
                {
                    data: 'provinsi',
                    name: 'provinsi'
                },
                {
                    data: 'kode_pos',
                    name: 'kode_pos'
                },
                {
                    data: 'agama',
                    name: 'agama'
                },
                {
                    data: 'saldo_cuti',
                    name: 'saldo_cuti'
                },
            ],
            order: [
                [4, 'asc']
            ]
        });

        $(document).on('click', '#navs-pills-nonaktif-home_id', function() {
            // console.log(table2);
            table2.DataTable().ajax.reload();
        });
    </script>
@endsection
