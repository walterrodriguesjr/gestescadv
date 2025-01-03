<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use Illuminate\Http\Request;

class ProcessoController extends Controller
{
    public function viewProcesso()
    {
    return view('processo.processo-main');

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Processo $processo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Processo $processo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Processo $processo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Processo $processo)
    {
        //
    }
}
