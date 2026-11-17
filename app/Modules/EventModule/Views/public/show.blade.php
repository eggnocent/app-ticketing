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
            <p class="mt-1 text-sm text-slate-500">
                Informasi harga dan sisa tiket akan tampil setelah modul tiket diintegrasikan.
            </p>
        </section>

        <a href="{{ route('events.index') }}" class="inline-block rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
            Kembali ke daftar event
        </a>
    </div>
@endsection
