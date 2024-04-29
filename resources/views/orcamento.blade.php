@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">
            Propostas de Tratamento
        </h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-orcamento">
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
                    <tr class="sortable-columns" for="#table-orcamento">
                        <th width="8%" class="text-center">Nº Proposta</th>
                        <th width="18%">Associado</th>
                        <th width="10%">Solicitante</th>
                        <th width="10%">Convênio</th>
                        <th width="7%" class="text-right">A Vista</th>
                        <th width="7%" class="text-right">A Prazo</th>
                        <th width="8%">Criado Em</th>
                        <th width="8%">Válido Até</th>
                        <th width="10%" class="text-center">Status</th>
                        <th width="14%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-orcamento" class="table table-hover">
                <tbody>
                    @foreach ($orcamentos as $orcamento)
                        <tr>
                            <td width="8%" class="text-center">
                                {{ str_pad($orcamento->num_pedido, 6, "0", STR_PAD_LEFT) }}
                            </td>
                            <td width="18%" onclick="verificar_cad_redirecionar({{ $orcamento->id_paciente }})">{{ $orcamento->descr_paciente }}</td>
                            <td width="10%">{{ $orcamento->descr_prof_exa }}</td>
                            <td width="10%">{{ $orcamento->descr_convenio }}</td>
                            <td width="7%" class="text-right">R$ {{ str_replace('.', ',', strval($orcamento->total)) }}</td>
                            <td width="7%" class="text-right">R$ {{ str_replace('.', ',', strval($orcamento->total_prazo)) }}</td>
                            <td width="8%">
                                {{ date('d/m/Y', strtotime($orcamento->created_at)) }}
                            </td>
                            <td width="8%">{{ date('d/m/Y', strtotime($orcamento->data_validade)) }}</td>
                            <td width="10%" style="font-size:0.75rem">
                                @if ($orcamento->status == 'F')
                                    <div class="tag-pedido-finalizado">
                                        Aprovado
                                    </div>
                                @elseif ($orcamento->status == 'P')
                                    <div class="tag-pedido-primary">
                                        Aprovado Parcialmente
                                    </div>
                                @elseif ($orcamento->status == 'E')
                                    <div class="tag-pedido-aberto">
                                        Aprovação do Associado
                                    </div>
                                @elseif ($orcamento->status == 'A')
                                    <div class="tag-pedido-primary">
                                        Em Edição
                                    </div>
                                @else
                                    <div class="tag-pedido-cancelado">
                                        Cancelado
                                    </div>
                                @endif
                            </td>
                            <td width="14%" class="text-right btn-table-action">
                                @if ($orcamento->status != 'C')
                                    @if ($orcamento->status == 'E' || $orcamento->status == 'P')
                                        <i class="my-icon far fa-user-check" onclick="converter_orcamento({{ $orcamento->id }})"></i>
                                    @elseif ($orcamento->status == 'A')
                                        <i class="my-icon far fa-user-clock" onclick="mudar_status_orcamento({{ $orcamento->id }}, 'E')"></i>
                                    @endif
                                    @if ($orcamento->status == 'A' || ($orcamento->status == 'E' && $orcamento->qtde_servicos_autorizados == 0))
                                        <i class="my-icon far fa-edit" onclick="editar_orcamento({{ $orcamento->id }})"></i>
                                    @endif
                                    <i class="my-icon far fa-file-times" onclick="mudar_status_orcamento({{ $orcamento->id }}, 'C')"></i>
                                    <i class="my-icon far fa-print" onclick="redirect('orcamento/imprimir/{{ $orcamento->id }}', true)"></i>
                                @endif
                                <i class="my-icon far fa-trash-alt" onclick="deletar_orcamento({{ $orcamento->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="abrir_orcamento()">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('modals.orcamento_modal')
@include('modals.orcamento_conversao_modal')

@endsection
