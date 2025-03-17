<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset';
    protected $description = 'Reseta o banco de dados, roda as migrations e popula os dados corretamente.';

    public function handle()
    {
        $this->info("🚀 Resetando banco de dados...");

        Artisan::call('migrate:fresh');
        $this->info("✅ Migrations executadas com sucesso!");

        $seeders = [
            'NivelAcessoSeeder',
            'AdminUserSeeder',
            'AdminUserPermissaoSeeder',
            'EscritorioSeeder',
            'ClienteSeeder',
            'MembroSeeder',
        ];

        foreach ($seeders as $seeder) {
            Artisan::call("db:seed --class={$seeder}");
            $this->info("✅ {$seeder} executado com sucesso.");
        }

        $this->info("🎉 Banco de dados resetado e populado com sucesso!");
    }
}
