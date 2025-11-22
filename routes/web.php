<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\authController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\HoldingController;
use App\Http\Controllers\jabatanController;
use App\Http\Controllers\karyawanController;
use App\Http\Controllers\KaryawanKesehatanController;
use App\Http\Controllers\KaryawanRiwayatController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MappingShiftController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\UserKaryawanController;
use App\Models\Holding;
use Illuminate\Support\Facades\Route;

Route::get('/', [authController::class, 'index']);
Route::middleware('auth')->group(function () {
    // DASHBOARD
    Route::get('/dashboard/hrd/{holding}', [dashboardController::class, 'index']);
    Route::get('/dashboard/portal/{holding}', [dashboardController::class, 'index_portal']);
    Route::get('/dashboard/option/{holding}', [dashboardController::class, 'dashboard_option']);
    Route::get('/dashboard/holding', [dashboardController::class, 'holding'])->name('dashboard_holding');
    Route::get('/get_grafik_absensi_karyawan/{holding}', [dashboardController::class, 'get_grafik_absensi_karyawan']);
    Route::get('/graph_Dashboard_All/{holding}', [dashboardController::class, 'graph_Dashboard_All']);

    // MASTER DEPARTEMEN
    Route::get('/departemen/{holding}', [DepartemenController::class, 'index']);
    Route::get('/departemen-datatable/{holding}', [DepartemenController::class, 'datatable']);
    Route::post('/departemen/insert/{holding}', [DepartemenController::class, 'insert']);
    Route::get('/departemen/edit/{id}/{holding}', [DepartemenController::class, 'edit']);
    Route::post('/departemen/update/{holding}', [DepartemenController::class, 'update']);
    Route::get('/departemen/delete/{id}/{holding}', [DepartemenController::class, 'delete']);
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
    Route::get('/divisi/delete/{id}/{holding}', [DivisiController::class, 'delete']);
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
    Route::get('/bagian/delete/{id}/{holding}', [BagianController::class, 'delete']);
    Route::get('/bagian/get_divisi/{id}/{holding}', [BagianController::class, 'get_divisi']);
    Route::get('/bagian/get_bagian/{id}', [BagianController::class, 'get_bagian']);
    Route::post('/bagian/ImportBagian/{holding}', [BagianController::class, 'ImportBagian']);
    Route::get('/jabatan/jabatan-datatable/{id?}/{holding}', [BagianController::class, 'jabatan_datatable']);
    Route::get('/jabatan/karyawanjabatan-datatable/{id?}/{holding}', [BagianController::class, 'karyawanjabatan_datatable']);
    Route::get('/jabatan/get_jabatan/{id}', [jabatanController::class, 'get_jabatan']);


    // MASTER JABATAN
    Route::get('/jabatan/{holding}', [jabatanController::class, 'index']);
    Route::get('/detail_jabatan/{id?}/{holding}', [jabatanController::class, 'detail_jabatan']);
    Route::get('/jabatan-datatable/{id?}/{holding}', [jabatanController::class, 'datatable']);
    Route::get('/bawahanjabatan-datatable/{id?}/{holding}', [jabatanController::class, 'bawahan_datatable']);
    Route::get('/karyawanjabatan-datatable/{id?}/{holding}', [jabatanController::class, 'karyawan_datatable']);
    Route::get('/jabatan/create/{holding}', [jabatanController::class, 'create']);
    Route::post('/jabatan/insert/{holding}', [jabatanController::class, 'insert']);
    Route::get('/jabatan/edit/{id}/{holding}', [jabatanController::class, 'edit']);
    Route::post('/jabatan/update/{holding}', [jabatanController::class, 'update']);
    Route::get('/jabatan/delete/{id}/{holding}', [jabatanController::class, 'delete']);
    Route::get('/jabatan/get_bagian/{id}/{holding}', [jabatanController::class, 'get_bagian']);
    Route::post('/jabatan/ImportJabatan/{holding}', [jabatanController::class, 'ImportJabatan']);

    // HOLDING
    Route::get('/holding/{holding}', [HoldingController::class, 'index']);
    Route::get('/holding-datatable/{holding}', [HoldingController::class, 'datatable']);

    // SITES
    Route::get('/site/{holding}', [SitesController::class, 'index']);
    Route::get('/site-datatable/{holding}', [SitesController::class, 'datatable']);
    Route::get('/site/tambah_site/{holding}', [SitesController::class, 'tambah_site']);
    Route::post('/site/addSite/{holding}', [SitesController::class, 'addSite']);

    // LOKASI
    Route::get('/lokasi/{holding}', [LokasiController::class, 'index']);
    Route::get('/lokasi/tambah_lokasi/{holding}', [LokasiController::class, 'tambah_lokasi']);
    Route::get('/lokasi-datatable/{holding}', [LokasiController::class, 'datatable']);
    Route::post('/lokasi/add/{holding}', [LokasiController::class, 'addLokasi']);
    Route::post('/lokasi/edit/{holding}', [LokasiController::class, 'updateLokasi']);
    Route::get('/lokasi/delete/{id}/{holding}', [LokasiController::class, 'deleteLokasi']);
    Route::put('/lokasi/radius/{id}/{holding}', [LokasiController::class, 'updateRadiusLokasi']);
    Route::get('/lokasi/get_lokasi/{holding}', [LokasiController::class, 'get_lokasi']);

    // MASTER KARYAWAN
    Route::put('/karyawan/proses-edit-shift/sp', [karyawanController::class, 'prosesEditShift']);
    Route::put('/karyawan/proses-edit-shift/sps', [karyawanController::class, 'prosesEditShift']);
    Route::put('/karyawan/proses-edit-shift/sip', [karyawanController::class, 'prosesEditShift']);
    Route::get('/karyawan/{holding}', [karyawanController::class, 'index']);
    Route::get('/karyawan_bulanan-datatable/{holding}', [karyawanController::class, 'datatable_bulanan']);
    Route::get('/karyawan_harian-datatable/{holding}', [karyawanController::class, 'datatable_harian']);
    Route::get('/karyawan/{holding}', [karyawanController::class, 'index']);
    Route::get('/karyawan_harian-datatable/{holding}', [karyawanController::class, 'datatable_harian']);
    Route::get('/karyawan/tambah-karyawan/{holding}', [karyawanController::class, 'tambahKaryawan']);
    Route::post('/karyawan/tambah-karyawan-proses', [karyawanController::class, 'tambahKaryawanProses']);
    Route::get('/karyawan/detail/{id}/{holding}', [karyawanController::class, 'detail']);
    Route::post('/karyawan/proses-edit/{id}/sp', [karyawanController::class, 'editKaryawanProses']);
    Route::post('/karyawan/proses-edit/{id}/sps', [karyawanController::class, 'editKaryawanProses']);
    Route::post('/karyawan/proses-edit/{id}/sip', [karyawanController::class, 'editKaryawanProses']);
    Route::get('/karyawan/delete/{id}/sp', [karyawanController::class, 'deleteKaryawan']);
    Route::get('/karyawan/delete/{id}/sps', [karyawanController::class, 'deleteKaryawan']);
    Route::get('/karyawan/delete/{id}/sip', [karyawanController::class, 'deleteKaryawan']);
    Route::post('/karyawan/ImportKaryawan/sp', [karyawanController::class, 'ImportKaryawan']);
    Route::post('/karyawan/ImportKaryawan/sps', [karyawanController::class, 'ImportKaryawan']);
    Route::post('/karyawan/ImportKaryawan/sip', [karyawanController::class, 'ImportKaryawan']);
    Route::post('/karyawan/ImportUpdateKaryawan/sp', [karyawanController::class, 'ImportUpdateKaryawan']);
    Route::post('/karyawan/ImportUpdateKaryawan/sps', [karyawanController::class, 'ImportUpdateKaryawan']);
    Route::post('/karyawan/ImportUpdateKaryawan/sip', [karyawanController::class, 'ImportUpdateKaryawan']);
    Route::get('/karyawan/ExportKaryawan/{holding}', [karyawanController::class, 'ExportKaryawan']);
    Route::get('/karyawan/pdfKaryawan/{holding}', [karyawanController::class, 'download_pdf_karyawan']);

    // PENDIDIKAN KARYAWAN
    Route::get('/karyawan/pendidikan/{id}', [karyawanController::class, 'pendidikan_datatable']);
    Route::post('/karyawan/AddPendidikan/', [karyawanController::class, 'add_pendidikan']);
    Route::post('/karyawan/UpdatePendidikan/', [karyawanController::class, 'update_pendidikan']);
    Route::post('/karyawan/DeletePendidikan/', [karyawanController::class, 'delete_pendidikan']);

    // Riwayat Pekerjaan
    Route::get('/karyawan/riwayat/{id}', [KaryawanRiwayatController::class, 'riwayat_datatable']);
    Route::post('/karyawan/riwayat_update', [karyawanRiwayatController::class, 'riwayat_update']);
    Route::post('/karyawan/riwayat_post', [karyawanRiwayatController::class, 'riwayat_post']);
    Route::post('/karyawan/delete_riwayat/{id}', [karyawanRiwayatController::class, 'delete_riwayat']);
    Route::get('/karyawan/button_riwayat/{id?}', [KaryawanRiwayatController::class, 'button_riwayat'])->name('button_riwayat');

    // kesehatan
    Route::get('/cpanel/cv/kesehatan/kesehatan_get/{id}', [KaryawanKesehatanController::class, 'kesehatan_get'])->name('kesehatan_get');
    Route::post('/cpanel/cv/kesehatan/kesehatan_post', [KaryawanKesehatanController::class, 'kesehatan_post'])->name('kesehatan_post');

    // KEAHLIAN KARYAWAN
    Route::get('/karyawan/keahlian/{id}', [karyawanController::class, 'keahlian_datatable']);
    Route::post('/karyawan/AddKeahlian/', [karyawanController::class, 'add_keahlian']);
    Route::post('/karyawan/UpdateKeahlian/', [karyawanController::class, 'update_keahlian']);
    Route::post('/karyawan/DeleteKeahlian/', [karyawanController::class, 'delete_keahlian']);

    Route::get('/karyawan_non_aktif/sp', [karyawanController::class, 'karyawan_non_aktif']);
    Route::get('/karyawan_non_aktif/sps', [karyawanController::class, 'karyawan_non_aktif']);
    Route::get('/karyawan_non_aktif/sip', [karyawanController::class, 'karyawan_non_aktif']);
    Route::get('/database_karyawan_non_aktif/sp', [karyawanController::class, 'database_karyawan_non_aktif']);
    Route::get('/database_karyawan_non_aktif/sps', [karyawanController::class, 'database_karyawan_non_aktif']);
    Route::get('/database_karyawan_non_aktif/sip', [karyawanController::class, 'database_karyawan_non_aktif']);
    Route::post('/karyawan/non_aktif_proses', [karyawanController::class, 'non_aktif_proses']);

    Route::get('/karyawan_ingin_bergabung/sp', [karyawanController::class, 'karyawan_ingin_bergabung']);
    Route::get('/karyawan_ingin_bergabung/sps', [karyawanController::class, 'karyawan_ingin_bergabung']);
    Route::get('/karyawan_ingin_bergabung/sip', [karyawanController::class, 'karyawan_ingin_bergabung']);

    Route::get('/karyawan/karyawan_masa_tenggang_kontrak/sp', [karyawanController::class, 'karyawan_masa_tenggang_kontrak']);
    Route::get('/karyawan/karyawan_masa_tenggang_kontrak/sps', [karyawanController::class, 'karyawan_masa_tenggang_kontrak']);
    Route::get('/karyawan/karyawan_masa_tenggang_kontrak/sip', [karyawanController::class, 'karyawan_masa_tenggang_kontrak']);
    Route::get('/karyawan/database_karyawan_masa_tenggang_kontrak/sp', [karyawanController::class, 'database_karyawan_masa_tenggang_kontrak']);
    Route::get('/karyawan/database_karyawan_masa_tenggang_kontrak/sps', [karyawanController::class, 'database_karyawan_masa_tenggang_kontrak']);
    Route::get('/karyawan/database_karyawan_masa_tenggang_kontrak/sip', [karyawanController::class, 'database_karyawan_masa_tenggang_kontrak']);
    Route::post('/karyawan/update_kontrak_proses', [karyawanController::class, 'update_kontrak_proses']);

    Route::get('/users/{holding}', [UserKaryawanController::class, 'index_users']);
    Route::get('/users_finger/{holding}', [UserKaryawanController::class, 'index_finger']);
    Route::post('/users/prosesTambahUser/sp', [UserKaryawanController::class, 'prosesTambahUser']);
    Route::post('/users/prosesTambahUser/sps', [UserKaryawanController::class, 'prosesTambahUser']);
    Route::post('/users/prosesTambahUser/sip', [UserKaryawanController::class, 'prosesTambahUser']);
    Route::get('/users_bulanan-datatable/{holding}', [UserKaryawanController::class, 'datatable_users_bulanan']);
    Route::get('/users_harian-datatable/{holding}', [UserKaryawanController::class, 'datatable_users_harian']);
    Route::get('/users_finger-datatable/{holding}', [UserKaryawanController::class, 'users_finger_datatable']);
    Route::get('/users/edit-password/{id}/sp', [UserKaryawanController::class, 'editPassword']);
    Route::get('/users/edit-password/{id}/sps', [UserKaryawanController::class, 'editPassword']);
    Route::get('/users/edit-password/{id}/sip', [UserKaryawanController::class, 'editPassword']);
    Route::post('/users/edit-password-proses/{id}/sp', [UserKaryawanController::class, 'editPasswordProses']);
    Route::post('/users/edit-password-proses/{id}/sps', [UserKaryawanController::class, 'editPasswordProses']);
    Route::post('/users/edit-password-proses/{id}/sip', [UserKaryawanController::class, 'editPasswordProses']);
    Route::post('/users/non_aktif_proses', [UserKaryawanController::class, 'non_aktif_proses']);
    Route::post('/users/aktif_proses', [UserKaryawanController::class, 'aktif_proses']);
    Route::post('/users/ImportUser/sp', [UserKaryawanController::class, 'ImportUser']);
    Route::post('/users/ImportUser/sps', [UserKaryawanController::class, 'ImportUser']);
    Route::post('/users/ImportUser/sip', [UserKaryawanController::class, 'ImportUser']);
    Route::post('/users/ImportUpdateUser/sp', [UserKaryawanController::class, 'ImportUpdateUser']);
    Route::post('/users/ImportUpdateUser/sps', [UserKaryawanController::class, 'ImportUpdateUser']);
    Route::post('/users/ImportUpdateUser/sip', [UserKaryawanController::class, 'ImportUpdateUser']);
    Route::get('/users/ExportUser/sp', [UserKaryawanController::class, 'ExportUser']);
    Route::get('/users/ExportUser/sps', [UserKaryawanController::class, 'ExportUser']);
    Route::get('/users/ExportUser/sip', [UserKaryawanController::class, 'ExportUser']);
    Route::get('/users/pdfUserKaryawan/sps', [UserKaryawanController::class, 'download_pdf_user_karyawan']);
    Route::get('/users/pdfUserKaryawan/sp', [UserKaryawanController::class, 'download_pdf_user_karyawan']);
    Route::get('/users/pdfUserKaryawan/sip', [UserKaryawanController::class, 'download_pdf_user_karyawan']);

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

    // Mapping User
    Route::get('mapping_shift/dashboard/', [MappingShiftController::class, 'index']);
    Route::get('mapping_shift/tambah_mapping/', [MappingShiftController::class, 'tambah_mapping']);
    Route::get('mapping_shift/get_shiftData/', [MappingShiftController::class, 'getShiftData']);
    Route::get('mapping_shift/getKaryawanMappingShift', [MappingShiftController::class, 'getKaryawanMappingShift']);
    Route::post('/mapping_shift/addMappingShift', [MappingShiftController::class, 'addMappingShift']);
    Route::post('/mapping_shift/update_mapping_shift', [MappingShiftController::class, 'update_mapping_shift']);
    Route::post('/mapping_shift/delete_mapping_shift', [MappingShiftController::class, 'delete_mapping_shift']);

    Route::post('/mapping_shift/prosesAddMappingShift/{holding}', [MappingShiftController::class, 'prosesAddMappingShift']);
    Route::post('/karyawan/mapping_shift/prosesEditMappingShift/{holding}', [MappingShiftController::class, 'prosesEditMappingShift']);

    //APPROVAL
    Route::get('/approval/mapping_shift', [ApprovalController::class, 'mapping_shift']);
    // STRUKTUR ORGANISASI
    Route::get('/struktur_organisasi/{holding}', [StrukturOrganisasiController::class, 'index']);
});

require __DIR__ . '/auth.php';
