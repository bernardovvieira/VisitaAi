@props([
    'title' => null,
    'description' => null,
    'icon' => 'heroicon-o-inbox',
])

<div {{ $attributes->class(['rounded-xl border border-dashed border-slate-200/80 bg-slate-50/80 px-4 py-10 text-center dark:border-slate-700 dark:bg-slate-900/40']) }}>
    <div class="mx-auto flex max-w-sm flex-col items-center justify-center">
        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
            <x-dynamic-component :component="$icon" class="h-7 w-7 shrink-0 text-slate-400 dark:text-slate-500" />
        </div>
        @if(filled($title))
            <p class="font-medium text-slate-700 dark:text-slate-300">{{ $title }}</p>
        @endif
        @if(filled($description))
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
        @endif
        {{ $slot }}
    </div>
</div>