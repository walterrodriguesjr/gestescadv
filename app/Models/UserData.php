<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class UserData extends Model
{
    protected $fillable = [
        'user_id',
        'cpf',
        'telefone',
        'celular',
        'cidade',
        'estado',
        'oab',
        'estado_oab',
        'data_nascimento',
        'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


public function getDecrypted($field)
{
    try {
        $encryptedFields = ['cpf', 'telefone', 'celular', 'oab'];

        if (in_array($field, $encryptedFields) && !is_null($this->$field)) {
            return Crypt::decryptString($this->$field); // 🔥 Aqui troca para decryptString
        }
        return $this->$field;
    } catch (\Exception $e) {
        Log::error("❌ Erro ao descriptografar {$field}: " . $e->getMessage());
        return null;
    }
}

}
