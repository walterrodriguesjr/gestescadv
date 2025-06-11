<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Escritorio;
use App\Models\ClientePessoaFisica;
use App\Models\ClientePessoaJuridica;
use Illuminate\Support\Facades\Crypt;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $escritorios = Escritorio::all();

        foreach ($escritorios as $escritorio) {
            // Criando 5 Clientes Pessoa Física
            for ($i = 1; $i <= 5; $i++) {
                $cpf = '11111111' . rand(1, 9) . str_pad((string)rand(0, 99), 2, '0', STR_PAD_LEFT);

                ClientePessoaFisica::create([
                    'escritorio_id' => $escritorio->id,
                    'nome' => 'Cliente PF ' . $escritorio->nome_escritorio . ' #' . $i,
                    'cpf' => Crypt::encryptString($cpf),
                    'telefone' => null,
                    'celular' => Crypt::encryptString('1198888' . rand(1000, 9999)),
                    'email' => Crypt::encryptString('pf' . $escritorio->id . $i . '@gmail.com'),
                    'cep' => Crypt::encryptString('0100' . $i . '000'),
                    'logradouro' => Crypt::encryptString('Rua PF ' . $i),
                    'numero' => Crypt::encryptString((string)(rand(10, 500))),
                    'bairro' => Crypt::encryptString('Bairro PF ' . $i),
                    'cidade' => Crypt::encryptString('Cidade PF ' . $i),
                    'estado' => Crypt::encryptString('SP'),
                ]);
            }

            // Criando 5 Clientes Pessoa Jurídica
            for ($i = 1; $i <= 5; $i++) {
                $cnpj = '11111' . rand(100, 999) . str_pad((string)rand(1000, 9999), 4, '0', STR_PAD_LEFT) . str_pad((string)$i, 2, '0', STR_PAD_LEFT);

                ClientePessoaJuridica::create([
                    'escritorio_id' => $escritorio->id,
                    'razao_social' => 'Cliente PJ ' . $escritorio->nome_escritorio . ' #' . $i,
                    'nome_fantasia' => 'Fantasia PJ ' . $escritorio->id . '-' . $i,
                    'cnpj' => Crypt::encryptString($cnpj),
                    'telefone' => null,
                    'celular' => Crypt::encryptString('1197777' . rand(1000, 9999)),
                    'email' => Crypt::encryptString('pj' . $escritorio->id . $i . '@gmail.com'),
                    'cep' => Crypt::encryptString('0200' . $i . '000'),
                    'logradouro' => Crypt::encryptString('Rua PJ ' . $i),
                    'numero' => Crypt::encryptString((string)(rand(20, 800))),
                    'bairro' => Crypt::encryptString('Bairro PJ ' . $i),
                    'cidade' => Crypt::encryptString('Cidade PJ ' . $i),
                    'estado' => Crypt::encryptString('SP'),
                ]);
            }
        }
    }
}
