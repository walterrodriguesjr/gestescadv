<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Servico;
use App\Models\AndamentoServico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;


class AndamentoServicoController
{

    public function listarTodosArquivosServico($servicoId, $clienteId)
    {
        $base1 = storage_path("app/public/arquivos_servicos/servico_{$servicoId}_cliente_{$clienteId}");
        $base2 = storage_path("app/public/arquivos_servicos_andamento");

        $arquivos = [];

        // Arquivos diretos do serviço
        if (File::exists($base1)) {
            foreach (File::files($base1) as $file) {
                $arquivos[] = [
                    'nome' => $file->getFilename(),
                    'url' => asset('storage/arquivos_servicos/servico_' . $servicoId . '_cliente_' . $clienteId . '/' . $file->getFilename()),
                ];
            }
        }

        // Arquivos de andamentos
        $pastaAndamentos = collect(File::directories($base2))->filter(function ($pasta) use ($servicoId, $clienteId) {
            return str_contains($pasta, "servico_{$servicoId}_") && str_contains($pasta, "_cliente_{$clienteId}");
        });

        foreach ($pastaAndamentos as $pasta) {
            foreach (File::files($pasta) as $file) {
                $dir = basename($pasta);
                $arquivos[] = [
                    'nome' => $file->getFilename(),
                    'url' => asset('storage/arquivos_servicos_andamento/' . $dir . '/' . $file->getFilename()),
                ];
            }
        }

        return response()->json(['arquivos' => $arquivos]);
    }

    public function atualizarNumeroProcesso(Request $request, $id)
{
    $request->validate([
        'numero_processo' => ['required', 'string', 'max:25']
    ]);

    $servico = Servico::findOrFail($id);
    $servico->numero_processo = $request->numero_processo;
    $servico->save();

    return response()->json(['message' => 'Número do processo salvo com sucesso.']);
}


    public function anexarArquivo(Request $request, $servicoId, $andamentoId, $clienteId)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:100000',
            'nome_original' => 'required|string|max:255',
        ]);

        try {
            $arquivo = $request->file('file');
            $extensao = $arquivo->getClientOriginalExtension();
            $nomeSanitizado = Str::slug(pathinfo($request->nome_original, PATHINFO_FILENAME));
            $nomeFinal = $nomeSanitizado . '-' . time() . '.' . $extensao;

            $caminho = "arquivos_servicos_andamento/servico_{$servicoId}_andamento_{$andamentoId}_cliente_{$clienteId}/{$nomeFinal}";

            Storage::disk('public')->putFileAs(
                "arquivos_servicos_andamento/servico_{$servicoId}_andamento_{$andamentoId}_cliente_{$clienteId}",
                $arquivo,
                $nomeFinal
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Erro ao anexar arquivo andamento", [
                'erro' => $e->getMessage(),
                'linha' => $e->getLine()
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    public function listarArquivos($servicoId, $andamentoId, $clienteId)
    {
        $caminho = "arquivos_servicos_andamento/servico_{$servicoId}_andamento_{$andamentoId}_cliente_{$clienteId}";

        if (!Storage::disk('public')->exists($caminho)) {
            return response()->json(['arquivos' => []]);
        }

        $arquivos = collect(Storage::disk('public')->files($caminho))->map(function ($arquivo) {
            return [
                'nome_original' => basename($arquivo),
                'url' => Storage::url($arquivo)
            ];
        });

        return response()->json(['arquivos' => $arquivos]);
    }


    public function atualizarNomeArquivo(Request $request)
    {
        $request->validate([
            'servico_id'    => 'required|integer',
            'andamento_id'  => 'required|integer',
            'cliente_id'    => 'required|integer',
            'nome_atual'    => 'required|string',   // nome velho COM extensão
            'nome_original' => 'required|string'    // novo nome sem extensão
        ]);

        $servicoId   = $request->servico_id;
        $andamentoId = $request->andamento_id;
        $clienteId   = $request->cliente_id;

        $pasta = "arquivos_servicos_andamento/servico_{$servicoId}_andamento_{$andamentoId}_cliente_{$clienteId}";
        $caminhoAntigo = "{$pasta}/{$request->nome_atual}";

        if (!Storage::disk('public')->exists($caminhoAntigo)) {
            return response()->json(['message' => 'Arquivo original não encontrado.'], 404);
        }

        $ext      = pathinfo($request->nome_atual, PATHINFO_EXTENSION);
        $novoNome = Str::slug($request->nome_original) . '.' . $ext;
        $caminhoNovo = "{$pasta}/{$novoNome}";

        // ✅ Se o novo nome for igual ao atual, não precisa mover nem dar erro
        if ($request->nome_atual === $novoNome) {
            return response()->json(['message' => 'Nome do arquivo mantido.']);
        }

        if (Storage::disk('public')->exists($caminhoNovo)) {
            return response()->json(['message' => 'Já existe um arquivo com esse nome.'], 422);
        }

        Storage::disk('public')->move($caminhoAntigo, $caminhoNovo);

        return response()->json(['message' => 'Nome do arquivo atualizado com sucesso.']);
    }



    public function deletarArquivo(Request $request, $servicoId, $andamentoId, $clienteId)
    {
        $request->validate(['arquivo' => 'required|string']);

        $caminho = "arquivos_servicos_andamento/servico_{$servicoId}_andamento_{$andamentoId}_cliente_{$clienteId}/{$request->arquivo}";

        if (Storage::disk('public')->exists($caminho)) {
            Storage::disk('public')->delete($caminho);
            return response()->json(['message' => 'Arquivo deletado com sucesso.']);
        }

        return response()->json(['message' => 'Arquivo não encontrado.'], 404);
    }


    public function atualizarObservacoes(Request $request, $servicoId)
    {
        $request->validate([
            'etapa' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_hora' => 'required|date'
        ]);

        $dataHora = Carbon::parse($request->data_hora);

        $andamento = AndamentoServico::where('servico_id', $servicoId)
            ->where('etapa', $request->etapa)
            ->whereBetween('data_hora', [
                $dataHora->copy()->startOfMinute(),
                $dataHora->copy()->endOfMinute()
            ])
            ->first();

        if (!$andamento) {
            return response()->json([
                'message' => 'Andamento não encontrado.'
            ], 404);
        }

        $andamento->observacoes = $request->descricao;
        $andamento->save();

        return response()->json([
            'message' => 'Observações atualizadas com sucesso!',
            'andamento' => $andamento
        ]);
    }



    public function buscarObservacoes(Request $request, $servicoId)
{
    $request->validate([
        'etapa' => 'required|string|max:255',
        'data_hora' => 'required|date'
    ]);

    $dataHora = Carbon::parse($request->data_hora);

    $andamento = AndamentoServico::where('servico_id', $servicoId)
        ->where('etapa', $request->etapa)
        ->whereBetween('data_hora', [
            $dataHora->copy()->startOfMinute(),
            $dataHora->copy()->endOfMinute()
        ])
        ->first();

    return response()->json([
        'existe' => (bool) $andamento,
        'descricao' => $andamento->observacoes ?? null,
        'id' => $andamento->id ?? null
    ]);
}



    // Controller: AndamentoServicoController.php
    public function listarAndamentos(Request $request, $servicoId)
    {
        $andamentos = AndamentoServico::with('agenda')
            ->where('servico_id', $servicoId)
            ->orderByDesc('data_hora')
            ->paginate(5);

        $dados = $andamentos->map(function ($item) {
            $isAgenda = $item->agenda_id && $item->agenda;
            return [
                'id' => $item->id,
                'etapa' => $item->etapa,
                'descricao' => $item->descricao ?? 'Sem descrição informada.',
                'data_hora' => $item->data_hora->format('d/m/Y H:i'),
                'data_hora_fim' => $isAgenda ? optional($item->agenda->data_hora_fim)->format('d/m/Y H:i') : null,
                'icone_cor' => $isAgenda ? 'bg-success' : 'bg-primary',
                'tipo' => $isAgenda ? 'agenda' : 'andamento',
            ];
        });

        return response()->json([
            'data' => $dados,
            'next_page_url' => $andamentos->nextPageUrl()
        ]);
    }



    public function index($servicoId)
    {
        $servico = Servico::with(['tipoServico', 'andamentos'])->findOrFail($servicoId);
        $cliente = $servico->clienteFormatado;

        // Descriptografar CPF, CNPJ e celular
        $cpfCnpj = $cliente?->cpf
            ? Crypt::decryptString($cliente->cpf)
            : ($cliente?->cnpj ? Crypt::decryptString($cliente->cnpj) : null);

        $tipoDocumento = $cliente?->cpf ? 'cpf' : ($cliente?->cnpj ? 'cnpj' : null);

        $andamentos = AndamentoServico::with('agenda')
            ->where('servico_id', $servicoId)
            ->orderByDesc('data_hora') // ordem decrescente
            ->get()
            ->map(function ($item) {
                $isAgenda = $item->agenda_id && $item->agenda;

                return (object) [
                    'id'            => $item->id,
                    'tipo'          => $isAgenda ? 'agenda' : 'andamento',
                    'etapa'         => $item->etapa,
                    'descricao'     => $item->descricao ?? 'Sem descrição informada.',
                    'data_hora'     => $item->data_hora,
                    'data_hora_fim' => $isAgenda ? $item->agenda->data_hora_fim : null,
                    'icone_cor'     => $isAgenda ? 'bg-success' : 'bg-primary',
                ];
            });

        return view('andamento.ver-andamento', [
            'servico'        => $servico,
            'andamentos'     => $andamentos,
            'clienteNome'    => $cliente?->nome ?? $cliente?->razao_social ?? '—',
            'cpfCnpj'        => $cpfCnpj ?? '—',
            'tipoDocumento'  => $tipoDocumento,
        ]);
    }




    public function store(Request $request, $servicoId)
    {
        $validator = Validator::make($request->all(), [
            'etapa' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'honorario' => 'nullable|numeric',
            'data_hora' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $andamento = AndamentoServico::create([
            'servico_id' => $servicoId,
            'etapa' => $request->etapa,
            'descricao' => $request->descricao,
            'observacoes' => $request->observacoes,
            'honorario' => $request->honorario ?? null,
            'data_hora' => $request->data_hora,
        ]);

        return response()->json([
            'message' => 'Andamento salvo com sucesso!',
            'andamento' => $andamento
        ]);
    }

    public function destroy($id)
    {
        $andamento = AndamentoServico::findOrFail($id);
        $andamento->delete();

        return response()->json(['message' => 'Andamento removido com sucesso.']);
    }
}
