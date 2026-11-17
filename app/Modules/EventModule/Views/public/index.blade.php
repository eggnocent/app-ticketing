@extends('layouts.app')

@section('title', 'Daftar Event')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Event Mendatang</h1>
        <p class="mt-1 text-sm text-slate-500">Hanya event berstatus published yang ditampilkan.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            <article class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                @if ($event->banner_path)
                    <img src="{{ asset('storage/'.$event->banner_path) }}" alt="Banner {{ $event->title }}" class="h-40 w-full object-cover">
                @else
                    <div class="flex h-40 items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 text-sm font-medium text-slate-600">
                        {{ $event->category?->name }}
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ $event->category?->name }}</p>
                    <h2 class="mt-1 text-lg font-semibold">{{ $event->title }}</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $event->start_time?->format('d M Y H:i') }} · {{ $event->location }}
                    </p>
                    <p class="mt-3 flex-1 text-sm text-slate-600">
                        {{ \Illuminate\Support\Str::limit($event->description, 110) }}
                    </p>
                    <a href="{{ route('events.show', $event->slug) }}" class="mt-4 inline-flex items-center justify-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">
                        Lihat Detail
                    </a>
                </div>
            </article>
        @empty
            <p class="text-sm text-slate-500">Belum ada event published.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $events->links() }}
    </div>
@endsection
