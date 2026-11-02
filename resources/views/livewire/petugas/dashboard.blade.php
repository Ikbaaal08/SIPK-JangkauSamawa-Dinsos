<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Dashboard Petugas Penerima
                </h1>
                <flux:badge color="sky" size="sm">Petugas Penerima</flux:badge>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Selamat bertugas, <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</span>. Lakukan verifikasi dan pencatatan aduan awal masyarakat dengan teliti.
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
            description="Semua aduan masuk ke sistem"
        />

        <x-statistic-card
            title="Aduan Baru"
            :value="$statistics->aduanBaru"
            icon="sparkles"
            color="sky"
            description="Menunggu verifikasi awal petugas"
            badge="Perlu Verifikasi"
        />

        <x-statistic-card
            title="Aduan Diproses"
            :value="$statistics->aduanDiproses"
            icon="arrow-path"
            color="amber"
            description="Aduan valid sedang ditindaklanjuti"
        />

        <x-statistic-card
            title="Aduan Selesai"
            :value="$statistics->aduanSelesai"
            icon="check-circle"
            color="emerald"
            description="Aduan yang telah tuntas ditangani"
            badge="Tuntas"
        />
    </div>

    <!-- Instructions & Flow -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Alur Standar Operasional Verifikasi</h2>
            <p class="text-xs text-zinc-500">Panduan standar kerja bagi Petugas Penerima Aduan</p>

            <div class="mt-5 space-y-3">
                <div class="flex items-start gap-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">1</span>
                    <div>
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Periksa Kelengkapan Data Pelapor</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Pastikan nama pelapor, nomor WhatsApp aktif, desa/kelurahan, dan kecamatan terisi jelas.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">2</span>
                    <div>
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Kesesuaian Kategori Sosial</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Cocokkan isi aduan dengan kategori yang relevan: Bantuan Sosial, Lansia, Disabilitas, atau Penanganan Bencana.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">3</span>
                    <div>
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Ubah Status & Teruskan Aduan</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Jika valid, ubah status menjadi 'diproses' atau 'diteruskan' ke Kepala Dinas untuk disposisi. Jika fiktif, beri alasan penolakan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Status Tindak Lanjut</h2>
            <p class="text-xs text-zinc-500">Kondisi antrean kerja penerimaan</p>

            <div class="mt-5 space-y-4 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Aduan Perlu Verifikasi</span>
                    <span class="font-bold text-sky-600 dark:text-sky-400">{{ $statistics->extra['perluVerifikasi'] ?? 0 }} Aduan</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Aduan Ditolak</span>
                    <span class="font-bold text-red-600 dark:text-red-400">{{ $statistics->extra['aduanDitolak'] ?? 0 }} Aduan</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-zinc-500">Status Tugas Penerima</span>
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Siaga
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

