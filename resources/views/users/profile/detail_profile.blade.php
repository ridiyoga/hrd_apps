@extends('users.layouts.main')
@section('title')
APPS | KARYAWAN - SP
@endsection
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.css">

<style>
    .profile-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);

    }

    .section-title {
        font-size: 13px;
        font-weight: 600;
        color: #6f42c1;
        margin-bottom: 10px;
    }

    .form-control {
        border-radius: 12px;
        padding-left: 42px;
        height: 44px;
        border: 1px solid #e5e5e5;
    }

    .form-control:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 .15rem rgba(111, 66, 193, .2);
    }

    .form-input {
        position: relative;
        margin-bottom: 14px;
    }

    .form-input .input-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #6f42c1;
        font-size: 14px;
    }

    .modern-check .form-check-input {
        border-radius: 6px;
    }

    @media (max-width: 576px) {
        .profile-card {
            border-radius: 12px;
        }

        .profile-tabs span {
            font-size: 10px;
        }
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

    .profile-tabs {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        border-bottom: none;
    }

    .profile-tabs .nav-link {
        border: none;
        border-radius: 14px;
        padding: 8px 12px;
        background: #f3e8ff;
        color: #6b21a8;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .profile-tabs .nav-link.active {
        background: linear-gradient(135deg, #a855f7, #7c3aed);
        color: #fff;
        box-shadow: 0 6px 14px rgba(168, 85, 247, 0.4);
    }

    .profile-tabs .nav-link i {
        font-size: 12px;
    }

    /* wrapper */
    .select2-container {
        width: 100% !important;
    }

    /* main select */
    .select2-container--default .select2-selection--single {
        height: 44px;
        border-radius: 14px;
        border: 1px solid #e9d5ff;
        background: #faf5ff;
        padding-left: 42px;
        display: flex;
        align-items: center;
    }

    /* text */
    .select2-selection__rendered {
        font-size: 14px;
        color: #4c1d95;
        line-height: normal !important;
    }

    /* arrow */
    .select2-selection__arrow {
        top: 50% !important;
        transform: translateY(-50%);
    }

    /* focus */
    .select2-container--focus .select2-selection {
        border-color: #813ac3;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.25);
        background: #fff;
    }

    /* dropdown */
    .select2-dropdown {
        border-radius: 14px;
        border: 1px solid #ddd;
        overflow: hidden;
    }

    /* option */
    .select2-results__option {
        padding: 10px 14px;
        font-size: 14px;
    }

    /* selected option */
    .select2-results__option--highlighted {
        background-color: #ede9fe !important;
        color: #4c1d95;
    }

    .select2-container--default .select2-selection--single {
        height: 44px;
        border-radius: 12px;
        padding: 8px 12px;
    }

    .select2-selection__rendered {
        line-height: 28px !important;
    }

    /* Item terpilih */
    .select2-container--default .select2-results__option--selected {
        background: linear-gradient(135deg, #a855f7, #7c3aed);
        /* indigo modern */
        color: #fff !important;
    }

    /* Hover item */
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: linear-gradient(135deg, #a855f7, #7c3aed);
        color: #fff;
    }

    .custom-file-input::-webkit-file-upload-button {
        visibility: hidden;
        display: none;
    }

    .custom-file-input::before {
        content: 'Pilih File';
        display: inline-block;
        background: #e9ecef;
        /* border:  solid #ced4da; */
        border-radius: 1px;
        padding: 2px 12px;
        outline: none;
        white-space: nowrap;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        margin-right: 1px;
    }

    .custom-file-input:hover::before {
        border-color: #adb5bd;
    }

    .custom-file-input:active::before {
        background: #dee2e6;
    }
</style>
@endsection
@section('content_top')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <div class="card profile-card border-0">
            {{-- Header --}}
            <div class="futuristic-header text-center">
                <div class="futuristic-icon">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <h5 class="mt-3 mb-1">Informasi Profil</h5>
                <small class="text-muted">Perbarui Informasi Profil</small>
                <div class="alert_profile"></div>
            </div>
            <div class="card-body px-3 px-md-4">
                <ul class="nav profile-tabs" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#data-diri">
                            <i class="fa-solid fa-building"></i>
                            <span>Data Diri</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alamat">
                            <i class="fa-solid fa-house"></i>
                            <span>Alamat</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pendidikan">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>Pendidikan</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#kontak_kantor">
                            <i class="fa-solid fa-signal"></i>
                            <span>Kontak Kantor</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    {{-- DATA DIRI --}}
                    <div class="tab-pane fade show active" id="data-diri">
                        @if ($karyawan_temp_jml == 0)
                        <form method="POST" id="form_update_profile"
                            action="{{ url('profile/save_detail_profile') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="karyawan_id" value="{{ $user_karyawan->id }}">
                            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
                            {{-- BASIC INFO --}}
                            <div class="section-title">Data Diri Berdasarkan KTP</div>

                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-id-card',
                            'type' => 'number',
                            'name' => 'nik',
                            'class' => 'nik',
                            'label' => 'NIK',
                            'value' => old('nik', $user_karyawan->nik),
                            ])

                            <input type="hidden" name="ktp_old" id="ktp_old" value="{{ $user_karyawan->ktp }}">
                            <div id="group-button-ktp"></div>
                            <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-rounded"
                                data-bs-toggle="offcanvas" data-bs-target="#offcanvas_ktp">
                                <i class="fa-solid fa-upload"></i></i>&nbsp;Upload KTP
                            </a>
                            @if ($user_karyawan->ktp != null)
                            <a href="javascript:void(0)"
                                class="btn btn-sm btn-outline-secondary btn-rounded mx-2"
                                data-bs-toggle="offcanvas" data-bs-target="#offcanvas_lihat_ktp">
                                <i class="fa-regular fa-eye"></i></i>&nbsp;Lihat KTP
                            </a>
                            @endif

                            <input type="file" id="ktp_pic" class="@error('ktp_pic') is-invalid @enderror"
                                name="ktp_pic" value="" hidden>


                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-user',
                            'type' => 'text',
                            'name' => 'name',
                            'class' => 'name mt-4',
                            'label' => 'Nama Lengkap',
                            'value' => old('name', $user_karyawan->name),
                            'upper' => true,
                            ])

                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-building',
                            'type' => 'text',
                            'class' => 'tempat_lahir',
                            'name' => 'tempat_lahir',
                            'label' => 'Tempat Lahir',
                            'value' => old('tempat_lahir', $user_karyawan->tempat_lahir),
                            ])

                            <div class="section-title mt-4">Tanggal Lahir</div>
                            @include('users.profile.form_input', [
                            'icon' => 'fa-regular fa-calendar-days',
                            'type' => 'date',
                            'class' => 'tgl_lahir',
                            'name' => 'tgl_lahir',
                            'label' => 'Tanggal Lahir',
                            'value' => old('tgl_lahir', $user_karyawan->tgl_lahir),
                            ])

                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-star',
                            'name' => 'agama',
                            'class' => 'agama',
                            'label' => 'Pilih Agama',
                            'value' => $user_karyawan->agama,
                            'options' => [
                            '1' => 'ISLAM',
                            '2' => 'KRISTEN PROTESTAN',
                            '3' => 'KRISTEN KATOLIK',
                            '4' => 'HINDU',
                            '5' => 'BUDDHA',
                            '6' => 'KHONGHUCU',
                            ],
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-genderless',
                            'name' => 'gender',
                            'class' => 'gender',
                            'label' => 'Pilih Kelamin',
                            'value' => $user_karyawan->gender,
                            'options' => [
                            '1' => 'LAKI-LAKI',
                            '2' => 'PEREMPUAN',
                            ],
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-genderless',
                            'name' => 'status_nikah',
                            'label' => 'Pilih Status Nikah',
                            'class' => 'status_nikah',
                            'value' => $user_karyawan->status_nikah,
                            'options' => [
                            '1' => 'BELUM KAWIN',
                            '2' => 'SUDAH KAWIN',
                            '3' => 'CERAI HIDUP',
                            '4' => 'CERAI MATI',
                            ],
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-child',
                            'type' => 'number',
                            'name' => 'jumlah_anak',
                            'class' => 'jumlah_anak',
                            'label' => 'Jumlah Anak',
                            'value' => old('jumlah_anak', $user_karyawan->jumlah_anak),
                            ])

                            {{-- KARTU KELUARGA --}}
                            <div class="section-title mt-4">Kartu Keluarga</div>


                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-id-card',
                            'type' => 'number',
                            'name' => 'no_kk',
                            'class' => 'no_kk',
                            'label' => 'No. Kartu Keluarga',
                            'value' => old('no_kk', $user_karyawan->no_kk),
                            ])
                            <div class="section-title mt-4">Tanggal Terbit KK</div>

                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-calendar-days',
                            'type' => 'date',
                            'name' => 'tgl_terbit_kk',
                            'class' => 'tgl_terbit_kk',
                            'label' => 'Tanggal Terbit Kartu Keluargaa',
                            'value' => old(
                            'tgl_terbit_kk',
                            optional($user_karyawan->tgl_terbit_kk)->format('Y-m-d')),
                            ])

                            <div class="d-flex">
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-rounded"
                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvas_kk">
                                    <i class="fa-solid fa-upload"></i></i>&nbsp;Upload KK
                                </a>
                                <input type="file" id="kk_pic" name="kk_pic" value=""
                                    class="@error('kk_pic') is-invalid @enderror" hidden>
                                <input type="hidden" name="kk_old" id="kk_old"
                                    value="{{ $user_karyawan->kk }}">
                                <div id="group-button-kk"></div>

                                @if ($user_karyawan->kk != null)
                                <a href="javascript:void(0)"
                                    class="btn btn-sm btn-outline-secondary btn-rounded mx-2"
                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvas_lihat_kk">
                                    <i class="fa-regular fa-eye"></i>&nbsp;Lihat KK
                                </a>
                                @endif

                                {{-- @if ($user_karyawan->kk != null)
                                    <a type="button" href="{{ url('storage/kk/' . $user_karyawan->kk) }}" target="_blank"
                                class="btn btn-sm btn-primary btn-rounded">
                                <i class="fa-regular fa-floppy-disk me-1"></i></i> Lihat KK
                                </a>
                                @endif --}}

                            </div>
                            {{-- CONTACT --}}

                            <div class="section-title mt-4">Kontak</div>

                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-at',
                            'type' => 'email',
                            'name' => 'email',
                            'class' => 'email',
                            'label' => 'Email',
                            'value' => old('email', $user_karyawan->email),
                            ])

                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-phone',
                            'type' => 'number',
                            'name' => 'telepon',
                            'class' => 'telepon',
                            'label' => 'Telepon',
                            'value' => old('telepon', $user_karyawan->telepon),
                            ])

                            <div class="form-check modern-check mb-3">
                                <input class="form-check-input" type="checkbox" name="status_nomor"
                                    id="status_nomor" value="ya"
                                    {{ old('status_nomor', $user_karyawan->status_nomor) == 'ya' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Terhubung dengan WhatsApp
                                </label>
                            </div>

                            @include('users.profile.form_input', [
                            'icon' => 'fa-brands fa-whatsapp',
                            'type' => 'number',
                            'name' => 'nomor_wa',
                            'class' => 'nomor_wa',
                            'label' => 'Nomor WhatsApp',
                            'value' => old('nomor_wa', $user_karyawan->nomor_wa),
                            ])
                            <div class="section-title mt-4">Alasan Update Data</div>


                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-wind',
                            'type' => 'text',
                            'name' => 'alasan',
                            'class' => 'alasan',
                            'label' => 'Alasan Update Data',
                            'value' => old('alasan'),
                            ])
                            {{-- ACTION --}}
                            <div class="row text-center">
                                <div class="col-12">
                                    <a href="{{ url('profile/' . $menu_id) }}"
                                        class="btn btn-sm btn-outline-secondary btn-rounded" type="button">
                                        <i class="fa-solid fa-angle-left me-1"></i>KEMBALI
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary btn-rounded"
                                        id="btn_update_profile" @if (Auth::user()->user_aktif == 'NON AKTIF') disabled @endif>
                                        <i class="fa-regular fa-floppy-disk me-1"></i></i> UPDATE
                                    </button>
                                </div>
                            </div>
                        </form>
                        @else
                        Hapus editan yang belum di-approve HRD, atau konfirmasi ke HRD segera di-approve untuk
                        mengedit data diri anda
                        @endif
                        <div class="section-title mt-4">Pengajuan Update Data Diri</div>

                        <ul class="nav profile-tabs" id="profileTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pengajuan">
                                    <i class="fa-solid fa-upload"></i>
                                    <span>Pengajuan</span>
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#history_pengajuan">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>History</span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pengajuan">
                                <div class="section-title mt-4">Pengajuan</div>

                                @foreach ($karyawan_temp_null as $kt)
                                <a href="javascript:void(0)" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_pilih_profil{{ $kt->id }}"
                                    class="card stat-card hadir" style="background-color: #F0F8FF;">
                                    <div class="card-body d-flex">
                                        <div class="stat-icon p-1 rounded">
                                            <i class="fa-solid fa-user fa-4x" style="color: #6495ED;"></i>
                                        </div>
                                        <div class="text mx-4">
                                            <small class="text-muted" style="color: #FFFF;">Waktu :
                                                {{ $kt->created_at }}</small>
                                            <p class="stat-title mb-0" style="color: #6495ED;">{{ $kt->alasan }}
                                            </p>

                                            <small class="text-muted" style="color: #FFFF;">
                                                @if ($kt->status == null)
                                                <span class="badge bg-info">Belum Dilihat HRD</span>
                                                @elseif ($kt->status == 1)
                                                <span class="badge bg-success">Disetujui HRD</span>
                                                @elseif ($kt->status == 2)
                                                <span class="badge bg-warning">Ditolak HRD</span>
                                                @endif
                                            </small>
                                        </div>

                                        <div class="stat-content text-end">
                                            {{-- <div class="stat-icon p-2 rounded" style="background-color: #008B8B;">
                                            <i class="fa-regular fa-pen-to-square fa-1x" style="color: #7FFFD4;"></i>
                                        </div> --}}
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>

                            <div class="tab-pane fade" id="history_pengajuan">
                                <div class="section-title mt-4">History</div>

                                @foreach ($karyawan_temp as $kt)
                                <a href="javascript:void(0)" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_pilih_profil{{ $kt->id }}"
                                    class="card stat-card hadir" style="background-color: #F0F8FF;">
                                    <div class="card-body d-flex">
                                        <div class="stat-icon p-1 rounded">
                                            <i class="fa-solid fa-user fa-4x" style="color: #6495ED;"></i>
                                        </div>
                                        <div class="text mx-4">
                                            <small class="text-muted" style="color: #FFFF;">Waktu :
                                                {{ $kt->created_at }}</small>
                                            <p class="stat-title mb-0" style="color: #6495ED;">
                                                {{ $kt->alasan }}
                                            </p>

                                            <small class="text-muted" style="color: #FFFF;">
                                                @if ($kt->status == null)
                                                <span class="badge bg-info">Belum Dilihat HRD</span>
                                                @elseif ($kt->status == 1)
                                                <span class="badge bg-success">Disetujui HRD</span>
                                                @elseif ($kt->status == 2)
                                                <span class="badge bg-warning">Ditolak HRD</span>
                                                @endif
                                            </small>
                                        </div>

                                        <div class="stat-content text-end">
                                            {{-- <div class="stat-icon p-2 rounded" style="background-color: #008B8B;">
                                            <i class="fa-regular fa-pen-to-square fa-1x" style="color: #7FFFD4;"></i>
                                        </div> --}}
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="alamat">
                        @if ($karyawan_temp_alamat_jml == 0)
                        <form method="POST" id="form_update_alamat"
                            action="{{ url('profile/save_detail_alamat') }}">
                            @csrf
                            <input type="hidden" name="karyawan_id" value="{{ $user_karyawan->id }}">

                            {{-- BASIC INFO --}}
                            <div class="section-title">Alamat KTP</div>
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'provinsi',
                            'class' => 'select2_provinsi',
                            'label' => 'Pilih Provinsi',
                            'value' => $user_karyawan->provinsi,
                            'options' => $provinsi,
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'kabupaten',
                            'class' => 'select2_kabupaten',
                            'label' => 'Pilih Kabupaten/Kota',
                            'value' => $user_karyawan->kabupaten,
                            'options' => $kabupaten,
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'kecamatan',
                            'class' => 'select2_kecamatan',
                            'label' => 'Pilih Kecamatan',
                            'value' => $user_karyawan->kecamatan,
                            'options' => $kecamatan,
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'desa',
                            'class' => 'select2_desa',
                            'label' => 'Pilih Kelurahan/Desa',
                            'value' => $user_karyawan->desa,
                            'options' => $desa,
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-diamond-turn-right',
                            'type' => 'number',
                            'name' => 'rt',
                            'class' => 'rt',
                            'label' => 'RT',
                            'value' => old('rt', $user_karyawan->rt),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-diamond-turn-right',
                            'type' => 'number',
                            'name' => 'rw',
                            'class' => 'rw',
                            'label' => 'RW',
                            'value' => old('rw', $user_karyawan->rw),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-envelopes-bulk',
                            'type' => 'number',
                            'name' => 'kode_pos',
                            'class' => 'kode_pos',
                            'label' => 'Kode Pos',
                            'value' => old('kode_pos_domisili', $user_karyawan->kode_pos),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-road',
                            'type' => 'text',
                            'name' => 'detail_alamat',
                            'class' => 'detail_alamat',
                            'label' => 'Gang/ Jalan/ Dusun',
                            'value' => old('detail_alamat', $user_karyawan->detail_alamat),
                            ])
                            <div class="form-check modern-check mb-3">
                                <input class="form-check-input" type="checkbox" name="status_alamat"
                                    id="status_alamat" value="ya"
                                    {{ old('status_alamat', $user_karyawan->status_alamat) == 'ya' ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Alamat Domisili Sesuai KTP
                                </label>
                            </div>
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'provinsi_domisili',
                            'class' => 'select2_provinsi_domisili',
                            'label' => 'Pilih Provinsi',
                            'value' => $user_karyawan->provinsi_domisili,
                            'options' => $provinsi_domisili,
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'kabupaten_domisili',
                            'class' => 'select2_kabupaten_domisili',
                            'label' => 'Pilih Kabupaten/Kota',
                            'value' => $user_karyawan->kabupaten_domisili,
                            'options' => $kabupaten_domisili,
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'kecamatan_domisili',
                            'class' => 'select2_kecamatan_domisili',
                            'label' => 'Pilih Kecamatan',
                            'value' => $user_karyawan->kecamatan_domisili,
                            'options' => $kecamatan_domisili,
                            ])
                            @include('users.profile.form_select', [
                            'icon' => 'fa-solid fa-city',
                            'name' => 'desa_domisili',
                            'class' => 'select2_desa_domisili',
                            'label' => 'Pilih Kelurahan/Desa',
                            'value' => $user_karyawan->desa_domisili,
                            'options' => $desa_domisili,
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-diamond-turn-right',
                            'type' => 'number',
                            'name' => 'rt_domisili',
                            'class' => 'rt_domisili',
                            'label' => 'RT',
                            'value' => old('rt_domisili', $user_karyawan->rt_domisili),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-diamond-turn-right',
                            'type' => 'number',
                            'name' => 'rw_domisili',
                            'class' => 'rw_domisili',
                            'label' => 'RW',
                            'value' => old('rw_domisili', $user_karyawan->rw_domisili),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-envelopes-bulk',
                            'type' => 'number',
                            'name' => 'kode_pos_domisili',
                            'class' => 'kode_pos_domisili',
                            'label' => 'Kode Pos',
                            'value' => old('kode_pos_domisili', $user_karyawan->kode_pos_domisili),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-road',
                            'type' => 'text',
                            'name' => 'detail_alamat_domisili',
                            'class' => 'detail_alamat_domisili',
                            'label' => 'Gang/ Jalan/ Dusun',
                            'value' => old(
                            'detail_alamat_domisili',
                            $user_karyawan->detail_alamat_domisili),
                            ])

                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-wind',
                            'type' => 'text',
                            'name' => 'alasan',
                            'class' => 'alasan',
                            'label' => 'Alasan Update Alamat',
                            'value' => old('alasan'),
                            ])
                            <input type="hidden" name="menu_id" value="{{ $menu_id }}">

                            {{-- ACTION --}}
                            <div class="row text-center">
                                <div class="col-12">
                                    <a href="{{ url('profile/' . $menu_id) }}"
                                        class="btn btn-sm btn-outline-secondary btn-rounded" type="button">
                                        <i class="fa-solid fa-angle-left me-1"></i>KEMBALI
                                    </a>
                                    <button type="button" id="btn_update_alamat"
                                        class="btn btn-sm btn-primary btn-rounded"
                                        @if (Auth::user()->user_aktif == 'NON AKTIF') disabled @endif>
                                        <i class="fa-regular fa-floppy-disk me-1"></i></i> UPDATE
                                    </button>
                                </div>
                            </div>
                        </form>
                        @else
                        Hapus editan yang belum di-approve HRD, atau konfirmasi ke HRD segera di-approve untuk
                        mengedit alamat anda
                        @endif

                        <div class="section-title mt-4">Pengajuan Update Alamat</div>

                        <ul class="nav nav-tabs profile-tabs" id="alamatTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#pengajuan_alamat">
                                    <i class="fa-solid fa-upload"></i>
                                    <span>Pengajuan</span>
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#history_pengajuan_alamat">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>History</span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pengajuan_alamat">
                                <div class="section-title mt-4">Pengajuan</div>

                                @foreach ($karyawan_temp_alamat_null as $kt)
                                <a href="javascript:void(0)" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_pilih_alamat{{ $kt->id }}"
                                    class="card stat-card hadir" style="background-color: #F0F8FF;">
                                    <div class="card-body d-flex">
                                        <div class="stat-icon p-1 rounded">
                                            <i class="fa-solid fa-user fa-4x" style="color: #6495ED;"></i>
                                        </div>
                                        <div class="text mx-4">
                                            <small class="text-muted" style="color: #FFFF;">Waktu :
                                                {{ $kt->created_at }}</small>
                                            <p class="stat-title mb-0" style="color: #6495ED;">
                                                {{ $kt->alasan }}
                                            </p>

                                            <small class="text-muted" style="color: #FFFF;">
                                                @if ($kt->status == null)
                                                <span class="badge bg-info">Belum Dilihat HRD</span>
                                                @elseif ($kt->status == 1)
                                                <span class="badge bg-success">Disetujui HRD</span>
                                                @elseif ($kt->status == 2)
                                                <span class="badge bg-warning">Ditolak HRD</span>
                                                @endif
                                            </small>
                                        </div>

                                        <div class="stat-content text-end">
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>

                            <div class="tab-pane fade" id="history_pengajuan_alamat">
                                <div class="section-title mt-4">History</div>

                                @foreach ($karyawan_temp_alamat as $kt)
                                <a href="javascript:void(0)" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_pilih_alamat{{ $kt->id }}"
                                    class="card stat-card hadir" style="background-color: #F0F8FF;">
                                    <div class="card-body d-flex">
                                        <div class="stat-icon p-1 rounded">
                                            <i class="fa-solid fa-user fa-4x" style="color: #6495ED;"></i>
                                        </div>
                                        <div class="text mx-4">
                                            <small class="text-muted" style="color: #FFFF;">Waktu :
                                                {{ $kt->created_at }}</small>
                                            <p class="stat-title mb-0" style="color: #6495ED;">
                                                {{ $kt->alasan }}
                                            </p>

                                            <small class="text-muted" style="color: #FFFF;">
                                                @if ($kt->status == null)
                                                <span class="badge bg-info">Belum Dilihat HRD</span>
                                                @elseif ($kt->status == 1)
                                                <span class="badge bg-success">Disetujui HRD</span>
                                                @elseif ($kt->status == 2)
                                                <span class="badge bg-warning">Ditolak HRD</span>
                                                @endif
                                            </small>
                                        </div>

                                        <div class="stat-content text-end">
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div class="tab-pane fade" id="pendidikan">
                        <div class="section-title mt-4">Tambah Riwayat Pendidikan</div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-rounded"
                            data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pendidikan">
                            <i class="fa-regular fa-floppy-disk me-1"></i></i>Tambah
                        </a>
                        {{-- @if ($user_karyawan->ijazah != null)
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary btn-rounded mx-2"
                                    data-bs-toggle="offcanvas" data-bs-target="#offcanvas_lihat_ijazah">
                                    <i class="fa-regular fa-floppy-disk me-1"></i></i> Lihat Ijazah
                                </a>
                            @endif --}}
                        <div class="section-title mt-4"></div>
                        @foreach ($pendidikan as $pend)
                        <a href="javascript:void(0)" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvas_pilih_pendidikan{{ $pend->id_pendidikan }}"
                            class="card stat-card hadir" style="background-color: #FAEBD7;">
                            <div class="card-body d-flex">
                                <div class="stat-icon p-1 rounded">
                                    <i class="fa-solid fa-user-graduate fa-4x" style="color: #B8860B;"></i>
                                </div>
                                <div class="text mx-4">
                                    <h8 class="stat-title mb-0" style="color: #B8860B;">
                                        {{ $pend->jurusan }}
                                    </h8>
                                    <h6 class="stat-title mb-0" style="color: #B8860B;">{{ $pend->institusi }}
                                    </h6>

                                    <small class="text-muted"
                                        style="color: #FFFF;">({{ $pend->tanggal_masuk }}-{{ $pend->tanggal_keluar ?? '...' }})</small>
                                </div>

                                <div class="stat-content text-end">
                                    {{-- <div class="stat-icon p-2 rounded" style="background-color: #008B8B;">
                                            <i class="fa-regular fa-pen-to-square fa-1x" style="color: #7FFFD4;"></i>
                                        </div> --}}
                                </div>
                            </div>
                        </a>
                        @endforeach
                        <div class="section-title mt-4">Upload Dokumen Pendidikan</div>
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-rounded"
                            data-bs-toggle="offcanvas" data-bs-target="#offcanvas_dokumen">
                            <i class="fa-solid fa-upload"></i>&nbsp;Upload
                        </a>
                        @if ($user_karyawan->ijazah != null)
                        <div class="section-title mt-4">Dokumen</div>
                        <a href="{{ url('profile/download_ijazah/' . $user_karyawan->ijazah) }}"
                            class="btn btn-sm btn-outline-secondary btn-rounded" type="button">
                            <i class="fa-regular fa-floppy-disk me-1"></i></i> Ijazah
                        </a>
                        @endif
                        @if ($user_karyawan->transkrip_nilai != null)
                        <a href="{{ url('profile/download_transkrip_nilai/' . $user_karyawan->transkrip_nilai) }}"
                            class="btn btn-sm btn-outline-secondary btn-rounded mx-2" type="button">
                            <i class="fa-regular fa-floppy-disk me-1"></i></i> Transkrip
                        </a>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="kontak_kantor">
                        <div class="section-title mt-4">Tambahkan Kontak Kantor(Opsional)</div>
                        <div class="section-title mt-4"></div>
                        <form method="POST" id="form_update_kontak_kantor" action="{{ url('profile/save_detail_kontak_kantor') }}">
                            @csrf
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-envelope',
                            'type' => 'email',
                            'name' => 'email_kantor',
                            'class' => 'email_kantor',
                            'label' => 'Email Kantor',
                            'value' => old('email_kantor', $user_karyawan->email_kantor),
                            ])
                            @include('users.profile.form_input', [
                            'icon' => 'fa-solid fa-phone',
                            'type' => 'number',
                            'name' => 'nomor_wa_kantor',
                            'class' => 'nomor_wa_kantor',
                            'label' => 'Nomer Kantor',
                            'value' => old('nomor_wa_kantor', $user_karyawan->nomor_wa_kantor),
                            ])
                            {{-- ACTION --}}
                            <input type="hidden" name="karyawan_id" value="{{ $user_karyawan->id }}">
                            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
                            <div class="row text-center">
                                <div class="col-12">
                                    <a href="{{ url('profile/' . $menu_id) }}"
                                        class="btn btn-sm btn-outline-secondary btn-rounded" type="button">
                                        <i class="fa-solid fa-angle-left me-1"></i>KEMBALI
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary btn-rounded"
                                        id="btn_update_kontak_kantor" @if (Auth::user()->user_aktif == 'NON AKTIF') disabled @endif>
                                        <i class="fa-regular fa-floppy-disk me-1"></i></i> UPDATE
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@section('content')
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_logout" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h6 class="title">KONFIRMASI</h6>
        <p>Apakah Anda Ingin Keluar Dari Aplikasi Ini?</p>
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
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_konfirmasi_simpan"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Konfirmasi</h5>
        <p>Apakah anda ingin menyimpan data ini?</p>
        <button type="button" id="btn_simpan" class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-regular fa-floppy-disk"></i>
            &nbsp;Simpan
        </button>
        <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
            aria-label="Close">
            &nbsp;Batal
        </a>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_konfirmasi_simpan_kontak_kantor"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Konfirmasi</h5>
        <p>Apakah anda ingin menyimpan data ini?</p>
        <button type="button" id="btn_simpan_kontak_kantor" class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-regular fa-floppy-disk"></i>
            &nbsp;Simpan
        </button>
        <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
            aria-label="Close">
            &nbsp;Batal
        </a>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_konfirmasi_simpan_alamat"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Konfirmasi</h5>
        <p>Apakah anda ingin menyimpan data ini?</p>
        <button type="button" id="btn_simpan_alamat" class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-regular fa-floppy-disk"></i>
            &nbsp;Simpan
        </button>
        <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
            aria-label="Close">
            &nbsp;Batal
        </a>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_ktp" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Pilih Metode</h5>
        <p>Pilih Menggunakan Gallery Atau Kamera</p>
        <button type="button" class="btn btn-sm btn-info light pwa-btn " id="btn_gallery">
            <i class="fa-regular fa-images"></i>
            &nbsp;Gallery
        </button>
        {{-- <a id="btn_klik" href="{{ url('profile/change_photoprofile_camera/' . $menu_id) }}"
        class="btn btn-sm light btn-primary ms-2">
        <i class="fa-solid fa-camera"></i>
        &nbsp;Camera
        </a> --}}
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_lihat_ktp"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Gambar KTP</h5>
        {{-- <p>Pilih Menggunakan Gallery Atau Kamera</p> --}}
        <img src="{{ asset('storage/ktp/' . $user_karyawan->ktp) }}" id="template_ktp" max-height="200"
            height="200" class="rounded" alt="">
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_konfirmasi_ktp"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Konfirmasi</h5>
        <p>Konfirmasi gambar asli yang dipilih</p>
        <img src="" id="preview_gallery" alt="">
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/save_capture_profile') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="col-md-6">
                <div id="results"></div>
                <img id="image_crop_ktp" class="img-fluid rounded">
                <input type="hidden" name="karyawan_id" value="{{ $user_karyawan->id }}">
                <input type="file" hidden id="ktp" name="ktp" value="" accept="image/jpeg,image/png">
                {{-- <input type="text" id="ktp_pic" name="ktp_pic" value="" hidden> --}}
                {{-- <input type="text" name="ktp_old" value="{{ $user_karyawan->ktp }}" hidden> --}}
            </div>
            <br>
            <button type="button" id="btn_simpan_ktp" class="btn btn-sm btn-info light pwa-btn">
                <i class="fa-regular fa-floppy-disk"></i>
                &nbsp;Simpan
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_kk" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Pilih Metode</h5>
        <p>Pilih Menggunakan Gallery Atau Kamera</p>
        <button type="button" class="btn btn-sm btn-info light pwa-btn " id="btn_gallery_kk">
            <i class="fa-regular fa-images"></i>
            &nbsp;Gallery
        </button>
        {{-- <a id="btn_klik" href="{{ url('profile/change_photoprofile_camera/' . $menu_id) }}"
        class="btn btn-sm light btn-primary ms-2">
        <i class="fa-solid fa-camera"></i>
        &nbsp;Camera
        </a> --}}
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_konfirmasi_kk"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Konfirmasi</h5>
        <p>Konfirmasi gambar asli yang dipilih</p>
        <img src="" id="preview_gallery" alt="">
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/save_capture_profile') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="col-md-6">
                <div id="results"></div>
                <img id="image_crop_kk" class="img-fluid rounded">
                <input type="hidden" name="karyawan_id" value="{{ $user_karyawan->id }}">
                <input type="file" hidden id="kk" name="kk" value="" accept="image/jpeg,image/png">
                {{-- <input type="text" id="kk_pic" name="kk_pic" value="" hidden> --}}
                {{-- <input type="text" name="kk_old" value="{{ $user_karyawan->kk }}" hidden> --}}
            </div>
            <br>
            <button type="button" id="btn_simpan_kk" class="btn btn-sm btn-info light pwa-btn">
                <i class="fa-regular fa-floppy-disk"></i>
                &nbsp;Simpan
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_lihat_kk"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Gambar KK</h5>
        {{-- <p>Pilih Menggunakan Gallery Atau Kamera</p> --}}
        <img src="{{ asset('storage/kk/' . $user_karyawan->kk) }}" id="template_kk" max-height="200" height="200"
            class="rounded" alt="">
    </div>
</div>
{{-- pendidikan --}}
{{-- create --}}
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_pendidikan"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Pendidikan</h5>
        <p>Tambahkan Riwayat Pendidikan</p>
        <div class="col-md-4">
            <div class="preview"></div>
        </div>
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/save_pendidikan') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <div class="col-md-6">
                @include('users.profile.form_input', [
                'icon' => 'fa-solid fa-building',
                'type' => 'text',
                'name' => 'institusi',
                'class' => 'name mt-4',
                'label' => 'Institusi',
                'value' => '',
                'upper' => true,
                ])
                @include('users.profile.form_select', [
                'icon' => 'fa-solid fa-stairs',
                'name' => 'jenjang',
                'label' => 'PILIH JENJANG',
                'class' => 'jenjang',
                'value' => '',
                'options' => [
                'SD' => 'SD',
                'SMP' => 'SMP',
                'SMA' => 'SMA',
                'SMK' => 'SMK',
                'D1' => 'D1',
                'D2' => 'D2',
                'D3' => 'D3',
                'D4' => 'D4',
                'S1' => 'S1',
                'S2' => 'S2',
                'S3' => 'S3',
                ],
                ])
                @include('users.profile.form_input', [
                'icon' => 'fa-solid fa-user-graduate',
                'type' => 'text',
                'name' => 'jurusan',
                'class' => 'name mt-4',
                'label' => 'JURUSAN',
                'value' => '',
                'upper' => true,
                ])
                @include('users.profile.form_select', [
                'icon' => 'fa-regular fa-calendar-days',
                'name' => 'tanggal_masuk',
                'class' => 'tanggal_masuk',
                'label' => 'TAHUN MASUK',
                'value' => '',
                'options' => array_combine(range(date('Y'), 1980), range(date('Y'), 1980)),
                ])
                @include('users.profile.form_select', [
                'icon' => 'fa-regular fa-calendar-days',
                'name' => 'tanggal_keluar',
                'class' => 'tanggal_keluar',
                'label' => 'TAHUN KELUAR',
                'value' => '',
                'options' => array_combine(range(date('Y'), 1980), range(date('Y'), 1980)),
                ])
            </div>
            <br>
            <button type="submit" class="btn btn-sm btn-info light pwa-btn">
                <i class="fa-regular fa-floppy-disk"></i>
                &nbsp;Simpan
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-danger ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
{{-- pilih update / hapus --}}
@foreach ($pendidikan as $pend)
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_pilih_pendidikan{{ $pend->id_pendidikan }}"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Ubah Pendidikan</h5>
        <p>Update / hapus data pendidikan</p>
        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas_update_pendidikan{{ $pend->id_pendidikan }}"
            class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-regular fa-floppy-disk"></i>
            &nbsp;Update
        </a>
        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas_delete_pendidikan{{ $pend->id_pendidikan }}"
            class="btn btn-sm light btn-danger ms-2">
            <i class="fa-solid fa-trash-can"></i>
            &nbsp;Hapus
        </a>
    </div>
</div>

<div class="offcanvas offcanvas-bottom" tabindex="-1"
    id="offcanvas_update_pendidikan{{ $pend->id_pendidikan }}" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Ubah Pendidikan</h5>
        <p>Update / hapus data pendidikan</p>
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/update_pendidikan') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="id_pendidikan" value="{{ $pend->id_pendidikan }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <div class="col-md-6">
                @include('users.profile.form_input', [
                'icon' => 'fa-solid fa-building',
                'type' => 'text',
                'name' => 'institusi',
                'class' => 'name mt-4',
                'label' => 'Institusi',
                'value' => $pend->institusi,
                'upper' => true,
                ])
                @include('users.profile.form_select', [
                'icon' => 'fa-solid fa-stairs',
                'name' => 'jenjang',
                'label' => 'PILIH JENJANG',
                'class' => 'jenjang',
                'value' => $pend->jenjang,
                'options' => [
                'SD' => 'SD',
                'SMP' => 'SMP',
                'SMA' => 'SMA',
                'SMK' => 'SMK',
                'D1' => 'D1',
                'D2' => 'D2',
                'D3' => 'D3',
                'D4' => 'D4',
                'S1' => 'S1',
                'S2' => 'S2',
                'S3' => 'S3',
                ],
                ])
                @include('users.profile.form_input', [
                'icon' => 'fa-solid fa-user-graduate',
                'type' => 'text',
                'name' => 'jurusan',
                'class' => 'name mt-4',
                'label' => 'JURUSAN',
                'value' => $pend->jurusan,
                'upper' => true,
                ])
                @include('users.profile.form_select', [
                'icon' => 'fa-regular fa-calendar-days',
                'name' => 'tanggal_masuk',
                'class' => 'tanggal_masuk',
                'label' => 'TAHUN MASUK',
                'value' => $pend->tanggal_masuk,
                'options' => array_combine(range(date('Y'), 1980), range(date('Y'), 1980)),
                ])
                @include('users.profile.form_select', [
                'icon' => 'fa-regular fa-calendar-days',
                'name' => 'tanggal_keluar',
                'class' => 'tanggal_keluar',
                'label' => 'TAHUN KELUAR',
                'value' => $pend->tanggal_keluar,
                'options' => array_combine(range(date('Y'), 1980), range(date('Y'), 1980)),
                ])
            </div>
            <br>
            <button type="submit" class="btn btn-sm btn-info light pwa-btn">
                <i class="fa-regular fa-floppy-disk"></i>
                &nbsp;Simpan
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-danger ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1"
    id="offcanvas_delete_pendidikan{{ $pend->id_pendidikan }}" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Ubah Pendidikan</h5>
        <p>Update / hapus data pendidikan</p>
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/delete_pendidikan') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="id_pendidikan" value="{{ $pend->id_pendidikan }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <br>
            <button type="submit" class="btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-trash-can"></i>
                &nbsp;Hapus
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
@endforeach
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_dokumen"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Tambah Dokumen Pendidikan</h5>
        <p>Ijazah Asli dan Transkrip Nilai</p>
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/dokumen_pendidikan') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <div class="form-group mb-3">
                <div class="section-title mt-4">Ijazah Asli PDF</div>
                <div class="input-group border rounded-3 p-1 align-items-center bg-white shadow-sm">
                    <span class="input-group-text border-0 bg-transparent text-primary">
                        <i class="fa-solid fa-file-pdf fa-lg"></i>
                    </span>
                    <input type="file" name="ijazah"
                        class="form-control border-0 custom-file-input @error('ijazah') is-invalid @enderror"
                        accept=".pdf" id="ijazah">
                    <input type="hidden" name="ijazah_old" value="{{ $user_karyawan->ijazah }}">
                </div>
            </div>
            <div class="form-group mb-3">
                <div class="section-title mt-4">Transkrip Nilai PDF</div>
                <div class="input-group border rounded-3 p-1 align-items-center bg-white shadow-sm">
                    <span class="input-group-text border-0 bg-transparent text-primary">
                        <i class="fa-solid fa-file-pdf fa-lg"></i>
                    </span>
                    <input type="file" name="transkrip_nilai"
                        class="form-control border-0 custom-file-input @error('transkrip_nilai') is-invalid @enderror"
                        accept=".pdf" id="transkrip_nilai">
                    <input type="hidden" name="transkrip_nilai_old" value="{{ $user_karyawan->transkrip_nilai }}">
                </div>
            </div>
            <div class="section-title mt-4">IPK (ketik 0 jika tidak ada)</div>
            @include('users.profile.form_input', [
            'icon' => 'fa-regular fa-gem',
            'type' => 'text',
            'class' => 'ipk',
            'name' => 'ipk',
            'label' => 'IPK',
            'value' => old('ipk', $user_karyawan->ipk),
            ])
            <br>
            <button type="submit" class="btn btn-sm btn-info light pwa-btn">
                <i class="fa-regular fa-floppy-disk"></i>
                &nbsp;Simpan
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
{{-- end pendidikan --}}

{{-- profil --}}

@foreach ($karyawan_temp as $kt)
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_pilih_profil{{ $kt->id }}"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Data Profile</h5>
        <p>Lihat / hapus pengajuan ubah profil</p>
        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas_lihat_profil{{ $kt->id }}"
            class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-regular fa-eye"></i>
            &nbsp;Lihat
        </a>
        @if ($kt->status == null)
        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas_delete_profil{{ $kt->id }}"
            class="btn btn-sm light btn-danger ms-2">
            <i class="fa-solid fa-trash-can"></i>
            &nbsp;Hapus
        </a>
        @endif

    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_lihat_profil{{ $kt->id }}"
    aria-labelledby="offcanvasBottomLabel">
    {{-- <div class="offcanvas-body text-center small">
                <h5 class="title">Data Profile</h5>
                <p>Perubahan yang diajukan</p>
            </div> --}}
    <div class="offcanvas-body small">

        <div class="section-title mt-2">No. KTP</div>
        <div>{{ $kt->nik }}</div>
        <div class="section-title mt-2">KTP</div>
        <img src="{{ asset('storage/ktp/' . $kt->ktp) }}" id="template_ktp" max-height="100" height="100"
            class="rounded" alt="">
        <div class="section-title mt-2">Nama Lengkap</div>
        <div>{{ $kt->name }}</div>
        <div class="section-title mt-2">Tempat Lahir</div>
        <div>{{ $kt->tempat_lahir }}</div>
        <div class="section-title mt-2">Tanggal Lahir</div>
        <div>{{ $kt->tgl_lahir }}</div>
        <div class="section-title mt-2">Agama</div>
        @if ($kt->agama == 1)
        <div>ISLAM</div>
        @elseif ($kt->agama == 2)
        <div>KRISTEN PROTESTAN</div>
        @elseif ($kt->agama == 3)
        <div>KRISTEN KATOLIK</div>
        @elseif ($kt->agama == 4)
        <div>HINDU</div>
        @elseif ($kt->agama == 5)
        <div>BUDDHA</div>
        @elseif ($kt->agama == 6)
        <div>KHONGHUCU</div>
        @endif
        <div class="section-title mt-2">Jenis Kelamin</div>
        @if ($kt->gender == 1)
        <div>LAKI-LAKI</div>
        @elseif ($kt->gender == 2)
        <div>PEREMPUAN</div>
        @endif
        <div class="section-title mt-2">Status Perkawinan</div>
        @if ($kt->status_nikah == 1)
        <div>BELUM KAWIN</div>
        @elseif ($kt->status_nikah == 2)
        <div>KAWIN</div>
        @elseif ($kt->status_nikah == 3)
        <div>CERAI HIDUP</div>
        @elseif ($kt->status_nikah == 4)
        <div>CERAI MATI</div>
        @endif
        <div class="section-title mt-2">No. KK</div>
        <div>{{ $kt->no_kk }}</div>
        <div class="section-title mt-2">KK</div>
        <img src="{{ asset('storage/kk/' . $kt->kk) }}" id="template_kk" max-height="100" height="100"
            class="rounded" alt="">
        <div class="section-title mt-2">Tanggal Terbit KK</div>
        <div>{{ optional($kt->tgl_terbit_kk)->format('Y-m-d') }}</div>
        <div class="section-title mt-2">Email</div>
        <div>{{ $kt->email }}</div>
        <div class="section-title mt-2">No. Whatsapp</div>
        <div>{{ $kt->telepon }}</div>
        <div class="section-title mt-2">Alasan Update</div>
        <div>{{ $kt->alasan }}</div>
        <div class="section-title mt-2">Status</div>
        @if ($kt->status == null)
        <div>BELUM DILIHAT HRD</div>
        @elseif ($kt->status == 1)
        <div>DISETUJUI HRD</div>
        @elseif ($kt->status == 2)
        <div>DITOLAK HRD</div>
        @endif
    </div>
    <div class="offcanvas-body text-center small">

        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-solid fa-shop-slash"></i>
            &nbsp;Tutup
        </a>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_delete_profil{{ $kt->id }}"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Hapus</h5>
        <p>Hapus pengajuan ubah profil</p>
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/delete_profil') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="id" value="{{ $kt->id }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <input type="hidden" name="kk" value="{{ $kt->kk }}">
            <input type="hidden" name="ktp" value="{{ $kt->ktp }}">
            <br>
            <button type="submit" class="btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-trash-can"></i>
                &nbsp;Hapus
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
@endforeach

{{-- end profil --}}
{{-- alamat  --}}
@foreach ($karyawan_temp_alamat as $kt)
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_pilih_alamat{{ $kt->id }}"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Data Alamat</h5>
        <p>Lihat / hapus pengajuan ubah alamat</p>
        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas_lihat_alamat{{ $kt->id }}"
            class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-regular fa-eye"></i>
            &nbsp;Lihat
        </a>
        @if ($kt->status == null)
        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvas_delete_alamat{{ $kt->id }}"
            class="btn btn-sm light btn-danger ms-2">
            <i class="fa-solid fa-trash-can"></i>
            &nbsp;Hapus
        </a>
        @endif

    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_lihat_alamat{{ $kt->id }}"
    aria-labelledby="offcanvasBottomLabel">
    {{-- <div class="offcanvas-body text-center small">
                <h5 class="title">Data Profile</h5>
                <p>Perubahan yang diajukan</p>
            </div> --}}
    <div class="offcanvas-body small">
        <div class="section-title mt-2">RT</div>
        <div>{{ $kt->rt }}</div>
        <div class="section-title mt-2">RW</div>
        <div>{{ $kt->rw }}</div>
        <div class="section-title mt-2">Desa</div>
        <div>{{ $kt->Desa->name }}</div>
        <div class="section-title mt-2">Kecamatan</div>
        <div>{{ $kt->Kecamatan->name }}</div>
        <div class="section-title mt-2">Kabupaten</div>
        <div>{{ $kt->Kabupaten->name }}</div>
        <div class="section-title mt-2">Provinsi</div>
        <div>{{ $kt->Provinsi->name }}</div>
        <div class="section-title mt-2">Detail Alamat</div>
        <div>{{ $kt->detail_alamat }}</div>
        @if ($kt->status_alamat == null)
        <div class="section-title mt-2">RT Domisili</div>
        <div>{{ $kt->rt_domisili }}</div>
        <div class="section-title mt-2">RW Domisili</div>
        <div>{{ $kt->rw_domisili }}</div>
        <div class="section-title mt-2">Desa Domisili</div>
        <div>{{ $kt->DesaNOW->name }}</div>
        <div class="section-title mt-2">Kecamatan Domisili</div>
        <div>{{ $kt->KecamatanNOW->name }}</div>
        <div class="section-title mt-2">Kabupaten Domisili</div>
        <div>{{ $kt->KabupatenNOW->name }}</div>
        <div class="section-title mt-2">Provinsi Domisili</div>
        <div>{{ $kt->ProvinsiNOW->name }}</div>
        <div class="section-title mt-2">Detail Alamat Domisilis</div>
        <div>{{ $kt->detail_alamat_domisili }}</div>
        @endif

        <div class="section-title mt-2">Status Pengajuan</div>
        @if ($kt->status == null)
        <div>BELUM DILIHAT HRD</div>
        @elseif ($kt->status == 1)
        <div>DISETUJUI HRD</div>
        @elseif ($kt->status == 2)
        <div>DITOLAK HRD</div>
        @endif
    </div>
    <div class="offcanvas-body text-center small">

        <a href="javascript:void(0);" type="button" data-bs-toggle="offcanvas"
            class="btn btn-sm btn-info light pwa-btn">
            <i class="fa-solid fa-shop-slash"></i>
            &nbsp;Tutup
        </a>
    </div>
</div>
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvas_delete_alamat{{ $kt->id }}"
    aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-body text-center small">
        <h5 class="title">Hapus</h5>
        <p>Hapus pengajuan ubah profil</p>
        <form method="POST" id="form_konfirmasi" action="{{ url('profile/delete_alamat') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_karyawan" value="{{ $user_karyawan->id }}">
            <input type="hidden" name="id" value="{{ $kt->id }}">
            <input type="hidden" name="menu_id" value="{{ $menu_id }}">
            <br>
            <button type="submit" class="btn btn-sm btn-danger light pwa-btn">
                <i class="fa-solid fa-trash-can"></i>
                &nbsp;Hapus
            </button>
            <a href="javascript:void(0);" class="btn btn-sm light btn-primary ms-2" data-bs-dismiss="offcanvas"
                aria-label="Close">
                &nbsp;Batal
            </a>
        </form>
    </div>
</div>
@endforeach
{{-- end alamat --}}
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script>
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            // Reinitialize JS
            Swal.close();
        }
    });
    var session_success = "{{ session('profile_update_success') }}";
    var session_error = "{{ session('profile_update_error') }}";
    // console.log(session_success, session_error);
    if (session_success) {
        $('.alert_profile').stop(true, true).hide().html(
            '<div class="alert alert-success light alert-lg alert-dismissible fade show">' +
            '<i class="fa-solid fa-circle-check fa-bounce me-2"></i>' +
            '<strong>Sukses!</strong>&nbsp;' + session_success +
            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>').fadeIn();
        setTimeout(function() {
            $('.alert_profile').fadeOut('slow');
        }, 10000);
    } else if (session_error) {
        $('.alert_profile').stop(true, true).hide().html(
            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
            '<i class="fa-solid fa-circle-exclamation fa-bounce me-2"></i>' +
            '<strong>Error!</strong>&nbsp;' + session_error +
            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>').fadeIn();
        setTimeout(function() {
            $('.alert_profile').fadeOut('slow');
        }, 10000);
    }
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
</script>
<script>
    $(document).ready(function() {
        $('#provinsi', '#provinsi_domisili').select2({
            // theme: 'bootstrap-5',
            placeholder: "Pilih Provinsi",
            allowClear: true,
        });
        $('#kabupaten', '#kabupaten_domisili').select2({
            // theme: 'bootstrap-5',
            placeholder: "Pilih Kabupaten/Kota",
            allowClear: true,
        });
        $('#kecamatan', '#kecamatan_domisili').select2({
            // theme: 'bootstrap-5',
            placeholder: "Pilih Kecamatan",
            allowClear: true,
        });
        $('#desa', '#desa_domisili').select2({
            // theme: 'bootstrap-5',
            placeholder: "Pilih Kelurahan/Desa",
            allowClear: true,
        });
        var status_nomor = "{{ old('status_nomor', $user_karyawan->status_nomor) }}";
        // console.log(status_nomor);
        if (status_nomor == 'ya') {
            $('.nomor_wa').hide();
        } else if (status_nomor == 'tidak') {
            $('.nomor_wa').show();
        }
        var status_nomor = "{{ old('status_nomor', $user_karyawan->status_nomor) }}";
        // console.log(status_nomor);
        if (status_nomor == 'ya') {
            $('.nomor_wa').hide();
        } else if (status_nomor == 'tidak') {
            $('.nomor_wa').show();
        }
        var status_alamat = "{{ old('status_alamat', $user_karyawan->status_alamat) }}";
        // console.log(status_alamat);
        if (status_alamat == 'ya') {
            $('.select2_provinsi_domisili').hide();
            $('.select2_kabupaten_domisili').hide();
            $('.select2_kecamatan_domisili').hide();
            $('.select2_desa_domisili').hide();
            $('.rt_domisili').hide();
            $('.rw_domisili').hide();
            $('.detail_alamat_domisili').hide();

        } else if (status_alamat == 'tidak') {
            $('.select2_provinsi_domisili').show();
            $('.select2_kabupaten_domisili').show();
            $('.select2_kecamatan_domisili').show();
            $('.select2_desa_domisili').show();
            $('.rt_domisili').show();
            $('.rw_domisili').show();
            $('.detail_alamat_domisili').show();
        }
        var status_nikah = "{{ old('status_alamat', $user_karyawan->status_nikah) }}";
        // console.log(status_nikah);
        if (status_nikah == '1') {
            $('.jumlah_anak').hide();

        } else {
            $('.jumlah_anak').show();
        }
        $('#status_nikah').on('change', function() {
            let value = $(this).val();
            if (value == '1') {
                $('.jumlah_anak').hide();
            } else {
                $('.jumlah_anak').show();
            }
        });
        $('#status_alamat').on('click', function() {
            var isChecked = $(this).is(':checked')
            // console.log('ok');
            if (isChecked) {
                $('.select2_provinsi_domisili').hide();
                $('.select2_kabupaten_domisili').hide();
                $('.select2_kecamatan_domisili').hide();
                $('.select2_desa_domisili').hide();
                $('.rt_domisili').hide();
                $('.rw_domisili').hide();
                $('.detail_alamat_domisili').hide();
            } else {
                $('.select2_provinsi_domisili').show();
                $('.select2_kabupaten_domisili').show();
                $('.select2_kecamatan_domisili').show();
                $('.select2_desa_domisili').show();
                $('.rt_domisili').show();
                $('.rw_domisili').show();
                $('.detail_alamat_domisili').show();
            }
        });
        $('#status_nomor').on('click', function() {
            var isChecked = $(this).is(':checked')
            // console.log('ok');
            if (isChecked) {
                $('.nomor_wa').hide();
            } else {
                $('.nomor_wa').show();
            }
        });
        $('#provinsi').on('change', function() {
            let id_provinsi = $(this).val();
            $('#kabupaten')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');
            $('#kecamatan')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');
            $('#desa')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');

            if (!id_provinsi) return;

            let url = "{{ url('/profile/detail_profile/get_kabupaten') }}" + "/" + id_provinsi;
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
                    if (response.code == 200) {
                        let options = '<option value="">Pilih Kabupaten</option>';

                        $.each(response.data, function(i, item) {
                            options +=
                                `<option value="${item.code}">${item.name}</option>`;
                        });

                        $('#kabupaten')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');

                    } else {
                        $('.alert_profile').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            '<i class="fa-solid fa-circle-exclamation"></i>' +
                            '<strong>Error!</strong>&nbsp;' + response.data +
                            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                        ).fadeIn();
                        setTimeout(function() {
                            $('.alert_profile').fadeOut('slow');
                        }, 10000);
                    }
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        $('#kabupaten').on('change', function() {
            let id_kabupaten = $(this).val();
            $('#kecamatan')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');

            if (!id_kabupaten) return;

            let url = "{{ url('/profile/detail_profile/get_kecamatan') }}" + "/" + id_kabupaten;
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
                    if (response.code == 200) {
                        let options = '<option value="">Pilih Kecamatan</option>';

                        $.each(response.data, function(i, item) {
                            options +=
                                `<option value="${item.code}">${item.name}</option>`;
                        });

                        $('#kecamatan')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');

                    } else {
                        $('.alert_profile').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            '<i class="fa-solid fa-circle-exclamation"></i>' +
                            '<strong>Error!</strong>&nbsp;' + response.data +
                            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                        ).fadeIn();
                        setTimeout(function() {
                            $('.alert_profile').fadeOut('slow');
                        }, 10000);
                    }
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        $('#kecamatan').on('change', function() {
            let id_kecamatan = $(this).val();
            $('#desa')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');

            if (!id_kecamatan) return;

            let url = "{{ url('/profile/detail_profile/get_desa') }}" + "/" + id_kecamatan;
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
                    if (response.code == 200) {
                        let options = '<option value="">Pilih Desa</option>';

                        $.each(response.data, function(i, item) {
                            options +=
                                `<option value="${item.code}">${item.name}</option>`;
                        });

                        $('#desa')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');

                    } else {
                        $('.alert_profile').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            '<i class="fa-solid fa-circle-exclamation"></i>' +
                            '<strong>Error!</strong>&nbsp;' + response.data +
                            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                        ).fadeIn();
                        setTimeout(function() {
                            $('.alert_profile').fadeOut('slow');
                        }, 10000);
                    }
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        // alamat domisili
        $('#provinsi_domisili').on('change', function() {
            let id_provinsi = $(this).val();
            $('#kabupaten_domisili')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');
            $('#kecamatan_domisili')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');
            $('#desa_domisili')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');

            if (!id_provinsi) return;

            let url = "{{ url('/profile/detail_profile/get_kabupaten') }}" + "/" + id_provinsi;
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
                    if (response.code == 200) {
                        let options = '<option value="">Pilih Kabupaten</option>';

                        $.each(response.data, function(i, item) {
                            options +=
                                `<option value="${item.code}">${item.name}</option>`;
                        });

                        $('#kabupaten_domisili')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');

                    } else {
                        $('.alert_profile').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            '<i class="fa-solid fa-circle-exclamation"></i>' +
                            '<strong>Error!</strong>&nbsp;' + response.data +
                            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                        ).fadeIn();
                        setTimeout(function() {
                            $('.alert_profile').fadeOut('slow');
                        }, 10000);
                    }
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        $('#kabupaten_domisili').on('change', function() {
            let id_kabupaten = $(this).val();
            $('#kecamatan_domisili')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');

            if (!id_kabupaten) return;

            let url = "{{ url('/profile/detail_profile/get_kecamatan') }}" + "/" + id_kabupaten;
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
                    if (response.code == 200) {
                        let options = '<option value="">Pilih Kecamatan</option>';

                        $.each(response.data, function(i, item) {
                            options +=
                                `<option value="${item.code}">${item.name}</option>`;
                        });

                        $('#kecamatan_domisili')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');

                    } else {
                        $('.alert_profile').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            '<i class="fa-solid fa-circle-exclamation"></i>' +
                            '<strong>Error!</strong>&nbsp;' + response.data +
                            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                        ).fadeIn();
                        setTimeout(function() {
                            $('.alert_profile').fadeOut('slow');
                        }, 10000);
                    }
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        $('#kecamatan_domisili').on('change', function() {
            let id_kecamatan = $(this).val();
            $('#desa_domisili')
                .prop('disabled', true)
                .html('<option value="">Loading...</option>')
                .trigger('change');

            if (!id_kecamatan) return;

            let url = "{{ url('/profile/detail_profile/get_desa') }}" + "/" + id_kecamatan;
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
                    if (response.code == 200) {
                        let options = '<option value="">Pilih Desa</option>';

                        $.each(response.data, function(i, item) {
                            options +=
                                `<option value="${item.code}">${item.name}</option>`;
                        });

                        $('#desa_domisili')
                            .html(options)
                            .prop('disabled', false)
                            .trigger('change');

                    } else {
                        $('.alert_profile').stop(true, true).hide().html(
                            '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                            '<i class="fa-solid fa-circle-exclamation"></i>' +
                            '<strong>Error!</strong>&nbsp;' + response.data +
                            '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                        ).fadeIn();
                        setTimeout(function() {
                            $('.alert_profile').fadeOut('slow');
                        }, 10000);
                    }
                },
                error: function(data) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.responseJSON.message,
                    })
                },

            })
        })
        // button save profile
        $('#btn_update_profile').on('click', function() {
            const offcanvasEl = document.getElementById('offcanvas_konfirmasi_simpan');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
            // $('#form_update_profile').submit();
        });
        $('#btn_update_kontak_kantor').on('click', function() {
            const offcanvasEl = document.getElementById('offcanvas_konfirmasi_simpan_kontak_kantor');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
            // $('#form_update_profile').submit();
        });
        $('#btn_simpan').on('click', function() {
            $('#form_update_profile').submit();
        });
        $('#btn_simpan_alamat').on('click', function() {
            $('#form_update_alamat').submit();
        });
        $('#btn_simpan_kontak_kantor').on('click', function() {
            $('#form_update_kontak_kantor').submit();
        });
        // button save Alamat
        $('#btn_update_alamat').on('click', function() {
            const offcanvasEl = document.getElementById('offcanvas_konfirmasi_simpan_alamat');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        });
    })
</script>
{{-- upload foto --}}
<script>
    // upload ktp
    let $imagetKTP = $('#image_crop_ktp');
    let offcanvasKTP = $('#offcanvas_konfirmasi_ktp');
    let offcanvasKTP_up = $('#offcanvas_ktp');
    $(document).on('click', '#btn_gallery', function() {
        $('#ktp').trigger('click');
    })
    $('#ktp').on('change', function(e) {

        const file = e.target.files[0];
        if (!file) return;

        // Validasi tipe file
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Format file tidak sesuai!',
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {

            // Set preview image
            $imagetKTP.attr('src', event.target.result);

            // Tampilkan offcanvas
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvasKTP[0]);
            bsOffcanvas.show();
        };

        reader.readAsDataURL(file);
    });
    $(document).on('click', '#btn_simpan_ktp', function() {
        const file = document.getElementById('ktp').files[0];
        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih gambar terlebih dahulu!',
            });
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('ktp_pic').files = dt.files;
        document.getElementById('ktp').value = '';

        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasKTP[0]);
        const bsOffcanvasUp = bootstrap.Offcanvas.getInstance(offcanvasKTP_up[0]);
        if (bsOffcanvas) bsOffcanvas.hide();
        if (bsOffcanvasUp) bsOffcanvasUp.hide();
    });
    // end upload ktp

    // upload kk
    let $imagetKK = $('#image_crop_kk');
    let offcanvasKK = $('#offcanvas_konfirmasi_kk');
    let offcanvasKK_up = $('#offcanvas_kk');
    $(document).on('click', '#btn_gallery_kk', function() {
        $('#kk').trigger('click');
    })
    $('#kk').on('change', function(e) {

        const file = e.target.files[0];
        if (!file) return;

        // Validasi tipe file
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Format file tidak sesuai!',
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {

            // Set preview image
            $imagetKK.attr('src', event.target.result);

            // Tampilkan offcanvas
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvasKK[0]);
            bsOffcanvas.show();
        };

        reader.readAsDataURL(file);
    });
    $(document).on('click', '#btn_simpan_kk', function() {
        const file = document.getElementById('kk').files[0];
        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih gambar terlebih dahulu!',
            });
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('kk_pic').files = dt.files;
        document.getElementById('kk').value = '';

        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasKK[0]);
        const bsOffcanvasUp = bootstrap.Offcanvas.getInstance(offcanvasKK_up[0]);
        if (bsOffcanvas) bsOffcanvas.hide();
        if (bsOffcanvasUp) bsOffcanvasUp.hide();
    });
    // end upload kk
</script>
{{-- end upload foto --}}
@endsection