<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Sincroniza a senha do admin (bernardo@bitwise.dev.br) com ADMIN_INITIAL_PASSWORD do .env.
 * Útil quando você altera a senha no .env e precisa atualizar o banco sem rodar migrate:fresh.
 */
class SyncAdminPassword extends Command
{
    protected $signature = 'admin:sync-password';

    protected $description = 'Atualiza a senha do admin gestor com ADMIN_INITIAL_PASSWORD do .env';

    public function handle(): int
    {
        $senha = env('ADMIN_INITIAL_PASSWORD', 'Senha123!');
        $email = 'bernardo@bitwise.dev.br';

        $user = User::where('use_email', $email)->first();

        if (!$user) {
            $this->error("Usuário admin ({$email}) não encontrado.");
            return 1;
        }

        $user->use_senha = Hash::make($senha);
        $user->save();

        $this->info('Senha do admin atualizada com sucesso.');
        return 0;
    }
}
