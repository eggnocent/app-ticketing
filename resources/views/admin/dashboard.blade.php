@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">Dashboard Admin</h1>
        <p class="mt-1 text-sm text-slate-500">
            Selamat datang, {{ auth()->user()->name }}. Pengelolaan kategori dan event tersedia pada fase berikutnya.
        </p>
    </div>
@endsection
