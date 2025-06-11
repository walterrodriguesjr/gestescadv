<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExclusaoConta extends Model
{
    use HasFactory;

    protected $table = 'exclusoes_conta';

    protected $fillable = [
        'user_id',
        'email',
        'data_solicitacao',
    ];
}
