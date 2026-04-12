<?php

namespace App\Support;

class SocioeconomicoEtiquetas
{
    private const NAO_INFORMADO = 'nao_informado';

    /**
     * @param  array<string, string>|null  $fallback
     */
    public static function opcao(?string $configKey, ?string $value, ?array $fallback = null): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if ($value === self::NAO_INFORMADO) {
            return __('Não informado');
        }
        $opts = $configKey !== null
            ? config('visitaai_socioeconomico.'.$configKey, [])
            : ($fallback ?? []);
        if (! is_array($opts)) {
            return $value;
        }

        $label = $opts[$value] ?? $value;
        return is_string($label) ? __($label) : (string) $label;
    }

    public static function municipioRenda(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if ($value === self::NAO_INFORMADO) {
            return __('Não informado');
        }
        $opts = config('visitaai_municipio.renda_faixa_opcoes', []);
        if (is_array($opts) && isset($opts[$value])) {
            $label = $opts[$value];
            return is_string($label) ? __($label) : (string) $label;
        }

        return $value;
    }

    public static function municipioEscolaridade(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if ($value === self::NAO_INFORMADO) {
            return __('Não informado');
        }
        $opts = config('visitaai_municipio.escolaridade_opcoes', []);
        if (is_array($opts) && isset($opts[$value])) {
            $label = $opts[$value];
            return is_string($label) ? __($label) : (string) $label;
        }

        return $value;
    }

    public static function municipioCor(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if ($value === self::NAO_INFORMADO) {
            return __('Não informado');
        }
        $opts = config('visitaai_municipio.cor_raca_opcoes', []);
        if (is_array($opts) && isset($opts[$value])) {
            $label = $opts[$value];
            return is_string($label) ? __($label) : (string) $label;
        }

        return $value;
    }

    public static function municipioTrabalho(?string $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if ($value === self::NAO_INFORMADO) {
            return __('Não informado');
        }
        $opts = config('visitaai_municipio.situacao_trabalho_opcoes', []);
        if (is_array($opts) && isset($opts[$value])) {
            $label = $opts[$value];
            return is_string($label) ? __($label) : (string) $label;
        }

        return $value;
    }

    public static function simNaoBool(?bool $v): string
    {
        if ($v === null) {
            return '-';
        }

        return $v ? __('Sim') : __('Não');
    }
}
