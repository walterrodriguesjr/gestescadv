<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Escritorio;

class EscritorioSeeder extends Seeder
{
    public function run(): void
    {
        $admin1 = User::where('email', 'walterrjr.86@gmail.com')->first();
        $admin2 = User::where('email', 'renan@gmail.com')->first();

        Escritorio::create([
            'user_id' => $admin1->id,
            'nome_escritorio' => 'Escritório Walter',
            'cnpj_escritorio' => '11.222.333/0001-01',
            'telefone_escritorio' => '(11) 3344-5566',
            'celular_escritorio' => '(11) 98765-4321',
            'email_escritorio' => 'contato@escritoriowalter.com',
            'cep_escritorio' => '01000-000',
            'logradouro_escritorio' => 'Rua das Flores',
            'numero_escritorio' => '123',
            'bairro_escritorio' => 'Centro',
            'estado_escritorio' => 'SP',
            'cidade_escritorio' => 'São Paulo',
        ]);

        Escritorio::create([
            'user_id' => $admin2->id,
            'nome_escritorio' => 'Escritório Renan',
            'cnpj_escritorio' => '22.333.444/0001-02',
            'telefone_escritorio' => '(41) 3344-5566',
            'celular_escritorio' => '(41) 97777-6666',
            'email_escritorio' => 'contato@escritoriorenan.com',
            'cep_escritorio' => '80000-000',
            'logradouro_escritorio' => 'Rua XV de Novembro',
            'numero_escritorio' => '456',
            'bairro_escritorio' => 'Centro',
            'estado_escritorio' => 'PR',
            'cidade_escritorio' => 'Curitiba',
        ]);
    }
}
