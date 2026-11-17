# CHECKLIST PENGERJAAN ANGGOTA 2
## TicketModule + Koordinator Skema & Database — Sistem Manajemen Event & Tiket

**Pemilik:** Anggota 2 (B) — Muhammad Imam Panji Prasetya (`panjiprasetyaa`)  
**Acuan:** `ROADMAP_DAN_DOKUMENTASI_UTS_BACKEND_EVENT_TIKET_V4_ROADMAP_TIM.md` + `PANDUAN_PENGERJAAN_ANGGOTA_2.md`  
**Legenda:** `[x]` Selesai · `[ ]` Perlu Verifikasi Manual/Tangkapan Layar  
**Update Terakhir:** 17 November 2026

---

## 0. STATUS SAAT INI

- [x] Lingkungan lokal terverifikasi: PHP 8.4.19, Composer 2.9.5, Git 2.54.0.
- [x] Repositori `app-ticketing` berhasil diklon di `d:\uts BE\app-ticketing`.
- [x] Branch kerja pribadi `feat/anggota-2-ticket-and-database` aktif.
- [x] Git author lokal terkonfigurasi atas nama `panjiprasetya`.
- [x] Kredensial SSH key pair berhasil dibuat (`C:\Users\ASUS\.ssh\id_ed25519.pub`).
- [x] Seluruh 80 automated tests awal lulus 100%.
- [x] Standar kode lolos Laravel Pint (`./vendor/bin/pint --test`).

---

## 1. FASE F2: KOORDINATOR SKEMA DATABASE UUID & AUDIT5

- [x] Verifikasi skema tabel `tickets` menggunakan UUID sebagai Primary Key.
- [x] Verifikasi relasi One-to-Many: `events` (1) $\rightarrow$ `tickets` (N) dengan `foreignUuid('event_id')`.
- [x] Verifikasi 5 kolom audit (`[AUDIT5]`): `id`, `created_at`, `created_by`, `updated_at`, `updated_by` pada tabel `tickets`.
- [x] Verifikasi tabel pivot Many-to-Many `registration_tickets` dengan atribut `quantity`, `unit_price_rupiah`, `subtotal_rupiah`, dan UUID audit.
- [x] Menyusun dokumen kamus data dan diagram ERD formal di `docs/DATA_DICTIONARY_DAN_ERD.md`.
- [ ] **Tangkapan Layar Bukti:**
  - [ ] Screenshot `php artisan migrate:status` $\rightarrow$ simpan ke `docs/evidence/02-database/02-01-migration-status.png`.
  - [ ] Screenshot `php artisan migrate:fresh --seed` $\rightarrow$ simpan ke `docs/evidence/02-database/02-02-seeder-execution.png`.
  - [ ] Screenshot tabel database di GUI/SQLite viewer $\rightarrow$ simpan ke `docs/evidence/02-database/02-03-database-tables-erd.png`.

---

## 2. FASE F5: TICKETMODULE (LOGIKA BISNIS & KONTRAK)

- [x] Validasi input form penambahan tiket (`StoreTicketRequest`):
  - `event_id`: valid UUID dan terdaftar di tabel `events`.
  - `name`: string wajib diisi.
  - `price_rupiah`: integer non-negatif (`min:0`).
  - `initial_quota`: integer minimal 1.
  - `sales_start` & `sales_end`: tanggal valid dengan aturan `sales_end >= sales_start`.
- [x] Validasi form edit tiket (`UpdateTicketRequest`):
  - Mencegah kuota baru disetel lebih kecil dari jumlah tiket yang sudah terjual.
- [x] Interface `TicketInventoryContract` terdaftar dan di-bind di `TicketModuleServiceProvider`.
- [x] Operasi pemotongan stok `TicketInventoryService::reserve()`:
  - Memeriksa transaksi aktif (`DB::transactionLevel() > 0`).
  - Mengambil lock baris eksklusif (`lockForUpdate()`).
  - Memvalidasi status tiket `active` dan jendela penjualan `isOnSale()`.
  - Melempar `TicketQuotaExceededException` jika kuota kurang.
  - Men-stamp `updated_by` secara server-side dari `Auth::id()`.
- [x] Operasi pengembalian stok `TicketInventoryService::release()`:
  - Mengembalikan kuota secara atomik tanpa melebihi `initial_quota`.
- [x] Tampilan antarmuka Blade admin tiket (`/admin/tickets`):
  - Desain bersih neutral Slate/Zinc sesuai `design-taste-frontend`.
  - Tombol tactile feedback dengan `:active:scale-[0.98]` sesuai `emil-design-eng`.
  - Badge sisa kuota informatif (hijau untuk aman, kuning untuk menipis, merah untuk habis).
  - Empty state yang komunikatif saat belum ada tiket.
- [ ] **Tangkapan Layar Bukti:**
  - [ ] Screenshot daftar tiket admin $\rightarrow$ simpan ke `docs/evidence/05-ticket/05-01-admin-ticket-index.png`.
  - [ ] Screenshot form tambah tiket $\rightarrow$ simpan ke `docs/evidence/05-ticket/05-02-admin-ticket-create.png`.
  - [ ] Screenshot form edit kuota tiket $\rightarrow$ simpan ke `docs/evidence/05-ticket/05-03-admin-ticket-edit.png`.

---

## 3. FASE F7: QUALITY GATE & CONCURRENCY AUDIT

- [x] Menjalankan test suite modul tiket:
  ```powershell
  php artisan test tests/Feature/TicketModule
  ```
  *(Status: 2 test files, 15 assertions passed).*
- [x] Menjalankan seluruh test suite proyek:
  ```powershell
  php artisan test
  ```
  *(Status: 80 tests passed, 225 assertions).*
- [x] Menjalankan pemformat kode Laravel Pint:
  ```powershell
  ./vendor/bin/pint --test
  ```
  *(Status: Passed).*
- [x] Memastikan tidak ada ketergantungan langsung ke model modul lain (zero direct Eloquent cross-module access).

---

## 4. FASE F8: LAPORAN UTS & DOKUMENTASI BUKTI

- [x] Menyusun draf **BAB II (Perancangan ERD & Data Dictionary)** di `docs/DRAF_LAPORAN_BAB2_BAB3_ANGGOTA_2.md`.
- [x] Menyusun draf **BAB III (Implementasi Database & TicketModule)** di `docs/DRAF_LAPORAN_BAB2_BAB3_ANGGOTA_2.md`.
- [x] Menyediakan panduan tangkapan layar di:
  - `docs/evidence/02-database/README.md`
  - `docs/evidence/05-ticket/README.md`

---

## 5. HANDOFF KE ANGGOTA 3 (SIAP DILANJUTKAN)

- [x] `TicketInventoryContract` stabil dan terverifikasi untuk dipanggil oleh `CheckoutService` milik Anggota 3.
- [x] Custom Exception `TicketQuotaExceededException` terbukti membatalkan transaksi atomik dan me-rollback perubahan.
- [x] Tabel pivot `registration_tickets` dan tabel `issued_tickets` siap menerima data pesanan peserta dari Anggota 3.
