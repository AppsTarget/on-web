@extends('layouts.app')

@section('content')

@if (isset($_GET["acao"]))
    @if ($_GET["acao"] == "consulta")
        <div class="main-toolbar">
            <img style='width:97px;padding:0' src="/saude-beta/img/logo_topo_limpo_on.png">
        </div>
    @else
        @include('components.main-toolbar')
    @endif
@else
    @include('components.main-toolbar')
@endif

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">
            @if (isset($_GET["acao"]))
                @if ($_GET["acao"] == "consulta")
                    Selecione um contrato
                @else
                    Contratos
                @endif
            @else
                Contratos
            @endif
        </h3>
        <input type = "hidden" id = "consultando" value = "
            @if (isset($_GET["acao"]))
                {{$_GET["acao"]}}
            @endif
        "/>
        <div id="filtro-grid-pedido" class="input-group col-12 mb-3" data-table="#table-plano_tratamento">
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
                        <th width="5%" class="text-center">Nº</th>
                        <th width="15%" class="text-center">Associado</th>
                        <th
                            @if (isset($_GET["acao"]))
                                @if ($_GET["acao"] == "consulta")
                                    width="11%"
                                @else
                                    width="15%"
                                @endif
                            @else
                                width="15%"
                            @endif
                            class="text-center">
                                Consultor de vendas
                        </th>
                        <th
                            @if (isset($_GET["acao"]))
                                @if ($_GET["acao"] == "consulta")
                                    width="12%"
                                @else
                                    width="10%"
                                @endif
                            @else
                                width="10%"
                            @endif
                            class="text-center">
                                Financeira
                        </th>
                        <th width="10%" class="text-center">Valor</th>
                        <th width="10%">Criado em</th>
                        <th width="10%">Válido até</th>
                        <th
                            @if (isset($_GET["acao"]))
                                @if ($_GET["acao"] == "consulta")
                                    width="12%"
                                @else
                                    width="14%"
                                @endif
                            @else
                                width="14%"
                            @endif
                            class="text-center">
                                Status
                        </th>
                        @if (!isset($_GET["acao"]))
                            <th width="15%" class="text-center">Ações</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-plano_tratamento" class="table table-hover">
                <tbody>
                    @foreach ($pedidos as $pedido)
                        <tr
                            @if (isset($_GET["acao"]))
                                onclick = "openAgendamentoLote({{$pedido->id}})"
                            @endif
                        >
                            <td width="5%" class="text-center">
                                {{ str_pad($pedido->num_pedido, 5, "0", STR_PAD_LEFT) }}
                            </td>
                            <td width="15%"
                                @if (!isset($_GET["acao"]))
                                    onclick="verificar_cad_redirecionar({{ $pedido->id_paciente }})"
                                @endif
                            >{{ $pedido->descr_paciente }}</td>
                            <td
                                @if (isset($_GET["acao"]))
                                    @if ($_GET["acao"] == "consulta")
                                        width="11%"
                                    @else
                                        width="15%"
                                    @endif
                                @else
                                    width="15%"
                                @endif
                            >{{ $pedido->descr_prof_exa }}</td>
                            <td class = "td_finan"
                                @if (isset($_GET["acao"]))
                                    @if ($_GET["acao"] == "consulta")
                                        width="12%"
                                    @else
                                        width="10%"
                                    @endif
                                @else
                                    width="10%"
                                @endif
                            >
                            @if($pedido->descr_convenio == '')
                                --------------
                            @else
                                {{ $pedido->descr_convenio }}
                            @endif
                            </td>
                            <td width="10%" class="text-right"
                                @if (isset($_GET["acao"]))
                                    style="padding-right:4%"
                                @endif
                            >
                                R$ {{ number_format($pedido->total, 2, ',', '.') }}
                            </td>
                            <td width="10%">
                                {{ date('d/m/Y', strtotime($pedido->created_at)) }}
                            </td>
                            @if ($pedido->status == 'S')
                                <td width="10%" style="text-decoration: line-through">{{ date('d/m/Y', strtotime($pedido->data_validade)) }}</td>
                            @else 
                                <td width="10%">{{ date('d/m/Y', strtotime($pedido->data_validade)) }}</td>
                            @endif
                            <td
                                @if (isset($_GET["acao"]))
                                    @if ($_GET["acao"] == "consulta")
                                        width="12%"
                                    @else
                                        width="14%"
                                    @endif
                                @else
                                    width="14%"
                                @endif
                            style="font-size:0.75rem">
                                @if ($pedido->status == 'F')
                                    <div class="tag-pedido-finalizado">
                                        Ativo
                                    </div>
                                @elseif ($pedido->status == 'E')
                                    <div class="tag-pedido-aberto">
                                        Aprovação do Associado
                                    </div>
                                @elseif ($pedido->status == 'A')
                                    <div class="tag-pedido-primary">
                                        Em Edição
                                    </div>
                                @elseif ($pedido->status == 'S')
                                    <div class="tag-pedido-primary">
                                        Congelado
                                    </div>
                                @else
                                    <div class="tag-pedido-cancelado">
                                        Cancelado
                                    </div>
                                @endif
                            </td>
                            @if (!isset($_GET["acao"]))
                                <td width="15%" class="text-right btn-table-action">
                                    @if ($pedido->status != 'S' and $pedido->status != 'C')
                                        <img id="congelar-contrato" onclick="abrircongelarContrato({{ $pedido->id }})"src="{{ asset('img/proibido.png') }}"> 
                                    @elseif($pedido->status != 'C')
                                    <img onclick="descongelar_contrato({{ $pedido->id }})"src="{{ asset('img/desbloquear.png') }}" style="vertical-align: middle;
                                                                                                border-style: none;
                                                                                                max-width: 23px;
                                                                                                position: relative;
                                                                                                top: -6px;
                                                                                                right: 3px;
                                                                                                opacity: 0.7;
                                                                                                cursor: pointer"> 
                                    @endif

                                    @if ($pedido->status != 'C' && $pedido->status != 'S')
                                        @if ($pedido->status == 'A')
                                            <i class="my-icon far fa-user-check" onclick="mudar_status_pedido({{ $pedido->id }}, 'P')"></i>
                                        @elseif ($pedido->status == 'P')
                                            <i class="my-icon far fa-user-check" onclick="mudar_status_pedido({{ $pedido->id }}, 'F')"></i>
                                        @endif
                                        <i class="my-icon far fa-file-times" onclick="mudar_status_pedido({{ $pedido->id }}, 'C')"></i>
                                        <i class="my-icon far fa-print" onclick="redirect('pedido/imprimir/{{ $pedido->id }}/{{$pedido->sistema_antigo}}', true)"></i>
                                        {{-- <i class="my-icon far fa-edit" onclick="editar_pedido({{ $pedido->id }})"></i> --}}
                                    @endif
                                    <i class="my-icon far fa-trash-alt" onclick="deletar_pedido({{ $pedido->id }})"></i>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style type = "text/css">
    .listaEnc{background:#f2f2f2;cursor:pointer}
    .listaEnc:hover{background:#80a9d6}
</style>
@if (!isset($_GET["acao"]))
    <button class="btn btn-primary custom-fab" type="button" onclick="abrir_pedido()">
        <i class="my-icon fas fa-plus"></i>
    </button>
@endif
@if (
        App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'R' &&
        App\Pessoa::find(Auth::user()->id_profissional)->colaborador != 'A'
    )
<script>
    window.addEventListener("load", function() {
        location.href = "/saude-beta/"
    });
</script>
@endif
@include('.modals.agendamento_lote_modal')
@include('modals.pedido_modal')
@include('modals.completar_cadastro_modal')
@include('modals.congelar_pedido_modal')
@include('modals.pedido_pessoas_modal')
@include('modals.encaminhamentosLista_modal')
@include("modals.listar_tabelasEncaminhamento_modal")
@endsection
