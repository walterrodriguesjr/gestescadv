<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SessaoController extends Controller
{
    /**
     * Exibe todas as sessões ativas do usuário logado.
     */
    public function listarSessoesAtivas()
    {
        $userId = Auth::id();

        // Obtém todas as sessões ativas do usuário logado
        $sessoes = DB::table('sessions')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'ultima_atividade' => date('d/m/Y H:i:s', $session->last_activity),
                ];
            });

        return response()->json($sessoes);
    }

    /**
     * Encerra uma sessão específica do usuário.
     */
    public function encerrarSessao(Request $request, $id)
    {
        $user = Auth::user();
        $session = DB::table('sessions')->where('id', $id)->first();

        if (!$session || $session->user_id !== $user->id) {
            return response()->json(['message' => 'Sessão não encontrada ou não pertence a você.'], 403);
        }

        DB::table('sessions')->where('id', $id)->delete(); // Remove a sessão do banco

        // Se o usuário encerrou a própria sessão ativa, forçamos o logout
        if ($session->id === session()->getId()) {
            Auth::logout();
            return response()->json(['logout' => true, 'message' => 'Sessão encerrada. Você foi desconectado.']);
        }

        return response()->json(['message' => 'Sessão encerrada com sucesso.']);
    }


    /**
     * Encerra todas as sessões do usuário, exceto a atual.
     */
    public function encerrarTodasSessoes(Request $request)
    {
        $user = Auth::user();

        // Remove todas as sessões do usuário do banco de dados
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Logout da sessão atual
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return response()->json([
            'logout' => true,
            'message' => 'Todas as sessões foram encerradas com sucesso.',
        ]);
    }
}
