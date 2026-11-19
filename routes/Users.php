<?php

use App\Http\Controllers\AbsensiBawahanController;
use App\Http\Controllers\AbsenUserController;
use App\Http\Controllers\Approval\TicketKpiController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CutiUserController;
use App\Http\Controllers\HomeUserController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\IzinPulangController;
use App\Http\Controllers\IzinKeluarController;
use App\Http\Controllers\IzinLiburController;
use App\Http\Controllers\IzinSakitController;
use App\Http\Controllers\IzinTidakMasukController;
use App\Http\Controllers\IzinUserController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\MappingShiftController;
use App\Http\Controllers\PerdinController;
use App\Http\Controllers\PositionKaryawanController;
use App\Http\Controllers\PresensiFotoController;
use App\Http\Controllers\ReportPresensiBawahanController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\QuickPanelController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketVendorController;
use App\Http\Controllers\TicketVendorApprovalController;
use App\Http\Controllers\TukarShiftController;
use App\Http\Controllers\InventarisUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
  // HOME USER
  Route::get('/home/{id}', [HomeUserController::class, 'index'])->name('home.menu');
  Route::get('/get_count_absensi_home', [HomeUserController::class, 'get_count_absensi_home'])->name('get_count_absensi_home');

  // QUICK PANEL
  // GET
  Route::get('/getdata_quick_approval', [QuickPanelController::class, 'getdata_quick_approval'])->name('getdata_quick_approval');
  Route::get('/quick/approve/{id}', [QuickPanelController::class, 'approve']);
  Route::get('/quick/get_quick_approve/{id}', [QuickPanelController::class, 'get_quick_approve']);
  // POST
  Route::post('/quick/save_approve/{id}', [QuickPanelController::class, 'save_approve']);

  // HOME ABSEN
  Route::prefix('absen')
    ->group(function () {
      Route::get('dashboard/{id}', [AbsenUserController::class, 'index']);
      Route::get('get_dataAbsensi/{id}', [AbsenUserController::class, 'get_dataAbsensi']);
    });

  // Interview Manager
  Route::prefix('interview')
    ->group(function () {
      Route::get('dashboard/{id}', [InterviewController::class, 'index']);
      Route::get('detail/{menu_id}/{id}', [InterviewController::class, 'detail']);
      Route::get('pdfUserKaryawan/{id}', [InterviewController::class, 'pdfUserKaryawan']);
      Route::get('pdfNilaiKaryawan/{id}', [InterviewController::class, 'pdfNilaiKaryawan']);
      Route::post('approve/proses/{menu_id}', [InterviewController::class, 'prosesInterview']);
    });

  // Update Posisi
  Route::prefix('update_posisi')
    ->group(function () {
      Route::get('dashboard/{id}', [PositionKaryawanController::class, 'index']);
      Route::get('get_PosisiKaryawan/{id}', [PositionKaryawanController::class, 'get_PosisiKaryawan']);
      Route::get('get_tim_karyawan/{id}', [PositionKaryawanController::class, 'get_tim_karyawan']);
      Route::get('get_change_karyawan/{id}', [PositionKaryawanController::class, 'get_change_karyawan']);
      Route::get('get_departemen', [PositionKaryawanController::class, 'get_departemen']);
      Route::get('get_divisi', [PositionKaryawanController::class, 'get_divisi']);
      Route::get('get_bagian', [PositionKaryawanController::class, 'get_bagian']);
      Route::get('get_jabatan', [PositionKaryawanController::class, 'get_jabatan']);
      Route::post('proses_add_update_posisi', [PositionKaryawanController::class, 'proses_add_update_posisi']);
    });

  // Mapping Shift
  Route::prefix('mapping_shift')
    ->group(function () {
      // GET
      Route::get('dashboard/{id}', [MappingShiftController::class, 'index']);
      Route::get('tambah_mapping/{id}', [MappingShiftController::class, 'tambah_mapping']);
      Route::get('tambah_mapping_edit/{id_head}/{id}', [MappingShiftController::class, 'tambah_mapping_edit']);
      Route::get('get_shiftData/', [MappingShiftController::class, 'getShiftData']);
      Route::get('getKaryawanMappingShift', [MappingShiftController::class, 'getKaryawanMappingShift']);
      Route::get('getApproval/{id}', [MappingShiftController::class, 'getApproval']);
      Route::get('getDate/{id}', [MappingShiftController::class, 'getDate']);
      // POST
      Route::post('addMKaryawan', [MappingShiftController::class, 'addMKaryawan']);
      Route::post('addMKaryawan_edit/{id}', [MappingShiftController::class, 'addMKaryawan_edit']);
      Route::post('addMappingShift', [MappingShiftController::class, 'addMappingShift']);
      Route::post('update_mapping_shift', [MappingShiftController::class, 'update_mapping_shift']);
      Route::post('delete_mapping_shift', [MappingShiftController::class, 'delete_mapping_shift']);
    });

  Route::prefix('tukar_shift')
    ->group(function () {
      Route::get('dashboard/{id}', [TukarShiftController::class, 'index']);
      Route::get('get_TukarShiftData/', [TukarShiftController::class, 'getTukarShiftData']);
      Route::get('get_TukarShiftData_canvas/', [TukarShiftController::class, 'getTukarShiftData_canvas']);
      Route::get('get_TukarShiftData_pengajuan/{id}/', [TukarShiftController::class, 'getTukarShiftData_pengajuan']);
      Route::post('ubah_shift/', [TukarShiftController::class, 'ubah_shift']);
      Route::post('hapus_ubah_shift/', [TukarShiftController::class, 'hapus_ubah_shift']);
      Route::get('get_detail_tukar/', [TukarShiftController::class, 'get_detail_tukar']);

      // approval 1
      Route::get('get_TukarShiftData_atasan/', [TukarShiftController::class, 'getTukarShiftData_atasan']);
      Route::post('ubah_shift_atasan/', [TukarShiftController::class, 'ubah_shift_atasan']);
      Route::post('tolak_ubah_shift_atasan/', [TukarShiftController::class, 'tolak_ubah_shift_atasan']);
      // approval 2
      Route::get('get_TukarShiftData_hrd/', [TukarShiftController::class, 'getTukarShiftData_hrd']);
      Route::post('ubah_shift_hrd/', [TukarShiftController::class, 'ubah_shift_hrd']);
      Route::post('tolak_ubah_shift_hrd/', [TukarShiftController::class, 'tolak_ubah_shift_hrd']);
      // tukar shift atasan
      Route::get('edit_mapping/{id_head}/{id}', [TukarShiftController::class, 'edit_mapping']);
      Route::get('getDate/{id}', [TukarShiftController::class, 'getDate']);
      Route::get('getApproval/{id}', [TukarShiftController::class, 'getApproval']);
      Route::post('addMKaryawan/{id}', [TukarShiftController::class, 'addMKaryawan']);
      Route::post('editMappingShift/{id}', [TukarShiftController::class, 'editMappingShift']);
      Route::get('get_AtasanShiftData/', [TukarShiftController::class, 'getAtasanShiftData']);
      Route::get('detailTukarAtasan/{id}', [TukarShiftController::class, 'detailTukarAtasan']);
      Route::get('get_HRDShiftData/', [TukarShiftController::class, 'getHRDShiftData']);
      Route::get('detailTukarAtasanHRD/{id}/{menu_active}', [TukarShiftController::class, 'detailTukarAtasanHRD']);

      Route::post('ubah_shift_atasan_hrd/{menu_id}', [TukarShiftController::class, 'ubah_shift_atasan_hrd']);
      Route::post('tolak_ubah_shift_atasan_hrd/{menu_id}', [TukarShiftController::class, 'tolak_ubah_shift_atasan_hrd']);
    });

  // APPROVAL
  Route::prefix('approval')
    ->group(function () {
      Route::get('mapping_shift/{id}', [ApprovalController::class, 'mapping_shift']);
      Route::get('get_list_approval_mapping_shift/{id}', [ApprovalController::class, 'get_list_approval_mapping_shift']);
      Route::get('approvalDetail/{id}/{menu_id}', [ApprovalController::class, 'approvalDetail']);
      Route::get('get_list_approval_mapping_shift_detail/{id}', [ApprovalController::class, 'get_list_approval_mapping_shift_detail'])->where('id', '.*');
      Route::post('save_approval_mapping_shift/{id}', [ApprovalController::class, 'save_approval_mapping_shift']);
      // APPROVAL
      Route::get('dashboard/{menu}', [ApprovalController::class, 'dashboard']);
      Route::get('dashboard/cuti/{menu}', [ApprovalController::class, 'cuti']);
      // CUTI
      Route::get('cuti/get_cuti', [ApprovalController::class, 'get_cuti']);
      Route::get('cuti/get_cuti_riwayat', [ApprovalController::class, 'get_cuti_riwayat']);
      Route::get('cuti/cutiDetail/{id}', [ApprovalController::class, 'approveCutiDetail']);
      Route::get('cuti/cetak_form_cuti/{id}', [ApprovalController::class, 'cetak_form_cuti']);
      Route::get('cuti/get_cutiDetail/{id}', [ApprovalController::class, 'get_approveCutiDetail']);
      Route::post('cuti/save_approve/{id}', [ApprovalController::class, 'save_approval_cuti']);
      Route::post('/cuti/save_dobel/{id}', [ApprovalController::class, 'save_dobel']);
      // HISTORY APPROVAL
      Route::get('history/historyDetail/{id}', [ApprovalController::class, 'approveHistoryDetail']);
      Route::get('history/get_history_approval/{menu}', [ApprovalController::class, 'get_history_approval']);
      Route::get('history/historyDetail/get_HistoryDetailApproval/{id}', [ApprovalController::class, 'get_HistoryDetailApproval']);
    });

  Route::prefix('absensi_bawahan')
    ->group(function () {
      // GET
      Route::get('dashboard/{id}', [AbsensiBawahanController::class, 'index']);

      Route::get('get_dataAbsensiBawahan/{id}', [AbsensiBawahanController::class, 'get_dataAbsensiBawahan']);
      Route::get('get_dataAbsensiBawahan/{id}/{holdingId}', [AbsensiBawahanController::class, 'get_dataAbsensiBawahan']);
      Route::get('export/{holdingId}', [ReportPresensiBawahanController::class, 'exportMobile']);
      Route::get('detail_absensi_bawahan/{id}/{menu_id}', [AbsensiBawahanController::class, 'detail_absensi_bawahan']);
      Route::get('get_detail_karyawan_bawahan/{id}', [AbsensiBawahanController::class, 'get_detail_karyawan_bawahan']);
      Route::get('get_detail_absensi_bawahan/{id}', [AbsensiBawahanController::class, 'get_detail_absensi_bawahan']);
      Route::get('get_terlambat_Detail/{id}', [AbsensiBawahanController::class, 'get_terlambat_Detail']);
    });

  Route::prefix('laporan_presensi_bawahan')
    ->group(function () {
      Route::get('dashboard/{id}', [AbsensiBawahanController::class, 'indexLaporan']);
      Route::get('get_dataAbsensiBawahan/{id}', [AbsensiBawahanController::class, 'get_dataAbsensiBawahan']);
      Route::get('get_dataAbsensiBawahan/{id}/{holdingId}', [AbsensiBawahanController::class, 'get_dataAbsensiBawahan']);
      Route::get('export/{holdingId}', [ReportPresensiBawahanController::class, 'exportMobile']);
      Route::get('detail_absensi_bawahan/{id}/{menu_id}', [AbsensiBawahanController::class, 'detailLaporan']);
      Route::get('get_detail_karyawan_bawahan/{id}', [AbsensiBawahanController::class, 'get_detail_karyawan_bawahan']);
      Route::get('get_detail_absensi_bawahan/{id}', [AbsensiBawahanController::class, 'get_detail_absensi_bawahan']);
      Route::get('get_terlambat_Detail/{id}', [AbsensiBawahanController::class, 'get_terlambat_Detail']);
    });

  Route::prefix('izin')
    ->group(function () {
      Route::get('dashboard/{id}', [IzinUserController::class, 'index']);
      Route::get('terlambat/{id}', [IzinUserController::class, 'terlambat']);
      Route::get('get_terlambat/{id}', [IzinUserController::class, 'get_terlambat']);
      Route::get('get_jam_terlambat/{tanggal}', [IzinUserController::class, 'get_jam_terlambat']);
      Route::get('detail_terlambat/{id}/{id_2}', [IzinUserController::class, 'detail_terlambat']);
      Route::post('proses_pengajuan_terlambat/{id}', [IzinUserController::class, 'proses_pengajuan_terlambat']);
      Route::post('proses_hapus_terlambat/{id}', [IzinUserController::class, 'proses_hapus_terlambat']);
      Route::get('terlambat_approval/{id}', [IzinUserController::class, 'terlambat_approval']);
      Route::get('get_terlambat_approval', [IzinUserController::class, 'get_terlambat_approval']);
      Route::get('approve_terlambat_detail/{id}', [IzinUserController::class, 'approve_terlambat_detail']);
      Route::get('get_terlambat_Detail/{id}', [IzinUserController::class, 'get_terlambat_Detail']);
      Route::post('tolak_pengajuan_terlambat', [IzinUserController::class, 'tolak_pengajuan_terlambat']);
      Route::get('get_terlambat_riwayat', [IzinUserController::class, 'get_terlambat_riwayat']);
      Route::get('get_terlambat_riwayat_approval', [IzinUserController::class, 'get_terlambat_riwayat_approval']);
      Route::post('approve_pengajuan_terlambat', [IzinUserController::class, 'approve_pengajuan_terlambat']);
      Route::get('cetak_form_terlambat/{id}/{karyawan_id}', [IzinUserController::class, 'cetak_form_terlambat']);
      //PULANG CEPAT
      Route::get('pulang/{id}', [IzinPulangController::class, 'pulang']);
      Route::post('proses_pengajuan_pulang/{id}', [IzinPulangController::class, 'proses_pengajuan_pulang']);
      Route::get('get_pulang/{id}', [IzinPulangController::class, 'get_pulang']);
      Route::get('get_pulang_riwayat', [IzinPulangController::class, 'get_pulang_riwayat']);
      Route::get('detail_pulang/{id}/{id_2}', [IzinPulangController::class, 'detail_pulang']);
      Route::post('proses_hapus_pulang/{id}', [IzinPulangController::class, 'proses_hapus_pulang']);
      Route::get('pulang_approval/{id}', [IzinPulangController::class, 'pulang_approval']);
      Route::get('get_pulang_approval', [IzinPulangController::class, 'get_pulang_approval']);
      Route::get('get_pulang_riwayat_approval', [IzinPulangController::class, 'get_pulang_riwayat_approval']);
      Route::get('approve_pulang_detail/{id}', [IzinPulangController::class, 'approve_pulang_detail']);
      Route::get('get_pulang_detail/{id}', [IzinPulangController::class, 'get_pulang_detail']);
      Route::post('tolak_pengajuan_pulang', [IzinPulangController::class, 'tolak_pengajuan_pulang']);
      Route::post('approve_pengajuan_pulang', [IzinPulangController::class, 'approve_pengajuan_pulang']);
      Route::get('cetak_form_pulang/{id}/{karyawan_id}', [IzinPulangController::class, 'cetak_form_pulang']);
      //KELUAR
      Route::get('keluar/{id}', [IzinKeluarController::class, 'keluar']);
      Route::post('proses_pengajuan_keluar/{id}', [IzinKeluarController::class, 'proses_pengajuan_keluar']);
      Route::get('get_keluar/{id}', [IzinKeluarController::class, 'get_keluar']);
      Route::get('get_keluar_riwayat', [IzinKeluarController::class, 'get_keluar_riwayat']);
      Route::get('detail_keluar/{id}/{id_2}', [IzinKeluarController::class, 'detail_keluar']);
      Route::post('proses_hapus_keluar/{id}', [IzinKeluarController::class, 'proses_hapus_keluar']);
      Route::get('keluar_approval/{id}', [IzinKeluarController::class, 'keluar_approval']);
      Route::get('get_keluar_approval', [IzinKeluarController::class, 'get_keluar_approval']);
      Route::get('get_keluar_riwayat_approval', [IzinKeluarController::class, 'get_keluar_riwayat_approval']);
      Route::get('approve_keluar_detail/{id}', [IzinKeluarController::class, 'approve_keluar_detail']);
      Route::get('get_keluar_detail/{id}', [IzinKeluarController::class, 'get_keluar_detail']);
      Route::post('tolak_pengajuan_keluar', [IzinKeluarController::class, 'tolak_pengajuan_keluar']);
      Route::post('approve_pengajuan_keluar', [IzinKeluarController::class, 'approve_pengajuan_keluar']);
      Route::get('cetak_form_keluar/{id}/{karyawan_id}', [IzinKeluarController::class, 'cetak_form_keluar']);
      //SAKIT
      Route::get('sakit/{id}', [IzinSakitController::class, 'sakit']);
      Route::get('get_sakit/{id}', [IzinSakitController::class, 'get_sakit']);
      Route::get('get_sakit_riwayat', [IzinSakitController::class, 'get_sakit_riwayat']);
      Route::post('proses_pengajuan_sakit/{id}', [IzinSakitController::class, 'proses_pengajuan_sakit']);
      Route::get('detail_sakit/{id}/{id_2}', [IzinSakitController::class, 'detail_sakit']);
      Route::post('proses_hapus_sakit/{id}', [IzinSakitController::class, 'proses_hapus_sakit']);
      Route::get('sakit_approval/{id}', [IzinSakitController::class, 'sakit_approval']);
      Route::get('get_sakit_approval', [IzinSakitController::class, 'get_sakit_approval']);
      Route::get('get_sakit_riwayat_approval', [IzinSakitController::class, 'get_sakit_riwayat_approval']);
      Route::get('approve_sakit_detail/{id}', [IzinSakitController::class, 'approve_sakit_detail']);
      Route::get('get_sakit_detail/{id}', [IzinSakitController::class, 'get_sakit_detail']);
      Route::post('tolak_pengajuan_sakit', [IzinSakitController::class, 'tolak_pengajuan_sakit']);
      Route::post('approve_pengajuan_sakit', [IzinSakitController::class, 'approve_pengajuan_sakit']);
      Route::get('cetak_form_sakit/{id}/{karyawan_id}', [IzinSakitController::class, 'cetak_form_sakit']);
      //LIBUR
      Route::get('libur/{id}', [IzinLiburController::class, 'libur']);
      Route::post('proses_pengajuan_libur/{id}', [IzinLiburController::class, 'proses_pengajuan_libur']);
      Route::get('get_libur/{id}', [IzinLiburController::class, 'get_libur']);
      Route::get('get_libur_riwayat', [IzinLiburController::class, 'get_libur_riwayat']);
      Route::get('detail_libur/{id}/{id_2}', [IzinLiburController::class, 'detail_libur']);
      Route::post('proses_hapus_libur/{id}', [IzinLiburController::class, 'proses_hapus_libur']);
      Route::get('libur_approval/{id}', [IzinLiburController::class, 'libur_approval']);
      Route::get('get_libur_approval', [IzinLiburController::class, 'get_libur_approval']);
      Route::get('get_libur_riwayat_approval', [IzinLiburController::class, 'get_libur_riwayat_approval']);
      Route::get('approve_libur_detail/{id}', [IzinLiburController::class, 'approve_libur_detail']);
      Route::get('get_libur_detail/{id}', [IzinLiburController::class, 'get_libur_detail']);
      Route::post('tolak_pengajuan_libur', [IzinLiburController::class, 'tolak_pengajuan_libur']);
      Route::post('approve_pengajuan_libur', [IzinLiburController::class, 'approve_pengajuan_libur']);
      Route::get('cetak_form_libur/{id}/{karyawan_id}', [IzinLiburController::class, 'cetak_form_libur']);
      //TIDAK MASUK
      Route::get('tidak_masuk/{id}', [IzinTidakMasukController::class, 'tidak_masuk']);
      Route::get('get_tidak_masuk/{id}', [IzinTidakMasukController::class, 'get_tidak_masuk']);
      Route::get('get_tidak_masuk_riwayat', [IzinTidakMasukController::class, 'get_tidak_masuk_riwayat']);
      Route::post('proses_pengajuan_tidak_masuk/{id}', [IzinTidakMasukController::class, 'proses_pengajuan_tidak_masuk']);
      Route::get('detail_tidak_masuk/{id}/{id_2}', [IzinTidakMasukController::class, 'detail_tidak_masuk']);
      Route::post('proses_hapus_tidak_masuk/{id}', [IzinTidakMasukController::class, 'proses_hapus_tidak_masuk']);
      Route::get('tidak_masuk_approval/{id}', [IzinTidakMasukController::class, 'tidak_masuk_approval']);
      Route::get('get_tidak_masuk_approval', [IzinTidakMasukController::class, 'get_tidak_masuk_approval']);
      Route::get('get_tidak_masuk_riwayat_approval', [IzinTidakMasukController::class, 'get_tidak_masuk_riwayat_approval']);
      Route::get('approve_tidak_masuk_detail/{id}', [IzinTidakMasukController::class, 'approve_tidak_masuk_detail']);
      Route::get('get_tidak_masuk_detail/{id}', [IzinTidakMasukController::class, 'get_tidak_masuk_detail']);
      Route::post('tolak_pengajuan_tidak_masuk', [IzinTidakMasukController::class, 'tolak_pengajuan_tidak_masuk']);
      Route::post('approve_pengajuan_tidak_masuk', [IzinTidakMasukController::class, 'approve_pengajuan_tidak_masuk']);
      Route::get('cetak_form_tidak_masuk/{id}/{karyawan_id}', [IzinTidakMasukController::class, 'cetak_form_tidak_masuk']);
    });
  Route::prefix('presensi_foto')
    ->group(function () {
      Route::get('index/{id}', [PresensiFotoController::class, 'index']);
      Route::get('get_presensi/{id}', [PresensiFotoController::class, 'get_presensi']);
      Route::get('get_presensi_riwayat', [PresensiFotoController::class, 'get_presensi_riwayat']);
      Route::post('proses_pengajuan_presensi/{id}', [PresensiFotoController::class, 'proses_pengajuan_presensi']);
      Route::get('detail_presensi/{id}/{id_2}', [PresensiFotoController::class, 'detail_presensi']);
      Route::post('proses_hapus_presensi/{id}', [PresensiFotoController::class, 'proses_hapus_presensi']);
      Route::get('cetak_form_presensi/{id}/{karyawan_id}', [PresensiFotoController::class, 'cetak_form_presensi']);
    });

  Route::prefix('lembur')
    ->group(function () {
      Route::get('dashboard/{id}', [LemburController::class, 'dashboard']);
      Route::get('lembur_shift_akhir/{id}', [LemburController::class, 'lembur_shift_akhir']);
      Route::get('lembur_non_shift/{id}', [LemburController::class, 'lembur_non_shift']);
      Route::get('get_karyawan_lembur/{tgl}/{jam}', [LemburController::class, 'get_karyawan_lembur']);
      Route::get('get_non_shift_lembur', [LemburController::class, 'get_non_shift_lembur']);
      Route::get('get_keluar_shift/{id}', [LemburController::class, 'get_keluar_shift']);
      Route::get('get_lembur/{id}', [LemburController::class, 'get_lembur']);
      Route::get('get_lembur_riwayat', [LemburController::class, 'get_lembur_riwayat']);
      Route::get('get_lembur_riwayat_user', [LemburController::class, 'get_lembur_riwayat_user']);
      Route::post('proses_pengajuan_lembur/{id}', [LemburController::class, 'proses_pengajuan_lembur']);
      Route::get('user_detail/{id}/{id_2}', [LemburController::class, 'user_detail']);
      Route::post('proses_hapus_lembur/{id}', [LemburController::class, 'proses_hapus_lembur']);
      Route::get('lembur_approval/{id}', [LemburController::class, 'lembur_approval']);
      Route::get('get_lembur_approval', [LemburController::class, 'get_lembur_approval']);
      Route::get('get_lembur_riwayat_approval', [LemburController::class, 'get_lembur_riwayat_approval']);
      Route::get('approve_lembur_detail/{id}', [LemburController::class, 'approve_lembur_detail']);
      Route::get('get_lembur_detail/{id}', [LemburController::class, 'get_lembur_detail']);
      Route::post('tolak_pengajuan_lembur', [LemburController::class, 'tolak_pengajuan_lembur']);
      Route::post('approve_pengajuan_lembur', [LemburController::class, 'approve_pengajuan_lembur']);
      Route::get('cetak_form_lembur/{id}/{karyawan_id}', [LemburController::class, 'cetak_form_lembur']);
      Route::get('pantau_lembur/{id}', [LemburController::class, 'pantau_lembur']);
      Route::get('detail_user/{id}/{id_2}', [LemburController::class, 'user_detail']);
      Route::get('detail_lembur/{id}/{id_2}', [LemburController::class, 'detail_lembur']);
    });
  Route::prefix('perdin')
    ->group(function () {
      //PERDIN
      Route::get('dashboard/{id}', [PerdinController::class, 'perdin']);
      Route::get('get_perdin/{id}', [PerdinController::class, 'get_perdin']);
      Route::get('get_perdin_riwayat', [PerdinController::class, 'get_perdin_riwayat']);
      Route::post('proses_pengajuan_perdin/{id}', [PerdinController::class, 'proses_pengajuan_perdin']);
      Route::get('detail_perdin/{id}/{id_2}', [PerdinController::class, 'detail_perdin']);
      Route::post('proses_hapus_perdin/{id}', [PerdinController::class, 'proses_hapus_perdin']);
      Route::get('perdin_approval/{id}', [PerdinController::class, 'perdin_approval']);
      Route::get('get_perdin_approval', [PerdinController::class, 'get_perdin_approval']);
      Route::get('get_perdin_riwayat_approval', [PerdinController::class, 'get_perdin_riwayat_approval']);
      Route::get('approve_perdin_detail/{id}', [PerdinController::class, 'approve_perdin_detail']);
      Route::get('get_perdin_detail/{id}', [PerdinController::class, 'get_perdin_detail']);
      Route::post('tolak_pengajuan_perdin', [PerdinController::class, 'tolak_pengajuan_perdin']);
      Route::post('approve_pengajuan_perdin', [PerdinController::class, 'approve_pengajuan_perdin']);
      Route::get('cetak_form_perdin/{id}/{karyawan_id}', [PerdinController::class, 'cetak_form_perdin']);
    });
  // CUTI
  Route::prefix('cuti')
    ->group(function () {
      // GET
      Route::get('{id}', [CutiUserController::class, 'index']);
      Route::get('get_cuti/{id}', [CutiUserController::class, 'get_cuti']);
      Route::get('get_kategori_cuti/{id}', [CutiUserController::class, 'get_kategori_cuti']);
      Route::get('get_saldo_cuti/{id}', [CutiUserController::class, 'get_saldo_cuti']);
      Route::get('editCuti/{id}', [CutiUserController::class, 'editCuti']);
      Route::get('get_editCuti/{id}', [CutiUserController::class, 'get_editCuti']);
      Route::get('get_approval/{id}', [CutiUserController::class, 'get_approval']);
      Route::get('get_karyawan_pengganti/{id}', [CutiUserController::class, 'get_karyawan_pengganti']);
      Route::get('datatable_cuti', [CutiUserController::class, 'datatable_cuti'])->name('datatable_cuti');
      // HISTORY
      Route::get('history/get_history_cuti', [CutiUserController::class, 'get_history_cuti']);
      Route::get('history/history_detail/{id}', [CutiUserController::class, 'history_detail']);
      // CETAK FORM CUTI
      Route::get('history/cetak_form_cuti/{id}', [CutiUserController::class, 'cetak_form_cuti']);
      // POST
      Route::post('proses_pengajuan_cuti/{id}', [CutiUserController::class, 'proses_pengajuan_cuti']);
      Route::post('proses_hapus_cuti', [CutiUserController::class, 'proses_hapus_cuti']);
    });

  // PROFILE
  Route::prefix('profile')
    ->group(function () {
      // GET
      Route::get('{id}', [ProfileUserController::class, 'index'])->name('profile');
      Route::get('detail_profile/{id}', [ProfileUserController::class, 'detail_profile']);
      Route::get('detail_profile/get_kabupaten/{id}', [ProfileUserController::class, 'get_kabupaten']);
      Route::get('detail_profile/get_kecamatan/{id}', [ProfileUserController::class, 'get_kecamatan']);
      Route::get('detail_profile/get_desa/{id}', [ProfileUserController::class, 'get_desa']);
      Route::get('detail_hr/{id}', [ProfileUserController::class, 'detail_hr']);
      Route::get('detail_account/{id}', [ProfileUserController::class, 'detail_account']);
      Route::get('detail_account/change_password/{id}', [ProfileUserController::class, 'change_password']);
      Route::get('change_photoprofile_camera/{id}', [ProfileUserController::class, 'change_photoprofile_camera']);
      // LIHAT KONTRAK
      Route::get('lihat_kontrak_kerja/{id}', [ProfileUserController::class, 'lihat_kontrak_kerja']);
      Route::get('kontrak_download/{id}', [ProfileUserController::class, 'kontrak_download']);
      // LIHAT JABATAN
      Route::get('lihat_jabatan/{id}', [ProfileUserController::class, 'lihat_jabatan']);
      // LIHAT DOKUMEN
      Route::get('lihat_dokumen', [ProfileUserController::class, 'lihat_dokumen']);
      // LIHAT STRUKTUR ORGANISASI
      Route::get('lihat_struktur_organisasi', [ProfileUserController::class, 'lihat_struktur_organisasi']);
      // POST
      Route::get('lihat_rekan_kerja/{id}', [ProfileUserController::class, 'lihat_rekan_kerja']);
      Route::post('save_detail_profile', [ProfileUserController::class, 'save_detail_profile']);
      Route::post('save_detail_alamat', [ProfileUserController::class, 'save_detail_alamat']);
      Route::post('save_detail_account', [ProfileUserController::class, 'save_detail_account']);
      Route::post('save_detail_kontak_kantor', [ProfileUserController::class, 'save_detail_kontak_kantor']);
      Route::post('update_username', [ProfileUserController::class, 'update_username']);
      Route::post('save_change_password', [ProfileUserController::class, 'save_change_password']);
      Route::post('save_capture_profile', [ProfileUserController::class, 'save_capture_profile']);

      // Pendidikan
      Route::post('save_pendidikan', [ProfileUserController::class, 'save_pendidikan']);
      Route::post('update_pendidikan', [ProfileUserController::class, 'update_pendidikan']);
      Route::post('delete_pendidikan', [ProfileUserController::class, 'delete_pendidikan']);
      Route::post('dokumen_pendidikan', [ProfileUserController::class, 'dokumen_pendidikan']);
      Route::get('download_ijazah/{file}', [ProfileUserController::class, 'download_ijazah']);
      Route::get('download_transkrip_nilai/{file}', [ProfileUserController::class, 'download_transkrip_nilai']);

      Route::post('delete_profil', [ProfileUserController::class, 'delete_profil']);
      Route::post('delete_alamat', [ProfileUserController::class, 'delete_alamat']);
    });

  // INVENTARIS
  Route::prefix('inventaris')
    ->group(function () {
      //PERDIN
      Route::get('dashboard/{id}', [InventarisUserController::class, 'inventaris']);
      Route::get('inventaris_detail/{id}/{id_inventaris}', [InventarisUserController::class, 'inventaris_detail']);
    });

  Route::prefix('ticket')
    ->group(function () {
      // hub
      Route::get('hub/{menu_id}', [TicketController::class, 'hub'])->name('ticket.hub');
      // ticket ubah hapus
      Route::get('tambah_ticket/{id}', [TicketController::class, 'ticketHead_add'])->name('ticket.tambah');
      Route::post('simpan_ticket', [TicketController::class, 'ticketHead_store'])->name('ticket.head.store');
      Route::post('update_ticket/{id}', [TicketController::class, 'ticketHead_update'])->name('ticket.head.update');
      Route::delete('hapus_ticket', [TicketController::class, 'ticketHead_destroy'])->name('ticket.head.destroy');
      // get master
      Route::get('/get_ticket_kategori/{kategori}', [TicketController::class, 'get_ticket_kategori'])->name('ticket.get_ticket_kategori');
      Route::get('/get_menu_kategori/{kategori}', [TicketController::class, 'get_menu_kategori'])->name('submenu.get_menu_kategori');
      Route::get('/submenu/get_submenu_ticket/{id}', [TicketController::class, 'get_submenu_ticket'])->name('submenu.get_submenu_ticket');
      Route::get('/get_kondisi_menu/{menu_id}', [TicketController::class, 'getKondisiMenu'])->name('ticket.get_kondisi_menu');
      // ticket crud
      Route::get('edit/{menu_id}/{ticket_id}', [TicketController::class, 'editticket'])->name('ticket.edit');
      Route::get('lihat/{menu_id}/{ticket_id}', [TicketController::class, 'lihatTicket'])->name('ticket.lihat');
      Route::get('tambah_generated/draft/{draftTicketId}', [TicketController::class, 'ticketHead_addGeneratedDraft'])->name('ticket.tambah.generated.draft');
      Route::get('tambah_generated/{menuId}/{parentTicketId}/{subMenuId}', [TicketController::class, 'ticketHead_addGenerated'])->name('ticket.tambah.generated');
      Route::post('simpan_generated', [TicketController::class, 'ticketHead_storeGenerated'])->name('ticket.head.store.generated');
      Route::post('requestor-action/{ticketId}', [TicketController::class, 'requestorActionTicket'])->name('ticket.requestor.action');
      // export Report
      Route::get('export/{id}', [TicketController::class, 'ticketExport'])->name('ticket.export');
      Route::get('/get_departemen', [TicketController::class, 'getDepartemen'])->name('ticket.get_departemen');
      Route::get('/list_report', [TicketController::class, 'getListTicketReport'])->name('ticket.list_report');
      Route::get('report/{menu_id}', [TicketController::class, 'ticketReport'])->name('ticket.user.report');
      // wild card
      Route::get('/get_ticket/{id}', [TicketController::class, 'get_ticket_ubah_hapus'])->name('ticket.get_ticket');
      Route::get('{id}', [TicketController::class, 'index'])->name('ticket');
    });

  // ==========================================
  // VENDOR / FIS USER ROUTES (STANDALONE GROUP)
  // ==========================================
  Route::prefix('ticket/vendor')
    ->group(function () {
      Route::get('search-existing', [TicketVendorController::class, 'searchExistingVendors'])->name('ticket.vendor.search_existing');
      Route::get('{menu_id}/create', [TicketVendorController::class, 'create'])->name('ticket.vendor.create');
      Route::post('store', [TicketVendorController::class, 'store'])->name('ticket.vendor.store');
      Route::get('{menu_id}/show/{id}', [TicketVendorController::class, 'show'])->name('ticket.vendor.show');
      Route::get('preview-flow/{menuId}', [TicketVendorController::class, 'previewApproval'])->name('ticket.vendor.preview_flow');
      Route::get('{menu_id}', [TicketVendorController::class, 'index'])->name('ticket.vendor');
    });

  Route::prefix('approval/ticket')
    ->group(function () {
      Route::get('menu/{id}', [ApprovalController::class, 'approvalMenu'])
        ->name('approval.menu');
      // VALIDATOR
      Route::get('validator/{menuId}', [ApprovalController::class, 'validatorIndex'])->name('approval.ticket.validator.index');
      Route::get('validator/{menuId}/list', [ApprovalController::class, 'getListValidatorTicket'])->name('approval.ticket.validator.list');
      Route::get('validator/{menuId}/{ticketId}', [ApprovalController::class, 'showValidatorTicket'])->name('approval.ticket.validator.show');
      Route::post('validator/save/{ticketId}', [ApprovalController::class, 'saveValidatorTicket'])->name('approval.ticket.validator.save');
      Route::post('validator/attachment/{ticketId}', [ApprovalController::class, 'uploadValidatorAttachment'])->name('approval.ticket.validator.attachment.upload');
      Route::delete('validator/attachment/{attachmentId}', [ApprovalController::class, 'deleteValidatorAttachment'])->name('approval.ticket.validator.attachment.delete');
      Route::post('validator/pending/{ticketId}', [ApprovalController::class, 'pendingValidatorTicket'])->name('approval.ticket.validator.pending');
      Route::post('validator/resume/{ticketId}', [ApprovalController::class, 'resumeValidatorTicket'])->name('approval.ticket.validator.resume');
      Route::post('line-cancel/{ticketId}', [ApprovalController::class, 'saveLineCancel'])
        ->name('approval.ticket.line.cancel');
      Route::post('/ticket/validator/{menuId}/group-save', [ApprovalController::class, 'saveValidatorGroupTicket'])->name('approval.ticket.validator.group.save');
      // approval
      Route::get('get-list/{id}', [ApprovalController::class, 'getListTicket'])->name('approval.get_list_ticket');
      Route::get('approve/{id}/{ticketId}', [ApprovalController::class, 'showApproveTicket'])->name('approval.ticket.approve');
      Route::post('save-approve/{ticketId}', [ApprovalController::class, 'saveApproveTicket'])->name('approval.ticket.save');
      Route::post('checker/save/{ticketId}', [ApprovalController::class, 'saveCheckerTicket'])->name('approval.ticket.checker.save');
      Route::post('checker/generate/{approvalId}', [ApprovalController::class, 'retryGenerateApproval'])->name('approval.ticket.checker.generate');
      Route::get('checker/{menuId}/{ticketId}', [ApprovalController::class, 'showCheckerTicket'])->name('approval.ticket.checker');
      Route::post('checker/reject/{ticketId}', [ApprovalController::class, 'rejectCheckerTicket'])->name('approval.ticket.checker.reject');
      // asignment
      Route::get('assign/{menuId}', [ApprovalController::class, 'assignTicketIndex'])->name('approval.ticket.assign.index');
      Route::get('assign/{menuId}/list', [ApprovalController::class, 'getListAssignTicket'])->name('approval.ticket.assign.list');
      Route::get('assign/{menuId}/pic', [ApprovalController::class, 'getListPic'])->name('approval.ticket.assign.pic');
      Route::get('assign/{menuId}/detail/{ticketId}', [ApprovalController::class, 'getTicketAssignDetail'])->name('approval.ticket.assign.detail');
      Route::post('assign/store', [ApprovalController::class, 'storeAssignTicket'])->name('approval.ticket.assign.store');
      // pic
      Route::get('pic/{menuId}', [ApprovalController::class, 'picTicketIndex'])->name('approval.ticket.pic.index');
      Route::get('pic/{menuId}/list', [ApprovalController::class, 'getListPicTicket'])->name('approval.ticket.pic.list');
      Route::get('pic/{menuId}/detail/{ticketId}', [ApprovalController::class, 'getTicketPicDetail'])->name('approval.ticket.pic.detail');
      Route::post('pic/status/{ticketId}', [ApprovalController::class, 'updatePicTicketStatus'])->name('approval.ticket.pic.status');
      // KPI
      Route::get('kpi', [TicketKpiController::class, 'index'])->name('approval.ticket.kpi.index');
      Route::get('kpi/dashboard', [TicketKpiController::class, 'dashboard'])->name('approval.ticket.kpi.dashboard');
      Route::get('kpi/dashboard/{picId}', [TicketKpiController::class, 'dashboard'])->name('approval.ticket.kpi.dashboard.show');
      Route::get('kpi/difficulty-master', [TicketKpiController::class, 'difficultyMaster'])->name('approval.ticket.difficulty_master');
      Route::get('kpi/{picId}', [TicketKpiController::class, 'show'])->name('approval.ticket.kpi.show');
      // preview flow
      Route::get('preview-flow/{submenuId}', [TicketController::class, 'previewApprovalFlow'])->name('approval.ticket.preview_flow');
      Route::get('get-approval/{menuId}/{ticketId}', [ApprovalController::class, 'getApprovalInfo'])->name('ticket.get_approval');
      Route::get('{id}', [ApprovalController::class, 'ubahHapusIndex'])->name('approval.ticket');
    });

  // ==========================================
  // VENDOR / FIS APPROVAL ROUTES (STANDALONE GROUP)
  // ==========================================
  Route::prefix('approval/ticket/vendor')
    ->group(function () {
      Route::get('{menuId}/list', [TicketVendorApprovalController::class, 'list'])->name('approval.ticket.vendor.list');
      Route::get('{menuId}/{vendorId}', [TicketVendorApprovalController::class, 'show'])->name('approval.ticket.vendor.show');
      Route::post('save/{vendorId}', [TicketVendorApprovalController::class, 'save'])->name('approval.ticket.vendor.save');
      Route::get('{menuId}', [TicketVendorApprovalController::class, 'index'])->name('approval.ticket.vendor.index');
    });

  // Knowledge Base
  Route::prefix('knowledge-base')->group(function () {
    Route::get('/artikel/{artikel_slug}', [KnowledgeBaseController::class, 'kb_user_detail'])->name('user.kb.detail');
    Route::get('/attachment/{id}/download', [KnowledgeBaseController::class, 'downloadAttachment'])->name('knowledge-base.attachment.download');
    Route::get('/lampiran/{id}/download', [KnowledgeBaseController::class, 'kb_user_download'])
      ->name('user.kb.download');
    Route::get('/search', [KnowledgeBaseController::class, 'kb_user_search'])->name('user.kb.search');
    Route::get('/', [KnowledgeBaseController::class, 'kb_user_index'])->name('user.kb.index');
    Route::get('/{kategori_slug}', [KnowledgeBaseController::class, 'kb_user_kategori'])->name('user.kb.artikel');
  });

  // Fallback/Legacy route untuk /kb
  Route::get('/kb/{menu_id?}', function ($menu_id = null) {
    if ($menu_id && \Illuminate\Support\Facades\Auth::check()) {
      \App\Models\User::where('id', \Illuminate\Support\Facades\Auth::id())->update(['menu_active' => $menu_id]);
    }
    return redirect()->route('user.kb.index');
  });
});
