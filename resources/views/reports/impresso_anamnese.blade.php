<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ANAMNESE - {{$pessoa}} - {{$anamnese->descr}}</title>

    <link rel="icon shortcut" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/normalize.min.css')        }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-print.min.css')  }}" rel="stylesheet">

    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0">
    <style>
        @page { margin: 0 }
        body  { margin: 0 }
        .sheet {
            margin: 0;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            page-break-after: always;
        }

        /** Paper sizes **/
        body.A3               .sheet { width: 297mm; height: 419mm }
        body.A3.landscape     .sheet { width: 420mm; height: 296mm }
        body.A4               .sheet { width: 210mm; height: 296mm }
        body.A4.landscape     .sheet { width: 297mm; height: 209mm }
        body.A5               .sheet { width: 148mm; height: 209mm }
        body.A5.landscape     .sheet { width: 210mm; height: 147mm }
        body.letter           .sheet { width: 216mm; height: 279mm }
        body.letter.landscape .sheet { width: 280mm; height: 215mm }
        body.legal            .sheet { width: 216mm; height: 356mm }
        body.legal.landscape  .sheet { width: 357mm; height: 215mm }

        /** Padding area **/
        .sheet.padding-10mm { padding: 10mm }
        .sheet.padding-15mm { padding: 15mm }
        .sheet.padding-20mm { padding: 20mm }
        .sheet.padding-25mm { padding: 25mm }

        /** For screen preview **/
        @media screen {
            body { background: #e0e0e0 }
            .sheet {
                background: white;
                box-shadow: 0 .5mm 2mm rgba(0,0,0,.3);
                margin: 5mm auto;
            }
        }

        /** Fix for Chrome issue #273306 **/
        @media print {
            body.A3.landscape          { width: 420mm }
            body.A3, body.A4.landscape { width: 297mm }
            body.A4, body.A5.landscape { width: 210mm }
            body.A5                    { width: 148mm }
            body.letter, body.legal    { width: 216mm }
            body.letter.landscape      { width: 280mm }
            body.legal.landscape       { width: 357mm }
        }
    </style>
    <style type="text/css" media="print">
        .sheet {
            zoom: 150%;
        }

        .group-fab {
            display: none !important;
        }
    </style>
    <style>
        .sheet {
            background: #FFF;
        }

        .mid-lane {
            background: black;
            height: calc(100% - 40px);
            width: 1px;
            position: absolute;
            left: calc(50% - 0.5px);
            top: 20px;
        }

        .report-title {
            /* display: flex; */
        }

        .report-title * {
            line-height: 1;
        }

        .report-logo {
            text-align: center;
        }

        .report-logo > img {
            height: 80px;
        }

        .report-header,
        .report-body,
        .report-footer {
            position: relative;
            width: 100%;
        }

        .header-left {
            /* padding: 20px 20px 20px 0; */
            padding: 20px 30px;
            width: 50%;
        }

        .header-right {
            /* padding: 20px 0 20px 20px; */
            padding: 20px 30px;
            width: 50%;
        }

        .table-header-scroll tr
        {
            background-color: #e8e8e8;
        }

        table {
            width: 100%;
        }

        table th,
        table td {
            padding: .4rem !important;
            vertical-align: top !important;
        }

        .tag-pedido-cancelado {
            background: #dc3545;
        }

        .tag-pedido-aberto {
            background: #dc8a35;
        }

        .tag-pedido-primary {
            background: #238DFC;
        }

        .tag-pedido-finalizado {
            background: #28a745;
        }

        .tag-pedido-aberto,
        .tag-pedido-finalizado,
        .tag-pedido-cancelado,
        .tag-pedido-primary {
            border-radius: .5rem;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1;
            margin-left: auto;
            margin-right: auto;
            padding: 5px 10px;
            text-align: center;
        }

        .d-grid {
            display: grid !important;
        }

        .group-fab {
            display: flex;
            position: fixed;
            bottom: 25px;
            right: 25px;
        }

        .btn-fab {
            border-radius: 2.5rem;
            color: #FFF;
            display: flex;
            font-size: 1.5rem;
            margin-left: 5px;
            padding: 0;
            height: 50px;
            width: 50px;
        }

        .btn-fab > img{
            margin: auto;
            height: 30px;
            width: 30px;
        }

    </style>
</head>

<body class="A4">
    <section class="sheet padding-10mm">
        <div class = "soma">
            <h2 style='text-align:center'>Relatório de Anamnese</h2>
            <img style='width: 180px;margin-top: -60px;opacity: 0.7;' src='{{ asset('img/logo_topo_limpo_on.png')}}'>
            <div style='width: 75%;margin-left: 177px;margin-top: -94px;'>
                <span>Anamnese nº. {{$anamnese->id}}</span><br>
                <span>Membro: {{$membro}}</span><br>
                <span>Pessoa: {{$pessoa}}</span>            
            </div>
            <h4 style='text-align:center;margin-top: 15px;'>{{$anamnese->descr}}</h4>
            <hr/>
            <h4 style='border-bottom: 1px solid;width: 100%;'>{{$pessoa}} </h4>
            <h4 style='text-align:end;margin-top: -35px;'>{{$anamnese_pessoa->dataformatada}} {{$anamnese_pessoa->hora}}</h4>
        </div>
        <div class="row">
            <table>
                <thead>
                    <th width='50%'>Pergunta</th>
                    <th width='50%'>Resposta</th>
                </thead>
            
                <tbody>
                    @for($i = 0; $i < sizeof($respostas); $i++)
                        <tr class = 'soma' style = 'font-size:12px;
                            @if($i%2==0)
                                background-color:#d8d8d8;
                                color:black
                            @endif
                        '>
                            <td style='color:black' width='50%'>{{$anamnese->perguntas[$i]->pergunta}}</td>      
                            <td style='color:black' width='50%'>{{$respostas[$i]}}</td>
                        </tr>
                    @endfor
                <tbody>
            </table>
        </div>
    </section>
    <section class="sheet padding-10mm" style = "display:none" id = "pag2">
        <div class="row">
            <table>
                <thead>
                    <th width='50%'>Pergunta</th>
                    <th width='50%'>Resposta</th>
                </thead>
                <tbody id = "pag2-tabela">
                </tbody>
            </table>
        </div>
    </section>

    <div class="group-fab">
        <div class="btn btn-fab btn-info" onclick="download_pdf()">
            <img src="/saude-beta/img/download.png">
        </div>
        <div class="btn btn-fab btn-primary" onclick="window.print()">
            <img src="/saude-beta/img/print.png">
        </div>
    </div>

    <!-- Bibliotecas -->
    <script src="{{ asset('js/jquery.min.js')           }}"></script>
    <script src="{{ asset('js/bootstrap-print.min.js')  }}"></script>
    <script src="{{ asset('js/jspdf.min.js')            }}"></script>
    <script type="text/javascript">
        function download_pdf() {
            window.print()
        }

        window.onload = function() {
            var elementos = document.getElementsByClassName("soma");
            var soma = 0;
            var pag2 = new Array();
            for (var i = 0; i < elementos.length; i++) {
                soma += elementos[i].offsetHeight;
                if (soma > document.getElementsByClassName("sheet")[0].offsetHeight - 100) pag2.push(elementos[i]);
            }
            if (pag2.length) {
                document.getElementById("pag2").style.display = "";
                for (var i = 0; i < pag2.length; i++) document.getElementById("pag2-tabela").appendChild(pag2[i]);
            }
        }
    </script>
</body>

</html>