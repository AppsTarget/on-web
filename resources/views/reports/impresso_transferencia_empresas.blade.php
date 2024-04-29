<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Transferência Entre Empresas</title>

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

        .col-3{       
            max-width: 24%;
            margin: 3px;
        }
        * {
            font-family: 'Helvetica'
        }
        .tabela > thead, .tabela .header-tabela1{

        }
        .tabela > thead, .tabela > tbody { 
           line-height: 10px
        }
        .tabela  .header-tabela1 { 
            border-bottom: 1px solid black;
            border-radius: 10px
        }
        .tabela > thead > tr > th, 
        .tabela > tbody > tr > td {
            font-size: 11px;
            font-family: 'Helvetica'
        }
        .tabela2 > thead > tr > th, 
        .tabela2 > tbody > tr > td {
            font-size: 11px;
            font-family: 'Helvetica'
        }
    </style>
</head>

<body class="A4">
    <input type="hidden" value="{{$quebra = 0}}">
    <input type="hidden" value="{{$paginas = 0}}">
    <section class="sheet padding-10mm">
        <h1 align = "center" style = "font-size: 2rem">TRANSFERENCIA ENTRE EMPRESAS</h1>
        <p align = "center" style = "font-size: 1rem;margin-bottom: 50px">- {{$data_inicial}} a {{$data_final}} -</p>
        @for($k = 0; $k < sizeof($resultado); $k++)
            <div>
                <div>
                    <h4 align = "center" style = "font-size: 1.50rem;font-weight: bold">{{ $resultado[$k]["origem"]["descr"] }}</h4>
                </div>
                @for($i = 0; $i < sizeof($resultado[$k]["origem"]["destino"]); $i++)
                    <h5 style="font-size: 16px;margin-bottom: 30px;margin-top: 55px;font-weight: bold;">Contratos usados em:  {{ $resultado[$k]["origem"]["destino"][$i]["descr"] }}</h5>
                    
                    @for($l = 0; $l < sizeof($resultado[$k]["origem"]["destino"][$i]["contratos"]); $l++)
                        <table class="tabela">
                            <thead>
                                <tr style="background-color: #b1b1b1;">
                                    <th class="text-center" width="10%">CONTRATO</th>
                                    <th class="text-left" width="12.5%">DATA INICIAL</th>
                                    <th class="text-left" width="12.5%">DATA FINAL</th>
                                    <th class="text-left" width="50%">ASSOCIADO</th>
                                    <th class="text-right" width="15%">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="header-tabela1">
                                    <td class="text-center">{{$resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["id"]}}</td>
                                    <td>{{$resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["pedido_data"]}}</td>
                                    <td>{{$resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["pedido_validade"]}}</td>
                                    <td>{{$resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["associado"]}}</td>
                                    <td class="text-right">R$ {{number_format($resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["pedido_total"],2,",",".")}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="5" style="padding: 0 !important">
                                        <table class="tabela2">
                                            <thead>
                                                <tr>
                                                    <th>AGENDAMENTO</th>
                                                    <th>MEMBRO</th>
                                                    <th class="text-right">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($j = 0; $j < sizeof($resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["agendamentos"]); $j++)
                                                    <tr>
                                                        <td>{{$resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["agendamentos"][$j]["data"]}}</td>
                                                        <td>{{$resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["agendamentos"][$j]["membro"]}}</td>
                                                        <td class="text-right">R$ {{number_format($resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["agendamentos"][$j]["valor"],2,",",".")}}</td>
                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="2">
                                                        <p style="text-align: end;font-weight: bold;font-size: 15px;">VALOR TOTAL: R$ {{number_format($resultado[$k]["origem"]["destino"][$i]["contratos"][$l]["soma"],2,",",".")}}</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="height: 0px;background: #555555;margin-top: -15px;margin-bottom: 50px;"></div>
                        @if($l == 4) </section><section class="sheet padding-10mm"> @endif
                    @endfor
                        </section><section class="sheet padding-10mm">
                @endfor
            </div>
        @endfor
        <h4 align="center" style="margin-bottom: 50px; font-weight: bold">RESUMO</h4>
        <h5 style="border-bottom: 2px solid black;margin-bottom: 5px; font-weight: bold">TOTAL DAS TRANSAÇOES</h5>
        @for($cont=0; $cont < sizeof($resultado); $cont++)
            @for($cont2=0; $cont2 < sizeof($resultado[$cont]["origem"]["destino"]); $cont2++)
                <div style="display: flex; align-items: center; justify-content: space-between">
                    <div style="display: flex;align-items: center;justify-content: space-between;width: 86%;">
                        <p style="margin: 0;min-width: 44%;font-size: 13px;max-width: 44%;">{{$resultado[$cont]["origem"]["descr"]}}</p>
                        <div style="height: 26px">
                            <img style="max-width:100%; min-width: 100%; max-height: 100%; min-height: 100%" src="{{ asset('img/seta-direita2.png') }}">
                        </div>
                        <p style="margin: 0; font-size: 13px;min-width: 45%;max-width: 45%;">{{$resultado[$cont]["origem"]["destino"][$cont2]["descr"]}}</p>
                    </div>
                    <p style="margin: 0">R$ {{number_format($resultado[$cont]["origem"]["destino"][$cont2]["soma"],2,",",".")}}</p>
                </div>
            @endfor
        @endfor

        {{--
        <h5 style="margin-top: 40px; border-bottom: 2px solid black;margin-bottom: 5px; font-weight: bold">TOTAL A TRANSFERIR</h5>
        @for($cont=0; $cont < sizeof($empresas); $cont++)
            @for($cont2=0; $cont2 < sizeof($empresas[$cont]->empresas_divergentes); $cont2++)
                <div style="display: flex; align-items: center; justify-content: space-between">
                    <div style="display: flex;align-items: center;justify-content: space-between;width: 86%;">
                        <p style="margin: 0;min-width: 44%;font-size: 13px;max-width: 44%;">{{$empresas[$cont]->descr}}</p>
                        <div style="height: 26px">
                            <img style="max-width:100%; min-width: 100%; max-height: 100%; min-height: 100%" src="{{ asset('img/seta-direita2.png') }}">
                        </div>
                        <p style="margin: 0; font-size: 13px;min-width: 45%;max-width: 45%;">{{$empresas[$cont]->empresas_divergentes[$cont2]->descr}}</p>
                    </div>
                    <p style="margin: 0">R$ {{number_format($empresas[$cont]->empresas_divergentes[$cont2]->total_a_transferir,2,",",".")}}</p>
                </div>
            @endfor
        @endfor
        --}}
    </section>

    <div class="group-fab">
        {{--<div class="btn btn-fab btn-info" onclick="window.print()">
            <img src="/saude-beta/img/download.png">
        </div>--}}
        <div class="btn btn-fab btn-primary" onclick="csv2()">
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
    <script src="{{ asset('js/reports.js')              }}"></script>
    @php
        echo "<script type = 'text/javascript'>";
        echo "
            var csvJSON;
            window.addEventListener('load', () =>{
                setTimeout(() => {
                    csvJSON = '".$csvJSON."';
                    csvJSON = $.parseJSON(csvJSON);
                    valor_total = 0;
                    C = 0
                    document.querySelectorAll('.valores').forEach(el => {
                        if (C === 0) {
                            valor_total = el.innerHTML.replace(',', '.')
                            C = 1
                        }
                        else valor_total += '+' + el.innerHTML.replace(',', '.')
                    })
                    $('#valor-total').html(eval(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}))
                }, 500)
            })
        ";
        echo "</script>";
    @endphp
    <script type="text/javascript">
        function download_pdf() {
            window.print();
        }

        function csv2() {
            csvTitulos = [
                "ORIGEM",
                "DESTINO",
                "CONTRATO",
                "CONTRATADO EM",
                "VALIDADE",
                "ASSOCIADO",
                "VALOR DO CONTRATO",
                "AGENDAMENTO",
                "MEMBRO",
                "VALOR"
            ];
            csvDados = new Array();
            for (var i = 0; i < csvJSON.length; i++) {
                var linha = "";
                for (x in csvJSON[i]) {
                    var val = csvJSON[i][x];
                    if (x == "pedido_total") val = val.replace(".", ","); 
                    else if (x == "data") val = val.replace("às", "");
                    linha += val + ";";
                }
                csvDados.push(linha);
            }
            var dados = new Array();
            dados.push(csvTitulos.join(";"));
            for (var i = 0; i < csvDados.length - 1; i++) dados.push(csvDados[i].substring(0, csvDados[i].length - 1));
            csvMain(dados);
        }
    </script>
</body>

</html>






