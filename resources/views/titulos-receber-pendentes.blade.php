@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Borderô De Pagamentos Do Membro</h3>
        <div class="row" style="justify-content: center;background-color: white;padding: 20px 0px 0px 0px;margin-top: 4%;">
            <div class="col-5 form-group">
                <label for="empresa" class="custom-label-form">Unidade</label>
                <select id="empresa" name="empresa" class="custom-select" autocomplete="off" type="text">
                    @foreach($empresas as $empresa)
                        <option value="{{$empresa->id}}">{{$empresa->descr}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group">
                <label for="membro" class="custom-label-form">Membro</label>
                <select id="membro" name="membro" class="custom-select" autocomplete="off" type="text">
                    
                </select>
            </div>
            <div class="col-3 form-group">
                <label for="documento" class="custom-label-form">Documento</label>
                <input id="documento" name="documento" class="form-control" autocomplete="off" type="text" maxlength="20" required>
            </div>
            <div class="col-2 form-group"></div>
            <div class="col-3">
                <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                <input id="data-inicial" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-3">
                <label for="data-final" class="custom-label-form">Data Final*</label>
                <input id="data-final" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-11 text-right">
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function imprimir(){
        let empresa   = $("#empresa").val(),
            membro    = $("#membro").val(),
            documento = $("#documento").val(),
            dinicial  = $("#data-inicial").val().replaceAll("/", '-'),
            dfinal    = $("#data-final").val().replaceAll("/", '-')
            url       = '/saude-beta/bordero/imprimir/'
        dinicial = dinicial.substr(6,4) + dinicial.substr(2,4) + dinicial.substr(0,2)
        dfinal   = dfinal.substr(6,4) + dfinal.substr(2,4) + dfinal.substr(0,2)
        if (documento == '') documento = 0

        if (empresa != '' &&  membro != '' &&  dinicial != '' &&  dfinal != ''){
            url += empresa + '/' + membro + '/' + documento + '/' + dinicial + '/' + dfinal
            window.open(url, "_blank");
        }
        else alert("Para prosseguir, preencha todos os campos");
    }    
</script>


@endsection
