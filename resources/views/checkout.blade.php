@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Relatório de Checkouts</h3>
        <div class="row" style="justify-content: center;background-color: white;padding: 20px 0px 0px 0px;margin-top: 4%;">
            <div class="col-5 form-group">
                <label for="empresa" class="custom-label-form">Unidade</label>
                <select id="empresa" name="empresa" class="custom-select" autocomplete="off" type="text">
                        @foreach($empresas as $empresa)
                            <option value="{{$empresa->id}}">{{$empresa->descr}}</option>
                        @endforeach
                </select>
            </div>
            <div class="col-6 form-group form-search">
                <label for="membro" class="custom-label-form">Membro *</label>
                <input id="membro"
                    name="membro"
                    class="form-control autocomplete"
                    placeholder="Digitar Nome do procedimento..."
                    data-input="#membro-id"
                    data-table="pessoa"
                    data-column="descr"
                    data-filter_col="colaborador"
                    data-filter="P"
                    type="text"
                    autocomplete="off"
                    novalidate>
                <input id="membro-id" name="membro_id" type="hidden">
            </div>
            
            <div class="col-3">
                <label for="data-inicial" class="custom-label-form">Data Inicial*</label>
                <input id="data-inicial" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>
            <div class="col-3">
                <label style="display: block" for="data-final" class="custom-label-form">Data Final*</label>
                <input style="display: block" id="data-final" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
            </div>

            <div class="col-5 text-right">
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function imprimir(){
        let empresa   = $("#empresa").val(),
            dinicial  = $("#data-inicial").val().replaceAll("/", '-'),
            dfinal    = $("#data-final").val().replaceAll("/", '-'),
            membro    = $('#membro-id').val(),
            url       = '/saude-beta/checkout/imprimir/'

            // FORMATAR DATA
        dinicial = dinicial.substr(6,4) + dinicial.substr(2,4) + dinicial.substr(0,2)
        dfinal   = dfinal.substr(6,4) + dfinal.substr(2,4) + dfinal.substr(0,2)

        if (empresa != '' &&  dinicial != '' && dfinal != '' && membro != ''){
            url += empresa + '/' + dinicial + '/' + dfinal + '/' + membro   
            window.open(url, "_blank");
        }
        else alert("Para prosseguir, preencha todos os campos");
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
