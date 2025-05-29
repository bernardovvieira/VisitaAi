<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class UserController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if (request()->filled('search')) {
            $search = trim(request('search'));
            $busca = strtolower($search);

            // Resolver tipo de perfil mesmo por fragmento parcial
            $perfil = null;

            if (str_contains($busca, 'gestor') || str_contains($busca, 'ges')) {
                $perfil = 'gestor';
            } elseif (str_contains($busca, 'endemias') || str_contains($busca, 'en') || str_contains($busca, 'agente')) {
                $perfil = 'agente_endemias';
            } elseif (str_contains($busca, 'saude') || str_contains($busca, 'saúde') || str_contains($busca, 'sa') || str_contains($busca, 'agente')) {
                $perfil = 'agente_saude';
            }

            $query->where(function ($q) use ($search, $perfil) {
                $q->where('use_nome', 'like', "%{$search}%")
                ->orWhere('use_email', 'like', "%{$search}%");

                if ($perfil) {
                    $q->orWhere('use_perfil', $perfil);
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

        User::create($dados);

        LogHelper::registrar(
            'Cadastro de usuário',
            'Usuário',
            'create',
            'Usuário criado: ' . $dados['use_nome'] . ' (' . $dados['use_email'] . ')'
        );

        return redirect()->route('gestor.users.index')->with('status', 'Usuário cadastrado com sucesso.');
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
    
        if (!empty($dados['use_senha'])) {
            $dados['use_senha'] = Hash::make($dados['use_senha']);
        } else {
            unset($dados['use_senha']);
        }
    
        $user->update($dados);

        LogHelper::registrar(
            'Atualização de usuário',
            'Usuário',
            'update',
            'Usuário atualizado: ' . $user->use_nome . ' (ID: ' . $user->use_id . ')'
        );
    
        return redirect()->route('gestor.users.index')->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Bloquear o usuário de se auto anonimizar
        if ($user->use_id == Auth::id()) {
            return redirect()->route('gestor.users.index')->with('error', 'Você não pode anonimizar a si mesmo.');
        }       

        // Anonimizar
        $user->update([
            'use_nome'       => 'Anonimizado (ref. ' . $user->use_id . ')',
            'use_email'      => 'Anonimizado (ref. ' . $user->use_id . ')',
            'use_cpf'        => 'Anonimizado (ref. ' . $user->use_id . ')',
            'use_senha'      => Hash::make('senha_anonima_' . $user->use_id),
            'use_aprovado'   => false,
            'use_status'     => 'inativo',
            'fk_gestor_id'   => null, 
            'use_data_anonimizacao' => now(),
        ]);

        LogHelper::registrar(
            'Anonimização de usuário',
            'Usuário',
            'delete',
            'Usuário anonimizado: ID ' . $user->use_id
        );

        return redirect()->route('gestor.users.index')->with('status', 'Usuário anonimizado com sucesso.');
    }
}
