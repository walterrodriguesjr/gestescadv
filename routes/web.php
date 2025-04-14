<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EscritorioController;
use App\Http\Controllers\MembroEscritorioController;
use App\Http\Controllers\NivelAcessoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\SessaoController;

/**
 * Redireciona '/' com base na autenticação do usuário.
 */
Route::get('/', function () {
    // Se o usuário estiver autenticado, redireciona para 'main'
    if (Auth::check()) {
        return redirect()->route('main');
    }
    // Caso contrário, redireciona para 'login'
    return redirect()->route('login');
});

/**
 * Rotas Públicas (Sem autenticação)
 */
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('main');
    }
    return view('login.login');
})->name('login');

Route::get('/logout', function () {
    return redirect()->route('login')->with('info', 'Por favor, faça login para acessar esta página.');
})->name('logout.get');


Route::get('/forgot-password', function () {
    return view('password.forgot-password');
})->name('password.request');

Route::post('/forgot-password', [PasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordController::class, 'resetPassword'])
    ->name('password.update');


/**
 * Rotas Protegidas (Requer autenticação)
 */
Route::middleware(['auth', 'two-factor.verified', 'usuario.ativo'])->group(function () {
    // Rota principal
    Route::get('/main', function () {
        return view('layouts.main');
    })->name('main');

    // Rota de logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //alterar a senha estando logado
    Route::post('/alterar-senha', [PasswordController::class, 'alterarSenha'])
        ->middleware('auth') // Apenas para usuários logados
        ->name('password.change');

    //alterar autenticacao dois fatores estando logado
    Route::post('/atualizar-2fa', [TwoFactorController::class, 'atualizarAutenticacaoDoisFatores'])
        ->name('two-factor.update')
        ->middleware('auth'); // Garantir que só usuários logados alterem a configuração

    Route::get('/sessoes-ativas', [SessaoController::class, 'listarSessoesAtivas'])
        ->name('sessoes.ativas');

    Route::post('/sessoes-ativas/logout/{id}', [SessaoController::class, 'encerrarSessao'])
        ->name('sessoes.encerrar');

    Route::post('/sessoes-ativas/logout-all', [SessaoController::class, 'encerrarTodasSessoes'])
        ->name('sessoes.encerrar-todas');

    Route::get('/perfil/exportar-dados', [PerfilController::class, 'exportarDados'])
        ->name('perfil.exportar-dados');

    Route::get('/perfil/historico', [PerfilController::class, 'historicoAlteracoes']);

    Route::post('/validar-senha-exclusao', [PerfilController::class, 'validarSenhaExclusao']);

    Route::post('/excluir-conta', [PerfilController::class, 'excluirConta']); // Mudando para refletir a função correta

    Route::get('dados-escritorio', [EscritorioController::class, 'index'])->name('dados-escritorio.index');

    Route::get('/meu-escritorio', [EscritorioController::class, 'show'])->name('dados-escritorio.meu-escritorio');


    //Rota de escritório
    Route::resource('dados-escritorio', EscritorioController::class)
        ->except(['store', 'update']) // ⛔ Excluímos store e update para registrá-los separadamente

        ->names([
            'index'  => 'dados-escritorio.index',
            'create' => 'dados-escritorio.create',
            'show'   => 'dados-escritorio.show',
            'edit'   => 'dados-escritorio.edit',
            'destroy' => 'dados-escritorio.destroy',
        ]);

    // ✅ Aplicamos o middleware APENAS nas rotas store e update separadamente
    Route::post('dados-escritorio', [EscritorioController::class, 'store'])
        ->name('dados-escritorio.store')
        ->middleware('permissao_escritorio');

    Route::put('dados-escritorio/{id}', [EscritorioController::class, 'update'])
        ->name('dados-escritorio.update')
        ->middleware('permissao_escritorio');


    // Rota de perfil
    Route::resource('perfil', PerfilController::class);

    // Rota de nível de acesso
    Route::resource('nivel-acesso', NivelAcessoController::class)->names([
        'index'   => 'nivel-acesso.index',
        'create'  => 'nivel-acesso.create',
        'store'   => 'nivel-acesso.store',
        'show'    => 'nivel-acesso.show',
        'edit'    => 'nivel-acesso.edit',
        'update'  => 'nivel-acesso.update',
        'destroy' => 'nivel-acesso.destroy',
    ]);

    // Rota de membros do escritorio
    Route::resource('membro-escritorio', MembroEscritorioController::class)->names([
        'index'   => 'membro-escritorio.index',
        'create'  => 'membro-escritorio.create',
        'store'   => 'membro-escritorio.store',
        'edit'    => 'membro-escritorio.edit',
        'update'  => 'membro-escritorio.update',
        'destroy' => 'membro-escritorio.destroy',
    ]);

    Route::post('membro-escritorio/{id}/suspender', [MembroEscritorioController::class, 'suspender'])
        ->name('membro-escritorio.suspender')
        ->middleware('permissao_escritorio');

    // Rota para reativar membro do escritório
    Route::post('membro-escritorio/{id}/reativar', [MembroEscritorioController::class, 'reativar'])
        ->name('membro-escritorio.reativar')
        ->middleware('permissao_escritorio');

    Route::post('/membros/{id}/reenviar-convite', [MembroEscritorioController::class, 'reenviarConvite'])
        ->name('membros.reenviarConvite')
        ->middleware('permissao_escritorio');

    Route::put('/membros/{membroEscritorio}/update', [MembroEscritorioController::class, 'update'])
        ->name('membros.update')
        ->middleware('permissao_escritorio');

    Route::delete('/membros/{membroEscritorio}/delete', [MembroEscritorioController::class, 'destroy'])
        ->name('membros.destroy')
        ->middleware('permissao_escritorio');

    // Rota de membros do escritorio
    Route::resource('clientes', ClienteController::class)->names([
        'index'   => 'clientes.index',
        'create'  => 'clientes.create',
        'store'   => 'clientes.store',
        'show'    => 'clientes.show',
        'edit'    => 'clientes.edit',
        'update'  => 'clientes.update',
        'destroy' => 'clientes.destroy',
    ]);

    Route::post('/clientes/{tipo}/{id}/documentos', [ClienteController::class, 'anexarDocumento']);
    Route::get('/clientes/{tipo}/{id}/documentos', [ClienteController::class, 'listarDocumentos']);
    Route::put('/documentos/{tipoCliente}/{documento}', [ClienteController::class, 'atualizarDocumentoCliente'])->name('documentos.atualizarDocumentoCliente');
    Route::delete('/documentos/{tipoCliente}/{documento}', [ClienteController::class, 'deletarDocumentoCliente'])->name('documentos.deletarDocumentoCliente');

    Route::get('/servicos', [ServicoController::class, 'index'])->name('servicos.index');

    Route::get('/listar_tipo_servico/{id}', [ServicoController::class, 'listar_tipo_servico'])->name('tipo_servicos.listar_tipo_servico');
    Route::get('/tipo_servicos/{id}', [ServicoController::class, 'listar_tipo_servico'])->name('tipo_servicos.listar_tipo_servico');
    Route::post('/tipo_servicos/{id}', [ServicoController::class, 'cadastrar_tipo_servico'])->name('tipo_servicos.cadastrar_tipo_servico');
    Route::get('/buscar_tipo_servicos/{id}', [ServicoController::class, 'buscar_tipo_servicos'])->name('tipo_servicos.buscar_tipo_servicos');
    Route::put('/atualizar_tipo_servico/{id}', [ServicoController::class, 'atualizar_tipo_servico'])->name('tipo_servicos.atualizar_tipo_servico');
    Route::delete('/deletar_tipo_servico/{id}', [ServicoController::class, 'deletar_tipo_servico'])->name('tipo_servicos.deletar_tipo_servico');

    Route::get('/servicos/listar', [ServicoController::class, 'listarServicos'])->name('servicos.listar');
    Route::post('/servicos', [ServicoController::class, 'store'])->name('servicos.store');


});

Route::get('membro-escritorio/{id}', [MembroEscritorioController::class, 'show'])
    ->name('membro-escritorio.show');

/**
 * Rotas de autenticação
 */
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/two-factor', [TwoFactorController::class, 'showTwoFactorForm'])
    ->middleware('auth')
    ->name('two-factor.show');

Route::post('/two-factor', [TwoFactorController::class, 'verifyTwoFactor'])
    ->middleware('auth')
    ->name('two-factor.verify');

Route::post('/two-factor/resend', [TwoFactorController::class, 'resendTwoFactorCode'])
    ->middleware('auth')
    ->name('two-factor.resend');
