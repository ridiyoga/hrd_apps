@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
        #note {
            position: absolute;
            left: 50px;
            top: 35px;
            padding: 0px;
            margin: 0px;
            cursor: default;
        }

        .futuristic-header {
            padding: 28px 20px 18px;
        }

        .futuristic-header h5 {
            color: --primary();
            font-weight: 600;
        }

        .futuristic-header small {
            color: rgba(255, 255, 255, 0.65);
        }

        .futuristic-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(145deg, #7b2ff7, #9b6dff);
            color: white;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            box-shadow: 0 0 25px rgba(155, 109, 255, 0.8);
        }

        .dl-row {
            display: grid;
            grid-template-columns: 220px 1fr;
            row-gap: 10px;
            column-gap: 12px;
        }

        .dl-row dt {
            font-weight: 600;
            margin: 0;
        }

        .dl-row dd {
            margin: 0;
        }
    </style>
@endsection
@section('content_top')

    <div class="card profile-card border-0">
        {{-- Header --}}
        <div class="futuristic-header text-center">
            <div class="d-inline-block">
                <img src="{{ url_karir() . '/storage/file_pp/' . $table->Cv->file_pp ?? '-' }}"
                    style="max-height: 150px; max-width: 100px;">
            </div>
            <h5 class="mt-3 mb-1">Informasi Interview</h5>
            <small class="text-muted">Inverview Manager Calon Karyawan</small>
        </div>
        <div class="card-body px-3 px-md-4">
            <div class="alert_interview"></div>
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 col-md-3 mb-2">
                    <span class="fw-bold">
                        <i class="fas fa-user me-2"></i>Nama Pelamar
                    </span>
                </div>

                <div class="col-sm-8 col-md-9 ">
                    <div class="p-3 border-start border-3 border-primary bg-light rounded-end fw-bold text-dark">
                        {{ $table->Cv->nama_lengkap ?? '-' }}
                    </div>
                </div>
            </div>
            <div class="row align-items-center mb-3">
                <div class="col-sm-4 col-md-3 mb-2">
                    <span class="fw-bold">
                        <i class="fas fa-user me-2"></i>Detail Pelamar
                    </span>
                </div>

                <div class="col-sm-8 col-md-9 ">
                    <div class="p-3 border-start border-3 border-primary bg-light rounded-end fw-bold text-dark">
                        <a class="btn btn-sm btn-outline-dark" href="{{ url('interview/pdfUserKaryawan/' . $table->id) }}"
                            target="_blank">
                            Download CV
                        </a>
                    </div>
                </div>
            </div>

            <div class="row align-items-center mb-3">
                <div class="col-sm-4 col-md-3 mb-2">
                    <span class="fw-bold">
                        <i class="fas fa-file-alt me-2"></i>Catatan HRD
                    </span>
                </div>

                <div class="col-sm-8 col-md-9 ">
                    <div class="p-3 border-start border-3 border-primary bg-light rounded-end text-dark"
                        style="text-align: justify;">
                        {{ $table->DataInterview ? $table->DataInterview->catatan : '' }}
                    </div>
                </div>
            </div>


            <div class="row align-items-center mb-3">
                <div class="col-sm-4 col-md-3 mb-2">
                    <span class="fw-bold">
                        <i class="fa-solid fa-arrow-up-1-9 me-2"></i>Nilai Ujian Pelamar
                    </span>
                </div>

                <div class="col-sm-8 col-md-9 ">
                    <div class="p-3 border-start border-3 border-primary bg-light rounded-end fw-bold text-dark">
                        <a class="btn btn-sm btn-outline-dark" href="{{ url('interview/pdfNilaiKaryawan/' . $table->id) }}"
                            target="_blank">
                            Download Nilai
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-md-3 mb-2">
                <span class="fw-bold">
                    <i class="fa-solid fa-arrow-up-1-9 me-2"></i>Ujian Pilihan Ganda
                </span>
            </div>
            @foreach ($table->waktuujian as $uu)
                <div class="col-sm-8 col-md-9 ">
                    <div class="p-3 border-start border-3 border-primary bg-light rounded-end fw-bold text-dark">
                        <a class="btn btn-sm btn-outline-dark"
                            href="{{ url('/pg-data-interview/dt/data-data_ujian_user/data-get_pg_interview_pdf/' . $uu->kode . '/' . $table->id . '/' . $table->Holding->holding_code) }}"
                            target="_blank">
                            {{ $uu->ujian->nama }}
                        </a>
                    </div>
                </div>
            @endforeach
            <div class="col-sm-4 col-md-3 my-2">
                <span class="fw-bold">
                    <i class="fa-solid fa-arrow-up-1-9 me-2"></i>Ujian Essai
                </span>
            </div>
            @foreach ($table->ujianEsaiJawab as $uu)
                <div class="col-sm-8 col-md-9 mb-2">
                    <div class="p-3 border-start border-3 border-primary bg-light rounded-end fw-bold text-dark">
                        <a class="btn btn-sm btn-outline-dark"
                            href="{{ url('pg-data-interview/data-get_esai_interview_pdf/' . $uu->kode . '/' . $table->id . '/' . $table->Holding->holding_code) }}"
                            target="_blank">
                            {{ $uu->ujian->nama }}
                        </a>
                    </div>
                </div>
            @endforeach



            @if ($table->status_lanjutan == '4b' || $table->status_lanjutan == '5b')
                <div class="{{ $table->status_lanjutan == '4b' ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-sticky-note"></i>&nbsp;Catatan Manager
                </div>
                <div>{{ $table?->DataInterview?->catatan_interview_manager }}</div>
            @endif

            @if ($table->status_lanjutan == '4b' || $table->status_lanjutan == '5b' || $table->status_lanjutan == '8b')
                <div class="col-lg-12">
                    <div class="text-center">
                        @if ($table->status_lanjutan == '4b' || $table->status_lanjutan == '8b')
                            <span class="badge bg-success"><i class="fa fa-check" aria-hidden="true"></i> Approved <br>
                                {{ Carbon\Carbon::parse($table->tanggal_wawancara_manager)->format('d-m-Y H:i') }}</span>
                        @else
                            <span class="badge bg-danger"><i class="fa fa-times" aria-hidden="true"></i> Rejected <br>
                                {{ Carbon\Carbon::parse($table->tanggal_wawancara_manager)->format('d-m-Y H:i') }}</span>
                        @endif
                    </div>
                </div>
            @else
                <form id="form_approve" class="my-2" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="hidden" id="id" name="id" value="{{ $table->id }}">
                        <input type="hidden" id="status" name="status" value="">
                        <input type="hidden" id="menu_id" name="menu_id" value="{{ $menu_id }}">
                    </div>
                    <div class="input-group">
                        <textarea class="form-control" placeholder="Catatan" id="catatan" name="catatan" style="font-weight: bold"></textarea>
                    </div>
                    <br>
                    <div class="col-12">
                        <div class="text-center">
                            <!-- <div class="input-group"> -->
                            <button type="button" id="approve_btn_confirm" class="btn btn-sm btn-success btn-rounded"
                                data-action="save-png"><i class="fa fa-save" aria-hidden="true"> </i> &nbsp;
                                Diterima</button>
                            <button type="button" id="not_approve_btn_confirm" class="btn btn-sm btn-warning btn-rounded"
                                data-action="not_save-png"><i class="fa fa-times" aria-hidden="true"> </i> &nbsp; Tidak
                                Diterima</button>
                        </div>
                        <!-- </div> -->
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
@section('content')
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini ?</p>
            <form method="POST" action="{{ route('logout_user') }}">
                @csrf
                <a onclick="event.preventDefault(); this.closest('form').submit();"
                    class="btn_klik btn btn-sm btn-danger light pwa-btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    &nbsp;Logout
                </a>
                <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                    aria-label="Close">Batal</a>
            </form>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_interview_diterima"
        aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah Anda Ingin Menerima Calon Karyawan Ini ?</p>

            <button type="button" id="approve_btn" class="btn btn-sm btn-success light pwa-btn">
                <i class="fa-solid fa-save"></i>
                &nbsp;Iya
            </button>
            <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal
            </a>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_interview_ditolak"
        aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-body text-center small">
            <h6 class="title">KONFIRMASI</h6>
            <p>Apakah Anda Ingin Menolak Calon Karyawan Ini ?</p>

            <button type="button" id="not_approve_btn" class="btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-save"></i>
                &nbsp;Iya
            </button>
            <a href="javascrpit:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">Batal
            </a>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            window.addEventListener("pageshow", function(event) {
                if (event.persisted) {
                    // Reinitialize JS
                    Swal.close();
                }
            });
            window.onbeforeunload = function() {
                Swal.fire({
                    allowOutsideClick: false,
                    background: 'transparent',
                    html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                    showCancelButton: false,
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        // Swal.showLoading()
                    },
                });
            };

            $(document).on('click', '#approve_btn_confirm', function(e) {
                e.preventDefault();
                var offcanvasEl = document.getElementById('offcanvas_interview_diterima');
                var offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();
            });
            $(document).on('click', '#not_approve_btn_confirm', function(e) {
                e.preventDefault();
                var offcanvasEl = document.getElementById('offcanvas_interview_ditolak');
                var offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();
            });
            $(document).on('click', '#approve_btn', function(e) {
                e.preventDefault();
                var approve = 'approve';
                var id = $('#id').val();
                var catatan = $('#catatan').val();
                var status = '4b';
                var menu_id = $('#menu_id').val();
                // console.log(id, catatan, status);
                $.ajax({
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                        catatan: catatan,
                        status: status,
                        menu_id: menu_id
                    },
                    url: "{{ url('/interview/approve/proses') }}" + '/' + menu_id,
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(data) {
                        if (data.code == 200) {
                            var url = "{{ url('/interview/dashboard/') }}" + "/" + data
                                .menu; //the url I want to redirect to
                            $(location).attr('href', url);
                        } else {
                            var session = data.session;
                            var url = "{{ url('/interview/dashboard') }}" + "/" + data
                                .menu; //the url I want to redirect to
                            $(location).attr('href', url);
                        }

                    },
                    error: function(data) {
                        // console.log('error:', data)
                        Swal.close();
                        Swal.fire({
                            title: 'Error',
                            text: data.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        })
                    }
                });
            });
            $(document).on('click', '#not_approve_btn', function(e) {
                e.preventDefault();
                var approve = 'not_approve';
                var id = $('#id').val();
                var catatan = $('#catatan').val();
                var status = '5b';
                var menu_id = $('#menu_id').val();
                // console.log(menu_id);

                $.ajax({
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                        catatan: catatan,
                        status: status,
                        menu_id: menu_id
                    },
                    url: "{{ url('/interview/approve/proses') }}" + '/' + menu_id,
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            allowOutsideClick: false,
                            background: 'transparent',
                            html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                            showCancelButton: false,
                            showConfirmButton: false,
                            onBeforeOpen: () => {
                                // Swal.showLoading()
                            },
                        });
                    },
                    success: function(data) {
                        if (data.code == 200) {
                            var url = "{{ url('/interview/dashboard/') }}" + "/" + data
                                .menu; //the url I want to redirect to
                            $(location).attr('href', url);
                        } else {
                            var session = data.session;
                            var url = "{{ url('/interview/dashboard') }}" + "/" + data
                                .menu; //the url I want to redirect to
                            $(location).attr('href', url);
                        }

                    },
                    error: function(data) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error',
                            text: data.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });

                    }
                });
            });
            $(document).on('click', '#btn_klik', function(e) {
                Swal.fire({
                    allowOutsideClick: false,
                    background: 'transparent',
                    html: ' <div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div><div class="spinner-grow text-primary spinner-grow-sm me-2" role="status"></div>',
                    showCancelButton: false,
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        // Swal.showLoading()
                    },
                });
            });

        });
    </script>
@endsection
