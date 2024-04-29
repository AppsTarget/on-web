<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BORDERÔ</title>

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
    </style>
</head>

<body class="A4">
    <input type="hidden" value="{{$quebra = 0}}">
    <input type="hidden" value="{{$paginas = 0}}">
    @for($i = 0; $i < sizeof($aux_array); $i++)
        @if($i == 0)
            <section class="sheet padding-10mm">
                <div class="row">
                    <div style="position: absolute;width: 140px;top: 15px;opacity: .95;">
                        <img style="max-width: 100%;max-height: 100%;" src="{{ asset('/img/logo_topo_limpo_on.png') }}">
                    </div>
                    <div style="width:100%;line-height: 10px;margin-top: -2%;margin-bottom: 2%;">
                        <h4 style="text-align: center">
                            DISTRIBUIÇÃO DE LUCROS
                        </h4>
                        <p style="text-align: center;color: #d20000;">
                            {{ \App\Empresa::find($id_emp)->descr}} - {{ \App\Empresa::find($id_emp)->cidade}}
                        </p>
                        <h5 style="text-align: center">
                            {{ $profissional->nome_fantasia }}
                        </h5>
                    </div>
                    <div class="col-3" style="border: 2px solid;border-radius: 15px;min-height: 70px;">
                        <p style="text-align: center;margin-bottom: 5px;margin-top: 0px;">
                            <strong>
                                Período de apuração
                            </strong>
                        </p>
                        <div style="display: flex;justify-content: center;">
                            <p style="font-size: 14px;text-align: center;position: absolute;bottom: -10px;">
                                {{ $data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-3" style="border: 2px solid;border-radius: 15px;min-height: 70px;">
                        <p style="text-align: center;margin-bottom: 5px;margin-top: 0px;">
                            <strong>
                                Pessoas Atendidas
                            </strong>
                        </p>
                        <div style="display: flex;justify-content: center;">
                            <h4 style="position: absolute;bottom: -6px;font-weight: 1000;">
                                {{ $pessoas_atendidas }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-3" style="border: 2px solid;border-radius: 15px;min-height: 70px;">
                        <p style="text-align: center;margin-bottom: 5px;margin-top: 2px;line-height: 15px;">
                            <strong>
                                Atendimentos Confirmados
                            </strong>
                        </p>
                        <div style="display: flex;justify-content: center;">
                            <h4 style="position: absolute;bottom: -6px;font-weight: 1000;">
                                {{ $atendimentos_confirmados }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-3" style="border: 2px solid;border-radius: 15px;min-height: 70px;">
                        <p style="text-align: center;margin-bottom: 5px;margin-top: 0px;">
                            <strong>
                                Total a Receber
                            </strong>
                        </p>
                        <div style="display: flex;justify-content: center;">
                            {{-- <h4 style="position: absolute;bottom: -6px;font-weight: 1000;">R$ {{ number_format($valor_total,2,",",".") }}</h4> --}}
                            <h4 style="position: absolute;bottom: -6px;font-weight: 1000;" id="valor_total"></h4>
                        </div>
                    </div>
                    <h5 style="margin-top: 1%;color: #2100ff;font-size: 15px;border-bottom: 1px solid black;width: 100%;font-weight: 700;">
                        Extrato de pagamento
                    </h5>
                </div>
                <div>
                    <ul style='padding:0'>
        @endif
        @if ($agendamentos[0]->id_modalidade != 0)


            <div style="display:none">{{$quebra++}}</div>
            @if($quebra == 12 and $i < 12)
                    <div style="display: none">{{$paginas++}}</div>
                        <div style="background: #7b7b7b;width: 90%;height: 1px;position: absolute;bottom: 50px;">
                        </div>
                    <p style="position: absolute;bottom: 10px;right: 45px;">
                        {{$paginas}}/{{intval(sizeof($agendamentos)/11)}}
                    </p>
                </section>
                <section class="sheet padding-10mm"> 
                    <div style="display: none">
                        {{$quebra = 0}}
                    </div>
                        
            @endif
            @if($quebra == 14 and $i > 14)<div style="display: none">{{$paginas++}}</div>
                <div style="background: #7b7b7b;width: 90%;height: 1px;position: absolute;bottom: 95px;">
                </div>
                <p style="position: absolute;bottom: 55px;right: 45px;">{{$paginas}}/{{intval(sizeof($agendamentos)/11)}}</p>
                </section>
                <section class="sheet padding-10mm"> 
                <div style="display: none">{{$quebra = 0}}</div>
            @endif
            @if ($i%2 != 0)
                <li style="display: flex;margin: 2px -20px 2px -20px;padding: 5px 16px 0px 16px;">
            @else 
                <li style="display: flex;margin: 2px -20px 2px -20px;padding: 5px 16px 0px 16px;background: #ebebeb;">
            @endif
                <div style="width: 8%;height: 100%;">
                    <img style="max-width:100%;min-width:100%;max-height:100%;min-height:100%;object-fit: cover"src="http://vps.targetclient.com.br/saude-beta/img/areas/{{$agendamentos[$i]->id_modalidade}}.png">
                </div>
                <div style="padding: 0px 0px 0px 15px;width: 100%;">
                    <div style="width: 100%;font-size: 12px;">
                        <strong>{{ $agendamentos[$i]->descr_modalidade }}</strong>
                    </div>
                    <div style="display:flex"> 
                        <div style="display: flex;justify-content: space-between;width: 20%;font-size: 11px;">
                            <div>
                                <span>Contrato:</span>
                            </div>
                            <div>
                                <span>Associado:</span>
                            </div>
                        </div>
                        <div style="width: 15%;"></div>
                        <div style="font-size: 12px;display: flex;justify-content: space-between;width: 65%;">
                            <div>
                                <span>Agendamento: <strong>{{ date('d/m/Y', strtotime($agendamentos[$i]->data)) }} - {{ substr($agendamentos[$i]->hora,0, 5)}}</strong></span>
                            </div>
                            <div> 
                                <span>Valor</span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="display: flex;justify-content: space-between;font-size: 11px;width: 57%;">
                            <div>
                                <span>
                                    <strong>
                                        {{ $agendamentos[$i]->id_contrato, 0, 37 }}
                                    </strong>
                                </span>
                            </div>
                            <div style="width: 79%;">
                                <span>
                                    <strong>
                                        @if (substr($agendamentos[$i]->descr_pessoa,0,20) != $agendamentos[$i]->descr_pessoa)
                                            {{ substr($agendamentos[$i]->descr_pessoa, 0, 20) . "..."}}
                                        @else 
                                            {{ $agendamentos[$i]->descr_pessoa}}
                                        @endif
                                    </strong>
                                </span>
                            </div>
                        </div>
                        
                        <div style="font-size: 12px;display: flex;justify-content: space-between;width: 43%;">
                            @if ((
                                    $agendamentos[$i]->tipo_pagamento <> 'N' 
                                            || 
                                    ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1)) == 'Sem valor'
                                )  
                                && 
                                (
                                    App\Procedimento::find($agendamentos[$i]->id_modalidade)->tipo_de_comissao != 'F'
                                ))
                                <div style="color:red">
                                    @if(ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1)) == 'Sem valor' && App\Procedimento::find($agendamentos[$i]->id_modalidade)->tipo_de_comissao != 'F')
                                        <span><strong>*SEM VALOR</strong></span>
                                    @elseif ($agendamentos[$i]->tipo_pagamento == 'R')
                                        <span><strong>*RETORNO</strong></span>
                                    @elseif ($agendamentos[$i]->tipo_pagamento == 'E')
                                        <span><strong>*EXPERIMENTAL</strong></span>
                                    @elseif ($agendamentos[$i]->tipo_pagamento == 'C')
                                        <span><strong>*CORTESIA</strong></span>
                                    @endif
                                </div>
                            @else
                                <div>
                                    @if ($agendamentos[$i]->id_tipo_procedimento == 5)
                                        <span style="position: absolute;left: 42%;">EXPERIMENTAL</span>
                                        <span>Forma de Pagamento: EXPERIMENTAL</span>
                                    @elseif ($agendamentos[$i]->tipo_pagamento == 'C')
                                    <span style="position: absolute;left: 42%;">CORTESIA</span>
                                        <span>Forma de Pagamento: CORTESIA</span>
                                    @else
                                        @if ($agendamentos[$i]->tipo_de_comissao == '%')
                                            <span style="position: absolute;left: 42%;">%Comis...:<strong>{{ $percentual_de_comissao[$i] }}%</strong></span>
                                        @else 
                                            <span style="position: absolute;left: 42%;">Fixo...:<strong>R$ {{ number_format($percentual_de_comissao[$i],2,",",".") }}</strong></span>
                                        @endif
                                        <span>Forma de Pagamento: {{ ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1)) }}</span>
                                    @endif
                                </div>
                            @endif
                            <div style="font-size: 16px;margin-top: -5px;">
                                <span>
                                    <strong id="valores">
                                        @if ($agendamentos[$i]->id_tipo_procedimento == 5)
                                            {{number_format(0,2,",",".")}}
                                        @elseif ((in_array($agendamentos[$i]->tipo_pagamento, array("E", "R", "C"))
                                                     || 
                                                (ucfirst(substr($formas_pag[$i],0,1)) . strtolower(substr(strtr($formas_pag[$i], $caracteres_sem_acento),1)) == 'Sem valor'))  && \App\Procedimento::find($agendamentos[$i]->id_modalidade)->tipo_de_comissao != 'F')
                                            {{number_format(0,2,",",".")}}
                                        @elseif ($agendamentos[$i]->tipo_pagamento == 'C')
                                            {{number_format(0,2,",",".")}}
                                        @else
                                            {{number_format($valores[$i],2,",",".")}}
                                        @endif
                                    </strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endif
    @endfor
        </ul>
    </div>
    </section>

    <div class="group-fab">
        {{--
        <div class="btn btn-fab btn-info" onclick="download_xls({{ $id_emp.','.$id_membro.','.$id_contrato.','.$dinicial.','.$dfinal }})">
            <img src="/saude-beta/img/download.png">
        </div>
        --}}
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
                    valor_total = 0;
                    csvJSON = '".$csvJSON."';
                    csvJSON = $.parseJSON(csvJSON);
                    C = 0;
                    document.querySelectorAll('#valores').forEach(el => {
                        valor_total += parseFloat(el.innerHTML.replaceAll('R$', '').replaceAll(',', '').replaceAll('.', ''))/100
                    })
                    $('#valor_total').html(eval(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}))
                }, 500)
            })
        ";
        echo "</script>";
    @endphp
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
        function download_xls(id_emp, id_membro, id_contrato, dinicial, d_final) {
            // $(
            //     '/saude-beta/bordero/gerar-xls/' +'/'+ id_emp +'/'+ id_membro +'/'+ id_contrato +'/'+ dinicial +'/'+ d_final,
            //     function(data, status) {
            //         console.log(data, status)
            //     } 
            // )
            window.open('_blank', 'http://vps.targetclient.com.br/saude-beta/bordero/gerar-xls/' +'/'+ id_emp +'/'+ id_membro +'/'+ id_contrato +'/'+ dinicial +'/'+ d_final)
        }

        function csv2() {
            csvTitulos = ["MODALIDADE", "CONTRATO", "ASSOCIADO", "TIPO", "FORMA DE PAGAMENTO", "VALOR"];
            csvDados = new Array();
            for (var i = 0; i < csvJSON.length; i++) {
                var linha = "";
                for (x in csvJSON[i]) linha += csvJSON[i][x] + ";";
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






