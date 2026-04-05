<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

/**
 * Acesso centralizado à terminologia alinhada ao Ministério da Saúde (MS).
 * Fonte: config/ms_terminologia.php
 */
class MsTerminologia
{
    /**
     * Rótulo curto do perfil para exibição (Gestor Municipal, ACE, ACS).
     */
    public static function perfilLabel(?string $perfil): string
    {
        if ($perfil === null || $perfil === '') {
            return '';
        }
        $perfis = Config::get('ms_terminologia.perfis', []);
        $p = $perfis[$perfil] ?? null;
        if ($p !== null) {
            $curto = $p['label_curto'] ?? $p['label_abrev'] ?? $perfil;
            $sigla = $p['sigla'] ?? $p['label_abrev'] ?? '';
            $s = $sigla !== '' ? $curto.' ('.$sigla.')' : $curto;

            return __($s);
        }

        return ucfirst(str_replace('_', ' ', $perfil));
    }

    /**
     * Rótulo curto para navbar: apenas "Gestor", "ACE" ou "ACS".
     */
    public static function perfilLabelNav(?string $perfil): string
    {
        if ($perfil === null || $perfil === '') {
            return '';
        }
        if ($perfil === 'gestor') {
            return __('Gestor');
        }
        if ($perfil === 'agente_endemias') {
            return 'ACE';
        }
        if ($perfil === 'agente_saude') {
            return 'ACS';
        }

        return self::perfilSigla($perfil) ?: ucfirst(str_replace('_', ' ', $perfil));
    }

    /**
     * Sigla do perfil quando existir (ACE, ACS); senão vazio.
     */
    public static function perfilSigla(?string $perfil): string
    {
        if ($perfil === null || $perfil === '') {
            return '';
        }
        $p = Config::get("ms_terminologia.perfis.{$perfil}", []);

        return $p['sigla'] ?? $p['label_abrev'] ?? '';
    }

    /**
     * Nota MS do perfil (para tooltip/documentação).
     */
    public static function perfilNotaMs(?string $perfil): ?string
    {
        if ($perfil === null || $perfil === '') {
            return null;
        }
        $p = Config::get("ms_terminologia.perfis.{$perfil}", []);
        $nota = $p['nota_ms'] ?? null;
        if ($nota === null || $nota === '') {
            return null;
        }

        return __($nota);
    }

    /**
     * Label curto da atividade PNCD para tabelas (ex.: LIRAa, LI).
     */
    public static function atividadeLabel(?string $codigoAtividade): string
    {
        if ($codigoAtividade === null || $codigoAtividade === '') {
            return '';
        }
        $at = Config::get("ms_terminologia.atividades_pncd.{$codigoAtividade}", []);
        $label = $at['label'] ?? $at['codigo'] ?? $codigoAtividade;

        return __($label);
    }

    /**
     * Código completo da atividade para PDF/relatório (ex.: 7-LIRAa).
     */
    public static function atividadeCodigo(?string $codigoAtividade): string
    {
        if ($codigoAtividade === null || $codigoAtividade === '') {
            return '';
        }
        $at = Config::get("ms_terminologia.atividades_pncd.{$codigoAtividade}", []);

        return $at['codigo'] ?? $codigoAtividade;
    }

    /**
     * Nome completo da atividade (ex.: LIRAa (Levantamento de Índice Rápido...)).
     */
    public static function atividadeNome(?string $codigoAtividade): string
    {
        if ($codigoAtividade === null || $codigoAtividade === '') {
            return '';
        }
        $at = Config::get("ms_terminologia.atividades_pncd.{$codigoAtividade}", []);
        $nome = $at['nome'] ?? $at['codigo'] ?? $codigoAtividade;

        return __($nome);
    }

    /**
     * Todas as atividades PNCD no formato [ '1' => '1-LI', '7' => '7-LIRAa', ... ] para PDF/legenda.
     */
    public static function atividadesPncdCodigos(): array
    {
        $at = Config::get('ms_terminologia.atividades_pncd', []);
        $out = [];
        foreach ($at as $k => $v) {
            $out[$k] = $v['codigo'] ?? $k;
        }

        return $out;
    }

    /**
     * Todas as atividades no formato [ '1' => 'LI', '7' => 'LIRAa', ... ] para listagens.
     */
    public static function atividadesPncdLabels(): array
    {
        $at = Config::get('ms_terminologia.atividades_pncd', []);
        $out = [];
        foreach ($at as $k => $v) {
            $out[$k] = $v['label'] ?? $v['codigo'] ?? $k;
        }

        return $out;
    }

    /**
     * Label do tipo de visita (N = Normal, R = Recuperação). Conforme PNCD/Diretriz MS.
     */
    public static function visitaTipoLabel(?string $tipo): string
    {
        if ($tipo === null || $tipo === '') {
            return '';
        }
        $v = Config::get("ms_terminologia.visita_tipo.{$tipo}", []);
        $label = $v['label'] ?? $tipo;

        return __($label);
    }

    /**
     * Referência curta para rodapé/PDF (ex.: "Conforme Diretrizes MS - Vigilância Entomológica e Controle Vetorial").
     */
    public static function referenciasRodape(): array
    {
        return Config::get('ms_terminologia.referencias', []);
    }
}
