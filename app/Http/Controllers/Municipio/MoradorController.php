<?php

namespace App\Http\Controllers\Municipio;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Municipio\MoradorRequest;
use App\Models\Local;
use App\Models\Morador;
use App\Support\SmartSearch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $searchNormalized = $this->normalizeSearchTerm($search);
        $terms = SmartSearch::terms($search);
        $moradoresQuery = $local->moradores()->orderBy('mor_id');

        if ($search !== '') {
            $moradoresQuery->where(function ($q) use ($terms, $searchNormalized) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhereRaw('LOWER(COALESCE(mor_nome, "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('mor_nome').' LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_profissao, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_naturalidade, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_parentesco, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_telefone, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_rg_numero, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_rg_orgao, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_cpf, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_observacao, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_renda_formal_informal, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_ajuda_compra_imovel, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_tempo_uniao_conjuge, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(mor_data_nascimento, "")) LIKE ?', [$like])
                        ->orWhereRaw('CAST(mor_id AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(fk_local_id AS CHAR) LIKE ?', [$like]);
                }

                foreach ($this->configOptionMatches($searchNormalized, config('visitaai_socioeconomico.sexo_opcoes', [])) as $value) {
                    $q->orWhere('mor_sexo', $value);
                }
                foreach ($this->configOptionMatches($searchNormalized, config('visitaai_socioeconomico.estado_civil_opcoes', [])) as $value) {
                    $q->orWhere('mor_estado_civil', $value);
                }
                foreach ($this->configOptionMatches($searchNormalized, config('visitaai_municipio.escolaridade_opcoes', [])) as $value) {
                    $q->orWhere('mor_escolaridade', $value);
                }
                foreach ($this->configOptionMatches($searchNormalized, config('visitaai_municipio.renda_faixa_opcoes', [])) as $value) {
                    $q->orWhere('mor_renda_faixa', $value);
                }
                foreach ($this->configOptionMatches($searchNormalized, config('visitaai_municipio.cor_raca_opcoes', [])) as $value) {
                    $q->orWhere('mor_cor_raca', $value);
                }
                foreach ($this->configOptionMatches($searchNormalized, config('visitaai_municipio.situacao_trabalho_opcoes', [])) as $value) {
                    $q->orWhere('mor_situacao_trabalho', $value);
                }
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
            ->route($profile.'.locais.moradores.edit', [$local, $morador])
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
        // individual ficha generation removed — use LocalController::fichaSocioeconomicaPdf instead
    }

    public function downloadDocumentoPessoal(Local $local, Morador $morador)
    {
        $this->authorize('view', $local);
        $this->authorize('view', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }

        $path = str_replace('\\', '/', ltrim((string) ($morador->mor_documento_pessoal_path ?? ''), '/'));
        if ($path === '' || str_contains($path, '..') || ! str_starts_with($path, 'moradores/documentos/')) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $downloadName = (string) ($morador->mor_documento_pessoal_nome ?: ('documento-pessoal-ocupante-'.$morador->mor_id));
        $downloadName = trim((string) preg_replace('/[\r\n]+/', '', $downloadName));
        if ($downloadName === '') {
            $downloadName = 'documento-pessoal-ocupante-'.$morador->mor_id;
        }

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

    private function normalizeSearchTerm(string $value): string
    {
        return (string) Str::of($value)->ascii()->lower()->trim()->replaceMatches('/\s+/', ' ');
    }

    /**
     * @param  array<string, string>  $options
     * @return array<int, string>
     */
    private function configOptionMatches(string $search, array $options): array
    {
        if ($search === '') {
            return [];
        }

        $matches = [];
        foreach ($options as $value => $label) {
            $valueNormalized = $this->normalizeSearchTerm((string) $value);
            $labelNormalized = $this->normalizeSearchTerm((string) $label);

            if (str_contains($valueNormalized, $search) || str_contains($labelNormalized, $search)) {
                $matches[] = (string) $value;
            }
        }

        return array_values(array_unique($matches));
    }
}
