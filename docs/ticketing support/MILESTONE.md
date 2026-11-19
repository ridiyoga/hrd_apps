//CTRL+SHIFT+V
# Milestone — Ticketing Support

| Informasi | Keterangan |
| --- | --- |
| Aplikasi / versi dokumen | `hrd_apps_server` / 1.0 |
| Tanggal baseline | 1 Oktober 2026 |
| Dasar penilaian | Pemeriksaan source code, bukan riwayat penyelesaian proyek atau bukti UAT |
| Dokumen terkait | [BRD](BRD.md), [PRD](PRD.md), [FRD](FRD.md) |

## 1. Cara membaca milestone

Dokumen ini memetakan tahapan modul yang sudah memiliki implementasi dan pekerjaan menuju penerimaan operasional. Nomor milestone menunjukkan urutan ketergantungan yang masuk akal, bukan tanggal pengerjaan historis.

- **Terdokumentasi:** artefak dokumentasi tersedia dan ditinjau terhadap source code.
- **Tersedia di kode — perlu verifikasi:** route/handler/service/view terkait ditemukan; keberhasilan end-to-end belum dibuktikan pada peninjauan ini.
- **Direncanakan:** pekerjaan lanjutan berdasarkan temuan dan kebutuhan penerimaan; belum dinyatakan dikerjakan atau disetujui jadwalnya.
- **Menunggu penerimaan:** memerlukan hasil pengujian dan keputusan pemilik proses.

Tidak ditetapkan persentase selesai, tanggal selesai historis, tanggal go-live, atau nama PIC tanpa bukti. Penanggung jawab pada tabel adalah **usulan peran**; target tanggal seluruh milestone menunggu penetapan tim.

## 2. Milestone implementasi dan dokumentasi

| ID | Hasil / cakupan | Ketergantungan | Status baseline | Usulan penanggung jawab | Bukti / kriteria penutupan |
| --- | --- | --- | --- | --- | --- |
| M00 | BRD, PRD, FRD, dan milestone berbasis implementasi | Pemeriksaan repository | Terdokumentasi | Analis sistem + pemilik proses | Empat dokumen tersedia, saling tertaut, kebutuhan dan temuan dapat ditelusuri; persetujuan bisnis masih terpisah |
| M01 | Master layanan, hub, organisasi approval, kondisi, dependency, validator, assignment, difficulty, canvas | Data User/Karyawan/organisasi | Tersedia di kode — perlu verifikasi | Developer + admin aplikasi | S04/S12 pada FRD; UAT-01 membuktikan simpan/baca ulang serta pengaruh konfigurasi terhadap alur |
| M02 | Pengajuan transaksi, detail/subdetail, lampiran, tanda tangan, detail tiket, edit draft | M01 | Tersedia di kode — perlu verifikasi | Developer + perwakilan pengaju | S02; UAT-02 lulus, GAP-04 dan route terkait GAP-05 ditangani |
| M03 | Preview approval, keputusan atasan, pemeriksaan checker | M01–M02 | Tersedia di kode — perlu verifikasi | Developer + approver/checker | S02/S03/S05; UAT-03 lulus dengan variasi pihak dan auto-approval |
| M04 | Dependency forward/reverse, draft turunan, lineage, lintas lingkungan, retry, pembatalan draft | M01–M03 | Tersedia di kode — perlu verifikasi | Developer + checker | S05/S06/S07; UAT-04/05 lulus, termasuk anti-loop dan pengiriman bersamaan |
| M05 | Validasi individual/kelompok dan gerbang kesiapan rantai | M03–M04 | Tersedia di kode — perlu verifikasi | Developer + validator | S03/S07/S09; GAP-01 ditutup dan UAT-06 lulus |
| M06 | Assignment/PIC, sesi kerja, pending/resume, feedback/close/cancel | M05 dan master assignment | Tersedia di kode — perlu verifikasi | Developer + assignor/PIC/pengaju | S03/S08/S02; UAT-07/08/09 lulus, GAP-03 dan bagian GAP-07 diselesaikan |
| M07 | Formulir dan approval vendor Epicor/Accurate | Hub dan approval group pada M01 | Tersedia di kode — perlu verifikasi | Developer + pemilik proses vendor | S02/S03/S10; UAT-13 lulus termasuk konteks holding/menu, file, dan tahap yang tidak terisi |
| M08 | Laporan pengguna/admin, PDF, KPI admin/PIC | M02–M06 dan data sesi kerja | Tersedia di kode — perlu verifikasi | Developer + pemantau layanan | S02/S04/S08; GAP-02 ditutup, UAT-10/11 lulus dengan rekonsiliasi angka |
| M09 | Email tahap berikutnya, komentar, jejak aktivitas | Tahap proses terkait; mailer/queue/storage | Tersedia di kode — perlu verifikasi | Developer + pengelola aplikasi | S09/S10; UAT-12 lulus dan cakupan notifikasi vendor dipastikan |

Referensi S01–S12 dijelaskan di [FRD bagian 1](FRD.md#1-metode-dan-sumber). Pengelompokan fitur M01–M09 tidak berarti semua kriteria penutupan sudah tercapai.

## 3. Milestone lanjutan menuju penerimaan

| ID | Tahap | Masukan / dependensi | Status | Usulan penanggung jawab | Keluaran dan syarat selesai |
| --- | --- | --- | --- | --- | --- |
| M10 | Perbaikan dan penyelarasan baseline | Daftar GAP-01–GAP-10; master dan skema uji | Direncanakan | Developer + analis sistem | Temuan direproduksi, diperbaiki atau diputuskan secara eksplisit; unit waktu/target disepakati; route, skema, akses, dan vendor diuji ulang |
| M11 | UAT lintas peran dan regresi | M10; akun/data uji tiga lingkungan; queue/storage uji | Direncanakan | QA + perwakilan setiap peran | UAT-01–UAT-14 memiliki bukti, hasil, pelaksana, tanggal, dan referensi temuan; temuan penghambat ditutup dan diuji ulang |
| M12 | Penerimaan bisnis dan kesiapan rilis | M11 serta persetujuan cakupan BRD/PRD | Menunggu penerimaan | Pemilik proses + pengelola aplikasi | Persetujuan bisnis, keputusan rilis, prosedur instalasi/upgrade, pemulihan, monitoring email/queue, serta panduan operasional tersedia |

Target tanggal M10–M12 ditentukan setelah triase temuan, pemeriksaan skema aktual, serta ketersediaan tim UAT. Tidak ada estimasi durasi yang dianggap komitmen pada baseline ini.

## 4. Urutan tindak lanjut yang disarankan

| Urutan | Pekerjaan | Referensi | Bukti yang dibutuhkan |
| --- | --- | --- | --- |
| 1 | Pastikan skema uji lengkap dan cocok dengan query/field kode | GAP-06 | Instalasi/upgrade uji berhasil tanpa kehilangan data; field inti/pending/sesi tersedia |
| 2 | Perbaiki jalur validasi individual, feedback kosong, edit draft, dan endpoint yang tidak lengkap | GAP-01/03/04/05 | Skenario positif/negatif berhasil dan pesan validasi sesuai |
| 3 | Verifikasi hak akses per peran, ownership, holding, detail/PDF/lampiran dan aksi admin | GAP-09 | Matriks pengujian akses termasuk request URL langsung |
| 4 | Selaraskan satuan waktu, target kesulitan, dan status pending pada seluruh laporan | GAP-02/07 | Perhitungan manual cocok dengan sesi, header, dashboard, dan label |
| 5 | Selesaikan alur vendor, file, konteks menu/holding, serta notifikasi | GAP-08 | Pengajuan sampai akhir untuk kedua sistem dan variasi approval group |
| 6 | Uji transaksi, retry, pengiriman bersamaan, dan pemulihan kegagalan | GAP-10, FR-04/09 | Tidak ada duplikasi/record parsial; job gagal dapat ditelusuri |
| 7 | Jalankan seluruh UAT dan tinjauan bisnis | PRD bagian 8 | Laporan UAT serta penerimaan tercatat pada M11/M12 |

Pekerjaan dapat berjalan bersamaan bila dependensinya terpenuhi; tabel tidak menetapkan penugasan personel atau izin deployment.

## 5. Bukti minimum untuk menutup milestone

Setiap penutupan M01–M12 selain penyusunan dokumen harus mencatat:

1. Versi/commit aplikasi dan lingkungan pengujian.
2. Skenario yang dijalankan beserta data uji dan peran pelaksana.
3. Hasil aktual, bukti pendukung, serta daftar temuan yang masih terbuka.
4. Tanggal pelaksanaan dan pihak yang menerima hasil.
5. Untuk rilis: kesiapan migration/skema, konfigurasi mailer/queue/storage, prosedur pemulihan, dan pemantauan operasional.

## 6. Catatan baseline

| Item | Hasil per 1 Oktober 2026 |
| --- | --- |
| Dokumen awal | BRD.md, PRD.md, FRD.md kosong sebelum penyusunan |
| Pemeriksaan implementasi | Route, controller, service, model, view, migration/seeder terkait sudah ditinjau secara statis |
| Dokumen hasil | BRD, PRD, FRD direvisi/diisi; MILESTONE.md ditambahkan |
| Pengujian aplikasi dalam pekerjaan dokumentasi ini | Tidak dijalankan; tidak ada transaksi bisnis atau pengiriman email yang dilakukan |
| Riwayat penyelesaian fitur | Tidak diverifikasi; tanggal file/migration tidak dipakai sebagai tanggal selesai |
| UAT dan persetujuan bisnis | Belum dibuktikan |
| Go-live | Belum dapat disimpulkan dari pemeriksaan repository |
