<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientePessoaFisica extends Model
{
    use HasFactory;

    protected $table = 'cliente_pessoa_fisicas';

    protected $fillable = [
        'escritorio_id',
        'nome',
        'cpf', // CPF pode ser duplicado, pois é vinculado ao Escritório
        'telefone',
        'celular',
        'email', // E-mail também pode ser duplicado
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }
}
