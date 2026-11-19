<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KaryawanApiController extends Controller
{
    public function karyawan_datatable($holding)
    {
        $getHolding = Holding::where('holding_code', $holding)->first();
        $table = Karyawan::with('Desa')
            ->with('Kecamatan')
            ->with('Kabupaten')
            ->with('Provinsi')
            ->with('Sertifikat')
            ->with('KaryawanJabatanOne')
            ->with('KaryawanKontrakOne')
            ->with('KaryawanPendidikanOne')
            ->with('KaryawanKontrak')
            ->with('PenempatanKerja')
            ->where('kontrak_kerja', $getHolding->id)
            ->where('status_aktif', 'AKTIF')
            // ->limit(5)
            ->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('holding', function ($row) {
                    return $row->KontrakKerja->holding_category ?? '-';
                })
                ->addColumn('sertifikat', function ($row) {

                    if ($row->Sertifikat->isEmpty()) {
                        return '-';
                    }

                    return $row->Sertifikat
                        ->pluck('keahlian')
                        ->implode(', ');
                })
                ->addColumn('status_nikah', function ($row) {
                    if ($row->status_nikah == 1) {
                        $nikah = 'LAJANG';
                    } elseif ($row->status_nikah == 2) {
                        $nikah = 'MENIKAH';
                    } elseif ($row->status_nikah == 3) {
                        $nikah = 'CERAI HIDUP';
                    } elseif ($row->status_nikah == 4) {
                        $nikah = 'CERAI MATI';
                    } else {
                        $nikah = '-';
                    }
                    return $nikah;
                })
                ->addColumn('no_kk', function ($row) {
                    return $row->no_kk ?? '-';
                })
                ->addColumn('tgl_terbit_kk', function ($row) {
                    return $row->tgl_terbit_kk ? $row->tgl_terbit_kk->format('d-M-Y') : '-';
                })
                ->addColumn('npwp', function ($row) {
                    return $row->npwp ?? '-';
                })
                ->addColumn('ptkp', function ($row) {
                    return $row->ptkp ?? '-';
                })
                ->addColumn('no_bpjs_ketenagakerjaan', function ($row) {
                    return $row->no_bpjs_ketenagakerjaan ?? '-';
                })
                ->addColumn('no_bpjs_kesehatan', function ($row) {
                    return $row->no_bpjs_kesehatan ?? '-';
                })
                ->addColumn('detail_alamat', function ($row) {
                    $detail_alamat = 'RT ' . $row->rt . ', RW ' . $row->rw . ', ' . $row->detail_alamat;
                    return $detail_alamat;
                })
                ->addColumn('desa', function ($row) {
                    return $row->Desa != null ? $row->Desa->name : '-';
                })
                ->addColumn('kecamatan', function ($row) {
                    return $row->Kecamatan != null ? $row->Kecamatan->name : '-';
                })
                ->addColumn('kabupaten', function ($row) {
                    return $row->Kabupaten ? $row->Kabupaten->name : '-';
                })
                ->addColumn('provinsi', function ($row) {
                    return $row->Provinsi != null ? $row->Provinsi->name : '-';
                })
                ->addColumn('kode_pos', function ($row) {
                    return $row->kode_pos ?? '-';
                })
                ->addColumn('agama', function ($row) {
                    if ($row->agama != null) {
                        if ($row->agama == 1) {
                            $agama = 'ISLAM';
                        } elseif ($row->agama == 2) {
                            $agama = 'KRISTEN';
                        } elseif ($row->agama == 3) {
                            $agama = 'KRISTEN';
                        } elseif ($row->agama == 4) {
                            $agama = 'HINDU';
                        } elseif ($row->agama == 5) {
                            $agama = 'BUDHA';
                        } elseif ($row->agama == 6) {
                            $agama = 'KONGHUCU';
                        }
                    } else {
                        $agama = '-';
                    }
                    return $agama;
                })
                ->addColumn('nama_jabatan', function ($row) {
                    return $row->KaryawanJabatanOne->Jabatan->nama_jabatan ?? '-';
                })
                ->addColumn('nama_divisi', function ($row) {
                    return $row->KaryawanJabatanOne->Divisi->nama_divisi ?? '-';
                })
                ->addColumn('nama_departemen', function ($row) {
                    return $row->KaryawanJabatanOne->Departemen->nama_departemen ?? '-';
                })
                ->addColumn('nama_bagian', function ($row) {
                    return $row->KaryawanJabatanOne->Bagian->nama_bagian ?? '-';
                })
                ->addColumn('penempatan_kerja', function ($row) {
                    return $row->PenempatanKerja->site_name ?? '-';
                })
                ->addColumn('tgl_join', function ($row) {
                    return $row->tgl_join ? $row->tgl_join->format('d-M-Y') : '-';
                })
                ->addColumn('lama_kerja', function ($row) {
                    if (!$row->tgl_join) {
                        return '-';
                    }
                    // return round(Carbon::parse($row->tgl_join)->diffInYears(now())) . ' TAHUN';
                    $diff = Carbon::parse($row->tgl_join)->diff(now());
                    return $diff->y . ' TAHUN ' . $diff->m . ' BULAN';
                })
                ->addColumn('gender', function ($row) {
                    if ($row->gender == 1) {
                        return 'LAKI-LAKI';
                    } elseif ($row->gender == 2) {
                        return 'PEREMPUAN';
                    }
                })
                ->addColumn('nama_bank', function ($row) {
                    return substr($row->nama_bank, 1);
                })
                ->addColumn('riwayat_kontrak', function ($row) {
                    if ($row->KaryawanKontrak->isEmpty()) {
                        return '-';
                    }

                    // return $row->KaryawanKontrak;
                    return $row->KaryawanKontrak
                        ->map(function ($item) {
                            $mulai = $item->tanggal_mulai
                                ? Carbon::parse($item->tanggal_mulai)->format('d-m-Y')
                                : '-';

                            $selesai = $item->tanggal_selesai
                                ? Carbon::parse($item->tanggal_selesai)->format('d-m-Y')
                                : '-';
                            if ($item->lama_kontrak == '1bln') {
                                $lama = '(1 Bulan)';
                            } elseif ($item->lama_kontrak == '3bln') {
                                $lama = '(3 Bulan)';
                            } elseif ($item->lama_kontrak == '6bln') {
                                $lama = '(6 Bulan)';
                            } elseif ($item->lama_kontrak == '1thn') {
                                $lama = '(1 Tahun)';
                            } elseif ($item->lama_kontrak == '2thn') {
                                $lama = '(2 Tahun)';
                            } elseif ($item->lama_kontrak == 'tetap') {
                                $lama = 'Tetap';
                            } elseif ($item->lama_kontrak == null) {
                                $lama = '-';
                            } else {
                                $lama = '-';
                            }
                            return $mulai . ' - ' . $selesai . ' ' . ($lama);
                        })
                        ->implode(', ');
                })
                ->addColumn('tanggal_berakhir', function ($row) {
                    return Carbon::parse($row->KaryawanKontrakOne?->tanggal_selesai)->format('d-m-Y') ?? '-';
                })
                ->addColumn('tgl_lahir', function ($row) {
                    return Carbon::parse($row->tgl_lahir)->format('d-m-Y') ?? '-';
                })
                ->addColumn('hari_expired', function ($row) {
                    $kontrak = $row->KaryawanKontrakOne;
                    if (!$kontrak || !$kontrak->tanggal_selesai) {
                        return '-';
                    }

                    if ($row->KaryawanKontrakOne->tanggal_selesai < today()) {
                        $hari_lebih = Carbon::parse($row->KaryawanKontrakOne->tanggal_selesai)
                            ->diffInDays(today());

                        return '+' . $hari_lebih;
                    } elseif ($row->KaryawanKontrakOne->tanggal_selesai > today()) {
                        $hari_kurang = Carbon::parse($row->KaryawanKontrakOne->tanggal_selesai)
                            ->diffInDays(today());

                        return $hari_kurang;
                    }
                })
                ->addColumn('jenjang', function ($row) {
                    return $row->KaryawanPendidikanOne?->jenjang ?? '-';
                })
                ->addColumn('jurusan', function ($row) {
                    return $row->KaryawanPendidikanOne?->jurusan ?? '-';
                })
                ->addColumn('institusi', function ($row) {
                    return $row->KaryawanPendidikanOne?->institusi ?? '-';
                })
                ->addColumn('saldo_cuti', function ($row) {
                    return '-';
                })
                ->rawColumns([
                    'status_nikah',
                    'holding',
                    'sertifikat',
                    'no_kk',
                    'tgl_terbit_kk',
                    'npwp',
                    'ptkp',
                    'no_bpjs_ketenagakerjaan',
                    'no_bpjs_kesehatan',
                    'detail_alamat',
                    'desa',
                    'kecamatan',
                    'kabupaten',
                    'provinsi',
                    'kode_pos',
                    'agama',
                    'nama_jabatan',
                    'nama_divisi',
                    'nama_departemen',
                    'nama_bagian',
                    'penempatan_kerja',
                    'tgl_join',
                    'tgl_lahir',
                    'lama_kerja',
                    'gender',
                    'nama_bank',
                    'riwayat_kontrak',
                    'tanggal_berakhir',
                    'hari_expired',
                    'jenjang',
                    'jurusan',
                    'institusi',
                    'saldo_cuti',
                ])
                ->make(true);
        }
    }
}
