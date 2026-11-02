@props([
    'status',
])

@if ($status)
    @php
        $isWarning = str_contains(strtolower($status), 'dinonaktifkan') || str_contains(strtolower($status), 'gagal') || str_contains(strtolower($status), 'tidak');
    @endphp
    <div {{ $attributes->merge(['class' => 'font-medium text-sm ' . ($isWarning ? 'text-amber-700 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 rounded-lg p-3 dark:text-amber-300' : 'text-green-600 dark:text-green-400')]) }}>
        {{ $status }}
    </div>
@endif
