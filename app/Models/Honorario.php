<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Honorario extends Model
{
    use HasFactory;

    protected $fillable = [
        'escritorio_id',
        'servico_id',
        'cliente_id',
        'valor',
        'observacoes',
        'data_recebimento'
    ];

    protected $casts = [
        'data_recebimento' => 'date',
    ];

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }

    public function clientePessoaFisica()
    {
        return $this->belongsTo(ClientePessoaFisica::class, 'cliente_id');
    }

    public function clientePessoaJuridica()
    {
        return $this->belongsTo(ClientePessoaJuridica::class, 'cliente_id');
    }
}
