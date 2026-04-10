<?php

namespace App\Support;

class SocioeconomicoEtiquetas
{
    /**
     * @param  array<string, string>|null  $fallback
     */
    public static function opcao(?string $configKey, ?string $value, ?array $fallback = null): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $opts = $configKey !== null
            ? config('visitaai_socioeconomico.'.$configKey, [])
            : ($fallback ?? []);
        if (! is_array($opts)) {
            return $value;
        }

        return $opts[$value] ?? $value;
    }

    public static function municipioRenda(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $opts = config('visitaai_municipio.renda_faixa_opcoes', []);

        return is_array($opts) && isset($opts[$value]) ? $opts[$value] : $value;
    }

    public static function municipioEscolaridade(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $opts = config('visitaai_municipio.escolaridade_opcoes', []);

        return is_array($opts) && isset($opts[$value]) ? $opts[$value] : $value;
    }

    public static function municipioCor(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $opts = config('visitaai_municipio.cor_raca_opcoes', []);

        return is_array($opts) && isset($opts[$value]) ? $opts[$value] : $value;
    }

    public static function municipioTrabalho(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        $opts = config('visitaai_municipio.situacao_trabalho_opcoes', []);

        return is_array($opts) && isset($opts[$value]) ? $opts[$value] : $value;
    }

    public static function simNaoBool(?bool $v): string
    {
        if ($v === null) {
            return '-';
        }

        return $v ? __('Sim') : __('Não');
    }
}
