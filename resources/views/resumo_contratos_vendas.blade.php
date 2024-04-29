@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <h3 class="col header-color mb-3">Resumo Contratos Vendas</h3>
        <div class="row" style="justify-content: center;background-color: white;padding: 30px 0px 30px 0px;margin-top: 1%;">
            <div class="col-2 form-group">
                <label for="agrupamento" class="custom-label-form">Agrupamento Por</label>
                <select id="agrupamento" name="agrupamento" onchange="controlAgrupamento($(this))" class="custom-select" autocomplete="off" type="text">
                    {{-- <option value="1">Modalidade</option> --}}
                    <option value="2">Planos</option>
                </select>
            </div>
            <div class="col-4 form-group">
                <label for="empresa" class="custom-label-form">Unidade</label>
                <select id="empresa" name="empresa" class="custom-select" onChange="control" autocomplete="off" type="text">
                    @foreach($empresas as $empresa)
                        <option value="{{$empresa->id}}">{{$empresa->descr}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group form-search" style="display: none" >
                <label for="procedimento-nome-agenda" class="custom-label-form">Modalidade *</label>
                <input id="procedimento-nome-agenda"
                    name="procedimento_nome-agenda"
                    class="form-control autocomplete"
                    placeholder="Digitar Nome do procedimento..."
                    data-input="#procedimento-id"
                    data-table="procedimento"
                    data-column="descr"
                    data-filter_col="id_emp"
                    data-filter="{{ getEmpresa() }}"
                    type="text"
                    autocomplete="off"
                    novalidate>
                <input id="procedimento-id" name="procedimento_id" type="hidden">
            </div>
            
            <div class="col-4 form-group form-search">
                <label for="id_plano" class="custom-label-form">Planos</label>
                <select id="id_plano" name="id_convenio" class="custom-select">
                    <option value="0">Selecionar Plano...</option>
                    @foreach($tabela_precos AS $tabela_preco)
                        <option value="{{$tabela_preco->id }}">{{$tabela_preco->descr}}</option>
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
            <div class="col-2 form-group">
                <label for="exibirF" class="custom-label-form">Mostrar</label>
                <select id="exibirF" name="exibirF" class="custom-select" autocomplete="off" type="text">
                    <option value="T">Todos</option>
                    <option value="S">Apenas faturados</option>
                </select>
            </div>
            <div class="col-2 text-right" style="align-items: end;display: flex;justify-content: end;">
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="imprimir();" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">IMPRIMIR</button>
            </div>
        </div>
    </div>
</div>
<script>
    function controlAgrupamento(_this) {
        switch(parseInt(_this.val())) {
            case 1:
                $('#id_plano').parent().hide()
                $('#procedimento-id').parent().show()
                break;
            case 2:
                $('#id_plano').parent().show()
                $('#procedimento-id').parent().hide()
        }
    }

    function imprimir(){
        let empresa     = $("#empresa").val(),
            agrupamento = $('#agrupamento').val(),
            dinicial    = $("#data-inicial").val().replaceAll("/", '-'),
            dfinal      = $("#data-final").val().replaceAll("/", '-'),
            modalidade  = $('#procedimento-id').val(),
            plano       = $('#id_plano').val(),
            exibirF     = $('#exibirF').val(),
            url         = '/saude-beta/resumo-contratos-vendas/imprimir/'
        dinicial = dinicial.substr(6,4) + dinicial.substr(2,4) + dinicial.substr(0,2)
        dfinal   = dfinal.substr(6,4) + dfinal.substr(2,4) + dfinal.substr(0,2)

        if (empresa != '' &&  agrupamento != '' &&  dinicial != '' &&  dfinal != ''){
            
            if (modalidade == '') modalidade = 0

            url += empresa + '/' + agrupamento + '/' + dinicial + '/' + dfinal + '/'
            if (agrupamento == 1){
                url += modalidade
            }
            else {
                url += plano
            }
            url += '/' + exibirF;
            window.open(url, "_blank");
        }
        else alert("Para prosseguir, preencha todos os campos");
    }    
</script>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' && Auth::user()->id_profissional != 28480001071)
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif

@endsection
