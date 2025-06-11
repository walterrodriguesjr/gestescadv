<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'escritorio_id',
        'tipo_documento_id',
        'titulo',
        'texto'
    ];
}
