@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4 rounded-xl border border-red-300/90 bg-red-50/85 px-4 py-3 shadow-sm backdrop-blur-sm dark:border-red-900/55 dark:bg-red-950/35']) }} role="alert">
        <div class="text-sm font-bold text-red-800 dark:text-red-200">
            {{ __('Ops! Algo deu errado.') }}
        </div>
        @if ($errors->count() === 1)
            <p class="mt-2 text-sm font-medium text-red-700 dark:text-red-300">{{ $errors->first() }}</p>
        @else
            <ul class="mt-2 list-inside list-disc space-y-0.5 text-sm font-medium text-red-700 dark:text-red-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
