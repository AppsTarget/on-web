<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LAUDO - {{$laudo->paciente}} - {{$laudo->dtCriacao}}</title>

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

        .paragrafo {
            margin-bottom:0;
            padding-bottom:1.5rem
        }
    </style>
</head>

<body class="A4">
    <section class="sheet padding-10mm">
        <!-- <h2 style='text-align:center'>Laudo On</h2> -->
        <div id = "cabecalho">
            <img style='width: 180px;margin-top: -45px;opacity: 0.7;margin-left: -20px;' src='{{ asset('img/logo_topo_limpo_on.png')}}'>
            <div style='width: 75%;margin-left: 177px;margin-top: -94px;'>
                <span id = "span_laudo">Laudo nº. {{$laudo->id}}</span><br>
                <span id = "span_membro">Membro: {{$laudo->profissional}}</span><br>
                <span id = "span_pessoa">Pessoa: {{$laudo->paciente}}</span>
            </div>
        </div>
        
        <hr/>

        <div class="row">
            <div class="container-fluid card" id="chart-content-tudo" style = "padding-bottom:3rem"></div>
            <div style="margin-top: 30px" id = "diag">
                @php
                    $linhas = preg_split("/\n/", $laudo->diagnostico);
                    $linhasCheias = array();
                    for ($i = 0; $i < sizeof($linhas); $i++) {
                        if (trim($linhas[$i]) != "") array_push($linhasCheias, '<p class="paragrafo" id="id'.$i.'" style = "visibility:hidden">'.$linhas[$i].'</p>');
                    }
                    for ($i = 0; $i < sizeof($linhasCheias); $i++) echo $linhasCheias[$i];
                @endphp
            </div>
        </div>
    </section>

    <div class="group-fab">
        <div class="btn btn-fab btn-primary" onclick="window.print()">
            <img src="/saude-beta/img/print.png">
        </div>
    </div>

    <!-- Bibliotecas -->
    <script src="{{ asset('js/jquery.min.js')           }}"></script>
    <script src="{{ asset('js/bootstrap-print.min.js')  }}"></script>
    <script src="{{ asset('js/jspdf.min.js')            }}"></script>
    <script src="{{ asset('js/chart.min.js')            }}"></script>
    <script type="text/javascript">
        function download_pdf() {
            window.print()

        }
        var textJson
        window.addEventListener('load', () => {
            principal(true);
        })

        function principal(repetir) {
            const el = document.getElementById("chart-content-tudo");
            var tamanho = el.offsetTop + el.offsetHeight + 40;
            var linhas = new Array();
            var paragrafos = document.getElementsByClassName("paragrafo");
            for (var i = 0; i < paragrafos.length; i++) {
                var texto = '<p class="paragrafo" id="' + paragrafos[i].id + '" style = "text-align:justify">' + paragrafos[i].innerHTML.trim() + '</p>';
                while (texto.indexOf("\n") > -1) texto = texto.replace("\n", "");
                linhas.push(texto);
            }
            document.getElementById("diag").innerHTML = "";
            const tamanhoFolha = document.getElementsByClassName("sheet")[0].offsetHeight;
            var contFolha = 0;
            for (var i = 0; i < linhas.length; i++) {
                if (tamanho > tamanhoFolha - 140) {
                    contFolha++;
                    document.getElementById("diag").innerHTML += "airjengscnpgoauwergç:AXf";
                    document.getElementById("diag").innerHTML += "<section id = 'folha" + contFolha + "' class='sheet padding-10mm'>" +
                            "<img style='width: 180px;margin-top: -45px;opacity: 0.7;margin-left: -20px;' src='http://vps.targetclient.com.br/saude-beta/img/logo_topo_limpo_on.png'>" +
                            "<div style='width: 75%;margin-left: 177px;margin-top: -94px;'>" +
                                "<span>" + document.getElementById("span_laudo").innerHTML + "</span><br>" +
                                "<span>" + document.getElementById("span_membro").innerHTML + "</span><br>" +
                                "<span>" + document.getElementById("span_pessoa").innerHTML + "</span>" +
                            "</div>" +
                            "<hr/>";
                    tamanho = el.offsetTop;
                }
                if (contFolha == 0) document.getElementById("diag").innerHTML += linhas[i];
                else                document.getElementById("folha" + contFolha).innerHTML += linhas[i];
                var ultimo = document.getElementsByClassName("paragrafo");
                ultimo = ultimo[ultimo.length - 1];
                tamanho += ultimo.offsetHeight;
            }
            while (document.body.innerHTML.indexOf("airjengscnpgoauwergç:AXf") > -1) document.body.innerHTML = document.body.innerHTML.replace("airjengscnpgoauwergç:AXf", "</section>");

            $.get('/saude-beta/IEC/getDataGrafico/{{$laudo->id}}',
            function(data, status) {
                data = $.parseJSON(data)
                document.getElementById("chart-content-tudo").innerHTML = '<canvas id = "chart-content' + 1 + '" style = "margin-bottom:-5rem">';
                try {
                    if ({{$laudo->id}} < 10) {
                        for (var i = 0; i < data.datasets[0].data.length; i++) data.datasets[0].data[i]--;
                    }
                    var radarChart = new Chart(document.getElementById("chart-content" + 1), {
                        type: 'radar',
                        data: data,
                        options: {
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            events: [],
                            scales: {
                                r: {
                                    beginAtZero: true,
                                    max: 3,
                                    ticks: {
                                        display: false // Hides the labels in the middel (numbers)
                                    }
                                }
                            },
                            scale: {
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    });
                } catch(err) {}
                document.getElementById("chart-content-tudo").style.height = document.getElementById("chart-content-tudo").offsetHeight;
                if (repetir) principal(false);
            })
        }
    </script>
</body>

</html>