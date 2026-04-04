<?php

namespace App\Support;

use App\Models\Morador;

class VisitaOcupantesObservacao
{
    /**
     * Filtra observações por ocupantes que pertencem ao local. Retorna null se vazio.
     *
     * @param  array<string|int, mixed>  $raw  [mor_id => texto]
     * @return array<string, string>|null
     */
    public static function fromInputArray(mixed $raw, int $fkLocalId): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        $allowed = Morador::query()
            ->where('fk_local_id', $fkLocalId)
            ->pluck('mor_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allowedSet = array_fill_keys($allowed, true);
        $out = [];

        foreach ($raw as $key => $text) {
            $id = (int) $key;
            if (! isset($allowedSet[$id])) {
                continue;
            }
            $s = is_string($text) ? trim($text) : '';
            if ($s !== '') {
                $out[(string) $id] = $s;
            }
        }

        return $out === [] ? null : $out;
    }
}
