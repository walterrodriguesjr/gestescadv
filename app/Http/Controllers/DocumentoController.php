<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Services\HuggingFaceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class DocumentoController
{

    public function index()
    {
        $user = Auth::user()->load('userData', 'escritorio');

        $cpf = $user->userData?->getDecrypted('cpf') ?? '';
        $oab = $user->userData?->getDecrypted('oab') ?? '';
        $cidade = $user->userData?->cidade ?? '';
        $estado = $user->userData?->estado ?? '';
        $nome = $user->name;

        return view('documento.documento', [
            'user' => $user,
            'escritorioId' => $user->escritorio->id ?? null,
            'cpf' => $cpf,
            'oab' => $oab,
            'cidade' => $cidade,
            'estado' => $estado,
            'nome' => $nome,
        ]);
    }


    public function assistenteIa(Request $request, \App\Services\LmStudioService $ia)
    {
        $prompt = $request->input('prompt');
        if (!$prompt) {
            return response()->json(['message' => 'Prompt não informado.'], 422);
        }

        try {
            $resposta = $ia->gerarSugestaoDocumento($prompt);
            return response()->json(['resposta' => $resposta]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }




    // Demais métodos resource (placeholders)
    public function create()
    { /* ... */
    }
    public function store(Request $request)
{
    $request->validate([
        'escritorio_id' => 'required|integer',
        'tipo_documento_id' => 'required|integer',
        'titulo' => 'required|string|max:255',
        'conteudo' => 'required|string'
    ]);

    $documento = Documento::create([
        'escritorio_id' => $request->escritorio_id,
        'tipo_documento_id' => $request->tipo_documento_id,
        'titulo' => $request->titulo,
        'texto' => $request->conteudo
    ]);

    $tipo = TipoDocumento::find($request->tipo_documento_id);
    $tituloSanitizado = Str::slug($request->titulo);
    $arquivoNome = "{$tituloSanitizado}-{$documento->id}-escritorio-{$request->escritorio_id}.doc";

    $caminho = "documentos/documento-escritorio-{$request->escritorio_id}/" . Str::slug($tipo->titulo);

    Storage::disk('public')->makeDirectory($caminho);

    $conteudoHtml = view('documento.modelo-word', [
        'conteudo' => $request->conteudo
    ])->render();

    Storage::disk('public')->put("{$caminho}/{$arquivoNome}", $conteudoHtml);

    return response()->json(['message' => 'Documento salvo com sucesso.']);
}
    public function show($id)
    { /* ... */
    }
    public function edit($id)
    { /* ... */
    }
    public function update(Request $request, $id)
    { /* ... */
    }
    public function destroy($id)
    { /* ... */
    }
}
