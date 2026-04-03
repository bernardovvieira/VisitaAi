<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\MoradorRequest;
use App\Models\Local;
use App\Models\Morador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoradorController extends Controller
{
    private function routeProfile(): string
    {
        $u = Auth::user();
        if ($u->isGestor()) {
            return 'gestor';
        }
        if ($u->isAgenteEndemias()) {
            return 'agente';
        }
        abort(403);
    }

    public function index(Request $request, Local $local)
    {
        $this->authorize('view', $local);
        $this->authorize('viewAny', Morador::class);

        $moradores = $local->moradores()->orderBy('mor_id')->paginate(15)->withQueryString();
        $profile = $this->routeProfile();

        return view('moradores.index', compact('local', 'moradores', 'profile'));
    }

    public function create(Local $local)
    {
        $this->authorize('view', $local);
        $this->authorize('create', Morador::class);

        $profile = $this->routeProfile();
        $morador = new Morador(['fk_local_id' => $local->loc_id]);

        return view('moradores.create', compact('local', 'morador', 'profile'));
    }

    public function store(MoradorRequest $request, Local $local)
    {
        $this->authorize('view', $local);
        $this->authorize('create', Morador::class);

        $data = $request->validated();
        $data['fk_local_id'] = $local->loc_id;
        $morador = Morador::create($data);

        LogHelper::registrar(
            'Cadastro de ocupante (Visita Aí)',
            'Morador',
            'create',
            'Ocupante #'.$morador->mor_id.' no local '.$local->loc_codigo_unico
        );

        $profile = $this->routeProfile();

        return redirect()
            ->route($profile.'.locais.moradores.index', $local)
            ->with('success', 'Ocupante cadastrado com sucesso.');
    }

    public function edit(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('update', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $profile = $this->routeProfile();

        return view('moradores.edit', compact('local', 'morador', 'profile'));
    }

    public function update(MoradorRequest $request, Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('update', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $morador->update($request->validated());

        LogHelper::registrar(
            'Atualização de ocupante (Visita Aí)',
            'Morador',
            'update',
            'Ocupante #'.$morador->mor_id.' no local '.$local->loc_codigo_unico
        );

        $profile = $this->routeProfile();

        return redirect()
            ->route($profile.'.locais.moradores.index', $local)
            ->with('success', 'Ocupante atualizado com sucesso.');
    }

    public function destroy(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('delete', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $id = $morador->mor_id;
        $morador->delete();

        LogHelper::registrar(
            'Exclusão de ocupante (Visita Aí)',
            'Morador',
            'delete',
            'Ocupante #'.$id.' no local '.$local->loc_codigo_unico
        );

        $profile = $this->routeProfile();

        return redirect()
            ->route($profile.'.locais.moradores.index', $local)
            ->with('success', 'Ocupante excluído com sucesso.');
    }
}
