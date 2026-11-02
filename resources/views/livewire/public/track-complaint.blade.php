<div class="space-y-8">
    <!-- Header Section -->
    <div class="text-center space-y-3">
        <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            Pelacakan Aduan Publik
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
            Lacak Aduan Jangkau Samawa
        </h1>
        <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-400 max-w-xl mx-auto">
            Masukkan kode tiket aduan Anda untuk melihat status penanganan.
        </p>
    </div>

    <!-- Tracking Form Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm p-6 sm:p-8">
        <form wire:submit="track" class="space-y-4">
            <div>
                <label for="ticket_code" class="block text-sm font-semibold text-gray-700 dark:text-zinc-300 mb-1.5">
                    Kode Tiket Aduan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="text"
                        id="ticket_code"
                        wire:model="form.ticket_code"
                        placeholder="Contoh: JS-2026-0001"
                        autocomplete="off"
                        class="w-full rounded-xl border @error('form.ticket_code') border-red-500 focus:ring-red-500 focus:border-red-500 @else border-gray-300 dark:border-zinc-700 focus:ring-blue-500 focus:border-blue-500 @enderror bg-white dark:bg-zinc-800/80 px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 shadow-xs uppercase tracking-wider font-mono focus:outline-none focus:ring-2"
                    />
                </div>
                @error('form.ticket_code')
                    <p class="mt-1.5 text-xs text-red-600 font-medium" data-test="validation-error">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- WhatsApp Note -->
            <div class="flex items-start gap-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 p-3.5 border border-blue-100 dark:border-blue-900/50">
                <svg class="size-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <p class="text-xs text-blue-800 dark:text-blue-300 leading-relaxed">
                    Petunjuk: Kode tiket dikirim melalui WhatsApp saat aduan pertama kali didaftarkan oleh sistem Dinas Sosial.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button
                    type="submit"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <svg wire:loading.remove wire:target="track" class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <svg wire:loading wire:target="track" class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Lacak Aduan</span>
                </button>

                @if($hasSearched)
                    <button
                        type="button"
                        wire:click="resetSearch"
                        class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-5 py-3 text-sm font-medium text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 transition"
                    >
                        Cari Tiket Lain
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Error Message State -->
    @if($errorMessage)
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-900/60 dark:bg-red-950/40 text-center space-y-2" data-test="not-found-message">
            <div class="inline-flex size-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400">
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-red-800 dark:text-red-300">
                {{ $errorMessage }}
            </p>
            <p class="text-xs text-red-600 dark:text-red-400">
                Periksa kembali penulisan kode tiket aduan Anda atau hubungi layanan WhatsApp Dinas Sosial.
            </p>
        </div>
    @endif

    <!-- Search Result Section -->
    @if($complaint)
        @php
            $statusLabels = [
                'baru' => 'Baru Masuk',
                'diproses' => 'Sedang Diproses',
                'diteruskan' => 'Diteruskan ke Bidang',
                'didisposisikan' => 'Didisposisikan',
                'ditindaklanjuti' => 'Sedang Ditindaklanjuti',
                'selesai' => 'Selesai Ditangani',
                'ditolak' => 'Aduan Ditolak',
            ];

            $statusBadgeClasses = [
                'baru' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
                'diproses' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                'diteruskan' => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
                'didisposisikan' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                'ditindaklanjuti' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                'ditolak' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-800',
            ];

            $statusNarratives = [
                'baru' => 'Aduan telah berhasil dicatat dan masuk ke sistem penerimaan aduan.',
                'diproses' => 'Aduan telah diverifikasi oleh petugas penerima dan sedang dalam proses.',
                'diteruskan' => 'Aduan telah diteruskan ke Kepala Bidang teknis yang berwenang.',
                'didisposisikan' => 'Aduan telah didisposisikan kepada Petugas Pelaksana teknis di lapangan.',
                'ditindaklanjuti' => 'Petugas pelaksana sedang menindaklanjuti penanganan aduan.',
                'selesai' => 'Penanganan aduan kesejahteraan sosial telah selesai dilaksanakan.',
                'ditolak' => 'Aduan belum dapat diproses sesuai kriteria dan ketentuan yang berlaku.',
            ];
        @endphp

        <div class="space-y-6" data-test="complaint-result">
            <!-- Complaint Summary Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 dark:border-zinc-800 bg-gray-50/60 dark:bg-zinc-800/40 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs uppercase font-semibold text-gray-500 dark:text-zinc-400">Kode Tiket</span>
                        <div class="text-xl font-extrabold font-mono text-gray-900 dark:text-white tracking-wide" data-test="ticket-code">
                            {{ $complaint->ticket_code }}
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center rounded-full border px-3.5 py-1 text-xs font-bold {{ $statusBadgeClasses[$complaint->status] ?? 'bg-gray-100 text-gray-800' }}" data-test="current-status">
                            {{ $statusLabels[$complaint->status] ?? ucfirst($complaint->status) }}
                        </span>
                    </div>
                </div>

                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Tanggal Aduan Masuk</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white" data-test="reported-at">
                            {{ ($complaint->reported_at ?? $complaint->created_at)->translatedFormat('d F Y, H:i') }} WITA
                        </p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Kategori Aduan</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white" data-test="category-name">
                            {{ $complaint->category?->name ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Bidang Tujuan Penanganan</span>
                        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400" data-test="bidang-name">
                            {{ $complaint->bidang }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Nomor WhatsApp Pelapor</span>
                        <p class="text-sm font-mono text-gray-700 dark:text-zinc-300" data-test="masked-phone">
                            {{ $complaint->masked_phone }}
                        </p>
                    </div>

                    <div class="col-span-1 md:col-span-2 space-y-1 pt-2 border-t border-gray-100 dark:border-zinc-800">
                        <span class="text-xs font-medium text-gray-500 dark:text-zinc-400">Judul Aduan</span>
                        <p class="text-base font-semibold text-gray-900 dark:text-white" data-test="complaint-title">
                            {{ $complaint->title }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status History Timeline Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="size-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Riwayat Perubahan Status
                    </h2>
                    <span class="text-xs text-gray-500 dark:text-zinc-400">Terbaru ke terlama</span>
                </div>

                @if($complaint->histories->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-zinc-400 italic">
                        Belum ada riwayat perubahan status.
                    </p>
                @else
                    <ol class="relative border-s border-gray-200 dark:border-zinc-700 ms-3 space-y-6" data-test="history-timeline">
                        @foreach($complaint->histories as $history)
                            @php
                                $histStatus = $history->new_status ?? 'baru';
                            @endphp
                            <li class="ms-6">
                                <span class="absolute -start-3 flex size-6 items-center justify-center rounded-full bg-blue-100 text-blue-600 ring-4 ring-white dark:bg-blue-950 dark:text-blue-300 dark:ring-zinc-900">
                                    <span class="size-2 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                                </span>

                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1">
                                    <span class="inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold {{ $statusBadgeClasses[$histStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$histStatus] ?? ucfirst($histStatus) }}
                                    </span>
                                    <time class="text-xs text-gray-500 dark:text-zinc-400 font-mono">
                                        {{ $history->created_at->translatedFormat('d F Y, H:i') }} WITA
                                    </time>
                                </div>

                                <p class="text-xs text-gray-600 dark:text-zinc-400 mt-1">
                                    {{ $statusNarratives[$histStatus] ?? 'Status aduan telah diperbarui.' }}
                                </p>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    @endif
</div>
