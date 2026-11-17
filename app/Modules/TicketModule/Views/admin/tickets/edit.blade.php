@extends('layouts.app')

@section('title', 'Edit Tiket')

@section('content')
    <div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">Edit Tiket</h1>

        <div class="mt-6">
            @include('ticket::admin.tickets._form', ['ticket' => $ticket])
        </div>
    </div>
@endsection
