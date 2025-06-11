<?php

namespace App\Http\Controllers;

use App\Models\NivelAcesso;
use Illuminate\Http\Request;

class NivelAcessoController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Busca todos os níveis de acesso, exceto o Administrador
            $niveisAcesso = NivelAcesso::where('nome', '!=', 'Administrador')->get(['id', 'nome']);

            return response()->json([
                'success' => true,
                'niveis' => $niveisAcesso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar níveis de acesso.',
                'error' => $e->getMessage()
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

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
