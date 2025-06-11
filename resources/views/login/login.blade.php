<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Inclua os estilos e scripts compilados -->
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow border-0 rounded">
                    <div class="card-header text-center bg-primary text-white">
                        <h3 class="mb-0">Gestão Jurídica</h3>
                        <small class="text-white-50">Acesse sua conta</small>
                    </div>
                    <div class="card-body">

                        @if ($errors->has('throttle'))
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle"></i> {{ $errors->first('throttle') }}
                            </div>
                        @endif


                        <!-- Formulário de Login -->
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Senha -->
                            <div class="mb-4">
                                <label for="password" class="form-label">Senha</label>
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

                            <!-- Lembrar-me -->
                            <div class="mb-4 form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="remember"
                                    name="remember">
                                <label class="form-check-label" for="remember">Lembrar-me</label>
                            </div>

                            <!-- Botão de Login -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Entrar</button>
                            </div>
                        </form>
                    </div>

                    <!-- Link para recuperação de senha -->
                    <div class="card-footer text-center">
                        <a href="{{ route('password.request') }}" class="text-decoration-none">Esqueceu sua senha?</a>
                    </div>
                </div>

                <!-- Rodapé -->
                <footer class="text-center mt-4 text-muted">
                    <small>© {{ date('Y') }} Gestão Jurídica. Todos os direitos reservados.</small>
                </footer>
            </div>
        </div>
    </div>
{{--     <script src="{{ asset('js/app.js') }}"></script> <!-- Inclua o JS compilado -->
 --}}</body>
</html>
