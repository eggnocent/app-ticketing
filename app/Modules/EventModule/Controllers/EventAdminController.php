<?php

namespace App\Modules\EventModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use App\Modules\EventModule\Requests\StoreEventRequest;
use App\Modules\EventModule\Requests\UpdateEventRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $events = Event::query()
            ->with('category')
            ->when(
                in_array($status, ['draft', 'published', 'cancelled'], true),
                fn ($query) => $query->where('status', $status),
            )
            ->orderByDesc('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('event::admin.events.index', compact('events', 'status'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('event::admin.events.create', compact('categories'));
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['banner']);

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event): View
    {
        $event->load('category');

        return view('event::admin.events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('event::admin.events.edit', compact('event', 'categories'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        unset($data['banner']);

        if ($request->hasFile('banner')) {
            if ($event->banner_path) {
                Storage::disk('public')->delete($event->banner_path);
            }

            $data['banner_path'] = $request->file('banner')->store('banners', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->banner_path) {
            Storage::disk('public')->delete($event->banner_path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    public function publish(Event $event): RedirectResponse
    {
        if ($event->end_time->isPast()) {
            return back()->with('error', 'Event yang sudah berakhir tidak bisa dipublikasikan.');
        }

        $event->update(['status' => 'published']);

        return back()->with('success', 'Event berhasil dipublikasikan.');
    }
}
