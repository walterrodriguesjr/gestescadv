<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;

class UsuarioUserDataController extends Controller
{

    /*     public function show()
    {
        $user = auth()->user();

        // Obtenha os dados complementares do usuário
        $cpf = $user->userData->user_cpf ?? '';
        $celular = $user->userData->user_celular ?? '';

        return view('profile.show', compact('cpf', 'celular'));
    } */



    public function CadastrarUsuarioUserData(Request $request)
    {
        try {
            // Validação dos dados
            $validated = $request->validate([
                'cpf' => 'nullable|string|size:11|unique:user_data,user_cpf,' . auth()->id() . ',user_id',
                'celular' => 'nullable|string|max:15',
            ]);

            // Obter o usuário autenticado
            $user = auth()->id();

            if (!$user) {
                throw new \Exception('Usuário não autenticado.');
            }

            // Atualizar ou criar os dados complementares
            $userData = UserData::updateOrCreate(
                ['user_id' => $user],
                [
                    'user_cpf' => $validated['cpf'] ?? null,
                    'user_celular' => $validated['celular'] ?? null,
                ]
            );

            // Retornar a resposta JSON
            return response()->json([
                'success' => true,
                'message' => 'Dados complementares atualizados com sucesso!',
                'data' => $userData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
