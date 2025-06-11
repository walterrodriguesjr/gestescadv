<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => 'Enviamos um link para redefinir sua senha. Verifique seu email.'])
            : back()->withErrors(['email' => __($status)]);
    }



    public function resetPassword(Request $request)
{
    $validated = $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => [
            'required',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ],
    ], [
        'password.regex' => 'A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial.',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            // 🔥 Atualiza o status do membro para "ativo"
            \App\Models\MembroEscritorio::where('user_id', $user->id)
                ->update(['status' => 'ativo']);
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('success', 'Senha redefinida com sucesso. Agora você pode acessar sua conta.')
        : back()->withErrors(['email' => __($status)]);
}



    public function showResetForm(Request $request, $token)
    {
        return view('password.reset-password', [
            'token' => $token,
            'email' => $request->email, // Adicione o email se necessário na view
        ]);
    }

    public function alterarSenha(Request $request)
    {
        // Validação dos campos
        $request->validate([
            'senha_atual' => ['required'],
            'nova_senha' => [
                'required',
                'min:8',
                'confirmed', // Valida com o campo `nova_senha_confirmation`
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
        ], [
            'nova_senha.regex' => 'A senha deve conter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial.',
        ]);

        // Verifica se a senha atual está correta
        if (!Hash::check($request->senha_atual, Auth::user()->password)) {
            return response()->json(['error' => 'A senha atual está incorreta.'], 422);
        }

        // Atualiza a senha do usuário logado
        Auth::user()->update([
            'password' => Hash::make($request->nova_senha),
        ]);

        return response()->json(['success' => 'Senha atualizada com sucesso!']);
    }
}
