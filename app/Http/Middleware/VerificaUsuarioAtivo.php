<?php

namespace App\Http\Middleware;

use App\Models\MembroEscritorio;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificaUsuarioAtivo
{
    /**
     * Manipula a requisição.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Se o usuário estiver autenticado e inativo, impede o acesso
        if ($user && MembroEscritorio::where('user_id', $user->id)->where('status', 'inativo')->exists()) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta foi suspensa pelo Gestor do escritório ao qual você era convidado.',
            ]);
        }

        return $next($request);
    }
}
