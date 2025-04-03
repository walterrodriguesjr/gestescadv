<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use App\Models\TipoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServicoController
{
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
        //
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
                'succcess' => true,
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
