<?php

namespace App\Http\Controllers;

use App\Models\TipoDespesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoDespesaController
{

    //chama novo tipo de despesa para o select de tipo de despesa após ela ser cadastrada, dinamicamente
    public function listar($escritorio_id)
    {
        $tipos = TipoDespesa::where('escritorio_id', $escritorio_id)
            ->orderBy('titulo')
            ->get(['id', 'titulo']);
        return response()->json($tipos);
    }


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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user || !$user->escritorio) {
            return response()->json(['message' => 'Usuário sem escritório vinculado.'], 403);
        }

        $tipo = TipoDespesa::create([
            'titulo'        => ucwords(mb_strtolower($request->titulo)),
            'escritorio_id' => $user->escritorio->id,
            'user_id'       => $user->id,
        ]);

        return response()->json([
            'message' => 'Tipo de despesa cadastrado com sucesso!',
            'tipo' => [
                'id' => $tipo->id,
                'titulo' => $tipo->titulo
            ]
        ]);
    }


    /**
     * Display the specified resource.
     */
    // app/Http/Controllers/DespesaController.php

    public function show($id)
    {
        $user = auth()->user();
        if (!$user || !$user->escritorio) {
            return response()->json([], 403);
        }
        $tipos = \App\Models\TipoDespesa::where('escritorio_id', $user->escritorio->id)
            ->orderBy('titulo')
            ->get(['id', 'titulo']);
        return response()->json($tipos);
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
