<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset';
    protected $description = 'Reseta o banco de dados, roda as migrations e popula os dados corretamente.';

    public function handle()
    {
        $this->info("🚀 Resetando banco de dados...");

        // Apagar diretórios com exceções
        $this->limparArquivos();

        Artisan::call('migrate:fresh');
        $this->info("✅ Migrations executadas com sucesso!");

        $seeders = [
            'NivelAcessoSeeder',
            'AdminUserSeeder',
            'AdminUserPermissaoSeeder',
            'EscritorioSeeder',
            'ClienteSeeder',
            'MembroSeeder',
            'TipoServicoSeeder',
            'EtapasServicoSeeder',
            'TipoDespesaSeeder'
        ];

        foreach ($seeders as $seeder) {
            Artisan::call("db:seed --class={$seeder}");
            $this->info("✅ {$seeder} executado com sucesso.");
        }

        $this->info("🎉 Banco de dados resetado e populado com sucesso!");
    }

    protected function limparArquivos()
    {
        $pastasParaApagar = [
            storage_path('app/public/arquivos_servicos'),
            storage_path('app/public/arquivos_servicos_andamento'),
            storage_path('app/public/documento-usuario'),
            storage_path('app/public/honorarios'),
        ];

        foreach ($pastasParaApagar as $pasta) {
            if (File::exists($pasta)) {
                File::deleteDirectory($pasta);
                $this->info("🗑️  Removido: {$pasta}");
            }
        }

        // Remover todas as fotos de perfil, exceto a padrão
        $fotoPerfilPath = storage_path('app/public/foto-perfil');
        $fotoPadrao = 'sem-foto.jpg';

        if (File::exists($fotoPerfilPath)) {
            $fotos = File::files($fotoPerfilPath);
            foreach ($fotos as $foto) {
                if ($foto->getFilename() !== $fotoPadrao) {
                    File::delete($foto->getRealPath());
                    $this->info("🧹 Foto removida: {$foto->getFilename()}");
                }
            }
        }
    }
}
