<?php

namespace App\Http\Controllers\Municipio;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Municipio\MoradorRequest;
use App\Models\Local;
use App\Models\Morador;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MoradorController extends Controller
{
    private function routeProfile(): string
    {
        /** @var \App\Models\User|null $u */
        $u = Auth::user();
        if (! $u) {
            abort(403);
        }
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

        $search = trim((string) $request->query('q', ''));
        $moradoresQuery = $local->moradores()->orderBy('mor_id');

        if ($search !== '') {
            $like = '%'.$search.'%';
            $moradoresQuery->where(function ($q) use ($like) {
                $q->where('mor_nome', 'like', $like)
                    ->orWhere('mor_profissao', 'like', $like)
                    ->orWhere('mor_naturalidade', 'like', $like)
                    ->orWhere('mor_escolaridade', 'like', $like)
                    ->orWhere('mor_renda_faixa', 'like', $like)
                    ->orWhere('mor_cor_raca', 'like', $like)
                    ->orWhere('mor_situacao_trabalho', 'like', $like)
                    ->orWhere('mor_observacao', 'like', $like);
            });
        }

        $moradores = $moradoresQuery->paginate(15)->withQueryString();
        $profile = $this->routeProfile();

        return view('municipio.moradores.index', compact('local', 'moradores', 'profile', 'search'));
    }

    public function create(Local $local)
    {
        $this->authorize('view', $local);
        $this->authorize('create', Morador::class);

        $profile = $this->routeProfile();
        $morador = new Morador(['fk_local_id' => $local->loc_id]);

        return view('municipio.moradores.create', compact('local', 'morador', 'profile'));
    }

    public function store(MoradorRequest $request, Local $local)
    {
        $this->authorize('view', $local);
        $this->authorize('create', Morador::class);

        $data = $request->validated();
        unset($data['mor_documento_pessoal'], $data['remover_documento_pessoal']);

        if ($request->hasFile('mor_documento_pessoal')) {
            $data = array_merge($data, $this->uploadDocumentoPessoal($request->file('mor_documento_pessoal')));
        }

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
            ->with('success', __('Ocupante cadastrado com sucesso.'));
    }

    public function edit(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('update', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $profile = $this->routeProfile();

        return view('municipio.moradores.edit', compact('local', 'morador', 'profile'));
    }

    public function update(MoradorRequest $request, Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('update', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $data = $request->validated();
        $removeDocumento = (bool) ($data['remover_documento_pessoal'] ?? false);
        unset($data['mor_documento_pessoal'], $data['remover_documento_pessoal']);

        if ($removeDocumento) {
            $this->deleteDocumentoPessoal($morador);
            $data['mor_documento_pessoal_path'] = null;
            $data['mor_documento_pessoal_nome'] = null;
            $data['mor_documento_pessoal_mime'] = null;
            $data['mor_documento_pessoal_tamanho'] = null;
        }

        if ($request->hasFile('mor_documento_pessoal')) {
            $this->deleteDocumentoPessoal($morador);
            $data = array_merge($data, $this->uploadDocumentoPessoal($request->file('mor_documento_pessoal')));
        }

        $morador->update($data);

        LogHelper::registrar(
            'Atualização de ocupante (Visita Aí)',
            'Morador',
            'update',
            'Ocupante #'.$morador->mor_id.' no local '.$local->loc_codigo_unico
        );

        $profile = $this->routeProfile();

        return redirect()
            ->route($profile.'.locais.moradores.index', $local)
            ->with('success', __('Ocupante atualizado com sucesso.'));
    }

    public function destroy(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('delete', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $this->deleteDocumentoPessoal($morador);

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
            ->with('success', __('Ocupante excluído com sucesso.'));
    }

    public function fichaSocioeconomicaPdf(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('view', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $local->loadMissing(['socioeconomico']);

        $pdf = Pdf::loadView('pdf.ficha_socioeconomica', [
            'local' => $local,
            'socio' => $local->socioeconomico,
            'moradores' => collect([$morador]),
            'moradorSelecionado' => $morador,
            'titulos' => config('visitaai_socioeconomico.secao_titulos', []),
        ])->setPaper('a4', 'portrait');

        $safeCode = preg_replace('/\D/', '', (string) $local->loc_codigo_unico) ?: 'imovel';
        $safeMorador = preg_replace('/[^a-z0-9]+/i', '-', (string) ($morador->mor_nome ?? 'morador'));
        $safeMorador = trim($safeMorador ?? '', '-');
        $safeMorador = $safeMorador !== '' ? $safeMorador : 'morador';

        return $pdf->download('ficha-socioeconomica-'.$safeCode.'-'.$safeMorador.'.pdf');
    }

    public function downloadDocumentoPessoal(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('view', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $path = (string) ($morador->mor_documento_pessoal_path ?? '');
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $downloadName = $morador->mor_documento_pessoal_nome ?: ('documento-pessoal-ocupante-'.$morador->mor_id);

        return response()->download(Storage::disk('local')->path($path), $downloadName);
    }

    private function uploadDocumentoPessoal(UploadedFile $file): array
    {
        $path = $file->store('moradores/documentos', 'local');

        return [
            'mor_documento_pessoal_path' => $path,
            'mor_documento_pessoal_nome' => $file->getClientOriginalName(),
            'mor_documento_pessoal_mime' => $file->getClientMimeType(),
            'mor_documento_pessoal_tamanho' => $file->getSize(),
        ];
    }

    private function deleteDocumentoPessoal(Morador $morador): void
    {
        $path = (string) ($morador->mor_documento_pessoal_path ?? '');
        if ($path !== '' && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
