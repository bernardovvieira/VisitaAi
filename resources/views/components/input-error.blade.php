@props(['messages'])

@if ($messages && (is_array($messages) ? count($messages) > 0 : true))
    <div {{ $attributes->merge(['class' => 'mt-1 text-sm text-red-600 dark:text-red-400']) }} role="alert">
        @if (is_array($messages))
            @if (count($messages) === 1)
                <p>{{ $messages[0] }}</p>
            @else
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($messages as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            @endif
        @else
            <p>{{ $messages }}</p>
        @endif
    </div>
@endif
