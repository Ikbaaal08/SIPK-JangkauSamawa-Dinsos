@props([
    'title',
    'value',
    'icon' => 'document-text',
    'color' => 'blue',
    'description' => null,
    'badge' => null,
])

@php
    $colorClasses = match ($color) {
        'blue' => [
            'border' => 'border-l-blue-600',
            'iconBg' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400',
            'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        ],
        'amber' => [
            'border' => 'border-l-amber-500',
            'iconBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
            'badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        ],
        'emerald' => [
            'border' => 'border-l-emerald-600',
            'iconBg' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400',
            'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        ],
        'sky' => [
            'border' => 'border-l-sky-500',
            'iconBg' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400',
            'badge' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
        ],
        'red' => [
            'border' => 'border-l-red-600',
            'iconBg' => 'bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400',
            'badge' => 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        ],
        default => [
            'border' => 'border-l-zinc-500',
            'iconBg' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
            'badge' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        ],
    };
@endphp

<div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-5 shadow-xs transition duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-md border-l-4 {{ $colorClasses['border'] }} dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
            {{ $title }}
        </p>
        <div class="flex size-10 items-center justify-center rounded-lg {{ $colorClasses['iconBg'] }}">
            <flux:icon :name="$icon" class="size-5" />
        </div>
    </div>

    <div class="mt-3 flex items-baseline justify-between">
        <p class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ number_format($value) }}
        </p>
        @if ($badge)
            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $colorClasses['badge'] }}">
                {{ $badge }}
            </span>
        @endif
    </div>

    @if ($description)
        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
            {{ $description }}
        </p>
    @endif
</div>

