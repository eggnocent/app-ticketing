# Event Ticket UTS

Aplikasi web manajemen event dan penjualan tiket berbasis **Laravel 13**, **PHP 8.4+**, **Blade**, **MySQL**, dan **Laravel Sanctum**. Proyek ini disusun untuk UTS Backend Development (DT199) dengan arsitektur **Modular Monolith**: `EventModule`, `TicketModule`, dan `RegistrationModule`.

> **Status:** tahap setup (F1). Fitur inti dan data demo ditambahkan pada fase berikutnya.

## Studi Kasus

Sistem manajemen event dan tiket dengan dua peran:

- **Admin** — mengelola kategori, event, jenis tiket, kuota, dan melihat rekap pendaftaran.
- **Peserta** — melihat event published, memilih tiket, checkout simulasi, dan melihat e-ticket miliknya.

Checkout pada proyek ini adalah **simulasi pembelian sukses**, bukan integrasi payment gateway nyata.

## Stack

| Komponen | Versi |
|---|---|
| PHP | 8.4+ |
| Laravel | 13.x |
| Database | MySQL 8 |
| Frontend | Blade + Tailwind CSS + Vite |
| Auth | Laravel Sanctum + session |
| Testing | PHPUnit |
| Formatter | Laravel Pint |

## Requirement

- PHP 8.4 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `fileinfo`, `zip`.
- Composer 2.
- Node.js 20.19+ (disarankan 22/24 LTS) dan npm.
- MySQL 8.
- Git.

## Instalasi

```bash
git clone https://github.com/panjiprasetyaa/app-ticketing.git
cd app-ticketing
composer install
cp .env.example .env
php artisan key:generate
```

Buat database dan user lokal:

```bash
sudo mysql < scripts/create-database.sql
```

Sesuaikan `DB_*` di `.env` bila perlu, lalu:

```bash
php artisan migrate
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

> `php artisan migrate:fresh --seed` hanya untuk database development/testing karena menghapus seluruh data.

## Struktur Modul

```text
app/Modules/
├── EventModule/          # kategori, event, katalog, EventInfoContract
├── TicketModule/         # jenis tiket, harga, kuota, TicketInventoryContract
└── RegistrationModule/   # checkout, riwayat, e-ticket, issued tickets
```

Setiap modul memiliki Service Provider sendiri dan didaftarkan di `bootstrap/providers.php`.

## Akun Demo

Seeder akun demo ditambahkan pada fase F2. Rencana akun:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.test` | `password` |
| Peserta | `peserta1@example.test` | `password` |
| Peserta | `peserta2@example.test` | `password` |

**Jangan memakai password ini di lingkungan produksi.**

## Testing dan Kualitas Kode

```bash
php artisan test
./vendor/bin/pint --test
php artisan route:list
```

## Dokumentasi

- [Roadmap & dokumentasi lengkap](ROADMAP_DAN_DOKUMENTASI_UTS_BACKEND_EVENT_TIKET_V4_ROADMAP_TIM.md)
- [Panduan pengerjaan Anggota 1](PANDUAN_PENGERJAAN_ANGGOTA_1.md)
- [Checklist Anggota 1](CHECKLIST_ANGGOTA_1.md)

## Keamanan

- `.env` tidak di-commit.
- Jangan menaruh password, token, atau kredensial produksi di repository.
- Data seeder bersifat fiktif.
- Gunakan `APP_DEBUG=false` saat demo publik.

## Lisensi

Proyek ini dibuat untuk keperluan akademik. Lisensi mengikuti komponen open-source yang digunakan.
