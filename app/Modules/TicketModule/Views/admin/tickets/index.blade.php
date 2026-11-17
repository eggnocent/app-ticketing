@extends('layouts.app')

@section('title', 'Kelola Tiket')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Kelola Tiket</h1>
            <p class="mt-1 text-sm text-slate-500">Atur harga, kuota, dan periode penjualan tiket untuk setiap event.</p>
        </div>
        <a
            href="{{ route('admin.tickets.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-slate-800 active:scale-[0.98] transition-all duration-120 ease-out"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Tiket
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5 font-semibold">Event</th>
                        <th class="px-5 py-3.5 font-semibold">Nama Tiket</th>
                        <th class="px-5 py-3.5 font-semibold">Harga</th>
                        <th class="px-5 py-3.5 font-semibold">Sisa / Total Kuota</th>
                        <th class="px-5 py-3.5 font-semibold">Periode Penjualan</th>
                        <th class="px-5 py-3.5 font-semibold">Status</th>
                        <th class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-slate-50/60 transition-colors duration-100">
                            <td class="px-5 py-4 font-medium text-slate-900">
                                {{ $eventTitles[$ticket->event_id] ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-slate-800 font-medium">
                                {{ $ticket->name }}
                            </td>
                            <td class="px-5 py-4 tabular-nums font-semibold text-slate-900">
                                Rp {{ number_format($ticket->price_rupiah, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="tabular-nums font-medium text-slate-900">{{ $ticket->remaining_quota }}</span>
                                    <span class="text-xs text-slate-400">/ {{ $ticket->initial_quota }}</span>
                                    @if ($ticket->remaining_quota === 0)
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700 border border-rose-200/60">
                                            Habis
                                        </span>
                                    @elseif ($ticket->remaining_quota <= 5)
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 border border-amber-200/60">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 border border-emerald-200/60">
                                            Tersedia
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500 tabular-nums">
                                @if ($ticket->sales_start && $ticket->sales_end)
                                    {{ $ticket->sales_start->format('d M Y') }} — {{ $ticket->sales_end->format('d M Y') }}
                                @else
                                    <span class="text-slate-400">Tidak dibatasi</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($ticket->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 border border-emerald-200/60">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 border border-slate-200/60">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('admin.tickets.edit', $ticket) }}"
                                        class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 active:scale-[0.98] transition-all duration-120"
                                    >
                                        Edit
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('admin.tickets.destroy', $ticket) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-md border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50 active:scale-[0.98] transition-all duration-120"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-900">Belum ada tiket yang dibuat</h3>
                                <p class="mt-1 text-sm text-slate-500">Mulai alokasikan kuota dan harga tiket untuk event yang tersedia.</p>
                                <div class="mt-4">
                                    <a
                                        href="{{ route('admin.tickets.create') }}"
                                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800 active:scale-[0.98] transition-all duration-120"
                                    >
                                        Tambah Tiket Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($tickets->hasPages())
        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    @endif
@endsection
