<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ApprovalMasterController;
use App\Http\Controllers\authController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\CutiMasterController;
use App\Http\Controllers\CutiReportController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\FingerController;
use App\Http\Controllers\HakAksesApprovalController;
use App\Http\Controllers\HistoryUserController;
use App\Http\Controllers\HoldingController;
use App\Http\Controllers\HomeUserController;
use App\Http\Controllers\jabatanController;
use App\Http\Controllers\karyawanController;
use App\Http\Controllers\KaryawanKesehatanController;
use App\Http\Controllers\KaryawanPendidikanController;
use App\Http\Controllers\KaryawanRiwayatController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MappingShiftController;
use App\Http\Controllers\MenuAccessController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\RecruitmentLaporanController;
use App\Http\Controllers\RecruitmentUserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportPresensiBawahanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\SummernoteController;
use App\Http\Controllers\UjianUserController;
use App\Http\Controllers\UserKaryawanController;
use App\Http\Controllers\DokumenLegalController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KategoriDokumenController;
use App\Http\Controllers\SubKategoriDokumenController;
use App\Http\Controllers\PenerbitDokumenController;
use App\Http\Controllers\DokumenLegalDashboardController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\InventarisRuanganController;
use App\Http\Controllers\IzinKeluarAdminController;
use App\Http\Controllers\IzinLiburAdminController;
use App\Http\Controllers\IzinPulangAdminController;
use App\Http\Controllers\IzinSakitAdminController;
use App\Http\Controllers\IzinTerlambatAdminController;
use App\Http\Controllers\IzinTidakMasukAdminController;
use App\Http\Controllers\IzinTidakMasukController;
use App\Http\Controllers\ProjectManagementController;
use App\Http\Controllers\ProjectManagementMeetingController;
use App\Http\Controllers\KaryawanJabatanController;
use App\Http\Controllers\KaryawanKontrakController;
use App\Http\Controllers\InventarisGedungController;
use App\Http\Controllers\InventarisKategoriController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MappingShiftReportController;
use App\Http\Controllers\MemoInternalController;
use App\Http\Controllers\ReportMasterController;
use App\Http\Controllers\TicketWebController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketVendorAdminController;
use App\Http\Controllers\TicketVendorController;
use App\Http\Controllers\TicketVendorApprovalController;
use App\Http\Controllers\TicketDinamisTypeController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\InventarisKondisiController;
use App\Http\Controllers\InventarisPemakaianController;
use App\Http\Controllers\InventarisPerjalananController;
use App\Http\Controllers\KaryawanKecualiController;
use App\Http\Controllers\KelolaAbsenController;
use App\Http\Controllers\KelolaBaruController;
use App\Http\Controllers\LemburAdminController;
use App\Http\Controllers\MappingShiftAdminController;
use App\Http\Controllers\PerdinAdminController;
use App\Http\Controllers\PerdinKendaraanController;
use App\Http\Controllers\PerdinKendaraanKantorController;
use App\Http\Controllers\PresensiFotoController;
use App\Http\Controllers\RuanganController;
use App\Models\Holding;
use App\Notifications\NotifContoh;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [authController::class, 'index']);
Route::get('/testConnection', [ReportController::class, 'testConnection']);
Route::get('/getLogsOnly', [ReportController::class, 'getLogsOnly']);
Route::middleware('auth')->group(function () {

    // PROFILE
    Route::get('/admin/profile/{holding}', [ProfileController::class, 'index']);
    Route::post('/admin/profile/save_profile/{holding}', [ProfileController::class, 'save_profile']);

    // DASHBOARD
    Route::get('/dashboard/hrd/{holding}', [dashboardController::class, 'index']);
    Route::get('/dashboard/portal/{holding}', [dashboardController::class, 'index_portal']);
    Route::get('/dashboard/option/{holding}', [dashboardController::class, 'dashboard_option']);
    Route::get('/dashboard/switch/{holding}', [dashboardController::class, 'dashboard_switch']);
    Route::get('/dashboard/holding', [dashboardController::class, 'holding'])->name('dashboard_holding');
    Route::get('/get_grafik_absensi_karyawan/{holding}', [dashboardController::class, 'get_grafik_absensi_karyawan']);
    Route::get('/graph_Dashboard_All/{holding}', [dashboardController::class, 'graph_Dashboard_All']);
    Route::get('/dashboard/dokumen-legal/{holding}', [DokumenLegalDashboardController::class, 'index'])->name('dokumen_legal.dashboard');
    Route::get('/dashboard/dokumen-legal/{holdingCode}/heatmap-detail', [DokumenLegalDashboardController::class, 'heatmapDetail'])->name('dashboard.dokumen-legal.heatmap-detail');
});

// REPORT MASTER
Route::get('/report_departemen/{holding}', [ReportMasterController::class, 'report_departemen']);
Route::get('/report_divisi/{holding}', [ReportMasterController::class, 'report_divisi']);
Route::get('/report_bagian/{holding}', [ReportMasterController::class, 'report_bagian']);
Route::get('report_jabatan-datatable/{holding}', [ReportMasterController::class, 'jabatan_datatable']);
Route::get('/report_jabatan/{holding}', [ReportMasterController::class, 'report_jabatan']);
Route::get('/report_holding/{holding}', [ReportMasterController::class, 'report_holding']);
Route::get('/report_lokasi/{holding}', [ReportMasterController::class, 'report_lokasi']);
Route::get('/report_site/{holding}', [ReportMasterController::class, 'report_site']);
Route::get('/report_finger/{holding}', [ReportMasterController::class, 'report_finger']);
Route::get('/report_shift/{holding}', [ReportMasterController::class, 'report_shift']);
Route::get('/report_cuti_kategori/{holding}', [ReportMasterController::class, 'report_cuti_kategori']);
// REPORT MASTER END

// MASTER DEPARTEMEN
Route::get('/departemen/{holding}', [DepartemenController::class, 'index']);
Route::get('/departemen-datatable/{holding}', [DepartemenController::class, 'datatable']);
Route::post('/departemen/insert/{holding}', [DepartemenController::class, 'insert']);
Route::get('/departemen/edit/{id}/{holding}', [DepartemenController::class, 'edit']);
Route::post('/departemen/update/{holding}', [DepartemenController::class, 'update']);
Route::get('/departemen/delete/{id}/{holding}/{menu_id}', [DepartemenController::class, 'delete']);
Route::post('/departemen/ImportDepartemen/{holding}', [DepartemenController::class, 'ImportDepartemen']);
Route::get('/departemen/divisi-datatable/{id?}/{holding}', [DepartemenController::class, 'divisi_datatable']);
Route::get('/departemen/karyawandepartemen-datatable/{id?}/{holding}', [DepartemenController::class, 'karyawandepartemen_datatable']);
// MASTER DIVISI
Route::get('/divisi/{holding}', [DivisiController::class, 'index']);
Route::get('/divisi-datatable/{holding}', [DivisiController::class, 'datatable']);
Route::get('/divisi/create/{holding}', [DivisiController::class, 'create']);
Route::post('/divisi/insert/{holding}', [DivisiController::class, 'insert']);
Route::get('/divisi/edit/{id}/{holding}', [DivisiController::class, 'edit']);
Route::post('/divisi/update/{holding}', [DivisiController::class, 'update']);
Route::get('/divisi/delete/{id}/{holding}/{menu_id}', [DivisiController::class, 'delete']);
Route::post('/divisi/ImportDivisi/{holding}', [DivisiController::class, 'ImportDivisi']);
Route::get('/divisi/bagian-datatable/{id?}/{holding}', [DivisiController::class, 'bagian_datatable']);
Route::get('/divisi/karyawandivisi-datatable/{id?}/{holding}', [DivisiController::class, 'karyawandivisi_datatable']);

// MASTER BAGIAN
Route::get('/bagian/{holding}', [BagianController::class, 'index']);
Route::get('/bagian-datatable/{holding}', [BagianController::class, 'datatable']);
Route::get('/bagian/create/{holding}', [BagianController::class, 'create']);
Route::post('/bagian/insert/{holding}', [BagianController::class, 'insert']);
Route::get('/bagian/edit/{id}/{holding}', [BagianController::class, 'edit']);
Route::post('/bagian/update/{holding}', [BagianController::class, 'update']);
Route::get('/bagian/delete/{id}/{holding}/{menu_id}', [BagianController::class, 'delete']);
Route::get('/bagian/get_divisi/{id}/{holding}', [BagianController::class, 'get_divisi']);
Route::get('/bagian/get_bagian/{id}', [BagianController::class, 'get_bagian']);
Route::post('/bagian/ImportBagian/{holding}', [BagianController::class, 'ImportBagian']);
Route::get('/jabatan/jabatan-datatable/{id?}/{holding}', [BagianController::class, 'jabatan_datatable']);
Route::get('/jabatan/karyawanjabatan-datatable/{id?}/{holding}', [BagianController::class, 'karyawanjabatan_datatable']);
Route::get('/jabatan/get_jabatan/{id}', [jabatanController::class, 'get_jabatan']);


// MASTER JABATAN
Route::get('/jabatan/{holding}', [jabatanController::class, 'index']);
Route::post('/jabatan_master_post', [jabatanController::class, 'jabatan_master_post']);
Route::get('/jabatan_master_dt', [jabatanController::class, 'jabatan_master_dt']);
Route::post('/jabatan_master_update', [jabatanController::class, 'jabatan_master_update']);
Route::post('/jabatan_master_delete', [jabatanController::class, 'jabatan_master_delete']);
Route::get('/jabatan/detail_jabatan/{id?}/{holding}', [jabatanController::class, 'detail_jabatan']);
Route::get('/jabatan-datatable/{id?}/{holding}', [jabatanController::class, 'datatable']);
Route::get('/bawahanjabatan-datatable/{id?}/{holding}', [jabatanController::class, 'bawahan_datatable']);
Route::get('/karyawanjabatan-datatable/{id?}/{holding}', [jabatanController::class, 'karyawan_datatable']);
Route::get('/jabatan/create/{holding}', [jabatanController::class, 'create']);
Route::post('/jabatan/insert/{holding}', [jabatanController::class, 'insert']);
Route::get('/jabatan/edit/{id}/{holding}', [jabatanController::class, 'edit']);
Route::post('/jabatan/update/', [jabatanController::class, 'update']);
Route::post('/jabatan/delete/', [jabatanController::class, 'delete']);
Route::get('/jabatan/get_bagian/{id}/{holding}', [jabatanController::class, 'get_bagian']);
Route::post('/jabatan/ImportJabatan/{holding}', [jabatanController::class, 'ImportJabatan']);

// MASTER LOG
Route::get('/log/{holding}', [LogController::class, 'index']);
Route::get('/log/dt_activity/{holding}', [LogController::class, 'dt_activity']);
Route::get('/log/dt_activity_mobile/{holding}', [LogController::class, 'dt_activity_mobile']);
Route::get('/log/dt_activity_karir/{holding}', [LogController::class, 'dt_activity_karir']);
Route::get('/log/dt_activity_system/{holding}', [LogController::class, 'dt_activity_system']);

// HOLDING
Route::get('/holding/{holding}', [HoldingController::class, 'index']);
Route::post('/holding/update', [HoldingController::class, 'holding_update']);
Route::post('/holding/post', [HoldingController::class, 'holding_post']);
Route::post('/holding/delete', [HoldingController::class, 'holding_delete']);
Route::get('/holding-datatable/{holding}', [HoldingController::class, 'datatable']);

// SITES
Route::get('/site/{holding}', [SitesController::class, 'index']);
Route::get('/site-datatable/{holding}', [SitesController::class, 'datatable']);
Route::post('/site/addSite/{holding}', [SitesController::class, 'addSite']);
Route::post('/site/updateSite/{holding}', [SitesController::class, 'updateSite']);
Route::post('/site/delete', [SitesController::class, 'site_delete']);

// LOKASI
Route::get('/lokasi/{holding}', [LokasiController::class, 'index']);
Route::get('/lokasi/tambah_lokasi/{holding}', [LokasiController::class, 'tambah_lokasi']);
Route::get('/lokasi-datatable/{holding}', [LokasiController::class, 'datatable']);
Route::post('/lokasi/add/{holding}', [LokasiController::class, 'addLokasi']);
Route::post('/lokasi/edit/{holding}', [LokasiController::class, 'updateLokasi']);
Route::get('/lokasi/delete/{id}/{holding}', [LokasiController::class, 'deleteLokasi']);
Route::put('/lokasi/radius/{id}/{holding}', [LokasiController::class, 'updateRadiusLokasi']);
Route::get('/lokasi/get_lokasi/{holding}', [LokasiController::class, 'get_lokasi']);

// SHIFT
Route::get('/shift/{holding}', [ShiftController::class, 'index']);
Route::get('/shift-datatable/{holding}', [ShiftController::class, 'datatable']);
Route::get('/shift/create/{holding}', [ShiftController::class, 'create']);
Route::post('/shift/store/{holding}', [ShiftController::class, 'store']);
Route::post('/shift/update/{holding}', [ShiftController::class, 'update']);
Route::get('/shift/delete/{id}/{holding}', [ShiftController::class, 'destroy']);
Route::get('/karyawan/shift/{id}/{holding}', [karyawanController::class, 'shift']);

//PROVINSI
Route::get('/karyawan/get_kabupaten/{id}', [KaryawanController::class, 'get_kabupaten']);
Route::get('/karyawan/get_kecamatan/{id}', [KaryawanController::class, 'get_kecamatan']);
Route::get('/karyawan/get_desa/{id}', [KaryawanController::class, 'get_desa']);


// FINGER MACHINE
Route::get('/finger/{holding}', [FingerController::class, 'index']);
Route::get('/finger-datatable/{holding}', [FingerController::class, 'datatable']);
Route::post('/finger/store/{holding}', [FingerController::class, 'store']);
Route::post('/finger/update/{holding}', [FingerController::class, 'update']);
Route::get('/finger/delete/{id}/{holding}', [FingerController::class, 'destroy']);

// MASTER MENU
Route::get('/menu/{holding}', [MenuController::class, 'index']);
Route::post('/recruitment/update_menu', [MenuController::class, 'update_menu']);
Route::post('/menu/save_all_change', [MenuController::class, 'save_all_change']);
Route::post('/menu/store/{holding}', [MenuController::class, 'store']);
Route::get('/menu/delete/{id}/{menu_id_add}', [MenuController::class, 'delete']);
Route::get('/menu/get_parent_options/{parentId}/{holding}', [MenuController::class, 'get_parent_options']);

// MASTER KATEGORI INVENTARIS
Route::get('/kategori_inventaris/{holding}', [InventarisKategoriController::class, 'index']);
Route::get('/kategori_inventaris-datatable', [InventarisKategoriController::class, 'datatable']);
Route::post('/kategori_inventaris/insert/{holding}', [InventarisKategoriController::class, 'insert']);
Route::get('/kategori_inventaris/edit/{id}/{holding}', [InventarisKategoriController::class, 'edit']);
Route::post('/kategori_inventaris/update/{holding}', [InventarisKategoriController::class, 'update']);
Route::get('/kategori_inventaris/delete/{id}/{holding}/{menu_id}', [InventarisKategoriController::class, 'delete']);

// MASTER KONDISI INVENTARIS
Route::get('/kondisi_inventaris/{holding}', [InventarisKondisiController::class, 'index']);
Route::get('/kondisi_inventaris-datatable', [InventarisKondisiController::class, 'datatable']);
Route::post('/kondisi_inventaris/insert/{holding}', [InventarisKondisiController::class, 'insert']);
Route::get('/kondisi_inventaris/edit/{id}/{holding}', [InventarisKondisiController::class, 'edit']);
Route::post('/kondisi_inventaris/update/{holding}', [InventarisKondisiController::class, 'update']);
Route::get('/kondisi_inventaris/delete/{id}/{holding}/{menu_id}', [InventarisKondisiController::class, 'delete']);

// INVENTARIS
Route::get('/inventaris/{holding}', [InventarisController::class, 'index']);
Route::get('/inventaris-datatable', [InventarisController::class, 'datatable']);
Route::post('/inventaris/insert/{holding}', [InventarisController::class, 'insert']);
Route::get('/inventaris/edit/{id}/{holding}', [InventarisController::class, 'edit']);
Route::post('/inventaris/update/{holding}', [InventarisController::class, 'update']);
Route::get('/inventaris/delete/{id}/{holding}/{menu_id}', [InventarisController::class, 'delete']);

//INVENTARIS PEMAKAIAN
Route::get('/inventaris_pemakaian/{holding}/{id}', [InventarisPemakaianController::class, 'index']);

// MASTER KATEGORI GEDUNG
Route::get('/kategori_gedung/{holding}', [InventarisGedungController::class, 'index']);
Route::get('/kategori_gedung-datatable', [InventarisGedungController::class, 'datatable']);
Route::post('/kategori_gedung/insert/{holding}', [InventarisGedungController::class, 'insert']);
Route::get('/kategori_gedung/edit/{id}/{holding}', [InventarisGedungController::class, 'edit']);
Route::post('/kategori_gedung/update/{holding}', [InventarisGedungController::class, 'update']);
Route::get('/kategori_gedung/delete/{id}/{holding}/{menu_id}', [InventarisGedungController::class, 'delete']);

// MASTER KATEGORI RUANGAN
Route::get('/ruangan/{holding}', [InventarisRuanganController::class, 'index']);
Route::get('/ruangan/get_gedung/{id}', [InventarisRuanganController::class, 'get_gedung']);
Route::get('/ruangan/get_ruangan/{id}', [InventarisRuanganController::class, 'get_ruangan']);
Route::get('/ruangan-datatable', [InventarisRuanganController::class, 'datatable']);
Route::post('/ruangan/insert/{holding}', [InventarisRuanganController::class, 'insert']);
Route::get('/ruangan/edit/{id}/{holding}', [InventarisRuanganController::class, 'edit']);
Route::post('/ruangan/update/{holding}', [InventarisRuanganController::class, 'update']);
Route::get('/ruangan/delete/{id}/{holding}/{menu_id}', [InventarisRuanganController::class, 'delete']);


// MASTER KENDARAAN UMUM
Route::get('/kendaraan_umum/{holding}', [PerdinKendaraanController::class, 'index']);
Route::get('/kendaraan_umum-datatable', [PerdinKendaraanController::class, 'datatable']);
Route::post('/kendaraan_umum/insert/{holding}', [PerdinKendaraanController::class, 'insert']);
Route::get('/kendaraan_umum/edit/{id}/{holding}', [PerdinKendaraanController::class, 'edit']);
Route::post('/kendaraan_umum/update/{holding}', [PerdinKendaraanController::class, 'update']);
Route::get('/kendaraan_umum/delete/{id}/{holding}/{menu_id}', [PerdinKendaraanController::class, 'delete']);

// MASTER KENDARAAN KANTOR
Route::get('/kendaraan_kantor/{holding}', [PerdinKendaraanKantorController::class, 'index']);
Route::get('/kendaraan_kantor-datatable', [PerdinKendaraanKantorController::class, 'datatable']);
Route::post('/kendaraan_kantor/insert/{holding}', [PerdinKendaraanKantorController::class, 'insert']);
Route::get('/kendaraan_kantor/edit/{id}/{holding}', [PerdinKendaraanKantorController::class, 'edit']);
Route::post('/kendaraan_kantor/update/{holding}', [PerdinKendaraanKantorController::class, 'update']);
Route::get('/kendaraan_kantor/delete/{id}/{holding}/{menu_id}', [PerdinKendaraanKantorController::class, 'delete']);

// MASTER PERJALANAN
Route::get('/perjalanan/{holding}', [InventarisPerjalananController::class, 'index']);
Route::get('/perjalanan-datatable', [InventarisPerjalananController::class, 'datatable']);
Route::post('/perjalanan/insert/{holding}', [InventarisPerjalananController::class, 'insert']);
Route::get('/perjalanan/edit/{id}/{holding}', [InventarisPerjalananController::class, 'edit']);
Route::post('/perjalanan/update/{holding}', [InventarisPerjalananController::class, 'update']);
Route::get('/perjalanan/delete/{id}/{holding}/{menu_id}', [InventarisPerjalananController::class, 'delete']);

// ROLE
Route::get('/role/{holding}', [RoleController::class, 'index']);
Route::get('/role-datatable/{holding}', [RoleController::class, 'datatable']);
Route::get('/menu-datatable/{id}/{holding}', [RoleController::class, 'datatable_menu']);
Route::get('/menu-datatable-update/{id}/{kategori}/{holding}', [RoleController::class, 'datatable_menu_update']);
Route::get('/role/menu-tree/{id}/{holding}', [RoleController::class, 'menuTree']);
Route::get('/role/add_role/{id}/{holding}', [RoleController::class, 'add_role']);
Route::post('/role/role_save_add/{holding}', [RoleController::class, 'role_save_add']);
Route::post('/role/role_save_update/{holding}', [RoleController::class, 'role_save_update']);


// ROLE
Route::get('/kelola_absen/{holding}', [KelolaAbsenController::class, 'index']);
Route::get('/kelola_absen_dt/{holding}', [KelolaAbsenController::class, 'kelola_absen_dt']);
Route::post('/kelola_absen/potong_cuti/{holding}', [KelolaAbsenController::class, 'potong_cuti']);

// MASTER KARYAWAN
Route::post('/karyawan/tambah-karyawan-proses/{id}', [karyawanController::class, 'tambahKaryawanProses']);
Route::put('/karyawan/proses-edit-shift/sp', [karyawanController::class, 'prosesEditShift']);
Route::put('/karyawan/proses-edit-shift/sps', [karyawanController::class, 'prosesEditShift']);
Route::put('/karyawan/proses-edit-shift/sip', [karyawanController::class, 'prosesEditShift']);
Route::get('/karyawan/{holding}', [karyawanController::class, 'index']);
Route::get('/karyawan/ptkp/{holding}', [karyawanController::class, 'ptkp'])->name('ptkp');
Route::get('/karyawan/ptkp_datatable/{holding}', [karyawanController::class, 'ptkp_datatable'])->name('ptkp_datatable');
Route::get('/karyawan/ptkp_pdf/{holding}', [karyawanController::class, 'ptkp_pdf']);
Route::get('/karyawan_bulanan-datatable/{holding}', [karyawanController::class, 'datatable_bulanan']);
Route::get('/karyawan_bulanan_tenggang-datatable/{holding}', [karyawanController::class, 'datatable_bulanan_tenggang']);
Route::get('/karyawan_tenggang_atasan-datatable/{holding}', [karyawanController::class, 'datatable_tenggang_atasan']);
Route::get('/karyawan_tenggang_menengah-datatable/{holding}', [karyawanController::class, 'datatable_tenggang_menengah']);
Route::get('/karyawan_tenggang_bawahan-datatable/{holding}', [karyawanController::class, 'datatable_tenggang_bawahan']);
Route::get('/karyawan_bulanan_nonaktif-datatable/{holding}', [karyawanController::class, 'datatable_bulanan_nonaktif']);
Route::get('/karyawan_akan_nonaktif-datatable/{holding}', [karyawanController::class, 'datatable_akan_nonaktif']);
Route::get('/karyawan_akan_nonaktif-delete/{id}/{holding}/{menu_id}', [karyawanController::class, 'datatable_akan_nonaktif_delete']);
Route::get('/karyawan_harian-datatable/{holding}', [karyawanController::class, 'datatable_harian']);
Route::get('/karyawan/{holding}', [karyawanController::class, 'index']);
Route::get('/karyawan_harian-datatable/{holding}', [karyawanController::class, 'datatable_harian']);
Route::get('/karyawan/detail/{id}/{holding}', [karyawanController::class, 'detail']);
Route::post('/karyawan/proses-edit/{id}/sp', [karyawanController::class, 'editKaryawanProses']);
Route::post('/karyawan/proses-edit/{id}/sps', [karyawanController::class, 'editKaryawanProses']);
Route::post('/karyawan/proses-edit/{id}/sip', [karyawanController::class, 'editKaryawanProses']);
Route::get('/karyawan/delete/{id}/sp', [karyawanController::class, 'deleteKaryawan']);
Route::get('/karyawan/delete/{id}/sps', [karyawanController::class, 'deleteKaryawan']);
Route::get('/karyawan/delete/{id}/sip', [karyawanController::class, 'deleteKaryawan']);
Route::post('/karyawan/ImportKaryawan/{holding}', [karyawanController::class, 'ImportKaryawan']);
Route::get('/karyawan/ExportKaryawan/{holding}', [karyawanController::class, 'ExportKaryawan']);
Route::get('/karyawan/pdfKaryawan/{holding}', [karyawanController::class, 'download_pdf_karyawan']);
Route::get('/karyawan/inventaris/{id}/{holding}', [karyawanController::class, 'inventaris']);


Route::get('/pengajuan_profil/{holding}', [karyawanController::class, 'pengajuan_profil']);
Route::get('/dt_pengajuan_profil/{holding}', [karyawanController::class, 'dt_pengajuan_profil']);
Route::get('/dt_pengajuan_history/{holding}', [karyawanController::class, 'dt_pengajuan_history']);
Route::get('/dt_pengajuan_alamat/{holding}', [karyawanController::class, 'dt_pengajuan_alamat']);
Route::get('/dt_pengajuan_alamat_history/{holding}', [karyawanController::class, 'dt_pengajuan_alamat_history']);

Route::post('/pengajuan_profil_update', [karyawanController::class, 'pengajuan_profil_update']);
Route::post('/pengajuan_profil_ditolak', [karyawanController::class, 'pengajuan_profil_ditolak']);
Route::post('/pengajuan_alamat_update', [karyawanController::class, 'pengajuan_alamat_update']);
Route::post('/pengajuan_alamat_ditolak', [karyawanController::class, 'pengajuan_alamat_ditolak']);


// GET DEPARTEMEN,DIVISI
Route::get('karyawan/get_departemen/{id}', [karyawanController::class, 'get_departemen']);
Route::get('karyawan/get_divisi/{id}', [karyawanController::class, 'get_divisi']);
Route::get('karyawan/get_bagian/{id}', [karyawanController::class, 'get_bagian']);
Route::get('karyawan/get_jabatan/{id}', [karyawanController::class, 'get_jabatan']);

// CUTI KARYAWAN
Route::get('/cuti_karyawan/{holding}', [CutiController::class, 'index'])->name('cuti_karyawan');
Route::get('/cuti_karyawan_detail/{id_karyawan}/{holding}', [CutiController::class, 'detail'])->name('cuti_karyawan_detail');
Route::get('/dt_karyawan_kuota_cuti/{holding}', [CutiController::class, 'dt_karyawan_kuota_cuti'])->name('dt_karyawan_kuota_cuti');
Route::get('/dt_detail_kuota_cuti/{id_karyawan}/{holding}', [CutiController::class, 'dt_detail_kuota_cuti'])->name('dt_detail_kuota_cuti');
Route::get('/dt_karyawan_hari_cuti/{holding}', [CutiController::class, 'dt_karyawan_hari_cuti'])->name('dt_karyawan_hari_cuti');
Route::post('/cuti_update/{holding}', [CutiController::class, 'update'])->name('cuti_update');
Route::post('/hari_update/{holding}', [CutiController::class, 'hari_update'])->name('hari_update');
Route::post('/hapus_hari_update/{holding}', [CutiController::class, 'hapus_hari_update'])->name('hapus_hari_update');

// IZIN SAKIT KARYAWAN
Route::get('/sakit_karyawan/{holding}', [IzinSakitAdminController::class, 'index'])->name('sakit_karyawan');
Route::get('/dt_sakit/{holding}', [IzinSakitAdminController::class, 'dt_sakit'])->name('dt_sakit');
Route::post('/sakit_approve_hrd/{holding}', [IzinSakitAdminController::class, 'sakit_approve_hrd'])->name('sakit_approve_hrd');
Route::get('/dt_sakit_riwayat/{holding}', [IzinSakitAdminController::class, 'dt_sakit_riwayat'])->name('dt_sakit_riwayat');
Route::get('/sakit_report_karyawan/{holding}', [IzinSakitAdminController::class, 'sakit_report_karyawan'])->name('sakit_report_karyawan');
Route::get('/dt_sakit_report/{holding}', [IzinSakitAdminController::class, 'dt_sakit_report'])->name('dt_sakit_report');

// IZIN TIDAK MASUK KARYAWAN
Route::get('/tidak_masuk_karyawan/{holding}', [IzinTidakMasukAdminController::class, 'index'])->name('tidak_masuk_karyawan');
Route::get('/dt_tidak_masuk/{holding}', [IzinTidakMasukAdminController::class, 'dt_tidak_masuk'])->name('dt_tidak_masuk');
Route::post('/tidak_masuk_approve_hrd/{holding}', [IzinTidakMasukAdminController::class, 'tidak_masuk_approve_hrd'])->name('tidak_masuk_approve_hrd');
Route::get('/dt_tidak_masuk_riwayat/{holding}', [IzinTidakMasukAdminController::class, 'dt_tidak_masuk_riwayat'])->name('dt_tidak_masuk_riwayat');
Route::get('/tidak_masuk_report_karyawan/{holding}', [IzinTidakMasukAdminController::class, 'tidak_masuk_report_karyawan'])->name('tidak_masuk_report_karyawan');
Route::get('/dt_tidak_masuk_report/{holding}', [IzinTidakMasukAdminController::class, 'dt_tidak_masuk_report'])->name('dt_tidak_masuk_report');

// IZIN TERLAMBAT KARYAWAN
Route::get('/terlambat_karyawan/{holding}', [IzinTerlambatAdminController::class, 'index'])->name('terlambat_karyawan');
Route::get('/dt_terlambat/{holding}', [IzinTerlambatAdminController::class, 'dt_terlambat'])->name('dt_terlambat');
Route::post('/terlambat_approve_hrd/{holding}', [IzinTerlambatAdminController::class, 'terlambat_approve_hrd'])->name('terlambat_approve_hrd');
Route::get('/dt_terlambat_riwayat/{holding}', [IzinTerlambatAdminController::class, 'dt_terlambat_riwayat'])->name('dt_terlambat_riwayat');
Route::get('/terlambat_report_karyawan/{holding}', [IzinTerlambatAdminController::class, 'terlambat_report_karyawan'])->name('terlambat_report_karyawan');
Route::get('/dt_terlambat_report/{holding}', [IzinTerlambatAdminController::class, 'dt_terlambat_report'])->name('dt_terlambat_report');

// IZIN KELUAR KARYAWAN
Route::get('/keluar_karyawan/{holding}', [IzinKeluarAdminController::class, 'index'])->name('keluar_karyawan');
Route::get('/dt_keluar/{holding}', [IzinKeluarAdminController::class, 'dt_keluar'])->name('dt_keluar');
Route::post('/keluar_approve_hrd/{holding}', [IzinKeluarAdminController::class, 'keluar_approve_hrd'])->name('keluar_approve_hrd');
Route::get('/dt_keluar_riwayat/{holding}', [IzinKeluarAdminController::class, 'dt_keluar_riwayat'])->name('dt_keluar_riwayat');
Route::get('/keluar_report_karyawan/{holding}', [IzinKeluarAdminController::class, 'keluar_report_karyawan'])->name('keluar_report_karyawan');
Route::get('/dt_keluar_report/{holding}', [IzinKeluarAdminController::class, 'dt_keluar_report'])->name('dt_keluar_report');

// IZIN PULANG KARYAWAN
Route::get('/pulang_karyawan/{holding}', [IzinPulangAdminController::class, 'index'])->name('pulang_karyawan');
Route::get('/dt_pulang/{holding}', [IzinPulangAdminController::class, 'dt_pulang'])->name('dt_pulang');
Route::post('/pulang_approve_hrd/{holding}', [IzinPulangAdminController::class, 'pulang_approve_hrd'])->name('pulang_approve_hrd');
Route::get('/dt_pulang_riwayat/{holding}', [IzinPulangAdminController::class, 'dt_pulang_riwayat'])->name('dt_pulang_riwayat');
Route::get('/pulang_report_karyawan/{holding}', [IzinPulangAdminController::class, 'pulang_report_karyawan'])->name('pulang_report_karyawan');
Route::get('/dt_pulang_report/{holding}', [IzinPulangAdminController::class, 'dt_pulang_report'])->name('dt_pulang_report');

// IZIN LIBUR KARYAWAN
Route::get('/libur_karyawan/{holding}', [IzinLiburAdminController::class, 'index'])->name('libur_karyawan');
Route::get('/dt_libur/{holding}', [IzinLiburAdminController::class, 'dt_libur'])->name('dt_libur');
Route::post('/libur_approve_hrd/{holding}', [IzinLiburAdminController::class, 'libur_approve_hrd'])->name('libur_approve_hrd');
Route::get('/dt_libur_riwayat/{holding}', [IzinLiburAdminController::class, 'dt_libur_riwayat'])->name('dt_libur_riwayat');
Route::get('/libur_report_karyawan/{holding}', [IzinLiburAdminController::class, 'libur_report_karyawan'])->name('libur_report_karyawan');
Route::get('/dt_libur_report/{holding}', [IzinLiburAdminController::class, 'dt_libur_report'])->name('dt_libur_report');

// PRESENSI FOTO
Route::get('/presensi_foto_karyawan/{holding}', [PresensiFotoController::class, 'presensi_foto_karyawan'])->name('presensi_foto_karyawan');
Route::get('/dt_presensi/{holding}', [PresensiFotoController::class, 'dt_presensi'])->name('dt_presensi');
Route::post('/presensi_approve_hrd/{holding}', [PresensiFotoController::class, 'presensi_approve_hrd'])->name('presensi_approve_hrd');
Route::get('/dt_presensi_riwayat/{holding}', [PresensiFotoController::class, 'dt_presensi_riwayat'])->name('dt_presensi_riwayat');
Route::get('/presensi_foto_report/{holding}', [PresensiFotoController::class, 'presensi_foto_report'])->name('presensi_foto_report');
Route::get('/dt_presensi_report/{holding}', [PresensiFotoController::class, 'dt_presensi_report'])->name('dt_presensi_report');

// PERDIN
Route::get('/perdin_karyawan/{holding}', [PerdinAdminController::class, 'index'])->name('perdin_karyawan');
Route::get('/dt_perdin/{holding}', [PerdinAdminController::class, 'dt_perdin'])->name('dt_perdin');
Route::post('/perdin_approve_hrd/{holding}', [PerdinAdminController::class, 'perdin_approve_hrd'])->name('perdin_approve_hrd');
Route::get('/dt_perdin_riwayat/{holding}', [PerdinAdminController::class, 'dt_perdin_riwayat'])->name('dt_perdin_riwayat');
Route::get('/perdin_report_karyawan/{holding}', [PerdinAdminController::class, 'perdin_report_karyawan'])->name('perdin_report_karyawan');
Route::get('/dt_perdin_report/{holding}', [PerdinAdminController::class, 'dt_perdin_report'])->name('dt_perdin_report');

// LEMBUR KARYAWAN
Route::get('/lembur_karyawan/{holding}', [LemburAdminController::class, 'index'])->name('lembur_karyawan');
Route::get('/detail_lembur_karyawan/{holding}/{id}', [LemburAdminController::class, 'detail_lembur_karyawan'])->name('detail_lembur_karyawan');
Route::get('/lembur_karyawan_report/{id}', [LemburAdminController::class, 'lembur_karyawan_report'])->name('lembur_karyawan_report');
Route::get('/dt_lembur/{holding}', [LemburAdminController::class, 'dt_lembur'])->name('dt_lembur');
Route::get('/dt_detail_lembur/{id}', [LemburAdminController::class, 'dt_detail_lembur'])->name('dt_detail_lembur');
Route::get('/dt_detail_lembur_report/{id}', [LemburAdminController::class, 'dt_detail_lembur_report'])->name('dt_detail_lembur_report');
Route::get('/dt_detail_lembur_report2/{holding}', [LemburAdminController::class, 'dt_detail_lembur_report2'])->name('dt_detail_lembur_report2');
Route::post('/lembur_approve_hrd/{holding}', [LemburAdminController::class, 'lembur_approve_hrd'])->name('lembur_approve_hrd');
Route::get('/dt_lembur_riwayat/{holding}', [LemburAdminController::class, 'dt_lembur_riwayat'])->name('dt_lembur_riwayat');
Route::get('/dt_lembur_riwayat_report/{holding}', [LemburAdminController::class, 'dt_lembur_riwayat_report'])->name('dt_lembur_riwayat_report');
Route::get('/lembur_report_karyawan/{holding}', [LemburAdminController::class, 'lembur_report_karyawan'])->name('lembur_report_karyawan');
Route::get('/dt_lembur_report/{holding}', [LemburAdminController::class, 'dt_lembur_report'])->name('dt_lembur_report');

// PENDIDIKAN KARYAWAN
Route::get('/karyawan/pendidikan/{id}', [karyawanController::class, 'pendidikan_datatable']);
Route::post('/karyawan/AddPendidikan/', [karyawanController::class, 'add_pendidikan']);
Route::post('/karyawan/UpdatePendidikan/', [karyawanController::class, 'update_pendidikan']);
Route::post('/karyawan/DeletePendidikan/', [karyawanController::class, 'delete_pendidikan']);

// Riwayat Pekerjaan
Route::get('/karyawan/riwayat/{id}', [KaryawanRiwayatController::class, 'riwayat_datatable']);
Route::post('/karyawan/riwayat_update', [KaryawanRiwayatController::class, 'riwayat_update']);
Route::post('/karyawan/riwayat_post', [KaryawanRiwayatController::class, 'riwayat_post']);
Route::post('/karyawan/delete_riwayat/{id}', [KaryawanRiwayatController::class, 'delete_riwayat']);
Route::get('/karyawan/button_riwayat/{id?}', [KaryawanRiwayatController::class, 'button_riwayat'])->name('button_riwayat');

// Jabatan Karyawan
Route::get('/karyawan/jabatan/{id}', [KaryawanJabatanController::class, 'jabatan_datatable']);
Route::get('/karyawan/jabatan/get_departemen/{id}', [KaryawanJabatanController::class, 'get_departemen']);
Route::get('/karyawan/jabatan/get_divisi/{id}', [KaryawanJabatanController::class, 'get_divisi']);
Route::get('/karyawan/jabatan/get_bagian/{id}', [KaryawanJabatanController::class, 'get_bagian']);
Route::get('/karyawan/jabatan/get_jabatan/{id}', [KaryawanJabatanController::class, 'get_jabatan']);
Route::post('/karyawan/jabatan_update', [KaryawanJabatanController::class, 'jabatan_update']);
Route::post('/karyawan/jabatan_post', [KaryawanJabatanController::class, 'jabatan_post']);
Route::post('/karyawan/kategori_jabatan_post', [KaryawanJabatanController::class, 'kategori_jabatan_post']);
Route::post('/karyawan/jabatan_delete', [KaryawanJabatanController::class, 'jabatan_delete']);
Route::get('/karyawan/button_jabatan/{id?}', [KaryawanJabatanController::class, 'button_jabatan'])->name('button_jabatan');

// cuti
Route::get('/cuti_kategori/{holding}', [CutiMasterController::class, 'cuti_kategori']);
Route::get('/dt_cuti_kategori', [CutiMasterController::class, 'dt_cuti_kategori']);
Route::post('/cuti_kategori_create', [CutiMasterController::class, 'cuti_kategori_create']);
Route::post('/cuti_kategori_update', [CutiMasterController::class, 'cuti_kategori_update']);
Route::post('/cuti_kategori_delete', [CutiMasterController::class, 'cuti_kategori_delete']);

// Info HR dan kontrak
Route::get('/karyawan/kategori_cuti_datatable/{id}/{holding}', [karyawanController::class, 'kategori_cuti_datatable']);
Route::post('/karyawan/hr_jabatan_post', [KaryawanKontrakController::class, 'hr_jabatan_post']);
Route::get('/karyawan/kontrak_datatable/{id}/{holding}', [KaryawanKontrakController::class, 'kontrak_datatable']);
Route::get('/karyawan/kontrak_download/{id}/{holding}', [KaryawanKontrakController::class, 'kontrak_download']);
Route::post('/karyawan/kontrak_post', [KaryawanKontrakController::class, 'kontrak_post']);
Route::post('/karyawan/kontrak_update', [KaryawanKontrakController::class, 'kontrak_update']);
Route::post('/karyawan/kontrak_delete', [KaryawanKontrakController::class, 'kontrak_delete']);

// MASTER KARYAWAN TAB
Route::get('/karyawan/dokumen_datadiri/{id}', [karyawanController::class, 'dokumen_datadiri']);
Route::post('/karyawan/profil-edit/{id}', [karyawanController::class, 'editProfil']);
Route::post('/karyawan/bank-edit/{id}', [karyawanController::class, 'editBank']);
Route::post('/karyawan/pajak-edit/{id}', [karyawanController::class, 'editPajak']);
Route::post('/karyawan/bpjs-edit/{id}', [karyawanController::class, 'editBPJS']);
Route::post('/karyawan/jabatan-edit/{id}', [karyawanController::class, 'editJabatan']);
Route::post('/karyawan/ijazah-edit/{id}', [karyawanController::class, 'ijazah_update']);

// PENDIDIKAN KARYAWAN
Route::get('/karyawan/pendidikan/button_pendidikan/{id}', [KaryawanPendidikanController::class, 'button_pendidikan'])->name('button_pendidikan');
Route::get('/karyawan/pendidikan/{id}', [KaryawanPendidikanController::class, 'pendidikan_datatable']);
Route::post('/karyawan/AddPendidikan/', [karyawanPendidikanController::class, 'add_pendidikan']);
Route::post('/karyawan/UpdatePendidikan/', [karyawanPendidikanController::class, 'update_pendidikan']);
Route::post('/karyawan/DeletePendidikan/{id}', [karyawanPendidikanController::class, 'delete_pendidikan']);

// kesehatan
Route::get('/cpanel/cv/kesehatan/kesehatan_get/{id}', [KaryawanKesehatanController::class, 'kesehatan_get'])->name('kesehatan_get');
Route::post('/cpanel/cv/kesehatan/kesehatan_post', [KaryawanKesehatanController::class, 'kesehatan_post'])->name('kesehatan_post');

Route::post('/cpanel/cv/kesehatan/pengobatan_post', [KaryawanKesehatanController::class, 'pengobatan_post'])->name('pengobatan_post');
Route::post('/cpanel/cv/kesehatan/pengobatan_delete', [KaryawanKesehatanController::class, 'pengobatan_delete'])->name('pengobatan_delete');
Route::post('/cpanel/cv/kesehatan/pengobatan_reset', [KaryawanKesehatanController::class, 'pengobatan_reset'])->name('pengobatan_reset');
Route::get('/cpanel/cv/kesehatan/dt_pengobatan/{id}', [KaryawanKesehatanController::class, 'dt_pengobatan'])->name('dt_pengobatan');
Route::get('/cpanel/cv/kesehatan/pengobatan_count/{id}', [KaryawanKesehatanController::class, 'pengobatan_count'])->name('pengobatan_count');

Route::post('/cpanel/cv/kesehatan/rumah_sakit_post', [KaryawanKesehatanController::class, 'rumah_sakit_post'])->name('rumah_sakit_post');
Route::post('/cpanel/cv/kesehatan/rumah_sakit_delete', [KaryawanKesehatanController::class, 'rumah_sakit_delete'])->name('rumah_sakit_delete');
Route::post('/cpanel/cv/kesehatan/rumah_sakit_reset', [KaryawanKesehatanController::class, 'rumah_sakit_reset'])->name('rumah_sakit_reset');
Route::get('/cpanel/cv/kesehatan/dt_rumah_sakit/{id}', [KaryawanKesehatanController::class, 'dt_rumah_sakit'])->name('dt_rumah_sakit');
Route::get('/cpanel/cv/kesehatan/rumah_sakit_count/{id}', [KaryawanKesehatanController::class, 'rumah_sakit_count'])->name('rumah_sakit_count');


Route::post('/cpanel/cv/kesehatan/kecelakaan_post', [KaryawanKesehatanController::class, 'kecelakaan_post'])->name('kecelakaan_post');
Route::post('/cpanel/cv/kesehatan/kecelakaan_delete', [KaryawanKesehatanController::class, 'kecelakaan_delete'])->name('kecelakaan_delete');
Route::post('/cpanel/cv/kesehatan/kecelakaan_reset', [KaryawanKesehatanController::class, 'kecelakaan_reset'])->name('kecelakaan_reset');
// Route::get('/cpanel/cv/kesehatan/rumah_sakit_count', [KaryawanKesehatanController::class, 'rumah_sakit_count'])->name('rumah_sakit_count');
Route::get('/cpanel/cv/kesehatan/dt_kecelakaan/{id}', [KaryawanKesehatanController::class, 'dt_kecelakaan'])->name('dt_kecelakaan');
Route::get('/cpanel/cv/kesehatan/kecelakaan_count/{id}', [KaryawanKesehatanController::class, 'kecelakaan_count'])->name('kecelakaan_count');
// kesehatan end

// KEAHLIAN KARYAWAN
Route::get('/karyawan/button_keahlian/{id}', [karyawanController::class, 'button_keahlian'])->name('button_keahlian');
Route::get('/karyawan/keahlian/{id}', [karyawanController::class, 'keahlian_datatable']);
Route::post('/karyawan/AddKeahlian/', [karyawanController::class, 'add_keahlian']);
Route::post('/karyawan/UpdateKeahlian/', [karyawanController::class, 'update_keahlian']);
Route::post('/karyawan/DeleteKeahlian/{id}', [karyawanController::class, 'delete_keahlian']);

Route::get('/non_aktif/{holding}', [karyawanController::class, 'non_aktif']);
Route::post('/karyawan/non_aktif_proses', [karyawanController::class, 'non_aktif_proses']);

Route::get('/masa_tenggang_kontrak/{holding}', [karyawanController::class, 'masa_tenggang_kontrak']);
Route::get('/database_masa_tenggang_kontrak/{holding}', [karyawanController::class, 'database_masa_tenggang_kontrak']);
Route::post('/karyawan/update_kontrak_proses', [karyawanController::class, 'update_kontrak_proses']);


// USERS FINGER
Route::get('/users/users_finger/{holding}', [UserKaryawanController::class, 'index_finger']);
Route::get('/users/users_finger-datatable/{holding}', [UserKaryawanController::class, 'users_finger_datatable']);

// USERS APP
Route::get('/users/user_app/{holding}', [UserKaryawanController::class, 'index_users']);
Route::post('/users/user_app/prosesTambahUser/{holding}', [UserKaryawanController::class, 'prosesTambahUser']);
Route::get('/users_bulanan-datatable/{holding}', [UserKaryawanController::class, 'datatable_users_bulanan']);
Route::get('/users_harian-datatable/{holding}', [UserKaryawanController::class, 'datatable_users_harian']);
Route::get('/users/user_app/edit-password/{id}/{holding}', [UserKaryawanController::class, 'editPassword']);
Route::post('/users/user_app/edit-password-proses/{id}/{holding}', [UserKaryawanController::class, 'editPasswordProses']);
Route::get('/users/user_app/get_user_karyawan/{holding}', [UserKaryawanController::class, 'get_user_karyawan']);
Route::get('/users/user_app/edit_karyawan/{id}/{holding}', [UserKaryawanController::class, 'edit_karyawan']);
Route::post('/users/user_app/update_user_karyawan/{holding}', [UserKaryawanController::class, 'update_user_karyawan']);
Route::post('/users/user_app/non_aktif_proses', [UserKaryawanController::class, 'non_aktif_proses']);
Route::post('/users/user_app/aktif_proses', [UserKaryawanController::class, 'aktif_proses']);
Route::post('/users/user_app/ImportUser/{holding}', [UserKaryawanController::class, 'ImportUser']);
Route::post('/users/user_app/ImportUpdateUser/{holding}', [UserKaryawanController::class, 'ImportUpdateUser']);
Route::get('/users/user_app/ExportUser/{holding}', [UserKaryawanController::class, 'ExportUser']);
Route::get('/users/user_app/pdfUserKaryawan/{holding}', [UserKaryawanController::class, 'download_pdf_user_karyawan']);

Route::get('/users/user_app/approval_role_option/{level}/{holding}', [UserKaryawanController::class, 'approval_role_option']);
Route::post('/users/user_app/approval_save_add/{holding}', [UserKaryawanController::class, 'approval_save_add']);


// HAK AKSES MENU KARYAWAN
Route::get('/users/hak_akses/menu/{holding}', [MenuAccessController::class, 'index']);
Route::get('/users/hak_akses/menu-datatable/{holding}', [MenuAccessController::class, 'get_datatable']);
Route::post('/users/hak_akses/save_access/{holding}', [MenuAccessController::class, 'save_access']);
Route::get('/users/hak_akses/karyawan-datatable/{id}/{holding}', [MenuAccessController::class, 'karyawan_datatable']);

// HAK AKSES APPROVAL KARYAWAN 
Route::get('/users/hak_akses/approval/{holding}', [HakAksesApprovalController::class, 'index']);
Route::get('/users/hak_akses/approvalChart/{holding}', [HakAksesApprovalController::class, 'approvalChart']);
Route::get('/users/hak_akses/approval-datatable/{holding}', [HakAksesApprovalController::class, 'get_datatable']);
Route::post('/users/hak_akses/save_approval/{holding}', [HakAksesApprovalController::class, 'save_approval']);
Route::get('/users/hak_akses/karyawan-datatable/{id}/{group}/{holding}', [HakAksesApprovalController::class, 'karyawan_datatable']);

// PENGECUALIAN ABSEN
Route::get('/karyawan_kecuali/{holding}', [KaryawanKecualiController::class, 'index']);
Route::get('/karyawan_kecuali-datatable', [KaryawanKecualiController::class, 'datatable']);
Route::post('/karyawan_kecuali/insert/{holding}', [KaryawanKecualiController::class, 'insert']);
Route::get('/karyawan_kecuali/edit/{id}/{holding}', [KaryawanKecualiController::class, 'edit']);
Route::post('/karyawan_kecuali/update/{holding}', [KaryawanKecualiController::class, 'update']);
Route::get('/karyawan_kecuali/delete/{id}/{holding}/{menu_id}', [KaryawanKecualiController::class, 'delete']);

// KARYAWAN R
Route::get('/karyawan_r/{holding}', [KelolaBaruController::class, 'index']);
Route::get('/karyawan_r/get_tanggal/{tanggal}', [KelolaBaruController::class, 'get_tanggal']);
Route::get('/karyawan_r/get_cut/{tanggal}', [KelolaBaruController::class, 'get_cut']);
Route::get('/karyawan_r-datatable', [KelolaBaruController::class, 'datatable']);
Route::post('/karyawan_r/insert/{holding}', [KelolaBaruController::class, 'insert']);
Route::get('/karyawan_r/edit/{id}/{holding}', [KelolaBaruController::class, 'edit']);
Route::post('/karyawan_r/update/{holding}', [KelolaBaruController::class, 'update']);
Route::get('/karyawan_r/delete/{id}/{holding}/{menu_id}', [KelolaBaruController::class, 'delete']);

// mapping shift
Route::get('/karyawan/shift/{id}/sp', [karyawanController::class, 'shift']);
Route::get('/karyawan/shift/{id}/sps', [karyawanController::class, 'shift']);
Route::get('/karyawan/shift/{id}/sip', [karyawanController::class, 'shift']);
Route::get('/karyawan/mapping_shift_datatable/{id}/{holding}', [karyawanController::class, 'mapping_shift_datatable']);
Route::post('/karyawan/shift/proses-tambah-shift/sp', [karyawanController::class, 'prosesTambahShift']);
Route::post('/karyawan/shift/proses-tambah-shift/sps', [karyawanController::class, 'prosesTambahShift']);
Route::post('/karyawan/shift/proses-tambah-shift/sip', [karyawanController::class, 'prosesTambahShift']);
Route::get('/karyawan/delete-shift/{id}/sp', [karyawanController::class, 'deleteShift']);
Route::get('/karyawan/delete-shift/{id}/sps', [karyawanController::class, 'deleteShift']);
Route::get('/karyawan/delete-shift/{id}/sip', [karyawanController::class, 'deleteShift']);
Route::get('/karyawan/edit-shift/{id}/sp', [karyawanController::class, 'editShift']);
Route::get('/karyawan/edit-shift/{id}/sps', [karyawanController::class, 'editShift']);
Route::get('/karyawan/edit-shift/{id}/sip', [karyawanController::class, 'editShift']);


// mapping shift NEW
Route::get('/karyawan/mapping_shift/{holding}', [MappingShiftController::class, 'mapping_shift_index']);
Route::get('/karyawan/mapping_shift/detail/{id}/{holding}', [MappingShiftController::class, 'mapping_shift_detail_index']);
Route::get('/karyawan/get_karyawan_mapping/{holding}', [MappingShiftController::class, 'get_karyawan_mapping']);
Route::get('/karyawan/mapping_shift_datatable/{holding}', [MappingShiftController::class, 'mapping_shift_datatable']);
Route::get('/karyawan/mapping_shift_detail_datatable/{id}/{holding}', [MappingShiftController::class, 'mapping_shift_detail_datatable']);
Route::post('/karyawan/mapping_shift/proses-tambah-shift/{holding}', [MappingShiftController::class, 'prosesTambahDetailShift']);
Route::get('/karyawan/mapping_calendar/{holding}', [MappingShiftController::class, 'mapping_calendar']);
Route::get('/karyawan/mapping_calendar/getDetailTanggal/{holding}', [MappingShiftController::class, 'getDetailTanggal']);
Route::get('/mapping_shift/get_columns/{holding}', [MappingShiftController::class, 'get_columns']);

Route::get('/mapping_shift_approval/{holding}', [MappingShiftAdminController::class, 'index']);
Route::get('/dt_shift_approval/{holding}', [MappingShiftAdminController::class, 'dt_shift_approval']);
Route::get('/dt_detail_shift_approval/{holding}/{id}', [MappingShiftAdminController::class, 'dt_detail_shift_approval'])->name('dt_detail_shift_approval');
Route::post('/shift_approve/{holding}', [MappingShiftAdminController::class, 'shift_approve']);



Route::post('/shift/proses-tambah-shift/sp', [MappingShiftController::class, 'prosesTambahShift']);
Route::post('/shift/proses-tambah-shift/sps', [MappingShiftController::class, 'prosesTambahShift']);
Route::post('/shift/proses-tambah-shift/sip', [MappingShiftController::class, 'prosesTambahShift']);
Route::get('/karyawan/delete-shift/sp', [MappingShiftController::class, 'deleteShift']);
Route::get('/karyawan/delete-shift/sps', [MappingShiftController::class, 'deleteShift']);
Route::get('/karyawan/delete-shift/sip', [MappingShiftController::class, 'deleteShift']);
Route::get('/karyawan/edit-shift/sp', [MappingShiftController::class, 'editShift']);
Route::get('/karyawan/edit-shift/sps', [MappingShiftController::class, 'editShift']);
Route::get('/karyawan/edit-shift/sip', [MappingShiftController::class, 'editShift']);

Route::get('/mapping_shift/get_divisi', [MappingShiftController::class, 'get_divisi']);
Route::get('/mapping_shift/get_bagian', [MappingShiftController::class, 'get_bagian']);
Route::get('/mapping_shift/get_jabatan', [MappingShiftController::class, 'get_jabatan']);


Route::post('/mapping_shift/prosesAddMappingShift/{holding}', [MappingShiftController::class, 'prosesAddMappingShift']);
Route::post('/karyawan/mapping_shift/prosesEditMappingShift/{holding}', [MappingShiftController::class, 'prosesEditMappingShift']);


// STRUKTUR ORGANISASI
Route::get('/struktur_organisasi/{holding}', [StrukturOrganisasiController::class, 'index']);
Route::get('/struktur_get_parent/{id}/{holding_id}', [StrukturOrganisasiController::class, 'struktur_get_parent']);
Route::get('/get_tree_struktur/{holding}', [StrukturOrganisasiController::class, 'get_tree_struktur']);
Route::post('/struktur_store/{holding_id}', [StrukturOrganisasiController::class, 'struktur_store']);
Route::post('/struktur_update/{holding_id}', [StrukturOrganisasiController::class, 'struktur_update']);
Route::post('/struktur_bawahan/{holding_id}', [StrukturOrganisasiController::class, 'struktur_bawahan']);
Route::get('/struktur_bawahan/divisi/{id}/{holding_id}', [StrukturOrganisasiController::class, 'struktur_divisi']);
Route::get('/struktur_bawahan/bagian/{id}', [StrukturOrganisasiController::class, 'struktur_bagian']);
Route::get('/struktur_bawahan_select/{id_bagian}', [StrukturOrganisasiController::class, 'struktur_bawahan_select']);
Route::post('/struktur/struktur_delete', [StrukturOrganisasiController::class, 'struktur_delete']);
Route::get('/struktur_organisasi/dt_karyawan/{id_jabatan}', [StrukturOrganisasiController::class, 'dt_karyawan']);


Route::get('/dt_struktur/', [StrukturOrganisasiController::class, 'dt_struktur']);


// ACCESS
Route::get('/access/{holding}', [AccessController::class, 'index']);
Route::get('/access-datatable/{holding}', [AccessController::class, 'datatable']);
Route::get('/access/role_access_datatable/{id}/{holding}', [AccessController::class, 'role_access_datatable']);
Route::get('/access/add_access/{id}/{holding}', [AccessController::class, 'add_access']);
Route::post('/access/access_save_add/{holding}', [AccessController::class, 'access_save_add']);


// APPROVAL MASTER
Route::get('/approval/{holding}', [ApprovalMasterController::class, 'index']);
Route::get('/approval/get_tree_approval/{holding}', [ApprovalMasterController::class, 'get_tree_approval']);
Route::get('/approval/get_group/{id}/{holding}', [ApprovalMasterController::class, 'get_group']);
Route::get('/approval/get_menu_group/{holding}', [ApprovalMasterController::class, 'get_menu_group']);
Route::get('/approval/get_add_approval/{id}/{holding}', [ApprovalMasterController::class, 'get_add_approval']);


Route::get('/approval/get_parent/{id_approval}/{group_id}/{holding_id}', [ApprovalMasterController::class, 'get_parent']);
Route::get('/approval-datatable/{holding}', [ApprovalMasterController::class, 'datatable']);
Route::get('/approval_group-datatable/{holding}', [ApprovalMasterController::class, 'group_datatable']);
Route::post('/approval/approval_save_add/{holding}', [ApprovalMasterController::class, 'approval_save_add']);
Route::post('/approval/approval_save_update/{holding}', [ApprovalMasterController::class, 'approval_save_update']);
Route::post('/approval/approval_group_save_add/{holding}', [ApprovalMasterController::class, 'approval_group_save_add']);
Route::post('/approval/approval_group_save_update/{holding}', [ApprovalMasterController::class, 'approval_group_save_update']);
Route::post('/approval/group_delete', [ApprovalMasterController::class, 'group_delete']);
Route::post('/approval/approval_delete', [ApprovalMasterController::class, 'approval_delete']);
Route::get('/approval/reject/{id}/{holding}', [ApprovalMasterController::class, 'reject']);


// REPORT ABSENSI
Route::get('/report/{holding}', [ReportController::class, 'index']);
Route::get('/report-datatable/{holding}', [ReportController::class, 'datatable']);
Route::get('/report-datatable_finger/{holding}', [ReportController::class, 'datatable_finger']);
Route::get('/report/get_divisi/{holding}', [ReportController::class, 'get_divisi']);
Route::get('/report/get_bagian/{holding}', [ReportController::class, 'get_bagian']);
Route::get('/report/get_jabatan/{holding}', [ReportController::class, 'get_jabatan']);
Route::get('/report/get_columns/{holding}', [ReportController::class, 'get_columns']);
Route::get('/report/get_filter_month', [ReportController::class, 'get_filter_month']);
Route::get('/report/ExportReport', [ReportController::class, 'ExportReport']);
Route::get('/report/cetak_form_cuti/{id}/{cuti_id}', [ReportController::class, 'cetak_form_cuti']);

Route::get('/report/absensi/kedisiplinan/{holding}', [ReportController::class, 'index_kedisiplinan']);
Route::get('/report/absensi/kedisiplinan1/{holding}', [ReportController::class, 'index_kedisiplinan1']);
Route::get('/report/absensi/kedisiplinan-datatable/{holding}', [ReportController::class, 'datatable_kedisiplinan']);
Route::get('/report/absensi/kedisiplinan-datatable1/{holding}', [ReportController::class, 'datatable_kedisiplinan1']);
Route::get('/report/absensi/kedisiplinan/get_divisi/{holding}', [ReportController::class, 'get_divisi']);
Route::get('/report/absensi/kedisiplinan/get_bagian/{holding}', [ReportController::class, 'get_bagian']);
Route::get('/report/absensi/kedisiplinan/get_jabatan/{holding}', [ReportController::class, 'get_jabatan']);
Route::get('/report/absensi/kedisiplinan/get_columns/{holding}', [ReportController::class, 'get_columns_kedisiplinan']);
Route::get('/report/absensi/kedisiplinan/get_grafik_absensi/{holding}', [ReportController::class, 'get_grafik_absensi']);

// DETAIL REPORT KEDISIPLINAN 
Route::get('/report/absensi/kedisiplinan/detail/{id}/{holding}/{start}/{end}', [ReportController::class, 'detail_index_kedisiplinan']);
Route::get('/report/absensi/kedisiplinan/detail_datatable/{id}/{holding}', [ReportController::class, 'detail_datatable']);
// END DETAIL REPORT KEDISIPLINAN

// REPORT PRESENSI BAWAHAN
Route::get('/reportpresensibawahan/{holding}', [ReportPresensiBawahanController::class, 'index']);
Route::get('/reportpresensibawahan/employees/{holding}', [ReportPresensiBawahanController::class, 'employees']);
Route::get('/reportpresensibawahan/scope/{holding}/{karyawanId}', [ReportPresensiBawahanController::class, 'scope']);
Route::get('/reportpresensibawahan/attendance/{holding}/{karyawanId}', [ReportPresensiBawahanController::class, 'attendance']);
Route::post('/reportpresensibawahan/relations/{holding}', [ReportPresensiBawahanController::class, 'saveRelations']);

//MAPPING SHIFT REPORT
Route::get('/report/mapping_shift/{holding}', [MappingShiftReportController::class, 'index'])->name('mapping_shift_report');
Route::get('/report/dt_laporan_shift/{holding}', [MappingShiftReportController::class, 'dt_laporan_shift'])->name('dt_laporan_shift');
Route::get('/report/mapping_shift_detail_report/{holding}/{id}', [MappingShiftReportController::class, 'mapping_shift_detail_report'])->name('mapping_shift_detail_report');
Route::get('/report/dt_laporan_detail_shift/{holding}/{id}', [MappingShiftReportController::class, 'dt_laporan_detail_shift'])->name('dt_laporan_detail_shift');
Route::get('/report/dt_laporan_detail_shift2/{holding}/{id}', [MappingShiftReportController::class, 'dt_laporan_detail_shift2'])->name('dt_laporan_detail_shift2');

Route::get('/report/dt_laporan_tukar/{holding}/{id}', [MappingShiftReportController::class, 'dt_laporan_tukar'])->name('dt_laporan_tukar');
Route::get('/report/mapping_tukar_detail_report/{holding}/{id}', [MappingShiftReportController::class, 'mapping_tukar_detail_report'])->name('mapping_tukar_detail_report');
Route::get('/report/dt_laporan_detail_tukar/{holding}/{id}', [MappingShiftReportController::class, 'dt_laporan_detail_tukar'])->name('dt_laporan_detail_tukar');

//MAPPING SHIFT REPORT END

///CUTI REPORT END
Route::get('/report/cuti/{holding}', [CutiReportController::class, 'index'])->name('cuti_report');
Route::get('/cuti_report_detail/{id?}/{id_karyawan}/{holding}', [CutiReportController::class, 'detail'])->name('cuti_report_detail');
Route::get('/dt_detail_report_cuti/{id}/{id_karyawan}/{holding}', [CutiReportController::class, 'dt_detail_report_cuti'])->name('dt_detail_report_cuti');
Route::get('/report/dt_report_cuti/{holding}', [CutiReportController::class, 'dt_report_cuti'])->name('dt_report_cuti');
Route::get('/report/dt_laporan_kuota_cuti/{holding}', [CutiReportController::class, 'dt_laporan_kuota_cuti'])->name('dt_laporan_kuota_cuti');
Route::get('/report/cetak_form_cuti_detail/{id}', [CutiReportController::class, 'cetak_form_cuti_detail']);

///CUTI REPORT END



// MEMO INTERNAL
Route::get('/memo_internal/{holding}', [MemoInternalController::class, 'index']);
Route::get('/memo_internal/datatable/{holding}', [MemoInternalController::class, 'memo_datatable']);
Route::get('/memo_internal/destroy/{id}', [MemoInternalController::class, 'delete']);
Route::get('/memo_internal/memo_file_pdf/{id}', [MemoInternalController::class, 'memo_file_pdf']);
// POST
Route::post('memo_internal/store/{holding}', [MemoInternalController::class, 'store']);

// END MEMO INTERNAL
// Export
Route::get('/report/absensi/kedisiplinan/RekapAbsensiKedisiplinan/{holding}', [ExportController::class, 'RekapAbsensiKedisiplinan']);
// MENU RECRUITMENT

// RECRUITMENT DASHBOARD ADMIN
Route::get('/pg-data-recruitment/{holding}', [RecruitmentController::class, 'pg_recruitment']);
Route::get('/pg-data-recruitment/data-list-pelamar/{id?}/{holding}', [RecruitmentController::class, 'pg_list_pelamar']);
Route::get('/pg-data-recruitment/pelamar-detail/{id?}/{holding}', [RecruitmentController::class, 'pelamar_detail']);
Route::get('/pg-data-recruitment/data-list-ranking/{id?}/{holding}', [RecruitmentController::class, 'pg_list_ranking']);


Route::post('/recruitment/create/{holding}', [RecruitmentController::class, 'create']);

Route::post('/recruitment/update', [RecruitmentController::class, 'update']);

Route::get('/recruitment/delete/{id?}/{holding}/{menu_id}', [RecruitmentController::class, 'delete']);

Route::get('/dt/data-recruitment/{holding}', [RecruitmentController::class, 'dt_recruitment']);
Route::get('/dt/data-recruitment_aktif/{holding}', [RecruitmentController::class, 'dt_recruitment_aktif']);
Route::get('/dt/data-recruitment_kadaluarsa/{holding}', [RecruitmentController::class, 'dt_recruitment_kadaluarsa']);
Route::get('/recruitment/update/status-recruitment/{id?}/{holding}/{menu_id}', [RecruitmentController::class, 'update_status']);

Route::get('/pg/data-list-user_meta/{id?}/{holding}', [RecruitmentController::class, 'user_meta']);
Route::get('/pg/data-list-user_kandidat/{id?}/{holding}', [RecruitmentController::class, 'user_kandidat']);
Route::get('/pg/data-list-user_wait/{id?}/{holding}', [RecruitmentController::class, 'user_wait']);
Route::get('/pg/data-list-user_reject/{id?}/{holding}', [RecruitmentController::class, 'user_reject']);


Route::get('/report/rekrutmen/pelamar-detail/{id?}/{holding}', [RecruitmentController::class, 'pelamar_detail']);

Route::get('/report/rekrutmen/pelamar-detail_pdf/{id?}/{holding}', [RecruitmentController::class, 'pelamar_detail_pdf']);
Route::get('/pg/pelamar-nilai_pdf/{id?}', [RecruitmentController::class, 'pelamar_nilai_pdf']);

Route::post('/report/rekrutmen/pelamar-detail-ubah/{holding}', [RecruitmentController::class, 'pelamar_detail_ubah']);
Route::get('/report/rekrutmen/rekrutmen/{holding}', [RecruitmentLaporanController::class, 'laporan_recruitment']);




Route::get('/pg-data-interview/{holding}', [RecruitmentController::class, 'pg_data_interview']);
Route::get('/dt/data-interview/{holding}', [RecruitmentController::class, 'dt_data_interview']);
Route::get('/dt/data-interview1/{holding}', [RecruitmentController::class, 'dt_data_interview1']);
Route::get('/dt/data-interview2/{holding}', [RecruitmentController::class, 'dt_data_interview2']);
Route::get('/dt/data-interview3/{holding}', [RecruitmentController::class, 'dt_data_interview3']);
Route::get('/pg-data-interview/dt/data-data_ujian_user/{id}/{holding}', [RecruitmentController::class, 'data_ujian_user']);

Route::get('/dt/data-get_data_esai/{id}/{holding}', [UjianUserController::class, 'dt_ujian_esai']);

Route::get('/dt/data-dt_interview_user/{id}/{holding}', [UjianUserController::class, 'dt_interview_user']);

Route::post('/dt/interview_user_post', [UjianUserController::class, 'interview_user_post']);
Route::get('/dt/get_catatan_interview/{id}', [UjianUserController::class, 'get_catatan_interview']);
Route::post('/dt/update_catatan', [UjianUserController::class, 'update_catatan']);
Route::get('/dt/dt_catatan/{id}', [UjianUserController::class, 'dt_catatan']);

Route::get('/dt/data-get_data_pg/{id}/{holding}', [UjianUserController::class, 'dt_ujian_pg']);

Route::post('/dt/data-interview/penilaian_esai', [UjianUserController::class, 'penilaian_esai'])->name('penilaian_esai');

Route::get('/pg-data-interview/data-get_esai_interview/{id}/{id_user}/{holding}', [UjianUserController::class, 'show_esai']);
Route::get('/pg-data-interview/data-get_esai_interview_pdf/{id}/{id_user}/{holding}', [UjianUserController::class, 'show_esai_pdf']);

Route::get('/dt/data-get_interview_user/{id}/{id_user}/sp', [UjianUserController::class, 'show_interview_user']);
Route::get('/dt/data-get_interview_user/{id}/{id_user}/sps', [UjianUserController::class, 'show_interview_user']);
Route::get('/dt/data-get_interview_user/{id}/{id_user}/sip', [UjianUserController::class, 'show_interview_user']);

Route::get('/pg-data-interview/dt/data-data_ujian_user/data-get_pg_interview/{id}/{id_user}/{holding}', [UjianUserController::class, 'show_pg']);
Route::get('/pg-data-interview/dt/data-data_ujian_user/data-get_pg_interview_pdf/{id}/{id_user}/{holding}', [UjianUserController::class, 'show_pg_pdf']);

Route::post('/dt/data-interview/presensi_recruitment_update', [RecruitmentController::class, 'presensi_recruitment_update']);
Route::post('/dt/data-interview/ranking_update_status', [RecruitmentController::class, 'ranking_update_status']);
Route::post('/dt/data-interview/integrasi', [RecruitmentController::class, 'user_integrasi']);


Route::get('/pg-data-ujian/{holding}', [RecruitmentController::class, 'pg_ujian']);

Route::post('/pg-data-pembobotan_post', [RecruitmentController::class, 'pembobotan_post']);
Route::get('/pg-data-dt_pembobotan', [RecruitmentController::class, 'dt_pembobotan'])->name('dt_pembobotan');

Route::get('/dt-data-list-ujian/{holding}', [RecruitmentController::class, 'dt_ujian']);

Route::get('/dt-data-list-esai/{holding}', [RecruitmentController::class, 'dt_esai']);

Route::get('/dt-data-list-ujian_kategori/{holding}', [RecruitmentController::class, 'dt_ujian_kategori']);

Route::get('/dt-data-list-interview_admin/{holding}', [RecruitmentController::class, 'dt_interview_admin']);

Route::get('/dt_referensi', [RecruitmentController::class, 'dt_referensi']);
Route::post('/referensi_add', [RecruitmentController::class, 'referensi_add']);
Route::post('/delete_referensi', [RecruitmentController::class, 'delete_referensi']);
Route::post('/referensi_update', [RecruitmentController::class, 'referensi_update']);



Route::post('ujian_kategori_post', [RecruitmentController::class, 'ujian_kategori_post']);
Route::post('delete_ujian_kategori', [RecruitmentController::class, 'delete_ujian_kategori']);
Route::post('ujian_kategori_update', [RecruitmentController::class, 'ujian_kategori_update']);

Route::post('interview_admin_post', [RecruitmentController::class, 'interview_admin_post']);
Route::post('interview_admin_update', [RecruitmentController::class, 'interview_admin_update']);
Route::post('interview_admin_delete', [RecruitmentController::class, 'interview_admin_delete']);

Route::get('/pg-data-ujian/edit-ujian/{kode?}/{holding}', [RecruitmentController::class, 'edit_ujian']);

Route::get('/show-esai/{kode?}/{holding}', [RecruitmentController::class, 'show_esai']);

Route::get('/pg-data-ujian/edit-esai/{kode?}/{holding}', [RecruitmentController::class, 'edit_esai']);

Route::get('/pg-data-ujian/ujian_pg/{holding}', [RecruitmentController::class, 'pg_ujian_pg']);

Route::get('/pg-data-ujian/ujian_pg_esai/{holding}', [RecruitmentController::class, 'pg_esai_pg']);

Route::post('/ujian/ujian-pg-store', [RecruitmentController::class, 'ujian_pg_store']);
Route::post('/ujian/ujian-pg-update', [RecruitmentController::class, 'ujian_pg_update']);
Route::post('/ujian/esai-pg-store', [RecruitmentController::class, 'esai_pg_store']);
Route::post('/ujian/esai-pg-update', [RecruitmentController::class, 'esai_pg_update']);

// SUMMERNOTE
Route::post('/summernote/upload', [SummernoteController::class, 'upload'])->name('summernote_upload');
Route::post('/summernote/delete', [SummernoteController::class, 'delete'])->name('summernote_delete');
Route::get('/summernote/unduh/{file}', [SummernoteController::class, 'unduh']);
Route::post('/summernote/delete_file', [SummernoteController::class, 'delete_file']);

Route::post('/nilai-interview-hrd/update/sp', [RecruitmentController::class, 'nilai_interview_hrd']);
Route::post('/nilai-interview-hrd/update/sps', [RecruitmentController::class, 'nilai_interview_hrd']);
Route::post('/nilai-interview-hrd/update/sip', [RecruitmentController::class, 'nilai_interview_hrd']);

Route::post('/nilai-interview-manager/update/sp', [RecruitmentController::class, 'nilai_interview_manager']);
Route::post('/nilai-interview-manager/update/sps', [RecruitmentController::class, 'nilai_interview_manager']);
Route::post('/nilai-interview-manager/update/sip', [RecruitmentController::class, 'nilai_interview_manager']);

Route::get('/pg-data-ranking/{holding}', [RecruitmentController::class, 'pg_ranking']);

Route::get('/dt/data-ranking/{holding}', [RecruitmentController::class, 'dt_data_ranking']);


Route::get('/dt/data-list-ranking/{id?}/{holding}', [RecruitmentController::class, 'dt_list_ranking']);

Route::get('/dt/data-list-progres/{id?}/{holding}', [RecruitmentController::class, 'dt_list_progres']);

Route::get('/konfirmasi-interview/{email?}/tidak-konfirmasi', [RecruitmentUserController::class, 'tidak_konfirmasi']);
Route::get('/konfirmasi-interview/{email?}/konfirmasi', [RecruitmentUserController::class, 'konfirmasi']);


//Laporan pelamar
Route::get('/report/rekrutmen/pelamar/{holding}', [RecruitmentLaporanController::class, 'report_pelamar']);
Route::get('/laporan_recruitment/{holding}', [RecruitmentLaporanController::class, 'laporan_recruitment']);
Route::get('/report/rekrutmen/pelamar-detail_riwayat/{id}/{holding}', [RecruitmentLaporanController::class, 'detail_riwayat']);
Route::get('/dt_laporan_recruitment/{holding}', [RecruitmentLaporanController::class, 'dt_laporan_recruitment']);
Route::get('/dt_laporan_recruitment2/{holding}', [RecruitmentLaporanController::class, 'dt_laporan_recruitment2']);
Route::get('/dt_laporan_recruitment_print/{holding}', [RecruitmentLaporanController::class, 'dt_laporan_recruitment_print']);
Route::get('/dt_riwayat_recruitment/{id}/{holding}', [RecruitmentLaporanController::class, 'dt_riwayat_recruitment']);
Route::get('/report_recruitment/get_divisi/{holding}', [RecruitmentLaporanController::class, 'get_divisi']);
Route::get('/report_recruitment/get_bagian/{holding}', [RecruitmentLaporanController::class, 'get_bagian']);
Route::get('/report_recruitment/get_jabatan/{holding}', [RecruitmentLaporanController::class, 'get_jabatan']);
Route::get('/report/rekrutmen/perdivisi/{holding}', [RecruitmentLaporanController::class, 'report_per_divisi']);


//laporan pelamar end

Route::get('/report/rekrutmen/perdivisi-detail/{id}/{holding}', [RecruitmentLaporanController::class, 'detail_per_divisi']);
Route::get('/dt_per_divisi/{holding}', [RecruitmentLaporanController::class, 'dt_per_divisi']);
Route::get('/dt_per_divisi_print/{holding}', [RecruitmentLaporanController::class, 'dt_per_divisi_print']);;


// HISTORY
Route::get('/history', [HistoryUserController::class, 'index'])->name('history');

// ===============================
// MASTER PENERBIT
// ===============================
Route::prefix('dokumen_legal/penerbit')
    ->name('dokumen_legal.penerbit.')
    ->group(function () {
        Route::get('{holding}', [PenerbitDokumenController::class, 'index'])->name('index');
        Route::get('{holding}/create', [PenerbitDokumenController::class, 'create'])->name('create');
        Route::post('{holding}', [PenerbitDokumenController::class, 'store'])->name('store');
        Route::get('{holding}/{id_penerbit}', [PenerbitDokumenController::class, 'show'])->name('show');
        Route::get('{holding}/{id_penerbit}/edit', [PenerbitDokumenController::class, 'edit'])->name('edit');
        Route::put('{holding}/{id_penerbit}', [PenerbitDokumenController::class, 'update'])->name('update');
        Route::delete('{holding}/{id_penerbit}', [PenerbitDokumenController::class, 'destroy'])->name('destroy');
    });

Route::get(
    '/dokumen-legal/ajax/penerbit/{kategoriId}',
    [DokumenLegalController::class, 'ajaxPenerbit']
)->name('dokumen_legal.ajax.penerbit');

//KATEGORI DOKUMEN LEGAL
Route::prefix('dokumen_legal/kategori/kategori_master')
    ->name('dokumen_legal.kategori.kategori_master.')
    ->group(function () {

        Route::get('{holding}', [KategoriDokumenController::class, 'index'])->name('index');
        Route::get('{holding}/create', [KategoriDokumenController::class, 'create'])->name('create');
        Route::post('{holding}', [KategoriDokumenController::class, 'store'])->name('store');
        Route::get('{holding}/{id_kategori}', [KategoriDokumenController::class, 'show'])->name('show');
        Route::get('{holding}/{id_kategori}/edit', [KategoriDokumenController::class, 'edit'])->name('edit');
        Route::put('{holding}/{id_kategori}', [KategoriDokumenController::class, 'update'])->name('update');
        Route::delete('{holding}/{id_kategori}', [KategoriDokumenController::class, 'destroy'])->name('destroy');
        Route::get('{holding}/{id_kategori}/sub-kategori/create', [SubKategoriDokumenController::class, 'createFromKategori'])->name('sub_kategori.create_from_kategori');
        Route::get('{holding}/{id_kategori}/penerbit/create', [PenerbitDokumenController::class, 'createFromKategori'])->name('penerbit.create_from_kategori');
    });

Route::get(
    '/dokumen-legal/ajax/sub-kategori/{kategori}',
    function ($kategori) {
        return \App\Models\SubKategoriDokumen::where('kategori_id', $kategori)
            ->orderBy('sub_name')
            ->get(['id_sub', 'sub_name']);
    }
)->name('dokumen_legal.ajax.sub_kategori');

//SUB KATEGORI DOKUMEN LEGAL
Route::prefix('dokumen_legal/kategori/kategori_sub')
    ->name('dokumen_legal.kategori.kategori_sub.')
    ->group(function () {
        Route::get('{holding}', [SubKategoriDokumenController::class, 'index'])->name('index');
        Route::get('{holding}/create', [SubKategoriDokumenController::class, 'create'])->name('create');
        Route::post('{holding}', [SubKategoriDokumenController::class, 'store'])->name('store');
        Route::get('{holding}/{id_sub_kategori}', [SubKategoriDokumenController::class, 'show'])->name('show');
        Route::get('{holding}/{id_sub_kategori}/edit', [SubKategoriDokumenController::class, 'edit'])->name('edit');
        Route::put('{holding}/{id_sub_kategori}', [SubKategoriDokumenController::class, 'update'])->name('update');
        Route::delete('{holding}/{id_sub_kategori}', [SubKategoriDokumenController::class, 'destroy'])->name('destroy');
    });

// LEGAL DOCUMENT
Route::prefix('dokumen_legal')
    ->name('dokumen_legal.')
    ->group(function () {
        Route::get('index/{holding}', [DokumenLegalController::class, 'index'])->name('index');
        Route::get('{holding}/create', [DokumenLegalController::class, 'create'])->name('create');
        Route::post('{holding}', [DokumenLegalController::class, 'store'])->name('store');
        Route::get('{holding}/{id}/edit', [DokumenLegalController::class, 'edit'])->name('edit');
        Route::put('{holding}/{id}', [DokumenLegalController::class, 'update'])->name('update');
        Route::delete('{holding}/{id}', [DokumenLegalController::class, 'destroy'])->name('destroy');
        Route::get('{holding}/{id}', [DokumenLegalController::class, 'show'])->name('show');
    });

// ==========================================
// VENDOR / FIS ADMIN ROUTES
// ==========================================
Route::prefix('master_ticket')->name('ticket.vendor.')->group(function () {
    // Cascading & Employee API endpoints for Node Approval Builder
    Route::get('vendor-workflow/cascade/divisi', [TicketVendorAdminController::class, 'getDivisiByDept'])->name('workflow.cascade.divisi');
    Route::get('vendor-workflow/cascade/bagian', [TicketVendorAdminController::class, 'getBagianByDivisi'])->name('workflow.cascade.bagian');
    Route::get('vendor-workflow/cascade/level-jabatan', [TicketVendorAdminController::class, 'getLevelJabatan'])->name('workflow.cascade.level_jabatan');
    Route::get('vendor-workflow/cascade/karyawan', [TicketVendorAdminController::class, 'getKaryawanByFilter'])->name('workflow.cascade.karyawan');
    Route::get('vendor-workflow/search-karyawan', [TicketVendorAdminController::class, 'searchKaryawan'])->name('workflow.search_karyawan');

    // Workflow Tree Data (OrgChart JSON) & Node Step CRUD
    Route::get('vendor-workflow/tree/{workflowId}', [TicketVendorAdminController::class, 'workflowTreeData'])->name('workflow.tree');
    Route::post('vendor-workflow/step/store', [TicketVendorAdminController::class, 'workflowStepStore'])->name('workflow.step.store');
    Route::post('vendor-workflow/step/update/{id}', [TicketVendorAdminController::class, 'workflowStepUpdate'])->name('workflow.step.update');
    Route::post('vendor-workflow/step/change-parent/{id}', [TicketVendorAdminController::class, 'workflowStepChangeParent'])->name('workflow.step.change_parent');
    Route::delete('vendor-workflow/step/delete/{id}', [TicketVendorAdminController::class, 'workflowStepDelete'])->name('workflow.step.delete');
    Route::get('vendor-workflow/step/karyawan/{stepId}', [TicketVendorAdminController::class, 'getStepKaryawan'])->name('workflow.step.karyawan');

    // Master Workflow Management
    Route::get('vendor-workflow/datatable/{holding?}', [TicketVendorAdminController::class, 'workflowDatatable'])->name('workflow.datatable');
    Route::post('vendor-workflow/store', [TicketVendorAdminController::class, 'workflowStore'])->name('workflow.store');
    Route::post('vendor-workflow/update/{id}', [TicketVendorAdminController::class, 'workflowUpdate'])->name('workflow.update');
    Route::delete('vendor-workflow/delete/{id}', [TicketVendorAdminController::class, 'workflowDestroy'])->name('workflow.destroy');
    Route::get('vendor-workflow/{holding?}', [TicketVendorAdminController::class, 'workflowIndex'])->name('workflow.index');

    // Vendor Monitoring & ERP ID Pemasok Mapping
    Route::get('vendor-monitoring/datatable/{holding?}', [TicketVendorAdminController::class, 'vendorMonitoringDatatable'])->name('monitoring.datatable');
    Route::post('vendor/update-id-pemasok/{vendorId}', [TicketVendorAdminController::class, 'updateIdPemasok'])->name('update_id_pemasok');
    Route::get('vendor-monitoring/{holding?}', [TicketVendorAdminController::class, 'vendorMonitoringIndex'])->name('monitoring.index');

    // ============================================================================
    // USER-FACING VENDOR REGISTRATION ROUTES (FIS - Formulir Identitas Supplier)
    // ============================================================================
    Route::get('vendor/{menu_id}', [TicketVendorController::class, 'index'])->name('vendor.index');
    Route::get('vendor/create/{menu_id}', [TicketVendorController::class, 'create'])->name('vendor.create');
    Route::post('vendor/store', [TicketVendorController::class, 'store'])->name('vendor.store');
    Route::get('vendor/preview-approval/{menu_id}', [TicketVendorController::class, 'previewApproval'])->name('vendor.previewApproval');
    Route::get('vendor/show/{menu_id}/{id}', [TicketVendorController::class, 'show'])->name('vendor.show');
    Route::get('vendor/search-existing', [TicketVendorController::class, 'searchExistingVendors'])->name('vendor.search_existing');
    Route::get('vendor/preview-flow/{menu_id}', [TicketVendorController::class, 'previewApproval'])->name('vendor.preview_flow');

    // ============================================================================
    // VENDOR APPROVAL ROUTES (Approver-facing)
    // ============================================================================
    Route::prefix('approval/vendor')->name('approval.ticket.vendor.')->group(function () {
        Route::get('{menu_id}', [TicketVendorApprovalController::class, 'index'])->name('index');
        Route::get('list/{menu_id}', [TicketVendorApprovalController::class, 'list'])->name('list');
        Route::get('show/{menu_id}/{vendorId}', [TicketVendorApprovalController::class, 'show'])->name('show');
        Route::post('save/{vendorId}', [TicketVendorApprovalController::class, 'save'])->name('save');
    });
});

Route::prefix('master_ticket')->name('ticket.')->group(function () {
    // menu
    Route::get('datatable/kondisi', [TicketWebController::class, 'datatableKondisiMenu'])->name('datatable.kondisi');
    Route::get('datatable', [TicketWebController::class, 'datatable'])->name('datatable');
    Route::post('store', [TicketWebController::class, 'store'])->name('store');
    Route::put('update', [TicketWebController::class, 'update'])->name('update');
    Route::delete('delete/{id}', [TicketWebController::class, 'destroy'])->name('destroy');
    // submenu
    Route::get('submenu/datatable', [TicketWebController::class, 'datatableSubMenu'])->name('submenu.datatable');
    Route::post('submenu/store', [TicketWebController::class, 'storeSubMenu'])->name('submenu.store');
    Route::get('/submenu/menu_kategori', [TicketWebController::class, 'menu_kategori'])->name('submenu.menu_kategori');
    Route::put('submenu/update', [TicketWebController::class, 'updateSubMenu'])->name('submenu.update');
    Route::delete('submenu/delete/{id}', [TicketWebController::class, 'destroySubMenu'])->name('submenu.destroy');
    // menu hub
    Route::get('hub-menu/datatable', [TicketWebController::class, 'hubMenuDatatable'])->name('hub_menu.datatable');
    Route::post('hub-menu/store', [TicketWebController::class, 'hubMenuStore'])->name('hub_menu.store');
    Route::post('hub-menu/update/{id}', [TicketWebController::class, 'hubMenuUpdate'])->name('hub_menu.update');
    Route::delete('hub-menu/delete/{id}', [TicketWebController::class, 'hubMenuDestroy'])->name('hub_menu.destroy');
    Route::get('hub-menu/users/{id}', [TicketWebController::class, 'hubMenuGetUsers'])->name('hub_menu.get_users');
    Route::get('hub-menu/users-datatable/{holding}/{id}', [TicketWebController::class, 'hubMenuUserDatatable'])->name('hub_menu.users_datatable');
    Route::post('hub-menu/users/save/{id}', [TicketWebController::class, 'hubMenuSaveUsers'])->name('hub_menu.save_users');
    Route::post('hub-menu/approval-group/datatable', [TicketWebController::class, 'getApprovalMenu'])->name('hub_menu.approval_group.datatable');
    Route::post('hub-menu/approval-group/save/{id}', [TicketWebController::class, 'saveApprovalGroup'])->name('hub_menu.approval_group.save');
    // kondisi
    Route::post('simpan_kondisi', [TicketWebController::class, 'storeKondisiMenu'])->name('kondisi.store');
    Route::put('update_kondisi', [TicketWebController::class, 'updateKondisiMenu'])->name('kondisi.update');
    Route::delete('delete_kondisi/{id}', [TicketWebController::class, 'destroyKondisiMenu'])->name('kondisi.destroy');
    Route::get('kondisi/datatable', [TicketWebController::class, 'datatableKondisi'])->name('kondisi.datatable');
    Route::get('kondisi/list', [TicketWebController::class, 'kondisiList'])->name('kondisi.list');
    Route::get('departemen/list', [TicketWebController::class, 'departemenList'])->name('departemen.list');
    // canvas
    Route::get('canvas/data', [TicketWebController::class, 'canvasData']);
    Route::post('canvas/save-position', [TicketWebController::class, 'canvasSavePosition']);
    Route::post('canvas/edge/toggle/{id}', [TicketWebController::class, 'canvasEdgeToggle']);
    Route::get('canvas/org-tree/{subMenuId}', [TicketWebController::class, 'canvasOrgTree']);
    Route::post('canvas/submenu/quick-store', [TicketWebController::class, 'canvasQuickStoreSubMenu']);
    Route::post('canvas/node/unplace/{subMenuId}', [TicketWebController::class, 'canvasUnplaceNode']);
    Route::get('canvas/flow-list', [TicketWebController::class, 'canvasFlowList']);
    Route::post('canvas/submenu/set-flow', [TicketWebController::class, 'canvasSetSubMenuFlow']);
    // approval flow
    Route::get('approval-flow/datatable', [TicketWebController::class, 'datatableFlowApproval'])->name('approval_flow.datatable');
    Route::post('approval-flow/store', [TicketWebController::class, 'storeFlowApproval'])->name('approval_flow.store');
    Route::get('approval-flow/list_menu', [TicketWebController::class, 'listMenuApproval'])->name('approval_flow.list_menu');
    Route::get('approval-flow/list', [TicketWebController::class, 'approvalFlowList'])->name('approval_flow.list');

    Route::get('approval-flow/submenu_list', [TicketWebController::class, 'approvalGetSubMenu'])->name('approval_flow.submenu_list');
    Route::post('approval-flow/save-user', [TicketWebController::class, 'save_user_approval'])->name('approval_flow.save_user');
    Route::put('approval-flow/update/{id}', [TicketWebController::class, 'updateApprovalFlow'])->name('approval_flow.group.update');
    Route::delete('approval-flow/delete/{id}', [TicketWebController::class, 'deleteApprovalGroup'])->name('approval_flow.group.delete');
    // approval step
    Route::post('approval-flow/step/store', [TicketWebController::class, 'storeApprovalStep'])->name('approval_flow.step.store');
    Route::post('approval-flow/step/update/{id}', [TicketWebController::class, 'updateApprovalStep'])->name('approval_flow.step.update');
    Route::post('approval-flow/step/change-parent/{id}', [TicketWebController::class, 'changeStepParent'])->name('approval_flow.step.change_parent');
    Route::delete('approval-flow/step/delete/{id}', [TicketWebController::class, 'deleteApprovalStep'])->name('approval_flow.step.delete');
    // karyawan
    Route::get('approval-flow/get-holding', [TicketWebController::class, 'get_holding'])->name('approval_flow.get_holding');
    Route::get('approval-flow/get-departemen', [TicketWebController::class, 'get_departemen'])->name('approval_flow.get_department');
    Route::get('approval-flow/get-divisi', [TicketWebController::class, 'get_divisi'])->name('approval_flow.get_divisi');
    Route::get('approval-flow/get-bagian', [TicketWebController::class, 'get_bagian'])->name('approval_flow.get_bagian');
    Route::get('approval-flow/get-jabatan', [TicketWebController::class, 'get_jabatan'])->name('approval_flow.get_jabatan');
    Route::get('approval-flow/get-jabatan-level', [TicketWebController::class, 'getJabatanLevel']);
    Route::get('approval-flow/karyawan/{step_id}', [TicketWebController::class, 'get_karyawan'])->name('approval_flow.get_karyawan');
    // dependency
    Route::get('dependency/datatable', [TicketWebController::class, 'dependencyDatatable'])->name('dependency.datatable');
    Route::get('dependency/submenu_list', [TicketWebController::class, 'dependency_menu_kategori'])->name('dependency.menu_kategori');
    Route::get('dependency/kondisi_list', [TicketWebController::class, 'dependencyKondisiList'])->name('dependency.kondisi_list');
    Route::post('dependency/store', [TicketWebController::class, 'dependencyStore'])->name('dependency.store');
    Route::match(['put', 'post'], 'dependency/update/{id}', [TicketWebController::class, 'dependencyUpdate'])->name('dependency.update');
    Route::delete('dependency/delete/{id}', [TicketWebController::class, 'dependencyDestroy'])->name('dependency.destroy');
    // validator
    Route::get('validator/datatable', [TicketWebController::class, 'validatorDatatable'])->name('validator.datatable');
    Route::get('validator/get-users', [TicketWebController::class, 'validatorGetUsers'])->name('validator.get_users');
    Route::post('validator/store', [TicketWebController::class, 'validatorStore'])->name('validator.store');
    Route::post('validator/update/{id}', [TicketWebController::class, 'validatorUpdate'])->name('validator.update');
    Route::post('validator/delete/{id}', [TicketWebController::class, 'validatorDestroy'])->name('validator.destroy');
    // Assignment
    Route::get('assignment/datatable', [TicketWebController::class, 'assignmentDatatable'])->name('assignment.datatable');
    Route::post('assignment/store',    [TicketWebController::class, 'assignmentStore'])->name('assignment.store');
    Route::post('assignment/update/{id}', [TicketWebController::class, 'assignmentUpdate'])->name('assignment.update');
    Route::post('assignment/delete/{id}', [TicketWebController::class, 'assignmentDestroy'])->name('assignment.destroy');
    // Difficulty / Tingkat Kesulitan Master
    Route::get('difficulty/datatable', [TicketWebController::class, 'difficultyDatatable'])->name('difficulty.datatable');
    Route::post('difficulty/store',    [TicketWebController::class, 'difficultyStore'])->name('difficulty.store');
    Route::post('difficulty/update/{id}', [TicketWebController::class, 'difficultyUpdate'])->name('difficulty.update');
    Route::post('difficulty/delete/{id}', [TicketWebController::class, 'difficultyDestroy'])->name('difficulty.destroy');

    // Report Ticket
    Route::get('report/departemen/list', [TicketWebController::class, 'reportDepartemenList'])->name('report.departemen.list');
    Route::get('report/data/list', [TicketWebController::class, 'getListTicketReport'])->name('report.data.list');
    Route::get('report/detail/{id}', [TicketWebController::class, 'getTicketDetail'])->name('report.detail');
    Route::post('report/cancel-generated-draft/{id}', [TicketWebController::class, 'cancelGeneratedDraft'])->name('report.cancel_generated_draft');
    Route::get('report/{holding}', [TicketWebController::class, 'report'])->name('report');

    // index
    Route::get('submenu/{holding}', [TicketWebController::class, 'subMenu'])->name('submenu');
    Route::get('{holding}', [TicketWebController::class, 'index'])->name('index');
});
Route::get('/report_ticket/{holding}', [TicketWebController::class, 'report'])->name('report_ticket');
Route::prefix('master_approval_flow')->name('approval_flow.')->group(function () {
    Route::get('approval-flow/{holding}', [TicketWebController::class, 'approvalFlow'])->name('approval_flow');
});

Route::prefix('knowledge_base')->name('knowledge_base.')->group(function () {
    Route::get('kategori', [KnowledgeBaseController::class, 'kb_kategori_data'])->name('kategori');
    Route::get('kategori/tree', [KnowledgeBaseController::class, 'kb_kategori_tree'])->name('kategori_tree');
    Route::get('kategori/parent', [KnowledgeBaseController::class, 'kb_kategori_data_parent'])->name('kategori_parent');
    Route::post('kategori/store', [KnowledgeBaseController::class, 'kb_kategori_store'])->name('kategori.store');
    Route::post('kategori/update/{id}', [KnowledgeBaseController::class, 'kb_kategori_update'])->name('kategori.update');
    Route::post('kategori/access/{id}', [KnowledgeBaseController::class, 'kb_kategori_access_update'])->name('kategori.access'); // NEW
    Route::delete('kategori/destroy/{id}', [KnowledgeBaseController::class, 'kb_kategori_destroy'])->name('kategori.destroy');
    Route::get('kategori/{id}', [KnowledgeBaseController::class, 'kb_kategori_show'])->name('kategori.show');

    Route::get('artikel/kategori/{id}', [KnowledgeBaseController::class, 'kba_artikel_by_kategori'])->name('artikel.by_kategori');
    Route::get('{holding}/artikel/create/{kategori_id}/{kb_slug}', [KnowledgeBaseController::class, 'kba_artikel_create'])->name('artikel.create');
    Route::post('artikel/store', [KnowledgeBaseController::class, 'kba_artikel_store'])->name('artikel.store');
    Route::get('{holding}/artikel/edit/{kategori_id}/{id}', [KnowledgeBaseController::class, 'kba_artikel_edit'])->name('artikel.edit');
    Route::post('artikel/update/{id}', [KnowledgeBaseController::class, 'kba_artikel_update'])->name('artikel.update');
    Route::post('artikel/access/{id}', [KnowledgeBaseController::class, 'kb_artikel_access_update'])->name('artikel.access');
    Route::delete('artikel/destroy/{id}', [KnowledgeBaseController::class, 'kba_artikel_destroy'])->name('artikel.destroy');
    Route::get('artikel/{id}', [KnowledgeBaseController::class, 'kb_artikel_show'])->name('artikel.show');

    Route::get('{holding}/departemen', [KnowledgeBaseController::class, 'kb_departemen_list'])->name('departemen'); // moved, now holding-scoped

    Route::get('{holding}', [KnowledgeBaseController::class, 'kb_index'])->name('index');
});

Route::get('/dokumen_legal/ajax/penerbit_by_kategori/{kategori}', [DokumenLegalController::class, 'ajaxPenerbitByKategori'])->name('dokumen_legal.ajax.penerbit_by_kategori');

Route::get('/dokumen_legal/ajax/kategori_by_penerbit/{penerbit}', [DokumenLegalController::class, 'ajaxKategoriByPenerbit'])->name('dokumen_legal.ajax.kategori_by_penerbit');

Route::patch('/dokumen-legal/{holding}/{id}/tidak-diperpanjang', [DokumenLegalController::class, 'setTidakDiperpanjang'])->name('dokumen_legal.tidak_diperpanjang');

Route::get('/dokumen-legal/ajax/reference', [DokumenLegalController::class, 'ajaxReference']);

//PROJECT MANAGEMENT
Route::prefix('project_management')
    ->name('project_management.')
    ->middleware('auth')
    ->group(function () {
        Route::get('index/{holding}', [ProjectManagementController::class, 'index'])->name('index');
        Route::get('workload-dashboard/{holding?}', [ProjectManagementController::class, 'workloadDashboard'])->name('workload_dashboard');
        Route::get('report/{holding?}', [ProjectManagementController::class, 'report'])->name('report');
        Route::get('{holding}', [ProjectManagementController::class, 'index'])->name('dashboard');
        Route::get('{holding}/create', [ProjectManagementController::class, 'create'])->name('create');
        Route::post('{holding}', [ProjectManagementController::class, 'store'])->name('store');
        Route::post('{holding}/{project}/milestones', [ProjectManagementController::class, 'storeMilestone'])->name('milestones.store');
        Route::put('{holding}/{project}/milestones/{milestone}', [ProjectManagementController::class, 'updateMilestone'])->name('milestones.update');
        Route::delete('{holding}/{project}/milestones/{milestone}', [ProjectManagementController::class, 'destroyMilestone'])->name('milestones.destroy');
        Route::post('{holding}/{project}/members', [ProjectManagementController::class, 'storeMember'])->name('members.store');
        Route::put('{holding}/{project}/members/{member}', [ProjectManagementController::class, 'updateMember'])->name('members.update');
        Route::delete('{holding}/{project}/members/{member}', [ProjectManagementController::class, 'destroyMember'])->name('members.destroy');
        Route::get('{holding}/{project}/meetings', [ProjectManagementMeetingController::class, 'index'])->name('meetings.index');
        Route::get('{holding}/{project}/meetings/create', [ProjectManagementMeetingController::class, 'create'])->name('meetings.create');
        Route::post('{holding}/{project}/meetings', [ProjectManagementMeetingController::class, 'store'])->name('meetings.store');
        Route::get('{holding}/{project}/meetings/{meeting}', [ProjectManagementMeetingController::class, 'show'])->name('meetings.show');
        Route::get('{holding}/{project}/meetings/{meeting}/pdf', [ProjectManagementMeetingController::class, 'pdf'])->name('meetings.pdf');
        Route::get('{holding}/{project}/meetings/{meeting}/edit', [ProjectManagementMeetingController::class, 'edit'])->name('meetings.edit');
        Route::put('{holding}/{project}/meetings/{meeting}', [ProjectManagementMeetingController::class, 'update'])->name('meetings.update');
        Route::delete('{holding}/{project}/meetings/{meeting}', [ProjectManagementMeetingController::class, 'destroy'])->name('meetings.destroy');
        Route::post('{holding}/{project}/meetings/{meeting}/tasks', [ProjectManagementMeetingController::class, 'storeMeetingTask'])->name('meetings.tasks.store');
        Route::delete('{holding}/{project}/meetings/{meeting}/tasks/{meetingTask}', [ProjectManagementMeetingController::class, 'destroyMeetingTask'])->name('meetings.tasks.destroy');
        Route::patch('{holding}/{project}/meetings/{meeting}/finalize', [ProjectManagementMeetingController::class, 'finalize'])->name('meetings.finalize');
        Route::patch('{holding}/{project}/meetings/{meeting}/cancel', [ProjectManagementMeetingController::class, 'cancel'])->name('meetings.cancel');
        Route::post('{holding}/{project}/meetings/{meeting}/action-items', [ProjectManagementMeetingController::class, 'storeActionItem'])->name('meetings.action_items.store');
        Route::patch('{holding}/{project}/meetings/{meeting}/action-items/{actionItem}', [ProjectManagementMeetingController::class, 'updateActionItem'])->name('meetings.action_items.update');
        Route::post('{holding}/{project}/meetings/{meeting}/action-items/{actionItem}/generate-task', [ProjectManagementMeetingController::class, 'generateTask'])->name('meetings.action_items.generate_task');
        Route::get('{holding}/{project}/meetings/{meeting}/attachments/{attachment}', [ProjectManagementMeetingController::class, 'attachment'])->name('meetings.attachments.show');
        Route::get('{holding}/{project}/tasks/create', [ProjectManagementController::class, 'createTask'])->name('tasks.create');
        Route::post('{holding}/{project}/tasks', [ProjectManagementController::class, 'storeTask'])->name('tasks.store');
        Route::get('{holding}/{project}/tasks/{task}', [ProjectManagementController::class, 'showTask'])->name('tasks.show');
        Route::post('{holding}/{project}/tasks/{task}/comments', [ProjectManagementController::class, 'storeTaskComment'])->name('tasks.comments.store');
        Route::delete('{holding}/{project}/tasks/{task}/comments/{comment}', [ProjectManagementController::class, 'destroyTaskComment'])->name('tasks.comments.destroy');
        Route::get('{holding}/{project}/tasks/{task}/attachments/{attachment}', [ProjectManagementController::class, 'showTaskAttachment'])->name('tasks.attachments.show');
        Route::get('{holding}/{project}/tasks/{task}/edit', [ProjectManagementController::class, 'editTask'])->name('tasks.edit');
        Route::patch('{holding}/{project}/tasks/{task}/reopen', [ProjectManagementController::class, 'reopenTask'])->name('tasks.reopen');
        Route::put('{holding}/{project}/tasks/{task}', [ProjectManagementController::class, 'updateTask'])->name('tasks.update');
        Route::delete('{holding}/{project}/tasks/{task}', [ProjectManagementController::class, 'destroyTask'])->name('tasks.destroy');
        Route::get('my-tasks/{holding}', [ProjectManagementController::class, 'myTasks'])->name('my_tasks.index');
        Route::get('my-tasks/{holding}/{task}/edit', [ProjectManagementController::class, 'editMyTask'])->name('my_tasks.edit');
        Route::put('my-tasks/{holding}/{task}', [ProjectManagementController::class, 'updateMyTask'])->name('my_tasks.update');
        Route::get('review-tasks/{holding}', [ProjectManagementController::class, 'reviewTasks'])->name('review_tasks.index');
        Route::get('review-tasks/{holding}/{task}/edit', [ProjectManagementController::class, 'editReviewTask'])->name('review_tasks.edit');
        Route::put('review-tasks/{holding}/{task}', [ProjectManagementController::class, 'updateReviewTask'])->name('review_tasks.update');
        Route::get('blocked-tasks/{holding}', [ProjectManagementController::class, 'blockedTasks'])->name('blocked_tasks.index');
        Route::get('{holding}/{id}/edit', [ProjectManagementController::class, 'edit'])->name('edit');
        Route::put('{holding}/{id}', [ProjectManagementController::class, 'update'])->name('update');
        Route::delete('{holding}/{id}', [ProjectManagementController::class, 'destroy'])->name('destroy');
        Route::get('{holding}/{id}', [ProjectManagementController::class, 'show'])->name('show');
    });


// Endpoint data JSON untuk dashboard (tidak perlu {holding} di path,
// filter holding dikirim lewat query string ?holding_code=xxx sama seperti
// filter lain di halaman report yang sudah ada)
Route::prefix('ticket/report/kpi')->group(function () {
    Route::get('/summary', [TicketWebController::class, 'kpiSummary'])
        ->name('ticket.report.kpi.summary');

    Route::get('/trend', [TicketWebController::class, 'kpiTrend'])
        ->name('ticket.report.kpi.trend');

    Route::get('/top-divisi', [TicketWebController::class, 'kpiTopDivisi'])
        ->name('ticket.report.kpi.top-divisi');

    Route::get('/top-module', [TicketWebController::class, 'kpiTopModule'])
        ->name('ticket.report.kpi.top-module');

    Route::get('/assignee-performance', [TicketWebController::class, 'kpiAssigneePerformance'])
        ->name('ticket.report.kpi.assignee');

    Route::get('/avg-time-detail', [TicketWebController::class, 'kpiAvgTimeDetail'])
        ->name('ticket.report.kpi.avg-time-detail');

    Route::get('/pic-sla-detail', [TicketWebController::class, 'kpiPicSlaDetail'])
        ->name('ticket.report.kpi.pic-sla-detail');

    Route::get('/status-distribution', [TicketWebController::class, 'kpiStatusDistribution'])
        ->name('ticket.report.kpi.status-distribution');
});

Route::get('/ticket/report/kpi/{holding}/', [TicketWebController::class, 'kpiDashboard'])
    ->name('ticket.report.kpi');
Route::get('/test-500', function () {
    abort(500, 'Terjadi kesalahan pada server');
});

//Optimize Route
Route::get('optimize', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');


    Artisan::call('view:cache');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('optimize');

    echo 'optimize clear';
});
/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/notifications/{id}/read', function ($id) {
        $notif = Auth::user()
            ->Notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notif->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    })->middleware('auth');
});






require __DIR__ . '/auth.php';
require __DIR__ . '/Users.php';
