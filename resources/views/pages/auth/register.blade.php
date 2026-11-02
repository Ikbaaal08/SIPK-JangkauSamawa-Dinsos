<x-layouts::auth :title="__('Daftar Akun')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            title="Daftar Akun Baru"
            description="Lengkapi data diri Anda untuk membuat akun di Jangkau Samawa"
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Name -->
            <flux:input
                name="name"
                label="Nama Lengkap"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap sesuai identitas"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                label="Alamat Email"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="nama@email.com"
            />

            <!-- WhatsApp Phone Number -->
            <flux:input
                name="phone"
                label="Nomor WhatsApp"
                :value="old('phone')"
                type="tel"
                required
                autocomplete="tel"
                placeholder="081234567890"
            />

            <!-- Password -->
            <flux:input
                name="password"
                label="Kata Sandi"
                type="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                label="Konfirmasi Kata Sandi"
                type="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end mt-2">
                <flux:button type="submit" variant="primary" class="w-full bg-blue-600 hover:bg-blue-700 text-white" data-test="register-user-button">
                    Daftar Akun
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>Sudah memiliki akun?</span>
            <flux:link :href="route('login')" wire:navigate class="text-blue-600 hover:underline">Masuk</flux:link>
        </div>
    </div>
</x-layouts::auth>
