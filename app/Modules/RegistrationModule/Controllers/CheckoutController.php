<?php

namespace App\Modules\RegistrationModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\RegistrationModule\Requests\CheckoutRequest;
use App\Modules\RegistrationModule\Services\CheckoutService;
use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly EventInfoContract $events,
        private readonly TicketInventoryContract $tickets,
    ) {}

    public function create(string $eventSlug): View
    {
        $option = collect($this->events->getEventOptions())
            ->firstWhere('slug', $eventSlug);

        abort_unless($option && $this->events->isPurchasable($option['id']), 404);

        $event = $this->events->getSummary($option['id']);
        $tickets = collect($this->tickets->getEventTickets($option['id']))
            ->filter(fn (array $ticket) => $ticket['is_on_sale'])
            ->values()
            ->all();

        return view('registration::checkout.create', compact('event', 'tickets'));
    }

    public function store(CheckoutRequest $request, CheckoutService $checkout): RedirectResponse
    {
        $registration = $checkout->process(
            $request->user()->id,
            $request->validated('items'),
        );

        return redirect()
            ->route('registrations.show', $registration)
            ->with('success', 'Checkout berhasil. E-ticket Anda sudah diterbitkan.');
    }
}
