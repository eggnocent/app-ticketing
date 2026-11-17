@extends('layouts.app')

@section('title', 'Kategori Event')

@section('content')
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold">Kategori Event</h1>
            <p class="text-sm text-slate-500">Kelola kategori untuk pengelompokan event.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Tambah Kategori
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Jumlah Event</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $category->slug }}</td>
                        <td class="px-4 py-3">{{ $category->events_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="rounded-md border border-slate-300 px-2 py-1 text-xs hover:bg-slate-50">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
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
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection
