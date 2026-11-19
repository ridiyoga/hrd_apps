@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
        integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
        crossorigin="" />
    <style type="text/css">
        .leaflet-container {
            height: 400px;
            width: 600px;
            max-width: 100%;
            max-height: 100%;
        }
    </style>
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
                            <h5 class="card-title">DATA MASTER LOKASI</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <hr class="my-1">

                        <div class="modal fade" id="modal_lihat_lokasi" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="backDropModalTitle">Lihat Lokasi (MAPS)</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col-md-12">
                                            <div class="card mb-4">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                        <table>
                                                            <tr>
                                                                <th>Lokasi</th>
                                                                <td>&nbsp;</td>
                                                                <td>:</td>
                                                                <td id="nama_lokasi"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Site</th>
                                                                <td>&nbsp;</td>
                                                                <td>:</td>
                                                                <td id="nama_site"> </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Radius</th>
                                                                <td>&nbsp;</td>
                                                                <td>:</td>
                                                                <td id="radius_titik"></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="hidden" id="lat_titik" value="">
                                            <input type="hidden" id="long_titik" value="">
                                            <input type="hidden" id="radius" value="">
                                            <input type="hidden" id="lokasi_kantor" value="">
                                            <input type="hidden" id="nama_titik" value="">
                                            <div class="card mb-4">
                                                <div id="lihat_lokasi"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex master-table-toolbar">

                            <table class="table" id="table_lokasi2" style="width: 100%; font-size: small;">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama&nbsp;Lokasi</th>
                                        <th>Nama&nbsp;Site</th>
                                        <th>Lat&nbsp;Lokasi</th>
                                        <th>Long&nbsp;Lokasi</th>
                                        <th>Radius</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="master-table-area">
                        <table class="table" id="table_lokasi" style="width: 100%; font-size: small;">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama&nbsp;Lokasi</th>
                                    <th>Nama&nbsp;Site</th>
                                    <th>Lat&nbsp;Lokasi</th>
                                    <th>Long&nbsp;Lokasi</th>
                                    <th>Radius</th>
                                    <th>Maps</th>
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
        let holding = '{{ $holding->holding_code }}';
        var table = $('#table_lokasi').DataTable({
            scrollY: '200px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('/hrd/lokasi-datatable') }}@else{{ url('lokasi-datatable') }}@endif" +
                    '/' + holding,
            },
            columns: [{
                    data: "id_titik",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_lokasi',
                    name: 'nama_lokasi'
                },
                {
                    data: 'nama_site',
                    name: 'nama_site'
                },

                {
                    data: 'lat_lokasi',
                    name: 'lat_lokasi'
                },
                {
                    data: 'long_lokasi',
                    name: 'long_lokasi'
                },
                {
                    data: 'radius_lokasi',
                    name: 'radius_lokasi'
                },
                {
                    data: 'lihat_maps',
                    name: 'lihat_maps'
                },
            ],
            order: [1, 'asc'],
        });
        $('#table_lokasi2').hide();

        var table2 = $('#table_lokasi2').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            paginate: false,
            filter: false,
            info: false,
            dom: 'Bfrtip',
            ajax: {
                url: "@if (Auth::user()->is_admin == 'hrd'){{ url('/hrd/lokasi-datatable') }}@else{{ url('lokasi-datatable') }}@endif" +
                    '/' + holding,
            },
            buttons: [{
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-success',
                text: '<i class="menu-icon tf-icons mdi mdi-file-excel"></i>Excel',
                titleAttr: 'Excel',
                title: 'DAFTAR LOKASI',
                messageTop: '{{ $holding->holding_name }}',
                exportOptions: {
                    columns: ':not(:first-child)',
                },
                filename: function() {
                    var d = new Date();
                    var l = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
                    var n = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
                    return 'LOKASI_' + l + ' ' + n;
                },
            }],
            columns: [{
                    data: "id_titik",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nama_lokasi',
                    name: 'nama_lokasi'
                },
                {
                    data: 'nama_site',
                    name: 'nama_site'
                },

                {
                    data: 'lat_lokasi',
                    name: 'lat_lokasi'
                },
                {
                    data: 'long_lokasi',
                    name: 'long_lokasi'
                },
                {
                    data: 'radius_lokasi',
                    name: 'radius_lokasi'
                },
            ],
            order: [1, 'asc'],
        });
    </script>
    <script>
        $(document).on('click', '#btn_lokasi_saya', function() {
            getLocation();

            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else {
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }

            function showPosition(position) {
                $('#lat_kantor').val(position.coords.latitude);
                $('#long_kantor').val(position.coords.longitude);
                $('#lat_lokasi_update').val(position.coords.latitude);
                $('#long_lokasi_update').val(position.coords.longitude);
            }
        })
        $(document).on('click', '#btn_refresh_lokasi', function() {
            $('#lat_kantor').val('');
            $('#long_kantor').val('');
            $('#lat_lokasi_update').val('');
            $('#long_lokasi_update').val('');
        })

        $(document).on('click', '#btn_delete_lokasi', function() {
            var cek = $(this).data('id');
            var holding = $(this).data('holding');
            // console.log(holding);
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Kamu tidak dapat mengembalikan data ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "@if (Auth::user()->is_admin == 'hrd') {{ url('/hrd/lokasi/delete/') }} @else {{ url('lokasi/delete/') }} @endif" +
                            cek + "/" + holding,
                        type: "GET",
                        error: function() {
                            alert('Something is wrong');
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Data anda berhasil di hapus.',
                                icon: 'success',
                                timer: 1500
                            })
                            $('#table_lokasi').DataTable().ajax.reload();
                        }
                    });
                } else {
                    Swal.fire("Cancelled", "Your data is safe :)", "error");
                }
            });

        });
        $(document).on('click', '#btn_lihat_lokasi', function() {
            var lokasi = $(this).data('lokasi');
            var lat = $(this).data('lat');
            var long = $(this).data('long');
            var site = $(this).data('site');
            var radius = $(this).data('radius');
            $('#radius').val(radius);
            $('#lat_titik').val(lat);
            $('#long_titik').val(long);
            $('#lokasi_kantor').val(lokasi);
            $('#nama_lokasi').html(lokasi);
            $('#nama_site').html(site);
            $('#radius_titik').html(radius + ' M');
            $('#modal_lihat_lokasi').modal('show');
            maps_lokasi();
        });
        $(function() {
            $(document).on('change', '#site_update', function() {
                let value = $(this).val();
                let holding = '{{ $holding->id }}';
                // console.log(holding);
                $.ajax({
                    type: 'GET',
                    url: "@if (Auth::user()->is_admin == 'hrd'){{ url('hrd/lokasi/get_lokasi') }}/ @else {{ url('lokasi/get_lokasi') }}/ @endif" +
                        holding,
                    data: {
                        holding: holding,
                        value: value
                    },
                    cache: false,

                    success: function(response) {
                        var result = JSON.parse(response);
                        // console.log(result);
                        if (result != null) {
                            $('#site_name_update').val(result.site_name);
                            $('#lat_site_update').val(result.site_lat);
                            $('#long_site_update').val(result.site_long);
                            maps_edit_lokasi();
                        } else {
                            $('#tambah_lokasi').hide();
                        }
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },

                });
            });
            var typingTimer; //timer identifier
            var doneTypingInterval = 0;
            $(document).on('keyup', '#radius', function() {
                let value = $(this).val();
                let holding = '{{ $holding }}';
                // console.log(value);
                clearTimeout(typingTimer);
                typingTimer = setTimeout(maps_lokasi, doneTypingInterval);

            });
        })
    </script>
    <script>
        var map = null;

        function maps_lokasi() {
            var radius = $('#radius').val();
            var lat = $('#lat_titik').val();
            var long = $('#long_titik').val();
            var lokasi_kantor = $("#lokasi_kantor").val()
            var nama_titik = $("#nama_titik").val()
            // console.log(lokasi_kantor, ' & ', nama_titik);
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

            var circle = L.circle([lat, long], {
                color: 'purple',
                fillColor: 'purple',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);
            var marker = L.marker([lat, long]).addTo(map)
                .bindPopup(nama_titik).openPopup();
        }

        function maps_edit_lokasi() {
            if (map) {
                map.off();
                map.remove();
            }
            var long_site = $("#long_site_update").val();
            var lat_site = $("#lat_site_update").val()
            var long_lokasi = $("#long_lokasi_update").val();
            var lat_lokasi = $("#lat_lokasi_update").val()
            var lokasi = $("#nama_lokasi_update").val()
            var nama_titik = $("#nama_lokasi_update").val()
            var sitename = $("#site_name_update").val()

            map = L.map('lihat_edit_lokasi').fitWorld().setView([lat_site, long_site], 17);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 25,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);


            var popup = L.popup()
                .setLatLng([lat_site, long_site])
                .setContent(sitename)
                .openOn(map);
            // console.log(lokasi_kantor);
            // SP
            if (sitename == 'CV. SUMBER PANGAN - KEDIRI') {
                var latlngs = [
                    [-7.757852, 112.093890],
                    [-7.756964, 112.094195],
                    [-7.757866, 112.096507],
                    [-7.758657, 112.095336]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'CV. SUMBER PANGAN - TUBAN') {
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
            } else if (sitename == 'DEPO SP SIDOARJO') {
                var latlngs = [
                    [-7.361735, 112.784873],
                    [-7.361757, 112.785147],
                    [-7.362231, 112.785102],
                    [-7.362195, 112.784741]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SP SAMARINDA') {
                var latlngs = [
                    [-0.46124004439708466, 117.1890440835615],
                    [-0.4612392469974343, 117.18918363302389],
                    [-0.46134587505367874, 117.18918108680002],
                    [-0.4613312150395592, 117.18903673736563]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SP DENPASAR') {
                var latlngs = [
                    [-8.652895481207116, 115.20293696056507],
                    [-8.652912717125513, 115.2030294967747],
                    [-8.652755926596885, 115.20305008509402],
                    [-8.652733064463064, 115.2029671528421]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SP MALANG') {
                var latlngs = [
                    [-7.967760845267797, 112.65873922458452],
                    [-7.967798033683292, 112.65879957428648],
                    [-7.967823932756354, 112.65878616324159],
                    [-7.967790064737394, 112.65872983685311]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SP MALANG') {
                var latlngs = [
                    [-7.967760845267797, 112.65873922458452],
                    [-7.967798033683292, 112.65879957428648],
                    [-7.967823932756354, 112.65878616324159],
                    [-7.967790064737394, 112.65872983685311]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SP PALANGKARAYA') {
                var latlngs = [
                    [-2.1739101807413506, 113.864207945572],
                    [-2.1737446735313326, 113.86422269772137],
                    [-2.173735292555323, 113.86412814985499],
                    [-2.1739061603235093, 113.86411876212357]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SP SEMARANG') {
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
            } else if (sitename == 'PT. SURYA PANGAN SEMESTA - KEDIRI') {
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
            } else if (sitename == 'PT. SURYA PANGAN SEMESTA - NGAWI') {
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
            } else if (sitename == 'PT. SURYA PANGAN SEMESTA - SUBANG') {
                var latlngs = [
                    [-6.29533870949617, 107.90681938912391],
                    [-6.295727870479563, 107.90769375045888],
                    [-6.293953207394033, 107.9077779126219],
                    [-6.293911897422521, 107.9069474641808]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SPS BANDUNG') {
                var latlngs = [
                    [-6.887528841438018, 107.60032030611694],
                    [-6.887538161422427, 107.60048257975994],
                    [-6.887629364117361, 107.60047855644646],
                    [-6.887622041273895, 107.60032164722143]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'BULOG PARON - KEDIRI') {
                var latlngs = [
                    [-7.813968757527632, 112.05662997145677],
                    [-7.81236784846995, 112.05722929959332],
                    [-7.813095022711804, 112.05940470904986],
                    [-7.815163768273714, 112.0587276399426]
                ];
                var polygon = L.polygon(latlngs, {
                    color: 'red'
                }).addTo(map);
            } else if (sitename == 'DEPO SPS CIPINANG (JAKARTA)') {
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
            marker = L.marker([lat_lokasi, long_lokasi]).addTo(map)
                .bindPopup(nama_titik).openPopup();
            radius = $("#radius_update").val()
            circle = L.circle([lat_lokasi, long_lokasi], {
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
                $("#long_lokasi_update").val(longitude);
                $("#lat_lokasi_update").val(latitude);
                // map.removeLayer(circle1);
                // map.removeLayer(circle);
                var nama_titik = $("#nama_lokasi_update").val()
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
