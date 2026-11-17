@extends('layouts.app')

@section('title', 'Tambah Event')

@section('content')
    <div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">Tambah Event</h1>

        <div class="mt-6">
            @include('event::admin.events._form')
        </div>
    </div>
@endsection
