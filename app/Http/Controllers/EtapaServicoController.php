<?php

namespace App\Http\Controllers;

use App\Models\EtapaServico;
use Illuminate\Http\Request;

class EtapaServicoController
{
    public function listar()
    {
        $etapas = EtapaServico::select('id', 'nome', 'icone_cor')
            ->orderBy('nome')
            ->get();

        return response()->json([
            'data' => $etapas
        ]);
    }
}
