<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaServico extends Model
{
    protected $table = 'etapas_servico';

    protected $fillable = [
        'nome',
        'icone_cor',
    ];
}
