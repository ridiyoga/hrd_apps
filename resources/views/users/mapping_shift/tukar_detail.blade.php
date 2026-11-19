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
                            </tr>
                            {{-- Data --}}
                            @foreach ($items as $item)
                                <tr style="background-color: #ffffff;">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold" style="font-size: 0.85rem; color: #2d3436;">
                                            {{ $item->nama_karyawan }}</div>
                                        <div style="font-size: 0.7rem; color: #b2bec3;">
                                            {{ $item->Karyawan->KaryawanJabatanOne->Bagian->nama_bagian }}
                                            ({{ $item->Karyawan->KaryawanJabatanOne->Jabatan->nama_jabatan }})
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-light text-dark fw-normal"
                                            style="font-size: 0.65rem;">{{ $item->nama_shift }}</span>
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
                            @if ($tukar_shift->approved_signature != null)
                                <img src="/storage/signatures/{{ $tukar_shift->approved_signature }}"
                                    style="width:100%; height:100px; object-fit:contain;" />
                            @endif
                        </div>
                        <small class="fw-bold text-break" style="font-size: 9px; line-height: 1.1;" id="atasan2">
                            {{ $tukar_shift->approved_by_name }}
                        </small>
                        <small style="font-size: 8px; line-height: 1.1;">Tgl:
                            {{ $tukar_shift->approved_at ?? '-' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorie End -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript"></script>
@endsection
