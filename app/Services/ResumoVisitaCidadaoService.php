<?php

namespace App\Services;

use App\Models\Visita;
use Carbon\Carbon;

/**
 * Gera um resumo em linguagem simples da visita para o cidadão na consulta pública.
 * Evita dados sensíveis e informações que possam estigmatizar o imóvel ou causar
 * conflitos entre moradores: não cita nomes de doenças nem detalhes clínicos.
 */
class ResumoVisitaCidadaoService
{
    private const ATIVIDADES = [
        '1' => 'Levantamento de Índice (LI)',
        '2' => 'Levantamento + Tratamento (LI+T)',
        '3' => 'Ponto Estratégico + Tratamento (PPE+T)',
        '4' => 'Tratamento (T)',
        '5' => 'Delimitação de Foco (DF)',
        '6' => 'Pesquisa Vetorial Especial (PVE)',
        '7' => 'LIRAa (Levantamento de Índice Rápido)',
        '8' => 'Ponto Estratégico (PE)',
    ];

    /**
     * Retorna um resumo neutro e não sensível para exibição ao cidadão.
     * Não menciona doenças registradas nem dados que possam causar intrigas.
     */
    public function resumir(Visita $visita, string $enderecoCompleto): string
    {
        $data = $visita->vis_data
            ? Carbon::parse($visita->vis_data)->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y')
            : 'Data não informada';

        $atividade = self::ATIVIDADES[$visita->vis_atividade ?? ''] ?? 'visita de monitoramento';

        $frase = "Em {$data} foi realizada uma visita de vigilância epidemiológica neste endereço, com atividade de {$atividade}. ";

        $frase .= "Foram realizadas ações de monitoramento e orientação conforme protocolo. ";
        $frase .= "Recomenda-se manter medidas de prevenção, como eliminar água parada e manter recipientes cobertos, e seguir as orientações repassadas pela equipe de saúde.";

        return $frase;
    }
}
