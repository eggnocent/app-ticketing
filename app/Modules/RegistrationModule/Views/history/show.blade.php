@extends('layouts.app')

@section('title', 'E-Ticket — '.$registration->invoice_number)

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">E-Ticket</p>
                    <h1 class="mt-1 text-2xl font-semibold">{{ $registration->invoice_number }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $registration->user->name }}</p>
                </div>
                <div class="text-right">
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                        {{ $registration->status }}
                    </span>
                    <p class="mt-2 font-semibold">Rp {{ number_format($registration->total_rupiah, 0, ',', '.') }}</p>
                </div>
            </div>

            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Waktu Checkout</dt>
                    <dd class="font-medium">{{ $registration->confirmed_at?->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Total Item</dt>
                    <dd class="font-medium">{{ $registration->issuedTickets->count() }} tiket</dd>
                </div>
            </dl>
        </article>

        <div class="space-y-4">
            @foreach ($items as $item)
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-medium">{{ $item['event']['title'] ?? 'Event' }}</p>
                            <p class="text-sm text-slate-500">{{ $item['ticket']['name'] ?? 'Tiket' }}</p>
                        </div>
                        <div class="text-right text-sm">
                            <p class="font-medium">
                                {{ $item['line']->quantity }} ×
                                Rp {{ number_format($item['line']->unit_price_rupiah, 0, ',', '.') }}
                            </p>
                            <p class="font-semibold">
                                Rp {{ number_format($item['line']->subtotal_rupiah, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($item['line']->issuedTickets as $issued)
                            <div class="rounded-lg border border-dashed border-slate-300 p-3">
                                <p class="font-mono text-sm font-semibold">{{ $issued->ticket_code }}</p>
                                <p class="mt-1 text-xs uppercase tracking-wide text-slate-500">
                                    {{ $issued->status }}
                                    @if ($issued->checked_in_at)
                                        — {{ $issued->checked_in_at->format('d M Y H:i') }}
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <a href="{{ route('registrations.index') }}" class="inline-block rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
            Kembali ke pesanan saya
        </a>
    </div>
@endsection
