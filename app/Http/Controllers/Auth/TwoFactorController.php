<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TwoFactorController extends RoutingController
{
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = Auth::user();

        if ($user->two_factor_code !== $request->code || now()->greaterThan($user->two_factor_expires_at)) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        // Limpa o código após uso
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        return redirect()->route('main');
    }

    public function showTwoFactorForm()
    {
        return view('two-factor.two-factor');
    }

    public function resendTwoFactorCode(Request $request)
    {
        $user = Auth::user();

        // Gera e envia um novo código de 2FA 
        $user->generateTwoFactorCode();
        $user->sendTwoFactorCode();

        return back()->with('status', 'Um novo código foi enviado para seu ' . ($user->two_factor_type === 'sms' ? 'número de telefone' : 'e-mail') . '.');
    }

    public function atualizarAutenticacaoDoisFatores(Request $request)
    {
        // Validação
        $validator = Validator::make($request->all(), [
            'dois_fatores' => 'required|in:sim,nao',
            'tipo_autenticacao' => 'nullable|in:email,sms,app',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if ($request->dois_fatores === "nao") {
            // Desativa 2FA
            $user->forceFill([
                'two_factor_enabled' => false,
                'two_factor_type' => null,
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ])->save();
        } else {
            // Ativa 2FA e define o método escolhido
            if (!$request->tipo_autenticacao) {
                return response()->json(['error' => 'Escolha um método para ativar a autenticação de dois fatores.'], 400);
            }

            $user->forceFill([
                'two_factor_enabled' => true,
                'two_factor_type' => $request->tipo_autenticacao,
            ])->save();

            // Gera e envia o código inicial para o usuário
            $user->generateTwoFactorCode();
            $user->sendTwoFactorCode();
        }

        return response()->json([
            'message' => 'Configuração de segurança atualizada com sucesso!',
            'two_factor_enabled' => $user->two_factor_enabled,
            'two_factor_type' => $user->two_factor_type
        ], 200);
    }
}
