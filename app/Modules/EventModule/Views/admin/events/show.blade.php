@extends('layouts.app')

@section('title', 'Detail Event')

@section('content')
    <div class="mx-auto max-w-3xl space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ $event->category?->name }}</p>
                    <h1 class="text-2xl font-semibold">{{ $event->title }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $event->slug }}</p>

                    @if ($event->tags->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($event->tags as $tag)
                                <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-600">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                    {{ $event->status }}
                </span>
            </div>

            @if ($event->banner_path)
                <img src="{{ asset('storage/'.$event->banner_path) }}" alt="Banner {{ $event->title }}" class="mt-4 max-h-72 w-full rounded-lg object-cover">
            @endif

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

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.events.edit', $event) }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                Edit
            </a>

            @if ($event->status !== 'published')
                <form method="POST" action="{{ route('admin.events.publish', $event) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-md border border-green-300 px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                        Publikasikan
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.events.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </div>
@endsection
