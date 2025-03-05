<?php

namespace App\Http\Controllers;

use App\Mail\CodigoExclusaoMail;
use App\Models\ExclusaoConta;
use App\Models\PerfilLog;
use App\Models\User;
use App\Models\UserData;
use App\Notifications\ExclusaoContaNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('perfil.perfil');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    try {
        $user = User::findOrFail($id);
        $userData = $user->userData;

        // 🔍 Definir caminho padrão da foto
        $fotoPath = asset("storage/foto-perfil/sem-foto.jpg"); // Foto padrão caso não haja uma válida

        if ($userData) {
            try {
                // 🔍 Remove pontuações do CPF para garantir correspondência com o nome do arquivo
                $cpfLimpo = $userData->cpf ? preg_replace('/\D/', '', Crypt::decryptString($userData->cpf)) : null;

                if ($cpfLimpo) {
                    Log::info("📸 Buscando foto para CPF: {$cpfLimpo}");

                    // 🔎 Buscar fotos diretamente na pasta real do sistema de arquivos
                    $fotoDir = storage_path('app/public/foto-perfil');
                    $fotos = File::glob("{$fotoDir}/foto-{$cpfLimpo}-*.*"); // 🔥 Busca correta no diretório

                    // 🔎 Exibir todos os arquivos encontrados no log
                    Log::info("📁 Arquivos encontrados na pasta foto-perfil:", $fotos);

                    // Ordena os arquivos pela data mais recente (do nome do arquivo)
                    usort($fotos, function ($a, $b) {
                        return strcmp($b, $a); // Ordenação decrescente
                    });

                    // 🔎 Exibir as fotos filtradas no log
                    Log::info("📸 Fotos filtradas para o usuário {$cpfLimpo}: ", $fotos);

                    if (!empty($fotos)) {
                        // Obtém a foto mais recente e converte para URL acessível
                        $fotoArquivo = basename($fotos[0]); // Apenas o nome do arquivo
                        $fotoPath = asset("storage/foto-perfil/{$fotoArquivo}");

                        Log::info("✅ Foto encontrada: {$fotoPath}");
                    } else {
                        Log::warning("⚠️ Nenhuma foto encontrada para CPF: {$cpfLimpo}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("❌ Erro ao buscar a foto: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'dados' => [
                'nome_usuario' => $user->name,
                'email_usuario' => $user->email,
                'cpf_usuario' => $userData && $userData->cpf ? Crypt::decryptString($userData->cpf) : null,
                'celular_usuario' => $userData && $userData->celular ? Crypt::decryptString($userData->celular) : null,
                'data_nascimento_usuario' => $userData->data_nascimento ?? null,
                'estado_usuario' => $userData->estado ?? null,
                'cidade_usuario' => $userData->cidade ?? null,
                'oab_usuario' => $userData && $userData->oab ? Crypt::decryptString($userData->oab) : null,
                'estado_oab_usuario' => $userData->estado_oab ?? null,
                'foto_usuario' => $fotoPath, // 🔥 Foto mais recente ou "sem-foto.jpg"
            ],
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Usuário não encontrado.',
        ], 404);
    } catch (\Exception $e) {
        Log::error("❌ Erro ao buscar dados do usuário: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erro ao buscar os dados do usuário.',
            'error' => $e->getMessage(), // Remova em produção
        ], 500);
    }
}










    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    try {
        DB::beginTransaction();

        // Busca o usuário pelo ID
        $user = User::findOrFail($id);
        $userData = $user->userData ?? new UserData(['user_id' => $user->id]);

        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'nome_usuario' => 'required|string|min:3|max:255',
            'email_usuario' => 'required|email|max:255|unique:users,email,' . $user->id,
            'cpf_usuario' => 'required|string|size:14',
            'celular_usuario' => 'required|string|size:15',
            'data_nascimento_usuario' => 'required|date|before:today',
            'estado_usuario' => 'required|size:2',
            'cidade_usuario' => 'required|string|max:255',
            'oab_usuario' => 'nullable|numeric|digits_between:1,8',
            'estado_oab_usuario' => 'nullable|string|size:2',
            'foto_usuario' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Valida imagem até 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Garante que a pasta de fotos existe
        $path = storage_path('app/public/foto-perfil');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        // Captura CPF descriptografado para nome do arquivo
        $cpfLimpo = preg_replace('/\D/', '', $request->input('cpf_usuario'));

        // Salva a foto do usuário, removendo a anterior se existir
        if ($request->hasFile('foto_usuario')) {
            Log::info("Iniciando salvamento de imagem...");
        
            if ($userData->foto && Storage::exists("public/foto-perfil/{$userData->foto}")) {
                Storage::delete("public/foto-perfil/{$userData->foto}");
                Log::info("Imagem antiga removida: " . $userData->foto);
            }
        
            $file = $request->file('foto_usuario');
            $fileName = "foto-{$cpfLimpo}-" . now()->format('YmdHis') . "." . $file->getClientOriginalExtension();
        
            // Salva a imagem corretamente dentro de storage/app/public/foto-perfil/
            $file->move(storage_path('app/public/foto-perfil'), $fileName);
        
            // Garante que o arquivo foi salvo
            if (!file_exists(storage_path("app/public/foto-perfil/{$fileName}"))) {
                Log::error("Erro ao salvar a imagem: " . storage_path("app/public/foto-perfil/{$fileName}"));
                throw new \Exception("Erro ao salvar a imagem.");
            }
        
            Log::info("Imagem salva com sucesso: " . storage_path("app/public/foto-perfil/{$fileName}"));
        
            // Atualiza o campo no banco de dados
            $userData->foto = $fileName;
        }

        // Atualiza os dados do usuário
        $user->update([
            'name' => $request->input('nome_usuario'),
            'email' => $request->input('email_usuario'),
        ]);

        // Atualiza os dados do usuário no userData
        $userData->fill([
            'cpf' => Crypt::encryptString($request->input('cpf_usuario')),
            'celular' => Crypt::encryptString($request->input('celular_usuario')),
            'data_nascimento' => $request->input('data_nascimento_usuario'),
            'estado' => $request->input('estado_usuario'),
            'cidade' => $request->input('cidade_usuario'),
            'oab' => Crypt::encryptString($request->input('oab_usuario')),
            'estado_oab' => $request->input('estado_oab_usuario')
        ])->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Dados atualizados com sucesso!',
            'dados' => [
                'foto_usuario' => $userData->foto ? asset("storage/foto-perfil/{$userData->foto}") : asset("storage/foto-perfil/sem-foto.jpg")
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Erro ao atualizar os dados. Tente novamente mais tarde.',
            'error' => $e->getMessage(),
        ], 500);
    }
}





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Exporta os dados do usuário logado em JSON ou CSV
     */
    public function exportarDados(Request $request)
    {
        try {
            DB::beginTransaction(); // Inicia a transação para garantir consistência

            $user = Auth::user();

            // Garante que o usuário esteja autenticado
            abort_if(!$user, 403, 'Acesso negado.');

            $userData = $user->userData; // Dados adicionais do usuário
            $escritorio = $user->escritorio; // Dados do escritório, se houver

            // Estrutura básica dos dados do usuário
            $dadosUsuario = [
                'ID' => $user->id,
                'Nome' => $user->name,
                'E-mail' => $user->email,
                'Autenticação de Dois Fatores' => $user->two_factor_enabled ? 'Ativada' : 'Desativada',
                'Método de 2FA' => $user->two_factor_type ?? 'Não configurado',
                'Data de Criação' => $user->created_at->format('d/m/Y H:i:s'),
                'Última Atualização' => $user->updated_at->format('d/m/Y H:i:s'),
            ];

            // Adiciona os dados adicionais do usuário, descriptografando os campos necessários
            if ($userData) {
                $dadosUsuario = array_merge($dadosUsuario, [
                    'CPF' => $userData->cpf ? Crypt::decryptString($userData->cpf) : 'Não informado',
                    'Telefone' => $userData->telefone ? Crypt::decryptString($userData->telefone) : 'Não informado',
                    'Celular' => $userData->celular ? Crypt::decryptString($userData->celular) : 'Não informado',
                    'Cidade' => $userData->cidade ?? 'Não informado',
                    'Estado' => $userData->estado ?? 'Não informado',
                    'OAB' => $userData->oab ? Crypt::decryptString($userData->oab) : 'Não informado',
                    'Estado da OAB' => $userData->estado_oab ?? 'Não informado',
                    'Data de Nascimento' => $userData->data_nascimento ? date('d/m/Y', strtotime($userData->data_nascimento)) : 'Não informado',
                ]);
            }

            // Adiciona os dados do escritório, se existirem
            $dadosEscritorio = [];
            if ($escritorio) {
                $dadosEscritorio = [
                    'Nome do Escritório' => $escritorio->nome ?? 'Não informado',
                    'CNPJ' => $escritorio->cnpj ?? 'Não informado',
                    'Telefone' => $escritorio->telefone ?? 'Não informado',
                    'Celular' => $escritorio->celular ?? 'Não informado',
                    'E-mail' => $escritorio->email ?? 'Não informado',
                    'CEP' => $escritorio->cep ?? 'Não informado',
                    'Endereço' => $escritorio->logradouro ?? 'Não informado',
                    'Número' => $escritorio->numero ?? 'Não informado',
                    'Bairro' => $escritorio->bairro ?? 'Não informado',
                    'Cidade' => $escritorio->cidade ?? 'Não informado',
                    'Estado' => $escritorio->estado ?? 'Não informado',
                ];
            }

            // Captura o histórico de logins do usuário
            $historicoLogins = DB::table('sessions')->where('user_id', $user->id)->get()->map(function ($session) {
                return [
                    'IP' => $session->ip_address,
                    'Navegador' => $session->user_agent,
                    'Última Ação' => date('d/m/Y H:i:s', $session->last_activity),
                ];
            });

            DB::commit(); // Confirma a transação

            // Captura o formato desejado (JSON ou CSV)
            $formato = strtolower($request->query('formato', 'json'));
            abort_if(!in_array($formato, ['json', 'csv']), 400, 'Formato inválido.');

            if ($formato === 'csv') {
                return $this->exportarComoCSV($dadosUsuario, $dadosEscritorio, $historicoLogins);
            }

            // Retorna os dados em JSON
            return response()->json([
                'dados_usuario' => $dadosUsuario,
                'dados_escritorio' => $dadosEscritorio,
                'historico_logins' => $historicoLogins
            ], 200, [
                'Content-Disposition' => 'attachment; filename="meus-dados.json"',
                'Content-Type' => 'application/json'
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Desfaz a transação em caso de erro
            Log::error("Erro ao exportar dados: " . $e->getMessage());

            return response()->json(['message' => 'Erro ao exportar os dados.'], 500);
        }
    }



    /**
     * Exporta os dados como CSV
     */
    private function exportarComoCSV($dadosUsuario, $dadosEscritorio, $historicoLogins)
    {
        try {
            $response = new StreamedResponse(function () use ($dadosUsuario, $dadosEscritorio, $historicoLogins) {
                $handle = fopen('php://output', 'w');

                // Escreve cabeçalhos do CSV
                fputcsv($handle, ['Campo', 'Valor']);

                // Escreve os dados do usuário
                foreach ($dadosUsuario as $campo => $valor) {
                    fputcsv($handle, [$campo, $valor]);
                }

                fputcsv($handle, ['']); // Linha em branco para separação

                // Escreve os dados do escritório, se houver
                if (!empty($dadosEscritorio)) {
                    fputcsv($handle, ['Dados do Escritório']);
                    foreach ($dadosEscritorio as $campo => $valor) {
                        fputcsv($handle, [$campo, $valor]);
                    }
                    fputcsv($handle, ['']); // Linha em branco para separação
                }

                // Escreve o histórico de logins
                fputcsv($handle, ['Histórico de Logins']);
                fputcsv($handle, ['IP', 'Navegador', 'Última Ação']);
                foreach ($historicoLogins as $login) {
                    fputcsv($handle, [$login['IP'], $login['Navegador'], $login['Última Ação']]);
                }

                fclose($handle);
            });

            // Define headers apropriados
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="meus-dados.csv"');

            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao exportar CSV: " . $e->getMessage());

            return response()->json(['message' => 'Erro ao gerar o CSV.'], 500);
        }
    }

    public function validarSenhaExclusao(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->senha_confirmacao, $user->password)) {
            return response()->json(['message' => 'Senha incorreta.'], 422);
        }

        // Gerar código e armazenar no cache por 10 minutos
        $codigo = rand(100000, 999999);
        Cache::put("codigo_exclusao_{$user->id}", $codigo, now()->addMinutes(10));

        // Enviar e-mail com o código
        Mail::to($user->email)->send(new CodigoExclusaoMail($codigo));

        return response()->json(['message' => 'Código enviado ao e-mail.'], 200);
    }


    public function excluirConta(Request $request)
    {
        $user = Auth::user();
        $codigoArmazenado = Cache::get("codigo_exclusao_{$user->id}");

        if (!$codigoArmazenado || $codigoArmazenado !== (int) $request->codigo_exclusao) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 422);
        }

        try {
            DB::beginTransaction();

            // Criar e armazenar o CSV antes da exclusão
            $csvPath = $this->generateCsv($user);

            // Verifica se o arquivo foi gerado corretamente antes de enviar o e-mail
            if (file_exists(storage_path("app/" . $csvPath))) {
                Log::info("Arquivo CSV gerado com sucesso: " . $csvPath);
                $user->notify(new ExclusaoContaNotification($csvPath));
            } else {
                Log::error("Arquivo CSV não encontrado: " . $csvPath);
                return response()->json(['message' => 'Erro ao gerar o CSV.'], 500);
            }

            // Registrar a exclusão no banco
            ExclusaoConta::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'data_solicitacao' => now(),
            ]);

            // Excluir usuário
            $user->delete();

            DB::commit();

            return response()->json(['message' => 'Conta excluída e dados enviados por e-mail.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao excluir conta: " . $e->getMessage());
            return response()->json(['message' => 'Erro ao excluir a conta.'], 500);
        }
    }

    public function enviarCsvAntesDeExcluir(Request $request)
    {
        $user = Auth::user();
        $codigoArmazenado = Cache::get("codigo_exclusao_{$user->id}");

        if (!$codigoArmazenado || $codigoArmazenado != $request->codigo_exclusao) {
            return response()->json(['message' => 'Código inválido ou expirado.'], 422);
        }

        try {
            DB::beginTransaction();

            // Criar e enviar o CSV antes da exclusão
            $csvPath = $this->generateCsv($user);
            Mail::to($user->email)->send(new ($csvPath . $user));

            // Registrar a exclusão da conta
            DB::table('exclusoes_conta')->insert([
                'user_id' => $user->id,
                'email' => $user->email,
                'data_solicitacao' => now(),
            ]);

            // Excluir usuário
            $user->delete();

            DB::commit();

            return response()->json(['message' => 'Conta excluída e dados enviados por e-mail.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao excluir a conta.'], 500);
        }
    }

    /**
     * Gera um arquivo CSV com os dados do usuário.
     */
    private function generateCsv($user)
    {
        $csvFileName = "private/exports/user_data_{$user->id}.csv";
        $csvPath = storage_path("app/" . $csvFileName);

        // Abrindo arquivo para escrita
        $handle = fopen($csvPath, 'w+');

        // Escrevendo cabeçalho do CSV
        fputcsv($handle, ['Campo', 'Valor']);

        // Dados básicos do usuário
        fputcsv($handle, ['Nome', $user->name]);
        fputcsv($handle, ['E-mail', $user->email]);
        fputcsv($handle, ['Data de Criação', $user->created_at]);
        fputcsv($handle, ['Autenticação de Dois Fatores', $user->two_factor_enabled ? 'Ativada' : 'Desativada']);
        fputcsv($handle, ['Método de 2FA', $user->two_factor_type ?? 'Não configurado']);

        // Dados adicionais do usuário (userData)
        if ($user->userData) {
            fputcsv($handle, ['CPF', $this->decryptSafe($user->userData->cpf)]);
            fputcsv($handle, ['Telefone', $this->decryptSafe($user->userData->telefone)]);
            fputcsv($handle, ['Celular', $this->decryptSafe($user->userData->celular)]);
            fputcsv($handle, ['Cidade', $user->userData->cidade ?? 'Não informado']);
            fputcsv($handle, ['Estado', $user->userData->estado ?? 'Não informado']);
            fputcsv($handle, ['OAB', $this->decryptSafe($user->userData->oab)]);
            fputcsv($handle, ['Estado da OAB', $user->userData->estado_oab ?? 'Não informado']);
            fputcsv($handle, ['Data de Nascimento', $user->userData->data_nascimento ? date('d/m/Y', strtotime($user->userData->data_nascimento)) : 'Não informado']);
        }

        // Dados do escritório (caso tenha)
        if ($user->escritorio) {
            fputcsv($handle, ['Escritório', 'Dados do Escritório']);
            fputcsv($handle, ['Nome do Escritório', $user->escritorio->nome ?? 'Não informado']);
            fputcsv($handle, ['CNPJ', $user->escritorio->cnpj ?? 'Não informado']);
            fputcsv($handle, ['Telefone', $user->escritorio->telefone ?? 'Não informado']);
            fputcsv($handle, ['Celular', $user->escritorio->celular ?? 'Não informado']);
            fputcsv($handle, ['E-mail', $user->escritorio->email ?? 'Não informado']);
            fputcsv($handle, ['CEP', $user->escritorio->cep ?? 'Não informado']);
            fputcsv($handle, ['Endereço', $user->escritorio->logradouro ?? 'Não informado']);
            fputcsv($handle, ['Número', $user->escritorio->numero ?? 'Não informado']);
            fputcsv($handle, ['Bairro', $user->escritorio->bairro ?? 'Não informado']);
            fputcsv($handle, ['Cidade', $user->escritorio->cidade ?? 'Não informado']);
            fputcsv($handle, ['Estado', $user->escritorio->estado ?? 'Não informado']);
        }

        // Histórico de sessões ativas do usuário
        $activeSessions = $user->activeSessions();
        if ($activeSessions->isNotEmpty()) {
            fputcsv($handle, ['Histórico de Sessões Ativas']);
            fputcsv($handle, ['IP', 'Navegador', 'Última Atividade']);

            foreach ($activeSessions as $session) {
                fputcsv($handle, [$session->ip_address, $session->user_agent, date('d/m/Y H:i:s', $session->last_activity)]);
            }
        }

        fclose($handle);

        return $csvFileName; // Retorna caminho relativo para o Storage
    }

    private function decryptSafe($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : 'Não informado';
        } catch (\Exception $e) {
            return 'Erro ao descriptografar';
        }
    }

    public function historicoAlteracoes()
    {
        $userId = auth()->id();

        $historico = PerfilLog::where('user_id', $userId)
            ->orderBy('alterado_em', 'desc')
            ->get();

        return response()->json($historico);
    }
}
