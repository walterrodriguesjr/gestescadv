<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelAcesso extends Model
{
    use HasFactory;

    protected $table = 'niveis_acesso';

    protected $fillable = [
        'nome',
        'permissoes',
    ];

    protected $casts = [
        'permissoes' => 'array',
    ];

    // Usuários com esse nível de acesso diretamente
    public function usuarios()
    {
        return $this->hasMany(User::class, 'nivel_acesso_id');
    }

    // Usuários que receberam esse nível de acesso via PermissaoUsuario
    public function permissoesUsuarios()
    {
        return $this->hasMany(PermissaoUsuario::class, 'nivel_acesso_id');
    }
}
