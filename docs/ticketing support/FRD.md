//CTRL+SHIFT+V
# FRD — Ticketing Support

| Informasi | Keterangan |
| --- | --- |
| Aplikasi / versi | `hrd_apps_server` / dokumen 1.0 |
| Tanggal peninjauan | 1 Oktober 2026 |
| Status | Baseline source code; belum diverifikasi melalui runtime/UAT |
| Dokumen terkait | [BRD](BRD.md), [PRD](PRD.md), [Milestone](MILESTONE.md) |

## 1. Metode dan sumber

Spesifikasi ini membedakan perilaku yang ditemukan di kode, hasil yang diharapkan untuk penerimaan, dan keterbatasan implementasi. Framework yang dinyatakan pada [composer.json](../../composer.json) adalah Laravel `^12.0` dengan PHP `^8.2`; view menggunakan Blade. Versi paket terpasang dan kesiapan server tidak diuji dalam peninjauan ini.

| Ref | Sumber | Tanggung jawab |
| --- | --- | --- |
| S01 | [routes/Users.php](../../routes/Users.php), [routes/web.php](../../routes/web.php) | Registrasi route pengguna dan admin |
| S02 | [TicketController](../../app/Http/Controllers/TicketController.php) | Pengajuan, generated draft, feedback/close, vendor, laporan pengguna, PDF |
| S03 | [ApprovalController](../../app/Http/Controllers/ApprovalController.php) | Approver, checker, validator, assignment, PIC, approval vendor |
| S04 | [TicketWebController](../../app/Http/Controllers/TicketWebController.php) | Master, canvas, laporan/KPI admin, pembatalan generated draft |
| S05 | [CheckerService](../../app/Services/CheckerService.php), [DependencyEngine](../../app/Services/DependencyEngine.php) | Evaluasi kondisi dan arah dependensi |
| S06 | [GeneratedTicketService](../../app/Services/GeneratedTicketService.php), [TicketGenerationService](../../app/Services/TicketGenerationService.php), [TicketSubmissionService](../../app/Services/TicketSubmissionService.php) | Pembuatan draft, retry generation, submit tiket turunan |
| S07 | [TicketChainService](../../app/Services/TicketChainService.php) | Representasi rantai dan sinkronisasi status kelompok |
| S08 | [TicketKpiService](../../app/Services/TicketKpiService.php), [TicketKpiController](../../app/Http/Controllers/Approval/TicketKpiController.php) | Sesi kerja, durasi, KPI PIC |
| S09 | [TicketApprovalEmailService](../../app/Services/TicketApprovalEmailService.php), [SendTicketApprovalEmailJob](../../app/Jobs/SendTicketApprovalEmailJob.php) | Penerima dan antrean email tiket perubahan |
| S10 | [TicketVendorApprovalEmailService](../../app/Services/TicketVendorApprovalEmailService.php) | Email approval vendor |
| S11 | [Model TicketHead](../../app/Models/TicketHead.php), [model TicketApproval](../../app/Models/TicketApproval.php), [direktori model](../../app/Models) | Status, atribut, dan hubungan data |
| S12 | [View pengguna](../../resources/views/users/ticket), [view admin](../../resources/views/admin/ticket) | Form, daftar, laporan, dan interaksi pengguna |

File service bernama `(backup)` tidak dijadikan sumber perilaku aktif. Nama method dicantumkan sebagai penunjuk yang lebih stabil daripada nomor baris.

## 2. Aktor, identitas, dan akses

- Route pada `routes/Users.php` berada dalam grup `auth`. Tindakan approval menggunakan resolusi identitas User/Karyawan melalui `currentApprovalSubjectIds()` dan fungsi terkait.
- `requestor_id` pada tiket baru menyimpan `Auth::id()`; PIC dan master assignment menggunakan identitas karyawan. Keduanya tidak boleh dianggap ID yang selalu sama.
- Peran assignment: `A01` = assignor, `A02` = PIC; tindakan memeriksa status aktif master.
- Approver dan checker ditentukan dari pihak pada tiket/record approval. Validator menggunakan master validator serta konteks lingkungan pada jalur terkait.
- Hub dan navigasi menggunakan konfigurasi user/role. Pembatasan navigasi bukan bukti bahwa setiap URL/API telah memiliki otorisasi yang lengkap; pembacaan detail, ekspor, pembatalan admin, dan perubahan draft memerlukan pengujian akses langsung.

## 3. Spesifikasi fungsi

### FR-01 — Master dan konfigurasi (PR-01)

S04 menyediakan pengelolaan menu/modul, submenu/jenis perubahan, kondisi, hub menu dan user, approval group hub per holding, approval flow beserta step/karyawan, dependency rule, validator, assignment, serta difficulty.

Canvas menyediakan pengambilan node/edge, penyimpanan posisi, toggle edge, penambahan cepat submenu, pelepasan posisi node, daftar flow, pemetaan flow, dan struktur organisasi. Konfigurasi ini menjadi masukan resolver approval/dependensi, bukan bukti bahwa semua kombinasi konfigurasi valid.

Dependency rule memiliki sumber/tujuan submenu, `condition_key`, status aktif, `is_conditional`, `is_bidirectional`, `is_cross_environment`, serta kondisi checker/requestor menurut arah. `is_mandatory_forward` juga dikenali model/engine; ketersediaan atribut model tidak otomatis berarti seluruh atribut dapat diatur dari setiap form.

Lingkungan menu tiket: Epicor ERP (1), Accurate Online SP (2), Accurate Online AJA (3). Flow dan pihak yang dipilih bergantung master organisasi/karyawan yang tersedia.

### FR-02 — Pengajuan dan perubahan draft (PR-02)

S02 `ticketHead_store()` memvalidasi masukan, mencari data karyawan, menyelesaikan resolver approval, membuat UUID/nomor tiket, menyimpan header, detail/subdetail, checker, lampiran, tanda tangan, root tiket, activity log, kemudian memanggil notifikasi.

| Masukan | Validasi yang ditemukan |
| --- | --- |
| `menu_ticket`, `submenu_ticket` | Wajib |
| `details` | Array wajib, minimal satu transaksi |
| `details.*.nomor_transaksi` | String wajib, maksimal 255 karakter |
| `details.*.tanggal_transaksi` | Tanggal wajib |
| `details.*.sub_details` | Array wajib, minimal satu baris |
| `line_number` per subdetail | Wajib |
| `alasan_ubah`, `before_ubah`, `after_ubah` per subdetail | String wajib |
| `signature` | Wajib |
| `lampiran` | Array opsional |
| `lampiran.*` | File maksimal 20.480 KB menurut validator; ekstensi pdf, doc/docx, xls/xlsx, ppt/pptx, txt, csv, rtf, jpg/jpeg, png, gif, webp, bmp, zip, rar, 7z |
| `selected_conditions`, `include_backward_dependency` | Dibaca sebagai masukan resolver; jangan diasumsikan memiliki validasi rinci yang sama dengan detail |

UI dan pesan validasi masih menyebut maksimal 5 MB per file, berbeda dari rule server sekitar 20 MB (GAP-04).

Status awal dari `determineInitialStatus()`:

| Kondisi resolver | Status |
| --- | --- |
| `auto_validated` | T13, menunggu penugasan |
| `auto_approved` dan ada checker | T12, menunggu pemeriksaan |
| `auto_approved` tanpa checker | T02, disetujui |
| Selain itu | T01, menunggu persetujuan |

`ticketHead_update()` hanya mengizinkan status T00, lalu memperbarui detail dan mengajukan kembali. Jalur ini memiliki temuan pada scope variabel dan penanganan lampiran. Route hapus tiket terdaftar tetapi handler tidak ditemukan; penghapusan umum tidak dinyatakan berfungsi (GAP-05).

### FR-03 — Persetujuan dan checker (PR-03)

- `previewApprovalFlow()` menampilkan hasil resolver sebelum pengajuan. Resolver memeriksa flow/submenu, posisi requestor, atasan, dan checker; tidak semua tiket selalu membutuhkan dua approver.
- `saveApproveTicket()` memeriksa apakah pelaku cocok dengan approver 1/2 pada tiket. Persetujuan memerlukan tanda tangan; komentar opsional maksimal 1.000 karakter. Nilai keputusan, waktu, identitas, dan tanda tangan disimpan; finalisasi dan notifikasi dievaluasi kembali.
- Jalur penolakan tersedia. Efeknya terhadap kelompok tiket harus diuji bersama skenario penolakan kelompok, bukan dianggap identik dengan penolakan satu baris checker.
- `saveCheckerTicket()` memeriksa identitas checker dan mencegah pemeriksaan ulang pada record yang sudah `completed`/`approved`. Masukan wajib: signature dan menu ID; komentar maksimal 1.000 karakter.
- `CheckerService::processCheckerSubmission()` memproses kondisi, detail yang dipilih, opsi penyalinan data, dan pembuatan draft. Bila ada draft menunggu detail, pengguna diarahkan ke antrean formulir draft yang disimpan dalam session.

### FR-04 — Dependensi dan generated ticket (PR-04)

1. Engine menentukan apakah rule aktif dapat ditelusuri dari submenu saat ini. Forward memakai arah sumber ke tujuan; reverse memerlukan aturan dua arah.
2. Engine memiliki pemeriksaan incoming edge, riwayat traversal, dan lineage untuk menghindari siklus/penelusuran berulang. Perilaku setiap kombinasi kondisi harus diuji.
3. Checker memicu `GeneratedTicketService::createDraftFromApproval()`. Draft memakai T00, `is_draft=true`, `is_submit=false`; requestor draft adalah User checker.
4. Metadata `root_ticket_id`, `parent_ticket_id`, `generated_from_ticket_id`, dan `dependency_rule_id` menelusuri asal. Record approval menyimpan `generated_ticket_id`, status, waktu, dan kesalahan generation.
5. Detail induk yang dipilih dapat menjadi referensi melalui `reference_ticket_detail_id`. Opsi salin lengkap menyalin nomor baris/alasan; **nilai before/after tetap kosong** untuk diisi ulang.
6. Terdapat pengecekan penggunaan kembali draft/record yang sudah dibuat. Ketahanan terhadap request bersamaan belum dibuktikan melalui pengujian.
7. `TicketSubmissionService::submitDraftGenerated()` melengkapi draft, menghitung ulang approval, menyimpan detail/lampiran, mengubah flag submit, menandai generation `SUCCESS`, dan menyinkronkan kelompok.
8. Arah draft turunan diwariskan dari rule pembentuknya melalui `deriveIncludeBackward()`; pilihan arah requestor tidak bebas mengubah lineage tiket turunan.
9. Tiket lintas lingkungan dapat memiliki root baru pada dirinya sendiri, dengan `origin_cross_ticket_id` menunjuk asal. Kelompok internal dan lintas lingkungan tidak boleh diperlakukan sebagai satu alur linear wajib.
10. Retry tersedia melalui `retryGenerateApproval()` / `TicketGenerationService`; hanya checker yang sesuai dan record dengan dependency rule yang dapat memintanya.

Status generation: `PENDING`, `AWAITING_DETAILS`, `SUCCESS`, `FAILED`, `SKIPPED`; `TicketGenerationService` juga mendefinisikan `PROCESSING`. Status ini berbeda dari status tiket T00–T14.

Pembatalan melalui laporan admin memanggil `cancelGeneratedDraft()`. Service memeriksa `is_draft` dan status T00/T12, menandai tiket T08/rejected, mengubah record generation induk ke `SKIPPED`, mengembalikan kondisi terkait ke false, mengevaluasi ulang status induk, dan mencatat aktivitas. Otorisasi admin yang lebih spesifik perlu dibuktikan; pemeriksaan eksplisit pada handler mencakup pengguna sudah login.

### FR-05 — Validasi (PR-05)

`saveValidatorTicket()` dan `saveValidatorGroupTicket()` menyediakan aksi `validate`/`reject`, signature wajib, dan komentar opsional maksimal 1.000 karakter. Jalur kelompok mewajibkan `root_ticket_id`; alasan penolakan kelompok wajib maksimal 1.000 karakter.

Sebelum menerima kelompok, kode memeriksa kesiapan rantai melalui `isChainFullyApprovedAndChecked()` dan status checker terkait. Kelompok internal yang masih memiliki draft atau proses approval/checker tertunda tidak seharusnya dilanjutkan. Validasi berhasil menyimpan identitas/tanda tangan/waktu, `validator=true`, approval `approved`, execution `ready`, dan T13.

Jalur individual memiliki variabel `$rootId` yang digunakan sebelum didefinisikan (GAP-01). Dengan demikian, keberadaan route/form tidak cukup untuk menyatakan validasi individual siap digunakan.

### FR-06 — Assignment dan pelaksanaan PIC (PR-06)

`storeAssignTicket()` memerlukan tiket yang ada, karyawan PIC yang ada dan aktif pada master A02, actor assignor aktif, serta `validator` tiket bernilai benar. `priority` hanya menerima P01/P02/P03/P04; note opsional maksimal 500 karakter. Penugasan menyimpan assignor, waktu, PIC, kesulitan, T03, execution `ready`, serta mengosongkan `finish_at`. Penugasan ulang juga ditangani.

`updatePicTicketStatus()` memerlukan PIC aktif yang sama dengan `pic_id` tiket. T06/T08 ditolak. Aksi dan status yang **diizinkan controller saat ini**:

| Aksi | Status asal yang diizinkan | Hasil | Catatan |
| --- | --- | --- | --- |
| `start` | T02, T11, T13, T03, T07 | T04 / execution `in_progress` | Membuka sesi bila belum ada sesi aktif |
| `finish` | T02, T11, T13, T03, T04, T07 | T05 / execution `done` | Note wajib; menutup sesi dan mengisi `finish_at` |
| `pending` | T04 | T14 / execution tetap `in_progress` | Note wajib; menutup sesi, menyimpan alasan/waktu pending |
| `resume` | T14 | T04 | Membuka sesi bertipe `lanjutan` |
| `cancel` | T02, T11, T13, T03, T04, T07, T14 | T08 / execution `waiting` | Note wajib; menutup sesi dan mengisi `closed_at` |

Note aksi PIC maksimal 1.000 karakter. Daftar asal di atas lebih luas daripada alur normal T03 → T04 → T05; pembatasan bisnis final perlu dikonfirmasi melalui UAT. `finish` tanpa `start` memiliki fallback pembuatan sesi dari `pic_started_at`, `assign_at`, `validator_at`, atau waktu saat ini.

### FR-07 — Feedback dan close (PR-07)

`requestorActionTicket()` menerima `feedback`/`close` dengan komentar maksimal 1.000 karakter. Tiket dicari berdasarkan `requestor_id = Auth::id()`, harus T05, dan status dicek kembali di dalam transaksi dengan row lock.

- `feedback`: komentar wajib berisi teks, T07, `feedback_cycles` bertambah, `finish_at` dikosongkan; PIC memulai sesi revisi dan menerima notifikasi terkait.
- `close`: T06, execution `done`, `closed_at` diisi, aktual/target/variance/overrun dievaluasi.
- Cabang feedback kosong saat ini mengakses `$ticket` sebelum tiket dimuat; respons validasi yang diharapkan perlu diperbaiki (GAP-03).

### FR-08 — Laporan, PDF, dan KPI (PR-08)

Laporan pengguna/admin menyediakan daftar dan detail berdasarkan parameter yang tersedia pada handler, termasuk departemen, status, kategori, holding, dan periode pada jalur yang mendukungnya. Dashboard admin memiliki summary, trend, top divisi, top module, assignee performance, detail rata-rata waktu, detail SLA PIC, dan distribusi status. Dashboard PIC menampilkan ringkasan PIC dan tiket beserta sesi.

`ticketExport()` menggunakan Dompdf, kertas A4 portrait, view `users.ticket.export`; pada user-agent mobile hasil diunduh, selain itu ditampilkan melalui stream. Ekspor ini adalah PDF per tiket; ekspor spreadsheet seluruh laporan tidak dinyatakan tersedia.

**Perhitungan aktual yang ditemukan pada S08:**

- Durasi sesi = selisih detik mulai–selesai / 60, dibulatkan dua desimal.
- `calculateTotalActualHours()` mengembalikan jumlah `duration_minutes` sesi tertutup, tanpa dibagi 60. Nilainya adalah menit meskipun nama fungsi/kolom memakai `hours`.
- Pending eksternal dan review pengaju tidak dihitung sebagai sesi aktif normal. Feedback membuka siklus revisi saat PIC mulai lagi; resume memakai `lanjutan`.
- Saat close: `variance_hours = actual_hours - expected_hours`; `overrun_flag = actual_hours > expected_hours` bila target tersedia.
- `summaryForPic()` memakai tiket T06 dengan execution `done` untuk metrik selesai; efisiensi adalah rata-rata `(expected / actual) × 100` pada tiket aktual > 0 dan target tidak null.
- On-time = jumlah tiket selesai dikurangi overrun; rate = on-time / total selesai × 100. Jika tidak ada tiket selesai, service mengembalikan rate 100; ini bukan bukti pencapaian layanan.
- Penghitungan tiket aktif pada service PIC meliputi T03/T04/T05/T07, belum T14. Dashboard admin menggunakan query/perhitungan tersendiri, sehingga kesamaan angka tidak boleh diasumsikan.

| Kode default | Nama | Nilai `expected_hours` pada seeder/service |
| --- | --- | --- |
| P01 | Critical Risk | 24 |
| P02 | High | 12 |
| P03 | Medium | 6 |
| P04 | Low | 1 |

Form master difficulty menyebut satuan **menit**, sementara nama kolom dan beberapa label menyebut hours. Tabel di atas mencatat nilai mentah, bukan menetapkan jam atau SLA resmi. Satuan, default, konversi, data lama, dan label perlu diselaraskan sebelum laporan dipakai sebagai KPI resmi (GAP-02).

### FR-09 — Notifikasi dan aktivitas (PR-09)

S09 menentukan penerima tahap berikutnya berdasarkan status dan kesiapan kelompok, lalu menjalankan `SendTicketApprovalEmailJob` untuk pesan single/group. Job menggunakan mailer `ticket`, `afterCommit`, maksimal 3 percobaan, backoff `[10, 30, 60]` detik, timeout 60 detik, dan pencatatan kegagalan. Efek antrean bergantung konfigurasi queue/worker lingkungan.

Service vendor menggunakan mailer `hrd` dan `send()` langsung. Pemanggilan ditemukan setelah approval vendor ketika status masih `process`; `ticketVendorStore()` belum memanggil service notifikasi awal. Email vendor tidak didokumentasikan sebagai pengiriman melalui job tiket perubahan.

Komentar approval dan sejumlah activity log disimpan pada jalur tindakan. Adanya model execution log tidak membuktikan semua perubahan status menulis log tersebut. Cakupan audit per aksi perlu diperiksa dalam UAT.

### FR-10 — Pendaftaran vendor (PR-10)

Pengajuan menyimpan header, detail, dokumen, dan tanda tangan dalam alur yang berbeda dari `ticket_head`. `system_type` wajib `1`/`2`; `vendor_category` wajib `V01`/`V02`.

| Kelompok | Field pokok pada form/validator |
| --- | --- |
| Usaha | Nama/alamat usaha wajib; NPWP, NPWP16, alamat NPWP, afiliasi opsional |
| Pemilik | Nama, NIK maksimal 16 karakter, alamat wajib |
| Bank dan pembayaran | Nama bank, nomor rekening, nama penerima, metode dan syarat pembayaran wajib; cabang opsional |
| Kontak | Nama dan nomor telepon wajib; alamat dan jabatan opsional |
| Konfigurasi sistem | Tax liability, supplier group, GL control Epicor; cabang/kategori/sourcing Accurate |
| Bukti | Signature wajib; dokumen dan nama dokumen dapat dilampirkan |

Nomor pengajuan berbentuk `VND-YYYYMMDD-` diikuti enam karakter UUID yang dikapitalisasi. Group approval dicari dari hub route `ticket.vendor` dan holding, kemudian rantai disaring berdasarkan departemen/pengaju. Ketiadaan konfigurasi menghasilkan kesalahan pengajuan.

Tahapan handler vendor: `atasan1`, `atasan2`, `checker`, `validator`; pelaku harus cocok dengan ID pihak pada `current_step`. Status header menggunakan `process`, `approved`, `rejected`, bukan T00–T14. Setelah validator terakhir, `current_step=done`; reject mengosongkan current step. Konfigurasi tanpa pihak pada tahap tertentu harus diuji karena pemilihan tahap berikutnya mengikuti urutan tetap.

Pada approval validator Epicor, field tax liability, supplier group, GL control type/code diwajibkan; Accurate mewajibkan kategori. Dokumen vendor diunggah ke disk public, tetapi aturan file pada validator pengajuan belum setara dengan lampiran tiket perubahan (GAP-08).

## 4. Kamus status tiket perubahan

Sumber utama: `TicketHead` dan handler transisi. Urutan kode bukan urutan wajib perjalanan tiket.

| Kode | Makna operasional / label |
| --- | --- |
| T00 | Draft; generated draft perlu dilengkapi |
| T01 | Menunggu persetujuan |
| T02 | Disetujui; tidak otomatis berarti sudah ditugaskan |
| T03 | Ditugaskan |
| T04 | Dikerjakan |
| T05 | Menunggu respons/review pengaju |
| T06 | Selesai/ditutup pengaju |
| T07 | Feedback dari pengaju; perlu tindak lanjut PIC |
| T08 | Dibatalkan atau ditolak; pembeda juga memakai approval status/konteks aksi |
| T09 | Label model: Disetujui Atasan 1; nama konstanta `STATUS_WAITING_APPROVAL1` |
| T10 | Label model: Disetujui Atasan 2; nama konstanta `STATUS_WAITING_APPROVAL2` |
| T11 | Menunggu divalidasi |
| T12 | Menunggu pemeriksaan/checker, termasuk kondisi menunggu draft terkait |
| T13 | Menunggu penugasan |
| T14 | Pending pihak eksternal; konstanta dan handler tersedia, mapping label belum merata |

Dimensi terpisah:

- `approval_status`: `draft`, `pending`, `approved`, `rejected`, `checked`.
- `execution_status`: `waiting`, `ready`, `in_progress`, `done`.
- `cross_env_status`: `NONE`, `PENDING_TRIGGER`, `TRIGGERED`, `SKIPPED`.
- `is_draft`, `is_submit`, `is_generated`: flag tambahan; tampilan/status kelompok dapat mempertimbangkannya.

T05 memiliki execution `done` tetapi tiket belum ditutup. Nama konstanta T09/T10 berbeda dari label tampilannya; gunakan konteks handler, jangan menerjemahkan nama konstanta saja.

## 5. Model data utama

Nama tabel berikut berasal dari model, bukan verifikasi skema database hidup.

| Tabel / kelompok model | Fungsi dan relasi penting |
| --- | --- |
| `ticket_menu`, `ticket_sub_menu`, `ticket_kondisi` | Modul, jenis perubahan, kondisi |
| `ticket_hub_menus`, `ticket_hub_menu_approvals` | Hub layanan dan pemetaan approval group per holding |
| `ticket_approval_flow`, `ticket_approval_step`, `ticket_approval_karyawan` | Master alur/step dan pihak |
| `ticket_dependency_rule` | Hubungan sumber/tujuan dan aturan kondisi/arah |
| `ticket_approval_validator`, `ticket_assignment_master`, `ticket_difficulty_master` | Validator, assignor/PIC, target kesulitan |
| `ticket_head` | Identitas, status, pihak, tanda tangan, lineage, waktu dan ringkasan KPI tiket |
| `ticket_detail`, `ticket_sub_detail` | Transaksi dan baris perubahan; reference detail pada tiket turunan |
| `ticket_head_attachment` | Metadata lampiran tiket |
| `ticket_approvals` | Checker/approval dinamis, dependency rule dan hasil generation |
| `ticket_hasil_cek` | Nilai kondisi beserta pemeriksa/waktu |
| `ticket_approval_comment` | Komentar dan tindakan pihak terkait |
| `ticket_work_sessions` | Ticket type/ID, PIC, nomor siklus, tipe sesi, mulai/selesai, durasi menit |
| `ticket_execution_log` | Model log eksekusi; kelengkapan pemakaian belum diverifikasi |
| `ticket_vendor_head`, `ticket_vendor_detail`, `ticket_vendor_attachment` | Pengajuan vendor dan bukti pendukung |

Model `TicketVendorWorkflowMaster`/`Detail` juga ada, tetapi jalur pengajuan yang diperiksa mengambil konfigurasi dari `ApprovalGroup`/`Approval` dan menyimpan pihak approval pada header vendor; keberadaan model workflow tidak dianggap bukti jalur aktif tambahan.

## 6. Pemetaan endpoint utama

Path di bawah sesuai deklarasi route yang diperiksa; parameter menu navigasi tidak selalu sama dengan ID master modul. Tabel merupakan daftar jalur utama, bukan seluruh endpoint AJAX.

| HTTP / path | Handler | Fungsi |
| --- | --- | --- |
| GET `ticket/hub/{menu_id}` | S02 `hub` | Hub layanan |
| POST `ticket/simpan_ticket` | S02 `ticketHead_store` | Pengajuan perubahan |
| POST `ticket/update_ticket/{id}` | S02 `ticketHead_update` | Ubah/ajukan draft |
| GET `ticket/tambah_generated/draft/{draftTicketId}` | S02 `ticketHead_addGeneratedDraft` | Lengkapi generated draft |
| POST `ticket/simpan_generated` | S02 `ticketHead_storeGenerated` | Submit tiket turunan |
| POST `ticket/requestor-action/{ticketId}` | S02 `requestorActionTicket` | Feedback/close |
| POST `approval/ticket/save-approve/{ticketId}` | S03 `saveApproveTicket` | Keputusan approver; handler membaca `id_ticket` dari body |
| POST `approval/ticket/checker/save/{ticketId}` | S03 `saveCheckerTicket` | Pemeriksaan |
| POST `approval/ticket/checker/generate/{approvalId}` | S03 `retryGenerateApproval` | Retry generation |
| POST `approval/ticket/validator/save/{ticketId}` | S03 `saveValidatorTicket` | Validasi individual |
| POST `approval/ticket/ticket/validator/{menuId}/group-save` | S03 `saveValidatorGroupTicket` | Path kelompok mengandung `ticket` dua kali sesuai deklarasi saat ini |
| POST `approval/ticket/assign/store` | S03 `storeAssignTicket` | Assignment |
| POST `approval/ticket/pic/status/{ticketId}` | S03 `updatePicTicketStatus` | Aksi PIC |
| GET `ticket/export/{id}` | S02 `ticketExport` | PDF tiket |
| POST `ticket/vendor/store` | S02 `ticketVendorStore` | Pengajuan vendor |
| POST `approval/ticket/vendor/save/{vendorId}` | S03 `saveVendorApproval` | Approval vendor |
| POST `master_ticket/report/cancel-generated-draft/{id}` | S04 `cancelGeneratedDraft` | Pembatalan draft turunan |
| GET `master_ticket/report/data/list` | S04 `getListTicketReport` | Laporan admin |
| GET `ticket/report/kpi/...` | S04 method KPI | Dashboard/data KPI admin |
| GET `approval/ticket/kpi/...` | S08 controller KPI | Dashboard/data KPI PIC |

Kontrak respons belum seragam: terdapat JSON `code`, `message`, `errors`, `data`, `redirect_url` atau `redirect`, dan sebagian error hanya memakai `code` dalam body tanpa HTTP status yang sama. Integrasi/frontend harus mengikuti handler aktual; penyelarasan kontrak memerlukan perubahan aplikasi tersendiri.

## 7. Penyimpanan dan dependensi teknis

- Lampiran dan tanda tangan memakai Laravel storage; vendor menggunakan direktori `vendor-attachments` pada disk public. Akses berkas perlu diuji sesuai kebijakan data internal.
- PDF bergantung pada Dompdf dan view ekspor; email perubahan menggunakan mailer `ticket`, email vendor menggunakan `hrd`.
- Terdapat query PostgreSQL-specific, misalnya cast `::text` pada recovery generated ticket. Dukungan lintas database tidak diasumsikan.
- Migration yang ditemukan mencakup approval group hub, tabel pemetaan approval hub, serta difficulty master. Tidak ditemukan migration pembentukan `ticket_head`, `ticket_work_sessions`, dan sejumlah tabel inti Ticketing dalam direktori migration yang diperiksa. Skema operasional mungkin berasal dari sumber lain dan perlu direkonsiliasi.
- Belum ditemukan suite pengujian khusus Ticketing pada direktori `tests` yang ditinjau.

## 8. Ketertelusuran kebutuhan

| BRD | PRD | FRD | Milestone | UAT utama |
| --- | --- | --- | --- | --- |
| BR-01 | PR-01 | FR-01 | M01 | UAT-01 |
| BR-02 | PR-02 | FR-02 | M02 | UAT-02 |
| BR-03 | PR-03 | FR-03 | M03 | UAT-03 |
| BR-04 | PR-04 | FR-04 | M04 | UAT-04/05 |
| BR-05 | PR-05 | FR-05 | M05 | UAT-06 |
| BR-06 | PR-06 | FR-06 | M06 | UAT-07/08 |
| BR-07 | PR-07 | FR-07 | M06 | UAT-08/09 |
| BR-08 | PR-08 | FR-08 | M08 | UAT-10/11 |
| BR-09 | PR-09 | FR-09 | M09 | UAT-12 |
| BR-10 | PR-10 | FR-10 | M07 | UAT-13 |

## 9. Verifikasi yang dilakukan

Pemeriksaan statis meliputi registrasi route dan keberadaan handler, validasi input, transisi status, perhitungan sesi/KPI, konfigurasi notifikasi, hubungan model, form utama, dan migration/seeder terkait. Tidak menjalankan transaksi bisnis, migration, seeder, queue, pengiriman email, atau UAT. Skenario penerimaan yang perlu dijalankan terdapat di PRD.

## 10. Temuan dan batasan baseline

Temuan berikut merupakan hasil membaca kode; dampak runtime perlu direproduksi pada lingkungan uji. Prioritas adalah usulan tindak lanjut, bukan hasil triase tim yang sudah disepakati. Dokumen ini tidak memperbaiki kode aplikasi.

| ID | Bukti dan kondisi | Dampak / tindak lanjut | Prioritas usulan |
| --- | --- | --- | --- |
| GAP-01 | S03 `saveValidatorTicket()` memakai `$rootId` sebelum didefinisikan | Perbaiki dan uji validasi individual, termasuk kelompok | Tinggi |
| GAP-02 | S08 menjumlahkan menit ke atribut bernama `actual_hours`; master S04 memakai label menit, default/label lain memakai hours | Sepakati satuan, selaraskan default/label/perhitungan dan data lama; rekonsiliasi KPI sebelum dipakai resmi | Tinggi |
| GAP-03 | S02 `requestorActionTicket()` mengakses `$ticket` pada cabang feedback kosong sebelum query tiket | Pastikan feedback kosong menghasilkan validasi yang benar, bukan kesalahan runtime | Tinggi |
| GAP-04 | S02 `ticketHead_update()` memakai `$user_karyawan` dalam closure tanpa capture; lampiran divalidasi sebagai array tetapi ada pemanggilan `store()` seperti file tunggal; rule 20 MB berbeda dari pesan/UI 5 MB | Perbaiki jalur edit draft dan satukan kontrak lampiran; uji tanpa/dengan banyak lampiran dan auto-validation | Tinggi |
| GAP-05 | Route menunjuk `ticketHead_destroy`, `get_ticket_kategori`, `get_history_ticket` pada S02 dan `listMenuApproval` pada S04, tetapi method tidak ditemukan; `datatableKondisi` memerlukan argumen kategori/holding yang tidak ada di path route | Lengkapi/hapus route yang tidak digunakan dan uji semua endpoint terkait; fitur tersebut belum dapat dinyatakan operasional | Tinggi |
| GAP-06 | Migration inti Ticketing tidak lengkap pada direktori yang ditinjau; service menulis field pending; beberapa query memakai PostgreSQL-specific cast | Cocokkan skema aktual, buat prosedur instalasi/upgrade yang dapat diulang pada database tujuan | Tinggi |
| GAP-07 | Form difficulty menerima kode bebas, assignment hanya P01–P04; T14 belum ada pada sejumlah mapping/ringkasan PIC | Selaraskan pilihan kesulitan, label pending, dan definisi tiket aktif | Sedang |
| GAP-08 | Store vendor belum memanggil email awal, validator belum memvalidasi file `documents.*`, input `accurate_sourching` berbeda dari field simpan `accurate_sourcing`; header menyimpan holding null dan tidak mengisi `menu_id` pada create yang diperiksa | Uji pengajuan/approval lengkap, redirect, data konteks, file, dan notifikasi vendor; lengkapi bagian yang diperlukan | Tinggi |
| GAP-09 | Pemeriksaan peran/ownership berbeda antarhandler; misalnya pembatalan draft admin secara eksplisit memeriksa login, sedangkan detail/PDF/edit memakai query ID pada jalur terkait | Audit dan uji otorisasi URL langsung, akses berkas, serta isolasi data per pengguna/holding sebelum penerimaan | Tinggi |
| GAP-10 | Pembuatan tiket utama melibatkan beberapa write tanpa transaksi menyeluruh pada `ticketHead_store`; respons error antarhandler tidak seragam; belum ada suite Ticketing khusus yang ditemukan | Uji rollback/duplikasi/aksi bersamaan, konsistensi respons, dan tambahkan cakupan regresi pada jalur kritis | Sedang |

Penyelesaian dan bukti pengujian setiap temuan dikelola melalui M10–M12 pada [Milestone](MILESTONE.md).
