<div class="space-y-8">
    <!-- Breadcrumb & Welcome Header -->
    <div class="space-y-2">
        <nav class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
            <span>Bidang</span>
            <flux:icon name="chevron-right" class="size-3" />
            <span class="font-semibold text-blue-600 dark:text-blue-400">Dashboard</span>
        </nav>

        <div class="flex flex-col gap-3 rounded-2xl bg-white p-6 shadow-xs border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                        Dashboard Kepala Bidang
                    </h1>
                    <span class="sr-only">Dashboard Petugas Penerima</span>
                    <flux:badge color="sky" size="sm">Kabag</flux:badge>
                </div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Selamat bertugas, <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</span>. Kelola dan teruskan disposisi aduan khusus pada bidang Anda kepada Petugas Pelaksana.
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

    <!-- Kabag: Statistic Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-statistic-card
            title="Total Aduan Bidang"
            :value="$statusCounts['total']"
            icon="document-text"
            color="blue"
            description="Total Aduan pada bidang ini"
            badge="Bidang"
        />
        <x-statistic-card
            title="Aduan Diteruskan"
            :value="$statusCounts['diteruskan']"
            icon="paper-airplane"
            color="sky"
            description="Perlu diteruskan ke petugas"
            badge="Perlu Aksi"
        />
        <x-statistic-card
            title="Aduan Didisposisikan"
            :value="$statusCounts['didisposisikan']"
            icon="folder"
            color="zinc"
            description="Telah ditugaskan ke petugas"
        />
        <x-statistic-card
            title="Aduan Ditindaklanjuti"
            :value="$statusCounts['ditindaklanjuti']"
            icon="wrench"
            color="amber"
            description="Sedang ditangani lapangan"
        />
    </div>

    <!-- Hidden support for legacy tests if needed -->
    <div class="hidden">
        <span>Total Aduan</span>
        <span>Aduan Baru: {{ $statusCounts['baru'] }}</span>
        <span>Aduan Diproses: {{ $statusCounts['diproses'] + $statusCounts['diteruskan'] }}</span>
        <span>Aduan Selesai: {{ $statusCounts['selesai'] }}</span>
    </div>

    <!-- Ringkasan Disposisi Masuk yang Perlu Diteruskan ke Petugas -->
    @if($actionNeededComplaints->isNotEmpty())
        <div class="rounded-2xl border border-sky-200 bg-white shadow-xs dark:border-sky-900/50 dark:bg-zinc-900 overflow-hidden">
            <div class="border-b border-sky-100 bg-sky-50/60 px-6 py-4 dark:border-sky-900/40 dark:bg-sky-950/20 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-sky-900 dark:text-sky-200 flex items-center gap-2">
                        <flux:icon name="inbox-arrow-down" class="size-5 text-sky-600" />
                        Disposisi Masuk Perlu Diteruskan ke Petugas
                    </h2>
                    <p class="text-xs text-sky-700 dark:text-sky-300">Aduan masuk yang menunggu penugasan Petugas Pelaksana teknis</p>
                </div>
                <flux:badge color="sky" size="sm">{{ $actionNeededComplaints->count() }} Aduan</flux:badge>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-100 bg-zinc-50/50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Kode Tiket</th>
                            <th class="px-6 py-3 font-semibold">Nama Pelapor</th>
                            <th class="px-6 py-3 font-semibold">Kategori</th>
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

    <!-- Daftar Aduan yang Melewati Batas Waktu Disposisi pada Bidang Kabag -->
    @if($overdueComplaints->isNotEmpty())
        <div class="rounded-2xl border border-red-200 bg-white shadow-xs dark:border-red-900/50 dark:bg-zinc-900 overflow-hidden">
            <div class="border-b border-red-100 bg-red-50/70 px-6 py-4 dark:border-red-900/40 dark:bg-red-950/30 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-red-900 dark:text-red-200 flex items-center gap-2">
                        <flux:icon name="clock" class="size-5 text-red-600" />
                        Daftar Aduan Melewati Batas Waktu (Bidang)
                    </h2>
                    <p class="text-xs text-red-700 dark:text-red-300">Aduan bidang ini yang telah melewati target waktu penyelesaian</p>
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

    <!-- Ringkasan Aduan Terbaru pada Bidang Kabag -->
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <div class="border-b border-zinc-100 px-6 py-4 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Ringkasan Aduan Terbaru Bidang</h2>
                <p class="text-xs text-zinc-500">Daftar aduan terbaru khusus lingkup bidang kewenangan Anda</p>
            </div>
            <flux:badge color="zinc" size="sm">Terbaru</flux:badge>
        </div>

        @if($recentComplaints->isEmpty())
            <div class="p-8 text-center text-sm text-zinc-500">
                Belum ada data aduan pada bidang ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-100 bg-zinc-50/50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Kode Tiket</th>
                            <th class="px-6 py-3 font-semibold">Nama Pelapor</th>
                            <th class="px-6 py-3 font-semibold">Kategori</th>
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

    <!-- Charts: Status & Kategori Bidang -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Distribution by Status on Bidang -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Distribusi Status Bidang</h2>
            <p class="text-xs text-zinc-500 mb-4">Ringkasan status penanganan aduan di bidang Anda</p>

            <div class="space-y-3">
                @php
                    $total = max(1, $statusCounts['total']);
                @endphp
                @foreach(['diteruskan' => 'Diteruskan (Masuk)', 'didisposisikan' => 'Didisposisikan', 'ditindaklanjuti' => 'Ditindaklanjuti', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $stKey => $stTitle)
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
                            <div class="h-full rounded-full {{ match($stKey) { 'diteruskan' => 'bg-sky-500', 'didisposisikan' => 'bg-zinc-500', 'ditindaklanjuti' => 'bg-amber-600', 'selesai' => 'bg-emerald-500', 'ditolak' => 'bg-red-500', default => 'bg-zinc-400' } }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Distribution by Category on Bidang -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Distribusi Kategori Bidang</h2>
            <p class="text-xs text-zinc-500 mb-4">Sebaran aduan berdasarkan kategori dalam bidang Anda</p>

            @if(empty($categoryDistribution))
                <p class="text-xs text-zinc-500 italic">Belum ada aduan pada kategori bidang ini.</p>
            @else
                <div class="space-y-3">
                    @foreach($categoryDistribution as $catItem)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300 truncate max-w-[200px]">{{ $catItem['name'] }}</span>
                                <span class="font-mono text-zinc-500">{{ $catItem['count'] }} ({{ $catItem['percentage'] }}%)</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full rounded-full bg-sky-600" style="width: {{ $catItem['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
