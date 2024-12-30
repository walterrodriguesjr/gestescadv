<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer para injetar dados em todas as views
    View::composer('profile.show', function ($view) {
        $user = auth()->user();
        $view->with([
            'cpf' => $user->userData->user_cpf ?? '',
            'celular' => $user->userData->user_celular ?? '',
        ]);
    });
    }
}
