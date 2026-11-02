<div class="space-y-8">
    <!-- Breadcrumb & Welcome Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
            <span>Aplikasi</span>
            <flux:icon name="chevron-right" class="size-3" />
            <span class="font-semibold text-blue-600 dark:text-blue-400">Dashboard</span>
        </nav>

        <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                        {{ $isSuperAdmin ? 'Dashboard Super Admin' : 'Dashboard Administrator' }}
                    </h1>
                    <flux:badge color="{{ $isSuperAdmin ? 'purple' : 'blue' }}" size="sm">
                        {{ $isSuperAdmin ? 'Super Admin' : 'Admin' }}
                    </flux:badge>
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
    </div>

    @php
        $statusBadgeClasses = [
            'baru' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
            'diproses' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
            'diteruskan' => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
            'didisposisikan' => 'bg-zinc-100 text-zinc-800 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700',
            'ditindaklanjuti' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
            'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            'ditolak' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-800',
        ];

        $statusLabels = [
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'diteruskan' => 'Diteruskan',
            'didisposisikan' => 'Didisposisikan',
            'ditindaklanjuti' => 'Ditindaklanjuti',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    @endphp

    <!-- Statistic Cards Grid -->
    @if($isSuperAdmin)
        <!-- Super Admin: 8 Statistic Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-statistic-card
                title="Total Aduan"
                :value="$statusCounts['total']"
                icon="document-text"
                color="blue"
                description="Semua aduan dalam sistem"
            />
            <x-statistic-card
                title="Aduan Baru"
                :value="$statusCounts['baru']"
                icon="sparkles"
                color="blue"
                description="Menunggu verifikasi awal"
            />
            <x-statistic-card
                title="Aduan Diproses"
                :value="$statusCounts['diproses']"
                icon="arrow-path"
                color="amber"
                description="Sedang diverifikasi admin"
            />
            <x-statistic-card
                title="Aduan Diteruskan"
                :value="$statusCounts['diteruskan']"
                icon="paper-airplane"
                color="sky"
                description="Diteruskan ke Kepala Bidang"
            />
            <x-statistic-card
                title="Aduan Didisposisikan"
                :value="$statusCounts['didisposisikan']"
                icon="folder"
                color="zinc"
                description="Didisposisikan ke Petugas"
            />
            <x-statistic-card
                title="Aduan Ditindaklanjuti"
                :value="$statusCounts['ditindaklanjuti']"
                icon="wrench"
                color="amber"
                description="Penanganan lapangan berjalan"
            />
            <x-statistic-card
                title="Aduan Selesai"
                :value="$statusCounts['selesai']"
                icon="check-circle"
                color="emerald"
                description="Tuntas ditangani Dinsos"
                badge="Tuntas"
            />
            <x-statistic-card
                title="Aduan Ditolak"
                :value="$statusCounts['ditolak']"
                icon="x-circle"
                color="red"
                description="Tidak memenuhi kriteria"
            />
        </div>
    @else
        <!-- Admin: 6 Statistic Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-statistic-card
                title="Total Aduan"
                :value="$statusCounts['total']"
                icon="document-text"
                color="blue"
                description="Seluruh aduan terdaftar"
            />
            <x-statistic-card
                title="Aduan Baru"
                :value="$statusCounts['baru']"
                icon="sparkles"
                color="blue"
                description="Aduan baru masuk via WA"
                badge="Perlu Respon"
            />
            <x-statistic-card
                title="Aduan Diproses"
                :value="$statusCounts['diproses']"
                icon="arrow-path"
                color="amber"
                description="Tahap verifikasi data"
            />
            <x-statistic-card
                title="Aduan Diteruskan"
                :value="$statusCounts['diteruskan']"
                icon="paper-airplane"
                color="sky"
                description="Diteruskan ke Kepala Bidang"
            />
            <x-statistic-card
                title="Aduan Selesai"
                :value="$statusCounts['selesai']"
                icon="check-circle"
                color="emerald"
                description="Telah selesai ditangani"
                badge="Tuntas"
            />
            <x-statistic-card
                title="Aduan Ditolak"
                :value="$statusCounts['ditolak']"
                icon="x-circle"
                color="red"
                description="Aduan tidak valid / ditolak"
            />
        </div>
    @endif

    <!-- Section: Action Needed (For Admin) -->
    @if(! $isSuperAdmin && $actionNeededComplaints->isNotEmpty())
        <div class="rounded-2xl border border-amber-200 bg-white shadow-xs dark:border-amber-900/50 dark:bg-zinc-900 overflow-hidden">
            <div class="border-b border-amber-100 bg-amber-50/60 px-6 py-4 dark:border-amber-900/40 dark:bg-amber-950/20 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-amber-900 dark:text-amber-200 flex items-center gap-2">
                        <flux:icon name="exclamation-triangle" class="size-5 text-amber-600" />
                        Aduan yang Perlu Diverifikasi
                    </h2>
                    <p class="text-xs text-amber-700 dark:text-amber-300">Aduan berstatus baru dan diproses yang perlu segera ditindaklanjuti</p>
                </div>
                <flux:badge color="amber" size="sm">{{ $actionNeededComplaints->count() }} Aduan</flux:badge>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-100 bg-zinc-50/50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Kode Tiket</th>
                            <th class="px-6 py-3 font-semibold">Nama Pelapor</th>
                            <th class="px-6 py-3 font-semibold">Kategori</th>
                            <th class="px-6 py-3 font-semibold">Bidang</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold">Tanggal Aduan</th>
                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($actionNeededComplaints as $complaint)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/50 transition">
                                <td class="px-6 py-3.5 font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $complaint->ticket_code }}
                                </td>
                                <td class="px-6 py-3.5 text-zinc-800 dark:text-zinc-200">
                                    {{ $complaint->reporter_name }}
                                </td>
                                <td class="px-6 py-3.5 text-zinc-600 dark:text-zinc-300">
                                    {{ $complaint->category?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-blue-600 dark:text-blue-400 font-medium">
                                    {{ $complaint->bidang }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold {{ $statusBadgeClasses[$complaint->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ $statusLabels[$complaint->status] ?? ucfirst($complaint->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-zinc-500 font-mono">
                                    {{ ($complaint->reported_at ?? $complaint->created_at)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a
                                        href="{{ route('public.tracking.index', ['ticket' => $complaint->ticket_code]) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 dark:hover:bg-blue-900"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Overdue Complaints Alert & Table (Melewati Batas Waktu) -->
    @if($overdueComplaints->isNotEmpty())
        <div class="rounded-2xl border border-red-200 bg-white shadow-xs dark:border-red-900/50 dark:bg-zinc-900 overflow-hidden">
            <div class="border-b border-red-100 bg-red-50/70 px-6 py-4 dark:border-red-900/40 dark:bg-red-950/30 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-red-900 dark:text-red-200 flex items-center gap-2">
                        <flux:icon name="clock" class="size-5 text-red-600" />
                        Aduan Melewati Batas Waktu Disposisi
                    </h2>
                    <p class="text-xs text-red-700 dark:text-red-300">Aduan yang belum selesai namun telah melewati batas waktu (due date) disposisi</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-red-600 px-2.5 py-0.5 text-xs font-bold text-white shadow-xs">
                    {{ $overdueComplaints->count() }} Terlambat
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-100 bg-zinc-50/50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Kode Tiket</th>
                            <th class="px-6 py-3 font-semibold">Nama Pelapor</th>
                            <th class="px-6 py-3 font-semibold">Kategori</th>
                            <th class="px-6 py-3 font-semibold">Batas Waktu</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold">Keterangan</th>
                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($overdueComplaints as $complaint)
                            <tr class="hover:bg-red-50/30 dark:hover:bg-red-950/20 transition">
                                <td class="px-6 py-3.5 font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $complaint->ticket_code }}
                                </td>
                                <td class="px-6 py-3.5 text-zinc-800 dark:text-zinc-200">
                                    {{ $complaint->reporter_name }}
                                </td>
                                <td class="px-6 py-3.5 text-zinc-600 dark:text-zinc-300">
                                    {{ $complaint->category?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 font-mono text-red-600 dark:text-red-400 font-semibold">
                                    {{ $complaint->active_due_date ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold {{ $statusBadgeClasses[$complaint->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ $statusLabels[$complaint->status] ?? ucfirst($complaint->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center rounded-md bg-red-100 px-2 py-0.5 text-[11px] font-bold text-red-700 border border-red-200 dark:bg-red-950/60 dark:text-red-300 dark:border-red-800">
                                        Terlambat
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a
                                        href="{{ route('public.tracking.index', ['ticket' => $complaint->ticket_code]) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Recent Complaints Table -->
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Ringkasan Aduan Terbaru</h2>
                <p class="text-xs text-zinc-500">Daftar aduan warga yang baru masuk ke sistem</p>
            </div>
            <flux:badge color="zinc" size="sm">Terbaru</flux:badge>
        </div>

        @if($recentComplaints->isEmpty())
            <div class="p-8 text-center text-sm text-zinc-500">
                Belum ada data aduan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-100 bg-zinc-50/50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Kode Tiket</th>
                            <th class="px-6 py-3 font-semibold">Nama Pelapor</th>
                            <th class="px-6 py-3 font-semibold">Kategori</th>
                            <th class="px-6 py-3 font-semibold">Bidang</th>
                            <th class="px-6 py-3 font-semibold">Status</th>
                            <th class="px-6 py-3 font-semibold">Tanggal Aduan</th>
                            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($recentComplaints as $complaint)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/50 transition">
                                <td class="px-6 py-3.5 font-mono font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $complaint->ticket_code }}
                                </td>
                                <td class="px-6 py-3.5 text-zinc-800 dark:text-zinc-200">
                                    {{ $complaint->reporter_name }}
                                </td>
                                <td class="px-6 py-3.5 text-zinc-600 dark:text-zinc-300">
                                    {{ $complaint->category?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-blue-600 dark:text-blue-400 font-medium">
                                    {{ $complaint->bidang }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[11px] font-semibold {{ $statusBadgeClasses[$complaint->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                        {{ $statusLabels[$complaint->status] ?? ucfirst($complaint->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-zinc-500 font-mono">
                                    {{ ($complaint->reported_at ?? $complaint->created_at)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a
                                        href="{{ route('public.tracking.index', ['ticket' => $complaint->ticket_code]) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300 dark:hover:bg-blue-900"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Charts / Distributions Grid -->
    <div class="grid grid-cols-1 gap-6 {{ $isSuperAdmin ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }}">
        <!-- Distribution by Status -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Distribusi per Status</h2>
            <p class="text-xs text-zinc-500 mb-4">Proporsi status penanganan aduan</p>

            <div class="space-y-3">
                @php
                    $total = max(1, $statusCounts['total']);
                @endphp
                @foreach(['baru' => 'Baru', 'diproses' => 'Diproses', 'diteruskan' => 'Diteruskan', 'didisposisikan' => 'Didisposisikan', 'ditindaklanjuti' => 'Ditindaklanjuti', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $stKey => $stTitle)
                    @php
                        $cnt = $statusCounts[$stKey] ?? 0;
                        $pct = round(($cnt / $total) * 100, 1);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $stTitle }}</span>
                            <span class="font-mono text-zinc-500">{{ $cnt }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full rounded-full {{ match($stKey) { 'baru' => 'bg-blue-500', 'diproses' => 'bg-amber-500', 'diteruskan' => 'bg-sky-500', 'didisposisikan' => 'bg-zinc-500', 'ditindaklanjuti' => 'bg-amber-600', 'selesai' => 'bg-emerald-500', 'ditolak' => 'bg-red-500', default => 'bg-zinc-400' } }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Distribution by Category -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Distribusi per Kategori</h2>
            <p class="text-xs text-zinc-500 mb-4">Sebaran aduan berdasarkan topik permasalahan</p>

            @if(empty($categoryDistribution))
                <p class="text-xs text-zinc-500 italic">Belum ada data kategori aduan.</p>
            @else
                <div class="space-y-3">
                    @foreach($categoryDistribution as $catItem)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300 truncate max-w-[200px]">{{ $catItem['name'] }}</span>
                                <span class="font-mono text-zinc-500">{{ $catItem['count'] }} ({{ $catItem['percentage'] }}%)</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full rounded-full bg-blue-600" style="width: {{ $catItem['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Distribution by Bidang (Super Admin / Admin) -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 {{ ! $isSuperAdmin ? 'lg:col-span-2' : '' }}">
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Distribusi per Bidang</h2>
            <p class="text-xs text-zinc-500 mb-4">Beban kerja dan sebaran aduan per bidang teknis</p>

            <div class="space-y-4">
                @foreach($bidangDistribution as $bKey => $bData)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $bData['label'] }}</span>
                            <span class="font-mono text-zinc-500">{{ $bData['count'] }} ({{ $bData['percentage'] }}%)</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full rounded-full {{ match($bKey) { 'perlindungan_jaminan_sosial' => 'bg-blue-600', 'rehabilitasi_sosial' => 'bg-emerald-600', 'pengembangan_kesejahteraan' => 'bg-amber-600', default => 'bg-zinc-500' } }}" style="width: {{ $bData['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
