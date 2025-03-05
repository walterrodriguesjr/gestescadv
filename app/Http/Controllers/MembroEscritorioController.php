<?php

namespace App\Http\Controllers;

use App\Mail\ConviteMembroEscritorioMail;
use App\Models\Escritorio;
use App\Models\MembroEscritorio;
use App\Models\PermissaoUsuario;
use App\Models\User;
use App\Models\UserData;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MembroEscritorioController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function reenviarConvite($id)
    {
        try {
            // 🔍 Busca o membro pelo ID
            $membro = MembroEscritorio::findOrFail($id);
            $usuario = $membro->usuario;

            if (!$usuario) {
                Log::warning("⚠️ Usuário do membro ID {$id} não encontrado.");
                return response()->json(['success' => false, 'message' => 'Usuário não encontrado.'], 404);
            }

            // 🔍 Verifica se o usuário ainda está pendente
            if ($membro->status !== 'pendente') {
                return response()->json(['success' => false, 'message' => 'Este usuário já está ativo ou inativo.'], 400);
            }

            // 🔥 Gera um novo token para redefinição de senha
            $token = Password::getRepository()->create($usuario);

            // 🔗 Gera o link de redefinição de senha
            $resetLink = url("/reset-password/{$token}");

            // 📩 Reenvia o e-mail usando o mesmo modelo
            Mail::to($usuario->email)->send(new \App\Mail\ConviteMembroEscritorioMail($usuario, $token));

            Log::info("✅ Convite reenviado para {$usuario->email}.");

            return response()->json(['success' => true, 'message' => 'Convite reenviado com sucesso!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Membro não encontrado.'], 404);
        } catch (\Exception $e) {
            Log::error("❌ Erro ao reenviar convite: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao reenviar convite.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // 🔥 Log do recebimento dos dados
            Log::info('Recebendo dados para cadastro de membro', $request->all());

            // Validação
            $request->validate([
                'nome_membro'         => 'required|string|max:255',
                'cpf_membro'          => 'required|string|max:14',
                'email_membro'        => 'required|email|unique:users,email|max:255',
                'nivel_acesso_membro' => 'required|exists:niveis_acesso,id',
            ]);

            Log::info('Dados validados com sucesso.');

            // Criar usuário
            $user = User::create([
                'name'     => $request->nome_membro,
                'email'    => $request->email_membro,
                'password' => bcrypt(uniqid()), // Define senha temporária
            ]);

            Log::info('Usuário criado', ['user_id' => $user->id]);

            // ✅ Criar UserData e criptografar corretamente
            UserData::create([
                'user_id'          => $user->id,
                'cpf'              => $request->cpf_membro ? Crypt::encryptString($request->cpf_membro) : null,
                'telefone'         => $request->telefone_membro ? Crypt::encryptString($request->telefone_membro) : null,
                'celular'          => $request->celular_membro ? Crypt::encryptString($request->celular_membro) : null,
                'cidade'           => $request->cidade_membro ?? null,
                'estado'           => $request->estado_membro ?? null,
                'oab'              => $request->oab_membro ? Crypt::encryptString($request->oab_membro) : null,
                'estado_oab'       => $request->estado_oab_membro ?? null,
                'data_nascimento'  => $request->data_nascimento_membro ?? null, // Mantém sem criptografia
            ]);

            Log::info('UserData criado com criptografia', ['user_id' => $user->id]);

            // Criar MembroEscritorio
            $membro = MembroEscritorio::create([
                'user_id'       => $user->id,
                'escritorio_id' => Auth::user()->escritorio->id,
                'gestor_id'     => Auth::id(),
            ]);

            Log::info('MembroEscritorio criado', ['membro_id' => $membro->id]);

            // Criar PermissaoUsuario
            PermissaoUsuario::create([
                'usuario_id'     => $user->id,
                'nivel_acesso_id' => $request->nivel_acesso_membro,
                'escritorio_id'  => Auth::user()->escritorio->id,
                'concedente_id'  => Auth::id(),
            ]);

            Log::info('PermissaoUsuario criado', ['user_id' => $user->id]);

            // Gerar token de redefinição de senha
            $token = Password::createToken($user);
            $link  = route('password.reset', ['token' => $token]);

            Log::info('Token de redefinição gerado', ['user_id' => $user->id, 'token' => $token]);

            // Enviar e-mail para o novo usuário
            Mail::send('emails.bem_vindo', compact('user', 'link'), function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Bem-vindo ao Escritório - Redefina sua senha');
            });

            Log::info('E-mail de boas-vindas enviado para ' . $user->email);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Membro cadastrado com sucesso! Um e-mail foi enviado para redefinir a senha.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar membro', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar membro.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($escritorioId)
    {
        try {
            $usuario = Auth::user();

            Log::info("🔍 Buscando membros para o escritório ID: {$escritorioId}, usuário ID: {$usuario->id}");

            // Valida se o escritório pertence ao usuário
            $escritorio = Escritorio::where('id', $escritorioId)
                ->where(function ($query) use ($usuario) {
                    $query->where('user_id', $usuario->id)
                        ->orWhereHas('membros', function ($q) use ($usuario) {
                            $q->where('user_id', $usuario->id);
                        });
                })->first();

            if (!$escritorio) {
                Log::warning("⚠️ Escritório ID {$escritorioId} não encontrado ou usuário sem permissão.");
                return response()->json(['success' => false, 'message' => 'Sem permissão.'], 403);
            }

            Log::info("✅ Escritório encontrado. Buscando membros...");

            $membros = MembroEscritorio::where('escritorio_id', $escritorioId)
                ->with(['usuario.nivelAcesso', 'usuario.userData'])
                ->get()
                ->map(function ($membro) {
                    $userData = $membro->usuario->userData ?? null;

                    // 🔐 Descriptografando apenas os campos criptografados
                    $cpf = null;
                    $telefone = null;
                    $celular = null;
                    $oab = null;

                    try {
                        $cpf = $userData && $userData->cpf ? Crypt::decryptString($userData->cpf) : null;
                    } catch (\Exception $e) {
                        Log::error("❌ Erro ao descriptografar CPF do usuário {$membro->usuario->name}: " . $e->getMessage());
                    }

                    try {
                        $telefone = $userData && $userData->telefone ? Crypt::decryptString($userData->telefone) : null;
                    } catch (\Exception $e) {
                        Log::error("❌ Erro ao descriptografar TELEFONE do usuário {$membro->usuario->name}: " . $e->getMessage());
                    }

                    try {
                        $celular = $userData && $userData->celular ? Crypt::decryptString($userData->celular) : null;
                    } catch (\Exception $e) {
                        Log::error("❌ Erro ao descriptografar CELULAR do usuário {$membro->usuario->name}: " . $e->getMessage());
                    }

                    try {
                        $oab = $userData && $userData->oab ? Crypt::decryptString($userData->oab) : null;
                    } catch (\Exception $e) {
                        Log::error("❌ Erro ao descriptografar OAB do usuário {$membro->usuario->name}: " . $e->getMessage());
                    }

                    // 📷 **Buscar a foto mais recente do usuário**
                    $fotoPath = asset("storage/foto-perfil/sem-foto.jpg"); // Foto padrão
                    if ($userData && $cpf) {
                        try {
                            $cpfLimpo = preg_replace('/\D/', '', $cpf); // Remove pontuações do CPF

                            // 🔍 Buscar fotos diretamente no sistema de arquivos
                            $fotoDir = storage_path('app/public/foto-perfil');
                            $fotos = File::glob("{$fotoDir}/foto-{$cpfLimpo}-*.*");

                            Log::info("📁 Fotos encontradas para CPF {$cpfLimpo}:", $fotos);

                            // Ordena as fotos pela data mais recente
                            usort($fotos, function ($a, $b) {
                                return strcmp($b, $a); // Ordenação decrescente
                            });

                            if (!empty($fotos)) {
                                $fotoArquivo = basename($fotos[0]); // Apenas o nome do arquivo
                                $fotoPath = asset("storage/foto-perfil/{$fotoArquivo}");

                                Log::info("✅ Foto mais recente encontrada para {$membro->usuario->name}: {$fotoPath}");
                            } else {
                                Log::warning("⚠️ Nenhuma foto encontrada para CPF: {$cpfLimpo}");
                            }
                        } catch (\Exception $e) {
                            Log::error("❌ Erro ao buscar foto do usuário {$membro->usuario->name}: " . $e->getMessage());
                        }
                    }

                    // 🕒 **Verifica se o token do convite está expirado**
                    $tokenExpirado = false;
                    if ($membro->status === "pendente") {
                        $resetToken = DB::table('password_reset_tokens')
                            ->where('email', $membro->usuario->email)
                            ->orderByDesc('created_at')
                            ->first();

                        if ($resetToken) {
                            $expiracaoPadrao = config('auth.passwords.users.expire', 60); // Tempo em minutos (padrão: 60 min)
                            $tokenCriadoEm = Carbon::parse($resetToken->created_at);
                            $tokenExpirado = $tokenCriadoEm->addMinutes($expiracaoPadrao)->isPast();

                            Log::info("🕒 Token encontrado para {$membro->usuario->email}. Criado em: {$tokenCriadoEm}. Expira em {$expiracaoPadrao} minutos. Expirado: " . ($tokenExpirado ? 'Sim' : 'Não'));
                        } else {
                            $tokenExpirado = true;
                            Log::warning("⚠️ Nenhum token encontrado para {$membro->usuario->email}. Considerando expirado.");
                        }
                    }

                    $dadosFormatados = [
                        'id'              => $membro->id,
                        'nome'            => $membro->usuario->name ?? 'Desconhecido',
                        'email'           => $membro->usuario->email ?? 'Sem email',
                        'nivel_acesso'    => $membro->usuario->nivelAcesso->nome ?? 'Removido',
                        'status'          => $membro->status,

                        // 🔓 Campos descriptografados
                        'cpf'             => $cpf ?? 'Não informado',
                        'telefone'        => $telefone ?? 'Não informado',
                        'celular'         => $celular ?? 'Não informado',
                        'oab'             => $oab ?? 'Não informado',

                        // 🔥 Campos normais (não criptografados)
                        'cidade'          => $userData->cidade ?? 'Não informado',
                        'estado'          => $userData->estado ?? 'Não informado',
                        'estado_oab'      => $userData->estado_oab ?? 'Não informado',
                        'data_nascimento' => $userData->data_nascimento ?? 'Não informado',
                        'foto'            => $fotoPath, // 🔥 Foto mais recente ou padrão

                        // 🕒 **Retorna se o token está expirado**
                        'token_expirado'  => $tokenExpirado,
                    ];

                    Log::info("✅ Dados processados do membro {$membro->usuario->name}", $dadosFormatados);

                    return $dadosFormatados;
                });

            Log::info("✅ Membros encontrados:", ['membros' => $membros]);

            return response()->json(['success' => true, 'data' => $membros]);
        } catch (\Exception $e) {
            Log::error("❌ Erro ao carregar membros: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar membros.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }






    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MembroEscritorio $membroEscritorio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MembroEscritorio $membroEscritorio)
    {
        try {
            DB::beginTransaction(); // Inicia a transação

            // Buscar o usuário associado ao membro
            $usuario = User::find($membroEscritorio->user_id);
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado.',
                ], 404);
            }

            // Buscar os dados do usuário (user_data)
            $userData = UserData::where('user_id', $usuario->id)->first();

            // Validação dos dados recebidos
            $validator = Validator::make($request->all(), [
                'nome'         => 'required|string|max:255',
                'email'        => 'required|email|max:255|unique:users,email,' . $usuario->id,
                'nivelAcesso'  => 'required|string|in:Funcionário,Estagiário,Administrador',
                'cpf'          => 'required|string|size:14|unique:user_data,cpf,' . optional($userData)->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Atualizar os dados do usuário (tabela users)
            $usuario->update([
                'name'  => $request->nome,
                'email' => $request->email,
            ]);

            // Atualizar os dados do usuário na tabela `user_data`
            if ($userData) {
                $userData->update([
                    'cpf' => Crypt::encryptString($request->cpf),
                ]);
            } else {
                // Criar user_data caso não exista
                UserData::create([
                    'user_id' => $usuario->id,
                    'cpf' => Crypt::encryptString($request->cpf),
                ]);
            }

            // Atualizar o nível de acesso do membro
            $membroEscritorio->update([
                'nivel_acesso' => $request->nivelAcesso,
            ]);

            DB::commit(); // Confirma a transação

            Log::info("✅ Membro atualizado com sucesso", ['membro_id' => $membroEscritorio->id]);

            return response()->json([
                'success' => true,
                'message' => 'Membro atualizado com sucesso!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Reverte a transação em caso de erro
            Log::error("❌ Erro ao atualizar membro", ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o membro. Por favor, tente novamente.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembroEscritorio $membroEscritorio)
    {
        try {
            Log::info("🗑️ Tentando deletar membro ID: {$membroEscritorio->id}");

            // ⚠️ Impede a exclusão se o membro não estiver INATIVO
            if ($membroEscritorio->status !== 'inativo') {
                Log::warning("🚫 Tentativa de exclusão falhou: Membro ID {$membroEscritorio->id} ainda está ativo ou pendente.");
                return response()->json([
                    'success' => false,
                    'message' => 'Só é possível excluir membros inativos.'
                ], 400);
            }

            // 🔍 Deleta também os dados do usuário relacionados
            $usuario = $membroEscritorio->usuario;
            if ($usuario) {
                Log::info("🗑️ Deletando usuário associado ID: {$usuario->id}");

                // Excluir `user_data` (dados adicionais)
                if ($usuario->userData) {
                    $usuario->userData->delete();
                    Log::info("✅ Dados adicionais do usuário excluídos.");
                }

                // Excluir o próprio usuário
                $usuario->delete();
                Log::info("✅ Usuário excluído com sucesso.");
            }

            // Finalmente, remove o membro do escritório
            $membroEscritorio->delete();
            Log::info("✅ Membro ID {$membroEscritorio->id} excluído do escritório.");

            return response()->json([
                'success' => true,
                'message' => 'Membro excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Erro ao excluir membro: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir o membro. Tente novamente mais tarde.'
            ], 500);
        }
    }


    public function suspender($id)
    {
        try {
            DB::beginTransaction();

            $usuarioLogado = Auth::user();

            Log::info("🚨 Tentativa de suspensão do membro ID: {$id} pelo usuário ID: {$usuarioLogado->id}");

            // 🔎 Busca o membro do escritório
            $membro = MembroEscritorio::findOrFail($id);
            $usuarioMembro = User::findOrFail($membro->user_id);

            // ✅ Alterando o status do membro para "inativo"
            $membro->update(['status' => 'inativo']);
            Log::info("✅ Status do membro atualizado para 'inativo'.", ['membro_id' => $membro->id]);

            // ✅ Soft Delete na permissão do usuário (removendo-a sem apagar)
            PermissaoUsuario::where('usuario_id', $usuarioMembro->id)->delete();
            Log::info("✅ Permissão do usuário suspensa (Soft Delete).", ['user_id' => $usuarioMembro->id]);

            // ❌ Remove qualquer token de redefinição de senha ativo desse usuário
            DB::table('password_reset_tokens')->where('email', $usuarioMembro->email)->delete();
            Log::info("✅ Token de redefinição de senha removido.", ['user_id' => $usuarioMembro->id]);

            // 🔥 Opcional: Destruir todas as sessões ativas do usuário suspenso
            DB::table('sessions')->where('user_id', $usuarioMembro->id)->delete();
            Log::info("✅ Sessões do usuário suspenso removidas.", ['user_id' => $usuarioMembro->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Membro suspenso com sucesso!",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Erro ao suspender membro: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Erro ao suspender membro.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reativar($id)
    {
        try {
            DB::beginTransaction();

            $usuarioLogado = Auth::user();

            Log::info("🚀 Tentativa de reativação do membro ID: {$id} pelo usuário ID: {$usuarioLogado->id}");

            // 🔎 Busca o membro inativo
            $membro = MembroEscritorio::withTrashed()->findOrFail($id);
            $usuarioMembro = User::findOrFail($membro->user_id);

            if ($membro->status === 'ativo') {
                return response()->json([
                    'success' => false,
                    'message' => "Este membro já está ativo.",
                ], 400);
            }

            // ✅ Define o novo status como "pendente" para obrigar a redefinição de senha
            $membro->update(['status' => 'pendente']);
            Log::info("✅ Status do membro atualizado para 'pendente'.", ['membro_id' => $membro->id]);

            // 🔥 Define uma senha temporária criptografada (usuário **não saberá** essa senha)
            $senhaTemporaria = bcrypt(uniqid()); // Senha aleatória única
            $usuarioMembro->update(['password' => $senhaTemporaria]);
            Log::info("❌ Senha antiga do usuário foi invalidada e substituída por senha temporária.", ['user_id' => $usuarioMembro->id]);

            // 🔥 Reativa a permissão do usuário com os dados do último soft-deleted
            $ultimaPermissao = PermissaoUsuario::onlyTrashed()
                ->where('usuario_id', $usuarioMembro->id)
                ->where('escritorio_id', $membro->escritorio_id)
                ->latest('deleted_at')
                ->first();

            if ($ultimaPermissao) {
                PermissaoUsuario::create([
                    'usuario_id'      => $usuarioMembro->id,
                    'nivel_acesso_id' => $ultimaPermissao->nivel_acesso_id,
                    'escritorio_id'   => $membro->escritorio_id,
                    'concedente_id'   => $usuarioLogado->id,
                ]);
                Log::info("✅ Permissão restaurada para usuário ID: {$usuarioMembro->id}");
            } else {
                Log::warning("⚠️ Nenhuma permissão anterior encontrada para usuário ID: {$usuarioMembro->id}");
            }

            // 🔥 Remove qualquer token anterior antes de criar um novo
            DB::table('password_reset_tokens')->where('email', $usuarioMembro->email)->delete();

            // 🔥 Gera um novo token para redefinição de senha
            $token = Password::createToken($usuarioMembro);
            Log::info("✅ Novo token de redefinição gerado para reativação do membro.", ['user_id' => $usuarioMembro->id]);

            // 📩 Envia e-mail para redefinir a senha
            Mail::to($usuarioMembro->email)->send(new ConviteMembroEscritorioMail($usuarioMembro, $token));
            Log::info("📧 E-mail enviado para redefinição de senha do usuário reativado.", ['user_id' => $usuarioMembro->id]);

            // 🔥 Remove qualquer sessão ativa deletada anteriormente
            DB::table('sessions')->where('user_id', $usuarioMembro->id)->delete();
            Log::info("✅ Sessões antigas do usuário removidas.", ['user_id' => $usuarioMembro->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Membro reativado! Um novo e-mail foi enviado para redefinição de senha.",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Erro ao reativar membro: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Erro ao reativar membro.",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
