@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Formas de Pagamento</h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-forma-pag">
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
                    <tr class="sortable-columns" for="#table-forma-pag">
                        <th width="10%" class="text-center">Código</th>
                        <th width="42.5%">Descrição</th>
                        <th width="12.5%">Máx. Parcelas</th>
                        <th width="12.5%">Dias Entre Parc.</th>
                        <th width="12.5%">Forma</th>
                        <th width="10%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-forma-pag" class="table table-hover">
                <tbody>
                    @foreach ($forma_pag as $pag)
                        <tr>
                            <td width="10%" class="text-center">{{ $pag->id }}</td>
                            <td width="42.5%">{{ $pag->descr }}</td>
                            <td width="12.5%">{{ $pag->max_parcelas }}</td>
                            <td width="12.5%">{{ $pag->dias_entre_parcela }}</td>
                            <td width="12.5%">
                                @if ($pag->avista_prazo == 'V')
                                    À Vista
                                @else
                                    À Prazo
                                @endif
                            </td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon fas fa-comment-dollar" onclick="listar_financeira_formas_pag({{ $pag->id }})"></i>
                                <i class="my-icon far fa-edit" onclick="editar_forma_pag({{ $pag->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="deletar_forma_pag({{ $pag->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#formaPagModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif

@include('modals.forma_pag_modal')
@include('modals.financeira_formas_pag_modal')

@endsection
