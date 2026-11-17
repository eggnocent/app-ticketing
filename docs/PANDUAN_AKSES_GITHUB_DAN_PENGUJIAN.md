# Panduan Akses GitHub, Verifikasi PR #1, dan Pengujian UTS Backend

Dokumen ini disusun untuk **Muhammad Imam Panji Prasetya** (`panjiprasetyaa`) sebagai panduan operasional mengelola repository [panjiprasetyaa/app-ticketing](https://github.com/panjiprasetyaa/app-ticketing), menghubungkan autentikasi GitHub, memverifikasi Pull Request #1 dari rekan tim (`eggnocent`), menjalankan aplikasi, dan mengumpulkan bukti pengujian untuk Laporan UTS Backend Development (DT199).

---

## 1. Status Terkini Repository & Pull Request #1

- **Lokasi Project Lokal:** `d:\uts BE\app-ticketing\`
- **Branch Aktif:** `main`
- **Status Penggabungan (Merge):**
  Pull Request #1 (`feat - working for p1.5-p16`) dari `eggnocent` telah berhasil di-fetch ke branch lokal `pr-1-eggnocent`, diuji, dan di-merge dengan aman ke branch `main`.
- **Hasil Verifikasi Otomatis:**
  - **Automated Tests:** **80 passed**, 225 assertions (0 failed, 0 errors).
  - **Laravel Pint:** **Passed** (100% PSR-12 / Laravel code style).
  - **Database Migration & Seeder:** Berjalan lancar (termasuk 3 modul, relasi 1-N, dan tabel pivot M-N `registration_tickets`).
  - **Vite Asset Build:** Compiled (`manifest.json` terbuat di `public/build/`).

Pohon Git commit lokal saat ini:
```text
*   2086bcb (HEAD -> main) merge: pull request #1 from eggnocent (modular monolith core features)
|\  
| * 5d98e15 (pr-1-eggnocent) feat(registration): add transactional checkout, e-tickets, and recap
| * b5c8e2a feat(ticket): add ticket inventory and admin CRUD
| * f2ccf43 feat(event): add category and event CRUD with public catalog
| * e76dcf5 feat(auth): add session auth, role middleware, and auth views
| * d888170 feat(database): add UUID schema, audit trait, and demo seeders
| * b91364b docs: add architecture decision record and evidence folders
|/  
* 8c5dcdf (origin/main) chore: initialize Laravel 13 project with module skeleton
```

---

## 2. Cara Mengaktifkan Akses Full GitHub (`panjiprasetyaa`)

Agar Anda dapat melakukan `git push`, mengelola pull request, dan mempublikasikan branch `main` hasil merge ke GitHub, ikuti salah satu dari 2 metode berikut:

### Opsi A: Menggunakan SSH Key (Sangat Direkomendasikan)
Kami telah men-generate pasangan SSH Key di laptop Anda:
- File private key: `C:\Users\ASUS\.ssh\id_ed25519`
- File public key: `C:\Users\ASUS\.ssh\id_ed25519.pub`

**Langkah aktivasi:**
1. Salin teks Public Key berikut:
   ```text
   ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIK0rRQ5DSQf3Om8spLvm4yI3Sei/jIbrCgr6uoB8+gIP 243574562+panjiprasetyaa@users.noreply.github.com
   ```
2. Buka browser dan login ke akun GitHub Anda, lalu menuju ke menu:
   👉 **[https://github.com/settings/keys](https://github.com/settings/keys)**
3. Klik tombol hijau **New SSH key**:
   - **Title:** `Laptop ASUS Panji (UTS BE)`
   - **Key type:** `Authentication Key`
   - **Key:** Tempelkan (*paste*) teks public key di atas.
4. Klik **Add SSH key**.
5. Di terminal PowerShell, jalankan perintah pengujian koneksi:
   ```powershell
   ssh -T git@github.com
   ```
   *(Jika muncul pertanyaan `Are you sure you want to continue connecting?`, ketik `yes`. Pesan sukses: `Hi panjiprasetyaa! You've successfully authenticated...`)*
6. Ubah remote URL lokal ke SSH dan push hasil merge:
   ```powershell
   Set-Location "d:\uts BE\app-ticketing"
   git remote set-url origin git@github.com:panjiprasetyaa/app-ticketing.git
   git push origin main
   ```

---

### Opsi B: Menggunakan HTTPS & Git Credential Manager / Personal Access Token
Jika Anda tidak ingin menggunakan SSH:
1. Tetap gunakan URL HTTPS:
   ```powershell
   Set-Location "d:\uts BE\app-ticketing"
   git remote set-url origin https://github.com/panjiprasetyaa/app-ticketing.git
   ```
2. Jalankan perintah push:
   ```powershell
   git push origin main
   ```
3. Windows Git Credential Manager akan membuka jendela browser untuk meminta Anda mengotorisasi login akun GitHub `panjiprasetyaa`. Klik **Authorize GitCredentialManager**.

---

## 3. Menjalankan Aplikasi & Kredensial Akun Demo

Aplikasi menggunakan database SQLite lokal yang sudah siap di `database/database.sqlite`.

### Perintah Menjalankan Server Lokal:
```powershell
Set-Location "d:\uts BE\app-ticketing"
php artisan serve
```
Akses di browser: 👉 **`http://127.0.0.1:8000`**

### Akun Demo (Hasil Seeder):

| Peran (Role) | Email | Password | Hak Akses Utama |
|---|---|---|---|
| **Admin** | `admin@example.test` | `password` | Kelola Kategori, Event, Jenis Tiket & Kuota, Rekap Transaksi Peserta |
| **Peserta 1** | `peserta1@example.test` | `password` | Lihat Event, Beli Tiket (Checkout), Cetak E-Ticket |
| **Peserta 2** | `peserta2@example.test` | `password` | Pemesanan tiket akun kedua |

---

## 4. Alur Pengujian Fitur Utama & Pembuktian Rubrik UTS

Untuk demonstrasi video dan tangkapan layar Laporan UTS BAB IV, jalankan skenario berikut:

### Skenario 1: Uji Autentikasi Sanctum & Otorisasi RBAC (Bobot 10%)
1. Login sebagai `admin@example.test` $\rightarrow$ Buka menu **Admin Dashboard** (`/admin/dashboard`) $\rightarrow$ Akses diterima.
2. Logout, lalu login sebagai `peserta1@example.test`.
3. Coba ketikkan URL admin secara manual: `http://127.0.0.1:8000/admin/events`.
4. **Hasil:** Sistem langsung menolak dengan response **403 Forbidden** (membuktikan `RoleMiddleware` bekerja sempurna).

### Skenario 2: Uji Relasi One-to-Many & Many-to-Many Pivot (Bobot 15%)
1. Login sebagai Admin $\rightarrow$ Buka **Event Management**.
2. Tambah kategori baru (One-to-Many: `Category` $\rightarrow$ `Event`).
3. Tambah event baru, lalu tambahkan beberapa jenis tiket (misal *VIP* dan *Regular*).
4. Relasi Many-to-Many terbukti saat Peserta membeli tiket: transaksi disimpan pada tabel `registrations` dan detail item tiket disimpan pada tabel pivot `registration_tickets` lengkap dengan `unit_price_rupiah` (snapshot harga) dan `ticket_code` (kode unik).

### Skenario 3: Uji `DB::transaction()` & Custom Exception (Bobot 20%: 10% Exception + 10% Transaksi)
1. Sebagai Admin, atur kuota sisa tiket "Early Bird" menjadi **1**.
2. Login sebagai Peserta $\rightarrow$ Pilih tiket "Early Bird", masukkan jumlah pembelian **2**.
3. Klik tombol **Checkout / Bayar**.
4. **Hasil:**
   - Sistem melempar `TicketQuotaExceededException`.
   - Muncul alert merah informatif di halaman: *"Sisa kuota untuk tiket 'Early Bird' hanya 1, tidak mencukupi untuk pesanan sebanyak 2."*
   - **Bukti Rollback:** Buka database, periksa bahwa tidak ada baris data registrasi baru yang tersimpan (transaksi di-rollback 100% bersih).

---

## 5. Pengumpulan Bukti Gambar (Evidence) untuk Laporan UTS

Folder penyimpanan bukti telah dibuat di `d:\uts BE\app-ticketing\docs\evidence\`:

1. `01-architecture/`: Screenshot struktur direktori `app/Modules/` dan file Service Provider.
2. `02-database/`: Screenshot terminal `php artisan migrate:fresh --seed` dan diagram ERD.
3. `03-auth/`: Screenshot halaman login, register, dan halaman `403 Forbidden` saat user biasa mengakses admin.
4. `04-event/`: Screenshot daftar event publik dan form kelola event admin.
5. `05-ticket/`: Screenshot daftar tiket dan modal/form edit kuota tiket.
6. `06-checkout/`: Screenshot halaman checkout, tampilan E-Ticket dengan kode tiket unik, serta screenshot alert rollback `TicketQuotaExceededException`.
7. `07-tests/`: Screenshot terminal `php artisan test` dengan output **80 passed** berwarna hijau.
8. `08-git/`: Screenshot terminal menjalankan `git log --oneline --graph`.
