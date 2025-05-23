<?php

namespace App\Http\Controllers;

use App\Models\Local;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loc_endereco'       => 'required|string|max:255|unique:locais,loc_endereco',
            'loc_bairro'         => 'required|string|max:255',
            'latitude'           => 'required|string',
            'longitude'          => 'required|string',
            'loc_codigo_unico'   => 'required|string|max:255|unique:locais,loc_codigo_unico',
        ]);

        $validated['loc_coordenadas'] = "{$validated['latitude']},{$validated['longitude']}";
        unset($validated['latitude'], $validated['longitude']);

        Local::create($validated);

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local cadastrado com sucesso.');
    }

    public function edit(Local $local)
    {
        // Divide latitude/longitude antes de enviar para view
        [$lat, $lng] = explode(',', $local->loc_coordenadas);
        $local->latitude = $lat;
        $local->longitude = $lng;

        return view('agente.locais.edit', compact('local'));
    }

    public function update(Request $request, Local $local)
    {
        $validated = $request->validate([
            'loc_endereco'       => 'required|string|max:255|unique:locais,loc_endereco,' . $local->loc_id . ',loc_id',
            'loc_bairro'         => 'required|string|max:255',
            'latitude'           => 'required|string',
            'longitude'          => 'required|string',
            'loc_codigo_unico'   => 'required|string|max:255|unique:locais,loc_codigo_unico,' . $local->loc_id . ',loc_id',
        ]);

        $validated['loc_coordenadas'] = "{$validated['latitude']},{$validated['longitude']}";
        unset($validated['latitude'], $validated['longitude']);

        $local->update($validated);

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
