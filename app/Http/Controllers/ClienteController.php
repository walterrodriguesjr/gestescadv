<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function viewCliente()
{
    return view('cliente.cliente-main');

}
}
