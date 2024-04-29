@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">
            Contratos
        </h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-plano_tratamento">
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
                        <th width="10%" class="text-center">Nº Contrato</th>
                        <th width="20%">Associado</th>
                        <th width="15%">Responsavel</th>
                        <th width="10%" class="text-right">Valor</th>
                        <th width="10%">Criado Em</th>
                        <th width="10%">Válido Até</th>
                        <th width="10%" class='text-center'>Status</th>
                        <th width="15%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-plano_tratamento" class="table table-hover">
                <tbody>
                    @foreach ($contratos as $contrato)
                        <tr>
                            <td width="10%" class="text-center">
                                {{ str_pad($contrato->id, 6, "0", STR_PAD_LEFT) }}
                            </td>
                            <td width="20%" >{{ $contrato->descr_paciente}}</td>
                            <td width="15%">{{ $contrato->Responsavel }}</td>
                            {{-- <td width="10%">{{ $contrato->Valor_contrato }}</td> --}}
                            <td width="10%" class="text-right">R$ {{ number_format($contrato->Valor_contrato, 2, ',', '.') }}</td>
                            <td width="10%">
                                {{ date('d/m/Y', strtotime($contrato->Data_inicial)) }}
                            </td>
                            <td width="10%">{{ date('d/m/Y', strtotime($contrato->Data_final)) }}</td>

                            
                            <td width="10%" style="font-size:0.75rem">
                                @if ($contrato->Situacao == 'F')
                                    <div class="tag-pedido-finalizado">
                                        Em Execução
                                    </div>
                                @elseif ($contrato->Situacao == 'E')
                                    <div class="tag-pedido-aberto">
                                        Aprovação do Associado
                                    </div>
                                @elseif ($contrato->Situacao == 'A')
                                    <div class="tag-pedido-primary">
                                        Em Edição
                                    </div>
                                @else
                                    <div class="tag-pedido-cancelado">
                                        Cancelado
                                    </div>
                                @endif
                            </td>
                            <td width="15%" class="text-right btn-table-action">
                                @if ($contrato->Situacao != 'C')
                                    @if ($contrato->Situacao == 'A')
                                        <i class="my-icon far fa-user-check" ></i>
                                    @endif
                                    <i class="my-icon far fa-file-times" ></i>
                                    <i class="my-icon far fa-print" ></i>
                                    <i class="my-icon far fa-edit" ></i>
                                @endif
                                <i class="my-icon far fa-trash-alt" onclick="deletar_contrato({{ $contrato->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="criar_contrato()">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('modals.contrato_modal')

@endsection
