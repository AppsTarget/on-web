@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<form class="container-fluid h-100 px-3 py-4" method="POST" action="#" onsubmit="criar_anamnese(event)">
    <div class="row">
        <h3 class="col header-color mb-3">Nova Anamnese</h3>
    </div>
    <div class="card p-3" style="height:calc(100% - 50px)">
        <div class="row"> 
            <div class="col-7">
                <label for="descr" class="custom-label-form">Descrição</label>
                <input id="descr" name="descr" class="form-control" type="text" required>
            </div>
            <div class="col-3">
                <label for="especialidade" class="custom-label-form">Área da saúde *</label>
                <select id="especialidade" name="especialidade" class="form-control custom-select">
                    <option value="">
                        Selecionar área da saúde...
                    </option>
                    @foreach ($especialidades as $especialidade)
                    <option value="{{ $especialidade->id }}">
                        {{ $especialidade->descr }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-target mt-auto mx-3 mb-1 px-5" id="enviar">Salvar</button>
        </div>
        <div class="custom-card row mx-0 mt-3" style="background:#dfdfdf; height:calc(100% - 80px)"> 
            <div class="col h-100 p-2">
                <div id="lista-criacao-anamnese" class="container-fluid custom-scrollbar overflow-auto h-100" ondrop="drop(event)" ondragover="allowDrop(event)">
                </div>
            </div>
            <div class="col-1">
                <div class="inputs-anamnese">
                    <img draggable="true" ondragstart="drag(event)" data-type="text"              title="Campo Texto"             src="/saude-beta/img/input-text.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="number"            title="Campo Numérico"          src="/saude-beta/img/input-number.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="positive-negative" title="Campo Positivo/Negativo" src="/saude-beta/img/input-positive-negative.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="checkbox"          title="Campo Múltipla Escolha"  src="/saude-beta/img/input-checkbox.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="radio"             title="Campo Sim/Não"           src="/saude-beta/img/input-radio.png">
                </div>
            </div>
        </div>
    </div>
</form>

@endsection