<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Contratos por período</title>

    <link rel="icon shortcut" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/normalize.min.css')        }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-print.min.css')  }}" rel="stylesheet">
    <style>
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
        body.A4               .sheet { width: 296mm; height: 210mm }
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
            body.A4.landscape { width: 297mm }
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
    </style>
</head>

<body class="A4">
    <input type="hidden" value="{{$quebra = 0}}">
    <input type="hidden" value="{{$paginas = 0}}">
    <section class="sheet padding-10mm">
            @for($i = 0; $i < sizeof($contratos); $i++)
                @if ($i == 0)
                    <div>
                        <div style="position: absolute;width: 140px;top: 15px;opacity: .95;">
                            <img style="max-width: 100%;max-height: 100%;" src="{{ asset('/img/logo_topo_limpo_on.png') }}">
                        </div>
                        <h3 class="text-center" style="margin-bottom:15px">Relatório de Contratos Por Período</h3>
                        <p class="text-center">{A partir de {}{$data_inicial}} até {{$data_final}} - {{getEmpresaObj()->descr}} - {{getEmpresaObj()->cidade}}</p>
                    </div>
                    <p class="text-center" style="margin: 35px 0px 5px 0px;">
                        <strong>{{getEmpresaObj()->descr}}</strong>
                    </p>
                    @if ($consultor != '0')
                        <span>{{$consultor}}<span>
                    @endif
                    <div style="width:100%; height:1px; background-color:black;"></div>
                
                    <table>
                        <thead>
                            <th>Data Ini.</th>
                            <th>Data Exp.</th>
                            <th>Contrato</th>
                            <th>Associado</th>
                            <th>Plano</th>
                            <th>Valor</th>
                            <th>Total</th>
                        </thead>
                @endif
                @if($quebra == 14)
                    </tbody>
                    </table>
                    </section>
                    <section class="sheet padding-10mm">
                        <div>
                            <div style="position: absolute;width: 140px;top: 15px;opacity: .95;">
                                <img style="max-width: 100%;max-height: 100%;" src="{{ asset('/img/logo_topo_limpo_on.png') }}">
                            </div>
                            <h3 class="text-center" style="margin-bottom:15px">Relatório de Contratos Por Período</h3>
                            <p class="text-center">A partir de {{$data_inicial}} até {{$data_final}} - {{getEmpresaObj()->descr}} - {{getEmpresaObj()->cidade}}</p>
                        </div>
                        <p class="text-center" style="margin: 35px 0px 5px 0px;">
                            <strong>{{getEmpresaObj()->descr}}</strong>
                        </p>
                        @if ($consultor != '0')
                            <span>{{$consultor}}<span>
                        @endif
                        <div style="width:100%; height:1px; background-color:black;"></div>
                    
                        <table>
                            <thead>
                                <th>Data Ini.</th>
                                <th>Data Exp.</th>
                                <th>Contrato</th>
                                <th>Associado</th>
                                <th>Plano</th>
                                <th>Valor</th>
                                <th>Total</th>
                            </thead>
                            <tbody>
                    <div style="display: none">{{ $quebra = 0 }}</div>  
                @endif
                <tr style="font-size:12px; text-transform:uppercase;
                    @if($i%2==0) 
                        background: #d7d7d7;
                    @endif ">
                    <td>{{date('d/m/Y', strtotime($contratos[$i]->data_inicial)) }}</td>
                    <td>{{date('d/m/Y', strtotime($contratos[$i]->data_final)) }}</td>
                    <td>{{$contratos[$i]->id_contrato }}</td>
                    <td>{{substr($contratos[$i]->associado, 0, 35) . '...' }}</td>
                    <td>{{substr($contratos[$i]->plano, 0, 25) . '...' }}</td>
                    <td>{{ number_format($contratos[$i]->valor,2,",",".") }} </td>
                    <td class='valores'>{{ number_format($contratos[$i]->total,2,",","") }} </td>
                </tr> 
                <div style="display: none">{{ $quebra++ }}</div>        
            @endfor
        </tbody>
        <p style='position: absolute;bottom: 55px;right: 40px;font-size: 35px;'>
            Total: 
            <strong id='valor-total'>

            </strong>
        </p>
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
            window.print();
            // var group_fab = $('.group-fab')[0],
            //     pdf = new jsPDF('p','mm',[297,210]);

            // $('.group-fab').remove();
            // $(window).scrollTop(0);
            // pdf.addHTML(
            //     $('.sheet')[0],
            //     function () {
            //         pdf.save('0.pdf');
            //         $('body').append(group_fab);
            //     }
            // );
        }
        window.addEventListener('load', () =>{
            setTimeout(() => {
                valor_total = 0;
                C = 0
                document.querySelectorAll(".valores").forEach(el => {
                    if (C === 0) {
                        valor_total = el.innerHTML.replace(',', '.')
                        C = 1
                    }
                    else valor_total += '+' + el.innerHTML.replace(',', '.')
                })
                $("#valor-total").html(eval(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}))
            }, 500)
        })

    </script>
</body>

</html>






