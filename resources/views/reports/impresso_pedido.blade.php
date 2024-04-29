<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ getEmpresaObj()->descr }} - Contrato #{{ str_pad($pedido_header->num_pedido, 6, "0", STR_PAD_LEFT) }}</title>

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

        tr {
            font-size: 13px !important;
        }
        /** Paper sizes **/
        body.A3               .sheet { width: 297mm; height: 419mm }
        body.A3.landscape     .sheet { width: 420mm; height: 296mm }
        body.A4               .sheet { width: 210mm; min-height: 296mm }
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
            padding: .75rem !important;
            vertical-align: middle !important;
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
        .table-header-scroll tbody tr{
            background: white
        }

    </style>
</head>

<body class="A4">
    <section class="sheet padding-10mm">
        <div class="row">
            {{-- @if ($emp_logo != null)
                <div class="report-logo col">
                    <img src="data:image/jpg;base64,{{ $emp_logo }}">
                </div>
            @endif --}}
            <div class="report-title col d-grid">
                <h4 class="text-center mt-auto">
                    Espelho da Venda
                    <br>
                    Nº #{{ str_pad($pedido_header->num_pedido, 6, "0", STR_PAD_LEFT) }}
                    <br>
                    <p style="font-size: 20px;margin: 15px 1px 1px 1px;">{{$pedido_header->descr_emp}}</p>
                </h4>
                @if ($pedido_header->status == 'F')
                    <div class="tag-pedido-finalizado mb-auto">
                        Finalizado
                    </div>
                @elseif ($pedido_header->status == 'E')
                    <div class="tag-pedido-aberto mb-auto">
                        Aprovação do Cliente
                    </div>
                @elseif ($pedido_header->status == 'A')
                    <div class="tag-pedido-primary mb-auto">
                        Em Edição
                    </div>
                @else
                    <div class="tag-pedido-cancelado mb-auto">
                        Cancelado
                    </div>
                @endif
            </div>
        </div>
        <div class="report-header d-flex" style="margin-bottom:1rem">
            <div class="header-left">
                <h4>Dados do associado</h4>
                <div class="resumo-info">
                    <span class="custom-label-form">Nome</span>
                    <h5>{{ $pedido_header->descr_paciente }}</h5>
                </div>
                <div class="resumo-info">
                    <span class="custom-label-form">Assinado?</span>
                    @if ($pedido_header->assinado == 'N') 
                        <h5>Não</h5>
                    @else 
                        <h5>Sim</h5>
                    @endif
                </div>
            </div>
            <div class="mid-lane"></div>
            <div class="header-right">
                <h4>Dados Gerais</h4>
                <div class="d-flex">
                    <div class="resumo-info">
                        <span class="custom-label-form">Consultor de Vendas</span>
                        <h5>{{ $pedido_header->descr_prof_exa }}</h5>
                    </div>

                    <div class="resumo-info ml-auto">
                        <span class="custom-label-form">Data de Validade</span>
                        <h5>{{ date('d/m/Y', strtotime($pedido_header->data_validade)) }}</h5>
                    </div>
                </div>

                <div class="resumo-info">
                    <span class="custom-label-form">Observação</span>
                    <h5>
                        @if ($pedido_header->obs == null || $pedido_header->obs == '')
                            Sem Observação
                        @else
                            {{ $pedido_header->obs }}
                        @endif
                    </h5>
                </div>
            </div>
        </div>

        <div class="report-body">
            <div class="row">
                <div class="col-12">
                    <h4>Planos</h4>
                </div>
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <div class="custom-table">
                        <div class="table-header-scroll">
                            <table>
                                <thead>
                                    <th width="50%">Plano</th>


                                    {{-- <th width="10%" class="text-right">Qtde.</th> --}}
                                    {{-- <th width="15%" class="text-right">Pessoa</th> --}}
                                    

                                    {{-- <th width="10%" class="text-right">Total (R$)</th> --}}
                                    <th width="10%"  class="text-center">Atvs. Rest.</th>
                                    <th width="10%"  class="text-center">Atvs. total</th>
                                    <th width="10%"  class="text-center">Vigência</th>
                                    <th width="15%" class="text-right">Valor</th>
                                </thead>
                                <tbody>
                                    @foreach ($planos as $plano)
                                    <tr>
                                        <td width="50%">{{ $plano->descr }}</td>
                                        <td width="10%" class="text-center" style="font-weight:bolder">
                                            {{ $plano->max_atv - $plano->atv_consu  }}
                                        </td>
                                        <td width="10%" class="text-center" style="font-weight:bolder">
                                            {{ $plano->max_atv }}
                                        </td>
                                        <td width="10%" class="text-center">{{ $plano->vigencia }} dias</td>
                                        <td width="15%" class="text-right">R$ {{  number_format($plano->valor,2,",",".") }}</td>
                                        </>
                                        
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h4>Condições de Pagamento</h4>
                </div>
                <div class="col-12">
                    <div class="custom-table">
                        <div class="table-header-scroll">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="55%">Forma de Pagamento</th>
                                        <th width="15%" class="text-right">Nº Parcela</th>
                                        <th width="15%" class="text-right">Valor</th>
                                        <th width="15%" class="text-right">Vencimento</th>

                                        {{-- <th width="15%" class="text-right">À Vista (R$)</th>
                                        <th width="15%" class="text-right">À Prazo (R$)</th> --}}

                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div>
                            <table id="table-resumo-pedido-formas-pag" class="table table-hover">
                                <thead>
                                    @if($antigo == 1)
                                        @foreach ($parcelas as $parcela)
                                            <tr>
                                                <td width="55%">
                                                    {{ $parcela->descr_forma_pag }}
                                                </td>
                                                <td width="15%" class="text-right">
                                                    {{ $parcela->parcela }}
                                                </td>
                                                <td width="15%" class="text-right valor">
                                                    {{ number_format($parcela->valor, 2, ',', '.') }}
                                                </td>
                                                <td width="15%" class="text-right">
                                                    {{ date('d/m/Y', strtotime($parcela->vencimento)) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else 
                                    @foreach ($pedido_formas_pag as $forma_pag)
                                            @foreach ($forma_pag->parcelas as $parcela)
                                                <tr>
                                                    <td width="55%">
                                                        {{ $forma_pag->descr_forma_pag }}
                                                    </td>
                                                    <td width="15%" class="text-right">
                                                        {{ $parcela->parcela }}
                                                    </td>
                                                    <td width="15%" class="text-right valor">
                                                        {{ number_format($parcela->valor, 2, ',', '.') }}
                                                    </td>
                                                    <td width="15%" class="text-right">
                                                        {{ date('d/m/Y', strtotime($parcela->vencimento)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                    {{-- @endif --}}
                                </thead>
                            </table>
                            <h3 align="right">Total: <strong id="total"></strong></h3>
                        </div>
                    </div>
                </div>
            </div>
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
            var group_fab = $('.group-fab')[0],
                pdf = new jsPDF('p','mm',[297,210]);

            $('.group-fab').remove();
            $(window).scrollTop(0);
            pdf.addHTML(
                $('.sheet')[0],
                function () {
                    pdf.save('{{ getEmpresaObj()->descr }} - Contrato #{{ str_pad($pedido_header->num_pedido, 6, "0", STR_PAD_LEFT) }}.pdf');
                    $('body').append(group_fab);
                }
            );
        }
        window.addEventListener('load', () => {
            valor_total = 0
            document.querySelectorAll('.valor').forEach(el => {
                valor_total += parseFloat(el.innerHTML.replace('.', '').replace(',','.'))
            })
            document.querySelector('#total').innerHTML = "R$ " + parseFloat(valor_total).toFixed(2).replace(".", ",");
            if (screen.width < screen.height) $(".group-fab").css("display", "none");
        })
    </script>
</body>

</html>
