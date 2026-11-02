<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('pelaksana.dashboard') }}" class="hover:text-amber-600 transition-colors" wire:navigate>
                Dashboard
            </a>
            <flux:icon name="chevron-right" class="size-3.5 text-zinc-400" />
            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                Tugas Disposisi
            </span>
        </nav>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Tugas Disposisi Lapangan
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Daftar penugasan dan arahan disposisi dari pimpinan yang wajib Anda tindaklanjuti di lapangan.
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
                    placeholder="Cari tiket, judul, atau instruksi..."
                    clearable
                />
            </div>

            <div class="flex items-center gap-2">
                <flux:select wire:model.live="statusFilter" class="w-full sm:w-48">
                    <flux:select.option value="">Semua Status Disposisi</flux:select.option>
                    <flux:select.option value="aktif">Tugas Aktif</flux:select.option>
                    <flux:select.option value="selesai">Tugas Selesai</flux:select.option>
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
                        <th class="px-5 py-3.5 text-start">Instruksi Disposisi</th>
                        <th class="px-5 py-3.5 text-center">Tenggat</th>
                        <th class="px-5 py-3.5 text-center">Status Aduan</th>
                        <th class="px-5 py-3.5 text-end">Aksi Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($dispositions as $disp)
                        @php $complaint = $disp->complaint; @endphp
                        <tr wire:key="pelaksana-disp-{{ $disp->id }}" class="hover:bg-zinc-50/75 dark:hover:bg-zinc-800/40 transition-colors">
                            <td class="px-5 py-4 text-center font-medium text-zinc-500">
                                {{ ($dispositions->currentPage() - 1) * $dispositions->perPage() + $loop->iteration }}
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
                            <td class="px-5 py-4 max-w-xs">
                                <div class="text-xs text-zinc-800 dark:text-zinc-200">
                                    <span class="font-semibold text-zinc-500">Dari: {{ $disp->assignedBy?->name }}</span>
                                    <p class="truncate text-zinc-600 dark:text-zinc-300 mt-0.5" title="{{ $disp->instruction }}">"{{ $disp->instruction }}"</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($disp->due_date)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $disp->due_date->isPast() && $disp->status !== 'selesai' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                        {{ $disp->due_date->translatedFormat('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $badgeColor = match ($complaint->status) {
                                        'didisposisikan' => 'zinc',
                                        'ditindaklanjuti' => 'amber',
                                        'selesai' => 'emerald',
                                        default => 'blue',
                                    };
                                @endphp
                                <flux:badge :color="$badgeColor" size="sm" class="capitalize">
                                    {{ $complaint->status }}
                                </flux:badge>
                            </td>
                            <td class="px-5 py-4 text-end">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($complaint->status === 'didisposisikan')
                                        <flux:button
                                            size="sm"
                                            variant="primary"
                                            class="bg-amber-600 hover:bg-amber-700 text-white"
                                            icon="play"
                                            wire:click="openActionModal({{ $disp->id }}, 'tindaklanjuti')"
                                            data-test="btn-tindaklanjuti-{{ $disp->id }}"
                                        >
                                            Tindak Lanjuti
                                        </flux:button>
                                    @endif

                                    @if ($complaint->status !== 'selesai')
                                        <flux:button
                                            size="sm"
                                            variant="primary"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white"
                                            icon="check-circle"
                                            wire:click="openActionModal({{ $disp->id }}, 'selesai')"
                                            data-test="btn-selesaikan-{{ $disp->id }}"
                                        >
                                            Selesaikan
                                        </flux:button>
                                    @else
                                        <flux:badge color="emerald" size="sm">Tuntas</flux:badge>
                                    @endif

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
                            <td colspan="8" class="px-5 py-12 text-center text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="clipboard-document-list" class="size-10 stroke-1 text-zinc-400" />
                                    <span class="font-semibold text-base">Belum ada tugas disposisi</span>
                                    <p class="text-xs max-w-sm">Anda hanya akan melihat aduan yang didisposisikan secara khusus kepada Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dispositions->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $dispositions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Aksi (Tindak Lanjut / Penyelesaian) -->
    <flux:modal wire:model="showActionModal" class="md:w-[500px]">
        @if ($selectedDisposition)
            <div class="space-y-5">
                <div>
                    <flux:heading size="lg">
                        {{ $actionType === 'tindaklanjuti' ? 'Konfirmasi Tindak Lanjut Lapangan' : 'Konfirmasi Penyelesaian Aduan' }}
                    </flux:heading>
                    <flux:subheading>
                        Aduan #{{ $selectedDisposition->complaint->ticket_code }}: {{ $selectedDisposition->complaint->title }}
                    </flux:subheading>
                </div>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Catatan Lapangan (Opsional)</flux:label>
                        <flux:textarea
                            wire:model="actionNote"
                            rows="3"
                            placeholder="{{ $actionType === 'tindaklanjuti' ? 'Contoh: Tim pelaksana telah tiba di lokasi dan sedang melakukan verifikasi berkas...' : 'Contoh: Bantuan telah diserahkan langsung kepada pelapor disaksikan oleh kepala desa setempat...' }}"
                        />
                    </flux:field>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <flux:button variant="ghost" type="button" wire:click="closeActionModal">
                            Batal
                        </flux:button>
                        <flux:button
                            variant="primary"
                            type="button"
                            class="{{ $actionType === 'tindaklanjuti' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white"
                            wire:click="submitAction"
                            data-test="confirm-submit-action"
                        >
                            {{ $actionType === 'tindaklanjuti' ? 'Mulai Tindak Lanjut' : 'Tandai Selesai' }}
                        </flux:button>
                    </div>
                </div>
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
                    <flux:subheading>{{ $selectedComplaint->category?->name }}</flux:subheading>
                </div>

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
                        <flux:icon name="arrows-right-left" class="size-4 text-amber-600" />
                        Alur Disposisi Digital Berjenjang
                    </h3>

                    @forelse ($selectedComplaint->dispositions as $disp)
                        <div class="rounded-lg border border-zinc-200 bg-zinc-50/60 p-3.5 text-xs dark:border-zinc-800 dark:bg-zinc-800/30 space-y-1.5">
                            <div class="flex items-center justify-between font-medium">
                                <span class="text-zinc-900 dark:text-zinc-100">
                                    Dari: <strong>{{ $disp->assignedBy?->name }}</strong> ({{ $disp->assignedBy?->roleLabel() }})
                                </span>
                                <span class="text-zinc-500 font-mono text-[11px]">{{ $disp->created_at->translatedFormat('d M Y H:i') }}</span>
                            </div>
                            <div class="text-zinc-700 dark:text-zinc-300">
                                Diteruskan Kepada: <strong>{{ $disp->assignedTo?->name }}</strong> ({{ $disp->assignedTo?->roleLabel() }})
                            </div>
                            <div class="p-2.5 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 font-sans italic">
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
                            Belum ada riwayat disposisi.
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

