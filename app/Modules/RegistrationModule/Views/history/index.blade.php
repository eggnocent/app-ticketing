@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="space-y-4">
        <div>
            <h1 class="text-xl font-semibold">Pesanan Saya</h1>
            <p class="text-sm text-slate-500">Riwayat checkout dan e-ticket Anda.</p>
        </div>

        <div class="space-y-3">
            @forelse ($registrations as $registration)
                <a
                    href="{{ route('registrations.show', $registration) }}"
                    class="block rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:border-slate-300"
                >
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-medium">{{ $registration->invoice_number }}</p>
                            <p class="text-sm text-slate-500">{{ $registration->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">Rp {{ number_format($registration->total_rupiah, 0, ',', '.') }}</p>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                {{ $registration->status }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-slate-200 bg-white p-6 text-sm text-slate-500 shadow-sm">
                    Anda belum punya pesanan.
                </div>
            @endforelse
        </div>

        <div>
            {{ $registrations->links() }}
        </div>
    </div>
@endsection
