@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($event->banner_path)
                <img src="{{ asset('storage/'.$event->banner_path) }}" alt="Banner {{ $event->title }}" class="max-h-80 w-full object-cover">
            @endif

            <div class="p-6">
                <p class="text-xs uppercase tracking-wide text-slate-500">{{ $event->category?->name }}</p>
                <h1 class="mt-1 text-3xl font-semibold">{{ $event->title }}</h1>

                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Lokasi</dt>
                        <dd class="font-medium">{{ $event->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Jadwal</dt>
                        <dd class="font-medium">
                            {{ $event->start_time?->format('d M Y H:i') }} — {{ $event->end_time?->format('d M Y H:i') }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $event->description }}</div>
            </div>
        </article>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Tiket</h2>

            <div class="mt-4 space-y-3">
                @forelse ($tickets as $ticket)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 p-4">
                        <div>
                            <p class="font-medium">{{ $ticket['name'] }}</p>
                            <p class="text-sm text-slate-500">
                                Penjualan {{ \Illuminate\Support\Carbon::parse($ticket['sales_start'])->format('d M Y H:i') }}
                                — {{ \Illuminate\Support\Carbon::parse($ticket['sales_end'])->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">Rp {{ number_format($ticket['price_rupiah'], 0, ',', '.') }}</p>
                            @if ($ticket['remaining_quota'] === 0)
                                <p class="text-sm font-medium text-red-600">Habis</p>
                            @elseif (! $ticket['is_on_sale'])
                                <p class="text-sm text-slate-500">Penjualan ditutup</p>
                            @else
                                <p class="text-sm text-slate-500">Sisa {{ $ticket['remaining_quota'] }} tiket</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Tiket belum tersedia untuk event ini.</p>
                @endforelse
            </div>
        </section>

        <a href="{{ route('events.index') }}" class="inline-block rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
            Kembali ke daftar event
        </a>
    </div>
@endsection
