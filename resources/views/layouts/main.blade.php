<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>

    <!-- Fontes locais -->
    <style>
        @font-face {
            font-family: 'Nunito';
            src: url("{{ asset('fonts/nunito/Nunito-VariableFont_wght.ttf') }}") format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Nunito';
            src: url("{{ asset('fonts/nunito/Nunito-Italic-VariableFont_wght.ttf') }}") format('truetype');
            font-weight: 100 900;
            font-style: italic;
            font-display: swap;
        }

        html,
        body {
            font-family: 'Nunito', sans-serif !important;
        }

        /* Container do campo */
    </style>

    <!-- Adicione no <head> -->


    <!-- AdminLTE CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.0.0/dist/css/adminlte.min.css">

    <!-- Font Awesome via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/css/dataTables.bootstrap5.min.css') }}">

    <!-- sweetalert CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert/css/sweetalert2.min.css') }}">

    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/choices/css/choices.min.css') }}">

    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/css/dropzone.css') }}">

    <!-- CSS personalizado view perfil-->
    <link rel="stylesheet" href="{{ asset('css/perfil/perfil-form.css') }}">

    <!-- CSS personalizado view escritorio-->
    <link rel="stylesheet" href="{{ asset('css/escritorio/escritorio-form.css') }}">

    <link rel="stylesheet" href="{{ asset('css/escritorio/escritorio-form.css') }}">

    <!-- Override visual do Choices.js -->
    <link rel="stylesheet" href="{{ asset('css/custom/choices-overrides.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <!-- Botão para expandir/recolher o sidebar -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <!-- Conteúdo alinhado à direita -->
            <ul class="navbar-nav ms-auto mr-3">
                <li class="nav-item d-flex align-items-center">
                    <span class="me-3">Olá, {{ Auth::user()->name }}</span>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">Sair</button>
                    </form>
                </li>
            </ul>
        </nav>


        <!-- Sidebar -->
        @include('components.sidebar.sidebar')

        <!-- Conteúdo principal -->
        <main class="content-wrapper p-4">
            @yield('content')
        </main>
    </div>


    <!-- Jquery -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.0.0/dist/js/adminlte.min.js"></script>

    {{-- Jquery Mask --}}
    <script src="{{ asset('vendor/jquery-mask-plugin/jquery.mask.min.js') }}"></script>

    {{-- Jquery Validation --}}
    <script src="{{ asset('vendor/jquery-validation/js/jquery.validate.min.js') }}"></script>

    {{-- select2 --}}
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>

    {{-- datatable --}}
    <script src="{{ asset('vendor/datatable/js/dataTables.min.js') }}"></script>

    {{-- datatable Bootstrap --}}
    <script src="{{ asset('vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    {{-- sweetalert --}}
    <script src="{{ asset('vendor/sweetalert/js/sweetalert2.all.min.js') }}"></script>

    <!-- Choices.js JS -->
    <script src="{{ asset('vendor/choices/js/choices.min.js') }}"></script>

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/js/dropzone-min.js') }}"></script>

    <!-- Moment.js -->
    <script src="{{ asset('vendor/moment-js/js/moment-with-locales.js') }}"></script>


    <!-- Scripts adicionais -->
    @stack('scripts')

</body>

</html>
