@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Relatório de Atividades</h3>
        <div class="row" style="justify-content: center;background-color: white;padding: 20px 0px 0px 0px;margin-top: 4%;">
            <div class="col-5 form-group">
                <label for="paciente_nome" class="custom-label-form">Associado *</label>   
                <input id="paciente_nome"
                       name="paciente_nome"  
                       class="form-control autocomplete" 
                       placeholder="Digitar Nome do associado..."
                       data-input="#paciente_id"
                       data-table="pessoa" 
                       data-column="nome_fantasia" 
                       data-filter_col="paciente"
                       data-filter="S"
                       type="text" 
                       autocomplete="off"
                       required >
                <input onchange="changeContratoRelatorioAtividades();" id="paciente_id" name="paciente_id" type="hidden">
            </div>
            
            <div class="col-3">
                <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                <input onchange="changeContratoRelatorioAtividades();" id="data-inicial" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-3">
                <label  for="data-final" class="custom-label-form">Data Final*</label>
                <input onchange="changeContratoRelatorioAtividades();" id="data-final" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-5 form-group">
                <label for="contrato" class="custom-label-form">Contrato:</label>
                <select onchange="changeContratoRelatorioAtividades();" id="contrato" name="contrato" class="custom-select" autocomplete="off" type="text" disabled>
                </select>
            </div>
            <div class="col-3 form-group">
                <label for="plano" class="custom-label-form">Plano</label>
                <select onchange="changeContratoRelatorioAtividades();" id="plano" name="contrato" class="custom-select" autocomplete="off" type="text" disabled>
                </select>
            </div>
            <div class="col-3">
                <label for="sistema-antigo" class="custom-label-form">Origem</label>
                <select onchange="disable_elements();" id="sistema-antigo" class="custom-select" name="sistema-antigo" >
                    <option value="0">Sistema Atual</option>
                    <option value="1">Sistema Antigo</option>
                </select>
            </div>
            <div class="col-11 text-right" style='margin-top: 2%'>
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir_relatorio_atividades();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function imprimir_relatorio_atividades(){
        let contrato       = $('#contrato').val(),
            plano          = $('#plano').val(),
            sistema_antigo = $('#sistema-antigo').val(),
            url            = '/saude-beta/relatorio-atividades/imprimir/'

            url += sistema_antigo + '/' + contrato + '/' + plano
            window.open(url, '_blank')
    }

    function changeContratoRelatorioAtividades(){
        var paciente    = $('#paciente_id'),
            datainicial = $('#data-inicial'),
            datafinal   = $('#data-final'),
            contrato    = $('#contrato'),
            plano       = $('#plano')
        
            if (!campo_invalido('#paciente_id', true)){
                if(!campo_invalido('#contrato', true)) {
                    encontrarPlanosRA()
                }
                else {
                    encontrarContratosRA()
                }
            }
    }













        
    //     id_contrato = $("#criarAgendamentoModal #id_contrato").val()
    //     data1 = $('#criarAgendamentoModal #data').val();
    //     data1 = data1[6]+data1[7]+data1[8]+data1[9]+'-'+data1[3]+data1[4]+'-'+data1[0]+data1[1]
    //     $.get('/saude-beta/pedido/listar-planos-pedido/', {
    //         data: data1,
    //         id_contrato: id_contrato
    //     }, function(data){
    //         $('#criarAgendamentoModal #id_plano').empty()
    //         $('#criarAgendamentoModal #id_plano').append('<option value="0">Selecionar plano...</option>')
    //         console.log(data)
    //         a = data
    //         data.forEach(plano => {
    //             html =  '<option value="' + plano.id + '">'
    //             html += plano.descr + ' (Restam ' + (parseInt(plano.agendaveis) - parseInt(plano.agendados)) + ' atividades)'
    //             html += '</option>'
    //             $('#criarAgendamentoModal #id_plano').append(html);
    //         })
    //         callback()
    //     })
    // } 
    
</script>
@if ((
        App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'R'   &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'A'
    ) || Auth::user()->id_profissional == 28480002313 || Auth::user()->id_profissional == 443000000)
)
<script>
    window.addEventListener("load", function() {
        location.href = "/saude-beta/"
    });
</script>
@endif
@endsection
