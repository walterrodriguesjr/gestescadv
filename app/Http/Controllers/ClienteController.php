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
}
