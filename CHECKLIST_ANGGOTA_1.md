# CHECKLIST PENGERJAAN ANGGOTA 1
## EventModule + Auth/RBAC — Sistem Manajemen Event & Tiket

**Pemilik:** Anggota 1 (A)  
**Acuan:** `ROADMAP_DAN_DOKUMENTASI_UTS_BACKEND_EVENT_TIKET_V4_ROADMAP_TIM.md` + `PANDUAN_PENGERJAAN_ANGGOTA_1.md`  
**Legenda:** `[x]` selesai · `[ ]` belum · `[~]` sedang dikerjakan  
**Update terakhir:** 16 November 2026

---

# 0. STATUS SAAT INI

## 0.1 Sudah selesai

- [x] PHP **8.4.23** terpasang lokal (`~/.local/opt/php-8.4.23`)
- [x] Composer **2.11.2** (`~/.local/bin/composer`)
- [x] Node.js **24.22.3** + npm 11.19.1
- [x] Laravel **13.42.0** terpasang di root proyek
- [x] Laravel Sanctum **4.4.0**
- [x] Laravel Boost (dev tool)
- [x] `.env` diarahkan ke MySQL `uts_event_ticket`
- [x] Script `scripts/create-database.sql`
- [x] Kerangka 3 modul + 3 Service Provider terdaftar
- [x] `php artisan serve` → HTTP 200
- [x] `php artisan test` → 2 passed
- [x] Pint → passed
- [x] `npm install` + `npm run build` → sukses
- [x] `php artisan storage:link`
- [x] Git repo diinisialisasi (`main`)

## 0.2 Blocker saat ini

- [ ] **Buat database + user MySQL** (butuh sudo):
  ```bash
  cd /home/panjiprasetya/Documents/app-ticketing
  sudo mysql < scripts/create-database.sql
  ```
- [ ] **Set identitas Git**:
  ```bash
  git config --global user.name "Nama Kamu"
  git config --global user.email "email@contoh.com"
  ```
- [ ] **Commit awal** setelah identitas Git diisi.
- [ ] **Buat repo GitHub public** dan push `main`.

## 0.3 Verifikasi cepat setelah blocker selesai

- [ ] `mysql -h 127.0.0.1 -u ticket_app -pticket_app -e "SELECT VERSION();"` berhasil.
- [ ] `php artisan migrate:status` tidak error koneksi.
- [ ] `git config --global user.name` dan `user.email` terisi.
- [ ] `git log --oneline` menampilkan commit awal.

---

# 1. F0 — ADMINISTRASI & SCOPE

- [ ] Konfirmasi Studi Kasus 5 tidak dipakai kelompok lain.
- [ ] Catat deadline dari dashboard + simpan screenshot.
- [ ] Data anggota lengkap: nama, NIM, kelas.
- [ ] Board tugas dibuat: `Backlog → Ready → In Progress → Review → Done`.
- [ ] Repo GitHub **public** dibuat dan bisa diakses semua anggota.
- [ ] `docs/decisions/001-architecture.md` ditulis.
- [ ] ADR memuat: stack, UUID, Audit5, integer rupiah, status event, arti checkout simulasi, nama Contract.
- [ ] Aturan `isPurchasable` disepakati: `published` dan `now() < end_time`.
- [ ] Branch/commit convention disepakati.
- [ ] Pembagian tugas A/B/C disetujui bertiga.

**Selesai jika:** semua keputusan tertulis dan disetujui; tidak ada ambiguitas aturan.

**Bukti:** link repo, screenshot board, file ADR, daftar anggota.

---

# 2. F1 — SETUP & REPO (SISA)

- [ ] Jalankan script MySQL.
- [ ] Test koneksi MySQL dengan user `ticket_app`.
- [ ] Set identitas Git.
- [ ] `git add .`
- [ ] Commit awal: `chore: initialize Laravel 13 project with module skeleton`
- [ ] Buat repo GitHub public.
- [ ] `git remote add origin ...`
- [ ] `git push -u origin main`
- [ ] Tulis `README.md` awal:
  - [ ] Nama proyek + ringkasan studi kasus
  - [ ] Requirement PHP 8.4+, Composer, Node, MySQL
  - [ ] Cara clone + install
  - [ ] Cara buat DB + `.env`
  - [ ] Cara `migrate --seed`, `npm run build`, `php artisan serve`
  - [ ] Cara test + Pint
  - [ ] Daftar modul + akun demo
  - [ ] Catatan checkout = simulasi
- [ ] Screenshot struktur `app/Modules`.
- [ ] Screenshot 3 provider + `bootstrap/providers.php`.
- [ ] Screenshot `php artisan route:list`.
- [ ] Screenshot `php artisan about`.
- [ ] Pastikan `.env` tidak masuk Git.
- [ ] Pastikan `vendor/`, `node_modules/`, `public/build/` tidak masuk Git.

**Selesai jika:** semua anggota bisa clone, install, dan menjalankan proyek; provider/routes terdeteksi.

---

# 3. F2 — DATABASE USERS, CATEGORIES, EVENTS

## 3.1 Migration

- [ ] Edit migration `users`:
  - [ ] `id` UUID primary
  - [ ] `role` default `peserta`
  - [ ] `created_by` UUID nullable
  - [ ] `updated_by` UUID nullable
- [ ] Ubah `sessions.user_id` menjadi `foreignUuid`.
- [ ] Ubah `personal_access_tokens` menjadi `uuidMorphs('tokenable')`.
- [ ] Migration `categories`:
  - [ ] UUID + Audit5
  - [ ] `slug` unique
- [ ] Migration `events`:
  - [ ] UUID + Audit5
  - [ ] FK `category_id`
  - [ ] `slug` unique
  - [ ] index `(status, start_time)`
- [ ] Urutan migrasi benar: users → categories → events.

## 3.2 Model

- [ ] `User`: `HasUuids`, `HasApiTokens`, `HasFactory`, `Notifiable`.
- [ ] `User`: `role` tidak masuk `fillable`.
- [ ] `User`: helper `isAdmin()`.
- [ ] Trait `HasAudit`:
  - [ ] isi `created_by`/`updated_by` saat create
  - [ ] isi `updated_by` saat update
  - [ ] `created_by` tidak berubah saat update
- [ ] `Category`: `HasUuids`, `HasAudit`, `HasFactory`, `hasMany(Event)`.
- [ ] `Event`: `HasUuids`, `HasAudit`, `HasFactory`, `belongsTo(Category)`, `scopePublished`.
- [ ] Tidak ada `Event::tickets()` di EventModule.

## 3.3 Factory & Seeder

- [ ] `UserFactory` + state `admin()`.
- [ ] `CategoryFactory`.
- [ ] `EventFactory` + state `published()`, `draft()`, `cancelled()`.
- [ ] Seeder: 1 admin, 2 peserta.
- [ ] Seeder: 3 kategori.
- [ ] Seeder: 5 event (3 published, 1 draft, 1 cancelled).
- [ ] Urutan `DatabaseSeeder` benar.
- [ ] Password akun demo memakai hash.

## 3.4 Verifikasi

- [ ] `php artisan migrate:fresh --seed` sukses.
- [ ] Semua PK/FK berupa UUID.
- [ ] `created_by/updated_by` terisi saat create/update.
- [ ] Seeder guest/system boleh `NULL`.
- [ ] Test audit create/update event.
- [ ] Screenshot ERD bagian users/categories/events.
- [ ] Screenshot hasil seeder.
- [ ] PR `feat/uuid-database` direview dan merge.

**Selesai jika:** `migrate:fresh --seed` sukses; UUID, FK, unique, audit sesuai ERD.

---

# 4. F3 — AUTH & RBAC

## 4.1 Implementasi

- [ ] Route register/login/logout.
- [ ] `RegisterRequest` + `LoginRequest`.
- [ ] RegisterController:
  - [ ] role default `peserta`
  - [ ] role dari request diabaikan
  - [ ] login otomatis setelah register
  - [ ] session regenerate
- [ ] LoginController:
  - [ ] `Auth::attempt`
  - [ ] session regenerate
  - [ ] logout invalidate + regenerate token
  - [ ] pesan error tidak membocorkan email terdaftar
- [ ] `RoleMiddleware` + alias `role` di `bootstrap/app.php`.
- [ ] View login/register + layout.
- [ ] `@csrf` di semua form.
- [ ] `@error` di semua field.
- [ ] Navigasi berubah sesuai role.

## 4.2 Test

- [ ] Guest buka `/admin/events` → redirect login.
- [ ] Peserta buka `/admin/events` → 403.
- [ ] Admin buka `/admin/events` → 200.
- [ ] Register baru → role `peserta`.
- [ ] Request `role=admin` saat register → tetap `peserta`.
- [ ] Login/logout bekerja.
- [ ] Logout membuat session invalid.
- [ ] Screenshot 403.
- [ ] Screenshot login/register.
- [ ] PR `feat/auth-sanctum-rbac` direview dan merge.

**Selesai jika:** guest/peserta/admin berperilaku sesuai aturan dan semua test lulus.

---

# 5. F4 — EVENTMODULE

## 5.1 Route

- [ ] `GET /events` publik.
- [ ] `GET /events/{slug}` publik.
- [ ] Resource `/admin/categories` → `role:admin`.
- [ ] Resource `/admin/events` → `role:admin`.
- [ ] `PATCH /admin/events/{event}/publish`.
- [ ] Nama route konsisten: `events.*`, `admin.categories.*`, `admin.events.*`.
- [ ] Semua route modul di bawah middleware `web`.

## 5.2 Form Request

- [ ] `StoreCategoryRequest` + `UpdateCategoryRequest`.
- [ ] `StoreEventRequest` + `UpdateEventRequest`.
- [ ] Validasi `end_time` `after:start_time`.
- [ ] Validasi slug unik (ignore saat update).
- [ ] Validasi `category_id` `uuid` + `exists`.
- [ ] Validasi status `draft|published|cancelled`.
- [ ] Validasi banner: image, mime, max 2MB.
- [ ] Tidak memakai `$request->all()`.

## 5.3 Controller & View

- [ ] CRUD kategori (index/create/store/edit/update/destroy).
- [ ] CRUD event (index/create/store/edit/update/destroy).
- [ ] Upload banner ke disk `public`.
- [ ] Publish event + cegah event yang sudah berakhir.
- [ ] Hapus banner saat event dihapus.
- [ ] View admin kategori.
- [ ] View admin event.
- [ ] Katalog publik + pagination.
- [ ] Detail event via slug.
- [ ] Index dan show hanya `published`.
- [ ] Tampilkan harga/sisa tiket via Contract B (bukan query tabel `tickets`).
- [ ] Blade escaping aman; tidak ada `{!! !!}` untuk input pengguna.

## 5.4 Contract

- [ ] `EventInfoContract`:
  - [ ] `getSummary(string $eventId): ?array`
  - [ ] `isPurchasable(string $eventId): bool`
- [ ] `EventInfoService` mengembalikan array primitive, bukan model.
- [ ] Binding contract di `EventModuleServiceProvider`.
- [ ] `isPurchasable`: published + belum berakhir.
- [ ] `getSummary`: id, title, slug, category, location, start, end, status.

## 5.5 Test

- [ ] T01 guest lihat event published.
- [ ] T02 guest buka event draft → 404.
- [ ] T05 admin CRUD kategori/event.
- [ ] T06 `end_time <= start_time` → 422.
- [ ] Slug duplikat → 422.
- [ ] Publish event valid.
- [ ] Publish event berakhir → ditolak.
- [ ] Contract summary published.
- [ ] `isPurchasable` draft/cancelled/ended → false.
- [ ] `isPurchasable` published aktif → true.
- [ ] Audit create oleh admin A.
- [ ] Audit update oleh admin B.
- [ ] Screenshot form error.
- [ ] Screenshot katalog + detail.
- [ ] PR `feat/event-crud` + `feat/event-catalog` direview dan merge.

**Selesai jika:** CRUD, publish, validasi, katalog published-only, dan contract lulus test.

---

# 6. F5 — DUKUNGAN CHECKOUT

- [ ] `EventInfoContract` final dan disetujui B/C.
- [ ] Test `isPurchasable` untuk event published/draft/cancelled/ended.
- [ ] Pastikan event published di seeder bisa dipakai checkout.
- [ ] Review `CheckoutRequest` C tidak mengakses tabel `events` langsung.
- [ ] Review `CheckoutService` C memakai `EventInfoContract`.
- [ ] Bantu C menyiapkan data event untuk uji checkout.
- [ ] Review method contract untuk daftar tiket event (jika B menambahkannya).

**Selesai jika:** C bisa memakai contract tanpa direct model/table access.

---

# 7. F6 — KEAMANAN EVENT & ROLE

- [ ] Semua route admin diuji untuk guest/peserta.
- [ ] Event draft/cancelled tidak bisa diakses via URL publik.
- [ ] Event draft/cancelled tidak bisa dibeli.
- [ ] Admin tidak bisa hapus kategori yang masih dipakai event tanpa pesan jelas.
- [ ] Audit create/update event teruji.
- [ ] Review ownership e-ticket peserta lain (milik C).
- [ ] Review peserta tidak bisa membuka data peserta lain.
- [ ] Screenshot 403 dan 404.
- [ ] Catat temuan di board.

**Selesai jika:** tidak ada kebocoran akses lintas role/ownership.

---

# 8. F7 — QUALITY GATE

- [ ] `php artisan test` semua lulus.
- [ ] `./vendor/bin/pint --test` lulus.
- [ ] `php artisan route:list` sesuai aturan akses.
- [ ] `php artisan about` menunjukkan PHP 8.4.
- [ ] Grep boundary:
  ```bash
  rg 'Modules\\(TicketModule|EventModule)\\Models' app/Modules/RegistrationModule
  rg "DB::table\\(['\"](tickets|events)" app/Modules/RegistrationModule
  rg 'Modules\\(TicketModule|RegistrationModule)\\Models' app/Modules/EventModule
  ```
- [ ] Tidak ada import model lintas modul.
- [ ] CSRF aktif di semua form.
- [ ] XSS dicek dengan input `<script>`.
- [ ] Mass assignment dicek.
- [ ] `.env` tidak masuk Git.
- [ ] `APP_DEBUG=false` saat demo publik.
- [ ] Uji manual ujung ke ujung:
  - [ ] Admin login → buat kategori → buat event → publish.
  - [ ] Peserta daftar → login → lihat event → checkout.
  - [ ] Riwayat + e-ticket tampil.
  - [ ] Stok berkurang.
  - [ ] Checkout gagal saat stok habis.
- [ ] Screenshot output test.
- [ ] Screenshot Pint.
- [ ] Screenshot `git log --oneline --graph`.

**Selesai jika:** seluruh test, Pint, boundary, dan uji manual lulus.

---

# 9. F8 — LAPORAN & BUKTI

## 9.1 Screenshot

- [ ] Struktur `app/Modules`.
- [ ] 3 provider + `bootstrap/providers.php`.
- [ ] `route:list`.
- [ ] Migration users/categories/events.
- [ ] ERD.
- [ ] Register/login/logout.
- [ ] 403 peserta ke admin.
- [ ] Middleware role.
- [ ] CRUD kategori.
- [ ] CRUD event.
- [ ] Validation error.
- [ ] Publish event.
- [ ] Katalog publik.
- [ ] Detail event.
- [ ] `EventInfoContract` + binding.
- [ ] Output test.
- [ ] Output Pint.
- [ ] `git log --oneline --graph --all --decorate`.

## 9.2 Laporan

- [ ] BAB I — Pendahuluan (penyusun utama).
- [ ] BAB II 2.1 — Bounded context.
- [ ] BAB II 2.5 — Matriks hak akses.
- [ ] BAB III 3.2 — Struktur modular + provider.
- [ ] BAB III 3.5 — Auth/RBAC.
- [ ] BAB III 3.6 — Form Request + error event.
- [ ] BAB IV — Test auth/event + screenshot.
- [ ] BAB V — ditulis bersama.
- [ ] Tabel pembagian tugas + peer assessment.
- [ ] Review silang dengan B/C.

**Selesai jika:** draft bagian A lengkap dan screenshot sesuai implementasi nyata.

---

# 10. F9 — DEMO & SUBMIT

- [ ] Video intro 0:00–0:45.
- [ ] Video arsitektur 0:45–2:00.
- [ ] Video auth & admin 3:20–4:50.
- [ ] Video penutup 9:20–10:00.
- [ ] Durasi total ≤ 10 menit.
- [ ] Audio jelas, tidak ada password/token terlihat.
- [ ] Repo GitHub public.
- [ ] README final + cara menjalankan.
- [ ] `.env.example` final.
- [ ] Semua tautan di laporan aktif.
- [ ] PDF bernama `UTS_BackendDevelopment_Kelas_NamaKelompok.pdf`.
- [ ] PDF + video + link GitHub siap.
- [ ] Upload sebelum tenggat + simpan bukti.

**Selesai jika:** repo, video, PDF, dan tautan terverifikasi.

---

# 11. HANDOFF DENGAN B & C

## Yang A serahkan

- [ ] Struktur tabel `users`, `categories`, `events`.
- [ ] Trait `HasAudit` + aturan pengisian audit.
- [ ] Daftar event + `event_id` untuk seeder tiket B.
- [ ] `EventInfoContract` final + contoh pemanggilan.
- [ ] Route katalog + akun demo.
- [ ] Matriks akses guest/peserta/admin.
- [ ] Screenshot auth/event untuk laporan.

## Yang A butuhkan

- [ ] Dari B: schema `tickets` + `TicketInventoryContract` untuk halaman detail event.
- [ ] Dari B: method daftar tiket per event (jika dipakai).
- [ ] Dari C: hasil uji ownership e-ticket.
- [ ] Dari C: hasil uji checkout untuk memastikan aturan event benar.

---

# 12. DEFINITION OF DONE ANGGOTA 1

- [ ] Auth Sanctum + session berjalan.
- [ ] RBAC guest/peserta/admin teruji.
- [ ] CRUD kategori dan event berjalan.
- [ ] Semua form memakai Form Request.
- [ ] Katalog publik hanya event published.
- [ ] `EventInfoContract` + service + binding teruji.
- [ ] UUID + Audit5 pada tabel milik A.
- [ ] Tidak ada direct model/table access lintas modul.
- [ ] Feature test auth/event/contract lulus.
- [ ] Pint lulus.
- [ ] Screenshot bukti lengkap.
- [ ] BAB I + bagian BAB II/III selesai.
- [ ] Segmen video selesai.
- [ ] PR direview dan merge.
- [ ] Repo public + README aman.

---

# 13. TARGET HARIAN

| Hari | Target A | Status |
|---|---|---|
| H1 | F0 + F1 setup, DB, Git, README | `[~]` |
| H2 | F2 migration users/categories/events + seeder | `[ ]` |
| H3 | F3 auth + RBAC + test | `[ ]` |
| H4 | F4 CRUD event/kategori + katalog | `[ ]` |
| H5 | F4 contract + test + review checkout C | `[ ]` |
| H6 | F6 keamanan event/role + audit | `[ ]` |
| H7 | Buffer / bantu integrasi | `[ ]` |
| H8 | F7 quality gate + perbaikan | `[ ]` |
| H9 | F8 laporan + screenshot | `[ ]` |
| H10 | F9 video + submit | `[ ]` |

---

# 14. PERINTAH CEPAT

```bash
# buka proyek
cd /home/panjiprasetya/Documents/app-ticketing

# jika terminal lama
source ~/.bashrc

# database (sekali saja)
sudo mysql < scripts/create-database.sql
mysql -h 127.0.0.1 -u ticket_app -pticket_app -e "SELECT VERSION();"

# jalankan aplikasi
php artisan serve
npm run dev

# database
php artisan migrate:fresh --seed
php artisan migrate:status

# kualitas
php artisan test
./vendor/bin/pint --test
./vendor/bin/pint
php artisan route:list
php artisan about

# git
git status
git add .
git commit -m "feat(scope): pesan jelas"
git push -u origin main

# boundary
rg 'Modules\\(TicketModule|EventModule)\\Models' app/Modules/RegistrationModule
rg "DB::table\\(['\"](tickets|events)" app/Modules/RegistrationModule
```

---

**Cara pakai:** centang dari atas ke bawah. Jangan centang item yang belum benar-benar terbukti di kode, test, atau screenshot. Jika ada item yang berubah karena keputusan tim, catat perubahannya di ADR.
