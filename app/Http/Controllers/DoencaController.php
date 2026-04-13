<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\DoencaRequest;
use App\Models\Doenca;
use App\Models\User;
use App\Support\SmartSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoencaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $search = trim((string) $request->input('search'));
        $terms = SmartSearch::terms($search);

        $query = Doenca::query();
        if ($search !== '') {
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhereRaw('LOWER(COALESCE(doe_nome, "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('doe_nome').' LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(CAST(doe_sintomas AS CHAR), "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('CAST(doe_sintomas AS CHAR)').' LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(CAST(doe_transmissao AS CHAR), "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('CAST(doe_transmissao AS CHAR)').' LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(CAST(doe_medidas_controle AS CHAR), "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('CAST(doe_medidas_controle AS CHAR)').' LIKE ?', [$like])
                        ->orWhereRaw('CAST(doe_id AS CHAR) LIKE ?', [$like]);
                }
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
        /** @var User $user */
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
        // Lista completa de opções para vigilância entomológica e doenças
        $optionsSintomas = [
            'Febre alta', 'Febre baixa', 'Dor de cabeça', 'Dor atrás dos olhos',
            'Dor muscular', 'Dor nas articulações', 'Manchas vermelhas na pele',
            'Conjuntivite', 'Náusea', 'Vômito', 'Coceira', 'Fadiga', 'Cefaleia',
        ];

        $optionsTransmissao = [
            'Picada do mosquito Aedes aegypti',
            'Transmissão sexual',
            'Transmissão vertical (gestante para o feto)',
            'Contato direto',
            'Hemocontato (sangue)',
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
            'Campanhas de conscientização',
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
            'Doença cadastrada: '.$data['doe_nome']
        );

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', __('Doença cadastrada com sucesso.'));
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
            'Dor abdominal', 'Conjuntivite',
        ];
        $optionsTransmissao = [
            'Contato direto', 'Gotículas respiratórias', 'Aerossois (transmissão aérea)',
            'Água contaminada', 'Alimentos contaminados', 'Fômites',
            'Vetor biológico', 'Vetor mecânico', 'Transmissão vertical',
            'Hemocontato (sangue)', 'Transmissão sexual',
        ];
        $optionsMedidas = [
            'Higienização das mãos', 'Máscara cirúrgica', 'Máscara N95/FFP2',
            'Protetor ocular', 'Luvas descartáveis', 'Avental',
            'Isolamento de casos', 'Quarentena de contatos', 'Etiqueta respiratória',
            'Distanciamento físico', 'Ventilação adequada',
            'Limpeza e desinfecção', 'Controle de vetores',
            'Vacinação', 'WASH (água, saneamento e higiene)',
            'Educação em saúde', 'Vigilância entomológica',
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
            'Doença atualizada: '.$doenca->doe_nome
        );

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', __('Doença atualizada com sucesso.'));
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
            'Doença excluída: '.$nome
        );

        return redirect()
            ->route('gestor.doencas.index')
            ->with('success', __('Doença excluída com sucesso.'));
    }
}
