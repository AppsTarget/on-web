<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Agendamentos</title>

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
    <link rel="icon shortcut" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <link rel="icon"          href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/bootstrap.min.css')        }}" rel="stylesheet">
    <link href="{{ asset('css/my-style.css')             }}" rel="stylesheet">
    <link href="{{ asset('css/mystyle-mobile.css')             }}" rel="stylesheet">
    <link href="{{ asset('css/summernote.css')           }}" rel="stylesheet">
    <link href="{{ asset('css/datepicker.min.css')       }}" rel="stylesheet">
    <link href="{{ asset('css/colorpicker.css')          }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome-pro.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/slick.css')          }}" rel="stylesheet">
    <link href="{{ asset('css/slick-theme.css') }}" rel="stylesheet">
</head>

<body class="A4">
    <input type="hidden" value="{{$quebra = 0}}">
    <input type="hidden" value="{{$paginas = 0}}">
    <input type="hidden" value="{{ $dia_temp = 0 }}">
    <section class="sheet padding-10mm">
        <div class="row">
            <ul style="display: flex;
            justify-content: center;
            flex-wrap: wrap;">
                @foreach($agendamentos AS $agendamento)
                    @if ($agendamento->data != $dia_temp && $quebra != 8)
                        <hr>
                            <h4 style="color:#212529">{{ date("d/m/Y", strtotime($agendamento->data)) }}</h4>
                        <input type="hidden" value="{{ $dia_temp = $agendamento->data }}">
                    @endif
                    @if($quebra == 8)
                        </ul></div></section>
                        <section class="sheet padding-10mm">
                            <div class="row">
                                <ul style="display: flex;
                                justify-content: center;
                                flex-wrap: wrap;">
                                <hr>
                                <h4 style="color:#212529">{{ date("d/m/Y", strtotime($agendamento->data)) }}</h4>
                            <input type="hidden" value="{{ $dia_temp = $agendamento->data }}">
                        <input type="hidden" value="{{$quebra = 0}}">
                    @endif
                    @if ($agendamento->id)
                        <li style="background:{{ $agendamento->cor_status }}; color: {{ $agendamento->cor_letra }};max-height: 94px;min-width: 100%;margin-bottom: 20px;cursor: pointer">
                            <div class="my-1 mx-1 d-flex">
                            <img class="foto-paciente-agenda" src="/saude-beta/img/pessoa/{{ $agendamento->id_paciente }}.jpg" onerror="this.onerror=null;this.src='/saude-beta/img/paciente_default.png'">
                            <div>
                                <p class="col p-0">
                                    <span class="ml-0 my-auto" style="font-weight:600">
                                            {{substr($agendamento->hora, 0, 5) . '  -  ' . strtoupper($agendamento->nome_paciente)}}
                                    </span>
                                </p>
                                <p class="tag-agenda" style="font-weight:400">
                                        {{$agendamento->nome_profissional . ' | ' }}
                                        @if ($agendamento->descr_procedimento != null) {{$agendamento->descr_procedimento . ' | ' }}@endif
                                        @if ($agendamento->tipo_procedimento != null) {{$agendamento->tipo_procedimento . ' | ' }} @endif
                                        @if ($agendamento->convenio_nome != null) {{$agendamento->convenio_nome}}
                                        @else Particular @endif
                                </p>
                            </div>

                        <div class="tags">
                        </div>

                        </div>
                        </li>
                    @endif
                    <input type="hidden" value="{{$quebra++ }}">
                @endforeach
            <ul>
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
                document.querySelectorAll("#valores").forEach(el => {
                    if (C === 0) {
                        valor_total = el.innerHTML.replace(',', '.')
                        C = 1
                    }
                    else valor_total += '+' + el.innerHTML.replace(',', '.')
                })
                $("#valor_total").html(eval(valor_total).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'}))
            }, 500)
        })
    </script>
</body>

</html>






