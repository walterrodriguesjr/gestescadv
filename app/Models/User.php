<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'two_factor_enabled',
        'two_factor_type',
        'two_factor_code',
        'two_factor_expires_at',
        'nivel_acesso_id', // 🔥 Adicionado campo para nível de acesso
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code', // Esconde o código de 2FA
    ];

    protected $casts = [
        'password' => 'hashed',
        'two_factor_expires_at' => 'datetime',
    ];

    /**
     * Relacionamento: Usuário tem um conjunto de dados adicionais
     */
    public function userData()
    {
        return $this->hasOne(UserData::class);
    }
    

    /**
     * Relacionamento: Usuário pode ser dono de um escritório
     */
    public function escritorio()
    {
        return $this->hasOne(Escritorio::class, 'user_id');
    }

    /**
     * Relacionamento: Usuário pode ter múltiplas permissões associadas a diferentes escritórios
     */
    public function permissoes()
    {
        return $this->hasMany(PermissaoUsuario::class, 'usuario_id');
    }

    /**
     * 🔥 Relacionamento: Usuário pertence a um nível de acesso
     */
    public function nivelAcesso()
    {
        return $this->hasOneThrough(NivelAcesso::class, PermissaoUsuario::class, 'usuario_id', 'id', 'id', 'nivel_acesso_id');
    }

    public function membros()
    {
        return $this->hasMany(MembroEscritorio::class, 'user_id');
    }


    /**
     * 🔥 Obtém todas as permissões do usuário baseado no nível de acesso
     */
    public function getPermissions()
    {
        return $this->nivelAcesso ? json_decode($this->nivelAcesso->permissions, true) : [];
    }

    /**
     * 🔥 Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission($permission)
    {
        $permissoes = json_decode($this->nivelAcesso->permissoes ?? '{}', true);
        return !empty($permissoes[$permission]);
    }

    /**
     * Obtém todas as sessões ativas do usuário
     */
    public function activeSessions()
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    /**
     * Envia notificação de redefinição de senha
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Gera um código de autenticação de dois fatores (2FA)
     */
    public function generateTwoFactorCode()
    {
        $this->forceFill([
            'two_factor_code' => random_int(100000, 999999), // Código de 6 dígitos
            'two_factor_expires_at' => now()->addMinutes(10), // Expiração de 10 minutos
        ])->save();
    }

    /**
     * Envia o código de autenticação de dois fatores (2FA) via e-mail ou SMS
     */
    public function sendTwoFactorCode()
    {
        if ($this->two_factor_type === 'email') {
            Mail::to($this->email)->send(new \App\Mail\TwoFactorCodeMail($this));
        } elseif ($this->two_factor_type === 'sms') {
            // Integração com serviço de SMS, como Twilio
        }
    }
}
