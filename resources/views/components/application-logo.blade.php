<div {{ $attributes->merge(['class' => 'flex shrink-0 flex-col items-center leading-none']) }}>
    <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 rounded-2xl ring-1 ring-transparent transition hover:ring-slate-200/90 dark:hover:ring-slate-600/80">
        <img src="{{ asset('images/visitaai.svg') }}"
            alt="{{ config('app.brand') }}"
            width="64"
            height="64"
            class="-mt-0.5 mb-0 h-16 w-auto p-0 leading-none object-contain sm:h-[4.25rem]" />
        <span class="mb-0.5 text-base font-extrabold leading-tight tracking-tight text-black sm:text-lg dark:text-white">
            {{ config('app.brand') }}
        </span>
    </a>
</div>
