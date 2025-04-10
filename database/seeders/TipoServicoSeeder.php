<?php

namespace Database\Seeders;

use App\Models\TipoServico;
use Illuminate\Database\Seeder;

class TipoServicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicos = [
            'Ainda não definido',
            'Consultoria',
            'Contrato',
            'Trabalhista',
            'Criminal',
            'Cível',
            'Divórcio/Pensão Alimentícia',
            'Imobiliário',
            'Tributário',
            'Consumidor',
            'Crédito',
            'Previdenciário',
        ];

        foreach ($servicos as $servico) {
            TipoServico::create([
                'escritorio_id' => 1,
                'nome_servico' => $servico,
            ]);
        }
    }
}
