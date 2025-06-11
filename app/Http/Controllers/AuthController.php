<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginFailedTooManyTimes;
use App\Notifications\LoginSuccessful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:12', // Pelo menos 12 caracteres
                'regex:/[a-z]/', // Pelo menos uma letra minúscula
                'regex:/[A-Z]/', // Pelo menos uma letra maiúscula
                'regex:/[0-9]/', // Pelo menos um número
                'regex:/[@$!%*#?&]/', // Pelo menos um caractere especial
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Usuário registrado com sucesso!');
    }

    public function login(Request $request)
{
    try {
        // Validação básica
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca o usuário
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'E-mail não cadastrado.'])->withInput();
        }

        // Contador de tentativas de login falhas (Throttle)
        $maxAttempts = 5; // Número máximo de tentativas antes do bloqueio
        $lockoutTime = 60; // Tempo de bloqueio em segundos (1 minuto)
        $key = 'login_attempts_' . $request->ip();

        if (cache()->has($key) && cache()->get($key) >= $maxAttempts) {
            // Enviar e-mail de alerta de tentativas falhas ao usuário
            $user->notify(new LoginFailedTooManyTimes());
            return back()->withErrors(['throttle' => 'Muitas tentativas de login. Tente novamente em 1 minuto.'])->withInput();
        }

        // Verifica credenciais
        if (!Auth::attempt($request->only('email', 'password'))) {
            cache()->increment($key, 1);
            cache()->put($key, cache()->get($key, 0) + 1, now()->addSeconds($lockoutTime));
            return back()->withErrors(['password' => 'Senha incorreta.'])->withInput();
        }

        // Limpa tentativas falhas após login bem-sucedido
        cache()->forget($key);

        // Login bem-sucedido
        $user = Auth::user();

        // Verifica se o 2FA está habilitado
        if ($user->two_factor_enabled) {
            $user->generateTwoFactorCode(); // Gera o código de 2FA
            $user->sendTwoFactorCode(); // Envia o código por e-mail ou SMS

            return redirect()->route('two-factor.show')->with('status', 'Código de verificação enviado.');
        }

        // Se 2FA não estiver ativado, enviar e-mail de login bem-sucedido
        $user->notify(new LoginSuccessful());

        return redirect()->route('main')->with('success', 'Bem-vindo, ' . $user->name);
    } catch (ThrottleRequestsException $e) {
        return back()->withErrors(['throttle' => 'Muitas tentativas de login. Tente novamente em 1 minuto.'])->withInput();
    }
}


    public function logout(Request $request)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Você já está desconectado.');
        }

        Auth::logout(); // Realiza logout da sessão
        $request->session()->invalidate(); // Invalida a sessão atual
        $request->session()->regenerateToken(); // Gera um novo token CSRF

        return redirect()->route('login')->with('success', 'Você foi desconectado com sucesso.');
    }
}
