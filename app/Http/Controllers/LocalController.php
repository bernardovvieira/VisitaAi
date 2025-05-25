<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Http\Requests\LocalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class LocalController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $search = $request->input('search');

        $query = Local::query();
        if ($search) {
            $query->whereRaw("loc_endereco LIKE ? COLLATE utf8mb4_unicode_ci", ["%{$search}%"]);
        }

        $locais = $query->paginate(10)->appends(['search' => $search]);

        $view = $user->isAgente()
            ? 'agente.locais.index'
            : 'gestor.locais.index';

        return view($view, compact('locais', 'search'));
    }

    public function show(Local $local)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $view = $user->isAgente()
            ? 'agente.locais.show'
            : 'gestor.locais.show';

        return view($view, compact('local'));
    }

    public function create()
    {
        return view('agente.locais.create');
    }

    public function store(LocalRequest $request)
    {
        $local = Local::create($request->validated());

        LogHelper::registrar(
            'Cadastro de local',
            'Local',
            'create',
            'Local cadastrado: ' . $local->loc_endereco . ', ' . $local->loc_numero
        );

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local cadastrado com sucesso.');
    }

    public function edit(Local $local)
    {
        return view('agente.locais.edit', compact('local'));
    }

    public function update(LocalRequest $request, Local $local)
    {
        $local->update($request->validated());

        LogHelper::registrar(
            'Atualização de local',
            'Local',
            'update',
            'Local atualizado: ' . $local->loc_endereco . ', nº ' . $local->loc_numero
        );

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local atualizado com sucesso.');
    }

    public function destroy(Local $local)
    {
        $descricao = $local->loc_endereco . ', ' . $local->loc_numero;
        
        $local->delete();

        LogHelper::registrar(
            'Exclusão de local',
            'Local',
            'delete',
            'Local excluído: ' . $descricao
        );

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local excluído com sucesso.');
    }
}