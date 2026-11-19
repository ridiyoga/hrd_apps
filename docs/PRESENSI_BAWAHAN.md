//CTRL+SHIFT+V
# Modul Presensi Bawahan

Dokumen ini menjelaskan cara kerja modul Presensi Bawahan dari sisi developer dan cara penggunaannya oleh Admin serta User mobile.

## 1. Tujuan Modul

Modul ini menyediakan:

- Pengaturan relasi atasan dan bawahan oleh Admin per holding.
- Daftar karyawan yang dapat dicari dan dipilih menggunakan checkbox.
- Tampilan bawahan pada aplikasi mobile berdasarkan holding yang dipilih User.
- Laporan detail presensi bawahan.
- Download laporan presensi bawahan dalam format Excel.

Satu karyawan dapat memiliki relasi bawahan yang berbeda pada holding yang berbeda.

Contoh:

- Dicky pada Holding A memiliki bawahan A1 dan A2.
- Dicky pada Holding B memiliki bawahan B1 dan B2.
- Saat Dicky memilih Holding A, hanya A1 dan A2 yang ditampilkan.

## 2. Konsep Data

### 2.1 Tabel utama

Tabel `presensi_bawahan_relations` menyimpan relasi manual:

| Kolom | Keterangan |
| --- | --- |
| `id` | UUID relasi |
| `holding_id` | Holding tempat relasi berlaku |
| `atasan_id` | ID karyawan atasan |
| `bawahan_id` | ID karyawan bawahan |
| `created_by` | ID user yang menyimpan konfigurasi |
| `created_at` | Waktu dibuat |
| `updated_at` | Waktu diperbarui |

Relasi unik berdasarkan kombinasi:

```text
holding_id + atasan_id + bawahan_id
```

Artinya, relasi tidak boleh ganda pada holding yang sama, tetapi karyawan yang sama boleh mempunyai relasi berbeda pada holding lain.

### 2.2 Sumber data karyawan

Data karyawan berasal dari `karyawan_jabatan` dan `karyawans`.

- Daftar karyawan pada halaman Admin ditampilkan lintas holding.
- Assignment yang aktif dan `main = 1` digunakan sebagai assignment utama.
- Karyawan harus berstatus aktif.
- Validasi penyimpanan tetap memastikan atasan dan bawahan adalah karyawan valid.

### 2.3 Sumber struktur organisasi

`struktur_organisasi` digunakan sebagai acuan kandidat bawahan dan fallback ketika relasi manual belum tersedia.

Prioritas data untuk User:

1. Relasi manual pada `presensi_bawahan_relations` untuk holding yang dipilih.
2. Struktur organisasi pada holding yang dipilih jika relasi manual belum disimpan.

Setelah Admin menyimpan konfigurasi untuk seorang atasan pada holding tertentu, konfigurasi manual menjadi sumber utama untuk atasan tersebut.

## 3. Alur Developer

### 3.1 Alur Admin

Admin membuka menu dengan URL:

```text
/reportpresensibawahan/{holding_code}
```

`ReportPresensiBawahanController@index` menerima `holding_code`, mencari data holding, lalu menampilkan halaman Admin.

Daftar karyawan dimuat melalui:

```text
GET /reportpresensibawahan/employees/{holding_code}
```

Daftar ini tidak dibatasi oleh holding agar Admin dapat mencari semua karyawan. Holding tetap dikontrol oleh URL halaman Admin.

Saat Admin memilih karyawan sebagai atasan, scope dimuat melalui:

```text
GET /reportpresensibawahan/scope/{holding_code}/{karyawan_id}
```

Saat tombol Simpan ditekan:

```text
POST /reportpresensibawahan/relations/{holding_code}
```

Payload utama:

```text
atasan_id
bawahan_ids[]
```

Proses penyimpanan:

1. Holding dicari berdasarkan `holding_code` pada URL.
2. Atasan dan bawahan divalidasi sebagai karyawan aktif.
3. Seluruh relasi lama untuk kombinasi holding dan atasan dihapus.
4. Relasi baru dari checkbox dimasukkan dalam transaction.
5. Checkbox kosong berarti semua bawahan atasan tersebut dihapus dari holding itu.

### 3.2 Alur laporan Admin

Data presensi dimuat melalui:

```text
GET /reportpresensibawahan/attendance/{holding_code}/{karyawan_id}
```

Laporan menggunakan:

- Log presensi dari `AttendanceLog`.
- Jadwal dari `MappingShift`.
- Shift `NON SHIFT`.
- Kalender kerja/libur.
- Data cuti, sakit, dan tukar libur.

Status yang dihasilkan antara lain:

- `TEPAT WAKTU`
- `TELAT`
- `DATANG LEBIH AWAL`
- `TIDAK HADIR`
- `CUTI`
- `SAKIT`
- `TUKAR LIBUR`
- `LIBUR`
- `BELUM DIJADWALKAN`

### 3.3 Alur User mobile

Menu mobile:

```text
/laporan_presensi_bawahan/dashboard/{menu_id}
```

Saat halaman dibuka:

1. Sistem mencari seluruh holding aktif milik User dari `karyawan_jabatan`.
2. User harus memilih holding.
3. Sistem mengirim `holding_id` ke endpoint daftar bawahan.
4. Sistem hanya membaca relasi atau struktur organisasi pada holding tersebut.

Endpoint daftar bawahan:

```text
GET /laporan_presensi_bawahan/get_dataAbsensiBawahan/{menu_id}/{holding_id}
```

Validasi User:

- Holding harus terdaftar pada assignment aktif User.
- User harus mempunyai data karyawan.
- Bawahan tidak boleh berasal dari holding lain.

Endpoint lama tetap tersedia untuk kompatibilitas:

```text
/absensi_bawahan/...
```

### 3.4 Alur download Excel

Endpoint User:

```text
GET /laporan_presensi_bawahan/export/{holding_id}
```

Parameter query:

```text
start_date=YYYY-MM-DD
end_date=YYYY-MM-DD
```

Atasan diambil dari akun yang sedang login, bukan dari parameter browser. Ini mencegah User meminta laporan milik karyawan lain.

Isi Excel:

- Tanggal
- Hari
- ID Karyawan
- Nama Karyawan
- Jabatan
- Departemen
- Divisi
- Bagian
- Shift
- Masuk
- Pulang
- Status

Rentang tanggal maksimal 31 hari.

File XLSX dibuat memakai OpenSpout yang sudah tersedia pada dependency project. Modul ini tidak menggunakan `Maatwebsite\\Excel`.

## 4. Cara Penggunaan Admin

1. Login sebagai Admin atau Superadmin.
2. Masuk ke holding yang ingin dikonfigurasi.
3. Buka menu **Downline Presensi Bawahan**.
4. Cari nama, ID, jabatan, atau unit atasan pada daftar karyawan.
5. Pilih karyawan yang akan diatur sebagai atasan.
6. Pada bagian **Susun Bawahan**, cari karyawan menggunakan kolom pencarian.
7. Centang semua karyawan yang boleh dilihat oleh atasan tersebut.
8. Hilangkan centang untuk menghapus akses bawahan.
9. Klik **Simpan**.
10. Ulangi langkah yang sama untuk holding atau atasan lainnya.

Catatan:

- Daftar checkbox menampilkan semua karyawan aktif, bukan hanya karyawan dalam holding aktif.
- Label struktur organisasi digunakan sebagai acuan kandidat, tetapi Admin dapat memilih karyawan sesuai kebutuhan konfigurasi.
- Konfigurasi disimpan untuk holding yang sedang dibuka pada URL Admin.
- Jika checkbox dikosongkan lalu disimpan, relasi bawahan untuk atasan tersebut pada holding itu akan dihapus.

## 5. Cara Penggunaan User Mobile

1. Login ke aplikasi mobile.
2. Buka menu **Laporan Presensi Bawahan**.
3. Pilih holding pada pilihan **Pilih Holding**.
4. Setelah holding dipilih, daftar bawahan akan dimuat.
5. Pilih bawahan untuk melihat detail presensinya.
6. Untuk download, pilih tanggal awal dan tanggal akhir.
7. Klik **Download Excel Presensi Bawahan**.

Jika User mempunyai beberapa holding, daftar bawahan akan berubah sesuai holding yang dipilih.

## 6. Pesan Kondisi yang Perlu Dipahami

### User belum memilih holding

```text
Silakan pilih holding terlebih dahulu.
```

### User tidak terdaftar pada holding

```text
Holding tidak terdaftar untuk user ini.
```

Periksa assignment aktif User pada `karyawan_jabatan`.

### Belum ada bawahan

```text
Belum ada bawahan untuk Anda pada holding ini.
Admin perlu menyusun dan menyimpan relasi bawahan terlebih dahulu.
```

Admin perlu membuka holding yang benar, memilih atasan, mencentang bawahan, lalu menekan Simpan.

### Akun belum terhubung ke karyawan

```text
Akun Anda belum terhubung dengan data karyawan. Hubungi admin.
```

Periksa nilai `users.karyawan_id` dan hubungan ke data `karyawans`.

### Rentang tanggal tidak valid

```text
Rentang tanggal maksimal 31 hari dan tanggal harus valid.
```

## 7. Instalasi dan Deployment

Migration yang berkaitan dengan modul:

```text
database/migrations/2026_08_27_090000_add_downline_presensi_bawahan_menu.php
database/migrations/2026_08_27_100000_create_presensi_bawahan_relations_table.php
database/migrations/2026_08_27_101000_add_laporan_presensi_bawahan_mobile_menu.php
database/migrations/2026_08_27_110000_fix_laporan_presensi_bawahan_mobile_menu.php
```

Jalankan migration:

```powershell
php artisan migrate
```

Jika view masih menampilkan tampilan lama setelah deployment:

```powershell
php artisan view:clear
php artisan view:cache
```

Pastikan dependency berikut tersedia melalui `composer.lock` dan folder `vendor`:

```text
openspout/openspout
```

## 8. Troubleshooting Developer

### Tabel relasi tidak ditemukan

Error seperti berikut berarti migration belum dijalankan:

```text
relation "presensi_bawahan_relations" does not exist
```

Solusi:

```powershell
php artisan migrate
```

### Class Excel tidak ditemukan

Jangan menggunakan:

```php
Maatwebsite\\Excel\\Facades\\Excel
```

Package tersebut tidak digunakan oleh modul ini. Export menggunakan:

```php
OpenSpout\\Writer\\XLSX\\Writer
```

### Icon menu tampil sebagai teks

Kolom `menus.icon` dapat berisi dua format:

```text
mdi mdi-card-account-details
```

atau:

```html
<i class="fa-solid fa-chart-line"></i>
```

Layout mobile menangani kedua format tersebut. Setelah perubahan view, bersihkan cache:

```powershell
php artisan view:clear
php artisan view:cache
```

### Menu mobile tidak muncul

Periksa:

1. `menus.kategori = mobile`.
2. `menus.main` bernilai `0`, `1`, atau `2` sesuai area menu.
3. URL menu adalah `laporan_presensi_bawahan/dashboard`.
4. Ada baris `role_menus` untuk role User.
5. `role_menus.can_view = 1`.
6. User memiliki baris `role_users` yang menunjuk role tersebut.

## 9. File Implementasi

Controller Admin dan laporan:

```text
app/Http/Controllers/ReportPresensiBawahanController.php
```

Controller User mobile:

```text
app/Http/Controllers/AbsensiBawahanController.php
```

View Admin:

```text
resources/views/admin/report/presensi_bawahan/index.blade.php
```

View User mobile:

```text
resources/views/users/absensi_bawahan/index.blade.php
resources/views/users/absensi_bawahan/detail.blade.php
```

Layout menu mobile:

```text
resources/views/users/layouts/menubar.blade.php
resources/views/users/home/index.blade.php
```

Model relasi:

```text
app/Models/PresensiBawahanRelation.php
```
