<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Aceita JSON array no BD, ou texto simples legado (uma linha ou lista separada por vírgula).
 * Evita JsonException do cast "array" nativo quando o conteúdo não é JSON válido.
 *
 * @implements CastsAttributes<list<string>|array<int|string, mixed>, mixed>
 */
class FlexibleStringOrJsonArray implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $value = (string) $value;
        $trim = trim($value);
        if ($trim === '') {
            return [];
        }

        $first = $trim[0] ?? '';
        if ($first === '[' || $first === '{') {
            try {
                $decoded = json_decode($trim, true, 512, JSON_THROW_ON_ERROR);

                return is_array($decoded) ? $decoded : [$decoded];
            } catch (\Throwable) {
                return array_values(array_filter(array_map('trim', explode(',', $trim)), fn ($s) => $s !== ''));
            }
        }

        if (str_contains($trim, ',')) {
            return array_values(array_filter(array_map('trim', explode(',', $trim)), fn ($s) => $s !== ''));
        }

        return [$trim];
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            return json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
        }

        return json_encode([], JSON_UNESCAPED_UNICODE);
    }
}
