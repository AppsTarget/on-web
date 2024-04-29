@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<input id="id-anamnese" type="hidden" value="{{ $id }}">
<form class="container-fluid h-100 px-3 py-4" method="POST" action="#" onsubmit="editar_anamnese(event)">
    <div class="row">
        <h3 class="col header-color mb-3">Editar Anamnese</h3>
    </div>
    <div class="card p-3" style="height:calc(100% - 50px)">
        <div class="row"> 
            <div class="col-7">
                <label for="descr" class="custom-label-form">Descrição</label>
                <input id="descr" name="descr" class="form-control" type="text" required>
            </div>
            {{-- <div class="col-3">
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
            </div> --}}
            <button type="submit" class="btn btn-target mt-auto mx-3 mb-1 px-5" id="enviar">Salvar</button>
        </div>
        <div class="custom-card row mx-0 mt-3" style="background:#dfdfdf; height:calc(100% - 80px)"> 
            <div class="col h-100 p-2">
                <div id="lista-criacao-anamnese" class="container-fluid custom-scrollbar overflow-auto h-100" ondrop="drop(event)" ondragover="allowDrop(event)">
                </div>
            </div>
            <div class="col-1">
                <div class="inputs-anamnese">
                    <img draggable="true" ondragstart="drag(event)" data-type="text" title="Campo Texto"             src="/saude-beta/img/input-text.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="number"            title="Campo Numérico"          src="/saude-beta/img/input-number.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="positive-negative" title="Campo Positivo/Negativo" src="/saude-beta/img/input-positive-negative.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="checkbox"          title="Campo Múltipla Escolha"  src="/saude-beta/img/input-checkbox.png">
                    <img draggable="true" ondragstart="drag(event)" data-type="radio"             title="Campo Sim/Não"           src="/saude-beta/img/input-radio.png">
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    window.addEventListener('load', () => {   
        $.get(
            '/saude-beta/anamnese/exibir/' + $("#id-anamnese").val(),
            function(data,status){
                var tdata = data.data;
                var lista_opcoes = data.opcoes;
                data = tdata;
                console.log(data);
                console.log(lista_opcoes);
                console.log(data + ' | ' + status);
                
                a = data;
                i = 0
                $("#descr").val(data.descricao)
                data.anamnese_questao.forEach(anamnese_questao => {
                        if (anamnese_questao.tipo == 'C'){
                            add_input_anamnese('checkbox')
                            let card = $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1])
                            card.find('#pergunta').val(anamnese_questao.pergunta);
                            card.find('#obs').val(anamnese_questao.obs)
                            var respostas = lista_opcoes["id_" + anamnese_questao.id];
                            for (i=0; i < respostas.length; i++){
                                $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1]).find('.row.m-0 > .col.p-0 > input')[i].value = respostas[i].descr
                                $($($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1]).find('.row.m-0 > .col.p-0 > input')[i]).parent().parent().find('.fa-plus-circle').click()
                            }
                        }
                        else if(anamnese_questao.tipo == 'N'){
                            add_input_anamnese('number')
                            let card = $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1])
                            card.find('#pergunta').val(anamnese_questao.pergunta);
                            card.find('#obs').val(anamnese_questao.obs)
                        }
                        else if(anamnese_questao.tipo == '+'){
                            add_input_anamnese('positive-negative')
                            let card = $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1])
                            card.find('#pergunta').val(anamnese_questao.pergunta);
                            card.find('#obs').val(anamnese_questao.obs)
                        }
                        else if(anamnese_questao.tipo == 'R'){
                            add_input_anamnese('radio')
                            let card = $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1])
                            card.find('#pergunta').val(anamnese_questao.pergunta);
                            card.find('#obs').val(anamnese_questao.obs)
                        }
                        else if(anamnese_questao.tipo == 'T'){
                            add_input_anamnese('text')
                            let card = $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1])
                            card.find('#pergunta').val(anamnese_questao.pergunta);
                            card.find('#obs').val(anamnese_questao.obs)
                        }
                        else{
                            alert('erro')
                        }
                })
            }
        )
    })
</script>
@endsection