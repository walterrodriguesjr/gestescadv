<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin original
        $admin1 = User::create([
            'name' => 'Admin',
            'email' => 'walterrjr.86@gmail.com',
            'password' => Hash::make('Pmprparana2025!'),
            'two_factor_enabled' => false,
            'two_factor_type' => 'email',
        ]);

        UserData::create([
            'user_id' => $admin1->id,
            'cpf' => Crypt::encryptString('123.456.789-00'),
            'telefone' => Crypt::encryptString('(11) 4002-8922'),
            'celular' => Crypt::encryptString('(41) 99999-8888'),
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'oab' => Crypt::encryptString('123456'),
            'estado_oab' => 'SP',
            'data_nascimento' => '1990-01-01',
        ]);

        // Admin 2 (Renan)
        $admin2 = User::create([
            'name' => 'Admin 2',
            'email' => 'renan@gmail.com',
            'password' => Hash::make('Pmprparana2026!'),
            'two_factor_enabled' => false,
            'two_factor_type' => 'email',
        ]);

        UserData::create([
            'user_id' => $admin2->id,
            'cpf' => Crypt::encryptString('987.654.321-00'),
            'telefone' => Crypt::encryptString('(41) 4002-8922'),
            'celular' => Crypt::encryptString('(41) 98888-7777'),
            'cidade' => 'Curitiba',
            'estado' => 'PR',
            'oab' => Crypt::encryptString('654321'),
            'estado_oab' => 'PR',
            'data_nascimento' => '1992-02-02',
        ]);
    }
}
