<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Ainda não definido',
            'Procuração',
            'Petição Inicial',
            'Requerimento Administrativo',
            'Recurso Ordinário Administrativo',
            'Recurso Especial Administrativo',
            'Contestação',
            'Recurso Inominado',
            'Apelação',
            'Embargos de Declaração',
            'Embargos Infringentes',
            'Embargos de Execução',
            'Petição de Cumprimento de Sentença',
            'Petição de Emenda à Inicial',
            'Petição de Contrarrazões de Apelação',
            'Petição de Contrarrazões de Recurso Inominado',
            'Petição de Impugnação à Contestação',
            'Petição de Agravo de Instrumento',
            'Petição de Recurso Extraordinário',
            'Petição de Recurso Especial Judicial',
            'Contrato',
            'Acordo',
            'Declaração',
            'Notificação Extrajudicial',
            'Requerimento',
            'Ata',
            'Ofício',
            'Certidão',
            'Laudo Pericial',
            'Recibo',
            'Parecer',
            'Termo de Quitação',
            'Memorando',
            'Procuração Ad Judicia',
            'Carta de Preposição',
            'Cessão de Direitos',
        ];

        foreach ($tipos as $tipo) {
            TipoDocumento::create([
                'escritorio_id' => 1,
                'user_id' => null,
                'titulo' => $tipo,
            ]);
        }
    }
}
