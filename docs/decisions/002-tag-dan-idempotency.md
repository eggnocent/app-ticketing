# 002: Keputusan Tag Many-to-Many dan Idempotency Checkout

- **Tanggal:** 17 November 2026
- **Status:** Diterima
- **Konteks:** Pemenuhan gap roadmap UTS: bukti relasi Many-to-Many Eloquent, pencegahan double checkout, dan pengujian concurrency.

## Tag Event

Keputusan:

- `tags` dan pivot `event_tag` berada di `EventModule`. Tidak ada modul baru karena tag hanya memperkaya katalog event.
- Keduanya memakai UUID primary key dan Audit5. Pivot memakai UUID surrogate agar `EventTag` dapat menjadi model Eloquent penuh dengan trait `HasUuids` dan `HasAudit`.
- `Event::tags()` dan `Tag::events()` memakai `belongsToMany(...)->using(EventTag::class)->withPivot(['id', 'created_by', 'updated_by'])->withTimestamps()`.
- Unique constraint `(event_id, tag_id)` mencegah tag ganda pada satu event.
- Admin memilih tag lewat checkbox. `EventAdminController` memanggil `sync()` di dalam alur simpan event. Validasi memakai `exists:tags,id`.
- Tag bersifat opsional pada form. Seeder menyediakan enam tag dan memasangkannya ke lima event demo.

Konsekuensi:

- `withPivot()` wajib memuat `id`, `created_by`, dan `updated_by`; tanpa itu atribut audit pivot tidak tersedia pada hasil relasi.
- Menghapus tag atau event ditolak oleh foreign key `restrictOnDelete`. Penghapusan event terlebih dahulu melepas pivot di dalam transaksi.

## Idempotency Checkout

Keputusan:

- `registrations.idempotency_key` nullable dengan unique constraint `(user_id, idempotency_key)`. Scope per user mencegah satu user membajak key user lain, sementara key yang sama pada user berbeda tetap sah.
- Halaman checkout membuat UUID baru saat GET dan mengirimkannya lewat hidden input.
- `CheckoutService` memeriksa key sebelum transaksi. Jika record sudah ada, service mengembalikan registrasi tersebut beserta relasi tiket.
- Jika dua request membawa key yang sama secara bersamaan, request kedua menangkap `UniqueConstraintViolationException`, lalu membaca ulang registrasi yang sudah commit. Jika penyebab benturan bukan idempotency, exception dilempar kembali.
- Pengiriman ulang mengembalikan pesanan yang sama, bukan error 409. Ini sesuai alur simulasi checkout yang mengarahkan user ke halaman sukses.

Konsekuensi:

- Key hanya berlaku untuk pasangan user dan key yang sama. Perubahan isi keranjang dengan key lama tetap mengembalikan pesanan lama, jadi frontend harus membuat key baru ketika isi keranjang berubah.
- Test concurrency memakai `pcntl_fork` dan hanya berjalan pada MySQL/PostgreSQL. SQLite melewati test karena mekanisme lock dan isolasinya berbeda.
