@php($editing = isset($ticket))

<form
    method="POST"
    action="{{ $editing ? route('admin.tickets.update', $ticket) : route('admin.tickets.store') }}"
    class="space-y-4"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="event_id" class="block text-sm font-medium text-slate-700">Event</label>
            <select
                id="event_id"
                name="event_id"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
                <option value="">Pilih event</option>
                @foreach ($eventOptions as $option)
                    <option value="{{ $option['id'] }}" @selected(old('event_id', $editing ? $ticket->event_id : '') === $option['id'])>
                        {{ $option['title'] }} ({{ $option['status'] }})
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nama Tiket</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $editing ? $ticket->name : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price_rupiah" class="block text-sm font-medium text-slate-700">Harga (rupiah)</label>
            <input
                id="price_rupiah"
                name="price_rupiah"
                type="number"
                min="0"
                step="1"
                value="{{ old('price_rupiah', $editing ? $ticket->price_rupiah : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('price_rupiah')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="initial_quota" class="block text-sm font-medium text-slate-700">Kuota Awal</label>
            <input
                id="initial_quota"
                name="initial_quota"
                type="number"
                min="0"
                step="1"
                value="{{ old('initial_quota', $editing ? $ticket->initial_quota : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @if ($editing)
                <p class="mt-1 text-xs text-slate-500">
                    Terjual: {{ $ticket->initial_quota - $ticket->remaining_quota }}, sisa: {{ $ticket->remaining_quota }}.
                </p>
            @endif
            @error('initial_quota')
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
                @foreach (['active', 'inactive'] as $option)
                    <option value="{{ $option }}" @selected(old('status', $editing ? $ticket->status : 'active') === $option)>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sales_start" class="block text-sm font-medium text-slate-700">Mulai Penjualan</label>
            <input
                id="sales_start"
                name="sales_start"
                type="datetime-local"
                value="{{ old('sales_start', $editing ? $ticket->sales_start?->format('Y-m-d\TH:i') : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('sales_start')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sales_end" class="block text-sm font-medium text-slate-700">Selesai Penjualan</label>
            <input
                id="sales_end"
                name="sales_end"
                type="datetime-local"
                value="{{ old('sales_end', $editing ? $ticket->sales_end?->format('Y-m-d\TH:i') : '') }}"
                required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
            >
            @error('sales_end')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            Simpan
        </button>
        <a href="{{ route('admin.tickets.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">
            Batal
        </a>
    </div>
</form>
