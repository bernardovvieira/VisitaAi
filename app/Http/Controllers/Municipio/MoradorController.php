<?php

namespace App\Http\Controllers\Municipio;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Municipio\MoradorRequest;
use App\Models\Local;
use App\Models\Morador;
use App\Models\MoradorDocumento;
use App\Models\User;
use App\Support\SmartSearch;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MoradorController extends Controller
{
    private function routeProfile(): string
    {
        /** @var User|null $u */
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
        if ($u->isAgenteSaude()) {
            return 'saude';
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
        $moradoresQuery = $local->moradores()->with('documentosPessoais')->orderBy('mor_id');

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
        unset($data['mor_documentos_pessoal'], $data['remover_documentos_pessoal']);

        $data['fk_local_id'] = $local->loc_id;
        $morador = Morador::create($data);

        try {
            $this->anexarNovosDocumentosPessoais($morador, $this->normalizeUploadedFileList($request->file('mor_documentos_pessoal')));
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages([
                'mor_documentos_pessoal' => [$e->getMessage()],
            ]);
        }

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

        $morador->load('documentosPessoais');
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
        unset($data['mor_documentos_pessoal'], $data['remover_documentos_pessoal']);

        $morador->update($data);

        $this->removerDocumentosPessoaisMarcados($morador, $request->input('remover_documentos_pessoal', []));
        try {
            $this->anexarNovosDocumentosPessoais($morador, $this->normalizeUploadedFileList($request->file('mor_documentos_pessoal')));
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages([
                'mor_documentos_pessoal' => [$e->getMessage()],
            ]);
        }

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

        $this->apagarTodosDocumentosPessoaisDoMorador($morador);

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

    public function downloadDocumentoPessoal(Local $local, Morador $morador, MoradorDocumento $documento)
    {
        $this->authorize('view', $local);
        $this->authorize('view', $morador);

        if ((int) $morador->fk_local_id !== (int) $local->loc_id) {
            abort(404);
        }
        if ((int) $documento->fk_morador_id !== (int) $morador->mor_id) {
            abort(404);
        }

        $path = str_replace('\\', '/', ltrim((string) ($documento->path ?? ''), '/'));
        if ($path === '' || str_contains($path, '..') || ! str_starts_with($path, 'moradores/documentos/')) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $downloadName = (string) ($documento->original_name ?: ('documento-pessoal-ocupante-'.$morador->mor_id));
        $downloadName = trim((string) preg_replace('/[\r\n]+/', '', $downloadName));
        if ($downloadName === '') {
            $downloadName = 'documento-pessoal-ocupante-'.$morador->mor_id;
        }

        return response()->download(Storage::disk('local')->path($path), $downloadName);
    }

    /**
     * @param  list<UploadedFile>  $files
     */
    private function anexarNovosDocumentosPessoais(Morador $morador, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('moradores/documentos', 'local');
            if ($path === false || $path === '') {
                throw new \RuntimeException(__('Não foi possível gravar o documento pessoal. Verifique permissões de armazenamento.'));
            }
            $morador->documentosPessoais()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType() ?: $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }
    }

    /**
     * @param  list<int|string>  $ids
     */
    private function removerDocumentosPessoaisMarcados(Morador $morador, mixed $ids): void
    {
        if (! is_array($ids)) {
            return;
        }
        foreach ($ids as $raw) {
            $id = is_numeric($raw) ? (int) $raw : 0;
            if ($id <= 0) {
                continue;
            }
            $doc = MoradorDocumento::query()
                ->where('fk_morador_id', $morador->mor_id)
                ->whereKey($id)
                ->first();
            if (! $doc) {
                continue;
            }
            $path = (string) ($doc->path ?? '');
            if ($path !== '' && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
            $doc->delete();
        }
    }

    private function apagarTodosDocumentosPessoaisDoMorador(Morador $morador): void
    {
        foreach ($morador->documentosPessoais()->get() as $doc) {
            $path = (string) ($doc->path ?? '');
            if ($path !== '' && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }
        $morador->documentosPessoais()->delete();
    }

    /**
     * @return list<UploadedFile>
     */
    private function normalizeUploadedFileList(mixed $input): array
    {
        if ($input instanceof UploadedFile) {
            return $input->isValid() ? [$input] : [];
        }
        if (! is_array($input)) {
            return [];
        }
        $out = [];
        foreach ($input as $f) {
            if ($f instanceof UploadedFile && $f->isValid()) {
                $out[] = $f;
            }
        }

        return $out;
    }

    private function normalizeSearchTerm(string $value): string
    {
        return (string) Str::of($value)->ascii()->lower()->trim()->replaceMatches('/\s+/', ' ');
    }

    /**
     * @param  array<string, string>  $opcoes
     * @return list<string>
     */
    private function configOptionMatches(string $needle, array $opcoes): array
    {
        $matches = [];
        foreach ($opcoes as $key => $label) {
            $labelNorm = $this->normalizeSearchTerm((string) $label);
            if ($labelNorm !== '' && str_contains($labelNorm, $needle)) {
                $matches[] = (string) $key;
            }
        }

        return $matches;
    }
}
