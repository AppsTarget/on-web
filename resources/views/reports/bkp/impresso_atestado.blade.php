<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Atestado | {{ getEmpresaObj()->descr }}</title>

    <link rel="icon shortcut" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon"          href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/bootstrap.min.css')        }}" rel="stylesheet">
    <link href="{{ asset('css/my-style.css')             }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome-pro.min.css') }}" rel="stylesheet">
</head>
<body>
    <main>
        Atestado Médico
    </main>

    <!-- Bibliotecas -->
    <script src="{{ asset('js/jquery.min.js')           }}"></script>
    <script src="{{ asset('js/bootstrap.min.js')        }}"></script>
    <script src="{{ asset('js/jquery-ui.js')            }}"></script>
    <script src="{{ asset('js/moment.js')               }}"></script>
    <script src="{{ asset('lang/moment-pt-BR.js')       }}"></script>
    <script src="{{ asset('js/font-awesome-pro.min.js') }}"></script>
    
</body>
</html>
