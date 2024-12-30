<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_data';

    protected $fillable = [
        'user_id',
        'user_cpf',
        'user_celular'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
