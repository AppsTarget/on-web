@extends('layouts.app')

@section('content')
@include('.components.main-toolbar')
<style>
    .td-cockpit{
        border-right: 1px solid;
        width: 12%;
        line-height: 0;
    }
    .td-cockpit2{
        border-right: 1px solid;
        width: 12%;
        line-height:0;
    }
    .td-cockpit div, .td-cockpit2 div {
        margin-right: 15px
    }
    .loader {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
</style>
<div class="row h-100 m-0" style='background: #e4e4e4;'>
    <div id="loading-cockpit"  style="  position: fixed;
                                        background: white;
                                        z-index: 9;
                                        opacity: 0.8;
                                        display: flex;
                                        justify-content: center;
                                        width: 100%;
                                        height: 500%;
                                        padding: 6% 0px 0px 0px;">
        <div>
            <div>
                <img src="http://vps.targetclient.com.br/saude-beta/img/logo_topo_limpo_on.png">
            </div>
            <div class='d-flex' style='justify-content: center'>
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="col-12" style='display: flex;justify-content: space-between;margin-top: 30px;color: #2e434e;'>
            <h2>Dashboard</h2>
            <div>
                {{-- <button onclick='filtrar_cockpit_data()'>atualizar</button> --}}
                <label for="periodo-cockpit" style="position: absolute;bottom: 4px;right: 180px;">Período:</label>
                <select type='date' id='periodo-cockpit' class='custom-select' name="periodo-cockpit"
                onchange='filtrar_cockpit_data();' style="">  
                </select>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 5px;">
            <div style='color: #2e434e;'>
                <h3>Associados</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"ativos")'>
                            <img title='Quantidade de associados com contratos vigentes no período' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Ativos</span>
                                <h1 id='n-ativos'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"novos")'>
                            <img title='Quantidade de pessoas que se associaram no período' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Novos</span>
                                <h1 id='n-novos'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"renovados")'>
                            <img title='Quantidade de associados que, durante o período, fizeram novos contratos ao término de outros' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Renovados</span>
                                <h1 id='n-renovados'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"resgatados")'>
                            <img title='Quantidade de associados que, durante o período, voltaram a fazer novos contratos depois de algum tempo sem contratar' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Resgatados</span>
                                <h1 id='n-resgatados'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"perdidos")'>
                            <img title='Quantidade de associados cujos contratos venceram no mês anterior ao período selecionado e não renovaram seus contratos no período' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Perdidos</span>
                                <h1 id='n-perdidos'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"iec")'>
                            <img title='Total de IECs realizados no período' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Total IECs</span>
                                <h1 id='n-totalIECs'>0</h1>
                            </div>
                        </td>

                        {{-- <td class='td-cockpit' onclick='abrirModalCockpit(this,"iecNConv")'>
                            <img title='Quantidade de pessoas que não contrataram um Plano dentro de um mês após realizar um IEC.' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Não Conv.</span>
                                <h1 id='n-iecNConv'>0</h1>
                            </div>
                        </td>

                         --}}
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"iecConv")'>
                            <img title='Quantidade de pessoas que contrataram um plano dentro de um mês após realizar um IEC' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>IEC conv.</span>
                                <h1 id='n-iecConv'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit' onclick='abrirModalCockpit(this,"iecConv")'>
                            <img title='Porcentagem de pessoas que contrataram um plano dentro de um mês após realizar um IEC' id="travar-escolha-ag-status"class="ico-info" style="position: relative;left: -140px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Taxa conv.</span>
                                <h1 id='n-td-cockpit'>0%</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 5px;margin-top: 15px;">
            <div style='color: #2e434e;'>
                <h3>Atendimentos</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"agendamentos_dia")'>
                            <img title='Quantidade de agendamentos no dia' id="travar-escolha-ag-status"class="ico-info" style="position:relative;left: -194px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Agendamentos do dia</span>
                                <h1 id="agendamentos_dia">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"agendamentos_canc_dia")'>
                            <img title='Quantidade de agendamentos cancelados no dia' id="travar-escolha-ag-status"class="ico-info" style="position:relative;left: -194px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Cancelados no dia</span>
                                <h1 id="agendamentos_cancelados_dia">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"agendamentos_atend_dia")'>
                            <img title='Quantidade de agendamentos atendidos no dia' id="travar-escolha-ag-status"class="ico-info" style="position:relative;left: -194px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Atendidos no dia</span>
                                <h1 id="atend_dia">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"agendamentos_atend_mes")'>
                            <img title='Quantidade de agendamentos atendidos no mês selecionado' id="travar-escolha-ag-status"class="ico-info" style="position:relative;left: -194px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Atendidos no mês</span>
                                <h1 id='atend_mes'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"pessoas_atend_mes")'>
                            <img title='Quantidade de pessoas atendidas no mês selecionado' id="travar-escolha-ag-status"class="ico-info" style="position:relative;left: -194px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Pessoas atendidas</span>
                                <h1 id='pessoas_atend'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'  onclick='abrirModalCockpit(this,"pessoas_atend_cortesia")'>
                            <img title='Quantidade de atendimentos cortesia no mês selecionado' id="travar-escolha-ag-status"class="ico-info" style="position:relative;left: -194px;top: 8px;width: 20px;max-width: 18px;" src="{{ asset('/img/icone-de-informacao.png') }}">
                            <div>
                                <span>Atendimentos cortesia</span>
                                <h1 id='pessoas_atend_cortesia'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 25px;margin-top: 15px;">
            <div style='color: #2e434e;'>
                <h3>Faturamento - Aluguel</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"aluguel_dia")' style = "width:11.7%">
                            <div>
                                <span>Do dia</span>
                                <h1 id='aluguel_dia'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"aluguel_mes")'>
                            <div>
                                <span>Do mês</span>
                                <h1 id='aluguel_mes'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"aluguel_semestre")' style = "width:13.6%">
                            <div>
                                <span>Do semestre</span>
                                <h1 id='aluguel_semestre'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 25px;margin-top: 15px;">
            <div style='color: #2e434e;'>
                <h3>Faturamento - Habilitação</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_hab_dia")'>
                            <div>
                                <span>Do dia</span>
                                <h1 id='faturamento_hab_dia'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_hab_mes")'>
                            <div>
                                <span>Do mês</span>
                                <h1 id='faturamento_hab_mes'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_hab_semestre")'>
                            <div>
                                <span>Do semestre</span>
                                <h1 id='faturamento_hab_semestre'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 25px;margin-top: 15px;">
            <div style='color: #2e434e;'>
                <h3>Faturamento - Reabilitação</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_reab_dia")'>
                            <div>
                                <span>Do dia</span>
                                <h1 id='faturamento_reab_dia'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_reab_mes")'>
                            <div>
                                <span>Do mês</span>
                                <h1 id='faturamento_reab_mes'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_reab_semestre")'>
                            <div>
                                <span>Do semestre</span>
                                <h1 id='faturamento_reab_semestre'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 25px;margin-top: 15px;">
            <div style='color: #2e434e;'>
                <h3>Faturamento Total</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_dia")'>
                            <div>
                                <span>Do dia</span>
                                <h1 id='faturamento_dia'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_mes")'>
                            <div>
                                <span>Do mês</span>
                                <h1 id='faturamento_mes'>0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2' onclick='abrirModalCockpit(this,"faturamento_semestre")'>
                            <div>
                                <span>Do semestre</span>
                                <h1 id='faturamento_semestre'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
                <div style="width: 100%;display: flex;justify-content: space-between;">
                    <div id='div-grafico1' style="width: 50%">
                        <h3 style="color: #2e434e;margin-top: 25px;font-size: 20px;text-align: center;">Evolução do Faturamento nos Últimos 6 meses</h3>
                        <canvas id="grafico1" width="650" height="350"></canvas>
                    </div>
                    <div id='div-grafico2' style="width: 50%">
                        <h3 style="color: #2e434e;margin-top: 25px;font-size: 20px;text-align: center;">Quantidade por Modalidade</h3>
                        <canvas id="grafico2" width="650" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 15px;background-color: white;border-radius: 5px;margin-top: 15px;">
            <div style='color: #2e434e;'>
                <h3>Contas a Receber</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2'
                            @if (Auth::user()->id_profissional != 28480002313 && Auth::user()->id_profissional != 443000000)
                                onclick='abrirTitulosReceber("t")'
                            @endif
                        >
                            <div>
                                <span>Em Atraso</span>
                                <h1 id="receber-atraso">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'
                            @if (Auth::user()->id_profissional != 28480002313 && Auth::user()->id_profissional != 443000000)
                                onclick='abrirTitulosReceber("d")'
                            @endif
                        >
                            <div>
                                <span>Vencem Hoje</span>
                                <h1 id="receber-hoje">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'
                            @if (Auth::user()->id_profissional != 28480002313 && Auth::user()->id_profissional != 443000000)
                                onclick='abrirTitulosReceber("w")'
                            @endif
                        >
                            <div>
                                <span>Vencem Nessa Semana</span>
                                <h1 id="receber-semana">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'
                            @if (Auth::user()->id_profissional != 28480002313 && Auth::user()->id_profissional != 443000000)
                                onclick='abrirTitulosReceber("m")'
                            @endif
                        >
                            <div>
                                <span>Vencem Nesse Mes</span>
                                <h1 id='receber-mes'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        {{-- 
        <div style="padding: 15px;background-color: white;border-radius: 5px;margin-top: 15px;margin-bottom: 30px">
            <div style='color: #2e434e;'>
                <h3>Contas a Pagar</h3>
                <table style='width:100%;text-align: end;'>
                    <tr>
                        <td class='td-cockpit2'>
                            <div>
                                <span>Em Atraso</span>
                                <h1 id="pagar-atraso">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'>
                            <div>
                                <span>Vencem Hoje</span>
                                <h1 id="pagar-hoje">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'>
                            <div>
                                <span>Vencem Nessa Semana</span>
                                <h1 id="pagar-semana">0</h1>
                            </div>
                        </td>
                        <td class='td-cockpit2'>
                            <div>
                                <span>Vencem Nesse Mes</span>
                                <h1 id='pagar-mes'>0</h1>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        --}}
        <div style="padding: 15px;background-color: white;border-radius: 5px;margin-top: 15px;margin-bottom: 30px">
            <div style='color: #2e434e;'>
                <h3>Alcance das divulgações</h3>
                <figure class="highcharts-figure">
                    <div id="chart-content-tudo"></div>
                </figure>
            </div>
        </div>

    </div>
</div>
<button class="btn btn-primary custom-fab" type="button" onclick="filtrar_cockpit_data()">
    <i class="my-icon fas fa-plus"></i>
</button>
<script src="{{ asset('js/chart.min.js')}}"></script>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' || Auth::user()->id_profissional == 28480002313 || Auth::user()->id_profissional == 443000000)
<script>
    window.addEventListener('load', () => {
        let meses = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
            lista = [],
            values = [],
            labels1 = []
        for (i = 0; i <= 12; i++) {
            let dtHoje = new Date();
            dtHoje.setMonth(dtHoje.getMonth() - i)
            lista.push(meses[dtHoje.getMonth()] + ' de ' + dtHoje.getFullYear())
            if ((dtHoje.getMonth() + 1).toString().length == 1) mes = '0' + (dtHoje.getMonth() + 1).toString()
            else mes = (dtHoje.getMonth() + 1).toString()
            values.push(dtHoje.getFullYear() + '-' + mes + '-01')

            if (i < 6) labels1.push(meses[dtHoje.getMonth()] + ' de ' + dtHoje.getFullYear())
        }

        for (j = 0; j <= 12; j++) {
            html = '<option value="' + values[j] + '">' + lista[j] + '</option>'
            $("#periodo-cockpit").append(html)
        }
        $("#periodo-cockpit").change();

        $.get('/saude-beta/cockpit/alcance', function(ret) {
            ret = $.parseJSON(ret);
            ret = ret[0];
            console.log(ret);
            var dadosGrafico = new Array();
            var soma = 0;
            for (x in ret) {
                //if (x != "naoinformado") {
                    var nome;
                    switch(x) {
                        case "naoinformado":
                            nome = "Não informado";
                            break;
                        case "midiassociais":
                            nome = "Mídias sociais";
                            break;
                        case "fachada":
                            nome = "Fachada";
                            break;
                        case "networkmembro":
                            nome = "Network membro";
                            break;
                        case "indicacao":
                            nome = "Indicação";
                            break;
                    }
                    if (ret[x] > 0) {
                        dadosGrafico.push({
                            name : nome,
                            y : ret[x]
                        });
                    }
                    soma += ret[x];
                //}
            }
            for (var i = 0; i < dadosGrafico.length; i++) dadosGrafico[i].z = parseInt((dadosGrafico[i].y / soma) * 100);
            Highcharts.chart('chart-content-tudo', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    headerFormat: '',
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
                    'Pessoas: <b>{point.y}</b><br/>' +
                    'Porcentagem: <b>{point.z}%</b><br/>'
                },
                series: [{
                    minPointSize: 10,
                    innerSize: '20%',
                    zMin: 0,
                    data: dadosGrafico,
                    colors: [
                        '#4CAEFE',
                        '#4A5899',
                        '#2E5339',
                        '#4C191B',
                        '#170312'
                    ]
                }]
            });
        });
    });

    document.addEventListener("keyup", function(e) {
        if (e.keyCode == 27) {
            $(".modal").each(function(){
                $(this).modal("hide")
            });
        }
    });
</script>
@else
<script>
    window.addEventListener('load', function() {
        location.href = "/saude-beta/"
    })
</script>
@endif
<script src="{{ asset('js/highcharts.js') }}"></script>
<script src="https://code.highcharts.com/modules/variable-pie.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<style>
    /* #grafico1, #grafico2 {
        width: 50% !important;
        height: 400px !important;
} */
</style>
@include('modals.cockpit_modal')
@endsection