@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
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

        /* Style Signature Pad */
        .kbw-signature {
            width: 100%;
            height: 160px;
            border: 1px solid #f3e8ff !important;
            background: #ffffff;
        }

        #sig canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .border-dashed {
            border: 2px dashed #d8b4fe !important;
        }

        .opacity-75 {
            opacity: 0.75;
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

        .table-primary-custom {
            background: linear-gradient(135deg, #f5edff, #fcfafd);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .skeleton {
            display: inline-block;
            background: linear-gradient(90deg,
                    #ede9fe 25%,
                    #f5f3ff 37%,
                    #ede9fe 63%);
            background-size: 400% 100%;
            animation: pulse 1.4s ease-in-out infinite;
            border-radius: 6px;
        }

        .skeleton-number {
            display: inline-block;
            /* ⬅️ WAJIB */
            width: 2ch;
            height: 50px;
            /* ⬅️ samakan dengan h3 */
            vertical-align: middle;
            /* ⬅️ penting */
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: .5;
            }

            50% {
                opacity: 1;
            }
        }
    </style>
@endsection
@section('content_top')
    <!-- Categorie -->
    <div class="card">
        {{-- Header --}}
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <h5 class="mt-3 mb-1">Detail Edit Shift</h5>
            <small class="text-muted">Detail Pengajuan Edit Mapping Shift</small>
        </div>
        {{-- Table Section --}}

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        @foreach ($detail_mapping as $tanggal => $items)
                            {{-- GRUP TANGGAL 1 --}}
                            <tr style="background-color: #f1f4f9;">
                                <td colspan="3" class="ps-4 py-2">
                                    <span class="fw-bold"
                                        style="font-size: 0.8rem; color: #495057;">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span>
                                </td>
                            </tr>
                            {{-- Sub-Header Kolom --}}
                            <tr style="background-color: #ffffff;">
                                <td class="ps-4 py-2"><small class="text-muted fw-bold"
                                        style="font-size: 0.6rem; text-transform: uppercase;">Nama</small></td>
                                <td class="text-center py-2"><small class="text-muted fw-bold"
                                        style="font-size: 0.6rem; text-transform: uppercase;">Shift</small></td>
                                <td class="text-center py-2"><small class="text-muted fw-bold"
                                        style="font-size: 0.6rem; text-transform: uppercase;">Original</small></td>
                            </tr>
                            {{-- Data --}}
                            @foreach ($items as $item)
                                <tr style="background-color: #ffffff;">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold" style="font-size: 0.85rem; color: #2d3436;">
                                            {{ $item->nama_karyawan }}</div>
                                        <div style="font-size: 0.7rem; color: #b2bec3;">
                                            {{ $item->Karyawan->KaryawanJabatanOne->Bagian->nama_bagian ?? '-' }}
                                            ({{ $item->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-' }})
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-light text-dark fw-normal"
                                            style="font-size: 0.65rem;">{{ $item->nama_shift }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-light text-dark fw-normal"
                                            style="font-size: 0.65rem;">
                                            @if ($item->is_active == 3)
                                                {{ $item->Original->nama_shift ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="text-center mb-3">
                <span class="mb-0 fw-bold">Otorisasi & Pengesahan</span>
            </div>
            @if ($tukar_shift->approved != null)
                <div class="row g-2 d-flex align-items-stretch">
                    <div class="col-6">
                        <div
                            class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                            <small class="d-block" style="font-size: 8px;">DIAJUKAN OLEH:</small>
                            <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                <img src="/storage/signatures/{{ $tukar_shift->input_by_signature }}"
                                    style="width:100%; height:100px; object-fit:contain;" />
                            </div>
                            <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;"
                                id="atasan1">{{ $tukar_shift->input_by_name }}</small>
                            <small style="font-size: 8px; line-height: 1.1;">Tgl: {{ $tukar_shift->created_at }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div
                            class="p-2 border rounded-3 text-center opacity-75 h-100 d-flex flex-column justify-content-between">
                            <small class="d-block" style="font-size: 8px;">DISAHKAN OLEH:</small>
                            <div class="py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                                <img src="/storage/signatures/{{ $tukar_shift->approved_signature }}"
                                    style="width:100%; height:100px; object-fit:contain;" />
                            </div>
                            <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan2">
                                {{ $tukar_shift->approved_by_name }}
                            </small>
                            <small style="font-size: 8px; line-height: 1.1;">Tgl:
                                {{ $tukar_shift->approved_at ?? '-' }}</small>
                        </div>
                    </div>
                </div>
            @else
                {{-- <input type="hidden" name="shift" id="shift" value="{{ $id }}"> --}}
                <div class="card border-dashed bg-white rounded-4 overflow-hidden mb-3">
                    <div class="card-header bg-light border-0 py-2 text-center">
                        <input type="text" name="name" id="id" value="{{ $tukar_shift->id }}" hidden>
                        <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                            id="user_pengajuan">Disetujui oleh: {{ $user_karyawan->name }}</small>
                        <small class="fw-bold text-uppercase" style="font-size: 10px; color: #6b21a8;"
                            id="user_name"></small>
                    </div>
                    <div class="card-body p-2">
                        <div class="alert_signature"></div>
                        <div id="sig" class="kbw-signature rounded-3" style="width: 100%; height: 200px;">
                            <canvas id="signature-pad"
                                style="width:100%; touch-action: none !important; height:200px; border:1px solid #ccc;"
                                hidden></canvas>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                                id="clear">
                                <i class="fa-solid fa-eraser me-1"></i> Hapus
                            </button>
                            <small class="text-muted italic" style="font-size: 10px;">Tanda tangan di atas</small>
                        </div>
                        <textarea id="signature" name="signature" style="display: none"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-center">

                    <div id="div_btn_approval">
                        <button id="btn_update_shift" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-circle-check"></i>&nbsp;Approve
                        </button>
                        <button id="btn_tolak_update_shift" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-circle-xmark"></i>&nbsp;Tolak
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Categorie End -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            var canvas = document.getElementById('signature-pad');
            // console.log(canvas);
            var signaturePad = new SignaturePad(canvas);
            $('#signature-pad').removeAttr('hidden');
            $(document).on('click', '#clear', function() {
                signaturePad.clear();
            })

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);

                // ambil ukuran CSS asli
                const width = canvas.offsetWidth;
                const height = canvas.offsetHeight;

                // set ukuran canvas sebenarnya
                canvas.width = width * ratio;
                canvas.height = height * ratio;

                const ctx = canvas.getContext('2d');
                ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform
                ctx.scale(ratio, ratio);

                signaturePad.clear();
            }
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();
            $('#tolak_').on('click', function() {
                // let formData = new FormData($('#form_add_karyawan')[0]); // ✅ FIX
                var formData = new FormData();

                const canvas = document.getElementById('signature-pad');
                const signature = canvas.toDataURL('image/png');

                function isCanvasEmpty(canvas) {
                    const blank = document.createElement('canvas');
                    blank.width = canvas.width;
                    blank.height = canvas.height;
                    return canvas.toDataURL() === blank.toDataURL();
                }

                if (isCanvasEmpty(canvas)) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Tanda tangan wajib diisi!'
                    });
                    return;
                }
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('id_mapping_tukar', $('#id_mapping_tukar').val());
                formData.append('menu_id', $('#menu_id').val());
                formData.append('signature', signature);
                formData.append('start', globalStart);
                formData.append('end', globalEnd);
                // formData.append('tukars', globalTukars);
                // console.log(formData);
                $.ajax({
                    url: "{{ url('tukar_shift/ubah_shift_hrd') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib
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
                    success: function(response) {

                        if (response.code == 200) {
                            // console.log('success');


                        } else {

                            let errorMsg = '';

                            // ambil error alasan (array)
                            if (response.errors && response.errors.alasan) {
                                errorMsg = response.errors.alasan[0];
                            } else {
                                errorMsg = response.message;
                            }
                            $('.alert_profile').empty();
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Gagal!</strong>&nbsp;' + errorMsg +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 60000);


                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();

                        let message = 'Terjadi kesalahan sistem';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        } else if (error) {
                            message = error;
                        }

                        Swal.fire({
                            title: 'Oops...',
                            text: message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })

            });
            $('#btn_update_shift').on('click', function() {
                // let formData = new FormData($('#form_add_karyawan')[0]); // ✅ FIX
                let menu_active = '{{ $menu_active }}'
                let legal_number_mapping = '{{ $legal_number_mapping->legal_number_mapping }}';
                var formData = new FormData();

                const canvas = document.getElementById('signature-pad');
                const signature = canvas.toDataURL('image/png');

                function isCanvasEmpty(canvas) {
                    const blank = document.createElement('canvas');
                    blank.width = canvas.width;
                    blank.height = canvas.height;
                    return canvas.toDataURL() === blank.toDataURL();
                }

                if (isCanvasEmpty(canvas)) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Tanda tangan wajib diisi!'
                    });
                    return;
                }
                formData.append('_token', '{{ csrf_token() }}');
                // formData.append('id_mapping_tukar', $('#id_mapping_tukar').val());
                formData.append('id', $('#id').val());
                formData.append('menu_active', menu_active);
                formData.append('legal_number_mapping', legal_number_mapping);
                formData.append('signature', signature);
                // formData.append('start', globalStart);
                // formData.append('end', globalEnd);
                // formData.append('tukars', globalTukars);
                // console.log(formData);
                $.ajax({
                    url: "{{ url('tukar_shift/ubah_shift_atasan_hrd') }}" + '/' +
                        menu_active,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib
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
                    success: function(response) {

                        if (response.code == 200) {
                            // console.log('success');
                            window.location.href = response.redirect;

                        } else {

                            let errorMsg = '';

                            // ambil error alasan (array)
                            if (response.errors && response.errors.alasan) {
                                errorMsg = response.errors.alasan[0];
                            } else {
                                errorMsg = response.message;
                            }
                            $('.alert_profile').empty();
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Gagal!</strong>&nbsp;' + errorMsg +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 60000);


                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();

                        let message = 'Terjadi kesalahan sistem';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        } else if (error) {
                            message = error;
                        }

                        Swal.fire({
                            title: 'Oops...',
                            text: message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })

            });
            $('#btn_tolak_update_shift').on('click', function() {
                // let formData = new FormData($('#form_add_karyawan')[0]); // ✅ FIX
                let menu_active = '{{ $menu_active }}'
                var formData = new FormData();

                const canvas = document.getElementById('signature-pad');
                const signature = canvas.toDataURL('image/png');

                function isCanvasEmpty(canvas) {
                    const blank = document.createElement('canvas');
                    blank.width = canvas.width;
                    blank.height = canvas.height;
                    return canvas.toDataURL() === blank.toDataURL();
                }

                if (isCanvasEmpty(canvas)) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Tanda tangan wajib diisi!'
                    });
                    return;
                }
                formData.append('_token', '{{ csrf_token() }}');
                // formData.append('id_mapping_tukar', $('#id_mapping_tukar').val());
                formData.append('id', $('#id').val());
                formData.append('menu_active', menu_active);
                formData.append('signature', signature);
                // formData.append('start', globalStart);
                // formData.append('end', globalEnd);
                // formData.append('tukars', globalTukars);
                // console.log(formData);
                $.ajax({
                    url: "{{ url('tukar_shift/tolak_ubah_shift_atasan_hrd') }}" + '/' +
                        menu_active,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false, // 🔥 wajib
                    contentType: false, // 🔥 wajib
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
                    success: function(response) {

                        if (response.code == 200) {
                            // console.log('success');
                            window.location.href = response.redirect;

                        } else {

                            let errorMsg = '';

                            // ambil error alasan (array)
                            if (response.errors && response.errors.alasan) {
                                errorMsg = response.errors.alasan[0];
                            } else {
                                errorMsg = response.message;
                            }
                            $('.alert_profile').empty();
                            $('.alert_profile').stop(true, true).hide().html(
                                '<div class="alert alert-danger light alert-lg alert-dismissible fade show">' +
                                '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' +
                                '<strong>Gagal!</strong>&nbsp;' + errorMsg +
                                '<button class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>'
                            ).fadeIn();
                            setTimeout(function() {
                                $('.alert_profile').fadeOut('slow');
                            }, 60000);


                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr);
                        Swal.close();

                        let message = 'Terjadi kesalahan sistem';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            message = xhr.responseText;
                        } else if (error) {
                            message = error;
                        }

                        Swal.fire({
                            title: 'Oops...',
                            text: message,
                            icon: 'error',
                            iconColor: '#b54a4a',
                            showConfirmButton: false,
                            confirmButtonText: 'Oke, Mengerti',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'premium-swal',
                                title: 'premium-title',
                                htmlContainer: 'premium-text',
                                confirmButton: 'premium-confirm'
                            },
                            timer: 6000,
                            timerProgressBar: true
                        });
                    }
                })

            });
        });
    </script>
@endsection
