<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>IEC - {{$pessoa}} - {{$IEC->descr}}</title>

    <link rel="icon shortcut" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/normalize.min.css')        }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-print.min.css')  }}" rel="stylesheet">
    <style>
        @page { margin: 0 }
        body { margin: 0 }
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
        .resposta{
            width: 75%;
            padding: 1px 10px;
            border-radius: 40px;
            color: white;
        font-weight: bolder;
        }

    </style>
</head>

<body class="A4">
    <input type="hidden" value="{{$quebra = 0}}">
    <input type="hidden" value="{{$paginas = 0}}">
    @for($i = 0; $i < sizeof($respostas); $i++)
        @if ($i == 0)
        <section class="sheet padding-10mm">
            <h2 style='text-align:center'>Relatório de IEC</h2>
            <img style='width: 180px;margin-top: -60px;opacity: 0.9;' src='{{ asset('img/logo_topo_limpo_on.png')}}'>
            <div style='width: 75%;margin-left: 177px;margin-top: -94px;'>
            <span>IEC nº. {{$IEC->id}}</span><br>
            <span>Membro: {{$membro}}</span><br>
            <span>Pessoa: {{$pessoa}}</span>
            
            </div>
            <h4 style='text-align:center;margin-top: 15px;'>{{$IEC->descr}}</h4>
            <hr/>
            <h4 style='border-bottom: 1px solid;width: 100%;'>{{$pessoa}} </h4>
            <div>
        @endif
            @if ($quebra > 11 && $i <= 12) </section>
                <section class="sheet padding-10mm"> 
                    <h4 style='border-bottom: 1px solid;width: 100%;'>{{$pessoa}} </h4>
                    <div>
                <div style="display:none">{{ $quebra = 0 }}</div>
            @elseif ($quebra > 14) </section>
                <section class="sheet padding-10mm"> 
                    <h4 style='border-bottom: 1px solid;width: 100%;'>{{$pessoa}} </h4>
                    <div>
                <div style="display:none">{{ $quebra = 0 }}</div>
            @endif
                @if($i%2==0)
                    <div style='background-color: #d8d8d8;height: 56px;margin-bottom: 10px;'>
                @else
                    <div>
                @endif
                    <p>P: {{$IEC->perguntas[$i]->pergunta}}</p>
                    @if ($valores[$i] == 1)       
                        <div style='display: flex; margin-top: -15px;'>
                            <p>R:</p> <p class='resposta' style='background-color:#dc3545'>{{$respostas[$i]}}</p>
 
                        @elseif ($valores[$i] == 2)
                        <div style='display: flex; margin-top: -15px;'>
                            <p>R:</p> <p class='resposta' style='background-color:#e6e629'>{{$respostas[$i]}}</p>
                        @elseif ($valores[$i] == 3)
                        <div style='display: flex; margin-top: -15px;'>
                            <p>R:</p> <p class='resposta' style='background-color:#28a745'>{{$respostas[$i]}}</p>
                        @else
                        <div style='display: flex; margin-top: -15px;'>
                            <p>R:</p> <p class='resposta' style='background-color:#238DFC'>{{$respostas[$i]}}</p>
                    @endif
                    <div>
                    @foreach ($id_areas_sugeridas[$i] as $ids)
                        @if ($ids->id_area != '')
                            <img style='width: 41px;position: relative;bottom: 10px;' src="{{ asset('img/areas/' . $ids->id_area . '.png') }}" >
                        @endif
                    @endforeach
                    </div>
                </div>
                </div>
                <div style="display:none">{{ $quebra++ }}</div>
            @endfor 
            <h4 style="margin: 35px 0px -30px 0px;">Observações</h4>
            <div style="margin: 45px 0px 50px 0px;">{{ $obs }}</div>
        </div>
    </section>

    <div class="group-fab">
        <div class="btn btn-fab btn-info" onclick="window.print()">
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
    </script>
</body>

</html>






