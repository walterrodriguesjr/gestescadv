<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços do aplicativo.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicia o aplicativo.
     */
    public function boot(): void
    {
        if (str_contains(config('app.url'), 'ngrok-free.app')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
        // 🔥 Definição de Gates baseada nas permissões armazenadas no banco
        Gate::define('gerenciar-tudo', function (User $user) {
            return $user->hasPermission('gerenciar_tudo');
        });

        Gate::define('visualizar-dados', function (User $user) {
            return $user->hasPermission('visualizar_dados');
        });

        Gate::define('gerenciar-escritorio', function (User $user) {
            return $user->hasPermission('gerenciar_escritorio') || $user->hasPermission('gerenciar_tudo');
        });

        Gate::define('cadastrar-advogados', function (User $user) {
            return $user->hasPermission('cadastrar_advogados');
        });

        Gate::define('cadastrar-estagiarios', function (User $user) {
            return $user->hasPermission('cadastrar_estagiarios');
        });

        Gate::define('visualizar-processos', function (User $user) {
            return $user->hasPermission('visualizar_processos');
        });

        Gate::define('inserir-documentos', function (User $user) {
            return $user->hasPermission('inserir_documentos');
        });
    }
}
