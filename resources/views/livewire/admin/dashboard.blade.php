<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Dashboard Administrator
                </h1>
                <flux:badge color="blue" size="sm">Admin</flux:badge>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Selamat datang kembali, <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</span>. Pantau seluruh aliran pengaduan kesejahteraan sosial di Kabupaten Sumbawa.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                <flux:icon name="calendar" class="size-4" />
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- 4 Statistic Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-statistic-card
            title="Total Aduan"
            :value="$statistics->totalAduan"
            icon="document-text"
            color="blue"
            description="Seluruh aduan warga yang masuk"
        />

        <x-statistic-card
            title="Aduan Baru"
            :value="$statistics->aduanBaru"
            icon="sparkles"
            color="sky"
            description="Aduan baru belum diproses"
            badge="Perlu Respon"
        />

        <x-statistic-card
            title="Aduan Diproses"
            :value="$statistics->aduanDiproses"
            icon="arrow-path"
            color="amber"
            description="Sedang dalam penanganan petugas"
        />

        <x-statistic-card
            title="Aduan Selesai"
            :value="$statistics->aduanSelesai"
            icon="check-circle"
            color="emerald"
            description="Tuntas ditangani Dinas Sosial"
            badge="Tuntas"
        />
    </div>

    <!-- Role Information & System Overview -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Status Breakdown Card -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4 dark:border-zinc-800">
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Ringkasan Status Sistem</h2>
                    <p class="text-xs text-zinc-500">Rincian status penanganan aduan masyarakat terkini</p>
                </div>
                <flux:badge color="zinc" size="sm">Real-time</flux:badge>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-700/50">
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Diteruskan</div>
                    <div class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ $statistics->extra['aduanDiteruskan'] ?? 0 }}
                    </div>
                    <div class="mt-1 text-[11px] text-zinc-400">Siap untuk disposisi</div>
                </div>

                <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-700/50">
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Didisposisikan</div>
                    <div class="mt-2 text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                        {{ $statistics->extra['aduanDidisposisikan'] ?? 0 }}
                    </div>
                    <div class="mt-1 text-[11px] text-zinc-400">Ditugaskan ke pelaksana</div>
                </div>

                <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-700/50">
                    <div class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Ditolak</div>
                    <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">
                        {{ $statistics->extra['aduanDitolak'] ?? 0 }}
                    </div>
                    <div class="mt-1 text-[11px] text-zinc-400">Tidak memenuhi kriteria</div>
                </div>
            </div>

            <div class="mt-6 rounded-xl bg-blue-50/70 p-4 border border-blue-100 text-xs text-blue-900 dark:bg-blue-950/30 dark:border-blue-900/50 dark:text-blue-300">
                <div class="flex items-start gap-2.5">
                    <flux:icon name="information-circle" class="size-4 shrink-0 text-blue-600 mt-0.5" />
                    <span>Sebagai Administrator, Anda memiliki hak penuh untuk mengelola pengguna, memantau kategori, dan mengawasi seluruh riwayat disposisi serta pemrosesan aduan.</span>
                </div>
            </div>
        </div>

        <!-- System Fast Info -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Informasi Wilayah Layanan</h2>
            <p class="text-xs text-zinc-500">Cakupan penanganan kesejahteraan sosial</p>

            <div class="mt-5 space-y-4 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Instansi</span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">Dinas Sosial Kab. Sumbawa</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Wilayah Kerja</span>
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">Kabupaten Sumbawa, NTB</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Kanal Aduan</span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">WhatsApp & Web</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-zinc-500">Format Tiket</span>
                    <span class="font-mono font-semibold text-zinc-800 dark:text-zinc-200">JS-YYYY-XXXX</span>
                </div>
            </div>
        </div>
    </div>
</div>

