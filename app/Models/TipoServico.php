<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoServico extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tipo_servicos';

    protected $fillable = [
        'escritorio_id',
        'nome_servico',
    ];

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }
}
