@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'v-input disabled:cursor-not-allowed disabled:opacity-60']) }}>
