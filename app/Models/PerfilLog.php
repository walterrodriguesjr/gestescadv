<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilLog extends Model
{
    use HasFactory;

    protected $table = 'perfil_logs';

    protected $fillable = [
        'user_id',
        'campo',
        'valor_anterior',
        'valor_novo',
        'alterado_em',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
