<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissaoUsuario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permissoes_usuarios';

    protected $fillable = [
        'usuario_id',
        'nivel_acesso_id',
        'escritorio_id',
        'concedente_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function nivelAcesso()
    {
        return $this->belongsTo(NivelAcesso::class, 'nivel_acesso_id');
    }

    public function concedente()
    {
        return $this->belongsTo(User::class, 'concedente_id');
    }

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class, 'escritorio_id');
    }
}
