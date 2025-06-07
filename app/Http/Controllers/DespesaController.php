<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use App\Models\TipoDespesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DespesaController
{
    /**
     * Display a listing of the resource.
     */

public function index()
{
    $user = auth()->user();

    if (!$user || !$user->escritorio) {
        abort(403, 'Usuário não possui escritório vinculado.');
    }

    $escritorioId = $user->escritorio->id;

    // Se AJAX (DataTables), devolve despesas em JSON
    if (request()->ajax()) {
        $despesas = \App\Models\Despesa::with('tipoDespesa')
            ->where('escritorio_id', $escritorioId)
            ->orderByDesc('created_at')
            ->get();

        // Adapte se precisar paginar!
        return response()->json([
            'data' => $despesas
        ]);
    }

    // Se não for AJAX, carrega view normalmente
    $tiposDespesa = \App\Models\TipoDespesa::where('escritorio_id', $escritorioId)
        ->orderBy('titulo')
        ->get();

    return view('despesa.despesa', compact('tiposDespesa'));
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
    // app/Http/Controllers/DespesaController.php

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'escritorio_id'    => 'required|integer|exists:escritorios,id',
            'tipo_despesa_id'  => 'required|integer|exists:tipos_despesa,id',
            'valor'            => 'required|numeric|min:0.01',
            'data_vencimento'  => 'required|date',
        ]);

        DB::beginTransaction();

        $despesa = new \App\Models\Despesa();
        $despesa->escritorio_id    = $validated['escritorio_id'];
        $despesa->tipo_despesa_id  = $validated['tipo_despesa_id'];
        $despesa->valor            = $validated['valor'];
        $despesa->data_vencimento  = $validated['data_vencimento'];
        $despesa->situacao         = false; // não paga
        $despesa->data_pagamento   = null;
        $despesa->save();

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Despesa cadastrada com sucesso.']);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Campos obrigatórios não preenchidos ou inválidos.',
            'errors'  => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erro ao salvar despesa: ' . $e->getMessage(), [
            'exception' => $e,
            'request'   => $request->all()
        ]);
        return response()->json([
            'message' => 'Erro interno ao cadastrar despesa.',
            'error'   => $e->getMessage()
        ], 500);
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
