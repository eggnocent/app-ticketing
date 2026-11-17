# Panduan Bukti Screenshot: Database Foundation & ERD (Fase F2)
## Porsi Laporan BAB II & BAB III — Anggota 2 (Koordinator Skema & Database)

Folder ini digunakan untuk menampung berkas tangkapan layar (*screenshot*) yang menjadi bukti fisik pada **BAB II (Perancangan ERD)** dan **BAB III (Implementasi Database)** dalam Laporan UTS Backend Development (DT199).

---

## 1. Daftar Screenshot Wajib

| Nama Berkas | Konten Tangkapan Layar | Perintah / Halaman Sumber | Lokasi di Laporan UTS |
|---|---|---|---|
| `02-01-migration-status.png` | Output terminal daftar migrasi berstatus `Ran` | `php artisan migrate:status` | BAB III — Subbab 3.2 (Manajemen Database) |
| `02-02-seeder-execution.png` | Output terminal seeder berhasil mengisi data dummy | `php artisan migrate:fresh --seed` | BAB III — Subbab 3.2 (Seeder & Factory) |
| `02-03-database-tables-erd.png` | Struktur 8 tabel UUID di GUI database (DBeaver / TablePlus / phpMyAdmin / DB Browser SQLite) | GUI Database Client | BAB II — Subbab 2.2 (Entity Relationship Diagram) |
| `02-04-pivot-table-registration-tickets.png` | Isi tabel pivot `registration_tickets` dengan snapshot harga | GUI Database / `SELECT * FROM registration_tickets` | BAB III — Subbab 3.3 (Relasi Many-to-Many Pivot) |

---

## 2. Cara Mengambil Screenshot yang Berkualitas (Anti-Slop Standard)

1. **Resolusi Tajam:** Pastikan teks terminal dan tabel terlihat jelas tanpa blur atau resolusi pecah.
2. **Konteks Nyata:** Tampilkan baris prompt terminal atau header nama database (`uts_event_ticket` atau `database.sqlite`).
3. **Format Berkas:** Simpan dalam format `.png` dengan nama persis sesuai tabel di atas.
