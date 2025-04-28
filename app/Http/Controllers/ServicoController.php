<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Models\Agenda;
use App\Models\AndamentoServico;
use App\Models\Escritorio;
use App\Models\Servico;
use App\Models\TipoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ClientePessoaFisica;
use App\Models\ClientePessoaJuridica;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;


class ServicoController
{

    public function listarServicos(Request $request)
    {
        try {
            $tipo = $request->input('tipo');

            if (!in_array($tipo, ['pessoa_fisica', 'pessoa_juridica'])) {
                return response()->json(['message' => 'Tipo de cliente inválido.'], 400);
            }

            $servicos = Servico::with(['tipoServico', 'andamentos'])
                ->where('tipo_cliente', $tipo)
                ->get()
                ->map(function ($servico) {
                    $cliente = $servico->clienteFormatado;

                    $cpfCnpj = $cliente?->cpf
                        ? Crypt::decryptString($cliente->cpf)
                        : ($cliente?->cnpj ? Crypt::decryptString($cliente->cnpj) : null);

                    $celular = $cliente?->celular ? Crypt::decryptString($cliente->celular) : null;

                    return [
                        'id' => $servico->id,
                        'nome' => $cliente?->nome ?? $cliente?->razao_social ?? '—',
                        'cpf_cnpj' => $cpfCnpj ?? '—',
                        'tipo_documento' => $cliente?->cpf ? 'cpf' : ($cliente?->cnpj ? 'cnpj' : null),
                        'celular' => $celular,
                        'status' => $servico->andamentos->last()?->etapa ?? 'Não iniciado',
                    ];
                });

            return response()->json(['data' => $servicos]);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar serviços: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao buscar serviços.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('servico.servico');
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
    $rules = [
        'tipo_servico_id' => 'required|exists:' . (new TipoServico)->getTable() . ',id',
        'tipo_cliente'    => 'required|in:pf,pj',
        'data_inicio'     => 'required|date',
        'numero_processo' => [
            'nullable',
            'regex:/^\d{7}-\d{2}\.\d{4}\.\d\.\d{2}\.\d{4}$/'
        ],
    ];

    $messages = [
        'tipo_servico_id.required' => 'O tipo de serviço é obrigatório.',
        'tipo_servico_id.exists'   => 'Tipo de serviço não encontrado.',
        'tipo_cliente.required'    => 'O tipo de cliente é obrigatório.',
        'tipo_cliente.in'          => 'Tipo de cliente inválido.',
        'data_inicio.required'     => 'A data de início é obrigatória.',
        'numero_processo.regex'    => 'Número de processo fora do padrão CNJ (ex: 0000000-00.0000.0.00.0000).',
    ];

    $tipoCliente = strtolower($request->tipo_cliente);

    if ($tipoCliente === 'pf') {
        $rules['cliente_id'] = 'required|exists:' . (new ClientePessoaFisica)->getTable() . ',id';
        $messages['cliente_id.required'] = 'O cliente é obrigatório.';
        $messages['cliente_id.exists']   = 'Cliente pessoa física não encontrado.';
    } elseif ($tipoCliente === 'pj') {
        $rules['cliente_id'] = 'required|exists:' . (new ClientePessoaJuridica)->getTable() . ',id';
        $messages['cliente_id.required'] = 'O cliente é obrigatório.';
        $messages['cliente_id.exists']   = 'Cliente pessoa jurídica não encontrado.';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Erro de validação.',
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        $usuario = auth()->user();
        if (!$usuario || !$usuario->id) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $escritorio = Escritorio::where('user_id', $usuario->id)->first();
        if (!$escritorio) {
            return response()->json(['message' => 'Usuário sem escritório associado.'], 401);
        }

        $tipoClienteConvertido = match ($tipoCliente) {
            'pf' => 'pessoa_fisica',
            'pj' => 'pessoa_juridica',
            default => null,
        };

        DB::beginTransaction();

        $servico = Servico::create([
            'escritorio_id'   => $escritorio->id,
            'tipo_servico_id' => $request->tipo_servico_id,
            'tipo_cliente'    => $tipoClienteConvertido,
            'cliente_id'      => $request->cliente_id,
            'data_inicio'     => $request->data_inicio,
            'observacoes'     => $request->observacoes,
            'numero_processo' => $request->numero_processo,
        ]);

        $arquivos = $request->file('anexos') ?? [];
        if (!empty($arquivos)) {
            $clienteId = $request->cliente_id;
            $pasta = "arquivos_servicos/servico_{$servico->id}_cliente_{$clienteId}";

            foreach ($arquivos as $index => $arquivo) {
                if ($arquivo->isValid()) {
                    $nomeOriginal = $arquivo->getClientOriginalName();
                    $extensao     = $arquivo->getClientOriginalExtension();
                    $nomeArquivo  = pathinfo($nomeOriginal, PATHINFO_FILENAME)
                                  . '_' . time() . "_{$index}_" . rand(1000, 9999) . ".{$extensao}";
                    $arquivo->storeAs($pasta, $nomeArquivo, 'public');
                }
            }
        }

        AndamentoServico::create([
            'servico_id' => $servico->id,
            'etapa'      => 'Iniciado',
            'data_hora'  => now(),
        ]);

        if ($request->agendar_consulta) {
            $agenda = Agenda::create([
                'escritorio_id'    => $escritorio->id,
                'servico_id'       => $servico->id,
                'tipo_cliente'     => $tipoClienteConvertido,
                'cliente_id'       => $request->cliente_id,
                'data_hora_inicio' => $request->data_hora_inicio,
                'data_hora_fim'    => $request->data_hora_fim,
                'motivo_agenda_id' => $request->motivo_agenda_id,
            ]);

            $nomeMotivo = optional($agenda->motivo)->nome ?? 'Agenda';

            AndamentoServico::create([
                'servico_id' => $servico->id,
                'etapa'      => $nomeMotivo,
                'data_hora'  => Carbon::parse($agenda->data_hora_inicio)->addMinute(),
                'agenda_id'  => $agenda->id,
            ]);
        }

        DB::commit();
        return response()->json(['message' => 'Serviço cadastrado com sucesso.']);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erro ao iniciar serviço: '.$e->getMessage(), [
            'linha'   => $e->getLine(),
            'arquivo' => $e->getFile(),
            'trace'   => $e->getTraceAsString()
        ]);
        return response()->json(['message' => 'Erro ao iniciar o serviço.'], 500);
    }
}







    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function listar_tipo_servico($id)
    {
        try {
            /* verifica se o id é válido */
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID do escritório inválido',
                    'error' => 'O ID fornecido não é um número válido'
                ], 400);
            }

            /* busca os tipos de serviços do escritório em questà0 */
            $tipoServicos = TipoServico::where('escritorio_id', $id)->get();

            /* verifica se encontrou resultado */
            if ($tipoServicos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum tipo de serviço encontrado',
                    'data' => []
                ], 404);
            }

            /* retornar os dados encontrados */
            return response()->json([
                'success' => true,
                'message' => 'Tipos de serviços recuperados com sucesso',
                'data' => $tipoServicos
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Erro específico de consulta ao banco de dados
            Log::error('Erro de consulta ao banco de dados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar o banco de dados',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        } catch (\Exception $e) {
            // Erro genérico
            Log::error('Erro ao listar tipos de serviço: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a solicitação',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    public function buscar_tipo_servicos($id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID inválido.',
                ], 400);
            }

            $tipoServico = TipoServico::find($id);

            if (!$tipoServico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum tipo de serviço encontrado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tipo de serviço encontrado com sucesso',
                'data' => $tipoServico
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar tipo de serviço: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao buscar o tipo de serviço',
            ], 500);
        }
    }

    public function cadastrar_tipo_servico(Request $request, $id)
    {
        try {
            // Validações
            $dados = $request->validate([
                'nome_servico' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[A-Za-zÀ-ú0-9\s]+$/'],
            ]);

            $nomeServicoNormalizado = StringHelper::normalizar($dados['nome_servico']);

            // Busca serviços existentes para esse escritório
            $servicosExistentes = TipoServico::where('escritorio_id', $id)->get();

            foreach ($servicosExistentes as $servico) {
                $normalizadoExistente = StringHelper::normalizar($servico->nome_servico);

                // Igualdade exata
                if ($normalizadoExistente === $nomeServicoNormalizado) {
                    return response()->json([
                        'message' => 'Já existe um serviço igual cadastrado: ' . $servico->nome_servico
                    ], 422);
                }

                // Similaridade textual (ex: "civel" vs "cível")
                similar_text($nomeServicoNormalizado, $normalizadoExistente, $percentual);
                if ($percentual >= 85) {
                    return response()->json([
                        'message' => "Já existe um serviço muito similar cadastrado: {$servico->nome_servico} ({$percentual}% semelhante)."
                    ], 422);
                }
            }


            // Capitaliza a primeira letra antes de salvar
            $dados['nome_servico'] = ucfirst(mb_strtolower($dados['nome_servico'], 'UTF-8'));
            $dados['escritorio_id'] = $id;

            TipoServico::create($dados);

            return response()->json(['message' => 'Serviço cadastrado com sucesso!'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar serviço: ' . $e->getMessage());

            return response()->json([
                'message' => 'Erro interno ao cadastrar serviço.'
            ], 500);
        }
    }

    public function atualizar_tipo_servico(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID inválido.',
                ], 400);
            }

            $request->validate([
                'nome_servico' => 'required|string|min:3|max:100'
            ]);

            $novoNome = trim($request->nome_servico);

            // Normalização para comparação mais robusta
            $nomeNormalizado = StringHelper::normalizar($novoNome);

            // Verifica se já existe serviço similar
            $servicos = TipoServico::where('id', '!=', $id)->get();
            foreach ($servicos as $servico) {
                similar_text($nomeNormalizado, StringHelper::normalizar($servico->nome_servico), $percentual);
                if ($percentual > 85) {
                    return response()->json([
                        'success' => false,
                        'message' => "Já existe um tipo de serviço semelhante cadastrado: {$servico->nome_servico} ({$percentual}% similar)."
                    ], 422);
                }
            }

            $tipoServico = TipoServico::find($id);

            if (!$tipoServico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de serviço não encontrado.'
                ], 404);
            }

            $tipoServico->nome_servico = ucfirst(mb_strtolower($novoNome));
            $tipoServico->save();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de serviço atualizado com sucesso.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->validator->getMessageBag()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar tipo de serviço: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao atualizar o tipo de serviço.'
            ], 500);
        }
    }

    public function deletar_tipo_servico($id)
    {
        try {
            $tipoServico = TipoServico::findOrFail($id);

            // Exemplo de proteção futura
            // if ($tipoServico->servicos()->exists()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Este tipo de serviço está vinculado a serviços e não pode ser excluído.'
            //     ], 400);
            // }

            $tipoServico->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Tipo de serviço desativado com sucesso.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de serviço não encontrado.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar tipo de serviço: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a solicitação.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno.'
            ], 500);
        }
    }
}
