<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissaoEscritorio
{
    public function handle(Request $request, Closure $next)
{
    $usuario = Auth::user();

    // 🔍 Verifica se o usuário está autenticado
    if (!$usuario) {
        return response()->json([
            'success' => false,
            'message' => 'Acesso negado. Você precisa estar autenticado.'
        ], 403);
    }

    // 🔍 Obtém o nível de acesso do usuário através da relação permissaoUsuario → nivelAcesso
    $nivelAcesso = $usuario->permissoes()->with('nivelAcesso')->get()->pluck('nivelAcesso.nome');

    // 🔍 Permissões permitidas
    $permissoesPermitidas = ['Administrador', 'Gestor'];

    // 🚀 Verifica se o usuário tem um dos níveis permitidos
    if (!$nivelAcesso->intersect($permissoesPermitidas)->count()) {
        Log::warning("🚨 AUDITORIA - Middleware: O usuário '{$usuario->name}' (ID: {$usuario->id}) tentou realizar uma ação restrita no escritório sem permissão.", [
            'user_id' => $usuario->id ?? 'Desconhecido',
            'user_nome' => $usuario->name ?? 'Nome desconhecido',
            'user_email' => $usuario->email ?? 'Sem email',
            'rota' => $request->path(),
            'ip' => $request->ip(),
            'dados_enviados' => $request->all(),
            'timestamp' => now(), // ⏳ Inclui o timestamp exato do evento
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Acesso negado. Você precisa ser Administrador ou Gestor para realizar esta ação.'
        ], 403);
    }

    return $next($request);
}

}
