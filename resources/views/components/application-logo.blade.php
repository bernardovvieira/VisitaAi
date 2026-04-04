<div {{ $attributes->merge(['class' => 'flex shrink-0 flex-col items-center leading-none']) }}>
    <a href="{{ url('/') }}" class="flex flex-col items-center gap-0.5 rounded-2xl ring-1 ring-transparent transition hover:ring-slate-200/90 dark:hover:ring-slate-600/80">
        <img src="{{ asset('images/visitaai_rembg.png') }}"
            alt="{{ __('Visita Aí') }}"
            class="-mt-1 mb-0 h-20 w-auto p-0 leading-none sm:h-24" />
        <span class="mb-0.5 bg-gradient-to-r from-blue-700 to-blue-600 bg-clip-text text-lg font-extrabold leading-tight tracking-tight text-transparent sm:text-[1.35rem] dark:from-blue-400 dark:to-blue-300">
            {{ __('Visita Aí') }}
        </span>
    </a>
</div>
