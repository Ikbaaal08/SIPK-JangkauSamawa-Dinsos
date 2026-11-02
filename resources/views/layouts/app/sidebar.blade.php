<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gray-50 text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
        <!-- Sidebar Kiri -->
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header class="gap-3">
                <a href="{{ auth()->user()->dashboardRoute() }}" class="flex items-center gap-2.5 font-bold tracking-tight text-zinc-900 dark:text-white" wire:navigate>
                    <div class="flex size-9 items-center justify-center rounded-lg bg-blue-600 text-white font-extrabold shadow-xs">
                        JS
                    </div>
                    <div class="flex flex-col text-start">
                        <span class="text-base font-bold leading-tight text-zinc-900 dark:text-zinc-100">Jangkau Samawa</span>
                        <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">Dinsos Kab. Sumbawa</span>
                    </div>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="Menu Utama" class="grid">
                    @if (auth()->user()->isAdmin())
                        <flux:sidebar.item
                            icon="home"
                            :href="route('admin.dashboard')"
                            :current="request()->routeIs('admin.dashboard')"
                            wire:navigate
                        >
                            Dashboard Admin
                        </flux:sidebar.item>
                    @elseif (auth()->user()->isPetugasPenerima())
                        <flux:sidebar.item
                            icon="home"
                            :href="route('petugas.dashboard')"
                            :current="request()->routeIs('petugas.dashboard')"
                            wire:navigate
                        >
                            Dashboard Penerima
                        </flux:sidebar.item>
                    @elseif (auth()->user()->isKepalaDinas())
                        <flux:sidebar.item
                            icon="home"
                            :href="route('kepala-dinas.dashboard')"
                            :current="request()->routeIs('kepala-dinas.dashboard')"
                            wire:navigate
                        >
                            Dashboard Kepala Dinas
                        </flux:sidebar.item>
                    @elseif (auth()->user()->isPetugasPelaksana())
                        <flux:sidebar.item
                            icon="home"
                            :href="route('pelaksana.dashboard')"
                            :current="request()->routeIs('pelaksana.dashboard')"
                            wire:navigate
                        >
                            Dashboard Pelaksana
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>

                <flux:sidebar.group heading="Akses Cepat" class="grid mt-4">
                    <flux:sidebar.item
                        icon="globe-alt"
                        :href="route('home')"
                        target="_blank"
                    >
                        Portal Publik
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="border-t border-zinc-200 pt-3 dark:border-zinc-800">
                <x-desktop-user-menu class="w-full" :name="auth()->user()->name" />
            </div>
        </flux:sidebar>

        <!-- Navbar Atas -->
        <flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="hidden sm:flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Aplikasi:</span>
                <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Sistem Informasi Pengaduan Kesejahteraan Sosial</span>
            </div>

            <flux:spacer />

            <div class="flex items-center gap-3">
                @php
                    $roleBadgeColor = match (auth()->user()->role) {
                        'admin' => 'blue',
                        'kepala_dinas' => 'emerald',
                        'petugas_pelaksana' => 'amber',
                        default => 'sky',
                    };
                @endphp
                <flux:badge :color="$roleBadgeColor" size="sm" class="capitalize">
                    {{ auth()->user()->roleLabel() }}
                </flux:badge>

                <!-- Mobile / Header User Menu -->
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate font-semibold">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>

                        <flux:menu.separator />

                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            Pengaturan Profil
                        </flux:menu.item>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer text-red-600 dark:text-red-400"
                                data-test="logout-button"
                            >
                                Keluar dari Sistem
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:header>

        <!-- Main Content -->
        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
