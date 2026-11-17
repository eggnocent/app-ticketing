@extends('layouts.app')

@section('title', 'Kelola Event')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">Kelola Event</h1>
            <p class="text-sm text-slate-500">Buat, ubah, publikasikan, dan hapus event.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Tambah Event
        </a>
    </div>

    <form method="GET" action="{{ route('admin.events.index') }}" class="mb-4 flex items-end gap-2">
        <div>
            <label for="status" class="block text-xs font-medium uppercase tracking-wide text-slate-500">Filter status</label>
            <select id="status" name="status" class="mt-1 rounded-md border border-slate-300 px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach (['draft', 'published', 'cancelled'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-sm hover:bg-slate-50">
            Terapkan
        </button>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Mulai</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($events as $event)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $event->title }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $event->category?->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $event->start_time?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <a href="{{ route('admin.events.show', $event) }}" class="rounded-md border border-slate-300 px-2 py-1 text-xs hover:bg-slate-50">
                                    Lihat
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="rounded-md border border-slate-300 px-2 py-1 text-xs hover:bg-slate-50">
                                    Edit
                                </a>
                                @if ($event->status !== 'published')
                                    <form method="POST" action="{{ route('admin.events.publish', $event) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-md border border-green-300 px-2 py-1 text-xs text-green-700 hover:bg-green-50">
                                            Publish
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada event.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $events->links() }}
    </div>
@endsection
