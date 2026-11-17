<?php

namespace App\Modules\EventModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Models\Event;
use Illuminate\View\View;

class EventCatalogController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->published()
            ->with('category')
            ->orderBy('start_time')
            ->paginate(9);

        return view('event::public.index', compact('events'));
    }

    public function show(string $slug): View
    {
        $event = Event::query()
            ->published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('event::public.show', compact('event'));
    }
}
