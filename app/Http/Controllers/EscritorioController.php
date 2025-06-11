<?php

namespace App\Http\Controllers;

use App\Models\Escritorio;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EscritorioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('escritorio.escritorio');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        DB::beginTransaction();

        $user = Auth::user();

        // Verifica se o usuário já possui um escritório
        if ($user->escritorio) {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui um escritório cadastrado. Apenas atualizações são permitidas.'
            ], 400);
        }

        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'nome_escritorio' => 'required|string|max:255',
            'cnpj_escritorio' => 'nullable|string|max:18',
            'telefone_escritorio' => 'nullable|string|max:15',
            'celular_escritorio' => 'required|string|max:15',
            'email_escritorio' => 'required|email|max:255',
            'cep_escritorio' => 'nullable|string|max:9',
            'logradouro_escritorio' => 'nullable|string|max:255',
            'numero_escritorio' => 'nullable|string|max:10',
            'bairro_escritorio' => 'nullable|string|max:255',
            'estado_escritorio' => 'nullable|string|max:2',
            'cidade_escritorio' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Criação do escritório garantindo que valores vazios sejam nulos
        $data = array_map(fn($value) => $value === "" ? null : $value, $request->all());
        $data['user_id'] = $user->id;

        $escritorio = Escritorio::create($data);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Escritório cadastrado com sucesso!',
            // 🔥 **Aqui garantimos que "dados" contenha "id"**  
            'dados' => [
                'id'                  => $escritorio->id,
                'nome_escritorio'     => $escritorio->nome_escritorio,
                'cnpj_escritorio'     => $escritorio->cnpj_escritorio,
                'telefone_escritorio' => $escritorio->telefone_escritorio,
                'celular_escritorio'  => $escritorio->celular_escritorio,
                'email_escritorio'    => $escritorio->email_escritorio,
                'cep_escritorio'      => $escritorio->cep_escritorio,
                'logradouro_escritorio' => $escritorio->logradouro_escritorio,
                'numero_escritorio'   => $escritorio->numero_escritorio,
                'bairro_escritorio'   => $escritorio->bairro_escritorio,
                'estado_escritorio'   => $escritorio->estado_escritorio,
                'cidade_escritorio'   => $escritorio->cidade_escritorio,
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Erro ao cadastrar o escritório.',
            'error' => $e->getMessage() // Para debug, remova em produção
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show()
{
    try {
        $usuario = Auth::user();

        Log::info("🔍 Buscando escritório para usuário ID: {$usuario->id}");

        // 🔎 Busca o escritório ao qual o usuário pertence (como dono ou membro)
        $escritorio = Escritorio::where('user_id', $usuario->id)
            ->orWhereHas('membros', function ($q) use ($usuario) {
                $q->where('user_id', $usuario->id);
            })
            ->first();

        if (!$escritorio) {
            Log::warning("⚠️ Nenhum escritório encontrado para o usuário ID {$usuario->id}");
            return response()->json([
                'success' => false,
                'message' => 'Nenhum escritório encontrado para este usuário.'
            ], 403);
        }

        Log::info("✅ Escritório encontrado", ['escritorio_id' => $escritorio->id]);

        // 🔎 Buscar membros vinculados ao escritório
        $membros = $escritorio->membros()->with('usuario:id,name,email')->get()
            ->map(fn($membro) => [
                'id'    => $membro->id,
                'nome'  => $membro->usuario->name,
                'email' => $membro->usuario->email,
            ]);

        Log::info("✅ Membros do escritório carregados", ['membros' => $membros]);

        return response()->json([
            'success' => true,
            'dados' => [
                'id'                 => $escritorio->id,
                'nome_escritorio'    => $escritorio->nome_escritorio,
                'cnpj_escritorio'    => $escritorio->cnpj_escritorio,
                'telefone_escritorio' => $escritorio->telefone_escritorio,
                'celular_escritorio' => $escritorio->celular_escritorio,
                'email_escritorio'   => $escritorio->email_escritorio,
                'cep_escritorio'     => $escritorio->cep_escritorio,
                'logradouro_escritorio' => $escritorio->logradouro_escritorio,
                'numero_escritorio'  => $escritorio->numero_escritorio,
                'bairro_escritorio'  => $escritorio->bairro_escritorio,
                'estado_escritorio'  => $escritorio->estado_escritorio,
                'cidade_escritorio'  => $escritorio->cidade_escritorio,
                'membros'            => $membros,
            ],
        ]);

    } catch (\Exception $e) {
        Log::error("❌ Erro ao carregar escritório: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erro ao carregar os dados do escritório.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Escritorio $escritorio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();
        
        $usuario = Auth::user();

        // 🔍 Obtém os níveis de acesso do usuário
        $niveisAcesso = $usuario->permissoes()->with('nivelAcesso')->get()->pluck('nivelAcesso.nome');

        // 🔐 Se não for Administrador ou Gestor, bloqueia a ação e registra tentativa no log
        if (!$niveisAcesso->intersect(['Administrador', 'Gestor'])->count()) {
            Log::warning("🚨 AUDITORIA - Controller -  O usuário '{$usuario->name}' (ID: {$usuario->id}) tentou atualizar o escritório (ID: {$id}) sem possuir permissão necessária.", [
                'user_id' => $usuario->id,
                'user_nome' => $usuario->name,
                'user_email' => $usuario->email,
                'escritorio_id' => $id,
                'rota' => $request->path(),
                'ip' => $request->ip(),
                'dados_enviados' => $request->all(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para atualizar este escritório.'
            ], 403);
        }

        // 🔍 Busca o escritório que o usuário tem permissão de alterar
        $escritorio = Escritorio::where('id', $id)
            ->where(function ($query) use ($usuario) {
                $query->where('user_id', $usuario->id)
                      ->orWhereHas('membros', function ($q) use ($usuario) {
                          $q->where('user_id', $usuario->id);
                      });
            })
            ->first();

        if (!$escritorio) {
            Log::warning("🚨 AUDITORIA - O usuário '{$usuario->name}' (ID: {$usuario->id}) tentou atualizar um escritório inexistente ou não autorizado (ID: {$id}).", [
                'user_id' => $usuario->id,
                'user_email' => $usuario->email,
                'escritorio_id' => $id,
                'rota' => $request->path(),
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Escritório não encontrado ou sem permissão para edição.'
            ], 404);
        }

        // 📌 Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'nome_escritorio'      => 'required|string|max:255',
            'cnpj_escritorio'      => 'nullable|string|max:18',
            'telefone_escritorio'  => 'nullable|string|max:15',
            'celular_escritorio'   => 'required|string|max:15',
            'email_escritorio'     => 'required|email|max:255',
            'cep_escritorio'       => 'nullable|string|max:9',
            'logradouro_escritorio' => 'nullable|string|max:255',
            'numero_escritorio'    => 'nullable|string|max:10',
            'bairro_escritorio'    => 'nullable|string|max:255',
            'estado_escritorio'    => 'nullable|string|max:2',
            'cidade_escritorio'    => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // 🔹 Converte valores vazios para NULL para evitar falhas
        $data = array_map(fn($value) => $value === "" ? null : $value, $request->all());

        // 🔍 Verifica se houve alterações nos dados
        $alterado = false;
        foreach ($data as $key => $value) {
            if ($escritorio->$key !== $value) {
                $alterado = true;
                break;
            }
        }

        if ($alterado) {
            // 🔥 Atualiza os dados do escritório
            $update = $escritorio->update($data);

            if ($update) {
                DB::commit();
                Log::info("✅ AUDITORIA - O usuário '{$usuario->name}' (ID: {$usuario->id}) atualizou com sucesso o escritório (ID: {$escritorio->id}).", [
                    'escritorio_id' => $escritorio->id,
                    'dados_atualizados' => $data,
                    'user_id' => $usuario->id,
                    'user_email' => $usuario->email,
                    'rota' => $request->path(),
                    'ip' => $request->ip(),
                    'timestamp' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dados do escritório atualizados com sucesso!',
                    'dados'   => $escritorio->fresh()
                ]);
            } else {
                DB::rollBack();
                Log::error("❌ AUDITORIA - Erro ao atualizar os dados do escritório (ID: {$escritorio->id}).", [
                    'user_id' => $usuario->id,
                    'user_email' => $usuario->email,
                    'escritorio_id' => $escritorio->id,
                    'rota' => $request->path(),
                    'ip' => $request->ip(),
                    'timestamp' => now(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao tentar atualizar os dados no banco.',
                ], 500);
            }
        } else {
            Log::warning("⚠️ AUDITORIA - O usuário '{$usuario->name}' (ID: {$usuario->id}) tentou atualizar o escritório (ID: {$escritorio->id}), mas nenhuma alteração foi feita.", [
                'user_id' => $usuario->id,
                'user_email' => $usuario->email,
                'escritorio_id' => $escritorio->id,
                'rota' => $request->path(),
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Nenhuma alteração foi feita nos dados.',
            ], 200);
        }
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ AUDITORIA - Erro crítico ao atualizar o escritório (ID: {$id}).", [
            'error' => $e->getMessage(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'Desconhecido',
            'rota' => $request->path(),
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erro ao atualizar os dados do escritório.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Escritorio $escritorio)
    {
        //
    }
}
