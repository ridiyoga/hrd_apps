@extends('admin.layouts.dashboard')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<style>
    .form-floating-outline .form-control {
        border-radius: 10px;
    }

    .card {
        border-radius: 14px;
    }

    .card-header {
        border-bottom: 1px solid #f0f0f0;
    }

    .btn-primary {
        border-radius: 10px;
    }

    .profile-photo-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .profile-photo-btn i {
        font-size: 18px;
    }
</style>
@endsection
@section('isi')
@include('sweetalert::alert')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">PROFILE</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row mt-3">
                    <!-- FOTO PROFILE -->
                    <div class="col-4 border-end">
                        <div class="text-center p-3">
                            <div class="position-relative d-inline-block">
                                <img src="{{ Auth::user()->photo ? asset('storage/profile/'.Auth::user()->photo) : asset('admin/assets/img/avatars/1.png') }}"
                                    class="rounded-circle shadow"
                                    width="130" height="130"
                                    style="object-fit: cover">

                                <label for="photo"
                                    class="btn btn-primary btn-icon btn-sm profile-photo-btn position-absolute bottom-0 end-0 shadow">
                                    <i class="mdi mdi-camera"></i>
                                </label>
                            </div>

                            <h6 class="mt-3 mb-0">{{ Auth::user()->username }}</h6>
                        </div>
                    </div>
                    <div class="col-8">
                        <form id="formProfile" enctype="multipart/form-data">
                            <input type="file" name="photo" id="photo" class="d-none">
                            <input type="hidden" name="change_password" id="change_password">
                            <!-- FORM PROFILE -->
                            <!-- USERNAME -->
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="text"
                                    class="form-control"
                                    name="username"
                                    value="{{ Auth::user()->username }}">
                                <label>Username</label>
                                <div class="invalid-feedback"></div>
                            </div>

                            <hr class="my-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="changePassword">
                                <label class="form-check-label" for="changePassword">
                                    Ubah Password
                                </label>
                            </div>

                            <div id="passwordWrapper" class="d-none">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="password" class="form-control" name="password">
                                    <label>Password Baru</label>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="password" class="form-control" name="password_confirmation">
                                    <label>Konfirmasi Password</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>


                            <button type="button" class="btn_save_user btn btn-primary text-center">
                                <i class="mdi mdi-content-save-outline me-1"></i>
                                Simpan Perubahan
                            </button>
                        </form>
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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        $('#parent_approval_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Parent...",
            allowClear: true
        });

        let holding = "{{ $holding->holding_code }}";
        let holding_id = "{{ $holding->id }}";
        $('#changePassword').on('change', function() {
            $('#change_password').val(this.checked ? 1 : 0);
            if ($(this).is(':checked')) {
                $('#passwordWrapper').slideDown(200).removeClass('d-none');
            } else {
                $('#passwordWrapper').slideUp(200);

                // reset value & error
                $('#passwordWrapper input').val('').removeClass('is-invalid');
                $('#passwordWrapper .invalid-feedback').html('');
            }
        });


        $(document).on('click', '#btn_hak_akses_karyawan', function() {
            // console.log('ok');
            var id = $(this).data('id');
            $('#role_id').val(id);
            $('#table_akses_karyawan').DataTable().clear().destroy();
            var table = $('#table_akses_karyawan').DataTable({
                pageLength: -1,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                "scrollY": true,
                "scrollX": true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('users/hak_akses/karyawan-datatable') }}" + '/' + id + '/' + holding,
                },
                columns: [{
                        data: "id",

                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'nama_karyawan',
                        name: 'nama_karyawan'
                    },
                    {
                        data: 'nama_departemen',
                        name: 'nama_departemen'
                    },
                    {
                        data: 'nama_jabatan',
                        name: 'nama_jabatan'
                    },
                    {
                        data: 'option',
                        name: 'option'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
            $('#modal_hak_akses_karyawan').modal('show');
        });

        $(document).on('click', '.btn_save_user', function() {
            var get_holding = '{{ $holding->holding_code }}';
            var url = "{{url('admin/profile/save_profile')}}/" + get_holding;
            var formData = new FormData();
            var photoFile = $('#photo')[0].files[0];
            var changePassword = $('#changePassword').is(':checked');

            formData.append('_token', "{{ csrf_token() }}");
            formData.append('username', $('#formProfile [name="username"]').val());
            formData.append('change_password', changePassword ? 1 : 0);
            if (photoFile) {
                formData.append('photo', photoFile);
            }
            if (changePassword) {
                formData.append('password', $('#formProfile [name="password"]').val());
                formData.append('password_confirmation', $('#formProfile [name="password_confirmation"]').val());
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Mohon tunggu...',
                        text: 'Sedang memproses data',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    // console.log(data);
                    Swal.close();
                    if (data.code == 200) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 4500
                        })
                        window.location.reload();
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || Object.values(data.errors || {})
                                .reduce(function(messages, errors) {
                                    return messages.concat(errors);
                                }, []).join('\n') || 'Terjadi kesalahan.',
                            icon: 'error',
                            timer: 4500
                        })
                    }
                },
                error: function(data) {
                    Swal.close();
                    var response = data.responseJSON || {};
                    var errors = Object.values(response.errors || {}).reduce(function(messages, fieldErrors) {
                        return messages.concat(fieldErrors);
                    }, []);
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.message || errors.join('\n') || 'Gagal menyimpan profil.',
                        icon: 'error',
                        timer: 4500
                    })
                }
            })
        })


    });
</script>
@endsection