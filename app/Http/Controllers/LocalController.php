<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Http\Requests\LocalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Local::create($request->validated());

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

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local atualizado com sucesso.');
    }

    public function destroy(Local $local)
    {
        $local->delete();

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local excluído com sucesso.');
    }
}