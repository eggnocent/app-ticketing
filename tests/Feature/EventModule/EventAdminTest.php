<?php

namespace Tests\Feature\EventModule;

use App\Models\User;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_event_routes(): void
    {
        $this->get(route('admin.events.index'))->assertRedirect(route('login'));
    }

    public function test_participant_is_forbidden_from_admin_event_routes(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.events.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_category(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.categories.store'), [
                'name' => 'Seminar',
                'slug' => 'seminar',
                'description' => 'Kategori seminar.',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['slug' => 'seminar']);
    }

    public function test_category_with_events_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Event::factory()->create(['category_id' => $category->id]);

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.categories.destroy', $category))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_an_event_and_the_actor_is_audited(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.events.store'), [
            'category_id' => $category->id,
            'title' => 'Seminar Backend',
            'slug' => 'seminar-backend',
            'description' => 'Seminar tentang backend.',
            'location' => 'Jakarta',
            'start_time' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'end_time' => now()->addDays(2)->addHours(2)->format('Y-m-d\TH:i'),
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.events.index'));

        $event = Event::query()->where('slug', 'seminar-backend')->firstOrFail();

        $this->assertSame($admin->id, $event->created_by);
        $this->assertSame($admin->id, $event->updated_by);
    }

    public function test_event_end_time_must_be_after_start_time(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.events.store'), [
                'category_id' => $category->id,
                'title' => 'Event Salah Waktu',
                'slug' => 'event-salah-waktu',
                'description' => 'Deskripsi.',
                'location' => 'Bandung',
                'start_time' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'end_time' => now()->addDays(1)->format('Y-m-d\TH:i'),
                'status' => 'draft',
            ]);

        $response->assertSessionHasErrors('end_time');
        $this->assertDatabaseMissing('events', ['slug' => 'event-salah-waktu']);
    }

    public function test_event_slug_must_be_unique(): void
    {
        $existing = Event::factory()->create(['slug' => 'event-dup']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.events.store'), [
                'category_id' => $existing->category_id,
                'title' => 'Event Duplikat',
                'slug' => 'event-dup',
                'description' => 'Deskripsi.',
                'location' => 'Surabaya',
                'start_time' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'end_time' => now()->addDays(2)->addHours(1)->format('Y-m-d\TH:i'),
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_admin_can_publish_a_future_event(): void
    {
        $event = Event::factory()->draft()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.events.publish', $event))
            ->assertSessionHas('success');

        $this->assertSame('published', $event->refresh()->status);
    }

    public function test_ended_event_cannot_be_published(): void
    {
        $event = Event::factory()->draft()->create([
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDay(),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.events.publish', $event))
            ->assertSessionHas('error');

        $this->assertSame('draft', $event->refresh()->status);
    }

    public function test_banner_is_stored_on_create_and_removed_on_delete(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('admin.events.store'), [
            'category_id' => $category->id,
            'title' => 'Event Banner',
            'slug' => 'event-banner',
            'description' => 'Deskripsi.',
            'location' => 'Yogyakarta',
            'start_time' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'end_time' => now()->addDays(3)->addHours(2)->format('Y-m-d\TH:i'),
            'status' => 'draft',
            'banner' => UploadedFile::fake()->image('banner.jpg'),
        ]);

        $event = Event::query()->where('slug', 'event-banner')->firstOrFail();

        $this->assertNotNull($event->banner_path);
        Storage::disk('public')->assertExists($event->banner_path);

        $this->actingAs($admin)
            ->delete(route('admin.events.destroy', $event))
            ->assertRedirect(route('admin.events.index'));

        Storage::disk('public')->assertMissing($event->banner_path);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_admin_can_update_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'Lama', 'slug' => 'lama']);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.categories.update', $category), [
                'name' => 'Baru',
                'slug' => 'baru',
                'description' => 'Deskripsi baru.',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Baru',
            'slug' => 'baru',
        ]);
    }

    public function test_admin_can_update_an_event(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create(['title' => 'Judul Lama', 'slug' => 'judul-lama']);

        $this->actingAs($admin)
            ->put(route('admin.events.update', $event), [
                'category_id' => $event->category_id,
                'title' => 'Judul Baru',
                'slug' => 'judul-baru',
                'description' => 'Deskripsi baru.',
                'location' => 'Medan',
                'start_time' => now()->addDays(4)->format('Y-m-d\TH:i'),
                'end_time' => now()->addDays(4)->addHours(2)->format('Y-m-d\TH:i'),
                'status' => 'draft',
            ])
            ->assertRedirect(route('admin.events.index'));

        $event->refresh();

        $this->assertSame('Judul Baru', $event->title);
        $this->assertSame($admin->id, $event->updated_by);
    }

    public function test_admin_can_delete_an_unused_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
