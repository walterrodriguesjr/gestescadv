<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientePessoaJuridica extends Model
{
    use HasFactory;

    protected $table = 'cliente_pessoa_juridicas';

    protected $fillable = [
        'escritorio_id',
        'razao_social',
        'nome_fantasia',
        'cnpj', // CNPJ pode ser duplicado, pois é vinculado ao Escritório
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
