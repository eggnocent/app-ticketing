<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_use_uuid_and_default_role(): void
    {
        $user = User::create([
            'name' => 'Peserta Baru',
            'email' => 'peserta.baru@example.test',
            'password' => 'password',
        ]);

        $this->assertTrue(Str::isUuid($user->id));

        $user->refresh();

        $this->assertSame('peserta', $user->role);
    }

    public function test_admin_factory_state_sets_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->isAdmin());
    }

    public function test_category_and_event_use_uuid_and_relations(): void
    {
        $category = Category::factory()->create();
        $event = Event::factory()->published()->create([
            'category_id' => $category->id,
        ]);

        $this->assertTrue(Str::isUuid($category->id));
        $this->assertTrue(Str::isUuid($event->id));
        $this->assertTrue($category->is($event->category));
        $this->assertCount(1, $category->events);
    }

    public function test_audit_columns_follow_the_authenticated_actor(): void
    {
        $adminA = User::factory()->admin()->create();
        $adminB = User::factory()->admin()->create();

        $this->actingAs($adminA);

        $event = Event::factory()->create();

        $this->assertSame($adminA->id, $event->created_by);
        $this->assertSame($adminA->id, $event->updated_by);

        $this->actingAs($adminB);

        $event->update(['title' => 'Judul Diubah Admin B']);

        $this->assertSame($adminA->id, $event->created_by);
        $this->assertSame($adminB->id, $event->updated_by);
    }

    public function test_audit_columns_are_null_for_guest_created_records(): void
    {
        $category = Category::factory()->create();

        $this->assertNull($category->created_by);
        $this->assertNull($category->updated_by);
    }

    public function test_category_slug_must_be_unique(): void
    {
        Category::factory()->create(['slug' => 'musik']);

        $this->expectException(QueryException::class);

        Category::factory()->create(['slug' => 'musik']);
    }

    public function test_seeder_produces_the_expected_demo_data(): void
    {
        $this->seed();

        $this->assertSame(3, User::count());
        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertSame(2, User::where('role', 'peserta')->count());

        $this->assertSame(3, Category::count());
        $this->assertSame(5, Event::count());
        $this->assertSame(3, Event::published()->count());
        $this->assertSame(1, Event::where('status', 'draft')->count());
        $this->assertSame(1, Event::where('status', 'cancelled')->count());
    }
}
