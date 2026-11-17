@php($editing = isset($event))
@php($selectedTags = old('tags', $editing ? $event->tags->pluck('id')->all() : []))

<form
    method="POST"
    action="{{ $editing ? route('admin.events.update', $event) : route('admin.events.store') }}"
    enctype="multipart/form-data"
    class="space-y-4"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="title" class="block text-sm font-medium text-slate-700">Judul</label>
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $editing ? $event->title : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-slate-700">Slug</label>
            <input
                id="slug"
                name="slug"
                type="text"
                value="{{ old('slug', $editing ? $event->slug : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('slug')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium text-slate-700">Kategori</label>
            <select
                id="category_id"
                name="category_id"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $editing ? $event->category_id : '') === $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <fieldset class="sm:col-span-2">
            <legend class="block text-sm font-medium text-slate-700">Tag</legend>
            <p class="mt-1 text-sm text-slate-500">Pilih satu atau lebih tag untuk mengelompokkan event.</p>

            @if ($tags->isEmpty())
                <p class="mt-2 text-sm text-slate-500">Belum ada tag. Tambahkan tag terlebih dahulu lewat seeder atau data master.</p>
            @else
                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    @foreach ($tags as $tag)
                        <label class="flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm">
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->id }}"
                                @checked(in_array($tag->id, $selectedTags, true))
                                class="h-4 w-4 rounded border-slate-300"
                            >
                            <span>{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif

            @error('tags')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('tags.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </fieldset>

        <div>
            <label for="location" class="block text-sm font-medium text-slate-700">Lokasi</label>
            <input
                id="location"
                name="location"
                type="text"
                value="{{ old('location', $editing ? $event->location : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('location')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
            <select
                id="status"
                name="status"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                @foreach (['draft', 'published', 'cancelled'] as $option)
                    <option value="{{ $option }}" @selected(old('status', $editing ? $event->status : 'draft') === $option)>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="start_time" class="block text-sm font-medium text-slate-700">Mulai</label>
            <input
                id="start_time"
                name="start_time"
                type="datetime-local"
                value="{{ old('start_time', $editing ? $event->start_time?->format('Y-m-d\TH:i') : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('start_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="end_time" class="block text-sm font-medium text-slate-700">Selesai</label>
            <input
                id="end_time"
                name="end_time"
                type="datetime-local"
                value="{{ old('end_time', $editing ? $event->end_time?->format('Y-m-d\TH:i') : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('end_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >{{ old('description', $editing ? $event->description : '') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="banner" class="block text-sm font-medium text-slate-700">Banner (opsional)</label>
            <input
                id="banner"
                name="banner"
                type="file"
                accept="image/*"
                class="mt-1 block w-full text-sm text-slate-600"
            >
            @error('banner')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($editing && $event->banner_path)
                <img src="{{ asset('storage/'.$event->banner_path) }}" alt="Banner {{ $event->title }}" class="mt-3 h-32 rounded-md object-cover">
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Simpan
        </button>
        <a href="{{ route('admin.events.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
            Batal
        </a>
    </div>
</form>
