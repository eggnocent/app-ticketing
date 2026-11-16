# PANDUAN PENGERJAAN ANGGOTA 1
## EventModule + Auth/RBAC — Sistem Manajemen Event & Tiket

**Status:** Dokumen eksekusi operasional Anggota 1.  
**Acuan utama:** `ROADMAP_DAN_DOKUMENTASI_UTS_BACKEND_EVENT_TIKET_V4_ROADMAP_TIM.md` (bagian F, J, K, L, M, N, O).  
**Stack:** Laravel 13, PHP 8.4+, Blade, MySQL, Laravel Sanctum, UUID, RBAC `admin`/`peserta`.  
**Sifat jadwal:** estimasi internal 10 hari kerja efektif, **bukan tanggal resmi dari dosen**.

> **Cara memakai dokumen ini:** kerjakan berurutan per fase. Setiap langkah punya output yang harus benar-benar ada (kode, test, screenshot, commit). Jangan lanjut ke fase berikutnya jika exit gate belum lulus.

---

# 0. RINGKASAN PERAN ANGGOTA 1

| Item | Isi |
|---|---|
| **Nama peran** | Anggota 1 (A) — EventModule + Auth/RBAC |
| **Ownership kode** | `app/Modules/EventModule/**`, `app/Models/User.php`, `app/Http/Middleware/RoleMiddleware.php`, controller/request/view auth |
| **PIC fase** | F0 (validasi & scope), F1 (setup repo), F3 (auth/RBAC), F4 (EventModule), F9 (demo/submit) |
| **Bantuan fase** | F6 (uji keamanan event/role), F7 (uji auth, CSRF, XSS, boundary review) |
| **Output PR wajib** | `feat/auth-sanctum-rbac`, `feat/event-crud`, `test/event-access` |
| **Output dokumen** | BAB I, BAB II bagian bounded context, BAB III bagian auth/Event |
| **Output video** | Intro + arsitektur (0:00–2:00), Auth & admin (3:20–4:50), penutup |
| **Estimasi beban** | ±33% (bukan skor kontribusi; kontribusi nyata dibuktikan commit) |

**Yang TIDAK dikerjakan Anggota 1 (milik teman):**

- Migration/model/logic `tickets`, kuota, `reserve()`, `TicketInventoryContract` → **Anggota 2 (B)**.
- Migration/model/logic `registrations`, `registration_tickets`, `issued_tickets`, checkout, e-ticket → **Anggota 3 (C)**.
- Anggota 1 boleh **membaca dan mereview** kode mereka, tetapi tidak mengambil alih ownership.

**Aturan main:**

1. Desain skema, nama Contract, format uang, dan aturan status **disepakati bertiga dulu** (F0) sebelum coding.
2. Semua tabel aplikasi memakai `id` UUID, `created_at`, `created_by` UUID, `updated_at`, `updated_by` UUID.
3. `EventModule` tidak boleh mengimpor model/query internal `TicketModule` atau `RegistrationModule`.
4. Setiap PR direview minimal satu teman; setiap anggota commit dengan akun Git masing-masing.
5. Jangan menulis semua kode lalu mengganti nama author. Riwayat commit harus mencerminkan pekerjaan nyata.

---

# 1. TARGET SELESAI ANGGOTA 1 (DEFINITION OF DONE)

Tandai selesai hanya jika semua poin berikut benar-benar terbukti:

- [ ] Repository Laravel 13 + PHP 8.4 berjalan di mesin sendiri dan bisa di-clone teman.
- [ ] Tiga folder modul dan tiga Service Provider terdaftar; `php artisan route:list` menampilkan route modul.
- [ ] Tabel `users` memakai UUID, punya `role`, kolom audit, dan Sanctum berfungsi.
- [ ] Register, login, logout berjalan; role default `peserta`; tidak bisa self-assign `admin`.
- [ ] Middleware `role:admin` / `role:peserta` aktif dan diuji (guest redirect, peserta 403, admin 200).
- [ ] Migration `categories` dan `events` memakai UUID + Audit5, FK, unique slug, index status/waktu.
- [ ] Model `Category` dan `Event` memakai `HasUuids`, relasi 1-N, scope published, audit otomatis.
- [ ] CRUD kategori dan event berjalan, seluruh form memakai Form Request.
- [ ] Validasi `end_time > start_time`, slug unik, status valid, banner aman.
- [ ] Katalog publik hanya menampilkan event `published`; draft/cancelled tidak bisa diakses guest.
- [ ] `EventInfoContract` + `EventInfoService` dibuat, di-bind di provider, dan diuji.
- [ ] Feature test auth, RBAC, event CRUD, katalog, contract, audit lulus.
- [ ] `./vendor/bin/pint --test` lulus.
- [ ] Screenshot bukti tersimpan di `docs/evidence/`.
- [ ] Bagian laporan dan segmen video milik A selesai.
- [ ] PR sudah direview dan di-merge ke `main`.

---

# 2. POSISI DI TIM DAN HANDOFF

| Anggota | Ownership | Yang A butuhkan | Yang A serahkan |
|---|---|---|---|
| **A (kamu)** | Auth, users, categories, events, EventInfoContract | Schema tickets dari B untuk halaman detail; data event untuk seeder C | Contract event final, route katalog, akun demo admin/peserta |
| **B** | Tickets, inventory, ERD, Audit5 | Daftar event + `event_id` untuk seeder tiket | Struktur tabel `categories`/`events`, trait audit, keputusan UUID |
| **C** | Registrations, checkout, e-ticket, QA | `EventInfoContract` stabil + event published untuk uji checkout | Uji akses event/role, bantuan UI e-ticket |

**Kesepakatan wajib di H1 (F0):**

1. Nama method Contract: `getSummary(string $eventId): ?array`, `isPurchasable(string $eventId): bool`.
2. Format uang: integer rupiah (`price_rupiah`, `total_rupiah`), bukan float.
3. Status event: `draft`, `published`, `cancelled`.
4. Aturan `isPurchasable`: status `published` **dan** `now() < end_time`. Catat di ADR.
5. Locasi trait audit: `app/Models/Concerns/HasAudit.php` (dipakai semua model domain).
6. Siapa mengisi `DatabaseSeeder` utama: A menyusun urutan, B/C menambahkan seeder masing-masing.
7. Nama route dan prefix: `admin.categories.*`, `admin.events.*`, `events.*`.

---

# 3. PETA FASE ANGGOTA 1

| Fase | Hari | Fokus A | Exit gate |
|---|---|---|---|
| F0 | H1 | Konfirmasi studi kasus, scope, ADR, board, GitHub | Semua aturan disepakati; tidak ada ambiguitas |
| F1 | H1 | Setup Laravel, Sanctum, 3 provider, Git | Satu perintah `php artisan about` dan `route:list` sukses |
| F2 | H2 | Migration `users`, `categories`, `events` + factory/seeder | `migrate:fresh --seed` sukses |
| F3 | H3 | Auth, role middleware, test akses | Guest/peserta/admin sesuai aturan |
| F4 | H4 | CRUD kategori/event, katalog, EventInfoContract | CRUD + validasi + publish + contract lulus test |
| F5 | H5 | Review aturan event untuk checkout; uji contract | Contract disetujui B/C |
| F6 | H6 | Uji keamanan event/role, audit test | Tidak ada kebocoran akses |
| F7 | H8 | Uji auth/CSRF/XSS, boundary review | Test + Pint lulus |
| F8 | H9 | BAB I, BAB II bounded context, screenshot | Draft laporan bagian A |
| F9 | H10 | Demo arsitektur/auth/event, final check | Video + PDF + repo siap |

---

# 4. F0 — VALIDASI SOAL DAN SCOPE (HARI 1)

## 4.1 Yang harus dikonfirmasi

- [ ] Studi Kasus 5 (Event/Ticket/Registration) tidak dipakai kelompok lain di kelas.
- [ ] Tanggal pengumpulan dilihat di dashboard; screenshot pengumuman disimpan.
- [ ] Maksimal 3 anggota; nama, NIM, kelas lengkap.
- [ ] Repo GitHub dibuat **public** dan semua anggota punya akses.
- [ ] Board dibuat: `Backlog → Ready → In Progress → Review → Done`.
- [ ] `docs/decisions/001-architecture.md` ditulis dan disetujui bertiga.

## 4.2 Isi minimal ADR

```markdown
# 001 — Keputusan Arsitektur

- Studi kasus: Sistem Manajemen Event & Tiket
- Anggota: A, B, C
- Stack: Laravel 13, PHP 8.4+, Blade, MySQL, Sanctum
- Modul: EventModule, TicketModule, RegistrationModule
- UUID untuk seluruh tabel aplikasi + Audit5
- Uang: integer rupiah
- Checkout: simulasi pembelian sukses (bukan payment gateway)
- Satu checkout hanya untuk satu event
- Status event: draft, published, cancelled
- isPurchasable: published dan now() < end_time
- Tabel issued_tickets: satu kode per tiket individual
- Event–Tag: dipakai/tidak dipakai (pilih salah satu)
```

## 4.3 Branch dan commit convention

```text
main
├── feat/auth-sanctum-rbac
├── feat/uuid-database
├── feat/event-crud
├── feat/event-catalog
├── test/event-access
└── docs/laporan-bab-1-2
```

Format commit:

```text
chore: initialize Laravel 13 project
feat(auth): add uuid users and sanctum
feat(rbac): add role middleware
feat(event): add category crud
feat(event): add event crud and publish action
feat(event): add public catalog and event info contract
test(event): cover published filter and event contract
docs: add architecture decision record
```

**Bukti fase F0:** screenshot board, link repo, ADR, daftar anggota.

---

# 5. F1 — SETUP PROYEK DAN STRUKTUR MODUL (HARI 1)

## 5.1 Cek environment

```bash
php -v          # wajib 8.4+
composer --version
node --version
npm --version
git --version
mysql --version
```

Jika PHP belum 8.4, perbaiki dulu. Jangan memaksa jalan di versi lama karena syarat dosen eksplisit.

## 5.2 Buat proyek

```bash
composer create-project laravel/laravel event-ticket "^13.0"
cd event-ticket
php artisan --version
cp .env.example .env
php artisan key:generate
php artisan install:api
npm install
```

> `install:api` memasang Sanctum dan migration `personal_access_tokens`. Jika versi Laravel yang terpasang berbeda, sesuaikan perintah dengan dokumentasi resmi yang terpasang, tetapi tetap catat versi nyata di laporan.

## 5.3 Konfigurasi `.env`

```dotenv
APP_NAME="Event Ticket UTS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uts_event_ticket
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
```

Buat database kosong:

```sql
CREATE DATABASE uts_event_ticket CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 5.4 Perbaikan UUID pada tabel framework yang terpengaruh

Ini bagian yang sering terlewat. Karena `users.id` akan UUID:

1. **Migration `sessions`** — ubah `user_id` menjadi UUID:

```php
$table->foreignUuid('user_id')->nullable()->index();
```

2. **Migration `personal_access_tokens`** (dari Sanctum) — ubah morph menjadi UUID:

```php
$table->uuidMorphs('tokenable');
```

3. Jangan mengubah tabel framework lain tanpa alasan dan pengujian.

## 5.5 Struktur folder modul

Buat struktur berikut (file boleh kosong dulu, diisi per fase):

```text
app/Modules/EventModule/
├── Contracts/EventInfoContract.php
├── Controllers/{CategoryController,EventAdminController,EventCatalogController}.php
├── Database/{Migrations,Seeders,Factories}/
├── Models/{Category,Event}.php
├── Providers/EventModuleServiceProvider.php
├── Requests/{StoreCategoryRequest,UpdateCategoryRequest,StoreEventRequest,UpdateEventRequest}.php
├── Routes/web.php
├── Services/EventInfoService.php
└── Views/{admin,public}/

app/Modules/TicketModule/
├── Contracts/TicketInventoryContract.php
├── Providers/TicketModuleServiceProvider.php
└── Routes/web.php

app/Modules/RegistrationModule/
├── Providers/RegistrationModuleServiceProvider.php
└── Routes/web.php
```

## 5.6 Provider skeleton

```php
<?php

namespace App\Modules\EventModule\Providers;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\EventModule\Services\EventInfoService;
use Illuminate\Support\ServiceProvider;

class EventModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventInfoContract::class, EventInfoService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Views', 'event');
    }
}
```

Untuk B dan C, buat skeleton serupa tanpa binding jika belum ada service. A bertanggung jawab memastikan ketiganya terdaftar.

`bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Modules\EventModule\Providers\EventModuleServiceProvider::class,
    App\Modules\TicketModule\Providers\TicketModuleServiceProvider::class,
    App\Modules\RegistrationModule\Providers\RegistrationModuleServiceProvider::class,
];
```

## 5.7 Route modul wajib memakai middleware `web`

File `app/Modules/EventModule/Routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    // route publik dan admin diisi pada F4
});
```

Tanpa `web`, session, CSRF, dan auth Blade tidak berjalan.

## 5.8 Verifikasi F1

```bash
php artisan migrate
php artisan route:list
php artisan about
npm run build
php artisan serve
```

- [ ] Browser membuka `http://127.0.0.1:8000`.
- [ ] Tiga provider muncul di `php artisan about` atau terbukti route/migration-nya termuat.
- [ ] `.env` tidak masuk Git.
- [ ] Commit `chore: initialize Laravel 13 project` dan push.

**Bukti F1:** screenshot struktur `app/Modules`, isi tiga provider, `route:list`, `about`, dan halaman awal.

---

# 6. F2 — DATABASE: USERS, CATEGORIES, EVENTS (HARI 2)

## 6.1 Migration `users`

Edit migration bawaan (`0001_01_01_000000_create_users_table.php`) **sebelum** migrasi pertama. Jangan membuat tabel `users` dua kali.

```php
Schema::create('users', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('role')->default('peserta');
    $table->rememberToken();
    $table->timestamps();

    $table->foreignUuid('created_by')->nullable();
    $table->foreignUuid('updated_by')->nullable();
});
```

Catatan:

- `created_by`/`updated_by` pada `users` dibuat nullable **tanpa FK self-reference** pada migrasi awal.
- Jangan simpan role dari input register; role default ditentukan server.
- `sessions.user_id` harus UUID seperti pada bagian 5.4.

## 6.2 Migration `categories`

`app/Modules/EventModule/Database/Migrations/2026_11_16_100000_create_categories_table.php`:

```php
Schema::create('categories', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();

    $table->timestamps();
    $table->foreignUuid('created_by')->nullable()
        ->constrained('users')->restrictOnDelete();
    $table->foreignUuid('updated_by')->nullable()
        ->constrained('users')->restrictOnDelete();
});
```

## 6.3 Migration `events`

`2026_11_16_100100_create_events_table.php`:

```php
Schema::create('events', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('category_id')->constrained('categories')->restrictOnDelete();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description');
    $table->string('location');
    $table->dateTime('start_time');
    $table->dateTime('end_time');
    $table->string('status')->default('draft'); // draft|published|cancelled
    $table->string('banner_path')->nullable();

    $table->timestamps();
    $table->foreignUuid('created_by')->nullable()
        ->constrained('users')->restrictOnDelete();
    $table->foreignUuid('updated_by')->nullable()
        ->constrained('users')->restrictOnDelete();

    $table->index(['status', 'start_time']);
});
```

Urutan migrasi: `users` → `categories` → `events` → `tickets` (B) → `registrations` (C) → pivot (C) → `issued_tickets` (C).

## 6.4 Model `User`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
```

`role` sengaja **tidak** masuk `fillable`. Untuk registrasi, role default `peserta` dari database. Untuk seeder admin, set langsung: `$user->role = 'admin'; $user->save();`.

## 6.5 Trait audit `HasAudit`

`app/Models/Concerns/HasAudit.php`:

```php
<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    public static function bootHasAudit(): void
    {
        static::creating(function ($model) {
            $actor = Auth::id();
            $model->created_by ??= $actor;
            $model->updated_by ??= $actor;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
```

Aturan:

- `created_by` tidak boleh diubah saat update.
- Jangan menerima `created_by`/`updated_by` dari request.
- Mutasi massal via query builder **tidak** memicu trait; jika B/C memakainya, stamping audit harus eksplisit.
- Seeder/guest boleh `NULL`.

## 6.6 Model `Category`

```php
<?php

namespace App\Modules\EventModule\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = ['name', 'slug', 'description'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
```

## 6.7 Model `Event`

```php
<?php

namespace App\Modules\EventModule\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'location',
        'start_time', 'end_time', 'status', 'banner_path',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
```

**Jangan** menambahkan `Event::tickets()` di EventModule, karena akan mengimpor model milik TicketModule. Relasi 1-N yang dibuktikan di modul ini adalah `Category → Event`.

## 6.8 Factory dan Seeder

Factory `UserFactory`:

```php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
        'role' => 'peserta',
    ];
}

public function admin(): static
{
    return $this->state(fn () => ['role' => 'admin']);
}
```

Factory `CategoryFactory`: nama unik + slug dari `Str::slug($name)`.  
Factory `EventFactory`: category_id dari factory Category, status draft default, `start_time` dan `end_time` valid, ditambah state `published()`, `draft()`, `cancelled()`.

Seeder minimal:

- 1 admin: `admin@example.test` / password `password`.
- 2 peserta: `peserta1@example.test`, `peserta2@example.test`.
- 3 kategori.
- 5 event: 3 published, 1 draft, 1 cancelled, waktu berbeda-beda.

`DatabaseSeeder` mengatur urutan:

```php
$this->call([
    UserSeeder::class,
    CategorySeeder::class,
    EventSeeder::class,
    TicketSeeder::class,        // B
    RegistrationSeeder::class,  // C, opsional
]);
```

Jangan meng-hardcode UUID. Gunakan `Str::uuid()` atau biarkan `HasUuids` mengisi. Untuk foreign key di seeder, ambil dari model yang sudah dibuat, bukan UUID tebakan.

## 6.9 Verifikasi F2

```bash
php artisan migrate:fresh --seed
php artisan tinker
```

Cek di tinker:

```php
App\Modules\EventModule\Models\Event::with('category')->published()->count();
App\Models\User::where('role', 'admin')->count();
```

**Bukti F2:** screenshot ERD bagian users/categories/events, hasil seeder, isi tabel, dan `migrate:fresh --seed` sukses.

---

# 7. F3 — AUTH DAN RBAC (HARI 3)

## 7.1 Route auth dan admin

`routes/web.php` (core, bukan modul):

```php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
```

Route admin ditempatkan di file route modul, dengan middleware `web`, `auth`, `role:admin`.

## 7.2 Middleware role

`app/Http/Middleware/RoleMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_unless($request->user()?->role === $role, 403, 'Akses ditolak.');

        return $next($request);
    }
}
```

Daftarkan alias di `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

## 7.3 Form Request auth

`RegisterRequest`:

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];
}
```

Jangan masukkan `role` ke rules. Jika request mengirim `role=admin`, nilainya harus diabaikan.

## 7.4 Controller register

```php
public function store(RegisterRequest $request): RedirectResponse
{
    $user = User::create($request->safe()->only(['name', 'email', 'password']));

    // Role default 'peserta' dari database; jangan menerima role dari browser.
    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('events.index')
        ->with('success', 'Registrasi berhasil.');
}
```

## 7.5 Controller login/logout

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate(); // Auth::attempt + throttle bila dipilih
    $request->session()->regenerate();

    return redirect()->intended(route('events.index'));
}

public function destroy(Request $request): RedirectResponse
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
```

Gunakan `RateLimiter` untuk login (opsional tetapi nilai tambah keamanan). Jangan menampilkan pesan yang membedakan “email tidak ada” dan “password salah”.

## 7.6 View auth

- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/layouts/app.blade.php`

Wajib:

- `@csrf` pada setiap form.
- `@error('field')` untuk pesan validasi.
- `old('email')` pada input.
- Tautan login/register/logout sesuai status `@auth`.
- Jangan memakai `{!! !!}` untuk input pengguna.

## 7.7 Test F3

| Test | Ekspektasi |
|---|---|
| Guest buka `/admin/events` | redirect ke login |
| Peserta buka `/admin/events` | 403 |
| Admin buka `/admin/events` | 200 |
| Register baru | role `peserta` |
| Request register menyertakan `role=admin` | tetap `peserta` |
| Peserta A tidak bisa melihat data peserta B | 403/404 (uji milik C, A bantu review) |
| Logout | session invalid, redirect login |

**Bukti F3:** screenshot login, register, 403, kode middleware, hasil test.

---

# 8. F4 — EVENTMODULE (HARI 4)

## 8.1 Daftar route

`app/Modules/EventModule/Routes/web.php`:

```php
use App\Modules\EventModule\Controllers\CategoryController;
use App\Modules\EventModule\Controllers\EventAdminController;
use App\Modules\EventModule\Controllers\EventCatalogController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/events', [EventCatalogController::class, 'index'])->name('events.index');
    Route::get('/events/{slug}', [EventCatalogController::class, 'show'])->name('events.show');

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('events', EventAdminController::class);
        Route::patch('events/{event}/publish', [EventAdminController::class, 'publish'])
            ->name('events.publish');
    });
});
```

Catatan: route publik **tidak** memakai `auth`. Route admin memakai `auth` + `role:admin`. Jika tim memutuskan `auth:sanctum`, pastikan session Blade tetap bekerja dan diuji.

## 8.2 Form Request kategori

`StoreCategoryRequest`:

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:100'],
        'slug' => ['required', 'string', 'max:120', 'alpha_dash', 'unique:categories,slug'],
        'description' => ['nullable', 'string', 'max:2000'],
    ];
}
```

`UpdateCategoryRequest` sama, tetapi unique memakai `Rule::unique('categories', 'slug')->ignore($this->route('category'))`.

## 8.3 Form Request event

`StoreEventRequest`:

```php
public function rules(): array
{
    return [
        'category_id' => ['required', 'uuid', 'exists:categories,id'],
        'title' => ['required', 'string', 'max:150'],
        'slug' => ['required', 'string', 'max:180', 'alpha_dash', 'unique:events,slug'],
        'description' => ['required', 'string'],
        'location' => ['required', 'string', 'max:255'],
        'start_time' => ['required', 'date'],
        'end_time' => ['required', 'date', 'after:start_time'],
        'status' => ['required', 'in:draft,published,cancelled'],
        'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ];
}
```

`UpdateEventRequest` menyesuaikan unique dan route model binding. Validasi `after:start_time` memastikan `end_time` benar-benar lebih besar.

## 8.4 Controller kategori

- `index`: paginate kategori + jumlah event.
- `create` / `store`: simpan, redirect dengan flash success.
- `edit` / `update`: update, redirect.
- `destroy`: cegah hapus jika masih punya event, atau gunakan `restrictOnDelete` dan tangkap error dengan pesan informatif.

Jangan memakai `$request->all()`; gunakan `$request->validated()`.

## 8.5 Controller event admin

- `index`: daftar event + filter status (opsional) + paginate.
- `create` / `store`: upload banner ke `storage/app/public/banners`, simpan path.
- `edit` / `update`: ganti banner lama dengan aman.
- `destroy`: hapus banner terkait bila ada.
- `publish`: validasi event belum berakhir, lalu ubah status menjadi `published`.

Contoh `publish`:

```php
public function publish(Event $event): RedirectResponse
{
    if ($event->end_time->isPast()) {
        return back()->with('error', 'Event yang sudah berakhir tidak bisa dipublikasikan.');
    }

    $event->update(['status' => 'published']);

    return back()->with('success', 'Event berhasil dipublikasikan.');
}
```

Jalankan `php artisan storage:link` agar banner dapat diakses.

## 8.6 Katalog publik

```php
public function index()
{
    $events = Event::query()
        ->published()
        ->with('category')
        ->orderBy('start_time')
        ->paginate(9);

    return view('event::public.index', compact('events'));
}

public function show(string $slug)
{
    $event = Event::query()
        ->published()
        ->with('category')
        ->where('slug', $slug)
        ->firstOrFail();

    return view('event::public.show', compact('event'));
}
```

**Penting:** detail event memakai `published()` juga. Jangan hanya memfilter di index, karena draft bisa bocor lewat URL slug.

Harga dan sisa tiket di halaman detail:

- Jangan query tabel `tickets` dari EventModule.
- Sepakati method tambahan di `TicketInventoryContract`, misalnya `getEventTickets(string $eventId): array`.
- Inject contract tersebut di `EventCatalogController` (bukan model Ticket).
- Jika B belum selesai, tampilkan daftar kosong/stub dan beri catatan TODO; integrasi final setelah contract stabil.

## 8.7 EventInfoContract

`app/Modules/EventModule/Contracts/EventInfoContract.php`:

```php
<?php

namespace App\Modules\EventModule\Contracts;

interface EventInfoContract
{
    public function getSummary(string $eventId): ?array;

    public function isPurchasable(string $eventId): bool;
}
```

`EventInfoService`:

```php
<?php

namespace App\Modules\EventModule\Services;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\EventModule\Models\Event;

class EventInfoService implements EventInfoContract
{
    public function getSummary(string $eventId): ?array
    {
        $event = Event::query()->with('category:id,name')->find($eventId);

        if (! $event) {
            return null;
        }

        return [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'category' => $event->category?->name,
            'location' => $event->location,
            'start_time' => $event->start_time?->toIso8601String(),
            'end_time' => $event->end_time?->toIso8601String(),
            'status' => $event->status,
        ];
    }

    public function isPurchasable(string $eventId): bool
    {
        return Event::query()
            ->whereKey($eventId)
            ->where('status', 'published')
            ->where('end_time', '>', now())
            ->exists();
    }
}
```

Binding sudah ada di provider F1. Jangan mengembalikan model Eloquent dari contract; kembalikan array/DTO primitive.

## 8.8 View EventModule

```text
app/Modules/EventModule/Views/
├── public/index.blade.php
├── public/show.blade.php
├── admin/categories/index.blade.php
├── admin/categories/create.blade.php
├── admin/categories/edit.blade.php
├── admin/events/index.blade.php
├── admin/events/create.blade.php
├── admin/events/edit.blade.php
└── admin/events/show.blade.php
```

Panggil dengan namespace: `view('event::public.index')`, `view('event::admin.events.index')`.

Checklist view:

- [ ] Layout konsisten, navigasi admin/peserta berbeda.
- [ ] `@csrf` pada form mutasi.
- [ ] `@method('PUT')`/`@method('DELETE')` pada form update/delete.
- [ ] `@error` di setiap field.
- [ ] Tombol publish hanya untuk admin.
- [ ] Tidak menampilkan tombol admin ke peserta.
- [ ] Banner memakai `asset('storage/'.$event->banner_path)` atau `Storage::url`.

## 8.9 Test F4

| ID | Skenario | Ekspektasi |
|---|---|---|
| T01 | Guest buka `/events` | 200, hanya published tampil |
| T02 | Guest buka slug draft | 404 |
| T05 | Admin CRUD kategori/event | Data tersimpan |
| T06 | `end_time <= start_time` | 422, pesan validasi |
| — | Slug duplikat | 422 |
| — | Peserta buka admin | 403 |
| — | Publish event valid | status berubah |
| — | Publish event sudah berakhir | ditolak |
| — | Contract summary event published | array lengkap |
| — | `isPurchasable` draft/cancelled | false |
| — | `isPurchasable` published aktif | true |
| — | Audit create oleh admin A | `created_by=A`, `updated_by=A` |
| — | Audit update oleh admin B | `created_by` tetap A, `updated_by=B` |

**Bukti F4:** halaman CRUD, form error, katalog published, kode contract, hasil test.

---

# 9. F5–F7 — DUKUNGAN DAN QUALITY GATE

## 9.1 F5 (Hari 5) — dukungan ke C

- [ ] Pastikan `EventInfoContract` final dan bisa dipakai `CheckoutService`.
- [ ] Uji `isPurchasable` untuk event published, draft, cancelled, dan event yang sudah berakhir.
- [ ] Bantu C memahami aturan event; jangan menulis query ticket di modul C.
- [ ] Review `CheckoutRequest` agar tidak mengakses tabel event/ticket langsung.
- [ ] Pastikan event published di seeder benar-benar bisa dipakai checkout.

## 9.2 F6 (Hari 6) — keamanan event dan role

- [ ] Peserta tidak bisa membuka route admin mana pun (403).
- [ ] Guest diarahkan ke login untuk route terproteksi.
- [ ] Event draft/cancelled tidak bisa diakses lewat URL publik.
- [ ] Event draft/cancelled tidak bisa dibeli (test C memakai contract A).
- [ ] Admin tidak bisa menghapus kategori yang masih dipakai event tanpa pesan jelas.
- [ ] Uji audit create/update event.
- [ ] Review akses peserta ke e-ticket peserta lain (milik C).

## 9.3 F7 (Hari 8) — quality gate

```bash
php artisan test
./vendor/bin/pint --test
php artisan route:list
php artisan about
```

Boundary check:

```bash
rg 'Modules\\(TicketModule|EventModule)\\Models' app/Modules/RegistrationModule
rg "DB::table\\(['\"](tickets|events)" app/Modules/RegistrationModule
rg 'Modules\\(TicketModule|RegistrationModule)\\Models' app/Modules/EventModule
```

Hasil kosong belum otomatis benar; tetap review manual.

Checklist keamanan A:

- [ ] Blade memakai escaping default, tidak ada `{!! !!}` untuk input pengguna.
- [ ] Semua form mutasi punya `@csrf`.
- [ ] `role` tidak bisa diisi dari request.
- [ ] Route admin memakai `auth` + `role:admin`.
- [ ] Slug draft tidak bisa diakses publik.
- [ ] File upload dibatasi tipe dan ukuran.
- [ ] `.env`, password, token tidak ada di repo/screenshot.
- [ ] `APP_DEBUG=false` saat demo publik.

---

# 10. MATRIKS TEST TANGGUNG JAWAB ANGGOTA 1

| ID | Skenario | Level | Bukti |
|---|---|---|---|
| T01 | Guest buka event published | Feature | Screenshot + test |
| T02 | Guest buka event draft | Feature | Test |
| T03 | Register/login peserta | Feature | Screenshot + test |
| T04 | Peserta buka admin | Feature | Screenshot 403 |
| T05 | Admin CRUD kategori/event | Feature | Screenshot + test |
| T06 | Event `end <= start` | Feature | Screenshot error |
| T17 | Form Request dipakai semua form | Review + test | Daftar form |
| T18 | Tiga provider termuat | CLI | Screenshot `route:list`/`about` |
| — | Slug duplikat | Feature | Test |
| — | Publish event | Feature | Screenshot + test |
| — | Contract event published/draft/cancelled | Feature | Test |
| — | Audit create/update event | Feature | Screenshot data |
| — | Role tidak bisa dimanipulasi | Feature | Test payload `role=admin` |
| T20 | Boundary modul | Grep + review | Screenshot/grep |

---

# 11. BUKTI DAN SCREENSHOT

Buat folder:

```text
docs/evidence/
├── 01-architecture/
├── 02-database/
├── 03-auth/
├── 04-event/
├── 07-tests/
└── 08-git/
```

Minimal screenshot A:

- [ ] Struktur folder `app/Modules`.
- [ ] Tiga Service Provider dan isi `bootstrap/providers.php`.
- [ ] `php artisan route:list` (route publik + admin).
- [ ] Migration `users`, `categories`, `events`.
- [ ] ERD bagian event.
- [ ] Halaman register, login, logout.
- [ ] 403 peserta membuka admin.
- [ ] Middleware role.
- [ ] CRUD kategori.
- [ ] CRUD event + form validation error.
- [ ] Publish event.
- [ ] Katalog publik dan detail event.
- [ ] Kode `EventInfoContract` + binding.
- [ ] Output `php artisan test`.
- [ ] Output Pint.
- [ ] `git log --oneline --graph --all --decorate`.

Nama file bermakna, contoh: `03-rbac-participant-forbidden.png`, `04-event-validation-end-time.png`, `07-event-tests-passed.png`.

---

# 12. LAPORAN DAN VIDEO

## 12.1 Kontribusi laporan

| Bagian | Kontribusi A |
|---|---|
| Cover | Data diri + nama kelompok (koordinasi) |
| BAB I | Latar belakang, tujuan, ruang lingkup (penyusun utama) |
| BAB II 2.1 | Diagram bounded context + penjelasan boundary |
| BAB II 2.5 | Matriks hak akses guest/peserta/admin |
| BAB III 3.2 | Struktur modular + provider |
| BAB III 3.5 | Auth/RBAC |
| BAB III 3.6 | Form Request dan error event |
| BAB IV | Test auth/event + screenshot |
| BAB V | Ditulis bersama |

## 12.2 Segmen video

| Waktu | Bagian | Isi |
|---|---|---|
| 0:00–0:45 | Intro | Judul, anggota, studi kasus, stack |
| 0:45–2:00 | Arsitektur | 3 modul, provider, batas modul |
| 3:20–4:50 | Auth & admin | Login admin, CRUD event/kategori, peserta 403 |
| 9:20–10:00 | Penutup | Kesimpulan, repo, video |

Pastikan durasi total ≤ 10 menit, audio jelas, tanpa password/token terlihat.

---

# 13. CHECKLIST HARIAN ANGGOTA 1

## Hari 1 — F0/F1
- [ ] Konfirmasi studi kasus dan anggota.
- [ ] Tulis ADR.
- [ ] Buat repo public + board.
- [ ] Setup Laravel 13/PHP 8.4, Sanctum, `.env`.
- [ ] Buat 3 provider + folder modul.
- [ ] Perbaiki UUID pada `sessions` dan `personal_access_tokens`.
- [ ] Verifikasi `route:list`, `about`, `migrate`.
- [ ] Commit setup.

## Hari 2 — F2
- [ ] Migration users UUID + role + audit.
- [ ] Migration categories + events.
- [ ] Model Category/Event + trait audit.
- [ ] Factory Category/Event + UserFactory admin state.
- [ ] Seeder admin/peserta/kategori/event.
- [ ] `migrate:fresh --seed` sukses.

## Hari 3 — F3
- [ ] Route auth.
- [ ] Register/login/logout.
- [ ] RoleMiddleware + alias.
- [ ] View auth + layout.
- [ ] Test RBAC dan role default.
- [ ] Screenshot 403.

## Hari 4 — F4
- [ ] CRUD kategori + Form Request.
- [ ] CRUD event + upload banner.
- [ ] Publish event.
- [ ] Katalog publik + detail published-only.
- [ ] EventInfoContract + service + binding.
- [ ] Test event/contract/audit.
- [ ] Pint.

## Hari 5 — F5
- [ ] Review contract untuk checkout C.
- [ ] Uji aturan event published.
- [ ] Bantu integrasi harga/tiket via contract B.
- [ ] Review boundary C.

## Hari 6 — F6
- [ ] Uji semua route admin vs peserta/guest.
- [ ] Uji draft/cancelled di URL publik.
- [ ] Uji audit event create/update.
- [ ] Review ownership e-ticket.

## Hari 7 — F7
- [ ] Jalankan seluruh test.
- [ ] Pint.
- [ ] Grep boundary.
- [ ] Perbaiki bug temuan.

## Hari 8 — F8
- [ ] Screenshot lengkap.
- [ ] BAB I + bagian BAB II/III.
- [ ] Tabel test.
- [ ] Minta review silang.

## Hari 9 — F9
- [ ] Finalisasi laporan.
- [ ] Rekam segmen video.
- [ ] Cek repo public, README, `.env.example`.
- [ ] Cek PDF dan tautan.
- [ ] Upload sebelum tenggat.

---

# 14. RISIKO DAN MITIGASI

| Risiko | Dampak | Mitigasi |
|---|---|---|
| `users.id` UUID tetapi `sessions.user_id` bigint | Login gagal/error SQL | Ubah ke `foreignUuid` sebelum migrate |
| `personal_access_tokens` masih `morphs` | Token Sanctum tidak cocok UUID | Ubah ke `uuidMorphs` |
| Provider tidak terdaftar | Route/migration modul tidak muncul | Cek `bootstrap/providers.php` + `route:list` |
| Route modul tanpa `web` | Session/CSRF/auth gagal | Bungkus `Route::middleware('web')` |
| Event draft bocor lewat slug | Pelanggaran aturan bisnis | Scope `published()` di index dan show |
| Role diambil dari request | Peserta bisa jadi admin | Role default server-side, jangan masukkan ke rules/fillable |
| Audit tidak terisi | Rubrik audit gagal | Trait + test create/update; stamping eksplisit untuk query builder |
| Tanggal event tidak valid | Data kacau | `after:start_time` + test T06 |
| Banner tidak tampil | Nilai UI kurang | `storage:link`, path konsisten |
| Model Ticket diimpor EventModule | Melanggar boundary | Gunakan contract, review grep |
| Commit tidak mencerminkan kontribusi | Nilai Git jelek | Commit bertahap per fitur, akun masing-masing |
| Test hanya di SQLite | Locking tidak terbukti | Uji transaksi di MySQL (B/C) |

---

# 15. PERINTAH CEPAT

```bash
# setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link

# jalan
php artisan serve
npm run dev

# kualitas
php artisan test
./vendor/bin/pint --test
./vendor/bin/pint
php artisan route:list
php artisan about

# git
git checkout -b feat/nama-fitur
git add .
git commit -m "feat(scope): pesan jelas"
git push -u origin feat/nama-fitur

# boundary
rg 'Modules\\(TicketModule|EventModule)\\Models' app/Modules/RegistrationModule
rg "DB::table\\(['\"](tickets|events)" app/Modules/RegistrationModule
```

---

# 16. CHECKLIST FINAL ANGGOTA 1

- [ ] Semua tabel milik A memakai UUID + Audit5.
- [ ] Tidak ada direct model access lintas modul.
- [ ] Tiga provider aktif.
- [ ] Auth Sanctum + session berjalan.
- [ ] RBAC diuji.
- [ ] CRUD event/kategori + Form Request.
- [ ] Katalog published-only.
- [ ] EventInfoContract + service + binding.
- [ ] Test dan Pint lulus.
- [ ] Screenshot lengkap.
- [ ] BAB I, BAB II 2.1/2.5, BAB III 3.2/3.5/3.6 selesai.
- [ ] Segmen video selesai.
- [ ] PR direview dan di-merge.
- [ ] Repo public, README, `.env.example` aman.
- [ ] PDF + video + tautan siap diunggah.

---

**Catatan penutup:** dokumen ini adalah panduan eksekusi, bukan bukti implementasi. Semua kode contoh wajib dijalankan, diuji, dan disesuaikan dengan hasil nyata di repository. Jika ada perbedaan antara dokumen ini dan roadmap V4, **roadmap V4 tetap menjadi acuan utama**.
