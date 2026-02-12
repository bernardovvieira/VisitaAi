@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4']) }}>
        <div class="font-medium text-red-600">
            {{ __('Ops! Algo deu errado.') }}
        </div>
        @if ($errors->count() === 1)
            <p class="mt-3 text-sm text-red-600">{{ $errors->first() }}</p>
        @else
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
