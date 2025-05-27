<?php

namespace App\Http\Controllers;

use App\Models\Doenca;
use Illuminate\Http\Request;
use App\Http\Requests\DoencaRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Helpers\LogHelper;

class DoencaController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

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
        $doencas = $query->paginate(10)->appends(['search' => $search]);

        if ($user && $user->isAgenteEndemias()) {
            return view('agente.doencas.index', compact('doencas', 'search'));
        } elseif ($user && $user->isAgenteSaude()) {
            return view('saude.doencas.index', compact('doencas', 'search'));
        } elseif ($user && $user->isGestor()) {
            return view('gestor.doencas.index', compact('doencas', 'search'));
        } else {
            abort(403, 'Acesso não autorizado.');
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Doenca $doenca)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->isAgenteEndemias()) {
            return view('agente.doencas.show', compact('doenca'));
        } elseif ($user && $user->isAgenteSaude()) {
            return view('saude.doencas.show', compact('doenca'));
        } elseif ($user && $user->isGestor()) {
            return view('gestor.doencas.show', compact('doenca'));
        } else {
            abort(403, 'Acesso não autorizado.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lista completa de opções para vigilância epidemiológica
        $optionsSintomas = [
            'Febre alta', 'Febre baixa', 'Dor de cabeça', 'Dor atrás dos olhos',
            'Dor muscular', 'Dor nas articulações', 'Manchas vermelhas na pele',
            'Conjuntivite', 'Náusea', 'Vômito', 'Coceira', 'Fadiga', 'Cefaleia'
        ];

        $optionsTransmissao = [
            'Picada do mosquito Aedes aegypti',
            'Transmissão sexual',
            'Transmissão vertical (gestante para o feto)',
            'Contato direto',
            'Hemocontato (sangue)'
        ];

        $optionsMedidas = [
            'Eliminação de criadouros',
            'Uso de repelentes',
            'Controle de vetores',
            'Educação em saúde',
            'Telagem de recipientes',
            'Proteção da gestante',
            'Acompanhamento médico',
            'Vacinação',
            'Campanhas de conscientização'
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
    public function store(DoencaRequest $request)
    {
        // Só os campos validados serão retornados
        $data = $request->validated();
    
        Doenca::create($data);

        LogHelper::registrar(
            'Cadastro de doença',
            'Doença',
            'create',
            'Doença cadastrada: ' . $data['doe_nome']
        );
    
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
    public function update(DoencaRequest $request, Doenca $doenca)
    {
        $doenca->update($request->validated());

        LogHelper::registrar(
            'Atualização de doença',
            'Doença',
            'update',
            'Doença atualizada: ' . $doenca->doe_nome
        );
    
        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', 'Doença atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doenca $doenca)
    {
        $nome = $doenca->doe_nome;

        $doenca->delete();

        LogHelper::registrar(
            'Exclusão de doença',
            'Doença',
            'delete',
            'Doença excluída: ' . $nome
        );

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', 'Doença excluída com sucesso.');
    }

}