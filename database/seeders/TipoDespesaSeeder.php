<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Escritorio;
use App\Models\TipoDespesa;

class TipoDespesaSeeder extends Seeder
{
    public function run()
    {
        $tiposPadrao = [
            // Operacionais
            'Protocolos e Custas',
            'Cópias e Impressões',
            'Correios e Entregas',
            'Deslocamento e Transporte',
            'Honorários de Terceiros',

            // Administrativas
            'Água',
            'Energia Elétrica',
            'Aluguel do Prédio',
            'Internet',
            'Telefone',
            'Manutenção de Equipamentos',
            'Material de Escritório',
            'Serviços de Limpeza',
            'Assinaturas e Licenças (OAB, Softwares, etc)'
        ];

        Escritorio::all()->each(function ($escritorio) use ($tiposPadrao) {
            foreach ($tiposPadrao as $titulo) {
                TipoDespesa::firstOrCreate(
                    [
                        'escritorio_id' => $escritorio->id,
                        'titulo'        => $titulo,
                    ],
                    [
                        'user_id' => 1 // ou null, se preferir
                    ]
                );
            }
        });
    }
}
