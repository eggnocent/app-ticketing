<?php

namespace App\Modules\RegistrationModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Models\RegistrationTicket;
use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly EventInfoContract $events,
        private readonly TicketInventoryContract $tickets,
    ) {}

    public function index(Request $request): View
    {
        $registrations = Registration::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('registration::history.index', compact('registrations'));
    }

    public function show(Request $request, Registration $registration): View
    {
        abort_unless($registration->user_id === $request->user()->id, 403);

        $registration->load('registrationTickets.issuedTickets');

        $items = $registration->registrationTickets->map(function (RegistrationTicket $line): array {
            $ticket = $this->tickets->getSummary($line->ticket_id);
            $event = $ticket ? $this->events->getSummary($ticket['event_id']) : null;

            return [
                'line' => $line,
                'ticket' => $ticket,
                'event' => $event,
            ];
        });

        return view('registration::history.show', compact('registration', 'items'));
    }
}
