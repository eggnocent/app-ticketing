@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="mx-auto max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">Tambah Kategori</h1>

        <div class="mt-6">
            @include('event::admin.categories._form')
        </div>
    </div>
@endsection
