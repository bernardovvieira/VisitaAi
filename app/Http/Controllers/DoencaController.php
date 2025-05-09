<?php

namespace App\Http\Controllers;

use App\Models\Doenca;
use Illuminate\Http\Request;

class DoencaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $query = Doenca::query();
    
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereRaw("doe_nome LIKE ? COLLATE utf8mb4_unicode_ci", ["%{$search}%"])
                  ->orWhereRaw("doe_sintomas->> '$' LIKE ? COLLATE utf8mb4_unicode_ci", ["%{$search}%"])
                  ->orWhereRaw("doe_transmissao->> '$' LIKE ? COLLATE utf8mb4_unicode_ci", ["%{$search}%"])
                  ->orWhereRaw("doe_medidas_controle->> '$' LIKE ? COLLATE utf8mb4_unicode_ci", ["%{$search}%"]);
            });
        }
    
        $doencas = $query
            ->paginate(10)
            ->appends(['search' => $search]);
    
        return view('gestor.doencas.index', compact('doencas', 'search'));
    }    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lista completa de opções para vigilância epidemiológica
        $optionsSintomas = [
            'Febre', 'Calafrios', 'Tosse', 'Tosse seca', 'Tosse produtiva',
            'Fadiga', 'Dor de cabeça', 'Mialgias', 'Artralgias',
            'Dispneia', 'Odinofagia', 'Congestão nasal', 'Coriza',
            'Anosmia', 'Ageusia', 'Náusea', 'Vômito', 'Diarreia',
            'Sudorese noturna', 'Rigidez de nuca', 'Rash cutâneo',
            'Dor abdominal', 'Conjuntivite'
        ];
        $optionsTransmissao = [
            'Contato direto', 'Gotículas respiratórias', 'Aerossois (transmissão aérea)',
            'Água contaminada', 'Alimentos contaminados', 'Fômites',
            'Vetor biológico', 'Vetor mecânico', 'Transmissão vertical',
            'Hemocontato (sangue)', 'Transmissão sexual'
        ];
        $optionsMedidas = [
            'Higienização das mãos', 'Máscara cirúrgica', 'Máscara N95/FFP2',
            'Protetor ocular', 'Luvas descartáveis', 'Avental',
            'Isolamento de casos', 'Quarentena de contatos', 'Etiqueta respiratória',
            'Distanciamento físico', 'Ventilação adequada',
            'Limpeza e desinfecção', 'Controle de vetores',
            'Vacinação', 'WASH (água, saneamento e higiene)',
            'Educação em saúde', 'Vigilância epidemiológica'
        ];

        return view('gestor.doencas.create', compact(
            'optionsSintomas',
            'optionsTransmissao',
            'optionsMedidas'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'doe_nome'               => 'required|string|max:255',
            'doe_sintomas'           => 'required|array|min:1',
            'doe_sintomas.*'         => 'string',
            'doe_transmissao'        => 'required|array|min:1',
            'doe_transmissao.*'      => 'string',
            'doe_medidas_controle'   => 'required|array|min:1',
            'doe_medidas_controle.*' => 'string',
        ]);

        Doenca::create($data);

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', 'Doença cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doenca $doenca)
    {
        // Mesmas opções de create
        $optionsSintomas = [
            'Febre', 'Calafrios', 'Tosse', 'Tosse seca', 'Tosse produtiva',
            'Fadiga', 'Dor de cabeça', 'Mialgias', 'Artralgias',
            'Dispneia', 'Odinofagia', 'Congestão nasal', 'Coriza',
            'Anosmia', 'Ageusia', 'Náusea', 'Vômito', 'Diarreia',
            'Sudorese noturna', 'Rigidez de nuca', 'Rash cutâneo',
            'Dor abdominal', 'Conjuntivite'
        ];
        $optionsTransmissao = [
            'Contato direto', 'Gotículas respiratórias', 'Aerossois (transmissão aérea)',
            'Água contaminada', 'Alimentos contaminados', 'Fômites',
            'Vetor biológico', 'Vetor mecânico', 'Transmissão vertical',
            'Hemocontato (sangue)', 'Transmissão sexual'
        ];
        $optionsMedidas = [
            'Higienização das mãos', 'Máscara cirúrgica', 'Máscara N95/FFP2',
            'Protetor ocular', 'Luvas descartáveis', 'Avental',
            'Isolamento de casos', 'Quarentena de contatos', 'Etiqueta respiratória',
            'Distanciamento físico', 'Ventilação adequada',
            'Limpeza e desinfecção', 'Controle de vetores',
            'Vacinação', 'WASH (água, saneamento e higiene)',
            'Educação em saúde', 'Vigilância epidemiológica'
        ];

        return view('gestor.doencas.edit', compact(
            'doenca',
            'optionsSintomas',
            'optionsTransmissao',
            'optionsMedidas'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doenca $doenca)
    {
        $data = $request->validate([
            'doe_nome'               => 'required|string|max:255',
            'doe_sintomas'           => 'required|array|min:1',
            'doe_sintomas.*'         => 'string',
            'doe_transmissao'        => 'required|array|min:1',
            'doe_transmissao.*'      => 'string',
            'doe_medidas_controle'   => 'required|array|min:1',
            'doe_medidas_controle.*' => 'string',
        ]);

        $doenca->update($data);

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', 'Doença atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doenca $doenca)
    {
        $doenca->delete();

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', 'Doença excluída com sucesso.');
    }
}