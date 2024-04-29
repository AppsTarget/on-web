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
    <link href="{{ asset('css/font-awesome-pro.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-size: 20px;
            height: 297mm;
            margin: 0mm 0mm 0mm 70mm;
            width: 210mm;

            /* Esconder fundo */
            background: gray;
            opacity: 0;
            /* —————————————— */
        }

        p {
            line-height: 1.5;
            margin: 0;
            page-break-inside:auto
        }

        .report-header,
        .report-body,
        .report-footer {
            margin-bottom: 3rem;
            width: 210mm;
        }

        .report-body {
            margin-bottom: 2rem;
        }

        .row {
            margin: 0;
        }
        
        @media print {
            @page {
                size: portrait;
                margin: 0;
                margin-top: 50mm;
                margin-bottom: 15mm;
            }
            
            body {
                /* Esconder fundo */
                background: white;
                opacity: 1;
                /* —————————————— */
            }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h2 class="text-center">
            <u>{{ $documento->titulo }}</u>
        </h2>
    </div>

    <div class="report-body">
        <div class="row">
            <div class="col">
                <p style="text-transform:uppercase">
                    &nbsp;{!! str_replace("%nome_paciente%", $paciente->nome_fantasia, $documento->corpo) !!}
                </p>
            </div>
        </div>
    </div>

    <div class="report-footer">
        <div class="row">
            <div class="col">
                @php
                    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                    date_default_timezone_set('America/Sao_Paulo');
                    echo getEmpresaObj()->cidade . ', ' . strftime('%d de %B, %Y');
                @endphp
            </div>
        </div>

        <div class="row">
            <div class="col-4 offset-8 text-center">
                <p>{{ str_repeat("_", strlen(getProfissional()->nome_fantasia) + str_word_count(getProfissional()->nome_fantasia)) }}</p>
                <p style="text-transform:uppercase">{{ getProfissional()->nome_fantasia }}</p>
                <p>CRM - {{ getProfissional()->crm_cro }}</p>
                <p>MÉDICO</p>
            </div>
        </div>
    </div>

    <!-- Bibliotecas -->
    <script src="{{ asset('js/jquery.min.js')           }}"></script>
    <script src="{{ asset('js/bootstrap.min.js')        }}"></script>
    <script src="{{ asset('js/jquery-ui.js')            }}"></script>
    <script src="{{ asset('js/font-awesome-pro.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() { 
            window.print(); 
            setTimeout(function(){ 
                        window.close();
            }, 300);
        });
    </script>
    
</body>
</html>
