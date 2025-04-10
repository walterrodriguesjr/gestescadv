<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'escritorio_id',
        'tipo_servico_id',
        'tipo_cliente',
        'cliente_id',
        'data_inicio',
        'observacoes',
        'anexos'
    ];

    protected $casts = [
        'anexos' => 'array',
        'data_inicio' => 'date'
    ];

    // Escritório dono do serviço
    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    // Tipo de serviço
    public function tipoServico()
    {
        return $this->belongsTo(TipoServico::class);
    }

    // Cliente vinculado ao serviço (Pessoa Física ou Jurídica)
    public function cliente()
    {
        if ($this->tipo_cliente === 'pessoa_fisica') {
            return $this->belongsTo(ClientePessoaFisica::class, 'cliente_id');
        }

        if ($this->tipo_cliente === 'pessoa_juridica') {
            return $this->belongsTo(ClientePessoaJuridica::class, 'cliente_id');
        }

        return null;
    }

    // Relacionamento polimórfico manual — acessar cliente corretamente no código
    public function getClienteFormatadoAttribute()
    {
        if ($this->tipo_cliente === 'pessoa_fisica') {
            return ClientePessoaFisica::find($this->cliente_id);
        }

        if ($this->tipo_cliente === 'pessoa_juridica') {
            return ClientePessoaJuridica::find($this->cliente_id);
        }

        return null;
    }

    public function andamentos()
    {
        return $this->hasMany(AndamentoServico::class, 'servico_id')->orderBy('data_hora');
    }
}
