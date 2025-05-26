<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Local;

class LocalRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Local::class) || $this->user()->can('update', Local::class);
    }

    public function rules()
    {
        $localId = $this->route('local')?->loc_id;

        return [
            'loc_cep'            => ['required', 'string', 'size:9'],
            'loc_endereco'       => ['required', 'string', 'max:255', "unique:locais,loc_endereco,{$localId},loc_id"],
            'loc_numero'         => ['required', 'string', 'max:20'],
            'loc_bairro'         => ['required', 'string', 'max:100'],
            'loc_cidade'         => ['nullable', 'string', 'max:100'],
            'loc_estado'         => ['nullable', 'string', 'max:2'],
            'loc_pais'           => ['nullable', 'string', 'max:100'],
            'loc_latitude'       => ['required', 'string', 'max:20'],
            'loc_longitude'      => ['required', 'string', 'max:20'],
            'loc_codigo_unico' => ['nullable', 'string', 'max:255', "unique:locais,loc_codigo_unico,{$localId},loc_id"],
        ];
    }
}