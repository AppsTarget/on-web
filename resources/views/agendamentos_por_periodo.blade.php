@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Agendamentos por Período</h3>
        <div class="row" style="justify-content: center;background-color: white;padding: 20px 0px 0px 0px;margin-top: 4%;">
            <div class="col-5 form-group">
                <label for="empresa" class="custom-label-form">Unidade</label>
                <select id="empresa" name="empresa" class="custom-select" autocomplete="off" type="text">
                        @foreach($empresas as $empresa)
                            <option value="{{$empresa->id}}">{{$empresa->descr}}</option>
                        @endforeach
                </select>
            </div>
            
            <div class="col-3">
                <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                <input id="data-inicial" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-3">
                <label style="display: none" for="data-final" class="custom-label-form">Data Final*</label>
                <input style="display: none" id="data-final" name="data-final" class="form-control" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-3 form-group">
                <label for="filtrop" class="custom-label-form">Filtrar Por:</label>
                <select onchange="changeTipoQtd();" id="filtrop" name="filtrop" class="custom-select" autocomplete="off" type="text">
                    <option value="T">Todos</option>
                    <option value="0">Perdidos</option>
                    <option value="Qtd">Filtrar por Quantidade</option>
                    <option value="mes">Expiram no mês</option>
                </select>
            </div>
            <div class="col-1 form-group">
                <label id="labelqtd" for="inputqtd" class="custom-label-form" style="display:none">Quantidade:</label>
                <input class="form-control" id="inputqtd" name="inputqtd" max="10" min="1" autocomplete="off" type="number" style="display:none">
            </div>
            <div class="col-2 form-group"></div>
            <div class="col-5 text-right">
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function imprimir(){
        let empresa   = $("#empresa").val(),
            filtro    = $("#filtrop").val(),
            inputqtd  = $("#inputqtd").val(),
            dinicial  = $("#data-inicial").val().replaceAll("/", '-'),
            dfinal    = $("#data-final").val().replaceAll("/", '-'),
            url       = '/saude-beta/agendamentos-por-periodo/imprimir/'

            // FORMATAR DATA
        dinicial = dinicial.substr(6,4) + dinicial.substr(2,4) + dinicial.substr(0,2)
        dfinal   = dfinal.substr(6,4) + dfinal.substr(2,4) + dfinal.substr(0,2)

        if (inputqtd == '') inputqtd = 0;
        if (dfinal == '') dfinal = 0;

        if ($('#filtrop').val() === 'Qtd' || $('#filtrop').val() == 'T' || $('#filtrop').val() === 'mes') {
            if (empresa != '' &&  dinicial != '' && filtro != ''){
                url += empresa + '/' + dinicial + '/' + dfinal + '/' + filtro + '/' + inputqtd
                window.open(url, "_blank");
            }
            else alert("Para prosseguir, preencha todos os campos");
        }
        else {
            window.open('http://vps.targetclient.com.br/saude-beta/cockpit/imprimir/perdidos/' + dinicial, "_blank")
        }
    }
    
    function changeTipoQtd(){
    let value = document.querySelector('#filtrop').value
    if(value == 'Qtd'){
        document.querySelector('#inputqtd').style.display = ''
        document.querySelector('#labelqtd').style.display = ''
    }
    else{
        document.querySelector('#inputqtd').style.display = 'none'
        document.querySelector('#labelqtd').style.display = 'none'
    }
}   
</script>
@endsection
