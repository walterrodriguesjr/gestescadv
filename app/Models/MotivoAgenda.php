<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoAgenda extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'motivo_agenda_id');
    }
}
