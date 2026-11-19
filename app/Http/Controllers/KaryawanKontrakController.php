<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Cities;
use App\Models\District;
use App\Models\Holding;
use App\Models\Karyawan;
use App\Models\KaryawanJabatan;
use App\Models\KaryawanKontrak;
use App\Models\Menu;
use App\Models\User;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class KaryawanKontrakController extends Controller
{
    public function hr_jabatan_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'penempatan_kerja' => 'required',
                'kontrak_kerja' => 'required',
                'kuota_cuti' => 'required',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        try {
            Karyawan::where('id', $request->id_karyawan)->update([
                'penempatan_kerja' => $request->penempatan_kerja,
                'kontrak_kerja' => $request->kontrak_kerja,
                'kuota_cuti_tahunan' => $request->kuota_cuti,
            ]);
            $get_holding = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $nama_karyawan = Karyawan::where('id', $request->id_karyawan)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_karyawan,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawans',
                'activity' => 'update',
                'description' => 'mengubah info HR ' . $nama_karyawan->name,
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function kontrak_datatable($id, $holding)
    {
        $table = KaryawanKontrak::where('karyawan_id', $id)->orderBy('created_at', 'ASC')->get();
        // dd($table);
        if (request()->ajax()) {
            return DataTables::of($table)
                ->addColumn('file_kontrak', function ($row) {
                    $file = asset('storage/file_kontrak/' . $row->file_kontrak);
                    $file_kontrak = ' <a type="button" href="' . $file . '" target="_blank"
                            class="btn btn-sm btn-primary waves-effect waves-light"><i class="mdi mdi-eye"></i>
                            &nbsp;Lihat
                        </a>';
                    // $file_kontrak =  "<embed type='application/pdf' src='$file' width='600' height='400'>";
                    if ($row->file_kontrak != NULL) {
                        return $file_kontrak;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('file_kontrak_download', function ($row) use ($holding) {
                    $url = url('/karyawan/kontrak_download/' . $row->id_kontrak . '/' . $holding);
                    $file_kontrak = ' <a href="' . $url . '"
                                    type="button" class="btn btn-sm btn-info" target="_blank">
                                    <i class="tf-icons mdi mdi-download me-1"></i>
                                    Download PDF
                                </a>';
                    // $file_kontrak =  "<embed type='application/pdf' src='$file' width='600' height='400'>";
                    return $file_kontrak;
                })
                ->addColumn('tanggal_mulai', function ($row) {
                    return $row->tanggal_mulai;
                })
                ->addColumn('lama_kontrak', function ($row) {
                    if ($row->lama_kontrak == '1bln') {
                        return '1 Bulan';
                    } elseif ($row->lama_kontrak == '3bln') {
                        return '3 Bulan';
                    } elseif ($row->lama_kontrak == '6bln') {
                        return '6 Bulan';
                    } elseif ($row->lama_kontrak == '1thn') {
                        return '1 Tahun';
                    } elseif ($row->lama_kontrak == '2thn') {
                        return '2 Tahun';
                    } elseif ($row->lama_kontrak == 'tetap') {
                        return 'Karyawan Tetap';
                    }
                })
                ->addColumn('tanggal_selesai', function ($row) {
                    return $row->tanggal_selesai;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                ->addColumn('option', function ($row) {
                    $btn =
                        '
                        <button type="button" id="btn_edit_kontrak"
                            data-id_kontrak="' . $row->id_kontrak . '"
                            data-file_kontrak="' . $row->file_kontrak . '"
                            data-tanggal_mulai="' . $row->tanggal_mulai . '"
                            data-lama_kontrak="' . $row->lama_kontrak . '"
                            class="btn btn-sm btn-primary">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" id="btn_delete_kontrak"
                            data-id_kontrak="' . $row->id_kontrak . '"
                            data-file_kontrak="' . $row->file_kontrak . '"
                            class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete"></i>
                        </button>
                        ';
                    return $btn;
                })
                ->addColumn('is_active', function ($row) {
                    if ($row->is_active == '1') {
                        return '<span class="badge bg-label-success">Aktif</span>';
                    } else {
                        return '<span class="badge bg-label-info">Non Aktif</span>';
                    }
                })
                ->rawColumns(['file_kontrak', 'file_kontrak_download', 'option'])
                ->make(true);
        }
    }
    private function terbilang($angka)
    {
        $angka = abs((int) $angka);
        $bil = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

        if ($angka < 12) return $bil[$angka];
        if ($angka < 20) return $this->terbilang($angka - 10) . ' belas';
        if ($angka < 100) return $this->terbilang(intval($angka / 10)) . ' puluh ' . $this->terbilang($angka % 10);
        if ($angka < 200) return 'seratus ' . $this->terbilang($angka - 100);
        if ($angka < 1000) return $this->terbilang(intval($angka / 100)) . ' ratus ' . $this->terbilang($angka % 100);
        if ($angka < 2000) return 'seribu ' . $this->terbilang($angka - 1000);
        if ($angka < 1000000) return $this->terbilang(intval($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000);

        return '';
    }
    public function kontrak_download($id, $holding)
    {
        // dd($id);
        $get_holding = Holding::where('id', $holding)->first();
        $get_kontrak = KaryawanKontrak::where('id_kontrak', $id)
            ->with([
                'Karyawan' => function ($query) {
                    $query->with([
                        'provinsiKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kabupatenKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kecamatanKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'desaKTP' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'provinsiNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kabupatenNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'kecamatanNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ])->with([
                        'desaNOW' => function ($query) {
                            $query->orderBy('id', 'ASC');
                        }
                    ]);
                }
            ])
            ->first();
        // dd($get_kontrak);
        $direktur = KaryawanJabatan::where('holding_id', $get_holding->id)->where('is_active', 1)->with([
            'Jabatan' => function ($query) {
                $query->with([
                    'LevelJabatan' => function ($query) {
                        $query;
                    }
                ]);
            }
        ])->with([
            'Karyawan' => function ($query) {
                $query;
            }
        ])->whereHas('Jabatan.LevelJabatan', function ($query) {
            $query->where('level_jabatan', '-1');
        })
            // ->where('main', '1')
            ->first();
        // dd($direktur);
        $jabatan = KaryawanJabatan::where('karyawan_id', $get_kontrak->Karyawan->id)->where('main', 1)->where('is_active', 1)->with([
            'Bagian' => function ($query) {
                $query->with([
                    'Divisi' => function ($query) {
                        $query->with([
                            'Departemen' => function ($query) {
                                $query->orderBy('nama_departemen', 'ASC');
                            }
                        ]);
                    }
                ]);
            }
        ])
            ->with('Jabatan.LevelJabatan')
            ->first();
        $hari_alf = Carbon::parse($get_kontrak->tanggal_mulai)->translatedFormat('l');
        $tanggal_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_mulai)->day);
        $bulan_alf = Carbon::parse($get_kontrak->tanggal_mulai)->translatedFormat('F');
        $tahun_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_mulai)->year);
        $date_mulai = Carbon::parse($get_kontrak->tanggal_mulai)->format('d-m-Y');
        $tanggal_mulai_kontrak = Carbon::parse($get_kontrak->tanggal_mulai)->format('d');
        $tanggal_mulai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_mulai)->format('d'));

        $tanggal_selesai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_selesai)->day);
        $bulan_selesai_alf = Carbon::parse($get_kontrak->tanggal_selesai)->translatedFormat('F');
        $tahun_selesai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_selesai)->year);
        $date_selesai_selesai = Carbon::parse($get_kontrak->tanggal_selesai)->format('d-m-Y');
        $tanggal_selesai_kontrak = Carbon::parse($get_kontrak->tanggal_selesai)->format('d');
        $tanggal_selesai_alf = $this->terbilang(Carbon::parse($get_kontrak->tanggal_selesai)->format('d'));

        $pdf = Pdf::setOption([
            'isRemoteEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ])->loadView('admin.karyawan.kontrak_pdf', [
            'tittle' => 'Data Recruitment',
        ], compact(
            'get_holding',
            'jabatan',
            'get_kontrak',
            'hari_alf',
            'tanggal_alf',
            'bulan_alf',
            'tahun_alf',
            'date_mulai',
            'tanggal_mulai_kontrak',
            'tanggal_mulai_alf',

            'tanggal_selesai_alf',
            'bulan_selesai_alf',
            'tahun_selesai_alf',
            'tanggal_selesai_kontrak',
            'direktur',
        ));
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // TULIS FOOTER DI SINI
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            450,      // posisi X (kanan)
            820,      // posisi Y (bawah)
            "Halaman {PAGE_NUM} dari {PAGE_COUNT}",
            null,     // font default
            10,       // font size
            [0, 0, 0] // warna hitam
        );

        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas) use ($fontMetrics) {

            if ($pageNumber == $pageCount) return;

            $pageWidth  = $canvas->get_width();
            $pageHeight = $canvas->get_height();

            $cm = 28.35;

            /* =========================
       UKURAN RAMPING
    ========================= */
            $col       = 95;      // lebih kecil
            $headerH   = 16;
            $bodyH     = 38;
            $parafW    = $col * 2;
            $parafH    = $headerH + $bodyH;

            $x = $pageWidth  - (2.5 * $cm) - $parafW;
            $y = $pageHeight - (1   * $cm) - $parafH;

            $font = $fontMetrics->getFont("Times-Roman", "normal");
            $size = 7;

            $center = function ($text, $bx, $by, $bw, $bh) use ($canvas, $fontMetrics, $font, $size) {
                $tw = $fontMetrics->getTextWidth($text, $font, $size);
                $tx = $bx + ($bw - $tw) / 2;
                $ty = $by + ($bh / 2) - ($size / 2);
                $canvas->text($tx, $ty, $text, $font, $size);
            };

            /* HEADER KIRI */
            $canvas->rectangle($x, $y, $col, $headerH, [0, 0, 0], 1);
            $center("Paraf", $x, $y, $col, 8);
            $center("Karyawan", $x, $y + 8, $col, 8);

            /* HEADER KANAN */
            $rx = $x + $col;
            $canvas->rectangle($rx, $y, $col, $headerH, [0, 0, 0], 1);
            $center("Paraf Pihak", $rx, $y, $col, 8);
            $center("Kesatu", $rx, $y + 8, $col, 8);

            /* BODY */
            $canvas->rectangle($x,  $y + $headerH, $col, $bodyH, [0, 0, 0], 1);
            $canvas->rectangle($rx, $y + $headerH, $col, $bodyH, [0, 0, 0], 1);
        });
        return $pdf->stream('Kontrak ' . $get_kontrak->Karyawan->name . '.pdf');
    }
    public function kontrak_post(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'tanggal_mulai' => 'required',
                'lama_kontrak' => 'required',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        // $parent = KaryawanKontrak::where('karyawan_id', $request->id_karyawan)->orderBy('created_at', 'desc')->first();
        // dd($parent);
        try {
            if ($request->lama_kontrak == '1bln') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addMonths(1);
            } elseif ($request->lama_kontrak == '3bln') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addMonths(3);
            } elseif ($request->lama_kontrak == '6bln') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addMonths(6);
            } elseif ($request->lama_kontrak == '1thn') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addYears(1);
            } elseif ($request->lama_kontrak == '2thn') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addYears(2);
            } elseif ($request->lama_kontrak == 'tetap') {
                $selesai = null;
            }
            if ($request->file_kontrak != null) {
                $file = $request->file('file_kontrak')->store('file_kontrak');
                $file_save = basename($file);
            } else {
                $file_save = null;
            }
            $kontrak = new KaryawanKontrak();
            $kontrak->id_kontrak = Uuid::uuid4();
            $kontrak->karyawan_id = $request->id_karyawan;
            $kontrak->file_kontrak = $file_save;
            $kontrak->tanggal_mulai = $request->tanggal_mulai;
            $kontrak->lama_kontrak = $request->lama_kontrak;
            $kontrak->tanggal_selesai = $selesai;
            // $kontrak->parent_id = $parent;
            $kontrak->created_at = date('Y-m-d H:i:s');
            $kontrak->save();

            $get_holding = Karyawan::where('id', $request->id_karyawan)->first();
            $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $nama_karyawan = Karyawan::where('id', $request->id_karyawan)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $kontrak->id_kontrak,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_kontrak',
                'activity' => 'post',
                'description' => 'Menambahkan Kontrak ' . $nama_karyawan->name,
                'keterangan' => 'Success',
            ]);

            return response()->json([
                'code' => 200,
                // 'data2' => $get_data2,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function kontrak_update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'tanggal_mulai' => 'required',
                'lama_kontrak' => 'required',
            ],
            [
                'required' => ':attribute Tidak boleh kosong!',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ]);
        }
        try {
            if ($request->lama_kontrak == '1bln') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addMonths(1);
            } elseif ($request->lama_kontrak == '3bln') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addMonths(3);
            } elseif ($request->lama_kontrak == '6bln') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addMonths(6);
            } elseif ($request->lama_kontrak == '1thn') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addYears(1);
            } elseif ($request->lama_kontrak == '2thn') {
                $selesai = Carbon::parse($request->tanggal_mulai)->addYears(2);
            } elseif ($request->lama_kontrak == 'tetap') {
                $selesai = null;
            }
            if ($request->file_kontrak != null) {
                if ($request->old_file != null) {
                    if (Storage::disk('file_kontrak')->exists($request->old_file)) {
                        Storage::disk('file_kontrak')->delete($request->old_file);
                    }
                }
                $file = $request->file('file_kontrak')->store('file_kontrak');
                $file_kontrak = basename($file);
            } else {
                $file_kontrak = $request->old_file;
            }
            $riwayat = KaryawanKontrak::where('id_kontrak', $request->id_kontrak)->first();
            // dd($riwayat);
            $riwayat->tanggal_mulai = $request->tanggal_mulai;
            $riwayat->lama_kontrak = $request->lama_kontrak;
            $riwayat->file_kontrak = $file_kontrak;
            $riwayat->tanggal_selesai = $selesai;
            $riwayat->updated_at = date('Y-m-d H:i:s');
            $riwayat->save();

            $get_holding = Karyawan::where('id', $riwayat->karyawan_id)->first();
            $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();

            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $nama_karyawan = Karyawan::where('id', $riwayat->karyawan_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_kontrak,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_kontrak',
                'activity' => 'update',
                'description' => 'Edit Kontrak ' . $nama_karyawan->name,
                'keterangan' => 'Success',
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function kontrak_delete(Request $request)
    {
        // dd($request->all());
        try {
            if ($request->file_kontrak != NULL) {

                Storage::disk('file_kontrak')->delete($request->file_kontrak);
            }

            $karyawan = KaryawanKontrak::where('id_kontrak', $request->id_kontrak)->first();
            $get_holding = Karyawan::where('id', $karyawan->karyawan_id)->first();
            $holdings = Holding::where('id', $get_holding->kontrak_kerja)->first();
            // dd($karyawan);
            $nama_menu = Menu::where('id', $request->menu_id)->first();
            $user_name = User::where('id', $request->user()->id)->first();
            $nama_karyawan = Karyawan::where('id', $karyawan->karyawan_id)->first();
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'user_name' => $user_name->username,
                'menu_id' => $request->menu_id,
                'menu_name' => $nama_menu->name,
                'id_table' => $request->id_kontrak,
                'holding_id' => $holdings->id ?? '-',
                'holding_category' => $holdings->holding_category ?? '-',
                'table' => 'karyawan_kontrak',
                'activity' => 'delete',
                'description' => 'Delete Kontrak ' . $nama_karyawan->name,
                'keterangan' => 'Success',
            ]);
            $karyawan->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Data Berhasil Diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'error' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }
    public function get_kabupaten($id)
    {
        // dd($id);
        $get_kabupaten = Cities::where('province_code', $id)->orderBy('name', 'ASC')->get();
        // return $get_kabupaten;
        echo "<option value=''>Pilih Kabupaten...</option>";
        foreach ($get_kabupaten as $kabupaten) {
            echo "<option value='$kabupaten->code'>$kabupaten->name</option>";
        }
    }
    public function get_kecamatan($id)
    {
        // dd($id);
        $get_desa = District::where('city_code', $id)->orderBy('name', 'ASC')->get();
        // return $get_desa;
        echo "<option value=''>Pilih Kecamatan...</option>";
        foreach ($get_desa as $desa) {
            echo "<option value='$desa->code'>$desa->name</option>";
        }
    }

    public function get_desa($id)
    {
        // dd($id);
        $get_kecamatan = Village::where('district_code', $id)->orderBy('name', 'ASC')->get();
        // return $get_kecamatan;
        echo "<option value=''>Pilih Desa...</option>";
        foreach ($get_kecamatan as $kecamatan) {
            echo "<option value='$kecamatan->code'>$kecamatan->name</option>";
        }
    }
}
