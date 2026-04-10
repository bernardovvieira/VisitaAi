@php
    /** @var \App\Models\Local|null $local */
    $sf = old('socio');
    if (! is_array($sf)) {
        $sf = [];
    }
    if (count($sf) === 0 && isset($local) && $local !== null) {
        $local->loadMissing('socioeconomico');
        if ($local->socioeconomico) {
            $sf = $local->socioeconomico->toFormArray();
        }
    }
    $sv = fn (string $k) => old('socio.'.$k, $sf[$k] ?? '');
@endphp
