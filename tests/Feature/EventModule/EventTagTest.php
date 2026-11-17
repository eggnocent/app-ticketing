<?php

namespace Tests\Feature\EventModule;

use App\Models\User;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\EventModule\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_and_tag_share_a_many_to_many_relation_with_uuid_pivot(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->actingAs($admin);

        $event->tags()->attach($tags->pluck('id'));

        $event->refresh();

        $this->assertCount(2, $event->tags);
        $this->assertCount(1, $tags->first()->fresh()->events);

        $pivot = $event->tags->first()->pivot;

        $this->assertTrue(Str::isUuid($pivot->id));
        $this->assertSame($admin->id, $pivot->created_by);
        $this->assertSame($admin->id, $pivot->updated_by);
        $this->assertNotNull($pivot->created_at);
        $this->assertNotNull($pivot->updated_at);
    }

    public function test_admin_can_assign_tags_when_creating_an_event(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->actingAs($admin)
            ->post(route('admin.events.store'), $this->eventPayload($category->id, [
                'tags' => $tags->pluck('id')->all(),
            ]))
            ->assertRedirect(route('admin.events.index'));

        $event = Event::query()->where('slug', 'seminar-tag')->firstOrFail();

        $this->assertCount(2, $event->tags);
        $this->assertDatabaseHas('event_tag', [
            'event_id' => $event->id,
            'tag_id' => $tags->first()->id,
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_replace_event_tags_on_update(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $oldTag = Tag::factory()->create();
        $newTag = Tag::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $event->tags()->attach($oldTag->id);

        $this->actingAs($admin)
            ->put(route('admin.events.update', $event), $this->eventPayload($category->id, [
                'tags' => [$newTag->id],
            ]))
            ->assertRedirect(route('admin.events.index'));

        $event->refresh();

        $this->assertTrue($event->tags->contains($newTag));
        $this->assertFalse($event->tags->contains($oldTag));
        $this->assertDatabaseHas('event_tag', [
            'event_id' => $event->id,
            'tag_id' => $newTag->id,
            'created_by' => $admin->id,
        ]);
    }

    public function test_event_tag_rejects_duplicate_pairs(): void
    {
        $event = Event::factory()->create();
        $tag = Tag::factory()->create();

        $event->tags()->attach($tag->id);

        $this->expectException(QueryException::class);

        $event->tags()->attach($tag->id);
    }

    public function test_tags_must_exist_when_assigning(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.events.store'), $this->eventPayload($category->id, [
                'tags' => ['00000000-0000-0000-0000-000000000000'],
            ]))
            ->assertSessionHasErrors('tags.0');
    }

    public function test_public_event_page_lists_its_tags(): void
    {
        $event = Event::factory()->published()->create();
        $tag = Tag::factory()->create(['name' => 'Musik']);
        $event->tags()->attach($tag->id);

        $this->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertSee('Musik');
    }

    public function test_deleting_an_event_removes_its_tag_links_only(): void
    {
        $event = Event::factory()->create();
        $tag = Tag::factory()->create();
        $event->tags()->attach($tag->id);

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.events.destroy', $event))
            ->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseMissing('event_tag', ['event_id' => $event->id]);
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    private function eventPayload(string $categoryId, array $overrides = []): array
    {
        return array_merge([
            'category_id' => $categoryId,
            'title' => 'Seminar Tag',
            'slug' => 'seminar-tag',
            'description' => 'Seminar untuk pengujian tag.',
            'location' => 'Yogyakarta',
            'start_time' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'end_time' => now()->addDays(2)->addHours(2)->format('Y-m-d\TH:i'),
            'status' => 'draft',
        ], $overrides);
    }
}
