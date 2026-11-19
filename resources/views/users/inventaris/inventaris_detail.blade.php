@extends('users.layouts.main')
@section('title')
    APPS | KARYAWAN - SP
@endsection
@section('css')
    <style>
        .inventaris-detail-page {
            max-width: 760px;
            margin: 0 auto;
        }

        .inventaris-detail-photo {
            overflow: hidden;
            display: flex;
            min-height: 240px;
            align-items: center;
            justify-content: center;
            border-radius: 22px;
            background: linear-gradient(135deg, #f3efff, #f8f7fb);
            box-shadow: 0 14px 30px rgba(35, 25, 70, .08);
        }

        .inventaris-detail-photo img {
            display: block;
            width: 100%;
            max-height: 420px;
            object-fit: contain;
        }

        .inventaris-detail-photo-placeholder {
            padding: 40px 20px;
            color: #7862b5;
            text-align: center;
        }

        .inventaris-detail-photo-placeholder i {
            display: block;
            margin-bottom: 12px;
            font-size: 48px;
            opacity: .7;
        }

        .inventaris-detail-title {
            margin: 18px 0 0;
            color: #27213b;
            font-size: clamp(1.25rem, 5vw, 1.75rem);
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .inventaris-detail-card {
            margin-top: 18px;
            padding: 22px;
            border: 1px solid #eeeaf8;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(35, 25, 70, .06);
        }

        .inventaris-detail-card h2 {
            margin: 0 0 16px;
            color: #27213b;
            font-size: 1rem;
            font-weight: 700;
        }

        .inventaris-detail-row {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f0eef5;
        }

        .inventaris-detail-row:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .inventaris-detail-row:first-of-type {
            padding-top: 0;
        }

        .inventaris-detail-row-icon {
            display: inline-flex;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f3efff;
            color: #6f42c1;
        }

        .inventaris-detail-label {
            display: block;
            margin-bottom: 4px;
            color: #827d91;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .inventaris-detail-value {
            color: #302b3d;
            font-size: .95rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .inventaris-detail-description {
            font-weight: 400;
            line-height: 1.6;
            white-space: pre-line;
        }

        @media (max-width: 576px) {

            .inventaris-detail-photo,
            .inventaris-detail-card {
                border-radius: 17px;
            }

            .inventaris-detail-photo {
                min-height: 200px;
            }

            .inventaris-detail-card {
                padding: 19px;
            }
        }
    </style>
@endsection
@section('content_top')
    <div class="inventaris-detail-page">
        <div class="inventaris-detail-photo">
            @if ($inventaris->foto)
                <img src="{{ asset('storage/public/foto_inventaris/' . $inventaris->foto) }}" alt="Foto">
            @else
                <div class="inventaris-detail-photo-placeholder">
                    <i class="fa-regular fa-image" aria-hidden="true"></i>
                    <span>Foto inventaris belum tersedia</span>
                </div>
            @endif
        </div>
        <h1 class="inventaris-detail-title">{{ $inventaris->nama_inventaris }}</h1>

        <section class="inventaris-detail-card" aria-labelledby="inventaris-info-title">
            <h2 id="inventaris-info-title">Informasi barang</h2>
            <div class="inventaris-detail-row">
                <span class="inventaris-detail-row-icon" aria-hidden="true">
                    <i class="fa-solid fa-box-open"></i>
                </span>
                <div>
                    <span class="inventaris-detail-label">Nama Barang</span>
                    <div class="inventaris-detail-value">{{ $inventaris->nama_inventaris ?? '-' }}</div>
                </div>
            </div>
            <div class="inventaris-detail-row">
                <span class="inventaris-detail-row-icon" aria-hidden="true">
                    <i class="fa-solid fa-barcode"></i>
                </span>
                <div>
                    <span class="inventaris-detail-label">ID Aset</span>
                    <div>{{ $inventaris->id_aset ?? '-' }}</div>
                </div>
            </div>
            <div class="inventaris-detail-row">
                <span class="inventaris-detail-row-icon" aria-hidden="true">
                    <i class="fa-solid fa-hashtag"></i>
                </span>
                <div>
                    <span class="inventaris-detail-label">Nomor seri</span>
                    <div>{{ $inventaris->serial_number ?? '-' }}</div>
                </div>
            </div>
            <div class="inventaris-detail-row">
                <span class="inventaris-detail-row-icon" aria-hidden="true">
                    <i class="fa-solid fa-box-open"></i>
                </span>
                <div>
                    <span class="inventaris-detail-label">Kondisi</span>
                    <div class="inventaris-detail-value">{{ $inventaris->nama_kondisi ?? '-' }}</div>
                </div>
            </div>
            <div class="inventaris-detail-row">
                <span class="inventaris-detail-row-icon" aria-hidden="true">
                    <i class="fa-regular fa-note-sticky"></i>
                </span>
                <div>
                    <span class="inventaris-detail-label">Keterangan</span>
                    <div class="inventaris-detail-value inventaris-detail-description">
                        {{ $inventaris->keterangan ?: 'Tidak ada keterangan.' }}
                    </div>
                </div>
            </div>
        </section>
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
