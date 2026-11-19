@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
@endsection
@section('isi')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 text-dark"><i class="mdi mdi-beach me-2"></i>DETAIL CUTI : {{ $karyawan->name }}
                                (TERPAKAI: {{ $kuota_awal->terpakai }})
                            </h5>
                            <input type="hidden" id="menu_id_add" value="">

                        </div>
                    </div>
                    <div class="card-body">

                        <hr class="my-3">
                        <input type="hidden" id="menu_id_add" value="">

                        <div id="table_detail_cuti_div">
                            <table class="table table-hover" id="table_detail_cuti"
                                style="width: 100%; font-size: smaller;">
                                <thead class="table-light">
                                    <tr class="text-start">
                                        <th>No.</th>
                                        <th>Legal&nbsp;Number</th>
                                        <th>Tanggal&nbsp;Terpakai</th>
                                        <th>Terpotong</th>
                                        <th>Dibatalkan</th>
                                        {{-- <th>Terpakai</th> --}}
                                        <th>Saldo</th>
                                    </tr>
                                    <tr class="text-start">
                                        <th>-</th>
                                        <th>SALDO AWAL</th>
                                        <th>-</th>
                                        <th>-</th>
                                        {{-- <th>-</th> --}}
                                        <th>-</th>
                                        <th>{{ $kuota_awal->kuota }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tbody>
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
            dom: 'Bfrtip',
            ajax: {
                url: "{{ url('/dt_detail_kuota_cuti') }}" + "/" + id_karyawan + "/" + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'LAPORAN DETAIL CUTI {{ $karyawan->name }}',
                messageTop: 'TERPAKAI: {{ $kuota_awal->terpakai }}',

                exportOptions: {
                    columns: ':not(:first-child)' // Lewati kolom No.
                },

                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                    // KARENA ADA TITLE & MESSAGETOP:
                    // Baris 1 = Title, Baris 2 = MessageTop.
                    // Maka, Header bawaan (baris saldo awal) otomatis berada di Baris 3.
                    var targetRow = $('row[r=3]', sheet);

                    // Buat element row baru khusus untuk ditempatkan di Baris 3 (Header Utama)
                    var newRowHtml = '<row r="3">';
                    newRowHtml += '<c t="inlineStr" r="A3" s="2"><is><t>Legal Number</t></is></c>';
                    newRowHtml += '<c t="inlineStr" r="B3" s="2"><is><t>Tanggal Terpakai</t></is></c>';
                    newRowHtml += '<c t="inlineStr" r="C3" s="2"><is><t>Terpotong</t></is></c>';
                    newRowHtml += '<c t="inlineStr" r="D3" s="2"><is><t>Dibatalkan</t></is></c>';
                    newRowHtml += '<c t="inlineStr" r="E3" s="2"><is><t>Saldo</t></is></c>';
                    newRowHtml += '</row>';

                    // Geser hanya baris ke-3 dan seterusnya ke bawah sebanyak 1 baris
                    // Baris 1 (Title) dan Baris 2 (MessageTop) TIDAK IKUT DIGESER agar tidak rusak
                    $('row', sheet).each(function() {
                        var r = parseInt($(this).attr('r'));

                        if (r >= 3) {
                            $(this).attr('r', r + 1);
                            $(this).find('c').each(function() {
                                var ref = $(this).attr('r');
                                var col = ref.replace(/[0-9]/g,
                                    ''); // Ambil huruf kolom (A, B, C, dst)
                                var row = parseInt(ref.replace(/\D/g,
                                    '')); // Ambil angka baris asli
                                $(this).attr('r', col + (row +
                                    1)); // Set koordinat baru (misal A3 jadi A4)
                            });
                        }
                    });

                    // Selipkan Header Utama tepat di atas baris ke-3 yang lama (yang sekarang sudah bergeser jadi baris 4)
                    targetRow.before(newRowHtml);
                },

                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'KUOTA_CUTI_KARYAWAN_' + l + '_' + n;
                },
            }],
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'no_form_cuti',
                    name: 'no_form_cuti'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'jumlah_hari',
                    name: 'jumlah_hari'
                },
                {
                    data: 'dibatalkan',
                    name: 'dibatalkan'
                },
                // {
                //     data: 'terpakai',
                //     name: 'terpakai'
                // },
                {
                    data: 'sisa',
                    name: 'sisa'
                },
            ],
            order: [
                [2, 'asc']
            ]
        });
    </script>
@endsection
