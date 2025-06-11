<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $table = 'tipos_documento';

    protected $fillable = [
        'escritorio_id',
        'user_id',
        'titulo'
    ];

    // Relacionamentos (opcional)
    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
