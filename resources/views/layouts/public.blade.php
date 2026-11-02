<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-full bg-gray-50 text-gray-900 antialiased dark:bg-zinc-950 dark:text-zinc-100 flex flex-col">
        <!-- Top Navigation Bar -->
        <header class="border-b border-gray-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-600 font-extrabold text-white shadow-sm">
                        JS
                    </div>
                    <div>
                        <div class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                            Jangkau Samawa
                        </div>
                        <div class="text-xs text-gray-500 dark:text-zinc-400">
                            Dinas Sosial Kabupaten Sumbawa
                        </div>
                    </div>
                </a>

                <nav class="flex items-center gap-3 sm:gap-4 text-sm font-medium">
                    <a
                        href="{{ route('home') }}"
                        class="text-gray-600 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400 transition"
                        wire:navigate
                    >
                        Beranda
                    </a>
                    <a
                        href="{{ route('public.tracking.index') }}"
                        class="text-blue-600 font-semibold dark:text-blue-400"
                        wire:navigate
                    >
                        Lacak Aduan
                    </a>

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-blue-700 transition"
                            wire:navigate
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 transition"
                            wire:navigate
                        >
                            Masuk Petugas
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-8 sm:py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6">
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white py-6 text-center text-xs text-gray-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
            <div class="mx-auto max-w-5xl px-4">
                <p class="font-medium text-gray-700 dark:text-zinc-300">
                    Sistem Informasi Pengaduan Kesejahteraan Sosial (Jangkau Samawa)
                </p>
                <p class="mt-1">
                    Dinas Sosial Kabupaten Sumbawa &copy; {{ date('Y') }}. Hak Cipta Dilindungi.
                </p>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>

