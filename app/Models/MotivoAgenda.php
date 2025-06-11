<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoAgenda extends Model
{
    use HasFactory;

    protected $table = 'motivos_agenda'; // nome correto da tabela no banco

    protected $fillable = ['nome']; // campos que existem na tabela

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'motivo_agenda_id');
    }

    public function motivo()
{
    return $this->belongsTo(MotivoAgenda::class, 'motivo_agenda_id');
}

}
