<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoClientePessoaFisica extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_pessoa_fisica_id',
        'nome_original',
        'nome_arquivo',
        'tipo_documento',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClientePessoaFisica::class, 'cliente_pessoa_fisica_id');
    }

    public function documentos()
{
    return $this->hasMany(DocumentoClientePessoaFisica::class, 'cliente_pessoa_fisica_id');
}

}
