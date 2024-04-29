@extends('layouts.app')

@section('content')
@include('components.main-toolbar')
<input id="id-IEC" type="hidden" value="{{ $id }}">
<form class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Editar IEC</h3>
    </div>
    <div class="card p-3" style="height:calc(100% - 50px)">
        <div class="row"> 
            <div class="col-7">
                <label for="descr" class="custom-label-form">Descrição</label>
                <input id="descr" name="descr" class="form-control" type="text" required>
            </div>
            <button type='button' onclick='editar_IEC();' class="btn btn-target mt-auto mx-3 mb-1 px-5">Salvar</button>
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
<script>
    window.addEventListener('load', () => {   
        $.get(
            '/saude-beta/IEC/exibir/' + $("#id-IEC").val(),
            function(data,status){
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                a = data;
                $i = 0
                $x = 0
                $("#descr").val(data.descricao)
                lista = ['Pessimo', 'Ruim', 'Bom', 'Excelente']
                data.IEC_questao.forEach(IEC => {
                    add_input_IEC();
                    let card = $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1]),
                        areas = card.find('.areas')

                    card.find('#pergunta').val(IEC.pergunta);
                    card.find('#obs').val(IEC.obs)
                    card.find('#Pessimo').val(IEC.pessimo)
                    card.find('#Ruim').val(IEC.ruim)
                    card.find('#Bom').val(IEC.bom)
                    card.find('#Excelente').val(IEC.excelente)

                    for (j=0; j < 4; j++){
                        console.log(data.areas)
                        console.log($i)
                        $($(".custom-card.row.p-2.mb-2")[$(".custom-card.row.p-2.mb-2").length-1]).find('.areas')[j].value = data.areas[$i].substring(0, data.areas[$i].length-1)
                        $("#" + lista[j] + $x).html(data.areas[$i].substring(0, data.areas[$i].length-1).split(',').length)
                        $i++;
                    }$x++;
                    
                })
            }
        )
    })
</script>
@include('.modals.add_areas_iec_modal')

@endsection