<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoClientePessoaJuridica extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_pessoa_juridica_id',
        'nome_original',
        'nome_arquivo',
        'tipo_documento',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClientePessoaJuridica::class, 'cliente_pessoa_juridica_id');
    }

    public function documentos()
{
    return $this->hasMany(DocumentoClientePessoaJuridica::class, 'cliente_pessoa_juridica_id');
}

}

