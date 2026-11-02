<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors" wire:navigate>
                Dashboard
            </a>
            <flux:icon name="chevron-right" class="size-3.5 text-zinc-400" />
            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                Kelola Fitur
            </span>
        </nav>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                        Kelola Fitur Sistem
                    </h1>
                    <flux:badge color="blue" size="sm">Super Admin</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Aktifkan atau nonaktifkan fitur aplikasi secara dinamis melalui kontrol switch tanpa mengubah kode atau berkas konfigurasi .env.
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

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse ($features as $feature)
            <div
                wire:key="feature-card-{{ $feature->id }}"
                class="flex flex-col justify-between rounded-2xl border bg-white p-5 shadow-xs transition-all dark:bg-zinc-900 {{ $feature->is_enabled ? 'border-emerald-200 dark:border-emerald-800/60' : 'border-zinc-200 dark:border-zinc-800' }}"
            >
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-base text-zinc-900 dark:text-zinc-100">
                                    {{ $feature->label }}
                                </h3>
                                <span class="font-mono text-[11px] text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded">
                                    {{ $feature->key }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                {{ $feature->description }}
                            </p>
                        </div>

                        <!-- Status Badge -->
                        @if ($feature->is_enabled)
                            <flux:badge color="emerald" size="sm" inset="top bottom">
                                Aktif
                            </flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm" inset="top bottom">
                                Nonaktif
                            </flux:badge>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <div class="text-[11px] text-zinc-400">
                        @if ($feature->updatedBy)
                            Diperbarui oleh: <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $feature->updatedBy->name }}</span>
                            <span class="block text-[10px]">{{ $feature->updated_at->diffForHumans() }}</span>
                        @else
                            <span>Nilai bawaan sistem</span>
                        @endif
                    </div>

                    <!-- Toggle Control -->
                    <flux:button
                        variant="{{ $feature->is_enabled ? 'primary' : 'subtle' }}"
                        size="sm"
                        class="{{ $feature->is_enabled ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'text-zinc-700 dark:text-zinc-300' }}"
                        wire:click="toggle('{{ $feature->key }}')"
                        data-test="toggle-{{ $feature->key }}"
                    >
                        <flux:icon name="{{ $feature->is_enabled ? 'check' : 'x-mark' }}" class="size-4 mr-1" />
                        {{ $feature->is_enabled ? 'ON (Aktif)' : 'OFF (Nonaktif)' }}
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="col-span-2 p-12 text-center text-zinc-400">
                <flux:icon name="cpu-chip" class="size-10 mx-auto stroke-1" />
                <p class="mt-2 text-sm font-semibold">Belum ada konfigurasi fitur.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Konfirmasi Dampak Besar (misal: nonaktifkan verifikasi email) -->
    <flux:modal wire:model="showConfirmModal" class="md:w-[480px]">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <flux:icon name="exclamation-triangle" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg">{{ $confirmingTitle }}</flux:heading>
                    <flux:subheading>Konfirmasi perubahan konfigurasi keamanan</flux:subheading>
                </div>
            </div>

            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                {{ $confirmingMessage }}
            </p>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button variant="ghost" wire:click="cancelConfirm">
                    Batal
                </flux:button>
                <flux:button variant="danger" wire:click="confirmToggle" data-test="btn-confirm-toggle">
                    Lanjutkan & Nonaktifkan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

