//CTRL+SHIFT+V
# PRD — Ticketing Support

| Informasi | Keterangan |
| --- | --- |
| Aplikasi / versi dokumen | `hrd_apps_server` / 1.0 |
| Tanggal baseline | 1 Oktober 2026 |
| Status | Dokumentasi implementasi; kriteria penerimaan belum dieksekusi sebagai UAT |
| Dokumen terkait | [BRD](BRD.md), [FRD](FRD.md), [Milestone](MILESTONE.md) |

## 1. Definisi produk

Ticketing Support adalah modul web internal untuk mengajukan perubahan transaksi, menjalankan persetujuan dan pemeriksaan dependensi, menugaskan PIC, menerima hasil pekerjaan, serta memantau laporan dan KPI. Pendaftaran vendor tersedia sebagai jalur terpisah.

Baseline berasal dari source code, bukan demonstrasi aplikasi atau data operasional. Status “tersedia” berarti tersedia pada kode. Kriteria penerimaan adalah hasil yang harus dibuktikan melalui pengujian; temuan pada FRD dapat menyebabkan sebagian kriteria belum terpenuhi.

## 2. Pengguna dan kebutuhan utama

| Pengguna | Kebutuhan |
| --- | --- |
| Pengaju | Mengajukan perubahan lengkap dan mengetahui pihak/tahap penanganannya |
| Approver / checker | Memeriksa bukti sebelum memutuskan atau membuat tiket terkait |
| Validator | Memastikan seluruh prasyarat kelompok sudah siap |
| Assignor | Memilih PIC aktif dan kesulitan pekerjaan |
| PIC | Mengelola tugas, mencatat jeda dan hasil pekerjaan |
| Administrator | Mengonfigurasi layanan dan alur melalui master |
| Pemantau layanan | Memantau status, distribusi pekerjaan, durasi, dan hasil |

## 3. Permukaan produk

| Area | Halaman/fungsi yang ditemukan |
| --- | --- |
| Master admin | Ticket, submenu, kondisi, hub menu, approval flow, dependency, validator, assignment, difficulty |
| Desain alur | Canvas node/edge, posisi, aktivasi edge, pemetaan flow, struktur organisasi |
| Pengajuan | Hub, daftar, tambah, edit draft, detail/rantai, formulir generated draft |
| Persetujuan | Approval, checker, validator individual dan kelompok |
| Pelaksanaan | Assignment, pilihan PIC, daftar/detail pekerjaan PIC |
| Vendor | Daftar, tambah, detail, daftar/detail approval |
| Pelaporan | Laporan pengguna/admin, PDF tiket, KPI admin/PIC |

View referensi: [pengguna](../../resources/views/users/ticket), [admin](../../resources/views/admin/ticket), [komponen rantai](../../resources/views/components/ticket-chain-card.blade.php).

## 4. Kebutuhan produk dan penerimaan

Prioritas **Inti** menunjukkan alur layanan utama; **Pendukung** menunjukkan konfigurasi, pemantauan, atau komunikasi; **Jalur khusus** menunjukkan vendor. Pengelompokan ini bukan urutan pengerjaan historis.

| ID / BRD | Kebutuhan dan prioritas | Kriteria penerimaan | Status baseline |
| --- | --- | --- | --- |
| PR-01 / BR-01 | Konfigurasi — Pendukung | Admin mengelola master, memasangkan flow, menyimpan canvas, menentukan pihak aktif; konfigurasi terbaca kembali | Tersedia; akses endpoint dan konfigurasi perlu UAT |
| PR-02 / BR-02 | Pengajuan perubahan — Inti | Pengaju memilih lingkungan/modul/jenis, mengisi minimal satu transaksi/subdetail dan signature; nomor/status terbentuk; field wajib kosong ditolak | Tersedia; edit draft/lampiran memiliki GAP-04 |
| PR-03 / BR-03 | Approval/checker — Inti | Pihak yang tepat menyetujui/menolak/memeriksa, signature dan komentar tercatat; pihak lain ditolak | Tersedia; urutan dan akses perlu UAT |
| PR-04 / BR-04 | Tiket berantai — Inti | Dependensi menghasilkan draft atas nama checker; referensi asal terjaga; pengiriman ulang tidak menggandakan; traversal tidak berputar | Tersedia; concurrency dan lintas lingkungan perlu UAT |
| PR-05 / BR-05 | Validasi — Inti | Tiket/kelompok dapat divalidasi/ditolak; checker/draft tertunda menghalangi kelanjutan; validasi berhasil menuju penugasan | Tersedia; jalur individual memiliki GAP-01 |
| PR-06 / BR-06 | Assignment dan PIC — Inti | Assignor memilih PIC aktif dan P01–P04; start/finish/pending/resume/cancel mengikuti status; sesi tercatat | Tersedia; kode kesulitan tambahan dan pending memiliki catatan |
| PR-07 / BR-07 | Review hasil — Inti | Hanya pengaju pada T05 dapat memberi feedback atau close; feedback wajib berisi teks; penghitung revisi bertambah | Tersedia; feedback kosong memiliki GAP-03 |
| PR-08 / BR-08 | Laporan/PDF/KPI — Pendukung | Filter dan PDF sesuai data; KPI dapat direkonsiliasi dengan sesi dan master target | Tersedia; satuan KPI harus diselaraskan |
| PR-09 / BR-09 | Notifikasi/aktivitas — Pendukung | Penerima yang tepat mendapat tautan tindakan; kegagalan email dapat ditelusuri; komentar/aktivitas tersedia | Tersedia untuk jalur yang dipanggil; email awal vendor belum terhubung pada store |
| PR-10 / BR-10 | Vendor — Jalur khusus | Data usaha/pemilik/bank/kontak lengkap; approval sesuai holding/departemen; validator melengkapi field sistem dan menyelesaikan/menolak | Tersedia; data konteks, tahap, dan file perlu UAT |

## 5. Perjalanan pengguna

### 5.1 Perubahan transaksi

1. Pengaju membuka hub, memilih lingkungan, modul, dan jenis perubahan.
2. Pengaju melihat preview approval, mengisi transaksi/kondisi, melampirkan bukti bila ada, dan menandatangani.
3. Sistem menentukan pihak sesuai master/posisi pengaju. Auto-approval/auto-validation hanya berlaku jika resolver menghasilkan kondisi tersebut.
4. Checker memeriksa dampak. Bila perlu tiket turunan, checker menjadi pengaju draft, melengkapi perubahan, lalu mengirimkannya.
5. Setelah prasyarat kelompok terpenuhi, validator meninjau dan meneruskan ke assignor.
6. Assignor menetapkan PIC dan kesulitan. PIC memulai, dapat menjeda karena pihak lain, lalu melanjutkan pekerjaan.
7. PIC menyelesaikan dengan catatan. Pengaju menutup setelah menerima hasil atau memberikan feedback untuk revisi.

### 5.2 Vendor

1. Pengaju memilih Epicor/Accurate dan kategori vendor.
2. Data usaha, pemilik, rekening, pembayaran, kontak, signature, dan dokumen pendukung diisi.
3. Sistem mencari group approval dari hub vendor/holding dan rantai sesuai departemen/pengaju.
4. Pihak pada tahap aktif mengambil keputusan; validator melengkapi field konfigurasi sistem.
5. Pengajuan berakhir approved/rejected. Status ini tidak membuktikan data telah dibuat pada ERP eksternal.

## 6. Ketentuan pengalaman pengguna

- Validasi harus menunjukkan field yang perlu dilengkapi dalam Bahasa Indonesia.
- Detail menampilkan status, pihak, komentar, lampiran, dan hubungan tiket agar langkah berikutnya dapat dipahami.
- Draft turunan dapat dibedakan dari tiket yang sudah diajukan; nilai sebelum/sesudah wajib diisi ulang secara sadar.
- Selesai oleh PIC mengirim pekerjaan kepada pengaju; penutupan akhir melalui aksi pengaju.
- Batas lampiran, label status, dan satuan waktu harus konsisten antara form, server, laporan, dan PDF. Ketidaksesuaian saat ini tercatat pada FRD.

## 7. Persyaratan kualitas untuk penerimaan

| Area | Kriteria | Kondisi peninjauan |
| --- | --- | --- |
| Hak akses | Aksi/detail/PDF/berkas hanya tersedia kepada pihak berwenang; URL langsung diuji | Matriks akses belum diuji |
| Konsistensi | Request ulang/bersamaan tidak menghasilkan duplikasi, sesi ganda, atau approval parsial | Ada transaksi/lock/pengecekan duplikasi pada sejumlah jalur; belum diuji menyeluruh |
| Pelaporan | Target, aktual, jeda, revisi, dan filter memiliki definisi konsisten | Ada temuan satuan KPI |
| Operasional | Queue, mailer, storage, signature, dan PDF berfungsi di lingkungan tujuan | Belum diuji pada pekerjaan dokumentasi ini |
| Performa | Respons memadai untuk volume tiket/pengguna yang disepakati | Target latensi, kapasitas, volume uji belum ditetapkan |

## 8. Skenario UAT

Semua skenario berstatus **belum dijalankan pada peninjauan ini**. Siapkan tiga lingkungan tiket, akun tiap peran, tiket dengan/tanpa checker, serta dependensi internal/lintas lingkungan.

| ID | Cakupan | Hasil yang harus dibuktikan |
| --- | --- | --- |
| UAT-01 | PR-01 | Master/canvas tersimpan dan memengaruhi preview/alur yang benar |
| UAT-02 | PR-02 | Pengajuan valid berhasil; tanpa detail/subdetail/signature atau dengan file tidak valid ditolak jelas |
| UAT-03 | PR-03 | Approve/reject, urutan pihak, auto-approval, checker, dan akses mengikuti konfigurasi |
| UAT-04 | PR-04 | Kondisi benar/salah, forward/reverse, anti-loop, request ulang/bersamaan menghasilkan rantai benar |
| UAT-05 | PR-04 | Draft menyimpan referensi, tidak menyalin before/after, dapat diajukan; pembatalan menyinkronkan induk |
| UAT-06 | PR-05 | Validasi individual/kelompok berhasil; checker/draft tertunda menghalangi; penolakan tercatat |
| UAT-07 | PR-06 | Assignment/reassignment dan PIC aktif tepat; pihak lain tidak dapat mengubah pekerjaan |
| UAT-08 | PR-06/07 | Start → pending → resume → finish → feedback → start → finish → close menghasilkan status/sesi benar |
| UAT-09 | PR-07 | Feedback kosong memberi validasi; pengaju lain/aksi di luar T05 ditolak; close tidak diproses ulang |
| UAT-10 | PR-08 | Kerja 30 menit + pending 60 menit + kerja 15 menit menghasilkan aktual 45 menit; review pengaju tidak menambah aktual |
| UAT-11 | PR-08 | Filter, PDF, status, akses, dan angka dashboard tepat; target/aktual memakai satuan yang disepakati |
| UAT-12 | PR-09 | Email kepada pihak benar, tautan tepat, failure/retry tercatat; transaksi gagal tidak mengirim notifikasi prematur |
| UAT-13 | PR-10 | Vendor kedua sistem dapat diajukan dan diproses sampai akhir; field validator/file/redirect/notifikasi benar |
| UAT-14 | Seluruh PR | URL/request langsung tidak membuka data/aksi pihak lain; endpoint yang tidak lengkap ditutup atau diperbaiki |

## 9. Kriteria siap rilis

Rilis memerlukan penyelesaian temuan prioritas FRD, bukti UAT, rekonsiliasi KPI, kesiapan skema database, verifikasi email/storage, dan penerimaan pemilik proses. [Milestone](MILESTONE.md) memuat tahap dan usulan penanggung jawab. Baseline tidak menyatakan go-live atau persentase penyelesaian produksi.

## 10. Bukti implementasi

- [TicketController](../../app/Http/Controllers/TicketController.php): pengajuan, preview, generated draft, aksi pengaju, vendor, PDF, laporan pengguna.
- [ApprovalController](../../app/Http/Controllers/ApprovalController.php): approval, checker, validator, assignment, PIC, approval vendor.
- [TicketWebController](../../app/Http/Controllers/TicketWebController.php): master, canvas, laporan/KPI admin.
- [TicketKpiController](../../app/Http/Controllers/Approval/TicketKpiController.php): dashboard dan data KPI PIC.
- [FRD](FRD.md): kontrak field, transisi, service, tabel, dan temuan implementasi.
