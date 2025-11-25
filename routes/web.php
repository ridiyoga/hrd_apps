<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\authController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\FingerController;
use App\Http\Controllers\HistoryUserController;
use App\Http\Controllers\HoldingController;
use App\Http\Controllers\HomeUserController;
use App\Http\Controllers\jabatanController;
use App\Http\Controllers\karyawanController;
use App\Http\Controllers\KaryawanKesehatanController;
use App\Http\Controllers\KaryawanRiwayatController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MappingShiftController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\RecruitmentUserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\SummernoteController;
use App\Http\Controllers\UjianUserController;
use App\Http\Controllers\UserKaryawanController;
use App\Models\Holding;
use Illuminate\Support\Facades\Route;

Route::get('/', [authController::class, 'index']);
Route::get('/testConnection', [ReportController::class, 'testConnection']);
Route::get('/getLogsOnly', [ReportController::class, 'getLogsOnly']);
Route::middleware('auth')->group(function () {
    // TEST

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

    // SHIFT
    Route::get('/shift/{holding}', [ShiftController::class, 'index']);
    Route::get('/shift-datatable/{holding}', [ShiftController::class, 'datatable']);
    Route::get('/shift/create/{holding}', [ShiftController::class, 'create']);
    Route::post('/shift/store/{holding}', [ShiftController::class, 'store']);
    Route::post('/shift/update/{holding}', [ShiftController::class, 'update']);
    Route::get('/shift/delete/{id}/{holding}', [ShiftController::class, 'destroy']);
    Route::get('/karyawan/shift/{id}/{holding}', [karyawanController::class, 'shift']);


    // FINGER MACHINE
    Route::get('/finger/{holding}', [FingerController::class, 'index']);
    Route::get('/finger-datatable/{holding}', [FingerController::class, 'datatable']);
    Route::post('/finger/store/{holding}', [FingerController::class, 'store']);
    Route::post('/finger/update/{holding}', [FingerController::class, 'update']);
    Route::get('/finger/delete/{id}/{holding}', [FingerController::class, 'destroy']);

    // MASTER MENU
    Route::get('/menu/{holding}', [MenuController::class, 'index']);
    Route::post('/menu/save_all_change', [MenuController::class, 'save_all_change']);
    Route::post('/menu/store/{holding}', [MenuController::class, 'store']);
    Route::get('/menu/delete/{id}', [MenuController::class, 'delete']);

    // ROLE
    Route::get('/role/{holding}', [RoleController::class, 'index']);
    Route::get('/role-datatable/{holding}', [RoleController::class, 'datatable']);
    Route::get('/menu-datatable/{holding}', [RoleController::class, 'datatable_menu']);
    Route::get('/role/add_role/{id}/{holding}', [RoleController::class, 'add_role']);
    Route::post('/role/role_save_add/{holding}', [RoleController::class, 'role_save_add']);
    Route::post('/role/role_save_update/{holding}', [RoleController::class, 'role_save_update']);

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
    Route::get('/home', [HomeUserController::class, 'index'])->name('home');
    Route::get('/pusher', [HomeUserController::class, 'pusher'])->name('pusher');
    Route::post('/home/absenMasuk', [HomeUserController::class, 'absenMasuk'])->name('absenMasuk');
    Route::get('/datatableHome', [HomeUserController::class, 'datatableHome'])->name('datatableHome');
    Route::get('/get_count_absensi_home', [HomeUserController::class, 'get_count_absensi_home'])->name('get_count_absensi_home');
    Route::get('/home/absen', [HomeUserController::class, 'HomeAbsen'])->name('absen');
    Route::post('/home/absenPulang', [HomeUserController::class, 'absenPulang'])->name('absenPulang');
    Route::get('/home/maps/{lat}/{long}', [HomeUserController::class, 'maps']);
    Route::get('/home/my-absen', [HomeUserController::class, 'myAbsen']);
    Route::get('/home/my-location', [HomeUserController::class, 'myLocation']);
    Route::get('/home/form_datang_terlambat', [HomeUserController::class, 'form_datang_terlambat']);
    Route::post('/home/pulang_cepat_proses', [HomeUserController::class, 'proses_izin_pulang_cepats']);
    Route::get('/home/get_notif', [HomeUserController::class, 'get_notif']);
    Route::get('/home/get_notif_cuti', [HomeUserController::class, 'get_notif_cuti']);
    Route::get('/home/get_notif_penugasan', [HomeUserController::class, 'get_notif_penugasan']);
    Route::get('/home/create_face_id', [HomeUserController::class, 'create_face_id'])->name('create_face_id');
    Route::post('/home/savefaceid', [HomeUserController::class, 'savefaceid'])->name('savefaceid');

    // ACCESS
    Route::get('/access/{holding}', [AccessController::class, 'index']);
    Route::get('/access-datatable/{holding}', [AccessController::class, 'datatable']);
    Route::get('/access/role_access_datatable/{id}/{holding}', [AccessController::class, 'role_access_datatable']);
    Route::get('/access/add_access/{id}/{holding}', [AccessController::class, 'add_access']);
    Route::post('/access/access_save_add/{holding}', [AccessController::class, 'access_save_add']);


    // MENU RECRUITMENT

    // RECRUITMENT DASHBOARD ADMIN
    Route::get('/pg-data-recruitment/{holding}', [RecruitmentController::class, 'pg_recruitment']);

    Route::post('/recruitment/create/{holding}', [RecruitmentController::class, 'create']);

    Route::post('/recruitment/update', [RecruitmentController::class, 'update']);

    Route::get('/recruitment/delete/{id?}/{holding}', [RecruitmentController::class, 'delete']);

    Route::get('/dt/data-recruitment/{holding}', [RecruitmentController::class, 'dt_recruitment']);
    Route::get('/recruitment/update/status-recruitment/{id?}/{holding}', [RecruitmentController::class, 'update_status']);

    Route::get('/pg/data-list-pelamar/{id?}/{holding}', [RecruitmentController::class, 'pg_list_pelamar']);
    Route::get('/pg/data-list-user_meta/{id?}/{holding}', [RecruitmentController::class, 'user_meta']);
    Route::get('/pg/data-list-user_kandidat/{id?}/{holding}', [RecruitmentController::class, 'user_kandidat']);
    Route::get('/pg/data-list-user_wait/{id?}/{holding}', [RecruitmentController::class, 'user_wait']);
    Route::get('/pg/data-list-user_reject/{id?}/{holding}', [RecruitmentController::class, 'user_reject']);


    Route::get('/pg/pelamar-detail/{id?}/{holding}', [RecruitmentController::class, 'pelamar_detail']);

    Route::get('/pg/pelamar-detail_pdf/{id?}/{holding}', [RecruitmentController::class, 'pelamar_detail_pdf']);
    Route::get('/pg/pelamar-nilai_pdf/{id?}', [RecruitmentController::class, 'pelamar_nilai_pdf']);

    Route::post('/pg/pelamar-detail-ubah/{holding}', [RecruitmentController::class, 'pelamar_detail_ubah']);


    Route::get('/pg-data-interview/{holding}', [RecruitmentController::class, 'pg_data_interview']);
    Route::get('/dt/data-interview/{holding}', [RecruitmentController::class, 'dt_data_interview']);
    Route::get('/dt/data-interview1/{holding}', [RecruitmentController::class, 'dt_data_interview1']);
    Route::get('/dt/data-interview2/{holding}', [RecruitmentController::class, 'dt_data_interview2']);
    Route::get('/dt/data-interview3/{holding}', [RecruitmentController::class, 'dt_data_interview3']);
    Route::get('/dt/data-data_ujian_user/{id}/{holding}', [RecruitmentController::class, 'data_ujian_user']);

    Route::get('/dt/data-get_data_esai/{id}/{holding}', [UjianUserController::class, 'dt_ujian_esai']);

    Route::get('/dt/data-dt_interview_user/{id}/{holding}', [UjianUserController::class, 'dt_interview_user']);

    Route::post('/dt/interview_user_post', [UjianUserController::class, 'interview_user_post']);
    Route::get('/dt/get_catatan_interview/{id}', [UjianUserController::class, 'get_catatan_interview']);
    Route::post('/dt/update_catatan', [UjianUserController::class, 'update_catatan']);
    Route::get('/dt/dt_catatan/{id}', [UjianUserController::class, 'dt_catatan']);

    Route::get('/dt/data-get_data_pg/{id}/{holding}', [UjianUserController::class, 'dt_ujian_pg']);

    Route::post('/dt/data-interview/penilaian_esai', [UjianUserController::class, 'penilaian_esai'])->name('penilaian_esai');

    Route::get('/dt/data-get_esai_interview/{id}/{id_user}/{holding}', [UjianUserController::class, 'show_esai']);

    Route::get('/dt/data-get_interview_user/{id}/{id_user}/sp', [UjianUserController::class, 'show_interview_user']);
    Route::get('/dt/data-get_interview_user/{id}/{id_user}/sps', [UjianUserController::class, 'show_interview_user']);
    Route::get('/dt/data-get_interview_user/{id}/{id_user}/sip', [UjianUserController::class, 'show_interview_user']);

    Route::get('/dt/data-get_pg_interview/{id}/{id_user}/{holding}', [UjianUserController::class, 'show_pg']);

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

    Route::get('/edit-ujian/{kode?}/{holding}', [RecruitmentController::class, 'edit_ujian']);

    Route::get('/show-esai/{kode?}/{holding}', [RecruitmentController::class, 'show_esai']);

    Route::get('/edit-esai/{kode?}/{holding}', [RecruitmentController::class, 'edit_esai']);

    Route::get('/pg-data-ujian/ujian_pg/{holding}', [RecruitmentController::class, 'pg_ujian_pg']);

    Route::get('/pg-data-ujian/ujian_pg_esai/{holding}', [RecruitmentController::class, 'pg_esai_pg']);

    Route::post('/ujian/ujian-pg-store', [RecruitmentController::class, 'ujian_pg_store']);
    Route::post('/ujian/ujian-pg-update', [RecruitmentController::class, 'ujian_pg_update']);
    Route::post('/ujian/esai-pg-store', [RecruitmentController::class, 'esai_pg_store']);
    Route::post('/ujian/esai-pg-update', [RecruitmentController::class, 'esai_pg_update']);
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

    Route::get('/pg/data-list-ranking/{id?}/{holding}', [RecruitmentController::class, 'pg_list_ranking']);

    Route::get('/dt/data-list-ranking/{id?}/{holding}', [RecruitmentController::class, 'dt_list_ranking']);

    Route::get('/dt/data-list-progres/{id?}/{holding}', [RecruitmentController::class, 'dt_list_progres']);

    Route::get('/konfirmasi-interview/{email?}/tidak-konfirmasi', [RecruitmentUserController::class, 'tidak_konfirmasi']);
    Route::get('/konfirmasi-interview/{email?}/konfirmasi', [RecruitmentUserController::class, 'konfirmasi']);
});
Route::get('/profile', [ProfileUserController::class, 'index'])->name('profile');

Route::get('/detail_profile', [ProfileUserController::class, 'detail_profile']);
Route::post('/save_detail_profile', [ProfileUserController::class, 'save_detail_profile']);

Route::get('/detail_alamat', [ProfileUserController::class, 'detail_alamat']);
Route::post('/save_detail_alamat', [ProfileUserController::class, 'save_detail_alamat']);

Route::get('/detail_account', [ProfileUserController::class, 'detail_account']);
Route::post('/save_detail_account', [ProfileUserController::class, 'save_detail_account']);

Route::get('/change_photoprofile_camera', [ProfileUserController::class, 'change_photoprofile_camera']);
Route::post('/save_capture_profile', [ProfileUserController::class, 'save_capture_profile']);

Route::get('/profile/lihat_jabatan', [ProfileUserController::class, 'lihat_jabatan']);
Route::get('/profile/lihat_kontrak_kerja', [ProfileUserController::class, 'lihat_kontrak_kerja']);
Route::get('/profile/lihat_dokumen', [ProfileUserController::class, 'lihat_dokumen']);
Route::get('/profile/lihat_struktur_organisasi', [ProfileUserController::class, 'lihat_struktur_organisasi']);
Route::get('/profile/lihat_rekan_kerja', [ProfileUserController::class, 'lihat_rekan_kerja']);
// HISTORY
Route::get('/history', [HistoryUserController::class, 'index'])->name('history');


require __DIR__ . '/auth.php';
