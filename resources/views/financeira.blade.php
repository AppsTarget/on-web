@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Financeira</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-financeira">
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
                    <tr class="sortable-columns" for="#table-financeira">
                        <th width="10%" class="text-center">Código</th>
                        <th width="50%">Descrição</th>
                        <th width="10%" class="text-right">Prazo</th>
                        <th width="10%" class="text-right">Taxa</th>
                        <th width="10%" class="text-right">Tipo</th>
                        <th width="10%" class="text-right"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-financeira" class="table table-hover">
                <tbody>
                    @foreach ($financeiras as $financeira)
                        <tr>
                            <td width="10%" class="text-center">{{ $financeira->id }}</td>
                            <td width="50%">{{ $financeira->descr }}</td>
                            <td width="10%" class="text-right">{{ $financeira->prazo }}</td>
                            <td width="10%" class="text-right">{{ $financeira->taxa_padrao }}</td>
                            @if($financeira->tipo_de_baixa == 'D') 
                                <td width="10%" class="text-right">Débito</td>
                            @else
                                <td width="10%" class="text-right">Crédito</td>
                            @endif
                            <td width="10%" class="text-center btn-table-action">
                                {{-- <i class="my-icon fas fa-percentage" onclick="listar_financeira_taxas({{ $financeira->id }})"></i> --}}
                                <i class="my-icon far fa-edit" onclick="editar_financeira({{ $financeira->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_financeira({{ $financeira->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#financeiraModal">
    <i class="my-icon fas fa-plus"></i>
</button>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
@include('modals.financeira_modal')
@include('modals.financeira_taxas_modal')

@endsection
