<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Visita;

class VisitaRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Visita::class);
    }

    public function rules()
    {
        return [
            'vis_data'             => ['required', 'date'],
            'vis_observacoes'      => ['nullable', 'string'],
            'fk_local_id'          => ['required', 'exists:locais,loc_id'],
            'doencas'              => ['nullable', 'array'],
            'doencas.*'            => ['exists:doencas,doe_id'],

            // PNCD
            'vis_ciclo'            => ['nullable', 'string', 'max:10'],
            'vis_atividade'        => ['nullable', 'in:1,2,3,4,5,6,7,8'],
            'vis_concluida'        => ['nullable', 'boolean'],
            'vis_visita_tipo'      => ['nullable', 'in:N,R'],
            'vis_pendencias'       => ['nullable', 'boolean'],

            'vis_amos_inicial'     => ['nullable', 'integer', 'min:0'],
            'vis_amos_final'       => ['nullable', 'integer', 'min:0'],
            'vis_coleta_amostra'   => ['nullable', 'boolean'],
            'vis_qtd_tubitos'      => ['nullable', 'integer', 'min:0'],
            'vis_imoveis_tratados' => ['nullable', 'integer', 'min:0'],
            'vis_depositos_eliminados' => ['nullable', 'integer', 'min:0'],

            // Depósitos inspecionados
            'insp_a1' => ['nullable', 'integer', 'min:0'],
            'insp_a2' => ['nullable', 'integer', 'min:0'],
            'insp_b'  => ['nullable', 'integer', 'min:0'],
            'insp_c'  => ['nullable', 'integer', 'min:0'],
            'insp_d1' => ['nullable', 'integer', 'min:0'],
            'insp_d2' => ['nullable', 'integer', 'min:0'],
            'insp_e'  => ['nullable', 'integer', 'min:0'], 

            // Tratamentos
            'tratamentos'                         => ['nullable', 'array'],
            'tratamentos.*.trat_tipo'             => ['nullable', 'in:Larvicida,Adulticida'],
            'tratamentos.*.trat_forma'            => ['nullable', 'in:Focal,Perifocal'],
            'tratamentos.*.linha'                 => ['nullable', 'integer', 'min:1'],
            'tratamentos.*.qtd_gramas'            => ['nullable', 'integer', 'min:0'],
            'tratamentos.*.qtd_depositos_tratados'=> ['nullable', 'integer', 'min:0'],
            'tratamentos.*.qtd_cargas'            => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('tratamentos')) {
            $this->merge([
                'tratamentos' => collect($this->input('tratamentos'))->filter(function ($t) {
                    return !empty($t['trat_forma']) || !empty($t['trat_tipo']) ||
                        !empty($t['linha']) || !empty($t['qtd_gramas']) ||
                        !empty($t['qtd_depositos_tratados']) || !empty($t['qtd_cargas']);
                })->values()->toArray()
            ]);
        }
    }
}