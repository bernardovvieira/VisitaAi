<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Tabela e PK com prefixo
    protected $table        = 'users';
    protected $primaryKey   = 'use_id';
    public    $incrementing = true;
    public    $timestamps   = false;

    /**
     * Atributos em massa.
     */
    protected $fillable = [
        'use_nome',           
        'use_cpf',
        'use_email',
        'use_senha',
        'use_perfil',
        'use_aprovado',
        'use_data_criacao',
        'use_data_anonimizacao',
        'fk_gestor_id',
    ];

    /**
     * Campos a ocultar em JSON/respostas.
     */
    protected $hidden = [
        'use_senha',           // senha hash
        'remember_token',      // sem prefixo
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Casts de tipo.
     */
    protected $casts = [
        'use_aprovado'          => 'boolean',
        'use_data_criacao'      => 'datetime',
        'use_data_anonimizacao' => 'date',
    ];

    /**
     * Coluna que contém o hash da senha.
     */
    public function getAuthPassword()
    {
        return $this->use_senha;
    }

    /**
     * Para o Password Broker usar `use_email`.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->use_email;
    }

    /**
     * Define o e-mail de destino para todas as notificações.
     */
    public function routeNotificationForMail($notification)
    {
        return $this->use_email;
    }

    /**
     * Envia notificação de verificação de e-mail.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /* -----------------------------------------------------------------
     |  RELACIONAMENTOS
     |-----------------------------------------------------------------*/

    /**
     * Usuário gestor (auto-relacionamento).
     */
    public function gestor()
    {
        return $this->belongsTo(self::class, 'fk_gestor_id', 'use_id');
    }

    /**
     * Agentes vinculados a este gestor.
     */
    public function agentes()
    {
        return $this->hasMany(self::class, 'fk_gestor_id', 'use_id');
    }

    /* -----------------------------------------------------------------
     |  HELPERS
     |-----------------------------------------------------------------*/

    public function isAgente(): bool
    {
        return $this->use_perfil === 'agente';
    }

    public function isGestor(): bool
    {
        return $this->use_perfil === 'gestor';
    }

    public function isAprovado(): bool
    {
        return (bool) $this->use_aprovado;
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            abort(403, 'Operação proibida: usuários devem ser anonimizados, não excluídos.');
        });
    }
}
