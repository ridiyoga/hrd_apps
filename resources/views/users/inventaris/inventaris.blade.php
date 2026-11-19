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

        .coworker-card {
            background: linear-gradient(135deg, #f5edff, #ffffff);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(111, 66, 193, 0.12);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .coworker-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(111, 66, 193, 0.18);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(5px);
        }
    </style>
@endsection
@section('content_top')
    <div class="card">
        <div class="futuristic-header text-center">
            <div class="futuristic-icon">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <h5 class="mt-3 mb-1"> Inventaris</h5>
            <small class="text-muted">Informasi barang milik perusahaan yang dibawa karyawan</small>
        </div>
        <div class="alert alert-info d-flex align-items-center gap-2 rounded-3">
            <i class="fa-solid fa-info-circle fs-5"></i>
            <div class="alert-content">
                <strong>Informasi</strong><br>
                Pastikan data inventaris anda benar dan tidak tertukar dengan pegawai lain!
            </div>
        </div>
        @foreach ($inventaris as $inv)
            <a class="btn_klik" href="{{ url('inventaris/inventaris_detail/' . $menu_id . '/' . $inv->id) }}">
                <div class="coworker-card d-flex align-items-center my-3 p-2-5" style="padding: 12px 15px;">

                    <div class="avatar-wrapper">
                        <i class="fa-solid fa-circle-dot" style="font-size: 11pt;"></i>
                    </div>

                    <div class="flex-grow-1 ms-3 text-start">
                        <h6 class="mb-2 fw-semibold" style="font-size: 10.5pt; line-height: 1.2;">
                            {{ $inv->nama_inventaris }}
                        </h6>

                        <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">
                            <i class="fa-solid fa-window-restore me-2" style="width: 14px; text-align: center;"></i>
                            <span class="price-item">ID Asset : {{ $inv->id_aset }}</span>
                        </div>


                        <div class="text-muted small d-flex align-items-center mb-1" style="font-size: 8.5pt;">

                            <i class="fa-solid fa-sitemap me-2" style="width: 14px; text-align: center;"></i>

                            {{ $inv->nama_kategori }}
                        </div>
                    </div>

                    <div class="icon-box" style="font-size: 12pt;">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>
                </div>
            </a>
        @endforeach


    </div>
@endsection
@section('content')
    @include('users.logout_confirm')
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // Reinitialize JS
                Swal.close();
            }
        });
    </script>
@endsection
