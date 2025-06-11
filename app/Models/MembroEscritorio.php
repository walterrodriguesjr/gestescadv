<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembroEscritorio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'membro_escritorios';

    protected $fillable = [
        'user_id',
        'escritorio_id',
        'gestor_id',
        'status', 
    ];

    // Relacionamento com o usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relacionamento com o escritório
    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class, 'escritorio_id');
    }

    // Relacionamento com o gestor que cadastrou
    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }
}
