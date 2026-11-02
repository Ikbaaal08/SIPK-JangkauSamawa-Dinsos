<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('kepala-dinas.dashboard') }}" class="hover:text-emerald-600 transition-colors" wire:navigate>
                Dashboard
            </a>
            <flux:icon name="chevron-right" class="size-3.5 text-zinc-400" />
            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                Disposisi Aduan
            </span>
        </nav>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Disposisi Pimpinan
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Berikan instruksi dan disposisi resmi kepada Kepala Bagian (Kabag) untuk penanganan aduan kesejahteraan sosial.
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800/50 dark:bg-emerald-950/40 dark:text-emerald-300">
            <flux:icon name="check-circle" class="size-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800/50 dark:bg-red-950/40 dark:text-red-300">
            <flux:icon name="exclamation-circle" class="size-5 text-red-600 dark:text-red-400 shrink-0" />
            <div class="font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Card Table -->
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <div class="relative w-full sm:w-80">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="Cari tiket, judul, atau pelapor..."
                    clearable
                />
            </div>

            <div class="flex items-center gap-2">
                <flux:select wire:model.live="statusFilter" class="w-full sm:w-48">
                    <flux:select.option value="">Semua Status Aduan</flux:select.option>
                    <flux:select.option value="diteruskan">Diteruskan ke Pimpinan</flux:select.option>
                    <flux:select.option value="didisposisikan">Sudah Didisposisikan</flux:select.option>
                    <flux:select.option value="ditindaklanjuti">Sedang Ditindaklanjuti</flux:select.option>
                    <flux:select.option value="selesai">Selesai</flux:select.option>
                    <flux:select.option value="baru">Baru</flux:select.option>
                </flux:select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm text-zinc-600 dark:text-zinc-300">
                <thead class="bg-zinc-50 text-xs uppercase font-semibold text-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-5 py-3.5 text-center w-14">No</th>
                        <th class="px-5 py-3.5 text-start">Kode Tiket</th>
                        <th class="px-5 py-3.5 text-start">Pelapor & Wilayah</th>
                        <th class="px-5 py-3.5 text-start">Kategori & Judul</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-start">Status Disposisi</th>
                        <th class="px-5 py-3.5 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($complaints as $complaint)
                        <tr wire:key="kadis-complaint-{{ $complaint->id }}" class="hover:bg-zinc-50/75 dark:hover:bg-zinc-800/40 transition-colors">
                            <td class="px-5 py-4 text-center font-medium text-zinc-500">
                                {{ ($complaints->currentPage() - 1) * $complaints->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-300">
                                    {{ $complaint->ticket_code }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $complaint->reporter_name }}</div>
                                <div class="text-xs text-zinc-500">{{ $complaint->village ?: '—' }}, {{ $complaint->district ?: '—' }}</div>
                            </td>
                            <td class="px-5 py-4 max-w-xs">
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block">{{ $complaint->category?->name }}</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100 truncate block" title="{{ $complaint->title }}">{{ $complaint->title }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $badgeColor = match ($complaint->status) {
                                        'baru' => 'blue',
                                        'diproses', 'ditindaklanjuti' => 'amber',
                                        'diteruskan' => 'sky',
                                        'didisposisikan' => 'zinc',
                                        'selesai' => 'emerald',
                                        'ditolak' => 'red',
                                        default => 'zinc',
                                    };
                                @endphp
                                <flux:badge :color="$badgeColor" size="sm" class="capitalize">
                                    {{ $complaint->status }}
                                </flux:badge>
                            </td>
                            <td class="px-5 py-4">
                                @if ($complaint->dispositions->isNotEmpty())
                                    @php $latestDisp = $complaint->dispositions->last(); @endphp
                                    <div class="text-xs">
                                        <span class="font-medium text-zinc-800 dark:text-zinc-200">Ke: {{ $latestDisp->assignedTo?->name }}</span>
                                        <span class="text-zinc-500 block truncate max-w-xs" title="{{ $latestDisp->instruction }}">
                                            "{{ $latestDisp->instruction }}"
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400 italic">Belum didisposisikan</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-end">
                                <div class="inline-flex items-center gap-1.5">
                                    <flux:button
                                        size="sm"
                                        variant="primary"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white"
                                        icon="arrow-right-circle"
                                        wire:click="openDispositionModal({{ $complaint->id }})"
                                        data-test="btn-disposisi-{{ $complaint->id }}"
                                    >
                                        Beri Disposisi
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="subtle"
                                        icon="eye"
                                        wire:click="openDetailModal({{ $complaint->id }})"
                                    >
                                        Detail
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="inbox" class="size-10 stroke-1 text-zinc-400" />
                                    <span class="font-semibold text-base">Belum ada data aduan</span>
                                    <p class="text-xs max-w-sm">Aduan yang memerlukan disposisi pimpinan akan tercantum pada tabel ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($complaints->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Disposisi (Kadis ke Kabag) -->
    <flux:modal wire:model="showDispositionModal" class="md:w-[560px]">
        @if ($selectedComplaint)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Formulir Disposisi Pimpinan</flux:heading>
                    <flux:subheading>
                        Kirimkan arahan disposisi resmi kepada Kepala Bagian (Kabag) terkait aduan ini.
                    </flux:subheading>
                </div>

                <!-- Ringkasan Aduan -->
                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-800/40 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-bold text-blue-600">{{ $selectedComplaint->ticket_code }}</span>
                        <flux:badge size="sm" color="sky">{{ $selectedComplaint->category?->name }}</flux:badge>
                    </div>
                    <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $selectedComplaint->title }}</div>
                    <div class="text-zinc-600 dark:text-zinc-400">Pelapor: {{ $selectedComplaint->reporter_name }} ({{ $selectedComplaint->village ?: '—' }}, {{ $selectedComplaint->district ?: '—' }})</div>
                    <div class="text-zinc-500 line-clamp-2 pt-1 border-t border-zinc-200 dark:border-zinc-700/60">{{ $selectedComplaint->description }}</div>
                </div>

                <form wire:submit="submitDisposition" class="space-y-4">
                    <flux:field>
                        <flux:label>Pejabat Penerima Disposisi (Kabag)</flux:label>
                        <flux:select wire:model="form.assigned_to" placeholder="Pilih Kepala Bagian...">
                            <flux:select.option value="">Pilih Kabag Penerima</flux:select.option>
                            @foreach ($penerimaList as $penerima)
                                <flux:select.option value="{{ $penerima->id }}">
                                    {{ $penerima->name }} ({{ $penerima->roleLabel() }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="form.assigned_to" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Tenggat Waktu Tindak Lanjut (Batas Waktu)</flux:label>
                        <flux:input type="date" wire:model="form.due_date" min="{{ now()->toDateString() }}" />
                        <flux:error name="form.due_date" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Instruksi / Arahan Pimpinan</flux:label>
                        <flux:textarea
                            wire:model="form.instruction"
                            rows="4"
                            placeholder="Tuliskan arahan resmi pimpinan, misalnya: Lakukan verifikasi lapangan bersama tim pelaksana dan siapkan bantuan logistik darurat dalam 3 hari kerja..."
                            required
                        />
                        <flux:error name="form.instruction" />
                    </flux:field>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button variant="ghost" type="button" wire:click="closeDispositionModal">
                            Batal
                        </flux:button>
                        <flux:button variant="primary" type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white" data-test="submit-disposisi-kadis">
                            Terbitkan Disposisi
                        </flux:button>
                    </div>
                </form>
            </div>
        @endif
    </flux:modal>

    <!-- Modal Detail Aduan & Disposisi -->
    <flux:modal wire:model="showDetailModal" class="md:w-[650px]">
        @if ($selectedComplaint)
            <div class="space-y-5">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-bold text-sm text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $selectedComplaint->ticket_code }}</span>
                        <flux:badge size="sm" color="emerald">{{ $selectedComplaint->status }}</flux:badge>
                    </div>
                    <flux:heading size="lg" class="mt-2">{{ $selectedComplaint->title }}</flux:heading>
                    <flux:subheading>{{ $selectedComplaint->category?->name }} • Diajukan: {{ $selectedComplaint->reported_at?->translatedFormat('d F Y H:i') }}</flux:subheading>
                </div>

                <!-- Isi Laporan Warga -->
                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-xs dark:border-zinc-800 dark:bg-zinc-800/40 space-y-2">
                    <div class="grid grid-cols-2 gap-2 text-zinc-600 dark:text-zinc-400">
                        <div><span class="font-semibold text-zinc-800 dark:text-zinc-200">Pelapor:</span> {{ $selectedComplaint->reporter_name }}</div>
                        <div><span class="font-semibold text-zinc-800 dark:text-zinc-200">Wilayah:</span> {{ $selectedComplaint->village ?: '—' }}, {{ $selectedComplaint->district ?: '—' }}</div>
                    </div>
                    <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                        <span class="font-semibold block mb-1">Deskripsi Aduan:</span>
                        {{ $selectedComplaint->description }}
                    </div>
                </div>

                <!-- Riwayat Disposisi Berjenjang -->
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <flux:icon name="arrows-right-left" class="size-4 text-emerald-600" />
                        Alur Disposisi Digital Berjenjang
                    </h3>

                    @forelse ($selectedComplaint->dispositions as $disp)
                        <div class="rounded-lg border border-emerald-100 bg-emerald-50/40 p-3.5 text-xs dark:border-emerald-900/40 dark:bg-emerald-950/20 space-y-1.5">
                            <div class="flex items-center justify-between font-medium">
                                <span class="text-emerald-900 dark:text-emerald-300">
                                    Dari: <strong>{{ $disp->assignedBy?->name }}</strong> ({{ $disp->assignedBy?->roleLabel() }})
                                </span>
                                <span class="text-zinc-500 font-mono text-[11px]">{{ $disp->created_at->translatedFormat('d M Y H:i') }}</span>
                            </div>
                            <div class="text-zinc-700 dark:text-zinc-300">
                                Diteruskan Kepada: <strong>{{ $disp->assignedTo?->name }}</strong> ({{ $disp->assignedTo?->roleLabel() }})
                            </div>
                            <div class="p-2.5 rounded bg-white dark:bg-zinc-900 border border-emerald-200/60 dark:border-emerald-800/40 text-zinc-800 dark:text-zinc-200 font-sans italic">
                                "{{ $disp->instruction }}"
                            </div>
                            @if ($disp->due_date)
                                <div class="text-[11px] text-amber-700 dark:text-amber-400 font-medium">
                                    Tenggat Waktu: {{ $disp->due_date->translatedFormat('d F Y') }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-zinc-400 italic p-3 bg-zinc-50 dark:bg-zinc-800/30 rounded-lg text-center">
                            Belum ada lembar disposisi yang diterbitkan untuk aduan ini.
                        </div>
                    @endforelse
                </div>

                <div class="flex justify-end pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button variant="ghost" wire:click="closeDetailModal">Tutup</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>

