<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Inclua os estilos e scripts compilados -->
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow border-0 rounded">
                    <div class="card-header text-center bg-primary text-white">
                        <h3 class="mb-0">Redefinir Senha</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ $email ?? old('email') }}" 
                                    required 
                                    autofocus>
                                    @error('email')
                                    <div class="invalid-feedback">
                                        @if ($message === 'This password reset token is invalid.')
                                            O link de redefinição de senha expirou ou é inválido. Solicite um novo link.
                                        @else
                                            {{ $message }}
                                        @endif
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Nova Senha</label>
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirme a Nova Senha</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Redefinir Senha</button>
                            </div>
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
