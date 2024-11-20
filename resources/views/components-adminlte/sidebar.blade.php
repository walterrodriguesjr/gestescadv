<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Meu Sistema</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('profile.show') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Perfil</p>
                    </a>
                </li>
                <!-- Adicione mais itens de menu aqui -->
            </ul>
        </nav>
    </div>
</aside>

<!-- Script para o toggle do sidebar -->
<script>
    $(document).ready(function () {
        $('.nav-link[data-widget="pushmenu"]').on('click', function (e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-collapse'); // Alterna a classe para recolher ou expandir o sidebar
        });
    });
</script>
