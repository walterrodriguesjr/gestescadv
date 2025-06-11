<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtapasServicoSeeder extends Seeder
{
    public function run()
    {
        DB::table('etapas_servico')->insert([
            ['nome' => 'Início do Processo', 'icone_cor' => 'bg-primary'],
            ['nome' => 'Análise de Documentos', 'icone_cor' => 'bg-info'],
            ['nome' => 'Elaboração de Petição Inicial', 'icone_cor' => 'bg-primary'],
            ['nome' => 'Protocolo no Sistema', 'icone_cor' => 'bg-primary'],
            ['nome' => 'Audiência de Conciliação', 'icone_cor' => 'bg-success'],
            ['nome' => 'Audiência de Instrução e Julgamento', 'icone_cor' => 'bg-success'],
            ['nome' => 'Decisão Judicial', 'icone_cor' => 'bg-warning'],
            ['nome' => 'Sentença Publicada', 'icone_cor' => 'bg-warning'],
            ['nome' => 'Recurso Interposto', 'icone_cor' => 'bg-danger'],
            ['nome' => 'Execução de Sentença', 'icone_cor' => 'bg-dark'],
            ['nome' => 'Arquivamento', 'icone_cor' => 'bg-secondary'],
        ]);
    }
}
