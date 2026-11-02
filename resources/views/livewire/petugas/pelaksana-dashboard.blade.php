<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Dashboard Petugas Pelaksana
                </h1>
                <flux:badge color="amber" size="sm">Petugas Pelaksana</flux:badge>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Semangat bertugas, <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</span>. Laksanakan disposisi pimpinan dan tinjau langsung kondisi warga di lapangan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                <flux:icon name="calendar" class="size-4" />
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- 4 Statistic Cards (Tugas Khusus Petugas Pelaksana) -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-statistic-card
            title="Total Tugas Aduan"
            :value="$statistics->totalAduan"
            icon="document-text"
            color="blue"
            description="Semua disposisi ditugaskan ke Anda"
        />

        <x-statistic-card
            title="Tugas Baru"
            :value="$statistics->aduanBaru"
            icon="sparkles"
            color="sky"
            description="Disposisi baru belum ditindaklanjuti"
            badge="Perlu Aksi"
        />

        <x-statistic-card
            title="Ditindaklanjuti"
            :value="$statistics->aduanDiproses"
            icon="arrow-path"
            color="amber"
            description="Sedang dalam proses tindak lanjut lapangan"
        />

        <x-statistic-card
            title="Tugas Selesai"
            :value="$statistics->aduanSelesai"
            icon="check-circle"
            color="emerald"
            description="Disposisi tuntas dikerjakan dan dilaporkan"
            badge="Tuntas"
        />
    </div>

    <!-- Instructions & Field Guidelines -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 lg:col-span-2">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Prosedur Lapangan & Tindak Lanjut</h2>
            <p class="text-xs text-zinc-500">Tata cara pelaksanaan verifikasi faktual kesejahteraan sosial</p>

            <div class="mt-5 space-y-3">
                <div class="flex items-start gap-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-600 text-xs font-bold text-white">1</span>
                    <div>
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Kaji Instruksi Disposisi Kepala Dinas</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Pahami secara seksama arahan pimpinan dan perhatikan tenggat waktu (due date) yang ditetapkan.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-600 text-xs font-bold text-white">2</span>
                    <div>
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Verifikasi Faktual & Dokumentasi</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Kunjungi lokasi penerima manfaat atau hubungi aparat desa/kelurahan setempat untuk mengecek kondisi riil warga.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-600 text-xs font-bold text-white">3</span>
                    <div>
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Perbarui Status Jadi 'Selesai'</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Setelah bantuan/pendampingan disalurkan, tandai status penanganan menjadi 'selesai' dengan catatan tindak lanjut.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Beban Tugas Saya</h2>
            <p class="text-xs text-zinc-500">Ringkasan penugasan aktif Anda</p>

            <div class="mt-5 space-y-4 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Disposisi Aktif</span>
                    <span class="font-bold text-amber-600 dark:text-amber-400">{{ $statistics->extra['tugasAktif'] ?? 0 }} Tugas</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-zinc-500">Tugas Baru Menunggu</span>
                    <span class="font-bold text-sky-600 dark:text-sky-400">{{ $statistics->aduanBaru }} Tugas</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-zinc-500">Status Petugas</span>
                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span> Bertugas Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

