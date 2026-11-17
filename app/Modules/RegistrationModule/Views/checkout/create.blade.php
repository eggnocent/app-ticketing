@extends('layouts.app')

@section('title', 'Checkout — '.$event['title'])

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ $event['category'] ?? 'Event' }}</p>
            <h1 class="mt-1 text-2xl font-semibold">{{ $event['title'] }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                {{ $event['location'] }} —
                {{ \Illuminate\Support\Carbon::parse($event['start_time'])->format('d M Y H:i') }}
            </p>
        </article>

        @if ($errors->isNotEmpty())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (count($tickets) === 0)
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-500 shadow-sm">
                Belum ada tiket yang sedang dijual untuk event ini.
            </div>
        @else
            <form
                method="POST"
                action="{{ route('checkout.store') }}"
                class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">

                <h2 class="text-lg font-semibold">Pilih Tiket</h2>
                <p class="mt-1 text-sm text-slate-500">Total dihitung dari harga database saat checkout.</p>

                <div class="mt-4 space-y-3">
                    @foreach ($tickets as $ticket)
                        @php($maxQuantity = min(10, $ticket['remaining_quota']))
                        <div class="flex flex-wrap items-center justify-between gap-4 rounded-lg border border-slate-200 p-4">
                            <div>
                                <p class="font-medium">{{ $ticket['name'] }}</p>
                                <p class="text-sm text-slate-500">
                                    Rp {{ number_format($ticket['price_rupiah'], 0, ',', '.') }} —
                                    sisa {{ $ticket['remaining_quota'] }} tiket
                                </p>
                            </div>
                            <div>
                                <label class="sr-only" for="qty-{{ $ticket['id'] }}">Jumlah</label>
                                <input
                                    id="qty-{{ $ticket['id'] }}"
                                    type="number"
                                    name="quantities[{{ $ticket['id'] }}]"
                                    value="{{ old('quantities.'.$ticket['id'], 0) }}"
                                    min="0"
                                    max="{{ $maxQuantity }}"
                                    step="1"
                                    @disabled($maxQuantity === 0)
                                    class="w-24 rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center gap-2">
                    <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                        Checkout
                    </button>
                    <a href="{{ route('events.show', $event['slug']) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
                        Batal
                    </a>
                </div>
            </form>
        @endif
    </div>
@endsection
