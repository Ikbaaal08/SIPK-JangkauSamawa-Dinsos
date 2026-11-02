<x-layouts::auth :title="__('Masuk')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            title="Jangkau Samawa"
            description="Sistem Informasi Pengaduan Kesejahteraan Sosial Dinas Sosial Kabupaten Sumbawa"
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                label="Alamat Email"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@jangkausamawa.id"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    label="Kata Sandi"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        Lupa kata sandi?
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" label="Ingat saya" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white" data-test="login-button">
                    Masuk ke Sistem
                </flux:button>
            </div>
        </form>

        @if (app(\App\Services\FeatureService::class)->isEnabled('registration'))
            <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                <span>Belum memiliki akun?</span>
                <flux:link :href="route('register')" wire:navigate class="text-blue-600 hover:underline">
                    Daftar akun baru
                </flux:link>
            </div>
        @endif

        @if (app(\App\Services\FeatureService::class)->isEnabled('public_tracking'))
            <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                <span>Warga ingin cek status aduan?</span>
                <flux:link :href="route('public.tracking.index')" wire:navigate class="text-blue-600 hover:underline">
                    Lacak Aduan di sini
                </flux:link>
            </div>
        @endif

        <div class="text-xs text-center text-zinc-500 dark:text-zinc-400">
            Pemerintah Kabupaten Sumbawa — Dinas Sosial
        </div>
    </div>
</x-layouts::auth>
