<div>
    <flux:modal wire:model="showModal" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </flux:heading>
                <flux:subheading>
                    {{ $isEditing ? 'Perbarui informasi data kategori aduan di bawah ini.' : 'Isi formulir berikut untuk menambahkan kategori aduan baru ke dalam sistem.' }}
                </flux:subheading>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:input
                    wire:model="form.name"
                    label="Nama Kategori"
                    placeholder="Contoh: Bantuan Sosial Pangan"
                    required
                />

                <div>
                    <flux:input
                        wire:model="form.code"
                        label="Kode Kategori"
                        placeholder="Contoh: BSP"
                        class="uppercase"
                        required
                    />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        Kode unik kategori, otomatis dikonversi menjadi huruf kapital.
                    </p>
                </div>

                <flux:textarea
                    wire:model="form.description"
                    label="Deskripsi (Opsional)"
                    placeholder="Keterangan ringkas mengenai cakupan kategori masalah kesejahteraan sosial ini..."
                    rows="3"
                />

                <div class="pt-1">
                    <flux:checkbox
                        wire:model="form.is_active"
                        label="Kategori Aktif"
                        description="Kategori aktif dapat dipilih oleh pelapor dan petugas pada sistem."
                    />
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button variant="ghost" type="button" wire:click="close">
                        Batal
                    </flux:button>
                    <flux:button variant="primary" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white" data-test="submit-category-button">
                        Simpan
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>

