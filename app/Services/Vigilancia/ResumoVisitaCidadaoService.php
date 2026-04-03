<?php

namespace App\Services\Vigilancia;

use App\Models\Visita;
use Carbon\Carbon;

/**
 * Gera um resumo em linguagem simples da visita para o cidadão na consulta pública.
 * Evita dados sensíveis e informações que possam estigmatizar o imóvel ou causar
 * conflitos entre moradores: não cita nomes de doenças nem detalhes clínicos.
 */
class ResumoVisitaCidadaoService
{
    /**
     * Retorna um resumo neutro e não sensível para exibição ao cidadão.
     * Não menciona tipo de visita, doenças nem detalhes que identifiquem situações do imóvel.
     */
    public function resumir(Visita $visita, string $enderecoCompleto): string
    {
        $data = $visita->vis_data
            ? Carbon::parse($visita->vis_data)->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y')
            : 'Data não informada';

        $frase = "Em {$data} foi realizada uma visita de vigilância entomológica e controle vetorial neste endereço. ";

        $frase .= 'Foram realizadas ações de monitoramento e orientação conforme protocolo. ';
        $frase .= 'Recomenda-se manter medidas de prevenção, como eliminar água parada e manter recipientes cobertos, e seguir as orientações repassadas pela equipe de saúde.';

        return $frase;
    }
}
