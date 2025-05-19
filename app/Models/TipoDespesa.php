<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDespesa extends Model
{
    use HasFactory;

    protected $table = 'tipos_despesa';

    protected $fillable = [
        'titulo',
        'escritorio_id',
        'user_id',
    ];

    public function escritorio()
    {
        return $this->belongsTo(Escritorio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

   /*  public function despesas()
    {
        return $this->hasMany(Despesa::class, 'tipo_despesa_id');
    } */
}
