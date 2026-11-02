<div class="flex flex-col gap-6">
    <div class="flex flex-col items-center text-center">
        <div class="mb-2 flex size-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
            <flux:icon name="chat-bubble-left-right" class="size-6" />
        </div>
        <flux:heading size="xl" level="1">Verifikasi Nomor WhatsApp</flux:heading>
        <flux:subheading class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            Masukkan 6 digit kode OTP yang telah dikirimkan ke WhatsApp Anda
        </flux:subheading>
    </div>

    @if ($maskedPhone)
        <div class="flex items-center justify-center gap-2 rounded-lg bg-zinc-100 px-3 py-2 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
            <flux:icon name="phone" class="size-4 text-emerald-600" />
            <span>Nomor WhatsApp: <strong class="font-semibold">{{ $maskedPhone }}</strong></span>
        </div>
    @endif

    @if ($errorMessage)
        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700 dark:border-red-900/50 dark:bg-red-950/50 dark:text-red-400">
            <div class="flex items-start gap-2">
                <flux:icon name="exclamation-circle" class="size-4 shrink-0 mt-0.5" />
                <span>{{ $errorMessage }}</span>
            </div>
        </div>
    @endif

    @if ($successMessage)
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/50 dark:text-emerald-400">
            <div class="flex items-start gap-2">
                <flux:icon name="check-circle" class="size-4 shrink-0 mt-0.5" />
                <span>{{ $successMessage }}</span>
            </div>
        </div>
    @endif

    <form wire:submit="verify" class="flex flex-col gap-5">
        <flux:field>
            <flux:label>Kode OTP (6 Digit)</flux:label>
            <flux:input
                wire:model="form.otp"
                type="text"
                maxlength="6"
                placeholder="123456"
                autofocus
                class="text-center font-mono text-2xl tracking-[0.5em] font-bold"
                data-test="otp-input"
            />
            <flux:error name="form.otp" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full bg-blue-600 hover:bg-blue-700 text-white" data-test="verify-button">
            Verifikasi Akun
        </flux:button>
    </form>

    <div
        x-data="{
            cooldown: @entangle('cooldownSeconds'),
            timer: null,
            init() {
                if (this.cooldown > 0) {
                    this.startCountdown();
                }
                this.$watch('cooldown', (val) => {
                    if (val > 0 && !this.timer) {
                        this.startCountdown();
                    }
                });
            },
            startCountdown() {
                if (this.timer) clearInterval(this.timer);
                this.timer = setInterval(() => {
                    if (this.cooldown > 0) {
                        this.cooldown--;
                    } else {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                }, 1000);
            }
        }"
        class="flex flex-col items-center gap-2 text-center text-xs text-zinc-500 dark:text-zinc-400"
    >
        <span>Tidak menerima kode OTP?</span>

        <button
            type="button"
            wire:click="resend"
            :disabled="cooldown > 0"
            class="font-semibold text-blue-600 hover:underline disabled:text-zinc-400 disabled:no-underline dark:text-blue-400 cursor-pointer disabled:cursor-not-allowed"
            data-test="resend-otp-button"
        >
            <span x-show="cooldown <= 0">Kirim Ulang Kode OTP</span>
            <span x-show="cooldown > 0" x-text="`Kirim Ulang dalam ${cooldown} detik`"></span>
        </button>
    </div>

    <div class="border-t border-zinc-200 pt-4 text-center text-xs dark:border-zinc-800">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:underline cursor-pointer">
                Masuk dengan akun lain
            </button>
        </form>
    </div>
</div>

