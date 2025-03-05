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

        $this->info("📦 Populando os dados...");
        
        Artisan::call('db:seed --class=NivelAcessoSeeder');
        $this->info("✅ Níveis de acesso criados.");

        Artisan::call('db:seed --class=AdminUserSeeder');
        $this->info("✅ Usuário Administrador criado.");

        Artisan::call('db:seed --class=AdminUserPermissaoSeeder');
        $this->info("✅ Nível de Administrador atribuído ao usuário Admin.");

        $this->info("🎉 Banco de dados resetado e populado com sucesso!");
    }
}
