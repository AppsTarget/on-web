@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">
            Títulos a Pagar
        </h3>
        <div id="filtro-grid-pedido" class="input-group col-12 mb-3" data-table="#table-plano_tratamento">
            <div class="col-2 form-group">
                <input id="contrato" class="form-control" type='number' placeholder="Contrato...">
            </div>
            <div class="col-4 form-group">
                <input id="paciente_nome"
                    name="paciente_nome"  
                    class="form-control autocomplete" 
                    placeholder="Digitar Nome do associado..."
                    data-input="#paciente_id"
                    data-table="pessoa" 
                    data-column="nome_fantasia" 
                    data-filter_col="paciente"
                    data-filter="S"
                    type="text" 
                    autocomplete="off"
                    required>
                <input id="paciente_id" name="paciente_id" type="hidden">
            </div>
            <div class="col-6 form-group">
                <select id="empresa" class="custom-select">
                    <option value="0">Selecionar Empresa</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->descr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 form-group">
                <select id="venc-ou-lanc" class="custom-select">
                    <option value="vencimento">Vencimento</option>
                    <option value="lancamento">Lançamento</option>
                </select>
            </div>
            <div class="col-3 form-group">
                <input id="data-inicial" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
            </div>
            <div class="col-3 form-group">
                <input id="data-final" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required>
            </div>
            <div class="col-1 form-group  filtro-valor">
                <input id="valor-inicial" class="form-control text-right money-brl" placeholder="R$ 0,00" type="text" min="1">
            </div>
            <div class="form-group" style="padding-top: 1.3%">
                <span>
                    à
                </span>
            </div>
            <div class="col-1 form-group  filtro-valor">
                <input id="valor-final" class="form-control text-right money-brl" placeholder="R$ 0,00" type="text" min="1">
            </div>
            <div class="col-4 form-group">
                <label class="custom-label-form" for="forma-pag">Forma de Pagamento</label>
                <select id="forma-pag" name="forma-pag" class="custom-select">
                    <option value="0">Todos</option>
                    @foreach($formas_pag AS $forma_pag)
                        <option value="{{ $forma_pag->id }}">{{ $forma_pag->descr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 custom-control custom-checkbox custom-control-inline mr-2" style='position: relative;top: 8.6%;left: 5%;'>
                <input id="liquidados" name="liquidados" style="width: 17px;margin: -1.5% 5px 0px -6%;" type="checkbox">
                <span style='color: #001284;position: relative;top:34%'>Mostrar títulos liquidados</span>
            </div>
            <div class="col-3 text-right" style='margin-top: 2%;flex: 0 0 32.7%;max-width: 33%;'>
                <button type="button" class="btn btn-target px-5" id="imprimir" onclick="pesquisarTitulosPagar()" style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;">Pesquisar</button>
            </div>
         </div>
    </div>
    <div class="d-flex" style="justify-content: space-between;width: 29%;">
        <div class="d-flex" style="align-items: center">
            <img style="width: 27px; height: 27px" src="{{ asset('img/vencido.png') }}">
            <p style="font-size: 13px;margin: 5px 0 0 0;">Títulos Vencidos</p>
        </div>
        <div class="d-flex" style="align-items: center">
            <img style="width: 19px; height: 19px" src="{{ asset('img/pendente.png') }}">
            <p style="font-size: 13px;margin: 5px 0 0 7px;">Titulos Pendentes</p>
        </div>
        <div class="d-flex" style="align-items: center">
            <img style="width:18px; height: 18px" src="{{ asset('img/pago.png') }}">
            <p style="font-size: 13px;margin: 5px 0 0 7px;">Títulos Pagos</p>
        </div>
    </div>
    <div class="custom-table card">
        <div class="table-header-scroll">
            <table>
                <thead>
                    <tr class="sortable-columns" for="#table-plano_tratamento">
                        <th style='width:5%; min-width: 8%; max-width: 8%' class="text-left">Status</th>
                        <th style='width:5%;  min-width: 5%; max-width: 5%' class="text-left">Contrato</th>
                        <th style="width:19%; min-width: 22%; max-width: 22%" class="text-left">Nome</th>
                        <th style="width:10%; min-width: 10%; max-width: 10%" class="text-left">Descrição</th>
                        <th style="width:10%; min-width: 10%; max-width: 10%" class="text-right">Parcela</th>
                        <th style="width:10%; min-width: 10%; max-width: 10%" class="text-right">Valor</th>
                        {{-- <th width="10%">Acréscimo</th> --}}
                        <th style="width:10%; min-width: 10%; max-width: 10%" class='text-right'>Valor Pago</th>
                        <th style="width:10%;" class="text-right">Dt. Lanc</th>
                        <th style="width:10%;" class="text-right">Dt. Venc</th>
                        <th style="width:10%;" class="text-right"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-plano_tratamento" class="table table-hover">
                <tbody>
                    @foreach ($titulos_pagar as $titulo)
                        <tr style="cursor: pointer; font-size: 12px">
                            <td style="width: 5%; min-width: 5%; max-width: 5%" class="text-left">
                                @if ($titulo->pago == "S")
                                    <img style="width:18px; height: 18px; margin-left: 8%;" src="{{ asset('img/pago.png') }}">
                                @elseif ($titulo->pago != "S" && strtotime($titulo->dt_vencimento) < strtotime(date('Y-m-d')))
                                    <img style="width: 27px; height: 27px" src="{{ asset('img/vencido.png') }}">
                                @else
                                    <img style="width: 19px; height: 19px; margin-left: 8%" src="{{ asset('img/pendente.png') }}">
                                @endif
                            </td>
                            <td style="width: 6%;" class="text-left">
                                {{ $titulo->ndoc }}
                            </td>
                            <td style="width: 19%;" class="text-left">
                                {{ $titulo->pessoa }}
                            </td>
                            <td style="width: 10%; min-width: 10%; max-width: 10%" class="text-left">
                                {{ $titulo->descr }}
                            </td>
                            <td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right">
                                {{ $titulo->parcela }}
                            </td>
                            <td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right">
                                R$ {{ number_format($titulo->valor_total, 2, ',', '.') }}
                            </td>
                            <td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right">
                                R$ {{ number_format($titulo->valor_total_pago, 2, ',', '.') }}
                            </td>
                            <td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right">
                                {{  date('d/m/Y', strtotime($titulo->dt_lanc)) }}
                            </td>
                            <td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right">
                                {{ date('d/m/Y', strtotime($titulo->dt_vencimento)) }}
                            </td>
                            <td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right">
                                @if (\App\TitulosPagar::find($titulo->id)->valor_total != \App\TitulosPagar::find($titulo->id)->valor_total_pago)
                                    <img onclick="abrirBaixaTituloPagarModal({{ $titulo->id }})" style="width: 28px;height: 27px;margin-right: 15px;margin-top: 3px;" src="{{asset('img/correto.png') }}">
                                @endif
                                <img style="width:20px; height: 20px;margin-right: 10px"
                                    src="{{ asset('img/olho.png') }}"
                                    @if ($titulo->id_pedido != 0) 
                                        onclick="new_system_window('pedido/imprimir/{{ \App\TitulosReceber::find($titulo->id)->ndoc }}/0')" 
                                    @else
                                        onclick="visualizar_titulo_pagar({{ $titulo->id }})"
                                    @endif
                                        >
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="$('#cadastrarTituloPagarModal').modal('show')">
    <i class="my-icon fas fa-plus"></i>
</button>

@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' || Auth::user()->id_profissional == 28480001071 || Auth::user()->id_profissional == 14672 || Auth::user()->id_profissional == 429000000)
<script>
    window.addEventListener("load", function() {
        location.href = "/saude-beta/"
    });
</script>
@endif

@include('.modals.cadastrar_titulo_pagar_modal')
@include('.modals.ver_titulo_pagar_modal')
@include('.modals.baixar_titulo_pagar_modal')

@endsection
