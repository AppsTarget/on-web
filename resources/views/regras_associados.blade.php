@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Regras de Associados</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-evolucao-tipo">
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
                    <tr class="sortable-columns" for="#table-evolucao-tipo">
                        <th width="10%" class="text-right">Código</th>
                        <th width="70%">Descrição</th>
                        <th width="10%">Ativo</th>
                        <th width="10%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-evolucao-tipo" class="table table-hover">
                <tbody>
                    @foreach ($regras as $regra)
                        <tr>
                            <td width="10%" class="text-right">{{ $regra->id }}</td>
                            <td width="70%">{{ $regra->dias_pos_fim_contrato }}</td>
                            <td width="10%">
                                @if ($regra->ativo)
                                    Sim
                                @else
                                    Não
                                @endif
                            </td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-edit" onclick="editar_regra_associados({{ $regra->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_regra_associados({{ $regra->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#regrasAssociadosModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('modals.regras_associados_modal')

@endsection
