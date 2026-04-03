<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-md border border-transparent bg-red-600 px-3 py-1.5 text-[13px] font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40 active:bg-red-700']) }}>
    {{ $slot }}
</button>
