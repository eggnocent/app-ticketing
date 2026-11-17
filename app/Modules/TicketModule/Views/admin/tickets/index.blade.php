@extends('layouts.app')

@section('title', 'Kelola Tiket')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">Kelola Tiket</h1>
            <p class="text-sm text-slate-500">Atur harga, kuota, dan periode penjualan tiket.</p>
        </div>
        <a href="{{ route('admin.tickets.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Tambah Tiket
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Event</th>
                    <th class="px-4 py-3">Tiket</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Kuota</th>
                    <th class="px-4 py-3">Penjualan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="px-4 py-3">{{ $eventTitles[$ticket->event_id] ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $ticket->name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($ticket->price_rupiah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $ticket->remaining_quota }} / {{ $ticket->initial_quota }}</td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ $ticket->sales_start?->format('d M Y') }} — {{ $ticket->sales_end?->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="rounded-md border border-slate-300 px-2 py-1 text-xs hover:bg-slate-50">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}">
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
                        <td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada tiket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
@endsection
