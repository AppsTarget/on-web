<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token -->

    <title>tarCloud</title>

    <link rel="icon shortcut" href="{{ asset('Images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon"          href="{{ asset('Images/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/print-style.css')   }}" rel="stylesheet">
    <style>
        html {
            height: 100%;
        }
    </style>

</head>
<body onload="window.print(); window.onfocus=function(){ window.close();}" class="h-100 w-100">
    <div class="report-header w-100">
        <div class="w-50 float-right text-right">
            <b>DATA:</b> {{ date('d/m/Y', strtotime($prescricao->data)) }}
        </div>
        <div class="w-50" >
            <b>PACIENTE:</b> {{ $prescricao->paciente_nome }}
        </div>
    </div>

    <div class="report-body p-5 w-100">
        {!! $prescricao->corpo !!}
    </div>

    <div class="report-footer w-100 text-center">
        Prescrição gerada por sistema Target Client.
    </div>

    <!-- Bibliotecas -->
    <script src="{{ asset('js/jquery.min.js')                  }}" type="text/javascript"></script>
    <script src="{{ asset('js/app.js')                         }}" type="text/javascript"></script>
</body>
</html>
