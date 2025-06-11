<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NivelAcesso;

class NivelAcessoSeeder extends Seeder
{
    public function run(): void
    {
        $niveis = [
            [
                'nome' => 'Administrador',
                'permissoes' => json_encode([
                    'gerenciar_tudo' => true,
                    'visualizar_dados' => true,
                    'gerenciar_escritorios' => true,
                    'gerenciar_usuarios' => true,
                ]),
            ],
            /* [
                'nome' => 'Gestor',
                'permissoes' => json_encode([
                    'gerenciar_escritorio' => true,
                    'cadastrar_advogados' => true,
                    'cadastrar_estagiarios' => true,
                    'visualizar_dados' => true,
                ]),
            ], */
            /* [
                'nome' => 'Advogado',
                'permissoes' => json_encode([
                    'visualizar_processos' => true,
                    'inserir_documentos' => true,
                    'visualizar_dados' => true,
                ]),
            ], */
            [
                'nome' => 'Estagiário',
                'permissoes' => json_encode([
                    'visualizar_processos' => true,
                    'inserir_documentos' => false,
                    'visualizar_dados' => false,
                ]),
            ],
            [
                'nome' => 'Funcionário',
                'permissoes' => json_encode([
                    'visualizar_processos' => true,
                    'inserir_documentos' => false,
                    'visualizar_dados' => false,
                ]),
            ],
        ];

        foreach ($niveis as $nivel) {
            NivelAcesso::firstOrCreate(['nome' => $nivel['nome']], $nivel);
        }
    }
}
