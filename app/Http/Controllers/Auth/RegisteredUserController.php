<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tela de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Processa o registro de um novo usuário.
     */
    public function store(Request $request): RedirectResponse
    {
        /* -----------------------------------------------------------------
         |  0. Normaliza CPF (só dígitos → formato 000.000.000-00) para
         |     validação e armazenamento consistentes (evita duplicados por máscara)
         |-----------------------------------------------------------------*/
        $cpfDigits = preg_replace('/\D/', '', (string) $request->input('cpf', ''));
        if (strlen($cpfDigits) === 11) {
            $request->merge([
                'cpf' => substr($cpfDigits, 0, 3).'.'.substr($cpfDigits, 3, 3).'.'.substr($cpfDigits, 6, 3).'-'.substr($cpfDigits, 9, 2),
            ]);
        }

        /* -----------------------------------------------------------------
         |  1. Validação
         |-----------------------------------------------------------------*/
        $emailRule = app()->environment('testing')
            ? ['required', 'string', 'email', 'max:255', 'unique:users,use_email']
            : ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,use_email'];

        $validator = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:14', 'unique:users,use_cpf'],
            'email' => $emailRule,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nome.required' => __('O nome é obrigatório'),
            'cpf.required' => __('O CPF é obrigatório'),
            'cpf.unique' => __('Este CPF já está cadastrado'),
            'email.required' => __('O e-mail é obrigatório'),
            'email.email' => __('Formato de e-mail inválido'),
            'email.unique' => __('Este e-mail já está cadastrado'),
            'password.confirmed' => __('A confirmação de senha não confere'),
            'password.required' => __('Por favor, digite uma senha'),
            'password.min' => __('A senha deve ter no mínimo 8 caracteres'),
            'password.letters' => __('A senha deve conter pelo menos uma letra.'),
            'password.mixed' => __('A senha deve conter pelo menos uma letra maiúscula e uma minúscula.'),
            'password.numbers' => __('A senha deve conter pelo menos um número.'),
            'password.symbols' => __('A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).'),
        ]);

        if ($validator->fails()) {
            $failed = $validator->failed();
            if (isset($failed['email']['Unique']) || isset($failed['cpf']['Unique'])) {
                throw ValidationException::withMessages([
                    'register' => [__('Não foi possível concluir o cadastro com estes dados. Se você já tiver conta, faça login ou recupere a senha.')],
                ]);
            }

            throw new ValidationException($validator);
        }

        /* -----------------------------------------------------------------
         |  2. Criação: usuário entra como NÃO aprovado
         |-----------------------------------------------------------------*/
        $data = $validator->validated();

        $user = User::create([
            'use_nome' => $data['nome'],
            'use_cpf' => $data['cpf'],
            'use_email' => $data['email'],
            'use_senha' => Hash::make($data['password']),
            'use_perfil' => 'agente_endemias',   // padrão
            'use_aprovado' => false,      // aguardando aprovação do gestor
            'use_tema' => 'light',    // padrão modo claro ao criar conta
            'use_data_criacao' => now(),
        ]);

        // event(new Registered($user));

        /* -----------------------------------------------------------------
         |  3. Redireciona para login com aviso
         |     (não faz login automático porque ainda não foi aprovado)
         |-----------------------------------------------------------------*/
        return redirect()
            ->route('login')
            ->with('status', __('Cadastro enviado! Aguarde a aprovação de um gestor.'));
    }
}
