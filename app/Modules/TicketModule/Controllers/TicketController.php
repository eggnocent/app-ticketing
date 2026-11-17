<?php

namespace App\Modules\TicketModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\TicketModule\Models\Ticket;
use App\Modules\TicketModule\Requests\StoreTicketRequest;
use App\Modules\TicketModule\Requests\UpdateTicketRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(private readonly EventInfoContract $events) {}

    public function index(): View
    {
        $tickets = Ticket::query()
            ->orderBy('event_id')
            ->orderBy('price_rupiah')
            ->paginate(10);

        $eventTitles = collect($this->events->getEventOptions())->pluck('title', 'id');

        return view('ticket::admin.tickets.index', compact('tickets', 'eventTitles'));
    }

    public function create(): View
    {
        $eventOptions = $this->events->getEventOptions();

        return view('ticket::admin.tickets.create', compact('eventOptions'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['remaining_quota'] = $data['initial_quota'];

        Ticket::create($data);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil dibuat.');
    }

    public function edit(Ticket $ticket): View
    {
        $eventOptions = $this->events->getEventOptions();

        return view('ticket::admin.tickets.edit', compact('ticket', 'eventOptions'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();

        $sold = $ticket->initial_quota - $ticket->remaining_quota;
        $data['remaining_quota'] = $data['initial_quota'] - $sold;

        $ticket->update($data);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $sold = $ticket->initial_quota - $ticket->remaining_quota;

        if ($sold > 0) {
            return back()->with('error', 'Tiket yang sudah pernah terjual tidak bisa dihapus.');
        }

        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Tiket berhasil dihapus.');
    }
}
