<script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        var menu_id = $('#asoy').val();
        // console.log(menu_id);
        $('#menu_id_add').val(menu_id);
        $("input[type=text]").keyup(function() {
            $(this).val($(this).val().toUpperCase());
        });
        // simpan posisi sebelum reload
        window.addEventListener("beforeunload", function() {
            localStorage.setItem("scrollPos", window.scrollY);
        });

        // balikin posisi setelah reload
        window.addEventListener("load", function() {
            let scrollPos = localStorage.getItem("scrollPos");
            if (scrollPos) {
                window.scrollTo(0, scrollPos);
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            // simpan tab terakhir yang dibuka
            document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tab) {
                tab.addEventListener("shown.bs.tab", function(e) {
                    localStorage.setItem("activeTab", e.target.getAttribute(
                        "data-bs-target"));
                });
            });

            // balikin tab terakhir setelah reload
            let activeTab = localStorage.getItem("activeTab");
            if (activeTab) {
                let tabTriggerEl = document.querySelector(`button[data-bs-target="${activeTab}"]`);
                if (tabTriggerEl) {
                    new bootstrap.Tab(tabTriggerEl).show();

                    // kalau di tab restore ada input, baru kasih fokus di sini
                    setTimeout(() => {
                        let input = document.querySelector(`${activeTab} input`);
                        if (input) input.focus();
                    }, 300);
                }
            }
        });
        $('#id_provinsi').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Provinsi",
            allowClear: true
        });

        $('#id_kabupaten').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kabupaten",
            allowClear: true
        });
        $('#id_kecamatan').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kecamatan",
            allowClear: true
        });
        $('#id_desa').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Desa",
            allowClear: true
        });
        $('#id_provinsi_domisili').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Provinsi Domisili",
            allowClear: true
        });
        $('#id_kabupaten_domisili').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kabupaten Domisili",
            allowClear: true
        });
        $('#id_kecamatan_domisili').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kecamatan Domisili",
            allowClear: true
        });
        $('#id_desa_domisili').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Desa Domisili",
            allowClear: true
        });
        $('#penempatan_holding').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Penempatan Holding",
            allowClear: true
        });
        $('#penempatan_kerja_add').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Penempatan Kerja",
            allowClear: true
        });
        $('#id_departemen').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Departemen",
            allowClear: true
        });
        $('#id_divisi').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Divisi",
            allowClear: true
        });
        $('#id_jabatan').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Jabatan",
            allowClear: true
        });
        $('#id_bagian').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Bagian",
            allowClear: true
        });


        $(document).on('click', '#pendidikan_nav', function() {
            // console.log('asoy');
            $('#table_pendidikan').DataTable().ajax.reload();
        });
        $(document).on('click', '#keahlian_nav', function() {
            // console.log('asoy');
            $('#table_keahlian').DataTable().ajax.reload();
        });
        $(document).on('click', '#pengalaman_nav', function() {
            // console.log('asoy');
            $('#tabel_riwayat').DataTable().ajax.reload();
        });
        $(document).on('click', '#jabatan2_nav', function() {
            // console.log('asoy');
            $('#tabel_jabatan').DataTable().ajax.reload();
        });
        $(document).on('click', '#info_hr_nav', function() {
            // console.log('asoy');
            $('#tabel_kontrak').DataTable().ajax.reload();
            $('#table_cuti_diluar_tahunan').DataTable().ajax.reload();
        });
        $(document).on('click', '#pills-khusus-tab', function() {
            // console.log('asoy');
            $('#tabel_kontrak').DataTable().ajax.reload();
            $('#table_cuti_diluar_tahunan').DataTable().ajax.reload();
        });
        // BUTTON FOTO
        var foto_karyawan = '{{ $karyawan->foto_karyawan }}';
        if (foto_karyawan == null) {
            $('#group-button-foto').empty();
            $('#group-button-foto').append(
                '<button type="button" id="btn_edit_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit Foto</span></button>'
            );
        } else {
            $('#group-button-foto').empty();
            $('#group-button-foto').append(
                '<button type="button" id="btn_edit_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit Foto</span></button>'
            );

        }
        let karyawan_name = '{{ $karyawan->name }}';
        $(document).on('click', '#btn_edit_foto', function() {
            $('#foto_karyawan').click();
        });
        $('#foto_karyawan').change(function() {
            var file = this.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#template_foto_karyawan').attr('src', e.target.result);
            };
            $('#group-button-foto')
                .empty()
                .append(
                    '<button type="button" id="btn_edit_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit Foto</span></button>'
                )
                .append(
                    '<button type="button" id="btn_reset_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-reload text-danger"></i><span class="text-danger">&nbsp;Reset</span></button>'
                );
            reader.readAsDataURL(file);
        });
        $(document).on('click', '#btn_reset_foto', function() {
            $('#foto_karyawan').val('');
            $('#template_foto_karyawan').attr("src",
                "{{ asset('storage/foto_karyawan/default_profil.jpg') }}");
            $('#group-button-foto')
                .empty()
                .append(
                    '<button type="button" id="btn_edit_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit Foto</span></button>'
                );
        });
        $('#foto_karyawan').change(function() {
            var file = this.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#template_foto_karyawan').attr('src', e.target.result);
            };
            $('#group-button-foto')
                .empty()
                .append(
                    '<button type="button" id="btn_edit_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit Foto</span></button>'
                )
                .append(
                    '<button type="button" id="btn_reset_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-reload text-danger"></i><span class="text-danger">&nbsp;Reset</span></button>'
                );
            reader.readAsDataURL(file);
        });
        $(document).on('click', '#btn_reset_foto', function() {
            $('#foto_karyawan').val('');
            $('#template_foto_karyawan').attr("src",
                "{{ asset('storage/foto_karyawan/default_profil.jpg') }}");
            $('#group-button-foto')
                .empty()
                .append(
                    '<button type="button" id="btn_edit_foto" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit Foto</span></button>'
                );
        });


        $(document).on('click', '#btn_reset_ktp', function() {
            $('#ktp').val('');
            $('#template_ktp').attr('src', "{{ asset('storage/ktp/default_ktp.jpg') }}");
            $('#group-button-ktp')
                .empty()
                .append(
                    '<button type="button" id="btn_edit_ktp" class="btn btn-sm me-2 mb-3"><i class="mdi mdi-pencil-outline text-primary"></i><span class="text-primary">&nbsp;Edit KTP</span></button>'
                );
        });
        $(document).on('change', '#ktp', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // 1. Isi Data Preview
                    $('#img_preview_thumb').attr('src', e.target.result);
                    $('#new_file_name').text('KTP_' + karyawan_name);

                    // 2. Manipulasi UI (Sembunyikan yang lama, munculkan yang baru)
                    $('#ktp_info_row, #ktp_empty_row, #btn_lihat_ktp').addClass('d-none');
                    $('#ktp_preview_row, #btn_cancel_upload').removeClass('d-none').hide().fadeIn(
                        300);

                    // Ubah gaya tombol upload jadi sekedar icon refresh
                    $('#label_ktp').removeClass('btn-primary w-100').addClass(
                        'btn-outline-secondary').find('span').text('');
                    $('#label_ktp').find('i').removeClass('mdi-cloud-upload-outline me-1').addClass(
                        'mdi-refresh');
                }
                reader.readAsDataURL(file);
            }
        });
        $(document).on('change', '#kk', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // 1. Isi Data Preview
                    $('#img_preview_thumb_kk').attr('src', e.target.result);
                    $('#new_file_name_kk').text('KK_' + karyawan_name);

                    // 2. Manipulasi UI (Sembunyikan yang lama, munculkan yang baru)
                    $('#kk_info_row, #kk_empty_row, #btn_lihat_kk').addClass('d-none');
                    $('#kk_preview_row, #btn_cancel_upload_kk').removeClass('d-none').hide().fadeIn(
                        300);

                    // Ubah gaya tombol upload jadi sekedar icon refresh
                    $('#label_kk').removeClass('btn-primary w-100').addClass(
                        'btn-outline-secondary').find('span').text('');
                    $('#label_kk').find('i').removeClass('mdi-cloud-upload-outline me-1').addClass(
                        'mdi-refresh');
                }
                reader.readAsDataURL(file);
            }
        });
        // Batalkan Pilihan File Baru
        $(document).on('click', '#btn_cancel_upload', function() {
            $("#ktp").val(''); // Clear input
            $('#ktp_preview_row, #btn_cancel_upload').addClass('d-none');

            // Kembalikan ke kondisi semula
            if ($('#ktp_info_row').length > 0) {
                $('#ktp_info_row, #btn_lihat_ktp').removeClass('d-none');
            } else {
                $('#ktp_empty_row').removeClass('d-none');
                $('#label_ktp').addClass('btn-primary w-100').find('span').text('Upload KTP');
                $('#label_ktp').find('i').addClass('mdi-cloud-upload-outline me-1').removeClass(
                    'mdi-refresh');
            }
        });
        $(document).on('click', '#btn_cancel_upload_kk', function() {
            $("#kk").val(''); // Clear input
            $('#kk_preview_row, #btn_cancel_upload_kk').addClass('d-none');

            // Kembalikan ke kondisi semula
            if ($('#kk_info_row').length > 0) {
                $('#kk_info_row, #btn_lihat_kk').removeClass('d-none');
            } else {
                $('#kk_empty_row').removeClass('d-none');
                $('#label_kk').addClass('btn-primary w-100').find('span').text('Upload KK');
                $('#label_kk').find('i').addClass('mdi-cloud-upload-outline me-1').removeClass(
                    'mdi-refresh');
            }
        });
        view_dokumen_datadiri();

        function view_dokumen_datadiri() {
            var dokumen_datadiri = '';
            $.ajax({
                url: "{{ url('karyawan/dokumen_datadiri') }}" + "/" + '{{ $karyawan->id }}',
                method: "GET",
                dataType: "JSON",
                success: function(data) {
                    // console.log(data);
                    var base_url_ktp = "{{ asset('storage/ktp') }}";
                    var base_name_ktp = "{{ basename('KTP_') }}";
                    var base_url_kk = "{{ asset('storage/kk') }}";
                    var base_name_kk = "{{ basename('KK_') }}";
                    if (data.code = 200) {
                        $('#berkas_karyawan').empty();
                        if (data.data.ktp != null) {
                            var ktp_complete = `<div id="ktp_info_row" class="p-2 border border-success rounded bg-label-success mb-2 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center overflow-hidden">
                                                        <img id="img_preview_ktp" src="${base_url_ktp}/${data.data.ktp}" class="rounded" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #666cff;">
                                                        <small class="text-truncate text-success fw-bold" style="max-width: 150px;">${ base_name_ktp + data.data.name }</small>
                                                        <input type="hidden" id="ktp_old" name="ktp_old" value="${ data.data.ktp }">
                                                    </div>
                                                    <small class="text-xs text-success fw-medium">Tersimpan</small>
                                                </div>`;
                            var status_ktp = ``;
                            var color_ktp = `btn-outline-secondary`;
                            var icon_ktp = `mdi-refresh`;
                            var text_ktp = ``;
                        } else {
                            var ktp_complete = `<div id="ktp_empty_row" class="p-2 border border-dashed rounded mb-2 d-flex align-items-center justify-content-center bg-light">
                            <small class="text-muted italic"><i class="mdi mdi-information-outline"></i> Belum ada lampiran KTP</small>
                            </div>`;
                            var status_ktp = `d-none`;
                            var color_ktp = `btn-primary w-100`;
                            var icon_ktp = `mdi-cloud-upload-outline me-1`;
                            var text_ktp = `Unggah KTP`;
                        }
                        if (data.data.kk != null) {
                            var kk_complete = `<div id="kk_info_row" class="p-2 border border-success rounded bg-label-success mb-2 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center overflow-hidden">
                                                        <img id="img_preview_kk" src="${base_url_kk}/${data.data.kk}" class="rounded" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #666cff;">
                                                        <small class="text-truncate text-success fw-bold" style="max-width: 150px;">${ base_name_kk + data.data.name }</small>
                                                        <input type="hidden" id="kk_old" name="kk_old" value="${ data.data.kk }">
                                                    </div>
                                                    <small class="text-xs text-success fw-medium">Tersimpan</small>
                                                </div>`;
                            var status_kk = ``;
                            var color_kk = `btn-outline-secondary`;
                            var icon_kk = `mdi-refresh`;
                            var text_kk = ``;
                        } else {
                            var kk_complete = `<div id="kk_empty_row" class="p-2 border border-dashed rounded mb-2 d-flex align-items-center justify-content-center bg-light">
                            <small class="text-muted italic"><i class="mdi mdi-information-outline"></i> Belum ada lampiran KK</small>
                            </div>`;
                            var status_kk = `d-none`;
                            var color_kk = `btn-primary w-100`;
                            var icon_kk = `mdi-cloud-upload-outline me-1`;
                            var text_kk = `Unggah KK`;
                        }
                        dokumen_datadiri += `<div class="row g-4">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="document-card border rounded p-3 h-100 d-flex flex-column shadow-none">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <div class="avatar-initial bg-label-info rounded me-3">
                                                            <i class="mdi mdi-id-card mdi-24px"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold text-dark">KTP</h6>
                                                                    <small class="text-muted">Kartu Tanda Penduduk</small>
                                                                </div>
                                                                <span class="badge badge-dot bg-dot-success" data-bs-toggle="tooltip" title="Berkas Tersedia"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-auto">
                                                        <div id="ktp_wrapper">
                                                            ${ktp_complete}
                                                            {{-- MODE 2: PREVIEW FILE BARU (Hidden by default) --}}
                                                            <div id="ktp_preview_row" class="p-2 border border-primary rounded bg-label-primary mb-2 d-flex align-items-center justify-content-between d-none">
                                                                <div class="d-flex align-items-center overflow-hidden">
                                                                    <div id="preview_thumb_container" class="me-2">
                                                                        <img id="img_preview_thumb" src="" class="rounded" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #666cff;">
                                                                    </div>
                                                                    <small id="new_file_name" class="text-truncate text-primary fw-bold" style="max-width: 150px;"></small>
                                                                </div>
                                                                <small class="text-xs text-primary fw-medium anim-pulse" id="siap_simpan_ktp">Siap Simpan</small>
                                                            </div>

                                                           
                                                        </div>

                                                        <div class="d-flex gap-2">
                                                            {{-- Tombol Lihat (Hanya muncul jika sudah ada file di server) --}}
                                                            <a href="${base_url_ktp}/${data.data.ktp}"
                                                                id="btn_lihat_ktp"
                                                                target="_blank"
                                                                class="btn btn-primary btn-sm flex-grow-1 shadow-sm ${status_ktp}">
                                                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Berkas
                                                            </a>

                                                            {{-- Tombol Upload / Ganti --}}
                                                            <label for="ktp" class="btn btn-sm ${color_ktp} mb-0" id="label_ktp">
                                                                <i class="mdi ${icon_ktp}"></i>
                                                                <span>${text_ktp}</span>
                                                                <input type="file" hidden name="ktp" id="ktp" accept="image/png, image/jpeg">
                                                            </label>

                                                            {{-- Tombol Cancel (Hanya muncul saat preview baru) --}}
                                                            <button type="button" id="btn_cancel_upload" class="btn btn-sm btn-outline-danger d-none">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="document-card border rounded p-3 h-100 d-flex flex-column shadow-none">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <div class="avatar-initial bg-label-info rounded me-3">
                                                            <i class="mdi mdi-id-card mdi-24px"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold text-dark">KK</h6>
                                                                    <small class="text-muted">Kartu Keluarga</small>
                                                                </div>
                                                                <span class="badge badge-dot bg-dot-success" data-bs-toggle="tooltip" title="Berkas Tersedia"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-auto">
                                                        <div id="kk_wrapper">
                                                           
                                                            ${kk_complete}
                                                            {{-- MODE 2: PREVIEW FILE BARU (Hidden by default) --}}
                                                            <div id="kk_preview_row" class="p-2 border border-primary rounded bg-label-primary mb-2 d-flex align-items-center justify-content-between d-none">
                                                                <div class="d-flex align-items-center overflow-hidden">
                                                                    <div id="preview_thumb_container" class="me-2">
                                                                        <img id="img_preview_thumb_kk" src="" class="rounded" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #666cff;">
                                                                    </div>
                                                                    <small id="new_file_name_kk" class="text-truncate text-primary fw-bold" style="max-width: 150px;"></small>
                                                                </div>
                                                                <small class="text-xs text-primary fw-medium anim-pulse" id="siap_simpan_kk">Siap Simpan</small>
                                                            </div>

                                                        </div>

                                                        <div class="d-flex gap-2">
                                                            {{-- Tombol Lihat (Hanya muncul jika sudah ada file di server) --}}
                                                            <a href="${base_url_kk}/${data.data.kk}"
                                                                id="btn_lihat_kk"
                                                                target="_blank"
                                                                class="btn btn-primary btn-sm flex-grow-1 shadow-sm ${status_kk}">
                                                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Berkas
                                                            </a>

                                                            {{-- Tombol Upload / Ganti --}}
                                                            <label for="kk" class="btn btn-sm ${color_kk} mb-0" id="label_kk">
                                                                <i class="mdi ${icon_kk}"></i>
                                                                <span>${text_kk}</span>
                                                                <input type="file" hidden name="kk" id="kk" accept="image/png, image/jpeg">
                                                            </label>

                                                            {{-- Tombol Cancel (Hanya muncul saat preview baru) --}}
                                                            <button type="button" id="btn_cancel_upload_kk" class="btn btn-sm btn-outline-danger d-none">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                        $('#berkas_karyawan').html(dokumen_datadiri);
                    } else {
                        $('#berkas_karyawan').html(
                            '<span class="text-danger fw-bold text-center">Data tidak ditemukan</span>'
                        );
                    }
                }
            })
        }

        view_dokumen_pendidikan();

        function view_dokumen_pendidikan() {
            var dokumen_pendidikan = '';
            $.ajax({
                url: "{{ url('karyawan/dokumen_datadiri') }}" + "/" + '{{ $karyawan->id }}',
                method: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    var base_url_ijazah = "{{ asset('storage/ijazah') }}";
                    var base_name_ijazah = "{{ basename('IJAZAH_') }}";
                    var base_url_transkrip_nilai = "{{ asset('storage/transkrip_nilai') }}";
                    var base_name_transkrip_nilai = "{{ basename('TRANSKRIP_') }}";
                    if (data.code = 200) {
                        $('#berkas_pendidikan_karyawan').empty();
                        if (data.data.ijazah != null) {
                            var ijazah_complete = `<div id="ijazah_info_row" class="p-2 border border-success rounded bg-label-success mb-2 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center overflow-hidden">
                                                        <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                                        <small class="text-truncate text-success fw-bold" style="max-width: 150px;">${ base_name_ijazah + data.data.name }</small>
                                                        </div>
                                                        <small class="text-xs text-success fw-medium">Tersimpan</small>
                                                        </div>`;
                            var status_ijazah = ``;
                            var color_ijazah = `btn-outline-secondary`;
                            var icon_ijazah = `mdi-refresh`;
                            var text_ijazah = ``;
                        } else {
                            var ijazah_complete = `<div id="ijazah_empty_row" class="p-2 border border-dashed rounded mb-2 d-flex align-items-center justify-content-center bg-light">
                            <small class="text-muted italic"><i class="mdi mdi-information-outline"></i> Belum ada lampiran Scan Ijazah</small>
                            </div>`;
                            var status_ijazah = `d-none`;
                            var color_ijazah = `btn-primary w-100`;
                            var icon_ijazah = `mdi-cloud-upload-outline me-1`;
                            var text_ijazah = `Unggah Ijazah`;
                        }
                        if (data.data.transkrip_nilai != null) {
                            var transkrip_nilai_complete = `<div id="transkrip_nilai_info_row" class="p-2 border border-success rounded bg-label-success mb-2 d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center overflow-hidden">
                                                        <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                                        <small class="text-truncate text-success fw-bold" style="max-width: 150px;">${ base_name_transkrip_nilai + data.data.name }</small>
                                                        </div>
                                                        <small class="text-xs text-success fw-medium">Tersimpan</small>
                                                        </div>`;
                            var status_transkrip_nilai = ``;
                            var color_transkrip_nilai = `btn-outline-secondary`;
                            var icon_transkrip_nilai = `mdi-refresh`;
                            var text_transkrip_nilai = ``;
                        } else {
                            var transkrip_nilai_complete = `<div id="transkrip_nilai_empty_row" class="p-2 border border-dashed rounded mb-2 d-flex align-items-center justify-content-center bg-light">
                                                        <small class="text-muted italic"><i class="mdi mdi-information-outline"></i> Belum ada lampiran Scan Transkrip Nilai</small>
                                                        </div>`;
                            var status_transkrip_nilai = `d-none`;
                            var color_transkrip_nilai = `btn-primary w-100`;
                            var icon_transkrip_nilai = `mdi-cloud-upload-outline me-1`;
                            var text_transkrip_nilai = `Unggah Transkrip Nilai`;
                        }
                        dokumen_pendidikan += `<div class="row g-4">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="document-card border rounded p-3 h-100 d-flex flex-column shadow-none">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <div class="avatar-initial bg-label-info rounded me-3">
                                                            <i class="mdi mdi-id-card mdi-24px"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                            <h6 class="mb-0 fw-bold text-dark">IJAZAH</h6>
                                                            <small class="text-muted">Upload Scan Lampiran Ijazah</small>
                                                            </div>
                                                            <span class="badge badge-dot bg-dot-success" data-bs-toggle="tooltip" title="Berkas Tersedia"></span>
                                                            </div>
                                                            </div>
                                                            </div>
                                                            
                                                            <div class="mt-auto">
                                                            <div id="ijazah_wrapper">
                                                            ${ijazah_complete}
                                                            {{-- MODE 2: PREVIEW FILE BARU (Hidden by default) --}}
                                                            <div id="ijazah_preview_row" class="p-2 border border-primary rounded bg-label-primary mb-2 d-flex align-items-center justify-content-between d-none">
                                                            <div class="d-flex align-items-center overflow-hidden">
                                                            <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                                            <small id="new_file_name_ijazah" class="text-truncate text-primary fw-bold" style="max-width: 150px;"></small>
                                                                    </div>
                                                                    <small class="text-xs text-primary fw-medium anim-pulse" id="siap_simpan_ijazah">Siap Simpan</small>
                                                                </div>
                                                                </div>

                                                        <div class="d-flex gap-2">
                                                            {{-- Tombol Lihat (Hanya muncul jika sudah ada file di server) --}}
                                                            <a href="${base_url_ijazah}/${data.data.ijazah}"
                                                            id="btn_lihat_ijazah"
                                                            target="_blank"
                                                            class="btn btn-primary btn-sm flex-grow-1 shadow-sm ${status_ijazah}">
                                                            <i class="mdi mdi-eye-outline me-1"></i> Lihat Berkas
                                                            </a>
                                                            
                                                            {{-- Tombol Upload / Ganti --}}
                                                            <label for="file_ijazah" class="btn btn-sm ${color_ijazah} mb-0" id="label_ijazah">
                                                            <i class="mdi ${icon_ijazah}"></i>
                                                            <span>${text_ijazah}</span>
                                                            <input type="file" hidden name="file_ijazah" id="file_ijazah" accept="application/pdf" value=""> 
                                                            <input type="hidden" id="file_ijazah_old" name="file_ijazah_old" value="${ data.data.ijazah }">
                                                                </label>
                                                                
                                                            {{-- Tombol Cancel (Hanya muncul saat preview baru) --}}
                                                            <button type="button" id="btn_cancel_upload_ijazah" class="btn btn-sm btn-outline-danger d-none">
                                                                <i class="mdi mdi-close"></i>
                                                                </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6">
                                                <div class="document-card border rounded p-3 h-100 d-flex flex-column shadow-none">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <div class="avatar-initial bg-label-info rounded me-3">
                                                            <i class="mdi mdi-id-card mdi-24px"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold text-dark">TRANSKIP NILAI</h6>
                                                                    <small class="text-muted">Upload Scan Lampiran Transkrip Nilai</small>
                                                                </div>
                                                                <span class="badge badge-dot bg-dot-success" data-bs-toggle="tooltip" title="Berkas Tersedia"></span>
                                                            </div>
                                                            </div>
                                                    </div>

                                                    <div class="mt-auto">
                                                        <div id="transkrip_nilai_wrapper">
                                                        
                                                            ${transkrip_nilai_complete}
                                                            {{-- MODE 2: PREVIEW FILE BARU (Hidden by default) --}}
                                                            <div id="transkrip_nilai_preview_row" class="p-2 border border-primary rounded bg-label-primary mb-2 d-flex align-items-center justify-content-between d-none">
                                                            <div class="d-flex align-items-center overflow-hidden">
                                                                   <i class="mdi mdi-file-pdf-box mdi-24px me-2 text-danger"></i>
                                                                    <small id="new_file_name_transkrip_nilai" class="text-truncate text-primary fw-bold" style="max-width: 150px;"></small>
                                                                    </div>
                                                                    <small class="text-xs text-primary fw-medium anim-pulse" id="siap_simpan_transkrip_nilai">Siap Simpan</small>
                                                                    </div>
                                                                    
                                                                    </div>
                                                                    
                                                        <div class="d-flex gap-2">
                                                            {{-- Tombol Lihat (Hanya muncul jika sudah ada file di server) --}}
                                                            <a href="${base_url_transkrip_nilai}/${data.data.transkrip_nilai}"
                                                                id="btn_lihat_transkrip_nilai"
                                                                target="_blank"
                                                                class="btn btn-primary btn-sm flex-grow-1 shadow-sm ${status_transkrip_nilai}">
                                                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Berkas
                                                                </a>
                                                                
                                                                {{-- Tombol Upload / Ganti --}}
                                                                <label for="file_transkrip_nilai" class="btn btn-sm ${color_transkrip_nilai} mb-0" id="label_transkrip_nilai">
                                                                <i class="mdi ${icon_transkrip_nilai}"></i>
                                                                <span>${text_transkrip_nilai}</span>
                                                                <input type="file" hidden name="file_transkrip_nilai" id="file_transkrip_nilai" accept="application/pdf" value="">
                                                                <input type="hidden" id="transkrip_nilai_old" name="transkrip_nilai_old" value="${ data.data.transkrip_nilai }">
                                                                </label>
                                                                
                                                                {{-- Tombol Cancel (Hanya muncul saat preview baru) --}}
                                                                <button type="button" id="btn_cancel_upload_transkrip_nilai" class="btn btn-sm btn-outline-danger d-none">
                                                                <i class="mdi mdi-close"></i>
                                                                </button>
                                                                </div>
                                                                </div>
                                                    </div>
                                                    </div>
                                        </div>`;
                        $('#berkas_pendidikan_karyawan').html(dokumen_pendidikan);
                    } else {
                        $('#berkas_pendidikan_karyawan').html(
                            '<span class="text-danger fw-bold text-center">Data tidak ditemukan</span>'
                        );
                    }
                }
            })
        }
        $(document).on('change', '#file_ijazah', function() {
            const file = this.files[0];
            // console.log(file.type);
            if (file) {
                // Validasi PDF
                if (file.type !== "application/pdf") {
                    Swal.fire('Oops!', 'Format file harus PDF', 'error');
                    $(this).val('');
                    return;
                }
                $('#new_file_name_ijazah').text('IJAZAH_' + karyawan_name);
                // Tampilkan Nama File & Ubah UI
                $('#ijazah_info_row, #ijazah_empty_row, #btn_lihat_ijazah').addClass('d-none');
                $('#ijazah_preview_row, #btn_cancel_upload_ijazah').removeClass('d-none').hide().fadeIn(
                    300);

                // Ubah gaya tombol
                const label = $('#label_ijazah');
                label.removeClass('btn-danger btn-primary w-100').addClass('btn-outline-secondary')
                    .find('span').text('');
                label.find('i').removeClass('mdi-cloud-upload-outline me-1').addClass('mdi-refresh');
            }
        });
        $(document).on('change', '#file_transkrip_nilai', function() {
            const file = this.files[0];
            // console.log(file.type);
            if (file) {
                // Validasi PDF
                if (file.type !== "application/pdf") {
                    Swal.fire('Oops!', 'Format file harus PDF', 'error');
                    $(this).val('');
                    return;
                }
                $('#new_file_name_transkrip_nilai').text('TRANSKIP NILAI_' + karyawan_name);
                // Tampilkan Nama File & Ubah UI
                $('#transkrip_nilai_info_row, #transkrip_nilai_empty_row, #btn_lihat_transkrip_nilai')
                    .addClass('d-none');
                $('#transkrip_nilai_preview_row, #btn_cancel_upload_transkrip_nilai').removeClass(
                    'd-none').hide().fadeIn(300);

                // Ubah gaya tombol
                const label = $('#label_transkrip_nilai');
                label.removeClass('btn-danger btn-primary w-100').addClass('btn-outline-secondary')
                    .find('span').text('');
                label.find('i').removeClass('mdi-cloud-upload-outline me-1').addClass('mdi-refresh');
            }
        });
        $(document).on('click', '#btn_cancel_upload_ijazah', function() {
            $("#file_ijazah").val(''); // Clear input
            $('#ijazah_preview_row, #btn_cancel_upload_ijazah').addClass('d-none');

            // Kembalikan ke kondisi semula
            if ($('#ijazah_info_row').length > 0) {
                $('#ijazah_info_row, #btn_lihat_ijazah').removeClass('d-none');
            } else {
                $('#ijazah_empty_row').removeClass('d-none');
                $('#label_ijazah').addClass('btn-primary w-100').find('span').text('Upload Ijazah');
                $('#label_ijazah').find('i').addClass('mdi-cloud-upload-outline me-1').removeClass(
                    'mdi-refresh');
            }
        });
        $(document).on('click', '#btn_cancel_upload_transkrip_nilai', function() {
            $("#file_transkrip_nilai").val(''); // Clear input
            $('#transkrip_nilai_preview_row, #btn_cancel_upload_transkrip_nilai').addClass('d-none');

            // Kembalikan ke kondisi semula
            if ($('#transkrip_nilai_info_row').length > 0) {
                $('#transkrip_nilai_info_row, #btn_lihat_transkrip_nilai').removeClass('d-none');
            } else {
                $('#transkrip_nilai_empty_row').removeClass('d-none');
                $('#label_transkrip_nilai').addClass('btn-primary w-100').find('span').text(
                    'Upload Transkrip Nilai');
                $('#label_transkrip_nilai').find('i').addClass('mdi-cloud-upload-outline me-1')
                    .removeClass('mdi-refresh');
            }
        });
        // BUTTON PENDIDIKAN
        $('#pesan_disabled_pendidikan').hide();
        $.ajax({
            type: "GET",
            url: "{{ url('/karyawan/pendidikan/button_pendidikan/' . $karyawan->id) }}",
            error: function(error) {
                Swal.fire({
                    title: 'error',
                    text: error.responseJSON.message,
                    icon: 'error',
                    timer: 4500
                })

            },
            success: function(data_pendidikan) {
                // console.log(data_pendidikan);
                if (data_pendidikan.data_pendidikan >= 3) {
                    $('#pesan_disabled_pendidikan').show();
                    $('#btn_tambah_pendidikan').attr("disabled", true);

                }
            }
        });
        $('#btn_tambah_pendidikan').click(function() {
            $(this).find(':focus').blur(); // lepas focus dari elemen apapun di modal
            $('#nama_instansi').val('');
            $('#jurusan').val('');
            $('#jenjang').val('');
            $('#tahun_masuk').val('');
            $('#tahun_keluar').val('');
            $('#modal_add_pendidikan').modal('show');
        });
        $('#pesan_disabled_keahlian').hide();
        $.ajax({
            type: "GET",
            url: "{{ url('/karyawan/button_keahlian/' . $karyawan->id) }}",
            error: function(error) {
                Swal.fire({
                    title: 'error',
                    text: error.responseJSON.message,
                    icon: 'error',
                    timer: 4500
                })

            },
            success: function(data_keahlian) {
                // console.log(data_keahlian);
                if (data_keahlian.data_keahlian >= 5) {
                    $('#pesan_disabled_keahlian').show();
                    $('#btn_tambah_keahlian').attr("disabled", true);

                }
            }
        });
        $('#btn_tambah_keahlian').click(function() {
            $(this).find(':focus').blur(); // lepas focus dari elemen apapun di modal
            $('#nama_keahlian').val('');
            $('#file_keahlian').val('');
            $('.group-button-keahlian').empty();
            $('#modal_add_keahlian').modal('show');
        });
        var table_pendidikan = $('#table_pendidikan').DataTable({
            pageLength: 50,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan/pendidikan/' . $karyawan->id) }}",
            },
            columns: [{
                    data: "aksi",
                    name: "aksi"
                },
                {
                    data: "id",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'institusi',
                    name: 'institusi'
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
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'tanggal_keluar',
                    name: 'tanggal_keluar'
                }
            ],
            order: [
                [2, 'asc']
            ]
        });
        $('#nav_pendidikan').on('shown.bs.tab', function(e) {
            table_pendidikan.columns.adjust().draw().responsive.recalc();
            // table.draw();
        });
        var table_inventaris = $('#table_inventaris').DataTable({
            pageLength: 20,
            scrollY: '500px',
            scrollCollapse: true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/karyawan/inventaris/' . $karyawan->id . '/' . $holding->id) }}",
            },
            columns: [{
                    data: "id",

                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                // {
                //     data: 'option',
                //     name: 'option'
                // },
                {
                    data: 'nama_inventaris',
                    name: 'nama_inventaris'
                },
                {
                    data: 'foto',
                    name: 'foto'
                },
                {
                    data: 'serial_number',
                    name: 'serial_number'
                },
                {
                    data: 'id_aset',
                    name: 'id_aset'
                },
                {
                    data: 'nama_kategori',
                    name: 'nama_kategori'
                },
                {
                    data: 'nama_holding',
                    name: 'nama_holding'
                },
                {
                    data: 'nama_site',
                    name: 'nama_site'
                },
                {
                    data: 'nama_gedung',
                    name: 'nama_gedung'
                },
                {
                    data: 'nama_ruangan',
                    name: 'nama_ruangan'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'nama_kondisi',
                    name: 'nama_kondisi'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'nama_karyawan',
                    name: 'nama_karyawan'
                },
            ],
            order: [
                [1, 'asc'],
            ],
        });
        $('#inventaris_nav').on('shown.bs.tab', function() {
            table_inventaris.columns.adjust().ajax.reload();
        });
        var table1 = $('#table_keahlian').DataTable({
            pageLength: 50,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan/keahlian/' . $karyawan->id) }}",
            },
            columns: [{
                    data: "aksi",
                    name: "aksi"
                },
                {
                    data: "id",
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'keahlian',
                    name: 'keahlian'
                },
                {
                    data: 'file',
                    name: 'file'
                }
            ],
            order: [
                [2, 'asc']
            ]
        });


        // riwayat
        $('#pesan_disabled').hide();
        $.ajax({
            type: "GET",
            url: "{{ url('karyawan/button_riwayat/' . $karyawan->id) }}",
            error: function(error) {
                Swal.fire({
                    title: 'error',
                    text: error.responseJSON.message,
                    icon: 'error',
                    timer: 4500
                })
            },
            success: function(data_riwayat) {
                // console.log(data_riwayat);
                if (data_riwayat.data_riwayat >= 3) {
                    $('#pesan_disabled').show();
                    $('#btn_tambah_riwayat').attr("disabled", true);

                }
            }
        });
        var table2 = $('#tabel_riwayat').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan/riwayat/' . $karyawan->id) }}",
            },
            columns: [{
                    data: 'option',
                    name: 'option'
                },
                {
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan'
                },
                {
                    data: 'alamat_perusahaan',
                    name: 'alamat_perusahaan'
                },
                {
                    data: 'posisi',
                    name: 'posisi'
                }, {
                    data: 'gaji',
                    name: 'gaji'
                },
                {
                    data: 'tanggal_masuk',
                    name: 'tanggal_masuk'
                },
                {
                    data: 'tanggal_keluar',
                    name: 'tanggal_keluar'
                },
                {
                    data: 'alasan_keluar',
                    name: 'alasan_keluar'
                },
                {
                    data: 'surat_keterangan',
                    name: 'surat_keterangan'
                },
                {
                    data: 'nomor_referensi',
                    name: 'nomor_referensi'
                },
                {
                    data: 'jabatan_referensi',
                    name: 'jabatan_referensi'
                }

            ],
        });
        $('#nav_riwayat').on('shown.bs.tab', function(e) {
            table2.columns.adjust().draw().responsive.recalc();
            // table.draw();
        });
        $('#btn_tambah_riwayat').click(function() {
            $('#modal_tambah_riwayat').modal('show');
        });
        $(document).on('keyup', '#gaji_add', function(e) {
            var data = $(this).val();
            var hasil = formatRupiah(data, "Rp. ");
            $(this).val(hasil);
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
        }

        function replace_titik(x) {
            return ((x.replace('.', '')).replace('.', '')).replace('.', '');
        }
        $('#btn_save_riwayat').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan_add').val());
            formData.append('nama_perusahaan', $('#nama_perusahaan_add').val());
            formData.append('alamat_perusahaan', $('#alamat_perusahaan_add').val());
            formData.append('posisi', $('#posisi_add').val());
            formData.append('gaji', replace_titik($('#gaji_add').val()));
            formData.append('tanggal_masuk', $('#tanggal_masuk_add').val());
            formData.append('tanggal_keluar', $('#tanggal_keluar_add').val());
            formData.append('alasan_keluar', $('#alasan_keluar_add').val());
            formData.append('nomor_referensi', $('#nomor_referensi_add').val());
            formData.append('jabatan_referensi', $('#jabatan_referensi_add').val());

            var fileInput = $('#surat_keterangan_add')[0];
            if (fileInput.files.length > 0) {
                formData.append('surat_keterangan', fileInput.files[0]);
            }
            // post
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/riwayat_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#modal_tambah_riwayat').modal('hide');
                        $('#nama_perusahaan_add').val('');
                        $('#alamat_perusahaan_add').val('');
                        $('#posisi_add').val('');
                        $('#gaji_add').val('');
                        $('#tanggal_masuk_add').val('');
                        $('#tanggal_keluar_add').val('');
                        $('#alasan_keluar_add').val('');
                        $('#surat_keterangan_add').val('');
                        $('#nomor_referensi_add').val('');
                        $('#jabatan_referensi_add').val('');
                        $('#tabel_riwayat').DataTable().ajax.reload();
                        if (data.data_riwayat >= 3) {
                            $('#btn_tambah_riwayat').attr("disabled", true);
                            $('#pesan_disabled').show();
                        }
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        $(document).on('click', '#btn_edit_riwayat', function() {
            var id_riwayat = $(this).data('id_riwayat');
            var nama_perusahaan = $(this).data('nama_perusahaan');
            var alamat_perusahaan = $(this).data('alamat_perusahaan');
            var posisi = $(this).data('posisi');
            var gaji = $(this).data('gaji');
            var tanggal_masuk = $(this).data('tanggal_masuk');
            var tanggal_keluar = $(this).data('tanggal_keluar');
            var alasan_keluar = $(this).data('alasan_keluar');
            var nomor_referensi = $(this).data('nomor_referensi');
            var jabatan_referensi = $(this).data('jabatan_referensi');
            var old_file = $(this).data('old_file');
            $('#id_riwayat_update').val(id_riwayat);
            $('#nama_perusahaan_update').val(nama_perusahaan);
            $('#alamat_perusahaan_update').val(alamat_perusahaan);
            $('#posisi_update').val(posisi);
            $('#gaji_update').val(gaji);
            $('#tanggal_masuk_update').val(tanggal_masuk);
            $('#tanggal_keluar_update').val(tanggal_keluar);
            $('#alasan_keluar_update').val(alasan_keluar);
            $('#nomor_referensi_update').val(nomor_referensi);
            $('#jabatan_referensi_update').val(jabatan_referensi);
            $('#old_file_update').val(old_file);
            $('#modal_edit_riwayat').modal('show');

        });
        $(document).on('keyup', '#gaji_update', function(e) {
            var data = $(this).val();
            var hasil = formatRupiah(data, "Rp. ");
            $(this).val(hasil);
        });
        $('#btn_update_riwayat').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_riwayat', $('#id_riwayat_update').val());
            formData.append('nama_perusahaan', $('#nama_perusahaan_update').val());
            formData.append('alamat_perusahaan', $('#alamat_perusahaan_update').val());
            formData.append('posisi', $('#posisi_update').val());
            formData.append('gaji', replace_titik($('#gaji_update').val()));
            formData.append('tanggal_masuk', $('#tanggal_masuk_update').val());
            formData.append('tanggal_keluar', $('#tanggal_keluar_update').val());
            formData.append('alasan_keluar', $('#alasan_keluar_update').val());
            formData.append('nomor_referensi', $('#nomor_referensi_update').val());
            formData.append('jabatan_referensi', $('#jabatan_referensi_update').val());
            formData.append('old_file', $('#old_file_update').val());
            var fileInput = $('#surat_keterangan_update')[0];
            if (fileInput.files.length > 0) {
                formData.append('surat_keterangan', fileInput.files[0]);
            }
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/riwayat_update') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#nama_perusahaan_update').val('');
                        $('#alamat_perusahaan_update').val('');
                        $('#posisi_update').val('');
                        $('#gaji_update').val('');
                        $('#tanggal_masuk_update').val('');
                        $('#tanggal_keluar_update').val('');
                        $('#alasan_keluar_update').val('');
                        $('#surat_keterangan_update').val('');
                        $('#nomor_referensi_update').val('');
                        $('#jabatan_referensi_update').val('');
                        $('#modal_edit_riwayat').modal('hide');
                        $('#tabel_riwayat').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on('click', '#btn_delete_riwayat', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id_riwayat = $(this).data('id_riwayat');
            var surat_keterangan = $(this).data('surat_keterangan');
            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id_riwayat);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_riwayat: id_riwayat,
                            surat_keterangan: surat_keterangan,
                        },
                        url: "{{ url('karyawan/delete_riwayat/' . $karyawan->id) }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#tabel_riwayat').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                                // console.log(data.data_riwayat);
                                if (data.data_riwayat <= 3) {
                                    $('#btn_tambah_riwayat')
                                        .attr(
                                            "disabled", false);
                                    $('#pesan_disabled').hide();
                                }
                            } else {
                                $('#tabel_riwayat').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'error',
                                    text: data.error,
                                    icon: 'error',
                                    timer: 6500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        // end riwayat

        // jabatan
        $('#holding_id_add').select2({
            theme: 'bootstrap-5',
            // placeholder: "Pilih Penempatan Holding",
            allowClear: true,
            dropdownParent: $('#modal_tambah_jabatan')
        });
        $('#departemen_id_add').select2({
            theme: 'bootstrap-5',
            // placeholder: "Pilih Departemen",
            allowClear: true,
            dropdownParent: $('#modal_tambah_jabatan')

        });
        $('#divisi_id_add').select2({
            theme: 'bootstrap-5',
            // placeholder: "Pilih Divisi",
            allowClear: true,
            dropdownParent: $('#modal_tambah_jabatan')

        });
        $('#jabatan_id_add').select2({
            theme: 'bootstrap-5',
            // placeholder: "Pilih Jabatan",
            allowClear: true,
            dropdownParent: $('#modal_tambah_jabatan')

        });
        $('#bagian_id_add').select2({
            theme: 'bootstrap-5',
            // placeholder: "Pilih Bagian",
            allowClear: true,
            dropdownParent: $('#modal_tambah_jabatan')
        });

        $('#holding_id_add').on('change', function() {
            showSelectLoading('#departemen_id_add');
            showSelectLoading('#divisi_id_add');
            showSelectLoading('#bagian_id_add');
            showSelectLoading('#jabatan_id_add');
            let holding_id = $(this).val();
            let url = "{{ url('karyawan/jabatan/get_departemen') }}" + "/" + holding_id;
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,

                success: function(response) {
                    hideSelectLoading('#departemen_id_add');
                    Swal.close();
                    $('#departemen_id_add').html(response);
                },
                error: function(data) {
                    hideSelectLoading('#departemen_id_add');
                    hideSelectLoading('#divisi_id_add');
                    hideSelectLoading('#bagian_id_add');
                    hideSelectLoading('#jabatan_id_add');
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memuat data departemen',
                        icon: 'error',
                        timer: 30000
                    });
                },

            })
        })
        $('#departemen_id_add').on('change', function() {
            showSelectLoading('#divisi_id_add');
            showSelectLoading('#bagian_id_add');
            showSelectLoading('#jabatan_id_add');
            let departemen_id = $(this).val();
            let url = "{{ url('karyawan/jabatan/get_divisi') }}" + "/" + departemen_id;
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,

                success: function(response) {
                    hideSelectLoading('#divisi_id_add');
                    Swal.close();

                    $('#divisi_id_add').html(response);
                },
                error: function(data) {
                    hideSelectLoading('#divisi_id_add');
                    hideSelectLoading('#bagian_id_add');
                    hideSelectLoading('#jabatan_id_add');
                    Swal.close();

                    Swal.fire({
                        title: 'Gagal',
                        text: data.responseJSON.message ||
                            'Terjadi kesalahan saat memuat data divisi',
                        icon: 'error',
                    });
                },

            })
        })
        $('#divisi_id_add').on('change', function() {
            showSelectLoading('#bagian_id_add');
            showSelectLoading('#jabatan_id_add');
            let divisi_id = $(this).val();
            let url = "{{ url('karyawan/jabatan/get_bagian') }}" + "/" + divisi_id;
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,

                success: function(response) {
                    hideSelectLoading('#bagian_id_add');
                    Swal.close();

                    $('#bagian_id_add').html(response);
                },
                error: function(data) {
                    hideSelectLoading('#bagian_id_add');
                    hideSelectLoading('#jabatan_id_add');
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal',
                        text: data.responseJSON.message ||
                            'Terjadi kesalahan saat memuat data bagian',
                        icon: 'error',
                        timer: 30000
                    });
                },

            })
        })
        $('#bagian_id_add').on('change', function() {
            showSelectLoading('#jabatan_id_add');
            let bagian_id = $(this).val();
            let url = "{{ url('karyawan/jabatan/get_jabatan') }}" + "/" + bagian_id;
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,

                success: function(response) {
                    hideSelectLoading('#jabatan_id_add');

                    Swal.close();

                    $('#jabatan_id_add').html(response);
                },
                error: function(data) {
                    hideSelectLoading('#jabatan_id_add');
                    Swal.close();

                    Swal.fire({
                        title: 'Gagal',
                        text: data.responseJSON.message ||
                            'Terjadi kesalahan saat memuat data jabatan',
                        icon: 'error',
                        timer: 30000
                    });
                },

            })
        })
        var tableJ = $('#tabel_jabatan').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan/jabatan/' . $karyawan->id) }}",
            },
            columns: [{
                    data: 'option',
                    name: 'option'
                },
                {
                    data: 'holding',
                    name: 'holding'
                },
                {
                    data: 'departemen',
                    name: 'departemen'
                },
                {
                    data: 'divisi',
                    name: 'divisi'
                },
                {
                    data: 'bagian',
                    name: 'bagian'
                },
                {
                    data: 'jabatan',
                    name: 'jabatan'
                },
                {
                    data: 'main',
                    name: 'main'
                },
                {
                    data: 'is_active',
                    name: 'is_active'
                },
            ],
        });
        $('#btn_tambah_jabatan').click(function() {
            $('#modal_tambah_jabatan').modal('show');
        });
        $('#btn_save_jabatan').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan').val());
            formData.append('holding_id', $('#holding_id_add').val());
            formData.append('departemen_id', $('#departemen_id_add').val());
            formData.append('divisi_id', $('#divisi_id_add').val());
            formData.append('bagian_id', $('#bagian_id_add').val());
            formData.append('jabatan_id', $('#jabatan_id_add').val());
            formData.append('main', $('#main_add').val());
            formData.append('menu_id', $('#menu_id_add').val());

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/jabatan_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#modal_tambah_jabatan').modal('hide');
                        $('#jabatan_form')[0].reset();
                        $('.select-jabatan').val(null).trigger('change');
                        $('#tabel_jabatan').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else if (data.code == 600) {
                        Swal.fire({
                            text: data.message,
                            icon: 'warning',
                            timer: 4500
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        $(document).on('click', '#btn_edit_jabatan', function() {
            var id_jbt_karyawan = $(this).data('id_jbt_karyawan');
            var is_active = $(this).data('is_active');
            var main = $(this).data('main');
            $('#id_jbt_karyawan_update').val(id_jbt_karyawan);
            $('#is_active_update').val(is_active);
            $('#main_update').val(main);
            $('#modal_edit_jabatan').modal('show');

        });
        $('#btn_update_jabatan').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_jbt_karyawan', $('#id_jbt_karyawan_update').val());
            formData.append('is_active', $('#is_active_update').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // formData.append('main', $('#main_update').val());
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/jabatan_update') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#jabatan_update_form').val('')
                        $('#modal_edit_jabatan').modal('hide');
                        $('#tabel_jabatan').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });

                    } else if (data.code == 600) {
                        Swal.fire({
                            text: data.message,
                            icon: 'warning',
                            timer: 4500
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on('click', '#btn_delete_jabatan', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id_jbt_karyawan = $(this).data('id_jbt_karyawan');
            var menu_id = $('#menu_id_add').val();
            // console.log(menu_id);
            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini? Menghapus data ini akan menghapus semua data approval karyawan dengan jabatan ini",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id_jbt_karyawan);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_jbt_karyawan: id_jbt_karyawan,
                            menu_id: menu_id
                        },
                        url: "{{ url('/karyawan/jabatan_delete/') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#tabel_jabatan').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            } else {
                                $('#tabel_jabatan').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'error',
                                    text: data.error,
                                    icon: 'error',
                                    timer: 6500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        $('#btn_edit_kategori').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan').val());
            formData.append('kategori', $('#kategori_add').val());
            formData.append('shift', $('#shift_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // post
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/kategori_jabatan_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        // end jabatan

        // info HR dan kontrak
        $('#btn_update_hr').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan').val());
            formData.append('penempatan_kerja', $('#penempatan_kerja_add').val());
            formData.append('kontrak_kerja', $('#kontrak_kerja_add').val());
            formData.append('kuota_cuti', $('#kuota_cuti_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // post
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/hr_jabatan_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        var karyawan_id = "{{ $karyawan->id }}"
        var holding_id = "{{ $holding->id }}"
        var tablecuti = $('#table_cuti_diluar_tahunan').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan/kategori_cuti_datatable') }}" + '/' + karyawan_id + '/' +
                    holding_id,
            },
            columns: [{
                    data: 'nama_cuti',
                    name: 'nama_cuti'
                },
                {
                    data: 'jumlah_hari',
                    name: 'jumlah_hari',
                    className: 'text-center'
                },
                {
                    data: 'terpakai',
                    name: 'terpakai',
                    className: 'text-center'
                },
                {
                    data: 'sisa',
                    name: 'sisa',
                    className: 'text-center'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                },
            ],
            order: [
                [0, 'asc']
            ]
        });
        $('#btn_tambah_kontrak').click(function() {
            $('#modal_tambah_kontrak').modal('show');
        });


        var tableJ = $('#tabel_kontrak').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('karyawan/kontrak_datatable') }}" + "/" + karyawan_id + '/' + holding_id,
            },
            columns: [{
                    data: 'option',
                    name: 'option'
                },
                {
                    data: 'file_kontrak',
                    name: 'file_kontrak'
                },
                {
                    data: 'file_kontrak_download',
                    name: 'file_kontrak_download'
                },
                {
                    data: 'tanggal_mulai',
                    name: 'tanggal_mulai'
                },
                {
                    data: 'lama_kontrak',
                    name: 'lama_kontrak'
                },
                {
                    data: 'tanggal_selesai',
                    name: 'tanggal_selesai'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ],
            order: [
                [5, 'asc']
            ]
        });
        $('#btn_save_kontrak').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan').val());
            formData.append('tanggal_mulai', $('#tanggal_mulai_add').val());
            formData.append('lama_kontrak', $('#lama_kontrak_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            var fileInput = $('#file_kontrak_add')[0];
            if (fileInput.files.length > 0) {
                formData.append('file_kontrak', fileInput.files[0]);
            }

            // post
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/kontrak_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#modal_tambah_kontrak').modal('hide');
                        $('#kontrak_form').trigger('reset')
                        $('#tabel_kontrak').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        $(document).on('click', '#btn_edit_kontrak', function() {
            var id_kontrak = $(this).data('id_kontrak');
            var file_kontrak = $(this).data('file_kontrak');
            var tanggal_mulai = $(this).data('tanggal_mulai');
            var lama_kontrak = $(this).data('lama_kontrak');
            $('#id_kontrak_update').val(id_kontrak);
            $('#tanggal_mulai_update').val(tanggal_mulai);
            $('#lama_kontrak_update').val(lama_kontrak);
            $('#old_file_update').val(file_kontrak);
            $('#modal_edit_kontrak').modal('show');

        });
        $('#btn_update_kontrak').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_kontrak', $('#id_kontrak_update').val());
            formData.append('tanggal_mulai', $('#tanggal_mulai_update').val());
            formData.append('lama_kontrak', $('#lama_kontrak_update').val());
            formData.append('old_file', $('#old_file_update').val());
            formData.append('menu_id', $('#menu_id_add').val());
            var fileInput = $('#file_kontrak_update')[0];
            if (fileInput.files.length > 0) {
                formData.append('file_kontrak', fileInput.files[0]);
            }
            $.ajax({
                type: "POST",

                url: "{{ url('karyawan/kontrak_update') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        $('#modal_edit_kontrak').modal('hide');
                        $('#tabel_kontrak').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                }

            });
        });

        $(document).on('click', '#btn_delete_kontrak', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id_kontrak = $(this).data('id_kontrak');
            var file_kontrak = $(this).data('file_kontrak');
            var menu_id = $('#menu_id_add').val();

            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id_kontrak);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_kontrak: id_kontrak,
                            file_kontrak: file_kontrak,
                            menu_id: menu_id
                        },
                        url: "{{ url('karyawan/kontrak_delete/') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#tabel_kontrak').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                                // console.log(data.data_riwayat);

                            } else {
                                $('#tabel_kontrak').DataTable()
                                    .ajax
                                    .reload();
                                Swal.fire({
                                    title: 'error',
                                    text: data.error,
                                    icon: 'error',
                                    timer: 6500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        // info HR dan kontrak end

        $('#btn_simpan_pendidikan').click(function() {

            var id_karyawan = $('#id_karyawan').val();
            var jenjang = $('#jenjang').val();
            var jurusan = $('#jurusan').val();
            var tahun_masuk = $('#tahun_masuk').val();
            var tahun_lulus = $('#tahun_lulus').val();
            var nama_instansi = $('#nama_instansi').val();
            var menu_id = $('#menu_id_add').val();
            // console.log(id_karyawan, jenjang, jurusan, tahun_masuk, tahun_lulus, nama_instansi);
            $.ajax({
                url: "{{ url('karyawan/AddPendidikan') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id_karyawan: id_karyawan,
                    jenjang: jenjang,
                    jurusan: jurusan,
                    tahun_masuk: tahun_masuk,
                    tahun_lulus: tahun_lulus,
                    nama_instansi: nama_instansi,
                    menu_id: menu_id
                },
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_add_pendidikan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }

                    $('#form_add_pendidikan').trigger('reset');
                    // console.log(response.data_pen/didikan);
                    $('#table_pendidikan').DataTable().ajax.reload();

                    if (response.data_pendidikan >= 3) {
                        $('#btn_tambah_pendidikan').attr("disabled", true);
                        $('#pesan_disabled_pendidikan').show();
                    }
                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_add_pendidikan').trigger('reset');
                    $('#modal_add_pendidikan').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });

        });
        $(document).on('click', '.btn_edit_pendidikan', function() {
            // console.log($(this).data());
            var id_pendidikan = $(this).data('id_pendidikan');
            var jenjang = $(this).data('jenjang');
            var nama_instansi = $(this).data('nama_instansi');
            var jurusan = $(this).data('jurusan');
            var tahun_masuk = $(this).data('tahun_masuk');
            var tahun_lulus = $(this).data('tahun_lulus');
            $('#id_pendidikan').val(id_pendidikan);
            $('#jenjang_update').val(jenjang);
            $('#nama_instansi_update').val(nama_instansi);
            $('#jurusan_update').val(jurusan);
            $('#tahun_masuk_update').val(tahun_masuk);
            $('#tahun_lulus_update').val(tahun_lulus);
            $('#modal_edit_pendidikan').modal('show');
        });
        $('#btn_simpan_edit_pendidikan').click(function() {
            var id_pendidikan = $('#id_pendidikan').val();
            var id_karyawan = $('#id_karyawan').val();
            var jenjang = $('#jenjang_update').val();
            var jurusan = $('#jurusan_update').val();
            var tahun_masuk = $('#tahun_masuk_update').val();
            var tahun_lulus = $('#tahun_lulus_update').val();
            var nama_instansi = $('#nama_instansi_update').val();
            var menu_id = $('#menu_id_add').val();
            // console.log(id_karyawan, jenjang, jurusan, tahun_masuk, tahun_lulus, nama_instansi);
            $.ajax({
                url: "{{ url('karyawan/UpdatePendidikan') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id_pendidikan: id_pendidikan,
                    id_karyawan: id_karyawan,
                    jenjang: jenjang,
                    jurusan: jurusan,
                    tahun_masuk: tahun_masuk,
                    tahun_lulus: tahun_lulus,
                    nama_instansi: nama_instansi,
                    menu_id: menu_id
                },
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    $('#form_add_pendidikan').trigger('reset');
                    $('#modal_edit_pendidikan').modal('hide');
                    if (response.code == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 4000,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }
                    $('#table_pendidikan').DataTable().ajax.reload();
                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_edit_pendidikan').trigger('reset');
                    $('#modal_edit_pendidikan').modal('hide');
                    let errors = '';
                    $.each(data.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                        timer: 4000,
                    });

                }
            });

        });
        $(document).on('click', '#btn_delete_pendidikan', function() {
            let id = $(this).data('id'); // ambil id dari tombol
            let menu_id = $('#menu_id_add').val(); // ambil id dari tombol

            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data pendidikan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) { // v9 pakai result.value, bukan result.isConfirmed
                    $.ajax({
                        url: "{{ url('karyawan/DeletePendidikan/' . $karyawan->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_pendidikan: id,
                            menu_id: menu_id,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.code == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                $('#table_pendidikan').DataTable().ajax.reload();
                                if (response.data_pendidikan <= 3) {
                                    $('#btn_tambah_pendidikan').attr(
                                        "disabled", false);
                                    $('#pesan_disabled_pendidikan').hide();
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                    });
                }
            });
        });
        $('#btn_upload_keahlian').click(function() {
            $('#file_keahlian').click();
        });
        $('#file_keahlian').change(function() {
            var file = this.files[0];
            if (!file) return;
            var fileURL = URL.createObjectURL(file);
            // $(this).val(file);
            $('.group-button-keahlian').empty();
            $('.group-button-keahlian').append('<a href="' + fileURL +
                    '" target="_blank" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i></a>')
                .append(
                    '<button type="button" id="btn_reset_keahlian" class="btn btn-sm btn-danger"><i class="mdi mdi-refresh"></i></button>'
                );
        });
        $(document).on('click', '#btn_reset_keahlian', function() {
            // console.log('reset');
            $('#file_keahlian').val('');
            $('.group-button-keahlian').empty();
        });
        $(document).on('click', '#btn_reset_keahlian_update', function() {
            // console.log('reset');
            $('#file_keahlian_update').val('');
            $('.group-update-keahlian').empty();
            $('.group-update-keahlian').append(
                '<button type="button" id="btn_upload_keahlian_update" class="btn btn-sm btn-secondary"><i class="mdi mdi-upload"></i> Upload</button><p class="text-primary">format: PDF</p>'
            );

        });
        $(document).on('click', '#btn_upload_keahlian_update', function() {
            // console.log('upload');
            $('#file_keahlian_update').click();
        });
        $(document).on('click', '.btn_edit_keahlian', function() {
            // console.log($(this).data());
            var id_keahlian = $(this).data('id_keahlian');
            var keahlian = $(this).data('keahlian');
            var file_keahlian = $(this).data('file_keahlian');
            var file_url = $(this).data('file_url');
            $('#id_keahlian').val(id_keahlian);
            $('#nama_keahlian_update').val(keahlian);
            // console.log(file_keahlian.length);
            if (file_keahlian.length != 0) {
                // console.log('ya');
                $('#file_keahlian_old_update').val(file_keahlian);
                $('.group-update-keahlian').empty();
                $('.group-update-keahlian').append('<a href="' + file_url +
                        '" target="_blank" class="btn btn-sm btn-info"><i class="mdi mdi-eye"></i></a>')
                    .append(
                        '<button type="button" id="btn_reset_keahlian_update" class="btn btn-sm btn-danger"><i class="mdi mdi-delete"></i></button>'
                    );
            } else {
                // console.log('ok');
                $('.group-update-keahlian').empty();
                $('.group-update-keahlian').append(
                    '<button type="button" id="btn_upload_keahlian_update" class="btn btn-sm btn-secondary"><i class="mdi mdi-upload"></i> Upload</button>'
                );

            }
            $('#modal_edit_keahlian').modal('show');
        });
        $('#file_keahlian_update').change(function() {
            var file = this.files[0];
            if (!file) return;
            var fileURL = URL.createObjectURL(file);
            // $(this).val(file);
            $('.group-update-keahlian').empty();
            $('.group-update-keahlian').append('<a href="' + fileURL +
                    '" target="_blank" class="btn btn-sm btn-primary"><i class="mdi mdi-eye"></i></a>')
                .append(
                    '<button type="button" id="btn_reset_keahlian" class="btn btn-sm btn-danger"><i class="mdi mdi-refresh"></i></button>'
                );
        });
        $('#btn_simpan_keahlian').click(function() {
            var id_karyawan = $('#id_karyawan').val();
            var nama_keahlian = $('#nama_keahlian').val();
            var file_keahlian = $('#file_keahlian')[0].files[0];

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id_karyawan', id_karyawan);
            formData.append('nama_keahlian', nama_keahlian);
            if (file_keahlian) {
                formData.append('file_keahlian', file_keahlian);
            } else {
                formData.append('file_keahlian', null);
            }
            // console.log(id_karyawan, nama_keahlian, file_keahlian);
            $.ajax({
                url: "{{ url('karyawan/AddKeahlian/') }}",
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_add_keahlian').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }
                    $('#form_add_keahlian').trigger('reset');
                    table1.ajax.reload();
                    // console.log(response.data_keahlian);
                    if (response.data_keahlian >= 5) {
                        $('#btn_tambah_keahlian').attr("disabled", true);
                        $('#pesan_disabled_keahlian').show();
                    }


                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_add_keahlian').trigger('reset');
                    $('#modal_add_keahlian').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });
        });
        $('#btn_simpan_edit_keahlian').click(function() {
            var id_karyawan = $('#id_karyawan').val();
            var id_keahlian = $('#id_keahlian').val();
            var nama_keahlian = $('#nama_keahlian_update').val();
            var file_keahlian = $('#file_keahlian_update')[0].files[0];
            var file_keahlian_old = $('#file_keahlian_old_update').val();

            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id_karyawan', id_karyawan);
            formData.append('id_keahlian', id_keahlian);
            formData.append('nama_keahlian', nama_keahlian);
            formData.append('file_keahlian_old', file_keahlian_old);
            // console.log(file_keahlian);
            if (file_keahlian) {
                formData.append('file_keahlian', file_keahlian);
            } else {
                formData.append('file_keahlian', null);
            }
            // console.log(id_karyawan, nama_keahlian, file_keahlian);
            $.ajax({
                url: "{{ url('karyawan/UpdateKeahlian') }}",
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.code == 200) {
                        $('#modal_edit_keahlian').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        })
                    } else {
                        let errors = response.message;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    }

                    $('#form_edit_keahlian').trigger('reset');

                    table1.ajax.reload();
                },
                error: function(data) {
                    Swal.close();
                    // console.log(data);
                    $('#form_edit_keahlian').trigger('reset');
                    $('#modal_edit_keahlian').modal('hide');
                    let errors = '';
                    $.each(data.responseJSON.message, function(key, value) {
                        errors += value.join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errors,
                    });

                }
            });
        });
        $(document).on('click', '#btn_delete_keahlian', function() {
            let id = $(this).data('id'); // ambil id dari tombol

            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data keahlian akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) { // v9 pakai result.value, bukan result.isConfirmed
                    $.ajax({
                        url: "{{ url('karyawan/DeleteKeahlian/' . $karyawan->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_keahlian: id
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            if (response.code == 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                table1.ajax.reload(); // reload datatable
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                            if (response.data_keahlian <= 5) {
                                $('#btn_tambah_keahlian').attr(
                                    "disabled", false);
                                $('#pesan_disabled_keahlian').hide();
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                    });
                }
            });
        });

        // IJAZAH
        var file_ijazah = '{{ $karyawan->ijazah }}';
        var url_ijazah = "{{ asset('storage/ijazah') }}";
        $(document).on('click', '#btn_change_ijazah', function() {
            $('#file_ijazah').click();
            // console.log('change ijazah');
        });
        $(document).on('click', '#btn_upload_ijazah', function() {
            $('#file_ijazah').click();
            // console.log('upload ijazah');
        });
        $('#file_ijazah').change(function() {
            var file = this.files[0];
            if (!file) return;
            var fileURL = URL.createObjectURL(file);
            // $(this).val(file);
            $('.group-button-ijazah').empty();
            $('.group-button-ijazah').append('<a href="' + fileURL +
                    '" target="_blank" class="btn btn-sm bottom-0"><i class="mdi mdi-eye"></i><span class="text-primary">&nbsp;Lihat File</span></a>'
                )
                .append(
                    '<button type="button" id="btn_reset_ijazah" class="btn btn-sm bottom-0"><i class="mdi mdi-refresh"></i><span class="text-primary">&nbsp;Reset</span></button>'
                );
        });
        $(document).on('click', '#btn_reset_ijazah', function() {
            $('#file_ijazah').val('');
            if (file_ijazah == '') {
                $('.group-button-ijazah').empty();
                $('.group-button-ijazah').append(
                    '<button type="button" id="btn_upload_ijazah" class="btn btn-sm bottom-0"><i class="mdi mdi-upload"></i><span class="text-primary">&nbsp;Ganti</span></button>'
                );
            } else {
                $('.group-button-ijazah').empty();
                $('.group-button-ijazah').append('<a href="' + url_ijazah + '/' + file_ijazah +
                        '" target="_blank" class="btn btn-sm bottom-0"><i class="mdi mdi-eye"></i><span class="text-primary">&nbsp;Lihat File</span></a>'
                    )
                    .append(
                        '<button type="button" id="btn_change_ijazah" class="btn btn-sm bottom-0"><i class="mdi mdi-pencil"></i><span class="text-primary">&nbsp;Ganti</span></button>'
                    )
                    .append(
                        '<button type="button" id="btn_delete_file_ijazah" class="btn btn-sm bottom-0"><i class="mdi mdi-delete"></i><span class="text-primary">&nbsp;Hapus</span></button>'
                    );
            }
        });
        $(document).on('click', '#btn_delete_file_ijazah', function() {
            $('#file_ijazah').val('');
            $('.group-button-ijazah').empty();
            $('.group-button-ijazah').append(
                '<button type="button" id="btn_upload_ijazah" class="btn btn-sm bottom-0"><i class="mdi mdi-upload"></i><span class="text-primary">&nbsp;Upload</span></button>'
            );
        });

        // TRANSKRIP NILAI
        var file_transkrip_nilai = '{{ $karyawan->transkrip_nilai }}';
        var url_transkrip_nilai = "{{ asset('storage/transkrip_nilai') }}";
        $(document).on('click', '#btn_change_transkrip_nilai', function() {
            $('#file_transkrip_nilai').click();
            // console.log('change transkrip nilai');
        });
        $(document).on('click', '#btn_upload_transkrip_nilai', function() {
            $('#file_transkrip_nilai').click();
            // console.log('upload transkrip nilai');
        });
        $('#file_transkrip_nilai').change(function() {
            var file = this.files[0];
            if (!file) return;
            var fileURL = URL.createObjectURL(file);
            // $(this).val(file);
            $('.group-button-transkrip_nilai').empty();
            $('.group-button-transkrip_nilai').append('<a href="' + fileURL +
                    '" target="_blank" class="btn btn-sm bottom-0"><i class="mdi mdi-eye"></i><span class="text-primary">&nbsp;Lihat File</span></a>'
                )
                .append(
                    '<button type="button" id="btn_reset_transkrip_nilai" class="btn btn-sm bottom-0"><i class="mdi mdi-refresh"></i><span class="text-primary">&nbsp;Reset</span></button>'
                );
        });
        $(document).on('click', '#btn_reset_transkrip_nilai', function() {
            $('#file_transkrip_nilai').val('');
            if (file_transkrip_nilai == '') {
                $('.group-button-transkrip_nilai').empty();
                $('.group-button-transkrip_nilai').append(
                    '<button type="button" id="btn_upload_transkrip_nilai" class="btn btn-sm bottom-0"><i class="mdi mdi-upload"></i><span class="text-primary">&nbsp;Ganti</span></button>'
                );
            } else {
                $('.group-button-transkrip_nilai').empty();
                $('.group-button-transkrip_nilai').append('<a href="' + url_transkrip_nilai + '/' +
                        file_transkrip_nilai +
                        '" target="_blank" class="btn btn-sm bottom-0"><i class="mdi mdi-eye"></i><span class="text-primary">&nbsp;Lihat File</span></a>'
                    )
                    .append(
                        '<button type="button" id="btn_change_transkrip_nilai" class="btn btn-sm bottom-0"><i class="mdi mdi-pencil"></i><span class="text-primary">&nbsp;Ganti</span></button>'
                    )
                    .append(
                        '<button type="button" id="btn_delete_file_transkrip_nilai" class="btn btn-sm bottom-0"><i class="mdi mdi-delete"></i><span class="text-primary">&nbsp;Hapus</span></button>'
                    );
            }
        });
        $(document).on('click', '#btn_delete_file_transkrip_nilai', function() {
            $('#file_transkrip_nilai').val('');
            // console.log('File transkrip nilai dihapus');
            $('.group-button-transkrip_nilai').empty();
            $('.group-button-transkrip_nilai').append(
                '<button type="button" id="btn_upload_transkrip_nilai" class="btn btn-sm bottom-0"><i class="mdi mdi-upload"></i><span class="text-primary">&nbsp;Upload</span></button>'
            );
        });

        // Perbankan
        function bankCheck(that) {
            if (that.value == "BBRI") {
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    target: document.getElementById('modal_tambah_karyawan'),
                    position: 'top',
                    icon: 'warning',
                    title: 'Apakah Benar Bank BRI?',
                    showConfirmButton: true
                });
                bankdigit = 15;
                // document.getElementById("ifBRI").style.display = "block";
                // document.getElementById("ifBCA").style.display = "none";
                // document.getElementById("ifMANDIRI").style.display = "none";
            } else if (that.value == "BBCA") {
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    target: document.getElementById('modal_tambah_karyawan'),
                    position: 'top',
                    icon: 'warning',
                    title: 'Apakah Benar Bank BCA?',
                    showConfirmButton: true
                });
                bankdigit = 10;
                // document.getElementById("ifMANDIRI").style.display = "block";
                // document.getElementById("ifBCA").style.display = "none";
                // document.getElementById("ifBRI").style.display = "none";
            } else if (that.value == "BOCBC") {
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    target: document.getElementById('modal_tambah_karyawan'),
                    position: 'top',
                    icon: 'warning',
                    title: 'Apakah Benar Bank OCBC?',
                    showConfirmButton: true
                });
                bankdigit = 12;
                // document.getElementById("ifBCA").style.display = "block";
                // document.getElementById("ifMANDIRI").style.display = "none";
                // document.getElementById("ifBRI").style.display = "none";
            }
        }
        $('#nomor_rekening').keyup(function(e) {
            if ($(this).val().length >= bankdigit) {
                $(this).val($(this).val().substr(0, bankdigit));
                document.getElementById("nomor_rekening").focus();
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    target: document.getElementById('modal_tambah_karyawan'),
                    position: 'top',
                    icon: 'warning',
                    title: 'Nomor Rekening harus ' + bankdigit +
                        ' karakter. Mohon cek kembali!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
        $('#btn_update_bank').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nama_bank', $('#nama_bank').val());
            formData.append('nama_pemilik_rekening', $('#nama_pemilik_rekening').val());
            formData.append('nomor_rekening', $('#nomor_rekening').val());
            formData.append('menu_id', $('#menu_id_add').val());


            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan/bank-edit/' . $karyawan->id) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        // Perbankan end

        // pajak
        $('#npwp').keyup(function(e) {
            if ($(this).val().length >= 16) {
                $(this).val($(this).val().substr(0, 16));
                document.getElementById("npwp").focus();
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    target: document.getElementById('modal_tambah_karyawan'),
                    position: 'top',
                    icon: 'warning',
                    title: 'Nomor NPWP harus ' + 16 + ' karakter. Mohon cek kembali!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
        $('#btn_update_pajak').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('ptkp', $('#ptkp').val());
            formData.append('nama_pemilik_npwp', $('#nama_pemilik_npwp').val());
            formData.append('npwp', $('#npwp').val());
            formData.append('menu_id', $('#menu_id_add').val());


            fileInput = $('#status_npwp_ya').is(':checked');
            // console.log(fileInput);
            if (fileInput) {
                formData.append('status_npwp', $('#status_npwp_ya').val());
            } else {
                formData.append('status_npwp', $('#status_npwp_tidak').val());
            }
            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan/pajak-edit/' . $karyawan->id) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        // pajak end
        $('#btn_update_bpjs').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            bpjs_ketenagakerjaan = $('#bpjs_ketenagakerjaan_ya').is(':checked');
            if (bpjs_ketenagakerjaan) {
                formData.append('bpjs_ketenagakerjaan', $('#bpjs_ketenagakerjaan_ya').val());
            } else {
                formData.append('bpjs_ketenagakerjaan', $('#bpjs_ketenagakerjaan_tidak').val());
            }
            bpjs_pensiun = $('#bpjs_pensiun_ya').is(':checked');
            if (bpjs_pensiun) {
                formData.append('bpjs_pensiun', $('#bpjs_pensiun_ya').val());
            } else {
                formData.append('bpjs_pensiun', $('#bpjs_pensiun_tidak').val());
            }
            bpjs_kesehatan = $('#bpjs_kesehatan_ya').is(':checked');
            if (bpjs_kesehatan) {
                formData.append('bpjs_kesehatan', $('#bpjs_kesehatan_ya').val());
            } else {
                formData.append('bpjs_kesehatan', $('#bpjs_kesehatan_tidak').val());
            }

            formData.append('no_bpjs_ketenagakerjaan', $('#no_bpjs_ketenagakerjaan').val());
            formData.append('nama_pemilik_bpjs_kesehatan', $('#nama_pemilik_bpjs_kesehatan').val());
            formData.append('no_bpjs_kesehatan', $('#no_bpjs_kesehatan').val());
            formData.append('nama_pemilik_bpjs_ketenagakerjaan', $('#nama_pemilik_bpjs_ketenagakerjaan')
                .val());
            formData.append('kelas_bpjs', $('#kelas_bpjs').val());
            formData.append('menu_id', $('#menu_id_add').val());
            // post
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan/bpjs-edit/' . $karyawan->id) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        // BPJS Edit
        // Ijazah Edit
        $('#btn_update_doc_pend').on('click', function(e) {
            e.preventDefault();


            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('ipk', $('#ipk').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('old_ijazah', $('#file_ijazah_old').val());
            var ijazah = $('#file_ijazah')[0];
            if (ijazah.files.length > 0) {
                formData.append('ijazah', ijazah.files[0]);
            }
            formData.append('old_transkrip_nilai', $('#transkrip_nilai_old').val());
            var transkrip = $('#file_transkrip_nilai')[0];
            if (transkrip.files.length > 0) {
                formData.append('transkrip_nilai', transkrip.files[0]);
            }
            console.log($('#file_ijazah_old').val(), transkrip);
            $.ajax({
                type: "POST",

                url: "{{ url('/karyawan/ijazah-edit/' . $karyawan->id) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                },

            });
        });

        // Ijazah End
        // Profil Ajax
        $('#btn_update_profil').on('click', function(e) {
            e.preventDefault();


            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nik', $('#nik').val());
            formData.append('nomor_identitas_karyawan', $('#nomor_identitas_karyawan').val());
            formData.append('id_finger', $('#id_finger').val());
            formData.append('name', $('#name').val());
            formData.append('email', $('#email').val());
            formData.append('telepon', $('#telepon').val());
            formData.append('email_kantor', $('#email_kantor').val());
            formData.append('nomor_wa_kantor', $('#nomor_wa_kantor').val());
            formData.append('tgl_join', $('#tgl_join').val());
            formData.append('nomor_wa', $('#nomor_wa').val());
            formData.append('status_nomor', $('#status_nomor').val());
            formData.append('tempat_lahir', $('#tempat_lahir').val());
            formData.append('tgl_lahir', $('#tgl_lahir').val());
            formData.append('agama', $('#agama').val());
            formData.append('status_nikah', $('#status_nikah').val());
            formData.append('jumlah_anak', $('#jumlah_anak').val());
            formData.append('gender', $('#gender').val());
            formData.append('provinsi', $('#id_provinsi').val());
            formData.append('kabupaten', $('#id_kabupaten').val());
            formData.append('kecamatan', $('#id_kecamatan').val());
            formData.append('desa', $('#id_desa').val());
            formData.append('rt', $('#rt').val());
            formData.append('rw', $('#rw').val());
            formData.append('alamat', $('#alamat').val());
            formData.append('kode_pos', $('#kode_pos').val());
            formData.append('provinsi_domisili', $('#id_provinsi_domisili').val());
            formData.append('kabupaten_domisili', $('#id_kabupaten_domisili').val());
            formData.append('kecamatan_domisili', $('#id_kecamatan_domisili').val());
            formData.append('desa_domisili', $('#id_desa_domisili').val());
            formData.append('rt_domisili', $('#rt_domisili').val());
            formData.append('rw_domisili', $('#rw_domisili').val());
            formData.append('alamat_domisili', $('#alamat_domisili').val());
            formData.append('kode_pos_domisili', $('#kode_pos_domisili').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('no_kk', $('#no_kk').val());
            formData.append('tgl_terbit_kk', $('#tgl_terbit_kk').val());
            bpjs_ketenagakerjaan = $('#btn_status_no_ya').is(':checked');
            if (bpjs_ketenagakerjaan) {
                formData.append('status_nomor', $('#btn_status_no_ya').val());
            } else {
                formData.append('status_nomor', $('#btnradio_tidak').val());
            }
            pilihan_alamat_domisili = $('#btnradio_ya').is(':checked');
            if (pilihan_alamat_domisili) {
                formData.append('pilihan_alamat_domisili', $('#btnradio_ya').val());
            } else {
                formData.append('pilihan_alamat_domisili', $('#btn_status_no_tidak').val());
            }
            formData.append('foto_karyawan_old', $('#foto_karyawan_old').val());
            var file_pp = $('#foto_karyawan')[0];
            if (file_pp.files.length > 0) {
                formData.append('foto_karyawan', file_pp.files[0]);
            }
            formData.append('ktp_old', $('#ktp_old').val());
            var ktp = $('#ktp')[0];
            if (ktp.files.length > 0) {
                formData.append('ktp', ktp.files[0]);
            }
            formData.append('kk_old', $('#kk_old').val());
            var kk = $('#kk')[0];
            if (kk.files.length > 0) {
                formData.append('kk', kk.files[0]);
            }
            // post
            $.ajax({
                type: "POST",
                url: "{{ url('/karyawan/profil-edit/' . $karyawan->id) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Gagal',
                        html: data.responseJSON.message,
                        icon: 'error',
                        timer: 5000
                    });
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        view_dokumen_datadiri();
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        // Profil Ajax End
        // jabatan ajax
        $('#btn_update_jabatan').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('departemen_id', $('#id_departemen').val());
            formData.append('divisi_id', $('#id_divisi').val());
            formData.append('bagian_id', $('#id_bagian').val());
            formData.append('jabatan_id', $('#id_jabatan').val());
            formData.append('departemen1_id', $('#id_departemen1').val());
            formData.append('divisi1_id', $('#id_divisi1').val());
            formData.append('bagian1_id', $('#id_bagian1').val());
            formData.append('jabatan1_id', $('#id_jabatan1').val());
            formData.append('departemen2_id', $('#id_departemen2').val());
            formData.append('divisi2_id', $('#id_divisi2').val());
            formData.append('bagian2_id', $('#id_bagian2').val());
            formData.append('jabatan2_id', $('#id_jabatan2').val());
            formData.append('penempatan_kerja', $('#penempatan_kerja').val());
            formData.append('penempatan_holding', $('#penempatan_holding').val());
            // post
            $.ajax({
                type: "POST",
                url: "{{ url('/karyawan/jabatan-edit/' . $karyawan->id) }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong!');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        $('#nik').keyup(function(e) {
            if ($(this).val().length >= 16) {
                $(this).val($(this).val().substr(0, 16));
                document.getElementById("nik").focus();
                Swal.fire({
                    customClass: {
                        container: 'my-swal'
                    },
                    target: document.getElementById('modal_tambah_karyawan'),
                    position: 'top',
                    icon: 'warning',
                    title: 'Nomor NIK harus ' + 16 + ' karakter. Mohon cek kembali!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });

        function showSelectLoading(selectId) {
            $(selectId).prop('disabled', true)
                .closest('.form-floating')
                .find('.select-loader')
                .removeClass('d-none');
        }

        function hideSelectLoading(selectId) {
            $(selectId).prop('disabled', false)
                .closest('.form-floating')
                .find('.select-loader')
                .addClass('d-none');
        }
        $('#id_provinsi').on('change', function() {
            showSelectLoading('#id_kabupaten');
            showSelectLoading('#id_kecamatan');
            showSelectLoading('#id_desa');
            let id_provinsi = $(this).val();
            let url = "{{ url('/karyawan/get_kabupaten') }}" + "/" + id_provinsi;
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_provinsi: id_provinsi
                // },
                success: function(response) {
                    // console.log(response);
                    hideSelectLoading('#id_kabupaten');
                    $('#id_kabupaten').html(response);
                    $('#id_kecamatan').html('<option value="">Pilih Kecamatan</option>');
                    $('#id_desa').html('<option value="">Pilih Desa</option>');
                },
                error: function(data) {
                    hideSelectLoading('#id_kabupaten');
                    hideSelectLoading('#id_kecamatan');
                    hideSelectLoading('#id_desa');
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_kabupaten').on('change', function() {
            showSelectLoading('#id_kecamatan');
            showSelectLoading('#id_desa');

            let id_kabupaten = $(this).val();
            let url =
                "{{ url('/karyawan/get_kecamatan') }}" + "/" + id_kabupaten;
            // console.log(id_kabupaten);
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_kabupaten: id_kabupaten
                // },
                success: function(response) {
                    // console.log(response);
                    hideSelectLoading('#id_kecamatan');
                    $('#id_kecamatan').html(response);
                    $('#id_desa').html('<option value="">Pilih Desa</option>');
                },
                error: function(data) {
                    hideSelectLoading('#id_kecamatan');
                    hideSelectLoading('#id_desa');
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_kecamatan').on('change', function() {
            showSelectLoading('#id_desa');
            let id_kecamatan = $(this).val();
            let url =
                "{{ url('/karyawan/get_desa') }}" + "/" + id_kecamatan;
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_kecamatan: id_kecamatan
                // },
                success: function(response) {
                    // console.log(response);
                    hideSelectLoading('#id_desa');
                    $('#id_desa').html(response);

                },
                error: function(data) {
                    hideSelectLoading('#id_desa');
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_provinsi_domisili').on('change', function() {
            showSelectLoading('#id_kabupaten_domisili');
            showSelectLoading('#id_kecamatan_domisili');
            showSelectLoading('#id_desa_domisili');
            let id_provinsi = $(this).val();
            let url =
                "{{ url('/karyawan/get_kabupaten') }}" + "/" + id_provinsi;
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_provinsi: id_provinsi
                // },
                success: function(response) {
                    // console.log(response);
                    hideSelectLoading('#id_kabupaten_domisili');

                    $('#id_kabupaten_domisili').html(response);
                    $('#id_kecamatan_domisili').html(
                        '<option value="">Pilih Kecamatan</option>');
                    $('#id_desa_domisili').html('<option value="">Pilih Desa</option>');
                },
                error: function(data) {
                    hideSelectLoading('#id_kabupaten_domisili');
                    hideSelectLoading('#id_kecamatan_domisili');
                    hideSelectLoading('#id_desa_domisili');
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_kabupaten_domisili').on('change', function() {
            showSelectLoading('#id_kecamatan_domisili');
            showSelectLoading('#id_desa_domisili');

            let id_kabupaten = $(this).val();
            let url =
                "{{ url('/karyawan/get_kecamatan') }}" +
                "/" + id_kabupaten;
            // console.log(id_kabupaten);
            // console.log(url);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_kabupaten: id_kabupaten
                // },
                success: function(response) {
                    hideSelectLoading('#id_kecamatan_domisili');
                    // console.log(response);
                    $('#id_kecamatan_domisili').html(response);
                    $('#id_desa_domisili').html('<option value="">Pilih Desa</option>');
                },
                error: function(data) {
                    hideSelectLoading('#id_kecamatan_domisili');
                    hideSelectLoading('#id_desa_domisili');
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_kecamatan_domisili').on('change', function() {
            showSelectLoading('#id_desa_domisili');
            let id_kecamatan = $(this).val();
            let url =
                "{{ url('/karyawan/get_desa') }}" +
                "/" + id_kecamatan;
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                // data: {
                //     id_kecamatan: id_kecamatan
                // },
                success: function(response) {
                    hideSelectLoading('#id_desa_domisili');
                    // console.log(response);
                    $('#id_desa_domisili').html(response);
                },
                error: function(data) {
                    hideSelectLoading('#id_desa_domisili');
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })

        $('#row_bpjs_ketenagakerjaan').hide();
        $('#row_bpjs_kesehatan').hide();
        $('#row_kelas_bpjs').show();
        var status_nomor = "{{ old('status_nomor', $karyawan->status_nomor) }}";
        var status_bpjs_ketenagakerjaan = "{{ old('bpjs_ketenagakerjaan', $karyawan->bpjs_ketenagakerjaan) }}";
        var status_bpjs_kesehatan = "{{ old('bpjs_kesehatan', $karyawan->bpjs_kesehatan) }}";
        var pilih_domisili_alamat = "{{ old('pilihan_alamat_domisili', $karyawan->status_alamat) }}";
        var status_npwp = "{{ old('status_npwp', $karyawan->status_npwp) }}";
        // console.log(status_bpjs_ketenagakerjaan);
        if (status_nomor == 'ya') {
            $('#content_nomor_wa').hide();
        } else if (status_nomor == 'tidak') {
            $('#content_nomor_wa').show();
        } else {
            $('#content_nomor_wa').hide();
        }
        if (pilih_domisili_alamat == 'ya') {
            $('#content_alamat_domisili').hide();
        } else if (pilih_domisili_alamat == 'tidak') {
            $('#content_alamat_domisili').show();
        } else {
            $('#content_alamat_domisili').hide();
        }
        if (status_bpjs_ketenagakerjaan == 'on') {
            $('#row_bpjs_ketenagakerjaan').show();
        } else if (status_bpjs_ketenagakerjaan == 'off') {
            $('#row_bpjs_ketenagakerjaan').hide();
        } else {
            $('#row_bpjs_ketenagakerjaan').hide();

        }
        if (status_bpjs_kesehatan == 'on') {
            $('#row_bpjs_kesehatan').show();
            $('#row_kelas_bpjs').show();
        } else if (status_bpjs_kesehatan == 'off') {
            $('#row_kelas_bpjs').hide();
            $('#row_bpjs_kesehatan').hide();
        } else {
            $('#row_kelas_bpjs').hide();
            $('#row_bpjs_kesehatan').hide();

        }

        if (status_npwp == 'on') {
            $('#row_npwp').show();
        } else if (status_npwp == 'off') {
            $('#row_npwp').hide();
        } else {
            $('#row_npwp').hide();
        }
        $(document).on("click", "#btn_status_no_ya", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_nomor_wa').hide();

            }
        });
        $(document).on("click", "#btn_status_no_tidak", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_nomor_wa').show();
            }
        });
        $(document).on("click", "#status_npwp_ya", function() {
            var id = $(this).val();
            if (id == 'on') {
                $('#row_npwp').show();
            } else {
                $('#row_npwp').hide();

            }
        });
        $(document).on("click", "#btnradio_ya", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_alamat_domisili').hide();

            }
        });
        $(document).on("click", "#btnradio_tidak", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_alamat_domisili').show();
            }
        });
        $(document).on("click", "#status_npwp_tidak", function() {
            var id = $(this).val();
            if (id == 'off') {
                $('#row_npwp').hide();
            } else {
                $('#row_npwp').show();
            }
            $('#nama_pemilik_npwp').val('');
            $('#npwp').val('');
        });
        $(document).on("click", "#bpjs_ketenagakerjaan_ya", function() {
            var id = $(this).val();
            if (id == 'on') {
                $('#row_bpjs_ketenagakerjaan').show();
            } else {
                $('#row_bpjs_ketenagakerjaan').hide();

            }
        });
        $(document).on("click", "#bpjs_ketenagakerjaan_tidak", function() {
            var id = $(this).val();
            if (id == 'off') {
                $('#row_bpjs_ketenagakerjaan').hide();
            } else {
                $('#row_bpjs_ketenagakerjaan').show();

            }
            $('#nama_pemilik_bpjs_ketenagakerjaan').val('');
            $('#no_bpjs_ketenagakerjaan').val('');
        });
        $(document).on("click", "#bpjs_kesehatan_ya", function() {
            var id = $(this).val();
            if (id == 'on') {
                $('#row_bpjs_kesehatan').show();
                $('#row_kelas_bpjs').show();
            } else {
                $('#row_bpjs_kesehatan').hide();
                $('#row_kelas_bpjs').hide();

            }
        });
        $(document).on("click", "#bpjs_kesehatan_tidak", function() {
            var id = $(this).val();
            if (id == 'off') {
                $('#row_bpjs_kesehatan').hide();
                $('#row_kelas_bpjs').hide();
            } else {
                $('#row_bpjs_kesehatan').show();
                $('#row_kelas_bpjs').show();
            }
            $('#nama_pemilik_bpjs_kesehatan').val('');
            $('#no_bpjs_kesehatan').val('');
            $('#kelas_bpjs').val('');
        });
        var file_cv = '{{ $karyawan->file_cv }}';
        if (file_cv == '') {
            // console.log('ok');
            $('#btn_modal_lihat').hide();
        } else {
            // console.log('ok1');
            $('#btn_modal_lihat').show();
        }
        $('#file_cv').change(function() {


            let reader = new FileReader();
            // console.log(reader);
            reader.onload = (e) => {
                $('#lihat_file_cv').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);

        });
        $('#row_kategori_jabatan').hide();
        if ($('#site_job').val() == 'ALL SITES (SP, SPS, SIP)') {
            $('#row_kategori_jabatan').show();
        }
        $(document).on("change", "#penempatan_holding", function() {
            var id = $(this).val();
            if (id == '' || id == null) {
                holding = '{{ $karyawan->penempatan_holding }}';
            } else {
                var holding = id;
            }
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_departemen') }}" + "/" + holding,

                cache: false,

                success: function(msg) {
                    // $('#id_divisi').html(msg);
                    $('#id_departemen').html(msg);
                    $('#id_departemen1').html(msg);
                    $('#id_departemen2').html(msg);
                    $('#id_departemen3').html(msg);
                    $('#id_departemen4').html(msg);
                    $('#id_divisi').html('<option value=""></option>');
                    $('#id_bagian').html('<option value=""></option>');
                    $('#id_jabatan').html('<option value=""></option>');
                    $('#id_divisi1').html('<option value=""></option>');
                    $('#id_bagian1').html('<option value=""></option>');
                    $('#id_jabatan1').html('<option value=""></option>');
                    $('#id_divisi2').html('<option value=""></option>');
                    $('#id_bagian2').html('<option value=""></option>');
                    $('#id_jabatan2').html('<option value=""></option>');
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
            // console.log($(this).val());
        });

        $('#id_departemen').on('change', function() {
            let id_departemen = $('#id_departemen').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_divisi') }}" + '/' + id_departemen,
                cache: false,

                success: function(msg) {
                    $('#id_divisi').html(msg);
                    $('#id_bagian').html('<option value=""></option>');
                    $('#id_jabatan').html('<option value=""></option>');
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_departemen1').on('change', function() {
            let id_departemen = $('#id_departemen1').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_divisi') }}" + '/' + id_departemen,
                cache: false,

                success: function(msg) {

                    $('#id_divisi1').html(msg);
                    $('#id_bagian1').html('<option value=""></option>');
                    $('#id_jabatan1').html('<option value=""></option>');
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_departemen2').on('change', function() {
            let id_departemen = $('#id_departemen2').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_divisi') }}" + '/' + id_departemen,
                cache: false,

                success: function(msg) {

                    $('#id_divisi2').html(msg);
                    $('#id_bagian2').html('<option value=""></option>');
                    $('#id_jabatan2').html('<option value=""></option>');

                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_divisi').on('change', function() {
            let id_divisi = $('#id_divisi').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_bagian') }}" + '/' + id_divisi,
                cache: false,

                success: function(msg) {
                    $('#id_bagian').html(msg);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_divisi1').on('change', function() {
            let id_divisi = $('#id_divisi1').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_bagian') }}" + '/' + id_divisi,
                cache: false,

                success: function(msg) {
                    $('#id_bagian1').html(msg);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_divisi2').on('change', function() {
            let id_divisi = $('#id_divisi2').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_bagian') }}" + '/' + id_divisi,
                cache: false,

                success: function(msg) {
                    $('#id_bagian2').html(msg);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_bagian').on('change', function() {
            let id_bagian = $('#id_bagian').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_jabatan') }}" + '/' + id_bagian,
                cache: false,

                success: function(msg) {
                    $('#id_jabatan').html(msg);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_bagian1').on('change', function() {
            let id_bagian = $('#id_bagian1').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_jabatan') }}" + '/' + id_bagian,
                cache: false,

                success: function(msg) {
                    $('#id_jabatan1').html(msg);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_bagian2').on('change', function() {
            let id_bagian = $('#id_bagian2').val();
            $.ajax({
                type: 'GET',
                url: "{{ url('karyawan/get_jabatan') }}" + '/' + id_bagian,
                cache: false,

                success: function(msg) {
                    $('#id_jabatan2').html(msg);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        })
        $('#id_jabatan').on('change', function() {
            let id = $(this).val();
            let id_karyawan = $('#id_karyawan').val();
            let divisi = $('#id_divisi').val();
            let holding = '{{ $holding }}';
            let url = "{{ url('karyawan/atasan/get_jabatan') }}" + "/" + holding + "/" + divisi;
            // console.log(divisi);
            // console.log(holding);
            $.ajax({
                url: url,
                method: 'GET',
                contentType: false,
                cache: false,
                processData: true,
                data: {
                    id: id,
                    id_karyawan: id_karyawan,
                    holding: holding,
                    id_divisi: divisi
                },
                success: function(response) {
                    // console.log(response);
                    $('#atasan').html(response);
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },

            })
        });

        $('#row_bpjs_ketenagakerjaan').hide();
        $('#row_bpjs_kesehatan').hide();
        $('#row_kelas_bpjs').show();
        var status_nomor = "{{ old('status_nomor', $karyawan->status_nomor) }}";
        var status_bpjs_ketenagakerjaan =
            "{{ old('bpjs_ketenagakerjaan', $karyawan->bpjs_ketenagakerjaan) }}";
        var status_bpjs_kesehatan = "{{ old('bpjs_kesehatan', $karyawan->bpjs_kesehatan) }}";
        var pilih_domisili_alamat = "{{ old('pilihan_alamat_domisili', $karyawan->status_alamat) }}";
        var status_npwp = "{{ old('status_npwp', $karyawan->status_npwp) }}";
        // console.log(status_bpjs_ketenagakerjaan);
        if (status_nomor == 'ya') {
            $('#content_nomor_wa').hide();
        } else if (status_nomor == 'tidak') {
            $('#content_nomor_wa').show();
        } else {
            $('#content_nomor_wa').hide();
        }
        if (pilih_domisili_alamat == 'ya') {
            $('#content_alamat_domisili').hide();
        } else if (pilih_domisili_alamat == 'tidak') {
            $('#content_alamat_domisili').show();


        } else {
            $('#content_alamat_domisili').hide();
        }
        if (status_bpjs_ketenagakerjaan == 'on') {
            $('#row_bpjs_ketenagakerjaan').show();
        } else if (status_bpjs_ketenagakerjaan == 'off') {
            $('#row_bpjs_ketenagakerjaan').hide();
        } else {
            $('#row_bpjs_ketenagakerjaan').hide();

        }
        if (status_bpjs_kesehatan == 'on') {
            $('#row_bpjs_kesehatan').show();
            $('#row_kelas_bpjs').show();
        } else if (status_bpjs_kesehatan == 'off') {
            $('#row_kelas_bpjs').hide();
            $('#row_bpjs_kesehatan').hide();
        } else {
            $('#row_kelas_bpjs').hide();
            $('#row_bpjs_kesehatan').hide();

        }

        if (status_npwp == 'on') {
            $('#row_npwp').show();
        } else if (status_npwp == 'off') {
            $('#row_npwp').hide();
        } else {
            $('#row_npwp').hide();
        }
        $(document).on("click", "#btn_status_no_ya", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_nomor_wa').hide();

            }
        });
        $(document).on("click", "#btn_status_no_tidak", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_nomor_wa').show();
            }
        });
        $(document).on("click", "#status_npwp_ya", function() {
            var id = $(this).val();
            if (id == 'on') {
                $('#row_npwp').show();
            } else {
                $('#row_npwp').hide();

            }
        });
        $(document).on("click", "#btnradio_ya", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_alamat_domisili').hide();
            }
        });
        $(document).on("click", "#btnradio_tidak", function() {
            var isChecked = $(this).is(':checked')
            if (isChecked) {
                $('#content_alamat_domisili').show();
                // console.log('ok');
                $('#id_provinsi_domisili option:selected').prop('selected', false);
            }
        });
        $(document).on("click", "#status_npwp_tidak", function() {
            var id = $(this).val();
            if (id == 'off') {
                $('#row_npwp').hide();
            } else {
                $('#row_npwp').show();

            }
        });
        $(document).on("click", "#bpjs_ketenagakerjaan_ya", function() {
            var id = $(this).val();
            if (id == 'on') {
                $('#row_bpjs_ketenagakerjaan').show();
            } else {
                $('#row_bpjs_ketenagakerjaan').hide();

            }
        });
        $(document).on("click", "#bpjs_ketenagakerjaan_tidak", function() {
            var id = $(this).val();
            if (id == 'off') {
                $('#row_bpjs_ketenagakerjaan').hide();
            } else {
                $('#row_bpjs_ketenagakerjaan').show();

            }
        });
        $(document).on("click", "#bpjs_kesehatan_ya", function() {
            var id = $(this).val();
            if (id == 'on') {
                $('#row_bpjs_kesehatan').show();
                $('#row_kelas_bpjs').show();
            } else {
                $('#row_bpjs_kesehatan').hide();
                $('#row_kelas_bpjs').hide();

            }
        });
        $(document).on("click", "#bpjs_kesehatan_tidak", function() {
            var id = $(this).val();
            if (id == 'off') {
                $('#row_bpjs_kesehatan').hide();
                $('#row_kelas_bpjs').hide();
            } else {
                $('#row_bpjs_kesehatan').show();
                $('#row_kelas_bpjs').show();

            }
        });
        var file_cv = '{{ $karyawan->file_cv }}';
        if (file_cv == '') {
            // console.log('ok');
            $('#btn_modal_lihat').hide();
        } else {
            // console.log('ok1');
            $('#btn_modal_lihat').show();
        }
        $('#file_cv').change(function() {


            let reader = new FileReader();
            // console.log(reader);
            reader.onload = (e) => {
                $('#lihat_file_cv').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);

        });
        $('#sebutkan_alergi').hide();
        let alergi = "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->alergi }}";
        if (alergi == 1) {
            $('#sebutkan_alergi').show();
        } else if (alergi == '2') {
            $('#sebutkan_alergi').hide();
            $('#sebutkan_alergi').val('');

        } else {
            $('#sebutkan_alergi').hide();
        }

        $('#sebutkan_alergi').hide();
        $('#alergi_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#sebutkan_alergi').show();
            } else {
                $('#sebutkan_alergi').hide();
                $('#sebutkan_alergi_add').val('');
            }
        });
        $('#sebutkan_phobia').hide();
        let phobia = "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->phobia }}";
        if (phobia == 1) {
            $('#sebutkan_phobia').show();
        } else if (phobia == '2') {
            $('#sebutkan_phobia').hide();
            $('#sebutkan_phobia').val('');

        } else {
            $('#sebutkan_phobia').hide();
        }

        $('#sebutkan_phobia').hide();
        $('#phobia_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#sebutkan_phobia').show();
            } else {
                $('#sebutkan_phobia').hide();
                $('#sebutkan_phobia_add').val('');
            }

        });
        $('#sebutkan_keterbatasan_fisik').hide();
        let keterbatasan_fisik =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->keterbatasan_fisik }}";
        if (keterbatasan_fisik == 1) {

            $('#sebutkan_keterbatasan_fisik').show();
        } else if (keterbatasan_fisik == '2') {
            $('#sebutkan_keterbatasan_fisik').hide();
            $('#sebutkan_keterbatasan_fisik').val('');

        } else {
            $('#sebutkan_keterbatasan_fisik').hide();
        }

        $('#sebutkan_keterbatasan_fisik').hide();
        $('#keterbatasan_fisik_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#sebutkan_keterbatasan_fisik').show();
            } else {
                $('#sebutkan_keterbatasan_fisik').hide();
                $('#sebutkan_keterbatasan_fisik_add').val('');

            }

        });
        let pengobatan_rutin =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pengobatan_rutin }}";
        if (pengobatan_rutin == '1') {
            // console.log(pengobatan_rutin);
            $('#sebutkan_pengobatan_rutin').show();
        } else if (pengobatan_rutin == '2') {
            $('#sebutkan_pengobatan_rutin').hide();
        } else {
            $('#sebutkan_pengobatan_rutin').hide();
        }

        $('#sebutkan_pengobatan_rutin').hide();
        $('#pengobatan_rutin_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#sebutkan_pengobatan_rutin').show();
            } else {
                $('#sebutkan_pengobatan_rutin').hide();
                $.ajax({
                    type: "GET",
                    url: "{{ url('/cpanel/cv/kesehatan/pengobatan_count/' . $karyawan->id) }}",
                    error: function(error) {
                        Swal.fire({
                            title: 'error',
                            text: error.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },
                    success: function(data_pengobatan) {
                        if (code = 200) {
                            // console.log(data_pengobatan);
                            if (data_pengobatan.data_pengobatan != 0) {
                                var id_pengobatan = $('#id_user_pengobatan_add').val();
                                Swal.fire({
                                    title: 'Konfirmasi',
                                    icon: 'warning',
                                    text: "Apakah benar-benar ingin menghapus data ini?",
                                    showCancelButton: true,
                                    inputValue: 0,
                                    confirmButtonText: 'Yes',
                                }).then(function(result) {
                                    if (result.value) {
                                        // console.log(id_pengobatan);
                                        $.ajax({
                                            data: {
                                                "_token": "{{ csrf_token() }}",
                                                id_pengobatan: id_pengobatan,
                                            },
                                            url: "{{ route('pengobatan_reset') }}",
                                            type: "POST",
                                            dataType: 'json',
                                            beforeSend: function() {
                                                Swal.fire({
                                                    title: '', // Kosongkan title agar fokus ke animasi
                                                    html: `<div class="custom-loader-wrapper">
                                                        <div class="modern-spinner"></div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                                                        <p class="pulse-text small">Mohon tunggu sebentar...</p>
                                                    </div>`,
                                                    allowOutsideClick: false,
                                                    allowEscapeKey: false,
                                                    showConfirmButton: false,
                                                    width: '320px',
                                                    background: '#ffffff',
                                                    customClass: {
                                                        popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                                    },
                                                    didOpen: () => {
                                                        // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                                    }
                                                });
                                            },
                                            success: function(
                                                data) {
                                                Swal.close();
                                                if (data.code ==
                                                    200) {
                                                    $('#tabel_pengobatan')
                                                        .DataTable()
                                                        .ajax
                                                        .reload();
                                                    Swal.fire({
                                                        title: 'success',
                                                        text: 'Data Berhasil dihapus',
                                                        icon: 'success',
                                                        timer: 1500
                                                    })
                                                } else {
                                                    $('#tabel_pengobatan')
                                                        .DataTable()
                                                        .ajax
                                                        .reload();
                                                    Swal.fire({
                                                        title: 'error',
                                                        text: 'Data gagal dihapus',
                                                        icon: 'success',
                                                        timer: 1500
                                                    })
                                                }
                                            },
                                            error: function(data) {
                                                Swal.fire({
                                                    title: 'Gagal',
                                                    text: 'Data Gagal dihapus',
                                                    icon: 'error',
                                                    timer: 1500
                                                })
                                            }
                                        });

                                    } else {
                                        Swal.fire({
                                            title: 'Gagal !',
                                            text: 'Data gagal dihapus',
                                            icon: 'warning',
                                            timer: 1500
                                        })
                                    }

                                });
                            }
                        }
                    }
                });
                // $('#modal_delete_riwayat').modal('show');


            }
            // console.log('asoy');

        });
        let pernah_dirawat_rs =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pernah_dirawat_rs }}";
        if (pernah_dirawat_rs == '1') {
            // console.log(pernah_dirawat_rs);
            $('#pernah_dirawat_sebutkan').show();
        } else if (pernah_dirawat_rs == '2') {
            $('#pernah_dirawat_sebutkan').hide();
        } else {
            $('#pernah_dirawat_sebutkan').hide();
        }

        $('#pernah_dirawat_sebutkan').hide();
        $('#pernah_dirawat_rs_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#pernah_dirawat_sebutkan').show();
            } else {
                $('#pernah_dirawat_sebutkan').hide();
                $.ajax({
                    type: "GET",
                    url: "{{ url('') }}",
                    error: function(error) {
                        Swal.fire({
                            title: 'error',
                            text: error.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },
                    success: function(rumah_sakit) {
                        if (code = 200) {
                            // console.log(rumah_sakit);
                            if (rumah_sakit.rumah_sakit != 0) {
                                var id_karyawan = $('#id_user_rs_add').val();
                                Swal.fire({
                                    title: 'Konfirmasi',
                                    icon: 'warning',
                                    text: "Apakah benar-benar ingin menghapus data ini?",
                                    showCancelButton: true,
                                    inputValue: 0,
                                    confirmButtonText: 'Yes',
                                }).then(function(result) {
                                    if (result.value) {
                                        // console.log(id_karyawan);
                                        $.ajax({
                                            data: {
                                                "_token": "{{ csrf_token() }}",
                                                id_karyawan: id_karyawan,
                                            },
                                            url: "{{ route('rumah_sakit_reset') }}",
                                            type: "POST",
                                            dataType: 'json',
                                            beforeSend: function() {
                                                Swal.fire({
                                                    title: '', // Kosongkan title agar fokus ke animasi
                                                    html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                                    allowOutsideClick: false,
                                                    allowEscapeKey: false,
                                                    showConfirmButton: false,
                                                    width: '320px',
                                                    background: '#ffffff',
                                                    customClass: {
                                                        popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                                    },
                                                    didOpen: () => {
                                                        // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                                    }
                                                });
                                            },
                                            success: function(
                                                data
                                            ) {
                                                Swal.close();
                                                if (data
                                                    .code ==
                                                    200
                                                ) {
                                                    $('#tabel_rs')
                                                        .DataTable()
                                                        .ajax
                                                        .reload();
                                                    Swal.fire({
                                                        title: 'success',
                                                        text: 'Data Berhasil dihapus',
                                                        icon: 'success',
                                                        timer: 1500
                                                    })
                                                } else {
                                                    $('#tabel_rs')
                                                        .DataTable()
                                                        .ajax
                                                        .reload();
                                                    Swal.fire({
                                                        title: 'error',
                                                        text: 'Data gagal dihapus',
                                                        icon: 'success',
                                                        timer: 1500
                                                    })
                                                }
                                            },
                                            error: function(
                                                data
                                            ) {
                                                Swal.fire({
                                                    title: 'Gagal',
                                                    text: 'Data Gagal dihapus',
                                                    icon: 'error',
                                                    timer: 1500
                                                })
                                            }
                                        });

                                    } else {
                                        Swal.fire({
                                            title: 'Gagal !',
                                            text: 'Data gagal dihapus',
                                            icon: 'warning',
                                            timer: 1500
                                        })
                                    }

                                });
                            }
                        }
                    }
                });
            }
            // console.log('asoy');

        });
        let kecelakaan_serius =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->kecelakaan_serius }}";
        if (kecelakaan_serius == '1') {
            // console.log(kecelakaan_serius);
            $('#kecelakaan_serius_sebutkan').show();
        } else if (kecelakaan_serius == '2') {
            $('#kecelakaan_serius_sebutkan').hide();
        } else {
            $('#kecelakaan_serius_sebutkan').hide();
        }

        $('#kecelakaan_serius_sebutkan').hide();
        $('#kecelakaan_serius_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#kecelakaan_serius_sebutkan').show();
            } else {
                $('#kecelakaan_serius_sebutkan').hide();
                $.ajax({
                    type: "GET",
                    url: "{{ url('/cpanel/cv/kesehatan/kecelakaan_count/' . $karyawan->id) }}",
                    error: function(error) {
                        Swal.fire({
                            title: 'error',
                            text: error.responseJSON.message,
                            icon: 'error',
                            timer: 4500
                        })
                    },
                    success: function(kecelakaan) {
                        if (code = 200) {
                            // console.log(kecelakaan);
                            if (kecelakaan.kecelakaan != 0) {
                                var id_karyawan = $('#id_user_kecelakaan_add').val();
                                Swal.fire({
                                    title: 'Konfirmasi',
                                    icon: 'warning',
                                    text: "Apakah benar-benar ingin menghapus data ini?",
                                    showCancelButton: true,
                                    inputValue: 0,
                                    confirmButtonText: 'Yes',
                                }).then(function(result) {
                                    if (result.value) {
                                        // console.log(id_karyawan);
                                        $.ajax({
                                            data: {
                                                "_token": "{{ csrf_token() }}",
                                                id_karyawan: id_karyawan,
                                            },
                                            url: "{{ route('kecelakaan_reset') }}",
                                            type: "POST",
                                            dataType: 'json',
                                            beforeSend: function() {
                                                Swal.fire({
                                                    title: '', // Kosongkan title agar fokus ke animasi
                                                    html: `<div class="custom-loader-wrapper">
                                                        <div class="modern-spinner"></div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                                                        <p class="pulse-text small">Mohon tunggu sebentar...</p>
                                                    </div>`,
                                                    allowOutsideClick: false,
                                                    allowEscapeKey: false,
                                                    showConfirmButton: false,
                                                    width: '320px',
                                                    background: '#ffffff',
                                                    customClass: {
                                                        popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                                    },
                                                    didOpen: () => {
                                                        // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                                    }
                                                });
                                            },
                                            success: function(
                                                data) {
                                                Swal.close();
                                                if (data.code ==
                                                    200) {
                                                    $('#tabel_kecelakaan')
                                                        .DataTable()
                                                        .ajax
                                                        .reload();
                                                    Swal.fire({
                                                        title: 'success',
                                                        text: 'Data Berhasil dihapus',
                                                        icon: 'success',
                                                        timer: 1500
                                                    })
                                                } else {
                                                    $('#tabel_kecelakaan')
                                                        .DataTable()
                                                        .ajax
                                                        .reload();
                                                    Swal.fire({
                                                        title: 'error',
                                                        text: 'Data gagal dihapus',
                                                        icon: 'success',
                                                        timer: 1500
                                                    })
                                                }
                                            },
                                            error: function(data) {
                                                Swal.fire({
                                                    title: 'Gagal',
                                                    text: 'Data Gagal dihapus',
                                                    icon: 'error',
                                                    timer: 1500
                                                })
                                            }
                                        });

                                    } else {
                                        Swal.fire({
                                            title: 'Gagal !',
                                            text: 'Data gagal dihapus',
                                            icon: 'warning',
                                            timer: 1500
                                        })
                                    }

                                });
                            }
                        }
                    }
                });


            }
            // console.log('asoy');
        });
        $('#pemeriksaan_sebelumnya_hasil').hide();
        let pemeriksaan_kerja_sebelumnya =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->pemeriksaan_kerja_sebelumnya }}";
        if (pemeriksaan_kerja_sebelumnya == 1) {

            $('#pemeriksaan_sebelumnya_hasil').show();
        } else if (pemeriksaan_kerja_sebelumnya == '2') {
            $('#pemeriksaan_sebelumnya_hasil').hide();

        } else {
            $('#pemeriksaan_sebelumnya_hasil').hide();
        }

        $('#pemeriksaan_sebelumnya_hasil').hide();
        $('#pemeriksaan_kerja_sebelumnya_add').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('#pemeriksaan_sebelumnya_hasil').show();
            } else {
                $('#pemeriksaan_sebelumnya_hasil').hide();
            }
            // console.log('asoy');

        });
        $('#gangguan_sebutkan').hide();
        let gangguan_lainnya =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->gangguan_lainnya }}";

        if (gangguan_lainnya == 'on') {

            $('#gangguan_sebutkan').show();
        } else {
            $('#gangguan_sebutkan').hide();
        }


        $('#gangguan_sebutkan').hide();
        $('#gangguan_lainnya_add').on('change', function() {
            if ($(this).prop('checked')) {
                $('#gangguan_sebutkan').show();
            } else {
                $('#gangguan_sebutkan').hide();
                $('#sebutkan_gangguan_add').val('');
            }
        })
        $('#sebutkan_vaksin_lainnya').hide();
        let vaksin_lainnya =
            "{{ $karyawan->karyawanKesehatan == null ? '' : $karyawan->karyawanKesehatan->vaksin_lainnya }}"

        if (vaksin_lainnya == 'on') {
            // console.log(vaksin_lainnya);
            $('#sebutkan_vaksin_lainnya').show();
        } else {
            $('#sebutkan_vaksin_lainnya').val('');
            $('#sebutkan_vaksin_lainnya').hide();
        }

        $('#sebutkan_vaksin_lainnya').hide();
        $('#vaksin_lainnya_add').on('change', function() {
            if ($(this).prop('checked')) {
                $('#sebutkan_vaksin_lainnya').show();
            } else {
                $('#sebutkan_vaksin_lainnya').hide();
                $('#sebutkan_vaksin_lainnya_add').val('');

            }
        })

        var table = $('#tabel_pengobatan').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/cpanel/cv/kesehatan/dt_pengobatan/' . $karyawan->id) }}",
            },
            columns: [{
                    data: 'obat',
                    name: 'obat'
                },
                {
                    data: 'alasan',
                    name: 'alasan'
                },
                {
                    data: 'option',
                    name: 'option'
                },

            ],
        });
        $('#tambah_pengobatan').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('obat', $('#obat_add').val());
            formData.append('id_karyawan', $('#id_user_pengobatan_add').val());
            formData.append('alasan', $('#alasan_add').val());
            $.ajax({
                type: "POST",

                url: "{{ route('pengobatan_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function() {
                    Swal.close();
                    alert('Something is wrong');
                    // console.log(formData);
                },
                success: function(data) {
                    Swal.close();
                    // console.log(data.data_user.ijazah);
                    if (data.code == 200) {
                        Swal.fire({
                            // title: data.message,
                            text: 'Data Pengobatan ditambah',
                            icon: 'success',
                            timer: 4500
                        })

                        $('#obat_add').val('');
                        $('#alasan_add').val('');
                        $('#tabel_pengobatan').DataTable().ajax.reload();
                        // if (currentStep < totalSteps) {
                        //     showStep(currentStep + 1);
                        // }
                        //mengosongkan modal dan menyembunyikannya

                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on('click', '#btn_delete_pengobatan', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id_pengobatan = $(this).data('id_pengobatan');
            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id_pengobatan);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_pengobatan: id_pengobatan,
                        },
                        url: "{{ route('pengobatan_delete') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#tabel_pengobatan').DataTable().ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            } else {
                                $('#tabel_pengobatan').DataTable().ajax
                                    .reload();
                                Swal.fire({
                                    title: 'error',
                                    text: 'Data gagal dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        // end pengobatan
        // rumah sakit
        var table = $('#tabel_rs').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/cpanel/cv/kesehatan/dt_rumah_sakit/' . $karyawan->id) }}",
            },
            columns: [{
                    data: 'tahun_rs',
                    name: 'tahun_rs'
                },
                {
                    data: 'penyebab_rs',
                    name: 'penyebab_rs'
                },
                {
                    data: 'option',
                    name: 'option'
                },

            ],
        });
        $('#tambah_rs').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('tahun_rs', $('#tahun_rs_add').val());
            formData.append('id_karyawan', $('#id_user_rs_add').val());
            formData.append('penyebab_rs', $('#penyebab_rs_add').val());
            $.ajax({
                type: "POST",

                url: "{{ route('rumah_sakit_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },
                success: function(data) {
                    Swal.close();
                    // console.log(data.data_user.ijazah);

                    if (data.code == 200) {
                        Swal.fire({
                            // title: data.message,
                            text: 'Riwayat masuk RS ditambah',
                            icon: 'success',
                            timer: 4500
                        })
                        $('#tahun_rs_add').val('');
                        $('#penyebab_rs_add').val('');
                        $('#tabel_rs').DataTable().ajax.reload();
                        // if (currentStep < totalSteps) {
                        //     showStep(currentStep + 1);
                        // }
                        //mengosongkan modal dan menyembunyikannya

                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on('click', '#btn_delete_rumah_sakit', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id_kesehatan_rs = $(this).data('id_kesehatan_rs');
            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id_kesehatan_rs);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_kesehatan_rs: id_kesehatan_rs,
                        },
                        url: "{{ route('rumah_sakit_delete') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#tabel_rs').DataTable().ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            } else {
                                $('#tabel_rs').DataTable().ajax
                                    .reload();
                                Swal.fire({
                                    title: 'error',
                                    text: 'Data gagal dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        // kecelakaan
        var table = $('#tabel_kecelakaan').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/cpanel/cv/kesehatan/dt_kecelakaan/' . $karyawan->id) }}",
            },
            columns: [{
                    data: 'tahun_kecelakaan',
                    name: 'tahun_kecelakaan'
                },
                {
                    data: 'penyebab_kecelakaan',
                    name: 'penyebab_kecelakaan'
                },
                {
                    data: 'option',
                    name: 'option'
                },

            ],
        });
        $('#tambah_kecelakaan').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('tahun_kecelakaan', $('#tahun_kecelakaan_add').val());
            formData.append('id_karyawan', $('#id_user_kecelakaan_add').val());
            formData.append('penyebab_kecelakaan', $('#penyebab_kecelakaan_add').val());
            $.ajax({
                type: "POST",

                url: "{{ route('kecelakaan_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },
                success: function(data) {
                    Swal.close();
                    // console.log(data.data_user.ijazah);
                    if (data.code == 200) {
                        Swal.fire({
                            // title: data.message,
                            text: 'Data Pengobatan ditambah',
                            icon: 'success',
                            timer: 4500
                        })

                        $('#tahun_kecelakaan_add').val('');
                        $('#penyebab_kecelakaan_add').val('');
                        $('#tabel_kecelakaan').DataTable().ajax.reload();
                        // if (currentStep < totalSteps) {
                        //     showStep(currentStep + 1);
                        // }
                        //mengosongkan modal dan menyembunyikannya

                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })

                    }
                }

            });
        });
        $(document).on('click', '#btn_delete_kecelakaan', function() {
            // $('#modal_delete_riwayat').modal('show');
            var id_kecelakaan = $(this).data('id_kecelakaan');
            Swal.fire({
                title: 'Konfirmasi',
                icon: 'warning',
                text: "Apakah benar-benar ingin menghapus data ini?",
                showCancelButton: true,
                inputValue: 0,
                confirmButtonText: 'Yes',
            }).then(function(result) {
                if (result.value) {
                    // console.log(id_kecelakaan);
                    $.ajax({
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id_kecelakaan: id_kecelakaan,
                        },
                        url: "{{ route('kecelakaan_delete') }}",
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: '', // Kosongkan title agar fokus ke animasi
                                html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                width: '320px',
                                background: '#ffffff',
                                customClass: {
                                    popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                                },
                                didOpen: () => {
                                    // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data.code == 200) {
                                $('#tabel_kecelakaan').DataTable().ajax
                                    .reload();
                                Swal.fire({
                                    title: 'success',
                                    text: 'Data Berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            } else {
                                $('#tabel_kecelakaan').DataTable().ajax
                                    .reload();
                                Swal.fire({
                                    title: 'error',
                                    text: 'Data gagal dihapus',
                                    icon: 'success',
                                    timer: 1500
                                })
                            }
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Data Gagal dihapus',
                                icon: 'error',
                                timer: 1500
                            })
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Gagal !',
                        text: 'Data gagal dihapus',
                        icon: 'warning',
                        timer: 1500
                    })
                }

            });
        });
        // end kecelakaan
        // Add kesehatan
        $('#btn_update_kesehatan').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            //ambil data dari form
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_karyawan', $('#id_karyawan').val());
            formData.append('perokok', $('#perokok_add').val());
            formData.append('alkohol', $('#alkohol_add').val());
            formData.append('alergi', $('#alergi_add').val());
            formData.append('sebutkan_alergi', $('#sebutkan_alergi_add').val());
            formData.append('phobia', $('#phobia_add').val());
            formData.append('sebutkan_phobia', $('#sebutkan_phobia_add').val());
            formData.append('keterbatasan_fisik', $('#keterbatasan_fisik_add').val());
            formData.append('sebutkan_keterbatasan_fisik', $('#sebutkan_keterbatasan_fisik_add').val());
            formData.append('pengobatan_rutin', $('#pengobatan_rutin_add').val());
            formData.append('obat', $('#obat_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('alasan', $('#alasan_add').val());
            if ($('#asma_add').is(':checked')) {
                formData.append('asma', $('#asma_add').val());
            } else {
                formData.append('asma', '');
            }
            if ($('#diabetes_add').is(':checked')) {
                formData.append('diabetes', $('#diabetes_add').val());
            } else {
                formData.append('diabetes', '');
            }
            if ($('#hipertensi_add').is(':checked')) {
                formData.append('hipertensi', $('#hipertensi_add').val());
            } else {
                formData.append('hipertensi', '');
            }
            if ($('#jantung_add').is(':checked')) {
                formData.append('jantung', $('#jantung_add').val());
            } else {
                formData.append('jantung', '');
            }
            if ($('#tbc_add').is(':checked')) {
                formData.append('tbc', $('#tbc_add').val());
            } else {
                formData.append('tbc', '');
            }
            if ($('#hepatitis_add').is(':checked')) {
                formData.append('hepatitis', $('#hepatitis_add').val());
            } else {
                formData.append('hepatitis', '');
            }
            if ($('#epilepsi_add').is(':checked')) {
                formData.append('epilepsi', $('#epilepsi_add').val());
            } else {
                formData.append('epilepsi', '');
            }
            if ($('#gangguan_mental_add').is(':checked')) {
                formData.append('gangguan_mental', $('#gangguan_mental_add').val());
            } else {
                formData.append('gangguan_mental', '');
            }
            if ($('#gangguan_pengelihatan_add').is(':checked')) {
                formData.append('gangguan_pengelihatan', $('#gangguan_pengelihatan_add').val());
            } else {
                formData.append('gangguan_pengelihatan', '');
            }
            if ($('#gangguan_lainnya_add').is(':checked')) {
                formData.append('gangguan_lainnya', $('#gangguan_lainnya_add').val());
            } else {
                formData.append('gangguan_lainnya', '');
            }
            formData.append('pernah_dirawat_rs', $('#pernah_dirawat_rs_add').val());
            formData.append('sebutkan_gangguan', $('#sebutkan_gangguan_add').val());
            formData.append('tahun_rs', $('#tahun_rs_add').val());
            formData.append('penyebab_rs', $('#penyebab_rs_add').val());
            formData.append('kecelakaan_serius', $('#kecelakaan_serius_add').val());
            formData.append('mampu_shift', $('#mampu_shift_add').val());
            formData.append('pemeriksaan_kerja_sebelumnya', $('#pemeriksaan_kerja_sebelumnya_add')
                .val());
            formData.append('pemeriksaan_sebelumnya_hasil', $('#pemeriksaan_sebelumnya_hasil_add')
                .val());
            if ($('#covid_add').is(':checked')) {
                formData.append('covid', $('#covid_add').val());
            } else {
                formData.append('covid', '');
            }
            if ($('#hepatitis_add').is(':checked')) {
                formData.append('hepatitis', $('#hepatitis_add').val());
            } else {
                formData.append('hepatitis', '');
            }
            if ($('#tetanus_add').is(':checked')) {
                formData.append('tetanus', $('#tetanus_add').val());
            } else {
                formData.append('tetanus', '');
            }
            if ($('#vaksin_lainnya_add').is(':checked')) {
                formData.append('vaksin_lainnya', $('#vaksin_lainnya_add').val());
            } else {
                formData.append('vaksin_lainnya', '');
            }
            formData.append('sebutkan_vaksin_lainnya', $('#sebutkan_vaksin_lainnya_add').val());
            if ($('#persetujuan_kesehatan_add').is(':checked')) {
                formData.append('persetujuan_kesehatan', $('#persetujuan_kesehatan_add').val());
            } else {
                formData.append('persetujuan_kesehatan', '');
            }

            var fileInput = $('#surat_keterangan_add')[0];
            if (fileInput.files.length > 0) {
                formData.append('surat_keterangan', fileInput.files[0]);
            }
            // post
            $.ajax({
                type: "POST",

                url: "{{ route('kesehatan_post') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: '', // Kosongkan title agar fokus ke animasi
                        html: `<div class="custom-loader-wrapper">
                            <div class="modern-spinner"></div>
                        </div>
                        <div class="mt-3">
                            <h5 class="mb-1" style="font-weight: 700; color: #333;">MEMPROSES DATA</h5>
                            <p class="pulse-text small">Mohon tunggu sebentar...</p>
                        </div>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        width: '320px',
                        background: '#ffffff',
                        customClass: {
                            popup: 'modern-loading-popup' // Menggunakan class border-radius besar dari sebelumnya
                        },
                        didOpen: () => {
                            // Kita tidak memanggil Swal.showLoading() karena sudah pakai custom spinner
                        }
                    });
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error',
                        text: data.responseJSON.message ||
                            'terjadi kesalahan saat memproses data',
                        icon: 'error',
                        timer: 5000
                    });
                },
                success: function(data) {
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: data.message,
                            icon: 'success',
                            timer: 5000
                        })
                        //mengosongkan modal dan menyembunyikannya
                        // $('#perokok_add').val('');
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        let errorHtml =
                            '<ul style="text-align: left; font-size: 0.9rem; line-height: 1.6;">';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                // Menggunakan icon peringatan kecil di depan setiap pesan
                                errorHtml += `<li style="margin-bottom: 5px; list-style-type: none;">
                        <span style="color: #ff9f43; margin-right: 8px;">●</span> ${message}
                      </li>`;
                            });
                        });
                        errorHtml += '</ul>';

                        Swal.fire({
                            title: '<span style="font-size: 1.25rem; font-weight: bold;">Validasi Gagal</span>',
                            html: errorHtml, // Gunakan properti html, bukan text
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 6500,
                            customClass: {
                                popup: 'rounded-16 shadow-lg', // Opsional: jika ingin tambah class kustom
                            },
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 4500
                        })

                    }
                }
            });
        });
        $.ajax({
            type: "GET",
            url: "{{ url('/cpanel/cv/kesehatan/kesehatan_get/' . $karyawan->id) }}",
            error: function(error) {
                Swal.fire({
                    title: 'error',
                    text: error.responseJSON.message,
                    icon: 'error',
                    timer: 4500
                })

            },
            success: function(kesehatan_get) {
                // console.log(kesehatan_get);
                $('#sebutkan_alergi_add').val(kesehatan_get != null ? kesehatan_get
                    .sebutkan_alergi : '');
                $('#sebutkan_gangguan_add').val(kesehatan_get != null ? kesehatan_get
                    .sebutkan_gangguan : '');
                $('#sebutkan_keterbatasan_fisik_add').val(kesehatan_get != null ? kesehatan_get
                    .sebutkan_keterbatasan_fisik : '');
                $('#sebutkan_phobia_add').val(kesehatan_get != null ? kesehatan_get
                    .sebutkan_phobia : '');
                $('#sebutkan_vaksin_lainnya_add').val(kesehatan_get != null ? kesehatan_get
                    .sebutkan_vaksin_lainnya : '');

            }
        });
    });
</script>
