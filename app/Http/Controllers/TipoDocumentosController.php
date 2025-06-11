<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoDocumentosController
{

    public function listar($escritorio_id)
    {
        $tipos = TipoDocumento::where('escritorio_id', $escritorio_id)
            ->orderBy('titulo')
            ->get(['id', 'titulo']);
        return response()->json($tipos);
    }

    public function index()
    {
        $user = Auth::user();
        $escritorioId = $user->escritorio->id ?? null;
        $tipos = TipoDocumento::where('escritorio_id', $escritorioId)->orderBy('titulo')->get();

        return response()->json(['data' => $tipos]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $escritorioId = $user->escritorio->id ?? null;

        $request->validate([
            'titulo' => 'required|string|max:255'
        ]);

        $tipo = TipoDocumento::create([
            'escritorio_id' => $escritorioId,
            'user_id' => $user->id,
            'titulo' => $request->titulo
        ]);

        return response()->json(['success' => true, 'data' => $tipo]);
    }

    public function show($id)
    {
        $tipo = TipoDocumento::findOrFail($id);
        return response()->json(['data' => $tipo]);
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoDocumento::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255'
        ]);

        $tipo->update([
            'titulo' => $request->titulo
        ]);

        return response()->json(['success' => true, 'data' => $tipo]);
    }

    public function destroy($id)
    {
        $tipo = TipoDocumento::findOrFail($id);
        $tipo->delete();

        return response()->json(['success' => true]);
    }
}
