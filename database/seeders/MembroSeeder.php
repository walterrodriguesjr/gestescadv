<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserData;
use App\Models\MembroEscritorio;
use App\Models\PermissaoUsuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class MembroSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'email' => 'walterrjr.86@gmail.com',
                'membros' => [
                    ['nome' => 'Paulo', 'email' => 'paulo.walter', 'status' => 'ativo', 'cidade' => 'São Paulo', 'estado' => 'SP'],
                    ['nome' => 'Carlos', 'email' => 'carlos.walter', 'status' => 'pendente', 'cidade' => 'Guarulhos', 'estado' => 'SP'],
                    ['nome' => 'Pedro', 'email' => 'pedro.walter', 'status' => 'inativo', 'cidade' => 'Campinas', 'estado' => 'SP'],
                ],
            ],
            [
                'email' => 'renan@gmail.com',
                'membros' => [
                    ['nome' => 'Marcos', 'email' => 'marcos.renan', 'status' => 'ativo', 'cidade' => 'Curitiba', 'estado' => 'PR'],
                    ['nome' => 'Rafael', 'email' => 'rafael.renan', 'status' => 'pendente', 'cidade' => 'Londrina', 'estado' => 'PR'],
                    ['nome' => 'Lucas', 'email' => 'lucas.renan', 'status' => 'inativo', 'cidade' => 'Maringá', 'estado' => 'PR'],
                ],
            ],
        ];

        foreach ($admins as $adminData) {
            $admin = User::where('email', $adminData['email'])->first();

            if (!$admin) {
                continue;
            }

            $escritorio = $admin->escritorio;

            foreach ($adminData['membros'] as $membro) {
                // Cria o usuário membro
                $user = User::create([
                    'name' => $membro['nome'],
                    'email' => $membro['email'] . '@gmail.com',
                    'password' => Hash::make('123456'),
                ]);

                // Cria os dados do usuário no UserData
                UserData::create([
                    'user_id' => $user->id,
                    'cpf' => Crypt::encryptString(rand(100, 999) . '.' . rand(100, 999) . '.' . rand(100, 999) . '-' . rand(10, 99)),
                    'telefone' => null,
                    'celular' => Crypt::encryptString('(11) 9' . rand(4000, 9999) . '-' . rand(1000, 9999)),
                    'cidade' => $membro['cidade'],
                    'estado' => $membro['estado'],
                    'oab' => null,
                    'estado_oab' => null,
                    'data_nascimento' => now()->subYears(rand(18, 45))->format('Y-m-d'),
                    'foto' => null,
                ]);

                // Associa o membro ao escritório
                MembroEscritorio::create([
                    'user_id' => $user->id,
                    'escritorio_id' => $escritorio->id,
                    'gestor_id' => $admin->id,
                    'status' => $membro['status'],
                ]);

                // Concede permissão ao membro
                PermissaoUsuario::create([
                    'usuario_id' => $user->id,
                    'nivel_acesso_id' => 2, // Assumindo nível "Usuário"
                    'escritorio_id' => $escritorio->id,
                    'concedente_id' => $admin->id,
                ]);
            }
        }
    }
}
