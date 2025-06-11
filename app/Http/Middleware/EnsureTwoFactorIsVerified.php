<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorIsVerified
{
    /**
     * Manipula a solicitação de entrada.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Verifica se o usuário está autenticado
        if (!$user) {
            return redirect()->route('login');
        }

        // Verifica se o 2FA está habilitado e se o código ou expiração ainda é válido
        if (
            $user->two_factor_enabled &&
            ($user->two_factor_code || ($user->two_factor_expires_at && now()->lessThan($user->two_factor_expires_at)))
        ) {
            return redirect()->route('two-factor.show')
                ->with('status', 'Você precisa verificar sua identidade.');
        }

        return $next($request);
    }
}
