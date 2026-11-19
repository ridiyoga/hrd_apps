@extends('admin.layouts.dashboard')
@section('css')
    @include('css_baru')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <style type="text/css">
        .my-swal {
            z-index: X;
        }

        .swal2-container {
            z-index: 9999;
        }

        .text-center {
            text-align: center;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/@icon/entypo@1.0.3/entypo.css" rel="stylesheet">
@endsection
@section('isi')
    @include('sweetalert::alert')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Transactions -->
            <div class="col-lg-12">
                <div class="container card p-3">
                    <input type="hidden" id="menu_id_add" value="">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ranking-tab" data-bs-toggle="tab"
                                data-bs-target="#ranking-tab-pane" type="button" role="tab"
                                aria-controls="ranking-tab-pane" aria-selected="false">Ranking</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="progres-tab" data-bs-toggle="tab"
                                data-bs-target="#progres-tab-pane" type="button" role="tab"
                                aria-controls="progres-tab-pane" aria-selected="true">Progress</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="progres-tab-pane" role="tabpanel"
                            aria-labelledby="progres-tab" tabindex="0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">DATA PROGRES
                                        RECRUITMENT
                                        {{ $kuota->Jabatan->nama_jabatan }}
                                        {{ $kuota->Jabatan->Bagian->Divisi->nama_divisi }}
                                        (KUOTA : {{ $kuota->kuota }})
                                    </h5>
                                </div>
                            </div>
                            <table class="table" id="tabel_progres" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Pelamar</th>
                                        <th>Total Koefisien</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                        <th>Status</th>
                                        <th>Konfirmasi Pelamar</th>
                                        <th>Alasan</th>
                                        <th>Manager</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="ranking-tab-pane" role="tabpanel" aria-labelledby="progres-tab"
                            tabindex="0">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">DATA RANKING RECRUITMENT
                                        {{ $kuota->Jabatan->nama_jabatan }}
                                        {{ $kuota->Jabatan->Bagian->Divisi->nama_divisi }}
                                        (KUOTA : {{ $kuota->kuota }})
                                    </h5>
                                </div>
                            </div>
                            <table class="table" id="tabel_ranking" style="width: 100%; font-size: small;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Pelamar</th>
                                        <th>Total Koefisien</th>
                                        <th>Esai Average</th>
                                        <th>Bobot Esai</th>
                                        <th>pilihan ganda Average</th>
                                        <th>Bobot Pilihan Ganda</th>
                                        <th>Interview Average</th>
                                        <th>Bobot Interview</th>
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
            {{-- @foreach ($site as $ss)
                {{ $ss->sitename }}
        @endforeach --}}
        </div>
    </div>
    <div class="modal fade" id="modal_status" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">STATUS CALON KARYAWAN</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_update" name="id">
                    <div class="form-floating form-floating-outline custom-form-group mb-4">
                        <select class="form-select" id="status_update" name="status">
                            <option value="1b" selected>LOLOS INTERVIEW MANAGER</option>
                            <option value="2b">LOLOS LANGSUNG</option>
                            <option value="3b">TIDAK LOLOS</option>
                        </select>
                        <label for="status_update">PILIH STATUS</label>
                    </div>
                    <div id="lolos_manager">
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <input type="text" id="tanggal_wawancara" name="tanggal_wawancara"
                                class="form-control @error('tanggal_wawancara') is-invalid @enderror datepicker"
                                placeholder="Tanggal" value="{{ old('tanggal_wawancara') }}" readonly />
                            <label for="tanggal_wawancara">TANGGAL WAWANCARA</label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select @error('online') is-invalid @enderror" id="online_add"
                                name="online" autofocus value="{{ old('online') }}">
                                <option value="1" selected>OFFLINE</option>
                                <option value="2">ONLINE</option>
                            </select>
                            <label for="online_add">ONLINE?</label>
                            @error('online')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        @error('tanggal_wawancara')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div id="tempat_wawancara_form">

                            <div class="form-floating form-floating-outline custom-form-group mb-4">
                                <select class="form-select" id="tempat_wawancara" name="tempat_wawancara">
                                    <option value="" selected disabled>
                                        Pilih Tempat
                                    </option>
                                    @foreach ($site as $st)
                                        <option value="{{ $st->id }}">
                                            {{ $st->site_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="tempat_wawancara"><small>TEMPAT
                                        WAWANCARA</small></label>
                            </div>
                        </div>
                        <div id="link_wawancara_form">

                            <div class="form-floating form-floating-outline custom-form-group mb-4">

                                <input type="text" id="link_wawancara" name="link_wawancara"
                                    class="form-control @error('link_wawancara') is-invalid @enderror"
                                    value="{{ old('link_wawancara') }}" />
                                <label for="link_wawancara"><small>LINK
                                        WAWANCARA ONLINE</small></label>

                            </div>
                        </div>

                        @error('link_wawancara')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <div class="form-floating form-floating-outline custom-form-group mb-4">

                            <input type="time" id="waktu_wawancara" name="waktu_wawancara"
                                class="form-control @error('waktu_wawancara') is-invalid @enderror"
                                value="{{ old('waktu_wawancara') }}" />
                            <label for="waktu_wawancara"><small>WAKTU (JAM)
                                    WAWANCARA</small></label>
                        </div>
                        @error('waktu_wawancara')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div id="lolos_langsung">
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <input type="text" id="tanggal_diterima_update" name="tanggal_diterima"
                                class="form-control @error('tanggal_diterima') is-invalid @enderror datepicker"
                                placeholder="Tanggal" value="{{ old('tanggal_diterima') }}" readonly />
                            <label for="tanggal_diterima_update">TANGGAL MASUK KERJA</label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select" id="tempat_bekerja_update" name="tempat_bekerja">
                                <option value="" selected disabled>
                                    Pilih Tempat
                                </option>
                                @foreach ($site as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->site_name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="bagian_recruitment px-2"><small>TEMPAT
                                    BEKERJA</small></label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">

                            <input type="text" id="gaji_update" name="gaji"
                                class="form-control @error('gaji') is-invalid @enderror" value="{{ old('gaji') }}" />
                            <label for="gaji_update px-2"><small>GAJI (Rp)</small></label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">

                            <input type="time" id="waktu_bekerja_update" name="WAKTU_bekerja"
                                class="form-control @error('waktu_bekerja') is-invalid @enderror"
                                value="{{ old('waktu_bekerja') }}" />
                            <label for="waktu_bekerja_update"><small>WAKTU BEKERJA</small></label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <textarea type="text" id="notes_langsung_update" name="notes_langsung"
                                class="form-control @error('notes_langsung') is-invalid @enderror" value="{{ old('notes_langsung') }}"></textarea>
                            <label for="notes_langsung_update"><small>NOTES</small></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_save_status">submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_tidak_lolos" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">TIDAK LOLOS?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_ditolak" name="id">

                    <input type="hidden" id="ditolak" value="3b">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_save_ditolak">Ya</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_lolos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Lolos Bekerja</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_lolos" name="id">
                    <div class="form-floating form-floating-outline py-3">
                        <input type="hidden" value="2b" name="status" id="status_lolos">
                    </div>
                    <div class="form-floating form-floating-outline py-3">
                        <input type="text" id="tanggal_diterima_lolos" name="tanggal_diterima"
                            class="form-control @error('tanggal_diterima') is-invalid @enderror datepicker"
                            placeholder="Tanggal" value="{{ old('tanggal_diterima') }}" readonly />
                        <label for="bagian_recruitment">TANGGAL MASUK KERJA</label>
                    </div>
                    <label for="bagian_recruitment px-2"><small>GAJI (Rp)</small></label>
                    <div class="form-floating form-floating-outline mb-2">

                        <input type="text" id="gaji_lolos" name="gaji"
                            class="form-control @error('gaji') is-invalid @enderror" value="{{ old('gaji') }}" />
                    </div>
                    <label for="bagian_recruitment px-2"><small>WAKTU BEKERJA</small></label>
                    <div class="form-floating form-floating-outline mb-2">

                        <input type="time" id="waktu_bekerja_lolos" name="waktu_bekerja"
                            class="form-control @error('waktu_bekerja') is-invalid @enderror"
                            value="{{ old('waktu_bekerja') }}" />
                    </div>
                    <label for="bagian_recruitment px-2"><small>TEMPAT
                            BEKERJA</small></label>
                    <div class="form-floating form-floating-outline mb-2">
                        <select class="form-select" id="tempat_bekerja_lolos" name="tempat_bekerja">
                            <option selected disabled>
                                Pilih Tempat
                            </option>
                            @foreach ($site as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->site_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <label for="bagian_recruitment px-2"><small>NOTES</small></label>
                    <div class="form-floating form-floating-outline mb-2">
                        <textarea type="text" id="notes_langsung_lolos" name="notes_langsung"
                            class="form-control @error('notes_langsung') is-invalid @enderror" value="{{ old('notes_langsung') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_save_lolos">submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_pindah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">PEMINDAHAN LOWONGAN</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating form-floating-outline custom-form-group mb-4">
                        <input type="hidden" id="id_pindah" name="id">
                        <input type="hidden" value="{{ $id_recruitment }}" id="lowongan_lama_pindah"
                            name="lowongan_lama">

                        <select class="form-select select-jabatan" id="lowongan_baru_pindah" name="lowongan_baru">
                            <option value="" selected>PILIH LOWONGAN</option>
                            @foreach ($recruitment_admin as $admin)
                                <option value="{{ $admin->id }}">
                                    {{ $admin->jabatan->nama_jabatan }},
                                    {{ $admin->jabatan->bagian->nama_bagian }},
                                    {{ $admin->jabatan->bagian->divisi->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                        <label for="lowongan_baru_pindah" class="col-form-label">PILIH LOWONGAN</label>
                    </div>
                    <div class="form-floating form-floating-outline custom-form-group mb-4">
                        <select class="form-select select-jabatan" id="status_pindah" name="status">
                            <option value="6b" selected>LOLOS INTERVIEW MANAGER</option>
                            <option value="7b">LOLOS LANGSUNG</option>
                        </select>
                        <label for="status_pindah">PILIH STATUS</label>
                    </div>
                    <div id="pindah_manager">
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <input type="text" id="tanggal_wawancara_pindah" name="tanggal_wawancara"
                                class="form-control @error('tanggal_wawancara') is-invalid @enderror datepicker"
                                placeholder="Tanggal" value="{{ old('tanggal_wawancara') }}" readonly />
                            <label for="tanggal_wawancara_pindah">TANGGAL WAWANCARA</label>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">
                            <select class="form-select @error('online') is-invalid @enderror" id="online_add2"
                                name="online" autofocus value="{{ old('online') }}">
                                <option value="1" selected>OFFLINE</option>
                                <option value="2">ONLINE</option>
                            </select>
                            <label for="penempatan">ONLINE?</label>
                        </div>
                        <div id="tempat_wawancara_form2">
                            <div class="form-floating form-floating-outline custom-form-group mb-4">
                                <select class="form-select" id="tempat_wawancara2" name="tempat_wawancara">
                                    <option value="" selected disabled>
                                        Pilih Tempat
                                    </option>
                                    @foreach ($site as $st)
                                        <option value="{{ $st->id }}">
                                            {{ $st->site_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="tempat_wawancara">TEMPAT WAWANCARA</label>
                            </div>
                        </div>
                        <div id="link_wawancara_form2">

                            <div class="form-floating form-floating-outline mb-4">

                                <input type="text" id="link_wawancara2" name="link_wawancara"
                                    class="form-control @error('link_wawancara') is-invalid @enderror"
                                    value="{{ old('link_wawancara') }}" />
                                <label for="link_wawancara2">LINK WAWANCARA</label>
                            </div>
                        </div>
                        <div class="form-floating form-floating-outline custom-form-group mb-4">

                            <input type="time" id="waktu_wawancara_pindah" name="waktu_wawancara"
                                class="form-control @error('waktu_wawancara') is-invalid @enderror"
                                value="{{ old('waktu_wawancara') }}" />
                            <label for="waktu_wawancara_pindah"><small>WAKTU (JAM)
                                    WAWANCARA</small></label>
                        </div>

                    </div>
                    <div id="pindah_langsung">
                        <div class="form-floating form-floating-outline py-3">
                            <input type="text" id="tanggal_diterima_pindah" name="tanggal_diterima"
                                class="form-control @error('tanggal_diterima') is-invalid @enderror datepicker"
                                placeholder="Tanggal" value="{{ old('tanggal_diterima') }}" readonly />
                            <label for="bagian_recruitment">TANGGAL MASUK KERJA</label>
                        </div>
                        <label for="bagian_recruitment px-2"><small>GAJI (Rp)</small></label>
                        <div class="form-floating form-floating-outline mb-2">
                            <input type="text" id="gaji_pindah" name="gaji"
                                class="form-control @error('gaji') is-invalid @enderror" value="{{ old('gaji') }}" />
                        </div>
                        <label for="bagian_recruitment px-2"><small>WAKTU BEKERJA</small></label>
                        <div class="form-floating form-floating-outline mb-2">

                            <input type="time" id="waktu_bekerja_pindah" name="waktu_bekerja"
                                class="form-control @error('waktu_bekerja') is-invalid @enderror"
                                value="{{ old('waktu_bekerja') }}" />
                        </div>
                        <label for="bagian_recruitment px-2"><small>TEMPAT
                                BEKERJA</small></label>
                        <div class="form-floating form-floating-outline mb-2">
                            <select class="form-select" id="tempat_bekerja_pindah" name="tempat_bekerja">
                                <option selected disabled>
                                    Pilih Tempat
                                </option>
                                @foreach ($site as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->site_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="bagian_recruitment px-2"><small>NOTES</small></label>
                        <div class="form-floating form-floating-outline mb-2">
                            <textarea type="text" id="notes_langsung_pindah" name="notes_pindah"
                                class="form-control @error('notes_langsung') is-invalid @enderror" value="{{ old('notes_langsung') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_save_pindah">submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_integrasi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Integrasi ke Data Karyawan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_integrasi" name="id">
                    <select class="form-select @error('online') is-invalid @enderror" id="pilihan_add" name="pilihan"
                        autofocus value="{{ old('online') }}">
                        <option value="1" selected>YA</option>
                        <option value="2">TIDAK</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn_save_integrasi">submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_catatan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <!-- Ditambahkan modal-lg jika catatannya panjang -->
            <div class="modal-content custom-modal-content">

                <!-- Header Modal -->
                <div class="modal-header px-4 pt-4 border-0">
                    <h1 class="modal-title fs-5 fw-bold text-dark" id="exampleModalLabel">
                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>Catatan Wawancara
                    </h1>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body Modal -->
                <div class="modal-body px-4 py-2 text-justify">
                    <!-- Wrapper untuk teks catatan agar lebih rapi dan presisi -->
                    <div id="catatan" class="note-container mx-2">
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="modal-footer px-4 pb-4 border-0">
                    <button type="button" class="btn btn-light px-4 fw-medium text-secondary"
                        data-bs-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>

    <script>
        var menu_id = $('#asoy').val();
        // console.log(menu_id);
        $('#menu_id_add').val(menu_id);
        $(function() {
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0
            });
        });
        $('#tempat_wawancara').select2({
            dropdownParent: $('#modal_status'),
            theme: 'bootstrap-5',
            placeholder: "Tempat Wawancara",
            allowClear: true
        });
        $('#tempat_wawancara2').select2({
            dropdownParent: $('#modal_pindah'),
            theme: 'bootstrap-5',
            placeholder: "Tempat Wawancara",
            allowClear: true
        });
        $('#tempat_bekerja_update').select2({
            dropdownParent: $('#modal_status'),
            theme: 'bootstrap-5',
            placeholder: "Tempat Bekerja",
            allowClear: true
        });
        $('#tempat_bekerja_lolos').select2({
            dropdownParent: $('#modal_lolos'),
            theme: 'bootstrap-5',
            placeholder: "Tempat Bekerja",
            allowClear: true
        });
        $('#tempat_bekerja_pindah').select2({
            dropdownParent: $('#modal_pindah'),
            theme: 'bootstrap-5',
            placeholder: "Tempat Bekerja",
            allowClear: true
        });
        let holding = window.location.pathname.split("/").pop();
        $(document).on('keyup', '#gaji_update', function(e) {
            var data = $(this).val();
            var hasil = formatRupiah(data, "Rp. ");
            $(this).val(hasil);
        });
        $(document).on('keyup', '#gaji_lolos', function(e) {
            var data = $(this).val();
            var hasil = formatRupiah(data, "Rp. ");
            $(this).val(hasil);
        });
        $(document).on('keyup', '#gaji_pindah', function(e) {
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
        let id = @json($id_recruitment);
        // console.log(id);
        var table = $('#tabel_ranking').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('dt/data-list-ranking') }}" + '/' + id + '/' + holding,
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                    searchable: false
                }, {
                    data: 'nama_lengkap',
                    name: 'nama_lengkap'
                },
                {
                    data: 'total_koefisien',
                    name: 'total_koefisien'
                },
                {
                    data: 'esai_average',
                    name: 'esai_average'
                },
                {
                    data: 'bobot_esai',
                    name: 'bobot_esai'
                },
                {
                    data: 'pg_average',
                    name: 'pg_average'
                },
                {
                    data: 'bobot_pg',
                    name: 'bobot_pg'
                },
                {
                    data: 'interview_average',
                    name: 'interview_average'
                },
                {
                    data: 'bobot_interview',
                    name: 'bobot_interview'
                },

            ],
            order: [
                [2, 'desc']
            ]
        });
        $('#ranking-tab').on('shown.bs.tab', function(e) {
            table.columns.adjust().draw().responsive.recalc();
            // table.draw();
        });
        var table2 = $('#tabel_progres').DataTable({
            "scrollY": true,
            "scrollX": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('dt/data-list-progres') }}" + '/' + id + '/' + holding,
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                    searchable: false
                }, {
                    data: 'nama_lengkap',
                    name: 'nama_lengkap'
                },
                {
                    data: 'total_koefisien',
                    name: 'total_koefisien'
                },
                {
                    data: 'catatan',
                    name: 'catatan'
                },
                {
                    data: 'pilih_status',
                    name: 'pilih_status'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'feedback',
                    name: 'feedback'
                },
                {
                    data: 'alasan_lanjutan',
                    name: 'alasan_lanjutan'
                },
                {
                    data: 'nama_manager',
                    name: 'nama_manager'
                },
            ],
            order: [
                [2, 'desc']
            ]
        });
        $('#nav_riwayat').on('shown.bs.tab', function(e) {
            table2.columns.adjust().draw().responsive.recalc();
            // table.draw();
        });
        $(document).on('click', '#btn_status_ranking', function() {
            // console.log('asooy');
            var id = $(this).data('id');
            $('#id_update').val(id);
            $('#modal_status').modal('show');

        });
        $(document).on('click', '#btn_lolos', function() {
            // console.log('asooy');
            var id = $(this).data('id');
            $('#id_lolos').val(id);
            $('#modal_lolos').modal('show');

        });
        $(document).on('click', '#btn_pemindahan', function() {
            // console.log('asooy');
            var id = $(this).data('id');
            $('#id_pindah').val(id);
            $('#modal_pindah').modal('show');

        });
        $(document).on('click', '#btn_integrasi', function() {
            // console.log('asooy44');
            var id = $(this).data('id');
            $('#id_integrasi').val(id);
            $('#modal_integrasi').modal('show');

        });
        $(document).on('click', '#btn_tidak_lolos', function() {
            // console.log('asooy44');
            var id = $(this).data('id');
            $('#id_ditolak').val(id);
            $('#modal_tidak_lolos').modal('show');

        });
        $(document).on('click', '#btn_catatan', function() {
            // console.log('asooy44');
            var id_catatan = $(this).data('id_catatan');
            var catatan = $(this).data('catatan');
            $('#id_catatan').val(id);
            $('#catatan').html(catatan);
            $('#modal_catatan').modal('show');

        });
        $('#btn_save_status').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();
            // console.log($('#tempat_wawancara').val());
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_update').val());
            formData.append('status', $('#status_update').val());
            formData.append('tempat_bekerja', $('#tempat_bekerja_update').val() ?? null);
            formData.append('waktu_bekerja', $('#waktu_bekerja_update').val());
            formData.append('tanggal_diterima', $('#tanggal_diterima_update').val());
            formData.append('tanggal_wawancara', $('#tanggal_wawancara').val());
            formData.append('online', $('#online_add').val());
            formData.append('tempat_wawancara', $('#tempat_wawancara').val() ?? null);
            formData.append('gaji', $('#gaji_update').val());
            formData.append('link_wawancara', $('#link_wawancara').val());
            formData.append('waktu_wawancara', $('#waktu_wawancara').val());
            formData.append('notes_langsung', $('#notes_langsung_update').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('holding', holding);
            $.ajax({
                type: "POST",

                url: "{{ url('/dt/data-interview/ranking_update_status') }} ",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
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
                        $('#modal_status').modal('hide');
                        $('#tanggal_diterima_update').val('');
                        $('#notes_langsung_update').val('');
                        $('#tempat_bekerja_update').val('');
                        $('#waktu_bekerja_update').val('');
                        $('#tanggal_wawancara').val('');
                        $('#tempat_wawancara').val('');
                        $('#link_wawancara').val('');
                        $('#waktu_wawancara').val('');
                        $('#gaji_update').val('');
                        $('#tabel_progres').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        // console.log(errors);
                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                errorMessages += `• ${message}\n`;
                            });
                        });
                        Swal.fire({
                            // title: data.message,
                            text: errorMessages,
                            icon: 'warning',
                            timer: 4500
                        })

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })
                        $('#modal_status').modal('hide');
                    }
                }

            });
        });
        $('#btn_save_ditolak').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();
            // console.log($('#tempat_wawancara').val());
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_ditolak').val());
            formData.append('status', $('#ditolak').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('holding', holding);
            $.ajax({
                type: "POST",

                url: "{{ url('/dt/data-interview/ranking_update_status') }} ",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
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
                        $('#modal_status').modal('hide');
                        $('#tanggal_diterima_update').val('');
                        $('#notes_langsung_update').val('');
                        $('#tempat_bekerja_update').val('');
                        $('#waktu_bekerja_update').val('');
                        $('#tanggal_wawancara').val('');
                        $('#tempat_wawancara').val('');
                        $('#link_wawancara').val('');
                        $('#waktu_wawancara').val('');
                        $('#gaji_update').val('');
                        $('#tabel_progres').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        // console.log(errors);
                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                errorMessages += `• ${message}\n`;
                            });
                        });
                        Swal.fire({
                            // title: data.message,
                            text: errorMessages,
                            icon: 'warning',
                            timer: 4500
                        })

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })
                        $('#modal_status').modal('hide');
                    }
                }

            });
        });
        $('#btn_save_lolos').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_lolos').val());
            formData.append('status', $('#status_lolos').val());
            formData.append('tempat_bekerja', $('#tempat_bekerja_lolos').val());
            formData.append('waktu_bekerja', $('#waktu_bekerja_lolos').val());
            formData.append('tanggal_diterima', $('#tanggal_diterima_lolos').val());
            formData.append('gaji', $('#gaji_lolos').val());
            formData.append('notes_langsung', $('#notes_langsung_lolos').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('holding', holding);
            $.ajax({
                type: "POST",
                url: "{{ url('/dt/data-interview/ranking_update_status') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
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
                        $('#modal_lolos').modal('hide');
                        $('#tanggal_diterima_lolos').val('');
                        $('#notes_langsung_lolos').val('');
                        $('#tempat_bekerja_lolos').val('');
                        $('#gaji_lolos').val('');
                        $('#waktu_bekerja_lolos').val('');
                        $('#tabel_progres').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        // console.log(errors);
                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                errorMessages += `• ${message}\n`;
                            });
                        });
                        Swal.fire({
                            // title: data.message,
                            text: errorMessages,
                            icon: 'warning',
                            timer: 4500
                        })

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })
                        $('#modal_lolos').modal('hide');
                    }
                }

            });
        });
        $('#btn_save_pindah').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_pindah').val());
            formData.append('lowongan_baru', $('#lowongan_baru_pindah').val());
            formData.append('lowongan_lama', $('#lowongan_lama_pindah').val());
            formData.append('status', $('#status_pindah').val());
            formData.append('tempat_bekerja', $('#tempat_bekerja_pindah').val());
            formData.append('waktu_bekerja', $('#waktu_bekerja_pindah').val());
            formData.append('tanggal_wawancara', $('#tanggal_wawancara_pindah').val());
            formData.append('online', $('#online_add2').val());
            formData.append('tempat_wawancara', $('#tempat_wawancara2').val());
            formData.append('link_wawancara', $('#link_wawancara2').val());
            formData.append('tanggal_diterima', $('#tanggal_diterima_pindah').val());
            formData.append('waktu_wawancara', $('#waktu_wawancara_pindah').val());
            formData.append('gaji', $('#gaji_pindah').val());
            formData.append('notes_pindah', $('#notes_langsung_pindah').val());
            formData.append('menu_id', $('#menu_id_add').val());
            formData.append('holding', holding);


            $.ajax({
                type: "POST",

                url: "{{ url('/dt/data-interview/ranking_update_status') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
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
                        $('#modal_pindah').modal('hide');
                        $('#lowongan_baru_pindah').val('');
                        $('#tanggal_wawancara_pindah').val('');
                        $('#tabel_progres').DataTable().ajax.reload();
                        $('#tempat_wawancara2').val('');
                        $('#link_wawancara2').val('');
                        $('#tempat_bekerja_pindah').val('');
                        $('#waktu_bekerja_pindah').val('');
                        $('#tanggal_diterima_pindah').val('');
                        $('#waktu_wawancara_pindah').val('');
                        $('#gaji_pindah').val('');
                        $('#notes_langsung_pindah').val('');
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        // console.log(errors);
                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                errorMessages += `• ${message}\n`;
                            });
                        });
                        Swal.fire({
                            // title: data.message,
                            text: errorMessages,
                            icon: 'warning',
                            timer: 4500
                        })

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })
                        $('#modal_pindah').modal('hide');
                    }
                }

            });
        });
        $('#btn_save_integrasi').on('click', function(e) {
            e.preventDefault();
            var formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id_integrasi').val());
            formData.append('pilihan', $('#pilihan_add').val());
            formData.append('menu_id', $('#menu_id_add').val());
            $.ajax({
                type: "POST",
                url: "{{ url('/dt/data-interview/integrasi') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                error: function() {
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
                        $('#modal_integrasi').modal('hide');
                        $('#tabel_progres').DataTable().ajax.reload();
                    } else if (data.code == 400) {
                        let errors = data.errors;
                        // console.log(errors);
                        let errorMessages = '';

                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(message) {
                                errorMessages += `• ${message}\n`;
                            });
                        });
                        Swal.fire({
                            // title: data.message,
                            text: errorMessages,
                            icon: 'warning',
                            timer: 4500
                        })

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: data.error,
                            icon: 'error',
                            timer: 10000
                        })
                        $('#modal_lolos').modal('hide');
                    }
                }

            });
        });
        $('#lolos_langsung').hide();
        $('#status_update').on('change', function() {
            let value = $(this).val();
            if (value == '') {
                $('#lolos_langsung').hide();
                $('#tanggal_wawancara').val('');
                $('#tempat_wawancara').val('');
                $('#waktu_wawancara').val('');

            } else if (value == '1b') {
                $('#lolos_manager').show();
                $('#lolos_langsung').hide();

            } else if (value == '2b') {
                $('#lolos_langsung').show();
                $('#lolos_manager').hide();
            } else {
                $('#lolos_manager').hide();
                $('#lolos_langsung').hide();
                $('#tanggal_wawancara').val('');
                $('#tempat_wawancara').val('');
                $('#waktu_wawancara').val('');
            }
        });
        $('#link_wawancara_form').hide();
        $('#online_add').on('change', function() {
            let value = $(this).val();
            if (value == '2') {
                $('#link_wawancara_form').show();
                $('#tempat_wawancara_form').hide();
            } else {
                $('#link_wawancara_form').hide();
                $('#tempat_wawancara_form').show();
            }
        });
        $('#pindah_langsung').hide();
        $('#status_pindah').on('change', function() {
            let value = $(this).val();
            if (value == '') {
                $('#pindah_langsung').hide();
                $('#tanggal_wawancara').val('');
                $('#tempat_wawancara').val('');
                $('#waktu_wawancara').val('');

            } else if (value == '6b') {
                $('#pindah_manager').show();
                $('#pindah_langsung').hide();

            } else if (value == '7b') {
                $('#pindah_langsung').show();
                $('#pindah_manager').hide();
            }
        });
        $('#link_wawancara_form2').hide();
        $('#online_add2').on('change', function() {
            let value = $(this).val();
            if (value == '2') {
                $('#link_wawancara_form2').show();
                $('#tempat_wawancara_form2').hide();
            } else {
                $('#link_wawancara_form2').hide();
                $('#tempat_wawancara_form2').show();
            }
        });
    </script>
    </script>
    {{-- end datatable  --}}
@endsection
