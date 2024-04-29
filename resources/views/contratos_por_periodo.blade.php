@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Contratos Por Período</h3>
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
                <label for="data-final" class="custom-label-form">Data Final*</label>
                <input id="data-final" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-5 form-group">
                <label for="membro" class="custom-label-form">Consultor de Vendas</label>
                <select id="membro" name="membro" class="custom-select" autocomplete="off" type="text">
                    <option value="0">Todos</option>
                    @foreach($membros as $membro)
                        <option value="{{$membro->id}}">{{$membro->nome_fantasia}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-2 form-group">
                <label for="filtro-financeira" class="custom-label-form">Filtrar Por</label>
                <select id="filtro-financeira" name="filtro-financeira" class="custom-select" autocomplete="off" type="text">
                    <option value="0">Todos</option>
                    <option value="C">Convênios</option>
                    <option value="P">Particular</option>
                </select>
            </div>
            <div class="col-2 form-group">
                <label for="orientacao" class="custom-label-form">Orientação</label>
                <select id="orientacao" name="orientacao" class="custom-select" autocomplete="off" type="text">
                    {{-- <option value="R">Retrato</option> --}}
                    <option value="P">Paisagem</option>
                </select>
            </div>
            <div class="col-2 form-group">
                <label for="exibirF" class="custom-label-form">Mostrar</label>
                <select id="exibirF" name="exibirF" class="custom-select" autocomplete="off" type="text">
                    <option value="T">Todos</option>
                    <option value="S">Apenas faturados</option>
                </select>
            </div>
            <div class="col-11 text-right">
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function imprimir(){
        let empresa    = $("#empresa").val(),
            membro     = $("#membro").val(),
            dinicial   = $("#data-inicial").val().replaceAll("/", '-'),
            dfinal     = $("#data-final").val().replaceAll("/", '-'),
            filtro     = $('#filtro-financeira').val(),
            orientacao = $('#orientacao').val(),
            exibirF    = $('#exibirF').val(),
            url        = '/saude-beta/contratos-por-periodo/imprimir/';
        dinicial = dinicial.substr(6,4) + dinicial.substr(2,4) + dinicial.substr(0,2);
        dfinal   = dfinal.substr(6,4) + dfinal.substr(2,4) + dfinal.substr(0,2);

        if (empresa != '' &&  membro != '' &&  dinicial != '' &&  dfinal != ''){
            url += empresa + '/' + membro + '/' + dinicial + '/' + dfinal + '/' + filtro + '/' + orientacao + '/' + exibirF;
            window.open(url, "_blank");
        } else alert("Para prosseguir, preencha todos os campos");
    }    
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
