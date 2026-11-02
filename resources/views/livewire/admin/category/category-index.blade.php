<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors" wire:navigate>
                Dashboard
            </a>
            <flux:icon name="chevron-right" class="size-3.5 text-zinc-400" />
            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                Kategori
            </span>
        </nav>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Manajemen Kategori
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Kelola klasifikasi dan jenis pengaduan masalah kesejahteraan sosial masyarakat.
                </p>
            </div>

            <flux:button
                variant="primary"
                icon="plus"
                wire:click="openCreateModal"
                class="bg-blue-600 hover:bg-blue-700 text-white self-start sm:self-auto"
                data-test="create-category-button"
            >
                Tambah Kategori
            </flux:button>
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
        <!-- Filter & Search Toolbar -->
        <div class="p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <div class="relative w-full sm:w-80">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="Cari nama atau kode kategori..."
                    clearable
                />
            </div>

            <div class="flex items-center gap-2">
                <flux:select wire:model.live="statusFilter" class="w-full sm:w-44">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="active">Aktif</flux:select.option>
                    <flux:select.option value="inactive">Tidak Aktif</flux:select.option>
                </flux:select>
            </div>
        </div>

        <!-- Table Responsive -->
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm text-zinc-600 dark:text-zinc-300">
                <thead class="bg-zinc-50 text-xs uppercase font-semibold text-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-5 py-3.5 text-center w-16">No</th>
                        <th class="px-5 py-3.5 text-start">Nama Kategori</th>
                        <th class="px-5 py-3.5 text-start">Kode</th>
                        <th class="px-5 py-3.5 text-start">Deskripsi</th>
                        <th class="px-5 py-3.5 text-center">Status Aktif</th>
                        <th class="px-5 py-3.5 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($categories as $index => $category)
                        <tr wire:key="category-row-{{ $category->id }}" class="hover:bg-zinc-50/75 dark:hover:bg-zinc-800/40 transition-colors">
                            <td class="px-5 py-4 text-center font-medium text-zinc-500 dark:text-zinc-400">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $category->name }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                                    {{ $category->code }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-zinc-500 dark:text-zinc-400 max-w-xs truncate" title="{{ $category->description }}">
                                {{ $category->description ?: '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($category->is_active)
                                    <flux:badge color="emerald" size="sm" inset="top bottom">
                                        Aktif
                                    </flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom">
                                        Tidak Aktif
                                    </flux:badge>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-end">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <flux:button
                                        icon="pencil-square"
                                        size="sm"
                                        variant="subtle"
                                        wire:click="openEditModal({{ $category->id }})"
                                        data-test="edit-category-{{ $category->id }}"
                                    >
                                        Edit
                                    </flux:button>
                                    <flux:button
                                        icon="trash"
                                        size="sm"
                                        variant="subtle"
                                        class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30"
                                        wire:click="confirmDelete({{ $category->id }})"
                                        data-test="delete-category-{{ $category->id }}"
                                    >
                                        Hapus
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2 text-zinc-400 dark:text-zinc-500">
                                    <flux:icon name="folder-open" class="size-10 stroke-1" />
                                    <span class="text-base font-semibold text-zinc-700 dark:text-zinc-300">Belum ada data</span>
                                    <p class="text-xs text-zinc-500 max-w-sm">
                                        Kategori pengaduan belum tersedia atau tidak ditemukan berdasarkan kriteria pencarian Anda.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($categories->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <flux:modal wire:model="showDeleteModal" class="md:w-96">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                    <flux:icon name="exclamation-triangle" class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg">Hapus Kategori</flux:heading>
                    <flux:subheading>Konfirmasi tindakan penghapusan</flux:subheading>
                </div>
            </div>

            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                Apakah Anda yakin ingin menghapus kategori <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $deletingCategoryName }}</span>? Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="flex items-center justify-end gap-2 pt-2">
                <flux:button variant="ghost" wire:click="cancelDelete">
                    Batal
                </flux:button>
                <flux:button variant="danger" wire:click="deleteCategory" data-test="confirm-delete-button">
                    Hapus
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Form Tambah/Edit Kategori -->
    <livewire:admin.category.category-form-modal />
</div>

