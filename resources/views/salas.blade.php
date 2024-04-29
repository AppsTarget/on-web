@extends('layouts.app')

@section('content')
@include('components.main-toolbar')
<link href="{{ asset('css/font-awesome6.css') }}" rel="stylesheet">
<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">
            Salas
        </h3>
        <div id="filtro-grid-by0" class="input-group col-12 mb-3" data-table="#table-plano_tratamento">
            <input type="text" class="form-control form-control-lg" placeholder="Procurar por..." aria-label="Procurar por..." aria-describedby="btn-filtro">
            <div class="input-group-append">
                <button class="btn btn-secondary btn-search-grid" type="button" id="btn-filtro">
                    <i class="my-icon fas fa-search"></i>
                </button>
            </div>
         </div>
    </div>
    <div class="custom-table card">
        <div class="table-header-scroll">
            <table>
                <thead>
                    <tr class="sortable-columns" for="#table-plano_tratamento">
                        @if ($mostrar_nomes)
                            <th width = "30%">Descrição</th>
                            <th width = "50%">Alugado por</th>
                        @else
                            <th width = "80%">Descrição</th>
                        @endif
                        <th width = "10%">Valor</th>
                        <th width = "10%" class = "text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-plano_tratamento" class="table">
                <tbody>
                    @foreach ($data as $sala)
                        <tr id = "sala{{ $sala->id }}" class = "tr_sala"
                            data-ndoc        = "{{ $sala->ndoc }}"
                            data-pessoa      = "{{ $sala->id_pessoa }}"
                            data-pessoa_nome = "{{ $sala->alugado_por }}"
                            data-parcelas    = "{{ $sala->parcelas }}"
                            data-vencimento  = "{{ $sala->vencimento }}"
                        >
                            @if ($mostrar_nomes)
                                <td width = "30%">{{ $sala->descr }}</td>
                                <td width = "50%">
                                    @if ($sala->alugado_por != "")
                                        {{ $sala->alugado_por }}
                                        @if ($sala->valor_real != $sala->valor)
                                            (a
                                            <span class = 'mask-money-brl'>
                                                {{ $sala->valor_real }}
                                            </span>
                                            por parcela)
                                        @endif
                                    @else
                                        @for ($i = 0; $i < $mostrar_nomes + 1; $i++)
                                            -
                                        @endfor
                                    @endif
                                </td>
                            @else
                                <td width = "80%">{{ $sala->descr }}</td>
                            @endif
                            <td width = "10%" class = "mask-money-brl">{{ $sala->valor }}</td>
                            <td width = "10%" class = "acoes text-center">
                                @if ($sala->alugado_por != "")
                                    <div>
                                        <i
                                            class = "fa6-duotone fa6-handshake-slash"
                                            onclick = "encerrar({{ $sala->id }})"
                                            title = "Encerrar contrato"
                                        ></i>
                                    </div>
                                @else
                                    <div>
                                        <i
                                            class = "fa6-light fa6-handshake"
                                            onclick = "abreModal({{ $sala->id }},'aluguelModal')"
                                            title = "Alugar"
                                        ></i>
                                    </div>
                                @endif
                                @if ($sala->alugado_por != "")
                                    <div>
                                        <i
                                            class = "my-icon far fa-calendar-alt"
                                            onclick = "
                                                salaVencimento({{ $sala->id }});
                                                setTimeout(function() {
                                                    $('#membro_id').val($('#sala' + {{ $sala->id }}).data().pessoa);
                                                }, 500);
                                            "
                                            title = "Alterar vencimento"
                                        ></i>
                                    </div>
                                    <br>
                                @endif
                                @if ($sala->parcelas > 0)
                                    <div>
                                        <i
                                            class = "my-icon far fa-receipt"
                                            onclick = "salaHistorico({{ $sala->id }})"
                                            title = "Histórico"
                                        ></i>
                                    </div>
                                    @if ($sala->alugado_por == "")
                                        <br>
                                    @endif
                                @endif
                                <div style = "padding-left:4px">
                                    <i
                                        class = "my-icon far fa-edit"
                                        onclick = "salaModal({{ $sala->id }})"
                                        title = "Editar"
                                    ></i>
                                </div>
                                @if ($sala->alugado_por == "")
                                    <div>
                                        <i
                                            class = "my-icon far fa-trash-alt"
                                            onclick = "salaDeletar({{ $sala->id }})"
                                            title = "Excluir"
                                        ></i>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type = "hidden" id = "periodo-cockpit" class = "id_sala" />
<form style = "display:none" method = "post" id = "exclusao">
    @csrf
    <input id = "excluir" name = "id" type="hidden">
    <input id = "encerrar-n_doc"     name = "ndoc"      type="hidden">
    <input id = "encerrar-id_sala"   name = "id_sala"   type="hidden">
    <input id = "encerrar-id_pessoa" name = "id_pessoa" type="hidden">
</form>
<button class="btn btn-primary custom-fab" type="button" onclick="salaModal(0)">
    <i class="my-icon fas fa-plus"></i>
</button>

<style type = "text/css">
    .acoes svg, .acoes i {cursor:pointer}
    .acoes div {display:inline-block;width:20px;height:20px}
</style>

@php
    echo '<script type = "text/javascript" language = "JavaScript">';
    echo 'const totalDoc = '.$qtd.';';
    echo 'var documentos = {';
    foreach ($documentos as $documento) {
        echo "p".$documento->id_pessoa.":[".$documento->numeros."],";
    }
    echo '}';
    echo '</script>';
@endphp

<script type = "text/javascript" language = "JavaScript">
    window.addEventListener("load", function() {
        var maior = 0;
        var elementos = document.getElementsByClassName("tr_sala");
        for (var i = 0; i < elementos.length; i++) {
            if (elementos[i].offsetHeight > maior) maior = elementos[i].offsetHeight;
        }
        for (var i = 0; i < elementos.length; i++) {
            elementos[i].style.height = maior + "px";
        }
    });

    var vencAnt = 0;
    var valAnt = 0;
    var descrAnt = "";

    function salaModal(id) {
        abreModal(id, "salaModal");
        $("#retroativo").val("N");
        valAnt = 0;
        descrAnt = "";
        if (id > 0) {
            valAnt = $($("#sala" + id).children()[$("#sala" + id).children().length - 2]).html();
            descrAnt = $($("#sala" + id).children()[0]).html();
            $("#descr").val(descrAnt);
            $("#valor").val(valAnt);
        }
    }

    function salaDeletar(id) {
        ShowConfirmationBox(
            'Deseja excluir a sala ' + $($("#sala" + id).children()[0]).html() + '?',
            '',
            true, true, false,
            function () {
                $("#exclusao").attr("action", "/saude-beta/financeiro/alugueis/sala/excluir");
                $("#excluir").val(id);
                $("#exclusao").submit();
            },
            function () { },
            'Sim',
            'Não'
        );
    }

    function encerrar(id) {
        ShowConfirmationBox(
            'Deseja encerrar esse contrato?',
            '',
            true, true, false,
            function () {
                $("#encerrar-id_sala").val(id);
                $("#encerrar-n_doc").val($("#sala" + id).data().ndoc);
                $("#encerrar-id_pessoa").val($("#sala" + id).data().pessoa);
                $("#exclusao").attr("action", "/saude-beta/financeiro/alugueis/aluguel/encerrar");
                $("#exclusao").submit();
            },
            function () { },
            'Sim',
            'Não'
        );
    }

    function abreModal(id_sala, modal) {
        vencAnt = 0;
        $(".id_sala").each(function() {
            $(this).val(id_sala);
        });
        $("#" + modal + "Form .form-control").each(function() {
            $(this).val("");
            $(this).trigger("keydown");
            $(this).trigger("keyup");
        });
        $("#descrlen").html("0/100");
        $("#aluguelModalLabel").html("Aluguel - <i>" + $($("#sala" + id_sala).children()[0]).html() + "</i>");
        $("#aluguelModalForm").attr("action", "/saude-beta/financeiro/alugueis/aluguel/gravar");
        $("#membro_id").val("");
        $("#btnAluguel").html("Alugar");
        $("#doc").removeAttr("disabled");
        $("#membro_nome").removeAttr("disabled");
        $("#parc").removeAttr("disabled");
        $("#" + modal).modal("show");
    }

    function salaVencimento(id_sala) {
        abreModal(id_sala, "aluguelModal");
        $("#aluguelModalLabel").html("Alterar data de vencimento");
        $("#aluguelModalForm").attr("action", "/saude-beta/financeiro/alugueis/aluguel/dtVenc");
        $("#btnAluguel").html("Salvar");
        $("#membro_nome").val($("#sala" + id_sala).data().pessoa_nome);
        $("#doc").val($("#sala" + id_sala).data().ndoc);
        $("#doc").attr("value", $("#sala" + id_sala).data().ndoc);
        $("#parc").val($("#sala" + id_sala).data().parcelas);
        $("#venc").val(parseInt($("#sala" + id_sala).data().vencimento));
        $("#doc").attr("disabled", "true");
        $("#membro_nome").attr("disabled", "true");
        $("#parc").attr("disabled", "true");
        vencAnt = parseInt($("#sala" + id_sala).data().vencimento);
    }

    function salaHistorico(id_sala) {
        $(".id_sala").each(function() {
            $(this).val(id_sala);
        });
        abrirModalCockpitMain(id_sala, "aluguel");
    }
</script>

@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' || Auth::user()->id_profissional == 28480001071 || Auth::user()->id_profissional == 429000000)
<script>
    window.addEventListener("load", function() {
        location.href = "/saude-beta/"
    });
</script>
@endif

@include('modals.sala_modal')
@include('modals.aluguel_modal')
@include('modals.cockpit_modal')

@endsection
