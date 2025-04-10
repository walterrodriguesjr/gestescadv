<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AndamentoServico extends Model
{
    use HasFactory;

    protected $table = 'andamento_servico';

    protected $fillable = [
        'servico_id',
        'etapa',
        'descricao',
        'honorario',
        'data_hora',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'honorario' => 'decimal:2',
    ];

    /**
     * Relacionamento com o serviço principal
     */
    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    /**
     * Acessor formatado (opcional) para exibir o valor como moeda
     */
    public function getHonorarioFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->honorario, 2, ',', '.');
    }

    /**
     * Acessor de data e hora formatado
     */
    public function getDataHoraFormatadaAttribute()
    {
        return $this->data_hora->format('d/m/Y H:i');
    }
}
