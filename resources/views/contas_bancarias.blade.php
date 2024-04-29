@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">Contas Bancárias</h3>
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
                        <th width="10%" class="text-center">Numero Conta</th>
                        <th width="38%">Titular</th>
                        <th width="12.5%">Agência</th>
                        <th width="12.5%">Banco</th>
                        <th width="17.5%">Empresa</th>
                        <th width="10%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll">
            <table id="table-forma-pag" class="table table-hover">
                <tbody>
                    @foreach ($contas as $conta)
                        <tr>
                            <td width="10%" class="text-center">{{ $conta->numero }}</td>
                            <td width="38%">{{ $conta->titular }}</td>
                            <td width="12.5%">{{ $conta->agencia }}</td>
                            <td width="12.5%">{{ $conta->id_banco }}</td>
                            <td width="17.5%">{{ $conta->descr_emp }}</td>
                            <td width="10%" class="text-center btn-table-action">
                                <i class="my-icon far fa-edit" onclick="editar_conta_bancaria({{ $conta->id }})"></i>
                                <i class="my-icon far fa-trash-alt" onclick="excluir_conta_bancaria({{ $conta->id }})"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#contaBancariaModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif

@include('modals.conta_bancaria_modal')

@endsection
