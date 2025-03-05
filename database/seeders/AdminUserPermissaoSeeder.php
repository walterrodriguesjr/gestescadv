<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NivelAcesso;
use App\Models\PermissaoUsuario;

class AdminUserPermissaoSeeder extends Seeder // O nome da classe deve ser igual ao nome do arquivo!
{
    public function run(): void
    {
        $adminNivel = NivelAcesso::where('nome', 'Administrador')->first();

        if (!$adminNivel) {
            $this->command->error("🚨 Nível de acesso 'Administrador' não encontrado. Execute o seeder NivelAcessoSeeder primeiro.");
            return;
        }

        $adminUser = User::where('email', 'walterrjr.86@gmail.com')->first();

        if ($adminUser) {
            PermissaoUsuario::updateOrCreate(
                ['usuario_id' => $adminUser->id, 'nivel_acesso_id' => $adminNivel->id],
                ['escritorio_id' => null, 'concedente_id' => null]
            );

            $this->command->info("✅ Usuário Admin recebeu nível 'Administrador'.");
        } else {
            $this->command->warn("⚠️ Usuário Admin não encontrado. Execute o seeder AdminUserSeeder primeiro.");
        }
    }
}
