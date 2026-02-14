<?php

namespace App\Models;

use App\Database\Eloquent\UserBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, MustVerifyEmailTrait, Notifiable, TwoFactorAuthenticatable;

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
        'email_verified_at',
        'use_perfil',
        'use_aprovado',
        'use_tema',
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
        'email_verified_at'     => 'datetime',
        'use_aprovado'          => 'boolean',
        'use_data_criacao'      => 'datetime',
        'use_data_anonimizacao' => 'date',
    ];

    /**
     * Acesso ao id para compatibilidade com rotas/verificação (PK real: use_id).
     */
    public function getIdAttribute()
    {
        return $this->attributes['use_id'] ?? null;
    }

    /**
     * Alias para use_email (compatibilidade; Fortify usa use_email como username).
     */
    public function getLoginAttribute(): string
    {
        return $this->use_email ?? '';
    }

    /**
     * Acesso ao e-mail para compatibilidade com testes e notificações (campo real: use_email).
     */
    public function getEmailAttribute(): string
    {
        return $this->use_email;
    }

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
        return in_array($this->use_perfil, ['agente_endemias', 'agente_saude']);
    }

    public function isAgenteEndemias(): bool
    {
        return $this->use_perfil === 'agente_endemias';
    }

    public function isAgenteSaude(): bool
    {
        return $this->use_perfil === 'agente_saude';
    }

    public function isGestor(): bool
    {
        return $this->use_perfil === 'gestor';
    }

    public function isAprovado(): bool
    {
        return (bool) $this->use_aprovado;
    }

    /**
     * Builder que traduz where('login', ...) para where('use_email', ...).
     * Evita "Unknown column 'login'" no demo quando Fortify/guard usam a chave 'login'.
     */
    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            abort(403, 'Operação proibida: usuários devem ser anonimizados, não excluídos.');
        });
    }
}
