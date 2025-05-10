<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Doenca;

class DoencaRequest extends FormRequest
{
    public function authorize()
    {
        // Usa a policy: só permite se o usuário puder criar doenças
        return $this->user()->can('create', Doenca::class);
    }

    public function rules()
    {
        $doencaId = $this->route('doenca')?->doe_id; 
    
        $uniqueRule = $this->isMethod('POST')
            // create: só exige que não exista outro com o mesmo nome
            ? 'unique:doencas,doe_nome'
            // update: ignora o registro atual, referenciando a coluna doe_id
            : "unique:doencas,doe_nome,{$doencaId},doe_id";
    
        return [
            'doe_nome'               => ['required','string','max:255',$uniqueRule],
            'doe_sintomas'           => 'required|array|min:1',
            'doe_sintomas.*'         => 'string',
            'doe_transmissao'        => 'required|array|min:1',
            'doe_transmissao.*'      => 'string',
            'doe_medidas_controle'   => 'required|array|min:1',
            'doe_medidas_controle.*' => 'string',
        ];
    }
    
}
