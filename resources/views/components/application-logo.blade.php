<div {{ $attributes->merge(['class' => 'flex shrink-0 flex-col items-center leading-none']) }}>
    <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 rounded-2xl ring-1 ring-transparent transition hover:ring-slate-200/90 dark:hover:ring-slate-600/80">
        <img src="{{ asset('images/visitaai.svg') }}"
            alt="{{ __('Visita Aí') }}"
            width="64"
            height="64"
            class="-mt-0.5 mb-0 h-16 w-auto p-0 leading-none object-contain sm:h-[4.25rem]" />
        <span class="mb-0.5 bg-gradient-to-r from-blue-700 to-blue-600 bg-clip-text text-base font-extrabold leading-tight tracking-tight text-transparent sm:text-lg dark:bg-none dark:text-white">
            {{ __('Visita Aí') }}
        </span>
    </a>
</div>
