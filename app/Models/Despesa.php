<?php

// app/Models/Despesa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Despesa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'despesas';

    protected $fillable = [
        'escritorio_id',
        'tipo_despesa_id',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'situacao',
    ];

    protected $casts = [
        'situacao' => 'boolean',
        'valor' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function tipoDespesa()
    {
        return $this->belongsTo(TipoDespesa::class);
    }
}
