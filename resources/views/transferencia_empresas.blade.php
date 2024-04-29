@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Transferência Entre Empresas</h3>
        <div class="row" style="justify-content: center;background-color: white;padding: 20px 0px 0px 0px;margin-top: 4%;">
            <div class="col-3">
                <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                <input id="data-inicial" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-3">
                <label for="data-final" class="custom-label-form">Data Final*</label>
                <input id="data-final" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            
            <div class="col-2 form-group">
                <label for="orientacao" class="custom-label-form">Orientacao</label>
                <select id="orientacao" name="orientacao" class="custom-select" autocomplete="off" type="text">
                    <option value="R">Retrato</option>
                    <option value="P">Paisagem</option>
                </select>
            </div>
            <div class="col-3 form-group"></div>
            <div class="col-11 text-right">
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function imprimir(){
        let dinicial  = $("#data-inicial").val().replaceAll("/", '-'),
            dfinal    = $("#data-final").val().replaceAll("/", '-')
            orientacao = $('#orientacao').val(),
            url       = '/saude-beta/transferencia-empresas/imprimir/'
        dinicial = dinicial.substr(6,4) + dinicial.substr(2,4) + dinicial.substr(0,2)
        dfinal   = dfinal.substr(6,4) + dfinal.substr(2,4) + dfinal.substr(0,2)

        if (dinicial != '' &&  dfinal != ''){
            url += dinicial + '/' + dfinal  + '/' + orientacao
            window.open(url, "_blank");
        }
        else alert("Para prosseguir, preencha todos os campos");
    }    

    window.addEventListener('load', () => {
        dataI = new Date()
        $('#data-inicial').val('01' + dataI.toLocaleDateString().substr(2))
        dataF = new Date(dataI.getFullYear(), dataI.getMonth() + 1, 0);
        $('#data-final').val(dataF.toLocaleDateString())
    })
</script>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' && Auth::user()->id_profissional != 28480001071)
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif

@endsection
