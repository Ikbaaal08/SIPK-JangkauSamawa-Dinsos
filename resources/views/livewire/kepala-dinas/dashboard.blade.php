<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Dashboard Kepala Dinas Sosial
                </h1>
                <flux:badge color="emerald" size="sm">Kepala Dinas</flux:badge>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Selamat datang, <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</span>. Tinjau aduan masyarakat dan berikan arahan disposisi kepada petugas pelaksana.
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
            description="Seluruh aduan kesejahteraan sosial"
        />

        <x-statistic-card
            title="Perlu Disposisi"
            :value="$statistics->aduanBaru"
            icon="sparkles"
            color="amber"
            description="Aduan masuk yang menunggu arahan"
            badge="Tindakan Pimpinan"
        />

        <x-statistic-card
            title="Disposisi Berjalan"
            :value="$statistics->aduanDiproses"
            icon="arrow-path"
            color="sky"
            description="Sedang ditindaklanjuti tim pelaksana"
        />

        <x-statistic-card
            title="Aduan Selesai"
            :value="$statistics->aduanSelesai"
            icon="check-circle"
            color="emerald"
            description="Penyelesaian tuntas kesejahteraan warga"
            badge="Tuntas"
        />
    </div>

    <!-- Pimpinan Overview -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Kewenangan & Pengawasan Disposisi</h2>
            <p class="text-xs text-zinc-500">Pedoman pemberian disposisi penanganan pengaduan</p>

            <div class="mt-5 space-y-4">
                <div class="flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                    <flux:icon name="shield-check" class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" />
                    <div class="text-xs text-emerald-900 dark:text-emerald-300">
                        <span class="font-semibold block mb-0.5">Penetapan Petugas Pelaksana yang Tepat</span>
                        Pastikan disposisi ditugaskan kepada petugas pelaksana sesuai bidang dan kedekatan wilayah agar penanganan berlangsung cepat dan tepat sasaran.
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/50 p-4 dark:border-blue-900/40 dark:bg-blue-950/20">
                    <flux:icon name="clock" class="size-5 shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" />
                    <div class="text-xs text-blue-900 dark:text-blue-300">
                        <span class="font-semibold block mb-0.5">Penetapan Tenggat Waktu (Due Date)</span>
                        Setiap disposisi dianjurkan menyertakan batas waktu tindak lanjut (misal 3-7 hari kerja) demi kepastian pelayanan bagi warga Kabupaten Sumbawa.
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Kinerja Pelayanan</h2>
            <p class="text-xs text-zinc-500">Statistik penanganan dinas</p>

            <div class="mt-5 space-y-4 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Aduan Siap Disposisi</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400">{{ $statistics->extra['siapDisposisi'] ?? 0 }} Aduan</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Disposisi Aktif Lapangan</span>
                    <span class="font-bold text-sky-600 dark:text-sky-400">{{ $statistics->extra['disposisiAktif'] ?? 0 }} Tugas</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-zinc-500">Tingkat Ketuntasan</span>
                    @php
                        $rate = $statistics->totalAduan > 0
                            ? round(($statistics->aduanSelesai / $statistics->totalAduan) * 100, 1)
                            : 0;
                    @endphp
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $rate }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

