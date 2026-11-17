@php($editing = isset($category))

<form
    method="POST"
    action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    class="space-y-4"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Nama</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $editing ? $category->name : '') }}"
            required
            class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
        >
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-slate-700">Slug</label>
        <input
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $editing ? $category->slug : '') }}"
            required
            class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
        >
        @error('slug')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
        >{{ old('description', $editing ? $category->description : '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Simpan
        </button>
        <a href="{{ route('admin.categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
            Batal
        </a>
    </div>
</form>
