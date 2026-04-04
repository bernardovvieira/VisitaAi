<button {{ $attributes->merge(['type' => 'submit', 'class' => 'v-btn-primary']) }}>
    {{ $slot }}
</button>
