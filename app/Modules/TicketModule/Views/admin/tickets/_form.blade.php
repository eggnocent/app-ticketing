@php($editing = isset($ticket))

<form
    method="POST"
    action="{{ $editing ? route('admin.tickets.update', $ticket) : route('admin.tickets.store') }}"
    class="space-y-6"
>
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="event_id" class="block text-sm font-semibold text-slate-800">Event Penyelenggara</label>
            <select
                id="event_id"
                name="event_id"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            >
                <option value="">-- Pilih Event --</option>
                @foreach ($eventOptions as $option)
                    <option value="{{ $option['id'] }}" @selected(old('event_id', $editing ? $ticket->event_id : '') === $option['id'])>
                        {{ $option['title'] }} ({{ ucfirst($option['status']) }})
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-semibold text-slate-800">Nama Tipe Tiket</label>
            <input
                id="name"
                name="name"
                type="text"
                placeholder="Contoh: Early Bird / VIP / Regular"
                value="{{ old('name', $editing ? $ticket->name : '') }}"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            >
            @error('name')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price_rupiah" class="block text-sm font-semibold text-slate-800">Harga Tiket (Rupiah)</label>
            <div class="relative mt-1.5">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-400">Rp</span>
                <input
                    id="price_rupiah"
                    name="price_rupiah"
                    type="number"
                    min="0"
                    step="1"
                    placeholder="0 untuk tiket gratis"
                    value="{{ old('price_rupiah', $editing ? $ticket->price_rupiah : '') }}"
                    required
                    class="w-full rounded-lg border border-slate-300 pl-10 pr-3.5 py-2 text-sm text-slate-900 shadow-sm tabular-nums transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
                >
            </div>
            @error('price_rupiah')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="initial_quota" class="block text-sm font-semibold text-slate-800">Alokasi Kuota Awal</label>
            <input
                id="initial_quota"
                name="initial_quota"
                type="number"
                min="0"
                step="1"
                placeholder="Jumlah tiket tersedia"
                value="{{ old('initial_quota', $editing ? $ticket->initial_quota : '') }}"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-900 shadow-sm tabular-nums transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            >
            @if ($editing)
                <p class="mt-1 text-xs text-slate-500">
                    Tiket terjual: <strong class="text-slate-800">{{ $ticket->initial_quota - $ticket->remaining_quota }}</strong>, sisa kuota: <strong class="text-slate-800">{{ $ticket->remaining_quota }}</strong>.
                </p>
            @endif
            @error('initial_quota')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-semibold text-slate-800">Status Penjualan</label>
            <select
                id="status"
                name="status"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            >
                <option value="active" @selected(old('status', $editing ? $ticket->status : 'active') === 'active')>Aktif (Dapat Dipesan)</option>
                <option value="inactive" @selected(old('status', $editing ? $ticket->status : 'active') === 'inactive')>Nonaktif (Ditutup Sementara)</option>
            </select>
            @error('status')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sales_start" class="block text-sm font-semibold text-slate-800">Jadwal Mulai Penjualan</label>
            <input
                id="sales_start"
                name="sales_start"
                type="datetime-local"
                value="{{ old('sales_start', $editing ? $ticket->sales_start?->format('Y-m-d\TH:i') : '') }}"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            >
            @error('sales_start')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sales_end" class="block text-sm font-semibold text-slate-800">Jadwal Selesai Penjualan</label>
            <input
                id="sales_end"
                name="sales_end"
                type="datetime-local"
                value="{{ old('sales_end', $editing ? $ticket->sales_end?->format('Y-m-d\TH:i') : '') }}"
                required
                class="mt-1.5 w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900"
            >
            @error('sales_end')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button
            type="submit"
            class="inline-flex items-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-slate-800 active:scale-[0.98] transition-all duration-120 ease-out"
        >
            {{ $editing ? 'Perbarui Tiket' : 'Simpan Tiket' }}
        </button>
        <a
            href="{{ route('admin.tickets.index') }}"
            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 active:scale-[0.98] transition-all duration-120"
        >
            Batal
        </a>
    </div>
</form>
