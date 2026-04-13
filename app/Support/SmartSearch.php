<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SmartSearch
{
    public static function supportsPhonetic(): bool
    {
        return DB::connection()->getDriverName() !== 'sqlite';
    }

    public static function foldExpr(string $columnExpr): string
    {
        $expr = 'LOWER(COALESCE('.$columnExpr.', ""))';
        $map = [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ];

        foreach ($map as $from => $to) {
            $expr = "REPLACE($expr, '$from', '$to')";
        }

        return $expr;
    }

    /**
     * @return array<int, string>
     */
    public static function terms(?string $input): array
    {
        $raw = trim((string) $input);
        if ($raw === '') {
            return [];
        }

        $lower = mb_strtolower($raw);
        $ascii = (string) Str::of($raw)
            ->ascii()
            ->lower()
            ->replaceMatches('/\s+/', ' ')
            ->trim();

        $tokens = preg_split('/[^[:alnum:]]+/u', $ascii) ?: [];
        $tokens = array_values(array_filter(array_map(static fn ($t) => trim((string) $t), $tokens), static fn ($t) => $t !== ''));

        $all = array_merge([$lower, $ascii], $tokens);

        // Remove duplicados mantendo ordem estável.
        $seen = [];
        $out = [];
        foreach ($all as $term) {
            if ($term === '') {
                continue;
            }
            if (isset($seen[$term])) {
                continue;
            }
            $seen[$term] = true;
            $out[] = $term;
        }

        return $out;
    }
}
