# Hasil Pengujian Otomatis

Diperbarui: 17 November 2026

Lingkungan utama verifikasi: PHP 8.5.0, Laravel 13.42.0, PHPUnit 12.5, MariaDB 10.11.14. Database test berjalan pada instance MariaDB sementara di port 3307.

## Ringkasan

| Pemeriksaan | Perintah | Hasil |
|---|---|---|
| Seluruh test, MariaDB | `php artisan test` dengan `DB_CONNECTION=mysql` | 100 passed, 0 skipped |
| Seluruh test, SQLite | `php artisan test` | 99 passed, 1 skipped, 289 assertions |
| Concurrency T16 | `php artisan test --filter='ConcurrentCheckoutTest'` pada MariaDB | 1 passed, 4 assertions |
| Otorisasi checkout | `php artisan test --filter='CheckoutAuthorizationTest'` | 5 passed, 12 assertions |
| Event-Tag | `php artisan test --filter='EventTagTest'` | 7 passed, 25 assertions |
| Checkout | `php artisan test --filter='CheckoutTest::'` | 10 passed, 48 assertions |
| Idempotency | `php artisan test --filter='CheckoutIdempotencyTest::'` | 4 passed, 18 assertions |
| Sanctum | `php artisan test --filter='SanctumTokenTest'` | 2 passed, 4 assertions |
| Formatter | `./vendor/bin/pint --test` | Passed |
| Migrasi dan seeder | `php artisan migrate:fresh --seed` pada database sementara | Berhasil, 6 tag dan 5 event bertag |
| Route aplikasi | `php artisan route:list --except-vendor` | 35 route |

## Bukti T16

`ConcurrentCheckoutTest` menjalankan dua proses checkout serentak dengan `pcntl_fork` dan barrier file. Keduanya memperebutkan tiket terakhir dengan `remaining_quota = 1`. Hasil yang diverifikasi:

- Tepat satu checkout berhasil.
- Satu registrasi dan satu issued ticket terbentuk.
- `remaining_quota` menjadi 0.
- Checkout kedua gagal tanpa oversell.

Perintah eksekusi:

```bash
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3307 \
DB_DATABASE=uts_event_ticket_test DB_USERNAME=root DB_PASSWORD= \
php artisan test --filter=ConcurrentCheckoutTest
```

Pada SQLite, test ini di-skip karena row lock dan isolasi transaksi SQLite berbeda dari MySQL.

## Bukti yang Masih Perlu Diambil Manual

Tangkapan layar terminal untuk laporan dan screenshot pivot `event_tag` pada GUI database. Hasil otomatis di atas dapat dipakai sebagai teks pendukung, bukan pengganti screenshot yang diminta rubrik.
