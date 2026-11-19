@extends('admin.layouts.dashboard')

@section('isi')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Dokumen Legal</h5>
                <a href="{{ route('dokumen_legal.index', $holding->holding_code) }}" class="btn btn-secondary btn-sm">
                    Kembali
                </a>
            </div>

            <div class="card-body">

                {{-- ===================== --}}
                {{-- DETAIL DOKUMEN --}}
                {{-- ===================== --}}
                <table class="table table-bordered align-middle">
                    <tr>
                        <th>Kategori Dokumen</th>
                        <td>{{ $document->kategori->nama_kategori ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Sub Kategori Dokumen</th>
                        <td>{{ $document->subKategori->sub_name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Penerbit</th>
                        <td>{{ $document->penerbit->nama_penerbit ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nomor Dokumen</th>
                        <td>{{ $document->nomer_dukumen }}</td>
                    </tr>

                    <tr>
                        <th>Nama Dokumen</th>
                        <td>{{ $document->nama }}</td>
                    </tr>

                    <tr>
                        <th>Site</th>
                        <td>
                            {{ optional($sites->where('id', $document->site_id)->first())->site_name ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            @php
                                $status = strtolower($document->status);
                                $isOverdueActive =
                                    $status === 'aktif' &&
                                    $document->tanggal_akhir &&
                                    \Carbon\Carbon::parse($document->tanggal_akhir)->lt(\Carbon\Carbon::today());

                                $statusColor = $isOverdueActive ? 'danger' : match ($status) {
                                    'aktif' => 'success',
                                    'digantikan' => 'secondary',
                                    'expired' => 'danger',
                                    'tidak_diperpanjang' => 'dark',
                                    default => 'dark',
                                };

                                $statusLabel = $isOverdueActive ? 'Lewat Tanggal' : match ($status) {
                                    'aktif' => 'Aktif',
                                    'digantikan' => 'Digantikan',
                                    'expired' => 'Expired',
                                    'tidak_diperpanjang' => 'Tidak Diperpanjang',
                                    default => ucwords(str_replace('_', ' ', $status)),
                                };
                            @endphp

                            <span class="badge bg-{{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Awal</th>
                        <td>{{ $document->tanggal_awal?->format('d/m/Y') }}</td>
                    </tr>

                    <tr>
                        <th>Tanggal Akhir</th>
                        <td>{{ $document->tanggal_akhir?->format('d/m/Y') }}</td>
                    </tr>

                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $document->keterangan ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>File Dokumen</th>
                        <td>
                            @if ($document->file)
                                <a href="{{ asset('storage/' . $document->file) }}" target="_blank"
                                    class="btn btn-primary btn-sm">
                                    Lihat / Download File
                                </a>
                            @else
                                <span class="text-muted">Tidak ada file</span>
                            @endif
                        </td>
                    </tr>
                </table>

                {{-- ===================== --}}
                {{-- AKSI DOKUMEN --}}
                {{-- ===================== --}}
                @if ($document->status === 'expired' || ($document->status === 'aktif' && $document->tanggal_akhir && $document->tanggal_akhir->lt(\Carbon\Carbon::today())))
                    <div class="alert alert-danger mt-4">
                        <h6 class="mb-2">Aksi Dokumen Expired</h6>
                        <p class="mb-3">
                            Dokumen ini sudah <strong>melewati tanggal akhir</strong>.
                            Silakan tentukan apakah dokumen ini <strong>tidak diperpanjang</strong>
                            atau akan digantikan dengan dokumen baru.
                        </p>

                        <form
                            action="{{ route('dokumen_legal.tidak_diperpanjang', [$holding->holding_code, $document->id]) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin tandai dokumen ini sebagai TIDAK DIPERPANJANG?')">
                            @csrf
                            @method('PATCH')

                            <button type="submit" class="btn btn-dark">
                                <i class="bx bx-x-circle"></i> Tandai Tidak Diperpanjang
                            </button>
                        </form>
                    </div>
                @endif

                {{-- ===================== --}}
                {{-- RIWAYAT DOKUMEN --}}
                {{-- ===================== --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Riwayat Dokumen</h6>
                    </div>

                    <div class="card-body">

                        {{-- DOKUMEN INI ADALAH PENGGANTI --}}
                        @if ($document->dokumenLama)
                            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Dokumen Pengganti</strong><br>
                                    Dokumen ini menggantikan dokumen sebelumnya
                                </div>
                                <a href="{{ route('dokumen_legal.show', [$holding->holding_code, $document->dokumenLama->id]) }}"
                                    class="btn btn-sm btn-outline-warning">
                                    Lihat Dokumen Lama
                                </a>
                            </div>
                        @endif

                        {{-- DOKUMEN INI SUDAH DIGANTIKAN --}}
                        @if ($document->dokumenPengganti)
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Dokumen Digantikan</strong><br>
                                    Dokumen ini sudah digantikan dokumen baru
                                </div>
                                <a href="{{ route('dokumen_legal.show', [$holding->holding_code, $document->dokumenPengganti->id]) }}"
                                    class="btn btn-sm btn-outline-info">
                                    Lihat Dokumen Pengganti
                                </a>
                            </div>
                        @endif

                        {{-- TIDAK ADA RIWAYAT --}}
                        @if (!$document->dokumenLama && !$document->dokumenPengganti)
                            <div class="text-muted fst-italic">
                                Dokumen ini belum memiliki riwayat penggantian
                            </div>
                        @endif

                    </div>
                </div>


            </div>
        </div>

    </div>
@endsection
