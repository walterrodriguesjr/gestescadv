<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">


        <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- AdminLTE Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                @livewire('navigation-menu')
            </nav>

            <!-- Sidebar -->
            @include('components-adminlte.sidebar')

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Page Heading -->
                @if (isset($header))
                    <div class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <h1 class="m-0">{{ $header }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <section class="content">
                    <div class="container-fluid">
                        @yield('content')
                        {{ $slot ?? ''}}
                    </div>
                </section>
            </div>

            <!-- Footer -->
            <footer class="main-footer">
                <div class="float-right d-none d-sm-inline">
                    Qualquer texto adicional
                </div>
                <strong>Copyright &copy; 2023 <a href="#">Seu Sistema</a>.</strong> Todos os direitos reservados.
            </footer>
        </div>

        <!-- AdminLTE Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

        @livewireScripts
    </body>
</html>

<script>
    $(document).ready(function () {
    $('.nav-link[data-widget="pushmenu"]').on('click', function (e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-collapse'); // Alterna a classe para recolher ou expandir o sidebar
    });
});

</script>


