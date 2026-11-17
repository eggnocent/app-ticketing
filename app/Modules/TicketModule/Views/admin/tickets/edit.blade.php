@extends('layouts.app')

@section('title', 'Edit Tiket: ' . $ticket->name)

@section('content')
    <div class="mx-auto max-w-3xl rounded-xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit Konfigurasi Tiket</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui kuota, harga, atau status penjualan untuk tiket <strong class="text-slate-800">{{ $ticket->name }}</strong>.</p>
        </div>

        <div>
            @include('ticket::admin.tickets._form')
        </div>
    </div>
@endsection
