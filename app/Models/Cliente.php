<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    /**
     * Nome da tabela no banco de dados (opcional se o nome da tabela seguir o padrão pluralizado).
     */
    protected $table = 'clientes';

    /**
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'cliente_nome_completo',
        'cliente_cpf',
        'cliente_email',
        'cliente_celular',
        'cliente_telefone',
        'cliente_cep',
        'cliente_rua',
        'cliente_numero',
        'cliente_bairro',
        'cliente_estado',
        'cliente_cidade',
    ];

    /**
     * Os atributos que devem ser ocultados nas respostas JSON (se necessário).
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     */
    protected $casts = [
        'cliente_cpf' => 'string',
        'cliente_email' => 'string',
        'cliente_celular' => 'string',
        'cliente_telefone' => 'string',
        'cliente_cep' => 'string',
        'cliente_rua' => 'string',
        'cliente_numero' => 'string',
        'cliente_bairro' => 'string',
        'cliente_estado' => 'string',
        'cliente_cidade' => 'string',
    ];

    //relacionamento que vincula time ao Cliente
    public function teams()
{
    return $this->belongsToMany(Team::class, 'cliente_team')->withTimestamps();
}
}
