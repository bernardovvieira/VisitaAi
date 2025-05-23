<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Local;

class LocalRequest extends FormRequest
{
    public function authorize()
    {
        // Usa a policy: só permite se o usuário puder criar/editar locais
        return $this->user()->can('create', Local::class);
    }

    public function rules()
    {
        $localId = $this->route('local')?->loc_id;

        return [
            'loc_endereco'       => ['required', 'string', 'max:255', "unique:locais,loc_endereco,{$localId},loc_id"],
            'loc_bairro'         => ['required', 'string', 'max:255'],
            'loc_coordenadas'    => ['required', 'string', 'max:255'],
            'loc_codigo_unico'   => ['required', 'string', 'max:255', "unique:locais,loc_codigo_unico,{$localId},loc_id"],
        ];
    }
}
