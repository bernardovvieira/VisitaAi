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
            'vis_data'        => ['required', 'date'],
            'vis_observacoes' => ['nullable', 'string'],
            'fk_local_id'     => ['required', 'exists:locais,loc_id'],
            'doencas'         => ['nullable', 'array'],
            'doencas.*'       => ['exists:doencas,doe_id'],
            'vis_tipo' => ['required', 'in:LI+T,LIRAa'],
        ];
    }
}