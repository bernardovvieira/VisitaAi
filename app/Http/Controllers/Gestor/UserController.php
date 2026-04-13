<?php

namespace App\Http\Controllers\Gestor;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Support\SmartSearch;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if (request()->filled('search')) {
            $search = trim(request('search'));
            $busca = mb_strtolower($search);
            $terms = SmartSearch::terms($search);

            // Busca por perfil: gestor, ACE (agente_endemias), ACS (agente_saude), conformidade MS (Lei 11.350/2006).
            $perfis = null;
            $gestorPalavra = 'gestor';
            $endemiasPalavra = 'endemias';
            $saudePalavra = 'saude';

            if (str_contains($busca, 'ace') || (strlen($busca) >= 2 && str_starts_with($busca, 'ace'))) {
                $perfis = ['agente_endemias'];
            } elseif (str_contains($busca, 'acs') || (strlen($busca) >= 2 && str_starts_with($busca, 'acs'))) {
                $perfis = ['agente_saude'];
            } elseif (str_contains($busca, 'agente de')) {
                $resto = trim(substr($busca, strpos($busca, 'agente de') + strlen('agente de')));
                $restoNorm = preg_replace('/ú/u', 'u', $resto);
                if ($resto === '') {
                    $perfis = ['agente_endemias', 'agente_saude'];
                } elseif ($restoNorm !== '' && str_starts_with($saudePalavra, $restoNorm)) {
                    $perfis = ['agente_saude'];
                } elseif ($resto !== '' && str_starts_with($endemiasPalavra, $resto)) {
                    $perfis = ['agente_endemias'];
                } else {
                    $perfis = ['agente_endemias', 'agente_saude'];
                }
            } elseif (str_contains($busca, $gestorPalavra) || (strlen($busca) >= 3 && str_starts_with($gestorPalavra, $busca))) {
                $perfis = ['gestor'];
            } else {
                $buscaNorm = preg_replace('/ú/u', 'u', $busca);
                if ($buscaNorm !== '' && str_starts_with($saudePalavra, $buscaNorm)) {
                    $perfis = ['agente_saude'];
                } elseif ($busca !== '' && str_starts_with($endemiasPalavra, $busca)) {
                    $perfis = ['agente_endemias'];
                } elseif (strlen($busca) >= 3 && str_starts_with($gestorPalavra, $busca)) {
                    $perfis = ['gestor'];
                }
            }

            $query->where(function ($q) use ($terms, $perfis) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhereRaw('LOWER(COALESCE(use_nome, "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('use_nome').' LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(use_email, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(use_cpf, "")) LIKE ?', [$like])
                        ->orWhereRaw('CAST(use_id AS CHAR) LIKE ?', [$like]);
                }

                if ($perfis !== null) {
                    $q->orWhereIn('use_perfil', $perfis);
                }
            });
        }

        $usuarios = $query->paginate(10)->withQueryString();

        return view('gestor.users.index', compact('usuarios'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('gestor.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $dados = $request->validated();
        $dados['use_senha'] = Hash::make($dados['use_senha']);
        $dados['use_data_criacao'] = now();
        $dados['use_tema'] = $dados['use_tema'] ?? 'light';

        User::create($dados);

        LogHelper::registrar(
            'Cadastro de usuário',
            'Usuário',
            'create',
            'Usuário criado: '.$dados['use_nome'].' ('.$dados['use_email'].')'
        );

        return redirect()->route('gestor.users.index')->with('status', __('Usuário cadastrado com sucesso.'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('gestor.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $dados = $request->validated();

        if (! empty($dados['use_senha'])) {
            $dados['use_senha'] = Hash::make($dados['use_senha']);
        } else {
            unset($dados['use_senha']);
        }

        $user->update($dados);

        LogHelper::registrar(
            'Atualização de usuário',
            'Usuário',
            'update',
            'Usuário atualizado: '.$user->use_nome.' (ID: '.$user->use_id.')'
        );

        return redirect()->route('gestor.users.index')->with('status', __('Usuário atualizado com sucesso.'));
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Bloquear o usuário de se auto anonimizar
        if ($user->use_id == Auth::id()) {
            return redirect()->route('gestor.users.index')->with('error', __('Você não pode anonimizar a si mesmo.'));
        }

        // Anonimizar
        $user->update([
            'use_nome' => 'Anonimizado (ref. '.$user->use_id.')',
            'use_email' => 'Anonimizado (ref. '.$user->use_id.')',
            'use_cpf' => 'Anonimizado (ref. '.$user->use_id.')',
            'use_senha' => Hash::make('senha_anonima_'.$user->use_id),
            'use_aprovado' => false,
            'fk_gestor_id' => null,
            'use_data_anonimizacao' => now(),
        ]);

        LogHelper::registrar(
            'Anonimização de usuário',
            'Usuário',
            'delete',
            'Usuário anonimizado: ID '.$user->use_id
        );

        return redirect()->route('gestor.users.index')->with('status', __('Usuário anonimizado com sucesso.'));
    }
}
