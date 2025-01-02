<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Rules\Cpf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{

    public function viewCliente()
{
    return view('cliente.cliente-main');

}
    /**
     * Listar todos os clientes relacionados ao Team do usuario logado.
     */
    public function index()
    {
        try {
            /* Recupera o ID do team atual do usuario logado */
            $teamId = Auth::user()->currentTeam->id;
         
            /* Buscar todos os clientes associados ao ID contido em $teamId */
            $clientes = Cliente::whereHas('teams', function ($query) use ($teamId) {
                $query->where('cliente_team.team_id', $teamId); // Especifica a tabela pivot
            })->get();
         
            //retornar todos os clientes localizados como JSON
            return response()->json($clientes, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocorreu um erro ao buscar os clientes.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        // Validação dos dados
        $validated = $request->validate([
            'nome_completo' => 'required|string|min:3',
            'cpf' => ['required', 'string', new Cpf],
            'email' => 'required|email',
            'celular' => 'required|string|min:14',
            'telefone' => 'nullable|string',
            'cep' => 'nullable|string',
            'rua' => 'nullable|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'estado' => 'nullable|string',
            'cidade' => 'nullable|string',
        ]);

        // Recuperar o time atual do usuário logado
        $teamId = Auth::user()->currentTeam->id;

        // Verificar se o cliente já está associado ao time
        $clienteExistente = Cliente::where('cliente_cpf', $validated['cpf'])
            ->whereHas('teams', function ($query) use ($teamId) {
                $query->where('teams.id', $teamId); // Especifica 'teams.id'
            })->first();

        if ($clienteExistente) {
            return response()->json([
                'message' => 'Este cliente já está cadastrado para o time atual.',
            ], 409); // Código HTTP 409 (Conflito)
        }

        // Criar o cliente
        $cliente = Cliente::create([
            'cliente_nome_completo' => $validated['nome_completo'],
            'cliente_cpf' => $validated['cpf'],
            'cliente_email' => $validated['email'],
            'cliente_celular' => $validated['celular'],
            'cliente_telefone' => $validated['telefone'],
            'cliente_cep' => $validated['cep'],
            'cliente_rua' => $validated['rua'],
            'cliente_numero' => $validated['numero'],
            'cliente_bairro' => $validated['bairro'],
            'cliente_estado' => $validated['estado'],
            'cliente_cidade' => $validated['cidade'],
        ]);

        // Associar o cliente ao time atual
        $cliente->teams()->attach($teamId);

        return response()->json(['message' => 'Cliente cadastrado e vinculado ao time com sucesso!'], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Erro na validação dos dados.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Ocorreu um erro ao processar a requisição.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente);
        } catch (Exception $e) {
            return response()->json(['message' => 'Cliente não encontrado.'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
{
    // Validação dos dados recebidos
    $validatedData = $request->validate([
        'nome_completo' => 'required|string|min:3',
        'email' => 'nullable|email',
        'celular' => 'nullable|string|min:10|max:15',
        'telefone' => 'nullable|string|min:10|max:15',
        'cep' => 'nullable|string|min:8|max:9',
        'rua' => 'nullable|string|max:255',
        'numero' => 'nullable|string|max:10',
        'bairro' => 'nullable|string|max:255',
        'estado' => 'nullable|string|size:2',
        'cidade' => 'nullable|string|max:255',
    ]);

    try {
        // Atualiza os dados do cliente no banco
        $cliente->update([
            'cliente_nome_completo' => $validatedData['nome_completo'],
            'cliente_email' => $validatedData['email'],
            'cliente_celular' => $validatedData['celular'],
            'cliente_telefone' => $validatedData['telefone'],
            'cliente_cep' => $validatedData['cep'],
            'cliente_rua' => $validatedData['rua'],
            'cliente_numero' => $validatedData['numero'],
            'cliente_bairro' => $validatedData['bairro'],
            'cliente_estado' => $validatedData['estado'],
            'cliente_cidade' => $validatedData['cidade'],
        ]);

        return response()->json(['message' => 'Cliente atualizado com sucesso!'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro ao atualizar o cliente.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}
