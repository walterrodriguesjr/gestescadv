<?php

namespace App\Http\Controllers;

use App\Models\ClientePessoaFisica;
use App\Models\ClientePessoaJuridica;
use App\Models\Escritorio;
use App\Models\MembroEscritorio;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cliente.cliente');
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
        DB::beginTransaction();

        try {
            Log::info('Iniciando cadastro de cliente...', ['dados_recebidos' => $request->all()]);

            $user = Auth::user();
            $escritorio = Escritorio::where('user_id', $user->id)->first();

            if (!$escritorio) {
                Log::error('Usuário não pertence a nenhum escritório.');
                return response()->json(['message' => 'Erro ao cadastrar cliente. Escritório não encontrado.'], 400);
            }

            if ($request->tipo_cliente === 'pessoa_fisica') {
                Log::info('Validando dados para Pessoa Física');

                $validatedData = $request->validate([
                    'nome' => 'required|string|max:255',
                    'cpf' => 'required|string|max:20',
                    'email' => 'required|email|max:255',
                    'celular' => 'required|string|max:20',
                    'cep' => 'required|string|max:10',
                    'logradouro' => 'nullable|string|max:255',
                    'numero' => 'nullable|string|max:10',
                    'bairro' => 'nullable|string|max:255',
                    'cidade' => 'nullable|string|max:255',
                    'estado' => 'nullable|string|max:255',
                ]);

                // Verifica se CPF já existe no mesmo escritório
                $cpfExists = ClientePessoaFisica::where('escritorio_id', $escritorio->id)
                    ->get()
                    ->filter(fn($cliente) => Crypt::decryptString($cliente->cpf) === $validatedData['cpf'])
                    ->first();

                if ($cpfExists) {
                    Log::warning('CPF já cadastrado para este escritório', ['cpf' => $validatedData['cpf']]);
                    return response()->json(['message' => 'CPF já cadastrado neste escritório.'], 409);
                }

                $cliente = ClientePessoaFisica::create([
                    'escritorio_id' => $escritorio->id,
                    'nome' => $validatedData['nome'],
                    'cpf' => Crypt::encryptString($validatedData['cpf']),
                    'email' => Crypt::encryptString($validatedData['email']),
                    'celular' => Crypt::encryptString($validatedData['celular']),
                    'cep' => Crypt::encryptString($validatedData['cep']),
                    'logradouro' => Crypt::encryptString($validatedData['logradouro'] ?? ''),
                    'numero' => Crypt::encryptString($validatedData['numero'] ?? ''),
                    'bairro' => Crypt::encryptString($validatedData['bairro'] ?? ''),
                    'cidade' => Crypt::encryptString($validatedData['cidade'] ?? ''),
                    'estado' => Crypt::encryptString($validatedData['estado'] ?? ''),
                ]);

                Log::info('Cliente Pessoa Física cadastrado com sucesso!', ['cliente_id' => $cliente->id]);
            } elseif ($request->tipo_cliente === 'pessoa_juridica') {
                Log::info('Validando dados para Pessoa Jurídica');

                $validatedData = $request->validate([
                    'razao_social' => 'required|string|max:255',
                    'nome_fantasia' => 'nullable|string|max:255',
                    'cnpj' => 'required|string|max:20',
                    'email' => 'required|email|max:255',
                    'telefone' => 'nullable|string|max:20',
                    'celular' => 'nullable|string|max:20',
                    'cep' => 'required|string|max:10',
                    'logradouro' => 'required|string|max:255',
                    'numero' => 'nullable|string|max:10',
                    'bairro' => 'nullable|string|max:255',
                    'cidade' => 'nullable|string|max:255',
                    'estado' => 'nullable|string|max:255',
                ]);

                // Verifica se CNPJ já existe no mesmo escritório
                $cnpjExists = ClientePessoaJuridica::where('escritorio_id', $escritorio->id)
                    ->get()
                    ->filter(fn($cliente) => Crypt::decryptString($cliente->cnpj) === $validatedData['cnpj'])
                    ->first();

                if ($cnpjExists) {
                    Log::warning('CNPJ já cadastrado para este escritório', ['cnpj' => $validatedData['cnpj']]);
                    return response()->json(['message' => 'CNPJ já cadastrado neste escritório.'], 409);
                }

                $cliente = ClientePessoaJuridica::create([
                    'escritorio_id' => $escritorio->id,
                    'razao_social' => $validatedData['razao_social'],
                    'nome_fantasia' => $validatedData['nome_fantasia'] ?? null,
                    'cnpj' => Crypt::encryptString($validatedData['cnpj']),
                    'email' => Crypt::encryptString($validatedData['email']),
                    'telefone' => $validatedData['telefone'] ? Crypt::encryptString($validatedData['telefone']) : null,
                    'celular' => $validatedData['celular'] ? Crypt::encryptString($validatedData['celular']) : null,
                    'cep' => Crypt::encryptString($validatedData['cep']),
                    'logradouro' => Crypt::encryptString($validatedData['logradouro']),
                    'numero' => $validatedData['numero'] ? Crypt::encryptString($validatedData['numero']) : null,
                    'bairro' => $validatedData['bairro'] ? Crypt::encryptString($validatedData['bairro']) : null,
                    'cidade' => $validatedData['cidade'] ? Crypt::encryptString($validatedData['cidade']) : null,
                    'estado' => $validatedData['estado'] ? Crypt::encryptString($validatedData['estado']) : null,
                ]);

                Log::info('Cliente Pessoa Jurídica cadastrado com sucesso!', ['cliente_id' => $cliente->id]);
            } else {
                Log::error('Tipo de cliente inválido.', ['tipo_cliente' => $request->tipo_cliente]);
                return response()->json(['message' => 'Erro ao cadastrar cliente. Tipo de cliente inválido.'], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Cliente cadastrado com sucesso!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar cliente', ['exception' => $e->getMessage()]);
            return response()->json(['message' => 'Erro ao cadastrar cliente.'], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $tipoCliente)
    {
        $escritorioId = $request->query('escritorio_id');

        Log::info('Buscando clientes...', ['tipo_cliente' => $tipoCliente, 'escritorio_id' => $escritorioId]);

        try {
            if ($tipoCliente === 'pessoa_fisica') {
                $clientes = ClientePessoaFisica::where('escritorio_id', $escritorioId)->get()
                    ->map(function ($cliente) {
                        return [
                            'id' => $cliente->id,
                            'tipo_cliente' => 'pessoa_fisica',
                            'nome' => $cliente->nome,
                            'cpf' => $cliente->cpf ? Crypt::decryptString($cliente->cpf) : null,
                            'email' => $cliente->email ? Crypt::decryptString($cliente->email) : null,
                            'celular' => $cliente->celular ? Crypt::decryptString($cliente->celular) : null,
                            'telefone' => $cliente->telefone ? Crypt::decryptString($cliente->telefone) : null,
                            'cep' => $cliente->cep ? Crypt::decryptString($cliente->cep) : null,
                            'logradouro' => $cliente->logradouro ? Crypt::decryptString($cliente->logradouro) : null,
                            'numero' => $cliente->numero ? Crypt::decryptString($cliente->numero) : null,
                            'bairro' => $cliente->bairro ? Crypt::decryptString($cliente->bairro) : null,
                            'cidade' => $cliente->cidade ? Crypt::decryptString($cliente->cidade) : null,
                            'estado' => $cliente->estado ? Crypt::decryptString($cliente->estado) : null,
                        ];
                    });
            }  elseif ($tipoCliente === 'pessoa_juridica') {
                $clientes = ClientePessoaJuridica::where('escritorio_id', $escritorioId)->get()
                    ->map(function ($cliente) {
                        return [
                            'id' => $cliente->id,
                            'tipo_cliente' => 'pessoa_juridica',
                            'razao_social' => $cliente->razao_social,
                            'nome_fantasia' => $cliente->nome_fantasia,
                            'cnpj' => $cliente->cnpj ? Crypt::decryptString($cliente->cnpj) : null,
                            'email' => $cliente->email ? Crypt::decryptString($cliente->email) : null,
                            'telefone' => $cliente->telefone ? Crypt::decryptString($cliente->telefone) : null,
                            'celular' => $cliente->celular ? Crypt::decryptString($cliente->celular) : null,
                            'cep' => $cliente->cep ? Crypt::decryptString($cliente->cep) : null,
                            'logradouro' => $cliente->logradouro ? Crypt::decryptString($cliente->logradouro) : null,
                            'numero' => $cliente->numero ? Crypt::decryptString($cliente->numero) : null,
                            'bairro' => $cliente->bairro ? Crypt::decryptString($cliente->bairro) : null,
                            'cidade' => $cliente->cidade ? Crypt::decryptString($cliente->cidade) : null,
                            'estado' => $cliente->estado ? Crypt::decryptString($cliente->estado) : null,
                        ];
                    });
            } else {
                Log::error('Tipo de cliente inválido na busca.', ['tipo_cliente' => $tipoCliente]);
                return response()->json(['message' => 'Tipo de cliente inválido.'], 400);
            }

            return response()->json(['data' => $clientes]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar clientes', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Erro ao obter a lista de clientes.'], 500);
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
        DB::beginTransaction();

        try {
            Log::info('Iniciando atualização de cliente...', [
                'dados_recebidos' => $request->all()
            ]);

            // 1) Obtém o usuário logado e o Escritório associado
            $user = Auth::user();
            $escritorio = Escritorio::where('user_id', $user->id)->first();

            if (!$escritorio) {
                Log::error('Usuário não pertence a nenhum escritório.');
                return response()->json([
                    'message' => 'Erro ao atualizar cliente. Escritório não encontrado.'
                ], 400);
            }

            // 2) Verificamos qual tipo de cliente
            if ($request->tipo_cliente === 'pessoa_fisica') {

                // (A) Validação para PF
                $validatedData = $request->validate([
                    'nome'       => 'required|string|max:255',
                    'cpf'        => 'required|string|max:20',
                    'email'      => 'required|email|max:255',
                    'celular'    => 'required|string|max:20',
                    'telefone'   => 'nullable|string|max:20',

                    'cep'        => 'required|string|max:10',
                    'logradouro' => 'nullable|string|max:255',
                    'numero'     => 'nullable|string|max:10',
                    'bairro'     => 'nullable|string|max:255',
                    'cidade'     => 'nullable|string|max:255',
                    'estado'     => 'nullable|string|max:255',
                ]);

                // (B) Localiza o cliente PF no mesmo escritório
                $cliente = ClientePessoaFisica::where('escritorio_id', $escritorio->id)->find($id);

                if (!$cliente) {
                    Log::warning("Cliente PF não encontrado para update.", ['id' => $id]);
                    return response()->json(['message' => 'Cliente não encontrado.'], 404);
                }

                // (C) Verifica se CPF já existe em outro registro
                $outroCpf = ClientePessoaFisica::where('escritorio_id', $escritorio->id)
                    ->where('id', '!=', $cliente->id)
                    ->get()
                    ->filter(fn($c) => Crypt::decryptString($c->cpf) === $validatedData['cpf'])
                    ->first();

                if ($outroCpf) {
                    Log::warning('CPF já cadastrado para outro cliente neste escritório.', [
                        'cpf' => $validatedData['cpf'],
                        'cliente_id' => $outroCpf->id
                    ]);
                    return response()->json([
                        'message' => 'CPF já cadastrado para outro cliente neste escritório.'
                    ], 409);
                }

                // (D) Atualiza dados (criptografa, evitando erro caso as chaves não existam)
                $cliente->update([
                    'nome'    => $validatedData['nome'],
                    'cpf'     => Crypt::encryptString($validatedData['cpf']),
                    'email'   => Crypt::encryptString($validatedData['email']),
                    'celular' => Crypt::encryptString($validatedData['celular']),

                    'telefone' => (isset($validatedData['telefone']) && $validatedData['telefone'] !== '')
                        ? Crypt::encryptString($validatedData['telefone'])
                        : null,

                    'cep'        => (isset($validatedData['cep']) && $validatedData['cep'] !== '')
                        ? Crypt::encryptString($validatedData['cep'])
                        : null,
                    'logradouro' => (isset($validatedData['logradouro']) && $validatedData['logradouro'] !== '')
                        ? Crypt::encryptString($validatedData['logradouro'])
                        : null,
                    'numero'     => (isset($validatedData['numero']) && $validatedData['numero'] !== '')
                        ? Crypt::encryptString($validatedData['numero'])
                        : null,
                    'bairro'     => (isset($validatedData['bairro']) && $validatedData['bairro'] !== '')
                        ? Crypt::encryptString($validatedData['bairro'])
                        : null,
                    'cidade'     => (isset($validatedData['cidade']) && $validatedData['cidade'] !== '')
                        ? Crypt::encryptString($validatedData['cidade'])
                        : null,
                    'estado'     => (isset($validatedData['estado']) && $validatedData['estado'] !== '')
                        ? Crypt::encryptString($validatedData['estado'])
                        : null,
                ]);

                Log::info('Cliente PF atualizado com sucesso!', ['cliente_id' => $cliente->id]);
            } elseif ($request->tipo_cliente === 'pessoa_juridica') {

                // (A) Validação para PJ
                $validatedData = $request->validate([
                    'razao_social'  => 'required|string|max:255',
                    'nome_fantasia' => 'nullable|string|max:255',
                    'cnpj'          => 'required|string|max:20',
                    'email'         => 'required|email|max:255',
                    'telefone'      => 'nullable|string|max:20',
                    'celular'       => 'nullable|string|max:20',

                    'cep'           => 'required|string|max:10',
                    'logradouro'    => 'required|string|max:255',
                    'numero'        => 'nullable|string|max:10',
                    'bairro'        => 'nullable|string|max:255',
                    'cidade'        => 'nullable|string|max:255',
                    'estado'        => 'nullable|string|max:255',
                ]);

                // (B) Localiza o cliente PJ
                $cliente = ClientePessoaJuridica::where('escritorio_id', $escritorio->id)->find($id);

                if (!$cliente) {
                    Log::warning("Cliente PJ não encontrado para update.", ['id' => $id]);
                    return response()->json(['message' => 'Cliente não encontrado.'], 404);
                }

                // (C) Verifica se o CNPJ já existe em outro registro
                $outroCnpj = ClientePessoaJuridica::where('escritorio_id', $escritorio->id)
                    ->where('id', '!=', $cliente->id)
                    ->get()
                    ->filter(fn($c) => Crypt::decryptString($c->cnpj) === $validatedData['cnpj'])
                    ->first();

                if ($outroCnpj) {
                    Log::warning('CNPJ já cadastrado para outro cliente neste escritório.', [
                        'cnpj' => $validatedData['cnpj'],
                        'cliente_id' => $outroCnpj->id
                    ]);
                    return response()->json([
                        'message' => 'CNPJ já cadastrado para outro cliente neste escritório.'
                    ], 409);
                }

                // (D) Atualiza dados
                $cliente->update([
                    'razao_social' => $validatedData['razao_social'],
                    'nome_fantasia' => $validatedData['nome_fantasia'] ?? null,

                    'cnpj'    => Crypt::encryptString($validatedData['cnpj']),
                    'email'   => Crypt::encryptString($validatedData['email']),
                    'telefone' => (isset($validatedData['telefone']) && $validatedData['telefone'] !== '')
                        ? Crypt::encryptString($validatedData['telefone'])
                        : null,
                    'celular' => (isset($validatedData['celular']) && $validatedData['celular'] !== '')
                        ? Crypt::encryptString($validatedData['celular'])
                        : null,

                    'cep'        => (isset($validatedData['cep']) && $validatedData['cep'] !== '')
                        ? Crypt::encryptString($validatedData['cep'])
                        : null,
                    'logradouro' => (isset($validatedData['logradouro']) && $validatedData['logradouro'] !== '')
                        ? Crypt::encryptString($validatedData['logradouro'])
                        : null,
                    'numero'     => (isset($validatedData['numero']) && $validatedData['numero'] !== '')
                        ? Crypt::encryptString($validatedData['numero'])
                        : null,
                    'bairro'     => (isset($validatedData['bairro']) && $validatedData['bairro'] !== '')
                        ? Crypt::encryptString($validatedData['bairro'])
                        : null,
                    'cidade'     => (isset($validatedData['cidade']) && $validatedData['cidade'] !== '')
                        ? Crypt::encryptString($validatedData['cidade'])
                        : null,
                    'estado'     => (isset($validatedData['estado']) && $validatedData['estado'] !== '')
                        ? Crypt::encryptString($validatedData['estado'])
                        : null,
                ]);

                Log::info('Cliente PJ atualizado com sucesso!', ['cliente_id' => $cliente->id]);
            } else {
                Log::error('Tipo de cliente inválido.', ['tipo_cliente' => $request->tipo_cliente]);
                return response()->json([
                    'message' => 'Erro ao atualizar cliente. Tipo de cliente inválido.'
                ], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Cliente atualizado com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar cliente', [
                'exception' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Erro ao atualizar cliente.'], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    DB::beginTransaction();

    try {
        Log::info("Iniciando exclusão do cliente...", ["cliente_id" => $id]);

        $user = Auth::user();
        $escritorio = Escritorio::where("user_id", $user->id)->first();

        if (!$escritorio) {
            Log::error("Usuário não pertence a nenhum escritório.");
            return response()->json(["message" => "Erro ao excluir cliente. Escritório não encontrado."], 400);
        }

        // Verifica se o cliente é PF ou PJ
        $clientePF = ClientePessoaFisica::where("escritorio_id", $escritorio->id)->find($id);
        $clientePJ = ClientePessoaJuridica::where("escritorio_id", $escritorio->id)->find($id);

        if (!$clientePF && !$clientePJ) {
            Log::warning("Cliente não encontrado para exclusão.", ["id" => $id]);
            return response()->json(["message" => "Cliente não encontrado."], 404);
        }

        // Excluir PF ou PJ conforme encontrado
        if ($clientePF) {
            Log::info("Excluindo cliente PF...", ["id" => $id]);
            $clientePF->delete();
        } elseif ($clientePJ) {
            Log::info("Excluindo cliente PJ...", ["id" => $id]);
            $clientePJ->delete();
        }

        DB::commit();
        return response()->json(["message" => "Cliente excluído com sucesso!"]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Erro ao excluir cliente", ["exception" => $e->getMessage()]);
        return response()->json(["message" => "Erro ao excluir cliente."], 500);
    }
}

}
