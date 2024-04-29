@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<form class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Novo IEC</h3>
    </div>
    <div class="card p-3" style="height:calc(100% - 50px)">
        <div class="row"> 
            <div class="col-7">
                <label for="descr" class="custom-label-form">Descrição</label>
                <input id="descr" name="descr" class="form-control" type="text" required>
            </div>
            <button type='button' onclick='salvar_IEC();' class="btn btn-target mt-auto mx-3 mb-1 px-5">Salvar</button>
        </div>
        <div class="custom-card row mx-0 mt-3" style="background:#dfdfdf; height:calc(100% - 80px)"> 
            <div class="col h-100 p-2">
                <div id="lista-criacao-anamnese" class="container-fluid custom-scrollbar overflow-auto h-100">
                </div>
            </div>
            <div class="col-1">
                <div style='width: 90px;' class="input-IEC">
                    <img data-type="checkbox" onclick='add_input_IEC();' title="Campo Múltipla Escolha"  src="/saude-beta/img/input-checkbox.png">
                </div>
            </div>
        </div>
    </div>
</form>
@include('.modals.add_areas_iec_modal')

@endsection