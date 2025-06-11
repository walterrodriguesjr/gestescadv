<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação em 2 Fatores</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow border-0 rounded">
                    <div class="card-header text-center bg-primary text-white">
                        <h3 class="mb-0">Verificação de 2 Fatores</h3>
                        <small>Insira o código enviado para seu {{ Auth::user()->two_factor_type === 'sms' ? 'telefone' : 'e-mail' }}.</small>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('two-factor.verify') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="code" class="form-label">Código</label>
                                <input 
                                    type="text" 
                                    class="form-control @error('code') is-invalid @enderror" 
                                    id="code" 
                                    name="code" 
                                    required 
                                    autofocus>
                                @error('code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Verificar</button>
                            </div>
                        </form>
                        <form action="{{ route('two-factor.resend') }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-link">Reenviar Código</button>
                        </form>
                    </div>
                </div>
                <footer class="text-center mt-4 text-muted">
                    <small>© {{ date('Y') }} Gestão Jurídica. Todos os direitos reservados.</small>
                </footer>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
