@extends('layouts.app')

@section('title', 'Rekap Pendaftaran')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold">Rekap Pendaftaran</h1>
                <p class="text-sm text-slate-500">Daftar semua registrasi yang berhasil.</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Registrasi</p>
                <p class="text-2xl font-semibold">{{ number_format($totalRegistrations, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Nilai</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Invoice</th>
                        <th class="px-4 py-3">Peserta</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registrations as $registration)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $registration->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $registration->user?->name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($registration->total_rupiah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $registration->status }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $registration->created_at?->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada registrasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $registrations->links() }}
        </div>
    </div>
@endsection
