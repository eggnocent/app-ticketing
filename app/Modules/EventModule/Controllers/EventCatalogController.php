<?php

namespace App\Modules\EventModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Models\Event;
use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use Illuminate\View\View;

class EventCatalogController extends Controller
{
    public function __construct(private readonly TicketInventoryContract $tickets) {}

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

        $tickets = $this->tickets->getEventTickets($event->id);

        return view('event::public.show', compact('event', 'tickets'));
    }
}
