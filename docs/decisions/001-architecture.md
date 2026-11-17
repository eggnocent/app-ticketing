# 001 — Keputusan Arsitektur

- **Tanggal:** 17 November 2026
- **Status:** Diterima
- **Konteks:** UTS Backend Development (DT199), studi kasus sistem manajemen event dan tiket.

## Stack

| Komponen | Keputusan |
|---|---|
| Bahasa | PHP 8.4+ |
| Framework | Laravel 13 |
| Database | MySQL/MariaDB |
| Frontend | Blade + Tailwind CSS + Vite |
| Auth | Laravel Sanctum + session |
| Testing | PHPUnit |
| Formatter | Laravel Pint |

## Arsitektur

Modular Monolith dengan tiga modul:

- `EventModule` — kategori, event, katalog, `EventInfoContract`.
- `TicketModule` — jenis tiket, harga, kuota, `TicketInventoryContract`.
- `RegistrationModule` — registrasi, checkout, riwayat, e-ticket.

Aturan dependency:

- `RegistrationModule` hanya boleh memakai `EventInfoContract` dan `TicketInventoryContract`, tidak boleh mengimpor model/query/tabel internal modul lain.
- `EventModule` tidak boleh memanggil `RegistrationModule`.
- Komunikasi lintas modul mengembalikan array/DTO primitive, bukan Eloquent model.

## Database

- Semua tabel aplikasi memakai `id` UUID dan FK UUID. Tabel framework mengikuti skema Laravel, kecuali `sessions.user_id` dan morph `personal_access_tokens` yang disesuaikan ke UUID.
- Audit5 pada setiap tabel aplikasi: `id`, `created_at`, `created_by`, `updated_at`, `updated_by`.
- `created_by`/`updated_by` diisi server-side dari `Auth::id()`; `NULL` sah untuk guest/seeder/sistem. Nilai audit dari request diabaikan.
- Uang disimpan sebagai integer rupiah (`price_rupiah`, `total_rupiah`, `unit_price_rupiah`, `subtotal_rupiah`), bukan float.
- `ticket_code` dan `invoice_number` adalah string unik terpisah, bukan UUID.

Urutan migrasi: `users` → `categories` → `events` → `tickets` → `registrations` → `registration_tickets` → `issued_tickets` → `tags` → `event_tag`.

## Status dan aturan bisnis

- Status event: `draft`, `published`, `cancelled`.
- `isPurchasable(eventId)`: status `published` **dan** `now() < end_time`.
- Hanya tiket dengan periode jual aktif yang bisa dibeli.
- Checkout adalah **simulasi pembelian sukses** (`status = completed`), bukan pembayaran gateway nyata.
- Setiap unit tiket memperoleh satu baris `issued_tickets` dengan kode unik.

## Contract

```php
interface EventInfoContract
{
    public function getSummary(string $eventId): ?array;
    public function getEventOptions(): array;
    public function isPurchasable(string $eventId): bool;
}

interface TicketInventoryContract
{
    public function getSummary(string $ticketId): ?array;
    public function getEventTickets(string $eventId): array;
    public function reserve(string $ticketId, int $quantity): array;
    public function release(string $ticketId, int $quantity): void;
}
```

- `reserve()` wajib dipanggil di dalam `DB::transaction()` dan memakai `lockForUpdate()`.
- Item checkout diurutkan berdasarkan `ticket_id` agar urutan lock konsisten.
- Kegagalan di tengah transaksi melempar exception, bukan `return false`.
- `TicketPurchasedEvent` menerapkan `ShouldDispatchAfterCommit` dan listener berada di `RegistrationModule`.

## Auth dan otorisasi

- Role `admin` dan `peserta`; role default `peserta` ditentukan server.
- Input `role` dari register diabaikan.
- Route admin memakai middleware `web`, `auth`, `role:admin`.
- Route checkout dan riwayat peserta memakai `role:peserta`. Admin tidak dapat membeli tiket atau membuka halaman riwayat peserta.
- Ownership record (registrasi/e-ticket) tetap dicek di controller/service, bukan hanya middleware role.

## Git dan kualitas

- Branch fitur: `feat/<nama>`, `test/<nama>`, `docs/<nama>`, `fix/<nama>`.
- Commit: `feat(auth)`, `feat(event)`, `feat(ticket)`, `feat(registration)`, `test`, `docs`, `fix`, `chore`.
- Sebelum merge: `php artisan test` dan `./vendor/bin/pint --test` lulus.
- `migrate:fresh` hanya untuk database development/testing.

## Konsekuensi

- Skema dan contract harus disetujui sebelum implementasi checkout.
- Concurrency diuji pada MySQL/MariaDB; SQLite in-memory hanya untuk feature test umum.
- Perubahan schema/contract memerlukan pembaruan ADR ini.
