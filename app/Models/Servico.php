<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;

class Servico extends Model
{
    use HasFactory;

    /* ──────────────────────────── Atributos ─────────────────────────── */
    protected $fillable = [
        'escritorio_id',
        'tipo_servico_id',
        'tipo_cliente',
        'cliente_id',
        'data_inicio',
        'observacoes',
        'anexos',
        'numero_processo',
    ];

    protected $casts = [
        'anexos'      => 'array',
        'data_inicio' => 'date',
    ];

    /* ───────────────────────── Relacionamentos ──────────────────────── */
    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function tipoServico()
    {
        return $this->belongsTo(TipoServico::class, 'tipo_servico_id');
    }

    public function cliente()
    {
        return $this->tipo_cliente === 'pessoa_fisica'
            ? $this->belongsTo(ClientePessoaFisica::class, 'cliente_id')
            : $this->belongsTo(ClientePessoaJuridica::class, 'cliente_id');
    }

    /** Instância PF/PJ já resolvida */
    public function getClienteFormatadoAttribute()
    {
        return $this->tipo_cliente === 'pessoa_fisica'
            ? ClientePessoaFisica::find($this->cliente_id)
            : ClientePessoaJuridica::find($this->cliente_id);
    }

    public function andamentos()
    {
        return $this->hasMany(AndamentoServico::class, 'servico_id');
    }

    /* ─────────────────── Nº do processo – formatado ─────────────────── */
    public function getNumeroProcessoFormatadoAttribute(): ?string
    {
        if (!$this->numero_processo) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->numero_processo);

        return preg_replace(
            '/^(\d{7})(\d{2})(\d{4})(\d)(\d{2})(\d{4})$/',
            '$1-$2.$3.$4.$5.$6',
            $digits
        );
    }

    /* ─────── NÃO exibimos mais link CNJ (retorna sempre null) ─────── */
    public function getConsultaProcessoUrlAttribute(): ?string
    {
        return null;
    }

    /* link genérico da página de consulta do tribunal (sem nº) */
    public function getConsultaProcessoTribunalUrlAttribute(): ?string
    {
        $sigla = $this->detalhes_processo['tribunal_sigla'] ?? null;
        if (!$sigla) {
            return null;
        }

        $map = Config::get('cnj.urls_tribunais', []);
        if (!isset($map[$sigla])) {
            return null;
        }

        // remove eventual “=” do final
        return rtrim($map[$sigla], '=');
    }

    /* ─────────────── Detalhes (Órgão, Tribunal, Vara) ──────────────── */
    public function getDetalhesProcessoAttribute(): ?array
    {
        if (!$this->numero_processo) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->numero_processo);

        if (!preg_match('/^(\d{7})(\d{2})(\d{4})(\d)(\d{2})(\d{4})$/', $digits, $m)) {
            return null;
        }

        [, $seq, $dv, $ano, $orgao, $tr, $vara] = $m;

        return [
            'sequencial'      => $seq,
            'digito'          => $dv,
            'ano'             => $ano,
            'orgao_codigo'    => $orgao,
            'orgao_nome'      => Config::get("cnj.orgaos.$orgao",  'Órgão desconhecido'),
            'tribunal_codigo' => $tr,
            'tribunal_sigla'  => Config::get("cnj.tribunais.$tr"),
            'tribunal_nome'   => Config::get("cnj.tribunais.$tr", 'Tribunal desconhecido'),
            'vara_codigo'     => $vara,
        ];
    }
}
