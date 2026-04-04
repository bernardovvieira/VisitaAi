<button {{ $attributes->merge(['type' => 'submit', 'class' => 'v-btn-danger']) }}>
    {{ $slot }}
</button>
