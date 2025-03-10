<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Gestão Jurídica</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <a href="{{ route('perfil.index') }}"
            class="d-flex align-items-center flex-column text-white text-decoration-none user-panel mt-3 pb-3 mb-3">
            <!-- "Meus Dados" visível apenas no sidebar expandido -->
            <span class="fw-bold text-sm text-expanded mb-2 ml-2">Perfil</span>

            <!-- Foto do usuário -->
            <div class="image">
                @php
                    use Illuminate\Support\Facades\File;
                    use Illuminate\Support\Facades\Crypt;

                    $user = Auth::user();
                    $userData = $user->userData ?? null;
                    $fotoPath = asset('storage/foto-perfil/sem-foto.jpg'); // Foto padrão

                    if ($userData) {
                        try {
                            $cpfLimpo = $userData->cpf
                                ? preg_replace('/\D/', '', Crypt::decryptString($userData->cpf))
                                : null; // Remove pontuações do CPF

                            if ($cpfLimpo) {
                                // 🔍 Buscar fotos diretamente na pasta real do sistema de arquivos
                                $fotoDir = storage_path('app/public/foto-perfil');
                                $fotos = File::glob("{$fotoDir}/foto-{$cpfLimpo}-*.*"); // 🔥 Busca correta no diretório

                                // 🔎 Exibir todos os arquivos encontrados no log (para debug)
                                Log::info('📁 Arquivos encontrados na pasta foto-perfil:', $fotos);

                                // Ordena as fotos pela data mais recente
                                usort($fotos, function ($a, $b) {
                                    return strcmp($b, $a); // Ordenação decrescente
                                });

                                // Se houver fotos, pega a mais recente
                                if (!empty($fotos)) {
                                    $fotoArquivo = basename($fotos[0]); // Apenas o nome do arquivo
                                    $fotoPath = asset("storage/foto-perfil/{$fotoArquivo}");

                                    Log::info("✅ Foto encontrada para sidebar: {$fotoPath}");
                                } else {
                                    Log::warning("⚠️ Nenhuma foto encontrada para CPF: {$cpfLimpo}");
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error('❌ Erro ao buscar a foto para sidebar: ' . $e->getMessage());
                        }
                    }
                @endphp

                <img src="{{ $fotoPath }}" class="img-circle elevation-2" alt="User Image">
            </div>




            <!-- Nome do usuário -->
            <div class="info">
                <span class="d-block">{{ Auth::user()->name }}</span>
            </div>
        </a>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('main') }}" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dados-escritorio.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Escritório</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clientes.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clientes</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-gavel"></i>
                        <p>Processos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Documentos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Configurações</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Sair</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
