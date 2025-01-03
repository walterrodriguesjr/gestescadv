<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteUserDataController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\UsuarioUserDataController;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //clientes 
    Route::get('/view-cliente', [ClienteController::class, 'viewCliente'])->name('cliente/view_cliente');
    Route::resource('/cliente', ClienteController::class);
    Route::post('/usuario-user-data', [UsuarioUserDataController::class, 'CadastrarUsuarioUserData'])->name('usuario.user-data');
    
    //processos
    Route::get('/view-processo', [ProcessoController::class, 'viewProcesso'])->name('processo/view_processo');
});
