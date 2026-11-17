# Panduan Bukti Screenshot: TicketModule & Kuota (Fase F5)
## Porsi Laporan BAB III & BAB IV — Anggota 2 (Pemilik TicketModule)

Folder ini digunakan untuk menampung berkas tangkapan layar (*screenshot*) yang membuktikan implementasi `TicketModule`, antarmuka admin tiket, dan penanganan kuota untuk Laporan UTS Backend Development (DT199).

---

## 1. Daftar Screenshot Wajib

| Nama Berkas | Konten Tangkapan Layar | Halaman Sumber / Perintah | Lokasi di Laporan UTS |
|---|---|---|---|
| `05-01-admin-ticket-index.png` | Tabel daftar tiket di dashboard admin lengkap dengan badge sisa kuota | URL: `http://127.0.0.1:8000/admin/tickets` | BAB III — Subbab 3.4 (Implementasi TicketModule) |
| `05-02-admin-ticket-create.png` | Form penambahan tiket dengan validasi event, kuota, dan harga | URL: `http://127.0.0.1:8000/admin/tickets/create` | BAB III — Subbab 3.6 (Form Request Validation) |
| `05-03-admin-ticket-edit.png` | Form edit kuota tiket yang memperlihatkan kalkulasi sisa & terjual | URL: `http://127.0.0.1:8000/admin/tickets/{id}/edit` | BAB III — Subbab 3.4 (Manajemen Kuota Tiket) |
| `05-04-ticket-quota-exceeded-alert.png` | Alert merah Custom Exception saat pemesanan melebihi kuota sisa | Halaman checkout dengan pesan error `TicketQuotaExceededException` | BAB IV — Subbab 4.3 (Hasil Error Handling) |

---

## 2. Panduan Eksekusi

Jalankan server aplikasi di terminal:
```powershell
Set-Location "d:\uts BE\app-ticketing"
php artisan serve
```
Login sebagai Admin (`admin@example.test` / `password`), akses tautan di atas, dan ambil screenshot browser dengan tampilan bersih dan proporsional.
