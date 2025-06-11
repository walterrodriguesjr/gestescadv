<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'escritorio_id',
        'servico_id',
        'motivo_agenda_id',
        'tipo_cliente',
        'cliente_id',
        'data_hora_inicio',
        'data_hora_fim',
        'observacoes',
        'honorario',
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
        'honorario' => 'decimal:2',
    ];


    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }

    public function motivo()
    {
        return $this->belongsTo(MotivoAgenda::class, 'motivo_agenda_id');
    }

    public function clientePessoaFisica()
    {
        return $this->belongsTo(ClientePessoaFisica::class, 'cliente_id');
    }

    public function clientePessoaJuridica()
    {
        return $this->belongsTo(ClientePessoaJuridica::class, 'cliente_id');
    }

    public function cliente()
    {
        if ($this->tipo_cliente === 'pessoa_fisica') {
            return $this->clientePessoaFisica();
        }

        if ($this->tipo_cliente === 'pessoa_juridica') {
            return $this->clientePessoaJuridica();
        }

        return null;
    }
}
