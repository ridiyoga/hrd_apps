@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y master-table-page">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="card master-table-card">
                    <div class="card-header master-report-toolbar">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title">DATA MASTER SITE</h5>
                            <input type="hidden" id="menu_id_add" name="menu_id">
                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-3">
                        <div class="d-flex master-table-toolbar">
                            <table class="table" id="table_site2" style="width: 100%; font-size: small;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Site</th>
                                        <th>Alamat&nbsp;Site</th>
                                        <th>Status</th>
                                        <th>Lokasi&nbsp;Maps</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_site" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Site</th>
                                    <th>Alamat&nbsp;Site</th>
                                    <th>Status</th>
                                    <th>Lokasi&nbsp;Maps</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Transactions -->
            <!--/ Data Tables -->
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
        integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
        crossorigin=""></script>


    <script>
        $(document).ready(function() {
            $('#nama_site_add').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
            $('#alamat_site_add').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
            $('#site_name_update').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
            $('#alamat_site_update').on('keyup', function() {
                let val = $(this).val().toUpperCase();
                $(this).val(val);
            });
        });
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
        let holding = '{{ $holding->holding_code }}';
        var url =
            "{{ url('/site-datatable') }}" + '/' + holding;
        // console.log(url, '|', holding);
        var table = $('#table_site').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
            },
            columns: [{
                    data: "id_site",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'site_name',
                    name: 'site_name'
                },
                {
                    data: 'site_alamat',
                    name: 'site_alamat'
                },
                {
                    data: 'site_status',
                    name: 'site_status'
                },
                {
                    data: 'lihat_maps',
                    name: 'lihat_maps'
                },
            ],
            order: [1, 'asc'],
        });
        $('#table_site2').hide();

        var table2 = $('#table_site2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: url,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'DAFTAR SITE',
                messageTop: '{{ $holding->holding_name }}',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'SITE_{{ $holding->holding_name }}_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id_site",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'site_name',
                    name: 'site_name'
                },
                {
                    data: 'site_alamat',
                    name: 'site_alamat'
                },
                {
                    data: 'site_status',
                    name: 'site_status'
                },
                {
                    data: 'lihat_maps_excel',
                    name: 'lihat_maps_excel'
                },
            ],
            order: [1, 'asc'],
        });
    </script>
    <script>
        var map = null;

        function maps_lokasi() {
            var radius = $('#radius').val();
            var lat = $('#lat_titik').val();
            var long = $('#long_titik').val();
            var lokasi_kantor = $("#lokasi_kantor").val()
            var nama_titik = $("#nama_titik").val()
            if (map) {
                map.off();
                map.remove();


            }
            // console.log(lokasi_kantor);
            map = L.map('lihat_lokasi').fitWorld().setView([lat, long], 17);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 25,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);
            $('#modal_lihat_lokasi').on('shown.bs.modal', function() {
                map.invalidateSize();
            });


            var popup = L.popup()
                .setLatLng([lat, long])
                .setContent(lokasi_kantor)
                .openOn(map);
            // SP
            if (lokasi_kantor == 'CV. SUMBER PANGAN - KEDIRI') {
                var latlngs = [
                    [-7.757852, 112.093890],
                    [-7.756964, 112.094195],
                    [-7.757866, 112.096507],
                    [-7.758657, 112.095336]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'CV. SUMBER PANGAN - TUBAN') {
                var latlngs = [
                    [-6.991758822037412, 112.12048943252134],
                    [-6.992285922956118, 112.12087444394012],
                    [-6.991649636772762, 112.12126324857486],
                    [-6.9918209446766015, 112.12162739730593],
                    [-6.99158186659566, 112.12182464453525],
                    [-6.991630811724543, 112.12207689339583],
                    [-6.988976733872493, 112.12301030070874],
                    [-6.988841110863623, 112.1225521606721],
                    [-6.988496578083082, 112.12262012506571],
                    [-6.988366830934185, 112.12224502050286],
                    [-6.988087592439392, 112.12137545996293],
                    [-6.98793810105542, 112.1214266105829],
                    [-6.987859124455924, 112.12116801578183],
                    [-6.988502219235255, 112.1209008958774],
                    [-6.988694019261298, 112.12132146764182],
                    [-6.989663035162432, 112.12098199978163],
                    [-6.9897194468028525, 112.12109850952719],
                    [-6.990145354468302, 112.12087117343832],
                    [-6.989959196198711, 112.12060689523501],
                    [-6.990190483734605, 112.12045628507613],
                    [-6.990653058462982, 112.12096779127609]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP SIDOARJO') {
                var latlngs = [
                    [-7.361735, 112.784873],
                    [-7.361757, 112.785147],
                    [-7.362231, 112.785102],
                    [-7.362195, 112.784741]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP SAMARINDA') {
                var latlngs = [
                    [-0.46124004439708466, 117.1890440835615],
                    [-0.4612392469974343, 117.18918363302389],
                    [-0.46134587505367874, 117.18918108680002],
                    [-0.4613312150395592, 117.18903673736563]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP DENPASAR') {
                var latlngs = [
                    [-8.652895481207116, 115.20293696056507],
                    [-8.652912717125513, 115.2030294967747],
                    [-8.652755926596885, 115.20305008509402],
                    [-8.652733064463064, 115.2029671528421]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP MALANG') {
                var latlngs = [
                    [-7.967760845267797, 112.65873922458452],
                    [-7.967798033683292, 112.65879957428648],
                    [-7.967823932756354, 112.65878616324159],
                    [-7.967790064737394, 112.65872983685311]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP PALANGKARAYA') {
                var latlngs = [
                    [-2.1739101807413506, 113.864207945572],
                    [-2.1737446735313326, 113.86422269772137],
                    [-2.173735292555323, 113.86412814985499],
                    [-2.1739061603235093, 113.86411876212357]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP SEMARANG') {
                var latlngs = [
                    [-6.99848157965858, 110.46462216952277],
                    [-6.998261280500614, 110.4646979419191],
                    [-6.998228668229718, 110.46460071185301],
                    [-6.998402378462714, 110.46454237381336]
                ];
                // SPS
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'BULOG PARON - KEDIRI') {
                var latlngs = [
                    [-7.813968757527632, 112.05662997145677],
                    [-7.81236784846995, 112.05722929959332],
                    [-7.813095022711804, 112.05940470904986],
                    [-7.815163768273714, 112.0587276399426]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'PT. SURYA PANGAN SEMESTA - KEDIRI') {
                var latlngs = [
                    [-7.811054254338505, 112.07984213086016],
                    [-7.810839096224432, 112.08081884380057],
                    [-7.808489981554889, 112.08161649876598],
                    [-7.808405068773745, 112.08133682173685],
                    [-7.810097668835231, 112.08055007648335],
                    [-7.810057948477162, 112.08030628208806]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'PT. SURYA PANGAN SEMESTA - NGAWI') {
                var latlngs = [
                    [-7.503903124866787, 111.42901333909559],
                    [-7.503780799880943, 111.42583760362271],
                    [-7.505630060242543, 111.4257993236654],
                    [-7.505712281925328, 111.4285105703631],
                    [-7.504871090128984, 111.4285169497671],
                    [-7.504637074058243, 111.42896350806065]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'PT. SURYA PANGAN SEMESTA - SUBANG') {
                var latlngs = [
                    [-6.29533870949617, 107.90681938912391],
                    [-6.295727870479563, 107.90769375045888],
                    [-6.293953207394033, 107.9077779126219],
                    [-6.293911897422521, 107.9069474641808]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SPS BANDUNG') {
                var latlngs = [
                    [-6.887528841438018, 107.60032030611694],
                    [-6.887538161422427, 107.60048257975994],
                    [-6.887629364117361, 107.60047855644646],
                    [-6.887622041273895, 107.60032164722143]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SPS CIPINANG (JAKARTA)') {
                var latlngs = [
                    [-6.21311187156196, 106.88544203302257],
                    [-6.2120446956529545, 106.88543065337363],
                    [-6.212025840935464, 106.88472511513999],
                    [-6.213168435595694, 106.88476684051939]
                ];
                var latlngs1 = [
                    [-6.211847347299506, 106.8808114012799],
                    [-6.211852680220818, 106.88181991185459],
                    [-6.212351308125068, 106.88182795848152],
                    [-6.212327310001449, 106.88079799023502]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
                var polygon1 = L.polygon(latlngs1, {
                    color: 'red'
                }).addTo(map);

            }

            // console.log(circle);

            // console.log(marker);
            // function onMapClick(e) {
            // }
            // map.on('click', onMapClick);

            // var circle = L.circle([lat, long], {
            //     color: 'purple',
            //     fillColor: 'purple',
            //     fillOpacity: 0.5,
            //     radius: radius
            // }).addTo(map);
            // var marker = L.marker([lat, long]).addTo(map)
            //     .bindPopup(nama_titik).openPopup();
        }

        function maps_edit_lokasi() {
            if (map) {
                map.off();
                map.remove();
            }
            var long_titik = $("#long_kantor_update").val();
            var lat_titik = $("#lat_kantor_update").val()
            var lokasi_kantor = $("#lokasi_kantor_update").val()
            var nama_titik = $("#nama_titik_update").val()

            map = L.map('lihat_edit_lokasi').fitWorld().setView([lat_titik, long_titik], 17);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 25,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);


            var popup = L.popup()
                .setLatLng([lat_titik, long_titik])
                .setContent(lokasi_kantor)
                .openOn(map);
            // console.log(lokasi_kantor);
            // SP
            if (lokasi_kantor == 'CV. SUMBER PANGAN - KEDIRI') {
                var latlngs = [
                    [-7.757852, 112.093890],
                    [-7.756964, 112.094195],
                    [-7.757866, 112.096507],
                    [-7.758657, 112.095336]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'CV. SUMBER PANGAN - TUBAN') {
                var latlngs = [
                    [-6.991758822037412, 112.12048943252134],
                    [-6.992285922956118, 112.12087444394012],
                    [-6.991649636772762, 112.12126324857486],
                    [-6.9918209446766015, 112.12162739730593],
                    [-6.99158186659566, 112.12182464453525],
                    [-6.991630811724543, 112.12207689339583],
                    [-6.988976733872493, 112.12301030070874],
                    [-6.988841110863623, 112.1225521606721],
                    [-6.988496578083082, 112.12262012506571],
                    [-6.988366830934185, 112.12224502050286],
                    [-6.988087592439392, 112.12137545996293],
                    [-6.98793810105542, 112.1214266105829],
                    [-6.987859124455924, 112.12116801578183],
                    [-6.988502219235255, 112.1209008958774],
                    [-6.988694019261298, 112.12132146764182],
                    [-6.989663035162432, 112.12098199978163],
                    [-6.9897194468028525, 112.12109850952719],
                    [-6.990145354468302, 112.12087117343832],
                    [-6.989959196198711, 112.12060689523501],
                    [-6.990190483734605, 112.12045628507613],
                    [-6.990653058462982, 112.12096779127609]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP SIDOARJO') {
                var latlngs = [
                    [-7.361735, 112.784873],
                    [-7.361757, 112.785147],
                    [-7.362231, 112.785102],
                    [-7.362195, 112.784741]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP SAMARINDA') {
                var latlngs = [
                    [-0.46124004439708466, 117.1890440835615],
                    [-0.4612392469974343, 117.18918363302389],
                    [-0.46134587505367874, 117.18918108680002],
                    [-0.4613312150395592, 117.18903673736563]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP DENPASAR') {
                var latlngs = [
                    [-8.652895481207116, 115.20293696056507],
                    [-8.652912717125513, 115.2030294967747],
                    [-8.652755926596885, 115.20305008509402],
                    [-8.652733064463064, 115.2029671528421]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP MALANG') {
                var latlngs = [
                    [-7.967760845267797, 112.65873922458452],
                    [-7.967798033683292, 112.65879957428648],
                    [-7.967823932756354, 112.65878616324159],
                    [-7.967790064737394, 112.65872983685311]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP MALANG') {
                var latlngs = [
                    [-7.967760845267797, 112.65873922458452],
                    [-7.967798033683292, 112.65879957428648],
                    [-7.967823932756354, 112.65878616324159],
                    [-7.967790064737394, 112.65872983685311]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP PALANGKARAYA') {
                var latlngs = [
                    [-2.1739101807413506, 113.864207945572],
                    [-2.1737446735313326, 113.86422269772137],
                    [-2.173735292555323, 113.86412814985499],
                    [-2.1739061603235093, 113.86411876212357]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SP SEMARANG') {
                var latlngs = [
                    [-6.99848157965858, 110.46462216952277],
                    [-6.998261280500614, 110.4646979419191],
                    [-6.998228668229718, 110.46460071185301],
                    [-6.998402378462714, 110.46454237381336]
                ];
                // SPS
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'PT. SURYA PANGAN SEMESTA - KEDIRI') {
                var latlngs = [
                    [-7.811054254338505, 112.07984213086016],
                    [-7.810839096224432, 112.08081884380057],
                    [-7.808489981554889, 112.08161649876598],
                    [-7.808405068773745, 112.08133682173685],
                    [-7.810097668835231, 112.08055007648335],
                    [-7.810057948477162, 112.08030628208806]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'PT. SURYA PANGAN SEMESTA - NGAWI') {
                var latlngs = [
                    [-7.503903124866787, 111.42901333909559],
                    [-7.503780799880943, 111.42583760362271],
                    [-7.505630060242543, 111.4257993236654],
                    [-7.505712281925328, 111.4285105703631],
                    [-7.504871090128984, 111.4285169497671],
                    [-7.504637074058243, 111.42896350806065]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'PT. SURYA PANGAN SEMESTA - SUBANG') {
                var latlngs = [
                    [-6.29533870949617, 107.90681938912391],
                    [-6.295727870479563, 107.90769375045888],
                    [-6.293953207394033, 107.9077779126219],
                    [-6.293911897422521, 107.9069474641808]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SPS BANDUNG') {
                var latlngs = [
                    [-6.887528841438018, 107.60032030611694],
                    [-6.887538161422427, 107.60048257975994],
                    [-6.887629364117361, 107.60047855644646],
                    [-6.887622041273895, 107.60032164722143]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'BULOG PARON - KEDIRI') {
                var latlngs = [
                    [-7.813968757527632, 112.05662997145677],
                    [-7.81236784846995, 112.05722929959332],
                    [-7.813095022711804, 112.05940470904986],
                    [-7.815163768273714, 112.0587276399426]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (lokasi_kantor == 'DEPO SPS CIPINANG (JAKARTA)') {
                var latlngs = [
                    [-6.21311187156196, 106.88544203302257],
                    [-6.2120446956529545, 106.88543065337363],
                    [-6.212025840935464, 106.88472511513999],
                    [-6.213168435595694, 106.88476684051939]
                ];
                var latlngs1 = [
                    [-6.211847347299506, 106.8808114012799],
                    [-6.211852680220818, 106.88181991185459],
                    [-6.212351308125068, 106.88182795848152],
                    [-6.212327310001449, 106.88079799023502]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
                var polygon1 = L.polygon(latlngs1, {
                    color: 'red'
                }).addTo(map);

            }
            $('#modal_edit_lokasi').on('shown.bs.modal', function() {
                map.invalidateSize();
            });

            // console.log(circle);

            // function onMapClick(e) {
            // }
            // map.on('click', onMapClick);
            var marker = null;
            var circle = null;
            marker = L.marker([lat_titik, long_titik]).addTo(map)
                .bindPopup(nama_titik).openPopup();
            radius = $("#radius_update").val()
            circle = L.circle([lat_titik, long_titik], {
                color: 'purple',
                fillColor: 'purple',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
            map.on('click', function(e) {
                popup
                    .setLatLng(e.latlng)
                    .setContent('You clicked the map at ' + e.latlng.toString())
                    .openOn(map);
                let latitude = e.latlng.lat.toString().substring(0, 15);
                let longitude = e.latlng.lng.toString().substring(0, 15);
                // console.log(longitude);
                $("#long_kantor_update").val(longitude);
                $("#lat_kantor_update").val(latitude);
                // map.removeLayer(circle1);
                // map.removeLayer(circle);
                var nama_titik = $("#nama_titik_update").val()
                // console.log(nama_titik);
                if (marker !== null) {
                    map.removeLayer(marker);
                }
                marker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup(nama_titik).openPopup();
                if (circle !== null) {
                    map.removeLayer(circle);
                }
                var radius = $("#radius_update").val()
                circle = L.circle([latitude, longitude], {
                    color: 'purple',
                    fillColor: 'purple',
                    fillOpacity: 0.5,
                    radius: radius
                }).addTo(map);
                // map.removeLayer(L.circle([latitude, longitude]));
            });
        }
    </script>
@endsection
